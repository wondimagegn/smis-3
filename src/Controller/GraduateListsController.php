<?php
class GraduateListsController extends AppController
{
	public $name = 'GraduateLists';
	public $helpers = array('Media.Media', 'Csv');
	public $components = array('EthiopicDateTime', 'AcademicYear');

	var $menuOptions = array(
		'parent' => 'graduation',
		'weight' => 2,
		'exclude' => array(
			'search', 
			'delete', 
			'graduation_certificate', 
			'to_whom_it_may_concern', 
			'language_proficiency', 
			'temporary_degree', 
			'mass_certificate_print', 
			'check_graduate',
			'download_csv'
		),
		'alias' => array(
			'index' => 'List Graduates',
			'add' => 'Prepare Graduate List'
		)
	);

	public $paginate = array();

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			'search', 
			'check_graduate',
			'download_csv'
		);
	}

	public function beforeRender()
	{
		//parent::beforeRender();

		$current_academicyear = $defaultacademicyear = $this->AcademicYear->current_academicyear();
		$acyear_array_data = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $current_academicyear)[0]));

		//debug($acyear_array_data);

		//$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		//$program_types = $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$this->set(compact('acyear_array_data', 'defaultacademicyear'/* , 'program_types', 'programTypes', 'programs' */));

		if (isset($this->request->data['User']['password'])) {
			unset($this->request->data['User']['password']);
		}
	}

	function __init_search_graduate_list()
	{
		if (!empty($this->request->data['GraduateList'])) {
			$this->Session->write('search_data_graduate_list', $this->request->data['GraduateList']);
		} else if ($this->Session->check('search_data_graduate_list')) {
			$this->request->data['GraduateList'] = $this->Session->read('search_data_graduate_list');
		}
	}

	function __init_clear_session_filters($data = null)
	{
		if ($this->Session->check('search_data_graduate_list')) {
			$this->Session->delete('search_data_graduate_list');
		}
		//return $this->redirect(array('action' => 'index', $data));
	}

	function search()
	{
		$this->__init_search_graduate_list(); 

		$url['action'] = 'index';

		if (!empty($this->request->data)) {
			foreach ($this->request->data as $k => $v) {
				foreach ($v as $kk => $vv) {
					if (is_array($vv)) {
						foreach ($vv as $kkk => $vvv) {
							$url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
						}
					} else {
						$url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
					}
				}
			}
		}

		return $this->redirect($url, null, true);
	}

	public function index($data = null)
	{

		$selectedLimit =  100;
		$options = array();

		if (!empty($this->passedArgs)) {
			if (isset($this->passedArgs['GraduateList.department_id'])) {
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && $this->passedArgs['GraduateList.department_id'] == 0) {
					if (!empty($this->college_ids)) {
						$options['conditions'][]['Student.college_id'] =  $this->college_ids;
					} 
					if (!empty($this->department_ids)) {
						$options['conditions'][]['Student.department_id'] = $this->department_ids;
					}
				} else if(!empty($this->passedArgs['GraduateList.department_id'])) {
					$department_id = $this->passedArgs['GraduateList.department_id'];
					$college_id = explode('~', $department_id);
					if (count($college_id) > 1) {
						$options['conditions'][]['Student.college_id'] = $college_id[1];
					} else {
						$options['conditions'][]['Student.department_id'] = $department_id;
					}
				}
				$this->request->data['GraduateList']['department_id'] = $this->passedArgs['GraduateList.department_id'];
			}

			if (isset($this->passedArgs['GraduateList.program_id'])) {
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && $this->passedArgs['GraduateList.program_id'] == 0) {
					if (!empty($this->program_ids) || !empty($this->program_id)) {
						$options['conditions'][]['Student.program_id'] = (!empty($this->program_ids) ? $this->program_ids : $this->program_id);
					} 
				} else if(!empty($this->passedArgs['GraduateList.program_id'])) {
					$options['conditions'][]['Student.program_id'] = $this->passedArgs['GraduateList.program_id'];
				}
				$this->request->data['GraduateList']['program_id'] = $this->passedArgs['GraduateList.program_id'];
			}

			if (isset($this->passedArgs['GraduateList.program_type_id'])) {
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && $this->passedArgs['GraduateList.program_type_id'] == 0) {
					if (!empty($this->program_type_ids) || !empty($this->program_type_id)) {
						$options['conditions'][]['Student.program_type_id'] = (!empty($this->program_type_ids) ? $this->program_type_ids : $this->program_type_id);
					} 
				} else if(!empty($this->passedArgs['GraduateList.program_type_id'])){
					$options['conditions'][]['Student.program_type_id'] = $this->passedArgs['GraduateList.program_type_id'];
				}
				$this->request->data['GraduateList']['program_type_id'] = $this->passedArgs['GraduateList.program_type_id'];
			}

			if (isset($this->passedArgs['GraduateList.limit']) && !empty($this->passedArgs['GraduateList.limit'])) {
				$this->request->data['GraduateList']['limit'] = $selectedLimit = $this->passedArgs['GraduateList.limit'];
			} else {
				$this->request->data['GraduateList']['limit'] = $selectedLimit = '';
			}

			if (isset($this->passedArgs['GraduateList.minute_number']) && !empty($this->passedArgs['GraduateList.minute_number'])) {

				$options['conditions'][] = array(
					'OR' => array(
						'GraduateList.minute_number' => array(
							str_replace('-', '/', trim($this->passedArgs['GraduateList.minute_number'])),
							trim($this->passedArgs['GraduateList.minute_number']),
						)
					)
				);

				$this->request->data['GraduateList']['minute_number'] = str_replace('-', '/', trim($this->passedArgs['GraduateList.minute_number']));
			}

			if (isset($this->passedArgs['GraduateList.graduate_date_from.year'])) {
				$options['conditions'][] = array('GraduateList.graduate_date >= \'' . $this->passedArgs['GraduateList.graduate_date_from.year'] . '-' . $this->passedArgs['GraduateList.graduate_date_from.month'] . '-' . $this->passedArgs['GraduateList.graduate_date_from.day'] . '\'');
				$options['conditions'][] = array('GraduateList.graduate_date <= \'' . $this->passedArgs['GraduateList.graduate_date_to.year'] . '-' . $this->passedArgs['GraduateList.graduate_date_to.month'] . '-' . $this->passedArgs['GraduateList.graduate_date_to.day'] . '\'');
				
				//set the Search data, so the form remembers the option
				$this->request->data['GraduateList']['graduate_date_from']['year'] = $this->passedArgs['GraduateList.graduate_date_from.year'];
				$this->request->data['GraduateList']['graduate_date_from']['month'] = $this->passedArgs['GraduateList.graduate_date_from.month'];
				$this->request->data['GraduateList']['graduate_date_from']['day'] = $this->passedArgs['GraduateList.graduate_date_from.day'];

				$this->request->data['GraduateList']['graduate_date_to']['year'] = $this->passedArgs['GraduateList.graduate_date_to.year'];
				$this->request->data['GraduateList']['graduate_date_to']['month'] = $this->passedArgs['GraduateList.graduate_date_to.month'];
				$this->request->data['GraduateList']['graduate_date_to']['day'] = $this->passedArgs['GraduateList.graduate_date_to.day'];
			}

			////////////////////

			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['GraduateList']['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$this->request->data['GraduateList']['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$this->request->data['GraduateList']['direction'] = $this->passedArgs['direction'];
			}

			////////////////////

			$this->__init_search_graduate_list(); 
		}

		if (isset($data) && !empty($data['GraduateList'])) {
			$this->request->data = $data;
			debug($this->request->data);
			$this->__init_search_graduate_list();
		}

		if (isset($this->request->data['listStudentsForGraduateList'])) {
			unset($this->passedArgs);
			$this->__init_clear_session_filters();
			$this->__init_search_graduate_list();
		}

		debug($this->request->data);

		$graduateLists = array();

		if (!empty($options['conditions'])) {

			$this->Paginator->settings =  array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Student' => array(
						'order' => array(
							'Student.college_id' => 'ASC', 
							'Student.department_id' => 'ASC',
							'Student.program_id' => 'ASC',
							'Student.program_type_id' => 'ASC', 
							'Student.first_name' => 'ASC', 
							'Student.middle_name' => 'ASC', 
							'Student.last_name' => 'ASC'
						),
						'Department' => array('id', 'name', 'institution_code'),
						'College' => array(
							'fields' => array('id', 'name', 'institution_code'),
							'Campus' => array('id', 'name', 'campus_code')
						),
						'Curriculum' => array(
							'fields' => array(
								'id',
								'name',
								'year_introduced',
								'type_credit',
								'certificate_name',
								'amharic_degree_nomenclature',
								'specialization_amharic_degree_nomenclature',
								'english_degree_nomenclature',
								'specialization_english_degree_nomenclature',
								'minimum_credit_points',
								'department_id',
								'program_id',
								'department_study_program_id'
							),
							'DepartmentStudyProgram' => array(
								'StudyProgram' => array('id', 'study_program_name', 'code'),
								'ProgramModality' => array('id', 'modality', 'code'),
								'Qualification'  => array('id','qualification', 'code'),
							)
						), 
						'ProgramType' => array('id', 'name'), 
						'Program' => array('id', 'name'), 
						'StudentExamStatus' => array(
							'order' => array('StudentExamStatus.created' => 'DESC')
						),
						'ExitExam' => array(
							'order' => array('ExitExam.id' => 'DESC', 'ExitExam.exam_date' => 'DESC', 'ExitExam.modified' => 'DESC'),
							'limit' => 1
						),
						'Region' => array('id', 'name'),
					)
				),
				'order' => array('GraduateList.graduate_date' => 'DESC'),
				'limit' => (!empty($selectedLimit) ? $selectedLimit : 20000),
				'maxLimit' => (!empty($selectedLimit) ? $selectedLimit : 20000),
				'page' => (isset($page) && $page > 0 ? $page : 1)
			);

			try {
				$graduateLists = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('graduateLists'));
			} catch (NotFoundException $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['GraduateList'])) {
					unset($this->request->data['GraduateList']['page']);
					unset($this->request->data['GraduateList']['sort']);
					unset($this->request->data['GraduateList']['direction']);
				}
				unset($this->passedArgs);
				$this->Session->write('search_data_graduate_list', $this->request->data['GraduateList']);
				return $this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['GraduateList'])) {
					unset($this->request->data['GraduateList']['page']);
					unset($this->request->data['GraduateList']['sort']);
					unset($this->request->data['GraduateList']['direction']);
				}
				unset($this->passedArgs);
				$this->Session->write('search_data_graduate_list', $this->request->data['GraduateList']);
				return $this->redirect(array('action' => 'index'));
			}

			if (!empty($graduateLists)) {
				//debug($graduateLists);
				if ($this->Session->check('graduateLists_for_export')) {
					$this->Session->delete('graduateLists_for_export');
				}
				$this->Session->write('graduateLists_for_export', $graduateLists);
				$this->Session->write('search_data_graduate_list', $this->request->data['GraduateList']);
			}
		}

		if (empty($graduateLists) && !empty($options['conditions'])) {
			$this->Flash->info('There is no graduated student found based on the given criteria.');
			$turn_off_search = false;
		} else {
			$turn_off_search = false;
			//debug($graduateLists[0]);
		}

		

		/* if (!empty($this->paginate['conditions'])) {
			$this->Paginator->settings = $this->paginate;
		}

		if (!empty($this->Paginator->settings['conditions'])) {

			$graduateLists = $this->Paginator->paginate('GraduateList');
			// Write to session for csv Export
			$this->Session->write('graduateLists_for_export', $graduateLists);
			$this->Session->write('search_data_graduate_list', $this->request->data);
		} else {
			$graduateLists = array();
		}

		if (empty($graduateLists) && isset($this->passedArgs) && !empty($this->passedArgs)) {
			$this->Flash->info('There is no graduated student found based on the given criteria.');
		} */


		// $programs = $this->GraduateList->Student->Program->find('list');
		// $program_types = $this->GraduateList->Student->ProgramType->find('list');

		$programs = $this->GraduateList->Student->Program->find('list', array('conditions' => array('Program.id' => (!empty($this->program_ids) ? $this->program_ids : $this->program_id)/* , 'Program.active' => 1 */)));
		$program_types = $programTypes = $this->GraduateList->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => (!empty($this->program_type_ids) ? $this->program_type_ids : $this->program_type_id)/* , 'ProgramType.active' => 1 */)));
		
		$programs = array(0 => 'All Programs') + $programs;
		$program_types = array(0 => 'All Program Types') + $program_types;

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$departments = $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			$departments =  $this->GraduateList->Student->Department->allDepartmentsByCollege2(1, array(), $this->college_id, 1);
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
			
			if (!empty($this->program_ids) || !empty($this->program_id)) {
				$programs = $this->GraduateList->Student->Program->find('list', array('conditions' => array('Program.id' => (!empty($this->program_ids) ? $this->program_ids : $this->program_id))));
				$programs = array(0 => 'All Assigned Programs') + $programs;
			} else {
				$programs = array();
			}

			if (!empty($this->program_type_ids) || !empty($this->program_type_id)) {
				$program_types = $this->GraduateList->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => (!empty($this->program_type_ids) ? $this->program_type_ids : $this->program_type_id))));
				$program_types = array(0 => 'All Assigned Program Types') + $program_types;
			} else {
				$program_types = array();
			}

			if (isset($this->college_ids) && !empty($this->college_ids)) {
				$departments =  $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				$departments = array(0 => 'All Assigned Departments') + $departments;
			} else {
				$departments = array();
			}

			if (isset($this->department_ids) && !empty($this->department_ids)) {
				$departments = $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
				$departments = array(0 => 'All Assigned Departments') + $departments;
			} else {
				$departments = array();
			}
			
		} else {
			$departments = $this->GraduateList->Student->Department->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids, 1);
			$departments = array(0 => 'All ' . Configure::read('CompanyName') . ' Students') + $departments;
		}

		/* if (!empty($graduateLists) && isset($this->request->data['GraduateList']['exportToExcel'])) {
			
			$headerLabel = $this->__label(
				'Graduate Lists: ',
				$this->passedArgs['GraduateList.program_type_id'],
				$this->passedArgs['GraduateList.program_id'],
				$this->passedArgs['GraduateList.department_id']
			);

			$fromDate = date_format(date_create($this->passedArgs['GraduateList.graduate_date_from.year'] . '-' . $this->passedArgs['GraduateList.graduate_date_from.month'] . '-' . $this->passedArgs['GraduateList.graduate_date_from.day']), "M j Y");
			$toDate = date_format(date_create($this->passedArgs['GraduateList.graduate_date_to.year'] . '-' . $this->passedArgs['GraduateList.graduate_date_to.month'] . '-' . $this->passedArgs['GraduateList.graduate_date_to.day']), "M j Y");

			$this->autoLayout = false;
			$filename = 'Graduate Lists from ' . $fromDate.' to '.$toDate.' ' . date('Ymd H:i:s');

			$this->set(compact('graduateLists', 'filename' , 'headerLabel' ,'programs', 'program_types', 'departments'));
			$this->render('/Elements/graduated_list_xls');
			return;
		} */
	
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;

		$this->set(compact('programs', 'program_types', 'departments', 'default_department_id', 'default_program_id', 'default_program_type_id', 'graduateLists','selectedLimit'));
	}

	function add($department_id = null, $program_id = null, $program_type_id = null)
	{
		$programs = $this->GraduateList->Student->Program->find('list');
		$program_types = $this->GraduateList->Student->ProgramType->find('list');

		$departments = $this->GraduateList->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, array(), 1);

		$program_types = array(0 => 'All Program Types') + $program_types;
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;


		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
			
			if (!empty($this->program_ids)) {
				$programs = $this->GraduateList->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
				//$programs = array(0 => 'All Assigned Programs') + $programs;
			} else {
				$programs = array();
			}

			if (!empty($this->program_type_ids)) {
				$program_types = $this->GraduateList->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));
				$program_types = array(0 => 'All Assigned Program Types') + $program_types;
			} else {
				$program_types = array();
			}

			if (!empty($this->college_ids)) {
				$departments =  $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				//$departments = array(0 => 'All Assigned Departments') + $departments;
			} else {
				$departments = array();
			}

			if (!empty($this->department_ids)) {
				$departments = $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
				//$departments = array(0 => 'All Assigned Departments') + $departments;
			} else {
				$departments = array();
			}
			
		}
		 
		if ($this->Session->check('graduate_list_department_id')) {
			$department_id = $this->Session->read('graduate_list_department_id');
		} else if (!empty($this->department_ids)) {
			$department_id = array_values($this->department_ids)[0];
		} else {
			$department_id = -1;
		}

		if ($this->Session->check('graduate_list_program_id')) {
			$program_id  = $this->Session->read('graduate_list_program_id');
		} else if (!empty($programs)) {
			$program_id = array_keys($programs)[0];
		} else if (!empty($this->program_id)) {
			$program_id = array_values($this->program_id)[0];
		} else {
			$program_id = 0;
		}

		if ($this->Session->check('graduate_list_program_type_id')) {
			$program_type_id  = $this->Session->read('graduate_list_program_type_id');
		} else if (!empty($program_types)) {
			$program_type_id = array_keys(array_unique($program_types, SORT_REGULAR))[1];
		} else if (!empty($this->program_type_id)) {
			$program_type_id = array_values($this->program_type_id)[0];
		} else {
			$program_type_id = 0;
		}

		if (isset($this->request->data['listStudentsForGraduateList']) && $this->Session->check('graduate_list_department_id')) {
			$this->Session->write('graduate_list_department_id', $this->request->data['GraduateList']['department_id']);
			$this->Session->write('graduate_list_program_id', $this->request->data['GraduateList']['program_id']);
			$this->Session->write('graduate_list_program_type_id', $this->request->data['GraduateList']['program_type_id']);
		}

		//When any of the button is clicked (List students or Add to Graduate List)
		if (isset($this->request->data) && !empty($this->request->data)) {

			if (isset($this->request->data['GraduateList']['program_id']) && !empty($this->request->data['GraduateList']['program_id'])) {
				$program_id = $this->request->data['GraduateList']['program_id'];
			} else if (!empty($this->program_ids)) {
				$program_id = array_values($this->program_ids)[0];
			} else if (!empty($this->program_id)) {
				$program_id = $this->program_id;
			} else {
				$program_id = 0;
			}

			if (isset($this->request->data['GraduateList']['program_type_id']) && !empty($this->request->data['GraduateList']['program_type_id'])) {
				$program_type_id = $this->request->data['GraduateList']['program_type_id'];
			} else if (!empty($this->program_type_ids)) {
				$program_type_id = array_values($this->program_type_ids)[0];
			} else if (!empty($this->program_type_id)) {
				$program_type_id = $this->program_type_id;
			} else {
				$program_type_id = 0;
			}

			if (isset($this->request->data['GraduateList']['department_id']) && $this->request->data['GraduateList']['department_id'] != 0 ) {
				$department_id = $this->request->data['GraduateList']['department_id'];
			} else {
				if (!empty($this->department_ids)) {
					$department_id = array_values($this->department_ids)[0];
				} else {
					if (!empty($departments)) {
						$department_id = array_keys($departments);
					} else {
						$department_id = -1;
					}
				}
			}

			$students_for_graduate_list = $this->GraduateList->getListOfStudentsForGraduateList(
				$program_id,
				$ptype_id =  (empty($this->request->data['GraduateList']['program_type_id']) && !empty($this->program_type_ids) ? $this->program_type_ids : $program_type_id),
				$department_id
			);

			$default_department_id = $this->request->data['GraduateList']['department_id'];
			$default_program_id = $this->request->data['GraduateList']['program_id'];
			$default_program_type_id = $this->request->data['GraduateList']['program_type_id'];

		} else if (!empty($department_id) && !empty($program_id)) {
			//$students_for_graduate_list = $this->GraduateList->getListOfStudentsForGraduateList($program_id, $program_type_id, $department_id);
			$students_for_graduate_list = array();
			$default_department_id = $department_id;
			$default_program_id = $program_id;
			$default_program_type_id = $program_type_id;
		}
		
		if (isset($this->request->data) && isset($this->request->data['addStudentToGraduateList'])) {
	
			if (trim($this->request->data['GraduateList']['minute_number']) == "") {
				$this->Flash->error('Please provide minute number.');
			} else {

				$graduate_list = array();
				$deactivateAccount = array();
				$studentIdForSectionArchive = array();
				$count = 0;

				if (!empty($this->request->data['Student'])) {
					foreach ($this->request->data['Student'] as $key => $student) {
						if ($student['include_graduate'] == 1) {
							$sl_count = $this->GraduateList->find('count', array('conditions' => array('GraduateList.student_id' => $student['id'])));
							if ($sl_count == 0) {
								$sl_index = count($graduate_list);
								
								$graduate_list[$sl_index]['student_id'] = $student['id'];
								$graduate_list[$sl_index]['minute_number'] = trim($this->request->data['GraduateList']['minute_number']);
								$graduate_list[$sl_index]['graduate_date'] = $this->request->data['GraduateList']['graduate_date']['year'] . '-' . $this->request->data['GraduateList']['graduate_date']['month'] . '-' . $this->request->data['GraduateList']['graduate_date']['day'];
								
								$studentDetail = $this->GraduateList->Student->find('first', array('conditions' => array('Student.id' => $student['id'])));

								// deactivate account if exist
								if (isset($studentDetail['Student']['user_id']) && !empty($studentDetail['Student']['user_id'])) {
									$deactivateAccount['User'][$count]['id'] = $studentDetail['Student']['user_id'];
									$deactivateAccount['User'][$count]['active'] = 0;
								}

								//archive section
								if (isset($studentDetail['Student']['id']) && !empty($studentDetail['Student']['id'])) {
									$studentIdForSectionArchive[$studentDetail['Student']['id']] = $studentDetail['Student']['id'];
								}
								$count++;
							}
						}
					}
				}

				if (empty($graduate_list)) {
					$this->Flash->error('You are required to select at least one student to be included in the graduate list.');
				} else {
					if ($this->GraduateList->saveAll($graduate_list, array('validate' => false))) {
						$this->Flash->success(count($graduate_list) . '' . (count($graduate_list) > 1 ? ' students are' : ' student is') . ' included in the graduate list.');
						//archiveSection
						if (isset($studentIdForSectionArchive) && !empty($studentIdForSectionArchive)) {
							$sectionDeactivate = $this->GraduateList->query("UPDATE students_sections SET archive = 1 WHERE student_id in (" . join(', ', $studentIdForSectionArchive) . ") ");
						}
						if (isset($studentIdForSectionArchive) && !empty($studentIdForSectionArchive)) {
							$studentGraduate = $this->GraduateList->query("UPDATE students SET graduated = 1 WHERE id in (" . join(', ', $studentIdForSectionArchive) . ") ");
						}
						//deactivateAccount
						if (ClassRegistry::init('User')->saveAll($deactivateAccount['User'], array('validate' => false))) {
						}

						//return $this->redirect(array('action' => 'add', $this->request->data['GraduateList']['department_id'], $this->request->data['GraduateList']['program_id'], $this->request->data['GraduateList']['program_type_id']));
						$this->Session->write('graduate_list_department_id', $this->request->data['GraduateList']['department_id']);
						$this->Session->write('graduate_list_program_id', $this->request->data['GraduateList']['program_id']);
						$this->Session->write('graduate_list_program_type_id', $this->request->data['GraduateList']['program_type_id']);

						return $this->redirect(array('action' => 'add'));

					} else {
						$this->Flash->error('The system unable to include the selected students in the graduate list. Please try again.');
					}
				}
			}
		}

		$college_shortname = 'AMiT';

		if (!empty($department_id) || !empty($this->department_ids)) {

			if (isset($students_for_graduate_list) && !empty($students_for_graduate_list)){
				$dptID = $department_id;
			} else {
				$dptID = array_rand(array_values($this->department_ids));
				if (empty($dptID)) {
					$dptID = $department_id;
				}
			}

			$deptCollID = $this->GraduateList->Student->Department->field('college_id', array('Department.id' => $dptID));
			
			if (!empty($deptCollID)) {
				$college_shortname = $this->GraduateList->Student->College->field('shortname', array('College.id' => $deptCollID));
			}
		}

		$this->set(compact('programs', 'program_types', 'departments', 'students_for_graduate_list', 'default_department_id', 'default_program_id', 'default_program_type_id', 'college_shortname'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid graduate list id.');
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->GraduateList->save($this->request->data)) {
				$this->Flash->success('The graduate list has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The graduate list could not be saved. Please, try again.');
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->GraduateList->read(null, $id);
		}
		$students = $this->GraduateList->Student->find('list');
		$this->set(compact('students'));
	}

	function delete($id = null)
	{
		$this->GraduateList->id = $id;
		if (!$id || !$this->GraduateList->exists($id)) {
			$this->Flash->error('Invalid graduate list');
			return $this->redirect(array('action' => 'index'));
		}

		$graduate_detail = $this->GraduateList->find('first', array(
			'conditions' => array(
				'GraduateList.id' => $id
			),
			'contain' => array(
				'Student'
			)
		));

		if (!in_array($graduate_detail['Student']['department_id'], $this->department_ids)) {
			$this->Flash->error('You do not have the privilege to manage the selected student records.');
			return $this->redirect(array('action' => 'index'));
		}

		$valid_deletion_time = date('Y-m-d H:i:s', mktime(
			substr($graduate_detail['GraduateList']['created'], 11, 2),
			substr($graduate_detail['GraduateList']['created'], 14, 2),
			substr($graduate_detail['GraduateList']['created'], 17, 2),
			substr($graduate_detail['GraduateList']['created'], 5, 2),
			substr($graduate_detail['GraduateList']['created'], 8, 2) + Configure::read('Calendar.daysAvaiableForGraduateDeletion'),
			substr($graduate_detail['GraduateList']['created'], 0, 4)
		));

		if ($valid_deletion_time < date('Y-m-d')) {
			$this->Flash->error('You can not delete the record as it is archived.');
			return $this->redirect(array('action' => 'index'));
		} else {
			if ($this->GraduateList->delete($id)) {
				$this->GraduateList->query("UPDATE students set graduated = 0, modified = NOW() where id =". $graduate_detail['Student']['id']."");
				$this->Flash->success($graduate_detail['Student']['full_name'] . ' is successfully removed from the graduate list.');
				return $this->redirect(array('action' => 'index'));
			}
		}
		$this->Flash->error($graduate_detail['Student']['full_name'] . ' is not removed from the graduate list. Please try again.');
		return $this->redirect(array('action' => 'index'));
	}

	function temporary_degree($student_id = null)
	{
		$temporary_degree = null;
		if (!empty($this->request->data['displayTemporaryDegreePrint']) && !empty($this->request->data['GraduateList']['id'])) {
			$student_id = $this->request->data['GraduateList']['id'];
		}
		if (!empty($student_id) || !empty($this->request->data['continueTemporaryDegreePrint'])) {
			//Check if the user has privilege to print the student temporary degree
			if (!empty($this->request->data['GraduateList']['studentnumber']) && !empty($this->request->data['continueTemporaryDegreePrint'])) {
				if (trim($this->request->data['GraduateList']['studentnumber']) == "") {
					$this->Flash->error('Please provide student ID.');
					return $this->redirect(array('action' => 'temporary_degree'));
				} else {

					$student_detail = $this->GraduateList->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber' => $this->request->data['GraduateList']['studentnumber']
						),
						'contain' => array('GraduateList')
					));

					if (isset($student_detail['Student']['id'])) {

						$costShares = $this->GraduateList->Student->CostShare->find('all', array(
							'conditions' => array(
								'CostShare.student_id' => $student_detail['Student']['id']
							),
							'recursive' => -1,
							'order' => array('CostShare.cost_sharing_sign_date ASC')
						));

						$costSharingPayments = $this->GraduateList->Student->CostSharingPayment->find('all', array(
							'conditions' => array(
								'CostSharingPayment.student_id' => $student_detail['Student']['id']
							),
							'recursive' => -1,
							'order' => array('CostSharingPayment.created ASC')
						));

						$clearances = $this->GraduateList->Student->Clearance->find('all', array(
							'conditions' => array(
								'Clearance.student_id' => $student_detail['Student']['id'],
								'Clearance.type' => 'clearance',
								'Clearance.confirmed' => 1
							),
							'recursive' => -1,
							'order' => array('Clearance.request_date ASC')
						));
					}
				}
			} else {
				$student_detail = $this->GraduateList->Student->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					),
					'contain' => array('GraduateList'),
				));
			}

			if (empty($student_detail)) {
				$this->Flash->error('Please enter a valid student ID.');
				return $this->redirect(array('action' => 'temporary_degree'));
			} else if (!empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['department_id'], $this->department_ids)) {
				if (!empty($student_detail['Student']['department_id'])) {
					$department_name = $this->GraduateList->CourseRegistration->Student->Department->field('name', array(
						'Department.id' => $student_detail['Student']['department_id']
					));
					$department_name .= ' Department';
				} else {
					$department_name = $this->GraduateList->CourseRegistration->Student->College->field('name', array(
						'College.id' => $student_detail['Student']['college_id']
					));
					$department_name .= ' Freshman Program';
				}
				$this->Flash->error('You do not have the privilege to manage ' . $department_name . ' students. Please contact the registrar or system administrator to get privilege on ' . $department_name . '.');
				return $this->redirect(array('action' => 'temporary_degree'));
			} else {
				//Retrieve and pass student cost sharing, clearance, billing and other credits
				if (empty($student_detail['GraduateList']) || $student_detail['GraduateList']['id'] == "") {
					$this->Flash->error($student_detail['Student']['full_name'] . ' is not graduated to display ' . (strcasecmp($student_detail['Student']['gender'], 'male') == 0 ? 'his' : 'her') . ' temporary degree.');
				} else {
					$temporary_degree = $this->GraduateList->temporaryDegree($student_detail['Student']['id'],"TD");
					if (isset($this->request->data['displayTemporaryDegreePrint']) && isset($this->request->data['GraduateList']['id'])) {
						// before rendering the ceriticate
						
						if (isset($this->request->data['GraduateList']['have_agreement']) && $this->request->data['GraduateList']['have_agreement']) {
							$have_agreement = 1;
							$this->set(compact('have_agreement'));
						} 

						if (isset($this->request->data['GraduateList']['in_service']) && $this->request->data['GraduateList']['in_service']) {
							$in_service = 1;
							$this->set(compact('in_service'));
						} 

						$this->set(compact('temporary_degree'));
						$this->response->type('application/pdf');
						$this->layout = '/pdf/default';
						$this->render('/Elements/certificate/temporary_degree_pdf');
					}
				}
			}
		}
		$this->set(compact('temporary_degree', 'costShares', 'costSharingPayments', 'clearances'));
	}

	function language_proficiency($student_id = null)
	{
		//$this->_graduation_letter($student_id, 1);

		// New Update, Neway, we dont need any template for language proficiency its content is static

		$graduation_letter = null;
		$costShares = array();
		$costSharingPayments = array();
		//debug($this->request->data);
		if (isset($this->request->data['continueLanguageProficiencyLetterPrint']) && !empty(trim($this->request->data['GraduateList']['studentnumber']))) {
			debug($this->request->data);
			debug($this->request->data['GraduateList']['studentnumber']);

			$student_detail = $this->GraduateList->Student->find('first', array(
				'conditions' => array(
					'Student.studentnumber' => trim($this->request->data['GraduateList']['studentnumber']),
					//'Student.graduated' => 1,
				),
				'recursive' => -1
			));

			if (isset($student_detail['Student']) && !empty($student_detail['Student'])) {

				if ((!empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['department_id'], $this->department_ids)) || (empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['college_id'], $this->college_ids))) {
					if (!empty($student_detail['Student']['department_id'])) {
						$department_name = $this->GraduateList->Student->Department->field('name', array(
							'Department.id' => $student_detail['Student']['department_id']
						));
						$department_name .= ' Department';
					} else {
						$department_name = $this->GraduateList->Student->College->field('name', array(
							'College.id' => $student_detail['Student']['college_id']
						));
						$department_name .= ' Freshman Program';
					}

					if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
						$this->Flash->error('You need to switch your role ' . (empty($this->department_ids) ? 'from College to Department' : ' from Department to College'). ' in "Security > Users > List Users > Assign" for managing ' . $department_name . ' students.');
					} else {
						$this->Flash->error('You do not have the privilege to manage ' . $department_name . ' students. Please contact the registrar to get the privilege for managing ' . $department_name . ' students.');
					}

				} else { 

					$student_curriculum_id = $this->GraduateList->Student->field('curriculum_id', array('Student.id' => $student_detail['Student']['id']));
					$is_student_graduated = $this->GraduateList->Student->field('graduated', array('Student.id' => $student_detail['Student']['id']));

					if (!$is_student_graduated) {
						$this->Flash->error($student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'. ' is not graduated.');
					} else if (!is_numeric($student_curriculum_id) || is_null($student_curriculum_id)) {
						$this->Flash->error($student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'. ' is not attached to any curriculum.');
					} else {

						$graduation_letter =  $this->GraduateList->find('first', array(
							'conditions' => array(
								'GraduateList.student_id' => $student_detail['Student']['id'],
								'Student.graduated' => 1,
								'OR' => array(
									'Student.curriculum_id IS NOT NULL',
									'Student.curriculum_id != ""',
									'Student.curriculum_id != 0',
								)
							),
							'contain' => array(
								'Student' => array(
									'order' => array(
										'Student.first_name ASC', 
										'Student.middle_name ASC', 
										'Student.last_name ASC'
									),
									'Department' => array('id', 'name', 'institution_code'),
									'College' => array(
										'fields' => array('id', 'name', 'institution_code'),
										'Campus' => array('id', 'name', 'campus_code')
									),
									'Curriculum' => array(
										'fields' => array(
											'id',
											'name',
											'year_introduced',
											'type_credit',
											'certificate_name',
											'amharic_degree_nomenclature',
											'specialization_amharic_degree_nomenclature',
											'english_degree_nomenclature',
											'specialization_english_degree_nomenclature',
											'minimum_credit_points',
											'department_id',
											'program_id',
											'department_study_program_id'
										),
										'DepartmentStudyProgram' => array(
											'StudyProgram' => array('id', 'study_program_name', 'code'),
											'ProgramModality' => array('id', 'modality', 'code'),
											'Qualification'  => array('id','qualification', 'code'),
										)
									), 
									'ProgramType' => array('id', 'name'), 
									'Program' => array('id', 'name'), 
									'StudentExamStatus' => array(
										'order' => array('StudentExamStatus.created DESC')
									)
								)
							)
						));


						debug($graduation_letter);

						$degree_nomenclature_formatted = $this->__format_title($graduation_letter['Student']['Curriculum']['english_degree_nomenclature']);
					
						$costShares = $this->GraduateList->Student->CostShare->find('all', array(
							'conditions' => array(
								'CostShare.student_id' => $student_detail['Student']['id']
							),
							'recursive' => -1,
							'order' => array('CostShare.cost_sharing_sign_date ASC')
						));

						$costSharingPayments = $this->GraduateList->Student->CostSharingPayment->find('all', array(
							'conditions' => array(
								'CostSharingPayment.student_id' => $student_detail['Student']['id']
							),
							'recursive' => -1,
							'order' => array('CostSharingPayment.created ASC')
						));

						$clearances = $this->GraduateList->Student->Clearance->find('all', array(
							'conditions' => array(
								'Clearance.student_id' => $student_detail['Student']['id'],
								'Clearance.type' => 'clearance',
								'Clearance.confirmed' => 1
							),
							'recursive' => -1,
							'order' =>
							array('Clearance.request_date ASC')
						));

						$this->set(compact('graduation_letter', 'costShares', 'costSharingPayments', 'clearances', 'degree_nomenclature_formatted'));
					}
				}
			} else {
				$this->Flash->error('Student ID not found. Check for Typo Errors and Provide a valid Student ID without spaces.');
			}
		} else {
			if (!isset($this->request->data['displayLanguageProficiencyLetterPrint']) && isset($this->request->data['continueLanguageProficiencyLetterPrint'])) {
				$this->Flash->error('Please provide a valid Student ID.');
			}
		}

		$student_ids_array = array();
		$graduation_letters = array();

		if (isset($this->request->data['displayLanguageProficiencyLetterPrint']) && isset($this->request->data['GraduateList']['student_id'])) {
			$student_ids_array[] = $this->request->data['GraduateList']['student_id'];
		} else if (isset($student_id) && !empty($student_id)) {
			if (is_array($student_id)) {
				$student_ids_array = $student_id;
			} else {
				$student_ids_array[] = $student_id;
			}
		}

		if (!empty($student_ids_array)) {

			$graduation_letters =  $this->GraduateList->find('all', array(
				'conditions' => array(
					'GraduateList.student_id' => $student_ids_array,
					'Student.graduated' => 1,
					'OR' => array(
						'Student.curriculum_id IS NOT NULL',
						'Student.curriculum_id != ""',
						'Student.curriculum_id != 0',
					),
					'OR' => array(
						'Student.department_id' => $this->department_ids,
						'Student.college_id' => $this->college_ids,
					)
				),
				'contain' => array(
					'Student' => array(
						'order' => array(
							'Student.first_name ASC', 
							'Student.middle_name ASC', 
							'Student.last_name ASC'
						),
						'Department' => array('id', 'name', 'institution_code'),
						'College' => array(
							'fields' => array('id', 'name', 'institution_code'),
							'Campus' => array('id', 'name', 'campus_code')
						),
						'Curriculum' => array(
							'fields' => array(
								'id',
								'name',
								'year_introduced',
								'type_credit',
								'certificate_name',
								'amharic_degree_nomenclature',
								'specialization_amharic_degree_nomenclature',
								'english_degree_nomenclature',
								'specialization_english_degree_nomenclature',
								'minimum_credit_points',
								'department_id',
								'program_id',
								'department_study_program_id'
							),
							'DepartmentStudyProgram' => array(
								'StudyProgram' => array('id', 'study_program_name', 'code'),
								'ProgramModality' => array('id', 'modality', 'code'),
								'Qualification'  => array('id','qualification', 'code'),
							)
						), 
						'ProgramType' => array('id', 'name'), 
						'Program' => array('id', 'name'), 
						'StudentExamStatus' => array(
							'order' => array('StudentExamStatus.created DESC')
						)
					)
				)
			));

			if (!empty($graduation_letters) && isset($this->request->data['displayLanguageProficiencyLetterPrint'])) {
				//debug($graduation_letters);
				if (isset($this->request->data['GraduateList']['correct_degree_designation']) && $this->request->data['GraduateList']['correct_degree_designation']) {
					$correct_degree_designation = 1;
				} else {
					$correct_degree_designation = 0;
				}

				$degree_nomenclature_formatted = $this->request->data['GraduateList']['degree_nomenclature_formatted'];

				$this->set(compact('graduation_letters', 'degree_nomenclature_formatted', 'correct_degree_designation'));
				$this->response->type('application/pdf');
				$this->layout = '/pdf/default';
				$this->render('/Elements/certificate/language_proficiency_pdf');
			} else if (count($graduation_letters)>1) {
				$this->set(compact('graduation_letters'));
				$this->response->type('application/pdf');
				$this->layout = '/pdf/default';
				$this->render('/Elements/certificate/language_proficiency_pdf');
			}

		}
	}

	function to_whom_it_may_concern($student_id = null)
	{
		$this->_graduation_letter($student_id, 0);
	}

	private function _graduation_letter($student_id = null, $language_proficiency = null)
	{
		$graduation_letter = null;

		if (isset($this->request->data['displayLanguageProficiencyLetterPrint']) && isset($this->request->data['GraduateList']['id'])) {
			$student_id = $this->request->data['GraduateList']['id'];
		}
		if (isset($student_id) || isset($this->request->data['continueLanguageProficiencyLetterPrint'])) {
			//Check if the user has privilege to print the student graduation certificate
			if (isset($this->request->data['GraduateList']['studentnumber']) && isset($this->request->data['continueLanguageProficiencyLetterPrint'])) {
				if (trim($this->request->data['GraduateList']['studentnumber']) == "") {
					$this->Flash->error('Please provide Student ID.');
					return $this->redirect(array('action' => ($language_proficiency == 1 ? 'language_proficiency' : 'to_whom_it_may_concern')));
				} else {
					$student_detail = $this->GraduateList->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber' => trim($this->request->data['GraduateList']['studentnumber'])
						),
						'contain' => array(
							'GraduateList',
							'Program',
							'ProgramType'
						)
					));
				}
			} else {
				$student_detail = $this->GraduateList->Student->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					),
					'contain' => array(
						'GraduateList',
						'Program',
						'ProgramType'
					),
				));
			}
			if (empty($student_detail)) {
				$this->Flash->error('Please provide a valid Student ID.');
				return $this->redirect(array('action' => ($language_proficiency == 1 ? 'language_proficiency' : 'to_whom_it_may_concern')));
			} else if (!empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['department_id'], $this->department_ids)) {
				if (!empty($student_detail['Student']['department_id'])) {
					$department_name = $this->GraduateList->CourseRegistration->Student->Department->field('name', array(
						'Department.id' => $student_detail['Student']['department_id']
					));
					$department_name .= ' Department';
				} else {
					$department_name = $this->GraduateList->CourseRegistration->Student->College->field('name', array(
						'College.id' => $student_detail['Student']['college_id']
					));
					$department_name .= ' Freshman Program';
				}

				$this->Flash->error('You do not have the privilege to manage ' . $department_name . ' students. Please contact the registrar system administrator to get privilege on ' . $department_name . '.');
				return $this->redirect(array('action' => ($language_proficiency == 1 ? 'language_proficiency' : 'to_whom_it_may_concern')));
			} else {
				//Retrieve and pass student cost sharing, clearance, billing and other credits
				if (empty($student_detail['GraduateList']) || $student_detail['GraduateList']['id'] == "") {
					$this->Flash->error($student_detail['Student']['full_name'] . ' is not graduated to display ' . (strcasecmp($student_detail['Student']['gender'], 'male') == 0 ? 'his' : 'her') . ' ' . ($language_proficiency == 1 ? '<u>language proficiency</u>' : '<u>to whom it may concern</u>') . ' letter.');
				} else {

					$graduation_letter_template = ClassRegistry::init('GraduationLetter')->getGraduationLetter($student_detail['Student']['id'], $language_proficiency);
					$graduation_letter = $this->GraduateList->temporaryDegree($student_detail['Student']['id']);

					if (isset($this->request->data['displayLanguageProficiencyLetterPrint']) && isset($this->request->data['GraduateList']['id'])) {
						$e_day = $this->EthiopicDateTime->GetEthiopicDay(date('j'), date('n'), date('Y'));
						$e_month = $this->EthiopicDateTime->GetEthiopicMonth(date('j'), date('n'), date('Y'));
						$e_year = $this->EthiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));
						$e_month_name = $this->EthiopicDateTime->GetEthiopicMonthName(date('j'), date('n'), date('Y'));
						$g_d = $graduation_letter['student_detail']['GraduateList']['graduate_date'];
						$e_g_day = $this->EthiopicDateTime->GetEthiopicDay(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));
						$e_g_month = $this->EthiopicDateTime->GetEthiopicMonth(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));
						$e_g_year = $this->EthiopicDateTime->GetEthiopicYear(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));
						$e_g_month_name = $this->EthiopicDateTime->GetEthiopicMonthName(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));

						$graduate_date = date('d F, Y',
							mktime(0, 0, 0,
								substr($graduation_letter['student_detail']['GraduateList']['graduate_date'], 5, 2),
								substr($graduation_letter['student_detail']['GraduateList']['graduate_date'], 8, 2),
								substr($graduation_letter['student_detail']['GraduateList']['graduate_date'], 0, 4)
							)
						);

						debug(substr($graduation_letter['student_detail']['GraduateList']['graduate_date'], 0, 4));

						$this->set(compact('graduation_letter', 'graduation_letter_template', 'e_day', 'e_month', 'e_year', 'e_month_name', 'e_g_day', 'e_g_month', 'e_g_year', 'e_g_month_name', 'graduate_date'));

						$this->response->type('application/pdf');
						$this->layout = '/pdf/default';
						$this->render('graduation_letter_pdf');
					}
				}
			}
		}

		if (isset($student_detail) && !empty($student_detail)) {

			$graduation_letter_template = ClassRegistry::init('GraduationLetter')->getGraduationLetter($student_detail['Student']['id'], $language_proficiency);

			if (empty($graduation_letter_template)) {
				$this->Flash->error('The system is unable to find template for ' . ($language_proficiency == 1 ? '<u>language proficiency</u>' : '<u>to whom it may concern</u>') . ' letter. Please first record language proficiency letter template for ' . $student_detail['Program']['name'] . ' program and ' . $student_detail['ProgramType']['name'] . ' program type.');
			}
		}

		$this->set(compact('graduation_letter', 'graduation_letter_template'));
		$this->render(($language_proficiency == 1 ? 'language_proficiency' : 'to_whom_it_may_concern'));
	}

	function graduation_certificate($student_id = null)
	{
		$graduation_certificate = null;
		if (isset($this->request->data['displayGraduationCertificatePrint']) && isset($this->request->data['GraduateList']['id']))
			$student_id = $this->request->data['GraduateList']['id'];
		if (isset($student_id) || isset($this->request->data['continueGraduationCertificatePrint'])) {
			//Check if the user has privilege to print the student graduation certificate
			if (isset($this->request->data['GraduateList']['studentnumber']) && isset($this->request->data['continueGraduationCertificatePrint'])) {
				if (trim($this->request->data['GraduateList']['studentnumber']) == "") {
					$this->Flash->error('Please provide Student ID.');
					return $this->redirect(array('action' => 'graduation_certificate'));
				} else {
					$student_detail = $this->GraduateList->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber' => trim($this->request->data['GraduateList']['studentnumber'])
						),
						'contain' => array(
							'GraduateList',
							'Program',
							'ProgramType'
						)
					));
				}
			} else {
				$student_detail = $this->GraduateList->Student->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					),
					'contain' => array(
						'GraduateList',
						'Program',
						'ProgramType'
					),
				));
			}

			if (empty($student_detail)) {
				$this->Flash->error('Please provide a valid Student ID.');
				return $this->redirect(array('action' => 'graduation_certificate'));
			} else if (!empty($student_detail['Student']['department_id']) && !in_array($student_detail['Student']['department_id'], $this->department_ids)) {
				if (!empty($student_detail['Student']['department_id'])) {
					$department_name = $this->GraduateList->CourseRegistration->Student->Department->field('name', array(
						'Department.id' => $student_detail['Student']['department_id']
					));
					$department_name .= ' Department';
				} else {
					$department_name = $this->GraduateList->CourseRegistration->Student->College->field('name', array(
						'College.id' => $student_detail['Student']['college_id']
					));
					$department_name .= ' Freshman Program';
				}
				$this->Flash->error('You do not have the privilege to manage ' . $department_name . ' students. Please contact the registrar system administrator to get privilege on ' . $department_name . '.');
				return $this->redirect(array('action' => 'graduation_certificate'));
			} else {
				//Retrieve and pass student cost sharing, clearance, billing and other credits
				if (empty($student_detail['GraduateList']) || $student_detail['GraduateList']['id'] == "") {
					$this->Flash->error($student_detail['Student']['full_name'] . ' is not graduated to display ' . (strcasecmp($student_detail['Student']['gender'], 'male') == 0 ? 'his' : 'her') . ' temporary degree.');
				} else {

					$graduation_certificate_template = ClassRegistry::init('GraduationCertificate')->getGraduationCertificate($student_detail['Student']['id']);
					$graduation_certificate = $this->GraduateList->temporaryDegree($student_detail['Student']['id'], "GC");
					
					if (isset($this->request->data['displayGraduationCertificatePrint']) && isset($this->request->data['GraduateList']['id'])) {

						$e_day = $this->EthiopicDateTime->GetEthiopicDay(date('j'), date('n'), date('Y'));
						$e_month = $this->EthiopicDateTime->GetEthiopicMonth(date('j'), date('n'), date('Y'));
						$e_year = $this->EthiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));
						$e_month_name = $this->EthiopicDateTime->GetEthiopicMonthName(date('j'), date('n'), date('Y'));
						$g_d = $graduation_certificate['student_detail']['GraduateList']['graduate_date'];
						$e_g_day = $this->EthiopicDateTime->GetEthiopicDay(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));
						$e_g_month = $this->EthiopicDateTime->GetEthiopicMonth(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));
						$e_g_year = $this->EthiopicDateTime->GetEthiopicYear(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));
						$e_g_month_name = $this->EthiopicDateTime->GetEthiopicMonthName(substr($g_d, 8, 2), substr($g_d, 5, 2), substr($g_d, 0, 4));

						$graduate_date = date('d F, Y',
							mktime(0, 0, 0,
								substr($graduation_certificate['student_detail']['GraduateList']['graduate_date'], 5, 2),
								substr($graduation_certificate['student_detail']['GraduateList']['graduate_date'], 8, 2),
								substr($graduation_certificate['student_detail']['GraduateList']['graduate_date'], 0, 4)
							)
						);

						$this->set(compact('graduation_certificate', 'graduation_certificate_template', 'e_day', 'e_month', 'e_year', 'e_month_name', 'e_g_day', 'e_g_month', 'e_g_year', 'e_g_month_name', 'graduate_date'));
						$this->response->type('application/pdf');
						$this->layout = '/pdf/default';
						$this->render('/Elements/certificate/graduation_certificate_pdf');
					}
				}
			}
		}
		
		if (isset($student_detail) && !empty($student_detail)) {
			$graduation_certificate_template = ClassRegistry::init('GraduationCertificate')->getGraduationCertificate($student_detail['Student']['id']);
			if (empty($graduation_certificate_template)) {
				$this->Flash->error('The system is unable to find template for the graduation certificate. Please first record graduation certificate template for ' . $student_detail['Program']['name'] . ' program and ' . $student_detail['ProgramType']['name'] . ' program type.');
			}
		}
		$this->set(compact('graduation_certificate', 'graduation_certificate_template'));
	}

	function mass_certificate_print()
	{
		$this->__mass_certificate_print(null, null, null, null);
	}

	function __mass_certificate_print($program_id = null, $program_type_id = null, $department = null)
	{

		/*
		1. Retrieve list of students based on the given search criteria
		2. Display list of students
		3. Up on the selection of section, display list of students with check-box
		4. Prepare student grade copy in PDF for the selected students
		*/

		$programs = $this->GraduateList->Student->Program->find('list');
		$program_types = $this->GraduateList->Student->ProgramType->find('list');
		$departments = $this->GraduateList->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, array(), 1);
		$program_types = array(0 => 'All Program Types') + $program_types;

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
			if (isset($this->program_id) && !empty($this->program_id)) {
				$programs = $this->GraduateList->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_id)));
			} else {
				$programs = array();
			}

			if (isset($this->program_type_id) && !empty($this->program_type_id)) {
				$program_types = $this->GraduateList->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));
				$program_types = array(0 => 'All Assigned Program Types') + $program_types;
			} else {
				$program_types = array();
			}

			if (isset($this->college_ids) && !empty($this->college_ids)) {
				$departments =  $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
			} else {
				$departments = array();
			}

			if (isset($this->department_ids) && !empty($this->department_ids)) {
				$departments = $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else {
				$departments = array();
			}
		}
		
		$department_combo_id = null;
		//$program_types = array(0 => 'All Program Types') + $program_types;
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;

		/* $certificate_type_options = array(
			'graduation_certificate' => 'Graduation Certificate', 
			'language_proficiency' => 'Language Proficiency', 
			'to_whom_it_may_concern' => 'To Whom It May Concern', 
			'student_copy' => 'Student Copy'
		); */

		//Get list of students who are graduated when a button is clicked
		if (isset($this->request->data['listStudentsForCertficatePrint'])) {

			if (isset($this->request->data['Student'])) {
				unset($this->request->data['Student']);
			}

			if (isset($this->request->data['GraduateList']['select_all'])) {
				unset($this->request->data['GraduateList']['select_all']);
			}

			if (isset($this->request->data['GraduateList']['program_id']) && $this->request->data['GraduateList']['program_id'] != 0 ) {
				$program_id = $this->request->data['GraduateList']['program_id'];
			} else {
				$program_id = $this->program_id;
			}

			if (isset($this->request->data['GraduateList']['program_type_id']) && $this->request->data['GraduateList']['program_type_id'] != 0 ) {
				$program_type_id = $this->request->data['GraduateList']['program_type_id'];
			} else {
				$program_type_id = $this->program_type_id;
			}

			if (isset($this->request->data['GraduateList']['department_id']) && $this->request->data['GraduateList']['department_id'] != 0 ) {
				$department_id = $this->request->data['GraduateList']['department_id'];
			} else {
				if (isset($this->department_ids) && !empty($this->department_ids)) {
					$department_id = $this->department_ids;
				} else {
					if (isset($departments) && !empty($departments)) {
						$department_id = array_keys($departments);
					} else {
						$department_id = -1;
					}
				}
			}

			if (isset($this->request->data['GraduateList']['graduated']) && $this->request->data['GraduateList']['graduated'] == 1) {
				$student_lists = $this->GraduateList->getStudentListGraduated( null, $program_id, $program_type_id, $department_id, null, $this->request->data['GraduateList']['studentnumber'], $this->request->data['GraduateList']['name'], $this->request->data['GraduateList']['graduated'], $this->request->data['GraduateList']['graduate_date_from'], $this->request->data['GraduateList']['graduate_date_to']);
			} else {
				$student_lists = $this->GraduateList->getStudentListGraduated($this->request->data['GraduateList']['acadamic_year'], $program_id, $program_type_id, $department_id, null, $this->request->data['GraduateList']['studentnumber'], $this->request->data['GraduateList']['name'],  $this->request->data['GraduateList']['graduated'] , null, null);
			}
			
			$default_department_id = $this->request->data['GraduateList']['department_id'];
			$default_program_id = $this->request->data['GraduateList']['program_id'];
			$default_program_type_id = $this->request->data['GraduateList']['program_type_id'];
			$academic_year_selected = $this->request->data['GraduateList']['acadamic_year'];
			$program_id = $this->request->data['GraduateList']['program_id'];
			$program_type_id = $this->request->data['GraduateList']['program_type_id'];
		}

		//Get Certificate button is clicked
		if (isset($this->request->data['getStudentCertficate'])) {

			$student_ids = array();
			
			$certificate_template = array();

			if (!empty($this->request->data['Student'])) {
				foreach ($this->request->data['Student'] as $key => $student) {
					//debug($student['gp']);
					if ($student['gp'] == 1) {
						$student_ids[] = $student['student_id'];
					}

					if (empty($certificate_template)) {
						if ($this->request->data['GraduateList']['certificate_type'] == 'graduation_certificate') {
							$certificate_template = ClassRegistry::init('GraduationCertificate')->getGraduationCertificate($student['student_id']);
						} else if ($this->request->data['GraduateList']['certificate_type'] == 'to_whom_it_may_concern') {
							$certificate_template = ClassRegistry::init('GraduationLetter')->getGraduationLetter($student['student_id'], 0);
						} else if ($this->request->data['GraduateList']['certificate_type'] == 'language_proficiency') {
							$certificate_template = ClassRegistry::init('GraduationLetter')->getGraduationLetter($student['student_id'], 1);
						}
					}
				}
			} else {
				$this->Flash->error('No Graduated student sound using the given criteria.');
			}

			if (empty($student_ids)) {
				$this->Flash->error('You are required to select at least one student to isssue a certificate.');
			} else {

				if ($this->request->data['GraduateList']['certificate_type'] == 'graduation_certificate') {
					
					$graduation_certificate_template = $certificate_template;
					$graduation_certificates = $this->GraduateList->getTemporaryDegreeCertificateForMassPrint($student_ids,'GC');

					$this->set(compact('graduation_certificates', 'graduation_certificate_template'));

					if (!empty($graduation_certificates)) {
						$this->response->type('application/pdf');
						$this->layout = '/pdf/default';
						$this->render('/Elements/certificate/mass_graduation_certificate_print_pdf');
					} else {
						$this->Flash->error('The system is unable to find template for graduation certificate. Please define graduation certificate first.');
					}

				} else if ($this->request->data['GraduateList']['certificate_type'] == 'student_copy') {

					$student_copies = ClassRegistry::init('ExamGrade')->studentCopy($student_ids);

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
					//$this->render('mass_student_copy_pdf');
					$this->render('/Elements/certificate/student_copy_pdf');

				} else if ($this->request->data['GraduateList']['certificate_type'] == 'to_whom_it_may_concern') {

					$graduation_letter_template = $certificate_template;
					$graduation_letters = $this->GraduateList->getTemporaryDegreeCertificateForMassPrint($student_ids,'TC');

					$this->set(compact('graduation_letters', 'graduation_letter_template'));

					if (!empty($graduation_letter_template)) {
						$this->response->type('application/pdf');
						$this->layout = '/pdf/default';
						$this->render('/Elements/certificate/to_whom_it_may_concern_letter_pdf');
					} else {
						$this->Flash->error('The system is unable to find template for "to whom it may concern" letter. Please define to whom it may concern template first.');
					}

				} else if ($this->request->data['GraduateList']['certificate_type'] == 'language_proficiency') {

					/* $graduation_letter_template = $certificate_template;
					$graduation_letters = $this->GraduateList->getTemporaryDegreeCertificateForMassPrint($student_ids,'LP');

					$this->set(compact('graduation_letters', 'graduation_letter_template'));

					if (!empty($graduation_letter_template)) {
						$this->response->type('application/pdf');
						$this->layout = '/pdf/default';
						$this->render('/Elements/certificate/to_whom_profiency_letter_pdf');
					} else {
						$this->Flash->error('The system is unable to find template for "language proficiency" letter. Please define language proficiency template first.');
					} */

					$this->language_proficiency($student_ids);
					

				} else if ($this->request->data['GraduateList']['certificate_type'] == 'temporary_degree') {

					$temporary_degrees = $this->GraduateList->getTemporaryDegreeCertificateForMassPrint($student_ids,'TD');

					if (isset($this->request->data['GraduateList']['have_agreement']) && $this->request->data['GraduateList']['have_agreement']) {
						$have_agreement = 1;
						$this->set(compact('have_agreement'));
					} 

					if (isset($this->request->data['GraduateList']['in_service']) && $this->request->data['GraduateList']['in_service']) {
						$in_service = 1;
						$this->set(compact('in_service'));
					} 

					$this->set(compact('temporary_degrees'));
					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('/Elements/certificate/mass_temporary_degree_pdf');
					

				} else {
					$this->Flash->error('Something went wrong. Please try again.');
				}
			}
		}

		$font_size_options = array(27 => 'Small 1', 28 => 'Small 2', 29 => 'Small 3', 30 => 'Medium 1', 31 => 'Medium 2', 32 => 'Medium 3', 33 => 'Large 1', 34 => 'Large 2');

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
			$certificate_type_options = array(
				'graduation_certificate' => 'Graduation Certificate', 
				'student_copy' => 'Student Copy', 
				'temporary_degree' => 'Temporary Degree',
				'language_proficiency' => 'Language Proficiency',
				//'to_whom_it_may_concern' => 'To Whom It May Concern', 
			);
		} else {
			$certificate_type_options = array(
				'graduation_certificate' => 'Graduation Certificate', 
				'student_copy' => 'Student Copy', 
				'temporary_degree' => 'Temporary Degree',
				//'language_proficiency' => 'Language Proficiency', 
				//'to_whom_it_may_concern' => 'To Whom It May Concern'
			);
		}

		// for display only;
		$departmentsss = array();
		if (isset($this->department_ids) && !empty($this->department_ids)) {
			$departmentsss = $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
		} else if (isset($this->college_ids) && !empty($this->college_ids)) {
			$departmentsss =  $this->GraduateList->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids)));
		}
		// end for display only;

		$this->set(compact(
			'departments',
			'program_types',
			'programs',
			'default_program_type_id',
			'student_lists',
			'default_program_id',
			'default_department_id',
			'certificate_type_options',
			'font_size_options',
			'departmentsss'
		));
	}

	public function check_graduate()
	{
		$this->layout = 'login';

		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
			$isStudentValid = $this->GraduateList->Student->find('count', array('conditions' => array('Student.studentnumber' => trim($this->request->data['GraduateList']['studentID']))));
			if ($isStudentValid > 0) {
				$students = $this->GraduateList->Student->find('first', array(
					'conditions' => array('Student.studentnumber' => trim($this->request->data['GraduateList']['studentID'])),
					'contain' => array(
						'GraduateList','Attachment', 'Program', 'Department', 'College', 'ProgramType', 
						'Curriculum' => array(
							'fields' => array(
								'english_degree_nomenclature', 'amharic_degree_nomenclature',
								'certificate_name',
								'specialization_amharic_degree_nomenclature',
								'specialization_english_degree_nomenclature'
							)
						)
					)
				));
				$this->set(compact('students'));
			} else {
				$this->Flash->info('The student number provided is not in our system. If you made typo error please try again else the given student number is not our student based on the admitted student data since 2012!');
			}
		}
	}

	private function __label($prefix, $program_type_id, $program_id, $department_id)
	{

		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');

		$name = '';
		$label = $prefix;

		if ($program_type_id == 0 && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) { 
			if (count($this->program_type_id) > 0) {
				$label .= ' Program Types: ';
				$assignedPtIds = array_values($this->program_type_id); 

				$firstPtKey  = array_values($this->program_type_id)[0];
				$lastPtKey = array_values($this->program_type_id)[count($this->program_type_id)-1];

				foreach ($assignedPtIds as $ptk) {
					$label .= $programTypes[$ptk];
					if($ptk != $firstPtKey || $ptk != $lastPtKey ) {
						$label .= ', ';
					}
				}
			}
		} else if ($program_type_id == 0) {
			$label .= 'all program types ';
		} else {
			$label .= $programTypes[$program_type_id];
		}

		if ($program_id == 0 && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) { 
			
			if (count($this->program_id) > 0) {
				$label .= ' Programs: ';
				$assignedPIds = array_values($this->program_id);

				$firstPKey  = array_values($this->program_id)[0];
				$lastPKey = array_values($this->program_id)[count($this->program_id)-1];

				foreach ($assignedPIds as $pk) {
					$label .= $programs[$pk];
					if ($pk != $firstPKey || $pk !=  $lastPKey) {
						$label .= ', ';
					}
				}
			}

		} else if ($program_id == 0) {
			$label .= 'all programs ';
		} else {
			$label .= ' in ' . $programs[$program_id];
		}

		$college_id = explode('~', $department_id);

		if (count($college_id) > 1) {
			$namee = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $college_id[1]), 'recursive' => -1));
			$name .= ' for ' . $namee['College']['name'];
		} else if (!empty($department_id)) {
			$namee = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.id' => $department_id), 'recursive' => -1));
			$name .= ' for ' . $namee['Department']['name'];
		} else if ($department_id == 0 && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) { 
			if (count($this->department_ids) > 0) {

				$depts = ClassRegistry::init('Department')->find('list');

				$assignedDeptIds = array_values($this->department_ids);
				
				$firstDeptKey = array_values($this->department_ids)[0];
				$lastDeptKey = array_values($this->department_ids)[count($this->department_ids)-1];

				$name = ' Departments: ';

				foreach ($assignedDeptIds as  $dk) {
					$name .= $depts[$dk];
					if ($dk != $firstDeptKey || $dk != $lastDeptKey) {
						$name .= ', ';
					}
				}
			}
		} else if ($department_id == 0) {
			$name .= 'for all department';
		}

		$label .= $name;
		return $label;
	}

	function download_csv()
	{
		if ($this->Session->check('graduateLists_for_export')) {
			
			$graduateLists = $this->Session->read('graduateLists_for_export');
			//$this->Session->delete('graduateLists_for_export');

			$seach_data = $this->Session->read('search_data_graduate_list');
			//$this->Session->delete('search_data_graduate_list');
			
			$compactData = array();
			$counter = 1;

			if (!empty($graduateLists)) {
				foreach ($graduateLists as $key => $graduateList) {
					$credit_hour_sum = 0;
                    foreach ($graduateList['Student']['StudentExamStatus'] as $ses_key => $ses_value) {
                        $credit_hour_sum += $ses_value['credit_hour_sum'];
                    }
					//debug($acceptedStudent);
					$compactData[$key]['GraduateList']['#'] = $counter;
					$compactData[$key]['GraduateList']['Full Name'] = $graduateList['Student']['full_name'];
					$compactData[$key]['GraduateList']['Sex'] = strcasecmp(trim($graduateList['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($graduateList['Student']['gender']), 'female') == 0 ? 'F' : trim($graduateList['Student']['gender']));
					$compactData[$key]['GraduateList']['Student ID'] = $graduateList['Student']['studentnumber'];
					$compactData[$key]['GraduateList']['College'] = (isset($graduateList['Student']['College']) && !is_null($graduateList['Student']['College']['name']) ? $graduateList['Student']['College']['name'] : '');
					$compactData[$key]['GraduateList']['Department'] = (isset($graduateList['Student']['Department']) && !is_null($graduateList['Student']['Department']['name']) ? $graduateList['Student']['Department']['name'] : '');
					$compactData[$key]['GraduateList']['Program'] = $graduateList['Student']['Program']['name'];
					$compactData[$key]['GraduateList']['Program Type'] = $graduateList['Student']['ProgramType']['name'];
					$compactData[$key]['GraduateList']['Curriculum'] = (isset($graduateList['Student']['Curriculum']) && !empty($graduateList['Student']['Curriculum']['name']) ? $graduateList['Student']['Curriculum']['name'] : '');
					$compactData[$key]['GraduateList']['Degree Designation'] = (isset($graduateList['Student']['Curriculum']) && !empty($graduateList['Student']['Curriculum']['english_degree_nomenclature']) ? $graduateList['Student']['Curriculum']['english_degree_nomenclature'] : '');
					$compactData[$key]['GraduateList']['Degree Designation (Amharic)'] = (isset($graduateList['Student']['Curriculum']) && !empty($graduateList['Student']['Curriculum']['amharic_degree_nomenclature']) ? $graduateList['Student']['Curriculum']['amharic_degree_nomenclature'] : '');
					$compactData[$key]['GraduateList']['Specialization'] = (isset($graduateList['Student']['Curriculum']) && !empty($graduateList['Student']['Curriculum']['specialization_english_degree_nomenclature']) ? $graduateList['Student']['Curriculum']['specialization_english_degree_nomenclature'] : '');
					$compactData[$key]['GraduateList']['Required Credit for Graduation'] = (isset($graduateList['Student']['Curriculum']) && !empty($graduateList['Student']['Curriculum']['minimum_credit_points']) ? $graduateList['Student']['Curriculum']['minimum_credit_points'] : '');
					$compactData[$key]['GraduateList']['Study Program'] = (isset($graduateList['Student']['Curriculum']['DepartmentStudyProgram']['StudyProgram']) && !empty($graduateList['Student']['Curriculum']['DepartmentStudyProgram']['StudyProgram']) ? $graduateList['Student']['Curriculum']['DepartmentStudyProgram']['StudyProgram']['study_program_name'] : 'N/A');
					$compactData[$key]['GraduateList']['Program Modality'] = (isset($graduateList['Student']['Curriculum']['DepartmentStudyProgram']['ProgramModality']) && !empty($graduateList['Student']['Curriculum']['DepartmentStudyProgram']['ProgramModality']) ? $graduateList['Student']['Curriculum']['DepartmentStudyProgram']['ProgramModality']['modality'] : 'N/A');
					$compactData[$key]['GraduateList']['Qualification'] = (isset($graduateList['Student']['Curriculum']['DepartmentStudyProgram']['Qualification']) && !empty($graduateList['Student']['Curriculum']['DepartmentStudyProgram']['Qualification']) ? $graduateList['Student']['Curriculum']['DepartmentStudyProgram']['Qualification']['qualification'] : 'N/A');
					$compactData[$key]['GraduateList']['Credit Type'] = (isset($graduateList['Student']['Curriculum']) && !empty($graduateList['Student']['Curriculum']['type_credit']) ? (count(explode('ECTS', $graduateList['Student']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit') : '');
					$compactData[$key]['GraduateList']['Credit Taken'] = $credit_hour_sum;
					$compactData[$key]['GraduateList']['CGPA'] = $graduateList['Student']['StudentExamStatus'][0]['cgpa'];
					$compactData[$key]['GraduateList']['MCGPA'] = $graduateList['Student']['StudentExamStatus'][0]['mcgpa'];
					$compactData[$key]['GraduateList']['Date Graduated'] = date_format(date_create( $graduateList['GraduateList']['graduate_date']), "M j, Y");
					$compactData[$key]['GraduateList']['Minute Number'] = $graduateList['GraduateList']['minute_number'];
					$compactData[$key]['GraduateList']['Student National ID'] = (isset($graduateList['Student']['student_national_id']) && !empty($graduateList['Student']['student_national_id']) ? $graduateList['Student']['student_national_id'] : ''); 
					$compactData[$key]['GraduateList']['Exit Exam'] = (isset($graduateList['Student']['ExitExam']) && !empty($graduateList['Student']['ExitExam'][0]['result']) ? $graduateList['Student']['ExitExam'][0]['result'] : ''); 
					$compactData[$key]['GraduateList']['Region'] = (isset($graduateList['Student']['Region']) && !empty($graduateList['Student']['Region']['id']) ? $graduateList['Student']['Region']['name'] : ''); 
					$compactData[$key]['GraduateList']['Mobile'] = (isset($graduateList['Student']['phone_mobile']) && !empty($graduateList['Student']['phone_mobile']) ? $graduateList['Student']['phone_mobile'] : ''); 
					$compactData[$key]['GraduateList']['Email'] = (isset($graduateList['Student']['email']) && !empty($graduateList['Student']['email']) ? $graduateList['Student']['email'] : ''); 
					$counter++;
				}
			}

			//debug($compactData[0]);
			
			$this->set('graduateLists', $compactData);

			/* $headerLabel = $this->__label(
				'Graduate Lists -',
				$seach_data['program_type_id'],
				$seach_data['program_id'],
				$seach_data['department_id']
			); */

			$headerLabel = $this->__excel_file_name('Graduate List ', $seach_data);

			//debug($headerLabel);

			$fromDate = date_format(date_create($seach_data['graduate_date_from']['year'] . '-' . $seach_data['graduate_date_from']['month'] . '-' . $seach_data['graduate_date_from']['day']), "M j Y");
			$toDate = date_format(date_create($seach_data['graduate_date_to']['year'] . '-' . $seach_data['graduate_date_to']['month'] . '-' . $seach_data['graduate_date_to']['day']), "M j Y");

			$filename = $headerLabel. ' from ' . $fromDate . ' to ' . $toDate . ' - ' . date('Y-m-d');

			// To Display Amharic Font Properly in Excel.
			echo "\xEF\xBB\xBF"; 

			//debug($filename);
			$this->set(compact('filename'));

			$this->layout = null;
			$this->autoLayout = false;
			Configure::write('debug', '0');
		}
	}

	private function __format_title($title)
	{
		debug($title);
		$title = ucwords(strtolower(trim($title)));

		$prepositions_ucf = Configure::read('prepositions_ucf');
		$regex = '/\b(' . implode( '|', $prepositions_ucf ) . ')\b/i';

		$formatted_title = preg_replace_callback($regex, function($matches) {
			return strtolower( $matches[1]);
		}, $title);

		debug($formatted_title);

		return $formatted_title;
	}

	private function __excel_file_name($prefix = '', $data)
	{
		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');
		
		$label = '';
		$name = '';

		$program_type_id = $data['program_type_id'];
		$program_id = $data['program_id'];
		$department_id = $data['department_id'];
		
		$label .= $prefix;

		if ($program_id == 0) {
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' All Assigned Programs';
			} else {
				$label .= ' All Programs';
			}
		} else {
			$label .= ' ' . $programs[$program_id];
			//debug($program_id);
		}

		if ($program_type_id == 0) {
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' All Assigned Programs Types';
			} else {
				$label .= ' All Programs Types';
			}
		} else {
			$label .= ' ' .$programTypes[$program_type_id];
		}

		$college_id = explode('~', $department_id);

		if (count($college_id) > 1) {
			$namee = ClassRegistry::init('College')->field('College.name', array('College.id' => $college_id[1]));
			$name .= ' ' . $namee;
		} else if (!empty($department_id)) {
			$namee = ClassRegistry::init('Department')->field('Department.name', array('Department.id' => $department_id));
			$name .= ' ' . $namee;
		} else if ($department_id == 0) {
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$name .= ' All Assigned Departments';
			} else {
				$name .= ' ' . Configure::read('CompanyName');
			}
		}

		$label .= $name; //.' '. date('Y-m-d'); //date('Y-m-d H-i-s');
		return $label;
	}
}