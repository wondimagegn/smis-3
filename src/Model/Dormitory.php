<?php
class Dormitory extends AppModel {
	var $name = 'Dormitory';
	var $displayField = 'dorm_number';
	var $validate = array(
		'dorm_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Dorm name should not be empty, Please provide valid dorm name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Please provide valide dorm number, greater than zero.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Dorm number should only numeric. Please Provide in number.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique'=>array(
				 'rule'=>array('checkUnique','dorm_number'),
				 'message'=>'You have already entered this dorm number. Please provided
				 unique dorm number.'
			)
		),
		'floor' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Floor should not be empty, Please select floor.',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'capacity' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide capacity , it is required.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Please provide valide capacity, greater than zero.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Dorm capacity should only numeric. Please Provide dorm capacity in number.',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	var $hasMany = array(
		'DormitoryAssignment' => array(
			'className' => 'DormitoryAssignment',
			'foreignKey' => 'dormitory_id',
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
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'DormitoryBlock' => array(
			'className' => 'DormitoryBlock',
			'foreignKey' => 'dormitory_block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function checkUnique ($data, $fieldName) {
			$valid=true;
			//debug($this);
			if(!isset($this->data['Dormitory']['id'])){
			if(isset($fieldName) && $this->hasField($fieldName)) {
					$dormitory_block_data=$this->DormitoryBlock->send_dormitory_block_data();
					$dormitory_block_id=$this->DormitoryBlock->find('first',array('conditions'=>array('DormitoryBlock.campus_id'=>$dormitory_block_data['DormitoryBlock']['campus_id'], 'DormitoryBlock.block_name'=>$dormitory_block_data['DormitoryBlock']['block_name']),'recursive'=>-1));
					
					$check=$this->find('count',array('conditions'=>array('Dormitory.dormitory_block_id'=>$dormitory_block_id['DormitoryBlock']['id'],'Dormitory.dorm_number'=>$data['dorm_number'])));
					if($check>0) {
						$valid=false;
				    }
				    
			}
			}
			return $valid;
	}
	
}
