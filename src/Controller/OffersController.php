<?php
class OffersController extends AppController {

	var $name = 'Offers';

	function index() {
		$this->Offer->recursive = 0;
		$this->set('offers', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid offer'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('offer', $this->Offer->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Offer->create();
			if ($this->Offer->save($this->request->data)) {
				$this->Session->setFlash(__('The offer has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The offer could not be saved. Please, try again.'));
			}
		}
		$departments = $this->Offer->Department->find('list');
		$programTypes = $this->Offer->ProgramType->find('list');
		$this->set(compact('departments', 'programTypes'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid offer'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Offer->save($this->request->data)) {
				$this->Session->setFlash(__('The offer has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The offer could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Offer->read(null, $id);
		}
		$departments = $this->Offer->Department->find('list');
		$programTypes = $this->Offer->ProgramType->find('list');
		$this->set(compact('departments', 'programTypes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for offer'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->Offer->delete($id)) {
			$this->Session->setFlash(__('Offer deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Offer was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
