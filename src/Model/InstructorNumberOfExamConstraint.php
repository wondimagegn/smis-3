<?php
class InstructorNumberOfExamConstraint extends AppModel {
	var $name = 'InstructorNumberOfExamConstraint';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $validate = array(
		'max_number_of_exam' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide number only.',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
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
	function get_maximum_year_levels_of_college($college_id=null){
		$departments = $this->Staff->Department->find('list',array('conditions'=>array('Department.college_id'=>$college_id),'fields'=>array('Department.id')));
		$largest_yearLevel_department_id = null;
		$yearLevel_count = 0;
		foreach($departments as $department_id){
			$yearLevel_count_latest = $this->Staff->Department->YearLevel->find('count',array('conditions'=>array('YearLevel.department_id'=>$department_id)));
			if($yearLevel_count_latest > $yearLevel_count){
				$yearLevel_count = $yearLevel_count_latest;
				$largest_yearLevel_department_id = $department_id;
			}
		}

		$yearLevels = null;
		if(!empty($largest_yearLevel_department_id)){
			$yearLevels = $this->Staff->Department->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$largest_yearLevel_department_id),'fields'=>array('name','name')));
		}
		return $yearLevels;
	}
	
	function alreadyRecorded($college_id=null,$selected_academicyear=null, $selected_semester=null, $selected_year_level=null, $selected_instructor_id=null) {			
		foreach($selected_year_level as $ylk=>$ylv){
			$repeation =$this->find('count',array('conditions'=>array('InstructorNumberOfExamConstraint.college_id'=>$college_id, 'InstructorNumberOfExamConstraint.academic_year'=>$selected_academicyear, 'InstructorNumberOfExamConstraint.semester'=>$selected_semester, 'InstructorNumberOfExamConstraint.year_level_id'=>$ylv, "OR"=>array('InstructorNumberOfExamConstraint.staff_id'=>$selected_instructor_id, 'InstructorNumberOfExamConstraint.staff_for_exam_id'=>$selected_instructor_id))));
			if ($repeation>0) {
				$this->invalidate('already_recorded_instructor_number_of_exam','Instructor number of exam is already recorded for '.$ylv. ' year students');
				  return true;	
			}
		}
		return false;
	} 
}
