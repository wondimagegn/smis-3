<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InstructorNumberOfExamConstraintsTable extends Table
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

        $this->setTable('instructor_number_of_exam_constraints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
        ]);
        $this->belongsTo('StaffForExams', [
            'foreignKey' => 'staff_for_exam_id',
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {

        $validator
            ->notEmptyString('max_number_of_exam', 'Please provide number of exams.')
            ->numeric('max_number_of_exam', 'Please provide a valid number.');

        return $validator;
    }

    public function get_maximum_year_levels_of_college($college_id = null)
    {

        $departments = $this->Staff->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $college_id), 'fields' => array('Department.id'))
        );
        $largest_yearLevel_department_id = null;
        $yearLevel_count = 0;
        foreach ($departments as $department_id) {
            $yearLevel_count_latest = $this->Staff->Department->YearLevel->find(
                'count',
                array('conditions' => array('YearLevel.department_id' => $department_id))
            );
            if ($yearLevel_count_latest > $yearLevel_count) {
                $yearLevel_count = $yearLevel_count_latest;
                $largest_yearLevel_department_id = $department_id;
            }
        }

        $yearLevels = null;
        if (!empty($largest_yearLevel_department_id)) {
            $yearLevels = $this->Staff->Department->YearLevel->find(
                'list',
                array(
                    'conditions' => array('YearLevel.department_id' => $largest_yearLevel_department_id),
                    'fields' => array('name', 'name')
                )
            );
        }
        return $yearLevels;
    }

    public function alreadyRecorded(
        $college_id = null,
        $selected_academicyear = null,
        $selected_semester = null,
        $selected_year_level = null,
        $selected_instructor_id = null
    ) {

        foreach ($selected_year_level as $ylk => $ylv) {
            $repeation = $this->find(
                'count',
                array(
                    'conditions' => array(
                        'InstructorNumberOfExamConstraint.college_id' => $college_id,
                        'InstructorNumberOfExamConstraint.academic_year' => $selected_academicyear,
                        'InstructorNumberOfExamConstraint.semester' => $selected_semester,
                        'InstructorNumberOfExamConstraint.year_level_id' => $ylv,
                        "OR" => array(
                            'InstructorNumberOfExamConstraint.staff_id' => $selected_instructor_id,
                            'InstructorNumberOfExamConstraint.staff_for_exam_id' => $selected_instructor_id
                        )
                    )
                )
            );
            if ($repeation > 0) {
                $this->invalidate(
                    'already_recorded_instructor_number_of_exam',
                    'Instructor number of exam is already recorded for ' . $ylv . ' year students'
                );
                return true;
            }
        }
        return false;
    }
}
