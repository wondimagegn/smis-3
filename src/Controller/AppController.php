<?php
//App::uses('Controller', 'Controller');
class AppController extends Controller {

	// use DataTableRequestHandlerTrait;
	//public $theme = "CakeAdminLTE";

    public $cacheAction = true;

	public $components = array(
		'Acl', 'Session', 'Paginator', 'MenuOptimized', 'RequestHandler', 'Flash', /* 'DataTable', */
		'Auth' => array(
			'authorize' => array(
				'Actions' => array('actionPath' => 'controllers')
			),
			/* 'loginAction' => array(
				'controller' => 'users',
				'action' => 'login',
				//'plugin' => 'users'
			), */
			'authError' => 'You do not have permission to access the page you just selected',
			/* 'authenticate' => array(
				'Form' => array(
					'fields' => array(
					  'username' => 'username', 
					  'password' => 'password'
					)
				)
			) */
		),
	);

	public $persistModel = true; // performance

	public $helpers = array(
		'Js' => 'Jquery',
		'AssetCompress.AssetCompress',
		'Html',
		'Form',
		'Session',
		'Format',
		'Link',
		'Flash',
		'Csv'
	);

	public $college_id = null,
	$department_id = null,
	$role_id = null,
	$role_name = null,
	$college_name = null,
	$department_name = null,
	$student_id = null,
	$program_id = null,
	$program_type_id = null,
	$staff_id = null;

	public $programs = array(),
	$programs_list = array(),
	$program_types = array(),
	$program_types_list = array(),
	$departments = array(),
	$last_section  = array(),
	$year_levels  = array(),
	$departments_list = array();

	// Completed list of assignment for accounts created from the main account holder of registrar

    public $college_ids = array(), 
	$department_ids = array(), 
	$program_ids = array(), 
	$program_type_ids = array(), 
	$onlyPre = 0;


    function _findIp() 
	{
		if (!empty(getenv("HTTP_CLIENT_IP"))) {
			return getenv("HTTP_CLIENT_IP");
		} else if (!empty(getenv("HTTP_X_FORWARDED_FOR"))) {
			return getenv("HTTP_X_FORWARDED_FOR");
		} else {
			return getenv("REMOTE_ADDR");
		}
    }

