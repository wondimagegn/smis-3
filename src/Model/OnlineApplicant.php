<?php
App::uses('AppModel', 'Model');
/**
 * OnlineApplicant Model
 *
 * @property College $College
 * @property Department $Department
 * @property Program $Program
 * @property ProgramType $ProgramType
 */
class OnlineApplicant extends AppModel
{

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(

		'college_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select the college you want to join',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'department_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select the department you want to join',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'program_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select the study level',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'program_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please select the admission type',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'academic_year' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select the academic year you want to start.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'semester' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select the semester you want to start.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'undergraduate_university_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide undergraduate university name.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'undergraduate_university_cgpa' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Provide undergraduate university CPGA.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'undergraduate_university_field_of_study' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide undergraduate university field of study.',
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),

		'financial_support' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide financial support type.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name_of_sponsor' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide name of sponsor.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),

		'first_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide first name.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'father_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide father name.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'grand_father_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Provide grand father name.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'date_of_birth' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => 'Please provide birth date.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'gender' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select  gender.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'mobile_phone' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide the mobile number.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please provide the email address.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),

		'file' => array(
			'resource'   => array('rule' => 'checkResource'),
			'access'     => array('rule' => 'checkAccess'),
			'location'   => array('rule' => array('checkLocation', array(
				MEDIA_TRANSFER, '/tmp/'
			))),
			'permission' => array('rule' => array('checkPermission', '*')),
			'size'       => array(
				'rule' => array('checkSize', '10M'),
				'message' => 'File size is more than 10M.'
			),
			'pixels'     => array('rule' => array('checkPixels', '1600x1600')),
			'extension'  => array('rule' => array('checkExtension', false, array(
				'pdf', 'tmp'
			))),
			'mimeType'   => array('rule' => array('checkMimeType', false, array(
				'application/pdf'
			)))
		),


	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
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

	public $virtualFields = array(
		'full_name' => "CONCAT(OnlineApplicant.first_name, ' ',OnlineApplicant.father_name,' ',OnlineApplicant.grand_father_name)",
	);
	public $hasMany = array(
		'OnlineApplicantStatus' => array(
			'className' => 'OnlineApplicantStatus',
			'foreignKey' => 'online_applicant_id',
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
			'conditions'    => array('model' => 'OnlineApplicant'),
			'dependent' => true,
		),
	);

	public function nextTrackingNumber()
	{
		$nextapplicationnumber = $this->find(
			'first',
			array('order' => array('OnlineApplicant.created DESC'))
		);
		if (
			isset($nextapplicationnumber)
			&& !empty($nextapplicationnumber)
		) {
			return $nextapplicationnumber['OnlineApplicant']['applicationnumber'] + 1;
		}
		return 12100;
	}
	public function isAppliedFordmittion($data)
	{
		$applied = $this->find(
			'first',
			array(
				'conditions' => array(
					'OnlineApplicant.department_id' => $data['OnlineApplicant']['department_id'],
					'OnlineApplicant.college_id' => $data['OnlineApplicant']['college_id'],

					'OnlineApplicant.program_id' => $data['OnlineApplicant']['program_id'],
					'OnlineApplicant.program_type_id' => $data['OnlineApplicant']['program_type_id'],
					'OnlineApplicant.academic_year' => $data['OnlineApplicant']['academic_year'],
					'OnlineApplicant.semester' => $data['OnlineApplicant']['semester'],
					'OnlineApplicant.email' => $data['OnlineApplicant']['email']

				),
				'order' => array('OnlineApplicant.created DESC'),
				'recursive' => -1
			)
		);
		debug($data);
		debug($applied);
		if (isset($applied) && !empty($applied)) {
			return $applied['OnlineApplicant']['applicationnumber'];
		}
		return 0;
	}


	function checkUnique($data, $fieldName)
	{
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$valid = $this->isUnique(array($fieldName => $data));
		}
		return $valid;
	}
	function preparedAttachment($data = null)
	{

		foreach ($data['Attachment'] as $in =>  &$dv) {

			if (
				empty($dv['file']['name']) && empty($dv['file']['type'])
				&& empty($dv['tmp_name'])
			) {
				unset($data['Attachment'][$in]);
			} else if ($in == 0) {
				$dv['model'] = 'OnlineApplicant';
				$dv['group'] = 'OnlineApplicantFiles';
			} else if ($in == 1) {
				$dv['model'] = 'OnlineApplicant';
				$dv['group'] = 'OnlineApplicantPaymentSlips';
			}
		}
		return $data;
	}
}