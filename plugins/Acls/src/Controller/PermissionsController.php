<?php
namespace Acls\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

class PermissionsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Permissions');
        $this->loadModel('Users');
        $this->loadModel('Roles');
        $this->loadModel('Staffs');
        $this->loadModel('Acos');
        $this->loadModel('Aros');
        $this->loadComponent('Flash');

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);


    }


    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        $perms = [
            '1' => 'Allow',
            // '0' => 'Inherit',
            '-1' => 'Deny',
        ];
        $this->set(compact('perms'));
    }


    public function index($acoId)
    {
        $session = $this->request->getSession();
        $userId = $session->read('Auth.User.id');
        $roleId = $session->read('Auth.User.role_id');

        $adminDetail = $this->Users->find()
            ->contain(['Staffs'])
            ->where(['Users.id' => $userId])
            ->first();

        $this->_validateIfControllerPermissionCanBeManagedByTheUser($acoId);

        $aco = $this->Acos->find()
            ->where(['id' => $acoId])
            ->first();

        if (!$acoId || !$aco) {
            $this->Flash->error(__('Invalid task ID.'));
            return $this->redirect(['controller' => 'Acos', 'action' => 'index', 1]);
        }

        $path = $this->_getAcoPathList($acoId);

        $permissions = $this->Permissions->find()
            ->contain(['Aros'])
            ->where(['aco_id' => $acoId])
            ->toArray();

        foreach ($permissions as &$p) {
            $p['Permission']['path'] = implode('/', $this->_getAcoPathList($p['aco_id']));
        }

        $users = $this->Users->find('list', [
            'conditions' => ['Users.role_id <>' => ROLE_STUDENT],
            'keyField' => 'id',
            'valueField' => 'username',
            'order' => ['username' => 'ASC']
        ])->toArray();

        if (in_array($roleId, [ROLE_COLLEGE, ROLE_DEPARTMENT])) {
            foreach ($permissions as $key => $perm) {
                if (strcasecmp($perm['Aros']['model'], 'Roles') !== 0) {
                    $staff = $this->Staffs->find()
                        ->where(['user_id' => $perm['Aros']['foreign_key']])
                        ->first();

                    $adminStaff = $adminDetail->Staff[0] ?? null;
                    if (
                        ($roleId == ROLE_DEPARTMENT && $staff->department_id != $adminStaff->department_id) ||
                        ($roleId == ROLE_COLLEGE && $staff->college_id != $adminStaff->college_id)
                    ) {
                        unset($permissions[$key]);
                    }
                }
            }
        }

        $roles = $this->Roles->find('list', ['order' => ['name' => 'ASC']])->toArray();

        $this->set(compact('permissions', 'acoId', 'path', 'users', 'roles', 'aco'));
    }

    public function add($acoId = null, $roleId = null)
    {
        $this->_validateIfControllerPermissionCanBeManagedByTheUser($acoId);

        $session = $this->request->getSession();
        $userId = $session->read('Auth.User.id');
        $userRoleId = $session->read('Auth.User.role_id');

        $adminDetail = $this->Users->find()
            ->contain(['Staffs'])
            ->where(['Users.id' => $userId])
            ->first();

        if ($this->request->getData('Permission.role_id')) {
            $roleId = $this->request->getData('Permission.role_id');
        }

        $acoIdForCheck = $this->request->getData('Permission.aco_id') ?? $acoId;
        $aco = $this->Acos->get($acoIdForCheck);
        $acoAdmins = explode(',', $aco->admin);

        if ($userRoleId != ROLE_SYSADMIN && $aco->parent_id != 1 && !in_array($userRoleId, $acoAdmins)) {
            $this->Flash->error(__('You are trying to break system security.'));
            return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
        }

        $permission = $this->Permissions->newEntity();
        $requestData = $this->request->getData();

        if (!empty($requestData) && $this->request->is(['post', 'put'])) {

            $priv = $requestData['Permission']['privilege'];
            $requestData['Permission']['_create'] = $priv;
            $requestData['Permission']['_read'] = $priv;
            $requestData['Permission']['_update'] = $priv;
            $requestData['Permission']['_delete'] = $priv;

            $exists = $this->Permissions->find()
                ->where([
                    'aco_id' => $requestData['Permission']['aco_id'],
                    'aro_id' => $requestData['Permission']['aro_id']
                ])
                ->count();

            if (empty($requestData['Permission']['aro_id'])) {
                $this->Flash->error(__('Please select user for whom you want to give privilege.'));
            } elseif ($exists > 0) {
                $this->Flash->error(__('Privilege already exists. Use edit or delete.'));
            } else {
                $permission = $this->Permissions->patchEntity($permission, $requestData['Permission']);
                if ($this->Permissions->save($permission)) {
                    $session->delete('permissionLists');
                    $this->_clearMenuCatch($requestData['Permission']['aro_id']);
                    $this->Flash->success(__('Permission Granted.'));
                    return $this->redirect(['action' => 'index', $requestData['Permission']['aco_id']]);
                }
                $this->Flash->error(__('Add permission failed. Please try again.'));
            }
        } else {
            $permission->aco_id = $acoId;
            $permission->privilege = 1;
        }

        $path = $this->_getAcoPathList($permission->aco_id);
        $aros = $this->_getAroList();

        $aco = $this->Acos->get($permission->aco_id);

        $roles = ($userRoleId === ROLE_SYSADMIN || Configure::read('Developer'))
            ? $this->Roles->find('list')->toArray()
            : $this->Roles->find('list', ['conditions' => ['Roles.id !=' => ROLE_STUDENT]])->toArray();

        if ($userRoleId === ROLE_DEPARTMENT) {
            $roles = [
                ROLE_DEPARTMENT => 'Department',
                ROLE_INSTRUCTOR => 'Instructor'
            ];
        } elseif ($userRoleId === ROLE_COLLEGE) {
            $roles = [
                ROLE_COLLEGE => 'College',
                ROLE_DEPARTMENT => 'Department',
                ROLE_INSTRUCTOR => 'Instructor'
            ];
        }

        $roleIds = array_keys($roles);
        $roles = [0 => '[ Select Role ]'] + $roles;
        $users = [];

        if ($roleId && in_array($roleId, $roleIds)) {
            $staffQuery = $this->Staffs->find()
                ->contain(['Users'])
                ->where([
                    'Users.role_id' => $roleId,
                    'Users.active' => 1,
                    'Staffs.active' => 1
                ]);

            if ($userRoleId === ROLE_DEPARTMENT && in_array($roleId, [ROLE_DEPARTMENT, ROLE_INSTRUCTOR])) {
                $staffQuery->where(['Staffs.department_id' => $adminDetail->staffs[0]->department_id]);
            } elseif ($userRoleId === ROLE_COLLEGE && in_array($roleId, [ROLE_DEPARTMENT, ROLE_INSTRUCTOR])) {
                $collegeDepts = $this->Departments->find('list', [
                        'conditions' => ['college_id' => $adminDetail->staffs[0]->college_id],
                        'keyField' => 'id', 'valueField' => 'id']
                )->toArray();
                $staffQuery->where(['Staffs.department_id IN' => array_values($collegeDepts)]);
            } elseif ($userRoleId === ROLE_COLLEGE && $roleId == ROLE_COLLEGE) {
                $staffQuery->where(['Staffs.college_id' => $adminDetail->staffs[0]->college_id]);
            }

            $groupedUsers = [];
            foreach ($staffQuery->all() as $staff) {
                $aroId = $this->_getAroId('Users', $staff->user->id);
                $label = $staff->full_name . ' (' . $staff->user->username . ')';
                $key = ($roleId === ROLE_COLLEGE) ? $staff->college_id : (($roleId === ROLE_DEPARTMENT || $roleId === ROLE_INSTRUCTOR) ? $staff->department_id : null);

                if ($key !== null) {
                    $group = $this->Colleges->get($key)->name ?? $this->Departments->get($key)->name ?? 'Other';
                    $groupedUsers[$group][$aroId] = $label;
                } else {
                    $groupedUsers[$aroId] = $label;
                }
            }

            $users = ($userRoleId === ROLE_COLLEGE || $userRoleId === ROLE_DEPARTMENT)
                ? [0 => '[ Select User ]'] + $groupedUsers
                : [$this->_getAroId('Roles', $roleId) => '*** To all users for the selected role ***'] + $groupedUsers;
        } else {
            $users = [0 => '[ Select Role First ]'];
        }

        $this->set(compact('aros', 'path', 'aco', 'roles', 'users', 'roleId', 'permission'));
    }

    public function edit($acoId, $id = null)
    {
        $request = $this->request;
        $data = $request->getData();
        $targetAcoId = $data['Permission']['aco_id'] ?? $acoId;
        $this->_validateIfControllerPermissionCanBeManagedByTheUser($targetAcoId);

        $session = $request->getSession();
        $userId = $session->read('Auth.User.id');
        $userRoleId = $session->read('Auth.User.role_id');

        $adminDetail = $this->Users->find()
            ->contain(['Staffs'])
            ->where(['Users.id' => $userId])
            ->first();

        $permissionId = $data['Permission']['id'] ?? $id;
        $permission = $this->Permissions->get($permissionId, ['contain' => ['Aros']]);

        if (strcasecmp($permission->aro->model, 'Roles') !== 0) {
            $staff = $this->Staffs->find()
                ->where(['user_id' => $permission->aro->foreign_key])
                ->first();

            if (($userRoleId === ROLE_DEPARTMENT && $staff->department_id !== $adminDetail->staffs[0]->department_id) ||
                ($userRoleId === ROLE_COLLEGE && $staff->college_id !== $adminDetail->staffs[0]->college_id)) {
                $this->Flash->error(__('Security violation.'));
                return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
            }
        }

        if ($request->is(['patch', 'post', 'put'])) {
            $data['Permission']['_create'] = $data['Permission']['privilege'];
            $data['Permission']['_read'] = $data['Permission']['privilege'];
            $data['Permission']['_update'] = $data['Permission']['privilege'];
            $data['Permission']['_delete'] = $data['Permission']['privilege'];

            $exists = $this->Permissions->find()
                ->where([
                    'aco_id' => $data['Permission']['aco_id'],
                    'aro_id' => $data['Permission']['aro_id'],
                    'id !=' => $data['Permission']['id']
                ])->count();

            if ($exists > 0) {
                $this->Flash->error(__('Privilege already exists. Use edit or delete.'));
            } else {
                $permission = $this->Permissions->patchEntity($permission, $data['Permission']);
                if ($this->Permissions->save($permission)) {
                    $session->delete('permissionLists');
                    $this->_clearMenuCatch($permission->aro_id);
                    $this->Flash->success(__('Permission Updated.'));
                    return $this->redirect(['action' => 'index', $permission->aco_id]);
                }
                $this->Flash->error(__('Permission update failed.'));
            }
        }

        if (empty($permission)) {
            $this->Flash->error(__('Invalid Permission ID'));
            return $this->redirect(['action' => 'add', $acoId]);
        }

        $path = $this->_getAcoPathList($permission->aco_id);
        $aros = $this->_getAroList();
        $aco = $this->Acos->get($permission->aco_id);

        if (strcasecmp($permission->aro->model, 'Roles') === 0) {
            $role = $this->Roles->get($permission->aro->foreign_key);
            $aroName = $role->name;
            $aroType = 'Roles';
        } else {
            $user = $this->Users->find()
                ->contain(['Staffs'])
                ->where(['Users.id' => $permission->aro->foreign_key])
                ->first();
            $aroName = $user->Staff[0]->full_name . ' (' . $user->username . ')';
            $aroType = 'Users';
        }

        $this->set(compact('aros', 'path', 'aco', 'aroName', 'aroType', 'permission'));
    }

    public function delete()
    {
        $session = $this->request->getSession();
        $userId = $session->read('Auth.User.id');
        $userRoleId = $session->read('Auth.User.role_id');

        $adminDetail = $this->Users->find()
            ->contain(['Staffs'])
            ->where(['Users.id' => $userId])
            ->first();

        $deleteCount = 0;
        $data = $this->request->getData('Permission.delete');

        if (!empty($data)) {
            foreach ($data as $id => $delete) {
                if ($delete == 1) {
                    $permission = $this->Permissions->get($id, ['contain' => ['Aros', 'Acos']]);
                    $this->_validateIfControllerPermissionCanBeManagedByTheUser($permission->aco->id);

                    if (strcasecmp($permission->aro->model, 'Roles') !== 0) {
                        $staff = $this->Staffs->find()
                            ->where(['user_id' => $permission->aro->foreign_key])
                            ->first();

                        if (
                            ($userRoleId == ROLE_DEPARTMENT && $staff->department_id != $adminDetail->staffs[0]->department_id) ||
                            ($userRoleId == ROLE_COLLEGE && $staff->college_id != $adminDetail->staffs[0]->college_id)
                        ) {
                            $this->Flash->error(__('Security violation.'));
                            return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
                        }
                    } elseif (strcasecmp($permission->aro->model, 'Roles') === 0
                        && !in_array($userRoleId, [ROLE_SYSADMIN, ROLE_REGISTRAR])) {
                        $this->Flash->error(__('You are not allowed to delete Role Based Permissions.'));
                        return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
                    }

                    if ($this->Permissions->delete($permission)) {
                        $this->_clearMenuCatch($permission->aro->id);
                        $deleteCount++;
                    }
                }
            }
        }

        if ($deleteCount === 0) {
            $this->Flash->error(__('Please select at least one permission.'));
        } else {
            $this->Flash->success(__('{0} Permission{1} deleted', $deleteCount, $deleteCount === 1 ? '' : 's'));
           // $session->delete('permissionLists');
        }


       return $this->redirect($this->referer());


      //  return $this->redirect(['action' => 'index', $this->request->getData('aco_id')]);
    }




    protected function _bindModels(): void
    {
        $this->Permissions->Aro->belongsTo('Roles', [
            'className' => 'Roles',
            'foreignKey' => 'foreign_key',
            'conditions' => ['Aro.model' => 'Roles'],
            'joinType' => 'INNER'
        ]);

        $this->Permissions->Aro->belongsTo('Users', [
            'className' => 'Users',
            'foreignKey' => 'foreign_key',
            'conditions' => ['Aro.model' => 'Users'],
            'joinType' => 'INNER'
        ]);
    }

    protected function _getAroList()
    {
        $aros = [];
        $roleId = $this->request->getSession()->read('Auth.User.role_id');

        if ($roleId !== ROLE_SYSADMIN) {
            $roles = $this->Roles->find('list')
                ->where(['id' => $roleId])
                ->order(['name' => 'ASC'])
                ->toArray();
        } else {
            $roles = $this->Roles->find('list')
                ->order(['name' => 'ASC'])
                ->toArray();
        }

        if (!Configure::read('Developer') && isset($roles[ROLE_STUDENT])) {
            unset($roles[ROLE_STUDENT]);
        }

        foreach ($roles as $rId => $roleName) {
            $aros[$this->_getAroId('Roles', $rId)] = $roleName;
            $users = $this->Users->find('list', [
                'fields' => ['id', 'username'],
                'conditions' => ['role_id' => $rId],
                'order' => ['username' => 'ASC']
            ])->toArray();

            foreach ($users as $uId => $username) {
                $aros[$this->_getAroId('Users', $uId)] = '-- ' . $username;
            }
        }

        return $aros;
    }

    protected function _getAroId($model,  $foreignKey)
    {
        return $this->Permissions->Aros->find()
            ->select(['id'])
            ->where(['model' => $model, 'foreign_key' => $foreignKey])
            ->first()['id'] ?? null;
    }

    protected function _getAcoPathList($acoId)
    {
        $path = [];
        $acoPath = $this->Acos->find('path', ['for' => $acoId])->toArray();
        foreach ($acoPath as $aco) {
            $path[$aco->id] = $aco->alias;
        }
        return $path;
    }

    protected function _validateIfControllerPermissionCanBeManagedByTheUser($acoId = null)
    {
        $adminDetail = $this->Users->find()
            ->contain(['Staffs'])
            ->where(['Users.id' => $this->request->getSession()->read('Auth.User.id')])
            ->first();

        $aco = $this->Acos->find()
            ->where(['id' => $acoId])
            ->first();

        if ($aco && $aco->parent_id == 1) {
            $actions = $this->Acos->find('list', [
                'keyField' => 'id',
                'valueField' => 'admin'
            ])->where(['parent_id' => $aco->id])->toArray();

            $counts = array_count_values($actions);
            $admin = array_key_first($counts);

            if (count($counts) > 1 || $admin != $adminDetail->role_id) {
                $this->Flash->error(__('You are trying to break system security. Your action is logged.'));
                // Skipping actual email or alert send logic here
                return $this->redirect(['controller' => 'Acos', 'action' => 'index']);
            }
        }
    }

    protected function _clearMenuCatch($aroId)
    {
        $aro = $this->Permissions->Aros->get($aroId);

        $user = $this->Users->find()
            ->where(['id' => $aro->foreign_key])
            ->first();

        if (!$user) {
            $userList = $this->Users->find()
                ->where(['role_id' => $aro->foreign_key])
                ->toArray();

            foreach ($userList as $u) {
                $cacheKey = 'User' . $u->id . '_menu_storage';
                \Cake\Cache\Cache::delete($cacheKey, 'menu_component');
            }
        } else {
            $cacheKey = 'User' . $user->id . '_menu_storage';
            if (!\Cake\Cache\Cache::delete($cacheKey, 'menu_component')) {
                $this->log('Menu Component - Could not delete Menu cache.');
            }
        }
    }

}
