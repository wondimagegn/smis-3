<?php
class Role extends AppModel {
	public $name = 'Role';
	public $displayField = 'name';
	public $actsAs = array('Acl' => array('type' => 'requester'));
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name is required.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'role_id',
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
		 'PasswordChanageVote' => array(
			        'className' => 'PasswordChanageVote',
			        'foreignKey' => 'role_id',
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
	
	public function parentNode(){
	    return null;
	}
}
