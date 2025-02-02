<?php
class CourseExamGapConstraint extends AppModel {
	var $name = 'CourseExamGapConstraint';
	var $displayField = 'published_course_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $validate = array(
		'gap_before_exam' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide number of days only.',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	var $belongsTo = array(
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function beforeDeleteCheckEligibility($id=null,$college_id=null){
		$departments = $this->PublishedCourse->Department->find('list',array('fields'=>array('Department.id'),'conditions'=>array('Department.college_id'=>$college_id)));
		$publishedCourses_id_array = $this->PublishedCourse->find('list',array('fields'=>array('PublishedCourse.id'),'conditions'=>array('PublishedCourse.drop'=>0,"OR"=>array(array('PublishedCourse.college_id'=>$college_id),array('PublishedCourse.department_id'=>$departments)))));
		$count = $this->find('count',array('conditions'=>array('CourseExamGapConstraint.published_course_id'=>$publishedCourses_id_array, 'CourseExamGapConstraint.id'=>$id)));
		if($count >0){
			return true;
		} else{
			return false;
		}			
	}
}
