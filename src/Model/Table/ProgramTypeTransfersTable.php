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

    public function getStudentProgramType($studentId = null, $academicYear = null, $semester = null)
    {
        $studentTransfers = $this->find()
            ->where(['ProgramTypeTransfers.student_id' => $studentId])
            ->order(['ProgramTypeTransfers.transfer_date' => 'ASC'])
            ->toArray();

        $studentDetail = $this->Students->find()
            ->where(['Students.id' => $studentId])
            ->contain(['AcceptedStudents'])
            ->first();

        if (!$studentDetail) {
            return null;
        }

        $programTypeId = $studentDetail->program_type_id;
        $sysAcademicYear = $studentDetail->accepted_student->academic_year;
        $sysSemester = 'I';

        do {
            foreach ($studentTransfers as $transfer) {
                if ($sysAcademicYear == $transfer->academic_year && $sysSemester == $transfer->semester) {
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
                    $sysAcademicYear = (substr($sysAcademicYear, 0, 4) + 1) . '/' . substr((substr($sysAcademicYear, 0, 4) + 2), 2, 2);
                }
            } else {
                return $programTypeId;
            }
        } while ($sysAcademicYear !== '3000/01');

        return $programTypeId;
    }
}
