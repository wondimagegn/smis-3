<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CoursesTable extends Table
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

        $this->setTable('courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);
        $this->belongsTo('CourseCategories', [
            'foreignKey' => 'course_category_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('GradeTypes', [
            'foreignKey' => 'grade_type_id',
        ]);
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
        ]);
        $this->hasMany('Books', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('CourseExemptions', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('ExitExams', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('GraduationWorks', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('Journals', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('Prerequisites', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('Weblinks', [
            'foreignKey' => 'course_id',
        ]);
        $this->belongsToMany('Books', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'book_id',
            'joinTable' => 'courses_books',
        ]);
        $this->belongsToMany('Journals', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'journal_id',
            'joinTable' => 'courses_journals',
        ]);
        $this->belongsToMany('Staffs', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'staff_id',
            'joinTable' => 'courses_staffs',
        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'courses_students',
        ]);
        $this->belongsToMany('Weblinks', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'weblink_id',
            'joinTable' => 'courses_weblinks',
        ]);
    }


    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('course_title', 'Please provide course title, it is required.')
            ->notEmptyString('course_code', 'Please provide course code, it is required.')
            ->add('course_code', 'courseCodeSeparatedByMinus', [
                'rule' => function ($value, $context) {
                    return preg_match('/^[a-zA-Z]+-\d+$/', $value);
                },
                'message' => 'The course code should be separated with "-". Eg: COMP-200.'
            ])
            ->notEmptyString('credit', 'Please provide credit, it is required.')
            ->greaterThanOrEqual('credit', 0, 'Please provide a valid credit, greater than zero.')

            ->notEmptyString('lecture_hours', 'Please provide lecture hours, it is required.')
            ->greaterThanOrEqual('lecture_hours', 0, 'Please provide lecture hours, greater than or equal to zero.')

            ->notEmptyString('tutorial_hours', 'Please provide tutorial hours, it is required.')
            ->greaterThanOrEqual('tutorial_hours', 0, 'Please provide tutorial hours, greater than or equal to zero.')

            ->notEmptyString('course_status', 'Please provide course status, it is required.')

            ->numeric('curriculum_id', 'Please attach course to curriculum, it is required.')
            ->numeric('year_level_id', 'Please select course year level, it is required.')
            ->numeric('grade_type_id', 'Please select course grade type, it is required.')
            ->numeric('course_category_id', 'Please select course category, it is required.')

            ->notEmptyString('semester', 'Please select course semester, it is required.')

            ->notEmptyString('laboratory_hours', 'Please provide laboratory hours, it is required.')
            ->greaterThanOrEqual('laboratory_hours', 0, 'Please provide laboratory hours, greater than or equal to zero.');

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
        $rules->add($rules->existsIn(['curriculum_id'], 'Curriculums'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['course_category_id'], 'CourseCategories'));
        $rules->add($rules->existsIn(['grade_type_id'], 'GradeTypes'));
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));

        return $rules;
    }
}
