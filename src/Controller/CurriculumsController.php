<?php
class CurriculumsController extends AppController
{
	var $name = 'Curriculums';
	var $helpers = array('DatePicker', 'Media.Media');
	public $paginate = array();

	var $menuOptions = array(
		'title' => 'Curriculums',
		'exclude' => array(
			'get_curriculums', 
			'get_courses', 
			'get_course_category_combo', 
			'search', 
			'deleteCourseCategory', 
			'get_curriculum_combo', 
			'lock',
			'approve',
			'activate',
			'add_departmernt_study_program_for_curriculum',
			'get_curriculums_based_on_program_combo'
		),
		'alias' => array(
			'index' => 'List Curriculums',
			'add' => 'Add New Curricula'
		)
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			'get_curriculums',
			'get_courses',
			'get_course_category_combo',
			//'add_departmernt_study_program_for_curriculum',
			'get_curriculum_combo',
			'get_curriculums_based_on_program_combo',
			'search'
		);
	}

	public function beforeRender()
	{
		parent::beforeRender();
		
		$yearLevels = $this->year_levels;

		if ($this->role_id == ROLE_DEPARTMENT) {
			$yearLevels =  ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => $yearLevels)));
		} /* else {
			$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
			$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));
			$this->set(compact('program_types', 'programTypes', 'programs'));
		} */

		$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));
		$this->set(compact('program_types', 'programTypes', 'programs', 'yearLevels'));
		//$this->set(compact('yearLevels'));
	}

	function __init_search_curriculum()
	{
		if (!empty($this->request->data['Curriculum'])) {
			$search_session = $this->request->data['Curriculum'];
			$this->Session->write('Curriculum.search_data_curriculum', $search_session);
		} else {
			if ($this->Session->check('Curriculum.search_data_curriculum')) {
				debug($this->Session->read('Curriculum.search_data_curriculum'));
				$search_session = $this->Session->read('Curriculum.search_data_curriculum');
				$this->request->data['Curriculum'] = $search_session;
			}
		}
	}

	function __init_clear_session_filters($data = null)
	{
		/* if (!empty($this->request->data['Search'])) {
			unset($this->request->data['Search']);
		} */

		if ($this->Session->check('Curriculum.search_data_curriculum')) {
			$this->Session->delete('Curriculum.search_data_curriculum');
		}

		//return $this->redirect(array('action' => 'index', $data));

	}

	function search()
	{
		$this->__init_search_curriculum();

		$url['action'] = 'index';

		if (isset($this->request->data) && !empty($this->request->data)) {
			foreach ($this->request->data as $k => $v) {
				if (!empty($v)) {
					foreach ($v as $kk => $vv) {
						if (!empty($vv) && is_array($vv)) {
							foreach ($vv as $kkk => $vvv){
								$url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
							}
						} else {
							$url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
						}
					}
				}
			}
		}

		return $this->redirect($url, null, true);
	}

	function index($data = null)
	{
		//$this->__init_search();
		$this->__init_search_curriculum();
		$active = '';
		$page = '';

		if (isset($this->passedArgs) && !empty($this->passedArgs)) {
			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['Curriculum']['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$this->request->data['Curriculum']['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$this->request->data['Curriculum']['direction'] = $this->passedArgs['direction'];
			}
		}

		if (isset($data) && !empty($data['Curriculum'])) {
			$this->request->data = $data['Curriculum'];
			$this->__init_search_curriculum();
		}

		if (isset($this->request->data['search'])) {
			unset($this->passedArgs);
			$this->__init_search_curriculum();
			$this->__init_clear_session_filters($this->request->data);
		}

		$options = array();

		debug($this->request->data);

		if (!empty($this->request->data)) {

			if (!empty($page) && !isset($this->request->data['search'])) {
				$this->request->data['Curriculum']['page'] = $page;
			}

			$this->__init_search_curriculum();

			if ($this->request->data['Curriculum']['active'] == '1') {
				$active = 1;
			} else if ($this->request->data['Curriculum']['active'] == '0') {
				$active = 0;
			} else {
				$active =  array(0, 1);
			}

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$options['conditions'][] = array('Curriculum.department_id' => $this->department_id);
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				$department_ids = $this->Curriculum->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1), 'fields' => array('Department.id', 'Department.id')));

				if (!empty($this->request->data['Curriculum']['department_id'])) {
					$options['conditions'][] = array('Curriculum.department_id' => $this->request->data['Curriculum']['department_id']);
				} else {
					$options['conditions'][] = array('Curriculum.department_id' => $department_ids);
				}

				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
				
				if (!empty($this->request->data['Curriculum']['department_id'])) {
					$options['conditions'][] = array('Curriculum.department_id' => $this->request->data['Curriculum']['department_id'], 'Curriculum.program_id' => $this->program_ids);
				} else if (empty($this->request->data['Curriculum']['department_id']) && !empty($this->request->data['Curriculum']['college_id']) ) {
					$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Curriculum']['college_id'], 'Department.active' => 1)));
					$options['conditions'][] = array('Curriculum.department_id' => array_keys($departments), 'Curriculum.program_id' => $this->program_ids);
				} else {
					$options['conditions'][] = array('Curriculum.department_id' => $this->department_ids, 'Curriculum.program_id' => $this->program_ids);
				}

				$college_ids = $this->Curriculum->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1), 'fields' => array('Department.college_id')));
				$colleges = $this->Curriculum->Department->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1)));

			} else {

				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Curriculum->Department->College->find('list', array('conditions' => array('College.active' => 1)));

				if (!empty($this->request->data['Curriculum']['department_id'])) {
					$options['conditions'][] = array('Curriculum.department_id' => $this->request->data['Curriculum']['department_id']);
				} else if (empty($this->request->data['Curriculum']['department_id']) && !empty($this->request->data['Curriculum']['college_id']) ) {
					$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Curriculum']['college_id'], 'Department.active' => 1)));
					$options['conditions'][] = array('Curriculum.department_id' => array_keys($departments));
				} else {
					$options['conditions'][] = array('Curriculum.department_id' => array_keys($departments));
				}
			}

			if (!empty($this->request->data['Curriculum']['program_id'])) {
				$options['conditions'][] = array('Curriculum.program_id' => $this->request->data['Curriculum']['program_id']);
			}

			$options['conditions'][] = array('Curriculum.active' => $active);

			if (!empty($this->request->data['Curriculum']['college_id']) ) {
				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Curriculum']['college_id'], 'Department.active' => 1)));
			}

		} else {

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
				$options['conditions'][] = array('Curriculum.department_id' => array_keys($departments) , 'Curriculum.active' => 1);
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
				$options['conditions'][] = array('Curriculum.department_id' => $this->department_id, 'Curriculum.active' => 1);
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				if (!empty($this->department_ids)) {
					$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
					$college_ids = $this->Curriculum->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1 ), 'fields' => array('Department.college_id')));
					$colleges = $this->Curriculum->Department->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1)));
					$options['conditions'][] = array('Curriculum.department_id' => $this->department_ids, 'Curriculum.program_id' => $this->program_id, 'Curriculum.active' => 1);
				}
			} else {
				$departments = $this->Curriculum->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Curriculum->Department->College->find('list', array('conditions' => array('College.active' => 1)));
				$options['conditions'][] = array('Curriculum.department_id' => array_keys($departments));
			}
		}

		//debug($options['conditions']);

		if (!empty($options['conditions'])) {

			$this->Paginator->settings =  array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Department' => array(
						'fields' => array(
							'Department.id', 
							'Department.name', 
							'Department.shortname', 
							'Department.college_id'
						)
					),
					'Student' => array(
						'fields' => array(
							'Student.id', 
							'Student.full_name'
						),
						'limit' => 1
					),
					'Program' => array(
						'fields' => array(
							'Program.id', 
							'Program.name',
							'Program.shortname',
						)
					),
					'DepartmentStudyProgram' => array(
						'fields' => array(
							'DepartmentStudyProgram.id', 
							'DepartmentStudyProgram.study_program_id'
						),
						'StudyProgram' => array('fields' => array('StudyProgram.id', 'StudyProgram.study_program_name', 'StudyProgram.code')),
						'ProgramModality' => array('fields' => array('ProgramModality.id', 'ProgramModality.modality', 'ProgramModality.code')),
						//'Qualification'  => array('fields' => array('Qualification.id', 'Qualification.qualification', 'Qualification.code')),
					)
				), 
				'order' => array(
					'Curriculum.department_id',
					'Curriculum.program_id',
					'Curriculum.year_introduced',
				),
				'limit' => 100,
				'maxLimit' => 1000,
				'recursive'=> -1,
				'page' => $page
			);


			//$result_curriculums = $this->paginate($options['conditions']);

			try {
				$result_curriculums = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('result_curriculums'));
				//$result_curriculums = $this->paginate($options['conditions']);
			} catch (NotFoundException $e) {
				unset($this->request->data['Curriculum']['page']);
				unset($this->request->data['Curriculum']['sort']);
				unset($this->request->data['Curriculum']['direction']);
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index', $this->request->data));
			} catch (Exception $e) {
				unset($this->request->data['Curriculum']['page']);
				unset($this->request->data['Curriculum']['sort']);
				unset($this->request->data['Curriculum']['direction']);
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index', $this->request->data));
			}

			/* try {
				$this->set('results', $this->Paginator->paginate($this->modelClass));
			} catch (NotFoundException $e) {
				return $this->redirect(array('action' => 'index', '?' => $this->request->query, 'page' => 1));
			} catch (Exception $e) {
				return $this->redirect(array('action' => 'index', '?' => $this->request->query, 'page' => 1));
			} */

		} else {
			$result_curriculums = array();
			$this->set(compact('result_curriculums'));
		}

		//debug($result_curriculums);
			
		if (empty($result_curriculums) && !empty($options['conditions'])) {
			$this->Flash->info('No Curriculum found based on the the given search criteria.');
		}

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$department_college_id = $this->Curriculum->Department->field('Department.college_id', array('Department.id' => $this->department_id));
			$college_type = $this->Curriculum->Department->College->field('College.type', array('College.id' => $department_college_id));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			$college_type = $this->Curriculum->Department->College->field('College.type', array('College.id' => $this->college_id));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			$programs = $this->Curriculum->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
			$college_type = '';
		} else {
			$college_type = '';
		}
		
		$programs =  $this->Curriculum->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->Curriculum->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$this->set(compact('colleges', 'college_type', 'programs', 'departments'/* , 'result_curriculums' */));
	}

	public function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid curriculum.');
			return $this->redirect(array('action' => 'index'));
		}

		$curriculum = $this->Curriculum->find('first', array(
			'conditions' => array(
				'Curriculum.id' => $id
			), 
			'contain' => array(
				'Attachment', 
				'CourseCategory', 
				'Course' => array(
					'order' => 'Course.year_level_id ASC, Course.semester ASC, Course.course_title ASC',
					'CourseCategory' => array('fields' => array('id', 'name')), 
					'YearLevel' => array('id', 'name'), 
					'GradeType' => array('fields' => array('id', 'type')), 
					'Prerequisite' => array(
						'Course', 
						'PrerequisiteCourse'
					)
				), 
				'Department' => array('fields' => array('id', 'name')),
				'Program' => array('fields' => array('id', 'name')),
				'DepartmentStudyProgram' => array(
					'fields' => array(
						'DepartmentStudyProgram.id', 
						'DepartmentStudyProgram.study_program_id'
					),
					'StudyProgram' => array('fields' => array('StudyProgram.id', 'StudyProgram.study_program_name', 'StudyProgram.code')),
					'ProgramModality' => array('fields' => array('ProgramModality.id', 'ProgramModality.modality', 'ProgramModality.code')),
					'Qualification'  => array('fields' => array('Qualification.id', 'Qualification.qualification', 'Qualification.code')),
				)
			),
			'recursive'=> -1
		));

		$this->set('curriculum', $curriculum);
	}

	public function add()
	{
		$tmpCurr = array();

		if (!empty($this->request->data) && !empty($this->request->data['saveCurriculum'])) {
			
			$this->Curriculum->create();

			//$this->request->data = $this->Curriculum->preparedAttachment($this->request->data);
			//debug($this->request->data);
			$tmpCurr = $this->request->data;
			
			if (isset($this->request->data['Attachment']) && !empty($this->request->data['Attachment'])) {
				$this->request->data = $this->Curriculum->preparedAttachment($this->request->data);
			} 

			debug($this->request->data);

			if (!empty($this->request->data['Curriculum'])) {
				if ($this->Curriculum->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Flash->success($this->request->data['Curriculum']['name'] . ' been saved. You can make any required modifications or updates.');

					// add the program_id in session/ allowed program_ids if it is new, neway.
					if (!in_array($this->request->data['Curriculum']['program_id'], $this->program_ids)) {
						$this->program_ids = $this->program_ids + array($this->request->data['Curriculum']['program_id'] => $this->request->data['Curriculum']['program_id']);
					}
					
					return $this->redirect(array('action' => 'edit', $this->Curriculum->id));
					//$this->Flash->success('The curriculum has been saved');
					//return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The curriculum could not be saved. Please, try again.');
					$this->request->data = $tmpCurr;
				}
			} else {
				$this->Flash->error('The curriculum could not be saved. Please, try again.');
				return $this->redirect(array('action' => 'index'));
			}
		}

		//To copy curriculum from selected curriculum new curriculum
		if (!empty($this->request->data) && isset($this->request->data['copyCurriculum'])) {
			if (!empty($this->request->data['Curriculum']['from_curriculum'])) {

				$curriculums = $this->Curriculum->find('first', array('conditions' => array('Curriculum.id' => $this->request->data['Curriculum']['from_curriculum']), 'contain' => array('CourseCategory')));
				$formatCurriculumForSaveAll['Curriculum'] = $curriculums['Curriculum'];
				$formatCurriculumForSaveAll['Curriculum']['name'] = $curriculums['Curriculum']['name'] . '(copy)';

				// Un approve approvals if approved so that it is treated as new curriculum and must pass validation by the registrar
				$formatCurriculumForSaveAll['Curriculum']['lock'] = 0;
				$formatCurriculumForSaveAll['Curriculum']['registrar_approved'] = 0;

				$formatCurriculumForSaveAll['Curriculum']['created'] = date('Y-m-d H:i:s');
				$formatCurriculumForSaveAll['Curriculum']['modified'] = date('Y-m-d H:i:s');
				
				unset($formatCurriculumForSaveAll['Curriculum']['id']);
				
				if (isset($formatCurriculumForSaveAll['Curriculum']['department_study_program_id'])) {
					unset($formatCurriculumForSaveAll['Curriculum']['department_study_program_id']);
				}

				$count = 0;
				
				if (!empty($curriculums['CourseCategory'])) {
					foreach ($curriculums['CourseCategory'] as $k => $v) {
						$formatCurriculumForSaveAll['CourseCategory'][$count]['name'] = $v['name'];
						$formatCurriculumForSaveAll['CourseCategory'][$count]['code'] = $v['code'];
						$formatCurriculumForSaveAll['CourseCategory'][$count]['mandatory_credit'] = $v['mandatory_credit'];
						$formatCurriculumForSaveAll['CourseCategory'][$count]['total_credit'] = $v['total_credit'];

						$count++;
					}
				}

				$newCurriculumID = null;

				if (!empty($formatCurriculumForSaveAll)) {

					$this->Curriculum->create();
					
					if ($this->Curriculum->saveAll($formatCurriculumForSaveAll, array('validate' => false))) {

						$newCurriculumID = $this->Curriculum->id;

						$copied_courses = $this->Curriculum->Course->find('all', array(
							'conditions' => array(
								'Course.curriculum_id' => $curriculums['Curriculum']['id']
							), 
							'contain' => array(
								'Book', 
								'Journal', 
								'Weblink', 
								'Prerequisite' => array('PrerequisiteCourse'), 
								'CourseCategory'
							), 
							//'limit' => 5000000,
							'order' => 'Course.year_level_id ASC, Course.semester ASC, Course.course_title ASC'
						));
						//debug(count($copied_courses));

						//unset empty prerequisite/Book/Journal and weblink data before save
						$newCopyCourses = $this->Curriculum->Course->saveAllFormatCopyCourse($copied_courses);

						$saveCourse = array();
						$count = 0;
						$prerequiteHold = array();

						if (!empty($newCopyCourses)) {
							foreach ($newCopyCourses as $each_courses) {
								$saveCourse = $each_courses;
								$saveCourse['Course']['curriculum_id'] = $this->Curriculum->id;

								$courseCategory = $this->Curriculum->CourseCategory->find('first', array(
									'conditions' => array(
										'CourseCategory.curriculum_id' => $this->Curriculum->id,
										'CourseCategory.name' => $each_courses['CourseCategory']['name'],
										'CourseCategory.code' => $each_courses['CourseCategory']['code'],
										'CourseCategory.total_credit' => $each_courses['CourseCategory']['total_credit'], 
										'CourseCategory.mandatory_credit' => $each_courses['CourseCategory']['mandatory_credit']
									)
								));

								//find previous prerqusite and and hold it for  new created course
								unset($saveCourse['Prerequisite']);

								if (!empty($each_courses['Prerequisite']) && !empty($each_courses['Prerequisite'])) {
									$preCount = 0;
									// debug($each_courses);
									foreach ($each_courses['Prerequisite'] as $k => $v) {
										$prerequite = $this->Curriculum->Course->find('first', array(
											'conditions' => array(
												'Course.curriculum_id' => $this->Curriculum->id,
												'Course.course_code' => $v['PrerequisiteCourse']['course_code'], 
												'Course.course_title' => $v['PrerequisiteCourse']['course_title']
											)
										));

										if (!empty($prerequite)) {
											$saveCourse['Prerequisite'][$preCount]['prerequisite_course_id'] = $prerequite['Course']['id'];
											//$saveCourse['Prerequisite'][$preCount]['co_requisite']=$v['PrerequisiteCourse']['co_requisite'];
										}
										$preCount++;
									}
								}

								$saveCourse['Course']['course_category_id'] = $courseCategory['CourseCategory']['id'];
								unset($saveCourse['CourseCategory']);

								if (isset($saveCourse['Course']['id']) && !empty($saveCourse['Course']['id'])) {
									unset($saveCourse['Course']['id']);
								}

								if (!empty($saveCourse)) {
									if ($this->Curriculum->Course->saveAll($saveCourse, array('validate' => false))) {
									}
								}

								$count++;
							}
						}

						$this->Flash->success($curriculums['Curriculum']['name'] . ' been copied successfully to ' . $formatCurriculumForSaveAll['Curriculum']['name'] . '. You can now make the required modifications or updates.');
						return $this->redirect(array('action' => 'edit', $newCurriculumID));
						//return $this->redirect(array('action' => 'index'));
					} else {
						debug($this->Curriculum->invalidFields());
						$this->Flash->error('The curriculum could not be saved. Please, try again.');
					}
				} else {
					$this->Flash->error('Please select the curriculum you want to copy. Please, try again.');
				}
			} else {
				$this->Flash->error('Please select the curriculum you want to copy. Please, try again.');
			}
		}

		$earlierCurriculums = $this->Curriculum->find('list', array('conditions' => array('Curriculum.department_id' => $this->department_id, 'Curriculum.active' => 1), 'order' => array('Curriculum.year_introduced' => 'DESC', 'Curriculum.created' => 'DESC')));
		$course_category_values = Configure::read('course_category_options');
		debug($course_category_values);

		$department_id = $this->department_id;
		$programsss =  $this->Curriculum->Program->find('list', array('conditions' => array(/* 'Program.id' => $this->program_ids,  */'Program.active' => 1)));
		$program_types = $programTypes =  $this->Curriculum->ProgramType->find('list', array('conditions' => array(/* 'ProgramType.id' => $this->program_type_ids, */ 'ProgramType.active' => 1)));

		
		$year_based_curriculum_allowed = $this->Curriculum->Department->field('allow_year_based_curriculums', array('Department.id' => $this->department_id));
		debug($year_based_curriculum_allowed);
		
		$this->set('year_based_curriculum_allowed', $year_based_curriculum_allowed);

		$this->set(compact(
			'departments',
			'programsss',
			'programTypes',
			'department_id',
			'course_category_values',
			'earlierCurriculums'
		));
	}

	public function edit($id = null)
	{
		$curriculum_exist = $this->Curriculum->find('count', array('conditions' => array('Curriculum.id' => $id)));
		
		if ($curriculum_exist == 0) {
			$this->Flash->error('Invalid curriculum ID.');
			$this->redirect(array('action' => 'index'));
		}

		$elgible_user = $this->Curriculum->find('count', array('conditions' => array('Curriculum.id' => $id, 'Curriculum.department_id' => $this->department_id)));

		if ($elgible_user == 0) {
			$this->Flash->error('You are not elgible to edit this curriculum.');
			$this->redirect(array('action' => 'index'));
		}

		$temp = array();
		$saveError = false;

		if (!empty($this->request->data) && isset($this->request->data['saveCurriculum'])) {
			debug($this->request->data);
			
			$temp = $this->request->data;

			//$this->request->data = $this->Curriculum->preparedAttachment($this->request->data);
			
			if (isset($this->request->data['Attachment']) && !empty($this->request->data['Attachment'])) {
				$this->request->data = $this->Curriculum->preparedAttachment($this->request->data);
			} 

			debug($this->request->data);

			if ($this->Curriculum->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Flash->success('The curriculum has been saved.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The curriculum could not be saved. Please, try again.');
				$saveError = true;
				$this->request->data = $temp;
			}
		}

		if (empty($this->request->data) || !($saveError)) {
			//$curriculum = $this->request->data = $this->Curriculum->read(null, $id);

			$curriculum = $this->request->data = $this->Curriculum->find('first', array(
				'conditions' => array(
					'Curriculum.id' => $id
				), 
				'contain' => array(
					'Department',
					'Program',
					'ProgramType',
					'Attachment',
					'CourseCategory',
					'DepartmentStudyProgram',
				),
				'recursive' => -1
			));

			debug($curriculum);
		}

		$qualification_ids = ClassRegistry::init('Qualification')->find('list', array('fields' => array('Qualification.id'), 'conditions' => array('Qualification.program_id' => (isset($curriculum['Curriculum']['program_id']) && !empty($curriculum['Curriculum']['program_id']) ? $curriculum['Curriculum']['program_id'] : $this->request->data['Curriculum']['program_id']))));
		debug($qualification_ids);

		//$qualification_id = ClassRegistry::init('Qualification')->field('Qualification.id', array('Qualification.program_id' => $curriculum['Curriculum']['program_id']));

		//$program_modality_ids = $this->Curriculum->ProgramType->find('list', array('fields' => array('ProgramType.program_modality_id'), 'conditions' => array('ProgramType.id' => $curriculum['Curriculum']['program_type_id'])));
		//$program_modality_id = $this->Curriculum->ProgramType->field('ProgramType.program_modality_id', array('ProgramType.id' => 1));
		//debug($program_modality_ids);

		$getDepartmentStudyProgramList = $this->Curriculum->getDepartmentStudyProgramDetails($this->department_id, null, $qualification_ids);

		debug($getDepartmentStudyProgramList);

		$department_id = $this->department_id;;
		$programs =  $this->Curriculum->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->Curriculum->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$programsss =  $this->Curriculum->Program->find('list', array('conditions' => array('Program.id' => $this->request->data['Curriculum']['program_id'], 'Program.active' => 1)));

		$year_based_curriculum_allowed = $this->Curriculum->Department->field('allow_year_based_curriculums', array('Department.id' => $this->department_id));
		debug($year_based_curriculum_allowed);
		$this->set('year_based_curriculum_allowed', $year_based_curriculum_allowed);

		$course_category_values = Configure::read('course_category_options');
		debug($course_category_values);

		$this->set(compact('departments', 'programsss', 'programs', 'programTypes', 'department_id', 'course_category_values', 'getDepartmentStudyProgramList'));
	}

	function deleteCourseCategory($id = null, $action_controller_id = null)
	{

		if (!empty($action_controller_id)) {
			$course_category = explode('~', $action_controller_id);
		
			debug($course_category);

			$this->Curriculum->CourseCategory->id = $id;

			if (!$this->Curriculum->CourseCategory->exists()) {
				$this->Flash->error('Invalid course category ID');
			}

			// wrong Implemetation, Neway
			//$doesCourseCategoryBelongs = $this->Curriculum->find('count', array('conditions' => array('Curriculum.id' => $course_category[2])));
			
			$doesCourseCategoryBelongsToCourses = ClassRegistry::init('Course')->find('count', array('conditions' => array('Course.course_category_id' => $id)));

			$categoryName = $this->Curriculum->CourseCategory->field('name', array('CourseCategory.id' => $id));

			if (!$doesCourseCategoryBelongsToCourses) {
				if ($this->Curriculum->CourseCategory->delete($id)) {
					$this->Flash->success('"' . $categoryName. '"  course category is now deleted.');
					if (!empty($course_category[0]) && !empty($course_category[1]) && !empty($course_category[2])) {
						$this->redirect(array('controller' => $course_category[1], 'action' => $course_category[0], $course_category[2]));
					}
					//$this->redirect(array('action' => 'index'));
				}
			} else {
				$this->Flash->warning('"' . $categoryName. '" course category is not deleted.' . ($doesCourseCategoryBelongsToCourses > 0 ? ' This course category is associated to ' . $doesCourseCategoryBelongsToCourses . ' courses.' : ' This course category is associated to courses.'));
			}

			if (!empty($course_category[0]) && !empty($course_category[1]) && !empty($course_category[2])) {
				$this->redirect(array('controller' => $course_category[1], 'action' => $course_category[0], $course_category[2]));
			} else {
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	function delete($id = null)
	{
		$curriculum_exist = $this->Curriculum->find('count', array('conditions' => array('Curriculum.id' => $id)));
		
		if ($curriculum_exist == 0) {
			$this->Flash->error('Invalid curriculum. Curriculum already deleted or not exists in the system.');
			$this->redirect(array('action' => 'index'));
		}

		$elgible_user = $this->Curriculum->find('count', array('conditions' => array('Curriculum.id' => $id, 'Curriculum.department_id' => $this->department_id)));

		if ($elgible_user == 0) {
			$this->Flash->error('You are not elgible to delete this curriculum.');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->Curriculum->canItBeDeleted($id)) {
			if ($this->Curriculum->delete($id)) {
				$this->Flash->success('Curriculum deleted.');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Flash->error('You can not delete this curriculum, It is attached to other models like Courses, Categories etc.');
			$this->redirect(array('action' => 'index'));
		}
	}

	function get_courses($curriculum_id = null)
	{
		$this->layout = 'ajax';

		if (!empty($curriculum_id)) {
			$courses = $this->Curriculum->Course->find('list', array(
				'conditions' => array(
					'Course.curriculum_id' => $curriculum_id
				), 
				'fields' => array('id', 'course_code_title')
			));
		} else {
			$model_name = array_keys($this->request->data);
			$courses = $this->Curriculum->Course->find('list', array(
				'conditions' => array(
					'Course.curriculum_id' => $this->request->data[$model_name[0]]['curriculum_id']
				), 
				'fields' => array('id', 'course_code_title')
			));
		}
		$this->set(compact('courses'));
	}

	function get_curriculums($department_id = null)
	{
		$this->layout = 'ajax';

		if (!empty($department_id)) {
			$curriculums = $this->Curriculum->find('list', array(
				'conditions' => array(
					'Curriculum.department_id' => $department_id
				), 
				'fields' => array('Curriculum.curriculum_detail'))
			);
			$this->set(compact('curriculums'));
		} else {

			$model_name = array_keys($this->request->data);

			$curriculums = $this->Curriculum->find('list', array(
				'conditions' => array(
					'Curriculum.department_id' => $this->request->data[$model_name[0]]['department_id']
				), 
				'fields' => array('Curriculum.curriculum_detail')
			));

			if (!empty($curriculums)) {
				foreach ($curriculums as $ck => $cv) {
					$courses = $this->Curriculum->Course->find('list', array(
						'conditions' => array(
							'Course.curriculum_id' => $ck
						), 
						'fields' => array(
							'id', 
							'course_code_title'
						)
					));
					break;
				}
			}
			$this->set(compact('curriculums', 'courses'));
		}
	}

	function get_curriculum_combo($department_id = null, $program_id = null, $appr = null)
	{
		$this->layout = 'ajax';
		
		$approved = [ 0 => 0, 1 => 1];
		
		if (isset($appr) && !empty($appr)) {
			if ($appr == 1) {
				$approved = [1 => 1];
			} else if ($appr == 0) {
				$approved = [ 0 => 0];
			}
		}

		if (!empty($department_id) && !empty($program_id)) {
			$curriculums = $this->Curriculum->find('list', array(
				'conditions' => array(
					'Curriculum.department_id' => $department_id,
					'Curriculum.program_id' => $program_id,
					'Curriculum.registrar_approved' => $approved,
					'Curriculum.active' => 1,
				), 
				'fields' => array('Curriculum.curriculum_detail')
			));
		} else if (!empty($department_id)) {
			$curriculums = $this->Curriculum->find('list', array(
				'conditions' => array(
					'Curriculum.department_id' => $department_id,
					'Curriculum.registrar_approved' => $approved,
					'Curriculum.active' => 1,
				), 
				'fields' => array('Curriculum.curriculum_detail')
			));
		} else if (!empty($program_id)) {
			if (!empty($this->department_id) && $this->role_id == ROLE_DEPARTMENT) {
				$curriculums = $this->Curriculum->find('list', array(
					'conditions' => array(
						'Curriculum.program_id' => $program_id,
						'Curriculum.department_id' => $this->department_id,
						'Curriculum.registrar_approved' => $approved,
						'Curriculum.active' => 1,
					), 
					'fields' => array('Curriculum.curriculum_detail')
				));
			} else {
				$curriculums = $this->Curriculum->find('list', array(
					'conditions' => array(
						'Curriculum.program_id' => $program_id,
						'Curriculum.registrar_approved' => $approved,
						'Curriculum.active' => 1,
					), 
					'fields' => array('Curriculum.curriculum_detail')
				));
			}
		} else {
			$curriculums = array();
		}
		$this->set(compact('curriculums'));
	}

	function get_course_category_combo($curriculum_id = null)
	{
		$this->layout = 'ajax';
		$courseCategories = $this->Curriculum->CourseCategory->find('list', array(
			'conditions' => array(
				'CourseCategory.curriculum_id' => $curriculum_id
			),
			'fields' => array(
				'CourseCategory.id', 
				'CourseCategory.name'
			)
		));
		$this->set(compact('courseCategories'));
	}

	public function lock($id = null)
	{
		$this->Curriculum->id = $id;

		if (!$this->Curriculum->exists()) {
			throw new NotFoundException(__('Invalid Curriculum '));
		}

		$data = array();
		$sort = 'Curriculum.department_id';
		$direction = 'asc';
		$page = '';

		if ($this->Session->check('Curriculum.search_data_curriculum')) {
			debug($this->Session->read('Curriculum.search_data_curriculum'));
			$search_session = $this->Session->read('Curriculum.search_data_curriculum');
			$data['Curriculum'] = $search_session;
			$page = (isset($data['Curriculum']['page']) ? $data['Curriculum']['page'] : '');
			$sort = (isset($data['Curriculum']['sort']) && !empty($data['Curriculum']['sort']) ? $data['Curriculum']['sort'] : 'Curriculum.department_id');
			$direction = (isset($data['Curriculum']['direction']) && !empty($data['Curriculum']['direction']) ? $data['Curriculum']['direction'] : 'asc');
		}

		$curriculums = $this->Curriculum->find('first', array('conditions' => array('Curriculum.id' => $this->Curriculum->id) , 'contain' => array('Attachment'), 'recursive' => -1));
		
		$lock = ($curriculums['Curriculum']['lock'] == 0 ? 1 : 0);
		$message = ($lock == 1 ? 'locked' : 'unlocked');

		$this->request->allowMethod('post', 'lock');

		if ($this->Curriculum->saveField('lock', $lock)) {
			$this->Flash->success('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum is now ' . $message . '.');
		} else {
			$this->Flash->error('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum could not be ' . $message . ' right now, please try again later.');
		}

		if (!empty($page)) {
			return $this->redirect(array('action' => 'index', $data, 'page' => $page, 'sort' => $sort, 'direction' => $direction));
		} else {
			return $this->redirect(array('action' => 'index', $data));
		}
		
	}


	public function approve($id = null)
	{
		$this->Curriculum->id = $id;

		if (!$this->Curriculum->exists()) {
			throw new NotFoundException(__('Invalid Curriculum '));
		}

		$data = array();
		$sort = 'Curriculum.department_id';
		$direction = 'asc';
		$page = '';

		if ($this->Session->check('Curriculum.search_data_curriculum')) {
			debug($this->Session->read('Curriculum.search_data_curriculum'));
			$search_session = $this->Session->read('Curriculum.search_data_curriculum');
			$data['Curriculum'] = $search_session;
			$page = (isset($data['Curriculum']['page']) ? $data['Curriculum']['page'] : '');
			$sort = (isset($data['Curriculum']['sort']) && !empty($data['Curriculum']['sort']) ? $data['Curriculum']['sort'] : 'Curriculum.department_id');
			$direction = (isset($data['Curriculum']['direction']) && !empty($data['Curriculum']['direction']) ? $data['Curriculum']['direction'] : 'asc');
		}

		$curriculums = $this->Curriculum->find('first', array('conditions' => array('Curriculum.id' => $this->Curriculum->id), 'contain' => array('Attachment'),  'recursive' => -1));
		$approve = $curriculums['Curriculum']['registrar_approved'] == 0 ? 1 : 0;
		$message = $approve == 1 ? 'approved' : 'unapproved';
		
		$this->request->allowMethod('post', 'approve');

		if (((isset($curriculums['Curriculum']['department_study_program_id']) && REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_APPROVAL == 1) || REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_APPROVAL == 0) || ((!empty($curriculums['Attachment']) && REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_APPROVAL == 1 ) ||  REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_APPROVAL == 0)) {

			if ($this->Curriculum->saveField('registrar_approved', $approve)) {
				$this->Flash->success('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum is now ' . $message .'.');
			} else {
				$this->Flash->error('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum could not be ' . $message . ' right now, please try again later.');
			}
		} else {
			if ((!isset($curriculums['Curriculum']['department_study_program_id']) && REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_APPROVAL == 1) && (empty($curriculums['Attachment']) && REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_APPROVAL == 1 )) {
				$this->Flash->warning('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum  must have Curriculum Program study defined and a soft copy of the curriculum PDF must be attached to it before approval.');
			} else if ((!isset($curriculums['Curriculum']['department_study_program_id']) && REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_APPROVAL == 1)) {
				$this->Flash->warning('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum  must have Curriculum Program study defined to it before approval.');
			} else if ((empty($curriculums['Attachment']) && REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_APPROVAL == 1 )) {
				$this->Flash->warning('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum  must have have a soft copy of the curriculum PDF must be attached to it before approval.');
			} 
		}

		if (!empty($page)) {
			return $this->redirect(array('action' => 'index', $data, 'page' => $page, 'sort' => $sort, 'direction' => $direction));
		} else {
			return $this->redirect(array('action' => 'index', $data));
		}
	}

	function __init_search()
	{
		if (!empty($this->request->data['Search'])) {
			$search_session = $this->request->data['Search'];
			$this->Session->write('search_data', $search_session);
		} else {
			$search_session = $this->Session->read('search_data');
			$this->request->data['search'] = true;
			$this->request->data['Search'] = $search_session;
		}
	}

	public function activate($id = null)
	{
		$this->Curriculum->id = $id;

		if (!$this->Curriculum->exists()) {
			throw new NotFoundException(__('Invalid Curriculum'));
		}

		$data = array();
		$sort = 'Curriculum.department_id';
		$direction = 'asc';
		$page = '';

		if ($this->Session->check('Curriculum.search_data_curriculum')) {
			debug($this->Session->read('Curriculum.search_data_curriculum'));
			$search_session = $this->Session->read('Curriculum.search_data_curriculum');
			$data['Curriculum'] = $search_session;
			$page = (isset($data['Curriculum']['page']) ? $data['Curriculum']['page'] : '');
			$sort = (isset($data['Curriculum']['sort']) && !empty($data['Curriculum']['sort']) ? $data['Curriculum']['sort'] : 'Curriculum.department_id');
			$direction = (isset($data['Curriculum']['direction']) && !empty($data['Curriculum']['direction']) ? $data['Curriculum']['direction'] : 'asc');
		}

		$curriculums = $this->Curriculum->find('first', array('conditions' => array('Curriculum.id' => $this->Curriculum->id), 'recursive' => -1));

		$active = ($curriculums['Curriculum']['active'] == 0 ? 1 : 0);
		$message = ($active == 1 ? 'activated' : 'dectivated');

		$this->request->allowMethod('post', 'activate');

		if ($this->Curriculum->saveField('active', $active)) {
			$this->Flash->success('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum is now ' . $message . '.');
		} else {
			$this->Flash->error('"'. $curriculums['Curriculum']['curriculum_detail'] . '" curriculum could not be ' . $message . ' right now, please try again later.');
		}

		if (!empty($page)) {
			return $this->redirect(array('action' => 'index', $data, 'page' => $page, 'sort' => $sort, 'direction' => $direction));
		} else {
			return $this->redirect(array('action' => 'index', $data));
		}

	}

	// allow registrars to add department study programs under their supervision
	public function add_departmernt_study_program_for_curriculum()
	{
		if (isset($this->request->data['Curriculum']['id']) && isset($this->request->data['Curriculum']['department_study_program_id'])) {
			debug($this->request->data);

			$curriculum = $this->Curriculum->find('first', array('conditions' => array('Curriculum.id' => $this->request->data['Curriculum']['id']), 'recursive' => -1));

			$data = array();
			$sort = 'Curriculum.department_id';
			$direction = 'asc';
			$page = '';

			if ($this->Session->check('Curriculum.search_data_curriculum')) {
				debug($this->Session->read('Curriculum.search_data_curriculum'));
				$search_session = $this->Session->read('Curriculum.search_data_curriculum');
				$data['Curriculum'] = $search_session;
				$page = (isset($data['Curriculum']['page']) ? $data['Curriculum']['page'] : '');
				$sort = (isset($data['Curriculum']['sort']) && !empty($data['Curriculum']['sort']) ? $data['Curriculum']['sort'] : 'Curriculum.department_id');
				$direction = (isset($data['Curriculum']['direction']) && !empty($data['Curriculum']['direction']) ? $data['Curriculum']['direction'] : 'asc');
			}

			//debug($curriculum);

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && in_array($curriculum['Curriculum']['department_id'], $this->department_ids) && in_array($curriculum['Curriculum']['program_id'], $this->program_id)) {
				$this->Curriculum->id = $this->request->data['Curriculum']['id'];
				if ($this->Curriculum->saveField('department_study_program_id', $this->request->data['Curriculum']['department_study_program_id'])) {
					$this->Flash->success('Department study program added to "'. $curriculum['Curriculum']['curriculum_detail'] . '" curriculum successfully.');
					//$this->redirect(array('controller' => 'curriculums', 'action' => 'view', $this->request->data['Curriculum']['id']));
					//$this->redirect(array('controller' => 'curriculums', 'action' => 'index'));
				} else {
					$this->Flash->error('Department study program was not added to "'. $curriculum['Curriculum']['curriculum_detail'] . '" curriculum. please try again later.');
					//$this->redirect(array('controller' => 'curriculums', 'action' => 'index'));
				}
			} else {
				$this->Flash->error('You do not have the permission to add department study program to a curriculum.');
				//$this->redirect(array('controller' => 'curriculums', 'action' => 'index'));
			}
		} else {
			$this->Flash->error('You need to select a department study program and the curriculum to save.');
			//$this->redirect(array('controller' => 'curriculums', 'action' => 'index'));
		}

		if (!empty($page)) {
			return $this->redirect(array('action' => 'index', $data, 'page' => $page, 'sort' => $sort, 'direction' => $direction));
		} else {
			return $this->redirect(array('action' => 'index', $data));
		}

	}

	function get_curriculums_based_on_program_combo($program_id = null)
	{
		$this->layout = 'ajax';

		$curriculums = array();

		if (!empty($program_id)) {
			if (isset($this->department_ids) && !empty($this->department_ids)) {
				$curriculums = ClassRegistry::init('Curriculum')->find('list', array(
					'conditions' => array(
						'Curriculum.department_id' => $this->department_ids,
						'Curriculum.program_id' => $program_id,
						'Curriculum.registrar_approved' => 1,
						'Curriculum.active' => 1
					),
					'order' => array('Curriculum.program_id' => 'ASC', 'Curriculum.created' => 'DESC'),
				));
			} else if (isset($this->college_ids) && !empty($this->college_ids)) {
				
				$departments = ClassRegistry::init('Department')->find('list', array(
					'conditions' => array(
						'Department.college_id' => $this->college_ids,
						'Department.active' => 1,
					),
					'fields' => array('Department.id', 'Department.id'),
					'order' => array('Department.name'=> 'ASC'),
				));

				if (!empty($departments)) {
					$curriculums = ClassRegistry::init('Curriculum')->find('list', array(
						'conditions' => array(
							'Curriculum.department_id' => $departments,
							'Curriculum.program_id' => $program_id,
							'Curriculum.registrar_approved' => 1,
							'Curriculum.active' => 1
						),
						'order' => array('Curriculum.program_id' => 'ASC', 'Curriculum.created' => 'DESC'),
					));
				}
			}
		}

		$this->set(compact('curriculums'));
	}
}
