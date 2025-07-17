<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;

class CourseDropsTable extends Table
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

        $this->setTable('course_drops');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
            'joinType' => 'INNER',
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
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason');

        $validator
            ->uuid('department_approved_by')
            ->requirePresence('department_approved_by', 'create')
            ->notEmptyString('department_approved_by');

        $validator
            ->boolean('registrar_confirmation')
            ->allowEmptyString('registrar_confirmation');

        $validator
            ->uuid('registrar_confirmed_by')
            ->allowEmptyString('registrar_confirmed_by');

        $validator
            ->scalar('minute_number')
            ->maxLength('minute_number', 30)
            ->requirePresence('minute_number', 'create')
            ->notEmptyString('minute_number');

        $validator
            ->boolean('forced')
            ->notEmptyString('forced');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['course_registration_id'], 'CourseRegistrations'));

        return $rules;
    }

    /**
     * Checks if a student has taken and passed a prerequisite course or has an exemption.
     *
     * @param int|null $studentId The student ID.
     * @param int|null $prerequisiteCourseId The prerequisite course ID.
     * @return bool|int True if passed, 2 if taken but grade pending, false if not taken or failed.
     */
    public function prerequisiteTaken(?int $studentId = null, ?int $prerequisiteCourseId = null)
    {
        if ($studentId === null || $prerequisiteCourseId === null) {
            return false;
        }

        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');


        // Check if the student is exempt from the prerequisite
        if ($courseExemptionsTable->isCourseExempted($studentId, $prerequisiteCourseId) > 0) {
            return true;
        }


        // Get equivalent courses for the prerequisite
        $prerequisiteCourseIds = $coursesTable->getTakenEquivalentCourses($studentId, $prerequisiteCourseId);


        // Find published courses for the prerequisite or its equivalents
        $publishedCourseIds = $this->CourseRegistrations->PublishedCourses->find('list')
            ->where(['PublishedCourses.course_id IN' => $prerequisiteCourseIds])
            ->select(['id'])
            ->toArray();

        if (!empty($publishedCourseIds)) {
            // Check course registrations
            $courseRegistrationIds = $this->CourseRegistrations->find('list')
                ->where([
                    'CourseRegistrations.published_course_id IN' => $publishedCourseIds,
                    'CourseRegistrations.student_id' => $studentId,
                    'CourseRegistrations.id NOT IN' => $this->find()
                        ->where(['CourseDrops.student_id' => $studentId, 'CourseDrops.registrar_confirmation' => 1, 'CourseDrops.department_approval' => 1])
                        ->select(['CourseDrops.course_registration_id'])
                ])
                ->select(['id'])
                ->order([
                    'CourseRegistrations.academic_year' => 'DESC',
                    'CourseRegistrations.semester' => 'DESC',
                    'CourseRegistrations.id' => 'DESC',
                    'CourseRegistrations.created' => 'DESC'
                ])
                ->toArray();

            if (!empty($courseRegistrationIds)) {
                foreach ($courseRegistrationIds as $courseRegistrationId) {
                    $latestGrade = $this->CourseRegistrations->getCourseRegistrationLatestApprovedGradeDetail($courseRegistrationId);

                    if (empty($latestGrade)) {
                        return 2; // Grade pending
                    }

                    $gradeScaleId = strcasecmp($latestGrade['type'], 'Change') === 0
                        ? $examGradesTable->find()
                            ->where(['ExamGrades.id' => $latestGrade['ExamGrade']['exam_grade_id']])
                            ->select(['grade_scale_id'])
                            ->first()
                            ->grade_scale_id
                        : $latestGrade['ExamGrade']['grade_scale_id'];

                    $gradeSubmitted = $this->isGradePassed($latestGrade['ExamGrade']['grade'], $gradeScaleId);

                    if ($gradeSubmitted == 1) {
                        return true; // Passed
                    }
                }
            }

            // Check course adds
            $courseAddIds = $courseAddsTable->find('list')
                ->where([
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.student_id' => $studentId
                ])
                ->select(['id'])
                ->order([
                    'CourseAdds.academic_year' => 'DESC',
                    'CourseAdds.semester' => 'DESC',
                    'CourseAdds.id' => 'DESC',
                    'CourseAdds.created' => 'DESC'
                ])
                ->toArray();

            if (!empty($courseAddIds)) {
                foreach ($courseAddIds as $courseAddId) {
                    $latestGrade = $courseAddsTable->getCourseAddLatestApprovedGradeDetail($courseAddId);

                    if (empty($latestGrade)) {
                        return 2; // Grade pending
                    }

                    $gradeScaleId = strcasecmp($latestGrade['type'], 'Change') === 0
                        ? $examGradesTable->find()
                            ->where(['ExamGrades.id' => $latestGrade['ExamGrade']['exam_grade_id']])
                            ->select(['grade_scale_id'])
                            ->first()
                            ->grade_scale_id
                        : $latestGrade['ExamGrade']['grade_scale_id'];

                    $gradeSubmitted = $this->isGradePassed($latestGrade['ExamGrade']['grade'], $gradeScaleId);

                    if ($gradeSubmitted == 1) {
                        return true; // Passed
                    }
                }
            }
        }

        return false; // Failed or not taken
    }

    /**
     * Checks if a student has taken a course or its equivalent and determines if they can add it again.
     *
     * @param int|null $studentId The student ID.
     * @param int|null $courseId The course ID.
     * @return int Status code (1 = exclude, 2 = exclude, 3 = allow add, 4 = failed prerequisite).
     */
    public function courseTaken(?int $studentId = null, ?int $courseId = null)
    {
        if ($studentId === null || $courseId === null) {
            return 3; // Allow add if inputs are invalid
        }

        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        // Check for prerequisites
        if ($this->isPrerequisiteExist($courseId)) {
            $prerequisites = $this->CourseRegistrations->PublishedCourses->Courses->Prerequisites->find('list')
                ->where(['Prerequisites.course_id' => $courseId])
                ->select(['prerequisite_course_id'])
                ->toArray();

            foreach ($prerequisites as $prerequisiteId) {
                if (!$this->prerequisiteTaken($studentId, $prerequisiteId)) {
                    return 4; // Failed prerequisite
                }
            }
        }

        // Get equivalent courses
        $equivalentCourseTaken = $coursesTable->getTakenEquivalentCourses($studentId, $courseId);

        // Find published courses for the course or its equivalents
        $publishedCourseIds = $this->CourseRegistrations->PublishedCourses->find('list')
            ->where(['PublishedCourses.course_id IN' => $equivalentCourseTaken])
            ->select(['id'])
            ->toArray();

        // Check course registrations
        if (!empty($publishedCourseIds)) {
            $courseRegistrationIds = $this->CourseRegistrations->find('list')
                ->where([
                    'CourseRegistrations.published_course_id IN' => $publishedCourseIds,
                    'CourseRegistrations.student_id' => $studentId,
                    'CourseRegistrations.id NOT IN' => $this->find()
                        ->where(['CourseDrops.registrar_confirmation' => 1, 'CourseDrops.department_approval' => 1])
                        ->select(['CourseDrops.course_registration_id'])
                ])
                ->select(['id'])
                ->order([
                    'CourseRegistrations.academic_year' => 'ASC',
                    'CourseRegistrations.semester' => 'ASC',
                    'CourseRegistrations.id' => 'ASC'
                ])
                ->toArray();

            if (!empty($courseRegistrationIds)) {
                foreach ($courseRegistrationIds as $courseRegistrationId) {
                    $latestGrade = $examGradesTable->getApprovedGrade($courseRegistrationId, 1);

                    if (empty($latestGrade)) {
                        return 2; // Exclude due to pending grade
                    }

                    $gradeSubmitted = !empty($latestGrade['grade'])
                        ? $this->isTheGradeAllowedForRepetition($latestGrade['grade'], $latestGrade['grade_scale_id'])
                        : $this->isTheGradeAllowedForRepetition($latestGrade['ExamGrade']['grade'], $latestGrade['ExamGrade']['grade_scale_id']);

                    if ($gradeSubmitted == 1) {
                        return 3; // Allow repetition
                    } elseif ($gradeSubmitted == 0) {
                        return 2; // Exclude
                    }
                }
            }

            // Check course adds
            $courseAddIds = $courseAddsTable->find('list')
                ->where([
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.registrar_confirmation' => 1,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.student_id' => $studentId
                ])
                ->select(['id'])
                ->order([
                    'CourseAdds.academic_year' => 'ASC',
                    'CourseAdds.semester' => 'ASC',
                    'CourseAdds.id' => 'ASC'
                ])
                ->toArray();

            if (!empty($courseAddIds)) {
                foreach ($courseAddIds as $courseAddId) {
                    $latestGrade = $examGradesTable->getApprovedGrade($courseAddId, 0);

                    if (empty($latestGrade)) {
                        return 2; // Exclude due to pending grade
                    }

                    $gradeSubmitted = !empty($latestGrade['grade'])
                        ? $this->isTheGradeAllowedForRepetition($latestGrade['grade'], $latestGrade['grade_scale_id'])
                        : $this->isTheGradeAllowedForRepetition($latestGrade['ExamGrade']['grade'], $latestGrade['ExamGrade']['grade_scale_id']);

                    if ($gradeSubmitted == 1) {
                        return 3; // Allow repetition
                    } elseif ($gradeSubmitted == 0) {
                        return 2; // Exclude
                    }
                }
            }
        }

        return 3; // Allow add
    }

    /**
     * Checks if a grade is considered a pass based on the grade scale.
     *
     * @param string|null $grade The grade to check.
     * @param int|null $scaleId The grade scale ID.
     * @return int 1 if passed, 0 if failed.
     */
    public function isGradePassed(?string $grade = null, ?int $scaleId = null)
    {
        if ($grade === null || $scaleId === null) {
            return 0;
        }

        $gradeScalesTable = TableRegistry::getTableLocator()->get('GradeScales');

        if (strcasecmp($grade, 'I') === 0) {
            return 1;
        }

        $isGradePassMark = $gradeScalesTable->find()
            ->where(['GradeScales.id' => $scaleId])
            ->contain([
                'GradeScaleDetails' => [
                    'Grades' => ['fields' => ['id', 'pass_grade', 'grade']]
                ]
            ])
            ->first();

        debug($grade);

        foreach ($isGradePassMark->grade_scale_details as $value) {
            if (!empty($grade) && (strcasecmp($value->grade->grade, $grade) === 0 && $value->grade->pass_grade == 1) || strcasecmp($value->grade->grade, 'I') === 0) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Checks if a grade allows for course repetition based on the grade scale.
     *
     * @param string|null $grade The grade to check.
     * @param int|null $scaleId The grade scale ID.
     * @return int 1 if repetition allowed, 0 if not.
     */
    public function isTheGradeAllowedForRepetition(?string $grade = null, ?int $scaleId = null)
    {
        if ($grade === null || $scaleId === null) {
            return 0;
        }

        $gradeScalesTable = TableRegistry::getTableLocator()->get('GradeScales');

        $isGradeAllowRepetition = $gradeScalesTable->find()
            ->where(['GradeScales.id' => $scaleId])
            ->contain([
                'GradeScaleDetails' => [
                    'Grades' => ['fields' => ['id', 'pass_grade', 'grade', 'allow_repetition']]
                ]
            ])
            ->first();

        debug($grade);
        debug($scaleId);

        foreach ($isGradeAllowRepetition->grade_scale_details as $value) {
            if (!empty($grade) && strcasecmp($value->grade->grade, $grade) === 0 && $value->grade->allow_repetition) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Retrieves courses recommended for dropping due to unmet prerequisites.
     *
     * @param string|null $semester The semester.
     * @param string|null $academicYear The academic year.
     * @param int|null $studentId The student ID.
     * @return array List of courses with prerequisite status.
     */

    public function dropRecommendedCourses(?string $semester = null, ?string $academicYear = null, ?int $studentId = null)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $coursesDrop = $courseRegistrationsTable->find()
            ->where([
                'CourseRegistrations.academic_year LIKE' => $academicYear . '%',
                'CourseRegistrations.semester LIKE' => $semester . '%',
                'CourseRegistrations.student_id' => $studentId,
                'Students.graduated' => 0,
                'CourseRegistrations.id NOT IN' => $courseDropsTable->find()
                    ->select(['course_registration_id']),
                'CourseRegistrations.id NOT IN' => $examGradesTable->find()
                    ->where(['ExamGrades.course_registration_id IS NOT' => null])
                    ->select(['course_registration_id'])
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => [
                        'Prerequisites' => [
                            'fields' => ['id', 'course_id', 'prerequisite_course_id', 'co_requisite']
                        ]
                    ]
                ],
                'Students' => [
                    'fields' => ['id', 'full_name', 'studentnumber', 'gender', 'graduated']
                ],
                'ExamGrades'
            ])
            ->toArray();

        $courseDropReformat = [];
        $count = 0;

        foreach ($coursesDrop as $value) {
            if (!empty($value->published_course->course->prerequisites)) {
                $passedCount = 0;
                foreach ($value->published_course->course->prerequisites as $prevalue) {
                    if ($this->prerequisiteTaken($studentId, $prevalue->prerequisite_course_id)) {
                        $passedCount++;
                    }
                }

                $courseDropReformat[$count] = $value->toArray();
                $courseDropReformat[$count]['prerequisite_taken_passed'] = $passedCount == count($value->published_course->course->prerequisites) ? 1 : 0;
            } else {
                $courseDropReformat[$count] = $value->toArray();
                $courseDropReformat[$count]['prerequisite_taken_passed'] = 1;
            }

            $count++;
        }

        return $courseDropReformat;
    }

    /**
     * Lists students who need forced course drops due to registration without grades or drops.
     *
     * @param array|null $departmentIds List of department IDs.
     * @param array|null $collegeIds List of college IDs.
     * @param array|null $programIds List of program IDs.
     * @param array|null $programTypeIds List of program type IDs.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @param int $freshmanInclude Include freshman students (1 = yes, 0 = no).
     * @return array List of students and count.
     */
    public function listOfStudentsNeedForceDrop(
        ?array $departmentIds = null,
        ?array $collegeIds = null,
        ?array $programIds = null,
        ?array $programTypeIds = null,
        ?string $academicYear = null,
        ?string $semester = null,
        int $freshmanInclude = 0
    ): array {
        $typeOfRegistrations = [11, 12, 13];
        $listOfRegisteredIds = [];

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $query = $courseRegistrationsTable->find()
            ->where(['CourseRegistrations.type IN' => $typeOfRegistrations])
            ->group(['CourseRegistrations.student_id']);

        if (!empty($academicYear)) {
            $query->where(['CourseRegistrations.academic_year' => $academicYear]);
        }

        if (!empty($semester)) {
            $query->where(['CourseRegistrations.semester' => $semester]);
        }

        if ($freshmanInclude && !empty($collegeIds)) {
            $query->where([
                'OR' => [
                    'CourseRegistrations.year_level_id IS' => null,
                    'CourseRegistrations.year_level_id' => '',
                    'CourseRegistrations.year_level_id' => 0
                ]
            ]);
        }

        if (!empty($departmentIds)) {
            $query->contain([
                'Students' => function ($q) use ($departmentIds, $programIds, $programTypeIds) {
                    return $q->where([
                        'Students.department_id IN' => (array)$departmentIds,
                        'Students.program_id IN' => (array)$programIds,
                        'Students.program_type_id IN' => (array)$programTypeIds,
                        'Students.graduated' => 0
                    ])->select(['id', 'department_id']);
                }
            ]);
        } elseif (!empty($collegeIds)) {
            $query->contain([
                'Students' => function ($q) use ($collegeIds, $programIds, $programTypeIds) {
                    return $q->where([
                        'Students.department_id IS' => null,
                        'Students.college_id IN' => (array)$collegeIds,
                        'Students.program_id IN' => (array)$programIds,
                        'Students.program_type_id IN' => (array)$programTypeIds,
                        'Students.graduated' => 0
                    ])->select(['id', 'college_id', 'department_id']);
                }
            ]);
        }

        $results = $query->toArray();

        foreach ($results as $registration) {
            $student = $registration->student;

            if (
                !empty($student->department_id) &&
                !empty($registration->student_id) &&
                (is_array($departmentIds) ? in_array($student->department_id, $departmentIds) : $student->department_id == $departmentIds)
            ) {
                $listOfRegisteredIds[] = $registration->id;
            } elseif (
                !empty($student->college_id) &&
                !empty($registration->student_id) &&
                (is_array($collegeIds) ? in_array($student->college_id, $collegeIds) : $student->college_id == $collegeIds)
            ) {
                $listOfRegisteredIds[] = $registration->id;
            }
        }

        $coursesDrop = ['list' => [], 'count' => 0];

        if (!empty($listOfRegisteredIds)) {
            $coursesDrop['list'] = $courseRegistrationsTable->find()
                ->where(['CourseRegistrations.id IN' => $listOfRegisteredIds])
                ->contain([
                    'Students' => [
                        'Departments' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Programs' => ['fields' => ['id', 'name']],
                        'fields' => ['id', 'program_id', 'program_type_id', 'department_id', 'college_id', 'studentnumber',
                            'first_name','middle_name','last_name', 'gender', 'graduated']
                    ],
                    'CourseDrops',
                    'ExamGrades'
                ])
                ->toArray();

            foreach ($coursesDrop['list'] as $key => $cdrop) {
                if (!empty($cdrop->exam_grades) || !empty($cdrop->course_drops)) {
                    unset($coursesDrop['list'][$key]);
                }
            }

            $coursesDrop['count'] = count($coursesDrop['list']);
        }

        return $coursesDrop;
    }

    /**
     * Retrieves a list of courses eligible for dropping for a student.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year.
     * @return array Student details with drop-eligible courses.
     */
    public function dropCoursesList(?int $studentId = null, ?string $academicYear = null)
    {
        $studentDetailWithListOfRegisteredCourses = [];

        $latestAcademicYearSemester = $this->CourseRegistrations->getLastestStudentSemesterAndAcademicYear($studentId, $academicYear);

        $previousStatusSemester = $this->CourseRegistrations->Students->StudentExamStatuses->getPreviousSemester(
            $latestAcademicYearSemester['academic_year'],
            $latestAcademicYearSemester['semester']
        );

        $latestStatusYearSemester = $this->CourseRegistrations->Students->StudentExamStatuses->studentYearAndSemesterLevelOfStatusDisplay(
            $studentId,
            $latestAcademicYearSemester['academic_year'],
            $previousStatusSemester['semester']
        );

        $studentSectionExamStatus = $this->CourseRegistrations->Students->getStudentSection(
            $studentId,
            $latestAcademicYearSemester['academic_year'],
            $latestStatusYearSemester['semester']
        );

        $studentDetailWithListOfRegisteredCourses['student_basic'] = $studentSectionExamStatus;

        $semester = $this->CourseRegistrations->latestCourseRegistrationSemester($academicYear, $studentId);

        $conditions = [
            'CourseRegistrations.academic_year LIKE' => $academicYear . '%',
            'CourseRegistrations.semester' => $semester,
            'CourseRegistrations.student_id' => $studentId,
            'CourseRegistrations.id NOT IN' => $this->find()
                ->select(['CourseDrops.course_registration_id']),
            'CourseRegistrations.id NOT IN' => $this->CourseRegistrations->ExamGrades->find()
                ->where(['ExamGrades.course_registration_id IS NOT' => null])
                ->select(['ExamGrades.course_registration_id'])
        ];

        if (!empty($studentSectionExamStatus['StudentBasicInfo']['department_id'])) {
            $conditions['CourseRegistrations.year_level_id'] = $studentSectionExamStatus['Section']['year_level_id'];
        } else {
            $conditions['OR'] = [
                'CourseRegistrations.year_level_id IS' => null,
                'CourseRegistrations.year_level_id' => '',
                'CourseRegistrations.year_level_id' => 0
            ];
        }

        $coursesDrop = $this->CourseRegistrations->find()
            ->where($conditions)
            ->contain([
                'PublishedCourses' => [
                    'Courses' => [
                        'Prerequisites' => ['fields' => ['id', 'prerequisite_course_id', 'co_requisite']],
                        'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                        'fields' => ['id', 'course_code', 'course_title', 'lecture_hours', 'tutorial_hours', 'laboratory_hours', 'credit']
                    ]
                ],
                'YearLevels'
            ])
            ->toArray();

        debug($coursesDrop);

        $alreadyDropped = [];

        foreach ($coursesDrop as $value) {
            $check = $this->find()
                ->where(['CourseDrops.course_registration_id' => $value->id])
                ->count();
            if ($check > 0) {
                $alreadyDropped[] = $value->id;
            }
        }

        $studentDetailWithListOfRegisteredCourses['alreadyDropped'] = $alreadyDropped;
        $studentDetailWithListOfRegisteredCourses['courseDrop'] = $coursesDrop;
        $studentDetailWithListOfRegisteredCourses['semester'] = $latestAcademicYearSemester['semester'];

        return $studentDetailWithListOfRegisteredCourses;
    }

    /**
     * Lists students registered but not dropped or graded.
     *
     * @param array|null $data Search criteria.
     * @param string|null $currentAcademicYear Current academic year.
     * @return array List of students.
     */
    public function studentListRegisteredButNotDropped(?array $data = null, ?string $currentAcademicYear = null)
    {
        $latestSemesterAcademicYear = $this->CourseRegistrations->latestAcademicYearSemester($currentAcademicYear);

        $conditions = [
            'Students.graduated' => 0,
            'CourseRegistrations.academic_year LIKE' => $latestSemesterAcademicYear['academic_year'] . '%',
            'CourseRegistrations.id NOT IN' => $this->CourseRegistrations->ExamGrades->find()
                ->where(['ExamGrades.course_registration_id IS NOT' => null])
                ->select(['ExamGrades.course_registration_id']),
            'CourseRegistrations.id NOT IN' => $this->find()
                ->where(['CourseDrops.course_registration_id IS NOT' => null])
                ->select(['CourseDrops.course_registration_id'])
        ];

        if (!empty($data['Student']['department_id'])) {
            $conditions['Students.department_id'] = $data['Student']['department_id'];
        }

        if (!empty($data['Student']['studentnumber'])) {
            $conditions['Students.studentnumber'] = $data['Student']['studentnumber'];
        }

        if (!empty($data['Student']['college_id'])) {
            $conditions['Students.college_id'] = $data['Student']['college_id'];
            $conditions['Students.department_id IS'] = null;
        }

        if (!empty($data['Student']['semester'])) {
            $conditions['CourseRegistrations.semester'] = $data['Student']['semester'];
        }

        if (!empty($data['Student']['program_id'])) {
            $conditions['Students.program_id'] = $data['Student']['program_id'];
        }

        if (!empty($data['Student']['program_type_id'])) {
            $conditions['Students.program_type_id'] = $data['Student']['program_type_id'];
        }

        if (!empty($this->department_ids) && empty($data['Student']['department_id']) && empty($data['Student']['studentnumber'])) {
            $conditions['Students.department_id IN'] = $this->department_ids;
        } elseif (!empty($this->college_ids) && empty($data['Student']['college_id'])) {
            $conditions['Students.college_id IN'] = $this->college_ids;
            $conditions['Students.department_id IS'] = null;
        }

        $result = $this->CourseRegistrations->find()
            ->where($conditions)
            ->contain([
                'Students' => [
                    'fields' => ['id', 'full_name', 'gender', 'studentnumber', 'graduated'],
                    'Departments' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']]
                ],
                'CourseDrops',
                'ExamGrades'
            ])
            ->group(['CourseRegistrations.student_id'])
            ->toArray();

        debug($result);
        return $result;
    }

    /**
     * Checks if a course has non-co-requisite prerequisites.
     *
     * @param int|null $courseId The course ID.
     * @return bool True if prerequisites exist, false otherwise.
     */
    public function isPrerequisiteExist(?int $courseId = null)
    {
        $count = $this->CourseRegistrations->PublishedCourses->Courses->Prerequisites->find()
            ->where(['Prerequisites.course_id' => $courseId, 'Prerequisites.co_requisite' => 0])
            ->count();

        return $count > 0;
    }

    /**
     * Retrieves the prerequisite course ID for a given course.
     *
     * @param int|null $courseId The course ID.
     * @return int|array The prerequisite course ID or empty array if none.
     */
    public function getPrerequisiteCourseId(?int $courseId = null)
    {
        $prerequisite = $this->CourseRegistrations->PublishedCourses->Courses->Prerequisites->find()
            ->where(['Prerequisites.course_id' => $courseId])
            ->select(['prerequisite_course_id'])
            ->first();

        return $prerequisite->prerequisite_course_id ?? [];
    }

    /**
     * Lists course drop requests by role and department/college.
     *
     * @param string|null $roleId The role ID (e.g., ROLE_DEPARTMENT, ROLE_COLLEGE).
     * @param int|null $departmentId The department ID.
     * @param string|null $currentAcademicYear The current academic year.
     * @param array|null $collegeIds List of college IDs.
     * @return array Organized course drop requests.
     */
    public function listCourseDropRequest(?string $roleId = null, ?int $departmentId = null, ?string $currentAcademicYear = null, ?array $collegeIds = null): array
    {
        $sectionOrganizedPublishedCourse = [];
        $sections = [];

        if (!empty($departmentId)) {
            $sections = $this->Students->Sections->find('list')
                ->where(['Sections.department_id' => $departmentId, 'Sections.archive' => 0])
                ->select(['id', 'name'])
                ->toArray();
        } elseif (!empty($collegeIds)) {
            $sections = $this->Students->Sections->find('list')
                ->where(['Sections.department_id IS' => null, 'Sections.college_id IN' => $collegeIds, 'Sections.archive' => 0])
                ->select(['id', 'name'])
                ->toArray();
        }

        $conditions = ['Students.graduated' => 0];
        $contain = [
            'CourseRegistrations',
            'Students' => [
                'Departments' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'StudentsSections' => ['conditions' => ['StudentsSections.archive' => 0]],
                'CourseRegistrations' => [
                    'PublishedCourses' => [
                        'Courses' => ['fields' => ['id', 'course_detail_hours', 'credit', 'course_title', 'course_code']],
                        'fields' => ['id', 'semester', 'academic_year']
                    ],
                    'fields' => ['id']
                ],
                'fields' => ['id', 'full_name', 'department_id', 'program_id', 'program_type_id', 'college_id', 'graduated']
            ]
        ];

        if ($roleId === 'ROLE_DEPARTMENT' && !empty($departmentId)) {
            $conditions['Students.department_id'] = $departmentId;
            $conditions['CourseDrops.department_approval IS'] = null;
            $conditions['CourseDrops.registrar_confirmation IS'] = null;
        } elseif (!empty($collegeIds)) {
            $conditions['Students.department_id IS'] = null;
            $conditions['Students.college_id IN'] = $collegeIds;
            if ($roleId === 'ROLE_COLLEGE') {
                $conditions['CourseDrops.department_approval IS'] = null;
                $conditions['CourseDrops.registrar_confirmation IS'] = null;
            } else {
                $conditions['CourseDrops.department_approval'] = 1;
                $conditions['CourseDrops.registrar_confirmation IS'] = null;
            }
        } else {
            $conditions['Students.department_id'] = $departmentId;
            $conditions['CourseDrops.department_approval'] = 1;
            $conditions['CourseDrops.registrar_confirmation IS'] = null;
        }

        $courseDrops = $this->find()
            ->where($conditions)
            ->contain($contain)
            ->toArray();

        foreach ($courseDrops as &$pv) {
            if (!empty($pv->student->students_sections) && array_key_exists($pv->student->students_sections[0]->section_id, $sections)) {
                $semesterAc = $this->CourseRegistrations->getLastestStudentSemesterAndAcademicYear($pv->student->id, $currentAcademicYear, 1);
                $pv->student->max_load = $this->Students->calculateStudentLoad($pv->student->id, $semesterAc['semester'], $semesterAc['academic_year']);

                $key1 = !empty($pv->student->department->name) ? $pv->student->department->name : 'Pre/Fresh';
                $key2 = $pv->student->program->name;
                $key3 = $pv->student->program_type->name;
                $key4 = $sections[$pv->student->students_sections[0]->section_id];

                $sectionOrganizedPublishedCourse[$key1][$key2][$key3][$key4][] = $pv->toArray();
            }
        }

        return $sectionOrganizedPublishedCourse;
    }

    /**
     * Counts course drop requests by role and department/college.
     *
     * @param array|null $departmentIds List of department IDs.
     * @param int $registrar Role indicator (1 = registrar, 2 = department, 3 = college registrar).
     * @param array|null $collegeIds List of college IDs.
     * @return int The count of course drop requests.
     */
    public function countDropRequest(?array $departmentIds = null, int $registrar = 1, ?array $collegeIds = null): int
    {
        $query = $this->find()
            ->contain([
                'Students' => function ($q) {
                    return $q->contain([
                        'StudentsSections' => function ($q2) {
                            return $q2->where(['StudentsSections.archive' => 0]);
                        }
                    ])->select(['id', 'full_name', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated']);
                }
            ]);

        if ($registrar === 1) {
            if (!empty($departmentIds)) {
                $query->where([
                    'Students.department_id IN' => (array)$departmentIds,
                    'CourseDrops.department_approval' => 1,
                    'CourseDrops.registrar_confirmation IS' => null,
                    'Students.graduated' => 0
                ]);
            } elseif (!empty($collegeIds)) {
                $query->where([
                    'Students.department_id IS' => null,
                    'Students.college_id IN' => (array)$collegeIds,
                    'CourseDrops.department_approval' => 1,
                    'CourseDrops.registrar_confirmation IS' => null,
                    'Students.graduated' => 0
                ]);
            }
        } elseif ($registrar === 2) {
            $query->where([
                'Students.department_id IN' => (array)$departmentIds,
                'CourseDrops.department_approval IS' => null,
                'Students.graduated' => 0
            ]);
        } elseif ($registrar === 3 && !empty($collegeIds)) {
            $query->where([
                'Students.department_id IS' => null,
                'Students.college_id IN' => (array)$collegeIds,
                'CourseDrops.department_approval' => 1,
                'CourseDrops.registrar_confirmation IS' => null,
                'Students.graduated' => 0
            ]);
        }

        return $query->count();
    }

    /**
     * Calculates the total credit hours of dropped courses for a student.
     *
     * @param int $studentId The student ID.
     * @return int The sum of dropped course credits.
     */
    public function droppedCreditSum(int $studentId): int
    {
        $courseDrops = $this->find()
            ->where([
                'CourseDrops.student_id' => $studentId,
                'CourseDrops.department_approval' => 1,
                'CourseDrops.registrar_confirmation' => 1
            ])
            ->contain([
                'CourseRegistrations' => [
                    'PublishedCourses' => ['Courses']
                ]
            ])
            ->toArray();

        $droppedSum = 0;

        foreach ($courseDrops as $v) {
            if (!empty($v->course_registration->published_course->course_id)) {
                $courseTakenHaveGrade = $this->CourseRegistrations->PublishedCourses->Courses->isCourseTakenHaveRecentPassGrade(
                    $studentId,
                    $v->course_registration->published_course->course_id
                );
                if (!$courseTakenHaveGrade) {
                    $droppedSum += $v->course_registration->published_course->course->credit;
                }
            }
        }

        return $droppedSum;
    }
}
