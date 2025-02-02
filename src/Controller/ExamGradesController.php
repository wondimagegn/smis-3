<?php
App::import('Vendor', 'PhpExcelReader/Excel/reader');
class ExamGradesController extends AppController
{

	var $name = 'ExamGrades';
	var $helpers = array('Xls', 'Media.Media');
	var $menuOptions = array(
		'parent' => 'grades',
		'exclude' => array(
			'index', 
			'add', 
			'auto_ng_and_do_to_f', 
			'student_copy',
			'export_mastersheet_xls',
			'export_remedial_mastersheet_xls',
			'view_grade',
			'cancel_fx_resit_request',
			'academic_status_grade_interface',
			'getAddCoursesDataEntry', 
			'getPublishedAddCourses',
			'get_remedial_sections_combo'
		),
		'alias' => array(
			'approve_non_freshman_grade_submission' => 'Approve Grade Submission',
			'approve_freshman_grade_submission' => 'Approve Freshman Grade',
			'manage_ng' => 'Manage NG',
			'student_grade_view' => 'My Grade Report',
			'department_grade_report' => 'Grade Report(D)',
			'freshman_grade_report' => 'Freshman Grade Report(C)',
			'data_entry_interface' => 'Missing Registration & Grade Entry',
			'academic_status_grade_interface' => 'Data Entry with Academic Status',
			'grade_update' => 'Grade Cancellation and Update',
			'request_fx_exam_sit' => 'Request FX Resit Exam',
			'view_fx_resit' => 'View FX resit requests',
			'cancel_ng_grade' => 'Cancel Grade Converted from NG',
			'master_sheet_remedial' => "Remedial Master Sheet",
		)
	);

