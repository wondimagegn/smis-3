<?php
class GraduationCertificate extends AppModel
{
	var $name = 'GraduationCertificate';
	
	var $validate = array(
		'english_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Certificate title can not be empty',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'amharic_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Certificate title can not be empty',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'amharic_content' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter the amharic content',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'english_content' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter the english content.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select the academic year.',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	var $belongsTo = array(
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
		),
	);

	function getGraduationCertificate($student_id = null)
	{
		if (!empty($student_id)) {
			$student_detail = ClassRegistry::init('Student')->find('first', array(
				'conditions' => array(
					'Student.id' => $student_id
				),
				'contain' => array(
					'GraduateList',
					'Department' => array('id', 'name')
				)
			));

			$options = array();

			if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
				$options['conditions']['OR'][0] = array('GraduationCertificate.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
				$options['conditions']['OR'][1] = array(
					'GraduationCertificate.applicable_for_current_student' => 1,
					'GraduationCertificate.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
				);
			} else {
				$options['conditions']['GraduationCertificate.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
			}

			$options['conditions']['GraduationCertificate.program_id'] = $student_detail['Student']['program_id'];
			$options['conditions']['GraduationCertificate.program_type_id'] = $student_detail['Student']['program_type_id'];
			$options['order'] = array('GraduationCertificate.academic_year DESC');
			$options['conditions']['GraduationCertificate.department'] = 0;
			
			$optionsC = $options;
			$optionsD = $options;

			$optionsC['conditions']['GraduationCertificate.department'] = 'c~' . $student_detail['Student']['college_id'];
			$optionsD['conditions']['GraduationCertificate.department'] = $student_detail['Student']['department_id'];

			$GraduationCertificate_detail_all = $this->find('first', $options);
			$GraduationCertificate_detail_college = $this->find('first', $optionsC);
			$GraduationCertificate_detail_department = $this->find('first', $optionsD);

			if (!empty($GraduationCertificate_detail_department['GraduationCertificate'])) {
				return $GraduationCertificate_detail_department;
			} else if (!empty($GraduationCertificate_detail_college['GraduationCertificate'])) {
				return $GraduationCertificate_detail_college;
			} else if (!empty($GraduationCertificate_detail_all['GraduationCertificate'])) {
				return $GraduationCertificate_detail_all;
			} else {
				return array();
			}
		}
	}


	function getGraduationCertificateForMassPrint($student_ids = array())
	{
		$student_certificate_list = array();

		if (!empty($student_ids)) {
			foreach ($student_ids as $key => $student_id) {
				$student_detail = ClassRegistry::init('Student')->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					),
					'contain' => array(
						'GraduateList'
					)
				));

				$options = array();

				if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
					$options['conditions']['OR'][0] = array('GraduationCertificate.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
					$options['conditions']['OR'][1] = array(
						'GraduationCertificate.applicable_for_current_student' => 1,
						'GraduationCertificate.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
					);
				} else {
					$options['conditions']['GraduationCertificate.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
				}

				$options['conditions']['GraduationCertificate.program_id'] = $student_detail['Student']['program_id'];
				$options['conditions']['GraduationCertificate.program_type_id'] = $student_detail['Student']['program_type_id'];
				$options['conditions']['GraduationCertificate.department'] = 0;
				$options['order'] = array('GraduationCertificate.academic_year DESC');

				$optionsC = $options;
				$optionsD = $options;

				$optionsC['conditions']['GraduationCertificate.department'] = 'c~' . $student_detail['Student']['college_id'];
				$optionsD['conditions']['GraduationCertificate.department'] = $student_detail['Student']['department_id'];

				$GraduationCertificate_detail_college = $this->find('first', $optionsC);
				$GraduationCertificate_detail_department = $this->find('first', $optionsD);
				$GraduationCertificate_detail  = $this->find('first', $options);

				if (!empty($GraduationCertificate_detail_department)) {
					$student_certificate_list[] = $GraduationCertificate_detail_department;
				} else if (!empty($GraduationLetter_detail_college)) {
					$student_certificate_list[] = $GraduationCertificate_detail_college;
				} else if (!empty($GraduationLetter_detail_all)) {
					$student_certificate_list[] = $GraduationCertificate_detail;
				}
			}
		}
		return $student_certificate_list;
	}
}
