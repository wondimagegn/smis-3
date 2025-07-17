<?php
namespace App\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ReadmissionsTable extends Table
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

        $this->setTable('readmissions');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('student_id', __('Student ID must be a valid number.'))
            ->requirePresence('student_id', 'create', __('Student ID is required.'))
            ->notEmptyString('student_id', __('Please provide a valid student ID.'))
            ->scalar('minute_number', __('Minute number must be a string.'))
            ->requirePresence('minute_number', 'create', __('Minute number is required.'))
            ->notEmptyString('minute_number', __('Please provide a minute number.'))
            ->boolean('academic_commission_approval', __('Academic commission approval must be a boolean value.'))
            ->requirePresence('academic_commission_approval', 'create', __('Academic commission approval is required.'))
            ->notEmptyString('academic_commission_approval', __('Please select accepted or rejected for academic commission approval.'))
            ->boolean('registrar_approval', __('Registrar approval must be a boolean value.'))
            ->requirePresence('registrar_approval', 'create', __('Registrar approval is required.'))
            ->notEmptyString('registrar_approval', __('Please select accepted or rejected for registrar approval.'))
            ->scalar('academic_year', __('Academic year must be a string.'))
            ->requirePresence('academic_year', 'create', __('Academic year is required.'))
            ->notEmptyString('academic_year', __('Please provide an academic year.'))
            ->scalar('semester', __('Semester must be a string.'))
            ->requirePresence('semester', 'create', __('Semester is required.'))
            ->notEmptyString('semester', __('Please provide a semester.'));

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['student_id'], 'Students', [
            'allowNullable' => false,
            'message' => __('Student ID must reference an existing student.')
        ]));

        $rules->add($rules->isUnique(
            ['student_id', 'academic_year', 'semester'],
            __('A readmission application already exists for this student in the specified academic year and semester.')
        ));

        return $rules;
    }

    /**
     * Checks if a student was readmitted in a specific academic year.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @return bool
     */
    public function isReadmittedForYear(?int $studentId, ?string $academicYear): bool
    {
        if (!$studentId || !$academicYear) {
            return false;
        }

        return $this->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year LIKE' => $academicYear . '%',
                    'registrar_approval' => true,
                    'academic_commission_approval' => true
                ])
                ->count() > 0;
    }

    /**
     * Checks if a student was ever readmitted.
     *
     * @param int|null $studentId The student ID.
     * @return bool
     */
    public function hasEverBeenReadmitted(?int $studentId): bool
    {
        if (!$studentId) {
            return false;
        }

        return $this->find()
                ->where([
                    'student_id' => $studentId,
                    'registrar_approval' => true,
                    'academic_commission_approval' => true
                ])
                ->count() > 0;
    }

    /**
     * Checks if a student was readmitted for a specific academic year and semester.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param string|null $semester The semester (e.g., '1').
     * @return bool
     */
    public function isReadmitted(?int $studentId, ?string $academicYear, ?string $semester): bool
    {
        if (!$studentId || !$academicYear || !$semester) {
            return false;
        }

        return $this->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $academicYear,
                    'semester' => $semester,
                    'registrar_approval' => true,
                    'academic_commission_approval' => true
                ])
                ->count() > 0;
    }

    /**
     * Checks if a student is eligible for readmission.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $currentAcademicYear The current academic year (e.g., '2024/25').
     * @return bool
     */
    public function eligibleForReadmission(?int $studentId, ?string $currentAcademicYear): bool
    {
        // Placeholder: Original method incomplete, commented code references AcademicRule
        // Implement custom logic if provided, otherwise return false
        return false;
    }

    /**
     * Organizes readmission applicants by department or freshman status, program, and program type.
     *
     * @param array|null $data Array of readmission applicant data.
     * @return array
     */
    public function organizeReadmissionApplicants(?array $data): array
    {
        $organized = [];

        if (empty($data)) {
            return $organized;
        }

        foreach ($data as $value) {
            $departmentName = $value['Student']['Department']['name'] ?? '';
            $programName = $value['Program']['name'] ?? '';
            $programTypeName = $value['ProgramType']['name'] ?? '';

            $groupKey = empty($departmentName) ? 'Pre/Freshman' : $departmentName;

            $organized[$groupKey][$programName][$programTypeName][] = $value;
        }

        return $organized;
    }

    /**
     * Retrieves students eligible for readmission, excluding those already readmitted, graduated, or on senate lists.
     *
     * @param bool $nonFreshman Whether to filter for non-freshman students (default: true).
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param int|null $departmentId The department or college ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param string|null $semester The semester (e.g., '1').
     * @param string|null $name Partial student first name for filtering.
     * @param string|null $admissionYears Admission years for filtering.
     * @return array
     */
    public function getListOfStudentsForReadmission(
        bool $nonFreshman = true,
        ?int $programId = null,
        ?int $programTypeId = null,
        ?int $departmentId = null,
        ?string $academicYear = null,
        ?string $semester = null,
        ?string $name = null,
        ?string $admissionYears = null
    ): array {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $clearancesTable = TableRegistry::getTableLocator()->get('Clearances');

        $conditions = ['Students.graduated' => false];

        if ($admissionYears) {
            $conditions['Students.academicyear'] = $admissionYears;
        }

        if ($programId) {
            $conditions['Students.program_id'] = $programId;
        }

        if ($programTypeId) {
            $conditions['Students.program_type_id'] = $programTypeId;
        }

        if ($nonFreshman) {
            if ($departmentId) {
                $conditions['Students.department_id'] = $departmentId;
            }
        } else {
            if ($departmentId) {
                $conditions['Students.college_id'] = $departmentId;
                $conditions['Students.department_id IS'] = null;
            }
        }

        if ($name) {
            $conditions['Students.first_name LIKE'] = '%' . $name . '%';
        }

        if ($academicYear && $semester) {
            $requestDateFilter = (new Time())->modify('-' . DAYS_BACK_READMISSION . ' days')->format('Y-m-d H:i:s');
            $conditions[] = function ($exp) use ($studentId, $academicYear, $semester, $requestDateFilter) {
                return $exp->notExists(
                    $this->find()
                        ->select([1])
                        ->where([
                            'Readmissions.student_id = Students.id',
                            'Readmissions.academic_year' => $academicYear,
                            'Readmissions.semester' => $semester,
                            'Readmissions.created >=' => $requestDateFilter
                        ])
                );
            };
        }

        $notDismissalStatusIds = [1, 2, 3];

        $students = $studentsTable->find()
            ->select([
                'Students.id',
                'Students.first_name',
                'Students.department_id',
                'Students.college_id',
                'Students.program_id',
                'Students.program_type_id',
                'Students.curriculum_id',
                'Students.academicyear',
                'Students.graduated'
            ])
            ->where($conditions)
            ->contain([
                'Curriculums' => [
                    'fields' => [
                        'id',
                        'minimum_credit_points',
                        'certificate_name',
                        'amharic_degree_nomenclature',
                        'specialization_amharic_degree_nomenclature',
                        'english_degree_nomenclature',
                        'specialization_english_degree_nomenclature',
                        'name'
                    ],
                    'Departments' => ['fields' => ['id', 'name']],
                    'CourseCategories' => ['fields' => ['id', 'curriculum_id']]
                ],
                'Departments' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->toArray();

        $filteredStudents = [];

        foreach ($students as $student) {
            $lastStatus = $studentExamStatusesTable->find()
                ->select(['academic_status_id', 'cgpa', 'mcgpa'])
                ->where(['student_id' => $student->id])
                ->order(['created' => 'DESC'])
                ->first();

            if ($lastStatus && !in_array($lastStatus->academic_status_id, $notDismissalStatusIds) && !is_null($lastStatus->academic_status_id)) {
                $curriculumId = $student->curriculum_id;

                if (!isset($filteredStudents[$curriculumId])) {
                    $filteredStudents[$curriculumId][0] = [
                        'Curriculum' => $student->curriculum->toArray(),
                        'Program' => $student->program->toArray(),
                        'ProgramType' => $student->program_type->toArray(),
                        'Department' => $student->department ? $student->department->toArray() : []
                    ];
                }

                $index = count($filteredStudents[$curriculumId]);
                $filteredStudents[$curriculumId][$index] = $student->toArray();

                if ($lastStatus) {
                    $filteredStudents[$curriculumId][$index]['cgpa'] = $lastStatus->cgpa;
                    $filteredStudents[$curriculumId][$index]['mcgpa'] = $lastStatus->mcgpa;
                }

                $error = '';
                $eligible = $clearancesTable->eligibleForReadmission($student->id);

                if (in_array($eligible, [0, 5])) {
                    $error = __('This student does not have clearance or has withdrawn.') . '<br/>';
                }

                if ($error) {
                    $filteredStudents[$curriculumId][$index]['criteria']['error'] = $error;
                }
            }
        }

        return $filteredStudents;
    }
}
