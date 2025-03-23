<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GraduationStatusesTable extends Table
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

        $this->setTable('graduation_statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('cgpa', 'Please enter CGPA.')
            ->numeric('cgpa', 'Please enter a valid CGPA.')
            ->notEmptyString('status', 'Please enter graduation status.');

        return $validator;
    }


    function getStudentGraduationStatus($student_id = null)
    {
        $exam_status_detail = ClassRegistry::init('StudentExamStatus')->find('first', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id
            ),
            'recursive' => -1,
            'order' => array('StudentExamStatus.created DESC')
        ));

        $student_detail = ClassRegistry::init('Student')->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id
            ),
            'contain' => array(
                'GraduateList'
            )
        ));

        if (!empty($student_detail) && !empty($exam_status_detail)) {
            $options = array();

            if (isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
                $options['conditions'] = array('GraduationStatus.cgpa <= ' . $exam_status_detail['StudentExamStatus']['cgpa']);
                $options['conditions']['OR'][0] = array('GraduationStatus.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4));
                $options['conditions']['OR'][1] = array(
                    'GraduationStatus.applicable_for_current_student' => 1,
                    'GraduationStatus.program_id' => $student_detail['Student']['program_id'],
                    'GraduationStatus.academic_year <= ' . substr($student_detail['GraduateList']['graduate_date'], 0, 4)
                );
            } else {
                $options['conditions'] = array(
                    'GraduationStatus.academic_year <= ' . substr($student_detail['Student']['admissionyear'], 0, 4),
                    'GraduationStatus.cgpa <= ' . $exam_status_detail['StudentExamStatus']['cgpa']
                );
            }

            $options['order'] = array('GraduationStatus.academic_year DESC, GraduationStatus.cgpa DESC');

            $graduation_status_detail = $this->find('first', $options);

            if (isset($graduation_status_detail['GraduationStatus'])) {
                return $graduation_status_detail['GraduationStatus'];
            } else {
                return null;
            }
        } else {
            return false;
        }
    }
}
