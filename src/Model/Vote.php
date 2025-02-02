<?php
class Vote extends AppModel {
	var $name = 'Vote';
	var $displayField = 'task';
	
	var $belongsTo = array(
		'Requester' => array(
			'className' => 'User',
			'foreignKey' => 'requester_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ApplicableOn' => array(
			'className' => 'User',
			'foreignKey' => 'applicable_on_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ConfirmedBy' => array(
			'className' => 'User',
			'foreignKey' => 'confirmed_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function getListOfTaskForConfirmation($user_id = null) {
		$valid_date_from = date("Y-m-d H:i:s", mktime(date("H")-72, date("i"), date("s"), date("n"), date("j"), date("Y")));
		$tasks_for_confirmation = $this->find('all',
			array(
				'conditions' =>
				array(
					'Vote.requester_user_id <>' => $user_id,
					'Vote.confirmation' => 0,
					'Vote.created >= ' => $valid_date_from
				),
				'contain' =>
				array(
					'Requester' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
					'ApplicableOn' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username',
						'role_id',
						'Staff' =>
						array(
							'Department',
							'College'
						)
					),
					'ConfirmedBy' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
				),
				'order' =>
				array(
					'Vote.created DESC'
				)
			)
		);
		return $tasks_for_confirmation;
	}
	
	function getListOfMyTaskForConfirmation($user_id = null) {
		$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j")-7, date("Y")));
		$tasks_for_confirmation = $this->find('all',
			array(
				'conditions' =>
				array(
					'Vote.requester_user_id' => $user_id,
					'Vote.created >= ' => $valid_date_from
				),
				'contain' =>
				array(
					'Requester' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
					'ApplicableOn' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username',
						'role_id',
						'Staff' =>
						array(
							'Department',
							'College'
						)
					),
					'ConfirmedBy' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
				),
				'order' =>
				array(
					'Vote.created DESC'
				)
			)
		);
		return $tasks_for_confirmation;
	}
	
	function getListOfConfirmedTasks($user_id = null) {
		$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j")-7, date("Y")));
		$confirmed_tasks = $this->find('all',
			array(
				'conditions' =>
				array(
					'Vote.confirmed_by' => $user_id,
					'Vote.created >= ' => $valid_date_from
				),
				'contain' =>
				array(
					'Requester' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
					'ApplicableOn' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username',
						'role_id',
						'Staff' =>
						array(
							'Department',
							'College'
						)
					),
					'ConfirmedBy' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
				),
				'order' =>
				array(
					'Vote.created DESC'
				)
			)
		);
		return $confirmed_tasks;
	}
	
	function getListOfOtherAdminTasks($user_id = null) {
		$valid_date_from = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j")-30, date("Y")));
		$confirmed_tasks = $this->find('all',
			array(
				'conditions' =>
				array(
					'Vote.requester_user_id <> ' => $user_id,
					'Vote.confirmed_by <> ' => $user_id,
					'Vote.confirmed_by IS NOT NULL',
					'Vote.created >= ' => $valid_date_from
				),
				'contain' =>
				array(
					'Requester' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
					'ApplicableOn' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username',
						'role_id',
						'Staff' =>
						array(
							'Department',
							'College'
						)
					),
					'ConfirmedBy' =>
					array(
						'first_name',
						'last_name',
						'middle_name',
						'username'
					),
				),
				'order' =>
				array(
					'Vote.created DESC'
				)
			)
		);
		return $confirmed_tasks;
	}
	
}
