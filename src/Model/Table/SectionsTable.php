<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\ORM\Query;


class SectionsTable extends Table
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

        $this->setTable('sections');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Students', [
            'foreignKey' => 'section_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'students_sections',
            'through' => 'StudentsSections',
        ]);


        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
            'className' => 'App\Model\Table\CollegesTable',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
            'className' => 'App\Model\Table\DepartmentsTable',
        ]);

        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'INNER',
            'className' => 'App\Model\Table\YearLevelsTable',
        ]);

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
            'className' => 'App\Model\Table\ProgramsTable',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
            'className' => 'App\Model\Table\ProgramTypesTable',
        ]);

        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'LEFT',
            'className' => 'App\Model\Table\CurriculumsTable',
        ]);

        $this->hasMany('CourseInstructorAssignments', [
            'foreignKey' => 'section_id',
            'className' => 'App\Model\Table\CourseInstructorAssignmentsTable',
        ]);

        $this->hasMany('CourseRegistrations', [
            'foreignKey' => 'section_id',
            'className' => 'App\Model\Table\CourseRegistrationsTable',
        ]);

        $this->hasMany('SectionSplitForPublishedCourses', [
            'foreignKey' => 'section_id',
            'className' => 'App\Model\Table\SectionSplitForPublishedCoursesTable',
        ]);

        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'section_id',
            'className' => 'App\Model\Table\PublishedCoursesTable',
        ]);

        $this->hasMany('MergedSectionsExams', [
            'foreignKey' => 'section_id',
            'className' => 'App\Model\Table\MergedSectionsExamsTable',
        ]);

        $this->hasMany('CourseSchedules', [
            'foreignKey' => 'section_id',
            'className' => 'App\Model\Table\CourseSchedulesTable',
        ]);

        $this->belongsToMany('MergedSectionsCourses', [
            'joinTable' => 'merged_sections_courses_sections',
            'foreignKey' => 'section_id',
            'targetForeignKey' => 'merged_sections_course_id',
            'className' => 'App\Model\Table\MergedSectionsCoursesTable',
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
            ->scalar('name')
            ->maxLength('name', 255, __('Section name cannot exceed 255 characters.'))
            ->requirePresence('name', 'create', __('Section name is required.'))
            ->notEmptyString('name', __('Section name is required.'))
            ->numeric('college_id')
            ->requirePresence('college_id', 'create', __('College ID is required.'))
            ->notEmptyString('college_id', __('Please provide a valid college ID.'))
            ->numeric('department_id')
            ->allowEmptyString('department_id')
            ->numeric('year_level_id')
            ->requirePresence('year_level_id', 'create', __('Year level ID is required.'))
            ->notEmptyString('year_level_id', __('Please provide a valid year level ID.'))
            ->numeric('program_id')
            ->requirePresence('program_id', 'create', __('Program ID is required.'))
            ->notEmptyString('program_id', __('Please provide a valid program ID.'))
            ->numeric('program_type_id')
            ->requirePresence('program_type_id', 'create', __('Program type ID is required.'))
            ->notEmptyString('program_type_id', __('Please provide a valid program type ID.'))
            ->numeric('curriculum_id')
            ->allowEmptyString('curriculum_id')
            ->scalar('academic_year')
            ->requirePresence('academic_year', 'create', __('Academic year is required.'))
            ->notEmptyString('academic_year', __('Please provide an academic year.'));

        return $validator;
    }

    /**
     * Returns a rules checker object for validating application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name'], __('This section name is already taken.')));
        $rules->add($rules->existsIn(['college_id'], 'Colleges', [
            'allowNullable' => false,
            'message' => __('College ID must reference an existing college.')
        ]));
        $rules->add($rules->existsIn(['department_id'], 'Departments', [
            'allowNullable' => true,
            'message' => __('Department ID must reference an existing department.')
        ]));
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels', [
            'allowNullable' => false,
            'message' => __('Year level ID must reference an existing year level.')
        ]));
        $rules->add($rules->existsIn(['program_id'], 'Programs', [
            'allowNullable' => false,
            'message' => __('Program ID must reference an existing program.')
        ]));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes', [
            'allowNullable' => false,
            'message' => __('Program type ID must reference an existing program type.')
        ]));
        $rules->add($rules->existsIn(['curriculum_id'], 'Curriculums', [
            'allowNullable' => true,
            'message' => __('Curriculum ID must reference an existing curriculum.')
        ]));

        return $rules;
    }



    /**
     * Retrieves a student's section history
     *
     * @param int|null $studentId Student ID
     * @return array List of sections with associated data
     */
    /**
     * Retrieves a student's section history
     *
     * @param int|null $studentId Student ID
     * @return array List of sections with associated data
     */
    /*
    public function getStudentSectionHistory(?int $studentId): array
    {
        if (!is_numeric($studentId) || $studentId <= 0) {
            return [];
        }
        if (!is_numeric($studentId) || $studentId <= 0) {
            return [];
        }

        $query = $this->find()
            ->select([
                'StudentsSections.student_id',
                'StudentsSections.section_id',
                'StudentsSections.created',
                'StudentsSections.archive',
            ])
            ->where(['StudentsSections.student_id' => $studentId])
            ->contain([
                'Sections' => [
                    'YearLevels' => [
                        'fields' => ['YearLevels.id', 'YearLevels.name']
                    ],
                    'Programs' => [
                        'fields' => ['Programs.id', 'Programs.name']
                    ],
                    'ProgramTypes' => [
                        'fields' => ['ProgramTypes.id', 'ProgramTypes.name']
                    ]
                ]
            ])
            ->order([
                'Sections.academicyear' => 'ASC',
                'Sections.id' => 'ASC',
                'Sections.year_level_id' => 'ASC',
                'Sections.name' => 'ASC',
            ]);

        $results = $query->all()->toArray();

        // Transform results to match desired output format
        $sections = [];
        foreach ($results as $result) {
            $sections[] = [
                'id' => $result['section']['id'],
                'academicyear' => $result['section']['academicyear'],
                'name' => $result['section']['name'],
                'year_level_id' => $result['section']['year_level_id'],
                'archive' => $result['archive'] == 1,
                'created' => $result['created'],
                'year_level' => $result['section']['year_level'] ?? [],
                'program' => $result['section']['program'] ?? [],
                'program_type' => $result['section']['program_type'] ?? []
            ];
        }
        echo '<pre>';
        print_r($sections);
        echo '</pre>';

        return $sections;
    }
    */

    public function getStudentSectionHistory($studentId)
    {
        if (!is_numeric($studentId) || $studentId <= 0) {
            return [];
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        // Build subquery for sections associated with the student
        $subquery = $studentsSectionsTable->find()
            ->select(['section_id'])
            ->where(['student_id' => $studentId]);



        $query = $this->find()
            ->select([
                'Sections.id',
                'Sections.academicyear',
                'Sections.name',
                'Sections.year_level_id',
            ])
            ->where(['Sections.id IN' => $subquery])
            ->contain([
                'YearLevels' => [
                    'fields' => ['YearLevels.id', 'YearLevels.name'],
                    'joinType' => 'LEFT', // Explicitly use LEFT join to include sections with NULL year_level_id
                ],
                'Departments' => [
                    'fields' => ['Departments.id', 'Departments.name', 'Departments.type', 'Departments.college_id'],
                    'joinType' => 'LEFT',
                ],
                'Colleges' => [
                    'fields' => ['Colleges.id', 'Colleges.name', 'Colleges.type', 'Colleges.campus_id', 'Colleges.stream']
                ],
                'Programs' => [
                    'fields' => ['Programs.id', 'Programs.name']
                ],
                'ProgramTypes' => [
                    'fields' => ['ProgramTypes.id', 'ProgramTypes.name']
                ],
                'Curriculums' => [
                    'fields' => ['Curriculums.id', 'Curriculums.name', 'Curriculums.year_introduced', 'Curriculums.type_credit',
                        'Curriculums.active'],
                    'joinType' => 'LEFT',
                ],
                'StudentsSections' => function (Query $q) use ($studentId) {
                    return $q
                        ->select([
                            'StudentsSections.student_id',
                            'StudentsSections.section_id',
                            'StudentsSections.created',
                            'StudentsSections.archive'
                        ])
                        ->where(['StudentsSections.student_id' => $studentId])
                        ->order([
                            'StudentsSections.section_id' => 'ASC',
                            'StudentsSections.created' => 'ASC'
                        ]);
                }
            ])
            ->order([
                'Sections.academicyear' => 'ASC',
                'Sections.id' => 'ASC',

                'Sections.year_level_id IS NULL' => 'ASC', // Handle NULL year_level_id explicitly
                'Sections.year_level_id' => 'ASC',
                'Sections.name' => 'ASC'
            ]);
        $sections = $query->all()->toArray();

        // Set archive property for each section
        foreach ($sections as &$section) {
            $section['archive'] = !empty($section['students_sections']) && $section['students_sections'][0]['archive'] == 1;
            // Remove students_sections to match original output structure
            unset($section['students_sections']);
        }
        unset($section); // Clean up reference

        return $sections;
    }

    /**
     * Counts students without active section assignments.
     *
     * @param int|null $collegeId The college ID.
     * @param int|null $roleId The role ID.
     * @param int|null $departmentId The department ID.
     * @param string|null $year The academic year.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param int|null $curriculumId The curriculum ID.
     * @return int
     */
    public function countSectionlessStudents(
        ?int $collegeId = null,
        ?int $roleId = null,
        ?int $departmentId = null,
        ?string $year = null,
        ?int $programId = null,
        ?int $programTypeId = null,
        ?int $curriculumId = null
    ) {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $students = $studentsTable->getStudentsForCountSectionlessStudent(
            $collegeId,
            $roleId,
            $departmentId,
            $year,
            $programId,
            $programTypeId,
            $curriculumId
        );

        $sectionlessCount = 0;

        if (!empty($students)) {
            foreach ($students as $student) {
                $hasActiveSection = $this->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $student['id'],
                        'StudentsSections.archive' => 0
                    ])
                    ->contain([
                        'Sections' => ['fields' => ['id', 'department_id', 'archive']]
                    ])
                    ->count();

                if (!$hasActiveSection) {
                    $sectionlessCount++;
                    continue;
                }

                $isPreStudent = true;
                $sections = $this->StudentsSections->find()
                    ->where(['StudentsSections.student_id' => $student['id']])
                    ->contain(['Sections'])
                    ->toArray();

                foreach ($sections as $section) {
                    if ($section['section']['department_id'] || !$section['section']['archive']) {
                        $isPreStudent = false;
                        break;
                    }
                }

                if ($isPreStudent) {
                    $sectionlessCount++;
                }
            }
        }

        return $sectionlessCount;
    }

    /**
     * Retrieves a summary of sectionless students by program and program type.
     *
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param int|null $collegeId The college ID.
     * @param int|null $departmentId The department ID.
     * @param int|null $roleId The role ID.
     * @return array
     */
    public function getSectionlessStudentSummary(
        ?string $academicYear = null,
        ?int $collegeId = null,
        ?int $departmentId = null,
        ?int $roleId = null
    ) {
        if (!$academicYear || (!$collegeId && !$departmentId)) {
            return [];
        }

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();

        $data = [];

        foreach ($programs as $program) {
            foreach ($programTypes as $programType) {
                $conditions = [
                    'Students.academicyear' => $academicYear,
                    'Students.graduated' => false,
                    'Students.program_id' => $program->id,
                    'Students.program_type_id' => $programType->id,
                    'OR' => [
                        'Students.curriculum_id IS NOT NULL',
                        'Students.curriculum_id !=' => 0
                    ]
                ];

                if ($roleId == ROLE_DEPARTMENT) {
                    $conditions['Students.department_id'] = $departmentId;
                } else {
                    $conditions['Students.college_id'] = $collegeId;
                    $conditions['Students.department_id IS'] = null;
                }

                $studentIds = $studentsTable->find()
                    ->select(['id'])
                    ->where($conditions)
                    ->toArray();

                $studentIds = array_column($studentIds, 'id');

                if (empty($studentIds)) {
                    $data[$program->name][$programType->name] = 0;
                    continue;
                }

                $sectionCount = $studentsSectionsTable->find()
                    ->where(['StudentsSections.student_id IN' => $studentIds])
                    ->count();

                if ($sectionCount == 0) {
                    $sectionlessCount = count($studentIds);
                } else {
                    $sectionlessCount = $studentsSectionsTable->find()
                        ->where([
                            'StudentsSections.student_id IN' => $studentIds,
                            'StudentsSections.archive' => true,
                            'StudentsSections.section_id IN' => $this->find()
                                ->select(['id'])
                                ->where(['archive' => true])
                        ])
                        ->count();
                }

                $data[$program->name][$programType->name] = $sectionlessCount;
            }
        }

        return $data;
    }

    /**
     * Counts students without attached curricula.
     *
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param int|null $collegeId The college ID.
     * @param int|null $departmentId The department ID.
     * @param int|null $roleId The role ID.
     * @return int
     */
    public function getCurriculumUnattachedStudentSummary(
        ?string $academicYear = null,
        ?int $collegeId = null,
        ?int $departmentId = null,
        ?int $roleId = null
    ) {
        if (!$academicYear || (!$collegeId && !$departmentId) || $roleId != ROLE_DEPARTMENT) {
            return 0;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $students = $studentsTable->find()
            ->select(['id'])
            ->where([
                'Students.department_id' => $departmentId,
                'Students.academicyear' => $academicYear,
                'Students.graduated' => false,
                'OR' => [
                    'Students.curriculum_id IS NULL',
                    'Students.curriculum_id' => 0
                ]
            ])
            ->toArray();

        $studentIds = array_column($students, 'id');

        if (empty($studentIds)) {
            return 0;
        }

        $sectionCount = $studentsSectionsTable->find()
            ->where(['StudentsSections.student_id IN' => $studentIds])
            ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
            ->count();

        if ($sectionCount == 0) {
            return count($studentIds);
        }

        return $studentsSectionsTable->find()
            ->where([
                'StudentsSections.student_id IN' => $studentIds,
                'StudentsSections.archive' => true,
                'StudentsSections.section_id IN' => $this->find()
                    ->select(['id'])
                    ->where(['archive' => true])
            ])
            ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
            ->count();
    }

    /**
     * Retrieves curriculum IDs for sectionless students.
     *
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param int|null $collegeId The college ID.
     * @param int|null $departmentId The department ID.
     * @param int|null $roleId The role ID.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @return array
     */
    public function getSectionlessStudentCurriculum(
        ?string $academicYear = null,
        ?int $collegeId = null,
        ?int $departmentId = null,
        ?int $roleId = null,
        ?int $programId = null,
        ?int $programTypeId = null
    ): array {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $students = $studentsTable->getStudentsCurriculumForSection(
            $academicYear,
            $collegeId,
            $departmentId,
            $roleId,
            $programId,
            $programTypeId
        );

        $sectionlessCurriculumIds = [];

        foreach ($students as $student) {
            $sectionCount = $studentsSectionsTable->find()
                ->where([
                    'StudentsSections.student_id' => $student['Student']['id'],
                    'StudentsSections.archive' => false
                ])
                ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
                ->count();

            if (!$sectionCount && !empty($student['Student']['curriculum_id'])) {
                $sectionlessCurriculumIds[] = $student['Student']['curriculum_id'];
            }
        }

        return array_unique($sectionlessCurriculumIds);
    }

    /**
     * Retrieves sections for student assignment.
     *
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param int|null $collegeId The college ID.
     * @param int|null $departmentId The department ID.
     * @param int|null $roleId The role ID.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param int|null $yearLevelId The year level ID.
     * @param int|null $curriculumId The curriculum ID.
     * @return array
     */
    public function getSectionForAssignment(
        ?string $academicYear = null,
        ?int $collegeId = null,
        ?int $departmentId = null,
        ?int $roleId = null,
        ?int $programId = null,
        ?int $programTypeId = null,
        ?int $yearLevelId = null,
        ?int $curriculumId = null
    ) {
        if (!$academicYear || !$programId || !$programTypeId) {
            return [];
        }

        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $equivalentIds = $programTypesTable->find()
            ->select(['equivalent_to_id'])
            ->where(['id' => $programTypeId])
            ->first();

        $programTypeIds = [$programTypeId];
        if ($equivalentIds && $equivalentIds->equivalent_to_id) {
            $programTypeIds = array_merge($programTypeIds, json_decode($equivalentIds->equivalent_to_id, true) ?: []);
        }

        $conditions = [
            'Sections.academicyear LIKE' => $academicYear . '%',
            'Sections.program_id' => $programId,
            'Sections.program_type_id IN' => $programTypeIds,
            'Sections.archive' => false
        ];

        if ($roleId != ROLE_COLLEGE) {
            $conditions['Sections.department_id'] = $departmentId;
            $conditions['Sections.year_level_id'] = $yearLevelId;
        } else {
            $conditions['Sections.college_id'] = $collegeId;
            $conditions['OR'] = [
                'Sections.department_id IS NULL',
                'Sections.department_id' => 0
            ];
        }

        $sections = $this->find()
            ->select(['id', 'name'])
            ->where($conditions)
            ->contain([
                'Students' => [
                    'fields' => ['id', 'studentnumber', 'full_name', 'academic_year', 'gender', 'graduated'],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC']
                ]
            ])
            ->toArray();

        $filteredSections = [];

        foreach ($sections as $section) {
            if (empty($section->students)) {
                $filteredSections[] = $section;
                continue;
            }

            $firstStudentCurriculumId = $studentsTable->find()
                ->select(['curriculum_id'])
                ->where(['id' => $section->students[0]->id])
                ->first()->curriculum_id;

            if ($curriculumId == $firstStudentCurriculumId) {
                $filteredSections[] = $section;
            }
        }

        return $filteredSections;
    }

    /**
     * Counts active students in each section.
     *
     * @param array|null $sections Array of section data.
     * @return array
     */
    public function currentSectionsOccupation(?array $sections)
    {
        if (empty($sections)) {
            return [];
        }

        $data = [];
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        foreach ($sections as $key => $section) {
            $count = $studentsSectionsTable->find()
                ->where([
                    'StudentsSections.section_id' => $section['id'],
                    'StudentsSections.archive' => false
                ])
                ->count();
            $data[$key] = $count;
        }

        return $data;
    }

    /**
     * Retrieves curriculum names for sections.
     *
     * @param array|null $studentSections Array of section data with students.
     * @return array
     */
    public function sectionsCurriculum(?array $studentSections)
    {
        if (empty($studentSections)) {
            return [];
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');

        $curriculumNames = [];

        foreach ($studentSections as $sectionId => $section) {
            if (!empty($section['Curriculum']['name'])) {
                $curriculumNames[$sectionId] = $section['Curriculum']['name'] . (stripos($section['Curriculum']['type_credit'], 'ECTS') !== false ? ' (ECTS)' : ' (Credit)');
                continue;
            }

            if (empty($section['Student'])) {
                $curriculumNames[$sectionId] = __('The section is empty');
                continue;
            }

            $nonArchivedStudent = null;
            foreach ($section['Student'] as $student) {
                if ($student['StudentsSection']['archive'] == 0) {
                    $nonArchivedStudent = $student;
                    break;
                }
            }

            if (!$nonArchivedStudent) {
                $curriculumNames[$sectionId] = __('The section is empty');
                continue;
            }

            $curriculumDetail = $studentsTable->find()
                ->select(['curriculum_id'])
                ->where(['id' => $nonArchivedStudent['id']])
                ->contain(['Curriculums' => ['fields' => ['id', 'curriculum_detail']]])
                ->first();

            $curriculumNames[$sectionId] = $curriculumDetail->curriculum->curriculum_detail ?? __('The section is empty');
        }

        return $curriculumNames;
    }

    /**
     * Retrieves the curriculum ID for a section.
     *
     * @param int|null $sectionId The section ID.
     * @return mixed
     */
    public function sectionsCurriculumID(?int $sectionId)
    {
        if (!$sectionId) {
            return [];
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        $section = $this->find()
            ->select(['id', 'department_id', 'curriculum_id'])
            ->where(['Sections.id' => $sectionId, 'Sections.archive' => false])
            ->contain([
                'Students' => [
                    'fields' => ['id', 'curriculum_id', 'graduated'],
                    'conditions' => ['Students.graduated' => false],
                    'Curriculums' => ['fields' => ['id', 'name']]
                ],
                'Curriculums' => ['fields' => ['id', 'name']]
            ])
            ->first();

        if (!$section) {
            return [];
        }

        if (empty($section->department_id)) {
            return -1; // Freshman section
        }

        if ($section->curriculum_id && is_numeric($section->curriculum_id)) {
            return $section->curriculum_id;
        }

        if (empty($section->students)) {
            $publishedCourse = $publishedCoursesTable->find()
                ->select(['id'])
                ->where(['section_id' => $sectionId])
                ->contain([
                    'Courses' => [
                        'fields' => ['id', 'curriculum_id'],
                        'Curriculums' => ['fields' => ['id', 'name']]
                    ]
                ])
                ->order(['PublishedCourses.id' => 'DESC'])
                ->first();

            return $publishedCourse && $publishedCourse->course->curriculum_id ? $publishedCourse->course->curriculum_id : 0;
        }

        $curriculumIds = [];
        $totalStudents = count($section->students);
        $graduatedCount = 0;
        $nonGraduatedCount = 0;

        foreach ($section->students as $key => $student) {
            if ($student->students_section->archive == 0 && $student->curriculum_id) {
                $curriculumIds[$key] = $student->curriculum_id;
                $nonGraduatedCount++;
            }

            if ($student->graduated) {
                unset($curriculumIds[$key]);
                $graduatedCount++;
            }
        }

        if ($graduatedCount == $totalStudents) {
            return -1;
        }

        $uniqueCurriculumIds = array_unique($curriculumIds);

        if (count($uniqueCurriculumIds) == 1) {
            return reset($uniqueCurriculumIds);
        }

        if (!$totalStudents && !$graduatedCount) {
            return [-2];
        }

        return empty($uniqueCurriculumIds) ? [-1] : array_values($uniqueCurriculumIds);
    }

    /**
     * Retrieves the curriculum ID for a section.
     *
     * @param int|null $sectionId The section ID.
     * @return string|int
     */
    public function getSectionCurriculum(?int $sectionId)
    {
        if (!$sectionId) {
            return 'nostudentinsection';
        }

        $curriculumId = $this->sectionsCurriculumID($sectionId);

        return !empty($curriculumId) && !is_array($curriculumId) ? $curriculumId : 'nostudentinsection';
    }

    /**
     * Retrieves section data with associated students.
     *
     * @param int|null $sectionId The section ID.
     * @return array
     */
    public function getSectionData(?int $sectionId): array
    {
        if (!$sectionId) {
            return [];
        }

        return $this->find()
            ->select(['id'])
            ->where(['id' => $sectionId])
            ->contain([
                'Students' => ['fields' => ['id']]
            ])
            ->toArray();
    }

    /**
     * Retrieves the curriculum ID for a section with detailed logic.
     *
     * @param int|null $sectionId The section ID.
     * @return int|null
     */
    public function getSectionCurriculumId(?int $sectionId): ?int
    {
        if (!$sectionId) {
            return null;
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $curriculumAttachmentsTable = TableRegistry::getTableLocator()->get('CurriculumAttachments');

        $section = $this->find()
            ->select(['id'])
            ->where(['id' => $sectionId])
            ->contain([
                'Students' => [
                    'fields' => ['id', 'curriculum_id', 'accepted_student_id'],
                    'conditions' => [
                        'Students.id IN' => $this->CourseRegistrations->find()
                            ->select(['student_id'])
                            ->where(['section_id' => $sectionId])
                    ],
                    'Curriculums' => ['fields' => ['id']],
                    'limit' => 1
                ],
                'Curriculums' => ['fields' => ['id']]
            ])
            ->first();

        if ($section && $section->curriculum && is_numeric($section->curriculum->id)) {
            return null;
        }

        $studentSection = $studentsSectionsTable->find()
            ->select(['student_id'])
            ->where(['section_id' => $sectionId])
            ->first();

        if ($studentSection && $studentSection->student_id) {
            $student = $studentsTable->find()
                ->select(['id', 'curriculum_id'])
                ->where(['id' => $studentSection->student_id])
                ->first();

            if ($student && $student->curriculum_id) {
                return $student->curriculum_id;
            }

            $curriculumHistories = $curriculumAttachmentsTable->find()
                ->select(['curriculum_id'])
                ->where(['student_id' => $studentSection->student_id])
                ->toArray();

            if (count($curriculumHistories) == 1) {
                return $curriculumHistories[0]->curriculum_id ?? null;
            }

            foreach ($curriculumHistories as $history) {
                $otherStudent = $studentsTable->find()
                    ->select(['id'])
                    ->where([
                        'curriculum_id' => $history->curriculum_id,
                        'id !=' => $studentSection->student_id
                    ])
                    ->first();

                if ($otherStudent && $studentsSectionsTable->find()
                        ->where([
                            'student_id' => $otherStudent->id,
                            'section_id' => $sectionId
                        ])
                        ->count()) {
                    return $history->curriculum_id;
                }
            }

            return $student->curriculum_id ?? null;
        }

        return null;
    }

    /**
     * Checks if multiple sections have the same curriculum.
     *
     * @param array|null $sectionsData Array of section data.
     * @return bool
     */
    public function isSectionsHaveTheSameCurriculum(?array $sectionsData): bool
    {
        if (empty($sectionsData) || empty($sectionsData['Section']['Sections'])) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $curriculumIds = [];

        foreach ($sectionsData['Section']['Sections'] as $sectionKey) {
            $sectionId = $sectionsData['Section'][$sectionKey]['id'];
            $section = $this->getSectionData($sectionId);

            foreach ($section as $sectionData) {
                foreach ($sectionData['Student'] as $student) {
                    $curriculumId = $studentsTable->find()
                        ->select(['curriculum_id'])
                        ->where(['id' => $student['id']])
                        ->first()->curriculum_id;

                    if ($curriculumId) {
                        $curriculumIds[] = $curriculumId;
                        break 2;
                    }
                }
            }
        }

        return count(array_unique($curriculumIds)) <= 1;
    }

    /**
     * Retrieves sections with student details for a given context.
     *
     * @param int|null $collegeId The college ID.
     * @param int|null $roleId The role ID.
     * @param int|null $departmentId The department ID.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param int|null $yearLevelId The year level ID.
     * @return array|bool
     */
    public function studentSection(
        ?int $collegeId = null,
        ?int $roleId = null,
        ?int $departmentId = null,
        ?int $programId = null,
        ?int $programTypeId = null,
        ?string $academicYear = null,
        ?int $yearLevelId = null
    ) {
        if (!$roleId || !$academicYear || !$programId || !$programTypeId) {
            return false;
        }

        $conditions = [
            'Sections.archive' => false,
            'Sections.program_id' => $programId,
            'Sections.program_type_id' => $programTypeId,
            'Sections.academicyear' => $academicYear
        ];

        if ($roleId != ROLE_COLLEGE) {
            $conditions['Sections.department_id'] = $departmentId;
            $conditions['Sections.year_level_id'] = $yearLevelId;
        } else {
            $conditions['Sections.college_id'] = $collegeId;
            $conditions['OR'] = [
                'Sections.department_id IS NULL',
                'Sections.department_id' => ''
            ];
        }

        return $this->find()
            ->where($conditions)
            ->contain([
                'Students' => [
                    'fields' => ['id', 'studentnumber', 'full_name', 'gender', 'graduated', 'academic_year'],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC']
                ],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                'Departments' => ['fields' => ['id', 'name', 'type', 'college_id']],
                'Colleges' => ['fields' => ['id', 'name', 'type', 'campus_id', 'stream']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->toArray();
    }

    /**
     * Retrieves students in a specific section by ID.
     *
     * @param int|null $sectionId The section ID.
     * @return array|bool
     */
    public function studentsSectionById(?int $sectionId)
    {
        if (!$sectionId) {
            return false;
        }

        return $this->find()
            ->select([
                'id', 'name', 'program_id', 'program_type_id', 'college_id', 'department_id', 'academic_year'
            ])
            ->where([
                'Sections.id' => $sectionId,
                'Sections.id IN' => $this->StudentsSections->find()
                    ->select(['section_id'])
                    ->where(['section_id' => $sectionId, 'student_id IS NOT NULL'])
            ])
            ->contain([
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name', 'type', 'college_id']],
                'Colleges' => [
                    'fields' => ['id', 'name', 'type', 'campus_id', 'stream'],
                    'Campuses' => ['fields' => ['id', 'name']]
                ],
                'YearLevels' => ['fields' => ['id', 'name']],
                'Students' => [
                    'fields' => ['id', 'studentnumber', 'full_name', 'curriculum_id', 'gender', 'graduated', 'academic_year'],
                    'conditions' => [
                        'Students.id IN' => $this->StudentsSections->find()
                            ->select(['student_id'])
                            ->where(['archive' => false, 'section_id' => $sectionId])
                            ->group(['student_id', 'section_id'])
                    ],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.id' => 'ASC'],
                    'CourseRegistrations' => [
                        'PublishedCourses',
                        'ExamGrades' => ['fields' => ['id', 'course_registration_id', 'course_add_id', 'grade']],
                        'CourseDrops' => ['fields' => ['id', 'course_registration_id', 'semester', 'student_id', 'academic_year']]
                    ],
                    'CourseAdds' => [
                        'fields' => ['id', 'student_id', 'published_course_id'],
                        'PublishedCourses' => [
                            'Courses' => ['fields' => ['id']]
                        ]
                    ]
                ],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
            ])
            ->toArray();
    }

    /**
     * Checks if assigned students match the total number of available students.
     *
     * @param array|null $data Assignment data.
     * @param int|null $sectionlessTotalStudents Total sectionless students.
     * @return bool
     */
    public function isSectionAssignedStudentsEqualToTotalNumberOfAvailableStudents(?array $data,
        ?int $sectionlessTotalStudents)
    {
        if (empty($data)) {
            return true;
        }

        $cleanData = array_filter($data, function ($key) {
            return !in_array($key, ['academicyearSearch', 'year_level_id', 'assignment_type', 'academicyear', 'program_id', 'program_type_id', 'Curriculum']);
        }, ARRAY_FILTER_USE_KEY);

        $assignedSum = array_sum(array_column($cleanData, 'number'));

        if ($sectionlessTotalStudents !== $assignedSum) {
            $this->invalidate('section', __('The sum of assigned students must equal the total number of unassigned students (%s).', $sectionlessTotalStudents));
            return false;
        }

        return true;
    }

    /**
     * Checks if a section is empty.
     *
     * @param int|null $sectionId The section ID.
     * @return bool
     */
    public function isSectionEmpty(?int $sectionId)
    {
        if (!$sectionId) {
            return false;
        }

        $studentCount = $this->StudentsSections->find()
            ->where(['section_id' => $sectionId])
            ->count();

        return $studentCount == 0;
    }

    /**
     * Checks if courses are published in a section.
     *
     * @param int|null $sectionId The section ID.
     * @return bool
     */
    public function isCoursePublishedInTheSection(?int $sectionId)
    {
        if (!$sectionId) {
            return false;
        }

        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        return $publishedCoursesTable->find()
                ->where(['section_id' => $sectionId])
                ->count() > 0;
    }

    /**
     * Updates section curriculum ID based on published courses.
     *
     * @param int|null $sectionId The section ID.
     * @return array|int
     */
    public function updateSectionCurriculumIDFromPublishedCoursesOfTheSection(?int $sectionId)
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        if ($sectionId) {
            $section = $this->find()
                ->select(['id', 'curriculum_id'])
                ->where(['id' => $sectionId])
                ->first();

            if ($section && !$section->curriculum_id) {
                $publishedCourse = $publishedCoursesTable->find()
                    ->select(['id'])
                    ->where(['section_id' => $sectionId])
                    ->contain([
                        'Courses' => ['fields' => ['id', 'curriculum_id']]
                    ])
                    ->order(['PublishedCourses.id' => 'ASC'])
                    ->first();

                if ($publishedCourse && is_numeric($publishedCourse->course->curriculum_id)) {
                    $section->curriculum_id = $publishedCourse->course->curriculum_id;
                    $this->save($section);
                    return 1;
                }
            }

            return 0;
        }

        $sections = $this->find()
            ->select(['id'])
            ->where(['curriculum_id IS NULL'])
            ->toArray();

        $updateStatus = [
            'sections_with_null_curriculum_count' => count($sections),
            'updated_sections_count' => 0
        ];

        foreach ($sections as $section) {
            $publishedCourse = $publishedCoursesTable->find()
                ->select(['id'])
                ->where(['section_id' => $section->id])
                ->contain([
                    'Courses' => ['fields' => ['id', 'curriculum_id']]
                ])
                ->order(['PublishedCourses.id' => 'ASC'])
                ->first();

            if ($publishedCourse && is_numeric($publishedCourse->course->curriculum_id)) {
                $section->curriculum_id = $publishedCourse->course->curriculum_id;
                if ($this->save($section)) {
                    $updateStatus['updated_sections_count']++;
                }
            }
        }

        return $updateStatus;
    }

    /**
     * Checks if moving a student between sections is allowed.
     *
     * @param int|null $originalSectionId The original section ID.
     * @param int|null $studentId The student ID.
     * @param int|null $selectedSectionMoveId The target section ID.
     * @return mixed
     */
    public function isMoveAllowed(?int $originalSectionId, ?int $studentId, ?int $selectedSectionMoveId)
    {
        if (!$originalSectionId || !$studentId || !$selectedSectionMoveId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $studentFullName = $studentsTable->find()
            ->select(['full_name'])
            ->where(['id' => $studentId])
            ->first()->full_name ?? '';

        $newSection = $this->find()
            ->select(['id', 'name'])
            ->where(['id' => $selectedSectionMoveId])
            ->contain(['YearLevels' => ['fields' => ['name']]])
            ->first();

        if (!$newSection) {
            return false;
        }

        $studentYearLevel = $studentsTable->find()
            ->select(['year' => 'StudentExamStatuses.year'])
            ->where(['Students.id' => $studentId])
            ->contain(['StudentExamStatuses'])
            ->first();

        if ($studentYearLevel && $studentYearLevel->year && $studentYearLevel->year !== $newSection->year_level->name) {
            $this->invalidate(
                'move_not_allowed',
                __('%s cannot move to section %s. The target section is %s year while the student is %s year.',
                    $studentFullName, $newSection->name, $newSection->year_level->name, $studentYearLevel->year)
            );
            return false;
        }

        $latestPublishedCourse = $publishedCoursesTable->find()
            ->select(['id', 'course_id', 'academic_year', 'semester', 'section_id'])
            ->where(['section_id' => $originalSectionId])
            ->group(['semester'])
            ->order(['MAX(PublishedCourses.created)' => 'DESC'])
            ->first();

        if (!$latestPublishedCourse || !$latestPublishedCourse->academic_year || !$latestPublishedCourse->semester) {
            return true;
        }

        $registeredCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id', 'academic_year', 'semester', 'section_id'])
            ->where([
                'section_id' => $originalSectionId,
                'semester' => $latestPublishedCourse->semester,
                'academic_year' => $latestPublishedCourse->academic_year
            ])
            ->contain([
                'CourseRegistrations' => [
                    'fields' => ['id', 'student_id', 'published_course_id'],
                    'conditions' => ['CourseRegistrations.student_id' => $studentId]
                ]
            ])
            ->toArray();

        $registrationCount = 0;
        $registeredCourseIds = [];
        $previousPublishedCourseIds = [];
        $preparedForUpdate = [];

        foreach ($registeredCourses as $course) {
            if (!empty($course->course_registrations)) {
                $registrationCount++;
                $registeredCourseIds[] = $course->course_registrations[0]->id;
                $preparedForUpdate['CourseRegistration'][] = [
                    'id' => $course->course_registrations[0]->id,
                    'published_course_id' => $course->course_registrations[0]->published_course_id
                ];
            }
            $previousPublishedCourseIds[] = $course->id;
        }

        if (empty($preparedForUpdate)) {
            return 3;
        }

        $newSectionCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where([
                'semester' => $latestPublishedCourse->semester,
                'academic_year' => $latestPublishedCourse->academic_year,
                'section_id' => $selectedSectionMoveId
            ])
            ->toArray();

        if (empty($newSectionCourses) && $registrationCount > 0) {
            $this->invalidate(
                'move_not_allowed',
                __('%s cannot move to %s section. They have already registered for courses in the current section.',
                    $studentFullName, $newSection->name)
            );
            return false;
        }

        if (!empty($newSectionCourses) && $registrationCount == 0) {
            return true;
        }

        if (!empty($newSectionCourses) && $registrationCount > 0) {
            $ownSectionCourses = $publishedCoursesTable->find()
                ->select(['id', 'course_id'])
                ->where([
                    'semester' => $latestPublishedCourse->semester,
                    'academic_year' => $latestPublishedCourse->academic_year,
                    'section_id' => $originalSectionId
                ])
                ->toArray();

            $newSectionCourseIds = [];
            $isEveryCourseBelongs = true;

            foreach ($ownSectionCourses as $ownCourse) {
                $matchFound = false;
                foreach ($newSectionCourses as $newCourse) {
                    if ($newCourse->course_id == $ownCourse->course_id) {
                        $newSectionCourseIds[$ownCourse->id] = $newCourse->id;
                        $matchFound = true;
                        break;
                    }
                }
                if (!$matchFound) {
                    $isEveryCourseBelongs = false;
                    break;
                }
            }

            if ($isEveryCourseBelongs) {
                $registrations = $courseRegistrationsTable->find()
                    ->select(['id', 'student_id', 'published_course_id'])
                    ->where([
                        'student_id' => $studentId,
                        'published_course_id IN' => $previousPublishedCourseIds
                    ])
                    ->toArray();

                $preparedForUpdate = [];
                $gradeSubmittedIds = [];

                foreach ($registrations as $index => $registration) {
                    if (in_array($registration->published_course_id, $previousPublishedCourseIds)) {
                        $preparedForUpdate['CourseRegistration'][$index] = [
                            'id' => $registration->id,
                            'published_course_id' => $newSectionCourseIds[$registration->published_course_id],
                            'section_id' => $selectedSectionMoveId
                        ];
                        $gradeSubmittedIds[] = $registration->id;
                    }
                }

                $gradeCount = $examGradesTable->find()
                    ->where(['course_registration_id IN' => $gradeSubmittedIds])
                    ->count();

                if ($gradeCount > 0) {
                    $this->invalidate(
                        'move_not_allowed',
                        __('%s cannot move to %s section. Grades have already been submitted for their courses.',
                            $studentFullName, $newSection->name)
                    );
                    return false;
                }

                return $preparedForUpdate;
            }

            $this->invalidate(
                'move_not_allowed',
                __('%s cannot move to %s section. The target section has different published courses.',
                    $studentFullName, $newSection->name)
            );
            return false;
        }

        return true;
    }

    /**
     * Checks if moving multiple students between sections is allowed.
     *
     * @param int|null $originalSectionId The original section ID.
     * @param array|null $studentIds Array of student IDs.
     * @param int|null $selectedSectionMoveId The target section ID.
     * @return mixed
     */
    public function isSectionMoveAllowed(?int $originalSectionId, ?array $studentIds, ?int $selectedSectionMoveId)
    {
        if (!$originalSectionId || empty($studentIds) || !$selectedSectionMoveId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $newSection = $this->find()
            ->select(['id', 'name'])
            ->where(['id' => $selectedSectionMoveId])
            ->contain(['YearLevels' => ['fields' => ['name']]])
            ->first();

        if (!$newSection) {
            return false;
        }

        $successMoves = [];
        $unsuccessMoves = [];

        foreach ($studentIds as $studentId) {
            $yearLevel = $studentsTable->find()
                ->select(['year' => 'CourseRegistrations.year'])
                ->where(['Students.id' => $studentId])
                ->contain(['CourseRegistrations'])
                ->first();

            $sectionCount = $studentsSectionsTable->find()
                ->where([
                    'section_id' => $selectedSectionMoveId,
                    'student_id' => $studentId
                ])
                ->group(['section_id', 'student_id'])
                ->count();

            if ($sectionCount > 0) {
                continue;
            }

            if ($yearLevel && $yearLevel->year && $yearLevel->year !== $newSection->year_level->name) {
                $unsuccessMoves[] = $studentId;
            } else {
                $successMoves[] = $studentId;
            }
        }

        $latestPublishedCourse = $publishedCoursesTable->find()
            ->select(['id', 'course_id', 'academic_year', 'semester', 'section_id'])
            ->where(['section_id' => $originalSectionId])
            ->group(['semester'])
            ->order(['MAX(PublishedCourses.created)' => 'DESC'])
            ->first();

        if (!$latestPublishedCourse || !$latestPublishedCourse->academic_year || !$latestPublishedCourse->semester) {
            $saveSection = $this->saveSectionMove($successMoves, $originalSectionId, $selectedSectionMoveId);
            return $this->handleMoveResult($successMoves, $unsuccessMoves, $newSection, $yearLevel, $saveSection);
        }

        $registeredCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id', 'academic_year', 'semester', 'section_id'])
            ->where([
                'section_id' => $originalSectionId,
                'academic_year' => $latestPublishedCourse->academic_year
            ])
            ->contain([
                'CourseRegistrations' => [
                    'fields' => ['id', 'student_id', 'published_course_id'],
                    'conditions' => ['CourseRegistrations.student_id IN' => $successMoves]
                ]
            ])
            ->toArray();

        $registrationCount = 0;
        $previousPublishedCourseIds = [];
        $preparedForUpdate = [];

        foreach ($registeredCourses as $course) {
            if (!empty($course->course_registrations)) {
                $registrationCount++;
                $previousPublishedCourseIds[] = $course->id;
                foreach ($course->course_registrations as $registration) {
                    $preparedForUpdate['CourseRegistration'][] = [
                        'id' => $registration->id,
                        'published_course_id' => $registration->published_course_id
                    ];
                }
            }
        }

        if (empty($preparedForUpdate)) {
            $saveSection = $this->saveSectionMove($successMoves, $originalSectionId, $selectedSectionMoveId);
            return $this->handleMoveResult($successMoves, $unsuccessMoves, $newSection, $yearLevel, $saveSection);
        }

        $newSectionCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where([
                'academic_year' => $latestPublishedCourse->academic_year,
                'section_id' => $selectedSectionMoveId
            ])
            ->toArray();

        if (empty($newSectionCourses) && $registrationCount > 0) {
            $this->invalidate(
                'move_not_allowed',
                __('Students cannot move to %s section. No courses are published in the target section.', $newSection->name)
            );
            return false;
        }

        if (!empty($newSectionCourses) && $registrationCount == 0) {
            $saveSection = $this->saveSectionMove($successMoves, $originalSectionId, $selectedSectionMoveId);
            return $this->handleMoveResult($successMoves, $unsuccessMoves, $newSection, $yearLevel, $saveSection);
        }

        if (!empty($newSectionCourses) && $registrationCount > 0) {
            $ownSectionCourses = $publishedCoursesTable->find()
                ->select(['id', 'course_id'])
                ->where([
                    'academic_year' => $latestPublishedCourse->academic_year,
                    'section_id' => $originalSectionId
                ])
                ->toArray();

            $newSectionCourseIds = [];
            $isEveryCourseBelongs = true;

            foreach ($ownSectionCourses as $ownCourse) {
                $matchFound = false;
                foreach ($newSectionCourses as $newCourse) {
                    if ($newCourse->course_id == $ownCourse->course_id) {
                        $newSectionCourseIds[$ownCourse->id] = $newCourse->id;
                        $matchFound = true;
                        break;
                    }
                }
                if (!$matchFound) {
                    $isEveryCourseBelongs = false;
                    break;
                }
            }

            if ($isEveryCourseBelongs) {
                $registrations = $courseRegistrationsTable->find()
                    ->select(['id', 'student_id', 'published_course_id'])
                    ->where([
                        'student_id IN' => $successMoves,
                        'published_course_id IN' => $previousPublishedCourseIds
                    ])
                    ->toArray();

                $preparedForUpdate = [];
                $gradeSubmittedIds = [];

                foreach ($registrations as $index => $registration) {
                    if (in_array($registration->published_course_id, $previousPublishedCourseIds)) {
                        $preparedForUpdate['CourseRegistration'][$index] = [
                            'id' => $registration->id,
                            'published_course_id' => $newSectionCourseIds[$registration->published_course_id],
                            'section_id' => $selectedSectionMoveId
                        ];
                        $gradeSubmittedIds[] = $registration->id;
                    }
                }

                $gradeCount = $examGradesTable->find()
                    ->where(['course_registration_id IN' => $gradeSubmittedIds])
                    ->count();

                if ($gradeCount > 0) {
                    $this->invalidate(
                        'move_not_allowed',
                        __('Students cannot move to %s section. Grades have been submitted for their courses.', $newSection->name)
                    );
                    return false;
                }

                $saveSection = $this->saveSectionMove($successMoves, $originalSectionId, $selectedSectionMoveId, $preparedForUpdate);
                return $this->handleMoveResult($successMoves, $unsuccessMoves, $newSection, $yearLevel, $saveSection);
            }

            $this->invalidate(
                'move_not_allowed',
                __('Students cannot move to %s section. The target section has different courses.', $newSection->name)
            );
            return false;
        }

        $saveSection = $this->saveSectionMove($successMoves, $originalSectionId, $selectedSectionMoveId);
        return $this->handleMoveResult($successMoves, $unsuccessMoves, $newSection, $yearLevel, $saveSection);
    }

    /**
     * Handles the result of a section move operation.
     *
     * @param array $successMoves Successfully moved student IDs.
     * @param array $unsuccessMoves Unsuccessfully moved student IDs.
     * @param object $newSection The target section.
     * @param object|null $yearLevel The student's year level.
     * @param mixed $saveSection The save operation result.
     * @return mixed
     */
    protected function handleMoveResult(array $successMoves, array $unsuccessMoves, $newSection, $yearLevel, $saveSection)
    {
        if (!empty($unsuccessMoves)) {
            $message = __('%s student(s) cannot move to %s section. The target section is %s year while the student is %s year.',
                count($unsuccessMoves),
                $newSection->name,
                $newSection->year_level->name,
                $yearLevel->year ?? 'unknown'
            );

            if (!empty($successMoves)) {
                $message .= __(' But %s student(s) moved successfully.', count($successMoves));
            }

            $this->invalidate('move_not_allowed', $message);
        }

        return $saveSection;
    }

    /**
     * Checks if moving multiple students between sections is allowed (alternative method).
     *
     * @param int|null $originalSectionId The original section ID.
     * @param array|null $studentIds Array of student IDs.
     * @param int|null $selectedSectionMoveId The target section ID.
     * @return mixed
     */
    public function isSectionMoveAllowedM(?int $originalSectionId, ?array $studentIds, ?int $selectedSectionMoveId)
    {
        if (!$originalSectionId || empty($studentIds) || !$selectedSectionMoveId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $newSection = $this->find()
            ->select(['id', 'name'])
            ->where(['id' => $selectedSectionMoveId])
            ->contain(['YearLevels' => ['fields' => ['name']]])
            ->first();

        if (!$newSection) {
            return false;
        }

        $successMoves = [];
        $unsuccessMoves = [];

        foreach ($studentIds as $key => $studentId) {
            $yearLevel = $studentsTable->find()
                ->select(['year' => 'CourseRegistrations.year'])
                ->where(['Students.id' => $studentId])
                ->contain(['CourseRegistrations'])
                ->first();

            $sectionCount = $studentsSectionsTable->find()
                ->where([
                    'section_id' => $selectedSectionMoveId,
                    'student_id' => $studentId
                ])
                ->group(['section_id', 'student_id'])
                ->count();

            if ($sectionCount > 0) {
                unset($studentIds[$key]);
                continue;
            }

            if ($yearLevel && $yearLevel->year && $yearLevel->year !== $newSection->year_level->name) {
                $unsuccessMoves[] = $studentId;
            } else {
                $successMoves[] = $studentId;
            }
        }

        $originalCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where(['section_id' => $originalSectionId])
            ->toArray();

        $targetCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where(['section_id' => $selectedSectionMoveId])
            ->toArray();

        $targetCourseMap = array_column($targetCourses, 'id', 'course_id');

        $registrations = !empty($successMoves) ? $courseRegistrationsTable->find()
            ->select(['id', 'student_id', 'published_course_id', 'section_id'])
            ->where([
                'student_id IN' => $successMoves,
                'published_course_id IN' => array_column($originalCourses, 'id')
            ])
            ->contain(['ExamGrades'])
            ->toArray() : [];

        $preparedForUpdate = [];
        foreach ($registrations as $index => $registration) {
            if ($registration->published_course_id && empty($registration->exam_grades)) {
                $courseId = array_column($originalCourses, 'course_id', 'id')[$registration->published_course_id];
                if (isset($targetCourseMap[$courseId])) {
                    $preparedForUpdate['CourseRegistration'][$index] = [
                        'id' => $registration->id,
                        'published_course_id' => $targetCourseMap[$courseId],
                        'section_id' => $selectedSectionMoveId
                    ];
                }
            }
        }

        if (!empty($successMoves) && $originalSectionId) {
            $saveSection = $this->saveSectionMove($successMoves, $originalSectionId, $selectedSectionMoveId, $preparedForUpdate);
            return $this->handleMoveResult($successMoves, $unsuccessMoves, $newSection, $yearLevel, $saveSection);
        }

        return false;
    }

    /**
     * Saves the section move operation for students.
     *
     * @param array $studentIds Array of student IDs.
     * @param int $originalSectionId The original section ID.
     * @param int $selectedSectionMoveId The target section ID.
     * @param array $moveRegistration Course registration updates.
     * @return bool
     */
    public function saveSectionMove(array $studentIds, int $originalSectionId, int $selectedSectionMoveId, array $moveRegistration = []): bool
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        if (!empty($moveRegistration)) {
            if (!$courseRegistrationsTable->saveMany($moveRegistration['CourseRegistration'], ['validate' => false])) {
                $this->invalidate('move_not_allowed', __('Synchronization problem, student course registration is not synchronized with published courses.'));
                return false;
            }
        }

        $sectionMoveData = [];
        foreach ($studentIds as $index => $studentId) {
            $existingRecordId = $this->checkTheRecordInArchive($selectedSectionMoveId, $studentId);
            if ($existingRecordId) {
                $sectionMoveData['StudentsSection'][$index] = [
                    'id' => $existingRecordId,
                    'archive' => false,
                    'student_id' => $studentId
                ];
            } else {
                $sectionMoveData['StudentsSection'][$index] = [
                    'section_id' => $selectedSectionMoveId,
                    'student_id' => $studentId
                ];
            }
        }

        if (!empty($sectionMoveData['StudentsSection'])) {
            if ($studentsSectionsTable->saveMany($sectionMoveData['StudentsSection'], ['validate' => false])) {
                $archiveSections = [];
                $deleteSections = [];

                foreach ($sectionMoveData['StudentsSection'] as $index => $move) {
                    $existingSectionId = $studentsSectionsTable->find()
                        ->select(['id'])
                        ->where([
                            'student_id' => $move['student_id'],
                            'section_id' => $originalSectionId,
                            'archive' => false
                        ])
                        ->first()->id ?? null;

                    if (!$examGradesTable->isCourseGradeSubmitted($move['student_id'], $originalSectionId)) {
                        $deleteSections[] = $existingSectionId;
                    } else {
                        $archiveSections['StudentsSection'][$index] = [
                            'id' => $existingSectionId,
                            'archive' => true
                        ];
                    }
                }

                if (!empty($archiveSections)) {
                    $studentsSectionsTable->saveMany($archiveSections['StudentsSection'], ['validate' => false]);
                }

                if (!empty($deleteSections)) {
                    $studentsSectionsTable->deleteAll(['id IN' => $deleteSections], false);
                }

                return true;
            }
        }

        return true;
    }

    /**
     * Checks for archived section records.
     *
     * @param int|null $sectionId The section ID.
     * @param int|null $studentId The student ID.
     * @return int|null
     */
    public function checkTheRecordInArchive(?int $sectionId, ?int $studentId): ?int
    {
        if (!$sectionId || !$studentId) {
            return null;
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        return $studentsSectionsTable->find()
            ->select(['id'])
            ->where([
                'student_id' => $studentId,
                'section_id' => $sectionId,
                'archive' => true
            ])
            ->first()->id ?? null;
    }

    /**
     * Organizes department sections by program and program type.
     *
     * @param int|string $departmentId The department ID.
     * @param bool $archive Include archived sections.
     * @param bool $includeSplit Include split sections.
     * @param bool $includeMerge Include merged sections.
     * @return array
     */
    public function allDepartmentSectionsOrganizedByProgramAndProgramType(
        $departmentId = '',
        bool $archive = false,
        bool $includeSplit = false,
        bool $includeMerge = false
    ): array {
        if (empty($departmentId)) {
            return [];
        }

        $sections = $this->find()
            ->select(['id', 'name', 'program_id', 'program_type_id', 'academic_year'])
            ->where([
                'department_id' => $departmentId,
                'archive' => $archive
            ])
            ->contain([
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']]
            ])
            ->toArray();

        $organized = [];

        foreach ($sections as $section) {
            $programName = $section->program->name;
            $programTypeName = $section->program_type->name;
            $yearLevelName = $section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');

            $organized[$programName][$programTypeName][$section->id] = sprintf(
                '%s (%s, %s)',
                $section->name,
                $yearLevelName,
                $section->academic_year
            );
        }

        return $organized;
    }

    /**
     * Organizes department sections by program type.
     *
     * @param int|string $collegeOrDepartmentId The college or department ID.
     * @param bool $department Whether to filter by department (true) or college (false).
     * @param int $programId The program ID.
     * @param bool $archive Include archived sections.
     * @param bool $includeSplit Include split sections.
     * @param bool $includeMerge Include merged sections.
     * @return array
     */
    public function allDepartmentSectionsOrganizedByProgramType(
        $collegeOrDepartmentId = '',
        bool $department = true,
        int $programId = 1,
        bool $archive = false,
        bool $includeSplit = false,
        bool $includeMerge = false
    ): array {
        if (empty($collegeOrDepartmentId) || !$programId) {
            return [];
        }

        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $currentAcademicYear = $academicYearsTable->currentAcademicYear();
        $yearsInPast = defined('ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM') ? ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM : 0;

        $academicYears = $yearsInPast ? $academicYearsTable->academicYearInArray(
            (explode('/', $currentAcademicYear)[0] - $yearsInPast),
            explode('/', $currentAcademicYear)[0]
        ) : [$currentAcademicYear];

        $archiveSection = is_bool($archive) ? [$archive] : [0, 1];

        $conditions = [
            'Sections.program_id' => $programId,
            'Sections.academicyear IN' => $academicYears,
            'Sections.archive IN' => $archiveSection
        ];

        if ($department) {
            $conditions['Sections.department_id'] = $collegeOrDepartmentId;
        } else {
            $conditions['Sections.college_id'] = $collegeOrDepartmentId;
            $conditions['Sections.department_id IS'] = null;
        }

        $sections = $this->find()
            ->select(['id', 'name', 'program_type_id', 'academic_year', 'year_level_id'])
            ->where($conditions)
            ->contain([
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']]
            ])
            ->order([
                'Sections.academicyear' => 'DESC',
                'Sections.year_level_id' => 'ASC',
                'Sections.id' => 'ASC',
                'Sections.name' => 'ASC'
            ])
            ->toArray();

        $organized = [];

        foreach ($sections as $section) {
            $programTypeName = $section->program_type->name;
            $yearLevelName = $section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');

            $organized[$programTypeName][$section->id] = sprintf(
                '%s (%s, %s)',
                $section->name,
                $yearLevelName,
                $section->academic_year
            );
        }

        return $organized;
    }

    /**
     * Organizes department sections by program type for supplementary exams.
     *
     * @param int|string $collegeOrDepartmentId The college or department ID.
     * @param bool $department Whether to filter by department (true) or college (false).
     * @param int $programId The program ID.
     * @param bool $archive Include archived sections.
     * @param bool $includeSplit Include split sections.
     * @param bool $includeMerge Include merged sections.
     * @return array
     */
    public function allDepartmentSectionsOrganizedByProgramTypeSuppExam(
        $collegeOrDepartmentId = '',
        bool $department = true,
        int $programId = 1,
        bool $archive = false,
        bool $includeSplit = false,
        bool $includeMerge = false
    ): array {
        if (empty($collegeOrDepartmentId) || !$programId) {
            return [];
        }

        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $currentAcademicYear = $academicYearsTable->currentAcademicYear();
        $yearsInPast = defined('ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM') ? ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM : 0;

        $academicYears = $yearsInPast ? $academicYearsTable->academicYearInArray(
            (explode('/', $currentAcademicYear)[0] - $yearsInPast),
            explode('/', $currentAcademicYear)[0]
        ) : [$currentAcademicYear];

        $archiveSection = is_bool($archive) ? [$archive] : [0, 1];

        $conditions = [
            'Sections.program_id' => $programId,
            'Sections.academicyear IN' => $academicYears,
            'Sections.archive IN' => $archiveSection
        ];

        if ($department) {
            $conditions['Sections.department_id'] = $collegeOrDepartmentId;
        } else {
            $conditions['Sections.college_id'] = $collegeOrDepartmentId;
            $conditions['Sections.department_id IS'] = null;
        }

        $sections = $this->find()
            ->select(['id', 'name', 'program_type_id', 'academic_year', 'year_level_id'])
            ->where($conditions)
            ->contain([
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'Students' => [
                    'fields' => ['id'],
                    'conditions' => ['Students.graduated' => false]
                ]
            ])
            ->order([
                'Sections.academicyear' => 'DESC',
                'Sections.year_level_id' => 'ASC',
                'Sections.id' => 'ASC',
                'Sections.name' => 'ASC'
            ])
            ->toArray();

        $organized = [];

        foreach ($sections as $section) {
            if (empty($section->students)) {
                continue;
            }

            $programTypeName = $section->program_type->name;
            $yearLevelName = $section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');

            $organized[$programTypeName][$section->id] = sprintf(
                '%s (%s, %s)',
                $section->name,
                $yearLevelName,
                $section->academic_year
            );
        }

        return $organized;
    }

    /**
     * Retrieves all students in a section.
     *
     * @param int|string $sectionId The section ID.
     * @return array
     */
    public function allStudents($sectionId = ''): array
    {
        if (empty($sectionId)) {
            return [];
        }

        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');

        $section = $this->find()
            ->select(['id'])
            ->where(['id' => $sectionId])
            ->contain([
                'Students' => [
                    'fields' => [
                        'id', 'first_name', 'middle_name', 'last_name', 'gender',
                        'studentnumber', 'academic_year', 'graduated', 'full_name'
                    ],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC']
                ]
            ])
            ->first();

        $studentList = [];

        if (!empty($section->students)) {
            foreach ($section->students as $student) {
                if (!$student->graduated && !$graduateListsTable->isGraduated($student->id)) {
                    $studentList[$student->id] = sprintf(
                        '%s %s %s (%s)',
                        $student->first_name,
                        $student->middle_name,
                        $student->last_name,
                        $student->studentnumber
                    );
                }
            }
        }

        return $studentList;
    }

    /**
     * Retrieves all active students in a section.
     *
     * @param int|string $sectionId The section ID.
     * @return array
     */
    public function getAllActiveStudents($sectionId = ''): array
    {
        if (empty($sectionId)) {
            return [];
        }

        return $this->find()
            ->select(['id'])
            ->where(['id' => $sectionId, 'archive' => false])
            ->contain([
                'Students' => [
                    'fields' => [
                        'id', 'first_name', 'middle_name', 'last_name', 'gender',
                        'studentnumber', 'academic_year', 'graduated', 'curriculum_id', 'full_name'
                    ],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC']
                ]
            ])
            ->first()->toArray();
    }

    /**
     * Retrieves active students in a section.
     *
     * @param int|null $sectionId The section ID.
     * @return array
     */
    public function getSectionActiveStudents(?int $sectionId): array
    {
        if (!$sectionId) {
            return [];
        }

        return $this->StudentsSections->find()
            ->where([
                'section_id' => $sectionId,
                'archive' => false
            ])
            ->group(['student_id', 'section_id'])
            ->toArray();
    }

    /**
     * Retrieves active, registered students in a section.
     *
     * @param int|null $sectionId The section ID.
     * @return array
     */
    public function getSectionActiveStudentsRegistered(?int $sectionId): array
    {
        if (!$sectionId) {
            return [];
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');

        $students = $studentsSectionsTable->find()
            ->where([
                'section_id' => $sectionId,
                'archive' => false
            ])
            ->group(['student_id', 'section_id'])
            ->toArray();

        $registeredStudentIds = [];

        foreach ($students as $student) {
            $registrationCount = $courseRegistrationsTable->find()
                ->where([
                    'student_id' => $student['student_id'],
                    'section_id' => $sectionId
                ])
                ->count();

            if ($registrationCount) {
                $registrationIds = $courseRegistrationsTable->find()
                    ->select(['id'])
                    ->where([
                        'student_id' => $student['student_id'],
                        'section_id' => $sectionId
                    ])
                    ->toArray();

                $registrationIds = array_column($registrationIds, 'id');

                $droppedCount = $courseDropsTable->find()
                    ->where([
                        'student_id' => $student['student_id'],
                        'course_registration_id IN' => $registrationIds,
                        'registrar_confirmation' => true
                    ])
                    ->count();

                if ($droppedCount != count($registrationIds)) {
                    $registeredStudentIds[] = $student['student_id'];
                }
            }
        }

        if (empty($registeredStudentIds)) {
            return [];
        }

        return $studentsSectionsTable->find()
            ->where([
                'student_id IN' => $registeredStudentIds,
                'section_id' => $sectionId,
                'archive' => false
            ])
            ->group(['student_id', 'section_id'])
            ->toArray();
    }

    /**
     * Retrieves IDs of active students in a section.
     *
     * @param int|null $sectionId The section ID.
     * @param string $academicYear The academic year (e.g., '2024/25').
     * @return array
     */
    public function getSectionActiveStudentsId(?int $sectionId, string $academicYear = ''): array
    {
        if (!$sectionId) {
            return [];
        }

        $conditions = [
            'section_id' => $sectionId,
            'archive' => false
        ];

        if ($academicYear) {
            $conditions['Sections.academicyear LIKE'] = $academicYear . '%';
        }

        return $this->StudentsSections->find()
            ->select(['student_id'])
            ->where($conditions)
            ->contain(['Sections'])
            ->group(['student_id', 'section_id'])
            ->extract('student_id')
            ->toArray();
    }

    /**
     * Retrieves last sections for sectionless students.
     *
     * @param array|null $sectionlessStudentIds Array of sectionless student data.
     * @return array
     */
    public function getSectionlessStudentsLastSections(?array $sectionlessStudentIds): array
    {
        if (empty($sectionlessStudentIds)) {
            return [];
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $studentIds = array_column(array_column($sectionlessStudentIds, 'StudentsSection'), 'student_id');
        $sectionDetails = [];

        foreach ($studentIds as $studentId) {
            $lastSection = $studentsSectionsTable->find()
                ->select(['section_id'])
                ->where(['student_id' => $studentId])
                ->order(['modified' => 'DESC'])
                ->first();

            if ($lastSection) {
                $sectionDetails[$studentId] = $this->find()
                    ->select(['id', 'name', 'year_level_id', 'academic_year'])
                    ->where(['id' => $lastSection->section_id])
                    ->contain([
                        'YearLevels' => ['fields' => ['name']],
                        'Students' => [
                            'fields' => ['id', 'studentnumber', 'full_name', 'academic_year', 'gender', 'graduated'],
                            'conditions' => ['Students.id' => $studentId],
                            'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC']
                        ]
                    ])
                    ->first();
            }
        }

        return $sectionDetails;
    }

    /**
     * Retrieves section details by exam grade ID.
     *
     * @param int|null $examGradeId The exam grade ID.
     * @return array|null
     */
    public function getSectionByExamGradeId(?int $examGradeId): ?array
    {
        if (!$examGradeId) {
            return null;
        }

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $examGrade = $examGradesTable->find()
            ->select(['id'])
            ->where(['id' => $examGradeId])
            ->contain([
                'CourseAdds' => [
                    'PublishedCourses' => [
                        'Sections' => ['fields' => ['id', 'name']]
                    ]
                ],
                'CourseRegistrations' => [
                    'PublishedCourses' => [
                        'Sections' => ['fields' => ['id', 'name']]
                    ]
                ]
            ])
            ->first();

        if ($examGrade) {
            return $examGrade->course_registration->published_course->section->toArray() ??
                $examGrade->course_add->published_course->section->toArray() ?? null;
        }

        return null;
    }

    /**
     * Retrieves students in a section, optionally filtered by name.
     *
     * @param int|null $sectionId The section ID.
     * @param string|null $name Partial student name for filtering.
     * @return array
     */
    public function getSectionStudents(?int $sectionId, ?string $name): array
    {
        if (!$sectionId) {
            return [];
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $conditions = [
            'StudentsSections.section_id' => $sectionId,
            'StudentsSections.archive' => false
        ];

        if ($name) {
            $name = trim($name) . '%';
            $conditions['Students.id IN'] = $studentsTable->find()
                ->select(['id'])
                ->where([
                    'OR' => [
                        'first_name LIKE' => $name,
                        'middle_name LIKE' => $name,
                        'last_name LIKE' => $name,
                        'studentnumber LIKE' => $name
                    ]
                ]);
        }

        return $studentsSectionsTable->find()
            ->where($conditions)
            ->contain([
                'Students' => [
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC']
                ]
            ])
            ->group(['StudentsSections.section_id', 'StudentsSections.student_id'])
            ->toArray();
    }

    /**
     * Retrieves students in a section for status updates, optionally filtered by admission year.
     *
     * @param int|null $sectionId The section ID.
     * @param string|null $admissionYear The admission year.
     * @return array
     */
    public function getSectionStudentsForStatus(?int $sectionId, ?string $admissionYear): array
    {
        if (!$sectionId) {
            return [];
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $conditions = [
            'StudentsSections.section_id' => $sectionId,
            'Students.graduated' => false
        ];

        if ($admissionYear) {
            $conditions['Students.academic_year LIKE'] = $admissionYear . '%';
        }

        return $studentsSectionsTable->find()
            ->where($conditions)
            ->contain([
                'Students' => [
                    'fields' => [
                        'id', 'studentnumber', 'full_name', 'gender', 'graduated',
                        'curriculum_id', 'department_id', 'college_id', 'program_id',
                        'program_type_id', 'academic_year'
                    ],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC'],
                    'StudentExamStatuses' => [
                        'fields' => ['academic_year', 'semester', 'modified'],
                        'sort' => ['StudentExamStatuses.academic_year' => 'DESC', 'StudentExamStatuses.semester' => 'DESC'],
                        'limit' => 1
                    ]
                ]
            ])
            ->group(['StudentsSections.section_id', 'StudentsSections.student_id'])
            ->toArray();
    }

    /**
     * Retrieves courses taken by students in a section.
     *
     * @param int|null $sectionId The section ID.
     * @return array|bool
     */
    public function studentsAlreadyTakenCourse(?int $sectionId)
    {
        if (!$sectionId) {
            return false;
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $studentSections = $studentsSectionsTable->find()
            ->select(['student_id'])
            ->where(['section_id' => $sectionId])
            ->toArray();

        $studentIds = array_column($studentSections, 'student_id');

        $previousSections = [$sectionId];
        $previousStudentSections = $studentsSectionsTable->find()
            ->select(['section_id'])
            ->where([
                'student_id IN' => $studentIds,
                'archive' => true
            ])
            ->toArray();

        foreach ($previousStudentSections as $section) {
            if ($section->section_id != $sectionId) {
                $previousSections[] = $section->section_id;
            }
        }

        return $this->find()
            ->select(['id', 'name'])
            ->where(['id IN' => $previousSections])
            ->contain([
                'Students' => [
                    'fields' => [
                        'id', 'studentnumber', 'full_name', 'curriculum_id', 'gender',
                        'academic_year', 'graduated'
                    ],
                    'conditions' => ['Students.id IN' => $studentIds],
                    'sort' => ['Students.academic_year' => 'DESC', 'Students.studentnumber' => 'ASC'],
                    'CourseRegistrations' => [
                        'PublishedCourses',
                        'ExamGrades' => ['fields' => ['id', 'course_registration_id', 'course_add_id', 'grade']],
                        'CourseDrops' => ['fields' => ['id', 'course_registration_id', 'semester', 'student_id', 'academic_year']]
                    ],
                    'CourseAdds' => [
                        'fields' => ['id', 'student_id', 'published_course_id'],
                        'PublishedCourses' => [
                            'Courses' => ['fields' => ['id']]
                        ]
                    ]
                ]
            ])
            ->toArray();
    }

    /**
     * Retrieves sections organized by department.
     *
     * @param int|null $departmentId The department ID.
     * @return array
     */
    public function getSectionsByDepartment(?int $departmentId): array
    {
        if (!$departmentId) {
            return [];
        }

        $sections = $this->find()
            ->select(['id', 'name', 'program_id', 'year_level_id'])
            ->where([
                'department_id' => $departmentId,
                'archive' => false
            ])
            ->contain([
                'Programs' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->toArray();

        $organized = [];

        foreach ($sections as $section) {
            $programName = $section->program->name;
            $yearLevelName = $section->year_level->name ?? 'Unknown';
            $organized[$programName][$section->id] = sprintf('%s (%s)', $section->name, $yearLevelName);
        }

        return $organized;
    }

    /**
     * Counts total active students in a section.
     *
     * @param int|null $sectionId The section ID.
     * @return int
     */
    public function getTotalActiveStudentsOfTheSection(?int $sectionId): int
    {
        if (!$sectionId) {
            return 0;
        }

        return $this->StudentsSections->find()
            ->where([
                'section_id' => $sectionId,
                'archive' => false
            ])
            ->group(['section_id', 'student_id'])
            ->count();
    }

    /**
     * Formats the year level as a string (e.g., 1 => '1st', 2 => '2nd').
     *
     * @param int $yearLevel The year level number.
     * @return string
     */
    protected function formatYearLevel(int $yearLevel): string
    {
        if ($yearLevel === 1) {
            return '1st';
        } elseif ($yearLevel === 2) {
            return '2nd';
        } elseif ($yearLevel === 3) {
            return '3rd';
        }
        return $yearLevel . 'th';
    }

    /**
     * Retrieves published courses for exam scheduling.
     *
     * @param int|null $collegeId The college ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param string|null $semester The semester (e.g., '1').
     * @param int|null $programId The program ID.
     * @param array|null $programTypeIds Array of program type IDs.
     * @param array|null $departmentIds Array of department IDs.
     * @param array|null $yearLevels Array of year levels.
     * @return array
     */
    public function getSectionsPublishedCoursesForExamSchedule(
        ?int $collegeId = null,
        ?string $academicYear = null,
        ?string $semester = null,
        ?int $programId = null,
        ?array $programTypeIds = null,
        ?array $departmentIds = null,
        ?array $yearLevels = null
    ): array {
        if (empty($departmentIds) || empty($yearLevels) || !$academicYear || !$semester || !$programId || !$programTypeIds) {
            return [];
        }

        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $examPeriodsTable = TableRegistry::getTableLocator()->get('ExamPeriods');
        $publishedCourses = [];

        foreach ($departmentIds as $depKey => $departmentId) {
            foreach ($yearLevels as $yearLevel) {
                $yearLevelName = $this->formatYearLevel($yearLevel);

                $yearLevelData = $yearLevelsTable->find()
                    ->select(['id'])
                    ->where([
                        'name' => $yearLevelName,
                        'department_id' => $departmentId
                    ])
                    ->first();

                if (!$yearLevelData && $depKey !== 'FP') {
                    continue;
                }

                $conditions = [
                    'Sections.academicyear' => $academicYear,
                    'Sections.program_id' => $programId,
                    'Sections.program_type_id IN' => $programTypeIds,
                    'Sections.year_level_id' => $yearLevelData->id ?? 0
                ];

                if ($depKey === 'FP') {
                    $conditions['Sections.college_id'] = $collegeId;
                } else {
                    $conditions['Sections.department_id'] = $departmentId;
                }

                $sections = $this->find()
                    ->select(['id', 'program_type_id'])
                    ->where($conditions)
                    ->contain([
                        'YearLevels' => ['fields' => ['name']],
                        'PublishedCourses' => [
                            'fields' => ['id', 'course_id', 'academic_year', 'semester'],
                            'conditions' => [
                                'PublishedCourses.academic_year' => $academicYear,
                                'PublishedCourses.semester' => $semester,
                                'PublishedCourses.id NOT IN' => $this->find()
                                    ->select(['published_course_id'])
                                    ->from('excluded_published_course_exams'),
                                'PublishedCourses.id NOT IN' => $this->find()
                                    ->select(['published_course_id'])
                                    ->from('exam_schedules')
                            ],
                            'Courses' => ['fields' => ['id']],
                            'CourseExamGapConstraints',
                            'CourseExamConstraints',
                            'ExamRoomCourseConstraints'
                        ]
                    ])
                    ->toArray();

                foreach ($sections as $section) {
                    $examPeriod = $examPeriodsTable->find()
                        ->select(['start_date', 'end_date', 'default_number_of_invigilator_per_exam'])
                        ->where([
                            'college_id' => $collegeId,
                            'program_id' => $programId,
                            'program_type_id' => $section->program_type_id,
                            'academic_year' => $academicYear,
                            'semester' => $semester,
                            'year_level_id' => $yearLevelName
                        ])
                        ->contain(['ExamExcludedDateAndSessions'])
                        ->first();

                    if (!$examPeriod) {
                        continue;
                    }

                    $startDate = new \DateTime($examPeriod->start_date);
                    $endDate = new \DateTime($examPeriod->end_date);
                    $interval = $startDate->diff($endDate);
                    $numberOfExamDays = $interval->days + 1;

                    $excludeDateMatrix = [];
                    foreach ($examPeriod->exam_excluded_date_and_sessions ?? [] as $excluded) {
                        $index = count($excludeDateMatrix);
                        $found = false;
                        foreach ($excludeDateMatrix as $k => $v) {
                            if ($excluded->excluded_date === $v['date']) {
                                $excludeDateMatrix[$k]['sc']++;
                                $found = true;
                                if ($excludeDateMatrix[$k]['sc'] == 3) {
                                    $numberOfExamDays--;
                                }
                                break;
                            }
                        }
                        if (!$found) {
                            $excludeDateMatrix[$index] = ['date' => $excluded->excluded_date, 'sc' => 1];
                        }
                    }

                    $examDays = [];
                    $currentDate = $startDate->format('Y-m-d');
                    while ($currentDate <= $endDate->format('Y-m-d')) {
                        $found = false;
                        foreach ($excludeDateMatrix as $exclude) {
                            if ($exclude['date'] == $currentDate && $exclude['sc'] == 3) {
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $examDays[] = $currentDate;
                        }
                        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                    }

                    foreach ($section->published_courses as $publishedCourse) {
                        $courseExamConstraintActive = false;
                        foreach ($publishedCourse->course_exam_constraints ?? [] as $constraint) {
                            if ($constraint->active) {
                                $courseExamConstraintActive = true;
                                break;
                            }
                        }

                        $filteredExamDays = $examDays;
                        if ($courseExamConstraintActive) {
                            $filteredExamDays = array_filter(
                                array_map(
                                    fn($constraint) => $constraint->active ? $constraint->exam_date : null,
                                    $publishedCourse->course_exam_constraints ?? []
                                ),
                                fn($date) => !empty($date) && !in_array($date, $filteredExamDays)
                            );
                        } else {
                            foreach ($publishedCourse->course_exam_constraints ?? [] as $constraint) {
                                if (!$constraint->active) {
                                    $key = array_search($constraint->exam_date, $filteredExamDays);
                                    if ($key !== false) {
                                        unset($filteredExamDays[$key]);
                                    }
                                }
                            }
                        }

                        $index = count($publishedCourses);
                        $publishedCourses[$index] = [
                            'id' => $publishedCourse->id,
                            'course_id' => $publishedCourse->course->id,
                            'number_of_invigilator' => $examPeriod->default_number_of_invigilator_per_exam,
                            'start_date' => $examPeriod->start_date,
                            'end_date' => $examPeriod->end_date,
                            'exam_days' => array_values($filteredExamDays),
                            'section_id' => $section->id,
                            'year_level' => $section->year_level->name,
                            'weight' => 0,
                            'section_average_exam_day' => floor($numberOfExamDays / count($section->published_courses)),
                            'gap' => $publishedCourse->course_exam_gap_constraint->gap_before_exam ?? 0
                        ];

                        if (!empty($publishedCourse->course_exam_gap_constraint)) {
                            $publishedCourses[$index]['weight'] += 30;
                        }

                        $activeConstraints = array_sum(array_column($publishedCourse->course_exam_constraints ?? [], 'active'));
                        $inactiveConstraints = count($publishedCourse->course_exam_constraints ?? []) - $activeConstraints;
                        $publishedCourses[$index]['weight'] += $activeConstraints > 0 ? 30 / $activeConstraints : ($inactiveConstraints / 30);

                        $activeRoomConstraints = array_sum(array_column($publishedCourse->exam_room_course_constraints ?? [], 'active'));
                        $inactiveRoomConstraints = count($publishedCourse->exam_room_course_constraints ?? []) - $activeRoomConstraints;
                        $publishedCourses[$index]['weight'] += $activeRoomConstraints > 0 ? 30 / $activeRoomConstraints : ($inactiveRoomConstraints / 30);
                    }
                }
            }
        }

        shuffle($publishedCourses);
        usort($publishedCourses, fn($a, $b) => $b['weight'] <=> $a['weight']);

        return $publishedCourses;
    }

    /**
     * Retrieves published courses for exam scheduling organized by section.
     *
     * @param int|null $collegeId The college ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param string|null $semester The semester (e.g., '1').
     * @param int|null $programId The program ID.
     * @param array|null $programTypeIds Array of program type IDs.
     * @param array|null $departmentIds Array of department IDs.
     * @param array|null $yearLevels Array of year levels.
     * @return array
     */
    public function getPublishedCoursesForExamScheduleBySection(
        ?int $collegeId = null,
        ?string $academicYear = null,
        ?string $semester = null,
        ?int $programId = null,
        ?array $programTypeIds = null,
        ?array $departmentIds = null,
        ?array $yearLevels = null
    ): array {
        if (empty($departmentIds) || empty($yearLevels) || !$academicYear || !$semester || !$programId || !$programTypeIds) {
            return [];
        }

        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $publishedCourses = [];

        foreach ($departmentIds as $depKey => $departmentId) {
            foreach ($yearLevels as $yearLevel) {
                $yearLevelName = $this->formatYearLevel($yearLevel);

                $yearLevelData = $yearLevelsTable->find()
                    ->select(['id'])
                    ->where([
                        'name' => $yearLevelName,
                        'department_id' => $departmentId
                    ])
                    ->first();

                if (!$yearLevelData && $depKey !== 'FP') {
                    continue;
                }

                $conditions = [
                    'Sections.academicyear' => $academicYear,
                    'Sections.program_id' => $programId,
                    'Sections.program_type_id IN' => $programTypeIds,
                    'Sections.year_level_id' => $yearLevelData->id ?? 0
                ];

                if ($depKey === 'FP') {
                    $conditions['Sections.college_id'] = $collegeId;
                } else {
                    $conditions['Sections.department_id'] = $departmentId;
                }

                $sections = $this->find()
                    ->select(['id'])
                    ->where($conditions)
                    ->contain([
                        'PublishedCourses' => [
                            'fields' => ['id'],
                            'conditions' => [
                                'PublishedCourses.academic_year' => $academicYear,
                                'PublishedCourses.semester' => $semester,
                                'PublishedCourses.id NOT IN' => $this->find()
                                    ->select(['published_course_id'])
                                    ->from('excluded_published_course_exams'),
                                'PublishedCourses.id NOT IN' => $this->find()
                                    ->select(['published_course_id'])
                                    ->from('exam_schedules')
                            ]
                        ]
                    ])
                    ->toArray();

                foreach ($sections as $section) {
                    foreach ($section->published_courses as $course) {
                        $publishedCourses[$section->id][] = $course->id;
                    }
                }
            }
        }

        return $publishedCourses;
    }

    /**
     * Extracts merged section IDs from data.
     *
     * @param array|null $data Section data.
     * @return array
     */
    public function mergedSectionIds(?array $data): array
    {
        if (empty($data) || empty($data['Section']['Sections'])) {
            return [];
        }

        return array_map(fn($index) => $data['Section'][$index]['id'], $data['Section']['Sections']);
    }

    /**
     * Checks if a student should be excluded from sectionless status due to dropout, withdrawal, or dismissal.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $currentAcademicYear The current academic year (e.g., '2024/25').
     * @return int
     */
    public function dropOutWithDrawAfterLastRegistrationNotReadmittedExcludeFromSectionless(
        ?int $studentId,
        ?string $currentAcademicYear
    ): int {
        if (!$studentId || !$currentAcademicYear) {
            return 2;
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $dropOutsTable = TableRegistry::getTableLocator()->get('DropOuts');
        $clearancesTable = TableRegistry::getTableLocator()->get('Clearances');
        $dismissalsTable = TableRegistry::getTableLocator()->get('Dismissals');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $readmissionsTable = TableRegistry::getTableLocator()->get('Readmissions');

        $lastRegistration = $courseRegistrationsTable->find()
            ->select(['created', 'academic_year', 'semester'])
            ->where(['student_id' => $studentId])
            ->order(['created' => 'DESC'])
            ->first();

        if (!$lastRegistration) {
            return 2;
        }

        $dropoutCount = $dropOutsTable->find()
            ->where([
                'student_id' => $studentId,
                'drop_date >=' => $lastRegistration->created
            ])
            ->count();

        if ($dropoutCount > 0) {
            return 1;
        }

        $withdrawalCount = $clearancesTable->find()
            ->where([
                'student_id' => $studentId,
                'type' => 'withdraw',
                'confirmed' => true,
                'forced_withdrawal' => true,
                'request_date >=' => $lastRegistration->created
            ])
            ->count();

        if ($withdrawalCount > 0) {
            return $readmissionsTable->isReadmittedForYear($studentId, $currentAcademicYear) ? 2 : 1;
        }

        $dismissalCount = $dismissalsTable->find()
            ->where([
                'student_id' => $studentId,
                'dismisal_date >=' => $lastRegistration->created
            ])
            ->count();

        $dismissalStatusCount = $studentExamStatusesTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $lastRegistration->academic_year,
                'semester' => $lastRegistration->semester,
                'academic_status_id' => 4
            ])
            ->count();

        $noStatusGeneratedCount = $studentExamStatusesTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $lastRegistration->academic_year,
                'semester' => $lastRegistration->semester
            ])
            ->count();

        $noStatusCount = $studentExamStatusesTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $lastRegistration->academic_year,
                'semester' => $lastRegistration->semester,
                'OR' => [
                    'academic_status_id IS NULL',
                    'academic_status_id' => 0,
                    'academic_status_id' => ''
                ]
            ])
            ->count();

        if ($dismissalCount > 0 || $dismissalStatusCount > 0 || !$noStatusGeneratedCount || $noStatusCount) {
            return $readmissionsTable->isReadmittedForYear($studentId, $currentAcademicYear) ? 2 : 1;
        }

        return 2;
    }

    /**
     * Retrieves a student's section for a given academic year.
     *
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param int|null $studentId The student ID.
     * @return array
     */
    public function getStudentSectionInGivenAcademicYear($academicYear = null, $studentId = null, $semester = null)
    {
        $options = [
            'conditions' => [
                'Sections.academicyear' => $academicYear
            ],
            'order' => ['Sections.id' => 'DESC'],
            'contain' => [
                'YearLevels' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name', 'type', 'college_id']],
                'Colleges' => ['fields' => ['id', 'name', 'type', 'campus_id', 'stream']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
            ]
        ];

        $sections = [];

        if (!empty($studentId)) {
            if (!empty($semester) && !empty($academicYear)) {
                $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                $checkForCourseRegistration = $courseRegistrationsTable->find()
                    ->where([
                        'CourseRegistrations.student_id' => $studentId,
                        'CourseRegistrations.academic_year' => $academicYear,
                        'CourseRegistrations.semester' => $semester
                    ])
                    ->order([
                        'CourseRegistrations.academic_year' => 'ASC',
                        'CourseRegistrations.semester' => 'ASC'
                    ])
                    ->first();

                if (!empty($checkForCourseRegistration) && !empty($checkForCourseRegistration->section_id)) {

                    $options['conditions']['Sections.id'] = $checkForCourseRegistration->section_id;
                    $sections= $this->find('all', $options)->first();

                    if (!empty($sections)) {
                        return $sections;
                    }

                    unset($options['conditions']['Sections.id']);
                }
            }

            $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
            $options['conditions'][] = [
                'Sections.id IN' => $studentsSectionsTable->find()
                    ->select(['section_id'])
                    ->where(['student_id' => $studentId, 'archive' => 0])
                    ->group(['student_id', 'section_id'])
                    ->order(['section_id' => 'DESC', 'id' => 'DESC'])
            ];

            $sections = $this->find()->set($options)->first();

            if (empty($sections)) {
                unset($options['conditions'][0]);
                $options['conditions'][] = [
                    'Sections.id IN' => $studentsSectionsTable->find()
                        ->select(['section_id'])
                        ->where(['student_id' => $studentId])
                        ->group(['student_id', 'section_id'])
                        ->order(['section_id' => 'DESC', 'id' => 'DESC'])
                ];

                $sections =  $this->find('all', $options)->first();
            }
        }

        return $sections;
    }

    /**
     * Rearranges section assignments for students.
     *
     * @param string $academicYear The academic year (e.g., '2024/25').
     * @param int $departmentId The department ID.
     * @param int $yearLevel The year level ID.
     * @param int $programId The program ID.
     * @param int $programTypeId The program type ID.
     * @param string $type Sorting type (e.g., 'full_name').
     * @param bool $pre Whether to handle pre/freshman students.
     * @return int
     */
    public function rearrangeSectionList(
        string $academicYear,
        int $departmentId,
        int $yearLevel,
        int $programId,
        int $programTypeId,
        string $type,
        bool $pre = false
    ): int {
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        $conditions = [
            'Sections.academicyear' => $academicYear,
            'Sections.year_level_id' => $yearLevel,
            'Sections.program_id' => $programId,
            'Sections.program_type_id' => $programTypeId,
            'Sections.archive' => false
        ];

        if ($pre) {
            $conditions['Sections.department_id IS'] = null;
            $conditions['Sections.college_id'] = $departmentId;
        } else {
            $conditions['Sections.department_id'] = $departmentId;
        }

        $sections = $this->find()
            ->select(['id', 'name'])
            ->where($conditions)
            ->toArray();

        foreach ($sections as $section) {
            if ($publishedCoursesTable->find()->where(['section_id' => $section->id])->count()) {
                return 3;
            }
        }

        $studentIds = $studentsSectionsTable->find()
            ->select(['student_id'])
            ->where(['section_id IN' => array_column($sections, 'id')])
            ->group(['student_id'])
            ->toArray();

        $studentIds = array_column($studentIds, 'student_id');

        $students = TableRegistry::getTableLocator()->get('Students')->find()
            ->select(['id'])
            ->where(['id IN' => $studentIds])
            ->order(["Students.{$type}" => 'ASC'])
            ->toArray();

        $studentOrganized = [];
        $sectionCount = 0;
        $count = 0;

        foreach ($students as $student) {
            $count++;
            $studentOrganized[$sectionCount][] = $student->id;
            if ($count % 50 == 0) {
                $sectionCount++;
            }
        }

        $lastStop = 0;

        foreach ($sections as $section) {
            $classSize = $studentsSectionsTable->find()
                ->where(['section_id' => $section->id, 'archive' => false])
                ->count();

            if (!empty($studentOrganized[$lastStop])) {
                foreach ($studentOrganized[$lastStop] as $studentId) {
                    $belongsToSection = $studentsSectionsTable->find()
                        ->where([
                            'section_id' => $section->id,
                            'student_id' => $studentId,
                            'archive' => false
                        ])
                        ->count();

                    if (!$belongsToSection) {
                        $ownSection = $studentsSectionsTable->find()
                            ->select(['id'])
                            ->where(['student_id' => $studentId, 'archive' => false])
                            ->order(['created' => 'DESC'])
                            ->first();

                        if ($ownSection) {
                            $newSection = $studentsSectionsTable->newEntity([
                                'id' => $ownSection->id,
                                'student_id' => $studentId,
                                'section_id' => $section->id
                            ]);

                            $studentsSectionsTable->save($newSection);
                        }
                    }
                }
            }
            $lastStop++;
        }

        return 0;
    }

    /**
     * Swaps students in a specific batch across sections.
     *
     * @param string $batchAcademicYear The batch academic year.
     * @param string $academicYear The academic year (e.g., '2024/25').
     * @param string $semester The semester (e.g., '1').
     * @param int $departmentId The department ID.
     * @param int $yearLevel The year level ID.
     * @param int $programId The program ID.
     * @param int $programTypeId The program type ID.
     * @param string $type Sorting type (e.g., 'full_name').
     * @param bool $pre Whether to handle pre/freshman students.
     * @return void
     */
    public function swapTheWholeStudentInSpecificBatch(
        string $batchAcademicYear,
        string $academicYear,
        string $semester,
        int $departmentId,
        int $yearLevel,
        int $programId,
        int $programTypeId,
        string $type,
        bool $pre = false
    ): void {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');

        $conditions = [
            'Sections.academicyear' => $academicYear,
            'Sections.year_level_id' => $yearLevel,
            'Sections.program_id' => $programId,
            'Sections.program_type_id' => $programTypeId
        ];

        if ($pre) {
            $conditions['Sections.department_id IS'] = null;
            $conditions['Sections.college_id'] = $departmentId;
        } else {
            $conditions['Sections.department_id'] = $departmentId;
        }

        $sections = $this->find()
            ->select(['id', 'name'])
            ->where($conditions)
            ->order(['Sections.name' => 'ASC'])
            ->toArray();

        $studentConditions = [
            'Students.program_id' => $programId,
            'Students.program_type_id' => $programTypeId,
            'Students.admissionyear' => $academicYearsTable->getAcademicYearBeginningDate($batchAcademicYear),
            'Students.graduated' => false
        ];

        if ($pre) {
            $studentConditions['Students.department_id IS'] = null;
            $studentConditions['Students.college_id'] = $departmentId;
        } else {
            $studentConditions['Students.department_id'] = $departmentId;
        }

        $students = $studentsTable->find()
            ->select(['id'])
            ->where($studentConditions)
            ->order(["Students.{$type}" => 'ASC'])
            ->toArray();

        $studentOrganized = [];
        $sectionCount = 0;
        $count = 0;

        foreach ($students as $student) {
            $count++;
            $studentOrganized[$sectionCount][] = $student->id;
            if ($count % 50 == 0) {
                $sectionCount++;
            }
        }

        $lastStop = 0;

        foreach ($sections as $section) {
            $sectionCoursePublications = $publishedCoursesTable->find()
                ->select(['id', 'course_id'])
                ->where(['section_id' => $section->id, 'semester' => $semester])
                ->toArray();

            $studentSectionUpdate = ['StudentsSection' => []];
            $courseRegistrationUpdate = ['CourseRegistration' => []];

            if (!empty($studentOrganized[$lastStop])) {
                foreach ($studentOrganized[$lastStop] as $studentId) {
                    $belongsToSection = $studentsSectionsTable->find()
                        ->where(['section_id' => $section->id, 'student_id' => $studentId])
                        ->count();

                    if (!$belongsToSection) {
                        $ownSection = $studentsSectionsTable->find()
                            ->select(['id', 'section_id'])
                            ->where(['student_id' => $studentId])
                            ->order(['created' => 'DESC'])
                            ->first();

                        if ($ownSection) {
                            $ownSectionCourses = $publishedCoursesTable->find()
                                ->select(['id', 'course_id'])
                                ->where(['section_id' => $ownSection->section_id, 'semester' => $semester])
                                ->toArray();

                            if (count($ownSectionCourses) == count($sectionCoursePublications) && $this->similarCourseInSection($ownSectionCourses, $sectionCoursePublications)) {
                                $courseRegistrationUpdate['CourseRegistration'] = array_merge(
                                    $courseRegistrationUpdate['CourseRegistration'],
                                    $this->publicationMappingWithCourseRegistration($ownSectionCourses, $section->id, $studentId)
                                );

                                $studentSectionUpdate['StudentsSection'][] = [
                                    'student_id' => $studentId,
                                    'section_id' => $section->id
                                ];
                                $studentSectionUpdate['StudentsSection'][] = [
                                    'id' => $ownSection->id,
                                    'archive' => true
                                ];
                            } elseif (empty($ownSectionCourses) && empty($sectionCoursePublications)) {
                                $studentSectionUpdate['StudentsSection'][] = [
                                    'id' => $ownSection->id,
                                    'student_id' => $studentId,
                                    'section_id' => $section->id
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($studentSectionUpdate['StudentsSection'])) {
                $studentsSectionsTable->saveMany($studentSectionUpdate['StudentsSection'], ['validate' => false]);
            }

            if (!empty($courseRegistrationUpdate['CourseRegistration'])) {
                TableRegistry::getTableLocator()->get('CourseRegistrations')->saveMany($courseRegistrationUpdate['CourseRegistration'], ['validate' => false]);
            }

            $lastStop++;
        }
    }

    /**
     * Swaps a student between two sections.
     *
     * @param array $studentDetail Student details including 'Student' => ['id'].
     * @param int $previousSectionId The previous section ID.
     * @param int $targetSectionId The target section ID.
     * @return void
     */
    public function swapStudentSection(array $studentDetail, int $previousSectionId, int $targetSectionId): void
    {
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        // Find the student's current section assignment
        $ownSection = $studentsSectionsTable->find()
            ->select(['id'])
            ->where(['student_id' => $studentDetail['Student']['id']])
            ->first();

        if (!$ownSection) {
            return;
        }

        // Retrieve previous and target section details
        $previousSection = $this->find()
            ->select(['id'])
            ->where(['id' => $previousSectionId, 'archive' => false])
            ->contain([
                'YearLevels' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->first();

        $targetSection = $this->find()
            ->select(['id'])
            ->where(['id' => $targetSectionId, 'archive' => false])
            ->contain([
                'YearLevels' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->first();

        if (!$previousSection || !$targetSection) {
            return;
        }

        // Retrieve published courses for both sections
        $previousSectionCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where(['section_id' => $previousSection->id])
            ->toArray();

        $targetSectionCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where(['section_id' => $targetSection->id])
            ->toArray();

        $studentSectionUpdate = ['StudentsSection' => []];
        $courseRegistrationUpdate = ['CourseRegistration' => []];

        // Check if sections have similar courses or no courses
        if (count($previousSectionCourses) === count($targetSectionCourses) &&
            $this->similarCourseInSection($previousSectionCourses, $targetSectionCourses)) {
            // Update course registrations and section
            $courseRegistrationUpdate['CourseRegistration'] = $this->publicationMappingWithCourseRegistration(
                $previousSectionCourses,
                $targetSectionId,
                $studentDetail['Student']['id']
            );

            $studentSectionUpdate['StudentsSection'][] = [
                'id' => $ownSection->id,
                'student_id' => $studentDetail['Student']['id'],
                'section_id' => $targetSectionId
            ];
        } elseif (empty($previousSectionCourses) && empty($targetSectionCourses)) {
            // Simple section move
            $studentSectionUpdate['StudentsSection'][] = [
                'id' => $ownSection->id,
                'student_id' => $studentDetail['Student']['id'],
                'section_id' => $targetSectionId
            ];
        }

        // Save student section updates
        if (!empty($studentSectionUpdate['StudentsSection'])) {
            $studentsSectionsTable->saveMany(
                $studentsSectionsTable->newEntities($studentSectionUpdate['StudentsSection']),
                ['validate' => false]
            );
        }

        // Save course registration updates
        if (!empty($courseRegistrationUpdate['CourseRegistration'])) {
            $courseRegistrationsTable->saveMany(
                $courseRegistrationsTable->newEntities($courseRegistrationUpdate['CourseRegistration']),
                ['validate' => false]
            );
        }
    }



/**
 * Checks if two sections have similar published courses.
 *
 * @param array $ownSectionCourses Courses in the original section.
 * @param array $sectionCourses Courses in the target section.
 * @return bool
 */
public function similarCourseInSection(array $ownSectionCourses, array $sectionCourses): bool
{
    if (count($ownSectionCourses) !== count($sectionCourses)) {
        return false;
    }

    $ownCourseIds = array_column(array_column($ownSectionCourses, 'PublishedCourse'), 'course_id');
    $sectionCourseIds = array_column(array_column($sectionCourses, 'PublishedCourse'), 'course_id');

    return empty(array_diff($ownCourseIds, $sectionCourseIds));
}

/**
 * Maps published courses to course registrations for section movement.
 *
 * @param array $ownSectionCourses Courses in the original section.
 * @param int $targetSectionId The target section ID.
 * @param int $studentId The student ID.
 * @return array
 */
public function publicationMappingWithCourseRegistration(array $ownSectionCourses, int $targetSectionId, int $studentId): array
{
    $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
    $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

    $updates = [];

    foreach ($ownSectionCourses as $course) {
        $registration = $courseRegistrationsTable->find()
            ->select(['id', 'published_course_id'])
            ->where([
                'student_id' => $studentId,
                'published_course_id' => $course['PublishedCourse']['id']
            ])
            ->first();

        $targetCourse = $publishedCoursesTable->find()
            ->select(['id', 'section_id'])
            ->where([
                'section_id' => $targetSectionId,
                'course_id' => $course['PublishedCourse']['course_id']
            ])
            ->first();

        if ($registration && $targetCourse) {
            $updates[$registration->id] = [
                'id' => $registration->id,
                'published_course_id' => $targetCourse->id,
                'section_id' => $targetCourse->section_id
            ];
        }
    }

    return array_values($updates);
}

/**
 * Automatically downgrades sections for students.
 *
 * @param int $departmentCollegeId The department or college ID.
 * @param string $academicYear The academic year (e.g., '2024/25').
 * @param bool $pre Whether to handle pre/freshman students.
 * @return void
 */
public function automaticDownGradeSection(int $departmentCollegeId, string $academicYear, bool $pre = false): void
{
    $studentsTable = TableRegistry::getTableLocator()->get('Students');
    $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
    $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

    $conditions = [
        'academic_year' => $academicYear,
        'graduated' => false
    ];

    if ($pre) {
        $conditions['college_id'] = $departmentCollegeId;
        $conditions['department_id IS'] = null;
    } else {
        $conditions['department_id'] = $departmentCollegeId;
    }

    $students = $studentsTable->find()
        ->select(['id'])
        ->where($conditions)
        ->toArray();

    foreach ($students as $student) {
        $sections = $studentsSectionsTable->find()
            ->select(['id', 'section_id'])
            ->where(['student_id' => $student->id])
            ->order(['created' => 'ASC'])
            ->toArray();

        if (empty($sections)) {
            continue;
        }

        $registrations = $courseRegistrationsTable->find()
            ->select(['section_id', 'academic_year', 'semester'])
            ->where(['student_id' => $student->id])
            ->group(['academic_year', 'semester'])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
            ->toArray();

        $sectionIds = array_column($sections, 'section_id', 'id');

        foreach ($registrations as $registration) {
            if (isset($sectionIds[$registration->section_id])) {
                unset($sectionIds[$registration->section_id]);
            }
        }

        if (count($sectionIds) > 1) {
            $firstSectionId = array_key_first($sectionIds);
            $activeSectionId = $sectionIds[$firstSectionId];
            unset($sectionIds[$firstSectionId]);

            $section = $studentsSectionsTable->get($activeSectionId);
            $section->archive = false;
            $studentsSectionsTable->save($section);

            $studentsSectionsTable->deleteAll(['id IN' => array_keys($sectionIds)], false);
        }
    }
}

/**
 * Downgrades selected sections.
 *
 * @param array $selectedSectionIds Array of section IDs.
 * @return array
 */
public function downgradeSelectedSection(array $selectedSectionIds): array
{
    $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
    $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
    $downgradableSection = ['success' => [], 'unsuccess' => []];

    foreach ($selectedSectionIds as $secId => $id) {
        if (empty($id)) {
            $downgradableSection['unsuccess'][$secId] = $id;
            continue;
        }

        $precedingSection = $this->getPrecedingSection($secId);

        if (!$precedingSection) {
            $downgradableSection['unsuccess'][$secId] = $id;
            continue;
        }

        $currentStudents = $studentsSectionsTable->find()
            ->select(['id'])
            ->where(['section_id' => $secId, 'archive' => false])
            ->toArray();

        if (!$publishedCoursesTable->isCoursePublishedInSection($secId) && !empty($currentStudents)) {
            $studentsSectionsTable->deleteAll(['id IN' => array_column($currentStudents, 'id')], false);
            $this->deleteAll(['id' => $secId], false);

            $studentsSectionsTable->updateAll(
                ['archive' => false],
                ['section_id' => $precedingSection['Section']['id']]
            );

            $preceding = $this->get($precedingSection['Section']['id']);
            $preceding->archive = false;
            $this->save($preceding);

            $downgradableSection['success'][$secId] = $id;
        } else {
            $downgradableSection['unsuccess'][$secId] = $id;
        }
    }

    return $downgradableSection;
}

/**
 * Retrieves the preceding section for a given section.
 *
 * @param int $sectionId The section ID.
 * @return array
 */
public function getPrecedingSection(int $sectionId): array
{
    $section = $this->find()
        ->select(['id', 'name', 'academic_year', 'department_id', 'year_level_id', 'program_id', 'program_type_id'])
        ->where(['id' => $sectionId])
        ->contain(['Departments', 'YearLevels'])
        ->first();

    if (!$section) {
        return [];
    }

    $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');

    $previousAcademicYear = $studentExamStatusesTable->getPreviousSemester($section->academic_year);
    $previousYearLevel = $yearLevelsTable->find()
        ->select(['id'])
        ->where([
            'department_id' => $section->department_id,
            'id <' => $section->year_level_id
        ])
        ->order(['id' => 'DESC'])
        ->first();

    if (!$previousYearLevel) {
        return [];
    }

    $previousSection = $this->find()
        ->select(['id', 'name'])
        ->where([
            'name' => $this->getPrecedingSectionName($section->name),
            'program_id' => $section->program_id,
            'program_type_id' => $section->program_type_id,
            'year_level_id' => $previousYearLevel->id,
            'department_id' => $section->department_id,
            'academic_year' => $previousAcademicYear['academic_year'],
            'archive' => true
        ])
        ->contain(['Departments', 'YearLevels'])
        ->first();

    return $previousSection ? $previousSection->toArray() : [];
}

/**
 * Generates the preceding section name.
 *
 * @param string $currentSectionName The current section name.
 * @return string
 */
public function getPrecedingSectionName(string $currentSectionName): string
{
    $variableSectionName = substr($currentSectionName, strrpos($currentSectionName, ' ') + 1);
    $firstSpace = strpos($currentSectionName, ' ');
    $secondSpace = strrpos($currentSectionName, ' ');
    $prefixSectionName = substr($currentSectionName, 0, $firstSpace);
    $fixedSectionName = substr($currentSectionName, $firstSpace + 1, $secondSpace - $firstSpace - 1);

    $prefixCharacter = substr($prefixSectionName, 0, -1);
    $prefixYearLevel = (int)substr($prefixSectionName, -1);

    return $prefixCharacter . ($prefixYearLevel - 1) . ' ' . $fixedSectionName . ' ' . $variableSectionName;
}

    /**
     * Upgrades selected sections.
     *
     * @param array $selectedSectionIds Array of section IDs.
     * @return array
     */
    public function upgradeSelectedSection(array $selectedSectionIds): array
    {
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $upgradeableSection = [];

        foreach ($selectedSectionIds as $secId) {
            if (empty($secId)) {
                continue;
            }

            $students = $this->getSectionActiveStudentsRegistered($secId);

            if (empty($students)) {
                continue;
            }

            $academicYear = $this->find()
                ->select(['academic_year'])
                ->where(['id' => $secId])
                ->first()->academic_year;

            $upgradeSection = $this->getUpgradableSectionName($secId);

            if ($upgradeSection) {
                $newSection = $this->newEntity($upgradeSection['Section']);
                $this->save($newSection);
            }

            $upgradable = [];
            $unupgradable = [];

            foreach ($students as $index => $student) {
                $studentStatus = $studentExamStatusesTable->isStudentPassed($student['StudentsSection']['student_id'], $academicYear);
                $allGradesValid = $this->checkAllRegisteredAddedCoursesAreGraded(
                    $student['StudentsSection']['student_id'],
                    $secId,
                    1,
                    '',
                    0,
                    0
                );

                if (in_array($studentStatus, [2, 4]) || !$allGradesValid) {
                    $unupgradable[$index] = $student['StudentsSection']['student_id'];
                } else {
                    $upgradable[$index] = [
                        'student_id' => $student['StudentsSection']['student_id'],
                        'section_id' => $this->id
                    ];
                }
            }

            if (!empty($upgradable)) {
                $studentsSectionsTable->updateAll(['archive' => true], ['section_id' => $secId]);
                if ($studentsSectionsTable->saveMany($upgradable, ['validate' => false])) {
                    $oldSection = $this->newEntity(['id' => $secId, 'archive' => true]);
                    if ($this->save($oldSection)) {
                        $upgradeableSection[$this->id] = $upgradeSection['Section']['name'];
                    }
                }
            } elseif (!empty($this->id)) {
                $this->deleteAll(['id' => $this->id], false);
            }
        }

        return $upgradeableSection;
    }

/**
 * Generates an upgradable section name.
 *
 * @param int $sectionId The section ID.
 * @return array
 */
public function getUpgradableSectionName(int $sectionId): array
{
    $section = $this->find()
        ->select(['id', 'name', 'college_id', 'program_id', 'program_type_id', 'academic_year', 'department_id', 'year_level_id', 'curriculum_id'])
        ->where(['id' => $sectionId])
        ->first();

    if (!$section) {
        return [];
    }

    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
    $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

    $nextYearLevelId = $yearLevelsTable->getNextYearLevel($section->year_level_id, $section->department_id);

    if (!$nextYearLevelId) {
        return [];
    }

    $variableSectionName = substr($section->name, strrpos($section->name, ' ') + 1);
    $firstSpace = strpos($section->name, ' ');
    $secondSpace = strrpos($section->name, ' ');
    $prefixSectionName = substr($section->name, 0, $firstSpace);
    $fixedSectionName = substr($section->name, $firstSpace + 1, $secondSpace - $firstSpace - 1);

    $prefixCharacter = substr($prefixSectionName, 0, -1);
    $prefixYearLevel = (int)substr($prefixSectionName, -1);

    $nextAcademicYear = $studentExamStatusesTable->getNextSemester($section->academic_year);

    $newSection = [
        'Section' => [
            'college_id' => $section->college_id,
            'program_id' => $section->program_id,
            'program_type_id' => $section->program_type_id,
            'academic_year' => $nextAcademicYear['academic_year'],
            'department_id' => $section->department_id,
            'year_level_id' => $nextYearLevelId,
            'name' => $prefixCharacter . ($prefixYearLevel + 1) . ' ' . $fixedSectionName . ' ' . $variableSectionName,
            'previous_section_id' => $section->id
        ]
    ];

    if ($section->curriculum_id && is_numeric($section->curriculum_id) && $section->curriculum_id > 0) {
        $newSection['Section']['curriculum_id'] = $section->curriculum_id;
    }

    return $newSection;
}

/**
 * Finds a representative student in a section.
 *
 * @param array $studentDetail Student details.
 * @param string $currentAcademicYear The current academic year.
 * @param string $studentAdmissionYear The student's admission year.
 * @param string $sectionInThisAC The section in the academic year.
 * @return int
 */
public function findMeRepresentativeStudentInSection(
    array $studentDetail,
    string $currentAcademicYear,
    string $studentAdmissionYear,
    string $sectionInThisAC
): int {
    $counter = 0;

    do {
        $conditions = [
            'section_id IN' => $this->find()
                ->select(['id'])
                ->where([
                    'academic_year' => $currentAcademicYear,
                    'program_id' => $studentDetail['Student']['program_id'],
                    'program_type_id' => $studentDetail['Student']['program_type_id']
                ])
        ];

        if (!empty($studentDetail['Student']['department_id'])) {
            $conditions['section_id IN'] = $this->find()
                ->select(['id'])
                ->where(['department_id' => $studentDetail['Student']['department_id']]);
            $studentConditions = [
                'department_id' => $studentDetail['Student']['department_id'],
                'admissionyear' => $studentAdmissionYear,
                'curriculum_id' => $studentDetail['Student']['curriculum_id']
            ];
        } else {
            $conditions['section_id IN'] = $this->find()
                ->select(['id'])
                ->where([
                    'college_id' => $studentDetail['Student']['college_id'],
                    'department_id IS' => null
                ]);
            $studentConditions = ['admissionyear' => $studentAdmissionYear];
        }

        $possibleSection = $this->StudentsSections->find()
            ->select(['student_id'])
            ->where($conditions)
            ->contain([
                'Students' => ['fields' => ['id'], 'conditions' => $studentConditions]
            ])
            ->limit(1)
            ->first();

        if ($possibleSection && $possibleSection->student_id) {
            $sectionCheck = $this->StudentsSections->find()
                ->select(['section_id'])
                ->where([
                    'student_id' => $possibleSection->student_id,
                    'section_id IN' => $this->find()
                        ->select(['id'])
                        ->where([
                            'academic_year' => $sectionInThisAC,
                            'department_id' => $studentDetail['Student']['department_id'],
                            'program_id' => $studentDetail['Student']['program_id'],
                            'program_type_id' => $studentDetail['Student']['program_type_id']
                        ])
                ])
                ->limit(1)
                ->first();

            if ($sectionCheck) {
                return $sectionCheck->section_id;
            }
        }

        if ($counter > 100) {
            return 0;
        }

        $counter++;
    } while (true);

    return 0;
}

/**
 * Retrieves a student's active section.
 *
 * @param int $studentId The student ID.
 * @param string $academicYear The academic year (e.g., '2024/25').
 * @return array
 */
public function getStudentActiveSection(int $studentId, string $academicYear = ''): array
{
    if (!$studentId) {
        return [];
    }

    $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

    $studentSection = $studentsSectionsTable->find()
        ->select(['section_id'])
        ->where(['student_id' => $studentId, 'archive' => false])
        ->order(['created' => 'DESC'])
        ->first();

    if (!$studentSection) {
        return [];
    }

    $conditions = ['Sections.id' => $studentSection->section_id];

    if ($academicYear) {
        $conditions['Sections.academicyear LIKE'] = $academicYear . '%';
    }

    $section = $this->find()
        ->select(['id', 'name', 'academic_year'])
        ->where($conditions)
        ->contain([
            'YearLevels', 'Departments', 'Colleges', 'Programs', 'ProgramTypes'
        ])
        ->first();

    return $section ? $section->toArray() : [];
}

    /**
     * Retrieves the most representative courses taken in a section.
     *
     * @param int $sectionId The section ID.
     * @return array
     */
    public function getMostRepresentativeTakenCourse(int $sectionId): array
    {
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $readmissionsTable = TableRegistry::getTableLocator()->get('Readmissions');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $section = $this->find()
            ->select(['id', 'department_id', 'year_level_id', 'academic_year'])
            ->where(['id' => $sectionId])
            ->contain(['YearLevels'])
            ->first();

        if (!$section) {
            return ['taken' => [], 'selected_student' => []];
        }

        $yearLevels = $yearLevelsTable->find()
            ->select(['id'])
            ->where([
                'id <' => $section->year_level_id,
                'department_id' => $section->department_id
            ])
            ->toArray();

        $previousAcademicYears = [];
        $currentAcademicYear = $section->academic_year;

        foreach ($yearLevels as $yearLevel) {
            $previousSemester = $studentExamStatusesTable->getPreviousSemester($currentAcademicYear);
            $previousAcademicYears[$previousSemester['academic_year']] = $previousSemester['academic_year'];
            $currentAcademicYear = $previousSemester['academic_year'];
        }

        $students = $studentsSectionsTable->find()
            ->select(['student_id'])
            ->where(['section_id' => $sectionId])
            ->toArray();

        $studentIds = [];
        $selectedStudentIds = [$sectionId => []];

        foreach ($students as $student) {
            if (!$readmissionsTable->hasEverBeenReadmitted($student->student_id, $section->academic_year)) {
                $studentIds[] = $student->student_id;
                $selectedStudentIds[$sectionId][] = $student->student_id;
            }
        }

        $totalStudents = count($studentIds);

        $previousSections = [$sectionId];

        if (!empty($yearLevels)) {
            $previousStudentSections = $studentsSectionsTable->find()
                ->select(['student_id', 'section_id'])
                ->where([
                    'student_id IN' => $studentIds,
                    'section_id IN' => $this->find()
                        ->select(['id'])
                        ->where(['year_level_id IN' => array_column($yearLevels, 'id')])
                ])
                ->toArray();

            foreach ($previousStudentSections as $prevSection) {
                $prevYearLevelId = $this->find()
                    ->select(['year_level_id'])
                    ->where(['id' => $prevSection->section_id])
                    ->first()->year_level_id;

                $prevAcademicYear = $this->find()
                    ->select(['academic_year'])
                    ->where(['id' => $prevSection->section_id])
                    ->first()->academic_year;

                if ($prevSection->section_id != $sectionId && in_array($prevAcademicYear, $previousAcademicYears) &&
                    $section->year_level_id != $prevYearLevelId) {
                    $previousSections[$prevSection->section_id] = $prevSection->section_id;
                }
            }
        }

        $publishedCourses = $publishedCoursesTable->find()
            ->select(['id', 'course_id'])
            ->where([
                'section_id IN' => $previousSections,
                'department_id' => $section->department_id,
                'drop' => false,
                'academic_year IN' => array_keys($previousAcademicYears),
                'id IN' => $courseRegistrationsTable->find()
                    ->select(['published_course_id'])
                    ->where(['student_id IN' => $studentIds])
            ])
            ->toArray();

        $takenCourses = [$sectionId => []];

        if (!empty($publishedCourses)) {
            $majorityNumber = ($totalStudents / 2) + 1;
            foreach ($publishedCourses as $course) {
                if ($courseRegistrationsTable->ExamGrades->isGradeSubmitted($course->id, $studentIds) > $majorityNumber) {
                    $takenCourses[$sectionId][] = $course->course_id;
                }
            }
        }

        if (empty($takenCourses[$sectionId])) {
            $takenCourses[$sectionId][] = 0;
        }

        return ['taken' => $takenCourses, 'selected_student' => $selectedStudentIds];
    }

    /**
     * Updates course registrations and sections.
     *
     * @param string $admissionYear The admission year.
     * @param int $departmentId The department ID.
     * @param string $academicYear The academic year (e.g., '2024/25').
     * @param string $semester The semester (e.g., '1').
     * @param bool $pre Whether to handle pre/freshman students.
     * @return void
     */
    public function updateCourseRegistrationAndSection(
        string $admissionYear,
        int $departmentId,
        string $academicYear,
        string $semester,
        bool $pre = false
    ): void {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        $conditions = [
            'academic_year LIKE' => $admissionYear . '%',
            'graduated' => false
        ];

        if ($pre) {
            $conditions['college_id'] = $departmentId;
            $conditions['department_id IS'] = null;
        } else {
            $conditions['department_id'] = $departmentId;
        }

        $students = $studentsTable->find()
            ->select(['id'])
            ->where($conditions)
            ->toArray();

        $updates = ['CourseRegistration' => []];

        foreach ($students as $student) {
            $currentSection = $studentsSectionsTable->find()
                ->select(['id', 'section_id'])
                ->where([
                    'student_id' => $student->id,
                    'archive' => false,
                    'section_id IN' => $this->find()
                        ->select(['id'])
                        ->where(['academic_year' => $academicYear])
                ])
                ->first();

            $registrations = $courseRegistrationsTable->find()
                ->select(['id', 'student_id', 'published_course_id', 'section_id', 'academic_year', 'semester'])
                ->where([
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                    'semester' => $semester
                ])
                ->contain(['Courses'])
                ->toArray();

            foreach ($registrations as $course) {
                $currentCourse = $publishedCoursesTable->find()
                    ->select(['id', 'section_id'])
                    ->where([
                        'Courses.course_id' => $course->course->id,
                        'section_id' => $currentSection->section_id,
                        'academic_year' => $academicYear,
                        'semester' => $semester,
                        'drop' => false
                    ])
                    ->contain(['Courses'])
                    ->first();

                if ($course->section_id != $currentCourse->section_id) {
                    $updates['CourseRegistration'][] = [
                        'id' => $course->id,
                        'section_id' => $currentSection->section_id,
                        'student_id' => $course->student_id,
                        'published_course_id' => $currentCourse->id
                    ];
                }
            }
        }

        if (!empty($updates['CourseRegistration'])) {
            $courseRegistrationsTable->saveMany($updates['CourseRegistration'], ['validate' => false]);
        }
    }

    /**
     * Determines the student's year and semester level based on status.
     *
     * @param int $studentId The student ID.
     * @param string $academicYear The academic year (e.g., '2024/25').
     * @param string $semester The semester (e.g., '1').
     * @return int
     */
    public function studentYearLevel(int $studentId, string $academicYear, string $semester): int
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $registration = $courseRegistrationsTable->find()
            ->select(['year_level_id'])
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain(['YearLevels' => ['fields' => ['name']]])
            ->first();

        if (!$registration || empty($registration->year_level)) {
            return 1;
        }

        return (int)filter_var($registration->year_level->name, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Retrieves a student's year level.
     *
     * @param int $studentId The student ID.
     * @return array
     */
    public function getStudentYearLevel(int $studentId): array
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $registration = $courseRegistrationsTable->find()
            ->select(['section_id', 'academic_year'])
            ->where([
                'student_id' => $studentId,
                'section_id IN' => $this->find()
                    ->select(['id'])
                    ->where(['archive' => false])
            ])
            ->order(['created' => 'DESC', 'academic_year' => 'DESC'])
            ->first();

        $section = $registration
            ? $this->find()
                ->select(['academic_year'])
                ->where(['id' => $registration->section_id])
                ->contain(['YearLevels' => ['fields' => ['name']]])
                ->first()
            : $this->find()
                ->select(['academic_year'])
                ->where([
                    'id IN' => $studentsSectionsTable->find()
                        ->select(['section_id'])
                        ->where(['student_id' => $studentId])
                ])
                ->contain(['YearLevels' => ['fields' => ['name']]])
                ->order(['academic_year' => 'DESC'])
                ->first();

        $yearAcademic = [
            'academic_year' => $section->academic_year ?? '',
            'year' => $section && $section->year_level ? $section->year_level->name : '1st'
        ];

        return $yearAcademic;
    }

    /**
     * Automatically upgrades sections based on academic year and department.
     *
     * @param string $academicYear The academic year (e.g., '2024/25').
     * @param int|null $departmentId The department ID.
     * @return void
     */
    public function autoSectionUpgrade(string $academicYear, ?int $departmentId = null): void
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        $conditions = [
            'Sections.archive' => false,
            'Sections.academicyear' => $academicYear
        ];

        if ($departmentId) {
            $conditions['Sections.department_id'] = $departmentId;
        }

        $sections = $this->find()
            ->select(['id'])
            ->where($conditions)
            ->toArray();

        $lastPublishedCourses = [];
        foreach ($sections as $section) {
            $lastPublishedCourses[$section->id] = $publishedCoursesTable->lastPublishedCoursesForSection($section->id);
        }

        $upgradableSections = [];
        $unupgradableSections = [];

        foreach ($lastPublishedCourses as $sectionId => $courses) {
            $allGradesSubmitted = array_reduce(
                array_keys($courses),
                fn($carry, $courseId) => $carry && $publishedCoursesTable->CourseRegistrations->ExamGrades->isGradeSubmitted($courseId),
                true
            );

            if ($allGradesSubmitted) {
                $upgradableSections[$sectionId] = $sectionId;
            } else {
                $unupgradableSections[] = $sectionId;
            }
        }

        $this->upgradeSelectedSection($upgradableSections);
    }

    /**
     * Retrieves equivalent program types.
     *
     * @param int $programTypeId The program type ID.
     * @return array
     */
    public function getEquivalentProgramTypes(int $programTypeId = 0): array
    {
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $equivalentIds = $programTypesTable->find()
            ->select(['equivalent_to_id'])
            ->where(['id' => $programTypeId])
            ->first();

        $programTypes = [$programTypeId];

        if ($equivalentIds && $equivalentIds->equivalent_to_id) {
            $equivalent = json_decode($equivalentIds->equivalent_to_id, true) ?: [];
            $programTypes = array_merge($programTypes, $equivalent);
        }

        return $programTypes;
    }

    /**
     * Removes duplicate student section records.
     *
     * @param int|null $sectionId The section ID.
     * @return int
     */
    public function removeDuplicateStudentSections(?int $sectionId = null): int
    {
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $conditions = ['StudentsSections.archive' => false];

        if ($sectionId && is_numeric($sectionId)) {
            $conditions['StudentsSections.section_id'] = $sectionId;
        }

        $duplicates = $studentsSectionsTable->find()
            ->select(['StudentsSections.id', 'StudentsSections.student_id', 'StudentsSections.section_id'])
            ->where($conditions)
            ->join([
                'table' => 'students_sections',
                'alias' => 'a2',
                'type' => 'INNER',
                'conditions' => [
                    'StudentsSections.id < a2.id',
                    'StudentsSections.student_id = a2.student_id',
                    'StudentsSections.section_id = a2.section_id'
                ]
            ])
            ->toArray();

        if (empty($duplicates)) {
            return 0;
        }

        $duplicateIds = array_column($duplicates, 'id');
        $studentsSectionsTable->deleteAll(['id IN' => $duplicateIds], false);

        return count($duplicateIds);
    }

    /**
     * Checks if all registered and added courses are graded.
     *
     * @param int|array|null $studentId The student ID or array of IDs.
     * @param int|null $sectionId The section ID.
     * @param bool $checkForInvalidGrades Check for invalid grades.
     * @param string $fromStudent Source context.
     * @param bool $skipFGrade Skip F grades.
     * @param bool $getErrorMessage Return error messages.
     * @return mixed
     */
    public function checkAllRegisteredAddedCoursesAreGraded(
        $studentId = null,
        $sectionId = null,
        $checkForInvalidGrades = 0,
        $fromStudent = '',
        $skipFGrade = 0,
        $getErrorMessage = 0,
        $skipIGrade = 0
    ) {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        // Validate student ID
        $validStudentId = false;
        if (!empty($studentId) && !is_array($studentId) && is_numeric($studentId) && $studentId) {
            $validStudentId = $studentsTable->find()
                    ->where(['Students.id' => $studentId])
                    ->count() > 0;
        }

        // Validate section ID
        $validSection = true;
        $selectedSectionDetails = [];
        if (!empty($sectionId)) {
            $validSection = $sectionsTable->find()
                    ->where(['Sections.id' => $sectionId])
                    ->count() > 0;
        }

        if ($validSection && !empty($sectionId)) {
            $selectedSectionDetails = $sectionsTable->find()
                ->where(['Sections.id' => $sectionId])
                ->first();
        }

        $academicYear = '';
        $semester = '';

        // Build query conditions
        $conditions = [];
        if (!empty($studentId) && ($validStudentId || is_array($studentId))) {
            $conditions[] = [
                'Students.id' => $studentId,
                'Students.graduated' => 0
            ];
        } else {
            return 0;
        }

        if (!empty($selectedSectionDetails)) {
            $academicYear = $selectedSectionDetails->academicyear;
        }

        // Define query
        $query = $studentsTable->find()
            ->select([
                'Students.id',
                'Students.curriculum_id',
                'Students.first_name',
                'Students.middle_name',
                'Students.last_name',
                'Students.studentnumber',
                'Students.admissionyear',
                'Students.gender',
                'Students.academicyear',
                'Students.student_national_id'
            ])
            ->contain([
                'Curriculums' => [
                    'fields' => [
                        'Curriculums.id',
                        'Curriculums.type_credit',
                        'Curriculums.minimum_credit_points',
                        'Curriculums.certificate_name',
                        'Curriculums.amharic_degree_nomenclature',
                        'Curriculums.specialization_amharic_degree_nomenclature',
                        'Curriculums.english_degree_nomenclature',
                        'Curriculums.specialization_english_degree_nomenclature',
                        'Curriculums.minimum_credit_points',
                        'Curriculums.name',
                        'Curriculums.year_introduced'
                    ],
                    'Departments',
                    'CourseCategories' => [
                        'fields' => ['CourseCategories.id', 'CourseCategories.curriculum_id']
                    ]
                ],
                'Departments' => [
                    'fields' => ['Departments.name']
                ],
                'Programs' => [
                    'fields' => ['Programs.name']
                ],
                'ProgramTypes' => [
                    'fields' => ['ProgramTypes.name']
                ],
                'CourseRegistrations' => [
                    'fields' => [
                        'CourseRegistrations.id',
                        'CourseRegistrations.student_id',
                        'CourseRegistrations.section_id',
                        'CourseRegistrations.academic_year',
                        'CourseRegistrations.semester',
                        'CourseRegistrations.published_course_id'
                    ],
                    'PublishedCourses' => [
                        'fields' => [
                            'PublishedCourses.id',
                            'PublishedCourses.section_id',
                            'PublishedCourses.drop',
                            'PublishedCourses.academic_year',
                            'PublishedCourses.semester'
                        ],
                        'Courses' => [
                            'fields' => [
                                'Courses.course_title',
                                'Courses.credit',
                                'Courses.curriculum_id',
                                'Courses.course_code'
                            ]
                        ]
                    ]
                ],
                'CourseAdds' => [
                    'fields' => [
                        'CourseAdds.id',
                        'CourseAdds.student_id',
                        'CourseAdds.academic_year',
                        'CourseAdds.semester',
                        'CourseAdds.published_course_id',
                        'CourseAdds.registrar_confirmation'
                    ],
                    'PublishedCourses' => [
                        'fields' => [
                            'PublishedCourses.id',
                            'PublishedCourses.section_id',
                            'PublishedCourses.drop',
                            'PublishedCourses.academic_year',
                            'PublishedCourses.semester'
                        ],
                        'Courses' => [
                            'fields' => [
                                'Courses.credit',
                                'Courses.course_title',
                                'Courses.curriculum_id',
                                'Courses.course_code'
                            ]
                        ]
                    ]
                ]
            ])
            ->where($conditions)
            ->order([
                'Students.first_name' => 'ASC',
                'Students.middle_name' => 'ASC',
                'Students.last_name' => 'ASC'
            ]);

        $students = $query->all()->toArray();



        $filteredStudents = [];
        $incompleteGrade = false;
        $invalidGrade = false;

        if (!empty($students)) {
            foreach ($students as $student) {
                // Check Course Registrations
                if (!empty($student->course_registrations)) {
                    foreach ($student->course_registrations as $courseRegistration) {

                        if (!empty($sectionId) && $academicYear != $courseRegistration->academic_year) {
                            continue;
                        }

                        $semester = $courseRegistration->semester;
                        if (!$courseRegistrationsTable->isCourseDropped($courseRegistration->id)
                            && !$courseRegistration->published_course->drop) {

                            $gradeDetail = $courseRegistrationsTable->getCourseRegistrationLatestApprovedGradeDetail(
                                $courseRegistration->id);
                            $courseRepeated = $examGradesTable->getCourseRepetation($courseRegistration->id, $courseRegistration->student_id, 1);

                            if (!empty($courseRepeated['repeated_old'])) {
                                continue;
                            }

                            if (!empty($gradeDetail) && !empty($gradeDetail->exam_grade->grade)) {
                                $latestApprovedGrade = $examGradesTable->getApprovedGrade($courseRegistration->id, 1);

                                // Fix invalid grades (NG, I, F, Fx, Fail) if there are valid grade changes
                                if (in_array(strtoupper($gradeDetail->exam_grade->grade), ['NG', 'I', 'F', 'FX', 'FAIL'], true)) {
                                    if (!empty($latestApprovedGrade['grade_change_id']) && $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                        $gradeDetail->exam_grade->grade = $latestApprovedGrade['grade'];
                                    }
                                } elseif (!empty($latestApprovedGrade['grade_change_id']) && $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                    $gradeDetail->exam_grade->grade = $latestApprovedGrade['grade'];
                                }
                            }


                            if (empty($gradeDetail)) {
                                $checkForDuplicateGradeEntry = $examGradesTable->find()
                                    ->where([
                                        'ExamGrades.course_registration_id' => $courseRegistration->id,
                                        'ExamGrades.registrar_approval' => 1
                                    ])
                                    ->count();
                                if (!$checkForDuplicateGradeEntry) {
                                    $semesterLabel = $this->getSemesterLabel($courseRegistration->published_course->semester);
                                    $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                        'Incomplete grade: %s (%s) in %s, %s semester. (Course Registration)',
                                        trim($courseRegistration->published_course->course->course_title),
                                        trim($courseRegistration->published_course->course->course_code),
                                        $courseRegistration->published_course->academic_year,
                                        $semesterLabel
                                    );
                                }
                                $incompleteGrade = true;
                            } elseif ($checkForInvalidGrades && !empty($gradeDetail->exam_grade->grade) && (
                                    in_array(strtoupper($gradeDetail->exam_grade->grade), ['NG', 'DO', 'I', 'W', 'F', 'FX', 'FAIL'], true) ||
                                    ($courseRegistration->id == $gradeDetail->exam_grade->course_registration_id &&
                                        isset($latestApprovedGrade['point_value']) &&
                                        $latestApprovedGrade['point_value'] >= 0 &&
                                        empty($latestApprovedGrade['pass_grade']) &&
                                        !empty($latestApprovedGrade['grade_scale_id']) &&
                                        $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id'])
                                )) {
                                if ($skipFGrade && (
                                        in_array(strtoupper($gradeDetail->exam_grade->grade), ['F', 'FX', 'FAIL'], true) ||
                                        ($courseRegistration->id == $gradeDetail->exam_grade->course_registration_id &&
                                            isset($latestApprovedGrade['point_value']) &&
                                            $latestApprovedGrade['point_value'] >= 0 &&
                                            empty($latestApprovedGrade['pass_grade']) &&
                                            !empty($latestApprovedGrade['grade_scale_id']) &&
                                            $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id'])
                                    )) {
                                    continue;
                                }

                                if ($skipIGrade && strtoupper($gradeDetail->exam_grade->grade) === 'I' &&
                                    $courseRegistration->id == $gradeDetail->exam_grade->course_registration_id &&
                                    $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                    continue;
                                }

                                $semesterLabel = $this->getSemesterLabel($courseRegistration->published_course->semester);
                                if ($courseRegistration->id == $gradeDetail->exam_grade->course_registration_id &&
                                    isset($latestApprovedGrade['pass_grade']) &&
                                    empty($latestApprovedGrade['pass_grade']) &&
                                    empty($latestApprovedGrade['grade_scale_id']) &&
                                    $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                    $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                        'Invalid Grade (%s): %s (%s) in %s, %s semester. (Course Registration)',
                                        $gradeDetail->exam_grade->grade,
                                        trim($courseRegistration->published_course->course->course_title),
                                        trim($courseRegistration->published_course->course->course_code),
                                        $courseRegistration->published_course->academic_year,
                                        $semesterLabel
                                    );
                                } elseif ($courseRegistration->id == $gradeDetail->exam_grade->course_registration_id &&
                                    isset($latestApprovedGrade['point_value']) &&
                                    $latestApprovedGrade['point_value'] >= 0 &&
                                    empty($latestApprovedGrade['pass_grade']) &&
                                    !empty($latestApprovedGrade['grade_scale_id']) &&
                                    $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                    $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                        'Failed Grade (%s): %s (%s) in %s, %s semester. (Course Registration)',
                                        $gradeDetail->exam_grade->grade,
                                        trim($courseRegistration->published_course->course->course_title),
                                        trim($courseRegistration->published_course->course->course_code),
                                        $courseRegistration->published_course->academic_year,
                                        $semesterLabel
                                    );
                                } elseif (in_array(strtoupper($gradeDetail->exam_grade->grade),
                                    ['NG', 'DO', 'I', 'W', 'F', 'FX', 'FAIL'], true)) {
                                    $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                        'Invalid Grade (%s): %s (%s) in %s, %s semester. (Course Registration)',
                                        $gradeDetail->exam_grade->grade,
                                        trim($courseRegistration->published_course->course->course_title),
                                        trim($courseRegistration->published_course->course->course_code),
                                        $courseRegistration->published_course->academic_year,
                                        $semesterLabel
                                    );
                                }
                                $invalidGrade = true;
                            }
                        }
                    }
                }

                // Check Course Adds
                if (!empty($student->course_adds)) {
                    foreach ($student->course_adds as $courseAdd) {
                        if (!$courseAdd->registrar_confirmation || empty($courseAdd->id)) {
                            continue;
                        }

                        if (!empty($sectionId) && $academicYear != $courseAdd->academic_year) {
                            continue;
                        }

                        $gradeDetail = $courseAddsTable->getCourseAddLatestApprovedGradeDetail($courseAdd->id);
                        $courseRepeated = $examGradesTable->getCourseRepetation($courseAdd->id, $courseAdd->student_id, 0);

                        if (!empty($courseRepeated['repeated_old'])) {
                            continue;
                        }

                        if (!empty($gradeDetail) && !empty($gradeDetail->exam_grade->grade)) {
                            $latestApprovedGrade = $examGradesTable->getApprovedGrade($courseAdd->id, 0);

                            // Fix invalid grades (NG, I, F, Fx, Fail) if there are valid grade changes
                            if (in_array(strtoupper($gradeDetail->exam_grade->grade), ['NG', 'I', 'F', 'FX', 'FAIL'], true)) {
                                if (!empty($latestApprovedGrade['grade_change_id']) && $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                    $gradeDetail->exam_grade->grade = $latestApprovedGrade['grade'];
                                }
                            } elseif (!empty($latestApprovedGrade['grade_change_id']) && $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                $gradeDetail->exam_grade->grade = $latestApprovedGrade['grade'];
                            }
                        }

                        if (empty($gradeDetail)) {
                            $checkForDuplicateGradeEntry = $examGradesTable->find()
                                ->where([
                                    'ExamGrades.course_add_id' => $courseAdd->id,
                                    'ExamGrades.registrar_approval' => 1
                                ])
                                ->count();
                            if (!$checkForDuplicateGradeEntry && !empty($courseAdd->published_course->id)) {
                                $semesterLabel = $this->getSemesterLabel($courseAdd->published_course->semester);
                                $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                    'Incomplete grade: %s (%s) in %s, %s semester. (Course Add)',
                                    trim($courseAdd->published_course->course->course_title),
                                    trim($courseAdd->published_course->course->course_code),
                                    $courseAdd->published_course->academic_year,
                                    $semesterLabel
                                );
                            }
                            $incompleteGrade = true;
                        } elseif ($checkForInvalidGrades && !empty($gradeDetail->exam_grade->grade) && (
                                in_array(strtoupper($gradeDetail->exam_grade->grade), ['NG', 'DO', 'I', 'W', 'F', 'FX', 'FAIL'], true) ||
                                ($courseAdd->id == $gradeDetail->exam_grade->course_add_id &&
                                    isset($latestApprovedGrade['point_value']) &&
                                    $latestApprovedGrade['point_value'] >= 0 &&
                                    empty($latestApprovedGrade['pass_grade']) &&
                                    !empty($latestApprovedGrade['grade_scale_id']) &&
                                    $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id'])
                            )) {
                            if ($skipFGrade && (
                                    in_array(strtoupper($gradeDetail->exam_grade->grade), ['F', 'FX', 'FAIL'], true) ||
                                    ($courseAdd->id == $gradeDetail->exam_grade->course_add_id &&
                                        isset($latestApprovedGrade['point_value']) &&
                                        $latestApprovedGrade['point_value'] >= 0 &&
                                        empty($latestApprovedGrade['pass_grade']) &&
                                        !empty($latestApprovedGrade['grade_scale_id']) &&
                                        $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id'])
                                )) {
                                continue;
                            }

                            if ($skipIGrade && strtoupper($gradeDetail->exam_grade->grade) === 'I' &&
                                $courseAdd->id == $gradeDetail->exam_grade->course_add_id &&
                                $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                continue;
                            }

                            $semesterLabel = $this->getSemesterLabel($courseAdd->published_course->semester);
                            if ($courseAdd->id == $gradeDetail->exam_grade->course_add_id &&
                                isset($latestApprovedGrade['pass_grade']) &&
                                empty($latestApprovedGrade['pass_grade']) &&
                                empty($latestApprovedGrade['grade_scale_id']) &&
                                $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                    'Invalid Grade (%s): %s (%s) in %s, %s semester. (Course Add)',
                                    $gradeDetail->exam_grade->grade,
                                    trim($courseAdd->published_course->course->course_title),
                                    trim($courseAdd->published_course->course->course_code),
                                    $courseAdd->published_course->academic_year,
                                    $semesterLabel
                                );
                            } elseif ($courseAdd->id == $gradeDetail->exam_grade->course_add_id &&
                                isset($latestApprovedGrade['point_value']) &&
                                $latestApprovedGrade['point_value'] >= 0 &&
                                empty($latestApprovedGrade['pass_grade']) &&
                                !empty($latestApprovedGrade['grade_scale_id']) &&
                                $gradeDetail->exam_grade->id == $latestApprovedGrade['grade_id']) {
                                $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                    'Failed Grade (%s): %s (%s) in %s, %s semester. (Course Add)',
                                    $gradeDetail->exam_grade->grade,
                                    trim($courseAdd->published_course->course->course_title),
                                    trim($courseAdd->published_course->course->course_code),
                                    $courseAdd->published_course->academic_year,
                                    $semesterLabel
                                );
                            } elseif (in_array(strtoupper($gradeDetail->exam_grade->grade), ['NG', 'DO', 'I', 'W', 'F', 'FX', 'FAIL'], true)) {
                                $filteredStudents[$student->id]['disqualification'][] = sprintf(
                                    'Invalid Grade (%s): %s (%s) in %s, %s semester. (Course Add)',
                                    $gradeDetail->exam_grade->grade,
                                    trim($courseAdd->published_course->course->course_title),
                                    trim($courseAdd->published_course->course->course_code),
                                    $courseAdd->published_course->academic_year,
                                    $semesterLabel
                                );
                            }
                            $invalidGrade = true;
                        }
                    }
                }
            }
        }

        if (empty($filteredStudents)) {
            return 1;
        }

        if ($getErrorMessage) {
            return $filteredStudents;
        }

        return 0;
    }

    /**
     * Helper method to format semester labels
     */
    private function getSemesterLabel($semester)
    {
        switch ($semester) {
            case 'I':
                return '1st';
            case 'II':
                return '2nd';
            case 'III':
                return '3rd';
            default:
                return $semester;
        }
    }

    /**
     * Formats semester for display.
     *
     * @param string $semester The semester (e.g., 'I', 'II').
     * @return string
     */
    /**
     * Formats semester for display.
     *
     * @param string $semester The semester (e.g., 'I', 'II').
     * @return string
     */
    protected function formatSemester(string $semester): string
    {
        $semesterMap = [
            'I' => '1st',
            'II' => '2nd',
            'III' => '3rd'
        ];

        return $semesterMap[$semester] ?? $semester;
    }

    /**
     * Generates error message for invalid grades.
     *
     * @param object $gradeDetail The grade detail object.
     * @param array $latestApprovedGrade The latest approved grade data.
     * @param object $publishedCourse The published course object.
     * @param string $type The type of course (Registration or Add).
     * @return string
     */
    protected function getGradeErrorMessage($gradeDetail, array $latestApprovedGrade, $publishedCourse, string $type): string
    {
        $courseTitle = trim($publishedCourse->course->course_title);
        $courseCode = trim($publishedCourse->course->course_code);
        $academicYear = $publishedCourse->academic_year;
        $semester = $this->formatSemester($publishedCourse->semester);

        if ($gradeDetail->course_registration_id == $gradeDetail->course_registration_id &&
            !$latestApprovedGrade['pass_grade'] && empty($latestApprovedGrade['grade_scale_id']) &&
            $gradeDetail->id == $latestApprovedGrade['grade_id']) {
            return __('Invalid Grade (%s): %s (%s) in %s, %s semester. (%s)',
                $gradeDetail->grade, $courseTitle, $courseCode, $academicYear, $semester, $type);
        } elseif ($gradeDetail->course_registration_id == $gradeDetail->course_registration_id &&
            $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] &&
            $latestApprovedGrade['grade_scale_id'] && $gradeDetail->id == $latestApprovedGrade['grade_id']) {
            return __('Failed Grade (%s): %s (%s) in %s, %s semester. (%s)',
                $gradeDetail->grade, $courseTitle, $courseCode, $academicYear, $semester, $type);
        }

        return __('Invalid Grade (%s): %s (%s) in %s, %s semester. (%s)',
            $gradeDetail->grade, $courseTitle, $courseCode, $academicYear, $semester, $type);
    }

    /**
     * Retrieves detailed section name.
     *
     * @param int|null $id The section ID.
     * @param bool $all Include all details.
     * @param bool $includeCurriculumName Include curriculum name.
     * @return string|int
     */
    public function getSectionDetailedName(?int $id, bool $all = false, bool $includeCurriculumName = false)
    {
        if (!$id || !is_numeric($id) || $id <= 0) {
            return 0;
        }

        if ($all) {
            $section = $this->find()
                ->select(['id', 'name', 'academic_year'])
                ->where(['id' => $id])
                ->contain([
                    'YearLevels' => ['fields' => ['name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Colleges' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced']]
                ])
                ->first();

            if (!$section) {
                return 0;
            }

            $yearLevelName = $section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');
            $curriculumName = $section->curriculum ?
                "{$section->curriculum->name} - {$section->curriculum->year_introduced}" :
                'Not attached';

            $name = implode('~', [
                $section->college->name,
                $section->department->name,
                $section->program->name,
                $section->program_type->name,
                $yearLevelName,
                trim(str_replace('  ', ' ', $section->name)) . " ({$yearLevelName}, {$section->academic_year})"
            ]);

            if ($includeCurriculumName) {
                $name .= "~{$curriculumName}";
            }

            return $name;
        }

        $section = $this->find()
            ->select(['id', 'name', 'academic_year', 'program_id'])
            ->where(['id' => $id])
            ->contain(['YearLevels' => ['fields' => ['name']]])
            ->first();

        if (!$section) {
            return 0;
        }

        $yearLevelName = $section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');

        return trim(str_replace('  ', ' ', $section->name)) . " ({$yearLevelName}, {$section->academic_year})";
    }

}
