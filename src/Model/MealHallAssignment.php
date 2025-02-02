<?php
class MealHallAssignment extends AppModel {
	var $name = 'MealHallAssignment';
	var $displayField = 'meal_hall_id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'MealHall' => array(
			'className' => 'MealHall',
			'foreignKey' => 'meal_hall_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'accepted_student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function get_maximum_year_levels_of_college($college_id=null){
		$departments = $this->Student->Department->find('list',array('conditions'=>array('Department.college_id'=>$college_id),'fields'=>array('Department.id')));
		$largest_yearLevel_department_id = null;
		$yearLevel_count = 0;
		foreach($departments as $department_id){
			$yearLevel_count_latest = $this->Student->Department->YearLevel->find('count',array('conditions'=>array('YearLevel.department_id'=>$department_id)));
			if($yearLevel_count_latest > $yearLevel_count){
				$yearLevel_count = $yearLevel_count_latest;
				$largest_yearLevel_department_id = $department_id;
			}
		}

		$yearLevels = null;
		if(!empty($largest_yearLevel_department_id)){
			$yearLevels = $this->Student->Department->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$largest_yearLevel_department_id),'fields'=>array('name','name')));
		}
		return $yearLevels;
	}
	
	//Get student_id that already assigned meal hall in the selected academic year
	function get_student_have_mealhall($academicyear=null){
		$student_ids = $this->find('list',array('fields'=>array('MealHallAssignment.student_id'), 'conditions'=>array('MealHallAssignment.academic_year'=>$academicyear, 'NOT'=>array('MealHallAssignment.student_id'=>Null))));
		return $student_ids;
	}
	//Get accepted_student_id that already assigned meal hall in the selected academic year
	function get_accepted_student_have_mealhall($academicyear=null){
		$accepted_student_ids = $this->find('list',array('fields'=>array('MealHallAssignment.accepted_student_id'), 'conditions'=>array('MealHallAssignment.academic_year'=>$academicyear, "NOT"=>array('MealHallAssignment.accepted_student_id'=>Null))));
		return $accepted_student_ids;
	}
	
	function get_formatted_mealhall($academic_year=null){
		$mealHalls = $this->MealHall->find('all',array('contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));
		$formatted_mealhalls = array();
		foreach($mealHalls as $mealHall){
			$formatted_mealhalls[$mealHall['Campus']['name']][$mealHall['MealHall']['id']] = $mealHall['MealHall']['name'].' (Currently has:'.$this->get_mealhall_assigned_students_count($academic_year,$mealHall['MealHall']['id']).' students)';
		}
		
		return $formatted_mealhalls;
	}
	
	function get_mealhall_assigned_students_count($academic_year=null,$meal_hall_id=null){
		$count = $this->find('count',array('conditions'=>array('MealHallAssignment.meal_hall_id'=>$meal_hall_id,'MealHallAssignment.academic_year'=>$academic_year)));
		
		return $count;
	}
	
	function get_formatted_mealhall_for_view(){
		$mealHalls = $this->MealHall->find('all',array('contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));
		$formatted_mealhalls = array();
		foreach($mealHalls as $mealHall){
			$formatted_mealhalls[$mealHall['Campus']['name']][$mealHall['MealHall']['id']] = $mealHall['MealHall']['name'];
		}
		
		return $formatted_mealhalls;
	} 
	
	//Check this meal hall is ever been used in meal hall assignment or not
	function is_meal_hall_ever_used($meal_hall_id=null){
		if(!empty($meal_hall_id)){
			$count = 0;
			$count = $this->find('count',array('conditions'=>array('MealHallAssignment.meal_hall_id'=>$meal_hall_id),'limit'=>2));
			if($count==0){
				return false;
			} else {
				return true;
			}
		} 
	}

}
