<?php
class Title extends AppModel {
	var $name = 'Title';
	var $displayField = 'title';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    var $validate = array(
	   'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide title name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			 'unique' => array (
                                    'rule' => array('checkUnique', 'title'),
                                    'message' => 'Title name already recorded. Use another'
                            )
		),
		
	);
	 
	 function checkUnique() {
            $count=0;
            if (!empty($this->data['Title']['id'])) {
              
               $count=$this->find('count',array('conditions'=>array('Title.id <> '=>$this->data['Title']['id'],'Title.title'=>trim($this->data['Title']['title']))));
               
            } else {
              
               $count=$this->find('count',array('conditions'=>array('Title.title'=>
               trim($this->data['Title']['title']))));
            }
	        
	        if ($count>0) {
	            return false;
	        } 
	        return true; 
    }
    
	var $hasMany = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'title_id',
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

}
