<?php
namespace Acls\Controller;

use Acls\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class PermissionsController extends AppController
{
    /**
     * Initialization method
     */
    public function initialize(): void
    {
        parent::initialize();

        // ✅ Load Components
        $this->loadComponent('CakeDC/ACL.Acl');
        $this->loadComponent('Flash');
        $this->loadComponent('RequestHandler');

        // ✅ Load Models
        $this->Permissions = TableRegistry::getTableLocator()->get('Permissions');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->Roles = TableRegistry::getTableLocator()->get('Roles');
    }

    /**
     * beforeFilter callback
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * beforeRender callback
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        $perms = [
            '1' => 'Allow',
            '-1' => 'Deny',
        ];
        $this->set(compact('perms'));
    }

    /**
     * Index method
     */
    public function index($aco_id)
    {
        $admin_detail = $this->Users->get($this->request->getSession()->read('User.user'), [
            'contain' => ['Staff']
        ]);

        $this->_validateIfControllerPermissionCanBeManagedByTheUser($aco_id);

        $aco = $this->Permissions->Acos->get($aco_id);

        $path = $this->_getAcoPathList($aco_id);

        $permissions = $this->Permissions->find('all', [
            'conditions' => ['aco_id' => $aco_id]
        ])->toArray();

        foreach ($permissions as $key => $i) {
            $path2 = $this->_getAcoPathList($i->aco_id);
            $permissions[$key]->path = implode('/', $path2);
        }

        $users = $this->Users->find('list', [
            'conditions' => ['Users.role_id <>' => ROLE_STUDENT],
            'fields' => ['id', 'username'],
            'order' => ['username' => 'ASC']
        ])->toArray();

        $roles = $this->Roles->find('list', ['order' => 'name'])->toArray();

        $this->set(compact('permissions', 'aco_id', 'path', 'users', 'roles', 'aco'));
    }

    /**
     * Add method
     */
    public function add($aco_id = null, $role_id = null)
    {
        $this->_validateIfControllerPermissionCanBeManagedByTheUser($aco_id);

        $admin_detail = $this->Users->get($this->request->getSession()->read('User.user'), [
            'contain' => ['Staff']
        ]);

        $permission = $this->Permissions->newEmptyEntity();

        if ($this->request->is('post')) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->getData());

            $permission->_create = $permission->privilege;
            $permission->_read = $permission->privilege;
            $permission->_update = $permission->privilege;
            $permission->_delete = $permission->privilege;

            $privilage_exists = $this->Permissions->exists([
                'aco_id' => $permission->aco_id,
                'aro_id' => $permission->aro_id
            ]);

            if (empty($permission->aro_id)) {
                $this->Flash->error('Please select user for whom you want to give privilege.');
            } elseif ($privilage_exists) {
                $this->Flash->error('There is already recorded privilege for the selected user/role. Please use edit to apply changes or delete the privilege and re-create.');
            } else {
                if ($this->Permissions->save($permission)) {
                    $this->Flash->success('Permission Granted.');
                    $this->redirect(['action' => 'index', $permission->aco_id]);
                } else {
                    $this->Flash->error('Add permission failed. Please try again.');
                }
            }
        } else {
            $permission->aco_id = $aco_id;
            $permission->privilege = 1;
        }

        $path = $this->_getAcoPathList($permission->aco_id);
        $aros = $this->_getAroList();
        $aco = $this->Permissions->Acos->get($permission->aco_id);

        $roles = $this->Roles->find('list')->toArray();

        $this->set(compact('aros', 'path', 'aco', 'roles', 'role_id'));
    }

    /**
     * Edit method
     */
    public function edit($aco_id, $id = null)
    {
        $this->_validateIfControllerPermissionCanBeManagedByTheUser($aco_id);

        $permission = $this->Permissions->get($id);

        if ($this->request->is(['post', 'put'])) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->getData());

            $permission->_create = $permission->privilege;
            $permission->_read = $permission->privilege;
            $permission->_update = $permission->privilege;
            $permission->_delete = $permission->privilege;

            $privilage_exists = $this->Permissions->exists([
                'aco_id' => $permission->aco_id,
                'aro_id' => $permission->aro_id,
                'id <>' => $permission->id
            ]);

            if ($privilage_exists) {
                $this->Flash->error('There is already recorded privilege for the selected user/role. Please use edit to apply changes or delete the privilege and re-create.');
            } else {
                if ($this->Permissions->save($permission)) {
                    $this->Flash->success('Permission Updated.');
                    $this->redirect(['action' => 'index', $permission->aco_id]);
                } else {
                    $this->Flash->error('Permission update failed. Please try again.');
                }
            }
        }

        $path = $this->_getAcoPathList($permission->aco_id);
        $aros = $this->_getAroList();
        $aco = $this->Permissions->Acos->get($permission->aco_id);

        $this->set(compact('aros', 'path', 'aco', 'permission'));
    }

    /**
     * Delete method
     */
    public function delete()
    {
        $delete_count = 0;

        if ($this->request->is('post')) {
            foreach ($this->request->getData('Permission.delete') as $id => $delete) {
                if ($delete == 1) {
                    $permission = $this->Permissions->get($id);
                    if ($this->Permissions->delete($permission)) {
                        $delete_count++;
                    }
                }
            }
        }

        if ($delete_count == 0) {
            $this->Flash->error('Please select at least one permission.');
        } else {
            $this->Flash->success($delete_count . ' Permission' . ($delete_count == 1 ? ' was' : 's were') . ' deleted');
        }
        $this->redirect(['action' => 'index', $this->request->getData('Permission.aco_id')]);
    }

    /**
     * Get ARO list
     */
    protected function _getAroList()
    {
        $roles = $this->Roles->find('list', ['fields' => ['id', 'name'], 'order' => 'name'])->toArray();

        $developer_mode_enabled = Configure::read('Developer');
        if (!$developer_mode_enabled && isset($roles[ROLE_STUDENT])) {
            unset($roles[ROLE_STUDENT]);
        }

        $aros = [];
        foreach ($roles as $role_id => $role_name) {
            $aros[$this->_getAroId('Role', $role_id)] = $role_name;

            $users = $this->Users->find('list', [
                'fields' => ['id', 'username'],
                'conditions' => ['role_id' => $role_id],
                'order' => 'username'
            ])->toArray();

            foreach ($users as $user_id => $username) {
                $aros[$this->_getAroId('User', $user_id)] = '-- ' . $username;
            }
        }

        return $aros;
    }

    /**
     * Get ARO ID
     */
    protected function _getAroId($model, $foreign_key)
    {
        return $this->Permissions->Aros->find()
            ->where(['model' => $model, 'foreign_key' => $foreign_key])
            ->select(['id'])
            ->first()
            ->id ?? null;
    }

    /**
     * Get ACO Path List
     */
    protected function _getAcoPathList($aco_id)
    {
        $_path = $this->Permissions->Acos->find('path', ['for' => $aco_id])->toArray();
        $path = Hash::extract($_path, '{n}.alias');
        return $path;
    }

    /**
     * Validate if permission can be managed by the user
     */
    private function _validateIfControllerPermissionCanBeManagedByTheUser($aco_id = null)
    {
        $admin_detail = $this->Users->get($this->request->getSession()->read('User.user'), [
            'contain' => ['Staff']
        ]);

        $aco = $this->Permissions->Acos->get($aco_id);

        if ($aco->parent_id == 1) {
            $actions = $this->Permissions->Acos->find('list', [
                'conditions' => ['parent_id' => $aco->id],
                'fields' => ['admin']
            ])->toArray();

            $actions_admins = array_count_values($actions);
            $actions_admin = array_keys($actions_admins);
            $actions_admin = $actions_admin[0];

            if (count($actions_admins) > 1 || $actions_admin != $admin_detail->role_id) {
                $this->Flash->error('You are trying to break system security. Your action is logged and reported to the system administrators. Do not try this action again otherwise your account will be closed.');
                $this->redirect(['controller' => 'acos', 'action' => 'index']);
            }
        }
    }
}
