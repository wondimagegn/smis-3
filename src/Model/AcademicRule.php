<?php
class AcademicRule extends AppModel {
	var $name = 'AcademicRule';
	var $virtualFields = array(
        'cmp_sgpa' => 'CONCAT(AcademicRule.scmo,"",AcademicRule.sgpa)',
        'cmp_cgpa' => 'CONCAT(AcademicRule.ccmo,"",AcademicRule.cgpa)'
        );
	var $validate = array(
	    'sgpa' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'SGPA is required',
				
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'SGPA is numeric',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Please provide SGPA greather than or equal to zero',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'operatorI' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide operator.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cgpa' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'CGPA is required',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'SGPA is numeric',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Please provide CGPA greather than or equal to zero',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'operatorII' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide operator or uncheck two consecutive warnings.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
		'AcademicStand' => array(
			'className' => 'AcademicStand',
			'foreignKey' => 'academic_stand_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	/*
	var $hasAndBelongsToMany = array(
		'AcademicStand' => array(
			'className' => 'AcademicStand',
			'joinTable' => 'academic_stands_academic_rules',
			'foreignKey' => 'academic_rule_id',
			'associationForeignKey' => 'academic_stand_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	*/
	function checkExeclusiveNessOFGradeRule($academic_year=null) {
	 
				/*$already_recorded_range=$this->find('all',
			                 array('conditions'=>array(
		                 'AcademicStand.academic_year_from'=>$academic_year)));*/
				
				$already_recorded_range=$this->AcademicStand->find('all',
				array('conditions'=>array('AcademicStand.academic_year_from'=>$academic_year)));
				debug($already_recorded_range);
				/*
				foreach($already_recorded_range as $ar=>$sr) {
					$sr = $sr['PlacementsResultsCriteria'];
					//debug($sr);
						 if( ($data['result_from']<=$sr['result_from'] && $sr['result_from'] <=$data['result_to'])
						 || ($data['result_from']<=$sr['result_to'] && $sr['result_to'] <=$data['result_to'])
						 || ($sr['result_from']<=$data['result_from'] && $data['result_to'] <= $sr['result_to'])
						 || $data['result_from']<=$sr['result_from'] && $sr['result_to'] <= $data['result_to']){
						  
						  $this->invalidate('result_from_to',
	                'The given grade range is not uniqe. Please make sure that "result from" and/or "result to" is 
					not already recorded.');
						  return false;
						 }
				}
				*/
		
	}

}
