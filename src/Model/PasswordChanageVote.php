<?php
class PasswordChanageVote extends AppModel {
	var $name = 'PasswordChanageVote';
	var $validate = array(
		
		'chanage_password_request_date' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				
			)
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
