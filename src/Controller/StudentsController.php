<?php
//App::import('Vendor','nusoap');
App::import('Vendor', 'PhpExcelReader/Excel/reader');
class StudentsController extends AppController
{

	public $name = 'Students';
	public $conn;
	public $config = array();
	public $helpers = array('DatePicker', 'Media.Media', 'Xls');

	public $menuOptions = array(
		'parent' => 'placement',
		'exclude' => array(
			'add', 
			'search',
			'search_profile', 
			'name_change', 
			'correct_name', 
			'profile_not_build_list', 
			'get_course_registered_and_add',
			'get_possible_sup_registered_and_add',
			'delete_student_from_graduate_list_for_correction',
			'activate_deactivate_profile'
		),
		'alias' => array(
			'index' => 'List Admitted Students',
			'department_issue_password' => 'Issue/Reset Password',
			'freshman_issue_password' => 'Issue/Reset Password',
			'name_list' => 'Correct Student Name',
			'id_card_print' => 'Print Student ID Card',
			'move_batch_student_to_department' => 'Move Batch Student to Other Department',
			'admit_all' => 'Admit Accepted Students'
		)
	);

	public $paginate = array();

	public $components = array('AcademicYear', 'EthiopicDateTime');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			'ajax_get_department',
			'change',
			'get_regions',
			'get_cities',
			'ajax_update',
			'ajax_check_ecardnumber',
			'get_course_registered_and_add',
			'get_possible_sup_registered_and_add',
			'auto_yearlevel_update',
			'student_lists',
			'search',
			'search_profile',
			'get_modal_box',
			'id_card_print',
			'update_koha_db',
			'print_record',
			//'update_lms_db',
			//'move_batch_student_to_department',
			//'mass_import_student_national_id',
			'get_countries',
			'get_zones',
			'get_woredas'
		);
	}
	
	public function beforeRender()
	{
		parent::beforeRender();

		//$acyear_array_data = $this->AcademicYear->acyear_array();
		$current_academicyear = $defaultacademicyear = $this->AcademicYear->current_academicyear();

		$acyear_array_data = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, (explode('/', $current_academicyear)[0]));
		$acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(APPLICATION_START_YEAR, (explode('/', $current_academicyear)[0]) + 1); 

		// debug($acyear_array_data);
		// debug($acYearMinuSeparated);

		$defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);

		// $this->set('defaultacademicyear', $defaultacademicyear);
		// $this->set('defaultacademicyearMinusSeparted', $defaultacademicyearMinusSeparted);

		$programs =  $this->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  $this->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$yearLevels = $this->year_levels;

		if ($this->role_id == ROLE_DEPARTMENT) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => $yearLevels)));
		}

		$this->set(compact('acyear_array_data', 'defaultacademicyear', 'acYearMinuSeparated', 'program_types', 'programTypes', 'defaultacademicyearMinusSeparted', 'programs', 'yearLevels'));

		if (isset($this->request->data['User']['password'])) {
			unset($this->request->data['User']['password']);
		}

	}

	function __init_search()
	{
		if (!empty($this->request->data['Search'])) {

			$search_session = $this->request->data['Search'];

			if (isset($this->request->data['getacceptedstudent']) || isset($this->request->data['Search']['getacceptedstudent'])) {
				$search_session['getacceptedstudent'] = (isset($this->request->data['getacceptedstudent']) ? $this->request->data['getacceptedstudent'] : $this->request->data['Search']['getacceptedstudent']);

				if (isset($this->request->data['AcceptedStudent']['getacceptedstudent'])) {
					unset($this->request->data['AcceptedStudent']['getacceptedstudent']);
				}
			}

			$this->Session->write('search_data', $search_session);
			
		} else if ($this->Session->check('search_data')) {
			
			$this->request->data = $this->Session->read('search_data');

			if (isset($this->request->data['Search']['getacceptedstudent'])) {
				$this->request->data['getacceptedstudent'] = $this->request->data['Search']['getacceptedstudent'];
				unset($this->request->data['Search']['getacceptedstudent']);
			}
		}

		//debug($this->request->data);
	}

	function __init_search_index()
	{
		if (!empty($this->request->data['Search'])) {
			$search_session = $this->request->data['Search'];
			$this->Session->write('search_data_index', $search_session);
		} else if ($this->Session->check('search_data_index')) {
			$this->request->data['Search'] = $this->Session->read('search_data_index');
		}

		//debug($this->request->data);
	}

	function __init_clear_session_filters()
	{

		if ($this->Session->check('search_data')) {
			$this->Session->delete('search_data');
		}

		if ($this->Session->check('search_data_student')) {
			$this->Session->delete('search_data_student');
		}

		if ($this->Session->check('search_data_index')) {
			$this->Session->delete('search_data_index');
		}
	}

	function __init_search_student()
	{
		if (!empty($this->request->data['Student'])) {
			$this->Session->write('search_data_student', $this->request->data['Student']);
		} else if ($this->Session->check('search_data_student')) {
			$this->request->data['Student'] = $this->Session->read('search_data_student');
		}

		if (!empty($this->request->data['Display'])) {
			$this->Session->delete('display_field_student');
			$this->Session->write('display_field_student', $this->request->data['Display']);
		}
	}

	// Generic search for returned items
	function search()
	{
		$this->__init_search_student();
		
		$url['action'] = 'index';

		unset($this->request->data['Display']);

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

	function search_profile()
	{
		$this->__init_search_student();
		
		$url['action'] = 'profile_not_build_list';

		unset($this->request->data['Display']);

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

	public function index($data = null)
	{
		$limit = 100;
		$name = '';
		$page = '';

		$options = array();
		
		if (!empty($this->passedArgs)) {

			//debug($this->passedArgs);

			if (!empty($this->passedArgs['Search.limit'])) {
				$limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
			}

			if (!empty($this->passedArgs['Search.name'])) {
				$name = str_replace('-', '/', trim($this->passedArgs['Search.name']));
				//$this->request->data['Search']['name'] 
			}

			if (!empty($this->passedArgs['Search.department_id'])) {
				$this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
			}

			if (!empty($this->passedArgs['Search.college_id'])) {
				$this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
			}

			if (isset($this->passedArgs['Search.academicyear'])) {
				$selected_academic_year = $this->request->data['Search']['academicyear']  = str_replace('-', '/', $this->passedArgs['Search.academicyear']);
			} else {
				$selected_academic_year = '';
			}

			if (isset($this->passedArgs['Search.gender'])) {
				$this->request->data['Search']['gender'] = $this->passedArgs['Search.gender'];
			}

			if (isset($this->passedArgs['Search.program_id'])) {
				$this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
			}

			if (isset($this->passedArgs['Search.program_type_id'])) {
				$this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
			}

			if (isset($this->passedArgs['Search.status'])) {
				$this->request->data['Search']['status'] = $this->passedArgs['Search.status'];
			}

			////////////////////

			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$this->request->data['Search']['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$this->request->data['Search']['direction'] = $this->passedArgs['direction'];
			}

			////////////////////

			$this->__init_search_index();

		}

		if (isset($data) && !empty($data['Search'])) {
			$this->request->data = $data['Search'];
			debug($this->request->data);
			$this->__init_search_index();
		}

		$this->__init_search_index();

		if (isset($this->request->data['search'])) {
			unset($this->passedArgs);
			$this->__init_clear_session_filters();
			$this->__init_search_index();
		}
		
		if (!empty($this->request->data)) {

			debug($this->request->data);

			if (!empty($page) && !isset($this->request->data['search'])) {
				$this->request->data['Search']['page'] = $page;
			}

			if (!empty($this->request->data['Search']['limit'])) {
				$limit = $this->request->data['Search']['limit'];
			}

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))); 
				$options['conditions'][] = array('Student.department_id' => $this->department_id);
				$this->request->data['Search']['department_id'] = $this->department_id;
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				
				$departments = array();
				
				if (!$this->onlyPre) {
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				}

				if (!empty($this->request->data['Search']['department_id'])) {
					$options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
				} else {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids);
				}

				$this->request->data['Search']['college_id'] = $this->college_id;

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

				if (!empty($this->department_ids)) {

					$colleges = array();
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));

					if (!empty($this->request->data['Search']['department_id'])) {
						$options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
					} else {
						$options['conditions'][] = array("Student.department_id" => $this->department_ids);
					}

				} else if (!empty($this->college_ids)) {

					$departments = array();
					$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));

					if (!empty($this->request->data['Search']['college_id'])) {
						$options['conditions'][] = array('Student.college_id' => $this->request->data['Search']['college_id'], 'Student.department_id IS NULL');
					} else {
						$options['conditions'][] = array("Student.college_id" => $this->college_ids, 'Student.department_id IS NULL');
					}
					
				}

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
				unset($this->passedArgs);
				unset($this->request->data);
				return $this->redirect(array('action' => 'index'));
			} else {

				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));

				if (!empty($this->request->data['Search']['department_id'])) {
					$options['conditions'][] = array("Student.department_id" => $this->request->data['Search']['department_id']);
				} else if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Search']['college_id'], 'Department.active' => 1)));
					$options['conditions'][] = array('Student.college_id' => $this->request->data['Search']['college_id']);

				} else {

					if (!empty($departments) && !empty($colleges)) {
						$options['conditions'][] = array(
							'OR' => array(
								'Student.college_id' => $this->college_ids,
								'Student.department_id' => $this->department_ids
							)
						);
					} else if (!empty($this->college_ids)) {
						$options['conditions'][] = array('Student.college_id' => $this->college_ids);
					} else if (!empty($this->department_ids)) {
						$options['conditions'][] = array('Student.department_id' => $this->department_ids);
					}
				}
			}

			if (!empty($selected_academic_year)) {
				$options['conditions'][] = array('Student.academicyear' => $selected_academic_year);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions'][] = array('Student.program_id' => $this->request->data['Search']['program_id']);
			} else if (empty($this->request->data['Search']['program_id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				$options['conditions'][] = array('Student.program_id' => $this->program_ids);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions'][] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
			} else if (empty($this->request->data['Search']['program_type_id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				$options['conditions'][] = array('Student.program_type_id' => $this->program_type_ids);
			}

			if (isset($name) && !empty($name)) {
				$options['conditions'][] = array(
					'OR' => array(
						'Student.first_name LIKE ' => '%' . $name . '%',
						'Student.middle_name LIKE ' =>  '%' . $name . '%',
						'Student.last_name LIKE ' =>  '%' . $name . '%',
						'Student.studentnumber LIKE' =>  $name . '%',
					)
				);
			}

			if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id']) && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR ) {
				$departments = $this->Student->Department->find('list', array(
					'conditions' => array(
						'Department.college_id' => $this->request->data['Search']['college_id'],
						'Department.active' => 1
					)
				));
			}

			if (isset($this->request->data['Search']['gender']) && !empty($this->request->data['Search']['gender'])) {
				$options['conditions'][] = array('Student.gender LIKE ' => $this->request->data['Search']['gender']);
			}

			if (!empty($this->request->data['Search']['status'])) {
				$options['conditions'][] = array('Student.graduated' => $this->request->data['Search']['status']);
			}

		} else {

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				
				$departments = array();
				
				if (!$this->onlyPre) {
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				}

				if (empty($departments)) {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids);
				} else {
					$options['conditions'][] = array(
						'OR' => array(
							'Student.college_id' => $this->college_ids,
							'Student.department_id' => $this->department_ids
						)
					);
				}

				$this->request->data['Search']['college_id'] = $this->college_id;

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
				$options['conditions'][] = array('Student.department_id' => $this->department_ids);
				
				$this->request->data['Search']['department_id'] = $this->department_id;

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				
				if (!empty($this->department_ids)) {
					$colleges = array();
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
					$options['conditions'][] = array('Student.department_id' => $this->department_ids, 'Student.program_id' => $this->program_ids, 'Student.program_type_id' => $this->program_type_ids);
				} else if (!empty($this->college_ids)) {
					$departments = array();
					$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
					$options['conditions'][] = array('Student.college_id' => $this->college_ids, 'Student.department_id IS NULL', 'Student.program_id' => $this->program_ids, 'Student.program_type_id' => $this->program_type_ids);
				}

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
				$options['conditions'][] =  array('Student.id' => $this->student_id);
			} else {

				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));

				if (!empty($departments) && !empty($colleges)) {
					$options['conditions'][] = array(
						'OR' => array(
							'Student.department_id' => $this->department_ids,
							'Student.college_id' => $this->college_ids
						)
					);
				} else if (!empty($departments)) {
					$options['conditions'][] = array('Student.department_id' => $this->department_ids);
				} else if (!empty($colleges)) {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids);
				}
			}

			if (!empty($options['conditions'])) {
				$options['conditions'][] = array('Student.id IS NOT NULL');
				$options['conditions'][] = array('Student.graduated = 0');
			}
		}

		//debug($options['conditions']);
		$students = array();

		if (!empty($options['conditions'])) {
			$this->Paginator->settings =  array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Department' => array(
						'fields' => array(
							'Department.id', 
							'Department.name', 
							'Department.shortname', 
							'Department.college_id',
							'Department.institution_code'
						)
					),
					'College' => array(
						'fields' => array(
							'College.id', 
							'College.name', 
							'College.shortname',
							'College.institution_code', 
							'College.campus_id',
						),
						'Campus' => array(
							'id', 
							'name',
							'campus_code'
						)
					),
					'Program' => array(
						'fields' => array(
							'Program.id', 
							'Program.name',
							'Program.shortname',
						)
					),
					'AcceptedStudent' => array(
						'fields' => array(
							'AcceptedStudent.id'
						)
					),
					'ProgramType' => array(
						'fields' => array(
							'ProgramType.id', 
							'ProgramType.name',
							'ProgramType.shortname',
						)
					),
					'Curriculum' => array('id', 'name', 'year_introduced','type_credit', 'english_degree_nomenclature', 'active'),
					'Specialization' => array('id', 'name'),
					'Region' => array('id', 'name', 'short'),
					'Zone' => array('id', 'name', 'short'),
					'Woreda' => array('id', 'name', 'code'),
					'City' => array('id', 'name', 'short'),
				), 
				'order' => array('Student.admissionyear' => 'DESC', 'Student.department_id'  => 'ASC', 'Student.program_type_id'  => 'ASC', 'Student.studentnumber' => 'ASC', 'Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC', 'Student.created' => 'DESC'),
				'limit' => $limit,
				'maxLimit' => $limit,
				'recursive'=> -1,
				'page' => $page
			);
			
			//$students = $this->paginate($options['conditions']);

			try {
				$students = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('students'));
			} catch (NotFoundException $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['Student'])) {
					unset($this->request->data['Student']['page']);
					unset($this->request->data['Student']['sort']);
					unset($this->request->data['Student']['direction']);
				}
				unset($this->passedArgs);
				$this->__init_search_index();
				return $this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['Student'])) {
					unset($this->request->data['Student']['page']);
					unset($this->request->data['Student']['sort']);
					unset($this->request->data['Student']['direction']);
				}
				unset($this->passedArgs);
				$this->__init_search_index();
				return $this->redirect(array('action' => 'index'));
			}

			if (!empty($students)) {
				if ($this->Session->check('students')) {
					$this->Session->delete('students');
				}
				$this->Session->write('students', $students);
			}
		}

		if (empty($students) && !empty($options['conditions'])) {
			$this->Flash->info('No Student is found with the given search criteria.');
			$turn_off_search = false;
		} else {
			$turn_off_search = false;
			//debug($students[0]);
		}

		//$regions = $this->Student->Region->find('list');
		//$zones = $this->Student->Zone->find('list');
		//$woredas = $this->Student->Woreda->find('list');
		//$cities = $this->Student->City->find('list');

		$this->set(compact('colleges', 'departments', /* 'students', */ 'turn_off_search', 'limit', 'name'/* , 'regions',  'zones', 'woredas', 'cities' */));
	}

	function view($student_id = null)
	{

		if (!$student_id) {
			$this->Session->setFlash(__('Invalid student'));
			return $this->redirect(array('action' => 'index'));
		}

		$check_student_admitted = $this->Student->find('count', array('conditions' => array('Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0)))));
		
		if ($check_student_admitted == 0) {
			$this->Flash->info('You Student ID Not Found.');
			return $this->redirect(array('action' => 'index'));
		} else {

			$studentDetail = $this->Student->find('first', array(
				'conditions' => array(
					'Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))
				),
				'contain' => array(
					'User',
					'AcceptedStudent',
					'Program',
					'ProgramType',
					'Contact',
					'Country',
					'Region',
					'Zone',
					'Woreda',
					'City',
					'Department',
					'College',
					'EslceResult',
					'EheeceResult',
					'Attachment',
					'HigherEducationBackground',
					'HighSchoolEducationBackground'
				)
			));

			//debug($studentDetail);

			if (empty($this->request->data)) {
				$this->request->data = $this->Student->find('first', array(
					'conditions' => array(
						'Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))
					),
					'contain' => array(
						'User',
						'AcceptedStudent',
						'Program',
						'ProgramType',
						'Contact',
						'Country',
						'Region',
						'Zone',
						'Woreda',
						'City',
						'Department',
						'College',
						'EslceResult',
						'EheeceResult',
						'Attachment',
						'HigherEducationBackground',
						'HighSchoolEducationBackground'
					)
				));
			}

			$this->request->data['Student']['gender'] = (isset($this->request->data['AcceptedStudent']['sex']) ?  (strtolower(trim($this->request->data['AcceptedStudent']['sex']))) : (strtolower(trim($this->request->data['Student']['gender']))));

			//debug($this->request->data);
			
			$regions = $this->Student->Region->find('list');
			$countries = $this->Student->Country->find('list');
			$cities = $this->Student->City->find('list');

			$zones = $this->Student->Zone->find('list');
			$woredas = $this->Student->Woreda->find('list');
			

			$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $studentDetail['Student']['college_id'])));
			
			if (!empty($studentDetail['Student']['department_id']) && is_numeric($studentDetail['Student']['department_id']) && $studentDetail['Student']['department_id'] > 0) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $studentDetail['Student']['department_id'])));
			} else {
				//$departments = $this->Student->Department->find('list');
				$departments = array();
			}
			
			$contacts = $this->Student->Contact->find('list', array('conditions' => array('Contact.student_id' => $this->student_id)));
			$users = $this->Student->User->find('list', array('conditions' => array('User.username' => $studentDetail['Student']['studentnumber'])));
			$programs = $this->Student->Program->find('list', array('conditions' => array('Program.id' => $studentDetail['Student']['program_id'])));
			$programTypes = $this->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $studentDetail['Student']['program_type_id'])));

			$this->set(compact('studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes', 'regions', 'countries', 'Zone', 'Woreda', 'cities'));
		}
	}

	function add()
	{
		if (!empty($this->request->data)) {
			$this->request->data['User']['role_id'] = 3;

			if ($this->Student->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Flash->success(__('The student has been saved'));

				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The student could not be saved. Please, try again.'));
			}
		}


		$contacts = $this->Student->Contact->find('list');
		$departments = $this->Student->Department->find('list');
		
		$regions = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1)));
		$countries = $this->Student->Country->find('list');
		$cities = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));

		$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
		$woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));

		$colleges = $this->Student->College->find('list');
		//$users = $this->Student->User->find('list');
		$programs = $this->Student->Program->find('list');
		$programTypes = $this->Student->ProgramType->find('list');

		$this->set(compact(
			'contacts',
			'users',
			'departments',
			'regions',
			'countries',
			'cities',
			'colleges',
			'programs',
			'programTypes',
			'zones',
			'woredas'
		));

	}


	public function edit($id = null)
	{

		if (!$id) {
			$this->Flash->error( __('Invalid Student ID'));
			return $this->redirect(array('action' => 'index'));
		}

		$check_student_id = $this->Student->find('count', array('conditions' => array('Student.id' => $id)));
		$require_update = false;
		$require_update_fields = array();
		$rupdt_key = 0;
		

		//debug($check_student_id);
		
		if (!$check_student_id) {
			$this->Flash->error('Invalid Student ID');
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
			$this->Flash->error(__('You are not elgibile to edit the any student records. This incident will be reported to system administrators. Please don\'t try this again.'));
			$this->redirect(array('action' => 'index'));
		}

		$check_elegibility_to_edit = 0;

		if (!empty($this->department_ids)) {
			$check_elegibility_to_edit = $this->Student->find('count', array('conditions' => array('Student.department_id' => $this->department_ids, 'Student.id' => $id)));
		} else if (!empty($this->college_ids)) {
			$check_elegibility_to_edit = $this->Student->find('count', array('conditions' => array('Student.college_id' => $this->college_ids, 'Student.id' => $id)));
		}


		if ($check_elegibility_to_edit == 0) {
			$this->Flash->error(__('You are not elgibile to edit the selected student profile. This happens when you are trying to edit student\'s profile which you are not assigned to edit.'));
			$this->redirect(array('action' => 'index'));
		}

		//$this->Student->id = $id;
		
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

			/* $this->request->data =  */$studentDetail = $this->Student->find('first', array(
				'conditions' => array(
					'Student.id' => $id
				),
				'contain' => array(
					'User',
					'AcceptedStudent',
					'Program',
					'ProgramType',
					'Contact',
					'Department',
					'College',
					'EslceResult',
					'EheeceResult',
					'Attachment',
					'HigherEducationBackground',
					'HighSchoolEducationBackground',
					'Country',
					'Region',
					'City',
					'Zone',
					'Woreda'
				)
			));

			//debug($studentDetail);

			$student_admission_year = ((int) (isset($studentDetail['AcceptedStudent']['academicyear']) && !empty($studentDetail['AcceptedStudent']['academicyear']) ? (explode('/', $studentDetail['AcceptedStudent']['academicyear'])[0]) : (isset($studentDetail['Student']['academicyear']) && !empty($studentDetail['Student']['academicyear']) ? (explode('/', $studentDetail['Student']['academicyear'])[0]) : (explode('/', $this->AcademicYear->current_academicyear())[0]))));

			if (!empty($this->request->data) && isset($this->request->data['updateStudentDetail'])) {

				unset($this->request->data['User']);

				if (isset($this->request->data['AcceptedStudent'])) {
					unset($this->request->data['AcceptedStudent']);
				}

				if (isset($this->request->data['College'])) {
					unset($this->request->data['College']);
				}

				//unset($this->request->data['Student']['gender']);

				if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "female") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "f") == 0 ) {
					$this->request->data['Student']['gender'] = 'Female';
				} else if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "male") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "m") == 0 ) {
					$this->request->data['Student']['gender'] = 'Male';
				} else {
					$this->request->data['Student']['gender'] = (ucfirst(strtolower(trim($studentDetail['AcceptedStudent']['sex']))));
				}
				
				if (!empty($studentDetail['User']['username']) && !empty($this->request->data['Student']['email'])) {
					if (1) {
						$this->request->data['User']['email'] = trim($this->request->data['Student']['email']);
						
						if ($studentDetail['Student']['user_id']) {
							$this->request->data['User']['id'] = $studentDetail['Student']['user_id'];
						} else {
							
							$student_user_id = $this->Student->User->field('User.id', array('User.username LIKE ' => $studentDetail['Student']['studentnumber'], 'User.role_id' => ROLE_STUDENT));

							if (!empty($student_user_id)) {
								$this->request->data['User']['id'] = $student_user_id;
							}
						}
					}
				}

				// $student_user_id = $this->Student->User->field('User.id', array('User.username LIKE ' => $studentDetail['Student']['studentnumber'], 'User.role_id' => ROLE_STUDENT));
				// debug($student_user_id);

				if (isset($this->request->data['updateStudentDetail'])) {


					if (!empty($this->request->data['Student']['phone_mobile']) && !empty($this->request->data['Student']['email'])) {

						$this->request->data = $this->Student->unset_empty($this->request->data);

						if (empty($this->request->data['Student']['city_id'])) {
							unset($this->request->data['Student']['city_id']);
						}

						if (isset($this->request->data['Contact']) && (empty($this->request->data['Contact'][0]['first_name']) || empty($this->request->data['Contact'][0]['middle_name']) || empty($this->request->data['Contact'][0]['last_name']) || empty($this->request->data['Contact'][0]['phone_mobile']))) {
							unset($this->request->data['Contact']);
						}

						if (isset($this->request->data['Attachment']) && (empty($this->request->data['Attachment'][0]['file']['name']) || $this->request->data['Attachment'][0]['file']['error'])) {
							unset($this->request->data['Attachment']);
						}

						if (isset($this->request->data['HighSchoolEducationBackground']) && (empty($this->request->data['HighSchoolEducationBackground'][0]['name']) || empty($this->request->data['HighSchoolEducationBackground'][0]['town']) || empty($this->request->data['HighSchoolEducationBackground'][0]['region_id']))) {
							unset($this->request->data['HighSchoolEducationBackground']);
						}

						if (isset($this->request->data['HigherEducationBackground']) && (empty($this->request->data['HigherEducationBackground'][0]['name']) || empty($this->request->data['HigherEducationBackground'][0]['field_of_study']) || empty($this->request->data['HigherEducationBackground'][0]['diploma_awarded']) || empty($this->request->data['HigherEducationBackground'][0]['cgpa_at_graduation']))) {
							unset($this->request->data['HigherEducationBackground']);
						}

						if (isset($this->request->data['EheeceResult']) && (empty($this->request->data['EheeceResult'][0]['subject']) || empty($this->request->data['EheeceResult'][0]['mark']) /* || empty($this->request->data['EheeceResult'][0]['exam_year']) */)) {
							unset($this->request->data['EheeceResult']);
						}

						if (isset($this->request->data['EslceResult']) && (empty($this->request->data['EslceResult'][0]['subject']) || empty($this->request->data['EslceResult'][0]['grade']) || empty($this->request->data['EslceResult'][0]['exam_year']))) {
							unset($this->request->data['EslceResult']);
						}

						unset($this->request->data['updateStudentDetail']); 

						debug($this->request->data);
						
						if ($this->Student->saveAll($this->request->data, array('validate' => 'first'))) {
							$this->Flash->success(__('Student Profile has been updated.'));
							//return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
							return $this->redirect(array('action' => 'index'));
						} else {
							$this->Flash->error( __('Student profile could not be saved. Please, try again.'));
						}
					} else {
						if (empty($this->request->data['Student']['phone_mobile']) && empty($this->request->data['Student']['email'])) {
							$this->Flash->error(__('Please provide student mobile phone number and personal email address. You can use ' . (strtolower(str_replace('/', '.', $studentDetail['Student']['studentnumber'])) . INSTITUTIONAL_EMAIL_SUFFIX) . ' if the student doesn\'t have personal email address like Gmail, yahoo, hotmail etc..'));	
						} else if (empty($this->request->data['Student']['phone_mobile'])) {
							$this->Flash->error(__('Please provide your mobile phone number.'));	
						} else {
							$this->Flash->error(__('Please provide student personal email address. You can use ' . (strtolower(str_replace('/', '.', $studentDetail['Student']['studentnumber'])) . INSTITUTIONAL_EMAIL_SUFFIX) . ' if the student doesn\'t have personal email address like Gmail, yahoo, hotmail etc..'));
						}
					}
				}
			}

			if (empty($this->request->data)) {
				$this->request->data = $this->Student->find('first', array(
					'conditions' => array(
						'Student.id' => $id
					),
					'contain' => array(
						'User',
						'AcceptedStudent',
						'Program',
						'ProgramType',
						'Department', 
						'College',
						'Contact',
						'EslceResult',
						'EheeceResult',
						'Attachment',
						'HigherEducationBackground',
						'HighSchoolEducationBackground',
						'Country',
						'Region',
						'City',
						'Zone',
						'Woreda'
					)
				));


				if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "female") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "f") == 0 ) {
					$this->request->data['Student']['gender'] = 'Female';
				} else if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "male") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "m") == 0 ) {
					$this->request->data['Student']['gender'] = 'Male';
				} else {
					$this->request->data['Student']['gender'] = (ucfirst(strtolower(trim($studentDetail['AcceptedStudent']['sex']))));
				}

				if (isset($this->request->data['EheeceResult'][0]['exam_year']) && !empty($this->request->data['EheeceResult'][0]['exam_year']) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->data['EheeceResult'][0]['exam_year'], ($student_admission_year - 10), $student_admission_year)) {
					$require_update = true;
					$require_update_fields[$rupdt_key]['field'] = 'EHEECE Exam Taken Date';
					$require_update_fields[$rupdt_key]['previous_value'] = $this->request->data['EheeceResult'][0]['exam_year'];
					$this->request->data['EheeceResult'][0]['exam_year'] =  $student_admission_year .'-'. '07-01';
					$require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->data['EheeceResult'][0]['exam_year'];
					$require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date is not valid date.';

					if (((int) explode('-', $studentDetail['EheeceResult'][0]['exam_year'])[0]) > $student_admission_year) {
						$require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date can\'t be behind Student Admission Year.';
					}
					
					$rupdt_key++;
				} else if (empty($studentDetail['EheeceResult'])) {
					$this->request->data['EheeceResult'][0]['exam_year'] =  $student_admission_year .'-'. '07-01';
				}


				$maximum_estimated_graduation_year_limit =  $student_admission_year;
				
				if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $studentDetail['Student']['program_id'] == PROGRAM_PhD) {
					$maximum_estimated_graduation_year_limit =  $student_admission_year + 6;
				} else if ($studentDetail['Student']['program_id'] == PROGRAM_POST_GRADUATE) {
					if ($studentDetail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
						$maximum_estimated_graduation_year_limit =  $student_admission_year + 3;
					} else {
						$maximum_estimated_graduation_year_limit =  $student_admission_year + 6;
					}
				} else {
					// Remedial and PGDT
					$maximum_estimated_graduation_year_limit =  $student_admission_year;
				}
				

				if (!empty($studentDetail['Student']['curriculum_id']) && $studentDetail['Student']['curriculum_id'] > 0) {

					$get_curriculum_year_level_count = $this->Student->Curriculum->Course->find('count', array('conditions' => array('Course.curriculum_id' => $studentDetail['Student']['curriculum_id']), 'group' => array('Course.year_level_id')));

					if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $studentDetail['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
						if (!empty($get_curriculum_year_level_count )) {
							$maximum_estimated_graduation_year_limit = $student_admission_year + ($get_curriculum_year_level_count * 2);
						}
					}
					
					//debug($get_curriculum_year_level_count);

					if (isset($this->request->data['Student']['estimated_grad_date']) && !empty($this->request->data['Student']['estimated_grad_date']) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->data['Student']['estimated_grad_date'], $student_admission_year, ($student_admission_year + ($get_curriculum_year_level_count * 2)))) {
						$require_update = true;
						$require_update_fields[$rupdt_key]['field'] = 'Estimated Graduation Date';
						$require_update_fields[$rupdt_key]['previous_value'] = $this->request->data['Student']['estimated_grad_date'];
						$this->request->data['Student']['estimated_grad_date'] =  ($student_admission_year + $get_curriculum_year_level_count) .'-08-01';
						$require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->data['Student']['estimated_grad_date'];
						$require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date is not valid date.';

						if (((int) explode('-', $studentDetail['Student']['estimated_grad_date'])[0]) > ($student_admission_year + ($get_curriculum_year_level_count * 2))) {
							$require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be behind ' . ($student_admission_year + ($get_curriculum_year_level_count * 2)) . ' G.C. (Double of student\'s attached curriculum year levels, ' . $get_curriculum_year_level_count . ' X 2 years)';
						} else if (((int) explode('-', $studentDetail['Student']['estimated_grad_date'])[0]) < ($student_admission_year + $get_curriculum_year_level_count)) {
							$require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be before ' . ($student_admission_year + $get_curriculum_year_level_count) . ' G.C.';
						}
						
						$rupdt_key++;
					} else if (empty($studentDetail['Student']['estimated_grad_date'])) {
						$this->request->data['Student']['estimated_grad_date'] = ($student_admission_year + $get_curriculum_year_level_count) .'-08-01';
					}
				} else if (empty($studentDetail['Student']['estimated_grad_date']) || is_null($studentDetail['Student']['estimated_grad_date'])) {
					$this->request->data['Student']['estimated_grad_date']  = $maximum_estimated_graduation_year_limit .'-08-01';
				}
			}

			//$this->request->data['Student']['gender'] = (isset($this->request->data['AcceptedStudent']['sex']) ?  (strtolower(trim($this->request->data['AcceptedStudent']['sex']))) : (strtolower(trim($this->request->data['Student']['gender']))));

			//debug($this->request->data);

			//$foriegn_students_region_ids = Configure::read('foriegn_students_region_ids');

			$foriegn_students_region_ids = $this->Student->Region->find('list', array('conditions' => array('Region.country_id <> ' => COUNTRY_ID_OF_ETHIOPIA), 'fields' => array('Region.id', 'Region.id')));

			debug($foriegn_students_region_ids);

			$regions = array();
			$zones = array();
			$woredas = array();
			$cities = array();

			$foriegn_student = 0;

			$country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;

			$region_id_of_student = '';

			if (!empty($studentDetail['AcceptedStudent']['region_id']) || !empty($studentDetail['Student']['region_id'])) {
				
				$region_id_of_student = (!empty($studentDetail['AcceptedStudent']['region_id']) ? $studentDetail['AcceptedStudent']['region_id'] : $studentDetail['Student']['region_id']);

				$country_id_of_region = $this->Student->Region->field('country_id', array('Region.id' => $region_id_of_student));
				
				$countries = $this->Student->Country->find('list', array('conditions' => array('Country.id' => $country_id_of_region)));
				
				$regions = $this->Student->Region->find('list', array(
					'conditions' => array(
						'Region.id' =>  $region_id_of_student,
						'Region.country_id' => $country_id_of_region
					)
				));

				$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.region_id' => $region_id_of_student)));

				$city_zone_ids = $this->Student->City->find('list', array(
					'conditions' => array(
						'City.region_id' => $region_id_of_student
					),
					'fields' => array('City.zone_id', 'City.zone_id')
				));

				$woredas = $this->Student->Woreda->find('list', array(
					'conditions' => array(
						'Woreda.zone_id' => (!empty($zones) ? array_keys($zones) : (!empty($city_zone_ids) ? $city_zone_ids : NULL)),
					)
				));

				$cities = $this->Student->City->find('list', array(
					'conditions' => array(
						'OR' => array(
							'City.id' => $studentDetail['Student']['city_id'],
							'City.zone_id' => (!empty($zones) ? array_keys($zones) : (!empty($studentDetail['AcceptedStudent']['zone_id']) ? $studentDetail['AcceptedStudent']['zone_id'] : $studentDetail['Student']['zone_id'])),
							'City.region_id' => $region_id_of_student,
						)
					)
				));

			} else {
				$countries = $this->Student->Country->find('list');
				$regions = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1)));
				$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
				$woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
				$cities = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));
			}

			if (empty($regions)) {
				$regions = $this->Student->Region->find('list', array('conditions' => array('Region.country_id' => $country_id_of_region)));
			}

			if (empty($zones)) {
				$zones = $this->Student->Zone->find('list');
			}

			if (empty($woredas)) {
				$woredas = $this->Student->Woreda->find('list');
			}

			if (empty($cities)) {
				if (!empty($region_id_of_student)) {
					$cities = $this->Student->City->find('list', array('conditions' => array('City.region_id' => $region_id_of_student)));
				} else if (!empty($regions)) {
					$cities = $this->Student->City->find('list', array('conditions' => array('City.region_id' => array_keys($regions))));
				} else {
					$cities = $this->Student->City->find('list');
				}
			}

			if (!empty($foriegn_students_region_ids) && ((isset($studentDetail['AcceptedStudent']['region_id']) && !empty($studentDetail['AcceptedStudent']['region_id']) && in_array($studentDetail['AcceptedStudent']['region_id'], $foriegn_students_region_ids)) || (isset($studentDetail['Student']['region_id']) && !empty($studentDetail['Student']['region_id']) && in_array($studentDetail['Student']['region_id'], $foriegn_students_region_ids)))) {
				$foriegn_student = 1;
			}

			$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $studentDetail['Student']['college_id'])));
			
			if (!empty($studentDetail['Student']['department_id']) && is_numeric($studentDetail['Student']['department_id']) && $studentDetail['Student']['department_id'] > 0) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $studentDetail['Student']['department_id'])));
			} else {
				//$departments = $this->Student->Department->find('list');
				$departments = array();
			}


			$regionsAll = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1, 'Region.country_id' => $country_id_of_region)));
			$zonesAll = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
			$woredasAll = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
			$citiesAll = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));

			if (isset($this->request->data['Contact'][0]['region_id']) && !empty($this->request->data['Contact'][0]['region_id'])) {
				$citiesAll = $this->Student->City->find('list', array('conditions' => array('City.region_id' => $this->request->data['Contact'][0]['region_id'], 'City.active' => 1)));
			}
			
			$contacts = $this->Student->Contact->find('list', array('conditions' => array('Contact.student_id' => $this->student_id)));
			$users = $this->Student->User->find('list', array('conditions' => array('User.username' => $studentDetail['Student']['studentnumber'])));
			$programs = $this->Student->Program->find('list', array('conditions' => array('Program.id' => $studentDetail['Student']['program_id'])));
			$programTypes = $this->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $studentDetail['Student']['program_type_id'])));

			$this->set(compact('studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes', 'countries', 'regions', 'zones', 'woredas', 'cities', 'regionsAll', 'zonesAll', 'woredasAll', 'citiesAll', 'foriegn_student'));

		} else {
			$this->Flash->warning('You are not allowed to edit or view any student profile.');
			$this->redirect('/');
		}

		$this->set(compact('studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes', 'countries', 'regions', 'zones', 'woredas', 'cities', 'regionsAll', 'zonesAll', 'woredasAll', 'citiesAll', 'foriegn_student', 'require_update', 'require_update_fields', 'student_admission_year', 'maximum_estimated_graduation_year_limit'));

		// below is the old implementation, neway
		/* if (!empty($this->request->data)) {

			// Higher education information won't be present in many cases, do not store it if nothing is entered.

			if (isset($this->request->data['HigherEducationBackground'])) {
				foreach ($this->request->data['HigherEducationBackground'] as $k => $v) {
					if (empty($v['name']) && empty($v['diploma_awarded']) && empty($v['name']) && empty($v['cgpa_at_graduation']) && empty($v['city'])) {
						unset($this->request->data['HigherEducationBackground'][$k]);
					}
				}
			}

			// High school information won't be present, do not store it if nothing is entered.

			if ($this->request->data['Student']['program_id'] != PROGRAM_UNDEGRADUATE || $this->request->data['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
				if (isset($this->request->data['HighSchoolEducationBackground'])) {
					$save_highschool_education = false;
					foreach ($this->request->data['HighSchoolEducationBackground'] as $k => $v) {
						if (!empty($v['name']) || !empty($v['region_id']) || !empty($v['town']) || !empty($v['zone']) || !empty($v['school_level'])) {
							$save_highschool_education = true;
						}
					}

					if (!$save_highschool_education) {
						unset($this->request->data['HighSchoolEducationBackground']);
					}
				}
			}

			unset($this->request->data['User']);

			$this->Student->HighSchoolEducationBackground->deleteHighSchoolEducationBackgroundList($id, $this->request->data);
			$this->Student->HigherEducationBackground->deleteHigherEducationList($id, $this->request->data);
			$this->Student->EslceResult->deleteEslceResultList($id, $this->request->data);
			$this->Student->EheeceResult->deleteEheeceResultList($id, $this->request->data);

			debug($this->request->data);

			$this->request->data = $this->Student->unset_empty($this->request->data);
			$check_elegibility_to_edit = 0;

			// unset($this->request->data['Student']['first_name']);
			// TODO dont allow multiple national exam taken
			// debug($this->request->data);

			if ($this->Student->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Flash->success( __('The student profile has been updated'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error( __('The student could not be saved. Please, try again.'));
				//  debug($this->Student->invalidFields());
				// debug($this->request->data);
			}


			// $this->request->data = $this->Student->read(null, $id);
			$data = $this->Student->find('first', array(
				'conditions' => array(
					'Student.id' => $id
				),
				'contain' => array(
					'Contact', 
					'AcceptedStudent', 
					'EslceResult', 
					'EheeceResult',
					'Attachment', 
					'HigherEducationBackground', 
					'HighSchoolEducationBackground', 
					'User'
				)
			));

			$this->request->data['Student']['studentnumber'] = $data['Student']['studentnumber'];
			$this->request->data['Student']['program_id'] = $data['Student']['program_id'];
			$this->request->data['Student']['program_type_id'] = $data['Student']['program_type_id'];
			$this->request->data['Student']['department_id'] = $data['Student']['department_id'];
			$this->request->data['Student']['college_id'] = $data['Student']['college_id'];
			$this->request->data['Student']['admissionyear'] = $data['Student']['admissionyear'];

		}

		if (empty($this->request->data)) {

			$this->request->data = $this->Student->find('first', array(
				'conditions' => array('Student.id' => $id),
				'contain' => array(
					'Contact',
					'AcceptedStudent', 
					'EslceResult', 
					'EheeceResult',
					'Attachment', 
					'HigherEducationBackground', 
					'HighSchoolEducationBackground', 
					'User'
				)
			));

			debug($this->request->data);
		}

		$regions = $this->Student->Region->find('list');
		$countries = $this->Student->Country->find('list');
		$cities = $this->Student->City->find('list');
		$colleges = $this->Student->College->find('list');
		$departments = $this->Student->Department->find('list');

		//$contacts = $this->Student->Contact->find('list');
		//$users = $this->Student->User->find('list');
		$programs = $this->Student->Program->find('list');
		$programTypes = $this->Student->ProgramType->find('list');
		$this->set(compact('contacts', 'colleges', 'cities', 'departments', 'programs', 'programTypes', 'regions', 'countries')); */
	}

	// function which will allow the registrar to admit all students then update

	public function admit_all()
	{

		//debug($this->request->data);
		$this->__init_search();

		$last_sucess_message = '';

		if (!empty($this->request->data) && !empty($this->request->data['admit'])) {

			$data['Search'] = $this->request->data['Search'];
			debug($data['Search']);

			//debug($this->request->data['AcceptedStudent']);

			$atleast_select_one = array_sum($this->request->data['AcceptedStudent']['approve']);

			if ($atleast_select_one > 0) {
				unset($this->request->data['Student']['SelectAll']);
				$admittedStudentsLists = array();
				$selectedAdmittedCount = 0;

				foreach ($this->request->data['AcceptedStudent']['approve'] as $id => $selected) {
					if ($selected == 1) {
						$selected_students[] = $id;
						$basicData = $this->Student->AcceptedStudent->find('first', array('conditions' => array('AcceptedStudent.id' => $id)));
						$checkForAcceptedDuplication = $this->Student->find('first', array('conditions' => array('OR' => array('Student.accepted_student_id' => $basicData['AcceptedStudent']['id'], 'Student.studentnumber' => $basicData['AcceptedStudent']['studentnumber']))));

						if (!empty($basicData) && empty($checkForAcceptedDuplication)) {

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['first_name'] = $basicData['AcceptedStudent']['first_name'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['middle_name'] = $basicData['AcceptedStudent']['middle_name'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['last_name'] = $basicData['AcceptedStudent']['last_name'];

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['last_name'] = $basicData['AcceptedStudent']['last_name'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['user_id'] = $basicData['AcceptedStudent']['user_id'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['accepted_student_id'] = $basicData['AcceptedStudent']['id'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['gender'] = $basicData['AcceptedStudent']['sex'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['studentnumber'] = $basicData['AcceptedStudent']['studentnumber'];

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['country_id'] = ClassRegistry::init('Region')->field('Region.country_id', array('Region.id' => $basicData['AcceptedStudent']['region_id']));

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['region_id'] = $basicData['AcceptedStudent']['region_id'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['program_id'] = $basicData['AcceptedStudent']['program_id'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['college_id'] = $basicData['AcceptedStudent']['college_id'];

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['original_college_id'] = $basicData['AcceptedStudent']['college_id'];

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['department_id'] = $basicData['AcceptedStudent']['department_id'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['program_type_id'] = $basicData['AcceptedStudent']['program_type_id'];

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['curriculum_id'] = $basicData['AcceptedStudent']['curriculum_id'];

							$admittedStudentsLists['Student'][$selectedAdmittedCount]['high_school'] = $basicData['AcceptedStudent']['high_school'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['moeadmissionnumber'] = $basicData['AcceptedStudent']['moeadmissionnumber'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['benefit_group'] = $basicData['AcceptedStudent']['benefit_group'];
							$admittedStudentsLists['Student'][$selectedAdmittedCount]['academicyear'] = $basicData['AcceptedStudent']['academicyear'];


							$admittedStudentsLists['Student'][$selectedAdmittedCount]['admissionyear'] = $basicData['AcceptedStudent']['created'];
						}

						$selectedAdmittedCount++;
					}
				}

				//debug($admittedStudentsLists['Student']);

				if ($this->Student->saveAll($admittedStudentsLists['Student'], array('validate' => 'first'))) {

					$last_sucess_message =  'All selected ' . (count($admittedStudentsLists['Student'])). ' student(s) are admitted Successfully.';
					$this->Flash->success($last_sucess_message);

					$this->request->data = array();
					$this->request->data['Search'] = $data['Search'];
					$this->request->data['getacceptedstudent'] = true; 

					$this->__init_clear_session_filters();
					$this->Session->write('search_data', $this->request->data);
					$this->Session->write('search_data_index', $this->request->data);

					if (isset($this->request->data['Student']['SelectAll'])) {
						unset($this->request->data['Student']['SelectAll']);
					}
					
					$this->redirect(array('action' => 'admit_all'));
				} else {
					$this->Flash->error('Could not admit the selected student(s). Please, try again.');
				}
			} else {
				$this->Flash->error('Please select atleast one student to admit.');
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['getacceptedstudent'])) {
			
			$this->__init_clear_session_filters();
			$this->__init_search();

			if (!empty($this->request->data['Search']['academicyear'])) {

				$conditions = null;
				$ssacdemicyear = $this->request->data['AcceptedStudent']['academicyear'] = $this->request->data['Search']['academicyear'];
				$pprogram_id = $this->request->data['AcceptedStudent']['program_id'] = $this->request->data['Search']['program_id'];
				$pprogram_type_id = $this->request->data['AcceptedStudent']['program_type_id'] = $this->request->data['Search']['program_type_id'];
				$name = $this->request->data['AcceptedStudent']['name'] = $this->request->data['Search']['name'];
				$college_ids = array();
				$department_ids = array();

				if (!empty($this->college_ids)) {
					$college_ids = $this->college_ids;
				} elseif (!empty($this->department_ids)) {
					$department_ids = $this->department_ids;
				}
				// retrive list of students based on registrar clerk assigned responsibility

				if (!empty($college_ids)) {
					if (!empty($this->request->data['Search']['college_id'])) {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE " => '' . $ssacdemicyear . '%',
							"AcceptedStudent.first_name LIKE " => '%'. $name . '%',
							"AcceptedStudent.college_id" => $this->request->data['Search']['college_id'],
							"AcceptedStudent.program_id" => $pprogram_id,
							"AcceptedStudent.program_type_id" => $pprogram_type_id,
							"AcceptedStudent.studentnumber is not null",
							"Student.id is null",
							"AcceptedStudent.id NOT IN (select accepted_student_id from students where accepted_student_id is not null )",
						);
					} else {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE " => '' . $ssacdemicyear . '%',
							"AcceptedStudent.first_name LIKE " => '%'. $name . '%',
							"AcceptedStudent.college_id" => $college_ids,
							"AcceptedStudent.program_id" => $pprogram_id,
							"AcceptedStudent.program_type_id" => $pprogram_type_id,
							"AcceptedStudent.studentnumber is not null",
							"Student.id is null",
							"AcceptedStudent.id NOT IN (select accepted_student_id from students where accepted_student_id is not null )"
						);
					}

				} elseif (!empty($department_ids)) {

					if (!empty($this->request->data['Search']['department_id'])) {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE " => '' . $ssacdemicyear . '%',
							//"AcceptedStudent.first_name LIKE" => "%". $name."%",
							"AcceptedStudent.studentnumber IS NOT NULL",
							"AcceptedStudent.department_id" => $this->request->data['Search']['department_id'],
							"AcceptedStudent.program_id" => $pprogram_id,
							"AcceptedStudent.program_type_id" => $pprogram_type_id,
							"AcceptedStudent.studentnumber is not null",
							"Student.id is null",
							"AcceptedStudent.id NOT IN (select accepted_student_id from students where accepted_student_id is not null)",
						);
					} else {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE " => '' . $ssacdemicyear . '%',
							"AcceptedStudent.first_name LIKE " => '%'. $name . '%',
							"AcceptedStudent.department_id" => $department_ids,
							"AcceptedStudent.program_id" => $pprogram_id,
							"AcceptedStudent.program_type_id" => $pprogram_type_id,
							"AcceptedStudent.studentnumber is not null",
							"Student.id is null",
							"AcceptedStudent.id NOT IN (select accepted_student_id from students where accepted_student_id is not null)",
						);
					}
				}
				

				if (!empty($conditions)) {

					if (isset($this->request->data['Search']['limit']) && !empty($this->request->data['Search']['limit'])) {
						$limit = $this->request->data['Search']['limit'];
					} else {
						$this->request->data['Search']['limit'] = $limit = 1000;
					}

					$this->Paginator->settings = array(
						'conditions' => $conditions,
						'contain' => array(
							'Student' => array('id'),
							'Department' => array('id', 'name'),
							'College' => array('id', 'name'),
						),
						'fields' => array('AcceptedStudent.id', 'AcceptedStudent.full_name', 'AcceptedStudent.sex', 'AcceptedStudent.studentnumber', 'AcceptedStudent.program_id', 'AcceptedStudent.college_id', 'AcceptedStudent.department_id', 'AcceptedStudent.EHEECE_total_results', 'AcceptedStudent.academicyear'),
						'limit' => $limit,
						'maxLimit' => $limit,
						//'recursive' => -1
					);

					debug($this->Paginator->settings);
					$acceptedStudents = $this->Paginator->paginate('AcceptedStudent');
					//debug($acceptedStudents);
					debug(count($acceptedStudents));

					!empty($acceptedStudents) ? debug($acceptedStudents[0]) : '';

					$this->set('acceptedStudents', $acceptedStudents);

					if (!empty($acceptedStudents)) {
						$this->__init_clear_session_filters();
						$this->request->data['getacceptedstudent'] = true;
						$this->__init_search();
					} else {
						if (!empty($last_sucess_message) || isset($this->request->data['admit'])) {
							$this->Flash->success($last_sucess_message. ' All students with the given search criteria have been admitted and more new accepted student is found that needs admission for now. Check admitted students list for more or change search createria for admit other non admitted students.');
						} else {
							$this->Flash->success('Either all students have been admitted or no new accepted student is found that needs admission for now whith the given search criteria. Check admitted students list for more.');
							$this->__init_clear_session_filters();
							$this->request->data['getacceptedstudent'] = true;
							$this->request->data['Search'] = $this->request->data['Search'];
							$this->Session->write('search_data_index', $this->request->data['Search']);
							$this->redirect(array('action' => 'index'));
						}
					}

					//debug($this->request->data);
					$admitsearch = true;
					$this->set('admitsearch', true);
					// $this->request->data['getacceptedstudent'] = true;
				} else {
					$this->Flash->error('You dont have privilage to admit students in the given criteria.');
				}
			} else {
				//$this->Flash->error('Please select academic year');
			}
		}

		// display the right department and college based on the privilage of registrar users
		if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
			$college_ids = array();
			$department_ids = array();
			if (!empty($this->college_ids)) {
				$college_ids = $this->college_ids;
				$this->set('colleges', $this->Student->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1))));
				$this->set('departments', $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $college_ids, 'Department.active' => 1))));
				$this->set('college_level', true);
			} elseif (!empty($this->department_ids)) {
				$department_ids = $this->department_ids;
				$this->set('departments', $this->Student->Department->find('list', array('conditions' => array('Department.id' => $department_ids, 'Department.active' => 1))));
				$this->set('colleges', $this->Student->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1))));
				$this->set('department_level', true);
			}
			$this->set(compact('colleges'));
		} else {
			$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));
			$departments = $this->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
			$this->set(compact('colleges', 'departments'));
		}

		// $regions = $this->Student->Region->find('list');
		// $countries = $this->Student->Country->find('list');
		// $cities = $this->Student->City->find('list');
		$programs = $this->Student->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$programTypes =$this->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_id)));

		$this->__init_search();

		$this->set(compact('programs', 'programTypes'/* , 'regions', 'countries', 'cities' */));
	}

	function admit($id = null)
	{

		//check the student has already got student number, else redirect
		if ($id) {
			$check_elegibility_to_edit = 0;
			if (!empty($this->college_ids)) {
				$check_elegibility_to_edit = $this->Student->AcceptedStudent->find(
					'count',
					array('conditions' => array(
						'AcceptedStudent.college_id' => $this->college_ids,
						'AcceptedStudent.program_type_id' => $this->program_type_id,
						'AcceptedStudent.program_id' => $this->program_id,
						'AcceptedStudent.id' => $id
					))
				);
			} else if (!empty($this->department_ids)) {
				$check_elegibility_to_edit = $this->Student->AcceptedStudent->find(
					'count',
					array('conditions' => array(
						'AcceptedStudent.department_id' => $this->department_ids,
						'AcceptedStudent.program_type_id' => $this->program_type_id,
						'AcceptedStudent.program_id' => $this->program_id,
						'AcceptedStudent.id' => $id
					))
				);
			}

			if ($check_elegibility_to_edit == 0) {
				$this->Session->setFlash(__('<span></span> You are not elgibile to admit the student. This happens when you are trying to admit students which you are not assigned.'), 'default', array('class' => 'error-box error-message'));
				$this->redirect(array('action' => 'index'));
			}

			$studentnumber = $this->Student->AcceptedStudent->find(
				'first',
				array('conditions' => array('AcceptedStudent.id' => $id), 'fields' => array('AcceptedStudent.studentnumber'), 'recursive' => -1)
			);

			if (empty($studentnumber['AcceptedStudent']['studentnumber'])) {

				$this->Session->setFlash(__('<span></span> You can not admit students before generating student number, please generate student number.'), 'default', array('class' => 'error-box error-message'));
				$this->redirect(array('controller' => 'accepted_students', 'action' => 'generate'));
			}
			$isAdmitted = $this->Student->isAdmitted($id);
			if ($isAdmitted) {

				$this->Session->setFlash(__('<span></span> You have already admitted the students.'), 'default', array('class' => 'error-box error-message'));
				$this->redirect(array('controller' => 'students', 'action' => 'admit'));
			}
		} else {
			// Function to load/save search criteria.

			if ($this->Session->read('search_data')) {
				$this->request->data['getacceptedstudent'] = true;
			}
		}



		if (!empty($this->request->data) && isset($this->request->data['admit'])) {


			$this->set($this->request->data);
			// if($this->Student->validates()){

			$isAdmitted = $this->Student->isAdmitted($id);
			if (!$isAdmitted) {
				$this->request->data['User']['role_id'] = ROLE_STUDENT;
				$this->request->data['User']['username'] = $this->request->data['Student']['studentnumber'];
				$this->request->data['User']['first_name'] = $this->request->data['Student']['first_name'];
				$this->request->data['User']['last_name'] = $this->request->data['Student']['last_name'];
				$this->request->data['User']['middle_name'] = $this->request->data['Student']['middle_name'];
				$this->request->data['User']['email'] = $this->request->data['Student']['email'];
				// Higher education information won't be present in many cases, do not
				// store it if nothing is entered.

				if (isset($this->request->data['HigherEducationBackground'])) {
					$save_higher_education = false;
					foreach ($this->request->data['HigherEducationBackground'] as $k => $v) {
						if (
							!empty($v['name']) || !empty($v['diploma_awarded']) ||
							!empty($v['date_graduated']) || !empty($v['name'])
							|| !empty($v['cgpa_at_graduation'])
						) {
							$save_high_education = true;
						}
					}
					if (!$save_higher_education) {
						unset($this->request->data['HigherEducationBackground']);
					}
				}
				// High school information won't be present, do not
				// store it if nothing is entered.
				if (
					$this->request->data['Student']['program_id'] != PROGRAM_UNDEGRADUATE
					|| $this->request->data['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR
				) {
					if (isset($this->request->data['HighSchoolEducationBackground'])) {
						$save_highschool_education = false;
						foreach ($this->request->data['HighSchoolEducationBackground'] as $k => $v) {
							if (
								!empty($v['name']) || !empty($v['region']) ||
								!empty($v['town']) || !empty($v['zone'])
								|| !empty($v['school_level'])
							) {
								$save_highschool_education = true;
							}
						}
						if (!$save_highschool_education) {
							unset($this->request->data['HighSchoolEducationBackground']);
						}
					}
				}

				$this->request->data = $this->Student->unset_empty($this->request->data);

				if ($this->Student->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Session->setFlash(__('<span></span>The student has been saved'), 'default', array('class' => 'success-box success-message'));

					$this->redirect(array('action' => 'admit'));
				} else {
					$this->Session->setFlash(__('<span></span>The student could not be saved. Please, try again.'), 'default', array('class' => 'error-box error-message'));
					$this->set('id', $this->request->data['Student']['accepted_student_id']);
				}
			} else {
				$this->Session->setFlash(__('<span></span>The student has already admitted '), 'default', array('class' => 'warning-box warning-message'));
				$this->redirect(array('edit', $id));
			}
			$this->set('admitsearch', true);
		}

		if (!empty($this->request->data) && isset($this->request->data['getacceptedstudent'])) {
			$this->__init_search();
			if (!empty($this->request->data['AcceptedStudent']['academicyear'])) {
				$conditions = null;
				$ssacdemicyear = $this->request->data['AcceptedStudent']['academicyear'];
				$college_ids = array();
				$department_ids = array();
				if (!empty($this->college_ids)) {
					$college_ids = $this->college_ids;
				} elseif (!empty($this->department_ids)) {
					$department_ids = $this->department_ids;
				}
				// retrive list of students based on registrar clerk assigned responsibility
				if (!empty($college_ids)) {
					if (!empty($this->request->data['AcceptedStudent']['college_id'])) {

						$conditions = array(
							"AcceptedStudent.academicyear LIKE" => "$ssacdemicyear%",
							"AcceptedStudent.college_id" => $this->request->data['AcceptedStudent']['college_id'],

							"AcceptedStudent.id NOT IN (select accepted_student_id from students where
		                     accepted_student_id is not null)",
						);
					} else {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE" => "$ssacdemicyear%",
							"AcceptedStudent.college_id" => $college_ids,

							"AcceptedStudent.id NOT IN (select accepted_student_id from students
		                    where accepted_student_id is not null)"
						);
					}
				} elseif (!empty($department_ids)) {

					if (!empty($this->request->data['AcceptedStudent']['department_id'])) {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE" => "$ssacdemicyear%",
							"AcceptedStudent.department_id" => $this->request->data['AcceptedStudent']['department_id'],

							"AcceptedStudent.id NOT IN (select accepted_student_id from students
		                     where accepted_student_id is not null)",
						);
					} else {
						$conditions = array(
							"AcceptedStudent.academicyear LIKE" => "$ssacdemicyear%",
							"AcceptedStudent.department_id" => $department_ids,
							"AcceptedStudent.id NOT IN (select accepted_student_id from students
		                     where accepted_student_id is not null)",
						);
					}
				}
				if (!empty($this->request->data['AcceptedStudent']['program_id'])) {
					$conditions['AcceptedStudent.program_id'] = $this->request->data['AcceptedStudent']['program_id'];
				} else {
					$conditions['AcceptedStudent.program_id'] = $this->program_id;
				}
				if (!empty($this->request->data['AcceptedStudent']['program_type_id'])) {
					$conditions['AcceptedStudent.program_type_id'] = $this->request->data['AcceptedStudent']['program_type_id'];
				} else {
					$conditions['AcceptedStudent.program_type_id'] = $this->program_type_id;
				}
				debug($conditions);
				if (!empty($conditions)) {
					$this->paginate = array('limit' => 50000, 'contain' => array('Student', 'College', 'Department', 'Program', 'ProgramType', 'Region', 'User'));
					$this->paginate['conditions'] = $conditions;
					$this->Paginator->settings = $this->paginate;
					$acceptedStudents = $this->Paginator->paginate('AcceptedStudent');
					debug($acceptedStudents);
					$this->set('acceptedStudents', $acceptedStudents);
					if (!empty($acceptedStudents)) {
						$this->set('admitsearch', true);
					} else {
						$this->Session->setFlash(__('<span></span>No data is found with your search criteria'), 'default', array('class' => 'info-box info-message'));
					}
					$this->request->data['getacceptedstudent'] = true;
					$admitsearch = true;
				} else {
					$this->Session->setFlash(__('<span></span>You dont have privilage to admit students in the given criteria.'), 'default', array('class' => 'error-box error-message'));
				}

				$curriculums =  ClassRegistry::init('Curriculum')->find('list', array('fields' => array('Curriculum.id', 'Curriculum.curriculum_detail'), 'conditions' => array(
					'Curriculum.department_id' =>  $this->request->data['Student']['department_id'],
					'Curriculum.program_id' => $this->request->data['Student']['program_id'],
					//'Curriculum.program_type_id' => $this->request->data['Student']['program_type_id'],
					'Curriculum.registrar_approved' => 1,
				)));
				$this->set(compact('curriculums'));
			} else {
				$this->Flash->error(__('Please select academic  year'));
			}
		}

		if ($id) {
			$is_student_id_exist = $this->Student->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.id' => $id)));
			if ($is_student_id_exist) {
				$this->set(compact('id'));
				$this->set('admitsearch', true);
				$data = $this->Student->AcceptedStudent->find('first', array('conditions' => array('AcceptedStudent.id' => $id)));

				$data_import = array();

				if (!empty($data)) {

					unset($data['Preference']);
					unset($data['Student']);
					$data_import = $data;

					$data_import['Student']['accepted_student_id'] = $data['AcceptedStudent']['id'];
					$data_import['Student']['first_name'] = $data['AcceptedStudent']['first_name'];
					$data_import['Student']['middle_name'] = $data['AcceptedStudent']['middle_name'];
					$data_import['Student']['last_name'] = $data['AcceptedStudent']['last_name'];
					$data_import['Student']['studentnumber'] = $data['AcceptedStudent']['studentnumber'];
					$data_import['Student']['region_id'] = $data['AcceptedStudent']['region_id'];

					if (!empty($data['AcceptedStudent']['zone_id'])) {
						$data_import['Student']['zone_id'] = $data['AcceptedStudent']['zone_id'];
					}

					if (!empty($data['AcceptedStudent']['woreda_id'])) {
						$data_import['Student']['woreda_id'] = $data['AcceptedStudent']['woreda_id'];
					}

					if (!empty($data['AcceptedStudent']['original_college_id'])) {
						$data_import['Student']['original_college_id'] = $data['AcceptedStudent']['original_college_id'];
					} else {
						$data_import['Student']['original_college_id'] = $data_import['AcceptedStudent']['college_id'];
					}

					$data_import['Student']['college_id'] = $data_import['AcceptedStudent']['college_id'];
					$data_import['Student']['department_id'] = $data['AcceptedStudent']['department_id'];
					$data_import['Student']['program_id'] = $data['AcceptedStudent']['program_id'];
					$data_import['Student']['program_type_id'] = $data['AcceptedStudent']['program_type_id'];
					$data_import['Student']['gender'] = $data['AcceptedStudent']['sex'];
					$data_import['Student']['curriculum_id'] = $data['AcceptedStudent']['curriculum_id'];
					$data_import['User']['id'] = $data['User']['id'];
					$data_import['User']['role_id'] = $data['User']['role_id'];

					unset($data['AcceptedStudent']);
				}

				$this->request->data = $data_import;
			}
		}

		// display the right department and college based on the privilage of registrar users
		if ($this->role_id == ROLE_REGISTRAR) {
			$college_ids = array();
			$department_ids = array();

			if (!empty($this->college_ids)) {
				$college_ids = $this->college_ids;
				$this->set('colleges', $this->Student->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1))));
				$this->set('departments', $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $college_ids, 'Department.active' => 1))));
				$this->set('college_level', true);
			} elseif (!empty($this->department_ids)) {
				$department_ids = $this->department_ids;
				$this->set('departments', $this->Student->Department->find('list', array('conditions' => array('Department.id' => $department_ids, 'Department.active' => 1))));
				$this->set('colleges', $this->Student->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1))));
				$this->set('department_level', true);
			}

			$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));
			$this->set(compact('colleges'));
		} else {
			$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));
			$departments = $this->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
			$this->set(compact('colleges', 'departments'));
		}

		$regions = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1)));
		$countries = $this->Student->Country->find('list');
		$cities = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));

		$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
		$woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));

		$this->set(compact(
			'programs',
			'programTypes',
			'regions',
			'countries',
			'cities',
			'zones',
			'woredas'
		));
	}

	function get_countries($region_id = null)
	{
		$this->layout = 'ajax';
		
		if (!empty($region_id)) {
			$country_ids = $this->Student->Region->find('list', array('conditions' => array('Region.id' => $region_id), 'fields' => array('Region.country_id', 'Region.country_id')));
			$countries = $this->Student->Country->find('list', array('conditions' => array('Country.id' => $country_ids)));
		} else if (isset($this->request->data['Student']['region_id']) && !empty($this->request->data['Student']['region_id'])) {
			$country_ids = $this->Student->Region->find('list', array('conditions' => array('Region.id' => $this->request->data['Student']['region_id']), 'fields' => array('Region.country_id', 'Region.country_id')));
			$countries = $this->Student->Country->find('list', array('conditions' => array('Country.id' => $country_ids)));
		} else if (isset($this->request->data['Student']['country_id']) && !empty($this->request->data['Student']['country_id'])) {
			$countries = $this->Student->Country->find('list', array('conditions' => array('Country.id' => $this->request->data['Student']['country_id'])));
		} else {
			$countries = $this->Student->Country->find('list');
		}

		debug($countries);

		$this->set(compact('countries'));
	}

	function get_regions($country_id = null)
	{
		$this->layout = 'ajax';

		if ($country_id) {
			$regions = $this->Student->Region->find('list', array('conditions' => array('Region.country_id' => $country_id)));
		} else {
			$regions = $this->Student->Region->find('list', array('conditions' => array('Region.country_id' => $this->request->data['Student']['country_id'])));
		}

		$this->set(compact('regions'));
	}

	function get_zones($region_id = null)
	{
		$this->layout = 'ajax';

		if ($region_id) {
			$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.region_id' => $region_id)));
		} else {
			$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.region_id' => $this->request->data['Student']['region_id'])));
		}

		$this->set(compact('zones'));
	}

	function get_woredas($zone_id = null)
	{
		$this->layout = 'ajax';

		if ($zone_id) {
			$woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.zone_id' => $zone_id)));
		} else {
			$woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.zone_id' => $this->request->data['Student']['zone_id'])));
		}

		$this->set(compact('woredas'));
	}

	function get_cities($region_id = null)
	{
		$this->layout = 'ajax';

		if ($region_id) {
			$cities = $this->Student->City->find('list', array('conditions' => array('City.region_id' => $region_id)));
		} else {
			$cities = $this->Student->City->find('list', array('conditions' => array('City.region_id' => $this->request->data['Student']['region_id'])));
		}

		$this->set(compact('cities'));
	}

	function ajax_get_department()
	{
		$this->layout = 'ajax';

		$this->set('departments', $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Staff'][0]['college_id']))));
		// init departments
		/*$college_id = $this->request->data['id'];


	        $departments = array();
	        $this->layout = null;




	        if($college_id > 0) {
		            // get departments
		            $departments = $this->Department->find('list',array('conditions'=>array('Department.college_id'=>$college_id)));

	        }

	        // set
	        $this->set(compact('departments'));
        */
	}
	/*
    function issue_password() {
        //issue students password


	    if(!empty($this->request->data) &&
	    isset($this->request->data['issuepasswordtostudent'])){
	            $this->set($this->request->data);
	            if($this->Student->User->validates()){
	                //debug($this->request->data);
	                if (!empty($this->request->data['User']['password'])) {
	                $this->request->data['User']['password'] =
				    $this->Auth->password($this->request->data['User']['password']);
				    $this->request->data['User']['role_id']=ROLE_STUDENT;
	                if($this->Student->User->save($this->request->data['User'])){
	                     $this->request->data=null;

	                    $this->Session->setFlash('<span></span>The student password has been updated. ','default',
		           array('class'=>'success-box success-message'));


	                } else {
	                   $this->Session->setFlash('<span></span>The student password could not be updated. ','default',
		           array('class'=>'error-box error-message'));
	                }

	               } else {

	                  $this->Session->setFlash('<span></span>The issued password coudnt be empty. Please generate password first before issue. ','default',
		           array('class'=>'error-box error-message'));
	               }
	            }
	    }
       if(!empty($this->request->data)&&isset($this->request->data['issuestudentidsearch'])){

               if(!empty($this->request->data['Student']['studentnumber'])){
		               $students=$this->Student->find('first',array('conditions'=>array('Student.studentnumber LIKE '=>
			         trim($this->request->data['Student']['studentnumber']).'%')));

		             if ($this->role_id == ROLE_DEPARTMENT) {

			             if (!empty($students)) {

			                  $students=$this->Student->find('first',
			                 array('conditions'=>array('Student.studentnumber LIKE'=>
			                 trim($this->request->data['Student']['studentnumber']).'%',
			                 'Student.department_id'=>$this->department_id)));

			                if (!empty($students)) {
			                     if (!empty($students['Student']['department_id'])) {


			                     } else if ($students['Student']['college_id'] != $this->college_id) {
			                         $this->Session->setFlash('<span></span>
			                      You are not elegible to issue password.','default',
		                        array('class'=>'info-box info-message'));
			                     }

			                }
			             }


		             } else if ($this->role_id == ROLE_COLLEGE) {


			             if (!empty($students)) {

			                 $students=$this->Student->find('first',
			                 array('conditions'=>array('Student.studentnumber LIKE'=>
			                 trim($this->request->data['Student']['studentnumber']).'%',
			                 'Student.college_id'=>$this->college_id,
			                 'OR'=>array('Student.department_id is null',
			                 'Student.department_id'=>array(0,'')))));

			                if (!empty($students)) {
			                     if (!empty($students['Student']['department_id'])) {

			                     $this->Session->setFlash('<span></span>
			                      You can not issued password. Department is responsbile to manage those students who joined department. ','default',
		                        array('class'=>'info-box info-message'));
			                     } else if ($students['Student']['college_id'] != $this->college_id) {
			                         $this->Session->setFlash('<span></span>
			                      You are not elegible to issue password.','default',
		                        array('class'=>'info-box info-message'));
			                     }

			                }
			             }
		             }

			        //debug($students);
			        if(empty($students)){
			          $this->Session->setFlash('<span></span>Please enter a valid student number','default',
		           array('class'=>'error-box error-message'));
			        } else {
			               $this->set('students',$students);
			               $this->set('hide_search',true);
			               $this->set('student_number',$this->request->data['Student']['studentnumber']);
			        }

		       } else {
		           $this->Session->setFlash('<span></span>Please enter student number','default',
		           array('class'=>'error-box error-message'));
		       }

       }

		$this->set('studentks', $this->paginate());

    }
    */


	// this function has a known issue, please dont use it use department_issue_password or college_issue_password instead

	function issue_password()
	{
		//debug($this->request->data);
		if (!empty($this->request->data) && isset($this->request->data['issuepasswordtostudent'])) {

			// check password length
			/* $this->loadModel('Securitysetting');

			$securitysetting = $this->Securitysetting->find('first');

			if (strlen($this->request->data['User']['passwd']) >= $securitysetting['Securitysetting']['minimum_password_length'] && strlen($this->request->data['User']['passwd']) <= $securitysetting['Securitysetting']['maximum_password_length']) {

				// if (!empty($this->request->data['User']['password'])) {
				
				debug($this->request->data);

				$this->request->data['User']['role_id'] = ROLE_STUDENT;
				$this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['passwd']);
				
				unset($this->request->data['User']['passwd']);

				$username = $this->Student->User->find('first', array('conditions' => array('User.username' => $this->request->data['User']['username']), 'recursive' => -1));

				if (!empty($username)) {
					$this->request->data['User']['id'] = $username['User']['id'];
				}

				$this->request->data['User']['force_password_change'] = 1;

				if ($this->Student->User->save($this->request->data['User'])) {

					// if the issued is the first time, update  user_id field in student table
					if (empty($this->request->data['User']['id'])) {

						$this->request->data['Student']['user_id'] = $this->Student->User->id;
						$this->Student->id = $this->request->data['Student']['id'];

						$this->Student->saveField('user_id', $this->request->data['Student']['user_id']);
					}

					$student = $this->Student->find('first', array('conditions' => array('Student.id' => $this->request->data['Student']['id']), 'recursive' => -1, 'fields' => array('id', 'user_id')));

					if (!empty($student)) {

						if (!empty($this->request->data['User']['id'])) {
							$student['Student']['user_id'] = $this->request->data['User']['id'];
						} else {
							$student['Student']['user_id'] = $this->Student->User->id;
						}

						$this->Student->id = $student['Student']['id'];
						$this->Student->saveField('user_id', $student['Student']['user_id']);

						$this->Student->AcceptedStudent->id = $student['Student']['accepted_student_id'];
						$this->Student->AcceptedStudent->saveField('user_id', $student['Student']['user_id']);

						$this->Flash->success('The student password has been updated.');
					} else {
						$this->Flash->success('The student password has been updated.');
					}

					$this->request->data = null;
				} else {
					$this->Flash->error('The student password could not be updated. ');
				}
			} else {
				$this->Flash->error(__('Password policy: Your password should be greather than or equal to ' . $securitysetting['Securitysetting']['minimum_password_length'] . ' and less than or equal to ' . $securitysetting['Securitysetting']['maximum_password_length'] . ''));
			} */
		}

		if (!empty($this->request->data) && isset($this->request->data['issuestudentidsearch'])) {

			if (!empty($this->request->data['Student']['studentnumber'])) {

				$students = array();

				if ($this->role_id == ROLE_DEPARTMENT) {

					$students = $this->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber  LIKE ' => trim($this->request->data['Student']['studentnumber']) . '%',
							'Student.department_id' => $this->department_id
						),
						'contain' => array(
							'User', 
							'AcceptedStudent', 
							'Program', 
							'College',
							'Department', 
							'ProgramType'
						)
					));

					if (!empty($students)) {
						$this->set('students', $students);
						$this->set('hide_search', true);
						$this->set('student_number', $this->request->data['Student']['studentnumber']);
					} else {
						$this->Flash->warning('You are not elegible to issue/reset password.The student is not belongs to your  department.');
					}
				} else if ($this->role_id == ROLE_COLLEGE) {

					$students = $this->Student->find('first', array(
						'conditions' => array('Student.studentnumber LIKE ' => trim($this->request->data['Student']['studentnumber']) . '%'), 
						'contain' => array(
							'User', 
							'AcceptedStudent', 
							'Program', 
							'College',
							'Department', 
							'ProgramType'
						)
					));

					if (!empty($students)) {
						$students = $this->Student->find('first', array(
							'conditions' => array(
								'Student.studentnumber LIKE' => trim($this->request->data['Student']['studentnumber']) . '%',
								'Student.college_id' => $this->college_id,
								'Student.department_id is null',
							)
						));

						if (empty($students)) {
							$this->Flash->warning('You are not elegible to issue/reset password. The student has already assigned to department. Department is responsible for  password  issue or reset.');
						} else {
							$this->set('students', $students);
							$this->set('hide_search', true);
							$this->set('student_number', $this->request->data['Student']['studentnumber']);
						}
					} else {
						if (empty($students)) {
							$this->Flash->error('Please enter a valid student number');
						}
					}
				}
			} else {
				$this->Flash->error('Please enter student number');
			}
		}

		//$this->set('studentks', $this->paginate());

	}


	function profile($student_id = null)
	{

		$check_student_admitted = $this->Student->find('count', array('conditions' => array('Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0)))));
		
		if ($check_student_admitted == 0) {
			$this->Flash->info('You profile will be available after registrar finishes the admission data entry.');
			$this->redirect('/dashboard/index');
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {

			$require_update = false;
			$require_update_fields = array();
			$rupdt_key = 0;

			$studentDetail = $this->Student->find('first', array(
				'conditions' => array(
					'Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))
				),
				'contain' => array(
					'User',
					'AcceptedStudent',
					'Program',
					'ProgramType',
					'Contact',
					'Department',
					'College',
					'EslceResult',
					'EheeceResult',
					'Attachment',
					'HigherEducationBackground',
					'HighSchoolEducationBackground',
					'Country',
					'Region',
					'City',
					'Zone',
					'Woreda'
				)
			));

			$student_admission_year = ((int) (isset($studentDetail['AcceptedStudent']['academicyear']) && !empty($studentDetail['AcceptedStudent']['academicyear']) ? (explode('/', $studentDetail['AcceptedStudent']['academicyear'])[0]) : (isset($studentDetail['Student']['academicyear']) && !empty($studentDetail['Student']['academicyear']) ? (explode('/', $studentDetail['Student']['academicyear'])[0]) : (explode('/', $this->AcademicYear->current_academicyear())[0]))));


			if ($this->Auth->user('id') !=  $studentDetail['Student']['user_id']) {
				$this->Flash->error(__('There is a conflictiong session please login again.'));
				$this->Session->destroy();
				$this->redirect($this->Auth->logout());
			}

			//debug($studentDetail);

			if (!empty($this->request->data) && isset($this->request->data['updateStudentDetail'])) {

				unset($this->request->data['User']);

				if (isset($this->request->data['AcceptedStudent'])) {
					unset($this->request->data['AcceptedStudent']);
				}

				if (isset($this->request->data['College'])) {
					unset($this->request->data['College']);
				}

				//unset($this->request->data['Student']['gender']);

				if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "female") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "f") == 0) {
					$this->request->data['Student']['gender'] = 'Female';
				} else if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "male") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "m") == 0 ) {
					$this->request->data['Student']['gender'] = 'Male';
				} else {
					$this->request->data['Student']['gender'] = (ucfirst(strtolower(trim($studentDetail['AcceptedStudent']['sex']))));
				}
				
				if (!empty($this->request->data['Student']['email'])) {
					$this->request->data['User']['email'] = trim($this->request->data['Student']['email']);
					if ($this->role_id == ROLE_STUDENT && $this->Auth->user('id') ==  $studentDetail['Student']['user_id']) {
						$this->request->data['User']['id'] = $this->Auth->user('id');
					} else if ($studentDetail['Student']['user_id']) {
						$this->request->data['User']['id'] = $studentDetail['Student']['user_id'];
					} else {
						
						$student_user_id = $this->Student->User->field('User.id', array('User.username LIKE ' => $studentDetail['Student']['studentnumber'], 'User.role_id' => ROLE_STUDENT));

						if (!empty($student_user_id)) {
							$this->request->data['User']['id'] = $student_user_id;
						}
					}
				}

				// $student_user_id = $this->Student->User->field('User.id', array('User.username LIKE ' => $studentDetail['Student']['studentnumber'], 'User.role_id' => ROLE_STUDENT));
				// debug($student_user_id);

				if (isset($this->request->data['updateStudentDetail'])) {


					if (!empty($this->request->data['Student']['phone_mobile']) && !empty($this->request->data['Student']['email'])) {

						$this->request->data = $this->Student->unset_empty($this->request->data);

						//debug($this->request->data);

						/* if (isset($this->request->data['Contact']) && (empty($this->request->data['Contact'][0]['first_name']) || empty($this->request->data['Contact'][0]['middle_name']) || empty($this->request->data['Contact'][0]['last_name']) || empty($this->request->data['Contact'][0]['phone_mobile']))) {
							unset($this->request->data['Contact']);
						} */

						if (empty($this->request->data['Student']['city_id'])) {
							unset($this->request->data['Student']['city_id']);
						}

						if (isset($this->request->data['Attachment']) && (empty($this->request->data['Attachment'][0]['file']['name']) || $this->request->data['Attachment'][0]['file']['error'])) {
							unset($this->request->data['Attachment']);
						}

						if (isset($this->request->data['HighSchoolEducationBackground']) && (empty($this->request->data['HighSchoolEducationBackground'][0]['name']) || empty($this->request->data['HighSchoolEducationBackground'][0]['town']) || empty($this->request->data['HighSchoolEducationBackground'][0]['region_id']))) {
							unset($this->request->data['HighSchoolEducationBackground']);
						}

						if (isset($this->request->data['HigherEducationBackground']) && (empty($this->request->data['HigherEducationBackground'][0]['name']) || empty($this->request->data['HigherEducationBackground'][0]['field_of_study']) || empty($this->request->data['HigherEducationBackground'][0]['diploma_awarded']) || empty($this->request->data['HigherEducationBackground'][0]['cgpa_at_graduation']))) {
							unset($this->request->data['HigherEducationBackground']);
						}

						if (isset($this->request->data['EheeceResult']) && (empty($this->request->data['EheeceResult'][0]['subject']) || empty($this->request->data['EheeceResult'][0]['mark']) /* || empty($this->request->data['EheeceResult'][0]['exam_year']) */)) {
							unset($this->request->data['EheeceResult']);
						}

						if (isset($this->request->data['EslceResult']) && (empty($this->request->data['EslceResult'][0]['subject']) || empty($this->request->data['EslceResult'][0]['grade']) || empty($this->request->data['EslceResult'][0]['exam_year']))) {
							unset($this->request->data['EslceResult']);
						}

						unset($this->request->data['updateStudentDetail']); 

						debug($this->request->data);
						
						if ($this->Student->saveAll($this->request->data, array('validate' => 'first'))) {
							$this->Flash->success(__('Your Profile has been updated.'));
							//return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
							return $this->redirect(array('action' => 'profile'));
						} else {
							$this->Flash->error( __('Your student profile could not be saved. Please, try again.'));
						}
					} else {
						if (empty($this->request->data['Student']['phone_mobile']) && empty($this->request->data['Student']['email'])) {
							$this->Flash->error(__('Please provide your mobile phone number and personal email address. You can use ' . (strtolower(str_replace('/', '.', $studentDetail['Student']['studentnumber'])) . INSTITUTIONAL_EMAIL_SUFFIX) . ' if you don\'t have personal email address like Gmail, yahoo, hotmail etc..'));	
						} else if (empty($this->request->data['Student']['phone_mobile'])) {
							$this->Flash->error(__('Please provide your mobile phone number.'));	
						} else {
							$this->Flash->error(__('Please provide your personal email address. You can use ' . (strtolower(str_replace('/', '.', $studentDetail['Student']['studentnumber'])) . INSTITUTIONAL_EMAIL_SUFFIX) . ' if you don\'t have personal email address like Gmail, yahoo, hotmail etc..'));
						}
					}
				}
			}

			if (empty($this->request->data)) {
				$this->request->data = $this->Student->find('first', array(
					'conditions' => array(
						'Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))
					),
					'contain' => array(
						'User',
						'AcceptedStudent',
						'Program',
						'ProgramType',
						'Department', 
						'College',
						'Contact',
						'EslceResult',
						'EheeceResult',
						'Attachment',
						'HigherEducationBackground',
						'HighSchoolEducationBackground',
						'Country',
						'Region',
						'City',
						'Zone',
						'Woreda'
					)
				));
			}

			$this->request->data['Student']['gender'] = (isset($this->request->data['AcceptedStudent']['sex']) ?  (ucfirst(strtolower(trim($this->request->data['AcceptedStudent']['sex'])))) : (ucfirst(strtolower(trim($this->request->data['Student']['gender'])))));

			if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "female") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "f") == 0 ) {
				$this->request->data['Student']['gender'] = 'Female';
			} else if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "male") == 0 || strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "m") == 0 ) {
				$this->request->data['Student']['gender'] = 'Male';
			} else {
				$this->request->data['Student']['gender'] = (ucfirst(strtolower(trim($studentDetail['AcceptedStudent']['sex']))));
			}

			if (isset($this->request->data['EheeceResult'][0]['exam_year']) && !empty($this->request->data['EheeceResult'][0]['exam_year']) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->data['EheeceResult'][0]['exam_year'], ($student_admission_year - 10), $student_admission_year)) {
				$require_update = true;
				$require_update_fields[$rupdt_key]['field'] = 'EHEECE Exam Taken Date';
				$require_update_fields[$rupdt_key]['previous_value'] = $this->request->data['EheeceResult'][0]['exam_year'];
				$this->request->data['EheeceResult'][0]['exam_year'] =  $student_admission_year .'-'. '07-01';
				$require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->data['EheeceResult'][0]['exam_year'];
				$require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date is not valid date.';

				if (((int) explode('-', $studentDetail['EheeceResult'][0]['exam_year'])[0]) > $student_admission_year) {
					$require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date can\'t be behind Student Admission Year.';
				}
				
				$rupdt_key++;
			} else if (empty($studentDetail['EheeceResult'])) {
				$this->request->data['EheeceResult'][0]['exam_year'] =  $student_admission_year .'-'. '07-01';
			}


			$maximum_estimated_graduation_year_limit =  $student_admission_year;
			
			if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $studentDetail['Student']['program_id'] == PROGRAM_PhD) {
				$maximum_estimated_graduation_year_limit =  $student_admission_year + 6;
			} else if ($studentDetail['Student']['program_id'] == PROGRAM_POST_GRADUATE) {
				if ($studentDetail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
					$maximum_estimated_graduation_year_limit =  $student_admission_year + 3;
				} else {
					$maximum_estimated_graduation_year_limit =  $student_admission_year + 6;
				}
			} else {
				// Remedial and PGDT
				$maximum_estimated_graduation_year_limit =  $student_admission_year;
			}
			

			if (!empty($studentDetail['Student']['curriculum_id']) && $studentDetail['Student']['curriculum_id'] > 0) {

				$get_curriculum_year_level_count = $this->Student->Curriculum->Course->find('count', array('conditions' => array('Course.curriculum_id' => $studentDetail['Student']['curriculum_id']), 'group' => array('Course.year_level_id')));

				if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $studentDetail['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
					if (!empty($get_curriculum_year_level_count )) {
						$maximum_estimated_graduation_year_limit = $student_admission_year + ($get_curriculum_year_level_count * 2);
					}
				}
				
				//debug($get_curriculum_year_level_count);

				if (isset($this->request->data['Student']['estimated_grad_date']) && !empty($this->request->data['Student']['estimated_grad_date']) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->data['Student']['estimated_grad_date'], $student_admission_year, ($student_admission_year + ($get_curriculum_year_level_count * 2)))) {
					$require_update = true;
					$require_update_fields[$rupdt_key]['field'] = 'Estimated Graduation Date';
					$require_update_fields[$rupdt_key]['previous_value'] = $this->request->data['Student']['estimated_grad_date'];
					$this->request->data['Student']['estimated_grad_date'] =  ($student_admission_year + $get_curriculum_year_level_count) .'-08-01';
					$require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->data['Student']['estimated_grad_date'];
					$require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date is not valid date.';

					if (((int) explode('-', $studentDetail['Student']['estimated_grad_date'])[0]) > ($student_admission_year + ($get_curriculum_year_level_count * 2))) {
						$require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be behind ' . ($student_admission_year + ($get_curriculum_year_level_count * 2)) . ' G.C. (Double of student\'s attached curriculum year levels, ' . $get_curriculum_year_level_count . ' X 2 years)';
					} else if (((int) explode('-', $studentDetail['Student']['estimated_grad_date'])[0]) < ($student_admission_year + $get_curriculum_year_level_count)) {
						$require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be before ' . ($student_admission_year + $get_curriculum_year_level_count) . ' G.C.';
					}
					
					$rupdt_key++;
				} else if (empty($studentDetail['Student']['estimated_grad_date'])) {
					$this->request->data['Student']['estimated_grad_date'] = ($student_admission_year + $get_curriculum_year_level_count) .'-08-01';
				}
			} else if (empty($studentDetail['Student']['estimated_grad_date']) || is_null($studentDetail['Student']['estimated_grad_date'])) {
				$this->request->data['Student']['estimated_grad_date']  = $maximum_estimated_graduation_year_limit .'-08-01';
			}

			//debug($this->request->data);

			//$foriegn_students_region_ids = Configure::read('foriegn_students_region_ids');

			$foriegn_students_region_ids = $this->Student->Region->find('list', array('conditions' => array('Region.country_id <> ' => COUNTRY_ID_OF_ETHIOPIA), 'fields' => array('Region.id', 'Region.id')));

			debug($foriegn_students_region_ids);

			$regions = array();
			$zones = array();
			$woredas = array();
			$cities = array();

			$foriegn_student = 0;

			$country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;

			$region_id_of_student = '';

			if (!empty($studentDetail['AcceptedStudent']['region_id']) || !empty($studentDetail['Student']['region_id'])) {
				
				$region_id_of_student = (!empty($studentDetail['AcceptedStudent']['region_id']) ? $studentDetail['AcceptedStudent']['region_id'] : $studentDetail['Student']['region_id']);

				$country_id_of_region = $this->Student->Region->field('country_id', array('Region.id' => $region_id_of_student));
				
				$countries = $this->Student->Country->find('list', array('conditions' => array('Country.id' => $country_id_of_region)));
				
				$regions = $this->Student->Region->find('list', array(
					'conditions' => array(
						'Region.id' =>  $region_id_of_student,
						'Region.country_id' => $country_id_of_region
					)
				));

				$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.region_id' => $region_id_of_student)));

				$city_zone_ids = $this->Student->City->find('list', array(
					'conditions' => array(
						'City.region_id' => $region_id_of_student
					),
					'fields' => array('City.zone_id', 'City.zone_id')
				));

				$woredas = $this->Student->Woreda->find('list', array(
					'conditions' => array(
						'Woreda.zone_id' => (!empty($zones) ? array_keys($zones) : (!empty($city_zone_ids) ? $city_zone_ids : NULL)),
					)
				));

				$cities = $this->Student->City->find('list', array(
					'conditions' => array(
						'OR' => array(
							'City.id' => $studentDetail['Student']['city_id'],
							'City.zone_id' => (!empty($zones) ? array_keys($zones) : (!empty($studentDetail['AcceptedStudent']['zone_id']) ? $studentDetail['AcceptedStudent']['zone_id'] : $studentDetail['Student']['zone_id'])),
							'City.region_id' => $region_id_of_student,
						)
					)
				));

			} else {
				$countries = $this->Student->Country->find('list');
				$regions = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1)));
				$zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
				$woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
				$cities = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));
			}

			if (empty($regions)) {
				$regions = $this->Student->Region->find('list', array('conditions' => array('Region.country_id' => $country_id_of_region)));
			}

			if (empty($zones)) {
				$zones = $this->Student->Zone->find('list');
			}

			if (empty($woredas)) {
				$woredas = $this->Student->Woreda->find('list');
			}

			if (empty($cities)) {
				if (!empty($region_id_of_student)) {
					$cities = $this->Student->City->find('list', array('conditions' => array('City.region_id' => $region_id_of_student)));
				} else if (!empty($regions)) {
					$cities = $this->Student->City->find('list', array('conditions' => array('City.region_id' => array_keys($regions))));
				} else {
					$cities = $this->Student->City->find('list');
				}
			}

			if (!empty($foriegn_students_region_ids) && ((isset($studentDetail['AcceptedStudent']['region_id']) && !empty($studentDetail['AcceptedStudent']['region_id']) && in_array($studentDetail['AcceptedStudent']['region_id'], $foriegn_students_region_ids)) || (isset($studentDetail['Student']['region_id']) && !empty($studentDetail['Student']['region_id']) && in_array($studentDetail['Student']['region_id'], $foriegn_students_region_ids)))) {
				$foriegn_student = 1;
			}

			$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $studentDetail['Student']['college_id'])));
			
			if (!empty($studentDetail['Student']['department_id']) && is_numeric($studentDetail['Student']['department_id']) && $studentDetail['Student']['department_id'] > 0) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $studentDetail['Student']['department_id'])));
			} else {
				//$departments = $this->Student->Department->find('list');
				$departments = array();
			}


			$regionsAll = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1, 'Region.country_id' => $country_id_of_region)));
			$zonesAll = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
			$woredasAll = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
			$citiesAll = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));

			if (isset($this->request->data['Contact'][0]['region_id']) && !empty($this->request->data['Contact'][0]['region_id'])) {
				$citiesAll = $this->Student->City->find('list', array('conditions' => array('City.region_id' => $this->request->data['Contact'][0]['region_id'], 'City.active' => 1)));
			}
			
			$contacts = $this->Student->Contact->find('list', array('conditions' => array('Contact.student_id' => $this->student_id)));
			$users = $this->Student->User->find('list', array('conditions' => array('User.username' => $studentDetail['Student']['studentnumber'])));
			$programs = $this->Student->Program->find('list', array('conditions' => array('Program.id' => $studentDetail['Student']['program_id'])));
			$programTypes = $this->Student->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $studentDetail['Student']['program_type_id'])));

			$this->set(compact('studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes', 'countries', 'regions', 'zones', 'woredas', 'cities', 'regionsAll', 'zonesAll', 'woredasAll', 'citiesAll', 'foriegn_student', 'require_update', 'require_update_fields', 'student_admission_year', 'maximum_estimated_graduation_year_limit'));

		} else {
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				$this->Flash->info('You can edit student profile on this page.');
				$this->redirect('/edit', $student_id);
			} else {
				$this->Flash->warning('You are not allowed to edit or view any student profile.');
				$this->redirect('/');
			}
		}
	}

	public function move_batch_student_to_department()
	{
		if (!empty($this->request->data) && !empty($this->request->data['moveSelectedSection'])) {

			$selectedSections = array();
			$done = 0;
			$targetDepartmentDetail = $this->Student->Department->find(
				'first',
				array('conditions' => array('Department.id' => $this->request->data['AcceptedStudent']['target_department_id']), 'recursive' => -1)
			);

			$sourceDepartmentDetail = $this->Student->Department->find(
				'first',
				array('conditions' => array('Department.id' => $this->request->data['AcceptedStudent']['department_id']), 'recursive' => -1)
			);


			foreach ($this->request->data['AcceptedStudent']['selected_section'] as $k => $secId) {
				if ($secId) {
					//$selectedSections[$secId]=$secId;
					$secDetail = ClassRegistry::init('Section')->find('first', array('conditions' => array('Section.id' => $secId), 'contain' => array('YearLevel')));
					$yearLevelLists = ClassRegistry::init('YearLevel')->find('all', array('conditions' => array('YearLevel.department_id' => $secDetail['Section']['department_id'], 'YearLevel.id !=' => $secDetail['Section']['year_level_id']), 'recursive' => -1));

					$studentListsInTheSection = ClassRegistry::init('StudentsSection')->find('list', array('conditions' => array('StudentsSection.section_id' => $secId), 'fields' => array('student_id', 'student_id')));
					$acceptedStudentsList = $this->Student->find('list', array('conditions' => array('Student.id' => $studentListsInTheSection), 'fields' => array('accepted_student_id', 'accepted_student_id')));
					$curriculums = $this->Student->find('all', array('conditions' => array('Student.id' => $studentListsInTheSection), 'group' => array('Student.curriculum_id'), 'recursive' => -1, 'fields' => array(
						'Student.curriculum_id',
						'count(Student.curriculum_id) as total',

					)));
					$batchCurriculumC = 0;
					$batchCurriculum = 0;
					foreach ($curriculums as $ck => $cv) {
						if ($cv[0]['total'] > $batchCurriculumC) {
							$batchCurriculumC = $cv[0]['total'];
							$batchCurriculum = $cv['Student']['curriculum_id'];
						}
					}

					if (isset($studentListsInTheSection) && !empty($studentListsInTheSection)) {
						$sectionLists = array();
						$sectionLists[] = $secDetail;
						$sectAcademicYear = $secDetail['Section']['academicyear'];
						foreach ($yearLevelLists as $yk => $yv) {
							$nextAcademicYear = ClassRegistry::init('StudentExamStatus')->getNextSemster($sectAcademicYear);
							$secDetailIn = ClassRegistry::init('Section')->find(
								'first',
								array('conditions' => array(
									'Section.year_level_id' => $yv['YearLevel']['id'], 'Section.department_id' => $secDetail['Section']['department_id'], 'Section.program_id' => $secDetail['Section']['program_id'], 'Section.program_type_id' => $secDetail['Section']['program_type_id'],
									'Section.academicyear' => $nextAcademicYear['academic_year'],
									'Section.id in (select section_id from students_sections where student_id in (' . implode(', ', $studentListsInTheSection) . '))'
								), 'contain' => array('YearLevel'))
							);
							if (isset($secDetailIn) && !empty($secDetailIn)) {
								$sectionLists[] = $secDetailIn;
								$sectAcademicYear = $secDetailIn['Section']['academicyear'];
							}
						}
						//update each data accordingly
						foreach ($sectionLists as $sk => $sv) {
							$targetSectionYearLevel = ClassRegistry::init('YearLevel')->find(
								'first',
								array('conditions' => array('YearLevel.name' => $sv['YearLevel']['name'], 'YearLevel.department_id' => $this->request->data['AcceptedStudent']['target_department_id']), 'recursive' => -1)
							);
							$countSectionStudent = ClassRegistry::init('StudentsSection')->find(
								'count',
								array('conditions' => array('StudentsSection.section_id' => $sv['Section']['id']))
							);

							if ($countSectionStudent > 0 && isset($targetSectionYearLevel) && !empty($targetSectionYearLevel)) {
								//update section
								ClassRegistry::init('Section')->updateAll(array(
									'Section.department_id' => $targetSectionYearLevel['YearLevel']['department_id'],
									'Section.year_level_id' => $targetSectionYearLevel['YearLevel']['id']
								), array('Section.id' => $sv['Section']['id']));
								// update published courses
								ClassRegistry::init('PublishedCourse')->updateAll(array(
									'PublishedCourse.department_id' => $targetSectionYearLevel['YearLevel']['department_id'],
									'PublishedCourse.year_level_id' => $targetSectionYearLevel['YearLevel']['id']
								), array(
									'PublishedCourse.section_id' => $sv['Section']['id'],
									'PublishedCourse.year_level_id' => $sv['Section']['year_level_id']
								));
								// update registration
								ClassRegistry::init('CourseRegistration')->updateAll(array('CourseRegistration.year_level_id' => $targetSectionYearLevel['YearLevel']['id']), array(
									'CourseRegistration.section_id' => $sv['Section']['id'],
									'CourseRegistration.year_level_id' => $sv['Section']['year_level_id']
								));

								// update course adds
								ClassRegistry::init('CourseAdd')->updateAll(array('CourseAdd.year_level_id' => $targetSectionYearLevel['YearLevel']['id']), array(
									'CourseAdd.published_course_id in (select id from published_courses where section_id=' . $sv['Section']['id'] . ' and year_level_id=' . $sv['Section']['year_level_id'] . ')',
									'CourseAdd.year_level_id' => $sv['Section']['year_level_id']
								));
								//update curriculums if the number of students attached only those in this batch
								if ($batchCurriculum) {
									ClassRegistry::init('Curriculum')->updateAll(array('Curriculum.department_id' => $targetDepartmentDetail['Department']['id']), array(
										'Curriculum.id' => $batchCurriculum,
										'Curriculum.department_id' => $sourceDepartmentDetail['Department']['id']
									));
									ClassRegistry::init('Course')->updateAll(array(
										'Course.department_id' => $targetDepartmentDetail['Department']['id'],
										'Course.year_level_id' => $targetSectionYearLevel['YearLevel']['id']

									), array(
										'Course.curriculum_id' => $batchCurriculum,
										'Course.year_level_id' => $sv['Section']['year_level_id']
									));
								}


								$done++;
							}
						}
					}
					//update admitted student, and accepted student
					if ($done) {
						//targetDepartmentDetail sourceDepartmentDetail
						ClassRegistry::init('AcceptedStudent')->updateAll(array(
							'AcceptedStudent.department_id' => $targetDepartmentDetail['Department']['id'],
							'AcceptedStudent.college_id' => $targetDepartmentDetail['Department']['college_id']
						), array(
							'AcceptedStudent.id' => $acceptedStudentsList,
							'AcceptedStudent.department_id' => $sourceDepartmentDetail['Department']['id']
						));

						// sourceDepartmentDetail
						ClassRegistry::init('Student')->updateAll(array(
							'Student.department_id' => $targetDepartmentDetail['Department']['id'],
							'Student.college_id' => $targetDepartmentDetail['Department']['college_id']
						), array(
							'Student.id' => $studentListsInTheSection,
							'Student.department_id' => $sourceDepartmentDetail['Department']['id']
						));
					}
				}
			}

			if ($done) {
				$this->Session->setFlash('<span></span> ' . __('The selected section students has successfully moved from ' . $sourceDepartmentDetail['Department']['name'] . ' department to ' . $targetDepartmentDetail['Department']['name'] . ' department.'), 'default', array('class' => 'success-box success-message'));
			} else {
				$this->Session->setFlash('<span></span> ' . __('No section is selected to move the students to the target department. '), 'default', array('class' => 'error-box error-message'));
			}
		}
		if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudent'])) {

			// do validation
			$everythingfine = false;
			switch ($this->request->data) {
				case empty($this->request->data['AcceptedStudent']['academicyear']):
					$this->Session->setFlash('<span></span> ' . __('Please select the academic year of the batch admitted.'), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['AcceptedStudent']['department_id']):
					$this->Session->setFlash('<span></span> ' . __('Please select the current student department you want to transfer to target department. '), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['AcceptedStudent']['target_department_id']):
					$this->Session->setFlash('<span></span> ' . __('Please select the target student department you want to transfer the batch. '), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['AcceptedStudent']['program_id']):
					$this->Session->setFlash('<span></span> ' . __('Please select the program you want to  transfer. '), 'default', array('class' => 'error-box error-message'));
					break;
				case empty($this->request->data['AcceptedStudent']['program_type_id']):
					$this->Session->setFlash('<span></span> ' . __('Please select the program type you want to transfer. '), 'default', array('class' => 'error-box error-message'));
					break;


				case $this->request->data['AcceptedStudent']['department_id'] == $this->request->data['AcceptedStudent']['target_department_id']:
					$this->Session->setFlash('<span></span> ' . __('You have selected the same department for moving, please select a different target department. '), 'default', array('class' => 'error-box error-message'));
					break;


				default:
					$everythingfine = true;
			}

			if ($everythingfine) {
				$acceptedStudent = $this->Student->AcceptedStudent->find(
					'list',
					array('conditions' => array(
						'AcceptedStudent.department_id' => $this->request->data['AcceptedStudent']['department_id'],
						'AcceptedStudent.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],
						'AcceptedStudent.program_id' => $this->request->data['AcceptedStudent']['program_id'],
						'AcceptedStudent.academicyear' => $this->request->data['AcceptedStudent']['academicyear']
					), 'recursive' => -1, 'field' => array('id', 'id'))
				);
				$admittedStudent = $this->Student->find(
					'list',
					array('conditions' => array(
						'Student.accepted_student_id' => $acceptedStudent,

						'Student.id not in (select student_id from course_exemptions)',
					), 'recursive' => -1)
				);
				$gradutingCount = $this->Student->SenateList->find(
					'count',
					array('conditions' => array('SenateList.student_id' => $admittedStudent), 'fields' => array('Student.id', 'Student.id'))
				);

				$gradutingCount = $this->Student->SenateList->find(
					'count',
					array('conditions' => array('SenateList.student_id' => $admittedStudent))
				);

				if ($gradutingCount == 0 && isset($acceptedStudent) && !empty($acceptedStudent)) {
					$yearLevelId = ClassRegistry::init('YearLevel')->find('first', array('conditions' => array('YearLevel.department_id' => $this->request->data['AcceptedStudent']['department_id'], 'YearLevel.name' => '1st'), 'recursive' => -1));

					$sectionLists = ClassRegistry::init('Section')->find('all', array('conditions' => array(

						'Section.department_id' => $this->request->data['AcceptedStudent']['department_id'],
						'Section.year_level_id' => $yearLevelId['YearLevel']['id'],

						'Section.program_id' => $this->request->data['AcceptedStudent']['program_id'],
						'Section.academicyear' => $this->request->data['AcceptedStudent']['academicyear'],

						'Section.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],

						'Section.id in (select section_id from students_sections where student_id in (' . implode(',', $admittedStudent) . '))'
					), 'contain' => array('YearLevel'), 'order' => array('Section.academicyear asc')));

					$this->set(compact('sectionLists'));
				} else {
					if ($gradutingCount > 0) {
						$this->Session->setFlash('<span></span> ' . __('Some students have graduated in selected section so not possible to move to other department. '), 'default', array('class' => 'error-box error-message'));
					}
				}
			}
		}
		$acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 7, date('Y') - 1);
		$colleges = $this->Student->College->find('list');
		$departments = $this->Student->Department->find('list');
		$programs = $this->Student->Program->find('list');
		$programTypes = $this->Student->ProgramType->find('list');
		$this->set(compact('colleges', 'departments', 'programs', 'programTypes', 'acyear_list'));
	}

	public function ajax_update()
	{
		//Step 1. Update the value in the database
		$value = $this->request->data['update_value']; //new value to save
		$field = $this->request->data['element_id'];
		$this->Student->id = $this->student_id;

		if (!$this->Student->saveField($field, $value, true)) { // Update the field
			$this->set('error', true);
		}

		$student = $this->Student->read(null, $this->student_id);

		//Step 2. Get the display value for the field if the field is a foreign key
		// See if field to be updated is a foreign key and set the display value
		if (substr($field, -3) == '_id') {
			
			// Chop off the "_id"
			$new_field = substr($field, 0, strlen($field) - 3);

			// Camelize the result to get the Model name
			$model_name = Inflector::camelize($new_field);

			// See if the model has a display name other than default "name";
			if (!empty($this->$model_name->display_field)) {
				$display_field = $this->$model_name->display_field;
			} else {
				$display_field = 'name';
			}

			// Get the display value for the id
			$value = $this->$model_name->field($display_field, array('id' => $value));
		}

		//Step 3. Set the view variable and render the view.
		$this->set('value', $value);
		$this->beforeRender();
		$this->layout = 'ajax';
	}

	function get_course_registered_and_add($student_id = "")
	{
		$this->layout = "ajax";
		$published_courses = array();

		if ($student_id != "") {
			$published_courses = $this->Student->getStudentRegisteredAndAddCourses($student_id);
		}

		$this->set(compact('published_courses'));
	}

	function get_possible_sup_registered_and_add($student_id = "")
	{
		$this->layout = "ajax";
		$published_courses = array();

		if ($student_id != "") {
			$published_courses = $this->Student->getPossibleStudentRegisteredAndAddCoursesForSup($student_id);
		}
		
		$this->set(compact('published_courses'));
	}


	/// Web services to access students from warehouse system 

	function student_lists($student_id = null)
	{

		$this->Student->bindModel(array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.archive' => 0)))));
		if ($student_id) {

			$students = $this->Student->find('all', array('conditions' => array(
				'Student.id' => $student_id, 'Student.id NOT IN (select  student_id from graduate_lists)'
			), 'fields' => array('id', 'studentnumber', 'full_name', 'department_id'), 'contain' => array('StudentsSection')));
		} else {
			$students = $this->Student->find('all', array('conditions' => array('Student.id NOT IN (select  student_id from graduate_lists)'), 'fields' => array('id', 'studentnumber', 'full_name', 'department_id'), 'contain' => array('StudentsSection')));
		}


		$sections = $this->Student->Section->find(
			'all',
			array(

				'conditions' => array(

					'Section.archive' => 0
				),

				'contain' => array('Program' => array('id', 'name'), 'ProgramType' => array('id', 'name'))
			)
		);


		$colleges = $this->Student->College->find('all', array(
			'fields' => array('College.id', 'College.name'),
			'contain' => array()
		));
		$departments = $this->Student->Department->find('all', array(
			'fields' => array('Department.id', 'Department.name', 'Department.college_id'),
			'contain' => array()
		));
		$this->set(compact('students', 'sections', 'colleges', 'departments'));
	}

	function manage_student_medical_card_number()
	{
		if (isset($this->request->data['search'])) {
			$studentnumber = $this->request->data['Student']['studentnumber'];
			if (!empty($studentnumber)) {
				$students = $this->Student->get_student_details_for_health($studentnumber);
				if (empty($students)) {
					$this->Session->setFlash('<span></span>' . __('There is not student in this ID. Please provide correct student id (format example. Reg/453/88).'), 'default', array('class' => 'error-box error-message'));
				} else {
					$this->set(compact('students'));
				}
			} else {
				$this->Session->setFlash('<span></span>' . __('Please provide student ID (format example. Reg/453/88).'), 'default', array('class' => 'info-box info-message'));
			}
		}

		if (isset($this->request->data['submit'])) {
			$this->Student->id = $this->request->data['Student']['id'];
			if ($this->Student->saveField('card_number', $this->request->data['Student']['card_number'], true)) {
				$this->Session->setFlash('<span></span>' . __(' The card number has been saved.'), 'default', array('class' => 'success-box success-message'));
			} else {
				$this->Session->setFlash('<span></span>' . __('The card number could not be saved. Please, try again.'), 'default', array('class' => 'error-box error-message'));
			}
			$students = $this->Student->get_student_details_for_health($this->request->data['Student']['studentnumber']);
			$this->set(compact('students'));
		}
	}

	function student_academic_profile($student_id = null)
	{
		$academicYR = $this->AcademicYear->current_academicyear();
		$isTheStudentDismissed = 0;
		$isTheStudentReadmitted = 0;

		if ($this->role_id == ROLE_STUDENT) {
			$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($this->student_id, null, null);

			$otps = array();
			
			if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
				$otps = $this->Student->Otp->find('all', array(
					'conditions' => array(
						'Otp.student_id' => $this->student_id,
						'Otp.active' => 1
					),
					'contain' => array(),
					'order' => array('Otp.modified' => 'DESC', 'Otp.created' => 'DESC')
				));
			}

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
						'Readmission.student_id' => $this->student_id,
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

			$this->set('isTheStudentDismissed', $isTheStudentDismissed);
			$this->set('isTheStudentReadmitted', $isTheStudentReadmitted);

			$student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult($this->student_id, $academicYR);
			$studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($this->student_id);
			$this->set(compact('student_academic_profile', 'studentAttendedSections', 'student_section_exam_status', 'otps'));
			$this->set('academicYR', $academicYR);

		} else {
			$check_id_is_valid = 0;
			if (!empty($student_id) && is_numeric($student_id)) {
				$student_id = trim($student_id);
				if ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 0) {
					if (!empty($this->department_ids)) {
						$check_id_is_valid = $this->Student->find('count', array(
							'conditions' => array(
								'Student.id' => $student_id,
								'Student.program_type_id' => $this->program_type_ids,
								'Student.program_id' => $this->program_ids,
								'Student.department_id' => $this->department_ids
							)
						));
					} else if (!empty($this->college_ids)) {
						$check_id_is_valid = $this->Student->find('count', array(
							'conditions' => array(
								'Student.id' => $student_id,
								'Student.program_type_id' => $this->program_type_ids,
								'Student.program_id' => $this->program_ids,
								'Student.college_id' => $this->college_ids
							)
						));
					}
				} else if ($this->role_id == ROLE_DEPARTMENT) {
					$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.department_id' => $this->department_ids,)));
				} else if ($this->role_id == ROLE_COLLEGE) {
					$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.id' => $student_id, 'Student.college_id' => $this->college_ids), 'recursive' => -1));
				} else if ($this->role_id == ROLE_SYSADMIN || ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 1)) {
					$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.id' => $student_id)));
				}
			}

			if (isset($check_id_is_valid) && $check_id_is_valid > 0) {

				$otps = array();

				if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
					$otps = $this->Student->Otp->find('all', array(
						'conditions' => array(
							'Otp.student_id' => $student_id,
							'Otp.active' => 1
						),
						'contain' => array(),
						'order' => array('Otp.modified' => 'DESC', 'Otp.created' => 'DESC')
					));
				}
				
				$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($student_id, null, null);

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
							'Readmission.student_id' => $student_id,
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
				
				$this->set('isTheStudentDismissed', $isTheStudentDismissed);
				$this->set('isTheStudentReadmitted', $isTheStudentReadmitted);

				$student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult($student_id, $academicYR);
				$studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($student_id);
				
				$this->set(compact('student_academic_profile', 'studentAttendedSections', 'student_section_exam_status', 'otps'));
				$this->set('academicYR', $academicYR);
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
			//debug($this->request->data);
			if (!empty($this->request->data['Student']['studentID'])) {
				$student_id_valid = $this->Student->find('count', array('conditions' => array('Student.studentnumber' => trim($this->request->data['Student']['studentID'])), 'recursive' => -1));
				if ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 0) {
					if (!empty($this->department_ids)) {
						$check_id_is_valid = $this->Student->find('count', array(
							'conditions' => array(
								'Student.studentnumber' => trim($this->request->data['Student']['studentID']),
								'Student.program_type_id' => $this->program_type_ids,
								'Student.program_id' => $this->program_ids,
								'Student.department_id' => $this->department_ids
							), 'recursive' => -1
						));
					} else if (!empty($this->college_ids)) {
						$check_id_is_valid = $this->Student->find('count', array(
							'conditions' => array(
								'Student.studentnumber' => trim($this->request->data['Student']['studentID']),
								'Student.program_type_id' => $this->program_type_ids,
								'Student.program_id' => $this->program_ids,
								'Student.college_id' => $this->college_ids
							), 'recursive' => -1
						));
					}
				} else if ($this->role_id == ROLE_DEPARTMENT) {
					$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.studentnumber' => trim($this->request->data['Student']['studentID']), 'Student.department_id' => $this->department_ids), 'recursive' => -1));
				} else if ($this->role_id == ROLE_COLLEGE) {
					$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.studentnumber' => trim($this->request->data['Student']['studentID']), 'Student.college_id' => $this->college_ids), 'recursive' => -1));
				} else if ($this->role_id == ROLE_SYSADMIN || ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 1)) {
					$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.studentnumber' => trim($this->request->data['Student']['studentID'])), 'recursive' => -1));
				}

				$studentIDs = 1;

				if ($student_id_valid == 0) {
					$this->Flash->warning('The provided Student ID is not valid.');
				} else if ($student_id_valid > 0 && $check_id_is_valid > 0) {
					$everythingfine = true;
					
					$student_id = $this->Student->field('id', array('studentnumber' => trim($this->request->data['Student']['studentID'])));

					$otps = array();

					if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
						$otps = $this->Student->Otp->find('all', array(
							'conditions' => array(
								'Otp.student_id' => $student_id,
								'Otp.active' => 1
							),
							'contain' => array(),
							'order' => array('Otp.modified' => 'DESC', 'Otp.created' => 'DESC')
						));
					}

					$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($student_id, null, null);

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
								'Readmission.student_id' => $student_id,
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
					
					$this->set('isTheStudentDismissed', $isTheStudentDismissed);
					$this->set('isTheStudentReadmitted', $isTheStudentReadmitted);

					$student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult($student_id, $academicYR);
					$studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($student_id);
					$this->set(compact('student_academic_profile', 'studentAttendedSections', 'student_section_exam_status', 'otps'));
					$this->set('academicYR', $academicYR);

				} else  {
					if ($check_id_is_valid == 0) {
						$this->Flash->warning('You don\'t have the privilage to view the selected student\'s profile.');
					} else {
						$this->Flash->warning('The provided Student ID is not valid.');
					}
				}
			} else {
				$this->Flash->error('Please provide Student ID to view Academic Profile.');
			}
		}
	}

	function get_modal_box($student_id = null)
	{
		$this->layout = 'ajax';

		if ($this->Auth->user('id')) {
			if ($this->role_id == ROLE_STUDENT) {
				$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.id' => $this->student_id)));
				$student_id = $this->student_id;
			} else if ($this->role_id != ROLE_STUDENT) {
				$check_id_is_valid = $this->Student->find('count', array('conditions' => array('Student.id' => $student_id)));
			}

			if ($check_id_is_valid > 0) {

				$academicYR = $this->AcademicYear->current_academicyear();
				$isTheStudentDismissed = 0;
				$isTheStudentReadmitted = 0;

				/* $student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult($student_id, $this->AcademicYear->current_academicyear());
				if ($this->role_id != ROLE_STUDENT) {
					$studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($student_id);
					$this->set(compact('studentAttendedSections'));
				} */

				$student_section_exam_status = ClassRegistry::init('Student')->get_student_section($student_id, null, null);

				if (isset($student_section_exam_status['Section'])) {
					if (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
						$academicYR = $student_section_exam_status['Section']['academicyear'];
					}
				}

				if (isset($student_section_exam_status['StudentExamStatus']) && !empty($student_section_exam_status['StudentExamStatus']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
					
					$isTheStudentDismissed = 1;

					$possibleReadmissionYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange($student_section_exam_status['StudentExamStatus']['academic_year'], $this->AcademicYear->current_academicyear());

					$readmitted = ClassRegistry::init('Readmission')->find('first', array(
						'conditions' => array(
							'Readmission.student_id' => $student_id,
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

				$student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult($student_id, $academicYR);

				$studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($student_id);

				$this->set(compact('studentAttendedSections', 'student_academic_profile', 'student_section_exam_status'));
				$this->set('isTheStudentDismissed', $isTheStudentDismissed);
				$this->set('isTheStudentReadmitted', $isTheStudentReadmitted);
				$this->set('academicYR', $academicYR);
			}
		}
	}

	public function profile_not_build_list()
	{
		$limit = 100;
		$name = '';
		$page = '';

		$options = array();
		
		if (!empty($this->passedArgs)) {

			//debug($this->passedArgs);

			if (!empty($this->passedArgs['Search.limit'])) {
				$limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
			}

			if (!empty($this->passedArgs['Search.name'])) {
				$name = str_replace('-', '/', trim($this->passedArgs['Search.name']));
				//$this->request->data['Search']['name'] 
			}

			if (!empty($this->passedArgs['Search.department_id'])) {
				$this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
			}

			if (!empty($this->passedArgs['Search.college_id'])) {
				$this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
			}

			if (isset($this->passedArgs['Search.academicyear'])) {
				$selected_academic_year = $this->request->data['Search']['academicyear']  = str_replace('-', '/', $this->passedArgs['Search.academicyear']);
			} else {
				$selected_academic_year = '';
			}

			if (isset($this->passedArgs['Search.gender'])) {
				$this->request->data['Search']['gender'] = $this->passedArgs['Search.gender'];
			}

			if (isset($this->passedArgs['Search.program_id'])) {
				$this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
			}

			if (isset($this->passedArgs['Search.program_type_id'])) {
				$this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
			}

			if (isset($this->passedArgs['Search.status'])) {
				$this->request->data['Search']['status'] = $this->passedArgs['Search.status'];
			}

			////////////////////

			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$this->request->data['Search']['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$this->request->data['Search']['direction'] = $this->passedArgs['direction'];
			}

			////////////////////

			$this->__init_search_index();

		}

		$this->__init_search_index();

		if (isset($this->request->data['search'])) {
			unset($this->passedArgs);
			$this->__init_clear_session_filters();
			$this->__init_search_index();
		}
		
		if (!empty($this->request->data)) {

			debug($this->request->data);

			if (!empty($page) && !isset($this->request->data['search'])) {
				$this->request->data['Search']['page'] = $page;
			}

			if (!empty($this->request->data['Search']['limit'])) {
				$limit = $this->request->data['Search']['limit'];
			}

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))); 
				$options['conditions'][] = array('Student.department_id' => $this->department_id);
				$this->request->data['Search']['department_id'] = $this->department_id;
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				
				$departments = array();
				
				if (!$this->onlyPre) {
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				}

				if (!empty($this->request->data['Search']['department_id'])) {
					$options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
				} else {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids);
				}

				$this->request->data['Search']['college_id'] = $this->college_id;

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {

				if (!empty($this->department_ids)) {

					$colleges = array();
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));

					if (!empty($this->request->data['Search']['department_id'])) {
						$options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
					} else {
						$options['conditions'][] = array("Student.department_id" => $this->department_ids);
					}

				} else if (!empty($this->college_ids)) {

					$departments = array();
					$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));

					if (!empty($this->request->data['Search']['college_id'])) {
						$options['conditions'][] = array('Student.college_id' => $this->request->data['Search']['college_id'], 'Student.department_id IS NULL');
					} else {
						$options['conditions'][] = array("Student.college_id" => $this->college_ids, 'Student.department_id IS NULL');
					}
					
				}

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
				unset($this->passedArgs);
				unset($this->request->data);
				return $this->redirect(array('action' => 'index'));
			} else {

				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));

				if (!empty($this->request->data['Search']['department_id'])) {
					$options['conditions'][] = array("Student.department_id" => $this->request->data['Search']['department_id']);
				} else if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Search']['college_id'], 'Department.active' => 1)));
					$options['conditions'][] = array('Student.college_id' => $this->request->data['Search']['college_id']);

				} else {

					if (!empty($departments) && !empty($colleges)) {
						$options['conditions'][] = array(
							'OR' => array(
								'Student.college_id' => $this->college_ids,
								'Student.department_id' => $this->department_ids
							)
						);
					} else if (!empty($this->college_ids)) {
						$options['conditions'][] = array('Student.college_id' => $this->college_ids);
					} else if (!empty($this->department_ids)) {
						$options['conditions'][] = array('Student.department_id' => $this->department_ids);
					}
				}
			}

			if (!empty($selected_academic_year)) {
				$options['conditions'][] = array('Student.academicyear' => $selected_academic_year);
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions'][] = array('Student.program_id' => $this->request->data['Search']['program_id']);
			} else if (empty($this->request->data['Search']['program_id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				$options['conditions'][] = array('Student.program_id' => $this->program_ids);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions'][] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
			} else if (empty($this->request->data['Search']['program_type_id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				$options['conditions'][] = array('Student.program_type_id' => $this->program_type_ids);
			}

			if (isset($name) && !empty($name)) {
				$options['conditions'][] = array(
					'OR' => array(
						'Student.first_name LIKE ' => '%' . $name . '%',
						'Student.middle_name LIKE ' =>  '%' . $name . '%',
						'Student.last_name LIKE ' =>  '%' . $name . '%',
						'Student.studentnumber LIKE' =>  $name . '%',
					)
				);
			}

			if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id']) && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR ) {
				$departments = $this->Student->Department->find('list', array(
					'conditions' => array(
						'Department.college_id' => $this->request->data['Search']['college_id'],
						'Department.active' => 1
					)
				));
			}

			if (isset($this->request->data['Search']['gender']) && !empty($this->request->data['Search']['gender'])) {
				$options['conditions'][] = array('Student.gender LIKE ' => $this->request->data['Search']['gender']);
			}

			if (!empty($this->request->data['Search']['status'])) {
				$options['conditions'][] = array('Student.graduated' => $this->request->data['Search']['status']);
			}

		} else {

			$not_build_for = date('Y-m-d ', strtotime("-" . DAYS_BACK_PROFILE . " day "));

			if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
				
				$departments = array();
				
				if (!$this->onlyPre) {
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				}

				if (empty($departments)) {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids);
				} else {
					$options['conditions'][] = array(
						'OR' => array(
							'Student.college_id' => $this->college_ids,
							'Student.department_id' => $this->department_ids
						)
					);
				}

				$this->request->data['Search']['college_id'] = $this->college_id;

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
				$options['conditions'][] = array('Student.department_id' => $this->department_ids);
				
				$this->request->data['Search']['department_id'] = $this->department_id;

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
				
				if (!empty($this->department_ids)) {
					$colleges = array();
					$departments = $this->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
					$options['conditions'][] = array('Student.department_id' => $this->department_ids, 'Student.program_id' => $this->program_ids, 'Student.program_type_id' => $this->program_type_ids);
				} else if (!empty($this->college_ids)) {
					$departments = array();
					$colleges = $this->Student->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
					$options['conditions'][] = array('Student.college_id' => $this->college_ids, 'Student.department_id IS NULL', 'Student.program_id' => $this->program_ids, 'Student.program_type_id' => $this->program_type_ids);
				}

			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
				$options['conditions'][] =  array('Student.id' => $this->student_id);
			} else {

				$departments = $this->Student->Department->find('list', array('conditions' => array('Department.active' => 1)));
				$colleges = $this->Student->College->find('list', array('conditions' => array('College.active' => 1)));

				if (!empty($departments) && !empty($colleges)) {
					$options['conditions'][] = array(
						'OR' => array(
							'Student.department_id' => $this->department_ids,
							'Student.college_id' => $this->college_ids
						)
					);
				} else if (!empty($departments)) {
					$options['conditions'][] = array('Student.department_id' => $this->department_ids);
				} else if (!empty($colleges)) {
					$options['conditions'][] = array('Student.college_id' => $this->college_ids);
				}
			}

			if (!empty($options['conditions'])) {
				$options['conditions'][] = array('Student.id IS NOT NULL');
				$options['conditions'][] = array('Student.graduated = 0');
				$options['conditions'][] = array('Student.created >= ' => $not_build_for);
			}
		}

		//debug($options['conditions']);
		$students = array();

		if (!empty($options['conditions'])) {

			$options['conditions'][] = array('Student.id NOT IN (SELECT student_id FROM contacts)');
			
			$this->Paginator->settings =  array(
				'conditions' => $options['conditions'],
				'contain' => array(
					'Department' => array(
						'fields' => array(
							'Department.id', 
							'Department.name', 
							'Department.shortname', 
							'Department.college_id',
							'Department.institution_code'
						)
					),
					'College' => array(
						'fields' => array(
							'College.id', 
							'College.name', 
							'College.shortname',
							'College.institution_code', 
							'College.campus_id',
						),
						'Campus' => array(
							'id', 
							'name',
							'campus_code'
						)
					),
					'Program' => array(
						'fields' => array(
							'Program.id', 
							'Program.name',
							'Program.shortname',
						)
					),
					'AcceptedStudent' => array(
						'fields' => array(
							'AcceptedStudent.id'
						)
					),
					'ProgramType' => array(
						'fields' => array(
							'ProgramType.id', 
							'ProgramType.name',
							'ProgramType.shortname',
						)
					),
					'Contact', 
					'Curriculum' => array('id', 'name', 'year_introduced','type_credit', 'english_degree_nomenclature', 'active'),
					'Specialization' => array('id', 'name'),
					'Region' => array('id', 'name', 'short'),
					'Zone' => array('id', 'name', 'short'),
					'Woreda' => array('id', 'name', 'code'),
					'City' => array('id', 'name', 'short'),
				), 
				'order' => array('Student.admissionyear' => 'DESC', 'Student.department_id'  => 'ASC', 'Student.program_type_id'  => 'ASC', 'Student.studentnumber' => 'ASC', 'Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC', 'Student.created' => 'DESC'),
				'limit' => $limit,
				'maxLimit' => $limit,
				'recursive'=> -1,
				'page' => $page
			);
		

			try {
				$students = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('students'));
			} catch (NotFoundException $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['Student'])) {
					unset($this->request->data['Student']['page']);
					unset($this->request->data['Student']['sort']);
					unset($this->request->data['Student']['direction']);
				}
				unset($this->passedArgs);
				$this->__init_search_index();
				return $this->redirect(array('action' => 'profile_not_build_list'));
			} catch (Exception $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['Student'])) {
					unset($this->request->data['Student']['page']);
					unset($this->request->data['Student']['sort']);
					unset($this->request->data['Student']['direction']);
				}
				unset($this->passedArgs);
				$this->__init_search_index();
				return $this->redirect(array('action' => 'profile_not_build_list'));
			}

			if (!empty($students)) {
				if ($this->Session->check('students')) {
					$this->Session->delete('students');
				}
				$this->Session->write('students', $students);
			}
		}

		if (empty($students) && !empty($options['conditions'])) {
			$this->Flash->info('No Student is found with the given search criteria.');
			$turn_off_search = false;
		} else {
			$turn_off_search = false;
			//debug($students[0]);
		}

		$this->set(compact('colleges', 'departments', /* 'students', */ 'turn_off_search', 'limit', 'name'));
	}

	/* function profile_not_build_list()
	{
		$student_lists = array();

		if ($this->department_ids){
			$student_lists = $this->Student->getProfileNotBuildList(DAYS_BACK_PROFILE, $this->department_ids, null);
		} else if ($this->college_ids){
			$student_lists = $this->Student->getProfileNotBuildList(DAYS_BACK_PROFILE, null, $this->college_ids);
		}

		$this->set(compact('student_lists'));
	} */

	function name_change($id = null)
	{
		if (!empty($this->request->data['Student']) && isset($this->request->data['searchStudentName'])) {

			$student_id = null;
			$everythingfine = true;

			if (empty($this->request->data['Student'])) {
				$this->Flash->error( __('Please provide the student number (ID) you want to change name.'));
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
					$check_id_is_valid = $this->Student->find('count', array(
						'conditions' => array(
							'Student.studentnumber LIKE ' => trim($this->request->data['Student']['studentnumber']) . '%',
							'Student.department_id' => $department_id
						)
					));
				} else if (!empty($college_id)) {
					$check_id_is_valid = $this->Student->find('count', array(
						'conditions' => array(
							'Student.studentnumber LIKE ' => trim($this->request->data['Student']['studentnumber']) . '%', 
							'Student.college_id' => $college_id, 
							'Student.department_id is null'
						)
					));
				}

				if ($check_id_is_valid > 0) {
					// do something if needed
					$everythingfine = true;
					$student_id = $this->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber LIKE ' => trim($this->request->data['Student']['studentnumber']) . '%',
							'Student.department_id' => $department_id
						), 
						'recursive' => -1
					));
				} else {
					$everythingfine = false;
					$this->Flash->error(__('The provided student number is not valid or you don\'t have the privilage to change name to this student.'));
				}
			}

			if ($everythingfine) {
				$test_data = $this->Student->find('first', array('conditions' => array('Student.id' => $student_id['Student']['id']), 'recursive' => -1));
				$this->request->data = $this->Student->StudentNameHistory->reformat($test_data);
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['changeName'])) {

			$data = $this->Student->StudentNameHistory->reformat($this->request->data);

			$isThereChangeInFullName = true;

			if ($data['StudentNameHistory']['to_first_name'] === $data['StudentNameHistory']['from_first_name'] && $data['StudentNameHistory']['to_middle_name'] === $data['StudentNameHistory']['from_middle_name'] && $data['StudentNameHistory']['to_last_name'] === $data['StudentNameHistory']['from_last_name']) {
				$isThereChangeInFullName = false;
			}

			if ($isThereChangeInFullName) {
				if ($this->Student->StudentNameHistory->save($data)) {
					$change['Student']['amharic_first_name'] = $data['StudentNameHistory']['to_amharic_first_name'];
					$change['Student']['id'] = $data['StudentNameHistory']['student_id'];
					$change['Student']['amharic_middle_name'] = $data['StudentNameHistory']['to_amharic_middle_name'];
					$change['Student']['amharic_last_name'] = $data['StudentNameHistory']['to_amharic_last_name'];

					$change['Student']['first_name'] = $data['StudentNameHistory']['to_first_name'];
					$change['Student']['middle_name'] = $data['StudentNameHistory']['to_middle_name'];
					$change['Student']['last_name'] = $data['StudentNameHistory']['to_last_name'];

					if ($this->Student->save($change)) {
						$this->Flash->success( __('Student name change name has been saved.'));
						//save the changed name in student table
						$this->redirect($this->referer());
					} else {
						$this->Flash->error(__('Student name change could not be saved.  Please, try again.'));
						$this->Student->StudentNameHistory->delete($this->Student->StudentNameHistory->id);
					}
				} else {
					//debug($this->Student->StudentNameHistory->invalidFields());
					$this->Flash->error( __('Student name change could not be saved. Please, try again.'));
					$this->redirect($this->referer());
				}
			} else {
				$this->Flash->info(__('No change detected in previous and new student name. Nothing updated.'));
				$this->redirect($this->referer());
			}
		}

		if (empty($this->request->data) && !empty($id)) {
			$test_data = $this->Student->find('first', array('conditions' => array('Student.id' => $id), 'contain' => array()));
			$this->request->data = $this->Student->StudentNameHistory->reformat($test_data);
		}
	}

	function department_issue_password($section_id = null)
	{
		$this->__issue_password($section_id, 0);
	}

	function freshman_issue_password($section_id = null)
	{
		$this->__issue_password($section_id, 1);
	}

	private function __issue_password($section_id = null, $freshman_program = 0)
	{
		/*
			1. Retrieve list of sections based on the given search criteria
			2. Display list of sections
			3. Up on the selection of section, display list of students with check-box
			4. Prepare student password issue/reset in PDF for the selected students
		*/

		$programs = $this->Student->Section->Program->find('list');
		$program_types = $this->Student->Section->ProgramType->find('list');

		if ($freshman_program == 0) {
			$yearLevels = $this->Student->Section->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
		} else {
			$yearLevels[0] = "Pre/Freshman";
		}

		$reset_password_by_email = (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 1  ? 1 : 0);

		$section_ac_years = $this->AcademicYear->academicYearInArray((explode('/', $this->AcademicYear->current_academicyear())[0] - ACY_BACK_FOR_SECTION_ADD) , explode('/', $this->AcademicYear->current_academicyear())[0]);
		debug($section_ac_years);

		$departments[0] = 0;
		//Get sections button is clicked
		if (isset($this->request->data['listSections'])) {

			$this->__init_search_student();

			$options = array();
			
			$options = array(
				'conditions' => array(
					'Section.archive' => 0,
					'Section.program_id' => $this->request->data['Student']['program_id'],
					'Section.program_type_id' => $this->request->data['Student']['program_type_id']
				),
				'order' => array('Section.academicyear' => 'DESC', 'Section.year_level_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC'),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				$options['conditions'][] = array(
					'Section.college_id' => $this->college_id,
					'Section.archive' => 0,
					'Section.department_id IS NULL',
					'Section.academicyear' => $this->request->data['Student']['acadamic_year']
					//'Section.year_level_id IS NULL or Section.year_level_id="" or Section.year_level_id = 0'
				);
			} else {
				$options['conditions'][] = array(
					'Section.department_id' => $this->department_id,
					'Section.year_level_id' => $this->request->data['Student']['year_level_id'],
					'Section.academicyear' => $section_ac_years
				);
			}

			//$sections = $this->Student->Section->find('list', $options);

			$options['contain'] = array('YearLevel', 'Program', 'ProgramType');

			$sections_detail_all = $this->Student->Section->find('all', $options);

			if (!empty($sections_detail_all)) {
				foreach ($sections_detail_all as $seindex => $secvalue) {
					$sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name']) ? $secvalue['YearLevel']['name'] : ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue['Section']['academicyear'] . ')';
				}
			}

			if ($freshman_program == 1 && !empty($sections)) {
				$sections['pre'] = "All";
				asort($sections);
			}

			if (empty($sections)) {
				$this->Flash->info(__('No section is found with the given search criteria.'));
			} else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			}

			$year_level_selected = $this->request->data['Student']['year_level_id'];
			$program_id = $this->request->data['Student']['program_id'];
			$program_type_id = $this->request->data['Student']['program_type_id'];

		}

		//Section is selected from the combo box
		if (isset($this->request->data['issueStudentPassword']) || (!empty($section_id) && ($section_id != 0 || strcasecmp($section_id, "pre") == 0))) {
			
			$this->__init_search_student();
			
			if (isset($this->request->data['issueStudentPassword'])) {
				$section_id = $this->request->data['Student']['section_id'];
			}

			if (!empty($section_id) && $section_id != "pre" && $section_id > 0) {

				$section_detail = $this->Student->Section->find('first', array(
					'conditions' => array(
						'Section.id' => $section_id
					),
					'contain' => array(
						'Program' => array('id', 'name'),
						'ProgramType' => array('id', 'name'),
						'YearLevel' => array('id', 'name'),
					),
					'recursive' => -1
				));

				if (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 'AUTO') {
					$general_settings = ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(null, null, null, $section_id);
		
					if (!empty($general_settings['GeneralSetting'])) {
						//debug($general_settings['GeneralSetting']['allowStudentsToResetPasswordByEmail']);
						$reset_password_by_email = $general_settings['GeneralSetting']['allowStudentsToResetPasswordByEmail'];
					}
				}

				$year_level_selected = $section_detail['Section']['year_level_id'];
				$program_id = $section_detail['Section']['program_id'];
				$program_type_id = $section_detail['Section']['program_type_id'];
			}

			//Student list retrial
			if (strcasecmp($section_id, "pre") == 0) {
				$students_in_section = $this->Student->listStudentByAdmissionYear(
					null,
					$this->college_id,
					$this->request->data['Student']['acadamic_year'],
					$this->request->data['Student']['name'],
					0
				);

				$this->request->data['Student']['section_id'] = 'pre';

			} else {
				$students_in_section = $this->Student->Section->getSectionStudents($section_id, $this->request->data['Student']['name']);

				$this->request->data['Student']['section_id'] = $section_id;
			}

			$options = array();

			$options = array(
				'conditions' => array(
					'Section.archive' => 0,
					'Section.program_id' => $this->request->data['Student']['program_id'],
					'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
				),
				'order' => array('Section.academicyear' => 'DESC', 'Section.year_level_id' => 'ASC', 'Section.id' => 'ASC', 'Section.name' => 'ASC'),
				'recursive' => -1
			);

			if ($freshman_program == 1) {
				$options['conditions'][] = array(
					'Section.college_id' => $this->college_id,
					'Section.archive' => 0,
					'Section.department_id IS NULL',
					'Section.academicyear' => $this->request->data['Student']['acadamic_year'],
					//'Section.year_level_id IS NULL'
				);
			} else {
				$options['conditions'][] = array(
					'Section.department_id' => $this->department_id,
					'Section.year_level_id' => $this->request->data['Student']['year_level_id'],
					'Section.academicyear' => $section_ac_years
				);
			}

			//$sections = $this->Student->Section->find('list', $options);

			$options['contain'] = array('YearLevel', 'Program', 'ProgramType');
			
			$sections_detail_all = $this->Student->Section->find('all', $options);

			if (!empty($sections_detail_all)) {
				foreach ($sections_detail_all as $seindex => $secvalue) {
					$sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name']) ? $secvalue['YearLevel']['name'] : ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue['Section']['academicyear'] . ')';
				}
			}

			//Give an option to get all freshman studnet of the college
			if ($freshman_program == 1 && !empty($sections)) {
				$sections['pre'] = "All"; // most if the time, it doesn't work when the student number is too large, better to turn it off, reset by section is enough. // Added Common Password Option, It will remove this short coming, Neway
				asort($sections);
			}

			if (empty($sections)) {
				$this->Flash->info( __('There is no section with the selected search criteria.'));
			} else {
				$sections = array('0' => '[ Select Section ]') + $sections;
			}
		}

		//Issue Student Password button is clicked
		if (isset($this->request->data['issueStudentPassword'])) {
			$student_ids = array();

			if (!empty($this->request->data['Student'])) {
				foreach ($this->request->data['Student'] as $key => $student) {
					if (is_numeric($key) && !empty($student['student_id'])) {
						if (isset($student['gp']) && ($student['gp'] == 1 || $student['gp'] == '1')) {
							$student_detail['student_id'] = $student['student_id'];
							//$student_detail['flat_password'] = $this->_generatePassword(5);
							
							if (empty($this->request->data['Student']['common_password']) || (isset($this->request->data['Student']['common_password']) && strlen($this->request->data['Student']['common_password']) < 5)) {
								$student_detail['flat_password'] = $this->_generatePassword(5);
							} else {
								$student_detail['flat_password'] = $this->request->data['Student']['common_password'];
							}

							$student_detail['hashed_password'] = Security::hash(trim($student_detail['flat_password']), null, true);
							$student_ids[] =  $student_detail;
						}
					}
				}
			}

			if (empty($student_ids)) {
				$this->Flash->error(__('You are required to select at least one student.'));
			} else {

				//debug($student_ids[0]);
				//debug($student_ids);

				$student_passwords = $this->Student->getStudentPassword($student_ids);

				if (empty($student_passwords)) {
					$this->Flash->info('<span></span>' .__('ERROR: Unable to issue password for the selected students. Please try again.'));
				} else {

					$this->set(compact('student_passwords'));

					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';

					if (isset($section_detail)) {
						$section_for_file_name = $section_detail['Section']['name'] . '_' . (isset($section_detail['YearLevel']['name']) ?  $section_detail['YearLevel']['name'] : '') . '_' . (str_replace('/', '_', $section_detail['Section']['academicyear'])) .'_'. $section_detail['Program']['name'] .'_'. $section_detail['ProgramType']['name'];
					} else {
						$section_for_file_name = 'All_'. ($this->request->data['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial_Sections_' : 'Pre_Freshman_Sections_') . '_' . (str_replace('/', '_', $this->request->data['Student']['acadamic_year']));
					}

					$this->set(compact('section_for_file_name'));

					if ($this->request->data['Student']['single_page'] == "yes") {
						$this->render('mass_password_issue_single_page_pdf');
					} else {
						$this->set(compact('reset_password_by_email'));
						$this->render('issue_password_pdf');
					}

					return;
				}
			}
		}

		$this->set(compact('programs', 'program_types', 'departments', 'yearLevels', 'year_level_selected', 'semester_selected', 'program_id', 'program_type_id', 'section_id', 'sections', 'students_in_section'));

		$this->render('issue_password_list');
	}

	function _generatePassword($length = '')
	{
		$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$max = strlen($str);
		$length = @round($length);

		if (empty($length)) {
			$length = rand(8, 12);
		}

		$password = '';

		for ($i = 0; $i < $length; $i++) {
			$password .= $str{rand(0, $max - 1)};
		}

		return $password;
	}

	function auto_yearlevel_update()
	{
		$studentssections = $this->Student->find('all', array(
			'conditions' => array(
				//'Student.id NOT IN (select student_id from graduate_lists)'
				'Student.graduated' => 0
			), 
			'contain' => array(
				'CourseRegistration' => array(
					//'order' => array('CourseRegistration.created DESC'), // Backdated grade entry affects created field and it will not be accurate
					'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC'), 
					'limit' => 1
				)
			), 
			'fields' => array('Student.id', 'Student.studentnumber', 'Student.full_name', 'Student.department_id', 'Student.program_id')
		));

		$count = 0;
		$studentList = array();

		if (!empty($studentssections) && count($studentssections) > 0) {
			foreach ($studentssections as $key => $student) {

				$studentList['Student'][$count]['id'] = $student['Student']['id'];
				
				if (is_null($student['Student']['department_id']) && (empty($student['CourseRegistration'][0]['year_level_id']) || empty($student['CourseRegistration']))) {
					$studentList['Student'][$count]['yearLevel'] = 'Pre/1st';
				} else if (empty($student['CourseRegistration']) || empty($student['CourseRegistration'][0]['year_level_id'])) {
					$studentList['Student'][$count]['yearLevel'] = '1st';
				} else {
					// find the year level
					$yearLevel = ClassRegistry::init('YearLevel')->field('YearLevel.name', array('YearLevel.id' => $student['CourseRegistration'][0]['year_level_id']));
					
					if (!empty($yearLevel)) {
						$studentList['Student'][$count]['yearLevel'] = $yearLevel;
					}

					debug($yearLevel);
				}

				$count++;
			}
		}

		if (!empty($studentList['Student'])) {
			//saveAll
			if ($this->Student->saveAll($studentList['Student'], array('validate' => false))) {
			}
		}
	}

	public function name_list()
	{
		
		$this->paginate = array('contain' => array('Department', 'Curriculum', 'ProgramType', 'Program', 'College'));

		if ((isset($this->request->data['Student']) && isset($this->request->data['viewPDF']))) {
			$search_session = $this->Session->read('search_data');
			debug($search_session);
			$this->request->data['Student'] = $search_session;
		}

		if (isset($this->passedArgs)) {
			if (isset($this->passedArgs['page'])) {
				$this->__init_search_name();
				$this->request->data['Student']['page'] = $this->passedArgs['page'];
				$this->__init_search_name();
			}
		}

		if ((isset($this->request->data['Student']) && isset($this->request->data['listStudentsForNameChange']))) {
			$this->__init_search_name();
		}

		// filter by department or college
		if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
			
			$department_id = $this->request->data['Student']['department_id'];
			$college_id = explode('~', $department_id);
			
			if (count($college_id) > 1) {
				$this->paginate['conditions'][]['Student.college_id'] = $college_id[1];
			} else {
				$this->paginate['conditions'][]['Student.department_id'] = $department_id;
			}
		}

		if (isset($this->request->data['Student']['program_id']) && !empty($this->request->data['Student']['program_id'])) {
			$this->paginate['conditions'][]['Student.program_id'] = $this->request->data['Student']['program_id'];
		}

		if (isset($this->request->data['Student']['program_type_id']) && !empty($this->request->data['Student']['program_type_id'])) {
			$this->paginate['conditions'][]['Student.program_type_id'] = $this->request->data['Student']['program_type_id'];
		}

		if (isset($this->request->data['Student']['studentnumber']) && !empty($this->request->data['Student']['studentnumber'])) {
			unset($this->paginate);
			$this->paginate['conditions'][]['Student.studentnumber'] = $this->request->data['Student']['studentnumber'];
		}

		if (isset($this->request->data['Student']['admission_year']) && !empty($this->request->data['Student']['admission_year'])) {
			debug($this->request->data['Student']['admission_year']);
			$this->paginate['conditions'][]['Student.admissionyear'] = $this->AcademicYear->getAcademicYearBegainingDate($this->request->data['Student']['admission_year'], 'I');
		}

		if (isset($this->request->data['Student']['name']) && !empty($this->request->data['Student']['name'])) {
			unset($this->paginate);
			$this->paginate['conditions'][]['Student.first_name LIKE '] = trim($this->request->data['Student']['name']) . '%';
		}

		if (isset($this->request->data['Student']['page']) && !empty($this->request->data['Student']['page'])) {
			$this->paginate['page'] = $this->request->data['Student']['page'];
		}

		$this->Paginator->settings = $this->paginate;

		if (isset($this->request->data) && !empty($this->Paginator->settings['conditions'])) {
			$students_for_name_list = $senateLists = $this->Paginator->paginate('Student');
		} else {
			$students_for_name_list = array();
		}

		if (empty($students_for_name_list) && isset($this->request->data) && !empty($this->request->data)) {
			$this->Flash->info( __('There is no student in the system based with the given criteria.'));
		}

		//debug($students_for_name_list);

		$programs = $this->Student->Program->find('list');
		$program_types = $this->Student->ProgramType->find('list');
		$departments = $this->Student->Department->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);

		$programs = array(0 => 'All Programs') + $programs;
		$program_types = array(0 => 'All Program Types') + $program_types;
		$departments = array(0 => 'All University Students') + $departments;

		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;

		if ((isset($this->request->data['Student']) && isset($this->request->data['viewPDF']))) {
			
			debug($students_for_name_list);

			if (!empty($students_for_name_list)) {

				foreach ($students_for_name_list as $k => $v) {
					$g_d_obj = new DateTime($v['Student']['admissionyear']);
					$admission_year = explode('-', $v['Student']['admissionyear']);
					$e_g_year = $this->EthiopicDateTime->GetEthiopicYear($g_d_obj->format('j'), $g_d_obj->format('n'), $g_d_obj->format('Y'));
					$g_academic_year = $this->AcademicYear->get_academicyear($admission_year[1], $admission_year[0]);
					$students_for_name_list_pdf[$v['Department']['name'] . '~' . $v['Program']['name'] . '~' . $v['ProgramType']['name'] . '~' . $g_academic_year . '(' . $e_g_year . 'E.C)'][] = $v;
				}

				$this->set(compact('students_for_name_list_pdf', 'defaultacademicyear'));
				$this->response->type('application/pdf');
				$this->layout = 'pdf';
				$this->render('name_list_pdf');

			} else {
				$this->Flash->info( __('EMPTY DATA: Unable to generate PDF.'));
			}
		}

		$this->set(compact('programs', 'program_types', 'departments', 'students_for_name_list', 'default_department_id', 'default_program_id', 'default_program_type_id', 'senateLists'));
	}

	function __init_search_name()
	{
		// We create a search_data session variable when we fill any criteria  in the search form.
		if (!empty($this->request->data['Student'])) {
			$this->Session->write('search_data', $this->request->data['Student']);
		} else if ($this->Session->check('search_data')) {
			$this->request->data['Student'] = $this->Session->read('search_data');
		}
	}

	function correct_name($id)
	{

		if (!$id) {
			$this->Flash->error(__('Invalid ID'));
			$this->redirect($this->referer());
		}

		$check_elegibility_to_edit = 0;

		if (!empty($this->college_ids)) {
			$check_elegibility_to_edit = $this->Student->find('count', array(
				'conditions' => array(
					'Student.college_id' => $this->college_ids,
					'Student.id' => $id,
					'Student.program_id' => $this->program_ids,
					'Student.program_type_id' => $this->program_type_ids,
				)
			));
		} else if ($this->department_ids) {
			$check_elegibility_to_edit = $this->Student->find('count', array(
				'conditions' => array(
					'Student.department_id' => $this->department_ids,
					'Student.id' => $id,
					'Student.program_id' => $this->program_ids,
					'Student.program_type_id' => $this->program_type_ids,
				)
			));
		}


		if ($check_elegibility_to_edit == 0) {
			$this->Flash->error(__('You are not elgibile to correct the student name. This happens when you are trying to edit students name which you are not assigned to edit.'));
			//$this->redirect(array('action' => 'name_list'));
		}

		if (!empty($this->request->data) && $this->request->data['correctName']) {

			if ($this->Student->save($this->request->data)) {
				$this->Flash->success(__('The student name has been updated.'));
				$this->redirect($this->referer());
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error( __('The student name could not be saved. Please check other required fields are updated in studnet profile and try again.'));
			}
			
			$this->redirect($this->referer());
		}

		$studentDetail = $this->Student->find('first', array('conditions' => array('Student.id' => $id), /* 'contain' => array('StudentNameHistory'), */ 'recursive' => -1));

		//debug($studentDetail);

		if (empty($this->request->data)) {
			$this->request->data = $this->Student->find('first', array('conditions' => array('Student.id' => $id), /* 'contain' => array('StudentNameHistory'), */ 'recursive' => -1)); //$this->Student->read(null, $id);
		}

		$this->set(compact('studentDetail'));
	}

	function __auto_registration_update($publishedcourse_id)
	{
		$latest_academic_year = $this->AcademicYear->current_academicyear();

		$publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find('first', array('conditions' => array('PublishedCourse.id' => $publishedcourse_id), 'recursive' => -1));

		$studentssections = ClassRegistry::init('StudentsSection')->find('all', array('conditions' => array('StudentsSection.section_id' => $publishedCourseDetail['PublishedCourse']['section_id']), 'recursive' => -1));
		
		$count = 0;
		$studentList = array();

		if (!empty($studentssections) && count($studentssections) > 0) {
			foreach ($studentssections as $k => $v) {
				// registered
				$registered = ClassRegistry::init('CourseRegistration')->find('first', array('conditions' => array('CourseRegistration.published_course_id' => $publishedcourse_id, 'CourseRegistration.student_id' => $v['StudentsSection']['student_id']), 'recursive' => -1));
				
				//print_r($registered);
				if (empty($registered)) {
					// does that student dismissed ?
					$passed_or_failed = $this->Student->StudentExamStatus->getStudentLastExamStatus($v['StudentsSection']['student_id'], $latest_academic_year);

					if ($passed_or_failed == 1 || $passed_or_failed == 3) {

						$studentList['CourseRegistration'][$count]['year_level_id'] = $publishedCourseDetail['PublishedCourse']['year_level_id'];
						$studentList['CourseRegistration'][$count]['section_id'] = $publishedCourseDetail['PublishedCourse']['section_id'];
						$studentList['CourseRegistration'][$count]['semester'] = $publishedCourseDetail['PublishedCourse']['semester'];
						$studentList['CourseRegistration'][$count]['academic_year'] = $publishedCourseDetail['PublishedCourse']['academic_year'];
						$studentList['CourseRegistration'][$count]['student_id'] = $v['StudentsSection']['student_id'];
						$studentList['CourseRegistration'][$count]['published_course_id'] = $publishedCourseDetail['PublishedCourse']['id'];

						$studentList['CourseRegistration'][$count]['created'] = $publishedCourseDetail['PublishedCourse']['created'];
						$studentList['CourseRegistration'][$count]['modified'] = $publishedCourseDetail['PublishedCourse']['modified'];

						$count++;
					}
				}

				//$count++;
				//print_r($count);
			}
		}

		if (!empty($studentList['CourseRegistration'])) {
			//saveAll
			if (ClassRegistry::init('CourseRegistration')->saveAll($studentList['CourseRegistration'], array('validate' => false))) {
			}
		}
	}

	public function scan_profile_picture()
	{
		debug($this->request->data);
		if (isset($this->request->data['Synchronize']) && !empty($this->request->data['Synchronize'])) {

			/* $allImages = glob(WWW_ROOT . "media/transfer/img/*.jpg");
			debug($allImages);
			foreach ($allImages as $image) {
				debug($image);
				$imageFileName = explode(WWW_ROOT . 'media/transfer/img/', $image);

				$studentnumberWithImage = str_replace('-', '/', $imageFileName[1]);
				$studentnumber = explode('.jpg', $studentnumberWithImage);
				debug($studentnumber);
			} */

			$path = WWW_ROOT . "media/transfer/img/";
			$allImages = $this->__getNewestFN($path);
			$count = 0;

			if (!empty($allImages)) {
				foreach ($allImages as $image) {
					//check if student is there
					$attachmentModel = array();

					$imageFileName = explode(WWW_ROOT . 'media/transfer/img/', $image);

					$studentnumberWithImage = str_replace('-', '/', $imageFileName[1]);
					$studentnumber = explode('.jpg', $studentnumberWithImage);

					$student_number_exist = $this->Student->find('first', array('conditions' => array('Student.studentnumber' => $studentnumber[0])));
					$filename = $imageFileName[1];

					if (!empty($student_number_exist)) {

						$isUploadedAlready = ClassRegistry::init('Photo')->find('first', array(
							'conditions' => array(
								'Photo.model' => 'Student', 
								'Photo.foreign_key' => $student_number_exist['Student']['id'],
								'Photo.group' => 'profile'
							)
						));

						if (!empty($isUploadedAlready)) {
							$attachmentModel['Photo']['id'] = $isUploadedAlready['Photo']['id'];
						}

						$attachmentModel['Photo']['model'] = 'Student';
						$attachmentModel['Photo']['foreign_key'] = $student_number_exist['Student']['id'];
						$attachmentModel['Photo']['dirname'] = 'img';
						$attachmentModel['Photo']['basename'] = $filename;
						$attachmentModel['Photo']['checksum'] = md5($filename);
						$attachmentModel['Photo']['group'] = 'profile';

						if (!empty($attachmentModel['Photo'])) {

							if (empty($attachmentModel['Photo']['id'])) {
								ClassRegistry::init('Photo')->create();
							}

							if (ClassRegistry::init('Photo')->save($attachmentModel)) {
								$count++;
							}
						}
					}
				}
			}

			if ($count) {
				$this->Flash->success(__('The dropped profile pictures of students has been completed by synchronizing ' . $count . ' file(s).'));
			}
		}
	}

	private function __getNewestFN($path)
	{
		// store all .inf names in array

		$files = glob($path . '*.{jpg}', GLOB_BRACE);
		usort($files, array($this, "_filemtime_compare"));

		return $files;

		/* $p = opendir($path);

		while (false !== ($file = readdir($p))) {
			$parts = pathinfo($file);
			debug($parts);
			if (strstr($file, ".inf") && $parts['extension'] == 'jpg') {
				$list[] = date("YmdHis ", filemtime($path . $file)) . $path . $file;
			}
		}
		// sort array descending
		rsort($list);
		// return newest file name
		return $list[0]; */
		
	}
	
	private function _filemtime_compare($a, $b)
	{
		return filemtime($a) - filemtime($b);
	}

	public function mass_import_profile_picture()
	{

		if (!empty($this->request->data)) {

			//check the file type before doing the fucken manipulations.
			if (strcasecmp($this->request->data['Student']['xls']['type'], 'application/vnd.ms-excel')) {
				$this->Flash->error( __('Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type and import again. Current file format is: ' . $this->request->data['Student']['xls']['type']));
				return;
			}

			$data = new Spreadsheet_Excel_Reader();
			// Set output Encoding.
			$data->setOutputEncoding('CP1251');
			$data->read($this->request->data['Student']['xls']['tmp_name']);
			$headings = array();
			$xls_data = array();
			$non_existing_field = array();
			$required_fields = array('studentnumber', 'photonumber');

			if (empty($data->sheets[0]['cells'])) {
				$this->Flash->error( __('Importing Error. The excel file you uploaded is empty.'));
				return;
			}

			if (empty($data->sheets[0]['cells'][1])) {
				$this->Flash->error(__('Importing Error. Please insert your filed name (studentnumber,photonumber)  at first row of your excel file.'));
				return;
			}

			for ($k = 0; $k < count($required_fields); $k++) {
				if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === FALSE) {
					$non_existing_field[] = $required_fields[$k];
				}
			}

			if (count($non_existing_field) > 0) {
				$field_list = "";
				foreach ($non_existing_field as $k => $v) {
					$field_list .= ($v . ", ");
				}
				$field_list = substr($field_list, 0, (strlen($field_list) - 2));
				$this->Flash->error(__('Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.'));
				return;
			} else {

				$fields_name_import_table = $data->sheets[0]['cells'][1];
				$formatUploadedPicsPath = array();
				$uploadMaps = array();

				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
					$row_data = array();
					for ($j = 1; $j <= count($fields_name_import_table); $j++) {

						if ($fields_name_import_table[$j] == "studentnumber" && $data->sheets[0]['cells'][$i][$j] == "") {
							$non_valide_rows[] = "Please enter a valid student number on row number " . $i;
							continue;
						} else {

							if ($fields_name_import_table[$j] == "studentnumber") {
								$row_data['studentnumber'] = $data->sheets[0]['cells'][$i][$j];
							}

							if ($fields_name_import_table[$j] == "photonumber") {
								$row_data['photonumber'] = $data->sheets[0]['cells'][$i][$j];
							}
						}
					}
					$uploadMaps[$row_data['studentnumber']] = $row_data['photonumber'];
				}

				$invalidStudentIds = array();
				$validStudentIds = array();

				if (!empty($uploadMaps)) {

					$rowCount = 1;
					$attachmentModel = array();
					foreach ($uploadMaps as $kk => $vv) {
						//check if the student id exists

						$student_number_exist = $this->Student->find('first', array('conditions' => array('Student.studentnumber' => $kk), 'recursive' => -1));
						debug($student_number_exist);
						

						if ($student_number_exist) {
							$uploadAndSavePicture = array();
							foreach ($this->request->data['Student']['File'] as $fk => $fv) {
								$attachmentModel = array();
								if (stristr($fv['name'], $vv) !== FALSE) { 

									$ext = substr(strtolower(strrchr($fv['name'], '.')), 1); //get the extension

									$filenameNew = str_replace('/', '-', $kk) . '.'. $ext;

									$arr_ext = array('jpg', 'jpeg', 'png'); //set allowed extensions

									//only process if the extension is valid
									if (in_array($ext, $arr_ext)) {

										if (move_uploaded_file($fv['tmp_name'], WWW_ROOT . "/media/transfer/img/" . $filenameNew)) {

											$attachment = ClassRegistry::init('Photo')->find('first', array(
												'conditions' => array(
													'foreign_key' => $student_number_exist['Student']['id'], 
													'model' => "Student"
												), 
												'fields' => array('id', 'model', 'dirname', 'basename', 'checksum', 'group'),
												'recursive' => -1, 
											));

											if (!empty($attachment)) {
												$attachmentModel['Photo']['id'] = $attachment['Photo']['id'];
											}

											// do size validation and extension in here

											$attachmentModel['Photo']['model'] = 'Student';
											$attachmentModel['Photo']['foreign_key'] = $student_number_exist['Student']['id'];
											$attachmentModel['Photo']['dirname'] = 'img';
											$attachmentModel['Photo']['basename'] = $filenameNew;
											$attachmentModel['Photo']['checksum'] = md5($filenameNew);
											$attachmentModel['Photo']['group'] = 'profile';

											if (!empty($attachmentModel['Photo'])) {
												if (empty($attachmentModel['Photo']['id'])) {
													ClassRegistry::init('Photo')->create();
												}
												if (ClassRegistry::init('Photo')->save($attachmentModel)) {
													$validStudentIds[$kk] = $rowCount;
												}
											}
										}
									}
								}
							}
						} else {
							$invalidStudentIds[$kk] = $rowCount;
						}

						$rowCount++;
					}
				}

				if (!empty($validStudentIds)) {
					debug($invalidStudentIds);
					$this->Flash->success('Uploaded ' . count($validStudentIds) . ' profile pictures.');
				}
			}
		}

		$profilePictureUploaded = ClassRegistry::init('Attachment')->find('count', array(
			'conditions' => array(
				'group' => 'profile', 
				'model' => "Student",
				'foreign_key in (select id from students )'
			), 
			'recursive' => -1
		));

		$totalStudentCount = $this->Student->find('count', array('conditions' => array('Student.graduated = 0'), 'recursive' => -1));

		$this->set(compact('profilePictureUploaded', 'totalStudentCount'));
	}

	public function mass_import_student_national_id()
	{

		if (!empty($this->request->data)) {

			debug($this->request->data);

			//check the file type before doing the fucken manipulations.
			if (strcasecmp($this->request->data['Student']['xls']['type'], 'application/vnd.ms-excel')) {
				$this->Flash->error( __('Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type and import again. Current file format is: ' . $this->request->data['Student']['xls']['type']));
				return;
			}

			$data = new Spreadsheet_Excel_Reader();
			// Set output Encoding.
			$data->setOutputEncoding('CP1251');
			$data->read($this->request->data['Student']['xls']['tmp_name']);
			$headings = array();
			$xls_data = array();
			$non_existing_field = array();
			$required_fields = array('studentnumber', 'student_national_id');

			if (empty($data->sheets[0]['cells'])) {
				$this->Flash->error( __('Importing Error. The excel file you uploaded is empty.'));
				return;
			}

			if (empty($data->sheets[0]['cells'][1])) {
				$this->Flash->error(__('Importing Error. Please insert your fieled name (studentnumber,student_national_id) at first row of your excel file.'));
				return;
			}

			for ($k = 0; $k < count($required_fields); $k++) {
				if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === FALSE) {
					$non_existing_field[] = $required_fields[$k];
				}
			}

			if (count($non_existing_field) > 0) {
				$field_list = "";
				foreach ($non_existing_field as $k => $v) {
					$field_list .= ($v . ", ");
				}
				$field_list = substr($field_list, 0, (strlen($field_list) - 2));
				$this->Flash->error(__('Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.'));
				return;
			} else {

				$fields_name_import_table = $data->sheets[0]['cells'][1];
				$uploadMaps = array();

				for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
					$row_data = array();
					for ($j = 1; $j <= count($fields_name_import_table); $j++) {

						if ($fields_name_import_table[$j] == "studentnumber" && trim($data->sheets[0]['cells'][$i][$j]) == "") {
							$non_valide_rows[] = "Please enter a valid student number on row number " . $i;
							continue;
						} else if ($fields_name_import_table[$j] == "student_national_id" && trim($data->sheets[0]['cells'][$i][$j]) == "") {
							$non_valide_rows[] = "Please enter a valid Student National ID at row number " . $i;
							continue;
						} else {

							if ($fields_name_import_table[$j] == "studentnumber") {
								$row_data['studentnumber'] = trim($data->sheets[0]['cells'][$i][$j]);
							}

							if ($fields_name_import_table[$j] == "student_national_id") {
								$row_data['student_national_id'] = trim($data->sheets[0]['cells'][$i][$j]);
							}
						}
					}

					$uploadMaps[$row_data['studentnumber']] = $row_data['student_national_id'];
				}

				$invalidStudentIds = array();
				$errors_to_correct = array();
				$results_to_html_table = array();
				$validStudentIds = array();

				if (!empty($uploadMaps)) {

					$rowCount = 1;

					foreach ($uploadMaps as $kk => $vv) {
						//check if the student id exists

						$student_number_exist = $this->Student->find('first', array('conditions' => array('Student.studentnumber' => $kk), 'fields' => array('id', 'full_name', 'accepted_student_id', 'user_id', 'graduated', 'studentnumber', 'student_national_id'), 'recursive' => -1));
						//debug($student_number_exist);

						$results_to_html_table[$kk]['studentnumber'] = $kk;
						$results_to_html_table[$kk]['student_national_id'] = $vv;

						if (!empty($student_number_exist)) {

							$national_id_exists = $this->Student->find('first', array('conditions' => array('Student.student_national_id' => $vv), 'fields' => array('id', 'full_name', 'accepted_student_id', 'user_id', 'graduated', 'studentnumber', 'student_national_id'), 'recursive' => -1));
							//debug($national_id_exists);
							//debug(strlen($vv) > 7);

							if ((is_null($student_number_exist['Student']['student_national_id']) || empty($student_number_exist['Student']['student_national_id'])) && (strlen($vv) > 7) && empty($national_id_exists)) {

								$this->Student->id = $student_number_exist['Student']['id'];

								if ($this->Student->saveField('student_national_id', $vv)) {
									$validStudentIds[$kk] = $rowCount;
									$results_to_html_table[$kk]['status'] = 'Updated';
								} else {
									$results_to_html_table[$kk]['status'] = 'Database Error: unable to save National ID. Please try again.';
								}

							} else if (!empty($student_number_exist['Student']['student_national_id']) && $student_number_exist['Student']['student_national_id'] == $vv ) {
								// same national id existing in DB, same as in the excel row
								//$errors_to_correct[$kk] = 'National ID: '.  $vv . ' at row # ' . $rowCount . ' is already mapped previusly to the same student ' . $student_number_exist['Student']['full_name']. ' (' . $kk . '), Update skipped.';
								$results_to_html_table[$kk]['status'] = 'Skipped: Existing Student ID to National ID Combination';

							} else if (!empty($student_number_exist['Student']['student_national_id']) && $student_number_exist['Student']['student_national_id'] != $vv && !empty($national_id_exists['Student']['studentnumber']) && $national_id_exists['Student']['student_national_id'] == $vv ) {
								// national id already used for someone
								//$errors_to_correct[$kk] = 'National ID: '.  $vv . ' at row # ' . $rowCount . ' is already mapped to other student ' . $national_id_exists['Student']['full_name']. ' (' . $national_id_exists['Student']['studentnumber'] . '). Change the national ID or remove it the student from the excel.';
								$results_to_html_table[$kk]['status'] = 'Error: National ID: '. $vv . ' is previously assigend to other student: ' . $national_id_exists['Student']['full_name'] . ' (' . $national_id_exists['Student']['studentnumber']. '). Please change it to different National ID.';
							} else if (!empty($student_number_exist['Student']['student_national_id']) && $student_number_exist['Student']['student_national_id'] != $vv) {
								// student have previous national id recorded which is diffrent to the one to be updated
								$results_to_html_table[$kk]['status'] = 'Skipped: ' . $student_number_exist['Student']['full_name'] . ' (' .$student_number_exist['Student']['studentnumber'] . ') have existing National ID: ' . $student_number_exist['Student']['student_national_id'] . ' which is different from the one you are tying to update: ' . $vv;
							} else if (!empty($national_id_exists)) {
								$results_to_html_table[$kk]['status'] = 'Error: National ID: '. $vv . ' is previously assigend to other student: ' . $national_id_exists['Student']['full_name'] . ' (' . $national_id_exists['Student']['studentnumber']. '). Please change it to different National ID.';
							} else if (strlen($vv) < 8) {
								$results_to_html_table[$kk]['status'] = 'Error: National ID Length can not be less than 8 characters.';
							} else {
								$results_to_html_table[$kk]['status'] = 'Unknown Error: Validation Error/End';
							}
						} else {
							$invalidStudentIds[$kk] = $rowCount;
							$results_to_html_table[$kk]['status'] = 'Error: Student ID: "' . $kk . '" is not found in the system, Please check for spelling errors.';
						}

						$rowCount++;
					}
				}

				if (!empty($validStudentIds)) {
					$this->Flash->success('Updated ' . count($validStudentIds) . ' Student National IDs.');
				} else {
					$this->Flash->info('Nothing to update. Either all of ' . count($results_to_html_table) . ' Students National IDs in your Excel file already exists in the system or you have errors in your uploaded Excel File.');
				}

				$this->set(compact('invalidStudentIds', 'errors_to_correct', 'results_to_html_table'));
			}
		}

		$current_academicyear = $this->AcademicYear->current_academicyear();
		$ac_years_to_look = $this->AcademicYear->academicYearInArray((explode('/', $current_academicyear)[0] - ACY_BACK_FOR_STUDENT_NATIONAL_ID_CHECK) , explode('/', $current_academicyear)[0]);

		$admissions_years_to_look = $this->AcademicYear->academicYearInArray((explode('/', $current_academicyear)[0] - ACY_BACK_FOR_ALL) , explode('/', $current_academicyear)[0]);
		//debug($ac_years_to_look);

		$ac_years_to_look_imploded =  "'" .implode ( "', '", $ac_years_to_look ) .  "'";
		//debug($ac_years_to_look_imploded);

		/* $nonGraduatedStudentCount = $this->Student->CourseRegistration->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $ac_years_to_look,
			), 
			'contain' => array(
				'Student' => array(
					'conditions' => array(
						'graduated' => 0,
						'Student.academicyear' => $admissions_years_to_look,
						'Student.program_id !=' => PROGRAM_REMEDIAL,
						'OR' => array(
							'Student.student_national_id IS NOT NULL',
							'Student.student_national_id != 0',
							'Student.student_national_id != ""'
						)
					)
				)
			),
			'group' => array('CourseRegistration.student_id'),
			'recursive' => -1
		));

		debug($nonGraduatedStudentCount); */

		$nonGraduatedStudentCount = $this->Student->StudentExamStatus->find('count', array(
			'conditions' => array(
				'StudentExamStatus.academic_year' => $ac_years_to_look,
				//'StudentExamStatus.academic_status_id !=' => DISMISSED_ACADEMIC_STATUS_ID,
			), 
			'contain' => array(
				'Student' => array(
					'conditions' => array(
						'Student.graduated' => 0,
						'Student.program_id !=' => PROGRAM_REMEDIAL,
						'OR' => array(
							'Student.student_national_id IS NOT NULL',
							'Student.student_national_id != 0',
							'Student.student_national_id != ""'
						),
						'Student.id IN (select student_id from course_registrations where academic_year IN (' .$ac_years_to_look_imploded . ') GROUP BY student_id ) '
					)
				)
			),
			'group' => array('StudentExamStatus.student_id'),
			'recursive' => -1
		));

		debug($nonGraduatedStudentCount);

		//$totalStudentCount = $this->Student->find('count', array('conditions' => array('Student.graduated = 0', 'Student.academicyear' => $admissions_years_to_look), 'recursive' => -1));

		/* $totalStudentCount = $this->Student->find('count', array(
			'conditions' => array(
				'Student.graduated = 0',
				'Student.program_id !=' => PROGRAM_REMEDIAL,
				'Student.academicyear' => $admissions_years_to_look,
				'Student.id IN (select student_id from course_registrations where academic_year IN (' .$ac_years_to_look_imploded . ') GROUP BY student_id ) '
			), 
			'recursive' => -1
		)); */

		$totalStudentCount = $this->Student->CourseRegistration->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $ac_years_to_look,
			), 
			'contain' => array(
				'Student' => array(
					'conditions' => array(
						'graduated' => 0,
						'Student.program_id !=' => PROGRAM_REMEDIAL,
						'Student.academicyear' => $admissions_years_to_look,
					)
				)
			),
			'group' => array('CourseRegistration.student_id'),
			'recursive' => -1
		));

		debug($totalStudentCount);

		$this->set(compact('nonGraduatedStudentCount', 'totalStudentCount'));
	}


	public function activate_deactivate_profile($parameters)
	{
		if (!empty($parameters)) {

			$student = $this->Student->find('first', array('conditions' => array('Student.id' => $parameters), 'contain' => array('User')));

			if (!empty($student) && !empty($student['User']['id'])) {
				$this->Student->User->id = $student['User']['id'];
				if ($student['User']['active'] == true) {
					$this->Student->User->saveField('active', false);
					$this->Flash->success(__('The student profile has been deactivated.'));
				} elseif ($student['User']['active'] == false) {
					$this->Student->User->saveField('active', true);
					$this->Flash->success( __('The student profile has been activated.'));
				}
			} else {
				$this->Flash->warning( __('Username/password is not issued to the student until now and no account is found associated to the studunt. Thus, there is no need to activate/deactivate account.'));
			}

			$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student['Student']['id']));
		}
	}

	public function id_card_print()
	{
		if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudent'])) {

			$options = array();
			$limit = 100;

			if (!empty($this->request->data['Search']['academicyear'])) {
				$options['conditions']['AcceptedStudent.academicyear'] = $this->request->data['Search']['academicyear'];
			}

			if (!empty($this->request->data['Search']['department_id'])) {
				
				$college_id = explode('~', $this->request->data['Search']['department_id']);
				
				if (count($college_id) > 1) {
					$options['conditions']['AcceptedStudent.college_id'] = $college_id[1];
				} else {
					$options['conditions']['AcceptedStudent.department_id'] = $college_id;
				}
			}

			if (!empty($this->request->data['Search']['name'])) {
				$options['conditions']['AcceptedStudent.first_name LIKE '] = $this->request->data['Search']['name'] . '%';
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions']['AcceptedStudent.program_type_id'] = $this->request->data['Search']['program_type_id'];
			}

			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions']['AcceptedStudent.program_id'] = $this->request->data['Search']['program_id'];
			}

			if (!empty($this->request->data['Search']['limit'])) {
				$limit = $this->request->data['Search']['limit'];
			}


			if (!empty($options)) {
				
				$this->paginate = array(
					'limit' => $limit,
					'maxLimit' => $limit
				);

				$this->paginate['conditions'] = $options['conditions'];
				$this->Paginator->settings = $this->paginate;
				
				debug($this->Paginator->settings);
				$acceptedStudents = $this->Paginator->paginate('AcceptedStudent');
				
				if (empty($acceptedStudents)) {
					$this->Flash->info(__('No result found with the given criteria.'));
				}
				
				$this->set(compact('acceptedStudents'));
			}
		}

		if (!empty($this->request->data) && !empty($this->request->data['printIDCard'])) {
			
			$studentsList = array();

			if (!empty($this->request->data['AcceptedStudent']['approve'])) {
				foreach ($this->request->data['AcceptedStudent']['approve'] as $key => $value) {
					if ($value == 1) {
						
						$university['University'] = ClassRegistry::init('University')->getAcceptedStudentUnivrsity($key);

						$studentsList[$key] = array_merge(
							$this->Student->AcceptedStudent->find('first', array(
								'conditions' => array('AcceptedStudent.id' => $key),
								'contain' => array(
									'Student' => array('Attachment'), 
									'College',
									'Department', 
									'Program',
									'ProgramType'
								))
							), 
							$university
						);
					}
				}
			}

			if (empty($studentsList)) {
				$this->Flash->info( __('No student is found with the given criteria to print ID card '));
			} else {
				$this->set(compact('studentsList'));
				$this->response->type('application/pdf');
				$this->layout = '/pdf/default';
				$this->render('id_card_print_pdf');
				return;
			}
		}

		if ($this->role_id == ROLE_SYSADMIN) {
			$department_ids = ClassRegistry::init('Department')->find('list', array('fields' => array('Department.id', 'Department.id')));
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, $department_ids, $this->college_ids, 1);
		} else if (!empty($this->department_ids) || !empty($this->college_ids)) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids, 1);
		} else if (!empty($this->department_id)) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, $this->department_id, $this->college_id, 1);
		} else {
			$departments = array();
		}

		$this->set(compact('departments'));
	}

	public function card_printing_report()
	{

		if (
			isset($this->request->data['getReport'])
			|| isset($this->request->data['getReportExcel'])
		) {
			if ($this->request->data['Student']['report_type'] == 'IDPrintingCount' || $this->request->data['Student']['report_type'] == 'NOTPrinttedIDCount') {
				if ($this->request->data['Student']['report_type'] == 'NOTPrinttedIDCount') {
					$this->request->data['Student']['printed_count'] = 0;
					$headerLabel = $this->__label(
						'Not Printed ID Card Printing Statistics  for ',
						$this->request->data['Student']['acadamic_year'],
						$this->request->data['Student']['program_type_id'],
						$this->request->data['Student']['program_id'],
						$this->request->data['Student']['department_id'],
						$this->request->data['Student']['gender']
					);
				} else {
					$headerLabel = $this->__label(
						'ID Card Printing Statistics  for ',
						$this->request->data['Student']['acadamic_year'],
						$this->request->data['Student']['program_type_id'],
						$this->request->data['Student']['program_id'],
						$this->request->data['Student']['department_id'],
						$this->request->data['Student']['gender']
					);
				}

				$distributionIDPrintingCount = $this->Student->getIDPrintCount($this->request->data['Student']);
				$years = $this->__years($this->request->data['Student']['department_id']);


				$this->set(compact(
					'distributionIDPrintingCount',
					'years',
					'headerLabel'
				));

				if ($this->request->data['Student']['report_type'] == 'IDPrintingCount' && isset($this->request->data['getReportExcel'])) {

					$this->autoLayout = false;
					$filename = 'ID Card Printing Statistics -' . date('Ymd H:i:s');

					$this->set(compact(
						'distributionIDPrintingCount',
						'years',
						'headerLabel',
						'filename'
					));

					$this->render('/Elements/reports/xls/id_printing_stats_xls');
					return;
				}
			} else if ($this->request->data['Student']['report_type'] == 'IDNotIssuedStudentList') {
				$this->request->data['Student']['printed_count'] = 0;
				$headerLabel = $this->__label(
					'ID Card Not Issued List ',
					$this->request->data['Student']['acadamic_year'],
					$this->request->data['Student']['program_type_id'],
					$this->request->data['Student']['program_id'],
					$this->request->data['Student']['department_id'],
					$this->request->data['Student']['gender']
				);

				$idNotPrintedStudentList = $this->Student->getIDPrintCount($this->request->data['Student'], 'list');
				$years = $this->__years($this->request->data['Student']['department_id']);

				$this->set(compact(
					'idNotPrintedStudentList',
					'years',
					'headerLabel'
				));

				if ($this->request->data['Student']['report_type'] == 'IDNotIssuedStudentList' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'ID Card Not Issued List -' . date('Ymd H:i:s');

					$this->set(compact(
						'idNotPrintedStudentList',
						'years',
						'headerLabel',
						'filename'
					));

					$this->render('/Elements/reports/xls/id_not_issued_student_list_xls');
					return;
				}
			}
		}
		$report_type_options = array(
			'Statistics' => array(
				'IDPrintingCount' => 'ID Print Count',
				'NOTPrinttedIDCount' => 'Not Printed ID Count',

			),
			'List' => array(
				'IDNotIssuedStudentList' => 'ID Card Not Issued Student List',
				//'profileNotCompleted' => 'Profile Not Completed Student List',
			),
			/*
    	  	'List'=>array(
                  'IDNotIssuedStudentList' => 'ID Card Not Issued Student List',
                  'profileNotCompleted' => 'Profile Not Completed Student List',
    	  	),
    	  	*/

		);
		$programs = ClassRegistry::init('Program')->find('list');
		$program_types = ClassRegistry::init('ProgramType')->find('list');

		//debug($academicStatuses);
		if ($this->role_id == ROLE_SYSADMIN) {
			$department_ids = ClassRegistry::init('Department')->find('list', array('fields' =>
			array('Department.id', 'Department.id')));

			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$department_ids,
				$this->college_ids
			);
		} else if (
			!empty($this->department_ids) ||
			!empty($this->college_ids)
		) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_ids,
				$this->college_ids
			);
		} else if (!empty($this->department_id)) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_id,
				$this->college_id
			);
		} else {
			$departments = array();
		}
		$yearLevels =  ClassRegistry::init('YearLevel')->distinct_year_level();
		$programs = array(0 => 'All Programs') + $programs;
		$program_types = array(0 => 'All Program Types') + $program_types;
		$departments = array(0 => 'All University Students') + $departments;
		$yearLevels =   array(0 => 'All Year Level') + $yearLevels;

		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;
		$default_year_level_id = null;
		$default_year_level_id = null;
		$default_region_id = null;
		$graph_type = array('bar' => 'Bar Chart', 'pie' => 'Pie Chart', 'line' => 'Line Chart');
		$this->set(compact(
			'departments',
			'academicStatuses',
			'graph_type',
			'default_region_id',
			'program_types',
			'programs',
			'default_program_type_id',
			'graph_type',
			'student_lists',
			'default_program_id',
			'default_department_id',
			'report_type_options',
			'default_year_level_id',
			'yearLevels'
		));
	}

	private function __years($college_idds)
	{
		$college_id = explode('~', $college_idds);
		if (count($college_id) > 1) {
			$years =  ClassRegistry::init('YearLevel')->find('list', array(
				'conditions' => array('YearLevel.department_id in (select id from departments where college_id=' . $college_id[1] . ' )'),
				'fields' => array(
					'YearLevel.name', 'YearLevel.name'
				)
			));
		} else if (!empty($college_idds)) {
			$years =  ClassRegistry::init('YearLevel')->find('list', array(
				'conditions' => array('YearLevel.department_id' => $college_idds),
				'fields' => array(
					'YearLevel.name', 'YearLevel.name'
				)
			));
		} else {
			$years = ClassRegistry::init('YearLevel')->find('list', array(
				'fields' => array(
					'YearLevel.name', 'YearLevel.name'
				)
			));
		}
		return $years;
	}

	private function __label($prefix, $acadamic_year, $program_type_id, $program_id, $department_id, $gender)
	{

		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');

		$label = '';
		$name = '';
		$label .= $prefix . ' ' . $acadamic_year . ' of ';
		if ($program_type_id == 0) {
			$label .= 'all program types ';
		} else {
			$label .= $programTypes[$program_type_id];
		}

		if ($program_id == 0) {
			$label .= 'undergraduate/graduate ';
		} else {
			$label .= 'in ' . $programs[$program_id];
			debug($program_id);
		}


		if ($gender == "all") {
			//$label.=' both gender';
		}

		$college_id = explode('~', $department_id);
		if (count($college_id) > 1) {
			$namee = ClassRegistry::init('College')->find('first', array(
				'conditions' => array('College.id' => $college_id[1]),
				'recursive' => -1
			));
			$name .= ' ' . $namee['College']['name'];
		} else if (!empty($department_id)) {
			$namee = ClassRegistry::init('Department')->find('first', array(
				'conditions' => array('Department.id' => $department_id),
				'recursive' => -1
			));
			$name .= ' ' . $namee['Department']['name'];
		} else if ($department_id == 0) {
			$name .= 'for all department';
		}
		$label .= $name;
		return $label;
	}

	public function print_record()
	{
		if ($this->Session->check('students')) {
			$display_field_student['Display'] = $this->Session->read('display_field_student');
			$students = $this->Session->read('students');

			if (!empty($students)) {
				$university['University'] = ClassRegistry::init('University')->getStudentUnivrsity($students[0]['Student']['id']);
				$colleges = $this->Student->College->find('first', array('conditions' => array('College.id' => $students[0]['Student']['college_id']), 'recursive' => -1));
				$departments = $this->Student->Department->find('first', array('conditions' => array('Department.id' => $students[0]['Student']['department_id']), 'recursive' => -1));
				$this->set(compact('students', 'display_field_student', 'university', 'departments', 'colleges'));
				$this->response->type('application/pdf');
				$this->layout = '/pdf/default';
				$this->render('print_students_list_pdf');
				return;
			}

		} else {
			$this->Flash->error(__('Could\'t read students data, Please refresh your page.'));
			$this->redirect(array('controller' => 'students', 'action' => 'index'));
		}
	}
	public function ajax_check_ecardnumber()
	{
		$this->layout = 'ajax';
		$value = 'Invalid';
		if (!empty($this->data)) {
			if (!empty($this->data['Student']['ecardnumber'])) {
				$u = $this->Student->find(
					'first',
					array('conditions' => array('Student.ecardnumber' =>
					$this->data['Student']['ecardnumber']))
				);

				if (empty($u)) {
					$value = 'Valid';
				}
			}
		}
		$this->set(compact('value'));
	}

	/*
	public function push_students_cafe_entry() {

    	 if (!empty($this->request->data) && !empty($this->request->data['getStudent']))
    	 {

             $options=array();
             $limit=100;
             if(!empty($this->request->data['Search']['academicyear'])){
             	 $options['conditions']['Student.admissionyear']=$this->AcademicYear->get_academicYearBegainingDate($this->request->data['Search']['academicyear']);
             }
             if(!empty($this->request->data['Search']['currentAcademicYear'])
             	&& !empty($this->request->data['Search']['currentAcademicYear'])){

             	$cafe=$this->request->data['Search']['cafe'];

             	$options['conditions'][] = 'Student.id in (select student_id from course_registrations where academic_year="'.$this->request->data['Search']['currentAcademicYear'].'" and semester="'.$this->request->data['Search']['semester'].'" and cafeteria_consumer='.$cafe.') and Student.ecardnumber is not null';

             }
             if(!empty($this->request->data['Search']['department_id'])){
             	$college_id = explode('~', $this->request->data['Search']['department_id']);
				if(count($college_id) > 1) {
					$options['conditions']['Student.college_id']=$college_id[1];
				} else {
	              $options['conditions']['Student.department_id']=$college_id;
				}
             }

             if(!empty($this->request->data['Search']['name'])){
             	 $options['conditions']['Student.first_name LIKE ']=$this->request->data['Search']['name'].'%';
             }

             if(!empty($this->request->data['Search']['program_type_id'])){
             	 $options['conditions']['Student.program_type_id']=$this->request->data['Search']['program_type_id'];
             }
             if(!empty($this->request->data['Search']['program_id'])){
             	 $options['conditions']['Student.program_id']=$this->request->data['Search']['program_id'];
             }
             if(!empty($this->request->data['Search']['limit'])){
             	 $limit=$this->request->data['Search']['limit'];
             }


             if(!empty($options)){
             	  	 $this->paginate = array('limit'=>$limit,
			  'maxLimit'=>$limit);
					 $this->paginate['conditions']=$options['conditions'];
					 $this->Paginator->settings=$this->paginate;

			      $students=$this->Paginator->paginate('Student');
			      if(empty($students)){
			      	  $this->Session->setFlash('<span></span>'.__('No result found.'), 'default',array('class'=>'error-box error-message'));
			      }
                  $this->set(compact('students'));
             }
         }

        if (!empty($this->request->data) && !empty($this->request->data['pushStudentsToCafeGate'])) {
          	   $studentsList=array();
          	   $db = ConnectionManager::getDataSource("mssql");
          	   foreach ($this->request->data['Student']['approve'] as $key => $value) {
          			if($value==1){
          				$studentsList=1;
          				$studentInfo=$this->Student->find('first',array('conditions'=>array('Student.id'=>$key),'contain'=>array('College')));
          				$mealHallAssigned=$this->Student->MealHallAssignment->find('first',array('conditions'=>array('MealHallAssignment.student_id'=>$key,'MealHallAssignment.academic_year'=>$this->request->data['Search']['currentAcademicYear']),'recursive'=>-1));

          			    $studentQuery="SELECT TOP(1) SLN_Employee FROM dbo.MSTR_Employee AS S WHERE Employee_Code='".$studentInfo['Student']['studentnumber']."'";
		                //[Access_Level4]
		  				$studentResult = $db->query($studentQuery);
		  				if(!empty($studentResult[0][0]['SLN_Employee'])){
		  					    // does the student exist ?

          			    		$cardSQL="SELECT TOP(1) SLN_Employee FROM ACS_Cards_Info AS S WHERE SLN_Employee='".$studentResult[0][0]['SLN_Employee']."'";

          			    		$cardResult = $db->query($cardSQL);
          			    		if($this->request->data['Search']['allow']==1){
          			    			  $accessLevel4Cafe=isset($mealHallAssigned['MealHallAssignment']['meal_hall_id']) ? $mealHallAssigned['MealHallAssignment']['meal_hall_id']:0 ;

          			    		} else {
          			    			$accessLevel4Cafe=0;
          			    		}


          			    		if(!empty($cardResult[0][0]['SLN_Employee'])){
          			    			$cafeAccessSQL="UPDATE ACS_Cards_Info
SET Access_Level4 = ".$accessLevel4Cafe." WHERE SLN_Employee=".$cardResult[0][0]['SLN_Employee']."";

          			    		} else{
          			    				// do inseration to ess db
		  						$slnEmployee=$studentResult[0][0]['SLN_Employee'];
		  						$cardNumber=$studentInfo['Student']['ecardnumber'];
		  						$facilityID=$studentInfo['College']['campus_id'];
		  						$accessLevel1CommonGate=13;
		  						$accessLevel2AllStudentGate=9;
		  						$accessLevel3AllLibGate=5;

		  						$accessLevel5=0;
		  						$accessLevel6=0;
		  						$accessLevel7=0;
		  						$accessLevel8=0;

		  					   $cafeAccessSQL="INSERT INTO ACS_Cards_Info(SLN_Employee,Card_Number,Facility_ID,Access_Level1,Access_Level2,Access_Level3,Access_Level4,Access_Level5,Access_Level6,Access_Level7,Access_Level8) VALUES ('$slnEmployee','$cardNumber','$facilityID','$accessLevel1CommonGate','$accessLevel2AllStudentGate','$accessLevel3AllLibGate','$accessLevel4Cafe','$accessLevel5','$accessLevel6','$accessLevel7','$accessLevel8')";
          			    	}
          			    	$cafeQuery = $db->query($cafeAccessSQL);
          			    	debug($cafeQuery);

		  				}
          			}
          		}

				if(empty($studentsList)) {
					$this->Session->setFlash('<span></span>'.__('Please select the students you would like to allow/deny cafe gate.'), 'default', array('class'=>'info-box info-message'));
				}
				else {
					$this->Session->setFlash('<span></span>'.__('The selected students has been allowed/denied cafe gate and update has been propagated to devices.'), 'default', array('class'=>'success-box success-message'));
				}

          }


         if($this->role_id==ROLE_SYSADMIN){
		 	$department_ids=ClassRegistry::init('Department')->find('list',array('fields'=>
		 		array('Department.id','Department.id')));

            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1,
			$department_ids, $this->college_ids);
		 } else if (!empty($this->department_ids) ||
	!empty($this->college_ids)) {
		     $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1,
			$this->department_ids, $this->college_ids);
		 } else if(!empty($this->department_id)) {
		     $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1,
			$this->department_id, $this->college_id);
		 }  else {
            $departments=array();
		 }
		 $this->set(compact('departments'));
    }
    */

	public function push_students_cafe_entry()
	{
		$this->_mssql();
		if (!empty($this->request->data) && !empty($this->request->data['getStudent'])) {

			$options = array();
			$limit = 100;
			if (!empty($this->request->data['Search']['academicyear'])) {
				$options['conditions']['Student.admissionyear'] = $this->AcademicYear->get_academicYearBegainingDate($this->request->data['Search']['academicyear']);
			}
			if (
				!empty($this->request->data['Search']['currentAcademicYear'])
				&& !empty($this->request->data['Search']['currentAcademicYear'])
			) {

				$cafe = $this->request->data['Search']['cafe'];

				$options['conditions'][] = 'Student.id in (select student_id from course_registrations where academic_year="' . $this->request->data['Search']['currentAcademicYear'] . '" and semester="' . $this->request->data['Search']['semester'] . '" and cafeteria_consumer=' . $cafe . ') and Student.ecardnumber is not null';
			}
			if (!empty($this->request->data['Search']['department_id'])) {
				$college_id = explode('~', $this->request->data['Search']['department_id']);
				if (count($college_id) > 1) {
					$options['conditions']['Student.college_id'] = $college_id[1];
				} else {
					$options['conditions']['Student.department_id'] = $college_id;
				}
			}

			if (!empty($this->request->data['Search']['name'])) {
				$options['conditions']['Student.first_name LIKE '] = $this->request->data['Search']['name'] . '%';
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options['conditions']['Student.program_type_id'] = $this->request->data['Search']['program_type_id'];
			}
			if (!empty($this->request->data['Search']['program_id'])) {
				$options['conditions']['Student.program_id'] = $this->request->data['Search']['program_id'];
			}
			if (!empty($this->request->data['Search']['limit'])) {
				$limit = $this->request->data['Search']['limit'];
			}


			if (!empty($options)) {
				$this->paginate = array(
					'limit' => $limit,
					'maxLimit' => $limit
				);
				$this->paginate['conditions'] = $options['conditions'];
				$this->Paginator->settings = $this->paginate;

				$students = $this->Paginator->paginate('Student');
				if (empty($students)) {
					$this->Session->setFlash('<span></span>' . __('No result found.'), 'default', array('class' => 'error-box error-message'));
				}
				$this->set(compact('students'));
			}
		}

		if (!empty($this->request->data) && !empty($this->request->data['pushStudentsToCafeGate'])) {
			$studentsList = array();

			foreach ($this->request->data['Student']['approve'] as $key => $value) {
				if ($value == 1) {
					$studentsList = 1;
					$studentInfo = $this->Student->find('first', array('conditions' => array('Student.id' => $key), 'contain' => array('College')));
					$mealHallAssigned = $this->Student->MealHallAssignment->find('first', array('conditions' => array('MealHallAssignment.student_id' => $key, 'MealHallAssignment.academic_year' => $this->request->data['Search']['currentAcademicYear']), 'recursive' => -1));

					$studentQuery = "SELECT TOP(1) SLN_Employee FROM dbo.MSTR_Employee AS S WHERE Employee_Code='" . $studentInfo['Student']['studentnumber'] . "'";
					//[Access_Level4]
					$resultSetReturn = $this->_mssql($studentQuery);

					while ($row = mssql_fetch_assoc($resultSetReturn)) {
						$studentResult[0][0]['SLN_Employee'] = $row['SLN_Employee'];
					}
					mssql_free_result($resultSetReturn);
					if (!empty($studentResult[0][0]['SLN_Employee'])) {
						// does the student exist ?

						$cardSQL = "SELECT TOP(1) SLN_Employee FROM ACS_Cards_Info AS S WHERE SLN_Employee='" . $studentResult[0][0]['SLN_Employee'] . "'";

						//	$cardResult = $db->query($cardSQL);
						$cardResultSet = $this->_mssql($cardSQL);
						while ($row = mssql_fetch_assoc($resultSetReturn)) {
							$cardResult[0][0]['SLN_Employee'] = $row['SLN_Employee'];
						}
						mssql_free_result($cardResultSet);
						if ($this->request->data['Search']['allow'] == 1) {
							$accessLevel4Cafe = isset($mealHallAssigned['MealHallAssignment']['meal_hall_id']) ? $mealHallAssigned['MealHallAssignment']['meal_hall_id'] : 0;
						} else {
							$accessLevel4Cafe = 0;
						}


						if (!empty($cardResult[0][0]['SLN_Employee'])) {
							$cafeAccessSQL = "UPDATE ACS_Cards_Info
SET Access_Level4 = " . $accessLevel4Cafe . " WHERE SLN_Employee=" . $cardResult[0][0]['SLN_Employee'] . "";
						} else {
							// do inseration to ess db
							$slnEmployee = $studentResult[0][0]['SLN_Employee'];
							$cardNumber = $studentInfo['Student']['ecardnumber'];
							$facilityID = $studentInfo['College']['campus_id'];
							$accessLevel1CommonGate = 13;
							$accessLevel2AllStudentGate = 9;
							$accessLevel3AllLibGate = 5;

							$accessLevel5 = 0;
							$accessLevel6 = 0;
							$accessLevel7 = 0;
							$accessLevel8 = 0;

							$cafeAccessSQL = "INSERT INTO ACS_Cards_Info(SLN_Employee,Card_Number,Facility_ID,Access_Level1,Access_Level2,Access_Level3,Access_Level4,Access_Level5,Access_Level6,Access_Level7,Access_Level8) VALUES ('$slnEmployee','$cardNumber','$facilityID','$accessLevel1CommonGate','$accessLevel2AllStudentGate','$accessLevel3AllLibGate','$accessLevel4Cafe','$accessLevel5','$accessLevel6','$accessLevel7','$accessLevel8')";
						}
						$cafeQuery = $this->_mssql($cafeAccessSQL);
						debug($cafeQuery);
					}
				}
			}

			if (empty($studentsList)) {
				$this->Session->setFlash('<span></span>' . __('Please select the students you would like to allow/deny cafe gate.'), 'default', array('class' => 'info-box info-message'));
			} else {
				$this->Session->setFlash('<span></span>' . __('The selected students has been allowed/denied cafe gate and update has been propagated to devices.'), 'default', array('class' => 'success-box success-message'));
			}
			mssql_close($this->conn);
		}


		if ($this->role_id == ROLE_SYSADMIN) {
			$department_ids = ClassRegistry::init('Department')->find('list', array('fields' =>
			array('Department.id', 'Department.id')));

			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$department_ids,
				$this->college_ids
			);
		} else if (
			!empty($this->department_ids) ||
			!empty($this->college_ids)
		) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_ids,
				$this->college_ids
			);
		} else if (!empty($this->department_id)) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_id,
				$this->college_id
			);
		} else {
			$departments = array();
		}
		$this->set(compact('departments'));
	}

	public function change($id = null)
	{

		if (!empty($this->request->data)) {
			$data = $this->request->data;
			$this->request->data = $this->Student->find('first', array(
				'conditions' => array('Student.id' => $this->student_id),
				'contain' => array('Contact')
			));
			$this->request->data['Student']['ecardnumber'] = $data['Student']['ecardnumber'];
			$this->request->data['Student']['phone_mobile'] = $data['Student']['phone_mobile'];
			//$this->request->data['Contact'][0]['phone_mobile']=$data['Contact'][0]['phone_mobile'];

		}



		if (!empty($this->request->data) && $this->Student->save($this->request->data)) {
			$this->Session->setFlash('<span></span>' . __('The ecardnumber and mobile phone number was updated successfully'), 'default', array('class' => 'success-box success-message'));
			$this->redirect('/');
		} else if (!empty($this->request->data)) {
			$this->Session->setFlash('<span></span>' . __('Your data could not be saved.Please, try again.', true), 'default', array('class' => 'error-box error-message'));
		}
		$this->request->data = $this->Student->find('first', array(
			'conditions' => array('Student.id' => $this->student_id),
			'contain' => array(
				'Contact',
				'Attachment'
			)
		));
	}


	
	public function delete_student_from_graduate_list_for_correction($student_id)
	{
		if ($student_id) {

			$this->Student->id = $student_id;

			if ($this->Student->saveField('graduated', 0)) {

				/* $deleteromGraduateList = "DELETE FROM `graduate_lists` WHERE  student_id = $student_id";
				$dgl = $this->Student->GraduateList->query($deleteromGraduateList);

				$deleteromSenateList = "DELETE FROM `senate_lists` WHERE  student_id = $student_id";
				$dsl = $this->Student->SenateList->query($deleteromSenateList); */


				$graduateListID = $this->Student->GraduateList->field('GraduateList.id', array('GraduateList.student_id' => $student_id));
				debug($graduateListID);

				$senateListID = $this->Student->SenateList->field('SenateList.id', array('SenateList.student_id' => $student_id));
				debug($senateListID);

				if ($this->Student->GraduateList->delete($graduateListID) && $this->Student->SenateList->delete($senateListID)) {
					$this->Flash->success('The student is now deleted from Senate and Graduation Lists');
				}
			}
			
			$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student_id));
		}
	}

	public function update_koha_db()
	{

		if (!empty($this->request->data) && !empty($this->request->data['updateKohaDB'])) {

			$status = $this->Student->extendKohaBorrowerExpireDate($this->request->data['AcceptedStudent']['approve']);

			if ($status) {
				$this->Session->setFlash(__('<span></span>You have successfully update book borrower database.'), 'default', array('class' => 'success-box success-message'));
			}
		}
		debug($this->request->data);
		if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudent'])) {
			if (!empty($this->request->data['Search']['college_id'])) {
				$conditions['AcceptedStudent.college_id'] = $this->request->data['Search']['college_id'];
			}
			if (!empty($this->request->data['Search']['name'])) {
				$conditions['AcceptedStudent.first_name like '] = $this->request->data['Search']['name'] . '%';
			}
			if (!empty($this->request->data['Search']['academicyear'])) {
				$conditions['AcceptedStudent.academicyear like '] = $this->request->data['Search']['academicyear'] . '%';
			}
			if (!empty($this->request->data['Search']['program_id'])) {
				$conditions['AcceptedStudent.program_id'] = $this->request->data['Search']['program_id'];
			}
			if (!empty($this->request->data['Search']['program_type_id'])) {
				$conditions['AcceptedStudent.program_type_id'] = $this->request->data['Search']['program_type_id'];
			}
			if (!empty($conditions)) {
				if (isset($this->request->data['Search']['limit'])) {
					$limit = $this->request->data['AcceptedStudent']['limit'];
				} else {
					$limit = 1800;
				}
				$acceptedStudentIds = $this->Student->AcceptedStudent->find('list', array(
					'conditions' => $conditions,
					'limit' => $limit,
					'maxLimit' => $limit,
					'fields' => array(
						'AcceptedStudent.id',
						'AcceptedStudent.id'
					)
				));

				$students = ClassRegistry::init('StudentExamStatus')->getMostRecentStudentStatusForKoha($acceptedStudentIds, 1);


				if (!empty($students)) {
					$acceptedStudents = $students;
					$this->set(compact('acceptedStudents'));
				} else {
					$this->Session->setFlash(__('<span></span>No data is found with your search criteria that needs update, either all students has been updated or they are not qualified for borrower extension.'), 'default', array('class' => 'info-box info-message'));
				}
			}

			//debug($conditions);

		}
		// display the right department and college based on the privilage of registrar users
		$colleges = $this->Student->College->find('list');
		$departments = $this->Student->Department->find('list');
		$this->set(compact('colleges', 'departments'));

		$programs = $this->Student->Program->find('list');
		//  $programTypes =$this->Student->ProgramType->find('list');
		$this->set(compact(
			'programs',
			'programTypes',
			'colleges',
			'departments'
		));
	}

	public function update_lms_db()
	{

		if (!empty($this->request->data) && !empty($this->request->data['deleteLMSDB'])) {


			$department_ids = $this->Student->Department->find('list', array(
				'conditions' => array('Department.college_id' => $this->request->data['Search']['college_id']),
				'fields' => array('Department.id', 'Department.id')
			));

			$db = ConnectionManager::getDataSource('lms');
			// find published courses and update the courses table
			$publishedCourseListIds = ClassRegistry::init('PublishedCourse')->find(
				'list',
				array(
					'conditions' => array(
						'PublishedCourse.semester' => $this->request->data['Search']['semester'],
						'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
						'PublishedCourse.department_id' => $department_ids,
						'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
					),
					'fields' => array(
						'PublishedCourse.id',
						'PublishedCourse.id'

					)
				)
			);
			$count = 0;
			if (isset($publishedCourseListIds) && !empty($publishedCourseListIds)) {
				$deleteCourses = "DELETE FROM `enrollment` WHERE  course_id in (" . implode(",", $publishedCourseListIds) . ")";
				$d = $db->query($deleteCourses);
				$count = count($publishedCourseListIds);
			}
			//DELETE FROM `courses` WHERE `courses`.`id` = 2

			if (isset($publishedCourseListIds) && !empty($publishedCourseListIds)) {
				$deleteCourses = "DELETE FROM `courses` WHERE  courseid in (" . implode(",", $publishedCourseListIds) . ")";
				$d = $db->query($deleteCourses);
			}

			if ($count > 0) {

				$this->Session->setFlash(__('<span></span>You have successfully deleted ' . $count . ' courses from  LMS system .'), 'default', array('class' => 'success-box success-message'));
			}
		}

		if (!empty($this->request->data) && !empty($this->request->data['updateLMSDB'])) {

			debug($this->request->data);
			$department_ids = $this->Student->Department->find('list', array(
				'conditions' => array('Department.college_id' => $this->request->data['Search']['college_id']),
				'fields' => array('Department.id', 'Department.id')
			));
			debug($department_ids);
			$db = ConnectionManager::getDataSource('lms');
			// find published courses and update the courses table
			$publishedCourseList = ClassRegistry::init('PublishedCourse')->find(
				'all',
				array(
					'conditions' => array(
						'PublishedCourse.semester' => $this->request->data['Search']['semester'],
						'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
						'PublishedCourse.department_id' => $department_ids,
						'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
						'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
					),
					'contain' => array(
						'Course',
						'CourseInstructorAssignment' => array('Staff' => array(
							'User', 'Department', 'College',
							'City', 'Country'
						)),
					)
				)
			);
			debug($publishedCourseList);
			$count = 0;
			foreach ($publishedCourseList as $pk => $pv) {

				//feed course list
				if (isset($pv['PublishedCourse']['id']) && !empty($pv['PublishedCourse']['id'])) {
					$count++;
					// course inseration
					$sqlCourseRecorded = "SELECT count(*),courseid FROM  courses as course
							where courseid=" . $pv['PublishedCourse']['id'] . "";
					$resultCourseRecorded = $db->query($sqlCourseRecorded);
					if ($resultCourseRecorded[0][0]['count(*)'] == 0) {
						//create the course if not existed.
						$fullname = $pv['Course']['course_title'] . ' ' . $pv['PublishedCourse']['academic_year'] . ' ' . $pv['PublishedCourse']['semester'];
						$shortname = $pv['PublishedCourse']['id'];
						$pid = $pv['PublishedCourse']['id'];
						$categoryid = $this->request->data['Search']['college_id'];
						$ac_year = $pv['PublishedCourse']['academic_year'];
						$semester = $pv['PublishedCourse']['semester'];

						$createCourseSql = "INSERT INTO  `courses` (`id`,
							   `fullname`,`shortname`,`courseid`,`categoryid`,
							   `ac_year`,`semester`) VALUES (NULL,
							   \"$fullname\",\"$shortname\",\"$pid\",\"$categoryid\",
							   \"$ac_year\",\"$semester\")";
						$resultinsert = $db->query($createCourseSql);
					} else {
						// nothing to do for now but we need to update course detail required
					}
				}
				//instructor enrollement done
				if (
					isset($pv['CourseInstructorAssignment']) &&
					!empty($pv['CourseInstructorAssignment'])
				) {
					//check if the instructor is primary and enroll it

					foreach ($pv['CourseInstructorAssignment'] as $cia => $civ) {
						//is that primary instructor
						debug($civ);
						if (
							$civ['isprimary'] && isset($civ['Staff']['User']['username'])
							&& !empty($civ['Staff']['User']['username'])
						) {

							$sqlUserTeacher = "SELECT count(*),username FROM  users as user
							where username='" . $civ['Staff']['User']['username'] . "'";
							$resultUserTeacher = $db->query($sqlUserTeacher);

							if ($resultUserTeacher[0][0]['count(*)'] == 0) {
								// create user

								$username = strtolower($civ['Staff']['User']['email']);
								$password = $civ['Staff']['User']['password'];
								$firstname = $civ['Staff']['first_name'];
								$middlename = $civ['Staff']['middle_name'];
								$lastname = $civ['Staff']['last_name'];
								$email = $civ['Staff']['email'];
								$city = $civ['Staff']['City']['name'];
								$country = $civ['Staff']['Country']['name'];
								$institution = $civ['Staff']['College']['name'];
								$department = $civ['Staff']['Department']['name'];
								$mobile = $civ['Staff']['phone_mobile'];
								$phone = $civ['Staff']['phone_office'];
								$amharicfirstname = $civ['Staff']['first_name'];
								$amhariclastname = $civ['Staff']['last_name'];

								$address = $civ['Staff']['address'];
								if (
									isset($firstname) && !empty($firstname)
									&& isset($middlename) && !empty($middlename)
									&& isset($username) && !empty($username)
								) {

									$createUsersSql = "INSERT INTO  `users` (`id`,`username`,`password`,
							   `firstname`,`middlename`,`lastname`,`email`,`city`,`country`
							   ,`idnumber`,`institution`,`department`,
							   `mobile`,`phone`,
							   `amharicfirstname`,
							   `amhariclastname`,

							   `address`) VALUES (NULL,
							   \"$username\",\"$password\",
							   \"$firstname\",\"$middlename\",\"$lastname\",\"$email\",
							   \"$city\",\"$country\",\"$username\",
							\"$institution\",\"$department\",\"$mobile\",\"$phone\",
							\"$amharicfirstname\",
							\"$amhariclastname\",

							\"$address\"
							)";
									$resultinsert = $db->query($createUsersSql);
								}
							}
							debug($civ);
							//Is s/he already enrolled
							$courseId = $civ['published_course_id'];
							$idNumber = strtolower($civ['Staff']['User']['email']);
							$role_name = 'editingteacher';
							$ac_year = $civ['academic_year'];
							$semester = $civ['semester'];
							debug($courseId);
							$sqlEnrollTeacher = "SELECT count(*),
							course_id FROM  enrollment as enroll
							where course_id=" . $civ['published_course_id'] . "
							and id_number='" . $civ['Staff']['User']['username'] . "'
							and role_name='editingteacher'";
							$resultEnrollTeacher = $db->query($sqlEnrollTeacher);
							//debug($resultEnrollTeacher);
							$insertToEnrollementTeacher = "INSERT INTO  `enrollment` (`id`,`course_id`,`id_number`,`role_name`,`ac_year`,`semester`) VALUES (NULL,
							\"$courseId\",\"$idNumber\",
							\"$role_name\",\"$ac_year\",\"$semester\")";

							debug($resultEnrollTeacher);

							//$resultinsert = $db->query($insertToEnrollementTeacher);

							if ($resultEnrollTeacher[0][0]['count(*)'] == 0) {
								// never enrolled for the course as teacher
								$resultTeachers = $db->query($insertToEnrollementTeacher);
							} else {
								//update the new instructor
							}
						}
					}
				}


				//student enrollement

				$registeredStudentList = ClassRegistry::init('CourseRegistration')->find(
					'all',
					array(
						'conditions' => array(
							'CourseRegistration.published_course_id' =>
							$pv['PublishedCourse']['id'],
							'CourseRegistration.id not in (select course_registration_id from course_drops)'
						),
						'contain' => array('Student' => array('User', 'Department', 'College', 'City', 'Country'))
					)
				);
				if (
					isset($registeredStudentList) &&
					!empty($registeredStudentList)
				) {
					foreach ($registeredStudentList as $regk => $regv) {

						$sqlUserStudent = "SELECT count(*),idnumber FROM  users as user
							where idnumber='" . $regv['Student']['studentnumber'] . "'";
						$resultUserStudent = $db->query($sqlUserStudent);

						if ($resultUserStudent[0][0]['count(*)'] == 0) {
							// create user

							$username = strtolower(str_replace('/', '.', $regv['Student']['User']['username']));
							$password = $regv['Student']['User']['password'];
							$firstname = $regv['Student']['first_name'];
							$lastname = $regv['Student']['last_name'];
							if (isset($regv['Student']['email']) && !empty($regv['Student']['email'])) {
								$email = strtolower($regv['Student']['User']['email']);
							} else {

								$userId = strtolower(str_replace('/', '-', $regv['Student']['studentnumber']));
								$email = $userId . INSTITUTIONAL_EMAIL_SUFFIX;
							}
							$studentnumber = $regv['Student']['studentnumber'];
							//$email = $regv['Student']['email'];
							$city = $regv['Student']['City']['name'];
							$country = $regv['Student']['Country']['name'];
							$institution = $regv['Student']['College']['name'];
							$department = $regv['Student']['Department']['name'];
							$mobile = $regv['Student']['phone_mobile'];
							$phone = $regv['Student']['phone_home'];
							$amharicfirstname = $regv['Student']['amharic_first_name'];
							$amhariclastname = $regv['Student']['amharic_last_name'];
							$middlename = $regv['Student']['middle_name'];
							$address = $regv['Student']['address1'];
							if (
								isset($firstname) && !empty($firstname)
								&& isset($middlename) && !empty($middlename)
							) {
								$createUsersStudentSql = "INSERT INTO  `users` (`id`,`username`,`password`,
							   `firstname`,`middlename`,`lastname`,`email`,`city`,`country`
							   ,`idnumber`,`institution`,`department`,
							   `mobile`,`phone`,
							   `amharicfirstname`,
							   `amhariclastname`,

							   `address`) VALUES (NULL,
							   \"$username\",\"$password\",
							   \"$firstname\",\"$middlename\",\"$lastname\",\"$email\",
							   \"$city\",\"$country\",\"$studentnumber\",
							\"$institution\",\"$department\",\"$mobile\",\"$phone\",
							\"$amharicfirstname\",
							\"$amhariclastname\",
							\"$address\"
							)";
								$resultinsert = $db->query($createUsersStudentSql);
							}
						}

						//Is s/he already enrolled
						$courseId = $regv['CourseRegistration']['published_course_id'];
						$ac_year = $regv['CourseRegistration']['academic_year'];
						$semester = $regv['CourseRegistration']['semester'];
						$idNumber = $regv['Student']['studentnumber'];
						$role_name = 'student';
						$sqlEnrollStudent = "SELECT count(*),
							course_id FROM  enrollment as enroll
							where course_id=" . $regv['CourseRegistration']['published_course_id'] . "
							and id_number='" . $regv['Student']['User']['username'] . "'
							and role_name='student'";
						$resultEnrollStudent = $db->query($sqlEnrollStudent);

						if ($resultEnrollStudent[0][0]['count(*)'] == 0) {
							// never enrolled for the course as teacher
							$insertToEnrollementStudent = "INSERT INTO  `enrollment` (`id`,`course_id`,`id_number`,`role_name`,`ac_year`,`semester`) VALUES (NULL,
								\"$courseId\",\"$idNumber\",
								\"$role_name\",\"$ac_year\",\"$semester\")";

							$resultinsertS = $db->query($insertToEnrollementStudent);
						} else {
							//update the new student enrollement
						}
					}
				}
				$courseAddedStudentList = ClassRegistry::init('CourseAdd')->find(
					'all',
					array(
						'conditions' => array(
							'CourseAdd.published_course_id' =>
							$pv['PublishedCourse']['id']
						),
						'contain' => array('Student' => array('User', 'Department', 'College', 'City', 'Country'))
					)
				);

				if (
					isset($courseAddedStudentList) &&
					!empty($courseAddedStudentList)
				) {
					foreach ($courseAddedStudentList as $addk => $addv) {

						$sqlUserStudent = "SELECT count(*),idnumber FROM  users as user
							where idnumber='" . $addv['Student']['studentnumber'] . "'";
						$resultUserStudent = $db->query($sqlUserStudent);

						if ($resultUserStudent[0][0]['count(*)'] == 0) {
							// create user

							$username = strtolower(str_replace('/', '.', $addv['Student']['User']['username']));
							$password = $addv['Student']['User']['password'];
							$firstname = $addv['Student']['first_name'];
							$lastname = $addv['Student']['last_name'];
							if (isset($addv['Student']['email']) && !empty($addv['Student']['email'])) {
								$email = strtolower($addv['Student']['User']['email']);
							} else {

								$userId = strtolower(str_replace('/', '-', $addv['Student']['studentnumber']));
								$email = $userId . INSTITUTIONAL_EMAIL_SUFFIX;
							}
							$studentnumber = $addv['Student']['studentnumber'];
							//$email = $regv['Student']['email'];
							if (
								isset($addv['Student']['City']['name'])
								&& !empty($addv['Student']['City']['name'])
							) {
								$city = $addv['Student']['City']['name'];
							} else {
								$city = "";
							}

							if (
								isset($addv['Student']['Country']['name'])
								&& !empty($addv['Student']['Country']['name'])
							) {
								$country = $addv['Student']['Country']['name'];
							} else {
								$country = "";
							}



							$institution = $addv['Student']['College']['name'];


							$department = $addv['Student']['Department']['name'];
							$mobile = $addv['Student']['phone_mobile'];
							$phone = $addv['Student']['phone_home'];
							$amharicfirstname = $addv['Student']['amharic_first_name'];
							$amhariclastname = $addv['Student']['amharic_last_name'];
							$middlename = $addv['Student']['amharic_middle_name'];
							$address = $addv['Student']['address1'];
							if (
								isset($firstname) && !empty($firstname)
								&& isset($middlename) && !empty($middlename)
								&& isset($username) && !empty($username)
							) {
								$createUsersStudentSql = "INSERT INTO  `users` (`id`,`username`,`password`,
							   `firstname`,`middlename`,`lastname`,`email`,`city`,`country`
							   ,`idnumber`,`institution`,`department`,
							   `mobile`,`phone`,
							   `amharicfirstname`,
							   `amhariclastname`,

							   `address`) VALUES (NULL,
							   \"$username\",\"$password\",
							   \"$firstname\",\"$middlename\",\"$lastname\",\"$email\",
							   \"$city\",\"$country\",\"$studentnumber\",
							\"$institution\",\"$department\",\"$mobile\",\"$phone\",
							\"$amharicfirstname\",
							\"$amhariclastname\",
							\"$address\"
							)";
								$resultinsert = $db->query($createUsersStudentSql);
							}
						}

						//Is s/he already enrolled
						$courseId = $addv['CourseAdd']['published_course_id'];
						$ac_year = $addv['CourseAdd']['academic_year'];
						$semester = $addv['CourseAdd']['semester'];
						$idNumber = $addv['Student']['studentnumber'];
						$role_name = 'student';
						$sqlEnrollStudent = "SELECT count(*),
							course_id FROM  enrollment as enroll
							where course_id=" . $addv['CourseAdd']['published_course_id'] . "
							and id_number='" . $addv['Student']['User']['username'] . "'
							and role_name='student'";
						$resultEnrollStudent = $db->query($sqlEnrollStudent);

						if ($resultEnrollStudent[0][0]['count(*)'] == 0) {
							// never enrolled for the course as teacher
							$insertToEnrollementStudent = "INSERT INTO  `enrollment` (`id`,`course_id`,`id_number`,`role_name`,`ac_year`,`semester`) VALUES (NULL,
								\"$courseId\",\"$idNumber\",
								\"$role_name\",\"$ac_year\",\"$semester\")";

							$resultinsertS = $db->query($insertToEnrollementStudent);
						} else {
							//update the new student enrollement
						}
					}
				}
			}
			if ($count > 0) {

				$this->Session->setFlash(__('<span></span>You have successfully update ' . $count . ' courses from SMIS to  LMS system .'), 'default', array('class' => 'success-box success-message'));
			} else {
				$this->Session->setFlash(__('<span></span>There is no course to synchronize  from SMIS to  LMS system .'), 'default', array('class' => 'info-box info-message'));
			}
		}


		// display the right department and college based on the privilage of registrar users
		$colleges = $this->Student->College->find('list');
		$departments = $this->Student->Department->find('list');
		$this->set(compact('colleges', 'departments'));

		$programs = $this->Student->Program->find('list');
		//  $programTypes =$this->Student->ProgramType->find('list');
		$this->set(compact(
			'programs',
			'programTypes',
			'colleges',
			'departments'
		));
	}

	private function _mssql($query)
	{
		//connect to the database
		$this->conn = mssql_connect($this->config['host'], $this->config['login'], $this->config['password']);
		$selectDB = mssql_select_db($this->config['database'], $this->conn);
		$result = mssql_query($query);
		return  $result;
	}
	private function _config()
	{
		$this->config['host'] = '10.144.5.210';
		$this->config['login'] = 'sa';
		$this->config['password'] = 'admin@123';
		$this->config['database'] = 'ESS';
	}
}