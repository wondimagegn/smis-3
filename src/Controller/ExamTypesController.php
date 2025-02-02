<?php
class ExamTypesController extends AppController
{

	public $name = 'ExamTypes';
	public $components = array('AcademicYear');
	public $menuOptions = array(
		'parent' => 'grades',
		'exclude' => array(
			'get_exam_type_view_page',
			'get_exam_type_entry_form',
			'index',
			'assessement_template',
			'enable_course_for_moodle'
		),
		'alias' => array(
			'college_exam_type_mgt_for_instructor' => 'Freshman Course Exam Setup',
			'exam_type_mgt_for_instructor' => 'Department Course Exam Setup',
			'add' => 'Your Course Exam Setup'
			//'index'=>'View Exam Setup',
			//'add'=>'Manage Exam Setup',
			//'exam_type_mgt_for_instructor' => 'Manage Exam Setup'
		)
	);

	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->Auth->Allow(
			'get_exam_type_entry_form', 
			'get_exam_type_view_page', 
			'assessement_template'/* ,
			'enable_course_for_moodle' */,
			'sync_new_enrollments'
		);

		if ($this->Session->check('Message.auth')) {
			$this->Session->delete('Message.auth');
		}

		if ($this->Auth->user() && in_array($this->request->params['action'], array('login'))) {
			return $this->redirect($this->Auth->logout());
		}

	}

	public function beforeRender()
	{
		//$acyear_array_data = $this->AcademicYear->acyear_array();
		//$defaultacademicyear = $this->AcademicYear->current_academicyear();
		$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
		$defaultacademicyear = $current_acy_and_semester['academic_year'];
		$defaultsemester = $current_acy_and_semester['semester'];

		$current_academicyear = $this->AcademicYear->current_academicyear();

		if ($this->role_id == ROLE_INSTRUCTOR) {

			$acyear_array_data = $this->ExamType->PublishedCourse->CourseInstructorAssignment->find('list', array(
				'conditions' => array(
					'CourseInstructorAssignment.staff_id in (select id from staffs where user_id="' . $this->Auth->user('id') . '")'
				),
				'fields' => array(
					'CourseInstructorAssignment.academic_year',
					'CourseInstructorAssignment.academic_year'
				),
				'order' => array('CourseInstructorAssignment.academic_year' => 'DESC')
			));

			if (empty($acyear_array_data)) {
				//$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $current_academicyear)[0]) - ACY_BACK_FOR_ALL), (explode('/', $current_academicyear)[0]));
				$acyear_array_data[$current_academicyear] = $current_academicyear;
			}
			
		} else {
			$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $current_academicyear)[0]) - ACY_BACK_FOR_ALL), (explode('/', $current_academicyear)[0]));
		}

		$this->set(compact('acyear_array_data', 'defaultacademicyear', 'defaultsemester'));
	}

	public function index()
	{
		if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamTypes/add') && $this->role_id == ROLE_INSTRUCTOR) {
			return $this->redirect(array('action' => 'add'));
		} else if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamTypes/exam_type_mgt_for_instructor') && $this->role_id == ROLE_DEPARTMENT) {
			return $this->redirect(array('action' => 'exam_type_mgt_for_instructor'));
		} else if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamTypes/college_exam_type_mgt_for_instructor') && $this->role_id == ROLE_COLLEGE) {
			return $this->redirect(array('action' => 'college_exam_type_mgt_for_instructor'));
		} else {
			$this->Flash->warning(__('You don\'t have the previlage to access this area. Check your permissions in permission managent and add to your account if available, or contact system administrator for further assistance.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	public function add($published_course_id = null)
	{
		if ($this->role_id == ROLE_INSTRUCTOR) {
			debug($this->request->data);
			$this->__exam_type_mgt($published_course_id, 'instructor');
		} else if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamTypes/exam_type_mgt_for_instructor') && $this->role_id == ROLE_DEPARTMENT) {
			return $this->redirect(array('action' => 'exam_type_mgt_for_instructor'));
		} else if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamTypes/college_exam_type_mgt_for_instructor') && $this->role_id == ROLE_COLLEGE) {
			return $this->redirect(array('action' => 'college_exam_type_mgt_for_instructor'));
		} else {
			$this->Flash->warning(__('You don\'t have the previlage to access this area.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	public function college_exam_type_mgt_for_instructor($published_course_id = null)
	{
		if ($this->role_id == ROLE_COLLEGE || (isset ($this->college_id) && !empty ($this->college_id))) {
			//First I was thinking to limit this task for those who has only college or department role. 
			//But now it is accessible to anyone as long as he/she has college id
			//if($this->role_id == 6 || $this->role_id == 5) {
			$edit = 0;
			$programs = $this->ExamType->PublishedCourse->Section->Program->find('list');
			$program_types = $this->ExamType->PublishedCourse->Section->ProgramType->find('list');

			if (!empty ($this->request->data)) {
				$publishedCourses = $this->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection($this->college_id, $this->request->data['ExamType']['acadamic_year'], $this->request->data['ExamType']['semester'], $this->request->data['ExamType']['program_id'], $this->request->data['ExamType']['program_type_id']);
				if (empty ($publishedCourses)) {
					$this->Flash->info(__('No published courses found with the selected search criteria.'));
				} else {
					$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
				}
			} else {
				$publishedCourses = array();
				$published_course_combo_id = $published_course_id;
			}

			$this->set(compact('publishedCourses', 'programs', 'program_types'));

			if ($published_course_id == null && isset ($this->request->data['ExamType']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamType']['published_course_id'];
			}
			
			if (isset ($this->request->data['listPublishedCourses'])) {
				/**************************  Begin "List Published Courses" button  **************************/
				unset($this->request->data['ExamType']['published_course_id']);
				$published_course_combo_id = "";
			} else {
				/*******************************  Save Exam Type button clicked  ***************************/
				$this->__exam_type_mgt($published_course_id, 'college');
			}

			$this->set(compact('published_course_id', 'published_course_combo_id', 'edit'));
		} else {
			$this->Flash->error(__('You need to have assigned college through college or department or instructor role to access this area. Please contact your system administrator to get college or department or instructor role.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	function exam_type_mgt_for_instructor($published_course_id = null)
	{
		if ($this->role_id == ROLE_DEPARTMENT || (isset ($this->department_id) && !empty ($this->department_id))) {
			$edit = 0;
			$programs = $this->ExamType->PublishedCourse->Section->Program->find('list');
			$program_types = $this->ExamType->PublishedCourse->Section->ProgramType->find('list');

			if (!empty ($this->request->data)) {
				$department_id = $this->department_id;
				$publishedCourses = $this->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $this->request->data['ExamType']['acadamic_year'], $this->request->data['ExamType']['semester'], $this->request->data['ExamType']['program_id'], $this->request->data['ExamType']['program_type_id']);

				if (empty ($publishedCourses)) {
					$this->Flash->info(__('No published courses found with the selected search criteria.'));
				} else {
					$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
				}

				$this->set(compact('publishedCourses'));
			} else {

				$publishedCourses = array();
				$published_course_combo_id = $published_course_id;
				$this->set(compact('publishedCourses'));
			}

			if ($published_course_id == null && isset ($this->request->data['ExamType']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamType']['published_course_id'];
			}

			if (isset ($this->request->data['listPublishedCourses'])) {
				/**************************  Begin "List Published Courses" button  **************************/
				unset($this->request->data['ExamType']['published_course_id']);
				$published_course_combo_id = "";
			} else {
				/*******************************  Save Exam Type button clicked  ***************************/
				$this->__exam_type_mgt($published_course_id, 'department');
			}

			$this->set(compact('programs', 'program_types', 'published_course_id', 'published_course_combo_id', 'edit'));
		} else {
			$this->Flash->warning(__('You need to have department role to access this area. Please contact your system administrator to get either department or instructor role.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	private function __exam_type_mgt($published_course_id = null, $sourse = 'instructor')
	{
		$selected_acadamic_year = $this->AcademicYear->current_academicyear();
		$selected_semester = 'I';

		// $acadamic_year = $selected_acadamic_year = $this->AcademicYear->current_acy_and_semester()['academic_year'];
		// $semester = $selected_semester = $this->AcademicYear->current_acy_and_semester()['semester'];

		if (isset($this->request->data) && !empty($this->request->data['ExamType']['acadamic_year'])) {
			$acadamic_year = $selected_acadamic_year = $this->request->data['ExamType']['acadamic_year'];
		}

		if (isset($this->request->data) && !empty($this->request->data['ExamType']['semester'])) {
			$semester = $selected_semester = $this->request->data['ExamType']['semester'];
		}

		debug($this->request->data);

		$edit = 0;
		$published_course_combo_id = "";
		$percent_sum_validation = true;
		$grade_submitted = false;
		$deleted_exam_types = array();
		$view_only = false;
		$fraud = false;
		$exam_types = array();

		$instructor_id = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

		//List of exam setup retrival for redirection with published course ID
		if (!empty ($published_course_id)) {
			$exam_types = $this->ExamType->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id), 'recursive' => -1));
			$published_course_combo_id = $published_course_id;
		}
		//End of list of exam setup retrival for redirection with published course ID

		//Checking if the user is ligible to manage published course exam type
		if (!empty ($this->request->data['ExamType']['published_course_id'])) {
			$published_course_id = $this->request->data['ExamType']['published_course_id'];
		}

		if (!empty ($published_course_id)) {

			$instructor_id_for_checking = $this->ExamType->PublishedCourse->CourseInstructorAssignment->field('staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, /* 'CourseInstructorAssignment.type LIKE \'%Lecture%\'',  */ 'isprimary' => 1));

			$published_course_department = $this->ExamType->PublishedCourse->find('first', array(
				'conditions' => array('PublishedCourse.id' => $published_course_id),
				'contain' => array('Section' => array('Department'))
			));

			//Do you have the right to manage exam type
			$assigned_instructor_user_id = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
			$active_account = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));

			//debug($instructor_id_for_checking == $instructor_id);
			//debug($instructor_id_for_checking);
			//debug($instructor_id);
			//Role based checking is now removed

			//if(!(($this->role_id == 6 && $this->department_id == $published_course_department['Section']['Department']['id'] && $active_account == 0) || ($this->role_id == 5 && $this->college_id == $published_course_department['PublishedCourse']['college_id'] && $active_account == 0) || ($instructor_id_for_checking == $instructor_id))) {
			if (!(($this->department_id == (isset($published_course_department['Section']['Department']['id']) ? $published_course_department['Section']['Department']['id'] : "") && $active_account == 0) || ($this->college_id == (isset($published_course_department['PublishedCourse']['college_id']) ? $published_course_department['PublishedCourse']['college_id'] : "") && $active_account == 0) || ($instructor_id_for_checking == $instructor_id))) {
				$this->Flash->error(__('Sorry the selected published course is not assigned to you to manage its exam type. Please select a valid published course again.'));
				return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
			}
			//End of do you have the right to manage exam result and grade

			//Do you have view only access? (It is not neccessary as the user is trapped on the above checking)
			$login_instructor_id = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

			if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
				$view_only = true;
			}
		}
		//End for do they have view only access?

		if (!empty ($this->request->data)) {
			
			$exam_types = $this->ExamType->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id), 'recursive' => -1));
			
			if (count($exam_types) > 0) {
				$edit = 1;
			}

			$percent_sum = 0;
			$mandatory_exam = 0;

			$course_exam_grade = $this->ExamType->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id,
				),
				'contain' => array(
					'CourseRegistration' => array(
						'ExamGrade' => array(
							'fields' => 'ExamGrade.id'
						)
					),
					'CourseAdd' => array(
						'ExamGrade' => array(
							'fields' => 'ExamGrade.id'
						)
					)
				)
			));

			if ((isset ($course_exam_grade[0]['CourseRegistration'][0]['ExamGrade']) && count($course_exam_grade[0]['CourseRegistration'][0]['ExamGrade']) > 0) || (isset ($course_exam_grade[0]['CourseAdd'][0]['ExamGrade']) && count($course_exam_grade[0]['CourseAdd'][0]['ExamGrade']) > 0)) {
				$this->Flash->error(__('You can not apply changes on the exam setup of a grade submitted course.'));
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : 'exam_type_mgt_for_instructor'), $published_course_id));
			}

			$exam_type_user_edit_id = array();
			//debug($this->request->data['ExamType']);

			if (!empty($this->request->data['ExamType'])) {
				foreach ($this->request->data['ExamType'] as $key => &$exam_type) {
					if (is_array($exam_type)) {
						
						if (!isset ($exam_type['mandatory'])) {
							$exam_type['mandatory'] = 0;
						} else if ($exam_type['mandatory'] == 1) {
							$mandatory_exam++;
						}

						if ($edit == 1 && isset ($exam_type['id'])) {

							$exam_type_detail = $this->ExamType->find('first', array('conditions' => array('id' => $exam_type['id']), 'recursive' => -1));
							$exam_type_user_edit_id[] = $exam_type['id'];
							
							if ($exam_type_detail['ExamType']['published_course_id'] != $published_course_id) {
								$fraud = true;
								break;
							} else if ($exam_type['percent'] != "") {

								$exam_type_exam_result = $this->ExamType->ExamResult->find('first', array(
									'conditions' => array(
										'ExamResult.exam_type_id' => $exam_type['id']
									),
									'order' => array('ExamResult.result' => 'DESC')
								));

								if ($exam_type_exam_result['ExamResult']['result'] > $exam_type['percent']) {
									$this->Flash->error( __('There is/are already recorded exam result for "' . $exam_type['exam_name'] . '" and the maximum percentage can only be ' . $exam_type_exam_result['ExamResult']['result'] . '. Please delete the already recorded exam result/s to enter a larger percentage.'));
									return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : 'exam_type_mgt_for_instructor'), $published_course_id));
								}
							}
						}

						$exam_type['published_course_id'] = $published_course_id;
						//$exam_type['section_id'] = $section_course[0];

						if (trim($exam_type['percent']) != "" && is_numeric($exam_type['percent']) && $exam_type['percent'] > 0 && $exam_type['percent'] <= 100) {
							$percent_sum += $exam_type['percent'];
						} else {
							$percent_sum_validation = false;
						}
					}
				}
			}

			if ($fraud) {
				$this->Flash->error(__('The system encountered a problem while processing your exam setup submission. Please try your submission again.'));
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : 'exam_type_mgt_for_instructor'), $published_course_id));
			}

			$selected_acadamic_year = $this->request->data['ExamType']['acadamic_year'];
			$selected_semester = $this->request->data['ExamType']['semester'];

			if (!empty ($this->request->data['ExamType']['published_course_id'])) {
				$published_course_combo_id = $this->request->data['ExamType']['published_course_id'];
			}
			//$section_id = $section_course[0];
			if (strcasecmp($sourse, 'instructor') != 0) {
				$program_type_id = $this->request->data['ExamType']['program_type_id'];
				$program_id = $this->request->data['ExamType']['program_id'];
				unset($this->request->data['ExamType']['program_type_id']);
				unset($this->request->data['ExamType']['program_id']);
			}

			unset($this->request->data['ExamType']['published_course_id']);
			unset($this->request->data['ExamType']['acadamic_year']);
			unset($this->request->data['ExamType']['semester']);
			unset($this->request->data['ExamType']['edit']);

			//debug($this->request->data);
			//debug($edit);
			if ($edit == 1 && !empty ($published_course_id)) {

				$exam_types_db = $this->ExamType->find('all', array(
					'conditions' => array(
						'ExamType.published_course_id' => $published_course_id,
						//'ExamType.section_id' => $section_course[0]
					),
					'recursive' => -1
				));

				if (!empty($exam_types_db)) {
					foreach ($exam_types_db as $key => $exam_type_db) {
						if (!in_array($exam_type_db['ExamType']['id'], $exam_type_user_edit_id)) {
							$deleted_exam_types[] = $exam_type_db['ExamType']['id'];
						}
					}
				}

				if (isset($deleted_exam_types) && !empty($deleted_exam_types)) {
					foreach ($deleted_exam_types as $key => $user_deleted_exam_type_id) {
						$exam_result_count = $this->ExamType->ExamResult->find('count', array('conditions' => array('exam_type_id' => $user_deleted_exam_type_id), 'recursive' => -1));
						if ($exam_result_count > 0) {
							$exam_type_name = $this->ExamType->field('exam_name', array('ExamType.id' => $user_deleted_exam_type_id));
							$this->Flash->error(__($exam_type_name . ' exam type has exam result and could not be deleted. Please delete the exam result before you make deletion.'));
							return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : 'exam_type_mgt_for_instructor'), $published_course_id));
						}
					}
				}
			}

			$duplicate_exam_type = false;
			$exam_type_name = array();

			if (!empty($this->request->data['ExamType'])) {
				foreach ($this->request->data['ExamType'] as $key => $exam_type_check) {
					if (is_array($exam_type_check)) {
						$exam_type_name[] = $exam_type_check['exam_name'];
					}
				}
			}

			if (count($exam_type_name)) {
				for ($i = 0; $i < count($exam_type_name); $i++) {
					for ($j = $i + 1; $j < count($exam_type_name); $j++) {
						if (strcasecmp($exam_type_name[$i], $exam_type_name[$j]) == 0) {
							$duplicate_exam_type = $exam_type_name[$i];
							break 2;
						}
					}
				}
			}

			$this->ExamType->create();
			$this->set($this->request->data['ExamType']);

			if ($duplicate_exam_type) {
				$this->Flash->error( __($duplicate_exam_type . ' exam type is duplicated. Please use uniqe name.'));
			} else if ($percent_sum_validation && $percent_sum != 100) {
				$this->Flash->error( __('The sum of exam percentage should be equal with 100.'));
			} else if ($percent_sum_validation && $mandatory_exam == 0) {
				$this->Flash->error( __('You are required to select at least one exam (for example final exam) as a mandatory exam.'));
			} else if ($this->ExamType->saveAll($this->request->data['ExamType'], array('validate' => 'first'))) {
				if (!empty ($deleted_exam_types)) {
					if (!$this->ExamType->deleteAll(array('ExamType.id' => $deleted_exam_types), false)) {
						$this->Flash->error(__('Exam type entry/update is saved but deletion for exam type is interrupted. Please check your exam type entry and/or update for consistency.'));
						return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : 'exam_type_mgt_for_instructor'), $published_course_id));
					}
				}

				/* if (isset($deleted_exam_types) && !empty($deleted_exam_types)) {
					foreach ($deleted_exam_types as $key => $user_deleted_exam_type_id) {
						$this->ExamType->delete($user_deleted_exam_type_id);
					}
				} */

				$this->Flash->success(__('The exam type has been saved.'));
				return $this->redirect(array('controller' => 'examResults', 'action' => 'add', $published_course_id));
				return $this->redirect(array('action' => (strcasecmp($sourse, 'instructor') == 0 ? 'add' : 'exam_type_mgt_for_instructor'), $published_course_id));
			} else {
				$this->Flash->error(__('The exam type could not be saved. Please, try again.'));
			}
		}
		//End of if(!empty($this->request->data))

		//Published courses retrival
		if (strcasecmp($sourse, 'instructor') == 0) {
			$publishedCourses = $this->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection($selected_acadamic_year, $selected_semester, $instructor_id);
		} else if (!empty ($this->request->data) || $published_course_id) {
			$department_id = $this->department_id;
			if (empty ($this->request->data)) {
				$published_course_detail = $this->ExamType->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1));
				$program_type_id = $published_course_detail['PublishedCourse']['program_type_id'];
				$program_id = $published_course_detail['PublishedCourse']['program_id'];
				$acadamic_year = $published_course_detail['PublishedCourse']['academic_year'];
				$semester = $published_course_detail['PublishedCourse']['semester'];
				//$published_course_combo_id = $published_course_id;
			} else {
				//$program_type_id = $this->request->data['ExamType']['program_type_id'];
				//$program_id = $this->request->data['ExamType']['program_id'];
				$acadamic_year = $selected_acadamic_year;//$this->request->data['ExamType']['acadamic_year'];
				$semester = $selected_semester; //$this->request->data['ExamType']['semester'];
			}

			$publishedCourses = $this->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $acadamic_year, $semester, $program_id, $program_type_id);
		}
		//End of published courses retrival

		if (!empty ($publishedCourses)) {
			$publishedCourses = array('' => '[ Select Course ]') + $publishedCourses;
		}

		//The user need to select a course inorder to see the record page

		if (0 && !empty($publishedCourses) && empty ($this->request->data)) {
			$default_course_section = array_keys($publishedCourses);
			$default_course_section = array_keys($publishedCourses[$default_course_section[0]]);
			//$default_course_section = explode('~', $default_course_section[0]);
			//$section_id = $default_course_section[0];
			$published_course_id = $default_course_section[0];
			$exam_types = $this->ExamType->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id, /* 'ExamType.section_id' => $section_id */), 'recursive' => -1));
			
			if (count($exam_types) > 0) {
				$edit = 1;
			}

			//debug($exam_types);
		}

		$course_exam_grade = array();

		if (!empty ($published_course_id)) {

			$course_exam_grade = $this->ExamType->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id,
					//'PublishedCourse.section_id' => $section_id
				),
				'contain' => array(
					'CourseRegistration' => array(
						'ExamGrade' => array(
							'fields' => 'ExamGrade.id'
						)
					),
					'CourseAdd' => array(
						'ExamGrade' => array(
							'fields' => 'ExamGrade.id'
						)
					),
				),
			));
			//debug($course_exam_grade);
		}

		if ((isset ($course_exam_grade[0]['CourseRegistration'][0]['ExamGrade']) && count($course_exam_grade[0]['CourseRegistration'][0]['ExamGrade']) > 0) || (isset ($course_exam_grade[0]['CourseAdd'][0]['ExamGrade']) && count($course_exam_grade[0]['CourseAdd'][0]['ExamGrade']) > 0)) {
			//	$grade_submitted = true;
			$grade_submitted = ClassRegistry::init('ExamGrade')->editableExamType($published_course_id);
		}
		
		$all_exam_setup_detail = 'exam_name,percent,order,mandatory,edit';

		$this->set(compact('grade_submitted', 'edit', 'exam_types', 'publishedCourses', 'selected_acadamic_year', 'selected_semester', 'published_course_combo_id', 'all_exam_setup_detail', 'program_type_id', 'program_id', 'acadamic_year', 'semester'));
	}

	function get_exam_type_entry_form($published_course_id = null)
	{
		if ($this->Auth->user('id')) {

			if (strcasecmp('null', $published_course_id) == 0) {
				$published_course_id = null;
			}

			$this->layout = 'ajax';
			$edit = 0;
			$exam_types = array();
			$grade_submitted = false;
			$view_only = false;
			$enable_for_moodle = false;

			if (!empty($published_course_id)) {

				$published_course_department = $this->ExamType->PublishedCourse->find('first', array(
					'conditions' => array('PublishedCourse.id' => $published_course_id),
					'contain' => array(
						'Section' => array('Department'),
						'Course' => array('id', 'course_title', 'course_code')
					)
				));
	
				$ac_yearsForMoodle = array();

				$current_acy = $this->AcademicYear->current_academicyear();
	
				if (ENABLE_MOODLE_INTEGRATION == 1 && is_numeric(ACY_BACK_FOR_MOODLE_INTEGRATION) && ACY_BACK_FOR_MOODLE_INTEGRATION) {
					$ac_yearsForMoodle = $this->AcademicYear->academicYearInArray(((explode('/', $current_acy)[0]) - ACY_BACK_FOR_MOODLE_INTEGRATION), (explode('/', $current_acy)[0]));
				} else if (ENABLE_MOODLE_INTEGRATION == 1) {
					$ac_yearsForMoodle[$current_acy] = $current_acy;
				}

				debug($ac_yearsForMoodle);
	
				if (isset($ac_yearsForMoodle) && !empty($ac_yearsForMoodle) && !empty($published_course_department['PublishedCourse'])) {
					if (is_array($ac_yearsForMoodle)) {
						if (in_array($published_course_department['PublishedCourse']['academic_year'], $ac_yearsForMoodle)) {
							$enable_for_moodle = 1;
						}
					} else if ($ac_yearsForMoodle == $published_course_department['PublishedCourse']['academic_year']) {
						$enable_for_moodle = 1;
					}
					$ac_yearsForMoodle = array_keys($ac_yearsForMoodle);
					$acy_ranges_by_comma_quoted = "'" . implode ( "', '", $ac_yearsForMoodle ) . "'";

					if ($published_course_department['PublishedCourse']['enable_for_moodle']) {

						$enrolled_students = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
							'conditions' => array(
								'MoodleCourseEnrollment.published_course_id' => $published_course_department['PublishedCourse']['id'],
								'MoodleCourseEnrollment.user_role' => 'student',
							),
							'group' => array('MoodleCourseEnrollment.username')
						));

						$enrolled_primary_instructor = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
							'conditions' => array(
								'MoodleCourseEnrollment.published_course_id' => $published_course_department['PublishedCourse']['id'],
								'MoodleCourseEnrollment.user_role' => 'editingteacher',
							),
							'group' => array('MoodleCourseEnrollment.username')
						));

						$enrolled_secondary_instructor  = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
							'conditions' => array(
								'MoodleCourseEnrollment.published_course_id' => $published_course_department['PublishedCourse']['id'],
								'MoodleCourseEnrollment.user_role' => 'teacher',
							),
							'group' => array('MoodleCourseEnrollment.username')
						));

						debug($enrolled_students);
						debug($enrolled_primary_instructor);
						debug($enrolled_secondary_instructor);

						$this->set(compact('enrolled_students', 'enrolled_primary_instructor', 'enrolled_secondary_instructor'));

					}
				}

				//User legibility checking
				$instructor_id_for_checking = $this->ExamType->PublishedCourse->CourseInstructorAssignment->field('CourseInstructorAssignment.staff_id', array('CourseInstructorAssignment.published_course_id' => $published_course_id, 'CourseInstructorAssignment.type LIKE \'%Lecture%\'', 'isprimary' => 1));
				//Not to block department head his/her own assigned courses
				$login_instructor_id = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('Staff.user_id' => $this->Auth->user('id')));

				$assigned_instructor_user_id = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('Staff.user_id', array('Staff.id' => $instructor_id_for_checking));
				$active_account = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->User->field('active', array('User.id' => $assigned_instructor_user_id));
				//Role based checking is removed
				//if(($this->role_id == 5 || $this->role_id == 6) && $active_account == 1 && $instructor_id_for_checking != $login_instructor_id)
				if ($active_account == 1 && $instructor_id_for_checking != $login_instructor_id) {
					$view_only = true;
				}
				//End of user eligibility checking

				//Now it is accessible as long as he/she has permission
				//if($this->role_id == 5 || $this->role_id == 6 || $instructor_id_for_checking == $login_instructor_id) {
				if (1) {
					$instructor_id = $this->ExamType->PublishedCourse->CourseInstructorAssignment->Staff->field('id', array('user_id' => $this->Auth->user('id')));
					$course_assigned = $this->ExamType->PublishedCourse->CourseInstructorAssignment->find('count', array('conditions' => array('CourseInstructorAssignment.published_course_id' => $published_course_id), 'recursive' => -1));

					//Now it is accessible as long as s/he has the privilage (actually it is public)
					//if($course_assigned <= 0 && !($this->role_id == 6 || $this->role_id == 5))
					if (0) {
						$published_course_id = null;
					} else {
						$exam_types = $this->ExamType->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id), 'recursive' => -1));
					}

					if (count($exam_types) > 0) {
						$edit = 1;
					}

					$course_exam_grade = $this->ExamType->PublishedCourse->find('all', array(
						'conditions' => array(
							'PublishedCourse.id' => $published_course_id,
						),
						'contain' => array(
							'CourseRegistration' => array(
								'ExamGrade' => array(
									'fields' => 'ExamGrade.id'
								)
							),
							'CourseAdd' => array(
								'ExamGrade' => array(
									'fields' => 'ExamGrade.id'
								)
							)
						),
					));

					if ((isset ($course_exam_grade[0]['CourseRegistration'][0]['ExamGrade']) && count($course_exam_grade[0]['CourseRegistration'][0]['ExamGrade']) > 0) || (isset ($course_exam_grade[0]['CourseAdd'][0]['ExamGrade']) && count($course_exam_grade[0]['CourseAdd'][0]['ExamGrade']) > 0)) {
						$grade_submitted = true;
					}
					
					$this->set(compact('published_course_department'));

				} else {
					$published_course_id = null;
				}
			}
			//End of if if(!empty($published_course_id))

			$all_exam_setup_detail = 'exam_name,percent,order,mandatory,edit';
			$this->set(compact('grade_submitted', 'edit', 'exam_types', 'published_course_id', 'all_exam_setup_detail', 'view_only', 'ac_yearsForMoodle', 'enable_for_moodle'));
		}
	}

	public function view($pid)
	{
		$continouseExamSetup = array();
		$total_registered = 0;
		$continouseExamSetup = ClassRegistry::init('ExamType')->getExamType($pid);
		$total_registered = ClassRegistry::init('CourseRegistration')->find('count', array('conditions' => array('CourseRegistration.published_course_id' => $pid)));
		$this->set(compact('continouseExamSetup', 'total_registered'));
	}

	function assessement_template($published_course_id = null)
	{
		if (isset ($published_course_id) && !empty ($published_course_id)) {
			//First I was thinking to limit this task for those who has only department role.
			//But now it is accessible to anyone as long as he/she has department id
			//if($this->role_id == 6) {
			$edit = 0;
			$programs = $this->ExamType->PublishedCourse->Section->Program->find('list');
			$program_types = $this->ExamType->PublishedCourse->Section->ProgramType->find('list');

			if (!empty ($this->request->data)) {

				$department_id = $this->department_id;
				$publishedCourses = $this->ExamType->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection($department_id, $this->request->data['ExamType']['acadamic_year'], $this->request->data['ExamType']['semester'], $this->request->data['ExamType']['program_id'], $this->request->data['ExamType']['program_type_id']);

				if (empty ($publishedCourses)) {
					$this->Flash->info(__('No published courses found with selected search criteria.'));
				} else {
					$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
				}

				$this->set(compact('publishedCourses'));
			} else {
				$publishedCourses = array();
				$published_course_combo_id = $published_course_id;
				$this->set(compact('publishedCourses'));
			}

			if ($published_course_id == null && isset($this->request->data['ExamType']['published_course_id'])) {
				$published_course_id = $this->request->data['ExamType']['published_course_id'];
			}

			if (isset ($this->request->data['listPublishedCourses'])) {
				unset($this->request->data['ExamType']['published_course_id']);
				$published_course_combo_id = "";
			} else {
				// check if grade was not submitted then
				$exam_types = $this->ExamType->find('all', array(
					'conditions' => array('ExamType.published_course_id' => $published_course_id),
					'contain' => array(),
					'fields' => array('id', 'exam_name', 'percent', 'order'),
					'order' => array('order' => 'ASC'),
					'recursive' => -1
				));

				if (empty ($exam_types)) {
					// create default exam types
					$exam_types = array();

					$i = 0;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Attendance";
					$exam_types[$i]['ExamType']['percent'] = 5;
					$exam_types[$i]['ExamType']['order'] = 1;
					$i = 1;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Reading Assignment";
					$exam_types[$i]['ExamType']['percent'] = 5;
					$exam_types[$i]['ExamType']['order'] = 2;

					$i = 2;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Weekly Assignment";
					$exam_types[$i]['ExamType']['percent'] = 15;
					$exam_types[$i]['ExamType']['order'] = 3;

					$i = 3;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Monthly Assignment";
					$exam_types[$i]['ExamType']['percent'] = 15;
					$exam_types[$i]['ExamType']['order'] = 4;
					$i = 3;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Individual Assignment";
					$exam_types[$i]['ExamType']['percent'] = 5;
					$exam_types[$i]['ExamType']['order'] = 5;

					$i = 4;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Team Assignment";
					$exam_types[$i]['ExamType']['percent'] = 5;
					$exam_types[$i]['ExamType']['order'] = 6;

					$i = 5;
					$exam_types[$i]['ExamType']['id'] = $i;
					$exam_types[$i]['ExamType']['exam_name'] = "Final Exam";
					$exam_types[$i]['ExamType']['percent'] = 50;
					$exam_types[$i]['ExamType']['order'] = 4;
					$exam_types[$i]['ExamType']['mandatory'] = 1;
				}


				$student_course_register_and_adds = $this->ExamType->ExamResult->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($published_course_id);
				debug($student_course_register_and_adds);
				$students = $student_course_register_and_adds['register'];
				$student_adds = $student_course_register_and_adds['add'];
				$student_makeup = $student_course_register_and_adds['makeup'];
				$students_export = array_merge($students, $student_adds, $student_makeup);
				debug($students_export);
				$total_student_count = count($students) + count($student_adds) + count($student_makeup);

				$this->autoLayout = false;

				$publishedCourseDetails = $this->ExamType->PublishedCourse->find('first', array(
					'conditions' => array(
						'PublishedCourse.id' => $published_course_id,
					),
					'contain' => array(
						'Course' => array('id', 'course_code'),
						'Section' => array('id', 'name'),
					),
					'fields' => array('id')
				));
				
				$filename = 'Result_Import_Template_'. (str_replace(' ', '_', $publishedCourseDetails['Section']['name'])). '_' . $publishedCourseDetails['Course']['course_code'] . '_'. date('Y-m-d');
				$this->set(compact('students', 'total_student_count', 'student_adds', 'student_makeup', 'students_export', 'filename', 'exam_types'));
				$this->render('/Elements/grade/xls/assessement_template_xls');
				return;
				//$this->__exam_type_mgt($published_course_id, 'department');
			}

			$this->set(compact('programs', 'program_types', 'published_course_id', 'published_course_combo_id', 'edit'));

		} else {
			$this->Flash->error(__('You need to have department or instructor role to access this area. Please contact your system administrator to get either department or instructor role.'));
			return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
	}

	public function enable_course_for_moodle($published_course_id = null)
	{
		if (!empty($published_course_id)) {

			$this->ExamType->PublishedCourse->id = $published_course_id;

			if (!$this->ExamType->PublishedCourse->exists()) {
				throw new NotFoundException(__('Invalid Punlished Course ID'));
				//return $this->redirect(array('action' => ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR ? 'add' : 'exam_type_mgt_for_instructor')));
			}

			$publishedCourse = $this->ExamType->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id
				),
				'contain' => array(
					'CourseInstructorAssignment' => array(
						'Staff' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'type')
							),
							'College'  => array(
								'fields' => array('id', 'name', 'type'),
								'Campus' => array('id', 'name')
							),
							'Position' => array('id', 'position'),
							'User'
						)
					),
					'Course' => array('id', 'course_title', 'course_code'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'Section' => array('id', 'name'),
					'Department' => array(
						'fields' => array('id', 'name', 'type', 'moodle_category_id')
					),
					'College'  => array(
						'fields' => array('id', 'name', 'type', 'moodle_category_id'),
					),
					'CourseRegistration' => array(
						'Student' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'type')
							),
							'College'  => array(
								'fields' => array('id', 'name', 'type'),
								'Campus' => array('id', 'name')
							),
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'User',
						)
					),
					'CourseAdd.registrar_confirmation = 1' => array(
						'Student' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'type')
							),
							'College'  => array(
								'fields' => array('id', 'name', 'type'),
								'Campus' => array('id', 'name')
							),
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'User'
						)
					)
				),
			));

			//debug($publishedCourse);

			if (empty($publishedCourse['CourseRegistration'])) {
				$this->Flash->info('No Student is registered for ' . $publishedCourse['Course']['course_title'] . ' (' . $publishedCourse['Course']['course_code'] . ') course. Please try again when students are registered for the course.');
			} else  if (empty($publishedCourse['CourseInstructorAssignment'])) {
				$this->Flash->info('No Instructor is assigned for ' . $publishedCourse['Course']['course_title'] . ' (' . $publishedCourse['Course']['course_code'] . ') course. Please try again when instructor is assigned for the course.');
			} else {

				$enable_course = ($publishedCourse['PublishedCourse']['enable_for_moodle'] == 0 ? 1 : 0);
				$message = ($enable_course == 1 ? 'enabled for moodle and students registered/added for this course are being synced' : 'the course enrollments for this course on moodle is disabled');

				$this->request->allowMethod('post', 'enable_for_moodle');

				if ($this->ExamType->PublishedCourse->saveField('enable_for_moodle', $enable_course)) {

					if ($enable_course == 1) {

						$total_enrolled_students = 0;
						$enrolled_instructors = 0;
						$enrolled_primary_instructor = 0;
						$enrolled_secondary_instructor = 0;
						$total_registered_students = 0;
						$total_added_students = 0;

						$moodleCourseEnrollmentUpdate = array();
						$moodleCourseUpdate = array();
						$moodleUserUpdate = array();

						$pc_count = 0;
						$count = 0;

						$not_existing_student_user_account_on_smis = 0;

						if (!empty($publishedCourse['Course']) && !empty( $publishedCourse['PublishedCourse'])) {

							$checK_existing_moodle_course = ClassRegistry::init('MoodleCourse')->find('count', array('conditions' => array('MoodleCourse.published_course_id' => $published_course_id)));
							//debug($checK_existing_moodle_course);

							if (!$checK_existing_moodle_course) {
								$moodleCourseUpdate['MoodleCourse'][$pc_count]['published_course_id'] = (!empty($publishedCourse['PublishedCourse']['id']) ? $publishedCourse['PublishedCourse']['id'] : $published_course_id);
								$moodleCourseUpdate['MoodleCourse'][$pc_count]['course_title'] = (trim($publishedCourse['Course']['course_title']) . ' (' . $publishedCourse['PublishedCourse']['academic_year'] . ', ' . $publishedCourse['PublishedCourse']['semester'] . ')');
								$moodleCourseUpdate['MoodleCourse'][$pc_count]['course_code_pid'] = (trim($publishedCourse['Course']['course_code']) . '-' . $publishedCourse['PublishedCourse']['id']);
								$moodleCourseUpdate['MoodleCourse'][$pc_count]['category_id'] = (!empty($publishedCourse['Department']['moodle_category_id']) && $publishedCourse['Department']['moodle_category_id'] != 1 ? $publishedCourse['Department']['moodle_category_id'] : (!empty($publishedCourse['College']['moodle_category_id']) && $publishedCourse['College']['moodle_category_id'] != 1 ? $publishedCourse['College']['moodle_category_id'] : '1'));
								$moodleCourseUpdate['MoodleCourse'][$pc_count]['ac_year'] = (!empty($publishedCourse['PublishedCourse']['academic_year']) ? $publishedCourse['PublishedCourse']['academic_year'] : NULL);
								$moodleCourseUpdate['MoodleCourse'][$pc_count]['semester'] = (!empty($publishedCourse['PublishedCourse']['semester']) ? $publishedCourse['PublishedCourse']['semester'] : NULL);
							}
						}

						debug($moodleCourseUpdate);

						if (isset($moodleCourseUpdate['MoodleCourse']) && !empty($moodleCourseUpdate['MoodleCourse'])) {
							if (ClassRegistry::init('MoodleCourse')->saveAll($moodleCourseUpdate['MoodleCourse'], array('validate' => 'first'))) {
								//$this->Flash->success('Created ' . $publishedCourse['Course']['course_title'] . ' ('. $publishedCourse['Course']['course_code'] .  ') on ' . MOODLE_SITE_URL . '.');
							}
						}

						$otps_to_save = array();

						if (!empty($publishedCourse['CourseRegistration']) && count($publishedCourse['CourseRegistration']) > 0) {
							foreach ($publishedCourse['CourseRegistration'] as $key => $registration) {

								$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
									'conditions' => array(
										'MoodleCourseEnrollment.published_course_id' => $published_course_id,
										'MoodleCourseEnrollment.username' => (isset($registration['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))))),
									)
								));

								if (!isset($registration['Student']['User']['username'])) {
									$not_existing_student_user_account_on_smis++;
									continue;
								}

								//debug($checK_existing_moodle_enrollment);

								if (!$checK_existing_moodle_enrollment) {
								
									$total_enrolled_students++;
									$total_registered_students++;

									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['published_course_id'] = $registration['published_course_id'];
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = (isset($registration['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber'])))));
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'student';
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['academicyear'] = $registration['academic_year'];
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['semester'] = $registration['semester'];

									// MoodleUser Entries to update, If not exists
									$existing_moodle_user = ClassRegistry::init('MoodleUser')->find('count', array(
										'conditions' => array(
											'OR' => array(
												'MoodleUser.username' => (isset($registration['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))))),
												'MoodleUser.idnumber' => (trim($registration['Student']['studentnumber'])),
												'MoodleUser.email' => (isset($registration['Student']['User']['email']) && !empty(trim($registration['Student']['User']['email'])) ? (trim($registration['Student']['User']['email'])) : (!empty(trim($registration['Student']['email'])) ? (trim($registration['Student']['email'])) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX)))
											)
										)
									));

									//debug($existing_moodle_user);

									if (!$existing_moodle_user) {

										if (empty(trim($registration['Student']['User']['username']))) {
											$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))));
											$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($registration['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($registration['Student']['first_name'] . '@' . date('Y'))): $registration['Student']['first_name'] . '@' . date('Y')));
											$moodleUserUpdate['MoodleUser'][$count]['user_id'] = NULL;
										} else {
											$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username']))));
											//$moodleUserUpdate['MoodleUser'][$count]['password'] = $registration['Student']['User']['password'];
											$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($registration['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($registration['Student']['first_name'] . '@' . date('Y'))): $registration['Student']['first_name'] . '@' . date('Y')));
											$moodleUserUpdate['MoodleUser'][$count]['user_id'] = $registration['Student']['User']['id'];
										}
										
										$moodleUserUpdate['MoodleUser'][$count]['firstname'] = (!empty(trim($registration['Student']['first_name'])) ? (ucfirst(strtolower(trim($registration['Student']['first_name'])))) : (ucfirst(strtolower(trim($registration['Student']['User']['first_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['lastname'] = (!empty(trim($registration['Student']['middle_name'])) ? (ucfirst(strtolower(trim($registration['Student']['middle_name'])))) : (ucfirst(strtolower(trim($registration['Student']['User']['middle_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['middlename'] = (!empty(trim($registration['Student']['last_name'])) ? (ucfirst(strtolower(trim($registration['Student']['last_name'])))) : (ucfirst(strtolower(trim($registration['Student']['User']['last_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['email'] = (!empty(trim($registration['Student']['email'])) ? (trim($registration['Student']['email'])) : (!empty(trim($registration['Student']['User']['email'])) ? (trim($registration['Student']['User']['email'])) : (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username']))) . INSTITUTIONAL_EMAIL_SUFFIX)));
										$moodleUserUpdate['MoodleUser'][$count]['institution'] = (!empty(trim($registration['Student']['College']['name'])) ? (trim($registration['Student']['College']['name'])) : (Configure::read('CompanyName')));
										$moodleUserUpdate['MoodleUser'][$count]['department'] = (!empty(trim($registration['Student']['Department']['name'])) ? (trim($registration['Student']['Department']['name'])) : 'N/A');
										$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($registration['Student']['studentnumber'])) ? (trim($registration['Student']['studentnumber'])) : (!empty(trim($registration['Student']['email'])) ? (trim($registration['Student']['email'])) : (!empty(trim($registration['Student']['User']['email'])) ? (trim($registration['Student']['User']['email'])) : ((strtolower(trim($registration['Student']['User']['first_name']))) . '.' . (strtolower(trim($registration['Student']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))));


										$moodleUserUpdate['MoodleUser'][$count]['description'] = (!empty($registration['Student']['academicyear']) ? ($registration['Student']['academicyear'] . ': '. $registration['Student']['Program']['name'] . ' - '. $registration['Student']['ProgramType']['name']) : ($registration['Student']['Program']['name'] . ' - '. $registration['Student']['ProgramType']['name']));
										$moodleUserUpdate['MoodleUser'][$count]['mobile'] = (!empty(trim($registration['Student']['phone_mobile'])) ? (trim($registration['Student']['phone_mobile'])) : NULL);
										$moodleUserUpdate['MoodleUser'][$count]['phone'] =  (!empty(trim($registration['Student']['phone_home']))  ? (trim($registration['Student']['phone_home'])) : NULL);
										$moodleUserUpdate['MoodleUser'][$count]['address'] = (!empty(trim($registration['Student']['College']['name'])) ? (trim($registration['Student']['College']['Campus']['name'])) : (Configure::read('CompanyName')));
										$moodleUserUpdate['MoodleUser'][$count]['role_id'] = $registration['Student']['User']['role_id'];
										$moodleUserUpdate['MoodleUser'][$count]['table_id'] = $registration['Student']['id'];

										if (!empty($otps_to_save) && !in_array($registration['Student']['id'], array_keys($otps_to_save))) {
											$otps_to_save[$registration['Student']['id']] = array(
												'student_id' => $registration['Student']['id'],
												'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
												'password' => ($registration['Student']['first_name'] . '@' . date('Y')),
												'studentnumber' => trim($registration['Student']['studentnumber']),
												'service' => 'Elearning',
												'active' => 1,
												'created' => date('Y-m-d H:i:s'),
												'modified' => date('Y-m-d H:i:s'),
											);
										} else {
											$otps_to_save[$registration['Student']['id']] = array(
												'student_id' => $registration['Student']['id'],
												'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
												'password' => ($registration['Student']['first_name'] . '@' . date('Y')),
												'studentnumber' => trim($registration['Student']['studentnumber']),
												'service' => 'Elearning',
												'active' => 1,
												'created' => date('Y-m-d H:i:s'),
												'modified' => date('Y-m-d H:i:s'),
											);
										}
									}

									// END MoodleUser Entries to update, If not exists
									$count++;
								}
							}
						}

						if (!empty($publishedCourse['CourseAdd']) && count($publishedCourse['CourseAdd']) > 0) {
							foreach ($publishedCourse['CourseAdd'] as $key => $add) {

								$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
									'conditions' => array(
										'MoodleCourseEnrollment.published_course_id' => $published_course_id,
										'MoodleCourseEnrollment.username' => (isset($add['Student']['User']['username']) && !empty(trim($add['Student']['User']['username'])) ? (str_replace('/', '.', strtolower(trim($add['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber']))))),
									)
								));

								if (!isset($add['Student']['User']['username'])) {
									$not_existing_student_user_account_on_smis++;
									continue;
								}


								if (!$checK_existing_moodle_enrollment) {
									$total_enrolled_students++;
									$total_added_students++;

									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['published_course_id'] = $add['published_course_id'];
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = (isset($add['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($add['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber'])))));
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'student';
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['academicyear'] = $add['academic_year'];
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['semester'] = $add['semester'];

									// MoodleUser Entries to update, If not exists
									$existing_moodle_user = ClassRegistry::init('MoodleUser')->find('count', array(
										'conditions' => array(
											'OR' => array(
												'MoodleUser.username' => (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber'])))),
												'MoodleUser.idnumber' => trim($add['Student']['studentnumber']),
												'MoodleUser.email' => (isset($add['Student']['User']['email']) && !empty(trim($add['Student']['User']['email'])) ? (trim($add['Student']['User']['email'])) : (!empty(trim($add['Student']['email'])) ? (trim($add['Student']['email'])) : (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX)))
											)
										)
									));

									//debug($existing_moodle_user);

									if (!$existing_moodle_user) {

										if (empty(trim($add['Student']['User']['username']))) {
											$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber']))));
											$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($add['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($add['Student']['first_name'] . '@' . date('Y'))): $add['Student']['first_name'] . '@' . date('Y')));
											$moodleUserUpdate['MoodleUser'][$count]['user_id'] = NULL;
										} else {
											$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($add['Student']['User']['username']))));
											//$moodleUserUpdate['MoodleUser'][$count]['password'] = $add['Student']['User']['password'];
											$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($add['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($add['Student']['first_name'] . '@' . date('Y'))): $add['Student']['first_name'] . '@' . date('Y')));
											$moodleUserUpdate['MoodleUser'][$count]['user_id'] = $add['Student']['User']['id'];
										}
										
										$moodleUserUpdate['MoodleUser'][$count]['firstname'] = (!empty(trim($add['Student']['first_name'])) ? (ucfirst(strtolower(trim($add['Student']['first_name'])))) : (ucfirst(strtolower(trim($add['Student']['User']['first_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['lastname'] = (!empty(trim($add['Student']['middle_name'])) ? (ucfirst(strtolower(trim($add['Student']['middle_name'])))) : (ucfirst(strtolower(trim($add['Student']['User']['middle_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['middlename'] = (!empty(trim($add['Student']['last_name'])) ? (ucfirst(strtolower(trim($add['Student']['last_name'])))) : (ucfirst(strtolower(trim($add['Student']['User']['last_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['email'] = (!empty(trim($add['Student']['email'])) ? (trim($add['Student']['email'])) : (!empty(trim($add['Student']['User']['email'])) ? (trim($add['Student']['User']['email'])) : (str_replace('/', '.', strtolower(trim($add['Student']['User']['username']))) . INSTITUTIONAL_EMAIL_SUFFIX)));
										$moodleUserUpdate['MoodleUser'][$count]['institution'] = (!empty(trim($add['Student']['College']['name'])) ? (trim($add['Student']['College']['name'])) : (Configure::read('CompanyName')));
										$moodleUserUpdate['MoodleUser'][$count]['department'] = (!empty(trim($add['Student']['Department']['name'])) ? (trim($add['Student']['Department']['name'])) : 'N/A');
										$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($add['Student']['studentnumber'])) ? (trim($add['Student']['studentnumber'])) : (!empty(trim($add['Student']['email'])) ? (trim($add['Student']['email'])) : (!empty(trim($add['Student']['User']['email'])) ? (trim($add['Student']['User']['email'])) : ((strtolower(trim($add['Student']['User']['first_name']))) . '.' . (strtolower(trim($add['Student']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))));


										$moodleUserUpdate['MoodleUser'][$count]['description'] = (!empty($add['Student']['academicyear']) ? ($add['Student']['academicyear'] . ': '. $add['Student']['Program']['name'] . ' - '. $add['Student']['ProgramType']['name']) : ($add['Student']['Program']['name'] . ' - '. $add['Student']['ProgramType']['name']));
										$moodleUserUpdate['MoodleUser'][$count]['mobile'] = (!empty(trim($add['Student']['phone_mobile'])) ? (trim($add['Student']['phone_mobile'])) : NULL);
										$moodleUserUpdate['MoodleUser'][$count]['phone'] =  (!empty(trim($add['Student']['phone_home']))  ? (trim($add['Student']['phone_home'])) : NULL);
										$moodleUserUpdate['MoodleUser'][$count]['address'] = (!empty(trim($add['Student']['College']['name'])) ? (trim($add['Student']['College']['Campus']['name'])) : (Configure::read('CompanyName')));
										$moodleUserUpdate['MoodleUser'][$count]['role_id'] = $add['Student']['User']['role_id'];
										$moodleUserUpdate['MoodleUser'][$count]['table_id'] = $add['Student']['id'];

										if (!empty($otps_to_save) && !in_array($add['Student']['id'], array_keys($otps_to_save))) {
											$otps_to_save[$add['Student']['id']] = array(
												'student_id' => $add['Student']['id'],
												'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
												'password' => ($add['Student']['first_name'] . '@' . date('Y')),
												'studentnumber' => trim($add['Student']['studentnumber']),
												'service' => 'Elearning',
												'active' => 1,
												'created' => date('Y-m-d H:i:s'),
												'modified' => date('Y-m-d H:i:s'),
											);
										} else {
											$otps_to_save[$add['Student']['id']] = array(
												'student_id' => $add['Student']['id'],
												'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
												'password' => ($add['Student']['first_name'] . '@' . date('Y')),
												'studentnumber' => trim($add['Student']['studentnumber']),
												'service' => 'Elearning',
												'active' => 1,
												'created' => date('Y-m-d H:i:s'),
												'modified' => date('Y-m-d H:i:s'),
											);
										}
									}

									// END MoodleUser Entries to update, If not exists

									$count++;
								}
							}
						}

						if (!empty($publishedCourse['CourseInstructorAssignment']) && count($publishedCourse['CourseInstructorAssignment']) > 0) {
							foreach ($publishedCourse['CourseInstructorAssignment'] as $key => $inst_ass) {

								$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
									'conditions' => array(
										'MoodleCourseEnrollment.published_course_id' => $published_course_id,
										//'MoodleCourseEnrollment.username' => (isset($inst_ass['Staff']['User']['username']) ? (str_replace('/', '.', strtolower(trim($inst_ass['Staff']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($inst_ass['Staff']['User']['email']))))),
										'MoodleCourseEnrollment.username' => (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)))
									)
								));

								if (!$checK_existing_moodle_enrollment) {
									$enrolled_instructors++;
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['published_course_id'] = $inst_ass['published_course_id'];
									//$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = $inst_ass['Staff']['User']['username'];
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)));

									if ($inst_ass['isprimary']) {
										$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'editingteacher';
										$enrolled_primary_instructor++;
									} else {
										$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'teacher';
										$enrolled_secondary_instructor++;
									}

									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['academicyear'] = $inst_ass['academic_year'];
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['semester'] = $inst_ass['semester'];
									
									// MoodleUser Entries to update, If not exists
									$existing_moodle_user = ClassRegistry::init('MoodleUser')->find('count', array(
										'conditions' => array(
											'OR' => array(
												'MoodleUser.username' => $inst_ass['Staff']['User']['username'],
												//'MoodleUser.idnumber' => $inst_ass['Staff']['staffid'],
												'MoodleUser.email' => (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))),
											)
										)
									));

									debug($existing_moodle_user);

									if (!$existing_moodle_user) {

										//$moodleUserUpdate['MoodleUser'][$count]['username'] = $inst_ass['Staff']['User']['username'];
										$moodleUserUpdate['MoodleUser'][$count]['username'] = (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)));
										$moodleUserUpdate['MoodleUser'][$count]['firstname'] = (!empty(trim($inst_ass['Staff']['first_name'])) ? (ucfirst(strtolower(trim($inst_ass['Staff']['first_name'])))) : (ucfirst(strtolower(trim($inst_ass['Staff']['User']['first_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['lastname'] = (!empty(trim($inst_ass['Staff']['middle_name'])) ? (ucfirst(strtolower(trim($inst_ass['Staff']['middle_name'])))) : (ucfirst(strtolower(trim($inst_ass['Staff']['User']['middle_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['middlename'] = (!empty(trim($inst_ass['Staff']['last_name'])) ? (ucfirst(strtolower(trim($inst_ass['Staff']['last_name'])))) : (ucfirst(strtolower(trim($inst_ass['Staff']['User']['last_name'])))));
										$moodleUserUpdate['MoodleUser'][$count]['email'] = (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['User']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)));
										$moodleUserUpdate['MoodleUser'][$count]['institution'] = (!empty(trim($inst_ass['Staff']['College']['name'])) ? (trim($inst_ass['Staff']['College']['name'])) : (Configure::read('CompanyName')));
										$moodleUserUpdate['MoodleUser'][$count]['department'] = (!empty(trim($inst_ass['Staff']['Department']['name'])) ? (trim($inst_ass['Staff']['Department']['name'])) : 'N/A');
										//$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($inst_ass['Staff']['staffid'])) ? (trim($inst_ass['Staff']['staffid'])) : (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['User']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))));
										$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($inst_ass['Staff']['staffid'])) ? (trim($inst_ass['Staff']['staffid'])) : NULL);

										$moodleUserUpdate['MoodleUser'][$count]['password'] = $inst_ass['Staff']['User']['password'];
										$moodleUserUpdate['MoodleUser'][$count]['user_id'] = $inst_ass['Staff']['User']['id'];

										$moodleUserUpdate['MoodleUser'][$count]['description'] = (!empty($inst_ass['Staff']['Position']['position']) ? ($inst_ass['Staff']['Position']['position'] . ' at '. (!empty(trim($inst_ass['Staff']['Department']['name'])) ? (trim($inst_ass['Staff']['Department']['name'])) . ' ' . $inst_ass['Staff']['Department']['type'] :  (Configure::read('CompanyName')))) : ('Staff member of ' . (Configure::read('CompanyName'))));
										$moodleUserUpdate['MoodleUser'][$count]['mobile'] = (!empty(trim($inst_ass['Staff']['phone_mobile'])) ? (trim($inst_ass['Staff']['phone_mobile'])) : NULL);
										$moodleUserUpdate['MoodleUser'][$count]['phone'] = (!empty(trim($inst_ass['Staff']['phone_office'])) ? (trim($inst_ass['Staff']['phone_office'])) : NULL);
										$moodleUserUpdate['MoodleUser'][$count]['address'] = (!empty(trim($inst_ass['Staff']['College']['name'])) ? (trim($inst_ass['Staff']['College']['Campus']['name'])) : (Configure::read('CompanyName')));
										$moodleUserUpdate['MoodleUser'][$count]['role_id'] = $inst_ass['Staff']['User']['role_id'];
										$moodleUserUpdate['MoodleUser'][$count]['table_id'] = $inst_ass['Staff']['id'];
									}

									// END MoodleUser Entries to update, If not exists

									$count++;
								}
							}
						}

						//debug($moodleUserUpdate);

						//$moodleUserUpdate['MoodleUser'] = array_unique(array_values($moodleUserUpdate['MoodleUser']));

						//debug($otps_to_save);
						
						if (!empty($moodleUserUpdate['MoodleUser'])) {
							if (ClassRegistry::init('MoodleUser')->saveAll($moodleUserUpdate['MoodleUser'], array('validate' => 'first'))) {
								if (!empty($otps_to_save)) {
									ClassRegistry::init('Otp')->saveAll($otps_to_save, array('validate' => 'first'));
								}
								//$this->Flash->success('Created ' . (count($moodleUserUpdate['MoodleUser'])) . ' new accounts.');
							}
						}

						//debug($moodleCourseEnrollmentUpdate);

						if (!empty($moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'])) {
							if (ClassRegistry::init('MoodleCourseEnrollment')->saveAll($moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'], array('validate' => 'first'))) {
								$this->Flash->success('Created ' . $publishedCourse['Course']['course_title'] . ' ('. $publishedCourse['Course']['course_code'] .  ') on ' . MOODLE_SITE_URL . ' and enrolled ' . $total_enrolled_students . ' students ' . ($total_added_students > 0 ? ' including' . $total_added_students . ' added ' : '') . ' and ' . ($enrolled_instructors > 1 ? $enrolled_instructors . ' instructors' :  $enrolled_instructors . ' instructor')  . '(' . ($enrolled_primary_instructor > 0 && $enrolled_secondary_instructor > 0 ? $enrolled_primary_instructor . ' Primary and ' .  $enrolled_secondary_instructor . ' Secondary' : ($enrolled_primary_instructor > 0 ? $enrolled_primary_instructor . ' Primary' : ($enrolled_secondary_instructor > 0 ? $enrolled_secondary_instructor . ' Secondary' : ''))). ').'. ($not_existing_student_user_account_on_smis > 0 ? ' Info: ' . $not_existing_student_user_account_on_smis. ' students User Account is not created, Advise the deppartmet head to issue the studets a username and password.' : ''));
							}
						}

					} else {

						// delete existing course enrollments if any.
						$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array('conditions' => array('MoodleCourseEnrollment.published_course_id' => $published_course_id)));

						if ($checK_existing_moodle_enrollment) {
							if (ClassRegistry::init('MoodleCourseEnrollment')->deleteAll(array('MoodleCourseEnrollment.published_course_id' => $published_course_id), false)) {
								$existing_moodle_course = ClassRegistry::init('MoodleCourse')->find('count', array('conditions' => array('MoodleCourse.published_course_id' => $published_course_id)));
								if ($existing_moodle_course) {
									if (ClassRegistry::init('MoodleCourse')->deleteAll(array('MoodleCourse.published_course_id' => $published_course_id), false)) {
										$this->Flash->success('Deleted ' . $publishedCourse['Course']['course_title'] . ' (' . $publishedCourse['Course']['course_code'] . ')  course from ' . MOODLE_SITE_URL . ' and unenrolled all ' . ($checK_existing_moodle_enrollment) . ' users .');
									}
								}
							}
						} else {
							$existing_moodle_course = ClassRegistry::init('MoodleCourse')->find('count', array('conditions' => array('MoodleCourse.published_course_id' => $published_course_id)));
							if ($existing_moodle_course) {
								if (ClassRegistry::init('MoodleCourse')->deleteAll(array('MoodleCourse.published_course_id' => $published_course_id), false)) {
									$this->Flash->success('Deleted ' . $publishedCourse['Course']['course_title'] . ' (' . $publishedCourse['Course']['course_code'] . ')  course from ' . MOODLE_SITE_URL . ' and unenrolled all ' . ($checK_existing_moodle_enrollment) . ' users .');
								}
							} else {
								$this->Flash->success('Disabled ' . $publishedCourse['Course']['course_title'] . ' (' . $publishedCourse['Course']['course_code'] . ')  course from ' . MOODLE_SITE_URL . '.');
							}
						}
					}
				} else {
					$this->Flash->error('The course could not be ' . $message . ' right now, please try again later.');
				}
			}

			return $this->redirect(array('action' => ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR ? 'add' : 'exam_type_mgt_for_instructor'), /* $published_course_id */));
		}
	}

	public function sync_new_enrollments($published_course_id = null)
	{
		if (!empty($published_course_id)) {

			$this->ExamType->PublishedCourse->id = $published_course_id;

			if (!$this->ExamType->PublishedCourse->exists()) {
				throw new NotFoundException(__('Invalid Punlished Course ID'));
				//return $this->redirect(array('action' => ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR ? 'add' : 'exam_type_mgt_for_instructor')));
			}

			$existing_students_moodle_enrollments = ClassRegistry::init('MoodleCourseEnrollment')->find('list', array(
				'conditions' => array(
					'MoodleCourseEnrollment.published_course_id' => $published_course_id,
					'MoodleCourseEnrollment.user_role' => 'student',
				),
				'fields' => array('MoodleCourseEnrollment.username', 'MoodleCourseEnrollment.username')
			));

			$existing_staff_moodle_enrollments = ClassRegistry::init('MoodleCourseEnrollment')->find('list', array(
				'conditions' => array(
					'MoodleCourseEnrollment.published_course_id' => $published_course_id,
					'MoodleCourseEnrollment.user_role <>' => 'student',
				),
				'fields' => array('MoodleCourseEnrollment.username', 'MoodleCourseEnrollment.username')
			));


			$existing_moodle_course_enrollment_student_ids_to_exclude = ClassRegistry::init('MoodleUser')->find('list', array(
				'conditions' => array(
					'MoodleUser.role_id' => ROLE_STUDENT,
					'MoodleUser.username' => $existing_students_moodle_enrollments
				),
				'fields' => array('MoodleUser.table_id', 'MoodleUser.table_id')
			));


			$existing_moodle_course_enrollment_staff_ids_to_exclude = ClassRegistry::init('MoodleUser')->find('list', array(
				'conditions' => array(
					'MoodleUser.role_id' => ROLE_INSTRUCTOR,
					'MoodleUser.username' => $existing_staff_moodle_enrollments
				),
				'fields' => array('MoodleUser.table_id', 'MoodleUser.table_id')
			));

			debug($existing_moodle_course_enrollment_staff_ids_to_exclude);
			debug($existing_moodle_course_enrollment_student_ids_to_exclude);
			

			$publishedCourse = $this->ExamType->PublishedCourse->find('first', array(
				'conditions' => array(
					'PublishedCourse.id' => $published_course_id
				),
				'contain' => array(
					'CourseInstructorAssignment' => array(
						'conditions' => array(
							'CourseInstructorAssignment.staff_id NOT IN (' . join(',', $existing_moodle_course_enrollment_staff_ids_to_exclude) . ')'
						),
						'Staff' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'type')
							),
							'College'  => array(
								'fields' => array('id', 'name', 'type'),
								'Campus' => array('id', 'name')
							),
							'Position' => array('id', 'position'),
							'User'
						)
					),
					'Course' => array('id', 'course_title', 'course_code'),
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'Section' => array('id', 'name'),
					'Department' => array(
						'fields' => array('id', 'name', 'type', 'moodle_category_id')
					),
					'College'  => array(
						'fields' => array('id', 'name', 'type', 'moodle_category_id'),
					),
					'CourseRegistration' => array(
						'conditions' => array(
							'CourseRegistration.student_id NOT IN (' . join(',', $existing_moodle_course_enrollment_student_ids_to_exclude) . ')'
						),
						'Student' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'type')
							),
							'College'  => array(
								'fields' => array('id', 'name', 'type'),
								'Campus' => array('id', 'name')
							),
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'User',
						)
					),
					'CourseAdd' => array(
						'conditions' => array(
							'CourseAdd.registrar_confirmation' => 1,
							'CourseAdd.student_id NOT IN (' . join(',', $existing_moodle_course_enrollment_student_ids_to_exclude) . ')'
						),
						'Student' => array(
							'Department' => array(
								'fields' => array('id', 'name', 'type')
							),
							'College'  => array(
								'fields' => array('id', 'name', 'type'),
								'Campus' => array('id', 'name')
							),
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'User'
						)
					)
				),
			));

			debug($publishedCourse);

			if (empty($publishedCourse['CourseRegistration']) && empty($publishedCourse['CourseInstructorAssignment']) && empty($publishedCourse['CourseAdd'])) {
				$this->Flash->info('No new uses to sync.. You can try again later if you think there are students left to sync when they are registred or add your course.');
			} else {

				if ($publishedCourse['PublishedCourse']['enable_for_moodle']) {

					$total_enrolled_students = 0;
					$enrolled_instructors = 0;
					$enrolled_primary_instructor = 0;
					$enrolled_secondary_instructor = 0;
					$total_registered_students = 0;
					$total_added_students = 0;

					$moodleCourseEnrollmentUpdate = array();
					$moodleUserUpdate = array();

					$pc_count = 0;
					$count = 0;

					$not_existing_student_user_account_on_smis = 0;

					$otps_to_save = array();

					if (!empty($publishedCourse['CourseRegistration']) && count($publishedCourse['CourseRegistration']) > 0) {
						foreach ($publishedCourse['CourseRegistration'] as $key => $registration) {

							$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
								'conditions' => array(
									'MoodleCourseEnrollment.published_course_id' => $published_course_id,
									'MoodleCourseEnrollment.username' => (isset($registration['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))))),
								)
							));

							if (!isset($registration['Student']['User']['username'])) {
								$not_existing_student_user_account_on_smis++;
								continue;
							}

							//debug($checK_existing_moodle_enrollment);

							if (!$checK_existing_moodle_enrollment) {
							
								$total_enrolled_students++;
								$total_registered_students++;

								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['published_course_id'] = $registration['published_course_id'];
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = (isset($registration['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber'])))));
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'student';
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['academicyear'] = $registration['academic_year'];
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['semester'] = $registration['semester'];

								// MoodleUser Entries to update, If not exists
								$existing_moodle_user = ClassRegistry::init('MoodleUser')->find('count', array(
									'conditions' => array(
										'OR' => array(
											'MoodleUser.username' => (isset($registration['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))))),
											'MoodleUser.idnumber' => (trim($registration['Student']['studentnumber'])),
											'MoodleUser.email' => (isset($registration['Student']['User']['email']) && !empty(trim($registration['Student']['User']['email'])) ? (trim($registration['Student']['User']['email'])) : (!empty(trim($registration['Student']['email'])) ? (trim($registration['Student']['email'])) : (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX)))
										)
									)
								));

								//debug($existing_moodle_user);

								if (!$existing_moodle_user) {

									if (empty(trim($registration['Student']['User']['username']))) {
										$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($registration['Student']['studentnumber']))));
										$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($registration['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($registration['Student']['first_name'] . '@' . date('Y'))): $registration['Student']['first_name'] . '@' . date('Y')));
										$moodleUserUpdate['MoodleUser'][$count]['user_id'] = NULL;
									} else {
										$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username']))));
										//$moodleUserUpdate['MoodleUser'][$count]['password'] = $registration['Student']['User']['password'];
										$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($registration['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($registration['Student']['first_name'] . '@' . date('Y'))): $registration['Student']['first_name'] . '@' . date('Y')));
										$moodleUserUpdate['MoodleUser'][$count]['user_id'] = $registration['Student']['User']['id'];
									}
									
									$moodleUserUpdate['MoodleUser'][$count]['firstname'] = (!empty(trim($registration['Student']['first_name'])) ? (ucfirst(strtolower(trim($registration['Student']['first_name'])))) : (ucfirst(strtolower(trim($registration['Student']['User']['first_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['lastname'] = (!empty(trim($registration['Student']['middle_name'])) ? (ucfirst(strtolower(trim($registration['Student']['middle_name'])))) : (ucfirst(strtolower(trim($registration['Student']['User']['middle_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['middlename'] = (!empty(trim($registration['Student']['last_name'])) ? (ucfirst(strtolower(trim($registration['Student']['last_name'])))) : (ucfirst(strtolower(trim($registration['Student']['User']['last_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['email'] = (!empty(trim($registration['Student']['email'])) ? (trim($registration['Student']['email'])) : (!empty(trim($registration['Student']['User']['email'])) ? (trim($registration['Student']['User']['email'])) : (str_replace('/', '.', strtolower(trim($registration['Student']['User']['username']))) . INSTITUTIONAL_EMAIL_SUFFIX)));
									$moodleUserUpdate['MoodleUser'][$count]['institution'] = (!empty(trim($registration['Student']['College']['name'])) ? (trim($registration['Student']['College']['name'])) : (Configure::read('CompanyName')));
									$moodleUserUpdate['MoodleUser'][$count]['department'] = (!empty(trim($registration['Student']['Department']['name'])) ? (trim($registration['Student']['Department']['name'])) : 'N/A');
									$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($registration['Student']['studentnumber'])) ? (trim($registration['Student']['studentnumber'])) : (!empty(trim($registration['Student']['email'])) ? (trim($registration['Student']['email'])) : (!empty(trim($registration['Student']['User']['email'])) ? (trim($registration['Student']['User']['email'])) : ((strtolower(trim($registration['Student']['User']['first_name']))) . '.' . (strtolower(trim($registration['Student']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))));


									$moodleUserUpdate['MoodleUser'][$count]['description'] = (!empty($registration['Student']['academicyear']) ? ($registration['Student']['academicyear'] . ': '. $registration['Student']['Program']['name'] . ' - '. $registration['Student']['ProgramType']['name']) : ($registration['Student']['Program']['name'] . ' - '. $registration['Student']['ProgramType']['name']));
									$moodleUserUpdate['MoodleUser'][$count]['mobile'] = (!empty(trim($registration['Student']['phone_mobile'])) ? (trim($registration['Student']['phone_mobile'])) : NULL);
									$moodleUserUpdate['MoodleUser'][$count]['phone'] =  (!empty(trim($registration['Student']['phone_home']))  ? (trim($registration['Student']['phone_home'])) : NULL);
									$moodleUserUpdate['MoodleUser'][$count]['address'] = (!empty(trim($registration['Student']['College']['name'])) ? (trim($registration['Student']['College']['Campus']['name'])) : (Configure::read('CompanyName')));
									$moodleUserUpdate['MoodleUser'][$count]['role_id'] = $registration['Student']['User']['role_id'];
									$moodleUserUpdate['MoodleUser'][$count]['table_id'] = $registration['Student']['id'];

									if (!empty($otps_to_save) && !in_array($registration['Student']['id'], array_keys($otps_to_save))) {
										$otps_to_save[$registration['Student']['id']] = array(
											'student_id' => $registration['Student']['id'],
											'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
											'password' => ($registration['Student']['first_name'] . '@' . date('Y')),
											'studentnumber' => trim($registration['Student']['studentnumber']),
											'service' => 'Elearning',
											'active' => 1,
											'created' => date('Y-m-d H:i:s'),
											'modified' => date('Y-m-d H:i:s'),
										);
									} else {
										$otps_to_save[$registration['Student']['id']] = array(
											'student_id' => $registration['Student']['id'],
											'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
											'password' => ($registration['Student']['first_name'] . '@' . date('Y')),
											'studentnumber' => trim($registration['Student']['studentnumber']),
											'service' => 'Elearning',
											'active' => 1,
											'created' => date('Y-m-d H:i:s'),
											'modified' => date('Y-m-d H:i:s'),
										);
									}
								}

								// END MoodleUser Entries to update, If not exists
								$count++;
							}
						}
					}

					if (!empty($publishedCourse['CourseAdd']) && count($publishedCourse['CourseAdd']) > 0) {
						foreach ($publishedCourse['CourseAdd'] as $key => $add) {

							$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
								'conditions' => array(
									'MoodleCourseEnrollment.published_course_id' => $published_course_id,
									'MoodleCourseEnrollment.username' => (isset($add['Student']['User']['username']) && !empty(trim($add['Student']['User']['username'])) ? (str_replace('/', '.', strtolower(trim($add['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber']))))),
								)
							));

							if (!isset($add['Student']['User']['username'])) {
								$not_existing_student_user_account_on_smis++;
								continue;
							}


							if (!$checK_existing_moodle_enrollment) {
								$total_enrolled_students++;
								$total_added_students++;

								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['published_course_id'] = $add['published_course_id'];
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = (isset($add['Student']['User']['username']) ? (str_replace('/', '.', strtolower(trim($add['Student']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber'])))));
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'student';
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['academicyear'] = $add['academic_year'];
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['semester'] = $add['semester'];

								// MoodleUser Entries to update, If not exists
								$existing_moodle_user = ClassRegistry::init('MoodleUser')->find('count', array(
									'conditions' => array(
										'OR' => array(
											'MoodleUser.username' => (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber'])))),
											'MoodleUser.idnumber' => trim($add['Student']['studentnumber']),
											'MoodleUser.email' => (isset($add['Student']['User']['email']) && !empty(trim($add['Student']['User']['email'])) ? (trim($add['Student']['User']['email'])) : (!empty(trim($add['Student']['email'])) ? (trim($add['Student']['email'])) : (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX)))
										)
									)
								));

								//debug($existing_moodle_user);

								if (!$existing_moodle_user) {

									if (empty(trim($add['Student']['User']['username']))) {
										$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($add['Student']['studentnumber']))));
										$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($add['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($add['Student']['first_name'] . '@' . date('Y'))): $add['Student']['first_name'] . '@' . date('Y')));
										$moodleUserUpdate['MoodleUser'][$count]['user_id'] = NULL;
									} else {
										$moodleUserUpdate['MoodleUser'][$count]['username'] = (str_replace('/', '.', strtolower(trim($add['Student']['User']['username']))));
										//$moodleUserUpdate['MoodleUser'][$count]['password'] = $add['Student']['User']['password'];
										$moodleUserUpdate['MoodleUser'][$count]['password'] = (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1(($add['Student']['first_name'] . '@' . date('Y'))) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5(($add['Student']['first_name'] . '@' . date('Y'))): $add['Student']['first_name'] . '@' . date('Y')));
										$moodleUserUpdate['MoodleUser'][$count]['user_id'] = $add['Student']['User']['id'];
									}
									
									$moodleUserUpdate['MoodleUser'][$count]['firstname'] = (!empty(trim($add['Student']['first_name'])) ? (ucfirst(strtolower(trim($add['Student']['first_name'])))) : (ucfirst(strtolower(trim($add['Student']['User']['first_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['lastname'] = (!empty(trim($add['Student']['middle_name'])) ? (ucfirst(strtolower(trim($add['Student']['middle_name'])))) : (ucfirst(strtolower(trim($add['Student']['User']['middle_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['middlename'] = (!empty(trim($add['Student']['last_name'])) ? (ucfirst(strtolower(trim($add['Student']['last_name'])))) : (ucfirst(strtolower(trim($add['Student']['User']['last_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['email'] = (!empty(trim($add['Student']['email'])) ? (trim($add['Student']['email'])) : (!empty(trim($add['Student']['User']['email'])) ? (trim($add['Student']['User']['email'])) : (str_replace('/', '.', strtolower(trim($add['Student']['User']['username']))) . INSTITUTIONAL_EMAIL_SUFFIX)));
									$moodleUserUpdate['MoodleUser'][$count]['institution'] = (!empty(trim($add['Student']['College']['name'])) ? (trim($add['Student']['College']['name'])) : (Configure::read('CompanyName')));
									$moodleUserUpdate['MoodleUser'][$count]['department'] = (!empty(trim($add['Student']['Department']['name'])) ? (trim($add['Student']['Department']['name'])) : 'N/A');
									$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($add['Student']['studentnumber'])) ? (trim($add['Student']['studentnumber'])) : (!empty(trim($add['Student']['email'])) ? (trim($add['Student']['email'])) : (!empty(trim($add['Student']['User']['email'])) ? (trim($add['Student']['User']['email'])) : ((strtolower(trim($add['Student']['User']['first_name']))) . '.' . (strtolower(trim($add['Student']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))));


									$moodleUserUpdate['MoodleUser'][$count]['description'] = (!empty($add['Student']['academicyear']) ? ($add['Student']['academicyear'] . ': '. $add['Student']['Program']['name'] . ' - '. $add['Student']['ProgramType']['name']) : ($add['Student']['Program']['name'] . ' - '. $add['Student']['ProgramType']['name']));
									$moodleUserUpdate['MoodleUser'][$count]['mobile'] = (!empty(trim($add['Student']['phone_mobile'])) ? (trim($add['Student']['phone_mobile'])) : NULL);
									$moodleUserUpdate['MoodleUser'][$count]['phone'] =  (!empty(trim($add['Student']['phone_home']))  ? (trim($add['Student']['phone_home'])) : NULL);
									$moodleUserUpdate['MoodleUser'][$count]['address'] = (!empty(trim($add['Student']['College']['name'])) ? (trim($add['Student']['College']['Campus']['name'])) : (Configure::read('CompanyName')));
									$moodleUserUpdate['MoodleUser'][$count]['role_id'] = $add['Student']['User']['role_id'];
									$moodleUserUpdate['MoodleUser'][$count]['table_id'] = $add['Student']['id'];

									if (!empty($otps_to_save) && !in_array($add['Student']['id'], array_keys($otps_to_save))) {
										$otps_to_save[$add['Student']['id']] = array(
											'student_id' => $add['Student']['id'],
											'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
											'password' => ($add['Student']['first_name'] . '@' . date('Y')),
											'studentnumber' => trim($add['Student']['studentnumber']),
											'service' => 'Elearning',
											'active' => 1,
											'created' => date('Y-m-d H:i:s'),
											'modified' => date('Y-m-d H:i:s'),
										);
									} else {
										$otps_to_save[$add['Student']['id']] = array(
											'student_id' => $add['Student']['id'],
											'username' => $moodleUserUpdate['MoodleUser'][$count]['username'],
											'password' => ($add['Student']['first_name'] . '@' . date('Y')),
											'studentnumber' => trim($add['Student']['studentnumber']),
											'service' => 'Elearning',
											'active' => 1,
											'created' => date('Y-m-d H:i:s'),
											'modified' => date('Y-m-d H:i:s'),
										);
									}
								}

								// END MoodleUser Entries to update, If not exists

								$count++;
							}
						}
					}

					if (!empty($publishedCourse['CourseInstructorAssignment']) && count($publishedCourse['CourseInstructorAssignment']) > 0) {
						foreach ($publishedCourse['CourseInstructorAssignment'] as $key => $inst_ass) {

							$checK_existing_moodle_enrollment = ClassRegistry::init('MoodleCourseEnrollment')->find('count', array(
								'conditions' => array(
									'MoodleCourseEnrollment.published_course_id' => $published_course_id,
									//'MoodleCourseEnrollment.username' => (isset($inst_ass['Staff']['User']['username']) ? (str_replace('/', '.', strtolower(trim($inst_ass['Staff']['User']['username'])))) : (str_replace('/', '.', strtolower(trim($inst_ass['Staff']['User']['email']))))),
									'MoodleCourseEnrollment.username' => (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)))
								)
							));

							if (!$checK_existing_moodle_enrollment) {
								$enrolled_instructors++;
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['published_course_id'] = $inst_ass['published_course_id'];
								//$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = $inst_ass['Staff']['User']['username'];
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['username'] = (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)));

								if ($inst_ass['isprimary']) {
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'editingteacher';
									$enrolled_primary_instructor++;
								} else {
									$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['user_role'] = 'teacher';
									$enrolled_secondary_instructor++;
								}

								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['academicyear'] = $inst_ass['academic_year'];
								$moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'][$count]['semester'] = $inst_ass['semester'];
								
								// MoodleUser Entries to update, If not exists
								$existing_moodle_user = ClassRegistry::init('MoodleUser')->find('count', array(
									'conditions' => array(
										'OR' => array(
											'MoodleUser.username' => $inst_ass['Staff']['User']['username'],
											//'MoodleUser.idnumber' => $inst_ass['Staff']['staffid'],
											'MoodleUser.email' => (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))),
										)
									)
								));

								debug($existing_moodle_user);

								if (!$existing_moodle_user) {

									//$moodleUserUpdate['MoodleUser'][$count]['username'] = $inst_ass['Staff']['User']['username'];
									$moodleUserUpdate['MoodleUser'][$count]['username'] = (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)));
									$moodleUserUpdate['MoodleUser'][$count]['firstname'] = (!empty(trim($inst_ass['Staff']['first_name'])) ? (ucfirst(strtolower(trim($inst_ass['Staff']['first_name'])))) : (ucfirst(strtolower(trim($inst_ass['Staff']['User']['first_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['lastname'] = (!empty(trim($inst_ass['Staff']['middle_name'])) ? (ucfirst(strtolower(trim($inst_ass['Staff']['middle_name'])))) : (ucfirst(strtolower(trim($inst_ass['Staff']['User']['middle_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['middlename'] = (!empty(trim($inst_ass['Staff']['last_name'])) ? (ucfirst(strtolower(trim($inst_ass['Staff']['last_name'])))) : (ucfirst(strtolower(trim($inst_ass['Staff']['User']['last_name'])))));
									$moodleUserUpdate['MoodleUser'][$count]['email'] = (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['User']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX)));
									$moodleUserUpdate['MoodleUser'][$count]['institution'] = (!empty(trim($inst_ass['Staff']['College']['name'])) ? (trim($inst_ass['Staff']['College']['name'])) : (Configure::read('CompanyName')));
									$moodleUserUpdate['MoodleUser'][$count]['department'] = (!empty(trim($inst_ass['Staff']['Department']['name'])) ? (trim($inst_ass['Staff']['Department']['name'])) : 'N/A');
									//$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($inst_ass['Staff']['staffid'])) ? (trim($inst_ass['Staff']['staffid'])) : (!empty(trim($inst_ass['Staff']['email'])) ? (trim($inst_ass['Staff']['email'])) : (!empty(trim($inst_ass['Staff']['User']['email'])) ? (trim($inst_ass['Staff']['User']['email'])) : ((strtolower(trim($inst_ass['Staff']['User']['first_name']))) . '.' . (strtolower(trim($inst_ass['Staff']['User']['middle_name']))). INSTITUTIONAL_EMAIL_SUFFIX))));
									$moodleUserUpdate['MoodleUser'][$count]['idnumber'] = (!empty(trim($inst_ass['Staff']['staffid'])) ? (trim($inst_ass['Staff']['staffid'])) : NULL);

									$moodleUserUpdate['MoodleUser'][$count]['password'] = $inst_ass['Staff']['User']['password'];
									$moodleUserUpdate['MoodleUser'][$count]['user_id'] = $inst_ass['Staff']['User']['id'];

									$moodleUserUpdate['MoodleUser'][$count]['description'] = (!empty($inst_ass['Staff']['Position']['position']) ? ($inst_ass['Staff']['Position']['position'] . ' at '. (!empty(trim($inst_ass['Staff']['Department']['name'])) ? (trim($inst_ass['Staff']['Department']['name'])) . ' ' . $inst_ass['Staff']['Department']['type'] :  (Configure::read('CompanyName')))) : ('Staff member of ' . (Configure::read('CompanyName'))));
									$moodleUserUpdate['MoodleUser'][$count]['mobile'] = (!empty(trim($inst_ass['Staff']['phone_mobile'])) ? (trim($inst_ass['Staff']['phone_mobile'])) : NULL);
									$moodleUserUpdate['MoodleUser'][$count]['phone'] = (!empty(trim($inst_ass['Staff']['phone_office'])) ? (trim($inst_ass['Staff']['phone_office'])) : NULL);
									$moodleUserUpdate['MoodleUser'][$count]['address'] = (!empty(trim($inst_ass['Staff']['College']['name'])) ? (trim($inst_ass['Staff']['College']['Campus']['name'])) : (Configure::read('CompanyName')));
									$moodleUserUpdate['MoodleUser'][$count]['role_id'] = $inst_ass['Staff']['User']['role_id'];
									$moodleUserUpdate['MoodleUser'][$count]['table_id'] = $inst_ass['Staff']['id'];
								}

								// END MoodleUser Entries to update, If not exists

								$count++;
							}
						}
					}

					//debug($moodleUserUpdate);

					$message_to_display = '';
					
					if (!empty($moodleUserUpdate['MoodleUser'])) {
						if (ClassRegistry::init('MoodleUser')->saveAll($moodleUserUpdate['MoodleUser'], array('validate' => 'first'))) {
							//$this->Flash->success('Created ' . (count($moodleUserUpdate['MoodleUser'])) . ' new accounts.');
							$message_to_display .= 'Created ' . (count($moodleUserUpdate['MoodleUser'])) . ' new accounts,';
							if (!empty($otps_to_save)) {
								ClassRegistry::init('Otp')->saveAll($otps_to_save, array('validate' => 'first'));
							}
						} else {
						 	$message_to_display .= 'Failed to create ' . (count($moodleUserUpdate['MoodleUser'])) . ' new accounts,';
						}
					}

					//debug($moodleCourseEnrollmentUpdate);

					if (!empty($moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'])) {
						if (ClassRegistry::init('MoodleCourseEnrollment')->saveAll($moodleCourseEnrollmentUpdate['MoodleCourseEnrollment'], array('validate' => 'first'))) {
							//$this->Flash->success('Created ' . $publishedCourse['Course']['course_title'] . ' ('. $publishedCourse['Course']['course_code'] .  ') on ' . MOODLE_SITE_URL . ' and enrolled ' . $total_enrolled_students . ' students ' . ($total_added_students > 0 ? ' including' . $total_added_students . ' added ' : '') . ' and ' . ($enrolled_instructors > 1 ? $enrolled_instructors . ' instructors' :  $enrolled_instructors . ' instructor')  . '(' . ($enrolled_primary_instructor > 0 && $enrolled_secondary_instructor > 0 ? $enrolled_primary_instructor . ' Primary and ' .  $enrolled_secondary_instructor . ' Secondary' : ($enrolled_primary_instructor > 0 ? $enrolled_primary_instructor . ' Primary' : ($enrolled_secondary_instructor > 0 ? $enrolled_secondary_instructor . ' Secondary' : ''))). ').'. ($not_existing_student_user_account_on_smis > 0 ? ' Info: ' . $not_existing_student_user_account_on_smis. ' students User Account is not created, Advise the deppartmet head to issue the studets a username and password.' : ''));
							$message_to_display .= ' Enrolled ' . $total_enrolled_students . ' new students ' . ($total_added_students > 0 ? ' including' . $total_added_students . ' added ' : '') . ($enrolled_instructors ? ' and ' . ($enrolled_instructors > 1 ? $enrolled_instructors . ' instructors' :  $enrolled_instructors . ' instructor')  . '(' . ($enrolled_primary_instructor > 0 && $enrolled_secondary_instructor > 0 ? $enrolled_primary_instructor . ' Primary and ' .  $enrolled_secondary_instructor . ' Secondary' : ($enrolled_primary_instructor > 0 ? $enrolled_primary_instructor . ' Primary' : ($enrolled_secondary_instructor > 0 ? $enrolled_secondary_instructor . ' Secondary' : ''))) . ').' : ''). ($not_existing_student_user_account_on_smis > 0 ? ' Info: ' . $not_existing_student_user_account_on_smis. ' students User Account is not created, Advise the deppartmet head to issue the studets a username and password.' : '');
						} else {
						 	$message_to_display .= ' Failed to Enroll ' . $total_enrolled_students . ' new students ' . ($total_added_students > 0 ? ' including' . $total_added_students . ' added ' : '') . ($enrolled_instructors ? ' and ' . ($enrolled_instructors > 1 ? $enrolled_instructors . ' instructors' :  $enrolled_instructors . ' instructor')  . '(' . ($enrolled_primary_instructor > 0 && $enrolled_secondary_instructor > 0 ? $enrolled_primary_instructor . ' Primary and ' .  $enrolled_secondary_instructor . ' Secondary' : ($enrolled_primary_instructor > 0 ? $enrolled_primary_instructor . ' Primary' : ($enrolled_secondary_instructor > 0 ? $enrolled_secondary_instructor . ' Secondary' : ''))) . ').' : ''). ($not_existing_student_user_account_on_smis > 0 ? ' Info: ' . $not_existing_student_user_account_on_smis. ' students User Account is not created, Advise the deppartmet head to issue the studets a username and password.' : '');
						}
					} else {
						$message_to_display = 'No new course registration, course add or instructor assignment is found to sync.';
					}

					$this->Flash->info($message_to_display);

				}
			}

			return $this->redirect(array('action' => ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR ? 'add' : 'exam_type_mgt_for_instructor'), /* $published_course_id */));
		}
	}

}
