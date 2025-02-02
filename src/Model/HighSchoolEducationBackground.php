<?php
class HighSchoolEducationBackground extends AppModel {
	var $name = 'HighSchoolEducationBackground';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name is required field.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'town' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Town is required field.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'zone' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Zone is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	  'school_level' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'School level is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array (
     'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
    );
	var $hasMany = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'high_school_education_background_id',
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
	
	function deleteHighSchoolEducationBackgroundList ($student_id=null,$data=null) {
	        $dontdeleteids=array();
	        $deleteids=array();
	        $deleteids=$this->find('list',
            array('conditions'=>array('HighSchoolEducationBackground.student_id'=>$student_id),
            'fields'=>'id'));
         
            if (!empty($data['HighSchoolEducationBackground'])) {
	            foreach ($data['HighSchoolEducationBackground'] as $in=>$va) {
	                  if (!empty($va['id'])) {
	                        if (in_array($va['id'],$deleteids)) {
	                            $dontdeleteids[]=$va['id'];
	                        }
          
	                  } 
	            }
	        
	        }
	        if (!empty($dontdeleteids)) {
	            foreach ($deleteids as $in=>&$va) {
	                    if (in_array($va,$dontdeleteids)) {
	                        unset($deleteids[$in]);
	                    }
	            }
	        }
	       
            if (!empty($deleteids)) {
                $this->deleteAll(array(
                'HighSchoolEducationBackground.id'=>$deleteids), false);
            }
           
            
	}

}
