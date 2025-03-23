<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class StudentsDepartmentsController extends AppController {

	var $name = 'StudentsDepartments';
    public $paginate =[];
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

	function index() {
		$this->StudentsDepartment->recursive = 0;
		$this->set('studentsDepartments', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid students department'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('studentsDepartment', $this->StudentsDepartment->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->StudentsDepartment->create();
			if ($this->StudentsDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The students department has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The students department could not be saved. Please, try again.'));
			}
		}
		$colleges = $this->StudentsDepartment->College->find('list');
		$departments = $this->StudentsDepartment->Department->find('list');
		$students = $this->StudentsDepartment->Student->find('list');
		$this->set(compact('colleges', 'departments', 'students'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid students department'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->StudentsDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The students department has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The students department could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->StudentsDepartment->read(null, $id);
		}
		$colleges = $this->StudentsDepartment->College->find('list');
		$departments = $this->StudentsDepartment->Department->find('list');
		$students = $this->StudentsDepartment->Student->find('list');
		$this->set(compact('colleges', 'departments', 'students'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for students department'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->StudentsDepartment->delete($id)) {
			$this->Session->setFlash(__('Students department deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Students department was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
