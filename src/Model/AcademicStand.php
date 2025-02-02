<?php
class AcademicStand extends AppModel {
	var $name = 'AcademicStand';
	var $validate = array(
		
		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
		'academic_status_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Select the academic status you want to define rule.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
		'status_visible' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	  'status_visible' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'year_level_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Select year level.',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'semester' => array(
			'notBlank' => array(
				 'rule' => array('notBlank'),
				 'message' => 'Select semester.',
				 'allowEmpty' => false,
				 'required' => false,
				 'class'=>false,
				 'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		//
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AcademicStatus' => array(
			'className' => 'AcademicStatus',
			'foreignKey' => 'academic_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	var $hasMany = array (
	  'AcademicRule' => array(
			'className' => 'AcademicRule',
			'foreignKey' => 'academic_stand_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	
	);
	
	/**
	*Check duplicate entry
	*/
	function check_duplicate_entry($data) {
	      
         $existed_stand=$this->find('count',array('conditions'=>array('program_id'=>$data['AcademicStand']['program_id'],'semester'=>serialize($data['AcademicStand']['semester']),'academic_year_from'=>$data['AcademicStand']['academic_year_from'],'year_level_id'=>serialize($data['AcademicStand']['year_level_id']),'academic_status_id'=>$data['AcademicStand']['academic_status_id']),'recursive'=>-1));	    
	  
	    // debug($array_year_level_diff);
	    if ($existed_stand>0) {
	      $this->invalidate('duplicate',
	            'You have already setup an academic stand for the selected year level,semester and academic year.');
	   
	      return false;
	    }
	   
	    return true;
	}
	
	//check rule 
	function canEditDeleteAcademicRule($data=null) {
	  // when the new rule is applied for the  students who are not graduated but 
	  // currently active
	 
	  if ($data['AcademicStand']['applicable_for_all_current_student']==1) {
	        $edit_delete_possible=ClassRegistry::init('Student')->GraduateList->find('count',array('conditions'=>
	        array('YEAR(GraduateList.graduate_date) >='=>$data['AcademicStand']['academic_year_from'],
	        'Student.program_id'=>$data['AcademicStand']['program_id'])));
	        
	        if ($edit_delete_possible>0) {
	                 $this->invalidate('used_academic_rule','You can not delete or edit this academic stand rules, students has already graduated in this academic rule. You can define a new rule if you want.');
	                return false;
	        } else {
	            return true;
	        }
	  } else {
	     
	      /*$edit_delete_possible=ClassRegistry::init('Student')->find('count',
	      array(
	            'joins'=>array (
	                    array(
	                        'table'=>'student_exam_statuses',
	                        'alias'=>'StudentExamStatus',
	                        'type'=>'inner',
	                        'foreignKey'=>false,
	                        'conditions'=>array('StudentExamStatus.student_id = Student.id  
	                        and '.$data['AcademicStand']['academic_year_from'].' <= YEAR(Student.admissionyear)'))
	               ),
	                'conditions '=>array('YEAR ( Student.admissionyear ) < '=>$data['AcademicStand']['academic_year_from']
	            ,'Student.program_id'=>$data['AcademicStand']['program_id']),'contain'=>array('StudentExamStatus'))
	        );
	        */
	      
	       $edit_delete_possible = $this->query("SELECT students.id, COUNT( student_exam_statuses.student_id ) AS count
FROM students, student_exam_statuses
WHERE students.id = student_exam_statuses.student_id
AND students.program_id =".$data['AcademicStand']['program_id']."
AND YEAR( students.admissionyear ) >=  ".$data['AcademicStand']['academic_year_from']."
GROUP BY students.id limit 1");
	         if (isset($edit_delete_possible[0][0]['count']) && $edit_delete_possible[0][0]['count'] >= 2) {
	                // dont allow edit or delete
	                 $this->invalidate('used_academic_rule','You can not delete or edit this academic stand rules, students academic status has already computed. You can define a new rule if you want.');
	               return false;
	         } else {
	                // allow edit or delete
	                 return true;
	         }
	     
	  }
	  return false;
	// when the new rule is applied for the  students who are not graduated but 
	// currently active
	/**
    
	      $this->query("SELECT count(students.id) as count FROM students, 
	        graduate_lists WHERE graduate_lists.graduate_date >=".$data['AcademicStand']['academic_year_from']." 
	        AND students.id = graduate_lists.student_id AND students.program_id =".$data['AcademicStand']['program_id']." group by students.id");
	      
	 if (count>0) {
	    // you can neither delete or edit academic rule ,introduce new rule
	 } else {
	        // allow to edit or delet
	 }
	
	*/
	
	/*
	 select student.id, count(ExamStatus.student_id) as count from students, examstatus 
	 where examstatus.status is not null and student.id=examstatus.student_id and rule_academic_year <= student.admissionyear and student.admissionyear< (new_academie rule) group by student.id order by
	 c desc limit 0, 1
	 
	 if (count>=2) {
	        // dont allow edit or delete
	 } else {
	        // allow edit or delete
	 
	 }
	
	*/
	}
}
