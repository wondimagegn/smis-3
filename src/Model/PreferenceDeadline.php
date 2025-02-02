<?php
class PreferenceDeadline extends AppModel {
	var $name = 'PreferenceDeadline';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
		        'className' => 'User',
		        'foreignKey' => 'user_id',
		        'condition'=>'',
		        'fields' => '',
		        'order'=> ''
		)
	);
	

}

?>
