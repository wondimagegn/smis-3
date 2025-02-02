<?php
class GradeScalePublishedCourse extends AppModel {
	var $name = 'GradeScalePublishedCourse';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'GradeScale' => array(
			'className' => 'GradeScale',
			'foreignKey' => 'grade_scale_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
