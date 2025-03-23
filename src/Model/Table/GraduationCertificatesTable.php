<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GraduationCertificatesTable extends Table
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

        $this->setTable('graduation_certificates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
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
            ->notEmptyString('english_title', 'Certificate title cannot be empty')
            ->notEmptyString('amharic_title', 'Certificate title cannot be empty')
            ->notEmptyString('amharic_content', 'Please enter the Amharic content')
            ->notEmptyString('english_content', 'Please enter the English content')
            ->notEmptyString('academic_year', 'Please select the academic year');

        return $validator;
    }


    function getGraduationCertificate($student_id = null)
    {
        if (!empty($student_id)) {
            $student_detail = ClassRegistry::init('Student')->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                ),
                'contain' => array(
                    'GraduateList',
                    'Department' => array('id', 'name')
                )
            ));

            $options = array();

            if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
                $options['conditions']['OR'][0] = array('GraduationCertificate.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
                $options['conditions']['OR'][1] = array(
                    'GraduationCertificate.applicable_for_current_student' => 1,
                    'GraduationCertificate.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
                );
            } else {
                $options['conditions']['GraduationCertificate.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
            }

            $options['conditions']['GraduationCertificate.program_id'] = $student_detail['Student']['program_id'];
            $options['conditions']['GraduationCertificate.program_type_id'] = $student_detail['Student']['program_type_id'];
            $options['order'] = array('GraduationCertificate.academic_year DESC');
            $options['conditions']['GraduationCertificate.department'] = 0;

            $optionsC = $options;
            $optionsD = $options;

            $optionsC['conditions']['GraduationCertificate.department'] = 'c~' . $student_detail['Student']['college_id'];
            $optionsD['conditions']['GraduationCertificate.department'] = $student_detail['Student']['department_id'];

            $GraduationCertificate_detail_all = $this->find('first', $options);
            $GraduationCertificate_detail_college = $this->find('first', $optionsC);
            $GraduationCertificate_detail_department = $this->find('first', $optionsD);

            if (!empty($GraduationCertificate_detail_department['GraduationCertificate'])) {
                return $GraduationCertificate_detail_department;
            } elseif (!empty($GraduationCertificate_detail_college['GraduationCertificate'])) {
                return $GraduationCertificate_detail_college;
            } elseif (!empty($GraduationCertificate_detail_all['GraduationCertificate'])) {
                return $GraduationCertificate_detail_all;
            } else {
                return array();
            }
        }
    }


    function getGraduationCertificateForMassPrint($student_ids = array())
    {
        $student_certificate_list = array();

        if (!empty($student_ids)) {
            foreach ($student_ids as $key => $student_id) {
                $student_detail = ClassRegistry::init('Student')->find('first', array(
                    'conditions' => array(
                        'Student.id' => $student_id
                    ),
                    'contain' => array(
                        'GraduateList'
                    )
                ));

                $options = array();

                if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
                    $options['conditions']['OR'][0] = array('GraduationCertificate.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
                    $options['conditions']['OR'][1] = array(
                        'GraduationCertificate.applicable_for_current_student' => 1,
                        'GraduationCertificate.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
                    );
                } else {
                    $options['conditions']['GraduationCertificate.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
                }

                $options['conditions']['GraduationCertificate.program_id'] = $student_detail['Student']['program_id'];
                $options['conditions']['GraduationCertificate.program_type_id'] = $student_detail['Student']['program_type_id'];
                $options['conditions']['GraduationCertificate.department'] = 0;
                $options['order'] = array('GraduationCertificate.academic_year DESC');

                $optionsC = $options;
                $optionsD = $options;

                $optionsC['conditions']['GraduationCertificate.department'] = 'c~' . $student_detail['Student']['college_id'];
                $optionsD['conditions']['GraduationCertificate.department'] = $student_detail['Student']['department_id'];

                $GraduationCertificate_detail_college = $this->find('first', $optionsC);
                $GraduationCertificate_detail_department = $this->find('first', $optionsD);
                $GraduationCertificate_detail  = $this->find('first', $options);

                if (!empty($GraduationCertificate_detail_department)) {
                    $student_certificate_list[] = $GraduationCertificate_detail_department;
                } elseif (!empty($GraduationLetter_detail_college)) {
                    $student_certificate_list[] = $GraduationCertificate_detail_college;
                } elseif (!empty($GraduationLetter_detail_all)) {
                    $student_certificate_list[] = $GraduationCertificate_detail;
                }
            }
        }
        return $student_certificate_list;
    }
}
