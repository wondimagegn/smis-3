<?php
class Woreda extends AppModel
{
	var $name = 'Woreda';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide woreda name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueWoredaInZone' => array(
				'rule' => array('isUniqueWoredaInZone'),
				'message' => 'The woreda name must be unique in the selected zone. The name is already taken. Use another one.'
			),
		),
		'code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide woreda code.',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueWoredaCode' => array(
				'rule' => array('isUniqueWoredaCode'),
				'message' => 'The woreda code must be unique in the given zone. The the code is already taken. Use another one.'
			),
		),
		'zone_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please Select Zone.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
				//'on' => 'create',
			),
		),
	); 

	function isUniqueWoredaInZone()
	{
		$count = 0;
		
		if (!empty($this->data['Woreda']['id'])) {
			$count = $this->find('count', array('conditions' => array('Woreda.zone_id' => $this->data['Woreda']['zone_id'], 'Woreda.name' => $this->data['Woreda']['name'], 'Woreda.id <> ' => $this->data['Woreda']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('Woreda.zone_id' => $this->data['Woreda']['zone_id'], 'Woreda.name' => $this->data['Woreda']['name'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function isUniqueWoredaCode()
	{
		$count = 0;
		
		if (!empty($this->data['Woreda']['id'])) {
			$count = $this->find('count', array('conditions' => array('Woreda.code' => $this->data['Woreda']['code'], 'Woreda.zone_id' => $this->data['Woreda']['zone_id'], 'Woreda.id <> ' => $this->data['Woreda']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('Woreda.code' => $this->data['Woreda']['code'], 'Woreda.zone_id <> ' => $this->data['Woreda']['zone_id'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	var $belongsTo = array(
		'Zone' => array(
			'className' => 'Zone',
			'foreignKey' => 'zone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'woreda_id',
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
		/* 'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		), */
		'Contact' => array(
			'className' => 'Contact',
			'foreignKey' => 'woreda_id',
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
			'foreignKey' => 'woreda_id',
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
			'foreignKey' => 'woreda_id',
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

	function canItBeDeleted($woreda_id = null)
	{
		if ($this->Student->find('count', array('conditions' => array('Student.woreda_id' => $woreda_id))) > 0) {
			return false;
		} else if ($this->Contact->find('count', array('conditions' => array('Contact.woreda_id' => $woreda_id))) > 0) {
			return false;
		} else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.woreda_id' => $woreda_id))) > 0) {
			return false;
		} else if ($this->Staff->find('count', array('conditions' => array('Staff.woreda_id' => $woreda_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
