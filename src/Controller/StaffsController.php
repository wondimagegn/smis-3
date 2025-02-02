<?php
class StaffsController extends AppController
{
	public $name = 'Staffs';
	public $menuOptions = array(
		'parent' => 'security',
		'exclude' => array(
			'get_department_staffs',
			'update_staff_profile', 
			'get_instructor_combo', 
			'ajax_add_study', 
			'search',
			'add'
		),
		'alias' => array(
			'index' => 'List Staffs',
			//'add' => 'Add New Staff',
			'staff_profile' => 'Search Staff Profile',
			'general_report' => 'Staff Reports',
			'maintain_staff_profile' => 'Maintain Staff Profile',
		)
	);

	public $paginate = array();
	public $helpers = array('Media.Media');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			'get_instructor_combo', 
			'get_department_staffs', 
			'ajax_add_study', 
			'search'
		);
	}

	function __init_search_staff()
	{
		if (!empty($this->request->data['Staff'])) {
			$search_session = $this->request->data['Staff'];
			$this->Session->write('search_data_staff', $search_session);
		} else {
			if ($this->Session->check('search_data_staff')) {
				$search_session = $this->Session->read('search_data_staff');
				$this->request->data['Staff'] = $search_session;
			}
		}
	}

	function search()
	{

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

	public function index()
	{

		$limit = 100;
		$options = array();
		$page = 1;

		if (isset($this->passedArgs) && !empty($this->passedArgs)) {
			$options = array();

			if (isset($this->passedArgs['Staff.department_id']) && !empty($this->passedArgs['Staff.department_id'])) {
				$this->request->data['Staff']['department_id'] = $this->passedArgs['Staff.department_id'];
			}

			if (isset($this->passedArgs['Staff.search']) && !empty($this->passedArgs['Staff.search'])) {
				$this->request->data['Staff']['search'] = $this->passedArgs['Staff.search'];
			}
			
			if (isset($this->passedArgs['Staff.limit']) && !empty($this->passedArgs['Staff.limit'])) {
				$limit = $this->request->data['Staff']['limit'] = $this->passedArgs['Staff.limit'];
			}

			if (isset($this->passedArgs['Staff.page']) && !empty($this->passedArgs['Staff.page'])) {
				$page = $this->request->data['Staff']['page'] = $this->passedArgs['Staff.page'];
			} 
			
			if (isset($this->passedArgs['page']) && !empty($this->passedArgs['page'])) {
				$page = $this->request->data['Staff']['page'] = $this->passedArgs['page'];
			}

			$this->request->data['Staff']['status'] = $this->passedArgs['Staff.status'];
			$this->request->data['Staff']['haveuser'] = $this->passedArgs['Staff.haveuser'];
		}

		$this->__init_search_staff();

		if (isset($this->request->data['viewStaff'])) {
			if ($this->Session->check('search_data_staff')) {
				$this->Session->delete('search_data_staff');
			}
			$this->__init_search_staff();
		}

		if (isset($this->request->data) && !empty($this->request->data)) {

			$this->__init_search_staff();

			if ($this->role_id == ROLE_DEPARTMENT) {
				$this->request->data['Staff']['department_id'] = $this->department_id;
				$options[] = array('Staff.department_id' => $this->department_id);
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1)));
			} else if ($this->role_id == ROLE_COLLEGE) {
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
				if (!empty($this->request->data['Staff']['department_id'])) {
					$options[] = array('Staff.department_id' => $this->request->data['Staff']['department_id']);
				} else  {
					if (!empty($departments)) {
						$options[] = array('Staff.department_id' => array_keys($departments));
					} else {
						$options[] = array('Staff.college_id' => $this->college_id);
					}
				}
			} else if ($this->role_id != ROLE_SYSADMIN && $this->Session->read('Auth.User')['is_admin'] == 1) {
				$departments = array();
				$options[] = array('User.role_id' => $this->role_id);
			} else {
				
				if (!empty($this->request->data['Staff']['department_id'])) {
					$options[] = array('Staff.department_id' => $this->request->data['Staff']['department_id']);
				} 

				if (isset($this->department_ids) && !empty($this->department_ids)) {
					$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
				} else if (isset($this->college_ids) && !empty($this->college_ids)) {
					$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
				} else {
					$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)));
				}

				$options[] = array("Staff.id IS NOT NULL");
			}

			if ($this->request->data['Staff']['status'] == 1) {
				$options[] = array('Staff.active' => 1);
			} else if ($this->request->data['Staff']['status'] == 0) {
				$options[] = array('Staff.active' => 0);
			}

			if ($this->request->data['Staff']['haveuser'] == 1) {
				$options[] = array(
					'OR' => array(
						'Staff.user_id IS NOT NULL',
						'Staff.user_id != ""',
						'Staff.user_id != 0'
					)
				);
			} else if ($this->request->data['Staff']['haveuser'] == 0) {
				$options[] = array(
					'OR' => array(
						'Staff.user_id IS NULL',
						'Staff.user_id = ""',
						'Staff.user_id = 0'
					)
				);
			}

			if (!empty($this->request->data['Staff']['search'])) {
				$options[] = array(
					'OR' => array(
						'Staff.first_name LIKE ' => '%' . trim($this->request->data['Staff']['search']) . '%',
						'Staff.last_name LIKE ' => '%' . trim($this->request->data['Staff']['search']) . '%',
						'Staff.middle_name LIKE ' => '%' . trim($this->request->data['Staff']['search']) . '%',
						'Staff.email LIKE ' => '%' . trim($this->request->data['Staff']['search']) . '%',
						'Staff.phone_mobile LIKE ' => '%' . trim($this->request->data['Staff']['search']) . '%'
					)
				);
			}

		}

		debug($options); 
		$staffs = array();

		if (!empty($options)) {
			$this->Paginator->settings =  array(
				'conditions' => $options,
				'contain' => array(
					'Department' => array('id', 'name', 'shortname', 'college_id', 'institution_code'),
					'College' => array(
						'fields' => array('id', 'name', 'shortname', 'institution_code', 'campus_id'),
						'Campus' => array('id', 'name', 'campus_code')
					),
					'User' => array(
						'fields' => array('id', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'role_id', 'active', 'last_password_change_date', 'last_login'),
						'Role' => array('id', 'name'),
					),
					'Position' => array('id', 'position'),
					'Title' => array('id', 'title'),
				), 
				'order' => array('User.role_id' => 'ASC', 'Staff.college_id' => 'ASC', 'Staff.department_id'  => 'ASC', /* 'Staff.position_id'  => 'ASC', */ 'Staff.first_name' => 'ASC', 'Staff.middle_name' => 'ASC', 'Staff.last_name' => 'ASC', 'Staff.id' => 'ASC', 'User.last_login' => 'DESC'),
				'limit' => $limit,
				'maxLimit' => $limit,
				'recursive'=> -1
			);

			//$staffs = $this->paginate($options);

			try {
				$staffs = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('staffs'));
			} catch (NotFoundException $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['Staff'])) {
					unset($this->request->data['Staff']['page']);
					unset($this->request->data['Staff']['sort']);
					unset($this->request->data['Staff']['direction']);
				}
				unset($this->passedArgs);
				$this->__init_search_staff();
				return $this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				if (!empty($this->request->data['Staff'])) {
					unset($this->request->data['Staff']['page']);
					unset($this->request->data['Staff']['sort']);
					unset($this->request->data['Staff']['direction']);
				}
				unset($this->passedArgs);
				$this->__init_search_staff();
				return $this->redirect(array('action' => 'index'));
			}
		}

		if (empty($staffs) && isset($options) && !empty($options)) {
			$this->Flash->info(__('No staff is found with the given search criteria.'));
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1)));
		} else if ($this->role_id == ROLE_COLLEGE) {
			$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_id, 'Department.active' => 1)));
		} else if ($this->role_id != ROLE_SYSADMIN && $this->Session->read('Auth.User')['is_admin'] == 1) {
			$departments = array();
		} else {
			if (isset($this->department_ids) && !empty($this->department_ids)) {
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1)));
			} else if (isset($this->college_ids) && !empty($this->college_ids)) {
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->college_ids, 'Department.active' => 1)));
			} else {
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)));
			}
		}

		$this->set(compact('staffs', 'departments', 'limit', 'page'));
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid staff'));
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->role_id == ROLE_DEPARTMENT) {
			$staff = $this->Staff->find('first', array(
				'conditions' => array(
					'Staff.id' => $id,
					'Staff.department_id' => $this->department_id
				), 
				'contain' => array(
					'College', 
					'Position', 
					'CourseInstructorAssignment' => array('order' => array('CourseInstructorAssignment.created' => 'DESC'), 
					'PublishedCourse' => array('Course', 'Section')), 
					'Department', 
					'Title', 
					'User' => array('Role')
				)
			));

			if (empty($staff)) {
				$this->Flash->warning(__('You dont have the privilege to  view the selected staff profile.'));
				$this->redirect(array('action' => 'index'));
			}
		}

		if ($this->role_id == ROLE_COLLEGE) {
			$staff = $this->Staff->find('first', array(
				'conditions' => array(
					'Staff.id' => $id,
					'Staff.college_id' => $this->college_id, 
					//'Staff.department_id is null'
				),
				'contain' => array(
					'College', 
					'Position', 
					'CourseInstructorAssignment' => array('order' => array('CourseInstructorAssignment.created' => 'DESC'), 
					'PublishedCourse' => array('Course', 'Section')), 
					'Department', 
					'Title', 
					'User' => array('Role')
				)
			));

			if (empty($staff)) {
				$this->Flash->warning( __('You dont have the privilege to  view the selected staff profile.'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$staff = $this->Staff->find('first', array(
				'conditions' => array('Staff.id' => $id),
				'contain' => array(
					'College', 
					'CourseInstructorAssignment' => array('order' => array('CourseInstructorAssignment.created' => 'DESC'), 
					'PublishedCourse' => array('Course', 'Section')), 
					'Position', 
					'Department', 
					'Title', 
					'User' => array('Role')
				)
			));
		}

		$this->set('staff', $staff);
	}

	public function add()
	{
		if (!empty($this->request->data)) {
			$this->Staff->create();
			
			if ($this->role_id == ROLE_DEPARTMENT) {
				$this->request->data['Staff']['department_id'] = $this->department_id;
				$this->request->data['Staff']['college_id'] = $this->college_id;
			}

			if ($this->role_id == ROLE_COLLEGE) {
				$this->request->data['Staff']['college_id'] = $this->college_id;
			}

			$this->request->data = $this->Staff->preparedAttachment($this->request->data, 'Profile');

			//debug($this->request->data);
			$this->request->data['Staff']['first_name'] = ucwords(trim($this->request->data['Staff']['first_name']));
			$this->request->data['Staff']['middle_name'] = ucwords(trim($this->request->data['Staff']['middle_name']));
			$this->request->data['Staff']['last_name'] = ucwords(trim($this->request->data['Staff']['last_name']));
			$this->request->data['Staff']['email'] = strtolower(trim($this->request->data['Staff']['email']));
			debug($this->request->data);
			
			if ($this->Staff->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Flash->success(__('The staff has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The staff could not be saved. Please, try again.'));
			}
		}
		
		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate'
		);

		$servicewings = array('Academician' => 'Academician', 'Librarian' => 'Librarian', 'Registrar' => 'Registrar', 'Technical Support' => 'Technical Support');

		$positions = $this->Staff->Position->find('list', array('fields' => array('id', 'position')));
		$titles = $this->Staff->Title->find('list');
		$countries = $this->Staff->Country->find('list');

		if ($this->role_id == ROLE_SYSADMIN) {
			$colleges = $this->Staff->College->find('list', array('conditions' => array('College.active' => 1)));
			$departments = array();
			//$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)))

			if (!empty($this->request->data) && isset($this->request->data['Staff']['college_id'])) {
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Staff']['college_id'], 'Department.active' => 1)));
			}
		}

		$this->set(compact('positions', 'countries', 'educations', 'servicewings', 'titles', 'colleges', 'departments', 'users'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__('Invalid staff'));
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->role_id == ROLE_DEPARTMENT) {

			$staff = $this->Staff->find('first', array(
				'conditions' => array(
					'Staff.id' => $id,
					'Staff.department_id' => $this->department_id
				), 
				'contain' => array(
					'College', 
					'Department', 
					'Title', 
					'Position', 
					'User' => array('Role')
				)
			));

			if (empty($staff)) {
				$this->Flash->warning(__('You don\'t have the privilege to view the selected staff profile.'));
				$this->redirect(array('action' => 'index'));
			}
		}

		if ($this->role_id == ROLE_COLLEGE) {

			$staff = $this->Staff->find('first', array(
				'conditions' => array(
					'Staff.id' => $id,
					'Staff.college_id' => $this->college_id, 
					//'Staff.department_id is null'
				),
				'contain' => array(
					'College', 
					'Department', 
					'Position', 
					'Title', 
					'User' => array('Role')
				)
			));

			if (empty($staff)) {
				$this->Flash->warning(__('You don\'t have the privilege to view the selected staff profile.'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$staff = $this->Staff->find('first', array(
				'conditions' => array('Staff.id' => $id),
				'contain' => array(
					'College', 
					'Department', 
					'Position', 
					'Title', 
					'User' => array('Role')
				)
			));
		}

		if (!empty($this->request->data)) {

			if ($this->role_id == ROLE_DEPARTMENT) {
				$this->request->data['Staff']['department_id'] = $this->department_id;
				$this->request->data['Staff']['college_id'] = $this->college_id;
			}

			if ($this->role_id == ROLE_COLLEGE) {
				$this->request->data['Staff']['college_id'] = $this->college_id;
			}

			$this->request->data = $this->Staff->preparedAttachment($this->request->data, 'Profile');

			//debug($this->request->data);
			$this->request->data['Staff']['first_name'] = ucwords(trim($this->request->data['Staff']['first_name']));
			$this->request->data['Staff']['middle_name'] = ucwords(trim($this->request->data['Staff']['middle_name']));
			$this->request->data['Staff']['last_name'] = ucwords(trim($this->request->data['Staff']['last_name']));
			$this->request->data['Staff']['email'] = strtolower(trim($this->request->data['Staff']['email']));
			//debug($this->request->data);

			if ($this->Staff->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Flash->success(__('The staff has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The staff could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Staff->find('first', array('conditions' => array('Staff.id' => $id), 'contain' => array()));
		}

		$positions = $this->Staff->Position->find('list', array('fields' => array('id', 'position')));

		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate'
		);

		$servicewings = array('Academician' => 'Academician', 'Librarian' => 'Librarian', 'Registrar' => 'Registrar', 'Technical Support' => 'Technical Support');
		$countries = $this->Staff->Country->find('list');

		if ($this->role_id == ROLE_SYSADMIN) {
			$colleges = $this->Staff->College->find('list', array('conditions' => array('College.active' => 1)));
			$departments = array();
			//$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)))

			if (!empty($this->request->data) && isset($this->request->data['Staff']['college_id'])) {
				$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Staff']['college_id'], 'Department.active' => 1)));
			}
		}

		$titles = $this->Staff->Title->find('list');
		$this->set(compact('positions', 'educations', 'servicewings', 'departments', 'countries', 'titles', 'users', 'colleges'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error( __('Invalid id for staff'));
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->Staff->canItBeDeleted($id)) {
			if ($this->Staff->delete($id)) {
				$this->Flash->success(__('Staff deleted'));
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Flash->error('<span></span>' . __('Staff can not be deleted. It is involved in course assignments or it has associated user account.'));
		}

		return $this->redirect(array('action' => 'index'));
	}

	function get_instructor_combo($department_id = null)
	{
		$this->layout = 'ajax';

		$staffs = $this->Staff->find('all', array(
			'conditions' => array(
				'Staff.department_id' => $department_id,
				'Staff.active' => 1,
				'Staff.user_id  IN (SELECT id FROM users WHERE role_id =' . ROLE_INSTRUCTOR . ' OR (is_admin = 1 and role_id =' . ROLE_DEPARTMENT . '))'
			), 
			'contain' => array('Position', 'Title'))
		);

		$instructors = array();
		if (!empty($staffs)) {
			foreach ($staffs as $in => $value) {
				$instructors[$value['Position']['position']][$value['Staff']['id']] = $value['Title']['title'] . ' ' . $value['Staff']['full_name'];
			}
		}

		$this->set(compact('instructors'));
	}

	function get_department_staffs($department_id = null)
	{
		$this->layout = 'ajax';
		$staffs = array();

		if (!empty($department_id)) {
			if (strcasecmp($department_id, 'External') == 0) {
				$staffsTmp = ClassRegistry::init('StaffForExam')->find('all', array(
					'conditions' => array(
						'StaffForExam.college_id' => $this->college_id,
						'StaffForExam.active' => 1,
					),
					'fields' => array(
						'StaffForExam.id',
						'StaffForExam.first_name',
						'StaffForExam.middle_name',
						'StaffForExam.last_name'
					),
					'recursive' => -1
				));

				if (!empty($staffsTmp)) {
					foreach ($staffsTmp as $staff) {
						$staffs[$staff['StaffForExam']['id']] = $staff['StaffForExam']['first_name'] . ' ' . $staff['StaffForExam']['middle_name'] . ' ' . $staff['StaffForExam']['last_name'];
					}
				}
			} else {
				$staffs = $this->Staff->find('list', array(
					'conditions' => array(
						'Staff.department_id' => $department_id,
						'Staff.active' => 1,
					),
					'fields' => array(
						'Staff.id',
						'Staff.full_name'
					)
				));
			}
		}

		$this->set(compact('staffs'));
	}

	public function general_report()
	{
		$this->layout = 'report';

		if (isset($this->request->data['getReport']) || isset($this->request->data['getReportExcel'])) {

			if ($this->request->data['Staff']['report_type'] == 'distributionStatsGenderTeachersByGender') {

				$distributionStatistics = ClassRegistry::init('Staff')->getDistributionStats($this->request->data['Staff']['department_id'], $this->request->data['Staff']['gender']);
				//debug($distributionStatistics);
				$showFromToBlock = true;

				$headerLabel = 'Distribution Statistics of Teachers By Gender';

				if ($this->request->data['Staff']['report_type'] == 'distributionStatsGenderTeachersByGender' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Distribution Statistics of Teacher By Gender -' . date('Y-m-d');
					$this->set(compact('distributionStatistics', 'filename', 'headerLabel'));
					$this->render('/Elements/staffs/xls/distribution_gender_xls');
					return;
				}

				$this->set(compact('distributionStatistics', 'showFromToBlock', 'headerLabel'));

			} else if ($this->request->data['Staff']['report_type'] == 'distributionStatsByAcademicRank') {

				$distributionStatistics = ClassRegistry::init('Staff')->getDistributionStatsByAcademicRank($this->request->data['Staff']['department_id'], $this->request->data['Staff']['gender']);

				$showFromToBlock = true;

				$headerLabel = 'Distribution Statistics By Academic Rank';

				$teacherPositionLists = $this->Staff->find('list', array(
					'conditions' => array(
						'Staff.user_id IN (SELECT id FROM users WHERE role_id = ' . ROLE_INSTRUCTOR . ')',
					),
					'fields' => array('Staff.position_id'),
					'group' => array('Staff.position_id')
				));

				$positions = $this->Staff->Position->find('list', array('fields' => array('id', 'position'), 'conditions' => array('Position.id' => $teacherPositionLists)));

				if (isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Distribution Statistics By Academic Rank -' . date('Y-m-d');
					$this->set(compact('distributionStatistics', 'filename', 'positions', 'headerLabel'));
					$this->render('/Elements/staffs/xls/distribution_academicrank_xls');
					return;
				}

				$this->set(compact('distributionStatistics', 'showFromToBlock', 'positions', 'headerLabel'));
			}

			if ($this->request->data['Staff']['report_type'] == 'distributionStatsByStudents') {

				$distributionStatistics = ClassRegistry::init('Staff')->getDistributionStatsTeacherToStudents($this->request->data['Staff']['department_id'], $this->request->data['Staff']['gender']);
				//debug($distributionStatistics);
				$showFromToBlock = true;

				$headerLabel = 'Distribution Statistics of Teacher To Student';

				if (isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Distribution Statistics of Teacher To Students -' . date('Y-m-d');
					$this->set(compact('distributionStatistics', 'filename', 'headerLabel'));
					$this->render('/Elements/staffs/xls/distribution_teachertostudent_stat_xls');
					return;
				}

				$this->set(compact('distributionStatistics', 'showFromToBlock', 'headerLabel'));

			} else if ($this->request->data['Staff']['report_type'] == 'active_staff_list') {
				
				$distributionStatistics = ClassRegistry::init('Staff')->getActiveStaffList($this->request->data['Staff']['department_id'], $this->request->data['Staff']['gender'], 1);
				$showFromToBlock = true;
				$headerLabel = 'Active Staff List';

				if (isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Active Staff List -' . date('Y-m-d');
					$this->set(compact('distributionStatistics', 'filename', 'headerLabel'));
					$this->render('/Elements/staffs/xls/active_staff_list_xls');
					return;
				}

				$this->set(compact('distributionStatistics', 'showFromToBlock', 'headerLabel'));

			} else if ($this->request->data['Staff']['report_type'] == 'inactive_staff_list') {
				
				$distributionStatistics = ClassRegistry::init('Staff')->getActiveStaffList($this->request->data['Staff']['department_id'], $this->request->data['Staff']['gender'], 0);
				$showFromToBlock = true;
				$headerLabel = 'Deactivated Staff List';

				if (isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Deactivated Staff List -' . date('Y-m-d');
					$this->set(compact('distributionStatistics', 'filename', 'headerLabel'));
					$this->render('/Elements/staffs/xls/active_staff_list_xls');
					return;
				}

				$this->set(compact('distributionStatistics', 'showFromToBlock', 'headerLabel'));
			}
		}

		$report_type_options = array(
			'Distribution' => array(
				'distributionStatsGenderTeachersByGender' => 'Distribution Statistics Teachers By Gender',
				'distributionStatsByAcademicRank' => 'Distribution Statistics By Academic Rank',
				'distributionStatsByStudents' => 'Distribution Statistics of Teacher to  Students'
			),

			'List' => array(
				'active_staff_list' => 'Active Staff List',
				'inactive_staff_list' => 'Deactivated Staff List',
				//'top_academic_staff_rated_by_student'=>'Top Rated Teachers'
			),
		);

		//debug($academicStatuses);
		if (!empty($this->department_ids) || !empty($this->college_ids)) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_ids,
				$this->college_ids
			);
		} else {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_id,
				$this->college_id
			);
		}

		$default_department_id = $this->request->data['Staff']['department_id'];
		// $default_region_id = $this->request->data['Staff']['default_region_id'];
		$regions = ClassRegistry::init('Region')->find('list');

		if ($this->role_id == ROLE_DEPARTMENT) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				0,
				$this->department_id,
				array()
			);
		} else if ($this->role_id == ROLE_COLLEGE) {
			$departments =  ClassRegistry::init('Department')->allDepartmentsByCollege2(1, array(), $this->college_id);
		} else {
			$departments = array(0 => 'All University Departments') + $departments;
		}

		$graph_type = array(
			'pie' => 'Pie Chart', 
			'bar' => 'Bar Chart',
			'line' => 'Line Chart'
		);
		
		$this->set(compact('departments', 'regions', 'report_type_options', 'graph_type', 'default_department_id', 'default_region_id'));
	}

	public function maintain_staff_profile()
	{

		if (!empty($this->request->data)) {

			$nodept = true;

			if ($this->request->data['Staff']['servicewing'] == "Academician" && empty($this->request->data['Staff']['department_id'])) {
				$nodept = false;
			}

			$this->request->data = $this->Staff->preparedAttachment($this->request->data, 'Profile');

			//debug($this->request->data);
			$this->request->data['Staff']['first_name'] = ucwords(trim($this->request->data['Staff']['first_name']));
			$this->request->data['Staff']['middle_name'] = ucwords(trim($this->request->data['Staff']['middle_name']));
			$this->request->data['Staff']['last_name'] = ucwords(trim($this->request->data['Staff']['last_name']));
			$this->request->data['Staff']['email'] = strtolower(trim($this->request->data['Staff']['email']));
			//debug($this->request->data);

			// check for existing user and existing Email.
			$userExists = $this->Staff->User->find('first', array('conditions' => array('User.email' => $this->request->data['Staff']['email']), 'fields' => array('id', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'role_id'), 'recursive' => -1));
			debug($userExists);

			if (isset($userExists) && !empty($userExists)) {
				if ($userExists['User']['role_id'] != 3) {
					//check for user_id field in staff profile which have the id for the existing user which is not of studnet role.
					$staffDetails = $this->Staff->find('first', array('conditions' => array('OR' => array('Staff.first_name' => $userExists['User']['first_name'], 'Staff.middle_name' => $userExists['User']['middle_name'], 'Staff.last_name' => $userExists['User']['last_name']), 'Staff.user_id IS NOT NULL', 'Staff.email' => $this->request->data['Staff']['email']), 'fields' => array('id', 'first_name', 'middle_name', 'last_name', 'user_id', 'email'), 'recursive' => -1));
					//debug($staffDetails);

					if (isset($staffDetails)) {
						$this->Flash->error(__('User account for ' . $userExists['User']['first_name'] . ' ' .  $userExists['User']['middle_name'] . ' ' .  $userExists['User']['last_name'] . '(' .  $userExists['User']['username'] . ')' . ' aready exists. You do not neeed to add it again.'));
					}
				} else {
					$this->Flash->error(__('The provided email is already in use for a student ' . $userExists['User']['first_name'] . ' ' .  $userExists['User']['middle_name'] . ' ' .  $userExists['User']['last_name'] . '(' .  $userExists['User']['username'] . ')' . '. Please correct that before continuing.'));
				}
			} else {

				//check if there is staff profile with provided email that does not have a user account
				$staffProfileExists = $this->Staff->find('first', array('conditions' => array('OR' => array('Staff.user_id IS NULL', 'Staff.user_id = ""'), 'Staff.email' => $this->request->data['Staff']['email']), 'fields' => array('id', 'first_name', 'middle_name', 'last_name', 'department_id', 'user_id', 'email'), 'recursive' => -1));
				//debug($staffProfileExists);

				if (isset($staffProfileExists) && !empty($staffProfileExists)) {
					$this->Flash->error(__('Staff Profile for ' . $staffProfileExists['Staff']['first_name'] . ' ' .  $staffProfileExists['Staff']['middle_name'] . ' ' .  $staffProfileExists['Staff']['last_name'] . '(' .  $staffProfileExists['Staff']['email'] . ')' . ' already exists. Use create a user account feature instead.'));
				} else {
					$this->Staff->create();
					
					if ($nodept == false) {
						$this->Flash->error(__('Please provide department if the service wing is academician.'));
					} else {
						if ($this->Staff->saveAll($this->request->data, array('validate' => 'first'))) {
							$this->Flash->success( __('The staff has been saved.'));
							return $this->redirect(array('action' => 'index'));
						} else {
							$this->Flash->error(__('The staff could not be saved. Please, try again.'));
						}
					}
				}
			}
		}

		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate'
		);

		$servicewings = array('Academician' => 'Academician', 'Librarian' => 'Librarian', 'Registrar' => 'Registrar', 'Technical Support' => 'Technical Support');

		$positions = $this->Staff->Position->find('list', array('fields' => array('id', 'position')));
		$titles = $this->Staff->Title->find('list');
		$countries = $this->Staff->Country->find('list');
		$colleges = $this->Staff->College->find('list', array('conditions' => array('College.active' => 1)));

		$departments = array();
		//$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)))

		if (!empty($this->request->data) && isset($this->request->data['Staff']['college_id'])) {
			$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Staff']['college_id'], 'Department.active' => 1)));
		}

		$this->set(compact('positions', 'titles', 'countries', 'colleges', 'educations', 'servicewings', 'departments'));
	}

	function update_staff_profile($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error( __('Invalid staff'));
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			$nodept = true;

			if ($this->request->data['Staff']['servicewing'] == "Academician" && empty($this->request->data['Staff']['department_id'])) {
				$nodept = false;
			}

			$this->request->data = $this->Staff->preparedAttachment($this->request->data, 'Profile');

			$attachmentId = $this->Staff->Attachment->field('Attachment.id', array(
				'Attachment.model' => 'Staff',
				'Attachment.group' => 'Profile',
				'Attachment.foreign_key' => $id
			));

			if ($this->Staff->saveAll($this->request->data, array('validate' => 'first')) && $nodept) {
				$delete = $this->Staff->Attachment->delete($attachmentId);
				$this->Flash->success(__('The staff has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				if ($nodept == false) {
					$this->Flash->error( __('Please provide department if the service wing is academician.'));
				} else {
					$this->Flash->error( __('The staff could not be saved. Please, try again.'));
				}
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Staff->find('first', array('conditions' => array('Staff.id' => $id), 'contain' => array()));
			//$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Staff']['college_id'])));
		}


		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate'
		);

		$servicewings = array('Academician' => 'Academician', 'Librarian' => 'Librarian', 'Registrar' => 'Registrar', 'Technical Support' => 'Technical Support');
		$positions = $this->Staff->Position->find('list', array('fields' => array('id', 'position')));
		$titles = $this->Staff->Title->find('list');
		$countries = $this->Staff->Country->find('list');
		$colleges = $this->Staff->College->find('list', array('conditions' => array('College.active' => 1)));

		$departments = array();
		//$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)))

		if (!empty($this->request->data) && isset($this->request->data['Staff']['college_id'])) {
			$departments = $this->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $this->request->data['Staff']['college_id'], 'Department.active' => 1)));
		}

		$this->set(compact('positions', 'titles', 'countries', 'colleges', 'educations', 'departments', 'servicewings'));
		$this->render('maintain_staff_profile');
	}


	public function staff_profile($staff_id = null)
	{
		if (!empty($staff_id) && is_numeric($staff_id)) {
			$checkIdIsValid = $this->Staff->find('count', array('conditions' => array('Staff.id' => $staff_id)));
			if (isset($checkIdIsValid) && $checkIdIsValid > 0) {
				$staff_profile = $this->Staff->find('first', array(
					'conditions' => array('Staff.id' => $staff_id),
					'contain' => array(
						'Department',
						'College',
						'StaffStudy' => array('Attachment'),
						'Position',
						'Title',
						'Country',
						'Attachment'
					)
				));
				$this->set(compact('staff_profile'));
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
			if (!empty($this->request->data['Staff']['staffid'])) {
				$checkIdIsValid = $this->Staff->find('count', array('conditions' => array('Staff.staffid' => $this->request->data['Staff']['staffid'])));
				if ($checkIdIsValid > 0) {
					$staff_profile = $this->Staff->find('first', array(
						'conditions' => array('Staff.staffid' => $this->request->data['Staff']['staffid']),
						'contain' => array(
							'Department',
							'College',
							'StaffStudy' => array('Attachment'),
							'Position',
							'Title',
							'Country',
							'Attachment'
						)
					));
					$this->set(compact('staff_profile'));
				} else {
					$this->Flash->error(__('The provided staff id is not valid.'));
				}
			}
		}

		$countries = $this->Staff->Country->find('list');
		$this->set(compact('countries'));
	}

	public function ajax_add_study($staff_id, $editId = null)
	{
		$this->layout = 'ajax';

		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate', 
			'HDP' => 'HDP'
		);

		if (!empty($editId)) {
			$this->request->data = $this->Staff->StaffStudy->find('first', array('conditions' => array('StaffStudy.id' => $editId), 'contain' => array()));
		}

		$staff_profile = $this->Staff->find('first', array(
			'conditions' => array('Staff.id' => $staff_id),
			'contain' => array(
				'Department',
				'College',
				'StaffStudy' => array('Attachment', 'Country'),
				'Position',
				'Title',
				'Country',
				'Attachment'
			)
		));

		$countries = $this->Staff->Country->find('list');

		$this->set(compact('educations', 'countries', 'staff_profile'));
	}
}
