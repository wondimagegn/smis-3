<?php
App::uses('AppModel', 'Model');
class Help extends AppModel
{
	public $name = 'Help';
	public $displayField = 'title';

	public $validate = array(
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide the title',
				'allowEmpty' => false,
				'required' => true,
			),
		),
		'document_release_date' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => 'Please Provide a valid help release date.',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'version' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please Provide Help version number',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);

	public $hasMany = array(
		'Attachment' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array('model' => 'Help'),
			'dependent' => true,
			'order' => array('Attachment.created' => 'DESC')
		),
	);

	public function preparedAttachment($data = null)
	{
		if (isset($data['Attachment']) && !empty($data['Attachment'])) {
			foreach ($data['Attachment'] as $in => &$dv) {
				if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
					unset($data['Attachment'][$in]);
				} else {
					$dv['model'] = 'Help';
					$dv['group'] = 'attachment';
				}
			}
		}
		return $data;
	}
}
