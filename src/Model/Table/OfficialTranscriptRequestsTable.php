<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OfficialTranscriptRequestsTable extends Table
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

        $this->setTable('official_transcript_requests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('OfficialRequestStatuses', [
            'foreignKey' => 'official_transcript_request_id',
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
            ->integer('trackingnumber')
            ->requirePresence('trackingnumber', 'create')
            ->notEmptyString('trackingnumber')
            ->add('trackingnumber', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 200)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('father_name')
            ->maxLength('father_name', 200)
            ->requirePresence('father_name', 'create')
            ->notEmptyString('father_name');

        $validator
            ->scalar('grand_father')
            ->maxLength('grand_father', 200)
            ->requirePresence('grand_father', 'create')
            ->notEmptyString('grand_father');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('mobile_phone')
            ->maxLength('mobile_phone', 200)
            ->requirePresence('mobile_phone', 'create')
            ->notEmptyString('mobile_phone');

        $validator
            ->scalar('studentnumber')
            ->maxLength('studentnumber', 200)
            ->requirePresence('studentnumber', 'create')
            ->notEmptyString('studentnumber');

        $validator
            ->scalar('admissiontype')
            ->maxLength('admissiontype', 200)
            ->requirePresence('admissiontype', 'create')
            ->notEmptyString('admissiontype');

        $validator
            ->scalar('degreetype')
            ->maxLength('degreetype', 200)
            ->requirePresence('degreetype', 'create')
            ->notEmptyString('degreetype');

        $validator
            ->scalar('institution_name')
            ->maxLength('institution_name', 200)
            ->requirePresence('institution_name', 'create')
            ->notEmptyString('institution_name');

        $validator
            ->scalar('institution_address')
            ->requirePresence('institution_address', 'create')
            ->notEmptyString('institution_address');

        $validator
            ->scalar('recipent_country')
            ->maxLength('recipent_country', 200)
            ->requirePresence('recipent_country', 'create')
            ->notEmptyString('recipent_country');

        $validator
            ->boolean('request_processed')
            ->notEmptyString('request_processed');

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
        $rules->add($rules->isUnique(['trackingnumber']));

        return $rules;
    }

    public function nextTrackingNumber()
    {
        $nextTrackingNumber = $this->find(
            'first',
            array('order' => array('OfficialTranscriptRequest.id DESC'))
        );
        if (
            isset($nextTrackingNumber)
            && !empty($nextTrackingNumber)
        ) {
            return $nextTrackingNumber['OfficialTranscriptRequest']['trackingnumber'] + 1;
        }
        return 100000;
    }

    function checkUnique($data, $fieldName)
    {
        $valid = false;
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $valid = $this->isUnique(array($fieldName => $data));
        }
        return $valid;
    }
}
