<?php
namespace Acls\Model;

use Cake\ORM\Table;

class AclsAppModel extends Table
{
    /**
     * Initialization method
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // ✅ Define default table if required
        //$this->setTable('acls');
    }
}
?>
