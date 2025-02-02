<?php
class HigherEducationBackground extends AppModel {
	var $name = 'HigherEducationBackground';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'field_of_study' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Field of study can not be empty.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'diploma_awarded' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Diploma/Degree awarded date is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'date_graduated' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Date of graduation is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cgpa_at_graduation' => array(
			'notBlank' => array(
				'rule' => array('numeric'),
				'message' => 'CGPA  is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	function deleteHigherEducationList ($student_id=null,$data=null) {
	        $dontdeleteids=array();
	        $deleteids=array();
	        $deleteids=$this->find('list',
            array('conditions'=>array('HigherEducationBackground.student_id'=>$student_id),
            'fields'=>'id'));
            if (!empty($data['HigherEducationBackground'])) {
	            foreach ($data['HigherEducationBackground'] as $in=>$va) {
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
                'HigherEducationBackground.id'=>$deleteids), false);
            }
           
            
	}
	
}
