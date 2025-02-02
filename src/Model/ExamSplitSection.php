<?php
class ExamSplitSection extends AppModel {
	var $name = 'ExamSplitSection';
	var $displayField = 'section_name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'SectionSplitForExam' => array(
			'className' => 'SectionSplitForExam',
			'foreignKey' => 'section_split_for_exam_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasAndBelongsToMany = array(
		'Student' => array(
			'className' => 'Student',
			'joinTable' => 'students_exam_split_sections',
			'foreignKey' => 'exam_split_section_id',
			'associationForeignKey' => 'student_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	var $hasMany = array(
		'ExamSchedule'=>array(
		    'className' => 'ExamSchedule',
			'foreignKey' => 'exam_split_section_id',
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
}
?>
