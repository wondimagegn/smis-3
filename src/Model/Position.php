<?php
class Position extends AppModel
{
	var $name = 'Position';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $validate = array(
		'position' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide position name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('checkUnique', 'position'),
				'message' => 'Position name already recorded. Use another'
			)
		),

	);

	function checkUnique()
	{
		$count = 0;
		if (!empty($this->data['Position']['id'])) {

			$count = $this->find('count', array('conditions' => array('Position.id <> ' => $this->data['Position']['id'], 'Position.position' => trim($this->data['Position']['position']))));

		} else {

			$count = $this->find('count', array(
				'conditions' => array(
					'Position.position' =>
						trim($this->data['Position']['position'])
				)
			)
			);
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}
	var $hasMany = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'position_id',
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

	public $belongsTo = array(
		'ServiceWing' => array(
			'className' => 'ServiceWing',
			'foreignKey' => 'service_wing_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
