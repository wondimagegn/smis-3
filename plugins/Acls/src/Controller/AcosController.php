<?php
namespace Acls\Controller;

use Acls\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class AcosController extends AppController
{
    public $root_id;

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
        $this->Acos = TableRegistry::getTableLocator()->get('Acos');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
        $this->Roles = TableRegistry::getTableLocator()->get('Roles');

        // ✅ Get Root Node ID
        $this->root_id = $this->Acos->find()
            ->select(['id'])
            ->where(['alias' => 'controllers'])
            ->first()
            ->id ?? null;
    }

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Validate ACO
     */
    public function validateAco($data)
    {
        if (empty($data['alias'])) {
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
     * Index method
     */
    public function index($parent_id = null)
    {
        $admin_detail = $this->Users->find()
            ->contain(['Staff'])
            ->where(['Users.id' => $this->request->getSession()->read('User.user')])
            ->first();

        if (empty($parent_id)) {
            $parent_id = $this->root_id;
        } else {
            $this->Acos->addBehavior('Tree');
            $path = $this->_getAcoPathList($parent_id);
        }

        $this->Acos->virtualFields['num_children'] = 'CAST((Acos.rght - Acos.lft - 1) / 2 AS UNSIGNED)';
        $options = [
            'order' => ['order' => 'ASC'],
            'conditions' => ['parent_id' => $parent_id],
            'recursive' => -1
        ];

        $acos = $this->Acos->find('all', $options)->toArray();

        foreach ($acos as &$v) {
            $v->num_children = $this->Acos->find()
                ->where(['parent_id' => $v->id])
                ->count();
        }

        $aco = $this->Acos->findById($parent_id)->first();

        $this->set(compact('acos', 'aco', 'path', 'parent_id'));
    }

    /**
     * Edit method
     */
    public function edit($id = null)
    {
        $aco = $this->Acos->get($id);

        if ($this->request->is(['post', 'put'])) {
            $aco = $this->Acos->patchEntity($aco, $this->request->getData());

            if (!empty($aco->admin)) {
                $aco->admin = implode(',', $aco->admin);
            }

            if ($this->Acos->save($aco)) {
                $this->Flash->success('ACO Updated');
                return $this->redirect(['action' => 'index', $aco->parent_id]);
            } else {
                $this->Flash->error('Unable to update ACO. Please try again.');
            }
        }

        $parents = $this->_getParentsList();
        $roles = $this->Roles->find('list')->toArray();

        $this->set(compact('aco', 'parents', 'roles'));
    }

    /**
     * Delete method
     */
    public function delete()
    {
        $delete_count = 0;

        if ($this->request->is('post')) {
            foreach ($this->request->getData('Aco.delete') as $id => $delete) {
                if ($delete == 1) {
                    if ($this->Acos->delete($this->Acos->get($id))) {
                        $delete_count++;
                    }
                }
            }
        }

        $this->Flash->success($delete_count . ' ACO' . ($delete_count == 1 ? ' was' : 's were') . ' deleted');
        return $this->redirect(['action' => 'index', $this->request->getData('Aco.parent_id')]);
    }

    /**
     * Rebuild method
     */
    public function rebuild()
    {
        if ($this->request->is('post')) {
            $this->Flash->success('ACOs were rebuilt');
            $this->Acl->buildAcl();
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Get Parents List
     */
    protected function _getParentsList()
    {
        $acos = $this->Acos->find('all', ['order' => ['lft' => 'ASC']])->toArray();
        $parents = [];

        foreach ($acos as $aco) {
            $depth = substr_count($aco->alias, '/');
            $parents[$aco->id] = str_repeat('-- ', $depth) . $aco->alias;
        }

        return $parents;
    }

    /**
     * Get ACO Path List
     */
    protected function _getAcoPathList($aco_id)
    {
        $_path = $this->Acos->find('path', ['for' => $aco_id])->toArray();
        $path = Hash::extract($_path, '{n}.alias');
        return $path;
    }
}
