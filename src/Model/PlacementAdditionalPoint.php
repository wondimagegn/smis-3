<?php
App::uses('AppModel', 'Model');
/**
 * PlacementAdditionalPoint Model
 *
 * @property Program $Program
 * @property ProgramType $ProgramType
 */
class PlacementAdditionalPoint extends AppModel {


	public $belongsTo = array(
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
		)
	);
	
	
	public function reformat($data = array())
	{
		debug($data);
		$reformatedData = array();
		//	$group_identifier = strtotime(date('Y-m-d h:i:sa'));
		if (isset($data) && !empty($data)) {
			$firstData = $data['PlacementAdditionalPoint'][1];
			$findSettingGroup = classRegistry::init('PlacementRoundParticipant')->find("first", array(
				'conditions' => array(

					'PlacementRoundParticipant.applied_for' => $firstData['applied_for'],
					'PlacementRoundParticipant.program_id' => $firstData['program_id'],
					'PlacementRoundParticipant.program_type_id'
					=> $firstData['program_type_id'],

					'PlacementRoundParticipant.academic_year' => $firstData['academic_year'],
					'PlacementRoundParticipant.placement_round' => $firstData['round']
				),
				'recursive' => -1
			));

			foreach ($data['PlacementAdditionalPoint'] as $dk => $dv) {
				
				$isSettingAlreadyRecorded=$this->find('first',array('conditions'=>array('PlacementAdditionalPoint.type'=>$firstData['type'],
				'PlacementAdditionalPoint.point'=>$firstData['point'],
				'PlacementAdditionalPoint.round'=>$firstData['round'],
				'PlacementAdditionalPoint.applied_for'=>$firstData['applied_for'],
				
				'PlacementAdditionalPoint.academic_year'=>$firstData['academic_year'],
				'PlacementAdditionalPoint.program_id'=>$firstData['program_id'],
				'PlacementAdditionalPoint.program_type_id'=>$firstData['program_type_id'],
				),
				'recursive'=>-1));
				$reformatedData['PlacementAdditionalPoint'][$dk] = $dv;
				if(isset($isSettingAlreadyRecorded['PlacementAdditionalPoint']) && !empty($isSettingAlreadyRecorded['PlacementAdditionalPoint'])){
				$reformatedData['PlacementAdditionalPoint'][$dk]['id']=$isSettingAlreadyRecorded['PlacementAdditionalPoint']['id'];
				}
				
				$reformatedData['PlacementAdditionalPoint'][$dk]['group_identifier'] = $findSettingGroup['PlacementRoundParticipant']['group_identifier'];
				$reformatedData['PlacementAdditionalPoint'][$dk]['applied_for'] = $firstData['applied_for'];
				$reformatedData['PlacementAdditionalPoint'][$dk]['program_id'] = $firstData['program_id'];
				$reformatedData['PlacementAdditionalPoint'][$dk]['program_type_id'] = $firstData['program_type_id'];
				$reformatedData['PlacementAdditionalPoint'][$dk]['academic_year'] = $firstData['academic_year'];
				$reformatedData['PlacementAdditionalPoint'][$dk]['round'] = $firstData['round'];
			}
		}
		// Array after removing duplicates 
		//$xunique=array_unique($reformatedData);

		$reformatedDataDuplicateRemoved['PlacementAdditionalPoint'] = array_unique($reformatedData['PlacementAdditionalPoint'], SORT_REGULAR);
		if (count($reformatedData['PlacementAdditionalPoint']) > count($reformatedDataDuplicateRemoved['PlacementAdditionalPoint'])) {
			$this->invalidate(
				'result_type',
				'Please remove the duplicated rows, and try again.'
			);
			return false;
		}

		
		return $reformatedData;
	}
	public function isDuplicated($data = array())
	{
		
		if (isset($data) && !empty($data)) {
			$firstData = $data['PlacementAdditionalPoint'][1];
			$count = $this->find("first", array(
				'conditions' => array(
					'PlacementAdditionalPoint.type' =>
					$firstData['type'],
					
					'PlacementAdditionalPoint.applied_for' => $firstData['applied_for'],
					'PlacementAdditionalPoint.program_id' => $firstData['program_id'],
					'PlacementAdditionalPoint.program_type_id'
					=> $firstData['program_type_id'],

					'PlacementAdditionalPoint.academic_year' => $firstData['academic_year'],
					'PlacementAdditionalPoint.round' => $firstData['round']
				),
				'recursive' => -1
			));
			if (isset($count['PlacementAdditionalPoint']['group_identifier']) && !empty($count['PlacementAdditionalPoint']['group_identifier'])) {
				return $count['PlacementAdditionalPoint']['group_identifier'];
			}
		}
		return false;
	}
}
