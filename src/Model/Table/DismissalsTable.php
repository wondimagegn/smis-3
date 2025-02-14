<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DismissalsTable extends Table
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

        $this->setTable('dismissals');
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
            ->date('request_date')
            ->requirePresence('request_date', 'create')
            ->notEmptyDate('request_date');

        $validator
            ->date('acceptance_date')
            ->requirePresence('acceptance_date', 'create')
            ->notEmptyDate('acceptance_date');

        $validator
            ->boolean('for_good')
            ->notEmptyString('for_good');

        $validator
            ->date('dismisal_date')
            ->requirePresence('dismisal_date', 'create')
            ->notEmptyDate('dismisal_date');

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

    function dismissedBecauseOfDiscipelanryNotReadmitted($student_id=null,$current_academicyear=null) {
        $last_status_date=$this->Student->StudentExamStatus->find('first',
            array('conditions'=>array(
                'StudentExamStatus.student_id'=>$student_id),'order'=>array('StudentExamStatus.created DESC')));

        $check_dismissal = $this->find('count',
            array('conditions'=>array(
                'Dismissal.student_id'=>$student_id,
                'Dismissal.dismisal_date >= '=>$last_status_date['StudentExamStatus']['created'])));
        if ($check_dismissal>0) {
            if (!($this->Student->Readmission->is_readmitted ($student_id,$current_academicyear))){
                return true;
            } else {
                return false;
            }

        } else {

            return false;
        }

    }

    function dismissedBecauseOfDiscipelanryAfterRegistrationNotReadmitted($student_id=null,$current_academicyear=null) {
        $last_registration_date=$this->Student->CourseRegistration->find('first',
            array('conditions'=>array(
                'CourseRegistration.student_id'=>$student_id),'order'=>array('CourseRegistration.created DESC'),'recursive'=>-1));
        if(!empty($last_registration_date)){
            $check_dismissal = $this->find('count',
                array('conditions'=>array( 'Dismissal.student_id'=>$student_id,'Dismissal.dismisal_date >= '=>$last_registration_date['CourseRegistration']['created'])));
            if ($check_dismissal>0) {
                if (!($this->Student->Readmission->is_readmitted ($student_id,$current_academicyear))){
                    return true;
                } else {
                    return false;
                }

            } else {

                return false;
            }
        }
        return false;

    }
}
