<?php
namespace App\Model\Table;
use Acl\Controller\Component\AclComponent;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use ArrayObject;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Network\Request;
use Detection\MobileDetect;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Http\ServerRequest;
use Cake\Datasource\EntityInterface; // Correct Interface for Entity

use Cake\Controller\ComponentRegistry;
use Cake\Acl;
use Cake\Utility\Inflector; // Import Inflector from the correct namespace




class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Announcements', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('AutoMessages', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Logs', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('MedicalHistories', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Messages', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('MoodleUsers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('NumberProcesses', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('OnlineUsers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('PasswordChanageVotes', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('PasswordHistories', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('PlacementPreferences', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('PreferenceDeadlines', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Preferences', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('StaffAssignes', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('UserDormAssignments', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('UserMealAssignments', [
            'foreignKey' => 'user_id',
        ]);



        //  $this->addBehavior('Acl.Acl', ['type' => 'requester']);

      //  $this->addBehavior('Acl.Acl', ['requester']);
        $this->addBehavior('Acl.Acl', ['type' => 'requester']);



    }


    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('username', 'Your username is required')
            ->add('username', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Username is already taken. Use another.'
            ])
            ->notEmptyString('password', 'Your password is required')
            ->email('email', false, 'Please enter a valid email address.')
            ->allowEmptyString('email')
            ->notEmptyString('role_id', 'Please select a user role.');

        return $validator;
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {

        // Only hash the password if it's a new one
        if ($entity->isDirty('password')) {
            $entity->password = (new DefaultPasswordHasher())->hash($entity->password);
        }
        return true;

    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));

        return $rules;
    }



    function elgibleToEdit($userId = null, $departmentCollegeIds = null)
    {
        $responsibility = $this->StaffAssigne->find()
            ->where(['StaffAssigne.user_id' => $userId])
            ->first();

        return $responsibility;
    }

    function getEmailsForRoles($role_ids = null)
    {
        $returnArray = array();

        if (!empty($role_ids) && is_array($role_ids)) {
            foreach ($role_ids as $role_id) {
                $tempArray = $this->getListOfUsers($role_id);
                if (!empty($tempArray) && is_array($tempArray)) {
                    $returnArray = $returnArray + $tempArray;
                }
            }
            return $returnArray;
        } else {
            return null;
        }
    }

    public function getListOfUsers($roleId = null, $message = null)
    {
        if (!$roleId) {
            return null;
        }

        $users = $this->find()
            ->select(['id', 'username', 'email'])
            ->where(['role_id' => $roleId, 'active' => 1])
            ->group(['username'])
            ->all();

        $returnArray = [];

        foreach ($users as $user) {
            if (!empty($user->email)) {
                $returnArray[$user->id] = $user->email;
            }
        }

        return $returnArray;
    }

    public function getListOfUsersRole($roleId = null, $message = null, $data = null)
    {
        if (!$roleId) {
            return null;
        }

        $returnArray = [];
        $count = 0;

        if ($roleId == ROLE_STUDENT) {
            $query = $this->Students->find()
                ->contain(['Users'])
                ->where([
                    'Users.role_id' => $roleId,
                    'Users.active' => 1,
                    'Students.graduated' => 0,
                ])
                ->where(function ($exp, $q) {
                    return $exp->in('Students.id',
                        $q->newExpr()->add('SELECT student_id FROM students_sections WHERE archive = 0')
                    );
                });

            if (!empty($data['department_ids'])) {
                $query->where(['Students.department_id IN' => $data['department_ids']]);
            }
            if (!empty($data['college_ids'])) {
                $query->where(['Students.college_id IN' => $data['college_ids']]);
            }
            if (!empty($data['program_id'])) {
                $query->where(['Students.program_id' => $data['program_id']]);
            }
            if (!empty($data['program_type_id'])) {
                $query->where(['Students.program_type_id' => $data['program_type_id']]);
            }

            $users = $query->all();
        } else {
            $query = $this->Staff->find()
                ->contain(['Users'])
                ->where([
                    'Users.role_id' => $roleId,
                    'Users.active' => 1,
                    'Staff.active' => 1,
                ]);

            if (!empty($data['department_ids'])) {
                $query->where(['Staff.department_id IN' => $data['department_ids']]);
            }
            if (!empty($data['college_ids'])) {
                $query->where(['Staff.college_id IN' => $data['college_ids']]);
            }

            $users = $query->all();
        }

        foreach ($users as $user) {
            $returnArray[$count]['user_id'] = $user->user->id;
            $returnArray[$count]['read'] = 0;
            $returnArray[$count]['message'] = $message;
            $count++;
        }

        return $returnArray;
    }


    public function getNameOfTheUser($userId = null)
    {
        if (!$userId) {
            return false;
        }

        $role = $this->find()
            ->select(['role_id'])
            ->where(['id' => $userId])
            ->first();

        if (!$role) {
            return false;
        }

        if ($role->role_id != ROLE_STUDENT) {
            $user = $this->Staff->find()
                ->select(['first_name', 'last_name'])
                ->contain(['Titles' => function ($q) {
                    return $q->select(['id', 'title']);
                }])
                ->where(['Staff.user_id' => $userId])
                ->order(['Staff.user_id' => 'DESC'])
                ->first();
        } else {
            $user = $this->Students->find()
                ->select(['first_name', 'last_name'])
                ->where(['Students.user_id' => $userId])
                ->order(['Students.user_id' => 'DESC'])
                ->first();
        }

        return $user ?: false;
    }


    function validate_department_college($data = null)
    {
        if (empty($data['User']['role_id'])) {
            return true;
        }

        if ($data['User']['role_id'] == ROLE_DEPARTMENT && empty($data['Staff'][0]['department_id'])) {
            $this->setErrors([
                'college_department' => ['User with department role must have a department. Please select a department for the user.']
            ]);
            return false;
        } elseif ($data['User']['role_id'] == ROLE_COLLEGE && empty($data['Staff'][0]['college_id'])) {
            $this->setErrors([
                'college_department' => ['User with college role must have a college. Please select a college for the user.']
            ]);
            return false;
        }

        return true;
    }

    public function checkUserIsBelongsInYourAdmin($userId = null, $roleId = null, $departmentId = null, $collegeId = null)
    {
        if (!$userId || !$roleId) {
            return false;
        }

        switch ($roleId) {
            case ROLE_SYSADMIN:
                $userRoleIds = $this->Roles->find('list', [
                    'conditions' => ['Roles.parent_id' => $roleId],
                    'keyField' => 'id',
                    'valueField' => 'id'
                ])->toArray();

                return $this->exists([
                    'id' => $userId,
                    'role_id IN' => $userRoleIds,
                    'is_admin' => 1
                ]);

            case ROLE_REGISTRAR:
                $userRoleIds = $this->Roles->find('list', [
                    'conditions' => ['Roles.parent_id' => $roleId],
                    'keyField' => 'id',
                    'valueField' => 'id'
                ])->toArray();

                $userRoleIds[$roleId] = $roleId;

                return $this->exists([
                    'id' => $userId,
                    'role_id IN' => $userRoleIds
                ]);

            case ROLE_MEAL:
            case ROLE_ACCOMODATION:
            case ROLE_HEALTH:
                return $this->exists(['id' => $userId, 'role_id' => $roleId]);

            case ROLE_DEPARTMENT:
                return $this->Staff->exists([
                    'user_id' => $userId,
                    'department_id' => $departmentId
                ]);

            case ROLE_COLLEGE:
                return $this->Staff->exists([
                    'user_id' => $userId,
                    'college_id' => $collegeId
                ]);
        }

        return false;
    }

    function doesItFullfillPasswordStrength($password = null, $securitysetting = null)
    {
        // ereg function was DEPRECATED in PHP 5.3.0, and REMOVED in PHP 7.0.0. using preg_match()
        if (!empty($securitysetting)) {
            if ($securitysetting['password_strength'] == 1) { //Medium
                if (!preg_match('/[a-z]/', $password)) return false;
                if (!preg_match('/[A-Z]/', $password)) return false;
                if (!preg_match('/[0-9]/', $password)) return false;
            } else { //Strong
                if (!preg_match('/[a-z]/', $password)) return false;
                if (!preg_match('/[A-Z]/', $password)) return false;
                if (!preg_match('/[0-9]/', $password)) return false;
                if (!preg_match('/[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/', $password)) return false;
            }
        } else {
            return false;
        }
        return true;
    }


    /**
     * Retrieve permitted actions for a user from both user-specific and role-based ACL.
     *
     * @param int $userId The user id.
     * @return array An array with keys 'UserLevel' and 'RoleLevel', each containing permitted ACO paths.
     */
    public function getAllPermissions($userId)
    {

        if (!$userId) {
            return [];
        }

        // Load ACL component
        $registry = new ComponentRegistry();
        $acl = new AclComponent($registry);

        // Load the Aros table
        $ArosTable = TableRegistry::getTableLocator()->get('Aros');

        // Get user and role AROs
        $aroUser = $ArosTable->find()
            ->where(['model' => 'Users', 'foreign_key' => $userId])
            ->first();

        $user = $this->get($userId, ['contain' => ['Roles']]);
        $aroRole = $ArosTable->find()
            ->where(['model' => 'Roles', 'foreign_key' => $user->role_id])
            ->first();

        // Create user ARO if it doesn’t exist and role ARO is present
        if (!$aroUser && $aroRole) {
            $aroUser = $ArosTable->newEntity([
                'parent_id' => $aroRole->id,
                'model' => 'Users',
                'foreign_key' => $userId,
                'alias' => 'User-' . $userId
            ]);
            if (!$ArosTable->save($aroUser)) {
                $this->log('Failed to save user ARO for user ID ' . $userId, 'error');
                return ['UserLevel' => [], 'RoleLevel' => []];
            }
        }


        // Get all controllers and actions
        $controllers = $this->getAllControllers();
        $permissions = [];
        $reformatePermission = [];

        foreach ($controllers as $controller) {
            $actions = $this->getControllerActions($controller);

            foreach ($actions as $action) {
                $acoPath = "controllers/" . Inflector::camelize($controller) . "/{$action}";
                if ($aroUser && $acl->check(
                        ['model' => 'Users', 'foreign_key' => $userId],
                        $acoPath
                    )) {
                    $permissions[] = $acoPath;
                }
                if ($aroRole && $acl->check(
                        ['model' => 'Roles', 'foreign_key' => $user->role_id],
                        $acoPath
                    )) {
                    $permissions[] = $acoPath;
                }


            }
        }

        // Special permissions for admin/sysadmin
        if ($user->is_admin || $user->role_id === ROLE_SYSADMIN) {
            $adminPermissions = [
                'controllers/Securitysettings/permission_management',
                'controllers/Securitysettings/index',
                'controllers/Acls/Permissions/add',
                'controllers/Acls/Permissions/delete',
                'controllers/Acls/Permissions/index',
                'controllers/Acls/Permissions/edit',
                'controllers/Acls/Acos/index',
                'controllers/Acls/Acls/index',
                'controllers/Users/index'
            ];


            if ($user->role_id === Configure::read('Roles.REGISTRAR') ||
                $user->role_id === Configure::read('Roles.SYSADMIN')) {
                $adminPermissions[] = 'controllers/Users/responsible';
            }
            if ($user->role_id === Configure::read('Roles.DEPARTMENT') ) {
                $adminPermissions[] = 'controllers/Users/department_create_user_account';
            }
            if ($user->role_id === Configure::read('Roles.SYSADMIN') ) {
                $adminPermissions[] = 'controllers/Users/add';
            }
            if (Configure::read('Developer')) {
                $adminPermissions = array_merge($adminPermissions, [
                    'controllers/Acls/Acos/add',
                    'controllers/Acls/Acos/edit',
                    'controllers/Acls/Acos/delete',
                    'controllers/Acls/Acos/rebuild'
                ]);
            }
            $permissions = array_merge($permissions, $adminPermissions);
        }

        $permissions[] = 'controllers/Dashboard/index';

        $permissions = array_unique($permissions);

        // Reformat permissions for menu construction with no duplicates and conditional index
        foreach ($permissions as $perm) {
            $parts = explode('/', $perm);
            if (count($parts) <= 2 || $parts[0] !== 'controllers') {
                continue;
            }

            $controllerName = $parts[1];
            $actionName = $parts[2];

            // Skip Acls controller actions from reformatting
            if ($controllerName === 'Acls') {
                continue;
            }

            // Handle student-specific restrictions
            if ($user->role_id === Configure::read('Roles.STUDENT')) {
                $student = $user->students[0] ?? null;
                $acceptedStudent = $student->accepted_students[0] ?? null;
                if ($acceptedStudent && $acceptedStudent->placementtype === 'REGISTRAR PLACED' &&
                    in_array($controllerName, ['Preferences', 'AcceptedStudents'])) {
                    continue;
                }
                if ($acceptedStudent && empty($acceptedStudent->placementtype) &&
                    $acceptedStudent->placement_approved_by_department && $controllerName === 'Preferences') {
                    continue;
                }
            }

            // Initialize controller actions if not set
            if (!isset($reformatePermission[$controllerName]['action'])) {
                $reformatePermission[$controllerName]['action'] = [];
            }

            // Add action only if not already present
            if (!in_array($actionName, $reformatePermission[$controllerName]['action'])) {
                $reformatePermission[$controllerName]['action'][] = $actionName;
            }
        }

        // Add 'index' automatically only if other actions exist (excluding Acls)

        foreach ($reformatePermission as $controller => &$data) {
            $actions = array_filter($data['action'], function ($action) {
                return $action !== 'index';
            });
            if (!empty($actions) && !in_array('index', $data['action']) && $controller !== 'Acls') {
                $data['action'][] = 'index';
                $permissions[] = "controllers/$controller/index";
            }
        }



        // Handle equivalent ACLs
        $equivalentACL = Configure::read('ACL.equivalentACL');
        if (is_array($equivalentACL)) {
            foreach ($equivalentACL as $parent => $childAcls) {
                foreach ($childAcls as $childAcl) {
                    $checking = explode('/', $childAcl);
                    $parentParts = explode('/', $parent);

                    if ($checking[1] === '*' && isset($reformatePermission[$checking[0]]['action'])) {
                        $this->addEquivalentPermissions($checking[0], $parentParts[0], $reformatePermission, $permissions);
                    } elseif (isset($reformatePermission[$checking[0]]['action']) && in_array($checking[1], $reformatePermission[$checking[0]]['action'])) {
                        $this->addEquivalentPermissions($checking[0], $parentParts[0], $reformatePermission, $permissions);
                    }
                }
            }
        }

        // Handle equivalent ACLs
        $equivalentACL = Configure::read('ACL.equivalentACL');
        if (is_array($equivalentACL)) {
            foreach ($equivalentACL as $parent => $childAcls) {
                foreach ($childAcls as $childAcl) {
                    $checking = explode('/', $childAcl);
                    $parentParts = explode('/', $parent);

                    if ($checking[1] === '*' && isset($reformatePermission[$checking[0]]['action'])) {
                        $this->addEquivalentPermissions($checking[0], $parentParts[0], $reformatePermission, $permissions);
                    } elseif (isset($reformatePermission[$checking[0]]['action']) && in_array($checking[1], $reformatePermission[$checking[0]]['action'])) {
                        $this->addEquivalentPermissions($checking[0], $parentParts[0], $reformatePermission, $permissions);
                    }
                }
            }
        }

        return [
            'permission' => array_unique($permissions),
            'reformatePermission' => $reformatePermission
        ];

    }

    /**
     * Helper to add equivalent permissions.
     */
    private function addEquivalentPermissions($sourceController, $targetController, &$reformatePermission, &$permissions)
    {
        if (!isset($reformatePermission[$targetController]['action'])) {
            $reformatePermission[$targetController]['action'] = ['index'];
        } elseif (!in_array('index', $reformatePermission[$targetController]['action'])) {
            $reformatePermission[$targetController]['action'][] = 'index';
        }
        if (!in_array("controllers/$targetController/index", $permissions)) {
            $permissions[] = "controllers/$targetController/index";
        }
        if (!in_array("controllers/$sourceController/index", $permissions)) {
            $permissions[] = "controllers/$sourceController/index";
            $reformatePermission[$sourceController]['action'][] = 'index';
        }
    }


    /**
     * Retrieve a list of all controller names from the application's Controller directory.
     *
     * @return array List of controller names (without "Controller" suffix).
     */
    private function getAllControllers()
    {
        $controllers = [];
        $dir = APP . 'Controller' . DS;
        foreach (glob($dir . '*Controller.php') as $file) {
            $filename = basename($file, 'Controller.php');
            if ($filename !== 'App') {
                $controllers[] = $filename;
            }
        }
        return $controllers;
    }

    /**
     * Retrieve all public action methods for a given controller.
     *
     * @param string $controller The controller name.
     * @return array List of actions.
     */
    private function getControllerActions($controller)
    {
        $controllerClass = "App\\Controller\\{$controller}Controller";
        if (!class_exists($controllerClass)) {
            return [];
        }
        $methods = get_class_methods($controllerClass);
        $baseMethods = get_class_methods('Cake\\Controller\\Controller');
        $actions = [];
        foreach ($methods as $method) {
            // Skip private/protected methods and framework methods
            if (strpos($method, '_') === 0 || in_array($method, $baseMethods)) {
                continue;
            }
            $actions[] = $method;
        }
        return $actions;
    }
    /**
     * Create ARO Node for User if missing
     */
    private function createAroNodeForUser($userId, $roleId)
    {
        $aroTable = TableRegistry::getTableLocator()->get('Aros');
        $roleNode = $aroTable->find()
            ->where(['foreign_key' => $roleId, 'model' => 'Role'])
            ->first();

        if ($roleNode) {
            $aro = $aroTable->newEntity([
                'parent_id' => $roleNode->id,
                'model' => 'User',
                'foreign_key' => $userId
            ]);
            $aroTable->save($aro);
        }
    }

    /**
     * Apply Role-Based Restrictions to Permissions
     */
    private function applyRoleBasedRestrictions($userDetail, $permissions, &$permissionAggregated)
    {
        $roleId = $userDetail->role_id;

        if ($roleId == ROLE_STUDENT) {
            // Remove Course Exemptions & Substitutions
            $permissions = array_filter($permissions, function ($perm) {
                return !in_array($perm, [
                    'controllers/CourseExemptions',
                    'controllers/CourseSubstitutionRequests',
                    'controllers/Preferences',
                    'controllers/AcceptedStudents'
                ]);
            });
        }

        if ($roleId == ROLE_COLLEGE) {
            $permissions = array_filter($permissions, function ($perm) {
                return !in_array($perm, [
                    'controllers/courseInstructorAssignments/assign_course_instructor',
                    'controllers/Clearances/add',
                    'controllers/Clearances/approve_clearance',
                    'controllers/Clearances/withdraw_management'
                ]);
            });
        }

        if ($roleId == ROLE_DEPARTMENT) {
            $permissions = array_filter($permissions, function ($perm) {
                return !in_array($perm, [
                    'controllers/sectionSplitForPublishedCourses',
                    'controllers/Clearances/add',
                    'controllers/Clearances/approve_clearance',
                    'controllers/Clearances/withdraw_management'
                ]);
            });
        }

        return array_values($permissions);
    }
    function my_array_merge(&$array1, $array2)
    {
        $result = array();
        foreach ($array2 as $key => $value) {
            if (!in_array($value, $array1)) {
                $array1[] = $value;
            }
        }
        return $array1;
    }

    function searchUserConditions($role_id, $search_params = null, $department_id = null, $college_id = null)
    {
        $options = array();
        $role_parent = array();

        $search_params['User']['role_id'] = (isset($search_params['Search']) ? $search_params['Search']['role_id'] : $search_params['User']['role_id']);
        $search_params['User']['name'] = (isset($search_params['Search']) ? $search_params['Search']['name'] : $search_params['User']['name']);
        $search_params['User']['active'] = (isset($search_params['Search']) ? $search_params['Search']['active'] : $search_params['User']['active']);
        $search_params['Staff']['active'] = (isset($search_params['Search']) ? $search_params['Search']['Staff']['active'] : $search_params['Staff']['active']);

        if (!empty($search_params['User']['role_id'])) {
            $role_parent[$search_params['User']['role_id']] = $search_params['User']['role_id'];
        } else {
            $role_parent = $this->Role->find('list', array('conditions' => array('Role.parent_id' => $role_id), 'fields' => array('id')));
            $role_parent[$role_id] = $role_id;
        }

        unset($role_parent[ROLE_STUDENT]);

        if ($role_id == ROLE_SYSADMIN) {
            if (!empty($search_params['User']['role_id'])) {
                $options['conditions'][] = array(
                    'User.role_id' => $search_params['User']['role_id'],
                    //'User.is_admin' => 1
                );
            } else {
                $options['conditions'][] = array(
                    "OR" => array(
                        'User.is_admin' => 1,
                        'User.role_id' => $role_parent,
                    ),
                );
            }
        } else {
            $options['conditions'][]['User.role_id'] = $role_parent;
        }

        if (!empty($search_params['User']['name'])) {
            $options['conditions'][] = array(
                "OR" => array(
                    'User.first_name LIKE ' =>  '%'. (trim($search_params['User']['name'])) . '%',
                    'User.last_name LIKE ' =>  '%'. (trim($search_params['User']['name'])) . '%',
                    'User.middle_name LIKE ' =>  '%'.( trim($search_params['User']['name'])) . '%',
                    'User.username LIKE' =>  '%'. (trim($search_params['User']['name'])) . '%',
                    'User.email LIKE' =>  '%'. (trim($search_params['User']['name'])) . '%',
                )
            );
        }

        if (!empty($department_id) && $role_id == ROLE_DEPARTMENT) {
            //$options['conditions'][] = array('User.id IN (select user_id from staffs where department_id = ' . $department_id . ')');
            $options['conditions'][] = array('User.id IN (select user_id from staffs where department_id = ' . $department_id . ' and active = ' . $search_params['Staff']['active'] . ')');
        } else if (!empty($college_id) && $role_id == ROLE_COLLEGE) {
            //$options['conditions'][] = array('User.id IN (select user_id from staffs where college_id = ' . $college_id . ')');
            $options['conditions'][] = array('User.id IN (select user_id from staffs where college_id = ' . $college_id . ' and active = ' . $search_params['Staff']['active'] . ')');
        } else {
            $options['conditions'][] = array('User.id IN (select user_id from staffs where active = ' . $search_params['Staff']['active'] . ')');
        }

        $options['conditions'][]['User.active'] = $search_params['User']['active'];

        /* if (!empty($search_params['Staff']['active'])) {
            $options['conditions'][] = array('User.id IN (select user_id from staffs where active = ' . $search_params['Staff']['active'] . ')');
        }

        if (!empty($search_params['User']['active'])) {
            $options['conditions'][]['User.active'] = $search_params['User']['active'];
        } */

        return $options;
    }

    public function generatePassword($length = '')
    {
        // Array or string offset access with curly braces deprecated in PHP 7.4. Targeting PHP 8.2.0
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($str);
        $length = @round($length);

        if (empty($length)) {
            $length = rand(8, 12);
        }

        $password = '';

        if ($length) {
            for ($i = 0; $i < $length; $i++) {
                $password .= $str[rand(0, $max - 1)];
            }
        }

        return $password;
    }


    public function regenerate_password_by_batch($departmentCollegeId, $academicYear, $commonPassword, $pre = 0)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        // Build conditions dynamically
        $conditions = ['Students.graduated' => 0];

        if ($pre == 1) {
            $conditions['Students.department_id IS'] = null;
            $conditions['Students.college_id'] = $departmentCollegeId;
        } elseif ($departmentCollegeId !== "all") {
            $conditions['Students.department_id'] = $departmentCollegeId;
        }

        if ($academicYear !== "all") {
            $conditions['Students.academicyear'] = $academicYear;
        }

        // Fetch Students with Users who need a password reset
        $students = $studentsTable->find()
            ->select(['Students.id', 'Users.id'])
            ->contain(['Users' => function ($q) {
                return $q->where(['Users.role_id' => 3, 'Users.force_password_change' => 1]);
            }])
            ->where($conditions)
            ->all();

        if ($students->isEmpty()) {
            return 'No students found requiring password reset.';
        }

        // Prepare hashed password
        $hashedPassword = (new DefaultPasswordHasher)->hash(trim($commonPassword));

        // Collect user IDs to update
        $userIds = [];
        foreach ($students as $student) {
            if (!empty($student->user)) {
                $userIds[] = $student->user->id;
            }
        }

        if (!empty($userIds)) {
            $usersTable->updateAll(
                ['password' => $hashedPassword, 'force_password_change' => 1],
                ['id IN' => $userIds]
            );
            return 'Password reset successfully for ' . count($userIds) . ' students.';
        }

        return 'No students met the criteria for password reset.';
    }


    public function createStudentAccountBatch($programType, $departmentCollegeId, $academicYear, $commonPassword, $pre = 0)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');

        // Get the admission year conversion
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry);
        $admissionYearConverted = $AcademicYear->get_academicYearBegainingDate($academicYear);

        // Build query conditions dynamically
        $conditions = [
            'Students.admissionyear' => $admissionYearConverted,
            'Students.program_type_id' => $programType,
            'Students.graduated' => 0,
            'Students.user_id IS' => null,
        ];

        if ($pre == 1) {
            $conditions['Students.college_id'] = $departmentCollegeId;
            $conditions['Students.department_id IS'] = null;
        } else {
            $conditions['Students.department_id'] = $departmentCollegeId;
        }

        // Fetch students who need accounts created
        $students = $studentsTable->find()
            ->select(['id', 'accepted_student_id', 'studentnumber', 'email', 'first_name', 'middle_name', 'last_name'])
            ->where($conditions)
            ->all();

        if ($students->isEmpty()) {
            return 'No students found for account creation.';
        }

        // Prepare user accounts for batch insert
        $hashedPassword = (new DefaultPasswordHasher)->hash(trim($commonPassword));
        $newUsers = [];
        $studentUpdates = [];
        $acceptedStudentUpdates = [];

        foreach ($students as $student) {
            $newUsers[] = $usersTable->newEntity([
                'username' => $student->studentnumber,
                'email' => $student->email,
                'role_id' => ROLE_STUDENT,
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'last_name' => $student->last_name,
                'force_password_change' => 1,
                'password' => $hashedPassword,
            ]);
        }

        // Batch insert new users
        if ($usersTable->saveMany($newUsers)) {
            foreach ($newUsers as $index => $user) {
                $student = $students->toList()[$index];
                $studentUpdates[] = ['id' => $student->id, 'user_id' => $user->id];

                if ($student->accepted_student_id) {
                    $acceptedStudentUpdates[] = ['id' => $student->accepted_student_id, 'user_id' => $user->id];
                }
            }

            // Batch update students
            $studentsTable->updateAll(
                ['user_id' => $user->id],
                ['id IN' => array_column($studentUpdates, 'id')]
            );

            // Batch update accepted students
            if (!empty($acceptedStudentUpdates)) {
                $acceptedStudentsTable->updateAll(
                    ['user_id' => $user->id],
                    ['id IN' => array_column($acceptedStudentUpdates, 'id')]
                );
            }

            return 'Student accounts created successfully for ' . count($newUsers) . ' students.';
        }

        return 'Failed to create student accounts.';
    }

    public function resetPasswordBySMS($mobilePhoneNumber)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $staffTable = TableRegistry::getTableLocator()->get('Staff');
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        $generatedPassword = $this->generateRandomPassword(8);
        $hashedPassword = (new DefaultPasswordHasher)->hash($generatedPassword);

        // Check if the mobile number belongs to a student
        $student = $studentsTable->find()
            ->contain(['Users'])
            ->where(['Students.phone_mobile' => $mobilePhoneNumber])
            ->first();

        if ($student) {
            $user = $student->user ?? $usersTable->newEmptyEntity();
            $user->username = $student->studentnumber;
            $user->role_id = ROLE_STUDENT;
            $user->password = $hashedPassword;
            $user->first_name = $student->first_name;
            $user->middle_name = $student->middle_name;
            $user->last_name = $student->last_name;
            $user->force_password_change = 2;

            if ($usersTable->save($user)) {
                if (empty($student->user_id)) {
                    $student->user_id = $user->id;
                    $studentsTable->save($student);
                }
                return "{$generatedPassword} is your one-time password and will expire in 30 minutes.";
            }
        }

        // Check if the mobile number belongs to a staff member
        $staff = $staffTable->find()
            ->contain(['Users'])
            ->where(['Staff.phone_mobile' => $mobilePhoneNumber])
            ->first();

        if ($staff) {
            $user = $staff->user ?? $usersTable->newEmptyEntity();
            $user->username = $staff->staffnumber;
            $user->role_id = ROLE_STAFF;
            $user->password = $hashedPassword;
            $user->first_name = $staff->first_name;
            $user->middle_name = $staff->middle_name;
            $user->last_name = $staff->last_name;
            $user->force_password_change = 2;

            if ($usersTable->save($user)) {
                if (empty($staff->user_id)) {
                    $staff->user_id = $user->id;
                    $staffTable->save($staff);
                }
                return "{$generatedPassword} is your one-time password and will expire in 30 minutes.";
            }
        }

        return "The phone number you provided is not found in our system.";
    }

    private function generateRandomPassword($length = 8)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }


    public function afterSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        $eventManager = $this->getEventManager();
        $isNew = $entity->isNew();

        if ($isNew) {
            $userEvent = new Event('Model.User.created', $this, [
                'id' => $entity->id,
                'data' => $entity->toArray()
            ]);
            $eventManager->dispatch($userEvent);
        } else {
            // Initialize MobileDetect
            $detect = new MobileDetect();
            $browser = $detect->isMobile() ? 'Mobile' : 'Desktop';
            $os = $detect->isTablet() ? 'Tablet' : php_uname('s');

            // Get request from CakePHP
            $request = \Cake\Http\ServerRequestFactory::fromGlobals();
            $ipAddress = $request->clientIp();

            $loginEvent = new Event('Model.User.login', $this, [
                'data' => $entity->toArray(),
                'browser' => $browser,
                'os' => $os,
                'ip' => $ipAddress
            ]);
            $eventManager->dispatch($loginEvent);
        }
    }

    public function syncAccount($isStudent = false)
    {
        $model = $isStudent ? 'Student' : 'Staff';

        $subquery = $this->Aros->find()
            ->select(['foreign_key'])
            ->where(['model' => 'User'])
            ->where(['parent_id IS NOT' => null]);

        $findAccountNotInAro = $this->find('all', [
            'conditions' => [
                'User.role_id' => $isStudent ? ROLE_STUDENT : ['!=', ROLE_STUDENT],
                'User.id NOT IN' => $subquery
            ],
            'contain' => [$model]
        ]);

        if (!$findAccountNotInAro->isEmpty()) {
            $this->synchronizeAros($findAccountNotInAro, $model);
        }
    }

    public function synchronizeAros($findAccountNotInAro, $model = 'Staff')
    {
        if (empty($findAccountNotInAro)) {
            return;
        }

        $Aros = TableRegistry::get('Aros');
        $Users = TableRegistry::get('Users');
        $ModelTable = TableRegistry::get($model);

        // Get the last right value for `Aros`
        $lastAro = $Aros->find()
            ->select(['rght'])
            ->order(['rght' => 'DESC'])
            ->first();

        $lastRight = $lastAro ? $lastAro->rght : 0;

        foreach ($findAccountNotInAro as $userData) {
            $userId = $userData['User']['id'];
            $roleId = $userData['User']['role_id'];

            // Fetch Role ARO
            $roleAro = $Aros->find()
                ->select(['id'])
                ->where(['model' => 'Role', 'foreign_key' => $roleId, 'parent_id IS' => null])
                ->first();

            if (!$roleAro) {
                Log::write('error', "Role ARO not found for Role ID: $roleId");
                continue;
            }

            $userDetail = $Users->get($userId, ['contain' => [$model]]);

            // Prepare ARO data
            $aroData = $Aros->newEntity([
                'foreign_key' => $userId,
                'parent_id' => $roleAro->id,
                'model' => 'User',
                'lft' => $lastRight + 1,
                'rght' => $lastRight + 2
            ]);

            // Save ARO
            if ($Aros->save($aroData)) {
                $lastRight += 2;
            } else {
                Log::write('error', "Failed to save ARO for User ID: $userId");
                continue;
            }

            // If the user is missing Staff/Student entry, create it
            if (empty($userDetail->$model)) {
                $newModelEntry = $ModelTable->newEntity([
                    'user_id' => $userId,
                    'first_name' => $userDetail->first_name,
                    'middle_name' => $userDetail->middle_name,
                    'last_name' => $userDetail->last_name,
                    'email' => $userDetail->email
                ]);

                if (!$ModelTable->save($newModelEntry)) {
                    Log::write('error', "Failed to create $model for User ID: $userId");
                }
            }
        }
    }


    public function getUserLogDetail($userId, $params = [])
    {
        $Users = TableRegistry::get('Users');
        $Logs = TableRegistry::get('Logs');

        // Fetch the user
        $user = $Users->find()
            ->select(['id', 'username', 'first_name', 'middle_name'])
            ->where(['id' => $userId])
            ->first();

        if (!$user) {
            Log::write('error', "User not found for ID: $userId");
            return [];
        }

        $fields = $params['fields'] ?? [];
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $conditions = $params['conditions'] ?? [];
        $order = $params['order'] ?? ['created' => 'DESC'];
        $limit = $params['limit'] ?? 1;

        // Fetch logs based on conditions
        $logs = $Logs->find()
            ->select(array_merge(['id', 'user_id', 'created', 'action', 'change'], $fields))
            ->where($conditions)
            ->order($order)
            ->limit($limit)
            ->all();

        if ($logs->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($logs as $log) {
            // Fetch the user who made the change
            $actedUser = $Users->find()
                ->select(['first_name', 'middle_name', 'username'])
                ->where(['id' => $log->user_id])
                ->first();

            if (!$actedUser) {
                continue;
            }

            $label = $this->getLogChangeDetail($log);
            $changeDecomposed = explode(',', $log->change);
            $changeMade = '<ul><li>' . implode('</li><li>', $changeDecomposed) . '</li></ul>';

            $result[] = [
                'Log' => [
                    'id' => $log->id,
                    'created' => $log->created,
                    'event' => sprintf(
                        "%s %s (%s) %sed %s. <br/><strong>The change was:-</strong> %s",
                        $actedUser->first_name,
                        $actedUser->middle_name,
                        $actedUser->username,
                        $log->action,
                        $label,
                        $changeMade
                    )
                ]
            ];
        }

        return $result;
    }

    public function getLogChangeDetail($logDetail)
    {
        if (empty($logDetail['model']) || empty($logDetail['foreign_key'])) {
            return "Invalid log detail provided.";
        }

        $modelName = $logDetail['model'];
        $foreignKey = $logDetail['foreign_key'];

        // Load the respective model
        $ModelTable = TableRegistry::get($modelName);

        // Fetch log-related details
        $entity = $ModelTable->find()
            ->where([$modelName . '.id' => $foreignKey])
            ->contain($this->getAssociatedModels($modelName))
            ->first();

        if (!$entity) {
            return "The record has been deleted or is unavailable.";
        }

        switch ($modelName) {
            case 'ExamGrade':
            case 'ExamGradeChange':
                return $this->formatExamGradeDetail($entity);

            case 'CourseRegistration':
            case 'CourseAdd':
                return $this->formatCourseRegistrationDetail($entity);

            case 'Course':
                return "{$entity->course_title} ({$entity->course_code}) of {$entity->curriculum->name} introduced in {$entity->curriculum->year_introduced}";

            case 'Section':
                return "{$entity->name} ({$entity->year_level->name}) Section AC-{$entity->academicyear} of {$entity->department->name}";

            case 'Student':
                return "{$entity->full_name} ({$entity->studentnumber}) admitted in {$entity->accepted_student->academicyear}";

            default:
                return "Details unavailable for model: $modelName.";
        }
    }

    /**
     * Get associated models based on the main model.
     */
    private function getAssociatedModels($modelName)
    {
        $associations = [
            'ExamGrade' => ['CourseRegistration.Student', 'CourseAdd.Student', 'CourseRegistration.PublishedCourse.Course'],
            'ExamGradeChange' => ['ExamGrade.CourseRegistration.Student', 'ExamGrade.CourseAdd.Student', 'ExamGrade.CourseRegistration.PublishedCourse.Course'],
            'CourseRegistration' => ['PublishedCourse.Course', 'Student'],
            'CourseAdd' => ['PublishedCourse.Course', 'Student'],
            'Course' => ['Curriculum'],
            'Section' => ['Department', 'YearLevel'],
            'Student' => ['Department', 'Curriculum', 'AcceptedStudent'],
        ];

        return $associations[$modelName] ?? [];
    }

    /**
     * Format Exam Grade details.
     */
    private function formatExamGradeDetail($entity)
    {
        if (!empty($entity->course_registration)) {
            return "{$entity->course_registration->student->first_name} {$entity->course_registration->student->last_name} ({$entity->course_registration->student->studentnumber}) - {$entity->course_registration->published_course->course->course_title} ({$entity->course_registration->published_course->course->course_code})";
        } elseif (!empty($entity->course_add)) {
            return "{$entity->course_add->student->first_name} {$entity->course_add->student->last_name} ({$entity->course_add->student->studentnumber}) - {$entity->course_add->published_course->course->course_title} ({$entity->course_add->published_course->course->course_code})";
        }
        return "Exam Grade details unavailable.";
    }

    /**
     * Format Course Registration details.
     */
    private function formatCourseRegistrationDetail($entity)
    {
        if (!empty($entity->student)) {
            return "{$entity->student->first_name} {$entity->student->last_name} ({$entity->student->studentnumber}) - {$entity->published_course->course->course_title} ({$entity->published_course->course->course_code})";
        }
        return "Course Registration details unavailable.";
    }
    public function removeLog($months = 12)
    {
        $logsTable = TableRegistry::getTableLocator()->get('Logs');

        $dateThreshold = date('Y-m-d H:i:s', strtotime("-$months months"));

        $logsTable->deleteAll(['created <' => $dateThreshold]);

        return true;
    }


    public function getUserDetails($userId)
    {
        if (!$userId) {
            return [];
        }

        // Fetch user details with role
        $user = $this->find()
            ->where(['Users.id' => $userId])
            ->contain(['Roles'])
            ->select(['id', 'role_id', 'username', 'is_admin', 'email_verified'])
            ->first();

        if (!$user) {
            return [];
        }

        $userDetails = ['User' => $user->toArray(), 'ApplicableAssignments' => []];

        if ($user->role_id == Configure::read('Roles.STUDENT')) {
            $userDetails += $this->getStudentDetails($userId);
        } else {
            $userDetails += $this->getStaffDetails($userId, $user->role_id, $user->is_admin);
        }

        return $userDetails;
    }

    private function getStudentDetails($userId)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $student = $studentsTable->find()
            ->where(['Students.user_id' => $userId])
            ->contain([
                'Colleges' => ['fields' => ['id', 'name', 'campus_id', 'stream']],
                'Departments' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->first();

        if (!$student) {
            return [];
        }

        $applicableAssignments = [
            'college_ids' => [$student->college->id => $student->college->id ?? null],
            'department_ids' => [$student->department->id => $student->department->id ?? null],
            'program_ids' => [$student->program->id => $student->program->id ?? null],
            'program_type_ids' => $this->getEquivalentProgramTypes($student->program_type->id ?? null),
            'college_permission' => empty($student->department->id) ? 1 : 0,
            'year_level_names' => ['1st' => '1st'],
            'last_section' => null
        ];

        $studentSection = $studentsTable->get($student->id, ['contain' => ['Sections.YearLevels']]);
        if (!empty($studentSection->section)) {
            $applicableAssignments['year_level_names'] = [
                    $studentSection->section->year_level->name ?? 'Pre' => $studentSection->section->year_level->name ?? 'Pre'
            ];
            $applicableAssignments['last_section'] = $studentSection->section;
        }

        return ['ApplicableAssignments' => $applicableAssignments];
    }

    private function getStaffDetails($userId, $roleId, $isAdmin)
    {
        $staffTable = TableRegistry::getTableLocator()->get('Staffs');
        debug($staffTable);

        $staff = $staffTable->find()
            ->where(['Staffs.user_id' => $userId])
            ->contain([
                'Colleges' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']]
            ])
            ->first();

        if (!$staff) {
            return [];
        }

        $activePrograms = TableRegistry::getTableLocator()->get('Programs')
            ->find('list', ['conditions' => ['active' => 1], 'keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        $activeProgramTypes = TableRegistry::getTableLocator()->get('ProgramTypes')
            ->find('list', ['conditions' => ['active' => 1], 'keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        $applicableAssignments = [
            'college_ids' => [],
            'department_ids' => [],
            'college_permission' => 0,
            'year_level_names' => []
        ];

        if (in_array($roleId, [
            Configure::read('Roles.REGISTRAR'),
            Configure::read('Roles.MEAL'),
            Configure::read('Roles.ACCOMODATION')
        ])) {
            if ($isAdmin) {
                $applicableAssignments['program_ids'] = $activePrograms;
                $applicableAssignments['program_type_ids'] = $activeProgramTypes;
                $applicableAssignments['college_ids'] = TableRegistry::getTableLocator()->get('Colleges')
                    ->find('list', ['conditions' => ['active' => 1], 'keyField' => 'id', 'valueField' => 'id'])
                    ->toArray();
                $applicableAssignments['department_ids'] = TableRegistry::getTableLocator()->get('Departments')
                    ->find('list', ['conditions' => ['active' => 1], 'keyField' => 'id', 'valueField' => 'id'])
                    ->toArray();
                $applicableAssignments['year_level_names'] = [0 => 'Pre'];
            }
        } else if ($roleId == Configure::read('Roles.COLLEGE')) {
            $applicableAssignments['college_ids'] = [$staff->college->id => $staff->college->id];

            if (!$isAdmin) {
                $applicableAssignments['program_ids'] = Configure::read('programs_available_for_registrar_college_level_permissions');
                $applicableAssignments['program_type_ids'] = Configure::read('program_types_available_for_registrar_college_level_permissions');
                $applicableAssignments['college_permission'] = 1;
                $applicableAssignments['year_level_names'] = [0 => 'Pre'];
            }
        } else if ($roleId == Configure::read('Roles.DEPARTMENT')) {
            $applicableAssignments['department_ids'] = [$staff->department->id => $staff->department->id];
            $applicableAssignments['program_ids'] = $activePrograms;
            $applicableAssignments['program_type_ids'] = $activeProgramTypes;
        } else {
            $applicableAssignments['program_ids'] = $activePrograms;
            $applicableAssignments['program_type_ids'] = $activeProgramTypes;
            $applicableAssignments['college_ids'] = TableRegistry::getTableLocator()->get('Colleges')
                ->find('list', ['conditions' => ['active' => 1], 'keyField' => 'id', 'valueField' => 'id'])
                ->toArray();
            $applicableAssignments['department_ids'] = TableRegistry::getTableLocator()->get('Departments')
                ->find('list', ['conditions' => ['active' => 1], 'keyField' => 'id', 'valueField' => 'id'])
                ->toArray();
        }

        return ['ApplicableAssignments' => $applicableAssignments];
    }
    public function getEquivalentProgramTypes($program_type_id = 0)
    {
        $programTypesToLook = [$program_type_id];

        $programType = TableRegistry::getTableLocator()->get('ProgramTypes')
            ->find()
            ->select(['equivalent_to_id'])
            ->where(['id' => $program_type_id])
            ->first();

        if (!empty($programType) && !empty($programType->equivalent_to_id)) {
            $equivalentProgramTypes = unserialize($programType->equivalent_to_id);

            if (is_array($equivalentProgramTypes)) {
                $programTypesToLook = array_merge($programTypesToLook, $equivalentProgramTypes);
            }
        }

        return $programTypesToLook;
    }

}
