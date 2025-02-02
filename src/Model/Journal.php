<?php
class Journal extends AppModel {
	var $name = 'Journal';
	//var $displayField = 'title';
	var $validate = array(
		'journal_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide journal title, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'article_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide journal article title, it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'author' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide journal author, it is required.',
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
			'joinTable' => 'courses_journals',
			'foreignKey' => 'journal_id',
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
	
	function deleteJournalList ($course_id=null,$data=null) {
	        $dontdeleteids=array();
	        $deleteids=array();
	        $deleteids=$this->find('list',
            array('conditions'=>array('Journal.course_id'=>$course_id),
            'fields'=>'id'));
            if (!empty($data['Journal'])) {
	            foreach ($data['Journal'] as $in=>$va) {
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
                'Journal.id'=>$deleteids), false);
            }
           
            
	}

}
