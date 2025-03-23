<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AlumniMembersTable extends Table
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

        $this->setTable('alumni_members');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('trackingnumber')
            ->allowEmptyString('trackingnumber');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 200)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 200)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 50)
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender');

        $validator
            ->date('date_of_birth')
            ->requirePresence('date_of_birth', 'create')
            ->notEmptyDate('date_of_birth');

        $validator
            ->scalar('gradution')
            ->maxLength('gradution', 4)
            ->requirePresence('gradution', 'create')
            ->notEmptyString('gradution');

        $validator
            ->scalar('institute_college')
            ->maxLength('institute_college', 200)
            ->requirePresence('institute_college', 'create')
            ->notEmptyString('institute_college');

        $validator
            ->scalar('department')
            ->maxLength('department', 200)
            ->requirePresence('department', 'create')
            ->notEmptyString('department');

        $validator
            ->scalar('program')
            ->maxLength('program', 200)
            ->requirePresence('program', 'create')
            ->notEmptyString('program');

        $validator
            ->scalar('country')
            ->maxLength('country', 100)
            ->allowEmptyString('country');

        $validator
            ->scalar('city')
            ->maxLength('city', 100)
            ->allowEmptyString('city');

        $validator
            ->scalar('current_position')
            ->maxLength('current_position', 100)
            ->allowEmptyString('current_position');

        $validator
            ->scalar('name_of_employer')
            ->maxLength('name_of_employer', 250)
            ->allowEmptyString('name_of_employer');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 100)
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone');

        $validator
            ->scalar('work_telephone')
            ->maxLength('work_telephone', 100)
            ->allowEmptyString('work_telephone');

        $validator
            ->scalar('home_telephone')
            ->maxLength('home_telephone', 100)
            ->allowEmptyString('home_telephone');

        $validator
            ->scalar('remarks')
            ->allowEmptyString('remarks');

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

        $rules->add($rules->isUnique(['email']));

        return $rules;
    }

    public function nextTrackingNumber()
    {

        $nextapplicationnumber = $this->find(
            'first',
            array('order' => array('AlumniMember.created DESC'))
        );
        if (
            isset($nextapplicationnumber)
            && !empty($nextapplicationnumber)
        ) {
            return $nextapplicationnumber['AlumniMember']['trackingnumber'] + 1;
        }
        return 20011;
    }

    public function checkUnique($data, $fieldName)
    {

        $valid = false;
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $valid = $this->isUnique(array($fieldName => $data));
        }
        return $valid;
    }
}
