<?php
class MoodleCourse extends AppModel {
	var $name = 'MoodleCourse';
	var $displayField = 'course_code_pid';

	var $belongsTo = array(
		/* 'MoodleCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		), */
	);
}
