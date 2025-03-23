<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MedicalHistoriesTable extends Table
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

        $this->setTable('medical_histories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {

        $validator
            ->notEmptyString('record_type', 'Please select record type.')
            ->notEmptyString('details', 'Please provide medical history details.');

        return $validator;
    }

    public function get_student_details_for_health($student_id = null)
    {

        if (!empty($student_id)) {
            $students = $this->Student->find(
                'first',
                array(
                    'conditions' => array('Student.id' => $student_id),
                    'fields' => array(
                        'Student.id',
                        'Student.studentnumber',
                        'Student.full_name',
                        'Student.card_number',
                        'Student.gender',
                        'Student.birthdate'
                    ),
                    'contain' => array(
                        'College' => array('fields' => array('College.name')),
                        'Department' => array('fields' => array('Department.name')),
                        'Program' => array('fields' => array('Program.name')),
                        'ProgramType' => array('fields' => array('ProgramType.name'))
                    )
                )
            );
            return $students;
        }
    }
}
