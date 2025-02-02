<?php
class GraduationStatus extends AppModel
{
	var $name = 'GraduationStatus';
	var $validate = array(
		'cgpa' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter cgpa.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter a validate CGPA.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter graduation status.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*'acadamic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select academic year.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),*/
	);

	var $belongsTo = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	function getStudentGraduationStatus($student_id = null)
	{
		$exam_status_detail = ClassRegistry::init('StudentExamStatus')->find('first', array(
			'conditions' => array(
				'StudentExamStatus.student_id' => $student_id
			),
			'recursive' => -1,
			'order' => array('StudentExamStatus.created DESC')
		));

		$student_detail = ClassRegistry::init('Student')->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id
			),
			'contain' => array(
				'GraduateList'
			)
		));

		if (!empty($student_detail) && !empty($exam_status_detail)) {

			$options = array();

			if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {

				$options['conditions'] = array('GraduationStatus.cgpa <= ' . $exam_status_detail['StudentExamStatus']['cgpa']);
				$options['conditions']['OR'][0] = array('GraduationStatus.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
				$options['conditions']['OR'][1] = array(
					'GraduationStatus.applicable_for_current_student' => 1,
					'GraduationStatus.program_id' => $student_detail['Student']['program_id'],
					'GraduationStatus.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
				);

			} else {
				$options['conditions'] = array(
					'GraduationStatus.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4),
					'GraduationStatus.cgpa <= ' . $exam_status_detail['StudentExamStatus']['cgpa']
				);
			}

			$options['order'] = array('GraduationStatus.academic_year DESC, GraduationStatus.cgpa DESC');

			$graduation_status_detail = $this->find('first', $options);

			if (isset($graduation_status_detail['GraduationStatus'])) {
				return $graduation_status_detail['GraduationStatus'];
			} else {
				return null;
			}
		} else {
			return false;
		}
	}
}
