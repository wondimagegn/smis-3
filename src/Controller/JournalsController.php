<?php

namespace App\Controller;

use Cake\Event\Event;


class JournalsController extends AppController
{

    public $name = 'Journals';

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

        $this->Journal->recursive = 0;
        $this->set('journals', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid journal'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('journal', $this->Journal->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Journal->create();
            if ($this->Journal->save($this->request->data)) {
                $this->Session->setFlash(__('The journal has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The journal could not be saved. Please, try again.'));
            }
        }
        $courses = $this->Journal->Course->find('list');
        $this->set(compact('courses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid journal'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Journal->save($this->request->data)) {
                $this->Session->setFlash(__('The journal has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The journal could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Journal->read(null, $id);
        }
        $courses = $this->Journal->Course->find('list');
        $this->set(compact('courses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for journal'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Journal->delete($id)) {
            $this->Session->setFlash(__('Journal deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Journal was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
