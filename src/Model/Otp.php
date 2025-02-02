<?php
class Otp extends AppModel {
	var $name = 'Otp';
	var $displayField = 'password';

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
}
