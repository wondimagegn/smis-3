<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApplicablePayments Table
 */
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

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('sponsor_type')
            ->requirePresence('sponsor_type', 'create')
            ->notEmptyString('sponsor_type', 'Please provide sponsor type.');

        return $validator;
    }

    /**
     * Checks for duplicate applicable payment records
     *
     * @param array|null $data Applicable payment data
     * @return int Number of duplicate records found
     */
    public function duplication($data = null)
    {
        if (
            empty($data) ||
            empty($data['ApplicablePayment']) ||
            empty($data['ApplicablePayment']['sponsor_type']) ||
            empty($data['ApplicablePayment']['student_id']) ||
            empty($data['ApplicablePayment']['semester']) ||
            empty($data['ApplicablePayment']['academic_year'])
        ) {
            return 0;
        }

        return $this->find()
            ->where([
                'ApplicablePayments.student_id' => $data['ApplicablePayment']['student_id'],
                'ApplicablePayments.semester' => $data['ApplicablePayment']['semester'],
                'ApplicablePayments.academic_year' => $data['ApplicablePayment']['academic_year']
            ])
            ->count();
    }
}
