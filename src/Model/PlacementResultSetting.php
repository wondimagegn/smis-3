<?php
App::uses('AppModel', 'Model');
class PlacementResultSetting extends AppModel
{
	public $validate = array(

		'result_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select result type',
				'allowEmpty' => false,

			),
		),
		'percent' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide percent',
				'allowEmpty' => false,
			),
		),
		'applied_for' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select the unit you need to apply.',
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
		'round' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select placement round',
				'allowEmpty' => false,
			),
		),
		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select admission level',
				'allowEmpty' => false,
			),
		),
		'program_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select admission type',
				'allowEmpty' => false,
			),
		),

	);

	public function reformat($data = array())
	{

		$reformatedData = array();
		//	$group_identifier = strtotime(date('Y-m-d h:i:sa'));
		if (isset($data) && !empty($data)) {

			$firstData = $data['PlacementResultSetting'][1];

			$findSettingGroup = classRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(
					'PlacementRoundParticipant.applied_for' => $firstData['applied_for'],
					'PlacementRoundParticipant.program_id' => $firstData['program_id'],
					'PlacementRoundParticipant.program_type_id' => $firstData['program_type_id'],
					'PlacementRoundParticipant.academic_year' => $firstData['academic_year'],
					'PlacementRoundParticipant.placement_round' => $firstData['round'],
				),
				'recursive' => -1
			));

			foreach ($data['PlacementResultSetting'] as $dk => $dv) {
				$isSettingAlreadyRecorded = $this->find('first', array(
					'conditions' => array(
						'PlacementResultSetting.result_type' => $firstData['result_type'],
						'PlacementResultSetting.percent' => $firstData['percent'],
						'PlacementResultSetting.round' => $firstData['round'],
						'PlacementResultSetting.applied_for' => $findSettingGroup['PlacementRoundParticipant']['applied_for'],
						'PlacementResultSetting.group_identifier' => $findSettingGroup['PlacementRoundParticipant']['group_identifier'],
						'PlacementResultSetting.academic_year' => $firstData['academic_year'],
						'PlacementResultSetting.program_id' => $firstData['program_id'],
						'PlacementResultSetting.program_type_id' => $firstData['program_type_id'],
					),
					'recursive' => -1
				));

				$reformatedData['PlacementResultSetting'][$dk] = $dv;

				if (isset($isSettingAlreadyRecorded['PlacementResultSetting']) && !empty($isSettingAlreadyRecorded['PlacementResultSetting'])) {
					$reformatedData['PlacementResultSetting'][$dk]['id'] = $isSettingAlreadyRecorded['PlacementResultSetting']['id'];
				}

				$reformatedData['PlacementResultSetting'][$dk]['group_identifier'] = $findSettingGroup['PlacementRoundParticipant']['group_identifier'];
				$reformatedData['PlacementResultSetting'][$dk]['applied_for'] = $firstData['applied_for'];
				$reformatedData['PlacementResultSetting'][$dk]['program_id'] = $firstData['program_id'];
				$reformatedData['PlacementResultSetting'][$dk]['program_type_id'] = $firstData['program_type_id'];
				$reformatedData['PlacementResultSetting'][$dk]['academic_year'] = $firstData['academic_year'];
				$reformatedData['PlacementResultSetting'][$dk]['round'] = $firstData['round'];
			}
		}

		// Array after removing duplicates 
		//$xunique=array_unique($reformatedData);

		$reformatedDataDuplicateRemoved['PlacementResultSetting'] = array_unique($reformatedData['PlacementResultSetting'], SORT_REGULAR);

		if (count($reformatedData['PlacementResultSetting']) > count($reformatedDataDuplicateRemoved['PlacementResultSetting'])) {
			$this->invalidate('result_type', 'Please remove the duplicated rows, and try again.');
			return false;
		}

		$sumPercent = 0;

		if (!empty($reformatedData['PlacementResultSetting'])) {
			foreach ($reformatedData['PlacementResultSetting'] as $k => $v) {
				$sumPercent += $v['percent'];
			}
		}

		if ($sumPercent != 100) {
			$this->invalidate('percent', 'Please make sure the percent of result setting for all must be 100%, and try again.');
			return false;
		}

		return $reformatedData;
	}

	public function isDuplicated($data = array())
	{
		if (isset($data) && !empty($data)) {
			$firstData = $data['PlacementResultSetting'][1];
			$count = $this->find("first", array(
				'conditions' => array(
					'PlacementResultSetting.result_type' => $firstData['result_type'],
					// 'PlacementResultSetting.percent' => $firstData['percent'],
					'PlacementResultSetting.applied_for' => $firstData['applied_for'],
					'PlacementResultSetting.program_id' => $firstData['program_id'],
					'PlacementResultSetting.program_type_id' => $firstData['program_type_id'],
					'PlacementResultSetting.academic_year' => $firstData['academic_year'],
					'PlacementResultSetting.round' => $firstData['round']
				),
				'recursive' => -1
			));

			if (!isset($count['PlacementResultSetting']['id']) && isset($count['PlacementResultSetting']['group_identifier']) && !empty($count['PlacementResultSetting']['group_identifier'])) {
				return $count['PlacementResultSetting']['group_identifier'];
			}
		}

		return false;
	}
}
