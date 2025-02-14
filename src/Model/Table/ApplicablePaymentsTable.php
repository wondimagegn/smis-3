<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ApplicablePaymentsTable extends Table
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

        $this->setTable('applicable_payments');
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
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

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
            ->scalar('sponsor_type')
            ->maxLength('sponsor_type', 50)
            ->allowEmptyString('sponsor_type');

        $validator
            ->scalar('sponsor_name')
            ->maxLength('sponsor_name', 50)
            ->allowEmptyString('sponsor_name');

        $validator
            ->scalar('sponsor_address')
            ->allowEmptyString('sponsor_address');

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
        if (empty($data['ApplicablePayment']['sponsor_type'])) {
            return 0;
        }
        $count=$this->find('count',array('conditions'=>
            array('ApplicablePayment.student_id'=>$data['ApplicablePayment']['student_id'],
                'ApplicablePayment.semester'=>$data['ApplicablePayment']['semester'],
                'ApplicablePayment.academic_year'=>$data['ApplicablePayment']['academic_year'])));
        debug($count);
        return $count;
    }
}
