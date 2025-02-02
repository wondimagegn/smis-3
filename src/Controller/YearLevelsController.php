<?php
class YearLevelsController extends AppController
{
	public $name = 'YearLevels';

	public $menuOptions = array(
		'parent' => 'sections',
		'exclude' => array('index'),
		'alias' => array(
			'index' => 'View Year Levels',
			'add' => 'Add Year Levels'
		)
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	public function index()
	{
		$this->YearLevel->recursive = 0;
		$conditions = array("YearLevel.department_id " => $this->department_id);
		$this->set('yearLevels', $this->paginate($conditions));
	}

	public function view($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid Year Level ID.');
			return $this->redirect(array('action' => 'index'));
		}

		$this->set('yearLevel', $this->YearLevel->read(null, $id));
	}

	public function add()
	{
		if (!empty($this->request->data)) {

			$maximum_year_level = $this->request->data['YearLevel']['numberofyear'];

			unset($this->request->data['YearLevel']['numberofyear']);

			$yearLevels = array();
			$newly_formed_year_level = array();

			for ($i = 1; $i <= $maximum_year_level; $i++) {
				$name = '';
				switch ($i) {
					case 1:
						$name = $i . 'st';
						break;
					case 2:
						$name = $i . 'nd';
						break;
					case 3:
						$name = $i . 'rd';
						break;
					default:
						$name = $i . 'th';
				}

				$yearLevels['YearLevel'][$i]['name'] = $name;
				$yearLevels['YearLevel'][$i]['department_id'] = $this->request->data['YearLevel']['department_id'];
				$newly_formed_year_level[] = $name;
			}

			$check_already_created_year_level = $this->YearLevel->find('list', array(
				'conditions' => array(
					'YearLevel.department_id' => $this->request->data['YearLevel']['department_id']
				), 
				'recursive' => -1
			));

			// unset those yearlevels that has already created.

			if (!empty($check_already_created_year_level)) {
				foreach ($check_already_created_year_level as $year_level_id => $year_level_name) {
					if (in_array($year_level_name, $newly_formed_year_level)) {
						foreach ($yearLevels['YearLevel'] as $k => &$v) {
							if (strcasecmp($v['name'], $year_level_name) == 0) {
								unset($yearLevels['YearLevel'][$k]);
							}
						}
					}
				}
			}

			// save only the year level not saved.
			if (!empty($yearLevels['YearLevel'])) {
				if ($this->YearLevel->saveAll($yearLevels['YearLevel'])) {
					$this->Flash->success('The year levels have been saved');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error('The year level could not be saved. Please, try again.');
				}
			} else {
				$this->Flash->error('The year level could not be saved. There are already ' . $maximum_year_level. ' year levels in your department.');
			}
		}

		if ($this->role_id == ROLE_COLLEGE) {
			
			$departments = $this->YearLevel->Department->find('list', array(
				'conditions' => array(
					'Department.college_id' => $this->college_id,
					'Department.active' => 1,
				),
				'fields' => array('id', 'name')
			));

		} else if ($this->role_id == ROLE_DEPARTMENT) {

			echo $this->department_id;
			$departments = $this->YearLevel->Department->find('list', array('conditions' => array('Department.id' => $this->department_id)));

			$already_created_year_levels_count = $this->YearLevel->find('count', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
			$this->set('already_created_year_levels_count', $already_created_year_levels_count);

		} else if ($this->role_id == ROLE_SYSADMIN) {
			
			$departments = $this->YearLevel->Department->find('all', array(
				'fields' => array('id', 'name'),
				'contain' => array('College' => array('id', 'name'))
			));

			$return = array();

			if (!empty($departments)) {
				foreach ($departments as $dep_id => $dep_name) {
					$return[$dep_name['College']['name']][$dep_name['Department']['id']] = $dep_name['Department']['name'];
				}
			}

			$departments = $return;
		}

		$this->set(compact('departments'));
	}

	function edit($id = null)
	{
		if (!$id && empty($this->request->data)) {
			$this->Flash->error('Invalid Year Level ID.');
			return $this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data)) {
			if ($this->YearLevel->save($this->request->data)) {
				$this->Flash->success('The Year Level has been saved.');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error('The year level could not be saved. Please, try again.');
			}
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->YearLevel->read(null, $id);
		}

		$departments = $this->YearLevel->Department->find('all', array(
			'fields' => array('id', 'name'),
			'contain' => array(
				'College' => array('id', 'name')
			)
		));

		$return = array();

		if (!empty($departments)) {
			foreach ($departments as $dep_id => $dep_name) {
				$return[$dep_name['College']['name']][$dep_name['Department']['id']] = $dep_name['Department']['name'];
			}
		}

		$departments = $return;

		$this->set(compact('departments'));
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Flash->error('Invalid Year Level ID.');
			return $this->redirect(array('action' => 'index'));
		}

		$deletion_possible = false;

		$sections = $this->YearLevel->Section->find('all', array(
			'conditions' => array(
				'Section.year_level_id' => $id
			), 
			'contain' => array(
				'Student' => array(
					'fields' => array('id', 'full_name'),
					'conditions' => array(
						'Student.graduated = 0'
					)
				)
			)
		));

		if (!empty($sections)) {
			foreach ($sections as $sc => $sv) {
				if (!empty($sv['Section']) && !empty($sv['Student'])) {
					break;
				}
			}
		} else {
			$deletion_possible = true;
		}

		if ($deletion_possible) {
			if ($this->YearLevel->delete($id)) {
				$this->Flash->success('Year level deleted!');
				$this->redirect(array('action' => 'index'));
			}
		}
		
		$this->Flash->error('Year level was not deleted!, It is associated to student sections.');
		return $this->redirect(array('action' => 'index'));
	}
}
