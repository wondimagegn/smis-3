<?php
class CourseSplitSection extends AppModel {
	var $name = 'CourseSplitSection';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'SectionSplitForPublishedCourse' => array(
			'className' => 'SectionSplitForPublishedCourse',
			'foreignKey' => 'section_split_for_published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true,
			
		),
		
	);
	var $hasMany = array(
	   'CourseInstructorAssignment' => array(
		    'className' => 'CourseInstructorAssignment',
			'foreignKey' => 'course_split_section_id',
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
	     /*	
		'CourseSplitSection'=>array(
			'className' => 'CourseSplitSection',
			'foreignKey' => 'course_split_section_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
		*/
	);
	
	var $hasAndBelongsToMany = array(
		'Student' => array(
			'className' => 'Student',
			'joinTable' => 'students_course_split_sections',
			'foreignKey' => 'course_split_section_id',
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
		),
	);
}
