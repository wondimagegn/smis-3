<?php
class ServiceWing extends AppModel
{
	var $name = 'ServiceWing';
	var $displayField = 'name';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['ServiceWing']['name'])) {
				$this->invalidate('name_unique');
				return false;
			}
		}
		return true;
	}
}
