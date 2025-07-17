<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PlacementTypesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_types');
        $this->setDisplayField('placement_type');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('code', 'Code is required')
            ->add('code', 'checkUnique', [
                'rule' => 'checkUniqueCode',
                'message' => 'The code should be unique. This code is already taken. Try another one.'
            ]);

        return $validator;
    }

    public function beforeValidate($entity, $options = []): bool
    {
        if (!$entity->id && !$this->checkUniqueCode($entity)) {
            $entity->setError('code', ['code_unique' => 'The code should be unique.']);
            return false;
        }
        return true;
    }

    public function checkUniqueCode($entity): bool
    {
        $conditions = ['PlacementTypes.code' => trim($entity->code)];
        if (!empty($entity->id)) {
            $conditions['PlacementTypes.id <>'] = $entity->id;
        }

        $count = $this->find('count')
            ->where($conditions)
            ->disableHydration()
            ->count();

        return $count === 0;
    }

    public function canItBeDeleted(?int $id = null): bool
    {
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $count = $acceptedStudentsTable->find('count')
            ->where(['AcceptedStudents.placement_type_id' => $id])
            ->disableHydration()
            ->count();

        return $count === 0;
    }
}