    function beforeFilter() 
	{
        parent::beforeFilter();

		//$this->Auth->autoRedirect = false;
		//$this->Auth->userScope = array('User.active '=> 1);

		//$this->Auth->authError = __('<div class="warning-box warning-message"><span></span>You do not have permission to access the page you just selected.</div>');

		//Configure AuthComponent
		$prohibited_roles_to_access_this_host = array();

		if (gethostname() != 'KELI' && gethostname() != 'mistest' && gethostname() != 'smis') {
			debug(gethostname());
			debug($_SERVER['SERVER_NAME']);
			//$prohibited_roles_to_access_this_host = array(ROLE_STUDENT => ROLE_STUDENT/* , ROLE_INSTRUCTOR => ROLE_INSTRUCTOR */);
		}

		$this->Auth->loginAction = array(
			'controller' => 'users',
			'action' => 'login',
			'plugin' => false,
			'admin' => false
		);

		$this->Auth->logoutRedirect = array(
			'controller' => 'users',
			'action' => 'login'
		);

		$this->Auth->loginRedirect = array(
			'controller' => 'dashboard',
			'action' => 'index'
		);

		$auth = null;

		if ($this->Session->check('Auth.User')) {

			$auth = $this->Session->read('Auth.User');
			
			if (isset($auth) && !empty($auth)) {  

				if (!empty($prohibited_roles_to_access_this_host) && in_array($auth['role_id'], $prohibited_roles_to_access_this_host)) {
					$this->Flash->warning('You are not allowed to access this server ('.$_SERVER['SERVER_NAME'].'). Please use ' . PORTAL_URL_HTTPS . ' to access your account.');
					return $this->redirect($this->Auth->logout());
					//return $this->redirect(PORTAL_URL_HTTPS);
				}

				//debug(preg_replace('/[^-\.@_a-z0-9]/', '', strtolower($auth['username'])));
		
				$this->set('username', $auth['username']);
				$this->set('last_login', $auth['last_login']);
				$this->set('user_id', $auth['id']);
				$this->set('auto_messages', ClassRegistry::init('AutoMessage')->getMessages($auth['id']));
			
				//generate menu based on user privilage and save it to session 

				if (($auth['id'] && !$this->Session->read('permissionLists'))) {
					$aroKey = $auth;

					$permissionLists = ClassRegistry::init('User')->getAllPermissions($auth['id']);
				
					Configure::write('permissionLists', $permissionLists['permission']);
					Configure::write('PermissionLists.Perm', $permissionLists['permission']);
					Configure::write('reformatePermission', $permissionLists['reformatePermission']);

					$this->Session->write('permissionLists', $permissionLists['permission']);
					$this->Session->write('reformatePermission',$permissionLists['reformatePermission']);      
				}

				//save to the session the role of the user 
				$this->Session->write('role_id', $auth['role_id']);
				$this->role_id = $auth['role_id'];
			
				Configure::write('User.user', $auth['id']);
				Configure::write('User.role_id', $auth['role_id']);
				Configure::write('User.is_admin', $auth['is_admin']);
				Configure::write('User.active', $auth['active']);

				$this->set('user_full_name', $auth['full_name']);
				$this->Session->write('user_id', $auth['id']);

				//only query if the user details if not found in the session
				/* if (!$this->Session->read('users_relation')) {
					$this->Session->write('users_relation', ClassRegistry::init('User')->getUserDetails($auth['id']));
				} */

				//$userDetail = $this->Session->read('users_relation');

				if ($this->Session->check('users_relation')) {
					if ($auth['id'] === $this->Session->read('users_relation')['User']['id']) {
						$userDetail = $this->Session->read('users_relation');
					} else {
						$this->Session->destroy();
						$userDetail = array();
						$this->Flash->error('There is a conflicting session, Please close all open browser tabs that uses '.$_SERVER['SERVER_NAME'].' and login again.');
						return $this->redirect($this->Auth->logout());
					}
				} else {
					$userDetail = array();
					$this->Session->write('users_relation', ClassRegistry::init('User')->getUserDetails($auth['id']));
					$userDetail = $this->Session->read('users_relation');
				}

				// 1. Basic varibles are set to be visible by all controller of the application.
				// 2. To access the variable in any controller, use $this->variblename. Dont
				// 3. Dont forget to call parent::beforeFilter in your controller beforeFilter action, then all variable set in app controller will be used.
				// 4. To access it from view, just write $variablename

				/* if( $auth['id'] !== $this->Session->read('users_relation')['User']['id']){
					$this->Session->destroy();
					$this->Flash->error('There is a conflicting session, Please close all open browser tabs that uses '.$_SERVER['SERVER_NAME'].' and login again.');
					return $this->redirect($this->Auth->logout());
				} */

				//debug($userDetail);
					
				if (!empty($userDetail['Staff'][0])) {

					$this->staff_id = $userDetail['Staff'][0]['id'];

					if (isset($userDetail['Role']) && !empty($userDetail['Role'])) {
						$this->role_id = $userDetail['Role']['id'];
						$this->rolename = $userDetail['Role']['name'];
						$this->set('role_id', $userDetail['Role']['id']);
						$this->set('role_name', $userDetail['Role']['name']);
					}
					
					

					if (isset($userDetail['Staff'][0]['college_id']) && !empty($userDetail['Staff'][0]['college_id']) && isset($userDetail['Staff'][0]['department_id']) && !empty($userDetail['Staff'][0]['department_id'])) {

						$this->set('college_id', $userDetail['Staff'][0]['college_id']);
						$this->set('department_id', $userDetail['Staff'][0]['department_id']);
						$this->college_id = $userDetail['Staff'][0]['college_id'];
						$this->department_id = $userDetail['Staff'][0]['department_id'];

						if (isset($userDetail['Staff'][0]['College']) && !empty($userDetail['Staff'][0]['College']['name'])) {
							$this->set('college_name', $userDetail['Staff'][0]['College']['name']);
							$this->college_name = $userDetail['Staff'][0]['College']['name'];
						}

						if (isset($userDetail['Staff'][0]['Department']) && !empty($userDetail['Staff'][0]['Department']['name'])) {
							$this->set('department_name', $userDetail['Staff'][0]['Department']['name']);
							$this->department_name = $userDetail['Staff'][0]['Department']['name'];
						}

					} else if (isset($userDetail['Staff'][0]['college_id']) && !empty($userDetail['Staff'][0]['college_id'])) {

						$this->college_id = $userDetail['Staff'][0]['college_id'];
						$this->set('college_id', $userDetail['Staff'][0]['college_id']);

						if (isset($userDetail['Staff'][0]['College']) && !empty($userDetail['Staff'][0]['College']['name'])) {
							$this->set('college_name', $userDetail['Staff'][0]['College']['name']);
							$this->college_name = $userDetail['Staff'][0]['College']['name'];
						}
					}

					//debug($this->Session->read('Auth.User'));

					//registrar role

					/* if ($this->role_id == ROLE_REGISTRAR || $this->Session->read('Auth.User')['Role']['parent_id'] == ROLE_REGISTRAR) {
						if (isset($userDetail['StaffAssigne']['department_id']) && !empty($userDetail['StaffAssigne']['department_id'])) {
							$this->department_ids = unserialize($userDetail['StaffAssigne']['department_id']);
						} else if (isset($userDetail['StaffAssigne']['college_id']) && !empty($userDetail['StaffAssigne']['college_id'])) {
							$this->college_ids = unserialize($userDetail['StaffAssigne']['college_id']);
							$this->onlyPre = $userDetail['StaffAssigne']['collegepermission'];
						}

						if (!empty($userDetail['StaffAssigne']['program_id'])) {
							$this->program_ids = $this->program_id = unserialize($userDetail['StaffAssigne']['program_id']);
						}

						if (!empty($userDetail['StaffAssigne']['program_type_id'])) {
							$this->program_type_ids = $this->program_type_id = unserialize($userDetail['StaffAssigne']['program_type_id']);
						}
					} */

					//debug($userDetail['ApplicableAssignments']);

					$this->department_ids = $userDetail['ApplicableAssignments']['department_ids'];
					$this->college_ids = $userDetail['ApplicableAssignments']['college_ids'];
					$this->program_id = $this->program_ids = $userDetail['ApplicableAssignments']['program_ids'];
					$this->program_type_id = $this->program_type_ids = $userDetail['ApplicableAssignments']['program_type_ids'];
					$this->onlyPre = $userDetail['ApplicableAssignments']['college_permission'];
					$this->year_levels = $userDetail['ApplicableAssignments']['year_level_names'];

					if ($this->onlyPre == 1) {
						$this->department_ids = array();
					}

				} else if (!empty($userDetail['Student'][0]) && $this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
					
					$this->student_id = $userDetail['Student'][0]['id'];

					$this->set('college_id', $userDetail['Student'][0]['college_id']);
					$this->set('department_id', $userDetail['Student'][0]['department_id']);                     
					$this->college_id = $userDetail['Student'][0]['college_id'];                     
					$this->department_id = $userDetail['Student'][0]['department_id'];
					$this->program_id = $userDetail['Student'][0]['program_id'];
					$this->program_type_id = $userDetail['Student'][0]['program_type_id'];

					$this->set('program_id', $userDetail['Student'][0]['program_id']);
					$this->set('program_type_id', $userDetail['Student'][0]['program_type_id']);
					
					$this->last_section = $userDetail['ApplicableAssignments']['last_section'];

					if (isset($userDetail['Role']) && !empty($userDetail['Role'])) {
						$this->role_id = $userDetail['Role']['id'];
						$this->rolename = $userDetail['Role']['name'];
						$this->set('role_id', $userDetail['Role']['id']);
						$this->set('role_name', $userDetail['Role']['name']);
					}

					if (isset($userDetail['Student'][0]['College']) && !empty($userDetail['Student'][0]['College']['name'])) {
						$this->set('college_name', $userDetail['Student'][0]['College']['name']);
						$this->college_name = $userDetail['Student'][0]['College']['name'];
					}

					if (isset($userDetail['Student'][0]['Department']) && !empty($userDetail['Student'][0]['Department']['name'])) {
						$this->set('department_name', $userDetail['Student'][0]['Department']['name']);
						$this->department_name = $userDetail['Student'][0]['Department']['name'];
					}

					$this->department_ids = $userDetail['ApplicableAssignments']['department_ids'];
					$this->college_ids = $userDetail['ApplicableAssignments']['college_ids'];
					$this->program_ids = $userDetail['ApplicableAssignments']['program_ids'];
					$this->program_type_ids = $userDetail['ApplicableAssignments']['program_type_ids'];
					$this->onlyPre = $userDetail['ApplicableAssignments']['college_permission'];
					$this->year_levels = $userDetail['ApplicableAssignments']['year_level_names'];

					if ($this->onlyPre == 1) {
						$this->department_ids = array();
						$this->department_id = null;
					}

					
				} else if (!empty($userDetail['ApplicableAssignments'])) {
					
					$this->department_ids = (isset($userDetail['ApplicableAssignments']['department_ids']) && !empty($userDetail['ApplicableAssignments']['department_ids']) ? $userDetail['ApplicableAssignments']['department_ids'] : array());
					$this->college_ids = (isset($userDetail['ApplicableAssignments']['college_ids']) && !empty($userDetail['ApplicableAssignments']['college_ids']) ? $userDetail['ApplicableAssignments']['college_ids'] : array());
					$this->program_ids = (isset($userDetail['ApplicableAssignments']['program_ids']) && !empty($userDetail['ApplicableAssignments']['program_ids']) ? $userDetail['ApplicableAssignments']['program_ids'] : array());
					$this->program_type_ids = (isset($userDetail['ApplicableAssignments']['program_type_ids']) && !empty($userDetail['ApplicableAssignments']['program_type_ids']) ? $userDetail['ApplicableAssignments']['program_type_ids'] : array());
					$this->onlyPre = $userDetail['ApplicableAssignments']['college_permission'];
					$this->year_levels = (isset($userDetail['ApplicableAssignments']['year_level_names']) && !empty($userDetail['ApplicableAssignments']['year_level_names']) ? $userDetail['ApplicableAssignments']['year_level_names'] : array());

					if ($this->onlyPre == 1) {
						$this->department_ids = array();
					}
					
					if (isset($userDetail['Role']) && !empty($userDetail['Role'])) {
						$this->role_id = $userDetail['Role']['id'];
						$this->rolename = $userDetail['Role']['name'];
						$this->set('role_id', $userDetail['Role']['id']);
						$this->set('role_name', $userDetail['Role']['name']);
					}

				} else if (isset($userDetail['Role']) && !empty($userDetail['Role'])) {
					$this->role_id = $userDetail['Role']['id'];
					$this->rolename = $userDetail['Role']['name'];
					$this->set('role_id', $userDetail['Role']['id']);
					$this->set('role_name', $userDetail['Role']['name']);
				} else {

					if (count($this->uses) && $this->{$this->modelClass}->Behaviors->loaded('Logable')) {
						$activeUser = array('User' => array('id' => $auth['id'], 'username' => $auth['username']));
						$this->{$this->modelClass}->setUserData($activeUser);
						$this->{$this->modelClass}->setUserIp($this->modelClass, $this->_findIp()); 
					} 

					$this->Session->destroy();
					$userDetail = array();
					$this->Flash->error('There is a conflicting session, Please close all open browser tabs that uses '.$_SERVER['SERVER_NAME'].' and login again.');
					return $this->redirect($this->Auth->logout());

				}

				// merged to the last else in to if-else chain to prevent role rewrite , Neway
			
				// http://bakery.cakephp.org/articles/view/logablebehavior

				if (count($this->uses) && $this->{$this->modelClass}->Behaviors->loaded('Logable')) {

					$activeUser = array('User' => array('id' => $auth['id'], 'username' => $auth['username']));
							
					//$this->{$this->modelClass}->setUserData($this->modelClass,$activeUser);
					$this->{$this->modelClass}->setUserData($activeUser);
					$this->{$this->modelClass}->setUserIp($this->modelClass, $this->_findIp());
					/// needs some refinement for forwarded addresses if a load balancer is used.	 
				} 
			} else {
				// why we are logging not loggedin user ?? it increases the db size exponentially and not relevant. it  will also result in slow log table read time.
				if (count($this->uses) && $this->{$this->modelClass}->Behaviors->loaded('Logable')) {
					$this->{$this->modelClass}->setUserIp($this->modelClass, $this->_findIp());
				}
			}
		

			if (isset($auth) && !empty($auth)) {

				$user = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $auth['id']), 'recursive' => -1));

