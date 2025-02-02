<?php
class ExamScheduleController extends AppController {
	public $name = "ExamSchedule";
	public $components =array('AcademicYear');
	public $uses = array();
	public $menuOptions = array(
		 'parent' => 'schedule',
		 'exclude'=>array('index'),
		 'weight'=>-10000000,
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		//$this->Auth->allow(array('*'));
		//$this->Auth->allow('index');  
	}
	
	public function beforeRender() {
		$acadamicYears = $this->AcademicYear->acyear_array();
		$default_academic_year = $this->AcademicYear->current_academicyear();
		$this->set(compact('acadamicYears','default_academic_year'));
	}
	
	public function index(){
		
	}
	
	public function cancel_exam_schedule() {
		if(isset($this->request->data['cancelExamSchedule'])) {
			$program_type_ids = array();
			$department_ids = array();
			$year_level_ids = array();
			if(is_array($this->request->data['ExamSchedule']['program_type_id']))
				$program_type_ids = $this->request->data['ExamSchedule']['program_type_id'];
			else
				$program_type_ids[] = $this->request->data['ExamSchedule']['program_type_id'];
			
			if(is_array($this->request->data['ExamSchedule']['department_id']))
				$department_ids = $this->request->data['ExamSchedule']['department_id'];
			else
				$department_ids[] = $this->request->data['ExamSchedule']['department_id'];
			
			if(is_array($this->request->data['ExamSchedule']['year_level']))
				$year_levels = $this->request->data['ExamSchedule']['year_level'];
			else
				$year_levels[] = $this->request->data['ExamSchedule']['year_level'];
			$cancelationStatus = ClassRegistry::init('ExamSchedule')->cancelExamSchedule($this->college_id, $this->request->data['ExamSchedule']['acadamic_year'], $this->request->data['ExamSchedule']['semester'], $this->request->data['ExamSchedule']['program_id'], $program_type_ids, $department_ids, $year_levels);
			if($cancelationStatus == 0) {
				$this->Session->setFlash('<span></span>'.__('There is no exam schedule to cancel.'), 'default',array('class'=>'info-box info-message'));
			}
			else if($cancelationStatus == 1) {
				$this->Session->setFlash('<span></span>'.__('Exam schedule is successfully canceled.'), 'default',array('class'=>'success-box success-message'));
			}
			else {
				$this->Session->setFlash('<span></span>'.__('Exam schedule is cancellation is failed. Please try again'), 'default',array('class'=>'error-box error-message'));
			}
		}
		$departments = ClassRegistry::init('Department')->find('list',
			array(
				'conditions'=>
				array(
					'Department.college_id' => 
					$this->college_id
				)
			)
		);
		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');
		$semesters = array('I' => 'I', 'II' => 'II', 'III' => 'III');
		$max_year_level = ClassRegistry::init('YearLevel')->get_department_max_year_level($departments);
		$departments['FP'] = 'Freshman Program';
		for($i = 1; $i <= $max_year_level; $i++) {
			$yearLevels[$i] = $i.($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')));
		}
		$this->set(compact('departments', 'programs', 'programTypes', 'semesters', 'yearLevels'));
	}
	
	public function generate_exam_schedule() {
		debug($this->request->data);
		if(isset($this->request->data['generateExamSchedule'])) {
			$program_type_ids = array();
			$department_ids = array();
			$year_level_ids = array();
			if(is_array($this->request->data['ExamSchedule']['program_type_id']))
				$program_type_ids = $this->request->data['ExamSchedule']['program_type_id'];
			else
				$program_type_ids[] = $this->request->data['ExamSchedule']['program_type_id'];
			
			if(is_array($this->request->data['ExamSchedule']['department_id']))
				$department_ids = $this->request->data['ExamSchedule']['department_id'];
			else
				$department_ids[] = $this->request->data['ExamSchedule']['department_id'];
			
			if(is_array($this->request->data['ExamSchedule']['year_level']))
				$year_levels = $this->request->data['ExamSchedule']['year_level'];
			else
				$year_levels[] = $this->request->data['ExamSchedule']['year_level'];
			
			$publishedCourses = ClassRegistry::init('Section')->getSectionsPublishedCoursesForExamSchedule($this->college_id, $this->request->data['ExamSchedule']['acadamic_year'], $this->request->data['ExamSchedule']['semester'], $this->request->data['ExamSchedule']['program_id'], $program_type_ids, $department_ids, $year_levels);
			$sections_published_courses = ClassRegistry::init('Section')->getPublishedCoursesForExamScheduleBySection($this->college_id, $this->request->data['ExamSchedule']['acadamic_year'], $this->request->data['ExamSchedule']['semester'], $this->request->data['ExamSchedule']['program_id'], $program_type_ids, $department_ids, $year_levels);
			//Start scheduling
			$unable_to_schedule = array();
			$gap_exam_free_days = array();
			$already_scheduled_pc_ids = array();
			$successfully_scheduled_pc_ids = array();
			$t_c = 0;
			foreach($publishedCourses as $publishedCourse) {
				$gap_exam_free_days[$publishedCourse['section_id']] = array();
				//If the published course is already scheduled due to merge and identical courses to be given at the same time.
				if(in_array($publishedCourse['id'], $already_scheduled_pc_ids)) {
					continue;
				}
				if(empty($publishedCourse['exam_days'])) {
					$index = count($unable_to_schedule);
					$unable_to_schedule[$index]['published_course_id'] = $publishedCourse['id'];
					$unable_to_schedule[$index]['reason'] = 'The system unable to get free days to schedule the course. You have to either minimize the constraint on the published course or increase the exam period.';
					continue;
				}
				$continue = false;
				//Section last exam day
				$section_last_exam_date = ClassRegistry::init('ExamSchedule')->find('first',
					array(
						'conditions' =>
						array(
							'ExamSchedule.published_course_id IN (SELECT id FROM published_courses WHERE section_id = \''.$publishedCourse['section_id'].'\' AND exam_date >= \''.$publishedCourse['start_date'].'\' AND exam_date <= \''.$publishedCourse['end_date'].'\')'
						),
						'order' => array('ExamSchedule.exam_date DESC')
					)
				);
				
				//Make the course at the beginning of exam schedule if it has gap constraint
				if($publishedCourse['gap'] > 0) {
					$checkScheduleExistance = ClassRegistry::init('ExamSchedule')->find('first', 
						array(
							'conditions' =>
							array(
								'ExamSchedule.published_course_id' => $sections_published_courses[$publishedCourse['section_id']],
								'ExamSchedule.exam_date' => $publishedCourse['exam_days'][0]
							),
							'recursive' => -1
						)
					);
				}
				//If there is no scheduled course before
				if($publishedCourse['gap'] > 0 && empty($checkScheduleExistance)) {
					$exam_date = $publishedCourse['exam_days'][0];
				}
				else if(empty($section_last_exam_date)) {
					$exam_date = $publishedCourse['exam_days'][rand(0, (count($publishedCourse['exam_days'])-1))];
				}
				else {
					$exam_date = date("Y-m-d", strtotime("+".$publishedCourse['section_average_exam_day']." day",
					 strtotime($section_last_exam_date['ExamSchedule']['exam_date'])));
					//Check that the date is not out of schedule period
					if($exam_date > $publishedCourse['end_date']) {
						$datetime1 = new DateTime($exam_date);
						$datetime2 = new DateTime($publishedCourse['end_date']);
						$interval = $datetime1->diff($datetime2);
						if($interval->d > 0)
							$exam_date = date("Y-m-d", strtotime("+".$interval->d." day", strtotime($publishedCourse['start_date'])));
						else
							$exam_date = $publishedCourse['start_date'];
					}
				}
				//Check if the selected published course has possible days
				$to_be_given_on_the_same_day = array();
				//Get list of other published courses (the same course from the same curriculum) to be given on the same date and session
				foreach($publishedCourses as $k => $v) {
					if($v['course_id'] == $publishedCourse['course_id'] && !in_array($v['id'], $already_scheduled_pc_ids)) {
						$to_be_given_on_the_same_day[] = $v;
					}
				}
				//Section merging
				foreach($to_be_given_on_the_same_day as $to_be_given_key => $to_be_given) {
					$to_be_scheduled_published_course_ids = array();
					$mergedSectionsExam = ClassRegistry::init('MergedSectionsExam')->find('first',
						array(
							'conditions' =>
							array(
								'MergedSectionsExam.published_course_id' => $to_be_given['id']
							),
							'recursive' => -1
						)
					);
					
					if(!empty($mergedSectionsExam)) {
						$mergedSectionsExams = ClassRegistry::init('MergedSectionsExam')->find('all',
							array(
								'conditions' =>
								array(
									'MergedSectionsExam.merge_key' => $mergedSectionsExam['MergedSectionsExam']['merge_key']
								),
								'recursive' => -1
							)
						);
						foreach($mergedSectionsExams as $mergedSectionsExam) {
							$to_be_scheduled_published_course_found = false;
							foreach($publishedCourses as $k => $v) {
								if($mergedSectionsExam['MergedSectionsExam']['published_course_id'] == $v['id']) {
									$to_be_scheduled_published_course_found = true;
									break;
								}
							}
							if($to_be_scheduled_published_course_found == true) {
								$to_be_scheduled_published_course_ids[] = $mergedSectionsExam['MergedSectionsExam']['published_course_id'];
							}
						}
						$to_be_given_on_the_same_day[$to_be_given_key]['merged_sections'] = $to_be_scheduled_published_course_ids;
					}
					else {
						$to_be_scheduled_published_course_ids[] = $to_be_given['id'];
						$to_be_given_on_the_same_day[$to_be_given_key]['merged_sections'] = $to_be_scheduled_published_course_ids;
					}
				}
				
				$include_exam_free_days = false;
				$pc_based_exam_date = $exam_date;
				$pc_session_found = true;
				$pc_exam_room_found = true;
				$exam_days_check_count = 0;
				$c1 = 0;
				$c2 = 0;
				$c3 = 0;
				while(1) {//IF session is not found for the selected date
					if(in_array($publishedCourse['id'], $already_scheduled_pc_ids)) {
						continue 2;
					}
					//if($c1++ > 35) { debug('C1'); exit(); }
					//if($t_c++ > 20) { debug('t_c1'); debug($already_scheduled_pc_ids); exit(); }
				while(1) {//If suitable exam date is not found even including exam free days
					//if($c2++ > 35) { debug('C2'); exit(); }
					$pc_exam_check_start_date = $exam_date;
					$c31 = 0;
					while(1) {//If suitable exam date is not found
						//debug($publishedCourse);
						//debug($exam_date);
						//debug($already_scheduled_pc_ids); 
						$c31++;
						//if($c3++ > 40) { debug('C3 = '.$c3.', '.$c31); exit(); }
						//if($t_c++ > 10) { debug('t_c2'); exit(); }
						//Check if the selected date suitable for all published course
						$suitable_date_is_found = true;
						foreach($to_be_given_on_the_same_day as $to_be_given_on_the_same_day_v) {
							foreach($to_be_given_on_the_same_day_v['merged_sections'] as $to_be_scheduled_published_course_id) {
								if(count($to_be_given_on_the_same_day_v['merged_sections']) > 1) {
									foreach($publishedCourses as $toBeCheckedPublishedCourse) {
										if($toBeCheckedPublishedCourse['id'] == $to_be_scheduled_published_course_id) {
											break;
										}
									}
								}
								else {
									$toBeCheckedPublishedCourse = $to_be_given_on_the_same_day_v;
								}
								if(!((in_array($exam_date, $toBeCheckedPublishedCourse['exam_days']) && (!isset($gap_exam_free_days[$toBeCheckedPublishedCourse['section_id']]) || !in_array($exam_date, $gap_exam_free_days[$toBeCheckedPublishedCourse['section_id']]))) || 
									(in_array($exam_date, $toBeCheckedPublishedCourse['exam_days']) && $include_exam_free_days == true))) {
									$suitable_date_is_found = false;
									break 2;
								}
							}
						}
						if($suitable_date_is_found == true) {
							break 2;
						}
						$exam_date = date("Y-m-d", strtotime("+1 day", strtotime($exam_date)));
						//If it reaches exam end date, then go to the beginning of exam
						if($exam_date > $publishedCourse['end_date']) {
							$exam_date = $publishedCourse['start_date'];
						}
						
						if($pc_exam_check_start_date == $exam_date) {
							//If available date is not found in the possible date list, we have to search in the exam free date for available time otherwise it will be labeled as unable to schedule.
							if($include_exam_free_days == false && !empty($gap_exam_free_days[$to_be_given_on_the_same_day_v['section_id']])) {
								$include_exam_free_days = true;
								break;
							}
							else {
								//It checks the whole available date and 
								//it is the time to include in the unable list
								$index = count($unable_to_schedule);
								$unable_to_schedule[$index]['published_course_id'] = $to_be_given_on_the_same_day_v['id'];
								$unable_to_schedule[$index]['reason'] = 'The system unable to get free days to schedule the course. You have to either minimize the constraint on the published course or increase the exam period.';
								$continue = true;
								break 2;
							}
						}
					}
				}
				if($continue) {
					continue 2;
				}
				
				//Check if the course has gap
				$free_days_tmp = array();
				if($include_exam_free_days == false && $to_be_given_on_the_same_day_v['gap'] > 0) {
					/*make sure that there is N free days before the selected date. 
					If there is no N size free days, then assign the course after the 
						maximum available free days.
					1. Count the number of days before the selected exam date
					2. If there are enough days or the last back date is exam start date, then the date is perfect.
					3. If there is no enough space, increment the date by one and check again.
					4. If it circles, then pick the best available date.
					*/
					$gap_fit = false;
					$gap_check_start_date = $exam_date;
					//TODO: Consider also all sections including merged sections
					while(!$gap_fit) {
						$count_free_days = 0;
						$c_exam_date = $exam_date;
						$free_days_tmp = array();
						while($to_be_given_on_the_same_day_v['start_date'] < $c_exam_date) {
							$checkScheduleExistance = ClassRegistry::init('ExamSchedule')->find('first', 
								array(
									'conditions' =>
									array(
										'ExamSchedule.published_course_id' => $sections_published_courses[$to_be_given_on_the_same_day_v['section_id']],
										'ExamSchedule.exam_date' => $c_exam_date
									),
									'recursive' => -1
								)
							);
							if(empty($checkScheduleExistance) && 
								$to_be_given_on_the_same_day_v['start_date'] <= $c_exam_date) {
								$count_free_days++;
								$free_days_tmp[] = $c_exam_date;
								if($to_be_given_on_the_same_day_v['start_date'] == $c_exam_date ||
									$count_free_days >= $to_be_given_on_the_same_day_v['gap']) {
									$gap_fit = true;
									break;
								}
							}
							else {
								$count_free_days = 0;
								$free_days_tmp = array();
							}
							$c_exam_date = date("Y-m-d", strtotime("-1 day", strtotime($to_be_given_on_the_same_day_v['start_date'])));
						}
						if(!$gap_fit) {
							$exam_date = date("Y-m-d", strtotime("+1 day", strtotime($exam_date)));
							if($exam_date > $to_be_given_on_the_same_day_v['end_date']) {
								$exam_date = $to_be_given_on_the_same_day_v['start_date'];
							}
							if($gap_check_start_date == $exam_date) {
								/*There is no sequence of days full filling the gap.
								TODO:Pick the best available day
								For now I simply leave so that the originally picked date will be used
								*/
								break;
							}
						}
					}
				}
				//End of gap checking
				
				/****************** Time to search for exam session ***************/
				/*
				1. Check if section is free (not assigned and not in the constraint)
				2. If it is free go to the hall assignment
				3. If it is not, go to the next session or date
				*/
				$session_found = false;
				for($session = 1; $session <= 3; $session++) {
					//Check if the session is not taken
					$session_is_available = true;
					foreach($to_be_given_on_the_same_day as $to_be_given_key => $to_be_given) {
						foreach($to_be_given['merged_sections'] as $to_be_scheduled_published_course_id) {
							if(count($to_be_given['merged_sections']) > 1) {
								foreach($publishedCourses as $sessionToBeCheckedPublishedCourse) {
									if($sessionToBeCheckedPublishedCourse['id'] == $to_be_scheduled_published_course_id) {
										break;
									}
								}
							}
							else {
								$sessionToBeCheckedPublishedCourse = $to_be_given;
							}
							
							$check_session_availability = ClassRegistry::init('ExamSchedule')->find('count',
								array(
									'conditions' =>
									array(
										'ExamSchedule.published_course_id' => $sections_published_courses[$sessionToBeCheckedPublishedCourse['section_id']],
										'ExamSchedule.exam_date' => $exam_date,
										'ExamSchedule.session' => $session,
									)
								)
							);
							//Check if there is exam date and session constraint
							if($check_session_availability <= 0) {
								$no_date_constraint = true;
								$course_exam_constraints = ClassRegistry::init('CourseExamConstraint')->find('all',
									array(
		 								'conditions' =>
		 								array(
											'CourseExamConstraint.published_course_id' => $sessionToBeCheckedPublishedCourse['id'],
											'CourseExamConstraint.exam_date' => $exam_date,
											'CourseExamConstraint.session' => $session,
		 								),
		 								'recursive' => -1
		 							)
		 						);
								
								if(!empty($course_exam_constraints)) {
									//Determine which one to use; active or inactive
									$active_course_exam_constraint = false;
									$inactive = 0;
									foreach($course_exam_constraints as $course_exam_constraint) {
										if($course_exam_constraint['CourseExamConstraint']['active'] == 1) {
											$active_course_exam_constraint = true;
											break;
										}
									}
									if($active_course_exam_constraint) {
										$course_exam_constraint_check = ClassRegistry::init('CourseExamConstraint')->find('count',
											array(
												'conditions' =>
												array(
													'CourseExamConstraint.published_course_id' => $sessionToBeCheckedPublishedCourse['id'],
													'CourseExamConstraint.exam_date' => $exam_date,
													'CourseExamConstraint.session' => $session,
													'CourseExamConstraint.active' => 1,
												)
											)
										);
										if($course_exam_constraint_check <= 0) {
											$no_date_constraint = false;
										}
									}
									else {
										$course_exam_constraint_check = ClassRegistry::init('CourseExamConstraint')->find('count',
											array(
												'conditions' =>
												array(
													'CourseExamConstraint.published_course_id' => $sessionToBeCheckedPublishedCourse['id'],
													'CourseExamConstraint.exam_date' => $exam_date,
													'CourseExamConstraint.session' => $session,
													'CourseExamConstraint.active' => 0,
												)
											)
										);
										if($course_exam_constraint_check > 0) {
											$no_date_constraint = false;
										}
									}
								}
							}
							/*
							If "Avoid Add Students Schedule Conflict" (Who add to be scheduled published course, To be published course section students who add courses in another section) (cross college, within the same college) is selected, make sure that students who add this course are also free.
								A)
								1. Get list of students who add but not dropped the published course
								2. Get list of published courses each student is registered and add
								2. Check if there is exam schedule for the published course by the selected date and session
								3. If there is a schedule, go to the next session and/or date
								
								B)
								1. Get list of section students who are registered or add for the to be scheduled published course from the same section (published course section).
								2. Filter and get students who has add in another section for any published course
								3. Check if the schedule of those added published course conflict with the selected exam date and session.
							*/
							//A
							if(($this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college'] == 1 ||
								$this->request->data['ExamSchedule']['to_be_scheduled_course_add_cross_college'] == 1) &&
								$check_session_availability <= 0) {
								$student_pc_ids = array();
								$add_same_college = null;
								if($this->request->data['ExamSchedule']['to_be_scheduled_course_add_cross_college'] == 0) {
									$add_same_college = $this->college_id;
								}
								$students_who_add_pc = ClassRegistry::init('PublishedCourse')->getStudentsWhoAddPublishedCourse($sessionToBeCheckedPublishedCourse['id'], $this->college_id);
								foreach($students_who_add_pc as $student_id_who_add_pc) {
									$course_registration_pc_ids = ClassRegistry::init('CourseRegistration')->find('list',
										array(
											'conditions' =>
											array(
												'CourseRegistration.academic_year' => $this->request->data['ExamSchedule']['acadamic_year'],
												'CourseRegistration.semester' => $this->request->data['ExamSchedule']['semester'],
												'CourseRegistration.student_id' => $student_id_who_add_pc
											),
											'fields' =>
											array(
												'CourseRegistration.published_course_id'
											)
										)
									);
									$course_add_pc_ids = ClassRegistry::init('CourseAdd')->find('all',
										array(
											'conditions' =>
											array(
												'CourseAdd.academic_year' => $this->request->data['ExamSchedule']['acadamic_year'],
												'CourseAdd.semester' => $this->request->data['ExamSchedule']['semester'],
												'CourseAdd.student_id' => $student_id_who_add_pc,
												'OR' => array(
													'AND' => array(
														'CourseAdd.department_approval' => 1,
														'CourseAdd.registrar_confirmation' => 1
													),
													'PublishedCourse.add' => 1
												)
											),
											'fields' =>
											array(
												'CourseAdd.published_course_id'
											),
											'contain' => 
											array(
												'PublishedCourse'
											)
										)
									);
									foreach($course_registration_pc_ids as $course_registration_pc_id) {
										$student_pc_ids[] = $course_registration_pc_id;
									}
									foreach($course_add_pc_ids as $course_add_pc_id) {
										$student_pc_ids[] = $course_add_pc_id['PublishedCourse']['id'];
									}
								}//END for each student who add the published course
								$check_session_availability = ClassRegistry::init('ExamSchedule')->find('count',
									array(
										'conditions' =>
										array(
											'ExamSchedule.published_course_id' => $student_pc_ids,
											'ExamSchedule.exam_date' => $exam_date,
											'ExamSchedule.session' => $session,
										)
									)
								);
							}
							
							//B
							if(($this->request->data['ExamSchedule']['section_students_add_in_college'] == 1 ||
								$this->request->data['ExamSchedule']['section_students_add_cross_college'] == 1) &&
								$check_session_availability <= 0) {
								/*
								1. Get list of students of the published course section
								2. Filter students who are registered or add the published course
								*/
								$pc_section_student_ids = ClassRegistry::init('StudentsSection')->find('list',
									array(
										'conditions' =>
										array(
											'StudentsSection.section_id' => $sessionToBeCheckedPublishedCourse['section_id']
										),
										'fields' =>
										array(
											'StudentsSection.student_id'
										),
										'recursive' => -1
									)
								);
								
								$pc_registered_student_ids = ClassRegistry::init('CourseRegistration')->find('list',
									array(
										'conditions' =>
										array(
											'CourseRegistration.student_id' => $pc_section_student_ids,
											'CourseRegistration.published_course_id' => $sessionToBeCheckedPublishedCourse['id']
										),
										'fields' =>
										array(
											'CourseRegistration.student_id'
										)
									)
								);
								$course_add_pc_ids = ClassRegistry::init('CourseAdd')->find('all',
									array(
										'conditions' =>
										array(
											'CourseAdd.academic_year' => $this->request->data['ExamSchedule']['acadamic_year'],
											'CourseAdd.semester' => $this->request->data['ExamSchedule']['semester'],
											'CourseAdd.student_id' => $pc_registered_student_ids,
											'OR' => array(
												'AND' => array(
													'CourseAdd.department_approval' => 1,
													'CourseAdd.registrar_confirmation' => 1
												),
												'PublishedCourse.add' => 1
											)
										),
										'fields' =>
										array(
											'CourseAdd.published_course_id'
										),
										'contain' => 
										array(
											'PublishedCourse'
										)
									)
								);
								$student_pc_ids = array();
								foreach($course_add_pc_ids as $course_add_pc_id) {
									$student_pc_ids[] = $course_add_pc_id['PublishedCourse']['id'];
								}
								$check_session_availability = ClassRegistry::init('ExamSchedule')->find('count',
									array(
										'conditions' =>
										array(
											'ExamSchedule.published_course_id' => $student_pc_ids,
											'ExamSchedule.exam_date' => $exam_date,
											'ExamSchedule.session' => $session,
										)
									)
								);
							}
							
							if($check_session_availability > 0) {
								$session_is_available = false;
								break 2;
							}
						}
					}
					//Check if there is exam date and session constraint
					if($session_is_available == true) {
						$no_date_constraint = true;
						if($no_date_constraint == true) {
							//Bingo take the session for schedule
							$session_found = true;
							$grouped_courses_for_rollback = array();
							$exam_free_days_for_rollback = array();
							//if($publishedCourse['id']==40) {
								//debug($to_be_given_on_the_same_day);
							//}
							foreach($to_be_given_on_the_same_day as $to_be_given_key => $to_be_given) {
								/*********************** Search for exam hall *****************************/
								//Return all rooms which can accommodate all students (including for split)
								$examRooms = ClassRegistry::init('ClassRoom')->getClassRoomsForExam($this->college_id, $to_be_given['merged_sections'], $to_be_given['section_id'], $exam_date, $session, $this->request->data['ExamSchedule']['acadamic_year'], $this->request->data['ExamSchedule']['semester']);
								//If there is no room totally OR if there is no enough rooms for split sections
								if(empty($examRooms) || count($examRooms) > count($examRooms[0]['exam_rooms'])) {
									//Go to next session or date if it is full
									$pc_exam_room_found = false;
									$session_found = false;
									//debug('No room');
									break 2;
								}
								else {
									//debug('Room found 1');
									$examSchedules = array();
									//Pick the first room 
									$section_split = false;
									if(count($examRooms) > 1) {
										$section_split = true;
									}
									$exam_room_index = 0;
									//The loop is if there is section split for one published course
									$invigilator_index = 0;
									foreach($examRooms as $examRoom) {
										$index = count($examSchedules);
										$examSchedules[$index]['ExamSchedule']['published_course_id'] = $to_be_given['id'];
										$examSchedules[$index]['ExamSchedule']['acadamic_year'] = $this->request->data['ExamSchedule']['acadamic_year'];
										$examSchedules[$index]['ExamSchedule']['semester'] = $this->request->data['ExamSchedule']['semester'];
										$examSchedules[$index]['ExamSchedule']['session'] = $session;
										$examSchedules[$index]['ExamSchedule']['exam_date'] = $exam_date;
										$examSchedules[$index]['ExamSchedule']['class_room_id'] = $examRoom['exam_rooms'][$exam_room_index]['id'];
										if($section_split == true) {
											$examSchedules[$index]['ExamSchedule']['exam_split_section_id'] = $examRoom['section_id'];
										}
										/********************     invigilators     **********************/
										/*
										1. Check if there is specified invigilators for the selected room
										2. If there is no specified invigilator, use the default
										*/
										$number_of_invigilator = $to_be_given['number_of_invigilator'];
										if($examRoom['exam_rooms'][$exam_room_index]['number_of_invigilator'] > 0) {
											$number_of_invigilator = $examRoom['exam_rooms'][$exam_room_index]['number_of_invigilator'];
										}
										
										$staffsForExamInternal = ClassRegistry::init('Staff')->getInvigilators($this->college_id, $this->request->data['ExamSchedule']['acadamic_year'], $this->request->data['ExamSchedule']['semester'], $exam_date, $session, $to_be_given['year_level']);
										$staffsForExamExternal = ClassRegistry::init('StaffForExam')->getInvigilators($this->college_id, $this->request->data['ExamSchedule']['acadamic_year'], $this->request->data['ExamSchedule']['semester'], $exam_date, $session, $to_be_given['year_level']);
										//Staff merge
										$staffsForExam = array();
										foreach($staffsForExamInternal as $k => $v) {
											if($v['max_number_of_exam'] == 0 || 
												$v['assigned_exam'] < $v['max_number_of_exam']) {
												$v['external_staff'] = 0;
												$staffsForExam[] = $v;
											}
										}
										foreach($staffsForExamExternal as $k => $v) {
											if($v['max_number_of_exam'] == 0 || 
												$v['assigned_exam'] < $v['max_number_of_exam']) {
												$v['external_staff'] = 1;
												$staffsForExam[] = $v;
											}
										}
										//Sort
										for($i = 0; $i < count($staffsForExam); $i++) {
											for($j = $i+1; $j < count($staffsForExam); $j++) {
												if($staffsForExam[$i]['assigned_exam'] > $staffsForExam[$j]['assigned_exam']) {
													$tmp = $staffsForExam[$i];
													$staffsForExam[$i] = $staffsForExam[$j];
													$staffsForExam[$j] = $tmp;
												}
											}
										}
										
										$tmp_index = 0;
										for(; $invigilator_index < ($number_of_invigilator*($index+1)) && $invigilator_index < count($staffsForExam); $invigilator_index++) {
											if($staffsForExam[$invigilator_index]['external_staff'] == 0)
												$examSchedules[$index]['Invigilator'][$tmp_index]['staff_id'] = $staffsForExam[$invigilator_index]['id'];
											else
												$examSchedules[$index]['Invigilator'][$tmp_index]['staff_for_exam_id'] = $staffsForExam[$invigilator_index]['id'];
											$tmp_index++;
										}
										$exam_room_index++;
									}
									//If the schedule is for merged sections, duplicate the schedule by changing only the publish course id. Others are identical
									if(count($to_be_given['merged_sections']) > 1) {
										$examSchedulesTmp = array();
										foreach($to_be_given['merged_sections'] as $k => $v) {
											$index = count($examSchedulesTmp);
											$examSchedulesTmp[$index] = $examSchedules[0];
											$examSchedulesTmp[$index]['ExamSchedule']['published_course_id'] = $v;
										}
										$examSchedules = $examSchedulesTmp;
									}
									if(!empty($examSchedules)) {
										//TODO: check if the saveAll is successful 
										//otherwise display error message and reverse the already done saving
										foreach($examSchedules as $examSchedule) {
											$already_scheduled_pc_ids[] = $examSchedule['ExamSchedule']['published_course_id'];
											$successfully_scheduled_pc_ids[] = $examSchedule['ExamSchedule']['published_course_id'];
											ClassRegistry::init('ExamSchedule')->saveAll($examSchedule, array('validate' => false));
											//Later it will be used for roll back if enough number of rooms are not found for the course which is supposed to be given at once to all sections.
											$grouped_courses_for_rollback[] = $examSchedule['ExamSchedule']['published_course_id'];
										}
										//Mark days as a free day. It is if there is gap constraint
										foreach($free_days_tmp as $k => $v) {
											$gap_exam_free_days[$to_be_given['section_id']][] = $v;
											$exam_free_days_for_rollback[$to_be_given['section_id']][] = $v;
										}
									}
									//The break is to avoid checking other sessions
									//break 2;
								}
							}//End of for each course group (to be given at the same time)
							//TODO Check the following if condition if it is reliable
							if($session_found == true) {
								break;
							}
						}//If there is no date constraint
					}//If session is available (not already scheduled)
				}//End of session loop for($session = 1; ... )
				//If session is not found
				if($session_found == false) {
					//pc_session_found is used for circle checking to avoid infinite loop
					if($pc_session_found == false && ($exam_days_check_count > count($publishedCourse['exam_days']) || $pc_based_exam_date == $exam_date)) {
						//Circle searching for session.
						$index = count($unable_to_schedule);
						$unable_to_schedule[$index]['published_course_id'] = $to_be_given_on_the_same_day_v['id'];
						if($pc_exam_room_found == false) {
							/*
							If the room is not found for the course which is taken by more than one section
							*/
							if(!empty($grouped_courses_for_rollback)) {
								ClassRegistry::init('ExamSchedule')->deleteAll(array('ExamSchedule.published_course_id' => $grouped_courses_for_rollback));
								foreach($to_be_given_on_the_same_day as $to_be_given_key => $to_be_given) {
									$already_scheduled_pc_ids[] = $to_be_given['id'];
									$pc_in_successfull_list = array_search($to_be_given['id'], $successfully_scheduled_pc_ids);
									if($pc_in_successfull_list !== false) {
										unset($successfully_scheduled_pc_ids[$pc_in_successfull_list]);
									}
								}
								//Remove the date which was labeled before as exam free day
								//For each temp section
								foreach($exam_free_days_for_rollback as $section_id_k => $efd_list) {
									//For each section temp free day
									foreach($efd_list as $efd_k => $rb_exam_free_day_v) {
										//For each section recorded free day
										foreach($gap_exam_free_days[$section_id_k] as $section_fd_k => $free_day_v) {
											if($rb_exam_free_day_v == $free_day_v) {
												unset($gap_exam_free_days[$section_id_k][$section_fd_k]);
												break;
											}
										}
									}
								}
								
								$unable_to_schedule[$index]['reason'] = 'The system unable to get free class rooms for all identical courses which is supposed to be given on the same day and session. You have to either minimize the constraint on the published course or increase number of class rooms with enough capacity to accommodate all students.';
							}
							else {
								$unable_to_schedule[$index]['reason'] = 'The system unable to get free class rooms with exam capacity to accommodate all students within the available all free days and sessions to schedule the course. You have to either minimize the constraint on the published course or increase number of class rooms with enough capacity to accommodate all students.';
							}
						}
						else {
							$unable_to_schedule[$index]['reason'] = 'The system unable to get free day and session to schedule the course. You have to either minimize the constraint on the published course or increase the exam period.';
						}
						//debug('No room 2');
						break;
					}
					//Check for the next date
					else {
						$pc_session_found = false;
						$exam_date = date("Y-m-d", strtotime("+1 day", strtotime($exam_date)));
						$exam_days_check_count++;
						if($exam_date > $publishedCourse['end_date']) {
							$exam_date = $publishedCourse['start_date'];
						}
					}
				}
				else {
					break;
				}
				
				}//END: IF session is not found loop
			}//End for each published course

			if(count($successfully_scheduled_pc_ids) > 0 || !empty($unable_to_schedule)) {
				if(empty($unable_to_schedule)) {
					$this->Session->setFlash('<span></span>'.__('Exam schedule is successfully generated for '.count($successfully_scheduled_pc_ids).' published courses.'), 'default', array('class' => 'success-message success-box'));
					//$this->redirect(array('controller' => 'exam_schedules', 'action' => 'college_exam_schedule_view', $this->request->data['acadamic_year'], $this->request->data['semester']));
				}
				else if(!empty($unable_to_schedule)) {
					$this->Session->setFlash('<span></span>'.__('Exam schedule is successfully generated for '.count($successfully_scheduled_pc_ids).' published courses but the system failed to generate exam schedule for the following published courses.'), 'default', array('class' => 'warning-message warning-box'));
					foreach($unable_to_schedule as $k => $v) {
						$publishedCourse = ClassRegistry::init('PublishedCourse')->find('first',
							array(
								'conditions' =>
								array(
									'PublishedCourse.id' => $v['published_course_id']
								),
								'contain' =>
								array(
									'Course',
									'Section'
								)
							)
						);
						$unable_to_schedule[$k]['course'] = $publishedCourse['Course']['course_code'].' - '.$publishedCourse['Course']['course_title'];
						$unable_to_schedule[$k]['section'] = $publishedCourse['Section']['name'];
					}
					$this->set(compact('unable_to_schedule'));
				}
			}
			else {
				$this->Session->setFlash('<span></span>'.__('The system unable to get list of published courses for which exam schedule is not generated by the selected criteria. If there is already generated exam schedule by the selected criteria, you are required to cancel it to run the exam schedule again.'), 'default', array('class' => 'info-message info-box'));
			}
		}
		
		$departments = ClassRegistry::init('Department')->find('list',
			array(
				'conditions'=>
				array(
					'Department.college_id' => 
					$this->college_id
				)
			)
		);
		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');
		$semesters = array('I' => 'I', 'II' => 'II', 'III' => 'III');
		$max_year_level = ClassRegistry::init('YearLevel')->get_department_max_year_level($departments);
		$departments['FP'] = 'Freshman Program';
		for($i = 1; $i <= $max_year_level; $i++) {
			$yearLevels[$i] = $i.($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')));
		}
		$this->set(compact('departments', 'programs', 'programTypes', 'semesters', 'yearLevels'));
	}

}
?>
