<?php
class ExamResultsController extends AppController
{

	var $name = 'ExamResults';
	var $components = array('AcademicYear');
	var $menuOptions = array(
		'parent' => 'grades',
		'exclude' => array(
			'index', 
			'edit', 
			'delete', 
			'view', 
			'get_exam_result_entry_form', 
			'get_exam_result_view_page', 
			'cancel_grade_change_request',
			'get_exam_result_fx_entry_form',
			'get_registrar_assigned_grade_entry_form',
			'autoSaveResult',
			'rollback_entry_form',
			'grade_entry_form',
			//'download_exam_template'
		),
		'alias' => array(
			'submit_freshman_grade_for_instructor' => 'Freshman Course Exam & Grade',
			'submit_grade_for_instructor' => 'Department Course Exam & Grade',
			'add' => 'Your Course Exam & Grade',
			'submit_fx_grade' => 'Submit Fx Grade',
			'submit_assigned_grade' => 'Submit Registrar Grade Assignments',
			'rollback_grade_submission' => 'Rollback Grade Submission',
			'import_result' => 'Import Results from CSV'
			//'index'=>'View Exam Result',
			//'add'=>'Manage Exam Result',
			//'submit_grade_for_instructor' => 'Exam Management'
		)
	);

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow(
			'get_exam_result_view_page',
			'get_exam_result_entry_form',
			'get_exam_result_fx_entry_form',
			'download_exam_template',
			//'import_result',
			'get_registrar_assigned_grade_entry_form',
			'autoSaveResult',
			//'rollback_grade_submission', //ToRemove
			'rollback_entry_form', // TORemove
			'grade_entry_form'
			//'submit_grade_registrar',
			//'auto_grade_generation'	
		);

