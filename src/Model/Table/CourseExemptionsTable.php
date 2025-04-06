<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\FrozenDate;

class CourseExemptionsTable extends Table
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

        $this->setTable('course_exemptions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ExcludedCourseFromTranscripts', [
            'foreignKey' => 'course_exemption_id',
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
            ->scalar('reason')
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason');

        $validator
            ->scalar('taken_course_title')
            ->maxLength('taken_course_title', 50)
            ->requirePresence('taken_course_title', 'create')
            ->notEmptyString('taken_course_title');

        $validator
            ->scalar('taken_course_code')
            ->maxLength('taken_course_code', 15)
            ->requirePresence('taken_course_code', 'create')
            ->notEmptyString('taken_course_code');

        $validator
            ->integer('course_taken_credit')
            ->requirePresence('course_taken_credit', 'create')
            ->notEmptyString('course_taken_credit');

        $validator
            ->boolean('department_accept_reject')
            ->allowEmptyString('department_accept_reject');

        $validator
            ->scalar('department_reason')
            ->requirePresence('department_reason', 'create')
            ->notEmptyString('department_reason');

        $validator
            ->boolean('registrar_confirm_deny')
            ->allowEmptyString('registrar_confirm_deny');

        $validator
            ->scalar('registrar_reason')
            ->requirePresence('registrar_reason', 'create')
            ->notEmptyString('registrar_reason');

        $validator
            ->uuid('department_approve_by')
            ->requirePresence('department_approve_by', 'create')
            ->notEmptyString('department_approve_by');

        $validator
            ->uuid('registrar_approve_by')
            ->requirePresence('registrar_approve_by', 'create')
            ->notEmptyString('registrar_approve_by');

        $validator
            ->scalar('transfer_from')
            ->maxLength('transfer_from', 100)
            ->allowEmptyString('transfer_from');

        $validator
            ->scalar('grade')
            ->maxLength('grade', 5)
            ->allowEmptyString('grade');

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
        $rules->add($rules->existsIn(['course_id'], 'Courses'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }

    function isCourseExempted($student_id = null, $course_id = null)
    {
        $count = $this->find('count', array(
                'conditions' => array(
                    'CourseExemption.student_id' => $student_id,
                    'CourseExemption.course_id' => $course_id,
                    'registrar_confirm_deny' => 1,
                    'department_accept_reject' => 1
                )
        ));
        return $count;
    }

    function getStudentCourseExemptionCredit($student_id)
    {
        $course = $this->find('all', array(
            'conditions' => array(
                'CourseExemption.student_id' => $student_id,
                'registrar_confirm_deny' => 1,
                'department_accept_reject' => 1
            ),
            'recursive' => -1
        ));

        $exemptionSums = 0;

        if (isset($course) && !empty($course)) {
            foreach ($course as $k => $v) {
                $exemptionSums += $v['CourseExemption']['course_taken_credit'];
            }
        }
        return $exemptionSums;
    }

    //count course substitution request not approved
    public function countExemptionRequest($roleId = null, $departmentIds = null, $collegeIds = null): int
    {
        $courseExemptions = TableRegistry::getTableLocator()->get('CourseExemptions');
        $daysBack = FrozenDate::now()->subDays(DAYS_BACK_COURSE_SUBSTITUTION);

        $query = $courseExemptions->find()
            ->contain(['Students'])
            ->where(['CourseExemptions.request_date >=' => $daysBack]);

        if ($roleId == ROLE_DEPARTMENT) {
            $query->where([
                'Students.department_id IN' => (array)$departmentIds,
                'Students.graduated' => 0,
                'CourseExemptions.department_accept_reject IS' => null
            ]);
        } elseif ($roleId == ROLE_REGISTRAR) {
            $query->where([
                'Students.graduated' => 0,
                'CourseExemptions.department_accept_reject IS NOT' => null,
                'CourseExemptions.registrar_confirm_deny IS' => null
            ]);

            if (!empty($departmentIds)) {
                $query->where([
                    'Students.department_id IN' => (array)$departmentIds
                ]);
            } elseif (!empty($collegeIds)) {
                $query->where([
                    'Students.department_id IS' => null,
                    'Students.college_id IN' => (array)$collegeIds
                ]);
            }
        } else {
            return 0;
        }

        return $query->count();
    }
    function studentExemptedCourseList($student_id)
    {
        $exemptedCourseLists = $this->find('all', array(
            'conditions' => array(
                'CourseExemption.student_id' => $student_id,
                'registrar_confirm_deny' => 1,
                'department_accept_reject' => 1
            ),
            'contain' => array('Course')
        ));

        return $exemptedCourseLists;
    }
}
