<?php

namespace App\Controller;

use Cake\Event\Event;

class RolesController extends AppController
{

    public $name = 'Roles';

    public $menuOptions = array(
        'parent' => 'security',
        'alias' => array(
            'index' => 'List All Roles',
            'add' => 'Create Role'
        ),
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('*');
    }

    public function index()
    {

        $this->Role->recursive = 0;
        $this->set('roles', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('<span></span> Invalid role.'),
                'default',
                array('class' => 'error-box error-message')
            );

            return $this->redirect(array('action' => 'index'));
        }
        $this->set('role', $this->Role->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Role->create();
            if ($this->Role->save($this->request->data)) {
                //$this->Session->setFlash(__('The role has been saved'));
                $this->Session->setFlash(
                    __('<span></span> The role has been saved!'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('<span></span> The role could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }

        $this->set('roles', $this->Role->find('list'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                __('<span></span> Invalid role.'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Role->save($this->request->data)) {
                $this->Session->setFlash(
                    __('<span></span> The role has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('<span></span> The role could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Role->read(null, $id);
        }
        $this->set('roles', $this->Role->find('list'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('<span></span> Invalid id for role.'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Role->delete($id)) {
            $this->Session->setFlash(
                __('<span></span> Role deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(
            __('<span></span> Role was not deleted.'),
            'default',
            array('class' => 'error-box error-message')
        );

        return $this->redirect(array('action' => 'index'));
    }

}
