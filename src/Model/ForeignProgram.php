<?php
class ForeignProgram extends AppModel
{
	var $name = 'ForeignProgram';
	var $displayField = 'program';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['ForeignProgram']['code'])) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}
}
