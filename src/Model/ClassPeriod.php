<?php
class ClassPeriod extends AppModel {
	var $name = 'ClassPeriod';
	var $displayField = 'week_day';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'PeriodSetting' => array(
			'className' => 'PeriodSetting',
			'foreignKey' => 'period_setting_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
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
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	var $hasMany = array (
		'ClassPeriodCourseConstraint' => array(
		    'className' => 'ClassPeriodCourseConstraint',
			'foreignKey' => 'class_period_id',
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
		'ClassRoomClassPeriodConstraint' => array(
		    'className' => 'ClassRoomClassPeriodConstraint',
			'foreignKey' => 'class_period_id',
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
		'InstructorClassPeriodCourseConstraint' => array(
			'className' => 'InstructorClassPeriodCourseConstraint',
			'foreignKey' => 'class_period_id',
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
	
	var $hasAndBelongsToMany = array(
		'CourseSchedule' => array(
			'className' => 'CourseSchedule',
			'joinTable' => 'course_schedules_class_periods',
			'foreignKey' => 'class_period_id',
			'associationForeignKey' => 'course_schedule_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
	);
}
?>
