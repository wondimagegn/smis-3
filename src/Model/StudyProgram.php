<?php
class StudyProgram extends AppModel
{
	var $name = 'StudyProgram';
	var $displayField = 'study_program_name';

	public function beforeValidate($options = array())
	{
		if (!$this->id) {
			if ($this->findByName($this->data['StudyProgram']['code'])) {
				$this->invalidate('code_unique');
				return false;
			}
		}
		return true;
	}
}
