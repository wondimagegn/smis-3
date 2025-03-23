<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PasswordChanageVotesController extends AppController
{

    public $name = 'PasswordChanageVotes';

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

    public function index()
    {

        $this->PasswordChanageVote->recursive = 0;
        $this->set('passwordChanageVotes', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid password chanage vote'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('passwordChanageVote', $this->PasswordChanageVote->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->PasswordChanageVote->create();
            if ($this->PasswordChanageVote->save($this->request->data)) {
                $this->Session->setFlash(__('The password chanage vote has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The password chanage vote could not be saved. Please, try again.'));
            }
        }
        $users = $this->PasswordChanageVote->User->find('list');
        $roles = $this->PasswordChanageVote->Role->find('list');
        $this->set(compact('users', 'roles'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid password chanage vote'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->PasswordChanageVote->save($this->request->data)) {
                $this->Session->setFlash(__('The password chanage vote has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The password chanage vote could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->PasswordChanageVote->read(null, $id);
        }
        $users = $this->PasswordChanageVote->User->find('list');
        $roles = $this->PasswordChanageVote->Role->find('list');
        $this->set(compact('users', 'roles'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for password chanage vote'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->PasswordChanageVote->delete($id)) {
            $this->Session->setFlash(__('Password chanage vote deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Password chanage vote was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
