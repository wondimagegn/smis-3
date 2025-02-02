<?php
class CostShare extends AppModel {
	var $name = 'CostShare';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		
	);
	var $hasMany = array(
	  'Attachment' => array( 
            'className' => 'Media.Attachment', 
            'foreignKey' => 'foreign_key', 
            'conditions'    => array('model' => 'CostShare'),
            'dependent' => true, 
        ),
	);
	var $validate = array(
		
		'education_fee' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide eduction fee.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric'=>array(
				'rule' => array('numeric'),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison'=>array(
			    'rule' => array('comparison','>=',0),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			
			)
		),
		'accomodation_fee' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide accomodation fee.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric'=>array(
				'rule' => array('numeric'),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison'=>array(
			    'rule' => array('comparison','>=',0),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			
			)
		),
		'cafeteria_fee' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide cafeteria fee.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric'=>array(
				'rule' => array('numeric'),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison'=>array(
			    'rule' => array('comparison','>=',0),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			
			)
		),
		'medical_fee' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide medical fee.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric'=>array(
				'rule' => array('numeric'),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison'=>array(
			    'rule' => array('comparison','>=',0),
				'message' => 'Please provide numeric value.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			
			)
		),
	);

	public function getCostSharingGraduated($data){
		debug($data);
	   App::import('Component','AcademicYear');
	   $AcademicYear= new AcademicYearComponent(new ComponentCollection);
	   $graduateDate= $AcademicYear->get_academicYearBegainingDate($data['Report']['graduated_academic_year']);
	   $options=array();
	   $options['contain']=array(
	   		'Student'=>array('order'=>array('Student.first_name ASC'),'Department','GraduateList'),
	   		
	   );
	   if (isset($data['Report']['department_id']) && !empty($data['Report']['department_id'])) 
       {		      
			$college_ids = explode('~', $data['Report']['department_id']);
			if(count($college_ids) > 1) {
				$options['conditions']['Student.college_id']=$college_ids[1];
			} else {
              $options['conditions']['Student.department_id']=$data['Report']['department_id'];
			} 
	   }

	   if (isset($data['Report']['program_id']) && !empty($data['Report']['program_id'])) {	
			$options['conditions']['Student.program_id']=$data['Report']['program_id'];
	   }
	   if (isset($data['Report']['program_type_id']) && !empty($data['Report']['program_type_id'])) {	
			$options['conditions']['Student.program_type_id']=$data['Report']['program_type_id'];
	   }
	   

	    if(isset($data['Report']['graduated_academic_year']) && !empty($data['Report']['graduated_academic_year'])){
	   	  // it should be in between
	   	  $nextGraduateAcademicYear=$this->Student->StudentExamStatus->getNextSemster($data['Report']['graduated_academic_year'])['academic_year'];
	   	  $options['conditions'][] = "Student.id IN (SELECT student_id FROM graduate_lists where graduate_date >='$graduateDate' and graduate_date <='$nextGraduateAcademicYear' )";
	   } 
	   if (isset($data['Report']['name']) && !empty($data['Report']['name'])) {	
			$options['conditions']['Student.first_name LIKE ']=$data['Report']['name'].'%';
	   }
	   
	   if (isset($data['Report']['studentnumber']) && !empty($data['Report']['studentnumber'])) {	
	   		unset($options['conditions']);
			$options['conditions']['Student.studentnumber']=$data['Report']['studentnumber'];
	   }

	   $studentCosts=$this->find('all',$options);
	  
	   $formattedStudentList=array();
	   if(!empty($studentCosts)){
	   		App::import('Component','EthiopicDateTime');
	   		$EthiopicDateTimeAC= new EthiopicDateTimeComponent();
	  		foreach ($studentCosts as $key => $value) {
	   			
	   	    $formattedStudentList['StudentList'][$value['Student']['Department']['name'].'~'.$value['Student']['full_name'].'~'.$value['Student']['studentnumber'].'~'.$value['Student']['gender'].'~'.$value['Student']['GraduateList']['graduate_date']][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$value['CostShare']['academic_year'])]=$value['CostShare'];
	   	       $formattedStudentList['CostSharingYearList'][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$value['CostShare']['academic_year'])]=$EthiopicDateTimeAC->GetEthiopicYear(1,9,$value['CostShare']['academic_year']);
	   		}
	   		
	   }
	   asort($formattedStudentList['CostSharingYearList']);

	   return $formattedStudentList;
	}

	public function getCostSharingNotGraduated($data){
		debug($data);
	   App::import('Component','AcademicYear');
	   $AcademicYear= new AcademicYearComponent(new ComponentCollection);
	   $graduateDate= $AcademicYear->get_academicYearBegainingDate($data['Report']['graduated_academic_year']);
	   $options=array();
	   $options['contain']=array('CostShare','Department');
	   $options['order']=array('Student.first_name ASC');
	   if (isset($data['Report']['department_id']) && !empty($data['Report']['department_id'])) 
       {		      
			$college_ids = explode('~', $data['Report']['department_id']);
			if(count($college_ids) > 1) {
				$options['conditions']['Student.college_id']=$college_ids[1];
			} else {
              $options['conditions']['Student.department_id']=$data['Report']['department_id'];
			} 
	   }

	   if (isset($data['Report']['program_id']) && !empty($data['Report']['program_id'])) {	
			$options['conditions']['Student.program_id']=$data['Report']['program_id'];
	   }
	   if (isset($data['Report']['program_type_id']) && !empty($data['Report']['program_type_id'])) {	
			$options['conditions']['Student.program_type_id']=$data['Report']['program_type_id'];
	   }
	   
       if (isset($data['Report']['name']) && !empty($data['Report']['name'])) {	
			$options['conditions']['Student.first_name LIKE ']=$data['Report']['name'].'%';
	   }
	    

	    if(isset($data['Report']['graduated_academic_year']) && !empty($data['Report']['graduated_academic_year'])){
	   	  // it should be in between
	   	  $nextGraduateAcademicYear=$this->Student->StudentExamStatus->getNextSemster($data['Report']['graduated_academic_year'])['academic_year'];
	   	 

	   	   $options['conditions'][] = "Student.admissionyear >='$graduateDate' and Student.admissionyear <='$nextGraduateAcademicYear'";

	   } 

	   if (isset($data['Report']['studentnumber']) && !empty($data['Report']['studentnumber'])) {	
	   		unset($options['conditions']);
			$options['conditions']['Student.studentnumber']=$data['Report']['studentnumber'];
	   }
	   $studentCosts=$this->Student->find('all',$options);
	  
	   $formattedStudentList=array();
	   if(!empty($studentCosts)){
	   		App::import('Component','EthiopicDateTime');
	   		$EthiopicDateTimeAC= new EthiopicDateTimeComponent();
	  		foreach ($studentCosts as $key => $value) {
	  				foreach($value['CostShare'] as $cs=>$cv){
			   	    $formattedStudentList['StudentList'][$value['Department']['name'].'~'.$value['Student']['full_name'].'~'.$value['Student']['studentnumber'].'~'.$value['Student']['gender']][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$cv['academic_year'])]=$cv;
			   	       $formattedStudentList['CostSharingYearList'][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$cv['academic_year'])]=$EthiopicDateTimeAC->GetEthiopicYear(1,9,$cv['academic_year']);
	   	   		}
	   		}
	   		
	   }
	   asort($formattedStudentList['CostSharingYearList']);
	  
	   return $formattedStudentList;
	}
}
