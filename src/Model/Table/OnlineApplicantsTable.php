<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class OnlineApplicantsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('online_applicants');
        $this->setDisplayField('full_name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER'
        ]);

        $this->hasMany('OnlineApplicantStatuses', [
            'foreignKey' => 'online_applicant_id',
            'dependent' => false
        ]);
        $this->hasMany('Attachments', [
            'className' => 'Media.Attachments',
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'OnlineApplicant'],
            'dependent' => true
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('college_id')
            ->notEmptyString('college_id', __('Please select the college you want to join'))
            ->integer('department_id')
            ->notEmptyString('department_id', __('Please select the department you want to join'))
            ->integer('program_id')
            ->notEmptyString('program_id', __('Please select the study level'))
            ->integer('program_type_id')
            ->notEmptyString('program_type_id', __('Please select the admission type'))
            ->scalar('academic_year')
            ->notEmptyString('academic_year', __('Please select the academic year you want to start.'))
            ->scalar('semester')
            ->notEmptyString('semester', __('Please select the semester you want to start.'))
            ->scalar('undergraduate_university_name')
            ->notEmptyString('undergraduate_university_name', __('Provide undergraduate university name.'))
            ->numeric('undergraduate_university_cgpa')
            ->notEmptyString('undergraduate_university_cgpa', __('Provide undergraduate university CGPA.'))
            ->scalar('undergraduate_university_field_of_study')
            ->notEmptyString('undergraduate_university_field_of_study', __('Provide undergraduate university field of study.'))
            ->scalar('financial_support')
            ->notEmptyString('financial_support', __('Provide financial support type.'))
            ->scalar('name_of_sponsor')
            ->notEmptyString('name_of_sponsor', __('Provide name of sponsor.'))
            ->scalar('first_name')
            ->notEmptyString('first_name', __('Provide first name.'))
            ->scalar('father_name')
            ->notEmptyString('father_name', __('Provide father name.'))
            ->scalar('grand_father_name')
            ->notEmptyString('grand_father_name', __('Provide grand father name.'))
            ->date('date_of_birth')
            ->notEmptyDate('date_of_birth', __('Please provide birth date.'))
            ->scalar('gender')
            ->notEmptyString('gender', __('Please select gender.'))
            ->scalar('mobile_phone')
            ->notEmptyString('mobile_phone', __('Please provide the mobile number.'))
            ->email('email')
            ->notEmptyString('email', __('Please provide the email address.'));

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        return $rules;
    }

    public function nextTrackingNumber(): int
    {
        $last = $this->find()
            ->select(['applicationnumber'])
            ->orderDesc('created')
            ->first();

        return $last ? $last->applicationnumber + 1 : 12100;
    }

    public function isAppliedForAdmission(array $data): int
    {
        $conditions = [
            'OnlineApplicants.department_id' => $data['OnlineApplicant']['department_id'],
            'OnlineApplicants.college_id' => $data['OnlineApplicant']['college_id'],
            'OnlineApplicants.program_id' => $data['OnlineApplicant']['program_id'],
            'OnlineApplicants.program_type_id' => $data['OnlineApplicant']['program_type_id'],
            'OnlineApplicants.academic_year' => $data['OnlineApplicant']['academic_year'],
            'OnlineApplicants.semester' => $data['OnlineApplicant']['semester'],
            'OnlineApplicants.email' => $data['OnlineApplicant']['email']
        ];

        $applied = $this->find()
            ->select(['applicationnumber'])
            ->where($conditions)
            ->orderDesc('created')
            ->first();

        return $applied ? $applied->applicationnumber : 0;
    }

    public function checkUnique($data, string $fieldName): bool
    {
        if (!$this->hasField($fieldName)) {
            return false;
        }
        return $this->isUnique([$fieldName => $data]);
    }

    public function preparedAttachment(array $data = null): array
    {
        if (empty($data['Attachment'])) {
            return $data;
        }

        foreach ($data['Attachment'] as $in => &$dv) {
            if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['file']['tmp_name'])) {
                unset($data['Attachment'][$in]);
            } else {
                $dv['model'] = 'OnlineApplicant';
                $dv['group'] = ($in == 0) ? 'OnlineApplicantFiles' : 'OnlineApplicantPaymentSlips';
            }
        }

        return $data;
    }
}
