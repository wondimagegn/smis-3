<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PrerequisitesController extends AppController
{

    public $name = 'Prerequisites';
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

        $this->Prerequisite->recursive = 0;
        $this->set('prerequisites', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid prerequisite'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('prerequisite', $this->Prerequisite->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Prerequisite->create();
            if ($this->Prerequisite->save($this->request->data)) {
                $this->Session->setFlash(__('The prerequisite has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The prerequisite could not be saved. Please, try again.'));
            }
        }
        $courses = $this->Prerequisite->Course->find('list');
        $this->set(compact('courses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid prerequisite'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Prerequisite->save($this->request->data)) {
                $this->Session->setFlash(__('The prerequisite has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The prerequisite could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Prerequisite->read(null, $id);
        }
        $courses = $this->Prerequisite->Course->find('list');
        $this->set(compact('courses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for prerequisite'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Prerequisite->delete($id)) {
            $this->Session->setFlash(__('Prerequisite deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Prerequisite was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}

?>
