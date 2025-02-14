<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PaymentsTable extends Table
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

        $this->setTable('payments');
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
            ->scalar('academic_year')
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 4)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->scalar('reference_number')
            ->maxLength('reference_number', 20)
            ->requirePresence('reference_number', 'create')
            ->notEmptyString('reference_number');

        $validator
            ->numeric('fee_amount')
            ->requirePresence('fee_amount', 'create')
            ->notEmptyString('fee_amount');

        $validator
            ->boolean('tutition_fee')
            ->allowEmptyString('tutition_fee');

        $validator
            ->boolean('meal')
            ->allowEmptyString('meal');

        $validator
            ->boolean('accomodation')
            ->allowEmptyString('accomodation');

        $validator
            ->boolean('health')
            ->allowEmptyString('health');

        $validator
            ->dateTime('payment_date')
            ->requirePresence('payment_date', 'create')
            ->notEmptyDateTime('payment_date');

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

    function duplication ($data=null) {
        if (empty($data['Payment']['semester']) || empty($data['Payment']['academic_year'])
            || empty($data['Payment']['reference_number']) || empty($data['Payment']['fee_amount']) ) {
            return 0;
        }
        //fee_amount

        $count=$this->find('count',array('conditions'=>
            array('Payment.student_id'=>$data['Payment']['student_id'],
                'Payment.semester'=>$data['Payment']['semester'],
                'Payment.academic_year'=>$data['Payment']['academic_year'])));

        return $count;
    }
    public function paidPayment($student_id,$latestAcSemester){
        $allow=ClassRegistry::init('GeneralSetting')->allowRegistrationWithoutPayment($student_id);
        if($allow==1){
            return 1;
        } else {
            $pcount=$this->find('count',array('conditions'=>
                array('Payment.student_id'=>$student_id,
                    'Payment.semester'=>$latestAcSemester['semester'],
                    'Payment.academic_year'=>$latestAcSemester['academic_year'])));
            if($pcount>0){
                return 1;
            }

        }
        return 0;
    }
}
