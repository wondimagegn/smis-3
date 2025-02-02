<?php
class WeblinksController extends AppController {

	var $name = 'Weblinks';
    var $menuOptions = array(
    'controllerButton' => false,
);
	function index() {
		$this->Weblink->recursive = 0;
		$this->set('weblinks', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid weblink'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('weblink', $this->Weblink->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Weblink->create();
			if ($this->Weblink->save($this->request->data)) {
				$this->Session->setFlash(__('The weblink has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The weblink could not be saved. Please, try again.'));
			}
		}
		$courses = $this->Weblink->Course->find('list');
		$this->set(compact('courses'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid weblink'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Weblink->save($this->request->data)) {
				$this->Session->setFlash(__('The weblink has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The weblink could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Weblink->read(null, $id);
		}
		$courses = $this->Weblink->Course->find('list');
		$this->set(compact('courses'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for weblink'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->Weblink->delete($id)) {
			$this->Session->setFlash(__('Weblink deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Weblink was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
