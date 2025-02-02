<?php
class EslceResult extends AppModel {
	var $name = 'EslceResult';
	var $validate = array(
		'subject' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide subject',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'grade' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide grade',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'exam_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide examination year',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
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
	
	function deleteEslceResultList ($student_id=null,$data=null) {
	        $dontdeleteids=array();
	        $deleteids=array();
	        $deleteids=$this->find('list',
            array('conditions'=>array('EslceResult.student_id'=>$student_id),
            'fields'=>'id'));
           
            if (!empty($data['EslceResult'])) {
	            foreach ($data['EslceResult'] as $in=>$va) {
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
                'EslceResult.id'=>$deleteids), false);
            }
           
            
	}

}
