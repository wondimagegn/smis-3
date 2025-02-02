<?php
App::uses('AppModel', 'Model');
class PlacementDeadline extends AppModel
{
	public $validate = array(
		'deadline' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			),
		),
		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'program_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'group_identifier' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'applied_for' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

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

	// 0 - no deadline is defined at all
	// 1 - deadline defined and not passed
	// 2 - deadline defined and passed 

	public function getDeadlineStatus($acceptedStudentdetail = array(), $applied_for, $placementRound, $academic_year)
	{
		$status = $this->find('first', array('conditions' => array(
			//'PlacementDeadline.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'],
			'PlacementDeadline.program_id' => Configure::read('programs_available_for_placement_preference'), 
			'PlacementDeadline.applied_for' => $applied_for, 
			//'PlacementDeadline.program_type_id' => $acceptedStudentdetail['AcceptedStudent']['program_type_id'],
			'PlacementDeadline.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
			'PlacementDeadline.placement_round' => $placementRound,
			'PlacementDeadline.academic_year LIKE ' => $academic_year . '%',
			// 'PlacementDeadline.deadline > ' => date("Y-m-d H:i:s")
		)));
		
		debug($status);

		if (!empty($status)) {
			if ($status['PlacementDeadline']['deadline'] > date("Y-m-d H:i:s")) {
				// deined not passed
				return 1;
			} else if ($status['PlacementDeadline']['deadline'] < date("Y-m-d H:i:s")) {
				//defined and passed
				return 2;
			}
		}
		return 0;
	}

	public function isDuplicated($data = array())
	{
		if (isset($data) && !empty($data)) {

			if (isset($data['PlacementDeadline']['id'])) {
				$definedCount = $this->find("count", array(
					'conditions' => array(
						'PlacementDeadline.id <>' => $data['PlacementDeadline']['id'],
						'PlacementDeadline.applied_for' => $data['PlacementDeadline']['applied_for'],
						'PlacementDeadline.program_id' => $data['PlacementDeadline']['program_id'],
						'PlacementDeadline.program_type_id' => $data['PlacementDeadline']['program_type_id'],
						'PlacementDeadline.academic_year' => $data['PlacementDeadline']['academic_year'],
						'PlacementDeadline.placement_round' => $data['PlacementDeadline']['placement_round']
					),
					'recursive' => -1
				));
			} else {
				$definedCount = $this->find("count", array(
					'conditions' => array(
						'PlacementDeadline.applied_for' => $data['PlacementDeadline']['applied_for'],
						'PlacementDeadline.program_id' => $data['PlacementDeadline']['program_id'],
						'PlacementDeadline.program_type_id' => $data['PlacementDeadline']['program_type_id'],
						'PlacementDeadline.academic_year' => $data['PlacementDeadline']['academic_year'],
						'PlacementDeadline.placement_round' => $data['PlacementDeadline']['placement_round']
					),
					'recursive' => -1
				));
			}

			if ($definedCount) {
				return true;
			}
		}

		return false;
	}
}
