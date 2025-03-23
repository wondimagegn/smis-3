<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GradeScalePublishedCoursesTable extends Table
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

        $this->setTable('grade_scale_published_courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('GradeScales', [
            'foreignKey' => 'grade_scale_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
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
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

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
        $rules->add($rules->existsIn(['grade_scale_id'], 'GradeScales'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));

        return $rules;
    }
}
