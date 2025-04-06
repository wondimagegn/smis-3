<?php

namespace App\Controller;

use Cake\Event\Event;


class SecuritysettingsController extends AppController
{

    public $name = 'Securitysettings';

    public $menuOptions = array(
        'parent' => 'security',
        'exclude' => array('index'),
        'alias' => array(
            'view_ss' => 'Site Security Settings',
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function permissionManagement()
    {

        $this->redirect(array(
            'plugin' => 'acls',
            'controller' => 'acos',
            'action' => 'index',
        ));
    }

    public function index()
    {

        return $this->redirect(array('action' => 'view_ss'));
    }

    public function view_ss()
    {

        $this->set('securitysetting', $this->Securitysetting->find('first'));
    }

    public function edit()
    {

        if (!empty($this->request->data)) {
            if ($this->request->data['Securitysetting']['minimum_password_length'] <= $this->request->data['Securitysetting']['maximum_password_length']) {
                if ($this->Securitysetting->save($this->request->data)) {
                    $this->Flash->success('Security settings has been updated.');
                    return $this->redirect(array('action' => 'view_ss'));
                } else {
                    $this->Flash->error('Security settings could not be updated. Please, try again.');
                }
            } else {
                $this->Flash->error(
                    'Minimum password length can not be greater than maximum password length. Please, try again.'
                );
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->Securitysetting->find('first');
        }
        for ($i = 8; $i <= 30; $i++) {
            $min_password_length[$i] = $i;
        }
        for ($i = 10; $i <= 40; $i++) {
            $max_password_length[$i] = $i;
        }
        for ($i = 30; $i <= 240; $i++) {
            $password_duration[$i] = $i . ' Days';
        }
        for ($i = 60; $i <= 240; $i++) {
            $session_duration[$i] = $i . ' Minutes';
        }

        $password_strength[1] = 'Should Contain Uppercase Letters, Lowercase Letters and Numbers';
        $password_strength[2] = 'Should Containing Uppercase Letters, Lowercase Letters, Numbers and Symbols';
        $this->set(
            compact(
                'min_password_length',
                'max_password_length',
                'password_strength',
                'password_duration',
                'session_duration'
            )
        );
    }
}
