<?php
namespace App\Controller;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Utility\Security;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Filesystem\Folder;


use Exception;

class UsersController extends AppController
{

    public $loginAttemptLimit = 3;
    public $loginAttemptDuration = '+5 minutes';


    public $menuOptions = array(
        //'parent' => 'dashboard',
        'parent' => 'security',
        'exclude' => array(
            'resetPassword',
            'assign',
            'assignUserDormBlock',
            'assignUserMealHall',
            'cancelTaskConfirmation',
            'buildUserMenu',
            'confirmTask',
            'editProfile',
            'checkSession'
        ),
        'alias' => array(
            'index' => 'List All Users',
            'add' => 'Create User',
            'changePwd' => 'Change Your Password',
            'departmentCreateUserAccount' => 'Create User Account'
        ),
        'weight' => -2,
    );

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Attempt');
        $this->loadComponent('MathCaptcha');
        $this->loadComponent('Paginator');


    }
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['login', 'logout', 'forget', 'search',
            'resetPassword','buildUserMenu', 'checkSession','changePwd']);
    }


    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        // Ensure that encrypted passwords are not sent back to the user
        /*
        unset($this->request->data['User']['password']);
        unset($this->request->data['User']['passwd']);
        unset($this->request->data['User']['oldpassword']);
        unset($this->request->data['User']['password2']);
        unset($this->request->data['User']['confirm_password']);
        */
    }

    /*
    public function beforeRender(Event $event)
    {

        $session = $this->request->getSession();
        $authUser = $session->read('Auth.User');
        $usersRelation = $session->read('users_relation');

        if ($authUser) {
            if (
                isset($usersRelation['User']['id']) &&
                $authUser['id'] == $usersRelation['User']['id'] &&
                $this->role_id == $authUser['role_id']
            ) {
                $roleId = $authUser['role_id'];
            } else {
                $session->destroy();
                return $this->redirect($this->Auth->logout());
            }
        } else {
            $session->destroy();
            return $this->redirect($this->Auth->logout());
        }

        $this->set('role_id', $roleId);

    }
    */

    public function index()
    {


        // Retrieve query parameters
        $params = [
            'limit' => $this->request->getQuery('Search.limit', 100),
            'name' => $this->request->getQuery('Search.name'),
            'role_id' => $this->request->getQuery('Search.role_id', $this->Auth->user('role_id')),
            'staff_active' => $this->request->getQuery('Search.Staff.active', 1),
            'user_active' => $this->request->getQuery('Search.active', 1),
            'staff_department_id' => $this->request->getQuery('Search.Staff.department_id'),
            'sort' => $this->request->getQuery('sort', 'full_name'),
            'direction' => $this->request->getQuery('direction', 'desc'),
            'page' => $this->request->getQuery('page', 1)
        ];

        // Sanitize inputs
        $params['limit'] = filter_var($params['limit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5000, 'default' => 100]]);
        $params['name'] = filter_var($params['name'], FILTER_SANITIZE_STRING, ['options' => ['default' => null]]);
        $params['role_id'] = filter_var($params['role_id'], FILTER_VALIDATE_INT, ['options' => ['default' => $this->Auth->user('role_id')]]);
        $params['staff_active'] = filter_var($params['staff_active'], FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
        $params['user_active'] = filter_var($params['user_active'], FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
        $params['staff_department_id'] = filter_var($params['staff_department_id'], FILTER_VALIDATE_INT, ['options' => ['default' => null]]);
        $params['sort'] = filter_var($params['sort'], FILTER_SANITIZE_STRING, ['options' => ['default' => 'full_name']]);
        $params['direction'] = in_array(strtolower($params['direction']), ['asc', 'desc']) ? strtolower($params['direction']) : 'desc';
        $params['page'] = filter_var($params['page'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);

        // Initialize roles
        $parentRoles = $this->Users->Roles->find('list')
            ->where(['Roles.parent_id' => $this->Auth->user('role_id')])
            ->toArray();
        $parentRoles[$this->Auth->user('role_id')] = $this->Auth->user('role_id');
        unset($parentRoles[ROLE_STUDENT]);

        $roles = $this->Users->Roles->find('list')
            ->where(['OR' => [
                'Roles.parent_id IN' => array_keys($parentRoles),
                'Roles.id' => $this->Auth->user('role_id')
            ]])
            ->toArray();

        if (in_array($this->Auth->user('role_id'), [ROLE_DEPARTMENT, ROLE_COLLEGE])) {
            $roles[ROLE_INSTRUCTOR] = 'Instructor';
        }
        unset($roles[ROLE_STUDENT]);

        // Initialize departments for sysadmin
        $departments = [];
        if ($this->Auth->user('role_id') == ROLE_SYSADMIN) {
            $departments = $this->Users->Staffs->Departments->allDepartmentsByCollege2(1,
                $this->department_ids ?? [], [], 1);
        }

        // Handle clear filters
        if (empty($this->request->getQuery('getUsers'))) {

            $params = [
                'limit' => $this->request->getQuery('Search.limit', 100),
                'role_id' =>  $this->Auth->user('role_id'),
                'staff_active' => 1,
                'user_active' => 1
            ];

        }

        // Initialize search index
        //$this->initSearchIndex();

        // Build conditions
        $searchParams = ['Search' => $params];
        $conditions = $this->Users->searchUserConditions(
            $this->Auth->user('role_id'),
            $searchParams,
            $this->Auth->user('role_id') == ROLE_SYSADMIN
            && $params['staff_department_id'] ? $params['staff_department_id'] :
                ($this->department_id ?? null),
            $this->college_id ?? null
        );

        // Configure pagination
        $this->paginate = [
            'conditions' => $conditions['conditions'] ?? [],
            'contain' => ['Roles', 'Staffs'],
            'limit' => $params['limit'],
            'maxLimit' => $params['limit'],
            'order' => !empty($params['sort']) ? [$params['sort'] => $params['direction']] : [
                'Users.active' => 'DESC',
                'Users.first_name' => 'ASC',
                'Users.middle_name' => 'ASC',
                'Users.last_name' => 'ASC',
                'Users.last_login' => 'DESC'
            ],
            'page' => $params['page'],
            'query' => $this->request->getQuery() // Preserve query parameters
        ];

        // Paginate results
        $users = [];
        $users = $this->paginate($this->Users);
        // Handle empty results
        if (empty($users) && !empty($conditions['conditions'])) {
            $this->Flash->info(__('No users found with the given search criteria.'));
        }


        $selected_limit = $params['limit'];
        $selected_search = $params['name'];
        $selected_role= $params['role_id'];
        $selected_staff_active= $params['staff_active'];
        $selected_user_active= $params['user_active'];
        $selected_staff_department_id=$params['staff_department_id'];
        $order_by= $params['sort'];
        $sort_order= $params['direction'];

        // Set view variables
        $this->set(compact(
            'users',
            'roles',
            'departments',
            'selected_limit',
            'selected_search',
            'selected_role',
            'selected_staff_active',
            'selected_user_active',
            'selected_staff_department_id',
            'order_by',
            'sort_order'
        ));
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid user!'));
            return $this->redirect(['action' => 'index']);
        }

        $colleges = $this->Users->Staffs->Colleges->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $departments = $this->Users->Staffs->Departments->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
            'order' => ['Departments.name' => 'ASC']
        ])->toArray();

        $programs = $this->getTableLocator()->get('Programs')->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $programTypes = $this->getTableLocator()->get('ProgramTypes')->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $user = $this->Users->find()
            ->where(['Users.id' => $id])
            ->contain([
                'Roles',
                'StaffAssignes',
                'Staffs' => ['Colleges', 'Departments', 'Positions', 'Titles'],
                'Students' => ['Colleges', 'Departments']
            ])
            ->first();

        if (!$user) {
            $this->Flash->error(__('Invalid user!'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('user', 'colleges', 'departments', 'programs', 'programTypes'));
    }


    public function add()
    {
        $user = $this->Users->newEntity([]);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // Process and normalize form data
            $userData = [
                'email' => strtolower(trim($data['User']['email'] ?? $data['Staff']['email'] ?? '')),
                'username' => trim($data['User']['username'] ?? ''),
                'first_name' => ucwords(trim($data['Staff']['first_name'] ?? '')),
                'middle_name' => ucwords(trim($data['Staff']['middle_name'] ?? '')),
                'last_name' => ucwords(trim($data['Staff']['last_name'] ?? '')),
                'role_id' => $data['User']['role_id'] ?? null,
                'staffs' => [
                    [
                        'first_name' => ucwords(trim($data['Staff']['first_name'] ?? '')),
                        'last_name' => ucwords(trim($data['Staff']['last_name'] ?? '')),
                        'middle_name' => ucwords(trim($data['Staff']['middle_name'] ?? '')),
                        'email' => strtolower(trim($data['Staff']['email'] ?? '')),
                        'department_id' => $data['Staff']['department_id'] ?? null,
                        'college_id' => $data['Staff']['college_id'] ?? null,
                        'title_id' => $data['Staff']['title_id'] ?? null,
                        'position_id' => $data['Staff']['position_id'] ?? null,
                        'service_wing_id' => $data['Staff']['service_wing_id'] ?? null,
                        'education_id' => $data['Staff']['education_id'] ?? null,
                        'gender' => $data['Staff']['gender'] ?? null,
                        'birthdate' => $data['Staff']['birthdate'] ?? null,
                        'staffid' => $data['Staff']['staffid'] ?? null,
                        'phone_mobile' => $data['Staff']['phone_mobile'] ?? null,
                        'phone_office' => $data['Staff']['phone_office'] ?? null,
                        'address' => $data['Staff']['address'] ?? null,
                        'user_id' => null,
                        'attachments' => [],
                    ]
                ],
                'password' => $data['User']['password'] ?? null,
            ];


            // Add attachment if file is uploaded
            if (!empty($data['Staff']['attachments'][0]['upload']['tmp_name']) &&
                is_uploaded_file($data['Staff']['attachments'][0]['upload']['tmp_name'])) {
                $file = $data['Staff']['attachments'][0]['upload'];
                $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword'];
                $maxSize = 2 * 1024 * 1024;
                if (!in_array($file['type'], $allowedTypes)) {
                    $this->Flash->error(__('Invalid file type. Only JPEG, PNG, PDF, and DOC are allowed.'));
                    return $this->redirect(['action' => 'add']);
                }
                if ($file['size'] > $maxSize) {
                    $this->Flash->error(__('File size exceeds 2MB limit.'));
                    return $this->redirect(['action' => 'add']);
                }

                $attachmentGroup = strpos($file['type'], 'image') === 0 ? 'profile' : 'attachment';
                $userData['staffs'][0]['attachments'] = [
                    0 => [
                        'upload' => $file,
                        'model' => 'Staff',
                        'attachment_group' => $attachmentGroup,
                        'size' => 'original',
                        'alternative' => isset($data['Staff']['attachments'][0]['alternative']) ? trim($data['Staff']['attachments'][0]['alternative']) : null,
                    ]
                ];
            }

            // Check for existing user by email
            $userExists = $this->Users->find()
                ->where(['Users.email' => $userData['email']])
                ->select(['id', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'role_id'])
                ->first();
            if ($userExists) {
                if ($userExists->role_id != ROLE_STUDENT) {
                    $staffDetails = $this->Users->Staffs->find()
                        ->where([
                            'Staffs.email' => $userData['email'],
                            'Staffs.user_id IS NOT NULL'
                        ])
                        ->select(['id', 'first_name', 'middle_name', 'last_name', 'user_id', 'email'])
                        ->first();
                    if ($staffDetails) {
                        $this->Flash->error(__('User account for "{0}" already exists. You do not need to add it again.', $userExists->first_name . ' ' . $userExists->middle_name . ' ' . $userExists->last_name . ' (' . $userExists->username . ')'));
                        return $this->redirect(['action' => 'add']);
                    }
                } else {
                    $this->Flash->error(__('The provided email is already in use for a student "{0}". Please correct that before continuing.', $userExists->first_name . ' ' . $userExists->middle_name . ' ' . $userExists->last_name . ' (' . $userExists->username . ')'));
                    return $this->redirect(['action' => 'add']);
                }
            }

            // Check for existing unlinked staff profile
            $existingStaff = $this->Users->Staffs->find()
                ->where([
                    'Staffs.email' => $userData['email'],
                    'Staffs.user_id IS NULL'
                ])
                ->select(['id', 'first_name', 'middle_name', 'last_name', 'email'])
                ->first();
            if ($existingStaff) {
                $userData['staffs'][0]['id'] = $existingStaff->id;
            }

            // Check account limits for sysadmin
            $roleId = $this->request->getSession()->read('Auth.User.role_id') ?? ROLE_GUEST;
            $check = ($roleId == ROLE_SYSADMIN) ? $this->Users->checkNumberOfUserAccount($userData) : true;
            if (!$check) {
                $errors = $user->getErrors();
                if (isset($errors['college_department'])) {
                    $this->Flash->error($errors['college_department'][0]);
                } else {
                    $this->Flash->error(__('The user could not be saved due to account limits.'));
                }
                return $this->redirect(['action' => 'add']);
            }

            // Validate username length
            $minUsernameLength = (is_numeric(MINIMUM_USERNAME_LENGTH) && MINIMUM_USERNAME_LENGTH >= 3) ? MINIMUM_USERNAME_LENGTH : 3;
            if (strlen($userData['username']) < $minUsernameLength) {
                $this->Flash->error(__('The username must be at least {0} characters long.', $minUsernameLength));
                return $this->redirect(['action' => 'add']);
            }

            // Generate password
            $pwdLength = (is_numeric(GENERATE_PASSWORD_LENGTH) && GENERATE_PASSWORD_LENGTH >= 5) ? GENERATE_PASSWORD_LENGTH : 5;
            $password = $userData['password'] = $this->Users->generatePassword($pwdLength);

            // Define associations
            $associated = [
                'Staffs' => ['validate' => 'default']
            ];
            if (!empty($userData['staffs'][0]['attachments'])) {
                $associated = [
                    'Staffs' => [
                        'validate' => 'default',
                        'associated' => [
                            'Attachments' => ['validate' => 'default']
                        ]
                    ]
                ];
            }

            $user = $this->Users->newEntity($userData, [
                'validate' => 'default',
                'associated' => $associated
            ]);

            if ($this->Users->save($user, ['associated' => $associated,'atomic' => true])) {
                // Fetch saved user with associations
                $staffDetails = $this->Users->find()
                    ->where(['Users.id' => $user->id])
                    ->contain(['Staffs' => ['Departments', 'Colleges' => ['Campuses'], 'Titles', 'Positions', 'Educations', 'ServiceWings'], 'Staffs.Attachments'])
                    ->first();

                // Fix: Access uploaded attachment details if present (example for first attachment)
                if (!empty($staffDetails->staffs[0]['attachments'][0])) {
                    $attachment = $staffDetails->staffs[0]['attachments'][0];  // Assume first attachment

                    // Pass to view or PDF (e.g., for rendering in issue_password_staff_pdf.ctp)
                    $this->set('attachmentUrl', $attachment->getUrl());
                    $this->set('isImage', $attachment->is_image);
                    $this->set('thumbnails', $attachment->thumbnails);
                }


                // Fetch university details
                $university = $this->getTableLocator()->get('Universities')
                    ->find()
                    ->contain(['Attachments' => ['sort' => ['Attachments.created' => 'DESC']]])
                    ->order(['Universities.created' => 'DESC'])
                    ->first();

                // Send email notification
                $userEmail = !empty($userData['email']) ? trim($userData['email']) : ($staffDetails->email ? trim($staffDetails->email) : null);
                if (!empty($userEmail) && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                    $message = $this->createEmailMessage($staffDetails, $userData['password'], 0);
                    $email = new Email('default');
                    $email->setTemplate('password_reset')
                        ->setEmailFormat('html')
                        ->setTo($userEmail)
                        ->setSubject('Your New SMiS Account')
                        ->setViewVars(['message' => $message]);
                    try {
                        if ($email->send()) {
                            $this->Flash->success(__('The user has been created and an email has been sent to {0}.', $userEmail));
                        } else {
                            $this->Flash->success(__('The user has been created.'));
                        }
                    } catch (\Exception $e) {
                        $this->Flash->success(__('The user has been created.'));
                        $this->log('Email sending failed: ' . $e->getMessage(), 'error');
                    }
                } else {
                    $this->Flash->success(__('The user has been created.'));
                }

                // Render PDF
                $this->set(compact('staffDetails', 'university', 'password'));
                $this->response = $this->response->withType('application/pdf');
                $this->viewBuilder()->setLayout('pdf/default');
                $this->render('issue_password_staff_pdf');
            } else {
                $nestedErrors = $this->getNestedErrors($user);
                $errorMsg = 'Unable to save user and staff.';

                if (!empty($nestedErrors)) {
                    $errorMsg .= ' Errors: ' . implode('; ', $nestedErrors);
                }
                $this->Flash->error($errorMsg);
            }
        }

        $parentRoles = $this->Users->Roles->find('list')
            ->where(['Roles.parent_id' => $this->request->getSession()->read('Auth.User.role_id') ?? ROLE_GUEST])
            ->toArray();
        $parentRoles[$this->request->getSession()->read('Auth.User.role_id') ?? ROLE_GUEST] = $this->request->getSession()->read('Auth.User.role_id');
        $roles = $this->Users->Roles->find('list')
            ->where([
                'OR' => [
                    'Roles.parent_id IN' => array_keys($parentRoles),
                    'Roles.id' => $this->request->getSession()->read('Auth.User.role_id') ?? ROLE_GUEST
                ]
            ])
            ->toArray();
        $positions = $this->Users->Staffs->Positions->find('list', [
            'keyField' => 'id',
            'valueField' => 'position'
        ])->toArray();
        $titles = $this->Users->Staffs->Titles->find('list')->toArray();
        $colleges = $this->Users->Staffs->Colleges->find('list')
            ->where(['Colleges.active' => 1])
            ->toArray();
        $departments = $this->Users->Staffs->Departments->find('list')
            ->where(['Departments.active' => 1])
            ->toArray();
        $educations = $this->Users->Staffs->Educations->find('list')
            ->where(['Educations.active' => 1])
            ->toArray();
        $servicewings = $this->Users->Staffs->ServiceWings->find('list')
            ->where(['ServiceWings.active' => 1])
            ->toArray();
        $this->set(compact('user', 'departments', 'educations', 'servicewings', 'colleges', 'roles', 'titles', 'positions'));
    }

    // Fix: Recursive error extractor for nested entities (Staffs, Attachments)
    protected function getNestedErrors($entity, $prefix = '') {
        $messages = [];
        $errors = $entity->getErrors();
        foreach ($errors as $field => $fieldErrors) {
            foreach ((array)$fieldErrors as $message) {
                $messages[] = $prefix . $field . ': ' . $message;
            }
        }
        // Recurse into staffs
        if (isset($entity->staffs) && is_array($entity->staffs)) {
            foreach ($entity->staffs as $index => $staff) {
                $messages = array_merge($messages, $this->getNestedErrors($staff, "Staff[$index]."));
            }
        }
        // Recurse into attachments (could be on User or Staff)
        if (isset($entity->attachments) && is_array($entity->attachments)) {
            foreach ($entity->attachments as $index => $attachment) {
                $messages = array_merge($messages,
                    $this->getNestedErrors($attachment, "Attachment[$index]."));
            }
        }
        return $messages;
    }

    public function edit($id = null)
    {
        if (!$id && !$this->request->is('post')) {
            $this->Flash->error(__('Invalid user!'));
            return $this->redirect(['action' => 'index']);
        }

        $userCount = $this->Users->find()
            ->where(['Users.id' => $id])
            ->count();

        if ($userCount == 0) {
            $this->Flash->error(__('Invalid user! The selected user does not exist.'));
            return $this->redirect(['action' => 'index']);
        }

        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        $userId = $this->request->getSession()->read('Auth.User.id');

        if ($roleId == ROLE_COLLEGE || $roleId == ROLE_DEPARTMENT) {
            if (!Configure::read('ENABLE_INSTRUCTOR_USER_EDIT_COLLEGE_DEPARTMENT')) {
                $instructorCount = $this->Users->find()
                    ->where(['Users.id' => $id, 'Users.role_id' => ROLE_INSTRUCTOR])
                    ->count();
                if ($userId != $id && $instructorCount > 0) {
                    $this->Flash->error(__('You are not allowed to edit instructor profile!'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            $isOwnAccount = $userId == $id;
            $belongsToAdmin = $this->Users->checkUserIsBelongsInYourAdmin(
                $id,
                $roleId,
                $roleId == ROLE_DEPARTMENT ? ($this->department_id ?? null) : null,
                $roleId == ROLE_COLLEGE ? ($this->college_id ?? null) : null
            );

            if (!$belongsToAdmin && !$isOwnAccount) {
                $this->Flash->error(__('You are not eligible to edit the selected user details. The user belongs to another administrator.'));
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $isOwnAccount = $userId == $id;
            $belongsToAdmin = $this->Users->checkUserIsBelongsInYourAdmin($id, $roleId);
            if (!$belongsToAdmin && !$isOwnAccount) {
                $this->Flash->error(__('You are not eligible to edit the selected user details. The user belongs to another administrator.'));
                return $this->redirect(['action' => 'index']);
            }
        }

        $user = $this->Users->get($id, ['contain' => ['Staffs']]);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (!empty($data['Staff'])) {
                foreach ($data['Staff'] as $k => &$v) {
                    $data['User']['first_name'] = ucwords(trim($v['first_name']));
                    $data['User']['last_name'] = ucwords(trim($v['last_name']));
                    $data['User']['middle_name'] = ucwords(trim($v['middle_name']));
                    if (!empty($v['email'])) {
                        $data['User']['email'] = strtolower(trim($v['email']));
                    }
                    $data['User']['id'] = $id;
                    break;
                }
            }

            $user = $this->Users->patchEntity($user, $data, ['validate' => 'first']);
            if ($this->Users->save($user, ['associated' => ['Staffs']])) {
                $this->Flash->success(__('The user data has been updated.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->log('Validation errors: ' . json_encode($user->getErrors()), 'error');
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Users->find()
                ->where(['Users.id' => $id])
                ->contain(['Staffs'])
                ->first()
                ->toArray();

            if (!empty($this->request->data['Staff'][0]['phone_mobile'])) {
                $this->request->data['Staff'][0]['phone_mobile'] = $this->Users->Staffs->getformatedEthiopianMobilePhoneNumber(
                    $this->request->data['Staff'][0]['phone_mobile'],
                    0,
                    1
                );
            }
        }

        $countries = $this->Users->Staffs->Countries->find('list')->toArray();
        $positions = $this->Users->Staffs->Positions->find('list', [
            'keyField' => 'id',
            'valueField' => 'position'
        ])->toArray();
        $titles = $this->Users->Staffs->Titles->find('list')->toArray();
        $cities = $this->Users->Staffs->Cities->find('list')->toArray();
        $colleges = $this->Users->Staffs->Colleges->find('list')
            ->where(['Colleges.active' => 1])
            ->toArray();
        $departments = $this->Users->Staffs->Departments->find('list')
            ->where(['Departments.active' => 1])
            ->toArray();

        $educations = [
            'Doctorate' => 'PhD',
            'Master' => 'Master',
            'Medical Doctor' => 'Medical Doctorate',
            'Degree' => 'Degree',
            'Diploma' => 'Diploma',
            'Certificate' => 'Certificate'
        ];

        $servicewings = [
            'Academician' => 'Academician',
            'Librarian' => 'Librarian',
            'Registrar' => 'Registrar',
            'Technical Support' => 'Technical Support'
        ];

        // Role filtering
        $conditions = [];
        if ($roleId == ROLE_DEPARTMENT) {
            $conditions = [ROLE_DEPARTMENT, ROLE_INSTRUCTOR];
        } elseif ($roleId == ROLE_COLLEGE) {
            $conditions = [ROLE_COLLEGE, ROLE_INSTRUCTOR];
        } elseif ($roleId == ROLE_REGISTRAR) {
            $conditions = [ROLE_REGISTRAR];
        } elseif ($roleId == ROLE_MEAL) {
            $conditions = [ROLE_MEAL];
        } elseif ($roleId == ROLE_HEALTH) {
            $conditions = [ROLE_HEALTH];
        } elseif ($roleId == ROLE_ACCOMODATION) {
            $conditions = [ROLE_ACCOMODATION];
        } elseif ($roleId == ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM) {
            $conditions = [ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM];
        } elseif ($roleId == ROLE_SYSADMIN) {
            // No restrictions
        }

        if (!empty($conditions)) {
            $parentRoles[$roleId] = $roleId;
            $roles = $this->Users->Roles->find('list')
                ->where(['OR' => [
                    'Roles.parent_id IN' => array_keys($parentRoles),
                    'Roles.id IN' => $conditions
                ]])
                ->toArray();
        } else {
            $roles = $this->Users->Roles->find('list')->toArray();
        }

        $collegeDepartment = [];
        if (!empty($colleges)) {
            foreach ($colleges as $collegeId => $collegeName) {
                $departmentList = $this->Users->Staffs->Departments->find('list', [
                    'conditions' => [
                        'Departments.college_id' => $collegeId,
                        'Departments.active' => 1
                    ],
                    'keyField' => 'id',
                    'valueField' => 'name',
                    'order' => ['Departments.name' => 'ASC']
                ])->toArray();
                foreach ($departmentList as $departmentId => $departmentName) {
                    $collegeDepartment[$collegeId][$departmentId] = $departmentName;
                }
            }
        }

        $editingUser = $this->Users->find()
            ->where(['Users.id' => $userId])
            ->contain(['Staffs'])
            ->select(['id', 'username', 'role_id', 'is_admin'])
            ->first();

        if ($editingUser) {
            unset($editingUser->password);
        }

        $ownAccountOfEditingUser = $userId == $id ? 1 : 0;

        $this->set(compact(
            'id',
            'user',
            'departments',
            'educations',
            'countries',
            'cities',
            'colleges',
            'titles',
            'positions',
            'collegeDepartment',
            'editingUser',
            'ownAccountOfEditingUser',
            'servicewings',
            'roles'
        ));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if (!$id) {
            $this->Flash->error(__('Invalid id for user.'));
            return $this->redirect(['action' => 'index']);
        }

        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('User deleted!'));
        } else {
            $this->Flash->error(__('User was not deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
        $this->viewBuilder()->setLayout('home_new');
        $this->loadModel('Securitysettings');

        // Fetch security settings
        $securitysetting = $this->Securitysettings->find()
            ->select(['number_of_login_attempt'])
            ->first();

        $number_of_login_attempt = $securitysetting ? $securitysetting->number_of_login_attempt : $this->loginAttemptLimit;

        $session = $this->getRequest()->getSession();

        // If user is already logged in, redirect
        if ($session->check('Auth.User')) {
            $this->Flash->success(__('You are already logged in!'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $username = trim($data['username']);

            // Check user exists
            $userexist = $this->Users->find()
                ->where(['username' => $username])
                ->select(['id', 'username', 'is_admin', 'role_id', 'active', 'failed_login'])
                ->first();


            if ($userexist) {
                $failedLogins = $userexist->failed_login;

                if ($this->Attempt->limit($username, 'login', $number_of_login_attempt)) {
                    $user = $this->Auth->identify();
                    if ($user) {
                        $this->Auth->setUser($user);
                        if ($user['active']) {
                            // Ensure $user is an entity before saving
                            $userEntity = $this->Users->get($user['id']);  // Retrieve the existing user entity
                            $userEntity = $this->Users->patchEntity(
                                $userEntity,
                                ['last_login' => date('Y-m-d H:i:s')]
                            );
                            // Save the updated user entity
                            $this->Users->save($userEntity);
                            $session->write('User.is_logged_in', true);

                            if (isset($user) && !empty($user)) {
                                // Automatically update old passwords to new hashing method
                                if (strlen($user['password']) < 60) { // SHA1 is shorter than Bcrypt
                                    $userEntity = $this->Users->get($user['id']);
                                    $userEntity->password = $this->request->getData('password');
                                    $this->Users->save($userEntity);
                                    Log::info(
                                        "User password updated to Bcrypt: " .
                                        $user['username']
                                    );
                                }
                                $this->Auth->setUser($user);
                            }
                            return $this->redirect(
                                $this->Auth->redirectUrl() ?: ['controller' => 'Dashboard', 'action' => 'index']
                            );
                        } else {
                            // Redirect graduated students
                            if ($user->role_id == ROLE_STUDENT) {
                                $graduated = $this->Users->Students->find()
                                    ->where(['studentnumber' => $user->username])
                                    ->contain(['GraduateList', 'Alumnus'])
                                    ->first();

                                if (!empty($graduated->graduate_list) && empty($graduated->alumnus)) {
                                    return $this->redirect(['controller' => 'Alumni', 'action' => 'add']);
                                }
                            }
                            $this->Flash->error(__('Your account is inactive.'));
                        }
                    } else {
                        // Failed login attempt
                        //$user->failed_login++;
                        //$this->Users->save($user);
                        $this->Flash->error(__('Your password is incorrect. Please try again.'));
                        $this->Attempt->fail($username, 'login', $this->loginAttemptDuration);
                    }
                } else {
                    // Too many failed attempts
                    $this->Flash->error(__('Too many failed attempts! (' . $failedLogins . ')'));

                    if (!empty($data['security_code']) && $this->MathCaptcha->validates($data['security_code'])) {
                        $user = $this->Auth->identify();
                        if ($user) {
                            $this->Auth->setUser($user);

                            $userEntity = $this->Users->get($user['id']);  // Retrieve the existing user entity
                            $userEntity = $this->Users->patchEntity($userEntity, ['last_login' => date('Y-m-d H:i:s')]);
                            // Save the updated user entity
                            $this->Users->save($userEntity);
                            //  return $this->redirect($this->Auth->redirectUrl() ?: ['controller' => 'Dashboard', 'action' => 'index']);
                        } else {
                            $this->Attempt->fail($username, 'login', $this->loginAttemptDuration);
                            $this->Flash->error(__('Invalid password. Too many failed attempts will lock your account.'));
                        }
                    } else {
                        $this->Flash->error(__('Please enter the correct answer to the math question.'));
                    }
                    $this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
                }
            } else {
                $this->Flash->error(__('Account with username "' . $username . '" not found. Check spelling or typo errors.'));
            }
        }
    }
    public function forget()
    {
        $this->viewBuilder()->setLayout('home_new');
        // Redirect logged-in users to change password
        if ($this->Auth->user()) {
            $this->Flash->error('You do not need to use forget password while you are logged in. Please use the change password form instead!');
            return $this->redirect(['action' => 'changePwd']);
        }

        // Set layout
        $this->viewBuilder()->setLayout('login');

        if ($this->request->is('post')) {
            $email = strtolower(trim($this->request->getData('email')));

            if (empty($email)) {
                $this->Flash->error('Please enter an email address.');
            } else {
                // Validate math captcha
                if ($this->MathCaptcha->validates($this->request->getData('security_code'))) {
                    $this->loadModel('Users');

                    // Find users with the given email
                    $userCount = $this->Users->find()->where(['email' => $email])->count();

                    if ($userCount == 0) {
                        $this->Flash->error('Sorry, the system couldn\'t find your email address.');
                        return $this->redirect('/');
                    }

                    if ($userCount >= 1) {
                        $user = $this->Users->find()
                            ->contain(['Students', 'Staffs'])
                            ->where(['Users.email' => $email, 'Users.active' => 1])
                            ->order(['Users.created' => 'DESC'])
                            ->first();

                        if (!$user) {
                            $this->Flash->error('There are accounts registered with your email, but none of them are active!');
                            return $this->redirect('/');
                        }

                        if (!isset($user->email)) {
                            $this->Flash->error('Sorry, the system couldn\'t find your email address. Make sure you are using the email you provided when your account was created.');
                            return $this->redirect('/');
                        }

                        if (!$user->active) {
                            $this->Flash->error('This account is deactivated. Please contact your system administrator.');
                            return $this->redirect('/');
                        }

                        // Generate reset token
                        $hashyToken = Security::hash(date('mdY') . rand(4000000, 4999999), 'sha256', true);
                        $message = $this->Ticketmaster->createMessage($hashyToken);

                        // Send email using CakePHP Email class
                        $emailInstance = new Email('default');
                        $emailInstance->setTemplate('password_reset')
                            ->setEmailFormat('html')
                            ->setTo($email)
                            ->setSubject('SMiS Password Reset')
                            ->setViewVars(['message' => $message]);

                        if ($emailInstance->send()) {
                            $this->Flash->success('Check your email. The password reset email has been sent successfully to ' . $email);
                        } else {
                            $this->Flash->error('Email not sent. Please check your email SMTP settings and ensure the email server is running.');
                        }

                        // Save ticket
                        $this->loadModel('Tickets');
                        $ticket = $this->Tickets->newEntity([
                            'hash' => $hashyToken,
                            'data' => $email,
                            'expires' => $this->Ticketmaster->getExpirationDate()
                        ]);

                        if ($this->Tickets->save($ticket)) {
                            $this->Flash->success('An email has been sent to "' . $email . '" with instructions to reset your SMiS password. Please check your email!');
                            return $this->redirect('/');
                        } else {
                            $this->Flash->error('Ticket could not be issued. Please try again later.');
                            return $this->redirect('/');
                        }
                    }
                } else {
                    $this->Flash->error('Please enter the correct answer to the math question.');
                }
            }
        }

        // Generate math captcha
        $this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
    }

    public function logout()
    {

        $session = $this->request->getSession();
        $session->destroy(); // Ensure session is cleared

        return $this->redirect($this->Auth->logout());
    }

    public function changePwd()
    {


        if (!$this->Auth->user('id')) {
            $this->request->getSession()->destroy();
            return $this->redirect($this->Auth->logout());
        }
        $securitysetting = $this->getTableLocator()->get('Securitysettings')->find()->first();

        if ($this->request->is('post')) {

            $password_strength = $this->Users->doesItFullfillPasswordStrength($this->request->getData('passwd'),
                $securitysetting);
            $password_used = $this->getTableLocator()->get('PasswordHistories')->isThePasswordUsedBefore(
                $this->Auth->user('id'), $this->request->getData('passwd'));

            if (!empty($this->request->getData('password2'))
                && !empty($this->request->getData('passwd'))) {
                $passwd = $this->request->getData('passwd');
                $passwd2 = $this->request->getData('password2');
                if (strcmp($passwd, $passwd2) != 0) {
                    $this->request->data = null;
                    $this->Flash->error('Password change is failed. You entered two different passwords, please try again.');
                } else {
                    $user = $this->Users->get($this->Auth->user('id'));
                    // In your controller method
                    $hasher = new DefaultPasswordHasher();
                    $user->oldpassword = $hasher->hash($this->request->getData('oldpassword'));


                    if ($this->Users->veryifyOldPassword($user)) {
                        if (strlen($this->request->getData('passwd')) >= $securitysetting->minimum_password_length && strlen($this->request->getData('passwd')) <= $securitysetting->maximum_password_length) {
                            if ($password_strength) {
                                if ($securitysetting->previous_password_use_allowance == 1 || !$password_used) {
                                    $passwordHistory = $this->getTableLocator()->get('PasswordHistories')->newEntity();
                                    $passwordHistory->user_id = $this->Auth->user('id');
                                    $passwordHistory->password = $user->password;
                                    $this->getTableLocator()->get('PasswordHistories')->save($passwordHistory);

                                    $moodle_message = '';
                                    if (defined('ENABLE_MOODLE_INTEGRATION') && ENABLE_MOODLE_INTEGRATION == 1 && !empty($user->moodle_user->user_id)) {
                                        $moodleUseremail = trim($user->email);
                                        if (trim($user->email) !== trim($user->moodle_user->email)) {
                                            if ($this->Auth->user('role_id') == ROLE_STUDENT) {
                                                $moodleUseremail = isset($user->student[0]->email) && !empty(trim($user->student[0]->email)) ? trim($user->student[0]->email) : (str_replace('/', '.', strtolower(trim($user->username))) . INSTITUTIONAL_EMAIL_SUFFIX);
                                            } else {
                                                $moodleUseremail = isset($user->staff[0]->email) && !empty(trim($user->staff[0]->email)) ? trim($user->staff[0]->email) : (strtolower(trim($user->staff[0]->first_name)) . '.' . strtolower(trim($user->staff[0]->middle_name)) . INSTITUTIONAL_EMAIL_SUFFIX);
                                            }
                                        }
                                        $moodleUseremail = '"' . trim($moodleUseremail) . '"';
                                        $newPassword = '"' . (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'sha1' ? sha1($passwd) : (MOODLE_PASSWORD_ENCRYPRION_ALGORITHM == 'md5' ? md5($passwd) : md5($passwd))) . '"';
                                        $modified_date_time = "'" . (new FrozenTime())->format('Y-m-d H:i:s') . "'";
                                        try {
                                            if ($this->getTableLocator()->get('MoodleUsers')->updateAll([
                                                'password' => $newPassword,
                                                'email' => $moodleUseremail,
                                                'modified' => $modified_date_time
                                            ], ['user_id' => $this->Auth->user('id')])) {
                                                $moodle_message = ' You can also login to ' . MOODLE_SITE_URL . ' with the same password you set here now by using a username: ' . $user->moodle_user->username . '';
                                            } else {
                                                $moodle_message = ' But your e-Learning password update failed. Please contact site administrator if this error persists.';
                                            }
                                        } catch (\Exception $e) {
                                            $moodle_message = ' But your e-Learning password update failed. Please contact site administrator if this error persists.';
                                        }
                                    }
                                    $user->force_password_change = 0;
                                    $user->last_password_change_date = new FrozenTime();
                                    if ($this->Users->save($user)) {
                                        $this->Flash->success('Your Password changed successfully.' . $moodle_message);
                                        return $this->redirect('/');
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
                            $this->Flash->error('Password policy: Your password should be greater than or equal to ' . $securitysetting->minimum_password_length . ' and less than or equal to ' . $securitysetting->maximum_password_length . '.');
                        }
                    } else {
                        $errors = $this->Users->getErrors();
                        if (isset($errors['invaliduser'])) {
                            $this->Flash->error($errors['invaliduser'][0]);
                        }
                    }
                }
            }  else {
                $this->Flash->error('Please provide your password.');
            }

        }

        $this->set(compact('securitysetting'));


    }

    public function buildUserMenu($userId = null)
    {
        $session = $this->request->getSession();
        if (!$session->check('Auth.User.id')) {
            $session->destroy();
            return $this->redirect($this->Auth->logout());
        }

        // It is used to ignore recorded number of processes which are older than 1 hour to avoid stacked processes
        $lastProcessDate = date('Y-m-d H:i:s', mktime(date("H"), date("i") - 20, date("s"), date("n"), date("j"), date("Y")));
        $numberProcessTable = TableRegistry::getTableLocator()->get('NumberProcesses');
        $numberProcess = $numberProcessTable->find()
            ->where(['NumberProcesses.created >' => $lastProcessDate])
            ->count();

        $numberOfUserInitiatedProcess = $numberProcessTable->find()
            ->where([
                'NumberProcesses.created >' => $lastProcessDate,
                'NumberProcesses.initiated_by' => $this->Auth->user('id')
            ])
            ->count();

        // One administrator is allowed to run a maximum of one menu building task
        if ($numberOfUserInitiatedProcess <= 0) {
            if ($numberProcess < Configure::read('NumberProcessAllowedToRunProfile')) {
                $numberProcessTable->recordAsRunning($userId, $this->Auth->user('id'));
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $runningUsers = $usersTable->find()
                    ->where(['Users.id' => $userId])
                    ->first();

                // Construct the menus From the Controllers in the Application. This is an expensive Process Timewise and is cached.
                $session->delete('permissionLists'); // clear menu cache if existed
                $this->_clearMenuCache($userId);
                $this->MenuOptimized->constructMenu($userId);
                $numberProcessTable->jobDoneDelete($userId);
                $this->Flash->success('The system build the selected user menu successfully based on assigned user privilege.');
            } else {
                $this->Flash->info('The system is busy handling user menu construct requests. Please come back after some minutes to construct user menu.');
            }
        } else {
            $this->Flash->info('You already has menu construction request being handled by the system. Please be patient till the system finish the requested menu construction task to initiate another menu construction request.');
        }

        $this->redirect(['action' => 'index']);
    }

    protected function _clearMenuCache($userId = null): void
    {
        $folder = new Folder(Configure::read('Utility.cache'));
        $files = $folder->find('menu_storageuser' . $userId . '.*', true);
        if (!empty($files)) {
            foreach ($files as $file) {
                $output = shell_exec('rm ' . $file . " 2>&1");
            }
        }
    }

    public function checkSession()
    {

        $this->autoRender = false; // Prevent rendering a view
        $this->request->allowMethod(['post', 'get']); // Restrict to GET and POST requests

        $isLoggedIn = $this->request->getSession()->read('User.is_logged_in') ?? false;

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['is_logged_in' => $isLoggedIn]));
    }

    protected function createEmailMessage($staffDetails, $password, $passwordReset)
    {
        return "Welcome to SMiS! Your username: {$staffDetails->username}, Password: {$password}";
    }

    private function flattenErrors($errors, $prefix = '')
    {
        $messages = [];
        foreach ($errors as $field => $error) {
            if (is_array($error)) {
                $messages = array_merge($messages, $this->flattenErrors($error, $prefix . $field . '.'));
            } else {
                $messages[] = $prefix . $field . ': ' . $error;
            }
        }
        return $messages;
    }
}

