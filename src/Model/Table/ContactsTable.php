<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ContactsTable extends Table
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

        $this->setTable('contacts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'propertyName' => 'Student',
        ]);
        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
            'propertyName' => 'Staff',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'propertyName' => 'Country',
        ]);
        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'propertyName' => 'Region',
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'propertyName' => 'Zone',
        ]);
        $this->belongsTo('Woredas', [
            'foreignKey' => 'woreda_id',
            'propertyName' => 'Woreda',
        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',
            'propertyName' => 'City',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 255)
            ->allowEmptyString('first_name');

        $validator
            ->scalar('middle_name')
            ->maxLength('middle_name', 255)
            ->allowEmptyString('middle_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->allowEmptyString('last_name');

        $validator
            ->boolean('primary_contact')
            ->allowEmptyString('primary_contact');

        $validator
            ->scalar('relationship')
            ->maxLength('relationship', 20)
            ->allowEmptyString('relationship');

        $validator
            ->scalar('address1')
            ->allowEmptyString('address1');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('alternative_email')
            ->maxLength('alternative_email', 200)
            ->allowEmptyString('alternative_email');

        $validator
            ->scalar('phone_home')
            ->maxLength('phone_home', 200)
            ->allowEmptyString('phone_home');

        $validator
            ->scalar('phone_office')
            ->maxLength('phone_office', 200)
            ->allowEmptyString('phone_office');

        $validator
            ->scalar('phone_mobile')
            ->maxLength('phone_mobile', 200)
            ->allowEmptyString('phone_mobile');

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
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['staff_id'], 'Staffs'));
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        $rules->add($rules->existsIn(['region_id'], 'Regions'));
        $rules->add($rules->existsIn(['zone_id'], 'Zones'));
        $rules->add($rules->existsIn(['woreda_id'], 'Woredas'));
        $rules->add($rules->existsIn(['city_id'], 'Cities'));

        return $rules;
    }

    function checkLengthPhone($data, $fieldName)
    {
        $valid = true;
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $check = strlen($data[$fieldName]);
            debug($check);
            if (!empty($data[$fieldName]) && $check > 0 && ($check < 9 || $check != 13)) {
                $valid = false;
            }
        }
        return $valid;
    }
}
