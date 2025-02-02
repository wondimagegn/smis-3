<?php
class Dismissal extends AppModel {
	var $name = 'Dismissal';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function dismissedBecauseOfDiscipelanryNotReadmitted($student_id=null,$current_academicyear=null) {
	        $last_status_date=$this->Student->StudentExamStatus->find('first',
	                       array('conditions'=>array(
	                       'StudentExamStatus.student_id'=>$student_id),'order'=>array('StudentExamStatus.created DESC')));
	                       
 	        $check_dismissal = $this->find('count',
		    array('conditions'=>array(
		    'Dismissal.student_id'=>$student_id,
		    'Dismissal.dismisal_date >= '=>$last_status_date['StudentExamStatus']['created'])));
		    if ($check_dismissal>0) {
		        if (!($this->Student->Readmission->is_readmitted ($student_id,$current_academicyear))){
		            return true;
		        } else {
		            return false;
		        }
		        
		    } else {
		    
		        return false;
		    }
		   
	}
	
	function dismissedBecauseOfDiscipelanryAfterRegistrationNotReadmitted($student_id=null,$current_academicyear=null) {
	       $last_registration_date=$this->Student->CourseRegistration->find('first',
	                       array('conditions'=>array(
	                       'CourseRegistration.student_id'=>$student_id),'order'=>array('CourseRegistration.created DESC'),'recursive'=>-1));
	     if(!empty($last_registration_date)){
 	     $check_dismissal = $this->find('count',
		    array('conditions'=>array( 'Dismissal.student_id'=>$student_id,'Dismissal.dismisal_date >= '=>$last_registration_date['CourseRegistration']['created'])));
		    if ($check_dismissal>0) {
		        if (!($this->Student->Readmission->is_readmitted ($student_id,$current_academicyear))){
		            return true;
		        } else {
		            return false;
		        }
		        
		    } else {
		    
		        return false;
		    }
		}
		return false;
		   
	}
}
