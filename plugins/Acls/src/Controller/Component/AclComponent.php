<?php


namespace Acls\Controller\Component;

use Acl\AclInterface;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\IniConfig;
use Cake\Core\Exception\Exception;
use Cake\Utility\ClassRegistry;
use Cake\Utility\Inflector;

/**
 * Access Control List factory class.
 *
 * Uses a strategy pattern to allow custom ACL implementations to be used with the same component interface.
 * You can define by changing `Configure::write('Acl.classname', 'DbAcl');` in your App/Config/app.php. The adapter
 * you specify must implement `AclInterface`
 *
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html
 */
class AclComponent extends Component
{

    /**
     * Instance of an ACL class
     *
     * @var AclInterface
     */
    protected $_Instance = null;

    /**
     * Aro object.
     *
     * @var string
     */
    public $Aro;

    /**
     * Aco object
     *
     * @var string
     */
    public $Aco;

    /**
     * Constructor. Will return an instance of the correct ACL class as defined in `Configure::read('Acl.classname')`
     *
     * @param ComponentRegistry $collection A ComponentRegistry
     * @param array $config Array of configuration settings
     * @throws \Cake\Core\Exception\Exception when Acl.classname could not be loaded.
     */
    public function __construct(ComponentRegistry $collection, array $config = [])
    {
        parent::__construct($collection, $config);
        $className = $name = Configure::read('Acl.classname');
        if (!class_exists($className)) {
            $className = App::className('Acl.' . $name, 'Adapter');
            if (!$className) {
                throw new Exception(sprintf('Could not find %s.', $name));
            }
        }
        $this->adapter($className);
    }

    /**
     * Sets or gets the Adapter object currently in the AclComponent.
     *
     * `$this->Acl->adapter();` will get the current adapter class while
     * `$this->Acl->adapter($obj);` will set the adapter class
     *
     * Will call the initialize method on the adapter if setting a new one.
     *
     * @param AclInterface|string $adapter Instance of AclInterface or a string name of the class to use. (optional)
     * @return AclInterface|void either null, or the adapter implementation.
     * @throws \Cake\Core\Exception\Exception when the given class is not an instance of AclInterface
     */
    public function adapter($adapter = null)
    {
        if ($adapter) {
            if (is_string($adapter)) {
                $adapter = new $adapter();
            }
            if (!$adapter instanceof AclInterface) {
                throw new Exception('AclComponent adapters must implement AclInterface');
            }
            $this->_Instance = $adapter;
            $this->_Instance->initialize($this);

            return;
        }

        return $this->_Instance;
    }

