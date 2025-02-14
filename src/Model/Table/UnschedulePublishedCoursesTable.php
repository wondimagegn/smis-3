<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UnschedulePublishedCoursesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('unschedule_published_courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // Associations
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('CourseSplitSections', [
            'foreignKey' => 'course_split_section_id',
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
            ->integer('period_length')
            ->requirePresence('period_length', 'create')
            ->notEmptyString('period_length');

        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

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
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));
        $rules->add($rules->existsIn(['course_split_section_id'], 'CourseSplitSections'));

        return $rules;
    }
}
