<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DormitoriesTable extends Table
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

        $this->setTable('dormitories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('DormitoryBlocks', [
            'foreignKey' => 'dormitory_block_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('DormitoryAssignments', [
            'foreignKey' => 'dormitory_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('dorm_number', 'Dorm number is required.')
            ->numeric('dorm_number', 'Dorm number must be numeric.')
            ->greaterThanOrEqual('dorm_number', 0, 'Dorm number must be greater than or equal to zero.')
            ->add('dorm_number', 'unique', [
                'rule' => function ($value, $context) {

                    return $this->find(
                        'all',
                        ['conditions' => ['dorm_number' => $value]]
                    )->isEmpty();
                },
                'message' => 'This dorm number already exists.
                Please provide a unique dorm number.'
            ]);

        $validator
            ->notEmptyString('floor', 'Floor is required.');

        $validator
            ->notEmptyString('capacity', 'Capacity is required.')
            ->numeric('capacity', 'Capacity must be numeric.')
            ->greaterThanOrEqual('capacity', 0, 'Capacity must be greater than or equal to zero.');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['dormitory_block_id'], 'DormitoryBlocks'));

        return $rules;
    }
}
