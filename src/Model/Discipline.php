<?php
class Discipline extends AppModel {
      var $name = 'Discipline';
      var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $validate = array (
		
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide title ',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
			),
		),
		'description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide description ',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
      function duplication ($data=null) 
      {
            if ( empty($data['Discipline']['title']) || empty($data['Discipline']['description']) ) {
                    return 0;
            }
            //fee_amount
            
             $count=$this->find('count',array('conditions'=>
              array('Discipline.student_id'=>$data['Discipline']['student_id'],
              'Discipline.discipline_taken_date'=>$data['Discipline']['discipline_taken_date'])));
           
            return $count;
      }
}
