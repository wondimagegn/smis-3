<?php
class MealType extends AppModel {
	var $name = 'MealType';
	var $displayField = 'meal_name';
	var $validate = array(
		'meal_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Meal name should not be empty, Please provide valid meal name',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique'=>array(
				 'rule'=>array('checkUnique','meal_name'),
				 'message'=>'You have already entered meal type name. Please provided
				 unique meal_name.'
			)
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'MealAttendance' => array(
			'className' => 'MealAttendance',
			'foreignKey' => 'meal_type_id',
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
	
	function checkUnique ($data, $fieldName) {
			$valid=true;
			if(!isset($this->data['MealType']['id'])){
			if(isset($fieldName) && $this->hasField($fieldName)) {
					
				$check=$this->find('count',array('conditions'=>array('MealType.meal_name'=>$this->data['MealType']['meal_name'])));
					if($check>0) {
						$valid=false;
				    }	    
			}
			}
			return $valid; 
	}
}
