<?php
class StudentNameHistory extends AppModel
{
	var $name = 'StudentNameHistory';
	var $validate = array(
		'to_first_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter first name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'to_middle_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter middle name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'to_last_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter last name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),

		'minute_number' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter minute number',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
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

	function reformat($data = null)
	{
		$reformated_data = array();
		if (isset($data['Student']['id']) && !empty($data['Student']['id'])) {
			
			///////////////////////////////amharic/////////////////////////////////////
			$reformated_data['StudentNameHistory']['to_amharic_first_name'] = $data['Student']['amharic_first_name'];
			$reformated_data['StudentNameHistory']['to_amharic_middle_name'] = $data['Student']['amharic_middle_name'];
			$reformated_data['StudentNameHistory']['to_amharic_last_name'] = $data['Student']['amharic_last_name'];

			$reformated_data['StudentNameHistory']['from_amharic_first_name'] = $data['Student']['amharic_first_name'];
			$reformated_data['StudentNameHistory']['from_amharic_middle_name'] = $data['Student']['amharic_middle_name'];
			$reformated_data['StudentNameHistory']['from_amharic_last_name'] = $data['Student']['amharic_last_name'];

			///////////////////////////////english///////////////////////////////////
			$reformated_data['StudentNameHistory']['to_first_name'] = trim($data['Student']['first_name']);
			$reformated_data['StudentNameHistory']['to_middle_name'] = trim($data['Student']['middle_name']);
			$reformated_data['StudentNameHistory']['to_last_name'] = trim($data['Student']['last_name']);

			$reformated_data['StudentNameHistory']['from_first_name'] = trim($data['Student']['first_name']);
			$reformated_data['StudentNameHistory']['from_middle_name'] = trim($data['Student']['middle_name']);
			$reformated_data['StudentNameHistory']['from_last_name'] = trim($data['Student']['last_name']);
			$reformated_data['StudentNameHistory']['student_id'] = $data['Student']['id'];

			return $reformated_data;
		} else {
			return $data;
		}
	}

	function checkDuplication($data = null)
	{

	}
}
