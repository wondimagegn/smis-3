<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudyProgramsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('study_programs');
        $this->setPrimaryKey('id');
        $this->setDisplayField('study_program_name');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('study_program_name')
            ->maxLength('study_program_name', 255)
            ->requirePresence('study_program_name', 'create')
            ->notEmptyString('study_program_name', 'Study Program Name is required');

        $validator
            ->scalar('code')
            ->maxLength('code', 50)
            ->requirePresence('code', 'create')
            ->notEmptyString('code', 'Code is required')
            ->add('code', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The code must be unique',
            ]);

        return $validator;
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && $this->exists(['code' => $entity->code])) {
            return false; // Prevent duplicate codes
        }
        return true;
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
        $rules->add($rules->isUnique(['code']));

        return $rules;
    }
}
