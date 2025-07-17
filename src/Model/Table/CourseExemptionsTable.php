<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;

/**
 * CourseExemptions Table
 */
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

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT'
        ]);

        $this->hasMany('Attachments', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'CourseExemption'],
            'dependent' => true
        ]);

        $this->hasMany('ExcludedCoursesFromTranscripts', [
            'foreignKey' => 'course_exemption_id',
            'dependent' => false
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('reason')
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason', 'Please provide reason for request.')
            ->scalar('taken_course_title')
            ->requirePresence('taken_course_title', 'create')
            ->notEmptyString('taken_course_title', 'Please provide course title')
            ->scalar('taken_course_code')
            ->requirePresence('taken_course_code', 'create')
            ->notEmptyString('taken_course_code', 'Please provide course code.')
            ->numeric('course_taken_credit')
            ->requirePresence('course_taken_credit', 'create')
            ->notEmptyString('course_taken_credit', 'Please provide credit in number')
            ->greaterThanOrEqual('course_taken_credit', 0, 'Credit should be greater than or equal to zero')
            ->scalar('department_reason')
            ->requirePresence('department_reason', 'create')
            ->notEmptyString('department_reason', 'Please provide reason');

        return $validator;
    }

    /**
     * Checks if a course is exempted for a student
     *
     * @param int|null $studentId Student ID
     * @param int|null $courseId Course ID
     * @return int Number of exemptions
     */
    public function isCourseExempted(?int $studentId = null, ?int $courseId = null)
    {
        if (!$studentId || !$courseId) {
            return 0;
        }

        return $this->find()
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.course_id' => $courseId,
                'CourseExemptions.registrar_confirm_deny' => 1,
                'CourseExemptions.department_accept_reject' => 1
            ])
            ->count();
    }

    /**
     * Calculates total exemption credits for a student
     *
     * @param int|null $studentId Student ID
     * @return float Total exemption credits
     */
    public function getStudentCourseExemptionCredit(?int $studentId = null): float
    {
        if (!$studentId) {
            return 0.0;
        }

        $courses = $this->find()
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.registrar_confirm_deny' => 1,
                'CourseExemptions.department_accept_reject' => 1
            ])
            ->contain([
                'Courses' => ['fields' => ['id', 'credit']]
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $exemptionSums = 0.0;

        foreach ($courses as $course) {
            if (
                isset($course['Course']['credit']) &&
                is_numeric($course['Course']['credit']) &&
                !empty($course['Course']['credit'])
            ) {
                $exemptionSums += (float) $course['Course']['credit'];
            }
        }

        return $exemptionSums;
    }

    /**
     * Counts unapproved course exemption requests
     *
     * @param int|null $roleId User role ID
     * @param array|int|null $departmentIds Department IDs
     * @param array|int|null $collegeIds College IDs
     * @return int Number of unapproved requests
     */
    public function countExemptionRequest(?int $roleId = null, $departmentIds = null, $collegeIds = null): int
    {
        $options = [];

        if ($roleId === ROLE_DEPARTMENT && !empty($departmentIds)) {
            $options['conditions'] = [
                'Students.department_id IN' => (array) $departmentIds,
                'Students.graduated' => 0,
                'CourseExemptions.department_accept_reject IS NULL',
                'CourseExemptions.request_date >=' => (new Time())->modify('-' . DAYS_BACK_COURSE_SUBSTITUTION . ' days')->format('Y-m-d')
            ];
        } elseif ($roleId === ROLE_REGISTRAR) {
            if (!empty($departmentIds)) {
                $options['conditions'] = [
                    'Students.department_id IS NOT NULL',
                    'Students.department_id IN' => (array) $departmentIds,
                    'Students.graduated' => 0,
                    'CourseExemptions.department_accept_reject IS NOT NULL',
                    'CourseExemptions.registrar_confirm_deny IS NULL',
                    'CourseExemptions.request_date >=' => (new Time())->modify('-' . DAYS_BACK_COURSE_SUBSTITUTION . ' days')->format('Y-m-d')
                ];
            } elseif (!empty($collegeIds)) {
                $options['conditions'] = [
                    'Students.department_id IS NULL',
                    'Students.college_id IN' => (array) $collegeIds,
                    'Students.graduated' => 0,
                    'CourseExemptions.department_accept_reject IS NOT NULL',
                    'CourseExemptions.registrar_confirm_deny IS NULL',
                    'CourseExemptions.request_date >=' => (new Time())->modify('-' . DAYS_BACK_COURSE_SUBSTITUTION . ' days')->format('Y-m-d')
                ];
            }
        }

        if (empty($options)) {
            return 0;
        }

        return $this->find()
            ->where($options['conditions'])
            ->contain(['Students'])
            ->count();
    }

    /**
     * Retrieves list of exempted courses for a student
     *
     * @param int|null $studentId Student ID
     * @return array List of exempted courses
     */
    public function studentExemptedCourseList(?int $studentId = null): array
    {
        if (!$studentId) {
            return [];
        }

        return $this->find()
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.registrar_confirm_deny' => 1,
                'CourseExemptions.department_accept_reject' => 1
            ])
            ->contain(['Courses'])
            ->disableHydration()
            ->all()
            ->toArray();
    }
}
