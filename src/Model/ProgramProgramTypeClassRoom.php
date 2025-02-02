<?php
class ProgramProgramTypeClassRoom extends AppModel {
	var $name = 'ProgramProgramTypeClassRoom';
	var $displayField = 'class_room_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
