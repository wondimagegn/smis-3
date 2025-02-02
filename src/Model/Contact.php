<?php
class Contact extends AppModel
{
	var $name = 'Contact';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $virtualFields = array('full_name' => 'CONCAT(Contact.first_name, " ",Contact.middle_name," ",Contact.last_name)');

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Zone' => array(
			'className' => 'Zone',
			'foreignKey' => 'zone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Woreda' => array(
			'className' => 'Woreda',
			'foreignKey' => 'woreda_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $validate = array(
		'first_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter contact first name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'middle_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter contact middle name',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'last_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter contact last name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'kebele' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter contact kebele',
				'allowEmpty' => true,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'phone_mobile' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter primary contact mobile phone number in +251999999999 format.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
				//'on' => 'update',
			),
			'length' => array(
				'rule' => array('checkLengthPhone', 'phone_mobile'),
				'message' => 'The primary contact phone number you provided is not correct. Please provide phone number in +251999999999 format.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
				'on' => 'update',
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The primary contact phone number you provided is used by someone. Please provide another phone number.',
				//'on' => 'update',
			)
		),
		'region_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Region is required field.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'zone_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Zone is required field.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'woreda_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Woreda is required field.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	function checkLengthPhone($data, $fieldName)
	{
		$valid = true;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$check = strlen($data[$fieldName]);
			debug($check);
			if (!empty($data[$fieldName]) && $check > 0 && ($check < 9 || $check != 13)) {
				$valid = false;
			}
		}
		return $valid;
	}
}
