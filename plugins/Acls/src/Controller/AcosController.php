<?php
namespace Acls\Controller;
use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;

class AcosController extends AppController
{
    protected $rootId;

    /**
     * Initializes controller components and loads required models.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Acls.AcoBuilder');
        $this->loadModel('Permissions');
        $this->loadModel('Acls.Acos');
        $this->loadModel('Aros');
        $this->loadModel('Users');
        $this->loadModel('Roles');
        $this->loadModel('Staffs');
       // $this->Auth->allow(['index']);
    }
    /**
     * Loads the root ACO ID for the controller tree.
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->rootId = $this->Acos->find()
            ->select(['id'])
            ->where(['alias' => substr('controllers/', 0, -1)])
            ->first()
            ->id ?? null;
    }

    public function validateAco(array $data): bool
    {
        $errors = [];
        if (empty($data['Model']['alias'])) {
            $errors['Acl']['alias'] = 'Alias is required';
        }
        if (empty($errors)) {
            return true;
        } else {
            $this->set(compact('errors'));
            return false;
        }
    }

    /**
     * Displays a filtered list of ACOs (Access Control Objects) based on parent node and user role.
     *
     * - Filters children ACOs by current user's role and permissions.
     * - Calculates number of permitted actions for each ACO.
     * - Excludes ACL entries defined in configuration.
     * - Supports tree path and dynamic permission depth metrics.
     *
     * @param int|null $parentId The ID of the parent ACO node. Defaults to the root node.
     * @return void
     */
    public function index($parentId = null)
    {
        $session = $this->request->getSession();

        $userId = $session->read('Auth.User.id');
        $roleId = $session->read('Auth.User.role_id');


        $adminDetail = $this->Users->find()
            ->contain(['Staffs'])
            ->where(['Users.id' => $userId])
            ->first();

        $parentId = $parentId ?? $this->rootId;
        $path = $parentId !== $this->rootId ? $this->_getAcoPathList($parentId) : [];

        $actionPath = rtrim((string)$this->Auth->getConfig('actionPath'), '/');

        $acos = $this->Acos->find()
            ->where(['parent_id' => $parentId])
            ->order(['`Acos`.`order`' => 'ASC'])
            ->enableHydration(false)
            ->toArray();

        if ($parentId > 1) {
            foreach ($acos as $k => $v) {
                $adminIds = explode(',', (string)$v['admin']);
                if (!in_array($roleId, $adminIds)) {
                    unset($acos[$k]);
                }
            }
        }

        if ($parentId == 1) {
            foreach ($acos as $acoKey => &$aco) {
                $children = $this->Acos->find()
                    ->where(['parent_id' => $aco['id']])
                    ->order(['`Acos`.`order`' => 'ASC'])
                    ->enableHydration(false)
                    ->toArray();

                $count = 0;
                foreach ($children as $child) {
                    $adminIds = explode(',', (string)$child['admin']);
                    if (in_array($roleId, $adminIds)) {
                        $count++;
                    }
                }

                if ($count === 0) {
                    unset($acos[$acoKey]);
                    continue;
                }

                $aco['num_children'] = $count;

                $adminList = $this->Acos->find('list', [
                    'keyField' => 'id', 'valueField' => 'admin',
                    'conditions' => ['parent_id' => $aco['id']]
                ])->toArray();

                $counts = array_count_values($adminList);
                $firstKey = array_key_first($counts);
                $aco['remove_permission'] = count($counts) > 1 || $firstKey != $adminDetail->role_id;
            }
        }

        foreach ($acos as &$v) {
            $v['num_children'] = $v['num_children'] ?? 0;

            $descendants = $this->Acos->find()
                ->where(['parent_id' => $v['id']])
                ->order(['lft'])
                ->enableHydration(false)
                ->toArray();

            $ids = collection($descendants)->extract('id')->toList();

            if (!empty($ids)) {
                $userAroId = $this->Aros->find()
                    ->select(['id'])
                    ->where(['foreign_key' => $userId])
                    ->first()['id'] ?? null;

                $query = "SELECT COUNT(*) as cont FROM aros_acos WHERE (aro_id = :roleId OR aro_id = :userAroId) AND aco_id IN (" . implode(',', $ids) . ") AND (_create > 0 OR _read > 0 OR _update > 0 OR _delete > 0)";

                $connection = ConnectionManager::get('default');
                $result = $connection->execute($query, compact('roleId', 'userAroId'))->fetch('assoc');
                $v['num_permitted_actions_controlloer'] = $result['cont'] ?? 0;
            } else {
                $permissions = $this->Permissions->find()
                    ->contain(['Aros'])
                    ->where(['aco_id' => $v['id']])
                    ->enableHydration(false)
                    ->toArray();

                foreach ($permissions as &$p) {
                    $path = $this->_getAcoPathList($p['aco_id']);
                    $p['Permission']['path'] = implode('/', $path);
                }

                if (in_array($roleId, [ROLE_COLLEGE, ROLE_DEPARTMENT])) {
                    foreach ($permissions as $pKey => $perm) {
                        if (strcasecmp($perm['Aro']['model'], 'Role') !== 0) {
                            $staff = $this->Staffs->find()
                                ->where(['user_id' => $perm['Aro']['foreign_key']])
                                ->first();

                            if ((defined('ROLE_DEPARTMENT') && $roleId == ROLE_DEPARTMENT && $staff->department_id != $adminDetail->staffs[0]->department_id) ||
                                (defined('ROLE_COLLEGE') && $roleId == ROLE_COLLEGE && $staff->college_id != $adminDetail->staffs[0]->college_id)) {
                                unset($permissions[$pKey]);
                            }
                        }
                    }
                }

                $v['num_permitted_actions_controlloer'] = count($permissions);
            }
        }

        $excluded = (array)configure::read('ACL.excludedACL');
        foreach ($excluded as $entry) {
            $parts = explode(DS, $entry);
            foreach ($acos as $key => $aco) {
                if ((count($parts) > 1 && $parts[0] === '*' && strcasecmp($parts[1], $aco['alias']) === 0) ||
                    ($aco['parent_id'] == 1 && count($parts) === 1 && strcasecmp($entry, $aco['alias']) === 0)) {
                    unset($acos[$key]);
                } elseif ($aco['parent_id'] != 1 && count($parts) > 1) {
                    $parent = $this->Acos->find()->where(['id' => $aco['parent_id']])->first();
                    if (!empty($parent) && strcasecmp($parent->alias . DS . $aco['alias'], $entry) === 0) {
                        unset($acos[$key]);
                    }
                }
            }
        }

        $aco = $this->Acos->get($parentId);



        $this->set(compact('acos', 'aco', 'path', 'actionPath', 'parentId'));
    }

