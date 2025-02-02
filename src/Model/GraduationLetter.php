    <?php
	class GraduationLetter extends AppModel
	{
		var $name = 'GraduationLetter';
		var $displayField = 'title';

		var $validate = array(
			'type' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Type of letter can not be empty',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'title' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Letter title can not be empty',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'title_font_size' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Please select title font size.',
					'allowEmpty' => false,
					'required' => true,
					'last' => true, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => 'Please use only numeric value for title font size',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'content' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Please enter the content',
					'allowEmpty' => false,
					'required' => true,
					'last' => true, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'content_font_size' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Please select content font size.',
					'allowEmpty' => false,
					'required' => true,
					'last' => true, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => 'Please use only numeric value for content font size',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'academic_year' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => 'Please select academic year',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
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

		function getGraduationLetter($student_id = null, $language_proficiency = 1)
		{
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
				$options['conditions']['OR'][0] = array('GraduationLetter.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
				$options['conditions']['OR'][1] = array(
					'GraduationLetter.applicable_for_current_student' => 1,
					'GraduationLetter.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
				);
			} else {
				$options['conditions']['GraduationLetter.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
			}

			$options['conditions']['GraduationLetter.program_id'] = $student_detail['Student']['program_id'];
			$options['conditions']['GraduationLetter.program_type_id'] = $student_detail['Student']['program_type_id'];
			$options['conditions']['GraduationLetter.type'] = ($language_proficiency == 1 ? 'Language Proficiency' : 'To Whom It May Concern');
			$options['order'] = array('GraduationLetter.academic_year DESC');

			$optionsC = $options;
			$optionsD = $options;

			$optionsC['conditions']['GraduationLetter.department'] = 'c~' . $student_detail['Student']['college_id'];
			$optionsD['conditions']['GraduationLetter.department'] = $student_detail['Student']['department_id'];
			
			$GraduationLetter_detail_all = $this->find('first', $options);
			$GraduationLetter_detail_college = $this->find('first', $optionsC);
			$GraduationLetter_detail_department = $this->find('first', $optionsD);


			if (!empty($GraduationLetter_detail_department)) {
				return $GraduationLetter_detail_department;
			} else if (!empty($GraduationLetter_detail_college)) {
				return $GraduationLetter_detail_college;
			} else if (!empty($GraduationLetter_detail_all)) {
				return $GraduationLetter_detail_all;
			} else {
				return array();
			}
		}


		function getGraduationLetterByMass($student_ids = array(), $language_proficiency = 1)
		{
			$letter = array();

			if (!empty($student_ids)) {

				foreach ($student_ids as $k => $student_id) {
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
						$options['conditions']['OR'][0] = array('GraduationLetter.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
						$options['conditions']['OR'][1] = array(
							'GraduationLetter.applicable_for_current_student' => 1,
							'GraduationLetter.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
						);
					} else {
						$options['conditions']['GraduationLetter.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
					}

					$options['conditions']['GraduationLetter.program_id'] = $student_detail['Student']['program_id'];
					$options['conditions']['GraduationLetter.program_type_id'] = $student_detail['Student']['program_type_id'];
					$options['conditions']['GraduationLetter.type'] = ($language_proficiency == 1 ? 'Language Proficiency' : 'To Whom It May Concern');
					$options['order'] = array('GraduationLetter.academic_year DESC');

					$optionsC = $options;
					$optionsD = $options;

					$optionsC['conditions']['GraduationLetter.department'] = 'c~' . $student_detail['Student']['college_id'];
					$optionsD['conditions']['GraduationLetter.department'] = $student_detail['Student']['department_id'];

					$GraduationLetter_detail_all = $this->find('first', $options);
					$GraduationLetter_detail_college = $this->find('first', $optionsC);
					$GraduationLetter_detail_department = $this->find('first', $optionsD);

					if (!empty($GraduationLetter_detail_department)) {
						$letter[] = $GraduationLetter_detail_department;
					} else if (!empty($GraduationLetter_detail_college)) {
						$letter[] = $GraduationLetter_detail_college;
					} else if (!empty($GraduationLetter_detail_all)) {
						$letter[] = $GraduationLetter_detail_all;
					}
				}
			}
			return $letter;
		}
	}
