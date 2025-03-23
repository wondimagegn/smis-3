<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WithdrawalsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('withdrawals'); // Define database table
        $this->setPrimaryKey('id'); // Define primary key
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('student_id', 'Student ID must be numeric')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id', 'Student ID is required');

        $validator
            ->notEmptyString('reason', 'Reason is required');

        $validator
            ->date('acceptance_date', ['ymd'], 'Provide a valid date')
            ->requirePresence('acceptance_date', 'create');

        $validator
            ->boolean('forced_withdrawal', 'Must be a boolean value');

        $validator
            ->notEmptyString('minute_number', 'Minute number is required');

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
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }
}
