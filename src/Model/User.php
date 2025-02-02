<?php
App::uses('CakeEvent', 'Event');
App::import('Sanitize');
App::uses('AppModel', 'Model');
class User extends AppModel
{
	public $name = 'User';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $actsAs = array(
		'Acl' => array('type' => 'requester'), 
		'Containable', 
		'Tools.Logable' => array(
			'change' => 'full',
			//'change' => 'list',
			'description_ids' => 'true',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
	);

	public $virtualFields = array('full_name' => "CONCAT(User.first_name, ' ', User.middle_name,' ',User.last_name)");

	public $validate = array(
		'username' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Your username is required'
			),
			'unique' => array(
				'rule' => array('checkUnique', 'username'),
				'message' => 'User name taken. Use another'
			)
		),
		'passwd' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'Your password is required'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid email address.',
				'allowEmpty' => true,
				'required' => false,
				//'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => 'isUnique',
				//'on' => 'update',
				'message' => 'The email address is used by someone. Please provided unique different email.',
			)
		),
		'role_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'allowEmpty' => false,
				'message' => 'Please select user role.'
			)
		),
	);
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	public $hasMany = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'user_id',
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
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'user_id',
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
		'TaskRequester' => array(
			'className' => 'Vote',
			'foreignKey' => 'requester_user_id',
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
		'Mailer' => array(
			'className' => 'Mailer',
			'foreignKey' => 'user_id',
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
		'PasswordHistory' => array(
			'className' => 'PasswordHistory',
			'foreignKey' => 'user_id',
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
		'NumberProcess' => array(
			'className' => 'NumberProcess',
			'foreignKey' => 'user_id',
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
		'TaskApplicable' => array(
			'className' => 'Vote',
			'foreignKey' => 'applicable_on_user_id',
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
		'TaskConfirmer' => array(
			'className' => 'Vote',
			'foreignKey' => 'applicable_on_user_id',
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
		'AutoMessage' => array(
			'className' => 'AutoMessage',
			'foreignKey' => 'user_id',
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
		'AcceptedStudent' => array(
			'className' => 'AcceptedStudent',
			'foreignKey' => 'user_id',
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
		'UserDormAssignment' => array(
			'className' => 'UserDormAssignment',
			'foreignKey' => 'user_id',
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
		'UserMealAssignment' => array(
			'className' => 'UserMealAssignment',
			'foreignKey' => 'user_id',
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
			'foreignKey' => 'user_id',
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
		'StaffAssigne' => array(
			'className' => 'StaffAssigne',
			'foreignKey' => 'user_id',
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
		'MoodleUser' => array(
			'className' => 'MoodleUser',
			'foreignKey' => 'user_id',
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

	function verifyLogin($data)
	{
		/* $user = $this->find('first', array(
		    'conditions' => array(
			    'User.username' => $data['User']['username'],
			    'User.password' => md5($data['User']['password'])
		    )
	    ));  */

		$user = $this->find('first', array(
			'conditions' => array(
				'User.username' => $data['User']['username'],
				'User.password' =>  Security::hash($data['User']['password'])
			)
		));

		if (!empty($user)) {
			return $user;
		} else {
			$this->invalidate('', 'Incorrect username or password. Please try again.');
		}
		return false;
	}
	
	function veryifyOldPassword($data = null)
	{
		$user = $this->find('first', array('conditions' => array('User.id' => $data['User']['id'])));

		if (strcmp($user['User']['password'], $data['User']['oldpassword']) == 0) {
			return $user;
		} else {
			$this->invalidate('invaliduser', 'Incorrect current password, please try again.');
			return false;
		}
	}

	function checkNumberOfUserAccount($data = null)
	{
		// clearnce role allow them to add as they want 
		if ($data['User']['role_id'] == ROLE_CLEARANCE) {
			return true;
		} else if ($data['User']['role_id'] == ROLE_GENERAL) {
			return true;
		} else {
			// check if admin main account has already created for selected department ?
			if (ROLE_DEPARTMENT == $data['User']['role_id']) {
				$isMainAccountCreated = $this->find('first', array(
					'conditions' => array(
						'User.is_admin' => 1, 
						'User.role_id' => $data['User']['role_id']
					), 
					'contain' => array(
						'Role', 
						'Staff' => array('conditions' => array('Staff.department_id' => $data['Staff'][0]['department_id']))
					)
				));
			} elseif (ROLE_COLLEGE == $data['User']['role_id']) {
				$isMainAccountCreated = $this->find('first', array(
					'conditions' => array(
						'User.is_admin' => 1,
						'User.role_id' => $data['User']['role_id']
					), 
					'contain' => array(
						'Role', 
						'Staff' => array(
							'conditions' => array('Staff.college_id' => $data['Staff'][0]['college_id'])
						)
					)
				));
			} else {
				$isMainAccountCreated = $this->find('first', array(
					'conditions' => array(
						'User.is_admin' => 1, 
						'User.role_id' => $data['User']['role_id'], 
						'User.active' => 1
					), 
					'contain' => array('Staff', 'Role')
				));
			}

			if (empty($isMainAccountCreated)) {
				$isMainAccountCreated = $this->find('all', array(
					'conditions' => array(
						'User.is_admin' => 0,
						'User.role_id' => ROLE_SYSADMIN, 
						'User.active' => 1
					), 
					'limit' => 3, 
					'contain' => array('Staff', 'Role')
				));
			}

			if (!empty($isMainAccountCreated) && $data['User']['role_id'] == ROLE_SYSADMIN && count($isMainAccountCreated) > 2 ) {

				$message = 'The selected role has already system administrator. You can not create more than 2 administrator for ' . $isMainAccountCreated[0]['Role']['name'] . ' role. 
				If the system administartor in the list are nomore system administrator, please deactive their account and create the new one. The system administrator in the system are :-
                <ul>
                    <li>' . $isMainAccountCreated[0]['User']['full_name'] . '</li>
					<li>' . $isMainAccountCreated[1]['User']['full_name'] . '</li>
					<li>' . $isMainAccountCreated[2]['User']['full_name'] . '</li>
				</ul>';

				$this->invalidate('college_department', $message);
				return false;
			} else if ((!empty($isMainAccountCreated) && !empty($isMainAccountCreated['Staff'])) && ($data['User']['role_id'] == ROLE_REGISTRAR || $data['User']['role_id'] == ROLE_COLLEGE || $data['User']['role_id'] == ROLE_DEPARTMENT || $data['User']['role_id'] == ROLE_MEAL || $data['User']['role_id'] == ROLE_HEALTH || $data['User']['role_id'] == ROLE_ACCOMODATION || $data['User']['role_id'] == ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM)) {
				
				$message = 'The selected role has already system administrator. You can not create more than one administrator for ' . $isMainAccountCreated['Role']['name'] . ' role. 
				If the system administartor in the selected role is nomore system administrator, please deactivate their account and create the new one.
                <ul>
                    <li>' . $isMainAccountCreated['User']['full_name'] . '</li>
				</ul>';

				$this->invalidate('college_department', $message);
				return false;
			}
		}
		return true;
	}

	function checkUnique($data, $fieldName)
	{
		$valid = false;
		if (isset($fieldName) && $this->hasField($fieldName)) {
			$valid = $this->isUnique(array($fieldName => $data));
		}
		return $valid;
	}

	function parentNode()
	{

		if (!$this->id && empty($this->data)) {
			return null;
		}

		if (isset($this->data['User']['role_id'])) {
			$roleId = $this->data['User']['role_id'];
		} else {
			$roleId = $this->field('role_id');
		}

		if (!$roleId) {
			return null;
		}
		return array('Role' => array('id' => $roleId));


		/* $data = $this->data;

		if (empty ($this->data)) {
			$data = $this->read();
		} else {
			if (!isset ($data['User']['role_id'])) {
				$test = $this->read(null, $data['User']['id']);
				$data['User']['role_id'] = $test['User']['role_id'];
			}
		}

		if (!$data['User']['role_id']) {
			return null;
		} else {
			return array('Role' => array('id' => $data['User']['role_id']));
		} */
	   
	}
	
	public function getUserDetails($user)
	{

		if (isset($user) && !empty($user)) {

			$userRole = $this->find('first', array(
				'conditions' => array(
					'User.id' => $user
				), 
				'fields' => array(
					'User.id', 
					'User.role_id',
					'User.username',
					'User.is_admin',
					'User.email_verified',
				),
				'recursive' => -1
			));

			$userdetails = array();
		
			if (!empty($userRole)) {

				if ($userRole['User']['role_id'] == ROLE_STUDENT) {
					
					$userdetails = $this->find('first', array(
						'conditions' => array('User.id' => $user),
						'fields' => array('User.id', 'User.role_id', 'User.username', 'User.first_name', 'User.email', 'User.is_admin', 'User.email_verified'),
						'contain' => array(
							'Role' => array(
								'fields' => array(
									'Role.id',
									'Role.name'
								)
							),
							'Student' => array(
								'conditions' => array('Student.user_id' => $user),
								'College' => array(
									'fields' => array(
										'College.id',
										'College.name',
										'College.campus_id',
										'College.stream'
									)
								),
								'Department' => array(
									'fields' => array(
										'Department.id',
										'Department.name'
									)
								),
								'Program' => array(
									'fields' => array('Program.id', 'Program.name')
								),
								'ProgramType' => array(
									'fields' => array('ProgramType.id', 'ProgramType.name')
								)
							)
						)
					));

					$userdetails['ApplicableAssignments']['college_ids'] = array();
					$userdetails['ApplicableAssignments']['department_ids'] = array();
					$userdetails['ApplicableAssignments']['college_permission'] = 0;
					$userdetails['ApplicableAssignments']['year_level_names'] = array();
					$userdetails['ApplicableAssignments']['last_section'] = array();

					if (!empty($userdetails['Student'])) {

						$collID = $userdetails['Student'][0]['College']['id'];
						$deptID = null;

						if (isset($userdetails['Student'][0]['Department']['id'])) {
							$deptID = $userdetails['Student'][0]['Department']['id'];
						} else {
							$userdetails['ApplicableAssignments']['college_permission'] = 1;
						}

						$progID = $userdetails['Student'][0]['Program']['id'];
						$progTypeID = $userdetails['Student'][0]['ProgramType']['id'];
						$studentDBID = $userdetails['Student'][0]['id'];

						if (isset($collID) && is_numeric($collID) && $collID ) {
							$userdetails['ApplicableAssignments']['college_ids'] = [$collID => $collID];
						} 

						if (isset($deptID) && is_numeric($deptID) && $deptID) {
							$userdetails['ApplicableAssignments']['department_ids'] = [$deptID => $deptID];
						}

						if (isset($progID) && is_numeric($progID) && $progID) {
							$userdetails['ApplicableAssignments']['program_ids'] = [$progID => $progID];
						}

						if (isset($progTypeID) && is_numeric($progTypeID) && $progTypeID) {
							$userdetails['ApplicableAssignments']['program_type_ids']  = $this->getEquivalentProgramTypes($progTypeID);
						}

						$studentSection = ClassRegistry::init('Student')->get_student_section($studentDBID);
						//debug($studentSection);

						if (!empty($studentSection['Section'])) {
							if (!empty($studentSection['Section']['YearLevel']['name'])) {
								$yearLevelName = $studentSection['Section']['YearLevel']['name'];
								$userdetails['ApplicableAssignments']['year_level_names'] = [$yearLevelName => $yearLevelName];
							} else {
								$userdetails['ApplicableAssignments']['year_level_names'] = [0 => 'Pre'];
							}
							$userdetails['ApplicableAssignments']['last_section'] = $studentSection['Section'];
						} else {
							if (isset($userdetails['Student'][0]['Department']['id'])) {
								$userdetails['ApplicableAssignments']['year_level_names'] = ['1st' => '1st'];
							} else {
								$userdetails['ApplicableAssignments']['year_level_names'] = [0 => 'Pre'];
							}
						}

						debug($userdetails);
						return $userdetails;
					}

				} else if ($userRole['User']['role_id'] != ROLE_STUDENT) {
					//debug($user);

					$userdetails = $this->find('first', array(
						'conditions' => array('User.id' => $user),
						'fields' => array('User.id', 'User.role_id', 'User.username', 'User.first_name', 'User.email', 'User.is_admin', 'User.email_verified'),
						'contain' => array(
							'Role' => array(
								'fields'  => array(
									'Role.id',
									'Role.name'
								)
							),
							'Staff' => array(
								'College' => array(
									'fields' => array(
										'College.id',
										'College.name'
									)
								),
								'Department' => array(
									'fields' => array(
										'Department.id',
										'Department.name'
									)
								)
							),
							'StaffAssigne',
						)
					));

					$userdetails['ApplicableAssignments']['college_ids'] = array();
					$userdetails['ApplicableAssignments']['department_ids'] = array();
					$userdetails['ApplicableAssignments']['college_permission'] = 0;
					//$userdetails['ApplicableAssignments']['year_level_names'] = ['1st' => '1st'];
					$userdetails['ApplicableAssignments']['year_level_names'] = array();

					if (!empty($userdetails['Staff'])) {

						$activePrograms  = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1), 'fields' => array('Program.id', 'Program.id')));
						$activeProgramTypes  = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1), 'fields' => array('ProgramType.id', 'ProgramType.id')));

						if ($userRole['User']['role_id'] == ROLE_REGISTRAR || $userRole['User']['role_id'] == ROLE_MEAL || $userRole['User']['role_id'] == ROLE_ACCOMODATION) {

							if ($userRole['User']['is_admin'] == 1 && ($userRole['User']['role_id'] == ROLE_MEAL || $userRole['User']['role_id'] == ROLE_ACCOMODATION)) {

								$userdetails['ApplicableAssignments']['program_ids'] = $activePrograms;
								$userdetails['ApplicableAssignments']['program_type_ids'] = $activeProgramTypes;

								$activeColleges = ClassRegistry::init('College')->find('list', array(
									'conditions' => array(
										'College.active' => 1
									),
									'fields' => array('College.id', 'College.id')
								));

								$activeDepartments = ClassRegistry::init('Department')->find('list', array(
									'conditions' => array(
										'Department.active' => 1
									),
									'fields' => array('Department.id', 'Department.id'),
									'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
								));

								$userdetails['ApplicableAssignments']['college_ids'] = $activeColleges;
								$userdetails['ApplicableAssignments']['department_ids'] = $activeDepartments;

								$availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
									'fields' => array('Curriculum.program_id', 'Curriculum.program_id'),
									'conditions' => array(
										'Curriculum.department_id' => $activeDepartments,
										'Curriculum.program_id' => $activePrograms,
										'Curriculum.active' => 1
									),
									'group' => 'Curriculum.program_id'
								));

								//debug($availableProgramsInCurriculums);

								$filteredPrograms = array();

								if (!empty($availableProgramsInCurriculums)) {
									$filteredPrograms = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1, 'Program.id' => $availableProgramsInCurriculums), 'fields' => array('Program.id', 'Program.id')));
									$filteredPrograms[PROGRAM_REMEDIAL] = ''.PROGRAM_REMEDIAL.'';
								}

								if (!empty($filteredPrograms)) {
									$userdetails['ApplicableAssignments']['program_ids'] = $filteredPrograms;
								}

								$year_names_from_active_departments = array();

								if (!empty($userdetails['ApplicableAssignments']['program_ids']) && !empty($userdetails['ApplicableAssignments']['department_ids'])) {
									$year_names_from_active_departments = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($userRole['User']['role_id'], null, $userdetails['ApplicableAssignments']['department_ids'], $userdetails['ApplicableAssignments']['program_ids']);
								}

								if (!empty($year_names_from_active_departments)) {
									// $userdetails['ApplicableAssignments']['year_level_names'] = $year_names_from_active_departments;
									// $userdetails['ApplicableAssignments']['year_level_names'][0] = 'Pre';

									$pre = [0 => 'Pre'];
									$userdetails['ApplicableAssignments']['year_level_names'] = $pre + $year_names_from_active_departments;
								}

							} else if (!empty($userdetails['StaffAssigne'])) {

								$userdetails['ApplicableAssignments']['college_permission'] = $userdetails['StaffAssigne']['collegepermission'];
								$userdetails['ApplicableAssignments']['program_ids'] = unserialize($userdetails['StaffAssigne']['program_id']);
								$userdetails['ApplicableAssignments']['program_type_ids'] = unserialize($userdetails['StaffAssigne']['program_type_id']);

								if ($userdetails['ApplicableAssignments']['college_permission'] == 1) {
									$userdetails['ApplicableAssignments']['college_ids'] = unserialize($userdetails['StaffAssigne']['college_id']);
									$userdetails['ApplicableAssignments']['program_ids'] = Configure::read('programs_available_for_registrar_college_level_permissions');
									$userdetails['ApplicableAssignments']['program_type_ids'] = Configure::read('program_types_available_for_registrar_college_level_permissions');
									$userdetails['ApplicableAssignments']['year_level_names'] = [0 => 'Pre'];
								} else {

									$userdetails['ApplicableAssignments']['department_ids'] = unserialize($userdetails['StaffAssigne']['department_id']);
									$userdetails['ApplicableAssignments']['college_ids'] = unserialize($userdetails['StaffAssigne']['college_id']);

									if (!is_array($userdetails['ApplicableAssignments']['college_ids'])) {
										$userdetails['ApplicableAssignments']['college_ids'] = array();
									}

									if (!is_array($userdetails['ApplicableAssignments']['department_ids'])) {
										$userdetails['ApplicableAssignments']['department_ids'] = array();
									}

									if (!empty($userdetails['ApplicableAssignments']['program_ids'])) {

										if (!empty($userdetails['ApplicableAssignments']['department_ids'])) {

											$activeDepartments = ClassRegistry::init('Department')->find('list', array(
												'conditions' => array(
													'Department.id' => $userdetails['ApplicableAssignments']['department_ids'],
													'Department.active' => 1
												),
												'fields' => array('Department.id', 'Department.id'),
												'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
											));

											if (!empty($activeDepartments)) {
												$userdetails['ApplicableAssignments']['department_ids'] = $activeDepartments;
											}
											
											$availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
												'fields' => array('Curriculum.program_id', 'Curriculum.program_id'),
												'conditions' => array(
													'Curriculum.department_id' => $userdetails['ApplicableAssignments']['department_ids'],
													'Curriculum.program_id' => $userdetails['ApplicableAssignments']['program_ids'],
													'Curriculum.active' => 1
												),
												'group' => 'Curriculum.program_id'
											));

											//debug($availableProgramsInCurriculums);

											$filteredPrograms = array();
									
											if (!empty($availableProgramsInCurriculums)) {
												$filteredPrograms = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1, 'Program.id' => $availableProgramsInCurriculums), 'fields' => array('Program.id', 'Program.id')));
												if ($userRole['User']['is_admin'] == 1 && $userRole['User']['role_id'] == ROLE_REGISTRAR) {
													$filteredPrograms[PROGRAM_REMEDIAL] = ''.PROGRAM_REMEDIAL.'';
												}
											}

											if (!empty($filteredPrograms)) {
												$userdetails['ApplicableAssignments']['program_ids'] = $filteredPrograms;
											}

											$year_names_from_active_departments = array();

											if (!empty($userdetails['ApplicableAssignments']['program_ids']) && !empty($userdetails['ApplicableAssignments']['department_ids'])) {
												$year_names_from_active_departments = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($userRole['User']['role_id'], null, $userdetails['ApplicableAssignments']['department_ids'], $userdetails['ApplicableAssignments']['program_ids']);
											}

											$filteredProgramTypes  = array();

											if (!empty($userdetails['ApplicableAssignments']['program_type_ids'])) {
												$filteredProgramTypes  = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1, 'ProgramType.id' => $userdetails['ApplicableAssignments']['program_type_ids']), 'fields' => array('ProgramType.id', 'ProgramType.id')));
											}

											if (!empty($filteredProgramTypes)) {
												$userdetails['ApplicableAssignments']['program_type_ids'] = $filteredProgramTypes;
											}

											if (!empty($year_names_from_active_departments)) {
												$userdetails['ApplicableAssignments']['year_level_names'] = $year_names_from_active_departments;
											}

										} else if (!empty($userdetails['ApplicableAssignments']['college_ids'])) {
											
											$activeColleges = ClassRegistry::init('College')->find('list', array(
												'conditions' => array(
													'College.id' => $userdetails['ApplicableAssignments']['college_ids'],
													'College.active' => 1
												),
												'fields' => array('College.id', 'College.id')
											));

											if (!empty($activeColleges)) {
												
												$userdetails['ApplicableAssignments']['college_ids'] = $activeColleges;

												if (!empty($userdetails['ApplicableAssignments']['department_ids'])) {

													$activeDepartments = ClassRegistry::init('Department')->find('list', array(
														'conditions' => array(
															'Department.college_id' => $activeColleges,
															'Department.active' => 1
														),
														'fields' => array('Department.id', 'Department.id'),
														'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
													));
			
													if (!empty($activeDepartments)) {

														$userdetails['ApplicableAssignments']['department_ids'] = $activeDepartments;

														$availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
															'fields' => array('Curriculum.program_id', 'Curriculum.program_id'),
															'conditions' => array(
																'Curriculum.department_id' => $userdetails['ApplicableAssignments']['department_ids'],
																'Curriculum.program_id' => $userdetails['ApplicableAssignments']['program_ids'],
																'Curriculum.active' => 1
															),
															'group' => 'Curriculum.program_id'
														));
				
														//debug($availableProgramsInCurriculums);
				
														$filteredPrograms = array();
												
														if (!empty($availableProgramsInCurriculums)) {
															$filteredPrograms = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1, 'Program.id' => $availableProgramsInCurriculums), 'fields' => array('Program.id', 'Program.id')));
														}
				
														if (!empty($filteredPrograms)) {
															$userdetails['ApplicableAssignments']['program_ids'] = $filteredPrograms;
														}
				
														$year_names_from_active_departments = array();
				
														if (!empty($userdetails['ApplicableAssignments']['program_ids']) && !empty($userdetails['ApplicableAssignments']['department_ids'])) {
															$year_names_from_active_departments = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($userRole['User']['role_id'], null, $userdetails['ApplicableAssignments']['department_ids'], $userdetails['ApplicableAssignments']['program_ids']);
														}
				
														$filteredProgramTypes  = array();
				
														if (!empty($userdetails['ApplicableAssignments']['program_type_ids'])) {
															$filteredProgramTypes  = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.active' => 1, 'ProgramType.id' => $userdetails['ApplicableAssignments']['program_type_ids']), 'fields' => array('ProgramType.id', 'ProgramType.id')));
														}
				
														if (!empty($filteredProgramTypes)) {
															$userdetails['ApplicableAssignments']['program_type_ids'] = $filteredProgramTypes;
														}
				

														if (!empty($year_names_from_active_departments) && $userdetails['ApplicableAssignments']['college_permission'] != 1) {
															$userdetails['ApplicableAssignments']['year_level_names'] = $year_names_from_active_departments;
														}
													}
			
												}
											}

											if (isset($userdetails['ApplicableAssignments']['year_level_names']) && !empty($userdetails['ApplicableAssignments']['year_level_names'])) {
												//$userdetails['ApplicableAssignments']['year_level_names'][0] = 'Pre';
												$pre = [0 => 'Pre'];
												$userdetails['ApplicableAssignments']['year_level_names'] = $pre + $userdetails['ApplicableAssignments']['year_level_names'];
											} else {
												$userdetails['ApplicableAssignments']['year_level_names'] = [0 => 'Pre'];
											}
											
										}
									}
								}
								
							}

						} else if ($userRole['User']['role_id'] == ROLE_COLLEGE) {

							$collID = $userdetails['Staff'][0]['College']['id'];
							//debug($collID);

							$only_stream_based_colleges_pre_social_natural = Configure::read('only_stream_based_colleges_pre_social_natural');
							$programs_available_for_registrar_college_level_permissions = Configure::read('programs_available_for_registrar_college_level_permissions');

							$userdetails['ApplicableAssignments']['college_ids'] = [$collID => $collID];

							if ($userRole['User']['is_admin'] == 0) {

								$userdetails['ApplicableAssignments']['program_ids'] = $programs_available_for_registrar_college_level_permissions;
								$userdetails['ApplicableAssignments']['program_type_ids'] = Configure::read('program_types_available_for_registrar_college_level_permissions');
								$userdetails['ApplicableAssignments']['department_ids'] = array();
								$userdetails['ApplicableAssignments']['college_permission'] = 1;  // for only pre/freshman management only
								$userdetails['ApplicableAssignments']['year_level_names'][0] = 'Pre';

							} else {

								$userdetails['ApplicableAssignments']['program_ids'] = $activePrograms;
								$userdetails['ApplicableAssignments']['program_type_ids'] = $activeProgramTypes;

								$activeDepartments = ClassRegistry::init('Department')->find('list', array(
									'conditions' => array(
										'Department.college_id' => $collID,
										'Department.active' => 1
									),
									'fields' => array('Department.id', 'Department.id'),
									'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
								));

								if (!empty($activeDepartments)) {

									$userdetails['ApplicableAssignments']['department_ids'] = $activeDepartments;
									
									$availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
										'fields' => array('Curriculum.program_id', 'Curriculum.program_id'),
										'conditions' => array(
											'Curriculum.department_id' => $activeDepartments,
											'Curriculum.program_id' => $userdetails['ApplicableAssignments']['program_ids'],
											'Curriculum.active' => 1
										),
										'group' => 'Curriculum.program_id'
									));

									//debug($availableProgramsInCurriculums);

									$filteredPrograms = array();
							
									if (!empty($availableProgramsInCurriculums)) {
										$filteredPrograms = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1, 'Program.id' => $availableProgramsInCurriculums), 'fields' => array('Program.id', 'Program.id')));
									}

									if (!empty($filteredPrograms)) {
										$userdetails['ApplicableAssignments']['program_ids'] = $filteredPrograms;
									}

									$year_names_from_active_departments = array();

									if (!empty($userdetails['ApplicableAssignments']['program_ids']) && !empty($userdetails['ApplicableAssignments']['college_ids'])) {
										$year_names_from_active_departments = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($userRole['User']['role_id'], $userdetails['ApplicableAssignments']['college_ids'], null, $userdetails['ApplicableAssignments']['program_ids']);
									}

									if (!empty($year_names_from_active_departments)) {
										// $userdetails['ApplicableAssignments']['year_level_names'] = $year_names_from_active_departments;
										// $userdetails['ApplicableAssignments']['year_level_names'][0] = 'Pre';
										$pre = [0 => 'Pre'];
										$userdetails['ApplicableAssignments']['year_level_names'] = $pre + $year_names_from_active_departments;
									}
								}
							}

							if (!empty($only_stream_based_colleges_pre_social_natural) && is_array($only_stream_based_colleges_pre_social_natural) && in_array($collID,  $only_stream_based_colleges_pre_social_natural)) {
								if (!empty($userdetails['ApplicableAssignments']['program_ids']) && is_array($programs_available_for_registrar_college_level_permissions)) {
									if (in_array(PROGRAM_UNDEGRADUATE,$programs_available_for_registrar_college_level_permissions) && !in_array(PROGRAM_UNDEGRADUATE,$userdetails['ApplicableAssignments']['program_ids'])) {
										$userdetails['ApplicableAssignments']['program_ids'][PROGRAM_UNDEGRADUATE] = ''. PROGRAM_UNDEGRADUATE .'';
									}
									if (in_array(PROGRAM_REMEDIAL,$programs_available_for_registrar_college_level_permissions) && !in_array(PROGRAM_REMEDIAL,$userdetails['ApplicableAssignments']['program_ids'])) {
										$userdetails['ApplicableAssignments']['program_ids'][PROGRAM_REMEDIAL] = ''. PROGRAM_REMEDIAL .'';
									}
								} else if (is_array($programs_available_for_registrar_college_level_permissions)) {
									if (in_array(PROGRAM_UNDEGRADUATE,$programs_available_for_registrar_college_level_permissions)) {
										$userdetails['ApplicableAssignments']['program_ids'][PROGRAM_UNDEGRADUATE] = ''. PROGRAM_UNDEGRADUATE .'';
									}
									if (in_array(PROGRAM_REMEDIAL,$programs_available_for_registrar_college_level_permissions)) {
										$userdetails['ApplicableAssignments']['program_ids'][PROGRAM_REMEDIAL] = ''. PROGRAM_REMEDIAL .'';
									}
								}

								ksort($userdetails['ApplicableAssignments']['program_ids']);
							}

						} else if ($userRole['User']['role_id'] == ROLE_DEPARTMENT) {

							$collID = $userdetails['Staff'][0]['College']['id'];
							$deptID = $userdetails['Staff'][0]['Department']['id'];
							//debug($collID);

							//$userdetails['ApplicableAssignments']['college_ids'] = [$collID => $collID];
							$userdetails['ApplicableAssignments']['college_ids'] = array();
							$userdetails['ApplicableAssignments']['department_ids'] = [$deptID => $deptID];

							$userdetails['ApplicableAssignments']['program_ids'] = $activePrograms;
							$userdetails['ApplicableAssignments']['program_type_ids'] = $activeProgramTypes;

							$availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
								'fields' => array('Curriculum.program_id', 'Curriculum.program_id'),
								'conditions' => array(
									'Curriculum.department_id' => $deptID,
									'Curriculum.program_id' => $userdetails['ApplicableAssignments']['program_ids'],
									'Curriculum.active' => 1
								),
								'group' => 'Curriculum.program_id'
							));

							//debug($availableProgramsInCurriculums);

							$filteredPrograms = array();

							if (!empty($availableProgramsInCurriculums)) {
								$filteredPrograms = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1, 'Program.id' => $availableProgramsInCurriculums), 'fields' => array('Program.id', 'Program.id')));
							}

							if (!empty($filteredPrograms)) {
								$userdetails['ApplicableAssignments']['program_ids'] = $filteredPrograms;
							}

							$year_names_from_active_departments = array();

							if (!empty($userdetails['ApplicableAssignments']['program_ids']) && !empty($userdetails['ApplicableAssignments']['department_ids'])) {
								$year_names_from_active_departments = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($userRole['User']['role_id'], null, $userdetails['ApplicableAssignments']['department_ids'], $userdetails['ApplicableAssignments']['program_ids']);
							}

							if (!empty($year_names_from_active_departments)) {
								$userdetails['ApplicableAssignments']['year_level_names'] = $year_names_from_active_departments;
							}

						} else {

							$userdetails['ApplicableAssignments']['program_ids'] = $activePrograms;
							$userdetails['ApplicableAssignments']['program_type_ids'] = $activeProgramTypes;

							$activeColleges = ClassRegistry::init('College')->find('list', array(
								'conditions' => array(
									'College.active' => 1
								),
								'fields' => array('College.id', 'College.id')
							));

							$activeDepartments = ClassRegistry::init('Department')->find('list', array(
								'conditions' => array(
									'Department.active' => 1
								),
								'fields' => array('Department.id', 'Department.id'),
								'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
							));

							$userdetails['ApplicableAssignments']['college_ids'] = $activeColleges;
							$userdetails['ApplicableAssignments']['department_ids'] = $activeDepartments;

							$availableProgramsInCurriculums = ClassRegistry::init('Curriculum')->find('list', array(
								'fields' => array('Curriculum.program_id', 'Curriculum.program_id'),
								'conditions' => array(
									'Curriculum.department_id' => $activeDepartments,
									'Curriculum.program_id' => $activePrograms,
									'Curriculum.active' => 1
								),
								'group' => 'Curriculum.program_id'
							));

							//debug($availableProgramsInCurriculums);

							$filteredPrograms = array();

							if (!empty($availableProgramsInCurriculums)) {
								$filteredPrograms = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1, 'Program.id' => $availableProgramsInCurriculums), 'fields' => array('Program.id', 'Program.id')));
							}

							if (!empty($filteredPrograms)) {
								$userdetails['ApplicableAssignments']['program_ids'] = $filteredPrograms;
							}

							$year_names_from_active_departments = array();

							if (!empty($userdetails['ApplicableAssignments']['program_ids']) && !empty($userdetails['ApplicableAssignments']['department_ids'])) {
								$year_names_from_active_departments = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($userRole['User']['role_id'], null, $userdetails['ApplicableAssignments']['department_ids'], $userdetails['ApplicableAssignments']['program_ids']);
							}

							if (!empty($year_names_from_active_departments)) {
								// $userdetails['ApplicableAssignments']['year_level_names'] = $year_names_from_active_departments;
								// $userdetails['ApplicableAssignments']['year_level_names'][0] = 'Pre';
								$pre = [0 => 'Pre'];
								$userdetails['ApplicableAssignments']['year_level_names'] = $pre + $year_names_from_active_departments;
							}
						}
					}

					debug($userdetails);
					return $userdetails;
				}
			}
		} 

		return array();
	}

	function lastLogin($id)
	{
		if (!empty($id)) {
			$this->id = $id;
			$this->saveField('last_login', date('Y-m-d H:i:s'));
		}
	}

	public function beforeSave($options = array())
	{
		if (isset($this->data['User']['passwd'])) {
			$this->data['User']['notEncryptedPass'] = $this->data['User']['passwd'];
			$this->data['User']['password'] = Security::hash($this->data['User']['passwd'], null, true);
			unset($this->data['User']['passwd']);
		} else {
			if (isset($this->data['User']['password'])) {
				$this->data['User']['notEncryptedPass'] = $this->data['User']['password'];
			}
		}

		return true;
	}


	function elgibleToEdit($user_id = null, $deparment_college_ids = null)
	{
		$responsibility = $this->User->StaffAssigne->find('first', array('conditions' => array('StaffAssigne.user_id' => $user_id), 'recursive' => -1));
	}

	function getEmailsForRoles($role_ids = null)
	{
		$returnArray = array();

		if (!empty($role_ids) && is_array($role_ids)) {
			foreach ($role_ids as $role_id) {
				$tempArray = $this->getListOfUsers($role_id);
				if (!empty($tempArray) && is_array($tempArray)) {
					$returnArray = $returnArray + $tempArray;
				}
			}
			return $returnArray;
		} else {
			return null;
		}
	}

	function getListOfUsers($role_id = null, $message = null)
	{
		$this->recursive = 0;
		$returnArray = array();

		if (!$role_id) {
			return NULL;
		} else {
			$conditions = array('User.role_id' => $role_id, 'User.active' => 1);
		}

		$users = $this->find('all', array(
			'fields' => array(
				'id', 'User.username',
				'User.email'
			),
			'conditions' => $conditions,
			'group' => array('User.username')
		));

		if (!empty($users)) {
			foreach ($users as $user) {
				$id = $user['User']['id'];
				$email = $user['User']['email'];
				if (!empty($email)) {
					$returnArray[$id] = $email;
				}
			}
		}

		return $returnArray;
	}

	function getListOfUsersRole($role_id = null, $message = null, $data = null ) 
	{
		$this->recursive = 0;
		$returnArray = array();
		$options = array();

		if (!$role_id) {
			return NULL;
		}

		$options['conditions']['User.role_id'] = $role_id;
		$options['conditions']['User.active'] = 1;
		
		if ($role_id == ROLE_STUDENT) {

			$options['contain'] = array('User');
			
			$options['conditions'][] = array(
				//'Student.id NOT IN (SELECT graduate_lists.student_id from graduate_lists)',
				'Student.graduated = 0',
				'Student.id IN (SELECT student_id from students_sections where archive = 0)',
			);

			if (!empty($data['department_ids'])) {
				$options['conditions']['Student.department_id'] = $data['department_ids'];
			}

			if (!empty($data['college_ids'])) {
				$options['conditions']['Student.college_id'] = $data['college_ids'];
			}

			if (!empty($data['program_id'])) {
				$options['conditions']['Student.program_id'] = $data['program_id'];
			}

			if (!empty($data['program_type_id'])) {
				$options['conditions']['Student.program_type_id'] = $data['program_type_id'];
			}

			$users = $this->Student->find('all', $options);

		} else {
			$options['contain'] = array('User');
			$options['conditions']['Staff.active'] = 1;

			if (!empty($data['department_ids'])) {
				$options['conditions']['Staff.department_id'] = $data['department_ids'];
			}

			if (!empty($data['college_ids'])) {
				$options['conditions']['Staff.college_id'] = $data['college_ids'];
			}

			$users = $this->Staff->find('all', $options);
		}

		$count = 0;

		if (!empty($users)) {
			foreach ($users as $user) {
				$id = $user['User']['id'];
				$returnArray[$count]['user_id'] = $id;
				$returnArray[$count]['read'] = 0;
				$returnArray[$count]['message'] = $message;
				$count++;
			}
		}

		return $returnArray;
	}

	
	function getNameOfTheUser($user_id = null)
	{
		$this->recursive = 0;
		$order = 'User.id DESC';

		if (!$user_id) {
			return false;
		} else {

			$role = $this->field('role_id', array('User.id' => $user_id));

			if ($role != ROLE_STUDENT) {
				$users = $this->Staff->find('first', array(
					'conditions' => array(
						'Staff.user_id' => $user_id
					),
					'fields' => array(
						'first_name',
						'last_name'
					),
					'contain' => array('Title' => array('id', 'title')),
					'order' => 'Staff.user_id'
				));
			} else {
				$users = $this->Student->find('first', array(
					'conditions' => array(
						'Student.user_id' => $user_id
					),
					'fields' => array(
						'first_name',
						'last_name'
					),
					'order' => 'Student.user_id'
				));
			}
		}

		return $users;
	}


	function validate_department_college($data = null)
	{
		if (empty($data['User']['role_id'])) {
			return true;
		}

		if ($data['User']['role_id'] == ROLE_DEPARTMENT && empty($data['Staff'][0]['department_id'])) {
			$this->invalidate('college_department', 'User with department role must have department. Please select department for the user.');
			return false;
		} else if ($data['User']['role_id'] == ROLE_COLLEGE && empty($data['Staff'][0]['college_id'])) {
			$this->invalidate('college_department', 'User with college role must have college. Please select college for the user.');
			return false;
		}

		return true;
	}

	function checkUserIsBelongsInYourAdmin($user_id = null, $role_id = null, $department_id = null, $college_id = null)
	{
		// check if admin account has already privilage to reset ?

		if ($role_id == ROLE_SYSADMIN) {
			$user_role_ids = $this->Role->find('list', array('conditions' => array('Role.parent_id' => $role_id), 'fields' => array('Role.id', 'Role.id')));

			$is_account_belongs_to_admin = $this->find('count', array(
				'conditions' => array(
					'User.id' => $user_id,
					'User.role_id' => $user_role_ids, 
					'User.is_admin' => 1
				)
			));

			if ($is_account_belongs_to_admin == 0) {
				return false;
			} else {
				return true;
			}
		}


		if ($role_id == ROLE_REGISTRAR) {

			$user_role_ids = $this->Role->find('list', array(
				'conditions' => array('Role.parent_id' => $role_id),
				'fields' => array('Role.id', 'Role.id')
			));

			$user_role_ids[$role_id] = $role_id;

			$is_account_belongs_to_admin = $this->find('count', array(
				'conditions' => array(
					'User.id' => $user_id,
					'User.role_id' => $user_role_ids
				)
			));

			if ($is_account_belongs_to_admin == 0) {
				return false;
			} else {
				return true;
			}
		}

		if ($role_id == ROLE_MEAL) {
			$is_account_belongs_to_admin = $this->find('count', array('conditions' => array('User.id' => $user_id, 'User.role_id' => $role_id)));
			if ($is_account_belongs_to_admin == 0) {
				return false;
			} else {
				return true;
			}
		}

		if ($role_id == ROLE_ACCOMODATION) {
			$is_account_belongs_to_admin = $this->find('count', array('conditions' => array('User.id' => $user_id, 'User.role_id' => $role_id)));
			if ($is_account_belongs_to_admin == 0) {
				return false;
			} else {
				return true;
			}
		}

		if ($role_id == ROLE_HEALTH) {
			$is_account_belongs_to_admin = $this->find('count', array('conditions' => array('User.id' => $user_id, 'User.role_id' => $role_id)));
			if ($is_account_belongs_to_admin == 0) {
				return false;
			} else {
				return true;
			}
		}

		if ($role_id == ROLE_DEPARTMENT) {
			$staff_belongs_to_you = $this->Staff->find('count', array('conditions' => array('Staff.user_id' => $user_id, 'Staff.department_id' => $department_id)));
			if ($staff_belongs_to_you == 0) {
				return false;
			} else {
				return true;
			}
		}

		if ($role_id == ROLE_COLLEGE) {
			$staff_belongs_to_you = $this->Staff->find('count', array('conditions' => array('Staff.user_id' => $user_id, 'Staff.college_id' => $college_id)));
			if ($staff_belongs_to_you == 0) {
				return false;
			} else {
				return true;
			}
		}
		return false;
	}

	function doesItFullfillPasswordStrength($password = null, $securitysetting = null)
	{
		// ereg function was DEPRECATED in PHP 5.3.0, and REMOVED in PHP 7.0.0. using preg_match()
		if (!empty($securitysetting)) {
			if ($securitysetting['password_strength'] == 1) { //Medium
				if (!preg_match('/[a-z]/', $password)) return false;
				if (!preg_match('/[A-Z]/', $password)) return false;
				if (!preg_match('/[0-9]/', $password)) return false;
			} else { //Strong
				if (!preg_match('/[a-z]/', $password)) return false;
				if (!preg_match('/[A-Z]/', $password)) return false;
				if (!preg_match('/[0-9]/', $password)) return false;
				if (!preg_match('/[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/', $password)) return false;
			}
		} else {
			return false;
		}
		return true;
	}


	function getAllPermissions($userId)
	{
		$node = array();
		$arosNodeNotFound = false;
		$sqlAro1 = "SELECT id,parent_id,model,foreign_key,alias FROM aros as Aro WHERE Aro.foreign_key='" . $userId . "' limit 1 ";
		$aro1 = $this->query($sqlAro1);
		$usersDetail = $this->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => -1));

		if (isset($aro1[0]['Aro']['parent_id']) && !empty($aro1[0]['Aro']['parent_id'])) {
			$sqlAro2 = "SELECT id, parent_id, model, foreign_key, alias FROM aros as Aro WHERE  Aro.id='" . $aro1[0]['Aro']['parent_id'] . "' and model='Role'";
			$aro2 = $this->query($sqlAro2);
		}

		if (isset($aro1[0]) && !empty($aro1[0])) {
			$node[] = $aro1[0];
		}

		if (isset($aro2[0]) && !empty($aro2[0])) {
			$node[] = $aro2[0];
			//debug($node);
		} else if (!empty($userDetail['User']['role_id'])) {

			$user = array('User' => array('id' => $userId));
			$nodes = $this->node($user);
			$usersDetail = $this->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => -1));

			if (!empty($nodes)) {
				foreach ($nodes as $k => $v) {
					if ($v['Aro']['model'] == "Role" && $v['Aro']['id'] == $usersDetail['User']['role_id']) {
						$node[] = $v;
						break;
					}
				}
			}
		}

		//debug($aro2);
		if (empty($node)) {
			//create node 
			$parentNodeQuery = "SELECT id, parent_id FROM aros as Aro  where model='Role' and foreign_key='" . $usersDetail['User']['role_id'] . "'";
			$parentNodeResult = $this->query($parentNodeQuery);
			//debug($parentNodeResult);

			$lftMaxQuery = "SELECT parent_id, lft FROM aros as Aro WHERE parent_id = '" . $usersDetail['User']['role_id'] . "' order by lft DESC   limit 1";
			$lftMaxQueryResult = $this->query($lftMaxQuery);

			$rghtMaxQuery = "SELECT parent_id, rght FROM aros as Aro WHERE parent_id = '" . $usersDetail['User']['role_id'] . "'  order by rght DESC limit 1";
			$rghtMaxQueryResult = $this->query($rghtMaxQuery);

			$aro = new Aro();
			$data['Aro']['parent_id'] = $parentNodeResult[0]['Aro']['id'];
			$data['Aro']['model'] = "User";
			$data['Aro']['foreign_key'] = $usersDetail['User']['id'];
			$data['Aro']['lft'] = $lftMaxQueryResult[0]['Aro']['lft'] + 1;
			$data['Aro']['rght'] = $rghtMaxQueryResult[0]['Aro']['rght'] + 1;

			if (isset($data) && !empty($data)) {
				$aro->create();
				$aro->save($data);
			}

			$aroSQL = "SELECT id,parent_id, model, foreign_key,alias FROM aros as Aro WHERE Aro.foreign_key = '" . $userId . "' limit 1";
			$aroSQLResult = $this->query($aroSQL);

			if (isset($aroSQLResult[0]) && !empty($aroSQLResult[0])) {
				$node[] = $aroSQLResult[0];
			}
		}
		//debug($node);

		$permissions = array();
		$permissionAggregated = array();

		if ($usersDetail['User']['role_id'] == ROLE_STUDENT) {
			$userDetail = $this->find('first', array(
				'conditions' => array(
					'User.id' => $userId
				),
				'contain' => array(
					'AcceptedStudent',
					'Student' => array('AcceptedStudent')
				)
			));
		} else {
			$userDetail = $this->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => -1));
		}

		// the sql for role and user privilage access is a bit different
		if (!empty($node)) {
			foreach ($node as $in => $value) {
				$aroId = $value['Aro']['id'];
				//retrive user level privilage for those none admin and their role is not student, instructor,and sysadmin
				if (($userDetail['User']['is_admin'] != 1 &&
					($userDetail['User']['role_id'] == ROLE_DEPARTMENT || 
					$userDetail['User']['role_id'] == ROLE_COLLEGE ||
					$userDetail['User']['role_id'] == ROLE_ACCOMODATION ||
					$userDetail['User']['role_id'] == ROLE_MEAL ||
					$userDetail['User']['role_id'] == ROLE_HEALTH ||
					$userDetail['User']['role_id'] == ROLE_REGISTRAR || 
					$userDetail['User']['role_id'] > 10)) && strcmp($value['Aro']['model'], 'User') == 0) 
				{

					$sql = "SELECT DISTINCT alias FROM  (
					SELECT user_id.aro_id ,(
					SELECT case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias FROM (
					SELECT id,`alias` as method, (SELECT `alias` FROM acos WHERE id=t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id=(SELECT `parent_id` FROM acos WHERE id=t.parent_id)) as `master` FROM acos t) Aco WHERE Aco.id = p.aco_id) as name,
					CASE WHEN p.aco_id = 0  OR p.aco_id is NULL  THEN p.aco_id  END as aco_id,
					CASE WHEN p._create = 0 OR p._create is NULL THEN p._create  END as _create,
					CASE WHEN p._read = 0   OR p._read is NULL   THEN p._read END as _read,
					CASE WHEN p._update = 0 OR p._update is NULL THEN p._update  END as _update,
					CASE WHEN p._delete = 0 OR p._delete is NULL THEN p._delete END as _delete FROM  (
					SELECT 'a' as flag,$aroId as aro_id) user_id LEFT JOIN (
					SELECT 'a' as mark2,aco_id,`_create`,`_read`, `_update`, `_delete`  FROM `aros_acos` AS `Permission` LEFT JOIN `aros` AS `Aro` ON (`Permission`.`aro_id` = `Aro`.`id`) 
					LEFT JOIN `acos` AS `Aco` ON (`Permission`.`aco_id` = `Aco`.`id`) WHERE `Permission`.`aro_id` = $aroId )p ON p.mark2 = user_id.flag
					WHERE p._create = 1 OR p._read = 1 or p._update = 1 or p._delete = 1 ) tt 
					LEFT JOIN (
					SELECT `Aco`.`id` , `Aco`.`parent_id` , `Aco`.`model` , `Aco`.`foreign_key` , `Aco`.rght,`Aco`.lft, case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias FROM ( 
					SELECT *, `alias` as method, (SELECT `alias` FROM acos WHERE id=t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id = (SELECT `parent_id` FROM acos WHERE id = t.parent_id)) as `master` FROM acos t) Aco  ) tmp_aco ON tmp_aco.id=tt.aco_id OR tmp_aco.alias like concat(tt.name,'%')";

					$tmppermissions = $this->query($sql);
					//debug($tmppermissions);
					$tmppermissions = Set::extract('/tmp_aco/alias', $tmppermissions);
					$permissions = $tmppermissions;
					$permissionAggregated['UserLevel'] = $tmppermissions;
					
					//retrive role level privilage for those who are admin, or instructor, student, or admin 
				} else if (($userDetail['User']['is_admin'] == 1 ||
					($userDetail['User']['role_id'] == ROLE_INSTRUCTOR ||
					$userDetail['User']['role_id'] == ROLE_STUDENT || 
					$userDetail['User']['role_id'] == ROLE_SYSADMIN || 
					$userDetail['User']['role_id'] > 10)) && strcmp($value['Aro']['model'], 'Role') == 0) 
				{

					//debug($value);
					$sql = "SELECT DISTINCT alias FROM (
					SELECT user_id.aro_id ,(
					SELECT case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias FROM (
					SELECT id,`alias` as method, (SELECT `alias` FROM acos WHERE id = t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id=(SELECT `parent_id` FROM acos WHERE id=t.parent_id)) as `master` FROM acos t) Aco
					WHERE Aco.id=u.aco_id OR Aco.id = p.aco_id ) as name,
					CASE WHEN u.aco_id = 0  OR u.aco_id is NULL  THEN p.aco_id  ELSE u.aco_id  END as aco_id,
					CASE WHEN u._create = 0 OR u._create is NULL THEN p._create ELSE u._create END as _create,
					CASE WHEN u._read = 0   OR u._read is NULL   THEN p._read   ELSE u._read   END as _read,
					CASE WHEN u._update = 0 OR u._update is NULL THEN p._update ELSE u._update END as _update,
					CASE WHEN u._delete = 0 OR u._delete is NULL THEN p._delete ELSE u._delete END as _delete
					FROM (
					SELECT 'a' as flag,$aroId as aro_id) user_id LEFT JOIN (
					SELECT 'a' as mark1,aco_id,`_create`,`_read`, `_update`, `_delete` FROM `aros_acos` AS `Permission` LEFT JOIN `aros` AS `Aro` ON (`Permission`.`aro_id` = `Aro`.`id`) LEFT JOIN `acos` AS `Aco` ON (`Permission`.`aco_id` = `Aco`.`id`) WHERE `Permission`.`aro_id` = $aroId  ORDER BY `Aco`.`lft` ) u ON u.mark1 = user_id.flag
					LEFT JOIN (
					SELECT 'a' as mark2,aco_id,`_create`,`_read`, `_update`, `_delete`  FROM `aros_acos` AS `Permission` LEFT JOIN `aros` AS `Aro` ON (`Permission`.`aro_id` = `Aro`.`id`) LEFT JOIN `acos` AS `Aco` ON (`Permission`.`aco_id` = `Aco`.`id`) WHERE `Permission`.`aro_id` = (
					SELECT id FROM aros WHERE id = (SELECT parent_id from aros WHERE id = $aroId)))p ON p.mark2 = user_id.flag
					WHERE u._create = 1 OR u._read = 1 or u._update = 1 or u._delete = 1 OR p._create = 1 OR p._read = 1 or p._update = 1 or p._delete = 1 ) tt
					LEFT JOIN (
					SELECT `Aco`.`id` , `Aco`.`parent_id` , `Aco`.`model` , `Aco`.`foreign_key` , `Aco`.rght,`Aco`.lft,
					case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias
					FROM (
					SELECT *, `alias` as method, (SELECT `alias` FROM acos WHERE id = t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id = (SELECT `parent_id` FROM acos WHERE id=t.parent_id)) as `master` FROM acos t) Aco ) tmp_aco ON tmp_aco.id = tt.aco_id OR tmp_aco.alias like concat(tt.name,'%')
					";

					//debug($sql);
					$tmppermissions = $this->query($sql);
					$tmppermissions = Set::extract('/tmp_aco/alias', $tmppermissions);
					$permissions = $tmppermissions;
					$permissionAggregated['RoleLevel'] = $tmppermissions;
					//debug($permissionAggregated);
				}

				if (strcmp($value['Aro']['model'], 'User') == 0) {

					$sql = "SELECT DISTINCT alias FROM (
					SELECT user_id.aro_id ,(
					SELECT case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias FROM (
					SELECT id,`alias` as method, (SELECT `alias` FROM acos WHERE id = t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id = (SELECT `parent_id` FROM acos WHERE id=t.parent_id)) as `master` FROM acos t) Aco
					WHERE Aco.id = p.aco_id) as name,
					CASE WHEN p.aco_id = 0  OR p.aco_id is NULL  THEN p.aco_id  END as aco_id,
					CASE WHEN p._create = 0 OR p._create is NULL THEN p._create  END as _create,
					CASE WHEN p._read = 0   OR p._read is NULL   THEN p._read END as _read,
					CASE WHEN p._update = 0 OR p._update is NULL THEN p._update  END as _update,
					CASE WHEN p._delete = 0 OR p._delete is NULL THEN p._delete END as _delete
					FROM (
					SELECT 'a' as flag,$aroId as aro_id) user_id LEFT JOIN  (
					SELECT 'a' as mark2,aco_id,`_create`,`_read`, `_update`, `_delete`  FROM `aros_acos` AS `Permission` LEFT JOIN `aros` AS `Aro` ON (`Permission`.`aro_id` = `Aro`.`id`) LEFT JOIN `acos` AS `Aco` ON (`Permission`.`aco_id` = `Aco`.`id`) WHERE `Permission`.`aro_id` = $aroId )p ON p.mark2 = user_id.flag
					WHERE p._create = 1 OR p._read = 1 or p._update = 1 or p._delete = 1 ) tt
					LEFT JOIN (
					SELECT `Aco`.`id` , `Aco`.`parent_id` , `Aco`.`model` , `Aco`.`foreign_key` , `Aco`.rght,`Aco`.lft,
					case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias
					FROM (
					SELECT *, `alias` as method, (SELECT `alias` FROM acos WHERE id = t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id = (SELECT `parent_id` FROM acos WHERE id = t.parent_id)) as `master` FROM acos t) Aco ) tmp_aco ON tmp_aco.id = tt.aco_id OR tmp_aco.alias like concat(tt.name,'%')
					";

					$a = $this->query($sql);
					$a = Set::extract('/tmp_aco/alias', $a);

					if (isset($a) && !empty($a)) {
						$permissionAggregated['UserLevelAllowed'] = $a;
					}
					// debug($permissionAggregated);

					$sql = "SELECT DISTINCT alias FROM (
					SELECT user_id.aro_id ,(
					SELECT case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias FROM (
					SELECT id,`alias` as method, (SELECT `alias` FROM acos WHERE id=t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id=(SELECT `parent_id` FROM acos WHERE id=t.parent_id)) as `master` FROM acos t) Aco
					WHERE Aco.id = p.aco_id ) as name,
					CASE WHEN p.aco_id = 0  OR p.aco_id is NULL  THEN p.aco_id  END as aco_id,
					CASE WHEN p._create = 0 OR p._create is NULL THEN p._create  END as _create,
					CASE WHEN p._read = 0   OR p._read is NULL   THEN p._read END as _read,
					CASE WHEN p._update = 0 OR p._update is NULL THEN p._update  END as _update,
					CASE WHEN p._delete = 0 OR p._delete is NULL THEN p._delete END as _delete
					FROM (
					SELECT 'a' as flag, $aroId as aro_id) user_id LEFT JOIN (
					SELECT 'a' as mark2,aco_id,`_create`,`_read`, `_update`, `_delete`  FROM `aros_acos` AS `Permission` LEFT JOIN `aros` AS `Aro` ON (`Permission`.`aro_id` = `Aro`.`id`) LEFT JOIN `acos` AS `Aco` ON (`Permission`.`aco_id` = `Aco`.`id`) WHERE `Permission`.`aro_id` = $aroId )p ON p.mark2 = user_id.flag
					WHERE p._create =-1 OR p._read = -1 or p._update = -1 or p._delete = -1 ) tt
					LEFT JOIN (
					SELECT `Aco`.`id` , `Aco`.`parent_id` , `Aco`.`model` , `Aco`.`foreign_key` , `Aco`.rght,`Aco`.lft,
					case when controler is null then method WHEN master is null then concat(controler,'/',method) else concat(master,'/',controler,'/',method) end as alias
					FROM (
					SELECT *, `alias` as method, (SELECT `alias` FROM acos WHERE id = t.parent_id) as controler, (SELECT `alias` FROM acos WHERE id = (SELECT `parent_id` FROM acos WHERE id=t.parent_id)) as `master` FROM acos t) Aco ) tmp_aco ON tmp_aco.id = tt.aco_id OR tmp_aco.alias like concat(tt.name,'%')
					";

					$x = $this->query($sql);
					$x = Set::extract('/tmp_aco/alias', $x);

					if (isset($x) && !empty($x)) {
						$permissionAggregated['UserLevelDenied'] = $x;
					}
				}
			}
		}

		//add to permission list if user specific privilage is assigned
		if (isset($permissionAggregated['UserLevelAllowed']) && !empty($permissionAggregated['UserLevelAllowed'])) {
			foreach ($permissionAggregated['UserLevelAllowed'] as $value) {
				if (isset($permissionAggregated['RoleLevel']) && !empty($permissionAggregated['RoleLevel'])) {
					if (!in_array($value, $permissionAggregated['RoleLevel'])) {
						$permissions[] = $value;
					}
				} else {
					$permissions[] = $value;
				}
			}
		}

		//remove from  permission list if user specific privilage is denied
		if (isset($permissionAggregated['UserLevelDenied']) && !empty($permissionAggregated['UserLevelDenied'])) {
			foreach ($permissions as $index => &$value) {
				if (in_array($value, $permissionAggregated['UserLevelDenied']) || empty($value)) {
					unset($permissions[$index]);
				}
			}
		}

		// 
		$reformatePermission = array();
		//check the user is admin and give privilage to security modules

		if (!isset($userDetail['Student']) && ($userDetail['User']['is_admin'] || $userDetail['User']['role_id'] == ROLE_SYSADMIN)) {

			if ($userDetail['User']['role_id'] == ROLE_REGISTRAR || ($userDetail['User']['is_admin']  == 1 && $userDetail['User']['role_id'] == ROLE_SYSADMIN) ) {
				$permissions[] = "controllers/Users/assign";
			}

			if ($userDetail['User']['role_id'] == ROLE_DEPARTMENT) {
				$permissions[] = "controllers/Users/department_create_user_account";
			}

			if ($userDetail['User']['role_id'] != ROLE_DEPARTMENT && $userDetail['User']['role_id'] != ROLE_COLLEGE) {
				$permissions[] = "controllers/Users/add";
			}

			if (Configure::read("Developer")) {
				$permissions[] = 'controllers/Acls/Acos/add';
				$permissions[] = 'controllers/Acls/Acos/edit';
				$permissions[] = 'controllers/Acls/Acos/delete';
				$permissions[] = 'controllers/Acls/Acos/rebuild';
			}
			
			$permissions[] = 'controllers/Securitysettings/permission_management';
			$permissions[] = 'controllers/Securitysettings/index';
			$permissions[] = 'controllers/Acls/Permissions/add';
			$permissions[] = 'controllers/Acls/Permissions/delete';
			$permissions[] = 'controllers/Acls/Permissions/index';
			$permissions[] = 'controllers/Acls/Permissions/edit';
			$permissions[] = 'controllers/Acls/Acos/index';
			$permissions[] = 'controllers/Acls/Acls/index';
			$permissions[] = 'controllers/Users/index';
		}

		if (!isset($userDetail['Student']) && $userDetail['User']['role_id'] == ROLE_CLEARANCE) {
			$permissions[] = "controllers/TakenProperties/add";
			$permissions[] = "controllers/TakenProperties/edit";
			$permissions[] = "controllers/TakenProperties/delete";
			$permissions[] = "controllers/TakenProperties/returned_property";
		}

		// debug($permissions);
		$permissions[] = 'controllers/Dashboard/index';
		$permissions[] = 'controllers/Helps/index';
		
		// Make sure every entry is unique
		if (!empty($permissions)) {
			$permissions = array_unique($permissions);
		}

		//reformat the permission list for menu construction 
		foreach ($permissions as $in => $controller) {

			$tmController = explode('/', $controller);

			if (!isset($controller) && empty($controller)) {
				unset($permissions[$in]);
				continue;
			}

			//if student has department, dont show placement tab
			//  debug($userDetail);

			/* if ($userDetail['User']['role_id'] == ROLE_STUDENT && isset($userDetail['AcceptedStudent'][0]['program_id']) && $userDetail['AcceptedStudent'][0]['program_id'] == PROGRAM_UNDEGRADUATE) {
				$expireat = date("Y-m-d 00:00:01", strtotime("- 180 day"));
				if (isset($userDetail['AcceptedStudent']) && !empty($userDetail['AcceptedStudent']) && !empty($userDetail['AcceptedStudent'][0]['department_id']) && $userDetail['AcceptedStudent'][0]['Placement_Approved_By_Department'] == 1 && $userDetail['AcceptedStudent'][0]['created'] < $expireat) {
					// debug($tmController);
					if (count($tmController) > 2 && $tmController[0] == 'controllers' && strcmp($tmController[1], 'Preferences') == 0) {
						unset($permissions[$in]);
						continue;

					}
				}
			} */
           

			if ($userDetail['User']['role_id'] == ROLE_STUDENT && isset($userDetail['Student'][0]['AcceptedStudent']['id']) && $userDetail['Student'][0]['AcceptedStudent']['placementtype'] == "REGISTRAR PLACED") {
				if (count($tmController) > 2 && $tmController[0] == 'controllers' && (strcmp($tmController[1], 'Preferences') == 0 || strcmp($tmController[1], 'AcceptedStudents') == 0)) {
					unset($permissions[$in]);
					continue;
				}
			} else if ($userDetail['User']['role_id'] == ROLE_STUDENT && isset($userDetail['Student'][0]['AcceptedStudent']['id']) && empty($userDetail['Student'][0]['AcceptedStudent']['placementtype'])) {
				$expireat = date("Y-m-d 00:00:01", strtotime("- 120 day"));

				if (isset($userDetail['Student'][0]) && !empty($userDetail['Student'][0]) && !empty($userDetail['Student'][0]['AcceptedStudent']['department_id']) &&  $userDetail['Student'][0]['AcceptedStudent']['Placement_Approved_By_Department'] == 1 && !empty($userDetail['Student'][0]['curriculum_id'])) {
					if (count($tmController) > 2 && $tmController[0] == 'controllers' && strcmp($tmController[1], 'Preferences') == 0 ) {
						unset($permissions[$in]);
						continue;
					}
				}
			}

			// remove course exemptiom & course substitution premissions from Student Role temporarly until smis upgrade is complete
			if ($userDetail['User']['role_id'] == ROLE_STUDENT && (count($tmController) > 2 && $tmController[0] == 'controllers' && (strcmp($tmController[1], 'CourseExemptions') == 0 || strcmp($tmController[1], 'CourseSubstitutionRequests') == 0 ))) {
				unset($permissions[$in]);
				continue;
			}

			// remove PlacementParticipatingStudents from menu list
			if (count($tmController) > 2 && $tmController[0] == 'controllers' && strcmp($tmController[1], 'PlacementParticipatingStudents') == 0 ) {
				unset($permissions[$in]);
				continue;
			}

			if (count($tmController) > 2 && $tmController[0] == 'controllers' && $tmController[2] != 'Acls') {
				$reformatePermission[$tmController[1]]['action'][] = $tmController[2];
			}
		}

		//include index automatically if any of the controller action is allowed

		if (!empty($reformatePermission)) {
			foreach ($reformatePermission as $c => &$a) {
				if (!in_array('index', $a['action']) && !strcmp($c, "Acls") == 0) {
					$a['action'][] = 'index';
					$permissions[] = 'controllers' . DS . $c . DS . 'index';
				}
			}
		}

		// $permissions[]= "controllers/Dashboard/index";
		// debug($reformatePermission);

		$equivalentACL = Configure::read('ACL.equivalentACL');

		if (isset($equivalentACL) && !empty($equivalentACL) && is_array($equivalentACL)) {
			foreach ($equivalentACL as $parent => $child_acls) {
				foreach ($child_acls as $child_acl) {
					$checking = explode('/', $child_acl);
					if ($checking[1] == '*' && isset($checking[0])) {
						if (isset($reformatePermission[$checking[0]]['action'])) {
							$controllerActions = $reformatePermission[$checking[0]]['action'];
							if (isset($controllerActions) && !empty($controllerActions)) {
								$cntrlaction = explode('/', $parent);
								//give privilage to index if any controller action is privilaged
								if (isset($reformatePermission[$checking[0]]['action']) && is_array($reformatePermission[$checking[0]]['action'])) {
									//for false controllers index 
									if (!isset($reformatePermission[$cntrlaction[0]]['action'])) {
										$reformatePermission[$cntrlaction[0]]['action'][] = 'index';
									} else {
										if (!in_array('index', $reformatePermission[$cntrlaction[0]]['action'])) {
											$reformatePermission[$cntrlaction[0]]['action'][] = 'index';
										}
									}
								}
								// for other controllers index 
								// debug($checking[0]);       
								if (isset($reformatePermission[$checking[0]]['action'])) {
									if (!in_array('index', $reformatePermission[$checking[0]]['action'])) {
										$reformatePermission[$checking[0]]['action'][] = 'index';
										$permissions[] = 'controllers' . '/' . $checking[0] . '/' . 'index';
									}
								}

								if (is_array($permissions)) {
									if (!in_array('controllers' . '/' . $cntrlaction[0] . '/' . 'index', $permissions)) {
										$permissions[] = 'controllers' . '/' . $cntrlaction[0] . '/' . 'index';
									}
								}
							}
						}
					} else {
						if (isset($checking[0])) {
							if (isset($reformatePermission[$checking[0]]['action']) && in_array($checking[1], $reformatePermission[$checking[0]]['action'])) {
								$controllerActions = $reformatePermission[$checking[0]]['action'];
								if (isset($controllerActions) && !empty($controllerActions)) {
									$cntrlaction = explode('/', $parent);
									//give privilage to false controller index if any controller action is privilaged
									if (!isset($reformatePermission[$cntrlaction[0]]['action'])) {
										$reformatePermission[$cntrlaction[0]]['action'][] = 'index';
									} else {
										if (!in_array('index', $reformatePermission[$cntrlaction[0]]['action'])) {
											$reformatePermission[$cntrlaction[0]]['action'][] = 'index';
										}
									}
									if (is_array($permissions)) {
										if (!in_array('controllers' . '/' . $cntrlaction[0] . '/' . 'index', $permissions)) {
											$permissions[] = 'controllers' . '/' . $cntrlaction[0] . '/' . 'index';
										}
									}
								}
								// for other controllers index            
								if (isset($reformatePermission[$checking[0]]['action'])) {
									if (!in_array('index', $reformatePermission[$checking[0]]['action'])) {
										$reformatePermission[$checking[0]]['action'][] = 'index';
										$permissions[] = 'controllers' . '/' . $checking[0] . '/' . 'index';
									}
								}
							}
						}
					}

					// Make sure every entry is unique
					if (isset($reformatePermission[$checking[0]]['action'])) {
						array_unique($reformatePermission[$checking[0]]['action']);
					}
				}
			}
		}

		$result['permission'] = $permissions;
		$result['reformatePermission'] = $reformatePermission;
		$result['permissionAggregated'] = $permissionAggregated;

		//debug($result);

		if ($userDetail['User']['role_id'] == ROLE_STUDENT) {
			// unset($result['reformatePermission']['AcceptedStudents']);
			// unset($result['reformatedPermission']['Students'][0]['index']);

			if (!empty($result['permissionAggregated']['RoleLevel']) && is_array($result['permissionAggregated']['RoleLevel'])) {
				foreach ($result['permissionAggregated']['RoleLevel'] as $key => $value) {
					if (strcasecmp($value, 'controllers/CourseRegistrations/register_individual_course') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}
				}
			}

			if (!empty($result['reformatePermission']['CourseRegistrations']['action']) && is_array($result['reformatePermission']['CourseRegistrations']['action'])) {
				foreach ($result['reformatePermission']['CourseRegistrations']['action'] as $key => $value) {
					if (strcasecmp($value, 'register_individual_course') == 0) {
						unset($result['reformatePermission']['CourseRegistrations']['action'][$key]);
					}
				}
			}

			if (!empty($result['reformatePermission']['Readmissions']['action']) && is_array($result['reformatePermission']['Readmissions']['action'])) {
				foreach ($result['reformatePermission']['Readmissions']['action'] as $key => $value) {
					if (strcasecmp($value, 'apply') == 0) {
						unset($result['reformatePermission']['Readmissions']['action'][$key]);
					}
				}
			}

			if (!empty($result['permission']) && is_array($result['permission'])) {
				foreach ($result['permission'] as $key => $value) {
					if (strcasecmp($value, 'controllers/CourseRegistrations/register_individual_course') == 0) {
						unset($result['permission'][$key]);
					}
				}
			}
		}

		if ($userDetail['User']['role_id'] == ROLE_COLLEGE) {
			if (!empty($result['permission']) && is_array($result['permission'])) {
				foreach ($result['permission'] as $key => $value) {
					if (strcasecmp($value, 'controllers/courseInstructorAssignments/assign_course_instructor') == 0) {
						unset($result['permission'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/add') == 0) {
						unset($result['permission'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/approve_clearance') == 0) {
						unset($result['permission'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/withdraw_management') == 0) {
						unset($result['permission'][$key]);
					}
				}
			}

			if (!empty($result['permissionAggregated']['RoleLevel']) && is_array($result['permissionAggregated']['RoleLevel'])) {
				foreach ($result['permissionAggregated']['RoleLevel'] as $key => $value) {
					if (strcasecmp($value, 'controllers/sectionSplitForPublishedCourses') == 0 || strcasecmp($value, 'controllers/sectionSplitForPublishedCourses/split') == 0 || strcasecmp($value, 'controllers/sectionSplitForPublishedCourses/index') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/add') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/approve_clearance') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/withdraw_management') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}
				}
			}
		}

		if ($userDetail['User']['role_id'] == ROLE_DEPARTMENT) {
			if (!empty($result['permissionAggregated']['RoleLevel']) && is_array($result['permissionAggregated']['RoleLevel'])) {
				foreach ($result['permissionAggregated']['RoleLevel'] as $key => $value) {
					if (strcasecmp($value, 'controllers/sectionSplitForPublishedCourses') == 0 || strcasecmp($value, 'controllers/sectionSplitForPublishedCourses/split') == 0 || strcasecmp($value, 'controllers/sectionSplitForPublishedCourses/index') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/add') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/approve_clearance') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/withdraw_management') == 0) {
						unset($result['permissionAggregated']['RoleLevel'][$key]);
					}
				}
			}

			if (!empty($result['permission']) && is_array($result['permission'])) {
				foreach ($result['permission'] as $key => $value) {
					if (strcasecmp($value, 'controllers/Clearances/add') == 0) {
						unset($result['permission'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/approve_clearance') == 0) {
						unset($result['permission'][$key]);
					}

					if (strcasecmp($value, 'controllers/Clearances/withdraw_management') == 0) {
						unset($result['permission'][$key]);
					}
				}
			}
		}

		if (!empty($result['permission'])) {
			$result['permission'] = array_unique($result['permission']);
			//debug($result['permission']);
		} 

		if (!empty($result['reformatePermission'])) {
			ksort($result['reformatePermission']);
			debug($result['reformatePermission']);
		} 

		if (!empty($result['permissionAggregated'])) {
			debug($result['permissionAggregated']);
		} 

		//debug($result);
		return $result;
	}

	function my_array_merge(&$array1, $array2)
	{
		$result = array();
		foreach ($array2 as $key => $value) {
			if (!in_array($value, $array1)) {
				$array1[] = $value;
			}
		}
		return $array1;
	}

	function searchUserConditions($role_id, $search_params = null, $department_id = null, $college_id = null)
	{
		$options = array();
		$role_parent = array();
		
		$search_params['User']['role_id'] = (isset($search_params['Search']) ? $search_params['Search']['role_id'] : $search_params['User']['role_id']);
		$search_params['User']['name'] = (isset($search_params['Search']) ? $search_params['Search']['name'] : $search_params['User']['name']);
		$search_params['User']['active'] = (isset($search_params['Search']) ? $search_params['Search']['active'] : $search_params['User']['active']);
		$search_params['Staff']['active'] = (isset($search_params['Search']) ? $search_params['Search']['Staff']['active'] : $search_params['Staff']['active']);
		
		if (!empty($search_params['User']['role_id'])) {
			$role_parent[$search_params['User']['role_id']] = $search_params['User']['role_id'];
		} else {
			$role_parent = $this->Role->find('list', array('conditions' => array('Role.parent_id' => $role_id), 'fields' => array('id')));
			$role_parent[$role_id] = $role_id;
		}

		unset($role_parent[ROLE_STUDENT]);

		if ($role_id == ROLE_SYSADMIN) {
			if (!empty($search_params['User']['role_id'])) {
				$options['conditions'][] = array(
					'User.role_id' => $search_params['User']['role_id'],
					//'User.is_admin' => 1
				);
			} else {
				$options['conditions'][] = array(
					"OR" => array(
						'User.is_admin' => 1,
						'User.role_id' => $role_parent,
					),
				);
			}
		} else {
			$options['conditions'][]['User.role_id'] = $role_parent;
		}

		if (!empty($search_params['User']['name'])) {
			$options['conditions'][] = array(
				"OR" => array(
					'User.first_name LIKE ' =>  '%'. (trim($search_params['User']['name'])) . '%',
					'User.last_name LIKE ' =>  '%'. (trim($search_params['User']['name'])) . '%',
					'User.middle_name LIKE ' =>  '%'.( trim($search_params['User']['name'])) . '%',
					'User.username LIKE' =>  '%'. (trim($search_params['User']['name'])) . '%',
					'User.email LIKE' =>  '%'. (trim($search_params['User']['name'])) . '%',
				)
			);
		}

		if (!empty($department_id) && $role_id == ROLE_DEPARTMENT) {
			//$options['conditions'][] = array('User.id IN (select user_id from staffs where department_id = ' . $department_id . ')');
			$options['conditions'][] = array('User.id IN (select user_id from staffs where department_id = ' . $department_id . ' and active = ' . $search_params['Staff']['active'] . ')');
		} else if (!empty($college_id) && $role_id == ROLE_COLLEGE) {
			//$options['conditions'][] = array('User.id IN (select user_id from staffs where college_id = ' . $college_id . ')');
			$options['conditions'][] = array('User.id IN (select user_id from staffs where college_id = ' . $college_id . ' and active = ' . $search_params['Staff']['active'] . ')');
		} else {
			$options['conditions'][] = array('User.id IN (select user_id from staffs where active = ' . $search_params['Staff']['active'] . ')');
		}

		$options['conditions'][]['User.active'] = $search_params['User']['active'];

		/* if (!empty($search_params['Staff']['active'])) {
			$options['conditions'][] = array('User.id IN (select user_id from staffs where active = ' . $search_params['Staff']['active'] . ')');
		}

		if (!empty($search_params['User']['active'])) {
			$options['conditions'][]['User.active'] = $search_params['User']['active'];
		} */

		return $options;
	}

	public function generatePassword($length = '')
	{
		// Array or string offset access with curly braces deprecated in PHP 7.4. Targeting PHP 8.2.0
		$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$max = strlen($str);
		$length = @round($length);

		if (empty($length)) {
			$length = rand(8, 12);
		}

		$password = '';

		if ($length) {
			for ($i = 0; $i < $length; $i++) {
				$password .= $str[rand(0, $max - 1)];
			}
		}

		return $password;
	}


	function regenerate_password_by_batch($department_college_id, $academicYear, $commonPassword, $pre = 0)
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		if ($pre == 1) {
			$options['conditions'] = array('Student.department_id is null', 'Student.college_id' => $department_college_id);
		} else if ($department_college_id != "all") {
			$options['conditions']['Student.department_id'] = $department_college_id;
		}

		if ($academicYear != "all") {
			//$options['conditions']['Student.admissionyear'] = $AcademicYear->get_academicYearBegainingDate($academicYear); //will not be accurate, there are students that will be admitted lateley
			$options['conditions']['Student.academicyear'] = $academicYear;
		}

		$options['conditions']['Student.graduated'] = 0;

		$options['fields'] = array('Student.id', 'Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.admissionyear', 'Student.gender', 'Student.academicyear');
		$options['order'] = array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC', 'Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC');

		$options['conditions'][] = 'Student.user_id  in (select id from users where role_id = 3 and force_password_change = 1)';
		$options['recursive'] = -1;

		$studenLists = ClassRegistry::init('Student')->find('all', $options);

		$student_password = array();
		$count = 0;

		if (!empty($studenLists)) {
			foreach ($studenLists as $kkk => $vv) {

				$student_details = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $vv['Student']['id']), 'contain' => array('User')));

				if (!empty($student_details['User']) && $student_details['User']['force_password_change'] == 1) {
					$student_password['User'][$count]['id'] = $student_details['User']['id'];
					$student_password['User'][$count]['force_password_change'] = 1;
					$student_password['User'][$count]['password'] =  Security::hash(trim($commonPassword), null, true);
				}
				$count++;
			}
		}

		if (!empty($student_password)) {
			if ($this->saveAll($student_password['User'], array('validate' => false))) {
				return 'done';
			}
		}
		//debug(count($student_password['User']));
	}

	public function createStudentAccountBatch($program_type, $department_college_id, $academicYear, $commonPassword, $pre = 0)
	{
		App::import('Component', 'AcademicYear');
		$AcademicYear = new AcademicYearComponent(new ComponentCollection);

		$admissionYearConverted = $AcademicYear->get_academicYearBegainingDate($academicYear);

		if (!empty($program_type) && !empty($admissionYearConverted) && $pre == 0) {
			$studentLists = ClassRegistry::init('Student')->find('all', array(
				'conditions' => array(
					'Student.department_id' => $department_college_id, 
					'Student.admissionyear' => $admissionYearConverted, 
					'Student.program_type_id' => $program_type,
					'Student.graduated' => 0,
					'Student.user_id is null'
				), 
				'recursive' => -1
			));
		} else {
			$studentLists = ClassRegistry::init('Student')->find('all', array(
				'conditions' => array(
					'Student.college_id' => $department_college_id, 
					'Student.department_id is null', 
					'Student.admissionyear' => $admissionYearConverted, 
					'Student.program_type_id' => $program_type,
					'Student.graduated' => 0,
					'Student.user_id is null'
				), 
				'recursive' => -1
			));
		}

		$student_table_update = array();
		$accepted_student_table_update = array();
		$count = 0;
		$failedCreation = 0;

		if (!empty($studentLists)) {
			foreach ($studentLists as $kkk => $vv) {
				$student_password = array();

				if (empty($vv['Student']['user_id'])) {

					$student_password['User']['username'] = $vv['Student']['studentnumber'];
					$student_password['User']['email'] = $vv['Student']['email'];
					$student_password['User']['role_id'] = ROLE_STUDENT;
					$student_password['User']['first_name'] = $vv['Student']['first_name'];
					$student_password['User']['middle_name'] = $vv['Student']['middle_name'];
					$student_password['User']['last_name'] = $vv['Student']['last_name'];
					$student_password['User']['force_password_change'] = 1;
					$student_password['User']['password'] =  Security::hash(trim($commonPassword), null, true);
					$student_password['User']['force_password_change'] = 1;

					if (!empty($student_password)) {
						$this->create();
						if ($this->save($student_password['User'])) {
							$student_table_update['Student'][$count]['id'] = $vv['Student']['id'];
							$student_table_update['Student'][$count]['user_id'] = $this->id;
							$accepted_student_table_update['AcceptedStudent'][$count]['id'] =  $vv['Student']['accepted_student_id'];
							$accepted_student_table_update['AcceptedStudent'][$count]['user_id'] =  $this->id;
						} else {
							$failedCreation++;
							//debug($this->invalidFields());
						}
					}
				}
				$count++;
			}
		}

		if (!empty($student_table_update)) {
			if (ClassRegistry::init('Student')->saveAll($student_table_update['Student'], array('validate' => false))) {
				echo 'done student update';
			}
		}

		if (!empty($accepted_student_table_update)) {
			if (ClassRegistry::init('AcceptedStudent')->saveAll($accepted_student_table_update['AcceptedStudent'], array('validate' => false))) {
				echo 'done accepted student update';
			}
		}
		//debug(count($student_password['User']));
	}

	public function resetPasswordBySMS($mobilePhoneNumber)
	{
		$userDetails = array();
		$generatedPassword = ClassRegistry::init('Student')->generatePassword(8);
		$studentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.phone_mobile' => $mobilePhoneNumber), 'contain' => array('User')));
		$result = '';
		
		if (!empty($studentDetail)) {
			// account already existed so update the password 
			if (!empty($studentDetail['User'])) {
				$result .= $generatedPassword . ' is your one time password and will expired in 30 minutes.';
				$userDetails['User']['id'] = $studentDetail['User']['id'];
				$userDetails['User']['password'] = Security::hash($generatedPassword, null, true);
				//$userDetails['User']['password']=$generatedPassword;
				$userDetails['User']['force_password_change'] = 2;
			} else { // create the account  and send the password 
				$result .= $generatedPassword . ' is your one time password and will expired in 30 minutes.';
				$userDetails['User']['role_id'] = ROLE_STUDENT;
				$userDetails['User']['username'] = $studentDetail['Student']['studentnumber'];
				$userDetails['User']['password'] = Security::hash($generatedPassword, null, true);
				$userDetails['User']['first_name'] = $studentDetail['Student']['first_name'];
				$userDetails['User']['middle_name'] = $studentDetail['Student']['middle_name'];
				$userDetails['User']['last_name'] = $studentDetail['Student']['last_name'];
				$userDetails['User']['force_password_change'] = 2;
			}
		} else {
			$staffDetail = ClassRegistry::init('Staff')->find('first', array(
				'conditions' => array('Staff.phone_mobile' => $mobilePhoneNumber), 
				'contain' => array('User')
			));
			// account already existed so update the password 

			if (!empty($staffDetail['User']['id'])) {
				$result .= $generatedPassword . ' is your one time password and will expired in 30 minutes.';
				$userDetails['User']['id'] = $staffDetail['User']['id'];
				$userDetails['User']['password'] = Security::hash($generatedPassword, null, true);
				//$userDetails['User']['password']=$generatedPassword;
				$userDetails['User']['force_password_change'] = 2;
			}
		}

		if (empty($userDetails['User']['id'])) {
			$this->create();
		}

		if (!empty($userDetails)) {
			if ($this->save($userDetails['User'])) {
				if ($studentDetail['User']['role_id'] == ROLE_STUDENT && empty($studentDetail['Student']['user_id'])) {
					ClassRegistry::init('Student')->id = $studentDetail['Student']['id'];
					ClassRegistry::init('Student')->saveField('user_id', $this->id);
					ClassRegistry::init('AcceptedStudent')->id = $studentDetail['Student']['accepted_student_id'];
					ClassRegistry::init('AcceptedStudent')->saveField('user_id', $this->id);
				} else if (empty($staffDetail['Staff']['user_id'])) {
					ClassRegistry::init('Staff')->id = $staffDetail['Staff']['id'];
					ClassRegistry::init('Staff')->saveField('user_id', $this->id);
				}
				return $result;
			} else {
			}
		}
		return 'The phone number you provided is not found in our system.';
	}

	//event management system, observer pattern :) raising and event 
	public function afterSave($created, $options = array())
	{
		parent::afterSave($created, $options);

		if ($created === true) {
			$Event = new CakeEvent('Model.User.created', $this, array(
				'id' => $this->id,
				'data' => $this->data[$this->alias]
			));
			$this->getEventManager()->dispatch($Event);
		} else {

			App::import('Component', 'Browser');
			$Browser = new BrowserComponent();

			$Event = new CakeEvent('Model.User.login', $this, array(
				'data' => $this->data[$this->alias],
				'browser' => $Browser->showInfo('browser'),
				'os' => $Browser->showInfo('os'),
				'ip' => $_SERVER['REMOTE_ADDR']
			));
			$this->getEventManager()->dispatch($Event);
		}
	}

	public function syncAccount($isStudent = 0)
	{
		if ($isStudent == 0) {
			$model = 'Staff';

			$findAccountNotInAro = $this->find('all', array(
				'conditions' => array(
					'User.role_id !=' => ROLE_STUDENT,
					'User.id not in (select foreign_key from aros where model = "User" and parent_id is not null)'
				),
				'contain' => array($model, 'Aro')
			));

			if (!empty($findAccountNotInAro)) {
				$this->synchronizeAros($findAccountNotInAro, $model);
			}

		} else {
			$model = 'Student';

			$findAccountNotInAro = $this->find('all', array(
				'conditions' => array(
					'User.role_id' => ROLE_STUDENT,
					'User.id not in (select foreign_key from aros where model = "User" and parent_id is not null)'
				),
				'contain' => array($model, 'Aro')
			));

			if (!empty($findAccountNotInAro)) {
				$this->synchronizeAros($findAccountNotInAro, $model);
			}
		}
	}

	function synchronizeAros($findAccountNotInAro, $model = 'Staff')
	{
		if (!empty($findAccountNotInAro)) {
			foreach ($findAccountNotInAro as $akey => $avalue) {
				$aros = ClassRegistry::init('Aros')->find('first', array('order' => array('Aros.rght' => 'DESC')));
				$userDetail = $this->find('first', array('conditions' => array('User.id' => $avalue['User']['id']), 'contain' => array("$model")));

				$arosRoles = ClassRegistry::init('Aros')->find('first', array(
					'conditions' => array(
						'Aros.model' => 'Role', 
						'Aros.parent_id is null', 
						'Aros.foreign_key' => $avalue['User']['role_id']
					)
				));

				if (!empty($userDetail["$model"])) {
					// create only the aro
					$saveAro['Aros']['foreign_key'] = $userDetail['User']['id'];
					$saveAro['Aros']['parent_id'] = $arosRoles['Aros']['id'];
					$saveAro['Aros']['model'] = 'User';
					$saveAro['Aros']['lft'] = $aros['Aros']['rght'] + 1;
					$saveAro['Aros']['rght'] = $saveAro['Aros']['lft'] + 1;

					ClassRegistry::init('Aros')->create();

					if (ClassRegistry::init('Aros')->save($saveAro)) {
					}
				} else if (!empty($userDetail) && empty($userDetail[$model])) {
					// create staff and aro 
					$saveAro['Aros']['foreign_key'] = $userDetail['User']['id'];
					$saveAro['Aros']['parent_id'] = $arosRoles['Aros']['id'];
					$saveAro['Aros']['model'] = 'User';
					$saveAro['Aros']['lft'] = $aros['Aros']['rght'] + 1;
					$saveAro['Aros']['rght'] = $saveAro['Aros']['lft'] + 1;
					ClassRegistry::init('Aros')->create();

					if (ClassRegistry::init('Aros')->save($saveAro)) {
					}

					$saveStaff["$model"]['user_id'] = $userDetail['User']['id'];
					$saveStaff["$model"]['first_name'] = $userDetail['User']['first_name'];
					$saveStaff["$model"]['middle_name'] = $userDetail['User']['middle_name'];
					$saveStaff["$model"]['last_name'] = $userDetail['User']['last_name'];
					$saveStaff["$model"]['email'] = $userDetail['User']['email'];

					$this->$model->create();

					if ($this->$model->save($saveStaff, array('validate' => false))) {
					}
				}
			}
		}
	}

	public function getUserLogDetail($userId, $params = array())
	{
		$user = $this->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => -1));
		$username = $user['User']['username'];
		$fields = array();

		if (isset($params['fields'])) {
			if (is_array($params['fields'])) {
				$fields = $params['fields'];
			} else {
				$fields = array($params['fields']);
			}
		}

		if (isset($params['conditions'])) {
			$conditions = $params['conditions'];
		}

		$order = array('created' => 'DESC');

		if (isset($params['order'])) {
			$order = $params['order'];
		}

		$limit = 1;

		if (isset($params['limit'])) {
			$limit = $params['limit'];
		}

		$data = ClassRegistry::init('Log')->find('all', array(
			'conditions' => $conditions,
			'recursive' => -1,
			'fields' => $fields,
			'order' => $order,
			'limit' => $limit
		));

		/*
		if (!isset($params['events']) || (isset($params['events']) && $params['events'] == false)) {
			return $data;
		}
		*/

		$result = array();

		if (!empty($data)) {
			foreach ($data as $key => $row) {
				$actedUser = $this->find('first', array('conditions' => array('User.id' => $row['Log']['user_id']), 'recursive' => -1));
				$one = $row['Log'];
				$result[$key]['Log']['id'] = $one['id'];
				$result[$key]['Log']['created'] = $one['created'];

				// have all the detail models and change as list :
				$label = $this->getLogChangeDetail($one);
				$changeDecomposed = explode(',', $one['change']);
				$changeMade = '<ul>';

				if (!empty($changeDecomposed)) {
					foreach ($changeDecomposed as $chk => $chv) {
						$changeMade .= '<li>' . $chv . '</li>';
					}
				}

				$changeMade .= '</ul>';
				$result[$key]['Log']['event'] = $actedUser['User']['first_name'] . ' ' . $actedUser['User']['middle_name'] . '(' . $actedUser['User']['username'] . ')' . ' ' . $one['action'] . 'ed ' . $label . '. <br/><strong>The change was:-</strong> ' . $changeMade . ' ';
			}
		}

		//debug($result);
		return $result;
	}

	function getLogChangeDetail($logDetail) 
	{
		if ($logDetail['model'] == "ExamGrade") {
			preg_match("/\(([^\)]*)\)/", $logDetail['description'], $aMatches);
			//debug($aMatches);
			$regId = $aMatches[1];

			if (isset($logDetail['foreign_key']) && !empty($logDetail['foreign_key'])) {
				$examGradeDetail = ClassRegistry::init('ExamGrade')->find('first', array(
					'conditions' => array(
						'ExamGrade.id' => $logDetail['foreign_key'],
					),
					'contain' => array(
						'CourseRegistration' => array(
							'Student', 
							'PublishedCourse' => array('Course')
						),
						'CourseAdd' => array(
							'Student',
							'PublishedCourse' => array('Course')
						)
					)
				));
			} else if (isset($regId) && !empty($regId)) {
				$examGradeDetail = ClassRegistry::init('ExamGrade')->find('first', array(
					'conditions' => array(
						'ExamGrade.id' => $regId,
					),
					'contain' => array(
						'CourseRegistration' => array(
							'Student', 
							'PublishedCourse' => array('Course')
						),
						'CourseAdd' => array(
							'Student',
							'PublishedCourse' => array('Course')
						)
					)
				));
			}

			if (isset($examGradeDetail) && !empty($examGradeDetail)) {
				if (isset($examGradeDetail['CourseRegistration']) && !empty($examGradeDetail['CourseRegistration'])) {
					$label = $examGradeDetail['CourseRegistration']['Student']['first_name'] . ' ' . $examGradeDetail['CourseRegistration']['Student']['last_name'] . '(' . $examGradeDetail['CourseRegistration']['Student']['studentnumber'] . ') of ' . $examGradeDetail['CourseRegistration']['PublishedCourse']['Course']['course_title'] . '' . $examGradeDetail['CourseRegistration']['PublishedCourse']['Course']['course_code'];
					return $label;
				} else if (isset($examGradeDetail['CourseAdd']) && !empty($examGradeDetail['CourseAdd'])) {
					$label = $examGradeDetail['CourseAdd']['Student']['first_name'] . ' ' . $examGradeDetail['CourseAdd']['Student']['last_name'] . '(' . $examGradeDetail['CourseAdd']['Student']['studentnumber'] . ') ' . $examGradeDetail['CourseAdd']['PublishedCourse']['Course']['course_title'] . '' . $examGradeDetail['CourseAdd']['PublishedCourse']['Course']['course_code'];
					return $label;
				}
			} else if (empty($examGradeDetail) && !empty($logDetail)) {
				$label = "but not reflected in the application since it has been deleted from the database by database administrator.";
				return $label;
			}

		} else if ($logDetail['model'] == 'CourseRegistration') {

			preg_match("/\(([^\)]*)\)/", $logDetail['description'], $aMatches);
			//debug($aMatches);
			$regId = $aMatches[1];

			if (isset($logDetail['foreign_key']) && !empty($logDetail['foreign_key'])) {
				$examGradeDetail = ClassRegistry::init('CourseRegistration')->find('first', array(
					'conditions' => array(
						'CourseRegistration.id' => $logDetail['foreign_key'],
					),
					'contain' => array(
						'PublishedCourse' => array('Course'),
						'Student'
					)
				));
			} else if (isset($regId) && !empty($regId)) {
				$examGradeDetail = ClassRegistry::init('CourseRegistration')->find('first', array(
					'conditions' => array(
						'CourseRegistration.id' => $regId,
					),
					'contain' => array(
						'PublishedCourse' => array('Course'),
						'Student'
					)
				));
			}

			debug($examGradeDetail);
			debug($logDetail);

			if (isset($examGradeDetail) && !empty($examGradeDetail)) {
				if (isset($examGradeDetail['CourseRegistration']) && !empty($examGradeDetail['CourseRegistration'])) {
					$label = $examGradeDetail['Student']['first_name'] . ' ' . $examGradeDetail['Student']['last_name'] . '(' . $examGradeDetail['Student']['studentnumber'] . ') of ' . $examGradeDetail['PublishedCourse']['Course']['course_title'] . '' . $examGradeDetail['PublishedCourse']['Course']['course_code'];
					return $label;
				}
			} else if (empty($examGradeDetail) && !empty($logDetail)) {
				$label = "but not reflected in the application since it has been deleted from the database by database administrator.";
				return $label;
			}

		} else if ($logDetail['Model'] == 'CourseAdd') {

			$examGradeDetail = ClassRegistry::init('CourseAdd')->find('first', array(
				'conditions' => array(
					'CourseAdd.id' => $logDetail['foreign_key'],
				),
				'contain' => array(
					'PublishedCourse' => array('Course'),
					'Student'
				)
			));

			if (isset($examGradeDetail) && !empty($examGradeDetail)) {
				if (isset($examGradeDetail['CourseAdd']) && !empty($examGradeDetail['CourseAdd'])) {
					$label = $examGradeDetail['Student']['first_name'] . ' ' . $examGradeDetail['Student']['last_name'] . '(' . $examGradeDetail['Student']['studentnumber'] . ') of ' . $examGradeDetail['PublishedCourse']['Course']['course_title'] . '' . $examGradeDetail['PublishedCourse']['Course']['course_code'];
					return $label;
				}
			} else if (empty($examGradeDetail) && !empty($logDetail)) {
				$label = "but not reflected in the application since it has been deleted from the database by database administrator.";
				return $label;
			}

		} else if ($logDetail['model'] == 'ExamGradeChange') {

			$examGradeDetail = ClassRegistry::init('ExamGradeChange')->find('first', array(
				'conditions' => array(
					'ExamGradeChange.id' => $logDetail['foreign_key'],
				),
				'contain' => array(
					'ExamGrade' => array(
						'CourseRegistration' => array(
							'Student',
							'PublishedCourse' => array('Course')
						),
						'CourseAdd' => array(
							'Student',
							'PublishedCourse' => array('Course')
						),
					)
				)
			));

			if (isset($examGradeDetail) && !empty($examGradeDetail)) {
				if (isset($examGradeDetail['ExamGrade']['CourseRegistration']) && !empty($examGradeDetail['ExamGrade']['CourseRegistration'])) {
					$label = $examGradeDetail['ExamGrade']['CourseRegistration']['Student']['first_name'] . ' ' . $examGradeDetail['ExamGrade']['CourseRegistration']['Student']['last_name'] . '(' . $examGradeDetail['ExamGrade']['CourseRegistration']['Student']['studentnumber'] . ') of ' . $examGradeDetail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course']['course_title'] . '' . $examGradeDetail['ExamGrade']['CourseRegistration']['PublishedCourse']['Course']['course_code'];
					return $label;
				} else if (isset($examGradeDetail['ExamGrade']['CourseAdd']) && !empty($examGradeDetail['ExamGrade']['CourseAdd'])) {
					$label = $examGradeDetail['ExamGrade']['CourseAdd']['Student']['first_name'] . ' ' . $examGradeDetail['ExamGrade']['CourseAdd']['Student']['last_name'] . '(' . $examGradeDetail['ExamGrade']['CourseAdd']['Student']['studentnumber'] . ') ' . $examGradeDetail['ExamGrade']['CourseAdd']['PublishedCourse']['Course']['course_title'] . '' . $examGradeDetail['ExamGrade']['CourseAdd']['PublishedCourse']['Course']['course_code'];
					return $label;
				}
			}

		} else if ($logDetail['model'] == 'Course') {

			$courseChange = ClassRegistry::init('Course')->find('first', array(
				'conditions' => array(
					'Course.id' => $logDetail['foreign_key'],
				),
				'contain' => array(
					'Curriculum'
				)
			));

			if (isset($courseChange['Course']) && !empty($courseChange['Course'])) {
				$label = $courseChange['Course']['course_title'] . '' . $courseChange['Course']['course_code'] . ' of' . $courseChange['Curriculum']['name'] . ' ' . $courseChange['Curriculum']['year_introduced'];
				return $label;
			} else if (empty($courseChange) && !empty($logDetail)) {
				$label = "but not reflected in the application since it has been deleted from the database by database administrator.";
				return $label;
			}

		} else if ($logDetail['model'] == 'Section') {

			$courseChange = ClassRegistry::init('Section')->find('first', array(
				'conditions' => array(
					'Section.id' => $logDetail['foreign_key'],
				),
				'contain' => array(
					'Department',
					'YearLevel'
				)
			));
			
			if (isset($courseChange['Section']) && !empty($courseChange['Section'])) {
				$label = $courseChange['Section']['name'] . '' . $courseChange['YearLevel']['name'] . '(Section AC-' . $courseChange['Section']['academicyear'] . ')' . ' of' . $courseChange['Department']['name'] . ' ' . $courseChange['Curriculum']['year_introduced'];
				return $label;
			} else if (empty($courseChange) && !empty($logDetail)) {
				$label = "but not reflected in the application since it has been deleted from the database by database administrator.";
				return $label;
			}

		} else if ($logDetail['model'] == 'Student') {

			$courseChange = ClassRegistry::init('Student')->find('first', array(
				'conditions' => array(
					'Student.id' => $logDetail['foreign_key'],
				),
				'contain' => array(
					'Department',
					'Curriculum',
					'AcceptedStudent'
				)
			));

			if (isset($courseChange['Student']) && !empty($courseChange['Student'])) {
				$label = $courseChange['Student']['full_name'] . '(' . $courseChange['Student']['studentnumber'] . ') admitted in ' . $courseChange['AcceptedStudent']['academicyear'];
				return $label;
			} else if (empty($courseChange) && !empty($logDetail)) {
				$label = "but histories not reflected in the application since it has been deleted from the database by database administrator.";
				return $label;
			}
			
		} else if ($logDetail['model'] == 'Session') {
		}
		return null;
	}

	public function removeLog($months = 12)
	{
		//$removeSQL="DELETE FROM logs WHERE created < UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL ".$days." DAY))";
		$removeSQL = "DELETE FROM  `logs` WHERE created < DATE_SUB(NOW(),INTERVAL " . $months . " MONTH )";
		$result = $this->query($removeSQL);
	}

	function getEquivalentProgramTypes($program_type_id = 0) 
	{
		$program_types_to_look = array();

		$equivalentProgramType = unserialize(ClassRegistry::init('ProgramType')->field('ProgramType.equivalent_to_id', array('ProgramType.id' => $program_type_id)));
		
		if (!empty($equivalentProgramType)) {
			$selected_program_type_array = array();
			$selected_program_type_array[] = $program_type_id;
			$program_types_to_look = array_merge($selected_program_type_array, $equivalentProgramType);
		} else {
			$program_types_to_look[] = $program_type_id;
		}

		//debug($program_types_to_look);

		return $program_types_to_look;
	}
}