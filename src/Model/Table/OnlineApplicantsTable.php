<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OnlineApplicantsTable extends Table
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

        $this->setTable('online_applicants');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'online_applicant_id',
        ]);
        $this->hasMany('OnlineApplicantStatuses', [
            'foreignKey' => 'online_applicant_id',
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
            ->integer('applicationnumber')
            ->requirePresence('applicationnumber', 'create')
            ->notEmptyString('applicationnumber');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 10)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 10)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->scalar('undergraduate_university_name')
            ->maxLength('undergraduate_university_name', 200)
            ->requirePresence('undergraduate_university_name', 'create')
            ->notEmptyString('undergraduate_university_name');

        $validator
            ->numeric('undergraduate_university_cgpa')
            ->requirePresence('undergraduate_university_cgpa', 'create')
            ->notEmptyString('undergraduate_university_cgpa');

        $validator
            ->scalar('undergraduate_university_field_of_study')
            ->maxLength('undergraduate_university_field_of_study', 200)
            ->requirePresence('undergraduate_university_field_of_study', 'create')
            ->notEmptyString('undergraduate_university_field_of_study');

        $validator
            ->scalar('postgraduate_university_name')
            ->maxLength('postgraduate_university_name', 200)
            ->requirePresence('postgraduate_university_name', 'create')
            ->notEmptyString('postgraduate_university_name');

        $validator
            ->numeric('postgraduate_university_cgpa')
            ->requirePresence('postgraduate_university_cgpa', 'create')
            ->notEmptyString('postgraduate_university_cgpa');

        $validator
            ->scalar('postgraduate_university_field_of_study')
            ->maxLength('postgraduate_university_field_of_study', 200)
            ->requirePresence('postgraduate_university_field_of_study', 'create')
            ->notEmptyString('postgraduate_university_field_of_study');

        $validator
            ->scalar('financial_support')
            ->maxLength('financial_support', 200)
            ->requirePresence('financial_support', 'create')
            ->notEmptyString('financial_support');

        $validator
            ->scalar('name_of_sponsor')
            ->maxLength('name_of_sponsor', 200)
            ->requirePresence('name_of_sponsor', 'create')
            ->notEmptyString('name_of_sponsor');

        $validator
            ->integer('year_of_experience')
            ->notEmptyString('year_of_experience');

        $validator
            ->scalar('disability')
            ->maxLength('disability', 200)
            ->requirePresence('disability', 'create')
            ->notEmptyString('disability');

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
            ->scalar('grand_father_name')
            ->maxLength('grand_father_name', 200)
            ->requirePresence('grand_father_name', 'create')
            ->notEmptyString('grand_father_name');

        $validator
            ->date('date_of_birth')
            ->requirePresence('date_of_birth', 'create')
            ->notEmptyDate('date_of_birth');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 10)
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender');

        $validator
            ->scalar('mobile_phone')
            ->maxLength('mobile_phone', 36)
            ->requirePresence('mobile_phone', 'create')
            ->notEmptyString('mobile_phone');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->boolean('application_status')
            ->notEmptyString('application_status');

        $validator
            ->scalar('approved_by')
            ->maxLength('approved_by', 36)
            ->requirePresence('approved_by', 'create')
            ->notEmptyString('approved_by');

        $validator
            ->boolean('document_submitted')
            ->notEmptyString('document_submitted');

        $validator
            ->numeric('entrance_result')
            ->requirePresence('entrance_result', 'create')
            ->notEmptyString('entrance_result');

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
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }


    public function nextTrackingNumber()
    {
        $nextapplicationnumber = $this->find(
            'first',
            array('order' => array('OnlineApplicant.created DESC'))
        );
        if (
            isset($nextapplicationnumber)
            && !empty($nextapplicationnumber)
        ) {
            return $nextapplicationnumber['OnlineApplicant']['applicationnumber'] + 1;
        }
        return 12100;
    }
    public function isAppliedFordmittion($data)
    {
        $applied = $this->find(
            'first',
            array(
                'conditions' => array(
                    'OnlineApplicant.department_id' => $data['OnlineApplicant']['department_id'],
                    'OnlineApplicant.college_id' => $data['OnlineApplicant']['college_id'],

                    'OnlineApplicant.program_id' => $data['OnlineApplicant']['program_id'],
                    'OnlineApplicant.program_type_id' => $data['OnlineApplicant']['program_type_id'],
                    'OnlineApplicant.academic_year' => $data['OnlineApplicant']['academic_year'],
                    'OnlineApplicant.semester' => $data['OnlineApplicant']['semester'],
                    'OnlineApplicant.email' => $data['OnlineApplicant']['email']

                ),
                'order' => array('OnlineApplicant.created DESC'),
                'recursive' => -1
            )
        );
        debug($data);
        debug($applied);
        if (isset($applied) && !empty($applied)) {
            return $applied['OnlineApplicant']['applicationnumber'];
        }
        return 0;
    }


    function checkUnique($data, $fieldName)
    {
        $valid = false;
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $valid = $this->isUnique(array($fieldName => $data));
        }
        return $valid;
    }
    function preparedAttachment($data = null)
    {

        foreach ($data['Attachment'] as $in => &$dv) {
            if (
                empty($dv['file']['name']) && empty($dv['file']['type'])
                && empty($dv['tmp_name'])
            ) {
                unset($data['Attachment'][$in]);
            } elseif ($in == 0) {
                $dv['model'] = 'OnlineApplicant';
                $dv['group'] = 'OnlineApplicantFiles';
            } elseif ($in == 1) {
                $dv['model'] = 'OnlineApplicant';
                $dv['group'] = 'OnlineApplicantPaymentSlips';
            }
        }
        return $data;
    }
}
