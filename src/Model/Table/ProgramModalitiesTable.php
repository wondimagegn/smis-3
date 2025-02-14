<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProgramModalitiesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('program_modalities');
        $this->setPrimaryKey('id');
        $this->setDisplayField('modality');

        // Add timestamp behavior
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('modality', 'Modality name is required.')
            ->notEmptyString('code', 'Code is required.')
            ->add('code', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This code is already taken.',
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['code'], 'This code is already taken.'));
        return $rules;
    }
}
