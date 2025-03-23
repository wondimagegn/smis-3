<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Utility\Security;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Cache\Cache;

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
            'editprofile',
            'checkSession'
        ),
        'alias' => array(
            'index' => 'List All Users',
            'add' => 'Create User',
            'changePwd' => 'Change Your Password',
            'department_create_user_account' => 'Create User Account'
        ),
        'weight' => -2,
    );

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Attempt');
        $this->loadComponent('MathCaptcha');
        $this->loadComponent('Paginator');
        $this->Auth->allow(['login', 'logout', 'forget', 'search', 'resetpassword', 'checkSession']);
    }

    public $loginAttemptLimit = 3;
    public $loginAttemptDuration = '+5 minutes';

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Delete auth flash message
        if ($this->getRequest()->getSession()->check('Message.auth')) {
            $this->getRequest()->getSession()->delete('Message.auth');
        }

        // If already logged in, redirect away from login page
        /*
        if ($this->Auth->user() && $this->request->getParam('action') === 'login') {
            $this->getRequest()->getSession()->destroy();
            return $this->redirect($this->Auth->logout());
        }
        */
    }

    public function search()
    {
        $this->__initSearchIndex();

        $url = ['action' => 'index'];

        if ($this->getRequest()->is('post')) {
            $data = $this->getRequest()->getData();
            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    foreach ($value as $subKey => $subValue) {
                        if (!empty($subValue) && is_array($subValue)) {
                            foreach ($subValue as $subSubKey => $subSubValue) {
                                $url[$key . '.' . $subKey . '.' . $subSubKey] = str_replace('/', '-', trim($subSubValue));
                            }
                        } else {
                            $url[$key . '.' . $subKey] = str_replace('/', '-', trim($subValue));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }

    private function __initSearchIndex()
    {
        $session = $this->getRequest()->getSession();

        if ($this->getRequest()->is('post') && !empty($this->getRequest()->getData('Search'))) {
            $session->write('search_data_users_index', $this->getRequest()->getData());
        } elseif ($session->check('search_data_users_index')) {
            $this->request = $this->request->withData('Search', $session->read('search_data_users_index'));
        }
    }

    private function __initClearSessionFilters()
    {
        $this->getRequest()->getSession()->delete('search_data_users_index');
    }

    private function __initClearRoleIdAndActiveFieldsIfSetInSession()
    {
        $this->getRequest()->getSession()->delete('search_data_users_index');
    }

    public function login()
    {
        $this->viewBuilder()->setLayout('home');
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

    public function checkSession()
    {

        $this->autoRender = false; // Prevent rendering a view
        $this->request->allowMethod(['post', 'get']); // Restrict to GET and POST requests

        $isLoggedIn = $this->request->getSession()->read('User.is_logged_in') ?? false;

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['is_logged_in' => $isLoggedIn]));
    }
}
