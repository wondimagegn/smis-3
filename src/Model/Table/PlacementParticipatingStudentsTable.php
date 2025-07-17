<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class PlacementParticipatingStudentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_participating_students');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('PlacementRoundParticipants', [
            'foreignKey' => 'placement_round_participant_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('accepted_student_id', 'Accepted student ID must be numeric')
            ->notEmptyString('accepted_student_id')
            ->numeric('student_id', 'Student ID must be numeric')
            ->notEmptyString('student_id')
            ->numeric('program_id', 'Program ID must be numeric')
            ->notEmptyString('program_id')
            ->numeric('program_type_id', 'Program Type ID must be numeric')
            ->notEmptyString('program_type_id')
            ->notEmptyString('original_college_department', 'Original college department is required')
            ->notEmptyString('academic_year', 'Academic year is required')
            ->numeric('round', 'Round must be numeric')
            ->notEmptyString('round')
            ->numeric('total_placement_weight', 'Total placement weight must be numeric')
            ->numeric('female_placement_weight', 'Female placement weight must be numeric')
            ->numeric('disability_weight', 'Disability weight must be numeric')
            ->numeric('developing_region_weight', 'Developing region weight must be numeric')
            ->notEmptyString('placement_type', 'Placement type is required')
            ->numeric('status', 'Status must be numeric');

        return $validator;
    }

    public function reformat(array $data = [])
    {
        if (!empty($data)) {
            $reformatedData = [];
            $checkedBoxStudents = $data['PlacementParticipatingStudent']['approve'] ?? [];
            $selectedStudents = array_keys($checkedBoxStudents, 1);
            unset($data['PlacementParticipatingStudent']['approve']);
            $dataa['PlacementParticipatingStudent'] = $data['PlacementParticipatingStudent'];

            $isThePlacementRun = $this->find('count')
                ->where([
                    'PlacementParticipatingStudents.program_id IN' => Configure::read('programs_available_for_placement_preference'),
                    'PlacementParticipatingStudents.program_type_id IN' => Configure::read('program_types_available_for_placement_preference'),
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                    'OR' => [
                        'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL',
                        'PlacementParticipatingStudents.status' => 1
                    ]
                ])
                ->disableHydration()
                ->count();

            if ($isThePlacementRun) {
                return 1;
            }

            if (!empty($dataa['PlacementParticipatingStudent'])) {
                foreach ($dataa['PlacementParticipatingStudent'] as $dk => $dv) {
                    $alreadyPrepared = $this->find('count')
                        ->where([
                            'PlacementParticipatingStudents.accepted_student_id' => $dv['accepted_student_id'],
                            'PlacementParticipatingStudents.student_id' => $dv['student_id'],
                            'PlacementParticipatingStudents.program_id' => $dv['program_id'],
                            'PlacementParticipatingStudents.program_type_id' => $dv['program_type_id'],
                            'PlacementParticipatingStudents.academic_year' => $dv['academic_year'],
                            'PlacementParticipatingStudents.round' => $dv['round']
                        ])
                        ->disableHydration()
                        ->count();

                    if (in_array($dv['accepted_student_id'], $selectedStudents)) {
                        $reformatedData['PlacementParticipatingStudent'][$dk] = $dv;
                    }
                }
            }

            return $reformatedData;
        }

        return 0;
    }

    public function reformatForDelete(array $data = [])
    {
        if (!empty($data)) {
            $reformatedData = [];
            $checkedBoxStudents = $data['PlacementParticipatingStudent']['approve'] ?? [];
            $selectedStudents = array_keys($checkedBoxStudents, 1);
            unset($data['PlacementParticipatingStudent']['approve']);
            $dataa['PlacementParticipatingStudent'] = $data['PlacementParticipatingStudent'];

            $isThePlacementRun = $this->find('count')
                ->where([
                    'PlacementParticipatingStudents.program_id IN' => Configure::read('programs_available_for_placement_preference'),
                    'PlacementParticipatingStudents.program_type_id IN' => Configure::read('program_types_available_for_placement_preference'),
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                    'OR' => [
                        'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL',
                        'PlacementParticipatingStudents.status' => 1
                    ]
                ])
                ->disableHydration()
                ->count();

            if ($isThePlacementRun) {
                return 1;
            }

            if (!empty($dataa['PlacementParticipatingStudent'])) {
                foreach ($dataa['PlacementParticipatingStudent'] as $dk => $dv) {
                    $alreadyPrepared = $this->find('count')
                        ->where([
                            'PlacementParticipatingStudents.accepted_student_id' => $dv['accepted_student_id'],
                            'PlacementParticipatingStudents.student_id' => $dv['student_id'],
                            'PlacementParticipatingStudents.program_id' => $dv['program_id'],
                            'PlacementParticipatingStudents.program_type_id' => $dv['program_type_id'],
                            'PlacementParticipatingStudents.academic_year' => $dv['academic_year'],
                            'PlacementParticipatingStudents.round' => $dv['round']
                        ])
                        ->disableHydration()
                        ->count();

                    if ($alreadyPrepared && in_array($dv['accepted_student_id'], $selectedStudents) && !empty($dv['id'])) {
                        $reformatedData['PlacementParticipatingStudent'][$dk] = $dv['id'];
                    }
                }
            }

            return $reformatedData;
        }

        return 0;
    }

    public function getNextRound(string $academic_year, int $accepted_student_id)
    {
        $acceptedStudentdetail = $this->AcceptedStudents->find('first')
            ->where(['AcceptedStudents.id' => $accepted_student_id])
            ->contain(['Students'])
            ->disableHydration()
            ->first();

        $previousRound = $this->find('first')
            ->where([
                'PlacementParticipatingStudents.academic_year LIKE' => $academic_year . '%',
                'PlacementParticipatingStudents.accepted_student_id' => $acceptedStudentdetail['AcceptedStudent']['id']
            ])
            ->order([
                'PlacementParticipatingStudents.academic_year' => 'DESC',
                'PlacementParticipatingStudents.round' => 'DESC'
            ])
            ->disableHydration()
            ->first();

        if (!empty($previousRound)) {
            $applied_for = $previousRound['PlacementParticipatingStudent']['applied_for'];
        } else {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $student_section_exam_status = $studentsTable->get_student_section($acceptedStudentdetail['Student']['id'], null, null);

            if (!empty($student_section_exam_status['Section']) && $student_section_exam_status['Section']['academicyear'] == $academic_year && !$student_section_exam_status['Section']['archive']) {
                $nonBatchStudentlist = $studentsTable->find('list')
                    ->where([
                        'Students.college_id' => $acceptedStudentdetail['Student']['college_id'],
                        'Students.program_id IN' => Configure::read('programs_available_for_placement_preference'),
                        'Students.program_type_id IN' => Configure::read('program_types_available_for_placement_preference'),
                        'Students.department_id IS NULL',
                        'Students.academicyear' => $academic_year,
                        "Students.id IN (SELECT student_id FROM students_sections WHERE section_id = {$student_section_exam_status['Section']['id']} GROUP BY student_id, section_id)"
                    ])
                    ->select(['id'])
                    ->disableHydration()
                    ->toArray();

                $previousRound = $this->find('first')
                    ->where([
                        'PlacementParticipatingStudents.academic_year LIKE' => $academic_year . '%',
                        'PlacementParticipatingStudents.student_id IN' => array_keys($nonBatchStudentlist)
                    ])
                    ->order([
                        'PlacementParticipatingStudents.academic_year' => 'DESC',
                        'PlacementParticipatingStudents.round' => 'DESC'
                    ])
                    ->disableHydration()
                    ->first();
            } else {
                $batchAcceptedStudentlist = $this->AcceptedStudents->find('list')
                    ->where([
                        'AcceptedStudents.academicyear' => $acceptedStudentdetail['AcceptedStudent']['academicyear'],
                        'AcceptedStudents.college_id' => $acceptedStudentdetail['AcceptedStudent']['college_id']
                    ])
                    ->select(['id'])
                    ->disableHydration()
                    ->toArray();

                $previousRound = $this->find('first')
                    ->where([
                        'PlacementParticipatingStudents.academic_year LIKE' => $academic_year . '%',
                        'PlacementParticipatingStudents.accepted_student_id IN' => array_keys($batchAcceptedStudentlist)
                    ])
                    ->order([
                        'PlacementParticipatingStudents.academic_year' => 'DESC',
                        'PlacementParticipatingStudents.round' => 'DESC'
                    ])
                    ->disableHydration()
                    ->first();
            }

            if (!empty($previousRound)) {
                $applied_for = $previousRound['PlacementParticipatingStudent']['applied_for'];
            } else {
                if (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id'])) {
                    $applied_for = 'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'];
                } elseif (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && empty($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
                    $applied_for = 'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'];
                }
            }
        }

        if (!empty($applied_for)) {
            $participatingRound = $this->find('first')
                ->where([
                    'PlacementParticipatingStudents.applied_for' => $applied_for,
                    'PlacementParticipatingStudents.academic_year LIKE' => $academic_year . '%'
                ])
                ->order([
                    'PlacementParticipatingStudents.academic_year' => 'DESC',
                    'PlacementParticipatingStudents.round' => 'DESC'
                ])
                ->disableHydration()
                ->first();
        } else {
            $participatingRound = $this->find('first')
                ->where([
                    'PlacementParticipatingStudents.academic_year LIKE' => $academic_year . '%',
                    'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL'
                ])
                ->order([
                    'PlacementParticipatingStudents.academic_year' => 'DESC',
                    'PlacementParticipatingStudents.round' => 'DESC'
                ])
                ->disableHydration()
                ->first();
        }

        return !empty($participatingRound) ? $participatingRound['PlacementParticipatingStudent']['round'] + 1 : 1;
    }

    public function isCurrentPlacementRoundDefined(array $data = [])
    {
        $firstData = $data['PlacementRoundParticipant'][1];

        $participatingRound = $this->find('first')
            ->where([
                'PlacementParticipatingStudents.applied_for' => $firstData['applied_for'],
                'PlacementParticipatingStudents.academic_year LIKE' => $firstData['academic_year'] . '%',
                'PlacementParticipatingStudents.round' => $firstData['placement_round'],
                'PlacementParticipatingStudents.program_id' => $firstData['program_id'],
                'PlacementParticipatingStudents.program_type_id' => $firstData['program_type_id']
            ])
            ->order([
                'PlacementParticipatingStudents.academic_year' => 'DESC',
                'PlacementParticipatingStudents.round' => 'DESC'
            ])
            ->disableHydration()
            ->first();

        return !empty($participatingRound['PlacementParticipatingStudent']) ? $participatingRound['PlacementParticipatingStudent']['round'] : 0;
    }
}
