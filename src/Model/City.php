<?php
class City extends AppModel
{
	var $name = 'City';
	var $displayField = 'name';

	var $belongsTo = array(
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Zone' => array(
			'className' => 'Zone',
			'foreignKey' => 'zone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	var $hasMany = array(
		'Contact' => array(
			'className' => 'Contact',
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
		),
		'Staff' => array(
			'className' => 'Staff',
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
		),
		'Student' => array(
			'className' => 'Student',
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
		)
	);


	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide City name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueCityInRegion' => array(
				'rule' => array('isUniqueCityInRegion'),
				'message' => 'The city name should be unique in the selected region. The name is already taken. Use another one.'
			),
			'isUniqueCityInZone' => array(
				'rule' => array('isUniqueCityInZone'),
				'message' => 'The city name should be unique in the selected zone. The name is already taken. Use another one.'
			),
		),
		'short' => array(
			/* 'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide city short name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			), */
			'isUniqueCityCode' => array(
				'rule' => array('isUniqueCityCode'),
				'message' => 'The city short name must be unique. The short name is already taken. Use another one.'
			),
		),

	);

	function isUniqueCityInRegion()
	{
		$count = 0;

		if (!empty($this->data['City']['id'])) {
			$count = $this->find('count', array('conditions' => array('City.region_id' => $this->data['City']['region_id'], 'City.name' => $this->data['City']['name'], 'City.id <>' => $this->data['City']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('City.region_id' => $this->data['City']['region_id'], 'City.name' => $this->data['City']['name'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function isUniqueCityInZone()
	{
		$count = 0;

		if (!empty($this->data['City']['id'])) {
			$count = $this->find('count', array('conditions' => array('City.zone_id' => $this->data['City']['zone_id'], 'City.name' => $this->data['City']['name'], 'City.id <>' => $this->data['City']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('City.zone_id' => $this->data['City']['zone_id'], 'City.name' => $this->data['City']['name'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function isUniqueCityCode()
	{
		$count = 0;
		
		if (!empty($this->data['Zone']['id'])) {
			$count = $this->find('count', array('conditions' => array('City.short IS NOT NULL', 'City.short' => $this->data['City']['short'], 'City.id <> ' => $this->data['City']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('City.short IS NOT NULL', 'City.short' => $this->data['City']['short'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function canItBeDeleted($city = null)
	{
		if ($this->Student->find('count', array('conditions' => array('Student.city_id' => $city))) > 0) {
			return false;
		} else if ($this->Contact->find('count', array('conditions' => array('Contact.city_id' => $city))) > 0) {
			return false;
		} else if ($this->Staff->find('count', array('conditions' => array('Staff.city_id' => $city))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
