<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Security;
use Cake\Validation\Validator;

class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
        $this->Auth->allow(['login', 'logout', 'search', 'resetpassword']);
    }

    public $loginAttemptLimit = 3;
    public $loginAttemptDuration = '+5 minutes';

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        // Ensure passwords are not sent to views
        unset($this->request->data['password']);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Delete auth flash message
        if ($this->getRequest()->getSession()->check('Message.auth')) {
            $this->getRequest()->getSession()->delete('Message.auth');
        }

        // If already logged in, redirect away from login page
        if ($this->Auth->user() && $this->request->getParam('action') === 'login') {
            $this->getRequest()->getSession()->destroy();
            return $this->redirect($this->Auth->logout());
        }
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
            $user = $this->Users->find()
                ->where(['username' => $username])
                ->select(['id', 'username', 'is_admin', 'role_id', 'active', 'failed_login'])
                ->first();

            if ($user) {
                $failedLogins = $user->failed_login;

                if ($this->Attempt->limit($username, 'login', $number_of_login_attempt)) {
                    if ($this->Auth->identify()) {
                        if ($user->active) {
                            $this->Auth->setUser($user);
                            $user->failed_login = 0;
                            $user->last_login = date('Y-m-d H:i:s');
                            $this->Users->save($user);
                            $session->write('User.is_logged_in', true);

                            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
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
                        $user->failed_login++;
                        $this->Users->save($user);
                        $this->Flash->error(__('Your password is incorrect. Please try again.'));
                        $this->Attempt->fail($username, 'login', $this->loginAttemptDuration);
                    }
                } else {
                    // Too many failed attempts
                    $this->Flash->error(__('Too many failed attempts! (' . $failedLogins . ')'));

                    if (!empty($data['security_code']) && $this->MathCaptcha->validates($data['security_code'])) {
                        if ($this->Auth->identify()) {
                            $this->Auth->setUser($user);
                            $user->last_login = date('Y-m-d H:i:s');
                            $this->Users->save($user);
                            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
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
    public function logout()
    {
        $session = $this->getRequest()->getSession();
        $session->destroy();

        return $this->redirect($this->Auth->logout());
    }

}
