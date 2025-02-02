<?php
class University extends AppModel
{
	var $name = 'University';
	var $displayField = 'name';

	var $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Please provide university name, it is required.',
			),
			'checkUnique' => array(
				'rule' => array('checkUnique'),
				'message' => 'The university name should be unique. The name is already taken. Use another one.'
			),
		),
		'p_o_box' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Please provide university registrar P.O.Box, it is required.',
			),
		),
		'telephone' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Please provide university registrar telephone, it is required.',
			),
		),
		'fax' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Please provide university registrar fax, it is required.',
			),
		),
		'amharic_name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Please provide university amharic name, it is required.',
			),
		),
		/* 'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide year the name is active.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		), */
	);

	var $hasMany = array(
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array('model' => 'University'),
			'dependent' => true,
		),
	);

	function checkUnique()
	{
		$count = 0;
		if (!empty($this->data['University']['id'])) {
			$count = $this->find('count', array('conditions' => array('University.id <> ' => $this->data['University']['id'], 'University.name' => trim($this->data['University']['name']))));
		} else {
			$count = $this->find('count', array('conditions' => array('University.name' => trim($this->data['University']['name']))));
		}

		if ($count > 0) {
			return false;
		}

		return true;
	}

	function getStudentUnivrsity($student_id = null)
	{
		$student_detail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'contain' => array('GraduateList')));

		$options = array();

		if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
			$options['conditions']['OR'][0] = array('University.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
			$options['conditions']['OR'][1] = array(
				'University.applicable_for_current_student' => 1,
				'University.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
			);
		} else {
			$options['conditions'] = array('University.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
		}

		$options['order'] = array('University.academic_year' => 'DESC');
		
		$options['contain'] = array(
			'Attachment' => array(
				'order' => array('Attachment.created' => 'DESC')
			)
		);

		$university_detail = array();

		if (isset($options['conditions'])) {
			$university_detail = $this->find('first', $options);
		}

		if (isset($university_detail['University'])) {
			return $university_detail;
		} else {
			return array();
		}
	}

	function attach_temp_photo($data = null)
	{
		//unset empty inputs for attachment
		if (!empty($data['Attachment'])) {
			foreach ($data['Attachment'] as $k => &$dv) {
				if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
					unset($data['Attachment'][$k]);
				} else {
					if ($k == 0) {
						$dv['group'] = 'background';
					} else {
						$dv['group'] = 'logo';
					}
					$dv['model'] = 'University';
				}

			}

			if (empty($data['Attachment'])) {
				unset($data['Attachment']);
			}
		}

		return $data;
	}

	function getSectionUniversity($section_id)
	{
		$section_detail = ClassRegistry::init('StudentsSection')->find('first', array('conditions' => array('StudentsSection.section_id' => $section_id)));
		$student_detail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $section_detail['StudentsSection']['student_id']), 'contain' => array('GraduateList')));

		$options = array();

		if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
			$options['conditions']['OR'][0] = array('University.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
			$options['conditions']['OR'][1] = array(
				'University.applicable_for_current_student' => 1,
				'University.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
			);
		} else {
			$options['conditions'] = array('University.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
		}

		$options['order'] = array('University.academic_year' => 'DESC');
		
		$options['contain'] = array(
			'Attachment' => array(
				'order' => array('Attachment.created' => 'DESC')
			)
		);

		$university_detail = array();

		if (isset($options['conditions'])) {
			$university_detail = $this->find('first', $options);
		}

		if (isset($university_detail['University'])) {
			return $university_detail;
		} else {
			return array();
		}

	}

	function getAcceptedStudentUnivrsity($accepted_student_id = null)
	{
		$student_detail = ClassRegistry::init('AcceptedStudent')->find('first', array('conditions' => array('AcceptedStudent.id' => $accepted_student_id), 'contain' => array('Student' => array('GraduateList'))));

		$options = array();

		if (isset($student_detail['Student']['GraduateList']) && $student_detail['Student']['GraduateList']['id'] != "") {
			$options['conditions']['OR'][0] = array('University.academic_year <= ' . substr($student_detail['AcceptedStudent']['academicyear'], 0, 4));
			$options['conditions']['OR'][1] = array(
				'University.applicable_for_current_student' => 1,
				'University.academic_year <= ' . substr($student_detail['Student']['GraduateList']['graduate_date'], 0, 4)
			);
		} else {
			$options['conditions'] = array('University.academic_year <= ' . substr($student_detail['AcceptedStudent']['academicyear'], 0, 4));
		}

		$options['order'] = array('University.academic_year' => 'DESC');
		
		$options['contain'] = array(
			'Attachment' => array(
				'order' => array('Attachment.created' => 'DESC')
			)
		);

		$university_detail = array();

		if (isset($options['conditions'])) {
			$university_detail = $this->find('first', $options);
		}

		if (isset($university_detail['University'])) {
			return $university_detail;
		} else {
			return array();
		}
	}
}
