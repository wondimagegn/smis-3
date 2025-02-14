<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ServiceWingsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('service_wings');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        // Add Timestamp behavior for created/modified tracking
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255, 'Service Wing name is too long.')
            ->notEmptyString('name', 'Service Wing name is required.')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Service Wing name must be unique.',
            ]);

        return $validator;
    }
}