				unset($user['User']['password']);

				$first_time_login = 0;
				$password_duration_expired = false;
				$last_password_change_date = null;

				$email_verified = 0;
				$email_address = $user['User']['email'];
				$last_email_verified_date = null;
				$email_validation_expired = false;

				//Check if the user has to change his/her password
				$securitysetting = ClassRegistry::init('Securitysetting')->find('first');

				$password_to_change_date =  date('Y-m-d H:i:s', mktime (date('H'), date('i'), date('s'), date('n'), date('j') - $securitysetting['Securitysetting']['password_duration'], date('Y')));
				
				if (isset($user['User']['last_password_change_date']) && $password_to_change_date >  $user['User']['last_password_change_date']) {
					$password_duration_expired = true;
					$last_password_change_date = $user['User']['last_password_change_date'];
				}

				//Check if the user login is for the first time
				if (isset($user['User']['force_password_change']) && $user['User']['force_password_change'] == 1) {
					$first_time_login = 1;
				}
					
				if (isset($user['User']['force_password_change'])) {
					$this->set('force_password_change', $user['User']['force_password_change']);
				} else {
					$this->set('force_password_change', 0);
				}

				$this->set('first_time_login', $first_time_login);
				$this->set('password_duration_expired', $password_duration_expired);
				$this->set('last_password_change_date', $last_password_change_date);
				$this->set('password_duration', $securitysetting['Securitysetting']['password_duration']);

