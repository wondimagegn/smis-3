<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class NotesController extends AppController
{

    public $name = 'Notes';
    public $menuOptions = array(
        'parent' => 'campuses',
        'alias' => array(
            'index' => 'View Notes',
            'add' => "Add Note"
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

    public function index()
    {

        $this->Note->recursive = 0;
        $this->set('notes', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid note'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('note', $this->Note->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Note->create();
            if ($this->Note->save($this->request->data)) {
                $this->Session->setFlash(__('The note has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The note could not be saved. Please, try again.'));
            }
        }
        $colleges = $this->Note->College->find('list');
        $departments = $this->Note->Department->find('list');
        $users = $this->Note->User->find('list');
        $this->set(compact('colleges', 'departments', 'users'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid note'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Note->save($this->request->data)) {
                $this->Session->setFlash(__('The note has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The note could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Note->read(null, $id);
        }
        $colleges = $this->Note->College->find('list');
        $departments = $this->Note->Department->find('list');
        $users = $this->Note->User->find('list');
        $this->set(compact('colleges', 'departments', 'users'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for note'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Note->delete($id)) {
            $this->Session->setFlash(__('Note deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Note was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
