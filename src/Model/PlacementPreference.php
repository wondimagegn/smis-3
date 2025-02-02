<?php
App::uses('AppModel', 'Model');

class PlacementPreference extends AppModel
{
	var $name = 'PlacementPreference';

	public $belongsTo = array(
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'accepted_student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PlacementRoundParticipant' => array(
			'className' => 'PlacementRoundParticipant',
			'foreignKey' => 'placement_round_participant_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function getPreferenceStat($placementRoundParticipant, $type2 = null)
	{
		//Get participating departments
		$stat = array();

		$participatingDepartments = ClassRegistry::init('PlacementRoundParticipant')->find('all', array(
			'conditions' => array(
				'PlacementRoundParticipant.group_identifier' => $placementRoundParticipant['PlacementRoundParticipant']['group_identifier'],
			),
			'recursive' => -1
		));

		/* $placementsResultSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array(
			'conditions' => array(
				'PlacementResultSetting.group_identifier' => $placementRoundParticipant['PlacementRoundParticipant']['group_identifier'],
			),
			'recursive' => -1
		)); */

		if (!empty($participatingDepartments)) {
			foreach ($participatingDepartments as $participatingDepartment) {
				
				$index = count($stat);
				$type = $placementRoundParticipant['PlacementRoundParticipant']['type'];
				
				if ($type == "College") {
					$name = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $participatingDepartment['PlacementRoundParticipant']['foreign_key']), 'recursive' => -1));
				} else if ($type == "Department") {
					$name = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.id' => $participatingDepartment['PlacementRoundParticipant']['foreign_key']), 'recursive' => -1));
				}

				$stat[$index]['foreign_key'] = $participatingDepartment['PlacementRoundParticipant']['foreign_key'];

				if (isset($participatingDepartment['PlacementRoundParticipant']['name']) && !empty($participatingDepartment['PlacementRoundParticipant']['name'])) {
					$stat[$index]['department_name'] = $participatingDepartment['PlacementRoundParticipant']['name'];
				} else {
					$stat[$index]['department_name'] = $name["$type"]['name'];
				}

				for ($i = 1; $i <= count($participatingDepartments); $i++) {
					$options = array(
						'conditions' => array(
							'PlacementPreference.placement_round_participant_id' => $participatingDepartment['PlacementRoundParticipant']['id'],
							//'PlacementPreference.college_id' => $college_id,
							'PlacementPreference.preference_order' => $i
						)
					);

					$forcollege = explode('c~', $placementRoundParticipant['PlacementRoundParticipant']['applied_for']);
					$fordepartment = explode('d~', $placementRoundParticipant['PlacementRoundParticipant']['applied_for']);

					if (isset($forcollege[1]) && !empty($forcollege[1])) {
						$options['conditions'][0] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE department_id is null and academicyear = \'' . $placementRoundParticipant['PlacementRoundParticipant']['academic_year'] . '\' AND college_id = \'' . $forcollege[1] . '\'';
					} else if (isset($fordepartment[1]) && !empty($fordepartment[1])) {
						$options['conditions'][0] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = \'' . $placementRoundParticipant['PlacementRoundParticipant']['academic_year'] . '\' AND department_id = \'' . $fordepartment[1] . '\'';
					}

					if (strcasecmp($type2, 'female') == 0) {
						$options['conditions'][0] .= ' AND (sex LIKE \'%female%\' OR sex LIKE \'%f%\'))';
					} else if (strcasecmp($type2, 'disable') == 0) {
						$options['conditions'][0] .= ' AND disability IS NOT NULL AND disability <> \'\')';
					} else if (!empty($participatingDepartment['PlacementRoundParticipant']['developing_region']) && strcasecmp($type2, 'region') == 0) {
						$options['conditions'][0] .= ' AND region_id IN (\'' . $participatingDepartment['PlacementRoundParticipant']['developing_region'] . '\'))';
					} else {
						$options['conditions'][0] .= ')';
					}

					$stat[$index]['count'][$i]['~total~'] = $this->find('count', $options);

					/* foreach ($placementsResultSettings as $placementsResultsCriteria) {
						//after finishing settings result
						$options = array(
							'conditions' => array(
								'Preference.department_id' => $participatingDepartment['ParticipatingDepartment']['department_id'],
								'Preference.college_id' => $college_id,
								'Preference.academicyear' => $academic_year,
								'Preference.preferences_order' => $i
							)
						);

						if ($isPrepartory) {
							$options['conditions'][0] = 'Preference.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = \'' . $academic_year . '\' AND college_id = \'' . $college_id . '\' AND EHEECE_total_results >= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_from'] . ' AND EHEECE_total_results <= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_to'];
						} else {
							$options['conditions'][0] = 'Preference.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = \'' . $academic_year . '\' AND college_id = \'' . $college_id . '\' AND freshman_result >= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_from'] . ' AND freshman_result <= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_to'];
						}

						if (strcasecmp($type, 'female') == 0) {
							$options['conditions'][0] .= ' AND (sex = \'female\' OR sex = \'f\'))';
						} else if (strcasecmp($type, 'disable') == 0) {
							$options['conditions'][0] .= ' AND disability IS NOT NULL AND disability <> \'\')';
						} else if (!empty($participatingDepartment['ParticipatingDepartment']['developing_regions_id']) && strcasecmp($type, 'region') == 0) {
							$options['conditions'][0] .= ' AND region_id IN (\'' . $participatingDepartment['ParticipatingDepartment']['developing_regions_id'] . '\'))';
						} else {
							$options['conditions'][0] .= ')';
						}

						$stat[$index]['count'][$i][$placementsResultsCriteria['PlacementsResultsCriteria']['name']] = $this->find('count', $options);
						
					} */
				}
			}
		}
		return $stat;
	}


	function getPreparedPreferenceStat($placementRoundParticipant, $type = null)
	{
		//Get participating departments
		$stat = array();

		$participatingDepartments = ClassRegistry::init('PlacementRoundParticipant')->find('all', array(
			'conditions' => array(
				'PlacementRoundParticipant.group_identifier' => $placementRoundParticipant['PlacementRoundParticipant']['group_identifier'],
			),
			'recursive' => -1
		));

		$accepted_student_ids = ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
			'conditions' => array(
				'PlacementParticipatingStudent.round' => $placementRoundParticipant['PlacementRoundParticipant']['placement_round'],
				'PlacementParticipatingStudent.academic_year' => $placementRoundParticipant['PlacementRoundParticipant']['academic_year'],
				'PlacementParticipatingStudent.applied_for' => $placementRoundParticipant['PlacementRoundParticipant']['applied_for'] 
			),
			'fields' => array('PlacementParticipatingStudent.accepted_student_id','PlacementParticipatingStudent.accepted_student_id')
		));

		if (!empty($participatingDepartments) && !empty($accepted_student_ids)) {
			foreach ($participatingDepartments as $participatingDepartment) {
				$index = count($stat);
				//$type = $placementRoundParticipant['PlacementRoundParticipant']['type'];

				/* if ($type == "College") {
					$name = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $participatingDepartment['PlacementRoundParticipant']['foreign_key']), 'recursive' => -1));
				} else if ($type == "Department") {
					$name = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.id' => $participatingDepartment['PlacementRoundParticipant']['foreign_key']), 'recursive' => -1));
				} */

				$stat[$index]['foreign_key'] = $participatingDepartment['PlacementRoundParticipant']['foreign_key'];
				
				if (isset($participatingDepartment['PlacementRoundParticipant']['name']) && !empty($participatingDepartment['PlacementRoundParticipant']['name'])) {
					$stat[$index]['department_name'] = $participatingDepartment['PlacementRoundParticipant']['name'];
				} else {
					$stat[$index]['department_name'] = $participatingDepartment['PlacementRoundParticipant']['name'];
				}

				for ($i = 1; $i <= count($participatingDepartments); $i++) {
					$options = array(
						'conditions' => array(
							'PlacementPreference.placement_round_participant_id' => $participatingDepartment['PlacementRoundParticipant']['id'],
							'PlacementPreference.preference_order' => $i
						)
					);

					//$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT accepted_student_id FROM placement_participating_students WHERE round=\'' . $placementRoundParticipant['PlacementRoundParticipant']['placement_round'] . '\' AND  academic_year = \'' . $placementRoundParticipant['PlacementRoundParticipant']['academic_year'] . '\' AND applied_for = \'' . $placementRoundParticipant['PlacementRoundParticipant']['applied_for'] . '\')';

					//$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (' . join(',', $accepted_student_ids) . ')';

					if (strcasecmp($type, 'female') == 0) {
						//$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE (sex = \'female\' OR sex = \'f\') )';
						$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE id IN (' . join(',', $accepted_student_ids) . ') and (sex LIKE \'female%\' OR sex LIKE \'f%\') )';
					} else if (strcasecmp($type, 'disable') == 0) {
						//$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE disability IS NOT NULL AND disability <> \'\' )';
						$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE id IN (' . join(',', $accepted_student_ids) . ') and disability IS NOT NULL AND disability <> \'\' )';
					} else if (!empty($participatingDepartment['PlacementRoundParticipant']['developing_region']) && strcasecmp($type, 'region') == 0) {
						//$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE region_id IN (\'' . $participatingDepartment['PlacementRoundParticipant']['developing_region'] . '\'))';
						$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE id IN (' . join(',', $accepted_student_ids) . ') and region_id IN (\'' . $participatingDepartment['PlacementRoundParticipant']['developing_region'] . '\'))';
					} else {
						$options['conditions'][] = 'PlacementPreference.accepted_student_id IN (' . join(',', $accepted_student_ids) . ')';
					}

					$stat[$index]['count'][$i]['~total~'] = $this->find('count', $options);
				}
			}
		}
		return $stat;
	}

	// This function will validate the student has entered his preference once.
	function isAlreadyEnteredPreference($data = null)
	{
		$countUser = $this->find('count', array('conditions' => array(
			'PlacementPreference.accepted_student_id' => $data['accepted_student_id'],
			'PlacementPreference.academic_year' => $data['academic_year'],
			'PlacementPreference.round' => $data['round']
		)));

		$isEditing = false;
		if ($data['id']) {
			$isEditing = true;
		}
		if ($countUser && !$isEditing) {
			$this->invalidate('alreadypreferencerecorded', 'Validation Error: You have already recorded preference for selected student.');
			return true;
		} else {
			return false;
		}
	}

	//This function will validate student has selected orderely their department choice
	function isAllPreferenceSelectedDifferent($data = null, $require_all_selected_switch = 0)
	{
		$array = array();

		if ($require_all_selected_switch) {
			if (!empty($data)) {
				foreach ($data as $value) {
					if (!empty($value['placement_round_participant_id'])) {
						$array[] = $value['placement_round_participant_id'];
					} else {
						$this->invalidate('department', 'Validation Error: Please select program preference for each preference order.');
						return false;
					}
				}
			}
		} else {
			// to allow empty preference for pre engineering
			// uncomment the above if block and comment the below one for normal operation

			// and excute the following on mysql to allow null values of placement_round_participant_id

			if (!empty($data)) {
				foreach ($data as $value) {
					if ($value['placement_round_participant_id']) {
						$array[] = $value['placement_round_participant_id'];
					}
				}
			}
		}


		if (!empty($array)) {

			$arrayvaluecount = array();
			$arrayvaluecount = array_count_values($array);

			//return $arrayvaluecount;
			foreach ($arrayvaluecount as $k => $v) {
				if ($v > 1) {
					$this->invalidate('preference', 'Validation Error: Please select different program preference for each preference order.');
					return false;
				}
			}
		} else {
			$this->invalidate('preference', 'Validation Error: Empty Preference. You did not selected any preference. Please select program preference for each preference order.');
			return false;
		}

		return true;
	}

	public function getStudentWhoFilledPreference($data = array(), $searchAble = "")
	{
		debug($data);

		if (isset($data) && !empty($data)) {
			$firstData = ClassRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
				),
				'recursive' => -1
			));

			$allRoundParticipants = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
				'conditions' => array('PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']),
				'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.id')
			));

			if (isset($searchAble) && !empty($searchAble)) {
				$allStudentsWhoFilledPreference = $this->find('all', array(
					'conditions' => array(
						'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementPreference.round' => $data['PlacementSetting']['round'],
						'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,
						'Student.first_name like' => $searchAble . '%'
					),
					'group' => array('student_id'),
					'contain' => array('Student'),
					'limit' => 10
				));
			} else {
				$allStudentsWhoFilledPreference = $this->find('all', array(
					'conditions' => array(
						'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementPreference.round' => $data['PlacementSetting']['round'],
						'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,

					),
					'group' => array('student_id'),
					'contain' => array('Student'),
					'limit' => 10
				));
			}

			return $allStudentsWhoFilledPreference;
		}
		return array();
	}

	public function getPlacementCriteriaSummary($data = array())
	{
		$placementSummary = array();

		if (isset($data) && !empty($data)) {
			$firstData = ClassRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
				),
				'recursive' => -1
			));

			if (!empty($firstData)) {

				$allRoundParticipants = ClassRegistry::init('PlacementRoundParticipant')->find('all', array(
					'conditions' => array('PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']),
					'recursive' => -1
				));

				$resultSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array(
					'conditions' => array('PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']),
					'recursive' => -1
				));

				$targetUnit = explode('~', $data['PlacementSetting']['applied_for']);

				if ($targetUnit[0] == "c") {
					$name = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $targetUnit[1]), 'recursive' => -1));
					$targetUnitName = $name['College']['name'];
				} else if ($targetUnit[0] == "d") {
					$name = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.id' => $targetUnit[1]), 'recursive' => -1));
					$targetUnitName = $name['Department']['name'];
				} else if ($targetUnit[0] == "s") {
					$name = ClassRegistry::init('Specialization')->find('first', array('conditions' => array('Specialization.id' => $targetUnit[1]), 'recursive' => -1));
					$targetUnitName = $name['Specialization']['name'];
				}

				$placementSummary['targetStudentInUnit'] = $targetUnitName;
				$placementSummary['round'] = $data['PlacementSetting']['round'];

				$placementSummary['placementAlreadyRun'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id is not null',
					), 
					'recursive' => -1
				));

				debug($placementSummary);

				$placementSummary['academic_year'] = $data['PlacementSetting']['academic_year'];
				$placementSummary['totalStudentReadyForPlacement'] = ClassRegistry::init('PlacementParticipatingStudent')->find("count", array(
					'conditions' => array(
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
					), 
					'group' => array(
						'PlacementParticipatingStudent.academic_year', 
						'PlacementParticipatingStudent.round', 
						'PlacementParticipatingStudent.student_id',
						'PlacementParticipatingStudent.accepted_student_id',
					),
					'recursive' => -1
				));


				$placementSummary['PlacementRoundParticipant'] = $allRoundParticipants;

				$allRoundParticipantsList = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
					'conditions' => array('PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']),
					'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.id')
				));

				$developingRegions = $firstData['PlacementRoundParticipant']['developing_region'];

				$preferenceOrders = $this->find('list', array(
					'conditions' => array('PlacementPreference.placement_round_participant_id' => $allRoundParticipantsList),
					'fields' => 'preference_order',
					'group' => 'preference_order',
					'order' => 'preference_order',
				));

				$preference = array();
				$count = 0;
				debug($preferenceOrders);

				if (!empty($preferenceOrders) && !empty($allRoundParticipants)) {
					foreach ($preferenceOrders as $pk => $pv) {
						foreach ($allRoundParticipants as $pkk => $pvv) {
							$preference[$count]['unit'] = $pvv['PlacementRoundParticipant']['name'];
							$preference[$count]['preference_order'] = $pv;

							$preference[$count]['male'] = 0;
							$preference[$count]['female'] = 0;
							$preference[$count]['disability'] = 0;
							$preference[$count]['developing_region'] = 0;

							$preference[$count]['total'] = $this->find('count', array(
								'conditions' => array(
									'PlacementPreference.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
									'PlacementPreference.round' => $pvv['PlacementRoundParticipant']['placement_round'],
									'PlacementPreference.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
									'PlacementPreference.preference_order' => $pv
								)
							));

							$preference[$count]['female'] += $this->find('count', array(
								'conditions' => array(
									'PlacementPreference.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
									'PlacementPreference.round' => $pvv['PlacementRoundParticipant']['placement_round'],
									'PlacementPreference.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
									'PlacementPreference.preference_order' => $pv,
									'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE (sex = \'female\' OR sex = \'f\') )'
								)
							));

							$preference[$count]['male'] += $this->find('count', array(
								'conditions' => array(
									'PlacementPreference.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
									'PlacementPreference.round' => $pvv['PlacementRoundParticipant']['placement_round'],
									'PlacementPreference.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
									'PlacementPreference.preference_order' => $pv,
									'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE (sex = \'male\' OR sex = \'m\') )'
								)
							));

							$preference[$count]['disability'] += $this->find('count', array(
								'conditions' => array(
									'PlacementPreference.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
									'PlacementPreference.round' => $pvv['PlacementRoundParticipant']['placement_round'],
									'PlacementPreference.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
									'PlacementPreference.preference_order' => $pv,
									'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE disability is not null )'
								)
							));

							if (isset($developingRegions) && !empty($developingRegions)) {
								$preference[$count]['developing_region'] += $this->find('count', array(
									'conditions' => array(
										'PlacementPreference.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
										'PlacementPreference.round' => $pvv['PlacementRoundParticipant']['placement_round'],
										'PlacementPreference.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
										'PlacementPreference.preference_order' => $pv,
										'PlacementPreference.accepted_student_id IN (SELECT id FROM accepted_students WHERE region_id in (' . $developingRegions . '))'
									)
								));
							} else {
								$preference[$count]['developing_region'] = 0;
							}

							$count++;
						}
					}
				}

				$placementSummary['ResultWeight'] = $resultSettings;
				$placementSummary['Preference'] = $preference;
			}
		}

		return $placementSummary;
	}

	public function getStudentWhoTookEntranceExam($data = array())
	{
		if (isset($data) && !empty($data)) {
			$firstData = ClassRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
				),
				'recursive' => -1
			));

			//debug($firstData);

			$additionalPoints = ClassRegistry::init('PlacementAdditionalPoint')->find("all", array(
				'conditions' => array(
					'PlacementAdditionalPoint.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementAdditionalPoint.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementAdditionalPoint.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementAdditionalPoint.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementAdditionalPoint.round' => $data['PlacementSetting']['round']
				),
				'recursive' => -1
			));

			$points = array();

			if (isset($additionalPoints) && !empty($additionalPoints)) {
				foreach ($additionalPoints as $pk => $pv) {
					$points[$pv['PlacementAdditionalPoint']['type']] = $pv['PlacementAdditionalPoint']['point'];
				}
			}

			//debug($additionalPoints);

			$allRoundParticipants = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
				'conditions' => array('PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']),
				'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.id')
			));

			if (isset($data['PlacementSetting']['limit']) && !empty($data['PlacementSetting']['limit'])) {
				$limit = $data['PlacementSetting']['limit'];
			} else {
				$limit = 5000;
			}

			if ($data['PlacementSetting']['include'] == 0) {
				$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementEntranceExamResultEntry')->find('all', array(
					'conditions' => array(
						'PlacementEntranceExamResultEntry.placement_round_participant_id' => $allRoundParticipants,
						'PlacementEntranceExamResultEntry.accepted_student_id not in (select accepted_student_id from placement_participating_students where applied_for="' . $data['PlacementSetting']['applied_for'] . '" and academic_year="' . $data['PlacementSetting']['academic_year'] . '" and round="' . $data['PlacementSetting']['round'] . '" )'
					),
					'contain' => array('Student', 'AcceptedStudent'),
					'group' => array('PlacementEntranceExamResultEntry.student_id'),
					'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC'),
					'limit' => $limit,
					'maxLimit' => 5000
				));
			} else {
				$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementEntranceExamResultEntry')->find('all', array(
					'conditions' => array(
						'PlacementEntranceExamResultEntry.placement_round_participant_id' => $allRoundParticipants
					),
					'contain' => array('Student', 'AcceptedStudent'),
					'group' => array('PlacementEntranceExamResultEntry.student_id'),
					'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC'),
					'limit' => $limit,
					'maxLimit' => 5000
				));
			}

			if (empty($allStudentsWhoEntranceExam)) {
				//debug($allRoundParticipants);
				$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementPreference')->find('all', array(
					'conditions' => array(
						'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,
					),
					'contain' => array('PlacementRoundParticipant', 'Student', 'AcceptedStudent'),
					'group' => array('PlacementPreference.student_id'),
					'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC'),
					'limit' => $limit,
					'maxLimit' => $limit
				));
			}
			//debug($allStudentsWhoEntranceExam);

			if (!empty($allStudentsWhoEntranceExam)) {

				$selected_program_name = classRegistry::init('Program')->field('Program.name', array('Program.id' => $allStudentsWhoEntranceExam[0]['Student']['program_id']));
				$selected_program_type_name = classRegistry::init('ProgramType')->field('ProgramType.name', array('ProgramType.id' => $allStudentsWhoEntranceExam[0]['Student']['program_type_id']));

				if (!isset($allStudentsWhoEntranceExam[0]['Student']['department_id'])) {
					$selected_applied_unit_name = classRegistry::init('College')->field('College.name', array('College.id' => $allStudentsWhoEntranceExam[0]['Student']['college_id']));
				} else {
					$selected_applied_unit_name = classRegistry::init('Department')->field('Department.name', array('Department.id' => $allStudentsWhoEntranceExam[0]['Student']['department_id']));
				}

				foreach ($allStudentsWhoEntranceExam as $p => &$v) {
					$alreadyPrepared = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
						'conditions' => array(
							'PlacementParticipatingStudent.accepted_student_id' => $v['AcceptedStudent']['id'],
							'PlacementParticipatingStudent.student_id' => $v['Student']['id'],
							'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
							'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
							'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
							'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						),
						'recursive' => -1
					));

					if ($data['PlacementSetting']['include'] == 0 && isset($alreadyPrepared) && !empty($alreadyPrepared)) {
						unset($allStudentsWhoEntranceExam[$p]);
						continue;
					}

					$prep = 0;
					$fresh = 0;
					$entrance = 0;
					$female_placement_weight = 0;
					$disability_weight = 0;
					$developing_region_weight = 0;
					$freshmanResult = 0.0;
					$disability_weight = 0;

					if ($data['PlacementSetting']['round'] == 1) {
						$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
							'conditions' => array(
								'StudentExamStatus.student_id' => $v['Student']['id'],
								'StudentExamStatus.academic_year' => $data['PlacementSetting']['academic_year'],
								'StudentExamStatus.semester' => 'I',
								//'StudentExamStatus.academic_status_id <> 4',
							),
							'contain' => array(
								'AcademicStatus' => array(
									'fields' => array('AcademicStatus.id', 'AcademicStatus.name')
								)
							),
							'fields' => array(
								'StudentExamStatus.academic_status_id',
								'StudentExamStatus.sgpa',
								'StudentExamStatus.cgpa'
							),
							'group' => array(
								'StudentExamStatus.student_id',
								'StudentExamStatus.semester',
								'StudentExamStatus.academic_year',
							),
							'order' => array('StudentExamStatus.created' => 'DESC'),
							'recursive' => -1
						));
						//debug($freshManresult);
					} else {
						$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
							'conditions' => array(
								'StudentExamStatus.student_id' => $v['Student']['id'],
								'StudentExamStatus.academic_year' => $data['PlacementSetting']['academic_year'],
								'StudentExamStatus.semester' => 'II',
								//'StudentExamStatus.academic_status_id <> 4',
							),
							'contain' => array(
								'AcademicStatus' => array(
									'fields' => array('AcademicStatus.id', 'AcademicStatus.name')
								)
							),
							'fields' => array(
								'StudentExamStatus.academic_status_id',
								'StudentExamStatus.sgpa',
								'StudentExamStatus.cgpa'
							),
							'group' => array(
								'StudentExamStatus.student_id',
								'StudentExamStatus.semester',
								'StudentExamStatus.academic_year',
							),
							'order' => array('StudentExamStatus.created' => 'DESC'),
							'recursive' => -1
						));
						//debug($freshManresult);
					}


					// Add a condition to check status and academic status here, Neway
					if (isset($freshManresult['AcademicStatus']['name'])) {
						$v['Student']['academic_status'] = $freshManresult['AcademicStatus']['name'];
					} else {
						$v['Student']['academic_status'] = null;
					}

					// Add a condition to check status here, Neway
					if (isset($freshManresult['StudentExamStatus']['cgpa'])) {
						$v['Student']['cgpa'] = $freshManresult['StudentExamStatus']['cgpa'];
					} else {
						$v['Student']['cgpa'] = null;
					}

					if (isset($freshManresult['StudentExamStatus']['academic_status_id']) && !empty($freshManresult['StudentExamStatus']['academic_status_id']) && $freshManresult['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
						unset($allStudentsWhoEntranceExam[$p]);
						continue;
					} else if (empty($freshManresult['StudentExamStatus']['academic_status_id'])) {
						unset($allStudentsWhoEntranceExam[$p]);
						continue;
					} else if (isset($freshManresult['StudentExamStatus']['academic_status_id']) && !empty($freshManresult['StudentExamStatus']['academic_status_id'])) {
						//unset($allStudentsWhoEntranceExam[$p]);
						//continue;
					}

					$placementResultSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array('conditions' => array('PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']), 'recursive' => -1));
					$allPlacementResultSetting = array();
					$allMaxPlacementResultSetting = array();

					if (!empty($placementResultSettings)) {
						foreach ($placementResultSettings as $pk => $pv) {
							$allPlacementResultSetting[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['percent'];
							$allMaxPlacementResultSetting[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['max_result'];
						}
					} else {
						/* $this->invalidate('NO_PLACEMENT_SETTING_FOUND', 'No Placement Setting is found for the selected search criteria. Please define it first.');
						return false; */
						$error1 = 'No placement setting is defined for round ' . $data['PlacementSetting']['round'] . ' of '  . $selected_program_name .  ' - ' . $selected_program_type_name .  ' in ' . $selected_applied_unit_name .' This is for view only and it is generated with default placement settings(' . DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT . '% for Freshman CGPA out of 4.00, ' . DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT. '% for Preparatory EHEECE total results out of 700 and ' . DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT . '% for Department Entrance Exam out of 30) . Please define Placement Setting first and try to Prepare.';
						$this->invalidate('NO_PLACEMENT_SETTING_FOUND', $error1);
						//return false;
					}

					//debug($allPlacementResultSetting);

					if (isset($freshManresult['StudentExamStatus']['cgpa']) && !empty($freshManresult['StudentExamStatus']['cgpa'])) {
						$freshmanResult = $freshManresult['StudentExamStatus']['cgpa'];
					}

					$other_settings = ClassRegistry::init('PlacementRoundParticipant')->find('first', array(
						'conditions' => array(
							'PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']
						),
						'recursive' => -1
					));

					//debug($other_settings);

					if (isset($other_settings['PlacementRoundParticipant']['developing_region']) && !empty($other_settings['PlacementRoundParticipant']['developing_region'])) {
						$region_ids = explode(',', $other_settings['PlacementRoundParticipant']['developing_region']);
					} else {
						$region_ids = array();
					}

					//debug($region_ids);

					$freshamnResltSetting = '';
					$entranceResltSetting = '';
					$preparatoryResltSetting = '';

					if (!empty($allPlacementResultSetting)) {
						foreach ($allPlacementResultSetting as $setkey => $setvalue) {
							if($setkey == 'freshman_result') {
								$freshamnResltSetting = $setvalue;
							} else if($setkey == 'EHEECE_total_results') {
								$preparatoryResltSetting = $setvalue;
							} else if($setkey == 'entrance_result') {
								$entranceResltSetting = $setvalue;
							} 
						}
					}

					// debug($freshamnResltSetting);
					// debug($entranceResltSetting);
					// debug($preparatoryResltSetting);

					$entrance_settings = ClassRegistry::init('PlacementResultSetting')->find('first', array(
						'conditions' => array(
							'PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
							'PlacementResultSetting.result_type' => 'entrance_result'
						)
					));

					$quota_settings = ClassRegistry::init('PlacementRoundParticipant')->find('first', array(
						'conditions' => array('PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'])
					));

					if (isset($entrance_settings['PlacementResultSetting']) && !empty($entrance_settings['PlacementResultSetting'])) {
						if (isset($allMaxPlacementResultSetting[$entrance_settings['PlacementResultSetting']['result_type']]) && !empty($allMaxPlacementResultSetting[$entrance_settings['PlacementResultSetting']['result_type']])) {
							$entrance = ($entrance_settings['PlacementResultSetting']['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / $allMaxPlacementResultSetting[$entrance_settings['PlacementResultSetting']['result_type']];
						} else {
							$entrance = ($entrance_settings['PlacementResultSetting']['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
						}
					}

					$prepartory_settings = ClassRegistry::init('PlacementResultSetting')->find('first', array(
						'conditions' => array(
							'PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
							'PlacementResultSetting.result_type' => 'EHEECE_total_results'
						)
					));

					if (isset($prepartory_settings) && !empty($prepartory_settings)) {
						// check here ignores readmitted students we can use academicyear from placement preferences table for applied student
						if ($data['PlacementSetting']['academic_year'] == $v['AcceptedStudent']['academicyear']) {
							if (isset($allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']]) && !empty($allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']])) {
								debug($allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']]);
								$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / $allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']];
							} else {
								$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
							}
						} else {
							$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / 700;
						}
					}

					$freshman_settings = ClassRegistry::init('PlacementResultSetting')->find('first', array(
						'conditions' => array(
							'PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
							'PlacementResultSetting.result_type' => 'freshman_result'
						)
					));

					if (isset($freshman_settings) && !empty($freshman_settings)) {
						$fresh = ($freshman_settings['PlacementResultSetting']['percent']  * $freshmanResult) / FRESHMANMAXIMUM;
						// check here
						if (isset($allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']]) && !empty($allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']])) {
							debug($allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']]);
							$fresh = ($freshman_settings['PlacementResultSetting']['percent']  * $freshmanResult) / $allMaxPlacementResultSetting[$prepartory_settings['PlacementResultSetting']['result_type']];
						} else {
							$fresh = ($freshman_settings['PlacementResultSetting']['percent']  * $freshmanResult) / FRESHMANMAXIMUM;
						}
					} else {
						
						if (isset($v['Student']['cgpa']) && $v['Student']['cgpa'] > 0 ) {
							$fresh = (DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT  * $v['Student']['cgpa']) / FRESHMANMAXIMUM;
						}

						if (isset($v['AcceptedStudent']['EHEECE_total_results']) && $v['AcceptedStudent']['EHEECE_total_results'] > 100 ) {
							$prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT  * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
						}

						if (isset($v['PlacementEntranceExamResultEntry']['result']) && $v['PlacementEntranceExamResultEntry']['result'] >= 0) {
							$entrance = (DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT  * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
						}
					}

					if (isset($v['AcceptedStudent']['sex']) && !empty($v['AcceptedStudent']['sex']) && (strcasecmp($v['AcceptedStudent']['sex'], "female") == 0 || strcasecmp($v['AcceptedStudent']['sex'], "f") == 0)) {
						if (isset($points['female']) && !empty($points['female'])) {
							$female_placement_weight = $points['female'];
						} else if (is_numeric(INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT) && INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT == 1) {
							$female_placement_weight = DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT;
						} else {
							$female_placement_weight = 0;
						}
					}

					$v['PlacementEntranceExamResultEntry']['female_placement_weight'] = $female_placement_weight;

					if (isset($v['AcceptedStudent']['disability']) && !empty($v['AcceptedStudent']['disability'])) {
						$disability_weight += 5;
					}

					$v['PlacementEntranceExamResultEntry']['disability_weight'] = $disability_weight;

					if (isset($v['AcceptedStudent']['region_id']) && !empty($v['AcceptedStudent']['region_id']) && in_array($v['AcceptedStudent']['region_id'], $region_ids)) {
						//$developing_region_weight = 0;
						if (isset($v['AcceptedStudent']['sex']) && !empty($v['AcceptedStudent']['sex']) && (strcasecmp($v['AcceptedStudent']['sex'], "female") == 0 || strcasecmp($v['AcceptedStudent']['sex'], "f") == 0)) {
							$developing_region_weight = 5;
						} else if (isset($v['AcceptedStudent']['disability']) && !empty($v['AcceptedStudent']['disability'])) {
							$developing_region_weight = 10;
						}
					}

					$v['PlacementEntranceExamResultEntry']['developing_region_weight'] = $developing_region_weight;
					$v['PlacementEntranceExamResultEntry']['result_weight'] = round(($prep + $fresh + $entrance), 2);

					$v['PlacementEntranceExamResultEntry']['prepartory'] = round($prep, 2);
					$v['PlacementEntranceExamResultEntry']['entrance'] = $entrance;
					$v['PlacementEntranceExamResultEntry']['gpa'] = round($fresh, 2);

					$v['PlacementEntranceExamResultEntry']['academic_year'] = $data['PlacementSetting']['academic_year'];
					$v['PlacementEntranceExamResultEntry']['applied_for'] = $data['PlacementSetting']['applied_for'];

					$v['PlacementEntranceExamResultEntry']['round'] =  $data['PlacementSetting']['round'];
					$v['PlacementEntranceExamResultEntry']['program_id'] =  $data['PlacementSetting']['program_id'];
					$v['PlacementEntranceExamResultEntry']['program_type_id'] =  $data['PlacementSetting']['program_type_id'];

					$v['PlacementEntranceExamResultEntry']['total_weight'] = round(($v['PlacementEntranceExamResultEntry']['developing_region_weight'] + $v['PlacementEntranceExamResultEntry']['disability_weight'] + $v['PlacementEntranceExamResultEntry']['female_placement_weight'] + $v['PlacementEntranceExamResultEntry']['result_weight']), 2);
					$v['PlacementEntranceExamResultEntry']['total_placement_weight'] = 	round(($v['PlacementEntranceExamResultEntry']['developing_region_weight'] + $v['PlacementEntranceExamResultEntry']['disability_weight'] + $v['PlacementEntranceExamResultEntry']['female_placement_weight'] + $v['PlacementEntranceExamResultEntry']['result_weight']), 2);


					if (isset($alreadyPrepared) && !empty($alreadyPrepared)) {
						$v['PlacementParticipatingStudent'] = $alreadyPrepared['PlacementParticipatingStudent'];
					} else {
						$v['PlacementParticipatingStudent'] = array();
					}
				}

				usort($allStudentsWhoEntranceExam, array($this, "cmp"));
				return $allStudentsWhoEntranceExam;
			}
		}

		return array();
	}

	public function auto_placement_algorithm($data = array())
	{
		if (isset($data) && !empty($data)) {
			$units = $this->getListOfUnitNeedByPrivilageStudentMost($data);


			if (!empty($units)) {
				foreach ($units as $department_id => $weight) {
					//simply read quota for each deparment
					$adjusted_privilaged_quota = array();

					$detail_of_participating_unit = ClassRegistry::init('PlacementRoundParticipant')->find('first', array(
						'conditions' => array(
							'PlacementRoundParticipant.id' => $department_id,
							'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
							'PlacementRoundParticipant.academic_year' =>  $data['PlacementSetting']['academic_year'],
							'PlacementRoundParticipant.placement_round' =>  $data['PlacementSetting']['round'],
							'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
							'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
						)
					));

					$intake_capacity = $detail_of_participating_unit['PlacementRoundParticipant']['intake_capacity'];
					$adjusted_privilaged_quota['female'] = $detail_of_participating_unit['PlacementRoundParticipant']['female_quota'];

					$adjusted_privilaged_quota['regions'] =  $detail_of_participating_unit['PlacementRoundParticipant']['region_quota'];
					$adjusted_privilaged_quota['disability'] = $detail_of_participating_unit['PlacementRoundParticipant']['disability_quota'];

					//// adjusted privilaged  quota
					$preReadyNormalPrivilagedDepartmentAllocation = $this->checkAndAdjustPrivilagedQuota(
						$data,
						$department_id,
						$adjusted_privilaged_quota,
						$detail_of_participating_unit['PlacementRoundParticipant']['intake_capacity']
					);

					$placedStudents = array();

					$sortedStudentByPreferenceAndGrade = $this->sortOutStudentByPreference(
						$data,
						$department_id
					);


					if (!empty($sortedStudentByPreferenceAndGrade)) {
						$n = ($intake_capacity <= count($sortedStudentByPreferenceAndGrade) ? $intake_capacity : count($sortedStudentByPreferenceAndGrade));

						// send the other to quote
						$notQualifedInCompetitive=array();
						//$n = $intake_capacity;
						$lastIndex = $n;
						for ($i = 0; $i < $n; $i++) {
							$placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['PlacementPreference']['accepted_student_id'];
						}

						// cut quote qualified
						/* for ($j = $n; $j <= count($sortedStudentByPreferenceAndGrade); $j++) {
							$notQualifedInCompetitive[] = $sortedStudentByPreferenceAndGrade[$j]['PlacementPreference']['accepted_student_id'];
						} */

						unset($sortedStudentByPreferenceAndGrade);
					}
					//debug($placedStudents);

					////iterate for three famious privilage

					$quotaBalanceForCompetitive = 0;

					foreach ($preReadyNormalPrivilagedDepartmentAllocation[0] as $privilage_type => &$quota) {
						if ($quota > 0) {

							$privilaged_selected = $this->privilagedStudentsFilterOut(
								$data,
								$department_id,
								$preReadyNormalPrivilagedDepartmentAllocation[0],

								$placedStudents,
								$privilage_type

							);

							/*$privilaged_selected = $this->privilagedStudentsFilterOut(
								$data,
								$department_id,
								$preReadyNormalPrivilagedDepartmentAllocation[0],

								$placedStudents,
								$privilage_type,
								$notQualifedInCompetitive

							);*/


							//debug($quota);
							//debug($privilaged_selected[$privilage_type]);
							if (!empty($privilaged_selected) && $quota <= count($privilaged_selected[$privilage_type])) {
								$n = $quota;
								for ($i = 0; $i < $n; $i++) {
									$placedStudents['Q'][] = $privilaged_selected[$privilage_type][$i];
								}
							} else {
								$quotaBalanceForCompetitive = $quota - count($privilaged_selected[$privilage_type]);
							}
						}
					}

					$lastCA = count($placedStudents['C']);

					if ($quotaBalanceForCompetitive > 0) {

						$remain = $quotaBalanceForCompetitive;

						for ($i = $lastCA; $remain > 0; $i++) {
							$placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['PlacementPreference']['accepted_student_id'];
							$remain--;
						}
					} else if ($lastCA < $intake_capacity) {
						$remain = ($intake_capacity - $lastCA);

						for ($i = $lastCA; $remain > 0; $i++) {
							$placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['PlacementPreference']['accepted_student_id'];
							$remain--;
						}
					}
					unset($sortedStudentByPreferenceAndGrade);

					// reformat placedStudents to make suitable for saveAll and
					// flag C and Q in the database.
					//debug($department_id);

					if (!empty($placedStudents)) {
						$placedStudentsSave = array();
						$count = 0;
						$failedC = 0;
						$ittCount = 0;
						$failedCArr = array();

						foreach ($placedStudents as $key => $value) {
							//ras
							debug(count(array_unique($value)));
							foreach ($value as $k => $student_id) {
								$ittCount++;
								//find prepared students

								$preparedStudent = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
									'conditions' => array(
										'PlacementParticipatingStudent.accepted_student_id' => $student_id,
										'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
										'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
										'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
										'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
										'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id']
									), 
									'recursive' => -1
								));

								if (isset($preparedStudent['PlacementParticipatingStudent']['id']) && !empty($preparedStudent['PlacementParticipatingStudent']['id'])) {

									ClassRegistry::init('PlacementParticipatingStudent')->id = $preparedStudent['PlacementParticipatingStudent']['id'];
									ClassRegistry::init('PlacementParticipatingStudent')->saveField('placementtype', AUTO_PLACEMENT);
									ClassRegistry::init('PlacementParticipatingStudent')->saveField('placement_based', $key);
									ClassRegistry::init('PlacementParticipatingStudent')->saveField('placement_round_participant_id', $department_id);

									$count++;
								} else {
									$failedC++;
								}
							}


							$failedCArr[$department_id] = $failedC;
						}
					}
				}


				//Not in quote amount department and assign those who are prepared students
				$unitUnderQuota = $this->getListOfUnitsNotFullyAssignedQuota($data);
				debug($unitUnderQuota);
				$groupLength = count($unitUnderQuota) - 1;
				$count = 0;

				if (!empty($unitUnderQuota)) {

					foreach ($unitUnderQuota as $department_id => $weight) {
						$placedStudentss = array();

						if ($count == $groupLength) {
							$notassignedList = $this->sortOutForRandomAssignmentForNonAssigned($data, $department_id, true);
						} else {
							$notassignedList = $this->sortOutForRandomAssignmentForNonAssigned($data, $department_id);
						}
						debug($department_id);
						debug(count($notassignedList));
						debug($weight['weight']);
						debug($notassignedList);
						//debug(count($notassignedList));
						$count++;

						if (!empty($notassignedList)) {

							$n = ($weight['weight'] <= count($notassignedList) ? $weight['weight'] : count($notassignedList));
							//$n = $intake_capacity;
							$lastIndex = $n;
							for ($i = 0; $i < $n; $i++) {
								$placedStudentss['C'][] = $notassignedList[$i]['PlacementParticipatingStudent']['accepted_student_id'];
							}

							unset($notassignedList);


							if (isset($placedStudentss) && !empty($placedStudentss)) {
								debug($placedStudentss);
								$failed = 0;
								foreach ($placedStudentss as $key => $value) {
									foreach ($value as $k => $student_id) {
										$preparedStudent = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
												'conditions' => array(
													'PlacementParticipatingStudent.accepted_student_id' => $student_id,
													'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
													'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
													'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
													'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
													'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id']
												),
												'recursive' => -1
											)
										);
										debug($preparedStudent);

										if (isset($preparedStudent['PlacementParticipatingStudent']['id']) && !empty($preparedStudent['PlacementParticipatingStudent']['id']) && empty($preparedStudent['PlacementParticipatingStudent']['placement_round_participant_id'])) {
											ClassRegistry::init('PlacementParticipatingStudent')->id = $preparedStudent['PlacementParticipatingStudent']['id'];
											ClassRegistry::init('PlacementParticipatingStudent')->saveField('placementtype', AUTO_PLACEMENT);
											ClassRegistry::init('PlacementParticipatingStudent')->saveField('placement_based', $key);
											ClassRegistry::init('PlacementParticipatingStudent')->saveField('placement_round_participant_id', $department_id);
										} else {
											$failed++;
										}
									}
								}

								debug($failed);
							}
						}
					}
				}
					
			} // nothing to execute since there is no defined unit

		}

		$result_order_by = 'PlacementParticipatingStudent.total_placement_weight desc';

		$placedstudent = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => array(
				'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
				'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
				'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
				'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id']
			),
			'contain' => array(
				'AcceptedStudent' => array('PlacementPreference'), 
				'Student', 
				'Program', 
				'ProgramType', 
				'PlacementRoundParticipant'
			),
			'order' => array('PlacementParticipatingStudent.placement_round_participant_id asc', $result_order_by),
			'limit' => 10000
		));

		$units = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'conditions' => array(
				"PlacementRoundParticipant.applied_for" => $data['PlacementSetting']['applied_for'],
				"PlacementRoundParticipant.program_id" => $data['PlacementSetting']['program_id'],
				"PlacementRoundParticipant.program_type_id" => $data['PlacementSetting']['program_type_id'], 
				"PlacementRoundParticipant.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementRoundParticipant.placement_round" => $data['PlacementSetting']['round']
			), 
			'fields' => array('PlacementRoundParticipant.id')
		));

		$dep_id = array_keys($units);
		$dep_name = ClassRegistry::init('PlacementRoundParticipant')->find('list', array('conditions' => array('PlacementRoundParticipant.id' => $dep_id)));
		$newly_placed_student = array();
		
		if (!empty($dep_name)) {
			foreach ($dep_name as $dk => $dv) {
				if (!empty($placedstudent)) {
					foreach ($placedstudent as $k => $v) {
						if ($dk == $v['PlacementRoundParticipant']['id']) {
							$newly_placed_student[$dv][$k] = $v;
						}
					}
				}

				$newly_placed_student['auto_summery'][$dv]['C'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id' => $dk,
						'PlacementParticipatingStudent.placement_based' => 'C'
					)
				));

				$newly_placed_student['auto_summery'][$dv]['Q'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id' => $dk,
						'PlacementParticipatingStudent.placement_based' => 'Q'
					)
				));
			}
		}

		return $newly_placed_student;
	}

	public function getAssignedStudentsForDirectPlacement($data)
	{
		$units = $this->getListOfUnitNeedByPrivilageStudentMost($data);
		$options = array();

		if (isset($data['PlacementSetting']['assigned_to']) && !empty($data['PlacementSetting']['assigned_to'])) {
			$options['PlacementParticipatingStudent.placement_round_participant_id'] = $data['PlacementSetting']['assigned_to'];
		}

		if (isset($data['PlacementSetting']['placement_based']) && !empty($data['PlacementSetting']['placement_based'])) {
			if ($data['PlacementSetting']['placement_based'] != "all") {
				$options['PlacementParticipatingStudent.placement_based'] = $data['PlacementSetting']['placement_based'];
			}
		}

		if (isset($data['PlacementSetting']['placementtype']) && !empty($data['PlacementSetting']['placementtype'])) {
			if ($data['PlacementSetting']['placementtype'] != "all") {
				$options['PlacementParticipatingStudent.placementtype'] = $data['PlacementSetting']['placementtype'];
			}
		}

		if (isset($data['PlacementSetting']['gender']) && !empty($data['PlacementSetting']['gender']) && $data['PlacementSetting']['gender'] != "All") {
			$options['AcceptedStudent.sex'] = $data['PlacementSetting']['gender'];
		}

		if (isset($data['PlacementSetting']['round']) && !empty($data['PlacementSetting']['round'])) {
			$options['PlacementParticipatingStudent.round'] = $data['PlacementSetting']['round'];
		}

		if (isset($data['PlacementSetting']['academic_year']) && !empty($data['PlacementSetting']['academic_year'])) {
			$options['PlacementParticipatingStudent.academic_year LIKE'] = $data['PlacementSetting']['academic_year'] . '%';
		}

		if (isset($data['PlacementSetting']['program_id']) && !empty($data['PlacementSetting']['program_id'])) {
			$options['PlacementParticipatingStudent.program_id'] = $data['PlacementSetting']['program_id'];
		}

		if (isset($data['PlacementSetting']['program_type_id']) && !empty($data['PlacementSetting']['program_type_id'])) {
			$options['PlacementParticipatingStudent.program_type_id'] = $data['PlacementSetting']['program_type_id'];
		}

		$options[] = 'PlacementParticipatingStudent.accepted_student_id in (select id from accepted_students where curriculum_id = 0 or curriculum_id is null )';

		$placedstudent = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => $options,
			'contain' => array(
				'AcceptedStudent' => array('PlacementPreference'), 
				'Student', 
				'Program', 
				'ProgramType', 
				'PlacementRoundParticipant'
			),
			'order' => array('PlacementParticipatingStudent.placement_round_participant_id' => 'ASC', 'PlacementParticipatingStudent.total_placement_weight' => 'DESC')
		));

		return $placedstudent;
	}

	public function getAssignedStudents($data)
	{
		$result_order_by = 'PlacementParticipatingStudent.total_placement_weight desc';
		//$units = $this->getListOfUnitNeedByPrivilageStudentMost($data);
		
		$units = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
			'conditions' => array(
				"PlacementRoundParticipant.applied_for" => $data['PlacementSetting']['applied_for'],
				"PlacementRoundParticipant.program_id" => $data['PlacementSetting']['program_id'],
				"PlacementRoundParticipant.program_type_id" => $data['PlacementSetting']['program_type_id'], 
				"PlacementRoundParticipant.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementRoundParticipant.placement_round" => $data['PlacementSetting']['round']
			), 
			'fields' => array('PlacementRoundParticipant.id')
		));


		$options = array();

		if (isset($data['PlacementSetting']['assigned_to']) && !empty($data['PlacementSetting']['assigned_to'])) {
			$options['PlacementParticipatingStudent.placement_round_participant_id'] = $data['PlacementSetting']['assigned_to'];
		}

		if (isset($data['PlacementSetting']['placement_based']) && !empty($data['PlacementSetting']['placement_based'])) {
			if ($data['PlacementSetting']['placement_based'] != "all") {
				$options['PlacementParticipatingStudent.placement_based'] = $data['PlacementSetting']['placement_based'];
			}
		}

		if (isset($data['PlacementSetting']['placementtype']) && !empty($data['PlacementSetting']['placementtype'])) {
			if ($data['PlacementSetting']['placementtype'] != "all") {
				$options['PlacementParticipatingStudent.placementtype'] = $data['PlacementSetting']['placementtype'];
			}
		}

		if (isset($data['PlacementSetting']['gender']) && !empty($data['PlacementSetting']['gender']) && $data['PlacementSetting']['gender'] != "All") {
			$options['AcceptedStudent.sex'] = $data['PlacementSetting']['gender'];
		}

		if (isset($data['PlacementSetting']['round']) && !empty($data['PlacementSetting']['round'])) {
			$options['PlacementParticipatingStudent.round'] = $data['PlacementSetting']['round'];
		}

		if (isset($data['PlacementSetting']['academic_year']) && !empty($data['PlacementSetting']['academic_year'])) {
			$options['PlacementParticipatingStudent.academic_year'] = $data['PlacementSetting']['academic_year'];
		}

		if (isset($data['PlacementSetting']['program_id']) && !empty($data['PlacementSetting']['program_id'])) {
			$options['PlacementParticipatingStudent.program_id'] = $data['PlacementSetting']['program_id'];
		}

		if (isset($data['PlacementSetting']['program_type_id']) && !empty($data['PlacementSetting']['program_type_id'])) {
			$options['PlacementParticipatingStudent.program_type_id'] = $data['PlacementSetting']['program_type_id'];
		}

		debug($options);

		$placedstudent = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => $options,
			'contain' => array(
				'AcceptedStudent' => array('PlacementPreference'), 
				'Student', 
				'Program', 
				'ProgramType', 
				'PlacementRoundParticipant'
			),
			'order' => array('PlacementParticipatingStudent.placement_round_participant_id asc', $result_order_by)
		));

		debug($placedstudent);
		$dep_id = array_keys($units);
		$dep_name = ClassRegistry::init('PlacementRoundParticipant')->find('list', array('conditions' => array('PlacementRoundParticipant.id' => $dep_id)));
		$newly_placed_student = array();


		if (!empty($dep_name)) {
			foreach ($dep_name as $dk => $dv) {
				if (!empty($placedstudent)) {
					foreach ($placedstudent as $k => $v) {
						if ($dk == $v['PlacementRoundParticipant']['id']) {
							$newly_placed_student[$dv][$k] = $v;
						}
					}
				}

				$newly_placed_student['auto_summery'][$dv]['C'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id' => $dk,
						'PlacementParticipatingStudent.placement_based' => 'C'
					)
				));

				$newly_placed_student['auto_summery'][$dv]['Q'] = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id' => $dk,
						'PlacementParticipatingStudent.placement_based' => 'Q'

					)
				));
			}
		}
		return $newly_placed_student;
	}

	public function cancel_placement_algorithm($data = array())
	{
		if (isset($data) && !empty($data)) {
			ClassRegistry::init('PlacementParticipatingStudent')->unbindModel(
				array(
					'belongsTo' => array(
						'AcceptedStudent',
						'Student',
						'Program',
						'ProgramType',
						'PlacementRoundParticipant'
					)
				)
			);

			$update = ClassRegistry::init('PlacementParticipatingStudent')->updateAll(
				array(
					'PlacementParticipatingStudent.placementtype' => null,
					'PlacementParticipatingStudent.placement_based' => null,
					'PlacementParticipatingStudent.placement_round_participant_id' => null,
				),
				array(
					'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id']
				)
			);

			return true;
		}

		return false;
	}

	public function approve_placement($data = array(), $type = 1)
	{
		if (isset($data) && !empty($data)) {

			ClassRegistry::init('PlacementParticipatingStudent')->unbindModel(array(
				'belongsTo' => array(
					'AcceptedStudent',
					'Student',
					'Program',
					'ProgramType',
					'PlacementRoundParticipant'
				)
			));

			$alreadyApproved = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.status' => 1
					)
				)
			);

			if ($alreadyApproved) {
				//return 2;
			}

			// $appliedUnitId=$this->getAppliedUnitsId( $data['PlacementSetting']['applied_for']);

			$update = ClassRegistry::init('PlacementParticipatingStudent')->updateAll(
				array(
					'PlacementParticipatingStudent.status' => $type,
					'PlacementParticipatingStudent.remark' => '"' . $data['PlacementSetting']['remark'] . '"',
				),
				array(
					'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id']
				)
			);

			// update the accepted students table, and archive the section accordingly
			ClassRegistry::init('PlacementParticipatingStudent')->bindModel(array(
				'belongsTo' => array(
					'AcceptedStudent',
					'Student',
					'Program',
					'ProgramType',
					'PlacementRoundParticipant'
				)
			));

			$allPlacedStudents = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
				'conditions' => array(
					'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
					'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id']
					//'placement_round_participant_id'
					/*
					array("OR" => array(
						"PlacementParticipatingStudent.placement_round_participant_id" => 0,
						"PlacementParticipatingStudent.placement_round_participant_id is null",
						"PlacementParticipatingStudent.placement_round_participant_id = ''"
					)),
					*/

				),
				'contain' => array(
					'AcceptedStudent',
					'Student',
					'Program',
					'ProgramType',
					'PlacementRoundParticipant'
				)
			));

			$clgexp = explode('~', $data['PlacementSetting']['applied_for']);

			$campuses = ClassRegistry::init('Campus')->find('list', array('conditions' => array('Campus.available_for_college' => $clgexp[1]), 'fields' => array('Campus.id', 'Campus.id')));
			debug($campuses);
			debug($clgexp);

			$collegeIds = ClassRegistry::init('College')->find('list', array('conditions' => array('College.campus_id' => $campuses), 'fields' => array('College.id', 'College.id')));
			debug($collegeIds);
			//start the updating process
			$sectionAttendedInStudent = array();
			$sectionArchived = array();
			$collegePlacement = false;

			if (!empty($allPlacedStudents)) {
				foreach ($allPlacedStudents as $pk => $pv) {
					//check the placement type and update accordingly
					debug($pv);

					if ($pv['PlacementRoundParticipant']['type'] == "College") {
						
						$collegePlacement = true;
						
						$sectionAttended = ClassRegistry::init('StudentsSection')->find('list', array(
							'conditions' => array(
								'StudentsSection.student_id' => $pv['PlacementParticipatingStudent']['student_id'],
								'StudentsSection.section_id in (select id from sections where academicyear="' . $pv['PlacementParticipatingStudent']['academic_year'] . '" and program_id="' . $pv['PlacementParticipatingStudent']['program_id'] . '" and program_type_id="' . $pv['PlacementParticipatingStudent']['program_type_id'] . '")'
							),
							'fields' => array('section_id', 'section_id')
						));

						//update the current college accepted students

						ClassRegistry::init('AcceptedStudent')->id = $pv['AcceptedStudent']['id'];
						ClassRegistry::init('AcceptedStudent')->saveField('college_id', $pv['PlacementRoundParticipant']['foreign_key']);
						ClassRegistry::init('AcceptedStudent')->saveField('original_college_id', $pv['PlacementRoundParticipant']['foreign_key']);

						//update the current college students
						ClassRegistry::init('Student')->id = $pv['Student']['id'];
						ClassRegistry::init('Student')->saveField('college_id', $pv['PlacementRoundParticipant']['foreign_key']);
						ClassRegistry::init('Student')->saveField('original_college_id', $pv['PlacementRoundParticipant']['foreign_key']);

					} else if ($pv['PlacementRoundParticipant']['type'] == "Department") {
						$collegePlacement = false;

						$collegeID = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.id' => $pv['PlacementRoundParticipant']['foreign_key']), 'contain' => array('College')));
						debug($pv);

						//update department and college id accepted students
						ClassRegistry::init('AcceptedStudent')->id = $pv['AcceptedStudent']['id'];
						ClassRegistry::init('AcceptedStudent')->saveField('department_id', $pv['PlacementRoundParticipant']['foreign_key']);
						
						if (isset($collegeID['College']['id']) && !empty($collegeID['College']['id'])) {
							ClassRegistry::init('AcceptedStudent')->saveField('college_id', $collegeID['College']['id']);
							ClassRegistry::init('AcceptedStudent')->saveField('original_college_id', $collegeID['College']['id']);
						}

						//update department and college id accepted students
						ClassRegistry::init('Student')->id = $pv['Student']['id'];
						ClassRegistry::init('Student')->saveField('department_id', $pv['PlacementRoundParticipant']['foreign_key']);

						if (isset($collegeID['College']['id']) && !empty($collegeID['College']['id'])) {
							ClassRegistry::init('Student')->saveField('college_id',  $collegeID['College']['id']);
							ClassRegistry::init('Student')->saveField('original_college_id',  $collegeID['College']['id']);
						}

					} else if ($pv['PlacementRoundParticipant']['type'] == "Specialization") {
						
						$collegePlacement = false;
						$departmentID = ClassRegistry::init('Specialization')->find('first', array('conditions' => array('Specialization.department_id' => $pv['PlacementRoundParticipant']['foreign_key']), 'contain' => array('Department')));

						//update specialization
						ClassRegistry::init('AcceptedStudent')->id = $pv['AcceptedStudent']['id'];
						ClassRegistry::init('AcceptedStudent')->saveField('specialization_id', $pv['PlacementRoundParticipant']['foreign_key']);
						
						if (isset($departmentID['Department']['id']) && !empty($departmentID['Department']['id'])) {
							ClassRegistry::init('AcceptedStudent')->saveField('department_id',  $departmentID['Department']['id']);
							ClassRegistry::init('AcceptedStudent')->saveField('college_id',  $departmentID['Department']['college_id']);
						}

						//update specialization
						ClassRegistry::init('Student')->id = $pv['Student']['id'];
						ClassRegistry::init('Student')->saveField('specialization_id', $pv['PlacementRoundParticipant']['foreign_key']);

						if (isset($departmentID['Department']['id']) && !empty($departmentID['Department']['id'])) {
							ClassRegistry::init('Student')->saveField('department_id',  $departmentID['Department']['id']);
							ClassRegistry::init('Student')->saveField('college_id',  $departmentID['Department']['college_id']);
						}
					}

					if ($pv['PlacementRoundParticipant']['type'] == "College") {
						debug($collegeIds);
						if (isset($collegeIds) && !empty($collegeIds)) {
							//available_for_college
							$sectionAttended = ClassRegistry::init('StudentsSection')->find('list', array(
								'conditions' => array(
									'StudentsSection.student_id' => $pv['Student']['id'],
									// 'StudentsSection.archive' => 0,
									'StudentsSection.section_id in (select id from sections where academicyear="' . $pv['PlacementRoundParticipant']['academic_year'] . '" and program_id="' . $pv['PlacementRoundParticipant']['program_id'] . '" and program_type_id="' . $pv['PlacementRoundParticipant']['program_type_id'] . '" and college_id in (' . implode(',', $collegeIds) . '))'
								), 
								'fields' => array('StudentsSection.section_id', 'StudentsSection.section_id')
							));
							debug($sectionAttended);
						}
					} else {
						$sectionAttended = ClassRegistry::init('StudentsSection')->find('list', array(
							'conditions' => array(
								'StudentsSection.student_id' => $pv['Student']['id'],
								'StudentsSection.archive' => 0,
								'StudentsSection.section_id in (select id from sections where academicyear="' . $pv['PlacementRoundParticipant']['academic_year'] . '" and program_id="' . $pv['PlacementRoundParticipant']['program_id'] . '" and program_type_id="' . $pv['PlacementRoundParticipant']['program_type_id'] . '")'
							), 'fields' => array(
								'StudentsSection.section_id',
								'StudentsSection.section_id'
							)
						));
					}

					debug($sectionAttended);
					
					//debug($pv['Student']['id']);
					if (isset($sectionAttended) && !empty($sectionAttended)) {

						if ($pv['PlacementRoundParticipant']['type'] == "College") {
							if ($clgexp[1] == 2) {
								$intoconsideration[] = 1;
								$intoconsideration[] = 11;
							} else if ($clgexp[1] == 6) {
								$intoconsideration[] = 6;
								$intoconsideration[] = 5;
							}

							$sectionsDetails = ClassRegistry::init('Section')->find('all', array('conditions' => array('Section.id' => $sectionAttended), 'recursive' => -1));

							if (!empty($sectionsDetails)) {
								foreach ($sectionsDetails as $sedk => $secv) {
									//check if the section is where it belongs
									$sectionArchived[$secv['Section']['id']] = $secv['Section']['id'];
									debug($secv);

									if ($pv['PlacementRoundParticipant']['foreign_key'] == $secv['Section']['college_id'] || in_array($secv['Section']['college_id'], $intoconsideration)) {
										$notarchived = ClassRegistry::init('StudentsSection')->updateAll(
											array(
												'StudentsSection.archive' => 0,
											),
											array(
												'StudentsSection.student_id' => $pv['Student']['id'],
												'StudentsSection.section_id' => $secv['Section']['id']
											)
										);
									} else {
										$notarchived = ClassRegistry::init('StudentsSection')->updateAll(
											array(
												'StudentsSection.archive' => 1,
											),
											array(
												'StudentsSection.student_id' => $pv['Student']['id'],
												'StudentsSection.section_id' => $secv['Section']['id']
											)
										);
									}
								}
							}
						} else {
							$archived = ClassRegistry::init('StudentsSection')->updateAll(
								array(
									'StudentsSection.archive' => 1,
								),
								array(
									'StudentsSection.student_id' => $pv['Student']['id'],
									'StudentsSection.section_id' => $sectionAttended
								)
							);
						}

						$sectionAttendedInStudent[] = $pv['Student']['id'];
					}
				}
			}

			// archive remaining only for those who are placed to college

			if (isset($sectionAttendedInStudent) && !empty($sectionAttendedInStudent) && isset($sectionArchived) && !empty($sectionArchived) && $collegePlacement) {

				$archived = ClassRegistry::init('StudentsSection')->updateAll(
					array(
						'StudentsSection.archive' => 1,
					),
					array(
						'StudentsSection.student_id NOT IN ' => $sectionAttendedInStudent,
						'StudentsSection.section_id' => $sectionArchived,
					)
				);

				$studentsNotAssignedToEngineeringScience = ClassRegistry::init('StudentsSection')->find('list', array(
					'conditions' => array(
						'StudentsSection.student_id NOT IN ' => $sectionAttendedInStudent,
						'StudentsSection.student_id in (select id from students where department_id is null or department_id="" or department_id = 0 )',
						'StudentsSection.section_id' => $sectionArchived,
						//'StudentsSection.archive' => 1,
					),
					'fields' => array(
						'StudentsSection.student_id',
						'StudentsSection.student_id'
					)
				));

				if (isset($studentsNotAssignedToEngineeringScience) && !empty($studentsNotAssignedToEngineeringScience)) {
					$acceptedStudentNotAssignedToEngineeringScience = ClassRegistry::init('Student')->find('list', array(
						'conditions' => array('Student.id ' => $studentsNotAssignedToEngineeringScience),
						'fields' => array('Student.accepted_student_id','Student.accepted_student_id')
					));
				}

				if (isset($acceptedStudentNotAssignedToEngineeringScience) && !empty($acceptedStudentNotAssignedToEngineeringScience) && isset($studentsNotAssignedToEngineeringScience) && !empty($studentsNotAssignedToEngineeringScience) && isset($clgexp[1]) && !empty($clgexp[1])) {

					$update = ClassRegistry::init('Student')->updateAll(
						array(
							'Student.college_id' =>  $clgexp[1],
							'Student.original_college_id' =>  $clgexp[1]
						),
						array(
							'Student.id' => $studentsNotAssignedToEngineeringScience
						)
					);

					$update = ClassRegistry::init('AcceptedStudent')->updateAll(
						array(
							'AcceptedStudent.college_id' =>  $clgexp[1],
							'AcceptedStudent.original_college_id' =>  $clgexp[1]
						),
						array(
							'AcceptedStudent.id' => $acceptedStudentNotAssignedToEngineeringScience
						)
					);

					// restore archived section to target college section if they exists
					if ($clgexp[1] == 2) {
						$collegeConsideration[] = $clgexp[1];
						$collegeConsideration[] = 4;
						$collegeConsideration[] = 3;
					} else if ($clgexp[1] == 6) {
						$collegeConsideration[] = $clgexp[1];
						$collegeConsideration[] = 9;
					} else {
						$collegeConsideration[] = $clgexp[1];
					}

					$targetCollegeSectionIds = ClassRegistry::init('Section')->find('list', array(
						'conditions' => array(
							'Section.academicyear' => $data['PlacementSetting']['academic_year'],
							'Section.program_id' => $data['PlacementSetting']['program_id'],
							'Section.program_type_id' => $data['PlacementSetting']['program_type_id'],
							'Section.college_id' => $collegeConsideration,
							'Section.department_id is null'
						),
						'fields' => array('Section.id', 'Section.id')
					));

					if (isset($targetCollegeSectionIds) && !empty($targetCollegeSectionIds)) {
						
						$unarchive = ClassRegistry::init('StudentsSection')->updateAll(
							array(
								'StudentsSection.archive' => 0,
							),
							array(
								'StudentsSection.student_id' => $studentsNotAssignedToEngineeringScience,
								'StudentsSection.section_id' => $targetCollegeSectionIds,
							)
						);

						//
						$targetCollegeSectionIdss = ClassRegistry::init('Section')->find('list', array(
							'conditions' => array(
								'Section.academicyear' => $data['PlacementSetting']['academic_year'],
								'Section.program_id' => $data['PlacementSetting']['program_id'],
								'Section.program_type_id' => $data['PlacementSetting']['program_type_id'],
								'Section.college_id' => $collegeIds,
								'Section.department_id is null'
							),
							'fields' => array('Section.id', 'Section.id')
						));

						if (!empty($targetCollegeSectionIdss)) {
							foreach ($targetCollegeSectionIdss as $sek => $secv) {
								$count = ClassRegistry::init('StudentsSection')->find('count', array(
									'conditions' => array(
										'StudentsSection.section_id' => $secv,
										'StudentsSection.archive' => 0,
									)
								));

								debug($count);
								if ($count == 0) {
									//archive the section so that no one will be added
									ClassRegistry::init('Section')->updateAll(
										array(
											'Section.archive' => 1,
										),
										array(
											'Section.id' => $secv
										)
									);
								}
							}
						}
					}
				}
				//

				// put them in their college where their section is active

				if (!empty($studentsNotAssignedToEngineeringScience)) {
					foreach ($studentsNotAssignedToEngineeringScience as $sk => $sv) {
						
						$studentdetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $sv), 'recursive' => -1));
						
						$studentsection = ClassRegistry::init('StudentsSection')->find('first', array(
							'conditions' => array(
								'StudentsSection.student_id' => $studentdetail['Student']['id'],
								'StudentsSection.archive' => 0,
							),
							'contain' => array('Section')
						));

						if (isset($studentdetail) && !empty($studentdetail) && isset($studentsection['Section']['college_id']) && !empty($studentsection['Section']['college_id'])) {
							ClassRegistry::init('AcceptedStudent')->id = $studentdetail['Student']['accepted_student_id'];
							ClassRegistry::init('AcceptedStudent')->saveField('college_id', $studentsection['Section']['college_id']);
							//update the current college
							ClassRegistry::init('Student')->id = $studentdetail['Student']['id'];
							ClassRegistry::init('Student')->saveField('college_id', $studentsection['Section']['college_id']);
						}
					}
				}
			}

			return 1;
		}

		return 0;
	}


	public function direct_placement($data = array(), $type = "") 
	{
		if (isset($data) && !empty($data)) {
			ClassRegistry::init('PlacementParticipatingStudent')->unbindModel(
				array(
					'belongsTo' => array(
						'AcceptedStudent',
						'Student',
						'Program',
						'ProgramType',
						'PlacementRoundParticipant'
					)
				)
			);
			$selectedParticipantsIds = array_keys($data['PlacementDirectly']['approve'], 1);

			$update = ClassRegistry::init('PlacementParticipatingStudent')->updateAll(
				array(
					'PlacementParticipatingStudent.placement_round_participant_id' => $data['PlacementDirectly']['placement_round_participant_id'],
					'PlacementParticipatingStudent.placementtype' => '"' . $type . '"',
				),
				array(
					'PlacementParticipatingStudent.id' => $selectedParticipantsIds
				)
			);

			// update the accepted students table, and archive the section accordingly
			ClassRegistry::init('PlacementParticipatingStudent')->bindModel(
				array(
					'belongsTo' => array(
						'AcceptedStudent',
						'Student',
						'Program',
						'ProgramType',
						'PlacementRoundParticipant'
					)
				)
			);

			$allPlacedStudents = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
				'conditions' => array(
					'PlacementParticipatingStudent.id' => $selectedParticipantsIds,
				),
				'contain' => array(
					'AcceptedStudent',
					'Student',
					'Program',
					'ProgramType',
					'PlacementRoundParticipant'
				)
			));
			//debug($allPlacedStudents);
			//return 1;
			//start the updating process
			foreach ($allPlacedStudents as $pk => $pv) {
				//check the placement type and update accordingly
				if ($pv['PlacementRoundParticipant']['type'] == "College") {
					$sectionAttended = ClassRegistry::init('StudentsSection')->find('list', array(
						'conditions' => array(
							'StudentsSection.student_id' => $pv['PlacementParticipatingStudent']['student_id'],
							'StudentsSection.section_id in (select id from sections where academicyear="' . $pv['PlacementParticipatingStudent']['academic_year'] . '" and program_id="' . $pv['PlacementParticipatingStudent']['program_id'] . '" and program_type_id="' . $pv['PlacementParticipatingStudent']['program_type_id'] . '")'
						),
						'fields' => array('section_id', 'section_id')
					));


					//update the current college
					//engineering

					ClassRegistry::init('AcceptedStudent')->id = $pv['AcceptedStudent']['id'];
					ClassRegistry::init('AcceptedStudent')->saveField('college_id', $pv['PlacementRoundParticipant']['foreign_key']);
					ClassRegistry::init('AcceptedStudent')->saveField('original_college_id', $pv['PlacementRoundParticipant']['foreign_key']);
					ClassRegistry::init('AcceptedStudent')->saveField('department_id', null);

					//update the current college
					ClassRegistry::init('Student')->id = $pv['Student']['id'];
					ClassRegistry::init('Student')->saveField('college_id', $pv['PlacementRoundParticipant']['foreign_key']);
					ClassRegistry::init('Student')->saveField('original_college_id', $pv['PlacementRoundParticipant']['foreign_key']);
					ClassRegistry::init('Student')->saveField('department_id', null);


					$sectionAttended = ClassRegistry::init('StudentsSection')->find('list', array(
						'conditions' => array(
							'StudentsSection.student_id' => $pv['Student']['id'], 'StudentsSection.archive' => 1,
							'StudentsSection.section_id in (select id from sections where academicyear="' . $pv['PlacementRoundParticipant']['academic_year'] . '" and program_id="' . $pv['PlacementRoundParticipant']['program_id'] . '" and program_type_id="' . $pv['PlacementRoundParticipant']['program_type_id'] . '")'
						)
					));

					if (isset($sectionAttended) && !empty($sectionAttended)) {
						$archived = ClassRegistry::init('StudentsSection')->updateAll(
							array(
								'StudentsSection.archive'=> 1,
							),
							array(
								'StudentsSection.student_id' => $pv['Student']['id'],
								'StudentsSection.section_id' => $sectionAttended
							)
						);
					}
				} else if ($pv['PlacementRoundParticipant']['type'] == "Department") {
					$collegeID = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.college_id' => $pv['PlacementRoundParticipant']['foreign_key'])));
					//update department
					ClassRegistry::init('AcceptedStudent')->id = $pv['AcceptedStudent']['id'];
					ClassRegistry::init('AcceptedStudent')->saveField('department_id', $pv['PlacementRoundParticipant']['foreign_key']);

					if (isset($collegeID['College']['id']) && !empty($collegeID['College']['id'])) {
						ClassRegistry::init('AcceptedStudent')->saveField('college_id', $collegeID['College']['id']);
						ClassRegistry::init('AcceptedStudent')->saveField('original_college_id', $collegeID['College']['id']);
					}

					//update department
					ClassRegistry::init('Student')->id = $pv['Student']['id'];
					ClassRegistry::init('Student')->saveField('department_id', $pv['PlacementRoundParticipant']['foreign_key']);

					if (isset($collegeID['College']['id']) && !empty($collegeID['College']['id'])) {
						ClassRegistry::init('Student')->saveField('college_id',  $collegeID['College']['id']);
						ClassRegistry::init('Student')->saveField('original_college_id',  $collegeID['College']['id']);
					}

				} else if ($pv['PlacementRoundParticipant']['type'] == "Specialization") {
					$departmentID = ClassRegistry::init('Specialization')->find('first', array('conditions' => array('Specialization.department_id' => $pv['PlacementRoundParticipant']['foreign_key']), 'contain' => array('Department')));

					//update specialization
					ClassRegistry::init('AcceptedStudent')->id = $pv['AcceptedStudent']['id'];
					ClassRegistry::init('AcceptedStudent')->saveField('specialization_id', $pv['PlacementRoundParticipant']['foreign_key']);

					if (isset($departmentID['Department']['id']) && !empty($departmentID['Department']['id'])) {
						ClassRegistry::init('AcceptedStudent')->saveField('department_id',  $departmentID['Department']['id']);
						ClassRegistry::init('AcceptedStudent')->saveField('college_id',  $departmentID['Department']['college_id']);
					}

					//update specialization
					ClassRegistry::init('Student')->id = $pv['Student']['id'];
					ClassRegistry::init('Student')->saveField('specialization_id', $pv['PlacementRoundParticipant']['foreign_key']);

					if (isset($departmentID['Department']['id']) && !empty($departmentID['Department']['id'])) {
						ClassRegistry::init('Student')->saveField('department_id',  $departmentID['Department']['id']);
						ClassRegistry::init('Student')->saveField('college_id',  $departmentID['Department']['college_id']);
					}
				}
			}
			return 1;
		}
		return 0;
	}

	public function getAppliedUnitsId($appliedFor)
	{
		$targetUnit = explode('~', $appliedFor);
		$restoringUnits = array();

		if ($targetUnit[0] == "c") {
			$restoringUnits['college_id'] = $targetUnit[1];
		} else if ($targetUnit[0] == "d") {
			$restoringUnits['department_id'] = $targetUnit[1];
		} else if ($targetUnit[0] == "s") {
			$restoringUnits['specialization_id'] = $targetUnit[1];
		}

		return $restoringUnits;
	}



	function privilagedStudentsFilterOut($data, $department_id, $adjusted_privilaged_quota, $placedStudents, $privilage_type) 
	{
		$competitivly_assigned_students = (empty($placedStudents['C']) ? array() : $placedStudents['C']);
		$quota_assigned_students = (empty($placedStudents['Q']) ? array() : $placedStudents['Q']);
		
		//get count of participating deparment for the given unit and academic year
		$number_of_participating_department = ClassRegistry::init('PlacementRoundParticipant')->find('count', array(
			'conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'], 
				'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
				'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round'],
				'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id']
			), 
			'recursive' => -1
		));

		if (strcasecmp($privilage_type, "female") == 0) {
			$privilagedcondition = "AcceptedStudent.sex = 'female'";
		} elseif (strcasecmp($privilage_type, "disability") == 0) {
			$privilagedcondition = "AcceptedStudent.disability IS NOT NULL";
		} else {
			$regions = ClassRegistry::init('PlacementRoundParticipant')->find('first', array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'], 
					'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id']
				), 
				'recursive' => -1
			));

			if (empty($regions['PlacementRoundParticipant']['developing_region'])) {
				return array();
			}

			$privilagedcondition = "AcceptedStudent.region_id IN (" . $regions['PlacementRoundParticipant']['developing_region'] . ")";
		}

		$list_students_in_x_preference = array();
		$list_of_students_selected = array();

		if (count($number_of_participating_department) && $adjusted_privilaged_quota[$privilage_type] > 0) {
			// the logic is unkown ?
			for ($i = 1; $i <= $number_of_participating_department; $i++) {
				$list_students_in_x_preference = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
					'joins' => array(array(
						'table' => 'placement_preferences',
						'alias' => 'PlacementPreference',
						'type' => 'LEFT',
						'conditions' => array('PlacementPreference.accepted_student_id = PlacementParticipatingStudent.accepted_student_id')
					)),
					'fields' => array(
						'PlacementPreference.preference_order',
						'PlacementParticipatingStudent.id',
						'PlacementPreference.accepted_student_id',
						'PlacementParticipatingStudent.total_placement_weight',
					),
					'contain' => array('AcceptedStudent'),
					'conditions' => array(
						"OR" => array(
							'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
							'PlacementParticipatingStudent.placement_round_participant_id = ""',
							'PlacementParticipatingStudent.placement_round_participant_id = 0'
						),
						"PlacementPreference.academic_year LIKE " => $data['PlacementSetting']['academic_year'] . '%', 
						"PlacementPreference.round" => $data['PlacementSetting']['round'],
						"PlacementPreference.placement_round_participant_id" => $department_id,
						'PlacementPreference.preference_order' => $i, 
						$privilagedcondition,
					),
					'order' => array('PlacementPreference.preference_order' => 'ASC', 'PlacementParticipatingStudent.total_placement_weight' => 'DESC'),
					'group' => array(
						'PlacementPreference.preference_order',
						'PlacementPreference.accepted_student_id',
					),
				));

				// simply count privilaged students in preference 1

				if ($i == 1) {
					//if the students is not in competitive list, please consider me in the quota.
					if (!empty($list_students_in_x_preference)) {
						foreach ($list_students_in_x_preference as $student) {
							if (in_array($student['PlacementPreference']['accepted_student_id'], $competitivly_assigned_students) === false && in_array($student['PlacementPreference']['accepted_student_id'], $quota_assigned_students) === false) {
								$list_of_students_selected[] = $student['PlacementPreference']['accepted_student_id'];
								//echo "Giba = ".$student['PlacementPreference']['accepted_student_id'];
							}
						}
					}

					// if there are enough students by their first preference for allocated quota for the department. no need to continue the loop if there are enough privilaged students in system
					if (count($list_of_students_selected) >= $adjusted_privilaged_quota[$privilage_type]) {
						break;
					}

					continue;
				}

				// we need to have already allocated departments_id
				$reformat_list_of_department_ids = array();

				$list_of_departments_id = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
					'conditions' => array(
						'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id is not null ',
						//'PlacementParticipatingStudent.placement_round_participant_id not ' => array('', 0),
						'PlacementParticipatingStudent.placement_round_participant_id != ""',
						'PlacementParticipatingStudent.placement_round_participant_id != 0',
						
					),
					'fields' => array('DISTINCT PlacementParticipatingStudent.placement_round_participant_id'),
					'recursive' => -1
				));

				if (!empty($list_of_departments_id)) {
					foreach ($list_of_departments_id  as $key => $value) {
						$reformat_list_of_department_ids[] = $value['PlacementParticipatingStudent']['placement_round_participant_id'];
					}
				}

				$excluded_student_count = 0;
				//per students check for departments assingment and exclude
				$preliminary_students_filter = array();

				if (!empty($list_students_in_x_preference)) {
					foreach ($list_students_in_x_preference as &$student) {
						//check students back preferenc if they are not assigned.
						$exclude_student = false;
						for ($j = 1; $j < $i; $j++) {
							$department_id_accepted_student = $this->find('first', array(
								'conditions' => array(
									'PlacementPreference.accepted_student_id' => $student['PlacementPreference']['accepted_student_id'],
									'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
									'PlacementPreference.round' => $data['PlacementSetting']['round'],
									'PlacementPreference.preference_order' => $j
								),
								'fields' => array('PlacementPreference.placement_round_participant_id')
							));

							// is her/his previous preference selected department was processed ? Exclude from selecting, wait till her preference runs.

							if (!empty($reformat_list_of_department_ids) && isset($department_id_accepted_student['PlacementPreference']['placement_round_participant_id']) && is_numeric($department_id_accepted_student['PlacementPreference']['placement_round_participant_id']) && $department_id_accepted_student['PlacementPreference']['placement_round_participant_id'] > 0) {
								if (in_array($department_id_accepted_student['PlacementPreference']['placement_round_participant_id'], $reformat_list_of_department_ids) === false) {
									$exclude_student = true;
									break;
								}
							}
						}

						if (!$exclude_student) {
							$preliminary_students_filter[] = $student['PlacementPreference']['accepted_student_id'];
						}
					}

					//if the students is not in competitive list, please consider me in the quota.

					if (!empty($preliminary_students_filter)) {
						foreach ($preliminary_students_filter as $student_id) {
							if (in_array($student_id, $competitivly_assigned_students) === false && in_array($student_id, $quota_assigned_students) === false) {
								$list_of_students_selected[] = $student_id;
							}
						}
					}
				}

				if (count($list_of_students_selected) >= $adjusted_privilaged_quota[$privilage_type]) {
					break;
				}
			}

			$privilaged_selected[$privilage_type] = $list_of_students_selected;
			return $privilaged_selected;
		}
		//nothing
		return array();
	}


	/*

	function privilagedStudentsFilterOut(
		$data,
		$department_id,
		$adjusted_privilaged_quota,
		$placedStudents,
		$privilage_type,
		$notQualifedInCompetitive
	) {

		$competitivly_assigned_students = (empty($placedStudents['C']) ? array() : $placedStudents['C']);
		$quota_assigned_students = (empty($placedStudents['Q']) ? array() : $placedStudents['Q']);
		//get count of participating deparment for the given unit and academic year
		$number_of_participating_department = ClassRegistry::init('PlacementRoundParticipant')
			->find('count', array('conditions' => array(
				'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'], 'PlacementRoundParticipant.academic_year'
				=> $data['PlacementSetting']['academic_year'],
				'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round'],
				'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id']

			), 'recursive' => -1));
		if (
			strcasecmp($privilage_type, "female")
			== 0
		) {
			$privilagedcondition = "sex='female'";
		} elseif (strcasecmp($privilage_type, "disability") == 0) {
			$privilagedcondition = "disability IS NOT NULL";
		} else {
			$regions = ClassRegistry::init('PlacementRoundParticipant')
				->find('first', array('conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'], 'PlacementRoundParticipant.academic_year'
					=> $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id']
				), 'recursive' => -1));
			if (empty($regions['PlacementRoundParticipant']['developing_region']))
				return array();
			$privilagedcondition = "region_id IN (" . $regions['PlacementRoundParticipant']['developing_regions_id'] . ")";
		}

		$result_order_by = 'PlacementParticipatingStudent.total_placement_weight desc';
		if (
			$number_of_participating_department &&
			$adjusted_privilaged_quota[$privilage_type] > 0
		) {
			// get accepted student ids where the privileged
			 $acceptedStudentIds=ClassRegistry::init('PlacementParticipatingStudent')
			->find('list', array('conditions' => array(
				'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'], 'PlacementParticipatingStudent.academic_year'
				=> $data['PlacementSetting']['academic_year'],
				'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
				'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
				'PlacementParticipatingStudent.accepted_student_id in (select id from accepted_students where '.$privilagedcondition.' )',
				'PlacementParticipatingStudent.accepted_student_id' =>$notQualifedInCompetitive



			), 'fields' => array(
				'PlacementParticipatingStudent.accepted_student_id',
				'PlacementParticipatingStudent.accepted_student_id'
			)));

			$partcipantsOrderByResult =  ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(

			'fields' => array(
				'PlacementParticipatingStudent.id',
				'PlacementParticipatingStudent.total_placement_weight',
				'PlacementParticipatingStudent.accepted_student_id'
			),
			'recursive' => -1,
			'conditions' => array(
				"OR" => array(
					'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
					'PlacementParticipatingStudent.placement_round_participant_id' => array('', 0)
				),
				"PlacementParticipatingStudent.academic_year"
				=> $data['PlacementSetting']['academic_year'], "PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],

				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
				"PlacementParticipatingStudent.accepted_student_id" =>$acceptedStudentIds



			),
			'order' => array(

				$result_order_by,

			),
			'limit' => 100000
		));
		$students_to_be_removed=array();
		 foreach($partcipantsOrderByResult as $park=>$student){

				$x =  ClassRegistry::init('PlacementPreference')->find('first', array(

				'conditions' => array(

					"PlacementPreference.academic_year"
					=> $data['PlacementSetting']['academic_year'],
					"PlacementPreference.round" => $data['PlacementSetting']['round'],
					"PlacementPreference.placement_round_participant_id" => $department_id,
					"PlacementPreference.accepted_student_id" => $student['PlacementParticipatingStudent']['accepted_student_id'],
					'PlacementPreference.round'=> $data['PlacementSetting']['round']
				),
				'recursive' => -1,
				'order' => array(
					'PlacementPreference.preference_order asc',
				),
			));


			if (isset($x['PlacementPreference']) && !empty($x['PlacementPreference'])) {

				$student['PlacementPreference'] = $x['PlacementPreference'];
				       //$partcipantsOrderByResultNew[] = $pk;
			}


			if (in_array($student['PlacementPreference']['accepted_student_id'],$competitivly_assigned_students) === false && in_array($student['PlacementPreference']['accepted_student_id'],$quota_assigned_students) === false  ) {
							$list_of_students_selected[] = $student['PlacementPreference']['accepted_student_id'];
				}
		  }

		  $privilaged_selected[$privilage_type] = $list_of_students_selected;
		  return $privilaged_selected;
	   }
		//nothing
	   return array();
	}
	*/


	function getListOfUnitNeedByPrivilageStudentMost($data = array())
	{

		$prefrenceMatrixOfDepartments = $this->find('all', array(
			'fields' => array(
				'PlacementPreference.placement_round_participant_id', 'PlacementPreference.preference_order',
				'count(PlacementPreference.accepted_student_id) as student_count'
			),
			'limit' => 100000,
			'conditions' => array(
				"PlacementPreference.academic_year like" => $data['PlacementSetting']['academic_year'] . '%',
				"PlacementPreference.round" => $data['PlacementSetting']['round'],
				"PlacementPreference.preference_order" => array(1, 2, 3),

				"PlacementRoundParticipant.applied_for" => $data['PlacementSetting']['applied_for']
			),

			'group' => array('PlacementPreference.placement_round_participant_id', 'PlacementPreference.preference_order'),
			'order' => array('PlacementPreference.placement_round_participant_id', 'PlacementPreference.preference_order asc'),
			'contain' => array('PlacementRoundParticipant')
		));

		$prefrenceMatrix = array();
		foreach ($prefrenceMatrixOfDepartments as $key => $prefrenceMatrixOfDepartment) {
			$prefrenceMatrix[$prefrenceMatrixOfDepartment['PlacementPreference']['placement_round_participant_id']][$prefrenceMatrixOfDepartment['PlacementPreference']['preference_order']] =
				$prefrenceMatrixOfDepartment[0]['student_count'];
		}
		//unit capacity
		$department_capacity = ClassRegistry::init('PlacementRoundParticipant')->find(
			"all",
			array(
				"fields" => array("PlacementRoundParticipant.id", "PlacementRoundParticipant.intake_capacity"),
				"recursive" => -1,
				"conditions" => array(
					'PlacementRoundParticipant.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
				)
			)
		);

		$weight = array();
		$count = count($prefrenceMatrix);
		for (
			$i = 1;
			$i <= count($prefrenceMatrix);
			$i++
		) {
			$weight[$i] = $count--;
		}

		$unitssprivilagedorder = array();
		if (!empty($prefrenceMatrix)) {

			foreach ($prefrenceMatrix as $key => $value) {
				$sum = 0;
				$total_student = array_sum($value);
				//multipied each number of students by weight
				foreach ($value as $preference_key => $number_students) {
					foreach ($weight as $weight_preference_key => $weight_preference_point) {

						if ($preference_key == $weight_preference_key) {

							$sum = $sum + ($weight_preference_point * $number_students);
						}
					}
				}
				//debug($department_capacity);
				$unit_capacity_number = 1;
				foreach ($department_capacity as $depat_key => $dept_value) {
					if ($dept_value['PlacementRoundParticipant']['id'] == $key) {
						$unit_capacity_number = $dept_value['PlacementRoundParticipant']['intake_capacity'];
						break;
					}
				}
				//   $departmentsprivilagedorder[$key]['sum']=$sum;
				$unitssprivilagedorder[$key]['weight'] = $sum / $unit_capacity_number;
			}
		}

		uasort($unitssprivilagedorder, array(&$this, 'compareweight'));
		debug($unitssprivilagedorder);
		//unitssprivilagedorder
		return $unitssprivilagedorder;
	}

	function getListOfUnitsNotFullyAssignedQuota($data = array())
	{
		//unit capacity
		$department_capacity = ClassRegistry::init('PlacementRoundParticipant')->find("all", array(
			"conditions" => array(
				'PlacementRoundParticipant.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
				'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
				'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
				'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
			),
			"fields" => array("PlacementRoundParticipant.id", "PlacementRoundParticipant.intake_capacity"),
			"recursive" => -1,
		));

		$weight = array();

		$unitssprivilagedorder = array();

		if (!empty($department_capacity)) {
			foreach ($department_capacity as $k => $v) {
				$assignedCount = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
						"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
						"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
						"PlacementParticipatingStudent.program_id" => $data['PlacementSetting']['program_id'],
						"PlacementParticipatingStudent.program_type_id" => $data['PlacementSetting']['program_type_id'],
						"PlacementParticipatingStudent.placement_round_participant_id" => $v['PlacementRoundParticipant']['id']
					),
					//'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.placement_round_participant_id'),
				));

				$intake_capacity = $v['PlacementRoundParticipant']['intake_capacity'];
				$difference = $intake_capacity - $assignedCount;
				debug($difference);

				if ($difference > 0) {
					$unitssprivilagedorder[$v['PlacementRoundParticipant']['id']]['weight'] = $difference;
				}
			}
		}

		uasort($unitssprivilagedorder, array(&$this, 'compareweight'));
		return $unitssprivilagedorder;
	}

	function sortOutForRandomAssignmentForNonAssigned($data, $department_id = null, $lastRound = false)
	{
		// students who completed their preference on time
		$partcipantsOrderByResult =  ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => array(
				"OR" => array(
					'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
					'PlacementParticipatingStudent.placement_round_participant_id = ""',
					'PlacementParticipatingStudent.placement_round_participant_id = 0',
				),
				"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'], 
				"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
			),
			'order' => array('PlacementParticipatingStudent.total_placement_weight' => 'DESC'),
			'fields' => array(
				'PlacementParticipatingStudent.id',
				'PlacementParticipatingStudent.total_placement_weight',
				'PlacementParticipatingStudent.accepted_student_id'
			),
			'recursive' => -1,
			'limit' => 100000
		));

		$partcipantsOrderByResultNew = array();
		$withoutPreference = array();

		if (!empty($partcipantsOrderByResult)) {
			foreach ($partcipantsOrderByResult as $k => $pk) {
				$x = ClassRegistry::init('PlacementPreference')->find('first', array(
					'conditions' => array(
						"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
						"PlacementPreference.round" => $data['PlacementSetting']['round'],
						"PlacementPreference.placement_round_participant_id" => $department_id,
						"PlacementPreference.accepted_student_id" => $pk['PlacementParticipatingStudent']['accepted_student_id'],
					),
					'recursive' => -1,
					'order' => array('PlacementPreference.preference_order' => 'ASC'),
				));

				if (isset($x['PlacementPreference']) && !empty($x['PlacementPreference'])) {
					$pk['PlacementPreference'] = $x['PlacementPreference'];
					$partcipantsOrderByResultNew[] = $pk;
				} else if (empty($x['PlacementPreference'])) {
					$withoutPreference[] = $pk;
				}
			}
		}

		$sortOutStudentsResultCategory =  $partcipantsOrderByResultNew;
		$failed = 0;

		if (!empty($sortOutStudentsResultCategory)) {
			foreach ($sortOutStudentsResultCategory as $k => &$v) {
				if (!empty($v['PlacementPreference']['preference_order']) && is_numeric($v['PlacementPreference']['preference_order']) && $v['PlacementPreference']['preference_order'] > 1) {
					$previous_preferences = $this->find('all', array(
						'conditions' => array(
							'PlacementPreference.accepted_student_id' => $v['PlacementPreference']['accepted_student_id'],
							"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
							"PlacementPreference.round" => $data['PlacementSetting']['round'],
							"PlacementPreference.preference_order < " => $v['PlacementPreference']['preference_order'],
						),
						'recursive' => -1
					));

					$placement_pp_dept_done = 1;

					if (!empty($previous_preferences)) {
						foreach ($previous_preferences as $pp_v) {
							$placed_students_count = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
								'conditions' => array(
									'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
									'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
									'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
									'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
									'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
									'PlacementParticipatingStudent.placement_round_participant_id' => $pp_v['PlacementPreference']['placement_round_participant_id'],
								),
								'recursive' => -1
							));

							if ($placed_students_count == 0) {
								$placement_pp_dept_done = 0;
								break;
							}
						}
					}

					if ($placement_pp_dept_done == 1) {
						//Sort the student to keep more competitive
						$students_to_be_sorted[] = $v;
					} else  if ($placement_pp_dept_done == 0) {
						$students_to_be_removed[] = $v['PlacementPreference']['accepted_student_id'];
					}
				} else if (empty($v['PlacementPreference'])) {
					$failed++;
				} else { //dont want first choice to be consider since the last random assignment
					unset($sortOutStudentsResultCategory[$k]);
				}
			}
		}

		debug($failed);

		/* $notAssignedDifference = ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
			'conditions' => array(
				"NOT" => array("PlacementParticipatingStudent.accepted_student_id" => $students_to_be_removed),
				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
				"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
				"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementParticipatingStudent.placement_round_participant_id is null"
			),
			//'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.placement_round_participant_id'),
			'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.accepted_student_id'),
		)); */

		if (isset($students_to_be_removed) && !empty($students_to_be_removed)) {
			$notAssignedDifference = ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
				'conditions' => array(
					"NOT" => array("PlacementParticipatingStudent.accepted_student_id" => $students_to_be_removed),
					"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
					"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
					"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
					"PlacementParticipatingStudent.placement_round_participant_id is null"
				),
				//'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.placement_round_participant_id'),
				'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.accepted_student_id'),
			));
		} else {
			$notAssignedDifference = ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
				'conditions' => array(
					"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
					"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
					"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
					"PlacementParticipatingStudent.placement_round_participant_id is null"
				),
				//'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.placement_round_participant_id'),
				'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.accepted_student_id'),
			));
		}

		$partcipantsOrderByResultt =  ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => array(
				"OR" => array(
					'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
					'PlacementParticipatingStudent.placement_round_participant_id = ""',
					'PlacementParticipatingStudent.placement_round_participant_id = 0',
				),
				"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementParticipatingStudent.accepted_student_id" => $notAssignedDifference,
				"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],

			),
			'fields' => array(
				'PlacementParticipatingStudent.id',
				'PlacementParticipatingStudent.total_placement_weight',
				'PlacementParticipatingStudent.accepted_student_id'
			),
			'recursive' => -1,
			'order' => array('PlacementParticipatingStudent.total_placement_weight' => 'DESC'),
			'limit' => 100000
		));

		$tmpSort = array_merge($sortOutStudentsResultCategory, $partcipantsOrderByResultt, $withoutPreference);
		$sortOutStudentsResultCategory = $tmpSort;


		// those without preference must be added to in the list
		if (!$lastRound && !empty($students_to_be_removed)) {
			debug($students_to_be_removed);
			$tmp = array();

			if (!empty($sortOutStudentsResultCategory)) {
				foreach ($sortOutStudentsResultCategory as $for_sort_k => $for_sort_v) {
					if (!in_array($for_sort_v['PlacementParticipatingStudent']['accepted_student_id'], $students_to_be_removed)) {
						$tmp[] = $for_sort_v;
					}
				}
			}

			unset($sortOutStudentsResultCategory);
			$sortOutStudentsResultCategory = $tmp;
			debug(count($sortOutStudentsResultCategory));
			unset($tmp);
		}

		return $sortOutStudentsResultCategory;
	}

	function sortOutStudentByPreference($data, $department_id = null)
	{
		// students who completed their preference on time
		$partcipantsOrderByResult =  ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => array(
				"OR" => array(
					'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
					'PlacementParticipatingStudent.placement_round_participant_id = ""',
					'PlacementParticipatingStudent.placement_round_participant_id = 0'
				),
				"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'], 
				"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
			),
			'fields' => array(
				'PlacementParticipatingStudent.id',
				'PlacementParticipatingStudent.total_placement_weight',
				'PlacementParticipatingStudent.accepted_student_id'
			),
			'recursive' => -1,
			'order' => array('PlacementParticipatingStudent.total_placement_weight' => 'DESC'),
			'limit' => 100000
		));

		$partcipantsOrderByResultNew = array();
		$partcipantsOrderByResultNewFirstPreferenceNotRun = array();

		/* $thoseDepartmentAlreadyPlaced = ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
			'conditions' => array(
				'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
				'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
				'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
				'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
				'PlacementParticipatingStudent.placement_round_participant_id is not null',
				'PlacementParticipatingStudent.status' => 1
			),
			'fields' => array('PlacementParticipatingStudent.placement_round_participant_id', 'PlacementParticipatingStudent.placement_round_participant_id')
		)); */

		$students_to_be_sorted = array();

		if (!empty($partcipantsOrderByResult)) {
			foreach ($partcipantsOrderByResult as $k => $pk) {
				$x = ClassRegistry::init('PlacementPreference')->find('first', array(
					'conditions' => array(
						"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
						"PlacementPreference.round" => $data['PlacementSetting']['round'],
						"PlacementPreference.placement_round_participant_id" => $department_id,
						"PlacementPreference.accepted_student_id" => $pk['PlacementParticipatingStudent']['accepted_student_id'],
					),
					'recursive' => -1,
					'order' => array('PlacementPreference.preference_order' => 'ASC'),
				));

				if (isset($x['PlacementPreference']) && !empty($x['PlacementPreference'])) {
					$pk['PlacementPreference'] = $x['PlacementPreference'];
					$partcipantsOrderByResultNew[] = $pk;
				}

				if (!empty($pk['PlacementPreference']['preference_order']) && is_numeric($pk['PlacementPreference']['preference_order']) && $pk['PlacementPreference']['preference_order'] > 1) {
					$previous_preferences = $this->find('all', array(
						'conditions' => array(
							'PlacementPreference.accepted_student_id' => $pk['PlacementPreference']['accepted_student_id'],
							"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
							"PlacementPreference.round" => $data['PlacementSetting']['round'],
							"PlacementPreference.preference_order < " => $pk['PlacementPreference']['preference_order'],
						),
						'recursive' => -1,
						'order' => array('PlacementPreference.preference_order' => 'ASC'),
					));


					$placement_pp_dept_done = 1;

					if (!empty($previous_preferences)) {
						foreach ($previous_preferences as $pp_v) {
							$placed_students_count = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
								'conditions' => array(
									'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
									'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
									'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
									'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
									'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
									'PlacementParticipatingStudent.placement_round_participant_id' => $pp_v['PlacementPreference']['placement_round_participant_id'],
								),
								'recursive' => -1
							));

							if ($placed_students_count == 0 ) {
								$placement_pp_dept_done = 0;
								break;
							}
						}
					}

					if ($placement_pp_dept_done == 1 ) {
						//Sort the student to keep more competitive
						//$students_to_be_sorted[] = $pk;
					} else  if ($placement_pp_dept_done == 0) {
						$students_to_be_removed[] = $pk['PlacementPreference']['accepted_student_id'];
					}
				}
			}
		}

		//$sortOutStudentsResultCategory =  $partcipantsOrderByResultNew;

		/* $notAssignedDifference = ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
			'conditions' => array(
				"NOT" => array("PlacementParticipatingStudent.accepted_student_id" => $students_to_be_removed),
				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
				"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
				"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementParticipatingStudent.placement_round_participant_id is null"
			),
			//'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.placement_round_participant_id'),
			'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.accepted_student_id'),
		));

		$partcipantsOrderByResultt =  ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
			'conditions' => array(
				"OR" => array(
					'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
					'PlacementParticipatingStudent.placement_round_participant_id = ""',
					'PlacementParticipatingStudent.placement_round_participant_id = 0',
				),
				"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementParticipatingStudent.accepted_student_id" => $notAssignedDifference,
				"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
				"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
			),
			'fields' => array(
				'PlacementParticipatingStudent.id',
				'PlacementParticipatingStudent.total_placement_weight',
				'PlacementParticipatingStudent.accepted_student_id'
			),
			'recursive' => -1,
			'order' => array('PlacementParticipatingStudent.total_placement_weight' => 'DESC'),
			'limit' => 100000
		)); */


		if (!empty($partcipantsOrderByResult)) {
			foreach ($partcipantsOrderByResult as $k => $pk) {
				$x = ClassRegistry::init('PlacementPreference')->find('first', array(
					'conditions' => array(
						"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
						"PlacementPreference.round" => $data['PlacementSetting']['round'],
						"PlacementPreference.placement_round_participant_id" => $department_id,
						"PlacementPreference.accepted_student_id" => $pk['PlacementParticipatingStudent']['accepted_student_id'],
					),
					'recursive' => -1,
					'order' => array('PlacementPreference.preference_order' => 'ASC'),
				));

				if (isset($x['PlacementPreference']) && !empty($x['PlacementPreference'])) {
					$pk['PlacementPreference'] = $x['PlacementPreference'];
					$partcipantsOrderByResultNew[] = $pk;
				}
			}
		}

		$sortOutStudentsResultCategory =  $partcipantsOrderByResultNew;
		$resorted = array();

		if (isset($students_to_be_removed) && !empty($students_to_be_removed)) {
			$quoteDefined = ClassRegistry::init('PlacementRoundParticipant')->find('count', array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					"OR" => array(
						'PlacementRoundParticipant.female_quota >' => 0,
						'PlacementRoundParticipant.disability_quota >' => 0,
						'PlacementRoundParticipant.region_quota >' => 0
					)
				),
				'recursive' => -1
			));

			if (!empty($sortOutStudentsResultCategory)) {
				foreach ($sortOutStudentsResultCategory as $for_sort_k => $for_sort_v) {
					if ($quoteDefined) {
						if (!in_array($for_sort_v['PlacementParticipatingStudent']['accepted_student_id'], $students_to_be_removed)) {
							$resorted[] = $for_sort_v;
						}
					} else {
						$propabilityCheck = $this->getPropabilityOfPreviousPreference($data, $department_id, $for_sort_v['PlacementPreference']['accepted_student_id']);
						if (!in_array($for_sort_v['PlacementParticipatingStudent']['accepted_student_id'], $students_to_be_removed) || ($propabilityCheck == 0)) {
							$resorted[] = $for_sort_v;
						}
					}

				}
			}

			unset($sortOutStudentsResultCategory);
			$sortOutStudentsResultCategory = $resorted;
		}

		return $sortOutStudentsResultCategory;
	}

	/*
	* 0 consider in the current department , dont remove it
	* 1 has chance so remove it for other consideration
	*/
	function getPropabilityOfPreviousPreference($data, $current_department_id,$accepted_student_id)
	{
		$currentDepartmentPreferenceofStudent = ClassRegistry::init('PlacementPreference')->find('first', array(
			'conditions' => array(
				"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementPreference.round" => $data['PlacementSetting']['round'],
				"PlacementPreference.placement_round_participant_id" => $current_department_id,
				"PlacementPreference.accepted_student_id" => $accepted_student_id,
			),
			'recursive' => -1,
			'order' => array('PlacementPreference.preference_order' => 'ASC')
		));

		$previous_preference_lists = $this->find('all', array(
			'conditions' => array(
				'PlacementPreference.accepted_student_id' => $accepted_student_id,
				"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
				"PlacementPreference.round" => $data['PlacementSetting']['round'],
				"PlacementPreference.preference_order < " => $currentDepartmentPreferenceofStudent['PlacementPreference']['preference_order']
			),
			'recursive' => -1,
			'order' => array('PlacementPreference.preference_order' => 'ASC')
		));

		if(!empty($previous_preference_lists)){
			foreach ($previous_preference_lists as $prrl => $prrv) {
				//check if placement run already for the iterted department
				$placed_students_count = ClassRegistry::init('PlacementParticipatingStudent')->find('count', array(
					'conditions' => array(
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
						'PlacementParticipatingStudent.placement_round_participant_id' => $prrv['PlacementPreference']['placement_round_participant_id']
					),
					'recursive' => -1
				));

				if($placed_students_count > 0){
					continue;
				}

				$detail_of_participating_unit = ClassRegistry::init('PlacementRoundParticipant')->find('first', array(
					'conditions' => array(
						'PlacementRoundParticipant.id' => $prrv['PlacementPreference']['placement_round_participant_id'],
						'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round'],
						'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					)
				));

				$intake_capacity = $detail_of_participating_unit['PlacementRoundParticipant']['intake_capacity'] + (!empty($detail_of_participating_unit['PlacementRoundParticipant']['female_quota'])? $detail_of_participating_unit['PlacementRoundParticipant']['female_quota'] : 0 ) + (!empty($detail_of_participating_unit['PlacementRoundParticipant']['disability_quota']) ? $detail_of_participating_unit['PlacementRoundParticipant']['disability_quota'] : 0) + (!empty($detail_of_participating_unit['PlacementRoundParticipant']['region_quota'])? $detail_of_participating_unit['PlacementRoundParticipant']['region_quota'] : 0);

				// list of students who are prefered it as first the iterated department
				$those_who_prefered_it_as_first = $this->find('list', array(
					'conditions' => array(
						"PlacementPreference.academic_year" => $data['PlacementSetting']['academic_year'],
						"PlacementPreference.round" => $data['PlacementSetting']['round'],
						"PlacementPreference.placement_round_participant_id" => $prrv['PlacementPreference']['placement_round_participant_id'],
						"PlacementPreference.preference_order <= " => $prrv['PlacementPreference']['preference_order']
					),
					//'fields' => array('PlacementPreference.placement_round_participant_id', 'PlacementPreference.preference_order'),
					'fields' => array('PlacementPreference.accepted_student_id', 'PlacementPreference.accepted_student_id')
				));

				// students who completed their preference on time
				$partcipantsOrderByResult = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
					'conditions' => array(
						"OR" => array(
							'PlacementParticipatingStudent.placement_round_participant_id IS NULL',
							'PlacementParticipatingStudent.placement_round_participant_id = ""',
							'PlacementParticipatingStudent.placement_round_participant_id = 0',
						),
						"PlacementParticipatingStudent.academic_year" => $data['PlacementSetting']['academic_year'],
						"PlacementParticipatingStudent.round" => $data['PlacementSetting']['round'],
						"PlacementParticipatingStudent.applied_for" => $data['PlacementSetting']['applied_for'],
						"PlacementParticipatingStudent.accepted_student_id" => $those_who_prefered_it_as_first
					),
					'fields' => array(
						'PlacementParticipatingStudent.id',
						'PlacementParticipatingStudent.total_placement_weight',
						'PlacementParticipatingStudent.accepted_student_id'
					),
					'recursive' => -1,
					'order' => array('PlacementParticipatingStudent.total_placement_weight' => 'DESC'),
					'limit' => $intake_capacity
				));

				if (!empty($partcipantsOrderByResult)) {
					foreach ($partcipantsOrderByResult as $pr => $pv) {
						if ($pv['PlacementParticipatingStudent']['accepted_student_id'] == $accepted_student_id) {
							// has chance to be placed with other first choice competitors
							return 1;
						}
					}
				}
			}
		}
		return 0;
	}

	function multi_unique($src)
	{
		$output = array_map("unserialize", array_unique(array_map("serialize", $src)));
		return $output;
	}

	/**
	 *Method to adjust privilaged quota
	 *return adjusted value of the privilaged quota
	 */
	function checkAndAdjustPrivilagedQuota($data, $department_id, $adjusted_privilaged_quota = array(), $resevedquote) 
	{
		if (isset($data) && !empty($data)) {
			//$studentPreparednessCondition =	"PlacementPreference.accepted_student_id IN (select accepted_student_id from placement_participating_students where applied_for='" . $data['PlacementSetting']['applied_for'] . "' and  academic_year='" . $data['PlacementSetting']['academic_year'] . "' and round=" . $data['PlacementSetting']['round'] . " and program_id=" . $data['PlacementSetting']['program_id'] . " and program_type_id=" . $data['PlacementSetting']['program_type_id'] . " )";
			/* if (count(explode('c~', $data['PlacementSetting']['applied_for'])) > 1) {
		 	} */
			$studentPreparednessCondition =	"PlacementPreference.accepted_student_id IN (select accepted_student_id from placement_participating_students where applied_for = '" . $data['PlacementSetting']['applied_for'] . "' and  academic_year = '" . $data['PlacementSetting']['academic_year'] . "' and round = " . $data['PlacementSetting']['round'] . " )";
		} else {
			return;
		}

		//get count of participating deparment for the given parameter
		$number_of_participating_department = ClassRegistry::init('PlacementRoundParticipant')->find('count', array(
			'conditions' => array(
				'PlacementRoundParticipant.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
				'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
				'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
				'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
				'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
			)
		));

		// do for the three privilaged
		if (!empty($adjusted_privilaged_quota)) {
			foreach ($adjusted_privilaged_quota as $privilage_type => &$quota) {
				
				$privilagedcondition = null;
				
				if (strcasecmp($privilage_type, "female") == 0) {
					$privilagedcondition = " ( AcceptedStudent.sex='female' or AcceptedStudent.sex='f' ) ";
				} elseif (strcasecmp($privilage_type, "disability") == 0) {
					$privilagedcondition = "AcceptedStudent.disability IS NOT NULL";
				} else {
					$regions = ClassRegistry::init('PlacementRoundParticipant')->find('first', array(
						'conditions' => array(
							'PlacementRoundParticipant.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
							'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
							'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
							'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
							'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
						),
						'recursive' => -1
					));

					if (empty($regions['PlacementRoundParticipant']['developing_region'])) {
						continue;
					}

					$privilagedcondition = "AcceptedStudent.region_id IN (" . $regions['PlacementRoundParticipant']['developing_region'] . ")";
				}

				// iterate each students availabilty against preference order for the given deparment_id
				$sum_available_students_privilaged = 0;
				$list_students_in_x_preference = array();

				if (count($number_of_participating_department) && $quota) {
					// the logic is unkown ?
					for ($i = 1; $i <= $number_of_participating_department; $i++) {
						$list_students_in_x_preference = $this->find('all', array(
							'conditions' => array(
								'PlacementPreference.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
								'PlacementPreference.placement_round_participant_id' => $department_id,
								'PlacementPreference.preference_order' => $i,
								$privilagedcondition,
								$studentPreparednessCondition
							),
							'contain' => array('AcceptedStudent'),
							'fields' => array('PlacementPreference.accepted_student_id', 'PlacementPreference.placement_round_participant_id'),
						));

						// simply count privilaged students in preference 1 for a particular department
						if ($i == 1) {
							$sum_available_students_privilaged += count($list_students_in_x_preference);
							debug($sum_available_students_privilaged);
							debug($quota);
							// if there are enough students by their firstmpreference for allocated quota for the department. no need to continue the loop if there are enough privilaged students in system
							if ($sum_available_students_privilaged >= $quota) {
								break;
							}
							continue;
						}

						// we need to have already allocated departments_id
						$reformat_list_of_department_ids = array();

						$list_of_departments_id = ClassRegistry::init('PlacementParticipatingStudent')->find('all', array(
							'conditions' => array(
								'PlacementParticipatingStudent.academic_year LIKE ' => $data['PlacementSetting']['academic_year'] . '%',
								'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
								'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
								'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
								//'PlacementParticipatingStudent.placement_round_participant_id is not null ',
								//'PlacementParticipatingStudent.placement_round_participant_id not ' => array('', 0)
								'OR' => array(
									'PlacementParticipatingStudent.placement_round_participant_id is not null',
									'PlacementParticipatingStudent.placement_round_participant_id != ""',
									'PlacementParticipatingStudent.placement_round_participant_id != 0',
								)
							),
							'fields' => array('DISTINCT PlacementParticipatingStudent.placement_round_participant_id'),
							'recursive' => -1
						));

						if (!empty($list_of_departments_id)) {
							foreach ($list_of_departments_id  as $key => $value) {
								$reformat_list_of_department_ids[] = $value['PlacementParticipatingStudent']['placement_round_participant_id'];
							}
						}

						$excluded_student_count = 0;
						//per students check for departments assignment and exclude
						if (!empty($list_students_in_x_preference)) {
							foreach ($list_students_in_x_preference as &$student) {
								//check students back preferenc if they are not assigned.
								for ($j = 1; $j < $i; $j++) {
									$department_id_accepted_student = $this->find('first', array(
										'conditions' => array(
											'PlacementPreference.accepted_student_id' => $student['PlacementPreference']['accepted_student_id'],
											'PlacementPreference.preference_order' => $j
										),
										'fields' => array('PlacementPreference.placement_round_participant_id'), 
									));

									// is her/his previous preference selected department was processed?
									if (!empty($reformat_list_of_department_ids) && isset($department_id_accepted_student['PlacementPreference']['placement_round_participant_id']) && is_numeric($department_id_accepted_student['PlacementPreference']['placement_round_participant_id']) && $department_id_accepted_student['PlacementPreference']['placement_round_participant_id'] > 0) {
										if (in_array($department_id_accepted_student['PlacementPreference']['placement_round_participant_id'], $reformat_list_of_department_ids) === false) {
											$excluded_student_count++;
											break;
										}
									}
								}
							}
						}

						$sum_available_students_privilaged += (count($list_students_in_x_preference) - $excluded_student_count);

						if ($sum_available_students_privilaged >= $quota) {
							break;
						}
					}

					if ($sum_available_students_privilaged < $quota) {
						//call function
						$privilaged_quota_gap = ($quota - $sum_available_students_privilaged);
						$quota -= $privilaged_quota_gap;
						$reserved_sum = $resevedquote;
					}
				}
			} // end of the three privilages
		}

		$array_reserved_privilaged_merged[] = $adjusted_privilaged_quota;

		return $array_reserved_privilaged_merged;
	}

	public function getStudentWhoToPrepareForPlacement($data = array())
	{
		debug($data);

		if (isset($data) && !empty($data)) {
			$firstData = ClassRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['PlacementSetting']['round']
				),
				'recursive' => -1
			));

			debug($firstData);
			debug($firstData['PlacementRoundParticipant']['semester']);

			$additionalPoints = ClassRegistry::init('PlacementAdditionalPoint')->find("all", array(
				'conditions' => array(
					'PlacementAdditionalPoint.applied_for' => $data['PlacementSetting']['applied_for'],
					'PlacementAdditionalPoint.program_id' => $data['PlacementSetting']['program_id'],
					'PlacementAdditionalPoint.program_type_id' => $data['PlacementSetting']['program_type_id'],
					'PlacementAdditionalPoint.academic_year' => $data['PlacementSetting']['academic_year'],
					'PlacementAdditionalPoint.round' => $data['PlacementSetting']['round']
				),
				'recursive' => -1
			));

			$points = array();

			if (isset($additionalPoints) && !empty($additionalPoints)) {
				foreach ($additionalPoints as $pk => $pv) {
					$points[$pv['PlacementAdditionalPoint']['type']] = $pv['PlacementAdditionalPoint']['point'];
				}
			}

			debug($additionalPoints);

			$allRoundParticipants = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
				'conditions' => array('PlacementRoundParticipant.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']),
				'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.id')
			));

			//debug($allRoundParticipants);


			if (isset($data['PlacementSetting']['limit']) && !empty($data['PlacementSetting']['limit'])) {
				$limit = $data['PlacementSetting']['limit'];
			} else {
				$limit = 5000;
			}

			$student_ids_that_have_exam_result_entries_for_the_round = array();

			if ($data['PlacementSetting']['with_entrance'] == 1) {
				$student_ids_that_have_exam_result_entries_for_the_round = ClassRegistry::init('PlacementEntranceExamResultEntry')->find('list', array(
					'conditions' => array(
						'PlacementEntranceExamResultEntry.placement_round_participant_id' => $allRoundParticipants,
					),
					'group' => array('PlacementEntranceExamResultEntry.accepted_student_id', 'PlacementEntranceExamResultEntry.student_id'),
					'fields' => array('PlacementEntranceExamResultEntry.accepted_student_id', 'PlacementEntranceExamResultEntry.student_id'),
				));
			}

			debug($student_ids_that_have_exam_result_entries_for_the_round);

			
			if ($data['PlacementSetting']['include'] == 0) {

				$already_prepared_students_for_the_round =  ClassRegistry::init('PlacementParticipatingStudent')->find('list', array(
					'conditions' => array(
						'PlacementParticipatingStudent.applied_for' => $data['PlacementSetting']['applied_for'],
						'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
						'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
						'PlacementParticipatingStudent.program_type_id' =>$data['PlacementSetting']['program_type_id']
					),
					'fields' => array('PlacementParticipatingStudent.accepted_student_id', 'PlacementParticipatingStudent.student_id'),
				));

				debug(count($already_prepared_students_for_the_round));

				if ($data['PlacementSetting']['with_entrance'] == 1 && !empty($student_ids_that_have_exam_result_entries_for_the_round)) {
					$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementPreference')->find('all', array(
						'conditions' => array(
							'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,
							'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
							'PlacementPreference.round' => $data['PlacementSetting']['round'],
							'PlacementPreference.student_id' => $student_ids_that_have_exam_result_entries_for_the_round,
							'NOT' => array(
								'PlacementPreference.accepted_student_id' =>  array_keys($already_prepared_students_for_the_round),
								//'PlacementPreference.student_id' => $already_prepared_students_for_the_round
							),
						),
						'contain' => array(
							//'PlacementRoundParticipant', 
							'Student' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name',  */'gender', 'full_name', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'college_id', 'department_id')), 
							'AcceptedStudent' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'sex', 'full_name', 'EHEECE_total_results', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'region_id', 'college_id', 'department_id', 'disability')), 
						),
						'group' => array('PlacementPreference.accepted_student_id', 'PlacementPreference.student_id', 'PlacementPreference.academic_year', 'PlacementPreference.round'),
						'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC', 'PlacementPreference.preference_order' => 'ASC'),
						'limit' => $limit,
						'maxLimit' => $limit
					));

				} else {

					$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementPreference')->find('all', array(
						'conditions' => array(
							'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,
							'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
							'PlacementPreference.round' => $data['PlacementSetting']['round'],
							'NOT' => array(
								//'PlacementPreference.accepted_student_id' =>  array_keys($already_prepared_students_for_the_round),
								'PlacementPreference.student_id' => $already_prepared_students_for_the_round
							),
						),
						'contain' => array(
							//'PlacementRoundParticipant', 
							'Student' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'gender', 'full_name', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'college_id', 'department_id')), 
							'AcceptedStudent' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'sex', 'full_name', 'EHEECE_total_results', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'region_id', 'college_id', 'department_id', 'disability')), 
						),
						'group' => array('PlacementPreference.accepted_student_id', 'PlacementPreference.student_id', 'PlacementPreference.academic_year', 'PlacementPreference.round'),
						'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC', 'PlacementPreference.preference_order' => 'ASC'),
						'limit' => $limit,
						'maxLimit' => $limit
					));
				}

			} else {

				if ($data['PlacementSetting']['with_entrance'] == 1 && !empty($student_ids_that_have_exam_result_entries_for_the_round)) {
					$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementPreference')->find('all', array(
						'conditions' => array(
							'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,
							'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
							'PlacementPreference.round' => $data['PlacementSetting']['round'],
							'PlacementPreference.student_id' => $student_ids_that_have_exam_result_entries_for_the_round,
							/* 'NOT' => array(
								'PlacementPreference.accepted_student_id' =>  array_keys($already_prepared_students_for_the_round),
								//'PlacementPreference.student_id' => $already_prepared_students_for_the_round
							), */
						),
						'contain' => array(
							//'PlacementRoundParticipant', 
							'Student' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'gender', 'full_name', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'college_id', 'department_id')), 
							'AcceptedStudent' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'sex', 'full_name', 'EHEECE_total_results', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'region_id', 'college_id', 'department_id', 'disability')), 
						),
						'group' => array('PlacementPreference.accepted_student_id', 'PlacementPreference.student_id', 'PlacementPreference.academic_year', 'PlacementPreference.round'),
						'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC', 'PlacementPreference.preference_order' => 'ASC'),
						'limit' => $limit,
						'maxLimit' => $limit
					));

				} else {

					$allStudentsWhoEntranceExam =  ClassRegistry::init('PlacementPreference')->find('all', array(
						'conditions' => array(
							'PlacementPreference.placement_round_participant_id' => $allRoundParticipants,
							'PlacementPreference.academic_year' => $data['PlacementSetting']['academic_year'],
							'PlacementPreference.round' => $data['PlacementSetting']['round']
						),
						'contain' => array(
							//'PlacementRoundParticipant', 
							'Student' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'gender', 'full_name', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'college_id', 'department_id')), 
							'AcceptedStudent' => array('fields' => array('id', /* 'first_name', 'middle_name', 'last_name', */ 'sex', 'full_name', 'EHEECE_total_results', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'region_id', 'college_id', 'department_id', 'disability')), 
						),
						'group' => array('PlacementPreference.accepted_student_id', 'PlacementPreference.student_id', 'PlacementPreference.academic_year', 'PlacementPreference.round'),
						'order' => array('Student.id' => 'ASC', 'Student.program_id' => 'ASC', 'Student.program_type_id' => 'ASC', 'PlacementPreference.preference_order' => 'ASC'),
						'limit' => $limit,
						'maxLimit' => $limit
					));
				}

			}

			//debug($allStudentsWhoEntranceExam);

			debug(count($allStudentsWhoEntranceExam));


			if (!empty($allStudentsWhoEntranceExam)) {

				$selected_program_name = classRegistry::init('Program')->field('Program.name', array('Program.id' => $allStudentsWhoEntranceExam[0]['Student']['program_id']));
				$selected_program_type_name = classRegistry::init('ProgramType')->field('ProgramType.name', array('ProgramType.id' => $allStudentsWhoEntranceExam[0]['Student']['program_type_id']));

				if (!isset($allStudentsWhoEntranceExam[0]['Student']['department_id'])) {
					$selected_applied_unit_name = classRegistry::init('College')->field('College.name', array('College.id' => $allStudentsWhoEntranceExam[0]['Student']['college_id']));
				} else {
					$selected_applied_unit_name = classRegistry::init('Department')->field('Department.name', array('Department.id' => $allStudentsWhoEntranceExam[0]['Student']['department_id']));
				}


				$placementResultSettings = ClassRegistry::init('PlacementResultSetting')->find('all', array('conditions' => array('PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']), 'recursive' => -1));
				debug($placementResultSettings);

				$freshman_settings = ClassRegistry::init('PlacementResultSetting')->find('first', array(
					'conditions' => array(
						'PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
						'PlacementResultSetting.result_type' => 'freshman_result'
					)
				));

				debug($freshman_settings);

				$prepartory_settings = ClassRegistry::init('PlacementResultSetting')->find('first', array(
					'conditions' => array(
						'PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
						'PlacementResultSetting.result_type' => 'EHEECE_total_results'
					)
				));

				//debug($prepartory_settings);

				$entrance_settings = ClassRegistry::init('PlacementResultSetting')->find('first', array(
					'conditions' => array(
						'PlacementResultSetting.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
						'PlacementResultSetting.result_type' => 'entrance_result'
					)
				));

				debug($entrance_settings);

				$region_ids = array();

				if (isset($firstData['PlacementRoundParticipant']['developing_region']) && !empty($firstData['PlacementRoundParticipant']['developing_region'])) {
					$region_ids = explode(',', $firstData['PlacementRoundParticipant']['developing_region']);
				}

				debug($region_ids);

				// No placement setting is defined, default placement settings from smis.conf file are being used.

				if ((empty($freshman_settings['PlacementResultSetting']) && empty($prepartory_settings['PlacementResultSetting']) && empty($entrance_settings['PlacementResultSetting'])) || empty($placementResultSettings)) {
					/* $this->invalidate('NO_PLACEMENT_SETTING_FOUND', 'No Placement Setting is found for the selected search criteria. Please define it first.');
					return false; */
					$error1 = 'No placement setting is defined for round ' . $data['PlacementSetting']['round'] . ' of '  . $selected_program_name .  ' - ' . $selected_program_type_name .  ' in ' . $selected_applied_unit_name .' This is for view only and it is generated with default placement settings(' . DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT . '% for Freshman CGPA out of 4.00, ' . DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT. '% for Preparatory EHEECE total results out of 700 and ' . DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT . '% for Department Entrance Exam out of 30) . Please define Placement Setting first and try to Prepare.';
					$this->invalidate('NO_PLACEMENT_SETTING_FOUND', $error1);
					//return false;
				}

				foreach ($allStudentsWhoEntranceExam as $p => &$v) {
					$alreadyPrepared = ClassRegistry::init('PlacementParticipatingStudent')->find('first', array(
						'conditions' => array(
							'PlacementParticipatingStudent.accepted_student_id' => $v['AcceptedStudent']['id'],
							'PlacementParticipatingStudent.student_id' => $v['Student']['id'],
							'PlacementParticipatingStudent.program_id' => $data['PlacementSetting']['program_id'],
							'PlacementParticipatingStudent.program_type_id' => $data['PlacementSetting']['program_type_id'],
							'PlacementParticipatingStudent.academic_year' => $data['PlacementSetting']['academic_year'],
							'PlacementParticipatingStudent.round' => $data['PlacementSetting']['round'],
						),
						'recursive' => -1
					));

					/* if ($data['PlacementSetting']['include'] == 0 && isset($alreadyPrepared) && !empty($alreadyPrepared)) {
						unset($allStudentsWhoEntranceExam[$p]);
						continue;
					} */

					$prep = 0;
					$fresh = 0;
					$entrance = 0;
					$female_placement_weight = 0;
					$disability_weight = 0;
					$developing_region_weight = 0;
					$freshmanResult = 0.0;
					$disability_weight = 0;

					if (isset($firstData['PlacementRoundParticipant']['semester']) && !empty($firstData['PlacementRoundParticipant']['semester'])) {
						$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
							'conditions' => array(
								'StudentExamStatus.student_id' => $v['Student']['id'],
								'StudentExamStatus.academic_year' => $data['PlacementSetting']['academic_year'],
								'StudentExamStatus.semester' => $firstData['PlacementRoundParticipant']['semester'],
							),
							'contain' => array(
								'AcademicStatus' => array(
									'fields' => array('AcademicStatus.id', 'AcademicStatus.name')
								)
							),
							'fields' => array(
								'StudentExamStatus.academic_status_id',
								'StudentExamStatus.sgpa',
								'StudentExamStatus.cgpa'
							),
							'group' => array(
								'StudentExamStatus.student_id',
								'StudentExamStatus.semester',
								'StudentExamStatus.academic_year',
							),
							'order' => array('StudentExamStatus.id' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
							'recursive' => -1
						));
						//debug($freshManresult);
					} else {
						if ($data['PlacementSetting']['round'] == 1) {
							$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
								'conditions' => array(
									'StudentExamStatus.student_id' => $v['Student']['id'],
									'StudentExamStatus.academic_year' => $data['PlacementSetting']['academic_year'],
									'StudentExamStatus.semester' => 'I',
									//'StudentExamStatus.academic_status_id <> 4',
								),
								'contain' => array(
									'AcademicStatus' => array(
										'fields' => array('AcademicStatus.id', 'AcademicStatus.name')
									)
								),
								'fields' => array(
									'StudentExamStatus.academic_status_id',
									'StudentExamStatus.sgpa',
									'StudentExamStatus.cgpa'
								),
								'group' => array(
									'StudentExamStatus.student_id',
									'StudentExamStatus.semester',
									'StudentExamStatus.academic_year',
								),
								'order' => array('StudentExamStatus.id' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
								'recursive' => -1
							));
							debug($freshManresult);
						} else {
							$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
								'conditions' => array(
									'StudentExamStatus.student_id' => $v['Student']['id'],
									'StudentExamStatus.academic_year' => $data['PlacementSetting']['academic_year'],
									'StudentExamStatus.semester' => 'II',
									//'StudentExamStatus.academic_status_id <> 4',
								),
								'contain' => array(
									'AcademicStatus' => array(
										'fields' => array('AcademicStatus.id', 'AcademicStatus.name')
									)
								),
								'fields' => array(
									'StudentExamStatus.academic_status_id',
									'StudentExamStatus.sgpa',
									'StudentExamStatus.cgpa'
								),
								'group' => array(
									'StudentExamStatus.student_id',
									'StudentExamStatus.semester',
									'StudentExamStatus.academic_year',
								),
								'order' => array('StudentExamStatus.id' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
								'recursive' => -1
							));
							debug($freshManresult);
						}
					}


					// Add a condition to check status and academic status here, Neway
					if (isset($freshManresult['AcademicStatus']['name'])) {
						$v['Student']['academic_status'] = $freshManresult['AcademicStatus']['name'];
						$v['Student']['academic_status_id'] = $freshManresult['AcademicStatus']['id'];
					} else {
						$v['Student']['academic_status'] = null;
					}

					// Add a condition to check status here, Neway
					if (isset($freshManresult['StudentExamStatus']['cgpa'])) {
						$v['Student']['cgpa'] = $freshManresult['StudentExamStatus']['cgpa'];
					} else {
						$v['Student']['cgpa'] = null;
					}

					if ($firstData['PlacementRoundParticipant']['require_cgpa'] == 1) {	
						if (!isset($freshManresult['StudentExamStatus']['cgpa']) || !is_numeric($freshManresult['StudentExamStatus']['cgpa'])) {
							unset($allStudentsWhoEntranceExam[$p]);
							continue;
						} else if (isset($freshManresult['StudentExamStatus']['academic_status_id']) && $freshManresult['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
							unset($allStudentsWhoEntranceExam[$p]);
							continue;
						} else if (is_numeric($freshManresult['StudentExamStatus']['cgpa']) && $freshManresult['StudentExamStatus']['cgpa'] < DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
							unset($allStudentsWhoEntranceExam[$p]);
							continue;
						} else {
							if (isset($firstData['PlacementRoundParticipant']['minimum_cgpa']) && !isset($firstData['PlacementRoundParticipant']['maximum_cgpa'])) {
								if ($freshManresult['StudentExamStatus']['cgpa'] < $firstData['PlacementRoundParticipant']['minimum_cgpa']) {
									unset($allStudentsWhoEntranceExam[$p]);
									continue;
								}
							} else if (isset($firstData['PlacementRoundParticipant']['minimum_cgpa']) && isset($firstData['PlacementRoundParticipant']['maximum_cgpa'])) {
								if ($freshManresult['StudentExamStatus']['cgpa'] < $firstData['PlacementRoundParticipant']['minimum_cgpa'] || $freshManresult['StudentExamStatus']['cgpa'] > $firstData['PlacementRoundParticipant']['maximum_cgpa']) {
									unset($allStudentsWhoEntranceExam[$p]);
									continue;
								}
							}
						}
					}

					if (isset($freshManresult['StudentExamStatus']['academic_status_id']) && !empty($freshManresult['StudentExamStatus']['academic_status_id']) && $freshManresult['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
						unset($allStudentsWhoEntranceExam[$p]);
						continue;
					} else if (empty($freshManresult['StudentExamStatus']['academic_status_id'])) {
						unset($allStudentsWhoEntranceExam[$p]);
						continue;
					} else if (isset($freshManresult['StudentExamStatus']['academic_status_id']) && !empty($freshManresult['StudentExamStatus']['academic_status_id'])) {
						//unset($allStudentsWhoEntranceExam[$p]);
						//continue;
					}

					if (isset($freshManresult['StudentExamStatus']['cgpa']) && !empty($freshManresult['StudentExamStatus']['cgpa'])) {
						$freshmanResult = $freshManresult['StudentExamStatus']['cgpa'];
					}

					
					

					if (isset($entrance_settings['PlacementResultSetting']['percent']) && !empty($entrance_settings['PlacementResultSetting']['percent']) && isset($v['PlacementEntranceExamResultEntry']['result'])) {

						$entranceExamResult = ClassRegistry::init('PlacementEntranceExamResultEntry')->find('first', array(
							'conditions' => array(
								'OR' => array(
									'PlacementEntranceExamResultEntry.accepted_student_id' => $v['AcceptedStudent']['id'],
									'PlacementEntranceExamResultEntry.student_id' => $v['Student']['id'],
								),
								'PlacementEntranceExamResultEntry.placement_round_participant_id' => $allRoundParticipants,
							),
							'group' => array('PlacementEntranceExamResultEntry.accepted_student_id', 'PlacementEntranceExamResultEntry.student_id'),
							'order' => array('PlacementEntranceExamResultEntry.result' => 'DESC'),
							'recursive' => -1
						));
	
						debug($entranceExamResult['PlacementEntranceExamResultEntry']['result']);

						if (isset($entranceExamResult['PlacementEntranceExamResultEntry']['result']) && !empty($entranceExamResult['PlacementEntranceExamResultEntry']['result'])) {
							if (isset($entrance_settings['PlacementResultSetting']['max_result']) && $entrance_settings['PlacementResultSetting']['max_result'] <= ENTRANCEMAXIMUM && $entrance_settings['PlacementResultSetting']['max_result'] >= 0 ) {
								$entrance = ($entrance_settings['PlacementResultSetting']['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / $entrance_settings['PlacementResultSetting']['max_result'];
							} else {
								$entrance = ($entrance_settings['PlacementResultSetting']['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
							}
						}
					}

					if (isset($prepartory_settings['PlacementResultSetting']['percent']) && !empty($prepartory_settings['PlacementResultSetting']['percent'])) {
						// check here ignores readmitted students we can use academicyear from placement preferences table for applied student
						if ($data['PlacementSetting']['academic_year'] == $v['AcceptedStudent']['academicyear']) {
							if (isset($prepartory_settings['PlacementResultSetting']['max_result']) && $prepartory_settings['PlacementResultSetting']['max_result'] <= PREPARATORYMAXIMUM && $prepartory_settings['PlacementResultSetting']['max_result'] >= 0 ) {
								$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / $prepartory_settings['PlacementResultSetting']['max_result'];
							} else {
								if (in_array($v['AcceptedStudent']['college_id'], Configure::read('social_stream_college_ids'))) {
									$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / SOCIAL_STREAM_PREPARATORY_MAXIMUM;
								} else if (in_array($v['AcceptedStudent']['college_id'], Configure::read('natural_stream_college_ids'))) {
									$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / NATURAL_STREAM_PREPARATORY_MAXIMUM;
								} else {
									$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
								}
							}
						} else {
							if (in_array($v['AcceptedStudent']['college_id'], Configure::read('social_stream_college_ids'))) {
								$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / SOCIAL_STREAM_PREPARATORY_MAXIMUM;
							} else if (in_array($v['AcceptedStudent']['college_id'], Configure::read('natural_stream_college_ids'))) {
								$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / NATURAL_STREAM_PREPARATORY_MAXIMUM;
							} else {
								$prep = ($prepartory_settings['PlacementResultSetting']['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
							}
						}
					}

					if (isset($freshman_settings['PlacementResultSetting']['percent']) && !empty($freshman_settings['PlacementResultSetting']['percent'])) {
						if (isset($freshman_settings['PlacementResultSetting']['max_result']) && $freshman_settings['PlacementResultSetting']['max_result'] <= FRESHMANMAXIMUM && $freshman_settings['PlacementResultSetting']['max_result'] >= 0) {
							$fresh = ($freshman_settings['PlacementResultSetting']['percent']  * $freshmanResult) / $freshman_settings['PlacementResultSetting']['max_result'];
						} else {
							$fresh = ($freshman_settings['PlacementResultSetting']['percent']  * $freshmanResult) / FRESHMANMAXIMUM;
						}
					} 
					
					// No placement setting is defined, default placement settings from smis.conf file are being used.

					if (empty($freshman_settings['PlacementResultSetting']) && empty($prepartory_settings['PlacementResultSetting']) && empty($entrance_settings['PlacementResultSetting']) ) {
						
						if (isset($v['Student']['cgpa']) && $v['Student']['cgpa'] > 0 ) {
							$fresh = (DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT  * $v['Student']['cgpa']) / FRESHMANMAXIMUM;
						}

						if (isset($v['AcceptedStudent']['EHEECE_total_results']) && $v['AcceptedStudent']['EHEECE_total_results'] > 100 ) {

							if (in_array($v['AcceptedStudent']['college_id'], Configure::read('social_stream_college_ids'))) {
								$prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / SOCIAL_STREAM_PREPARATORY_MAXIMUM;
							} else if (in_array($v['AcceptedStudent']['college_id'], Configure::read('natural_stream_college_ids'))) {
								$prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / NATURAL_STREAM_PREPARATORY_MAXIMUM;
							} else {
								$prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
							}
						}

						if (isset($v['PlacementEntranceExamResultEntry']['result']) && $v['PlacementEntranceExamResultEntry']['result'] >= 0) {
							$entrance = (DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT  * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
						}
					}

					if (isset($v['AcceptedStudent']['sex']) && !empty($v['AcceptedStudent']['sex']) && (strcasecmp($v['AcceptedStudent']['sex'], "female") == 0 || strcasecmp($v['AcceptedStudent']['sex'], "f") == 0)) {
						if (isset($points['female']) && !empty($points['female'])) {
							$female_placement_weight = $points['female'];
						} else if (is_numeric(INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT) && INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT == 1) {
							$female_placement_weight = DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT;
						} else {
							$female_placement_weight = 0;
						}
					}

					$v['PlacementParticipatingStudent']['female_placement_weight'] = $female_placement_weight;

					if (isset($v['AcceptedStudent']['disability']) && !empty($v['AcceptedStudent']['disability'])) {
						$disability_weight = 5;
					}

					$v['PlacementParticipatingStudent']['disability_weight'] = $disability_weight;

					if (isset($v['AcceptedStudent']['region_id']) && !empty($v['AcceptedStudent']['region_id']) && in_array($v['AcceptedStudent']['region_id'], $region_ids)) {
						//$developing_region_weight = 0;
						if (isset($v['AcceptedStudent']['sex']) && !empty($v['AcceptedStudent']['sex']) && (strcasecmp($v['AcceptedStudent']['sex'], "female") == 0 || strcasecmp($v['AcceptedStudent']['sex'], "f") == 0)) {
							$developing_region_weight = 5;
						} else if (isset($v['AcceptedStudent']['disability']) && !empty($v['AcceptedStudent']['disability'])) {
							$developing_region_weight = 10;
						}
					}

					$v['PlacementParticipatingStudent']['developing_region_weight'] = $developing_region_weight;
					$v['PlacementParticipatingStudent']['result_weight'] = round(($prep + $fresh + $entrance), 2);

					$v['PlacementParticipatingStudent']['prepartory'] = round($prep, 2);
					$v['PlacementParticipatingStudent']['entrance'] = $entrance;
					$v['PlacementParticipatingStudent']['gpa'] = round($fresh, 2);

					$v['PlacementParticipatingStudent']['academic_year'] = $data['PlacementSetting']['academic_year'];
					$v['PlacementParticipatingStudent']['applied_for'] = $data['PlacementSetting']['applied_for'];

					$v['PlacementParticipatingStudent']['round'] =  $data['PlacementSetting']['round'];
					$v['PlacementParticipatingStudent']['program_id'] =  $data['PlacementSetting']['program_id'];
					$v['PlacementParticipatingStudent']['program_type_id'] =  $data['PlacementSetting']['program_type_id'];

					$v['PlacementParticipatingStudent']['total_weight'] = round(($v['PlacementParticipatingStudent']['developing_region_weight'] + $v['PlacementParticipatingStudent']['disability_weight'] + $v['PlacementParticipatingStudent']['female_placement_weight'] + $v['PlacementParticipatingStudent']['result_weight']), 2);
					$v['PlacementParticipatingStudent']['total_placement_weight'] = 	round(($v['PlacementParticipatingStudent']['developing_region_weight'] + $v['PlacementParticipatingStudent']['disability_weight'] + $v['PlacementParticipatingStudent']['female_placement_weight'] + $v['PlacementParticipatingStudent']['result_weight']), 2);


					if (isset($alreadyPrepared) && !empty($alreadyPrepared)) {
						//$v['PlacementParticipatingStudent'] = $alreadyPrepared['PlacementParticipatingStudent'];
						$v['PlacementParticipatingStudent']['id'] = $alreadyPrepared['PlacementParticipatingStudent']['id'];
					}
				}

				if (!empty($allStudentsWhoEntranceExam)) {
					usort($allStudentsWhoEntranceExam, array($this, "cmp"));
					debug($allStudentsWhoEntranceExam[0]);
					return $allStudentsWhoEntranceExam;
				}
			}
		}

		return array();
	}


	function cmp($a, $b)
	{
		// return strcmp($a['PlacementParticipatingStudent']['total_weight'], $b['PlacementParticipatingStudent']['total_weight']); 
		if (isset($a['PlacementParticipatingStudent']['total_weight']) && isset($b['PlacementParticipatingStudent']['total_weight']) && $a['PlacementParticipatingStudent']['total_weight'] == $b['PlacementParticipatingStudent']['total_weight']) {
			return 0;
		}
		return ((isset($a['PlacementParticipatingStudent']['total_weight']) && isset($b['PlacementParticipatingStudent']['total_weight']) && $a['PlacementParticipatingStudent']['total_weight'] < $b['PlacementParticipatingStudent']['total_weight']) ? 1 : -1);
	}

	function compareweight($x, $y)
	{
		if ($x['weight'] < $y['weight']) {
			return true;
		} else {
			return false;
		}
	}

	function cmpTotalPlacementWeight($a, $b)
	{
		/* if ($a['PlacementEntranceExamResultEntry']['total_weight'] == $b['PlacementEntranceExamResultEntry']['total_weight']) {
			return 0;
		}
		return $a['PlacementEntranceExamResultEntry']['total_weight'] < $b['PlacementEntranceExamResultEntry']['total_weight'] ? 1 : -1; */
	}

	public function get_defined_list_of_applied_for($data = null, $curr_acy= null) 
	{

		if (isset($curr_acy) && !empty($curr_acy)) {
			$placementRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('list', array(
				'conditions' => array(
					'PlacementRoundParticipant.program_id' => 1,
					'PlacementRoundParticipant.program_type_id' => 1,
					'PlacementRoundParticipant.academic_year' => $curr_acy,
					'PlacementRoundParticipant.placement_round' => 1,
				),
				'fields' => array('PlacementRoundParticipant.applied_for')
			));
		} else if (!empty($data)) {
			//debug($data);
			$placementRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('list', array(
				'conditions' => array(
					'PlacementRoundParticipant.program_id' => $data['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $data['academic_year'],
					'PlacementRoundParticipant.placement_round' => (isset($data['round']) ? $data['round'] : (isset($data['placement_round']) ? $data['placement_round'] : 1 )),
				),
				'fields' => array('PlacementRoundParticipant.applied_for')
			));
		} else {
			$placementRoundParticipants = classRegistry::init('PlacementRoundParticipant')->find('list', array('fields' => array('PlacementRoundParticipant.applied_for')));
		}

		$dept_ids = array();
		$coll_ids = array();
		$appliedForList = array();

		if (!empty($placementRoundParticipants)) {
			$prtpnt = array_values(array_unique(array_values($placementRoundParticipants)));
			if (!empty($prtpnt)) {
				foreach ($prtpnt as $prk => $prval) {
					if(explode('~', $prval)[0] == 'd') {
						array_push($dept_ids, explode('~', $prval)[1]);
					}
					if(explode('~', $prval)[0] == 'c') {
						array_push($coll_ids, explode('~', $prval)[1]);
					}
				}
			}
		}

		if (!empty($coll_ids)) {
			$colls = classRegistry::init('College')->find('list', array('conditions' => array('College.id' => $coll_ids, 'College.active' => 1)));
			if (!empty($colls)) {
				foreach ($colls as $colkey => $colval) {
					$appliedForList[$colval]['c~' . $colkey] = 'All ' . $colval;
				}
			}
		}

		if (!empty($dept_ids)) {
			$depts = classRegistry::init('Department')->find('all', array(
				'conditions' => array(
					'Department.id' => $dept_ids, 
					'Department.active' => 1
				), 
				'contain' => array(
					'College' => array(
						'fields' => array('College.name')
					)
				),
				'recursive' => -1
			));

			if (!empty($depts)) {
				foreach ($depts as $deptkey => $deptval) {
					$appliedForList[$deptval['College']['name']]['d~' . $deptval['Department']['id']] = $deptval['Department']['name'];
				}
			}
		}

		return $appliedForList;
	}
}