<?php
class Region extends AppModel
{
	var $name = 'Region';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide region name.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueRegionInCountry' => array(
				'rule' => array('isUniqueRegionInCountry'),
				'message' => 'The region name must be unique in the selected country. The name is already taken. Use another one.'
			),
		),
		'short' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide region short name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueRegionCode' => array(
				'rule' => array('isUniqueRegionCode'),
				'message' => 'The region short name must be unique. The short name is already taken. Use another one.'
			),
		),
		'country_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please Select Country.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	); 

	function isUniqueRegionInCountry()
	{
		$count = 0;

		if (!empty($this->data['Region']['id'])) {
			$count = $this->find('count', array('conditions' => array('Region.country_id' => $this->data['Region']['country_id'], 'Region.name' => $this->data['Region']['name'], 'Region.id <> ' => $this->data['Region']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('Region.country_id' => $this->data['Region']['country_id'], 'Region.name' => $this->data['Region']['name'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function isUniqueRegionCode()
	{
		$count = 0;
		
		if (!empty($this->data['Region']['id'])) {
			$count = $this->find('count', array('conditions' => array('Region.short' => $this->data['Region']['short'], 'Region.id <> ' => $this->data['Region']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('Region.short' => $this->data['Region']['short'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	var $belongsTo = array(
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'region_id',
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
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'region_id',
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
		'Contact' => array(
			'className' => 'Contact',
			'foreignKey' => 'region_id',
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
			'foreignKey' => 'region_id',
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
			'foreignKey' => 'region_id',
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

	function canItBeDeleted($region_id = null)
	{
		if ($this->Student->find('count', array('conditions' => array('Student.region_id' => $region_id))) > 0) {
			return false;
		} else if ($this->Contact->find('count', array('conditions' => array('Contact.region_id' => $region_id))) > 0) {
			return false;
		} else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.region_id' => $region_id))) > 0) {
			return false;
		} else if ($this->Staff->find('count', array('conditions' => array('Staff.region_id' => $region_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
