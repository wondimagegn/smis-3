<?php
class ProgramsController extends AppController {

	var $name = 'Programs';

	public $menuOptions = array(
		//'parent' => 'mainData',
		'alias' => array(
			'index' => 'List Programs',
			'add' => 'Add Program',
		),
		'exclude' => array(
			'edit', 'view', 'delete'
		),
	);

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow(
			'get_departments_combo'
		);
	}

	function index() {
		$this->Program->recursive = 0;
		$this->set('programs', $this->paginate());
	}


	function view($id = null) {
		if (!$id) {
			$this->Flash->error(__('Invalid program'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('program', $this->Program->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->Program->create();
			if ($this->Program->save($this->request->data)) {
				$this->Flash->success(__('The program has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The program could not be saved. Please, try again.'));
			}
		}
	}

	function edit($id = null) {

		if (!$id && empty($this->request->data)) {
			$this->Flash->error(__('Invalid program'));
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			if ($this->Program->save($this->request->data)) {
				$this->Flash->success(__('The program has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The program could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Program->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Flash->error(__('Invalid id for program'));
			return $this->redirect(array('action'=>'index'));
		}

		/* if ($this->Program->delete($id)) {
			$this->Flash->success(__('Program deleted'));
			return $this->redirect(array('action'=>'index'));
		} */

		$this->Flash->error(__('Program was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}

	function get_departments_combo($program_ids = null)
	{
		$this->layout = 'ajax';

		$departments = array();

		if (!empty($program_ids)) {
			if (isset($this->department_ids) && !empty($this->department_ids)) {
				$availableDepartmentsBasedOnProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
					'fields' => array('Curriculum.department_id', 'Curriculum.department_id'),
					'conditions' => array(
						'Curriculum.department_id' => $this->department_ids,
						'Curriculum.program_id' => $program_ids,
						'Curriculum.active' => 1
					),
					'group' => array('Curriculum.department_id') 
				));
			} else if (isset($this->college_ids) && !empty($this->college_ids)) {

				$departIDs = ClassRegistry::init('Department')->find('list', array(
					'conditions' => array(
						'Department.college_id' => $this->college_ids,
						'Department.active' => 1,
					),
					'fields' => array('Department.id', 'Department.id'),
					'order' => array('Department.name' => 'ASC'),
				));

				if (!empty($departIDs)) {
					$availableDepartmentsBasedOnProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
						'fields' => array('Curriculum.department_id', 'Curriculum.department_id'),
						'conditions' => array(
							'Curriculum.department_id' => $departIDs,
							'Curriculum.program_id' => $program_ids,
							'Curriculum.active' => 1
						),
						'group' => array('Curriculum.department_id') 
					));
				}
			} else {
				$availableDepartmentsBasedOnProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
					'fields' => array('Curriculum.department_id', 'Curriculum.department_id'),
					'conditions' => array(
						'Curriculum.program_id' => $program_ids,
						'Curriculum.active' => 1
					),
					'group' => array('Curriculum.department_id') 
				));
			}
		}

		if (!empty($availableDepartmentsBasedOnProgramsInCurriculums)) {
			$departments = ClassRegistry::init('Department')->find('list', array(
				'conditions' => array(
					'Department.id' => $availableDepartmentsBasedOnProgramsInCurriculums,
					'Department.active' => 1,
				)
			));
		}

		$this->set(compact('departments'));
	}
}
