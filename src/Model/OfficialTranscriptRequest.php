<?php
App::uses('AppModel', 'Model');
/**
 * OfficialTranscriptRequest Model
 *
 * @property OfficialRequestStatus $OfficialRequestStatus
 */
class OfficialTranscriptRequest extends AppModel
{

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'trackingnumber' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' =>
				'Please provide tracking number.',
				'allowEmpty' => false,
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => array('checkUnique', 'trackingnumber'),
				'message' => 'Tracking number is taken. Use another'
			)
		),
		'first_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide first name.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'father_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide father name.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'grand_father' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide grand father name.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' =>
				'Please provide email.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'mobile_phone' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide mobile phone.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'studentnumber' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide ID. Number.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'admissiontype' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide admission type.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'degreetype' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide degree type.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'institution_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide institution name.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'institution_address' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide institution address.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
		'recipent_country' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' =>
				'Please provide institution country.',
				'allowEmpty' => false,
				//'on' => 'create',
			),
		),
	);
	public $virtualFields = array(
		'full_name' => "CONCAT(OfficialTranscriptRequest.first_name, ' ',OfficialTranscriptRequest.father_name,' ',OfficialTranscriptRequest.grand_father)",
	);
	public $hasMany = array(
		'OfficialRequestStatus' => array(
			'className' => 'OfficialRequestStatus',
			'foreignKey' => 'official_transcript_request_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	public function nextTrackingNumber()
	{
		$nextTrackingNumber = $this->find(
			'first',
			array('order' => array('OfficialTranscriptRequest.id DESC'))
		);
		if (
			isset($nextTrackingNumber)
			&& !empty($nextTrackingNumber)
		) {
			return $nextTrackingNumber['OfficialTranscriptRequest']['trackingnumber'] + 1;
		}
		return 100000;
	}

	function checkUnique($data, $fieldName)
	{
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$valid = $this->isUnique(array($fieldName => $data));
		}
		return $valid;
	}
}