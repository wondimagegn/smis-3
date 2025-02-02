<?php
class Campus extends AppModel
{
	var $name = 'Campus';
	var $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $hasMany = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'campus_id',
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
		'DormitoryBlock' => array(
			'className' => 'DormitoryBlock',
			'foreignKey' => 'campus_id',
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
		'MealHall' => array(
			'className' => 'MealHall',
			'foreignKey' => 'campus_id',
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
				'rule' => 'notBlank',
				'message' => 'Name is required'

			),
			'checkUnique' => array(
				'rule' => array('checkUnique'),
				'message' => 'The campus name should be unique. The name is already taken. Use another one.'
			),
		),
	);

	function checkUnique()
	{
		$count = 0;
		if (!empty($this->data['Campus']['id'])) {
			$count = $this->find('count', array('conditions' => array('Campus.id <> ' => $this->data['Campus']['id'], 'Campus.name' => trim($this->data['Campus']['name']))));
		} else {
			$count = $this->find('count', array('conditions' => array('Campus.name' => trim($this->data['Campus']['name']))));
		}

		if ($count > 0) {
			return false;
		}
		
		return true;
	}

	function canItBeDeleted($campus_id = null)
	{
		if ($this->College->find('count', array('conditions' => array('College.campus_id' => $campus_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
