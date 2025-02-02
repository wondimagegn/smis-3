<?php
class TitlesController extends AppController {

	var $name = 'Titles';
    var $menuOptions = array(
            'parent' => 'mainDatas',
             'alias' => array(
                    'index'=>'View Title',
                    'add'=>'Add Title',
                )
    );
	function index() {
		$this->Title->recursive = 0;
		$this->set('titles', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid title'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('title', $this->Title->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Title->create();
			if ($this->Title->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The title has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The title could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid title'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Title->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The title has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The title could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Title->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for title'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->Title->delete($id)) {
			$this->Session->setFlash('<span></span>'.__('Title deleted'),
			'default',array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Title was not deleted'),
		'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
}
