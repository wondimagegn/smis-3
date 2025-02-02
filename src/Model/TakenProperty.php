<?php
class TakenProperty extends AppModel {
	var $name = 'TakenProperty';
	var $displayField = 'name';
	var $validate = array(
	
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide property name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'return_date'=>array(
			 'comparison' => array(
			        'rule'=>array('field_comparison', '>=', 'taken_date'), 
			        'message' => 'Return date should be greater than taken date.',
		       )
		),
		
	);
	
	 function field_comparison($check1, $operator, $field2) { 
        foreach($check1 as $key=>$value1) { 
            $value2 = $this->data[$this->alias][$field2]; 
            if (!Validation::comparison($value1, $operator, $value2)) 
                return false; 
        } 
        return true; 
    }
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
	
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
