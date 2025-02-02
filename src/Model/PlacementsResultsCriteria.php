<?php
class PlacementsResultsCriteria extends AppModel {
	var $name = 'PlacementsResultsCriteria';
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
		var $validate = array(
		
		'result_from' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter number required',
				
			)
			,
		),
		'result_to' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Enter number required',
				
			),
		),
	   'admissionyear'=>array(
		        'notempty'=>array(
		                'rule'=>array('notempty'),
		                'message'=>'Select Academic Year.'
		        ),
		),
		'name'=>array(
		        'notempty'=>array(
		                'rule'=>array('notempty'),
		                'message'=>'Enter name.'
		        ),
		)
	);
     var $virtualFields = array(
        'result_category' => "CONCAT(PlacementsResultsCriteria.name, ' (', PlacementsResultsCriteria.result_from,'-',PlacementsResultsCriteria.result_to)",
    );
	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ReservedPlace' => array(
			'className' => 'ReservedPlace',
			'foreignKey' => 'placements_results_criteria_id',
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
	/**
	* method to check result criteria is recored for the given academic year and
	* college
	*/
	function isPlacementResultRecorded($academicyear=null,$college_id=null){
	    if($college_id){
	        $isRecorded=$this->find('count',array('conditions'=>
	        array('college_id'=>$college_id,'PlacementsResultsCriteria.admissionyear LIKE'=>$academicyear.'%')));
	        
	        if($isRecorded){
	            return 1;
	        } else {
	            return 0;
	        }
	    }
	}
	/**
	* Method to check reserved place is recored for the given academic year and 
	* college
	* return boolean true or false
	*/
	function isReservedPlaceRecorded($academicyear=null, $college_id=null){
	        if($college_id){
	            $isRecorded=$this->ReservedPlace->find('all',array('conditions'=>
	        array('ReservedPlace.college_id'=>$college_id,'ReservedPlace.academicyear LIKE'=>$academicyear.'%')));
	         
	        if($isRecorded){
	            return 1;
	        } else {
	            return 0;
	        }
	        }
	}
	/**
	* Method to check reserved place is recored for the given academic year and 
	* college
	* return boolean true or false
	*/
	function isParticipationgDepartmentRecorded($academicyear=null, $college_id=null){
	        if($college_id){
	            $isRecorded=$this->ReservedPlace->ParticipatingDepartment->find('count',array('conditions'=>
	        array('ParticipatingDepartment.college_id'=>$college_id,'ParticipatingDepartment.academic_year  LIKE'=>$academicyear.'%')));
	       
	            if($isRecorded){
	                return 1;
	            } else {
	                return 0;
	            }
	        }
	}
	/**
	* Method to return what result will be considered for selection
	* prepartory or freshman
	*/
	function isPrepartoryResult($academicyear=null,$college_id=null){
	    if($college_id){
	        $isDefined=$this->find('first',array('conditions'=>array('admissionyear LIKE'=>$academicyear.'%','college_id'=>$college_id)));
	         	
	        $isPrepartory=$this->find('count',array('conditions'=>array(
	        'admissionyear LIKE'=>$academicyear.'%','college_id'=>$college_id,
	        'prepartory_result'=>1)));
	        
	       if($isPrepartory){   
	            return 1;
	       } else {
	      
	          if(isset($isDefined) 
	          && !empty($isDefined)){  
	          		return 0;
	       	  } else {
	       	  		return 1;
	       	  }
	       }
	    }
	    return 1;
	}
	
	function isPrepartoryResult2($academicyear=null,$college_id=null){
	    if($college_id){
	        $isPrepartory=$this->find('first',array('conditions'=>array(
	        'admissionyear LIKE'=>$academicyear.'%','college_id'=>$college_id)));
	        if(empty($isPrepartory)){
	            return -1;
	       } else {
	       	if($isPrepartory['PlacementsResultsCriteria']['prepartory_result'] == 1) {
	            return 1;
	          }
	          else {
	          	return 0;
	          }
	       }
	    }
	    return null;
	}
	
	/**
	* Method to return the reserved place for each result category 
	@return array
	*/
	function reservedPlaceCategory($academicyear=null,$college_id=null,$department_id=null){
	    if($college_id){
	        $result=$this->ReservedPlace->find('all',array('conditions'=>array(
	        'ReservedPlace.college_id'=>$college_id,'ReservedPlace.academicyear LIKE'=>$academicyear.'%','ReservedPlace.participating_department_id'=>$department_id),
	        'recursive'=>1));
	        return $result;
	    }
	}
	
	/**
	* Method to return the reserved place for each result category 
	@return array
	*/
	function reservedPlaceForDepartmentByGradeRange($academicyear=null,$college_id=null,$department_id=null){
	    if($college_id){
	        $result=$this->ReservedPlace->find('all',array('conditions'=>array(
	        'ReservedPlace.college_id'=>$college_id,'ReservedPlace.academicyear LIKE'=>$academicyear.'%','ReservedPlace.participating_department_id'=>$department_id),
	        'recursive'=>1));
	        return $result;
	    }
	}
	
	function resultCategoryInput($data=null,$max=null,$min=null){
	    $maxtmp=0;
	    $mintmp=0;
	   // debug($data);
	    if(!$max || !$min){
                $result_type=$data['prepartory_result'];
                $is_preparatory=null;
                if($result_type){
                $is_preparatory='EHEECE_total_results';
                } else {
                $is_preparatory='freshman_result';
                }
		
		       $maxtmp=ClassRegistry::init('AcceptedStudent')->find('first',
			                 array('fields'=>array("MAX(".$is_preparatory.")"),
			                 'conditions'=>array(
		                 'AcceptedStudent.college_id'=>
		                 $data['college_id'],'AcceptedStudent.academicyear'=>
		                 $data['admissionyear'])));
		         $mintmp=ClassRegistry::init('AcceptedStudent')->find('first',
			                 array('fields'=>array("MIN(".$is_preparatory.")"),
			                 'conditions'=>array(
		                 'AcceptedStudent.college_id'=>$data['college_id'],'AcceptedStudent.academicyear'=>
						 $data['admissionyear'])));
		        $maxtmp=$maxtmp[0]['MAX('.$is_preparatory.')'];
		        $mintmp=$mintmp[0]['MIN('.$is_preparatory.')'];
	    } else {
	     $maxtmp=$max;
	     $mintmp=$min;
	    }
	   
	    if(empty($data['name'])){
	         $this->invalidate('result_criteria_name',
	        'Please enter the name for the result category.');
            return false;
	    } elseif(empty($data['result_from'])){
	          $this->invalidate('result_from',
	        'Please enter the result from.');
	         return false;
	    } elseif(empty($data['result_to'])){
	         $this->invalidate('result_to',
	        'Please enter the result to');
	         return false;
	    }
		if(!$this->checkUnique($data)){
			$this->invalidate('result_criteria_name','The name should be unique, please change to other name.');
			return false;
		} 
	    
        if(!empty($data['result_from']) && !empty($data['result_to'])){
             if ($maxtmp!="" && $mintmp!="") {
                  if($data['result_from']>$maxtmp || $data['result_from']<$mintmp ){
	                      $this->invalidate('result_from_problem',
	                    'The "result from" should be less than or equal to '.$maxtmp.'
	                    result and greather than or equal to '.$mintmp.'.');
	                     return false;
	              }
	              
	              if($data['result_to']>$maxtmp || $data['result_to']<$mintmp ){
	                  $this->invalidate('result_to_problem', 'The "result to" should be less 
	                  than or equal to '.$maxtmp.'  and greater than or equal to '.$mintmp.'.');
	                  return false;
	              }
	              
	          }
	         
	    }
	    
	    if($data['result_to']<$data['result_from']){
	       $this->invalidate('result_from_to',
	                'The "result from" should be less than the "result to".');
	                 return false;
	    }
	    $check_no_entry=$this->find('count',
			                 array('conditions'=>array(
		                 'PlacementsResultsCriteria.college_id'=>$data['college_id'],
						 'PlacementsResultsCriteria.admissionyear'=>$data['admissionyear'])));
		if($check_no_entry !=0 ) {
				$already_recorded_range=$this->find('all',
			                 array('conditions'=>array(
		                 'PlacementsResultsCriteria.college_id'=>$data['college_id'],
						 'PlacementsResultsCriteria.admissionyear'=>$data['admissionyear'])));
				//debug($already_recorded_range);
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
		}
	    return true;
	    
	   
	}
	
	
	/**
	* Method to return the reserved place for each result category 
	@return array
	*/
	function getListOfGradeCategory($academicyear=null,$college_id=null){
	   
	        $result=$this->find('all',array('fields'=>array(
	        'PlacementsResultsCriteria.id',
	        'PlacementsResultsCriteria.result_from',
	        'PlacementsResultsCriteria.result_to'),'conditions'=>array(
	        'PlacementsResultsCriteria.college_id'=>$college_id,'PlacementsResultsCriteria.admissionyear LIKE'=>$academicyear.'%'),
	        'recursive'=>-1));
	        return $result;
	}
	
	/**
	* Model validation against continutiy of grade
	*/
	function gradeRangeContinuty($data=null){
	
	
	     $check_no_entry=$this->find('count',
			                 array('conditions'=>array(
		                 'PlacementsResultsCriteria.college_id'=>$data['college_id'],
						 'PlacementsResultsCriteria.admissionyear'=>$data['admissionyear'])));
		 if ($check_no_entry!=0) {
	         $min_from_all= $this->find('first',
			                     array('fields'=>array("MIN(result_from)"),'conditions'=>array(
		                     'PlacementsResultsCriteria.college_id'=>$data['college_id'],'PlacementsResultsCriteria.admissionyear'=>$data['admissionyear'])));
		     $min=$min_from_all[0]["MIN(result_from)"];
		   
		     if($data['result_to']<$min && $data['result_to']>$data['result_from']){
		            return true;
		     } else {
		         $this->invalidate('grade_range_continuty', 'The result To  should be less 
	                  than '.$min.'.');
	             return false;
		     }
		 
        }  else {
        
            return true;
        }
		
	}
	
	function checkUnique($data=null){
		//debug($data);
		$count = $this->hasAny(array('PlacementsResultsCriteria.college_id'=>
		$data['college_id'],
		'PlacementsResultsCriteria.admissionyear'=>$data['admissionyear'],
		'PlacementsResultsCriteria.name'=>$data['name']));
		//debug($count);
		/*$count = $this->find('count',array('conditions'=>array('PlacementsResultsCriteria.college_id'=>
		$data['college_id'],
		'PlacementsResultsCriteria.admissionyear'=>$data['aadmissionyear'])));
		
		*/
		if ($count) {
				return false;
		} else {
		
		
			return true;
		}
	}
	/*
	function beforeDelete(){
        $count = $this->ReservedPlace->find("count", array("conditions" => array("ReservedPlace.placements_results_criteria_id" => $this->id)));
        if ($count == 0) {
            
            return true;
        } else {
           
            return true;
        }
    }
    */
    function getPlacementResultCriteria($college_id,$academicYear){
		   	$resultList=array();
           $placementResultCriteria=$this->find('all',
array('conditions'=>array('PlacementsResultsCriteria.college_id'=>$college_id,
'PlacementsResultsCriteria.admissionyear'=>$academicYear)));
		
			 $resultList['all']='All';
			 foreach($placementResultCriteria as $k=>$v){
					$resultList[$v['PlacementsResultsCriteria']['id']]=$v['PlacementsResultsCriteria']['name'].'('.$v['PlacementsResultsCriteria']['result_from'].'-'.$v['PlacementsResultsCriteria']['result_to'].')';
			 }
            return 	$resultList;
	}
}
