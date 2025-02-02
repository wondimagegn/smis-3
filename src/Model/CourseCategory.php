<?php
class CourseCategory extends AppModel {
	var $name = 'CourseCategory';
	var $displayField = 'name';
	/*var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Pleas provide name,it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'total_credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Pleas provide total credit,it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Pleas provide total credit,it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	*/
	var $validate = array(
	   'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Pleas provide name,it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'total_credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide total credit,it is required.',
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Please provide total credit,it is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		  		
		),
		'mandatory_credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide mandatory credit,it is required.',
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison','>=',0),
				'message' => 'Please provide mandatory credit,it is required.',
				
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		  		
		),
	);
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'curriculum_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	var $hasMany = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'course_category_id',
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
