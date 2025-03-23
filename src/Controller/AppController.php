<?php

namespace App\Controller;

use App\Auth\LegacyPasswordHasher;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

// Import Inflector from the correct namespace

class AppController extends Controller
{
    public $college_id = null;
    public $department_id = null;
    public $role_id = null;
    public $role_name = null;
    public $college_name = null;
    public $department_name = null;
    public $student_id = null;
    public $program_id = null;
    public $program_type_id = null;
    public $staff_id = null;

    public $programs = [];
    public $programs_list = [];
    public $program_types = [];
    public $program_types_list = [];
    public $departments = [];
    public $last_section = [];
    public $year_levels = [];
    public $departments_list = [];

    public $college_ids = [];
    public $department_ids = [];
    public $program_ids = [];
    public $program_type_ids = [];
    public $onlyPre = 0;
    protected $allowedActions = []; // Store allowed actions dynamically
    public function initialize()
    {
        parent::initialize();
        /*
        $this->loadComponent('Acl', [
            'className' => 'Acl.Acl',


        ]);
        */

        $this->loadComponent('Acl', [
            'className' => 'App.CustomAcl'
        ]);

        // Load Auth only once
        $this->loadComponent('Auth', [

            'authorize' => ['Acl.Actions'],
            //'authorize' => [], // Disable ACL
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'username', // OR 'username' depending on your login field
                        'password' => 'password'
                    ],
                    'passwordHasher' => LegacyPasswordHasher::class // Use Legacy Hasher
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],


            'authError' => 'You do not have permission to access the page you just selected',
            'unauthorizedRedirect' => false, // ✅ Prevent redirect loop
            'storage' => 'Session',

            // 'unauthorizedRedirect' => $this->referer(),
            //  'authorize' => ['Controller'], // Optional for ACL

        ]);
        // Ensure user session is loaded
        $this->set('authUser', $this->Auth->user());

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('MenuOptimized');
    }

    protected function _findIp()
    {
        return $this->request->clientIp();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Store all allowed actions from child controllers
        if (!isset($this->allowedActions)) {
            $this->allowedActions = [];
        }

        // Retrieve actions that are explicitly allowed in the child controller
        $this->allowedActions = array_merge($this->allowedActions,
            $this->Auth->allowedActions);

        // ✅ Skip ACL Check for Publicly Allowed Actions (from any controller)
        if (in_array($this->request->getParam('action'), $this->allowedActions)) {

            return; // Skip ACL check, allow the action
        }



        $session = $this->request->getSession();
        if ($session->check('Auth.User')) {
            $auth = $session->read('Auth.User');
            if (!empty($auth)) {
                $userTable = TableRegistry::getTableLocator()->get('Users');
                $permissionLists = $userTable->getAllPermissions($auth['id']);

                $this->set('username', $auth['username']);
                $this->set('last_login', $auth['last_login']);
                $this->set('user_id', $auth['id']);


                if ($auth['id'] && !$session->read('permissionLists') || true) {

                    $permissionLists = $userTable->getAllPermissions($auth['id']);

                    Configure::write('permissionLists', $permissionLists['permission']);
                    Configure::write('PermissionLists.Perm', $permissionLists['permission']);
                    Configure::write('reformatePermission', $permissionLists['reformatePermission']);

                    $session->write('permissionLists', $permissionLists['permission']);
                    $session->write('reformatePermission', $permissionLists['reformatePermission']);
                }


                $session->write('role_id', $auth['role_id']);
                $this->role_id = $auth['role_id'];

                Configure::write('User.user', $auth['id']);
                Configure::write('User.role_id', $auth['role_id']);
                Configure::write('User.is_admin', $auth['is_admin']);
                Configure::write('User.active', $auth['active']);

                $this->set('user_full_name', $auth['full_name']);
                $session->write('user_id', $auth['id']);

                $userDetail = $session->read('users_relation') ??
                    $userTable->getUserDetails($auth['id']);

                if (isset($userDetail) && !empty($userDetail)) {
                    $session->write('users_relation', $userDetail);
                } else {
                    $this->Flash->error('There is a conflicting session, please login again.');
                    return $this->redirect($this->Auth->logout());
                }

                if (!empty($userDetail['Staff'])) {
                    $this->staff_id = $userDetail['Staff']['id'];

                    if (!empty($userDetail['Role'])) {
                        $this->role_id = $userDetail['Role']['id'];
                        $this->role_name = $userDetail['Role']['name'];
                        $this->set('role_id', $this->role_id);
                        $this->set('role_name', $this->role_name);
                    }

                    if (!empty($userDetail['Staff']['college_id'])) {
                        $this->college_id = $userDetail['Staff']['college_id'];
                        $this->set('college_id', $this->college_id);
                    }

                    if (!empty($userDetail['Staff']['department_id'])) {
                        $this->department_id = $userDetail['Staff']['department_id'];
                        $this->set('department_id', $this->department_id);
                    }

                    if (!empty($userDetail['Staff']['College']['name'])) {
                        $this->college_name = $userDetail['Staff']['College']['name'];
                        $this->set('college_name', $this->college_name);
                    }

                    if (!empty($userDetail['Staff']['Department']['name'])) {
                        $this->department_name = $userDetail['Staff']['Department']['name'];
                        $this->set('department_name', $this->department_name);
                    }
                } elseif (!empty($userDetail['Student'])) {
                    $this->student_id = $userDetail['Student']['id'];
                    $this->college_id = $userDetail['Student']['college_id'];
                    $this->department_id = $userDetail['Student']['department_id'];
                    $this->program_id = $userDetail['Student']['program_id'];
                    $this->program_type_id = $userDetail['Student']['program_type_id'];

                    $this->set('college_id', $this->college_id);
                    $this->set('department_id', $this->department_id);
                    $this->set('program_id', $this->program_id);
                    $this->set('program_type_id', $this->program_type_id);
                }



                $this->department_ids = $userDetail['ApplicableAssignments']['department_ids'] ?? [];
                $this->college_ids = $userDetail['ApplicableAssignments']['college_ids'] ?? [];
                $this->program_ids = $userDetail['ApplicableAssignments']['program_ids'] ?? [];
                $this->program_type_ids = $userDetail['ApplicableAssignments']['program_type_ids'] ?? [];
                $this->onlyPre = $userDetail['ApplicableAssignments']['college_permission'] ?? 0;
                $this->year_levels = $userDetail['ApplicableAssignments']['year_level_names'] ?? [];



                if ($this->onlyPre == 1) {
                    $this->department_ids = [];
                    $this->department_id = null;
                }

                if ($this->role_id == ROLE_STUDENT) {
                    if (TableRegistry::getTableLocator()->get('Alumnus')->checkIfStudentGradutingClass($this->student_id)) {
                        return $this->redirect(['controller' => 'Alumni', 'action' => 'add']);
                    }
                }
            }
        }
        /*
        $user = $this->Auth->user();

        if (!empty($user) && isset($user['id'])) {

            // Store all allowed actions from child controllers
            if (!isset($this->allowedActions)) {
                $this->allowedActions = [];
            }

            // Retrieve actions that are explicitly allowed in the child controller
            $this->allowedActions = array_merge($this->allowedActions, $this->Auth->allowedActions);

            // ✅ Skip ACL Check for Publicly Allowed Actions (from any controller)
            if (in_array($this->request->getParam('action'), $this->allowedActions)) {
                return; // Skip ACL check, allow the action
            }

            $acoPath = "controllers/" . Inflector::camelize($this->name) .
                "/{$this->request->getParam('action')}";

            $permissionCheck = $this->Acl->check(['Roles' => ['id' => $user['role_id']]], $acoPath);
            // If no access for role, check by user
            if (!$permissionCheck) {
                $this->Flash->error(__('You are not authorized to access this page.'));
                //  return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                return $this->redirect('/');
            }

        }
        */
    }
}
