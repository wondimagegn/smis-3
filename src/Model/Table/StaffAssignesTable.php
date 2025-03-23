<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StaffAssignesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('staff_assignes');
        $this->setPrimaryKey('id');

        // Define Relationships
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id', 'User is required.');

        return $validator;
    }
}
