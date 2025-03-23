<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SpecializationsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('specializations');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        // Add Timestamp behavior for created/modified tracking
        $this->addBehavior('Timestamp');

        // Define relationships
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('department_id', 'Department ID must be a valid number.')
            ->requirePresence('department_id', 'create')
            ->notEmptyString('department_id', 'Department ID is required.');

        $validator
            ->scalar('name')
            ->maxLength('name', 255, 'Field of Study Name is too long.')
            ->notEmptyString('name', 'Field of Study Name is required.')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Field of Study Name must be unique.',
            ]);

        $validator
            ->boolean('active', 'Active field must be a boolean value.')
            ->notEmptyString('active', 'Active status is required.');

        return $validator;
    }
}
