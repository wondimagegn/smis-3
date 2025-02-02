<?php
class DepartmentsController extends AppController
{
	var $name = 'Departments';
	var $menuOptions = array(
		'parent' => 'campuses',
		'alias' => array(
			//'index' => 'List Departments',
			'add' => 'Add New Department'
		),
		'exclude' => array(
			'index'
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
			'search',
			'get_department_combo',
			'get_department_for_transfer'
		);
	}

	function index()
	{
		$conditions = array();
		$colleges = array();
		
		if (isset($this->passedArgs['Department.college_id']) && !empty($this->passedArgs['Department.college_id'])) {
			$conditions['conditions']['Department.college_id'] = $this->request->data['Department']['college_id'] =  $this->passedArgs['Department.college_id'];
		}

		if (isset($this->passedArgs['Department.name']) && !empty($this->passedArgs['Department.name'])) {
			$conditions['conditions']['Department.name like '] = $this->request->data['Department']['name'] = $this->passedArgs['Department.name'] . '%';
		}
		
		$this->Paginator->settings =  array('contain' => array('College' => array('Campus' => array('id', 'name'))), 'limit' => 200, 'maxLimit' => 200, 'order' => array('College.campus_id' => 'ASC', 'Department.college_id' => 'ASC', 'Department.name' => 'ASC'), 'recursive'=> -1);
		
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
			$conditions['conditions'] = ['Department.active IN (0,1)'];
			$colleges = $this->Department->College->find('list');
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE /* && $this->Session->read('Auth.User')['is_admin'] == 1 */) {
			$conditions['conditions'] = ['Department.college_id' => $this->college_id, 'Department.active = 1'];
			$colleges = $this->Department->College->find('list', array('conditions' => array('College.id' => $this->college_id, 'College.active' => 1)));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT/*  && $this->Session->read('Auth.User')['is_admin'] == 1 */) {
			$conditions['conditions'] = ['Department.id' => $this->department_id, 'Department.active = 1'];
			$college_ids = $this->Department->find('list', array('conditions' => array('Department.id' => $this->department_id), 'fields' => array('Department.college_id', 'Department.college_id')));
			$colleges = $this->Department->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1)));
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR /* && $this->Session->read('Auth.User')['is_admin'] == 1 */) {
			if (!empty($this->department_ids)) {
				$conditions['conditions'] = ['Department.id' => $this->department_ids, 'Department.active = 1'];
				$college_ids = $this->Department->find('list', array('conditions' => array('Department.id' => $this->department_ids), 'fields' => array('Department.college_id', 'Department.college_id')));
				$colleges = $this->Department->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1)));
			} else if (!empty($this->college_ids)) {
				$conditions['conditions'] = ['Department.college_id' => $this->college_ids, 'Department.active = 1'];
				$colleges = $this->Department->College->find('list', array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1)));
			}
		}

		$departments = array();

		if (isset($conditions['conditions']) && !empty($conditions['conditions'])) {
			$departments = $this->paginate($conditions['conditions']);
		}

		if (empty($departments) && isset($conditions['conditions']) && !empty($conditions['conditions'])) {
			$this->Flash->info('No department found based on the given search criteria.');
		}

		debug($colleges);
		$this->set(compact('colleges', 'departments' ));
	}

	function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid department');
			return $this->redirect(array('action' => 'index'));
		}

		$this->Department->id = $id;

		if (!$this->Department->exists()) {
			$this->Flash->error('Invalid Department ID');
			return $this->redirect(array('action' => 'index'));
		}

		$dept_college_id = $this->Department->field('college_id', array('Department.id' => $id));

		$department = $this->Department->find('first', array(
			'conditions' => array(
				'Department.id' => $id
			), 
			'contain' => array(
				'College' => array('Campus' => array('id', 'name')), 
				'GradeScale'=> array(
					'conditions' => array(
						'GradeScale.model'=> 'Department',
						'GradeScale.foreign_key'=> $id,
					),
					'GradeType',
					'Program'=> array('fields' => array('id', 'name')),
					'order'=> 'GradeScale.program_id'
				),
				'Staff'=> array(
					'conditions' => array(
						'Staff.active' => 1,
					),
					'Title'=> array('fields' => array('id', 'title')),
					'Position'=> array('fields' => array('id', 'position')),
					'Department'=> array('fields' => array('id', 'name')),
					'order'=> array('Staff.department_id', 'Staff.position_id')
				),
			),
			'recursive'=> -1
		));

		$college_level_defined_grade_scales = array();

		if ($dept_college_id) {
			$college_level_defined_grade_scales  = $this->Department->College->find('first', array(
				'conditions' => array(
					'College.id' => $dept_college_id
				), 
				'contain' => array(
					'GradeScale'=> array(
						'conditions' => array(
							'GradeScale.model'=> 'College',
							'GradeScale.foreign_key'=> $dept_college_id,
						),
						'GradeType',
						'Program'=> array('fields' => array('id', 'name')),
						'order'=> 'GradeScale.program_id'
					),
				),
				'fields' => array(
					'College.id',
					'College.name'
				),
				'recursive'=> -1
			));

			//debug($college_level_defined_grade_scales);
		}

		$this->set('department', $department);
		$this->set('college_level_defined_grade_scales', $college_level_defined_grade_scales);
	}

	function add()
	{
		if (!empty($this->request->data)) {
			$this->Department->create();

			$this->request->data['Department']['name'] = trim(ucwords(strtolower($this->request->data['Department']['name'])));
			$this->request->data['Department']['shortname'] = trim($this->request->data['Department']['shortname']);
			$this->request->data['Department']['amharic_name'] = trim($this->request->data['Department']['amharic_name']);
			$this->request->data['Department']['amharic_short_name'] = trim($this->request->data['Department']['amharic_short_name']);
			$this->request->data['Department']['institution_code'] = trim($this->request->data['Department']['institution_code']);

			if ($this->Department->save($this->request->data)) {
				$this->Flash->success('The department has been saved');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The department could not be saved. Please, try again.');
			}
		}

		$colleges = $this->Department->College->find('list', array('conditions' => array('College.active' => 1)));
		$this->set(compact('colleges'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid Department ID');
			return $this->redirect(array('action' => 'index'));
		}

		$this->Department->id = $id;

		if (!$this->Department->exists()) {
			$this->Flash->error('Invalid Department ID');
			return $this->redirect(array('action' => 'index'));
		}

		$this->set($this->request->data);

		if (!empty($this->request->data)) {
			
			$this->request->data['Department']['name'] = trim(ucwords(strtolower($this->request->data['Department']['name'])));
			$this->request->data['Department']['shortname'] = trim($this->request->data['Department']['shortname']);
			$this->request->data['Department']['amharic_name'] = trim($this->request->data['Department']['amharic_name']);
			$this->request->data['Department']['amharic_short_name'] = trim($this->request->data['Department']['amharic_short_name']);
			$this->request->data['Department']['institution_code'] = trim($this->request->data['Department']['institution_code']);

			if ($this->Department->save($this->request->data)) {
				$this->Flash->success('The Department has been updated.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The Department could not be saved. Please, try again.');
			}
		}

		if (empty($this->request->data)) {
			$department = $this->Department->find('first', array('conditions' => array('Department.id' => $id), 'recursive'=> -1));
			$this->request->data =  $department;
		}

		$colleges = $this->Department->College->find('list');
		$this->set(compact('colleges'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid Department ID');
			return $this->redirect(array('action' => 'index'));
		}

		$this->Department->id = $id;

		if (!$this->Department->exists()) {
			$this->Flash->error('Invalid Department ID');
			return $this->redirect(array('action' => 'index'));
		}

		if ($this->Department->canItBeDeleted($id)) {
			if ($this->Department->delete($id)) {
				$this->Flash->success('Department deleted.');
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->Flash->error('Department was not deleted, It is associated to Students.');
		return $this->redirect(array('action' => 'index'));
	}

	function get_department_combo($college_id = null, $all = 0, $active_only = '')
	{
		$this->layout = 'ajax';

		$departments = array();

		if (empty($active_only)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $active_only;
		}

		if (!empty($college_id) && $all) {
			$departments = $this->Department->find('list', array(
				'conditions' => array(
					'Department.college_id' => $college_id,
					'Department.active' => $active
				)
			));
		} else if (!empty($college_id)) {
			if (!empty($this->department_ids)) {
				$departments = $this->Department->find('list', array(
					'conditions' => array(
						'Department.college_id' => $college_id,
						'Department.id' => $this->department_ids,
						'Department.active' => $active
					)
				));
			} else {
				$departments = $this->Department->find('list', array(
					'conditions' => array(
						'Department.college_id' => $college_id,
						'Department.active' => $active,
					)
				));
			}
		}

		$this->set(compact('departments'));
	}

	function get_department_for_transfer($college_id = null, $exclude_department = null)
	{
		$this->layout = 'ajax';
		$departments = array();

		if (!empty($college_id) && !empty($exclude_department)) {
			$departments = $this->Department->find('list', array(
				'conditions' => array(
					'Department.college_id' => $college_id,
					'Department.active' => 1,
					'Department.id !=' . $exclude_department . ''
				)
			));
		} else if (!empty($college_id)) {
			$departments = $this->Department->find('list', array(
				'conditions' => array(
					'Department.college_id' => $college_id,
					'Department.active' => 1,
				)
			));
		}

		$this->set(compact('departments'));
		$this->render('get_department_combo');
	}
}
