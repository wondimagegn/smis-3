<?php
class Weblink extends AppModel {
	var $name = 'Weblink';
	var $displayField = 'title';
	var $validate = array(
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide web link title, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'url_address' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Please provide valid url, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	/*var $hasAndBelongsToMany = array(
		'Course' => array(
			'className' => 'Course',
			'joinTable' => 'courses_weblinks',
			'foreignKey' => 'weblink_id',
			'associationForeignKey' => 'course_id',
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
	);*/
	
	function deleteWeblinkList ($course_id=null,$data=null) {
	        $dontdeleteids=array();
	        $deleteids=array();
	        $deleteids=$this->find('list',
            array('conditions'=>array('Weblink.course_id'=>$course_id),
            'fields'=>'id'));
           if (!empty($data['Weblink'])) {
	            foreach ($data['Weblink'] as $in=>$va) {
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
                'Weblink.id'=>$deleteids), false);
            }
           
            
	}

}
