<?php

namespace App\Controller\Component;

use Acl\AclInterface;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Acl\Controller\Component\AclComponent;

// Import ACL Plugin Component
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\Core\Exception\Exception;

class CustomAclComponent extends AclComponent
{

    public function __construct(ComponentRegistry $registry, array $config = [])
    {

        parent::__construct($registry, $config);
    }

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
     * Override the check method to use custom logic.
     */
    public function check($aro, $aco, $action = "*")
    {

        if ($aro === null || $aco === null) {
            return false;
        }

        $session = $this->getController()->getRequest()->getSession();
        $permissionLists = $session->read('permissionLists');

        if (!empty($permissionLists) && in_array($aco, $permissionLists)) {
            return true;
        }

        // If not found in session, fallback to core ACL check
        return parent::check($aro, $aco, $action);
    }

    public function allow($aro, $aco, $action = "*")
    {

        return $this->_Instance->allow($aro, $aco, $action);
    }

    public function deny($aro, $aco, $action = "*")
    {

        return $this->_Instance->deny($aro, $aco, $action);
    }

    public function inherit($aro, $aco, $action = "*")
    {

        return $this->_Instance->inherit($aro, $aco, $action);
    }
}
