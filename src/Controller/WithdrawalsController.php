<?php
class WithdrawalsController extends AppController {

	var $name = 'Withdrawals';

	function index() {
		$this->Withdrawal->recursive = 0;
		$this->set('withdrawals', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid withdrawal'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('withdrawal', $this->Withdrawal->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Withdrawal->create();
			if ($this->Withdrawal->save($this->request->data)) {
				$this->Session->setFlash(__('The withdrawal has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The withdrawal could not be saved. Please, try again.'));
			}
		}
		$students = $this->Withdrawal->Student->find('list');
		$this->set(compact('students'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid withdrawal'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Withdrawal->save($this->request->data)) {
				$this->Session->setFlash(__('The withdrawal has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The withdrawal could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Withdrawal->read(null, $id);
		}
		$students = $this->Withdrawal->Student->find('list');
		$this->set(compact('students'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for withdrawal'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->Withdrawal->delete($id)) {
			$this->Session->setFlash(__('Withdrawal deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Withdrawal was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
