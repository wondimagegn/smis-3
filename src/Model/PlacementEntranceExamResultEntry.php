<?php
class PlacementEntranceExamResultEntry extends AppModel
{
	public $validate = array(
		'accepted_student_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'student_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'result' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'placement_round_participant_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

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
		)
	);

	public function get_selected_section($data)
	{
		$appliedUnitClg = explode('c~', $data['Search']['applied_for']);

		if (!isset($appliedUnitClg[1])) {
			$appliedUnitDept = explode('d~', $data['Search']['applied_for']);
		}

		$currentUnitClg = explode('c~', $data['Search']['current_unit']);

		if (!isset($currentUnitClg[1])) {
			$currentUnitDept = explode('d~', $data['Search']['current_unit']);
		}

		if (isset($data['Search']['applied_for']) && !empty($data['Search']['applied_for'])) {

			$options = array(
				'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
				'contain' => array(
					'YearLevel', 
					'College', 
					'Department'
				),
				'recursive' => -1
			);
			
			if (isset($currentUnitClg[1]) && !empty($currentUnitClg[1])) {
				$options['conditions'][] = array(
					'Section.college_id' => $currentUnitClg[1],
					'Section.department_id is null'
				);
			} else if (isset($currentUnitDept[1]) && !empty($currentUnitDept[1])) {
				$options['conditions'][] = array('Section.department_id' => $currentUnitDept[1]);
			} else {
				if (isset($appliedUnitClg[1]) && !empty($appliedUnitClg[1])) {
					$options['conditions'][] = array(
						'Section.college_id' => $appliedUnitClg[1],
						'Section.department_id is null'
					);
				} else if (isset($appliedUnitDept[1]) && !empty($appliedUnitDept[1])) {
					$options['conditions'][] = array('Section.department_id' => $appliedUnitDept[1]);
				}
			}

			$options['conditions'][] = array('Section.program_id' => $data['Search']['program_id']);
			$options['conditions'][] = array('Section.program_type_id' => $data['Search']['program_type_id']);
			$options['conditions'][] = array('Section.academicyear' => $data['Search']['academic_year']);

		}
		//debug($options);

		if (isset($options) && !empty($options)) {
			$sections = ClassRegistry::init('Section')->find('all', $options);
		} else {
			$sections = array();
		}

		//$sections = ClassRegistry::init('Section')->find('all', $options);
		$sectionF = array();

		if (!empty($sections)) {
			foreach ($sections as $k => $v) {
				//check if there is students in the section
				$studentCount = ClassRegistry::init('StudentsSection')->find('count', array('conditions' => array('StudentsSection.section_id' => $v['Section']['id'], 'StudentsSection.archive' => 0)));
				if ($studentCount) {
					if (!isset($v['YearLevel']['name'])) {
						$sectionF['Pre/1st'][$v['Section']['id']] = $v['Section']['name'];
						//$sectionF['1st']['A'] = 'All';
						//array_unshift($sectionF['1st'], 'All');
					} else {
						$sectionF[$v['YearLevel']['name']][$v['Section']['id']] = $v['Section']['name'];
						//array_unshift($sectionF[$v['YearLevel']['name']], 'All');
						//$sectionF[$v['YearLevel']['name']]['A'] = 'All';
					}
				}
			}
		}

		$sec = array();

		if (!empty($sectionF)) {
			foreach ($sectionF as $k => $kv) {
				//array_unshift($kv, 'All');
				$arr[0] = "All";
				$sec[$k] = $arr + $kv;
			}
		}
		//array_unshift($sectionF, 'All');
		//$sectionF['All']['All'] = 'All';
		return $sec;
		//return $sectionF;
	}

	public function get_selected_student($data)
	{
		//debug($data);
		$processedStudents = array();

		$appliedUnitClg = explode('c~', $data['Search']['applied_for']);
		$appliedUnitDpt = explode('d~', $data['Search']['applied_for']);

		if (!isset($appliedUnitClg[1])) {
			$appliedUnitDept = explode('d~', $data['Search']['applied_for']);
			if (isset($appliedUnitDept[1]) && !empty($appliedUnitDept[1])) {
				$foreignKey = $appliedUnitDept[1];
			}
		} else if (isset($appliedUnitClg[1]) && !empty($appliedUnitClg[1])) {
			$foreignKey = $appliedUnitClg[1];
		}

		if (isset($data['Search']['placement_round_participant_id']) && !empty($data['Search']['placement_round_participant_id'])) {

			$placementRoundParticipantSelected = ClassRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.id' => $data['Search']['placement_round_participant_id'],
					/*
					'PlacementRoundParticipant.applied_for' => $data['Search']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['Search']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['Search']['program_type_id'],
					'PlacementRoundParticipant.foreign_key' => $foreignKey,
					'PlacementRoundParticipant.academic_year' => $data['Search']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['Search']['placement_round'],
					*/
				),
				'recursive' => -1
			));

			//placement_round_participant_id
			//debug($foreignKey);
			//debug($placementRoundParticipantSelected);

			$isPlacementDone = ClassRegistry::init('PlacementParticipatingStudent')->find("count", array(
				'conditions' => array(
					'PlacementParticipatingStudent.placement_round_participant_id' => $placementRoundParticipantSelected['PlacementRoundParticipant']['id'],
				),
				'recursive' => -1
			));

			//debug($isPlacementDone);

			$options = array(
				'contain' => array(
					'Student' => array('order' => 'Student.first_name ASC'), 
					'Section'
				),
				'recursive' => -1
			);

			//debug($data);

			if ($data['Search']['section_id'] == 0) {
				//get_all_section_ids
				$options['conditions'][] = array('StudentsSection.section_id' => $this->get_all_section_ids($data));
			} else {
				$options['conditions'][] = array('StudentsSection.section_id' => $data['Search']['section_id']);
			}

			$options['conditions'][] = array(
				'StudentsSection.archive' => 0,
			);

			$students = ClassRegistry::init('StudentsSection')->find('all', $options);
			//$processedStudents = array();
			$count = 0;

			if (!empty($students)) {
				foreach ($students as $k => $v) {
					// find the result if exists
					$resultExisted = $this->find('first', array(
						'conditions' => array(
							'PlacementEntranceExamResultEntry.placement_round_participant_id' => $data['Search']['placement_round_participant_id'],
							'PlacementEntranceExamResultEntry.student_id' => $v['Student']['id'],
							'PlacementEntranceExamResultEntry.accepted_student_id' => $v['Student']['accepted_student_id']
						),
						'recursive' => -1
					));

					$processedStudents[$count]['Student'] = $v['Student'];
					$processedStudents[$count]['Student']['placement_round_participant_id'] = $data['Search']['placement_round_participant_id'];

					if (isset($resultExisted['PlacementEntranceExamResultEntry']) && !empty($resultExisted['PlacementEntranceExamResultEntry'])) {
						$processedStudents[$count]['EntranceResult'] = $resultExisted['PlacementEntranceExamResultEntry'];
					}

					$processedStudents[$count]['PlacementStatus'] = $isPlacementDone;
					$count++;
				}
			}
		}

		return $processedStudents;
	}

	public function getStudentForPreferenceEntry($data)
	{
		$processedStudents = array();

		$appliedUnitClg = explode('c~', $data['Search']['applied_for']);
		$appliedUnitDpt = explode('d~', $data['Search']['applied_for']);
		
		if (!isset($appliedUnitClg[1])) {
			$appliedUnitDept = explode('d~', $data['Search']['applied_for']);
			if (isset($appliedUnitDept[1]) && !empty($appliedUnitDept[1])) {
				$foreignKey = $appliedUnitDept[1];
			}
		} else if (isset($appliedUnitClg[1]) && !empty($appliedUnitClg[1])) {
			$foreignKey = $appliedUnitClg[1];
		}

		if (isset($data['Search']['applied_for']) && !empty($data['Search']['applied_for'])) {

			$placementRoundParticipantSelected = ClassRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					//	'PlacementRoundParticipant.id' => $data['Search']['placement_round_participant_id'],
					'PlacementRoundParticipant.applied_for' => $data['Search']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['Search']['program_id'],
					//'PlacementRoundParticipant.program_type_id' => $data['Search']['program_type_id'],
					//'PlacementRoundParticipant.foreign_key' => $foreignKey,
					'PlacementRoundParticipant.academic_year' => $data['Search']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['Search']['placement_round'],
				),
				'recursive' => -1
			)); 
			//placement_round_participant_id
			//debug($placementRoundParticipantSelected);

			$placementRoundParticipantUnitsList = ClassRegistry::init('PlacementRoundParticipant')->find("list", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $data['Search']['applied_for'],
					'PlacementRoundParticipant.program_id' => $data['Search']['program_id'],
					'PlacementRoundParticipant.program_type_id' => $data['Search']['program_type_id'],
					//'PlacementRoundParticipant.foreign_key' => $foreignKey,
					'PlacementRoundParticipant.academic_year' => $data['Search']['academic_year'],
					'PlacementRoundParticipant.placement_round' => $data['Search']['placement_round'],
				),
				'fields' => array('PlacementRoundParticipant.id', 'PlacementRoundParticipant.name')
			));

			$semester = null;

			if (!empty($placementRoundParticipantSelected)) {
				$semester = $placementRoundParticipantSelected['PlacementRoundParticipant']['semester'];
			}

			if (!isset($semester)) {
				if ($data['Search']['placement_round'] == 1) {
					$semester = 'I';
				} else if ($data['Search']['placement_round'] == 2 || $data['Search']['placement_round'] == 3) {
					$semester = 'II';
				} else {
					$semester = 'I';
				}
			}

			debug($semester);

			if (isset($placementRoundParticipantUnitsList) && !empty($placementRoundParticipantUnitsList)) {

				$listIds = array_keys($placementRoundParticipantUnitsList);

				$isPlacementDone = ClassRegistry::init('PlacementParticipatingStudent')->find("count", array(
					'conditions' => array(
						'PlacementParticipatingStudent.placement_round_participant_id' => $listIds,
						'PlacementParticipatingStudent.status' => 1
					),
					'recursive' => -1
				));

				$preferenceDeadline = classRegistry::init('PlacementDeadline')->find('first', array(
					'conditions' => array(
						'PlacementDeadline.program_id' => $data['Search']['program_id'], 
						'PlacementDeadline.applied_for' => $data['Search']['applied_for'], 
						'PlacementDeadline.program_type_id' => $data['Search']['program_type_id'], 
						'PlacementDeadline.academic_year LIKE ' => $data['Search']['academic_year'] . '%', 
						'PlacementDeadline.placement_round' => $data['Search']['placement_round'], 
						//'PlacementDeadline.deadline > ' => date("Y-m-d H:i:s")
					), 
					'recursive' => -1
				));

				$isDeadlinePassed = 0;
				$deadline = '';

				if (isset($preferenceDeadline) && !empty($preferenceDeadline)) {
					//debug($preferenceDeadline);
					$deadline = $preferenceDeadline['PlacementDeadline']['deadline'];

					if (is_numeric(DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE) && DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE > 0) {
						$date_now =	date("Y-m-d H:i:s", strtotime("-".DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE." day"));
					} else {
						$date_now = date("Y-m-d H:i:s");
					}

					//debug($deadline);
					//debug($date_now);

					if ($deadline < $date_now) {
						$isDeadlinePassed = 1;
					} 
				}

				//debug($isDeadlinePassed);

				$options = array(
					'contain' => array(
						'Student' => array(
							'order' => array('Student.first_name' => 'ASC'), 
							'AcceptedStudent',
							/* 'StudentExamStatus' => array(
								'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.id' => 'DESC'), 
								'AcademicStatus' => array('id', 'name', 'computable'),
							) */
						), 
						'Section'
					),
					'recursive' => -1
				);

				if ($data['Search']['section_id'] == 0) {
					//get_all_section_ids
					if (isset($appliedUnitClg[1]) && is_numeric($appliedUnitClg[1])) {
						// to exclude department assigned students
						$options['conditions'][] = array('Student.department_id is null');

						$collegeSectionIDs = ClassRegistry::init('Section')->find('list', array(
							'conditions' => array(
								'Section.college_id' => $appliedUnitClg[1],
								'Section.department_id is null',
								'Section.program_id' => $data['Search']['program_id'],
								'Section.program_type_id' => $data['Search']['program_type_id'],
								'Section.academicyear' => $data['Search']['academic_year'],
								'Section.archive' => 0,
							),
							'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
							'fields' =>array('Section.id', 'Section.id'),
						));

						if (!empty($collegeSectionIDs)) {
							$options['conditions'][] = array('StudentsSection.section_id' => $collegeSectionIDs, 'StudentsSection.archive' => 0);
						} else {
							$options['conditions'][] = array('StudentsSection.section_id' => $this->get_all_section_ids($data), 'StudentsSection.archive' => 0);
						}

					} else if (isset($appliedUnitDpt[1]) && is_numeric($appliedUnitDpt[1])) {
						
						$deptSectionIDs = ClassRegistry::init('Section')->find('list', array(
							'conditions' => array(
								'Section.department_id' => $appliedUnitDpt[1],
								'Section.program_id' => $data['Search']['program_id'],
								'Section.program_type_id' => $data['Search']['program_type_id'],
								'Section.academicyear' => $data['Search']['academic_year'],
								'Section.archive' => 0,
							),
							'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
							'fields' =>array('Section.id', 'Section.id'),
						));

						if (!empty($deptSectionIDs)) {
							$options['conditions'][] = array('StudentsSection.section_id' => $deptSectionIDs, 'StudentsSection.archive' => 0);
						} else {
							$options['conditions'][] = array('StudentsSection.section_id' => $this->get_all_section_ids($data), 'StudentsSection.archive' => 0);
						}

					} else {
						$options['conditions'][] = array('StudentsSection.section_id' => $this->get_all_section_ids($data));
					}

				} else {
					$options['conditions'][] = array('StudentsSection.section_id' => $data['Search']['section_id'], 'StudentsSection.archive' => 0);
				}

				$placementSettings = classRegistry::init('PlacementResultSetting')->find('all', array(
					'conditions' => array(
						'PlacementResultSetting.applied_for' => $data['Search']['applied_for'],
						'PlacementResultSetting.round' => $data['Search']['placement_round'],
						'PlacementResultSetting.academic_year' => $data['Search']['academic_year'],
						'PlacementResultSetting.program_id' => $data['Search']['program_id'],
						'PlacementResultSetting.program_type_id' => $data['Search']['program_type_id']
					)
				));

				$resultType = array();

				$entranceMax = ENTRANCEMAXIMUM;
				$freshmanMax = FRESHMANMAXIMUM;
				$preparatoryMax = PREPARATORYMAXIMUM;

				$isEntranceSet = false;
				$isFreshmanSet = false;
				$isPreparatorySet = false;

				if (isset($placementSettings) && !empty($placementSettings)) {
					foreach ($placementSettings as $pl => $pv) {
						if (isset($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && ((int) $pv['PlacementResultSetting']['percent'])) {
							
							$resultType[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['percent'];

							if ($pv['PlacementResultSetting']['result_type'] == 'entrance_result') {
								if (is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] >= 0) {
									$entranceMax = $pv['PlacementResultSetting']['max_result'];
									$isEntranceSet = true;
								}
							} else if ($pv['PlacementResultSetting']['result_type'] == 'freshman_result') {
								if (is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] >= 0) {
									$freshmanMax = $pv['PlacementResultSetting']['max_result'];
									$isFreshmanSet = true;
								}
							} else if ($pv['PlacementResultSetting']['result_type'] == 'EHEECE_total_results') {
								if (is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] >= 0) {
									$preparatoryMax = $pv['PlacementResultSetting']['max_result'];
									$isPreparatorySet = true;
								}
							}
						}
					}
				}

				/* debug($resultType);
				debug($entranceMax);
				debug($freshmanMax);
				debug($preparatoryMax);
				debug($isEntranceSet);
				debug($isFreshmanSet);
				debug($isPreparatorySet); */

				$students = ClassRegistry::init('StudentsSection')->find('all', $options);

				$count = 0;
				$processedStudents['ParticipantUnit'] = $placementRoundParticipantUnitsList;
				$prfOrder = 1;

				if (!empty($placementRoundParticipantUnitsList)) {
					foreach ($placementRoundParticipantUnitsList as $k => $v) {
						$processedStudents['ParticipantUnitPreferenceOrder'][$prfOrder] = $prfOrder;
						$prfOrder++;
					}
				}

				if (!empty($students)) {
					foreach ($students as $k => &$v) {
						
						// find the result if exists
						$freshManresult = ClassRegistry::init('StudentExamStatus')->find('first', array(
							'conditions' => array(
								'StudentExamStatus.student_id' => $v['Student']['id'],
								'StudentExamStatus.academic_year' => $data['Search']['academic_year'],
								'StudentExamStatus.semester' => $semester,
							),
							'fields' => array('StudentExamStatus.sgpa', 'StudentExamStatus.cgpa'),
							'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.id' => 'DESC')
						));
						//debug($freshManresult);

						if ($isFreshmanSet) {
							if (!empty($freshManresult) && isset($freshManresult['StudentExamStatus']['cgpa']) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && $freshManresult['StudentExamStatus']['cgpa'] > DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
								if (is_numeric($resultType['freshman_result']) && $resultType['freshman_result'] > 0) {
									$v['Student']['AcceptedStudent']['freshman_result'] = ($resultType['freshman_result'] * $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMax;
								} else {
									$v['Student']['AcceptedStudent']['freshman_result'] = (DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMax;
								}
							}
						} else if (!empty($freshManresult) && isset($freshManresult['StudentExamStatus']['cgpa']) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && $freshManresult['StudentExamStatus']['cgpa'] > DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
							$v['Student']['AcceptedStudent']['freshman_result'] = (DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * $freshManresult['StudentExamStatus']['cgpa']) / $freshmanMax;
						}

						if ($isPreparatorySet && isset($v['Student']['AcceptedStudent']['EHEECE_total_results']) && is_numeric($v['Student']['AcceptedStudent']['EHEECE_total_results'])) {
							if (is_numeric($resultType['EHEECE_total_results']) && $resultType['EHEECE_total_results'] >= 0) {
								$v['Student']['AcceptedStudent']['EHEECE_total_results'] = ($resultType['EHEECE_total_results'] * $v['Student']['AcceptedStudent']['EHEECE_total_results']) / $preparatoryMax;
							}
						} else if (is_numeric($v['Student']['AcceptedStudent']['EHEECE_total_results']) && $v['Student']['AcceptedStudent']['EHEECE_total_results'] >= 0) {
							$v['Student']['AcceptedStudent']['EHEECE_total_results'] = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['Student']['AcceptedStudent']['EHEECE_total_results']) / $preparatoryMax;
						}

						$resultExisted = $this->find('first', array(
							'conditions' => array(
								'PlacementEntranceExamResultEntry.placement_round_participant_id' => $listIds,
								'PlacementEntranceExamResultEntry.student_id' => $v['Student']['id'],
								'PlacementEntranceExamResultEntry.accepted_student_id' => $v['Student']['accepted_student_id']
							),
							'order' => array('PlacementEntranceExamResultEntry.result' => 'DESC'),
							'recursive' => -1
						));

						if ($isEntranceSet) {
							if (isset($resultExisted['PlacementEntranceExamResultEntry']) && is_numeric($resultExisted['PlacementEntranceExamResultEntry']['result']) && $resultExisted['PlacementEntranceExamResultEntry']['result'] >= 0) {
								if (is_numeric($resultType['entrance_result']) && $resultType['entrance_result'] >= 0) {
									$v['Student']['AcceptedStudent']['entrance_result'] = ($resultType['entrance_result'] * $resultExisted['PlacementEntranceExamResultEntry']['result']) / $entranceMax;
								} else {
									$v['Student']['AcceptedStudent']['entrance_result'] = (DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * $resultExisted['PlacementEntranceExamResultEntry']['result']) / $entranceMax;
								}
							}
						} else if (isset($resultExisted['PlacementEntranceExamResultEntry']) && is_numeric($resultExisted['PlacementEntranceExamResultEntry']['result']) && $resultExisted['PlacementEntranceExamResultEntry']['result'] >= 0) {
							$v['Student']['AcceptedStudent']['entrance_result'] = (DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * $resultExisted['PlacementEntranceExamResultEntry']['result']) / $entranceMax;
						}

						$preferenceDetails =  ClassRegistry::init('PlacementPreference')->find('all', array(
							'conditions' => array(
								'PlacementPreference.placement_round_participant_id' => $listIds,
								'PlacementPreference.student_id' => $v['Student']['id'],
								'PlacementPreference.accepted_student_id' => $v['Student']['accepted_student_id'],
								'PlacementPreference.academic_year' => $data['Search']['academic_year'],
								'PlacementPreference.round' => $data['Search']['placement_round'],
							),
							'recursive' => -1
						));

						//debug($v['Student']);

						if ($data['Search']['include'] == 1 && isset($resultExisted['PlacementEntranceExamResultEntry']) && !empty($resultExisted['PlacementEntranceExamResultEntry'])) {
							if ($data['Search']['only_with_status'] == 1) {
								if (isset($freshManresult) && !empty($freshManresult['StudentExamStatus'])) {
									$processedStudents['Student'][$count]['Student'] = $v['Student'];
									$processedStudents['Student'][$count]['PlacementPreference'] = $preferenceDetails;
									$processedStudents['Student'][$count]['Status'] = $freshManresult['StudentExamStatus'];
									$processedStudents['Student'][$count]['EntranceResult'] = $resultExisted['PlacementEntranceExamResultEntry'];
									$processedStudents['Student'][$count]['PlacementStatus'] = $isPlacementDone;
									$processedStudents['Student'][$count]['Deadline']  = $deadline;
									$processedStudents['Student'][$count]['DeadlinePassed']  = $isDeadlinePassed;
								}
							} else {

								$processedStudents['Student'][$count]['Student'] = $v['Student'];
								$processedStudents['Student'][$count]['PlacementPreference'] = $preferenceDetails;
								
								if (isset($freshManresult) && !empty($freshManresult['StudentExamStatus'])) {
									$processedStudents['Student'][$count]['Status'] = $freshManresult['StudentExamStatus'];
								} else {
									$processedStudents['Student'][$count]['Status'] = array();
								}

								$processedStudents['Student'][$count]['EntranceResult'] = $resultExisted['PlacementEntranceExamResultEntry'];
								$processedStudents['Student'][$count]['PlacementStatus'] = $isPlacementDone;
								$processedStudents['Student'][$count]['Deadline']  = $deadline;
								$processedStudents['Student'][$count]['DeadlinePassed']  = $isDeadlinePassed;

							}
						} else if ($data['Search']['include'] == 0) {

							if ($data['Search']['only_with_status'] == 1) {
								if (isset($freshManresult) && !empty($freshManresult['StudentExamStatus'])) {
									$processedStudents['Student'][$count]['Student'] = $v['Student'];
									$processedStudents['Student'][$count]['PlacementPreference'] = $preferenceDetails;
									$processedStudents['Student'][$count]['Status'] = $freshManresult['StudentExamStatus'];
									
									if ($isEntranceSet) {
										if (isset($resultExisted['PlacementEntranceExamResultEntry']) && !empty($resultExisted['PlacementEntranceExamResultEntry'])) {
											$processedStudents['Student'][$count]['EntranceResult'] = $resultExisted['PlacementEntranceExamResultEntry'];
										}
									}

									$processedStudents['Student'][$count]['PlacementStatus'] = $isPlacementDone;
									$processedStudents['Student'][$count]['Deadline']  = $deadline;
									$processedStudents['Student'][$count]['DeadlinePassed']  = $isDeadlinePassed;
								}
							} else {

								$processedStudents['Student'][$count]['Student'] = $v['Student'];
								$processedStudents['Student'][$count]['PlacementPreference'] = $preferenceDetails;
								
								if (isset($freshManresult) && !empty($freshManresult['StudentExamStatus'])) {
									$processedStudents['Student'][$count]['Status'] = $freshManresult['StudentExamStatus'];
								} else {
									$processedStudents['Student'][$count]['Status'] = array();
								}

								if ($isEntranceSet) {
									if (isset($resultExisted['PlacementEntranceExamResultEntry']) && !empty($resultExisted['PlacementEntranceExamResultEntry'])) {
										$processedStudents['Student'][$count]['EntranceResult'] = $resultExisted['PlacementEntranceExamResultEntry'];
									}
								}

								$processedStudents['Student'][$count]['PlacementStatus'] = $isPlacementDone;
								$processedStudents['Student'][$count]['Deadline']  = $deadline;
								$processedStudents['Student'][$count]['DeadlinePassed']  = $isDeadlinePassed;
							}
						}

						$count++;
					}
				}
			}
		}

		//debug($processedStudents['Student'][1]);

		return $processedStudents;
	}

	public function get_all_section_ids($data)
	{
		$appliedUnitClg = explode('c~', $data['Search']['applied_for']);

		if (!isset($appliedUnitClg[1])) {
			$appliedUnitDept = explode('d~', $data['Search']['applied_for']);
		}

		$currentUnitClg = explode('c~', $data['Search']['current_unit']);

		if (!isset($currentUnitClg[1])) {
			$currentUnitDept = explode('d~', $data['Search']['current_unit']);
		}

		if (isset($data['Search']['applied_for']) && !empty($data['Search']['applied_for'])) {

			$options = array(
				'order' => array('Section.id' => 'ASC', 'Section.name' => 'ASC'),
				'contain' => array('YearLevel', 'College', 'Department'),
				'recursive' => -1
			);

			if (isset($currentUnitClg[1]) && !empty($currentUnitClg[1])) {
				$options['conditions'][] = array(
					'Section.college_id' => $currentUnitClg[1],
					'Section.department_id is null or Section.department_id = 0 or Section.department_id = "" '
				);
			} else if (isset($currentUnitDept[1]) && !empty($currentUnitDept[1])) {
				$options['conditions'][] = array('Section.department_id' => $currentUnitDept[1]);
			} else {
				if (isset($appliedUnitClg[1]) && !empty($appliedUnitClg[1])) {
					$options['conditions'][] = array(
						'Section.college_id' => $appliedUnitClg[1],
						'Section.department_id is null or Section.department_id = 0 or Section.department_id = ""'
					);
				} else if (isset($appliedUnitDept[1]) && !empty($appliedUnitDept[1])) {
					$options['conditions'][] = array('Section.department_id' => $appliedUnitDept[1]);
				}
			}

			$options['conditions'][] = array('Section.program_id' => $data['Search']['program_id']);
			$options['conditions'][] = array('Section.program_type_id' => $data['Search']['program_type_id']);
			$options['conditions'][] = array('Section.academicyear' => $data['Search']['academic_year']);
			$options['conditions'][] = array('Section.archive' => 0);
			$options['fields'] = array('Section.id', 'Section.id');
		}

		if (!empty($options)) {
			$sections = ClassRegistry::init('Section')->find('list', $options);
		} else {
			$sections = array();
		}

		return $sections;
	}
}
