<?php
class Quota extends AppModel {
	var $name = 'Quota';
	var $validate = array(
		'female' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'The field can not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('checkQuotaPositiveAndGreaterThanZero','female'),
				'message' => 'Please enter number greaer than zero',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			
		),
		'regions' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'The field can not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('checkQuotaPositiveAndGreaterThanZero','regions'),
				'message' => 'Please enter number greaer than zero',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			
	    )
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	/**
	*Check given number is greather than zero 
	@return boolean
	*/
	function checkQuotaPositiveAndGreaterThanZero($data,$fieldName){
	      
	        if(isset($fieldName)){
	            if($data<0){
	                return false;
	            }
	        }
	        return true;
	}
	/**
	*This method validate the  female qutoa should be less than or equal to total
	* accepted female students. 
	@return boolean
	*/
	function checkAvailableFemaleInTheGivenAcademicYear($data,$college_id=null,
	$academicyear=null){
	        $female=$this->College->AcceptedStudent->find('count',array(
	        'conditions'=>array('AcceptedStudent.sex'=>'female',
	        'AcceptedStudent.college_id'=>$college_id)));
	        if($female<=$data['female']){
	            return true; 
	        } else {
	            return false;
	        }
	}
	
	/**
	*This method validate the  regions qutoa should be less than or equal to total
	* students in the given region. 
	@return boolean
	*/
	function checkAvailableRegionStudentInTheGivenAcademicYear($data,
	$college_id=null,$region_ids,$academicyear=null){
	        $regions=$this->College->AcceptedStudent->find('count',array(
	        'conditions'=>array('AcceptedStudent.region_id'=>array($region_ids),
	        'AcceptedStudent.college_id'=>$college_id)));
	        if($regions<=$data['regions']){
	            return true; 
	        } else {
	            return false;
	        }
	}
	/**
	* This method count the number of quota for the given academic year and college
	@ return true or false
	*/
	function isQuotaRecorded($academicyear=null, $college_id=null) {
	    if ($college_id) { 
	        $count=$this->find('count',
	           array('conditions'=>array('Quota.college_id'=>$college_id,
	           'Quota.academicyear LIKE '=>$academicyear.'%')));
	        if($count){
	            return 1;
	        } else {
	            return 0;
	        }
        }
        return 0;
    }
    
    /**
	* Method to return the quota 
	@return array
	*/
	function quotaCategory($academicyear=null,$college_id=null){
	    if($college_id){
	        $result=$this->find('all',
	           array('conditions'=>array('Quota.college_id'=>$college_id,
	           'Quota.academicyear LIKE '=>$academicyear.'%')));
	        return $result;
	    }
	}
	
	 /**
	* Method to return the quota 
	@return array
	*/
	function quotaNameAndValue($academicyear=null,$college_id=null){
	    if($college_id){
	        $result=$this->find('first',
	           array('conditions'=>array('Quota.college_id'=>$college_id,
	           'Quota.academicyear LIKE '=>$academicyear.'%')));
	        return $result;
	    }
	}
	
    
    
}