    /**
     * Pass-thru function for ACL check instance. Check methods
     * are used to check whether or not an ARO can access an ACO
     *
     * @param array|string|Model $aro ARO The requesting object identifier. See `AclNode::node()` for possible formats
     * @param array|string|Model $aco ACO The controlled object identifier. See `AclNode::node()` for possible formats
     * @param string $action Action (defaults to *)
     * @return bool Success
     */
    public function check($aro, $aco, $action = '*')
    {
        if ($aro === null || $aco === null) {
            return false;
        }

        // Hardcoded role-based permissions
        if (
            (strcasecmp($aco, 'controllers/Dashboard/index') === 0) ||
            (
                (
                    strcasecmp($aco, 'controllers/Users/index') === 0 ||
                    (strcasecmp($aco, 'controllers/Users/department_create_user_account') === 0 && $aro['User']['role_id'] == ROLE_DEPARTMENT) ||
                    (strcasecmp($aco, 'controllers/Users/add') === 0 && $aro['User']['role_id'] != ROLE_DEPARTMENT) ||
                    (strcasecmp($aco, 'controllers/Users/assign') === 0 && $aro['User']['role_id'] == ROLE_REGISTRAR) ||
                    strcasecmp($aco, 'controllers/Securitysettings/permission_management') === 0 ||
                    strcasecmp($aco, 'controllers/Securitysettings/index') === 0 ||
                    strcasecmp($aco, 'controllers/Acls/Permissions/add') === 0 ||
                    strcasecmp($aco, 'controllers/Acls/Permissions/delete') === 0 ||
                    strcasecmp($aco, 'controllers/Acls/Permissions/index') === 0 ||
                    strcasecmp($aco, 'controllers/Acls/Permissions/edit') === 0 ||
                    strcasecmp($aco, 'controllers/Acls/Acos/index') === 0 ||
                    strcasecmp($aco, 'controllers/Acls/Acls/index') === 0 ||
                    strcasecmp($aco, 'controllers/Users/build_user_menu') === 0 ||
                    (strcasecmp($aco, 'controllers/Acls/Acos/add') === 0 && Configure::read('Developer')) ||
                    (strcasecmp($aco, 'controllers/Acls/Acos/edit') === 0 && Configure::read('Developer')) ||
                    (strcasecmp($aco, 'controllers/Acls/Acos/delete') === 0 && Configure::read('Developer')) ||
                    (strcasecmp($aco, 'controllers/Acls/Acos/rebuild') === 0 && Configure::read('Developer'))
                ) &&
                ($aro['User']['role_id'] == ROLE_SYSADMIN || $aro['User']['is_admin'] == 1) &&
                $aro['User']['active'] == 1
            )
        ) {
            return true;
        }

        // Equivalent ACL checking
        $equivalentACL = Configure::read('ACL.equivalentACL');
        if (!empty($equivalentACL) && is_array($equivalentACL)) {
            foreach ($equivalentACL as $parent => $childAcls) {
                if (strcasecmp('controllers' . DS . $parent, $aco) === 0) {
                    foreach ($childAcls as $childAcl) {
                        $checking = explode(DS, $childAcl);
                        if ($checking[1] === '*') {
                            $controllerId = $this->Aco->find()
                                ->where(['Aco.alias' => $checking[0]])
                                ->select(['id'])
                                ->first()
                                ->id ?? null;
                            $actions = $this->Aco->find('list', [
                                'keyField' => 'id',
                                'valueField' => 'alias',
                                'conditions' => ['Aco.parent_id' => $controllerId],
                            ])->toArray();

                            foreach ($actions as $actionValue) {
                                if (
                                    strcasecmp('controllers' . DS . $parent, 'controllers' . DS . $checking[0] . DS . $actionValue) !== 0 &&
                                    $this->check($aro, 'controllers' . DS . $checking[0] . DS . $actionValue)
                                ) {
                                    return true;
                                }
                            }
                        } elseif ($this->check($aro, 'controllers' . DS . $childAcl)) {
                            return true;
                        }
                    }
                }
            }
        }

        // Delegate to DbAcl's check method
        return $this->_Instance->check($aro, $aco, $action);
    }
    /**
     * Pass-thru function for ACL allow instance. Allow methods
     * are used to grant an ARO access to an ACO.
     *
     * @param array|string|Model $aro ARO The requesting object identifier. See `AclNode::node()` for possible formats
     * @param array|string|Model $aco ACO The controlled object identifier. See `AclNode::node()` for possible formats
     * @param string $action Action (defaults to *)
     * @return bool Success
     */
    public function allow($aro, $aco, $action = "*")
    {
        return $this->_Instance->allow($aro, $aco, $action);
    }

    /**
     * Pass-thru function for ACL deny instance. Deny methods
     * are used to remove permission from an ARO to access an ACO.
     *
     * @param array|string|Model $aro ARO The requesting object identifier. See `AclNode::node()` for possible formats
     * @param array|string|Model $aco ACO The controlled object identifier. See `AclNode::node()` for possible formats
     * @param string $action Action (defaults to *)
     * @return bool Success
     */
    public function deny($aro, $aco, $action = "*")
    {
        return $this->_Instance->deny($aro, $aco, $action);
    }

    /**
     * Pass-thru function for ACL inherit instance. Inherit methods
     * modify the permission for an ARO to be that of its parent object.
     *
     * @param array|string|Model $aro ARO The requesting object identifier. See `AclNode::node()` for possible formats
     * @param array|string|Model $aco ACO The controlled object identifier. See `AclNode::node()` for possible formats
     * @param string $action Action (defaults to *)
     * @return bool Success
     */
    public function inherit($aro, $aco, $action = "*")
    {
        return $this->_Instance->inherit($aro, $aco, $action);
    }
}
