<?php
class StudentStatusPatternsController extends AppController
{

	var $name = 'StudentStatusPatterns';
	var $menuOptions = array(
		'parent' => 'grades',
		'alias' => array(
			'index' => 'View all Status Pattern',
			'add' => 'Add New Status Pattern',
			'regenerate_status' => 'Regenerate Status By Course',
			'regenerate_academic_status' => 'Batch Regenerate Status',
			'regenerate_individual_academic_status' => 'Regenerate Student\'s Status'
		)
	);

	var $components = array('AcademicYear');

	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	function beforeRender()
	{
		//$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 8, date('Y'));
		//$acyear_array_data = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $this->AcademicYear->current_academicyear())[0]));

		$current_academicyear = $defaultacademicyear = $this->AcademicYear->current_academicyear(); 

		$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $current_academicyear)[0]) - 8), (explode('/', $current_academicyear)[0]));
		
		debug($acyear_array_data);

		//$this->set('defaultacademicyear', $defaultacademicyear);
		//$this->set(compact('acyear_array_data', 'defaultacademicyear'));

		
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

		$this->set(compact('acyear_array_data', 'defaultacademicyear', 'program_types', 'programTypes', 'programs', 'yearLevels'));
	}


	function index()
	{
		$this->StudentStatusPattern->recursive = 0;
		$this->set('studentStatusPatterns', $this->paginate());
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid student status pattern');
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('studentStatusPattern', $this->StudentStatusPattern->read(null, $id));
	}

	function add()
	{
		if (!empty($this->request->data)) {
			$this->StudentStatusPattern->create();
			if ($this->StudentStatusPattern->save($this->request->data)) {
				$this->Flash->success('The student status pattern has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The student status pattern could not be saved. Please, try again.');
			}
		}

		$programs = $this->StudentStatusPattern->Program->find('list');
		$programTypes = $this->StudentStatusPattern->ProgramType->find('list');

		$this->set(compact('programs', 'programTypes'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid student status pattern');
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->StudentStatusPattern->save($this->request->data)) {
				$this->Flash->success('The student status pattern has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The student status pattern could not be saved. Please, try again.');
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->StudentStatusPattern->read(null, $id);
		}

		$programs = $this->StudentStatusPattern->Program->find('list');
		$programTypes = $this->StudentStatusPattern->ProgramType->find('list');

		$this->set(compact('programs', 'programTypes'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid id for student status pattern');
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->StudentStatusPattern->delete($id)) {
			$this->Flash->success('Student status pattern deleted');
			return $this->redirect(array('action' => 'index'));
		}

		$this->Flash->error('Student status pattern was not deleted.');
		return $this->redirect(array('action' => 'index'));
	}


	// Regenerate Student Academic Status given published course 
	public function regenerate_status($published_course_id = null)
	{
		$published_course_combo_id = null;
		$department_combo_id = null;
		$publishedCourses = array();
		$have_message = false;

		if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
			$department_ids[] = $this->department_id;
			$college_ids = array();
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $department_ids, $college_ids, 1);
			$programs = $this->StudentStatusPattern->Program->find('list');
			$program_types = $this->StudentStatusPattern->ProgramType->find('list');
		} else {
			if (!empty($this->department_ids) && $this->role_id == ROLE_REGISTRAR) {
				$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_ids, array(), 1);
			} else if (!empty($this->college_ids) && $this->role_id == ROLE_REGISTRAR) {
				$departments = ClassRegistry::init('Department')->onlyFreshmanInAllColleges($this->college_ids, 1);
			} else {
				$departments = array();
			}

			$programs = $this->StudentStatusPattern->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
			$program_types = $this->StudentStatusPattern->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));
		}

		//List published course button is clicked
		if (isset($this->request->data['listPublishedCourses'])) {
			//There is nothing to do here for the time being
			$this->Session->delete('search_data_statusgeneration');
			// $publishedCourses = array();
			$this->request->data['StudentStatusPattern']['published_course_id'] = null;
			$published_course_id  = null;
		}

		if (!empty($this->request->data)) {

			$department_id = $this->request->data['StudentStatusPattern']['department_id'];
			$department_combo_id = $department_id;
			$college_id = explode('~', $department_id);
			$publishedCourses = array();

			if (is_array($college_id) && count($college_id) > 1) {
				$college_id = $college_id[1];
				$publishedCourses = ClassRegistry::init('CourseInstructorAssignment')->listOfCoursesCollegeFreshTakingOrgBySection(
					$college_id,
					$this->request->data['StudentStatusPattern']['acadamic_year'],
					$this->request->data['StudentStatusPattern']['semester'],
					$this->request->data['StudentStatusPattern']['program_id'],
					$this->request->data['StudentStatusPattern']['program_type_id']
				);
			} else {
				$publishedCourses = ClassRegistry::init('CourseInstructorAssignment')->listOfCoursesSectionsTakingOrgBySection(
					$department_id,
					$this->request->data['StudentStatusPattern']['acadamic_year'],
					$this->request->data['StudentStatusPattern']['semester'],
					$this->request->data['StudentStatusPattern']['program_id'],
					$this->request->data['StudentStatusPattern']['program_type_id']
				);
			}
			if (empty($publishedCourses)) {
				$this->Flash->info('There is no published courses with the selected filter criteria.');
				return $this->redirect(array('action' => 'regenerate_status'));
			} else {
				$publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
			}
			// $this->set(compact('publishedCourses'));
		}

		//When published course is selected from the combo box
		if (!empty($published_course_id) || (isset($this->request->data['StudentStatusPattern']['published_course_id']) && $this->request->data['StudentStatusPattern']['published_course_id'] != 0)) {

			if (isset($this->request->data['StudentStatusPattern']['published_course_id'])) {
				$published_course_id = $this->request->data['StudentStatusPattern']['published_course_id'];
			}

			$publishedCourses = array();

			$published_course = ClassRegistry::init('PublishedCourse')->find('first', array(
				'conditions' => array('PublishedCourse.id' => $published_course_id),
				'contain' => array('Section')
			));

			$departmentIds = array();
			$collegeIds = array();

			if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
				$departmentIds[] = $this->department_id;
			} else if (!empty($this->college_id) && $this->role_id == ROLE_COLLEGE) {
				$collegeIds[] = $this->college_id;
			} else if (!empty($this->department_ids)) {
				$departmentIds = $this->department_ids;
			} else if (!empty($this->college_ids)) {
				$collegeIds = $this->college_ids;
			}
			
			if (empty($published_course) || (!empty($published_course['PublishedCourse']['department_id']) && !in_array($published_course['PublishedCourse']['department_id'], $departmentIds)) || (!empty($published_course['PublishedCourse']['college_id']) && !in_array($published_course['PublishedCourse']['college_id'], $collegeIds))) {
				$this->Flash->error('Please select a valid published course.');
				return $this->redirect(array('action' => 'regenerate_status'));
			} else {
				if (empty($published_course['PublishedCourse']['department_id'])) {
					$publishedCourses = ClassRegistry::init('CourseInstructorAssignment')->listOfCoursesCollegeFreshTakingOrgBySection(
						$published_course['PublishedCourse']['college_id'],
						$published_course['PublishedCourse']['academic_year'],
						$published_course['PublishedCourse']['semester'],
						$published_course['PublishedCourse']['program_id'],
						$published_course['PublishedCourse']['program_type_id']
					);
					$department_combo_id = 'c~' . $published_course['PublishedCourse']['college_id'];
				} else {
					$publishedCourses = ClassRegistry::init('CourseInstructorAssignment')->listOfCoursesSectionsTakingOrgBySection(
						$published_course['PublishedCourse']['department_id'],
						$published_course['PublishedCourse']['academic_year'],
						$published_course['PublishedCourse']['semester'],
						$published_course['PublishedCourse']['program_id'],
						$published_course['PublishedCourse']['program_type_id']
					);
					$department_combo_id = $published_course['PublishedCourse']['department_id'];
				}
			}

			$published_course_combo_id = $published_course_id;

			if (!empty($published_course_id)) {
				// debug($published_course_id);
				$result = ClassRegistry::init('Student')->StudentExamStatus->updateAcdamicStatusByPublishedCourse($published_course_id);
				if ($result) {
					$this->Flash->success('Status Regenerated Successfully.');
					$this->Session->delete('search_data_statusgeneration');
					//return $this->redirect(array('action' => 'regenerate_status'));
				}
			}

			$program_id = $published_course['PublishedCourse']['program_id'];
			$program_type_id = $published_course['PublishedCourse']['program_type_id'];
			$department_id = $published_course['PublishedCourse']['department_id'];
			$academic_year_selected = $published_course['PublishedCourse']['academic_year'];
			$semester_selected = $published_course['PublishedCourse']['semester'];
		}

		$this->set(compact(
			'publishedCourses',
			'programs',
			'program_types',
			'departments',
			'published_course_combo_id',
			'department_combo_id',
			'program_id',
			'program_type_id',
			'department_id',
			'academic_year_selected',
			'semester_selected'
		));
	}

	function regenerate_individual_academic_status()
	{

		if (!empty($this->request->data) && isset($this->request->data['regenerate'])) {
			// read hidden status id for delete
			if (!empty($this->request->data['StudentStatusPattern'])) {
				$statusListForDelete = array_keys($this->request->data['StudentStatusPattern']);
			}

			$studentSectionAttended = ClassRegistry::init('StudentsSection')->find('list', array(
				'conditions' => array(
					'StudentsSection.student_id' => $this->request->data['Student']['id']
				), 
				'fields' => array('StudentsSection.section_id', 'StudentsSection.student_id')
			));

			$course_registered = ClassRegistry::init('CourseRegistration')->find('list', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $this->request->data['Student']['id']
				),
				'order' => array(
					'CourseRegistration.academic_year' => 'ASC',
					'CourseRegistration.semester' => 'ASC'
				),
				'fields' => array(
					'CourseRegistration.published_course_id',
					'CourseRegistration.published_course_id'
				),
				'recursive' => -1
			));

			$course_added = ClassRegistry::init('CourseAdd')->find('list', array(
				'conditions' => array(
					'CourseAdd.student_id' => $this->request->data['Student']['id']
				),
				'order' => array(
					'CourseAdd.academic_year' => 'ASC',
					'CourseAdd.semester' => ' ASC'
				),
				'fields' => array(
					'CourseAdd.published_course_id',
					'CourseAdd.published_course_id'
				), 'recursive' => -1
			));

			$listofputaken = $course_registered + $course_added;

			$listPublishedCourseTakenBySection = ClassRegistry::init('PublishedCourse')->find('all', array(
				'conditions' => array(
					'PublishedCourse.id' => $listofputaken
				),
				'order' => array(
					'PublishedCourse.academic_year' => 'ASC',
					'PublishedCourse.semester' => 'ASC'
				),
				'recursive' => -1
			));

			if (isset($statusListForDelete) && !empty($statusListForDelete)) {
				ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.id' => $statusListForDelete), false);
			}

			$statusgenerated = false;

			if (!empty($listPublishedCourseTakenBySection)) {
				foreach ($listPublishedCourseTakenBySection as $value) {
					$checkIfStatusIsGenerated = ClassRegistry::init('Student')->StudentExamStatus->find('count', array(
						'conditions' => array(
							'StudentExamStatus.student_id' => $this->request->data['Student']['id'], 
							'StudentExamStatus.academic_year' => $value['PublishedCourse']['academic_year'], 
							'StudentExamStatus.semester' => $value['PublishedCourse']['semester']
						)
					));
					
					if (!$checkIfStatusIsGenerated) {
						$statusgenerated = ClassRegistry::init('Student')->StudentExamStatus->updateAcdamicStatusByPublishedCourseOfStudent($value['PublishedCourse']['id'], $this->request->data['Student']['id']);
						if ($statusgenerated) {
							//debug($value);
						} else {
							debug($value);
						}
					}
				}
			}

			if ($statusgenerated) {
				$this->Flash->success('Status Regenerated Successfully.');
			}

			$this->request->data['regeneratestudentstatus'] = true;
		}

		// Function to load/save search criteria.
		if ($this->Session->read('search_data_statusgeneration') && !isset($this->request->data['regeneratestudentstatus'])) {
			$this->request->data['regeneratestudentstatus'] = true;
			$this->request->data['Student'] = $this->Session->read('search_data_statusgeneration');
			$this->set('hide_search', true);
		}

		if (!empty($this->request->data) && isset($this->request->data['regeneratestudentstatus'])) {
			$this->Session->delete('search_data_statusgeneration');
			$everythingfine = true;
			
			if (empty($this->request->data)) {
				$this->Flash->error('Please provide the Student Number(ID) you want to regenerate or update status.');
				$everythingfine = false;
			}

			$department_id = null;
			$college_id = null;

			if (!empty($this->department_ids)) {
				$department_id = $this->department_ids;
			} else if (!empty($this->department_id)) {
				$department_id = $this->department_id;
			} else {
				if ($this->role_id == ROLE_REGISTRAR) {
					if (!empty($this->department_ids)) {
						$department_id = $this->department_ids;
					} else if (!empty($this->college_ids)) {
						$college_id = $this->college_ids;
					}
				}
			}

			if ($everythingfine) {
				if (!empty($department_id)) {
					if ($this->role_id == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
						$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
							'conditions' => array(
								'Student.studentnumber' => trim($this->request->data['Student']['studentnumber']),
								//'Student.department_id' => $department_id,
								'Student.graduated'  => 0,
							)
						));
					} else {
						$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
							'conditions' => array(
								'Student.studentnumber' => trim($this->request->data['Student']['studentnumber']),
								'Student.department_id' => $department_id,
								'Student.graduated'  => 0,
							)
						));
					}
				} else if (!empty($college_id)) {
					$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
						'conditions' => array(
							'Student.studentnumber' => trim($this->request->data['Student']['studentnumber']), 
							'Student.college_id' => $college_id, 
							'Student.graduated'  => 0,
						)
					));
				}


				if ($check_id_is_valid > 0) {
					// do something if needed
					$everythingfine = true;
				} else {
					$everythingfine = false;

					$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
						'conditions' => array(
							'Student.studentnumber' => trim($this->request->data['Student']['studentnumber']),
							'Student.department_id' => $department_id,
							'Student.graduated'  => 1,
						)
					));

					if ($check_id_is_valid) {
						
						$studentDetails = ClassRegistry::init('Student')->find('first', array(
							'conditions' => array(
								'Student.studentnumber' => trim($this->request->data['Student']['studentnumber']),
								'Student.department_id' => $department_id,
							),
							'fields' => array('Student.full_name', 'Student.studentnumber'),
							'recursive' => -1
						));

						$this->Flash->warning($studentDetails['Student']['full_name'] .' ('. $studentDetails['Student']['studentnumber'] .') is a graduated student. Status Regeneration for graduated student is not allowed.');

					} else {

						$check_id_is_valid = ClassRegistry::init('Student')->find('count', array(
							'conditions' => array(
								'Student.studentnumber' => trim($this->request->data['Student']['studentnumber']),
							)
						));

						if (!$check_id_is_valid) {
							$this->Flash->error('The provided student number is not valid, Please check and try again.');
						} else {
							$this->Flash->warning('You don not have the privilege to access the selected student\'s profile.');
						}

					}
				}
			}

			//debug($this->request->data);
			if ($everythingfine) {
				$this->__init_search();

				$studentDbId = ClassRegistry::init('Student')->field('Student.id', array('Student.studentnumber' => trim($this->request->data['Student']['studentnumber'])));
				$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($studentDbId, null, null);

				$alreadyGeneratedStatus = ClassRegistry::init('StudentExamStatus')->find('all', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $studentDbId
					),
					'contain' => array(
						'Student' => array(
							'College',
							'Department',
							'Program',
							'ProgramType'
						),
						'AcademicStatus'
					)
				));

				$this->set('hide_search', true);
				$this->set(compact('alreadyGeneratedStatus', 'student_section_exam_status'));
			}
		}

		$programs = $this->StudentStatusPattern->Program->find('list');
		$program_types = $this->StudentStatusPattern->ProgramType->find('list');

		if (!empty($this->department_ids)) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array(
				'conditions' => array(
					'YearLevel.department_id' => $this->department_ids
				),
				'fields' => array('name', 'name')
			));
		} else if (!empty($this->department_id)) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array(
				'conditions' => array(
					'YearLevel.department_id' => $this->department_id
				),
				'fields' => array('name', 'name')
			));
		} else {
			$yearLevels[0] = "Pre/Unassign Freshman";
		}

		$this->set(compact('programs', 'program_types', 'yearLevels'));
	}


	function __init_search()
	{
		if (!empty($this->request->data['Student'])) {
			$this->Session->write('search_data_statusgeneration', $this->request->data['Student']);
		} else if ($this->Session->check('search_data_statusgeneration')) {
			$this->request->data['Student'] = $this->Session->read('search_data_statusgeneration');
		}
	}

	function regenerate_academic_status($section_id = null)
	{
		if (!empty($this->department_ids)) {
			$this->__regenerate_status($section_id, 0);
			//$this->_generateBySection($section_id);
		} else if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
			$this->__regenerate_status($section_id, 0);
		} else {
			$this->__regenerate_status($section_id, 1);
		}

		$this->__init_search_student();

		debug($this->request->data);
	}



	private function __regenerate_status($section_id = null, $freshman_program = 0)
	{
		/*
			1. Retrieve list of sections based on the given search criteria
			2. Display list of sections
			3. Up on the selection of section, display list of students with check-box
			4. Prepare student password issue/reset in PDF for the selected students
		*/

		if (isset($this->request->data['regenerateStatus'])) {
			
			//debug($this->request->data);
			$student_ids = array();
			
			if (!empty($this->request->data['StudentStatusPattern'])) {
				foreach ($this->request->data['StudentStatusPattern'] as $key => $student) {
					if (is_numeric($key)) {
						if (isset($student['gp']) && $student['gp'] == 1) {
							$student_ids[] =  $student['student_id'];
						}
						// unset selected students, Neway
						unset($this->request->data['StudentStatusPattern'][$key]['gp']);
					}
				}
			}

			//debug($student_ids);

			if (!empty($student_ids)) {
				if ($this->_generateBySection($this->request->data['StudentStatusPattern']['section_id'], $student_ids)) {
					$this->Flash->success('Status regenerated for ' . (count($student_ids)) . ' ' . (count($student_ids) > 1 ? 'students' : 'student' ) . ' successfully.');
					//$this->redirect(array('action' => 'regenerate_academic_status'));
					//$this->redirect(array('action' => 'regenerate_academic_status', $this->request->data['StudentStatusPattern']['section_id']));
				}
				unset($this->request->data['StudentStatusPattern']['select_all']);
				unset($student_ids);
			} else {
				$this->Flash->error('No student selected, Please select at least one student.');
			}

			//$section_id = $this->request->data['StudentStatusPattern']['section_id'];
			
		}

		if ($freshman_program == 0 && !empty($this->department_ids)) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_ids), 'fields' => array('name', 'name')));
		} else if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id), 'fields' => array('name', 'name')));
		} else {
			$yearLevels[0] = "Pre/Freshman";
		}

		$departments[0] = 0;

		// Expensive Process, for all university students

		/* if (isset($this->request->data['regenerateAllStatus'])) {

			//delete their previous status
			$isTheDeletionSuccessful = ClassRegistry::init('StudentExamStatus')->deleteAll(array(
				'StudentExamStatus.student_id in (select id from students where graduated = 0 and admissionyear >="' . $this->AcademicYear->get_academicYearBegainingDate($this->request->data['StudentStatusPattern']['acadamic_year']) . '" 
				and admissionyear <="' . $this->AcademicYear->nextAcademicYearBeginingDate($this->request->data['StudentStatusPattern']['acadamic_year']) . '" 
				and department_id=' . $this->request->data['StudentStatusPattern']['program_id'] . ' 
				and program_id=' . $this->request->data['StudentStatusPattern']['program_id'] . ' 
				and program_id=' . $this->request->data['StudentStatusPattern']['program_type_id'] . ' )'), false
			);

			if ($isTheDeletionSuccessful) {
				$done = ClassRegistry::init('Student')->regenerate_academic_status_by_batch(
					$this->request->data['StudentStatusPattern']['department_id'],
					$this->request->data['StudentStatusPattern']['acadamic_year'],
					0,
					0,
					0,
					0,
					$this->request->data['StudentStatusPattern']['program_id'],
					$this->request->data['StudentStatusPattern']['program_type_id']
				);

				if ($done) {
					$this->Flash->success('Status Regenerated Successfully.');
				}
			}
		} */

		$this->__init_search_student();

		$options = array();
		$section = array();

		//Get sections button is clicked
		if (isset($this->request->data['listSections'])) {

			$this->__init_clear_session_filters();
			$this->__init_search_student();

			$this->redirect(array('action' => 'regenerate_academic_status'));

			debug($this->request->data);

			if (!empty($this->request->data['StudentStatusPattern']['department_id'])) {
				$year_level_selected_id = ClassRegistry::init('YearLevel')->field('YearLevel.id', array('YearLevel.name' => $this->request->data['StudentStatusPattern']['year_level_id'], 'YearLevel.department_id' => $this->request->data['StudentStatusPattern']['department_id']));
			} else {
				$year_level_selected_id = null;
			}

			$options = array();

			$options = array(
				'conditions' => array(
					'Section.program_id' => $this->request->data['StudentStatusPattern']['program_id'],
					'Section.program_type_id' => $this->request->data['StudentStatusPattern']['program_type_id']
				),
				'contain' => array('YearLevel'),
				'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				$options['conditions'][] = array(
					'Section.college_id' => $this->request->data['StudentStatusPattern']['college_id'],
					'Section.archive' => 0,
					'Section.department_id IS NULL'
				);
			} else {
				$options['conditions'][] = array(
					'Section.department_id' => $this->request->data['StudentStatusPattern']['department_id'],
					'Section.archive' => 0,
					'Section.year_level_id' => $year_level_selected_id,
				);
			}

			//$sections = ClassRegistry::init('Section')->find('list', $options);

			$sections = ClassRegistry::init('Section')->find('all', $options);

			if (!empty($sections)) {
				$sectionOrganizedByYearLevel = array();
				//$sectionOrganizedByYearLevel[0] = '[ Select Section ]';
				foreach ($sections as $k => $v) {
					if (!empty($v['YearLevel']['name'])) {
						$sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
					} else {
						$sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", Pre/1st)";
					}
				}
				$sections = $sectionOrganizedByYearLevel;
			} 

			/* if ($freshman_program == 1) {
				$sections['pre'] = "All";
				asort($sections);
			} */

			if (empty($sections)) {
				$this->Flash->error('There is no section by the selected search criteria.');
			} /* else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			} */

			$year_level_selected = $this->request->data['StudentStatusPattern']['year_level_id'];
			$program_id = $this->request->data['StudentStatusPattern']['program_id'];
			$program_type_id = $this->request->data['StudentStatusPattern']['program_type_id'];
		}

		//Section is selected from the combo box
		else if (!empty($this->request->data['StudentStatusPattern']) || (!empty($section_id) && ($section_id != 0 || strcasecmp($section_id, "pre") == 0))) {
			
			if (!empty($this->request->data['StudentStatusPattern']['department_id'])) {
				$year_level_selected_id = ClassRegistry::init('YearLevel')->field('YearLevel.id', array(
					'YearLevel.name' => $this->request->data['StudentStatusPattern']['year_level_id'], 
					'YearLevel.department_id' => $this->request->data['StudentStatusPattern']['department_id']
				));
			} else {
				$year_level_selected_id = null;
			}

			/* if (isset($this->request->data['regenerateStatus'])) {
				$section_id = $this->request->data['StudentStatusPattern']['section_id'];
			} */

			if ($section_id != "pre" && is_numeric($section_id) && $section_id > 0) {

				$section_detail = ClassRegistry::init('Section')->find('first', array(
					'conditions' => array(
						'Section.id' => $section_id
					),
					'contain' => array('YearLevel'),
					'recursive' => -1
				));

				$year_level_selected = $section_detail['Section']['year_level_id'];
				$program_id = $section_detail['Section']['program_id'];
				$program_type_id = $section_detail['Section']['program_type_id'];
			}

			//Student list retrial
			if (strcasecmp($section_id, "pre") == 0) {
				$students_in_section = ClassRegistry::init('Student')->listStudentByAdmissionYear(null, $this->request->data['StudentStatusPattern']['college_id'], $this->request->data['StudentStatusPattern']['acadamic_year'], $this->request->data['StudentStatusPattern']['name'], 0);
				// $sections['pre']="All";
				// asort($sections);
			} else {
				//$students_in_section = ClassRegistry::init('Section')->getSectionStudents($section_id, $this->request->data['StudentStatusPattern']['name'], 0);
				$students_in_section = ClassRegistry::init('Section')->getSectionStudentsForStatus($section_id, null /* $this->request->data['StudentStatusPattern']['acadamic_year'] */);
			}

			//debug($students_in_section);

			if (!empty($this->request->data['StudentStatusPattern'])) {

				$options = array(
					'conditions' => array(
						'Section.program_id' => $this->request->data['StudentStatusPattern']['program_id'],
						'Section.program_type_id' => $this->request->data['StudentStatusPattern']['program_type_id'],
						'Section.archive' => 0,
						'Section.academicyear' => $this->request->data['StudentStatusPattern']['status_acadamic_year']
					),
					'contain' => array('YearLevel'),
					'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
					'recursive' => -1
				);

				if ($freshman_program == 1) {
					$options['conditions'][] = array(
						'Section.college_id' => $this->request->data['StudentStatusPattern']['college_id'],
						'Section.academicyear' => $this->request->data['StudentStatusPattern']['status_acadamic_year'],
						'Section.department_id IS NULL'
					);
				} else {
					$options['conditions'][] = array(
						'Section.department_id' => $this->request->data['StudentStatusPattern']['department_id'],
						'Section.academicyear' => $this->request->data['StudentStatusPattern']['status_acadamic_year'],
						'Section.year_level_id' => $year_level_selected_id
					);
				}

				//$sections = ClassRegistry::init('Section')->find('list', $options);
				$sections = ClassRegistry::init('Section')->find('all', $options);
				

				if (!empty($sections)) {
					$sectionOrganizedByYearLevel = array();
					//$sectionOrganizedByYearLevel[0] = '[ Select Section ]';
					foreach ($sections as $k => $v) {
						if (!empty($v['YearLevel']['name'])) {
							$sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
						} else {
							$sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", Pre/1st)";
						}
					}
					$sections = $sectionOrganizedByYearLevel;
				} 

				//Give an option to get all freshman studnet of the college
				/* if ($freshman_program == 1) {
					$sections['pre'] = "All";
					asort($sections);
				} */

				if (empty($sections)) {
					$this->Flash->info('There is no section by the selected search criteria.');
				} 
			}
		}


		if (!empty($section_id) && is_numeric($section_id) && $section_id > 0) {

			$selectedSectionsDetail = ClassRegistry::init('Section')->find('first', array('conditions' => array('Section.id' => $section_id)));

			/* $sections = ClassRegistry::init('Section')->find('list', array('conditions' => array(
				'Section.department_id' => $selectedSectionsDetail['Section']['department_id'],
				'Section.year_level_id' => $selectedSectionsDetail['Section']['year_level_id'],
				'Section.program_type_id' => $selectedSectionsDetail['Section']['program_type_id'],
				'Section.program_id' => $selectedSectionsDetail['Section']['program_id'],
				'Section.academicyear' => $selectedSectionsDetail['Section']['academicyear'],
				'Section.college_id' => $selectedSectionsDetail['Section']['college_id']
			))); */


			$sections = ClassRegistry::init('Section')->find('all', array(
				'conditions' => array(
					'Section.department_id' => $selectedSectionsDetail['Section']['department_id'],
					'Section.year_level_id' => $selectedSectionsDetail['Section']['year_level_id'],
					'Section.program_type_id' => $selectedSectionsDetail['Section']['program_type_id'],
					'Section.program_id' => $selectedSectionsDetail['Section']['program_id'],
					'Section.academicyear' => $selectedSectionsDetail['Section']['academicyear'],
					'Section.college_id' => $selectedSectionsDetail['Section']['college_id']
				),
				'contain' => array('YearLevel'),
				'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
				'recursive' => -1
			));

			if (!empty($sections)) {
				$sectionOrganizedByYearLevel = array();
				//$sectionOrganizedByYearLevel[0] = '[ Select Section ]';
				foreach ($sections as $k => $v) {
					if (!empty($v['YearLevel']['name'])) {
						$sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", " . $v['YearLevel']['name'] . ")";
					} else {
						$sectionOrganizedByYearLevel[$v['Section']['id']] = $v['Section']['name'] . " (" . $v['Section']['academicyear'] . ", Pre/1st)";
					}
				}
				$sections = $sectionOrganizedByYearLevel;
			} 
			
		}


		$department_ids = array();
		$college_ids = array();

		if (!empty($this->department_ids) && $this->role_id == ROLE_REGISTRAR) {
			$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $this->department_ids,'Department.active' => 1), 'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')));
			$department_ids = $this->department_ids;
		} else if (!empty($this->college_ids) && $this->role_id == ROLE_REGISTRAR) {
			$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			$college_ids = $this->college_ids;
		} else if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
			$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if (!empty($this->college_id) && $this->role_id == ROLE_COLLEGE) {
			$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.id' => $this->college_id)));
		}

		$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$this->set(compact(
			'programs',
			'departments',
			'colleges',
			'program_types',
			'departments',
			'yearLevels',
			'year_level_selected',
			'semester_selected',
			'program_id',
			'program_type_id',
			'section_id',
			'sections',
			'students_in_section',
			'department_ids',
			'college_ids'
		));

		$this->render('regenerate_academic_status');
	}

	function __init_search_student()
	{
		if (!empty($this->request->data['StudentStatusPattern'])) {
			if (isset($this->request->data['listSections'])) {
				unset($this->request->data['StudentStatusPattern']['select_all']);
				unset($this->request->data['StudentStatusPattern']['section_id']);
				foreach ($this->request->data['StudentStatusPattern'] as $sspkey => $sspval) {
					if (is_numeric($sspkey)) {
						unset($this->request->data['StudentStatusPattern'][$sspkey]);
					}
				}
			}

			unset($this->request->data['StudentStatusPattern']['section_id']);
			
			$this->Session->write('search_data_student_status_pattern', $this->request->data['StudentStatusPattern']);
		} else if ($this->Session->check('search_data_student_status_pattern')) {
			$this->request->data['StudentStatusPattern'] = $this->Session->read('search_data_student_status_pattern');
		}
	}

	function __init_clear_session_filters()
	{
		if ($this->Session->check('search_data_student_status_pattern')) {
			$this->Session->delete('search_data_student_status_pattern');
		}
	}

	private function _generateBySection($section_id, $studentLists = array())
	{
		$statusgenerated = false;

		if (!empty($studentLists)) {
			$studentSectionAttended = ClassRegistry::init('StudentsSection')->find('all', array(
				'conditions' => array(
					'section_id' => $section_id,
					'student_id' => $studentLists
				), 
				'fields' => array('section_id', 'student_id'),
				'group' => array('section_id', 'student_id')
			));
		} else {
			$studentSectionAttended = ClassRegistry::init('StudentsSection')->find('all', array(
				'conditions' => array(
					'section_id' => $section_id
				), 
				'fields' => array('section_id', 'student_id'),
				'group' => array('section_id', 'student_id'),
			));
		}

		if (!empty($studentSectionAttended)) {
			foreach ($studentSectionAttended as $sec_id => $student) {
				$statusListForDelete = ClassRegistry::init('StudentExamStatus')->find('list', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $student['StudentsSection']['student_id']
					), 
					'fields' => array('StudentExamStatus.id', 'StudentExamStatus.id')
				));

				$course_registered = ClassRegistry::init('CourseRegistration')->find('list', array(
					'conditions' => array(
						'CourseRegistration.student_id' => $student['StudentsSection']['student_id']
					),
					'order' => array(
						'CourseRegistration.academic_year' => 'ASC',
						'CourseRegistration.semester' => 'ASC'
					),
					'fields' => array(
						'CourseRegistration.published_course_id',
						'CourseRegistration.published_course_id'
					),
					'recursive' => -1,
				));

				$course_added = ClassRegistry::init('CourseAdd')->find('list', array(
					'conditions' => array(
						'CourseAdd.student_id' => $student['StudentsSection']['student_id']
					),
					'order' => array(
						'CourseAdd.academic_year' => 'ASC',
						'CourseAdd.semester' => 'ASC'
					),
					'fields' => array(
						'CourseAdd.published_course_id',
						'CourseAdd.published_course_id'
					),
					'recursive' => -1,
				));

				$listofputaken = $course_registered + $course_added;

				$listPublishedCourseTakenBySection = ClassRegistry::init('PublishedCourse')->find('all', array(
					'conditions' => array(
						'PublishedCourse.id' => $listofputaken
					),
					'order' => array(
						'PublishedCourse.academic_year' => 'ASC',
						'PublishedCourse.semester' => 'ASC'
					), 
					'recursive' => -1
				));

				debug($statusListForDelete);

				if (isset($statusListForDelete) && !empty($statusListForDelete)) {
					ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.id' => $statusListForDelete), false);
				}

				debug($listPublishedCourseTakenBySection);

				if (!empty($listPublishedCourseTakenBySection)) {
					foreach ($listPublishedCourseTakenBySection as $value) {
						$checkIfStatusIsGenerated = ClassRegistry::init('Student')->StudentExamStatus->find('count', array('conditions' => array('StudentExamStatus.student_id' => $student['StudentsSection']['student_id'], 'StudentExamStatus.academic_year' => $value['PublishedCourse']['academic_year'], 'StudentExamStatus.semester' => $value['PublishedCourse']['semester'])));
						if (!$checkIfStatusIsGenerated) {
							$statusgenerated = ClassRegistry::init('Student')->StudentExamStatus->updateAcdamicStatusByPublishedCourseOfStudent($value['PublishedCourse']['id'], $student['StudentsSection']['student_id']);
						}
					}
				}
			}
		}
		return $statusgenerated;
	}
}