				if (isset($user['User']['id']) && !empty($user['User']['id']) && ($user['User']['force_password_change'] != 0 || $password_duration_expired) && strcasecmp($this->request->params['controller'], 'users') != 0  && strcasecmp($this->request->params['action'], 'changePwd') != 0) {
					return $this->redirect(array('controller' => 'users', 'action' => 'changePwd'));
				}

				if (FORCE_EMAIL_VERIFICATION && FORCE_EMAIL_VERIFICATION_AFTER_LOGIN) {
					if (!empty($user['User']['email'])) {
						$email_to_revalidate_date =  date('Y-m-d H:i:s', mktime (date('H'), date('i'), date('s'), date('n'), date('j') - DAYS_TO_ENFORCE_EMAIL_REVALIDATION, date('Y')));
						debug($email_to_revalidate_date);
						debug(DAYS_TO_ENFORCE_EMAIL_REVALIDATION);

						if (FORCE_EMAIL_VERIFICATION_FOR_ALL_ROLES == 0) {
							$roles_to_check = Configure::read('roles_for_email_verification');
							//debug($roles_to_check);
							if (in_array($auth['role_id'], $roles_to_check)) {
								if ($user['User']['email_verified'] == 0) {
									$email_validation_expired = true;
								} else if (isset($user['User']['last_email_verified_date']) && $email_to_revalidate_date >  $user['User']['last_email_verified_date']) {
									$email_validation_expired = true;
									$last_email_verified_date = $user['User']['last_email_verified_date'];
								} else if (!isset($user['User']['last_email_verified_date'])) {
									$email_validation_expired = true;
								} else {
									//$email_validation_expired = true;
								}
							}
						} else {
							if ($user['User']['email_verified'] == 0) {
								$email_validation_expired = true;
							} else if (isset($user['User']['last_email_verified_date']) && $email_to_revalidate_date >  $user['User']['last_email_verified_date']) {
								$email_validation_expired = true;
								$last_email_verified_date = $user['User']['last_email_verified_date'];
							} else if (!isset($user['User']['last_email_verified_date'])) {
								$email_validation_expired = true;
							} else {
								//$email_validation_expired = true;
							}
						}
					} else {
						$email_validation_expired = true;
					}

					if ($email_validation_expired) {
						if (empty($email_address) && isset($user['User']['id']) && !empty($user['User']['id']) && strcasecmp($this->request->params['controller'], 'users') != 0  && strcasecmp($this->request->params['action'], 'edit') != 0) {
							$this->Flash->info('Dear, ' . $user['User']['first_name']. ' you are required to have an email address inorder to use this platform, please provide a valid email address here and verify it.');
							return $this->redirect(array('controller' => 'users', 'action' => 'edit', $user['User']['id']));
						} else if (isset($user['User']['id']) && !empty($user['User']['id']) && strcasecmp($this->request->params['controller'], 'users') != 0  && strcasecmp($this->request->params['action'], 'edit') != 0) {
							$this->Flash->info('Dear, ' . $user['User']['first_name']. ' you are required to validate your email address every ' .DAYS_TO_ENFORCE_EMAIL_REVALIDATION . ' days to continue access to use this platform, Please verify your ' . $email_address . ' email address here.');
							return $this->redirect(array('controller' => 'users', 'action' => 'edit' , $user['User']['id']));
						}
					}
				}
				
