<?php
class CourseSubstitutionRequest extends AppModel
{
	var $name = 'CourseSubstitutionRequest';

	var $validate = array(
		'remark' => array(
			'remark' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide remark',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_be_substitued_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select course to be equivalent, it is required.',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_for_substitued_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select Equivalent courses, it is required.',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CourseForSubstitued' => array(
			'className' => 'Course',
			'foreignKey' => 'course_for_substitued_id',
			'conditions' => '',
			'fields' => array(
				'id', 'lecture_hours', 'tutorial_hours', 'laboratory_hours',
				'course_code', 'course_title', 'credit', 'department_id', 'curriculum_id'
			),
			'order' => ''
		),
		'CourseBeSubstitued' => array(
			'className' => 'Course',
			'foreignKey' => 'course_be_substitued_id',
			'conditions' => '',
			'fields' => array(
				'id', 'lecture_hours', 'tutorial_hours', 'laboratory_hours', 'course_code',
				'course_title', 'credit', 'department_id', 'curriculum_id'
			),
			'order' => ''
		)
	);

	function number_of_previously_sustitued_courses($student_id = null)
	{
	}

	function isSimilarCurriculum($data = null)
	{
		if (empty($data['CourseSubstitutionRequest']['course_for_substitued_id']) || empty($data['CourseSubstitutionRequest']['course_be_substitued_id'])) {
			return true;
		}
		//other_curriculum_id
		if (!empty($data['CourseSubstitutionRequest']['curriculum_id']) && !empty($data['CourseSubstitutionRequest']['other_curriculum_id'])) {
			if ($data['CourseSubstitutionRequest']['curriculum_id'] == $data['CourseSubstitutionRequest']['other_curriculum_id']) {
				$this->invalidate('error', 'You are trying to request course substitution for similar curriculum courses. You can not request similar curriculum courses for substitution.');
				return false;
			}
		}
		return true;
	}

	//count course substitution request not approved
	function count_substitution_request($department_ids = null)
	{
		$options = array();

		$options['conditions'] = array(
			'Student.department_id' => $department_ids,
			'Student.graduated' => 0,
			'CourseSubstitutionRequest.department_approve is null',
			'CourseSubstitutionRequest.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
		);

		$substitutionCount = $this->find('count', $options);

		return  $substitutionCount;
	}
}
