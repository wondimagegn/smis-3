<?php
class MealHall extends AppModel {
	var $name = 'MealHall';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Block name should not be empty, Please provide valid Block name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique'=>array(
				 'rule'=>array('checkUnique','name'),
				 'message'=>'You have already entered meal hall name. Please provided
				 unique name.'
			)
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Campus' => array(
			'className' => 'Campus',
			'foreignKey' => 'campus_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	var $hasMany = array(
		'MealHallAssignment' => array(
			'className' => 'MealHallAssignment',
			'foreignKey' => 'meal_hall_id',
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
		'ExceptionMealAssignment' => array(
			'className' => 'ExceptionMealAssignment',
			'foreignKey' => 'meal_hall_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function checkUnique ($data, $fieldName) {
			$valid=true;
			if(!isset($this->data['MealHall']['id'])){
			if(isset($fieldName) && $this->hasField($fieldName)) {
					
				$check=$this->find('count',array('conditions'=>array('MealHall.campus_id'=>$this->data['MealHall']['campus_id'], 'MealHall.name'=>$this->data['MealHall']['name'])));
					if($check>0) {
						$valid=false;
				    }	    
			}
			}
			return $valid; 
	}
	
	function getMealHall () {
        
       $mealHalls = $this->find('all',array('contain'=>array('Campus')));
      
       $reformateMealHalls=array();
       
       foreach ($mealHalls as $in=>$name) {
          $reformateMealHalls[$name['Campus']['name']][$name['MealHall']['id']]=$name['MealHall']['name'];
       }
       return $reformateMealHalls;
   }
   
   function get_formatted_mealhall($meal_hall_ids=null){
   		$mealHalls = $this->find('all',array('conditions'=>array('MealHall.id'=>$meal_hall_ids),'contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));
		$formatted_mealhalls = array();
		foreach($mealHalls as $mealHall){
			$formatted_mealhalls[$mealHall['Campus']['name']][$mealHall['MealHall']['id']] = $mealHall['MealHall']['name'];
		}
		return $formatted_mealhalls;
   }
   
}
