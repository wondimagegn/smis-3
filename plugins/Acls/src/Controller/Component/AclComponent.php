<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Core\Exception\Exception;

class AclComponent extends Component
{
    protected $_instance = null;

    public $Aro;

    public $Aco;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        $name = Configure::read('Acl.classname');
        if (!class_exists($name)) {
            list($plugin, $name) = pluginSplit($name, true);
            if (!class_exists($name)) {
                throw new Exception(sprintf('Could not find %s.', $name));
            }
        }
        $this->adapter($name);

        // Load tables
        $this->Aro = TableRegistry::getTableLocator()->get('Aros');
        $this->Aco = TableRegistry::getTableLocator()->get('Acos');
    }

    public function adapter($adapter = null)
    {
        if ($adapter) {
            if (is_string($adapter)) {
                $adapter = new $adapter();
            }
            $this->_instance = $adapter;
            $this->_instance->initialize($this);
            return;
        }
        return $this->_instance;
    }

    public function check($aro, $aco, $action = "*")
    {
        if ($aro === null || $aco === null) {
            return false;
        }

        if ($this->_isSpecialCase($aro, $aco)) {
            return true;
        }

        $equivalentACL = Configure::read('ACL.equivalentACL');
        if (!empty($equivalentACL) && is_array($equivalentACL)) {
            foreach ($equivalentACL as $parent => $childAcls) {
                if (strcasecmp('controllers/' . $parent, $aco) === 0) {
                    foreach ($childAcls as $childAcl) {
                        $checking = explode('/', $childAcl);
                        if ($checking[1] === '*') {
                            $controllerId = $this->Aco->find()->select(['id'])->where(['alias' => $checking[0]])->first();
                            $actions = $this->Aco->find('list', [
                                'conditions' => ['parent_id' => $controllerId->id],
                                'keyField' => 'id',
                                'valueField' => 'alias'
                            ])->toArray();

                            foreach ($actions as $actionValue) {
                                if ($this->check($aro, 'controllers/' . $checking[0] . '/' . $actionValue)) {
                                    return true;
                                }
                            }
                        } else {
                            if ($this->check($aro, 'controllers/' . $childAcl)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        $permKeys = $this->_getAcoKeys($this->Aro->Permissions->getSchema()->columns());
        $aroPath = $this->Aro->node($aro);
        $acoPath = $this->Aco->node($aco);

        if (empty($aroPath) || empty($acoPath)) {
            trigger_error(__('DbAcl::check() - Failed ARO/ACO node lookup in permissions check.'), E_USER_WARNING);
            return false;
        }

        if (strcasecmp($acoPath[0]['alias'], 'index') === 0) {
            $childrenAco = $this->Aco->find('list', [
                'conditions' => ['parent_id' => $acoPath[0]['parent_id']],
                'keyField' => 'id',
                'valueField' => 'id'
            ])->toArray();

            $aroKeys = Hash::extract($aroPath, '{n}.id');
            $acoKeys = array_values($childrenAco);

            $perms = $this->Aro->Permissions->find('all', [
                'conditions' => [
                    'aro_id IN' => $aroKeys,
                    'aco_id IN' => $acoKeys,
                    '_create' => 1,
                    '_read' => 1,
                    '_update' => 1,
                    '_delete' => 1,
                ]
            ])->toArray();

            if (!empty($perms)) {
                return true;
            }
        }

        $aroNode = $aroPath[0];
        $acoNode = $acoPath[0];

        if ($action !== '*' && !in_array('_' . $action, $permKeys)) {
            trigger_error(sprintf(__('ACO permissions key %s does not exist'), $action), E_USER_NOTICE);
            return false;
        }

        $inherited = [];
        $acoIDs = Hash::extract($acoPath, '{n}.id');

        foreach ($aroPath as $path) {
            $perms = $this->Aro->Permissions->find('all', [
                'conditions' => [
                    'aro_id' => $path['id'],
                    'aco_id IN' => $acoIDs
                ]
            ])->toArray();

            foreach ($perms as $perm) {
                if ($action === '*') {
                    foreach ($permKeys as $key) {
                        if ($perm[$key] === -1) {
                            return false;
                        } elseif ($perm[$key] === 1) {
                            $inherited[$key] = 1;
                        }
                    }
                    if (count($inherited) === count($permKeys)) {
                        return true;
                    }
                } else {
                    if ($perm['_' . $action] === 1) {
                        return true;
                    } elseif ($perm['_' . $action] === -1) {
                        return false;
                    }
                }
            }
        }

        return false;
    }

    private function _isSpecialCase($aro, $aco)
    {
        if (strcasecmp($aco, 'controllers/Dashboard/index') === 0) {
            return true;
        }

        $sysAdminOrAdmin = ($aro['User']['role_id'] == ROLE_SYSADMIN || $aro['User']['is_admin'] == 1) && $aro['User']['active'] == 1;

        if (
            in_array($aco, [
                'controllers/Users/index',
                'controllers/Securitysettings/permission_management',
                'controllers/Securitysettings/index',
                'controllers/Acls/Permissions/add',
                'controllers/Acls/Permissions/delete',
                'controllers/Acls/Permissions/index',
                'controllers/Acls/Permissions/edit',
                'controllers/Acls/Acos/index',
                'controllers/Acls/Acls/index',
                'controllers/Users/build_user_menu'
            ]) && $sysAdminOrAdmin
        ) {
            return true;
        }

        if (strcasecmp($aco, 'controllers/Users/department_create_user_account') === 0 && $aro['User']['role_id'] == ROLE_DEPARTMENT) {
            return true;
        }

        if (strcasecmp($aco, 'controllers/Users/add') === 0 && $aro['User']['role_id'] != ROLE_DEPARTMENT) {
            return true;
        }

        if (strcasecmp($aco, 'controllers/Users/assign') === 0 && $aro['User']['role_id'] == ROLE_REGISTRAR) {
            return true;
        }

        if (Configure::read('Developer')) {
            if (in_array($aco, [
                'controllers/Acls/Acos/add',
                'controllers/Acls/Acos/edit',
                'controllers/Acls/Acos/delete',
                'controllers/Acls/Acos/rebuild'
            ])) {
                return true;
            }
        }

        return false;
    }

    private function _getAcoKeys($schema)
    {
        $keys = [];
        foreach ($schema as $field) {
            if (strpos($field, '_') === 0) {
                $keys[] = $field;
            }
        }
        return $keys;
    }

    public function allow($aro, $aco, $action = "*")
    {
        return $this->_instance->allow($aro, $aco, $action);
    }

    public function deny($aro, $aco, $action = "*")
    {
        return $this->_instance->deny($aro, $aco, $action);
    }

    public function inherit($aro, $aco, $action = "*")
    {
        return $this->_instance->inherit($aro, $aco, $action);
    }

    public function grant($aro, $aco, $action = "*")
    {
        trigger_error(__('AclComponent::grant() is deprecated, use allow() instead'), E_USER_WARNING);
        return $this->_instance->allow($aro, $aco, $action);
    }

    public function revoke($aro, $aco, $action = "*")
    {
        trigger_error(__('AclComponent::revoke() is deprecated, use deny() instead'), E_USER_WARNING);
        return $this->_instance->deny($aro, $aco, $action);
    }
}
