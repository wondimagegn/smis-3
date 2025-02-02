<?php
class Zone extends AppModel
{
	var $name = 'Zone';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide zone name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueZoneInRegion' => array(
				'rule' => array('isUniqueZoneInRegion'),
				'message' => 'The zone name must be unique in the selected region. The name is already taken. Use another one.'
			),
		),
		'short' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide zone short name.',
				'allowEmpty' => false,
				'required' => true,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUniqueZoneCode' => array(
				'rule' => array('isUniqueZoneCode'),
				'message' => 'The zone short name must be unique in the given region. The zone short name is already taken. Use another one.'
			),
		),
		'region_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please Select Region.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true,
				//'on' => 'create',
			),
		),
	); 

	function isUniqueZoneInRegion()
	{
		$count = 0;
		
		if (!empty($this->data['Zone']['id'])) {
			$count = $this->find('count', array('conditions' => array('Zone.region_id' => $this->data['Zone']['region_id'], 'Zone.name' => $this->data['Zone']['name'], 'Zone.id <> ' => $this->data['Zone']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('Zone.region_id' => $this->data['Zone']['region_id'], 'Zone.name' => $this->data['Zone']['name'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function isUniqueZoneCode()
	{
		$count = 0;

		if (!empty($this->data['Zone']['id'])) {
			$count = $this->find('count', array('conditions' => array('Zone.short' => $this->data['Zone']['short'], 'Zone.region_id' => $this->data['Zone']['region_id'], 'Zone.id <> ' => $this->data['Zone']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('Zone.short' => $this->data['Zone']['short'], 'Zone.region_id' => $this->data['Zone']['region_id'])));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	var $belongsTo = array(
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'zone_id',
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
		'Contact' => array(
			'className' => 'Contact',
			'foreignKey' => 'zone_id',
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
			'foreignKey' => 'zone_id',
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
			'foreignKey' => 'zone_id',
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

	function canItBeDeleted($zone_id = null)
	{
		if ($this->Student->find('count', array('conditions' => array('Student.zone_id' => $zone_id))) > 0) {
			return false;
		} else if ($this->Contact->find('count', array('conditions' => array('Contact.zone_id' => $zone_id))) > 0) {
			return false;
		} else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.zone_id' => $zone_id))) > 0) {
			return false;
		} else if ($this->Staff->find('count', array('conditions' => array('Staff.zone_id' => $zone_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
