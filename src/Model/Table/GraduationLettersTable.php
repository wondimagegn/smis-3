<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GraduationLettersTable extends Table
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

        $this->setTable('graduation_letters');
        $this->setDisplayField('title');
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('type', 'Type of letter cannot be empty')
            ->notEmptyString('title', 'Letter title cannot be empty')
            ->notEmptyString('title_font_size', 'Please select title font size')
            ->numeric('title_font_size', 'Please use only numeric value for title font size')
            ->notEmptyString('content', 'Please enter the content')
            ->notEmptyString('content_font_size', 'Please select content font size')
            ->numeric('content_font_size', 'Please use only numeric value for content font size')
            ->notEmptyString('academic_year', 'Please select academic year');

        return $validator;
    }

    function getGraduationLetter($student_id = null, $language_proficiency = 1)
    {
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
            $options['conditions']['OR'][0] = array('GraduationLetter.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
            $options['conditions']['OR'][1] = array(
                'GraduationLetter.applicable_for_current_student' => 1,
                'GraduationLetter.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
            );
        } else {
            $options['conditions']['GraduationLetter.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
        }

        $options['conditions']['GraduationLetter.program_id'] = $student_detail['Student']['program_id'];
        $options['conditions']['GraduationLetter.program_type_id'] = $student_detail['Student']['program_type_id'];
        $options['conditions']['GraduationLetter.type'] = ($language_proficiency == 1 ? 'Language Proficiency' : 'To Whom It May Concern');
        $options['order'] = array('GraduationLetter.academic_year DESC');

        $optionsC = $options;
        $optionsD = $options;

        $optionsC['conditions']['GraduationLetter.department'] = 'c~' . $student_detail['Student']['college_id'];
        $optionsD['conditions']['GraduationLetter.department'] = $student_detail['Student']['department_id'];

        $GraduationLetter_detail_all = $this->find('first', $options);
        $GraduationLetter_detail_college = $this->find('first', $optionsC);
        $GraduationLetter_detail_department = $this->find('first', $optionsD);


        if (!empty($GraduationLetter_detail_department)) {
            return $GraduationLetter_detail_department;
        } elseif (!empty($GraduationLetter_detail_college)) {
            return $GraduationLetter_detail_college;
        } elseif (!empty($GraduationLetter_detail_all)) {
            return $GraduationLetter_detail_all;
        } else {
            return array();
        }
    }


    function getGraduationLetterByMass($student_ids = array(), $language_proficiency = 1)
    {
        $letter = array();

        if (!empty($student_ids)) {
            foreach ($student_ids as $k => $student_id) {
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
                    $options['conditions']['OR'][0] = array('GraduationLetter.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
                    $options['conditions']['OR'][1] = array(
                        'GraduationLetter.applicable_for_current_student' => 1,
                        'GraduationLetter.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
                    );
                } else {
                    $options['conditions']['GraduationLetter.academic_year <= '] = substr($student_detail['Student']['admissionyear'], 0, 4);
                }

                $options['conditions']['GraduationLetter.program_id'] = $student_detail['Student']['program_id'];
                $options['conditions']['GraduationLetter.program_type_id'] = $student_detail['Student']['program_type_id'];
                $options['conditions']['GraduationLetter.type'] = ($language_proficiency == 1 ? 'Language Proficiency' : 'To Whom It May Concern');
                $options['order'] = array('GraduationLetter.academic_year DESC');

                $optionsC = $options;
                $optionsD = $options;

                $optionsC['conditions']['GraduationLetter.department'] = 'c~' . $student_detail['Student']['college_id'];
                $optionsD['conditions']['GraduationLetter.department'] = $student_detail['Student']['department_id'];

                $GraduationLetter_detail_all = $this->find('first', $options);
                $GraduationLetter_detail_college = $this->find('first', $optionsC);
                $GraduationLetter_detail_department = $this->find('first', $optionsD);

                if (!empty($GraduationLetter_detail_department)) {
                    $letter[] = $GraduationLetter_detail_department;
                } elseif (!empty($GraduationLetter_detail_college)) {
                    $letter[] = $GraduationLetter_detail_college;
                } elseif (!empty($GraduationLetter_detail_all)) {
                    $letter[] = $GraduationLetter_detail_all;
                }
            }
        }
        return $letter;
    }
}
