<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CourseSplitSections Model
 *
 * @property \App\Model\Table\SectionSplitForPublishedCoursesTable&\Cake\ORM\Association\BelongsTo $SectionSplitForPublishedCourses
 * @property \App\Model\Table\CourseInstructorAssignmentsTable&\Cake\ORM\Association\HasMany $CourseInstructorAssignments
 * @property \App\Model\Table\CourseSchedulesTable&\Cake\ORM\Association\HasMany $CourseSchedules
 * @property \App\Model\Table\UnschedulePublishedCoursesTable&\Cake\ORM\Association\HasMany $UnschedulePublishedCourses
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\CourseSplitSection get($primaryKey, $options = [])
 * @method \App\Model\Entity\CourseSplitSection newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CourseSplitSection[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CourseSplitSection|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseSplitSection saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseSplitSection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CourseSplitSection[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CourseSplitSection findOrCreate($search, callable $callback = null, $options = [])
 */
class CourseSplitSectionsTable extends Table
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

        $this->setTable('course_split_sections');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('SectionSplitForPublishedCourses', [
            'foreignKey' => 'section_split_for_published_course_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CourseInstructorAssignments', [
            'foreignKey' => 'course_split_section_id',
        ]);
        $this->hasMany('CourseSchedules', [
            'foreignKey' => 'course_split_section_id',
        ]);
        $this->hasMany('UnschedulePublishedCourses', [
            'foreignKey' => 'course_split_section_id',
        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'course_split_section_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'students_course_split_sections',
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
            ->scalar('section_name')
            ->maxLength('section_name', 20)
            ->allowEmptyString('section_name');

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
        $rules->add($rules->existsIn(['section_split_for_published_course_id'], 'SectionSplitForPublishedCourses'));

        return $rules;
    }
}
