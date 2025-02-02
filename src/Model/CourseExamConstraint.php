<?php
class CourseExamConstraint extends AppModel {
	var $name = 'CourseExamConstraint';
	var $displayField = 'published_course_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function get_already_recorded_course_exam_constraint($published_course_id=null){
		if(!empty($published_course_id)){
			$courseExamConstraints = $this->find('all',array('conditions'=>array('CourseExamConstraint.published_course_id'=>$published_course_id),'order'=>array('CourseExamConstraint.exam_date','CourseExamConstraint.session'),'recursive'=>-1));
			$course_exam_constraints_by_date = array();
			foreach($courseExamConstraints as $courseExamConstraint){
				$course_exam_constraints_by_date[$courseExamConstraint['CourseExamConstraint']['exam_date']][$courseExamConstraint['CourseExamConstraint']['session']]['id'] = $courseExamConstraint['CourseExamConstraint']['id'];
				$course_exam_constraints_by_date[$courseExamConstraint['CourseExamConstraint']['exam_date']][$courseExamConstraint['CourseExamConstraint']['session']]['active'] = $courseExamConstraint['CourseExamConstraint']['active'];
			}
			return $course_exam_constraints_by_date;
		}
	}
}
