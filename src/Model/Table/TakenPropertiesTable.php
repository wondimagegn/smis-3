<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TakenPropertiesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('taken_properties');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Offices', [
            'foreignKey' => 'office_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Please provide property name.')
            ->maxLength('name', 255);

        $validator
            ->requirePresence('return_date', 'create')
            ->notEmptyDate('return_date', 'Return date is required.')
            ->add('return_date', 'validDate', [
                'rule' => function ($value, $context) {
                    return isset($context['data']['taken_date']) && strtotime($value) >= strtotime($context['data']['taken_date']);
                },
                'message' => 'Return date should be greater than or equal to taken date.'
            ]);

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
        $rules->add($rules->existsIn(['office_id'], 'Offices'));
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));

        return $rules;
    }
}
