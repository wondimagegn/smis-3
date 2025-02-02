<?php
App::uses('AppModel', 'Model');
/**
 * CertificateVerificationCode Model
 *
 */
class CertificateVerificationCode extends AppModel
{
    var $name = 'CertificateVerificationCode';
    var $displayField = 'code';
    var $belongsTo = array(
        'Student' => array(
            'className' => 'Student',
            'foreignKey' => 'student_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );


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