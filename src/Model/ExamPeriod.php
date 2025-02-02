<?php
class ExamPeriod extends AppModel {
	var $name = 'ExamPeriod';
	var $displayField = 'id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	//
		var $validate = array(
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide final exam period academic year, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'program_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide final exam period program, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*
		'program_type_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide final exam period program type, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),*/
		'semester' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide final exam period semester, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*
		'year_level_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide final exam period year level, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		*/
		'start_date'=>array(
			  'comparestartdatewithtoday' => array(
			        'rule'=>array('comparestartdatewithtoday'), 
			         'message' => 'Exam period start date should be today or in the future',
			         'allowEmpty' => false,
					 'required' => true,
					 'last' => false, // Stop validation after this rule
					 //'on' => 'create', // Limit validation to 'create' or 'update' operations
			         )        
		),
	   'end_date'=>array(
			 'comparison' => array(
			        'rule'=>array('field_comparison', '>=', 'start_date'), 
			         'message' => 'Exam period end date should be greater than start date',
			         'allowEmpty' => false,
					 'required' => true,
					 'last' => true, // Stop validation after this rule
					 //'on' => 'create', // Limit validation to 'create' or 'update' operations
			         ),
			  'compareenddatewithtoday' => array(
			        'rule'=>array('compareenddatewithtoday'), 
			         'message' => 'Exam period end date should be today or in the future',
			         'allowEmpty' => false,
					 'required' => true,
					 'last' => false, // Stop validation after this rule
					 //'on' => 'create', // Limit validation to 'create' or 'update' operations
			         )        
		),
	);
	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'year_level_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ExamExcludedDateAndSession' => array(
			'className' => 'ExamExcludedDateAndSession',
			'foreignKey' => 'exam_period_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	function compareenddatewithtoday(){
		if($this->data['ExamPeriod']['end_date']>=date("Y-m-d")) {
	        return true;
	    }
		return false;
	}
	
	function comparestartdatewithtoday(){
		if($this->data['ExamPeriod']['start_date']>=date("Y-m-d")) {
	        return true;
	    }
		return false;
	}
	
	function field_comparison($check1, $operator, $field2) { 
        foreach($check1 as $key=>$value1) { 
            $value2 = $this->data[$this->alias][$field2]; 
            if (!Validation::comparison($value1, $operator, $value2)) 
                return false; 
        } 
        return true; 
    }
	function get_maximum_year_levels_of_college($college_id=null){
		$departments = $this->College->Department->find('list',array('conditions'=>array('Department.college_id'=>$college_id),'fields'=>array('Department.id')));
		$largest_yearLevel_department_id = null;
		$yearLevel_count = 0;
		foreach($departments as $department_id){
			$yearLevel_count_latest = $this->College->Department->YearLevel->find('count',array('conditions'=>array('YearLevel.department_id'=>$department_id)));
			if($yearLevel_count_latest > $yearLevel_count){
				$yearLevel_count = $yearLevel_count_latest;
				$largest_yearLevel_department_id = $department_id;
			}
		}

		$yearLevels = null;
		if(!empty($largest_yearLevel_department_id)){
			$yearLevels = $this->College->Department->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$largest_yearLevel_department_id),'fields'=>array('name','name')));
		}
		return $yearLevels;
	}
	
	function alreadyRecorded($data=null) {			
			// validation of repeation 
			$selected_college_id = $data['ExamPeriod']['college_id'];	
			$selected_academic_year = $data['ExamPeriod']['academic_year'];
			$selected_program_id = $data['ExamPeriod']['program_id'];
			$selected_semester = $data['ExamPeriod']['semester'];
			foreach($data['ExamPeriod']['program_type_id'] as $ptk=>$ptv){
				foreach($data['ExamPeriod']['year_level_id'] as $ylk=>$ylv){
					$repeation =$this->find('count',array('conditions'=>array('ExamPeriod.college_id'=>$selected_college_id,'ExamPeriod.academic_year'=>$selected_academic_year,'ExamPeriod.program_id'=>$selected_program_id, 'ExamPeriod.program_type_id'=>$ptv,'ExamPeriod.year_level_id'=>$ylv, 'ExamPeriod.semester'=>$selected_semester)));
					if ($repeation>0) {
						$program_type_name = $this->ProgramType->field('ProgramType.name', array('ProgramType.id'=>$ptv));
						$this->invalidate('already_recorded_exam_perid','The exam period is already recorded for '.$program_type_name.' '.$ylv. ' year students');
						  return false;	
					}
				}
			}
			return true;
	}
	
	function beforeDeleteCheckEligibility($id=null,$college_id=null){
		$count = $this->find('count',array('conditions'=>array('ExamPeriod.college_id'=>$college_id, 'ExamPeriod.id'=>$id)));
		if($count >0){
			return true;
		} else{
			return false;
		}			
	}
}
