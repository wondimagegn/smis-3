<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseExamConstraintsTable extends Table
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

        $this->setTable('course_exam_constraints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->date('exam_date')
            ->allowEmptyDate('exam_date');

        $validator
            ->allowEmptyString('session');

        $validator
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmptyString('active');

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

        return $rules;
    }

    public function get_already_recorded_course_exam_constraint($published_course_id = null)
    {

        if (!empty($published_course_id)) {
            $courseExamConstraints = $this->find(
                'all',
                array(
                    'conditions' => array('CourseExamConstraint.published_course_id' => $published_course_id),
                    'order' => array('CourseExamConstraint.exam_date', 'CourseExamConstraint.session'),
                    'recursive' => -1
                )
            );
            $course_exam_constraints_by_date = array();
            foreach ($courseExamConstraints as $courseExamConstraint) {
                $course_exam_constraints_by_date[$courseExamConstraint['CourseExamConstraint']['exam_date']][$courseExamConstraint['CourseExamConstraint']['session']]['id'] = $courseExamConstraint['CourseExamConstraint']['id'];
                $course_exam_constraints_by_date[$courseExamConstraint['CourseExamConstraint']['exam_date']][$courseExamConstraint['CourseExamConstraint']['session']]['active'] = $courseExamConstraint['CourseExamConstraint']['active'];
            }
            return $course_exam_constraints_by_date;
        }
    }
}
