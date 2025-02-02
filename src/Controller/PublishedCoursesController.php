<?php
class PublishedCoursesController extends AppController
{

	public $name = 'PublishedCourses';
	public $helpers = array('Xls', 'Media.Media');
	public $menuOptions = array(
		'parent' => 'curriculums',
		'exclude' => array(
			'print_published_pdf', 
			'export_published_xls', 
			'get_year_level', 
			'get_course_type_session',
			'getPublishedCoursesForSplit', 
			'getPublishedCoursesForExam', 
			'get_course_grade_scale', 
			'get_course_grade_stats',
			'getPublishedCourses', 
			'selectedPublishedCourses', 
			'getPublishedCoursesForExamForSplit',
			'get_course_published_for_section', 
			'publisheForUnassigned'
		),
		'alias' => array(
			'index' => 'List Published Courses',
			//'attache_scale' => 'Attach Grade Scale(D)',
			'add' => 'Publish Semester Courses',
			//'college_attache_scale' => 'Attach Grade Scale(C)',
			'unpublish' => 'Unpublish/Drop Semester Courses',
			'college_publish_course' => 'Publish Courses (Pre/1st)',
			'college_unpublish_course' => 'Unpublish/Drop Courses (Pre/1st)'
		)
	);

	public $components = array('AcademicYear');
	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow(
			'getPublishedCourses',
			'getPublishedCoursesForSplit',
			'get_year_level',
			'selectedPublishedCourses',
			'getPublishedCoursesForExam',
			'getPublishedCoursesForExamForSplit',
			'get_course_type_session',
			'publisheForUnassigned',
			'get_course_grade_scale',
			'get_course_grade_stats',
			'get_course_published_for_section'
		);
	}

	public function beforeRender()
	{
		parent::beforeRender();

		$current_academicyear = $this->AcademicYear->current_academicyear();

		if (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
			$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $current_academicyear)[0]) - ACY_BACK_FOR_ALL), (explode('/', $current_academicyear)[0]));
		} else {
			$acyear_array_data[$current_academicyear] = $current_academicyear;
		}

		//debug($acyear_array_data);

		$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
		$defaultacademicyear = $current_acy_and_semester['academic_year'];
		$defaultsemester = $current_acy_and_semester['semester'];

		//debug($defaultsemester);

		$this->set('defaultacademicyear', $defaultacademicyear);
		$this->set('defaultsemester', $defaultsemester);
		
		$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		//$depts_for_year_level = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));
		
		//$yearLevels = $this->year_levels;
		//$yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(null , null, array_keys($depts_for_year_level), array_keys($programs));
		//debug($yearLevels);


		$this->set(compact('acyear_array_data', 'program_types', 'programTypes', 'programs', 'yearLevels'));
	}

	public function index($semester = null, $academic_year = null, $selected_program_id = null, $selected_program_type_id = null)
	{
		//	$this->PublishedCourse->recursive = 0;

		if (isset($this->request->data['search'])) {
			if ($this->Session->check('search_data_published_course')) {
				$this->Session->delete('search_data_published_course');
				$this->__init_search();
			}
		}

		if ($this->Session->check('search_data_published_course')) {
			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$academic_year = (!empty($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $this->AcademicYear->current_acy_and_semester()['academic_year']);
			$semester = (!empty($this->request->data['PublishedCourse']['semester']) ? $this->request->data['PublishedCourse']['semester'] : $this->AcademicYear->current_acy_and_semester()['semester']);
			$selected_program_id = $this->request->data['PublishedCourse']['program_id'];
			$selected_program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
			
		} else {

			if (!empty($semester) && !empty($academic_year)) {
				$academic_year = str_replace('-', '/', $academic_year);
				$this->request->data['PublishedCourse']['semester'] = $semester;
				$this->request->data['PublishedCourse']['academic_year'] = $academic_year;
			} else {
				$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester();
				$academic_year = $current_acy_and_semester['academic_year'];
				$semester = $current_acy_and_semester['semester'];
			}
		}


		if (!empty($this->request->data['PublishedCourse']['program_id'])) {
			$selected_program_id = $this->request->data['PublishedCourse']['program_id'];
		} else if (empty($selected_program_id)) {
			if (!empty($this->program_ids)) {
				$selected_program_id = array_values($this->program_ids)[0];
				$this->request->data['PublishedCourse']['program_id'] = $selected_program_id;
			} else {
				$programss = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
				if (!empty($programss)) {
					$selected_program_id = array_values($programss)[0];
					$this->request->data['PublishedCourse']['program_id'] = $selected_program_id;
				}
			}
		} else {
			$this->request->data['PublishedCourse']['program_id'] = $selected_program_id;
		}

		if (!empty($this->request->data['PublishedCourse']['program_type_id'])) {
			$selected_program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
		} else if (empty($selected_program_type_id)) {
			if (!empty($this->program_type_ids)) {
				$selected_program_type_id = array_values($this->program_type_ids)[0];
				$this->request->data['PublishedCourse']['program_type_id'] = $selected_program_type_id;
			} else {
				$program_typess = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1)));
				if (!empty($program_typess)) {
					$selected_program_type_id = array_values($program_typess)[0];
					$this->request->data['PublishedCourse']['program_type_id'] = $selected_program_type_id;
				}
			}
		} else {
			$this->request->data['PublishedCourse']['program_type_id'] = $selected_program_type_id;
		}

		if ($this->role_id == ROLE_COLLEGE || ($this->role_id == ROLE_REGISTRAR && $this->onlyPre && !empty($this->college_ids))) {
			$conditions = null;
			if (!empty($this->request->data)) {
				$everythingfine = true;
				/* switch ($this->request->data) {
					case empty($this->request->data['PublishedCourse']['academic_year']):
						$this->Flash->error(__('Please select the academic year you want to view publish courses.'));
						break;
					case empty($this->request->data['PublishedCourse']['semester']):
						$this->Flash->error( __('Please select the semester you want to view published courses. '));
						break;
					default:
						$everythingfine = true;
				} */

				if ($everythingfine) {

					//$programs = $this->PublishedCourse->Program->find('list');
					//$programTypes = $this->PublishedCourse->ProgramType->find('list');

					$publishedCourses = array();

					$academic_year = (isset($this->request->data['PublishedCourse']['academic_year']) && !empty($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $academic_year);
					$semester = (isset($this->request->data['PublishedCourse']['semester']) && !empty($this->request->data['PublishedCourse']['semester']) ? $this->request->data['PublishedCourse']['semester'] : $semester);
					$selected_program_id = (isset($this->request->data['PublishedCourse']['program_id']) && !empty($this->request->data['PublishedCourse']['program_id']) ? $this->request->data['PublishedCourse']['program_id'] : (isset($this->program_ids) ? array_values($this->program_ids)[0] : '1'));
					$selected_program_type_id = (isset($this->request->data['PublishedCourse']['program_type_id']) && !empty($this->request->data['PublishedCourse']['program_type_id']) ? $this->request->data['PublishedCourse']['program_type_id'] : (isset($this->program_type_ids) ? array_values($this->program_type_ids)[0] : '1'));

					if (!empty($semester)) {
						$this->request->data['PublishedCourse']['semester'] = $semester;
					}

					if ($academic_year) {
						$this->request->data['PublishedCourse']['academic_year'] = $academic_year;
					}

					$sections = ClassRegistry::init('Section')->find('list', array(
						'conditions' => array(
							'Section.college_id' => $this->college_ids,
							'Section.academicyear LIKE ' => $academic_year .'%',
							'Section.program_id' => $selected_program_id,
							'Section.program_type_id' => $selected_program_type_id,
							'Section.department_id is null',
						)
					));

					//debug($sections);


					if (!empty($sections)) {

						$this->Paginator->settings =  array(
							'contain' => array(
								'YearLevel'=> array('id', 'name'),
								'Course' => array(
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
								),
								'ProgramType' => array('id', 'name'),
								'Program' => array('id', 'name'),
								/* 'Department' => array(
									'fields' => array('id', 'name', 'type', 'college_id', 'active'), 
									'College' => array(
										'fields' => array('id', 'name', 'type', 'campus_id', 'stream', 'active'), 
										'Campus' => array('id', 'name')
									)
								), */
								'GivenByDepartment' => array(
									'fields' => array('id', 'name', 'type', 'college_id', 'active'), 
									'College' => array(
										'fields' => array('id', 'name', 'type', 'campus_id', 'stream', 'active'), 
										'Campus' => array('id', 'name')
									)
								),
								'College' => array(
									'fields' => array('id', 'name', 'type', 'campus_id', 'stream', 'active'), 
									'Campus' => array('id', 'name')
								),
								'Section' => array(
									'YearLevel'=> array('id', 'name'),
									'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
								)
							), 
							'order' => array('PublishedCourse.department_id' => 'ASC', 'PublishedCourse.year_level_id' => 'ASC', 'PublishedCourse.section_id' => 'ASC', 'PublishedCourse.id' => 'ASC'),
							'recursive' => -1
						);

						foreach ($sections as $sk => $sv) {
							if (!empty($academic_year)) {
								$conditions = array(
									"PublishedCourse.college_id" => $this->college_ids,
									"PublishedCourse.semester" => $semester,
									"PublishedCourse.published" => 1,
									"PublishedCourse.section_id" => $sk,
									"PublishedCourse.program_id" => $selected_program_id,
									"PublishedCourse.program_type_id" => $selected_program_type_id,
									"PublishedCourse.academic_year LIKE " => $academic_year
								);
							} else {
								$conditions = array(
									"PublishedCourse.college_id " => $this->college_id,
									"PublishedCourse.semester" => $semester,
									"PublishedCourse.published" => 1,
									"PublishedCourse.section_id" => $sk,
									"PublishedCourse.program_id" =>  $selected_program_id,
									"PublishedCourse.program_type_id" => $selected_program_type_id,
								);
							}

							$checkempty = $this->paginate($conditions);

							if (!empty($checkempty)) {
								$tmpcopy = array();
								foreach ($checkempty as $chindex => $chevalue) {
									if ($chevalue['PublishedCourse']['add'] == 0 && $chevalue['PublishedCourse']['drop'] == 0) {
										$tmpcopy['Semester Registered'][] = $chevalue;
									} else if ($chevalue['PublishedCourse']['add'] == 1) {
										$tmpcopy['Mass Add'][] = $chevalue;
									} else if ($chevalue['PublishedCourse']['drop'] == 1) {
										$tmpcopy['Mass Dropped'][] = $chevalue;
									}
								}

								$sectionDetail = ClassRegistry::init('Section')->find('first', array(
									'conditions' => array(
										'Section.id' => $sk,
									),
									'contain' => array(
										'College' => array('id', 'name', 'shortname'),
										'ProgramType' => array('id', 'name'),
										'Program' => array('id', 'name'),
									),
									'recursive' => -1
								));

								//$publishedCourses[$sv] = $tmpcopy;

								$publishedCourses[$sectionDetail['Program']['name']][$sectionDetail['ProgramType']['name']][$sectionDetail['College']['name']][$sv] = $tmpcopy;
							}
						}
					}

					if (empty($publishedCourses)) {
						debug($this->request->clientIp);
						$this->Flash->info(__('There is no published courses in the given search criteria. Please select different criteria.'));
					} else {
						$this->set('publishedCoursesCollege', $publishedCourses);
						$this->set('semester', $semester);
						$this->set('academic_year', $academic_year);
						//$this->set(compact('semester', 'academic_year'));
						$this->Session->write('publishedCourses', $publishedCourses);
						$this->Session->write('selected_academic_year', $academic_year);
						$this->Session->write('selected_semester', $semester);
					}
				}
			}
		}

		if ($this->role_id == ROLE_DEPARTMENT || ($this->role_id == ROLE_REGISTRAR && !$this->onlyPre && !empty($this->department_ids))) {
			$conditions = null;

			if (!empty($this->request->data)) {
				$everythingfine = true;
				/* switch ($this->request->data) {
					case empty($this->request->data['PublishedCourse']['academic_year']):
						$this->Flash->error( __('Please select the academic year you want to view publish courses.'));
						break;
					case empty($this->request->data['PublishedCourse']['semester']):
						$this->Flash->error( __('Please select the semester you want to view published courses.'));
						break;
					default:
						$everythingfine = true;
				} */

				if ($everythingfine) {

					$academic_year = (isset($this->request->data['PublishedCourse']['academic_year']) && !empty($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $academic_year);
					$semester = (isset($this->request->data['PublishedCourse']['semester']) && !empty($this->request->data['PublishedCourse']['semester']) ? $this->request->data['PublishedCourse']['semester'] : $semester);
					$selected_program_id = (isset($this->request->data['PublishedCourse']['program_id']) && !empty($this->request->data['PublishedCourse']['program_id']) ? $this->request->data['PublishedCourse']['program_id'] : (isset($this->program_ids) ? array_values($this->program_ids)[0] : '1'));
					$selected_program_type_id = (isset($this->request->data['PublishedCourse']['program_type_id']) && !empty($this->request->data['PublishedCourse']['program_type_id']) ? $this->request->data['PublishedCourse']['program_type_id'] : (isset($this->program_type_ids) ? array_values($this->program_type_ids)[0] : '1'));

					if (!empty($semester)) {
						$this->request->data['PublishedCourse']['semester'] = $semester;
					}

					if ($academic_year) {
						$this->request->data['PublishedCourse']['academic_year'] = $academic_year;
					}

					$publishedCourses = null;
					$options = array();

					//$options['conditions']['PublishedCourse.department_id'] = $this->department_ids;

					if ($this->role_id == ROLE_REGISTRAR && !empty($this->college_ids)) {
						$options['conditions']['PublishedCourse.college_id'] = $this->college_ids;
						$options['conditions']['PublishedCourse.department_id'] = NULL;
					} else {
						$options['conditions']['PublishedCourse.department_id'] = $this->department_ids;
					}

					$options['conditions']['PublishedCourse.semester'] =  $semester;
					$options['conditions']['PublishedCourse.academic_year'] = $academic_year;
					$options['conditions']['PublishedCourse.program_id'] = $selected_program_id;
					$options['conditions']['PublishedCourse.program_type_id'] = $selected_program_type_id;
					// $options['conditions']['PublishedCourse.published'] =  1;

					$options['contain'] = array(
						'YearLevel' => array('id', 'name'),
						'Course' => array(
							'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active')
						),
						'ProgramType' => array('id', 'name'),
						'Program' => array('id', 'name'),
						'Department' => array(
							'fields' => array('id', 'name', 'type', 'college_id', 'active'),
							'College' => array(
								'fields' => array('id', 'name', 'type', 'campus_id', 'stream', 'active'),
								'Campus' => array('id', 'name')
							)
						),
						'GivenByDepartment' => array(
							'fields' => array('id', 'name', 'type', 'college_id', 'active'),
							'College' => array(
								'fields' => array('id', 'name', 'type', 'campus_id', 'stream', 'active'),
								'Campus' => array('id', 'name')
							)
						),
						'College' => array(
							'fields' => array('id', 'name', 'type', 'campus_id', 'stream', 'active'),
							'Campus' => array('id', 'name')
						),
						'Section' => array(
							'YearLevel' => array('id', 'name'),
							'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
						)
					);

					$options['recursive'] = -1;
					$options['order'] = array('PublishedCourse.department_id' => 'ASC', 'PublishedCourse.year_level_id' => 'ASC', 'PublishedCourse.section_id' => 'ASC', 'PublishedCourse.id' => 'ASC');

					$checkempty = $this->PublishedCourse->find('all', $options);
					// debug($checkempty);

					if (!empty($checkempty)) {
						$section_one = null;

						$last_index = count($checkempty) - 1;

						if (!empty($checkempty)) {
							foreach ($checkempty as $chindex => $chevalue) {
								if ($chevalue['PublishedCourse']['add'] == 0 && $chevalue['PublishedCourse']['drop'] == 0 ) {
									$tmpcopy[$chevalue['PublishedCourse']['section_id']]['Semester Registered'][] = $chevalue;
								} else if ($chevalue['PublishedCourse']['add'] == 1) {
									$tmpcopy[$chevalue['PublishedCourse']['section_id']]['Mass Added'][] = $chevalue;
								} else if ($chevalue['PublishedCourse']['drop'] == 1) {
									$tmpcopy[$chevalue['PublishedCourse']['section_id']]['Mass Dropped'][] = $chevalue;
								}

								$publishedCourses[$this->request->data['PublishedCourse']['semester']][$chevalue['Program']['name']][$chevalue['ProgramType']['name']][$chevalue['Department']['name']][$chevalue['YearLevel']['name']][$chevalue['Section']['name']] = $tmpcopy[$chevalue['PublishedCourse']['section_id']];
							}
						}
					}

					if (empty($publishedCourses)) {
						$this->Flash->info( __('There is no published courses in the given search criteria. Please select different criteria.'));
					} else {
						$this->set('publishedCourses', $publishedCourses);
						$this->set('semester', $semester);
						$this->set('academic_year', $academic_year);
						$this->Session->write('publishedCourses', $publishedCourses);
						$this->Session->write('selected_academic_year', $academic_year);
						$this->Session->write('selected_semester', $semester);
						$this->Session->write('selected_program_id', $selected_program_id);
						$this->Session->write('selected_program_type_id', $selected_program_type_id);
					}
				}
			}
		}
		$this->__init_search();
	}

	public function view($id = null)
	{
		if (!$id) {
			$this->Flash->error( __('Invalid published course'));
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$conditions = array('PublishedCourse.id' => $id, 'PublishedCourse.department_id' => $this->department_id);
		} else if ($this->role_id == ROLE_DEPARTMENT) {
			$conditions = array('PublishedCourse.id' => $id, 'PublishedCourse.department_id' => $this->college_id);
		}

		$publishedCourse = $this->PublishedCourse->find('first', array('conditions' => $conditions));
		$this->set('publishedCourse', $publishedCourse);
	}

	public function add()
	{
		if (!empty($this->request->data) && isset($this->request->data['getsection'])) {
			$everythingfine = false;
			switch ($this->request->data) {
				case empty($this->request->data['Course']['academicyear']):
					$this->Flash->error( __('Please select the academic year you want to publish courses.'));
					break;
				case empty($this->request->data['Curriculum']['semester']):
					$this->Flash->error( __('Please select the semester you want to publish courses. Please, try again.'));
					break;
				case empty($this->request->data['Course']['year_level_id']):
					$this->Flash->error( __('Please select the year level you want to publish courses. Please, try again.'));
					break;
				case empty($this->request->data['Curriculum']['program_id']):
					$this->Flash->error( __('Please select the program you want to publish courses. Please, try again.'));
					break;
				case empty($this->request->data['Curriculum']['program_type_id']):
					$this->Flash->error( __('Please select the program type you want to publish courses. Please, try again.'));
					break;
				default:
					$everythingfine = true;
			}

			if ($everythingfine) {
				debug($this->request->data);
				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.department_id' => $this->department_id,
						'Section.year_level_id' => $this->request->data['Course']['year_level_id'],
						'Section.program_id' => $this->request->data['Curriculum']['program_id'],
						'Section.program_type_id' => $this->request->data['Curriculum']['program_type_id'],
						'Section.archive' => 0,
						'Section.academicyear' => $this->request->data['Course']['academicyear']
					))
				);

				debug($sections);
				// get the curriculum of each section student is attending and
				// attach it and display for the user
				$section_array_list = $this->__section_curriculum($sections, true);
				debug($section_array_list);
				$sections = $section_array_list;

				if (empty($sections)) {
					$this->Flash->info( __('No section is found that needs courses publishing with the selected search criteria.'));
				} else {

					$year_level_id = $this->request->data['Course']['year_level_id'];
					$program_id = $this->request->data['Curriculum']['program_id'];
					$program_type_id = $this->request->data['Curriculum']['program_type_id'];
					$academic_year = $this->request->data['Course']['academicyear'];
					$semester = $this->request->data['Curriculum']['semester'];

					$this->set('turn_off_search', true);

					$this->set(compact(
						'sections',
						'year_level_id',
						'program_id',
						'program_type_id',
						'curriculum_id',
						'academic_year',
						'section_map_curriculum',
						'semester'
					));
				}
			} else {
				//$this->redirect(array('action'=>'add'));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['continuepublish'])) {
			$everythingfine = false;
			switch ($this->request->data) {
				case empty($this->request->data['PublishedCourse']['section_id']):
					$this->Flash->error( __('Please select at least one section you want to publish courses.'));
					break;
				default:
					$everythingfine = true;
			}

			if ($everythingfine) {

				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.department_id' => $this->department_id,
						'Section.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
						'Section.archive' => 0,
						'Section.academicyear' => $this->request->data['Course']['academicyear']
					)
				));

				$selected_section = array();

				if (!empty($this->request->data['PublishedCourse']['section_id'])) {
					foreach ($this->request->data['PublishedCourse']['section_id'] as $pse => $psv) {
						$selected_section[$psv] = $sections[$psv];
					}
				}

				debug($selected_section);

				// curriculum of selected section
				$section_curriculum_attachment = $this->__section_curriculum($selected_section, false);
				//$sections = $this->__section_curriculum($selected_section,true);
				$sections = $this->__section_curriculum($sections, true);

				//find the already taken courses of the section
				$section_already_taken_courses = array();
				$max_courses_taken_count = 0;
				$max_courses_taken_index_student = 0;
				//  debug($this->request->data['PublishedCourse']['section_id']);

				if (!empty($this->request->data['PublishedCourse']['section_id'])) {
					foreach ($this->request->data['PublishedCourse']['section_id'] as $sec_key => $sec_Id) {

						$courses = $this->PublishedCourse->Section->studentsAlreaydTakenCourse($sec_Id);

						if (!empty($courses)) {
							foreach ($courses as $ck => $cv) {
								if (!empty($cv['Student'])) {
									foreach ($cv['Student'] as $index => $course) {
										if (count($course['CourseRegistration']) <= $max_courses_taken_count) {
											$max_courses_taken_count = count($course['CourseRegistration']);
											$max_courses_taken_index_student = $index;
										}
									}
									debug($max_courses_taken_index_student);

									if (isset($cv['Student'][$max_courses_taken_index_student]['CourseRegistration']) && !empty($cv['Student'][$max_courses_taken_index_student]['CourseRegistration'])) {
										foreach ($cv['Student'][$max_courses_taken_index_student]['CourseRegistration'] as $kk => $course_ids) {
											debug($course_ids);
											if ($this->PublishedCourse->Course->isCourseTakenHaveRecentPassGrade($course_ids['student_id'], $course_ids['PublishedCourse']['course_id'])) {
												$section_already_taken_courses[$sec_Id][] = $course_ids['PublishedCourse']['course_id'];
											}
										}
									} else {
										$section_already_taken_courses[$sec_Id][] = 0;
									}
								}
							}
						}
					}
				}
				//debug($section_already_taken_courses);

				$ready_for_publishing_courses = array();
				// incase all students fail or something happens
				$taken_courses_allow_to_publishe_it = array();

				if (!empty($section_already_taken_courses)) {
					foreach ($section_already_taken_courses as $section_id => $taken_courses) {
						if (!empty($taken_courses)) {
							// without semester to make it flexible for users, but huge course list
							// 'Course.semester' => trim($this->request->data['PublishedCourse']['semester']);
							
							$ready_for_publishing_courses[$section_id] = $this->PublishedCourse->Course->find('all', array('conditions' => array('Course.curriculum_id' => $section_curriculum_attachment[$section_id], 'Course.department_id' => $this->department_id, "NOT" => array('Course.id ' => $taken_courses)), 'contain' => array('PublishedCourse')));
							
							$taken_courses_allow_to_publishe_it[$section_id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
									'Course.department_id' => $this->department_id,
									'Course.id ' => $taken_courses
								), 
								'contain' => array('PublishedCourse')
							));

							$ready_for_publishing_courses[$section_id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$section_id], 
									"NOT" => array('Course.id ' => $taken_courses)
								), 
								'contain' => array('PublishedCourse')
							));

							$taken_courses_allow_to_publishe_it[$section_id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
									'Course.department_id' => $this->department_id,
									'Course.id ' => $taken_courses
								), 
								'contain' => array('PublishedCourse')
							));

						} else {
							// without semester to make it flexible for users, but huge course list
							// 'Course.semester' => trim($this->request->data['PublishedCourse']['semester']);
							$ready_for_publishing_courses[$section_id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
									'Course.department_id' => $this->department_id
								),
								'contain' => array(
									'PublishedCourse'
								)
							));
						}
					}
				}


				$this->set('coursesss', $ready_for_publishing_courses);
				$this->set(compact('taken_courses_allow_to_publishe_it'));
				$show_publish_page = true;

				$year_level_id = $this->request->data['PublishedCourse']['year_level_id'];
				$program_id = $this->request->data['PublishedCourse']['program_id'];
				$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
				$academic_year = $this->request->data['PublishedCourse']['academic_year'];
				$semester = $this->request->data['PublishedCourse']['semester'];
				
				// $sections = $selected_section;
				$this->set(compact('show_publish_page'));
				$this->set('turn_off_search', true);

				$this->set(compact(
					'sections',
					'year_level_id',
					'program_id',
					'program_type_id',
					'academic_year',
					'semester',
					'selected_section'
				));

			} else {

				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.department_id' => $this->department_id,
						'Section.year_level_id' => $this->request->data['PublishedCourse']['year_level_id']
					)
				));


				$section_array_list = $this->__section_curriculum($sections, true);
				$sections = $section_array_list;
				$year_level_id = $this->request->data['PublishedCourse']['year_level_id'];
				$academic_year = $this->request->data['PublishedCourse']['academic_year'];
				$program_id = $this->request->data['PublishedCourse']['program_id'];
				$semester = $this->request->data['PublishedCourse']['semester'];
				$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
				
				$this->set('turn_off_search', true);
				
				$this->set(compact(
					'year_level_id',
					'academic_year',
					'program_id',
					'program_type_id',
					'semester'
				));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['publishselectedasadd'])) {

			unset($this->request->data['PublishedCourse']['section_id']);
			$this->request->data['PublishedCourse']['department_id'] = $this->department_id;

			$save_reformated_published_courses = array();
			$courses_ids = array();
			$section_ids = array();

			$count = 0;

			if (!empty($this->request->data['Course'])) {
				foreach ($this->request->data['Course'] as $section_id => $courses) {

					$pcourses_count = $this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.section_id' => $section_id, 'PublishedCourse.academic_year' => (isset($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $this->request->data['PublishedCourse']['academicyear']), 'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'])));
					debug($pcourses_count);
					
					$section_ids[] = $section_id;
					foreach ($courses as $cid => $is_selected) {
						if ($is_selected != 0) {
							if ($pcourses_count) {
								$save_reformated_published_courses['PublishedCourse'][$count] = $this->request->data['PublishedCourse'];
								$save_reformated_published_courses['PublishedCourse'][$count]['published'] = 1;
								$save_reformated_published_courses['PublishedCourse'][$count]['course_id'] = $cid;
								$save_reformated_published_courses['PublishedCourse'][$count]['section_id'] = $section_id;
								$save_reformated_published_courses['PublishedCourse'][$count]['given_by_department_id'] = $this->department_id;
								$save_reformated_published_courses['PublishedCourse'][$count]['add'] = 1;
								$courses_ids[] = $cid;
								$count++;
							} else {
								if (empty($no_previous_published_courses_error)) {
									$no_previous_published_courses_error = $this->PublishedCourse->Section->field('name', array('Section.id' => $section_id)) . ' section doesn\'t have any previous course publication for ' . (isset($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $this->request->data['PublishedCourse']['academicyear']) . ' semster ' . $this->request->data['PublishedCourse']['semester'] . ', please use Publish Courses instead.';
								}
							}
						}
					}
				}
			}


			if ($count == 0 || !empty($no_previous_published_courses_error)) {
				if (!empty($no_previous_published_courses_error)) {
					$this->Flash->error($no_previous_published_courses_error);
					$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year'])));
				} else {
					$this->Flash->error(__('Please select atleast one course you want to publish as an add .'));
				}
			}

			$check_courses_published = $this->PublishedCourse->find('count', array(
				'conditions' => array(
					'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year'], 
					'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
					'PublishedCourse.course_id' => $courses_ids, 'PublishedCourse.section_id' => $section_ids, 
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'], 
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.department_id' => $this->department_id
				))
			);

			$published_courses = $this->PublishedCourse->find('list', array(
					'conditions' => array(
						'PublishedCourse.academic_year like' => $this->request->data['PublishedCourse']['academic_year'] . '%',
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.section_id' => $section_ids,
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.department_id' => $this->department_id
					),
					'fields' => 'PublishedCourse.course_id'
				)
			);

			// unset those courses which has already published and publish only coures not published

			/* if (!empty($published_courses)) {
				foreach ($published_courses as $pk => $pv) {
					//check and unset those already published courses
					if (isset($save_reformated_published_courses['PublishedCourse']) && !empty($save_reformated_published_courses['PublishedCourse'])) {
						foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => &$sv) {
							if ($sv['course_id'] == $pv) {
								unset($save_reformated_published_courses['PublishedCourse'][$sk]);
							}
						}
					}
				}
			} */

			if (!empty($save_reformated_published_courses['PublishedCourse'])) {
				foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => $sv) {
					$pc_course_exists = $this->PublishedCourse->find('count', array(
						'conditions' => array(
							'PublishedCourse.academic_year' => $sv['academic_year'],
							'PublishedCourse.semester' => $sv['semester'],
							'PublishedCourse.section_id' => $sv['section_id'],
							'PublishedCourse.course_id' => $sv['course_id'],
							//'PublishedCourse.published' => $sv['published'],
						)
					));
					//debug($pc_course_exists);
					if ($pc_course_exists) {
						unset($save_reformated_published_courses['PublishedCourse'][$sk]);
					}
				}
			}

			// check if all courses has already published and redirect the user to the published course page.
			if ($check_courses_published == count($courses_ids)) {
				$this->Flash->info( __('The selected courses are already published for the selected section'));

				$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
				$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
			}

			$count_already_published = count($published_courses);
			$count_ready_published = count($save_reformated_published_courses);

			if (!empty($save_reformated_published_courses['PublishedCourse'])) {
				if ($this->PublishedCourse->saveAll($save_reformated_published_courses['PublishedCourse'], array('validate' => 'first'))) {
					if ($count_already_published == 0) {
						$this->Flash->success(__('The course has been published for registration.'));
					} else {
						$this->Flash->success( __('Among selected courses, ' . $count_ready_published . ' course(s) published as mass add, ' . $count_already_published . ' courses were already published previously for the section.'));
					}

					$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);

					$this->Session->write('search_data_published_course', $this->request->data['PublishedCourse']);

					$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
				} else {
					$this->Flash->error( __('The published course could not be saved. Please, try again.'));
				}
			} else {
				$this->Flash->error( __('No new course is found to publish or update form the courses you selected.'));
				$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
				$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['publishselected'])) {

			unset($this->request->data['PublishedCourse']['section_id']);
			$this->request->data['PublishedCourse']['department_id'] = $this->department_id;
			$save_reformated_published_courses = array();
			$courses_ids = array();
			$section_ids = array();
			$upgrade_downgrade_section = 0;
			$count = 0;

			/////////////////////////////////////////////////////////////////////
			debug($this->request->data);

			if (!empty($this->request->data['Course'])) {
				foreach ($this->request->data['Course'] as $section_id => $courses) {
					$section_ids[] = $section_id;
					foreach ($courses as $cid => $is_selected) {
						if ($is_selected != 0) {
							$save_reformated_published_courses['PublishedCourse'][$count] = $this->request->data['PublishedCourse'];
							$save_reformated_published_courses['PublishedCourse'][$count]['published'] = 1;
							$save_reformated_published_courses['PublishedCourse'][$count]['course_id'] = $cid;
							$save_reformated_published_courses['PublishedCourse'][$count]['section_id'] = $section_id;
							$save_reformated_published_courses['PublishedCourse'][$count]['given_by_department_id'] = $this->department_id;
							$save_reformated_published_courses['PublishedCourse'][$count]['elective'] = $this->request->data['Elective'][$section_id][$cid];
							$courses_ids[] = $cid;
							$count++;
						}
					}
				}
			}

			//$selected_sections_publishe=array();
			if ($this->role_id == ROLE_DEPARTMENT) {

				if (isset($this->request->data['Section']['selected']) && !empty($this->request->data['Section']['selected'])) {
					foreach ($this->request->data['Section']['selected'] as $sec_id => $sec_value) {
						if ($sec_value != 0) {
							$check_if_course_is_published_for_given_academic_year_semester = $this->PublishedCourse->find('count', array(
								'conditions' => array(
									'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year'],
									'PublishedCourse.department_id' => $this->department_id,
									'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'], 'PublishedCourse.section_id' => $sec_id,
									'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
									'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
									'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id']
								)
							));

							if ($check_if_course_is_published_for_given_academic_year_semester == 0) {

								$prev_semester_academic_year = $this->PublishedCourse->CourseRegistration->Student->StudentExamStatus->getPreviousSemester($this->request->data['PublishedCourse']['academic_year'], $this->request->data['PublishedCourse']['semester']);

								$check_previous_semester_published = $this->PublishedCourse->previous_semester_and_academic_course_published(
									$this->request->data['PublishedCourse']['semester'],
									$this->request->data['PublishedCourse']['academic_year'],
									$this->department_id,
									$this->request->data['PublishedCourse']['program_id'],
									$this->request->data['PublishedCourse']['program_type_id'],
									$this->request->data['PublishedCourse']['year_level_id'],
									$sec_id
								);

								if (!$check_previous_semester_published) {
									$upgrade_downgrade_section = $sec_value;
									break;
								}
							}
						}
					}
				}
			}

			///////////////////////////////////////////////////////////////////////
			debug($upgrade_downgrade_section);

			if ($count == 0 ||  $upgrade_downgrade_section > 0) {

				$year_level_id = $this->request->data['PublishedCourse']['year_level_id'];
				$program_id = $this->request->data['PublishedCourse']['program_id'];
				$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
				$academic_year = $this->request->data['PublishedCourse']['academic_year'];
				$semester = $this->request->data['PublishedCourse']['semester'];

				$this->set('turn_off_search', true);

				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.department_id' => $this->department_id,
						'Section.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
						'Section.id' => $this->request->data['Section']['selected']
					))
				);

				$selectedsection = $this->request->data['Section']['selected'];
				$sections = $this->__section_curriculum($sections, true);


				if ($count == 0) {
					$this->Flash->error( __('Please select at least one course you want to publish.'));
				} else {

					$prev_semester_academic_year = $this->PublishedCourse->CourseRegistration->Student->StudentExamStatus->getPreviousSemester($this->request->data['PublishedCourse']['academic_year'], $this->request->data['PublishedCourse']['semester']);

					$this->Session->setFlash(
						'<span></span>' . __('You can not publish semester ' . $this->request->data['PublishedCourse']['semester'] . ' of ' . $this->request->data['PublishedCourse']['academic_year'] . ' before publishing semester ' . $prev_semester_academic_year['semester'] . ' of ' . $prev_semester_academic_year['academic_year'] . ' for ' . $sections[$upgrade_downgrade_section] . '', true),
						"session_flash_link",
						array(
							"class" => 'error-box error-message',
							"link_text" => " Upgrade or downgrade section.",
							"link_url" => array(
								"controller" => "sections",
								"action" => "upgrade_sections",
								"admin" => false
							)
						)
					);
				}

				$this->set(compact(
					'sections',
					'year_level_id',
					'program_id',
					'program_type_id',
					'curriculum_id',
					'academic_year',
					'section_map_curriculum',
					'semester',
					'selectedsection'
				));

				//$this->render('selectedPublishedCourses');
			} else {

				$selected_sections_ids = array();
				//debug($this->request->data['Section']['selected']);

				if (isset($this->request->data['Section']['selected']) && !empty($this->request->data['Section']['selected'])) {
					foreach ($this->request->data['Section']['selected'] as $se_index => $se_value) {
						if ($se_value != 0) {
							$selected_sections_ids[] = $se_index;
						}
					}
				}

				$check_courses_published = $this->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year'], 
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.course_id' => $courses_ids,
						'PublishedCourse.section_id' => $selected_sections_ids,
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.department_id' => $this->department_id
					)
				));

				// dont allow publication for semester if students has started registration

				if (!empty($selected_sections_ids)) {
					$list_courses_published_ids = $this->PublishedCourse->find('list', array(
						'conditions' => array(
							'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
							'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
							'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
							'PublishedCourse.section_id' => $selected_sections_ids,
							'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
							'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
							'PublishedCourse.department_id' => $this->department_id
						), 
						'fields' => 'id'
					));
				}


				if (isset($list_courses_published_ids) && !empty($list_courses_published_ids)) {
					$check_registration_started = $this->PublishedCourse->CourseRegistration->find('count', array('conditions' => array('CourseRegistration.published_course_id' => $list_courses_published_ids)));
					if ($check_registration_started == 0) {
						$check_registration_started = $this->PublishedCourse->CourseAdd->find('count', array('conditions' => array('CourseAdd.published_course_id' => $list_courses_published_ids)));
					}
				} else {
					$check_registration_started = 0;
				}

				if ($check_registration_started == 0) {

					///////////////////////////////////////////////////////////////////

					$published_courses = $this->PublishedCourse->find('list', array(
						'conditions' => array(
							'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
							'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
							'PublishedCourse.section_id' => $section_ids,
							'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
							'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
							'PublishedCourse.department_id' => $this->department_id
						),
						'fields' => 'PublishedCourse.course_id'
					));

					if (!empty($save_reformated_published_courses['PublishedCourse'])) {
						foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => $sv) {
							$pc_course_exists = $this->PublishedCourse->find('count', array(
								'conditions' => array(
									'PublishedCourse.academic_year' => $sv['academic_year'],
									'PublishedCourse.semester' => $sv['semester'],
									'PublishedCourse.section_id' => $sv['section_id'],
									'PublishedCourse.course_id' => $sv['course_id'],
									//'PublishedCourse.published' => $sv['published'],
								)
							));
							//debug($pc_course_exists);
							if ($pc_course_exists) {
								unset($save_reformated_published_courses['PublishedCourse'][$sk]);
							}
						}
					}


					if (!empty($save_reformated_published_courses['PublishedCourse'])) {
						if ($this->PublishedCourse->saveAll($save_reformated_published_courses['PublishedCourse'], array('validate' => 'first'))) {
							$this->Flash->success( __(count($save_reformated_published_courses['PublishedCourse']) . ' ' . (count($save_reformated_published_courses['PublishedCourse']) > 1 ? 'courses have' : 'course has') . ' been published for registration for ' . $this->request->data['PublishedCourse']['academic_year'] . ' academic year, semester: ' . $this->request->data['PublishedCourse']['semester'] . '.'));
							
							$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
							$this->Session->write('search_data_published_course', $this->request->data['PublishedCourse']);
							$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
						} else {
							$this->Flash->error( __('The published course could not be saved. Please, try again.'));
						}
					} else {
						$this->Flash->error( __('No new course is found to publish or update form the courses you selected.'));
						$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
						$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
					}
				} else {
					$this->Flash->error( __('You can not publish semester courses since registration has already started, but you can publish course as mass add.'));
					$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
					$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
				}
			}

			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$publishedcourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.department_id' => $this->department_id, 
					'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'], 
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'], 
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year'],
					'PublishedCourse.published' => 1
				)
			));

			$this->set('turn_off_search', true);
			$this->set('show_unpublish_page', true);
			$this->set('publishedcourses', $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, $this->department_id, $publishedcourses));
		}


		$yearLevels = array();
		
		$yearLevelsInCurriculum = $this->PublishedCourse->Course->find('list', array('conditions' => array('Course.department_id' => $this->department_id), 'fields' => array('Course.year_level_id', 'Course.year_level_id'), 'group' => array('Course.year_level_id')));

		if (!empty($yearLevelsInCurriculum)) {
			$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.id' => $yearLevelsInCurriculum)));
		} else if (isset($this->department_id) && !empty($this->department_id)) {
			$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
		}

		//$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
		$programTypes = $this->PublishedCourse->ProgramType->find('list');
		$programs = $this->PublishedCourse->Program->find('list');

		$this->set(compact('yearLevels', 'courses', 'programTypes', 'colleges', 'programs', 'departments', 'sections'));
	}

	public function unpublish()
	{

		if (isset($this->request->data['getsection'])) {
			if ($this->Session->check('search_data_published_course')) {
				$this->Session->delete('search_data_published_course');
				$this->__init_search();
			}
		}

		if ($this->Session->check('search_data_published_course')) {
			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');
		}

		if (!empty($this->request->data) && isset($this->request->data['getsection'])) {

			$everythingfine = false;
			switch ($this->request->data) {
				case empty($this->request->data['PublishedCourse']['academic_year']):
					$this->Flash->error(__('Please select the academic year you want to unpublish or publish as a drop course.'));
					break;
				case empty($this->request->data['PublishedCourse']['year_level_id']):
					$this->Flash->error( __('Please select the year level you want to unpublish or publish as a drop course.'));
					break;
				case empty($this->request->data['PublishedCourse']['semester']):
					$this->Flash->error( __('Please select the semester you want to unpublish or publish as a drop course.'));
					break;
				case empty($this->request->data['PublishedCourse']['program_id']):
					$this->Flash->error( __('Please select the program you want to unpublish or publish as a drop course..'));
					break;
				case empty($this->request->data['PublishedCourse']['program_type_id']):
					$this->Flash->error( __('Please select the program type you want to unpublish or publish as a drop course.'));
					break;
				default:
					$everythingfine = true;
			}

			if ($everythingfine) {
				// Function to load/save search criteria.
				$this->__init_search();

				$publishedcourses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.department_id' => $this->department_id,
						'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.academic_year like ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
						'PublishedCourse.published' => 1
					), 
					'contain' => array(
						'Course' => array(
							'fields' => array('id', 'course_title', 'course_code', 'semester', 'credit', 'lecture_hours', 'laboratory_hours', 'tutorial_hours', 'course_detail_hours'), 
							'YearLevel' => array('id', 'name'),
							'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
						), 
						'Section' => array(
							'fields' => array('id', 'name', 'academicyear'),
							'YearLevel' => array('id', 'name'), 
							'Program' => array('id', 'name'),
							'ProgramType' => array('id', 'name'),
							'Department' => array('id', 'name', 'college_id', 'type'),
							'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
							'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
						), 
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'YearLevel' => array('id', 'name'),
						'Department' => array('id', 'name', 'college_id', 'type'),
						'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
					)
				));

				if (empty($publishedcourses)) {
					$this->Flash->info( __('There is no published course in the given criteria, please select different criteria.'));
					// exit;
				} else {

					$this->set('turn_off_search', true);
					$this->set('show_unpublish_page', true);

					$year_level_id = $this->request->data['PublishedCourse']['year_level_id'];
					$program_id = $this->request->data['PublishedCourse']['program_id'];
					$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];

					$this->set('publishedcourses', $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, $this->department_id, $publishedcourses));
					$this->set(compact('year_level_id', 'program_id', 'program_type_id'));
				}
			} else {
				//$this->redirect(array('action'=>'add'));
			}
		}

		//delete publisehd courses
		if (!empty($this->request->data) && isset($this->request->data['deleteselected'])) {

			$count = 0;
			$courses_ids = array();
			$section_ids = array();

			if (!empty($this->request->data['Course']['pub'])) {
				foreach ($this->request->data['Course']['pub'] as $section_id => $selected_courses) {
					$section_ids[] = $section_id;
					foreach ($selected_courses as $course_id => $selected_flag) {
						if ($selected_flag == 1) {
							$count++;
							$courses_ids[$section_id][] = $course_id;
						}
					}
				}
			}

			if ($count == 0) {
				$this->Flash->error( __('Please select atleast one published course you want to unbublish/delete.'));
			} else {

				$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');
				$courses_not_allowed = array();
				$courses_allowed_to_delete = array();

				if (!empty($section_ids)) {
					foreach ($section_ids as $kk => $sv) {
						if (isset($courses_ids[$sv])) {
							$publishedcourses = $this->PublishedCourse->find('all', array(
								'conditions' => array(
									'PublishedCourse.department_id' => $this->department_id,
									'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
									'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
									'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
									'PublishedCourse.course_id' => $courses_ids[$sv],
									'PublishedCourse.section_id' => $sv,
									'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
									'PublishedCourse.academic_year like ' => $this->request->data['PublishedCourse']['academic_year'] . '%'
								),
								'contain' => array(
									'CourseRegistration' => array(
										'Student' => array(
											'fields' => array(
												'Student.full_name',
											)
										)
									)
								)
							));

							if (!empty($publishedcourses)) {
								foreach ($publishedcourses as $pk => $pv) {
									if (count($pv['CourseRegistration']) == 0) {
										$courses_allowed_to_delete[$sv][$pk] = $pv['PublishedCourse']['id'];
										//$courses_allowed_to_delete[$pk]['course_id']=$pv['PublishedCourse']['course_id'];
									} else {
										$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['id'];
										$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['course_id'];
									}
								}
							}
						}
					}
				}
				//iterate section by section

				/* if (count($courses_not_allowed) > 0) {
					$this->Flash->error(__('You can not delete the red marked published course(s), students already registered for them. Please uncheck the red marked onees.'));
					$this->set(compact('courses_not_allowed'));
				} else { */

				if (count($courses_allowed_to_delete) > 0) {
					//$count_deleted_record=count($courses_allowed_to_delete);

					$count_delete_record = array();
					
					if (!empty($section_ids)) {
						foreach ($section_ids as $key => $section_id) {
							if (isset($courses_allowed_to_delete[$section_id])) {
								if ($this->PublishedCourse->deleteAll(array('PublishedCourse.id' => $courses_allowed_to_delete[$section_id]), false)) {
									$count_delete_record[$section_id] = count($courses_allowed_to_delete[$section_id]);
								}
							}
						}
					}

					debug($count_delete_record);

					if (!empty($count_delete_record)) {
						$sum = 0;
						
						foreach ($count_delete_record as $sec_id => $del_count) {
							$sum += $del_count;
						}

						if ($sum > 0) {
							$this->Flash->success(__('From the selected published course(s), you have unpublished/deleted ' . $sum . '  published course(s).'));
						} else {
							$this->Flash->error( __('Published course was not unpublished/deleted.Please try again.'));
						}
					}
				} else if (count($courses_not_allowed) > 0) {
					$this->Flash->error(__('You can not delete the red marked published course(s), students already registered for them. Please uncheck the red marked onees.'));
					$this->set(compact('courses_not_allowed'));
				} 
				//}
			}

			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$publishedcourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.department_id' => $this->department_id,
					'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year'],
					'PublishedCourse.published' => 1
				), 
				'contain' => array(
					'Course' => array(
						'fields' => array('id', 'course_title', 'course_code', 'semester', 'credit', 'lecture_hours', 'tutorial_hours', 'course_detail_hours'), 
						'YearLevel' => array('id', 'name'),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					), 
					'Section' => array(
						'fields' => array('id', 'name', 'academicyear'),
						'YearLevel' => array('id', 'name'), 
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'Department' => array('id', 'name', 'college_id', 'type'),
						'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					), 
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'Department' => array('id', 'name', 'college_id', 'type'),
					'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
				)
			));

			$this->set('turn_off_search', true);
			$this->set('show_unpublish_page', true);
			$this->set('publishedcourses', $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, $this->department_id, $publishedcourses));

		}

		//publish as drop coures drop publisehd courses
		if (!empty($this->request->data) && isset($this->request->data['dropselected'])) {

			$count = 0;
			$courses_ids = array();
			$section_ids = array();

			if (!empty($this->request->data['Course']['pub'])) {
				foreach ($this->request->data['Course']['pub'] as $section_id => $selected_courses) {
					$section_ids[] = $section_id;
					foreach ($selected_courses as $course_id => $selected_flag) {
						if ($selected_flag == 1) {

							$count++;
							$courses_ids[$section_id][] = $course_id;
						}
					}
				}
			}

			if ($count == 0) {
				$this->Flash->error(__('Please select atleast one published course you want to publish as mass drop.'));
			} else {
				$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

				$courses_not_allowed = array();
				$courses_allowed_to_drop = array();

				if (!empty($section_ids)) {
					foreach ($section_ids as $kk => $sv) {
						if (isset($courses_ids[$sv])) {
							$publishedcourses = $this->PublishedCourse->find('all', array(
								'conditions' => array(
									'PublishedCourse.department_id' => $this->department_id,
									'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
									'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
									'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
									'PublishedCourse.published' => 1,
									'PublishedCourse.course_id' => $courses_ids[$sv],
									'PublishedCourse.section_id' => $sv,
									'PublishedCourse.drop' => 0,
									'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
									'PublishedCourse.academic_year like ' => $this->request->data['PublishedCourse']['academic_year'] . '%'
								),
								'contain' => array(
									'CourseRegistration' => array(
										'Student' => array(
											'fields' => array(
												'Student.full_name',
											)
										)
									)
								)
							));

							if (!empty($publishedcourses)) {
								foreach ($publishedcourses as $pk => $pv) {
									if (count($pv['CourseRegistration']) > 0) {
										$courses_allowed_to_drop[$sv][$pk] = $pv['PublishedCourse']['id'];
										//$courses_allowed_to_delete[$pk]['course_id']=$pv['PublishedCourse']['course_id'];
									} else {
										$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['id'];
										$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['course_id'];
									}
								}
							}
						}
					}
				}

				if (count($courses_not_allowed) > 0) {
					$this->Flash->error(__('There is no registration for the red marked course(s) so you can delete it permanently using "Delete Selected" option. you can also communicate the registrar or student to drop the selected courses.'));
					$this->set(compact('courses_not_allowed'));
				} else {

					// reformat courses_allowed to unpublished
					$save_reformat_drop_published_courses = array();

					if (!empty($section_ids)) {
						$count_drop = 0;
						foreach ($section_ids as $section_index => $section_id) {
							if (isset($courses_allowed_to_drop[$section_id])) {
								foreach ($courses_allowed_to_drop[$section_id] as $index => $course_id) {
									$save_reformat_drop_published_courses['PublishedCourse'][$count_drop]['id'] = $course_id;
									$save_reformat_drop_published_courses['PublishedCourse'][$count_drop]['published'] = 1;
									$save_reformat_drop_published_courses['PublishedCourse'][$count_drop]['drop'] = 1;
									$count_drop++;
								}
							}
						}

						//save the drop published coureses
						if (isset($save_reformat_drop_published_courses['PublishedCourse']) && count($save_reformat_drop_published_courses['PublishedCourse']) > 0) {
							if ($this->PublishedCourse->saveAll($save_reformat_drop_published_courses['PublishedCourse'], array('validate' => 'first'))) {
								$this->Flash->success(__('Among selected courses, ' . $count_drop . ' has been published as drop course.'));
								//$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
								//$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'],$academic_year));
								$this->Session->write('search_data_published_course', $this->request->data['PublishedCourse']);
								$this->redirect(array('action' => 'index'));
							} else {
								$this->Flash->error( __('Could not publish the selected published course(s) as mass drop. Please, try again.'));
							}
						} else {
							$this->Flash->error(__('Internal Error occured while trying to publish the selected published course(s) as mass drop.'));
						}
					}
				}
			}

			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$publishedcourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.department_id' => $this->department_id,
					'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
					'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year'],
					'PublishedCourse.published' => 1, 
					'PublishedCourse.drop' => 0
				), 
				'contain' => array(
					'Course' => array(
						'fields' => array('id', 'course_title', 'course_code', 'semester', 'credit', 'lecture_hours', 'tutorial_hours', 'course_detail_hours'), 
						'YearLevel' => array('id', 'name'),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					), 
					'Section' => array(
						'fields' => array('id', 'name', 'academicyear'),
						'YearLevel' => array('id', 'name'), 
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'Department' => array('id', 'name', 'college_id', 'type'),
						'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					), 
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'Department' => array('id', 'name', 'college_id', 'type'),
					'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
				)
			));

			$section_organized_published_courses = $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, $this->department_id, $publishedcourses);

			$this->set('turn_off_search', true);
			$this->set('show_unpublish_page', true);
			$this->set('publishedcourses', $section_organized_published_courses);
		}

		//unpublished coures
		if (!empty($this->request->data) && isset($this->request->data['unpublishselected'])) {

			$count = 0;
			$courses_ids = array();

			if (!empty($this->request->data['Course']['pub'])) {
				foreach ($this->request->data['Course']['pub'] as $section_id => $selected_courses) {
					foreach ($selected_courses as $course_id => $course_selected_flag) {
						if ($course_selected_flag == 1) {
							$count++;
							$courses_ids[$section_id][] = $course_id;
						}
					}
				}
			}

			if ($count == 0) {
				$this->Flash->error(__('Please select atleast one published course you want to unpublish.'));
			} else {

				$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');
				
				$publishedcourses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.department_id' => $this->department_id,
						'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.published' => 1, 
						'PublishedCourse.course_id' => $courses_ids,
						'PublishedCourse.academic_year' => $this->request->data['PublishedCourse']['academic_year']
					),
					'contain' => array(
						'Course' => array(
							'Student' => array(
								'fields' => array(
									'Student.full_name',
								)
							)
						)
					)
				));

				$courses_not_allowed = array();
				$courses_allowed_to_unpublished = array();

				if (!empty($publishedcourses)) {
					foreach ($publishedcourses as $pk => $pv) {
						if (count($pv['Course']['Student']) == 0) {
							$courses_allowed_to_unpublished[$pk]['id'] = $pv['PublishedCourse']['id'];
							$courses_allowed_to_unpublished[$pk]['course_id'] = $pv['PublishedCourse']['course_id'];
						} else {
							$courses_not_allowed[$pk] = $pv['PublishedCourse']['id'];
							$courses_not_allowed[$pk] = $pv['PublishedCourse']['course_id'];
						}
					}
				}

				if (count($courses_not_allowed) > 0) {
					$this->Flash->error(__('You can not unpublish the red marked courses, students have already registered for them. Please uncheck the red marked courses.'));
					$this->set(compact('courses_not_allowed'));
				} else {
					// reformat courses allowed to be unpublished
					$save_reformat_unpublished_courses = array();

					if (!empty($courses_allowed_to_unpublished)) {
						foreach ($courses_allowed_to_unpublished as $pk => $pv) {
							$save_reformat_unpublished_courses['PublishedCourse'][$pk]['id'] = $pv['id'];
							$save_reformat_unpublished_courses['PublishedCourse'][$pk]['course_id'] = $pv['course_id'];
							$save_reformat_unpublished_courses['PublishedCourse'][$pk]['published'] = 0;
						}
					}

					//save the unpublished courses

					if ($this->PublishedCourse->saveAll($save_reformat_unpublished_courses['PublishedCourse'], array('validate' => 'first'))) {
						$this->Flash->success(__('Unpublished the selected published course(s) successfully.'));
						//$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
						//$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
						$this->Session->write('search_data_published_course', $this->request->data['PublishedCourse']);
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Flash->error( __('Could not unpublish the selected published course(s). Please, try again.'));
					}
				}
			}

			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$publishedcourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.department_id' => $this->department_id,
					'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id']
				), 
				'contain' => array(
					'Course' => array(
						'fields' => array('id', 'course_title', 'course_code', 'semester', 'credit', 'lecture_hours', 'tutorial_hours', 'course_detail_hours'), 
						'YearLevel' => array('id', 'name'),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					), 
					'Section' => array(
						'fields' => array('id', 'name', 'academicyear'),
						'YearLevel' => array('id', 'name'), 
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'Department' => array('id', 'name', 'college_id', 'type'),
						'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
						'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
					), 
					'Program' => array('id', 'name'),
					'ProgramType' => array('id', 'name'),
					'YearLevel' => array('id', 'name'),
					'Department' => array('id', 'name', 'college_id', 'type'),
					'College'=> array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
				)
			));

			$this->set('turn_off_search', true);
			$this->set('show_unpublish_page', true);
			$this->set('publishedcourses', $publishedcourses);
		}


		$yearLevels = array();

		$yearLevelsInCurriculum = $this->PublishedCourse->Course->find('list', array('conditions' => array('Course.department_id' => $this->department_id), 'fields' => array('Course.year_level_id', 'Course.year_level_id'), 'group' => array('Course.year_level_id')));

		if (!empty($yearLevelsInCurriculum)) {
			$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.id' => $yearLevelsInCurriculum)));
		} else if (isset($this->department_id) && !empty($this->department_id)) {
			$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
		}

		//$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
		$curriculums = $this->PublishedCourse->Course->Curriculum->find('list', array('fields' => array('Curriculum.curriculum_detail')));

		//$programTypes = $this->PublishedCourse->ProgramType->find('list');
		//$programs = $this->PublishedCourse->Program->find('list');

		$programs =  $this->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes = $this->PublishedCourse->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));


		if (!empty($this->request->data['PublishedCourse']['academic_year'])) {
			$academic_year = $this->request->data['PublishedCourse']['academic_year'];
			$this->set(compact('academic_year'));
		}

		$this->set(compact('yearLevels', 'programTypes', 'programs', 'departments'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error( __('Invalid id for published course'));
			return $this->redirect(array('action' => 'index'));
		}

		$is_publish_belongs_to_department = $this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.id' => $id, 'PublishedCourse.department_id' => $this->department_id)));

		if ($is_publish_belongs_to_department > 0) {
			$delete_allowed = $this->PublishedCourse->canItBeDeleted($id);
			if ($delete_allowed) {
				if ($this->PublishedCourse->delete($id)) {
					$this->Flash->success( __('Published course deleted'));
					$this->render('selected_published_courses');
				}
			}
		} else {
			$this->Flash->error(__('You are not  elegible to delete this course.'));
			$this->render('selected_published_courses');
		}

		$this->Flash->error(__('Published course was not deleted.'));
		$this->render('selected_published_courses');
	}
	
	function print_published_pdf()
	{
		$publishedCourses = $this->Session->read('publishedCourses');
		$selected_academic_year = $this->Session->read('selected_academic_year');

		$this->set(compact('publishedCourses', 'selected_academic_year'));
		$this->layout = 'pdf';
		$this->render();
	}
	/**
	 * export to xls
	 */
	function export_published_xls()
	{
		$publishedCourses = $this->Session->read('publishedCourses');
		$selected_academic_year = $this->Session->read('selected_academic_year');
		$this->set(compact('publishedCourses', 'selected_academic_year'));
	}

	function __init_search()
	{
		if (!empty($this->request->data['PublishedCourse'])) {
			$this->Session->write('search_data_published_course', $this->request->data['PublishedCourse']);
		} else if ($this->Session->check('search_data_published_course')) {
			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');
		}
	}

	function __section_curriculum($sections = array(), $flag = null)
	{
		$array_list_sections = array();
		$section_array_list = array();
		$section_map_curriculum = array();

		debug($sections);

		if (!empty($sections)) {
			foreach ($sections as $sk => $sv) {
				$array_list_sections = $this->PublishedCourse->Section->find('all', array(
					'conditions' => array(
						'Section.id' => $sk
					),
					'fields' => array('Section.id', 'Section.name'),
					'contain' => array(
						'Student' => array(
							'fields' => array('Student.id', 'Student.curriculum_id', 'Student.full_name', 'Student.academicyear'),
							'order' => array('Student.academicyear' => 'DESC'), // to get the latest section Student Attached Curricullum first, Not readmitted ones, if there is one, Neway
							'Section',
							'Curriculum' => array(
								'fields' => array(
									'Curriculum.curriculum_detail',
									'registrar_approved',
									'active'
								)
							)
						)
					)
				));

				//debug($array_list_sections);
				$curriculum_mapped = false;
				$found_curriculum = false;

				if (!empty($array_list_sections)) {
					foreach ($array_list_sections as $as => $av) {
						if (count($av['Student']) > 0) {
							foreach ($av['Student'] as $studentv) {
								if (isset($studentv['curriculum_id']) && !empty($studentv['curriculum_id']) && !$curriculum_mapped) {
									$section_map_curriculum[$sk] = $studentv['curriculum_id'];
									$section_array_list[$sk] = 'Section: ' . $sv . ': attached to: (' . $studentv['Curriculum']['curriculum_detail'] . ') curriculum' . ($studentv['Curriculum']['registrar_approved'] == 0 ? ' &nbsp;(<span class="text-red">Curriculum Not Approved</span>).' : '.');
									$curriculum_mapped = true;
									break 1;
								}
							}
						}
					}
				}
			}
		}

		if ($flag) {
			return $section_array_list;
		} else {
			return $section_map_curriculum;
		}
	}

	function getPublishedCourses($data = null)
	{
		$this->layout = 'ajax';
		$selected_sections = array();
		$interesected = array();
		$is_list_of_course_execute = 0;
		/// give the user the list of courses which is already displayed from the session when validation error occur.
		
		if ($this->Session->read('list_of_courses')) {
			if ($data == 2) {
				$is_list_of_course_execute = 1;
				$interesected = $this->Session->read('list_of_courses');
			} else {
				$this->Session->delete('list_of_courses');
			}
		}


		if (!empty($this->request->data['Section'])) {
			foreach ($this->request->data['Section']['selected'] as $key => $selected_section_id) {
				if ($selected_section_id != 0) {
					
					$is_list_of_course_execute = 1;
					
					$list_of_course = $this->PublishedCourse->find('all', array(
						'conditions' => array(
							'PublishedCourse.program_id' => $this->request->data['MergedSectionsCourse']['program_id'],
							'PublishedCourse.semester' => $this->request->data['MergedSectionsCourse']['semester'],
							'PublishedCourse.program_type_id' => $this->request->data['MergedSectionsCourse']['program_type_id'],
							'PublishedCourse.year_level_id' => $this->request->data['MergedSectionsCourse']['year_level_id'],
							'PublishedCourse.academic_year LIKE ' => $this->AcademicYear->current_academicyear() . '%',
							'PublishedCourse.section_id' => $selected_section_id
						),
						'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'),
						'contain' => array(
							'Course' => array(
								'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.credit', 'Course.elective')
							)
						)
					));

					if (empty($interesected)) {
						$interesected = $list_of_course;
					} else {
						foreach ($list_of_course as $course) {
							if (isset($interesected) && !empty($interesected)) {
								foreach ($interesected as $read) {
									if ($read['Course']['id'] == $course['Course']['id']) {
										$selected_sections[] = $course;
									}
								}
							}
						}

						$interesected = $selected_sections;
						$selected_sections = array();
					}
				}
			}
		}

		$list_of_courses = $interesected;

		if ($is_list_of_course_execute == 0) {
			$list_of_courses = null;
		}

		$this->Session->Write('list_of_courses', $list_of_courses);
	}

	// Get list of candidate publish courses 

	function selectedPublishedCourses($data = null)
	{
		$this->layout = 'ajax';

		$selected_section = array();
		$ready_for_publishing_courses = array();
		// incase all students fail or something happens
		$taken_courses_allow_to_publishe_it = array();

		$section_already_taken_courses = array();
		$selected_students_ids = array();
		$max_courses_taken_count = 0;
		$max_courses_taken_index_student = 0;

		/// give the user the list of courses which is already displayed from the session when validation error occur.
		if ($this->Session->read('candidate_publish_courses')) {
			if ($data == 2) {
				$ready_for_publishing_courses = $this->Session->read('candidate_publish_courses');
				$taken_courses_allow_to_publishe_it = $this->Session->read('taken_courses_allow_to_publishe_it');
				$published_courses_disable_not_to_published = $this->Session->read('published_courses_disable_not_to_published');
				$selected_section = $this->Session->read('selected_section');
			} else {
				$this->Session->delete('candidate_publish_courses');
				$this->Session->delete('taken_courses_allow_to_publishe_it');
				$this->Session->delete('selected_section');
				$this->Session->delete('published_courses_disable_not_to_published');
			}
		}

		if (!empty($this->request->data['Section'])) {
			//debug($this->request->data);
			$sections = $this->PublishedCourse->Section->find('list', array(
				'conditions' => array(
					'Section.department_id' => $this->department_id,
					'Section.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
					'Section.archive' => 0
				)
			));

			if (isset($this->request->data['Section']['selected']) && !empty($this->request->data['Section']['selected'])) {
				foreach ($this->request->data['Section']['selected'] as $section_id => $selecte_flag) {
					if ($selecte_flag != 0) {
						$selected_section[$selecte_flag] = $sections[$selecte_flag];
					}
				}
			}
			debug($selected_section);

			// curriculum of selected section
			$section_curriculum_attachment = $this->__section_curriculum($selected_section, false);

			$sections = $this->__section_curriculum($sections, true);

			//find the already taken courses of the section

			if (isset($this->request->data['Section']['selected']) && !empty($this->request->data['Section']['selected'])) {
				foreach ($this->request->data['Section']['selected'] as $sec_key => $sec_Id) {
					if ($sec_Id != 0) {

						/* $courses = $this->PublishedCourse->Section->studentsSectionById($sec_Id);
						$list = $this->PublishedCourse->Section->studentsAlreaydTakenCourse($sec_Id);
						debug($list);
						$courses = $this->PublishedCourse->Section->studentsAlreaydTakenCourse($sec_Id); */

						$taken_student = $this->PublishedCourse->Section->getMostRepresntiveTakenCourse($sec_Id);
						$section_already_taken_courses = $taken_student['taken'];
						//debug($taken_student);
						$selected_students_ids = $taken_student['selected_student'];
					} //check selected
				}
			}
			// if there is no courses taken by the section display the list of courses attached to the curriculum.
			debug($section_already_taken_courses);

			//echo "Start Time ".date("D M j, Y-H:i:s",time());
			if (!empty($section_already_taken_courses)) {
				foreach ($section_already_taken_courses as $section_id => $taken_courses) {
					//due to talking to much time
					if (0 && !empty($taken_courses)) {
						
						$taken_equivalent_course_ids = array();
						
						if (isset($selected_students_ids[$section_id]) && !empty($selected_students_ids[$section_id])) {
							foreach ($taken_courses as $dd => $ddv) {
								if ($ddv != 0) {
									$tmp_course_ids = $this->PublishedCourse->Course->getTakenEquivalentCourses($selected_students_ids[$section_id], $ddv);
									foreach ($tmp_course_ids as $tmp_ind => $tmp_value) {
										$taken_equivalent_course_ids[] = $tmp_value;
									}
								}
							}
						} else {
							$taken_equivalent_course_ids = $taken_courses;
						}

						if (!empty($taken_equivalent_course_ids)) {
							$ready_for_publishing_courses[$section_id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
									'Course.department_id' => $this->department_id,
									"NOT" => array('Course.id ' => $taken_equivalent_course_ids)
								),
								'contain' => array(
									'PublishedCourse',
									'YearLevel' => array('id', 'name'),
									'Prerequisite' => array(
										'fields' => array('id', 'prerequisite_course_id', 'course_id', 'co_requisite')
									)
								),
								'order' => array('Course.year_level_id', 'Course.semester')
							));
						} else {
							debug($section_curriculum_attachment[$section_id]);
							debug($this->department_id);
							$ready_for_publishing_courses[$section_id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
									'Course.department_id' => $this->department_id
								),
								'contain' => array(
									'PublishedCourse',
									'YearLevel' => array('id', 'name'),
									'Prerequisite' => array(
										'fields' => array('id', 'prerequisite_course_id', 'course_id', 'co_requisite')
									)
								),
								'order' => array('Course.year_level_id', 'Course.semester')
							));
							debug($ready_for_publishing_courses);
						}

						// attach prerequiste code
						if (isset($ready_for_publishing_courses) && !empty($ready_for_publishing_courses)) {
							foreach ($ready_for_publishing_courses[$section_id] as $ll => &$rrvalue) {
								if (!empty($rrvalue['Prerequisite'])) {
									foreach ($rrvalue['Prerequisite'] as $pll => $pvv) {
										if ($pvv['co_requisite'] == 1) {
											$rrvalue['Prerequisite'][$pll]['pre_code'] = $this->PublishedCourse->Course->field('course_code', array('id' => $pvv['prerequisite_course_id'])) . ' - Co requiste';
										} else {
											$rrvalue['Prerequisite'][$pll]['pre_code'] = $this->PublishedCourse->Course->field('course_code', array('id' => $pvv['prerequisite_course_id']));
										}
									}
								}
							}
						}

						$taken_courses_allow_to_publishe_it[$section_id] = $this->PublishedCourse->Course->find('all', array(
							'conditions' => array(
								'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
								'Course.department_id' => $this->department_id,
								'Course.id ' => $taken_equivalent_course_ids,
								'Course.active' => 1
							),
							'contain' => array(
								'PublishedCourse', 
								'YearLevel' => array('id', 'name'), 
								'Prerequisite'
							)
						));

					} else {
						$ready_for_publishing_courses[$section_id] = $this->PublishedCourse->Course->find('all', array(
							'conditions' => array(
								'Course.curriculum_id' => $section_curriculum_attachment[$section_id],
								'NOT' => array('Course.id' => $taken_courses),
								'Course.department_id' => $this->department_id,
								'Course.active' => 1
							),
							'contain' => array(
								'PublishedCourse',
								'YearLevel' => array('id', 'name'),
								'Prerequisite'
							)
						));

						//debug($ready_for_publishing_courses);
						debug($taken_courses);
					}
				}
				// echo "End Time".date("D M j, Y-H:i:s",time());
			} else {
				// section has not already taken coures.

				if (!empty($this->request->data['Section']['selected'])) {
					foreach ($this->request->data['Section']['selected'] as $sec_key => $sec_Id) {
						// only for the selected sections
						if ($sec_Id != 0) {
							$ready_for_publishing_courses[$sec_Id] = $this->PublishedCourse->Course->find('all', array(
								'conditions' => array(
									'Course.curriculum_id' => $section_curriculum_attachment[$sec_Id],
									'Course.department_id' => $this->department_id,
									'Course.active' => 1
								),
								'contain' => array(
									'PublishedCourse',
									'YearLevel' => array('id', 'name'),
									'Prerequisite' => array(
										'fields' => array('id', 'prerequisite_course_id', 'course_id', 'co_requisite')
									)
								)
							));

							/////////////////////////////////////////fixes///////////////////////
							//debug($ready_for_publishing_courses[$sec_Id]);

							if (isset($ready_for_publishing_courses) && !empty($ready_for_publishing_courses)) {
								foreach ($ready_for_publishing_courses[$sec_Id] as $ll => &$rrvalue) {
									if (!empty($rrvalue['Prerequisite'])) {
										foreach ($rrvalue['Prerequisite'] as $pll => $pvv) {
											if ($pvv['co_requisite'] == 1) {
												$rrvalue['Prerequisite'][$pll]['pre_code'] = $this->PublishedCourse->Course->field('course_code', array('id' => $pvv['prerequisite_course_id'])) . ' - Co requiste';
											} else {
												$rrvalue['Prerequisite'][$pll]['pre_code'] = $this->PublishedCourse->Course->field('course_code', array('id' => $pvv['prerequisite_course_id']));
											}
										}
									}
								}
							}

							/////////////////////////////////////////////////////////
						}
					}
				}
			}

			$published_courses_disable_not_to_published = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.year_level_id' => $this->request->data['PublishedCourse']['year_level_id'],
					'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
					'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
					'PublishedCourse.drop' => 0,
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.section_id' => $this->request->data['Section']['selected']
				),
				'fields' => array('id', 'course_id', 'section_id'),
				'contain' => array()
			));

			$this->set('turn_off_search', true);
			$this->set(compact('sections'));
		}

		$tmp = array();
		//debug($published_courses_disable_not_to_published);

		if (isset($published_courses_disable_not_to_published) && !empty($published_courses_disable_not_to_published)) {
			foreach ($published_courses_disable_not_to_published as $keey => $pcdv) {
				if (isset($pcdv['PublishedCourse']['section_id'])) {
					$tmp[$pcdv['PublishedCourse']['section_id']][$pcdv['PublishedCourse']['id']] = $pcdv['PublishedCourse']['course_id'];
				}
			}
		}

		$published_courses_disable_not_to_published = $tmp;

		//debug($ready_for_publishing_courses);
		//published courses detail for a selected category
		$this->Session->Write('candidate_publish_courses', $ready_for_publishing_courses);
		$this->Session->Write('taken_courses_allow_to_publishe_it', $taken_courses_allow_to_publishe_it);
		$this->Session->Write('selected_section', $selected_section);
		$this->Session->Write('published_courses_disable_not_to_published', $published_courses_disable_not_to_published);
		
		$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
		$defaultDepartment = $this->department_id;
		$defaultCollege = $this->college_id;
		$colleges = $this->PublishedCourse->College->find('list', array('conditions' => array('College.active' => 1)));

		$this->set(compact('colleges', 'departments', 'defaultDepartment', 'defaultCollege'));
	}

	function publisheForUnassigned($data = null)
	{
		$this->layout = 'ajax';

		$selected_section = array();
		$ready_for_publishing_courses = array();

		if ($this->Session->read('candidate_publish_courses')) {

			if ($data == 2) {
				$ready_for_publishing_courses = $this->Session->read('candidate_publish_courses');
				$published_courses_disable_not_to_published = $this->Session->read('published_courses_disable_not_to_published');
				$selected_section = $this->Session->read('selected_section');
			} else {
				$this->Session->delete('candidate_publish_courses');
				$this->Session->delete('selected_section');
				$this->Session->delete('published_courses_disable_not_to_published');
			}
		}

		if (!empty($this->request->data['Section'])) {

			$sections = $this->PublishedCourse->Section->find('list', array('conditions' => array('Section.college_id' => $this->college_id, 'OR' => array('Section.department_id is null', 'Section.department_id = 0', 'Section.department_id = ""'), 'Section.academicyear like ' => $this->request->data['PublishedCourse']['academic_year'] . '%')));

			foreach ($this->request->data['Section']['selected'] as $section_id => $selecte_flag) {
				if ($selecte_flag != 0) {
					$selected_section[$selecte_flag] = $sections[$selecte_flag];
				}
			}

			// section has not already taken coures.
			if (!empty($this->request->data['Section']['selected'])) {
				foreach ($this->request->data['Section']['selected'] as $sec_key => $sec_Id) {
					// only for the selected sections
					if ($sec_Id != 0) {
						$ready_for_publishing_courses[$sec_Id] = $this->PublishedCourse->Course->find('all', array(
							'conditions' => array(
								'Course.curriculum_id' => $this->request->data['PublishedCourse']['curriculum_id'], 
								'Course.semester' => $this->request->data['PublishedCourse']['semester'], 
								'Course.department_id' => $this->request->data['PublishedCourse']['department_id'],
								'Course.active' => 1
							),
							'order' => 'year_level_id',
							'contain' => array(
								'PublishedCourse' => array(
									'CourseRegistration' => array(
										'limit' => 1
									),
									'CourseAdd' => array(
										'limit' => 1
									)
								)
							),
						));
					}
				}
			}

			$published_courses_disable_not_to_published = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.college_id' => $this->college_id,
					'OR' => array(
						'PublishedCourse.department_id is null',
						'PublishedCourse.department_id = 0',
						'PublishedCourse.department_id = ""'
					),
					'PublishedCourse.academic_year like ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
					'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.section_id' => $this->request->data['Section']['selected']
				),
				'fields' => array('id', 'course_id', 'section_id'), 
				'contain' => array()
			));

			$this->set(compact('sections', 'selected_sections'));
		}

		$tmp = array();

		if (isset($published_courses_disable_not_to_published) && !empty($published_courses_disable_not_to_published)) {
			foreach ($published_courses_disable_not_to_published as $keey => $pcdv) {
				if (isset($pcdv['PublishedCourse']['section_id'])) {
					$tmp[$pcdv['PublishedCourse']['section_id']][$pcdv['PublishedCourse']['id']] = $pcdv['PublishedCourse']['course_id'];
				}
			}
		}

		$published_courses_disable_not_to_published = $tmp;
		$this->Session->Write('published_courses_disable_not_to_published', $published_courses_disable_not_to_published);

		$this->Session->Write('candidate_publish_courses', $ready_for_publishing_courses);
		$this->Session->Write('selected_section', $selected_section);
	}

	function getPublishedCoursesForSplit($data = null)
	{

		$this->layout = 'ajax';
		if ($this->Session->read('list_of_courses') && $data == 2) {
			$is_list_of_course_execute = 1;
			$list_of_courses = $this->Session->read('list_of_courses');
			$course_type_array = $this->Session->read('course_type_array');
		} else {
			$this->Session->delete('list_of_courses');
			$this->Session->delete('course_type_array');
			$list_of_courses = null;
			$course_type_array = array();
			if (!empty($this->request->data['Section']) and $this->request->data['SectionSplitForPublishedCourse']['selectedsection'] >= 0) {
				$selected_section_id = $this->request->data['Section'][$this->request->data['SectionSplitForPublishedCourse']['selectedsection']]['id'];
				$list_of_courses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.program_id' =>
						$this->request->data['SectionSplitForPublishedCourse']['program_id'], 'PublishedCourse.semester' =>
						$this->request->data['SectionSplitForPublishedCourse']['semester'], 'PublishedCourse.program_type_id' =>
						$this->request->data['SectionSplitForPublishedCourse']['program_type_id'], 'PublishedCourse.year_level_id' =>
						$this->request->data['SectionSplitForPublishedCourse']['year_level_id'], 'PublishedCourse.academic_year'
						=> $this->request->data['SectionSplitForPublishedCourse']['academicyear'],
						'PublishedCourse.section_id' => $selected_section_id
					),
					'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'),
					'contain' => array('Course' => array('fields' => array(
						'Course.id', 'Course.course_code', 'Course.course_title',
						'Course.credit', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours'
					)))
				));
				//debug($list_of_courses);
				//$course_type_array = array();
				foreach ($list_of_courses as $lck => &$lcv) {
					$course_type_array[$lck][-1] = "---Selecte Course Type---";
					$course_type_array[$lck]["Lecture"] = "Lecture";
					if ($lcv['Course']['tutorial_hours'] > 0) {
						$course_type_array[$lck]["Tutorial"] = "Tutorial";
						$course_type_array[$lck]["Lecture+Tutorial"] = "Lecture+Tutorial";
					} else if ($lcv['Course']['laboratory_hours'] > 0) {
						$course_type_array[$lck]["Lab"] = "Lab";
						$course_type_array[$lck]["Lecture+Lab"] = "Lecture+Lab";
					}
					$lcv['GradeSubmitted'] = $this->PublishedCourse->CourseRegistration->ExamResult->isExamResultSubmitted(
						$lcv['PublishedCourse']['id']
					);
				}

				$this->Session->write('list_of_courses', $list_of_courses);
				$this->Session->write('course_type_array', $course_type_array);
			}
		}
		$this->set(compact('list_of_courses', 'course_type_array'));
	}

	function getPublishedCoursesForExam($data = null)
	{
		$this->layout = 'ajax';
		//$selected_sections=array();
		$list_of_courses = array();
		$is_list_of_course_execute = 0;
		/// give the user the list of courses which is already displayed
		// from the session when validation error occur.
		if ($this->Session->read('list_of_courses')) {

			if ($data == 2) {
				$is_list_of_course_execute = 1;
				$list_of_courses = $this->Session->read('list_of_courses');
			} else {
				$this->Session->delete('list_of_courses');
			}
		}

		if (!empty($this->request->data['Section'])) {
			$selction_array = array();
			foreach ($this->request->data['Section']['selected'] as $key => $selected_section_id) {
				if ($selected_section_id != 0) {
					$selction_array[$selected_section_id] = $selected_section_id;
				}
			}
			if (count($selction_array) > 0) {

				$is_list_of_course_execute = 1;
				$list_of_courses = $this->PublishedCourse->find(
					'all',
					array(
						'conditions' => array(
							'PublishedCourse.program_id' => $this->request->data['MergedSectionsExam']['program_id'], 'PublishedCourse.semester' => $this->request->data['MergedSectionsExam']['semester'],
							'PublishedCourse.program_type_id' => $this->request->data['MergedSectionsExam']['program_type_id'],
							'PublishedCourse.academic_year' => $this->AcademicYear->current_academicyear(),
							'PublishedCourse.section_id' => $selction_array,
							'PublishedCourse.id NOT IN( select published_course_id from merged_sections_exams)',
							'PublishedCourse.id NOT IN( select published_course_id from excluded_published_course_exams)'
						),
						'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'),
						'contain' => array('Course' => array('fields' => array(
							'Course.id', 'Course.course_code',
							'Course.course_title', 'Course.credit'
						)), 'Section' => array('fields' => array('Section.name')))
					)
				);
			}
		}

		if ($is_list_of_course_execute == 0) {
			$list_of_courses = null;
		}
		$this->Session->Write('list_of_courses', $list_of_courses);
	}
	function getPublishedCoursesForExamForSplit($data = null)
	{
		//debug($this->request->data);
		$this->layout = 'ajax';
		if ($this->Session->read('list_of_courses') and $data == 2) {
			$is_list_of_course_execute = 1;
			$list_of_courses = $this->Session->read('list_of_courses');
			//$course_type_array = $this->Session->read('course_type_array');

		} else {
			$this->Session->delete('list_of_courses');
			//$this->Session->delete('course_type_array');
			$list_of_courses = null;
			$course_type_array = array();
			if (!empty($this->request->data['Section']) and $this->request->data['SectionSplitForExam']['selectedsection'] >= 0) {
				$selected_section_id = $this->request->data['Section'][$this->request->data['SectionSplitForExam']['selectedsection']]['id'];
				$list_of_courses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.program_id' =>
						$this->request->data['SectionSplitForExam']['program_id'], 'PublishedCourse.semester' =>
						$this->request->data['SectionSplitForExam']['semester'], 'PublishedCourse.program_type_id' =>
						$this->request->data['SectionSplitForExam']['program_type_id'], 'PublishedCourse.academic_year like' =>
						$this->AcademicYear->current_academicyear() . '%', 'PublishedCourse.section_id' => $selected_section_id,
						'PublishedCourse.id NOT IN( select published_course_id from section_split_for_exams)',
						'PublishedCourse.id NOT IN( select published_course_id from excluded_published_course_exams)'
					),
					'fields' => array('PublishedCourse.id', 'PublishedCourse.course_id'),
					'contain' => array('Course' => array('fields' => array(
						'Course.id', 'Course.course_code', 'Course.course_title',
						'Course.credit', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours'
					)))
				));
				//debug($list_of_courses);
				/*$course_type_array = array();
				foreach($list_of_courses as $lck=>$lcv) {
					$course_type_array[$lck][-1] = "---Selecte Course Type---";
					$course_type_array[$lck]["Lecture"] = "Lecture";
					if($lcv['Course']['tutorial_hours'] >0){
						$course_type_array[$lck]["tutorial"] = "Tutorial";
						$course_type_array[$lck]["Lecture+Tutorial"] = "Lecture + Tutorial";
					} else if($lcv['Course']['laboratory_hours'] >0){
						$course_type_array[$lck]["Lab"] = "Lab";
						$course_type_array[$lck]["Lecture+Lab"] = "Lecture + Lab";
					}
				} */
				$this->Session->write('list_of_courses', $list_of_courses);
				//$this->Session->write('course_type_array',$course_type_array);
			}
		}
		$this->set(compact('list_of_courses'));
	}
	public function add_course_session()
	{
		$programs = $this->PublishedCourse->Program->find('list');
		$programTypes = $this->PublishedCourse->ProgramType->find('list');
		$departments = $this->PublishedCourse->Department->find('list', array('conditions' =>
		array('Department.college_id' => $this->college_id)));
		$yearLevels = null;
		$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' =>
		array('YearLevel.department_id' => $this->department_id)));
		$this->set(compact('programs', 'programTypes', 'departments', 'yearLevels'));

		if (!empty($this->request->data) && isset($this->request->data['search'])) {
			if ($this->Session->read('sections_array')) {
				$this->Session->delete('sections_array');
			}
			//debug($this->request->data);
			$everythingfine = false;
			switch ($this->request->data) {
				case empty($this->request->data['PublishedCourse']['academicyear']):
					$this->Session->setFlash('<span></span> ' . __('Please select the academic year of the publish courses
					that you want to define courses number of session.', true), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['PublishedCourse']['program_id']):
					$this->Session->setFlash('<span></span> ' . __('Please select the program of the publish courses
					that you want to define courses number of session. ', true), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['PublishedCourse']['program_type_id']):
					$this->Session->setFlash('<span></span> ' . __('Please select the program type of the publish courses
					that you want to define courses number of session. ', true), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['PublishedCourse']['semester']):
					$this->Session->setFlash('<span></span> ' . __('Please select the semester of the publish courses
					that you want to define courses number of session. ', true), 'default', array('class' => 'error-box error-message'));
					break;
				default:
					$everythingfine = true;
			}
			if ($everythingfine) {
				$selected_academicyear = $this->request->data['PublishedCourse']['academicyear'];
				$this->Session->write('selected_academicyear', $selected_academicyear);
				$selected_program = $this->request->data['PublishedCourse']['program_id'];
				$this->Session->write('selected_program', $selected_program);
				$selected_program_type = $this->request->data['PublishedCourse']['program_type_id'];
				$this->Session->write('selected_program_type', $selected_program_type);
				$selected_semester = $this->request->data['PublishedCourse']['semester'];
				$this->Session->write('selected_semester', $selected_semester);
				//$program_type_id=$this->AcademicYear->equivalent_program_type($selected_program_type);

				if ($this->role_id == ROLE_COLLEGE) {
					if (empty($this->request->data['PublishedCourse']['department_id'])) {
						$conditions = array(
							'PublishedCourse.academic_year' => $selected_academicyear,
							'PublishedCourse.college_id' => $this->college_id, 'PublishedCourse.program_id' => $selected_program,
							'PublishedCourse.program_type_id' => $selected_program_type, 'PublishedCourse.semester' => $selected_semester, 'PublishedCourse.drop' => 0, "OR" => array("PublishedCourse.department_id is null", "PublishedCourse.department_id" => array(0, ''))
						);
					} else {
						$selected_year_level = $this->request->data['PublishedCourse']['year_level_id'];
						$this->Session->write('selected_year_level', $selected_year_level);
						if (empty($selected_year_level) || ($selected_year_level == "All")) {
							$selected_year_level = '%';
						}
						$selected_department = $this->request->data['PublishedCourse']['department_id'];
						$this->Session->write('selected_department', $selected_department);
						$yearLevels = $this->PublishedCourse->YearLevel->find('list', array(
							'conditions' => array('YearLevel.department_id' => $selected_department)
						));
						$conditions = array(
							'PublishedCourse.academic_year' => $selected_academicyear,
							'PublishedCourse.department_id' => $selected_department, 'PublishedCourse.program_id' =>
							$selected_program, 'PublishedCourse.program_type_id' => $selected_program_type,
							'PublishedCourse.year_level_id LIKE' => $selected_year_level, 'PublishedCourse.semester' => $selected_semester, 'PublishedCourse.drop' => 0
						);
					}
				} else {
					$selected_year_level = $this->request->data['PublishedCourse']['year_level_id'];
					$this->Session->write('selected_year_level', $selected_year_level);
					if (empty($selected_year_level) || ($selected_year_level == "All")) {
						$selected_year_level = '%';
					}
					$selected_department = $this->department_id;
					$this->Session->write('selected_department', $selected_department);
					$yearLevels = $this->PublishedCourse->YearLevel->find('list', array(
						'conditions' => array('YearLevel.department_id' => $selected_department)
					));
					$conditions = array(
						'PublishedCourse.academic_year' => $selected_academicyear,
						'PublishedCourse.department_id' => $selected_department, 'PublishedCourse.program_id' =>
						$selected_program, 'PublishedCourse.program_type_id' => $selected_program_type,
						'PublishedCourse.year_level_id LIKE' => $selected_year_level, 'PublishedCourse.semester' => $selected_semester, 'PublishedCourse.drop' => 0
					);
				}
				$publishedcourses = $this->PublishedCourse->find('all', array(
					'conditions' => $conditions, 'fields' => array('PublishedCourse.id', 'PublishedCourse.section_id'),
					'contain' => array('Section' => array('fields' => array('Section.id', 'Section.name')), 'Course' => array(
						'fields' => array(
							'Course.id', 'Course.course_title', 'Course.course_code', 'Course.credit',
							'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours'
						)
					))
				));
				//debug($publishedcourses);
				$sections_array = array();
				foreach ($publishedcourses as $key => $publishedcourse) {
					$sections_array[$publishedcourse['Section']['name']][$key]['course_title'] = $publishedcourse['Course']['course_title'];
					$sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse['Course']['id'];
					$sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse['Course']['course_code'];
					$sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse['Course']['credit'];
					$sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' .
						$publishedcourse['Course']['laboratory_hours'];
					$sections_array[$publishedcourse['Section']['name']][$key]['section_id'] =
						$publishedcourse['PublishedCourse']['section_id'];
					$sections_array[$publishedcourse['Section']['name']][$key]['published_course_id'] =
						$publishedcourse['PublishedCourse']['id'];
				}
				//debug($sections_array);
				if (empty($sections_array)) {
					$this->Session->setFlash('<span></span> ' . __('There is no published courses to define
					 number of session the selected criteria.', true), 'default', array('class' => 'info-box info-message'));
				} else {
					$this->Session->write('sections_array', $sections_array);
					$this->Session->write('yearLevels', $yearLevels);
					$this->set(compact('sections_array', 'yearLevels'));
				}
			}
		}
		if (!empty($this->request->data) && isset($this->request->data['submit'])) {
			//debug($this->request->data);
			if (!empty($this->request->data['PublishedCourse']['courses'])) {
				$PublishedCourse_update_array = array();
				$PublishedCourse_update_array['PublishedCourse']['id'] = $this->request->data['PublishedCourse']['courses'];
				if (isset($this->request->data['lecture_number_of_session'])) {
					$PublishedCourse_update_array['PublishedCourse']['lecture_number_of_session'] =
						$this->request->data['lecture_number_of_session'];
				}
				if (isset($this->request->data['tutorial_number_of_session'])) {
					$PublishedCourse_update_array['PublishedCourse']['tutorial_number_of_session'] =
						$this->request->data['tutorial_number_of_session'];
				}
				if (isset($this->request->data['lab_number_of_session'])) {
					$PublishedCourse_update_array['PublishedCourse']['lab_number_of_session'] =
						$this->request->data['lab_number_of_session'];
				}
				$this->PublishedCourse->save($PublishedCourse_update_array);
				$this->Session->setFlash(
					'<span></span> ' . __('Course number of sessions has been updated.'),
					'default',
					array('class' => 'success-box success-message')
				);
			} else {
				$this->Session->setFlash('<span></span> ' . __('Please select course.'), 'default', array(
					'class' => 'error-box error-message'
				));
			}
			$sections_array = $this->Session->read('sections_array');
			$yearLevels = $this->Session->read('yearLevels');
			$this->set(compact('sections_array', 'yearLevels'));
		}
		//debug($this->request->data);
		if (isset($this->request->data['PublishedCourse']) && !empty($this->request->data['PublishedCourse'])) {
			$selected_academicyear = $this->request->data['PublishedCourse']['academicyear'];
			$selected_program = $this->request->data['PublishedCourse']['program_id'];
			$selected_program_type = $this->request->data['PublishedCourse']['program_type_id'];
			$selected_semester = $this->request->data['PublishedCourse']['semester'];
			$selected_year_level = $this->request->data['PublishedCourse']['year_level_id'];
		}

		if ($this->Session->read('yearLevels')) {
			$yearLevels = $this->Session->read('yearLevels');
		}

		if ($this->role_id == ROLE_COLLEGE) {
			if (!empty($this->request->data['PublishedCourse']['department_id'])) {
				$selected_department = $this->request->data['PublishedCourse']['department_id'];
				$this->Session->write('selected_department', $selected_department);
			}
			if (empty($this->request->data['PublishedCourse']['department_id']) && !empty($selected_academicyear) && !empty($selected_program)) {
				$conditions = array(
					'PublishedCourse.academic_year' => $selected_academicyear,
					'PublishedCourse.college_id' => $this->college_id, 'PublishedCourse.program_id' => $selected_program,
					'PublishedCourse.program_type_id' => $selected_program_type, 'PublishedCourse.semester' =>
					$selected_semester, "OR" => array(
						"PublishedCourse.department_id is null",
						"PublishedCourse.department_id" => array(0, '')
					)
				);
			} else {
				if (!empty($this->request->data['PublishedCourse']['year_level_id'])) {
					$selected_year_level = $this->request->data['PublishedCourse']['year_level_id'];
					$this->Session->write('selected_year_level', $selected_year_level);
				}
				if (empty($selected_year_level) || ($selected_year_level == "All")) {
					$selected_year_level = '%';
				}
				if (!empty($selected_department)) {
					$yearLevels = $this->PublishedCourse->YearLevel->find('list', array(
						'conditions' => array('YearLevel.department_id' => $selected_department)
					));
					$conditions = array(
						'PublishedCourse.academic_year' => $selected_academicyear,
						'PublishedCourse.department_id' => $selected_department, 'PublishedCourse.program_id' =>
						$selected_program, 'PublishedCourse.program_type_id' => $selected_program_type,
						'PublishedCourse.year_level_id LIKE' => $selected_year_level, 'PublishedCourse.semester' => $selected_semester, 'PublishedCourse.drop' => 0
					);
				}
			}
		} else {
			$selected_year_level = $this->request->data['PublishedCourse']['year_level_id'];
			$this->Session->write('selected_year_level', $selected_year_level);
			if (empty($selected_year_level) || ($selected_year_level == "All")) {
				$selected_year_level = '%';
			}
			$selected_department = $this->department_id;
			$this->Session->write('selected_department', $selected_department);
			$yearLevels = $this->PublishedCourse->YearLevel->find('list', array(
				'conditions' => array('YearLevel.department_id' => $selected_department)
			));
			$conditions = array(
				'PublishedCourse.academic_year' => $selected_academicyear,
				'PublishedCourse.department_id' => $selected_department, 'PublishedCourse.program_id' =>
				$selected_program, 'PublishedCourse.program_type_id' => $selected_program_type,
				'PublishedCourse.year_level_id LIKE' => $selected_year_level, 'PublishedCourse.semester' => $selected_semester, 'PublishedCourse.drop' => 0
			);
		}
		if (!empty($conditions)) {
			$PublishedCorseHistory = $this->PublishedCourse->find('all', array(
				'conditions' => $conditions, 'fields' => array(
					'PublishedCourse.id', 'PublishedCourse.section_id',
					'PublishedCourse.lecture_number_of_session', 'PublishedCourse.tutorial_number_of_session',
					'PublishedCourse.lab_number_of_session'
				),
				'contain' => array('Section' => array('fields' => array('Section.id', 'Section.name')), 'Course' => array(
					'fields' => array(
						'Course.id', 'Course.course_title', 'Course.course_code', 'Course.credit',
						'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours'
					)
				))
			));
		}
		$PublishedCorseHistory_formatted_array = array();
		if (isset($PublishedCorseHistory) && !empty($PublishedCorseHistory)) {
			foreach ($PublishedCorseHistory as $key => $publishedcourse) {
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['course_title'] =
					$publishedcourse['Course']['course_title'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['course_id'] =
					$publishedcourse['Course']['id'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['course_code'] =
					$publishedcourse['Course']['course_code'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['credit'] =
					$publishedcourse['Course']['credit'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['credit_detail'] =
					$publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' .
					$publishedcourse['Course']['laboratory_hours'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['section_id'] =
					$publishedcourse['PublishedCourse']['section_id'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['published_course_id'] =
					$publishedcourse['PublishedCourse']['id'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['lecture_number_of_session'] =
					$publishedcourse['PublishedCourse']['lecture_number_of_session'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['tutorial_number_of_session'] =
					$publishedcourse['PublishedCourse']['tutorial_number_of_session'];
				$PublishedCorseHistory_formatted_array[$publishedcourse['Section']['name']][$key]['lab_number_of_session'] =
					$publishedcourse['PublishedCourse']['lab_number_of_session'];
			}
		}
		//debug($PublishedCorseHistory_formatted_array);

		$this->set(compact(
			'selected_academicyear',
			'selected_program',
			'selected_program_type',
			'selected_semester',
			'selected_year_level',
			'selected_department',
			'yearLevels',
			'PublishedCorseHistory_formatted_array'
		));
	}
	function get_year_level($department_id = null)
	{
		if (!empty($department_id)) {
			$this->layout = 'ajax';
			$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $department_id)));
			$this->set(compact('yearLevels'));
		}
	}
	function get_course_type_session($publishedCourse_id = null)
	{
		if (!empty($publishedCourse_id)) {
			$publishedcourse_data = $this->PublishedCourse->find('all', array(
				'conditions' => array('PublishedCourse.id' => $publishedCourse_id), 'fields' => array(
					'PublishedCourse.id'
				), 'contain' => array('Course' => array('fields' => array(
					'Course.id',
					'Course.course_title', 'Course.course_code', 'Course.credit', 'Course.lecture_hours',
					'Course.tutorial_hours', 'Course.laboratory_hours'
				)))
			));
			//$publishedcourse_data[0]['Course']
			$this->set(compact('publishedcourse_data'));
		}
	}
	
	function attache_scale()
	{

		if (!empty($this->request->data) && isset($this->request->data['attachescaletocourse'])) {

			$data['PublishedCourse'] = $this->request->data['Published'];



			if (empty($data['PublishedCourse'])) {
				$this->Session->setFlash('<span></span>' . __('No course has been attached to any scale.'), 'default', array('class' => 'info-box info-message'));
			} else {

				if ($this->PublishedCourse->saveAll(
					$data['PublishedCourse'],
					array('validate' => 'first')
				)) {
					$this->Session->setFlash(
						'<span></span>' . __('The grade scale has been saved'),
						'default',
						array('class' => 'success-message success-box')
					);
					//$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>' . __('The grade scale could not be saved. Please, try again.'), 'default', array('class' => 'error-box error-message'));
				}
			}


			$this->request->data['getPublishedCourseList'] = true;
		}
		if (!empty($this->request->data) && isset($this->request->data['getPublishedCourseList'])) {

			$everythingfine = false;

			switch ($this->request->data) {

				case empty($this->request->data['PublishedCourse']['academic_year']):
					$this->Session->setFlash('<span></span> ' . __('Please select the academic year you want to attach scale.'), 'default', array('class' => 'error-box error-message'));
					break;

				case empty($this->request->data['PublishedCourse']['semester']):
					$this->Session->setFlash('<span></span>' . __('Please select the semester you want to attache scale.'), 'default', array('class' => 'error-box error-message'));
					break;

				case empty($this->request->data['PublishedCourse']['program_id']):
					$this->Session->setFlash('<span></span>' . __('Please select the program you want to unpublished courses.'), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['PublishedCourse']['year_level_id']):
					$this->Session->setFlash('<span></span>' . __('Please select the year level  you want  attache scale.'), 'default', array('class' => 'error-box error-message'));
					break;

				default:
					$everythingfine = true;
			}

			if ($everythingfine) {

				$publishedCourses = $this->PublishedCourse->find('all', array(
					'fields' => array(
						'id',
						'section_id', 'grade_scale_id', 'year_level_id'
					), 'conditions' =>
					array(
						'PublishedCourse.semester ' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.academic_year like ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
						'PublishedCourse.drop' => 0,
						'PublishedCourse.given_by_department_id' => $this->department_id,
						//  'PublishedCourse.year_level_id'=>$this->request->data['PublishedCourse']['year_level_id'],
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id']
					),
					'contain' => array(
						'Course' => array('fields' => array(
							'id', 'course_title', 'credit',
							'course_code', 'grade_type_id'
						)),
						'Section' => array(
							'fields' => array('id', 'name'),
							'conditions' => array('Section.archive <> ' => 1),
							'ProgramType' => array('fields' => array('id', 'name')),
							'YearLevel' => array('fields' => array('id', 'name')),
							'Department' => array('fields' => array('id', 'name')),
							'College' => array('fields' => array('id', 'name')),

						)
					)
				));



				if (!empty($publishedCourses)) {

					$gradeScales = $this->PublishedCourse->GradeScale->find(
						'all',
						array(
							'conditions' => array(
								'GradeScale.model' => 'Department',
								'GradeScale.foreign_key' => $this->department_id,
								'GradeScale.active' => 1,
								'GradeScale.program_id' => $this->request->data['PublishedCourse']['program_id']
							),
							'fields' => array('id', 'name'),
							'contain' => array(
								'GradeScaleDetail' => array(
									'Grade' => array(
										'fields' => array('id', 'grade'),
										'GradeType' => array('id', 'type')
									)
								),
								'Program' => array('id', 'name')

							)
						)
					);

					$college_id = $this->PublishedCourse->Department->field(
						'college_id',
						array('Department.id' => $this->department_id)
					);

					$find_delegation_program_ids = $this->PublishedCourse->College->find(
						'first',
						array(
							'conditions' => array('College.id' => $college_id),
							'fields' => array('deligate_for_graduate_study', 'deligate_scale')
						)
					);

					if (empty($gradeScales)) {

						if (
							$find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 &&
							$this->request->data['PublishedCourse']['program_id'] == PROGRAM_POST_GRADUATE
						) {
							$this->Session->setFlash('<span></span>' . __('There is no grade scale in the system that are defined, please define scale before attaching grade scale to published courses.'), 'default', array('class' => 'error-box error-message'));
							$this->redirect(array('controller' => 'gradeScales', 'action' => 'add'));
						} else if (
							$find_delegation_program_ids['College']['deligate_scale'] == 1
							&& PROGRAM_UNDEGRADUATE == $this->request->data['PublishedCourse']['program_id']
						) {
							$this->Session->setFlash('<span></span>' . __('There is no grade scale in the system that are defined, please define scale before attaching grade scale to published courses.'), 'default', array('class' => 'error-box error-message'));
							$this->redirect(array('controller' => 'gradeScales', 'action' => 'set_grade_scale'));
						} else {
							$this->Session->setFlash('<span></span>' . __('You dont have mandate to attach
			              grade scale to published courses,
			              grade scale defined by the college will be applicable to
			              the published courses, if this is not right please contact college to delegate scale to department.', true), 'default', array('class' => 'error-box error-message'));
						}
					}

					$return = array();
					foreach ($gradeScales as $kk => $vv) {

						if (!empty($vv['GradeScaleDetail'][0]['Grade']['GradeType']['type'])) {
							$return[$vv['GradeScaleDetail'][0]['Grade']['GradeType']['id']][$vv['GradeScale']['name'] . '-'
								. $vv['GradeScaleDetail'][0]['Grade']['GradeType']['type'] . '-' .
								$vv['Program']['name']][$vv['GradeScale']['id']] = $vv['GradeScale']['name'];
						}
					}
					$gradeScales = $return;



					$section_organized_published_courses = $this->PublishedCourse->get_section_organized_published_courses($this->request->data, $this->department_id, $publishedCourses);

					$section_organized_published_courses = $this->PublishedCourse->getSectionOrganizedPublishedCoursesM($publishedCourses);

					if (empty($section_organized_published_courses)) {
						$this->Session->setFlash('<span></span>' . __('There is no published courses in the given criteria that needs scale attachment.'), 'default', array('class' => 'error-box error-message'));
					}
					$this->set(compact('section_organized_published_courses', 'gradeScales'));
				} else {
					$this->Session->setFlash('<span></span>' .
						__('There is no published courses in the given criteria that needs scale attachment.
			         ', true), 'default', array('class' => 'error-box error-message'));
				}
			}
		}


		$yearLevels = $this->PublishedCourse->YearLevel->find('list', array('conditions' => array(
			'YearLevel.department_id' => $this->department_id
		)));



		$college_id = $this->PublishedCourse->Department->field(
			'college_id',
			array('Department.id' => $this->department_id)
		);
		$find_delegation_program_ids = $this->PublishedCourse->College->find(
			'first',
			array(
				'conditions' => array('College.id' => $college_id),
				'fields' => array('deligate_for_graduate_study', 'deligate_scale')
			)
		);

		if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 1) {
			$programs = $this->PublishedCourse->Program->find('list');
		} else if (
			$find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0 &&
			$find_delegation_program_ids['College']['deligate_scale'] == 1
		) {
			$programs = $this->PublishedCourse->Program->find('list', array('conditions' =>
			array('Program.id' => PROGRAM_UNDEGRADUATE)));
		} else if (
			$find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1
			&& $find_delegation_program_ids['College']['deligate_scale'] == 0
		) {

			$programs = $this->PublishedCourse->Program->find('list', array('conditions' =>
			array('Program.id' => PROGRAM_POST_GRADUATE)));
		} else if (
			$find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0
			&& $find_delegation_program_ids['College']['deligate_scale'] == 0
		) {
			$programs['no_delegation'] = 'Not Delegated';
		}
		$gradeTypes = $this->PublishedCourse->Course->GradeType->find('list');

		$this->set(compact('programs', 'yearLevels', 'gradeTypes'));
	}

	function get_course_grade_scale($published_course_id = null)
	{
		//debug($published_course_id);
		$this->layout = 'ajax';
		$grade_scale = array();

		if (!empty($published_course_id)) {
			$grade_scale = $this->PublishedCourse->getGradeScaleDetail($published_course_id);
		}

		//debug($grade_scale);
		$this->set(compact('grade_scale'));
	}

	function get_course_grade_stats($published_course_id = null)
	{
		//debug($published_course_id);
		$this->layout = 'ajax';
		$gradeStatistics = array();

		if (!empty($published_course_id)) {
			$gradeStatistics = $this->PublishedCourse->CourseRegistration->ExamGrade->getLetterGradeStatistics($published_course_id);
		}

		//debug($gradeStatistics);
		$this->set(compact('gradeStatistics'));
	}

	function get_course_published_for_section($section_id = "", $last = 1)
	{
		$this->layout = 'ajax';

		$published_courses_list = array();

		if (!empty($section_id)) {
			if ($last == 1) {
				$published_courses_list = $this->PublishedCourse->lastPublishedCoursesForSection($section_id);
			} else {
				$published_courses_list = $this->PublishedCourse->sectionPublishedCourses($section_id);
			}
		}

		debug($published_courses_list);
		debug($section_id);
		debug($last);

		$this->set(compact('published_courses_list'));
	}

	// Publish course for department unassigned students 

	function college_publish_course()
	{
		if (!empty($this->request->data) && isset($this->request->data['getsection'])) {

			$everythingfine = false;

			switch ($this->request->data) {
				case empty($this->request->data['PublishedCourse']['academicyear']):
					$this->Flash->error(__('Please select the academic year you want to publish courses.'));
					break;
				case empty($this->request->data['PublishedCourse']['semester']):
					$this->Flash->error(__('Please select the semester you want to published courses.'));
					break;
				case empty($this->request->data['PublishedCourse']['department_id']):
					$this->Flash->error( __('Please select the department you want to to select curriculum to be published.'));
					break;
				case empty($this->request->data['PublishedCourse']['curriculum_id']):
					$this->Flash->error( __('Please select curriculum you want to to select courses to be published.'));
					break;
				default:
					$everythingfine = true;
			}

			if ($everythingfine) {

				// $year_level_id = $this->request->data['PublishedCourse']['year_level_id'];
				$program_id = $this->request->data['PublishedCourse']['program_id'];
				$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
				$academic_year = $this->request->data['PublishedCourse']['academicyear'];
				$semester = $this->request->data['PublishedCourse']['semester'];
				$department_id = $this->request->data['PublishedCourse']['department_id'];
				$curriculum_id = $this->request->data['PublishedCourse']['curriculum_id'];
				// $sections = $selected_section;

				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.college_id' => $this->college_id,
						'Section.academicyear like ' => $academic_year . '%',
						'Section.program_id' => $program_id,
						'Section.program_type_id' => $program_type_id,
						'OR' => array(
							'Section.department_id is null', 
							'Section.department_id = ""', 
							'Section.department_id = 0'
						)
					)
				));

				if (empty($sections)) {
					$this->Flash->info( __('You need to create section before publish courses for pre-engineering/freshman students.'));
					$this->redirect(array('controller' => 'sections', 'action' => 'add'));
				}

				$this->set(compact('show_publish_page'));
				$this->set('turn_off_search', true);
				$this->set(compact('sections', 'program_id', 'program_type_id', 'academic_year', 'semester', 'department_id', 'curriculum_id'));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['publishselectedadd'])) {

			$this->request->data['PublishedCourse']['college_id'] = $this->college_id;
			unset($this->request->data['PublishedCourse']['department_id']);
			$save_reformated_published_courses = array();
			$courses_ids = array();
			$section_ids = array();

			$count = 0;
			$no_previous_published_courses_error = ''; 

			if (!empty($this->request->data['Course'])) {
				foreach ($this->request->data['Course'] as $section_id => $courses) {

					$pcourses_count = $this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.section_id' => $section_id, 'PublishedCourse.academic_year' => (isset($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $this->request->data['PublishedCourse']['academicyear']), 'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'])));
					debug($pcourses_count);

					$section_ids[] = $section_id;
					
					foreach ($courses as $cid => $is_selected) {
						if ($is_selected != 0) {
							if ($pcourses_count) {

								$save_reformated_published_courses['PublishedCourse'][$count] = $this->request->data['PublishedCourse'];
								$save_reformated_published_courses['PublishedCourse'][$count]['published'] = 1;
								$save_reformated_published_courses['PublishedCourse'][$count]['add'] = 1;
								$save_reformated_published_courses['PublishedCourse'][$count]['course_id'] = $cid;
								$save_reformated_published_courses['PublishedCourse'][$count]['section_id'] = $section_id;
								$courses_ids[] = $cid;

								$count++;

							} else {
								if (empty($no_previous_published_courses_error)) {
									$no_previous_published_courses_error = $this->PublishedCourse->Section->field('name', array('Section.id' => $section_id)) . ' section doesn\'t have any previous course publication for ' . (isset($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $this->request->data['PublishedCourse']['academicyear']) . ' semster ' . $this->request->data['PublishedCourse']['semester'] . ', please use Publish Courses instead.';
								}
							}
						}
					}
					
				}
			}

			if ($count == 0 || !empty($no_previous_published_courses_error)) {

				if (!empty($no_previous_published_courses_error)) {
					$this->Flash->error($no_previous_published_courses_error);
				} else {
					$this->Flash->error(__('Please select atleast one course you want to publish as an add .'));
				}

				$program_id = $this->request->data['PublishedCourse']['program_id'];
				$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
				$academic_year = $this->request->data['PublishedCourse']['academic_year'];
				$semester = $this->request->data['PublishedCourse']['semester'];
				$department_id = (isset($this->request->data['PublishedCourse']['department_id']) ? $this->request->data['PublishedCourse']['department_id'] : '');
				$curriculum_id = (isset($this->request->data['PublishedCourse']['curriculum_id']) ? $this->request->data['PublishedCourse']['curriculum_id'] : '');

				$this->set('turn_off_search', true);

				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.college_id' => $this->college_id,
						'Section.academicyear like ' => $academic_year . '%',
						'OR' => array(
							'Section.department_id is null', 
							'Section.department_id = ""', 
							'Section.department_id = 0'
						)
					)
				));

				$selectedsection = $this->request->data['Section']['selected']; 

				if (!empty($no_previous_published_courses_error)) {
					//$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], str_replace('/', '-', $academic_year)));
				}
				
				$this->set(compact('sections', 'program_id', 'program_type_id', 'curriculum_id', 'department_id', 'academic_year', 'semester', 'selectedsection'));

			} else {

				$selected_ac_year = (isset($this->request->data['PublishedCourse']['academic_year']) && !empty($this->request->data['PublishedCourse']['academic_year']) ?  $this->request->data['PublishedCourse']['academic_year'] : $this->request->data['PublishedCourse']['academicyear']);

				$check_courses_published = $this->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $selected_ac_year . '%', 
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.course_id' => $courses_ids, 
						'PublishedCourse.section_id' => $section_ids, 
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'], 
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.college_id' => $this->college_id,
						'PublishedCourse.department_id is null'
					)
				));

				$published_courses = $this->PublishedCourse->find('list', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $selected_ac_year . '%', 
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'], 
						'PublishedCourse.section_id' => $section_ids, 
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'], 
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'], 
						'PublishedCourse.college_id' => $this->college_id
					), 
					'fields' => array('PublishedCourse.course_id', 'PublishedCourse.course_id')
				));

				// unset those courses which has already published and publish only coures not published

				/* if (!empty($published_courses) && !empty($save_reformated_published_courses)) {
					foreach ($published_courses as $pk => $pv) {
						//check and unset those already published courses
						foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => &$sv) {
							if ($sv['course_id'] == $pv && $sv['section_id'] == $pk) {
								unset($save_reformated_published_courses['PublishedCourse'][$sk]);
							}
						}
					}
				} */

				if (!empty($save_reformated_published_courses['PublishedCourse'])) {
					foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => $sv) {
						$pc_course_exists = $this->PublishedCourse->find('count', array(
							'conditions' => array(
								'PublishedCourse.academic_year' => $sv['academic_year'],
								'PublishedCourse.semester' => $sv['semester'],
								'PublishedCourse.section_id' => $sv['section_id'],
								'PublishedCourse.course_id' => $sv['course_id'],
								//'PublishedCourse.published' => $sv['published'],
							)
						));
						//debug($pc_course_exists);
						if ($pc_course_exists) {
							unset($save_reformated_published_courses['PublishedCourse'][$sk]);
						}
					}
				}

				// check if all courses has already published and redirect the user to the published course page.
				if ($check_courses_published == count($courses_ids)) {
					$this->Flash->info( __('The selected courses are already published for with the selected search criteria'));
					$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
					$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
				}


				$selected_sections_ids = array();
				//debug($this->request->data['Section']['selected']);

				if (!empty($this->request->data['Section']['selected'])) {
					foreach ($this->request->data['Section']['selected'] as $se_index => $se_value) {
						if ($se_value != 0) {
							$selected_sections_ids[] = $se_index;
						}
					}
				}

				if (!empty($selected_sections_ids)) {
					$list_courses_published_ids = $this->PublishedCourse->find('list', array(
						'conditions' => array(
							'PublishedCourse.academic_year like ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
							'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
							'PublishedCourse.section_id' => $selected_sections_ids,
							'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
							'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
							'PublishedCourse.department_id is null',
							'PublishedCourse.college_id' => $this->college_id
						)
					));
				}

				if (!empty($list_courses_published_ids)) {
					$check_registration_started = $this->PublishedCourse->CourseRegistration->find('count', array('conditions' => array('CourseRegistration.publish_course_id' => $list_courses_published_ids)));
					if ($check_registration_started == 0) {
						$check_registration_started = $this->PublishedCourse->CourseAdd->find('count', array('conditions' => array('CourseAdd.published_course_id' => $list_courses_published_ids)));
					}
				} else {
					$check_registration_started = 0;
				}


				if ($check_registration_started == 0) {
					if (!empty($save_reformated_published_courses['PublishedCourse'])) {
						if ($this->PublishedCourse->saveAll($save_reformated_published_courses['PublishedCourse'], array('validate' => 'first'))) {
							$this->Flash->success(__('Published the selected courses successfully.'));
							$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
							$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
						} else {
							$this->Flash->error( __('Coudn\'t publish the selected courses. Please, try again.'));
						}
					} else {
						$this->Flash->error( __('No new course is found to publish or update form the courses you selected.'));
					}
				} else {
					$this->Flash->warning(__('Course registration has been started. You can\'t publish the selected courses. You can publish them as mass add course instead.'));
					$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
					$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
				}
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['publishselected'])) {

			$this->request->data['PublishedCourse']['college_id'] = $this->college_id;
			unset($this->request->data['PublishedCourse']['department_id']);
			$save_reformated_published_courses = array();
			$courses_ids = array();
			$section_ids = array();

			$count = 0;

			if (!empty($this->request->data['Course'])) {
				foreach ($this->request->data['Course'] as $section_id => $courses) {
					debug($section_id);
					$section_ids[] = $section_id;
					foreach ($courses as $cid => $is_selected) {
						if ($is_selected != 0) {

							$save_reformated_published_courses['PublishedCourse'][$count] = $this->request->data['PublishedCourse'];
							$save_reformated_published_courses['PublishedCourse'][$count]['published'] = 1;
							$save_reformated_published_courses['PublishedCourse'][$count]['course_id'] = $cid;
							$save_reformated_published_courses['PublishedCourse'][$count]['section_id'] = $section_id;
							$courses_ids[] = $cid;

							$count++;
						}
					}
				}
			}

			if ($count == 0) {
				$this->Flash->error( __('Please select atleast one course you want to publish.'));

				$program_id = $this->request->data['PublishedCourse']['program_id'];
				$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
				$academic_year = $this->request->data['PublishedCourse']['academic_year'];
				$semester = $this->request->data['PublishedCourse']['semester'];
				$department_id = (isset($this->request->data['PublishedCourse']['department_id']) ? $this->request->data['PublishedCourse']['department_id'] : '');
				$curriculum_id = (isset($this->request->data['PublishedCourse']['curriculum_id']) ? $this->request->data['PublishedCourse']['curriculum_id'] : '');

				$this->set('turn_off_search', true);

				$sections = $this->PublishedCourse->Section->find('list', array(
					'conditions' => array(
						'Section.college_id' => $this->college_id,
						'Section.academicyear like ' => $academic_year . '%',
						'OR' => array(
							'Section.department_id is null', 
							'Section.department_id = ""', 
							'Section.department_id = 0'
						)
					)
				));

				debug($sections);
				$selectedsection = $this->request->data['Section']['selected'];

				$this->set(compact('sections', 'program_id', 'program_type_id', 'curriculum_id', 'academic_year', 'semester', 'selectedsection', 'department_id'));

			} else {

				$selected_ac_year = (isset($this->request->data['PublishedCourse']['academic_year']) && !empty($this->request->data['PublishedCourse']['academic_year']) ?  $this->request->data['PublishedCourse']['academic_year'] : $this->request->data['PublishedCourse']['academicyear']);

				$check_courses_published = $this->PublishedCourse->find('count', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $selected_ac_year  . '%',
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.course_id' => $courses_ids, 'PublishedCourse.section_id' => $section_ids, 
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'], 
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.college_id' => $this->college_id
					)
				));

				$published_courses = $this->PublishedCourse->find('list', array(
					'conditions' => array(
						'PublishedCourse.academic_year like ' => $selected_ac_year . '%',
						'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
						'PublishedCourse.section_id' => $section_ids,
						'PublishedCourse.department_id is null ',
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.college_id' => $this->college_id
					),
					'fields' => array('PublishedCourse.course_id', 'PublishedCourse.course_id')
				));

				//debug($published_courses);
				//debug($save_reformated_published_courses);

				// unset those courses which has already published and publish only coures not published

				/* if (!empty($published_courses) && !empty($save_reformated_published_courses)) {
					foreach ($published_courses as $pk => $pv) {
						//check and unset those already published courses
						foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => &$sv) {
							if ($sv['course_id'] == $pv && $sv['section_id'] == $pk) {
								unset($save_reformated_published_courses['PublishedCourse'][$sk]);
							}
						}
					}
				} */


				if (!empty($save_reformated_published_courses['PublishedCourse'])) {
					foreach ($save_reformated_published_courses['PublishedCourse'] as $sk => $sv) {
						$pc_course_exists = $this->PublishedCourse->find('count', array(
							'conditions' => array(
								'PublishedCourse.academic_year' => $sv['academic_year'],
								'PublishedCourse.semester' => $sv['semester'],
								'PublishedCourse.section_id' => $sv['section_id'],
								'PublishedCourse.course_id' => $sv['course_id'],
								//'PublishedCourse.published' => $sv['published'],
							)
						));
						//debug($pc_course_exists);
						if ($pc_course_exists) {
							unset($save_reformated_published_courses['PublishedCourse'][$sk]);
						}
					}
				}

				//debug($save_reformated_published_courses);
				// check if all courses has already published and redirect the user to the published course page.

				if ($check_courses_published == count($courses_ids)) {
					$this->Flash->info( __('The selected courses are already published for the selected criteria'));
					$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
					$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
				}

				$count_already_published = count($published_courses);
				$count_ready_published = count($save_reformated_published_courses);

				if (!empty($save_reformated_published_courses['PublishedCourse'])) {
					if ($this->PublishedCourse->saveAll($save_reformated_published_courses['PublishedCourse'], array('validate' => 'first'))) {
						$this->Flash->success(__('Published the selected courses successfully.'));
						$academic_year = str_replace('/', '-', $this->request->data['PublishedCourse']['academic_year']);
						$this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'], $academic_year));
					} else {
						$this->Flash->error( __('Coud\'t publish the selected courses. Please, try again.'));
					}
				} else {
					$this->Flash->error( __('No new course is found to publish or update form the courses you selected.'));
				}
			}
		}

		$remedial = 0;

		$programTypesAllowed = Configure::read('program_types_available_for_registrar_college_level_permissions');
		
		if (isset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING])) {
			unset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING]);
		}

		$programTypesAllowed[PROGRAM_TYPE_EVENING] = PROGRAM_TYPE_EVENING;
		$programTypesAllowed[PROGRAM_TYPE_WEEKEND] = PROGRAM_TYPE_WEEKEND;

		ksort($programTypesAllowed);

		debug($programTypesAllowed);

		$programTypess = $this->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $programTypesAllowed)));

		if (count($this->college_ids) == 1 && $this->role_id == ROLE_COLLEGE && (in_array(REMEDIAL_PROGRAM_NATURAL_COLLEGE_ID, $this->college_ids) || in_array(REMEDIAL_PROGRAM_SOCIAL_COLLEGE_ID, $this->college_ids))) {
			$programs = $this->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => Configure::read('programs_available_for_registrar_college_level_permissions'))));
			//$programTypes = $this->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => PROGRAM_TYPE_REGULAR)));
			$remedial = 1;
			//$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1, /* 'Department.college_id' => $this->college_id */)));
		} else {
			$programs = $this->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE)));
			//$programTypes = $this->PublishedCourse->ProgramType->find('list');
			//$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1)));
		}

		$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1)));

		$this->set('remedial', $remedial);
		$this->set(compact('sections', 'departments', 'programs', 'programTypess'));
	}

	function college_unpublish_course()
	{
		if (!empty($this->request->data) && isset($this->request->data['getsection'])) {
			$everythingfine = false;

			switch ($this->request->data) {
				case empty($this->request->data['PublishedCourse']['academic_year']):
					$this->Flash->error('Please select the academic year you want to publish courses.');
					break;
				case empty($this->request->data['PublishedCourse']['semester']):
					$this->Flash->error('Please select the semester you want to unpublished courses.');
					break;
				default:
					$everythingfine = true;
			}

			if ($everythingfine) {
				// Function to load/save search criteria.
				$this->__init_search();

				$publishedcourses = $this->PublishedCourse->find('all', array(
					'conditions' => array(
						'PublishedCourse.college_id' => $this->college_id,
						'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
						'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
						'PublishedCourse.published' => 1, 
						'PublishedCourse.department_id is null'
					), 
					'contain' => array(
						'Course' => array(
							'id', 
							'course_title', 
							'course_code', 
							'credit', 
							'lecture_hours', 
							'tutorial_hours',
							'laboratory_hours'
						), 
						'YearLevel' => array('id', 'name'), 
						'Section' => array('id', 'name'), 
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name')
					)
				));

				if (empty($publishedcourses)) {
					$this->Flash->error('There is no published course(s) in the given criteria, Please select a different criteria.');
				} else {

					$program_id = $this->request->data['PublishedCourse']['program_id'];
					$program_type_id = $this->request->data['PublishedCourse']['program_type_id'];
					
					$this->set('turn_off_search', true);
					$this->set('show_unpublish_page', true);
					$this->set('publishedcourses', $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, null, $publishedcourses, $this->college_id));
					$this->set(compact('program_id', 'program_type_id'));
				}
			} else {
				//$this->redirect(array('action'=>'add'));
			}
		}

		//delete publisehd courses
		if (!empty($this->request->data) && isset($this->request->data['deleteselected'])) {

			$count = 0;
			$courses_ids = array();
			$section_ids = array();

			foreach ($this->request->data['Course']['pub'] as $section_id => $selected_courses) {
				$section_ids[] = $section_id;
				foreach ($selected_courses as $course_id => $selected_flag) {
					if ($selected_flag == 1) {
						$count++;
						$courses_ids[$section_id][] = $course_id;
					}
				}
			}

			if ($count == 0) {
				$this->Flash->error('Please select atleast one course you want to delete.');
			} else {
				$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');
				//debug($this->request->data);
				$courses_not_allowed = array();
				$courses_allowed_to_delete = array();

				foreach ($section_ids as $kk => $sv) {
					if (isset($courses_ids[$sv])) {
						$publishedcourses = $this->PublishedCourse->find('all', array(
							'conditions' => array(
								'PublishedCourse.college_id' => $this->college_id,
								// 'PublishedCourse.department_id'=>$this->department_id,
								'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
								'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
								'PublishedCourse.published' => 1,
								'PublishedCourse.course_id' => $courses_ids[$sv],
								'PublishedCourse.section_id' => $sv,
								'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
								'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%'
							),
							'contain' => array(
								'CourseRegistration' => array(
									'Student' => array(
										'fields' => array('Student.full_name')
									)
								)
							),
						));

						if (!empty($publishedcourses)) {
							foreach ($publishedcourses as $pk => $pv) {
								// debug($pv);
								if (count($pv['CourseRegistration']) == 0) {
									$courses_allowed_to_delete[$sv][$pk] = $pv['PublishedCourse']['id'];
									//$courses_allowed_to_delete[$pk]['course_id']=$pv['PublishedCourse']['course_id'];
								} else {
									$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['id'];
									$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['course_id'];
								}
							}
						}
					}
				}

				//iterate section by section
				if (count($courses_not_allowed) > 0) {
					$this->Flash->error('You can not delete the red marked course(s) , students already registered for the course(s). Please uncheck the red marked course(s).');
					$this->set(compact('courses_not_allowed'));
				} else {
					//$count_deleted_record=count($courses_allowed_to_delete);
					$count_delete_record = array();

					foreach ($section_ids as $key => $section_id) {
						if (isset($courses_allowed_to_delete[$section_id])) {
							if ($this->PublishedCourse->deleteAll(array('PublishedCourse.id' => $courses_allowed_to_delete[$section_id], 'PublishedCourse.section_id' => $section_id))) {
								$count_delete_record[$section_id] = count($courses_allowed_to_delete[$section_id]);
							}
						}
					}

					if (!empty($count_delete_record)) {
						$sum = 0;

						foreach ($count_delete_record as $sec_id => $del_count) {
							$sum += $del_count;
						}

						if ($sum > 0) {
							$this->Flash->success('You have deleted ' . $sum . '  published course(s).');
						} else {
							$this->Flash->error('Published course was not deleted. Pleas try again.');
						}
					}
				}
			}

			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$publishedcourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.college_id' => $this->college_id,
					'PublishedCourse.department_id is null',
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
					'PublishedCourse.published' => 1
				)));

			$this->set('turn_off_search', true);
			$this->set('show_unpublish_page', true);
			$this->set('publishedcourses', $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, null, $publishedcourses, $this->college_id));
		}

		//publish as drop coures drop publisehd courses
		if (!empty($this->request->data) && isset($this->request->data['dropselected'])) {

			$count = 0;
			$courses_ids = array();
			$section_ids = array();

			if (!empty($this->request->data['Course']['pub'])) {
				foreach ($this->request->data['Course']['pub'] as $section_id => $selected_courses) {
					$section_ids[] = $section_id;
					foreach ($selected_courses as $course_id => $selected_flag) {
						if ($selected_flag == 1) {
							$count++;
							$courses_ids[$section_id][] = $course_id;
						}
					}
				}
			}

			if ($count == 0) {
				$this->Flash->error('Please select atleast one course you want to publish as drop course.');
			} else {

				$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');
				$courses_not_allowed = array();
				$courses_allowed_to_drop = array();

				if (!empty($section_ids)) {
					foreach ($section_ids as $kk => $sv) {
						if (isset($courses_ids[$sv])) {
							$publishedcourses = $this->PublishedCourse->find('all', array(
								'conditions' => array(
									'PublishedCourse.department_id is null', 'PublishedCourse.year_level_id' => 0,
									'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
									'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
									'PublishedCourse.published' => 1,
									'PublishedCourse.course_id' => $courses_ids[$sv],
									'PublishedCourse.section_id' => $sv,
									'PublishedCourse.drop' => 0,
									'PublishedCourse.semester' => $this->request->data['PublishedCourse']['semester'],
									'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%'
								),
								'contain' => array(
									'CourseRegistration' => array(
										'Student' => array(
											'fields' => array(
												'Student.full_name',
											)
										)

									)
								)
							));

							if (!empty($publishedcourses)) {
								foreach ($publishedcourses as $pk => $pv) {
									if (count($pv['CourseRegistration']) > 0) {
										$courses_allowed_to_drop[$sv][$pk] = $pv['PublishedCourse']['id'];
										//$courses_allowed_to_delete[$pk]['course_id']=$pv['PublishedCourse']['course_id'];
									} else {
										$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['id'];
										$courses_not_allowed[$sv][$pk] = $pv['PublishedCourse']['course_id'];
									}
								}
							}
						}
					}
				}

				if (count($courses_not_allowed) > 0) {
					$this->Flash->error('You can not publish as drop courses of the red marked courses , no student has registred for it. Please check the red marked courses and press delete button at the bottom if you intend to  make it invisible for registration');
					$this->set(compact('courses_not_allowed'));
				} else {
					// reformat courses_allowed to unpublished
					$save_reformat_drop_published_courses = array();

					if (!empty($section_ids)) {
						$count_drop = 0;
						foreach ($section_ids as $section_index => $section_id) {
							if (isset($courses_allowed_to_drop[$section_id])) {
								foreach ($courses_allowed_to_drop[$section_id] as $index => $course_id) {
									$save_reformat_drop_published_courses['PublishedCourse'][$count_drop]['id'] = $course_id;
									$save_reformat_drop_published_courses['PublishedCourse'][$count_drop]['published'] = 1;
									$save_reformat_drop_published_courses['PublishedCourse'][$count_drop]['drop'] = 1;
									$count_drop++;
								}
							}
						}

						//save the drop published coureses
						if (count($save_reformat_drop_published_courses['PublishedCourse']) > 0) {
							if ($this->PublishedCourse->saveAll($save_reformat_drop_published_courses['PublishedCourse'], array('validate' => 'first'))) {
								$this->Flash->success('Among selected  courses ' . $count_drop . ' has been published as drop course.');
								// $academic_year = str_replace('/','-', $this->request->data['PublishedCourse']['academic_year']);
								// $this->redirect(array('action' => 'index', $this->request->data['PublishedCourse']['semester'],$academic_year));
							} else {
								$this->Flash->error('The course could not be published as drop coures. Please, try again.');
							}
						} else {
							$this->Flash->error('Internal Error for publishing as drop.');
						}
					}
				}
			}

			$this->request->data['PublishedCourse'] = $this->Session->read('search_data_published_course');

			$publishedcourses = $this->PublishedCourse->find('all', array(
				'conditions' => array(
					'PublishedCourse.department_id is null',
					'PublishedCourse.college_id' => $this->college_id,
					'PublishedCourse.year_level_id' => 0,
					'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id'],
					'PublishedCourse.program_type_id' => $this->request->data['PublishedCourse']['program_type_id'],
					'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
					'PublishedCourse.published' => 1, 'PublishedCourse.drop' => 0
				)
			));

			// $section_organized_published_courses = $this->PublishedCourse->getSectionofPublishedCourses($this->request->data,$this->department_id, $publishedcourses);
			$section_organized_published_courses = $this->PublishedCourse->getSectionofPublishedCourses($this->request->data, null, $publishedcourses, $this->college_id);

			$this->set('turn_off_search', true);
			$this->set('show_unpublish_page', true);
			$this->set('publishedcourses', $section_organized_published_courses);
		}

		$programTypesAllowed = Configure::read('program_types_available_for_registrar_college_level_permissions');
		
		if (isset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING])) {
			unset($programTypesAllowed[PROGRAM_TYPE_ADVANCE_STANDING]);
		}

		$programTypesAllowed[PROGRAM_TYPE_EVENING] = PROGRAM_TYPE_EVENING;
		$programTypesAllowed[PROGRAM_TYPE_WEEKEND] = PROGRAM_TYPE_WEEKEND;

		ksort($programTypesAllowed);

		debug($programTypesAllowed);

		$programTypess = $this->PublishedCourse->Section->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $programTypesAllowed)));

		if (count($this->college_ids) == 1 && $this->role_id == ROLE_COLLEGE && (in_array(REMEDIAL_PROGRAM_NATURAL_COLLEGE_ID, $this->college_ids) || in_array(REMEDIAL_PROGRAM_SOCIAL_COLLEGE_ID, $this->college_ids))) {
			$programs = $this->PublishedCourse->Section->Program->find('list', array('conditions' => array('Program.id' => Configure::read('programs_available_for_registrar_college_level_permissions'))));
		} else {
			$programs = $this->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE)));
			//$programTypes = $this->PublishedCourse->ProgramType->find('list');
			$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.active' => 1)));
		}

		//$programs = $this->PublishedCourse->Program->find('list');
		//$programTypes = $this->PublishedCourse->ProgramType->find('list');

		$this->set(compact('programs', 'programTypess'));
	}

	function college_attache_scale()
	{
		if (!empty($this->request->data) && isset($this->request->data['attachescaletocourse'])) {
			$data['PublishedCourse'] = $this->request->data['Published'];
			if (empty($data['PublishedCourse'])) {
				$this->Flash->info('No course has been attached to any scale.');
			} else {
				if ($this->PublishedCourse->saveAll($data['PublishedCourse'], array('validate' => 'first'))) {
					$this->Flash->success('The grade scale has been saved');
					//$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The grade scale could not be saved. Please, try again.');
				}
			}
			
			$this->request->data['getPublishedCourseList'] = true;
		}

		if (!empty($this->request->data) && isset($this->request->data['getPublishedCourseList'])) {

			$everythingfine = false;

			switch ($this->request->data) {
				case empty($this->request->data['PublishedCourse']['academic_year']):
					$this->Flash->error('Please select the academic year you want to attach scale.');
					break;
				case empty($this->request->data['PublishedCourse']['semester']):
					$this->Flash->error('Please select the semester you want to attach scale.');
					break;
				case empty($this->request->data['PublishedCourse']['program_id']):
					$this->Flash->error('Please select the program you want to unpublished courses.');
					break;
				case empty($this->request->data['PublishedCourse']['department_id']):
					$this->Flash->error('Please select the department you want attach scale.');
					break;
				default:
					$everythingfine = true;
			}

			if ($everythingfine) {
				if (strcasecmp($this->request->data['PublishedCourse']['department_id'], 'pre') === 0) {
					$publishedCourses = $this->PublishedCourse->find('all', array(
						'fields' => array('id', 'section_id', 'grade_scale_id'), 
						'conditions' => array(
							'PublishedCourse.semester ' => $this->request->data['PublishedCourse']['semester'],
							'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
							'PublishedCourse.drop' => 0,
							'PublishedCourse.department_id is null',
							'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id']
						),
						'contain' => array(
							'Course' => array('fields' => array('id', 'course_title', 'credit', 'course_code', 'grade_type_id')),
							'Section' => array(
								'fields' => array('id', 'name'),
								'conditions' => array('Section.archive <> ' => 1), 
								'ProgramType' => array('fields' => array('id', 'name'))
							)
						)
					));
				} else {
					$publishedCourses = $this->PublishedCourse->find('all', array(
						'fields' => array(
							'id', 
							'section_id', 
							'grade_scale_id'
						), 
						'conditions' => array(
							'PublishedCourse.semester ' => $this->request->data['PublishedCourse']['semester'],
							'PublishedCourse.academic_year LIKE ' => $this->request->data['PublishedCourse']['academic_year'] . '%',
							'PublishedCourse.drop' => 0,
							'PublishedCourse.department_id' => $this->request->data['PublishedCourse']['department_id'],
							'PublishedCourse.program_id' => $this->request->data['PublishedCourse']['program_id']
						),
						'contain' => array(
							'Course' => array('fields' => array('id', 'course_title', 'credit', 'course_code', 'grade_type_id')),
							'Section' => array(
								'fields' => array('id', 'name'),
								'conditions' => array('Section.archive <> ' => 1), 
								'ProgramType' => array('fields' => array('id', 'name'))
							)
						)
					));
				}

				if (!empty($publishedCourses)) {
					if (strcasecmp($this->request->data['PublishedCourse']['department_id'], 'pre') === 0 && $this->request->data['PublishedCourse']['program_id'] == PROGRAM_UNDEGRADUATE) {
						$gradeScales = $this->PublishedCourse->GradeScale->find('all', array(
								'conditions' => array(
									'GradeScale.model' => 'College',
									'GradeScale.foreign_key' => $this->college_id,
									'GradeScale.active' => 1,
									// 'GradeScale.own'=>1,
									'GradeScale.own' => 0,
									'GradeScale.program_id' => $this->request->data['PublishedCourse']['program_id']
								),
								'fields' => array('id', 'name'),
								'contain' => array(
									'GradeScaleDetail' => array(
										'Grade' => array(
											'fields' => array('id', 'grade'),
											'GradeType' => array('id', 'type')
										)
									), 
									'Program' => array('fields' => array('id', 'name'))
								)
							)
						);
					} else {
						$gradeScales = $this->PublishedCourse->GradeScale->find('all', array(
							'conditions' => array(
								'GradeScale.model' => 'College',
								'GradeScale.foreign_key' => $this->college_id,
								'GradeScale.active' => 1,
								'GradeScale.own' => 0,
								'GradeScale.program_id' => $this->request->data['PublishedCourse']['program_id']
							),
							'fields' => array('id', 'name'),
							'contain' => array(
								'GradeScaleDetail' => array(
									'Grade' => array(
										'fields' => array('id', 'grade'),
										'GradeType' => array('id', 'type')
									)
								),
								'Program' => array(
									'fields' => array('id', 'name')
								)
							)
						));
					}

					$find_delegation_program_ids = $this->PublishedCourse->College->find('first', array(
						'conditions' => array(
							'College.id' => $this->college_id
						), 
						'fields' => array(
							'deligate_for_graduate_study', 
							'deligate_scale'
						)
					));

					if (empty($gradeScales)) {
						if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0 && $this->request->data['PublishedCourse']['program_id'] == PROGRAM_POST_GRADUATE) {
							$this->Flash->error(' There is no grade scale in the system that are defined, please define scale before attaching grade scale to published courses.');
							$this->redirect(array('controller' => 'gradeScales', 'action' => 'set_grade_scale'));
						} else if ($find_delegation_program_ids['College']['deligate_scale'] == 0 && PROGRAM_UNDEGRADUATE == $this->request->data['PublishedCourse']['program_id']) {
							$this->Flash->error('There is no grade scale in the system that are defined, please define scale before attaching grade scale to published courses.');
							$this->redirect(array('controller' => 'gradeScales', 'action' => 'set_grade_scale'));
						} else {
							if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $this->request->data['PublishedCourse']['program_id'] == PROGRAM_POST_GRADUATE) {
								$this->Flash->error('You have delegated attaching grade scale to department for postgraduate program.');
							}
							if ($find_delegation_program_ids['College']['deligate_scale'] == 1 && $this->request->data['PublishedCourse']['program_id'] == PROGRAM_POST_GRADUATE ) {
								$this->Flash->error('You have delegated attaching grade scale to department for undergraduate program.');
							}
						}
					}

					$return = array();

					if (!empty($gradeScales)) {
						foreach ($gradeScales as $kk => $vv) {
							if (!empty($vv['GradeScaleDetail'][0]['Grade']['GradeType']['type'])) {
								$return[$vv['GradeScaleDetail'][0]['Grade']['grade_type_id']][$vv['GradeScale']['name'] . '-' . $vv['GradeScaleDetail'][0]['Grade']['GradeType']['type'] . '-' . $vv['Program']['name']][$vv['GradeScale']['id']] = $vv['GradeScale']['name'];
							}
						}
					}

					$gradeScales = $return;
					$section_organized_published_courses = $this->PublishedCourse->get_section_organized_published_courses_scale_attachment($this->request->data, $this->request->data['PublishedCourse']['department_id'], $publishedCourses, $this->college_id);

					if (empty($section_organized_published_courses)) {
						$this->Flash->error('There is no published courses in the given criteria that needs scale attachment.');
					}
					$this->set(compact('section_organized_published_courses', 'gradeScales'));

				} else {
					$this->Flash->error('There is no published courses in the given criteria that needs scale attachment.');
				}
			}
		}

		$beginning['pre'] = 'Pre/Fresh';

		$find_delegation_program_ids = $this->PublishedCourse->College->find('first', array(
			'conditions' => array(
				'College.id' => $this->college_id
			), 
			'fields' => array(
				'deligate_for_graduate_study', 
				'deligate_scale'
			)
		));

		if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0 && $find_delegation_program_ids['College']['deligate_scale'] == 0) {
			$programs = $this->PublishedCourse->Program->find('list');
			$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id)));
		} else if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 0) {
			$programs = $this->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE)));
			$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id)));
		} else if (
			$find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0 && $find_delegation_program_ids['College']['deligate_scale'] == 1) {
			$programs = $this->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => PROGRAM_POST_GRADUATE)));
			$departments = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id)));
		} else if (
			$find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 1 ) {
			$programs = $this->PublishedCourse->Program->find('list', array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE)));
			$programs['delegation'] = 'Delegated To Department';
			$departments = array();
		}

		$departments = $beginning + $departments;
		$gradeTypes = $this->PublishedCourse->Course->GradeType->find('list');

		$this->set(compact('programs', 'departments', 'gradeTypes'));
	}
}