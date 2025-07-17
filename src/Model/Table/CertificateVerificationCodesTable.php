<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * CertificateVerificationCodes Table
 */
class CertificateVerificationCodesTable extends Table
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

        $this->setTable('certificate_verification_codes');
        $this->setDisplayField('code');
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
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Generates a unique verification code
     *
     * @param string $prefix Code prefix
     * @return string Generated code
     */
    public function generateCode(string $prefix = ''): string
    {
        $length = 8;
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $number = $graduateListsTable->find()->count();
        $initialValue = str_pad($number, $length, '0', STR_PAD_LEFT);

        $lastCode = $this->find()
            ->select(['code'])
            ->order(['CertificateVerificationCodes.id' => 'DESC'])
            ->first();

        if ($lastCode && !empty($lastCode->code)) {
            $extractedNumber = (int)substr($lastCode->code, strlen($prefix)) + 1;
            $filledNumber = str_pad($extractedNumber, $length, '0', STR_PAD_LEFT);
            return $prefix . $filledNumber;
        }

        return $prefix . $initialValue;
    }
}
