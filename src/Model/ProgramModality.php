<?php
class ProgramModality extends AppModel
{
	var $name = 'ProgramModality';
	var $displayField = 'modality';

	/* var $belongsTo = array(
        'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
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
	); */

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['ProgramModality']['code'])) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}
}