				$studentnumber = null;

				if (!empty($this->request->data[$this->modelClass]['studentnumber']) || !empty($this->request->data[$this->modelClass]['studentID'])) {
					if (!empty($this->request->data[$this->modelClass]['studentnumber'])) {
						$studentnumber = $this->request->data[$this->modelClass]['studentnumber'];
					} else if(!empty($this->request->data[$this->modelClass]['studentID'])) {
						$studentnumber = $this->request->data[$this->modelClass]['studentID'];
					} 
				} else {
					if(!empty($this->request->data['Student']['studentnumber'])){
						$studentnumber = $this->request->data['Student']['studentnumber'];
					} else if(!empty($this->request->data['Student']['studentID'])) {
						$studentnumber = $this->request->data['Student']['studentID'];
					}
				}
					
				if ($studentnumber && (!in_array($auth['role_id'], array(ROLE_DEPARTMENT,ROLE_REGISTRAR))) && 0 ){
					
					$suspended = ClassRegistry::init('Student')->find('first', array(
						'conditions' => array(
							'Student.studentnumber' => $studentnumber,
							'Student.user_id in (select id from users where active=0)'
						), 'recursive' => -1
					));

					if ($suspended) {
						$this->set(compact('suspended'));
						return $this->redirect(array('controller' => 'users', 'action' => 'suspended', $suspended['Student']['user_id']));
					}
				}
					
