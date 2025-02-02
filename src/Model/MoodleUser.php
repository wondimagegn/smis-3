<?php
class MoodleUser extends AppModel {
	var $name = 'MoodleUser';
	var $displayField = 'email';

	public $validate = array(
		'firstname' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter first name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/* 'middlename' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter middle name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		), */
		'lastname' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter last name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please provide a valid email address.',
				'allowEmpty' => false,
				'required' => true,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The Moodle User email address is used by someone. Please provided unique different email.',
				'on' => 'update',
			)
		),
		'idnumber' => array(
			'idnumber' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide a valid id number.',
				'allowEmpty' => true,
				'required' => false,
			),
			/* 'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The Moodle User email address is used by someone. Please provided unique different email.',
				'on' => 'update',
			) */
		),
		'user_id' => array(
			'user_id' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide a valid user id for moodle user.',
				'allowEmpty' => false,
				'required' => true,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The Moodle User email address is used by someone. Please provided unique different email.',
				//'on' => 'create',
			)
		),
		/* 'mobile' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter primary contact mobile phone number in +251999999999 format.',
				'allowEmpty' => true,
				'required' => false,
				'on' => 'update',
			),
			'length' => array(
				'rule' => array('checkLengthPhone', 'mobile'),
				'message' => 'The Moodle user phone number you provided is not correct. Please provide phone number in +251999999999 format.',
				'allowEmpty' => true,
				'required' => false,
				'on' => 'update',
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The Moodle user phone number you provided is used by someone. Please provide another phone number.',
				'on' => 'update',
			)
		),
		'phone' => array(
			'length' => array(
				'rule' => array('checkLengthPhone', 'phone'),
				'message' => 'The phone number you provided for moodle user is not correct. Please provide phone number in +251999999999 format.',
				'allowEmpty' => true,
				'required' => false,
				'on' => 'update',
			),
		), */
	);

	function checkLengthPhone($data, $fieldName)
	{
		debug($data);
		debug($fieldName);
		
		$valid = true;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$check = strlen($data[$fieldName]);
			debug($check);
			if ($check != 13) {
				$valid = false;
			}
		}
		return $valid;
	}
}
