<?php
class ExamRoomNumberOfInvigilator extends AppModel {
	var $name = 'ExamRoomNumberOfInvigilator';
	var $displayField = 'number_of_invigilator';
	var $validate = array(
		'number_of_invigilator' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide number only.',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide number of invigilator',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function is_class_room_used($id=null){
		$count = $this->find('count', array('conditions'=>array('ExamRoomNumberOfInvigilator.class_room_id'=>$id), 'limit'=>2));
		return $count;
	}
}
