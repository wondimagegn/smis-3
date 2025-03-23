<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExceptionMealAssignmentsTable extends Table
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

        $this->setTable('exception_meal_assignments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MealHalls', [
            'foreignKey' => 'meal_hall_id',
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
            ->greaterThanOrEqualField('end_date', 'start_date', 'End date should be greater than start date.');

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

        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['meal_hall_id'], 'MealHalls'));

        return $rules;
    }

    /**
     * 1 in the exception and allowed in the given date
     * 2 in the exception and denied in the given date
     * 3 nothing
     */
    public function isInException($student_id = null, $meal_hall_id = null)
    {

        $allow = $this->find('count', array(
            'conditions' => array(
                'ExceptionMealAssignment.student_id' => $student_id,
                'ExceptionMealAssignment.start_date <=' => date('Y-m-d'),
                'ExceptionMealAssignment.end_date >=' => date('Y-m-d'),
                'ExceptionMealAssignment.accept_deny' => 1,
                'ExceptionMealAssignment.meal_hall_id' => $meal_hall_id
            )
        ));
        if ($allow > 0) {
            return 1;
        }
        // s>today and e<today

        $deny = $this->find('count', array(
            'conditions' => array(
                'ExceptionMealAssignment.student_id'
                => $student_id,
                'ExceptionMealAssignment.start_date <=' => date('Y-m-d'),
                'ExceptionMealAssignment.end_date >=' =>
                    date('Y-m-d'),
                'ExceptionMealAssignment.accept_deny' => -1,
                'ExceptionMealAssignment.meal_hall_id' => $meal_hall_id
            )
        ));

        if ($deny > 0) {
            return 2;
        }
        return 3;
    }

    public function checkDuplication($data = null)
    {

        foreach ($data['ExceptionMealAssignment'] as $in => $val) {
            $check = $this->find('count', array(
                'conditions' => array(
                    'ExceptionMealAssignment.start_date' => $val['start_date']['year'] . '-' . $val['start_date']['month'] . '-' . $val['start_date']['day'],
                    'ExceptionMealAssignment.student_id' => $val['student_id']
                )
            ));
            $student_name = $this->Student->field('full_name', array('Student.id' => $val['student_id']));

            if ($check > 0) {
                $this->invalidate(
                    'error',
                    'You have already put  student ' . $student_name . ' in exception
                        for ' . $val['start_date']['year'] . '-' . $val['start_date']['month'] . '-' . $val['start_date']['day'] . ' date '
                );
                return false;
            }
        }
        return true;
    }
}
