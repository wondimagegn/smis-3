<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;

class CourseAddsTable extends Table
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

        $this->setTable('course_adds');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('ExamGrades', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('ExamResults', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('FxResitRequests', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('HistoricalStudentCourseGradeExcludes', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('MakeupExams', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('RejectedExamGrades', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('ResultEntryAssignments', [
            'foreignKey' => 'course_add_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->boolean('department_approval')
            ->allowEmptyString('department_approval');

        $validator
            ->scalar('reason')
            ->allowEmptyString('reason');

        $validator
            ->uuid('department_approved_by')
            ->allowEmptyString('department_approved_by');

        $validator
            ->boolean('registrar_confirmation')
            ->allowEmptyString('registrar_confirmation');

        $validator
            ->uuid('registrar_confirmed_by')
            ->allowEmptyString('registrar_confirmed_by');

        $validator
            ->boolean('auto_rejected')
            ->notEmptyString('auto_rejected');

        $validator
            ->boolean('cron_job')
            ->notEmptyString('cron_job');

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
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));

        return $rules;
    }

    /**
     * Retrieves the grade history for a given course add.
     *
     * @param int|null $courseAddId The ID of the course add.
     * @return array The grade history including add and change records.
     */
    public function getCourseAddGradeHistory(?int $courseAddId = null): array
    {
        $gradeHistory = [];
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        if ($courseAddId !== null) {
            $gradeHistoryRow = $this->ExamGrades->find()
                ->where(['ExamGrades.course_add_id' => $courseAddId])
                ->contain([
                    'ExamGradeChanges' => ['sort' => ['ExamGradeChanges.id' => 'ASC']]
                ])
                ->order(['ExamGrades.id' => 'DESC'])
                ->toArray();

            $count = 0;
            $gradeHistory[$count]['type'] = 'Add';

            if (count($gradeHistoryRow) > 1) {
                $skipFirst = false;
                foreach ($gradeHistoryRow as $rejectedGrade) {
                    if (!$skipFirst) {
                        $skipFirst = true;
                        continue;
                    }

                    $rejectedGrade->department_approved_by_name = $usersTable->find()
                        ->where(['Users.id' => $rejectedGrade->department_approved_by])
                        ->select(['full_name'])
                        ->first()
                        ->full_name ?? '';
                    $rejectedGrade->registrar_approved_by_name = $usersTable->find()
                        ->where(['Users.id' => $rejectedGrade->registrar_approved_by])
                        ->select(['full_name'])
                        ->first()
                        ->full_name ?? '';

                    $gradeHistory[$count]['rejected'][] = $rejectedGrade->toArray();
                }
            } else {
                $gradeHistory[$count]['rejected'] = [];
            }

            $gradeHistory[$count]['ExamGrade'] = !empty($gradeHistoryRow[0]) ? $gradeHistoryRow[0]->toArray() : [];

            if (!empty($gradeHistoryRow[0]->exam_grade_changes)) {
                foreach ($gradeHistoryRow[0]->exam_grade_changes as $examGradeChange) {
                    $count++;
                    $gradeHistory[$count]['type'] = 'Change';

                    $examGradeChange->department_approved_by_name = $usersTable->find()
                        ->where(['Users.id' => $examGradeChange->department_approved_by])
                        ->select(['full_name'])
                        ->first()
                        ->full_name ?? '';
                    $examGradeChange->college_approved_by_name = $usersTable->find()
                        ->where(['Users.id' => $examGradeChange->college_approved_by])
                        ->select(['full_name'])
                        ->first()
                        ->full_name ?? '';
                    $examGradeChange->registrar_approved_by_name = $usersTable->find()
                        ->where(['Users.id' => $examGradeChange->registrar_approved_by])
                        ->select(['full_name'])
                        ->first()
                        ->full_name ?? '';

                    $examGradeChange->grade_scale_id = $this->ExamGrades->find()
                        ->where(['ExamGrades.id' => $examGradeChange->exam_grade_id])
                        ->select(['grade_scale_id'])
                        ->first()
                        ->grade_scale_id ?? null;

                    if (!empty($examGradeChange->manual_ng_converted_by)) {
                        $examGradeChange->manual_ng_converted_by_name = $usersTable->find()
                            ->where(['Users.id' => $examGradeChange->manual_ng_converted_by])
                            ->select(['full_name'])
                            ->first()
                            ->full_name ?? '';
                    }

                    $gradeHistory[$count]['ExamGrade'] = $examGradeChange->toArray();
                }
            }
        }

        return $gradeHistory;
    }

    /**
     * Determines the status of an exam grade change.
     *
     * @param array|null $examGradeChange The exam grade change data.
     * @param string $type The type of status check ('simple' by default).
     * @return string The status ('accepted', 'rejected', or 'on-process').
     */
    public function getExamGradeChangeStatus(?array $examGradeChange = null, string $type = 'simple'): string
    {
        if (empty($examGradeChange)) {
            return 'on-process';
        }

        if ($examGradeChange['manual_ng_conversion'] == 1 || $examGradeChange['auto_ng_conversion'] == 1) {
            return 'accepted';
        }

        if ($examGradeChange['initiated_by_department'] == 1 || $examGradeChange['department_approval'] == 1) {
            if ($examGradeChange['college_approval'] == 1 || !is_null($examGradeChange['makeup_exam_result'])) {
                if ($examGradeChange['registrar_approval'] == 1) {
                    return 'accepted';
                } elseif ($examGradeChange['registrar_approval'] == -1) {
                    return 'rejected';
                } elseif (is_null($examGradeChange['registrar_approval'])) {
                    return 'on-process';
                }
            } elseif ($examGradeChange['college_approval'] == -1) {
                return 'rejected';
            } elseif (is_null($examGradeChange['college_approval'])) {
                return 'on-process';
            }
        } elseif ($examGradeChange['department_approval'] == -1) {
            return 'rejected';
        } elseif (is_null($examGradeChange['department_approval'])) {
            return 'on-process';
        }

        return 'on-process';
    }

    /**
     * Determines the status of an exam grade.
     *
     * @param array|null $examGrade The exam grade data.
     * @param string $type The type of status check ('simple' by default).
     * @return string The status ('accepted', 'rejected', or 'on-process').
     */
    public function getExamGradeStatus(?array $examGrade = null, string $type = 'simple'): string
    {
        if (empty($examGrade)) {
            return 'on-process';
        }

        if ($examGrade['department_approval'] == 1) {
            if ($examGrade['registrar_approval'] == 1) {
                return 'accepted';
            } elseif ($examGrade['registrar_approval'] == -1) {
                return 'rejected';
            } elseif (is_null($examGrade['registrar_approval'])) {
                return 'on-process';
            }
        } elseif ($examGrade['department_approval'] == -1) {
            return 'rejected';
        } elseif (is_null($examGrade['department_approval'])) {
            return 'on-process';
        }

        return 'on-process';
    }

    /**
     * Checks if any grade for a course add is still in process.
     *
     * @param int|null $courseAddId The ID of the course add.
     * @return bool True if any grade is on-process, false otherwise.
     */
    public function isAnyGradeOnProcess(?int $courseAddId = null): bool
    {
        $gradeHistories = $this->getCourseAddGradeHistory($courseAddId);

        foreach ($gradeHistories as $gradeHistory) {
            if (
                (strcasecmp($gradeHistory['type'], 'Add') === 0 && strcasecmp($this->getExamGradeStatus($gradeHistory['ExamGrade']), 'on-process') === 0) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 && strcasecmp($this->getExamGradeChangeStatus($gradeHistory['ExamGrade']), 'on-process') === 0)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the latest grade for a course add, ignoring course add approval but considering grade change approvals.
     *
     * @param int|null $courseAddId The ID of the course add.
     * @return string The latest grade or empty string if none found.
     */
    public function getCourseRegistrationLatestGrade(?int $courseAddId = null): string
    {
        $gradeHistories = $this->getCourseAddGradeHistory($courseAddId);
        $latestGrade = '';

        foreach ($gradeHistories as $gradeHistory) {
            if (
                !empty($gradeHistory['ExamGrade']['grade']) &&
                $gradeHistory['ExamGrade']['grade'] !== $latestGrade &&
                (
                    $gradeHistory['type'] !== 'Change' ||
                    (
                        ($gradeHistory['ExamGrade']['department_approval'] == 1 || $gradeHistory['ExamGrade']['initiated_by_department'] == 1) &&
                        $gradeHistory['ExamGrade']['registrar_approval'] == 1 &&
                        $gradeHistory['ExamGrade']['college_approval'] == 1
                    ) ||
                    (
                        !is_null($gradeHistory['ExamGrade']['makeup_exam_result']) &&
                        ($gradeHistory['ExamGrade']['department_approval'] == 1 || $gradeHistory['ExamGrade']['initiated_by_department'] == 1) &&
                        $gradeHistory['ExamGrade']['registrar_approval'] == 1
                    )
                )
            ) {
                $latestGrade = $gradeHistory['ExamGrade']['grade'];
            }
        }

        return $latestGrade;
    }

    /**
     * Retrieves the latest grade detail for a course add, regardless of approval state, unless fully rejected.
     *
     * @param int|null $courseAddId The ID of the course add.
     * @return array The latest grade detail or empty array if none found.
     */
    public function getCourseAddLatestGradeDetail(?int $courseAddId = null): array
    {
        $gradeHistories = $this->getCourseAddGradeHistory($courseAddId);
        $latestGradeDetail = [];

        foreach ($gradeHistories as $gradeHistory) {
            if (
                strcasecmp($gradeHistory['type'], 'Add') === 0 ||
                (
                    strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    (
                        (
                            is_null($gradeHistory['ExamGrade']['makeup_exam_result']) &&
                            $gradeHistory['ExamGrade']['department_approval'] != -1 &&
                            $gradeHistory['ExamGrade']['college_approval'] != -1 &&
                            $gradeHistory['ExamGrade']['registrar_approval'] != -1
                        ) ||
                        !is_null($gradeHistory['ExamGrade']['makeup_exam_result']) ||
                        ($gradeHistory['ExamGrade']['auto_ng_conversion'] == 1 || $gradeHistory['ExamGrade']['manual_ng_conversion'] == 1)
                    )
                )
            ) {
                $latestGradeDetail = $gradeHistory;
                unset($latestGradeDetail['rejected']);
            }
        }

        return $latestGradeDetail;
    }

    /**
     * Retrieves the latest approved grade detail for a course add, considering department and college approvals.
     *
     * @param int|null $courseAddId The ID of the course add.
     * @return array The latest approved grade detail or empty array if none found.
     */
    public function getCourseAddLatestApprovedGradeDetail(?int $courseAddId = null): array
    {
        $gradeHistories = $this->getCourseAddGradeHistory($courseAddId);
        $latestGradeDetail = [];

        foreach ($gradeHistories as $gradeHistory) {
            if (
                (
                    (
                        strcasecmp($gradeHistory['type'], 'Add') === 0 &&
                        !empty($gradeHistory['ExamGrade']) &&
                        $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                        $gradeHistory['ExamGrade']['registrar_approval'] == 1
                    ) ||
                    (
                        strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                        is_null($gradeHistory['ExamGrade']['makeup_exam_result']) &&
                        $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                        $gradeHistory['ExamGrade']['college_approval'] == 1 &&
                        $gradeHistory['ExamGrade']['registrar_approval'] == 1
                    ) ||
                    (
                        strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                        !is_null($gradeHistory['ExamGrade']['makeup_exam_result']) &&
                        $gradeHistory['ExamGrade']['initiated_by_department'] == 0 &&
                        $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                        $gradeHistory['ExamGrade']['registrar_approval'] == 1
                    ) ||
                    (
                        strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                        !is_null($gradeHistory['ExamGrade']['makeup_exam_result']) &&
                        $gradeHistory['ExamGrade']['initiated_by_department'] == 1 &&
                        $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                        $gradeHistory['ExamGrade']['registrar_approval'] == 1
                    ) ||
                    (!empty($gradeHistory['ExamGrade']['auto_ng_conversion']) && $gradeHistory['ExamGrade']['auto_ng_conversion'])
                ) &&
                (
                    empty($latestGradeDetail) ||
                    $gradeHistory['ExamGrade']['created'] > $latestGradeDetail['ExamGrade']['created']
                )
            ) {
                $latestGradeDetail = $gradeHistory;
                unset($latestGradeDetail['rejected']);
            }
        }

        return $latestGradeDetail;
    }

    /**
     * Retrieves course adds for a student, optionally filtered by academic year/semester and course ID.
     *
     * @param int|null $studentId The student ID.
     * @param array $ayAndSList Array of academic year and semester pairs.
     * @param int|null $courseId The course ID.
     * @param int $includeEquivalent Whether to include equivalent courses (1 = yes, 0 = no).
     * @return array List of course adds.
     */
    public function getCourseAdds(?int $studentId = null, array $ayAndSList = [], ?int $courseId = null, int $includeEquivalent = 1): array
    {
        $courseAdds = [];
        $equivalentCoursesTable = TableRegistry::getTableLocator()->get('EquivalentCourses');

        if (!empty($studentId)) {
            $options = [
                'conditions' => ['CourseAdds.student_id' => $studentId],
                'contain' => ['PublishedCourses.Courses'],
                'order' => ['CourseAdds.created' => 'DESC']
            ];

            if (!empty($ayAndSList)) {
                foreach ($ayAndSList as $ayAndS) {
                    $options['conditions']['OR'][] = [
                        'CourseAdds.academic_year' => $ayAndS['academic_year'],
                        'CourseAdds.semester' => $ayAndS['semester']
                    ];
                }
            }

            $matchingCourses = [$courseId];

            if ($includeEquivalent == 1 && $courseId !== null) {
                $studentDepartment = $this->Students->find()
                    ->where(['Students.id' => $studentId])
                    ->select(['department_id', 'curriculum_id'])
                    ->first();

                $courseDepartment = $this->PublishedCourses->Courses->find()
                    ->where(['Courses.id' => $courseId])
                    ->contain(['Curriculums'])
                    ->first();

                debug($courseId);

                if (!empty($studentDepartment->department_id)) {
                    if (
                        $studentDepartment->department_id == $courseDepartment->curriculum->department_id &&
                        $studentDepartment->curriculum_id == $courseDepartment->curriculum_id
                    ) {
                        $matchingCourses = [$courseId];
                    } else {
                        $matchingCourses = $equivalentCoursesTable->validEquivalentCourse($courseId, $studentDepartment->curriculum_id);
                    }
                }
            }

            $courseAddsRaw = $this->find('all', $options)->toArray();

            debug($courseAddsRaw);
            debug($matchingCourses);

            foreach ($courseAddsRaw as $value) {
                if (
                    ($value->published_course->add == 1 || ($value->department_approval == 1 && $value->registrar_confirmation == 1)) &&
                    in_array($value->published_course->course->id, $matchingCourses)
                ) {
                    $courseAdds[] = $value->toArray();
                }
            }
        }

        return $courseAdds;
    }

    /**
     * Retrieves course add requests waiting for approval.
     *
     * @param array|null $departmentIds List of department IDs.
     * @param int $registrarDepartment Registrar or department role (1 = registrar, 2 = department).
     * @param array|null $collegeIds List of college IDs.
     * @param int $registrarCollegePrivilege Registrar college privilege (1 = approval, 2 = registrar).
     * @param array|null $programId List of program IDs.
     * @param array|null $programTypeId List of program type IDs.
     * @param array|null $acyRanges List of academic year ranges.
     * @return array List of course add requests.
     */
    public function courseAddRequestWaitingApproval(
        ?array $departmentIds = null,
        int $registrarDepartment = 1,
        ?array $collegeIds = null,
        int $registrarCollegePrivilege = 1,
        ?array $programId = null,
        ?array $programTypeId = null,
        ?array $acyRanges = null
    ): array {
        $options = [];

        if ($registrarDepartment == 1) {
            $options['contain'] = [
                'PublishedCourses' => [
                    'Courses' => [
                        'Prerequisites',
                        'fields' => ['id', 'credit', 'course_detail_hours', 'course_title', 'course_code']
                    ],
                    'Sections' => [
                        'Departments' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                        'fields' => ['id', 'name']
                    ],
                    'fields' => ['id', 'semester', 'academic_year']
                ],
                'Students' => [
                    'Departments' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'StudentsSections' => ['conditions' => ['StudentsSections.archive' => 0]],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                    'fields' => ['id', 'full_name', 'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated', 'academicyear'],
                    'sort' => ['Students.academicyear' => 'DESC', 'Students.studentnumber' => 'ASC', 'Students.id' => 'ASC', 'Students.full_name' => 'ASC']
                ]
            ];

            $options['conditions'] = [
                'Students.department_id IN' => (array)$departmentIds,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation IS' => null,
                'Students.graduated' => 0,
                'PublishedCourses.section_id IS NOT' => null,
                'PublishedCourses.course_id IS NOT' => null
            ];

            if (!empty($programId)) {
                $options['conditions']['Students.program_id IN'] = (array)$programId;
            }

            if (!empty($programTypeId)) {
                $options['conditions']['Students.program_type_id IN'] = (array)$programTypeId;
            }
        } elseif ($registrarDepartment == 2) {
            $options['contain'] = [
                'PublishedCourses' => [
                    'Courses' => [
                        'Prerequisites',
                        'fields' => ['id', 'credit', 'course_detail_hours', 'course_title', 'course_code']
                    ],
                    'Sections' => [
                        'Departments' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                        'fields' => ['id', 'name']
                    ],
                    'fields' => ['id', 'semester', 'academic_year']
                ],
                'Students' => [
                    'Departments' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'StudentsSections' => ['conditions' => ['StudentsSections.archive' => 0]],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                    'fields' => ['id', 'full_name', 'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated', 'academicyear'],
                    'sort' => ['Students.academicyear' => 'DESC', 'Students.studentnumber' => 'ASC', 'Students.id' => 'ASC', 'Students.full_name' => 'ASC']
                ]
            ];

            $options['conditions'] = [
                'Students.department_id IN' => (array)$departmentIds,
                'CourseAdds.department_approval IS' => null,
                'CourseAdds.department_approved_by IS' => null,
                'Students.graduated' => 0,
                'PublishedCourses.section_id IS NOT' => null,
                'PublishedCourses.course_id IS NOT' => null
            ];
        }

        if (!empty($collegeIds) && $registrarCollegePrivilege == 1) {
            $options['contain'] = [
                'PublishedCourses' => [
                    'Courses' => [
                        'Prerequisites',
                        'fields' => ['id', 'credit', 'course_detail_hours', 'course_title', 'course_code']
                    ],
                    'Sections' => [
                        'Departments' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                        'fields' => ['id', 'name']
                    ],
                    'fields' => ['id', 'semester', 'academic_year']
                ],
                'Students' => [
                    'Departments' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'StudentsSections' => ['conditions' => ['StudentsSections.archive' => 0]],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                    'fields' => ['id', 'full_name', 'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated', 'academicyear'],
                    'sort' => ['Students.academicyear' => 'DESC', 'Students.studentnumber' => 'ASC', 'Students.id' => 'ASC', 'Students.full_name' => 'ASC']
                ]
            ];

            $options['conditions'] = [
                'Students.department_id IS' => null,
                'Students.college_id IN' => (array)$collegeIds,
                'CourseAdds.department_approval IS' => null,
                'Students.graduated' => 0,
                'PublishedCourses.section_id IS NOT' => null,
                'PublishedCourses.course_id IS NOT' => null
            ];
        }

        if (!empty($collegeIds) && $registrarCollegePrivilege == 2) {
            $options['contain'] = [
                'PublishedCourses' => [
                    'Courses' => [
                        'Prerequisites',
                        'fields' => ['id', 'credit', 'course_detail_hours', 'course_title', 'course_code']
                    ],
                    'Sections' => [
                        'Departments' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                        'fields' => ['id', 'name']
                    ],
                    'fields' => ['id', 'semester', 'academic_year']
                ],
                'Students' => [
                    'Departments' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'StudentsSections' => ['conditions' => ['StudentsSections.archive' => 0]],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                    'fields' => ['id', 'full_name', 'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated', 'academicyear'],
                    'sort' => ['Students.academicyear' => 'DESC', 'Students.studentnumber' => 'ASC', 'Students.id' => 'ASC', 'Students.full_name' => 'ASC']
                ]
            ];

            $options['conditions'] = [
                'Students.department_id IS' => null,
                'Students.college_id IN' => (array)$collegeIds,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation IS' => null,
                'CourseAdds.department_approved_by IS NOT' => null,
                'OR' => [
                    'CourseAdds.year_level_id IS' => null,
                    'CourseAdds.year_level_id' => '',
                    'CourseAdds.year_level_id' => 0
                ],
                'Students.graduated' => 0,
                'PublishedCourses.section_id IS NOT' => null,
                'PublishedCourses.course_id IS NOT' => null
            ];
        }

        $courseAdds = [];

        if (!empty($options['conditions'])) {
            if (!empty($acyRanges)) {
                $options['conditions']['CourseAdds.academic_year IN'] = (array)$acyRanges;
            }

            debug($options['conditions']);

            $courseAdds = $this->find('all', $options)->toArray();
        }

        return $courseAdds;
    }

    /**
     * Counts course add requests waiting for approval.
     *
     * @param array|null $departmentIds List of department IDs.
     * @param int $registrar Role indicator (1 = registrar, 2 = department, 3 = college registrar).
     * @param array|null $collegeIds List of college IDs.
     * @param array|null $programId List of program IDs.
     * @param array|null $programTypeId List of program type IDs.
     * @param array|null $acyRanges List of academic year ranges.
     * @return int The count of course add requests.
     */
    public function countAddRequest(
        ?array $departmentIds = null,
        int $registrar = 1,
        ?array $collegeIds = null,
        ?array $programId = null,
        ?array $programTypeId = null,
        ?array $acyRanges = null
    ): int {
        $query = $this->find()
            ->contain([
                'Students' => function ($q) {
                    return $q->contain([
                        'StudentsSections' => function ($q2) {
                            return $q2->where(['StudentsSections.archive' => 0]);
                        }
                    ])->select([
                        'id',
                        'gender',
                        'studentnumber',
                        'program_id',
                        'program_type_id',
                        'department_id',
                        'college_id',
                        'graduated'
                    ]);
                }
            ])
            ->group(['CourseAdds.student_id']);

        if (!empty($programId)) {
            $query->where(['Students.program_id IN' => (array)$programId]);
        }

        if (!empty($programTypeId)) {
            $query->where(['Students.program_type_id IN' => (array)$programTypeId]);
        }

        if ($registrar == 1) {
            if (!empty($departmentIds)) {
                $query->where([
                    'Students.department_id IN' => (array)$departmentIds,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation IS' => null
                ]);
            } elseif (!empty($collegeIds)) {
                $query->where([
                    'Students.department_id IS' => null,
                    'Students.college_id IN' => (array)$collegeIds,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation IS' => null,
                    'Students.graduated' => 0
                ]);
            }
        } elseif ($registrar == 2) {
            $query->where([
                'Students.department_id IN' => (array)$departmentIds,
                'CourseAdds.department_approval IS' => null,
                'Students.graduated' => 0
            ]);
        } elseif ($registrar == 3 && !empty($collegeIds)) {
            $query->where([
                'Students.department_id IS' => null,
                'Students.college_id IN' => (array)$collegeIds,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation IS' => null,
                'Students.graduated' => 0
            ]);
        }

        if (!empty($acyRanges)) {
            $query->where([
                'CourseAdds.academic_year IN' => (array)$acyRanges,
                'CourseAdds.auto_rejected !=' => 1
            ]);
        }

        return $query->count();
    }

    /**
     * Reformats course add approval requests by department, program, and section.
     *
     * @param array|null $courseAdds List of course add requests.
     * @param int|null $departmentId Department ID.
     * @param string|null $currentAcademicYear Current academic year.
     * @param int|null $collegeId College ID.
     * @return array Organized course add requests.
     */
    public function reformatApprovalRequest(?array $courseAdds = null, ?int $departmentId = null, ?string $currentAcademicYear = null, ?int $collegeId = null): array
    {
        $sectionOrganizedPublishedCourse = [];
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');

        if (!empty($courseAdds)) {
            foreach ($courseAdds as &$pv) {
                $pv['Student']['max_load'] = $this->Students->calculateStudentLoad($pv['Student']['id'], $pv['CourseAdd']['semester'], $pv['CourseAdd']['academic_year']);
                $pv['Student']['maximumCreditPerSemester'] = $academicCalendarsTable->maximumCreditPerSemester($pv['Student']['id']);

                if (!empty($pv['PublishedCourse']['Course']['credit']) && ($pv['PublishedCourse']['Course']['credit'] + $pv['Student']['max_load']) > $pv['Student']['maximumCreditPerSemester']) {
                    $pv['Student']['willBeOverMaxLoadWithThisAdd'] = 1;
                    $pv['Student']['overCredit'] = ($pv['PublishedCourse']['Course']['credit'] + $pv['Student']['max_load']) - $pv['Student']['maximumCreditPerSemester'];
                } else {
                    $pv['Student']['willBeOverMaxLoadWithThisAdd'] = 0;
                    $pv['Student']['overCredit'] = 0;
                }

                $key1 = empty($pv['Student']['Department']['name']) ? 'Pre/Fresh' : $pv['Student']['Department']['name'];
                $key2 = $pv['Student']['Program']['name'];
                $key3 = $pv['Student']['ProgramType']['name'];
                $key4 = $pv['PublishedCourse']['Section']['name'];

                $sectionOrganizedPublishedCourse[$key1][$key2][$key3][$key4][] = $pv;
            }
        }

        debug($sectionOrganizedPublishedCourse);
        return $sectionOrganizedPublishedCourse;
    }

    /**
     * Checks if a course has prerequisites and if they are fulfilled for a student.
     *
     * @param array $course The course data including PublishedCourse and Course details.
     * @param int $studentId The student ID.
     * @return bool True if prerequisites are fulfilled or none exist, false otherwise.
     */
    public function courseHasPrerequistAndFullFilled(array $course, int $studentId): bool
    {
        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');

        if (!empty($course['Course']['Prerequisite'])) {
            $readyRegisteredCourseIds = [];

            $publishedCourses = $this->PublishedCourses->find()
                ->where([
                    'PublishedCourses.department_id' => $course['PublishedCourse']['department_id'],
                    'PublishedCourses.section_id' => $course['PublishedCourse']['section_id'],
                    'PublishedCourses.year_level_id' => $course['PublishedCourse']['year_level_id'],
                    'PublishedCourses.add' => 0,
                    'PublishedCourses.academic_year LIKE' => $course['PublishedCourse']['academic_year'] . '%',
                    'PublishedCourses.semester' => $course['PublishedCourse']['semester']
                ])
                ->select(['course_id'])
                ->toArray();

            foreach ($publishedCourses as $v) {
                $readyRegisteredCourseIds[] = $v->course_id;
            }

            if ($courseExemptionsTable->isCourseExempted($studentId, $course['PublishedCourse']['course_id']) > 0) {
                return true;
            }

            $passedCount = ['passed' => 0, 'onhold' => 0];

            foreach ($course['Course']['Prerequisite'] as $prevalue) {
                if ($prevalue['co_requisite'] == 1) {
                    if (in_array($prevalue['prerequisite_course_id'], $readyRegisteredCourseIds)) {
                        $passedCount['passed']++;
                    } else {
                        $prePassed = $courseDropsTable->prerequisiteTaken($studentId, $prevalue['prerequisite_course_id']);
                        if ($prePassed === true) {
                            $passedCount['passed']++;
                        } elseif ($prePassed == 2) {
                            $passedCount['onhold']++;
                        }
                    }
                } else {
                    $prePassed = $courseDropsTable->prerequisiteTaken($studentId, $prevalue['prerequisite_course_id']);
                    if ($prePassed === true || $prePassed == 2) {
                        $passedCount['passed']++;
                    }
                }
            }

            return $passedCount['passed'] == count($course['Course']['Prerequisite']);
        }

        return true;
    }

    /**
     * Deletes course adds for a given academic year and semester if no corresponding registration exists.
     *
     * @param string $academicYear The academic year.
     * @param string $semester The semester.
     * @return bool|null True if deletion was successful, null if no records to delete.
     */
    public function deleteCourseAddIfRegistrationNotPresent(string $academicYear, string $semester): ?bool
    {
        $courseAdds = $this->find('list')
            ->where([
                'CourseAdds.academic_year' => $academicYear,
                'CourseAdds.semester' => $semester,
                'CourseAdds.id NOT IN' => $this->ExamGrades->find()
                    ->where(['ExamGrades.course_add_id IS NOT' => null])
                    ->select(['ExamGrades.course_add_id']),
                'CourseAdds.student_id NOT IN' => $this->CourseRegistrations->find()
                    ->where(['CourseRegistrations.semester' => $semester, 'CourseRegistrations.academic_year' => $academicYear])
                    ->select(['CourseRegistrations.student_id'])
            ])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($courseAdds)) {
            return $this->deleteAll(['CourseAdds.id IN' => $courseAdds], false);
        }

        return null;
    }
}
