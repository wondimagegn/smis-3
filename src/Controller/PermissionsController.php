<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PermissionsController extends AppController {
    var $name = 'Permissions';
    var $uses = array('Permission', 'User', 'Role');
    
    function beforeFilter() {
        parent::beforeFilter();
        $this->Permission->validate = array(
            'aro_id' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please select user or role.',
            )
        );
		
		// $this->Auth->allow('*');
    }
    
    function beforeRender() {
        parent::beforeRender();
        $perms = array(
            '1' => 'Allow',
           // '0' => 'Inherit',
           '-1' => 'Deny',
        );
        $this->set(compact('perms'));
    }
    
    function index($aco_id) {
       $admin_detail = ClassRegistry::init('User')->find('first',
  			array(
  				'conditions' =>
  				array(
  					'User.id' => Configure::read('User.user')
  				),
  				'contain' => 
  				array(
  					'Staff'
  				)
  			)
  		);
  		$this->_validateIfControllerPermissionCanBeManagedByTheUser($aco_id);
			$this->Permission->Aco->id = $aco_id;
			if (!$aco_id || !$this->Permission->Aco->exists()) {
				$this->Session->setFlash('<span></span>'.__('Invalid task ID.'), 'default', array('class' => 'error-message error-box'));
				$this->redirect(array('controller' => 'acos', 'action' => 'index', 1));
			}
        $this->Permission->Aco->actsAs[] = 'Tree';
        $aco = $this->Permission->Aco->find('first',
         	array(
         		'conditions' =>
         		array(
         			'Aco.id' => $aco_id
         		),
         		'recursive' => -1
         	)
         );
        $path = $this->_getAcoPathList($aco_id);
        $permissions = $this->Permission->find('all', array('conditions' => array('aco_id' => $aco_id)));
        foreach($permissions as $key => $i) {
            $path2 = $this->_getAcoPathList($i['Permission']['aco_id']);
            $permissions[$key]['Permission']['path'] = implode('/', $path2);
        }
        $users = $this->User->find('list', 
        	array(
        		'conditions' =>
        		array(
        			'User.role_id <>' => ROLE_STUDENT
        		),
        		'fields' => 
        		array(
        			'id', 
        			'username'
        		),
        		'order' => 'username'
        	)
        );
        if($admin_detail['User']['role_id'] == ROLE_COLLEGE || $admin_detail['User']['role_id'] == ROLE_DEPARTMENT) {
        	foreach($permissions as $p_key => $permission) {
        		if(strcasecmp($permission['Aro']['model'], 'Role') != 0) {
		     		$staff = ClassRegistry::init('Staff')->find('first',
		     			array(
		     				'conditions' =>
		     				array(
		     					'Staff.user_id' => $permission['Aro']['foreign_key']
		     				),
		     				'recursive' => -1
		     			)
		     		);
		     		if(($admin_detail['User']['role_id'] == ROLE_DEPARTMENT && $staff['Staff']['department_id'] != $admin_detail['Staff'][0]['department_id']) ||
		     			($admin_detail['User']['role_id'] == ROLE_COLLEGE && $staff['Staff']['college_id'] != $admin_detail['Staff'][0]['college_id'])
		     		) {
		     			unset($permissions[$p_key]);
		     		}
        		}
        	}
        }
        $roles = $this->Role->find('list', array('order' => 'name'));
        $this->set(compact('permissions', 'aco_id', 'path', 'users', 'roles', 'aco'));
    }
    
    
 function add($aco_id = null, $role_id = null) {
    	$this->_validateIfControllerPermissionCanBeManagedByTheUser($aco_id);
  		$admin_detail = ClassRegistry::init('User')->find('first',
  			array(
  				'conditions' =>
  				array(
  					'User.id' => Configure::read('User.user')
  				),
  				'contain' => 
  				array(
  					'Staff'
  				)
  			)
  		);
      if(isset($this->request->data['Permission']['role_id'])) {
      	$role_id = $this->request->data['Permission']['role_id'];
      }
      if(isset($this->request->data['Permission']['aco_id'])) {
      	$aco_id_forcheck = $this->request->data['Permission']['aco_id'];
      }
      else {
      	$aco_id_forcheck = $aco_id;
      }
        $aco = $this->Permission->Aco->find('first',
         	array(
         		'conditions' =>
         		array(
         			'Aco.id' => $aco_id_forcheck
         		),
         		'recursive' => -1
         	)
         );
         $aco_admins = explode(',', $aco['Aco']['admin']);
         if($aco['Aco']['parent_id'] != 1 && !in_array(Configure::read('User.role_id'), $aco_admins)) {
         	$this->Session->setFlash('<span></span>'.__('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.'), 'default',array('class'=>'error-box error-message'));
         	ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'));
         	$this->redirect(array('controller' => 'acos', 'action' => 'index'));
         }
        if (!empty($this->data)) {
            $this->request->data['Permission']['_create'] = $this->request->data['Permission']['privilege'];
            $this->request->data['Permission']['_read'] = $this->request->data['Permission']['privilege'];
            $this->request->data['Permission']['_update'] = $this->request->data['Permission']['privilege'];
            $this->request->data['Permission']['_delete'] = $this->request->data['Permission']['privilege'];
            $privilage_exits = $this->Permission->find('count', 
            	array(
            		'conditions' =>
            		array(
		         		'Permission.aco_id' => $this->request->data['Permission']['aco_id'],
		         		'Permission.aro_id' => $this->request->data['Permission']['aro_id']
            		)
            	)
            );
	         if (empty($this->request->data['Permission']['aro_id'])) {
	             $this->Session->setFlash('<span></span>'.__('Please select user for whom you want to give privilege.'), 'default',array('class'=>'error-box error-message'));
	         }
            else if($privilage_exits > 0) {
					$this->Session->setFlash('<span></span>'.__('There is already recorded privilege for the selected user/role. Please use edit to apply changes or delete the privilege and re-create.'), 'default',array('class'=>'error-box error-message'));
            }
            else {
		         if ($this->Permission->save($this->request->data)) {
		             $this->Session->setFlash('<span></span>'.__('Permission Granted.'),
		                         'default',array('class'=>'success-box success-message'));
		              $this->Session->delete('permissionLists');
		             // clear menu cache if existed 
		             $this->_clearMenuCatch ($this->request->data['Permission']['aro_id']);
		             $this->redirect(array('action' => 'index', $this->request->data['Permission']['aco_id']));
		         }
		         else {
		         	$this->Session->setFlash('<span></span>'.__('Add permission failed. Please try again.'), 'default',array('class'=>'error-box error-message'));
		         }
            }
        } else {
            $this->request->data['Permission']['aco_id'] = $aco_id;
            /*$this->data['Permission']['_create'] = 0;
            $this->data['Permission']['_read'] = 0;
            $this->data['Permission']['_update'] = 0;
            $this->data['Permission']['_delete'] = 0;*/
            $this->request->data['Permission']['privilege'] = 1;
        }
        $path = $this->_getAcoPathList($this->request->data['Permission']['aco_id']);
        $aros = $this->_getAroList();
        $aco = $this->Permission->Aco->find('first',
         	array(
         		'conditions' =>
         		array(
         			'Aco.id' => $this->request->data['Permission']['aco_id']
         		),
         		'recursive' => -1
         	)
         );
        if(Configure::read('Developer')) {
		     $roles = ClassRegistry::init('Role')->find('list');
        }
        else {
		     $roles = ClassRegistry::init('Role')->find('list',
		     	array(
		     		'conditions' =>
		     		array(
		     			'Role.id <> ' => ROLE_STUDENT
		     		)



		     	)
		     );
        }
        if($admin_detail['User']['role_id'] == ROLE_DEPARTMENT) {
        	$roles = array();
        	$roles[ROLE_DEPARTMENT] = 'Department';
        	$roles[ROLE_INSTRUCTOR] = 'Instructor';
        }
        else if($admin_detail['User']['role_id'] == ROLE_COLLEGE) {
        	$roles = array();
        	$roles[ROLE_COLLEGE] = 'College';
        	$roles[ROLE_DEPARTMENT] = 'Department';
        	$roles[ROLE_INSTRUCTOR] = 'Instructor';
        }
        $role_ids = array_keys($roles);
        $roles = array(0 => '--- Select Role ---') + $roles;
        $users = array();
        if($role_id && in_array($role_id, $role_ids)) {
        	$options =
        		array(
        			'conditions' =>
        			array(
        				'User.role_id' => $role_id,
        				'Staff.active' => 1,
        				'User.active' => 1,
        			),
        			'fields' =>
        			array(
        				'Staff.full_name',
        				'Staff.college_id',
        				'Staff.department_id',
        				'User.username',
        				'User.id'
        			),
        			'contain' => 
        			array(
        				'User'
        			)
        		);
        	if($admin_detail['User']['role_id'] == ROLE_DEPARTMENT && ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_INSTRUCTOR)) {
        		$options['conditions']['Staff.department_id'] = $admin_detail['Staff'][0]['department_id'];
        	}
        	else if($admin_detail['User']['role_id'] == ROLE_COLLEGE && ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_INSTRUCTOR)) {
        		$colleges = ClassRegistry::init('Department')->find('list',
        			array(
        				'conditions' =>
        				array(
        					'Department.college_id' => $admin_detail['Staff'][0]['college_id']
        				)
        			)
        		);
        		$college_ids = array_keys($colleges);
        		$options['conditions']['Staff.department_id'] = $college_ids;
        	}
        	else if($admin_detail['User']['role_id'] == ROLE_COLLEGE && $role_id == ROLE_COLLEGE) {
        		$options['conditions']['Staff.college_id'] = $admin_detail['Staff'][0]['college_id'];
        	}
        	$users = ClassRegistry::init('Staff')->find('all', $options);
        	$users_f = array();
        	if($role_id == ROLE_COLLEGE) {
        		// $colleges = ClassRegistry::init('College')->find('list');
        		// $college_ids = array_keys($colleges);
        		foreach($users as $user) {
		$collegeName=ClassRegistry::init('College')->field('College.name',array('College.id'=>$user['Staff']['college_id']));
        			if(!isset($users_f[$collegeName])) {
        				$users_f[$collegeName] = array();
        			}
        			$users_f[$collegeName][$this->_getAroId('User', $user['User']['id'])] = $user['Staff']['full_name'].' ('.$user['User']['username'].')';
        		}
        		$users = $users_f;
        	}
        	else if($role_id == ROLE_INSTRUCTOR || $role_id == ROLE_DEPARTMENT) {
        		//$departments = ClassRegistry::init('Department')->find('list');
        		//$department_ids = array_keys($departments);
        		foreach($users as $user) {
		$departmentName=ClassRegistry::init('Department')->field('Department.name',array('Department.id'=>$user['Staff']['department_id']));
        			if(!isset($users_f[$departmentName])) {
        				$users_f[$departmentName] = array();
        			}
        			$users_f[$departmentName][$this->_getAroId('User', $user['User']['id'])] = $user['Staff']['full_name'].' ('.$user['User']['username'].')';
        		}
        		$users = $users_f;
        	}
        	else {
        		foreach($users as $user) {
        			$users_f[$this->_getAroId('User', $user['User']['id'])] = $user['Staff']['full_name'].' ('.$user['User']['username'].')';
        		}
        		$users = $users_f;
        	}
        	if($admin_detail['User']['role_id'] == ROLE_COLLEGE || $admin_detail['User']['role_id'] == ROLE_DEPARTMENT)
        		$users = array(0 => '--- Select user ---') + $users;
        	else
        		$users = array($this->_getAroId('Role', $role_id) => '*** To all users for the selected role ***') + $users;
        }
        else {
        	$users = array(0 => '--- Select Role First ---') + $users;
        }
        $this->set(compact('aros', 'path', 'aco', 'roles', 'users', 'role_id'));
    }
    

     
    function edit($aco_id, $id = null) {
    	$this->_validateIfControllerPermissionCanBeManagedByTheUser(isset($this->request->data['Permission']['id']) ? $this->request->data['Permission']['aco_id'] : $aco_id);
///////////////////////////////////// Permission Checking //////////////////////////////
  		$admin_detail = ClassRegistry::init('User')->find('first',
  			array(
  				'conditions' =>
  				array(
  					'User.id' => Configure::read('User.user')
  				),
  				'contain' => 
  				array(
  					'Staff'
  				)
  			)
  		);
      $permission = $this->Permission->findById(isset($this->request->data['Permission']['id']) ? $this->request->data['Permission']['id'] : $id);
  		if(strcasecmp($permission['Aro']['model'], 'Role') != 0) {
     		$staff = ClassRegistry::init('Staff')->find('first',
     			array(
     				'conditions' =>
     				array(
     					'Staff.user_id' => $permission['Aro']['foreign_key']
     				),
     				'recursive' => -1
     			)
     		);
     		if(($admin_detail['User']['role_id'] == ROLE_DEPARTMENT && $staff['Staff']['department_id'] != $admin_detail['Staff'][0]['department_id']) ||
     			($admin_detail['User']['role_id'] == ROLE_COLLEGE && $staff['Staff']['college_id'] != $admin_detail['Staff'][0]['college_id'])) {
     			$this->Session->setFlash('<span></span>'.__('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.'), 'default',array('class'=>'error-box error-message'));
         	ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>'.$admin_detail['Staff'][0]['first_name'].' '.$admin_detail['Staff'][0]['middle_name'].' '.$admin_detail['Staff'][0]['last_name'].' ('.$admin_detail['User']['username'].')</u> is trying to alter another staff permission (for staff outside of his/her office). Please give appropriate warning.');
         	$this->redirect(array('controller' => 'acos', 'action' => 'index'));
     		}
  		}
///////////////////////////////////////////////////////////////////////////////////////////////
        if (!empty($this->request->data)) {
            $this->request->data['Permission']['_create'] = $this->request->data['Permission']['privilege'];
            $this->request->data['Permission']['_read'] = $this->request->data['Permission']['privilege'];
            $this->request->data['Permission']['_update'] = $this->request->data['Permission']['privilege'];
            $this->request->data['Permission']['_delete'] = $this->request->data['Permission']['privilege'];
            $privilage_exits = $this->Permission->find('count', 
            	array(
            		'conditions' =>
            		array(
		         		'Permission.aco_id' => $this->request->data['Permission']['aco_id'],
		         		'Permission.aro_id' => $this->request->data['Permission']['aro_id'],
		         		'Permission.id <> ' => $this->request->data['Permission']['id']
            		)
            	)
            );
            if($privilage_exits > 0) {
					$this->Session->setFlash('<span></span>'.__('There is already recorded privilege for the selected user/role. Please use edit to apply changes or delete the privilege and re-create.'), 'default',array('class'=>'error-box error-message'));
            }
            else {
		         if ($this->Permission->save($this->request->data)) {
		               $this->Session->setFlash('<span></span>'.__('Permission Updated.'),
		                         'default',array('class'=>'success-box success-message'));
		               $this->Session->delete('permissionLists');
		             // clear menu cache if existed 
		             $this->_clearMenuCatch ($this->request->data['Permission']['aro_id']);
		             $this->redirect(array('action' => 'index', $this->request->data['Permission']['aco_id']));
		         }
			      else {
			      	$this->Session->setFlash('<span></span>'.__('Permission update failed. Please try again.'), 'default',array('class'=>'error-box error-message'));
			      }
			  }
        } else {
            if (empty($permission)) {
                $this->Session->setFlash('<span></span>Invalid Permission ID', 'default',array('class'=>'error-box error-message'));
                $this->redirect(array('action' => 'add', $aco_id));
            } else {
                $this->request->data = $permission;
            }
        }
        $path = $this->_getAcoPathList($this->request->data['Permission']['aco_id']);
        $aros = $this->_getAroList();
        $aco = $this->Permission->Aco->find('first',
         	array(
         		'conditions' =>
         		array(
         			'Aco.id' => $this->request->data['Permission']['aco_id']
         		),
         		'recursive' => -1
         	)
         );
        if(strcasecmp($permission['Aro']['model'], 'Role') == 0) {
        	$role_detail = ClassRegistry::init('Role')->find('first',
        		array(
        			'conditions' =>
        			array(
        				'Role.id' => $permission['Aro']['foreign_key']
        			),
        			'recursive' => -1
        		)
        	);
        	$aro_name = $role_detail['Role']['name'];
        	$aro_type = 'Role';
        }
        else {
        	$user_detail = ClassRegistry::init('User')->find('first',
        		array(
        			'conditions' =>
        			array(
        				'User.id' => $permission['Aro']['foreign_key']
        			),
        			'contain' =>
        			array(
        				'Staff' =>
        				array('full_name')
        			)
        		)
        	);
        	$aro_name = $user_detail['Staff'][0]['full_name'].' ('.$user_detail['User']['username'].')';
        	$aro_type = 'User';
        }
        $this->set(compact('aros', 'path', 'aco', 'aro_name', 'aro_type'));
    }
   

    function delete() 
    {
	$admin_detail = ClassRegistry::init('User')->find('first',
		array(
			'conditions' =>
			array(
				'User.id' => Configure::read('User.user')
			),
			'contain' => 
			array(
				'Staff'
			)
		)
	);
        $delete_count = 0;
        if (!empty($this->request->data['Permission']['delete'])) {
            foreach($this->request->data['Permission']['delete'] as $id => $delete) {
                if ($delete == 1) {
///////////////////////////////////// Permission Checking //////////////////////////////
						$permission = $this->Permission->findById($id);
						$this->_validateIfControllerPermissionCanBeManagedByTheUser($permission['Aco']['id']);
				  		if(strcasecmp($permission['Aro']['model'], 'Role') != 0) {
					  		$staff = ClassRegistry::init('Staff')->find('first',
					  			array(
					  				'conditions' =>
					  				array(
					  					'Staff.user_id' => $permission['Aro']['foreign_key']
					  				),
					  				'recursive' => -1
					  			)
					  		);
					  		if(($admin_detail['User']['role_id'] == ROLE_DEPARTMENT && $staff['Staff']['department_id'] != $admin_detail['Staff'][0]['department_id']) ||
     			($admin_detail['User']['role_id'] == ROLE_COLLEGE && $staff['Staff']['college_id'] != $admin_detail['Staff'][0]['college_id'])) {
					  			$this->Session->setFlash('<span></span>'.__('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.'), 'default',array('class'=>'error-box error-message'));
								ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>'.$admin_detail['Staff'][0]['first_name'].' '.$admin_detail['Staff'][0]['middle_name'].' '.$admin_detail['Staff'][0]['last_name'].' ('.$admin_detail['User']['username'].')</u> is trying to delete another staff permission (for staff outside of his/her office). Please give appropriate warning.');
								$this->redirect(array('controller' => 'acos', 'action' => 'index'));
					  		}
				  		}
//////////////////////////////////////////////////////////////////////////////////////////
                    if ($this->Permission->delete($id)) {
                        $this->_clearMenuCatch($permission['Aro']['id']);
                        $delete_count++;
                    }
                }
            }
        }
        if($delete_count == 0) {
        		$this->Session->setFlash('<span></span>'.__('Please select at least one permission.'), 'default',array('class'=>'error-box error-message'));
        }
        else {
        		$this->Session->setFlash('<span></span>'.$delete_count . ' Permission' . (($delete_count == 1) ? ' was' : 's were') . ' deleted', 'default',array('class'=>'success-box success-message'));
        		$this->Session->delete('permissionLists');
        	}
        $this->redirect(array('action' => 'index', $this->request->data['Permission']['aco_id']));
    }
    
    function _bindModels() {
        $this->Permission->Aro->bindModel(
            array(
                'belongsTo' => array(
                    'Role' => array(
                        'className' => 'Role',
                        'foreignKey' => 'foreign_key',
                        'conditions' => array('Aro.model' => 'Role'),
                    ),
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'foreign_key',
                        'conditions' => array('Aro.model' => 'User'),
                    ),
                )
            )
        );
    }
    
    function _getAroList() {
        // give only their own users to main account holders
        if ($this->role_id !=ROLE_SYSADMIN) {
        $roles = $this->Role->find('list', array('fields' => array('id', 'name'), 'order' => 'Role.name',
        'conditions'=>array('Role.id'=>$this->role_id)));
        
        } else {
             $roles = $this->Role->find('list', array('fields' => array('id', 'name'), 'order' => 'Role.name'));
        }
        
        foreach($roles as $role_id => $role_name) {
           
            $aros[$this->_getAroId('Role', $role_id)] = $role_name;
            $users = $this->User->find('list', array('fields' => array('id', 'username'), 'conditions' => array('role_id' => $role_id), 'order' => 'User.username'));
            foreach($users as $user_id => $username) {
                $aros[$this->_getAroId('User', $user_id)] = '-- ' . $username;
            }
        }
        return $aros;
    }
    
    function _getAroId($model, $foreign_key) {
        return $this->Permission->Aro->field('id', array('model' => $model, 'foreign_key' => $foreign_key));
    }
    
    function _getAcoPathList($aco_id) {
        $_path = $this->Permission->Aco->getPath($aco_id);
        foreach($_path as $i) {
            $path[$i['Aco']['id']] = $i['Aco']['alias'];
        }
        return $path;
    }

     private function _validateIfControllerPermissionCanBeManagedByTheUser($aco_id = null) {
  		$admin_detail = ClassRegistry::init('User')->find('first',
  			array(
  				'conditions' =>
  				array(
  					'User.id' => Configure::read('User.user')
  				),
  				'contain' => 
  				array(
  					'Staff'
  				)
  			)
  		);
		$aco = $this->Permission->Aco->find('first',
			array(
				'conditions' =>
				array(
					'Aco.id' => $aco_id
				),
				'recursive' => -1
			)
		);
		//If it is 1st level controller (under master controller)
		if($aco['Aco']['parent_id'] == 1) {
			$actions = $this->Permission->Aco->find('list',
				array(
					'conditions' =>
					array(
						'Aco.parent_id' => $aco['Aco']['id']
					),
					'fields' => 
					array(
						'Aco.admin'
					)
				)
			);
			$actions_admins = array_count_values($actions);
			$actions_admin = array_keys($actions_admins);
			$actions_admin = $actions_admin[0];
			if(count($actions_admins) > 1 || $actions_admin != $admin_detail['User']['role_id']) {
				$this->Session->setFlash('<span></span>'.__('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.', true), 'default',array('class'=>'error-box error-message'));
         	ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>'.$admin_detail['Staff'][0]['first_name'].' '.$admin_detail['Staff'][0]['middle_name'].' '.$admin_detail['Staff'][0]['last_name'].' ('.$admin_detail['User']['username'].')</u> is trying to break system security and gain access to shared permission management system. Please give appropriate warning.');
         	$this->redirect(array('controller' => 'acos', 'action' => 'index'));
			}
		}
	}

       
	function _clearMenuCatch ($aro_id) {
		     $aroDetails = $this->Permission->Aro->find('first',
		 	array(
		 		'conditions' =>
		 		array(
		 			'Aro.id' => $aro_id
		 		),
		 		'recursive' => -1
		 	)
		 );
		 $aro=ClassRegistry::init('User')->find('first',
		    array(
		 		'conditions' =>
		 		array(
		 			'User.id' => $aroDetails['Aro']['foreign_key']
		 		),
		 		'recursive' => -1
		 	)
		 );
		
		 if (empty($aro)) {
		    $userLists=ClassRegistry::init('User')->find('all',
		    array(
		 		'conditions' =>
		 		array(
		 			'User.role_id' => $aroDetails['Aro']['foreign_key']
		 		),
		 		'recursive' => -1
		 	)
		   );
		  if (!empty($userLists)) {
		        foreach($userLists as $key=>$value) {
		         $aroKey = key($value) . $value[key($value)]['id'];
		         $cacheKey = $aroKey . '_' .'menu_storage'; 
		         Cache::delete($cacheKey, 'menu_component');
		       }
		  }
		 
		  
		   
		   
		 } else {
		     $aroKey = $aro;	        
			     if (is_array($aro)) {
				     $aroKey = key($aro) . $aro[key($aro)]['id'];
			     }
			     $cacheKey = $aroKey . '_' .'menu_storage'; 		
			 if (Cache::delete($cacheKey, 'menu_component')) {
			
			     } else {
				$this->log('Menu Component - Could not delete Menu cache.');
			     }
		 }
	}
}
?>
