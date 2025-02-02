<?php
class ClassRoomBlock extends AppModel {
	var $name = 'ClassRoomBlock';
	var $validate = array(
		'block_code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Block code should not be empty, Please provide valid Block code.',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Campus' => array(
			'className' => 'Campus',
			'foreignKey' => 'campus_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_block_id',
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
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_block_id',
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
   
   /**
   * Function that send this data to children model for validation
   * return $this->data
   */
   function send_class_room_block_data (){
   
   	return $this->data;
   }
}
?>
