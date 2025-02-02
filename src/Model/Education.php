<?php
class Education extends AppModel
{
	var $name = 'Education';
	var $displayField = 'name';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['Education']['name'])) {
				$this->invalidate('name_unique');
				return false;
			}
		}
		return true;
	}
}
