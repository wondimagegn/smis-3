<?php
class PasswordHistory extends AppModel {
	var $name = 'PasswordHistory';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function isThePasswordUsedBefore($user_id = null, $password = null) {
		$passwordHistories = $this->find('all',
			array(
				'conditions' =>
				array(
					'PasswordHistory.user_id' => $user_id,
				)
			)
		);
		$user = $this->User->find('first',
			array(
				'conditions' =>
				array(
					'User.id' => $user_id
				)
			)
		);
		$password = Security::hash($password, null, true);
		foreach($passwordHistories as $passwordHistory) {
			if(strcmp($passwordHistory['PasswordHistory']['password'], $password) == 0) {
				return true;
			}
		}
		if(strcmp($user['User']['password'], $password) == 0) {
			return true;
		}
		return false;
	}
	
}
