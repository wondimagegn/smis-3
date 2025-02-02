<?php
class SponsorType extends AppModel
{
	var $name = 'SponsorType';
	var $displayField = 'sponsor';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['SponsorType']['code'])) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}
}
