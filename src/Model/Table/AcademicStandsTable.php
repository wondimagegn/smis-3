<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;

/**
 * AcademicStands Table
 */
class AcademicStandsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('academic_stands');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('AcademicStatuses', [
            'foreignKey' => 'academic_status_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('AcademicRules', [
            'foreignKey' => 'academic_stand_id',
            'dependent' => true,
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
            ->allowEmptyString('id', null, 'create')
            ->numeric('program_id', 'Program ID must be numeric.')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Program ID is required.')
            ->numeric('academic_status_id', 'Academic status ID must be numeric.')
            ->requirePresence('academic_status_id', 'create')
            ->notEmptyString('academic_status_id', 'Select the academic status you want to define rule.')
            ->boolean('status_visible', 'Status visible must be a boolean.')
            ->requirePresence('status_visible', 'create')
            ->notEmptyString('status_visible', 'Status visible is required.')
            ->scalar('year_level_id')
            ->requirePresence('year_level_id', 'create')
            ->notEmptyString('year_level_id', 'Select year level.')
            ->scalar('semester')
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester', 'Select semester.')
            ->scalar('academic_year_from')
            ->requirePresence('academic_year_from', 'create')
            ->notEmptyString('academic_year_from', 'Academic year from is required.')
            ->boolean('applicable_for_all_current_student', 'Applicable for all current students must be a boolean.')
            ->requirePresence('applicable_for_all_current_student', 'create')
            ->notEmptyString('applicable_for_all_current_student', 'Applicable for all current students is required.');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['program_id'], 'Programs'), [
            'errorField' => 'program_id',
            'message' => 'The specified program does not exist.'
        ]);

        $rules->add($rules->existsIn(['academic_status_id'], 'AcademicStatuses'), [
            'errorField' => 'academic_status_id',
            'message' => 'The specified academic status does not exist.'
        ]);

        return $rules;
    }

    /**
     * Checks for duplicate academic stand entries
     *
     * @param array $data Academic stand data
     * @return bool True if no duplicates, false otherwise
     */
    public function checkDuplicateEntry(array $data): bool
    {
        if (empty($data['AcademicStand'])) {
            return true;
        }

        $conditions = [
            'AcademicStands.program_id' => $data['AcademicStand']['program_id'],
            'AcademicStands.semester' => serialize($data['AcademicStand']['semester']),
            'AcademicStands.academic_year_from' => $data['AcademicStand']['academic_year_from'],
            'AcademicStands.year_level_id' => serialize($data['AcademicStand']['year_level_id']),
            'AcademicStands.academic_status_id' => $data['AcademicStand']['academic_status_id']
        ];

        $count = $this->find()
            ->where($conditions)
            ->count();

        if ($count > 0) {
            $this->validationErrors['duplicate'] = ['You have already setup an academic stand for the selected year level, semester, and academic year.'];
            return false;
        }

        return true;
    }

    /**
     * Checks if an academic rule can be edited or deleted
     *
     * @param array $data Academic stand data
     * @return bool True if edit/delete is possible, false otherwise
     */
    public function canEditDeleteAcademicRule(array $data = null): bool
    {
        if (empty($data['AcademicStand'])) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        if ($data['AcademicStand']['applicable_for_all_current_student'] == 1) {
            $count = $graduateListsTable->find()
                ->matching('Students', function ($q) use ($data) {
                    return $q->where([
                        'Students.program_id' => $data['AcademicStand']['program_id'],
                        'YEAR(GraduateLists.graduate_date) >=' => $data['AcademicStand']['academic_year_from']
                    ]);
                })
                ->count();

            if ($count > 0) {
                $this->validationErrors['used_academic_rule'] = ['You cannot delete or edit this academic stand rule, as students have already graduated under this rule. Define a new rule if needed.'];
                return false;
            }

            return true;
        }

        $studentCount = $studentsTable->find()
            ->matching('StudentExamStatuses', function ($q) use ($data) {
                return $q->where(['Students.program_id' => $data['AcademicStand']['program_id']]);
            })
            ->where(['YEAR(Students.admissionyear) >=' => $data['AcademicStand']['academic_year_from']])
            ->group(['Students.id'])
            ->having(['COUNT(StudentExamStatuses.student_id) >= 2'])
            ->count();

        if ($studentCount > 0) {
            $this->validationErrors['used_academic_rule'] = ['You cannot delete or edit this academic stand rule, as students’ academic statuses have already been computed. Define a new rule if needed.'];
            return false;
        }

        return true;
    }
}
