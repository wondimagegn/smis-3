<?php
class College extends AppModel
{
	var $name = 'College';
	var $displayField = 'name';

	var $belongsTo = array(
		'Campus' => array(
			'className' => 'Campus',
			'foreignKey' => 'campus_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'college_id',
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
		'GivenByDepartment' => array(
			'className' => 'Department',
			'foreignKey' => 'college_id',
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
		'ParticipatingDepartment' => array(
			'className' => 'ParticipatingDepartment',
			'foreignKey' => 'college_id',
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
		/* 'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'college_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		), */
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'college_id',
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
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'college_id',
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
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'college_id',
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
		'Section' => array(
			'className' => 'Section',
			'foreignKey' => 'college_id',
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
		'GradeScale' => array(
			'className' => 'GradeScale',
			'foreignKey' => 'foreign_key',
			'conditions'    => array('model' => 'College'),
			'dependent' => true,
		),
		'AcademicCalendar' => array(
			'className' => 'AcademicCalendar',
			'foreignKey' => 'college_id',
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
		'PublishedCourse' => array(
			'className' => 'PublishedCourse',
			'foreignKey' => 'college_id',
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
		'PeriodSetting' => array(
			'className' => 'PeriodSetting',
			'foreignKey' => 'college_id',
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
		'ClassPeriod' => array(
			'className' => 'ClassPeriod',
			'foreignKey' => 'college_id',
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
		'ClassRoomBlock' => array(
			'className' => 'ClassRoomBlock',
			'foreignKey' => 'college_id',
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
		'InstructorClassPeriodCourseConstraint' => array(
			'className' => 'InstructorClassPeriodCourseConstraint',
			'foreignKey' => 'college_id',
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
		'ExamPeriod' => array(
			'className' => 'ExamPeriod',
			'foreignKey' => 'college_id',
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
		'StaffForExam' => array(
			'className' => 'StaffForExam',
			'foreignKey' => 'college_id',
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
		'TakenProperty' => array(
			'className' => 'TakenProperty',
			'foreignKey' => 'college_id',
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
	);

	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Name is required'

			),
			'isUniqueCollegeInCampus' => array(
				'rule' => array('isUniqueCollegeInCampus'),
				'message' => 'The college name should be unique in the campus. The name is already taken. Use another one.'
			),
		),
	);

	function isUniqueCollegeInCampus()
	{
		$count = 0;

		if (!empty($this->data['College']['id'])) {
			$count = $this->find('count', array('conditions' => array('College.campus_id' => $this->data['College']['campus_id'], 'College.name' => trim($this->data['College']['name']), 'College.id <> ' => $this->data['College']['id'])));
		} else {
			$count = $this->find('count', array('conditions' => array('College.campus_id' => $this->data['College']['campus_id'], 'College.name' => trim($this->data['College']['name']))));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function allowDelete($id = null)
	{
		if ($this->Student->find('count', array('conditions' => array('Student.college_id' => $id))) > 0) {
			return false;
		} else {
			return true;
		}
	}

	function canItBeDeleted($college_id = null)
	{
		if ($this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.college_id' => $college_id))) > 0) {
			return false;
		}
		if ($this->Student->find('count', array('conditions' => array('Student.college_id' => $college_id))) > 0) {
			return false;
		} else if ($this->Section->find('count', array('conditions' => array('Section.college_id' => $college_id))) > 0) {
			return false;
		} else if ($this->GradeScale->find('count', array('conditions' => array('GradeScale.model' => 'College', 'GradeScale.foreign_key' => $college_id))) > 0) {
			return false;
		} else {
			return true;
		}
	}
}
