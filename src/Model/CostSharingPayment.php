<?php
class CostSharingPayment extends AppModel {
	var $name = 'CostSharingPayment';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	var $validate = array(
		'reference_number' => array(
			'numeric' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide reference number of payment',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'amount' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide amount .',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric'=>array(
				'rule' => array('numeric'),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison'=>array(
			    'rule' => array('comparison','>=',0),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			
			)
		),
	);
}
