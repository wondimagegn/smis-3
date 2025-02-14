<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TranscriptFootersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('transcript_footers');
        $this->setPrimaryKey('id');
        $this->setDisplayField('academic_year');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year', 'Academic year is required.');

        return $validator;
    }

    public function getStudentTranscriptFooter($studentId = null)
    {
        if (!$studentId) {
            return [];
        }

        $studentsTable = $this->getTableLocator()->get('Students');
        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain(['GraduateLists'])
            ->first();

        if (!$studentDetail) {
            return [];
        }

        $admissionYear = substr($studentDetail->admissionyear, 0, 4);
        $conditions = ['TranscriptFooters.academic_year <=' => $admissionYear];

        if (!empty($studentDetail->graduate_list) && !empty($studentDetail->graduate_list->graduate_date)) {
            $graduateYear = substr($studentDetail->graduate_list->graduate_date, 0, 4);
            $conditions = [
                'OR' => [
                    ['TranscriptFooters.academic_year <=' => $admissionYear],
                    [
                        'TranscriptFooters.applicable_for_current_student' => 1,
                        'TranscriptFooters.academic_year <=' => $graduateYear
                    ]
                ]
            ];
        }

        return $this->find()
            ->where($conditions)
            ->order(['TranscriptFooters.academic_year' => 'DESC'])
            ->first();
    }
}
