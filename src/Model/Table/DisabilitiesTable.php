<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DisabilitiesTable extends Table
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

        $this->setTable('disabilities');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'disability_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('disability', 'Disability field is required.')
            ->notEmptyString('code', 'Code is required.')
            ->add('code', 'unique', [
                'rule' => function ($value, $context) {

                    $query = $this->find(
                        'all',
                        ['conditions' => ['code' => $value]]
                    );
                    return $query->isEmpty();
                },
                'message' => 'The code must be unique.'
            ]);

        return $validator;
    }
}
