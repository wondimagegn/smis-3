<?php
class NotesController extends AppController {

	var $name = 'Notes';
    var $menuOptions = array(
        'parent' => 'campuses',
                'alias' => array(
                    'index' => 'View Notes',
                    'add' => "Add Note"
                )
    );
    /*
      var $menuOptions = array(
             'parent' => 'campuses',
             'exclude' => array('index'),
             'alias' => array(
                    'add' => 'Add College',
            )
    );
    */
   
	function index() {
		$this->Note->recursive = 0;
		$this->set('notes', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid note'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('note', $this->Note->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Note->create();
			if ($this->Note->save($this->request->data)) {
				$this->Session->setFlash(__('The note has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The note could not be saved. Please, try again.'));
			}
		}
		$colleges = $this->Note->College->find('list');
		$departments = $this->Note->Department->find('list');
		$users = $this->Note->User->find('list');
		$this->set(compact('colleges', 'departments', 'users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid note'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Note->save($this->request->data)) {
				$this->Session->setFlash(__('The note has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The note could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Note->read(null, $id);
		}
		$colleges = $this->Note->College->find('list');
		$departments = $this->Note->Department->find('list');
		$users = $this->Note->User->find('list');
		$this->set(compact('colleges', 'departments', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for note'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->Note->delete($id)) {
			$this->Session->setFlash(__('Note deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Note was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
