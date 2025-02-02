<?php
class PlacementType extends AppModel
{
	var $name = 'PlacementType';
	var $displayField = 'placement_type';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->checkUniqueCode()) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}

	var $validate = array(
		'code' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Code is required'

			),
			'checkUnique' => array(
				'rule' => array('checkUniqueCode'),
				'message' => 'The code should be unique. This code is already taken. Try another one.'
			),
		),
	);

	function checkUniqueCode()
	{
		$count = 0;

		if (!empty($this->data['PlacementType']['id'])) {
			$count = $this->find('count', array('conditions' => array('PlacementType.id <> ' => $this->data['PlacementType']['id'], 'PlacementType.code' => trim($this->data['PlacementType']['code']))));
		} else {
			$count = $this->find('count', array('conditions' => array('PlacementType.code' => trim($this->data['PlacementType']['code']))));
		}

		if ($count > 0) {
			return false;
		}

		return true;
	}

	function canItBeDeleted($id = null)
	{
		if (ClassRegistry::init('AcceptedStudent')->find('count', array('conditions' => array('AcceptedStudent.placement_type_id' => $id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
