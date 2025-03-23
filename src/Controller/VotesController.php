<?php

namespace App\Controller;

use Cake\Event\Event;


class VotesController extends AppController
{

    public $name = 'Votes';
    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function index()
    {

        $this->Vote->recursive = 0;
        $this->set('votes', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid vote'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('vote', $this->Vote->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Vote->create();
            if ($this->Vote->save($this->request->data)) {
                $this->Session->setFlash(__('The vote has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The vote could not be saved. Please, try again.'));
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid vote'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Vote->save($this->request->data)) {
                $this->Session->setFlash(__('The vote has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The vote could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Vote->read(null, $id);
        }
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for vote'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Vote->delete($id)) {
            $this->Session->setFlash(__('Vote deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Vote was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
