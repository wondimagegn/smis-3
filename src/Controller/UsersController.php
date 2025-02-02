<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
class UsersController extends AppController
{
	public $name = 'Users';

	public $menuOptions = array(
		//'parent' => 'dashboard',
		'parent' => 'security',
		'exclude' => array(
			'resetpassword', 
			'assign', 
			'assign_user_dorm_block',
			'assign_user_meal_hall', 
			'cancel_task_confirmation', 
			'build_user_menu', 
			'confirm_task',
			'editprofile'
		),
		'alias' => array(
			'index' => 'List All Users',
			'add' => 'Create User',
			'changePwd' => 'Change Your Password',
			'department_create_user_account' => 'Create User Account'
		),
		'weight' => -2,
	);

	public $components = array('Attempt', 'MathCaptcha', 'Email', 'Ticketmaster', 'Session', 'Flash');

	public $helpers = array('Xls', 'Media.Media', 'Session', 'Flash');

	public $paginate = array();

	public $loginAttemptLimit = 3;
	//var $loginAttemptDuration = '+1 hour';
	// var $loginAttemptDuration = '5m';
	public $loginAttemptDuration = '+5 minutes';

	public function beforeRender()
	{
		parent::beforeRender();
		// Ensure that encrypted passwords are not sent back to the user
		unset($this->request->data['User']['password']);
		unset($this->request->data['User']['passwd']);
		unset($this->request->data['User']['oldpassword']);
		unset($this->request->data['User']['password2']);
		unset($this->request->data['User']['confirm_password']);
	}

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			'login',
			'logout',
			'forget',
			'useticket',
			'changePwd',
			'resetpassword',
			'build_user_menu',
			'edit',
			'search',
			'newpassword',
			'get_department',
			'check_session'
		);

		//delete auth flash message from the session
		if ($this->Session->check('Message.auth')) {
			$this->Session->delete('Message.auth');
		}

		// If logged in, these pages require logout
		if ($this->Auth->user() && in_array($this->request->params['action'], array('login'))) {
			$this->Session->destroy();
			return $this->redirect($this->Auth->logout());
		}
	}

	function search()
	{
		$this->__init_search_index();

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

	function __init_search_index()
	{
		if (!empty($this->request->data['Search'])) {
			$this->Session->write('search_data_users_index', $this->request->data);
		} else  if ($this->Session->check('search_data_users_index')) {
			$this->request->data = $this->Session->read('search_data_users_index');
		}
	}

	function __init_clear_session_filters()
	{
		if ($this->Session->check('search_data_users_index')) {
			$this->Session->delete('search_data_users_index');
		}
	}

	function __init_clear_role_id_and_active_fields_if_set_in_session()
	{
		if ($this->Session->check('search_data_users_index')) {
			$this->Session->delete('search_data_users_index');
		}
	}

	public function login()
	{
		$this->layout = 'home';
		$this->loadModel('Securitysetting');
		//$securitysetting['Securitysetting'] = array();
		$securitysetting = $this->Securitysetting->find('first');

		if (isset($securitysetting)) {
			$number_of_login_attempt = $securitysetting['Securitysetting']['number_of_login_attempt'];
		} else {
			$number_of_login_attempt = $this->loginAttemptLimit;
		}

		if ($this->Session->read('Auth.User')) {
			$this->Flash->success('You are logged in!');
			return $this->redirect('/');
		} 

		if ($this->request->is('post')) {
			$this->request->data['User']['username'] = trim($this->request->data['User']['username']);
			$usernameExistsRoleCheck = $this->User->find('first', array('conditions' => array('User.username' => $this->request->data['User']['username']),'fields' => array('id','username','is_admin','role_id','active','failed_login'),'recursive' => -1));
			if(isset($usernameExistsRoleCheck) && !empty($usernameExistsRoleCheck)){
				$failedLogins = $usernameExistsRoleCheck['User']['failed_login'];
				if ($this->Attempt->limit($usernameExistsRoleCheck['User']['username'], 'login', $number_of_login_attempt)) {
					if ($this->Auth->login() && $this->Auth->user('active')) {
						//$this->Flash->success('You are logged in!');
						$this->User->id = $this->Auth->user('id');
						$this->User->saveField('last_login', date('Y-m-d H:i:s'));
						if($failedLogins!=0){
                            $usernameExistsRoleCheck['User']['failed_login'] = 0;
                            $this->User->save($usernameExistsRoleCheck);
                        }
						$this->Session->write('User.is_logged_in', true);
						return $this->redirect('/');
					} else {
						if ($this->Auth->user('active') == false && $this->Auth->user('role_id') == ROLE_STUDENT) {
							$graduated = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.studentnumber' => $this->Auth->user('username')), 'contain' => array('GraduateList', 'Alumnus')));
							if (isset($graduated['GraduateList']['student_id']) && !empty($graduated['GraduateList']['student_id']) && empty($graduated['Alumnus']['student_id'])) {
								return $this->redirect(array('controller' => 'alumni', 'action' => 'add'));
							}
						}
						// Invalid credentials, 
						// count as failed attempt for an hour
						$failedLogins ++;
						$usernameExistsRoleCheck['User']['failed_login'] = $failedLogins;
						$this->Flash->error('Your password is not correct. Please try again.');
						$this->Attempt->fail($usernameExistsRoleCheck['User']['username'], 'login', $this->loginAttemptDuration);
						$this->User->save($usernameExistsRoleCheck);
					}
				} else {
					// User exceeded attempt limit
					if($usernameExistsRoleCheck['User']['role_id'] != ROLE_STUDENT && $usernameExistsRoleCheck['User']['active'] = 1 && ($usernameExistsRoleCheck['User']['is_admin'] = 1 || $usernameExistsRoleCheck['User']['role_id'] == ROLE_REGISTRAR || $usernameExistsRoleCheck['User']['role_id'] == ROLE_SYSADMIN)){
						$bruteForce = $this->request->data['User']['username'] . ' account has been attempted ' . $number_of_login_attempt . ' times  from ' . $this->RequestHandler->getClientIP() . ' IP address';
						ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>' . $bruteForce . '</u>. Please give appropriate warning.');
					}
					$this->Flash->error('Too many failed attempts! ('. $failedLogins .')');
					if (!empty($this->request->data['User']['security_code'])) {
						if ($this->MathCaptcha->validates($this->request->data['User']['security_code'])) {
							if ($this->Auth->login() && $this->Auth->user('active')) {
								$this->User->id = $this->Auth->user('id');
								$this->User->lastLogin($this->Auth->user('id'));
								$this->Flash->success('You are logged in!');
								$this->redirect('/');
							} else {
								// Invalid credentials, count as failed attempt for an hour
								$this->Attempt->fail($this->request->data['User']['username'], 'login', $this->loginAttemptDuration);
								$this->Flash->error('Your password is not correct. Please try again. Your account will be locked for '. $this->loginAttemptDuration .' if you try more than '. $this->loginAttemptLimit. ' times. ('. $failedLogins . ' failed attempts aready made)');
							}
						} else {
							$this->Flash->error('Please enter the correct answer to the math question.');
						}
					}
					$this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
				}
			} else {
				//$this->Flash->set('Account with username "'. $this->request->data['User']['username'] . '" is not found in the system. Check for Spelling or Typo Errors.', array('element' => 'error'));
				$this->Flash->error('Account with username "'. $this->request->data['User']['username'] . '" is not found in the system. Check for Spelling or Typo Errors.');
			}
		}
	}

	public function logout()
	{
		$this->Session->destroy();
		$this->redirect($this->Auth->logout());
	}

	public function check_session() 
	{
        $this->autoRender = false;
        $response = array('is_logged_in' => $this->Session->check('Auth.User'));
        echo json_encode($response);
    }

	public function index()
	{
		
		$selected_limit = 100;
		$selected_search = '';
		$selected_role = $this->role_id;
		$selected_staff_active = 1;
		$selected_user_active =  1;
		$sort_order = '';
		$order_by = '';

		$page = 1;
		$sort = 'full_name';
		$direction = 'desc';

		$parent_roles = $this->User->Role->find('list',array('conditions' => array('Role.parent_id' => $this->role_id)));
		$parent_roles[$this->role_id] = $this->role_id;

		unset($parent_roles[ROLE_STUDENT]);
		
		$roles = $this->User->Role->find('list', array('conditions' => array('OR' => array('Role.parent_id' => $parent_roles, 'Role.id' => $this->role_id))));
		
		if ($this->role_id == ROLE_DEPARTMENT) {
			// no need to change parent of instructor from sysadmin to department just to list and view instructor user accounts under department role, 
			// all neccessary security checks to previent editing user details using departmetn account are implemented in edit and resetpassword methods below.
			$roles[ROLE_INSTRUCTOR] = 'Instructor';
		}

		if ($this->role_id == ROLE_COLLEGE) {
			$roles[ROLE_INSTRUCTOR] = 'Instructor';
		}

		unset($roles[ROLE_STUDENT]);

		if (!empty($this->passedArgs)) {

			$this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
			$this->request->data['Search']['name'] = $this->passedArgs['Search.name'];
			$this->request->data['Search']['role_id'] = $this->passedArgs['Search.role_id'];
			$this->request->data['Search']['Staff']['active'] = $this->passedArgs['Search.Staff.active'];
			$this->request->data['Search']['active'] = $this->passedArgs['Search.active'];
			$this->request->data['Search']['sortorder'] = $this->passedArgs['Search.sortorder'];
			$this->request->data['Search']['orderby'] = $this->passedArgs['Search.orderby'];


			if (isset($this->passedArgs['page'])) {
				$page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
			}

			if (isset($this->passedArgs['sort'])) {
				$order_by = $sort = $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
			}

			if (isset($this->passedArgs['direction'])) {
				$sort_order = $direction = $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
			}

			$this->__init_search_index();
		}


		if (isset($this->request->data['getUsers'])) {
			
			$this->__init_clear_session_filters();
			$this->__init_search_index();

			if (isset($this->passedArgs)) {
				unset($this->passedArgs);
				return $this->redirect(array('action' => 'index'));
			}
			
		}

		$this->__init_search_index();

		if (!empty($this->request->data)) {

			if (!empty($page) && !isset($this->request->data['getUsers'])) {
				$this->request->data['Search']['page'] = $page;
			}

			$selected_limit = $this->request->data['Search']['limit'] = (isset($this->request->data['Search']['limit']) ? $this->request->data['Search']['limit'] : $selected_limit);
			$selected_search = $this->request->data['Search']['name'] = (isset($this->request->data['Search']['name']) ? $this->request->data['Search']['name'] : '');
			$selected_role = $this->request->data['Search']['role_id'] = (isset($this->request->data['Search']['role_id']) ? $this->request->data['Search']['role_id'] : $this->role_id);
			$selected_staff_active = $this->request->data['Search']['Staff']['active'] = (isset($this->request->data['Search']['Staff']['active']) ? $this->request->data['Search']['Staff']['active'] : 1);
			$selected_user_active = $this->request->data['Search']['active'] = (isset($this->request->data['Search']['active']) ? $this->request->data['Search']['active'] : 1);
			$sort_order = $this->request->data['Search']['sortorder'] =  (isset($this->request->data['Search']['sortorder']) ? $this->request->data['Search']['sortorder'] : $sort_order);
			$order_by = $this->request->data['Search']['orderby'] = (isset($this->request->data['Search']['orderby']) ? $this->request->data['Search']['orderby'] : $order_by);

			$conditions = $this->User->searchUserConditions($this->role_id, $this->request->data, $this->department_id, $this->college_id);
			debug($conditions);

			$this->request->data['getUsers'] = true;

		} else {

			$search_params['Search']['name'] = '';
			$search_params['Search']['role_id'] = $this->role_id;
			$search_params['Search']['active'] = 1;
			$search_params['Search']['Staff']['active'] =  1;
			
			$conditions = $this->User->searchUserConditions($this->role_id, $search_params, $this->department_id, $this->college_id);
			debug($conditions);
		}
		
		$users = array();

		if (isset($conditions) && !empty($conditions['conditions'])) {
			
			$this->Paginator->settings =  array(
				'conditions' => $conditions['conditions'],
				'contain' => array('Role'), 
				'limit' => $selected_limit, 
				'maxLimit' => $selected_limit, 
				'order' => (isset($sort_order) ?  array('User.'.$order_by.'' => $sort_order) : array('User.active' => 'DESC', 'User.first_name' => 'ASC', 'User.middle_name' => 'ASC', 'User.last_name' => 'ASC', 'User.last_login' => 'DESC')), 
				'recursive'=> -1
			);

			try {
				$users = $this->Paginator->paginate($this->modelClass);
				$this->set(compact('users'));
			} catch (NotFoundException $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				unset($this->passedArgs);
				if (isset($this->request->data['getUsers'])) {
					unset($this->request->data['getUsers']);
				}
				return $this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				if (!empty($this->request->data['Search'])) {
					unset($this->request->data['Search']['page']);
					unset($this->request->data['Search']['sort']);
					unset($this->request->data['Search']['direction']);
				}
				unset($this->passedArgs);
				if (isset($this->request->data['getUsers'])) {
					unset($this->request->data['getUsers']);
				}
				return $this->redirect(array('action' => 'index'));
			}
		}

		if (empty($users) && isset($conditions) && !empty($conditions['conditions'])) {
			$this->Flash->info('There is no user in the serch given criteria.');
		}

		$this->__init_search_index();

		$this->set(compact('users', 'roles','selected_limit','selected_search','selected_role','selected_staff_active','selected_user_active','order_by','sort_order'));
	}

	public function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid user!');
			return $this->redirect(array('action' => 'index'));
		}
		
		$colleges = $this->User->Staff->College->find('list');
		$departments = $this->User->Staff->Department->find('list', array('fields' => array('id', 'name'), 'order' => 'Department.name'));

		$user = $this->User->find('first', array(
			'conditions' => array('User.id' => $id),
			'contain' => array(
				'Role', 
				'StaffAssigne', 
				'Staff' => array(
					'College', 
					'Department', 
					'Position', 
					'Title'
				),
				'Student' => array(
					'College', 
					'Department'
				)
			)
		));

		$this->set(compact('user', 'colleges', 'departments'));
	}

	public function add()
	{

		if (!empty($this->request->data)) {
			$this->set($this->request->data);

			foreach ($this->request->data['Staff'] as $k => $v) {
				$this->request->data['User']['first_name'] = ucwords(trim($v['first_name']));
				$this->request->data['User']['last_name'] = ucwords(trim($v['last_name']));
				$this->request->data['User']['middle_name'] = ucwords(trim($v['middle_name']));
				$this->request->data['User']['email'] = strtolower(trim($v['email']));
				break;
			}

			$userExists = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['User']['email']), 'fields' => array('id','first_name','middle_name','last_name','username','email','role_id'),'recursive' => -1));
			debug($userExists);

			if(isset($userExists) && !empty($userExists) ){
				if($userExists['User']['role_id']!= ROLE_STUDENT){
					//check for user_id field in staff profile which have the id for the existing user which is not of studnet role.
					$staffDetails = $this->User->Staff->find('first', array('conditions' => array('OR' => array('Staff.first_name' => $userExists['User']['first_name'], 'Staff.middle_name' => $userExists['User']['middle_name'],'Staff.last_name' => $userExists['User']['last_name']),'Staff.user_id IS NOT NULL','Staff.email' => $this->request->data['User']['email']),'fields' => array('id','first_name','middle_name','last_name','user_id','email'),'recursive' => -1));
					debug($staffDetails);
					if(isset($staffDetails)){
						$this->Flash->error('User account for "'. $userExists['User']['first_name'] . ' '.  $userExists['User']['middle_name'] . ' '.  $userExists['User']['last_name'] . '('.  $userExists['User']['username'] . ')'. '" aready exists. You do not neeed to add it again.');
						return $this->redirect('/users/add');
					}
				} else {
					$this->Flash->error('The provided email is already in use for a student "'. $userExists['User']['first_name'] . ' '.  $userExists['User']['middle_name'] . ' '.  $userExists['User']['last_name'] . '('.  $userExists['User']['username'] . ')'. '". Please correct that before continuing.');
					return $this->redirect('/users/add');
				}
			}

			if ($this->role_id == ROLE_SYSADMIN) {
				$check = $this->User->checkNumberOfUserAccount($this->request->data);
			} else {
				$check = true;
			}

			if ($check) {
				$password = $this->User->generatePassword(5);
				$this->request->data['User']['username'] = trim($this->request->data['User']['username']);
				$this->request->data['User']['passwd'] = $password;
				if ($this->User->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Flash->success('The user has been created and email sent to user.');
					$staff_details = $this->User->find('first', array('conditions' => array('User.id' => $this->User->id), 'contain' => array('Staff' => array('Department', 'College' => array('Campus'), 'Title'))));
					$university = ClassRegistry::init('University')->find('first', array('contain' => array('Attachment' => array('order' => array('Attachment.created DESC'))), 'order' => array('University.created DESC')));
					$this->set(compact('staff_details', 'university', 'password'));
					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('issue_password_staff_pdf');
				} else {
					debug($this->User->invalidFields());
					$this->Flash->error('The user could not be saved. Please, try again.');
				}
			} else {
				$error = $this->User->invalidFields();
				if (isset($error['college_department'])) {
					$this->Flash->error($error['college_department'][0]);
				}
			}
		}

		$parent_roles = $this->User->Role->find('list', array('conditions' => array('Role.parent_id' => $this->role_id)));
		$parent_roles[$this->role_id] = $this->role_id;
		$roles = $this->User->Role->find('list', array('conditions' => array('OR' => array('Role.parent_id' => $parent_roles, 'Role.id' => $this->role_id))));
		//$countries=$this->User->Staff->Country->find('list');
		$positions = $this->User->Staff->Position->find('list', array('fields' => array('id', 'position')));
		$titles = $this->User->Staff->Title->find('list');
		//$cities=$this->User->Staff->City->find('list');
		$colleges = $this->User->Staff->College->find('list');
		$departments = $this->User->Staff->Department->find('list');

		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate'
		);

		$servicewings = array('Academician' => 'Academician', 'Librarian' => 'Librarian', 'Registrar' => 'Registrar', 'Technical Support' => 'Technical Support');

		$this->set(compact(
			'departments',
			'educations',
			'servicewings',
			'countries',
			'cities',
			'colleges',
			'roles',
			'titles',
			'positions',
			'college_department'
		));
	}

	public function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid user!');
			return $this->redirect(array('action' => 'index'));
		}

		$check_existed_user_ids = $this->User->find('count', array('conditions' => array('User.id' => $id)));

		if ($check_existed_user_ids == 0) {
			$this->Flash->error('Invalid user!. The selected user does not exist.');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->role_id == ROLE_COLLEGE) {

			if (!ENABLE_INSTRUCTOR_USER_EDIT_COLLEGE_DEPARTMENT) {
				$check_for_instructor_role = $this->User->find('count', array('conditions' => array('User.id' => $id,'User.role_id' => ROLE_INSTRUCTOR)));
				if ($this->Session->read('Auth.User')['id'] != $id && $check_for_instructor_role > 0) {
					$this->Flash->error('Your are not allowed to edit instructor profile!.');
					return $this->redirect(array('action' => 'index'));
				}
			}

			if (!$this->User->checkUserIsBelongsInYourAdmin($id, $this->role_id, null, $this->college_id)) {
				$ownAccount = $this->User->find('count', array('conditions' => array('User.id' => $this->Auth->user('id'))));
				if (!$ownAccount) {
					$this->Flash->error('You are not elegible to edit the selected user details. The user belongs to other administrator.');
					$this->redirect(array('action' => 'index'));
				}
			}

		} else if ($this->role_id == ROLE_DEPARTMENT) {

			if (!ENABLE_INSTRUCTOR_USER_EDIT_COLLEGE_DEPARTMENT) {
				$check_for_instructor_role = $this->User->find('count', array('conditions' => array('User.id' => $id,'User.role_id' => ROLE_INSTRUCTOR)));
				if ($this->Session->read('Auth.User')['id'] != $id && $check_for_instructor_role > 0) {
					$this->Flash->error('Your are not allowed to edit instructor profile!.');
					return $this->redirect(array('action' => 'index'));
				}
			}

			if (!$this->User->checkUserIsBelongsInYourAdmin($id, $this->role_id, $this->department_id, null)) {
				$ownAccount = $this->User->find('count', array('conditions' => array('User.id' => $this->Auth->user('id'))));
				if (!$ownAccount) {
					$this->Flash->error('You are not elegible to edit the selected user details. The user belongs to other administrator.');
					$this->redirect(array('action' => 'index'));
				}
			}

		} else {
			if (!$this->User->checkUserIsBelongsInYourAdmin($id, $this->role_id)) {
				$ownAccount = $this->User->find('count', array('conditions' => array('User.id' => $this->Auth->user('id'))));
				if (!$ownAccount) {
					$this->Flash->error('You are not elegible to edit the selected user details. The user belongs to other administrator.');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		
		if (!empty($this->request->data)) {
			
			$this->User->set($this->request->data); // this set the data to the model then we can use validates function
			
			if ($this->User->validates()) {
				foreach ($this->request->data['Staff'] as $k => &$v) {
					
					$this->request->data['User']['first_name'] = ucwords(trim($v['first_name']));
					$this->request->data['User']['last_name'] = ucwords(trim($v['last_name']));
					$this->request->data['User']['middle_name'] = ucwords(trim($v['middle_name']));
					
					if (!empty($v['email'])) {
						$this->request->data['User']['email'] = strtolower(trim($v['email']));
					
					}
					$this->request->data['User']['id'] = $id;

					break;
				}

				if ($this->User->saveAll($this->request->data, array('validate' => 'first'))) {
					$this->Flash->success('The user data has been updated.');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The user could not be saved. Please, try again.');
					debug($this->User->invalidFields());
				}
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->User->read(null, $id);
		}

		$countries = $this->User->Staff->Country->find('list');
		$positions = $this->User->Staff->Position->find('list', array('fields' => array('id', 'position')));
		$titles = $this->User->Staff->Title->find('list');
		$cities = $this->User->Staff->City->find('list');
		$colleges = $this->User->Staff->College->find('list', array('conditions' => array('College.active' => 1)));
		$departments = $this->User->Staff->Department->find('list', array('conditions' => array('Department.active' => 1)));

		$educations = array(
			'Doctorate' => 'PhD', 
			'Master' => 'Master',
			'Medical Doctor' => 'Medical Doctorate',
			'Degree' => 'Degree',
			'Diploma' => 'Diploma', 
			'Certificate' => 'Certificate'
		);

		$servicewings = array('Academician' => 'Academician', 'Librarian' => 'Librarian', 'Registrar' => 'Registrar', 'Technical Support' => 'Technical Support');


		//filter out main account roles they are allowed to create 

		if ($this->role_id == ROLE_DEPARTMENT) {
			$conditions = array(ROLE_DEPARTMENT, ROLE_INSTRUCTOR);
		} else if ($this->role_id == ROLE_COLLEGE) {
			$conditions = array(ROLE_COLLEGE, ROLE_INSTRUCTOR);
		} else if ($this->role_id == ROLE_REGISTRAR) {
			$conditions = array(ROLE_REGISTRAR);
		} else if ($this->role_id == ROLE_MEAL) {
			$conditions = array(ROLE_MEAL);
		} else if ($this->role_id == ROLE_HEALTH) {
			$conditions = array(ROLE_HEALTH);
		} else if ($this->role_id == ROLE_ACCOMODATION) {
			$conditions = array(ROLE_ACCOMODATION);
		} else if ($this->role_id == ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM) {
			$conditions = array(ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM);
		} else if ($this->role_id == ROLE_SYSADMIN) {
			//$conditions = array('Role.id <>' => ROLE_INSTRUCTOR, 'OR' => array('Role.id <>' => ROLE_STUDENT));
		} else {
			$conditions = array();
		}

		if (!empty($conditions)) {
			$parent_roles[$this->role_id] = $this->role_id;
			$roles = $this->User->Role->find('list', array('conditions' => array('OR' => array('Role.parent_id' => $parent_roles, 'Role.id' => $conditions))));
		} else {
			$roles = $this->User->Role->find('list');
		}

		$this->set(compact('roles'));

		$colleges = $this->User->Staff->College->find('list', array('conditions' => array('College.active' => 1)));

		$college_department = array();

		if (!empty($colleges)) {
			foreach ($colleges as $college_id => $college_name) {
				$departmentss = $this->User->Staff->Department->find('list', array(
					'conditions' => array(
						'Department.college_id' => $college_id,
						'Department.active' => 1
					),
					'fields' => array('id', 'name'),
					'order' => 'Department.name'
				));
				foreach ($departmentss as $department_id => $departmentname) {
					$college_department[$college_id][$department_id] =  $departmentname;
				}
			}
		}

		$editingUser = $this->User->find('first', array(
			'conditions' => array(
				'User.id' => $this->Auth->user('id')
			),
			'contain' => array(
				'Staff'
			),
			'fields' => array('id', 'username', 'role_id', 'is_admin'),
			'recursive' => -1
		));

		unset($editingUser['User']['password']);

		if ($this->Auth->user('id') === $id) {
			$ownAccountOfEditingUser = 1;
		} else {
			$ownAccountOfEditingUser = 0;
		}

		$this->set(compact(
			'id',
			'departments',
			'educations',
			'countries',
			'cities',
			'colleges',
			'titles',
			'positions',
			'college_department',
			'editingUser',
			'ownAccountOfEditingUser',
			'servicewings'
		));
	}

	public function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid id for user.');
			return $this->redirect(array('action' => 'index'));
		}
		/* if ($this->User->delete($id)) {
			$this->Flash->success('User deleted!');
			return $this->redirect(array('action' => 'index'));
		} */
		$this->Flash->error('User was not deleted.');
		return $this->redirect(array('action' => 'index'));
	}

	public function changePwd()
	{

		if(!isset($this->Session->read('Auth.User')['id'])){
			$this->Session->destroy();
			return $this->redirect($this->Auth->logout());
		}
		
		if (!empty($this->request->data)) {

			$this->loadModel('Securitysetting');
			$securitysetting = $this->Securitysetting->find('first');
			$password_strength = $this->User->doesItFullfillPasswordStrength($this->request->data['User']['passwd'], $securitysetting['Securitysetting']);
			$password_used = $this->User->PasswordHistory->isThePasswordUsedBefore($this->Auth->user('id'), $this->request->data['User']['passwd']);

			if (!empty($this->request->data['User']['password2']) && !empty($this->request->data['User']['passwd'])) {

				$passwd = $this->request->data['User']['passwd'];
				$passwd2 = $this->request->data['User']['password2'];

				if (strcmp($passwd, $passwd2) != 0) {
					$this->request->data = null;
					$this->Flash->error('Password change is failed. You entered two different passwords, please try again.');
				} else {
					$this->request->data['User']['id'] = $this->Auth->user('id');
					// needs  cheking ----
					$this->request->data['User']['oldpassword'] = $this->Auth->password($this->request->data['User']['oldpassword']);

					if ($this->User->veryifyOldPassword($this->request->data)) {
						// Limit to 10 failed attempts
						if (strlen($this->request->data['User']['passwd']) >= $securitysetting['Securitysetting']['minimum_password_length'] && strlen($this->request->data['User']['passwd']) <= $securitysetting['Securitysetting']['maximum_password_length']) {
							if ($password_strength) {
								if ($securitysetting['Securitysetting']['previous_password_use_allowance'] == 1 || !$password_used ) {
									
									$user = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id'))));

									debug($user);

									$passwordHistory['user_id'] = $this->Auth->user('id');
									$passwordHistory['password'] = $user['User']['password'];
									$this->User->PasswordHistory->save($passwordHistory);

									$moodle_message = '';

									//debug($passwd);

									if (ENABLE_MOODLE_INTEGRATION == 1 && !empty($user['MoodleUser']['user_id'])) {

										$moodleUseremail = trim($user['User']['email']);

										if (trim($user['User']['email']) !== trim($user['MoodleUser']['email'])) {
											if ($this->role_id == ROLE_STUDENT) {
												$moodleUseremail = isset($user['Student'][0]['email']) && !empty(trim($user['Student'][0]['email'])) ? (trim($user['Student'][0]['email'])) : (str_replace('/', '.' , (strtolower(trim($user['User']['username'])))) . INSTITUTIONAL_EMAIL_SUFFIX);
											} else {
												$moodleUseremail = isset($user['Staff'][0]['email']) && !empty(trim($user['Staff'][0]['email'])) ? (trim($user['Staff'][0]['email'])) : ((strtolower(trim($user['Staff'][0]['first_name']))) . '.'. (strtolower(trim($user['Staff'][0]['middle_name']))) . INSTITUTIONAL_EMAIL_SUFFIX);
											}
										}

										//debug($moodleUseremail);

										$moodleUseremail = '"'. (trim($moodleUseremail)) . '"';
										$newPassword = '"'. (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1($passwd) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5($passwd): md5($passwd))) . '"';

										if ($this->User->MoodleUser->updateAll(array('MoodleUser.password' => $newPassword, 'MoodleUser.email' => $moodleUseremail), array('MoodleUser.user_id' => $this->Auth->user('id')))) {
											$moodle_message = ' You can also login to ' . MOODLE_SITE_URL . ' with the same password you reset now by using a username: ' . $user['MoodleUser']['username'] . '';
										} else {
											$error = $this->User->MoodleUser->invalidFields();
											debug($error);
										}

									}

									$this->request->data['User']['force_password_change'] = 0;
									$this->request->data['User']['last_password_change_date'] = date('Y-m-d H:i:s');

									if ($this->User->save($this->request->data)) {
										$this->Flash->success('Your Password changed successfully.' . (!empty($moodle_message) ? $moodle_message : ''));
										$this->redirect('/');
									} else {
										$this->Flash->error('The User could not be saved. Please, try again.');
									}
								} else {
									$this->Flash->error('You already use the password that you entered as a new password before. Please use a password that you never used before.');
								}
							} else {
								$this->Flash->error('Your password does not fulfill the required strength which is mentioned below.');
							}
						} else {
							$this->Flash->error('Password policy: Your password should be greater than or equal to ' . $securitysetting['Securitysetting']['minimum_password_length'] . ' and less than or equal to ' . $securitysetting['Securitysetting']['maximum_password_length'] . '');
						}
					} else {
						$error = $this->User->invalidFields();
						if (isset($error['invaliduser'])) {
							$this->Flash->error($error['invaliduser'][0]);
						}
					}
				}
			} else {
				$this->Flash->error('Please provide your password.');
			}
		}
		$securitysetting = ClassRegistry::init('Securitysetting')->find('first');
		$this->set(compact('securitysetting'));
	}

	// Reset main account user password and others account,
	// reseting of main account user account requires votting
	
	public function resetpassword($id = null)
	{
		$check_existed_user_ids = $this->User->find('count', array('conditions' => array('User.id' => $id)));
		if ($check_existed_user_ids == 0) {
			$this->Flash->error('Password reset is failed. The selected user does not exist.');
			$this->redirect(array('action' => 'index'));
		}

		$check_only_active_account = $this->User->find('count', array('conditions' => array('User.id' => $id, 'User.active' => 0)));
		
		if ($check_only_active_account > 0) {
			$this->Flash->error('Password reset is failed. The account is deactive please activate the account before resetting password.');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->role_id == ROLE_COLLEGE) {

			$check_for_instructor_role = $this->User->find('count', array('conditions' => array('User.id' => $id,'User.role_id' => ROLE_INSTRUCTOR)));
			if ($this->Session->read('Auth.User')['id'] != $id && $check_for_instructor_role > 0) {
				$this->Flash->error('Your are not allowed to reset instructor password!.');
				return $this->redirect(array('action' => 'index'));
			}

			if (!$this->User->checkUserIsBelongsInYourAdmin($id, $this->role_id, null, $this->college_id )) {
				$this->Flash->error('Password reset is failed. You are not elegible to reset the password. The user belongs to other administrator.');
				$this->redirect(array('action' => 'index'));
			}
		} else if ($this->role_id == ROLE_DEPARTMENT) {

			$check_for_instructor_role = $this->User->find('count', array('conditions' => array('User.id' => $id,'User.role_id' => ROLE_INSTRUCTOR)));
			if ($this->Session->read('Auth.User')['id'] != $id && $check_for_instructor_role > 0) {
				$this->Flash->error('Your are not allowed to reset instructor password!.');
				return $this->redirect(array('action' => 'index'));
			}

			if (!$this->User->checkUserIsBelongsInYourAdmin($id, $this->role_id, $this->department_id, null)) {
				$this->Flash->error('Password reset is failed. You are not elegible to reset the password. The user belongs to other administrator.');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			if (!$this->User->checkUserIsBelongsInYourAdmin($id, $this->role_id)) {
				$this->Flash->error('Password reset is failed. You are not elegible to reset the password. The user belongs to other administrator.');
				$this->redirect(array('action' => 'index'));
			}
		}

		$breaker_detail = ClassRegistry::init('User')->find('first', array(
			'conditions' => array(
				'User.id' => $this->Auth->user('id')
			),
			'contain' => array(
				'Staff',
				'Student'
			)
		));

		$details = null;
		
		if (isset($breaker_detail['Staff']) && !empty($breaker_detail['Staff'])) {
			$details .= $breaker_detail['Staff'][0]['first_name'] . ' ' . $breaker_detail['Staff'][0]['middle_name'] . ' ' . $breaker_detail['Staff'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
		} else if (isset($breaker_detail['Student']) && !empty($breaker_detail['Student'])) {
			$details .= $breaker_detail['Student'][0]['first_name'] . ' ' . $breaker_detail['Student'][0]['middle_name'] . ' ' . $breaker_detail['Student'][0]['last_name'] . ' (' . $breaker_detail['User']['username'] . ')';
		}

		$check_instructor_account = $this->User->find('count', array('conditions' => array( 'User.id' => $id, 'User.role_id' => ROLE_INSTRUCTOR)));
		
		if ($check_instructor_account > 0) {
			$this->Flash->error('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.');
			ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>' . $details . '</u> is trying to reset  password of an instructor. Please give appropriate warning.');
			$this->redirect(array('action' => 'index'));
		}

		$is_account_admin = $this->User->find('count', array(
			'conditions' => array(
				'User.id' => $this->Auth->user('id'),
				'User.role_id' => $this->role_id,
				'User.is_admin' => 1
			)
		));

		/* $is_account_urs = $this->User->find('count', array(
			'conditions' => array(
				'User.id' => $this->Auth->user('id'), 
				'User.role_id' => $this->role_id
			)
		)); */

		// if admin or ur account allow  else deny
		if ($is_account_admin != 1 && $this->Auth->user('id') != $id) {
			$this->Flash->error('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.');
			ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>' . $details . '</u> is trying to reset password of other user. Please give appropriate warning.');
			$this->redirect(array('controller' => 'dashboard', 'action' => 'index')); 
		}

		if (!empty($this->request->data)) {
			if (empty($this->request->data['User']['id'])) {
				$this->Flash->error('User not specified');
				$this->redirect(array('action' => 'index'));
			}

			if (!empty($this->request->data['User']['password2']) && !empty($this->request->data['User']['passwd'])) {
				$passwd = $this->request->data['User']['passwd'];
				$passwd2 = $this->request->data['User']['password2'];
				if (strcmp($passwd, $passwd2) != 0) {
					$this->request->data = null;
					$this->Flash->error('Password change is failed. You entered two different passwords, please try again.');
				} else {
					// check password length
					$this->loadModel('Securitysetting');
					$securitysetting = $this->Securitysetting->find('first');

					if (strlen($this->request->data['User']['passwd']) >= $securitysetting['Securitysetting']['minimum_password_length'] && strlen($this->request->data['User']['passwd']) <= $securitysetting['Securitysetting']['maximum_password_length']) {
						
						$userH = $this->User->find('first', array(
							'conditions' => array(
								'User.id' => $this->request->data['User']['id']
							)
						));

						$passwordHistory['user_id'] = $this->request->data['User']['id'];
						$passwordHistory['password'] = $userH['User']['password'];
						$this->User->PasswordHistory->save($passwordHistory);

						$this->request->data['User']['force_password_change'] = 2;
						$this->request->data['User']['last_password_change_date'] = date('Y-m-d H:i:s');

						if ($this->User->save($this->request->data)) {
							$this->Flash->success('Password changed successfully');
							$this->redirect(array('action' => 'index'));
						} else {
							$this->Flash->error('The User could not be saved. Please, try again.');
						}
					} else {
						$this->Flash->error('Password policy: Your password should be greather than or equal to ' . $securitysetting['Securitysetting']['minimum_password_length'] . ' and less than or equal to ' . $securitysetting['Securitysetting']['maximum_password_length'] . '');
					}
				}
			} else {
				$this->Flash->error('Password change is failed. You have not provided password.');
			}
			$this->request->data = $this->User->read(null, $id);
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->User->read(null, $id);
		}
	}

	function editprofile($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid user!');
			return $this->redirect(array('action' => 'index'));
		}

		if ($id !== $this->Auth->user('id')) {
			//throw new NotFoundException(); // or redirect to a view saying that he doesn't have
			$this->Flash->error('You do not have the privilage to edit other users profile!');
			$this->redirect(array('action' => 'index'));
		}

		debug($this->request->data);

		if (!empty($this->request->data)) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success('The user has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The user could not be saved. Please, try again.');
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->User->read(null, $id);
		}
		
		$roles = $this->User->Role->find('list');
		//$students = $this->User->Student->find('list');
		$staffs = $this->User->Staff->find('list');
		$addresses = $this->User->Address->find('list');

		$this->set(compact('roles', 'staffs', 'addresses'));
	}

	public function forget()
	{
		if (isset($this->Session->read('Auth.User')['id'])) {
			$this->Flash->error('You do not need to use forget password while you are logged in, Please use change password form here instead!');
			$this->redirect(array('action' => 'changePwd'));
		}
		
		$this->layout = 'login';

		if (!empty($this->request->data)) {
			$this->User->set($this->request->data);
			//if ($this->User->validates()) {
				if (empty($this->request->data['User']['email'])) {
					$this->Flash->error('Please enter email address.');
				} else {
					if ($this->MathCaptcha->validates($this->request->data['User']['security_code'])) {	//email entered, check for it
						//$account = $this->User->findByEmail($this->request->data['User']['email']);
						$check_user_account = $this->User->find('count', array('conditions' => array('User.email' => strtolower(trim($this->request->data['User']['email'])))));

						if($check_user_account == 0){
							$this->Flash->error('Sorry the system couldn\'t find your email address.');
							return $this->redirect('/');
						} else if($check_user_account == 1){
							// check for role of the user, student or staff
							$check_user_role = $this->User->find('first', array('conditions' => array('User.email' => strtolower(trim($this->request->data['User']['email'])))));

							if($check_user_role['User']['role_id']!= ROLE_STUDENT){

								$account = $this->User->Staff->find('first', array(
									'conditions' => array(
										'User.email' => $check_user_role['User']['email'],
										'Staff.user_id' => $check_user_role['User']['id'],
										'Staff.email' => $check_user_role['User']['email'],
									),
									'contain' => array('User'),
									'fields' => array('Staff.user_id', 'Staff.email', 'User.id', 'User.email', 'User.active', 'User.last_login'),
									'recursive' => -1
								));

							} else {

								$account = $this->User->Student->find('first', array(
									'conditions' => array(
										'User.email' => $check_user_role['User']['email'],
										'Student.user_id' => $check_user_role['User']['id'],
										'Student.email' => $check_user_role['User']['email'],
									),
									'contain' => array('User'),
									'fields' => array('Student.user_id', 'Student.email', 'User.id', 'User.email', 'User.active', 'User.last_login'),
									'recursive' => -1
								));
							}

						} else {
							// more than 1 accounts found, check for active accounts, if there are again more than 2 accounts using the same role, display appropraite error message.
							$check_user_account_active = $this->User->find('count', array('conditions' => array('User.active' => 1, 'User.email' => strtolower(trim($this->request->data['User']['email'])))));

							if ($check_user_account_active == 0) {
								$this->Flash->error('There were '.$check_user_account .' accounts registered with your email '.strtolower(trim($this->request->data['User']['email'])).'</i>, but none of them are active!');
								return $this->redirect('/');
							} else if ($check_user_account_active == 1) {
								//check for the active user account, is that student or staff role
								if ($check_user_account_active['User']['role_id']!= ROLE_STUDENT) {

									$account = $this->User->Staff->find('first', array(
										'conditions' => array(
											'User.email' => $check_user_account_active['User']['email'],
											'Staff.user_id' => $check_user_account_active['User']['id'],
											'Staff.email' => $check_user_account_active['User']['email'],
										),
										'contain' => array('User'),
										'fields' => array('Staff.user_id', 'Staff.email', 'User.id', 'User.email', 'User.active', 'User.last_login'),
										'recursive' => -1
									));
	
								} else {
	
									$account = $this->User->Student->find('first', array(
										'conditions' => array(
											'User.email' => $check_user_account_active['User']['email'],
											'Student.user_id' => $check_user_account_active['User']['id'],
											'Student.email' => $check_user_account_active['User']['email'],
										),
										'contain' => array('User'),
										'fields' => array('Student.user_id', 'Student.email', 'User.id', 'User.email', 'User.active', 'User.last_login'),
										'order' => array('User.created' => 'DESC'),
										'recursive' => -1
									));
								}

							} else {
								//there are more than 1 active accounts, try to split them on role based, if they are the from  same role, display the last error message and end it here!!
								$check_user_account_active_role_based = $this->User->find('count', array('conditions' => array('User.active' => 1, 'User.email' => strtolower(trim($this->request->data['User']['email']))), 'groupby'=>'User.role_id'));

								if($check_user_account_active_role_based == 0){
									$this->Flash->error('There are '.$check_user_account .' accounts registered with your email '.strtolower(trim($this->request->data['User']['email'])).', but none of them are active!');
									return $this->redirect('/');
								} else if($check_user_account_active_role_based == 1){
									if($check_user_account_active_role_based['User']['role_id']!= ROLE_STUDENT){

										$account = $this->User->Staff->find('first', array(
											'conditions' => array(
												'User.email' => $check_user_account_active_role_based['User']['email'],
												'Staff.user_id' => $check_user_account_active_role_based['User']['id'],
												'Staff.email' => $check_user_account_active_role_based['User']['email'],
											),
											'contain' => array('User'),
											'fields' => array('Staff.user_id', 'Staff.email', 'User.id', 'User.email', 'User.active', 'User.last_login'),
											'recursive' => -1
										));
		
									} else {
		
										$account = $this->User->Student->find('first', array(
											'conditions' => array(
												'User.email' => $check_user_account_active_role_based['User']['email'],
												'Student.user_id' => $check_user_account_active_role_based['User']['id'],
												'Student.email' => $check_user_account_active_role_based['User']['email'],
											),
											'contain' => array('User'),
											'fields' => array('Student.user_id', 'Student.email', 'User.id', 'User.email', 'User.active', 'User.last_login'),
											'order' => array('User.created' => 'DESC'),
											'recursive' => -1
										));
									}
								} else {
									$this->Flash->error('There are '.$check_user_account_active .' active accounts registered with your email '.strtolower(trim($this->request->data['User']['email'])).' but the system couldn\'t identify your most recent account. Please contact system administrator to fix this issue.');
									return $this->redirect('/');
								}
								
							}
						} 

						debug($account);

						if (!isset($account['User']['email'])) {
							$this->Flash->error('Sorry the system couldn\'t find your email address. Make sure you are using the email you provided when your account is created or the one you updated later.');
							return $this->redirect('/');
						} else if (!$account['User']['active']) {
							//deactivated user, indicate where to go or report
							$this->Flash->error('This account is deactivated. Please contact your main account administrator or your system administrator to access your account.');
							return $this->redirect('/');
						}

						$hashyToken = md5(date('mdY') . rand(4000000, 4999999));
						$message = $this->Ticketmaster->createMessage($hashyToken);

						$Email = new CakeEmail('default');
						$Email->template('password_reset');
						$Email->emailFormat('html');
						//$Email->from(array('wondetask@gmail.com'=>'SMIS'));
						$Email->to($this->request->data['User']['email']);
						$Email->subject('SMiS Password Reset');
						$Email->viewVars(array('message' => $message));

						if ($Email->send()) {
							$this->Flash->success('Check your email. The password reset email has been sent successfully to ' . $this->request->data['User']['email']);
						} else {
							$this->Flash->error('Email not sent. Check your email SMTP settings and the Email server is up and running.');
						}
						//$this->Email->useremail($email,$account['User']['username'],$message);

						$data['Ticket']['hash'] = $hashyToken;
						$data['Ticket']['data'] = $this->request->data['User']['email'];
						$data['Ticket']['expires'] = $this->Ticketmaster->getExpirationDate();

						$this->loadModel('Ticket');

						if ($this->Ticket->save($data)) {
							$this->Flash->success('An email has been sent to "'. $this->request->data['User']['email']. '" with instructions to reset your SMiS password. Please check your email!');
							return $this->redirect('/');
						} else {
							$this->Flash->error('Ticket could not be issued. Please try angain later.');
							return $this->redirect('/');
						}
					} else {
						$this->Flash->error('Please enter the correct answer to the math question.');
					}
				}
			//}
		}
		$this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
	}

	function useticket($hash)
	{
		if (!isset($hash)) {
			return $this->redirect($this->Auth->logout());
		}

		//purge all expired tickets
		//built into check
		$this->layout = 'forget';
		$this->loadModel('Ticket');

		$results = $this->Ticketmaster->checkTicket($hash);

		if ($results) {
			$passTicket = $this->User->findByEmail($results['Ticket']['data']);

			//TO DO: auto verify email on successfull use of email in password resets, Neway

			$this->Ticketmaster->voidTicket($hash);
			$this->Session->write('tokenreset', $passTicket['User']['id']);
			$this->Flash->info('Enter your new password below.');
			return $this->redirect('/users/newpassword/' . $passTicket['User']['id']);
		} else {
			$this->Flash->error('Your ticket is expired or you already used the it. Please reinitiate your password reset again by using "Forgot password?" link.');
			return $this->redirect('/');
		}
	}

	public function newpassword($id = null)
	{
		$this->layout = 'forget';

		if ($this->Session->check('tokenreset')) {
			//user is not logged in, BUT has TOKEN in hand
		} else {
			//user is not logged in, passed newpassword followed by userid
			if (isset($id) && $id != $this->Session->read('Auth.User')['id']) {
				$this->Flash->error('WARNING!, you are trying to reset password without logging in or without having a valid ticket');
				return $this->redirect('/');
			} 

			//read the user info somehow, and only the user who owns the profile 
			$attempter = $this->Session->read('Auth.User');

			//make sure its the admin or the rigth user
			if ($attempter['User']['id'] != $id && $attempter['User']['role_id'] = !ROLE_SYSADMIN) {
				//not  the user, not the admin and not a reset request via tokens
				//$this->Userban->banuser('Edit Anothers Password');
				//$this->saveFiled();
				$this->Flash->error('Your account has been banned.');
				return $this->redirect('/');
			}
		}

		if (empty($this->request->data)) {
			if ($this->Session->check('tokenreset')) {
				$id = $this->Session->read('tokenreset');
			}
			if (!$id) {
				$this->Flash->error('Invalid id for User.');
				return $this->redirect('/');
			}
			$this->request->data = $this->User->read(null, $id);
		} else {
			//debug($this->request->data);
			if (!empty($this->request->data)) {
				$this->set($this->request->data);
				if ($this->User->validates()) {
					if (!empty($this->request->data['User']['confirmpassword']) && !empty($this->request->data['User']['passwd'])) {
						$passwd = $this->request->data['User']['passwd'];
						$passwd2 = $this->request->data['User']['confirmpassword'];
						if (strcmp($passwd, $passwd2) != 0) {
							//$this->request->data=null;
							$this->Flash->error('Password change failed. You entered two different passwords, please try again.');
							$this->redirect(array('action' => 'newpassword', $id));
						} else {
							// validate against password policy 
							// Limit to 10 failed attempts
							$this->loadModel('Securitysetting');
							$securitysetting = $this->Securitysetting->find('first');
							if (strlen($this->request->data['User']['passwd']) >= $securitysetting['Securitysetting']['minimum_password_length'] && strlen($this->request->data['User']['passwd']) <= $securitysetting['Securitysetting']['maximum_password_length']) {
								$updateUserPassLastLogin = $this->request->data;
								$updateUserPassLastLogin['User']['last_login'] = date('Y-m-d H:i:s');
								$updateUserPassLastLogin['User']['last_password_change_date'] = date('Y-m-d H:i:s');
								$updateUserPassLastLogin['User']['force_password_change'] = 0; 
								//debug($updateUserPassLastLogin);

								$user = $this->User->find('first', array('conditions' => array('User.id' => $id)));

								debug($user);

								$moodle_message = '';

								//debug($passwd);

								if (ENABLE_MOODLE_INTEGRATION == 1 && !empty($user['MoodleUser']['user_id'])) {

									$moodleUseremail = trim($user['User']['email']);

									if (trim($user['User']['email']) !== trim($user['MoodleUser']['email'])) {
										if ($user['User']['role_id'] == ROLE_STUDENT) {
											$moodleUseremail = isset($user['Student'][0]['email']) && !empty(trim($user['Student'][0]['email'])) ? (trim($user['Student'][0]['email'])) : (str_replace('/', '.' , (strtolower(trim($user['User']['username'])))) . INSTITUTIONAL_EMAIL_SUFFIX);
										} else {
											$moodleUseremail = isset($user['Staff'][0]['email']) && !empty(trim($user['Staff'][0]['email'])) ? (trim($user['Staff'][0]['email'])) : ((strtolower(trim($user['Staff'][0]['first_name']))) . '.'. (strtolower(trim($user['Staff'][0]['middle_name']))) . INSTITUTIONAL_EMAIL_SUFFIX);
										}
									}

									//debug($moodleUseremail);

									$moodleUseremail = '"'. (trim($moodleUseremail)) . '"';
									$newPasswordd = '"'. (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1($passwd) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ?  md5($passwd): md5($passwd))) . '"';

									if ($this->User->MoodleUser->updateAll(array('MoodleUser.password' => $newPasswordd, 'MoodleUser.email' => $moodleUseremail), array('MoodleUser.user_id' => $id))) {
										$moodle_message = ' You can also login to ' . MOODLE_SITE_URL . ' with the same password you reset now by using a username: ' . $user['MoodleUser']['username'] . '';
									} else {
										$error = $this->User->MoodleUser->invalidFields();
										debug($error);
									}

								}
								
								
								if ($this->User->save($updateUserPassLastLogin)) {
									//delete session token and delete used ticket from table
									$this->Session->delete('tokenreset');
									$this->Flash->success('Your Password has been updated.' . (!empty($moodle_message) ? $moodle_message : ''));
									$this->redirect('/');
								} else {
									$this->Flash->error('Please correct errors below.');
								}
							} else {
								$this->Flash->error('Password policy: Your password should be greather than or equal to ' . $securitysetting['Securitysetting']['minimum_password_length'] . ' and less than or equal to ' . $securitysetting['Securitysetting']['maximum_password_length'] . '');
							}
						}
					} else {
						$this->Flash->error('Please provide password and try again.');
					}
				} else {
					$this->Flash->error('The password could not be saved. Please, try again.');
				}
			}
		}
		$this->loadModel('Securitysetting');
		$securitysetting = $this->Securitysetting->find('first');
		$this->set('securitysetting', $securitysetting);
		debug($securitysetting);
	}

	function assign($id = null)
	{

		if (isset($this->request->data) && !empty($this->request->data)) {

			if (!empty($this->request->data['StaffAssigne']['program_id'])) {
				$this->request->data['StaffAssigne']['program_id'] = (!empty($this->request->data['StaffAssigne']['program_id']) ? (serialize($this->request->data['StaffAssigne']['program_id'])) : NULL);
			}

			if (!empty($this->request->data['StaffAssigne']['program_type_id'])) {
				$this->request->data['StaffAssigne']['program_type_id'] = (!empty($this->request->data['StaffAssigne']['program_type_id']) ? (serialize($this->request->data['StaffAssigne']['program_type_id'])) : NULL);
			}

			if (!empty($this->request->data['StaffAssigne']['departmentlevel'])) {
				$this->set('departmentlevel', true);
				$this->request->data['StaffAssigne']['department_id'] = (!empty($this->request->data['StaffAssigne']['department_id']) ? (serialize($this->request->data['StaffAssigne']['department_id'])) : NULL);
				$this->request->data['StaffAssigne']['college_id'] = null;
				$this->request->data['StaffAssigne']['collegepermission'] = 0;
			} else if (!empty($this->request->data['StaffAssigne']['collegelevel'])) {
				$this->set('collegelevel', true);
				$this->request->data['StaffAssigne']['college_id'] = (!empty($this->request->data['StaffAssigne']['college_id']) ? (serialize($this->request->data['StaffAssigne']['college_id'])) : NULL);
				$this->request->data['StaffAssigne']['collegepermission'] = (!empty($this->request->data['StaffAssigne']['college_id']) ? 1 : 0);
				$this->request->data['StaffAssigne']['department_id'] = null;
			} else if (empty($this->request->data['StaffAssigne']['college_id']) && $this->request->data['StaffAssigne']['department_id']) {
				$this->request->data['StaffAssigne']['college_id'] = NULL;
				$this->request->data['StaffAssigne']['collegepermission'] = 0;
				$this->request->data['StaffAssigne']['department_id'] = NULL;
				$this->request->data['StaffAssigne']['department_id'] = NULL;
				$this->request->data['StaffAssigne']['collegepermission'] = 0;
				$this->request->data['StaffAssigne']['program_id'] = NULL;
				$this->request->data['StaffAssigne']['program_type_id'] = NULL;
			}

			//debug($this->request->data);

			$this->request->data['StaffAssigne']['user_id'] = $this->request->data['User']['id'];

			$user_full_name = $this->User->field('full_name', array('User.id' => $this->request->data['User']['id']));


			debug($user_full_name);
			
			if (isset($this->request->data['assignResponsibility'])) {

				if ($this->User->StaffAssigne->save($this->request->data)) {

					if (empty($this->request->data['StaffAssigne']['college_id']) && empty($this->request->data['StaffAssigne']['department_id'])) {
						$this->Flash->warning('All assignd responsibility if any, has been revoked from ' . ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself. Please correct the changes, else you will not able to assign responsibility to yourself again if you log out' : $user_full_name) .'.');
					} else if (empty($this->request->data['StaffAssigne']['college_id']) && ($this->request->data['StaffAssigne']['collegepermission'] == 1 || isset($collegelevel))) {
						$this->Flash->warning('You assigned a college permission to '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself' : $user_full_name) . ' without selecting any college(s), if your are doing that on purpose, any assigned responsibility has been revoked from '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself' : $user_full_name) . '. Please check it again if you are assigning the permission instead.');
					} else if (empty($this->request->data['StaffAssigne']['department_id']) && ($this->request->data['StaffAssigne']['departmentlevel'] == 1 || isset($departmentlevel))) {
						$this->Flash->warning('You assigned a department permission to '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself' : $user_full_name) . ' without selecting any department(s), if your are doing that on purpose, the assigned responsibility has been revoked from '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself' : $user_full_name) . '. Please check it again if you are assigning the permission instead.');
					} else if (empty($this->request->data['StaffAssigne']['program_id']) || empty($this->request->data['StaffAssigne']['program_type_id'])) {
						$this->Flash->warning('Responsibility has been assigned to '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself' : $user_full_name) . ' without ' . (empty($this->request->data['StaffAssigne']['program_id']) && empty($this->request->data['StaffAssigne']['program_type_id']) ? ' any program and program type' : (empty($this->request->data['StaffAssigne']['program_id']) ? ' any program ': ' any program type')). ' selected. Please check it again if you are not revoking previous assigned permission from '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself' : $user_full_name) . '.');
					} else {
						$this->Flash->success('Responsibility has been assigned to '. ($this->Session->read('Auth.User')['id'] === $this->request->data['StaffAssigne']['user_id'] ? 'yourself.  You need to logout and login back to make the changes effect' : $user_full_name) . '.');
					}

					if (isset($this->request->data['User']['SelectAllColl'])) {
						unset($this->request->data['User']['SelectAllColl']);
					}
	
					if (isset($this->request->data['User']['SelectAll'])) {
						unset($this->request->data['User']['SelectAll']);
					}

					$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The user could not be saved. Please, try again.');
					if (!empty($this->request->data['StaffAssigne']['departmentlevel'])) {
						$this->set('departmentlevel', true);
						if (!empty($this->request->data['StaffAssigne']['department_id'])) {
							$this->request->data['StaffAssigne']['department_id'] = unserialize($this->request->data['StaffAssigne']['department_id']);
						}
					} else if (!empty($this->request->data['StaffAssigne']['collegelevel'])) {
						$this->set('collegelevel', true);
						$this->request->data['StaffAssigne']['college_id'] = unserialize($this->request->data['StaffAssigne']['college_id']);
					}
				}
				unset($this->request->data['assignResponsibility']);
			}
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->User->find('first', array(
				'conditions' => array('User.id' => $id),
				'contain' => array(
					'Role', 
					'StaffAssigne', 
					'Staff'
				)
			));

			//debug($this->request->data);

			if ($this->request->data['StaffAssigne']) {
				
				if (!empty($this->request->data['StaffAssigne']['collegepermission'])) {
					$this->request->data['StaffAssigne']['college_id'] = (!empty($this->request->data['StaffAssigne']['college_id']) ? (unserialize($this->request->data['StaffAssigne']['college_id'])) : NULL);
					$this->set('collegelevel', true);
				} else {
					$this->request->data['StaffAssigne']['department_id'] = (!empty($this->request->data['StaffAssigne']['department_id']) ? (unserialize($this->request->data['StaffAssigne']['department_id'])) : NULL);
					if (!empty($this->request->data['StaffAssigne']['department_id'])) {
						$this->set('departmentlevel', true);
					}
				}

				if (!empty($this->request->data['StaffAssigne']['program_id'])) {
					$this->request->data['StaffAssigne']['program_id'] = unserialize($this->request->data['StaffAssigne']['program_id']);
				}

				if (!empty($this->request->data['StaffAssigne']['program_type_id'])) {
					$this->request->data['StaffAssigne']['program_type_id'] = unserialize($this->request->data['StaffAssigne']['program_type_id']);
				}
			}
		}

		$colleges = $this->User->Staff->College->find('list', array('conditions' => array('College.active' => 1)));

		$college_department = array();

		if (!empty($colleges)) {
			foreach ($colleges as $college_id => $college_name) {
				$departments = $this->User->Staff->Department->find('list', array(
					'fields' => array('id', 'name'),
					'conditions' => array(
						'Department.college_id' => $college_id,
						'Department.active' => 1

					),
					'order' => 'Department.name'
				));
				// debug($departments);

				if (!empty($departments)) {
					foreach ($departments as $department_id => $departmentname) {
						$college_department[$college_id][$department_id] =  $departmentname;
					}
				}
			}
		}

		$basic_data = $this->User->find('first', array(
			'conditions' => array('User.id' => $id),
			'contain' => array(
				'Role', 
				'StaffAssigne', 
				'Staff'
			)
		));

		$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
		$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1)));

		$this->set(compact('colleges', 'college_department', 'programs', 'basic_data', 'id', 'programTypes'));
	}


	function assign_user_meal_hall($id = null, $unassign = null)
	{
		if ($unassign) {
			$this->__unassign_user_meal_hall($unassign, $id);
		}

		if (!empty($this->request->data)) {
			if (count($this->request->data['User']['meal_hall_id']) > 0) {
				$data = $this->__reformatedDataForMealRespAssignment($this->request->data);
				if ($this->User->UserMealAssignment->checkDuplicationAssignment($data)) {
					if ($this->User->UserMealAssignment->saveAll($data['UserMealAssignment'], array('validate' => 'first'))) {
						$this->Flash->success('Responsibility has been assigned .');
						$this->redirect(array('action' => 'index'));
					}
				} else {
					$error = $this->User->UserMealAssignment->invalidFields();
					$string = '';
					
					foreach ($error['error'] as $kk => $kv) {
						$string .= '' . $kv;
					}

					if (isset($error['error'])) {
						$this->Flash->error($string);
					}
				}
			} else {
				$this->Flash->error('The assignment could not be saved.Check atleast one meal hall for assignment .');
			}
		}

		if ($id) {
			$staff_basic_data = $this->User->find('first', array(
				'conditions' => array('User.id' => $id),
				'contain' => array('Role', 'UserMealAssignment', 'Staff' => array('Position', 'Title'))
			));
			$alreadyAssignedMealHalls = $this->User->UserMealAssignment->mealHallAssignmentOrganizedByCampus($id);
		} else if (!empty($this->request->data['User']['id'])) {
			$staff_basic_data = $this->User->find('first', array(
				'conditions' => array('User.id' => $this->request->data['User']['id']),
				'contain' => array('Role', 'UserMealAssignment', 'Staff' => array('Position', 'Title'))
			));
			$alreadyAssignedMealHalls = $this->User->UserMealAssignment->mealHallAssignmentOrganizedByCampus($id);
		}

		$mealHalls = $this->User->UserMealAssignment->MealHall->getMealHall();

		$this->set(compact('mealHalls'));
		$this->set(compact('staff_basic_data', 'alreadyAssignedMealHalls'));
	}


	function __reformatedDataForDormRespAssignment($data = null)
	{
		$user_assignment = array();
		$count = 0;

		if (!empty($data)) {
			foreach ($data['User']['dormitory_block_id'] as $i => $v) {
				$user_assignment['UserDormAssignment'][$count]['user_id'] = $data['User']['id'];
				$user_assignment['UserDormAssignment'][$count]['dormitory_block_id'] = $v;
				$count++;
			}
		}
		return $user_assignment;
	}

	function __reformatedDataForMealRespAssignment($data = null)
	{
		$user_assignment = array();
		$count = 0;

		if (!empty($data)) {
			foreach ($data['User']['meal_hall_id'] as $i => $v) {
				$user_assignment['UserMealAssignment'][$count]['user_id'] = $data['User']['id'];
				$user_assignment['UserMealAssignment'][$count]['meal_hall_id'] = $v;
				$count++;
			}
		}
		return $user_assignment;
	}

	function __unassign_user_dorm_block($assignment_id = null, $user_id = null)
	{
		if ($this->User->UserDormAssignment->delete($assignment_id)) {
			$this->Flash->success('User dorm block assignment responsibility deleted successfully');
			$this->redirect(array('action' => 'assign_user_dorm_block', $user_id));
		} else {
			$this->Flash->error('User dorm block assignment responsibility  was not deleted ');
			$this->redirect(array('action' => 'assign_user_dorm_block', $user_id));
		}
	}

	function __unassign_user_meal_hall($assignment_id = null, $user_id = null)
	{
		if ($this->User->UserMealAssignment->delete($assignment_id)) {
			$this->Flash->success('User meal hall assignment responsibility deleted successfully');
			$this->redirect(array('action' => 'assign_user_meal_hall', $user_id));
		} else {
			$this->Flash->error('User meal hall assignment responsibility  was not deleted ');
			$this->redirect(array('action' => 'assign_user_meal_hall', $user_id));
		}
	}


	public function assign_user_dorm_block($id = null, $unassign = null)
	{
		if ($unassign) {
			$this->__unassign_user_dorm_block($unassign, $id);
		}

		if (!empty($this->request->data)) {
			if (count($this->request->data['User']['dormitory_block_id']) > 0) {
				$data = $this->__reformatedDataForDormRespAssignment($this->request->data);

				if ($this->User->UserDormAssignment->checkDuplicationAssignment($data)) {
					if ($this->User->UserDormAssignment->saveAll($data['UserDormAssignment'], array('validate' => 'first'))) {
						$this->Flash->success('Responsibility has been assigned .');
						$this->redirect(array('action' => 'index'));
					}
				} else {
					$error = $this->User->UserDormAssignment->invalidFields();
					$string = '';

					foreach ($error['error'] as $kk => $kv) {
						$string .= '' . $kv;
					}

					if (isset($error['error'])) {
						$this->Flash->error($string);
					}
				}
			} else {
				$this->Flash->error('The assignment could not be saved. Check atleast one dorm block for assignment.');
			}
		}

		if ($id) {
			$staff_basic_data = $this->User->find('first', array(
				'conditions' => array('User.id' => $id),
				'contain' => array('Role', 'UserMealAssignment', 'Staff' => array('Position', 'Title'))
			));
			$alreadyAssignedBlocks = $this->User->UserDormAssignment->dormitoryBlocksAssignmentOrganizedByCampus($id);
		} else if (!empty($this->request->data['User']['id'])) {
			$staff_basic_data = $this->User->find('first', array(
				'conditions' => array('User.id' => $this->request->data['User']['id']),
				'contain' => array('Role', 'UserMealAssignment', 'Staff' => array('Position', 'Title'))
			));
			$alreadyAssignedBlocks = $this->User->UserDormAssignment->dormitoryBlocksAssignmentOrganizedByCampus($id);
		}

		$dormitoryBlocks = $this->User->UserDormAssignment->DormitoryBlock->getDormitoryBlock();

		$this->set(compact('dormitoryBlocks'));
		$this->set(compact('staff_basic_data', 'alreadyAssignedBlocks'));
	}


	function department_create_user_account($staff_id = null)
	{

		if (!empty($this->request->data) && isset($this->request->data['createAccount'])) {
			//debug($this->request->data);
			$staff_detail = $this->User->Staff->find('first', array('conditions' => array('Staff.id' => $this->request->data['Staff'][0]['id'])));

			$this->request->data['User']['first_name'] = ucwords(trim($staff_detail['Staff']['first_name']));
			$this->request->data['User']['last_name'] = ucwords(trim($staff_detail['Staff']['last_name']));
			$this->request->data['User']['middle_name'] = ucwords(trim($staff_detail['Staff']['middle_name']));

			if (!empty($staff_detail['Staff']['email'])) {
				$this->request->data['User']['email'] = strtolower(trim($staff_detail['Staff']['email']));
				$this->request->data['Staff'][0]['email'] = strtolower(trim($staff_detail['Staff']['email']));
			} else {
				$this->Flash->error('Email is required fro new account creation. Please check email is provided for the selected user.');
				$this->redirect(array('action' => 'department_create_user_account'));
			}

			if (empty($staff_detail['Staff']['gender'])) {
				$this->request->data['Staff'][0]['gender'] = 'male';
			} else {
				$this->request->data['Staff'][0]['gender'] = $staff_detail['Staff']['gender'];
			}

			$password = $this->User->generatePassword(5);
			$this->request->data['User']['passwd'] = $password;

			//debug($this->request->data);
			if ($this->User->saveAll($this->request->data, array('validate' => 'first'))) {
				$staff_details = $this->User->find('first', array('conditions' => array('User.id' => $this->User->id), 'contain' => array('Staff' => array('Department', 'College' => array('Campus'), 'Title'))));
				$university = ClassRegistry::init('University')->find('first', array('contain' => array('Attachment' => array('order' => array('Attachment.created DESC'))), 'order' => array('University.created DESC')));
				if (!empty($staff_details) && !empty($university)) {
					$this->set(compact('staff_details', 'university', 'password'));
					$this->response->type('application/pdf');
					$this->layout = '/pdf/default';
					$this->render('issue_password_staff_pdf');
				} else {
					$this->Flash->success('The user has been saved. Username=' . $staff_details['User']['username'] . ' and Password=' . $staff_details['User']['password'] . '');
					//$this->redirect(array('action' => 'index'));
				}
			} else {
				
				$error = $this->User->invalidFields();

				if (isset($error['Staff'][0]['phone_mobile'][0]) && !empty($error['Staff'][0]['phone_mobile'][0])) {
					$this->Flash->error('The user could not be saved. ' . $error['Staff'][0]['phone_mobile'][0]);
				} else {
					$this->Flash->error('The user could not be saved. Please, Check Staff Profile for data integrity and try again.');
				}
				
				$staff_id = $this->request->data['Staff'][0]['id'];
			}
		}

		if (!empty($staff_id) && !isset($this->request->data['createAccount'])) {

			$is_staff_belongs_to_ur_dept = $this->User->Staff->find('count', array(
				'conditions' => array(
					'Staff.id' => $staff_id,
					'Staff.department_id' => $this->department_id,
					//'Staff.user_id not in (select id from users)',
					/* 'OR' => array(
						'Staff.user_id is null', 'Staff.user_id = ""'
					) */
				)
			));

			if ($is_staff_belongs_to_ur_dept == 0 ) {
				//$this->Flash->error('User account for the selected staff is alreaddy created or you do not have privilage to create user account for the given staff.');
				$this->Flash->error('You do not have privilage to create user account for the given staff.');
			} else {
				//$staff_account_valid = true;

				$basic_data = $this->User->Staff->find('first', array('conditions' => array('Staff.id' => $staff_id), 'contain' => array('User' => array('Role'),'Position', 'Title')));

				if (isset($basic_data['Staff']['email']) && !empty($basic_data['Staff']['email'])) {
					$count_existing_users_using_provided_email = $this->User->find('count', array('conditions' => array('User.email' => strtolower(trim($basic_data['Staff']['email'])))));
				} else {
					$count_existing_users_using_provided_email = 0;
				}

				debug($count_existing_users_using_provided_email);

				if (!empty($basic_data['Staff']['user_id']) && $count_existing_users_using_provided_email == 1 ) {
					//debug($basic_data);
					$message= '<br/><p><ol><li> Full Name: ' . $basic_data['User']['full_name']. '<br/> Username: ' . $basic_data['User']['username'].'<br/> Role: '.$basic_data['User']['Role']['name'].'<br/> Active: '. ($basic_data['User']['active'] == 1 ?'Yes':'No') .'<br/> Last Login: ' . (($basic_data['User']['last_login'] == '0000-00-00 00:00:00' || $basic_data['User']['last_login'] == '' || is_null($basic_data['User']['last_login']))? 'Never Loggedin' : $basic_data['User']['last_login']) .'</li></ol></p>';
					$this->Session->setFlash(__('<span></span> &nbsp;&nbsp;<i>'. $basic_data['Staff']['full_name']. '</i> have the following existing user account: '.$message.''), 'default', array('class' => 'error-box error-message'));
					$this->redirect(array('action' => 'department_create_user_account'));

				} else if ($count_existing_users_using_provided_email > 1 ) {
					// check is the  provided email is not used by any user, if used, show the details in error message.
					// the provided email is used by another user or there is duplicate account for the selected user!
					$existing_users_using_this_email = $this->User->find('all', array('conditions' => array('User.email' => strtolower(trim($basic_data['Staff']['email']))),'fields' => array('Role.name','User.full_name', 'User.username', 'User.email', 'User.active', 'User.last_login'),'contain' => array('Role')));
					debug($existing_users_using_this_email);
	
					$message= '<br/><p><ol>';
	
					foreach ($existing_users_using_this_email as $existing_users) {
						$message .= '<li> Full Name: ' . $existing_users['User']['full_name']. '<br/> Username: ' . $existing_users['User']['username'].'<br/> Role: '.$existing_users['Role']['name'].'<br/> Active: '. ($existing_users['User']['active'] == 1 ?'Yes':'No') .'<br/> Last Login: ' . (($existing_users['User']['last_login'] == '0000-00-00 00:00:00' || $existing_users['User']['last_login'] == '' || is_null($existing_users['User']['last_login']))? 'Never Loggedin' : $existing_users['User']['last_login']) .'</li>';
					}
	
					$message .= '</ol></p>';
					$this->Session->setFlash(__('<span></span> &nbsp;&nbsp;The Email <i>'. strtolower(trim($basic_data['Staff']['email'])). '</i> is associated with the following existing users: '.$message.''), 'default', array('class' => 'error-box error-message'));
					$this->redirect(array('action' => 'department_create_user_account'));
					
				}

				$staff_account_valid = true;

				$staff_basic_data['Staff'][0] = $basic_data['Staff'];
				$staff_basic_data['Staff'][0]['Position'] = $basic_data['Position'];
				$staff_basic_data['Staff'][0]['Title'] = $basic_data['Title'];

				$recommeded_username = '';

				$check_existed_usename1 = $this->User->find('count', array('conditions' => array('User.username' => strtolower(trim($basic_data['Staff']['first_name'])).'.'. strtolower(trim($basic_data['Staff']['last_name'])))));
				$check_existed_usename2 = $this->User->find('count', array('conditions' => array('User.username' => strtolower(trim($basic_data['Staff']['first_name'])).'.'. strtolower(trim($basic_data['Staff']['middle_name'])))));

				$check_existed_usename3 = -1;

				if(!empty($basic_data['Staff']['email'])){
					$check_existed_usename3 = $this->User->find('count', array('conditions' => array('User.username' => strtolower(trim($basic_data['Staff']['email'])))));
				}

				if($check_existed_usename1 == 0){
					$recommeded_username = strtolower(trim($basic_data['Staff']['first_name'])).'.'. strtolower(trim($basic_data['Staff']['last_name']));
				} else if($check_existed_usename2 == 0){
					$recommeded_username = strtolower(trim($basic_data['Staff']['first_name'])).'.'. strtolower(trim($basic_data['Staff']['middle_name']));
				} else if($check_existed_usename3 == 0 && !empty($basic_data['Staff']['email'])){
					$recommeded_username = strtolower(trim($basic_data['Staff']['email']));
				} else {
					$recommeded_username = strtolower(trim($basic_data['Staff']['first_name'])).'.'. strtolower(trim($basic_data['Staff']['last_name'])).rand(1, 5);
				}

				debug($recommeded_username);
				$this->set(compact('staff_account_valid', 'staff_basic_data','recommeded_username'));
			}
		}

		if (!empty($this->request->data['Staff']['name']) && isset($this->request->data['search'])) {

			$staffs = $this->User->Staff->find('all', array(
				'conditions' => array(
					'Staff.department_id' => $this->department_id,
					'Staff.active' => 1,
					//'Staff.user_id not in (select id from users)',
					'OR' => array(
						'Staff.user_id is null', 'Staff.user_id = ""'
					),
					'OR' => array(
						'Staff.first_name LIKE ' => '%' . trim($this->request->data['Staff']['name']) . '%',
						'Staff.last_name LIKE ' => '%' . trim($this->request->data['Staff']['name']) . '%',
						'Staff.middle_name LIKE ' => '%' . trim($this->request->data['Staff']['name']) . '%',
						'Staff.email LIKE ' => '%' . trim($this->request->data['Staff']['name']) . '%',
					),
				),
				'contain' => array('College', 'Position', 'Department', 'Title'),
				//'recursive' => -1
			));

			if (isset($this->request->data) && !empty($this->request->data) && empty($staffs) && (!empty($this->request->data['Staff']['name']))) {
				$this->Flash->error('Based on your search, there is no staff in the system who does not have an account for system access from your department.');
			}
		} else {
			$staffs = $this->User->Staff->find('all', array(
				'conditions' => array(
					'Staff.department_id' => $this->department_id,
					'Staff.active' => 1,
					//'Staff.user_id not in (select id from users)',
					'OR' => array(
						'Staff.user_id is null', 'Staff.user_id = ""'
					)
				),
				'contain' => array('College', 'Position', 'Department', 'Title')
			));
		}


		if (empty($staffs)) {
			$this->Flash->error('Based on your search, there is no active staff from your department who doesn\'t have an account for system access.');
		}

		$conditions = array('Role.id' => array(ROLE_DEPARTMENT, ROLE_INSTRUCTOR));
		$roles = $this->User->Role->find('list', array('conditions' => $conditions));

		$this->set(compact('staffs', 'roles'));
	}

	function get_department($college_id = null)
	{
		$this->layout = 'ajax';

		if (!empty($college_id)) {
			$departments = $this->User->Staff->Department->find('list', array('conditions' => array('Department.college_id' => $college_id)));
		} else {
			$departments = array();
		}
		$this->set(compact('departments', 'college_id'));
	}

	function reset_password($role_id = null)
	{
		if (!empty($this->request->data)) {

			$role_id = $this->request->data['User']['role_id'];

			if (empty($this->request->data['User']['user_id'])) {
				$this->request->data['User']['passwd'] = null;
				$this->request->data['User']['password2'] = null;
				$this->Flash->error('Please select the user for whom you want to reset his/her password.');
			} else if (!empty($this->request->data['User']['password2']) && !empty($this->request->data['User']['passwd'])) {
				
				$passwd = $this->request->data['User']['passwd'];
				$passwd2 = $this->request->data['User']['password2'];

				if (strcmp($passwd, $passwd2) != 0) {
					$this->request->data['User']['passwd'] = null;
					$this->request->data['User']['password2'] = null;
					$this->Flash->error('Password change is failed. You entered two different passwords, please try again.');
				} else {
					
					$securitysetting = ClassRegistry::init('Securitysetting')->find('first');

					if (strlen($this->request->data['User']['passwd']) >= $securitysetting['Securitysetting']['minimum_password_length'] && strlen($this->request->data['User']['passwd']) <= $securitysetting['Securitysetting']['maximum_password_length']) {
						
						$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));

						$alread_requested = ClassRegistry::init('Vote')->find('count', array(
							'conditions' => array(
								'Vote.task' => 'Password Reset',
								'Vote.confirmation' => 0,
								'Vote.applicable_on_user_id' => $this->request->data['User']['user_id'],
								'Vote.created >= ' => $valid_date_from
							)
						));

						if ($alread_requested > 0) {
							$this->request->data['User']['passwd'] = null;
							$this->request->data['User']['password2'] = null;
							$this->Flash->error('There is already password reset request for the selected user. The request has to be either canceled or expired in-order to place password reset request again.');
						} else {

							$vote = array();
							$vote['task'] = 'Password Reset';
							$vote['requester_user_id'] = $this->Auth->user('id');
							$vote['applicable_on_user_id'] = $this->request->data['User']['user_id'];
							$vote['data'] = Security::hash($this->request->data['User']['passwd'], null, true);

							if (ClassRegistry::init('Vote')->save($vote)) {
								$this->request->data['User']['passwd'] = null;
								$this->request->data['User']['password2'] = null;
								$this->Flash->success('Password reset request is sent to other system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
								$this->redirect(array('action' => 'task_confirmation'));
							} else {
								$this->Flash->error('Password reset request is failed. Please, try again.');
							}
						}
					} else {
						$this->request->data['User']['passwd'] = null;
						$this->request->data['User']['password2'] = null;
						$this->Flash->error('Password policy: Your password should be greater than or equal to ' . $securitysetting['Securitysetting']['minimum_password_length'] . ' and less than or equal to ' . $securitysetting['Securitysetting']['maximum_password_length'] . '');
					}
				}
			} else {
				$this->request->data['User']['passwd'] = null;
				$this->request->data['User']['password2'] = null;
				$this->Flash->error('Password change failed. You did not provided the password with its confirmation.');
			}
		}
		//End of isset($this->request->data)

		$roles = ClassRegistry::init('Role')->find('list', array('conditions' => array('Role.id NOT IN ('.ROLE_STUDENT.')')));

		$roles = array(0 => '[ Select Role ]') + $roles;
		$role_ids = array_keys($roles);
		$users = array();

		if ($role_id && in_array($role_id, $role_ids)) {
			$options = array(
				'conditions' => array(
					'User.role_id' => $role_id,
					'Staff.active' => 1,
					'Staff.user_id NOT LIKE "' . $this->Auth->user('id') . '"',
					'User.active' => 1,
				),
				'fields' => array(
					'Staff.full_name',
					'Staff.college_id',
					'Staff.department_id',
					'User.username',
					'User.id'
				),
				'contain' => array(
					'User',
					'Department' => array('id', 'name'),
					'College' => array('id', 'name')
				)
			);

			if ($role_id == ROLE_INSTRUCTOR || $role_id == ROLE_SYSADMIN || $role_id == ROLE_GENERAL ||  $role_id == ROLE_CLEARANCE) {
				$options['conditions']['User.role_id'] = $role_id;
			} else {
				$options['conditions']['User.is_admin'] = 1;
				$options['conditions']['User.role_id'] = $role_id;
			}

			$users = ClassRegistry::init('Staff')->find('all', $options);
			//debug($users);

			$users_f = array();
			
			if ($role_id == ROLE_COLLEGE) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$collegeName = ClassRegistry::init('College')->field('College.name', array('College.id' => $user['Staff']['college_id']));
						if (!isset($users_f[$collegeName])) {
							$users_f[$collegeName] = array();
						}
						$users_f[$collegeName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else if ($role_id == ROLE_INSTRUCTOR || $role_id == ROLE_DEPARTMENT) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$departmentName = ClassRegistry::init('Department')->field('Department.name', array('Department.id' => $user['Staff']['department_id']));
						if (!isset($users_f[$departmentName])) {
							$users_f[$departmentName] = array();
						}
						$users_f[$departmentName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')  ' . $user['College']['name'] . ' ' . $user['Department']['name'] . '';
					}
					$users = $users_f;
				}
			} else {
				if (!empty($users)) {
					foreach ($users as $user) {
						$users_f[$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')  ' . $user['College']['name'] . ' ' . $user['Department']['name'] . '';
					}
					$users = $users_f;
				}
			}

			if (!empty($users)){
				$users = array(0 => '[ Select a user ]') + $users;
			} else {
				$users = array(0 => '[ No user found ]') + $users;
			}

		} else {
			$users = array(0 => '[ Select Role First ]') + $users;
		}

		$this->set(compact('roles', 'users', 'role_id'));
	}

	function cancel_main_account_administrator($role_id = null)
	{
		if (!empty($this->request->data)) {

			$role_id = $this->request->data['User']['role_id'];
			
			if (empty($this->request->data['User']['user_id'])) {
				$this->Flash->error('Please select the user from whom you want to cancel the main account administration privilage.');
			} else {
				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));

				$alread_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Administrator Cancellation',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $this->request->data['User']['user_id'],
						'Vote.created >= ' => $valid_date_from
					)
				));

				if ($alread_requested > 0) {
					$this->Flash->error('There is already "Administrator Cancellation" request for the selected user. The request has to be either canceled or expired in-order to place administrator cancellation request again.');
				} else {
					$vote = array();
					$vote['task'] = 'Administrator Cancellation';
					$vote['requester_user_id'] = $this->Auth->user('id');
					$vote['applicable_on_user_id'] = $this->request->data['User']['user_id'];
					$vote['data'] = 'Administrator Cancellation';

					if (ClassRegistry::init('Vote')->save($vote)) {
						$this->Flash->success('Main account administrator cancellation request is sent to other system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						$this->redirect(array('action' => 'task_confirmation'));
					} else {
						$this->Flash->error('Main account administrator cancellation request is failed. Please, try again.');
					}
				}
			}
		}

		//End of isset($this->request->data)
		$roles = ClassRegistry::init('Role')->find('list', array(
			'conditions' => array(
				'Role.id NOT ' => array(ROLE_STUDENT, ROLE_SYSADMIN, ROLE_INSTRUCTOR, ROLE_GENERAL, ROLE_CLEARANCE, ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM)
			)
		));

		$roles = array(0 => '[ Select Role ]') + $roles;
		$role_ids = array_keys($roles);
		$users = array();

		if ($role_id && in_array($role_id, $role_ids)) {
			$options = array(
				'conditions' => array(
					'User.role_id' => $role_id,
					'Staff.user_id NOT LIKE "' . $this->Auth->user('id') . '"',
					'User.is_admin' => 1,
					//'User.active' => 1,
				),
				'fields' => array(
					'Staff.full_name',
					'Staff.college_id',
					'Staff.department_id',
					'User.username',
					'User.id'
				),
				'contain' => array(
					'User'
				)
			);

			$users = ClassRegistry::init('Staff')->find('all', $options);
			$users_f = array();

			if ($role_id == ROLE_COLLEGE) {
				$colleges = ClassRegistry::init('College')->find('list');
				$college_ids = array_keys($colleges);
				if (!empty($users)) {
					foreach ($users as $user) {
						if (!isset($users_f[$colleges[$user['Staff']['college_id']]])) {
							$users_f[$colleges[$user['Staff']['college_id']]] = array();
						}
						$users_f[$colleges[$user['Staff']['college_id']]][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else if ($role_id == ROLE_DEPARTMENT) {
				$departments = ClassRegistry::init('Department')->find('list');
				$department_ids = array_keys($departments);
				if (!empty($users)) {
					foreach ($users as $user) {
						if (!isset($users_f[$departments[$user['Staff']['department_id']]])) {
							$users_f[$departments[$user['Staff']['department_id']]] = array();
						}
						$users_f[$departments[$user['Staff']['department_id']]][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else {
				if (!empty($users)) {
					foreach ($users as $user) {
						$users_f[$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			}

			if (!empty($users)) {
				$users = array(0 => '[ Select user ]') + $users;
			} else {
				$users = array(0 => '[ No administrator found ]') + $users;
			}

		} else {
			$users = array(0 => '[ Select Role First ]') + $users;
		}

		$this->set(compact('roles', 'users', 'role_id'));
	}

	function task_confirmation()
	{
		if($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN){
			$task_confirmation_request_status = ClassRegistry::init('Vote')->getListOfMyTaskForConfirmation($this->Auth->user('id'));
			$confirmed_tasks = ClassRegistry::init('Vote')->getListOfConfirmedTasks($this->Auth->user('id'));
			$this->set(compact('task_confirmation_request_status', 'confirmed_tasks'));
		} else {
			$tasks_for_confirmation = ClassRegistry::init('Vote')->getListOfTaskForConfirmation($this->Auth->user('id'));
			$task_confirmation_request_status = ClassRegistry::init('Vote')->getListOfMyTaskForConfirmation($this->Auth->user('id'));
			$confirmed_tasks = ClassRegistry::init('Vote')->getListOfConfirmedTasks($this->Auth->user('id'));
			$other_admin_tasks = ClassRegistry::init('Vote')->getListOfOtherAdminTasks($this->Auth->user('id'));
			$this->set(compact('tasks_for_confirmation', 'task_confirmation_request_status', 'confirmed_tasks', 'other_admin_tasks'));
		}
	}

	function cancel_task_confirmation($vote_id = null)
	{
		if (!empty($vote_id)) {
			
			$vote = $this->User->TaskRequester->find('first', array(
				'conditions' => array(
					'TaskRequester.id' => $vote_id
				),
				'recursive' => -1
			));

			//debug($vote);
			if (isset($vote['TaskRequester']['requester_user_id']) && strcasecmp($this->Session->read('Auth.User')['id'], $vote['TaskRequester']['requester_user_id']) == 0) {
				
				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 7, date("Y")));

				if (isset($vote['TaskRequester']['confirmation']) && $vote['TaskRequester']['confirmation'] == 0) {
					if ($valid_date_from < $vote['TaskRequester']['created']) {
						if ($this->User->TaskRequester->delete($vote_id)) {
							$this->Flash->success('Task for confirmation is successfully canceled.');
						} else {
							$this->Flash->error('Task for confirmation cancellation is failed. Please try again.');
						}
					} else {
						$this->Flash->error('Task confirmation request is already expired and there is no need to cancel it.');
					}
				} else {
					$this->Flash->error('Task confirmation request is already confirmed and there is no need to cancel it.');
				}
			} else {
				$this->Flash->error('You are trying to cancel others task confirmation request which is illegal.');
			}
		}

		return $this->redirect(array('action' => 'task_confirmation'));
	}

	function confirm_task($vote_id = null)
	{
		if (!empty($vote_id)) {

			$vote = $this->User->TaskRequester->find('first', array(
				'conditions' => array(
					'TaskRequester.id' => $vote_id
				),
				'recursive' => -1
			));

			if (strcasecmp($this->Session->read('Auth.User')['id'], $vote['TaskRequester']['requester_user_id']) != 0) {

				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));
				//debug($vote);debug($valid_date_from);exit();
				
				if ($vote['TaskRequester']['confirmation'] == 0) {
					if ($valid_date_from < $vote['TaskRequester']['created']) {

						if (strcasecmp($vote['TaskRequester']['task'], 'Password Reset') == 0) {
							$user_reset['id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$user_reset['force_password_change'] = 2;
							$user_reset['last_password_change_date'] = date('Y-m-d H:i:s');
							$user_reset['password'] = $vote['TaskRequester']['data'];
							
							$userH = $this->User->find('first', array(
								'conditions' =>array(
									'User.id' => $vote['TaskRequester']['applicable_on_user_id']
								)
							));

							$passwordHistory['user_id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$passwordHistory['password'] = $userH['User']['password'];
							$this->User->PasswordHistory->save($passwordHistory);

						} else if (strcasecmp($vote['TaskRequester']['task'], 'Role Change') == 0) {
							$user_reset['id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$user_reset['role_id'] = $vote['TaskRequester']['data'];
						} else if (strcasecmp($vote['TaskRequester']['task'], 'Account Deactivation') == 0) {
							$user_reset['id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$user_reset['active'] = 0;
						} else if (strcasecmp($vote['TaskRequester']['task'], 'Account Activation') == 0) {
							$user_reset['id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$user_reset['active'] = 1;
						} else if (strcasecmp($vote['TaskRequester']['task'], 'Administrator Cancellation') == 0) {
							$user_reset['id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$user_reset['is_admin'] = 0;
							$aro_id = ClassRegistry::init('Aro')->field('id', array('model' => 'User', 'foreign_key' => $vote['TaskRequester']['applicable_on_user_id']));
							$permission = ClassRegistry::init('Permission')->deleteAll(array(
								'Permission.aro_id' => $aro_id
							));
						} else if (strcasecmp($vote['TaskRequester']['task'], 'Administrator Assignment') == 0) {
							/* Check if there is already administrator */
							$selected_user = $this->User->find('first', array(
								'conditions' => array(
									'User.id' => $vote['TaskRequester']['applicable_on_user_id']
								),
								'contain' => array(
									'Staff'
								)
							));

							$options = array(
								'conditions' =>
								array(
									'User.is_admin' => 1,
									'User.role_id' => $selected_user['User']['role_id'],
								),
								'contain' => array(
									'College',
									'Department',
									'User'
								)
							);

							if ($selected_user['User']['role_id'] == ROLE_COLLEGE) {
								$options['conditions']['Staff.college_id'] = $selected_user['Staff'][0]['college_id'];
							}
							if ($selected_user['User']['role_id'] == ROLE_DEPARTMENT) {
								$options['conditions']['Staff.department_id'] = $selected_user['Staff'][0]['department_id'];
							}

							$is_there_admin = $this->User->Staff->find('first', $options);

							if (!empty($is_there_admin)) {
								$office = "";
								if ($selected_user['User']['role_id'] == ROLE_MEAL) {
									$office = "Meal service";
								}
								if ($selected_user['User']['role_id'] == ROLE_ACCOMODATION) {
									$office = "Accommodation service";
								}
								if ($selected_user['User']['role_id'] == ROLE_HEALTH) {
									$office = "Health service";
								}
								if ($selected_user['User']['role_id'] == ROLE_REGISTRAR) {
									$office = "Office of the Registrar";
								}
								if ($selected_user['User']['role_id'] == ROLE_DEPARTMENT) {
									$office = $is_there_admin['Department']['name'];
								}
								if ($selected_user['User']['role_id'] == ROLE_COLLEGE) {
									$office = $is_there_admin['College']['name'];
								}
								$this->Flash->error($office . ' already has ' . $is_there_admin['Staff']['first_name'] . ' ' . $is_there_admin['Staff']['middle_name'] . ' ' . $is_there_admin['Staff']['last_name'] . ' (' . $is_there_admin['User']['username'] . ') as an administrator. Please cancel the already assigned administrator before you confirm the new assignment.');
								return $this->redirect(array('action' => 'task_confirmation'));
							}

							$user_reset['id'] = $vote['TaskRequester']['applicable_on_user_id'];
							$user_reset['is_admin'] = 1;
						}

						if ($this->User->save($user_reset)) {

							$vote_update['id'] = $vote_id;
							$vote_update['confirmation'] = 1;
							$vote_update['confirmation_date'] = date('Y-m-d H:i:s');
							$vote_update['confirmed_by'] = $this->Auth->user('id');

							if ($this->User->TaskRequester->save($vote_update)) {

								$vote = $this->User->TaskRequester->find('first', array(
									'conditions' => array(
										'TaskRequester.id' => $vote_id
									),
									'contain' => array(
										'Requester' => array(
											'first_name',
											'last_name',
											'middle_name',
											'username'
										),
										'ApplicableOn' => array(
											'first_name',
											'last_name',
											'middle_name',
											'username',
											'role_id',
											'Staff' => array(
												'Department',
												'College'
											)
										),
										'ConfirmedBy' => array(
											'first_name',
											'last_name',
											'middle_name',
											'username'
										),
									)
								));

								//debug($vote);exit();
								if (strcasecmp($vote['TaskRequester']['task'], 'Password Reset') == 0) {
									$message = 'Your password reset request for <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u> is confirmed by <u>' . $vote['ConfirmedBy']['first_name'] . ' ' . $vote['ConfirmedBy']['middle_name'] . ' ' . $vote['ConfirmedBy']['last_name'] . ' (' . $vote['ConfirmedBy']['username'] . ')</u> and password change applied. Please communicate the new password to <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u>.';
								} else if (strcasecmp($vote['TaskRequester']['task'], 'Role Change') == 0) {
									$message = 'Your role change request for <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u> is confirmed by <u>' . $vote['ConfirmedBy']['first_name'] . ' ' . $vote['ConfirmedBy']['middle_name'] . ' ' . $vote['ConfirmedBy']['last_name'] . ' (' . $vote['ConfirmedBy']['username'] . ')</u> and role change is done.';
								} else if (strcasecmp($vote['TaskRequester']['task'], 'Account Deactivation') == 0) {
									
									$message = 'Your user account deactivation request for <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u> is confirmed by <u>' . $vote['ConfirmedBy']['first_name'] . ' ' . $vote['ConfirmedBy']['middle_name'] . ' ' . $vote['ConfirmedBy']['last_name'] . ' (' . $vote['ConfirmedBy']['username'] . ')</u> and account is deactivated.';
									
									$staffProfile = ClassRegistry::init('Staff')->find('first', array(
										'conditions' => array(
											'Staff.user_id' => $vote['TaskRequester']['applicable_on_user_id']
										),
										'contain' => array(
											'User'
										),
										'fields' => array('Staff.id','Staff.user_id','Staff.active', 'User.id','User.username','User.active')
									));

									if ($staffProfile) {

										$staffProfile['Staff']['active'] = 0;

										if (ClassRegistry::init('Staff')->save($staffProfile['Staff'], array('validate' => false))) {
											$message .= ' Associated staff profile is also deactivated';
										} else {
											$message .= ' But, unable to deactivate associated staff profile, Manual update required.';
										}
									} else {
										$message .= ' But, unable to find associated staff profile.';
									}

								} else if (strcasecmp($vote['TaskRequester']['task'], 'Account Activation') == 0) {
									
									$message = 'Your user account activation request for <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u> is confirmed by <u>' . $vote['ConfirmedBy']['first_name'] . ' ' . $vote['ConfirmedBy']['middle_name'] . ' ' . $vote['ConfirmedBy']['last_name'] . ' (' . $vote['ConfirmedBy']['username'] . ')</u> and account is activated.';

									$staffProfile = ClassRegistry::init('Staff')->find('first', array(
										'conditions' => array(
											'Staff.user_id' => $vote['TaskRequester']['applicable_on_user_id']
										),
										'contain' => array(
											'User'
										),
										'fields' => array('Staff.id','Staff.user_id','Staff.active', 'User.id','User.username','User.active')
									));

									if ($staffProfile) {

										$staffProfile['Staff']['active'] = 1;

										if (ClassRegistry::init('Staff')->save($staffProfile['Staff'], array('validate' => false))) {
											$message .= ' Associated staff profile is also activated';
										} else {
											$message .= ' But, unable to activate associated staff profile, Manual update required.';
										}
									} else {
										$message .= ' But, unable to find associated staff profile.';
									}
									
								} else if (strcasecmp($vote['TaskRequester']['task'], 'Administrator Cancellation') == 0) {
									$message = 'Your administrator cancellation request for <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u> is confirmed by <u>' . $vote['ConfirmedBy']['first_name'] . ' ' . $vote['ConfirmedBy']['middle_name'] . ' ' . $vote['ConfirmedBy']['last_name'] . ' (' . $vote['ConfirmedBy']['username'] . ')</u> and administrator cancellation is done.';
								} else if (strcasecmp($vote['TaskRequester']['task'], 'Administrator Assignment') == 0) {
									$office = "";
									if ($vote['ApplicableOn']['role_id'] == ROLE_MEAL) {
										$office = "Meal service";
									}
									if ($vote['ApplicableOn']['role_id'] == ROLE_ACCOMODATION) {
										$office = "Accommodation service";
									}
									if ($vote['ApplicableOn']['role_id'] == ROLE_HEALTH) {
										$office = "Health service";
									}
									if ($vote['ApplicableOn']['role_id'] == ROLE_REGISTRAR) {
										$office = "Office of the Registrar";
									}
									if ($vote['ApplicableOn']['role_id'] == ROLE_DEPARTMENT) {
										$office = $vote['ApplicableOn']['Staff'][0]['Department']['name'];
									}
									if ($vote['ApplicableOn']['role_id'] == ROLE_COLLEGE) {
										$office = $vote['ApplicableOn']['Staff'][0]['College']['name'];
									}
									$message = 'Your administrator assignment request for <u>' . $vote['ApplicableOn']['first_name'] . ' ' . $vote['ApplicableOn']['middle_name'] . ' ' . $vote['ApplicableOn']['last_name'] . ' (' . $vote['ApplicableOn']['username'] . ')</u> is confirmed by <u>' . $vote['ConfirmedBy']['first_name'] . ' ' . $vote['ConfirmedBy']['middle_name'] . ' ' . $vote['ConfirmedBy']['last_name'] . ' (' . $vote['ConfirmedBy']['username'] . ')</u> and the user becomes an administrator for ' . $office . '. Please let the assigned person know the new assignment.';
								}
								ClassRegistry::init('AutoMessage')->sendMessage($vote['TaskRequester']['requester_user_id'], $message, 1);
								$this->Flash->success('Task is successfully confirmed and applied.');
							} else {
								$this->Flash->error(' Task confirmation is failed. Please try again.');
							}
						} else {
							$this->Flash->error('Task confirmation failed. Please try again.');
						}
					} else {
						$this->Flash->error('Task confirmation request is expired to confirm.');
					}
				} else {
					$this->Flash->error('Task confirmation request is already confirmed.');
				}
			} else {
				$this->Flash->error('You can not confirm your own request. Please inform other system administrator to confirm your task request.');
			}
		}
		return $this->redirect(array('action' => 'task_confirmation'));
	}

	function assign_main_account_administrator($role_id = null)
	{
		if (!empty($this->request->data)) {

			$role_id = $this->request->data['User']['role_id'];

			if (empty($this->request->data['User']['user_id'])) {
				$this->Flash->error('Please select the user that you want to assign as an administrator.');
			} else {
				/*
          		1. Check if there is another assignment request
          		2. Check if there is already an administrator for the selected role
          		3. Check if there is already role change request
          		*/
				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));
				$alread_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Administrator Assignment',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $this->request->data['User']['user_id'],
						'Vote.created >= ' => $valid_date_from
					)
				));

				$there_is_role_change_alread_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Role Change',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $this->request->data['User']['user_id'],
						'Vote.created >= ' => $valid_date_from
					)
				));

				$selected_user = $this->User->find('first', array(
					'conditions' => array(
						'User.id' => $this->request->data['User']['user_id']
					),
					'contain' => array(
						'Staff'
					)
				));

				$options = array(
					'conditions' => array(
						'User.is_admin' => 1,
						'User.role_id' => $role_id,
					),
					'contain' => array(
						'College',
						'Department',
						'User'
					)
				);

				if ($role_id == ROLE_COLLEGE) {
					$options['conditions']['Staff.college_id'] = $selected_user['Staff'][0]['college_id'];
				}
				if ($role_id == ROLE_DEPARTMENT) {
					$options['conditions']['Staff.department_id'] = $selected_user['Staff'][0]['department_id'];
				}

				$is_there_admin = $this->User->Staff->find('first', $options);

				if (!empty($is_there_admin)) {
					$office = "";
					if ($role_id == ROLE_MEAL) {
						$office = "Meal service";
					}
					if ($role_id == ROLE_ACCOMODATION) {
						$office = "Accommodation service";
					}
					if ($role_id == ROLE_HEALTH) {
						$office = "Health service";
					}
					if ($role_id == ROLE_REGISTRAR) {
						$office = "Office of the Registrar";
					}
					if ($role_id == ROLE_DEPARTMENT) {
						$office = $is_there_admin['Department']['name'];
					}
					if ($role_id == ROLE_COLLEGE) {
						$office = $is_there_admin['College']['name'];
					}
					$this->Flash->error($office . ' already has ' . $is_there_admin['Staff']['first_name'] . ' ' . $is_there_admin['Staff']['middle_name'] . ' ' . $is_there_admin['Staff']['last_name'] . ' (' . $is_there_admin['User']['username'] . ') as an administrator. Please cancel the already assigned administrator before you make a new assignment.');
				} else if ($alread_requested > 0) {
					$this->Flash->error('There is already "Administrator Assignment" request for the selected user. The request has to be either canceled or expired in-order to place administrator assignment request again.');
				} else if ($there_is_role_change_alread_requested > 0) {
					$this->Flash->error('There is already "Role Change" request for the selected user. The role change request has to be either canceled or expired in-order to place administrator assignment request.');
				} else {
					$vote = array();
					$vote['task'] = 'Administrator Assignment';
					$vote['requester_user_id'] = $this->Auth->user('id');
					$vote['applicable_on_user_id'] = $this->request->data['User']['user_id'];
					$vote['data'] = 'Administrator Assignment';

					if (ClassRegistry::init('Vote')->save($vote)) {
						$this->Flash->success('Main account administrator assignment request is sent to other system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						$this->redirect(array('action' => 'task_confirmation'));
					} else {
						$this->Flash->error('Main account administrator assignment request is failed. Please, try again.');
					}
				}
			}
		}
		//End of isset($this->request->data)

		$roles = ClassRegistry::init('Role')->find('list', array(
			'conditions' => array(
				'Role.id NOT ' => array(ROLE_STUDENT, ROLE_SYSADMIN, ROLE_INSTRUCTOR, ROLE_GENERAL, ROLE_CLEARANCE, ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM)
			)
		));

		$roles = array(0 => '[ Select Role ]') + $roles;
		$role_ids = array_keys($roles);
		$users = array();

		if ($role_id && in_array($role_id, $role_ids)) {
			
			$options = array(
				'conditions' => array(
					'User.role_id' => $role_id,
					'User.is_admin' => 0,
					'User.active' => 1,
				),
				'fields' => array(
					'Staff.full_name',
					'Staff.college_id',
					'Staff.department_id',
					'User.username',
					'User.id'
				),
				'contain' => array(
					'User'
				)
			);

			$users = ClassRegistry::init('Staff')->find('all', $options);
			//debug($users);
			$users_f = array();

			if ($role_id == ROLE_COLLEGE) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$collegeName = ClassRegistry::init('College')->field('College.name', array('College.id' => $user['Staff']['college_id']));
						if (!isset($users_f[$collegeName])) {
							$users_f[$collegeName] = array();
						}
						$users_f[$collegeName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else if ($role_id == ROLE_DEPARTMENT) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$departmentName = ClassRegistry::init('Department')->field('Department.name', array('Department.id' => $user['Staff']['department_id']));
						if (!isset($users_f[$departmentName])) {
							$users_f[$departmentName] = array();
						}
						$users_f[$departmentName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else {
				if (!empty($users)) {
					foreach ($users as $user) {
						$users_f[$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			}

			if (!empty($users)) {
				$users = array(0 => '[ Select user ]') + $users;
			} else {
				$users = array(0 => '[ No user found ]') + $users;
			}

		} else {
			$users = array(0 => '[ Select Role First ]') + $users;
		}

		$this->set(compact('roles', 'users', 'role_id'));
	}

	function change_user_role($role_id = null)
	{
		/*
		Possible if 
		1. The user is not an administrator by his/her current role
		2. To department if the user has department id
		3. To college if the user has college id
		4. There is no administrator assignment process
		5. There is no already on process role change
		*/
		if (!empty($this->request->data)) {
			$role_id = $this->request->data['User']['role_id'];
			if (empty($this->request->data['User']['user_id'])) {
				$this->Flash->error('Please select the user for whom you want to change his/her role.');
			} else {
				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));
				
				$already_assignment_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Administrator Assignment',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $this->request->data['User']['user_id'],
						'Vote.created >= ' => $valid_date_from
					)
				));

				$already_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Role Change',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $this->request->data['User']['user_id'],
						'Vote.created >= ' => $valid_date_from
					)
				));

				$user_detail = $this->User->find('first', array(
					'conditions' => array(
						'User.id' => $this->request->data['User']['user_id']
					),
					'recursive' => -1
				));

				if ($user_detail['User']['is_admin'] == 1) {
					$this->Flash->error('The selected user is an administrator. You need to cancel his/her administrator privilege in order to change his/her role.');
				} else if ($already_assignment_requested > 0) {
					$this->Flash->error('There is already administrator assignment request for the selected user. The request has to be either canceled or expired in-order to place role change request.');
				} else if ($already_requested > 0) {
					$this->Flash->error('There is already a role change request for the selected user. The request has to be either canceled or expired in-order to place another role change request.');
				} else if (empty($this->request->data['User']['new_role_id'])) {
					$this->Flash->error('Please select user new role.');
				} else if ($user_detail['User']['role_id'] == $this->request->data['User']['new_role_id']) {
					$this->Flash->error('Please select a different role the user is supposed to has.');
				} else {

					$vote = array();
					$vote['task'] = 'Role Change';
					$vote['requester_user_id'] = $this->Auth->user('id');
					$vote['data'] = $this->request->data['User']['new_role_id'];
					$vote['applicable_on_user_id'] = $this->request->data['User']['user_id'];

					if (ClassRegistry::init('Vote')->save($vote)) {
						$this->Flash->success('User role change request is sent to other system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						$this->redirect(array('action' => 'task_confirmation'));
					} else {
						$this->Flash->error('User role change request is failed. Please, try again.');
					}
				}
			}
		}

		//End of isset($this->request->data)
		$roles = ClassRegistry::init('Role')->find('list', array(
			'conditions' => array(
				'Role.id NOT ' => array(ROLE_STUDENT, ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM
				)
			)
		));

		$roles = array(0 => '[ Select Role ]') + $roles;
		$role_ids = array_keys($roles);
		$users = array();

		if ($role_id && in_array($role_id, $role_ids)) {
			$options = array(
				'conditions' => array(
					'User.role_id' => $role_id,
					'User.active' => 1,
				),
				'fields' => array(
					'Staff.full_name',
					'Staff.college_id',
					'Staff.department_id',
					'User.username',
					'User.id'
				),
				'contain' => array(
					'User'
				)
			);

			$users = ClassRegistry::init('Staff')->find('all', $options);
			$users_f = array();
			if ($role_id == ROLE_COLLEGE) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$collegeName = ClassRegistry::init('College')->field('College.name', array('College.id' => $user['Staff']['college_id']));
						if (!isset($users_f[$collegeName])) {
							$users_f[$collegeName] = array();
						}
						$users_f[$collegeName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_INSTRUCTOR) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$departmentName = ClassRegistry::init('Department')->field('Department.name', array('Department.id' => $user['Staff']['department_id']));
						if (!isset($users_f[$departmentName])) {
							$users_f[$departmentName] = array();
						}
						$users_f[$departmentName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			} else {
				if (!empty($users)) {
					foreach ($users as $user) {
						$users_f[$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}
			}

			if (!empty($users)) {
				$users = array(0 => '[ Select user ]') + $users;
			} else {
				$users = array(0 => '[ No user found ]') + $users;
			}

		} else {
			$users = array(0 => '[ Select Role First ]') + $users;
		}
		$this->set(compact('roles', 'users', 'role_id'));
	}

	function deactivate_account($id = '')
	{
		$selected_user_id = '';
		$role_id = '';

		if (!empty($id)) {
			if (strlen($id) < 3 && is_numeric($id)) {
				$role_id = $id;
			} else {
				$selected_user_id = $id;
			}
		}

		if (!empty($this->request->data) || !empty($id)) {

			//$role_id = $this->request->data['User']['role_id'];

			if (!empty($this->request->data['User']['user_id'])) {
				$selected_user_id = $this->request->data['User']['user_id'];
				debug($selected_user_id);
			}

			if (empty($selected_user_id) && is_numeric($id)) {
				//$this->Flash->error('Please select the user whose account is going to be deactivated.');
			} else {
				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));

				$already_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Account Deactivation',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $selected_user_id,
						'Vote.created >= ' => $valid_date_from
					)
				));

				if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {

					$user_detail = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $selected_user_id,
							'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						),
						'contain' => array(
							'Staff' => array(
								'conditions' => array(
									'Staff.department_id' => $this->department_id,
									'Staff.user_id' => $selected_user_id
								)
							)
						),
						'recursive' => -1
					));

				} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {

					$user_detail = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $this->request->data['User']['user_id'],
							'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						),
						'contain' => array(
							'Staff' => array(
								'conditions' => array(
									'Staff.college_id' => $this->college_id,
									'Staff.user_id' => $this->request->data['User']['user_id']
								)
							)
						),
						'recursive' => -1
					));
				} else {

					$user_detail = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $selected_user_id,
							'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						),
						'contain' => array(
							'Staff' => array(
								'conditions' => array(
									'Staff.user_id' => $selected_user_id
								)
							)
						),
						'recursive' => -1
					));
				}

				if ($this->Auth->user('role_id') != ROLE_SYSADMIN) {

					if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
						if ($user_detail['Staff'][0]['department_id'] != $this->department_id) {
							$this->Flash->error('You are trying to deactivate other department staff, your action is logged and reported to system Administrators.');
							$this->redirect(array('action' => 'index'));
						}
					} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {
						if ($user_detail['Staff'][0]['college_id'] != $this->college_id) {
							$this->Flash->error('You are trying to deactivate other college staff, your action is logged and reported to system Administrators.');
							$this->redirect(array('action' => 'index'));
						}
					} else {
						if ($user_detail['User']['role_id'] != $this->Auth->user('role_id')) {
							$this->Flash->error('You are trying to deactivate other staffs that are not in your control, your action is logged and reported to system Administrators.');
							$this->redirect(array('action' => 'index'));
						}
					}

				}

				if ($user_detail['User']['active'] == 0) {
					$this->Flash->error('The selected user account is already deactivated.');
				} else if ($already_requested > 0) {
					$this->Flash->error('There is already account deactivation request for the selected user. The request has to be either canceled or expired in-order to place another request.');
				} else {

					$vote = array();
					$vote['task'] = 'Account Deactivation';
					$vote['requester_user_id'] = $this->Auth->user('id');
					$vote['applicable_on_user_id'] = $selected_user_id;
					$vote['data'] = 'Account Deactivation';

					if (ClassRegistry::init('Vote')->save($vote)) {

						if ($this->Auth->user('role_id') != ROLE_SYSADMIN) {
							$this->Flash->success('Your Staff\'s user account deactivation request is sent to system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						} else {
							$this->Flash->success('User account deactivation request is sent to other system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						}

						$this->redirect(array('action' => 'task_confirmation'));

					} else {
						$this->Flash->error('User account deactivation request is failed. Please, try again.');
					}
				}
			}
		}
		//End of isset($this->request->data)

		if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id ' => array(ROLE_DEPARTMENT, ROLE_INSTRUCTOR)
				)
			));

		} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id ' => array(ROLE_COLLEGE, ROLE_INSTRUCTOR)
				)
			));

		} else if ($this->Auth->user('role_id') == ROLE_SYSADMIN) {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id NOT ' => ROLE_STUDENT
				)
			));

		} else {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id' => $this->Auth->user('role_id')
				)
			));

		}

		$roles = array(0 => '[ Select Role ]') + $roles;
		$role_ids = array_keys($roles);
		$users = array();

		if ($role_id && in_array($role_id, $role_ids)) {

			if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {

				$options = array(
					'conditions' => array(
						'User.role_id' => $role_id,
						'User.active' => 1,
						'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						'Staff.department_id' => $this->department_id,
					),
					'fields' => array(
						'Staff.full_name',
						'Staff.college_id',
						'Staff.department_id',
						'User.username',
						'User.id'
					),
					'contain' => array(
						'User'
					)
				);

			} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {

				$options = array(
					'conditions' => array(
						'User.role_id' => $role_id,
						'User.active' => 1,
						'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						'Staff.college_id' => $this->college_id,
					),
					'fields' => array(
						'Staff.full_name',
						'Staff.college_id',
						'Staff.department_id',
						'User.username',
						'User.id'
					),
					'contain' => array(
						'User'
					)
				);

			} else {

				$options = array(
					'conditions' => array(
						'User.role_id' => $role_id,
						'User.active' => 1,
						'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
					),
					'fields' => array(
						'Staff.full_name',
						'Staff.college_id',
						'Staff.department_id',
						'User.username',
						'User.id'
					),
					'contain' => array(
						'User'
					)
				);

			}

			$users = ClassRegistry::init('Staff')->find('all', $options);
			debug(count($users));
			$users_f = array();

			if ($role_id == ROLE_COLLEGE) {
				if (!empty($users)) {
					foreach ($users as $user) {
						$collegeName = ClassRegistry::init('College')->field('College.name', array('College.id' => $user['Staff']['college_id']));
						if (!isset($users_f[$collegeName])) {
							$users_f[$collegeName] = array();
						}
						$users_f[$collegeName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}

			} else if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_INSTRUCTOR) {

				$departments = ClassRegistry::init('Department')->find('list');

				if (!empty($users)) {
					foreach ($users as $user) {
						$departmentName = ClassRegistry::init('Department')->field('Department.name', array('Department.id' => $user['Staff']['department_id']));
						if (!isset($users_f[$departments[$user['Staff']['department_id']]])) {

							$users_f[$departments[$user['Staff']['department_id']]] = array();
						}
						$users_f[$departments[$user['Staff']['department_id']]][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ' => ' . $departmentName . ')';
					}
					$users = $users_f;
				}

			} else {

				if (!empty($users)) {
					foreach ($users as $user) {
						$users_f[$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}

			}

			if (!empty($users)) {
				$users = array(0 => '[ Select user ]') + $users;
			} else {
				$users = array(0 => 'No active user found ]') + $users;
			}

		} else {
			$users = array(0 => '[ Select Role First ]') + $users;
		}

		$this->set(compact('roles', 'users', 'role_id'));
		
	}

	function activate_account($id = '')
	{
		$selected_user_id = '';
		$role_id = '';

		if (!empty($id)) {
			if (strlen($id) < 3 && is_numeric($id)) {
				$role_id = $id;
			} else {
				$selected_user_id = $id;
			}
		}

		if (!empty($this->request->data) || !empty($id)) {

			//$role_id = $this->request->data['User']['role_id'];

			if (!empty($this->request->data['User']['user_id'])) {
				$selected_user_id = $this->request->data['User']['user_id'];
				debug($selected_user_id);
			}

			if (empty($selected_user_id) && is_numeric($id)) {
				//$this->Flash->error('Please select the user whose account is going to be activated.');
			} else {
				$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - 3, date("Y")));

				$already_requested = ClassRegistry::init('Vote')->find('count', array(
					'conditions' => array(
						'Vote.task' => 'Account Activation',
						'Vote.confirmation' => 0,
						'Vote.applicable_on_user_id' => $selected_user_id,
						'Vote.created >= ' => $valid_date_from
					)
				));

				if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {

					$user_detail = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $selected_user_id,
							'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						),
						'contain' => array(
							'Staff' => array(
								'conditions' => array(
									'Staff.department_id' => $this->department_id,
									'Staff.user_id' => $selected_user_id
								)
							)
						),
						'recursive' => -1
					));

				} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {

					$user_detail = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $this->request->data['User']['user_id'],
							'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						),
						'contain' => array(
							'Staff' => array(
								'conditions' => array(
									'Staff.college_id' => $this->college_id,
									'Staff.user_id' => $this->request->data['User']['user_id']
								)
							)
						),
						'recursive' => -1
					));
				} else {

					$user_detail = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $selected_user_id,
							'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						),
						'contain' => array(
							'Staff' => array(
								'conditions' => array(
									'Staff.user_id' => $selected_user_id
								)
							)
						),
						'recursive' => -1
					));
				}

				if ($this->Auth->user('role_id') != ROLE_SYSADMIN) {

					if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
						if ($user_detail['Staff'][0]['department_id'] != $this->department_id) {
							$this->Flash->error('You are trying to activate other department staff, your action is logged and reported to system Administrators.');
							$this->redirect(array('action' => 'index'));
						}
					} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {
						if ($user_detail['Staff'][0]['college_id'] != $this->college_id) {
							$this->Flash->error('You are trying to activate other college staff, your action is logged and reported to system Administrators.');
							$this->redirect(array('action' => 'index'));
						}
					} else {
						if ($user_detail['User']['role_id'] != $this->Auth->user('role_id')) {
							$this->Flash->error('You are trying to activate other staffs that are not in your control, your action is logged and reported to system Administrators.');
							$this->redirect(array('action' => 'index'));
						}
					}

				}

				if ($user_detail['User']['active'] == 1) {
					$this->Flash->error('The selected user account is already activated.');
				} else if ($already_requested > 0) {
					$this->Flash->error('There is already account activation request for the selected user. The request has to be either canceled or expired in-order to place another request.');
				} else {

					$vote = array();
					$vote['task'] = 'Account Activation';
					$vote['requester_user_id'] = $this->Auth->user('id');
					$vote['applicable_on_user_id'] = $selected_user_id;
					$vote['data'] = 'Account Activation';

					if (ClassRegistry::init('Vote')->save($vote)) {

						if ($this->Auth->user('role_id') != ROLE_SYSADMIN) {
							$this->Flash->success('Your Staff\'s user account activation request is sent to system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						} else {
							$this->Flash->success('User account activation request is sent to other system administrators and it will be effective up on confirmation. You will be informed when your request is confirmed and applied.');
						}

						$this->redirect(array('action' => 'task_confirmation'));

					} else {
						$this->Flash->error('User account deactivation request is failed. Please, try again.');
					}
				}
			}
		}
		//End of isset($this->request->data)

		if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id ' => array(ROLE_DEPARTMENT, ROLE_INSTRUCTOR)
				)
			));

		} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id ' => array(ROLE_COLLEGE, ROLE_INSTRUCTOR)
				)
			));

		} else if ($this->Auth->user('role_id') == ROLE_SYSADMIN) {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id NOT ' => ROLE_STUDENT
				)
			));

		} else {

			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.id' => $this->Auth->user('role_id')
				)
			));

		}

		$roles = array(0 => '[ Select Role ]') + $roles;
		$role_ids = array_keys($roles);
		$users = array();

		if ($role_id && in_array($role_id, $role_ids)) {

			if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {

				$options = array(
					'conditions' => array(
						'User.role_id' => $role_id,
						'User.active' => 0,
						'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						'Staff.department_id' => $this->department_id,
					),
					'fields' => array(
						'Staff.full_name',
						'Staff.college_id',
						'Staff.department_id',
						'User.username',
						'User.id'
					),
					'contain' => array(
						'User'
					)
				);

			} else if ($this->Auth->user('role_id') == ROLE_COLLEGE) {

				$options = array(
					'conditions' => array(
						'User.role_id' => $role_id,
						'User.active' => 0,
						'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
						'Staff.college_id' => $this->college_id,
					),
					'fields' => array(
						'Staff.full_name',
						'Staff.college_id',
						'Staff.department_id',
						'User.username',
						'User.id'
					),
					'contain' => array(
						'User'
					)
				);

			} else {

				$options = array(
					'conditions' => array(
						'User.role_id' => $role_id,
						'User.active' => 0,
						'User.id NOT LIKE "' . $this->Auth->user('id') . '"',
					),
					'fields' => array(
						'Staff.full_name',
						'Staff.college_id',
						'Staff.department_id',
						'User.username',
						'User.id'
					),
					'contain' => array(
						'User'
					)
				);

			}

			$users = ClassRegistry::init('Staff')->find('all', $options);
			debug(count($users));
			$users_f = array();

			if ($role_id == ROLE_COLLEGE) {

				if (!empty($users)) {
					foreach ($users as $user) {
						$collegeName = ClassRegistry::init('College')->field('College.name', array('College.id' => $user['Staff']['college_id']));
						if (!isset($users_f[$collegeName])) {
							$users_f[$collegeName] = array();
						}
						$users_f[$collegeName][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}

			} else if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_INSTRUCTOR) {

				$departments = ClassRegistry::init('Department')->find('list');

				if (!empty($users)) {
					foreach ($users as $user) {
						$departmentName = ClassRegistry::init('Department')->field('Department.name', array('Department.id' => $user['Staff']['department_id']));
						if (!isset($users_f[$departments[$user['Staff']['department_id']]])) {

							$users_f[$departments[$user['Staff']['department_id']]] = array();
						}
						$users_f[$departments[$user['Staff']['department_id']]][$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ' => ' . $departmentName . ')';
					}
					$users = $users_f;
				}

			} else {

				if (!empty($users)) {
					foreach ($users as $user) {
						$users_f[$user['User']['id']] = $user['Staff']['full_name'] . ' (' . $user['User']['username'] . ')';
					}
					$users = $users_f;
				}

			}

			if (!empty($users)) {
				$users = array(0 => '[ Select user ]') + $users;
			} else {
				$users = array(0 => '[ No deactivated user found ]') + $users;
			}

		} else {
			$users = array(0 => '[ Select Role First ]') + $users;
		}
		
		$this->set(compact('roles', 'users', 'role_id'));
		
	}

	function build_user_menu($user_id = null)
	{
		if (!isset($this->Session->read('Auth.User')['id'])) {
			$this->Session->destroy();
			return $this->redirect($this->Auth->logout());
		}
		//It is used to ignore recorded number of process which are older than 1 hour to avoid stacked processes

		$last_process_date = date('Y-m-d H:i:s', mktime(date("H"), date("i") - 20, date("s"), date("n"), date("j"), date("Y")));
		
		$numberProcess = $this->User->NumberProcess->find('count', array(
			'conditions' => array(
				'NumberProcess.created > ' => $last_process_date
			)
		));

		$number_of_user_initiated_process = $this->User->NumberProcess->find('count', array(
			'conditions' => array(
				'NumberProcess.created > ' => $last_process_date,
				'NumberProcess.initiated_by' => $this->Auth->user('id')
			)
		));

		//One administrator is allowed to run a maximum of one menu building task
	        
		if ($number_of_user_initiated_process <= 0) {
			if ($numberProcess < Configure::read('NumberProcessAllowedToRunProfile')) {
				$saveRunningProcess = $this->User->NumberProcess->recoredAsRunning($user_id, $this->Auth->user('id'));
				$runningusers = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'recursive' => -1));
				
				// Construct the menus From the Controllers in the Application. This is an  expensive Process Timewise and is cached.
				$this->Session->delete('permissionLists');
				// clear menu cache if existed 
				$this->_clearMenuCatch($user_id);
				//$this->Menu->clearCache();
				// $this->Menu->constructMenu($runningusers);
				$this->MenuOptimized->constructMenu($user_id);
				$this->User->NumberProcess->jobDoneDelete($user_id);
				$this->Flash->success('The system build the selected user menu successfully based on assigned user privilege.');
			} else {
				$this->Flash->info('The system is busy handling user menu construct requests. Please come back after some minutes to construct user menu.');
			}
		} else {
			$this->Flash->info('You already has menu construction request being handled by the system. Please be patent till the system finish the requested menu construction task to initiate another menu construction request.');
		}
		$this->redirect(array('action' => 'index'));
	}

	function suspended($userId)
	{
		$userDetails = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
		$this->set(compact('userDetails'));
	}

	function _clearMenuCatch($user_id = null)
	{
		App::import('Folder');

		$dir = new Folder(Configure::read('Utility.cache'));
		$files = $dir->findRecursive('menu_storageuser' . $user_id . '.*');

		if (!empty($files)) {
			foreach ($files as $in => $file) {
				$output = shell_exec('rm ' . $file . " 2>&1");
			}
		}
	}
}