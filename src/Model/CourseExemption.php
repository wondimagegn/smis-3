<?php
class CourseExemption extends AppModel
{
	var $name = 'CourseExemption';
	
	var $validate = array(
		'reason' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide reason for request.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'taken_course_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide course title',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'taken_course_code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide course code.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'course_taken_credit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide credit in number',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Credit should be greather than or equal to zero',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'department_reason' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide reason',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	var $belongsTo = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions'    => array('model' => 'CourseExemption'),
			'dependent' => true,
		),
		'ExcludedCourseFromTranscript' => array(
			'className' => 'ExcludedCourseFromTranscript',
			'foreignKey' => 'course_exemption_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	/*
	function makeVersion($file, $process) {
		extract($process);
	    if ($version == 'm') {
	        return ClassRegistry::init('Queue.Job')->put(compact('file','process'));
	    }
	}
	*/

	function isCourseExempted($student_id = null, $course_id = null)
	{
		$count = $this->find('count', array(
			'conditions' => array(
				'CourseExemption.student_id' => $student_id,
				'CourseExemption.course_id' => $course_id,
				'registrar_confirm_deny' => 1,
				'department_accept_reject' => 1
			))
		);
		return $count;
	}

	function getStudentCourseExemptionCredit($student_id)
	{
		$course = $this->find('all', array(
			'conditions' => array(
				'CourseExemption.student_id' => $student_id,
				'registrar_confirm_deny' => 1,
				'department_accept_reject' => 1
			), 
			'recursive' => -1
		));

		$exemptionSums = 0;

		if (isset($course) && !empty($course)) {
			foreach ($course as $k => $v) {
				$exemptionSums += $v['CourseExemption']['course_taken_credit'];
			}
		}
		return $exemptionSums;
	}

	//count course substitution request not approved
	function count_exemption_request($role_id = null, $department_ids = null, $college_ids = null)
	{
		$options = array();

		if ($role_id == ROLE_DEPARTMENT) {
			$options['conditions'] = array(
				'Student.department_id' => $department_ids,
				'Student.graduated' => 0,
				'CourseExemption.department_accept_reject is null',
				'CourseExemption.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
			);
		} else if ($role_id == ROLE_REGISTRAR) {
			if (!empty($department_ids)) {
				$options['conditions'] = array(
					'Student.department_id is not null',
					'Student.department_id ' => $department_ids,
					'Student.graduated' => 0,
					'CourseExemption.department_accept_reject is not null',
					'CourseExemption.registrar_confirm_deny is null',
					'CourseExemption.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			} else if (!empty($college_ids)) {
				$options['conditions'] = array(
					'Student.department_id is null',
					'Student.college_id' => $college_ids,
					'Student.graduated' => 0,
					'CourseExemption.department_accept_reject is not null',
					'CourseExemption.registrar_confirm_deny is null',
					'CourseExemption.request_date >= ' => date("Y-m-d", strtotime("-".DAYS_BACK_COURSE_SUBSTITUTION." day")),
				);
			}
		}

		$exemptionCount = 0;

		if (!empty($options)) {
			debug($this->find('all', $options));
			$exemptionCount = $this->find('count', $options);
		}

		debug($exemptionCount);
		
		return  $exemptionCount;
	}

	function studentExemptedCourseList($student_id)
	{
		$exemptedCourseLists = $this->find('all', array(
			'conditions' => array(
				'CourseExemption.student_id' => $student_id,
				'registrar_confirm_deny' => 1,
				'department_accept_reject' => 1
			),
			'contain' => array('Course')
		));
		
		return $exemptedCourseLists;
	}
}
