<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassPeriodCourseConstraints Table
 */
class ClassPeriodCourseConstraintsTable extends Table
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

        $this->setTable('class_period_course_constraints');
        $this->setDisplayField('published_course_id');
        $this->setPrimaryKey('id');

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('ClassPeriods', [
            'foreignKey' => 'class_period_id',
            'joinType' => 'LEFT',
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
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Checks if a class period course constraint can be deleted
     *
     * @param int|null $id Constraint ID
     * @param int|null $collegeId College ID
     * @return bool True if eligible for deletion, false otherwise
     */
    public function checkDeleteEligibility($id = null, $collegeId = null): bool
    {
        if (!$id || !$collegeId) {
            return false;
        }

        $departmentsTable = $this->PublishedCourses->Departments;
        $departmentIds = $departmentsTable->find('list')
            ->select(['Departments.id'])
            ->where(['Departments.college_id' => $collegeId])
            ->toArray();

        $publishedCourseIds = $this->PublishedCourses->find('list')
            ->select(['PublishedCourses.id'])
            ->where([
                'PublishedCourses.drop' => 0,
                'OR' => [
                    ['PublishedCourses.college_id' => $collegeId],
                    ['PublishedCourses.department_id IN' => array_values($departmentIds)]
                ]
            ])
            ->toArray();

        $count = $this->find()
            ->where([
                'ClassPeriodCourseConstraints.published_course_id IN' => array_values($publishedCourseIds),
                'ClassPeriodCourseConstraints.id' => $id
            ])
            ->count();

        return $count > 0;
    }
}
