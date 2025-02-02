<?php
class HighSchoolEducationBackgroundsController extends AppController {

	var $name = 'HighSchoolEducationBackgrounds';

	function index() {
		$this->HighSchoolEducationBackground->recursive = 0;
		$this->set('highSchoolEducationBackgrounds', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid high school education background'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('highSchoolEducationBackground', $this->HighSchoolEducationBackground->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->HighSchoolEducationBackground->create();
			if ($this->HighSchoolEducationBackground->save($this->request->data)) {
				$this->Session->setFlash(__('The high school education background has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The high school education background could not be saved. Please, try again.'));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid high school education background'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->HighSchoolEducationBackground->save($this->request->data)) {
				$this->Session->setFlash(__('The high school education background has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The high school education background could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->HighSchoolEducationBackground->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for high school education background'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->HighSchoolEducationBackground->delete($id)) {
			$this->Session->setFlash(__('High school education background deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('High school education background was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
