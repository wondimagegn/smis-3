<?php
class MedicalHistory extends AppModel {
	var $name = 'MedicalHistory';
	//var $useDbConfig = 'local';
	var $displayField = 'student_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $validate = array(
		'record_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select record type.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'details' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provided medical history details',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function get_student_details_for_health($student_id = null){
		if(!empty($student_id)){
			$students = $this->Student->find('first',array('conditions'=>array('Student.id'=>$student_id),'fields'=>array('Student.id','Student.studentnumber','Student.full_name','Student.card_number', 'Student.gender','Student.birthdate'),'contain'=>array('College'=>array('fields'=>array('College.name')),'Department'=>array('fields'=>array('Department.name')),'Program'=>array('fields'=>array('Program.name')),'ProgramType'=>array('fields'=>array('ProgramType.name')))));
		return $students;
		}
	}
}
