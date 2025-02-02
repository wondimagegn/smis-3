<?php
//App::uses('AppModel', 'Model');
class Student extends AppModel
{
	public $name = 'Student';

	public $validate = array(
		'first_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter first name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'middle_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter middle name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'last_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter last name',
				'allowEmpty' => false,
				'required' => true,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'college_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter college',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'gender' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select sex',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'city_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'City is required field.',
				'allowEmpty' => true,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'region_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Region is required field.',
				'allowEmpty' => false,
				//'required' => true,
				'last' => true, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'zone_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Zone is required field.',
				'allowEmpty' => false,
				//'required' => true,
				'last' => true, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'woreda_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Woreda is required field.',
				'allowEmpty' => false,
				//'required' => true,
				'last' => true, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please provide a valid email address.',
				'allowEmpty' => true,
				'required' => false,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The email address is used by someone. Please provided unique different email.',
				'on' => 'update',
			)
		),
		'email_alternative' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please provide a valid email address.',
				'allowEmpty' => true,
				'required' => false,
			),
			/* 'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The email address is used by someone. Please provided unique different email.',
				'on' => 'update',
			) */
		),
		'ecardnumber' => array(
			'ecardnumber' => array(
				'rule' => array('notBlank'),
				'message' => 'Enter a valid ecardnumber.',
				'allowEmpty' => true,
				'required' => false,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The ecardnumber is used by someone. Please provide unique ecardnumber.',
				'on' => 'update',
			)
		),
		'phone_mobile' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter mobile phone number.',
				'allowEmpty' => true,
				'required' => false,
				'on' => 'update',
			),
			'length' => array(
				'rule' => array('checkLengthPhone', 'phone_mobile'),
				'message' => 'The phone number you provided is not correct. Please provide phone number in +251999999999 format.',
				'allowEmpty' => true,
				'required' => false,
				'on' => 'update',
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The mobile phone number is used by someone. Please provide another phone number.',
				'on' => 'update',
			)
		),
		'phone_home' => array(
			'length' => array(
				'rule' => array('checkLengthPhone', 'phone_home'),
				'message' => 'The phone number you provided is not correct. Please provide phone number in +251999999999 format.',
				'allowEmpty' => true,
				'required' => false,
				'on' => 'update',
			),
		),
		'card_number' => array(
			'length' => array(
				'rule' => array('checkLength', 'card_number'),
				'message' => 'Card number you provide is greater than 15 characters. Please provide card number in less than or equal to 15 characters.',
				'allowEmpty' => true,
				'required' => false,
			),
			'unique' => array(
				'rule' => array('checkUnique', 'card_number'),
				'message' => 'You have already used this card number. Please provide unique card number.'
			)
		),
		'student_national_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter Student National ID number.',
				'allowEmpty' => true,
				'required' => false,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'The Student National ID number is used by someone. Please provide differet National ID.',
				'on' => 'update',
			)
		),
		'studentnumber' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Student ID Number is required',
				'allowEmpty' => false,
				'last' => true,
				'required' => true,
				'on' => 'create', 
			),
			'isUniqueStudentNumber' => array(
				'rule' => array('isUniqueStudentNumber'),
				'message' => 'The the provided student number is taken. Please use another one.',
				//'on' => 'update',
			),
		),
	);

	public $virtualFields = array(
		'full_name' => "CONCAT(Student.first_name, ' ',Student.middle_name,' ',Student.last_name)",
		'full_am_name' => "CONCAT(Student.amharic_first_name, ' ',Student.amharic_middle_name,' ',Student.amharic_last_name)",
		'full_name_studentnumber' => "CONCAT(Student.first_name, ' ',Student.middle_name,' ',Student.last_name,' (',Student.studentnumber,')')",
	);

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'accepted_student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Curriculum' => array(
			'className' => 'Curriculum',
			'foreignKey' => 'curriculum_id',
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
		),
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Zone' => array(
			'className' => 'Zone',
			'foreignKey' => 'zone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Woreda' => array(
			'className' => 'Woreda',
			'foreignKey' => 'woreda_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'fields' => '',
			'order' => ''
		),
		'Specialization' => array(
			'className' => 'Specialization',
			'foreignKey' => 'specialization_id',
			'fields' => '',
			'order' => ''
		),
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasMany = array(
		'CostSharingPayment' => array(
			'className' => 'CostSharingPayment',
			'foreignKey' => 'student_id',
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
		'StudentNameHistory' => array(
			'className' => 'StudentNameHistory',
			'foreignKey' => 'student_id',
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
		'DropOut' => array(
			'className' => 'DropOut',
			'foreignKey' => 'student_id',
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
		'CourseExemption' => array(
			'className' => 'CourseExemption',
			'foreignKey' => 'student_id',
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
		'GraduationWork' => array(
			'className' => 'GraduationWork',
			'foreignKey' => 'student_id',
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
		'ExitExam' => array(
			'className' => 'ExitExam',
			'foreignKey' => 'student_id',
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
		'Otp' => array(
			'className' => 'Otp',
			'foreignKey' => 'student_id',
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
		'ApplicablePayment' => array(
			'className' => 'ApplicablePayment',
			'foreignKey' => 'student_id',
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
		'ExceptionMealAssignment' => array(
			'className' => 'ExceptionMealAssignment',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CostShare' => array(
			'className' => 'CostShare',
			'foreignKey' => 'student_id',
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
		'Payment' => array(
			'className' => 'Payment',
			'foreignKey' => 'student_id',
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
		'MakeupExam' => array(
			'className' => 'MakeupExam',
			'foreignKey' => 'student_id',
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
		'ResultEntryAssignment' => array(
			'className' => 'ResultEntryAssignment',
			'foreignKey' => 'student_id',
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
		'Contact' => array(
			'className' => 'Contact',
			'foreignKey' => 'student_id',
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
		'ProgramTypeTransfer' => array(
			'className' => 'ProgramTypeTransfer',
			'foreignKey' => 'student_id',
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
		'Clearance' => array(
			'className' => 'Clearance',
			'foreignKey' => 'student_id',
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
		'Withdrawal' => array(
			'className' => 'Withdrawal',
			'foreignKey' => 'student_id',
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
		'DepartmentTransfer' => array(
			'className' => 'DepartmentTransfer',
			'foreignKey' => 'student_id',
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
		'Readmission' => array(
			'className' => 'Readmission',
			'foreignKey' => 'student_id',
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
		'CurriculumAttachment' => array(
			'className' => 'CurriculumAttachment',
			'foreignKey' => 'student_id',
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
		'Attendance' => array(
			'className' => 'Attendance',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'EslceResult' => array(
			'className' => 'EslceResult',
			'foreignKey' => 'student_id',
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
		'EheeceResult' => array(
			'className' => 'EheeceResult',
			'foreignKey' => 'student_id',
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
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions'    => array('model' => 'Student'),
			'order' => array('Attachment.created' => 'DESC'),
			'dependent' => true,
		),
		'HigherEducationBackground' => array(
			'className' => 'HigherEducationBackground',
			'foreignKey' => 'student_id',
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
		'HighSchoolEducationBackground' => array(
			'className' => 'HighSchoolEducationBackground',
			'foreignKey' => 'student_id',
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
		'StudentExamStatus' => array(
			'className' => 'StudentExamStatus',
			'foreignKey' => 'student_id',
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
		'CourseRegistration' => array(
			'className' => 'CourseRegistration',
			'foreignKey' => 'student_id',
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
		'CourseDrop' => array(
			'className' => 'CourseDrop',
			'foreignKey' => 'student_id',
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
		'CourseAdd' => array(
			'className' => 'CourseAdd',
			'foreignKey' => 'student_id',
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
		'SenateList' => array(
			'className' => 'SenateList',
			'foreignKey' => 'student_id',
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
		'Dismissal' => array(
			'className' => 'Dismissal',
			'foreignKey' => 'student_id',
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
			'foreignKey' => 'student_id',
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
		'DormitoryAssignment' => array(
			'className' => 'DormitoryAssignment',
			'foreignKey' => 'student_id',
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
		'MealHallAssignment' => array(
			'className' => 'MealHallAssignment',
			'foreignKey' => 'student_id',
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
		'MealAttendance' => array(
			'className' => 'MealAttendance',
			'foreignKey' => 'student_id',
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
		'Discipline' => array(
			'className' => 'Discipline',
			'foreignKey' => 'student_id',
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
		'StudentRank' => array(
			'className' => 'StudentRank',
			'foreignKey' => 'student_id',
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
		'PlacementPreference' => array(
			'className' => 'PlacementPreference',
			'foreignKey' => 'student_id',
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

	public $hasOne = array(
		'GraduateList' => array(
			'className' => 'GraduateList',
			'foreignKey' => 'student_id',
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
		'Alumnus' => array(
			'className' => 'Alumnus',
			'foreignKey' => 'student_id',
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

	public $hasAndBelongsToMany = array(
		'Section' => array(
			'className' => 'Section',
			'joinTable' => 'students_sections',
			'foreignKey' => 'student_id',
			'associationForeignKey' => 'section_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'CourseSplitSection' => array(
			'className' => 'CourseSplitSection',
			'joinTable' => 'students_course_split_sections',
			'foreignKey' => 'student_id',
			'associationForeignKey' => 'course_split_section_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Course' => array(
			'className' => 'Course',
			'joinTable' => 'courses_students',
			'foreignKey' => 'student_id',
			'associationForeignKey' => 'course_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	function isUniqueStudentNumber()
	{
		$count = 0;

		if (!empty($this->data['Student']['id'])) {
			$count = $this->find('count', array(
				'conditions' => array(
					'Student.studentnumber LIKE ' => (trim($this->data['Student']['studentnumber'])) . '%', 
					'Student.id <> ' => $this->data['Student']['id']
				)
			));
		} else {
			$count = $this->find('count', array(
				'conditions' => array(
					'Student.studentnumber LIKE ' => (trim($this->data['Student']['studentnumber'])) . '%', 
				)
			));
		}

		if ($count > 0) {
			return false;
		}
		return true;
	}

	function checkLength($data, $fieldName)
	{
		$valid = true;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$check = strlen($data['card_number']);
			if ($check > 15) {
				$valid = false;
			}
		}
		return $valid;
	}

	function checkLengthPhone($data, $fieldName)
	{
		$valid = true;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$check = strlen($data[$fieldName]);
			debug($check);
			if (!empty($data[$fieldName]) && $check > 0 && ($check < 9 || $check != 13)) {
				$valid = false;
			}
		}
		return $valid;
	}

	function checkUnique($data, $fieldName)
	{
		$valid = true;
		if (isset($fieldName) && $this->hasField($fieldName)) {

			$check = $this->find('count', array('conditions' => array('Student.card_number' => $data['card_number'])));

			if ($check > 0) {
				$valid = false;
			}
		}
		return $valid;
	}

	function readAllById($id = null)
	{
		$data = $this->read(null, $id);
		return $data;
	}
	
	function countId($collegeid = null, $year = null)
	{
		$count = $this->find("count", array(
			"conditions" => array(
				'Student.admissionyear LIKE' => $year . '%', 
				'Student.college_id' => $collegeid,
				"NOT" => array(
					'studentnumber' => array('', 'null', 'NULL')
				)
			)
		));

		return $count;
	}

	function admittedMoreThanOneProgram($department_id = null)
	{
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$allAdmittedStudents = $this->query("SELECT  Student.studentnumber , Student.first_name ,  Student.middle_name ,  Student.last_name , Student.department_id,Student.program_type_id,Student.program_id , COUNT(*)
				FROM  students as Student WHERE  Student.college_id = " . $college_id[1] . " GROUP BY  Student.first_name, Student.middle_name, Student.last_name HAVING COUNT(*) > 1");
				//debug($allAdmittedStudents);
			} else {
				$allAdmittedStudents = $this->query("SELECT  Student.studentnumber , Student.first_name ,  Student.middle_name ,  Student.last_name , Student.department_id,Student.program_type_id,Student.program_id , COUNT(*)
				FROM  students as Student WHERE  Student.department_id = " . $department_id . " GROUP BY  Student.first_name, Student.middle_name, Student.last_name HAVING COUNT(*) > 1");
			}
		} else {
			$allAdmittedStudents = $this->query("SELECT  Student.studentnumber , Student.first_name ,  Student.middle_name ,  Student.last_name , Student.department_id,Student.program_type_id,Student.program_id , COUNT(*)
			FROM  students as Student GROUP BY  Student.first_name, Student.middle_name, Student.last_name HAVING COUNT(*) > 1 ");
		}

		//$formattedStudentList = array();

		if (isset($allAdmittedStudents) && !empty($allAdmittedStudents)) {
			foreach ($allAdmittedStudents as $key => $value) {
				$sameAdmitted = $this->find('all', array(
					'conditions' => array(
						'Student.first_name' => $value['Student']['first_name'],
						'Student.middle_name' => $value['Student']['middle_name'],
						'Student.last_name' => $value['Student']['last_name']
					),
					'contain' => array(
						'Department',
						'Program',
						'ProgramType'
					)
				));

				if (!empty($sameAdmitted)) {
					$formattedStudentList[$key] = $sameAdmitted;
				}
			}

			return $formattedStudentList;
		}

		return array();
	}

	// This method validates whethere a student is admitted or not
	function isAdmitted($id = null)
	{
		if ($id) {
			$count = $this->find('count', array('conditions' => array('Student.accepted_student_id' => $id)));
			if ($count > 0) {
				return true;
			} else {
				return false;
			}
		}
	}

	function student_academic_detail($id = null, $academic_year = null)
	{
		$this->bindModel(array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.archive' => 0)))));

		$student_section = $this->find('first', array(
			'conditions' => array(
				'Student.id' => $id
			),
			'fields' => array(
				'Student.id',
				'Student.studentnumber',
				'Student.full_name',
				'Student.curriculum_id',
				'Student.department_id',
				'Student.college_id',
				'Student.program_id',
				'Student.program_type_id',
				'Student.gender',
				'Student.graduated',
				'Student.academicyear'
			),
			'contain' => array(
				'Department' => array('id', 'name', 'type'),
				'College' => array('id', 'name', 'type', 'campus_id', 'stream'),
				'Program' => array('id', 'name'),
				'ProgramType' => array('id', 'name'),
				'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
				'StudentExamStatus' => array(
					'conditions' => array(
						'StudentExamStatus.academic_year LIKE ' => $academic_year . '%'
					),
					'AcademicStatus' => array('fields' => array('name'))
				),
				'StudentsSection',
			),
			'Section' => array(
				'fields' => array(
					'Section.id',
					'Section.name',
					'Section.year_level_id'
				),
				'YearLevel' => array(
					'fields' => array('id','name')
				)
			),
			'Course' => array(
				'fields' => array(
					'Course.id',
					'Course.course_code',
					'Course.lecture_hours',
					'Course.tutorial_hours',
					'Course.credit',
					'laboratory_hours'
				),
				'PublishedCourse' => array(
					'fields' => array(
						'course_id',
						'semester',
						'academic_year'
					),
				),
				'YearLevel' => array(
					'fields' => array('id','name'),
				)
			),
			'CourseRegistration' => array(
				'order' => array('CourseRegistration.semester' => 'ASC'),
				'ExamGrade' => array(
					'fields' => array(
						'id',
						'grade',
						'course_registration_id'
					)
				),
				'PublishedCourse' => array(
					'Course' => array(
						'id',
						'course_title',
						'course_code',
						'credit',
						'lecture_hours',
						'tutorial_hours',
						'laboratory_hours'
					)
				),
				'YearLevel' => array(
					'fields' => array('id','name'),
				),
				'Section' => array(
					'fields' => array('id','name'),
				)
			)
		));

		//debug($student_section);

		if (isset($student_section['StudentsSection'][0]['section_id'])) {
			$student_section_tmp = ClassRegistry::init('Section')->find('first', array('conditions' => array('Section.id' => $student_section['StudentsSection'][0]['section_id'])));
			$student_section['Section'][0] = $student_section_tmp['Section'];
		}
		
		return $student_section;
	}
	
	/* generate version
    function beforeMake($file,$process){
        extract($process);
        if ($version == $m){
            return classRegistry::ini('Queue.Job')->compact('file','process');
        }
    } */

	function get_students_curriculum_for_section($thisacademicyear = null, $college_id = null, $department_id = null, $role_id = null, $selected_program = null, $selected_program_type = null) {
		//$this->recursive = 1;
		$program_type_id = $selected_program_type;
		$find_the_equvilaent_program_type = unserialize($this->ProgramType->field('ProgramType.equivalent_to_id', array('ProgramType.id' => $selected_program_type)));
		
		if (!empty($find_the_equvilaent_program_type)) {
			$selected_program_type_array = array();
			$selected_program_type_array[] = $selected_program_type;
			$program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
		}

		if ($role_id == ROLE_DEPARTMENT) {
			$value = $this->find('all', array(
				'conditions' => array(
					'OR' => array(
						'AcceptedStudent.academicyear' => $thisacademicyear,
						'Student.academicyear' => $thisacademicyear,
					),
					'Student.department_id' => $department_id,
					'Student.program_id' => $selected_program,
					'Student.program_type_id' => $program_type_id,
					"NOT" => array('Student.curriculum_id' => array(0, 'null', ''))
				),
				'fields' => array(
					'Student.id', 
					'Student.curriculum_id'
				),
				'contain' => array(
					'Section',
					'AcceptedStudent' => array(
						'fields' => array(
							'AcceptedStudent.id',
							'AcceptedStudent.academicyear'
						)
					)
				)
			));
			return $value;
		} else {
			$value = $this->find('all', array(
				'conditions' => array(
					'OR' => array(
						'AcceptedStudent.academicyear' => $thisacademicyear,
						'Student.academicyear' => $thisacademicyear,
					),
					'Student.college_id' => $college_id,
					'Student.program_id' => $selected_program,
					'Student.program_type_id' => $program_type_id,
					"OR" => array(
						"Student.department_id is null", 
						"Student.department_id = ''"
					)
				),
				'fields' => array(
					'Student.id', 
					'Student.curriculum_id'
				),
				'contain' => array(
					'Section',
					'AcceptedStudent' => array(
						'fields' => array(
							'AcceptedStudent.id',
							'AcceptedStudent.academicyear'
						)
					)
				)
			));
			return $value;
		}
	}

	function get_students_for_countsectionlessstudent($collegeid = null, $role_id = null, $department_id = null, $year = null, $selected_program = null, $selected_program_type = null, $selected_curriculum = null) 
	{
		//$this->recursive = 1;
		if ($selected_curriculum == null) {
			$selected_curriculum = "%";
		}

		$program_type_id = $selected_program_type;

		$find_the_equvilaent_program_type = unserialize($this->ProgramType->field('ProgramType.equivalent_to_id', array('ProgramType.id' => $selected_program_type)));

		if (!empty($find_the_equvilaent_program_type)) {
			$selected_program_type_array = array();
			$selected_program_type_array[] = $selected_program_type;
			$program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
		}

		//Search using by department as well if user role is not college (use role is department)
		if ($role_id != ROLE_COLLEGE) {
			$conditions = array(
				"OR" => array(
					'AcceptedStudent.academicyear' => $year,
					'Student.academicyear' => $year,
				),
				'Student.department_id' => $department_id, 
				'Student.program_id' => $selected_program,
				'Student.program_type_id' => $program_type_id, 
				'Student.curriculum_id LIKE ' => $selected_curriculum,
				'Student.graduated' => 0
			);
		} else {
			$conditions = array(
				"OR" => array(
					'AcceptedStudent.academicyear' => $year,
					'Student.academicyear' => $year,
				),
				'Student.college_id' => $collegeid,
				'Student.program_id' => $selected_program, 
				'Student.program_type_id' => $program_type_id,
				'Student.graduated' => 0,
				"OR" => array(
					"Student.department_id is null", 
					"Student.department_id = ''",
				)
			);
		}

		$students = $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array(
				'Student.id', 
				'Student.full_name', 
				'Student.studentnumber',
				'Student.gender'
			),
			'contain' => array(
				'Section' => array(
					'fields' => array(
						'Section.id',
						'Section.name'
					)
				),
				'AcceptedStudent' => array(
					'fields' => array(
						'AcceptedStudent.id',
						'AcceptedStudent.academicyear'
					)
				)
			),
			'recursive' => -1
		));

		return $students;
	}

	function unset_empty($data = null)
	{
		if (!empty($data)) {

			$passed_student_id = (isset($data['Student']['id']) ? $data['Student']['id'] : '');

			debug($passed_student_id); 

			if (!empty($data['HighSchoolEducationBackground'])) {
				$save_highschool_education = false;
				foreach ($data['HighSchoolEducationBackground'] as $k => &$v) {
					if (empty($v['name']) && empty($v['region_id']) && empty($v['town']) && empty($v['zone']) && empty($v['school_level'])) {
						unset($data['HighSchoolEducationBackground'][$k]);
					} else {
						if (empty($v['student_id']) && !empty($passed_student_id)) {
							$v['student_id'] = $passed_student_id;
						}
						$save_highschool_education = true;
					}
				}

				if (!$save_highschool_education) {
					unset($this->data['HighSchoolEducationBackground']);
				}
			}

			if (!empty($data['HigherEducationBackground'])) {
				foreach ($data['HigherEducationBackground'] as $hk => &$hv) {
					if (empty($hv['name']) && empty($hv['field_of_study']) && empty($hv['cgpa_at_graduation']) && empty($hv['cgpa_at_graduation'])) {
						unset($data['HigherEducationBackground'][$hk]);
					} else {
						if (empty($hv['student_id']) && !empty($passed_student_id)) {
							$hv['student_id'] = $passed_student_id;
						}
					}
				}
				
				if (empty($data['HigherEducationBackground'])) {
					unset($data['HigherEducationBackground']);
				}
			}

			if (!empty($data['EslceResult'])) {
				debug($data['EslceResult']);
				$extra_ecslceResult_field = false;
				foreach ($data['EslceResult'] as $ebk => &$ebv) {
					if (empty($ebv['subject']) && empty($ebv['grade'])) {
						unset($data['EslceResult'][$ebk]);
					} else {
						if (empty($ebv['student_id']) && !empty($passed_student_id)) {
							$ebv['student_id'] = $passed_student_id;
						}
						$extra_ecslceResult_field = true;
					}

					if (isset($data['EslceResult'][0]['id']) && strlen($data['EslceResult'][0]['exam_year']) > 4) {
						$ebv['exam_year'] = $data['EslceResult'][0]['exam_year'];
					} else if (isset($data['EslceResult'][0]['exam_year']) && strlen($data['EslceResult'][0]['exam_year']) == 4) {
						$ebv['exam_year'] = $data['EslceResult'][0]['exam_year'];
					}
					
					/* if (isset($ebv['exam_year'][0]) && isset($ebv['exam_year'][1])) {
						$ebv['exam_year'] = $ebv['exam_year'][0] . '-' . $ebv['exam_year'][1] . '-' . '01';
					} else if (isset($ebv['exam_year']['month']) && isset($ebv['exam_year']['year'])) {
						$ebv['exam_year'] = $ebv['exam_year']['year'] . '-' . $ebv['exam_year']['month'] . '-' . '01';
					} */
				}

				if (!$extra_ecslceResult_field) {
					unset($data['EslceResult']);
				}
			}

			if (!empty($data['EheeceResult'])) {
				$extra_eheeceResult_field = false;
				debug($data['EheeceResult']);

				foreach ($data['EheeceResult'] as $hbk => &$hbv) {
					if (empty($hbv['subject']) && empty($hbv['mark']) || (!empty($hbv['mark']) && ((int)$hbv['mark'] > 100))) {
						unset($data['EheeceResult'][$hbk]);
					} else {
						if (empty($hbv['student_id']) && !empty($passed_student_id)) {
							$hbv['student_id'] = $passed_student_id;
						}
						$extra_eheeceResult_field = true;
					}

					
					if (isset($data['EheeceResult'][0]['id']) && is_array($data['EheeceResult'][0]['exam_year'])) {
						$hbv['exam_year'] = $data['EheeceResult'][0]['exam_year'];
					} else if (isset($data['EheeceResult'][0]['exam_year']) && is_array($data['EheeceResult'][0]['exam_year'])) {
						$hbv['exam_year'] = $data['EheeceResult'][0]['exam_year'];
					} else if (isset($data['EheeceResult'][0]['id']) && !is_array($data['EheeceResult'][0]['exam_year']) && strlen($data['EheeceResult'][0]['exam_year']) > 4) {
						$hbv['exam_year'] = $data['EheeceResult'][0]['exam_year'];
					} else if (isset($data['EheeceResult'][0]['exam_year']) && !is_array($data['EheeceResult'][0]['exam_year']) && strlen($data['EheeceResult'][0]['exam_year']) == 4) {
						$hbv['exam_year'] = $data['EheeceResult'][0]['exam_year'] . '-01-01';
					} else if (isset($hbv['exam_year']) && !is_array($hbv['exam_year']) && strlen($hbv['exam_year']) == 4) {
						$hbv['exam_year'] = $hbv['exam_year'] . '-01-01';
					} else {
						$hbv['exam_year'] = date('Y-m-d');
					}

					/* if (isset($hbv['exam_year'][0]) && isset($hbv['exam_year'][1])) {
						$hbv['exam_year'] = $hbv['exam_year'][0] . '-' . $hbv['exam_year'][1] . '-' . '01';
					} else if (isset($hbv['exam_year']['month']) && isset($hbv['exam_year']['year'])) {
						$hbv['exam_year'] = $hbv['exam_year']['year'] . '-' . $hbv['exam_year']['month'] . '-' . '01';
					} */
				}

				if (!$extra_eheeceResult_field) {
					unset($data['EheeceResult']);
				}
			}

			if (!empty($data['Attachment'])) {
				foreach ($data['Attachment'] as $k => &$dv) {
					if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
						unset($data['Attachment'][$k]);
					} else {
						$studentnumber = $this->field('Student.studentnumber', array('Student.id' => $data['Student']['id']));
						$dv['group'] = 'profile';
						$ext = substr(strtolower(strrchr($dv['file']['name'], '.')), 1); //get the extension
						$dv['file']['name'] = str_replace('/', '-', strtoupper($studentnumber)) . '.' . $ext;
						$dv['model'] = 'Student';
					}
				}
				debug($data);
			}
			return $data;
		}
	}

	// Given academic year and semester, return student load 
	function calculateStudentLoad($student_id = null, $semester = null, $academic_year = null, $detail = 0)
	{
		$credit_sum_added = 0;
		$credit_sum_registred = 0;

		$courseRegistration = ClassRegistry::init('CourseRegistration')->find('all', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.academic_year' => $academic_year,
				'CourseRegistration.semester' => $semester
			),
			'contain' => array(
				'PublishedCourse' => array(
					'Course' =>  array(
						'fields' => 'credit'
					),
				),
				'CourseDrop'
			), 
			'recursive' => -1
		));

		//debug($courseRegistration);

		$courseAdd = ClassRegistry::init('CourseAdd')->find('all', array(
			'conditions' => array(
				'CourseAdd.student_id' => $student_id,
				'CourseAdd.academic_year' => $academic_year,
				'CourseAdd.semester' => $semester,
				'CourseAdd.department_approval' => 1,
				'CourseAdd.registrar_confirmation' => 1,
			), 
			'contain' => array(
				'PublishedCourse' => array(
					'Course' =>  array(
						'fields' => 'credit'
					),
				)
			), 
			'recursive' => -1
		));

		if (!empty($courseRegistration)) {
			foreach ($courseRegistration as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id'])) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_registred += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}
		}

		if (!empty($courseAdd)) {
			foreach ($courseAdd as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_added += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}
		}

		if ($detail == 1) {
			$detail = array();
			$detail['registered'] = $credit_sum_registred;
			$detail['added'] = $credit_sum_added;
			$detail['total'] = $credit_sum_added + $credit_sum_registred;
			return $detail;
		} 

		return $credit_sum_added + $credit_sum_registred;
	}

	function calculateCumulativeStudentRegistredAddedCredit($student_id = null, $all = 0, $semester = null, $academic_year = null, $detail = 0)
	{
		$credit_sum_added = 0;
		$credit_sum_registred = 0;
		$credit_sum_exempted = 0;

		if ($all) {

			$courseRegistration = ClassRegistry::init('CourseRegistration')->find('all', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
				),
				'contain' => array(
					'PublishedCourse' => array(
						'Course' =>  array(
							'fields' => 'credit'
						),
					),
					'CourseDrop'
				),
				'order' => array('CourseRegistration.academic_year ASC', 'CourseRegistration.semester ASC' ),
				'recursive' => -1
			));
	
			$courseAdd = ClassRegistry::init('CourseAdd')->find('all', array(
				'conditions' => array(
					'CourseAdd.student_id' => $student_id,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
				), 
				'contain' => array(
					'PublishedCourse' => array(
						'Course' =>  array(
							'fields' => 'credit'
						),
					)
				),
				'order' => array('CourseAdd.academic_year ASC', 'CourseAdd.semester ASC'),
				'recursive' => -1
			));

			$courseExemtion = ClassRegistry::init('CourseExemption')->find('all', array(
				'conditions' => array(
					'CourseExemption.student_id' => $student_id,
					'CourseExemption.department_accept_reject' => 1,
					'CourseExemption.registrar_confirm_deny' => 1,
				),
				'contain' => array(
					'Course' =>  array(
						'fields' => 'credit'
					),
				),
				'recursive' => -1
			));

		} else {

			$courseRegistration = ClassRegistry::init('CourseRegistration')->find('all', array(
				'conditions' => array(
					'CourseRegistration.student_id' => $student_id,
					'CourseRegistration.academic_year' => $academic_year,
					'CourseRegistration.semester' => $semester
				),
				'contain' => array(
					'PublishedCourse' => array(
						'Course' =>  array(
							'fields' => 'credit'
						),
					),
					'CourseDrop'
				), 
				'recursive' => -1
			));

			$courseAdd = ClassRegistry::init('CourseAdd')->find('all', array(
				'conditions' => array(
					'CourseAdd.student_id' => $student_id,
					'CourseAdd.academic_year' => $academic_year,
					'CourseAdd.semester' => $semester,
					'CourseAdd.department_approval' => 1,
					'CourseAdd.registrar_confirmation' => 1,
				), 
				'contain' => array(
					'PublishedCourse' => array(
						'Course' =>  array(
							'fields' => 'credit'
						),
					)
				), 
				'recursive' => -1
			));
		}

		if (isset($courseRegistration) && !empty($courseRegistration) && !$all) {
			foreach ($courseRegistration as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id'])) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_registred += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}
		}

		if (isset($courseRegistration) && !empty($courseRegistration) && $all) {
			if (isset($semester) && isset($academic_year)) {
				$lastCourseRegistrationID = ClassRegistry::init('CourseRegistration')->find('first', array(
					'conditions' => array(
						'CourseRegistration.student_id' => $student_id,
						'CourseRegistration.academic_year' => $academic_year,
						'CourseRegistration.semester' => $semester
					),
					'order' => array('CourseRegistration.id DESC'),
					'fields' => array('CourseRegistration.id'),
					'recursive' => -1
				));
				//debug($lastCourseRegistrationID);

				foreach ($courseRegistration as $key => $value) {
					if ($value['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id']) && isset($lastCourseRegistrationID['CourseRegistration']) && $value['CourseRegistration']['id'] <= $lastCourseRegistrationID['CourseRegistration']['id']) {
						$credit_sum_registred += $value['PublishedCourse']['Course']['credit'];
					}
				}
			} else {
				foreach ($courseRegistration as $key => $value) {
					$isRegistrationFirstTime = ClassRegistry::init('ExamGrade')->isRegistrationAddForFirstTime($value['CourseRegistration']['id'], 1, 1);
					//debug($isRegistrationFirstTime);
					if ($value['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id']) && $isRegistrationFirstTime) {
						$credit_sum_registred += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}		
		}

		if (isset($courseAdd) && !empty($courseAdd) && !$all) {
			foreach ($courseAdd as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_added += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}
		}

		if (isset($courseAdd) && !empty($courseAdd) && $all) {
			if (isset($semester) && isset($academic_year)) {
				$lastCourseAddID = ClassRegistry::init('CourseAdd')->find('first', array(
					'conditions' => array(
						'CourseAdd.student_id' => $student_id,
						'CourseAdd.academic_year' => $academic_year,
						'CourseAdd.semester' => $semester,
						'CourseAdd.department_approval' => 1,
						'CourseAdd.registrar_confirmation' => 1,
					),
					'order' => array('CourseAdd.id DESC'),
					'fields' => array('CourseAdd.id'),
					'recursive' => -1
				));
				//debug($lastCourseAddID);

				foreach ($courseAdd as $key => $value) {
					if ($value['PublishedCourse']['drop'] == 0 && isset($lastCourseAddID['CourseAdd']) && $value['CourseAdd']['id'] <= $lastCourseAddID['CourseAdd']['id']) {
						$credit_sum_added += $value['PublishedCourse']['Course']['credit'];
					}
				}
			} else {
				foreach ($courseAdd as $key => $value) {
					//debug($value);
					$isAddForFirstTime = ClassRegistry::init('ExamGrade')->isRegistrationAddForFirstTime($value['CourseAdd']['id'], 0, 1);
					debug($isAddForFirstTime);

					if ($value['PublishedCourse']['drop'] == 0 && $isAddForFirstTime) {
						$credit_sum_added += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}		
		}

		if (isset($courseExemtion) && !empty($courseExemtion) && $all) {
			foreach ($courseExemtion as $key => $value) {
				$credit_sum_exempted += $value['Course']['credit'];
			}
		}

		if ($detail == 1) {
			$detail = array();
			$detail['registered'] = $credit_sum_registred;
			$detail['added'] = $credit_sum_added;
			$detail['exempted'] = $credit_sum_exempted;
			$detail['total'] = $credit_sum_added + $credit_sum_registred + $credit_sum_exempted;
			return $detail;
		} 

		return $credit_sum_added + $credit_sum_registred + $credit_sum_exempted;
	}

	function maxCreditExcludingI($student_id = null, $semester = null, $academic_year = null)
	{
		$credit_sum_added = 0;
		$credit_sum_registred = 0;
		$creditSumofI = 0;

		$courseRegistration = ClassRegistry::init('CourseRegistration')->find('all', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.academic_year' => $academic_year,
				'CourseRegistration.semester' => $semester
			),
			'contain' => array(
				'PublishedCourse' => array('Course'),
				'CourseDrop'
			)
		));

		$courseAdd = ClassRegistry::init('CourseAdd')->find('all',
			array('conditions' => array(
				'CourseAdd.student_id' => $student_id,
				'CourseAdd.academic_year' => $academic_year,
				'CourseAdd.semester' => $semester,
				'CourseAdd.department_approval' => 1,
				'CourseAdd.registrar_confirmation' => 1,
			), 
			'contain' => array(
				'PublishedCourse' => array('Course')
			)
		));

		if (!empty($courseRegistration)) {
			foreach ($courseRegistration as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id'])) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_registred += $value['PublishedCourse']['Course']['credit'];
						$grade_detail = $this->CourseRegistration->ExamGrade->getApprovedGrade($value['CourseRegistration']['id'], 1);
						//debug($grade_detail);

						if (isset($grade_detail)) {
							if (strcasecmp($grade_detail['grade'], "I") == 0) {
								$creditSumofI += $value['PublishedCourse']['Course']['credit'];
							}
						}
					}
				}
			}
		}

		if (!empty($courseAdd)) {
			foreach ($courseAdd as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_added += $value['PublishedCourse']['Course']['credit'];
						$grade_detail = $this->CourseRegistration->ExamGrade->getApprovedGrade($value['CourseAdd']['id'], 0);

						if (isset($grade_detail) && strcasecmp($grade_detail['grade'], "I") == 0) {
							$creditSumofI += $value['PublishedCourse']['Course']['credit'];
						}
					}
				}
			}
		}

		/* if ($student_id == 81595) {
			debug($creditSumofI);
		} */

		if (($credit_sum_added + $credit_sum_registred) > $creditSumofI) {
			return (($credit_sum_added + $credit_sum_registred) - $creditSumofI);
		} else {
			return $creditSumofI;
		}
	}

	function checkAllowedMaxCreditLoadPerSemester($student_id = null, $semester = null, $academic_year = null)
	{
		$attachedCurriculumID = $this->field('curriculum_id', array('Student.id' => $student_id));

		debug($attachedCurriculumID);

		$studentWithCurriculum = $this->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id
			), 
			'contain' => array(
				'Curriculum' => array(
					'fields' => array(
						'Curriculum.id',
						'Curriculum.type_credit',
					)

				)
			),
			'fields' => array(
				'Student.id',
				'Student.accepted_student_id',
				'Student.college_id',
				'Student.department_id',
				'Student.studentnumber',
				'Student.program_id',
				'Student.program_type_id',
				'Student.curriculum_id',
				'Student.graduated',
			), 
			'recursive' => -1
		));

		debug($studentWithCurriculum);

		if (!empty($studentWithCurriculum['Curriculum'])) {
			debug($studentWithCurriculum['Curriculum']['type_credit']);
			debug(count(explode('ECTS',$studentWithCurriculum['Curriculum']['type_credit'])));
			if (count(explode('ECTS',$studentWithCurriculum['Curriculum']['type_credit'])) >= 2) {
				// read general settings and multiply  by CREDIT_ECTS varialble
				
			}
		}

		$credit_sum_added = 0;
		$credit_sum_registred = 0;
		

		$courseRegistration = ClassRegistry::init('CourseRegistration')->find('all', array(
			'conditions' => array(
				'CourseRegistration.student_id' => $student_id,
				'CourseRegistration.academic_year' => $academic_year,
				'CourseRegistration.semester' => $semester
			),
			'contain' => array(
				'PublishedCourse' => array('Course'), 
				'CourseDrop'
			)
		));

		$courseAdd = ClassRegistry::init('CourseAdd')->find('all', array(
			'conditions' => array(
				'CourseAdd.student_id' => $student_id,
				'CourseAdd.academic_year' => $academic_year,
				'CourseAdd.semester' => $semester,
				'CourseAdd.department_approval' => 1,
				'CourseAdd.registrar_confirmation' => 1,
			), 
			'contain' => array(
				'PublishedCourse' => array('Course')
			)
		));

		if (!empty($courseRegistration)) {
			foreach ($courseRegistration as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id'])) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_registred += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}
		}

		if (!empty($courseAdd)) {
			foreach ($courseAdd as $key => $value) {
				if ($value['PublishedCourse']['drop'] == 0) {
					if (strcasecmp($value['PublishedCourse']['semester'], $semester) == 0 && strcasecmp($value['PublishedCourse']['academic_year'], $academic_year) == 0) {
						$credit_sum_added += $value['PublishedCourse']['Course']['credit'];
					}
				}
			}
		}
	
		return $credit_sum_added + $credit_sum_registred;
	}


	// Function to get students section and exam status
	function get_student_section($student_id = null, $academic_year = null, $semester = null)
	{
		$this->bindModel(array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.archive' => 0)))));

		$student_section = $this->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id
			),
			'fields' => array(
				'Student.studentnumber',
				'Student.full_name',
				'Student.curriculum_id',
				'Student.department_id',
				'Student.college_id',
				'Student.program_id',
				'Student.program_type_id', 
				'Student.gender',
				'Student.graduated',
				'Student.academicyear',
				'Student.admissionyear',
			),
			'contain' => array(
				'Department' => array('id', 'name', 'type'),
				'College' => array('id', 'name', 'type', 'campus_id', 'stream'),
				'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
				'Program' => array('id', 'name'),
				'ProgramType' => array('id', 'name'),
				'StudentsSection',
				'Section' => array(
					'fields' => array(
						'Section.id',
						'Section.name',
						'Section.year_level_id',
						'Section.academicyear',
						'Section.archive'
					),
					'YearLevel' => array(
						'fields' => array('id','name')
					)
				),
			),

			// needs extensive check as this function is used frequently throughout SMiS, I added and commented on purpuse for later checks (Neway)
			/* 'group' => array(
				'StudentsSection.student_id',
				'StudentsSection.section_id',
				// needs testing, group by behaves differently in mysql 8.0 and 5.7, regarding ordering, which to pick the old or new one from group by results even using order clause 
				'StudentsSection.archive'
			) */
		));

		if (!empty($academic_year) && !empty($semester)) {

			$student_latest_status = $this->StudentExamStatus->find('first', array(
				'conditions' => array(
					'StudentExamStatus.student_id' => $student_id,
					'StudentExamStatus.academic_year like ' => $academic_year . '%',
					'StudentExamStatus.semester' => $semester
				),
				'order' => array(
					'StudentExamStatus.academic_year DESC', 
					'StudentExamStatus.semester DESC'
				), 
				'contain' => array(
					'AcademicStatus' => array('id', 'name', 'computable')
				)
			));

			if (empty($student_latest_status)) {
				$previous_academic_year_semester = $this->StudentExamStatus->getPreviousSemester($academic_year, $semester);

				$student_latest_status = $this->StudentExamStatus->find('first', array(
					'conditions' => array(
						'StudentExamStatus.student_id' => $student_id,
						'StudentExamStatus.academic_year like ' => $previous_academic_year_semester['academic_year'] . '%'
					), 
					'order' => 'StudentExamStatus.created DESC ', 
					'contain' => array(
						'AcademicStatus' => array('id', 'name', 'computable')
					)
				));
			}
		} else {
			$student_latest_status = $this->StudentExamStatus->find('first', array(
				'conditions' => array(
					'StudentExamStatus.student_id' => $student_id
				), 
				'order' => 'StudentExamStatus.created DESC ', 
				'contain' => array(
					'AcademicStatus' => array('id', 'name', 'computable')
				)
			));
		}

		if (!empty($student_section)) {
			$section_detail = array();

			if (!empty($student_section['Section'])) {
				foreach ($student_section['Section'] as $kk => $vv) {
					if (!empty($vv['StudentsSection']) && $vv['StudentsSection']['archive'] == 0) {
						$section_detail['Section'] = $vv;
						$section_detail['StudentBasicInfo'] = $student_section['Student'];
						break;
					}
				}
				if (!isset($section_detail['StudentBasicInfo'])) {
					$section_detail['StudentBasicInfo'] = $student_section['Student'];
				}
			}

			$section_detail['Department'] = $student_section['Department'];
			$section_detail['College'] = $student_section['College'];
			$section_detail['Program'] = $student_section['Program'];
			$section_detail['ProgramType'] = $student_section['ProgramType'];
			//$section_detail['StudentBasicInfo']['Curriculum'] = $student_section['Curriculum'];
			$section_detail['Curriculum'] = $student_section['Curriculum'];

			if (isset($student_latest_status['StudentExamStatus'])) {
				$section_detail['StudentExamStatus'] = $student_latest_status['StudentExamStatus'];
				$section_detail['StudentExamStatus']['AcademicStatus'] = $student_latest_status['AcademicStatus'];
			}

			return $section_detail;
		}
		return array();
	}

	function getStudentRegisteredAndAddCourses($student_id = "")
	{
		$courses = array();

		if ($student_id != "" && $student_id != 0) {

			$registered_and_added_courses = $this->find('first', array(
				'conditions' => array(
					'Student.id' => $student_id
				),
				'contain' => array(
					'CourseExemption' => array(
						'conditions' => array(
							'CourseExemption.department_accept_reject' => 1,
							'CourseExemption.registrar_confirm_deny' => 1
						),
						'Course'
					),
					'CourseAdd' => array(
						'conditions' => array(
							'CourseAdd.department_approval' => 1,
							'CourseAdd.registrar_confirmation' => 1
						),
						'PublishedCourse' => array(
							'Course'
						)
					),
					'CourseRegistration' => array(
						'PublishedCourse' => array(
							'Course'
						)
					)
				)
			));

			if (isset($registered_and_added_courses['CourseRegistration']) && !empty($registered_and_added_courses['CourseRegistration'])) {
				foreach ($registered_and_added_courses['CourseRegistration'] as $key => $course_registration) {
					if (
						$course_registration['PublishedCourse']['drop'] == 0 &&
						!$this->CourseRegistration->isCourseDroped($course_registration['id'])
					) {
						$courses['Course Registered'][$course_registration['id'] . '~register'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . ') - [' . $course_registration['PublishedCourse']['academic_year'] . ' Acadamic Year ' . $course_registration['PublishedCourse']['semester'] . ' Semester]';
					}
				}
			}

			if (isset($registered_and_added_courses['CourseAdd']) && !empty($registered_and_added_courses['CourseAdd'])) {
				foreach ($registered_and_added_courses['CourseAdd'] as $key => $course_registration) {
					if ($course_registration['PublishedCourse']['drop'] == 0) {
						$courses['Course Added'][$course_registration['id'] . '~add'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . ') - [' . $course_registration['PublishedCourse']['academic_year'] . ' Acadamic Year ' . $course_registration['PublishedCourse']['semester'] . ' Semester]';
					}
				}
			}
			//debug($courses);
			//debug($registered_and_added_courses);
		}
		return $courses;
	}


	function getPossibleStudentRegisteredAndAddCoursesForSup($student_id = "")
	{
		$courses = array();

		if ($student_id != "" && $student_id != 0) {

			$studentDetails = $this->find('first', array(
				'conditions' => array(
					'Student.id' => $student_id
				),
				'fields' => array('Student.id', 'Student.department_id', 'Student.college_id', 'Student.program_id', 'Student.graduated', 'Student.academicyear'),
				'recursive' => -1
			));

			$section_ids = array('0', '0');

			if (isset($studentDetails['Student']['department_id']) && !empty($studentDetails['Student']['department_id']) && $studentDetails['Student']['department_id'] > 0) {
				$section_ids = ClassRegistry::init('Section')->find('list', array(
					'conditions' => array(
						'Section.department_id' => $studentDetails['Student']['department_id'],
						'Section.program_id' => $studentDetails['Student']['program_id'],
					),
					'fields' => array('Section.id', 'Section.id')
				));
			} else if (isset($studentDetails['Student']['college_id']) && !empty($studentDetails['Student']['college_id']) && empty($studentDetails['Student']['department_id'])) {
				$section_ids = ClassRegistry::init('Section')->find('list', array(
					'conditions' => array(
						'Section.college_id' => $studentDetails['Student']['college_id'],
						'Section.program_id' => $studentDetails['Student']['program_id'],
						'Section.academicyear' => $studentDetails['Student']['academicyear'],
					),
					'fields' => array('Section.id', 'Section.id')
				));
			}

			$possibleAllowedRepetitionGrade = array();

			if ($studentDetails['Student']['program_id'] == PROGRAM_POST_GRADUATE) {
				$possibleAllowedRepetitionGrade['C'] = 'C';
				$possibleAllowedRepetitionGrade['C+'] = 'C+';
				$possibleAllowedRepetitionGrade['D'] = 'D';
				$possibleAllowedRepetitionGrade['F'] = 'F';
				$possibleAllowedRepetitionGrade['NG'] = 'NG';
				$possibleAllowedRepetitionGrade['FAIL'] = 'FAIL';
				$possibleAllowedRepetitionGrade['I'] = 'I';
			} else {
				$possibleAllowedRepetitionGrade['C-'] = 'C-';
				$possibleAllowedRepetitionGrade['D'] = 'D';
				$possibleAllowedRepetitionGrade['F'] = 'F';
				$possibleAllowedRepetitionGrade['NG'] = 'NG';
				$possibleAllowedRepetitionGrade['FAIL'] = 'FAIL';
				$possibleAllowedRepetitionGrade['I'] = 'I';
			}

			$registered_and_added_courses = $this->find('first', array(
				'conditions' => array(
					'Student.id' => $student_id
				),
				'contain' => array(
					/* 'CourseExemption' => array(
						'conditions' => array(
							'CourseExemption.department_accept_reject' => 1,
							'CourseExemption.registrar_confirm_deny' => 1
						),
						'Course'
					), */
					'CourseAdd' => array(
						'conditions' => array(
							'CourseAdd.department_approval' => 1,
							'CourseAdd.registrar_confirmation' => 1
						),
						'PublishedCourse' => array(
							'Course'
						),
						'Student' => array('id', 'graduated')
					),
					'CourseRegistration' => array(
						'conditions' => array(
							'CourseRegistration.section_id' => $section_ids
						),
						'PublishedCourse' => array(
							'Course'
						),
						'Student' => array('id', 'graduated')
					)
				)
			));

			if (isset($registered_and_added_courses['CourseRegistration']) && !empty($registered_and_added_courses['CourseRegistration'])) {

				foreach ($registered_and_added_courses['CourseRegistration'] as $key => $course_registration) {
					
					if (!$course_registration['Student']['graduated']) {

						$graded = ClassRegistry::init('ExamGrade')->getApprovedNotChangedGrade($course_registration['id'], 1);

						if ($course_registration['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($course_registration['id']) && !ClassRegistry::init('GraduateList')->isGraduated($course_registration['student_id'])) {
							if (!empty($graded) && ((isset($graded['allow_repetition']) && $graded['allow_repetition'])|| (!empty($possibleAllowedRepetitionGrade) && in_array($graded['grade'], $possibleAllowedRepetitionGrade)))) {
								$courses['Course Registered'][$course_registration['id'] . '~register'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . '), Registered: ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester,  ' . $course_registration['PublishedCourse']['academic_year'] . '';
							}
						}
					}
				}
			}

			if (isset($registered_and_added_courses['CourseAdd']) && !empty($registered_and_added_courses['CourseAdd'])) {
				
				foreach ($registered_and_added_courses['CourseAdd'] as $key => $course_registration) {
					
					if (!$course_registration['Student']['graduated']) {

						$graded = ClassRegistry::init('ExamGrade')->getApprovedNotChangedGrade($course_registration['id'], 0);

						if ($course_registration['PublishedCourse']['drop'] == 0 && !ClassRegistry::init('GraduateList')->isGraduated($course_registration['student_id'])) {
							if (!empty($graded) && ($graded['allow_repetition'] || (!empty($possibleAllowedRepetitionGrade) && in_array($graded['grade'], $possibleAllowedRepetitionGrade)))) {
								$courses['Course Added'][$course_registration['id'] . '~add'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . '), Added: ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester, ' . $course_registration['PublishedCourse']['academic_year'] . '';
							}
						}
					}
				}
			}

			//debug($courses);
			//debug($registered_and_added_courses);
		}
		return $courses;
	}

	function get_student_details($student_id = "")
	{
		$students = $this->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id
			), 
			'contain' => array(
				'Department', 
				'College', 
				'Program', 
				'ProgramType'
			)
		));
		return $students;
	}

	/*
	Year level identification
		1. Get number of semester and year the student spends.
		2. Count full cycle (year)
		3. TODO: Deduct student dismisal taking into account the pattern
	*/
	function getListOfDepartmentStudentsByYearLevel($college_id = null, $department_id = null, $program_id = null, $program_type_id = null, $year_level_id = null, $plus_one = 1, $gender = null, $student_ids = null, $accepted_student_ids = null, $limit = 100)
	{
		$non_admitted_students = array();
		$admitted_students = array();
		$filtered_students = array();
		$given_year_level = null;
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		$currentAcademicYear = $AcademicYear->current_academicyear();
		debug($currentAcademicYear);
		if (!empty($year_level_id)) {
			if (is_numeric($year_level_id)) {
				//if year_level_id parameters is id
				$year_level_detail = ClassRegistry::init('YearLevel')->find(
					'first',
					array(
						'conditions' =>
						array(
							'YearLevel.id' => $year_level_id
						),
						'recursive' => -1
					)
				);
				$given_year_level = substr($year_level_detail['YearLevel']['name'], 0, (strlen($year_level_detail['YearLevel']['name']) - 2));
				debug($given_year_level);
			} else {
				//if year_level_id parameters is year level name
				$given_year_level = substr($year_level_id, 0, (strlen($year_level_id) - 2));
			}
		}
		$options =
			array(
				'conditions' =>
				array(
					"NOT" => array('Student.id' => $student_ids),
					'Student.id NOT IN (SELECT graduate_lists.student_id from graduate_lists)'
				),
				'fields' => array('Student.id', 'Student.studentnumber', 'Student.full_name'),
				'order' => array('Student.admissionyear DESC'),
				'recursive' => -1
			);
		if (!empty($department_id)) {
			$options['conditions'][] = array('Student.department_id' => $department_id);
		} else if (!empty($college_id)) {
			$options['conditions'][] = array('Student.college_id' => $college_id);
		}
		if (!empty($program_id)) {
			$options['conditions'][] = array('Student.program_id' => $program_id);
		}
		if (!empty($program_type_id)) {
			$options['conditions'][] = array('Student.program_type_id' => $program_type_id);
		}
		if (!empty($gender)) {
			$options['conditions'][] = array('Student.gender' => $gender);
		}
		$options['limit'] = $limit;
		$students = $this->find('all', $options);
		debug(count($students));
		foreach ($students as $key => &$student) {
			/*
			$ay_s_list = $this->CourseRegistration->ExamGrade->getListOfAyAndSemester($student['Student']['id']);
			$year_level = 0;
			if(empty($ay_s_list)) {
				$year_level = 1;
			}
			else {
				foreach($ay_s_list as $key => $ay_s) {
					if(strcasecmp($ay_s['semester'], 'I') == 0) {
						$year_level++;
					}
				}
				if($plus_one == 1) {
					$year_level++;
				}
			}
			*/
			$year_level = $this->CourseRegistration->Section->getStudentYearLevel($student['Student']['id']);
			$elegibleForAssignment = $this->StudentExamStatus->isElegibleForService($student['Student']['id'], $currentAcademicYear);
			if ((empty($given_year_level) || intval($year_level['year']) == $given_year_level) && $elegibleForAssignment == 1) {
				if ($this->StudentExamStatus->checkFxPresenseInStatus($student['Student']['id']) == 0) {
					debug($student['Student']);
					$student['Student']['fxinlaststatus'] = "Yes";
				} else {
					$student['Student']['fxinlaststatus'] = "No";
				}

				$admitted_students[] = $student;
			}
		}
		debug($admitted_students);
		if (empty($given_year_level) || $given_year_level == 1) {
			$options =
				array(
					'conditions' =>
					array(
						"NOT" => array('AcceptedStudent.id' => $accepted_student_ids),
						'AcceptedStudent.id NOT IN (SELECT students.accepted_student_id from students)'
					),
					'fields' => array('AcceptedStudent.id', 'AcceptedStudent.studentnumber', 'AcceptedStudent.full_name'),
					'recursive' => -1
				);
			if (!empty($department_id)) {
				$options['conditions'][] = array('AcceptedStudent.department_id' => $department_id);
			} else {
				$options['conditions'][] = array('AcceptedStudent.college_id' => $college_id);
			}
			if (!empty($program_id)) {
				$options['conditions'][] = array('AcceptedStudent.program_id' => $program_id);
			}
			if (!empty($program_type_id)) {
				$options['conditions'][] = array('AcceptedStudent.program_type_id' => $program_type_id);
			}
			if (!empty($gender)) {
				$options['conditions'][] = array('AcceptedStudent.sex' => $gender);
			}
			$non_admitted_students = $this->AcceptedStudent->find('all', $options);
		}
		$filtered_students['student'] = $admitted_students;
		$filtered_students['accepted_student'] = $non_admitted_students;
		return $filtered_students;
	}

	function getListOfDepartmentNonAssignedStudents($college_id = null, $program_id = null, $program_type_id = null, $gender = null, $student_ids = null, $accepted_student_ids = null, $limit = 100)
	{
		$students = array();

		$options =
			array(
				'conditions' =>
				array(
					'AcceptedStudent.department_id IS NULL',
					"NOT" => array('AcceptedStudent.id' => $accepted_student_ids),
					'AcceptedStudent.id NOT IN (SELECT students.accepted_student_id from students)'
				),
				'fields' => array('AcceptedStudent.id', 'AcceptedStudent.studentnumber', 'AcceptedStudent.full_name'),
				'recursive' => -1
			);
		if (!empty($college_id)) {
			$options['conditions'][] = array('AcceptedStudent.college_id' => $college_id);
		}
		if (!empty($program_id)) {
			$options['conditions'][] = array('AcceptedStudent.program_id' => $program_id);
		}
		if (!empty($program_type_id)) {
			$options['conditions'][] = array('AcceptedStudent.program_type_id' => $program_type_id);
		}
		if (!empty($gender)) {
			$options['conditions'][] = array('AcceptedStudent.sex' => $gender);
		}
		$non_admitted_students = $this->AcceptedStudent->find('all', $options);

		$options =
			array(
				'conditions' =>
				array(
					"NOT" => array('Student.id' => $student_ids),
					'Student.department_id IS NULL'
				),
				'fields' => array('Student.id', 'Student.studentnumber', 'Student.full_name'),
				'recursive' => -1,
				'limit' => $limit
			);
		if (!empty($college_id)) {
			$options['conditions'][] = array('Student.college_id' => $college_id);
		}
		if (!empty($program_id)) {
			$options['conditions'][] = array('Student.program_id' => $program_id);
		}
		if (!empty($program_type_id)) {
			$options['conditions'][] = array('Student.program_type_id' => $program_type_id);
		}
		if (!empty($gender)) {
			$options['conditions'][] = array('Student.gender' => $gender);
		}
		$admitted_students = $this->find('all', $options);
		$students['accepted_student'] = $non_admitted_students;
		$students['student'] = $admitted_students;
		return $students;
	}

	function get_student_details_for_health($studentnumber = null)
	{
		if (!empty($studentnumber)) {
			$students = $this->find('first', array('conditions' => array('Student.studentnumber' => $studentnumber), 'fields' => array('Student.id', 'Student.studentnumber', 'Student.full_name', 'Student.card_number', 'Student.gender', 'Student.birthdate'), 'contain' => array('College' => array('fields' => array('College.name')), 'Department' => array('fields' => array('Department.name')), 'Program' => array('fields' => array('Program.name')), 'ProgramType' => array('fields' => array('ProgramType.name')))));
			return $students;
		}
	}


	function getStudentRegisteredAddDropCurriculumResult($student_id = "", $current_academic_year = null, $for_document = 0, $includeBasicProfile = 1, $includeResult = 1, $includeExemption = 0) 
	{
		$courses = array();

		if ($student_id != "" && $student_id != 0) {
			$registered_and_added_courses = $this->find('first', array(
				'conditions' => array(
					'Student.id' => $student_id
				),
				'contain' => array(
					'CourseExemption' => array(
						'conditions' => array(
							'CourseExemption.department_accept_reject' => 1,
							'CourseExemption.registrar_confirm_deny' => 1
						),
						'Course',
						'order' => array('CourseExemption.id' => 'ASC', 'CourseExemption.course_id' => 'ASC')
					),
					'CourseAdd' => array(
						'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC'),
						'conditions' => array(
							'CourseAdd.department_approval' => 1,
							'CourseAdd.registrar_confirmation' => 1
						),
						'PublishedCourse' => array(
							'Course' => array(
								'Curriculum',
								'CourseBeSubstitued' => array(
									'fields' => array(
										'course_for_substitued_id',
										'course_be_substitued_id'
									)
								)
							),
							'Section'
						)
					),
					'CourseRegistration' => array(
						'order' => array('CourseRegistration.academic_year' => 'ASC', 'CourseRegistration.semester' => 'ASC', 'CourseRegistration.id' => 'ASC'),
						'PublishedCourse' => array(
							'Course' => array(
								'Curriculum',
								'Department',
								'CourseBeSubstitued' => array(
									'fields' => array(
										'course_for_substitued_id',
										'course_be_substitued_id'
									)
								)
							),
							'Section'
						)
					),
					'CostShare',
					'CostSharingPayment',
					'ApplicablePayment',
					'Payment'
				)
			));

			//debug($registered_and_added_courses);

			$studentAttachedCurriculumID = $this->field('curriculum_id', array('Student.id' => $student_id));
			$curriculumCourses = array();

			if ($studentAttachedCurriculumID) {
				$curriculumCourses =  ClassRegistry::init('Course')->find('list', array(
					'conditions' => array(
						'Course.curriculum_id' => $studentAttachedCurriculumID
					)
				));
			}


			$curriculumCourseIDs = array_values($curriculumCourses);

			if (!empty($registered_and_added_courses['CourseRegistration'])) {
				foreach ($registered_and_added_courses['CourseRegistration'] as $key => $course_registration) {
					if ($course_registration['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped($course_registration['id'])) {
						$courses['Course Registered'][$course_registration['id'] . '~register']['course_title'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . ')';
						$courses['Course Registered'][$course_registration['id'] . '~register']['credit'] = $course_registration['PublishedCourse']['Course']['credit'];
						$courses['Course Registered'][$course_registration['id'] . '~register']['curriculum_id'] = $course_registration['PublishedCourse']['Course']['curriculum_id'];
						$courses['Course Registered'][$course_registration['id'] . '~register']['curriculumname'] = $course_registration['PublishedCourse']['Course']['Curriculum']['name'] . ' ' . $course_registration['PublishedCourse']['Course']['Curriculum']['year_introduced'];
						$courses['Course Registered'][$course_registration['id'] . '~register']['acadamic_year'] = $course_registration['PublishedCourse']['academic_year'];
						$courses['Course Registered'][$course_registration['id'] . '~register']['semester'] = $course_registration['PublishedCourse']['semester'];
						$courses['Course Registered'][$course_registration['id'] . '~register']['sectionName'] = $course_registration['PublishedCourse']['Section']['name'];

						if ($studentAttachedCurriculumID != $course_registration['PublishedCourse']['Course']['curriculum_id']) {
							if (!empty($course_registration['PublishedCourse']['Course']['CourseBeSubstitued'])){
								foreach ($course_registration['PublishedCourse']['Course']['CourseBeSubstitued'] as $subkey => $subted) {
									if (in_array($subted['course_for_substitued_id'], $curriculumCourseIDs)) {
										$courses['Course Registered'][$course_registration['id'] . '~register']['mapped'] = $subted['course_for_substitued_id'];
									}
									$courses['Course Registered'][$course_registration['id'] . '~register']['otherCurriculum'] = 1;
								}
							}
						}

						// for generating course exclude list for course Exemption and course Adds
						$courses['Course Registered'][$course_registration['id'] . '~register']['course_id'] = $course_registration['PublishedCourse']['Course']['id'];

					} else {
						if ($this->CourseRegistration->isCourseDroped($course_registration['id'])) {
							$courses['Course Dropped'][$course_registration['id'] . '~register']['course_title'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . ')';
							$courses['Course Dropped'][$course_registration['id'] . '~register']['acadamic_year'] = $course_registration['PublishedCourse']['academic_year'];
							$courses['Course Dropped'][$course_registration['id'] . '~register']['semester'] = $course_registration['PublishedCourse']['semester'];
							$courses['Course Dropped'][$course_registration['id'] . '~register']['credit'] = $course_registration['PublishedCourse']['Course']['credit'];
							$courses['Course Dropped'][$course_registration['id'] . '~register']['curriculum_id'] = $course_registration['PublishedCourse']['Course']['curriculum_id'];
							$courses['Course Dropped'][$course_registration['id'] . '~register']['curriculumName'] = $course_registration['PublishedCourse']['Course']['Curriculum']['name'] . ' ' . $course_registration['PublishedCourse']['Course']['Curriculum']['year_introduced'];
							$courses['Course Dropped'][$course_registration['id'] . '~register']['sectionName'] = $course_registration['PublishedCourse']['Section']['name'];

							// for generating course exclude list for course Exemption and course Adds
							$courses['Course Dropped'][$course_registration['id'] . '~register']['course_id'] = $course_registration['PublishedCourse']['Course']['id'];
						}
					}
				}
			}

			if (!empty($registered_and_added_courses['CourseAdd'])) {
				foreach ($registered_and_added_courses['CourseAdd'] as $key => $course_registration) {
					if ($course_registration['PublishedCourse']['drop'] == 0) {
						$courses['Course Added'][$course_registration['id'] . '~add']['course_title'] = $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . ')';
						$courses['Course Added'][$course_registration['id'] . '~add']['credit'] = $course_registration['PublishedCourse']['Course']['credit'];
						$courses['Course Added'][$course_registration['id'] . '~add']['acadamic_year'] = $course_registration['PublishedCourse']['academic_year'];
						$courses['Course Added'][$course_registration['id'] . '~add']['semester'] = $course_registration['PublishedCourse']['semester'];
						$courses['Course Added'][$course_registration['id'] . '~add']['curriculum_id'] = $course_registration['PublishedCourse']['Course']['curriculum_id'];
						$courses['Course Added'][$course_registration['id'] . '~add']['curriculumName'] = $course_registration['PublishedCourse']['Course']['Curriculum']['name'] . ' ' . $course_registration['PublishedCourse']['Course']['Curriculum']['year_introduced'];
						$courses['Course Added'][$course_registration['id'] . '~add']['sectionName'] = $course_registration['PublishedCourse']['Section']['name'];
						

						if ($studentAttachedCurriculumID != $course_registration['PublishedCourse']['Course']['curriculum_id']) {
							if (!empty($course_registration['PublishedCourse']['Course']['CourseBeSubstitued'])){
								foreach ($course_registration['PublishedCourse']['Course']['CourseBeSubstitued'] as $subkey => $subted) {
									if (in_array($subted['course_for_substitued_id'], $curriculumCourseIDs)) {
										$courses['Course Added'][$course_registration['id'] . '~add']['mapped'] = $subted['course_for_substitued_id'];
									}
									$courses['Course Added'][$course_registration['id'] . '~add']['otherCurriculum'] = 1;
								}
							}
						}

						// for generating course exclude list for course Exemption and course Adds
						$courses['Course Added'][$course_registration['id'] . '~add']['course_id'] = $course_registration['PublishedCourse']['Course']['id'];
					}
				}
			}

			if ($includeExemption) {
				if (!empty($registered_and_added_courses['CourseExemption'])) {
					foreach ($registered_and_added_courses['CourseExemption'] as $key => $course_registration) {
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['transfer_from'] =  $course_registration['transfer_from'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['taken_course_title'] = $course_registration['taken_course_title'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['taken_course_code'] = $course_registration['taken_course_code'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['course_taken_credit'] = $course_registration['course_taken_credit'];

						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['course_title'] = $course_registration['Course']['course_title'] . ' (' . $course_registration['Course']['course_code'] . ')';
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['credit'] = $course_registration['Course']['credit'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['grade'] =  $course_registration['grade'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['curriculum_id'] = $course_registration['Course']['curriculum_id'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['curriculumName'] = $course_registration['Course']['Curriculum']['name'] . ' ' . $course_registration['Course']['Curriculum']['year_introduced'];

						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['registrar_approve_by'] =  $course_registration['registrar_approve_by'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['request_date'] =  $course_registration['request_date'];
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['registrar_approve_date'] =  $course_registration['modified'];
		

						// for generating course exclude list for course Exemption and course Adds
						$courses['Course Exempted'][$course_registration['id'] . '~exempt']['course_id'] = $course_registration['Course']['id'];
						
					}
				}
			}

			if ($includeBasicProfile) {

				$basic = $this->find('first', array(
					'conditions' => array(
						'Student.id' => $student_id
					), 
					'contain' => array(
						'Attachment' => array(
							'conditions' => array('Attachment.model' => 'Student'),
							'order' => array('Attachment.created DESC')
						), 
						'Curriculum' => array(
							'Department', 
							'Course' => array(
								'CourseCategory', 
								'Prerequisite' => array('PrerequisiteCourse'), 
								'GradeType', 
								'YearLevel' => array('id', 'name')
							), 
							'CourseCategory',
							'Program' => array('id', 'name')
						), 
						'Program', 
						'ProgramType', 
						'User',
						'Country', 
						'Region',
						'Zone',
						'Woreda',
						'City', 
						'Department', 
						'College'
					)
				));


				$courses['BasicInfo']['Student'] = $basic['Student'];
				$courses['BasicInfo']['Attachment'] = $basic['Attachment'];
				$courses['BasicInfo']['Program'] = $basic['Program'];
				$courses['BasicInfo']['Department'] = $basic['Department'];
				$courses['BasicInfo']['College'] = $basic['College'];
				$courses['BasicInfo']['ProgramType'] = $basic['ProgramType'];
				$courses['BasicInfo']['Country'] = $basic['Country'];
				$courses['BasicInfo']['Region'] = $basic['Region'];
				$courses['BasicInfo']['Curriculum'] = $basic['Curriculum'];
				$courses['CourseExemption'] = $registered_and_added_courses['CourseExemption'];
				$courses['BasicInfo']['User'] = $basic['User'];
				$courses['CostShare'] = $registered_and_added_courses['CostShare'];
				$courses['CostSharingPayment'] = $registered_and_added_courses['CostSharingPayment'];
				$courses['ApplicablePayment'] = $registered_and_added_courses['ApplicablePayment'];
				$courses['Payment'] = $registered_and_added_courses['Payment'];
				$courses['Curriculum'] = $this->Curriculum->organized_course_of_curriculum_by_year_semester($basic['Curriculum']);
				//debug($basic['Curriculum']);
			}

			//$student_ay_s_list = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester($student_id, $current_academic_year);
			// Checking if the grade hide thing will be removed if $current_academic_year and $current_semester is is set to null, Neway

			if (!$for_document) {
				$student_ay_s_list = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester($student_id, $current_academic_year);
			} else {
				$student_ay_s_list = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester($student_id, null, null);
			}

			//debug($student_ay_s_list);

			$acadamic_years = array();

			if ($includeResult) {
				if (!empty($student_ay_s_list)) {
					foreach ($student_ay_s_list as $key => $ay_s) {
						// die;
						//$acadamic_years[$ay_s['academic_year'].'~'.$ay_s['semester']] = $ay_s['academic_year'];
						$courses['Exam Result'][$ay_s['academic_year'] . '~' . $ay_s['semester']] = ClassRegistry::init('ExamGrade')->getStudentACProfile($student_id, $ay_s['academic_year'], $ay_s['semester']);
					}
				}
			}
		}

		return $courses;
	}

	function getStudentLists($student_ids = array())
	{
		$list = $this->find(
			'all',
			array('conditions' => array('Student.id ' => $student_ids), 'contain' => array())
		);
		$stu_list = array();
		if (!empty($list)) {
			foreach ($list as $in => $v) {
				$stu_list[$v['Student']['id']] = $v['Student']['full_name'] . '(' . $v['Student']['studentnumber'] . ')';
			}
		}
		return  $stu_list;
	}

	function getiProfileNotBuildList($max_not_build_time = 14)
	{
		$not_build_for = date('Y-m-d ', strtotime("-" . $max_not_build_time . " day "));

		$list = $this->find(
			'count',
			array('conditions' => array(
				'Student.id NOT IN (SELECT student_id FROM graduate_lists)', 'Student.id NOT IN (SELECT student_id FROM contacts)',
				'Student.id NOT IN (SELECT foreign_key FROM attachments where model="Student")',
				'Student.modified <= ' => $not_build_for
			), 'contain' => array(
				'GraduateList', 'Contact', 'Attachment', 'Program', 'ProgramType',
				'Department', 'College'
			))
		);

		return $list;
	}


	function getStudentPassword($student_ids)
	{
		$student_password = array();
		$student_password_data = array();
		$generated_password = null;

		if (!empty($student_ids)) {

			$return_students = array();

			foreach ($student_ids as $key => $student_id) {

				$student_password = array();
				$student_password_data = array();

				$student_details = $this->find('first', array('conditions' => array('Student.id' => $student_id['student_id']), 'contain' => array('User')));

				if (!empty($student_details)) {

					$student_password['User']['role_id'] = ROLE_STUDENT;
					$student_password['User']['is_admin'] = 0;

					$student_password['User']['username'] = trim($student_details['Student']['studentnumber']);
					$student_password['User']['password'] = $student_id['hashed_password'];


					$student_password_data['User']['role_id'] = ROLE_STUDENT;
					$student_password_data['User']['is_admin'] = 0;	

					$student_password_data['User']['username'] = trim($student_details['Student']['studentnumber']);
					$student_password_data['User']['password'] = $student_id['hashed_password'];

					$student_password_data['User']['first_name'] = $student_details['Student']['first_name'];
					$student_password_data['User']['middle_name'] = $student_details['Student']['middle_name'];
					$student_password_data['User']['last_name'] = $student_details['Student']['last_name'];

					$student_password_data['User']['email'] = (!empty($student_details['Student']['email']) ? $student_details['Student']['email'] : (str_replace('/', '.', (strtolower(trim($student_details['Student']['studentnumber'])))) . INSTITUTIONAL_EMAIL_SUFFIX));

					$student_password_data['User']['last_password_change_date'] = $student_details['Student']['created'];

					if (isset($student_details['User']['id']) && !empty($student_details['User']['id'])) {
						if (!empty($student_details['Student']['user_id']) && $student_details['Student']['studentnumber'] == $student_details['User']['username']) {
							$student_password['User']['id'] = $student_details['Student']['user_id'];
							$student_password_data['User']['id'] = $student_details['Student']['user_id'];
						} else {
							// happening for the first time so create user account
						}
					} else {
						// what if the user is created but not updating user_id in student field
						$checkFromUserTable = $this->User->find('first', array('conditions' => array('User.username' => $student_details['Student']['studentnumber'])));
						
						if ($checkFromUserTable) {
							if ($student_details['Student']['studentnumber'] == $checkFromUserTable['User']['username']) {
								$student_password['User']['id'] = $checkFromUserTable['User']['id'];
								$student_password_data['User']['id'] = $checkFromUserTable['User']['id'];
							}
						}
					}

					$student_password['User']['force_password_change'] = 1;
					$student_password_data['User']['force_password_change'] = 1;

					if (isset($student_password_data['User']) && !empty($student_password_data['User']['password'])) {
						
						if (empty($student_details['Student']['user_id'])) {
							$this->User->create();
						}

						if ($this->User->save($student_password_data)) {
							// if the issue is the first time update  student field

							if (isset($this->User->id) && !empty($this->User->id)) {
								$student_details['Student']['user_id'] = $this->User->id;
								
								$this->id = $student_id['student_id'];

								$this->saveField('user_id', $student_details['Student']['user_id']);

								if (empty($student_details['Student']['email'])) {
									$this->saveField('email', $student_password_data['User']['email']);
								}

								$this->AcceptedStudent->id = $student_details['Student']['accepted_student_id'];
								$this->AcceptedStudent->saveField('user_id', $student_details['Student']['user_id']);
							}
							unset($student_password['User']);
						} else {
							debug($student_password_data['User']);
							debug($this->User->validationErrors);
						}
					} else {
						debug($student_password_data);
					}

					$student_details_new = $this->find('first', array(
						'conditions' => array(
							'Student.id' => $student_id['student_id']
						), 
						'contain' => array(
							'College' => array('Campus'), 
							'Department', 
							'Program', 
							'ProgramType', 
							'User'
						)
					));
					
					if (!empty($student_details_new['Student'])) {

						if (!empty($student_id['flat_password'])) {
							$student_details_new['Student']['password_flat'] = $student_id['flat_password'];
							//$student_details_new['Student']['hashed_password'] = $student_id['hashed_password'];
						}

						$university_detail = ClassRegistry::init('University')->getStudentUnivrsity($student_id['student_id']);
						$student_details_new['University'] = $university_detail;
						//$student_password[] = $student_details_new;

						$return_students[] = $student_details_new;

						//debug($student_details_new);
						//$student_password_data['User'] = array();
						$student_password_data = null;
						$student_details = null;
						$student_details_new = null;
					}
				}

				$student_password = array();
				$student_password_data = array();
			}
		}
		//unset($student_password['User']);

		return $return_students;

		//return $student_password;
	}

	public function generatePassword($length = '')
	{
		$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$max = strlen($str);
		$length = @round($length);
		if (empty($length)) {
			$length = rand(8, 12);
		}
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $str{rand(0, $max - 1)};
		}
		return $password;
	}

	function listStudentByAdmissionYear($department_id = null, $college_id = null, $year = null, $name = null, $exclude_graduated = '')
	{
		$list = array();
		$yearFormated = explode('/', $year);
		
		if (empty($exclude_graduated)) {
			$graduated = array(0 => 0, 1 => 1);
		} else {
			$graduated =  $exclude_graduated;
		}
		
		$this->bindModel(array('hasMany' => array('StudentsSection')));

		if (!empty($college_id) && empty($department_id)) {
			if (!empty($name)) {
				$list = $this->find("all", array(
					"conditions" => array(
						//'YEAR(Student.admissionyear)' => $yearFormated[0],
						'Student.academicyear' => $year,
						'Student.college_id' => $college_id,
						'Student.department_id IS NULL',
						'OR' => array(
							'Student.first_name LIKE ' => '%'. (trim($name)) . '%',
							'Student.middle_name LIKE ' => '%'. (trim($name)) . '%',
							'Student.last_name LIKE ' => '%'. (trim($name)) . '%',
							'Student.studentnumber LIKE ' => '%'. (trim($name)) . '%',
						)
					),
					'contain' => array(
						'StudentsSection.archive = 0',
						'Section' => array(
							"conditions" => array(
								'Section.academicyear' => $year,
								'Section.archive' => 0,
							)
						)
					)
				));
			} else {
				$list = $this->find("all", array(
					"conditions" => array(
						//'YEAR(Student.admissionyear)' => $yearFormated[0],
						'Student.academicyear' => $year,
						'Student.college_id' => $college_id,
						'Student.department_id IS NULL'
					),
					'contain' => array(
						'StudentsSection.archive = 0',
						'Section' => array(
							"conditions" => array(
								'Section.academicyear' => $year,
								'Section.archive' => 0,
							)
						)
					)
				));
			}
		} else if (empty($collegeid) && !empty($department_id)) {
			if (!empty($name)) {
				$list = $this->find("all", array(
					"conditions" => array(
						//'Student.admissionyear LIKE'=>$year.'%',
						'Student.academicyear' => $year,
						'Student.department_id' => $department_id,
						'OR' => array(
							'Student.first_name LIKE ' => '%'. (trim($name)) . '%',
							'Student.middle_name LIKE ' => '%'. (trim($name)) . '%',
							'Student.last_name LIKE ' => '%'. (trim($name)) . '%',
							'Student.studentnumber LIKE ' => '%'. (trim($name)) . '%',
						),
						'Student.graduated' => $graduated,
					),
					'contain' => array(
						'StudentsSection.archive = 0',
						'Section' => array(
							"conditions" => array(
								'Section.academicyear' => $year,
								'Section.archive' => 0,
							)
						)
					)
				));
			} else {
				$list = $this->find("all", array(
					"conditions" => array(
						//'Student.admissionyear LIKE '=> $year.'%',
						'Student.academicyear' => $year,
						'Student.department_id' => $department_id,
						'Student.graduated' => $graduated,
					),
					'contain' => array(
						'StudentsSection.archive = 0',
						'Section' => array(
							"conditions" => array(
								'Section.academicyear' => $year,
								'Section.archive' => 0,
							)
						)
					)
				));
			}
		}

		//debug($list);
		return $list;
	}


	function getAttrationRate($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null, $year_level_id = null, $region_id = null, $sex = null)
	{

		$options = array();
		$matchingYearIds = array();

		$options['conditions']['Student.graduated'] = 1;

		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				//registered
				$options['conditions']['Student.college_id'] = $college_id[1];
			} else {
				//registered
				$options['conditions']['Student.department_id'] = $department_id;
				$matchingYearIds = ClassRegistry::init('YearLevel')->find('list', array(
					'conditions' => array(
						'YearLevel.name' => $year_level_id,
						'YearLevel.department_id' => $department_id
					), 
					'fields' => array('YearLevel.id', 'YearLevel.id')
				));
				debug($matchingYearIds);
			}
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$options['conditions']['Student.program_id'] = $program_ids[1];
			} else {
				$options['conditions']['Student.program_id'] = $program_id;
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$options['conditions']['Student.program_type_id'] = $program_type_ids[1];
			} else {
				$options['conditions']['Student.program_type_id'] = $program_type_id;
			}
		}

		if (isset($region_id) && !empty($region_id)) {
			$options['conditions']['Student.region_id'] = $region_id;
		}

		if (isset($sex) && !empty($sex)) {
			if ($sex != "all") {
				$options['conditions']['Student.gender'] = $sex;
			}
		}

		if (isset($acadamic_year) && isset($semester)) {
			$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM student_exam_statuses where academic_year="' . $acadamic_year . '" and semester="' . $semester . '")';
			if (!empty($matchingYearIds)) {
				$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM course_registrations where academic_year="' . $acadamic_year . '" and semester="' . $semester . '" and year_level_id in (' . join(',', $matchingYearIds) . '))';
			} else {
				$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM course_registrations where academic_year="' . $acadamic_year . '" and semester="' . $semester . '")';
			}
		} else {
			$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM student_exam_statuses)';
			if (!empty($matchingYearIds)) {
				$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM course_registrations where year_level_id in (' . join(',', $matchingYearIds) . '))';
			}
		}

		$options['contain'] = array(
			'StudentExamStatus'  => array(
				'order' => array('StudentExamStatus.created DESC')
			),
			'CourseRegistration'  => array(
				'limit' => 1,
				'order' => array('CourseRegistration.created DESC'),
				'id' => array(
					'Section' => array(
						'YearLevel' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
						'Department' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
						'College' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
						'Program' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
						'ProgramType' => array(
							'fields' => array(
								'id',
								'name'
							)
						),
					)
				)
			)
		);
		
		$options['fields'] = array(
			'Student.full_name', 
			'Student.first_name',
			'Student.middle_name', 
			'Student.last_name', 
			'Student.studentnumber', 
			'Student.admissionyear',
			'Student.gender',
			'Student.academicyear', 
			'Student.graduated'
		);

		$options['order'] = array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC');

		$students = $this->find('all', $options);

		$attraction_rate = array();
		$total_student = count($students);
		$yearLevelCount = array();


		if (!empty($students)) {
			foreach ($students as $key => $student) {

				if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
					if (isset($student['CourseRegistration'][0]['Section'])) {
						$section = $student['CourseRegistration'][0]['Section'];
					}
				}

				if (!isset($student['CourseRegistration'][0]['Section']) || !isset($section['Program']['name']) || !isset($section['ProgramType']['name'])) {
					continue;
				}

				if (isset($year_level_id) && !empty($year_level_id) && isset($section['YearLevel']['name']) && $section['YearLevel']['name'] == $year_level_id) {
					$yearLevelCount[$year_level_id] = $year_level_id;
				} else if (empty($year_level_id)) {
					/// initialization///////////////////////////////
					if (!isset($section['YearLevel']['name'])) {
						$yearLevelCount['1st'] = '1st';
					} else {
						$yearLevelCount[$section['YearLevel']['name']] = $section['YearLevel']['name'];
					}
				}

				//total department registred summation and initialization
				if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name']) && !empty($section['Department']['name']) && !empty($section['College']['name'])) {

					if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['total'] += 1;
					} else {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['total'] = 1;
					}

					// female initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['female_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['female_total'] = 0;
					}

					// male initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['male_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['male_total'] = 0;
					}
				}

				//total college registred summation and initialization
				if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {

					if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['total'] += 1;
					} else {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['total'] = 1;
					}

					// female initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['female_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['female_total'] = 0;
					}

					// male initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['male_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['male_total'] = 0;
					}
				}

				//total university registred summation and initialization
				if (isset($section['YearLevel']['name']) && !empty($section['YearLevel']['name'])) {

					if (isset($attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'])) {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'] += 1;
					} else {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['total'] = 1;
					}

					// female initialized
					if (!isset($attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'])) {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'] = 0;
					}

					// male initialized
					if (!isset($attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'])) {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'] = 0;
					}
				}

				//total preengineering dept registred summation and initialization
				if (empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {

					if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['total'] += 1;
					} else {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['total'] = 1;
					}

					// female initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['female_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['female_total'] = 0;
					}

					// male initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['male_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['male_total'] = 0;
					}
				}

				//total preengineering college summation and initialization
				if (empty($section['YearLevel']['name']) && !empty($section['College']['name'])) {

					if (isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['total'] += 1;
					} else {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['total'] = 1;
					}

					// female initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['female_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['female_total'] = 0;
					}

					// male initialized
					if (!isset($attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['male_total'])) {
						$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['male_total'] = 0;
					}
				}

				//total preengineering university summation and initialization
				if (empty($section['YearLevel']['name'])) {

					if (isset($attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'])) {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'] += 1;
					} else {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['total'] = 1;
					}

					// female initialized
					if (!isset($attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'])) {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'] = 1;
					}

					// male initialized
					if (!isset($attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'])) {
						$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'] = 0;
					}
				}
				//////////initialization end ///////////////////////////////

				debug($student);

				if (isset($student['StudentExamStatus'][0]['academic_status_id']) && $student['StudentExamStatus'][0]['academic_status_id'] == 4) {

					if (empty($section['Section']['department_id'])) {
						if (strcmp($student['Student']['gender'], 'male') == 0) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['male_total'] += 1;
						} else if (strcmp($student['Student']['gender'], 'female') == 0) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['Pre Engineering']['1st']['female_total'] += 1;
						}
					} else {
						if (strcmp($student['Student']['gender'], 'female') == 0) {
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['female_total'] += 1;
							}
						} else if (strcmp($student['Student']['gender'], 'male') == 0) {
							if (isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']][$section['Department']['name']][$section['YearLevel']['name']]['male_total'] += 1;
							}
						}
					}

					// sum college, university female and male  dismissed
					if (strcmp($student['Student']['gender'], 'female') == 0) {
						//total college level female dismissed
						if (isset($section['YearLevel']['name'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['female_total'] += 1;
						} else {
							if (!isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['female_total'] += 1;
							}
						}

						//university level total female dismissed
						if (isset($section['YearLevel']['name'])) {
							$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['female_total'] += 1;
						} else {
							if (!isset($section['YearLevel']['name'])) {
								$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['female_total'] += 1;
							}
						}
					} else if (strcmp($student['Student']['gender'], 'male') == 0) {

						//college level total male
						if (isset($section['YearLevel']['name'])) {
							$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College'][$section['YearLevel']['name']]['male_total'] += 1;
						} else {
							if (!isset($section['YearLevel']['name'])) {
								$attraction_rate[$section['Program']['name']][$section['ProgramType']['name']][$section['College']['name']]['College']['1st']['male_total'] += 1;
							}
						}

						//university level total male
						if (isset($section['YearLevel']['name'])) {
							$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']][$section['YearLevel']['name']]['male_total'] += 1;
						} else {
							if (!isset($section['YearLevel']['name'])) {
								$attraction_rate['University'][$section['Program']['name']][$section['ProgramType']['name']]['1st']['male_total'] += 1;
							}
						}
					}
				}
			}
		}

		// debug($attraction_rate);
		$attrationRate['attractionRate'] = $attraction_rate;
		$attrationRate['YearLevel'] = $yearLevelCount;

		return $attrationRate;
	}

	function getStudentListName($admission_year, $program_id, $program_type_id, $department_id, $year_level_id = null, $studentNumber = null, $studentName = null) 
	{
		$options['conditions']['Student.program_id'] = $program_id;

		if ($program_type_id != 0 && !empty($program_type_id)) {
			$options['conditions']['Student.program_type_id'] = $program_type_id;
		}

		$options['conditions']['Student.department_id'] = $department_id;
		$options['conditions'][] = 'Student.curriculum_id IS NOT NULL';
		$options['conditions'][] = 'Student.curriculum_id <> 0';

		$options['contain'] = array();

		if (!empty($admission_year)) {
			$admissionYear = explode('/', $admission_year);
			//$options['conditions']['YEAR(Student.admissionyear)'] = $admissionYear[0];
			//admissionyear will have issues when students are enrolled online later that their batches 
			// affects transfered students and non regular ones Neway
			$options['conditions']['Student.academicyear'] = $admissionYear;
		}

		if (!empty($studentName)) {
			$options['conditions']['Student.name LIKE '] = $studentName . '%';
		}

		if (!empty($studentNumber)) {
			unset($options);
			$options['conditions']['Student.studentnumber'] = $studentNumber;
		}

		$options['fields'] = array('Student.id', 'Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.admissionyear', 'Student.gender', 'Student.academicyear', 'Student.graduated');
		$options['order'] = array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC');

		return $this->find('all', $options);
	}

	function regenerate_academic_status_by_batch($department_college_id, $admissionAcademicYear = null, $statusAcademicYear = null, $semester = null, $all_college_dept = 0, $pre = 0, $program_id = null, $program_type_id = null)
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$options = array();
		$statusConditions['reg'] = array();
		$statusConditions['add'] = array();

		$options['conditions']['Student.graduated'] = 0;
		
		if ($department_college_id != 'all' && $pre == 1) {
			$options['conditions']['Student.college_id'] = $department_college_id;
			$options['conditions']['Student.department_id'] = null;
		}

		if (!empty($program_id)) {
			$options['conditions']['Student.program_id'] = $program_id;
		}

		if (!empty($program_type_id)) {
			$options['conditions']['Student.program_type_id'] = $program_type_id;
		}

		if ($department_college_id != 'all' && $pre == 0) {
			if ($all_college_dept == 0) {
				$options['conditions']['Student.department_id'] = $department_college_id;
			} else {
				$options['conditions']['Student.college_id'] = $department_college_id;
			}
		}

		if ($admissionAcademicYear != 'all') {
			//$options['conditions']['Student.admissionyear'] = $AcademicYear->get_academicYearBegainingDate($admissionAcademicYear);
			//admissionyear will have issues when students are enrolled online later that their batches 
			// affects transfered students and non regular ones Neway
			$options['conditions']['Student.academicyear'] = $admissionAcademicYear;
		}

		if (!empty($statusAcademicYear)) {
			$statusConditions['reg']['CourseRegistration.academic_year'] = $statusAcademicYear;
			$statusConditions['add']['CourseAdd.academic_year'] = $statusAcademicYear;
		}

		if (!empty($semester)) {
			$statusConditions['reg']['CourseRegistration.semester'] = $semester;
			$statusConditions['add']['CourseAdd.semester'] = $semester;
			$options['conditions'] = array('Student.id in (select student_id from course_registrations where semester="' . $semester . '" and academic_year="' . $statusAcademicYear . '")');
			debug($options);
		}

		$options['fields'] = array('DISTINCT Student.id', 'Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.admissionyear', 'Student.gender', 'Student.academicyear', 'Student.graduated');
		$options['order'] = array('Student.admissionyear ASC');
		$options['recursive'] = -1;
		$options['limit'] = 1000000;

		$studentLists = $this->find('all', $options);

		if (!empty($studentLists)) {
			foreach ($studentLists as $kkk => $vv) {

				$statusConditions['reg']['CourseRegistration.student_id'] = $vv['Student']['id'];
				$statusConditions['add']['CourseAdd.student_id'] = $vv['Student']['id'];

				$course_registered = ClassRegistry::init('CourseRegistration')->find('all', array(
					'conditions' => $statusConditions['reg'], 
					'order' => array(
						'CourseRegistration.academic_year' => 'ASC',
						'CourseRegistration.semester' => 'ASC'
					), 
					'contain' => array('PublishedCourse')
				));

				$course_added = ClassRegistry::init('CourseAdd')->find('all', array(
					'conditions' => $statusConditions['add'],
					'order' => array(
						'CourseAdd.academic_year ASC',
						'CourseAdd.semester ASC'
					), 
					'contain' => array('PublishedCourse')
				));

				$statusgenerated = false;

				if (!empty($course_registered)) {
					foreach ($course_registered as $rgk => $rgv) {
						$checkIfStatusIsGenerated = ClassRegistry::init('StudentExamStatus')->find('count', array(
							'conditions' => array(
								'StudentExamStatus.student_id' => $vv['Student']['id'], 
								'StudentExamStatus.academic_year' => $rgv['PublishedCourse']['academic_year'], 
								'StudentExamStatus.semester' => $rgv['PublishedCourse']['semester']
							)
						));

						if (!$checkIfStatusIsGenerated) {

							$checkTheStatusForward = ClassRegistry::init('StudentExamStatus')->find('first', array(
								'conditions' => array(
									'StudentExamStatus.student_id' => $vv['Student']['id'],
									'StudentExamStatus.academic_year >' => $rgv['PublishedCourse']['academic_year']
								)
							));

							if (!empty($checkTheStatusForward)) {
								ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id' => $vv['Student']['id'], 'StudentExamStatus.academic_year >' => $rgv['PublishedCourse']['academic_year']), false);
							}

							$statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByPublishedCourse($rgv['PublishedCourse']['id']);

							// $statusgenerated=ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($vv['Student']['id'],$rgv['PublishedCourse']['id']);
							// $statusgenerated=ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByPublishedCourseOfStudent($vv['Student']['id'],$rgv['PublishedCourse']['id']);

							echo 'Done=' . $vv['Student']['id'] . '=' . $statusgenerated;

						} else {
							//check if there is any grade chanage
							$gradeChanage = ClassRegistry::init('ExamGrade')->getApprovedGrade($rgv['CourseRegistration']['id'], 1);

							if (!empty($gradeChanage)) {

								$examGradeChanage = ClassRegistry::init('ExamGradeChange')->find('first', array(
									'conditions' => array(
										'ExamGradeChange.exam_grade_id' => $gradeChanage['grade_id']
									), 
									'recursive' => -1
								));

								if (!empty($examGradeChanage['ExamGradeChange'])) {
									$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($examGradeChanage['ExamGradeChange']['id']);
								}
							} else {
								// $updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($rgv['CourseRegistration']['id'],null);
							}
						}
					}
				}

				if (!empty($course_added)) {
					foreach ($course_added as $adk => $adv) {

						$checkIfStatusIsGenerated = ClassRegistry::init('Student')->StudentExamStatus->find('count', array(
							'conditions' => array(
								'StudentExamStatus.student_id' => $vv['Student']['id'], 
								'StudentExamStatus.academic_year' => $adv['PublishedCourse']['academic_year'], 
								'StudentExamStatus.semester' => $adv['PublishedCourse']['semester']
							)
						));

						if (!$checkIfStatusIsGenerated) {
							//does the student have forward status ?
							$checkTheStatusForward = ClassRegistry::init('StudentExamStatus')->find('first', array(
								'conditions' => array(
									'StudentExamStatus.student_id' => $vv['Student']['id'],
									'StudentExamStatus.academic_year >' => $adv['PublishedCourse']['academic_year']
								)
							));

							if (!empty($checkTheStatusForward)) {
								ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id' => $vv['Student']['id']), false);
							}

							// $statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByStudent($vv['Student']['id'],$adv['PublishedCourse']['id']);
							// $statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByPublishedCourse($adv['PublishedCourse']['id']);
					
							$statusgenerated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusByPublishedCourseOfStudent($adv['PublishedCourse']['id'], $vv['Student']['id']);

							echo 'Done=' . $vv['Student']['id'] . '=' . $statusgenerated;
						} else {
							//check if there is any grade chanage
							$gradeChanage = ClassRegistry::init('ExamGrade')->getApprovedGrade($adv['CourseRegistration']['id'], 1);
							if (!empty($gradeChanage)) {
								$examGradeChanage = ClassRegistry::init('ExamGradeChange')->find('first', array('conditions' => array('ExamGradeChange.exam_grade_id' => $gradeChanage['grade_id']), 'recursive' => -1));
								$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($examGradeChanage['ExamGradeChange']['id']);
							} else {
								$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($adv['CourseAdd']['id'], null);
							}
						}
					}
				}
			}
		}
		return "DONE";
	}


	function update_academic_status_by_batch($department_college_id, $admissionAcademicYear = null, $statusAcademicYear = null, $semester = null, $all_college_dept = 0, $pre = 0, $program_id = null, $program_type_id = null) 
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$options = array();
		$statusConditions['reg'] = array();
		$statusConditions['add'] = array();

		$options['conditions']['Student.graduated'] = 0;
		
		if ($department_college_id != 'all' && $pre == 1) {
			$options['conditions']['Student.college_id'] = $department_college_id;
			$options['conditions']['Student.department_id'] = null;
		}

		if (!empty($program_id)) {
			$options['conditions']['Student.program_id'] = $program_id;
		}

		if (!empty($program_type_id)) {
			$options['conditions']['Student.program_type_id'] = $program_type_id;
		}

		if ($department_college_id != 'all' && $pre == 0) {
			if ($all_college_dept == 0) {
				$options['conditions']['Student.department_id'] = $department_college_id;
			} else {
				$options['conditions']['Student.college_id'] = $department_college_id;
			}
		}

		if ($admissionAcademicYear != 'all') {
			//$options['conditions']['Student.admissionyear'] = $AcademicYear->get_academicYearBegainingDate($admissionAcademicYear);
			//admissionyear will have issues when students are enrolled online later that their batches 
			// affects transfered students and non regular ones Neway
			$options['conditions']['Student.academicyear'] = $admissionAcademicYear;
		}

		if (!empty($statusAcademicYear)) {
			$statusConditions['reg']['CourseRegistration.academic_year'] = $statusAcademicYear;
			$statusConditions['add']['CourseAdd.academic_year'] = $statusAcademicYear;
		}

		if (!empty($semester)) {
			$statusConditions['reg']['CourseRegistration.semester'] = $semester;
			$statusConditions['add']['CourseAdd.semester'] = $semester;
		}

		$options['fields'] = array('DISTINCT Student.id', 'Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.admissionyear', 'Student.gender', 'Student.academicyear', 'Student.graduated');
		$options['order'] = array('Student.admissionyear ASC');
		$options['recursive'] = -1;
		$options['limit'] = 10000;

		$studenLists = $this->find('all', $options);
		debug(count($studenLists));

		if (!empty($studenLists)) {
			foreach ($studenLists as $kkk => $vv) {

				$statusConditions['reg']['CourseRegistration.student_id'] = $vv['Student']['id'];
				$statusConditions['add']['CourseAdd.student_id'] = $vv['Student']['id'];

				$course_registered = ClassRegistry::init('CourseRegistration')->find('list', array(
					'conditions' => $statusConditions['reg'], 'order' => array(
						'CourseRegistration.academic_year ASC',
						'CourseRegistration.semester ASC'
					), 
					'fields' => array('CourseRegistration.id', 'CourseRegistration.id'),
					'recursive' => -1
				));

				$course_added = ClassRegistry::init('CourseAdd')->find('list', array(
					'conditions' => $statusConditions['add'],
					'order' => array(
						'CourseAdd.academic_year ASC',
						'CourseAdd.semester ASC'
					),
					'fields' => array('CourseAdd.id', 'CourseAdd.id'),
					'recursive' => -1
				));

				if (!empty($course_registered)) {
					foreach ($course_registered as $rk => $rv) {
						//check if there is grade change ?
						$gradeChanage = ClassRegistry::init('ExamGrade')->getApprovedGrade($rv, 1);
						if (!empty($gradeChanage)) {
							$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($gradeChanage['grade_id']);
						} else {
							$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($rv, null);
						}
						echo $updated;
					}
				}

				if (!empty($course_added)) {
					foreach ($course_added as $ak => $av) {
						//check if there is grade change ?
						$gradeChanage = ClassRegistry::init('ExamGrade')->getApprovedGrade($rv, 0);
						if (!empty($gradeChanage)) {
							$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($gradeChanage['grade_id']);
						} else {
							$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($av, 'add');
						}
						echo $updated;
					}
				}
			}
		}
	}


	function updateAcademicStatus($startingFrom = 2)
	{
		$days_after_gradechange_approved = $startingFrom;

		$recent_grade_change_date_from =date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('n'), date('j') - $days_after_gradechange_approved, date('Y')));
		$recent_grade_change_date_to = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('n'), date('j'), date('Y')));

		//It is if there is a makeup exam with NG
		$mostRecentGradeChange = ClassRegistry::init('ExamGradeChange')->find('all', array(
			'conditions' => array(
				'ExamGradeChange.registrar_approval = 1',
				'ExamGradeChange.created >= \'' . $recent_grade_change_date_from . '\'',
				'ExamGradeChange.created <= \'' . $recent_grade_change_date_to . '\'',
			),
			'contain' => array(
				'ExamGrade' => array(
					'CourseRegistration',
					'CourseAdd',
					'MakeupExam'
				)
			)
		));

		if (!empty($mostRecentGradeChange)) {
			foreach ($mostRecentGradeChange as $k => $vv) {
				$updated = ClassRegistry::init('StudentExamStatus')->updateAcdamicStatusForGradeChange($vv['ExamGradeChange']['id']);
			}
		}
		echo "Done!";
	}

	function updateMissingAcademicStatus($studentId)
	{
		$semesters = array('I', 'II', 'III');

		$acRange = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester($studentId);

		if (!empty($acRange)) {
			foreach ($acRange as $k => $value) {
			}
		}

		debug($acRange);
		/* 
		if (!empty($acRange)) {
			foreach ($acRange as $k => $acv) {
				foreach ($semesters as $v) {
					debug($v);
					$registrationList = ClassRegistry::init('CourseRegistration')->find('all', array(
							'conditions' => array(
								'CourseRegistration.student_id not in (select student_id from student_exam_statuses where  academic_year="' . $acv . '" and semester="' . $v . '")',
								'CourseRegistration.academic_year' => $acv,
								'CourseRegistration.semester' => $v,
							),
							'recursive' => -1,
							'order' => array(
								'CourseRegistration.academic_year DESC',
								'CourseRegistration.semester DESC'
							)
						)
					);
					//debug($registrationList);
					debug($acv);
					$statusgenerated = false;

					if (!empty($registrationList)) {
						foreach ($registrationList as $k => $value) {
							$checkTheStatusForward = ClassRegistry::init('Student')->StudentExamStatus->find('first', array(
								'conditions' => array(
									'StudentExamStatus.student_id' => $value['CourseRegistration']['student_id'],
									'StudentExamStatus.academic_year >' => $value['CourseRegistration']['academic_year']
								)
							));
							debug($checkTheStatusForward);

							if (!empty($checkTheStatusForward)) {
								ClassRegistry::init('StudentExamStatus')->deleteAll(array('StudentExamStatus.student_id' => $checkTheStatusForward['StudentExamStatus']['student_id']), false);
							}

							$checkIfStatusIsGenerated = ClassRegistry::init('Student')->StudentExamStatus->find('count', array(
								'conditions' => array(
									'StudentExamStatus.student_id' => $value['CourseRegistration']['student_id'],
									'StudentExamStatus.academic_year' => $value['CourseRegistration']['academic_year'],
									'StudentExamStatus.semester' => $value['CourseRegistration']['semester']
								)
							));
							debug($value['CourseRegistration']['student_id']);

							if (!$checkIfStatusIsGenerated) {
								debug($checkIfStatusIsGenerated);
								$statusgenerated = ClassRegistry::init('Student')->StudentExamStatus->updateAcdamicStatusByStudent($value['CourseRegistration']['student_id'], $value['CourseRegistration']['published_course_id']);
								echo 'Done=' . $value['CourseRegistration']['student_id'] . '=' . $statusgenerated;
							}
						}
					}
				}
			}
		}
		*/
	}

	function rankUpdate($academicYear, $department_id = 0)
	{

		$semesters = array("I", "II", "III");
		$rankCategory = array("cgpa", "sgpa");
		$count = 0;
		$studentList = array();

		foreach ($rankCategory as $catvalue) {
			foreach ($semesters as $value) {
				if (!empty($department_id)) {
					$studentssections = $this->find('all', array(
						'conditions' => array(
							'Student.department_id' => $department_id,
							'Student.id in (select student_id from course_registrations where academic_year="' . $academicYear . '" and semester="' . $value . '")'
						),
						'recursive' => -1
					));
				} else {
					$studentssections = $this->find('all', array(
						'conditions' => array(
							'Student.id in (select student_id from course_registrations where academic_year="' . $academicYear . '" and semester="' . $value . '")'
						), 
						'recursive' => -1
					));
				}

				if (!empty($studentssections)) {
					foreach ($studentssections as $key => $student) {
						//check if rank has already done and update z new one
						$rank = $this->StudentExamStatus->getACSemRank($student['Student']['id'], $academicYear, $value, $catvalue);
						//   debug($rank);
						//die;
						if (!empty($rank)) {
							$rankDoneEarly = $this->StudentRank->find('first', array(
								'conditions' => array(
									'StudentRank.student_id' => $student['Student']['id'], 
									'StudentRank.semester' => $value,
									'StudentRank.academicyear' => $academicYear,
									'StudentRank.category' => $catvalue
								)
							));

							$studentList['StudentRank'] = $rank['Rank'];
							if (!empty($rankDoneEarly)) {
								$studentList['StudentRank']['id'] = $rankDoneEarly['StudentRank']['id'];
							}
						}

						if (!empty($studentList['StudentRank'])) {
							if (!isset($studentList['StudentRank']['id']) && empty($studentList['StudentRank']['id'])) {
								$this->StudentRank->create();
							}
							if ($this->StudentRank->save($studentList)) {
							}
						}
						$count++;
					}
				}
			}
		}
	}


	function getProfileNotBuildList($max_not_build_time = null, $department_ids = null, $college_ids = null, $program_ids = null, $program_type_ids = null)
	{

		$list = array();

		if (isset($max_not_build_time) && !empty($max_not_build_time)){
			$not_build_for = date('Y-m-d ', strtotime("-" . $max_not_build_time . " day "));
		} else {
			$not_build_for = date('Y-m-d ', strtotime("-" . DAYS_BACK_PROFILE . " day "));
		}

		if (!empty($program_ids) && is_array($program_ids) && in_array(PROGRAM_REMEDIAL, $program_ids)) {
			unset($program_ids[PROGRAM_REMEDIAL]);
		} else if (!empty($program_ids) && !is_array($program_ids) && $program_ids == PROGRAM_REMEDIAL) {
			return $list;
		}

		if (!empty($department_ids)) {
			$list = $this->find('all', array(
				'conditions' => array(
					'Student.program_id' => $program_ids,
					'Student.program_type_id' => $program_type_ids,
					'Student.graduated' => 0,
					'Student.department_id' => $department_ids,
					'Student.id NOT IN (SELECT student_id FROM contacts)',
					//'Student.id NOT IN (SELECT foreign_key FROM attachments where model="Student")',
					//'Student.modified <= ' => $not_build_for,
					'Student.created >= ' => $not_build_for
				), 
				'contain' => array(
					//'GraduateList', 
					'Contact', 
					//'Attachment', 
					'Program' => array('fields' => array('id','name')), 
					'ProgramType' => array('fields' => array('id','name')),
					'Department' => array('fields' => array('id','name')), 
					'College' => array('fields' => array('id','name'))
				),
				'recursive' => -1
			));
		} else if (!empty($college_ids)){
			$list = $this->find('all', array(
				'conditions' => array(
					'Student.program_id' => $program_ids,
					'Student.program_type_id' => $program_type_ids,
					'Student.graduated' => 0,
					'Student.department_id IS NULL',
					'Student.college_id' => $college_ids,
					'Student.id NOT IN (SELECT student_id FROM contacts)',
					//'Student.id NOT IN (SELECT foreign_key FROM attachments where model="Student")',
					//'Student.modified <= ' => $not_build_for,
					'Student.created >= ' => $not_build_for
				), 
				'contain' => array(
					//'GraduateList', 
					'Contact', 
					//'Attachment', 
					'Program' => array('fields' => array('id','name')), 
					'ProgramType' => array('fields' => array('id','name')),
					'Department' => array('fields' => array('id','name')), 
					'College' => array('fields' => array('id','name'))
				),
				'recursive' => -1
			));
		}

		return $list;
	}

	function getProfileNotBuildListCount($max_not_build_time = null, $department_ids = null, $college_ids = null, $program_ids = null, $program_type_ids = null)
	{

		$count = 0;

		if (isset($max_not_build_time) && !empty($max_not_build_time)){
			$not_build_for = date('Y-m-d ', strtotime("-" . $max_not_build_time . " day "));
		} else {
			$not_build_for = date('Y-m-d ', strtotime("-" . DAYS_BACK_PROFILE . " day "));
		}

		if (!empty($program_ids) && is_array($program_ids) && in_array(PROGRAM_REMEDIAL, $program_ids)) {
			unset($program_ids[PROGRAM_REMEDIAL]);
		} else if (!empty($program_ids) && !is_array($program_ids) && $program_ids == PROGRAM_REMEDIAL) {
			return $count;
		}

		if (!empty($department_ids)) {
			$count = $this->find('count', array(
				'conditions' => array(
					'Student.program_id' => $program_ids,
					'Student.program_type_id' => $program_type_ids,
					'Student.graduated' => 0,
					'Student.department_id' => $department_ids,
					'Student.id NOT IN (SELECT student_id FROM contacts)',
					//'Student.id NOT IN (SELECT foreign_key FROM attachments where model="Student")',
					//'Student.modified <= ' => $not_build_for,
					'Student.created >= ' => $not_build_for
				)
			));
		} else if (!empty($college_ids)){
			$count = $this->find('count', array(
				'conditions' => array(
					'Student.program_id' => $program_ids,
					'Student.program_type_id' => $program_type_ids,
					'Student.graduated' => 0,
					'Student.department_id IS NULL',
					'Student.college_id' => $college_ids,
					'Student.id NOT IN (SELECT student_id FROM contacts)',
					//'Student.id NOT IN (SELECT foreign_key FROM attachments where model="Student")',
					//'Student.modified <= ' => $not_build_for,
					'Student.created >= ' => $not_build_for
				)
			));
		}

		return $count;
	}


	function updateDepartmentTransferFromFiled()
	{
		$updateDepartmentFiled = array();

		$studenLists = ClassRegistry::init('DepartmentTransfer')->find('all', array(
			'conditions' => array(
				'DepartmentTransfer.from_department_id is null'
			), 
			'contain' => array('Student')
		));

		$count = 0;

		if (!empty($studenLists)) {
			foreach ($studenLists as $kkk => $vv) {
				debug($vv);
				$updateDepartmentFiled['DepartmentTransfer'][$count]['id'] = $vv['DepartmentTransfer']['id'];
				$updateDepartmentFiled['DepartmentTransfer'][$count]['from_department_id'] = $vv['Student']['department_id'];
				$count++;
			}
		}

		if (!empty($updateDepartmentFiled['DepartmentTransfer'])) {
			if (ClassRegistry::init('DepartmentTransfer')->saveAll($updateDepartmentFiled['DepartmentTransfer'], array('validate' => false))) {
			}
		}
	}

	function updateDepartmentInStudentTable()
	{
		$updateDepartmentFiled = array();

		$studenLists = ClassRegistry::init('Student')->find('all', array(
			'conditions' => array(
				'Student.department_id is null'
			), 
			'contain' => array('AcceptedStudent')
		));

		$count = 0;

		if (!empty($studenLists)) {
			foreach ($studenLists as $kkk => $vv) {
				$updateDepartmentFiled['Student'][$count]['id'] = $vv['Student']['id'];
				$updateDepartmentFiled['Student'][$count]['department_id'] = $vv['AcceptedStudent']['department_id'];
				$count++;
			}
		}

		if (!empty($updateDepartmentFiled['Student'])) {
			if (ClassRegistry::init('Student')->saveAll($updateDepartmentFiled['Student'], array('validate' => false))) {
			}
		}
	}

	public function getDistributionStats($acadamic_year, $program_id = null, $program_type_id = null, $department_id = null, $sex = 'all', $year_level_id = null, $region_id = null, $freshman = 0, $exclude_graduated = 0, $semester = '') 
	{
		$regions = $this->Region->find('list');
		$query = "";
		$departments = array();

		$distributionByDepartmentYearLevel = array();

		$graph['data'] = array();
		$graph['labels'] = array();

		$graph['series'] = array('Male', 'Female');

		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and s.region_id=' . $region_id . '';
		}

		if (isset($exclude_graduated) && $exclude_graduated) {
			$query .= ' and s.graduated = 0';
		}

		if (isset($semester) && !empty($semester)) {
			$query .= ' and reg.semester LIKE "' . $semester . '"';
		}

		if (isset($program_id) && !empty($program_id)) {
			if (is_array($program_id)) {
				$programs_comma_quoted = "'" . implode ( "', '", $program_id ) . "'";
				$query .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
			} else {
				$query .= ' and s.program_id = ' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			if (is_array($program_type_id)) {
				$program_types_comma_quoted = "'" . implode ( "', '", $program_type_id ) . "'";
				$query .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
			} else {
				$query .= ' and s.program_type_id = ' . $program_type_id . '';
			}
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$query .= ' and sec.academicyear LIKE "' . $acadamic_year . '"';
			$query .= ' and reg.academic_year LIKE "' . $acadamic_year . '"';
		}

		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find('all', array(
					'conditions' => array(
						'Department.college_id' => $college_id[1],
						'Department.active' => 1
					),
					'contain' => array('College', 'YearLevel')
				));
				$college_ids[$college_id[1]] = $college_id[1];
			} else {
				$departments = $this->Department->find('all', array(
					'conditions' => array(
						'Department.id' => $department_id
					), 
					'contain' => array('College', 'YearLevel')
				));
			}
		} else {

			$departments = $this->Department->find('all', array(
				'conditions' => array(
					'Department.active' => 1
				), 
				'contain' => array('College', 'YearLevel')
			));

			$college_ids = $this->College->find('list', array(
				'conditions' => array(
					'College.active' => 1
				), 
				'fields' => array('College.id', 'College.id')
			));
		}

		$distributionByDepartmentYearLevel = array();
		$internalQuery = '';
		$genderQuery = '';

		if ($freshman == 0) {
			if (!empty($departments)) {
				foreach ($departments as $key => $value) { 
					foreach ($value['YearLevel'] as $ykey => $yvalue) {
						
						if (!empty($year_level_id) && $year_level_id == $yvalue['name']) {
							$internalQuery .= ' and sec.year_level_id="' . $yvalue['id'] . '"';
							$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';
						} else if (empty($year_level_id)) {
							$internalQuery .= ' and sec.year_level_id="' . $yvalue['id'] . '"';
							$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';
						}

						if (!empty($internalQuery) && $sex != "all" && ($sex == "male" || $sex == "female")) {
							
							$genderQuery .= ' and s.gender LIKE "' . $sex . '%"';

							$distributionStatsByDept = "SELECT count(DISTINCT s.studentnumber) FROM students AS s, course_registrations AS reg, students_sections AS stsec, sections AS sec, year_levels AS yrl, departments AS dpt WHERE s.department_id IS NOT NULL AND stsec.student_id = s.id AND yrl.id = sec.year_level_id AND dpt.id = s.department_id AND dpt.id = sec.department_id AND sec.id = stsec.section_id AND sec.id = reg.section_id AND sec.year_level_id = reg.year_level_id $query $internalQuery $genderQuery ";
							$distResultDept = $this->query($distributionStatsByDept);

							$distributionByDepartmentYearLevel[$value['Department']['name']][$yvalue['name']][strtolower($sex)] = $distResultDept[0][0]['count(DISTINCT s.studentnumber)'];

							$graph['labels'][$value['Department']['id']] = $value['Department']['name'];

							if (strtolower($sex) == "female") {
								$indexS = 1;
							} else if (strtolower($sex) == "male") {
								$indexS = 0;
							}

							$graph['data'][$indexS][$value['Department']['id']] += $distResultDept[0][0]['count(DISTINCT s.studentnumber)'];

						} else if (!empty($internalQuery)) {

							$sexList = array(
								'male' => 'male',
								'female' => 'female'
							);

							foreach ($sexList as $skey => $svalue) {

								$genderQuery .= ' and s.gender LIKE "' . $svalue . '%"';
								$distributionStatsByDept = "SELECT count(DISTINCT s.studentnumber) FROM students AS s, course_registrations AS reg, students_sections AS stsec, sections AS sec, year_levels AS yrl, departments AS dpt WHERE s.department_id IS NOT NULL AND stsec.student_id = s.id AND yrl.id = sec.year_level_id AND dpt.id = s.department_id AND dpt.id = sec.department_id AND sec.id = stsec.section_id AND sec.id = reg.section_id AND sec.year_level_id = reg.year_level_id  $query $internalQuery $genderQuery";
								$distResult = $this->query($distributionStatsByDept);
								
								//debug($distResult);

								if (isset($distResult[0][0]['count(DISTINCT s.studentnumber)']) && !empty($distResult[0][0]['count(DISTINCT s.studentnumber)'])) {

									//debug($value['Department']['id']);

									$distributionByDepartmentYearLevel[$value['Department']['name']][$yvalue['name']][strtolower($svalue)] = $distResult[0][0]['count(DISTINCT s.studentnumber)'];
									$graph['labels'][$value['Department']['id']] = $value['Department']['name'];

									if (strtolower($svalue) == "female") {
										$indexS = 1;
									} else if (strtolower($svalue) == "male") {
										$indexS = 0;
									}

									$genderQuery = '';

									$graph['data'][$indexS][$value['Department']['id']] += $distResult[0][0]['count(DISTINCT s.studentnumber)'];
									
								} else {
									$genderQuery = '';
								}

							}

							$internalQuery = '';
						}
					}
				}
			}
		}
		//// it is freshman program

		if (!empty($college_ids)) {
			foreach ($college_ids as $ck => $cv) {

				$college = $this->College->find('first', array('conditions' => array('College.id' => $cv), 'recursive' => -1));
				
				$internalQuery = '';
				$genderQuery = '';

				$internalQuery .= ' and (sec.year_level_id is null or sec.year_level_id = 0 or sec.year_level_id = "") and (sec.department_id is null or sec.department_id = 0 or sec.department_id = "") and sec.college_id="' . $cv . '"';

				if (!empty($internalQuery) && ($sex == "male" || $sex == "female")) {
					
					$genderQuery .= ' and s.gender LIKE "' . $sex . '%"';
					
					$distributionStatsByDept = "SELECT count(DISTINCT s.studentnumber) FROM students AS s, course_registrations AS reg, students_sections AS stsec, sections AS sec WHERE s.department_id IS NULL AND stsec.student_id = s.id AND sec.id = stsec.section_id AND sec.id = reg.section_id  $query $internalQuery $genderQuery ";

					$distResultDept = $this->query($distributionStatsByDept);
					$distributionByDepartmentYearLevel[$college['College']['name'] . ' Freshman']['1st'][strtolower($sex)] = $distResultDept[0][0]['count(DISTINCT s.studentnumber)'];

					$graph['labels'][$college['College']['id']] = $college['College']['name'] . ' Freshman';

					if (strtolower($sex) == "female") {
						$indexS = 1;
					} else if (strtolower($sex) == "male") {
						$indexS = 0;
					}

					$graph['data'][$indexS][$college['College']['id']] += $distResultDept[0][0]['count(DISTINCT s.studentnumber)'];

				} else if (!empty($internalQuery)) {
					
					$sexList = array(
						'male' => 'male',
						'female' => 'female'
					);

					foreach ($sexList as $skey => $svalue) {
						$genderQuery .= ' and s.gender LIKE "' . $svalue . '%"';

						$distributionStatsByDept = "SELECT count(DISTINCT s.studentnumber) FROM students AS s, course_registrations AS reg, students_sections AS stsec, sections AS sec WHERE s.department_id IS NULL AND stsec.student_id = s.id AND sec.id = stsec.section_id AND sec.id = reg.section_id $query $internalQuery $genderQuery ";

						$distResult = $this->query($distributionStatsByDept);

						if (isset($distResult[0][0]['count(DISTINCT s.studentnumber)']) && !empty($distResult[0][0]['count(DISTINCT s.studentnumber)'])) {
							
							$distributionByDepartmentYearLevel[$college['College']['name'] . ' Freshman']['1st'][strtolower($svalue)] = $distResult[0][0]['count(DISTINCT s.studentnumber)'];

							$graph['labels'][$college['College']['id']] = $college['College']['name'] . ' Freshman';
							
							if (strtolower($svalue) == "female") {
								$indexS = 1;
							} else if (strtolower($svalue) == "male") {
								$indexS = 0;
							}
							
							$genderQuery = '';

							$graph['data'][$indexS][$college['College']['id']] += $distResult[0][0]['count(DISTINCT s.studentnumber)'];
						}
					}
					$internalQuery = '';
				}
			}
		}

		$distribution['distributionByDepartmentYearLevel'] = $distributionByDepartmentYearLevel;
		$distribution['graph'] = $graph;

		return $distribution;
	}


	public function distributionStatsLetterGrade(
		$acadamic_year,
		$semester,
		$program_id = null,
		$program_type_id = null,
		$department_id = null,
		$sex = 'all',
		$year_level_id = null,
		$region_id = null,
		$freshman = 0
	) {
		$regions = $this->Region->find('list');

		$query = "";
		$queryACS = "";
		$student_ids = array();
		$options = array();
		$collegeId = false;
		$departments = array();
		$distributionByDepartmentYearLevel = array();
		$distributionByRegionDepartmentYearLevel = array();
		if ((empty($acadamic_year) && empty($semester)) || (empty($acadamic_year) || empty($semester))) {
			return array();
		}
		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and s.region_id=' . $region_id . '';
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$query .= ' and s.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and s.program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and s.program_type_id=' . $program_type_id . '';
			}
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$queryACS .= ' academic_year="' . $acadamic_year . '"';
		}

		if (isset($semester) && !empty($semester)) {
			$queryACS .= ' and semester="' . $semester . '"';
		}
		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'contain' => array('College', 'YearLevel')
					)
				);
				$college_ids[$college_id[1]] = $college_id[1];
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id), 'contain' => array('College', 'YearLevel' => array('order' => array('YearLevel.name ASC')))));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array(
				'College',
				'YearLevel' => array('order' => array('YearLevel.name ASC'))
			)));
			$college_ids = $this->College->find(
				'list',
				array(
					'fields' => array(
						'College.id',
						'College.id'
					)
				)
			);
		}

		$distributionLetterGrade = array();


		$graph['data'] = array();
		$graph['series'] = array();
		$graph['labels'] = array();

		if ($freshman == 0) {
			foreach ($departments as $key => $value) {
				$internalQuery = '';
				$genderQuery = '';

				foreach ($value['YearLevel'] as $ykey => $yvalue) {
					if (
						!empty($year_level_id)
						&& $year_level_id == $yvalue['name']
					) {
						$internalQuery .= ' and year_level_id="' . $yvalue['id'] . '"';

						$internalQuery .= ' and department_id="' . $value['Department']['id'] . '"';
					} else if (empty($year_level_id)) {
						$internalQuery .= ' and year_level_id="' . $yvalue['id'] . '"';

						$internalQuery .= ' and department_id="' . $value['Department']['id'] . '"';
					}


					if ((isset($internalQuery) && !empty($internalQuery)) && ($sex == "male" || $sex == "female")) {
						$genderQuery .= ' and s.gender="' . $sex . '"';

						$distributionStatsByLetter = "SELECT ex.grade,
						cr.published_course_id,
						count(ex.grade) as gcount
						FROM students AS s, course_registrations AS cr, exam_grades AS ex
						WHERE cr.student_id = s.id
						AND ex.course_registration_id = cr.id
						AND cr.published_course_id
						IN (

						SELECT id
						FROM published_courses
						WHERE $queryACS $internalQuery
						)
						AND ex.registrar_approval =1
						AND ex.department_approval =1
						$query $genderQuery
						group by ex.grade,
						cr.published_course_id
						";
						$distResultLetter = $this->query($distributionStatsByLetter);

						foreach ($distResultLetter as $dkey => $dvalue) {
							$publishedCourse = $this->CourseRegistration->PublishedCourse->find(
								'first',
								array(
									'conditions' => array('PublishedCourse.id' => $dvalue['cr']['published_course_id']),
									'contain' => array('Course')
								)
							);
							if (isset($dvalue['ex']['grade']) && !empty($dvalue['ex']['grade'])) {
								$distributionLetterGrade[$value['Department']['name']][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($sex)][$dvalue['ex']['grade']] = $distributionLetterGrade[$value['Department']['name']][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($sex)][$dvalue['ex']['grade']] + $dvalue[0]['gcount'];
							}
						}

						$internalQuery = '';
						$genderQuery = '';
					} else if (isset($internalQuery) && !empty($internalQuery)) {
						$sexList = array('male' => 'male', 'female' => 'female');
						foreach ($sexList as $skey => $svalue) {

							$genderQuery .= ' and s.gender="' . $svalue . '"';
							$distributionStatsByLetter = "SELECT ex.grade,
						cr.published_course_id,	count(ex.grade) as gcount
						FROM students AS s, course_registrations AS cr, exam_grades AS ex
						WHERE cr.student_id = s.id
						AND ex.course_registration_id = cr.id
						AND ex.grade is not null
						AND cr.published_course_id
						IN (

						SELECT id
						FROM published_courses
						WHERE $queryACS $internalQuery
						)
						AND ex.registrar_approval =1
						AND ex.department_approval =1
						$query $genderQuery
						group by ex.grade,
						cr.published_course_id
						";
							$distResultLetter = $this->query($distributionStatsByLetter);
							debug($distResultLetter);

							foreach ($distResultLetter as $dkey => $dvalue) {
								$publishedCourse = $this->CourseRegistration->PublishedCourse->find(
									'first',
									array(
										'conditions' => array('PublishedCourse.id' => $dvalue['cr']['published_course_id']),
										'contain' => array('Course')
									)
								);
								if (isset($dvalue['ex']['grade']) && !empty($dvalue['ex']['grade'])) {
									$distributionLetterGrade[$value['Department']['name']][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($svalue)][$dvalue['ex']['grade']] = 	$distributionLetterGrade[$value['Department']['name']][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($svalue)][$dvalue['ex']['grade']] + $dvalue[0]['gcount'];
								}
							}
							$genderQuery = '';
						}
						$internalQuery = '';
					}
				}
			}
		}
		//freshman

		foreach ($college_ids as $ck => $cv) {
			$college = $this->CourseRegistration->PublishedCourse->College->find(
				'first',
				array(
					'conditions' => array('College.id' => $cv),
					'recursive' => -1
				)
			);

			$internalQuery = '';
			$genderQuery = '';

			$internalQuery .= ' and (year_level_id is null or year_level_id=0 )';

			$internalQuery .= ' and college_id="' . $cv . '" and (department_id is null or department_id=0)';

			if ((isset($internalQuery) && !empty($internalQuery)) && ($sex == "male" || $sex == "female")) {
				$genderQuery .= ' and s.gender="' . $sex . '"';

				$distributionStatsByLetter = "SELECT ex.grade,
					cr.published_course_id,count(*) as gcount
					count(ex.grade) as gcount
					FROM students AS s, course_registrations AS cr, exam_grades AS ex
					WHERE cr.student_id = s.id
					AND ex.course_registration_id = cr.id
					AND ex.grade is not null
					AND cr.published_course_id
					IN (

					SELECT id
					FROM published_courses
					WHERE $queryACS $internalQuery
					)
					AND ex.registrar_approval =1
					AND ex.department_approval =1
					$query $genderQuery
					group by ex.grade,cr.published_course_id
					";
				debug($distributionStatsByLetter);
				$distResultLetter = $this->query($distributionStatsByLetter);

				foreach ($distResultLetter as $dkey => $dvalue) {
					$publishedCourse = $this->CourseRegistration->PublishedCourse->find(
						'first',
						array(
							'conditions' => array('PublishedCourse.id' => $dvalue['cr']['published_course_id']),
							'contain' => array('Course')
						)
					);
					if (isset($dvalue['ex']['grade']) && !empty($dvalue['ex']['grade'])) {
						$distributionLetterGrade[$college['College']['name'] . 'Freshman'][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($sex)][$dvalue['ex']['grade']] = $distributionLetterGrade[$college['College']['name'] . 'Freshman'][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($sex)][$dvalue['ex']['grade']] + $dvalue[0]['gcount'];
					}
				}

				$internalQuery = '';
				$genderQuery = '';
			} else if (isset($internalQuery) && !empty($internalQuery)) {
				$sexList = array('male' => 'male', 'female' => 'female');
				foreach ($sexList as $skey => $svalue) {

					$genderQuery .= ' and s.gender="' . $svalue . '"';
					$distributionStatsByLetter = "SELECT ex.grade,
					cr.published_course_id,count(*) as gcount
					FROM students AS s, course_registrations AS cr, exam_grades AS ex
					WHERE cr.student_id = s.id
					AND ex.course_registration_id = cr.id
					AND cr.published_course_id
					IN (

					SELECT id
					FROM published_courses
					WHERE $queryACS $internalQuery
					)
					AND ex.registrar_approval =1
					AND ex.department_approval =1
					$query $genderQuery
					group by ex.grade,
					cr.published_course_id
					";
					debug($distributionStatsByLetter);

					$distResultLetter = $this->query($distributionStatsByLetter);

					foreach ($distResultLetter as $dkey => $dvalue) {
						$publishedCourse = $this->CourseRegistration->PublishedCourse->find(
							'first',
							array(
								'conditions' => array('PublishedCourse.id' => $dvalue['cr']['published_course_id']),
								'contain' => array('Course')
							)
						);
						debug($dvalue);
						if (isset($dvalue['ex']['grade']) && !empty($dvalue['ex']['grade'])) {

							$distributionLetterGrade[$college['College']['name'] . 'Freshman'][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($svalue)][$dvalue['ex']['grade']] = 	$distributionLetterGrade[$college['College']['name'] . 'Freshman'][$publishedCourse['Course']['course_title'] . ' ' . $publishedCourse['Course']['course_code']][strtolower($svalue)][$dvalue['ex']['grade']] + $dvalue[0]['gcount'];
						}
					}
					$genderQuery = '';
				}
				$internalQuery = '';
			}
		}
		debug($distributionLetterGrade);
		$distribution['distributionLetterGrade'] = $distributionLetterGrade;

		return $distribution;
	}

	public function listStudentByLetterGrade($acadamic_year = '', $semester = '', $program_id = null, $program_type_id = null, $department_id = null, $sex = 'all', $year_level_id = null, $region_id = null, $grade, $freshman = 0) 
	{
		//TODO:Preengineering
		$queryPS = '';
		$queryST = 'id is not null ';
		$college_id = '';

		if ((empty($acadamic_year) && empty($semester)) || (empty($acadamic_year) || empty($semester))) {
			return array();
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$queryPS .= ' academic_year="' . $acadamic_year . '" and semester="' . $semester . '"';
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$queryPS .= ' and program_type_id=' . $program_type_id;
			$queryST .= ' and program_type_id=' . $program_type_id;
		}

		if (isset($program_id) && !empty($program_id)) {
			$queryPS .= ' and program_id=' . $program_id;
			$queryST .= ' and program_id=' . $program_id;
		}

		if ($freshman == 0) {
			if (isset($department_id) && !empty($department_id)) {
				$college_ids = explode('~', $department_id);
				debug($college_ids);
				if (count($college_ids) > 1) {
					$departments = $this->Department->find('list', array('conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1), 'fields' => array('Department.id', 'Department.id')));
					$queryPS .= ' and department_id in (' . join(',', $departments) . ')';
					$queryST .= ' and department_id in (' . join(',', $departments) . ')';
					$college_id = $college_ids[1];
				} else {
					$queryPS .= ' and department_id=' . $department_id;
					$queryST .= ' and department_id=' . $department_id;
					debug($department_id);
				}
			}
		} else {
			$college_ids = explode('~', $department_id);
			if (isset($college_ids[1]) && !empty($college_ids[1])) {
				$queryPS .= ' and (department_id is null and college_id =' . $college_ids[1] . ' ) ';
				$queryST .= ' and (department_id is null and college_id=' . $college_ids[1] . ' )';
			}
		}

		if (isset($year_level_id) &&  !empty($year_level_id) && !empty($college_id)) {
			$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id in (select id from departments where college_id="' . $college_id . '")', 'YearLevel.name' => $year_level_id), 'fields' => array('id', 'id')));
			$queryPS .= ' and year_level_id in (' . join(',', $yearLevels) . ')';
		} else if (isset($year_level_id) && !empty($year_level_id)) {
			if (isset($department_id) && !empty($department_id)) {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $department_id, 'YearLevel.name' => $year_level_id), 'fields' => array('id', 'id')));
				if (isset($yearLevels) && !empty($yearLevels)) {
					$queryPS .= ' and year_level_id in (' . join(',', $yearLevels) . ')';
				}
			} else {
				$queryPS .= ' and ( year_level_id is null year_level_id = "" or year_level_id=0)  ';
			}
		}

		if (isset($region_id) && !empty($region_id)) {
			$queryST .= ' and region_id = ' . $region_id . '';
		}

		if (isset($sex) && !empty($sex)) {
			if ($sex != "all") {
				$queryST .= ' and gender="' . $sex . '"';
			}
		}

		$publishedCourses_reg = $this->CourseRegistration->find('all', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $acadamic_year,
				'CourseRegistration.semester' => $semester,
				"CourseRegistration.published_course_id in (select id from published_courses where $queryPS )",
				"CourseRegistration.id in (select course_registration_id from exam_grades where grade='$grade' and registrar_approval=1 and department_approval=1)",
				"CourseRegistration.student_id in (select id from students where $queryST)"
			),
			'contain' => array(
				'Student', 
				'ExamGrade', 
				'PublishedCourse' => array(
					'Section', 
					'YearLevel', 
					'Program', 
					'ProgramType', 
					'Course', 
					'Department',
					'CourseInstructorAssignment' => array(
						'conditions' => array(
							'CourseInstructorAssignment.isprimary' => 1
						),
						'order' => array('CourseInstructorAssignment.isprimary' => 'DESC'),
						'Staff' => array(
							'Department',
							'Title' => array('id', 'title'),
							'Position' => array('id', 'position')
						)
					), 
				)
			)
		));

		$publishedCourses_add = $this->CourseAdd->find('all', array(
			'conditions' => array(
				'CourseAdd.academic_year' => $acadamic_year,
				'CourseAdd.semester' => $semester,
				"CourseAdd.published_course_id in (select id from published_courses where $queryPS )",
				"CourseAdd.id in (select course_add_id from exam_grades where grade='$grade' and registrar_approval=1 and department_approval=1 )",
				"CourseAdd.student_id in (select id from students where $queryST)"
			),
			'contain' => array(
				'Student', 
				'ExamGrade', 
				'PublishedCourse' => array(
					'Section', 
					'YearLevel', 
					'Program', 
					'ProgramType', 
					'Course', 
					'Department',
					'CourseInstructorAssignment' => array(
						'conditions' => array(
							'CourseInstructorAssignment.isprimary' => 1
						),
						'order' => array('CourseInstructorAssignment.isprimary' => 'DESC'),
						'Staff' => array(
							'Department',
							'Title' => array('id', 'title'),
							'Position' => array('id', 'position')
						)
					), 
				)
			)
		));

		debug($publishedCourses_reg);

		$publishedCourses = array_merge($publishedCourses_reg, $publishedCourses_add);

		$organized_Published_courses_by_sections = array();

		if (!empty($publishedCourses)) {
			foreach ($publishedCourses as $key => $published_course) {
				if (isset($published_course['CourseRegistration']['id'])) {
					$gradee = $this->CourseRegistration->ExamGrade->getApprovedGrade($published_course['CourseRegistration']['id'], 1);
				} else if (isset($published_course['CourseAdd']['id'])) {
					$gradee = $this->CourseRegistration->ExamGrade->getApprovedGrade($published_course['CourseAdd']['id'], 0);
				}

				$ylName = '';

				if (!empty($published_course['PublishedCourse']['YearLevel']['name'])) {
					$ylName = $published_course['PublishedCourse']['YearLevel']['name'];
				} else if ($published_course['PublishedCourse']['Program']['id'] == PROGRAM_REMEDIAL) {
					$ylName = 'Remedial';
				} else {
					$ylName = 'Pre/1st';
				}

				$assignedInstructor = 'Not Assigned';

				if (!empty($published_course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['id'])) {
					$assignedInstructor = $published_course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. '.  $published_course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $published_course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')';
				}

				if ($gradee['grade'] == $grade) {
					$organized_Published_courses_by_sections[$published_course['PublishedCourse']['Program']['name'] . '~' . $published_course['PublishedCourse']['ProgramType']['name'] . '~' . $published_course['PublishedCourse']['Section']['name'] . '~' . $published_course['PublishedCourse']['Course']['course_title'] . ' (' . $published_course['PublishedCourse']['Course']['course_code'] . ')'.'~' . $ylName . '~' . $assignedInstructor]['studentList'][] = $published_course['Student'];
				}
			}
		}

		return $organized_Published_courses_by_sections;
	}

	public function getStudentIdsNotRegisteredPublishourse($published_course_id)
	{
		$publishedDetails = $this->CourseRegistration->PublishedCourse->find('first', array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1));
		
		if (!empty($publishedDetails)) {

			$studentRegisteredLists = $this->CourseRegistration->find('list', array(
				'conditions' => array(
					'CourseRegistration.published_course_id' => $published_course_id
				),
				'fields' => array('CourseRegistration.student_id','CourseRegistration.student_id')
			));

			$getStudentsListInSection = ClassRegistry::init('StudentsSection')->getStudentsIdsInSection($publishedDetails['PublishedCourse']['section_id']);

			//debug(count($getStudentsListInSection));
			if (!empty($getStudentsListInSection)) {
				foreach ($getStudentsListInSection as $key => $sid) {
					if ($this->field('graduated', array('id' => $sid))) {
						unset($getStudentsListInSection[$key]);
					}
				}
			}
			//debug(count($getStudentsListInSection));

			$notRegisteredStudentIds = array_diff($getStudentsListInSection, $studentRegisteredLists);
			return $notRegisteredStudentIds;
		}
	}

	public function getStudentNotRegisteredPublishourse($published_course_id)
	{
		$publishedDetails = $this->CourseRegistration->PublishedCourse->find('first', array(
			'conditions' => array(
				'PublishedCourse.id' => $published_course_id
			),
			'recursive' => -1
		));

		if (!empty($publishedDetails)) {

			$studentRegisteredLists = $this->CourseRegistration->find('list', array(
				'conditions' => array('CourseRegistration.published_course_id' => $published_course_id),
				'fields' => array('CourseRegistration.student_id', 'CourseRegistration.student_id')
			));

			$getStudentsListInSection = ClassRegistry::init('StudentsSection')->getStudentsIdsInSection($publishedDetails['PublishedCourse']['section_id']);
			
			//debug(count($getStudentsListInSection));
			if (!empty($getStudentsListInSection)) {
				foreach ($getStudentsListInSection as $key => $sid) {
					if ($this->field('graduated', array('id' => $sid))) {
						unset($getStudentsListInSection[$key]);
					}
				}
			}
			//debug(count($getStudentsListInSection));
			
			$notRegisteredStudentIds = array_diff($getStudentsListInSection, $studentRegisteredLists);
			//debug($notRegisteredStudentIds);

			$students = $this->find('all', array(
				'conditions' => array(
					'Student.id' => $notRegisteredStudentIds,
					'Student.graduated' => 0
				),
				'contain' => array('Department', 'College', 'Program', 'ProgramType')
			));

			return $students;
		}
	}

	public function cancelStudentFxAutomaticallyConvertedChange($acadamic_year, $semester, $program_id, $program_type_id, $department_id, $year_level_id = 'All') 
	{
		//TODO:Preengineering
		$queryPS = '';
		$queryST = 'id is not null ';
		$college_id = '';

		if ((empty($acadamic_year) && empty($semester)) || (empty($acadamic_year) || empty($semester))) {
			return array();
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$queryPS .= ' academic_year="' . $acadamic_year . '" and semester="' . $semester . '"';
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$queryPS .= ' and program_type_id=' . $program_type_id;
			$queryST .= ' and program_type_id=' . $program_type_id;
		}

		if (isset($program_id) && !empty($program_id)) {
			$queryPS .= ' and program_id=' . $program_id;
			$queryST .= ' and program_id=' . $program_id;
		}

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_ids = explode('~', $department_id);
			if (count($college_ids) > 1) {
				$departments = $this->Department->find('list', array('conditions' => array('Department.college_id' => $college_ids[1]), 'fields' => array('Department.id', 'Department.id')));
				$queryPS .= ' and department_id in (' . join(',', $departments) . ')';
				$queryST .= ' and department_id in (' . join(',', $departments) . ')';
				$college_id = $college_ids[1];
			} else {
				$queryPS .= ' and department_id=' . $department_id;
				$queryST .= ' and department_id=' . $department_id;
			}
		}

		if (isset($year_level_id) &&  !empty($year_level_id) && !empty($college_id)) {
			if ($year_level_id == "All") {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id in (select id from departments where college_id="' . $college_id . '")'), 'fields' => array('id', 'id')));
			} else {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id in (select id from departments where college_id="' . $college_id . '")', 'YearLevel.name' => $year_level_id), 'fields' => array('id', 'id')));
			}
			$queryPS .= ' and year_level_id in (' . join(',', $yearLevels) . ')';
		} else if (isset($year_level_id) && !empty($year_level_id) && !empty($department_id)) {
			if ($year_level_id == "All") {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $department_id), 'fields' => array('id', 'id')));
			} else {
				$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $department_id, 'YearLevel.name' => $year_level_id), 'fields' => array('id', 'id')));
			}
			$queryPS .= ' and year_level_id in (' . join(',', $yearLevels) . ')';
		}


		$publishedCourses_reg = $this->CourseRegistration->find('all', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $acadamic_year,
				'CourseRegistration.semester' => $semester,
				"CourseRegistration.published_course_id in (select id from published_courses where $queryPS )",
				"CourseRegistration.id in (select course_registration_id from exam_grades where grade = 'FX' and registrar_approval=  1 and department_approval = 1)",
				"CourseRegistration.student_id in (select id from students where $queryST)",
				"CourseRegistration.student_id not in (select student_id from senate_lists )",
			),
			'contain' => array(
				'Student', 
				'ExamGrade', 
				'PublishedCourse' => array('Section', 'YearLevel', 'Program', 'ProgramType', 'Course', 'Department')
			)
		));

		$publishedCourses_add = $this->CourseAdd->find('all', array(
			'conditions' => array(
				'CourseAdd.academic_year' => $acadamic_year,
				'CourseAdd.semester' => $semester,
				"CourseAdd.published_course_id in (select id from published_courses where $queryPS )",
				"CourseAdd.id in (select course_add_id from exam_grades where grade = 'FX' and registrar_approval = 1 and department_approval = 1 )",
				"CourseAdd.student_id in (select id from students where $queryST)",
				"CourseAdd.student_id not in (select student_id from senate_lists )",
			),
			'contain' => array(
				'Student', 
				'ExamGrade', 
				'PublishedCourse' => array('Section', 'YearLevel', 'Program', 'ProgramType', 'Course', 'Department')
			)
		));

		$publishedCourses = array_merge($publishedCourses_reg, $publishedCourses_add);

		//$organized_Published_courses_by_sections = array();

		if (!empty($publishedCourses)) {
			foreach ($publishedCourses as $key => $published_course) {
				if (isset($published_course['CourseRegistration']['id'])) {
					$gradee = $this->CourseRegistration->ExamGrade->getApprovedNotChangedGrade($published_course['CourseRegistration']['id'], 1);
				} else if (isset($published_course['CourseAdd']['id'])) {
					$gradee = $this->CourseRegistration->ExamGrade->getApprovedNotChangedGrade($published_course['CourseAdd']['id'], 0);
				}

				if (strcasecmp($gradee['grade'], "Fx") == 0) {

					foreach ($published_course['ExamGrade'] as $result) {
						//delete grade
						$examGradeChangeConverted = $this->CourseRegistration->ExamGrade->ExamGradeChange->find('first', array(
							'conditions' => array(
								'ExamGradeChange.exam_grade_id' => $result['id'],
								'ExamGradeChange.auto_ng_conversion' => 1
							),
							'recursive' => -1
						));

						if (isset($examGradeChangeConverted) && !empty($examGradeChangeConverted)) {
							$deleted = $this->CourseRegistration->ExamGrade->ExamGradeChange->delete($examGradeChangeConverted['ExamGradeChange']['id']);
							debug($deleted);
							debug($gradee['grade']);
						}
					}
				}
			}
		}
		//return $organized_Published_courses_by_sections;
	}

	public function getDistributionStatsOfRegion(
		$acadamic_year,
		$program_id = null,
		$program_type_id = null,
		$department_id = null,
		$sex = 'all',
		$year_level_id = null,
		$region_id = null,
		$freshman = 0
	) {
		$regions = $this->Region->find('list');
		$query = "";
		$student_ids = array();
		$options = array();
		$collegeId = false;
		$departments = array();
		$distributionByDepartmentYearLevel = array();
		$distributionByRegionDepartmentYearLevel = array();

		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and s.region_id=' . $region_id . '';
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$query .= ' and s.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and s.program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and s.program_type_id=' . $program_type_id . '';
			}
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$query .= ' and sec.academicyear="' . $acadamic_year . '"';
		}
		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'contain' => array('College', 'YearLevel')
					)
				);
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id), 'contain' => array('College', 'YearLevel' => array('order' => array('YearLevel.name ASC')))));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array(
				'College',
				'YearLevel' => array('order' => array('YearLevel.name ASC'))
			)));
		}

		$distributionByDepartmentYearLevel = array();
		$distributionByRegionYearLevel = array();
		$internalQuery = '';
		$genderQuery = '';
		$graph['data'] = array();
		$graph['series'] = array();
		$graph['labels'] = array();
		if ($freshman == 0) {
			foreach ($departments as $key => $value) {
				foreach ($value['YearLevel'] as $ykey => $yvalue) {

					if (
						!empty($year_level_id)
						&& $year_level_id == $yvalue['name']
					) {
						$internalQuery .= ' and sec.year_level_id="' . $yvalue['id'] . '"';

						$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';
					} else if (empty($year_level_id)) {
						$internalQuery .= ' and sec.year_level_id="' . $yvalue['id'] . '"';

						$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';
					}

					if ((!empty($internalQuery)) && ($sex == "male" || $sex == "female")) {
						$genderQuery .= ' and s.gender="' . $sex . '"';

						$distributionStatsByRegion = "SELECT count(DISTINCT s.studentnumber),s.region_id FROM students AS s, students_sections AS stsec,sections AS sec,year_levels as yrl,departments as dpt WHERE  stsec.student_id=s.id AND yrl.id = sec.year_level_id and dpt.id=s.department_id and dpt.id=sec.department_id AND sec.id=stsec.section_id AND sec.id in (select section_id from students_sections) $query
              $internalQuery $genderQuery group by s.region_id
              order by s.region_id ASC";
						$distResultRegion = $this->query($distributionStatsByRegion);


						foreach ($distResultRegion as $dkey => $dvalue) {

							if ($dvalue['s']['region_id'] != 0) {
								$distributionByRegionYearLevel[$value['Department']['name']][$regions[$dvalue['s']['region_id']]][strtolower($sex)][$yvalue['name']] = $dvalue[0]['count(DISTINCT s.studentnumber)'];
								$graph['series'][$dvalue['s']['region_id']] = $regions[$dvalue['s']['region_id']];
								$graph['labels'][$value['Department']['id']] = $value['Department']['name'];
								$graph['data'][$dvalue['s']['region_id']][$value['Department']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
							}
						}

						$internalQuery = '';
						$genderQuery = '';
					} else if (!empty($internalQuery)) {
						$sexList = array('male' => 'male', 'female' => 'female');
						foreach ($sexList as $skey => $svalue) {

							$genderQuery .= ' and s.gender="' . $svalue . '"';
							$distributionStatsByRegion = "SELECT count(DISTINCT s.studentnumber),s.region_id FROM students AS s, students_sections AS stsec,sections AS sec,year_levels as yrl,departments as dpt WHERE  stsec.student_id=s.id AND yrl.id = sec.year_level_id and dpt.id=s.department_id and dpt.id=sec.department_id AND sec.id=stsec.section_id AND sec.id in (select section_id from students_sections) $query
              $internalQuery $genderQuery group by s.region_id order by s.region_id ASC ";

							$distResultRegion = $this->query($distributionStatsByRegion);

							foreach ($distResultRegion as $dkey => $dvalue) {
								if ($dvalue['s']['region_id'] != 0) {
									$distributionByRegionYearLevel[$value['Department']['name']][$regions[$dvalue['s']['region_id']]][strtolower($svalue)][$yvalue['name']] = $dvalue[0]['count(DISTINCT s.studentnumber)'];

									$graph['series'][$dvalue['s']['region_id']] = $regions[$dvalue['s']['region_id']];

									$graph['labels'][$value['Department']['id']] = $value['Department']['name'];
									$graph['data'][$dvalue['s']['region_id']][$value['Department']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
								}
							}
							$genderQuery = '';
						}
						$internalQuery = '';
					}
				}
			}
		} else {
			$college_id = explode('~', $department_id);
			if (isset($college_id[1]) && !empty($college_id[1])) {

				$colleges = $this->College->find('all', array('conditions' => array('College.id' => $college_id[1]), 'recursive' => -1));
			}

			debug($colleges);
			foreach ($colleges as $ck => $clv) {
				$genderQuery = '';
				$internalQuery = '';
				$internalQuery .= ' and (sec.year_level_id is null or sec.year_level_id=0 )';
				$internalQuery .= ' and sec.college_id="' . $clv['College']['id'] . '" and (sec.department_id is null or sec.department_id=0)';

				debug($sex);

				if ((!empty($internalQuery)) && ($sex == "male" || $sex == "female")) {
					$genderQuery .= ' and s.gender="' . $sex . '"';

					$distributionStatsByRegion = "SELECT count(DISTINCT s.studentnumber),s.region_id FROM students AS s, students_sections AS stsec,sections AS sec,colleges as clg WHERE  stsec.student_id=s.id AND  clg.id=s.college_id and clg.id=sec.college_id AND sec.id=stsec.section_id AND sec.id in (select section_id from students_sections) $query
              $internalQuery $genderQuery group by s.region_id
              order by s.region_id ASC";
					debug($distributionStatsByRegion);
					$distResultRegion = $this->query($distributionStatsByRegion);

					debug($distResultRegion);


					foreach ($distResultRegion as $dkey => $dvalue) {

						if ($dvalue['s']['region_id'] != 0) {
							$distributionByRegionYearLevel[$clv['College']['name']][$regions[$dvalue['s']['region_id']]][strtolower($sex)]['1st'] = $dvalue[0]['count(DISTINCT s.studentnumber)'];
							$graph['series'][$dvalue['s']['region_id']] = $regions[$dvalue['s']['region_id']];
							$graph['labels'][$clv['College']['id']] = $clv['College']['name'];
							$graph['data'][$dvalue['s']['region_id']][$clv['College']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
						}
					}

					$internalQuery = '';
					$genderQuery = '';
				} else if (!empty($internalQuery)) {
					$sexList = array('male' => 'male', 'female' => 'female');

					foreach ($sexList as $skey => $svalue) {

						$genderQuery .= ' and s.gender="' . $svalue . '"';
						$distributionStatsByRegion = "SELECT count(DISTINCT s.studentnumber),s.region_id FROM students AS s, students_sections AS stsec,sections AS sec,colleges as clg WHERE  stsec.student_id=s.id and clg.id=s.college_id and clg.id=sec.college_id AND sec.id=stsec.section_id AND sec.id in (select section_id from students_sections) $query
              $internalQuery $genderQuery group by s.region_id order by s.region_id ASC ";
						debug($distributionStatsByRegion);

						$distResultRegion = $this->query($distributionStatsByRegion);

						debug($distResultRegion);

						foreach ($distResultRegion as $dkey => $dvalue) {
							if ($dvalue['s']['region_id'] != 0) {
								$distributionByRegionYearLevel[$clv['College']['name']][$regions[$dvalue['s']['region_id']]][strtolower($svalue)]['1st'] = $dvalue[0]['count(DISTINCT s.studentnumber)'];

								$graph['series'][$dvalue['s']['region_id']] = $regions[$dvalue['s']['region_id']];

								$graph['labels'][$clv['College']['id']] = $clv['College']['name'];
								$graph['data'][$dvalue['s']['region_id']][$clv['College']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
							}
						}
					}
				}
			}
		}
		$distribution['distributionByRegionYearLevel'] = $distributionByRegionYearLevel;
		$distribution['graph'] = $graph;

		return $distribution;
	}

	public function findAttrationRate(
		$acadamic_year,
		$semester,
		$program_id = null,
		$program_type_id = null,
		$department_id = null,
		$year_level_id = null,
		$region_id = null,
		$sex = 'all',
		$freshman = 0
	) {
		$query = "";
		$queryR = '';
		$programs = $this->Program->find('list');
		$programTypes = $this->ProgramType->find('list');
		if (isset($sex) && !empty($sex)) {
			if ($sex != "all") {
				$sex .= ' and st.gender=' . $sex . '';
			}
		}

		$query .= ' and stexam.academic_status_id=4';
		$queryR .= ' id is not null ';

		$attritionRateByYearLevel = array();

		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and st.region_id=' . $region_id . '';
			$queryR .= 'and region_id=' . $region_id . '';
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$query .= ' and st.program_id=' . $program_ids[1] . '';
				$queryR .= 'and program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and st.program_id=' . $program_id . '';
				$queryR .= 'and program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$query .= ' and st.program_type_id=' . $program_type_ids[1] . '';
				$queryR .= ' and program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and st.program_type_id=' . $program_type_id . '';
				$queryR .= ' and program_type_id=' . $program_type_id . '';
			}
		}


		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$query .= ' and stexam.academic_year="' .
				$acadamic_year . '"';
		}

		if (isset($semester) && !empty($semester)) {
			$query .= ' and stexam.semester="' . $semester . '"';
		}

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'contain' => array('College', 'YearLevel')
					)
				);
				$college_ids[$college_id[1]] = $college_id[1];
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id), 'contain' => array('College', 'YearLevel' => array('order' => array('YearLevel.name ASC')))));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array(
				'College',
				'YearLevel' => array('order' => array('YearLevel.name ASC'))
			)));

			$college_ids = $this->College->find('list', array('fields' => array('College.id', 'College.id')));
		}

		if ($freshman == 0) {
			foreach ($departments as $key => $value) {
				$internalQuery = '';
				$yearLevel = array();
				if (!empty($year_level_id)) {

					foreach ($value['YearLevel'] as $yykey => $yyvalue) {
						if (
							!empty($year_level_id)
							&& strcasecmp($year_level_id, $yyvalue['name']) == 0
						) {
							$yearLevel[$yykey] = $yyvalue;
						}
					}
				} else if (empty($year_level_id)) {
					$yearLevel = $value['YearLevel'];
				}

				foreach ($yearLevel as
					$ykey => $yvalue) {
					$totalRegistred = 0;
					$internalQuery .= ' and st.id in (select student_id from course_registrations where year_level_id=' . $yvalue['id'] . ') ';
					$internalQuery .= ' and st.department_id=' . $value['Department']['id'] . '';
					$totalRegistredListIds = $this->CourseRegistration->find(
						'list',
						array(
							'conditions' =>
							array(
								'CourseRegistration.year_level_id' => $yvalue['id'],
								'CourseRegistration.semester' => $semester,
								'CourseRegistration.academic_year' => $acadamic_year,
								"CourseRegistration.student_id in (select id from students where
																$queryR)",
								"CourseRegistration.published_course_id in (select id from published_courses where year_level_id=" . $yvalue['id'] . " and department_id=" . $value['Department']['id'] . " ) ",
							),
							'fields' => array('CourseRegistration.student_id', 'CourseRegistration.student_id'),
							'group' => array('CourseRegistration.student_id')
						)
					);
					debug($totalRegistredListIds);
					if (
						isset($totalRegistredListIds) &&  !empty($totalRegistredListIds)
					) {

						$totalRegistred = count($totalRegistredListIds);
						if (!empty($internalQuery)) {
							$queryAttr = "SELECT
			      count(DISTINCT st.studentnumber),
			      st.gender,st.program_id,st.program_type_id FROM  student_exam_statuses AS stexam, students AS st
			      WHERE st.id in (" . implode(',', $totalRegistredListIds) . ") and st.id = stexam.student_id
			      $query $internalQuery
			      GROUP BY st.gender,st.program_id";
							$internalQuery = '';
							$internalR = '';
							if (!empty($queryAttr)) {
								$attrResult = $this->query($queryAttr);
							}
						}
						if ($totalRegistred > 0) {
							foreach ($attrResult as $akey => $avalue) {
								$attritionRateByYearLevel[$programs[$avalue['st']['program_id']] . '~' . $programTypes[$avalue['st']['program_type_id']]][$value['College']['name']][$value['Department']['name']][$yvalue['name']][strtolower($avalue['st']['gender'])] = $avalue[0]['count(DISTINCT st.studentnumber)'];
								$attritionRateByYearLevel[$programs[$avalue['st']['program_id']] . '~' . $programTypes[$avalue['st']['program_type_id']]][$value['College']['name']][$value['Department']['name']][$yvalue['name']]["total"] = $totalRegistred;
							}
						}
					}
				}
			}
		} else {

			if (isset($college_ids) && !empty($college_ids)) {

				foreach ($college_ids as $ck => $cv) {
					$internalQuery = '';


					// freshman students
					$internalQuery .= ' and st.id in (select student_id from course_registrations where (year_level_id is null or year_level_id=0 ) and student_id !=0) ';
					$totalRegistredListIds = $this->CourseRegistration->find('list', array(
						'conditions' =>
						array(
							'CourseRegistration.year_level_id is null',
							'CourseRegistration.semester' => $semester,
							'CourseRegistration.academic_year' => $acadamic_year,
							"CourseRegistration.student_id in (select id from students where $queryR)",
							"CourseRegistration.published_course_id in (select id from published_courses where ((year_level_id is null or year_level_id=0 or year_level_id='' ) and department_id is null or department_id=0 or department_id='' ) and college_id='$cv' ) ",

						),
						'fields' => array('CourseRegistration.student_id', 'CourseRegistration.student_id'),
						'group' => array('CourseRegistration.student_id')
					));
					if (isset($totalRegistredListIds) && !empty($totalRegistredListIds)) {
						$totalRegistredPr = count($totalRegistredListIds);

						$queryPreAttr = "SELECT
		      count(DISTINCT st.studentnumber),st.program_id,st.program_type_id,st.college_id,
		      st.gender FROM  student_exam_statuses AS stexam, students AS st
		      WHERE st.id in (" . implode(',', $totalRegistredListIds) . ") and  st.id = stexam.student_id
		      $query $internalQuery
					GROUP BY st.gender";
						debug($queryPreAttr);
						if (!empty($queryPreAttr)) {
							$attrResult = $this->query($queryPreAttr);
						}
						debug($attrResult);
						if (isset($attrResult) && !empty($attrResult)) {

							foreach ($attrResult as $akey => $avalue) {
								$college_d = $this->College->find(
									'first',
									array(
										'conditions' => array('College.id' => $avalue['st']['college_id']),
										'recursive' => -1
									)
								);
								$attritionRateByYearLevel[$programs[$avalue['st']['program_id']] . '~' . $programTypes[$avalue['st']['program_type_id']]][$college_d['College']['name']]['Freshman']['1st'][strtolower($avalue['st']['gender'])] = $avalue[0]['count(DISTINCT st.studentnumber)'];
								$attritionRateByYearLevel[$programs[$avalue['st']['program_id']] . '~' . $programTypes[$avalue['st']['program_type_id']]][$college_d['College']['name']]['Freshman']['1st']["total"] = $totalRegistredPr;
							}
						} else {
						}
					}
				}
			}
		}
		return $attritionRateByYearLevel;
	}
	public function getDistributionStatsOfStatus(
		$acadamic_year,
		$semester,
		$program_id = null,
		$program_type_id = null,
		$department_id = null,
		$sex = 'all',
		$year_level_id = null,
		$region_id = null,
		$freshman = 0
	) {
		$academicStatus = $this->StudentExamStatus->AcademicStatus->find('list');
		$query = "";
		$student_ids = array();
		$options = array();
		$collegeId = false;
		$departments = array();
		$distributionByDepartmentYearLevel = array();
		$distributionByRegionDepartmentYearLevel = array();


		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and s.region_id=' . $region_id . '';
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$query .= ' and s.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and s.program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and s.program_type_id=' . $program_type_id . '';
			}
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$query .= ' and sec.academicyear="' . $acadamic_year . '"';
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$query .= ' and stexam.academic_year="' .
				$acadamic_year . '"';
		}

		if (isset($semester) && !empty($semester)) {
			$query .= ' and stexam.semester="' . $semester . '"';
		}

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'contain' => array('College', 'YearLevel')
					)
				);
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id), 'contain' => array('College', 'YearLevel' => array('order' => array('YearLevel.name ASC')))));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array(
				'College',
				'YearLevel' => array('order' => array('YearLevel.name ASC'))
			)));
		}

		$distributionByDepartmentYearLevel = array();
		$distributionByRegionYearLevel = array();
		$internalQuery = '';
		$genderQuery = '';
		$graph['series'] = array();
		$graph['labels'] = array();
		$graph['data'] = array();

		if ($freshman == 0) {
			foreach ($departments as $key => $value) {
				foreach ($value['YearLevel'] as $ykey => $yvalue) {
					if (
						!empty($year_level_id)
						&& $year_level_id == $yvalue['name']
					) {
						$internalQuery .= ' and sec.year_level_id="' . $yvalue['id'] . '"';

						$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';
					} else if (empty($year_level_id)) {
						$internalQuery .= ' and sec.year_level_id="' . $yvalue['id'] . '"';

						$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';
					}


					if (!empty($internalQuery) && ($sex == "male" || $sex == "female")) {
						$genderQuery .= ' and s.gender="' . $sex . '"';

						$distributionStatsByStatus = "SELECT count(DISTINCT s.studentnumber),stexam.academic_status_id FROM students AS s, students_sections AS stsec,sections AS sec,year_levels as yrl,departments as dpt,student_exam_statuses as stexam WHERE  stsec.student_id=s.id AND yrl.id = sec.year_level_id and dpt.id=s.department_id and dpt.id=sec.department_id AND sec.id=stsec.section_id AND stexam.student_id=s.id AND sec.id in (select section_id from students_sections) $query  $internalQuery $genderQuery group by stexam.academic_status_id";
						$distResultStatus = $this->query($distributionStatsByStatus);
						
						if (!empty($distResultStatus)) {
							foreach ($distResultStatus as $dkey => $dvalue) {
								if (isset($dvalue['stexam']['academic_status_id'])) {
									$distributionByStatusYearLevel[$value['Department']['name']][$academicStatus[$dvalue['stexam']['academic_status_id']]][strtolower($sex)][$yvalue['name']] = $dvalue[0]['count(DISTINCT s.studentnumber)'];
									$graph['series'][$dvalue['stexam']['academic_status_id']] = $academicStatus[$dvalue['stexam']['academic_status_id']];
									$graph['labels'][$value['Department']['id']] = $value['Department']['name'];
									$graph['data'][$dvalue['stexam']['academic_status_id']][$value['Department']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
								}
							}
						}

						$internalQuery = '';
					} else if (!empty($internalQuery)) {
						$sexList = array('male' => 'male', 'female' => 'female');
						foreach ($sexList as $skey => $svalue) {
							$genderQuery .= ' and s.gender="' . $svalue . '"';

							$distributionStatsByStatus = "SELECT count(DISTINCT s.studentnumber),stexam.academic_status_id FROM students AS s, students_sections AS stsec,sections AS sec,year_levels as yrl,departments as dpt,student_exam_statuses as stexam WHERE  stsec.student_id=s.id AND yrl.id = sec.year_level_id and dpt.id=s.department_id and dpt.id=sec.department_id AND sec.id=stsec.section_id AND stexam.student_id=s.id AND sec.id in (select section_id from students_sections) $query $internalQuery group by stexam.academic_status_id";

							$distResultStatus = $this->query($distributionStatsByStatus);

							if (!empty($distResultStatus)) {
								foreach ($distResultStatus as $dkey => $dvalue) {
									if (isset($dvalue['stexam']['academic_status_id'])) {
										$distributionByStatusYearLevel[$value['Department']['name']][$academicStatus[$dvalue['stexam']['academic_status_id']]][strtolower($svalue)][$yvalue['name']] = $dvalue[0]['count(DISTINCT s.studentnumber)'];
										$graph['series'][$dvalue['stexam']['academic_status_id']] = $academicStatus[$dvalue['stexam']['academic_status_id']];
										$graph['labels'][$value['Department']['id']] = $value['Department']['name'];
										$graph['data'][$dvalue['stexam']['academic_status_id']][$value['Department']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
									}
								}
							}

							$genderQuery = '';
						}

						$internalQuery = '';
					}
				}
			}
		} else {


			$college_id = explode('~', $department_id);
			if (isset($college_id[1]) && !empty($college_id[1])) {

				$colleges = $this->College->find('all', array('conditions' => array('College.id' => $college_id[1]), 'recursive' => -1));
			}

			foreach ($colleges as $ck => $clv) {
				$genderQuery = '';
				$internalQuery = '';

				$internalQuery .= ' and (sec.year_level_id is null or sec.year_level_id=0) ';

				$internalQuery .= ' and sec.college_id="' . $clv['College']['id'] . '" and (sec.department_id is null or sec.department_id=0)';

				if (!empty($internalQuery) && ($sex == "male" || $sex == "female")) {
					$genderQuery .= ' and s.gender="' . $sex . '"';

					$distributionStatsByStatus = "SELECT count(DISTINCT s.studentnumber),stexam.academic_status_id FROM students AS s, students_sections AS stsec,sections AS sec,colleges as clg,student_exam_statuses as stexam WHERE  stsec.student_id=s.id  AND clg.id=s.college_id and clg.id=sec.college_id AND sec.id=stsec.section_id AND stexam.student_id=s.id AND sec.id in (select section_id from students_sections) $query  $internalQuery $genderQuery group by stexam.academic_status_id";
					$distResultStatus = $this->query($distributionStatsByStatus);
					
					if (!empty($distResultStatus)) {
						foreach ($distResultStatus as $dkey => $dvalue) {
							if (isset($dvalue['stexam']['academic_status_id'])) {
								$distributionByStatusYearLevel[$clv['College']['name']][$academicStatus[$dvalue['stexam']['academic_status_id']]][strtolower($sex)]['1st'] = $dvalue[0]['count(DISTINCT s.studentnumber)'];

								$graph['series'][$dvalue['stexam']['academic_status_id']] = $academicStatus[$dvalue['stexam']['academic_status_id']];

								$graph['labels'][$clv['College']['id']] = $clv['College']['name'];
								$graph['data'][$dvalue['stexam']['academic_status_id']][$clv['College']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
							}
						}
					}
				} else if (!empty($internalQuery)) {

					$sexList = array('male' => 'male', 'female' => 'female');
					
					foreach ($sexList as $skey => $svalue) {
						$genderQuery = '';
						$genderQuery .= ' and s.gender="' . $svalue . '"';

						$distributionStatsByStatus = "SELECT count(DISTINCT s.studentnumber),stexam.academic_status_id FROM students AS s, students_sections AS stsec,sections AS sec,colleges as clg,student_exam_statuses as stexam WHERE  stsec.student_id=s.id AND clg.id=s.college_id and clg.id=sec.college_id AND sec.id=stsec.section_id AND stexam.student_id=s.id AND sec.id in (select section_id from students_sections) $query $genderQuery $internalQuery group by stexam.academic_status_id";

						$distResultStatus = $this->query($distributionStatsByStatus);

						if (!empty($distResultStatus)) {
							foreach ($distResultStatus as $dkey => $dvalue) {
								if (isset($dvalue['stexam']['academic_status_id'])) {
									$distributionByStatusYearLevel[$clv['College']['name']][$academicStatus[$dvalue['stexam']['academic_status_id']]][strtolower($svalue)]['1st'] = $dvalue[0]['count(DISTINCT s.studentnumber)'];
									$graph['series'][$dvalue['stexam']['academic_status_id']] = $academicStatus[$dvalue['stexam']['academic_status_id']];
									$graph['labels'][$clv['College']['id']] = $clv['College']['name'];
									$graph['data'][$dvalue['stexam']['academic_status_id']][$clv['College']['id']] += $dvalue[0]['count(DISTINCT s.studentnumber)'];
								}
							}
						}
					}
				}
			}
		}

		$distribution['distributionByStatusYearLevel'] = $distributionByStatusYearLevel;
		$distribution['graph'] = $graph;

		return $distribution;
	}

	public function getDistributionStatsOfGraduate($acadamic_year, $programId, $programTypeId, $departmentId, $sex = "all", $regionId)
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$academicStatus = $this->StudentExamStatus->AcademicStatus->find('list');
		$regions = $this->Region->find('list');
		$query = "";
		$options = array();
		$collegeId = false;
		$departments = array();


		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and st.region_id=' . $region_id . '';
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$query .= ' and st.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and st.program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$query .= ' and st.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and st.program_type_id=' . $program_type_id . '';
			}
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {

			$graduateDate = $AcademicYear->get_academicYearBegainingDate($acadamic_year);
		}

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'contain' => array('College', 'YearLevel')
					)
				);
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id), 'contain' => array('College', 'YearLevel' => array('order' => array('YearLevel.name ASC')))));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array(
				'College',
				'YearLevel' => array('order' => array('YearLevel.name ASC'))
			)));
		}

		$distributionByDepartmentGraduate = array();
		$internalQuery = '';
		foreach ($departments as $key => $value) {

			if ($sex == "male" || $sex == "female") {

				$distributionStatsGraduate = "SELECT
              st1.academic_status_id , st1.created ,
              st.region_id,
              count(DISTINCT st.studentnumber)
FROM  student_exam_statuses st1
JOIN (

SELECT student_id, MAX( created ) created
FROM student_exam_statuses
GROUP BY student_id
)st2 ON st1.student_id = st2.student_id
AND st1.created = st2.created, students AS st
WHERE st1.student_id
IN (

SELECT id
FROM students
WHERE department_id=" . $value['Department']['id'] . "
AND gender = '$sex'
)
AND st1.student_id
IN (

SELECT student_id
FROM graduate_lists
WHERE graduate_date >= '$graduateDate'
)
AND st.id = st1.student_id $query
GROUP BY st1.academic_status_id , st.region_id";
				$distResultGraduate = $this->query($distributionStatsGraduate);
				foreach ($distResultGraduate as $dkey => $dvalue) {
					if (
						isset($dvalue['st1']['academic_status_id']) &&
						isset($dvalue['st']['region_id'])
					) {
						$distributionByDepartmentGraduate[$value['Department']['name']][$regions[$dvalue['st']['region_id']]][strtolower($sex)][$academicStatus[$dvalue['st1']['academic_status_id']]] = $dvalue[0]['count(DISTINCT st.studentnumber)'];
					}
				}
			} else {
				$sexList = array('male' => 'male', 'female' => 'female');
				foreach ($sexList as $skey => $svalue) {
					$distributionStatsGraduate = "SELECT
              st1.academic_status_id , st1.created ,
              st.region_id,
              count(DISTINCT st.studentnumber)
FROM  student_exam_statuses st1
JOIN (

SELECT student_id, MAX( created ) created
FROM student_exam_statuses
GROUP BY student_id
)st2 ON st1.student_id = st2.student_id
AND st1.created = st2.created, students AS st
WHERE st1.student_id
IN (

SELECT id
FROM students
WHERE department_id=" . $value['Department']['id'] . "
AND gender = '$svalue'
)
AND st1.student_id
IN (

SELECT student_id
FROM graduate_lists
WHERE graduate_date >= '$graduateDate'
)
AND st.id = st1.student_id $query
GROUP BY st1.academic_status_id , st.region_id";
					$distResultGraduate = $this->query($distributionStatsGraduate);
					foreach ($distResultGraduate as $dkey => $dvalue) {
						if (
							isset($dvalue['st1']['academic_status_id']) &&
							isset($dvalue['st']['region_id'])
						) {
							$distributionByDepartmentGraduate[$value['Department']['name']][$regions[$dvalue['st']['region_id']]][strtolower($svalue)][$academicStatus[$dvalue['st1']['academic_status_id']]] = $dvalue[0]['count(DISTINCT st.studentnumber)'];
						}
					}
				}
			}
		}

		$distribution['distributionByDepartmentGraduate'] = $distributionByDepartmentGraduate;
		debug($distribution);
		return $distribution;
	}

	public function getNotRegisteredList($acadamic_year, $semester, $program_id = 0, $program_type_id = 0, $department_id = null, $sex = 'all', $year_level_id = null, $region_id = null, $freshman = 0, $exclude_graduated = '')
	{

		$previousAcademicYear = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($acadamic_year, $semester);
		$anotherPreviousSemester = ClassRegistry::init('StudentExamStatus')->getPreviousSemester($previousAcademicYear['academic_year'], $previousAcademicYear['semester']);
		
		$listOfPrevious[] = $previousAcademicYear;
		$listOfPrevious[] = $anotherPreviousSemester;
		
		$activeList = array();

		if (!empty($listOfPrevious)) {
			foreach ($listOfPrevious as $v) {
				$activeList += ClassRegistry::init('StudentExamStatus')->getActiveStudentNotRegistered(
					$v['academic_year'],
					$v['semester'],
					$program_id,
					$program_type_id,
					$department_id,
					$sex,
					$year_level_id,
					$region_id,
					$acadamic_year,
					$semester,
					$freshman,
					$exclude_graduated
				);
			}
		}

		return $activeList;
	}

	public function isPhoneValid($mobilePhoneNumber)
	{
		$count = $this->find('count', array('conditions' => array('Student.phone_mobile' => $mobilePhoneNumber)));
		if ($count) {
			return true;
		} else {

			//check if the phone belongs to staff
			$count = ClassRegistry::init('Staff')->find('count', array('conditions' => array('Staff.phone_mobile' => $mobilePhoneNumber)));
			if ($count) {
				return true;
			} else {
				$count = ClassRegistry::init('Contact')->find('count', array('conditions' => array('Contact.phone_mobile' => $mobilePhoneNumber)));
				if ($count) {
					return true;
				}
			}
		}
		return false;
	}

	public function updateAllAcademicStatus($academic_year, $includingGraduate = 0)
	{
		App::import('Component', 'AcademicYear');
		
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		$admissionYear = $AcademicYear->get_academicYearBegainingDate($academic_year);
		$departments = $this->Department->find('list', array('conditions' => array('Department.active' => 1)));
		
		if (!empty($departments)) {
			foreach ($departments as $dptId => $dptValue) {
				if ($includingGraduate == 1) {
					$students = $this->find('all', array(
						'conditions' => array(
							'Student.department_id' => $dptId,
							'OR' => array(
								'Student.academicyear' => $academic_year,
								'Student.admissionyear' => $admissionYear
							)
						), 
						'recursive' => -1
					));
				} else {
					$students = $this->find('all', array(
						'conditions' => array(
							'Student.department_id' => $dptId,
							'graduated' => 0,
							//'Student.id not in (select student_id from graduate_lists)',
							'OR' => array(
								'Student.academicyear' => $academic_year,
								'Student.admissionyear' => $admissionYear
							)
						), 
						'recursive' => -1
					));
				}


				if (!empty($students)) {
					foreach ($students as $sk => $sv) {
						
						$isTheDeletionSuccessful = $this->StudentExamStatus->deleteAll(array('StudentExamStatus.student_id' => $sv['Student']['id']), false);

						$studentRegisteredLists = $this->CourseRegistration->find('all', array(
							'conditions' => array(
								'CourseRegistration.student_id' => $sv['Student']['id']
							),
							'order' => array('CourseRegistration.academic_year' => 'ASC', 'CourseRegistration.semester' => 'ASC', 'CourseRegistration.id' => 'ASC'),
							'group' => array('CourseRegistration.academic_year', 'CourseRegistration.semester'),
							'recursive' => -1,
						));


						if (!empty($studentRegisteredLists)) {
							foreach ($studentRegisteredLists as $sr => $srv) {
								$status = $this->StudentExamStatus->updateAcdamicStatusByPublishedCourseOfStudent($srv['CourseRegistration']['published_course_id'], $srv['CourseRegistration']['student_id']);
								echo $status;
							}
						}
					}
				}
			}
		}
	}

	public function getIDPrintCount($data, $type = 'count')
	{
		debug($data);
		$query = " s.id is not null  and s.id not in (select student_id
	       from graduate_lists) ";
		$student_ids = array();
		$options = array();
		$collegeId = false;
		$departments = array();
		$programs = $this->Program->find('list');
		$programTypes = $this->ProgramType->find('list');

		$distributionIDPrinting = array();
		$graph['data'] = array();
		$graph['labels'] = array();
		$graph['series'] = array('male', 'female');
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		$college_id = null;

		if (isset($data['program_id']) && !empty($data['program_id'])) {
			$program_ids = explode('~', $data['program_id']);
			if (count($program_ids) > 1) {
				$query .= ' and s.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and s.program_id=' . $data['program_id'] . '';
			}
		}

		if (isset($data['program_type_id']) && !empty($data['program_type_id'])) {
			$program_type_ids = explode('~', $data['program_type_id']);
			if (count($program_type_ids) > 1) {
				$query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and s.program_type_id=' . $data['program_type_id'] . '';
			}
		}
		if (
			isset($data['acadamic_year']) &&
			!empty($data['acadamic_year'])
		) {
			$admittedYear = $AcademicYear->get_academicYearBegainingDate($data['acadamic_year']);

			if (empty($data['year_level_id'])) {

				//$admittedConverted=date('Y-m-d', strtotime($admittedYear. ' - 1 years'));
				$query .= ' and s.admissionyear <="' . $admittedYear . '"';
			} else if (!empty($data['year_level_id'])) {
				//find out the students who are first year in specific academic year

				$admittedConverted = date('Y-m-d', strtotime($admittedYear .
					' - ' . intval($data['year_level_id']) . ' years'));

				$query .= ' and s.admissionyear <="' . $admittedYear . '" and s.admissionyear >="' . $admittedConverted . '"';
			}
		}

		if (
			isset($data['printed_count']) &&
			!empty($data['printed_count']) && $data['printed_count'] > 0
		) {

			$query .= ' and s.print_count =' . $data['printed_count'] . '';
		} else {

			if ($data['printed_count'] == 0 && $data['printed_count'] !== '') {
				$query .= ' and s.print_count=' . $data['printed_count'] . '';
			}
		}

		if (isset($data['gender']) && !empty($data['gender'])) {
			if ($data['gender'] != "all") {
				$query .= ' and s.gender =' . $data['gender'] . '';
			}
		}
		// list out the department
		if (isset($data['department_id']) && !empty($data['department_id'])) {

			$college_id = explode('~', $data['department_id']);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'contain' => array('College', 'YearLevel')
					)
				);
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $data['department_id']), 'contain' => array('College', 'YearLevel')));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array('College', 'YearLevel')));
			$college_id = $this->College->find('list');
		}

		$distributionIDPrinting = array();

		$internalQuery = '';

		foreach ($departments as $key => $value) {
			$internalQuery .= ' and s.department_id="' . $value['Department']['id'] . '"';

			$distributionStatsByDept = "SELECT * FROM students AS s
		         WHERE $query $internalQuery ";

			$distResult = $this->query($distributionStatsByDept);

			foreach ($distResult as $dkey => $dvalue) {
				$year = ClassRegistry::init('Section')->getStudentYearLevel($dvalue['s']['id']);
				if (!isset($year['year'])) {
					$year['year'] = '1st';
				}
				if ($type == "count") {

					if (
						!empty($data['year_level_id'])
						&& $data['year_level_id'] == $year['year'] && $dvalue['s']['print_count'] > 0
					) {

						$distributionIDPrinting['distributionIDPrintingCount'][$value['Department']['name']][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					} else if (
						!empty($data['year_level_id'])
						&& $data['year_level_id'] == $year['year'] && $dvalue['s']['print_count'] == 0
					) {

						$distributionIDPrinting['distributionIDPrintingCount'][$value['Department']['name']][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					} else if (empty($data['year_level_id']) && $dvalue['s']['print_count'] > 0) {

						$distributionIDPrinting['distributionIDPrintingCount'][$value['Department']['name']][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					} else if (empty($data['year_level_id']) &&  $dvalue['s']['print_count'] == 0) {

						$distributionIDPrinting['distributionIDPrintingCount'][$value['Department']['name']][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					}
				} else if ($type == "list") {
					debug($dvalue);
					$distributionIDPrinting['IDPrintingList'][$value['Department']['name'] . '~' . $programs[$dvalue['s']['program_id']] . '~' . $programTypes[$dvalue['s']['program_type_id']] . '~' . $year['year'] . '~' . $dvalue['s']['print_count']][] = $dvalue['s'];
				}
			}
			$internalQuery = '';
		}
		//preengineering

		if (!empty($college_id) && count($college_id) > 1) {
			$internalQuery = '';

			$colleges = $this->College->find(
				'list',
				array('conditions' => array('College.id' => $college_id[0]))
			);
			$internalQuery = " and s.department_id is null and s.college_id='$college_id[1]'";
			$distributionStatsByDept = "SELECT * FROM students AS s
		         WHERE $query $internalQuery ";
			$distResult = $this->query($distributionStatsByDept);
			foreach ($distResult as $dkey => $dvalue) {
				$year = ClassRegistry::init('Section')->getStudentYearLevel($dvalue['s']['id']);
				if (!isset($year['year'])) {
					$year['year'] = '1st';
				}
				if (!isset($year['year'])) {
					$year['year'] = '1st';
				}
				if ($type == "count") {

					if (
						!empty($data['year_level_id'])
						&& $data['year_level_id'] == $year['year'] && $dvalue['s']['print_count'] > 0
					) {

						$distributionIDPrinting['distributionIDPrintingCount'][$colleges[$dvalue['s']['college_id']] . ' Pre'][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					} else if (
						!empty($data['year_level_id'])
						&& $data['year_level_id'] == $year['year'] && $dvalue['s']['print_count'] == 0
					) {
						$distributionIDPrinting['distributionIDPrintingCount'][$colleges[$dvalue['s']['college_id']] . ' Pre'][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					} else if (empty($data['year_level_id']) && $dvalue['s']['print_count'] > 0) {

						$distributionIDPrinting['distributionIDPrintingCount'][$colleges[$dvalue['s']['college_id']] . ' Pre'][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					} else if (empty($data['year_level_id']) &&  $dvalue['s']['print_count'] == 0) {

						$distributionIDPrinting['distributionIDPrintingCount'][$colleges[$dvalue['s']['college_id']] . ' Pre'][$dvalue['s']['print_count']][strtolower($dvalue['s']['gender'])][$year['year']]++;
					}
				} else if ($type == "list") {

					$distributionIDPrinting['IDPrintingList'][$colleges[$dvalue['s']['id']] . ' Pre' . '~' . $programs[$dvalue['Student']['program_id']] . '~' . $programTypes[$dvalue['s']['program_type_id'] . '~' . $year['year'] . '~' . $dvalue['s']['print_count']]][] = $dvalue['s'];
				}
			}
		}

		$distribution['distributionIDPrintingCount'] = $distributionIDPrinting['distributionIDPrintingCount'];
		$distribution['IDPrintingList'] = $distributionIDPrinting['IDPrintingList'];

		return $distribution;
	}


	function getListOfDepartmentStudentsByYearLevelAndSection(
		$college_id = null,
		$department_id = null,
		$program_id = null,
		$program_type_id = null,
		$year_level_id = null,
		$plus_one = 1,
		$gender = null,
		$student_ids = null,
		$accepted_student_ids = null,
		$section_id = 0
	) {

		$non_admitted_students = array();
		$admitted_students = array();
		$filtered_students = array();
		$given_year_level = null;
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		$currentAcademicYear = $AcademicYear->current_academicyear();

		if (!empty($year_level_id)) {
			if (is_numeric($year_level_id)) {
				//if year_level_id parameters is id
				$year_level_detail = ClassRegistry::init('YearLevel')->find(
					'first',
					array(
						'conditions' =>
						array(
							'YearLevel.id' => $year_level_id
						),
						'recursive' => -1
					)
				);
				$given_year_level = substr($year_level_detail['YearLevel']['name'], 0, (strlen($year_level_detail['YearLevel']['name']) - 2));
			} else {
				//if year_level_id parameters is year level name
				$given_year_level = substr($year_level_id, 0, (strlen($year_level_id) - 2));
			}
		}
		$options =
			array(
				'conditions' =>
				array(
					"NOT" => array('Student.id' => $student_ids),
					'Student.id NOT IN (SELECT graduate_lists.student_id from graduate_lists)'
				),
				'fields' => array('Student.id', 'Student.studentnumber', 'Student.full_name'),
				'order' => array('Student.admissionyear DESC'),
				'recursive' => -1
			);
		if (!empty($department_id)) {
			$options['conditions'][] = array('Student.department_id' => $department_id);
		} else if (!empty($college_id)) {
			$options['conditions'][] = array('Student.college_id' => $college_id);
		}
		if (!empty($program_id)) {
			$options['conditions'][] = array('Student.program_id' => $program_id);
		}
		if (!empty($program_type_id)) {
			$options['conditions'][] = array('Student.program_type_id' => $program_type_id);
		}
		if (!empty($gender)) {
			$options['conditions'][] = array('Student.gender' => $gender);
		}

		if (!empty($section_id)) {
			$studentList = ClassRegistry::init('StudentsSection')->find(
				'list',
				array(
					'conditions' => array(
						'StudentsSection.section_id' => $section_id
					),
					'fields' => array('StudentsSection.student_id', 'StudentsSection.student_id')
				)
			);
			if (!empty($studentList)) {
				$options['conditions'][] = array('Student.id' => $studentList);
			}
		}
		$students = $this->find('all', $options);
		debug(count($students));
		foreach ($students as $key => &$student) {

			$year_level = $this->CourseRegistration->Section->getStudentYearLevel($student['Student']['id']);
			$elegibleForAssignment = $this->StudentExamStatus->isElegibleForService($student['Student']['id'], $currentAcademicYear);
			if ((empty($given_year_level) || intval($year_level['year']) == $given_year_level) && $elegibleForAssignment == 1) {
				if ($this->StudentExamStatus->checkFxPresenseInStatus($student['Student']['id']) == 0) {
					debug($student['Student']);
					$student['Student']['fxinlaststatus'] = "Yes";
				} else {
					$student['Student']['fxinlaststatus'] = "No";
				}

				$admitted_students[] = $student;
			}
		}
		debug($admitted_students);
		if (empty($given_year_level) || $given_year_level == 1) {
			$options =
				array(
					'conditions' =>
					array(
						"NOT" => array('AcceptedStudent.id' => $accepted_student_ids),
						'AcceptedStudent.id NOT IN (SELECT students.accepted_student_id from students)'
					),
					'fields' => array('AcceptedStudent.id', 'AcceptedStudent.studentnumber', 'AcceptedStudent.full_name'),
					'recursive' => -1
				);
			if (!empty($department_id)) {
				$options['conditions'][] = array('AcceptedStudent.department_id' => $department_id);
			} else {
				$options['conditions'][] = array('AcceptedStudent.college_id' => $college_id);
			}
			if (!empty($program_id)) {
				$options['conditions'][] = array('AcceptedStudent.program_id' => $program_id);
			}
			if (!empty($program_type_id)) {
				$options['conditions'][] = array('AcceptedStudent.program_type_id' => $program_type_id);
			}
			if (!empty($gender)) {
				$options['conditions'][] = array('AcceptedStudent.sex' => $gender);
			}
			$non_admitted_students = $this->AcceptedStudent->find('all', $options);
		}
		$filtered_students['student'] = $admitted_students;
		$filtered_students['accepted_student'] = $non_admitted_students;
		return $filtered_students;
	}

	public function checkAdmissionTransaction($student_id)
	{
		$isRegisterd = $this->CourseRegistration->find('count', array('conditions' => array('CourseRegistration.student_id' => $student_id)));
		$isBelongToSections = ClassRegistry::init('StudentsSection')->find('count', array('conditions' => array('StudentsSection.student_id' => $student_id)));

		if ($isRegisterd != 0 || $isBelongToSections != 0) {
			return 1;
		}

		return 0;
	}


	function getActiveStudentStatistics($acadamic_year, $semester, $department_id, $region_id = null, $program_id, $program_type_id, $sex = 'all')
	{

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find('all', array('conditions' => array('Department.college_id' => $college_id[1]), 'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC', 'Department.id' => 'ASC'), 'contain' => array('College', 'YearLevel')));
			} else {
				$departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id), 'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC', 'Department.id' => 'ASC'), 'contain' => array('College', 'YearLevel')));
			}
		} else {
			$departments = $this->Department->find('all', array('contain' => array('College', 'YearLevel'), 'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC', 'Department.id' => 'ASC')));
		}

		if (!empty($program_id)) {
			$programs = $this->Program->find('list', array('conditions' => array('Program.id' => $program_id), 'fields' => array('id', 'name')));
		} else {
			$programs = $this->Program->find('list', array('fields' => array('id', 'name')));
		}

		if (!empty($program_type_id)) {
			$programTypes = $this->ProgramType->find('list', array('conditions' => array('ProgramType.id' => $program_type_id)));
		} else {
			$programTypes = $this->ProgramType->find('list');
		}

		if ($sex == "all") {
			$sexList = array('male' => 'male', 'female' => 'female');
		} else {
			$sexList[$sex] = $sex;
		}

		$activeListStatistics = array();
		$collegeDepartmentYearCount = array();

		if (!empty($departments)) {
			foreach ($departments as $key => $value) {
				foreach ($value['YearLevel'] as $ykey => $yvalue) {
					//$collegeDepartmentYearCount[$value['College']['name']] += 1;
					if (isset($collegeDepartmentYearCount[$value['College']['name']])) {
						$collegeDepartmentYearCount[$value['College']['name']] += 1;
					} else {
						$collegeDepartmentYearCount[$value['College']['name']] = 1;
					}
					foreach ($programs as $pid => $pvalue) {
						foreach ($programTypes as $ptypeId => $ptype) {
							$total = 0;
							foreach ($sexList as $skey => $svalue) {
								$activeListStatistics[$pvalue][$value['College']['name']][$value['Department']['name']][$yvalue['name']][$ptype][$skey] = $this->find('count', array(
									'conditions' => array(
										'Student.department_id' => $value['Department']['id'],
										'Student.program_id' => $pid,
										'Student.program_type_id' => $ptypeId,
										'Student.gender' => $skey,
										'Student.graduated' => 0,
										'Student.id in (select student_id from course_registrations where academic_year = "' . $acadamic_year . '" and semester = "' . $semester . '" and year_level_id = ' . $yvalue['id'] . ')'
									)
								));
								$total += $activeListStatistics[$pvalue][$value['College']['name']][$value['Department']['name']][$yvalue['name']][$ptype][$skey];
							}
							$activeListStatistics[$pvalue][$value['College']['name']][$value['Department']['name']][$yvalue['name']][$ptype]['total'] = $total;
						}
					}
				}
			}
		}

		$activeListStatisticsList['result'] = $activeListStatistics;
		$activeListStatisticsList['collegeRowSpan'] = $collegeDepartmentYearCount;
		debug($collegeDepartmentYearCount);
		return $activeListStatisticsList;
	}

	function getStudentConsistencyByAgeRangeStatistics(
		$acadamic_year,
		$semester,
		$department_id,
		$region_id = null,

		$program_id,
		$program_type_id,
		$sex = 'all'
	) {

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find(
					'all',
					array(
						'conditions' => array('Department.college_id' => $college_id[1]),
						'order' => array('Department.college_id DESC'),
						'contain' => array('College', 'YearLevel')
					)
				);
			} else {
				$departments = $this->Department->find('all', array(
					'conditions' => array('Department.id' => $department_id),
					'order' => array('Department.college_id DESC'), 'contain' => array('College', 'YearLevel')
				));
			}
		} else {
			$departments = $this->Department->find('all', array(
				'contain' => array('College', 'YearLevel'),
				'order' => array('Department.college_id DESC')
			));
		}

		if (!empty($program_id)) {
			$programs = $this->Program->find(
				'list',
				array('conditions' => array('Program.id' => $program_id))
			);
		} else {
			$programs = $this->Program->find('list');
		}
		if (!empty($program_type_id)) {

			$programTypes = $this->ProgramType->find(
				'list',
				array('conditions' => array('ProgramType.id' => $program_type_id))
			);
		} else {
			$programTypes = $this->ProgramType->find('list');
		}
		if ($sex == "all") {
			$sexList = array('male' => 'male', 'female' => 'female');
		} else {
			$sexList[$sex] = $sex;
		}
		$activeListStatistics = array();
		$collegeDepartmentYearCount = array();
		$ageRange['<18'] = '18';
		$ageRange['18'] = '18';
		$ageRange['19'] = '19';
		$ageRange['20'] = '20';
		$ageRange['21'] = '21';
		$ageRange['22'] = '22';
		$ageRange['23'] = '23';
		$ageRange['24'] = '24';
		$ageRange['25'] = '25';
		$ageRange['26'] = '26';
		$ageRange['>26'] = '26';
		//echo "One 18 year ago the date was".date("Y-m-d",strtotime("18 years ago",time()));


		foreach ($departments as $key => $value) {
			foreach ($programs as $pid => $pvalue) {
				foreach ($programTypes as $ptypeId => $ptype) {

					foreach ($sexList as $skey => $svalue) {
						$birthdate = '';

						foreach ($ageRange as $agek => $agevalue) {

							$calculatedDate = date("Y-m-d", strtotime($agevalue . 'years ago', time()));
							if ($agek == "<18") {
								//if their birth date is greater than
								$birthdate = 'Student.birthdate > "' . $calculatedDate . '"';
							} else if ($agek == "<26") {
								// if their age is less than
								$birthdate = 'Student.birthdate < "' . $calculatedDate . '"';
							} else {

								$birthdate = 'Student.birthdate <="' . date("Y-m-d", strtotime($agevalue . 'years ago', time())) . '" and Student.birthdate > "' . date("Y-m-d", strtotime($agevalue . 'years 12 months ago ', time())) . '"';
							}

							$activeListStatistics[$pvalue][$agek][$ptype][$skey] += $this->find(
								'count',
								array(
									'conditions' => array(
										'Student.department_id' => $value['Department']['id'],
										'Student.program_id' => $pid,
										'Student.program_type_id' => $ptypeId,
										'Student.gender' => $skey,
										$birthdate,
										'Student.id in (select student_id from course_registrations where academic_year="' . $acadamic_year . '" and semester="' . $semester . '")'
									)
								)
							);
						}
					}
				}
			}
		}
		//debug($activeListStatistics);
		return $activeListStatistics;
	}

	function getAge($userDob)
	{
		//Create a DateTime object using the user's date of birth.
		$dob = new DateTime($userDob);

		//We need to compare the user's date of birth with today's date.
		$now = new DateTime();

		//Calculate the time difference between the two dates.
		$difference = $now->diff($dob);

		//Get the difference in years, as we are looking for the user's age.
		$age = $difference->y;

		//Print it out.
		return $age;
	}
	public function isBorrowerExpired(
		$studentNumber,
		$collegeId
	) {
		$db = ConnectionManager::getDataSource('koha');
		$branchCode[2] = 'AAES';
		$branchCode[1] = 'Main';
		$branchCode[3] = 'AHMAD';
		$branchCode[4] = 'AAGSL';
		$branchCode[6] = 'ASSH';
		if (
			isset($branchCode[$collegeId])
			&& !empty($branchCode[$collegeId])
		) {
			$sql = "SELECT count(*),borrowernumber FROM  borrowers as Borrower where branchcode='" . $branchCode[$collegeId] . "' and categorycode='ST' and cardnumber='" . $studentNumber . "' and
		dateexpiry < CURDATE()";
		} else {
			$sql = "SELECT count(*),borrowernumber FROM  borrowers as Borrower where categorycode='ST' and cardnumber='" . $studentNumber . "' and
		dateexpiry < CURDATE()";
		}
		$result = $db->query($sql);

		if ($result[0][0]['count(*)'] == 0) {
			return true;
		} else {
			return false;
		}
		return false;
	}
	public function synckoha($college_id = 0)
	{
		if (isset($college_id) && !empty($college_id)) {
			$colleges = $this->College->find(
				'all',
				array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
			);
		} else {
			$colleges = $this->College->find(
				'all',
				array('recursive' => -1)
			);
		}
		$db = ConnectionManager::getDataSource('koha');
		$branchCode[2] = 'AAES';
		$branchCode[1] = 'Main';
		$branchCode[3] = 'AHMAD';
		$branchCode[4] = 'AAGSL';
		$branchCode[6] = 'ASSH';
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		foreach ($colleges as $k => $val) {
			//find students who has a promoted status, and update
			$students = $this->StudentExamStatus->getMostRecentStudentStatus($val['College']['id']);

			foreach ($students as $stuval) {
				$sql = "SELECT count(*),borrowernumber FROM  borrowers as Borrower where branchcode='" . $branchCode[$val['College']['id']] . "' and categorycode='ST' and cardnumber='" . $stuval['Student']['studentnumber'] . "'";
				$studentnumberFormatted = str_replace('/', '-', $stuval['Student']['studentnumber']);

				$source = "http://smis.amu.edu.et/media/transfer/img/" . $studentnumberFormatted . ".jpg";
				$file_headers = @get_headers($source);
				if (
					isset($stuval['Student']['status_academic_year'])
					&& !empty($stuval['Student']['status_academic_year'])
				) {
					$newdate = strtotime(
						'+3 months',
						strtotime($AcademicYear->getAcademicYearBegainingDate($stuval['Student']['status_academic_year'], $stuval['Student']['status_semester']))
					);

					$dateExpired = date('Y-m-d', $newdate);
				} else {
					$newdate = strtotime(
						'+3 months',
						strtotime(date('Y-m-d'))
					);
					$dateExpired = date('Y-m-d', $newdate);
				}
				$result = $db->query($sql);

				if ($result[0][0]['count(*)'] == 0) {
					// add the students to the koha database
					$studentNumber = $stuval['Student']['studentnumber'];
					$sur_name = $stuval['Student']['last_name'];
					$first_name = $stuval['Student']['first_name'] . ' ' . $stuval['Student']['middle_name'];
					$address = $stuval['Student']['woreda'] . ' ' . $stuval['Student']['kebele'] . ' ' . $stuval['Student']['house_number'];
					$city = $stuval['City']['name'];
					$branchcode = $branchCode[$val['College']['id']];
					$email = $stuval['Student']['email'];
					$phone = $stuval['Student']['phone_mobile'];
					$dateOfBirth = $stuval['Student']['birthdate'];
					$categoryCode = 'ST';
					$dateEnrolled = $stuval['Student']['admissionyear'];

					$sex = $stuval['Student']['gender'];
					if (isset($stuval['User']['password']) && !empty($stuval['User']['password']) && 0) {
						$password = $stuval['User']['password'];
					} else {
						$password = md5($stuval['Student']['studentnumber']);
					}
					$password =
						$userId = $stuval['Student']['studentnumber'];
					//userid
					$insertToKoha = "INSERT INTO  `borrowers` (`borrowernumber`,`cardnumber`,`surname`,
		`firstname`,`address`,`city`,`branchcode`,`email`,
		`mobile`,`dateofbirth`,`categorycode`,
		`dateenrolled`,`dateexpiry`,`sex`,
		`password`,`userid`) VALUES (NULL,
		\"$studentNumber\",\"$sur_name\",\"$first_name\",
		\"$address\",\"$city\",\"$branchcode\",\"$email\",
		\"$phone\",\"$dateOfBirth\",\"$categoryCode\",
		\"$dateEnrolled\",\"$dateExpired\",\"$sex\",
		\"$password\",\"$userId\")";

					$resultinsert = $db->query($insertToKoha);
					//insert patronimage profile picture
					$LatestInsertedSql = "SELECT borrowernumber FROM  borrowers as Borrower where  cardnumber='" . $stuval['Student']['studentnumber'] . "' limit 1";
					$LatestResult = $db->query($LatestInsertedSql);

					if ($LatestResult[0]['Borrower']['borrowernumber'] && $file_headers[0] != 'HTTP/1.0 404 Not Found') {

						$insertedBorrowerNumber = $LatestResult[0]['Borrower']['borrowernumber'];

						$mimeType = 'image/jpeg';
						//$imagefile=file_get_contents($source);


						$insertPatronImage = "INSERT INTO  `patronimage` (`borrowernumber`,`mimetype`,`imagefile`) VALUES ($insertedBorrowerNumber,'$mimeType','" . mysql_escape_string(file_get_contents($source)) . "')";

						$PatronImageInserted = $db->query($insertPatronImage);
					}

					// }
				} else {
					//update the expire date
					$borrowernumber = $result[0]['Borrower']['borrowernumber'];
					$updateSQL = "UPDATE `borrowers` SET `dateexpiry` = \"$dateExpired\" WHERE `borrowernumber`=$borrowernumber";
					$resultUpdate = $db->query($updateSQL);
				}
			}
		}
	}


	public function extendKohaBorrowerExpireDate($student_ids = array())
	{

		$db = ConnectionManager::getDataSource('koha');
		$branchCode[2] = 'AAES';
		$branchCode[1] = 'Main';
		$branchCode[3] = 'AHMAD';
		$branchCode[4] = 'AAGSL';
		$branchCode[6] = 'ASSH';
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);
		//find students who has a promoted status, and update
		$students = $this->StudentExamStatus->getMostRecentStudentStatusForKoha($student_ids);

		foreach ($students as $stuval) {
			$sql = "SELECT count(*),borrowernumber FROM  borrowers as Borrower where branchcode='" . $branchCode[$stuval['College']['id']] . "' and categorycode='ST' and cardnumber='" . $stuval['Student']['studentnumber'] . "'";
			$studentnumberFormatted = str_replace('/', '-', $stuval['Student']['studentnumber']);

			$source = "http://smis.amu.edu.et/media/transfer/img/" . $studentnumberFormatted . ".jpg";
			$file_headers = @get_headers($source);
			if (
				isset($stuval['Student']['status_academic_year'])
				&& !empty($stuval['Student']['status_academic_year'])
			) {
				$newdate = strtotime(
					'+3 months',
					strtotime($AcademicYear->getAcademicYearBegainingDate($stuval['Student']['status_academic_year'], $stuval['Student']['status_semester']))
				);

				$dateExpired = date('Y-m-d', $newdate);
			} else {
				$newdate = strtotime(
					'+3 months',
					strtotime(date('Y-m-d'))
				);
				$dateExpired = date('Y-m-d', $newdate);
			}
			$result = $db->query($sql);

			if ($result[0][0]['count(*)'] == 0) {
				// add the students to the koha database
				$studentNumber = $stuval['Student']['studentnumber'];
				$sur_name = $stuval['Student']['last_name'];
				$first_name = $stuval['Student']['first_name'] . ' ' . $stuval['Student']['middle_name'];
				$address = $stuval['Student']['woreda'] . ' ' . $stuval['Student']['kebele'] . ' ' . $stuval['Student']['house_number'];
				$city = $stuval['City']['name'];
				$branchcode = $branchCode[$stuval['College']['id']];
				$email = $stuval['Student']['email'];
				$phone = $stuval['Student']['phone_mobile'];
				$dateOfBirth = $stuval['Student']['birthdate'];
				$categoryCode = 'ST';
				$dateEnrolled = $stuval['Student']['admissionyear'];

				$sex = $stuval['Student']['gender'];
				if (isset($stuval['User']['password']) && !empty($stuval['User']['password']) && 0) {
					$password = $stuval['User']['password'];
				} else {
					$password = md5($stuval['Student']['studentnumber']);
				}
				$password = $userId = $stuval['Student']['studentnumber'];
				//userid
				$insertToKoha = "INSERT INTO  `borrowers` (`borrowernumber`,`cardnumber`,`surname`,
				`firstname`,`address`,`city`,`branchcode`,`email`,
				`mobile`,`dateofbirth`,`categorycode`,
				`dateenrolled`,`dateexpiry`,`sex`,
				`password`,`userid`) VALUES (NULL,
				\"$studentNumber\",\"$sur_name\",\"$first_name\",
				\"$address\",\"$city\",\"$branchcode\",\"$email\",
				\"$phone\",\"$dateOfBirth\",\"$categoryCode\",
				\"$dateEnrolled\",\"$dateExpired\",\"$sex\",
				\"$password\",\"$userId\")";

				$resultinsert = $db->query($insertToKoha);
				//insert patronimage profile picture
				$LatestInsertedSql = "SELECT borrowernumber FROM  borrowers as Borrower where  cardnumber='" . $stuval['Student']['studentnumber'] . "' limit 1";
				$LatestResult = $db->query($LatestInsertedSql);

				if ($LatestResult[0]['Borrower']['borrowernumber'] && $file_headers[0] != 'HTTP/1.0 404 Not Found') {

					$insertedBorrowerNumber = $LatestResult[0]['Borrower']['borrowernumber'];

					$mimeType = 'image/jpeg';
					//$imagefile=file_get_contents($source);


					$insertPatronImage = "INSERT INTO  `patronimage` (`borrowernumber`,`mimetype`,`imagefile`) VALUES ($insertedBorrowerNumber,'$mimeType','" . mysql_escape_string(file_get_contents($source)) . "')";

					$PatronImageInserted = $db->query($insertPatronImage);
				}

				// }
			} else {
				//update the expire date
				$borrowernumber = $result[0]['Borrower']['borrowernumber'];
				$updateSQL = "UPDATE `borrowers` SET `dateexpiry` = \"$dateExpired\" WHERE `borrowernumber`=$borrowernumber";
				$resultUpdate = $db->query($updateSQL);
			}
		}
		return true;
	}

	public function getRegisteredStudentList($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null, $sex = 'all', $year_level_id = null, $region_id = null, $freshman = 0, $exclude_graduated = '')
	{

		$query = " s.id is not null ";
		$queryR = "";

		$student_ids = array();
		$options = array();
		$collegeId = false;
		$departments = array();
		$distributionByDepartmentYearLevel = array();
		$distributionByRegionDepartmentYearLevel = array();

		if (isset($region_id) && !empty($region_id)) {
			$query .= ' and s.region_id=' . $region_id . '';
		}

		if (isset($program_id) && !empty($program_id)) {
			$program_ids = explode('~', $program_id);
			if (count($program_ids) > 1) {
				$query .= ' and s.program_id=' . $program_ids[1] . '';
			} else {
				$query .= ' and s.program_id=' . $program_id . '';
			}
		}

		if (isset($program_type_id) && !empty($program_type_id)) {
			$program_type_ids = explode('~', $program_type_id);
			if (count($program_type_ids) > 1) {
				$query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
			} else {
				$query .= ' and s.program_type_id=' . $program_type_id . '';
			}
		}

		if (!empty($exclude_graduated) && $exclude_graduated == 1) {
			$query .= ' and s.graduated = 0';
		}

		if (isset($acadamic_year) && !empty($acadamic_year)) {
			$queryR .= ' and reg.academic_year="' . $acadamic_year . '"';
		}

		if (isset($semester) && !empty($semester)) {
			$queryR .= ' and reg.semester="' . $semester . '"';
		}

		if (!empty($acadamic_year)) {
			$queryR .= ' and reg.academic_year="' . $acadamic_year . '"';
		}

		// list out the department
		if (isset($department_id) && !empty($department_id)) {
			$college_id = explode('~', $department_id);
			if (count($college_id) > 1) {
				$departments = $this->Department->find('all', array(
					'conditions' => array(
						'Department.college_id' => $college_id[1],
						'Department.active' => 1
					),
					'contain' => array('College', 'YearLevel')
				));
				$college_ids[$college_id[1]] = $college_id[1];
				
				$colleges = $this->College->find('all', array(
					'conditions' => array('College.id' => $college_ids),
					'recursive' => -1
				));

			} else {
				$departments = $this->Department->find('all', array(
					'conditions' => array(
						'Department.id' => $department_id,
						'Department.active' => 1
					), 
					'contain' => array(
						'College', 
						'YearLevel' => array(
							'order' => array('YearLevel.name ASC')
						)
					)
				));
			}
		} else {
			$departments = $this->Department->find('all', array(
				'conditions' => array(
					'Department.active' => 1
				), 
				'contain' => array(
					'College',
					'YearLevel' => array(
						'order' => array('YearLevel.name ASC')
					)
				)
			));
		}

		if ($freshman == 1) {
			$departments = array();
		}

		$studentListNotRegistered = array();
		$studentListRegistered = array();
		$internalQuery = '';
		$internalQueryS = '';
		$genderQuery = '';
		

		if ($freshman == 0) {
			foreach ($departments as $key => $value) {
				foreach ($value['YearLevel'] as $ykey => $yvalue) {
					if (!empty($year_level_id) && $year_level_id == $yvalue['name']) {
						$internalQuery .= ' and reg.year_level_id="' . $yvalue['id'] . '"';
						$internalQuery .= ' and sec.department_id="' . $value['Department']['id'] . '"';

						$internalQueryS .= ' and sec.department_id="' . $value['Department']['id'] . '"';
						$internalQueryS .= ' and sec.year_level_id="' . $yvalue['id'] . '"';
					} else if (empty($year_level_id) && isset($yvalue['id'])) {

						$internalQuery .= ' and reg.year_level_id="' . $yvalue['id'] . '"';
						$internalQuery .= ' and sec.department_id="' . $yvalue['department_id'] . '"';

						$internalQueryS .= ' and sec.department_id="' . $yvalue['department_id'] . '"';
						$internalQueryS .= ' and sec.year_level_id="' . $yvalue['id'] . '"';
						debug($internalQueryS);
					}

					if (!empty($internalQuery)) {
						if ($sex == "male" || $sex == "female") {
							$sexList[$sex] = $sex;
						} else {
							$sexList = array('male' => 'male', 'female' => 'female');
						}

						foreach ($sexList as $skey => $svalue) {
							$genderQuery .= ' and s.gender="' . $svalue . '"';
							$getRegistrationListSQL = "SELECT 1 as a,group_concat(DISTINCT reg.student_id SEPARATOR',') FROM sections AS sec,course_registrations as reg WHERE  sec.id=reg.section_id $queryR $internalQuery AND reg.student_id not in (select student_id from graduate_lists where student_id is not null )AND reg.student_id in (select s.id from students as s where  $query $genderQuery) group by a";
							$resultGetRegistrationStatistics = $this->query($getRegistrationListSQL);
							if (isset($resultGetRegistrationStatistics[0][0]["group_concat(DISTINCT reg.student_id SEPARATOR',')"])) {
								$commaSeparatedRegisteredList = trim($resultGetRegistrationStatistics[0][0]["group_concat(DISTINCT reg.student_id SEPARATOR',')"], ',');
								$studentRegisteredLists = explode(',', $resultGetRegistrationStatistics[0][0]["group_concat(DISTINCT reg.student_id SEPARATOR',')"]);
								// $studentListRegistered=array_merge($studentListRegistered,$studentRegisteredLists);
								$studentListRegistered = $studentRegisteredLists;
								foreach ($studentListRegistered
									as $key => $stvalue) {

									$checkRegistered = $this->CourseRegistration->find('all', array('conditions' => array(
										'CourseRegistration.student_id' => $stvalue,
										'CourseRegistration.semester' => $semester,
										'CourseRegistration.academic_year' => $acadamic_year
									), 'contain' => array(
										'PublishedCourse' => array('Course'),
										'Section'
									)));

									if ($checkRegistered) {
										$studentDetail = $this->find(
											'first',
											array('conditions' => array('Student.id' => trim($stvalue)), 'fields' => array('Student.id', 'Student.full_name', 'Student.department_id', 'Student.gender', 'Student.studentnumber'), 'contain' => array('Program' => array('id', 'name'), 'ProgramType' => array('id', 'name'), 'College' => array('id', 'name')))
										);
										foreach ($checkRegistered as $cck => $ccv) {
											$studentDetail['Student']['credithour'] += $ccv['PublishedCourse']['Course']['credit'];
											$studentDetail['Student']['sectionName'] = $ccv['Section']['name'];
										}


										if ($studentDetail['Student']['department_id'] == $value['Department']['id']) {
											$studentListNotRegistered[$studentDetail['College']['name'] . '~' . $value['Department']['name'] . '~' . $studentDetail['Program']['name'] . '~' . $studentDetail['ProgramType']['name'] . '~' . $yvalue['name']][trim($stvalue)] = $studentDetail;
										}
									}
								}
							}
							$genderQuery = '';
							$internalQueryS = '';
							$queryPreviousStatus = '';
						}
						$internalQuery = '';
						$genderQuery = '';
						$internalQueryS = '';
						$queryPreviousStatus = '';
					}
				}
			}
		} else {
			//freshman
			foreach ($colleges as $key => $value) {
				$internalQuery .= ' and (reg.year_level_id is null or  reg.year_level_id=0) ';
				$internalQuery .= ' and (sec.department_id is null or sec.department_id=0 )';
				$internalQueryS .= ' and sec.college_id="' . $value['College']['id'] . '"';
				$sectionLists = ClassRegistry::init('Section')->find(
					'list',
					array(
						'conditions' => array(

							"Section.college_id" => $value['College']['id'],
							'Section.academicyear' => $acadamic_year,
							'Section.department_id is null',
							//'Section.year_level_id is null'
						),
						'fields' => array('Section.id', 'Section.id')
					)
				);

				if (isset($sectionLists) && !empty($sectionLists)) {

					$secstulist = ClassRegistry::init('StudentsSection')->find(
						'list',
						array(
							'conditions' => array(

								"StudentsSection.section_id" => $sectionLists
							),
							'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id')
						)
					);
				}
				debug($internalQuery);
				if (!empty($internalQuery)) {
					if ($sex == "male" || $sex == "female") {
						$sexList[$sex] = $sex;
					} else {
						$sexList = array('male' => 'male', 'female' => 'female');
					}

					foreach ($sexList as $skey => $svalue) {
						$genderQuery .= ' and s.gender="' . $svalue . '"';
						$getRegistrationListSQL = "SELECT 1 as a,group_concat(DISTINCT reg.student_id SEPARATOR',') FROM sections AS sec,course_registrations as reg WHERE  sec.id=reg.section_id $queryR $internalQuery AND reg.student_id not in (select student_id from graduate_lists where student_id is not null )AND reg.student_id in (select s.id from students as s where  $query $genderQuery) group by a";
						$resultGetRegistrationStatistics = $this->query($getRegistrationListSQL);
						if (isset($resultGetRegistrationStatistics[0][0]["group_concat(DISTINCT reg.student_id SEPARATOR',')"])) {
							$commaSeparatedRegisteredList = trim($resultGetRegistrationStatistics[0][0]["group_concat(DISTINCT reg.student_id SEPARATOR',')"], ',');
							$studentRegisteredLists = explode(',', $resultGetRegistrationStatistics[0][0]["group_concat(DISTINCT reg.student_id SEPARATOR',')"]);
							// $studentListRegistered=array_merge($studentListRegistered,$studentRegisteredLists);
							$studentListRegistered = $studentRegisteredLists;
							foreach ($studentListRegistered
								as $key => $stvalue) {

								$checkRegistered = $this->CourseRegistration->find('all', array('conditions' => array(
									'CourseRegistration.student_id' => $stvalue,
									'CourseRegistration.semester' => $semester,
									'CourseRegistration.academic_year' => $acadamic_year
								), 'contain' => array(
									'PublishedCourse' => array('Course'),
									'Section'
								)));

								if ($checkRegistered) {
									$studentDetail = $this->find(
										'first',
										array('conditions' => array('Student.id' => trim($stvalue)), 'fields' => array('Student.id', 'Student.full_name', 'Student.college_id', 'Student.gender', 'Student.studentnumber'), 'contain' => array('Program' => array('id', 'name'), 'ProgramType' => array('id', 'name'), 'College' => array('id', 'name')))
									);
									$studentDetail['Student']['credithour'] = 0;
									foreach ($checkRegistered as $cck => $ccv) {
										$studentDetail['Student']['credithour'] +=
											$ccv['PublishedCourse']['Course']['credit'];
										$studentDetail['Student']['sectionName'] = $ccv['Section']['name'];
									}


									$studentListNotRegistered[$studentDetail['College']['name'] . '~' . "Fresh" . '~' . $studentDetail['Program']['name'] . '~' . $studentDetail['ProgramType']['name'] . '~' . '1st Year'][trim($stvalue)] = $studentDetail;
								}
							}
						}
						$genderQuery = '';
						$internalQueryS = '';
						$queryPreviousStatus = '';
					}
					$internalQuery = '';
					$genderQuery = '';
					$internalQueryS = '';
					$queryPreviousStatus = '';
				}
			}
		}

		return $studentListNotRegistered;
	}

	public function updateAcademicYear($department_id = null)
	{
		if (!empty($department_id)) {
			$students = $this->find('all', array(
				'conditions' => array(
					'Student.department_id' => $department_id, 
					'Student.academicyear is null '
				), 
				'contain' => array('AcceptedStudent')
			));
		} else {
			$students = $this->find('all', array(
				'conditions' => array(
					'Student.academicyear is null'
				),
				'contain' => array('AcceptedStudent')
			));
		}


		if (!empty($students)) {
			foreach ($students as $skey => $svalue) {
				$updateStudents = array();
				
				if (isset($svalue['AcceptedStudent']['academicyear']) && !empty($svalue['AcceptedStudent']['academicyear'])) {
					
					$updateStudents['Student']['id'] = $svalue['Student']['id'];
					$updateStudents['Student']['academicyear'] = $svalue['AcceptedStudent']['academicyear'];
					
					if ($this->save($updateStudents, array('validate' => false))) {
						debug($updateStudents);
					} else {
						debug($this->invalidFields());
					}
				}
			}
		}
	}

	public function updateGraduated($department_id = null)
	{
		if (!empty($department_id)) {
			$students = $this->find('all', array(
				'conditions' => array(
					'Student.department_id' => $department_id, 
					'Student.graduated' => 0
				), 
				'contain' => array('GraduateList')
			));
		} else {
			$students = $this->find('all', array(
				'conditions' => array(
					'Student.graduated' => 0
				), 
				'contain' => array('GraduateList')
			));
		}


		if (!empty($students)) {
			foreach ($students as $skey => $svalue) {
				$updateStudents = array();
				
				if (isset($svalue['GraduateList']['student_id']) && !empty($svalue['GraduateList']['student_id'])) {
					
					$updateStudents['Student']['id'] = $svalue['Student']['id'];
					$updateStudents['Student']['graduated'] = 1;
					
					if ($this->save($updateStudents, array('validate' => false))) {
						debug($updateStudents);
					} else {
						debug($this->invalidFields());
					}
				}
			}
		}
	}
}