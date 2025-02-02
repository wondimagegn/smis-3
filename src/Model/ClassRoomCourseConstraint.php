<?php
class ClassRoomCourseConstraint extends AppModel {
	var $name = 'ClassRoomCourseConstraint';
	var $displayField = 'published_course_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'published_course_id',
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
	
	function beforeDeleteCheckEligibility($id=null,$college_id=null){
		$departments = $this->PublishedCourse->Department->find('list',array('fields'=>array('Department.id'),'conditions'=>array('Department.college_id'=>$college_id)));
		$publishedCourses_id_array = $this->PublishedCourse->find('list',array('fields'=>array('PublishedCourse.id'),'conditions'=>array('PublishedCourse.drop'=>0,"OR"=>array(array('PublishedCourse.college_id'=>$college_id),array('PublishedCourse.department_id'=>$departments)))));
		$count = $this->find('count',array('conditions'=>array('ClassRoomCourseConstraint.published_course_id'=>$publishedCourses_id_array,'ClassRoomCourseConstraint.id'=>$id)));
		if($count >0){
			return true;
		} else{
			return false;
		}			
	}
	
	function is_class_room_used($id=null){
		$count = $this->find('count', array('conditions'=>array('ClassRoomCourseConstraint.class_room_id'=>$id),'limit'=>2));
		return $count;
	}
}
