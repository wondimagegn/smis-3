<?php
class AcademicStatus extends AppModel {
	var $name = 'AcademicStatus';
	var $displayField = 'name';

	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide status name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		'unique' => array (
            'rule' => array('checkUnique', 'name'),
            'message' => 'You have already has this status.'
			)
		),
		'order' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide order of status in number.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

   function checkUnique($data, $fieldName) {
      $valid = false;
      if(isset($fieldName) && $this->hasField($fieldName)) {
      	$valid = $this->isUnique(array($fieldName => $data));
      }
      return $valid;
    }

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'AcademicStand' => array(
			'className' => 'AcademicStand',
			'foreignKey' => 'academic_status_id',
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
		'StudentExamStatus' => array(
			'className' => 'StudentExamStatus',
			'foreignKey' => 'academic_status_id',
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
    function canItBeDeleted($id = null) {
		        if($this->StudentExamStatus->find('count', array('conditions' => array('StudentExamStatus.academic_status_id' =>$id))) > 0)
			        return false;
			    if($this->AcademicStand->find('count',array('conditions' => array('AcademicStand.academic_status_id' =>$id))) > 0)
			        return false;
		        else
			        return false;
	}
}