		if ($this->Session->check('Message.auth')) {
			$this->Session->delete('Message.auth');
		}
	}

	function beforeRender()
	{
		$defaultacademicyear = $current_academicyear = $this->AcademicYear->current_academicyear();
		$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/',$current_academicyear)[0]) - ACY_BACK_FOR_ALL), (explode('/', $current_academicyear)[0]));
		
		// debug($acyear_array_data);
		//$this->set('defaultacademicyear', $defaultacademicyear);

		$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
		$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1)));

		$depts_for_year_level = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		
		//$yearLevels = $this->year_levels;
		$yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(null , null, array_keys($depts_for_year_level), array_keys($programs));
		//debug($yearLevels);

		if ($this->role_id == ROLE_INSTRUCTOR) {
			$acyear_array_data = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->find('list', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id in (select id from staffs where user_id="' . $this->Auth->user('id') . '")'
				),
				'fields' => array('CourseInstructorAssignment.academic_year', 'CourseInstructorAssignment.academic_year'
				),
				'order' => array('CourseInstructorAssignment.academic_year' => 'DESC')
			));
		}

		if (($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_COLLEGE) && $this->Session->read('Auth.User')['is_admin'] == 0) {
			$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
			$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));
		}

		$this->set(compact('acyear_array_data', 'program_types', 'defaultacademicyear', 'programTypes', 'programs', 'yearLevels'));

		unset($this->request->data['User']['password']);
	}

	function index()
	{
		//Department
		if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/submit_grade_for_instructor')) {
			return $this->redirect(array('controller' => 'examResults', 'action' => 'submit_grade_for_instructor'));
		}
		//Freshman
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/submit_freshman_grade_for_instructor')) {
			return $this->redirect(array('controller' => 'examResults', 'action' => 'submit_freshman_grade_for_instructor'));
		}
		//Instructor
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/add')) {
			return $this->redirect(array('controller' => 'examResults', 'action' => 'add'));
		} 
		// Not Authorized
		else {
			//return $this->redirect(array('controller' => 'examGrades', 'action' => 'index'));
			$this->Flash->warning( __('Your are not authorized to access the page you just selected!'));
			return $this->redirect('/');
		}
	}

	//Hnadle "Grade Change Request" button click
	function __grade_change_request($source = 'instructor')
	{
		if (isset($this->request->data['ExamResult']['published_course_id'])) {
			$published_course_id = $this->request->data['ExamResult']['published_course_id'];
			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
			$students = $student_course_register_and_adds['register'];
			$student_adds = $student_course_register_and_adds['add'];
			$student_makeup = $student_course_register_and_adds['makeup'];
			//Searches for the clicked exam grade change button
			debug($this->request->data);

			if (!empty($student_course_register_and_adds)) {
				for ($i = 0; $i <= (count($students) + count($student_adds) + count($student_makeup)); $i++) {
					if (isset($this->request->data['grade_change_request_' . $i])) {
						$exam_grade_detail = $this->ExamResult->CourseRegistration->ExamGrade->find('first', array(
							'conditions' => array(
								'ExamGrade.id' => $this->request->data['ExamGradeChange'][$i]['exam_grade_id']
							),
							'contain' => array(
								'CourseRegistration' => array('PublishedCourse' => array('CourseInstructorAssignment' => array('conditions' => array('CourseInstructorAssignment.type LIKE \'%Lecture%\''), 'Staff'), 'Course')),
								'CourseAdd' => array('PublishedCourse' => array('CourseInstructorAssignment' => array('conditions' => array('CourseInstructorAssignment.type LIKE \'%Lecture%\''), 'Staff'), 'Course')),
								'ExamGradeChange' => array(
									'conditions' => 'ExamGradeChange.makeup_exam_result IS NOT NULL',
									'order' => array('ExamGradeChange.id' => 'DESC')
								)
							),
							'order' => array('ExamGrade.id' => 'DESC')
						));

						$days_available_for_grade_change = ClassRegistry::init('AcademicCalendar')->daysAvaiableForGradeChange();
						$days_available_for_grade_change = ClassRegistry::init('GeneralSetting')->daysAvaiableForGradeChange($published_course_id);
						//Considering makeup exam grade submission
						$date_grade_submited = ClassRegistry::init('AcademicCalendar')->getPublishedCourseGradeSubmissionDate($published_course_id);

						/*
						if(isset($exam_grade_detail['ExamGradeChange']) && !empty($exam_grade_detail['ExamGradeChange']))
							$date_grade_submited = $exam_grade_detail['ExamGradeChange'][0]['created'];
						else
							$date_grade_submited = $exam_grade_detail['ExamGrade']['created'];
						*/

						$grade_change_deadline = date('Y-m-d H:i:s', mktime(
							substr($date_grade_submited, 11, 2),
							substr($date_grade_submited, 14, 2),
							substr($date_grade_submited, 17, 2),
							substr($date_grade_submited, 5, 2),
							substr($date_grade_submited, 8, 2) + $days_available_for_grade_change,
							substr($date_grade_submited, 0, 4)
						));


						$any_grade_on_process = false;

						if (isset($exam_grade_detail['CourseRegistration']) && !empty($exam_grade_detail['CourseRegistration']) && $exam_grade_detail['CourseRegistration']['id'] != "") {
							$any_grade_on_process = $this->ExamResult->CourseRegistration->isAnyGradeOnProcess($exam_grade_detail['CourseRegistration']['id']);
							$last_grade = $this->ExamResult->CourseRegistration->getCourseRegistrationLatestGrade($exam_grade_detail['CourseRegistration']['id']);
						} else {
							$any_grade_on_process = $this->ExamResult->CourseAdd->isAnyGradeOnProcess($exam_grade_detail['CourseAdd']['id']);
							$last_grade = $this->ExamResult->CourseAdd->getCourseRegistrationLatestGrade($exam_grade_detail['CourseAdd']['id']);
						}

						$published_course_department = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
							'conditions' => array('PublishedCourse.id' => $published_course_id),
							'contain' => array('Section' => array('Department'))
						));

						debug($date_grade_submited);
						$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
						$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));

						$instructor_active = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('active', array('Staff.id' => $instructor_id_for_checking));

						debug($instructor_active);

						debug($instructor_id);
						debug($instructor_id_for_checking);

						/* $makeup_exam_detail = $this->ExamResult->ExamType->PublishedCourse->CourseRegistration->MakeupExam->find('first', array(
							'conditions' => array(
								'OR' => array(
									'MakeupExam.course_registration_id' => $exam_grade_detail['CourseRegistration']['id'],
									'MakeupExam.course_add_id' => $exam_grade_detail['CourseAdd']['id'],
								)
							),
							'order' => array('MakeupExam.created DESC'),
							'recursive' => -1
						)); */

						$makeup_exam_detail = array();

						if (!empty($exam_grade_detail['CourseRegistration']['id']) && $exam_grade_detail['CourseRegistration']['id'] > 0) {
							$makeup_exam_detail = $this->ExamResult->ExamType->PublishedCourse->CourseRegistration->MakeupExam->find('first', array(
								'conditions' => array(
									'MakeupExam.course_registration_id' => $exam_grade_detail['CourseRegistration']['id'],
								),
								'order' => array('MakeupExam.id' => 'DESC'),
								'recursive' => -1
							));
						} else if (!empty($exam_grade_detail['CourseAdd']['id']) && $exam_grade_detail['CourseAdd']['id'] > 0) {
							$makeup_exam_detail = $this->ExamResult->ExamType->PublishedCourse->CourseRegistration->MakeupExam->find('first', array(
								'conditions' => array(
									'MakeupExam.course_add_id' => $exam_grade_detail['CourseAdd']['id'],
								),
								'order' => array('MakeupExam.id' => 'DESC'),
								'recursive' => -1
							));
						} 

						//debug($grade_change_deadline.' >= '.date('Y-m-d H:i:s'));exit();
						//(!isset($exam_grade_detail['CourseRegistration']) || empty($exam_grade_detail['CourseRegistration']) || $exam_grade_detail['CourseRegistration']['id'] == "" || $published_course_id == $exam_grade_detail['CourseRegistration']['published_course_id'] || $instructor_id == $instructor_id_for_checking || ($this->role_id == 6 && $this->department_id == $published_course_department['Section']['Department']['id'])) && (!isset($exam_grade_detail['CourseAdd']) || empty($exam_grade_detail['CourseAdd']) || $exam_grade_detail['CourseAdd']['id'] == "" || $instructor_id == $instructor_id_for_checking || $published_course_id == $exam_grade_detail['CourseAdd']['published_course_id'] || ($this->role_id == 6 && $this->department_id == $published_course_department['Section']['Department']['id'])) &&
						//Department checking is removed
						//Department checking is removed
						debug($grade_change_deadline);
						debug($grade_change_deadline >= date('Y-m-d H:i:s'));
						debug(!$any_grade_on_process);


						if (!empty($exam_grade_detail) && ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR  || (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) && !$instructor_active))) {
							if ((/* !isset($exam_grade_detail['CourseRegistration']) || empty($exam_grade_detail['CourseRegistration']) ||  (isset($exam_grade_detail['CourseRegistration']['id']) && empty($exam_grade_detail['CourseRegistration']['id'])) || */ (isset($exam_grade_detail['CourseRegistration']['published_course_id']) && !empty($exam_grade_detail['CourseRegistration']['published_course_id']) && $published_course_id == $exam_grade_detail['CourseRegistration']['published_course_id']) || (!empty($instructor_id_for_checking) && $instructor_id == $instructor_id_for_checking) || ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $this->department_id == $published_course_department['Section']['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && empty($published_course_department['Section']['department_id']) && $this->college_id == $published_course_department['Section']['college_id'])) &&
								(/* !isset($exam_grade_detail['CourseAdd']) || empty($exam_grade_detail['CourseAdd']) || (isset($exam_grade_detail['CourseAdd']['id']) && empty($exam_grade_detail['CourseAdd']['id'])) || */ (!empty($instructor_id_for_checking) && $instructor_id == $instructor_id_for_checking) || (isset($exam_grade_detail['CourseAdd']['published_course_id']) && !empty($exam_grade_detail['CourseAdd']['published_course_id']) && $published_course_id == $exam_grade_detail['CourseAdd']['published_course_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $this->department_id == $published_course_department['Section']['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && empty($published_course_department['Section']['department_id']) && $this->college_id == $published_course_department['Section']['college_id'])) &&
								((isset($exam_grade_detail['CourseRegistration']['published_course_id']) && !empty($exam_grade_detail['CourseRegistration']['published_course_id']) && $published_course_id == $exam_grade_detail['CourseRegistration']['published_course_id']) ||
									(isset($exam_grade_detail['CourseAdd']['published_course_id']) && !empty($exam_grade_detail['CourseAdd']['published_course_id'])  && $published_course_id == $exam_grade_detail['CourseAdd']['published_course_id']) ||
									(empty($makeup_exam_detail) || (isset($makeup_exam_detail['MakeupExam']['published_course_id']) && $makeup_exam_detail['MakeupExam']['published_course_id'] == $published_course_id))
								) && $exam_grade_detail['ExamGrade']['department_approval'] == 1 && $exam_grade_detail['ExamGrade']['registrar_approval'] == 1 && $grade_change_deadline >= date('Y-m-d H:i:s') && !$any_grade_on_process
							) {
								if ($this->request->data['ExamGradeChange'][$i]['result'] == "" || !is_numeric($this->request->data['ExamGradeChange'][$i]['result']) || $this->request->data['ExamGradeChange'][$i]['result'] < 0 || $this->request->data['ExamGradeChange'][$i]['result'] > 100 || trim($this->request->data['ExamGradeChange'][$i]['reason']) == "") {
									$this->Flash->error(__('The system unable to process your grade change request due to incomplete data. Please make sure that you submited exam result, reason for change and the new grade is different from the previous one.'));
								} else {
									$this->request->data['ExamGradeChange'][$i]['grade'] = $this->ExamResult->getTotalResultGrade($this->request->data['ExamGradeChange'][$i]['result'], $published_course_id);
									//$this->request->data['ExamGradeChange'][$i]['initiated_by_department'] = ($this->role_id == 6 ? 1 : 0);
									$this->request->data['ExamGradeChange'][$i]['initiated_by_department'] = (!empty($instructor_id_for_checking) && $instructor_id == $instructor_id_for_checking ? 0 : 1);
									if ($last_grade != $this->request->data['ExamGradeChange'][$i]['grade']) {
										// for debuging only not for production

										/* $this->request->data['ExamGradeChange'][$i]['minute_number'] = '';
										$this->request->data['ExamGradeChange'][$i]['department_reason'] = '';
										$this->request->data['ExamGradeChange'][$i]['registrar_reason'] = '';
										$this->request->data['ExamGradeChange'][$i]['college_reason'] = '';
										$this->request->data['ExamGradeChange'][$i]['department_approved_by'] = '';
										$this->request->data['ExamGradeChange'][$i]['registrar_approved_by'] = '';
										$this->request->data['ExamGradeChange'][$i]['college_approved_by'] = ''; */

										if ($this->ExamResult->CourseRegistration->ExamGrade->ExamGradeChange->save($this->request->data['ExamGradeChange'][$i], array('validate' => false))) {
											
											//Retrieving student detail to get his/her name
											if (isset($exam_grade_detail['CourseRegistration']) && !empty($exam_grade_detail['CourseRegistration']) && $exam_grade_detail['CourseRegistration']['id'] != "") {
												$student_detail = $this->ExamResult->CourseRegistration->find('first', array('conditions' => array('CourseRegistration.id' => $exam_grade_detail['CourseRegistration']['id']), 'contain' => array('Student')));
											} else {
												$student_detail = $this->ExamResult->CourseAdd->find('first', array('conditions' => array('CourseAdd.id' => $exam_grade_detail['CourseAdd']['id']), 'contain' => array('Student')));
											}

											//Instructor notification
											if (isset($exam_grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['user_id']) && !empty($exam_grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['user_id'])) {
												
												$acy_semester = '';
												if (isset($exam_grade_detail['CourseRegistration']['PublishedCourse']['Course']) && !empty($exam_grade_detail['CourseRegistration']['PublishedCourse']['Course'])) {
													$course_title = $exam_grade_detail['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $exam_grade_detail['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ')';
													$acy_semester = ' from ' . ($exam_grade_detail['CourseRegistration']['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_detail['CourseRegistration']['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) .' semester of ' . $exam_grade_detail['CourseRegistration']['PublishedCourse']['academic_year'];
												} else {
													$course_title = $exam_grade_detail['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $exam_grade_detail['CourseAdd']['PublishedCourse']['Course']['course_code'] . ')';
													$acy_semester = ' from ' . ($exam_grade_detail['CourseAdd']['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_detail['CourseAdd']['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) .' semester of ' . $exam_grade_detail['CourseAdd']['PublishedCourse']['academic_year'];
												}

												$auto_message['AutoMessage']['message'] = ($source != 'instructor' ? ('Grade change is initiated by ' . ($source == 'department' ? 'department' : 'college') . ' for <u>' . $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')</u> for the course <u>' . $course_title . '</u>') : ('You requested a grade change for <u>' . $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')</u>  for the course <u>' . $course_title . '</u>') . $acy_semester . '.');
												$auto_message['AutoMessage']['read'] = 0;
												$auto_message['AutoMessage']['user_id'] = $exam_grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['user_id'];
												
												ClassRegistry::init('AutoMessage')->save($auto_message);
											}

											$this->Flash->success(__('Grade change request for ' . ($student_detail['Student']['first_name'] . ' ' . $student_detail['Student']['middle_name'] . ' ' . $student_detail['Student']['last_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')') . ' is sent to ' . (!empty($student_detail['Student']['department_id']) ? 'department' : 'freshman program') . ' for approval.'));
										} else {
											$this->Flash->error( __('The system is unable to process your grade change request. Please try again.'));
										}
										//debug($this->request->data['ExamGradeChange'][$i]);
									} else {
										$this->Flash->error( __('There is no difference between the already recorded and new grade. Please make sure that you submitted a different grade.'));
									}
								}
							} else {
								$this->Flash->error( __('The system unable to process your grade change request. You are either accessing a different course or trying to send grade change request for non submitted or on process or rejected grade. Please try your request again within the grade change period.'));
								//return $this->redirect(array('action' => (strcasecmp($source, 'instructor') == 0 ? 'add' : (strcasecmp($source, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
							}
						} else if (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) && $instructor_active) {
							$this->Flash->error( __('The system unable to process your grade change request. The instructor assigned for this course is active, Grade change request must be submitted by the assigned instructor. If the assigned instructor is not active, you can request a user deactivation request for the assigned instructor and come back when your deactivation request is approved by system administrators.'));
						}
					}
				} //End of foreach loop which searches for the clicked exam grade change button
			}
		}
	}


	public function add($published_course_id = null)
	{
		//The following three function calling is for Student Status test and can be deleted at any time
		//$this->ExamResult->CourseAdd->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse(211);
		///$this->ExamResult->CourseAdd->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse(122);// 2011/12/I 122 2010/11/II 29
		//$this->ExamResult->CourseAdd->Student->StudentExamStatus->updateAcdamicStatusForGradeChange(199);
		//$this->ExamResult->CourseAdd->Student->StudentExamStatus->updateAcdamicStatusForGradeChange(217, 'register');
		//$this->ExamResult->CourseAdd->Student->StudentExamStatus->isStudentPassed(84, '2011/12');
		//$this->ExamResult->CourseAdd->getCourseAdds(124, array(), 236, 1);
		//$this->ExamResult->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToGenerateStatus(124, '2011/12', 'I');

		

		if (!empty($published_course_id) && $published_course_id > 0) {
			$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
			$list_of_assigned_courses_for_instructor = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->find('list', array('conditions' => array('CourseInstructorAssignment.staff_id' => $instructor_id), 'fields' => array('CourseInstructorAssignment.published_course_id', 'CourseInstructorAssignment.published_course_id')));
			//debug($list_of_assigned_courses_for_instructor);
			//debug(count($list_of_assigned_courses_for_instructor));
			if ((!empty($list_of_assigned_courses_for_instructor) && !in_array($published_course_id, $list_of_assigned_courses_for_instructor)) || empty($list_of_assigned_courses_for_instructor)) {
				// trying to cheat
				$this->Flash->warning(__('You are trying to access published course that is not assigned to you!, This incident will be reported. Please do not try this again.'));
				$message = '<u>' . $this->Session->read('Auth.User')['full_name'] . ' (' . $this->Session->read('Auth.User')['username'] . ')</u> is trying to access not assigned course exam result management. Please give appropriate warning.';
				ClassRegistry::init('AutoMessage')->sendInappropriateAccessAttempt($this->Auth->user('id'), $message);
				return $this->redirect('/');
			}
		}

		if (/* !$published_course_id && */!empty($this->request->data)) {
			if (!empty($published_course_id) && isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id']) && $published_course_id != $this->request->data['ExamResult']['published_course_id']) {
				$this->redirect(array('action' => 'add', $this->request->data['ExamResult']['published_course_id']));
			}

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR && isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$this->__grade_change_request('instructor');
			}
			
			if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamResult']['published_course_id'];
			}
			//$this->redirect(array('action' => 'add', $this->request->data['ExamResult']['published_course_id']));
		}

		$this->__exam_result_and_grade_mgt($published_course_id, 'instructor');
	}

	public function submit_fx_grade($published_course_id = null)
	{
		if (!$published_course_id && !empty($this->request->data)) {
			if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamResult']['published_course_id'];
			}
		}

		$this->__exam_result_and_grade_mgt_fx($published_course_id, 'instructor');
	}

	public function submit_assigned_grade($published_course_id = null)
	{
		if (!$published_course_id && !empty($this->request->data)) {
			if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamResult']['published_course_id'];
			}
		}

		$this->__data_entry_assignment($published_course_id, 'instructor');
	}


	public function submit_freshman_grade_for_instructor($published_course_id = null)
	{
		//Role based checking is now removed
		//if($this->role_id == 5 || $this->role_id == 6) {
		if (/* $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT ||  */$this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			//Check if change grade button is clicked
			if (!$published_course_id && !empty($this->request->data)) {
				if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
					if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
						$this->__grade_change_request('college'); // commented by Neway, temporarly
					} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
						$this->__grade_change_request('department'); // commented by Neway, temporarly
					}
				}
				/* I do not know why I put this code but it is protecting me from saving preview and performing other tasks and I disabled it now*/
				/* if (isset($this->request->data['ExamResult']['published_course_id'])) {
					$this->redirect(array('action' => 'submit_freshman_grade_for_instructor', $this->request->data['ExamResult']['published_course_id']));
				} */
			}

			$published_courses_by_section = array();
			$published_course_combo_id = "";
			$grade_scale = array();

			$programs = $this->ExamResult->CourseRegistration->PublishedCourse->Section->Program->find('list');
			$program_types = $this->ExamResult->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

			if (!empty($this->request->data)) {

				if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
					$college_id = $this->college_id;
					$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($college_id, $this->request->data['ExamResult']['acadamic_year'], $this->request->data['ExamResult']['semester'], $this->request->data['ExamResult']['program_id'], $this->request->data['ExamResult']['program_type_id']); 
				} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
					$department_id = $this->department_id;
					$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $this->request->data['ExamResult']['acadamic_year'], $this->request->data['ExamResult']['semester'], $this->request->data['ExamResult']['program_id'], $this->request->data['ExamResult']['program_type_id']);
				}
				
				if (empty($publishedCourses)) {
					$this->flash->info(__('There is no published courses for the selected filter criteria.'));
				} else {
					$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
				}
				$this->set(compact('publishedCourses'));
			} else {
				$publishedCourses = array();
				$published_course_combo_id = $published_course_id;
				$this->set(compact('publishedCourses'));
			}

			if ($published_course_id == null && isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamResult']['published_course_id'];
			}
			
			if (isset($this->request->data['listPublishedCourses'])) {
				/*****************************  Begin "List Published Courses" button  ****************************/
				/* if (isset($this->request->data['ExamResult']['published_course_id'])) {
					$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($this->request->data['ExamResult']['published_course_id']);
					$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($this->request->data['ExamResult']['published_course_id']);
				} */
				//$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($this->request->data['ExamResult']['published_course_id']);

				unset($this->request->data['ExamResult']['published_course_id']);
				$published_course_id = $published_course_combo_id = "";

			} else {
				/**************  Save Exam Result, Preview Grade, or other buttons are clicked  ******************/
				if (!empty($published_course_id) && !empty($publishedCourses)) {
					if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
						$this->__exam_result_and_grade_mgt($published_course_id, 'college');
					} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
						$this->__exam_result_and_grade_mgt($published_course_id, 'department');
					}
				}
			}

			if (empty($grade_scale) && !empty($published_course_id)) {
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
			}

			$this->set(compact('grade_scale', 'programs', 'program_types', 'gradeStatistics', 'published_course_id', 'published_course_combo_id'));

		} else {
			$this->Flash->error(__('You need to have either college or department role to access this area. Please contact your system administrator to get college or department role.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	public function submit_grade_for_instructor($published_course_id = null)
	{
		//Role based checking is now removed
		//if($this->role_id == 6) {
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT /* || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE */) {

			//Check if change grade button is clicked
			if (!$published_course_id && !empty($this->request->data)) {
				if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
					$this->__grade_change_request('department'); // commented by Neway, temporarly
				}
				/* I do not know why I put this code but it is protecting me from saving preview  and performing other tasks and I disabled it now*/
				/* if (isset($this->request->data['ExamResult']['published_course_id'])) {
					$this->redirect(array('action' => 'submit_grade_for_instructor', $this->request->data['ExamResult']['published_course_id']));
				} */
			}

			$published_courses_by_section = array();
			$published_course_combo_id = "";
			$department_id = "";
			$academic_year = "";
			$semester = "";
			$program_id = "";
			$program_type_id = "";
			$college_id = "";

			$grade_scale = array();
			$programs = $this->ExamResult->CourseRegistration->PublishedCourse->Section->Program->find('list');
			$program_types = $this->ExamResult->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

			if (!empty($this->request->data) && $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$department_id = $this->department_id;
				$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $this->request->data['ExamResult']['acadamic_year'], $this->request->data['ExamResult']['semester'], $this->request->data['ExamResult']['program_id'], $this->request->data['ExamResult']['program_type_id']);
				if (empty($publishedCourses)) {
					$this->Flash->info( __('There is no published courses for the selected filter criteria.'));
				} else {
					$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
				}
				$this->set(compact('publishedCourses'));
			} else {
				$publishedCourses = array();
				$published_course_combo_id = $published_course_id;
				$this->set(compact('publishedCourses'));
			}

			if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$published_course_combo_id = $published_course_id = $this->request->data['ExamResult']['published_course_id'];
			}
			
			if (isset($this->request->data['listPublishedCourses'])) {
				/*****************************  Begin "List Published Courses" button  ****************************/
				/* if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
					$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($this->request->data['ExamResult']['published_course_id']);
					$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($this->request->data['ExamResult']['published_course_id']);
				} */
				//$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($this->request->data['ExamResult']['published_course_id']);
				unset($this->request->data['ExamResult']['published_course_id']);
				$published_course_id = $published_course_combo_id = "";
			} else {
				/**************  Save Exam Result, Preview Grade, or other buttons are clicked  ******************/
				//$this->__exam_result_and_grade_mgt($published_course_id, 'department');
				if (!empty($published_course_id) && !empty($publishedCourses)) {
					$this->__exam_result_and_grade_mgt($published_course_id, 'department');
				}
			}

			if (!empty($published_course_id)) {

				if (empty($grade_scale)) {
					$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
					$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
				}
				
				$published_course = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1));

				$department_id = $published_course['PublishedCourse']['department_id'];
				$college_id = $published_course['PublishedCourse']['college_id'];
				$academic_year = $published_course['PublishedCourse']['academic_year'];
				$program_id = $published_course['PublishedCourse']['program_id'];
				$program_type_id = $published_course['PublishedCourse']['program_type_id'];
				$semester = $published_course['PublishedCourse']['semester'];
			}

			$this->set(compact('grade_scale', 'programs', 'program_types', 'gradeStatistics', 'published_course_id', 'published_course_combo_id', 'department_id', 'academic_year', 'semester', 'program_id', 'program_type_id', 'college_id'));
		} else {
			$this->Flash->error(__('You need to have department role to access this area. Please contact your system administrator to get department role.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	private function __exam_result_and_grade_mgt($published_course_id = null, $sourse = 'instructor')
	{
		if ($sourse == "instructor" && empty($published_course_id)) {
			$selectedAcadamicYear = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->find('first', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id in (select id from staffs where user_id="' . $this->Auth->user('id') . '")'
				),
				'fields' => array('CourseInstructorAssignment.academic_year', 'CourseInstructorAssignment.academic_year'),
				'order' => array('CourseInstructorAssignment.academic_year' => 'DESC')
			));

			if (!empty($selectedAcadamicYear)) {
				$selected_acadamic_year = $selectedAcadamicYear['CourseInstructorAssignment']['academic_year'];
			}
		} else {
			$selected_acadamic_year = $this->AcademicYear->current_academicyear();
		}

		$selected_semester = 'I';
		//$published_course_id = "";
		$published_course_combo_id = "";
		$students = array();
		$exam_results = array();
		$makeup_exam_results = array();
		$exam_types = array();
		$exam_result_delete_ids = array();
		$save_is_ok = true;
		$student_adds = array();
		$student_makeup = array();
		$display_grade = false;
		$do_not_manipulate = false;
		$view_only = false;
		$date_grade_submited = date('Y-m-d H:i:s');

		$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
		
		if (!empty($published_course_id)) {
			$exam_types = $this->ExamResult->ExamType->find('all', array(
				'fields' => array('id', 'exam_name', 'percent', 'order'), 'conditions' => array('ExamType.published_course_id' => $published_course_id),
				'contain' => array(),
				'order' => array('order' => 'ASC'),
				'recursive' => -1
			));

			$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id
				), 
				'contain' => array(
					'Section', 
					'Course',
					'CourseInstructorAssignment' => array(
						'conditions' => array('CourseInstructorAssignment.isprimary' => true),
						'Staff' => array('Title', 'Position')
					)
				)
			));

			if (!empty($section_and_course_detail['Section'])) {
				$section_detail = $section_and_course_detail['Section'];
			}

			if (!empty($section_and_course_detail['Course'])) {
				$course_detail = $section_and_course_detail['Course'];
			}

			if (!empty($section_and_course_detail['CourseInstructorAssignment'])) {
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];
			}

			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
			$students = $student_course_register_and_adds['register'];
			$student_adds = $student_course_register_and_adds['add'];
			$student_makeup = $student_course_register_and_adds['makeup'];
			//debug($students);
			$total_student_count = count($students) + count($student_adds) + count($student_makeup);
			$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);
			$published_course_combo_id = $published_course_id;
			//debug($student_course_register_and_adds);
			//$do_not_manipulate = true;
		}

		//Checking if the user is eligible to manage published course exam result and grade
		if (isset($this->request->data) && !empty($this->request->data['ExamResult']['published_course_id'])) {
			$published_course_id = $this->request->data['ExamResult']['published_course_id'];
		}

		if (!empty($published_course_id)) {

			$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
			$published_course_department = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section' => array('Department'))));
			
			//Do you have the right to manage exam result and grade
			$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
			$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));

			if (!(($this->department_id == $published_course_department['Section']['department_id'] && $active_account == 0 && isset($this->request->data))
				//Permitted if there is no data is submitted or the submited data is grade change
				|| ($this->department_id == $published_course_department['Section']['department_id'] && (!isset($this->request->data) || isset($this->request->data['ExamGradeChange'])))
				|| ($this->college_id == $published_course_department['PublishedCourse']['college_id'] && $active_account == 0 && isset($this->request->data))
				|| ($this->college_id == $published_course_department['PublishedCourse']['college_id'] && !isset($this->request->data))
				|| ($instructor_id_for_checking == $instructor_id))) {

				/* this  creating redirection loop ? we need to test the grade submission by pre, and decide either to comment or figure our why the redirection happened ?
				$this->Session->setFlash('<span></span>'.__('Sorry the selected published course is not assaigned to you to enter, preview and submit exam result and grade. Please select a valid published course again.'), 'default', array('class' => 'error-message error-box'));
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id)); */
			}
			//End of do you have the right to manage exam result and grade

			//Do they have view only access? (It is not neccessary as the user is trapped on the above checking)
			$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

			if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
				$view_only = true;
			}
			//End for do they have view only access?
		}

		if (!$do_not_manipulate && !empty($this->request->data)) {

			$exam_types = $this->ExamResult->ExamType->find('all', array(
				'fields' => array('id', 'exam_name', 'percent', 'order', 'mandatory'),
				'conditions' => array('ExamType.published_course_id' => $published_course_id),
				'contain' => array(),
				'order' => array('order' => 'ASC'),
				'recursive' => -1
			));

			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
			$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);
			
			/*******************************  Begin "Save Exam Result" button  ***********************************/

			if (isset($this->request->data['saveExamResult']) || isset($this->request->data['previewExamGrade'])) {
				$exam_inconsistentcy_found = false;

				foreach ($this->request->data['ExamResult'] as $key => $exam_result) {
					
					$fraud = false;

					if (is_array($exam_result)) {
						//debug($exam_result);
						//SECURITY PASS

						//Check if complete data is sent
						if (!isset($exam_result['result']) || (!isset($exam_result['id']) && !isset($exam_result['exam_type_id'])) || (isset($exam_result['exam_type_id']) && (!isset($exam_result['exam_type_id']) || (!isset($exam_result['course_registration_id']) && !isset($exam_result['course_add']))))) {
							//Some exam result data is missed when it is sent from the client machine
							$fraud = true;
						} else {

							//Exam result editing fraud checking
							if (isset($exam_result['id'])) {

								//Check if the result is for the published course
								$exam_result_detail = $this->ExamResult->find('first', array(
									'conditions' => array('ExamResult.id' => $exam_result['id']),
									'contain' => array('CourseRegistration', 'CourseAdd')
								));
								
								if (($exam_result_detail['ExamResult']['course_add'] == 1 && $exam_result_detail['CourseAdd']['published_course_id'] != $published_course_id) || ($exam_result_detail['ExamResult']['course_add'] == 0 && $exam_result_detail['CourseRegistration']['published_course_id'] != $published_course_id)) {
									//Exam result is some other published course result
									$fraud = true;
								}

								//Check if grade is already submitted
								if ($exam_result_detail['ExamResult']['course_add'] == 0) {
									
									$course_grade_submited = $this->ExamResult->CourseRegistration->ExamGrade->find('first', array(
										'conditions' => array(
											'ExamGrade.course_registration_id' => $exam_result_detail['ExamResult']['course_registration_id'],
											//'ExamGrade.department_approval <> -1'
										),
										'order' => array('ExamGrade.created' => 'DESC')
									));

									$other_exam_result_details = $this->ExamResult->find('all', array(
										'conditions' => array(
											'ExamResult.exam_type_id' => $exam_result_detail['ExamResult']['exam_type_id'],
											'ExamResult.course_registration_id' => $exam_result_detail['ExamResult']['course_registration_id'],
											'ExamResult.course_add' => 0,
											'ExamResult.id <> ' => $exam_result_detail['ExamResult']['id']
										),
										'recursive' => -1
									));

								} else {

									$course_grade_submited = $this->ExamResult->CourseAdd->ExamGrade->find('first', array(
										'conditions' => array(
											'ExamGrade.course_add_id' => $exam_result_detail['ExamResult']['course_registration_id'],
											//'ExamGrade.department_approval <> -1'
										),
										'order' => array('ExamGrade.created' => 'DESC')
									));

									$other_exam_result_details = $this->ExamResult->find('all', array(
										'conditions' => array(
											'ExamResult.exam_type_id' => $exam_result_detail['ExamResult']['exam_type_id'],
											'ExamResult.course_registration_id' => $exam_result_detail['ExamResult']['course_registration_id'],
											'ExamResult.course_add' => 1,
											'ExamResult.id <> ' => $exam_result_detail['ExamResult']['id']
										),
										'recursive' => -1
									));
								}

								if (isset($course_grade_submited['ExamGrade']) && $course_grade_submited['ExamGrade']['department_approval'] != -1) {
									//Grade is already submited for the to be edited exam result
									$fraud = true;
								}
								
								/* 
									-On update check that the ID actually exists. Otherwise reject the saving process.
									-Check also if there is more than one result with that ID's exam type and (registration or add or makeup)
								*/

								//Check for exam result entry consistency

								if (empty($exam_result_detail)) {
									$fraud = true;
								} else if (!empty($other_exam_result_details)) {
									//If there is other exam result entry by the same exam type and course registration or course add
									foreach ($other_exam_result_details as $other_exam_result_detail) {
										$this->ExamResult->delete($other_exam_result_detail['ExamResult']['id']);
									}
									$exam_inconsistentcy_found = true;
								}

							} else {

								//New result entry validation
								if ($exam_result['course_add'] == 0) {
									$exam_result_published_course_id = $this->ExamResult->CourseRegistration->field('published_course_id', array('CourseRegistration.id' => $exam_result['course_registration_id']));
								} else {
									$exam_result_published_course_id = $this->ExamResult->CourseAdd->field('published_course_id', array('CourseAdd.id' => $exam_result['course_registration_id']));
								}

								if ($published_course_id != $exam_result_published_course_id) {
									//Exam result is some other published course result
									$fraud = true;
								}

								//Check if grade is already submitted
								if ($exam_result['course_add'] == 0) {

									$course_grade_submited = $this->ExamResult->CourseRegistration->ExamGrade->find('count', array(
										'conditions' => array(
											'ExamGrade.course_registration_id' => $exam_result['course_registration_id'],
											'ExamGrade.department_approval <> -1',
											'ExamGrade.registrar_approval <> -1'
										)
									));

								} else {

									$course_grade_submited = $this->ExamResult->CourseAdd->ExamGrade->find('count', array(
										'conditions' => array(
											'ExamGrade.course_add_id' => $exam_result['course_registration_id'],
											'ExamGrade.department_approval <> -1',
											'ExamGrade.registrar_approval <> -1'
										)
									));

								}

								if ($course_grade_submited > 0) {
									//Grade is already submited for the to be entered exam result
									$fraud = true;
								}

								/*On save make sure that there is no result recorded with the given exam type and registration or add. If there is any, delete all records.*/
								
								$other_exam_result_details = $this->ExamResult->find('all', array(
									'conditions' => array(
										'ExamResult.exam_type_id' => $exam_result['exam_type_id'],
										'ExamResult.course_registration_id' => $exam_result['course_registration_id'],
										'ExamResult.course_add' => $exam_result['course_add'],
									),
									'recursive' => -1
								));

								//If there are already recorded results, it has to be deleted for the new exam result entry
								
								if (!empty($other_exam_result_details)) {
									foreach ($other_exam_result_details as $other_exam_result_detail) {
										$this->ExamResult->delete($other_exam_result_detail['ExamResult']['id']);
									}
								}
							} //END: New result entry validation
						}

						if ($fraud) {
							$this->Flash->error( __('The system encountered an error while processing your exam result submission. Please try your submission again.'));
							//$save_is_ok = false;
							return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
						}

						if (trim($exam_result['result']) != "") {

							$exam_results[$key] = $exam_result;
							$exam_percent = "";
							$exam_type_id = "";
							$exam_name = "";

							if (isset($exam_result['id'])) {
								$exam_type_id = $this->ExamResult->field('exam_type_id', array('id' => $exam_result['id']));
							} else {
								$exam_type_id = $exam_result['exam_type_id'];
							}

							//Check if exam type is available
							if (!empty($exam_types)) {
								foreach ($exam_types as $key => $exam_type) {
									if ($exam_type['ExamType']['id'] == $exam_type_id) {
										$exam_percent = $exam_type['ExamType']['percent'];
										$exam_name = $exam_type['ExamType']['exam_name'];
										break;
									}
								}
							}

							if ($exam_percent == "" || $exam_name == "" || $exam_type_id == "") {
								$this->Flash->error(__('Exam setup is not found for some results, Probably you applied changes on the exam setup. Please provide the exam result again.'));
								//$this->redirect(array('action' => 'add', $published_course_id));
								$save_is_ok = false;
							} else if (is_numeric($exam_result['result']) && $exam_result['result'] > $exam_percent) {
								$this->Flash->error(__('Exam result for ' . $exam_name . ' exam can only be a maximum of ' . $exam_percent . '. To change the percentage, please go to the exam setup section.'));
								//$this->redirect(array('action' => 'add', $published_course_id));
								$save_is_ok = false;
							}
						} else if (isset($exam_result['id'])) {
							$exam_result_delete_ids[] = $exam_result['id'];
						}
					}
				}

				if ($exam_inconsistentcy_found) {
					$this->Flash->error(__('The system encountered an error while processing your exam result submission. Please make sure that the exam results are consistent with what you are trying to enter and try your submission again.'));
					//$save_is_ok = false;
					return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				}

				//Makeup exam result data collection and formating
				//debug($this->request->data);
				if (isset($this->request->data['MakeupExam']) && !empty($this->request->data['MakeupExam'])) {
					foreach ($this->request->data['MakeupExam'] as $key => $makeup_exam) {
						$makeup_exam_published_course_id = $this->ExamResult->CourseRegistration->MakeupExam->field('published_course_id', array('id' => $makeup_exam['id']));
						if ($makeup_exam_published_course_id != $published_course_id) {
							$this->Flash->error(__('Sorry, the system is unable to get some students for makeup exam result entry. This usually happens when the department apply a change while you make exam result submission. Please re-submit your exam result entry.'));
							$save_is_ok = false;
						} else if (trim($makeup_exam['result']) != "" && (!is_numeric($makeup_exam['result']) || $makeup_exam['result'] > 100 || $makeup_exam['result'] < 0)) {
							//$this->ExamResult->saveAll($exam_results, array('validate' => 'only'));
							$this->Flash->error( __('The system is unable to record exam result entry. Please make sure that all your exam result entry is valid.'));
							$save_is_ok = false;
						} else {
							$makeup_exam_results[$key]['id'] = $makeup_exam['id'];
							$makeup_exam_results[$key]['result'] = (trim($makeup_exam['result']) === "" ? null : $makeup_exam['result']);
						}
					}
				}

				//debug($makeup_exam_results);
				debug($exam_results);
				//debug($exam_result_delete_ids);
				//debug($exam_results);exit();
				$this->ExamResult->create();
				$this->set($exam_results);

				if ($save_is_ok) {
					if ($this->ExamResult->saveAll($exam_results)) {
						if (!empty($exam_result_delete_ids)) {
							if (!$this->ExamResult->deleteAll(array('ExamResult.id' => $exam_result_delete_ids), false)) {
								$this->Flash->error(__('Exam result entry/update is saved but deletion for previous exam result is interrupted. Please check your exam result entry and/or update for consistency.'));
								return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
								//$this->redirect(array('action'=>'add', $published_course_id));
								//redirect to add page
							}
						}

						//Makeup exam result entry
						if (!empty($makeup_exam_results)) {
							$makeupExam = ClassRegistry::init('MakeupExam');
							$makeupExam->set($makeup_exam_results);
							if (!ClassRegistry::init('MakeupExam')->saveAll($makeup_exam_results, array('validate' => false))) {
								$this->Flash->error(__('Exam result entry/update for makeup exam is failed. Please re-enter the makeup exam result.'));
								return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
								//$this->redirect(array('action'=>'add', $published_course_id));
								//redirect to add page
							}
						}

						if (!isset($this->request->data['previewExamGrade'])) {
							$this->Flash->success(__('Exam result has been saved.'));
							return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
						}
					} else {
						$this->Flash->error(__('The exam result could not be saved. Please, try again.'));
						//$this->redirect(array('action' => 'add', $published_course_id));
					}
				}
				
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);
			} //End of "Save Exam Result" button


			/*************************** Begin "Preview Grade" button  **********************************/
			if (isset($this->request->data['previewExamGrade'])) {

				foreach ($this->request->data['ExamResult'] as $key => $exam_result) {
					if (is_array($exam_result)) {
						unset($this->request->data['ExamResult'][$key]);
					}
				}

				unset($this->request->data['InProgress']);

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				$course_required_scale = $this->ExamResult->CourseRegistration->PublishedCourse->isPublishedCourseRequiredScale($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);

				//debug($exam_types);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);

				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

				if (!isset($grade_scale['error'])) {
					$students = $this->ExamResult->generateCourseGrade($students, $grade_scale, $exam_types);
					$student_adds = $this->ExamResult->generateCourseGrade($student_adds, $grade_scale, $exam_types);
					$student_makeup = $this->ExamResult->generateCourseGrade($student_makeup, $grade_scale, $exam_types);
					$display_grade = true;
				} else {
					$this->Flash->error( __($grade_scale['error']));
					return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				}
				
			} else if (isset($this->request->data['submitExamGrade'])) {

				/*************************** Begin "Submit Grade" button  **********************************/

				$students_course_in_progress = array();

				if (isset($this->request->data['InProgress'])) {
					foreach ($this->request->data['InProgress'] as $key => $course_in_progress) {
						if ($course_in_progress['in_progress'] != 0) {
							$students_course_in_progress[] = $course_in_progress['in_progress'];
						}
					}
				}

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				
				$grade_submit_status = $this->ExamResult->submitGrade($student_course_register_and_adds, $students_course_in_progress, $grade_scale, $exam_types);

				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

				if (isset($grade_submit_status['error'])) {
					$this->Flash->error( __($grade_submit_status['error']));
				} else if (count($grade_submit_status['course_registration_add']) > 0 || count($grade_submit_status['makeup_exam']) > 0) {
					$this->Flash->success( __('Exam grade for ' . (count($grade_submit_status['course_registration_add']) + count($grade_submit_status['makeup_exam'])) . ' student' . ((count($grade_submit_status['course_registration_add']) + count($grade_submit_status['makeup_exam'])) > 1 ? 's' : '') . ' is successfully submitted to the department for approval. You can cancel your grade submission to apply changes, if needed, before it is approved by the department. Once the grades are approved, neither you can cancel the submitted grade nor edit exam result except for students with "In Progress" grade submission status.'));
				} else {
					$this->Flash->info( __('Exam grade is already submitted to the department for approval. No need to submit that again.'));
				}

				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));

			} else if (isset($this->request->data['cancelExamGrade'])) {

				/*************************** Begin "Cancel Submit Grade" button  **********************************/

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				$grade_cancelation_status = $this->ExamResult->cancelSubmitedGrade($published_course_id, $student_course_register_and_adds);

				if (isset($grade_cancelation_status['error'])) {
					$this->Flash->error( __($grade_cancelation_status['error']));
				} else if (count($grade_cancelation_status['course_registration_add']) > 0 || count($grade_cancelation_status['makeup_exam']) > 0) {
					$this->Flash->success( __('Exam grade submission cancellation for ' . (count($grade_cancelation_status['course_registration_add']) + count($grade_cancelation_status['makeup_exam'])) . ' student' . ((count($grade_cancelation_status['course_registration_add']) + count($grade_cancelation_status['makeup_exam'])) > 1 ? 's' : '') . ' was successfull. Please edit exam result, preview and submit the exam grade before grade submission deadline.'));
				} else {
					$this->Flash->info(__('Exam grade is on process by the department and registrar. Unable to process your exam grade cancellation request.'));
				}

				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));

			} else if (isset($this->request->data['cancelExamGradePreview'])) {

				/************************* Begin "Cancel Exam grade Preview" button  ********************************/
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));

			} else if (isset($this->request->data['exportExamGrade'])) {
				
				$this->autoLayout = false;

				$publish_course_detail_info = $this->ExamResult->ExamType->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Course' => array('CourseCategory'),
						'Section' => array('YearLevel'),
						'Program',
						'ProgramType',
						'College',
						'Department' => array('College'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array(
								'Position' => array('id', 'position'),
								'Title' => array('id', 'title'),
							)
						)
					)
				));

				debug($publish_course_detail_info);

				$university = ClassRegistry::init('University')->getSectionUniversity($publish_course_detail_info['PublishedCourse']['section_id']);
				//debug($publish_course_detail_info);
				//$filename = "Mark_Sheet" . $section_detail['name'] . '-'. $publish_course_detail_info['PublishedCourse']['academic_year'] . '-' . $publish_course_detail_info['PublishedCourse']['semester'];
				$filename = "Mark_Sheet_" . $publish_course_detail_info['Course']['course_code'] . '_' . (str_replace(' ', '_', (trim(str_replace('  ', ' ', $section_detail['name']))))) . '_' . (str_replace('/', '-', $publish_course_detail_info['PublishedCourse']['academic_year'])) . '_' . $publish_course_detail_info['PublishedCourse']['semester']. '_' . date('Y-m-d'); //. ($publish_course_detail_info['PublishedCourse']['semester'] == 'I' ? '1st' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'II' ? '2nd' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'III' ? '3rd' : $publish_course_detail_info['PublishedCourse']['semester']))) . 'Semester';
				$this->set(compact('selected_acadamic_year', 'selected_semester', 'gradeStatistics', 'grade_scale', 'published_course_detail', 'gradeStatistics', 'exam_results', 'published_course_combo_id', 'publishedCourses', 'students', 'exam_types', 'student_adds', 'student_makeup', 'section_detail', 'course_detail', 'display_grade', 'filename', 'university', 'publish_course_detail_info', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'total_student_count'));
				$this->render('/Elements/marksheet_grade_xls');
			}
		}

		if (!empty($published_course_id) && is_numeric($published_course_id) && $published_course_id > 0) {
			
			$published_course_detail = $this->ExamResult->CourseAdd->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id)));
			
			$selected_acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
			$selected_semester =  $published_course_detail['PublishedCourse']['semester'];
		} else if (isset($this->request->data['ExamResult']['semester']) && !empty($this->request->data['ExamResult']['semester'])) {
			$selected_semester = $this->request->data['ExamResult']['semester'];
			$selected_acadamic_year = $this->request->data['ExamResult']['acadamic_year'];
		}

		if (strcasecmp($sourse, 'instructor') == 0) {
			$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection($selected_acadamic_year, $selected_semester, $instructor_id);
		} else if (!empty($this->request->data) || $published_course_id) {
			
			if (/* empty($this->request->data) && */ $published_course_id && is_numeric($published_course_id) && $published_course_id > 0) {
				
				$published_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1));

				$program_type_id = $published_course_detail['PublishedCourse']['program_type_id'];
				$program_id = $published_course_detail['PublishedCourse']['program_id'];
				$acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
				$semester = $published_course_detail['PublishedCourse']['semester'];
				//$published_course_combo_id = $published_course_id;
			} else if (isset($this->request->data['ExamResult'])) {
				$program_type_id = $this->request->data['ExamResult']['program_type_id'];
				$program_id = $this->request->data['ExamResult']['program_id'];
				$acadamic_year = $this->request->data['ExamResult']['acadamic_year'];
				$semester = $this->request->data['ExamResult']['semester'];
			}


			if (strcasecmp($sourse, 'college') == 0) {
				$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($this->college_id, $acadamic_year, $semester, $program_id, $program_type_id);
			} else {
				$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($this->department_id, $acadamic_year, $semester, $program_id, $program_type_id);
			}
		}


		if (isset($published_course_id) && is_numeric($published_course_id) && $published_course_id > 0) {
			$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id
				),
				'contain' => array('Section', 'Course')
			));
		} else {
			$section_and_course_detail = array();
		}

		if (!empty($section_and_course_detail['Section'])) {
			$section_detail = $section_and_course_detail['Section'];
		}

		if (!empty($section_and_course_detail['Course'])) {
			$course_detail = $section_and_course_detail['Course'];
		}

		if (!empty($publishedCourses)) {
			$publishedCourses = array("" => "[ Select Course ]") + $publishedCourses;
		}
		//array_unshift($publishedCourses, array("" => "---Select Course---"));

		$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);

		$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

		//$days_available_for_grade_change = ClassRegistry::init('AcademicCalendar')->daysAvaiableForGradeChange();

		$days_available_for_grade_change = ClassRegistry::init('GeneralSetting')->daysAvaiableForGradeChange($published_course_id);

		$lastGradeSubmissionDate = ClassRegistry::init('AcademicCalendar')->getPublishedCourseGradeSubmissionDate($published_course_id);
		
		if (isset($published_course_id) && !empty($published_course_id)) {
			$date_grade_submited = ClassRegistry::init('AcademicCalendar')->getLastGradeChangeDate($published_course_id);
		} else {
			$date_grade_submited = date('Y-m-d H:i:s');
		}

		$this->set(compact('selected_acadamic_year', 'selected_semester', 'gradeStatistics', 'grade_scale', 'lastGradeSubmissionDate', 'published_course_detail', 'course_assignment_detail', 'exam_results', 'published_course_combo_id', 'publishedCourses', 'students', 'exam_types', 'student_adds', 'student_makeup', 'section_detail', 'course_detail', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'total_student_count', 'date_grade_submited'));
		//debug($students);
	}
	
	private function __data_entry_assignment($published_course_id = null, $sourse = 'instructor')
	{
		if ($sourse == "instructor" && empty($published_course_id)) {

			$selectedAcadamicYear = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->find('first', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id in (select id from staffs where user_id="' . $this->Auth->user('id') . '")'
				), 
				'fields' => array('CourseInstructorAssignment.academic_year', 'CourseInstructorAssignment.academic_year'),
				'order' => array('CourseInstructorAssignment.academic_year' => 'DESC')
			));

			if (!empty($selectedAcadamicYear)) {
				$selected_acadamic_year = $selectedAcadamicYear['CourseInstructorAssignment']['academic_year'];
			}

		} else {
			$selected_acadamic_year = $this->AcademicYear->current_academicyear();
		}

		$selected_semester = 'I';
		$published_course_combo_id = "";
		$students = array();
		$exam_results = array();
		$entry_exam_results = array();
		$exam_types = array();
		$exam_result_delete_ids = array();
		$save_is_ok = true;
		$student_adds = array();
		$student_makeup = array();
		$display_grade = false;
		$do_not_manipulate = false;
		$view_only = false;
		$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

		if (!empty($published_course_id)) {

			$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id
				), 
				'contain' => array(
					'Section', 
					'Course', 
					'CourseInstructorAssignment' => array(
						'conditions' => array('CourseInstructorAssignment.isprimary' => true),
						'Staff' => array('Title', 'Position')
					)
				)
			));

			if (!empty($section_and_course_detail['Section'])) {
				$section_detail = $section_and_course_detail['Section'];
			}

			if (!empty($section_and_course_detail['Course'])) {
				$course_detail = $section_and_course_detail['Course'];
			}

			if (!empty($section_and_course_detail['CourseInstructorAssignment'])) {
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];
			}

			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);

			$student_makeup = $student_course_register_and_adds['makeup'];

			$total_student_count = count($student_makeup);
			$grade_submission_status = $this->ExamResult->getExamGradeEntrySubmissionStatus($published_course_id, $student_course_register_and_adds);
			$published_course_combo_id = $published_course_id;
		}

		//Checking if the user is eligible to manage published course exam result and grade
		if (!empty($this->request->data)) {
			$published_course_id = $this->request->data['ExamResult']['published_course_id'];
		}

		if (!empty($published_course_id)) {

			$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
			$published_course_department = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section' => array('Department'))));
			
			//Do you have the right to manage exam result and grade
			$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
			$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));

			if (!(($this->department_id == $published_course_department['Section']['department_id'] && $active_account == 0 && isset($this->request->data))
				//Permitted if there is no data is submitted or the submited data is grade change
				|| ($this->department_id == $published_course_department['Section']['department_id'] && (!isset($this->request->data) || isset($this->request->data['ExamGradeChange'])))
				|| ($this->college_id == $published_course_department['PublishedCourse']['college_id'] && $active_account == 0 && isset($this->request->data))
				|| ($this->college_id == $published_course_department['PublishedCourse']['college_id'] && !isset($this->request->data))
				|| ($instructor_id_for_checking == $instructor_id))) {
			}

			//Do they have view only access? (It is not neccessary as the user is trapped on the above checking)
			$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

			if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
				$view_only = true;
			}
			//End for do they have view only access?
		}

		if (!$do_not_manipulate && !empty($this->request->data)) {

			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
			$grade_submission_status = $this->ExamResult->getExamGradeEntrySubmissionStatus($published_course_id, $student_course_register_and_adds);

			/*******************************  Begin "Save Exam Result" button  ***********************************/

			if (isset($this->request->data['saveExamResult']) || isset($this->request->data['previewExamGrade'])) {

				//ResultEntryAssignment exam result data collection and formating

				if (isset($this->request->data['ResultEntryAssignment'])) {
					foreach ($this->request->data['ResultEntryAssignment'] as $key => $entry_exam) {
						$entry_exam_published_course_id = $this->ExamResult->CourseRegistration->ResultEntryAssignment->field('published_course_id', array('id' => $entry_exam['id']));
						if ($entry_exam_published_course_id != $published_course_id) {
							$this->Flash->warning( __('Sorry, the system is unable to get some students for result entry. This usually happens when the department apply a change while you make exam result submission. Please re-submit your exam result entry.'));
							$save_is_ok = false;
						} else if (trim($entry_exam['result']) != "" && (!is_numeric($entry_exam['result']) || $entry_exam['result'] > 100 || $entry_exam['result'] < 0)) {
							$this->Flash->error(__('The system unable to record exam result entry. Please make sure that all your exam result entry is valid.'));
							$save_is_ok = false;
						} else {
							$entry_exam_results[$key]['id'] = $entry_exam['id'];
							$entry_exam_results[$key]['result'] = (trim($entry_exam['result']) === "" ? null : $entry_exam['result']);
						}
					}
				}

				if ($save_is_ok) {
					//Result exam result entry
					if (!empty($entry_exam_results)) {

						$resultEntryAssignment = ClassRegistry::init('ResultEntryAssignment');
						$resultEntryAssignment->set($entry_exam_results);

						if (!ClassRegistry::init('ResultEntryAssignment')->saveAll($entry_exam_results, array('validate' => false))) {
							$this->Flash->error(__('Exam result entry/update is failed. Please re-enter the  exam result.'));
							return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_assigned_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
						}
					}

					if (!isset($this->request->data['previewExamGrade'])) {
						$this->Flash->success( __('Exam result has been saved'));
						return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_assigned_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
					}
				}

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);
			}  //End of "Save Exam Result" button


			/*************************** Begin "Preview Grade" button  **********************************/

			if (isset($this->request->data['previewExamGrade'])) {

				/* foreach($this->request->data['ExamResult'] as $key => $exam_result) {
					if (is_array($exam_result)) {
						unset($this->request->data['ExamResult'][$key]);
					}
				} */

				unset($this->request->data['InProgress']);

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
				$course_required_scale = $this->ExamResult->CourseRegistration->PublishedCourse->isPublishedCourseRequiredScale($published_course_id);

				//debug($exam_types);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);

				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

				if (!isset($grade_scale['error'])) {
					//$students = $this->ExamResult->generateGradeEntryCourseGrade($students, $grade_scale);
					//$student_adds = $this->ExamResult->generateGradeEntryCourseGrade($student_adds, $grade_scale);
	
					$student_makeup = $this->ExamResult->generateGradeEntryCourseGrade($student_makeup, $grade_scale);
					debug($student_makeup);
					$display_grade = true;
				} else {
					$this->Flash->error(__($grade_scale['error']));
					return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_assigned_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				}
				// End of "Preview Grade" button

			} else if (isset($this->request->data['submitExamGrade'])) {

				/*************************** Begin "Submit Grade" button  **********************************/

				$students_course_in_progress = array();

				if (isset($this->request->data['InProgress'])) {
					foreach ($this->request->data['InProgress'] as $key => $course_in_progress) {
						if ($course_in_progress['in_progress'] != 0) {
							$students_course_in_progress[] = $course_in_progress['in_progress'];
						}
					}
				}

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$grade_submit_status = $this->ExamResult->submitGradeEntryAssignment($student_course_register_and_adds, $students_course_in_progress, $grade_scale);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
				
				if (isset($grade_submit_status['error'])) {
					$this->Flash->error(__($grade_submit_status['error']));
				} else if (count($grade_submit_status['course_registration_add']) > 0 || count($grade_submit_status['makeup_exam']) > 0) {
					$this->Flash->success(__('Exam grade for ' . (count($grade_submit_status['course_registration_add']) + count($grade_submit_status['makeup_exam'])) . ' student' . ((count($grade_submit_status['course_registration_add']) + count($grade_submit_status['makeup_exam'])) > 1 ? 's' : '') . ' is successfully submitted to the department for approval. You can cancel your grade submission to apply changes, if needed, before it is approved by the department. Once the grades are approved, neither you can cancel the submitted grade nor edit exam result except for students with "In Progress" grade submission status.'));
				} else {
					$this->Flash->warning(__('Exam grade is already successfully submitted to the department for approval.'));
				}

				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_assigned_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				// End of "Submit Grade" button
			} else if (isset($this->request->data['cancelExamGrade'])) {

				/*************************** Begin "Cancel Submit Grade" button  **********************************/

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
				$grade_cancelation_status = $this->ExamResult->cancelSubmitedGradeEntry($published_course_id, $student_course_register_and_adds);

				if (isset($grade_cancelation_status['error'])) {
					$this->Flash->error( __($grade_cancelation_status['error']));
				} else if (count($grade_cancelation_status['course_registration_add']) > 0 || count($grade_cancelation_status['makeup_exam']) > 0) {
					$this->Flash->success(__('Exam grade submission cancellation for ' . (count($grade_cancelation_status['course_registration_add']) + count($grade_cancelation_status['makeup_exam'])) . ' student' . ((count($grade_cancelation_status['course_registration_add']) + count($grade_cancelation_status['makeup_exam'])) > 1 ? 's' : '') . ' is successfully done. Please edit exam result, preview and submit the exam grade within the given grade submission period.'));
				} else {
					$this->Flash->info(__('Exam grade is on process by the department and registrar to proceed with your exam grade cancellation request.'));
				}

				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_assigned_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				// End of "Submit Grade"
			} else if (isset($this->request->data['cancelExamGradePreview'])) {
				/************************* Begin "Cancel Exam grade Preview" button  ********************************/
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_assigned_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				// End of "Cancel Preview"
			} else if (isset($this->request->data['exportExamGrade'])) {
				
				$this->autoLayout = false;
				
				$makeup_exam = true;
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);

				$publish_course_detail_info = $this->ExamResult->ExamType->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Course' => array('CourseCategory'),
						'Section' => array('YearLevel'),
						'Program',
						'ProgramType',
						'College',
						'Department' => array('College'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array(
								'Position' => array('id', 'position'),
								'Title' => array('id', 'title'),
							)
						)
					)
				));

				$university = ClassRegistry::init('University')->getSectionUniversity($publish_course_detail_info['PublishedCourse']['section_id']);
				//$filename = "Grade-Entry-Sheet" . $section_detail['name'] . '-' . $publish_course_detail_info['PublishedCourse']['academic_year'] . '-' . $publish_course_detail_info['PublishedCourse']['semester'];
				$filename = "Grade_Entry_Sheet_Section_" . $section_detail['name'] . '_for_' . ($publish_course_detail_info['PublishedCourse']['semester'] == 'I' ? '1st' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'II' ? '2nd' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'III' ? '3rd' : $publish_course_detail_info['PublishedCourse']['semester']))) . '_Semester_of_' . (str_replace('/', '-', $publish_course_detail_info['PublishedCourse']['academic_year'])) . '_Academic_Year';

				$this->set(compact(
					'selected_acadamic_year',
					'selected_semester',
					'gradeStatistics',
					'grade_scale',
					'published_course_detail',
					'makeup_exam',
					'gradeStatistics',
					'exam_results',
					'published_course_combo_id',
					'publishedCourses',
					'students',
					'exam_types',
					'student_adds',
					'student_makeup',
					'section_detail',
					'course_detail',
					'display_grade',
					'filename',
					'university',
					'publish_course_detail_info',
					'grade_submission_status',
					'view_only',
					'days_available_for_grade_change',
					'total_student_count'
				));
				$this->render('/Elements/marksheet_grade_xls');
			}
		} //End of if (!empty($this->request->data))

		if (!empty($published_course_id)) {
			$published_course_detail = $this->ExamResult->CourseAdd->PublishedCourse->find('first', array('conditions' =>array('PublishedCourse.id' => $published_course_id)));
			$selected_acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
			$selected_semester =  $published_course_detail['PublishedCourse']['semester'];
		} else if (isset($this->request->data['ExamResult']['semester']) && !empty($this->request->data['ExamResult']['semester'])) {
			$selected_semester = $this->request->data['ExamResult']['semester'];
			$selected_acadamic_year = $this->request->data['ExamResult']['acadamic_year'];
		}

		if (strcasecmp($sourse, 'instructor') == 0) {
			$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfAssignedGradeEntryAssignedBySection($selected_acadamic_year, $selected_semester, $instructor_id);
		} else if (!empty($this->request->data) || $published_course_id) {
			if (empty($this->request->data)) {
				$published_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1));
				$program_type_id = $published_course_detail['PublishedCourse']['program_type_id'];
				$program_id = $published_course_detail['PublishedCourse']['program_id'];
				$acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
				$semester = $published_course_detail['PublishedCourse']['semester'];
				//$published_course_combo_id = $published_course_id;
			} else {
				$program_type_id = $this->request->data['ExamResult']['program_type_id'];
				$program_id = $this->request->data['ExamResult']['program_id'];
				$acadamic_year = $this->request->data['ExamResult']['acadamic_year'];
				$semester = $this->request->data['ExamResult']['semester'];
			}

			if (strcasecmp($sourse, 'college') == 0) {
				$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfAssignedGradeEntryAssignedBySection($this->college_id, $acadamic_year, $semester, $program_id, $program_type_id);
			} else {
				$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfAssignedGradeEntryAssignedBySection($this->department_id, $acadamic_year, $semester, $program_id, $program_type_id);
			}
		}

		$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section', 'Course')));
		
		if (!empty($section_and_course_detail['Section'])) {
			$section_detail = $section_and_course_detail['Section'];
		}

		if (!empty($section_and_course_detail['Course'])) {
			$course_detail = $section_and_course_detail['Course'];
		}

		if (!empty($publishedCourses)) {
			$publishedCourses = array("" => "[ Select Course ]") + $publishedCourses;
		} else {
			$publishedCourses = array("" => "[ No Assigned Courses Found, Try Changing Filters ]");
		}

		$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
		$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

		$this->set(compact('selected_acadamic_year', 'selected_semester', 'gradeStatistics', 'grade_scale', 'lastGradeSubmissionDate', 'published_course_detail', 'course_assignment_detail', 'exam_results', 'published_course_combo_id', 'publishedCourses', 'students', 'exam_types', 'student_adds', 'student_makeup', 'section_detail', 'course_detail', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'total_student_count'));
	}

	private function __exam_result_and_grade_mgt_fx($published_course_id = null, $sourse = 'instructor')
	{
		if ($sourse == "instructor" && empty($published_course_id)) {
			$selectedAcadamicYear = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->find('first', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id in (select id from staffs where user_id="' . $this->Auth->user('id') . '")'
				), 
				'fields' => array('CourseInstructorAssignment.academic_year', 'CourseInstructorAssignment.academic_year'),
				'order' => array('CourseInstructorAssignment.academic_year DESC')
			));

			if (!empty($selectedAcadamicYear)) {
				$selected_acadamic_year = $selectedAcadamicYear['CourseInstructorAssignment']['academic_year'];
			}

		} else {
			$selected_acadamic_year = $this->AcademicYear->current_academicyear();
		}

		$selected_semester = 'I';
		$published_course_combo_id = "";
		$students = array();
		$exam_results = array();
		$makeup_exam_results = array();
		$exam_types = array();
		$exam_result_delete_ids = array();
		$save_is_ok = true;
		$student_adds = array();
		$student_makeup = array();
		$display_grade = false;
		$do_not_manipulate = false;
		$view_only = false;
		$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
		if (!empty($published_course_id)) {
			$exam_types = $this->ExamResult->ExamType->find('all', array(
				'fields' => array('id', 'exam_name', 'percent', 'order'), 'conditions' => array('ExamType.published_course_id' => $published_course_id),
				'contain' => array(),
				'order' => array('order ASC'),
				'recursive' => -1
			));
			$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array(
				'PublishedCourse.id' => $published_course_id
			), 'contain' => array('Section', 'Course', 'CourseInstructorAssignment' => array(
				'conditions' => array('CourseInstructorAssignment.isprimary' => true),
				'Staff' => array('Title', 'Position')
			))));
			if (!empty($section_and_course_detail['Section'])) {
				$section_detail = $section_and_course_detail['Section'];
			}
			if (!empty($section_and_course_detail['Course'])) {
				$course_detail = $section_and_course_detail['Course'];
			}

			if (!empty($section_and_course_detail['CourseInstructorAssignment'])) {
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];
			}
			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
			//$students = $student_course_register_and_adds['register'];
			$student_makeup = $student_course_register_and_adds['makeup'];

			$total_student_count = count($student_makeup);
			$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);
			$published_course_combo_id = $published_course_id;
		}
		//Checking if the user is eligible to manage published course exam result and grade
		if (!empty($this->request->data))
			$published_course_id = $this->request->data['ExamResult']['published_course_id'];

		if (!empty($published_course_id)) {
			$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
			$published_course_department = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section' => array('Department'))));
			//Do you have the right to manage exam result and grade
			$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
			$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));

			if (!(($this->department_id == $published_course_department['Section']['department_id'] && $active_account == 0 && isset($this->request->data))
				//Permitted if there is no data is submitted or the submited data is grade change
				|| ($this->department_id == $published_course_department['Section']['department_id'] && (!isset($this->request->data) || isset($this->request->data['ExamGradeChange'])))
				|| ($this->college_id == $published_course_department['PublishedCourse']['college_id'] && $active_account == 0 && isset($this->request->data))
				|| ($this->college_id == $published_course_department['PublishedCourse']['college_id'] && !isset($this->request->data))
				|| ($instructor_id_for_checking == $instructor_id))) {
			}

			//Do they have view only access? (It is not neccessary as the user is trapped on the above checking)
			$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

			if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id)
				$view_only = true;
			//End for do they have view only access?
		}

		if (
			!$do_not_manipulate
			&& !empty($this->request->data)
		) {
			$exam_types = $this->ExamResult->ExamType->find('all', array(
				'fields' => array('id', 'exam_name', 'percent', 'order', 'mandatory'), 'conditions' => array('ExamType.published_course_id' => $published_course_id),
				'contain' => array(), 'order' => array('order ASC'), 'recursive' => -1
			));
			$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
			$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);
			/*******************************  Begin "Save Exam Result" button  ***********************************/

			if (
				isset($this->request->data['saveExamResult']) ||
				isset($this->request->data['previewExamGrade'])
			) {
				$exam_inconsistentcy_found = false;
				foreach ($this->request->data['ExamResult'] as $key => $exam_result) {
					$fraud = false;
					if (is_array($exam_result)) {
						//debug($exam_result);
						//SECURITY PASS
						//Check if complete data is sent
						if (
							!isset($exam_result['result']) ||
							(!isset($exam_result['id']) && !isset($exam_result['exam_type_id'])) ||
							(isset($exam_result['exam_type_id']) && (!isset($exam_result['exam_type_id']) || (!isset($exam_result['course_registration_id']) && !isset($exam_result['course_add']))))
						) {
							//Some exam result data is missed when it is sent from the client machine
							$fraud = true;
						} else {
							//Exam result editing fraud checking
							if (isset($exam_result['id'])) {
								//Check if the result is for the published course
								$exam_result_detail = $this->ExamResult->find(
									'first',
									array(
										'conditions' => array('ExamResult.id' => $exam_result['id']),
										'contain' => array('CourseRegistration', 'CourseAdd')
									)
								);
								if (($exam_result_detail['ExamResult']['course_add'] == 1 && $exam_result_detail['CourseAdd']['published_course_id'] != $published_course_id) ||
									($exam_result_detail['ExamResult']['course_add'] == 0 && $exam_result_detail['CourseRegistration']['published_course_id'] != $published_course_id)
								) {
									//Exam result is some other published course result
									$fraud = true;
								}
								//Check if grade is already submitted
								if ($exam_result_detail['ExamResult']['course_add'] == 0) {
									$course_grade_submited = $this->ExamResult->CourseRegistration->ExamGrade->find(
										'first',
										array(
											'conditions' =>
											array(
												'ExamGrade.course_registration_id' => $exam_result_detail['ExamResult']['course_registration_id'],
												//'ExamGrade.department_approval <> -1'
											),
											'order' => array('ExamGrade.created DESC')
										)
									);
									$other_exam_result_details = $this->ExamResult->find(
										'all',
										array(
											'conditions' =>
											array(
												'ExamResult.exam_type_id' => $exam_result_detail['ExamResult']['exam_type_id'],
												'ExamResult.course_registration_id' => $exam_result_detail['ExamResult']['course_registration_id'],
												'ExamResult.course_add' => 0,
												'ExamResult.id <> ' => $exam_result_detail['ExamResult']['id']
											),
											'recursive' => -1
										)
									);
								} else {
									$course_grade_submited = $this->ExamResult->CourseAdd->ExamGrade->find(
										'first',
										array(
											'conditions' =>
											array(
												'ExamGrade.course_add_id' => $exam_result_detail['ExamResult']['course_registration_id'],
												//'ExamGrade.department_approval <> -1'
											),
											'order' => array('ExamGrade.created DESC')
										)
									);
									$other_exam_result_details = $this->ExamResult->find(
										'all',
										array(
											'conditions' =>
											array(
												'ExamResult.exam_type_id' => $exam_result_detail['ExamResult']['exam_type_id'],
												'ExamResult.course_registration_id' => $exam_result_detail['ExamResult']['course_registration_id'],
												'ExamResult.course_add' => 1,
												'ExamResult.id <> ' => $exam_result_detail['ExamResult']['id']
											),
											'recursive' => -1
										)
									);
								}
								if (isset($course_grade_submited['ExamGrade']) && $course_grade_submited['ExamGrade']['department_approval'] != -1) {
									//Grade is already submited for the to be edited exam result
									$fraud = true;
								}

								if (empty($exam_result_detail)) {
									$fraud = true;
								}
								//If there is other exam result entry by the same exam type and course registration or course add
								else if (!empty($other_exam_result_details)) {
									foreach ($other_exam_result_details as $other_exam_result_detail) {
										$this->ExamResult->delete($other_exam_result_detail['ExamResult']['id']);
									}
									$exam_inconsistentcy_found = true;
								}
							}
							//New result entry validation
							else {
								if ($exam_result['course_add'] == 0) {
									$exam_result_published_course_id = $this->ExamResult->CourseRegistration->field('published_course_id', array('CourseRegistration.id' => $exam_result['course_registration_id']));
								} else {
									$exam_result_published_course_id = $this->ExamResult->CourseAdd->field('published_course_id', array('CourseAdd.id' => $exam_result['course_registration_id']));
								}
								if ($published_course_id != $exam_result_published_course_id) {
									//Exam result is some other published course result
									$fraud = true;
								}
								//Check if grade is already submitted
								if ($exam_result['course_add'] == 0) {
									$course_grade_submited = $this->ExamResult->CourseRegistration->ExamGrade->find(
										'count',
										array(
											'conditions' =>
											array(
												'ExamGrade.course_registration_id' => $exam_result['course_registration_id'],
												'ExamGrade.department_approval <> -1',
												'ExamGrade.registrar_approval <> -1'
											)
										)
									);
								} else {
									$course_grade_submited = $this->ExamResult->CourseAdd->ExamGrade->find(
										'count',
										array(
											'conditions' =>
											array(
												'ExamGrade.course_add_id' => $exam_result['course_registration_id'],
												'ExamGrade.department_approval <> -1',
												'ExamGrade.registrar_approval <> -1'
											)
										)
									);
								}
								if ($course_grade_submited > 0) {
									//Grade is already submited for the to be entered exam result
									$fraud = true;
								}
								/*On save make sure that there is no result recorded with the given exam type and registration or add. If there is any, delete all records.*/
								$other_exam_result_details = $this->ExamResult->find(
									'all',
									array(
										'conditions' =>
										array(
											'ExamResult.exam_type_id' => $exam_result['exam_type_id'],
											'ExamResult.course_registration_id' => $exam_result['course_registration_id'],
											'ExamResult.course_add' => $exam_result['course_add'],
										),
										'recursive' => -1
									)
								);
								//If there are already recorded results, it has to be deleted for the new exam result entry
								if (!empty($other_exam_result_details)) {
									foreach ($other_exam_result_details as $other_exam_result_detail) {
										$this->ExamResult->delete($other_exam_result_detail['ExamResult']['id']);
									}
								}
							} //END: New result entry validation
						}

						if ($fraud) {

							$this->Session->setFlash('<span></span>' . __('The system encounter a problem while processing your exam result submission. Please try your submission again.'), 'default', array('class' => 'error-message error-box'));
							//$save_is_ok = false;
							return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
						}

						if (trim($exam_result['result']) != "") {
							$exam_results[$key] = $exam_result;
							$exam_percent = "";
							$exam_type_id = "";
							$exam_name = "";
							if (isset($exam_result['id'])) {
								$exam_type_id = $this->ExamResult->field('exam_type_id', array('id' => $exam_result['id']));
							} else
								$exam_type_id = $exam_result['exam_type_id'];
							//Check if exam type is available
							foreach ($exam_types as $key => $exam_type) {
								if ($exam_type['ExamType']['id'] == $exam_type_id) {
									$exam_percent = $exam_type['ExamType']['percent'];
									$exam_name = $exam_type['ExamType']['exam_name'];
									break;
								}
							}
							if ($exam_percent == "" || $exam_name == "" || $exam_type_id == "") {
								$this->Session->setFlash('<span></span>' . __('Exam setup is not found for some result and I think you apply changes on the exam setup. Please provide the exam result again.'), 'default', array('class' => 'error-message error-box'));
								//$this->redirect(array('action' => 'add', $published_course_id));
								$save_is_ok = false;
							} else if (is_numeric($exam_result['result']) && $exam_result['result'] > $exam_percent) {
								$this->Session->setFlash('<span></span>' . __('Exam result for ' . $exam_name . ' exam can only be a maximum of ' . $exam_percent . '. To change the percentage, please go to the exam setup section.'), 'default', array('class' => 'error-message error-box'));
								//$this->redirect(array('action' => 'add', $published_course_id));
								$save_is_ok = false;
							}
						} else if (isset($exam_result['id'])) {
							$exam_result_delete_ids[] = $exam_result['id'];
						}
					}
				}
				if ($exam_inconsistentcy_found) {
					$this->Session->setFlash('<span></span>' . __('The system encounter a problem while processing your exam result submission. Please make sure that the following exam result is consistent with what you are trying to enter and try your submission again.'), 'default', array('class' => 'error-message error-box'));
					//$save_is_ok = false;
					return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				}
				//Makeup exam result data collection and formating
				//debug($this->request->data);
				if (isset($this->request->data['MakeupExam'])) {
					foreach ($this->request->data['MakeupExam'] as $key => $makeup_exam) {
						$makeup_exam_published_course_id = $this->ExamResult->CourseRegistration->MakeupExam->field('published_course_id', array('id' => $makeup_exam['id']));
						if ($makeup_exam_published_course_id != $published_course_id) {
							$this->Session->setFlash('<span></span>' . __('Sorry, the system is unable to get some students for makeup exam result entry. This usually happens when the department apply a change while you make exam result submission. Please re-submit your exam result entry.'), 'default', array('class' => 'error-message error-box'));
							$save_is_ok = false;
						} else if (trim($makeup_exam['result']) != "" && (!is_numeric($makeup_exam['result']) || $makeup_exam['result'] > 100 || $makeup_exam['result'] < 0)) {
							//$this->ExamResult->saveALL($exam_results, array('validate' => 'only'));
							$this->Session->setFlash('<span></span>' . __('The system unable to record exam result entry. Please make sure that all your exam result entry is valid.'), 'default', array('class' => 'error-message error-box'));

							$save_is_ok = false;
						} else {
							$makeup_exam_results[$key]['id'] = $makeup_exam['id'];
							$makeup_exam_results[$key]['result'] = (trim($makeup_exam['result']) === "" ? null : $makeup_exam['result']);
						}
					}
				}

				$this->ExamResult->create();
				$this->set($exam_results);
				if ($save_is_ok) {
					if ($this->ExamResult->saveALL($exam_results)) {
						if (!empty($exam_result_delete_ids)) {
							if (!$this->ExamResult->deleteAll(array('ExamResult.id' => $exam_result_delete_ids), false)) {
								$this->Session->setFlash('<span></span>' . __('Exam result entry/update is saved but deletion for exam result is interrupted. Please check your exam result entry and/or update for consistency.'), 'default', array('class' => 'error-message error-box'));
								return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
							}
						}
						//Makeup exam result entry
						if (!empty($makeup_exam_results)) {
							$makeupExam = ClassRegistry::init('MakeupExam');
							$makeupExam->set($makeup_exam_results);
							if (!ClassRegistry::init('MakeupExam')->saveALL($makeup_exam_results, array('validate' => false))) {
								$this->Session->setFlash('<span></span>' . __('Exam result entry/update for makeup exam is failed. Please re-enter the makeup exam result.'), 'default', array('class' => 'error-message error-box'));
								return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
							}
						}

						if (!isset($this->request->data['previewExamGrade'])) {
							$this->Session->setFlash('<span></span>' . __('Exam result has been saved'), 'default', array('class' => 'success-message success-box'));
							return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
						}
					} else {
						$this->Session->setFlash('<span></span>' . __('The exam result could not be saved. Please, try again.'), 'default', array('class' => 'error-message error-box'));
					}
				}
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);
			} //End of "Save Exam Result" button
			/*************************** Begin "Preview Grade" button  **********************************/
			if (isset($this->request->data['previewExamGrade'])) {
				foreach ($this->request->data['ExamResult'] as $key => $exam_result) {
					if (is_array($exam_result))
						unset($this->request->data['ExamResult'][$key]);
				}
				unset($this->request->data['InProgress']);

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
				$course_required_scale = $this->ExamResult->CourseRegistration->PublishedCourse->isPublishedCourseRequiredScale($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);
				//debug($exam_types);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);

				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

				if (!isset($grade_scale['error'])) {
					$students = $this->ExamResult->generateCourseGrade($students, $grade_scale, $exam_types);
					$student_adds = $this->ExamResult->generateCourseGrade($student_adds, $grade_scale, $exam_types);
					$student_makeup = $this->ExamResult->generateCourseGrade($student_makeup, $grade_scale, $exam_types);
					$display_grade = true;
				} else {
					$this->Session->setFlash('<span></span>' . __($grade_scale['error']), 'default', array('class' => 'error-message error-box'));
					return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
				}
			} // End of "Preview Grade" button
			/*************************** Begin "Submit Grade" button  **********************************/
			else if (isset($this->request->data['submitExamGrade'])) {
				$students_course_in_progress = array();
				if (isset($this->request->data['InProgress'])) {
					foreach ($this->request->data['InProgress'] as $key => $course_in_progress) {
						if ($course_in_progress['in_progress'] != 0)
							$students_course_in_progress[] = $course_in_progress['in_progress'];
					}
				}
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$grade_submit_status = $this->ExamResult->submitGrade(
					$student_course_register_and_adds,
					$students_course_in_progress,
					$grade_scale,
					$exam_types
				);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
				if (isset($grade_submit_status['error'])) {
					$this->Session->setFlash('<span></span>' . __($grade_submit_status['error']), 'default', array('class' => 'success-message success-box'));
				} else if (count($grade_submit_status['course_registration_add']) > 0 || count($grade_submit_status['makeup_exam']) > 0) {
					$this->Session->setFlash('<span></span>' . __('Exam grade for ' . (count($grade_submit_status['course_registration_add']) + count($grade_submit_status['makeup_exam'])) . ' student' . ((count($grade_submit_status['course_registration_add']) + count($grade_submit_status['makeup_exam'])) > 1 ? 's' : '') . ' is successfully submitted to the department for approval. You can cancel your grade submission to apply changes, if needed, before it is approved by the department. Once the grades are approved, neither you can cancel the submitted grade nor edit exam result except for students with "In Progress" grade submission status.'), 'default', array('class' => 'success-message success-box'));
				} else {
					$this->Session->setFlash('<span></span>' . __('Exam grade is already successfully submitted to the department for approval.'), 'default', array('class' => 'info-message info-box'));
				}
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
			} // End of "Submit Grade" button
			/*************************** Begin "Cancel Submit Grade" button  **********************************/
			else if (isset($this->request->data['cancelExamGrade'])) {
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
				$grade_cancelation_status = $this->ExamResult->cancelSubmitedGrade($published_course_id, $student_course_register_and_adds);

				if (isset($grade_cancelation_status['error'])) {
					$this->Session->setFlash('<span></span>' . __($grade_cancelation_status['error']), 'default', array('class' => 'success-message success-box'));
				} else if (count($grade_cancelation_status['course_registration_add']) > 0 || count($grade_cancelation_status['makeup_exam']) > 0) {
					$this->Session->setFlash('<span></span>' . __('Exam grade submission cancellation for ' . (count($grade_cancelation_status['course_registration_add']) + count($grade_cancelation_status['makeup_exam'])) . ' student' . ((count($grade_cancelation_status['course_registration_add']) + count($grade_cancelation_status['makeup_exam'])) > 1 ? 's' : '') . ' is successfully done. Please edit exam result, preview and submit the exam grade within the given grade submission period.'), 'default', array('class' => 'success-message success-box'));
				} else {
					$this->Session->setFlash('<span></span>' . __('Exam grade is on process by the department and registrar to proceed with your exam grade cancellation request.'), 'default', array('class' => 'info-message info-box'));
				}
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
			} // End of "Submit Grade"
			/************************* Begin "Cancel Exam grade Preview" button  ********************************/
			else if (isset($this->request->data['cancelExamGradePreview'])) {
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'submit_fx_grade' : (strcasecmp($sourse, 'college') == 0 ? 'submit_freshman_grade_for_instructor' : 'submit_grade_for_instructor')), $published_course_id));
			} // End of "Cancel Preview"
			else if (isset($this->request->data['exportExamGrade'])) {
				$this->autoLayout = false;
				$makeup_exam = true;
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['makeup'];
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);

				$publish_course_detail_info = $this->ExamResult->ExamType->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Course' => array('CourseCategory'),
						'Section' => array('YearLevel'),
						'Program',
						'ProgramType',
						'College',
						'Department' => array('College'),
						'CourseInstructorAssignment' => array(
							'conditions' => array(
								'CourseInstructorAssignment.isprimary' => 1
							),
							'Staff' => array(
								'Position' => array('id', 'position'),
								'Title' => array('id', 'title'),
							)
						)
					)
				));

				$university = ClassRegistry::init('University')->getSectionUniversity($publish_course_detail_info['PublishedCourse']['section_id']);
				//$filename = "Makeup-Fx-Grade-Sheet" . $section_detail['name'] . '-' . $publish_course_detail_info['PublishedCourse']['academic_year'] . '-' . $publish_course_detail_info['PublishedCourse']['semester'];
				$filename = "Makeup_Fx_Grade_Sheet_Section_" . $section_detail['name'] . '_for_' . ($publish_course_detail_info['PublishedCourse']['semester'] == 'I' ? '1st' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'II' ? '2nd' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'III' ? '3rd' : $publish_course_detail_info['PublishedCourse']['semester']))) . '_Semester_of_' . (str_replace('/', '-', $publish_course_detail_info['PublishedCourse']['academic_year'])) . '_Academic_Year';

				$this->set(compact(
					'selected_acadamic_year',
					'selected_semester',
					'gradeStatistics',
					'grade_scale',
					'published_course_detail',
					'makeup_exam',
					'gradeStatistics',
					'exam_results',
					'published_course_combo_id',
					'publishedCourses',
					'students',
					'exam_types',
					'student_adds',
					'student_makeup',
					'section_detail',
					'course_detail',
					'display_grade',
					'filename',
					'university',
					'publish_course_detail_info',
					'grade_submission_status',
					'view_only',
					'days_available_for_grade_change',
					'total_student_count'
				));
				$this->render('/Elements/marksheet_grade_xls');
			}
		} //End of if (!empty($this->request->data))

		if (!empty($published_course_id)) {
			$published_course_detail = $this->ExamResult->CourseAdd->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id)));
			$selected_acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
			$selected_semester =  $published_course_detail['PublishedCourse']['semester'];
		} else if (isset($this->request->data['ExamResult']['semester']) && !empty($this->request->data['ExamResult']['semester'])) {
			$selected_semester = $this->request->data['ExamResult']['semester'];
			$selected_acadamic_year = $this->request->data['ExamResult']['acadamic_year'];
		}
		if (strcasecmp($sourse, 'instructor') == 0) {
			$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfFxCoursesInstructorAssignedBySection($selected_acadamic_year, $selected_semester, $instructor_id);
		} else if (!empty($this->request->data) || $published_course_id) {
			if (empty($this->request->data)) {
				$published_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1));
				$program_type_id = $published_course_detail['PublishedCourse']['program_type_id'];
				$program_id = $published_course_detail['PublishedCourse']['program_id'];
				$acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
				$semester = $published_course_detail['PublishedCourse']['semester'];
				//$published_course_combo_id = $published_course_id;
			} else {
				$program_type_id = $this->request->data['ExamResult']['program_type_id'];
				$program_id = $this->request->data['ExamResult']['program_id'];
				$acadamic_year = $this->request->data['ExamResult']['acadamic_year'];
				$semester = $this->request->data['ExamResult']['semester'];
			}

			if (strcasecmp($sourse, 'college') == 0) {
				$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfFxCoursesInstructorAssignedBySection($this->college_id, $acadamic_year, $semester, $program_id, $program_type_id);
			} else {
				$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfFxCoursesInstructorAssignedBySection($this->department_id, $acadamic_year, $semester, $program_id, $program_type_id);
			}
		}

		$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section', 'Course')));
		if (!empty($section_and_course_detail['Section'])) {
			$section_detail = $section_and_course_detail['Section'];
		}

		if (!empty($section_and_course_detail['Course'])) {
			$course_detail = $section_and_course_detail['Course'];
		}

		if (!empty($publishedCourses)) {
			$publishedCourses = array("" => "---Select Course---") + $publishedCourses;
		}

		$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
		$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

		//$days_available_for_grade_change = ClassRegistry::init('AcademicCalendar')->daysAvaiableForGradeChange();

		$days_available_for_grade_change = ClassRegistry::init('GeneralSetting')->daysAvaiableForGradeChange($published_course_id);

		$lastGradeSubmissionDate = ClassRegistry::init('AcademicCalendar')->getFxPublishedCourseGradeSubmissionDate($published_course_id);

		$this->set(compact('selected_acadamic_year', 'selected_semester', 'gradeStatistics', 'grade_scale', 'lastGradeSubmissionDate', 'published_course_detail', 'course_assignment_detail', 'exam_results', 'published_course_combo_id', 'publishedCourses', 'students', 'exam_types', 'student_adds', 'student_makeup', 'section_detail', 'course_detail', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'total_student_count'));
	}

	function get_exam_result_entry_form($published_course_id = null)
	{
		$this->layout = 'ajax';
		if ($this->Auth->user('id')) {

			$edit = 0;
			$exam_types = array();
			$exam_results = array();
			$students = array();
			$student_adds = array();
			$student_makeup = array();
			$grade_scale = array();
			$display_grade = false;
			$view_only = false;

			if (!empty($published_course_id)) {
				$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('CourseInstructorAssignment.staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
				//Not to block department head his/her own assigned courses
				$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

				$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
				$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));
				//Role based checking is removed
				//if(($this->role_id == 6 || $this->role_id == 5) && $active_account == 1 && $instructor_id_for_checking != $login_instructor_id)

				if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
					$view_only = true;
				}

				$exam_types = $this->ExamResult->ExamType->find('all', array(
					'fields' => array('id', 'exam_name', 'percent', 'order'),
					'conditions' => array('ExamType.published_course_id' => $published_course_id),
					'contain' => array(),
					'order' => array('order ASC'),
					'recursive' => -1
				));

				debug($published_course_id);

				$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Section', 
						'Course',
						'CourseInstructorAssignment' => array(
							'conditions' => array('CourseInstructorAssignment.isprimary' => true),
							'Staff' => array('Title', 'Position')
						)
					)
				));

				$section_detail = $section_and_course_detail['Section'];
				$course_detail = $section_and_course_detail['Course'];
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);

				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];

				$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);

				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
			}
			//$days_available_for_grade_change = ClassRegistry::init('AcademicCalendar')->daysAvaiableForGradeChange();

			$days_available_for_grade_change = ClassRegistry::init('GeneralSetting')->daysAvaiableForGradeChange($published_course_id);
			debug($days_available_for_grade_change);

			$lastGradeSubmissionDate = ClassRegistry::init('AcademicCalendar')->getPublishedCourseGradeSubmissionDate($published_course_id);
			debug($lastGradeSubmissionDate);

			if (isset($published_course_id) && !empty($published_course_id)) {
				$date_grade_submited = ClassRegistry::init('AcademicCalendar')->getLastGradeChangeDate($published_course_id);
				$date_grade_submited = $lastGradeSubmissionDate;
			} else if (isset($lastGradeSubmissionDate) && !empty($lastGradeSubmissionDate)) {
				$data_grade_submited = $lastGradeSubmissionDate;
			} else {
				$date_grade_submited = date('Y-m-d H:i:s');
			}

			$this->set(compact('grade_scale', 'published_course_id', 'students', 'exam_types', 'exam_results', 'section_detail', 'course_detail', 'course_assignment_detail', 'gradeStatistics', 'student_adds', 'student_makeup', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'lastGradeSubmissionDate', 'date_grade_submited'));
		} //End of login user cheking
	}

	function get_exam_result_fx_entry_form($published_course_id = null)
	{
		$this->layout = 'ajax';
		if ($this->Auth->user('id')) {
			$edit = 0;
			$exam_types = array();
			$exam_results = array();
			$students = array();
			$student_adds = array();
			$student_makeup = array();
			$grade_scale = array();
			$display_grade = false;
			$view_only = false;
			if (!empty($published_course_id)) {
				$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('CourseInstructorAssignment.staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
				//Not to block department head his/her own assigned courses
				$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

				$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
				$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));
				if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id)
					$view_only = true;
				$exam_types = $this->ExamResult->ExamType->find('all', array(
					'fields' => array('id', 'exam_name', 'percent', 'order'),
					'conditions' => array('ExamType.published_course_id' => $published_course_id),
					'contain' => array(),
					'order' => array('order ASC'),
					'recursive' => -1
				));

				$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array(
					'PublishedCourse.id' => $published_course_id
				), 'contain' => array('Section', 'Course', 'CourseInstructorAssignment' => array(
					'conditions' => array('CourseInstructorAssignment.isprimary' => true),
					'Staff' => array('Title', 'Position')
				))));
				$section_detail = $section_and_course_detail['Section'];
				$course_detail = $section_and_course_detail['Course'];
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
			}
			//$days_available_for_grade_change = ClassRegistry::init('AcademicCalendar')->daysAvaiableForGradeChange();
			$days_available_for_grade_change = ClassRegistry::init('GeneralSetting')->daysAvaiableForGradeChange($published_course_id);
			$lastGradeSubmissionDate = ClassRegistry::init('AcademicCalendar')->getFxPublishedCourseGradeSubmissionDate($published_course_id);
			debug($lastGradeSubmissionDate);
			debug($published_course_id);

			$this->set(compact('grade_scale', 'published_course_id', 'students', 'exam_types', 'exam_results', 'section_detail', 'course_detail', 'course_assignment_detail', 'gradeStatistics', 'student_adds', 'student_makeup', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'lastGradeSubmissionDate'));
		} //End of login user cheking
	}

	function get_registrar_assigned_grade_entry_form($published_course_id = null)
	{
		$this->layout = 'ajax';

		if ($this->Auth->user('id')) {
			$edit = 0;
			$exam_types = array();
			$exam_results = array();
			$students = array();
			$student_adds = array();
			$student_makeup = array();
			$grade_scale = array();
			$display_grade = false;
			$view_only = false;
			$student_course_register_and_adds = array();

			if (!empty($published_course_id)) {

				$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('CourseInstructorAssignment.staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
				//Not to block department head his/her own assigned courses
				$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
				$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
				$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));

				if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
					$view_only = true;
				}

				$exam_types = $this->ExamResult->ExamType->find('all', array(
					'fields' => array('id', 'exam_name', 'percent', 'order'),
					'conditions' => array('ExamType.published_course_id' => $published_course_id),
					'contain' => array(),
					'order' => array('order ASC'),
					'recursive' => -1
				));

				$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					), 
					'contain' => array(
						'Section', 
						'Course', 
						'CourseInstructorAssignment' => array(
							'conditions' => array('CourseInstructorAssignment.isprimary' => true),
							'Staff' => array('Title', 'Position')
						)
					)
				));

				$section_detail = $section_and_course_detail['Section'];
				$course_detail = $section_and_course_detail['Course'];
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);

				$student_makeup = $student_course_register_and_adds['makeup'];
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
				$grade_submission_status = $this->ExamResult->getExamGradeEntrySubmissionStatus($published_course_id, $student_course_register_and_adds);
			}

			debug($student_course_register_and_adds);
			$this->set(compact('grade_scale', 'published_course_id', 'students', 'exam_types', 'exam_results', 'section_detail', 'course_detail', 'course_assignment_detail', 'gradeStatistics', 'student_adds', 'student_makeup', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'lastGradeSubmissionDate'));
		}
	}

	function cancel_grade_change_request($grade_change_id = null)
	{
		if ($grade_change_id) {

			$last_grade_change = $this->ExamResult->CourseRegistration->ExamGrade->ExamGradeChange->find('first', array(
				'conditions' => array('ExamGradeChange.id' => $grade_change_id),
				'contain' => array('ExamGrade' => array('CourseRegistration', 'CourseAdd'))
			));

			if (isset($last_grade_change['ExamGrade']['CourseRegistration']) && !empty($last_grade_change['ExamGrade']['CourseRegistration'])) {
				
				$student_detail = $this->ExamResult->CourseRegistration->find('first', array(
					'conditions' => array('CourseRegistration.id' => $last_grade_change['ExamGrade']['CourseRegistration']['id']),
					'contain' => array('Student')
				));

				$published_course_id = $last_grade_change['ExamGrade']['CourseRegistration']['published_course_id'];
				
				$makeup_exam_detail = $this->ExamResult->ExamType->PublishedCourse->CourseRegistration->MakeupExam->find('first', array(
					'conditions' => array(
						'OR' => array(
							'MakeupExam.course_registration_id' => $last_grade_change['ExamGrade']['CourseRegistration']['id']
						)
					),
					'order' => array('MakeupExam.created' => 'DESC'),
					'recursive' => -1
				));

			} else {

				$student_detail = $this->ExamResult->CourseAdd->find('first', array(
					'conditions' => array('CourseAdd.id' => $last_grade_change['ExamGrade']['CourseAdd']['id']),
					'contain' => array('Student')
				));

				$published_course_id = $last_grade_change['ExamGrade']['CourseAdd']['published_course_id'];
				$makeup_exam_detail = $this->ExamResult->ExamType->PublishedCourse->CourseRegistration->MakeupExam->find('first', array(
					'conditions' => array(
						'OR' => array(
							'MakeupExam.course_add_id' => $last_grade_change['ExamGrade']['CourseAdd']['id'],
						)
					),
					'order' => array('MakeupExam.created DESC'),
					'recursive' => -1
				));
			}

			if (!empty($makeup_exam_detail)) {
				$published_course_id = $makeup_exam_detail['MakeupExam']['published_course_id'];
			}
			//Staff ID
			$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('CourseInstructorAssignment.staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
			//Staff ID
			$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
			//User ID
			$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));

			//TODO: Role based is removed but the instructor can cancel department grade change request.
			//To disable this add one authentication which is if the login user is the instructor or the department
			/*if(($this->role_id == 6 && 
				$last_grade_change['ExamGradeChange']['initiated_by_department'] == 1 &&
				$last_grade_change['ExamGradeChange']['manual_ng_conversion'] == 0 &&
				$last_grade_change['ExamGradeChange']['auto_ng_conversion'] == 0 &&
				$last_grade_change['ExamGradeChange']['college_approval'] == null) ||
				($this->role_id != 6 && 
				$last_grade_change['ExamGradeChange']['initiated_by_department'] == 0 &&
				$last_grade_change['ExamGradeChange']['manual_ng_conversion'] == 0 &&
				$last_grade_change['ExamGradeChange']['auto_ng_conversion'] == 0 &&
				$last_grade_change['ExamGradeChange']['department_approval'] == null)) {*/
			if (($login_instructor_id != $instructor_id_for_checking &&
					$last_grade_change['ExamGradeChange']['initiated_by_department'] == 1 &&
					$last_grade_change['ExamGradeChange']['manual_ng_conversion'] == 0 &&
					$last_grade_change['ExamGradeChange']['auto_ng_conversion'] == 0 &&
					$last_grade_change['ExamGradeChange']['college_approval'] == null) ||
				($login_instructor_id == $instructor_id_for_checking &&
					$last_grade_change['ExamGradeChange']['initiated_by_department'] == 0 &&
					$last_grade_change['ExamGradeChange']['manual_ng_conversion'] == 0 &&
					$last_grade_change['ExamGradeChange']['auto_ng_conversion'] == 0 &&
					$last_grade_change['ExamGradeChange']['department_approval'] == null)
			) {
				if ($this->ExamResult->CourseRegistration->ExamGrade->ExamGradeChange->delete($grade_change_id)) {
					$this->Flash->success(__('Exam grade change request for ' . ($student_detail['Student']['first_name'] . ' ' . $student_detail['Student']['middle_name'] . ' ' . $student_detail['Student']['last_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')') . ' is cancelled.'));
				} else {
					$this->Flash->error(__('Exam grade change request cancellation is failed. Please, try again.'));
				}
			}
			return $this->redirect(array('action' => (strcasecmp($this->request->action, 'add') == 0 ? 'add' : (!empty($student_detail['Student']['department_id']) ? 'submit_grade_for_instructor' : 'submit_freshman_grade_for_instructor')), $published_course_id));
		} else {
			return $this->redirect(array('action' => (strcasecmp($this->request->action, 'add') == 0 ? 'add' : (!empty($student_detail['Student']['department_id']) ? 'submit_grade_for_instructor' : 'submit_freshman_grade_for_instructor'))));
		}
	}

	// *@Wonde *Generate Student Academic Status Using Command line whenever needed 
	function _generate_student_academic_status($section_id = null)
	{
		if (!empty($section_id)) {
			$getListOfPublishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->find('list', array(
				'conditions' => array('PublishedCourse.section_id' => $section_id),
				'order' => array('PublishedCourse.semester' => 'ASC')
			));
		} else {
			$getListOfPublishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->find('list', array('order' => array('PublishedCourse.semester' => 'ASC')));
		}

		if (!empty($getListOfPublishedCourses)) {
			foreach ($getListOfPublishedCourses as $value) {
				$this->ExamResult->CourseAdd->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse(
					$value
				);
				echo "Done." . $value . "\n";
			}
		} else {
			return "There is no published course.";
		}
	}

	function autoSaveResult()
	{
		$this->autoRender = false;
		$exam_results = array();
		$save_is_ok = true;
		$do_manipulate = false;
		$published_course_id = $this->request->data['ExamResult']['published_course_id'];
		$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

		if (!empty($published_course_id)) {

			$exam_types = $this->ExamResult->ExamType->find('all', array(
				'fields' => array('id', 'exam_name', 'percent', 'order'), 
				'conditions' => array('ExamType.published_course_id' => $published_course_id),
				'contain' => array(),
				'order' => array('order ASC'),
				'recursive' => -1
			));

			$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
			$published_course_department = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section' => array('Department'))));
			//Do you have the right to manage exam result and grade
			$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
			$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));
			
			if ($instructor_id_for_checking == $instructor_id) {
				$do_manipulate = true;
			}

			$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

			if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
				$do_manipulate = false;
			}
		}

		debug($do_manipulate);

		if ($do_manipulate && isset($this->request->data['ExamResult']) && !empty($this->request->data['ExamResult']) && isset($exam_types) && !empty($exam_types)) {
			foreach ($this->request->data['ExamResult'] as $key => $exam_result) {
				$save_is_ok = true;
				if (is_array($exam_result)) {
					if (trim($exam_result['result']) != "") {
						debug($exam_result);
						$exam_results = $exam_result;
						$exam_percent = "";
						$exam_type_id = "";
						$exam_name = "";

						if (isset($exam_result['id'])) {
							$exam_type_id = $this->ExamResult->field('exam_type_id', array('id' => $exam_result['id']));
						} else {
							$exam_type_id = $exam_result['exam_type_id'];
						}

						//Check if exam type is available
						foreach ($exam_types as $key => $exam_type) {
							if ($exam_type['ExamType']['id'] == $exam_type_id) {
								$exam_percent = $exam_type['ExamType']['percent'];
								$exam_name = $exam_type['ExamType']['exam_name'];
								break;
							}
						}
						
						if ($exam_percent == "" || $exam_name == "" || $exam_type_id == "") {
							$save_is_ok = false;
						} else if (is_numeric($exam_result['result']) && $exam_result['result'] > $exam_percent) {
							$save_is_ok = false;
						}

						if ($save_is_ok) {
							$data['ExamResult'] = $exam_results;
							debug($data);
							if (isset($data['ExamResult']['id']) && !empty($data['ExamResult']['id'])) {
								$alreadyRecored = $this->ExamResult->find('first', array('conditions' => array('ExamResult.id' => $data['ExamResult']['id']), 'recursive' => -1));
							} else {
								$alreadyRecored = $this->ExamResult->find('first', array(
									'conditions' => array(
										'ExamResult.exam_type_id' => $data['ExamResult']['exam_type_id'], 
										'ExamResult.course_registration_id' => $data['ExamResult']['course_registration_id'],
										'ExamResult.course_add' => $data['ExamResult']['course_add']
									),
									'recursive' => -1
								));
							}

							if (isset($alreadyRecored) && !empty($alreadyRecored)) {
								$data['ExamResult']['id'] = $alreadyRecored['ExamResult']['id'];
							} else {
								$this->ExamResult->create();
							}

							$this->set($data['ExamResult']);

							if ($this->ExamResult->save($data)) {
							} else {
								debug($data);
							}
						}
					}
				}
			}
		}
	}

	public function rollback_grade_submission($published_course_id = null)
	{

		$published_courses_by_section = array();
		$published_course_combo_id = "";
		$department_id = "";
		$academic_year = "";
		$semester = "";
		$program_id = "";
		$program_type_id = "";
		$college_id = "";

		$grade_scale = array();
		$programs = $this->ExamResult->CourseRegistration->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $this->ExamResult->CourseRegistration->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		if (!empty($this->request->data) && isset($this->request->data['listPublishedCourses'])) {
			$department_id = $this->request->data['ExamResult']['department_id'];
			if (isset($this->college_ids) && !empty($this->college_ids)) {
				if (!isset($department_id) && empty($department_id)) {
					$department_id = $this->request->data['ExamResult']['college_id'];
				}
				$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($department_id, $this->request->data['ExamResult']['acadamic_year'], $this->request->data['ExamResult']['semester'], $this->request->data['ExamResult']['program_id'], $this->request->data['ExamResult']['program_type_id'], 0, null);
			} else {
				$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
					$department_id,
					$this->request->data['ExamResult']['acadamic_year'],
					$this->request->data['ExamResult']['semester'],
					$this->request->data['ExamResult']['program_id'],
					$this->request->data['ExamResult']['program_type_id'],
					0,
					$this->request->data['ExamResult']['year_level_id']
				);
			}

			if (empty($publishedCourses)) {
				$this->Flash->info(__('There is no published courses with the selected filter criteria.'));
			} else {
				$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
			}

			$this->set(compact('publishedCourses'));
		}


		//when rollback button is clicked
		if (isset($this->request->data['rollback']) && !empty($this->request->data['rollback'])) {
			
			$courseRegistrationAndGrade = array();
			$student_ids_to_regenarate_status = array();
			$rolledback_grades = array();
			$count = 0;

			if (!empty($this->request->data['ExamResult'])) {
				foreach ($this->request->data['ExamResult'] as $key => $student) {
					if (is_int($key) && $student['gp'] == 1 && !empty($student['exam_grade_id'])) {
						$courseRegistrationAndGrade['ExamGrade'][$count]['id'] = $student['exam_grade_id'];
						$courseRegistrationAndGrade['ExamGrade'][$count]['department_approval'] = null;
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approval'] = null;
						$courseRegistrationAndGrade['ExamGrade'][$count]['department_approved_by'] = null;
						$courseRegistrationAndGrade['ExamGrade'][$count]['department_approval_date'] = null;
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approval_date'] = null;
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approved_by'] = null;

						$rolledback_grades[] = $student['exam_grade_id'];

						if (!empty($student_ids_to_regenarate_status) && !in_array($student['student_id'], $student_ids_to_regenarate_status)) {
							$student_ids_to_regenarate_status[] = $student['student_id'];
						} else if (empty($student_ids_to_regenarate_status)) {
							$student_ids_to_regenarate_status[] = $student['student_id'];
						}

						$count++;
					}
					//$count++;
				}
			}

			// debug($this->request->data['ExamResult']);
			// debug($student_ids_to_regenarate_status);
			// debug($courseRegistrationAndGrade);
			//exit();

			if (!empty($courseRegistrationAndGrade)) {

				$department_approved_bys = ClassRegistry::init('ExamGrade')->find('list', array(
					'conditions' => array(
						'ExamGrade.id' => $rolledback_grades
					),
					'fields' => array('ExamGrade.department_approved_by')
				));

				if (ClassRegistry::init('ExamGrade')->saveAll($courseRegistrationAndGrade['ExamGrade'], array('validate' => false))) {
					$selected_count = count($student_ids_to_regenarate_status);
					$this->Flash->success(__('The selected ' . $selected_count  . ' submitted ' . ($selected_count == 1 ? 'grade is' : 'grades are') . ' rolled back successfully for resubmission by the assigned course instructor.'));
					// regenerate all students status
					if (!empty($student_ids_to_regenarate_status)) {
						foreach ($student_ids_to_regenarate_status as $key => $stdnt_id) {
							// regenarate all status regardless if it when it is regenerated
							$status_status = ClassRegistry::init('StudentExamStatus')->regenerate_all_status_of_student_by_student_id($stdnt_id, 0);
						}
					}

					// send grade rollback notification.
					if (!empty($rolledback_grades)) {
						if (!empty($department_approved_bys)) {
							$department_approved_bys = array_unique($department_approved_bys);
						}
						//debug($department_approved_bys);

						ClassRegistry::init('AutoMessage')->sendNotificationOnRegistrarGradeRollback($rolledback_grades, $this->Session->read('Auth.User')['full_name'], $this->Session->read('Auth.User')['id'], $department_approved_bys);
					}

					if (isset($this->request->data['ExamResult']['select_all'])) {
						unset($this->request->data['ExamResult']['select_all']);
					}
				} else {
					debug(ClassRegistry::init('ExamGrade')->invalidFields());
				}
			}
		}


		if (!empty($this->department_ids)) {
			$departments = $this->ExamResult->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->ExamResult->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
		}

		$current_acy = $this->AcademicYear->current_academicyear();

		//$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y') - 1);
		//$acyear_list = $this->AcademicYear->academicYearInArray((explode('/', $current_acy)[0]) - ACY_BACK_FOR_ROLLING_BACK_GRADE_SUBMISSION, (explode('/', $current_acy)[0]));

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
			$acyear_list = $this->AcademicYear->academicYearInArray((explode('/', $current_acy)[0]) - (ACY_BACK_FOR_ROLLING_BACK_GRADE_SUBMISSION + 5), (explode('/', $current_acy)[0]));
		} else {
			$acyear_list = $this->AcademicYear->academicYearInArray((explode('/', $current_acy)[0]) - ACY_BACK_FOR_ROLLING_BACK_GRADE_SUBMISSION, (explode('/', $current_acy)[0]));
		}
		
		
		//$yearLevels = $this->ExamResult->CourseRegistration->PublishedCourse->YearLevel->distinct_year_level();
		$yearLevels = $this->year_levels;

		$this->set(compact('grade_scale', 'programs', 'program_types', 'published_course_id', 'published_course_combo_id', 'department_id', 'academic_year', 'semester', 'program_id', 'departments', 'acyear_list', 'colleges', 'program_type_id', 'college_id', 'yearLevels', 'year_level_id'));
	}

	public function rollback_entry_form($published_course_id = null)
	{
		$this->layout = 'ajax';

		if ($this->Auth->user('id')) {
			$edit = 0;

			$students = array();
			$student_adds = array();
			$student_makeup = array();

			$display_grade = false;
			$view_only = false;

			if (!empty($published_course_id)) {
				$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Section' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'shortname', 'college_id'),
								'College' => array('id', 'name', 'shortname'),
							),
							'College' => array(
								'fields' => array('id', 'name', 'shortname'),
								'Campus' => array('id', 'name')
							),
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
							'YearLevel' => array('id', 'name'),
						), 
						'Course' => array(
							'GradeType' => array('Grade')
						),
						'CourseInstructorAssignment' => array(
							'conditions' => array('CourseInstructorAssignment.isprimary' => 1),
							'Staff' => array('Title', 'Position')
						)
					)
				));
				
				//debug($section_and_course_detail);

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
			}

			$this->set(compact('grade_submission_status', 'view_only', 'student_course_register_and_adds', 'section_and_course_detail', 'grade_scale', 'gradeStatistics'));
		}

	}

	public function submit_grade_registrar($published_course_id = null)
	{

		$published_courses_by_section = array();
		$published_course_combo_id = "";
		$department_id = "";
		$academic_year = "";
		$semester = "";
		$program_id = "";
		$program_type_id = "";
		$college_id = "";

		$grade_scale = array();
		$programs = $this->ExamResult->CourseRegistration->PublishedCourse->Section->Program->find('list');
		$program_types = $this->ExamResult->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		if (!empty($this->request->data) && isset($this->request->data['listPublishedCourses'])) {
			$department_id = $this->request->data['ExamResult']['department_id'];

			$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
				$department_id,
				$this->request->data['ExamResult']['acadamic_year'],
				$this->request->data['ExamResult']['semester'],
				$this->request->data['ExamResult']['program_id'],
				$this->request->data['ExamResult']['program_type_id'],
				0,
				$this->request->data['ExamResult']['year_level_id']
			);

			if (empty($publishedCourses)) {
				$this->Flash->info(__('There is no published courses with the selected search criteria.'));
			} else {
				$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
			}

			$this->set(compact('publishedCourses'));
		}


		//Get Grade Report button is clicked
		if (isset($this->request->data['saveGrade']) && !empty($this->request->data['saveGrade'])) {

			$courseRegistrationAndGrade = array();
			$count = 0;

			$publishedCourse = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.id' => $this->request->data['PublishedCourse']['id']
				),
				'contain' => array('Course'),
				'recursive' => -1
			));

			$scale_id = ClassRegistry::init('GradeScale')->getGradeScaleIdGivenPublishedCourse($this->request->data['PublishedCourse']['id']);

			if (!empty($this->request->data['ExamResult'])) {
				foreach ($this->request->data['ExamResult'] as $key => $student) {
					if (is_int($key) && $student['gp'] == 1 && $scale_id != 0 && !empty($student['grade'])) {
						debug($count);

						$courseRegistrationAndGrade['ExamGrade'][$count]['grade'] = $student['grade'];

						if (!empty($student['course_registration_id'])) {
							$courseRegistrationAndGrade['ExamGrade'][$count]['course_registration_id'] = $student['course_registration_id'];
						} else if (!empty($student['course_add_id'])) {
							$courseRegistrationAndGrade['ExamGrade'][$count]['course_add_id'] = $student['course_add_id'];
						}


						$courseRegistrationAndGrade['ExamGrade'][$count]['department_approval'] = 1;
						$courseRegistrationAndGrade['ExamGrade'][$count]['grade_scale_id'] = $scale_id;
						$courseRegistrationAndGrade['ExamGrade'][$count]['department_reason'] = 'Registrar Data Entry interface';
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approval'] = 1;
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_reason'] = 'Registrar Data Entry interface';
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_reason'] = 'Registrar Data Entry interface';
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s');
						$courseRegistrationAndGrade['ExamGrade'][$count]['department_approval_date'] = date('Y-m-d H:i:s');
						$courseRegistrationAndGrade['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
						$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');

						$count++;
					}
					//$count++;
				}
			}

			if (!empty($courseRegistrationAndGrade)) {
				debug($courseRegistrationAndGrade);
				//saveAll 
				if (ClassRegistry::init('ExamGrade')->saveAll($courseRegistrationAndGrade['ExamGrade'], array('validate' => false))) {
					//Notifications
					// ClassRegistry::init('AutoMessage')->sendNotificationOnRecord($courseRegistrationAndGrade['ExamGrade']);
					$st_count = count($courseRegistrationAndGrade['ExamGrade']);
					
					if (ClassRegistry::init('ExamGrade')->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($published_course_id)) {
					}

					$this->Flash->success( __('Exam grade saving for ' . ($st_count) . ' ' . ($st_count == 1 ? 'student' : 'students') .  ' for ' . $publishedCourse['Course']['course_code_title'] . ' course was successful.'));
					
					//unset($this->request->data);
					unset($this->request->data['select_all']);
				} else {
					debug(ClassRegistry::init('ExamGrade')->invalidFields());
				}
			}
		}


		if (!empty($this->department_ids)) {
			$departments = $this->ExamResult->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
		} else if (!empty($this->college_id)) {
			$colleges = $this->ExamResult->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
		}

		//$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y') - 1);

		$current_acy = $this->AcademicYear->current_academicyear();

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
			$acyear_list = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $current_acy)[0]));
		} else {
			$acyear_list = $this->AcademicYear->academicYearInArray((explode('/', $current_acy)[0]) - ACY_BACK_FOR_ALL, (explode('/', $current_acy)[0]));
		}
		
		$yearLevels = $this->ExamResult->CourseRegistration->PublishedCourse->YearLevel->distinct_year_level();

		$this->set(compact('grade_scale', 'programs', 'program_types', 'published_course_id', 'published_course_combo_id', 'department_id', 'academic_year', 'semester', 'program_id', 'departments', 'acyear_list', 'colleges', 'program_type_id', 'college_id', 'yearLevels', 'year_level_id'));
	}


	public function grade_entry_form($published_course_id = null)
	{
		$this->layout = 'ajax';

		if ($this->Auth->user('id')) {
			$edit = 0;

			$students = array();
			$student_adds = array();
			$student_makeup = array();

			$display_grade = false;
			$view_only = false;

			$assigned_instructor_fullname_and_username = '';
			$assigned_instructor_fullname_with_rank = '';
			$selected_course_title_code = '';
			$selected_section_name = '';
			$selected_section_detailed = array();

			if (!empty($published_course_id)) {

				$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Section' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'shortname', 'college_id'),
								'College' => array('id', 'name', 'shortname'),
							),
							'College' => array(
								'fields' => array('id', 'name', 'shortname'),
								'Campus' => array('id', 'name')
							),
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
							'YearLevel' => array('id', 'name'),
						), 
						'Course' => array(
							'GradeType' => array('Grade')
						),
						'CourseInstructorAssignment' => array(
							'conditions' => array('CourseInstructorAssignment.isprimary' => 1),
							'Staff' => array(
								'Title', 
								'Position',
								'User' => array('id', 'username', 'role_id', 'active'),
								'Department' => array('id', 'name'),
							)
						)
					)
				));

				if (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff'])) {
					
					
					//debug(str_ireplace('x', '', $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['full_name']));
					$search_x_in_name = '/\b[xX]+/i'; //occurrences of "x" or "X" at the beginning of a word, along with any trailing "x" or "X", will be removed.

					$assigned_instructor_fullname_and_username = (trim((ucwords(strtolower(preg_replace($search_x_in_name, '', $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['full_name'])))))) . (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['User']['username']) ? ' (' . $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['User']['username']. ')' : '');
					$assigned_instructor_fullname_with_rank = (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Title']['title']) ? $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Title']['title']. '. ' : '') . (trim(ucwords(strtolower(preg_replace($search_x_in_name, '',$section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['full_name']))))) . (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Position']['position']) ? ' (' . $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Position']['position']. ')' : '') . (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Department']['id']) ? ',  ' . $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Department']['name']. '' : '');

					if ($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['active'] || (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['User']) && $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['User']['active'])) {
						if (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 && ALLOW_ADMIN_REGISTRAR_GRADE_ENTRY_ON_NOT_DEACTIVATED_INSTRUCTOR_ASSIGNMENT == 0) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && ALLOW_NON_ADMIN_REGISTRAR_GRADE_ENTRY_ON_NOT_DEACTIVATED_INSTRUCTOR_ASSIGNMENT == 0)) {
							$view_only = true;
						} else if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
							$view_only = true;
						}
					} else if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
						$view_only = true;
					}
				}

				if (isset($section_and_course_detail['Course']['id'])) {
					$selected_course_title_code = $section_and_course_detail['Course']['course_code_title'];
				}

				if (isset($section_and_course_detail['Section']['id'])) {
					$selected_section_name = ClassRegistry::init('Section')->get_section_detailed_name($section_and_course_detail['Section']['id'], 0, 0);
					$selected_section_detailed = (explode ('~', ClassRegistry::init('Section')->get_section_detailed_name($section_and_course_detail['Section']['id'], 1, 1)));
				}

				// debug($selected_course_title_code);
				// debug($assigned_instructor_fullname_with_rank);
				//debug($assigned_instructor_fullname_and_username);
				// debug($selected_section_name);
				// debug($selected_section_detailed);

				//debug($section_and_course_detail);

				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
				
				//$grade_submission_status = $this->ExamResult->getExamGradeEntrySubmissionStatus($published_course_id, $student_course_register_and_adds);
				$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);

				//debug($grade_submission_status);

			}

			$this->set(compact('grade_submission_status', 'view_only', 'gradeStatistics', 'grade_scale', 'grade_submission_status', 'student_course_register_and_adds', 'section_and_course_detail', 'assigned_instructor_fullname_and_username', 'assigned_instructor_fullname_with_rank', 'selected_course_title_code', 'selected_section_name', 'selected_section_detailed'));
		} //End of login user cheking

	}

	public function auto_grade_generation($published_course_id = null)
	{
		die;
		$published_courses_by_section = array();
		$published_course_combo_id = "";
		$department_id = "";
		$academic_year = "";
		$semester = "";
		$program_id = "";
		$program_type_id = "";
		$college_id = "";

		$grade_scale = array();
		$programs = $this->ExamResult->CourseRegistration->PublishedCourse->Section->Program->find('list');
		$program_types = $this->ExamResult->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		if (!empty($this->request->data) && isset($this->request->data['listPublishedCourses'])) {
			$department_id = $this->request->data['ExamResult']['department_id'];

			$publishedCourses = $this->ExamResult->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
				$department_id,
				$this->request->data['ExamResult']['acadamic_year'],
				$this->request->data['ExamResult']['semester'],
				$this->request->data['ExamResult']['program_id'],
				$this->request->data['ExamResult']['program_type_id'],
				0,
				$this->request->data['ExamResult']['year_level_id']
			);

			if (empty($publishedCourses)) {
				$this->Flash->info(__('There is no published courses with the selected filter criteria.'));
			} else {
				$publishedCourses = $publishedCourses;
			}

			debug($publishedCourses);
			$courseRegistrationAndGrade = array();
			$count = 0;

			if (!empty($publishedCourses)) {
				foreach ($publishedCourses as $pk => $pv) {
					foreach ($pv as $pid => $pvi) {
						debug($pid);
						//$courseRegistrationAndGrade=array();
						//$count=0;

						$publishedC = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
							'conditions' => array('PublishedCourse.id' => $pid),
							'contain' => array('Course' => array('GradeType' => array('Grade')))
						));
						$scale_id = ClassRegistry::init('GradeScale')->getGradeScaleIdGivenPublishedCourse($pid);
						$gradeList = array();

						if (isset($publishedC['Course']['GradeType']['Grade'])) {
							foreach ($publishedC['Course']['GradeType']['Grade'] as $key => $value) {
								$gradeList[] = $value['grade'];
							}
							$gradeList[] = 'NG';
							$gradeList[] = 'W';
							$gradeList[] = 'I';
						}

						$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($pid);
						$count = 0;
						//debug($student_course_register_and_adds);

						if (!empty($student_course_register_and_adds)) {
							foreach ($student_course_register_and_adds as $aadk => $aadv) {
								foreach ($aadv as $akk => $adv) {
									$randGradeIndex = rand(0, count($gradeList) - 1);
									$gradeL = $gradeList[$randGradeIndex];
									debug($adv);
									debug($gradeL);

									if (empty($adv['ExamGrade']) && !empty($gradeL)) {
										$courseRegistrationAndGrade['ExamGrade'][$count]['grade'] = $gradeL;
										
										if (!empty($adv['CourseRegistration']['id'])) {
											$courseRegistrationAndGrade['ExamGrade'][$count]['course_registration_id'] = $adv['CourseRegistration']['id'];
										} else if (!empty($adv['CourseAdd']['id'])) {
											$courseRegistrationAndGrade['ExamGrade'][$count]['course_add_id'] = $adv['CourseAdd']['id'];
										}


										$courseRegistrationAndGrade['ExamGrade'][$count]['department_approval'] = 1;
										$courseRegistrationAndGrade['ExamGrade'][$count]['grade_scale_id'] = $scale_id;
										$courseRegistrationAndGrade['ExamGrade'][$count]['department_reason'] = 'Auto Generated for Testing';
										$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approval'] = 1;
										$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_reason'] = 'Auto Generated for Testing';
										$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_reason'] = 'Auto Generated for Testing';
										$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s');
										$courseRegistrationAndGrade['ExamGrade'][$count]['department_approval_date'] = date('Y-m-d H:i:s');
										$courseRegistrationAndGrade['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
										$courseRegistrationAndGrade['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');
										$count++;

										debug($courseRegistrationAndGrade);
									}
								}
							}
						}

						if (!empty($courseRegistrationAndGrade)) {
							debug($courseRegistrationAndGrade);
							if (ClassRegistry::init('ExamGrade')->saveAll($courseRegistrationAndGrade['ExamGrade'], array('validate' => false))) {
								$this->Flash->success(__('Exam grade has been successfully saved and approved.'));
							}
						}
					}
				}
			}
		}

		if (!empty($this->department_ids)) {
			$departments = $this->ExamResult->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
		} else if (!empty($this->college_id)) {
			$colleges = $this->ExamResult->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
		}

		$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y') - 1);
		$yearLevels = $this->ExamResult->CourseRegistration->PublishedCourse->YearLevel->distinct_year_level();
		$this->set(compact('grade_scale', 'programs', 'program_types', 'published_course_id', 'published_course_combo_id', 'department_id', 'academic_year', 'semester', 'program_id', 'departments', 'acyear_list', 'colleges', 'program_type_id', 'college_id', 'yearLevels', 'year_level_id'));
	}
	
	public function import_result($published_course_id = null)
	{

		if (!$published_course_id && !empty($this->request->data)) {
			if (isset($this->request->data['ExamResult']['published_course_id']) && !empty($this->request->data['ExamResult']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamResult']['published_course_id'];
			}
			//$this->redirect(array('action' => 'add', $this->request->data['ExamResult']['published_course_id']));
		}

		$selectedAcadamicYear = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->find('first', array(
			'conditions' => array(
				'CourseInstructorAssignment.staff_id in (select id from staffs where user_id="' . $this->Auth->user('id') . '")'
			),
			'fields' => array('CourseInstructorAssignment.academic_year', 'CourseInstructorAssignment.academic_year'),
			'order' => array('CourseInstructorAssignment.academic_year' => 'DESC')
		));

		if (!empty($selectedAcadamicYear)) {
			$selected_acadamic_year = $selectedAcadamicYear['CourseInstructorAssignment']['academic_year'];
		} else {
			$this->Flash->error( __('Your account doesn\'t have any course assignments yet in the system.'));
			$this->redirect('/');
		}

		$selected_semester = 'I';
		$published_course_combo_id = "";
		$students = array();

		$student_adds = array();
		$student_makeup = array();

		$instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
		
		if (!empty($published_course_id)) {
			$published_course_combo_id = $published_course_id;
			//debug($student_course_register_and_adds);
			//$do_not_manipulate = true;
			$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection($selected_acadamic_year, $selected_semester, $instructor_id);
		} else {
			$publishedCourses = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection($selected_acadamic_year, $selected_semester, $instructor_id);
		}

		$this->set(compact('published_course_combo_id', 'selected_semester'));

		if (!empty($this->request->data) && is_uploaded_file($this->request->data['File']['tmp_name'])) {

			$ext = strtolower(end(explode('.', $this->request->data['File']['name'])));

			// check if grade is already submitted then reject import
			$checkIfGradeSubmitted = $this->ExamResult->CourseRegistration->ExamGrade->isGradeSubmittedForPublishedCourse($this->request->data['ExamResult']['published_course_id']);
			
			if ($checkIfGradeSubmitted) {
				$this->Flash->error( __('Exam grade has been submitted already you can not modify it by uploading rather you need to modify or request grade change online.'));
			}

			//check the file type before doing the  manipulations.
			if (strcasecmp($ext, 'csv') != 0) {
				$this->Flash->error( __('Importing Error. Please save your file as "CSV" type while you saved the file and import again. Current file format is: ' . $ext));
				return $this->redirect(array('controller' => 'examResults', 'action' => 'import_result'));
				//return;
			}

			$handle = fopen($this->request->data['File']['tmp_name'], "r");
			$headers = fgetcsv($handle, 1000, ",");

			//required field
			$required_fields = array('StudentID');

			if (empty($headers)) {
				$this->Flash->error(__('Importing Error!! Please insert  StudentID filed name at first row of your excel file.'));
				return $this->redirect(array('controller' => 'examResults', 'action' => 'import_result'));
				//return;
			}

			$non_existing_field = array();

			if (count($required_fields) > 0) {
				for ($k = 0; $k < count($required_fields); $k++) {
					if (in_array($required_fields[$k], $headers) === FALSE) {
						$non_existing_field[] = $required_fields[$k];
					}
				}
			}

			if (count($non_existing_field) > 0) {
				$field_list = "";

				foreach ($non_existing_field as $k => $v) {
					$field_list .= ($v . ", ");
				}

				$field_list = substr($field_list, 0, (strlen($field_list) - 2));
				$this->Flash->error( __('Importing Error. ' . $field_list . ' are required in the excel file you imported at first row.'));
				return $this->redirect(array('controller' => 'examResults', 'action' => 'import_result'));
				//return;

			} else {

				// Need to do matching with department and find out the year level
				// Need to check if section created if not created , we need to create it
				// group them

				$arrayGroup = array();
				$fields_name_acceptedStudents_table = $headers;

				$non_valide_rows = array();
				debug($fields_name_acceptedStudents_table);

				// check if assessement was created earlier for the published course
				// if not create
				$setup = $this->ExamResult->ExamType->examSetupCreation($this->request->data['ExamResult']['published_course_id'], $fields_name_acceptedStudents_table);

				if ($setup) {
					// everything is fine so find the current assessement and do the validation for each students
					$assessement = $this->ExamResult->ExamType->find('all', array(
						'conditions' => array('ExamType.published_course_id' => $this->request->data['ExamResult']['published_course_id']),
						'recursive' => -1
					));
				} else {
					$error = $this->ExamResult->ExamType->invalidFields();
					if (isset($error['assessement']) && !empty($error['assessement'])) {
						$this->Flash->error(__($error['assessement'][0]));
					}
				}
				//return;

				//$row_data = array();
				$xls_data = array();

				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$row_data = array();
					$studentId = null;
					$count = 0;
					$regFind = array();
					$addFind = array();

					foreach ($data as $k => $v) {
						$fieldValue = $v;
						$fieldName = $fields_name_acceptedStudents_table[$k];

						if ($fields_name_acceptedStudents_table[$k] == 'StudentID' && (!isset($fieldValue) || trim($fieldValue) == NULL)) {
							$non_valide_rows[] = "Please enter student number " . $k;
							continue;
						}

						if (trim($fields_name_acceptedStudents_table[$k]) == 'StudentID') {
							$currentStudentNumber = trim($fieldValue);
							//check from the database if the student is valid
							if (isset($currentStudentNumber) && !empty($currentStudentNumber)) {
								$validStudent = ClassRegistry::init('Student')->find('first', array(
									'conditions' => array(
										'Student.studentnumber like ' => $currentStudentNumber . '%',
										'Student.id in (select student_id from course_registrations where published_course_id="' . $this->request->data['ExamResult']['published_course_id'] . '")'
									),
									'recursive' => -1
								));

								$studentId = $validStudent['Student']['id'];

								if (empty($validStudent)) {
									$non_valide_rows[] = "Student ID at row number " . $k . " does\'t registered for your course or Student ID doesn\'t exist in the system.";
									continue;
								}
							}
						}
						

						if (trim($fields_name_acceptedStudents_table[$k]) == "StudentID") {

							$validStudent =  ClassRegistry::init('Student')->find('first', array(
								'conditions' => array(
									'Student.studentnumber like ' => trim($fieldValue) . '%',
									'Student.id in (select student_id from course_registrations where published_course_id="' . $this->request->data['ExamResult']['published_course_id'] . '")'
								),
								'recursive' => -1
							));

							$studentId = $validStudent['Student']['id'];
							

							$regFind = ClassRegistry::init('CourseRegistration')->find('first', array(
								'conditions' => array(
									'CourseRegistration.published_course_id' => $this->request->data['ExamResult']['published_course_id'],
									'CourseRegistration.student_id' => $studentId,
								),
								'recursive' => -1
							));

							$addFind = ClassRegistry::init('CourseAdd')->find('first', array(
								'conditions' => array(
									'CourseAdd.published_course_id' => $this->request->data['ExamResult']['published_course_id'],
									'CourseAdd.student_id' => $studentId,
								),
								'recursive' => -1
							));

							if (isset($regFind) && !empty($regFind)) {
								$row_data[$count]['ExamResult']['course_registration_id'] = $regFind['CourseRegistration']['id'];
							} else {
								if (isset($addFind) && !empty($addFind)) {
									$row_data[$count]['ExamResult']['course_registration_id'] = $addFind['CouraseAdd']['id'];
									$row_data[$count]['ExamResult']['course_add'] = 1;
								}
							}

							if (empty($regFind['CourseRegistration']['id']) && empty($addFind['CourseRegistration']['id'])) {
								continue;
							}

						} else if (trim($fields_name_acceptedStudents_table[$k]) != 'StudentID') {
							
							$assement = explode('-', $fieldName);

							if (isset($assement[0]) && !empty($assement[0])) {

								$examTypes = ClassRegistry::init('ExamType')->find('first', array(
									'conditions' => array(
										'ExamType.published_course_id' => $this->request->data['ExamResult']['published_course_id'],
										'ExamType.exam_name' => trim($assement[0])
									),
									'recursive' => -1
								));


								if ($fieldValue > $examTypes['ExamType']['percent']) {
									$non_valide_rows[] = "Please enter a value less than " . $examTypes['ExamType']['percent'] . " at row number " . $k;
									continue;
								}

								//check if result is recorded for the type

								$regFind = ClassRegistry::init('CourseRegistration')->find('first', array(
									'conditions' => array(
										'CourseRegistration.published_course_id' => $this->request->data['ExamResult']['published_course_id'],
										'CourseRegistration.student_id' => $studentId,
									),
									'recursive' => -1
								));
								debug($regFind['CourseRegistration']['id']);

								$addFind = ClassRegistry::init('CourseAdd')->find('first', array(
									'conditions' => array(
										'CourseAdd.published_course_id' => $this->request->data['ExamResult']['published_course_id'],
										'CourseAdd.student_id' => $studentId,
									),
									'recursive' => -1
								));


								if (isset($regFind['CourseRegistration']['id']) && !empty($regFind['CourseRegistration']['id'])) {
									$resRecorded = ClassRegistry::init('ExamResult')->find('first', array(
										'conditions' => array(
											'ExamResult.course_registration_id' => $regFind['CourseRegistration']['id'],
											'ExamResult.exam_type_id' => $examTypes['ExamType']['id']
										),
										'recursive' => -1
									));
								}

								if (isset($resRecorded['ExamResult']['id']) && !empty($resRecorded['ExamResult']['id'])) {
									$row_data[$count]['ExamResult']['id'] = $resRecorded['ExamResult']['id'];
									$row_data[$count]['ExamResult']['course_registration_id'] = $resRecorded['ExamResult']['course_registration_id'];
									$row_data[$count]['ExamResult']['course_add'] = $resRecorded['ExamResult']['course_add'];
								} else {
									if (isset($regFind['CourseRegistration']['id']) && !empty($regFind['CourseRegistration']['id'])) {
										$row_data[$count]['ExamResult']['course_registration_id'] = $regFind['CourseRegistration']['id'];
									} else if (isset($addFind['CourseAdd']['id']) && !empty($addFind['CourseAdd']['id'])) {
										$row_data[$count]['ExamResult']['course_add_id'] = $addFind['CourseAdd']['id'];
										$row_data[$count]['ExamResult']['course_add'] = 1;
									}
								}


								$row_data[$count]['ExamResult']['exam_type_id'] = $examTypes['ExamType']['id'];
								$row_data[$count]['ExamResult']['result'] = $fieldValue;

								$count++;
							}
						}
					}

					$xls_data = array_merge($xls_data, $row_data);

					if (count($non_valide_rows) == 19) {
						$non_valide_rows[] = "Please check other similar errors in the CSV file you imported.";
						break;
					}
				}


				//invalid rows
				if (count($non_valide_rows) > 0) {
					$row_list = "";
					$this->Flash->error( __('Importing Error!! Please correct the following listed rows in your CSV excel file.'));
					$this->set('non_valide_rows', $non_valide_rows);
					//return;
				}


				if (isset($xls_data) && !empty($xls_data) && count($non_valide_rows) == 0) {
					$result = $xls_data;
				}
			}

			if (isset($result) && !empty($result)) {
				if ($this->ExamResult->saveAll($result, array('validate' => 'first'))) {
					$this->Flash->success('Success. Imported ' . (count($result)) . ' assessement records.');
					return $this->redirect(array('controller' => 'examResults', 'action' => 'add', $this->request->data['ExamResult']['published_course_id']));
				} else {
					$error = $this->ExamResult->invalidFields();
					debug($error);
					$this->Flash->error('Error!! unable to import assessement records.');
				}
			}
		}

		if (!empty($publishedCourses)) {
			$publishedCourses = array("" => "[ Select Course ]") + $publishedCourses;
		}

		$this->set(compact('publishedCourses'));
	}
	
	function download_exam_template($published_course_id = null)
	{
		$this->layout = 'ajax';

		if ($this->Auth->user('id')) {
			$edit = 0;
			$exam_types = array();
			$exam_results = array();
			$students = array();
			$student_adds = array();
			$student_makeup = array();
			$grade_scale = array();
			$display_grade = false;
			$view_only = false;

			if (!empty($published_course_id)) {
				$instructor_id_for_checking = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->field('CourseInstructorAssignment.staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
				//Not to block department head his/her own assigned courses
				$login_instructor_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));
				$assigned_instructor_user_id = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
				$active_account = $this->ExamResult->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));
				//Role based checking is removed
				//if(($this->role_id == 6 || $this->role_id == 5) && $active_account == 1 && $instructor_id_for_checking != $login_instructor_id)
				
				if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
					$view_only = true;
				}

				$exam_types = $this->ExamResult->ExamType->find('all', array(
					'fields' => array('id', 'exam_name', 'percent', 'order'),
					'conditions' => array('ExamType.published_course_id' => $published_course_id),
					'contain' => array(),
					'order' => array('order' => 'ASC'),
					'recursive' => -1
				));

				$section_and_course_detail = $this->ExamResult->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Section', 'Course',
						'CourseInstructorAssignment' => array(
							'conditions' => array('CourseInstructorAssignment.isprimary' => true),
							'Staff' => array('Title', 'Position')
						)
					)
				));

				$section_detail = $section_and_course_detail['Section'];
				$course_detail = $section_and_course_detail['Course'];
				$course_assignment_detail = $section_and_course_detail['CourseInstructorAssignment'];
				$student_course_register_and_adds = $this->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$students_export = array_merge($students, $student_adds, $student_makeup);


				$grade_submission_status = $this->ExamResult->getExamGradeSubmissionStatus($published_course_id, $student_course_register_and_adds);
				$grade_scale = $this->ExamResult->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);

				$gradeStatistics = $this->ExamResult->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);

				$checkIfGradeSubmitted = $this->ExamResult->CourseRegistration->ExamGrade->isGradeSubmittedForPublishedCourse($published_course_id);
			}

			//$days_available_for_grade_change = ClassRegistry::init('AcademicCalendar')->daysAvaiableForGradeChange();
			$days_available_for_grade_change = ClassRegistry::init('GeneralSetting')->daysAvaiableForGradeChange($published_course_id);
			$lastGradeSubmissionDate = ClassRegistry::init('AcademicCalendar')->getPublishedCourseGradeSubmissionDate($published_course_id);


			if (isset($published_course_id) && !empty($published_course_id)) {
				$date_grade_submited = ClassRegistry::init('AcademicCalendar')->getLastGradeChangeDate($published_course_id);
				$date_grade_submited = $lastGradeSubmissionDate;
			} else if (isset($lastGradeSubmissionDate) && !empty($lastGradeSubmissionDate)) {
				$data_grade_submited = $lastGradeSubmissionDate;
			} else {
				$date_grade_submited = date('Y-m-d H:i:s');
			}

			$this->set(compact('grade_scale', 'checkIfGradeSubmitted', 'published_course_id', 'students', 'exam_types', 'exam_results', 'section_detail', 'course_detail', 'course_assignment_detail', 'gradeStatistics', 'student_adds', 'student_makeup', 'display_grade', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'lastGradeSubmissionDate', 'date_grade_submited', 'students_export'));
		} //End of login user cheking
	}

}
