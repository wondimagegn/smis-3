<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProgramTypeTransfersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('program_type_transfers');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        // Add associations
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);

        // Add timestamp behavior
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year', 'Please provide academic year.');

        $validator
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester', 'Please provide semester.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }

    public function getProgramTransferDate($data = null)
    {
        $orderedItem = $this->find()
            ->where(['ProgramTypeTransfers.student_id' => $data['ProgramTypeTransfers']['student_id']])
            ->order(['ProgramTypeTransfers.transfer_date' => 'DESC'])
            ->first();

        if ($orderedItem) {
            $check1 = $orderedItem->transfer_date;
            $check2 = sprintf(
                '%s-%s-%s',
                $data['ProgramTypeTransfers']['transfer_date']['year'],
                $data['ProgramTypeTransfers']['transfer_date']['month'],
                $data['ProgramTypeTransfers']['transfer_date']['day']
            );

            if ($check2 < $check1) {
                return false;
            }
        }

        return true;
    }


    /**
     * Retrieves a student's program type for a given academic year and semester
     *
     * @param int|null $studentId Student ID
     * @param string|null $academicYear Academic year (e.g., "2024/25")
     * @param string|null $semester Semester (e.g., "I", "II", "III")
     * @return int|null Program type ID or null if not found
     */
    public function getStudentProgramType($studentId = null, $academicYear = null, $semester = null): ?int
    {
        if (!$studentId) {
            return null;
        }

        $studentTransfers = $this->find()
            ->where(['ProgramTypeTransfers.student_id' => $studentId])
            ->order(['ProgramTypeTransfers.transfer_date' => 'ASC'])
            ->toArray();

        $studentDetail = $this->Students->find()
            ->where(['Students.id' => $studentId])
            ->contain(['AcceptedStudents'])
            ->first();

        if (!$studentDetail || !$studentDetail->program_type_id) {
            return null;
        }

        $programTypeId = $studentDetail->program_type_id;

        // Check if accepted_student and academic_year are valid
        if (!$studentDetail->accepted_student || empty($studentDetail->accepted_student->academic_year)) {
            return $programTypeId; // Fallback to student's program_type_id
        }

        $sysAcademicYear = $studentDetail->accepted_student->academic_year;
        $sysSemester = 'I';

        // Validate academic year format (e.g., "2024/25")
        if (!preg_match('/^\d{4}\/\d{2}$/', $sysAcademicYear)) {
            return $programTypeId; // Fallback if format is invalid
        }

        do {
            foreach ($studentTransfers as $transfer) {
                if ($sysAcademicYear === $transfer->academic_year && $sysSemester === $transfer->semester) {
                    $programTypeId = $transfer->program_type_id;
                }
            }

            if (!($academicYear === $sysAcademicYear && $semester === $sysSemester)) {
                if ($sysSemester === 'I') {
                    $sysSemester = 'II';
                } elseif ($sysSemester === 'II') {
                    $sysSemester = 'III';
                } else {
                    $sysSemester = 'I';
                    // Safely increment academic year
                    $yearParts = explode('/', $sysAcademicYear);
                    $startYear = (int)$yearParts[0];
                    $sysAcademicYear = ($startYear + 1) . '/' . sprintf('%02d', ($startYear + 2) % 100);
                }
            } else {
                return $programTypeId;
            }
        } while ($sysAcademicYear !== '3000/01');

        return $programTypeId;
    }
}
