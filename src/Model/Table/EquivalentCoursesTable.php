<?php
namespace App\Model\Table;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
class EquivalentCoursesTable extends Table
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

        $this->setTable('equivalent_courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('CoursesForSubstituted', [
            'className' => 'Courses',
            'foreignKey' => 'course_for_substituted_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('CoursesBeSubstituted', [
            'className' => 'Courses',
            'foreignKey' => 'course_be_substituted_id',
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
            ->integer('course_for_substituted_id')
            ->requirePresence('course_for_substituted_id', 'create')
            ->notEmptyString('course_for_substituted_id', 'Please specify the course to substitute.');

        $validator
            ->integer('course_be_substituted_id')
            ->requirePresence('course_be_substituted_id', 'create')
            ->notEmptyString('course_be_substituted_id', 'Please specify the substituted course.')
            ->add('course_be_substituted_id', 'differentCourse', [
                'rule' => function ($value, $context) {

                    return $value !== ($context['data']['course_for_substituted_id'] ?? null);
                },
                'message' => 'The substituted course cannot be the same as the course to substitute.'
            ]);

        $validator
            ->add('curriculum_id', 'similarCurriculum', [
                'rule' => [$this, 'isSimilarCurriculum'],
                'message' => 'You cannot map courses from the same curriculum.',
                'provider' => 'table'
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object for validating application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {

        $rules->add(
            $rules->existsIn(['course_for_substituted_id'],
                'CoursesForSubstituted',
                'The course to substitute does not exist.')
        );
        $rules->add(
            $rules->existsIn(['course_be_substituted_id'],
                'CoursesBeSubstituted',
                'The substituted course does not exist.')
        );

        return $rules;
    }

    /**
     * Validates that courses are not from the same curriculum.
     *
     * @param mixed $value The curriculum ID.
     * @param array $context The validation context.
     * @return bool True if valid, false otherwise.
     */
    public function isSimilarCurriculum($value, array $context)
    {

        if (empty($context['data']['curriculum_id']) || empty($context['data']['other_curriculum_id'])) {
            return true;
        }

        if ($context['data']['curriculum_id'] == $context['data']['other_curriculum_id']) {
            return false;
        }

        return true;
    }

    /**
     * Checks if deleting an equivalent course mapping is allowed based on student grades.
     *
     * @param int|null $id The equivalent course ID.
     * @param int|null $departmentId The department ID.
     * @return bool True if deletion is allowed, false otherwise.
     */
    public function checkStudentTakingEquivalentCourseAndDenyDelete($id = null, $departmentId = null)
    {

        if (!$id || !$departmentId) {
            return false;
        }

        $equivalentCourse = $this->find()
            ->select(['course_be_substituted_id'])
            ->where(['EquivalentCourses.id' => $id])
            ->first();

        if (!$equivalentCourse) {
            return false;
        }

        $course = TableRegistry::getTableLocator()->get('Courses')
            ->find()
            ->select(['curriculum_id'])
            ->where(['id' => $equivalentCourse->course_be_substituted_id])
            ->first();

        if (!$course) {
            return false;
        }

        $courseIds = TableRegistry::getTableLocator()->get('Courses')
            ->find('list', ['valueField' => 'id'])
            ->where(['curriculum_id' => $course->curriculum_id])
            ->toArray();

        $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list', ['valueField' => 'id'])
            ->where([
                'course_id IN' => $courseIds,
                'department_id' => $departmentId
            ])
            ->toArray();

        if (empty($publishedCourseIds)) {
            return true;
        }

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        foreach ($publishedCourseIds as $publishedCourseId) {
            if ($examGradesTable->isGradeSubmitted($publishedCourseId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieves the credit value of a course or its equivalent.
     *
     * @param int|null $courseId The course ID.
     * @param int|null $studentAttachedCurriculum The curriculum ID.
     * @return float Credit value or 0 if not found.
     */
    public function equivalentCreditOfCourse($courseId = null, $studentAttachedCurriculum = null)
    {

        if (!$courseId || !$studentAttachedCurriculum) {
            return 0;
        }

        $course = TableRegistry::getTableLocator()->get('Courses')
            ->find()
            ->select(['credit'])
            ->where([
                'id' => $courseId,
                'curriculum_id' => $studentAttachedCurriculum
            ])
            ->first();

        if ($course) {
            return (float)$course->credit;
        }

        $equivalentCourseIds = $this->find('list', [
            'keyField' => 'course_for_substituted_id',
            'valueField' => 'course_for_substituted_id'
        ])
            ->where(['course_be_substituted_id' => $courseId])
            ->toArray();

        if (!empty($equivalentCourseIds)) {
            $equivalentCourse = TableRegistry::getTableLocator()->get('Courses')
                ->find()
                ->select(['credit'])
                ->where([
                    'id IN' => $equivalentCourseIds,
                    'curriculum_id' => $studentAttachedCurriculum
                ])
                ->first();

            if ($equivalentCourse) {
                return (float)$equivalentCourse->credit;
            }
        }

        return 0;
    }

    /**
     * Checks if a course or its equivalent exists in the student’s curriculum.
     *
     * @param int|null $courseId The course ID.
     * @param int|null $studentAttachedCurriculum The curriculum ID.
     * @return bool True if exists, false otherwise.
     */
    public function checkCourseHasEquivalentCourse($courseId = null, $studentAttachedCurriculum = null)
    {

        if (!$courseId || !$studentAttachedCurriculum) {
            return false;
        }

        $courseExists = TableRegistry::getTableLocator()->get('Courses')
                ->find()
                ->where([
                    'id' => $courseId,
                    'curriculum_id' => $studentAttachedCurriculum
                ])
                ->count() > 0;

        if ($courseExists) {
            return true;
        }

        $equivalentCourseIds = $this->find('list', [
            'keyField' => 'course_for_substituted_id',
            'valueField' => 'course_for_substituted_id'
        ])
            ->where(['course_be_substituted_id' => $courseId])
            ->toArray();

        if (!empty($equivalentCourseIds)) {
            return TableRegistry::getTableLocator()->get('Courses')
                    ->find()
                    ->where([
                        'id IN' => $equivalentCourseIds,
                        'curriculum_id' => $studentAttachedCurriculum
                    ])
                    ->count() > 0;
        }

        return false;
    }

    /**
     * Retrieves valid equivalent course IDs for a given course and curriculum.
     *
     * @param int|null $courseId The course ID.
     * @param int|null $studentAttachedCurriculum The curriculum ID.
     * @param int $type The type of equivalence (default 1).
     * @return array List of equivalent course IDs.
     */
    public function validEquivalentCourse($courseId = null, $studentAttachedCurriculum = null, $type = 1)
    {

        if (!$courseId) {
            return [];
        }

        $courseExists = $studentAttachedCurriculum ? TableRegistry::getTableLocator()->get('Courses')
                ->find()
                ->where([
                    'id' => $courseId,
                    'curriculum_id' => $studentAttachedCurriculum
                ])
                ->count() > 0 : false;

        $equivalentCourseIds = [];

        if ($courseExists) {
            $equivalentCourseIds = $this->find('list', [
                'keyField' => 'course_be_substituted_id',
                'valueField' => 'course_be_substituted_id'
            ])
                ->where(['course_for_substituted_id' => $courseId])
                ->toArray();
        } else {
            $equivalentCourseIds = $this->find('list', [
                'keyField' => 'course_for_substituted_id',
                'valueField' => 'course_for_substituted_id'
            ])
                ->where(['course_be_substituted_id' => $courseId])
                ->toArray();

            if (!empty($equivalentCourseIds) && $studentAttachedCurriculum) {
                $courseLists = TableRegistry::getTableLocator()->get('Courses')
                    ->find('list', ['valueField' => 'id'])
                    ->where([
                        'id IN' => $equivalentCourseIds,
                        'curriculum_id' => $studentAttachedCurriculum
                    ])
                    ->toArray();

                if (!empty($courseLists)) {
                    $additionalEquivalents = $this->find('list', [
                        'keyField' => 'course_be_substituted_id',
                        'valueField' => 'course_be_substituted_id'
                    ])
                        ->where(['course_for_substituted_id IN' => $courseLists])
                        ->toArray();

                    $equivalentCourseIds = array_merge($courseLists, $additionalEquivalents);
                }
            }
        }

        return array_unique($equivalentCourseIds);
    }

    /**
     * Retrieves the course category of an equivalent course.
     *
     * @param int|null $courseId The course ID.
     * @param int|null $studentAttachedCurriculum The curriculum ID.
     * @return string|null The category name or null if not found.
     */
    public function courseEquivalentCategory($courseId = null, $studentAttachedCurriculum = null)
    {

        if (!$courseId || !$studentAttachedCurriculum) {
            return null;
        }

        $equivalentCourseIds = $this->find('list', [
            'keyField' => 'course_for_substituted_id',
            'valueField' => 'course_for_substituted_id'
        ])
            ->where(['course_be_substituted_id' => $courseId])
            ->toArray();

        if (empty($equivalentCourseIds)) {
            return null;
        }

        $course = TableRegistry::getTableLocator()->get('Courses')
            ->find()
            ->where([
                'id IN' => $equivalentCourseIds,
                'curriculum_id' => $studentAttachedCurriculum
            ])
            ->contain(['CourseCategories' => ['fields' => ['name']]])
            ->first();

        return $course && $course->course_category ? $course->course_category->name : null;
    }

    /**
     * Checks if an equivalent course is marked as major.
     *
     * @param int|null $courseId The course ID.
     * @param int|null $studentAttachedCurriculum The curriculum ID.
     * @return int 1 if major, 0 otherwise.
     */
    public function isEquivalentCourseMajor($courseId = null, $studentAttachedCurriculum = null)
    {

        if (!$courseId || !$studentAttachedCurriculum) {
            return 0;
        }

        $equivalentCourseIds = $this->find('list', [
            'keyField' => 'course_for_substituted_id',
            'valueField' => 'course_for_substituted_id'
        ])
            ->where(['course_be_substituted_id' => $courseId])
            ->toArray();

        if (empty($equivalentCourseIds)) {
            return 0;
        }

        $course = TableRegistry::getTableLocator()->get('Courses')
            ->find()
            ->select(['major'])
            ->where([
                'id IN' => $equivalentCourseIds,
                'curriculum_id' => $studentAttachedCurriculum
            ])
            ->first();

        return $course && $course->major ? 1 : 0;
    }
}

