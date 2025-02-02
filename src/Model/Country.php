<?php
class Country extends AppModel {
	var $name = 'Country';
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    var $validate = array(
	   'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide country name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			 'unique' => array (
                                    'rule' => array('checkUnique'),
                                    'message' => 'Country name already recorded. Use another'
                            )
		),
		
	);
	function checkUnique() {
	    $count=0;
        if (!empty($this->data['Country']['id'])) {
          $count=$this->find('count',array('conditions'=>array('Country.id <> '=>$this->data['Country']['id'],'Country.name'=>trim($this->data['Country']['name']))));
        } else {
          $count=$this->find('count',array('conditions'=>array('Country.name'=>trim($this->data['Country']['name']))));
        }
	    
	    if ($count>0) {
	        return false;
	    } 
	    return true; 
    }
    
	var $hasMany = array(
		'Contact' => array(
			'className' => 'Contact',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'StaffStudy' => array(
			'className' => 'StaffStudy',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'country_id',
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
	
	function canItBeDeleted($country_id = null) {
		if($this->Student->find('count', array('conditions' => array('Student.country_id' 
		=> $country_id))) > 0)
			return false;
		else if($this->Contact->find('count', array('conditions' => 
		array('Contact.country_id' =>$country_id))) > 0)
			return false;
		else if($this->Region->find('count', array('conditions' => 
		array('Region.country_id' =>$country_id))) > 0)
			return false;
		else
			return true;
	 }


}
