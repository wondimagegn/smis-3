<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
            'propertyName' => 'PublishedCourse'
        ]);
        $this->belongsTo('ClassPeriods', [
            'foreignKey' => 'class_period_id',
            'joinType' => 'INNER',
            'propertyName' => 'ClassPeriod'
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
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

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
        $rules->add($rules->existsIn(['class_period_id'], 'ClassPeriods'));

        return $rules;
    }

    public function beforeDeleteCheckEligibility($id = null, $college_id = null)
    {

        $departments = $this->PublishedCourse->Department->find(
            'list',
            array('fields' => array('Department.id'), 'conditions' => array('Department.college_id' => $college_id))
        );
        $publishedCourses_id_array = $this->PublishedCourse->find(
            'list',
            array(
                'fields' => array('PublishedCourse.id'),
                'conditions' => array(
                    'PublishedCourse.drop' => 0,
                    "OR" => array(
                        array('PublishedCourse.college_id' => $college_id),
                        array('PublishedCourse.department_id' => $departments)
                    )
                )
            )
        );
        $count = $this->find(
            'count',
            array(
                'conditions' => array(
                    'ClassPeriodCourseConstraint.published_course_id' => $publishedCourses_id_array,
                    'ClassPeriodCourseConstraint.id' => $id
                )
            )
        );
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}
