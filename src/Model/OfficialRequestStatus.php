<?php
App::uses('AppModel', 'Model');
/**
 * OfficialRequestStatus Model
 *
 * @property OfficialTranscriptRequest $OfficialTranscriptRequest
 */
class OfficialRequestStatus extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'official_transcript_request_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	
	public $belongsTo = array(
		'OfficialTranscriptRequest' => array(
			'className' => 'OfficialTranscriptRequest',
			'foreignKey' => 'official_transcript_request_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
