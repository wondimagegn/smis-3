<?php
class ExceptionMealAssignment extends AppModel {
	var $name = 'ExceptionMealAssignment';
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MealHall' => array(
			'className' => 'MealHall',
			'foreignKey' => 'meal_hall_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    var $validate = array(
	
		'end_date'=>array(
			 'comparison' => array(
			        'rule'=>array('field_comparison', '>=', 'start_date'), 
			        'message' => 'End date should be greater than start date.',
		       )
		),
		
	);
	
	function field_comparison($check1, $operator, $field2) { 
        foreach($check1 as $key=>$value1) { 
            $value2 = $this->data[$this->alias][$field2]; 
            if (!Validation::comparison($value1, $operator, $value2)) 
                return false; 
        } 
        return true; 
    }
    /**
    * 1 in the exception and allowed in the given date  
    * 2 in the exception and denied in the given date 
    * 3 nothing 
    */
    function isInException ($student_id = null,$meal_hall_id=null) {
         $allow = $this->find('count',array('conditions'=>array('ExceptionMealAssignment.student_id'=>$student_id,'ExceptionMealAssignment.start_date <='=>date('Y-m-d'),
         'ExceptionMealAssignment.end_date >='=>date('Y-m-d'),
         'ExceptionMealAssignment.accept_deny'=>1,
         'ExceptionMealAssignment.meal_hall_id'=>$meal_hall_id)));   
         if ($allow>0) {
            return 1; 
         }
         // s>today and e<today 
         
         $deny = $this->find('count',array(
          'conditions'=>array('ExceptionMealAssignment.student_id'
          =>$student_id,
          'ExceptionMealAssignment.start_date <='=>date('Y-m-d'),
         'ExceptionMealAssignment.end_date >='=>
         date('Y-m-d'),
         'ExceptionMealAssignment.accept_deny'=>-1,
         'ExceptionMealAssignment.meal_hall_id'=>$meal_hall_id)));
         
         if ($deny>0) {
            return 2;
         }
         return 3;
    
    }
    
     function checkDuplication ($data=null) {
           
           foreach ($data['ExceptionMealAssignment'] as $in=>$val) {
                    $check=$this->find('count',array('conditions'=>array(
                    'ExceptionMealAssignment.start_date'=>$val['start_date']['year'].'-'.$val['start_date']['month'].'-'.$val['start_date']['day'],
                    'ExceptionMealAssignment.student_id'=>$val['student_id']
                    )));
                    $student_name = $this->Student->field('full_name',array('Student.id'=>$val['student_id']));
                   
                    if ($check>0) {
                        $this->invalidate('error','You have already put  student '.$student_name.' in exception  
                        for '.$val['start_date']['year'].'-'.$val['start_date']['month'].'-'.$val['start_date']['day'].' date ');
	                    return false;
                    }
           }
           return true;
     }
}
