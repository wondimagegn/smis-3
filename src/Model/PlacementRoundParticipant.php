<?php
App::uses('AppModel', 'Model');
class PlacementRoundParticipant extends AppModel
{
	var $name = 'PlacementRoundParticipant';
	var $hasMany = array(
		'PlacementParticipatingStudent' => array(
			'className' => 'PlacementParticipatingStudent',
			'foreignKey' => 'placement_round_participant_id',
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

	public $validate = array(
		'foreign_key' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select participating unit',
				'allowEmpty' => false,

			),
		),
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select academic year',
				'allowEmpty' => false,

			),
		),
		'placement_round' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select placement round',
				'allowEmpty' => false,
			),
		),
		'applied_for' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select placement round',
				'allowEmpty' => false,
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select placement round',
				'allowEmpty' => false,
			),
		),
		'type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),

			),
		),
	);

	public function reformat($data = array())
	{
		$reformatedData = array();

		$group_identifier = strtotime(date('Y-m-d h:i:sa'));

		if (isset($data) && !empty($data)) {
			$firstData = $data['PlacementRoundParticipant'][1];
			foreach ($data['PlacementRoundParticipant'] as $dk => $dv) {
				$reformatedData['PlacementRoundParticipant'][$dk] = $dv;
				$reformatedData['PlacementRoundParticipant'][$dk]['group_identifier'] = (isset($firstData['group_identifier']) && !empty($firstData['group_identifier']) ? $firstData['group_identifier'] : $group_identifier);
				$reformatedData['PlacementRoundParticipant'][$dk]['applied_for'] = $firstData['applied_for'];
				$reformatedData['PlacementRoundParticipant'][$dk]['program_id'] = $firstData['program_id'];
				$reformatedData['PlacementRoundParticipant'][$dk]['program_type_id'] = $firstData['program_type_id'];
				$reformatedData['PlacementRoundParticipant'][$dk]['academic_year'] = $firstData['academic_year'];
				$reformatedData['PlacementRoundParticipant'][$dk]['placement_round'] = $firstData['placement_round'];
				$reformatedData['PlacementRoundParticipant'][$dk]['semester'] = $firstData['semester'];
				$reformatedData['PlacementRoundParticipant'][$dk]['require_all_selected'] = $firstData['require_all_selected'];
			}
		}

		// Array after removing duplicates
		//$xunique=array_unique($reformatedData);

		$reformatedDataDuplicateRemoved['PlacementRoundParticipant'] = array_unique($reformatedData['PlacementRoundParticipant'], SORT_REGULAR);
		
		if (count($reformatedData['PlacementRoundParticipant']) > count($reformatedDataDuplicateRemoved['PlacementRoundParticipant'])) {
			$this->invalidate('foreign_key', 'Please remove the duplicated rows, and try again.');
			return false;
		}

		//debug($reformatedData);

		return $reformatedData;
	}

	public function isDuplicated($data = array(), $edit = 0)
	{
		if (isset($data) && !empty($data)) {
			$firstData = $data['PlacementRoundParticipant'][1];
			if ($edit) {
				$count = $this->find("first", array(
					'conditions' => array(
						'PlacementRoundParticipant.group_identifier <> ' => $firstData['group_identifier'],
						'PlacementRoundParticipant.type' => $firstData['type'],
						'PlacementRoundParticipant.applied_for' => $firstData['applied_for'],
						'PlacementRoundParticipant.program_id' => $firstData['program_id'],
						'PlacementRoundParticipant.program_type_id' => $firstData['program_type_id'],
						'PlacementRoundParticipant.foreign_key' => $firstData['foreign_key'],
						'PlacementRoundParticipant.academic_year' => $firstData['academic_year'],
						'PlacementRoundParticipant.placement_round' => $firstData['placement_round']
					),
					'recursive' => -1
				));

				if (!empty($count) && count($count) > 0) {
					return $count['PlacementRoundParticipant']['group_identifier'];
				}
			} else {
				$count = $this->find("first", array(
					'conditions' => array(
						'PlacementRoundParticipant.type' => $firstData['type'],
						'PlacementRoundParticipant.applied_for' => $firstData['applied_for'],
						'PlacementRoundParticipant.program_id' => $firstData['program_id'],
						'PlacementRoundParticipant.program_type_id' => $firstData['program_type_id'],
						'PlacementRoundParticipant.foreign_key' => $firstData['foreign_key'],
						'PlacementRoundParticipant.academic_year' => $firstData['academic_year'],
						'PlacementRoundParticipant.placement_round' => $firstData['placement_round']
					),
					'recursive' => -1
				));

				if (!empty($count) && count($count) > 0) {
					return $count['PlacementRoundParticipant']['group_identifier'];
				}
			}
		}

		return false;
	}

	public function isPossibleToDefineDeadline($data = array())
	{
		if (isset($data) && !empty($data)) {
			$row = $this->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementDeadline']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['PlacementDeadline']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementDeadline']['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementDeadline']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementDeadline']['placement_round']
				),
				'recursive' => -1
			));

			if (isset($row) && !empty($row)) {
				return $row['PlacementRoundParticipant']['group_identifier'];
			}
		}

		return false;
	}

	public function participating_unit_name($acceptedStudentdetail = array(), $selectedAcademicYear, $apply_for_college = null, $type = 'd')
	{

		debug(Configure::read('program_types_available_for_placement_preference'));

		if (!empty($acceptedStudentdetail)) {
			$placementRound = classRegistry::init('PlacementParticipatingStudent')->getNextRound($selectedAcademicYear, $acceptedStudentdetail['AcceptedStudent']['id']);

			if (empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
				// the student is still in college
				$applied_for = 'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'];
			} else if (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id'])) {
				// the assignment is specialization
				$applied_for = 'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'];
			}

			$rows = $this->find("all", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $applied_for,
					'PlacementRoundParticipant.placement_round' => $placementRound,
					//'PlacementRoundParticipant.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'],
					//'PlacementRoundParticipant.program_type_id' => array($acceptedStudentdetail['AcceptedStudent']['program_type_id'], 1),
					'PlacementRoundParticipant.program_id' => Configure::read('programs_available_for_placement_preference'),
					'PlacementRoundParticipant.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					'PlacementRoundParticipant.academic_year' => $selectedAcademicYear
				),
				'recursive' => -1
			));

		} else if (isset($apply_for_college) && !empty($apply_for_college)) {
			
			if ($type == 'c') {
				$applied_for = 'c~' . $apply_for_college;
			} else if ($type == 'd') {
				$applied_for = 'd~' . $apply_for_college;
			}

			//$apply_for_college=null,$type='d'
			$rows = $this->find("all", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $applied_for,
					'PlacementRoundParticipant.program_id' => Configure::read('programs_available_for_placement_preference'),
					'PlacementRoundParticipant.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
					'PlacementRoundParticipant.academic_year' => $selectedAcademicYear,
					//'PlacementRoundParticipant.placement_round' => $placementRound,
					// $placementRound is not defined here
				),
				'recursive' => -1
			));
		}

		$participatingdepartmentname = array();

		if (!empty($rows)) {
			foreach ($rows as $k => $v) {
				if (!empty($v['PlacementRoundParticipant']['name'])) {
					$participatingdepartmentname[$v['PlacementRoundParticipant']['id']] = $v['PlacementRoundParticipant']['name'];
				}
			}
		}

		return $participatingdepartmentname;
	}

	public function reformatDevRegion($data = array())
	{
		//debug($data);
		if (isset($data['PlacementSetting']) && !empty($data['PlacementSetting'])) {
			if (isset($data['PlacementSetting'][0]) && !empty($data['PlacementSetting'][0]['developing_region'])) {
				$developingRegions = implode(',', $data['PlacementSetting'][0]['developing_region']);
				if (!empty($developingRegions)) {
					foreach ($data['PlacementSetting'] as $k => &$v) {
						$v['developing_region'] = $developingRegions;
						$reformatedData['PlacementSetting'][$k] = $v;
					}
					return 	$reformatedData;
				}
			} else {
				return $data;
			}
		}

		return $data;
	}

	public function get_selected_participating_unit_name($data)
	{
		$rows = $this->find("all", array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['PlacementPreference']['applied_for'],
				'PlacementRoundParticipant.academic_year' => $data['PlacementPreference']['academic_year'],
				'PlacementRoundParticipant.placement_round' => $data['PlacementPreference']['round'],
				'PlacementRoundParticipant.program_id' => $data['PlacementPreference']['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['PlacementPreference']['program_type_id']
			),
			'recursive' => -1
		));

		$participatingdepartmentname = array();
		
		if (!empty($rows)) {
			foreach ($rows as $k => $v) {
				if (!empty($v['PlacementRoundParticipant']['name'])) {
					$participatingdepartmentname[$v['PlacementRoundParticipant']['id']] = $v['PlacementRoundParticipant']['name'];
				}
			}
		}

		return $participatingdepartmentname;
	}

	public function get_participating_unit_name($data)
	{
		$rows = $this->find("all", array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['applied_for'],
				'PlacementRoundParticipant.academic_year' => $data['academic_year'],
				'PlacementRoundParticipant.placement_round' => $data['round'],
				'PlacementRoundParticipant.program_id' => $data['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['program_type_id']
			),
			'limit' => isset($data['limit']) ? $data['limit'] : 100,
			'recursive' => -1
		));

		$participatingdepartmentname = array();

		if (!empty($rows)) {
			foreach ($rows as $k => $v) {
				if (!empty($v['PlacementRoundParticipant']['name'])) {
					$participatingdepartmentname[$v['PlacementRoundParticipant']['id']] = $v['PlacementRoundParticipant']['name'];
				}
			}
		}
		return $participatingdepartmentname;
	}

	public function allParticipatingUnitsDefined($data)
	{
		if (!empty($data['PlacementRoundParticipant']['applied_for'])) {

			$colleges = classRegistry::init('College')->find('list');
			$departments = classRegistry::init('Department')->find('list');

			$rows = $this->find("all", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementRoundParticipant']['applied_for'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementRoundParticipant']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementRoundParticipant']['placement_round'],
					'PlacementRoundParticipant.program_id' => $data['PlacementRoundParticipant']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementRoundParticipant']['program_type_id']
				),
				'recursive' => -1
			));

			//debug($rows);

			$participatingUnitName = array();
			$collegeID = null;
			$departmentID = null;
			$collegeName = null;
			$departmentName = null;

			$appliedUnitClg = explode('c~', $data['PlacementRoundParticipant']['applied_for']);

			if (isset($appliedUnitClg[1])) {
				$collegeID = $appliedUnitClg[1];
			} else {
				$appliedUnitDept = explode('d~', $data['PlacementRoundParticipant']['applied_for']);
				if (isset($appliedUnitDept[1])) {
					$departmentID = $appliedUnitDept[1];
				}
			}
			

			if (isset($collegeID)) {
				$collegeName = $colleges[$collegeID];
			} else if (isset($departmentID)) {
				$departmentName = $departments[$departmentID];
				$deptCollID = classRegistry::init('Department')->field('Department.college_id', array('Department.id '=> $departmentID));
				$collegeName = $colleges[$deptCollID];
			}

			debug($collegeName);
			debug($departmentName);
			
			if (!empty($rows)) {
				foreach ($rows as $k => $v) {
					if (empty($departmentName)) {
						if ($v['PlacementRoundParticipant']['type'] == 'College') {
							$participatingUnitName[$collegeName]['c~' . $v['PlacementRoundParticipant']['foreign_key']] = $colleges[$v['PlacementRoundParticipant']['foreign_key']] . ' Freshman ('. $v['PlacementRoundParticipant']['name']. ')';
						} else if ($v['PlacementRoundParticipant']['type'] == 'Department') {
							$participatingUnitName[$collegeName]['d~' . $v['PlacementRoundParticipant']['foreign_key']] = $departments[$v['PlacementRoundParticipant']['foreign_key']];
						}
					} else {
						if ($v['PlacementRoundParticipant']['type'] == 'College') {
							$participatingUnitName[$departmentName]['c~' . $v['PlacementRoundParticipant']['foreign_key']] = $colleges[$v['PlacementRoundParticipant']['foreign_key']] . ' Freshman ('. $v['PlacementRoundParticipant']['name']. ')';
						} else if ($v['PlacementRoundParticipant']['type'] == 'Department') {
							$participatingUnitName[$departmentName]['d~' . $v['PlacementRoundParticipant']['foreign_key']] = $departments[$v['PlacementRoundParticipant']['foreign_key']];
						}
					}
				}
			}

			return $participatingUnitName;
		} 
	}

	public function getParticipatingUnitForDirectPlacement($data)
	{
		$rows = $this->find("all", array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['applied_for'],
				'PlacementRoundParticipant.academic_year' => $data['academic_year'],
				// 'PlacementRoundParticipant.placement_round' => $data['round'],
				'PlacementRoundParticipant.program_id' => $data['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['program_type_id']
			),
			'limit' => isset($data['limit']) ? $data['limit'] : 100,
			'recursive' => -1
		));

		$participatingdepartmentname = array();

		if (!empty($rows)) {
			foreach ($rows as $k => $v) {
				if (!empty($v['PlacementRoundParticipant']['name'])) {
					$participatingdepartmentname[$v['PlacementRoundParticipant']['id']] = $v['PlacementRoundParticipant']['name'];
				}
			}
		}
		
		return $participatingdepartmentname;
	}

	public function get_participating_unit_for_edit($placement_round_participant_id)
	{
		if (isset($placement_round_participant_id) && !empty($placement_round_participant_id)) {
			$groupId = $this->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.id' => $placement_round_participant_id
				),
				'recursive' => -1
			));

			$rows = $this->find("all", array(
				'conditions' => array(
					'PlacementRoundParticipant.group_identifier' => $groupId['PlacementRoundParticipant']['group_identifier']
				),
				'recursive' => -1
			));

			$participatingdepartmentname = array();

			if (!empty($rows)) {
				foreach ($rows as $k => $v) {
					if (!empty($v['PlacementRoundParticipant']['name'])) {
						$participatingdepartmentname[$v['PlacementRoundParticipant']['id']] = $v['PlacementRoundParticipant']['name'];
					}
				}
			}
			return $participatingdepartmentname;
		}

		return array();
	}

	public function placementSettingDefined($data = array())
	{
		$participatinListCapacity = $this->find("all", array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['applied_for'],
				'PlacementRoundParticipant.academic_year' => $data['academic_year'],
				'PlacementRoundParticipant.placement_round' => $data['round'],
				'PlacementRoundParticipant.program_id' => $data['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['program_type_id'],
			),
			'recursive' => -1
		));

		$participatingSettings = $this->find("count", array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['applied_for'],
				'PlacementRoundParticipant.academic_year' => $data['academic_year'],
				'PlacementRoundParticipant.placement_round' => $data['round'],
				'PlacementRoundParticipant.program_id' => $data['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['program_type_id'],
			),
			'recursive' => -1
		));

		$placementResultSetting = ClassRegistry::init('PlacementResultSetting')->find("count", array(
			'conditions' => array(
				'PlacementResultSetting.applied_for' => $data['applied_for'],
				'PlacementResultSetting.academic_year' => $data['academic_year'],
				'PlacementResultSetting.round' => $data['round'],
				'PlacementResultSetting.program_id' => $data['program_id'],
				'PlacementResultSetting.program_type_id' => $data['program_type_id'],
			),
			'recursive' => -1
		));

		$placementReadyStudent = ClassRegistry::init('PlacementParticipatingStudent')->find("count", array(
			'conditions' => array(
				'PlacementParticipatingStudent.academic_year' => $data['academic_year'],
				'PlacementParticipatingStudent.round' => $data['round'],
				'PlacementParticipatingStudent.program_id' => $data['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $data['program_type_id']
			),
			'recursive' => -1
		));

		$intake_capacity_defined_for_all_participants = true;
		$defined_intake_capacies = array();

		if (!empty($participatinListCapacity)) {
			foreach ($participatinListCapacity as $key => $participants) {
				debug($participants['PlacementRoundParticipant']['intake_capacity']);
				if (!is_null($participants['PlacementRoundParticipant']['intake_capacity']) && is_numeric($participants['PlacementRoundParticipant']['intake_capacity'])) {
					$defined_intake_capacies[$participants['PlacementRoundParticipant']['name']] = $participants['PlacementRoundParticipant']['intake_capacity'];
				}
			}
			
		}

		if (count($defined_intake_capacies) != count($participatinListCapacity)) {
			$intake_capacity_defined_for_all_participants = false;
		}

		if ($participatingSettings == 0) {
			$this->invalidate('placement_round_participant', 'Please record placement round participant before running auto placement.');
			return false;
		} elseif ($placementResultSetting == 0) {
			$this->invalidate('placement_result_setting', 'Please record result settings in auto placement before running the auto placement.');
			return false;
		} elseif ($placementReadyStudent == 0) {
			$this->invalidate('placement_participating_student', 'Please prepare the students for auto placement before running the auto placement.');
			return false;
		} else if (!$intake_capacity_defined_for_all_participants) {
			$this->invalidate('placement_round_participant', 'Please define all intake capacities for all participating units for auto placement before running the auto placement.');
			return false;
		}
		return true;
	}

	public function roundLabel($round)
	{
		$r = '';
		if ($round == 1) {
			$r = $round . 'st';
		} elseif ($round == 2) {
			$r = $round . 'nd';
		} else if ($round == 3) {
			$r = $round . 'rd';
		} else {
			$r = $round . 'th';
		}
		return $r;
	}

	public function appliedFor($acceptedStudentdetail, $selectedAcademicYear)
	{
		$applied_for = '';

		if (!empty($acceptedStudentdetail)) {
			$placementRound = classRegistry::init('PlacementParticipatingStudent')->getNextRound($selectedAcademicYear, $acceptedStudentdetail['AcceptedStudent']['id']);

			if (isset($placementRound) && !empty($placementRound)) {

				$roundAppliedFor = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
					'conditions' => array(
						'PlacementParticipatingStudent.academic_year LIKE ' => $selectedAcademicYear . '%',
						'PlacementParticipatingStudent.round' => $placementRound,
						'PlacementParticipatingStudent.accepted_student_id' => $acceptedStudentdetail['AcceptedStudent']['id']
					), 'order' => array('PlacementParticipatingStudent.round DESC')
				));

				if (isset($roundAppliedFor) && !empty($roundAppliedFor)) {
					$applied_for = $roundAppliedFor['PlacementParticipatingStudent']['applied_for'];
				} else {
					if (empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
						// the student is still in college
						$applied_for = 'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'];
					} else if (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id'])) {
						// the assignment is specialization
						$applied_for = 'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'];
					}
				}
			}
		}

		return $applied_for;
	}

	function canItBeDeleted($round_participant_id = null)
	{
		if ($this->PlacementParticipatingStudent->find('count', array('conditions' => array('PlacementParticipatingStudent.placement_round_participant_id' => $round_participant_id))) > 0) {
			return false;
		} else if (ClassRegistry::init('PlacementParticipatingStudent')->find('count', array('conditions' => array('PlacementParticipatingStudent.placement_round_participant_id' => $round_participant_id))) > 0) {
			return false;
		} else if (ClassRegistry::init('PlacementPreference')->find('count', array('conditions' => array('PlacementPreference.placement_round_participant_id' => $round_participant_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}

	function latest_defined_academic_year_and_round($applied_for = null) {

		$latestAcyRnd = array();

		if (!empty($applied_for)) {
			$latestACYDefined = $this->find('first', array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $applied_for,
				),
				'order' => array('PlacementRoundParticipant.academic_year' => 'DESC', 'PlacementRoundParticipant.placement_round'=> 'DESC'),
				'group' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.placement_round', 'PlacementRoundParticipant.applied_for'),
			));
		} else {
			$latestACYDefined = $this->find('first', array(
				'order' => array('PlacementRoundParticipant.academic_year' => 'DESC', 'PlacementRoundParticipant.placement_round'=> 'DESC'),
				'group' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.placement_round', 'PlacementRoundParticipant.applied_for'),
			));
		}

		if (!empty($latestACYDefined)) {
			$latestAcyRnd['applied_for'] = $latestACYDefined['PlacementRoundParticipant']['applied_for'];
			$latestAcyRnd['academic_year'] = $latestACYDefined['PlacementRoundParticipant']['academic_year'];
			$latestAcyRnd['round'] = $latestACYDefined['PlacementRoundParticipant']['placement_round'];
		} 

		return $latestAcyRnd;

	}

	function get_placement_participant_ids_by_group_identifier($group_identifier = null)
	{
		if (!empty($group_identifier)) {
			$participantIDs = $this->find("list", array('conditions' => array('PlacementRoundParticipant.group_identifier' => $group_identifier), 'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.id')));
			if (!empty($participantIDs)) {
				return $participantIDs;
			}
		}
		return array();
	}
}
