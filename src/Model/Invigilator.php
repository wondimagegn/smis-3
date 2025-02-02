<?php
class Invigilator extends AppModel {
	var $name = 'Invigilator';
	
	var $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ExamSchedule' => array(
			'className' => 'ExamSchedule',
			'foreignKey' => 'exam_schedule_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StaffForExam' => array(
			'className' => 'StaffForExam',
			'foreignKey' => 'staff_for_exam_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
