<?php
class CollegesController extends AppController
{
	var $name = 'Colleges';
	var $menuOptions = array(
		'parent' => 'campuses',
		'exclude' => array(
			'index', 
			'delegate_scale', 
			'registrar_delegate_scale', 
			'get_college_combo', 
			'get_active_college_combo',
			'search'
		),
		'alias' => array(
			'add' => 'Add College',
			'delegate_scale' => 'Delegate Scale',
		)
	);

	public $paginate = array();
	public function search()
	{
		$url['action'] = 'index';

		if (isset($this->request->data) && !empty($this->request->data)) {
			foreach ($this->request->data as $k => $v) {
				if (!empty($v)) {
					foreach ($v as $kk => $vv) {
						if (!empty($vv) && is_array($vv)) {
							foreach ($vv as $kkk => $vvv){
								$url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
							}
						} else {
							$url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
						}
					}
				}
			}
		}

		return $this->redirect($url, null, true);
	}

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->Allow(
			'get_college_combo', 
			'get_active_college_combo',
			'search'
		);
	}

	public function index()
	{
		//$this->College->recursive = 0;
		/* $this->Paginator->settings =  array('contain' => array('Campus'), 'recursive'=> -1);
		$this->set('colleges', $this->paginate()); */

		$conditions = array();

		$this->Paginator->settings =  array('contain' => array('Campus' => array('id', 'name')), 'limit' => 20, 'maxLimit' => 20, 'order' => array('College.campus_id' => 'ASC', 'College.id' => 'ASC', 'College.name' => 'ASC'), 'recursive'=> -1);

		if (isset($this->passedArgs['College.name']) && !empty($this->passedArgs['College.name'])) {
			$conditions['conditions']['College.name like '] = $this->request->data['College']['name'] = $this->passedArgs['College.name'] . '%';
		}
		
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
			$conditions['conditions'] = ['College.active IN (0,1)'];
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			$conditions['conditions'] = ['College.id' => $this->college_id, 'College.active = 1'];
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$conditions['conditions'] = ['College.id' => $this->college_id,'College.active = 1'];
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$college_ids = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $this->department_ids), 'fields' => array('Department.college_id', 'Department.college_id')));
				debug($college_ids);
				$conditions['conditions'] = ['College.id' => array_keys($college_ids), 'College.active = 1'];
			} else if (!empty($this->college_ids)) {
				$conditions['conditions'] = ['College.id' => $this->college_ids, 'College.active = 1'];
			}
		} 

		$colleges = array();

		if (isset($conditions['conditions']) && !empty($conditions['conditions'])) {
			$colleges = $this->paginate($conditions['conditions']);
		}

		if (empty($colleges) && isset($conditions['conditions']) && !empty($conditions['conditions'])) {
			$this->Flash->info('No college is found based on the given search criteria.');
		}

		$this->set(compact('colleges'));
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid college');
			return $this->redirect(array('action' => 'index'));
		}

		$this->College->id = $id;

		if (!$this->College->exists()) {
			$this->Flash->error('Invalid college id');
			return $this->redirect(array('action' => 'index'));
		}

		$college = $this->College->find('first', array(
			'conditions' => array(
				'College.id' => $id
			), 
			'contain' => array(
				'Campus' => array('fields' => array('id', 'name')), 
				'Department'=> array('College' => array('id', 'name', 'shortname'),'order'=> array('Department.name')), 
				'GradeScale'=> array(
					'conditions' => array(
						'GradeScale.model'=> 'College',
						'GradeScale.foreign_key'=> $id,
					),
					'GradeType',
					'Program'=> array('fields' => array('id', 'name')),
					'order'=> 'GradeScale.program_id'
				),
				'Staff'=> array(
					'conditions' => array(
						'Staff.active'=> 1,
					),
					'Title'=> array('fields' => array('id', 'title')),
					'Position'=> array('fields' => array('id', 'position')),
					'Department'=> array('fields' => array('id', 'name')),
					'order'=> array('Staff.department_id', 'Staff.position_id')
				),
			),
			'recursive'=> -1
		));

		//$this->set('college', $this->College->read(null, $id));
		$this->set('college', $college);
	}

	function add()
	{
		$this->set($this->request->data);

		if (!empty($this->request->data)) {
			$this->College->create();
			
			$this->request->data['College']['name'] = trim(ucwords(strtolower($this->request->data['College']['name'])));
			$this->request->data['College']['shortname'] = trim(strtoupper($this->request->data['College']['shortname']));
			$this->request->data['College']['type'] = trim(ucwords(strtolower($this->request->data['College']['type'])));
			$this->request->data['College']['amharic_name'] = trim($this->request->data['College']['amharic_name']);
			$this->request->data['College']['amharic_short_name'] = trim($this->request->data['College']['amharic_short_name']);
			$this->request->data['College']['institution_code'] = trim($this->request->data['College']['institution_code']);
			
			if ($this->College->save($this->request->data)) {
				$this->Flash->success('The college has been saved');
				unset($this->request->data);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The college could not be saved. Please, try again.');
			}
		}

		$campuses = $this->College->Campus->find('list');
		$this->set(compact('campuses'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid college');
			return $this->redirect(array('action' => 'index'));
		}

		$this->College->id = $id;

		if (!$this->College->exists()) {
			$this->Flash->error('Invalid college id');
			return $this->redirect(array('action' => 'index'));
		}

		$this->set($this->request->data);

		if (!empty($this->request->data)) {

			$this->request->data['College']['name'] = trim(ucwords(strtolower($this->request->data['College']['name'])));
			$this->request->data['College']['shortname'] = trim(strtoupper($this->request->data['College']['shortname']));
			$this->request->data['College']['type'] = trim(ucwords(strtolower($this->request->data['College']['type'])));
			$this->request->data['College']['amharic_name'] = trim($this->request->data['College']['amharic_name']);
			$this->request->data['College']['amharic_short_name'] = trim($this->request->data['College']['amharic_short_name']);
			$this->request->data['College']['institution_code'] = trim($this->request->data['College']['institution_code']);

			if ($this->College->save($this->request->data)) {
				$this->Flash->success('The college has been updated.');
				unset($this->request->data);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The college could not be saved. Please, try again.');
			}
		}

		if (empty($this->request->data)) {
			//$this->request->data = $this->College->read(null, $id);
			$college = $this->College->find('first', array('conditions' => array('College.id' => $id), 'recursive'=> -1));
			$this->request->data =  $college;
		}

		$campuses = $this->College->Campus->find('list');
		$this->set(compact('campuses'));
	}

	function delegate_scale()
	{
		if (!empty($this->request->data)) {
			if ($this->College->save($this->request->data)) {
				$this->Flash->success('Delegation of grade scale has been successful.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The scale delegation couldnt be saved. Please, try again.');
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->College->find('first', array(
				'conditions' => array(
					'College.id' => $this->college_id, 
					'College.active' => 1
				), 
				'contain' => array(
					'Campus' => array('fields' => array('id', 'name')), 
					'Department' => array(
						'conditions' => array('Department.active' => 1), 
						'fields' => array('id', 'name')
					)
				)
			));
		}

		$campuses = $this->College->Campus->find('list');
		$this->set(compact('campuses'));
	}

	function registrar_delegate_scale()
	{
		if (!empty($this->request->data) && isset($this->request->data['update'])) {
			if ($this->College->save($this->request->data)) {
				$this->Flash->success('Delegation of grade scale has been successful.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The scale delegation couldnt be saved. Please, try again.');
			}
		}

		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
			if (!empty($this->request->data['Search']['college_id'])) {
				$this->request->data = $this->College->find('first', array(
					'conditions' => array('College.id' => $this->request->data['Search']['college_id']), 
					'contain' => array(
						'Campus' => array('fields' => array('id', 'name')), 
						'Department' => array(
							'conditions' => array('Department.active' => 1), 
							'fields' => array('id', 'name')
						)
					)
				));
			}
		}

		$colleges = $this->College->find('list', array('conditions' => array('College.active' => 1)));
		$campuses = $this->College->Campus->find('list');
		$this->set(compact('campuses', 'colleges'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid college id');
			return $this->redirect(array('action' => 'index'));
		}

		$this->College->id = $id;

		if (!$this->College->exists()) {
			$this->Flash->error('Invalid college id');
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->College->canItBeDeleted($id)) {
			if ($this->College->delete($id)) {
				$this->Flash->success('College deleted');
				return $this->redirect(array('action' => 'index'));
			}
		}

		$this->Flash->error('College was not deleted, It is associated to Students.');
		return $this->redirect(array('action' => 'index'));
	}

	function get_college_combo($campus_id = null, $all = 0)
	{
		$this->layout = 'ajax';
		$colleges = array();

		if (!empty($campus_id) && $all) {
			$colleges = $this->College->find('list', array('conditions' => array('College.campus_id' => $campus_id)));
		} else if (!empty($campus_id)) {
			if (!empty($this->college_ids)) {
				$colleges = $this->College->find('list', array(
					'conditions' => array(
						'College.campus_id' => $campus_id,
						'College.id' => $this->college_ids
					)
				));
			} else {
				$colleges = $this->College->find('list', array('conditions' => array('College.campus_id' => $campus_id)));
			}
		}
		$this->set(compact('colleges'));
	}

	function get_active_college_combo($campus_id = null, $all = 0)
	{
		$this->layout = 'ajax';
		$colleges = array();

		if (!empty($campus_id) && $all) {
			$colleges = $this->College->find('list', array('conditions' => array('College.campus_id' => $campus_id, 'College.active' => 1)));
		} else if (!empty($campus_id)) {
			if (!empty($this->college_ids)) {
				$colleges = $this->College->find('list', array(
					'conditions' => array(
						'College.campus_id' => $campus_id,
						'College.id' => $this->college_ids,
						'College.active' => 1
					)
				));
			} else {
				$colleges = $this->College->find('list', array('conditions' => array('College.campus_id' => $campus_id, 'College.active' => 1)));
			}
		}
		$this->set(compact('colleges'));
	}
}
