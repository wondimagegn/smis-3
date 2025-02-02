<?php
App::uses('AppModel', 'Model');
/**
 * OnlineApplicantStatus Model
 *
 * @property OnlineApplicant $OnlineApplicant
 */
class OnlineApplicantStatus extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'status' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select status.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'remark' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide remark.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'OnlineApplicant' => array(
			'className' => 'OnlineApplicant',
			'foreignKey' => 'online_applicant_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
