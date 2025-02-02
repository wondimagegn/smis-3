<?php
class InstructorExamExcludeDateConstraint extends AppModel {
	var $name = 'InstructorExamExcludeDateConstraint';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StaffForExam' => array(
			'className' => 'StaffForExam',
			'foreignKey' => 'staff_for_exam_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function get_already_recorded_instructor_exam_excluded_date_constraint($instructor_id=null){
		if(!empty($instructor_id)){
			$instructorExamExcludeDateConstraints = $this->find('all',array('conditions'=>array("OR"=>array('InstructorExamExcludeDateConstraint.staff_id'=>$instructor_id, 'InstructorExamExcludeDateConstraint.staff_for_exam_id'=>$instructor_id)),'order'=>array('InstructorExamExcludeDateConstraint.exam_date','InstructorExamExcludeDateConstraint.session'),'recursive'=>-1));
			$instructorExamExcludeDateConstraint_by_date = array();
			foreach($instructorExamExcludeDateConstraints as $instructorExamExcludeDateConstraint){
				$instructorExamExcludeDateConstraint_by_date[$instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['exam_date']][$instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['session']]['id'] = $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'];

			}
			return $instructorExamExcludeDateConstraint_by_date;
		}
	}
}
