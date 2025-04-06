<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseSubstitutionRequestsTable extends Table
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

        $this->setTable('course_substitution_requests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseForSubstitueds', [
            'foreignKey' => 'course_for_substitued_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseBeSubstitueds', [
            'foreignKey' => 'course_be_substitued_id',
            'joinType' => 'INNER',
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
            ->dateTime('request_date')
            ->requirePresence('request_date', 'create')
            ->notEmptyDateTime('request_date');

        $validator
            ->boolean('department_approve')
            ->allowEmptyString('department_approve');

        $validator
            ->scalar('department_approve_by')
            ->maxLength('department_approve_by', 36)
            ->allowEmptyString('department_approve_by');

        $validator
            ->scalar('remark')
            ->allowEmptyString('remark');

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
        $rules->add($rules->existsIn(['course_for_substitued_id'], 'CourseForSubstitueds'));
        $rules->add($rules->existsIn(['course_be_substitued_id'], 'CourseBeSubstitueds'));

        return $rules;
    }

    function number_of_previously_sustitued_courses($student_id = null)
    {
    }

    function isSimilarCurriculum($data = null)
    {
        if (empty($data['CourseSubstitutionRequest']['course_for_substitued_id']) || empty($data['CourseSubstitutionRequest']['course_be_substitued_id'])) {
            return true;
        }
        //other_curriculum_id
        if (!empty($data['CourseSubstitutionRequest']['curriculum_id']) && !empty($data['CourseSubstitutionRequest']['other_curriculum_id'])) {
            if ($data['CourseSubstitutionRequest']['curriculum_id'] == $data['CourseSubstitutionRequest']['other_curriculum_id']) {
                $this->invalidate('error', 'You are trying to request course substitution for similar curriculum courses. You can not request similar curriculum courses for substitution.');
                return false;
            }
        }
        return true;
    }

    //count course substitution request not approved
    public function countSubstitutionRequest($departmentIds = null): int
    {
        $daysBack = FrozenDate::now()->subDays(DAYS_BACK_COURSE_SUBSTITUTION);

        $courseSubstitutions = TableRegistry::getTableLocator()->get('CourseSubstitutionRequests');

        $query = $courseSubstitutions->find()
            ->contain(['Students'])
            ->where([
                'Students.department_id IN' => (array)$departmentIds,
                'Students.graduated' => 0,
                'CourseSubstitutionRequests.department_approve IS' => null,
                'CourseSubstitutionRequests.request_date >=' => $daysBack
            ]);

        return $query->count();
    }
}
