<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StaffForExamsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('staff_for_exams');
        $this->setPrimaryKey('id');

        // Define Relationships
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Invigilators', [
            'foreignKey' => 'staff_for_exam_id',
        ]);

        $this->hasMany('InstructorExamExcludeDateConstraints', [
            'foreignKey' => 'staff_for_exam_id',
        ]);

        $this->hasMany('InstructorNumberOfExamConstraints', [
            'foreignKey' => 'staff_for_exam_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('college_id')
            ->requirePresence('college_id', 'create')
            ->notEmptyString('college_id', 'College is required.');

        $validator
            ->integer('staff_id')
            ->requirePresence('staff_id', 'create')
            ->notEmptyString('staff_id', 'Staff is required.');

        return $validator;
    }

    function getInvigilators($college_id = null, $acadamic_year = null, $semester = null, $exam_date = null, $session = null, $year_level = null) {
        $staffsForExam = array();
        $staffs = $this->find('all',
            array(
                'conditions' =>
                    array(
                        'StaffForExam.college_id' => $college_id,
                        'StaffForExam.academic_year' => $acadamic_year,
                        'StaffForExam.semester' => $semester,
                        'StaffForExam.id NOT IN (SELECT staff_for_exam_id FROM instructor_exam_exclude_date_constraints WHERE exam_date = \''.$exam_date.'\' AND session = \''.$session.'\')',
                        //Exclude already assigned invigilators
                        'StaffForExam.id NOT IN (SELECT staff_for_exam_id FROM invigilators WHERE exam_schedule_id IN (SELECT id FROM exam_schedules WHERE exam_date = \''.$exam_date.'\' AND session = \''.$session.'\'))',
                    ),
                'contain' =>
                    array(
                        'Staff',
                        'InstructorNumberOfExamConstraint' =>
                            array(
                                'conditions' =>
                                    array(
                                        'InstructorNumberOfExamConstraint.academic_year' => $acadamic_year,
                                        'InstructorNumberOfExamConstraint.semester' => $semester,
                                        'InstructorNumberOfExamConstraint.year_level_id' => $year_level
                                    )
                            )
                    )
            )
        );
        //debug($staffs);
        $i = 0;
        foreach($staffs as $staff) {
            $staffsForExam[$i]['id'] = $staff['StaffForExam']['id'];
            if(!empty($staff['InstructorNumberOfExamConstraint'])) {
                $staffsForExam[$i]['max_number_of_exam'] = $staff['InstructorNumberOfExamConstraint'][0]['max_number_of_exam'];
            }
            else {
                $staffsForExam[$i]['max_number_of_exam'] = 0;
            }
            $staffsForExam[$i]['assigned_exam'] = 0;
            $assigned_exams = $this->Invigilator->ExamSchedule->find('all',
                array(
                    'conditions' =>
                        array(
                            'ExamSchedule.acadamic_year' => $acadamic_year,
                            'ExamSchedule.semester' => $semester,
                            'ExamSchedule.id IN (SELECT exam_schedule_id FROM invigilators WHERE staff_for_exam_id = \''.$staff['StaffForExam']['id'].'\')'
                        ),
                    'contain' =>
                        array(
                            'PublishedCourse' =>
                                array(
                                    'Section' =>
                                        array(
                                            'YearLevel'
                                        )
                                ),
                        )
                )
            );
            foreach($assigned_exams as $assigned_exam) {
                if(strcasecmp($assigned_exam['PublishedCourse']['Section']['YearLevel']['name'], $year_level) == 0)
                    $staffsForExam[$i]['assigned_exam']++;
            }
            //debug($assigned_exams);
            $i++;
        }
        //debug($staffsForExam);
        return $staffsForExam;
    }

}
