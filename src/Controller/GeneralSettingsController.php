<?php
App::uses('AppController', 'Controller');
class GeneralSettingsController extends AppController
{

	public $menuOptions = array(
		'parent' => 'dashboard',
		'alias' => array(
			'index' => 'List General Settings',
			'add' => 'Set General Settings',
		)
	);

	public $components = array('Paginator');

	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->Auth->Allow();
	}

	public function beforeRender()
	{
		parent::beforeRender();
	}

	public function index()
	{
		debug($this->GeneralSetting->notifyStudentsGradeByEmail(2));

		$this->GeneralSetting->recursive = 0;
		$generalSetting = $this->Paginator->paginate();

		if (!empty($generalSetting)) {
			foreach ($generalSetting as $ack => &$ackv) {
				$programs = $this->GeneralSetting->Program->find('list', array('conditions' => array('id' => unserialize($ackv['GeneralSetting']['program_id']))));
				$programTypes = $this->GeneralSetting->ProgramType->find('list', array('conditions' => array('id' => unserialize($ackv['GeneralSetting']['program_type_id']))));
				$ackv['GeneralSetting']['program_id'] = array_values($programs);
				$ackv['GeneralSetting']['program_type_id'] = array_values($programTypes);
			}
		}

		$this->set('generalSettings', $generalSetting);
	}

	public function view($id = null)
	{
		if (!$this->GeneralSetting->exists($id)) {
			throw new NotFoundException(__('Invalid general setting'));
		}

		$options = array('conditions' => array('GeneralSetting.' . $this->GeneralSetting->primaryKey => $id));
		$generalSetting = $this->GeneralSetting->find('first', $options);
		$generalSetting['GeneralSetting']['program_type_id'] = unserialize($generalSetting['GeneralSetting']['program_type_id']);
		$generalSetting['GeneralSetting']['program_id'] = unserialize($generalSetting['GeneralSetting']['program_id']);

		$this->set('generalSetting', $generalSetting);
	}

	public function add()
	{
		if ($this->request->is('post')) {
			$this->GeneralSetting->create();
			$this->request->data['GeneralSetting']['program_id'] = serialize($this->request->data['GeneralSetting']['program_id']);
			$this->request->data['GeneralSetting']['program_type_id'] = serialize($this->request->data['GeneralSetting']['program_type_id']);

			if ($this->GeneralSetting->check_duplicate_entry($this->request->data)) {
				if ($this->GeneralSetting->save($this->request->data)) {
					$this->Flash->success('The general setting has been saved.');
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The general setting could not be saved. Please, try again.');
				}
			} else {
				$error = $this->GeneralSetting->invalidFields();
				if (isset($error['duplicateEntries'])) {
					$this->Flash->error($error['duplicateEntries'][0][0]);
				}
			}

			$this->request->data['GeneralSetting']['program_id'] = unserialize($this->request->data['GeneralSetting']['program_id']);
			$this->request->data['GeneralSetting']['program_type_id'] = unserialize($this->request->data['GeneralSetting']['program_type_id']);
		}

		$programs = $this->GeneralSetting->Program->find('list');
		$programTypes = $this->GeneralSetting->ProgramType->find('list');

		$this->set(compact('programs', 'programTypes'));
	}

	public function edit($id = null)
	{
		if (!$this->GeneralSetting->exists($id)) {
			throw new NotFoundException(__('Invalid general setting'));
		}

		if ($this->request->is(array('post', 'put'))) {

			$this->request->data['GeneralSetting']['program_id'] = serialize($this->request->data['GeneralSetting']['program_id']);
			$this->request->data['GeneralSetting']['program_type_id'] = serialize($this->request->data['GeneralSetting']['program_type_id']);

			if ($this->GeneralSetting->check_duplicate_entry($this->request->data)) {
				if ($this->GeneralSetting->save($this->request->data)) {
					$this->Flash->success('The general setting has been saved.');
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The general setting could not be saved. Please, try again.');
				}
			} else {
				$error = $this->GeneralSetting->invalidFields();

				if (isset($error['duplicateEntries'])) {
					$this->Flash->error($error['duplicateEntries'][0][0]);
				}

				/* if (isset($error['duplicate'][0][0])) {
					$this->Flash->error($error['duplicate']. ' Those unchecked red marked department has an academic calendar for the given criteria .');
				} 
				
				$this->set('duplicateProgram', $error['duplicateProgram'][0]);
				$this->set('duplicateProgramTypes', $error['duplicateProgramTypes'][0]); */

				$this->request->data = $this->GeneralSetting->find('first', array('conditions' => array('GeneralSetting.id' => $id), 'recursive'=> -1));
				$this->request->data['GeneralSetting']['program_id'] = unserialize($this->request->data['GeneralSetting']['program_id']);
				$this->request->data['GeneralSetting']['program_type_id'] = unserialize($this->request->data['GeneralSetting']['program_type_id']);
				
			}

		} 

		if (empty($this->request->data) && isset($id)) {
			$this->request->data = $this->GeneralSetting->find('first', array('conditions' => array('GeneralSetting.id' => $id), 'recursive'=> -1));
			$this->request->data['GeneralSetting']['program_id'] = unserialize($this->request->data['GeneralSetting']['program_id']);
			$this->request->data['GeneralSetting']['program_type_id'] = unserialize($this->request->data['GeneralSetting']['program_type_id']);
		}

		$programs = $this->GeneralSetting->Program->find('list');
		$programTypes = $this->GeneralSetting->ProgramType->find('list');

		$this->set(compact('programs', 'programTypes'));
	}

	public function delete($id = null)
	{
		$this->GeneralSetting->id = $id;

		if (!$this->GeneralSetting->exists()) {
			throw new NotFoundException(__('Invalid General Setting'));
		}

		$this->request->allowMethod('post', 'delete');

		if ($this->GeneralSetting->delete()) {
			$this->Flash->success('The general setting has been deleted.');
		} else {
			$this->Flash->error('The general setting could not be deleted. Please, try again.');
		}

		return $this->redirect(array('action' => 'index'));
	}
}
