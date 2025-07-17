<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\ORM\TableRegistry;

class CourseRegistrationsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {

        parent::initialize($config);

        $this->setTable('course_registrations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('AcademicCalendars', [
            'foreignKey' => 'academic_calendar_id',
            'joinType' => 'INNER'
        ]);

        // HasMany Associations
        $this->hasMany('ExamResults', [
            'foreignKey' => 'course_registration_id',
            'dependent' => false
        ]);

        $this->hasMany('ExamGrades', [
            'foreignKey' => 'course_registration_id',
            'dependent' => true
        ]);

        $this->hasMany('ExcludedCoursesFromTranscripts', [
            'foreignKey' => 'course_registration_id',
            'dependent' => true
        ]);

        $this->hasMany('CourseDrops', [
            'foreignKey' => 'course_registration_id',
            'dependent' => false
        ]);

        $this->hasMany('MakeupExams', [
            'foreignKey' => 'course_registration_id',
            'dependent' => true
        ]);

        $this->hasMany('ResultEntryAssignments', [
            'foreignKey' => 'course_registration_id',
            'dependent' => true,
            'sort' => ['ResultEntryAssignments.id' => 'DESC'],
        ]);


        // Custom Logable behavior equivalent (simplified)
        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('year_level_id')
            ->requirePresence('year_level_id', 'create')
            ->notEmptyString('year_level_id', 'Year level is required.');

        $validator
            ->scalar('academic_year')
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year', 'Academic year is required.');

        $validator
            ->scalar('semester')
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester', 'Semester is required.');

        $validator
            ->integer('student_id')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id', 'Student is required.');

        $validator
            ->integer('published_course_id')
            ->requirePresence('published_course_id', 'create')
            ->notEmptyString('published_course_id', 'Published course is required.');

        $validator
            ->integer('section_id')
            ->requirePresence('section_id', 'create')
            ->notEmptyString('section_id', 'Section is required.');

        return $validator;
    }

    /**
     * Returns a rules checker object for validating application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {

        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels', 'Invalid year level.'));
        $rules->add($rules->existsIn(['student_id'], 'Students', 'Invalid student.'));
        $rules->add($rules->existsIn(['section_id'], 'Sections', 'Invalid section.'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses', 'Invalid published course.'));
        $rules->add($rules->existsIn(['academic_calendar_id'], 'AcademicCalendars', 'Invalid academic calendar.'));

        return $rules;
    }

    /**
     * Checks if a student is already registered for a semester.
     *
     * @param string|null $semester The semester.
     * @param string|null $academicYear The academic year.
     * @param int|null $studentId The student ID.
     * @return int Number of registrations.
     */
    public function alreadyRegistered($studentId = null, $academicYear = null, $semester = null)
    {

        if (!$studentId || !$academicYear) {
            return true; // Prevent inconsistencies
        }

        $semester = $semester ?? $this->latestCourseRegistrationSemester($academicYear, $studentId);

        return $this->find()
            ->where([
                'student_id' => $studentId,
                'academic_year LIKE' => $academicYear . '%',
                'semester' => $semester
            ])
            ->count();
    }

    /**
     * Retrieves the latest semester for a student’s course registration.
     *
     * @param string|null $academicYear The academic year.
     * @param int|null $studentId The student ID.
     * @return string The latest semester (e.g., 'I', 'II').
     */
    public function latestCourseRegistrationSemester($academicYear = null, $studentId = null)
    {

        $semester = 'I';

        if ($academicYear && $studentId) {
            $latest = $this->find()
                ->select(['semester', 'academic_year'])
                ->where([
                    'academic_year LIKE' => $academicYear . '%',
                    'student_id' => $studentId
                ])
                ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                ->first();

            return $latest ? $latest->semester : $semester;
        }

        if ($academicYear) {
            $latest = $this->find()
                ->select(['semester', 'academic_year'])
                ->where(['academic_year LIKE' => $academicYear . '%'])
                ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                ->first();

            return $latest ? $latest->semester : $semester;
        }

        return $semester;
    }

    /**
     * Retrieves the latest academic year and semester for published courses.
     *
     * @param string|null $academicYear The academic year.
     * @param int|null $studentProgramId The program ID.
     * @param int|null $studentProgramTypeId The program type ID.
     * @return array Array with 'semester' and 'academic_year'.
     */
    public function latestAcademicYearSemester(
        $academicYear = null,
        $studentProgramId = null,
        $studentProgramTypeId = null
    ) {

        $acSemester = [];

        if ($academicYear) {
            $conditions = ['academic_year LIKE' => $academicYear . '%'];
            if ($studentProgramId && $studentProgramTypeId) {
                $conditions['program_id'] = $studentProgramId;
                $conditions['program_type_id'] = $studentProgramTypeId;
            }

            $latestSemester = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find()
                ->select(['semester', 'academic_year'])
                ->where($conditions)
                ->group(['semester'])
                ->order(['MAX(created)' => 'DESC'])
                ->first();

            if ($latestSemester) {
                $acSemester = [
                    'semester' => $latestSemester->semester,
                    'academic_year' => $latestSemester->academic_year
                ];
            }
        }

        return $acSemester;
    }

    /**
     * Retrieves the latest semester for a section.
     *
     * @param int|null $sectionId The section ID.
     * @param string|null $currentAcademicYear The academic year.
     * @return string|int Semester or 2 if none found.
     */
    public function latestSemesterOfSection($sectionId = null, $currentAcademicYear = null)
    {

        if (!$sectionId || !$currentAcademicYear) {
            return 2;
        }

        $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list', ['valueField' => 'id'])
            ->where([
                'academic_year LIKE' => $currentAcademicYear . '%',
                'section_id' => $sectionId,
                'drop' => 0
            ])
            ->toArray();

        if (empty($publishedCourseIds)) {
            return 2;
        }

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $validCourseIds = [];
        foreach ($publishedCourseIds as $courseId) {
            if (!$examGradesTable->isGradeSubmitted($courseId)) {
                $validCourseIds[] = $courseId;
            }
        }

        if ($validCourseIds) {
            $publishedCourse = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find()
                ->select(['semester'])
                ->where([
                    'id IN' => $validCourseIds,
                    'academic_year LIKE' => $currentAcademicYear . '%',
                    'drop' => 0
                ])
                ->first();

            return $publishedCourse ? $publishedCourse->semester : 2;
        }

        return 2;
    }

    /**
     * Checks if a course is published for a section, allowing split.
     *
     * @param int|null $sectionId The section ID.
     * @param string|null $currentAcademicYear The academic year.
     * @return string|int Semester or 2 if splitting is allowed.
     */
    public function checkCourseIsPublishedForSection($sectionId = null, $currentAcademicYear = null)
    {

        if (!$sectionId || !$currentAcademicYear) {
            return 2;
        }

        $publishedCourses = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find()
            ->where([
                'academic_year' => $currentAcademicYear,
                'section_id' => $sectionId
            ])
            ->toArray();

        if (empty($publishedCourses)) {
            return 2;
        }

        $validCourseIds = [];
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        foreach ($publishedCourses as $course) {
            if (!$examGradesTable->isGradeSubmitted($course->id)) {
                $validCourseIds[] = $course->id;
            }
        }

        if ($validCourseIds) {
            $publishedCourse = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find()
                ->select(['semester'])
                ->where([
                    'id IN' => $validCourseIds,
                    'academic_year' => $currentAcademicYear,
                    'section_id' => $sectionId
                ])
                ->first();

            return $publishedCourse ? $publishedCourse->semester : 2;
        }

        return 2;
    }

    /**
     * Checks if merging sections is possible.
     *
     * @param int|null $newSectionId The new section ID.
     * @param array $sectionIds Array of section IDs to merge.
     * @param string|null $currentAcademicYear The academic year.
     * @return array|int Published course IDs or error code (2, 3, 4).
     */
    public function checkMergingIsPossible($newSectionId = null, $sectionIds = [], $currentAcademicYear = null)
    {

        if (!$newSectionId || empty($sectionIds) || !$currentAcademicYear) {
            return [];
        }

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

        $representativeStudentOfSection = [];
        $representativeSectionStudent = [];
        foreach ($sectionIds as $sectionId) {
            $student = $studentsSectionsTable->find()
                ->where([
                    'section_id' => $sectionId,
                    'student_id NOT IN' => $this->Students->Readmissions->find('list', ['valueField' => 'student_id'])
                ])
                ->first();

            if ($student) {
                $representativeStudentOfSection[$sectionId] = $student->student_id;
                $representativeSectionStudent[$student->student_id] = $sectionId;
            }
        }

        $students = $this->Students->find()
            ->where([
                'id IN' => $representativeStudentOfSection,
                'id NOT IN' => $this->Students->Readmissions->find('list', ['valueField' => 'student_id'])
            ])
            ->contain(['StudentsSections'])
            ->toArray();

        $earlierSections = [];
        foreach ($students as $student) {
            foreach ($student->students_sections as $section) {
                $currentSection = $representativeSectionStudent[$student->id];
                if ($section->section_id != $currentSection) {
                    $earlierSections[$currentSection][] = $section->section_id;
                }
            }
        }

        foreach ($sectionIds as $sectionId) {
            $mergerSection = $sectionsTable->find()
                ->where(['id' => $sectionId])
                ->contain(['YearLevels'])
                ->first();

            if (isset($earlierSections[$sectionId])) {
                foreach ($earlierSections[$sectionId] as $earlierSectionId) {
                    $earlierSection = $sectionsTable->find()
                        ->where(['id' => $earlierSectionId])
                        ->contain(['YearLevels'])
                        ->first();

                    if ($earlierSection->year_level->name > $mergerSection->year_level->name) {
                        return 2; // Year level upgrade detected
                    }
                }
            }
        }

        $coursesTakenThroughout = [];
        foreach ($sectionIds as $sectionId) {
            if (isset($earlierSections[$sectionId])) {
                foreach ($earlierSections[$sectionId] as $earlierSectionId) {
                    $coursesTakenThroughout[$sectionId][$earlierSectionId] = TableRegistry::getTableLocator()->get(
                        'PublishedCourses'
                    )
                        ->find()
                        ->select(['course_id'])
                        ->where(['section_id' => $earlierSectionId, 'drop' => 0])
                        ->contain(['Courses'])
                        ->toArray();
                }
            }
            $coursesTakenThroughout[$sectionId][$sectionId] = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find()
                ->select(['course_id'])
                ->where(['section_id' => $sectionId, 'drop' => 0])
                ->contain(['Courses'])
                ->toArray();
        }

        $takenCourseOrganizedByCourseId = [];
        foreach ($sectionIds as $sectionId) {
            if (isset($coursesTakenThroughout[$sectionId])) {
                foreach ($coursesTakenThroughout[$sectionId] as $courses) {
                    foreach ($courses as $course) {
                        $takenCourseOrganizedByCourseId[$sectionId][] = $course->course_id;
                    }
                }
            }
        }

        foreach ($sectionIds as $sectionId) {
            foreach ($sectionIds as $otherSectionId) {
                if ($sectionId != $otherSectionId && isset($takenCourseOrganizedByCourseId[$sectionId], $takenCourseOrganizedByCourseId[$otherSectionId])) {
                    if (array_diff(
                        $takenCourseOrganizedByCourseId[$sectionId],
                        $takenCourseOrganizedByCourseId[$otherSectionId]
                    )) {
                        return 3; // Different courses taken
                    }
                }
            }
        }

        $publishedCourses = [];
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        foreach ($sectionIds as $sectionId) {
            $courses = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find()
                ->select(['id'])
                ->where([
                    'academic_year' => $currentAcademicYear,
                    'section_id' => $sectionId
                ])
                ->toArray();

            foreach ($courses as $course) {
                if ($examGradesTable->isGradeSubmitted($course->id)) {
                    return 4; // Grade submitted
                }
                $publishedCourses[] = $course->id;
            }
        }

        return $publishedCourses;
    }

    /**
     * Updates course registrations after section merge.
     *
     * @param array $publishedCourseIds Array of published course IDs.
     * @param int $newSectionId The new section ID.
     * @return void
     */
    public function updateCourseRegistrationPublishedCourseInstructorAssignmentAfterSectionMerge(
        $publishedCourseIds = [],
        $newSectionId = null
    ) {

        if (empty($publishedCourseIds) || !$newSectionId) {
            return;
        }

        $this->getConnection()->update(
            'published_courses',
            ['section_id' => $newSectionId],
            ['id IN' => $publishedCourseIds]
        );
        $this->getConnection()->update(
            'course_registrations',
            ['section_id' => $newSectionId],
            ['published_course_id IN' => $publishedCourseIds]
        );
        $this->getConnection()->update(
            'course_instructor_assignments',
            ['section_id' => $newSectionId],
            ['published_course_id IN' => $publishedCourseIds]
        );
    }

    /**
     * Retrieves published courses for section merge.
     *
     * @param int|null $newSectionId The new section ID.
     * @param array|null $selectedSectionIds Array of section IDs to merge.
     * @param string|null $currentAcademicYear The academic year.
     * @return array|int Course data or status code (2).
     */
    public function getCourseForPublishedForSectionMerge(
        $newSectionId = null,
        $selectedSectionIds = null,
        $currentAcademicYear = null
    ) {

        if (!$newSectionId || empty($selectedSectionIds) || !$currentAcademicYear) {
            return 2;
        }

        $publishedCourses = [];
        foreach ($selectedSectionIds as $sectionId) {
            $courses = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find()
                ->where([
                    'academic_year LIKE' => $currentAcademicYear . '%',
                    'section_id' => $sectionId
                ])
                ->toArray();

            if ($courses) {
                $publishedCourses[$sectionId] = $courses;
            }
        }

        if (empty($publishedCourses)) {
            return 2;
        }

        $publishedCourseIdsNotGradeSubmitted = [];
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        foreach ($publishedCourses as $sectionId => $courses) {
            foreach ($courses as $course) {
                if (!$examGradesTable->isGradeSubmitted($course->id)) {
                    $publishedCourseIdsNotGradeSubmitted[$sectionId][$course->id] = $course->course_id;
                }
            }
        }

        $numberOfCourseSimilar = [];
        if ($publishedCourseIdsNotGradeSubmitted) {
            foreach ($selectedSectionIds as $sectionId) {
                if (!empty($publishedCourseIdsNotGradeSubmitted[$sectionId])) {
                    foreach ($publishedCourseIdsNotGradeSubmitted[$sectionId] as $pubCourseId => $courseId) {
                        foreach ($publishedCourseIdsNotGradeSubmitted as $otherSectionId => $otherCourses) {
                            if ($otherSectionId != $sectionId) {
                                foreach ($otherCourses as $otherPubCourseId => $otherCourseId) {
                                    if ($otherCourseId == $courseId) {
                                        $numberOfCourseSimilar[$sectionId][$otherSectionId] = $courseId;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $numberOfCourseSimilar;
    }

    /**
     * Retrieves formatted grade scale list for a published course.
     *
     * @param int|null $publishedCourseId The published course ID.
     * @return array Formatted grade scales.
     */
    public function getPublishedCourseGradeScaleList($publishedCourseId = null)
    {

        if (!$publishedCourseId) {
            return [];
        }

        $gradeScale = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->getGradeScaleDetail($publishedCourseId);

        $gradeScalesFormatted = [];
        if (!empty($gradeScale['GradeScaleDetail'])) {
            foreach ($gradeScale['GradeScaleDetail'] as $detail) {
                $gradeScalesFormatted[$detail['grade']] = sprintf(
                    '%s (%s - %s)',
                    $detail['grade'],
                    $detail['minimum_result'],
                    $detail['maximum_result']
                );
            }
        }

        return $gradeScalesFormatted;
    }

    /**
     * Retrieves grade history for a course registration or course add.
     *
     * @param int|null $courseRegistrationId The course registration or course add ID.
     * @param int $reg 1 for registration, 0 for course add.
     * @return array Grade history.
     */
    public function getCourseRegistrationGradeHistory($courseRegistrationId = null, $reg = 1)
    {

        if (!$courseRegistrationId) {
            return [];
        }

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        $conditions = $reg ? ['course_registration_id' => $courseRegistrationId] : ['course_add_id' => $courseRegistrationId];

        $contain = $reg ? [
            'CourseRegistrations' => [
                'ResultEntryAssignments' => function ($q) {
                    return $q->order(['ResultEntryAssignments.id' => 'DESC']);
                }
            ]
        ] : [
            'CourseAdds' => [
                'ResultEntryAssignments' => function ($q) {
                    return $q->order(['ResultEntryAssignments.id' => 'DESC']);
                }
            ]
        ];

        $gradeHistoryRows = $examGradesTable->find()
            ->where($conditions)
            ->order(['ExamGrades.id' => 'DESC'])
            ->contain(array_merge($contain, [
                'ExamGradeChanges' => function ($q) {
                    return $q->order(['ExamGradeChanges.id' => 'ASC']);
                }
            ]))
            ->toArray();

        $gradeHistory = [];
        $count = 0;
        $gradeHistory[$count]['type'] = $reg ? 'Register' : 'Add';
        $gradeHistory[$count]['result'] = TableRegistry::getTableLocator()->get('ExamResults')
            ->ExamTypes
            ->getAssessmentDetailType($courseRegistrationId, $reg ? 1 : 0);

        if (count($gradeHistoryRows) > 1) {
            $skipFirst = false;
            foreach ($gradeHistoryRows as $rejectedGrade) {
                if (!$skipFirst) {
                    $skipFirst = true;
                    continue;
                }
                $userDeptApproved = $usersTable->find()
                    ->select(['first_name','middle_name','last_name'])
                    ->where(['Users.id' => $rejectedGrade->exam_grade->department_approved_by])
                    ->enableHydration(true) // Ensure entity is returned to access virtual field
                    ->first();

                $fullNameD = $userDeptApproved ? $userDeptApproved->full_name : 'N/A';

                $rejectedGrade->exam_grade->department_approved_by_name = $fullNameD;

                $userRegApproved = $usersTable->find()
                    ->select(['first_name','middle_name','last_name'])
                    ->where(['Users.id' => $rejectedGrade->exam_grade->registrar_approved_by])
                    ->enableHydration(true) // Ensure entity is returned to access virtual field
                    ->first();

                $fullNameR = $userRegApproved ? $userRegApproved->full_name : 'N/A';
                $rejectedGrade->exam_grade->registrar_approved_by_name =$fullNameR;

                if (!empty($rejectedGrade->exam_grade)) {
                    $gradeHistory[$count]['rejected'][] = $rejectedGrade->exam_grade->toArray();
                } else {
                    // Optionally log or handle the case where exam_grade is null
                    $gradeHistory[$count]['rejected'][] = []; // Or skip, depending on requirements
                }

            }
        } else {
            $gradeHistory[$count]['rejected'] = [];
        }

        $gradeHistory[$count]['ExamGrade'] = !empty($gradeHistoryRows[0]->exam_grade) ? $gradeHistoryRows[0]->exam_grade->toArray(
        ) : [];
        $gradeHistory[$count]['ResultEntryAssignment'] = !empty($gradeHistoryRows[0]->{$reg ? 'course_registration' : 'course_add'}->result_entry_assignments[0])
            ? $gradeHistoryRows[0]->{$reg ? 'course_registration' : 'course_add'}->result_entry_assignments[0]->toArray(
            )
            : null;
        if ($gradeHistory[$count]['ResultEntryAssignment']) {
            $gradeHistory[$count]['result'] = $gradeHistory[$count]['ResultEntryAssignment']['result'];
        }

        if (!empty($gradeHistoryRows[0]->exam_grade_changes)) {
            foreach ($gradeHistoryRows[0]->exam_grade_changes as $examGradeChange) {
                $count++;
                $gradeHistory[$count]['type'] = 'Change';

                $examGradeChange->department_approved_by_name = $examGradeChange->department_approved_by
                    ? ($usersTable->find()
                        ->select(['first_name','middle_name','last_name'])
                        ->where(['Users.id' => $examGradeChange->department_approved_by])
                        ->enableHydration(true)
                        ->first()->full_name ?? 'N/A')
                    : 'N/A';

                $examGradeChange->college_approved_by_name = $examGradeChange->college_approved_by
                    ? ($usersTable->find()
                        ->select(['first_name','middle_name','last_name'])
                        ->where(['Users.id' => $examGradeChange->college_approved_by])
                        ->enableHydration(true)
                        ->first()->full_name ?? 'N/A')
                    : 'N/A';

                $examGradeChange->registrar_approved_by_name = $examGradeChange->registrar_approved_by
                    ? ($usersTable->find()
                        ->select(['first_name','middle_name','last_name'])
                        ->where(['Users.id' => $examGradeChange->registrar_approved_by])
                        ->enableHydration(true)
                        ->first()->full_name ?? 'N/A')
                    : 'N/A';

                $examGradeChange->grade_scale_id = $examGradeChange->exam_grade_id
                    ? ($examGradesTable->find()
                        ->select(['grade_scale_id'])
                        ->where(['ExamGrades.id' => $examGradeChange->exam_grade_id])
                        ->first()->grade_scale_id ?? null)
                    : null;

                $examGradeChange->manual_ng_converted_by_name = $examGradeChange->manual_ng_converted_by
                    ? ($usersTable->find()
                        ->select(['first_name','middle_name','last_name'])
                        ->where(['Users.id' => $examGradeChange->manual_ng_converted_by])
                        ->enableHydration(true)
                        ->first()->full_name ?? 'N/A')
                    : 'N/A';

                if (!empty($examGradeChange)) {
                    $gradeHistory[$count]['ExamGrade'] = $examGradeChange->toArray();
                } else {
                    // Optionally log or handle the case where exam_grade is null
                    $gradeHistory[$count]['ExamGrade'] = [];
                }


            }
        }

        return $gradeHistory;
    }

    /**
     * Gets the status of an exam grade change.
     *
     * @param array|null $examGradeChange The exam grade change data.
     * @param string $type The status type ('simple' or other).
     * @return string Status ('accepted', 'rejected', 'on-process').
     */
    public function getExamGradeChangeStatus($examGradeChange = null, $type = 'simple')
    {

        if (!is_array($examGradeChange) || empty($examGradeChange)) {
            return 'on-process';
        }

        if ($examGradeChange['manual_ng_conversion'] == 1 || $examGradeChange['auto_ng_conversion'] == 1) {
            return 'accepted';
        }

        if ($examGradeChange['initiated_by_department'] == 1 || $examGradeChange['department_approval'] == 1) {
            if ($examGradeChange['college_approval'] == 1 || $examGradeChange['makeup_exam_result'] !== null) {
                if ($examGradeChange['registrar_approval'] == 1) {
                    return 'accepted';
                }
                if ($examGradeChange['registrar_approval'] == -1) {
                    return 'rejected';
                }
                return 'on-process';
            }
            if ($examGradeChange['college_approval'] == -1) {
                return 'rejected';
            }
            return 'on-process';
        }

        if ($examGradeChange['department_approval'] == -1) {
            return 'rejected';
        }

        return 'on-process';
    }

    /**
     * Gets the status of an exam grade.
     *
     * @param array|null $examGrade The exam grade data.
     * @param string $type The status type ('simple' or other).
     * @return string Status ('accepted', 'rejected', 'on-process').
     */
    public function getExamGradeStatus($examGrade = null, $type = 'simple')
    {

        if (!is_array($examGrade) || empty($examGrade)) {
            return 'on-process';
        }

        if ($examGrade['department_approval'] == 1) {
            if ($examGrade['registrar_approval'] == 1) {
                return 'accepted';
            }
            if ($examGrade['registrar_approval'] == -1) {
                return 'rejected';
            }
            return 'on-process';
        }

        if ($examGrade['department_approval'] == -1) {
            return 'rejected';
        }

        return 'on-process';
    }

    /**
     * Checks if any grade is on process for a course registration.
     *
     * @param int|null $courseRegistrationId The course registration ID.
     * @return bool True if a grade is on process, false otherwise.
     */
    public function isAnyGradeOnProcess($courseRegistrationId = null)
    {

        if (!$courseRegistrationId) {
            return false;
        }

        $gradeHistories = $this->getCourseRegistrationGradeHistory($courseRegistrationId);

        foreach ($gradeHistories as $gradeHistory) {
            if (
                (strcasecmp($gradeHistory['type'], 'Register') === 0 && strcasecmp(
                        $this->getExamGradeStatus($gradeHistory['ExamGrade']),
                        'on-process'
                    ) === 0) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 && strcasecmp(
                        $this->getExamGradeChangeStatus($gradeHistory['ExamGrade']),
                        'on-process'
                    ) === 0)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the latest grade for a course registration.
     *
     * @param int|null $courseRegistrationId The course registration ID.
     * @return string The latest grade or empty string.
     */
    public function getCourseRegistrationLatestGrade($courseRegistrationId = null)
    {

        if (!$courseRegistrationId) {
            return '';
        }

        $gradeHistories = $this->getCourseRegistrationGradeHistory($courseRegistrationId);
        $latestGrade = '';

        foreach ($gradeHistories as $gradeHistory) {
            if (
                !empty($gradeHistory['ExamGrade']) &&
                isset($gradeHistory['ExamGrade']['grade']) && // Check for grade existence
                $gradeHistory['ExamGrade']['grade'] !== $latestGrade &&
                (
                    $gradeHistory['type'] !== 'Change' ||
                    (
                        (isset($gradeHistory['ExamGrade']['department_approval']) && $gradeHistory['ExamGrade']['department_approval'] == 1 ||
                            isset($gradeHistory['ExamGrade']['initiated_by_department']) && $gradeHistory['ExamGrade']['initiated_by_department'] == 1) &&
                        isset($gradeHistory['ExamGrade']['registrar_approval']) && $gradeHistory['ExamGrade']['registrar_approval'] == 1 &&
                        isset($gradeHistory['ExamGrade']['college_approval']) && $gradeHistory['ExamGrade']['college_approval'] == 1
                    ) ||
                    (
                        isset($gradeHistory['ExamGrade']['makeup_exam_result']) && $gradeHistory['ExamGrade']['makeup_exam_result'] !== null &&
                        (isset($gradeHistory['ExamGrade']['department_approval']) && $gradeHistory['ExamGrade']['department_approval'] == 1 ||
                            isset($gradeHistory['ExamGrade']['initiated_by_department']) && $gradeHistory['ExamGrade']['initiated_by_department'] == 1) &&
                        isset($gradeHistory['ExamGrade']['registrar_approval']) && $gradeHistory['ExamGrade']['registrar_approval'] == 1
                    )
                )
            ) {
                $latestGrade = $gradeHistory['ExamGrade']['grade'];
            }
        }

        return $latestGrade;
    }

    /**
     * Retrieves the latest grade detail for a course registration or course add.
     *
     * @param int|null $courseRegistrationId The course registration or course add ID.
     * @param int $reg 1 for registration, 0 for course add.
     * @return array The latest grade detail.
     */
    public function getCourseRegistrationLatestGradeDetail($courseRegistrationId = null, $reg = 1)
    {

        if (!$courseRegistrationId) {
            return [];
        }

        $gradeHistories = $this->getCourseRegistrationGradeHistory($courseRegistrationId, $reg);
        $latestGradeDetail = [];

        foreach ($gradeHistories as $gradeHistory) {
            if (
                strcasecmp($gradeHistory['type'], 'Register') === 0 ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    $gradeHistory['ExamGrade']['makeup_exam_result'] === null &&
                    $gradeHistory['ExamGrade']['department_approval'] != -1 &&
                    $gradeHistory['ExamGrade']['college_approval'] != -1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] != -1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    $gradeHistory['ExamGrade']['makeup_exam_result'] !== null &&
                    $gradeHistory['ExamGrade']['initiated_by_department'] == 0 &&
                    $gradeHistory['ExamGrade']['department_approval'] != -1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] != -1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    $gradeHistory['ExamGrade']['makeup_exam_result'] !== null &&
                    $gradeHistory['ExamGrade']['initiated_by_department'] == 1 &&
                    $gradeHistory['ExamGrade']['department_approval'] != -1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] != -1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    ($gradeHistory['ExamGrade']['auto_ng_conversion'] == 1 || $gradeHistory['ExamGrade']['manual_ng_conversion'] == 1))
            ) {
                $latestGradeDetail = $gradeHistory;
                unset($latestGradeDetail['rejected']);
            }
        }

        return $latestGradeDetail;
    }

    /**
     * Retrieves the latest approved grade detail for a course registration.
     *
     * @param int|null $courseRegistrationId The course registration ID.
     * @return array The latest approved grade detail.
     */
    public function getCourseRegistrationLatestApprovedGradeDetail($courseRegistrationId = null)
    {

        if (!$courseRegistrationId) {
            return [];
        }

        $gradeHistories = $this->getCourseRegistrationGradeHistory($courseRegistrationId);
        $latestGradeDetail = [];

        foreach ($gradeHistories as $gradeHistory) {
            if (
                (strcasecmp($gradeHistory['type'], 'Register') === 0 &&
                    !empty($gradeHistory['ExamGrade']) &&
                    $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] == 1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    $gradeHistory['ExamGrade']['makeup_exam_result'] === null &&
                    $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                    $gradeHistory['ExamGrade']['college_approval'] == 1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] == 1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    $gradeHistory['ExamGrade']['makeup_exam_result'] !== null &&
                    $gradeHistory['ExamGrade']['initiated_by_department'] == 0 &&
                    $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] == 1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    $gradeHistory['ExamGrade']['makeup_exam_result'] !== null &&
                    $gradeHistory['ExamGrade']['initiated_by_department'] == 1 &&
                    $gradeHistory['ExamGrade']['department_approval'] == 1 &&
                    $gradeHistory['ExamGrade']['registrar_approval'] == 1) ||
                (strcasecmp($gradeHistory['type'], 'Change') === 0 &&
                    ($gradeHistory['ExamGrade']['manual_ng_conversion'] == 1 ||
                        (!empty($gradeHistory['ExamGrade']['auto_ng_conversion']) &&
                            $gradeHistory['ExamGrade']['auto_ng_conversion'])))
            ) {
                if (!empty($latestGradeDetail) && $gradeHistory['ExamGrade']['created'] >
                    $latestGradeDetail['ExamGrade']['created']) {
                    $latestGradeDetail = $gradeHistory;
                } elseif (empty($latestGradeDetail)) {
                    $latestGradeDetail = $gradeHistory;
                }
                unset($latestGradeDetail['rejected']);
            }
        }

        return $latestGradeDetail;
    }

    /**
     * Checks if a course registration is dropped.
     *
     * @param int|null $courseRegistrationId The course registration ID.
     * @return bool True if dropped, false otherwise.
     */
    public function isCourseDropped($courseRegistrationId = null)
    {

        if (!$courseRegistrationId) {
            return false;
        }



        $courseRegistration = $this->find()
            ->where(['CourseRegistrations.id' => $courseRegistrationId])
            ->contain(['PublishedCourses', 'CourseDrops'])
            ->first();
        if (!$courseRegistration) {
            return false;
        }

        if (!empty($courseRegistration->course_drops)) {
            $drop = $courseRegistration->course_drops[0];
            if ($drop->forced || ($drop->department_status == 1 && $drop->registrar_status == 1)) {
                return true;
            }
        }

        return $courseRegistration->published_course->drop == 1;
    }

    /**
     * Retrieves course registrations for a student.
     *
     * @param int|null $studentId The student ID.
     * @param array $ayAndSList List of academic year and semester pairs.
     * @param int|null $courseId The course ID.
     * @param int $includeEquivalent Whether to include equivalent courses (1 or 0).
     * @param int $excludeDrop Whether to exclude dropped courses (1 or 0).
     * @return array List of course registrations.
     */
    public function getCourseRegistrations(
        $studentId = null,
        $ayAndSList = [],
        $courseId = null,
        $includeEquivalent = 1,
        $excludeDrop = 1
    ) {

        if (!$studentId || !$courseId) {
            return [];
        }

        $conditions = ['student_id' => $studentId];
        if (!empty($ayAndSList)) {
            $orConditions = [];
            foreach ($ayAndSList as $ayAndS) {
                $orConditions[] = [
                    'academic_year' => $ayAndS['academic_year'],
                    'semester' => $ayAndS['semester']
                ];
            }
            $conditions['OR'] = $orConditions;
        }

        $matchingCourses = [$courseId];
        if ($includeEquivalent) {
            $student = $this->Students->find()
                ->select(['department_id', 'curriculum_id'])
                ->where(['id' => $studentId])
                ->first();

            $course = TableRegistry::getTableLocator()->get('Courses')
                ->find()
                ->where(['id' => $courseId])
                ->contain(['Curriculums'])
                ->first();

            if ($student && $student->department_id && $course && $course->curriculum) {
                if ($student->department_id == $course->curriculum->department_id && $student->curriculum_id == $course->id) {
                    $matchingCourses = [$courseId];
                } else {
                    $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')
                        ->validEquivalentCourse($courseId, $student->curriculum_id);
                }
            }
        }

        $courseRegistrationsRaw = $this->find()
            ->where($conditions)
            ->order(['created' => 'DESC'])
            ->contain(['PublishedCourses' => ['Courses']])
            ->toArray();

        $courseRegistrations = [];
        foreach ($courseRegistrationsRaw as $registration) {
            if (in_array($registration->published_course->course->id, $matchingCourses)) {
                if (!$excludeDrop || ($excludeDrop && !$this->isCourseDropped($registration->id))) {
                    $courseRegistrations[] = $registration;
                }
            }
        }

        return $courseRegistrations;
    }

    /**
     * Filters published courses based on prerequisites.
     *
     * @param array|null $publishedCourses List of published courses.
     * @param int|null $studentId The student ID.
     * @return array Filtered courses with prerequisite status.
     */
    public function notAllowFailedPrerequisite($publishedCourses = null, $studentId = null)
    {

        $courseRegisterReformat = [];
        $count = 0;

        if (empty($publishedCourses) || !$studentId) {
            return $courseRegisterReformat;
        }

        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');

        foreach ($publishedCourses as $course) {
            if (!empty($course['Course']['Prerequisites'])) {
                $passedCount = 0;
                foreach ($course['Course']['Prerequisites'] as $prerequisite) {
                    if ($courseDropsTable->prerequisiteTaken($studentId, $prerequisite['prerequisite_course_id'])) {
                        $passedCount++;
                    }
                }

                $courseRegisterReformat[$count] = $course;
                $courseRegisterReformat[$count]['prerequisite_taken_passed'] = ($passedCount == count(
                        $course['Course']['Prerequisites']
                    )) ? 1 : 0;
            } else {
                $courseRegisterReformat[$count] = $course;
                $courseRegisterReformat[$count]['prerequisite_taken_passed'] = 1;
            }
            $count++;
        }

        return $courseRegisterReformat;
    }

    /**
     * Determines registration type for published courses.
     *
     * @param array|null $publishedCourses List of published courses.
     * @param int|null $studentId The student ID.
     * @param mixed $status The student status.
     * @return array Courses with registration type.
     */
    public function getRegistrationType($publishedCourses = null, $studentId = null, $status = null)
    {

        $courseRegisterReformat = [];
        $count = 0;
        $readyRegisteredCourseIds = [];

        if (empty($publishedCourses) || !$studentId) {
            return $courseRegisterReformat;
        }

        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');

        foreach ($publishedCourses as $course) {
            $readyRegisteredCourseIds[] = $course['Course']['id'];
        }

        foreach ($publishedCourses as $course) {
            if ($courseExemptionsTable->isCourseExempted($studentId, $course['Course']['id']) > 0) {
                $courseRegisterReformat[$count] = $course;
                $courseRegisterReformat[$count]['exemption'] = 1;
                $count++;
                continue;
            }

            if (!empty($course['Course']['Prerequisites'])) {
                $passedCount = ['passed' => 0, 'onhold' => 0];
                foreach ($course['Course']['Prerequisites'] as $prerequisite) {
                    if ($prerequisite['co_requisite'] == 1) {
                        if (in_array($prerequisite['prerequisite_course_id'], $readyRegisteredCourseIds)) {
                            $passedCount['passed']++;
                        } else {
                            $prePassed = $courseDropsTable->prerequisiteTaken(
                                $studentId,
                                $prerequisite['prerequisite_course_id']
                            );
                            if ($prePassed === true) {
                                $passedCount['passed']++;
                            } elseif ($prePassed == 2) {
                                $passedCount['onhold']++;
                            }
                        }
                    } else {
                        $prePassed = $courseDropsTable->prerequisiteTaken(
                            $studentId,
                            $prerequisite['prerequisite_course_id']
                        );
                        if ($prePassed === true) {
                            $passedCount['passed']++;
                        } elseif ($prePassed == 2) {
                            $passedCount['onhold']++;
                        }
                    }
                }

                if ($passedCount['passed'] == count($course['Course']['Prerequisites'])) {
                    $courseRegisterReformat[$count]['prerequisite_taken_passed'] = 1;
                } elseif ($passedCount['onhold'] == count($course['Course']['Prerequisites'])) {
                    $courseRegisterReformat[$count]['prerequisite_taken_passed'] = 2;
                } else {
                    $courseRegisterReformat[$count]['prerequisite_taken_passed'] = 0;
                }
                $courseRegisterReformat[$count] = $course;
            } else {
                $courseRegisterReformat[$count] = $course;
            }

            if ($status == 2) {
                $courseRegisterReformat[$count]['registration_type'] = 2;
            } elseif ($status == 1) {
                $courseRegisterReformat[$count]['registration_type'] = 1;
            } elseif ($status == 0) {
                $courseRegisterReformat[$count]['registration_type'] = 0;
            }

            $count++;
        }

        return $courseRegisterReformat;
    }

    /**
     * Registers a single student for courses.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @param int $excludeElective Whether to exclude elective courses (1 or 0).
     * @return array Registration result.
     */
    public function registerSingleStudent(
        $studentId = null,
        $academicYear = null,
        $semester = null,
        $excludeElective = 0
    ) {

        $publishedCoursesResult = ['passed' => false, 'register' => []];

        if (!$studentId || !$academicYear) {
            return $publishedCoursesResult;
        }

        $passedOrFailed = TableRegistry::getTableLocator()->get('StudentExamStatuses')
            ->getStudentExamStatus($studentId, $academicYear);

        $latestSemester = $semester ?? $this->getLatestStudentSemesterAndAcademicYear(
            $studentId,
            $academicYear
        )['semester'];
        $studentStatus = TableRegistry::getTableLocator()->get('StudentExamStatuses')
            ->getStudentAcademicStatus($studentId, $academicYear, $latestSemester);

        $studentSection = TableRegistry::getTableLocator()->get('Students')
            ->studentAcademicDetail($studentId, $academicYear);

        if (empty($studentSection) || empty($studentSection['Sections'])) {
            return $publishedCoursesResult;
        }

        $section = $studentSection['Sections'][0];
        if ($section['academic_year'] != $academicYear) {
            return $publishedCoursesResult;
        }

        $conditions = [
            'drop' => 0,
            'add' => 0,
            'published' => 1,
            'academic_year LIKE' => $academicYear . '%',
            'semester' => $latestSemester,
            'section_id' => $section->id
        ];

        if (empty($studentSection['Student']['department_id'])) {
            $conditions['department_id IS'] = null;
            $conditions['college_id'] = $studentSection['Student']['college_id'];
            $conditions['OR'] = [
                'year_level_id IS' => null,
                'year_level_id' => 0,
                'year_level_id' => ''
            ];
        } else {
            $conditions['department_id'] = $studentSection['Student']['department_id'];
            $conditions['year_level_id'] = $section->year_level_id;
        }

        if ($excludeElective) {
            $conditions['elective'] = 0;
        }

        $publishedCourses = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find()
            ->where($conditions)
            ->contain([
                'Courses' => [
                    'Prerequisites' => ['fields' => ['id', 'prerequisite_course_id', 'co_requisite']],
                    'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                    'fields' => [
                        'id',
                        'course_code',
                        'course_title',
                        'lecture_hours',
                        'tutorial_hours',
                        'laboratory_hours',
                        'credit'
                    ]
                ],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name', 'type']],
                'Colleges' => ['fields' => ['id', 'name', 'type']],
                'Sections' => [
                    'fields' => ['id', 'name', 'academic_year', 'archive'],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'type']],
                    'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                ],
                'YearLevels' => ['fields' => ['id', 'name']]
            ])
            ->toArray();

        $publishedCoursesResult['passed'] = $passedOrFailed;
        $publishedCoursesResult['register'] = $this->getRegistrationType($publishedCourses, $studentId, $studentStatus);

        return $publishedCoursesResult;
    }

    /**
     * Retrieves the latest semester and academic year for a student.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $currentAcademicYear The academic year.
     * @param int $addDrop Whether to consider add/drop (1 or 0).
     * @return array Latest semester and academic year.
     */
    public function getLatestStudentSemesterAndAcademicYear(
        $studentId = null,
        $currentAcademicYear = null,
        $addDrop = 0
    ) {

        $latestSemesterAcademicYear = ['semester' => 'I', 'academic_year' => $currentAcademicYear];

        if (!$studentId || !$currentAcademicYear) {
            return $latestSemesterAcademicYear;
        }

        $ayAndSList = TableRegistry::getTableLocator()->get('ExamGrades')
            ->getListOfAyAndSemester($studentId);

        if (empty($ayAndSList)) {
            return $latestSemesterAcademicYear;
        }

        $lastEnd = end($ayAndSList);
        if (strcasecmp($currentAcademicYear, $lastEnd['academic_year']) === 0) {
            $latestSemesterAcademicYear['semester'] = $addDrop ? $lastEnd['semester'] : TableRegistry::getTableLocator(
            )->get('StudentExamStatuses')
                ->getNextSemester($currentAcademicYear, $lastEnd['semester'])['semester'];
            $latestSemesterAcademicYear['academic_year'] = $currentAcademicYear;
        } elseif (strcasecmp($currentAcademicYear, $lastEnd['academic_year']) > 0) {
            $student = $this->Students->find()
                ->select(['program_id', 'program_type_id'])
                ->where(['id' => $studentId])
                ->first();

            $latestSemesterAcademicYear = $addDrop
                ? ['semester' => $lastEnd['semester'], 'academic_year' => $lastEnd['academic_year']]
                : $this->latestAcademicYearSemester(
                    $currentAcademicYear,
                    $student->program_id,
                    $student->program_type_id
                );
        }

        return $latestSemesterAcademicYear;
    }

    /**
     * Mass-registers students in a section.
     *
     * @param int|null $sectionId The section ID.
     * @param array $academicYear Array with 'academic_year' and 'semester'.
     * @return int Status code (1: success, 2: failure, 3: partial success).
     */
    public function massRegisterStudent($sectionId = null, $academicYear = null)
    {

        if (!$sectionId || empty($academicYear)) {
            return 2;
        }

        $academicYearData = [];
        if (!empty($academicYear['academicyear'])) {
            $academicYearData['academic_year'] = $academicYear['academicyear'];
            $academicYearData['semester'] = $academicYear['semester'];
        } elseif (!empty($academicYear['academic_year'])) {
            $academicYearData['academic_year'] = $academicYear['academic_year'];
            $academicYearData['semester'] = $academicYear['semester'];
        } else {
            return 2;
        }

        $publishedCourses = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find()
            ->where([
                'section_id' => $sectionId,
                'drop' => 0,
                'add' => 0,
                'academic_year' => $academicYearData['academic_year'],
                'semester' => $academicYearData['semester'],
                'elective' => 0,
                'published' => 1
            ])
            ->contain([
                'Courses' => [
                    'Prerequisites' => ['fields' => ['id', 'prerequisite_course_id', 'co_requisite']],
                    'fields' => ['id', 'course_code', 'course_title', 'lecture_hours', 'tutorial_hours', 'credit']
                ]
            ])
            ->toArray();

        $studentsList = $this->Sections->getSectionActiveStudentsId($sectionId);
        $validCourseRegistrationLists = [];

        if (!empty($studentsList) && !empty($publishedCourses)) {
            foreach ($studentsList as $studentId) {
                if (!$this->alreadyRegistered(
                    $studentId,
                    $academicYearData['academic_year'],
                    $academicYearData['semester']
                )) {
                    $studentStatus = TableRegistry::getTableLocator()->get('StudentExamStatuses')
                        ->getStudentExamStatus($studentId, $academicYearData['academic_year']);
                    if (in_array($studentStatus, [1, 3])) {
                        $validCourseRegistrationLists[$studentId] = [
                            'register' => $this->getRegistrationType($publishedCourses, $studentId, $studentStatus),
                            'passed' => $studentStatus
                        ];
                    }
                }
            }
        }

        $formattedSaveAllRegistration = [];
        $count = 0;
        foreach ($validCourseRegistrationLists as $studentId => $value) {
            foreach ($value['register'] as $publishedValue) {
                if (
                    (!isset($publishedValue['prerequisite_taken_passed']) && !isset($publishedValue['exemption'])) ||
                    (isset($publishedValue['prerequisite_taken_passed']) && $publishedValue['prerequisite_taken_passed'] == 1)
                ) {
                    $formattedSaveAllRegistration['CourseRegistration'][$count] = [
                        'published_course_id' => $publishedValue['PublishedCourse']['id'],
                        'course_id' => $publishedValue['PublishedCourse']['course_id'],
                        'semester' => $publishedValue['PublishedCourse']['semester'],
                        'academic_year' => $publishedValue['PublishedCourse']['academic_year'],
                        'student_id' => $studentId,
                        'section_id' => $publishedValue['PublishedCourse']['section_id'],
                        'year_level_id' => $publishedValue['PublishedCourse']['year_level_id']
                    ];

                    if (isset($publishedValue['registration_type']) && $publishedValue['registration_type'] == 2 && !isset($publishedValue['exemption'])) {
                        $formattedSaveAllRegistration['CourseRegistration'][$count]['type'] = 11;
                    } elseif (isset($publishedValue['prerequisite_taken_passed']) && $publishedValue['prerequisite_taken_passed'] == 2 && !isset($publishedValue['exemption'])) {
                        $formattedSaveAllRegistration['CourseRegistration'][$count]['type'] = 11;
                    } elseif (
                        (isset($publishedValue['registration_type']) && $publishedValue['registration_type'] == 2) &&
                        (isset($publishedValue['prerequisite_taken_passed']) && $publishedValue['prerequisite_taken_passed'] == 2) &&
                        !isset($publishedValue['exemption'])
                    ) {
                        $formattedSaveAllRegistration['CourseRegistration'][$count]['type'] = 13;
                    }

                    $count++;
                }
            }
        }

        if (!empty($formattedSaveAllRegistration['CourseRegistration'])) {
            $entities = $this->newEntities($formattedSaveAllRegistration['CourseRegistration']);
            if ($this->saveMany($entities, ['validate' => false])) {
                return 1;
            }
            return 2;
        }

        return empty($formattedSaveAllRegistration['CourseRegistration']) ? 2 : 3;
    }

    /**
     * Checks if a student is registered for a specific published course.
     *
     * @param int|null $publishedCourseId The published course ID.
     * @param string|null $semester The semester.
     * @param string|null $academicYear The academic year.
     * @param int|null $studentId The student ID.
     * @return int Number of registrations.
     */
    public function courseRegistered(
        $publishedCourseId = null,
        $semester = null,
        $academicYear = null,
        $studentId = null
    ) {

        if (!$publishedCourseId || !$semester || !$academicYear || !$studentId) {
            return 0;
        }

        return $this->find()
            ->where([
                'academic_year' => $academicYear,
                'student_id' => $studentId,
                'published_course_id' => $publishedCourseId,
                'semester' => $semester
            ])
            ->count();
    }

    /**
     * Retrieves registration statistics.
     *
     * @param string $academicYear The academic year.
     * @param string $semester The semester.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param int|null $departmentId The department ID.
     * @param array|null $type Filter types (registered, dismissed).
     * @return array Statistics with attraction rates and year levels.
     */
    public function getRegistrationStats($academicYear, $semester, $programId = null, $programTypeId = null, $departmentId = null, $type = null)
    {
        if (!$academicYear || !$semester) {
            return ['attractionRate' => [], 'YearLevel' => []];
        }

        $conditions = ['Students.graduated' => 0];

        if ($departmentId) {
            $collegeIdParts = explode('~', $departmentId);
            if (count($collegeIdParts) > 1) {
                $conditions['Students.college_id'] = $collegeIdParts[1];
            } else {
                $conditions['Students.department_id'] = $departmentId;
            }
        }

        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        $students = $this->Students->find()
            ->select([
                'Students.id', 'Students.full_name', 'Students.first_name', 'Students.middle_name',
                'Students.last_name', 'Students.studentnumber', 'Students.admissionyear', 'Students.gender'
            ])
            ->where($conditions)
            ->contain([
                'StudentExamStatuses' => [
                    'conditions' => ['semester' => $semester, 'academic_year' => $academicYear],
                    'limit' => 1,
                    'order' => ['created' => 'DESC']
                ],
                'CourseRegistrations' => [
                    'conditions' => ['semester' => $semester, 'academic_year' => $academicYear],
                    'order' => ['created' => 'DESC'],
                    'fields' => ['academic_year', 'semester'],
                    'Sections' => [
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']]
                    ]
                ]
            ])
            ->order(['Students.first_name' => 'ASC', 'Students.middle_name' => 'ASC', 'Students.last_name' => 'ASC'])
            ->toArray();

        $attractionRate = [];
        $yearLevelCount = [];
        $totalStudent = count($students);

        foreach ($students as $student) {
            $section = !empty($student->course_registrations) ? $student->course_registrations[0]->section : null;
            if (!$section || empty($section->program->name) || empty($section->program_type->name)) {
                continue;
            }

            $yearLevel = $section->year_level->name ?? '1st';
            $yearLevelCount[$yearLevel] = $yearLevel;

            $programName = $section->program->name;
            $programTypeName = $section->program_type->name;
            $collegeName = $section->college->name;
            $departmentName = $section->department->name ?? 'Pre Engineering';

            // Registered Students
            if (!empty($type['registered'])) {
                // Department Level
                if ($section->year_level->name && $section->department->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['male_total'] ?? 0;
                }

                // College Level
                if ($section->year_level->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['male_total'] ?? 0;
                }

                // University Level
                if ($section->year_level->name) {
                    $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['total'] =
                        ($attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['total'] ?? 0) + 1;
                    $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['female_total'] =
                        $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['female_total'] ?? 0;
                    $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['male_total'] =
                        $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['male_total'] ?? 0;
                }

                // Pre-Engineering Department
                if (!$section->year_level->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['male_total'] ?? 0;
                }

                // Pre-Engineering College
                if (!$section->year_level->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['male_total'] ?? 0;
                }

                // Pre-Engineering University
                if (!$section->year_level->name) {
                    $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['total'] =
                        ($attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['total'] ?? 0) + 1;
                    $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['female_total'] =
                        $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['female_total'] ?? 0;
                    $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['male_total'] =
                        $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['male_total'] ?? 0;
                }

                // Gender Breakdown
                if (strcasecmp($student->gender, 'female') === 0) {
                    if ($section->department->name) {
                        if ($section->year_level->name) {
                            $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['female_total']++;
                            $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['female_total']++;
                            $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['female_total']++;
                        }
                    } else {
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['female_total']++;
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['female_total']++;
                        $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['female_total']++;
                    }
                } elseif (strcasecmp($student->gender, 'male') === 0) {
                    if ($section->department->name) {
                        if ($section->year_level->name) {
                            $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Registered'][$yearLevel]['male_total']++;
                            $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered'][$yearLevel]['male_total']++;
                            $attractionRate['University']['Registered'][$programName][$programTypeName][$yearLevel]['male_total']++;
                        }
                    } else {
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Registered']['1st']['male_total']++;
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Registered']['1st']['male_total']++;
                        $attractionRate['University']['Registered'][$programName][$programTypeName]['1st']['male_total']++;
                    }
                }
            }

            // Dismissed Students
            if (!empty($type['dismissed']) && !empty($student->student_exam_statuses) && $student->student_exam_statuses[0]->academic_status_id == 4) {
                // Department Level
                if ($section->year_level->name && $section->department->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['male_total'] ?? 0;
                }

                // College Level
                if ($section->year_level->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['male_total'] ?? 0;
                }

                // University Level
                if ($section->year_level->name) {
                    $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['total'] =
                        ($attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['total'] ?? 0) + 1;
                    $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['female_total'] =
                        $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['female_total'] ?? 0;
                    $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['male_total'] =
                        $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['male_total'] ?? 0;
                }

                // Pre-Engineering Department
                if (!$section->year_level->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['male_total'] ?? 0;
                }

                // Pre-Engineering College
                if (!$section->year_level->name && $section->college->name) {
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['total'] =
                        ($attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['total'] ?? 0) + 1;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['female_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['female_total'] ?? 0;
                    $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['male_total'] =
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['male_total'] ?? 0;
                }

                // Pre-Engineering University
                if (!$section->year_level->name) {
                    $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['total'] =
                        ($attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['total'] ?? 0) + 1;
                    $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['female_total'] =
                        $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['female_total'] ?? 0;
                    $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['male_total'] =
                        $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['male_total'] ?? 0;
                }

                // Gender Breakdown
                if (strcasecmp($student->gender, 'female') === 0) {
                    if ($section->department->name) {
                        if ($section->year_level->name) {
                            $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['female_total']++;
                            $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['female_total']++;
                            $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['female_total']++;
                        }
                    } else {
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['female_total']++;
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['female_total']++;
                        $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['female_total']++;
                    }
                } elseif (strcasecmp($student->gender, 'male') === 0) {
                    if ($section->department->name) {
                        if ($section->year_level->name) {
                            $attractionRate[$programName][$programTypeName][$collegeName][$departmentName]['Dismissed'][$yearLevel]['male_total']++;
                            $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed'][$yearLevel]['male_total']++;
                            $attractionRate['University']['Dismissed'][$programName][$programTypeName][$yearLevel]['male_total']++;
                        }
                    } else {
                        $attractionRate[$programName][$programTypeName][$collegeName]['PreEngineering']['Dismissed']['1st']['male_total']++;
                        $attractionRate[$programName][$programTypeName][$collegeName]['College']['Dismissed']['1st']['male_total']++;
                        $attractionRate['University']['Dismissed'][$programName][$programTypeName]['1st']['male_total']++;
                    }
                }
            }
        }

        return ['attractionRate' => $attractionRate, 'YearLevel' => $yearLevelCount];
    }

    /**
     * Placeholder for formatted registration statistics.
     *
     * @return array Formatted registration stats (to be implemented).
     */
    public function getFormattedRegistrationStats()
    {
        // TODO: Implement formatted registration statistics
        return [];
    }

    /**
     * Placeholder for formatted dismissed statistics.
     *
     * @return array Formatted dismissed stats (to be implemented).
     */
    public function getFormattedDismissedStats()
    {
        // TODO: Implement formatted dismissed statistics
        return [];
    }

    /**
     * Placeholder for formatted dropout statistics.
     *
     * @return array Formatted dropout stats (to be implemented).
     */
    public function getFormattedDropOutStats()
    {
        // TODO: Implement formatted dropout statistics
        return [];
    }

    /**
     * Placeholder for formatted transferred statistics.
     *
     * @return array Formatted transferred stats (to be implemented).
     */
    public function getFormattedTransferred()
    {
        // TODO: Implement formatted transferred statistics
        return [];
    }

    /**
     * Checks if a course registration has a withdrawal grade.
     *
     * @param int|null $registrationId The course registration ID.
     * @return int Number of withdrawals.
     */
    public function doesTheCourseRegistrationHaveWithdraw($registrationId = null)
    {
        if (!$registrationId) {
            return 0;
        }

        return $this->find()
            ->where([
                'id' => $registrationId,
                'id IN' => $this->ExamGrades->find()
                    ->select(['course_registration_id'])
                    ->where([
                        'id IN' => $this->ExamGrades->ExamGradeChanges->find()
                            ->select(['exam_grade_id'])
                            ->where(['grade' => 'W'])
                    ])
            ])
            ->order(['created' => 'DESC'])
            ->count();
    }

    /**
     * Retrieves the most recent course registration for a student.
     *
     * @param int|null $studentId The student ID.
     * @return \Cake\ORM\Entity|null The course registration entity or null.
     */
    public function getMostRecentRegistration($studentId = null)
    {
        if (!$studentId) {
            return null;
        }

        return $this->find()
            ->where(['student_id' => $studentId])
            ->order(['created' => 'DESC'])
            ->contain([
                'Sections' => [
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'Colleges' => ['fields' => ['id', 'name']]
                ]
            ])
            ->first();
    }

    /**
     * Retrieves a specific course registration for a student.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @return \Cake\ORM\Entity|null The course registration entity or null.
     */
    public function getRegistration($studentId = null, $academicYear = null, $semester = null)
    {
        if (!$studentId || !$academicYear || !$semester) {
            return null;
        }

        return $this->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain([
                'Sections' => [
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'Colleges' => ['fields' => ['id', 'name']]
                ]
            ])
            ->order(['created' => 'DESC'])
            ->first();
    }

    /**
     * Determines a student’s year and semester level based on registrations.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @return array Year and semester level.
     */
    public function studentYearAndSemesterLevelByRegistration($studentId = null, $academicYear = null, $semester = null)
    {
        $statusLevel = ['year' => '1st', 'semester' => 'I'];

        if (!$studentId || !$academicYear || !$semester) {
            return $statusLevel;
        }

        $student = $this->Students->find()
            ->where(['id' => $studentId])
            ->contain(['AcceptedStudents'])
            ->first();

        if (!$student) {
            return $statusLevel;
        }

        $pattern = TableRegistry::getTableLocator()->get('ProgramTypes')
            ->StudentStatusPatterns
            ->getProgramTypePattern(
                $student->program_id,
                $student->program_type_id,
                $student->accepted_student->academic_year
            );

        $registrations = $this->find()
            ->where(['student_id' => $studentId])
            ->order(['created' => 'ASC'])
            ->group(['academic_year', 'semester'])
            ->toArray();

        $semesterCount = 0;
        foreach ($registrations as $registration) {
            if (
                strcasecmp($registration->academic_year, $academicYear) === 0 &&
                strcasecmp($registration->semester, $semester) === 0
            ) {
                break;
            }
            $semesterCount++;
        }

        $yearLevel = (int)($semesterCount / 2) + 1;
        $semesterLevel = ($semesterCount % 2 > 0) ? 'II' : 'I';

        switch ($yearLevel) {
            case 1:
                $name = '1st';
                break;
            case 2:
                $name = '2nd';
                break;
            case 3:
                $name = '3rd';
                break;
            default:
                $name = $yearLevel . 'th';
        }

        return ['year' => $name, 'semester' => $semesterLevel];
    }

    /**
     * Retrieves courses with 'Fx' grades for a department or college.
     *
     * @param int|null $departmentId The department or college ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param int $pre Whether for pre-engineering (1 or 0).
     * @param int $onlySelectedByStudent Whether to filter by student selection (1 or 0).
     * @return array Courses grouped by section.
     */
    public function listOfCoursesWithFx($departmentId = null, $academicYear = null, $semester = null, $programId = null, $programTypeId = null, $pre = 0, $onlySelectedByStudent = 0)
    {
        if (!$departmentId || !$academicYear || !$semester || !$programId || !$programTypeId) {
            return [];
        }

        $publishedCourseConditions = [
            'PublishedCourses.program_id' => $programId,
            'PublishedCourses.program_type_id' => $programTypeId,
            'PublishedCourses.academic_year' => $academicYear,
            'PublishedCourses.semester' => $semester
        ];

        if ($pre) {
            $publishedCourseConditions['PublishedCourses.college_id'] = $departmentId;
            $publishedCourseConditions['PublishedCourses.department_id IS'] = null;
        } else {
            $publishedCourseConditions['PublishedCourses.given_by_department_id'] = $departmentId;
        }

        $publishedCourses = $this->find()
            ->where([
                'CourseRegistrations.academic_year' => $academicYear,
                'CourseRegistrations.semester' => $semester,
                'CourseRegistrations.published_course_id IN' => $this->PublishedCourses->find()
                    ->select(['id'])
                    ->where($publishedCourseConditions),
                'CourseRegistrations.id IN' => $this->ExamGrades->find()
                    ->select(['course_registration_id'])
                    ->where(['grade' => 'Fx', 'department_approval' => 1, 'registrar_approval' => 1])
            ])
            ->contain([
                'PublishedCourses' => [
                    'Sections', 'YearLevels', 'Courses', 'Departments'
                ],
                'Students' => ['fields' => ['id', 'graduated']]
            ])
            ->toArray();

        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $publishedCoursesAdds = $courseAddsTable->find()
            ->where([
                'CourseAdds.academic_year' => $academicYear,
                'CourseAdds.semester' => $semester,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1,
                'CourseAdds.published_course_id IN' => $this->PublishedCourses->find()
                    ->select(['id'])
                    ->where($publishedCourseConditions),
                'CourseAdds.id IN' => $this->ExamGrades->find()
                    ->select(['course_add_id'])
                    ->where(['grade' => 'Fx', 'department_approval' => 1, 'registrar_approval' => 1])
            ])
            ->contain([
                'PublishedCourses' => [
                    'Sections', 'YearLevels', 'Courses', 'Departments'
                ],
                'Students' => ['fields' => ['id', 'graduated']]
            ])
            ->toArray();

        $publishedCoursesMerged = array_merge($publishedCourses, $publishedCoursesAdds);
        $organizedCoursesBySections = [];

        $fxResitRequestsTable = TableRegistry::getTableLocator()->get('FxResitRequests');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        foreach ($publishedCoursesMerged as $course) {
            if ($onlySelectedByStudent && !$fxResitRequestsTable->publishedCourseSelected($course->published_course->id)) {
                continue;
            }

            $grade = null;
            if (isset($course->id) && isset($course->student->id) && $course->student->graduated == 0) {
                $grade = $examGradesTable->getApprovedGrade($course->id, 1);
            } elseif (isset($course->course_add->id) && isset($course->student->id) && $course->student->graduated == 0) {
                $grade = $examGradesTable->getApprovedGrade($course->course_add->id, 0);
            }

            if ($grade && $grade['grade'] === 'Fx' && !empty($grade['noGradeChangeRecorded'])) {
                $sectionName = sprintf(
                    '%s (%s, %s)',
                    $course->published_course->section->name,
                    $course->published_course->year_level->name ?? ($course->published_course->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'),
                    $course->published_course->section->academic_year
                );

                $courseId = isset($course->id) ? $course->published_course_id : $course->course_add->published_course_id;
                $organizedCoursesBySections[$sectionName][$courseId] = $course->published_course->course->course_code_title;
            }
        }

        return $organizedCoursesBySections;
    }

    /**
     * Retrieves students with 'NG' grades due to cheating.
     *
     * @param int|null $departmentId The department or college ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @param int $pre Whether for pre-engineering (1 or 0).
     * @return array List of students with cheating incidents.
     */
    public function listOfStudentsWithNGToFWithCheating($departmentId = null, $academicYear = null, $semester = null, $programId = null, $programTypeId = null, $pre = 0)
    {
        if (!$departmentId || !$academicYear || !$semester || !$programId || !$programTypeId) {
            return [];
        }

        $publishedCourseConditions = [
            'PublishedCourses.program_id' => $programId,
            'PublishedCourses.program_type_id' => $programTypeId,
            'PublishedCourses.academic_year' => $academicYear,
            'PublishedCourses.semester' => $semester
        ];

        if ($pre) {
            $publishedCourseConditions['PublishedCourses.college_id'] = $departmentId;
            $publishedCourseConditions['PublishedCourses.department_id IS'] = null;
        } else {
            $publishedCourseConditions['PublishedCourses.given_by_department_id'] = $departmentId;
        }

        $publishedCourses = $this->find()
            ->where([
                'CourseRegistrations.academic_year' => $academicYear,
                'CourseRegistrations.semester' => $semester,
                'CourseRegistrations.published_course_id IN' => $this->PublishedCourses->find()
                    ->select(['id'])
                    ->where($publishedCourseConditions),
                'CourseRegistrations.id IN' => $this->ExamGrades->find()
                    ->select(['course_registration_id'])
                    ->where(['grade' => 'NG', 'department_approval' => 1, 'registrar_approval' => 1])
            ])
            ->contain([
                'Students',
                'PublishedCourses' => ['Sections', 'YearLevels', 'Courses', 'Departments']
            ])
            ->toArray();

        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $publishedCoursesAdds = $courseAddsTable->find()
            ->where([
                'CourseAdds.academic_year' => $academicYear,
                'CourseAdds.semester' => $semester,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1,
                'CourseAdds.published_course_id IN' => $this->PublishedCourses->find()
                    ->select(['id'])
                    ->where($publishedCourseConditions),
                'CourseAdds.id IN' => $this->ExamGrades->find()
                    ->select(['course_add_id'])
                    ->where(['grade' => 'NG', 'department_approval' => 1, 'registrar_approval' => 1])
            ])
            ->contain([
                'Students',
                'PublishedCourses' => ['Sections', 'YearLevels', 'Courses', 'Departments']
            ])
            ->toArray();

        $publishedCoursesMerged = array_merge($publishedCourses, $publishedCoursesAdds);
        $studentsList = [];

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');

        foreach ($publishedCoursesMerged as $course) {
            $grade = null;
            $courseRegIds = [];
            if (isset($course->id) && isset($course->student->id) && $course->student->graduated == 0) {
                $grade = $examGradesTable->getApprovedGrade($course->id, 1);
                $courseRegIds = $this->find('list', ['valueField' => 'id'])
                    ->where(['student_id' => $course->student->id, 'id !=' => $course->id])
                    ->toArray();
            } elseif (isset($course->course_add->id) && isset($course->student->id) && $course->student->graduated == 0) {
                $grade = $examGradesTable->getApprovedGrade($course->course_add->id, 0);
                $courseRegIds = $courseAddsTable->find('list', ['valueField' => 'id'])
                    ->where(['student_id' => $course->student->id, 'id !=' => $course->course_add->id])
                    ->toArray();
            }

            $previousCheatingCount = 0;
            if (!empty($courseRegIds)) {
                $examGradeIds = $examGradesTable->find('list', ['valueField' => 'id'])
                    ->where([($course->id ? 'course_registration_id' : 'course_add_id') . ' IN' => $courseRegIds])
                    ->toArray();
                $previousCheatingCount = $examGradeChangesTable->find()
                    ->where(['cheating' => 1, 'exam_grade_id IN' => $examGradeIds])
                    ->count();
            }

            $isCheating = $examGradeChangesTable->find()
                ->where(['cheating' => 1, 'exam_grade_id' => $grade['grade_id'] ?? 0])
                ->first();

            if ($isCheating && $course->student->graduated == 0) {
                $index = count($studentsList);
                $studentsList[$index] = [
                    'full_name' => $course->student->full_name,
                    'gender' => $course->student->gender,
                    'studentnumber' => $course->student->studentnumber,
                    'recentCheatingCourse' => sprintf(
                        '%s %s',
                        $course->published_course->course->course_title,
                        $course->published_course->course->course_code
                    ),
                    'previousCheatingCount' => $previousCheatingCount,
                    'grade_id' => $grade['grade_id'] ?? null,
                    'grade' => $grade['grade'] ?? null
                ];
            }
        }

        return $studentsList;
    }

    /**
     * Retrieves published courses with missing grade entries.
     *
     * @param int|null $departmentId The department ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @param int|null $programId The program ID.
     * @param int|null $programTypeId The program type ID.
     * @return array Courses with missing entries grouped by section.
     */
    public function getListOfPublishedCourseGradeEntryMissed($departmentId = null, $academicYear = null, $semester = null, $programId = null, $programTypeId = null)
    {
        if (!$departmentId || !$academicYear || !$semester || !$programId || !$programTypeId) {
            return [];
        }

        $publishedCourses = $this->PublishedCourses->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'drop' => 0,
                'add' => 0,
                'program_id' => $programId,
                'program_type_id' => $programTypeId,
                'department_id' => $departmentId,
                'id IN' => $this->find()->select(['published_course_id'])->where(['published_course_id IS NOT' => null])
            ])
            ->contain([
                'Sections', 'YearLevels', 'Courses', 'Departments', 'GivenByDepartments', 'Colleges'
            ])
            ->toArray();

        $organizedCoursesBySections = [];
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        foreach ($publishedCourses as $course) {
            $missingEntries = $this->Students->getStudentIdsNotRegisteredPublishedCourse($course->id);
            $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->id);

            if (!empty($missingEntries) && $gradeSubmitted) {
                $sectionName = sprintf(
                    '%s (%s, %s)',
                    $course->section->name,
                    $course->year_level->name ?? ($course->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'),
                    $course->section->academic_year
                );
                $organizedCoursesBySections[$sectionName][$course->id] = sprintf(
                    '%s (%s)',
                    $course->course->course_title,
                    $course->course->course_code
                );
            }
        }

        return $organizedCoursesBySections;
    }

    /**
     * Retrieves students not registered in a given section and period.
     *
     * @param array|null $data Search criteria.
     * @return array Students grouped by program and section.
     */
    public function studentListNotRegistered($data = null)
    {
        $searchConditions = [
            'conditions' => ['Students.graduated' => 0],
            'fields' => [
                'Students.id', 'Students.studentnumber', 'Students.full_name', 'Students.gender',
                'Students.department_id', 'Students.curriculum_id', 'Students.college_id',
                'Students.program_id', 'Students.program_type_id', 'Students.graduated'
            ],
            'limit' => 100,
            'order' => ['Students.full_name' => 'ASC'],
            'contain' => [
                'Departments' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
            ]
        ];

        if (!empty($data['Student']['college_id'])) {
            $searchConditions['conditions']['Students.college_id'] = $data['Student']['college_id'];
            $searchConditions['conditions']['Students.department_id IS'] = null;
        }

        if (!empty($data['Student']['department_id'])) {
            $searchConditions['conditions']['Students.department_id'] = $data['Student']['department_id'];
        }

        if (!empty($data['Student']['program_id'])) {
            $searchConditions['conditions']['Students.program_id'] = $data['Student']['program_id'];
        }

        if (!empty($data['Student']['program_type_id'])) {
            $searchConditions['conditions']['Students.program_type_id'] = $data['Student']['program_type_id'];
        }

        $yearLevelId = null;
        if (!empty($data['Student']['year_level_id']) && !empty($data['Student']['department_id'])) {
            $yearLevelId = TableRegistry::getTableLocator()->get('YearLevels')
                ->field('id', [
                    'department_id' => $data['Student']['department_id'],
                    'name' => $data['Student']['year_level_id']
                ]);
        }

        $sectionId = !empty($data['Student']['section_id']) ? $data['Student']['section_id'] : null;
        if ($sectionId) {
            $studentListIds = $this->Sections->getSectionActiveStudentsId($sectionId);
            $searchConditions['conditions']['Students.id IN'] = $studentListIds ?: [0];
        }

        if (!empty($data['Student']['semester']) && !empty($data['Student']['academic_year'])) {
            $subQuery = $this->find()
                ->select(['student_id'])
                ->distinct(['student_id'])
                ->where([
                    'semester' => $data['Student']['semester'],
                    'academic_year' => $data['Student']['academic_year']
                ]);
            if ($sectionId) {
                $subQuery->where(['section_id' => $sectionId]);
            } elseif ($yearLevelId) {
                $subQuery->where(['year_level_id' => $yearLevelId]);
            }
            $searchConditions['conditions']['Students.id NOT IN'] = $subQuery;
        }

        $students = $this->Students->find('all', $searchConditions)->toArray();
        $organizedStudents = [];

        foreach ($students as $student) {
            $sectionDetail = $this->Sections->getStudentActiveSection($student->id, $data['Student']['academic_year'] ?? null);
            if ($sectionDetail && $sectionDetail->year_level_id) {
                if (!$yearLevelId || $yearLevelId == $sectionDetail->year_level_id) {
                    $key = sprintf(
                        '%s~%s~%s~%s~%s',
                        $student->program->name,
                        $student->program_type->name,
                        $sectionDetail->year_level->name,
                        $sectionDetail->section->name,
                        $sectionDetail->section->id
                    );
                    $organizedStudents[$key][] = $student;
                }
            } elseif ($sectionDetail && $sectionDetail->college_id) {
                $key = sprintf(
                    '%s~%s~Pre/Fresh~%s~%s',
                    $student->program->name,
                    $student->program_type->name,
                    $sectionDetail->section->name,
                    $sectionDetail->section->id
                );
                $organizedStudents[$key][] = $student;
            }
        }

        return $organizedStudents;
    }

    /**
     * Registers all students in sections for a department.
     *
     * @param int|null $departmentId The department ID.
     * @param int|null $programTypeId The program type ID.
     * @param string|null $academicYear The academic year.
     * @param string|null $semester The semester.
     * @return bool True on success.
     */
    public function registerAllSection($departmentId = null, $programTypeId = null, $academicYear = null, $semester = null)
    {
        if (!$departmentId || !$programTypeId || !$academicYear || !$semester) {
            return false;
        }

        $sections = $this->Sections->find('list', ['valueField' => 'id'])
            ->where([
                'department_id' => $departmentId,
                'program_type_id' => $programTypeId,
                'academic_year' => $academicYear
            ])
            ->toArray();

        foreach ($sections as $sectionId) {
            $this->massRegisterStudent($sectionId, [
                'academic_year' => $academicYear,
                'semester' => $semester
            ]);
        }

        return true;
    }

    /**
     * Retrieves course registrations without corresponding student sections.
     *
     * @param int|null $departmentId The department ID.
     * @return void
     */
    public function getRegistrationWithoutStudentSectionCreated($departmentId = null)
    {
        $conditions = [
            'section_id NOT IN' => $this->Students->StudentsSections->find()
                ->select(['section_id'])
                ->where(['student_id IS NOT' => null])
        ];

        if ($departmentId) {
            $conditions['PublishedCourses.department_id'] = $departmentId;
        }

        $registrations = $this->find()
            ->where($conditions)
            ->contain(['PublishedCourses'])
            ->toArray();

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

        foreach ($registrations as $registration) {
            $sectionExists = $studentsSectionsTable->find()
                ->where([
                    'student_id' => $registration->student_id,
                    'section_id' => $registration->section_id
                ])
                ->count();

            if ($sectionExists == 0) {
                $studentSection = $studentsSectionsTable->newEntity([
                    'section_id' => $registration->section_id,
                    'student_id' => $registration->student_id,
                    'archive' => 1
                ]);

                $studentsSectionsTable->save($studentSection);

                $section = $sectionsTable->get($registration->section_id);
                $section->archive = 1;
                $sectionsTable->save($section);
            }
        }
    }

    /**
     * Retrieves all section IDs for a student’s course registrations.
     *
     * @param int|null $studentId The student ID.
     * @return array List of section IDs.
     */
    public function getAllSectionIdsForStudentFromCourseRegistrations($studentId = null)
    {
        if (!$studentId) {
            return [];
        }

        return $this->find('list', ['valueField' => 'section_id'])
            ->where(['student_id' => $studentId])
            ->group(['section_id'])
            ->toArray();
    }

}
