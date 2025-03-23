<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SponsorTypesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('sponsor_types');
        $this->setPrimaryKey('id');
        $this->setDisplayField('sponsor');

        // Add Timestamp behavior for created/modified tracking
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('sponsor')
            ->maxLength('sponsor', 255, 'Sponsor name is too long.')
            ->requirePresence('sponsor', 'create')
            ->notEmptyString('sponsor', 'Sponsor name is required.');

        $validator
            ->scalar('code')
            ->maxLength('code', 50, 'Code is too long.')
            ->requirePresence('code', 'create')
            ->notEmptyString('code', 'Code is required.')
            ->add('code', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This sponsor code is already taken. Try another one.',
            ]);

        return $validator;
    }
}
