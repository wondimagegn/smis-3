<?php
class UnschedulePublishedCourse extends AppModel {
	var $name = 'UnschedulePublishedCourse';
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CourseSplitSection' => array(
			'className' => 'CourseSplitSection',
			'foreignKey' => 'course_split_section_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
