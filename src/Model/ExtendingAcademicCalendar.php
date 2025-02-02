<?php
App::uses('AppModel', 'Model');
class ExtendingAcademicCalendar extends AppModel
{
	public $belongsTo = array(
		'AcademicCalendar' => array(
			'className' => 'AcademicCalendar',
			'foreignKey' => 'academic_calendar_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'YearLevel' => array(
			'className' => 'YearLevel',
			'foreignKey' => 'year_level_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProgramType' => array(
			'className' => 'ProgramType',
			'foreignKey' => 'program_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getExtendedDays($academic_calendar_id, $yearLevel, $department_id, $program_id, $program_type_id, $activity = "")
	{
		//debug("ACID=".$academic_calendar_id.'YearLevel=='.$yearLevel.'Department=='.$department_id.'Program=='.$program_id.'ProgramType=='.$program_type_id.'Actvity=='.$activity);
		$days = $this->find('first', array(
			'conditions' => array(
				'ExtendingAcademicCalendar.academic_calendar_id' => $academic_calendar_id,
				'ExtendingAcademicCalendar.year_level_id' => $yearLevel,
				'ExtendingAcademicCalendar.department_id' => $department_id,
				'ExtendingAcademicCalendar.program_id' => $program_id,
				'ExtendingAcademicCalendar.program_type_id' => $program_type_id,
				'ExtendingAcademicCalendar.activity_type' => $activity,
			),
			'order' => array('ExtendingAcademicCalendar.created DESC'),
			'recursive' => -1
		));

		if (isset($days) && !empty($days)) {
			return $days['ExtendingAcademicCalendar']['days'];
		}

		return 0;
	}
}
