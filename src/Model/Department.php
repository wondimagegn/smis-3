<?php
class Department extends AppModel
{
	var $name = 'Department';
	var $displayField = 'name';

	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Name is required'
			),
			'isUniqueDepartmentInCollege' => array(
				'rule' => array('isUniqueDepartmentInCollege'),
				'message' => 'The department name should be unique in the college. The name is already taken. Use another one.'
			),
		),
	);

	function isUniqueDepartmentInCollege()
	{
		$count = 0;
		if (!empty($this->data['Department']['id'])) {
			$count = $this->find('count', array(
				'conditions' => array(
					'Department.college_id' => $this->data['Department']['college_id'], 
					'Department.name' => trim($this->data['Department']['name']),
					'Department.id <> ' => $this->data['Department']['id']
				)
			));
		} else {
			$count = $this->find('count', array(
				'conditions' => array(
					'Department.college_id' => $this->data['Department']['college_id'], 
					'Department.name' => trim($this->data['Department']['name'])
				)
			));
		}
		if ($count > 0) {
			return false;
		}
		return true;
	}

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'department_id',
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
		'DepartmentTransfer' => array(
			'className' => 'DepartmentTransfer',
			'foreignKey' => 'department_id',
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
		'Specialization'  => array(
			'className' => 'Specialization',
			'foreignKey' => 'department_id',
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
		/*'GradeScale' => array(
			'className' => 'GradeScale',
			'foreignKey' => 'department_id',
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
		*/
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'department_id',
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
			'foreignKey' => 'department_id',
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
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'department_id',
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
		/* 'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'department_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		), */
		'Offer' => array(
			'className' => 'Offer',
			'foreignKey' => 'department_id',
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
		'Preference' => array(
			'className' => 'Preference',
			'foreignKey' => 'department_id',
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'department_id',
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
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'department_id',
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
		'Section' => array(
			'className' => 'Section',
			'foreignKey' => 'department_id',
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
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'department_id',
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
		'GradeScale' => array(
			'className' => 'GradeScale',
			'foreignKey' => 'foreign_key',
			'conditions'    => array('model' => 'Department'),
			'dependent' => true,
		),
		'AcademicCalendar' => array(
			'className' => 'AcademicCalendar',
			'foreignKey' => 'college_id',
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
		'TakenProperty' => array(
			'className' => 'TakenProperty',
			'foreignKey' => 'department_id',
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
	);

	function canItBeDeleted($department_id = null)
	{
		if ($this->YearLevel->find('count', array('conditions' => array('YearLevel.department_id' => $department_id))) > 0) {
			return false;
		}

		if ($this->Student->find('count', array('conditions' => array('Student.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->Section->find('count', array('conditions' => array('Section.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->GradeScale->find('count', array('conditions' => array('GradeScale.model' => 'Department', 'GradeScale.foreign_key' => $department_id))) > 0) {
			return false;
		} else if ($this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->Curriculum->find('count', array('conditions' => array('Curriculum.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.department_id' => $department_id))) > 0) {
			return false;
		} else if ($this->Staff->find('count', array('conditions' => array('Staff.department_id' => $department_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}

	function allDepartmentsByCollege($include_freshman_program = 0, $only_active = 0)
	{
		$departments_organized = array();

		if (empty($only_active)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $only_active;
		}

		$departments_data = $this->College->find('all', array(
			'contain' => array(
				'Department' => array(
					'conditions' => array(
						'Department.active' => $active
					)
				)
			)
		));

		//debug($departments_data);
		if (!empty($departments_data)) {
			foreach ($departments_data as $key => $college_and_department) {
				$departments_organized[$college_and_department['College']['name']] = array();
				if ($include_freshman_program == 1) {
					$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = $college_and_department['College']['shortname'] . ' - Pre/Freshman/Remedial';
				}
				foreach ($college_and_department['Department'] as $key => $department) {
					$departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
				}
			}
		}
		//array_unshift($sections_organized, array('' => '--- Select Section ---'));
		//debug($departments_organized);
		return $departments_organized;
	}

	//Filter list of departments by thier privligae (It is for registrar)
	function allDepartmentsByCollege2($include_all_department = 0, $privilaged_department_ids = array(), $privilaged_collage_ids = array(), $only_active = '') 
	{
		$departments_organized = array();

		if (empty($only_active)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $only_active;
		}

		if (!empty($privilaged_department_ids)) {
			$departments_data = $this->College->find('all', array(
				'contain' => array(
					'Department' => array(
						'conditions' => array(
							'Department.id' => $privilaged_department_ids,
							'Department.active' => $active
						),
						'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
					)
				)
			));
		} else if (!empty($privilaged_collage_ids)) {
			$departments_data = $this->College->find('all', array(
				'conditions' => array(
					'College.id' => $privilaged_collage_ids,
					'College.active' => $active
				),
				'contain' => array(
					'Department' => array(
						'conditions' => array(
							'Department.active' => $active
						),
						'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
					)
				)
			));
		} else {
			$departments_data = $this->College->find('all', array(
				'conditions' => array(
					'College.active' => $active
				),
				'contain' => array(
					'Department' => array(
						'conditions' => array(
							'Department.active' => $active
						),
						'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
					)
				)
			));
			//debug($departments_data);
		}

		if (!empty($departments_data)) {
			foreach ($departments_data as $key => $college_and_department) {
				if ($include_all_department == 1) {
					// Added By Neway
					if (!empty($privilaged_collage_ids) && !is_array($privilaged_collage_ids) && is_numeric($privilaged_collage_ids) && $college_and_department['College']['id'] == $privilaged_collage_ids) {
						//for College role
						$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
					} else if (!empty($privilaged_department_ids) && !empty($privilaged_collage_ids) && is_array($privilaged_collage_ids) && in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
						$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
					} else if (!empty($privilaged_collage_ids) && is_array($privilaged_collage_ids) && in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
						$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
					} else if (!empty($privilaged_department_ids) && empty($privilaged_collage_ids)) {
						//for department or registrar role without college_id passed
						$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
					} else if (empty($privilaged_collage_ids) && empty($privilaged_department_ids)) {
						// other roles than colllege, department & registrar
						$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
					}
					// end Added By neway
					//$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
				} else if (is_array($privilaged_department_ids) && in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
					$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'Pre/Freshman/Remedial - ' . $college_and_department['College']['shortname'];
				}
				foreach ($college_and_department['Department'] as $key => $department) {
					if (is_array($privilaged_department_ids) && !empty($privilaged_department_id)) {
						if (in_array($department['id'], $privilaged_department_ids)) {
							$departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
						}
					} else if (isset($privilaged_department_id) && $department['id'] == $privilaged_department_ids) {
						debug($department);
						$departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
					} else {
						$departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
					}
				}
			}
		}
		//array_unshift($sections_organized, array('' => '--- Select Section ---'));
		//debug($departments_organized);
		return $departments_organized;
	}

	function onlyFreshmanInAllColleges($college_ids = null, $only_active = 0)
	{
		$departments = array();

		if (empty($only_active)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $only_active;
		}

		if (!empty($college_ids)) {
			$colleges = $this->College->find('all', array(
				'conditions' => array(
					'College.id' => $college_ids, 
					'College.active' => $active
				),
				'recursive' => -1
			));
		} else {
			$colleges = $this->College->find('all', array(
				'conditions' => array(
					'College.active' => $active
				),
				'recursive' => -1
			));
		}

		if (!empty($colleges)) {
			foreach ($colleges as $k => $v) {
				$departments[$v['College']['name']]['c~' . $v['College']['id']] = 'Pre/Freshman/Remedial - ' . $v['College']['shortname'];
			}
		}
		
		return $departments;
	}

	//Filter list of departments by college (It is for college privliage use like grade view)
	function allCollegeDepartments($college_id = null, $only_active = 0, $include_freshman_program = 0)
	{
		$departments_organized = array();

		if (empty($only_active)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $only_active;
		}

		if (isset($college_id) && !empty($college_id)) {

			$departments_data = $this->College->Department->find('all', array(
				'conditions' => array(
					'Department.college_id' => $college_id,
					'Department.active' => $active
				),
				'contain' => array(
					'College' => array('id', 'name', 'shortname')
				),
				'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC'),
				'recursive' => -1
			));

			//$departments_organized['c~' . $college_id] = 'Freshman Program';

			if (!empty($departments_data)) {
				foreach ($departments_data as $key => $department) {
					if ($include_freshman_program || 1) {
						$departments_organized['c~' . $department['College']['id']] = 'Pre/Freshman/Remedial - ' . $department['College']['shortname'];
					}
					$departments_organized[$department['Department']['id']] = $department['Department']['name'];
				}
			}
		}
		return $departments_organized;
	}

	//Filter list of departments by thier privligae (It is for registrar)
	function allDepartmentsByCollege3($include_all_department = 0, $privilaged_department_ids = array(), $privilaged_collage_ids = array(), $only_active = 0) 
	{
		$departments_organized = array();

		if (empty($only_active)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $only_active;
		}

		$departments_data = $this->College->find('all', array(
			'conditions' => array(
				'College.active' => $active
			),
			'contain' => array(
				'Department' => array(
					'conditions' => array(
						'Department.active' => $active
					),
				),
				'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
			)
		));

		if (!empty($departments_data)) {
			foreach ($departments_data as $key => $college_and_department) {
				if ($include_all_department == 1) {
					$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . ' Students';
				}
				/* else if(in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
					$departments_organized[$college_and_department['College']['name']]['c~'.$college_and_department['College']['id']] = 'Freshman Program';
				} */
				foreach ($college_and_department['Department'] as $key => $department) {
					//	if(in_array($department['id'], $privilaged_department_ids)) {
					$departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
					//  }
				}
			}
		}
		//array_unshift($sections_organized, array('' => '--- Select Section ---'));
		//debug($departments_organized);
		return $departments_organized;
	}

	function allDepartmentInCollegeIncludingPre($department_ids = null, $college_ids = null, $includePre = 0, $only_active = 0)
	{
		$departments = array();

		if (empty($only_active)) {
			$active = array(0 => 0, 1 => 1);
		} else {
			$active =  $only_active;
		}

		if (!empty($department_ids)) {
			$college_s = $this->find('all', array(
				'conditions' => array(
					'Department.id' => $department_ids, 
					'Department.active' => $active
				), 
				'contain' => array(
					'College' => array(
						'conditions' => array(
							'College.active' => $active
						)
					)
				),
				'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
			));

			if (!empty($college_s)) {
				foreach ($college_s as $k => $v) {
					if ($includePre) {
						$departments[$v['College']['name']]['c~' . $v['College']['id']] = 'Pre/Freshman/Remedial - ' . $v['College']['shortname'];
					} else {
						//$departments[$v['Department']['id']] = $v['Department']['name'];
					}
					$departments[$v['College']['name']][$v['Department']['id']] = $v['Department']['name'];
				}
			}
		}

		if (!empty($college_ids)) {
			$college_s = $this->find('all', array(
				'conditions' => array(
					'Department.college_id' => $college_ids, 
					'Department.active' => $active
				), 
				'contain' => array(
					'College' => array(
						'conditions' => array(
							'College.active' => $active
						)
					)
				),
				'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
			));

			if (!empty($college_s)) {
				foreach ($college_s as $k => $v) {
					if ($includePre) {
						$departments[$v['College']['name']]['c~' . $v['College']['id']] = 'Pre/Freshman/Remedial - ' . $v['College']['shortname'];
					} else {
						//$departments[$v['Department']['id']] = $v['Department']['name'];
					}
					$departments[$v['College']['name']][$v['Department']['id']] = $v['Department']['name'];
				}
			}
		}
		return $departments;
	}

	function allUnits($role_id = null, $unit_id = null, $allUnit = 0)
	{
		$departments_organized = array();
		//debug($allUnit);

		if ($role_id == ROLE_COLLEGE && $allUnit == 0) {
			$departments_data = $this->College->find('all', array(
				'conditions' => array('College.id' => $unit_id),
				'contain' => array(
					'Department' => array(
						'conditions' => array('Department.active' => 1),
						'Specialization',
						'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
					)
				)
			));

			if (!empty($departments_data)) {
				foreach ($departments_data as $key => $college_and_department) {
					$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['name'] . '';
				}
			}
		} elseif ($role_id == ROLE_DEPARTMENT && $allUnit == 0) {
			debug($unit_id);
			$departments_data = $this->find('all', array('conditions' => array('Department.id' => $unit_id), 'contain' => array('College', 'Specialization')));
			debug($departments_data);

			if (!empty($departments_data)) {
				foreach ($departments_data as $key => $department) {
					$departments_organized[$department['College']['name']]['d~' . $department['Department']['id']] = 'Department of ' . $department['Department']['name'];
					if (!empty($department['Specialization'])) {
						foreach ($department['Specialization'] as $skey => $spec) {
							$departments_organized[$department['Department']['name']][$spec['id']] = $spec['name'];
						}
					}
				}
			}
		} elseif ($role_id == ROLE_REGISTRAR && $allUnit == 0) {
			$departments_data = $this->College->find('all', array(
				'contain' => array(
					'Department' => array(
						'conditions' => array('Department.active' => 1),
						'Specialization',
						'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
					)
				),
				'conditions' => array('College.active' => 1),
			));
			
			if (!empty($departments_data)) {
				foreach ($departments_data as $key => $college_and_department) {
					$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['name'] . '';
					foreach ($college_and_department['Department'] as $key => $department) {
						$departments_organized[$college_and_department['College']['name']]['d~' . $department['id']] =  $department['name'];
						if (!empty($department['Specialization'])) {
							foreach ($department['Specialization'] as $skey => $spec) {
								$departments_organized[$department['Department']['name']][$spec['id']] = $spec['name'];
							}
						}
					}
				}
			}
		} elseif ($allUnit == 1) {
			$departments_data = $this->College->find('all', array(
				'contain' => array(
					'Department' => array(
						'conditions' => array('Department.active' => 1), 
						'Specialization',
						'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
					)
				), 
				'conditions' => array('College.active' => 1)
			));
			
			if (!empty($departments_data)) {
				foreach ($departments_data as $key => $college_and_department) {
					$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['name'] . '';
					foreach ($college_and_department['Department'] as $key => $department) {
						$departments_organized[$college_and_department['College']['name']]['d~' . $department['id']] = 'Department of ' . $department['name'];
						if (!empty($department['Specialization'])) {
							foreach ($department['Specialization'] as $skey => $spec) {
								$departments_organized[$department['Department']['name']][$spec['id']] = $spec['name'];
							}
						}
					}
				}
			}
		}
		return $departments_organized;
	}
}
