<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

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

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        // Load Auth only once
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email', // OR 'username' depending on your login field
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authError' => 'You are not authorized to access that page.',
            'storage' => 'Session'
        ]);
        $this->loadComponent('Flash');


        /*
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login',
                'plugin' => false
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            'authError' => 'You do not have permission to access this page.'
        ]);
        */
    }

    protected function _findIp()
    {
        return $this->request->clientIp();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $session = $this->request->getSession();

        if ($session->check('Auth.User')) {
            $auth = $session->read('Auth.User');

            if (!empty($auth)) {
                $this->set('username', $auth['username']);
                $this->set('last_login', $auth['last_login']);
                $this->set('user_id', $auth['id']);

                $userTable = TableRegistry::getTableLocator()->get('Users');
                $permissionLists = $userTable->getAllPermissions($auth['id']);

                Configure::write('permissionLists', $permissionLists['permission']);
                Configure::write('PermissionLists.Perm', $permissionLists['permission']);
                Configure::write('reformatePermission', $permissionLists['reformatePermission']);

                $session->write('permissionLists', $permissionLists['permission']);
                $session->write('reformatePermission', $permissionLists['reformatePermission']);
                $session->write('role_id', $auth['role_id']);
                $this->role_id = $auth['role_id'];

                Configure::write('User.user', $auth['id']);
                Configure::write('User.role_id', $auth['role_id']);
                Configure::write('User.is_admin', $auth['is_admin']);
                Configure::write('User.active', $auth['active']);

                $this->set('user_full_name', $auth['full_name']);
                $session->write('user_id', $auth['id']);

                $userDetail = $session->read('users_relation') ?? $userTable->getUserDetails($auth['id']);

                if ($userDetail) {
                    $session->write('users_relation', $userDetail);
                } else {
                    $this->Flash->error('There is a conflicting session, please login again.');
                    return $this->redirect($this->Auth->logout());
                }

                if (!empty($userDetail['Staff'][0])) {
                    $this->staff_id = $userDetail['Staff'][0]['id'];

                    if (!empty($userDetail['Role'])) {
                        $this->role_id = $userDetail['Role']['id'];
                        $this->role_name = $userDetail['Role']['name'];
                        $this->set('role_id', $this->role_id);
                        $this->set('role_name', $this->role_name);
                    }

                    if (!empty($userDetail['Staff'][0]['college_id'])) {
                        $this->college_id = $userDetail['Staff'][0]['college_id'];
                        $this->set('college_id', $this->college_id);
                    }

                    if (!empty($userDetail['Staff'][0]['department_id'])) {
                        $this->department_id = $userDetail['Staff'][0]['department_id'];
                        $this->set('department_id', $this->department_id);
                    }

                    if (!empty($userDetail['Staff'][0]['College']['name'])) {
                        $this->college_name = $userDetail['Staff'][0]['College']['name'];
                        $this->set('college_name', $this->college_name);
                    }

                    if (!empty($userDetail['Staff'][0]['Department']['name'])) {
                        $this->department_name = $userDetail['Staff'][0]['Department']['name'];
                        $this->set('department_name', $this->department_name);
                    }
                } else if (!empty($userDetail['Student'][0])) {
                    $this->student_id = $userDetail['Student'][0]['id'];
                    $this->college_id = $userDetail['Student'][0]['college_id'];
                    $this->department_id = $userDetail['Student'][0]['department_id'];
                    $this->program_id = $userDetail['Student'][0]['program_id'];
                    $this->program_type_id = $userDetail['Student'][0]['program_type_id'];

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
    }
}
