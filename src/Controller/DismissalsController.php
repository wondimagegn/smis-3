<?php
class DismissalsController extends AppController {

	var $name = 'Dismissals';

	function index() {
		$this->Dismissal->recursive = 0;
		$this->set('dismissals', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid dismissal'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('dismissal', $this->Dismissal->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Dismissal->create();
			if ($this->Dismissal->save($this->request->data)) {
				$this->Session->setFlash(__('The dismissal has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The dismissal could not be saved. Please, try again.'));
			}
		}
		$students = $this->Dismissal->Student->find('list');
		$this->set(compact('students'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid dismissal'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Dismissal->save($this->request->data)) {
				$this->Session->setFlash(__('The dismissal has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The dismissal could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Dismissal->read(null, $id);
		}
		$students = $this->Dismissal->Student->find('list');
		$this->set(compact('students'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for dismissal'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->Dismissal->delete($id)) {
			$this->Session->setFlash(__('Dismissal deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Dismissal was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
