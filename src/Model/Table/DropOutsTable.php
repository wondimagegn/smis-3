<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DropOutsTable extends Table
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

        $this->setTable('drop_outs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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
            ->scalar('reason')
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason');

        $validator
            ->date('drop_date')
            ->requirePresence('drop_date', 'create')
            ->notEmptyDate('drop_date');

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

        return $rules;
    }

    function dropOutAfterLastRegistration($student_id = null, $current_academicyear = null)
    {
        $last_registration_date = $this->Student->CourseRegistration->find('first', array('conditions' => array('CourseRegistration.student_id' => $student_id), 'order' => array('CourseRegistration.created DESC'), 'recursive' => -1));
        if (!empty($last_registration_date)) {

            $check_dropout = $this->find('count', array(
                'conditions' => array(
                    'DropOut.student_id' => $student_id,
                    'DropOut.drop_date >= ' => $last_registration_date['CourseRegistration']['created']
                )
            ));

            if ($check_dropout > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

}