    /**
     * Edit or update an ACO node.
     *
     * Handles both GET (load form) and POST/PUT (save data) requests.
     *
     * @param int|null $id The ACO ID being edited.
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {

        $isPost = $this->request->is(['post', 'put']);

        if ($isPost) {

            $data = $this->request->getData();

            if (empty($data['parent_id'])) {
                $data['parent_id'] = $this->rootId;
            }

            $data['admin'] = !empty($data['admin']) ? implode(',', $data['admin']) : '';
            $aco = $this->Acos->get($data['id']);
            $aco = $this->Acos->patchEntity($aco, $data);

            if ($this->Acos->save($aco)) {
                $this->Flash->success(__('ACO Updated'));
                return $this->redirect(['action' => 'index', $aco->parent_id]);
            }

            $this->Flash->error(__('Unable to update ACO. Please try again.'));
        } else {
            $aco = $this->Acos->get($id);
            if (!$aco) {
                $this->Flash->error(__('Invalid ACO ID'));
                return $this->redirect(['action' => 'add']);
            }
            $aco->admin = explode(',', $aco->admin);
        }

        $parentAco = $this->Acos->get($aco->parent_id);
        $aco['parent_aco'] = $parentAco;

        $parents = $this->_getParentsList();
        $roles = $this->Roles->find('list')->toArray();

        $this->set(compact('parents', 'aco', 'roles'));
    }

    /**
     * Delete selected ACO nodes.
     *
     * Processes POST data from a multi-select delete form.
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        $data = $this->request->getData('Aco');
        $deleteCount = 0;

        if (!empty($data['delete'])) {
            foreach ($data['delete'] as $id => $shouldDelete) {
                if ($shouldDelete && $this->Acos->delete($this->Acos->get($id))) {
                    $deleteCount++;
                }
            }
        }

        $this->Flash->success(__($deleteCount . ' ACO' . ($deleteCount === 1 ? ' was' : 's were') . ' deleted'));

        return $this->redirect($this->referer());
        //return $this->redirect(['action' => 'index', $data['parent_id'] ?? $this->rootId]);
    }

    /**
     * Rebuilds the ACO tree from the current controllers/actions.
     *
     * Useful when ACL tree is corrupted or missing nodes.
     *
     * @return \Cake\Http\Response|null
     */
    public function rebuild()
    {
        if ($this->request->is('post')) {
            $this->AcoBuilder->buildAcl();
            $this->Flash->success(__('ACOs were rebuilt'));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Returns a list of ACOs formatted for select input (id => alias).
     *
     * Adds visual depth indicator (--) based on tree depth.
     *
     * @return array<int, string>
     */
    protected function _getParentsList(): array
    {
        $acos = $this->Acos->find()
            ->where(['lft >' => 1])
            ->order(['lft' => 'ASC'])
            ->find('withDepth')
            ->toArray();

        $parents = [];
        foreach ($acos as $aco) {
            $parents[$aco->id] = str_repeat('-- ', $aco->depth) . $aco->alias;
        }

        return $parents;
    }

    /**
     * Builds a readable path (array of alias) for an ACO node.
     *
     * Example: ['controllers', 'Users', 'edit']
     *
     * @param int $acoId The ACO node ID.
     * @return array<int, string>
     */
    protected function _getAcoPathList($acoId): array
    {
        $path = [];
        $acoPath = $this->Acos->find('path', ['for' => $acoId])->toArray();
        foreach ($acoPath as $aco) {
            $path[$aco->id] = $aco->alias;
        }
        return $path;
    }
}
