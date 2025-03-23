<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ForeignProgramsTable extends Table
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

        $this->setTable('foreign_programs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'foreign_program_id',
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
            ->notEmptyString('program', 'Please provide a program name.')
            ->notEmptyString('code', 'Please provide a program code.');

        return $validator;
    }

    public function beforeValidate($event, $entity, $options)
    {
        if (!$entity->isNew()) {
            return true;
        }

        $existing = $this->find()->where(['code' => $entity->code])->first();
        if ($existing) {
            $entity->setError('code', ['Code must be unique.']);
            return false;
        }

        return true;
    }
}
