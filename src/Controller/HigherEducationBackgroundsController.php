<?php
class HigherEducationBackgroundsController extends AppController {

	var $name = 'HigherEducationBackgrounds';

	function index() {
		$this->HigherEducationBackground->recursive = 0;
		$this->set('higherEducationBackgrounds', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid higher education background'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('higherEducationBackground', $this->HigherEducationBackground->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->HigherEducationBackground->create();
			if ($this->HigherEducationBackground->save($this->request->data)) {
				$this->Session->setFlash(__('The higher education background has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The higher education background could not be saved. Please, try again.'));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid higher education background'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->HigherEducationBackground->save($this->request->data)) {
				$this->Session->setFlash(__('The higher education background has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The higher education background could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->HigherEducationBackground->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for higher education background'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->HigherEducationBackground->delete($id)) {
			$this->Session->setFlash(__('Higher education background deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Higher education background was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