				/* if ($this->role_id == ROLE_STUDENT &&  ){
					return $this->redirect(array('controller'=>'alumni','action' => "add"));
				} */
					
				/* if (isset($user['User']['id']) && !empty($user['User']['id']) 
					&& strcasecmp($this->request->params['controller'], 'alumni') != 0 
					&& strcasecmp($this->request->params['action'], 'add') != 0 
					&& $user['User']['role_id'] == ROLE_STUDENT
					&& ClassRegistry::init('Alumnus')->checkIfStudentGradutingClass($this->student_id) == true  
					&& ($user['User']['force_password_change'] != 0 || $password_duration_expired) 
					&& strcasecmp($this->request->params['controller'], 'users') != 0 
					&& strcasecmp($this->request->params['action'], 'changePwd') != 0 ) {
							
					if ( ClassRegistry::init('Alumnus')->completedRoundOneQuestionner($this->student_id) == false) {
						return $this->redirect(array('controller' => 'alumni', 'action' => 'add'));
					} 
							
				} */

				/* if ($auth && $this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) {

					$programs = ClassRegistry::init('Program')->find('list');
					$program_types = ClassRegistry::init('ProgramType')->find('list');

					$programs_list = array(0 => 'All Programs') + $programs;
					$program_types_list = array(0 => 'All Program Types') + $program_types;

					if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
						$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_id, array());
						$departments_list = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_id, array());
					} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
						$departments =  ClassRegistry::init('Department')->allDepartmentsByCollege2(1, array(), $this->college_id);
						$departments_list =  ClassRegistry::init('Department')->allDepartmentsByCollege2(1, array(), $this->college_id);
					} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
			
						if (isset($this->program_ids) && !empty($this->program_ids)) {
							$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids)));
							$programs_list = array(0 => 'All Assigned Programs') + $programs;
						} else {
							$programs = array();
							$programs_list = array();
						}
			
						if (isset($this->program_type_ids) && !empty($this->program_type_ids)) {
							$program_types = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids)));
							$program_types_list = array(0 => 'All Assigned Program Types') + $program_types;
						} else {
							$program_types = array();
							$program_types_list = array();
						}
			
						if (isset($this->college_ids) && !empty($this->college_ids)) {
							$departments =  ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.college_id' => $this->college_ids)));
							$departments_list = array(0 => 'All Assigned Departments') + $departments;
						} else {
							$departments =  array();
							$departments_list = array();
						}
			
						if (isset($this->department_ids) && !empty($this->department_ids)) {
							$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $this->department_ids)));
							$departments_list = array(0 => 'All Assigned Departments') + $departments;
						} else {
							$departments =  array();
							$departments_list = array();
						}

					} else {
						$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, $this->department_id, $this->college_id);
						$departments_list = array(0 => 'All University Students') + $departments;
					}

					//debug($departments);
					//debug($departments_list);
					//debug($programs);
					//debug($programs_list);
					//debug($program_types);
					//debug($program_types_list);
				} */
			}
		} else {
			//$this->Session->destroy();
			//$this->Flash->info('You are required to login.');
		}
    } 
}
