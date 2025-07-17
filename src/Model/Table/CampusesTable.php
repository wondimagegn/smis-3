<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Campuses Table
 */
class CampusesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('campuses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Colleges', [
            'foreignKey' => 'campus_id',
            'dependent' => false,
        ]);

        $this->hasMany('DormitoryBlocks', [
            'foreignKey' => 'campus_id',
            'dependent' => false,
        ]);

        $this->hasMany('MealHalls', [
            'foreignKey' => 'campus_id',
            'dependent' => false,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Name is required')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The campus name should be unique. The name is already taken. Use another one.'
            ]);

        return $validator;
    }

    /**
     * Checks if a campus can be deleted based on associated colleges
     *
     * @param int|null $campusId Campus ID
     * @return bool True if can be deleted, false otherwise
     */
    public function canItBeDeleted($campusId = null)
    {
        if (!$campusId) {
            return false;
        }

        $count = $this->Colleges->find()
            ->where(['Colleges.campus_id' => $campusId])
            ->count();

        return $count === 0;
    }
}
