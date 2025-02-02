<?php
App::uses('AppModel', 'Model');
/**
 * OtherAcademicRule Model
 *
 */
class OtherAcademicRule extends AppModel {
	var $validate = array(
		'academic_status_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide to be applied.',
				
			),
		),
		'grade' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide to which grade the rule applies.',
				
			),
		),
		'number_courses' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide number of courses.',
				
			),
		),
	);
   /**
	*Check duplicate entry
	*/
	function check_duplicate_entry($data) {
	     debug($data);
         $existed_stand=$this->find('count',array(
         'conditions'=>array(
         'department_id'=>$data['OtherAcademicRule']['department_id'],
         'program_id'=>$data['OtherAcademicRule']['program_id'],
         'program_type_id'=>$data['OtherAcademicRule']['program_type_id'],
         'curriculum_id'=>$data['OtherAcademicRule']['curriculum_id'],
         //'course_category_id'=>$data['OtherAcademicRule']['course_category_id'],
         'year_level_id'=>$data['OtherAcademicRule']['year_level_id'],
         'academic_status_id'=>$data['OtherAcademicRule']['academic_status_id'],
         'number_courses'=>$data['OtherAcademicRule']['number_courses'],
         'grade'=>$data['OtherAcademicRule']['grade'],
         ),'recursive'=>-1));	    
	  
	    if ($existed_stand>0) {
	      $this->invalidate('duplicate',
	            'You have already defined the academic rule.');
	   
	      return false;
	    }
	   
	    return true;
	}
	
	function whatIsTheStatus($semCourseLists=array(),
	$student,$year=null){
	   $studentDetail=ClassRegistry::init('Student')->find('first',
		array('conditions'=>array('Student.id'=>$student['id']),
			 'recursive'=>-1));
	    if(isset($studentDetail) && !empty($studentDetail)){
			
			$or=$this->find('all',
			array('conditions'=>array(
			'OtherAcademicRule.curriculum_id'=>$studentDetail['Student']['curriculum_id']),'recursive'=>-1));
		   $otherAcademicRules=array();
		   foreach($or as $otr=>$otv){
			   	if(
			   	$otv['OtherAcademicRule']['year_level_id']
			   	==$year['year']){
			   	  $otherAcademicRules=$otv;
			   	  break;
			   	}	 
		   }
		   if(!isset($otherAcademicRules)
			 && empty($otherAcademicRules)){
			  $otherAcademicRules=$this->find('first',
				array('conditions'=>array(
				'OtherAcademicRule.curriculum_id'=>$studentDetail['Student']['curriculum_id']),'recursive'=>-1));
		  }
		if(isset($otherAcademicRules) && 
		!empty($otherAcademicRules)){ 
		
			$countRuleFound=0;
			$academicStatus=null;
			foreach($semCourseLists as $ck=>$cv){
			        $courseDetail=ClassRegistry::init('Course')->find('first',array('conditions'=>array('Course.id'=>$cv['course_id']),'contain'=>array('CourseCategory')));
			   if(isset($courseDetail['CourseCategory']) 
					&& !empty($courseDetail['CourseCategory'])){
						
						if($courseDetail['CourseCategory']['id']==$otv['OtherAcademicRule']['course_category_id'] && 
						strcasecmp($otherAcademicRules['OtherAcademicRule']['course_category_id'],
						$cv['grade'])==0){
						$countRuleFound++;
						
						}
					}
			   
			}
			if($countRuleFound>=$otherAcademicRules['OtherAcademicRule']['number_courses']){
					return $otherAcademicRules['OtherAcademicRule']['academic_status_id'];
			}
		  }
		}
	    return null;
		/*
						if(isset($cv['CourseRegistration']['id']) 
						&& !empty($cv['CourseRegistration']['id'])){
					    $gradeDetail = ClassRegistry::init('ExamGrade')->getApprovedGrade($cv['CourseRegistration']['id'], 1);
						} else {
						  $gradeDetail = ClassRegistry::init('ExamGrade')->getApprovedGrade($cv['CourseAdd']['id'], 0);
						}
						*/
						
	
	}
}
