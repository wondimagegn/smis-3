<?php
class YearLevel extends AppModel
{
	var $name = 'YearLevel';
	var $validate = array(
		'department_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select department',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	var $belongsTo = array(
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Section' => array(
			'className' => 'Section',
			'foreignKey' => 'year_level_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'year_level_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ExamPeriod' => array(
			'className' => 'ExamPeriod',
			'foreignKey' => 'year_level_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	// Function that returns distinct year level for registrar  Return array of year level 
	function distinct_year_level()
	{
		$year_level_find = $this->find('all', array(
			'fields' => array('DISTINCT YearLevel.name'),
			'order' => 'YearLevel.name asc', 
			'group' => 'YearLevel.name', 'recursive' => -1
		));

		$extract = Set::extract('/YearLevel/name', $year_level_find);

		$yearLevels = $extract;
		$yearLevels = $this->find('all', array('fields' => array('DISTINCT YearLevel.name'), 'recursive' => -1));

		$yearleveldistinct = array();

		if (!empty($yearLevels)) {
			foreach ($yearLevels as $key => $value) {
				$yearleveldistinct[$value['YearLevel']['name']] = $value['YearLevel']['name'];
			}
		}

		return $yearleveldistinct;
	}

	// helpfull in academic year definitions removing not aplicable year levels per college  and usefull in reports outputs like attrition rates etc.
	function distinct_year_level_based_on_role($role_id = null, $college_ids = null, $department_ids = null, $program_ids = array())
	{

		$year_level_find = array();

		if ($role_id == ROLE_COLLEGE) {

			$dept_ids = $this->Department->find('list', array('conditions' => array('Department.college_id' => $college_ids, 'Department.active' => 1)));
			
			if (empty($program_ids)) {

				$year_level_find = $this->find('list', array(
					'conditions' => array(
						'YearLevel.department_id' => array_keys($dept_ids)
					),
					'fields' => array('YearLevel.name', 'YearLevel.name'),
					'order' => array('YearLevel.name ASC'), 
					'group' => 'YearLevel.name', 
					'recursive' => -1
				));

			} else {

				$curriculum_ids  = ClassRegistry::init('Curriculum')->find('list', array(
					'fields' => array('Curriculum.id', 'Curriculum.id'),
					'conditions' => array(
						'Curriculum.department_id' => array_keys($dept_ids),
						'Curriculum.program_id' => $program_ids
					)
				));

				$year_level_ids = array();

				if (!empty($curriculum_ids)) {

					$year_level_ids  = ClassRegistry::init('Course')->find('list', array(
						'fields' => array('Course.year_level_id', 'Course.year_level_id'),
						'conditions' => array(
							'Course.curriculum_id' => array_keys($curriculum_ids),
							'Course.active' => 1
						),
						'group' => array('Course.year_level_id')
					));
				}

				if (!empty($year_level_ids)) {
					$year_level_find = $this->find('list', array(
						'conditions' => array(
							'YearLevel.id' => array_keys($year_level_ids)
						),
						'fields' => array('YearLevel.name', 'YearLevel.name'),
						'order' => array('YearLevel.name ASC'), 
						'group' => 'YearLevel.name', 
						'recursive' => -1
					));
				} else {
					$year_level_find = ['1st' => '1st'];
				}

			}

		} else if ($role_id == ROLE_REGISTRAR) {

			if(!empty($college_ids)) {
				
				$dept_ids = $this->Department->find('list', array('conditions' => array('Department.college_id' => $college_ids, 'Department.active' => 1)));
				
				if (empty($program_ids)) {
					$year_level_find = $this->find('list', array(
						'conditions' => array(
							'YearLevel.department_id' => array_keys($dept_ids)
						),
						'fields' => array('YearLevel.name', 'YearLevel.name'),
						'order' => 'YearLevel.name ASC', 
						'group' => 'YearLevel.name', 
						'recursive' => -1
					));
				} else {
					$year_level_find = ['1st' => '1st'];
				}

			} else if (!empty($department_ids)) {

				$dept_ids = $this->Department->find('list', array('conditions' => array('Department.id' => $department_ids, 'Department.active = 1')));
				
				if (empty($program_ids)) {
					$year_level_find = $this->find('list', array(
						'conditions' => array(
							'YearLevel.department_id' => array_keys($dept_ids)
						),
						'fields' => array('YearLevel.name', 'YearLevel.name'),
						'order' => 'YearLevel.name ASC', 
						'group' => 'YearLevel.name', 
						'recursive' => -1
					));
				} else {

					$curriculum_ids  = ClassRegistry::init('Curriculum')->find('list', array(
						'fields' => array('Curriculum.id', 'Curriculum.id'),
						'conditions' => array(
							'Curriculum.department_id' => array_keys($dept_ids),
							'Curriculum.program_id' => $program_ids
						)
					));
	
					$year_level_ids = array();
	
					if (!empty($curriculum_ids)) {
						$year_level_ids  = ClassRegistry::init('Course')->find('list', array(
							'fields' => array('Course.year_level_id', 'Course.year_level_id'),
							'conditions' => array(
								'Course.curriculum_id' => array_keys($curriculum_ids),
								'Course.active' => 1
							),
							'group' => array('Course.year_level_id')
						));
					}
	
					if (!empty($year_level_ids)) {
						$year_level_find = $this->find('list', array(
							'conditions' => array(
								'YearLevel.id' => array_keys($year_level_ids)
							),
							'fields' => array('YearLevel.name', 'YearLevel.name'),
							'order' => array('YearLevel.name ASC'), 
							'group' => 'YearLevel.name', 
							'recursive' => -1
						));
					} else {
						$year_level_find = ['1st' => '1st'];
					}

				}
			}

		} else if ($role_id == ROLE_DEPARTMENT) {

			if (empty($program_ids)) {

				$year_level_find = $this->find('list', array(
					'conditions' => array(
						'YearLevel.department_id' => $department_ids
					),
					'fields' => array('YearLevel.name', 'YearLevel.name'),
					'order' => array('YearLevel.name ASC'), 
					'group' => 'YearLevel.name', 
					'recursive' => -1
				));

			} else {

				$curriculum_ids  = ClassRegistry::init('Curriculum')->find('list', array(
					'fields' => array('Curriculum.id', 'Curriculum.id'),
					'conditions' => array(
						'Curriculum.department_id' => $department_ids,
						'Curriculum.program_id' => $program_ids
					)
				));

				$year_level_ids = array();

				if (!empty($curriculum_ids)) {
					$year_level_ids  = ClassRegistry::init('Course')->find('list', array(
						'fields' => array('Course.year_level_id', 'Course.year_level_id'),
						'conditions' => array(
							'Course.curriculum_id' => array_keys($curriculum_ids),
							'Course.active' => 1
						),
						'group' => array('Course.year_level_id')
					));
				}

				if (!empty($year_level_ids)) {

					$year_level_find = $this->find('list', array(
						'conditions' => array(
							'YearLevel.id' => $year_level_ids
						),
						'fields' => array('YearLevel.name', 'YearLevel.name'),
						'order' => array('YearLevel.name ASC'), 
						'group' => 'YearLevel.name', 
						'recursive' => -1
					));

				} else {
					$year_level_find = ['1st' => '1st'];
				}
			}

		} else {
			
			if (!empty($program_ids) && !empty($department_ids)) {

				$curriculum_ids  = ClassRegistry::init('Curriculum')->find('list', array(
					'fields' => array('Curriculum.id', 'Curriculum.id'),
					'conditions' => array(
						'Curriculum.department_id' => $department_ids,
						'Curriculum.program_id' => $program_ids
					)
				));

				$year_level_ids = array();

				if (!empty($curriculum_ids)) {
					$year_level_ids  = ClassRegistry::init('Course')->find('list', array(
						'fields' => array('Course.year_level_id', 'Course.year_level_id'),
						'conditions' => array(
							'Course.curriculum_id' => array_keys($curriculum_ids),
							'Course.active' => 1
						),
						'group' => array('Course.year_level_id')
					));
				}

				if (!empty($year_level_ids)) {

					$year_level_find = $this->find('list', array(
						'conditions' => array(
							'YearLevel.id' => $year_level_ids
						),
						'fields' => array('YearLevel.name', 'YearLevel.name'),
						'order' => array('YearLevel.name ASC'), 
						'group' => 'YearLevel.name', 
						'recursive' => -1
					));

				} else {
					$year_level_find = ['1st' => '1st'];
				}

			} else if (!empty($department_ids)) {

				$year_level_find = $this->find('list', array(
					'conditions' => array(
						'YearLevel.department_id' => $department_ids
					),
					'fields' => array('YearLevel.name', 'YearLevel.name'),
					'order' => array('YearLevel.name ASC'), 
					'group' => 'YearLevel.name', 
					'recursive' => -1
				));

			} else {
				
				$year_level_find = $this->find('list', array(
					'fields' => array('YearLevel.name', 'YearLevel.name'),
					'order' => array('YearLevel.name ASC'), 
					'group' => 'YearLevel.name', 
					'recursive' => -1
				));
			}
		}

		if (isset($year_level_find['10th'])) {
			unset($year_level_find['10th']);
			$year_level_find['10th'] = '10th';
		}

		return $year_level_find;
	}

	function get_department_max_year_level($department_ids = null)
	{
		$max_year_level = 0;

		if (is_array($department_ids)) {
			foreach ($department_ids as $department_id => $department_name) {
				$yearLevels = $this->find('list', array('conditions' => array('YearLevel.department_id' => $department_id)));
				if (!empty($yearLevels)) {
					foreach ($yearLevels as $yearLevel) {
						$year_level_number = substr($yearLevel, 0, strlen($yearLevel) - 2);
						if ($year_level_number > $max_year_level) {
							$max_year_level = $year_level_number;
						}
					}
				}
			}
		} else {
			$yearLevels = $this->find('list', array('conditions' => array('YearLevel.department_id' => $department_ids)));
			if (!empty($yearLevels)) {
				foreach ($yearLevels as $yearLevel) {
					$year_level_number = substr($yearLevel, 0, strlen($yearLevel) - 2);
					if ($year_level_number > $max_year_level) {
						$max_year_level = $year_level_number;
					}
				}
			}
		}
		return $max_year_level;
	}

	function getNextYearLevel($yearLevelId, $departmentId)
	{
		$yearLevel = $this->find('first', array('conditions' => array('YearLevel.id' => $yearLevelId), 'recursive' => -1));
		$yearLevels = $this->find('all', array('conditions' => array('YearLevel.department_id' => $departmentId), 'recursive' => -1, 'order' => 'YearLevel.name ASC'));
		
		if (!empty($yearLevels)) {
			foreach ($yearLevels as $k => $v) {
				if ($v['YearLevel']['name'] > $yearLevel['YearLevel']['name']) {
					return $v['YearLevel']['id'];
				}
			}
		}

		return null;
	}
}
