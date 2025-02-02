<?php
class DropOut extends AppModel
{
	var $name = 'DropOut';

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	function dropOutAfterLastRegistration($student_id = null, $current_academicyear = null)
	{
		$last_registration_date = $this->Student->CourseRegistration->find('first', array('conditions' => array('CourseRegistration.student_id' => $student_id), 'order' => array('CourseRegistration.created DESC'), 'recursive' => -1));
		if (!empty($last_registration_date)) {
			
			$check_dropout = $this->find('count', array(
				'conditions' => array(
					'DropOut.student_id' => $student_id,
					'DropOut.drop_date >= ' => $last_registration_date['CourseRegistration']['created']
				)
			));

			if ($check_dropout > 0) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
}
