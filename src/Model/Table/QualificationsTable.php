<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class QualificationsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('qualifications');
        $this->setPrimaryKey('id');
        $this->setDisplayField('qualification');

        // Add associations
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('qualification', 'create')
            ->notEmptyString('qualification', 'Qualification is required.');

        $validator
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Program ID is required.')
            ->numeric('program_id', 'Program ID must be numeric.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['program_id'], 'Programs'));

        return $rules;
    }
}
