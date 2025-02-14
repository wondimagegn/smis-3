<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
            ->scalar('type')
            ->maxLength('type', 100)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('code')
            ->maxLength('code', 10)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('user')
            ->maxLength('user', 36)
            ->requirePresence('user', 'create')
            ->notEmptyString('user');

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


    function generateCode($prefix='')
    {
        debug($prefix);
        $number = ClassRegistry::init('GraduateList')->find('count');
        $length = 8;
        $initialValue = substr(str_repeat(0, $length) . $number, -$length);

        $code = $this->find(
            'first',
            array('order' => array('CertificateVerificationCode.id DESC'))
        );
        if (isset($code) && !empty($code)) {
            // check if the code is string then extract string
            if(is_string($code['CertificateVerificationCode']["code"])){



                $extractedNumber=substr($code['CertificateVerificationCode']["code"],2,strlen($code['CertificateVerificationCode']["code"])) + 1;


                $filledExtractedNumber= substr(str_repeat(0, $length) . $extractedNumber, -$length);

            } else{

                $extractedNumber=$code['CertificateVerificationCode']["code"] + 1;
                $filledExtractedNumber= substr(str_repeat(0, $length) . $extractedNumber, -$length);

            }

            return $prefix.''.$filledExtractedNumber;
        } else{
            debug($initialValue);
        }

        return $prefix.''.$initialValue;
    }
}
