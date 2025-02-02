<?php
class InstructorClassPeriodCourseConstraint extends AppModel {
	var $name = 'InstructorClassPeriodCourseConstraint';
	var $displayField = 'staff_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ClassPeriod' => array(
			'className' => 'ClassPeriod',
			'foreignKey' => 'class_period_id',
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
		)
	);
	function beforeDeleteCheckEligibility($id=null,$college_id=null){
		$count = $this->find('count',array('conditions'=>array('InstructorClassPeriodCourseConstraint.college_id'=>$college_id, 'InstructorClassPeriodCourseConstraint.id'=>$id)));
		if($count >0){
			return true;
		} else{
			return false;
		}			
	}
}
