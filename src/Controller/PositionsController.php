<?php
class PositionsController extends AppController {
	var $name = 'Positions';
	var $menuOptions = array(
		'parent' => 'mainDatas',
		'alias' => array(
			'index' => 'View Position',
			'add' => 'Add Position',
		)
	);
	function index() 
	{
		$this->Position->recursive = 0;
		$this->set('positions', $this->paginate());
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error(__('Invalid position'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('position', $this->Position->read(null, $id));
	}

	function add()
	{
		if (!empty($this->request->data)) {

			if (!empty($this->request->data['Position']['applicable_educations'])) {
				$this->request->data['Position']['applicable_educations'] = serialize($this->request->data['Position']['applicable_educations']);
			}

			$this->Position->create();

			if ($this->Position->save($this->request->data)) {
				$this->Flash->success(__('The position has been saved') .' '. $this->Position->name);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The position could not be saved. Please, try again.'));
			}
		}
		
		$serviceWings = $this->Position->ServiceWing->find('list', array('conditions' => array('ServiceWing.active' => 1)));
		$applicableEducations = ClassRegistry::init('Education')->find('list', array('conditions' => array('Education.active' => 1)));

		$this->set(compact('applicableEducations', 'serviceWings'));
	}

	function edit($id = null) 
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__('Invalid position'));
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {

			if (!empty($this->request->data['Position']['applicable_educations'])) {
				$this->request->data['Position']['applicable_educations'] = serialize($this->request->data['Position']['applicable_educations']);
			}

			if ($this->Position->save($this->request->data)) {
				$this->Flash->success(__('The position has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The position could not be saved. Please, try again.'));
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Position->read(null, $id);
			if (!empty($this->request->data['Position']['applicable_educations'])) {
				$this->request->data['Position']['applicable_educations'] = unserialize($this->request->data['Position']['applicable_educations']);
			}
		}

		$serviceWings = $this->Position->ServiceWing->find('list', array('conditions' => array('ServiceWing.active' => 1)));
		$applicableEducations = ClassRegistry::init('Education')->find('list', array('conditions' => array('Education.active' => 1)));
		$this->set(compact('applicableEducations', 'serviceWings'));
	}

	function delete($id = null) 
	{
		if (!$id) {
			$this->Flash->error(__('Invalid id for position'));
			return $this->redirect(array('action'=>'index'));
		}

		if ($this->Position->delete($id)) {
			$this->Flash->success(__('Position deleted'));
			return $this->redirect(array('action'=>'index'));
		}

		$this->Flash->error(__('Position was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
}
