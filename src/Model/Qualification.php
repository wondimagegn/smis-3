<?php
class Qualification extends AppModel
{
	var $name = 'Qualification';
	var $displayField = 'qualification';

	var $belongsTo = array(
        'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
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
	);

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['Qualification']['code'])) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}
}
