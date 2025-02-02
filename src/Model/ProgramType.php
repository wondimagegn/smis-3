<?php
class ProgramType extends AppModel
{
	var $name = 'ProgramType';
	var $displayField = 'name';

	var $hasMany = array(
		'Offer' => array(
			'className' => 'Offer',
			'foreignKey' => 'program_type_id',
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
		'ProgramTypeTransfer' => array(
			'className' => 'ProgramTypeTransfer',
			'foreignKey' => 'program_type_id',
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
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'program_type_id',
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
			'foreignKey' => 'program_type_id',
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
			'foreignKey' => 'program_type_id',
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
		'ClassPeriod' => array(
			'className' => 'ClassPeriod',
			'foreignKey' => 'program_type_id',
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
		'ProgramProgramTypeClassRoom' => array(
			'className' => 'ProgramProgramTypeClassRoom',
			'foreignKey' => 'program_type_id',
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
		'StudentStatusPattern' => array(
			'className' => 'StudentStatusPattern',
			'foreignKey' => 'program_type_id',
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
			'foreignKey' => 'program_type_id',
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

	var $belongsTo = array(
		'ProgramModality' => array(
			'className' => 'ProgramModality',
			'foreignKey' => 'program_modality_id',
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

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['ProgramType']['name'])) {
				$this->invalidate('name_unique');
				return false;
			}
		}
		return true;
	}

	function getParentProgramType($program_type_id = null)
	{
		$program_types = $this->find('all', array('recursive' => -1));
		
		if (!empty($program_types)) {
			foreach ($program_types as $key => $program_type) {
				$equivalent_to_id = unserialize($program_type['ProgramType']['equivalent_to_id']);
				if (is_array($equivalent_to_id) && in_array($program_type_id, $equivalent_to_id)) {
					return $program_type['ProgramType']['id'];
				}
			}
		}
		return $program_type_id;
	}
}
