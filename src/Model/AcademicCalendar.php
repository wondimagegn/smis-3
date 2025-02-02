<?php
class AcademicCalendar extends AppModel
{
	public $name = 'AcademicCalendar';

	public $virtualFields = array('full_year' => 'CONCAT(AcademicCalendar.academic_year, "-",AcademicCalendar.semester)');

	public $validate = array(
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide academic year',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'semester' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide semester',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_registration_end_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'course_registration_start_date'),
				'message' => 'Course registration end date should be greater than start date',
			)
		),
		'course_add_end_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'course_add_start_date'),
				'message' => 'Course add end date should be greater than start date'
			)
		),
		'course_add_start_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'course_registration_end_date'),
				'message' => 'Course add start date should be greater  than course registration end date',
			),
		),
		'course_drop_end_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'course_drop_start_date'),
				'message' => 'Course drop end date should be greater than start date'
			)
		),
		'course_drop_start_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'course_registration_end_date'),
				'message' => 'Course drop start date should be greater than course registration end date',
			),
		),
		'grade_submission_end_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'grade_submission_start_date'),
				'message' => 'Grade submission end date should be greater than start date'
			)
		),
		'grade_submission_start_date' => array(
			'comparison' => array(
				'rule' => array('field_comparison', '>', 'course_registration_end_date'),
				'message' => 'Grade submission start date should be greater than course registration end date',
			),
		),
	);


	function field_comparison($check1, $operator, $field2)
	{
		foreach ($check1 as $key => $value1) {
			$value2 = $this->data[$this->alias][$field2];
			if (!Validation::comparison($value1, $operator, $value2)) {
				return false;
			}
		}
		return true;
	}
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'year_level_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasMany = array(
		'CourseRegistration' => array(
			'className' => 'CourseRegistration',
			'foreignKey' => 'academic_calendar_id',
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
		'ExtendingAcademicCalendar' => array(
			'className' => 'ExtendingAcademicCalendar',
			'foreignKey' => 'academic_calendar_id',
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


	function check_registration($academic_year = null, $semester = null, $department_college_id = null, $year_level_id = null, $program_id = null, $program_type_id = null)
	{

		$course_registration_start_date = null;
		//debug(serialize($department_college_id));
		$yearLevelForSearch = ($year_level_id == 0 ? '1st' : $year_level_id);

		if (!empty($program_id) && !empty($program_type_id) && !empty($department_college_id)) {
			$academic_calendar = $this->find('all', array(
				'conditions' => array(
					'academic_year' => $academic_year,
					'semester' => $semester,
					'program_id' => $program_id,
					'department_id LIKE ' => '%'. (serialize($department_college_id)) .'%',
					'year_level_id LIKE ' => '%'. (serialize($yearLevelForSearch)) .'%',
					'program_type_id' => $program_type_id
				), 
				'order' => array('AcademicCalendar.id' => 'DESC'),
				'recursive' => -1
			));
		} else if (!empty($program_id) && !empty($program_type_id)) {
			$academic_calendar = $this->find('all', array(
				'conditions' => array(
					'academic_year' => $academic_year,
					'semester' => $semester,
					'program_id' => $program_id,
					'program_type_id' => $program_type_id
				),
				'order' => array('AcademicCalendar.id' => 'DESC'),
				'recursive' => -1
			));
		} else {
			$academic_calendar = $this->find('all', array('conditions' => array('academic_year' => $academic_year, 'semester' => $semester), 'order' => array('AcademicCalendar.id' => 'DESC'), 'recursive' => -1));
		}

		debug($academic_calendar);

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$acv['AcademicCalendar']['college_id'] = unserialize($acv['AcademicCalendar']['college_id']);
				$acv['AcademicCalendar']['department_id'] = unserialize($acv['AcademicCalendar']['department_id']);
				$acv['AcademicCalendar']['year_level_id'] = unserialize($acv['AcademicCalendar']['year_level_id']);
			}
		}

		//debug($academic_calendar);

		//check that student can register
		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {

				$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $yearLevelForSearch, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'registration');
				debug($daysAdded);

				$course_registration_start_date = $acv['AcademicCalendar']['course_registration_start_date'];
				
				if (in_array($department_college_id, $acv['AcademicCalendar']['department_id']) && in_array($yearLevelForSearch, $acv['AcademicCalendar']['year_level_id'])) {
					//check deadline is not passed.
					if ((date('Y-m-d') >= $acv['AcademicCalendar']['course_registration_start_date']) && (date('Y-m-d') <= date('Y-m-d', strtotime($acv['AcademicCalendar']['course_registration_end_date'] . ' +' . $daysAdded . ' days ')))) {
						return 1;
					}
				}
				//fresh man check
				/* if ($year_level_id == 0) {
					if (in_array('pre_' . $department_college_id, $acv['AcademicCalendar']['department_id']) && in_array('1st', $acv['AcademicCalendar']['year_level_id'])) {
						if ((date('Y-m-d') >= $acv['AcademicCalendar']['course_registration_start_date']) && date('Y-m-d') <= date('Y-m-d', strtotime($acv['AcademicCalendar']['course_registration_end_date'] . ' +' . $daysAdded . ' days '))) {
							return 1;
						}
					}
				} */
			}
		}

		if (!empty($course_registration_start_date) && date('Y-m-d') < $course_registration_start_date) {
			return $course_registration_start_date;
		} else if (!empty($course_registration_start_date) && date('Y-m-d') > $course_registration_start_date) {
			return 2;
		} 

		return false;
	}

	function check_add_date_end($academic_year = null, $semester = null, $department_college_id = null, $year_level_id = null, $program_id = null, $program_type_id = null ) 
	{
		//return 1;
		//debug(serialize($department_college_id));

		$yearLevelForSearch = ($year_level_id == 0 ? '1st' : $year_level_id);

		if (!empty($program_id) && !empty($program_type_id) && !empty($department_college_id)) {
			$academic_calendar = $this->find('all', array(
				'conditions' => array(
					'academic_year' => $academic_year,
					'semester' => $semester,
					'program_id' => $program_id,
					'department_id LIKE ' => '%'. (serialize($department_college_id)) .'%',
					'year_level_id LIKE ' => '%'. (serialize($yearLevelForSearch)) .'%',
					'program_type_id' => $program_type_id
				), 
				'order' => array('AcademicCalendar.id' => 'DESC'),
				'recursive' => -1
			));
		} else if (!empty($program_id) && !empty($program_type_id)) {
			$academic_calendar = $this->find('all', array(
				'conditions' => array(
					'academic_year' => $academic_year,
					'semester' => $semester,
					'program_id' => $program_id,
					'program_type_id' => $program_type_id
				),
				'order' => array('AcademicCalendar.id' => 'DESC'),
				'recursive' => -1
			));
		} else {
			$academic_calendar = $this->find('all', array('conditions' => array('academic_year' => $academic_year, 'semester' => $semester), 'order' => array('AcademicCalendar.id' => 'DESC'), 'recursive' => -1));
		}

		debug($academic_calendar);

		$course_add_start_date = null;
		$course_add_end_date = null;
		$course_registration_end_date = null;

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$acv['AcademicCalendar']['college_id'] = unserialize($acv['AcademicCalendar']['college_id']);
				$acv['AcademicCalendar']['department_id'] = unserialize($acv['AcademicCalendar']['department_id']);
				$acv['AcademicCalendar']['year_level_id'] = unserialize($acv['AcademicCalendar']['year_level_id']);
			}
		}
		
		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $yearLevelForSearch, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'add');
				debug($daysAdded);
				$course_add_start_date = $acv['AcademicCalendar']['course_add_start_date'];
				$course_add_end_date = $acv['AcademicCalendar']['course_add_start_date'];
				$course_registration_end_date = $acv['AcademicCalendar']['course_registration_end_date'];

				if (in_array($department_college_id, $acv['AcademicCalendar']['department_id']) && in_array($yearLevelForSearch, $acv['AcademicCalendar']['year_level_id'])) {
					// check deadline is not passed.Course add is possible if today >= add start date and today <= add end date replace with next line of code when it is stable replace the below code after test
					if ((date('Y-m-d') >= $acv['AcademicCalendar']['course_add_start_date']) && (date('Y-m-d') <= date('Y-m-d', strtotime($acv['AcademicCalendar']['course_add_end_date'] . ' +' . $daysAdded . ' days ')))) {
						return 1;
					}
				}
				//fresh man check
				/* if ($year_level_id == 0) { 
					if (in_array('pre_' . $department_college_id, $acv['AcademicCalendar']['department_id']) && in_array('1st', $acv['AcademicCalendar']['year_level_id'])) {
						if ((date('Y-m-d') >= $acv['AcademicCalendar']['course_add_start_date']) && date('Y-m-d') <= date('Y-m-d', strtotime($acv['AcademicCalendar']['course_add_end_date'] . ' +' . $daysAdded . ' days '))) {
							return 1;
						}
					}
				} */
			}
		}

		if (!empty($course_add_start_date) && date('Y-m-d') < $course_add_start_date) {
			return $course_add_start_date;
		} else if (!empty($course_add_start_date) && date('Y-m-d') > $course_add_start_date) {
			return 2;
		}

		return false;
	}

	function check_add_date_start($academic_year = null, $semester = null, $department_college_id = null, $year_level_id = null) 
	{
		$academic_calendar = $this->find('all', array('conditions' => array('academic_year' => $academic_year, 'semester' => $semester), 'recursive' => -1));
		
		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$acv['AcademicCalendar']['college_id'] = unserialize($acv['AcademicCalendar']['college_id']);
				$acv['AcademicCalendar']['department_id'] = unserialize($acv['AcademicCalendar']['department_id']);
				$acv['AcademicCalendar']['year_level_id'] = unserialize($acv['AcademicCalendar']['year_level_id']);
			}
		}

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				if (in_array($department_college_id, $acv['AcademicCalendar']['department_id']) && in_array($year_level_id, $acv['AcademicCalendar']['year_level_id'])) {
					//check deadline is not passed.
					if ($acv['AcademicCalendar']['course_add_start_date'] >= date('Y-m-d')) {
						return $acv['AcademicCalendar']['id'];
					}
				}
			}
		}

		return false;
	}


	function check_add_drop_end($academic_year = null, $semester = null, $department_college_id = null, $year_level_id = null, $program_id = null, $program_type_id = null) 
	{
		$course_drop_start_date = null;

		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$academic_calendar = $this->find('all', array(
				'conditions' => array(
					'academic_year' => $academic_year,
					'semester' => $semester,
					'program_id' => $program_id,
					'program_type_id' => $program_type_id
				), 
				'recursive' => -1
			));
		} else {
			$academic_calendar = $this->find('all', array('conditions' => array('academic_year' => $academic_year, 'semester' => $semester), 'recursive' => -1));
		}

		//$academic_calendar = $this->find('all', array('conditions' => array('academic_year' => $academic_year, 'semester' => $semester), 'recursive' => -1));

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$acv['AcademicCalendar']['college_id'] = unserialize($acv['AcademicCalendar']['college_id']);
				$acv['AcademicCalendar']['department_id'] = unserialize($acv['AcademicCalendar']['department_id']);
				$acv['AcademicCalendar']['year_level_id'] = unserialize($acv['AcademicCalendar']['year_level_id']);
			}
		}

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $year_level_id, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'drop');
				$course_drop_start_date = $acv['AcademicCalendar']['course_drop_start_date'];
				
				if (in_array($department_college_id, $acv['AcademicCalendar']['department_id']) && in_array($year_level_id, $acv['AcademicCalendar']['year_level_id'])) {
					//check deadline is not passed.Course add is possible if today >= add start date and today <= add end date replace with next line of code when it is stable replace the below code after test
					if ((date('Y-m-d') >= $acv['AcademicCalendar']['course_drop_start_date']) && (date('Y-m-d') <= date('Y-m-d', strtotime($acv['AcademicCalendar']['course_drop_end_date'] . ' +' . $daysAdded . ' days ')))) {
						return 1;
					}
				}

				//fresh man check
				if ($year_level_id == 0 || $year_level_id == '' || is_null($year_level_id)) {
					if (in_array('pre_' . $department_college_id, $acv['AcademicCalendar']['department_id']) && in_array('1st', $acv['AcademicCalendar']['year_level_id'])) {
						if ((date('Y-m-d') >= $acv['AcademicCalendar']['course_drop_start_date']) && date('Y-m-d') <= date('Y-m-d', strtotime($acv['AcademicCalendar']['course_drop_end_date'] . ' +' . $daysAdded . ' days '))) {
							return 1;
						}
					}
				}
			}
		}

		if (date('Y-m-d') < $course_drop_start_date) {
			return $course_drop_start_date;
		}

		return false;
	}

	function check_registration_add_drop_start_end($academic_year = null, $semester = null, $department_college_id = null, $year_level_id = null, $program_id = null, $program_type_id = null, $type = '') 
	{
		$activity_start_date = null;

		if (!empty($type)) {
			if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
				$academic_calendar = $this->find('all', array(
					'conditions' => array(
						'academic_year' => $academic_year,
						'semester' => $semester,
						'program_id' => $program_id,
						'program_type_id' => $program_type_id
					), 
					'recursive' => -1
				));
			} else {
				$academic_calendar = $this->find('all', array('conditions' => array('academic_year' => $academic_year, 'semester' => $semester), 'recursive' => -1));
			}

			if (!empty($academic_calendar)) {
				foreach ($academic_calendar as $ack => &$acv) {
					$acv['AcademicCalendar']['college_id'] = unserialize($acv['AcademicCalendar']['college_id']);
					$acv['AcademicCalendar']['department_id'] = unserialize($acv['AcademicCalendar']['department_id']);
					$acv['AcademicCalendar']['year_level_id'] = unserialize($acv['AcademicCalendar']['year_level_id']);
				}
			}

			if (!empty($academic_calendar)) {
				foreach ($academic_calendar as $ack => &$acv) {

					if ($type == 'registration') {
						$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $year_level_id, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'registration');
						$activity_start_date = $acv['AcademicCalendar']['course_registration_start_date'];
						$activity_end_date = $acv['AcademicCalendar']['course_registration_end_date'];
					} else if ($type == 'add') {
						$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $year_level_id, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'add');
						$activity_start_date = $acv['AcademicCalendar']['course_add_start_date'];
						$activity_end_date = $acv['AcademicCalendar']['course_add_end_date'];
					} else if ($type == 'drop') {
						$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $year_level_id, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'drop');
						$activity_start_date = $acv['AcademicCalendar']['course_drop_start_date'];
						$activity_end_date = $acv['AcademicCalendar']['course_drop_end_date'];
					}

					
					if (in_array($department_college_id, $acv['AcademicCalendar']['department_id']) && in_array($year_level_id, $acv['AcademicCalendar']['year_level_id'])) {
						//check deadline is not passed.Course add is possible if today >= add start date and today <= add end date replace with next line of code when it is stable replace the below code after test
						if (isset($activity_start_date)  && isset($activity_end_date)) {
							if ((date('Y-m-d') >= $activity_start_date) && (date('Y-m-d') <= date('Y-m-d', strtotime($activity_end_date . ' +' . $daysAdded . ' days ')))) {
								return 1;
							}
						}
					}

					//fresh man check
					if ($year_level_id == 0 || $year_level_id == '' || is_null($year_level_id)) {
						if (in_array('pre_' . $department_college_id, $acv['AcademicCalendar']['department_id']) && in_array('1st', $acv['AcademicCalendar']['year_level_id'])) {
							if ((date('Y-m-d') >= $activity_start_date) && date('Y-m-d') <= date('Y-m-d', strtotime($activity_end_date . ' +' . $daysAdded . ' days '))) {
								return 1;
							}
						}
					}
				}
			}

			if (date('Y-m-d') < $activity_start_date) {
				return $activity_start_date;
			}

		}

		return false;
	}

	function check_grade_submission_end($academic_year = null, $semester = null, $department_college_id = null, $year_level_id) 
	{
		$academic_calendar = $this->find('all', array(
			'conditions' => array(
				'academic_year' => $academic_year, 
				'semester' => $semester
			), 
			'recursive' => -1
		));

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$acv['AcademicCalendar']['college_id'] = unserialize($acv['AcademicCalendar']['college_id']);
				$acv['AcademicCalendar']['department_id'] = unserialize($acv['AcademicCalendar']['department_id']);
				$acv['AcademicCalendar']['year_level_id'] = unserialize($acv['AcademicCalendar']['year_level_id']);
			}
		}
		//check that instructor can submit  grade

		if (!empty($academic_calendar)) {
			foreach ($academic_calendar as $ack => &$acv) {
				$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($acv['AcademicCalendar']['id'], $year_level_id, $department_college_id, $acv['AcademicCalendar']['program_id'], $acv['AcademicCalendar']['program_type_id'], 'grade_submission');

				if (in_array($department_college_id, $acv['AcademicCalendar']['department_id']) && in_array($year_level_id, $acv['AcademicCalendar']['year_level_id'])) {
					//check deadline is not passed.
					if (date('Y-m-d', strtotime($acv['AcademicCalendar']['grade_submission_end_date'] . ' +' . $daysAdded . ' days ')) >= date('Y-m-d')) {
						return $acv['AcademicCalendar']['id'];
					}
				}
			}
		}

		return false;
	}

	function check_duplicate_entry($data = null)
	{
		$existed_dept = array();

		debug($data);

		if (!empty($data['AcademicCalendar']['id'])) {
			$academic_calendars = $this->find('all', array(
				'conditions' => array(
					'AcademicCalendar.id <> ' => $data['AcademicCalendar']['id'],
					'AcademicCalendar.academic_year' => $data['AcademicCalendar']['academic_year'],
					'AcademicCalendar.semester' => $data['AcademicCalendar']['semester'],
					'AcademicCalendar.program_id' => $data['AcademicCalendar']['program_id'],
					'AcademicCalendar.program_type_id' => $data['AcademicCalendar']['program_type_id'],
					'AcademicCalendar.year_level_id' => serialize($data['AcademicCalendar']['year_level_id']),
				),
				'recursive' => -1
			));
		} else {
			$academic_calendars = $this->find('all', array(
				'conditions' => array(
					'AcademicCalendar.academic_year' => $data['AcademicCalendar']['academic_year'],
					'AcademicCalendar.semester' => $data['AcademicCalendar']['semester'],
					'AcademicCalendar.program_id' => $data['AcademicCalendar']['program_id'],
					'AcademicCalendar.program_type_id' => $data['AcademicCalendar']['program_type_id'],
					'AcademicCalendar.year_level_id' => serialize($data['AcademicCalendar']['year_level_id']),
				),
				'recursive' => -1
			));
		}

		//debug($academic_calendars);

		//debug(serialize($data['AcademicCalendar']['year_level_id']));

		if (!empty($academic_calendars)) {
			foreach ($academic_calendars as $index => $academic_calendar) {
				$departments_ids = unserialize($academic_calendar['AcademicCalendar']['department_id']);

				if (!empty($departments_ids)) {
					$year_level_ids = unserialize($academic_calendar['AcademicCalendar']['year_level_id']);
					//debug($year_level_ids);
					foreach ($departments_ids as $dep_key => $dep_id) {
						foreach ($year_level_ids as $year_key => $year_id) {
							if (in_array($dep_id, $departments_ids) && in_array($year_id, $year_level_ids)) {
								$existed_dept[$year_id][] = $dep_id;
							}
						}
					}
				}
			}
		}

		if (!empty($existed_dept)) {
			$alreadyexistedyearlevel = array();
			$depts = array();
			foreach ($existed_dept as $year => $departments) {
				if (!empty($departments)) {
					foreach ($departments as $key => $value) {
						if (!in_array($value, $depts)) {
							$depts[] = $value;
						}

						if (/* (count(explode('pre_',$value)) == 0) ||  */is_numeric($value)) {
							$alreadyexistedyearlevel[] = 'You have already setup an academic calendar for ' . $year . ' year '. ClassRegistry::init('Department')->field('name', array('Department.id' => $value));
						} else {
							$alreadyexistedyearlevel[] = 'You have already setup an academic calendar for ' . $year . ' year level';
						}
					}
				}
			}

			if (!empty($alreadyexistedyearlevel)) {
				$this->invalidate('duplicate', $alreadyexistedyearlevel[0] . ' and ' . (count($depts)-1) . ' others.');
				$this->invalidate('departmentduplicate', $departments);
				$this->invalidate('yearlevelduplicate', $alreadyexistedyearlevel);
				return false;
			}
		}

		return true;
	}

	function daysAvaiableForGradeChange($program_id = null, $program_type_id = null)
	{
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['daysAvaiableForGradeChange']) && !empty($settings['GeneralSetting']['daysAvaiableForGradeChange'])) {
				return $settings['GeneralSetting']['daysAvaiableForGradeChange'];
			} else {
				return DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
			}
		} else {
			return DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
		}
	}

	function daysAvaiableForNgToF($program_id = null, $program_type_id = null)
	{
		//Important Note: The maximum allowed days should not be greater than 4 months
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['daysAvaiableForNgToF']) && !empty($settings['GeneralSetting']['daysAvaiableForNgToF'])) {
				return $settings['GeneralSetting']['daysAvaiableForNgToF'];
			} else {
				return DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
			}
		} else {
			return DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
		}
		return DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
	}

	function daysAvaiableForDoToF($program_id = null, $program_type_id = null)
	{
		//Important Note: The maximum allowed days should not be greater than 4 months
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['daysAvaiableForDoToF']) && !empty($settings['GeneralSetting']['daysAvaiableForDoToF'])) {
				return $settings['GeneralSetting']['daysAvaiableForDoToF'];
			} else {
				return DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
			}
		} else {
			return DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
		}
		return DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
	}

	function daysAvailableForFxToF($program_id = null, $program_type_id = null)
	{
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['daysAvailableForFxToF']) && !empty($settings['GeneralSetting']['daysAvailableForFxToF'])) {
				return $settings['GeneralSetting']['daysAvailableForFxToF'];
			} else {
				return DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;
			}
		} else {
			return DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;
		}
	}

	function isFxConversionDate($academicCalendar, $department_id, $publishedDetail)
	{
		$calendar = $this->getAcademicCalenderDepartment($academicCalendar, $department_id);
		debug($calendar);

		$yearLevelName = ClassRegistry::init('YearLevel')->field('YearLevel.name', array('YearLevel.id' => $publishedDetail['year_level_id']));

		if (!empty($calendar)) {
			foreach ($calendar as $k => $v) {
				if (isset($v['calendarDetail']) && !empty($v['calendarDetail'])) {
					$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays($v['calendarDetail']['id'], $yearLevelName, $publishedDetail['year_level_id'], $v['calendarDetail']['program_id'], $v['calendarDetail']['program_type_id'], 'fx_grade_submission');
					debug($daysAdded);
					if (date('Y-m-d') > date('Y-m-d', strtotime($v['calendarDetail']['grade_fx_submission_end_date'] . ' +' . $daysAdded . ' days'))) {
						return true;
					}
				}
			}
		}

		return false;
	}

	function weekCountForAcademicYearAndSemester()
	{
		return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
	}

	function weekCountForAcademicYear($program_id = null, $program_type_id = null)
	{
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['weekCountForAcademicYear']) && !empty($settings['GeneralSetting']['weekCountForAcademicYear'])) {
				return $settings['GeneralSetting']['weekCountForAcademicYear'];
			} else {
				return DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR;
			}
		} else {
			return DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR;
		}
	}

	function weekCountForOneSemester($program_id = null, $program_type_id = null)
	{
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['weekCountForOneSemester']) && !empty($settings['GeneralSetting']['weekCountForOneSemester'])) {
				return $settings['GeneralSetting']['weekCountForOneSemester'];
			} else {
				return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
			}
		} else {
			return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
		}
	}

	function semesterCountForAcademicYear($program_id = null, $program_type_id = null)
	{
		if (isset($program_id) && !empty($program_id) && isset($program_type_id) && !empty($program_type_id)) {
			$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
				'conditions' => array(
					'GeneralSetting.program_id like' => '%s:_:"' . $program_id . '"%', 
					'GeneralSetting.program_type_id like' => '%s:_:"' . $program_type_id . '"%'
				), 
				'recursive' => -1
			));

			if (isset($settings['GeneralSetting']['semesterCountForAcademicYear']) && !empty($settings['GeneralSetting']['semesterCountForAcademicYear'])) {
				return $settings['GeneralSetting']['semesterCountForAcademicYear'];
			} else {
				return DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
			}
		} else {
			return DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
		}
	}

	function currentSemesterInTheDefinedAcademicCalender($academic_year)
	{
		$currentAcademicCalender = ClassRegistry::init('AcademicCalendar')->find('all', array(
			'conditions' => array(
				'AcademicCalendar.academic_year' => $academic_year
			)
		));

		if (!empty($currentAcademicCalender)) {
			foreach ($currentAcademicCalender as $k => $v) {
				$now = time(); // or your date as well
				$your_date = strtotime($v['AcademicCalendar']['course_registration_start_date']);
				$datediff = floor(($now - $your_date) / (60 * 60 * 24));
				//	debug($datediff);
				if ($datediff < 130) {
					return 	$v['AcademicCalendar']['semester'];
				}
			}
		}

		return 'I';
	}

	function semesterStartAndEndMonth($semester, $academic_year)
	{
		$m = array(
			'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 'Jun' => 0, 
			'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0
		);

		return $m;

		$currentAcademicCalender = ClassRegistry::init('AcademicCalendar')->find('all', array(
			'conditions' => array(
				'AcademicCalendar.academic_year' => $academic_year, 
				'AcademicCalendar.semester' => $semester
			)
		));

		$months = array();

		if (!empty($currentAcademicCalender)) {
			foreach ($currentAcademicCalender as $k => $v) {
				$rtime = strtotime($v['AcademicCalendar']['course_registration_start_date']);
				$rmonth = date("M", $rtime);
				$months[$rmonth] = 0;
				$gtime = strtotime($v['AcademicCalendar']['grade_submission_end_date']);
				$gmonth = date("M", $gtime);
				$months[$gmonth] = 0;
			}
		}
		//usort($months);
		return $months;
	}

	function getAcademicCalender($currentAcademicYear)
	{
		$calender = array();

		$currentAcademicCalender = ClassRegistry::init('AcademicCalendar')->find('all', array(
			'conditions' => array('AcademicCalendar.academic_year' => $currentAcademicYear), 
			'contain' => array('Program', 'ProgramType')
		));

		if (!empty($currentAcademicCalender)) {
			foreach ($currentAcademicCalender as $index => $academic_calendar) {
				$department_ids = unserialize($academic_calendar['AcademicCalendar']['department_id']);
				$year_level_ids = unserialize($academic_calendar['AcademicCalendar']['year_level_id']);
				foreach ($department_ids as $k => $v) {
					if (strpos($v, 'pre_') !== false) {
						$calender[$v]['departmentname'] = 'Pre(' . ClassRegistry::init('College')->field('name', array('College.id' => $v)) . ')';
						$calender[$v]['yearlevel'] = '1st';
					} else {
						$calender[$v]['departmentname'] = ClassRegistry::init('Department')->field('name', array('Department.id' => $v));
						$calender[$v]['yearlevel'] = $year_level_ids;
					}
					$calender[$v]['calendarDetail'] = $academic_calendar;
				}
			}
		}
		return $calender;
	}

	function getAcademicCalenderDepartment($currentAcademicYear, $department_id)
	{
		$calender = array();

		$currentAcademicCalender = ClassRegistry::init('AcademicCalendar')->find('all', array(
			'conditions' => array(
				'AcademicCalendar.academic_year' => $currentAcademicYear,
				'AcademicCalendar.department_id like' => '%s:_:"' . $department_id . '"%',
			), 
			'contain' => array('Program', 'ProgramType')
		));

		if (!empty($currentAcademicCalender)) {
			foreach ($currentAcademicCalender as $index => $academic_calendar) {
				$department_ids = unserialize($academic_calendar['AcademicCalendar']['department_id']);
				$year_level_ids = unserialize($academic_calendar['AcademicCalendar']['year_level_id']);
				foreach ($department_ids as $k => $v) {
					if (strpos($v, 'pre_') !== false) {
						$calender[$v]['departmentname'] = 'Pre(' . ClassRegistry::init('College')->field('name', array('College.id' => $v)) . ')';
						$calender[$v]['yearlevel'] = '1st';
					} else {
						$calender[$v]['departmentname'] = ClassRegistry::init('Department')->field('name', array('Department.id' => $v));
						$calender[$v]['yearlevel'] = $year_level_ids;
					}
					$calender[$v]['calendarDetail'] = $academic_calendar;
				}
			}
		}

		return $calender;
	}

	function getComingAcademicCalendarsDeadlines($currentAcademicYear, $department_id)
	{
		$currentAcademicCalender = ClassRegistry::init('AcademicCalendar')->find('all', array(
			'conditions' => array(
				'AcademicCalendar.academic_year' => $currentAcademicYear,
				'AcademicCalendar.department_id like' => '%s:_:"' . $department_id . '"%',
			), 
			'contain' => array('Program', 'ProgramType')
		));

		$deadlines = array();

		if (!empty($currentAcademicCalender)) {
			$count = 0;
			debug($currentAcademicCalender);
			foreach ($currentAcademicCalender as $index => $academic_calendar) {

				$today = date('Y-m-d');
				$gradeSubmissionEndDate = strtotime($academic_calendar['AcademicCalendar']['grade_submission_end_date'] . '-5 day');

				if (strtotime('"' . $gradeSubmissionEndDate . '"' . '-5 day') > strtotime($today)) {
					// no need to notify
					$deadlines[$count]['GradeSubmissionDeadline'] = date("F j, Y, g:i a", $gradeSubmissionEndDate);
				} else if (isset($gradeSubmissionEndDate) && !empty($gradeSubmissionEndDate)) {
					$deadlines[$count]['GradeSubmissionDeadline'] = date("F j, Y, g:i a", $gradeSubmissionEndDate);
				}
				$count++;
			}
			//Grade submission,Add/Drop,Grade Change,Fx Application Deadline
		}
		return $deadlines;
	}

	function minimumCreditForStatus($student_id)
	{

		//$studentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));
		$studentDetail = ClassRegistry::init('Student')->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id
			), 
			'contain' => array('Curriculum'),
			'recursive' => -1
		));

		$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
			'conditions' => array(
				'GeneralSetting.program_id like' => '%s:_:"' . $studentDetail['Student']['program_id'] . '"%',
				'GeneralSetting.program_type_id like' => '%s:_:"' . $studentDetail['Student']['program_type_id'] . '"%',

			), 'recursive' => -1
		));

		debug($settings);

		if (!empty($settings)) {
			if (isset($studentDetail['Curriculum']['id']) && is_numeric($studentDetail['Curriculum']['id'])) {
				if (count(explode('ECTS', $studentDetail['Curriculum']['type_credit'])) >= 2) {
					return (int) round($settings['GeneralSetting']['minimumCreditForStatus'] * CREDIT_TO_ECTS);
				} else {
					return $settings['GeneralSetting']['minimumCreditForStatus'];
				}
			} else {
				return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
			}
		}
		
		return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
	}

	function maximumCreditPerSemester($student_id)
	{
		$studentDetail = ClassRegistry::init('Student')->find('first', array(
			'conditions' => array('Student.id' => $student_id), 
			'contain' => array(
				'Curriculum' => array('id', 'name', 'type_credit')
			),
			'fields' => array('id', 'full_name', 'program_id', 'program_type_id', 'curriculum_id', 'department_id', 'college_id')
		));

		$settings = ClassRegistry::init('GeneralSetting')->find('first', array(
			'conditions' => array(
				'GeneralSetting.program_id like' => '%s:_:"' . $studentDetail['Student']['program_id'] . '"%',
				'GeneralSetting.program_type_id like' => '%s:_:"' . $studentDetail['Student']['program_type_id'] . '"%',
			), 
			'recursive' => -1
		));

		//debug($settings);

		if (!empty($settings)) {
			if (isset($studentDetail['Curriculum']['id']) && is_numeric($studentDetail['Curriculum']['id'])) {
				if ($settings['GeneralSetting']['maximumCreditPerSemester'] != 0) {
					if (count(explode('ECTS', $studentDetail['Curriculum']['type_credit'])) >= 2) {
						return (int) round($settings['GeneralSetting']['maximumCreditPerSemester'] * CREDIT_TO_ECTS);
					} else {
						return $settings['GeneralSetting']['maximumCreditPerSemester'];
					}
				} else {
					return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
				}
			} else {
				return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
			}
		}

		return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
	}


	public function getMostRecentAcademicCalenderForSMS($phonenumber)
	{
		$studentDetail = ClassRegistry::init('Student')->find('first', array(
			'conditions' => array('Student.phone_mobile' => $phonenumber), 
			'contain' => array('User')
		));

		if (!empty($studentDetail)) {

			$yearAndAcademicYear = ClassRegistry::init('Section')->getStudentYearLevel($studentDetail['Student']['id']);

			// return $department_id_ser;
			$recentAcademicCalendar = $this->find('first', array(
				'conditions' => array(
					'AcademicCalendar.academic_year' => $yearAndAcademicYear['academicyear'],
					'AcademicCalendar.program_id' => $studentDetail['Student']['program_id'],
					'AcademicCalendar.department_id like' => '%s:_:"' . $studentDetail['Student']['department_id'] . '"%',
					'AcademicCalendar.year_level_id like' => '%s:_:"' . $yearAndAcademicYear['year'] . '"%',
					'AcademicCalendar.program_type_id' => $studentDetail['Student']['program_type_id']
				),
				'order' => array('AcademicCalendar.academic_year DESC', 'AcademicCalendar.semester DESC'),
				'recursive' => -1
			));

			if (!empty($recentAcademicCalendar)) {
				return $this->formateAcademicCalendarForSMS($recentAcademicCalendar);
			}

			return "No registration/add/drop deadline defined for you.";
			// return $message;
		} 
		return "No registration date to be displayed for you.";
	}

	public function formateAcademicCalendarForSMS($academicCalender)
	{
		//return $academicCalender;
		$message =  
		"Academic Year:" . $academicCalender['AcademicCalendar']['academic_year'] .
		" Semester: " . $academicCalender['AcademicCalendar']['semester'] . 
		"\nRegistration Start: " . date("F j,Y,g:i a", strtotime($academicCalender['AcademicCalendar']['course_registration_start_date'])) . 
		"\nRegistration End:" . date("F j,Y,g:i a", strtotime($academicCalender['AcademicCalendar']['course_registration_end_date'])) . 
		"\nAdd Start:" . date("F j,Y,g:i a", strtotime($academicCalender['AcademicCalendar']['course_add_start_date'])) . 
		"\nAdd End:" . date("F j,Y,g:i a", strtotime($academicCalender['AcademicCalendar']['course_add_start_date'])) . 
		"\nDrop Start:" . date("F j,Y,g:i a", strtotime($academicCalender['AcademicCalendar']['course_drop_start_date'])) . 
		"\nDrop End:" . date("F j,Y,g:i a", strtotime($academicCalender['AcademicCalendar']['course_drop_end_date'])) . "";
		return $message;
	}

	public function recentAcademicYearSchedule($academicyear, $semester, $program_id, $program_type_id, $department_id, $year, $freshman = 0, $college_id = null)
	{
		if ($freshman == 0) {
			$recentAcademicCalendar = $this->find('first', array(
				'conditions' => array(
					'AcademicCalendar.academic_year' => $academicyear,
					'AcademicCalendar.semester' => $semester,
					'AcademicCalendar.program_id' => $program_id,
					'AcademicCalendar.department_id like' => '%s:_:"' . $department_id . '"%',
					'AcademicCalendar.year_level_id like' => '%s:_:"' . $year . '"%',
					'AcademicCalendar.program_type_id' => $program_type_id
				),
				'order' => array('AcademicCalendar.academic_year DESC', 'AcademicCalendar.semester DESC'),
				'recursive' => -1
			));
		} else {

			// Check this and fix for deadline not defined in getGradeSubmittedInstructorList Report, Neway.

			$year = '1st';
			$department_id = 'pre_' . $college_id;

			$recentAcademicCalendar = $this->find('first', array(
				'conditions' => array(
					'AcademicCalendar.academic_year' => $academicyear,
					'AcademicCalendar.semester' => $semester,
					'AcademicCalendar.program_id' => $program_id,
					'AcademicCalendar.department_id like' => '%s:_:"' . $department_id . '"%',
					'AcademicCalendar.year_level_id like' => '%s:_:"' . $year . '"%',
					'AcademicCalendar.program_type_id' => $program_type_id
				),
				'order' => array('AcademicCalendar.academic_year DESC', 'AcademicCalendar.semester DESC'),
				'recursive' => -1
			));

		}
		
		return $recentAcademicCalendar;
	}

	public function getPublishedCourseGradeSubmissionDate($pid)
	{

		$publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find('first', array('conditions' => array('PublishedCourse.id' => $pid), 'contain' => array('YearLevel', 'Course')));

		if (isset($publishedCourseDetail['PublishedCourse']) && !empty($publishedCourseDetail['PublishedCourse']) && $publishedCourseDetail['Course']['thesis'] == 1) {
			return date('Y-m-d', strtotime("+5 days"));
			//return date('Y-m-d H:i:s');
		}

		if (!empty($publishedCourseDetail['PublishedCourse'])) {

			if (isset($publishedCourseDetail['PublishedCourse']['department_id']) && !empty($publishedCourseDetail['PublishedCourse']['department_id'])) {
				$gradeSubmissionDate = $this->find('first', array(
					'conditions' => array(
						'AcademicCalendar.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
						'AcademicCalendar.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
						'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
						'AcademicCalendar.department_id like ' => '%s:_:"' . $publishedCourseDetail['PublishedCourse']['department_id'] . '"%',
						'AcademicCalendar.year_level_id like' => '%s:_:"' . $publishedCourseDetail['YearLevel']['name'] . '"%',
						'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
					),
					'order' => array(
						//'AcademicCalendar.academic_year DESC',
						//'AcademicCalendar.semester DESC',
						'AcademicCalendar.created DESC'
					),
					'recursive' => -1
				));
			} else {
				if (!isset($publishedCourseDetail['YearLevel']['name'])) {
					$year_level = '1st';
					$department_id = 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];
				}
				
				$gradeSubmissionDate = $this->find('first', array(
					'conditions' => array(
						'AcademicCalendar.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
						'AcademicCalendar.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
						'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
						'AcademicCalendar.department_id like ' => '%s:_:"' . $department_id . '"%',
						'AcademicCalendar.year_level_id like' => '%s:_:"' . $year_level . '"%',
						'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
					),
					'order' => array(
						//'AcademicCalendar.academic_year DESC',
						//'AcademicCalendar.semester DESC',
						'AcademicCalendar.created DESC'
					),
					'recursive' => -1
				));
			}

			if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
				return $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'];
			}
		} else {

			App::import('Component', 'AcademicYear');
			$AcademicYear = new AcademicYearComponent(new ComponentCollection);
			$current_acy_and_semester = $AcademicYear->current_acy_and_semester();

			$gradeSumissionEnd = $AcademicYear->getAcademicYearBegainingDate($current_acy_and_semester['academic_year'], $current_acy_and_semester['semester']);
			//$deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + 4 months'));
			//Check and uncomment the following lines later, Neway
			$days_to_add =  DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * 7;
			$deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + ' . $days_to_add . ' days'));
			debug($deadlineConverted);

			return $deadlineConverted;
		}
	}

	public function getFxPublishedCourseGradeSubmissionDate($pid)
	{
		$publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find('first', array('conditions' => array('PublishedCourse.id' => $pid), 'contain' => array('YearLevel', 'Course')));

		if (isset($publishedCourseDetail['PublishedCourse']) && !empty($publishedCourseDetail['PublishedCourse']) && $publishedCourseDetail['Course']['thesis'] == 1) {
			return date('Y-m-d', strtotime("+5 days"));
			//return date('Y-m-d H:i:s');
		}
		debug($publishedCourseDetail);

		if (!empty($publishedCourseDetail['PublishedCourse'])) {
			$gradeSubmissionDate = $this->find('first', array(
				'conditions' => array(
					'AcademicCalendar.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
					'AcademicCalendar.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
					'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
					'AcademicCalendar.department_id like ' => '%s:_:"' . $publishedCourseDetail['PublishedCourse']['department_id'] . '"%',
					'AcademicCalendar.year_level_id like' => '%s:_:"' . $publishedCourseDetail['YearLevel']['name'] . '"%',
					'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
				),
				'order' => array(
					//'AcademicCalendar.academic_year DESC',
					//'AcademicCalendar.semester DESC',
					'AcademicCalendar.created DESC'
				), 
				'recursive' => -1
			));

			if (!empty($gradeSubmissionDate['AcademicCalendar']['grade_fx_submission_end_date'])) {
				return $gradeSubmissionDate['AcademicCalendar']['grade_fx_submission_end_date'];
			} else {
				return date('Y-m-d', strtotime("+2 days"));
			}
		} else {
			App::import('Component', 'AcademicYear');
			$AcademicYear = new AcademicYearComponent(new ComponentCollection);
			$current_acy_and_semester = $AcademicYear->current_acy_and_semester();

			$gradeSumissionEnd = $AcademicYear->getAcademicYearBegainingDate($current_acy_and_semester['academic_year'], $current_acy_and_semester['semester']);
			$deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + 4 months'));

			return $deadlineConverted;
		}
	}

	public function getGradeSubmissionDate($academicyear, $semester, $program_id, $program_type_id, $department_id, $year)
	{
		$programID = null;
		$programTypeID = null;
		$departments = array();
		$colleges = array();
		$yearLevelName = null;
		$gradeSubmissionEndDate = null;

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$programID = $program_ids[1];
			} else {
				$programID = $program_id;
			}
		} else {
			$programID = $this->Program->find('list', array('fields' => array('id', 'id')));
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$programTypeID = $program_type_ids[1];
			} else {
				$programTypeID = $program_type_id;
			}
		} else {
			$programTypeID = $this->ProgramType->find('list', array('fields' => array('id', 'id')));
		}

		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = ClassRegistry::init('Department')->find('all', array(
					'conditions' => array(
						'Department.college_id' => $college_id[1],
						'Department.active' => 1,
					),
					'contain' => array('College', 'YearLevel')
				));

				$colleges = ClassRegistry::init('College')->find('all', array(
					'conditions' => array(
						'College.id' => $college_id[1],
						'College.active' => 1
					),
					'recursive' => -1
				));
			} else {
				$departments = ClassRegistry::init('Department')->find('all', array(
					'conditions' => array(
						'Department.id' => $department_id
					), 
					'contain' => array('College', 'YearLevel')
				));

				$colleges = ClassRegistry::init('College')->find('all', array(
					'conditions' => array(
						'College.id' => $departments[0]['College']['id']
					),
					'recursive' => -1
				));
			}
		}

		if (!empty($departments)) {
			foreach ($departments as $key => $value) {
				$yearLevel = array();

				if (!empty($year)) {
					foreach ($value['YearLevel'] as $yykey => $yyvalue) {
						if (!empty($year) && strcasecmp($year, $yyvalue['name']) == 0) {
							$yearLevel[$yykey] = $yyvalue;
						}
					}
				} else if (empty($year)) {
					$yearLevel = $value['YearLevel'];
				}

				if (!empty($yearLevel)) {
					foreach ($yearLevel as $key => $yvalue) {
						$gradeSubmissionDate = $this->find('first', array(
							'conditions' => array(
								'AcademicCalendar.academic_year' => $academicyear,
								'AcademicCalendar.semester' => $semester,
								'AcademicCalendar.program_id' => $programID,
								'AcademicCalendar.department_id like' => '%s:_:"' . $value['Department']['id'] . '"%',
								'AcademicCalendar.year_level_id like' => '%s:_:"' . $yvalue['name'] . '"%',
								'AcademicCalendar.program_type_id' => $programTypeID
							),
							'order' => array(
								'AcademicCalendar.academic_year DESC',
								'AcademicCalendar.semester DESC'
							), 
							'recursive' => -1
						));

						if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
							$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
								$gradeSubmissionDate['AcademicCalendar']['id'],
								$yvalue['name'],
								$value['Department']['id'],
								$programID,
								$programTypeID,
								'grade_submission'
							);

							if ($daysAdded){
								return date('Y-m-d', strtotime($gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'] . ' +' . $daysAdded . ' days '));
							}
							return $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'];
						}
					}
				}
			}
		}

		if (!empty($colleges)) {
			$yvalue_fresh = '1st';
			foreach ($colleges as $key => $value) {

				$fresh = 'pre_' . $value['College']['id'];

				$gradeSubmissionDate = $this->find('first', array(
					'conditions' => array(
						'AcademicCalendar.academic_year' => $academicyear,
						'AcademicCalendar.semester' => $semester,
						'AcademicCalendar.program_id' => $programID,
						//'AcademicCalendar.department_id like' => '%s:_:"pre_' . $value['College']['id'] . '"%',
						//'AcademicCalendar.year_level_id like' => '%s:_:"1st"%',
						'AcademicCalendar.department_id like' => '%s:_:"' . $fresh . '"%',
						'AcademicCalendar.year_level_id like' => '%s:_:"' . $yvalue_fresh . '"%',
						'AcademicCalendar.program_type_id' => $programTypeID
					),
					'order' => array(
						'AcademicCalendar.academic_year DESC',
						'AcademicCalendar.semester DESC'
					), 
					'recursive' => -1
				));

				if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
					$daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
						$gradeSubmissionDate['AcademicCalendar']['id'],
						$yvalue_fresh,
						$fresh,
						$programID,
						$programTypeID,
						'grade_submission'
					);

					if ($daysAdded){
						return date('Y-m-d', strtotime($gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'] . ' +' . $daysAdded . ' days '));
					}

					return $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'];
				}
			}
		}

		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$gradeSumissionEnd = $AcademicYear->getAcademicYearBegainingDate($academicyear, $semester);
		$deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + 4 months'));
		return $deadlineConverted;
	}

	public function getLastGradeChangeDate($pid)
	{
		$gradeSubmissionEndDate = null;

		$publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find('first', array('conditions' => array('PublishedCourse.id' => $pid), 'contain' => array('YearLevel', 'Course')));
		$nextAcademicYear = ClassRegistry::init('StudentExamStatus')->getNextSemster($publishedCourseDetail['PublishedCourse']['academic_year'], $publishedCourseDetail['PublishedCourse']['semester']);
		
		$publishedCourseAcademicYear['academic_year'] = $publishedCourseDetail['PublishedCourse']['academic_year'];
		$publishedCourseAcademicYear['semester'] = $publishedCourseDetail['PublishedCourse']['semester'];

		$listofAcademicYearToCheck[] = $nextAcademicYear;

		$listofAcademicYearToCheck[] = ClassRegistry::init('StudentExamStatus')->getNextSemster($nextAcademicYear['academic_year'], $nextAcademicYear['semester']);


		if (isset($publishedCourseDetail['PublishedCourse']) && !empty($publishedCourseDetail['PublishedCourse']) && $publishedCourseDetail['Course']['thesis'] == 1 ) {
			return date('Y-m-d', strtotime("+5 days"));
			//return date('Y-m-d H:i:s');
		}

		$deadlineConverted = null;

		if (!empty($listofAcademicYearToCheck)) {
			foreach ($listofAcademicYearToCheck as $kk => $kpv) {
				if (!empty($publishedCourseDetail['PublishedCourse'])) {
					if (isset($publishedCourseDetail['YearLevel']['name']) && !empty($publishedCourseDetail['YearLevel']['name'])) {
						$gradeSubmissionDate = $this->find('first', array(
							'conditions' => array(
								'AcademicCalendar.academic_year' => $kpv['academic_year'],
								'AcademicCalendar.semester' => $kpv['semester'],
								'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
								'AcademicCalendar.department_id like ' => '%s:_:"' . $publishedCourseDetail['PublishedCourse']['department_id'] . '"%',
								'AcademicCalendar.year_level_id like' => '%s:_:"' . $publishedCourseDetail['YearLevel']['name'] . '"%',
								'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
							),
							'order' => array(
								'AcademicCalendar.created DESC'
							), 
							'recursive' => -1
						));
					} else {
						if (!isset($publishedCourseDetail['YearLevel']['name'])) {
							$year_level = '1st';
							$department_id = 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];
						}

						$gradeSubmissionDate = $this->find('first', array(
							'conditions' => array(
								'AcademicCalendar.academic_year' => $kpv['academic_year'],
								'AcademicCalendar.semester' => $kpv['semester'],
								'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
								'AcademicCalendar.department_id like ' => '%s:_:"' . $department_id . '"%',
								'AcademicCalendar.year_level_id like' => '%s:_:"' . $year_level . '"%',
								'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
							),
							'order' => array(
								'AcademicCalendar.created DESC'
							), 
							'recursive' => -1
						));
					}

					if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
						return $gradeSubmissionDate['AcademicCalendar']['course_registration_start_date'];
					}

				} else {

					App::import('Component', 'AcademicYear');
					$AcademicYear = new AcademicYearComponent(new ComponentCollection);

					$gradeSumissionEnd = $AcademicYear->getAcademicYearBegainingDate($kpv['academic_year'], $kpv['semester']);
					$deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + 4 months'));

					//return $deadlineConverted;
				}
			}
		}
		// nothing is defined for next academic year, please check published course academic year
		$listofAcademicYearToCheckk[] = $publishedCourseAcademicYear;

		if (!empty($listofAcademicYearToCheckk)) {
			foreach ($listofAcademicYearToCheckk as $kk => $kpv) {
				if (!empty($publishedCourseDetail['PublishedCourse'])) {
					if (isset($publishedCourseDetail['YearLevel']['name']) && !empty($publishedCourseDetail['YearLevel']['name'])) {
						$gradeSubmissionDate = $this->find('first', array(
							'conditions' => array(
								'AcademicCalendar.academic_year' => $kpv['academic_year'],
								'AcademicCalendar.semester' => $kpv['semester'],
								'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
								'AcademicCalendar.department_id like ' => '%s:_:"' . $publishedCourseDetail['PublishedCourse']['department_id'] . '"%',
								'AcademicCalendar.year_level_id like' => '%s:_:"' . $publishedCourseDetail['YearLevel']['name'] . '"%',
								'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
							),
							'order' => array(
								'AcademicCalendar.created DESC'
							), 
							'recursive' => -1
						));

					} else {
						if (!isset($publishedCourseDetail['YearLevel']['name'])) {
							$year_level = '1st';
							$department_id = 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];
						}

						$gradeSubmissionDate = $this->find('first', array(
							'conditions' => array(
								'AcademicCalendar.academic_year' => $kpv['academic_year'],
								'AcademicCalendar.semester' => $kpv['semester'],
								'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
								'AcademicCalendar.department_id like ' => '%s:_:"' . $department_id . '"%',
								'AcademicCalendar.year_level_id like' => '%s:_:"' . $year_level . '"%',
								'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
							),
							'order' => array(
								'AcademicCalendar.created DESC'
							), 'recursive' => -1
						));
					}

					if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
						return $gradeSubmissionDate['AcademicCalendar']['course_registration_start_date'];
					}
				}
			}
		}

		return $deadlineConverted;
	}
}