<?php
namespace Acls\Model\Table;

use Acl\Model\Table\AcosTable as BaseAcosTable;
use Cake\ORM\Query;
use Cake\Datasource\ConnectionManager;
class AcosTable extends BaseAcosTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Tree');
        $this->setEntityClass(\Acl\Model\Entity\Aco::class);
    }

    public function findWithDepth(Query $query, array $options)
    {
        $connection = ConnectionManager::get('default');
        $subQuery = "(SELECT COUNT(*) FROM acos AS parent WHERE parent.lft < Acos.lft AND parent.rght > Acos.rght)";
        return $query->select(['Acos.id', 'Acos.alias'])
            ->select(['depth' => $subQuery]);
    }
}
