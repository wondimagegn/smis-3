<?php
class ClassRoomClassPeriodConstraint extends AppModel {
	var $name = 'ClassRoomClassPeriodConstraint';
	var $displayField = 'class_room_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_id',
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
		)
	);
	function beforeDeleteCheckEligibility($id=null,$college_id=null){
		$classPeriods_id_array = $this->ClassPeriod->find('list',array('fields'=>array('ClassPeriod.id'),'conditions'=>array('ClassPeriod.college_id'=>$college_id)));
		$count = $this->find('count',array('conditions'=>array('ClassRoomClassPeriodConstraint.class_period_id'=>$classPeriods_id_array, 'ClassRoomClassPeriodConstraint.id'=>$id)));
		if($count >0){
			return true;
		} else{
			return false;
		}			
	}
	
	function is_class_room_used($id=null){
		$count = $this->find('count', array('conditions'=>array('ClassRoomClassPeriodConstraint.class_room_id'=>$id),'limit'=>2));
		return $count;
	}
}