	var $components = array('EthiopicDateTime', 'AcademicYear', 'Email');

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow(
			'auto_ng_and_do_to_f',
			'export_mastersheet_xls',
			'export_mastersheet_pdf',
			//'getAddCoursesDataEntry',
			//'getPublishedAddCourses',
			'view_xls',
			'view_fx_resit',
			//'master_sheet_remedial',
			'export_remedial_mastersheet_xls',
			'get_remedial_sections_combo',
			'cheating_view'//,
			//'cancel_ng_grade'
		);
	}

	function beforeRender()
	{
		//$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 8, date('Y'));

		$current_academicyear = $defaultacademicyear = $this->AcademicYear->current_academicyear();

		$curr_ac_year_expoded = explode('/', $current_academicyear);

		$previous_academicyear = $current_academicyear;

		if (!empty($curr_ac_year_expoded)) {
			$previous_academicyear =  ($curr_ac_year_expoded[0] - 1) . '/'. ($curr_ac_year_expoded[1] - 1);
		}

		//debug($previous_academicyear);

		$acyear_array_data = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $current_academicyear)[0]));

		//$this->set('defaultacademicyear', $defaultacademicyear);

		$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
		$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1)));

		$depts_for_year_level = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		
		//$yearLevels = $this->year_levels;
		$yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(null , null, array_keys($depts_for_year_level), array_keys($programs));
		//debug($yearLevels);

		/* if ($this->role_id == ROLE_DEPARTMENT) {
			//$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => $yearLevels)));
		} */

		if (($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_COLLEGE) && $this->Session->read('Auth.User')['is_admin'] == 0) {
			$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
			$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));
		}

		//$academicYearRange = new AcademicYearComponent(new ComponentCollection);
		$academicYearRange = new $this->AcademicYear(new ComponentCollection);
		$years_to_look_list_for_display = $academicYearRange->academicYearInArray(((explode('/', $defaultacademicyear)[0]) - ACY_BACK_GRADE_APPROVAL_DASHBOARD), (explode('/', $defaultacademicyear)[0])); 

		if (count($years_to_look_list_for_display) >= 2) {
			// $years_to_look_list_for_display = array_values($years_to_look_list_for_display);
			// $endYear = $years_to_look_list_for_display[0];
			// $startYear = end($years_to_look_list_for_display);
			$startYr = array_pop($years_to_look_list_for_display);
			$endYr = reset($years_to_look_list_for_display);
			$years_to_look_list_for_display = 'from ' . $startYr . ' up to '. $endYr;
		} else if (count($years_to_look_list_for_display) == 1) {
			$years_to_look_list_for_display = ' on ' . $defaultacademicyear;
		} else {
			$years_to_look_list_for_display = '';
		}

		debug($years_to_look_list_for_display);

		$this->set(compact('acyear_array_data', 'defaultacademicyear', 'previous_academicyear', 'program_types', 'programTypes', 'programs', 'yearLevels', 'years_to_look_list_for_display'));

		unset($this->request->data['User']['password']);
	}

	function index()
	{
		//College
		if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/college_grade_view')) {
			return $this->redirect(array('controller' => 'examGrades', 'action' => 'college_grade_view'));
		}
		//Department
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/submit_grade_for_instructor')) {
			return $this->redirect(array('controller' => 'examResults', 'action' => 'submit_grade_for_instructor'));
		}
		//Freshman
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/submit_freshman_grade_for_instructor')) {
			return $this->redirect(array('controller' => 'examResults', 'action' => 'submit_freshman_grade_for_instructor'));
		}
		//Registrar
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/registrar_grade_view')) {
			return $this->redirect(array('controller' => 'examGrades', 'action' => 'registrar_grade_view'));
		}
		//Instructor
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/add')) {
			return $this->redirect(array('controller' => 'examResults', 'action' => 'add'));
		}
		//Student
		else if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/student_grade_view')) {
			return $this->redirect(array('controller' => 'examGrades', 'action' => 'student_grade_view'));
		} 
		// Not Authorized
		else {
			$this->Flash->warning('You are not Authorized to access the page you just selected!');
			return $this->redirect('/');
		}

	}


	function student_copy($student_id = null)
	{
		$student_copy = null;
		$costShares = array();
		$costSharingPayments = array();
		//debug($this->request->data);
		if (!empty($this->request->data['displayStudentCopyPrint']) && !empty($this->request->data['ExamGrade']['id']))
			$student_id = $this->request->data['ExamGrade']['id'];
		if (isset($student_id) || isset($this->request->data['continueStudentCopyPrint'])) {
			//Check the user privilege to print the student copy
			//TODO Check and display about clearance, cost sharing and other payments information
			if (isset($this->request->data['ExamGrade']['studentnumber']) && isset($this->request->data['continueStudentCopyPrint'])) {
				if (trim($this->request->data['ExamGrade']['studentnumber']) == "") {
					$this->Flash->error('Please provide Student ID.');
					return $this->redirect(array('action' => 'student_copy'));
				} else {

					$student_detail = $this->ExamGrade->CourseRegistration->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber' => $this->request->data['ExamGrade']['studentnumber']
						),
						'recursive' => -1
					));

					if (isset($student_detail['Student']['id'])) {
						$costShares = $this->ExamGrade->CourseRegistration->Student->CostShare->find('all', array(
							'conditions' => array(
								'CostShare.student_id' => $student_detail['Student']['id']
							),
							'recursive' => -1,
							'order' => array('CostShare.cost_sharing_sign_date ASC')
						));

						$costSharingPayments = $this->ExamGrade->CourseRegistration->Student->CostSharingPayment->find('all', array(
							'conditions' => array(
								'CostSharingPayment.student_id' => $student_detail['Student']['id']
							),
							'recursive' => -1,
							'order' =>
							array('CostSharingPayment.created ASC')
						));

						$clearances = $this->ExamGrade->CourseRegistration->Student->Clearance->find('all', array(
							'conditions' => array(
								'Clearance.student_id' => $student_detail['Student']['id'],
								'Clearance.type' => 'clearance',
								'Clearance.confirmed' => 1
							),
							'recursive' => -1,
							'order' =>
							array('Clearance.request_date ASC')
						));
					}
				}
			} else {
				$student_detail = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					),
					'recursive' => -1
				));
			}
			
			if (empty($student_detail)) {
				$this->Flash->error('Please provide a valid Student ID.');
				return $this->redirect(array('action' => 'student_copy'));
			} else if ($this->Session->read('Auth.User')['is_admin'] == 0 && ((!empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['department_id'], $this->department_ids)) || (empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['college_id'], $this->college_ids)))) {
				if (!empty($student_detail['Student']['department_id'])) {
					$department_name = $this->ExamGrade->CourseRegistration->Student->Department->field('name', array(
						'Department.id' => $student_detail['Student']['department_id']
					));
					$department_name .= ' Department';
				} else {
					$department_name = $this->ExamGrade->CourseRegistration->Student->College->field('name', array(
						'College.id' => $student_detail['Student']['college_id']
					));
					$department_name .= ' Freshman Program';
				}
				$this->Flash->error('You do not have the privilege to manage ' . $department_name . ' students. Please contact the registrar system administrator to get privilege on ' . $department_name . '.');
				return $this->redirect(array('action' => 'student_copy'));
			} else {
				//Retrieve and pass student cost sharing, clearance, billing and other credits
				$student_ids_array[] = $student_detail['Student']['id'];
				$student_copy = $this->ExamGrade->studentCopy($student_ids_array);
				//($student_copy);
				$student_copy = $student_copy[0];
				if (!isset($student_copy['courses_taken']) || empty($student_copy['courses_taken'])) {
					$this->Flash->error('There is no course a student registered for to display student copy.');
					//$this->redirect(array('action' => 'student_copy'));
				} else if (isset($this->request->data['displayStudentCopyPrint']) && isset($this->request->data['ExamGrade']['id'])) {

					$no_of_semester = $this->request->data['ExamGrade']['no_of_semester'];
					$course_justification = $this->request->data['ExamGrade']['course_justification'];
					$font_size = $this->request->data['ExamGrade']['font_size'];

					if ($course_justification == 2) {
						$course_justification = 0;
					} else if ($course_justification == 0) {
						$course_justification = -2;
					} else {
						$course_justification = -1;
					}

					$student_copies[] = $student_copy;

					$this->set(compact('student_copies', 'no_of_semester', 'course_justification', 'font_size'));

					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('/Elements/student_copy_pdf');
				}
			}
		}

		$font_size_options = array(27 => 'Small 1', 28 => 'Small 2', 29 => 'Small 3', 30 => 'Medium 1', 31 => 'Medium 2', 32 => 'Medium 3', 33 => 'Large 1', 34 => 'Large 2');
		$this->set(compact('student_copy', 'font_size_options', 'costShares', 'costSharingPayments', 'clearances'));
	}


	function mass_student_copy()
	{

		$this->__mass_student_copy(null, null, null, null);
	}

	function __mass_student_copy($program_id = null, $program_type_id = null, $department = null)
	{

		/*
		1. Retrieve list of students based on the given search criteria
		2. Display list of students
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade copy in PDF for the selected students
		*/

		$programs = $this->ExamGrade->CourseRegistration->Student->Program->find('list');
		$program_types = $this->ExamGrade->CourseRegistration->Student->ProgramType->find('list');
		$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids);
		$department_combo_id = null;
		$program_types = array(0 => 'All Program Types') + $program_types;
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;


		//Get list of students who are graduated when a button is clicked
		if (isset($this->request->data['listStudentsForStudentCopy'])) {

			$students_for_mass_student_copy = $this->ExamGrade->CourseRegistration->Student->getStudentListName(
				$this->request->data['ExamGrade']['acadamic_year'],
				$this->request->data['ExamGrade']['program_id'],
				$this->request->data['ExamGrade']['program_type_id'],
				$this->request->data['ExamGrade']['department_id'],
				null,
				$this->request->data['ExamGrade']['studentnumber'],
				$this->request->data['ExamGrade']['name']
			);

			$default_department_id = $this->request->data['ExamGrade']['department_id'];
			$default_program_id = $this->request->data['ExamGrade']['program_id'];
			$default_program_type_id = $this->request->data['ExamGrade']['program_type_id'];
			$academic_year_selected = $this->request->data['ExamGrade']['acadamic_year'];

			$program_id = $this->request->data['ExamGrade']['program_id'];
			$program_type_id = $this->request->data['ExamGrade']['program_type_id'];
		}

		//Get Grade Report button is clicked
		if (isset($this->request->data['getStudentCopy'])) {

			$student_ids = array();

			foreach ($this->request->data['Student'] as $key => $student) {
				if ($student['gp'] == 1) {
					$student_ids[] = $student['student_id'];
				}
			}

			if (empty($student_ids)) {
				$this->Flash->error('You are required to select at least one student.');
			} else {

				$student_copies = $this->ExamGrade->studentCopy($student_ids);

				if (empty($student_copies)) {
					$this->Flash->info('There is no course registration for the selected students to display student copy.');
				} else {

					// debug($student_copies);
					$no_of_semester = $this->request->data['Setting']['no_of_semester'];
					$course_justification = $this->request->data['Setting']['course_justification'];
					$font_size = $this->request->data['Setting']['font_size'];

					if ($course_justification == 2) {
						$course_justification = 0;
					} else if ($course_justification == 0) {
						$course_justification = -2;
					} else {
						$course_justification = -1;
					}


					$this->set(compact('student_copies', 'no_of_semester', 'course_justification', 'font_size'));
					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('/Elements/student_copy_pdf');
				}
			}
		}

		$font_size_options = array(27 => 'Small 1', 28 => 'Small 2', 29 => 'Small 3', 30 => 'Medium 1', 31 => 'Medium 2', 32 => 'Medium 3', 33 => 'Large 1', 34 => 'Large 2');
		
		$this->set(compact(
			'departments',
			'program_types',
			'programs',
			'default_program_type_id',
			'font_size_options',
			'students_for_mass_student_copy',
			'default_program_id',
			'default_department_id'
		));
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Session->setFlash(__('Invalid exam grade'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('examGrade', $this->ExamGrade->read(null, $id));
	}

	function auto_ng_and_do_to_f()
	{
		$privilaged_registrar = array();

		$all_users = ClassRegistry::init('User')->find('all', array(
			'conditions' => array(
				'User.role_id' => array(ROLE_REGISTRAR, ROLE_COLLEGE, ROLE_DEPARTMENT, ROLE_INSTRUCTOR),
				'User.active' => 1
			),
			'contain' => array('StaffAssigne')
		));

		if (!empty($all_users)) {
			foreach ($all_users as $key => $user) {
				if ($this->Acl->check($user, 'controllers/examGrades/registrar_grade_view')) {
					$privilaged_registrar[] = $user;
				}
			}

			if (!empty($privilaged_registrar)) {
				$this->ExamGrade->ExamGradeChange->autoNgAndDoConversion($privilaged_registrar);
			} else {
				return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
			}
		}
	}

	function manage_ng($published_course_id = null)
	{

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

			$published_course_combo_id = null;
			$department_combo_id = null;
			$publishedCourses = array();
			$students_with_ng = array();
			$have_message = false;
			$privilaged_registrar = array();
			
			$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
			$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));

			$departments = array();
			$colleges = array();
			$only_pre_assigned = 0;
			
			if (!empty($this->department_ids)) {
				$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
			} else if (!empty($this->college_ids)) {
				//$colleges = $this->ExamGrade->CourseRegistration->Student->College->find('list', array('College.id' => $this->college_ids, 'College.active' => 1));
				if ($this->onlyPre) {
					$only_pre_assigned = 1;
					$departments = $this->ExamGrade->CourseRegistration->Student->Department->onlyFreshmanInAllColleges($this->college_ids, 1);
				} else {
					$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
				}
			}

			if ($this->Session->read('Auth.User')['is_admin'] == 1) {
				$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids, $includePre = 1, $only_active = 1);
			}

			debug($this->request->data);

			if (!empty($published_course_id)) {

				$published_course_details = ClassRegistry::init('PublishedCourse')->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
					),
					'contain' => array(
						'Department' => array(
							'fields' => array('id', 'college_id'),
						),
						'GivenByDepartment' => array(
							'fields' => array('id', 'college_id'),
						),
						'CourseInstructorAssignment' => array(
							'Staff',
						),
						'Course' => array('id', 'course_title', 'course_code', 'course_code_title'),
						'Section' => array('id', 'name', 'academicyear'),
						'CourseRegistration' => array(
							'ExamGrade' => array('id', 'grade'),
							'limit' => 1
						),
						'CourseAdd' => array(
							'ExamGrade' => array('id', 'grade'),
							'limit' => 1
						),
					)
				));

				debug($published_course_details);

				//if (empty($published_course_details['PublishedCourse']['CourseRegistration']) && empty($published_course_details['PublishedCourse']['CourseAdd'])) {
					//$this->Flash->info('The course ' . $published_course_details['PublishedCourse']['Course']['course_code_title'] . 'published for ' . $published_course_details['PublishedCourse']['Section']['name'] . '(' . $published_course_details['PublishedCourse']['Section']['academicyear'] . ')' . ' doesnt have any registration or add please select an other published course.' );
					//return $this->redirect(array('action' => 'manage_ng'/* , $published_course_id */));
				//} else if ((!isset($published_course_details['PublishedCourse']['CourseRegistration'][0]['ExamGrade']) || !isset($published_course_details['PublishedCourse']['CourseRegistration'][0]['ExamGrade'][0])) && (!isset($published_course_details['PublishedCourse']['CourseAdd'][0]['ExamGrade']) || !isset($published_course_details['PublishedCourse']['CourseAdd'][0]['ExamGrade'][0]))) {
					//$this->Flash->info('The course ' . $published_course_details['PublishedCourse']['Course']['course_code_title'] . 'published for ' . $published_course_details['PublishedCourse']['Section']['name'] . '(' . $published_course_details['PublishedCourse']['Section']['academicyear'] . ')' . ' doesnt have any grade submission or add please select an other published course.' );
					//return $this->redirect(array('action' => 'manage_ng'/* , $published_course_id */));
				//}

				$deptID = array();
				$collID = array();
				$user_ids_to_look = array();
				$all_users = array();

				if (!empty($published_course_details['PublishedCourse']['given_by_department_id'])) {
					$deptID[] = $published_course_details['PublishedCourse']['given_by_department_id'];
					if (isset($published_course_details['GivenByDepartment']['college_id']) && !empty($published_course_details['GivenByDepartment']['college_id']) && is_numeric($published_course_details['GivenByDepartment']['college_id']) && $published_course_details['GivenByDepartment']['college_id']) {
						$collID[] = $published_course_details['GivenByDepartment']['college_id'];
					}
				} 

				if (!empty($published_course_details['PublishedCourse']['department_id'])) {
					$deptID[] = $published_course_details['PublishedCourse']['department_id'];
					if (isset($published_course_details['Department']['college_id']) && !empty($published_course_details['Department']['college_id']) && is_numeric($published_course_details['Department']['college_id']) && $published_course_details['Department']['college_id'] > 0) {
						$collID[] = $published_course_details['Department']['college_id'];
					}
				}

				if (!empty($published_course_details['PublishedCourse']['college_id']) && is_numeric($published_course_details['PublishedCourse']['college_id']) && $published_course_details['PublishedCourse']['college_id'] > 0) {
					$collID[] = $published_course_details['PublishedCourse']['college_id'];
				}
				
				if (!empty($deptID)) {

					$department_heads = ClassRegistry::init('User')->find('list', array(
						'conditions' => array(
							'User.id IN (SELECT user_id FROM staffs WHERE department_id IN (' . (implode(',', $deptID)). ') AND active = 1)',
							'User.active' => 1,
							'User.is_admin' => 1,
							'User.role_id' => ROLE_DEPARTMENT,
						), 
						'fields' => array('User.id', 'User.id')
					));

					if (!empty($department_heads)) {
						$user_ids_to_look = $department_heads;
					}
				}

				if (!empty($collID)) {

					$college_deans = ClassRegistry::init('User')->find('list', array(
						'conditions' => array(
							'User.id IN (SELECT user_id FROM staffs WHERE college_id IN (' . (implode(',', $collID)). ') AND active = 1)',
							'User.active' => 1,
							'User.is_admin' => 1,
							'User.role_id' => ROLE_COLLEGE,
						), 
						'fields' => array('User.id', 'User.id')
					));

					if (!empty($college_deans)) {
						//debug($college_deans);
						$user_ids_to_look = $user_ids_to_look + $college_deans;
					}
				}

				$user_ids_to_look[$this->Session->read('Auth.User')['id']] = $this->Session->read('Auth.User')['id'];

				/* if (isset($published_course_details['Staff']['user_id']) && !empty($published_course_details['Staff']['user_id'])) {
					debug($published_course_details['Staff']['user_id']);
					$user_ids_to_look[$published_course_details['Staff']['user_id']] = $published_course_details['Staff']['user_id'];
				} */

				debug($user_ids_to_look);

				if (!empty($user_ids_to_look)) {
					$all_users = ClassRegistry::init('User')->find('all', array(
						'conditions' => array(
							'User.id' => $user_ids_to_look,
						),
						'contain' => array('StaffAssigne')
					));
				} 

				//debug($all_users);

				if (!empty($all_users)) {
					foreach ($all_users as $key => $user) {
						$privilaged_registrar[] = $user;
					}
				}

				//debug($privilaged_registrar);

			}

			//List published course button is clicked
			if (isset($this->request->data['listPublishedCourses'])) {
				//There is nothing to do here for the time being
				
			} else if (isset($this->request->data['changeNgGrade'])) {
				//Change NG Grade button is clicked
				if (trim($this->request->data['ExamGrade']['minute_number']) == "") {
					$this->Flash->error('Please enter minute number.');
				} else {

					$check1 = 1;
					$check2 = 1;

					if ($this->Session->read('Auth.User')['is_admin'] != 1) {

						if (!empty($this->department_ids)) {
							$check1 = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
								'conditions' => array(
									'PublishedCourse.id' => $published_course_id,
									'OR' => array(
										'PublishedCourse.given_by_department_id' => $this->department_ids,
										'PublishedCourse.department_id' => $this->department_ids
									)
								)
							));
						}

						if (!empty($this->college_ids)) {
							$check2 = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
								'conditions' => array(
									'PublishedCourse.id' => $published_course_id,
									'PublishedCourse.college_id' => $this->college_ids
								))
							);
						}
					}

					if ($check1 == 0 || $check2 == 0) {
						$this->Flash->error('You are not authorized to manage the selected NG grades.');
						$this->redirect('/');
					}

					debug($privilaged_registrar);

					$exam_grade_changes = array();

					if (isset($this->request->data['ExamGrade']) && !empty($this->request->data['ExamGrade'])) {
						//debug($this->request->data['ExamGrade']);
						foreach ($this->request->data['ExamGrade'] as $key => $grade_change) {
							if (is_array($grade_change) && $grade_change['grade'] != "") {
								$exam_grade_changes[] = $grade_change;
							}
						}
					}
					
					debug($exam_grade_changes);
					//debug($this->Session->read('Auth.User')['full_name']);

					//exit();

					if (empty($exam_grade_changes)) {
						$this->Flash->error('You are required to select at least one student NG grade change.');
					} else {
						if ($this->ExamGrade->ExamGradeChange->applyManualNgConversion($exam_grade_changes, trim($this->request->data['ExamGrade']['minute_number']), $this->Session->read('Auth.User')['id'], $privilaged_registrar, $this->Session->read('Auth.User')['full_name'])) {
							$have_message = true;
							$this->Flash->success('NG exam grade change for ' . count($exam_grade_changes) . ' student grades was successful.');
							if (!empty($published_course_id)) {
								return $this->redirect(array('action' => 'manage_ng', $published_course_id));
							}
							return $this->redirect(array('action' => 'manage_ng'));
						} else {
							$this->Flash->error('NG exam grade change is not successful for the selected students. Please try again.');
						}
					}
				}
			}

			if (!empty($this->request->data) && isset($this->request->data['listPublishedCourses'])) {
				
				$department_id = $this->request->data['ExamGrade']['department_id'];

				$this->request->data['ExamGrade']['published_course_id'] = null;
				$published_course_id = null;
				$department_combo_id = $department_id;

				$college_id = explode('~', $department_id);
				
				if (is_array($college_id) && count($college_id) > 1) {
					$college_id = $college_id[1];
					$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($college_id, $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id']);
				} else {
					$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id']);
				}

				debug($publishedCourses);

				if (empty($publishedCourses)) {
					if ($this->onlyPre || (count(explode('~', $department_id)) > 1)) {
						$collegessss = $this->ExamGrade->CourseRegistration->Student->College->find('list');
						$this->Flash->info('No published course is found under ' . $collegessss[$college_id] . ' with the selected search criteria.');
					} else {
						$departmentssss = $this->ExamGrade->CourseRegistration->Student->Department->find('list');
						$this->Flash->info('No published course is found under ' . $departmentssss[$department_id] . ' with the selected search criteria.');
					}
					return $this->redirect(array('action' => 'manage_ng'));
				} else {
					$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
				}
			}

			//When published course is selected from the combo box
			if (!empty($published_course_id) || (isset($this->request->data['ExamGrade']['published_course_id']) && $this->request->data['ExamGrade']['published_course_id'] != 0)) {
				
				if (isset($this->request->data['ExamGrade']['published_course_id'])) {
					$published_course_id = $this->request->data['ExamGrade']['published_course_id'];
				}

				$publishedCourses = array();

				$published_course = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Course', 'Section')));

				if ($this->Session->read('Auth.User')['is_admin'] != 1 && (empty($published_course) || (!empty($published_course['PublishedCourse']['department_id']) && !in_array($published_course['PublishedCourse']['department_id'], $this->department_ids)) || (!empty($published_course['PublishedCourse']['college_id']) && !in_array($published_course['PublishedCourse']['college_id'], $this->college_ids)))) {
					if (empty($published_course)) {
						$this->Flash->error('Please select a valid published course.');
					} else {
						$this->Flash->error('Your are not authorized to manage the selected published course.');
					}
					return $this->redirect(array('action' => 'manage_ng'));
				} else if (empty($published_course)) {
					$this->Flash->error('Please select a valid published course.');
					return $this->redirect(array('action' => 'manage_ng'));
				} else {
					if (empty($published_course['PublishedCourse']['department_id'])) {
						$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($published_course['PublishedCourse']['college_id'], $published_course['PublishedCourse']['academic_year'], $published_course['PublishedCourse']['semester'], $published_course['PublishedCourse']['program_id'], $published_course['PublishedCourse']['program_type_id']);
						$department_combo_id = 'c~' . $published_course['PublishedCourse']['college_id'];
					} else {
						$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($published_course['PublishedCourse']['department_id'], $published_course['PublishedCourse']['academic_year'], $published_course['PublishedCourse']['semester'], $published_course['PublishedCourse']['program_id'], $published_course['PublishedCourse']['program_type_id']);
						$department_combo_id = $published_course['PublishedCourse']['department_id'];
					}
				}

				$published_course_combo_id = $published_course_id;
				$students_with_ng = $this->ExamGrade->getStudentsWithNG($published_course_id);
				//debug($students_with_ng);

				if (empty($students_with_ng)) {
					if ($have_message == false) {
						$this->Flash->info('There is no student with NG garde for ' . $published_course['Course']['course_code_title']. ' course from ' . $published_course['Section']['name'] . ' section.');
					}
				}

				$program_id = $published_course['PublishedCourse']['program_id'];
				$program_type_id = $published_course['PublishedCourse']['program_type_id'];
				$department_id = (isset($published_course['PublishedCourse']['college_id']) && !empty($published_course['PublishedCourse']['college_id']) ? 'c~' . $published_course['PublishedCourse']['college_id'] : $published_course['PublishedCourse']['department_id']);
				$college_id = (isset($published_course['PublishedCourse']['college_id']) ? $published_course['PublishedCourse']['college_id'] : NULL);
				$academic_year_selected = $published_course['PublishedCourse']['academic_year'];
				$semester_selected = $published_course['PublishedCourse']['semester'];

				$this->request->data['ExamGrade']['department_id'] = $department_id;

			}

			$applicable_grades = array(
				'' => '[ Select Grade ]', 
				'I' => 'I (Incomplete)', 
				'DO' => 'DO (Dropout)', 
				'W' => 'W (Withdraw)', 
				'F' => 'F'
			); 

			$this->set(compact('publishedCourses', 'programs', 'program_types', 'departments', 'publishedCourses', 'published_course_combo_id', 'department_combo_id', 'students_with_ng', 'applicable_grades', 'program_id', 'program_type_id',  'department_id', 'college_id', 'academic_year_selected', 'semester_selected', 'only_pre_assigned'));

		} else {
			$this->Flash->error('You are not authorized to manage NG grades.');
			return $this->redirect('/');
		}
		
	}


	function manage_fx($published_course_id = null)
	{
		$published_course_combo_id = null;
		$department_combo_id = null;
		$publishedCourses = array();
		$students_with_ng = array();
		$have_message = false;

		//$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list');
		//$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
		$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));


		if (!empty($this->department_ids) || !empty($this->college_ids)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids);
		} else {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array(
				'conditions' => array('Department.id' => $this->department_id),
				'recursive' => -1
			));
		}
		
		//List published course button is clicked
		if (isset($this->request->data['listPublishedCourses'])) {
			//There is nothing to do here for the time being
		} else if (!empty($this->request->data['ExamGrade']) && isset($this->request->data['changeNgGrade'])) {
			//Change NG Grade button is clicked
			//debug($this->request->data);
			
			if (trim($this->request->data['ExamGrade']['minute_number']) == "") {
				$this->Flash->error( __('Please enter minute number.'));
			} else {
				$exam_grade_changes = array();
				foreach ($this->request->data['ExamGrade'] as $key => $grade_change) {
					if (is_array($grade_change) && $grade_change['grade'] != "") {
						$exam_grade_changes[] = $grade_change;
					}
				}
				if (empty($exam_grade_changes)) {
					$this->Flash->error( __('You are required to apply at least one grade change.'));
				} else {

					$privilaged_registrar = array();

					$all_users = ClassRegistry::init('User')->find('all', array(
						'conditions' => array(
							'User.role_id' => array(ROLE_REGISTRAR, ROLE_COLLEGE, ROLE_DEPARTMENT, ROLE_INSTRUCTOR),
							'User.active' => 1
						),
						'contain' => array('StaffAssigne')
					));

					if (!empty($all_users)) {
						foreach ($all_users as $key => $user) {
							if ($this->Acl->check($user, 'controllers/examGrades/registrar_grade_view')) {
								$privilaged_registrar[] = $user;
							}
						}
					}

					if (!$this->ExamGrade->ExamGradeChange->applyManualFxConversion($exam_grade_changes, trim($this->request->data['ExamGrade']['minute_number']), $this->Auth->user('id'), $privilaged_registrar)) {
						$this->Flash->error(__('Fx exam grade change is not done for the selected students. Please try again.'));
					} else {
						$have_message = true;
						$this->Flash->success(__('Fx exam grade change for ' . count($exam_grade_changes) . ' student/s is successfully done.'));
					}
				}
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['listPublishedCourses'])) {
			
			$department_id = $this->request->data['ExamGrade']['department_id'];
			$this->request->data['ExamGrade']['published_course_id'] = null;
			$published_course_id = null;
			$department_combo_id = $department_id;
			$college_id = explode('~', $department_id);

			$registrar = ($this->role_id == ROLE_REGISTRAR) ? 1 : 0;

			if (is_array($college_id) && count($college_id) > 1) {
				$college_id = $college_id[1];
				$publishedCourses = $this->ExamGrade->CourseRegistration->listOfCoursesWithFx($college_id, $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id'], 1, $registrar);
			} else {
				$publishedCourses = $this->ExamGrade->CourseRegistration->listOfCoursesWithFx($department_id, $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id'], 0, $registrar);
			}

			if (empty($publishedCourses)) {
				$this->Flash->info(__('There is no published course has Fx with the selected filter criteria'));
				return $this->redirect(array('action' => 'manage_fx'));
			} else {
				$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
			}
		}

		//When published course is selected from the combo box
		if (!empty($published_course_id) || (isset($this->request->data['ExamGrade']['published_course_id']) && $this->request->data['ExamGrade']['published_course_id'] != 0)) {
			
			if (isset($this->request->data['ExamGrade']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamGrade']['published_course_id'];
			}
				
			$publishedCourses = array();

			$published_course = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Section')));

			if (empty($published_course) || (!empty($published_course['PublishedCourse']['department_id']) && !in_array($published_course['PublishedCourse']['department_id'], $this->department_ids) && $this->role_id == ROLE_REGISTRAR) || (!empty($published_course['PublishedCourse']['college_id']) && !in_array($published_course['PublishedCourse']['college_id'], $this->college_ids) && $this->role_id == ROLE_REGISTRAR) || (!empty($published_course['PublishedCourse']['given_by_department_id']) && $this->department_id != $published_course['PublishedCourse']['given_by_department_id'] && $this->role_id != ROLE_REGISTRAR)) {
				$this->Flash->info(__('Please select a valid published course.'));
				//return $this->redirect(array('action' => 'manage_fx'));
			} else {

				if (empty($published_course['PublishedCourse']['department_id'])) {

					//$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($published_course['PublishedCourse']['college_id'], $published_course['PublishedCourse']['academic_year'], $published_course['PublishedCourse']['semester'], $published_course['PublishedCourse']['program_id'], $published_course['PublishedCourse']['program_type_id']);
					
					if (!empty($published_course['PublishedCourse']['given_by_department_id']) || !empty($published_course['PublishedCourse']['department_id'])) {

						$deptID = (!empty($published_course['PublishedCourse']['given_by_department_id']) ? $published_course['PublishedCourse']['given_by_department_id'] : $published_course['PublishedCourse']['department_id']);
						
						$publishedCourses = $this->ExamGrade->CourseRegistration->listOfCoursesWithFx(
							$deptID, //$published_course['PublishedCourse']['given_by_department_id'],
							$published_course['PublishedCourse']['academic_year'],
							$published_course['PublishedCourse']['semester'],
							$published_course['PublishedCourse']['program_id'],
							$published_course['PublishedCourse']['program_type_id'],
							0
						);
						$department_combo_id = $deptID;
					} else if (empty($published_course['PublishedCourse']['department_id'])) {
						$publishedCourses = $this->ExamGrade->CourseRegistration->listOfCoursesWithFx(
							$published_course['PublishedCourse']['college_id'],
							$published_course['PublishedCourse']['academic_year'],
							$published_course['PublishedCourse']['semester'],
							$published_course['PublishedCourse']['program_id'],
							$published_course['PublishedCourse']['program_type_id'],
							0
						);
						$department_combo_id = 'c~' . $published_course['PublishedCourse']['college_id'];
					}
				} else {
					// $publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($published_course['PublishedCourse']['department_id'], $published_course['PublishedCourse']['academic_year'], $published_course['PublishedCourse']['semester'], $published_course['PublishedCourse']['program_id'], $published_course['PublishedCourse']['program_type_id']);
					$publishedCourses = $this->ExamGrade->CourseRegistration->listOfCoursesWithFx(
						$published_course['PublishedCourse']['given_by_department_id'], 
						$published_course['PublishedCourse']['academic_year'], 
						$published_course['PublishedCourse']['semester'], 
						$published_course['PublishedCourse']['program_id'], 
						$published_course['PublishedCourse']['program_type_id'], 
						0
					);
					$department_combo_id = $published_course['PublishedCourse']['department_id'];
				}
			}

			$published_course_combo_id = $published_course_id;

			$students_with_ng = $this->ExamGrade->getStudentsWithFX($published_course_id);

			if (empty($students_with_ng)) {
				if ($have_message == false) {
					$this->Flash->info(__('There is no student with Fx for the selected course.'));
				}
			}

			$program_id = $published_course['PublishedCourse']['program_id'];
			$program_type_id = $published_course['PublishedCourse']['program_type_id'];
			$department_id = $published_course['PublishedCourse']['department_id'];
			$academic_year_selected = $published_course['PublishedCourse']['academic_year'];
			$semester_selected = $published_course['PublishedCourse']['semester'];
		}

		// $applicable_grades = array('' => '[ Select Grade ]', 'I' => 'I (Incomplete)', 'DO' => 'DO (Dropout)', 'W' => 'W (Withdraw)');
		
		$applicable_grades = array('' => '[ Select Grade ]');

		if (!empty($published_course_id)) {
			$grades = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'contain' => array('Course' => array('GradeType' => ('Grade')))));
			foreach ($grades['Course']['GradeType']['Grade'] as $k => $v) {
				$applicable_grades[$v['grade']] = $v['grade'];
			}
		}

		$this->set(compact('publishedCourses', 'programs', 'program_types', 'departments', 'publishedCourses', 'published_course_combo_id', 'department_combo_id', 'students_with_ng', 'applicable_grades', 'program_id', 'program_type_id', 'department_id', 'academic_year_selected', 'semester_selected'));
	}

	function add()
	{
		if (!empty($this->request->data)) {
			$this->ExamGrade->create();
			if ($this->ExamGrade->save($this->request->data)) {
				$this->Flash->success('The exam grade has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The exam grade could not be saved. Please, try again.'));
			}
		}

		$courseRegistrations = $this->ExamGrade->CourseRegistration->find('list');
		$makeupExams = $this->ExamGrade->MakeupExam->find('list');
		$courseAdds = $this->ExamGrade->CourseAdd->find('list');

		$this->set(compact('courseRegistrations', 'makeupExams', 'courseAdds'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__('Invalid exam grade'));
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			if ($this->ExamGrade->save($this->request->data)) {
				$this->Flash->success(__('The exam grade has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The exam grade could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->ExamGrade->read(null, $id);
		}
		$courseRegistrations = $this->ExamGrade->CourseRegistration->find('list');
		//$makeupExams = $this->ExamGrade->MakeupExam->find('list');
		$courseAdds = $this->ExamGrade->CourseAdd->find('list');
		$this->set(compact('courseRegistrations', 'makeupExams','courseAdds'));

	}

	function delete($id = null, $action_controller_id = null)
	{
		if (!empty($action_controller_id)) {
			$exam_grade = explode('~', $action_controller_id);
		}

		$this->ExamGrade->id = $id;

		if (!$this->ExamGrade->exists()) {
			$this->Flash->error('Invalid id for exam grade');
			if (!empty($exam_grade[0]) && !empty($exam_grade[1]) && !empty($exam_grade[2])) {
				$this->redirect(array('controller' => $exam_grade[1], 'action' => $exam_grade[0], $exam_grade[2]));
			} elseif (!empty($exam_grade[0]) && !empty($exam_grade[1])) {
				$this->redirect(array('controller' => $exam_grade[1], 'action' => $exam_grade[0]));
			}
			return $this->redirect(array('action' => 'index'));
		}

		//TODO: CHeck grade is not approved by department
		// it true, call function in here to return true or false to allow deletion.
		$check_not_involved_approved_by_department = $this->ExamGrade->find('count', array(
			'conditions' => array(
				'ExamGrade.id' => $id, 
				'ExamGrade.registrar_approval is null',
				'ExamGrade.department_approval is null'
			)
		));

		if ($check_not_involved_approved_by_department == 0) {
			if ($this->ExamGrade->delete($id)) {
				$this->Flash->success('Exam grade deleted.');
				if (!empty($exam_grade[0]) && !empty($exam_grade[1]) && !empty($exam_grade[2])) {
					$this->redirect(array('controller' => $exam_grade[1], 'action' => $exam_grade[0], $exam_grade[2]));
				} elseif (!empty($exam_grade[0]) && !empty($exam_grade[1])) {
					$this->redirect(array('controller' => $exam_grade[1], 'action' => $exam_grade[0]));
				}
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->Flash->error('Exam grade is not deleted.');
		return $this->redirect(array('action' => 'index'));
	}

	function approve_freshman_grade_submission($published_course_id = null)
	{
		$this->__approve_grade_submission($published_course_id, 0);
		$this->render('approve_grade_submission');
	}

	function approve_non_freshman_grade_submission($published_course_id = null)
	{
		$this->__approve_grade_submission($published_course_id, 1);
		$this->render('approve_grade_submission');
	}

	/* 

	private function __approve_grade_submission($published_course_id = null, $department = 1)
	{
		
		//check the published course belongs the department
		if ($published_course_id != "") {
			if ($department) {
				$check = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
						'PublishedCourse.department_id' => $this->department_id
					)
				));
			} else {
				$check = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
						'PublishedCourse.college_id' => $this->college_id
					)
				));
			}
			if ($check == 0) {
				$this->Flash->error('You are not eligible to approve the selected course.');
				//$this->redirect(array('controller'=>'dashboard','action'=>'index'));
			} else {
				//get list of students with grade

				$get_list_of_students_with_grade = $this->ExamGrade->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				
				$publishedCourseDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array(
					'fields' => array('id', 'academic_year', 'semester'),
					'conditions' => array('PublishedCourse.id' => $published_course_id),
					'contain' => array(
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'Section' => array('id', 'name'),
						'YearLevel' => array('id', 'name'),
						'Department' => array('id', 'name'),
						'College' => array('id', 'name'),
						'Course' => array('id', 'course_title', 'course_code', 'credit'),
						'CourseInstructorAssignment' => array('Staff')
					)
				));

				$hide_approve_list = true;
				$search_published_course = true;
				$gradeScaleDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$instructorDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->getInstructorDetailGivingPublishedCourse($published_course_id);
				
				$exam_types = $this->ExamGrade->CourseRegistration->ExamResult->ExamType->find('all', array(
					'fields' => array('id', 'exam_name', 'percent', 'order'),
					'conditions' => array('ExamType.published_course_id' => $published_course_id),
					'contain' => array(),
					'order' => array('order ASC'),
					'recursive' => -1
				));

				$this->set(compact(
					'get_list_of_students_with_grade',
					'hide_approve_list',
					'search_published_course',
					'gradeScaleDetail',
					'instructorDetail',
					'publishedCourseDetail',
					'exam_types'
				));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['approvegradesubmission'])) {
			
			$approval = $this->request->data['ExamGrade']['department_approval'];
			$reason = $this->request->data['ExamGrade']['department_reason'];
			unset($this->request->data['ExamGrade']['department_approval']);
			unset($this->request->data['ExamGrade']['department_reason']);

			$reformat_approve_grade = array();
			$count = 0;
			$any_exam_grade_id = "";

			if (!empty($this->request->data['ExamGrade'])) {

				foreach ($this->request->data['ExamGrade'] as $exam_grade_key => $exam_grade_value) {
					
					$exam_grade_detail = $this->ExamGrade->find('first', array(
						'conditions' => array('ExamGrade.id' => $exam_grade_value['id']),
						'recursive' => -1
					));

					if ($exam_grade_detail['ExamGrade']['registrar_approval'] == -1) {
						$any_exam_grade_id = $exam_grade_detail['ExamGrade']['id'];
						
						unset($exam_grade_detail['ExamGrade']['id']);
						unset($exam_grade_detail['ExamGrade']['registrar_approval']);
						unset($exam_grade_detail['ExamGrade']['registrar_reason']);
						unset($exam_grade_detail['ExamGrade']['registrar_approval_date']);
						unset($exam_grade_detail['ExamGrade']['registrar_approved_by']);
						unset($exam_grade_detail['ExamGrade']['created']);
						unset($exam_grade_detail['ExamGrade']['modified']);

						$exam_grade_detail['ExamGrade']['department_reply'] = 1;
						$exam_grade_detail['ExamGrade']['department_approval'] = $approval;
						$exam_grade_detail['ExamGrade']['department_reason'] = $reason;
						$exam_grade_detail['ExamGrade']['department_approval_date'] = date('Y-m-d H:i:s');
						$exam_grade_detail['ExamGrade']['department_approved_by'] = $this->Auth->user('id');
						$reformat_approve_grade['ExamGrade'][$count] = $exam_grade_detail['ExamGrade'];

					} else {

						$any_exam_grade_id = $exam_grade_value['id'];
						$reformat_approve_grade['ExamGrade'][$count]['id'] = $exam_grade_value['id'];
						$reformat_approve_grade['ExamGrade'][$count]['department_approval'] = $approval;
						$reformat_approve_grade['ExamGrade'][$count]['department_reason'] = $reason;
						$reformat_approve_grade['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
						$reformat_approve_grade['ExamGrade'][$count]['department_approval_date'] = date('Y-m-d H:i:s');

					}

					$count++;
				}
			} else {
				$this->Flash->error('No grade is selected for approval. Please, try again.');
			}

			//saveAll
			if ($this->ExamGrade->saveAll($reformat_approve_grade['ExamGrade'], array('validate' => false))) {
				//Instructor notification
				
				$course_instructor = $this->ExamGrade->CourseRegistration->PublishedCourse->getInstructorByExamGradeId($any_exam_grade_id);
				$course = $this->ExamGrade->CourseRegistration->PublishedCourse->Course->getCourseByExamGradeId($any_exam_grade_id);
				$section = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->getSectionByExamGradeId($any_exam_grade_id);
				$published_course = $this->ExamGrade->CourseRegistration->PublishedCourse->getPublishedCourseByExamGradeId($any_exam_grade_id);
				
				if (!empty($course_instructor) && $course_instructor['user_id'] != "") {
					$auto_message['AutoMessage']['message'] = 'Your <u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> grade submission is ' . ($approval == 1 ? 'approved' : 'rejected') . ' by the ' . ($department == 1 ? 'department' : 'freshman program') . ' for <u>' . ($section['name']) . '</u> section. <a href="/exam_results/add/' . $published_course['id'] . '">View Grade</a>';
					
					if ($approval == -1) {
						$auto_message['AutoMessage']['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message['AutoMessage']['message'] . '</p>';
					} else if ($approval == 1) {
						$auto_message['AutoMessage']['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message['AutoMessage']['message'] . '</p>';
					}

					$auto_message['AutoMessage']['read'] = 0;
					$auto_message['AutoMessage']['user_id'] = $course_instructor['user_id'];

					ClassRegistry::init('AutoMessage')->save($auto_message);
				}

				$this->Flash->success('The exam grade has been approved. The system will notify registrar to confirm the result.');
				$this->redirect(array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission')));

			} else {
				$this->Flash->error('The exam grade approval could not be completed. Please, try again.');
			}
		}

		// always show the latest grade submitted but required department approval.
		if ($department == 1) {
			$published_course_list_student_registered = $this->ExamGrade->CourseRegistration->PublishedCourse->find('list', array(
				'conditions' => array(
					'PublishedCourse.drop' => 0,
					'PublishedCourse.department_id' => $this->department_id,
					'(PublishedCourse.id in (select published_course_id from course_registrations) or  PublishedCourse.id in (select published_course_id from course_adds))'
				),
				'fields' => array('PublishedCourse.id')
			));
		} else {
			$published_course_list_student_registered = $this->ExamGrade->CourseRegistration->PublishedCourse->find('list', array(
				'conditions' => array(
					'PublishedCourse.drop' => 0,
					'PublishedCourse.college_id' => $this->college_id,
					'(PublishedCourse.id in (select published_course_id from course_registrations) or PublishedCourse.id in (select published_course_id from course_adds))'
				),
				'fields' => array('PublishedCourse.id')
			));
		}

		$published_courses_student_registred_score_grade = $this->ExamGrade->CourseRegistration->find('all', array(
			'fields' => array('id', 'published_course_id'),
			'conditions' => array(
				'CourseRegistration.published_course_id' => $published_course_list_student_registered,
				'CourseRegistration.id in (select course_registration_id from exam_grades  where department_approval is null)'
			),
			'contain' => array(
				'PublishedCourse' => array(
					'fields' => array('id', 'semester', 'section_id'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'Section' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'CourseInstructorAssignment' => array(
						'fields' => array('id', 'published_course_id', 'staff_id'),
						'Staff' => array('id', 'full_name', 'user_id'),
						'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
					),
					'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
				)
			)
		));

		$published_courses_student_add_score_grade = $this->ExamGrade->CourseAdd->find('all', array(
			'fields' => array('id', 'published_course_id'),
			'conditions' => array(
				'CourseAdd.published_course_id' => $published_course_list_student_registered,
				'CourseAdd.id in (select course_add_id from exam_grades  where department_approval is null)'
			),
			'contain' => array(
				'PublishedCourse' => array(
					'fields' => array('id', 'semester', 'section_id'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'Section' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'CourseInstructorAssignment' => array(
						'fields' => array('id', 'published_course_id', 'staff_id'),
						'Staff' => array('id', 'full_name', 'user_id'),
						'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
					),
					'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
				)
			)
		));
		//debug($published_courses_student_add_score_grade);
		//debug($published_courses_student_registred_score_grade);exit();

		$merged_courses_student_registered_and_add = array();

		if (!empty($published_courses_student_registred_score_grade)) {
			foreach ($published_courses_student_registred_score_grade as $key => $registered_student) {
				if (!empty($merged_courses_student_registered_and_add)) {
					foreach ($merged_courses_student_registered_and_add as $key2 => $merged_studnet) {
						if ((isset($merged_studnet['CourseRegistration']) && $registered_student['CourseRegistration']['published_course_id'] == $merged_studnet['CourseRegistration']['published_course_id']) || (isset($merged_studnet['CourseAdd']) && $registered_student['CourseRegistration']['published_course_id'] == $merged_studnet['CourseAdd']['published_course_id'])) {
							//debug($registered_student);
							//debug($merged_studnet);
							break;
						}
					}
				}
				$merged_courses_student_registered_and_add[] = $registered_student;
			}
		}

		if (!empty($published_courses_student_add_score_grade)) {
			foreach ($published_courses_student_add_score_grade as $key => $added_student) {
				if (!empty($merged_courses_student_registered_and_add)) {
					foreach ($merged_courses_student_registered_and_add as $key2 => $merged_studnet) {
						if ((isset($merged_studnet['CourseRegistration']) && $added_student['CourseAdd']['published_course_id'] == $merged_studnet['CourseRegistration']['published_course_id']) || (isset($merged_studnet['CourseAdd']) && $added_student['CourseAdd']['published_course_id'] == $merged_studnet['CourseAdd']['published_course_id'])) {
							break;
						}
					}
				}
				$merged_courses_student_registered_and_add[] = $added_student;
			}
		}

		//Handling rejected exam grades by registrar
		$rejected_registered_students_published_courses_exam_grades = $this->ExamGrade->CourseRegistration->find('all', array(
			'fields' => array('id', 'published_course_id'),
			'conditions' => array(
				'CourseRegistration.published_course_id' => $published_course_list_student_registered,
				'CourseRegistration.id in (select course_registration_id from exam_grades  where registrar_approval = -1)'
			),
			'contain' => array(
				'ExamGrade' => array('order' => 'ExamGrade.created DESC'),
				'PublishedCourse' => array(
					'fields' => array('id', 'semester', 'section_id'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'Section' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'CourseInstructorAssignment' => array(
						'fields' => array('id', 'published_course_id', 'staff_id'),
						'Staff' => array('id', 'full_name', 'user_id'),
						'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
					),
					'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
				)
			)
		));

		$rejected_added_students_published_courses_exam_grades = $this->ExamGrade->CourseAdd->find('all', array(
			'fields' => array('id', 'published_course_id'),
			'conditions' => array(
				'CourseAdd.published_course_id' => $published_course_list_student_registered,
				'CourseAdd.id in (select course_add_id from exam_grades where registrar_approval = -1)'
			),
			'contain' => array(
				'ExamGrade' => array('order' => 'ExamGrade.created DESC'),
				'PublishedCourse' => array(
					'fields' => array('id', 'semester', 'section_id'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'Section' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'CourseInstructorAssignment' => array(
						'fields' => array('id', 'published_course_id', 'staff_id'),
						'Staff' => array('id', 'full_name', 'user_id'),
						'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
					),
					'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
				)
			)
		));

		$merged_courses_student_rejected_grade_registered_and_add = array();

		if (!empty($rejected_registered_students_published_courses_exam_grades)) {
			foreach ($rejected_registered_students_published_courses_exam_grades as $key => $value) {
				
				if (!empty($merged_courses_student_rejected_grade_registered_and_add)) {
					foreach ($merged_courses_student_rejected_grade_registered_and_add as $key2 => $merged_studnet) {
						if ((isset($merged_studnet['CourseRegistration']) && $value['CourseRegistration']['published_course_id'] == $merged_studnet['CourseRegistration']['published_course_id']) || (isset($merged_studnet['CourseAdd']) && $value['CourseRegistration']['published_course_id'] == $merged_studnet['CourseAdd']['published_course_id'])) {
							break;
						}
					}
				}

				if ($value['ExamGrade'][0]['registrar_approval'] == -1) {
					$merged_courses_student_rejected_grade_registered_and_add[] = $value;
				}
			}
		}
		//debug($merged_courses_student_rejected_grade_registered_and_add);die;

		if (!empty($rejected_added_students_published_courses_exam_grades)) {
			foreach ($rejected_added_students_published_courses_exam_grades as $key => $value) {

				if (!empty($merged_courses_student_rejected_grade_registered_and_add)) {
					foreach ($merged_courses_student_rejected_grade_registered_and_add as $key2 => $merged_studnet) {
						if ((isset($merged_studnet['CourseRegistration']) && $value['CourseAdd']['published_course_id'] == $merged_studnet['CourseRegistration']['published_course_id']) || (isset($merged_studnet['CourseAdd']) && $value['CourseAdd']['published_course_id'] == $merged_studnet['CourseAdd']['published_course_id'])) {
							break;
						}
					}
				}

				if ($value['ExamGrade'][0]['registrar_approval'] == -1) {
					$merged_courses_student_rejected_grade_registered_and_add[] = $value;
				}
			}
		}

		if (empty($merged_courses_student_registered_and_add) && empty($merged_courses_student_rejected_grade_registered_and_add)) {
			$this->Flash->info('There is no grade submission that needs your approval for now. You can view exam result, grade and status of all exams using Grade View.');
			//$this->redirect(array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission')));
			//$this->redirect(array('controller' => 'exam_results', 'action' => 'index'));
		} else {

			$grade_submitted_courses_organized_by_published_course = array();

			if (!empty($merged_courses_student_registered_and_add)) {
				foreach ($merged_courses_student_registered_and_add as $index => $value) {
					if (isset($value['PublishedCourse']['YearLevel']['name'])) {
						$year_level_name = $value['PublishedCourse']['YearLevel']['name'];
					} else {
						$year_level_name = '1st';
					}

					if (isset($value['CourseRegistration'])) {
						$grade_submitted_courses_organized_by_published_course[$value['PublishedCourse']['Program']['name']][$value['PublishedCourse']['ProgramType']['name']][$year_level_name][$value['PublishedCourse']['Section']['name']][$value['CourseRegistration']['published_course_id']] = $value['PublishedCourse'];
					} else {
						$grade_submitted_courses_organized_by_published_course[$value['PublishedCourse']['Program']['name']][$value['PublishedCourse']['ProgramType']['name']][$year_level_name][$value['PublishedCourse']['Section']['name']][$value['CourseAdd']['published_course_id']] = $value['PublishedCourse'];
					}
				}
			}

			$grade_submitted_courses_rejected_organized_by_published_course = array();

			if (!empty($merged_courses_student_rejected_grade_registered_and_add)) {
				foreach ($merged_courses_student_rejected_grade_registered_and_add as $index => $value) {
					if (isset($value['PublishedCourse']['YearLevel']['name'])) {
						$year_level_name = $value['PublishedCourse']['YearLevel']['name'];
					} else {
						$year_level_name = '1st';
					}

					if (isset($value['CourseRegistration'])) {
						$grade_submitted_courses_rejected_organized_by_published_course[$value['PublishedCourse']['Program']['name']][$value['PublishedCourse']['ProgramType']['name']][$year_level_name][$value['PublishedCourse']['Section']['name']][$value['CourseRegistration']['published_course_id']] = $value['PublishedCourse'];
					} else {
						$grade_submitted_courses_rejected_organized_by_published_course[$value['PublishedCourse']['Program']['name']][$value['PublishedCourse']['ProgramType']['name']][$year_level_name][$value['PublishedCourse']['Section']['name']][$value['CourseAdd']['published_course_id']] = $value['PublishedCourse'];
					}
				}
			}
		}

		$this->set(compact(
			'grade_submitted_courses_organized_by_published_course',
			'grade_submitted_courses_rejected_organized_by_published_course',
			'department'
		));

	}

	*/
	
	private function __approve_grade_submission($published_course_id = null, $department = 1)
	{
		//check the published course belongs the department
		if ($published_course_id != "") {
			if ($department == 1) {
				$check = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
						'PublishedCourse.given_by_department_id' => $this->department_ids
					)
				));
			} else {
				$check = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array('conditions' => array(
						'PublishedCourse.id' => $published_course_id,
						'PublishedCourse.college_id' => $this->college_ids
					))
				);
			}

			if ($check == 0) {
				$this->Flash->error('You are not eligible to approve the selected course grades.');
				$this->redirect(array('controller' => 'dashboard','action' => 'index'));
			} else {
				//get list of students with grade
				$get_list_of_students_with_grade = $this->ExamGrade->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				
				$publishedCourseDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'Section' => array('id', 'name'),
						'YearLevel' => array('id', 'name'),
						'Department' => array('id', 'name', 'type'),
						'GivenByDepartment' => array('id', 'name', 'type'),
						'College' => array('id', 'name', 'type'),
						'Course' => array(
							'fields' => array('id', 'course_title', 'course_code', 'credit'),
							'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
						)
					),
					'fields' => array('id', 'academic_year', 'semester'),
				));

				$this->request->data['Search']['academicyear'] = $publishedCourseDetail['PublishedCourse']['academic_year'];

				$hide_approve_list = true;
				$turn_off_search = true;

				$gradeScaleDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$instructorDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->getInstructorDetailGivingPublishedCourse($published_course_id);
				
				$exam_types = $this->ExamGrade->CourseRegistration->ExamResult->ExamType->find('all', array(
					'conditions' => array(
						'ExamType.published_course_id' => $published_course_id
					),
					'contain' => array(),
					'order' => array('order ASC'),
					'fields' => array(
						'id', 
						'exam_name', 
						'percent', 
						'order'
					),
					'recursive' => -1
				));

				$this->set(compact(
					'get_list_of_students_with_grade',
					'hide_approve_list',
					'search_published_course',
					'gradeScaleDetail',
					'instructorDetail',
					'publishedCourseDetail',
					'exam_types',
					'published_course_id',
					'turn_off_search'
				));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['approvegradesubmission'])) {
			
			$approval = $this->request->data['ExamGrade']['department_approval'];
			$reason = $this->request->data['ExamGrade']['department_reason'];
			
			unset($this->request->data['ExamGrade']['department_approval']);
			unset($this->request->data['ExamGrade']['department_reason']);
			
			$reformat_approve_grade = array();
			$count = 0;
			$any_exam_grade_id = "";


			$registrar_rejection = 0;

			if (isset($this->request->data['ExamGrade']) && !empty($this->request->data['ExamGrade'])) {

				foreach ($this->request->data['ExamGrade'] as $exam_grade_key => $exam_grade_value) {
					
					$exam_grade_detail = $this->ExamGrade->find('first', array(
						'conditions' => array('ExamGrade.id' => $exam_grade_value['id']),
						'recursive' => -1
					));

					if ($exam_grade_detail['ExamGrade']['registrar_approval'] == -1) {
						
						$registrar_rejection = 1;

						$any_exam_grade_id = $exam_grade_detail['ExamGrade']['id'];

						unset($exam_grade_detail['ExamGrade']['id']);
						unset($exam_grade_detail['ExamGrade']['registrar_approval']);
						unset($exam_grade_detail['ExamGrade']['registrar_reason']);
						unset($exam_grade_detail['ExamGrade']['registrar_approval_date']);
						unset($exam_grade_detail['ExamGrade']['registrar_approved_by']);
						unset($exam_grade_detail['ExamGrade']['created']);
						unset($exam_grade_detail['ExamGrade']['modified']);

						$exam_grade_detail['ExamGrade']['department_reply'] = 1;
						$exam_grade_detail['ExamGrade']['department_approval'] = $approval;
						$exam_grade_detail['ExamGrade']['department_reason'] = $reason;
						$exam_grade_detail['ExamGrade']['department_approval_date'] = date('Y-m-d H:i:s');
						$exam_grade_detail['ExamGrade']['department_approved_by'] = $this->Auth->user('id');
						$reformat_approve_grade['ExamGrade'][$count] = $exam_grade_detail['ExamGrade'];

					} else {

						$any_exam_grade_id = $exam_grade_value['id'];

						$reformat_approve_grade['ExamGrade'][$count]['id'] = $exam_grade_value['id'];
						$reformat_approve_grade['ExamGrade'][$count]['department_approval'] = $approval;
						$reformat_approve_grade['ExamGrade'][$count]['department_reason'] = $reason;
						$reformat_approve_grade['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
						$reformat_approve_grade['ExamGrade'][$count]['department_approval_date'] = date('Y-m-d H:i:s');
					}

					$count++;
				}
			}

			//saveAll
			if (isset($reformat_approve_grade['ExamGrade']) && !empty($reformat_approve_grade['ExamGrade'])) {
				if ($this->ExamGrade->saveAll($reformat_approve_grade['ExamGrade'], array('validate' => false))) {
					
					//Instructor notification
					$course_instructor = $this->ExamGrade->CourseRegistration->PublishedCourse->getInstructorByExamGradeId($any_exam_grade_id);
					$course = $this->ExamGrade->CourseRegistration->PublishedCourse->Course->getCourseByExamGradeId($any_exam_grade_id);
					$section = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->getSectionByExamGradeId($any_exam_grade_id);
					$published_course = $this->ExamGrade->CourseRegistration->PublishedCourse->getPublishedCourseByExamGradeId($any_exam_grade_id);
					
					if (!empty($course_instructor) && $course_instructor['user_id'] != "") {
						
						$auto_message['AutoMessage']['message'] = 'Your <u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> grade submission is ' . ($approval == 1 ? 'approved' : 'rejected') . ' by the ' . ($department == 1 ? 'department' : 'freshman program') . ' for <u>' . ($section['name']) . '</u> section. <a href="/exam_results/add/' . $published_course['id'] . '">View Grade</a>';
						
						if ($approval == -1) {
							$auto_message['AutoMessage']['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message['AutoMessage']['message'] . '</p>';
						} else if ($approval == 1) {
							$auto_message['AutoMessage']['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message['AutoMessage']['message'] . '</p>';
						}

						$auto_message['AutoMessage']['read'] = 0;
						$auto_message['AutoMessage']['user_id'] = $course_instructor['user_id'];

						ClassRegistry::init('AutoMessage')->save($auto_message);
					}

					if ($approval && $registrar_rejection) {
						$this->Flash->success('The exam grade has been rejected and sent back to the registrar stating the grades are correct. The system will notify registrar to confirm the result.'); 
					} else if ($approval == -1) {
						$this->Flash->warning('The exam grade has been rejected and sent back to the the instructor for re-consideration. The system will notify the assigned instructor to check the result and re-submit again.'); 
					} else {
						$this->Flash->success('The exam grade has been approved. The system will notify registrar to confirm the result.'); 
					}
					
					//$this->redirect(array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission')));
					//$this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
					$this->redirect(array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission')));

				} else {
					$this->Flash->error('The exam grade approval could not be completed. Please, try again.');
				}

			} else {
				$this->Flash->error('No Exam Grade selected to approve. Please select at least one.');
				//$this->redirect(array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission')));
			}
		}
		// print_r($department);

		$this->__init_search();

		//$defaultacademicyear = $this->AcademicYear->current_academicyear();

		if (isset($this->request->data['Search']['academicyear']) && !empty($this->request->data['Search']['academicyear'])) {
			$defaultacademicyear = $this->request->data['Search']['academicyear'];
		} else {
			$defaultacademicyear =  $this->AcademicYear->current_academicyear();
		}

		if (!empty($this->request->data) /* && isset($this->request->data['getCourseNeedsApproval']) */ && is_null($published_course_id)) {
			
			$everythingfine = false;

			if (empty($this->request->data['Search']['academicyear'])) {
				$this->request->data['Search']['academicyear'] = $defaultacademicyear;
			}

			switch ($this->request->data) {
				case empty($this->request->data['Search']['academicyear']):
					$this->Flash->error('Please select the academic year you want to approve grade submission.');
					break;
				/* case empty($this->request->data['Search']['semester']):
					$this->Flash->error('Please select the semester of the course you want to approve grade submission.');
					break;
				case empty($this->request->data['Search']['program_id']):
					$this->Flash->error('Please select the program of the course you want to approve grade submission.');
					break;
				case empty($this->request->data['Search']['program_type_id']):
					$this->Flash->error('Please select the program type of the course you want to approve grade submission.');
					break; */
				default:
					$everythingfine = true;
			}

			// if everthing okay
			if ($everythingfine) {
				/* debug($this->request->data);
				debug($this->department_ids);
				debug($this->college_ids); */

				if (isset($this->request->data['Search']['academicyear']) && !empty($this->request->data['Search']['academicyear'])) {
					$selected_academicyear = $this->request->data['Search']['academicyear'];
				} else {
					$selected_academicyear =  $defaultacademicyear;
				}

				if (isset($this->request->data['Search']['program_id']) && !empty($this->request->data['Search']['program_id'])) {
					$selected_programs = $this->request->data['Search']['program_id'];
				} else {
					$selected_programs = $this->program_type_ids;
				}

				if (isset($this->request->data['Search']['program_type_id']) && !empty($this->request->data['Search']['program_type_id'])) {
					$selected_program_types = $this->request->data['Search']['program_type_id'];
				} else {
					$selected_program_types = $this->program_type_ids;
				}

				if (isset($this->request->data['Search']['semester']) && !empty($this->request->data['Search']['semester'])) {
					$selected_semester = $this->request->data['Search']['semester'];
				} else {
					$selected_semester = '';
				}

				if (isset($this->request->data['Search']['year_level_id']) && !empty($this->request->data['Search']['year_level_id'])) {
					$selected_year_levels = $this->request->data['Search']['year_level_id'];
				} else {
					$programsss =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
					$depts_for_year_level = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
					$selected_year_levels = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(null , null, array_keys($depts_for_year_level), array_keys($programsss));
				}

				// always show the latest grade submitted but required department approval.
				if ($department == 1) {
					debug($this->department_id);

					/* $published_course_list_student_registered = $this->ExamGrade->getRejectedOrNonApprovedPublishedCourseList(
						$this->department_id, 
						1,
						$this->request->data['Search']['academicyear'],
						$this->request->data['Search']['semester'],
						$this->request->data['Search']['program_id'],
						$this->request->data['Search']['program_type_id'],
						null
						//$this->request->data['Search']['year_level_id']
					); */

					$published_course_list_student_registered = $this->ExamGrade->getRejectedOrNonApprovedPublishedCourseList2(
						$this->department_id,
						$selected_academicyear,
						$selected_semester,
						$selected_year_levels,
						$selected_programs,
						$selected_program_types,
						null,
						$this->role_id,
						0
					);

				} else {
					/* $published_course_list_student_registered = $this->ExamGrade->getRejectedOrNonApprovedPublishedCourseList(
						$this->college_id, 
						0,
						$this->request->data['Search']['academicyear'],
						$this->request->data['Search']['semester'],
						$this->request->data['Search']['program_id'],
						$this->request->data['Search']['program_type_id'],
						null
					); */

					$selected_year_level_ids = '';

					$published_course_list_student_registered = $this->ExamGrade->getRejectedOrNonApprovedPublishedCourseList2(
						$this->college_id,
						$this->request->data['Search']['academicyear'],
						$selected_semester,
						$selected_year_levels,
						$selected_programs,
						$selected_program_types,
						null,
						$this->role_id,
						1
					);
				}


				if (isset($published_course_list_student_registered) && !empty($published_course_list_student_registered)) {

					$grade_submitted_courses_organized_by_published_course = array();

					if (!empty($published_course_list_student_registered)) {
						foreach ($published_course_list_student_registered as $index => $value) {
							if (isset($value['YearLevel']['name'])) {
								$year_level_name = $value['YearLevel']['name'];
							} else {
								$year_level_name = 'Pre/1st';
							}

							if (isset($value['Department']['id'])) {
								$department_id = $value['Department']['id'];
							} else {
								$department_id = 0;
							}

							if (isset($value['College']['id'])) {
								$college_id = $value['College']['id'];
							} else  {
								$college_id = NULL;
							}

							if (is_numeric($department_id) && $department_id > 0) {
								$grade_submitted_courses_organized_by_published_course[$department_id][$value['Program']['name']][$value['ProgramType']['name']][$year_level_name][$value['Section']['name']][$value['PublishedCourse']['id']] = $value;
							} else if (is_numeric($college_id) && $college_id > 0) {
								$grade_submitted_courses_organized_by_published_course['c~' . $college_id][$value['Program']['name']][$value['ProgramType']['name']][$year_level_name][$value['Section']['name']][$value['PublishedCourse']['id']] = $value;
							}
						}
					}

					$this->set('turn_off_search', true);
					$this->set(compact('grade_submitted_courses_organized_by_published_course'));
					
				} else {
					$this->set('turn_off_search', false);
					$this->Flash->info('There is no grade submission for '. $defaultacademicyear . ' academic year that needs your approval for now. You can change the filters and check other academic year grade submissions which are prior to ' .$defaultacademicyear.'.');
					//$this->redirect(array('action' => ($department == 1 ? 'department_grade_view' : 'freshman_grade_view'), $this->request->data['Search']));
					//$this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
					//$this->redirect(Router::url($this->referer(), true));
				}

				$this->set(compact(
					'grade_submitted_courses_organized_by_published_course',
					'grade_submitted_courses_rejected_organized_by_published_course',
					'department'
				));
			}
		}

		// consider this if still the system is too slow

		if (!empty($this->department_ids) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$departments = $this->ExamGrade->CourseRegistration->PublishedCourse->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			$this->set(compact('departments'));
		} else if (!empty($this->college_ids) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$colleges = $this->ExamGrade->CourseRegistration->PublishedCourse->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			$this->set(compact('colleges'));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			$departments = $this->ExamGrade->CourseRegistration->PublishedCourse->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
			$this->set(compact('departments'));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$departments = $this->ExamGrade->CourseRegistration->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1,)));
			$programsss =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
			$depts_for_year_level = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
			$yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(null , null, array_keys($depts_for_year_level), array_keys($programsss));;
			//$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => $this->year_levels)));
			//debug($yearLevels);
			$this->set(compact('departments', 'yearLevels'));
		}

		$collegesss = $this->ExamGrade->CourseRegistration->PublishedCourse->College->find('list', array('conditions' => array('College.active' => 1)));
		$departmentsss = $this->ExamGrade->CourseRegistration->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1)));

		$this->set(compact(
			'grade_submitted_courses_organized_by_published_course',
			'programs',
			'programTypes',
			'defaultacademicyear',
			'departmentsss',
			'collegesss'
		));
	}

	function confirm_grade_submission($published_course_id = null)
	{

		$section_prog_id = '';
		$section_prog_type_id = '';

		if ($published_course_id != "") {
			
			$check1 = 1;
			$check2 = 1;
			$any_exam_grade_id = "";

			if (!empty($this->department_ids)) {
				$check1 = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
						'PublishedCourse.department_id' => $this->department_ids
					)
				));
			}

			if (!empty($this->college_ids)) {
				$check2 = $this->ExamGrade->CourseRegistration->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
						'PublishedCourse.college_id' => $this->college_ids
					))
				);
			}

			//debug($this->department_ids);
			if ($check1 == 0 || $check2 == 0) {
				$this->Flash->error('You are not eligible to approve the selected course grades.');
				$this->redirect(array('controller'=>'dashboard','action'=>'index'));
			} else {
				//get list of students with grade
				$get_list_of_students_with_grade = $this->ExamGrade->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);

				$publishedCourseDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array(
					'fields' => array('id', 'academic_year', 'semester', 'program_id', 'program_type_id'),
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id
					),
					'contain' => array(
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'Section' => array('id', 'name'),
						'YearLevel' => array('id', 'name'),
						'Department' => array('id', 'name', 'type'),
						'GivenByDepartment' => array('id', 'name', 'type'),
						'College' => array('id', 'name', 'type'),
						'Course' => array(
							'fields' => array('id', 'course_title', 'course_code', 'credit'),
							'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
						)
					)
				));

				$this->request->data['Search']['academicyear'] = $publishedCourseDetail['PublishedCourse']['academic_year'];

				$section_prog_id = $publishedCourseDetail['PublishedCourse']['program_id'];
				$section_prog_type_id = $publishedCourseDetail['PublishedCourse']['program_type_id'];

				//debug($this->request->data['Search']['academicyear']);

				$hide_approve_list = true;
				$search_published_course = true;
				$turn_off_search = true;

				$gradeScaleDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
				$instructorDetail = $this->ExamGrade->CourseRegistration->PublishedCourse->getInstructorDetailGivingPublishedCourse($published_course_id);

				//debug($instructorDetail);

				$this->set(compact(
					'get_list_of_students_with_grade',
					'hide_approve_list',
					'search_published_course',
					'gradeScaleDetail',
					'instructorDetail',
					'publishedCourseDetail',
					'turn_off_search'
				));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['confirmgradesubmission'])) {

			$confirmed = 0;

			if ($this->request->data['ExamGrade']['registrar_approval'] == 1) {
				$confirmed = 1;
			}

			$reason = $this->request->data['ExamGrade']['registrar_reason'];
			$approval = $this->request->data['ExamGrade']['registrar_approval'];

			unset($this->request->data['ExamGrade']['registrar_approval']);
			unset($this->request->data['ExamGrade']['registrar_reason']);

			$reformat_approve_grade = array();
			$approved_exam_grades = array();
			$count = 0;


			if (isset($this->request->data['ExamGrade']) && !empty($this->request->data['ExamGrade'])) {

				foreach ($this->request->data['ExamGrade'] as $exam_grade_key => $exam_grade_value) {
					
					$any_exam_grade_id = $exam_grade_value['id'];

					$reformat_approve_grade['ExamGrade'][$count]['id'] = $exam_grade_value['id'];
					$reformat_approve_grade['ExamGrade'][$count]['registrar_approval'] = $approval;
					$reformat_approve_grade['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');
					$reformat_approve_grade['ExamGrade'][$count]['registrar_reason'] = $reason;
					$reformat_approve_grade['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s');

					$approved_exam_grades[] = $exam_grade_value['id'];
					$count++;
				}
			}


			if (isset($reformat_approve_grade['ExamGrade']) && !empty($reformat_approve_grade['ExamGrade'])) {
				//saveAll
				if ($this->ExamGrade->saveAll($reformat_approve_grade['ExamGrade'], array('validate' => 'first'))) {
					//Notifications
					ClassRegistry::init('AutoMessage')->sendNotificationOnRegistrarGradeConfirmation($reformat_approve_grade['ExamGrade']);

					$published_course_search = $this->ExamGrade->CourseRegistration->ExamGrade->find('first', array(
						'conditions' => array(
							'ExamGrade.id' => $reformat_approve_grade['ExamGrade'][0]['id']
						),
						'contain' => array(
							'CourseRegistration' => array(
								'PublishedCourse'
							),
							'CourseAdd' => array(
								'PublishedCourse'
							)
						),
						'recursive' => -1
					));

					debug($published_course_search);

					if (!empty($published_course_search['CourseRegistration']) && !empty($published_course_search['CourseRegistration']['id'])) {
						$published_course_id2 = $published_course_search['CourseRegistration']['PublishedCourse']['id'];
					} else {
						$published_course_id2 = $published_course_search['CourseAdd']['PublishedCourse']['id'];
					}

					//launch background job
					//$result=shell_exec("/var/www/smis.aait/smis-2/app/Console/cake status_by_course ".$published_course_id2." generate ");

					$result = $this->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse($published_course_id2);

					// if registrar confirmed the grade, mail the result to student.
					if (GRADE_NOTIFICATION_FOR_STUDENTES_SYSTEM_WIDE_ENABLED && isset($confirmed) && $confirmed && isset($approved_exam_grades) && !empty($approved_exam_grades)) {
						debug($approved_exam_grades);
						if (!empty($section_prog_id) && !empty($section_prog_type_id)) {
							$generalSettings = ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($student_id = null, $section_prog_id, $section_prog_type_id, $section_id = null);
							debug($generalSettings);
							debug($generalSettings['GeneralSetting']['notifyStudentsGradeByEmail']);

							if (!empty($generalSettings) && $generalSettings['GeneralSetting']['notifyStudentsGradeByEmail']) {
								//disabled for now ENABLE IT AFTER TEST ON PRODUCTION
								//debug($this->__attachGradeToEmail($approved_exam_grades));
							}
						}
					}

					if ($result) {
						if ($confirmed) {
							$this->Flash->success('Exam grade submission confirmed successfully.');
						} else {
							$this->Flash->warning('Exam grade submission is rejected and sent back to department for re-consideration.');
						}
					} else {
						$this->Flash->warning('Exam grade submission confirmed successfully but student academic status is not generated. Please regenetate student academic status manually if this exam grade submission is the last submitted grade of the section for the semester.');
					}
					//$this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
					$this->redirect(array('action' => 'confirm_grade_submission'));
				} else {
					$this->Flash->error('The exam grade submission could not approved. Please, try again.');
				}
			} else {
				$this->Flash->error('No Exam Grade submission is selected to approve. Please select one.');
			}

			//$this->__init_search();
		}

		$this->__init_search();

		//$defaultacademicyear = $this->AcademicYear->current_academicyear();

		if (isset($this->request->data['Search']['academicyear']) && !empty($this->request->data['Search']['academicyear'])) {
			$defaultacademicyear = $this->request->data['Search']['academicyear'];
		} else {
			$defaultacademicyear =  $this->AcademicYear->current_academicyear();
		}

		if (!empty($this->request->data) /* && isset($this->request->data['getCourseNeedsApproval']) */ && is_null($published_course_id)) {
			
			$everythingfine = false;

			if (empty($this->request->data['Search']['academicyear'])) {
				$this->request->data['Search']['academicyear'] = $defaultacademicyear;
			}
			
			switch ($this->request->data) {
				case empty($this->request->data['Search']['academicyear']):
					$this->Flash->error('Please select the academic year you want to confirm the grade submission.');
					break;
				/* case empty($this->request->data['Search']['semester']):
					$this->Flash->error('Please select the semester of the course you want to confirm the grade submission.');
					break;
				case empty($this->request->data['Search']['program_id']):
					$this->Flash->error('Please select the program of the course you want to confirm the grade submission.');
					break;
				case empty($this->request->data['Search']['program_type_id']):
					$this->Flash->error('Please select the program type of the course you want to confirm the grade submission.');
					break; */
				default:
					$everythingfine = true;
			}

			// if everthing okay
			if ($everythingfine) {
				
				/* debug($this->request->data);
				debug($this->department_ids);
				debug($this->college_ids); */

				if (isset($this->request->data['Search']['academicyear']) && !empty($this->request->data['Search']['academicyear'])) {
					$selected_academicyear = $this->request->data['Search']['academicyear'];
				} else {
					$selected_academicyear =  $defaultacademicyear;
				}

				if (isset($this->request->data['Search']['program_id']) && !empty($this->request->data['Search']['program_id'])) {
					$selected_programs = $this->request->data['Search']['program_id'];
				} else {
					$selected_programs = $this->program_ids;
				}

				if (isset($this->request->data['Search']['program_type_id']) && !empty($this->request->data['Search']['program_type_id'])) {
					$selected_program_types = $this->request->data['Search']['program_type_id'];
				} else {
					$selected_program_types = $this->program_type_ids;
				}

				if (isset($this->request->data['Search']['semester']) && !empty($this->request->data['Search']['semester'])) {
					$selected_semester = $this->request->data['Search']['semester'];
				} else {
					$selected_semester = '';
				}

				//check to which department is assigned.
				if (!empty($this->department_ids)) {
					/* $published_course_list_student_registered = $this->ExamGrade->getRegistrarNonApprovedPublishedCourseList(
						$this->department_ids,
						null,
						$this->request->data['Search']['semester'],
						$this->request->data['Search']['program_id'],
						$this->request->data['Search']['program_type_id'],
						$this->request->data['Search']['academicyear']
					); */

					$published_course_list_student_registered = $this->ExamGrade->getRegistrarNonApprovedCoursesList2(
						$this->department_ids,
						null,
						$selected_academicyear,
						$selected_semester,
						$selected_programs,
						$selected_program_types,
						null
					);

				} else if (!empty($this->college_ids)) {
					/* $published_course_list_student_registered = $this->ExamGrade->getRegistrarNonApprovedPublishedCourseList(
						null,
						$this->college_ids,
						$this->request->data['Search']['semester'],
						$this->request->data['Search']['program_id'],
						$this->request->data['Search']['program_type_id'],
						$this->request->data['Search']['academicyear']
					); */

					$published_course_list_student_registered = $this->ExamGrade->getRegistrarNonApprovedCoursesList2(
						null,
						$this->college_ids,
						$selected_academicyear,
						$selected_semester,
						$selected_programs,
						$selected_program_types,
						null
					);
				}

				/*********************************************************************************/
				if (isset($published_course_list_student_registered) && !empty($published_course_list_student_registered)) {
					
					$grade_submitted_courses_organized_by_published_course = array();

					if (!empty($published_course_list_student_registered)) {
						foreach ($published_course_list_student_registered as $index => $value) {
								
							if (isset($value['YearLevel']['name'])) {
								$year_level_name = $value['YearLevel']['name'];
							} else {
								$year_level_name = 'Pre/1st';
							}

							if (isset($value['Department']['id'])) {
								$department_id = $value['Department']['id'];
							} else {
								$department_id = 0;
							}

							if (isset($value['College']['id'])) {
								$college_id = $value['College']['id'];
							} else  {
								$college_id = NULL;
							}

							if (is_numeric($department_id) && $department_id > 0) {
								$grade_submitted_courses_organized_by_published_course[$department_id][$value['Program']['name']][$value['ProgramType']['name']][$year_level_name][$value['Section']['name']][$value['PublishedCourse']['id']] = $value;
							} else if (is_numeric($college_id) && $college_id > 0) {
								$grade_submitted_courses_organized_by_published_course['c~' . $college_id][$value['Program']['name']][$value['ProgramType']['name']][$year_level_name][$value['Section']['name']][$value['PublishedCourse']['id']] = $value;
							}
						}

						$this->set('turn_off_search', true);
						$this->set(compact('grade_submitted_courses_organized_by_published_course'));
					}
				} else {
					$this->set('turn_off_search', false);
					$this->Flash->info('There is no grade submission for ' . $selected_academicyear . ' academic year ' . (!empty($selected_semester) ? ' Semester ' . $selected_semester : ' in the given criteria') .' that needs your confirmation for now.You can change the filters and check other academic year grade submissions which are prior to ' .$selected_academicyear.'.');
					//  $this->redirect(array('action' => 'registrar_grade_view'));
				}
			}
		}

		// consider this if still the system is too slow

		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
		$programTypes = $this->ExamGrade->CourseRegistration->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));


		if (!empty($this->department_ids) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$departments = $this->ExamGrade->CourseRegistration->PublishedCourse->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			$this->set(compact('departments'));
			//$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
			//$programTypes = $this->ExamGrade->CourseRegistration->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));
		} else if (!empty($this->college_ids) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$colleges = $this->ExamGrade->CourseRegistration->PublishedCourse->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			$this->set(compact('colleges'));
			//$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
			//$programTypes = $this->ExamGrade->CourseRegistration->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));
		}

		$departmentsss = $this->ExamGrade->CourseRegistration->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1)));
		$collegesss = $this->ExamGrade->CourseRegistration->PublishedCourse->College->find('list', array('conditions' => array('College.active' => 1)));

		$this->set(compact(
			'grade_submitted_courses_organized_by_published_course',
			'programs',
			'programTypes',
			'defaultacademicyear',
			'departmentsss',
			'collegesss'
		));
	}


	function __init_search()
	{
		if (!empty($this->request->data['Search'])) {
			$this->Session->write('search_data', $this->request->data['Search']);
		} else if ($this->Session->check('search_data')) {
			$this->request->data['Search'] = $this->Session->read('search_data');
		}
	}

	// Given exam grade id and find the student who registred for it and send notification about his result.
	
	function __attachGradeToEmail($exam_grade_ids = null)
	{
		//find email address of the student and send result notification.
		$detail = $this->ExamGrade->find('all', array(
			'conditions' => array('ExamGrade.id' => $exam_grade_ids),
			'fields' => array('ExamGrade.id', 'ExamGrade.grade', 'ExamGrade.course_registration_id', 'ExamGrade.course_add_id'),
			'contain' => array(
				'CourseRegistration' => array(
					'fields' => array('id', 'published_course_id'),
					'Student' => array(
						'User' => array('id', 'email', 'email_verified'),
						'fields' => array('id', 'full_name', 'first_name', 'email'),
					),
					'PublishedCourse' => array(
						'fields' => array('id'),
						'Course' => array('course_code', 'course_title', 'credit')
					)
				),
				'CourseAdd' => array(
					'fields' => array('id', 'published_course_id'),
					'Student' => array(
						'User' => array('id', 'email', 'email_verified'),
						'fields' => array('id', 'full_name', 'first_name', 'email'),
					),
					'PublishedCourse' => array(
						'fields' => array('id'),
						'Course' => array('course_code', 'course_title', 'credit')
					)
				)
			)
		));

		if (!empty($detail)) {
			$subject = "Examination Result";
			foreach ($detail as $key => $value) {
				// send email
				if ((!empty($value['CourseRegistration']['Student']['User']['email']) && !empty($value['CourseRegistration']['Student']['User']['email_verified']) && (int) $value['CourseRegistration']['Student']['User']['email_verified']) || (!empty($value['CourseAdd']['Student']['User']['email']) && !empty($value['CourseAdd']['Student']['User']['email_verified']) && (int) $value['CourseAdd']['Student']['User']['email_verified'])) {
					$email = (!empty($value['CourseRegistration']['Student']['User']['email']) ? $value['CourseRegistration']['Student']['User']['email'] : $value['CourseAdd']['Student']['User']['email']);
					$body = 'Dear ' . (!empty($value['CourseRegistration']['Student']['first_name']) ? $value['CourseRegistration']['Student']['first_name'] : $value['CourseAdd']['Student']['first_name']) . ', the grade you have got for ' . (!empty($value['CourseRegistration']['PublishedCourse']['Course']['course_title']) ? $value['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $value['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ')' : $value['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $value['CourseAdd']['PublishedCourse']['Course']['course_code'] . ')') . ' is ' . $value['ExamGrade']['grade'];
					$this->__sendGradeNotification($email, $subject, $body, $value['CourseRegistration']['Student']['id']);
					$body = '';
				}
			}
		}
	}

	//send grade notification message and log to database; a private function
	function __sendGradeNotification($email = null, $subject = null, $body = null, $student_id = null) 
	{
		$sent = false;
		$auth = $this->Session->read('Auth.User');
		$from = $auth['id'];
		$contentOfEMail = NULL;

		if (!empty($email)) {
			$userIdAndBatchName['user_id'] = $auth['id'];
			if ($this->__sendEmail('grade_notification', $subject, $email, $body, $student_id)) {
				$contentOfEMail = "To:" . $email . "\n" . "Subject:" . $subject . "\n" . $this->__getEmailReturnAddress() . "\n" . "--content--" . "\n" . $body . "\n";
				$message = array();
				$message['from'] = $from;
				$message['subject'] = $subject;
				$message['content'] = $contentOfEMail;
				//$message['user_id'] = $user_id;
				$message['model'] = 'ExamGrade';
				ClassRegistry::init('Mailer')->logMessage($message);
				$sent = true;
				$contentOfEMail = null;
			} else {
				$sent = false;
			}
			return $sent;
		}
	}

	//This function set return email address @ return the setted email addresses
	function __getEmailReturnAddress()
	{
		$returnAddress = null;
		$returnAddress = "From:" . $this->Email->from . "\n" . "Reply-To:" . $this->Email->replyTo . "\n" . "Return-Path:" . $this->Email->return . "";
		if (isset($returnAddress)) {
			return $returnAddress;
		}
		// return $returnAddress;
	}

	//function that takes user_id and set first name and last name  @ return false if the user_id is invalid
	function __attachNameToEmail($student_id = null)
	{
		// if the User id is valid and get the name of the person to attach to his/her name in message for personolization
		if ($student_id) {
			$students = $this->ExamGrade->CourseRegistration->Student->find('first', array('conditions' => array('Student.id' => $student_id)));
			if (!empty($students)) {
				$email_verified = $students['User']['email_verified'];
				if (!empty($email_verified) && $email_verified) {
					$this->set('firstname', $students['Student']['first_name']);
					$this->set('lastname', $students['Student']['middle_name']);
                    return true;
                }
			}
			return false;
		} else {
			// invalid User id don't send the email
			return false;
		}
		//return true;
	}

	// This function setup the template ,subject and  list of users who are receiver of this email @ return true or false based on the return of send function
	
	function __sendEmail($templateName, $emailSubject, $to, $body, $student_id, $from = EMAIL_DEFAULT_FROM, $replyToEmail = EMAIL_DEFAULT_REPLY_TO, $return = EMAIL_DEFAULT_RETURN_PATH, $sendAs = 'both') 
	{
		if (!$this->__attachNameToEmail($student_id)) {
			// invalid user id don't send the email
			return false;
		}

		$this->set('message', $body);
		$this->Email->to = $to;
		$this->Email->subject = $emailSubject;
		$this->Email->replyTo = $replyToEmail;
		$this->Email->from = $from;
		// address for bounced mail
		$this->Email->return = $return;
		// additional configuration setting  to  override send mail
		// return path
		$this->Email->additionalParams = "-r $return";
		$this->Email->template = $templateName;
		$this->Email->sendAs = $sendAs;
		return $this->Email->send();
	}

	function student_grade_view($ay1 = null, $ay2 = null, $semester = null)
	{
		/*
			1. Retrieve and list of AY and semester the student register and/or add.
			2. Display as a combo box for selection.
			3. Display grade report
			4. TODO: Check if students has filled instructor evaluation
		*/
		$notEvaluatedList = classRegistry::init('StudentEvalutionRate')->getNotEvaluatedRegisteredCourse($this->student_id);

		if (!classRegistry::init('GeneralSetting')->allowStudentsGradeViewWithouInstructorsEvalution($this->student_id) && !empty($notEvaluatedList)) {
			return $this->redirect(array('controller' => 'studentEvalutionRates', 'action' => "add"));
		}

		$student_ay_s_list = $this->ExamGrade->getListOfAyAndSemester($this->student_id);
		$acadamic_years = array();

		if (!empty($student_ay_s_list)) {
			foreach ($student_ay_s_list as $key => $ay_s) {
				$acadamic_years[$ay_s['academic_year']] = $ay_s['academic_year'];
			}
		}

		if (!empty($ay1) && !empty($ay2) && !empty($semester)) {
			$this->request->data['ExamGrade']['academic_year'] = str_replace('-', '/', $ay1);
			$this->request->data['ExamGrade']['semester'] = $semester;
			$this->request->data['myGradeReport'] = true;
		}

		//When the "Grade Report" button is clicked
		if (isset($this->request->data['myGradeReport'])) {
			$student_copy = $this->ExamGrade->getStudentCopy($this->student_id, $this->request->data['ExamGrade']['academic_year'], $this->request->data['ExamGrade']['semester']);
		}

		$this->set(compact('acadamic_years', 'student_copy'));
	}

	function department_grade_view($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
	{
		//$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'department');
		if (isset($this->request->data) && !empty($this->request->data)) {
			$this->__view_grade(null, $type, null, null, null, 'department');
		} else {
			$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'department');
		}
	}

	function freshman_grade_view($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
	{
		//$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'freshman');
		if (isset($this->request->data) && !empty($this->request->data)) {
			$this->__view_grade(null, $type, null, null, null, 'freshman');
		} else {
			$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'freshman');
		}
	}

	function college_grade_view($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
	{
		//$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'college');
		if (isset($this->request->data) && !empty($this->request->data)) {
			$this->__view_grade(null, $type, null, null, null, 'college');
		} else {
			$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'college');
		}
	}

	function cheating_view()
	{
		if (!empty($this->request->data) && isset($this->request->data['viewCheatingStudentList'])) {

			$studentsWithCheatingCases = $this->ExamGrade->CourseRegistration->listOfStudentsWithNGToFWithCheating($this->request->data['ExamGrade']['department_id'], $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id'], 0);

			if (empty($studentsWithCheatingCases)) {
				$this->Flash->error('There is no cheating result grade change recorded.');
			}
			$this->set(compact('studentsWithCheatingCases'));
		}

		if (isset($this->program_id) && !empty($this->program_id)) {
			$programs = $this->ExamGrade->CourseRegistration->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
		} else {
			$programs = $this->ExamGrade->CourseRegistration->Student->Program->find('list');
		}

		if (isset($this->program_type_id) && !empty($this->program_type_id)) {
			$programTypes = $this->ExamGrade->CourseRegistration->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		} else {
			$programTypes = $this->ExamGrade->CourseRegistration->Student->ProgramType->find('list');
		}

		if (!empty($this->department_ids)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
		} else if (isset($this->department_id) && !empty($this->department_id)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		}

		$this->set(compact('programs', 'program_types', 'departments'));
	}

	function registrar_grade_view($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
	{
		//$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'registrar');
		debug($section_or_published_course_id);
		if (isset($this->request->data) && !empty($this->request->data)) {
			$this->__view_grade(null, $type, null, null, null, 'registrar');
		} else {
			$this->__view_grade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'registrar');
		}
	}

	private function __view_grade($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null, $who = 'registrar') 
	{

		$published_course_combo_id = null;
		$department_combo_id = null;
		$publishedCourses = array();
		$students_with_ng = array();
		$have_message = false;

		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list');

		debug($section_or_published_course_id);
		//$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');
		//Department combo box building

		$grade_view_action = 'index';

		if (strcasecmp($who, 'registrar') == 0) {
			if ($this->Session->read('Auth.User')['is_admin'] == 1) {
				$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids, $includePre = 1, $only_active = 1);
			} else {
				if ($this->onlyPre) {
					$departments = $this->ExamGrade->CourseRegistration->Student->Department->onlyFreshmanInAllColleges($this->college_ids, 1);
				} else {
					$departments = $this->ExamGrade->CourseRegistration->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
				}
			}
			$grade_view_action = 'registrar_grade_view';
		} else if (strcasecmp($who, 'college') == 0) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->allCollegeDepartments($this->college_id, 1);
			$grade_view_action = 'college_grade_view';
		} else if (strcasecmp($who, 'department') == 0) {
			$departments[0] = 0;
			$grade_view_action = 'department_grade_view';
		} else if (strcasecmp($who, 'freshman') == 0) {
			$departments[0] = 0;
			$grade_view_action = 'freshman_grade_view';
		} else {
			$departments = array();
		}

		if (!empty($this->request->data)) {
			if (strcasecmp($who, 'department') == 0) {
				$department_id = $this->department_id;
			} else if (strcasecmp($who, 'freshman') == 0 || $this->onlyPre) {
				if (empty($this->request->data['ExamGrade']['department_id']) && !empty($this->college_ids)) {
					$department_id = 'c~' . (array_values($this->college_ids)[0]);
				} else if (!empty($this->request->data['ExamGrade']['department_id'])) {
					$department_id = $this->request->data['ExamGrade']['department_id'];
				} else {
					$department_id = 'c~' . $this->college_id;
				}
			} else {
				$department_id = $this->request->data['ExamGrade']['department_id'];
			}

			$department_combo_id = $department_id;
			$college_id = explode('~', $department_id);

			debug($college_id);

			if (is_array($college_id) && count($college_id) > 1) {
				$college_id = $college_id[1];
				$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($college_id, $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id'], 1);
			} else {
				$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester'], $this->request->data['ExamGrade']['program_id'], $this->request->data['ExamGrade']['program_type_id'], 1);
				//debug($publishedCourses);
			}

			if (empty($publishedCourses)) {
				$this->Flash->info('No published courses is found with the given criteria.');
				return $this->redirect(array('action' => $grade_view_action));
			} else {
				$publishedCourses = array('0' => '[ Select Published Course/Section ]') + $publishedCourses;
			}

			debug($this->request->data);
		}

		/////////////////////////////////////////
		//By published course and section.
		//$section_or_published_course_id variable used to represent either published course or section.
		
		if (!empty($section_or_published_course_id)) { 
			
			$published_course_id = $section_or_published_course_id;
			$section_detail = array();
			$published_course = array();

			if (strcasecmp($type, 'section') == 0) {
				
				$section_id = $section_or_published_course_id;
				debug($section_id);

				$section_detail = $this->ExamGrade->CourseAdd->Student->Section->find('first', array(
					'conditions' => array(
						'Section.id' => $section_id
					),
					'contain' => array(
						'Department',
						'College',
						'ProgramType' => array('id', 'name', 'shortname'), 
						'Program' => array('id', 'name', 'shortname'), 
						'YearLevel' => array('id', 'name'),
					)
				));

				$department_id = $section_detail['Department']['id'];

				if (empty($department_id)) {
					$college_id = $section_detail['Section']['college_id'];
					$section_college_id = $section_detail['Section']['college_id'];
				} else {

					$college_id = null;
					$section_college_id = $section_detail['Department']['college_id'];

					if (!empty($department_id)) {
						$privileged_department_ids[] = $department_id;
					}
				}

				debug($department_id);

				$academic_year = $ay1 . '/' . $ay2;
				$program_id = $section_detail['Section']['program_id'];
				$program_type_id = $section_detail['Section']['program_type_id'];
				$published_course_combo_id = 's~' . $section_or_published_course_id;

			} else {

				$published_course = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $section_or_published_course_id
					),
					'contain' => array(
						'Department',
						'GivenByDepartment',
						'College',
						'YearLevel' => array('id', 'name'),
						'ProgramType' => array('id', 'name', 'shortname'), 
						'Program' => array('id', 'name', 'shortname'),
						'Section' => array(
							'College',
							'Department',
							'ProgramType' => array('id', 'name', 'shortname'), 
							'Program' => array('id', 'name', 'shortname'), 
							'YearLevel' => array('id', 'name'),
						), 
					)
				));
				
				if (!empty($published_course['PublishedCourse']['department_id'])) {
					$privileged_department_ids[] = $published_course['PublishedCourse']['department_id'];
				}

				if (!empty($published_course['PublishedCourse']['given_by_department_id'])) {
					$privileged_department_ids[] = $published_course['PublishedCourse']['given_by_department_id'];
				}

				$department_id = $published_course['PublishedCourse']['department_id'];
				$given_by_department_id = $published_course['PublishedCourse']['given_by_department_id'];
				$college_id = $published_course['PublishedCourse']['college_id'];
				$academic_year = $published_course['PublishedCourse']['academic_year'];
				$program_id = $published_course['PublishedCourse']['program_id'];
				$program_type_id = $published_course['PublishedCourse']['program_type_id'];
				$semester = $published_course['PublishedCourse']['semester'];
				$published_course_combo_id = $section_or_published_course_id;

				if (!empty($college_id)) {
					$section_college_id = $college_id;
				} else if (!empty($published_course['Department']['college_id'])){
					$section_college_id = $published_course['Department']['college_id'];
				} else if (!empty($published_course['GivenByDepartment']['college_id'])){
					$section_college_id = $published_course['GivenByDepartment']['college_id'];
				}

				if (!empty($section_college_id)) {
					$privileged_department_ids[] = $section_college_id;
				}
			}

			$publishedCourses = array();
			debug($published_course);
			debug($section_detail);

			if ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				// Registrar admin, allow full access
			} else 
			if ((empty($published_course) && empty($section_detail)) ||
				(strcasecmp($who, 'registrar') == 0 && !empty($department_id) && !in_array($department_id, $this->department_ids)) ||
				(strcasecmp($who, 'registrar') == 0 && !empty($college_id) && !in_array($college_id, $this->college_ids)) ||
				(strcasecmp($who, 'college') == 0 && $section_college_id != $this->college_id) ||
				(strcasecmp($who, 'department') == 0 && !in_array($this->department_id, $privileged_department_ids)) ||
				(strcasecmp($who, 'freshman') == 0 && (!in_array($this->college_id, $privileged_department_ids) || !in_array($this->college_id, $privileged_department_ids)))
			) {
				$this->Flash->error('Please select a valid published course or section.');
				return $this->redirect(array('action' => $grade_view_action));
			} else {
				if (empty($published_course) && empty($section_detail)) {
					$this->Flash->error('Please select a valid published course or section.');
					return $this->redirect(array('action' => $grade_view_action));
				}
				if (empty($department_id)) {
					if ($this->department_id == $given_by_department_id) {
						$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($college_id, $academic_year, $semester, $program_id, $program_type_id, 1, $given_by_department_id);
					} else {
						$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($college_id, $academic_year, $semester, $program_id, $program_type_id, 1);
					}

					$department_combo_id = 'c~' . $college_id;

				} else {

					$publishedCourses = $this->ExamGrade->CourseRegistration->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
						$department_id,
						$academic_year,
						$semester,
						$program_id,
						$program_type_id,
						1
					);

					$department_combo_id = $department_id;
				}
			}

			//Retriving and displaying students with their grade
			if (strcasecmp($type, 'section') != 0) {

				$student_course_register_and_adds = $this->ExamGrade->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($section_or_published_course_id);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$grade_submission_status = $this->ExamGrade->CourseAdd->ExamResult->getExamGradeSubmissionStatus($section_or_published_course_id, $student_course_register_and_adds);
				
				//debug($student_course_register_and_adds);
				$section_and_course_detail = $this->ExamGrade->CourseRegistration->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $section_or_published_course_id
					),
					'contain' => array(
						'YearLevel' => array('id', 'name'),
						'ProgramType' => array('id', 'name', 'shortname'), 
						'Program' => array('id', 'name', 'shortname'),
						'GivenByDepartment',
						'Department',
						'College',
						'Section' => array(
							'College',
							'Department',
							'ProgramType' => array('id', 'name', 'shortname'), 
							'Program' => array('id', 'name', 'shortname'), 
							'YearLevel' => array('id', 'name'),
						), 
						'Course'
					)
				));

				$section_detail = $section_and_course_detail['Section'];
				$course_detail = $section_and_course_detail['Course'];
				$view_only = true;
				$display_grade = true;
				$grade_view_only = true;
				$exam_types = array();

				$program_id = $section_and_course_detail['Section']['program_id'];
				$program_type_id = $section_and_course_detail['Section']['program_type_id'];
				$department_id = $section_and_course_detail['Section']['department_id'];
				$academic_year_selected = $academic_year;
				$semester_selected = $semester;

				$this->set(compact(
					'published_course_id',
					'publishedCourses',
					'programs',
					'program_types',
					'departments',
					'publishedCourses',
					'published_course_combo_id',
					'department_combo_id',
					'students',
					'student_adds',
					'student_makeup',
					'grade_submission_status',
					'course_detail',
					'section_detail',
					'view_only',
					'display_grade',
					'exam_types',
					'exam_types',
					'grade_view_only',
					'program_id',
					'program_type_id',
					'department_id',
					'academic_year_selected',
					'semester_selected'
				));

				$this->render('view_grade');
				return;

			} else {

				$section_details = $this->ExamGrade->CourseRegistration->Student->Section->find('first', array(
					'conditions' => array(
						'Section.id' => $section_or_published_course_id
					),
					//'contain' => array('Department', 'College', 'ProgramType', 'Program', 'YearLevel'),
					'contain' => array(
						'Department', 
						'College',
						'YearLevel' => array('id', 'name'),
						'ProgramType' => array('id', 'name', 'shortname'), 
						'Program' => array('id', 'name', 'shortname'),
					),
				));

				$master_sheet = $this->ExamGrade->getMasterSheet($section_or_published_course_id, $academic_year, $semester);
				$section_detail = $section_details['Section'];
				$department_detail = $section_details['Department'];
				$college_detail = $section_details['College'];
				$program_detail = $section_details['Program'];
				$program_type_detail = $section_details['ProgramType'];

				$program_id = $section_details['Program']['id'];
				$program_type_id = $section_details['ProgramType']['id'];
				$department_id = $section_details['Department']['id'];
				$academic_year_selected = $academic_year;
				$semester_selected = $semester;

				//store to session for excel
				$this->Session->write('master_sheet', $master_sheet);
				$this->Session->write('section_detail', $section_detail);
				$this->Session->write('department_detail', $department_detail);
				$this->Session->write('college_detail', $college_detail);
				$this->Session->write('program_detail', $program_detail);
				$this->Session->write('program_type_detail', $program_type_detail);
				$this->Session->write('program_id', $program_id);
				$this->Session->write('program_type_id', $program_type_id);
				$this->Session->write('department_id', $department_id);
				$this->Session->write('academic_year_selected', $academic_year_selected);
				$this->Session->write('semester_selected', $semester_selected);

				$this->set(compact(
					'published_course_id',
					'publishedCourses',
					'programs',
					'program_types',
					'departments',
					'publishedCourses',
					'published_course_combo_id',
					'department_combo_id',
					'master_sheet',
					'section_detail',
					'college_detail',
					'department_detail',
					'program_detail',
					'program_type_detail',
					'academic_year',
					'semester',
					'program_id',
					'program_type_id',
					'department_id',
					'academic_year_selected',
					'semester_selected'
				));

				$this->render('master_sheet');
				return;
			}
		}

		$this->set(compact(
			'publishedCourses', 
			'programs', 
			'program_types', 
			'departments', 
			'publishedCourses', 
			'published_course_combo_id', 
			'department_combo_id', 
			'student_course_register_and_adds'
		));

		$this->render('view_grade');
	}

	function export_mastersheet_xls()
	{
		$this->autoLayout = false;
		$master_sheet = $this->Session->read('master_sheet');
		$section_detail = $this->Session->read('section_detail');
		$department_detail = $this->Session->read('department_detail');
		$college_detail = $this->Session->read('college_detail');
		$program_detail = $this->Session->read('program_detail');
		$program_type_detail = $this->Session->read('program_type_detail');
		$program_id = $this->Session->read('program_id');
		$program_type_id = $this->Session->read('program_type_id');
		$department_id = $this->Session->read('department_id');
		$academic_year = $this->Session->read('academic_year_selected');
		$semester = $this->Session->read('semester_selected');
		$filename = "Master_Sheet_" . (str_replace(' ', '_', (trim(str_replace('  ', ' ', $section_detail['name']))))) . '_' . $academic_year . '_' . $semester . '_' . date('Y-m-d');

		$this->set(compact(
			'master_sheet',
			'section_detail',
			'college_detail',
			'department_detail',
			'program_detail',
			'program_type_detail',
			'program_id',
			'program_type_id',
			'filename',
			'department_id',
			'academic_year',
			'semester'
		));

		$this->render('/Elements/master_sheet_xls');
	}

	function export_mastersheet_pdf()
	{
		$this->autoLayout = false;
		$master_sheet = $this->Session->read('master_sheet');
		$section_detail = $this->Session->read('section_detail');
		$department_detail = $this->Session->read('department_detail');
		$college_detail = $this->Session->read('college_detail');
		$program_detail = $this->Session->read('program_detail');
		$program_type_detail = $this->Session->read('program_type_detail');
		$program_id = $this->Session->read('program_id');
		$program_type_id = $this->Session->read('program_type_id');
		$department_id = $this->Session->read('department_id');
		$academic_year = $this->Session->read('academic_year_selected');
		$semester = $this->Session->read('semester_selected');
		$filename = "Master_Sheet_" . (str_replace(' ', '_', (trim(str_replace('  ', ' ', $section_detail['name']))))) . '_' . (str_replace('/', '-', $academic_year)) . '_' . $semester . '_' . date('Y-m-d');

		$this->set(compact(
			'master_sheet',
			'section_detail',
			'college_detail',
			'department_detail',
			'program_detail',
			'program_type_detail',
			'program_id',
			'program_type_id',
			'filename',
			'department_id',
			'academic_year',
			'semester'
		));

		$this->response->type('application/pdf');
		$this->render('/Elements/master_sheet_pdf');
	}


	function department_grade_report($section_id = null, $semester = null)
	{
		$this->__grade_report($section_id, $semester, 0);
	}

	function college_registrar_grade_report($section_id = null, $semester = null)
	{
		$this->__registrar_grade_report($section_id, $semester, 0);
	}

	function freshman_grade_report($section_id = null, $semester = null)
	{
		$this->__grade_report($section_id, $semester, 1);
	}

	private function __grade_report($section_id = null, $semester = null, $freshman_program = 0)
	{

		/*
		1. Retrieve list of sections based on the given search criteria
		2. Display list of sections
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade report in PDF for the selected students
		*/

		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list');
		$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		}
		
		$departments[0] = 0;

		//Get sections button is clicked
		if (isset($this->request->data['listSections'])) {
			
			$options = array();
			
			$options = array(
				'conditions' => array(
					'Section.academicyear' => $this->request->data['ExamGrade']['acadamic_year'],
					'Section.program_id' => $this->request->data['ExamGrade']['program_id'],
					'Section.program_type_id' => $this->request->data['ExamGrade']['program_type_id']
				),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				$options['conditions'][] = array('Section.college_id' => $this->college_id, 'Section.department_id IS NULL');
			} else {
				$options['conditions'][] = array('Section.department_id' => $this->department_id);
			}

			$sections = $this->ExamGrade->CourseAdd->Student->Section->find('list', $options);

			if (empty($sections)) {
				$this->Flash->info('There is no section by the selected search criteria.');
			} else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			}

			$academic_year_selected = $this->request->data['ExamGrade']['acadamic_year'];
			$semester_selected = $this->request->data['ExamGrade']['semester'];
			$program_id = $this->request->data['ExamGrade']['program_id'];
			$program_type_id = $this->request->data['ExamGrade']['program_type_id'];

		}

		//Section is selected from the combo box
		if (isset($this->request->data['getGradeReport']) || (!empty($section_id) && !empty($semester) && $section_id != 0)) {
			
			if (isset($this->request->data['getGradeReport'])) {
				$section_id = $this->request->data['ExamGrade']['section_id'];
				$semester = $this->request->data['ExamGrade']['semester_selected'];
			}

			$section_detail = $this->ExamGrade->CourseAdd->Student->Section->find('first', array('conditions' => array('Section.id' => $section_id), 'recursive' => -1 ));
			//Student list retrial
			$students_in_section = $this->ExamGrade->CourseAdd->Student->Section->getSectionStudents($section_id);

			//For search form default selection
			$section_published_course_detail = $this->ExamGrade->CourseAdd->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.section_id' => $section_detail['Section']['id'],
					'PublishedCourse.semester' => $semester
				),
				'recursive' => -1
			));
			
			if (!empty($section_published_course_detail)) {
				$academic_year_selected = $section_published_course_detail['PublishedCourse']['academic_year'];
			} else {
				$academic_year_selected = $section_detail['Section']['academicyear'];
			}

			$semester_selected = $semester;
			$program_id = $section_detail['Section']['program_id'];
			$program_type_id = $section_detail['Section']['program_type_id'];

			$options = array(
				'conditions' => array(
					'Section.academicyear' => $academic_year_selected,
					'Section.program_id' => $program_id,
					'Section.program_type_id' => $program_type_id
				),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				$options['conditions'][] = array('Section.college_id' => $this->college_id, 'Section.department_id IS NULL');
			} else {
				$options['conditions'][] = array('Section.department_id' => $this->department_id);
			}

			$sections = $this->ExamGrade->CourseAdd->Student->Section->find('list', $options);

			if (empty($sections)) {
				$this->Flash->info('There is no section by the selected search criteria.');
			} else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			}
		}

		//Get Grade Report button is clicked
		if (isset($this->request->data['getGradeReport'])) {
			$student_ids = array();
			
			if (!empty($this->request->data['Student'])) {
				foreach ($this->request->data['Student'] as $key => $student) {
					if ($student['gp'] == 1) {
						$student_ids[] = $student['student_id'];
					}
				}
			}

			if (empty($student_ids)) {
				$this->Flash->error('You are required to select at least one student.');
			} else {

				$student_copies = $this->ExamGrade->getStudentCopies($student_ids, $academic_year_selected, $semester);

				if (empty($student_copies)) {
					$this->Flash->info('There is no course registration for the selected students to display grade report.');
				} else {
					$this->set(compact('student_copies'));
					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('grade_report_pdf');
					return;
				}
			}
		}

		$this->set(compact('programs', 'program_types', 'departments', 'academic_year_selected', 'semester_selected', 'program_id', 'program_type_id', 'section_id', 'sections', 'students_in_section', 'student_copies'));
		$this->render('grade_report');

	}


	private function __registrar_grade_report($section_id = null, $semester = null, $freshman_program = 0)
	{
		/*
		1. Retrieve list of sections based on the given search criteria
		2. Display list of sections
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade report in PDF for the selected students
		*/
		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list');
		$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
		}

		// $departments[0] = "Pre";
		$departments = array();

		//Get sections button is clicked
		if (isset($this->request->data['listSections']) && !empty($this->request->data['listSections'])) {
			$options = array();
			
			$options = array(
				'conditions' => array(
					'Section.academicyear' => $this->request->data['ExamGrade']['acadamic_year'],
					'Section.program_id' => $this->request->data['ExamGrade']['program_id'],
					'Section.program_type_id' => $this->request->data['ExamGrade']['program_type_id']
				),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				if (isset($this->request->data['ExamGrade']['college_id'])) {
					$options['conditions'][] = array(
						'Section.college_id' => $this->request->data['ExamGrade']['college_id'],
						'Section.department_id IS NULL'
					);
				} else {
					$options['conditions'][] = array(
						'Section.college_id' => $this->request->data['ExamGrade']['college_id'],
						'Section.department_id IS NULL'
					);
				}
			} else {
				$options['conditions'][] = array(
					'Section.department_id' => $this->request->data['ExamGrade']['department_id']
				);
			}

			$sections = $this->ExamGrade->CourseAdd->Student->Section->find('list', $options);

			if (empty($sections)) {
				$this->Flash->info('There is no section by the selected search criteria.');
			} else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			}

			$academic_year_selected = $this->request->data['ExamGrade']['acadamic_year'];
			$semester_selected = $this->request->data['ExamGrade']['semester'];
			$program_id = $this->request->data['ExamGrade']['program_id'];
			$program_type_id = $this->request->data['ExamGrade']['program_type_id'];
			$department_id = $this->request->data['ExamGrade']['department_id'];
			$college_id = $this->request->data['ExamGrade']['college_id'];

		}

		//Section is selected from the combo box
		if (isset($this->request->data['getGradeReport']) || (!empty($section_id) && !empty($semester) && $section_id != 0)) {
			
			if (isset($this->request->data['getGradeReport'])) {
				$section_id = $this->request->data['ExamGrade']['section_id'];
				$semester = $this->request->data['ExamGrade']['semester_selected'];
			}

			$section_detail = $this->ExamGrade->CourseAdd->Student->Section->find('first', array(
				'conditions' => array(
					'Section.id' => $section_id
				),
				'recursive' => -1
			));

			//Student list retrial
			$students_in_section = $this->ExamGrade->CourseAdd->Student->Section->getSectionStudents($section_id);

			//For search form default selection
			$section_published_course_detail = $this->ExamGrade->CourseAdd->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.section_id' => $section_detail['Section']['id'],
					'PublishedCourse.semester' => $semester
				),
				'recursive' => -1
			));

			if (!empty($section_published_course_detail)) {
				$academic_year_selected = $section_published_course_detail['PublishedCourse']['academic_year'];
			} else {
				$academic_year_selected = $section_detail['Section']['academicyear'];
			}

			$semester_selected = $semester;
			$program_id = $section_detail['Section']['program_id'];
			$program_type_id = $section_detail['Section']['program_type_id'];
			$department_id = $section_detail['Section']['department_id'];
			$college_id = $section_detail['Section']['college_id'];

			$options = array(
				'conditions' => array(
					'Section.academicyear' => $academic_year_selected,
					'Section.program_id' => $program_id,
					//'Section.department_id' => $department_id,
					'Section.program_type_id' => $program_type_id
				),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				$options['conditions'][] = array(
					'Section.college_id' => $college_id,
					'Section.department_id IS NULL'
				);
			} else {
				$options['conditions'][] = array('Section.department_id' => $department_id);
			}

			$sections = $this->ExamGrade->CourseAdd->Student->Section->find('list', $options);

			if (empty($sections)) {
				$this->Flash->info('There is no section by the selected search criteria.');
			} else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			}
		}

		//Get Grade Report button is clicked
		if (isset($this->request->data['getGradeReport'])) {
			
			$student_ids = array();
			
			if (isset($this->request->data['Student']) && !empty($this->request->data['Student'])) {
				foreach ($this->request->data['Student'] as $key => $student) {
					if ($student['gp'] == 1) {
						$student_ids[] = $student['student_id'];
					}
				}
			}

			if (empty($student_ids)) {
				$this->Flash->error('You are required to select at least one student.');
			} else {

				$student_copies = $this->ExamGrade->getStudentCopies($student_ids, $academic_year_selected, $semester);
				
				if (empty($student_copies)) {
					$this->Flash->info('There is no course registration for the selected students to display grade report.');
				} else {
					$this->set(compact('student_copies'));
					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('grade_report_pdf');
					unset($this->request->data['Student']);
					return;
				}
			}
		}

		if (!empty($this->department_ids)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array(
				'conditions' => array(
					'Department.id' => $this->department_ids,
					'Department.active' => 1
				)
			));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->ExamGrade->CourseRegistration->Student->College->find('list', array(
				'conditions' => array(
					'College.id' => $this->college_ids,
					'College.active' => 1
				)
			));
		}

		$acyear_registrar = $this->AcademicYear->academicYearInArray(date('Y') - 5, date('Y'));

		$this->set(compact(
			'programs',
			'program_types',
			'departments',
			'academic_year_selected',
			'acyear_registrar',
			'semester_selected',
			'program_id',
			'program_type_id',
			'section_id',
			'sections',
			'students_in_section',
			'student_copies',
			'colleges',
			'department_id',
			'college_id'
		));

		$this->render('grade_report_registrar');
	}

	public function data_entry_interface()
	{
		if ($this->role_id == ROLE_REGISTRAR) {
			$this->__data_entry_interface();
		}
	}

	public function grade_update()
	{
		if ($this->role_id == ROLE_REGISTRAR) {
			$this->__data_entry_interface_edit();
		}
	}

	public function academic_status_grade_interface()
	{
		$this->__academic_status_grade_interface();
	}

	private function __data_entry_interface($selected = null)
	{
		/*
		1. Retrieve list of sections based on the given search criteria
		2. Display list of sections
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade report in PDF for the selected students
		*/
		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list');
		$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		$departments = array();

		//Get Grade Report button is clicked
		if (isset($this->request->data['saveGrade']) && !empty($this->request->data['saveGrade'])) {
			$publishedCoursesId = array();
			$student_ids = array();
			$studentId = null;
			$courseRegistrationAndGrade = array();
			$count = 0;
			$scaleNotFound['freq'] = 0;
			foreach ($this->request->data['CourseRegistration'] as $key => $student) {
				if ($student['grade_scale_id'] == 0) {
					$scaleNotFound['freq']++;
				}
				if ($student['gp'] == 1 && $student['grade_scale_id'] != 0 && !empty($student['grade'])) {
					$student_ids[] = $student['student_id'];
					$studentId = $student['student_id'];
					$courseRegistrationAndGrade[$count]['CourseRegistration'] = $student;
					debug($student);
					$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
					$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

					$publishedCoursesId = $student['published_course_id'];
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['grade'] = $student['grade'];
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval'] = 1;
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['grade_scale_id'] = $student['grade_scale_id'];
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval'] = 1;
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');

					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
				}
				$count++;
			}

			if (!empty($courseRegistrationAndGrade)) {
				//debug($courseRegistrationAndGrade);
				// die;
				foreach ($courseRegistrationAndGrade as $data) {

					$this->ExamGrade->CourseRegistration->saveAll($data, array('validate' => false));
				}
				if ($scaleNotFound['freq'] > 0) {
					$this->Session->setFlash('<span></span>' . __('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'), 'default', array('class' => 'success-box success-message'));
				} else {
					$this->Session->setFlash('<span></span>' . __('You have entered the data successfully.'), 'default', array('class' => 'success-box success-message'));
				}
				//$isTheDeletionSuccessful=ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id'=>$studentId),false);
				//$statusgenerated=ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($studentId,$publishedCoursesId);
			} else {
				if ($scaleNotFound['freq'] > 0) {
					$this->Session->setFlash('<span></span>  ' . __('It is required to have defined grade scale in order to perform data entry. ' . $scaleNotFound['freq'] . '  course(s) don\'t have scale, please ask either the registrar or department to define scale.'), 'default', array('class' => 'info-box info-message'));
					$this->request->data['listPublishedCourse'] = true;
				} else {
					if (empty($student_ids)) {
						$this->request->data['listPublishedCourse'] = true;
						$this->Session->setFlash('<span></span> ' . __('You are required to select at least one student.'), 'default', array('class' => 'error-box error-message'));
					}
				}
			}
		}

		//debug($this->request->data);
		//Course Add popup button clicked
		if (isset($this->request->data['addCoursesGrade']) && !empty($this->request->data['addCoursesGrade'])) {
			debug($this->request->data);
			$publishedCoursesId = array();
			$student_ids = array();
			$studentId = null;
			$courseAddAndGrade = array();
			$count = 0;
			$scaleNotFound['freq'] = 0;
			foreach ($this->request->data['CourseAdd'] as $key => $student) {
				if ($student['grade_scale_id'] == 0) {
					$scaleNotFound['freq']++;
				}
				if (
					$student['gp'] == 1 && $student['grade_scale_id'] != 0 &&
					!empty($student['grade'])
				) {
					$student_ids[] = $student['student_id'];
					$studentId = $student['student_id'];
					$courseAddAndGrade[$count]['CourseAdd'] = $student;
					$publishedCoursesId = $student['published_course_id'];
					$courseAddAndGrade[$count]['ExamGrade'][$count]['grade'] = $student['grade'];
					$courseAddAndGrade[$count]['ExamGrade'][$count]['department_approval'] = 1;
					$courseAddAndGrade[$count]['ExamGrade'][$count]['grade_scale_id'] = $student['grade_scale_id'];
					$courseAddAndGrade[$count]['ExamGrade'][$count]['department_reason'] = 'Via backend data entry interface';
					$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_approval'] = 1;
					$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
					$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');

					$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->get_academicYearBegainingDate($student['academic_year']);
					$courseAddAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseAddAndGrade[$count]['ExamGrade'][$count]['created'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseAddAndGrade[$count]['ExamGrade'][$count]['modified'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseAddAndGrade[$count]['CourseAdd']['created'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
					$courseAddAndGrade[$count]['CourseAdd']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate(
						$student['academic_year'],
						$student['semester']
					);
				}
				$count++;
			}

			if (!empty($courseAddAndGrade)) {

				foreach ($courseAddAndGrade as $data) {

					$this->ExamGrade->CourseAdd->saveAll($data, array('validate' => false));
				}
				if ($scaleNotFound['freq'] > 0) {
					$this->Session->setFlash('<span></span>' . __('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'), 'default', array('class' => 'success-box success-message'));
				} else {
					$this->Session->setFlash('<span></span>' . __('You have entered the add course(s)  data successfully.'), 'default', array('class' => 'success-box success-message'));
				}
				//$isTheDeletionSuccessful=ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id'=>$studentId),false);
				//$statusgenerated=ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($studentId,$publishedCoursesId);
			} else {
				if ($scaleNotFound['freq'] > 0) {
					$this->Session->setFlash('<span></span>  ' . __('It is required to have defined grade scale in order to perform data entry. ' .
						$scaleNotFound['freq'] . '  course(s) don\'t have scale, please ask either the registrar or department to define scale.'), 'default', array('class' => 'info-box info-message'));
					$this->request->data['listPublishedCourse'] = true;
				} else {
					if (empty($student_ids)) {
						$this->request->data['listPublishedCourse'] = true;
						$this->Session->setFlash('<span></span> ' . __('You are required to select at least one student.'), 'default', array('class' => 'error-box error-message'));
					}
				}
			}
			$this->request->data['ExamGrade']['studentnumber'] = $this->request->data['Student']['studentnumber'];
			$this->request->data['ExamGrade']['semester'] = $this->request->data['Student']['semester'];
			$this->request->data['ExamGrade']['acadamic_year'] = str_replace('-', '/', $this->request->data['Student']['acadamic_year']);
			$this->request->data['listPublishedCourse'] = true;
		}

		//Get published course for the selected student
		debug($this->request->data);

		if (isset($this->request->data['listPublishedCourse']) && !empty($this->request->data['listPublishedCourse'])) {

			$department_ids = array();
			$everyThingOk = false;
			$selectedStudent = array();
			if (!empty($this->department_ids)) {
				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array('Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])),
					'contain' => array('StudentsSection')
				));
				$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);

				if (!empty($selectedStudent)) {
					if (!in_array($selectedStudent['Student']['department_id'], $this->department_ids)) {
						$this->Session->setFlash('<span></span>' .
							__('You don\'t have the privilage to enter data for ' . $this->request->data['ExamGrade']['studentnumber'] . '.'), 'default', array('class' => 'info-box info-message'));
					} else {
						$everyThingOk = true;
					}
				} else {
					$this->Session->setFlash('<span></span>' . __(' ' . $this->request->data['ExamGrade']['studentnumber'] . ' is not a valid student number.'), 'default', array('class' => 'info-box info-message'));
				}
			} else if (!empty($this->college_ids)) {

				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array('Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])),
					'contain' => array('StudentsSection')
				));
				$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);

				if (!empty($selectedStudent)) {
					if (!in_array($selectedStudent['Student']['college_id'], $this->college_ids)) {
						$this->Session->setFlash('<span></span>' .
							__('You don\'t have the privilage to enter data for ' . $this->request->data['ExamGrade']['studentnumber'] . '.'), 'default', array('class' => 'info-box info-message'));
					} else {
						$everyThingOk = true;
					}
				} else {
					$this->Session->setFlash('<span></span>' . __(' ' . $this->request->data['ExamGrade']['studentnumber'] . ' is not a valid student number.'), 'default', array('class' => 'info-box info-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>' . __('You don\'t have the privilage to enter data for the selected student.'), 'default', array('class' => 'info-box info-message'));
			}

			if ($everyThingOk && !empty($selectedStudent)) {

				/*
			 * find the published course in that semester and academic year
			 * does that published course has registration, grade submitted, then disable in the interface data entry

			 */
				debug($selectedStudent);

				$yearLevelAndSemesterOfStudent = $this->ExamGrade->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevel($selectedStudent['Student']['id'], $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
				$graduated = $this->ExamGrade->CourseRegistration->Student->SenateList->find(
					'count',
					array(
						'conditions' => array('SenateList.student_id' => $selectedStudent['Student']['id']),
						'recursive' => -1
					)
				);

				$student_academic_profile = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id'], $this->AcademicYear->current_academicyear());

				$this->set(compact('student_academic_profile'));
				$selectedStudentDetails = $this->ExamGrade->getStudentCopy($selectedStudent['Student']['id'], $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
				$admission_explode = explode('-', $selectedStudentDetails['Student']['admissionyear']);
				$studentAdmissionYear = $this->AcademicYear->get_academicyear($admission_explode[1], $admission_explode[0]);

				if (empty($selectedStudentDetails['courses'])) {
					// there is no registration, so find the published course for that students

					$publishedCourses = $this->ExamGrade->getPublishedCourseIfExist(
						$selectedStudentDetails['Student']['department_id'],
						$this->request->data['ExamGrade']['acadamic_year'],
						$this->request->data['ExamGrade']['semester'],
						$selectedStudentDetails['Student']['program_id'],
						$selectedStudentDetails['Student']['program_type_id'],
						$selectedStudentDetails,
						$studentAdmissionYear,
						$this->AcademicYear->current_academicyear()
					);

					$studentbasic = $selectedStudentDetails;

					$this->set(compact('publishedCourses', 'studentbasic'));
				} else if (!empty($selectedStudentDetails['courses'])) {

					$publishedCourses = $this->ExamGrade->getPublishedCourseIfExist(
						$selectedStudentDetails['Student']['department_id'],
						$this->request->data['ExamGrade']['acadamic_year'],
						$this->request->data['ExamGrade']['semester'],
						$selectedStudentDetails['Student']['program_id'],
						$selectedStudentDetails['Student']['program_type_id'],
						$selectedStudentDetails,
						$studentAdmissionYear,
						$this->AcademicYear->current_academicyear()
					);
					foreach ($publishedCourses['courses'] as $key => &$value) {
						if ($value['PublishedCourse']['readOnly']) {

							unset($publishedCourses['courses'][$key]);
						}
					}

					$publishedCourses['courses'] = $this->__mergePublishedCourse($publishedCourses, $selectedStudentDetails);

					$studentbasic = $selectedStudentDetails;


					$this->set(compact('publishedCourses', 'studentbasic', 'graduated'));
				}
				$this->set(compact('graduated'));
			}
		}


		if (!empty($this->department_ids)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->ExamGrade->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids)));
		}

		$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 8, date('Y') - 1);

		$this->set(compact(
			'programs',
			'program_types',
			'departments',
			'academic_year_selected',
			'acyear_list',
			'semester_selected',
			'program_id',
			'program_type_id',
			'section_id',
			'sections',
			'students_in_section',
			'student_copies',
			'colleges',
			'department_id',
			'college_id',
			'acyear_list'
		));
		$this->render('data_entry_interface');
	}


	private function __data_entry_interface_edit($selected = null)
	{
		/*
		1. Retrieve list of sections based on the given search criteria
		2. Display list of sections
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade report in PDF for the selected students
		*/
		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$loggedUser = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $this->Auth->user('id')), 'recursive' => -1));

		$departments = array();

		//Get Grade Report button is clicked
		if (isset($this->request->data['saveGrade']) && !empty($this->request->data['saveGrade'])) {
			
			$publishedCoursesId = array();
			$student_ids = array();
			$studentId = null;
			$courseRegistrationAndGrade = array();
			$count = 0;
			$scaleNotFound['freq'] = 0;

			$gradeChangesToDeleteOnSuccessfullGradeUpdate = array();

			if (!empty($this->request->data['CourseRegistration'])) {

				debug($this->request->data['CourseRegistration']);
				$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
				debug($current_acy_and_semester);

				foreach ($this->request->data['CourseRegistration'] as $key => $student) {
					
					if ($student['grade_scale_id'] == 0) {
						$scaleNotFound['freq']++;
					}

					if (isset($student['gp']) && $student['gp'] == 1 && $student['grade_scale_id'] != 0 && !empty($student['grade'])) {

						// check if the grade scale and grade belongs to the same if not find another grade scale that matches the published course
						$gradeTypes = ClassRegistry::init('GradeScale')->find('first', array('conditions' => array('GradeScale.id' => $student['grade_scale_id']), 'recursive' => -1));

						debug($gradeTypes);

						debug($student);

						$student_ids[] = $student['student_id'];
						$studentId = $student['student_id'];

						$courseRegistrationAndGrade[$count]['CourseRegistration'] = $student;
						
						//$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
						//$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

						// for checking if this is the cause of grade hides in student profile and student copies, Neway
						/* if (empty($student['course_registration_id'])) {

							$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
							$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

							//$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = date('Y-m-d H:i:s');
							//$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = date('Y-m-d H:i:s');
						} */

						$publishedCoursesId[] = $student['published_course_id'];

						// this should be commented as it updates the existing grade and doesn't keep grade history. there should be a separate entry with different grade id for every time backdated grade entry is make for any grade. Neway 
						// ROLLING BACK TO PREVIOUS STATE
						//uncommented it to save the grade on the last approved grade and keep associated grade change history the the lastest approved exam_grade_id, commenting this will add new grade entry at the bottom leaving no grade history view.
						if (!empty($student['grade_id'])) {
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['id'] = $student['grade_id'];
						}

						if (!empty($student['id'])) {
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['course_registration_id'] = $student['id'];
						}
						//debug($student);

						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['grade'] = $student['grade'];
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval'] = 1;
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['grade_scale_id'] = $student['grade_scale_id'];
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_reason'] = 'Via backend data entry interface';
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval'] = 1;
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';

						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');

						/* $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);

						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
						
						$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
						$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']); */


						$check_registered = $this->ExamGrade->CourseRegistration->find('first', array(
							'conditions' => array(
								'CourseRegistration.academic_year' => $this->request->data['ExamGrade']['acadamic_year'],
								'CourseRegistration.student_id' => $student['student_id'],
								'CourseRegistration.semester' => $this->request->data['ExamGrade']['semester'],
								'CourseRegistration.published_course_id' =>  $student['published_course_id'],
							),
							'contain' => array(
								'ExamGrade' => array(
									'conditions' => array( 
										'ExamGrade.registrar_approval' => 1
									),
									'ExamGradeChange' => array(
										'conditions' => array( 
											'ExamGradeChange.registrar_approval' => 1
										),
										'order' => array('ExamGradeChange.id' => 'DESC'),
										'limit' => 1,
									),
									'order' => array('ExamGrade.id' => 'DESC'),
									'limit' => 1,
								)
							),
							'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC')
						));

						if (empty($check_registered)) {
							$check_registered = $this->ExamGrade->CourseRegistration->find('first', array(
								'conditions' => array(
									'CourseRegistration.academic_year' => $this->request->data['ExamGrade']['acadamic_year'],
									'CourseRegistration.student_id' => $student['student_id'],
									'CourseRegistration.semester' => $this->request->data['ExamGrade']['semester'],
								),
								'contain' => array(
									'ExamGrade' => array(
										'conditions' => array( 
											'ExamGrade.registrar_approval' => 1
										),
										'ExamGradeChange' => array(
											'conditions' => array( 
												'ExamGradeChange.registrar_approval' => 1
											),
											'order' => array('ExamGradeChange.id' => 'DESC'),
											'limit' => 1,
										),
										'order' => array('ExamGrade.id' => 'DESC'),
										'limit' => 1,
									)
								),
								'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC')
							));

							$published_course_registration_found = 0;

						} else {
							$published_course_registration_found = 1;
						}

						debug($check_registered);
						//debug($published_course_registration_found);

						if (!empty($check_registered)) {
							debug(($published_course_registration_found ? 'Published Course ' : 'Other ') . 'Course Registration found for: ' . $check_registered['CourseRegistration']['academic_year']. ', Semester: ' . $check_registered['CourseRegistration']['semester'] . ',  Created: ' . $check_registered['CourseRegistration']['created']);

							if ($current_acy_and_semester['academic_year'] == $check_registered['CourseRegistration']['academic_year'] && $current_acy_and_semester['semester'] ==  $check_registered['CourseRegistration']['semester']) {
								
								$debug_message = 'Current ACY and Semester Matches with the Selected ACY and Semester';
								debug($debug_message);

								//$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] =  date('Y-m-d H:i:s');
								//$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d h:i:s');
							
								$creg_time_ammended = (!empty($check_registered['CourseRegistration']['created']) && $check_registered['CourseRegistration']['created'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($check_registered['CourseRegistration']['created']))) : date('Y-m-d H:i:s'));
								$grade_entry_time_ammended = (!empty($check_registered['ExamGrade'][0]['created']) && $check_registered['ExamGrade'][0]['created'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($check_registered['ExamGrade'][0]['created']))) : date('Y-m-d h:i:s'));

								if (!$published_course_registration_found) {
									$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $creg_time_ammended;
									$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $grade_entry_time_ammended;
								} else {
									$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = (!empty($check_registered['CourseRegistration']['created']) && $check_registered['CourseRegistration']['created'] != '0000-00-00 00:00:00' ? $check_registered['CourseRegistration']['created'] : date('Y-m-d H:i:s'));
									$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = date('Y-m-d H:i:s');
									//$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = (!empty($check_registered['ExamGrade'][0]['created']) && $check_registered['ExamGrade'][0]['created'] != '0000-00-00 00:00:00' ? $check_registered['ExamGrade'][0]['created'] : date('Y-m-d H:i:s'));
									$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $grade_entry_time_ammended;
								}
								
								//$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] =  date('Y-m-d H:i:s');
							} else {

								$debug_message = 'Current ACY and Semester doesn\'t match with the Selected ACY and Semester';
								debug($debug_message);
								//!empty($check_registered['ExamGrade'][0]['created']) ? debug('Exam Grade Created Date: '. $check_registered['ExamGrade'][0]['created']) : debug('Exam Grade Created Date not found using Course Registration Date: ' . $check_registered['CourseRegistration']['created']);

								//$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $check_registered['CourseRegistration']['created'];
								//$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $check_registered['CourseRegistration']['created']; //date('Y-m-d h:i:s');

								$creg_time_ammended = (!empty($check_registered['CourseRegistration']['created']) && $check_registered['CourseRegistration']['created'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($check_registered['CourseRegistration']['created']))) : $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']));
								$grade_entry_time_ammended = (!empty($check_registered['ExamGrade'][0]['created']) && $check_registered['ExamGrade'][0]['created'] != '0000-00-00 00:00:00' ? date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($check_registered['ExamGrade'][0]['created']))) : $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']));

								$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $grade_entry_time_ammended;

								if (!$published_course_registration_found) {
									$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $creg_time_ammended;
								} else {
									$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = (!empty($check_registered['CourseRegistration']['created']) && $check_registered['CourseRegistration']['created'] != '0000-00-00 00:00:00' ? $check_registered['CourseRegistration']['created'] : date('Y-m-d H:i:s'));
									$courseRegistrationAndGrade[$count]['CourseRegistration']['modified']  = date('Y-m-d H:i:s');
								}
								
								$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d H:i:s');
								
								//$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = (!empty($check_registered['ExamGrade'][0]['created']) && $check_registered['ExamGrade'][0]['created'] != '0000-00-00 00:00:00' ? $check_registered['ExamGrade'][0]['created'] : $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']));
								//$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = (!empty($check_registered['ExamGrade'][0]['created']) && $check_registered['ExamGrade'][0]['created'] != '0000-00-00 00:00:00' ? $check_registered['ExamGrade'][0]['created'] : $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']));
							}


							// check if there is a grade change and for the grade to be updated delete/update it.
							if (isset($check_registered['ExamGrade'][0]['grade']) && $published_course_registration_found) {
								
								debug('Exam Grade Created Date: '. $check_registered['ExamGrade'][0]['created']);
								debug('Exam Grade Ammendmed Date: '. date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($check_registered['ExamGrade'][0]['created']))));
								debug($check_registered['ExamGrade'][0]['grade']);

								$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($check_registered['ExamGrade'][0]['created'])));
								$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d H:i:s');

								if (!empty($check_registered['ExamGrade'][0]['ExamGradeChange'][0]['id'])) {
									debug($check_registered['ExamGrade'][0]['ExamGradeChange'][0]['id']);
									//$gradeChangesToDeleteOnSuccessfullGradeUpdate[$check_registered['ExamGrade'][0]['id']] = $check_registered['ExamGrade'][0]['ExamGradeChange'][0]['id'];
								}

							}
						} else {

							// No course registration is found for the student either by published course id or by the given acy and semester.

							if ($current_acy_and_semester['academic_year'] == $this->request->data['ExamGrade']['acadamic_year'] && $current_acy_and_semester['semester'] == $this->request->data['ExamGrade']['semester']) {
								$debug_message = 'No course registration found but Current ACY and Semester Matches with the Selected ACY and Semester';
								debug($debug_message);

								// $courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = date('Y-m-d h:i:s');
								// $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s');
								// $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d h:i:s');

								$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] =  date('Y-m-d h:i:s');
							} else {
								$debug_message = 'No course registration found and Current ACY and Semester doesn\'t match with the Selected ACY and Semester';
								debug($debug_message);
								// $courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
								// $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
								// $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d h:i:s'); //$this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);

								$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
							}
						}

						// to make update date modified to latest date.

						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d H:i:s');

						/* if (!empty($check_registered)) {
							debug($check_registered['CourseRegistration']['created']);
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $check_registered['CourseRegistration']['created'];
							$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d h:i:s'); //$check_registered['CourseRegistration']['created'];
						} else {

							$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
							debug($current_acy_and_semester);

							if ($current_acy_and_semester['academic_year'] == $this->request->data['ExamGrade']['acadamic_year'] && $current_acy_and_semester['semester'] == $this->request->data['ExamGrade']['semester']) {
								$debug_message = 'Current Academic Year and Semester Matches with the Selected Academic Year and Semester';
								debug($debug_message);

								$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s');
								$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d h:i:s');

							} else {

								$debug_message = 'Current Academic Year and Semester doesn\'t match with the Selected Academic Year and Semester';
								debug($debug_message);

								$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
								$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d h:i:s');
							}
						} */

						unset($this->request->data['CourseRegistration'][$key]['gp']);
					}

					$count++;
				}
			}

			if (!empty($courseRegistrationAndGrade)) {
				
				$updated_grades_count = 0;
				$failed_grades_count = 0;

				$requested_grade_update_count = count($courseRegistrationAndGrade);

				debug($courseRegistrationAndGrade);
				debug($requested_grade_update_count);
				
				foreach ($courseRegistrationAndGrade as $data) {
					unset($data['CourseRegistration']['gp']);
					debug($gradeChangesToDeleteOnSuccessfullGradeUpdate);
					
					if ($this->ExamGrade->CourseRegistration->saveAll($data, array('validate' => false))) {
						$updated_grades_count++;
						debug($gradeChangesToDeleteOnSuccessfullGradeUpdate);
						if (!empty($gradeChangesToDeleteOnSuccessfullGradeUpdate) && in_array($data['CourseRegistration']['grade_id'], array_keys($gradeChangesToDeleteOnSuccessfullGradeUpdate))) {
							$date_modified = '"'. date('Y-m-d h:i:s') . '"';
							$reject_reason = '"'. 'Rejected following Grade Update Via Back Dated Data Entry' .'"';
							ClassRegistry::init('ExamGradeChange')->updateAll(array('ExamGradeChange.registrar_approval' => -1, 'ExamGradeChange.registrar_reason' => $reject_reason, 'ExamGradeChange.modified' => $date_modified), array('ExamGradeChange.exam_grade_id' => $data['CourseRegistration']['grade_id']));
						}
					} else {
						$failed_grades_count++;
					}
				}

				if ($updated_grades_count) {
					if ($scaleNotFound['freq'] > 0) {
						$this->Flash->success('You have entered ' . $updated_grades_count . ' exam grades successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.');
					} else {
						$this->Flash->success('You have entered ' . $updated_grades_count . ' exam grades successfully.');
					}
				} else {
					$this->Flash->error('No data is entered. Something went wrong.');
				}

				unset($courseRegistrationAndGrade);
				unset($this->request->data['saveGrade']);

				// $isTheDeletionSuccessful = ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id'=>$studentId),false);
				//$statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($student_ids, $publishedCoursesId);

				if ($updated_grades_count && !empty($student_ids)) {
					foreach ($student_ids as $key => $student_id) {
						$statusgenerated = ClassRegistry::init('StudentExamStatus')->regenerate_all_status_of_student_by_student_id($student_id);
					}
				}
				
			} else {
				if ($scaleNotFound['freq'] > 0) {
					$this->Flash->info('It is required to have a defined grade scale in order to perform data entry. ' . $scaleNotFound['freq'] . '  course(s) don\'t have grade scale, Please communicate the registrar to define an appropraite grade scale for these courses.');
					$this->request->data['listPublishedCourse'] = true;
				} else {
					if (empty($student_ids)) {
						$this->request->data['listPublishedCourse'] = true;
						$this->Flash->info('You are required to select at least one course to enter grade.');
					}
				}
			}
		}

		//deleteGrade button is clicked
		if (isset($this->request->data['deleteGrade']) && !empty($this->request->data['deleteGrade'])) {

			$publishedCoursesId = array();
			$student_ids = array();
			$studentId = null;
			$courseRegistrationAndGrade = array();
			$courseAddAndGrade = array();
			$count = 0;
			$scaleNotFound['freq'] = 0;
			$errorMessage = '';

			debug($this->request->data['CourseRegistration']);

			$allowed_grades_for_deletion = Configure::read('allowed_grades_for_deletion');

			if (!empty($this->request->data['CourseRegistration'])) {
				foreach ($this->request->data['CourseRegistration'] as $key => $student) {
					if (isset($student['gp']) && $student['gp'] == 1 && (in_array($student['grade'], $allowed_grades_for_deletion) || empty($student['grade']) || (ALLOW_REGISTRAR_ADMIN_TO_DELETE_VALID_GRADES && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1))) {

						$student_ids[] = $student['student_id'];
						$studentId = $student['student_id'];

						debug($student);

						$courseRegistrationAndGrade[$count]['CourseRegistration'] = $student;
						$publishedCoursesId = $student['published_course_id'];

						if (!empty($student['grade_id'])) {
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['id'] = $student['grade_id'];
						}

						if (!empty($student['course_registration_id'])) {
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['course_registration_id'] = $student['course_registration_id'];
						}

						if (!empty($student['id'])) {
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['course_registration_id'] = $student['id'];
						}

						if (!empty($student['course_add_id'])) {
							$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['course_add_id'] = $student['course_add_id'];
						}
					} else if (isset($student['gp']) && $student['gp'] == 1 && (!in_array($student['grade'], $allowed_grades_for_deletion))) {
						$errorMessage .= $student['grade'] . ',';
					}
					$count++;
				}
			}

			if (!empty($courseRegistrationAndGrade)) {
				$courseAddandRegistrationExamGradeIds = array();
				foreach ($courseRegistrationAndGrade as $data) {
					if (isset($data['ExamGrade']) && !empty($data['ExamGrade'])) {
						foreach ($data['ExamGrade'] as $k => $v) {
							
							if (isset($v['id']) && !empty($v['id'])) {
								$courseAddandRegistrationExamGradeIds['ExamGrade'][] = $v['id'];
							}
							
							if (!empty($v['course_registration_id'])) {
								$courseAddandRegistrationExamGradeIds['CourseRegistration'][] = $v['course_registration_id'];
							}

							if (!empty($v['course_add_id'])) {
								$courseAddandRegistrationExamGradeIds['CourseAdd'][] = $v['course_add_id'];
							}
						}
					} else {
						debug($data);
						if (!empty($data['CourseRegistration']['id'])) {
							$courseAddandRegistrationExamGradeIds['CourseRegistration'][] = $data['CourseRegistration']['id'];
						} else if (!empty($data['CourseAdd']['id'])) {
							$courseAddandRegistrationExamGradeIds['CourseAdd'][] = $data['CourseAdd']['id'];
						}
					}
				}

				debug($courseAddandRegistrationExamGradeIds);

				if (!empty($courseAddandRegistrationExamGradeIds['CourseRegistration'])) {
					debug($courseAddandRegistrationExamGradeIds);
					// allow to delete by exam grade id instead of course registration ID, It is more preferrable?? Neway
					if ($this->ExamGrade->CourseRegistration->deleteAll(array('CourseRegistration.id' => $courseAddandRegistrationExamGradeIds['CourseRegistration']), false)) {
						if (!empty($courseAddandRegistrationExamGradeIds['ExamGrade'])) {
							if ($this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false)) {

								debug($this->ExamGrade->ExamGradeChange->deleteAll(array('ExamGradeChange.exam_grade_id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false));

								if (!empty($student_ids)) {
									foreach ($student_ids as $key => $student_id) {
										$statusgenerated = ClassRegistry::init('StudentExamStatus')->regenerate_all_status_of_student_by_student_id($student_id);
									}
								}

								$this->Flash->success('You have successfully deleted the selected ' . (count($courseAddandRegistrationExamGradeIds['ExamGrade'])).  ' Exam Grades, Course Registrations, Assesments and Grade Channges, if any.' . (!empty($errorMessage) ? ' Skipped deleting the following selected grades '. $errorMessage . ' Only ' . join(', ', $allowed_grades_for_deletion). ' are allowed to delete.' : ''));
								unset($courseAddandRegistrationExamGradeIds['ExamGrade']);
							}
						} else {
							$this->Flash->success('You have successfully deleted the selected ' . (count($courseAddandRegistrationExamGradeIds['CourseRegistration'])).  ' Course Registrationa and Assesments, if any.');
						}
						unset($courseAddandRegistrationExamGradeIds['CourseRegistration']);
					}
				}

				if (!empty($courseAddandRegistrationExamGradeIds['CourseAdd'])) {
					if ($this->ExamGrade->CourseAdd->deleteAll(array('CourseAdd.id' => $courseAddandRegistrationExamGradeIds['CourseAdd']), false)) {
						if ($this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false)) {

							debug($this->ExamGrade->ExamGradeChange->deleteAll(array('ExamGradeChange.exam_grade_id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false));

							if (!empty($student_ids)) {
								foreach ($student_ids as $key => $student_id) {
									$statusgenerated = ClassRegistry::init('StudentExamStatus')->regenerate_all_status_of_student_by_student_id($student_id);
								}
							}
	
							$this->Flash->success('You have successfully deleted the selected  ' . (count($courseAddandRegistrationExamGradeIds['CourseAdd'])).  ' Exam Grade and data associated to it i.e, Course Adds, Assesments and Grade Channges, if any.' . (!empty($errorMessage) ? ' Skipped deleting the following selected grades '. $errorMessage . ' Only ' . join(', ', $allowed_grades_for_deletion). ' are allowed to delete.' : ''));
							unset($courseAddandRegistrationExamGradeIds['CourseAdd']);
						}
					}
				} 

				/* if (!empty($errorMessage) && empty($courseAddandRegistrationExamGradeIds['CourseAdd']) && empty($courseAddandRegistrationExamGradeIds['ExamGrade'])) {
					$this->Flash->error($errorMessage . ' is not in allowed grades to delete');
				} */
			} else {
				if (empty($student_ids)) {
					$this->request->data['listPublishedCourse'] = true;
					$this->Flash->error('You are required to select at least one course grade to delete and the grade must be in the allowed list of grades to delete(' . join(', ', $allowed_grades_for_deletion). ')');
				}
			}
		}

		//Course Add popup button clicked
		if (isset($this->request->data['addCoursesGrade']) && !empty($this->request->data['addCoursesGrade'])) {
			debug($this->request->data);
			
			$publishedCoursesId = array();
			$student_ids = array();
			$studentId = null;
			$courseAddAndGrade = array();
			$count = 0;
			$scaleNotFound['freq'] = 0;

			if (!empty($this->request->data['CourseAdd'])) {
				foreach ($this->request->data['CourseAdd'] as $key => $student) {
					if ($student['grade_scale_id'] == 0) {
						$scaleNotFound['freq']++;
					}
					if (isset($student['gp']) && $student['gp'] == 1 && $student['grade_scale_id'] != 0 && !empty($student['grade'])) {
						
						$student_ids[] = $student['student_id'];
						$studentId = $student['student_id'];

						$courseAddAndGrade[$count]['CourseAdd'] = $student;
						$publishedCoursesId = $student['published_course_id'];

						$courseAddAndGrade[$count]['ExamGrade'][$count]['grade'] = $student['grade'];
						$courseAddAndGrade[$count]['ExamGrade'][$count]['department_approval'] = 1;
						$courseAddAndGrade[$count]['ExamGrade'][$count]['grade_scale_id'] = $student['grade_scale_id'];
						$courseAddAndGrade[$count]['ExamGrade'][$count]['department_reason'] = 'Via backend data entry interface';
						$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_approval'] = 1;
						$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
						$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
						
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approved_by'] = $this->Auth->user('id');
						$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approved_by'] = $this->Auth->user('id');

						$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
						$courseAddAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

						//$courseAddAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = date('Y-m-d H:i:s');
						//$courseAddAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = date('Y-m-d H:i:s');
						
						$courseAddAndGrade[$count]['ExamGrade'][$count]['created'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
						$courseAddAndGrade[$count]['ExamGrade'][$count]['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

						//$courseAddAndGrade[$count]['ExamGrade'][$count]['created'] = date('Y-m-d H:i:s');
						//$courseAddAndGrade[$count]['ExamGrade'][$count]['modified'] = date('Y-m-d H:i:s');
						
						$courseAddAndGrade[$count]['CourseAdd']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);
						$courseAddAndGrade[$count]['CourseAdd']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

						//$courseAddAndGrade[$count]['CourseAdd']['created'] = date('Y-m-d H:i:s');
						//$courseAddAndGrade[$count]['CourseAdd']['modified'] = date('Y-m-d H:i:s');
					}
					$count++;
				}
			}

			if (!empty($courseAddAndGrade)) {
				foreach ($courseAddAndGrade as $data) {
					$this->ExamGrade->CourseAdd->saveAll($data, array('validate' => false));
				}
				if ($scaleNotFound['freq'] > 0) {
					$this->Flash->success('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.');
				} else {
					$this->Flash->success('You have entered the add course(s) data successfully.');
				}
				unset($courseAddAndGrade);
				$isTheDeletionSuccessful = ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id' => $studentId), false);
				$statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($studentId, $publishedCoursesId);
			} else {
				if ($scaleNotFound['freq'] > 0) {
					$this->Flash->info('It is required to have defined grade scale in order to perform data entry. ' . $scaleNotFound['freq'] . '  course(s) don\'t have scale, please ask either the registrar or department to define scale.');
					$this->request->data['listPublishedCourse'] = true;
				} else {
					if (empty($student_ids)) {
						$this->request->data['listPublishedCourse'] = true;
						$this->Flash->error('You are required to select at least one student.');
					}
				}
			}

			$this->request->data['ExamGrade']['studentnumber'] = $this->request->data['Student']['studentnumber'];
			$this->request->data['ExamGrade']['semester'] = $this->request->data['Student']['semester'];
			$this->request->data['ExamGrade']['acadamic_year'] = str_replace('-', '/', $this->request->data['Student']['acadamic_year']);
			$this->request->data['listPublishedCourse'] = true;
		}

		//Get published course for the selected student
		if (isset($this->request->data['listPublishedCourse']) && !empty($this->request->data['listPublishedCourse'])) {
			debug($this->request->data);

			unset($this->request->data['CourseRegistration']);

			$department_ids = array();
			$everyThingOk = false;
			$selectedStudent = array();

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
				
				$everyThingOk = true;
				
				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array(
						'Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])
					), 
					'contain' => array('StudentsSection')
				));

				if (!empty($selectedStudent)) {
					$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);
				} else {
					$this->Flash->error(' ' . $this->request->data['ExamGrade']['studentnumber'] . ' is not a valid student number.');
				}

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->department_ids)) {

				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array(
						'Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])
					),
					'contain' => array('StudentsSection')
				));

				//$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);

				if (!empty($selectedStudent)) {
					if (!in_array($selectedStudent['Student']['department_id'], $this->department_ids)) {
						$this->Flash->error('You don\'t have the privilage to enter data for ' . $this->request->data['ExamGrade']['studentnumber'] . '.');
					} else {
						$everyThingOk = true;
						$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);
					}
				} else {
					$this->Flash->error(' ' . $this->request->data['ExamGrade']['studentnumber'] . ' is not a valid student number.');
				}

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids)) {

				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array(
						'Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])
					), 
					'contain' => array('StudentsSection')
				));

				//$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);

				if (!empty($selectedStudent)) {
					if (!in_array($selectedStudent['Student']['college_id'], $this->college_ids)) {
						$this->Flash->error('You don\'t have the privilage to enter data for ' . $this->request->data['ExamGrade']['studentnumber'] . '.');
					} else {
						$everyThingOk = true;
						$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);
					}
				} else {
					$this->Flash->error(' ' . $this->request->data['ExamGrade']['studentnumber'] . ' is not a valid student number.');
				}

			} else {
				$this->Flash->error('You don\'t have the privilage to enter data for the selected student.');
			}

			$hashedPasswordGiven = Security::hash($this->request->data['ExamGrade']['password'], null, true);

			//$password = Security::hash($selectedStudent['User']['password'], null, true);

			if ($hashedPasswordGiven == $loggedUser['User']['password']) {
				$everyThingOk = true;
				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array(
						'Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])
					), 
					'contain' => array('StudentsSection')
				));
			} else {
				$everyThingOk = false;
				$this->Flash->error('Wrong password! Please try again!');
			}


			/* if (isset($this->request->data['ExamGrade']['password']) && $this->request->data['ExamGrade']['password'] == "neverusethisinterface") {
				$everyThingOk = true;
				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array('conditions' => array('Student.studentnumber' => trim($this->request->data['ExamGrade']['studentnumber'])), 'contain' => array('StudentsSection')));
			} */
			

			if ($everyThingOk && !empty($selectedStudent)) {
				//debug($selectedStudentDetail);
				/*
					* find the published course in that semester and academic year
					* does that published course has registration, grade submitted, then disable in the interface data entry
				*/

				$yearLevelAndSemesterOfStudent = $this->ExamGrade->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevel($selectedStudent['Student']['id'], $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);

				$student_academic_profile = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id'], $this->AcademicYear->current_academicyear());

				//////////////////////////////////////////////////////// ADDED BY NEWAY ////////////////////////////////////////////////////////

				$academicYR = $this->AcademicYear->current_academicyear();
				$isTheStudentDismissed = 0;
				$isTheStudentReadmitted = 0;

				$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($selectedStudent['Student']['id'], null, null);

				if (isset($student_section_exam_status['Section'])) {
					if (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
						debug($student_section_exam_status['Section']['academicyear']);
						$academicYR = $student_section_exam_status['Section']['academicyear'];
					}
				}

				if (isset($student_section_exam_status['StudentExamStatus']) && !empty($student_section_exam_status['StudentExamStatus']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
					$isTheStudentDismissed = 1;

					$possibleReadmissionYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange($student_section_exam_status['StudentExamStatus']['academic_year'], $this->AcademicYear->current_academicyear());

					$readmitted = ClassRegistry::init('Readmission')->find('first', array(
						'conditions' => array(
							'Readmission.student_id' => $selectedStudent['Student']['id'],
							'Readmission.registrar_approval' => 1,
							'Readmission.academic_commision_approval' => 1,
							'Readmission.academic_year' => $possibleReadmissionYears,
							/* 'OR' => array(
								'Readmission.academic_year' => $student_section_exam_status['StudentExamStatus']['academic_year'],
								'Readmission.semester' => $student_section_exam_status['StudentExamStatus']['semester'],
								'Readmission.registrar_approval_date' > $student_section_exam_status['StudentExamStatus']['modified'],
								'Readmission.modified' > $student_section_exam_status['StudentExamStatus']['modified'],
							) */
						),
						'order' => array('Readmission.academic_year' => 'DESC', 'Readmission.semester' => 'DESC', 'Readmission.modified' => 'DESC'),
						'recursive' => -1,
					));

					if (count($readmitted)) {
						$lastReadmittedAcademicYear = $readmitted['Readmission']['academic_year'];
						$lastReadmittedSemester = $readmitted['Readmission']['semester'];
						$lastReadmittedDate = $readmitted['Readmission']['registrar_approval_date'];

						debug($lastReadmittedAcademicYear);

						$isTheStudentReadmitted = 1;
						$possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange($lastReadmittedAcademicYear, $academicYR);
						$this->set(compact('possibleAcademicYears'));
					}

					debug($isTheStudentReadmitted);
				}

				//$this->set('isTheStudentDismissed', $isTheStudentDismissed);
				//$this->set('isTheStudentReadmitted', $isTheStudentReadmitted);
				$this->set('academicYR', $academicYR);

				$this->set(compact('isTheStudentDismissed', 'isTheStudentReadmitted'));
				

				//////////////////////////////////////////////////////// END ADDED BY NEWAY ////////////////////////////////////////////////////////
				

				$graduated = $this->ExamGrade->CourseRegistration->Student->SenateList->find('count', array(
					'conditions' => array(
						'SenateList.student_id' => $selectedStudent['Student']['id']
					),
					'recursive' => -1
				));

				$this->set(compact('student_academic_profile', 'graduated'));

				$selectedStudentDetails = $this->ExamGrade->getStudentCopy($selectedStudent['Student']['id'], $this->request->data['ExamGrade']['acadamic_year'], $this->request->data['ExamGrade']['semester']);
				//$admission_explode = explode('-', $selectedStudentDetails['Student']['admissionyear']);
				//$studentAdmissionYear = $this->AcademicYear->get_academicyear($admission_explode[1], $admission_explode[0]);
				$studentAdmissionYear = $selectedStudentDetails['Student']['academicyear'];
				//debug($selectedStudentDetails);

				if (empty($selectedStudentDetails['courses'])) {
					// there is no registration, so find the published course for that students
					$publishedCourses = $this->ExamGrade->getPublishedCourseIfExist(
						$selectedStudentDetails['Student']['department_id'],
						$this->request->data['ExamGrade']['acadamic_year'],
						$this->request->data['ExamGrade']['semester'],
						$selectedStudentDetails['Student']['program_id'],
						$selectedStudentDetails['Student']['program_type_id'],
						$selectedStudentDetails,
						$studentAdmissionYear,
						$this->AcademicYear->current_academicyear()
					);

					debug($publishedCourses);
					$studentbasic = $selectedStudentDetails;
					$this->set(compact('publishedCourses', 'studentbasic'));

				} else if (!empty($selectedStudentDetails['courses'])) {
					//debug($selectedStudentDetails);

					// getPublishedCourseIfExist($department_id, $academic_year, $semester,$program_id,$program_type_id,$studentDetail, $admissionAcademicYear,$currentAcademicYear=null) */
					$publishedCourses = $this->ExamGrade->getPublishedCourseIfExist(
						$selectedStudentDetails['Student']['department_id'],
						$this->request->data['ExamGrade']['acadamic_year'],
						$this->request->data['ExamGrade']['semester'],
						$selectedStudentDetails['Student']['program_id'],
						$selectedStudentDetails['Student']['program_type_id'],
						$selectedStudentDetails,
						$studentAdmissionYear,
						$this->AcademicYear->current_academicyear()
					);
					//debug($publishedCourses);

					if (!empty($publishedCourses['courses'])) {
						foreach ($publishedCourses['courses'] as $key => &$value) {
							if ($value['PublishedCourse']['readOnly']) {
								//unset($publishedCourses['courses'][$key]);
							}
						}
					}

					//$publishedCourses['courses'] = array_merge($publishedCourses['courses'], $selectedStudentDetails['courses']);
					//debug($publishedCourses);

					if (!empty($publishedCourses['courses'])) {
						$publishedCourses['courses'] = $this->__mergePublishedCourse($publishedCourses, $selectedStudentDetails);
					}
					//$publishedCourses['courses'] = $publishedCourses['courses'];
					//debug($publishedCourses);
					$studentbasic = $selectedStudentDetails;

					$this->set(compact('publishedCourses', 'studentbasic'));
				}
			}
		}

		if (!empty($this->department_ids)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->ExamGrade->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
		}

		//$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 10, date('Y') - 1);

		$current_acy = $this->AcademicYear->current_academicyear();

		//debug((explode('/', $current_acy)[0]) - ACY_BACK_FOR_BACK_DATED_DATA_ENTRY);

		if (is_numeric(ACY_BACK_FOR_BACK_DATED_DATA_ENTRY) && ACY_BACK_FOR_BACK_DATED_DATA_ENTRY) {
			$acyear_list = $this->AcademicYear->academicYearInArray(((explode('/', $current_acy)[0]) - ACY_BACK_FOR_BACK_DATED_DATA_ENTRY), (explode('/', $current_acy)[0]));
		} else if (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
			$acyear_list = $this->AcademicYear->academicYearInArray(((explode('/', $current_acy)[0]) - ACY_BACK_FOR_ALL), (explode('/', $current_acy)[0]));
		} else {
			$acyear_list[$current_acy] = $current_acy;
		}

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
			$acyear_list = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $current_acy)[0]));
		}

		//debug($acyear_list);
		
		$this->set(compact(
			'programs', 
			'program_types', 
			'departments', 
			'academic_year_selected', 
			'semester_selected', 
			'program_id', 
			'program_type_id', 
			'section_id', 
			'sections', 
			'students_in_section', 
			'acyear_list', 
			'student_copies', 
			'colleges', 
			'department_id', 
			'college_id'
		));

		$this->render('data_entry_interface_edit');
	}

	private function __mergePublishedCourse($publish1, $publish2)
	{
		$publishedCourses['courses'] = array();

		$academicYear = $publish1['courses'][0]['PublishedCourse']['academic_year'];
		$semester = $publish1['courses'][0]['PublishedCourse']['semester'];
		$publish3['courses'] = array();
		$publish4['courses'] = array();

		$studentId = null;

		if (!empty($publish2['courses'])) {
			foreach ($publish2['courses'] as $pk2 => $pv2) {
				if (isset($pv2['PublishedCourse']['academic_year']) && $pv2['PublishedCourse']['academic_year'] == $academicYear && isset($pv2['PublishedCourse']['semester']) && $pv2['PublishedCourse']['semester'] == $semester) {
					$publish3['courses'][] = $pv2;
				} else {
					//check any registration
					if (isset($pv2['CourseRegistration']['student_id']) && !empty($pv2['CourseRegistration']['student_id'])) {
						$studentId = $pv2['CourseRegistration']['student_id'];
					}
				}
			}
		}

		if (empty($publish3['courses'])) {
			$plist = $this->ExamGrade->CourseRegistration->find('all', array(
				'conditions' => array(
					'CourseRegistration.academic_year' => $academicYear,
					'CourseRegistration.semester' => $semester,
					'CourseRegistration.student_id' => $studentId,

				),
				'contain' => array(
					'PublishedCourse' => array(
						'Course' => array('GradeType' => array('Grade')),
						'CourseInstructorAssignment' => array(
							'Staff' => array(
								'fields' => array('id','full_name', 'first_name', 'middle_name','last_name'),
								'Title' => array('id', 'title'),
								'College' => array('id', 'name'),
								'Department' => array('id', 'name'),
								'Position' => array('id', 'position'),
							),
							'order' => array('isprimary' => 'DESC'),
							'limit' => 1
						)
					),
					'ExamGrade'
				)
			));

			$count = 0;

			if (!empty($plist)) {
				foreach ($plist as $pkl => &$plv) {
					$plv['PublishedCourse']['grade'] = $this->ExamGrade->getApprovedGrade($plv['CourseRegistration']['id'], 1);
					$publish3['courses'][$count]['PublishedCourse'] = $plv['PublishedCourse'];
					$publish3['courses'][$count]['Course'] = $plv['PublishedCourse']['Course'];
					$publish3['courses'][$count]['CourseRegistration'] = $plv['CourseRegistration'];
					$count++;
				}
			}

			$pAddlist = $this->ExamGrade->CourseAdd->find('all', array(
				'conditions' => array(
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
					'CourseAdd.academic_year' => $academicYear,
					'CourseAdd.semester' => $semester,
					'CourseAdd.student_id' => $studentId,
				),
				'contain' => array(
					'PublishedCourse' => array(
						'Course' => array('GradeType' => array('Grade')),
						'CourseInstructorAssignment' => array(
							'Staff' => array(
								'fields' => array('id','full_name', 'first_name', 'middle_name','last_name'),
								'Title' => array('id', 'title'),
								'College' => array('id', 'name'),
								'Department' => array('id', 'name'),
								'Position' => array('id', 'position'),
							),
							'order' => array('isprimary' => 'DESC'),
							'limit' => 1
						)
					),
					'ExamGrade'
				)
			));

			if (!empty($pAddlist)) {
				foreach ($pAddlist as $pkl => &$plv) {
					$plv['PublishedCourse']['grade'] = $this->ExamGrade->getApprovedGrade($plv['CourseAdd']['id'], 0);
					$publish3['courses'][$count]['PublishedCourse'] = $plv['PublishedCourse'];
					$publish3['courses'][$count]['Course'] = $plv['PublishedCourse']['Course'];
					$publish3['courses'][$count]['CourseAdd'] = $plv['CourseAdd'];
					$count++;
				}
			}
		}

		//check if existed in already registered once
		$publish5['courses'] = array();

		if (!empty($publish1['courses'])) {
			foreach ($publish1['courses'] as $pk1 => $pv1) {
				$found = false;
				foreach ($publish3['courses'] as $pk3 => $pv3) {
					if ($pv1['PublishedCourse']['id'] == $pv3['PublishedCourse']['id']) {
						$found = true;
						//$publish5['courses'][] = $pv3;
						//break;
					}
				}
				if ($found == false) {
					$publish5['courses'][] = $pv1;
				}
			}
		}

		//$publishedCourses['courses'] = $publish3['courses'];

		$publishedCourses['courses'] = array_merge($publish5['courses'], $publish3['courses']);

		//debug($publish3);

		/* foreach ($publish1['courses'] as $pk1 => $pv1) {
			//check if existed in
			if (isset($publish3['courses']) && !empty($publish3['courses'])) {
				foreach ($publish3['courses'] as $pk3 => $pv3) {
					if (strcasecmp($pv1['PublishedCourse']['id'], $pv3['PublishedCourse']['id']) != 0 && strcasecmp($pv3['PublishedCourse']['academic_year'], $pv1['PublishedCourse']['academic_year']) == 0 && strcasecmp($pv3['PublishedCourse']['semester'], $pv1['PublishedCourse']['semester']) == 0) {
						$publish4['courses'][] = $pv1;
						break;
					}
				}
			} else {
				$publish4['courses'][] = $pv1;

			}
		} */
		
		//debug($publish4);
		//$publishedCourses['courses'] = $publish3['courses'];
		//$publishedCourses['courses'] = array_merge($publish1['courses'], $publish3['courses']);
		//}

		/* foreach ($publish1['courses'] as $pk1 => $pv1) {
			$publish3['courses'][] = $pv1;
		} */
	   
		//$publishedCourses['courses'] = $publish3['courses'];
		//debug($publish3);

		//debug($publishedCourses);
		//debug($publish1['courses']);
		//debug($publish2['courses']);


		//$publishedCourses['courses'] = array_merge($publish1['courses'], $publish3['courses']);
		//$publishedCourses['courses'] = $publish3['courses'];

		/* if (isset($publish3['courses']) && !empty($publish3['courses'])) {
			$publishedCourses['courses'] =
				$publish3['courses'];
		} else if (isset($publish1['courses']) && !empty($publish1['courses'])) {
			$publishedCourses['courses'] = $publish1['courses'];
		} else {
			$publishedCourses['courses'] = array_merge($publish1['courses'], $publish2['courses']);
		} */
		

		$freq = array();

		if (!empty($publishedCourses['courses'])) {
			foreach ($publishedCourses['courses'] as $k => $v) {

				// added by Neway for removing undefined course_id error/debug notice
				if (isset($v['PublishedCourse']['course_id']) && !isset($freq[$v['PublishedCourse']['course_id']])) {
					$freq[$v['PublishedCourse']['course_id']] = 0;
				}
				// END added by Neway for removing undefined course_id error/debug notice

				if (isset($v['PublishedCourse']['course_id']) && !empty($v['PublishedCourse']['course_id'])) {
					$freq[$v['PublishedCourse']['course_id']]++;
				}
			}
		}

		debug($freq);

		if (!empty($publishedCourses['courses'])) {
			foreach ($publishedCourses['courses'] as $k => &$vv) {
				
				$failedAnyPrerequistie['freq'] = 0;

				if ($freq[$vv['PublishedCourse']['course_id']] > 1 && !isset($vv['CourseRegistration'])) {
					unset($publishedCourses['courses'][$k]);
				}

				$is_grade_submitted = $this->ExamGrade->isGradeSubmittedForPublishedCourseGivenStudentId($publish2['Student']['id'], $vv['PublishedCourse']['id']);

				if (!empty($vv['Course']['Prerequisite'])) {
					debug($vv['Course']);
					foreach ($vv['Course']['Prerequisite'] as $preValue) {
						$failed = ClassRegistry::init('CourseDrop')->prequisite_taken($publish2['Student']['id'], $preValue['prerequisite_course_id']);
						debug($failed);
						if ($failed == 0  && $preValue['co_requisite'] != true) {
							$failedAnyPrerequistie['freq']++;
						}
					}
				}

				if ($failedAnyPrerequistie['freq'] > 0) {
					$value['PublishedCourse']['prerequisiteFailed'] = true;
				} else {
					$value['PublishedCourse']['prerequisiteFailed'] = 0;
				}

				if ($is_grade_submitted) {
					$vv['PublishedCourse']['readOnly'] = true;
				} else {
					$vv['PublishedCourse']['readOnly'] = false;
				}

				if (isset($vv['PublishedCourse']['grade_scale_id']) && !empty($vv['PublishedCourse']['grade_scale_id']) && $vv['PublishedCourse']['grade_scale_id'] != 0) {
					$vv['Course']['grade_scale_id'] = $vv['PublishedCourse']['grade_scale_id'];
				} else {
					$vv['Course']['grade_scale_id'] = ClassRegistry::init('GradeScale')->getGradeScaleId($vv['Course']['grade_type_id'], $publish2);
				}
			}
		}

		//debug($publishedCourses['courses']);
		return $publishedCourses['courses'];
	}

	private function __academic_status_grade_interface($selected = null)
	{
		/*
		1. Retrieve list of sections based on the given search criteria
		2. Display list of sections
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade report in PDF for the selected students
		*/
		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find('list');
		$program_types = $this->ExamGrade->CourseRegistration->PublishedCourse->Section->ProgramType->find('list');

		$departments = array();

		//Get Grade Report button is clicked
		if (isset($this->request->data['saveGrade']) && !empty($this->request->data['saveGrade'])) {
			$publishedCoursesId = array();
			$student_ids = array();
			$studentId = null;
			$courseRegistrationAndGrade = array();
			$count = 0;
			$scaleNotFound['freq'] = 0;
			foreach ($this->request->data['CourseRegistration'] as $key => $student) {
				if ($student['grade_scale_id'] == 0) {
					$scaleNotFound['freq']++;
					debug($scaleNotFound);
				}
				if ($student['gp'] == 1 && $student['grade_scale_id'] != 0) {
					$student_ids[] = $student['student_id'];
					$studentId = $student['student_id'];
					$courseRegistrationAndGrade[$count]['CourseRegistration'] = $student;
					$publishedCoursesId = $student['published_course_id'];
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['grade'] = $student['grade'];
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval'] = 1;
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['grade_scale_id'] = $student['grade_scale_id'];
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval'] = 1;
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_reason'] = 'Via backend data entry interface';
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['registrar_approval_date'] = $this->AcademicYear->get_academicYearBegainingDate($student['academic_year']);
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['department_approval_date'] = $this->AcademicYear->get_academicYearBegainingDate($student['academic_year']);
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['created'] = $this->AcademicYear->get_academicYearBegainingDate(
						$student['academic_year']
					);
					$courseRegistrationAndGrade[$count]['ExamGrade'][$count]['modified'] = $this->AcademicYear->get_academicYearBegainingDate(
						$student['academic_year']
					);
					$courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->get_academicYearBegainingDate($student['academic_year']);
					$courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->get_academicYearBegainingDate($student['academic_year']);
				}
				$count++;
			}

			if (!empty($courseRegistrationAndGrade)) {

				foreach ($courseRegistrationAndGrade as $data) {
					$this->ExamGrade->CourseRegistration->saveAll($data, array('validate' => false));
				}
				if ($scaleNotFound['freq'] > 0) {
					$this->Session->setFlash('<span></span>' . __('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'), 'default', array('class' => 'success-box success-message'));
				} else {
					$this->Session->setFlash('<span></span>' . __('You have entered the data successfully.'), 'default', array('class' => 'success-box success-message'));
				}
				$isTheDeletionSuccessful = ClassRegistry::init('StudentExamStatus')->deleteAll(
					array('StudentExamStatus.student_id' => $studentId),
					false
				);
				$statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($studentId, $publishedCoursesId);
			} else {
				if ($scaleNotFound['freq'] > 0) {
					$this->Session->setFlash('<span></span>' . __('' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'), 'default', array('class' => 'success-box success-message'));
				} else {
					$this->Session->setFlash('<span></span>' . __('You are required to select at least one course.'), 'default', array('class' => 'error-box error-message'));
				}
			}
			if (empty($student_ids)) {
				$this->request->data['listPublishedCourse'] = true;
				$this->Session->setFlash('<span></span>' . __('You are required to select at least one course.'), 'default', array('class' => 'error-box error-message'));
			} else {
			}
		}

		//Get published course for the selected student
		if (isset($this->request->data['listPublishedCourse'])) {

			$department_ids = array();
			$everyThingOk = false;
			$selectedStudent = array();
			if (!empty($this->department_ids)) {
				$selectedStudent = $this->ExamGrade->CourseRegistration->Student->find('first', array(
					'conditions' => array('Student.studentnumber' => trim($this->request->data['Search']['studentnumber'])),
					'contain' => array('StudentsSection')
				));
				//debug($selectedStudent);
				$selectedStudentDetail = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id']);
				// debug($selectedStudentDetail);
				if (!empty($selectedStudent)) {
					if (!in_array($selectedStudent['Student']['department_id'], $this->department_ids)) {
						$this->Session->setFlash('<span></span>' .
							__('You don\'t have the privilage to enter data for ' . $this->request->data['Search']['studentnumber'] . '.'), 'default', array('class' => 'info-box info-message'));
					} else {
						$everyThingOk = true;
					}
				} else {
					$this->Session->setFlash('<span></span>' . __(' ' . $this->request->data['Search']['studentnumber'] . ' is not a valid student number.'), 'default', array('class' => 'info-box info-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>' . __('You don\'t have the privilage to enter data for the selected student.'), 'default', array('class' => 'info-box info-message'));
			}

			if ($everyThingOk && !empty($selectedStudent)) {
				/*
			 * find the published course in that semester and academic year, does that published course has registration, grade submitted, then disable in the interface data entry
			 */
				$yearLevelAndSemesterOfStudent = $this->ExamGrade->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevel($selectedStudent['Student']['id'], $this->request->data['Search']['acadamic_year'], $this->request->data['Search']['semester']);

				$student_academic_profile = $this->ExamGrade->CourseRegistration->Student->getStudentRegisteredAddDropCurriculumResult($selectedStudent['Student']['id'], $this->AcademicYear->current_academicyear());
				$this->set(compact('student_academic_profile'));
				$selectedStudentDetails = $this->ExamGrade->getStudentCopy(
					$selectedStudent['Student']['id'],
					$this->request->data['Search']['acadamic_year'],
					$this->request->data['Search']['semester']
				);

				$admission_explode = explode('-', $selectedStudentDetails['Student']['admissionyear']);

				$studentAdmissionYear = $this->AcademicYear->get_academicyear($admission_explode[1], $admission_explode[0]);
				// debug($selectedStudentDetails);
				if (empty($selectedStudentDetails['courses'])) {
					// there is no registration, so find the published course for that students

					$publishedCourses = $this->ExamGrade->getPublishedCourseIfExist(
						$selectedStudentDetails['Student']['department_id'],
						$this->request->data['Search']['acadamic_year'],
						$this->request->data['Search']['semester'],
						$selectedStudentDetails['Student']['program_id'],
						$selectedStudentDetails['Student']['program_type_id'],
						$selectedStudentDetails,
						$studentAdmissionYear,
						$this->AcademicYear->current_academicyear()
					);
					if (empty($publishedCourses['courses'])) {
						$manuallStatusEntry = true;
					}
					$studentbasic = $selectedStudentDetails;
					$this->set(compact('publishedCourses', 'manuallStatusEntry', 'studentbasic'));
				} else if (!empty($selectedStudentDetails['courses'])) {

					$publishedCourses = $this->ExamGrade->getPublishedCourseIfExist(
						$selectedStudentDetails['Student']['department_id'],
						$this->request->data['Search']['acadamic_year'],
						$this->request->data['Search']['semester'],
						$selectedStudentDetails['Student']['program_id'],
						$selectedStudentDetails['Student']['program_type_id'],
						$selectedStudentDetails,
						$studentAdmissionYear,
						$this->AcademicYear->current_academicyear()
					);
					foreach ($publishedCourses['courses'] as $key => &$value) {
						if ($value['PublishedCourse']['readOnly']) {
							unset($publishedCourses['courses'][$key]);
						}
					}
					$publishedCourses['courses'] = $this->__mergePublishedCourse($publishedCourses, $selectedStudentDetails);
					$studentbasic = $selectedStudentDetails;
					$this->set(compact('publishedCourses', 'studentbasic'));
				}
			}
		}
		if (!empty($this->department_ids)) {
			$departments = $this->ExamGrade->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
		} else if (!empty($this->college_ids)) {
			$colleges = $this->ExamGrade->CourseRegistration->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids)));
		}

		$gradeTypes = ClassRegistry::init('GradeType')->find('list', array('fields' => array('id', 'type')));
		if (empty($this->request->data)) {
			$temp = array_keys($gradeTypes);
			$gradeTypeId = $temp[0];
		} else {
			if (!empty($this->request->data['GradeScale']['grade_type_id'])) {
				$gradeTypeId = $this->request->data['GradeScale']['grade_type_id'];
			} else {
				$temp = array_keys($gradeTypes);
				$gradeTypeId = $temp[0];
			}
		}

		$grades = ClassRegistry::init('Grade')->find('list', array('conditions' => array('Grade.grade_type_id' => $gradeTypeId), 'fields' => array('id', 'grade')));
		$academicStatuses = ClassRegistry::init('AcademicStatus')->find('list', array('fields' => array('id', 'name')));

		$this->set(compact('programs', 'academicStatuses', 'program_types', 'grades', 'departments', 'gradeTypes', 'academic_year_selected', 'semester_selected', 'program_id', 'program_type_id', 'section_id', 'sections', 'students_in_section', 'student_copies', 'colleges', 'department_id', 'college_id'));
		$this->render('academic_status_grade_interface');
	}

	public function import_archived_data()
	{
		if (!empty($this->request->data) && is_uploaded_file($this->request->data['ExamGrade']['File']['tmp_name'])) {
			//check the file type before doing the fucken manipulations.
			if (strcasecmp(
				$this->request->data['ExamGrade']['File']['type'],
				'application/vnd.ms-excel'
			)) {
				$this->Session->setFlash('<span></span>' . __('Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type while you saved the file and import again. Try also to use other 97-2003 file types if you are using office 2010 or recent versions. Current file format is: ' . $this->request->data['ExamGrade']['File']['type']), 'default', array('class' => 'error-box error-message'));
				return;
			}
			$data = new Spreadsheet_Excel_Reader();
			// Set output Encoding.
			$data->setOutputEncoding('CP1251');
			$data->read($this->request->data['AcceptedStudent']['File']['tmp_name']);
			$headings = array();
			$xls_data = array();
			//required field
			$required_fields = array(
				'studentnumber', 'course_code', 'course_title', 'credit', 'grade', 'academic_year', 'semester', 'academic_status',
				'cgpa', 'mgpa'
			);
			if (empty($data->sheets[0]['cells'])) {
				$this->Session->setFlash('<span></span>' . __('Importing Error. The excel file
                     you uploaded is empty.', true), 'default', array('class' => 'error-box error-message'));
				return;
			}
			if (empty($data->sheets[0]['cells'][1])) {
				$this->Session->setFlash('<span></span>' .
					__('Importing Error. Please insert your filed name (studentnumber,course_code,course_title,credit,grade,
academic_year,semester,academic_status,cgpa,mgpa)  at first row of your excel file.', true), 'default', array('class' => 'error-box error-message'));
				return;
			}

			for ($k = 0; $k < count($required_fields); $k++) {
				if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === FALSE)
					$non_existing_field[] = $required_fields[$k];
			}
			if (count($non_existing_field) > 0) {
				$field_list = "";
				foreach ($non_existing_field as $k => $v)
					$field_list .= ($v . ", ");

				$field_list = substr($field_list, 0, (strlen($field_list) - 2));
				$this->Session->setFlash('<span></span>' . __('Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.', true), 'default', array('class' => 'error-box error-message'));
				return;
			} else {
			}
		}
	}

	public function getAddCoursesDataEntry($student_id, $academic_year, $semester)
	{
		$this->layout = 'ajax';
		$student = $this->ExamGrade->CourseAdd->Student->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));
		$departments = $this->ExamGrade->CourseAdd->PublishedCourse->Department->find(
			'list',
			array('conditions' => array('Department.id in (select department_id from published_courses where semester="' . $semester . '" and academic_year="' . str_replace('-', '/', $academic_year) . '" and program_id=' . $student['Student']['program_id'] . ' and program_type_id=' . $student['Student']['program_type_id'] . ')'))
		);
		$colleges = $this->ExamGrade->CourseAdd->PublishedCourse->College->find('list');
		$addParamaters['student_id'] = $student_id;
		$addParamaters['academic_year'] = $academic_year;
		$addParamaters['semester'] = $semester;
		$addParamaters['studentnumber'] = $student['Student']['studentnumber'];
		$this->set(compact('colleges', 'departments', 'addParamaters'));
	}

	//get_published_add_courses
	public function getPublishedAddCourses($section_id = null, $addParamaters = null)
	{
		$this->layout = 'ajax';
		$academicYearSemesterArray = explode(",", $addParamaters);
		//debug($academicYearSemesterArray);
		if (!empty($academicYearSemesterArray)) {
			$academicYear = str_replace("-", "/", $academicYearSemesterArray[1]);
			$current_academic_year = $academicYear;
			$section_semester = $academicYearSemesterArray[2];
		} else {
			$current_academic_year = $this->AcademicYear->current_academicyear();

			if (!empty($student_id)) {
				$latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear($academicYearSemesterArray[0], $current_academic_year);
			} else {
				$latestAcSemester = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear($academicYearSemesterArray[0], $current_academic_year);
			}
			$section_semester = ClassRegistry::init('CourseRegistration')->latest_semester_of_section($section_id, $current_academic_year);

			if ($section_semester == 2) {
				$section_semester = $latestAcSemester['semester'];
			}
		}

		if (!empty($academicYearSemesterArray[0])) {
			$student_section_id = $this->ExamGrade->CourseAdd->Student->StudentsSection->field('section_id', array('student_id' => $academicYearSemesterArray[0], 'archive' => 0));
		} else {
			$student_section_id = $this->ExamGrade->CourseAdd->Student->StudentsSection->field('section_id', array('student_id' => $academicYearSemesterArray[0], 'archive' => 0));
		}
		//debug($academicYear);
		if ($student_section_id == $section_id) {
			// exclude mass add
			$otherpublished = $this->ExamGrade->CourseAdd->PublishedCourse->find(
				'all',
				array(
					'conditions' => array(

						'PublishedCourse.academic_year' => $current_academic_year,

						'PublishedCourse.semester' => $section_semester,
						'PublishedCourse.add=0',
						'PublishedCourse.section_id' => $section_id
					),
					'contain' => array('Course' => array('fields' => array('course_code', 'credit', 'id', 'course_title'), 'GradeType' => array('Grade')))
				)
			);
		} else {
			$sectionAcademicYear = $this->ExamGrade->CourseAdd->PublishedCourse->Section->find('first', array('conditions' => array('Section.id' => $section_id), 'recursive' => -1));
			$otherpublished = $this->ExamGrade->CourseAdd->PublishedCourse->find(
				'all',
				array(
					'conditions' => array(
						'PublishedCourse.academic_year' => $sectionAcademicYear['Section']['academicyear'],
						'PublishedCourse.semester' => $section_semester,
						'PublishedCourse.drop=0',
						'PublishedCourse.section_id' => $section_id
					),
					'contain' => array('Course' => array('fields' => array('course_code', 'credit', 'id', 'course_title'), 'GradeType' => array('Grade')))
				)
			);
		}
		if (!empty($academicYearSemesterArray[0])) {
			$otherAdds = $this->__exclude_already_added($otherpublished, $academicYearSemesterArray[0]);
			// debug($otherAdds);
		}

		$addParamaterss['student_id'] = $academicYearSemesterArray[0];
		$addParamaterss['academic_year'] = $academicYearSemesterArray[1];
		$addParamaterss['semester'] = $academicYearSemesterArray[2];


		$this->set(compact('otherAdds', 'addParamaterss'));
	}

	function __exclude_already_added($otherAdds, $student_id = null)
	{
		$pub_own_as_add_courses = array();
		$count = 0;
		foreach ($otherAdds as $ownIndex => $ownValue) {
			$already_added = $this->ExamGrade->CourseAdd->find('first', array('conditions' => array('CourseAdd.student_id' => $student_id, 'CourseAdd.published_course_id' => $ownValue['PublishedCourse']['id']), 'contain' => array('ExamGrade')));
			// debug($already_added);
			if (!empty($ownValue['Course']['id'])) {
				$already_taken_course = ClassRegistry::init('CourseDrop')->course_taken($student_id, $ownValue['Course']['id']);
			}
			debug($already_taken_course);
			//$pub_own_as_add_courses[$count]['prerequiste_failed']=1;


			/**
			 *1 -exclude from add
			 *2 -exclude from add
			 *3 -allow add
			 *4 - prerequist failed.
			 */
			//  debug($pub_own_as_add_courses);
			if (0 && ($already_taken_course == 1 || $already_taken_course == 4 || $already_taken_course == 2)) {

				$pub_own_as_add_courses[$count] = $ownValue;

				$pub_own_as_add_courses[$count]['already_added'] = 1;
				if ($already_taken_course == 4) {
					$pub_own_as_add_courses[$count]['prerequiste_failed'] = 1;
				}
				$pub_own_as_add_courses[$count]['PublishedCourse']['grade_scale_id'] = ClassRegistry::init('ExamGrade')->getPublishedCourseGradeGradeScale($ownValue['PublishedCourse']['id']);
			} else {
				$pub_own_as_add_courses[$count] = $ownValue;
				debug($pub_own_as_add_courses[$count]);
				if (!empty(ClassRegistry::init('ExamGrade')->getPublishedCourseGradeGradeScale($ownValue['PublishedCourse']['id']))) {
					$pub_own_as_add_courses[$count]['PublishedCourse']['grade_scale_id'] = ClassRegistry::init('ExamGrade')->getPublishedCourseGradeGradeScale($ownValue['PublishedCourse']['id']);
				} else {

					$pub_own_as_add_courses[$count]['PublishedCourse']['grade_scale_id'] = ClassRegistry::init('GradeScale')->getGradeScaleIdGivenPublishedCourse($ownValue['PublishedCourse']['id']);
				}
				$pub_own_as_add_courses[$count]['already_added'] = 0;
			}
			$count++;
		}
		return $pub_own_as_add_courses;
	}

	public function view_pdf($id = null)
	{
		if (!$id) {
			$this->Session->setFlash('Sorry, not able to generate Pdf.');
			$this->redirect(array('action' => 'index'), null, true);
		}
		$view_only = true;
		$exam_types = ClassRegistry::init('ExamType')->find('all', array(
			'fields' => array('id', 'exam_name', 'percent', 'order', 'mandatory'),
			'conditions' => array('ExamType.published_course_id' => $id),
			'contain' => array(),
			'order' => array('order ASC'),
			'recursive' => -1
		));

		$published_course_detail = $publish_course_detail_info = ClassRegistry::init('PublishedCourse')->find(
			'first',
			array(
				'conditions' =>
				array(
					'PublishedCourse.id' => $id
				),

				'contain' => array('Course' => array('CourseCategory'), 'Section' => array('YearLevel'), 'Program', 'ProgramType', 'Department' => array('College'), 'CourseInstructorAssignment' => array('conditions' => array('CourseInstructorAssignment.isprimary' => 1), 'Staff'))
			)
		);
		$student_course_register_and_adds = ClassRegistry::init('PublishedCourse')->getStudentsTakingPublishedCourse($id);
		$students = $student_course_register_and_adds['register'];
		$student_adds = $student_course_register_and_adds['add'];
		$student_makeup = $student_course_register_and_adds['makeup'];

		$total_student_count = count($students) + count($student_adds) + count($student_makeup);

		$university = ClassRegistry::init('University')->getSectionUniversity($publish_course_detail_info['PublishedCourse']['section_id']);

		$filename = "Grade_Sheet_" . (str_replace(' ', '_', (trim(str_replace('  ', ' ', $publish_course_detail_info['Section']['name']))))) . '_' . (str_replace('/', '-', $publish_course_detail_info['PublishedCourse']['academic_year'])) . '_' . $publish_course_detail_info['PublishedCourse']['semester'];

		$this->set(compact('selected_acadamic_year', 'selected_semester', 'grade_scale', 'published_course_detail', 'exam_results', 'published_course_combo_id', 'publishedCourses', 'students', 'exam_types', 'student_adds', 'student_makeup', 'section_detail', 'course_detail', 'display_grade', 'filename', 'university', 'publish_course_detail_info', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'total_student_count'));
		$this->response->type('application/pdf');
		$this->layout = '/pdf/default';
		$this->render('/Elements/marksheet_grade_pdf');
	}

	public function view_xls($id = null)
	{
		$this->autoLayout = false;
		if (!$id) {
			$this->Flash->error('Sorry, unable to generate Excel File.');
			$this->redirect(array('action' => 'index'), null, true);
		}

		$view_only = true;

		$exam_types = ClassRegistry::init('ExamType')->find('all', array(
			'fields' => array('id', 'exam_name', 'percent', 'order', 'mandatory'),
			'conditions' => array('ExamType.published_course_id' => $id),
			'contain' => array(),
			'order' => array('order ASC'),
			'recursive' => -1
		));

		$published_course_detail = $publish_course_detail_info = ClassRegistry::init('PublishedCourse')->find('first', array(
			'conditions' => array(
				'PublishedCourse.id' => $id
			),
			'contain' => array(
				'Course' => array('CourseCategory'),
				'Section' => array('YearLevel'),
				'Program',
				'ProgramType',
				'Department' => array('College'),
				'College',
				'CourseInstructorAssignment' => array(
					'conditions' => array('CourseInstructorAssignment.isprimary' => 1),
					'Staff' => array(
						//'fields' => array('id', 'full_name'),
						//'Position' => array('id', 'position'),
						'Title' => array('id', 'title'),
					)
				)
			)
		));

		$student_course_register_and_adds = ClassRegistry::init('PublishedCourse')->getStudentsTakingPublishedCourse($id);
		$students = $student_course_register_and_adds['register'];
		$student_adds = $student_course_register_and_adds['add'];
		$student_makeup = $student_course_register_and_adds['makeup'];

		$total_student_count = count($students) + count($student_adds) + count($student_makeup);

		$university = ClassRegistry::init('University')->getSectionUniversity($publish_course_detail_info['PublishedCourse']['section_id']);

		$filename = "Mark_Sheet_" . $publish_course_detail_info['Course']['course_code'] . '_' . (str_replace(' ', '_', (trim(str_replace('  ', ' ', $publish_course_detail_info['Section']['name']))))) . '_' . (str_replace('/', '-', $publish_course_detail_info['PublishedCourse']['academic_year'])) . '_' . ($publish_course_detail_info['PublishedCourse']['semester'] == 'I' ? '1st' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'II' ? '2nd' : ($publish_course_detail_info['PublishedCourse']['semester'] == 'III' ? '3rd' : $publish_course_detail_info['PublishedCourse']['semester']))) . '_semester_' . date('Y-m-d');

		$this->set(compact('selected_acadamic_year', 'selected_semester', 'grade_scale', 'published_course_detail', 'exam_results', 'published_course_combo_id', 'publishedCourses', 'students', 'exam_types', 'student_adds', 'student_makeup', 'section_detail', 'course_detail', 'display_grade', 'filename', 'university', 'publish_course_detail_info', 'grade_submission_status', 'view_only', 'days_available_for_grade_change', 'total_student_count'));
		$this->render('/Elements/marksheet_grade_xls');

	}

	public function request_fx_exam_sit()
	{

		if (
			isset($this->student_id)
			&& !empty($this->student_id)
		) {
			$this->__get_fx_grade($this->student_id);
		} else {
			$this->__get_fx_grade(0);
		}
		$this->render('request_fx_exam_sit');
	}
	private function __get_fx_grade($student_id = 0)
	{
		$fx_grade_change = $this->ExamGrade->getListOfFXGradeChangeForStudentChoice($student_id);
		debug($fx_grade_change);
		$applied_request = ClassRegistry::init('FxResitRequest')->doesFxAppliedandQuotaUsed($this->student_id, $this->AcademicYear->current_academicyear());
		debug($applied_request);

		if ($applied_request == 2) {
			$this->Session->setFlash('<span></span>' . __('You have already applied one Fx exam retake and it is only allowed one course per semester to retake FX exam based on the new legislation.', true), 'default', array('class' => 'error-box error-message'));

			// 	 return $this->redirect(array('action'=>'view_fx_resit'));
		} else if ($applied_request == 3) {
			$this->Session->setFlash('<span></span>' . __('You have finished 3 Fx examination retake and based on the new legistration you are allowed to 4 Fx throughtout your stay at the university.', true), 'default', array('class' => 'error-box error-message'));
			return $this->redirect(array('action' => 'view_fx_resit'));
		}

		if (isset($this->request->data) && !empty($this->request->data)) {

			$selectedCourseCount = 0;
			$selectedCourseDetail = null;
			foreach ($this->request->data['FxResitRequest'] as $fk => $fv) {
				if ($fv['selected_id'] == 1) {
					$selectedCourseCount++;
					$selectedCourseDetail['FxResitRequest'] = $fv;
				}
			}
			if ($selectedCourseCount > 1) {
				$this->Session->setFlash('<span></span>' . __('You are allowed only to apply for one fx exam sit, please select only one course. ', true), 'default', array('class' => 'error-box error-message'));
			} else {

				//check if student has applied for the same academic year and semester
				if (isset($selectedCourseDetail['FxResitRequest']['course_registration_id']) && !empty($selectedCourseDetail['FxResitRequest']['course_registration_id'])) {
					$doesStudentAppliedFxSit =
						ClassRegistry::init('FxResitRequest')->doesStudentAppliedFxSit($selectedCourseDetail['FxResitRequest']['course_registration_id'], 1);
				} else if (isset($selectedCourseDetail['FxResitRequest']['course_add_id']) && !empty($selectedCourseDetail['FxResitRequest']['course_add_id'])) {
					$doesStudentAppliedFxSit =
						ClassRegistry::init('FxResitRequest')->doesStudentAppliedFxSit($selectedCourseDetail['FxResitRequest']['course_add_id'], 0);
				}
				if ($doesStudentAppliedFxSit == true) {
					$this->Session->setFlash('<span></span>' . __('You have already applied for Fx exam for the course, you can not apply now. ', true), 'default', array('class' => 'error-box error-message'));
				} else if ($doesStudentAppliedFxSit == false && isset($selectedCourseDetail) && !empty($selectedCourseDetail)) {
					ClassRegistry::init('FxResitRequest')->create();
					if (ClassRegistry::init('FxResitRequest')->save($selectedCourseDetail)) {
						$this->Session->setFlash('<span></span>' . __('Thank you, you have applied to Fx exam resit and your application will be dispatched to the instructor. ', true), 'default', array('class' => 'success-box success-message'));
					}
				}
			}
		}
		$this->set(compact('applied_request', 'fx_grade_change'));
	}
	public function view_fx_resit()
	{
		$options['contain'] = array(
			'Course',
			'FxResitRequest' => array('Student')
		);
		if (isset($this->student_id) && !empty($this->student_id)) {
			$options['conditions'][] = 'PublishedCourse.id in (select published_course_id from fx_resit_request where student_id=' . $this->student_id . ') ';
		} else {
			$options['conditions'][] = 'PublishedCourse.id in (select published_course_id from fx_resit_request where published_course_id is not null ) ';
			if (isset($this->department_id) && !empty($this->department_id)) {
				$options['conditions']['PublishedCourse.given_by_department_id'] = $this->department_id;
			} else if (isset($this->department_ids) && !empty($this->department_ids)) {
				$options['conditions']['PublishedCourse.given_by_department_id'] = $this->department_ids;
			}
		}
		if (isset($this->request->data['viewFxApplication']) && !empty($this->request->data['viewFxApplication'])) {
			if (isset($this->student_id) && !empty($this->student_id)) {
				$options['conditions']['PublishedCourse.academic_year'] = $this->request->data['ExamGrade']['academic_year'];
				$options['conditions']['PublishedCourse.semester'] = $this->request->data['ExamGrade']['semester'];
				$options['conditions'][] = 'PublishedCourse.id in (select published_course_id from fx_resit_request where student_id=' . $this->student_id . ' ) ';
				debug($options);
			} else {
				$options['conditions']['PublishedCourse.academic_year'] = $this->request->data['ExamGrade']['academic_year'];
				$options['conditions']['PublishedCourse.semester'] = $this->request->data['ExamGrade']['semester'];
				if (isset($this->department_id) && !empty($this->department_id)) {
					$options['conditions']['PublishedCourse.department_id'] = $this->department_id;
				} else if (isset($this->department_ids) && !empty($this->department_ids)) {
					$options['conditions']['PublishedCourse.department_id'] = $this->department_ids;
				}
			}
			$fxRequests = ClassRegistry::init('PublishedCourse')->find('all', $options);
		} else {
			$fxRequests = ClassRegistry::init('PublishedCourse')->find('all', $options);
			debug($options);
		}

		if (
			$this->role_id == ROLE_STUDENT
			&& $this->student_id
		) {
			foreach ($fxRequests as &$fxx) {
				foreach ($fxx['FxResitRequest']
					as $kxx => $kr) {
					if (
						$kr['student_id'] !=
						$this->student_id
					) {
						unset($fxx['FxResitRequest'][$kxx]);
					}
				}
			}
		}

		$this->set(compact('fxRequests'));
	}
	public function cancel_fx_resit_request(
		$id = null
	) {
		if (!$id) {
			$this->Session->setFlash(__('<span></span> Invalid request.'), 'default', array('class' => 'error-box error-message'));
			return $this->redirect(array('action' => 'request_fx_exam_sit'));
		}

		$isUserElegibleToDelete = ClassRegistry::init('FxResitRequest')->find('first', array('conditions' => array(
			'FxResitRequest.student_id' => $this->student_id,
			'FxResitRequest.id' => $id
		)));


		if (isset($isUserElegibleToDelete) && !empty($isUserElegibleToDelete)) {
			$reg = isset($isUserElegibleToDelete['FxResitRequest']['course_registration_id']) ? 1 : 0;
			$reg_add_id = isset($isUserElegibleToDelete['FxResitRequest']['course_registration_id']) ? $isUserElegibleToDelete['FxResitRequest']['course_registration_id'] : $isUserElegibleToDelete['FxResitRequest']['course_add_id'];
			$departmentAssignedFxToInstructor = ClassRegistry::init('MakeupExam')->makeUpExamApplied($this->student_id, $isUserElegibleToDelete['FxResitRequest']['published_course_id'], $reg_add_id, $reg);

			if ($departmentAssignedFxToInstructor) {
				$this->Session->setFlash(
					__('<span></span>Your request has already been assigned to instructor for exam retake.'),
					'default',
					array('class' => 'error-box error-message')
				);
				return $this->redirect(array('action' => 'request_fx_exam_sit'));
			} else {
				if (ClassRegistry::init('FxResitRequest')->delete($id)) {
					$this->Session->setFlash(
						__('<span></span>You have successfully cancelled your request.'),
						'default',
						array('class' => 'success-box success-message')
					);
					return $this->redirect(array('action' => 'request_fx_exam_sit'));
				}
			}
		}

		return $this->redirect(array('action' => 'request_fx_exam_sit'));
	}


	function cancel_ng_grade()
	{

		if (isset($this->request->data) && !empty($this->request->data['cancelNGGrade'])) {
			
			$gradeToBeCancelled = array();
			$courseAddandRegistrationExamGradeIds = array();
			$exam_grade_change_ids_to_delete = array();
			$exam_grade_ids_to_delete = array();
			$student_ids_to_regenarate_status = array();

			$ng_grades_without_any_assesnent = array();

			$ng_grades_registration_ids_without_any_assesnent = array();
			$ng_grades_add_ids_without_any_assesnent = array();
			$ng_grades_makeup_ids_without_any_assesnent = array();

			if (!empty($this->request->data['ExamGrade'])) {

				foreach ($this->request->data['ExamGrade'] as $key => $student) {

					if (is_int($key) && $student['gp'] == 1) {

						$courseAddandRegistrationExamGradeIds['ExamGrade'][] = $student['id'];
						$exam_grade_ids_to_delete[] = $student['id'];

						if (!empty($student_ids_to_regenarate_status) && !in_array($student['student_id'], $student_ids_to_regenarate_status)) {
							$student_ids_to_regenarate_status[] = $student['student_id'];
						} else if (empty($student_ids_to_regenarate_status)) {
							$student_ids_to_regenarate_status[] = $student['student_id'];
						}
						
						
						$tmp = $this->ExamGrade->find('first', array(
							'conditions' => array('ExamGrade.id' => $student['id']), 
							'contain' => array(
								'CourseAdd' => array(
									'ExamResult' => array(
										'conditions' => array(
											'ExamResult.course_add' => 0
										),
										'limit' => 1
									),
								), 
								'CourseRegistration' => array(
									'ExamResult' => array(
										'limit' => 1
									),
								),
								'MakeupExam' => array(
									'ExamResult' => array(
										'limit' => 1
									),
								),
								'ExamGradeChange'
							)
						));

						debug($tmp);

						if (!empty($tmp['ExamGradeChange'])) {
							//debug($tmp['ExamGradeChange']);
							foreach ($tmp['ExamGradeChange'] as $key => $exGrChange) {
								debug($exGrChange['id']);
								debug($exGrChange['exam_grade_id']);
								$exam_grade_change_ids_to_delete[] = $exGrChange['id'];
							}
						}

						if (isset($tmp['CourseRegistration']) && !empty($tmp['CourseRegistration']['id'])) {
							$courseAddandRegistrationExamGradeIds['CourseRegistration'][] = $tmp['CourseRegistration']['id'];
							if (isset($tmp['CourseRegistration']['ExamResult']) && empty($tmp['CourseRegistration']['ExamResult'])) {
								debug($tmp['CourseRegistration']['ExamResult']);
								$ng_grades_without_any_assesnent['ExamGrade'][] = $student['id'];
								$ng_grades_registration_ids_without_any_assesnent['CourseRegistration'][] = $tmp['CourseRegistration']['id'];
							}
						} else if (isset($tmp['CourseAdd']) && !empty($tmp['CourseAdd']['id'])) {
							$courseAddandRegistrationExamGradeIds['CourseAdd'][] = $tmp['CourseAdd']['id'];
							if (isset($tmp['CourseAdd']['ExamResult']) && empty($tmp['CourseAdd']['ExamResult'])) {
								debug($tmp['CourseAdd']['ExamResult']);
								$ng_grades_without_any_assesnent['ExamGrade'][] = $student['id'];
								$ng_grades_add_ids_without_any_assesnent['CourseAdd'][] = $tmp['CourseAdd']['id'];
							}
						} else if (isset($tmp['MakeupExam']) && !empty($tmp['MakeupExam']['id'])) {
							$courseAddandRegistrationExamGradeIds['MakeupExam'][] = $tmp['MakeupExam']['id'];
							if (isset($tmp['MakeupExam']['ExamResult']) && empty($tmp['MakeupExam']['ExamResult'])) {
								debug($tmp['MakeupExam']['ExamResult']);
								$ng_grades_without_any_assesnent['ExamGrade'][] = $student['id'];
								$ng_grades_makeup_ids_without_any_assesnent['MakeupExam'][] = $tmp['MakeupExam']['id'];
							}
						}
					}

				}
			}

			debug($courseAddandRegistrationExamGradeIds);
			debug($exam_grade_change_ids_to_delete);
			debug($exam_grade_ids_to_delete);
			debug($student_ids_to_regenarate_status);
			//exit();

			$students_count = count($student_ids_to_regenarate_status);
			$regenerated_students_count = 0;

			if (isset($courseAddandRegistrationExamGradeIds['ExamGrade']) && !empty($courseAddandRegistrationExamGradeIds['ExamGrade'])) {

				$deleted_grades_without_assesment = 0;

				if (!empty($ng_grades_without_any_assesnent)) {
					//debug($ng_grades_without_any_assesnent);
					if ($this->ExamGrade->deleteAll(array('ExamGrade.id' => $ng_grades_without_any_assesnent['ExamGrade']), false)) {
						if (!empty($ng_grades_registration_ids_without_any_assesnent)) {
							$this->ExamGrade->CourseRegistration->deleteAll(array('CourseRegistration.id' => $ng_grades_registration_ids_without_any_assesnent['CourseRegistration']), false);
							$deleted_grades_without_assesment += count($ng_grades_registration_ids_without_any_assesnent['CourseRegistration']);
						}

						if (!empty($ng_grades_add_ids_without_any_assesnent)) {
							$this->ExamGrade->CourseAdd->deleteAll(array('CourseAdd.id' => $ng_grades_add_ids_without_any_assesnent['CourseAdd']), false);
							$deleted_grades_without_assesment += count($ng_grades_add_ids_without_any_assesnent['CourseAdd']);
						}

						if (!empty($ng_grades_makeup_ids_without_any_assesnent)) {
							$this->ExamGrade->MakeupExam->deleteAll(array('MakeupExam.id' => $ng_grades_makeup_ids_without_any_assesnent['MakeupExam']), false);
							$deleted_grades_without_assesment += count($ng_grades_makeup_ids_without_any_assesnent['MakeupExam']);
						}
					}

					debug('Empty Grades without any assesment: ' . count($ng_grades_without_any_assesnent));
					debug('Deleted grades without any assesment: ' . $deleted_grades_without_assesment);
				}

				if (!empty($courseAddandRegistrationExamGradeIds['CourseRegistration'])) {
					debug($courseAddandRegistrationExamGradeIds['CourseRegistration']);
					//exit();
					if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION && 0) {
						// original
						if ($this->ExamGrade->CourseRegistration->deleteAll(array('CourseRegistration.id' => $courseAddandRegistrationExamGradeIds['CourseRegistration']), false)) {
							$this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false);
							$this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG grades and registration.');
						}
					} else {
						// check if there is continues assesment and delete only Exam Grade and Exam Grade Changes if any, Neway
						if ($this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false)) {
							$this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG  '. (count($courseAddandRegistrationExamGradeIds['ExamGrade']) > 1 ? 'grades' : ' grade') . '. Course Registration data and Assesment data is not affected');
						}
					}
				}

				if (!empty($courseAddandRegistrationExamGradeIds['CourseAdd'])) {
					if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION && 0) {
						// original
						if ($this->ExamGrade->CourseAdd->deleteAll(array('CourseAdd.id' => $courseAddandRegistrationExamGradeIds['CourseAdd']), false)) {
							$this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false);
							$this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG grades and course adds.');
						}
					} else {
						// check if there is continues assesment and delete only Exam Grade and Exam Grade Changes if any, Neway
						if ($this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false)) {
							$this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG  '. (count($courseAddandRegistrationExamGradeIds['ExamGrade']) > 1 ? 'grades' : ' grade') . '. Course Add data and Assesment data is not affected');
						}
					}
				}

				// Newly added, Neway
				if (!empty($courseAddandRegistrationExamGradeIds['MakeupExam'])) {
					if (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION && 0) {
						if ($this->ExamGrade->MakeupExam->deleteAll(array('MakeupExam.id' => $courseAddandRegistrationExamGradeIds['MakeupExam']), false)) {
							$this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false);
							$this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG grades and course adds.');
						}
					} else {
						// check if there is continues assesment and delete only Exam Grade and Exam Grade Changes if any, Neway
						if ($this->ExamGrade->deleteAll(array('ExamGrade.id' => $courseAddandRegistrationExamGradeIds['ExamGrade']), false)) {
							$this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG  '. (count($courseAddandRegistrationExamGradeIds['ExamGrade']) > 1 ? 'grades' : ' grade') . '. Make up  data and Assesment data is not affected');
						}
					}
				}

				// Delete Exam Grade changes associated to the given Exam Grade ID
				if (!empty($exam_grade_change_ids_to_delete)) {
					debug($this->ExamGrade->ExamGradeChange->deleteAll(array('ExamGradeChange.id' => $exam_grade_change_ids_to_delete), false));
				}

				// regenerate all students status
				if (!empty($student_ids_to_regenarate_status)) {
					foreach ($student_ids_to_regenarate_status as $key => $stdnt_id) {
						// regenarate all status regardless if it when it is regenerated
						$status_status = $this->ExamGrade->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id($stdnt_id, 0);

						if ($status_status == 3) {
							// status is regenerated in last 1 week, so check if there is any changes are possible after that
						} else {
							$regenerated_students_count++;
						}
					}
				}

				if (isset($this->request->data['ExamGrade']['select_all'])) {
					unset($this->request->data['ExamGrade']['select_all']);
				}
				//unset($this->request->data);
			}
		}

		$defaultacademicyear = $this->AcademicYear->current_academicyear();

		$applicable_grades = array(
			'F' => 'F',
			'I' => 'I (Incomplete)', 
			'DO' => 'DO (Dropout)', 
			'W' => 'W (Withdraw)',
		); 

		if (isset($this->request->data) && !empty($this->request->data['listPublishedCourses'])) {
			
			if (isset($this->college_ids) && !empty($this->college_ids) || count(explode('~', $this->request->data['ExamGrade']['department_id'])) > 1) {
				$type = 1;
			} else if (isset($this->department_ids) && !empty($this->department_ids)) {
				$type = 0;
			}

			if (isset($this->request->data['ExamGrade']['acadamic_year']) && !empty($this->request->data['ExamGrade']['acadamic_year'])) {
				$selected_academicyear = $this->request->data['ExamGrade']['acadamic_year'];
			} else if (isset($defaultacademicyear)) {
				$selected_academicyear =  $defaultacademicyear;
			}

			if (isset($this->request->data['ExamGrade']['program_id']) && !empty($this->request->data['ExamGrade']['program_id'])) {
				$selected_programs = $this->request->data['ExamGrade']['program_id'];
			} else if (isset($this->program_ids) && !empty($this->program_ids)) {
				$selected_programs = $this->program_ids;
			}

			if (isset($this->request->data['ExamGrade']['program_type_id']) && !empty($this->request->data['ExamGrade']['program_type_id'])) {
				$selected_program_types = $this->request->data['ExamGrade']['program_type_id'];
			} else if (isset($this->program_type_ids) && !empty($this->program_type_ids)) {
				$selected_program_types = $this->program_type_ids;
			}

			if (isset($this->request->data['ExamGrade']['semester']) && !empty($this->request->data['ExamGrade']['semester'])) {
				$selected_semester = $this->request->data['ExamGrade']['semester'];
			}

			if ((isset($this->request->data['ExamGrade']['department_id']) && !empty($this->request->data['ExamGrade']['department_id'])) || (isset($this->request->data['ExamGrade']['college_id']) && !empty($this->request->data['ExamGrade']['college_id'])) ) {
				
				$coll_id = array();
				
				if (isset($this->request->data['ExamGrade']['department_id']) && !empty($this->request->data['ExamGrade']['department_id'])) {
					$coll_id = explode('~', $this->request->data['ExamGrade']['department_id']);
				}

				if (count($coll_id) > 1) {
					$selected_dept_coll_id = $coll_id[1];
				} else if (isset($this->college_ids) && !empty($this->college_ids)) {
					$selected_dept_coll_id = $this->request->data['ExamGrade']['college_id'];
				} else if (isset($this->department_ids) && !empty($this->department_ids)) {
					$selected_dept_coll_id = $this->request->data['ExamGrade']['department_id'];
				}

			} else {
				if (isset($this->college_ids) && !empty($this->college_ids)) {
					$selected_dept_coll_id = array_values($this->college_ids)[0];
				} else if (isset($this->department_ids) && !empty($this->department_ids)) {
					$selected_dept_coll_id = array_values($this->department_ids)[0];
				}
			}

			//debug($this->request->data);

			$examGradeChanges = $this->ExamGrade->getListOfNGGrade(
				$selected_academicyear,
				$selected_semester,
				$selected_dept_coll_id,
				$selected_programs,
				$selected_program_types,
				$gradeToBeCancelled = (!empty($this->request->data['ExamGrade']['grade']) ? $this->request->data['ExamGrade']['grade']  : 0),
				$type
			);

			//debug($examGradeChanges);

			$turn_off_search = true;
			
			if (empty($examGradeChanges)) {
				$this->Flash->info('No auto or manual NG to ' . (!empty($this->request->data['ExamGrade']['grade']) ? $this->request->data['ExamGrade']['grade'] : (implode(', ', array_keys($applicable_grades)))). ' converted grade is found using the given search criteria.');
			} else {
				$turn_off_search = true;
			}

			$this->set(compact('examGradeChanges', 'turn_off_search'));
		}
		
		if (isset($this->college_ids) && !empty($this->college_ids)) {
			$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			$departments = array();
		} else if (isset($this->department_ids) && !empty($this->department_ids)) {
			$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			$colleges = array();
		}

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
			$departments = ClassRegistry::init('Department')->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids, $includePre = 1, $only_active = 1);
		}

		if (isset($this->request->data['ExamGrade']['select_all'])) {
			unset($this->request->data['ExamGrade']['select_all']);
		}

		$programs = $this->ExamGrade->CourseRegistration->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
		$programTypes = $this->ExamGrade->CourseRegistration->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));

		$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION, date('Y') - 1);

		$this->set(compact('departments', 'colleges', 'acyear_list', 'applicable_grades'));
	}
	
	function master_sheet_remedial($section_id = null, $ay1 = '2023', $ay2 = '24', $semester = 'I', $selected_program_id = '', $selected_program_type_id = '', $compact_version = '') 
	{

		$current_acy = $this->AcademicYear->current_academicyear();

		$program_id = (!empty($selected_program_id) ? $selected_program_id : PROGRAM_REMEDIAL);
		$program_type_id = (!empty($selected_program_type_id) ? $selected_program_type_id : PROGRAM_TYPE_REGULAR);

		$acyear_list = $acyear_array_data = $this->AcademicYear->academicYearInArray((explode('/', $current_acy)[0]) -2 , (explode('/', $current_acy)[0]));

		$compact_version_checked = (!empty($compact_version) && $compact_version ? 1 : 0);

		$programsss = array();
		$programsss[PROGRAM_REMEDIAL] = 'Remedial';

		$programTypesss = array();

		$programTypesss[PROGRAM_TYPE_REGULAR] = 'Regular';
		$programTypesss[PROGRAM_TYPE_EVENING] = 'Evening';
		$programTypesss[PROGRAM_TYPE_WEEKEND] = 'Weekend';

		$remedial_sections = $this->ExamGrade->CourseRegistration->Student->Section->find('list', array(
			'conditions' => array(
				'Section.program_id' => $program_id,
				'Section.program_type_id' => $program_type_id,
				'Section.academicyear' => $current_acy,
			),
			'order' => array('Section.year_level_id' => 'ASC', 'Section.college_id' => 'ASC', 'Section.department_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC')
		));

		if (!empty($remedial_sections)) {
			$remedial_sections = array('0' => '[ Select Section ]') + $remedial_sections;
		}

		//debug($remedial_sections);

		$this->set(compact(
			'acyear_list',
			'programsss',
			'programTypesss',
			'remedial_sections',
			'program_id',
			'program_type_id',
			'compact_version_checked'
		));
	

		if (!empty($section_id) && $section_id > 0 ) {

			$section_combo_id = $section_or_published_course_id = $section_id;

			$academic_year = $ay1 .'/'. $ay2;

			$section_details = $this->ExamGrade->CourseRegistration->Student->Section->find('first', array(
				'conditions' => array(
					'Section.id' => $section_or_published_course_id
				),
				'contain' => array(
					'Department', 
					'College',
					'YearLevel' => array('id', 'name'),
					'ProgramType' => array('id', 'name', 'shortname'), 
					'Program' => array('id', 'name', 'shortname'),
				),
			));

			$course_ids = $this->ExamGrade->CourseRegistration->PublishedCourse->find('list', array(
				'conditions' => array('PublishedCourse.section_id' => $section_id), 
				'fields' => array('PublishedCourse.course_id'),
				'recursive' => -1
			));

			$master_sheet = $this->ExamGrade->getMasterSheetRemedial($section_or_published_course_id, $academic_year, $semester);
			
			$section_detail = $section_details['Section'];
			$department_detail = $section_details['Department'];
			$college_detail = $section_details['College'];
			$program_detail = $section_details['Program'];
			$program_type_detail = $section_details['ProgramType'];

			$program_id = $section_details['Program']['id'];
			$program_type_id = $section_details['ProgramType']['id'];
			$department_id = $section_details['Department']['id'];
			$academic_year_selected = $academic_year;
			$semester_selected = $semester;

			//store to session for excel
			$this->Session->write('master_sheet', $master_sheet);
			$this->Session->write('section_detail', $section_detail);
			$this->Session->write('department_detail', $department_detail);
			$this->Session->write('college_detail', $college_detail);
			$this->Session->write('program_detail', $program_detail);
			$this->Session->write('program_type_detail', $program_type_detail);
			$this->Session->write('program_id', $program_id);
			$this->Session->write('program_type_id', $program_type_id);
			$this->Session->write('department_id', $department_id);
			$this->Session->write('academic_year_selected', $academic_year_selected);
			$this->Session->write('semester_selected', $semester_selected);
			$this->Session->write('compact_version', $compact_version);


			$this->set(compact(
				'published_course_id',
				'publishedCourses',
				'programs',
				'program_types',
				'departments',
				'publishedCourses',
				'published_course_combo_id',
				'department_combo_id',
				'master_sheet',
				'section_detail',
				'college_detail',
				'department_detail',
				'program_detail',
				'program_type_detail',
				'academic_year',
				'semester',
				'program_id',
				'program_type_id',
				'department_id',
				'academic_year_selected',
				'semester_selected',
				'acyear_list',
				'programsss',
				'programTypesss',
				'section_combo_id'
			));
		}

		$this->render('master_sheet_remedial');
		return;
	}

	function export_remedial_mastersheet_xls()
	{
		$this->autoLayout = false;
		$master_sheet = $this->Session->read('master_sheet');
		$section_detail = $this->Session->read('section_detail');
		$department_detail = $this->Session->read('department_detail');
		$college_detail = $this->Session->read('college_detail');
		$program_detail = $this->Session->read('program_detail');
		$program_type_detail = $this->Session->read('program_type_detail');
		$program_id = $this->Session->read('program_id');
		$program_type_id = $this->Session->read('program_type_id');
		$department_id = $this->Session->read('department_id');
		$academic_year = $this->Session->read('academic_year_selected');
		$semester = $this->Session->read('semester_selected');

		$compact_version = $this->Session->read('compact_version');

		$filename = "Remedial_Master_Sheet_" . (str_replace(' ', '_', (trim(str_replace('  ', ' ', $section_detail['name']))))) . '_' . (str_replace('/', '-', $academic_year)) . '_' . $semester . '_' . date('Y-m-d');

		$this->set(compact(
			'master_sheet',
			'section_detail',
			'college_detail',
			'department_detail',
			'program_detail',
			'program_type_detail',
			'program_id',
			'program_type_id',
			'filename',
			'department_id',
			'academic_year',
			'semester'
		));

		if ($compact_version) {
			$this->render('/Elements/remedial_master_sheet_compact_xls');
		} else {
			$this->render('/Elements/remedial_master_sheet_xls');
		}
	}

	function get_remedial_sections_combo($paramaters)
	{
		$this->layout = 'ajax';
		$criteriaLists = explode('~', $paramaters);
		debug($criteriaLists);

		if (!empty($criteriaLists) && count($criteriaLists) > 3) {
			$academicYear = str_replace('-', '/', $criteriaLists[0]);
			$semester = $criteriaLists[1];
			$program_id = $criteriaLists[2];
			$program_type_id = $criteriaLists[3];

			$options = array(
				'conditions' => array(
					'Section.academicyear' => $academicYear,
					'Section.program_id' => $program_id,
					'Section.program_type_id' => $program_type_id,
					//'Section.archive' => 0,
				),
				'contain' => array(
					'Program', 
					'ProgramType',
					'Department', 
					'YearLevel', 
					'College',
					'PublishedCourse'
				),
				'order' => array('Section.year_level_id' => 'ASC', 'Section.college_id' => 'ASC', 'Section.department_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC')
			);

			debug($options);
			
			$sections =  ClassRegistry::init('Section')->find('all', $options);

			$remedialSectionOrganized = array();

			if (!empty($sections)){
				$remedialSectionOrganized[''] = '[ Select Section ]';
				foreach ($sections as $k => $v) {
					if (!empty($v['YearLevel']['name'])) {
						$remedialSectionOrganized[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
					} else {
						$remedialSectionOrganized[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? ' Remedial' : ' Pre/1st') . ")";
					}
				}
			} else {
				$remedialSectionOrganized[''] = '[ No Active Sections, Try Changing Filters ]';
			}
		}

		$this->set(compact('remedialSectionOrganized'));
	}
	
}
