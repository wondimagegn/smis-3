<?php
class Disability extends AppModel
{
	var $name = 'Disability';
	var $displayField = 'disability';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['Disability']['code'])) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}
}
