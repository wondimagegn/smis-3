<?php
class ExamRoomConstraint extends AppModel {
	var $name = 'ExamRoomConstraint';
	var $displayField = 'exam_date';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ClassRoom' => array(
			'className' => 'ClassRoom',
			'foreignKey' => 'class_room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function get_already_recorded_exam_room_constraint($class_room_id=null){
		if(!empty($class_room_id)){
			$examRoomConstraints = $this->find('all',array('conditions'=>array('ExamRoomConstraint.class_room_id'=>$class_room_id),'order'=>array('ExamRoomConstraint.exam_date','ExamRoomConstraint.session'),'recursive'=>-1));
			$exam_room_constraints_by_date = array();
			foreach($examRoomConstraints as $examRoomConstraint){
				$exam_room_constraints_by_date[$examRoomConstraint['ExamRoomConstraint']['exam_date']][$examRoomConstraint['ExamRoomConstraint']['session']]['id'] = $examRoomConstraint['ExamRoomConstraint']['id'];
				$exam_room_constraints_by_date[$examRoomConstraint['ExamRoomConstraint']['exam_date']][$examRoomConstraint['ExamRoomConstraint']['session']]['active'] = $examRoomConstraint['ExamRoomConstraint']['active'];
			}
			return $exam_room_constraints_by_date;
		}
	}
	
	function is_class_room_used($id=null){
		$count = $this->find('count', array('conditions'=>array('ExamRoomConstraint.class_room_id'=>$id), 'limit'=>2));
		return $count;
	}
	
}
