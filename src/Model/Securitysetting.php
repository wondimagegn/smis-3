<?php
class Securitysetting extends AppModel
{
	var $name = 'Securitysetting';
	var $validate = array(
		'session_duration' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'The session duration should be numeric',
				'allowEmpty' => false,
				'required' => false,

			),
		),
		'minimum_password_length' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'The minimum password length should be numeric'
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'maximum_password_length' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'The maximum password length should be numeric',
				'allowEmpty' => false

			)
		),
		'password_duration' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'The password duration  should be numeric',
				'allowEmpty' => false,

			),
		),
		'previous_password_use_allowance' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			)
		),
		'number_of_login_attempt' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'falsify_duration' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
}
