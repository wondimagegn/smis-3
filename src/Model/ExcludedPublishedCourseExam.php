<?php
class ExcludedPublishedCourseExam extends AppModel {
	var $name = 'ExcludedPublishedCourseExam';
	var $actsAs = array('Containable'); 
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
		
	function beforeDeleteCheckEligibility($id=null,$college_id=null){
		$department_ids = $this->PublishedCourse->Department->find('list',array('fields'=>array('Department.id','Department.id'),'conditions'=>array('Department.college_id'=>$college_id)));
		$published_course_ids = $this->PublishedCourse->find('list', array('fields'=>array('PublishedCourse.id','PublishedCourse.id'), 'conditions'=>array("OR"=>array('PublishedCourse.college_id'=>$college_id,'PublishedCourse.department_id'=>$department_ids))));
		$count = $this->find('count',array('conditions'=>array('ExcludedPublishedCourseExam.published_course_id'=>$published_course_ids, 'ExcludedPublishedCourseExam.id'=>$id)));
		if($count >0){
			return true;
		} else{
			return false;
		}			
	}
	
}
?>
