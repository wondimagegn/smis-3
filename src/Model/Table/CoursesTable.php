<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class CoursesTable extends Table
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

        $this->setTable('courses');
        $this->setDisplayField('course_code_title');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('CourseCategories', [
            'foreignKey' => 'course_category_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('GradeTypes', [
            'foreignKey' => 'grade_type_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'LEFT',
        ]);

        // BelongsToMany Associations
        $this->belongsToMany('Staffs', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'staff_id',
            'joinTable' => 'courses_staffs',
            'saveStrategy' => 'replace',
        ]);

        $this->belongsToMany('Students', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'courses_students',
            'saveStrategy' => 'replace',
        ]);

        // HasMany Associations
        $this->hasMany('CourseForSubstituted', [
            'className' => 'EquivalentCourses',
            'foreignKey' => 'course_for_substituted_id',
            'dependent' => true,
        ]);

        $this->hasMany('CourseBeSubstituted', [
            'className' => 'EquivalentCourses',
            'foreignKey' => 'course_be_substituted_id',
            'dependent' => true,
        ]);

        $this->hasMany('GraduationWorks', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('Prerequisites', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('Books', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('Journals', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('Weblinks', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'course_id',
            'dependent' => false,
        ]);
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
            ->scalar('course_title')
            ->requirePresence('course_title', 'create')
            ->notEmptyString('course_title', 'Please provide course title, it is required.');

        $validator
            ->scalar('course_code')
            ->requirePresence('course_code', 'create')
            ->notEmptyString('course_code', 'Please provide course code, it is required.')
            ->add('course_code', 'unique', [
                'rule' => [$this, 'courseCodeUnique'],
                'message' => 'Please provide a unique course code. The entered course code is already recorded for another course.',
                'provider' => 'table'
            ])
            ->add('course_code', 'separatedByMinus', [
                'rule' => [$this, 'courseCodeSeparatedByMinus'],
                'message' => 'The course code should be separated with a hyphen (e.g., COMP-200).',
                'provider' => 'table'
            ]);

        $validator
            ->numeric('credit')
            ->requirePresence('credit', 'create')
            ->notEmptyString('credit', 'Please provide credit, it is required.')
            ->greaterThanOrEqual('credit', 0, 'Please provide a valid credit, greater than or equal to zero.');

        $validator
            ->numeric('lecture_hours')
            ->requirePresence('lecture_hours', 'create')
            ->notEmptyString('lecture_hours', 'Please provide lecture hours, it is required.')
            ->greaterThanOrEqual('lecture_hours', 0, 'Please provide lecture hours, greater than or equal to zero.');

        $validator
            ->numeric('tutorial_hours')
            ->requirePresence('tutorial_hours', 'create')
            ->notEmptyString('tutorial_hours', 'Please provide tutorial hours, it is required.')
            ->greaterThanOrEqual('tutorial_hours', 0, 'Please provide tutorial hours, greater than or equal to zero.');

        $validator
            ->numeric('laboratory_hours')
            ->requirePresence('laboratory_hours', 'create')
            ->notEmptyString('laboratory_hours', 'Please provide laboratory hours, it is required.')
            ->greaterThanOrEqual('laboratory_hours', 0, 'Please provide laboratory hours, greater than or equal to zero.');

        $validator
            ->scalar('course_status')
            ->requirePresence('course_status', 'create')
            ->notEmptyString('course_status', 'Please provide course status, it is required.');

        $validator
            ->integer('curriculum_id')
            ->requirePresence('curriculum_id', 'create')
            ->notEmptyString('curriculum_id', 'Please attach course to a curriculum, it is required.');

        $validator
            ->integer('year_level_id')
            ->requirePresence('year_level_id', 'create')
            ->notEmptyString('year_level_id', 'Please select course year level, it is required.');

        $validator
            ->integer('grade_type_id')
            ->requirePresence('grade_type_id', 'create')
            ->notEmptyString('grade_type_id', 'Please select course grade type, it is required.');

        $validator
            ->integer('course_category_id')
            ->requirePresence('course_category_id', 'create')
            ->notEmptyString('course_category_id', 'Please select course category, it is required.');

        $validator
            ->scalar('semester')
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester', 'Please select course semester, it is required.');

        return $validator;
    }

    /**
     * Validates if the course code is unique within the curriculum.
     *
     * @param string $courseCode The course code to validate.
     * @param array $context The validation context.
     * @return bool True if unique, false otherwise.
     */
    public function courseCodeUnique($courseCode, array $context)
    {
        $conditions = [
            'Courses.course_code' => trim($courseCode),
            'Courses.curriculum_id' => $context['data']['curriculum_id']
        ];
        if (!empty($context['data']['id'])) {
            $conditions['Courses.id !='] = $context['data']['id'];
        }

        return $this->find()->where($conditions)->count() === 0;
    }

    /**
     * Validates if the course code contains a hyphen.
     *
     * @param string $courseCode The course code to validate.
     * @return bool True if contains hyphen, false otherwise.
     */
    public function courseCodeSeparatedByMinus($courseCode)
    {
        return strpos($courseCode, '-') !== false;
    }

    /**
     * Validates lecture and lab attendance requirement values.
     *
     * @param array $data The data to validate.
     * @param array $context The validation context.
     * @return bool True if valid, false otherwise.
     */
    public function checkAttendanceRequirementValue($data, array $context)
    {
        foreach (['lecture_attendance_requirement', 'lab_attendance_requirement'] as $field) {
            if (!empty($data[$field])) {
                $value = $data[$field];
                if (strlen($value) > 4) {
                    $this->validationErrors[$field][] = 'The maximum character allowed is 4 (e.g., X%, XX%, XXX%).';
                    return false;
                }
                $numericPart = substr($value, 0, -1);
                if (!is_numeric($numericPart)) {
                    $this->validationErrors[$field][] = "The {$field} should be numeric. The value '{$numericPart}' is invalid.";
                    return false;
                }
                if (substr($value, -1) !== '%') {
                    $this->validationErrors[$field][] = "The {$field} must end with '%'. The value is missing %.";
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Checks if a course can be deleted based on related records.
     *
     * @param int|null $courseId The course ID.
     * @return bool True if can be deleted, false otherwise.
     */
    public function canItBeDeleted($courseId = null)
    {
        if (!$courseId) {
            return false;
        }

        $relatedTables = [
            'Prerequisites' => ['course_id' => $courseId],
            'Books' => ['course_id' => $courseId],
            'Journals' => ['course_id' => $courseId],
            'Weblinks' => ['course_id' => $courseId],
            'PublishedCourses' => ['course_id' => $courseId]
        ];

        foreach ($relatedTables as $table => $conditions) {
            if (TableRegistry::getTableLocator()->get($table)->find()->where($conditions)->count() > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ensures only one thesis course per curriculum and semester.
     *
     * @param int $value The thesis value (1 or 0).
     * @param array $context The validation context.
     * @return bool True if valid, false otherwise.
     */
    public function notAllowMoreThanOneThesis($value, array $context)
    {
        if ($value == 1) {
            $conditions = [
                'Courses.thesis' => 1,
                'Courses.curriculum_id' => $context['data']['curriculum_id'],
                'Courses.semester' => $context['data']['semester']
            ];
            if (!empty($context['data']['id'])) {
                $conditions['Courses.id !='] = $context['data']['id'];
            }

            $count = $this->find()->where($conditions)->count();
            if ($count > 0) {
                $curriculum = TableRegistry::getTableLocator()->get('Curriculums')
                    ->find('list', ['valueField' => 'curriculum_detail'])
                    ->where(['id' => $context['data']['curriculum_id']])
                    ->first();
                $this->validationErrors['thesis'][] = sprintf(
                    'You cannot define more than one thesis for %s curriculum. Please uncheck thesis.',
                    h($curriculum)
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Ensures only one exit exam course per curriculum.
     *
     * @param int $value The exit exam value (1 or 0).
     * @param array $context The validation context.
     * @return bool True if valid, false otherwise.
     */
    public function notAllowMoreThanOneExitExam($value, array $context)
    {
        if ($value == 1) {
            $conditions = [
                'Courses.exit_exam' => 1,
                'Courses.curriculum_id' => $context['data']['curriculum_id']
            ];
            if (!empty($context['data']['id'])) {
                $conditions['Courses.id !='] = $context['data']['id'];
            }

            $count = $this->find()->where($conditions)->count();
            if ($count > 0) {
                $curriculum = TableRegistry::getTableLocator()->get('Curriculums')
                    ->find('list', ['valueField' => 'curriculum_detail'])
                    ->where(['id' => $context['data']['curriculum_id']])
                    ->first();
                $this->validationErrors['exit_exam'][] = sprintf(
                    'You cannot define more than one exit exam for %s curriculum. Please uncheck exit exam.',
                    h($curriculum)
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Removes empty related data before saving.
     *
     * @param array $data The data to process.
     * @return array The cleaned data.
     */
    public function unsetEmpty($data = null)
    {
        if (empty($data)) {
            return $data;
        }

        $related = ['Prerequisite', 'Book', 'Journal', 'Weblink'];
        foreach ($related as $model) {
            if (!empty($data[$model])) {
                $hasData = false;
                foreach ($data[$model] as $key => $item) {
                    $isEmpty = true;
                    foreach ($item as $field => $value) {
                        if (!empty($value) && $field !== 'id') {
                            $isEmpty = false;
                            break;
                        }
                    }
                    if ($isEmpty) {
                        unset($data[$model][$key]);
                    } else {
                        $hasData = true;
                    }
                }
                if (!$hasData) {
                    unset($data[$model]);
                }
            }
        }

        return $data;
    }

    /**
     * Removes empty related data and IDs for copying courses.
     *
     * @param array $data The data to process.
     * @return array The cleaned data.
     */
    public function unsetEmptyForCopy($data = null)
    {
        if (empty($data['Course'])) {
            return $data;
        }

        foreach ($data['Course'] as &$course) {
            $related = ['Prerequisite', 'Book', 'Journal', 'Weblink'];
            foreach ($related as $model) {
                if (empty($course[$model])) {
                    unset($course[$model]);
                } else {
                    foreach ($course[$model] as &$item) {
                        unset($item['id'], $item['course_id'], $item['created'], $item['modified']);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Formats data for copying courses, removing IDs and timestamps.
     *
     * @param array $data The data to process.
     * @return array The formatted data.
     */
    public function saveAllFormatCopyCourse($data = null)
    {
        if (empty($data)) {
            return [];
        }

        $dataFormatted = $data;
        foreach ($dataFormatted as &$course) {
            unset($course['Course']['id'], $course['Course']['created'], $course['Course']['modified']);

            $related = ['Prerequisite', 'Book', 'Journal', 'Weblink'];
            foreach ($related as $model) {
                if (empty($course[$model])) {
                    unset($course[$model]);
                } else {
                    foreach ($course[$model] as &$item) {
                        unset($item['id'], $item['course_id'], $item['created'], $item['modified']);
                    }
                }
            }
        }

        return $dataFormatted;
    }

    /**
     * Retrieves grade scale details for a course.
     *
     * @param int|null $courseId The course ID.
     * @param int|null $collegeId The college ID.
     * @param int $active Whether to include active scales (1 or 0).
     * @param int $own Whether to include own scales (1 or 0).
     * @return array|bool Grade scale details or false if invalid.
     */
    public function getGradeScaleDetails($courseId = null, $collegeId = null, $active = 1, $own = 0)
    {
        if (!$courseId) {
            return false;
        }

        $course = $this->find()
            ->where(['Courses.id' => $courseId])
            ->contain(['Curriculums'])
            ->first();

        if (!$course || empty($course->grade_type_id)) {
            return false;
        }

        return TableRegistry::getTableLocator()->get('GradeTypes')->getGradeScaleDetails(
            $course->grade_type_id,
            $course->curriculum->program_id,
            'College',
            $collegeId,
            $active,
            $own
        );
    }

    /**
     * Checks if a course’s credit can be edited or deleted based on exam grades.
     *
     * @param int|null $courseId The course ID.
     * @return int 1 if editing/deleting is denied, 0 otherwise.
     */
    public function denyEditDeleteCredit($courseId = null)
    {
        if (!$courseId) {
            return 0;
        }

        $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list', ['valueField' => 'id'])
            ->where(['course_id' => $courseId])
            ->toArray();

        if (empty($publishedCourseIds)) {
            return 0;
        }

        $courseRegistrationIds = TableRegistry::getTableLocator()->get('CourseRegistrations')
            ->find('list', ['valueField' => 'id'])
            ->where(['published_course_id IN' => $publishedCourseIds])
            ->toArray();

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        if (!empty($courseRegistrationIds)) {
            if ($examGradesTable->find()->where(['course_registration_id IN' => $courseRegistrationIds])->count() > 0) {
                return 1;
            }
        }

        $courseAddIds = TableRegistry::getTableLocator()->get('CourseAdds')
            ->find('list', ['valueField' => 'id'])
            ->where(['published_course_id IN' => $publishedCourseIds])
            ->toArray();

        if (!empty($courseAddIds)) {
            if ($examGradesTable->find()->where(['course_add_id IN' => $courseAddIds])->count() > 0) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Checks if a course’s basic details can be edited/deleted based on graduated students.
     *
     * @param int|null $courseId The course ID.
     * @return int 1 if editing/deleting is denied, 0 otherwise.
     */
    public function denyEditDeleteCourseBasicDetailChange($courseId = null)
    {
        if (!$courseId) {
            return 0;
        }

        $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list', ['valueField' => 'id'])
            ->where(['course_id' => $courseId])
            ->toArray();

        if (empty($publishedCourseIds)) {
            return 0;
        }

        $registrationIds = TableRegistry::getTableLocator()->get('CourseRegistrations')
            ->find('list', ['valueField' => 'id'])
            ->where(['published_course_id IN' => $publishedCourseIds])
            ->toArray();

        $courseAddIds = TableRegistry::getTableLocator()->get('CourseAdds')
            ->find('list', ['valueField' => 'id'])
            ->where(['published_course_id IN' => $publishedCourseIds])
            ->toArray();

        if (empty($registrationIds) && empty($courseAddIds)) {
            return 0;
        }

        $studentRegisteredIds = TableRegistry::getTableLocator()->get('CourseRegistrations')
            ->find('list', ['valueField' => 'student_id'])
            ->where(['id IN' => $registrationIds])
            ->toArray();

        $studentAddIds = TableRegistry::getTableLocator()->get('CourseAdds')
            ->find('list', ['valueField' => 'student_id'])
            ->where(['id IN' => $courseAddIds])
            ->toArray();

        $graduateListTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $isGraduatedRegistered = $graduateListTable->find()
            ->where(['student_id IN' => $studentRegisteredIds])
            ->count();

        $isGraduatedAdd = $graduateListTable->find()
            ->where(['student_id IN' => $studentAddIds])
            ->count();

        return ($isGraduatedRegistered > 0 || $isGraduatedAdd > 0) ? 1 : 0;
    }

    /**
     * Retrieves a course by exam grade ID.
     *
     * @param int|null $examGradeId The exam grade ID.
     * @return array|null The course data or null if not found.
     */
    public function getCourseByExamGradeId($examGradeId = null)
    {
        if (!$examGradeId) {
            return null;
        }

        $examGrade = TableRegistry::getTableLocator()->get('ExamGrades')->find()
            ->where(['ExamGrades.id' => $examGradeId])
            ->contain([
                'CourseAdds.PublishedCourses.Courses',
                'CourseRegistrations.PublishedCourses.Courses'
            ])
            ->first();

        if ($examGrade) {
            if (!empty($examGrade->course_registration->published_course->course)) {
                return $examGrade->course_registration->published_course->course->toArray();
            }
            if (!empty($examGrade->course_add->published_course->course)) {
                return $examGrade->course_add->published_course->course->toArray();
            }
        }

        return null;
    }

    /**
     * Retrieves equivalent courses taken by a student.
     *
     * @param int|null $studentId The student ID.
     * @param int|null $courseId The course ID.
     * @param array $ayAndSList List of academic year and semester pairs.
     * @return array List of matching course IDs.
     */
    public function getTakenEquivalentCourses($studentId = null, $courseId = null, $ayAndSList = [])
    {
        if (!$studentId || !$courseId) {
            return [$courseId];
        }

        $matchingCourses = [$courseId];

        $student = TableRegistry::getTableLocator()->get('Students')
            ->find()
            ->select(['department_id', 'curriculum_id'])
            ->where(['id' => $studentId])
            ->first();

        $course = $this->find()
            ->select(['department_id', 'curriculum_id'])
            ->where(['id' => $courseId])
            ->first();
        if (!$student || !$course) {
            return $matchingCourses;
        }

        $equivalentCoursesTable = TableRegistry::getTableLocator()->get('EquivalentCourses');

        if ($student->department_id && $course->curriculum_id) {
            if ($student->department_id == $course->department_id && $student->curriculum_id == $course->curriculum_id) {
                $courseBeSubstituted = $equivalentCoursesTable->find()
                    ->select(['course_be_substituted_id'])
                    ->where(['course_for_substituted_id' => $courseId])
                    ->toArray();

                foreach ($courseBeSubstituted as $sub) {
                    $matchingCourses[] = $sub->course_be_substituted_id;
                }
            } else {
                $courseForSubstituted = $equivalentCoursesTable->find()
                    ->select(['course_for_substituted_id'])
                    ->where(['course_be_substituted_id' => $courseId])
                    ->toArray();

                foreach ($courseForSubstituted as $sub) {
                    $courseDetail = $this->find()
                        ->select(['curriculum_id'])
                        ->where(['id' => $sub->course_for_substituted_id])
                        ->contain(['Curriculums' => ['fields' => ['department_id']]])
                        ->first();

                    if ($courseDetail && $courseDetail->curriculum->department_id == $student->department_id) {
                        $matchingCourses[] = $sub->course_for_substituted_id;
                    }
                }
            }
        }

        $conditions = ['CourseRegistrations.student_id' => $studentId];
        $conditionsA = ['CourseAdds.student_id' => $studentId];
        if (!empty($ayAndSList)) {
            $orConditions = [];
            $orAConditions=[];
            foreach ($ayAndSList as $ayAndS) {
                $orConditions[] = [
                    'CourseRegistrations.academic_year' => $ayAndS['academic_year'],
                    'CourseRegistrations.semester' => $ayAndS['semester']
                ];
                $orAConditions[] = [
                    'CourseAdds.academic_year' => $ayAndS['academic_year'],
                    'CourseAdds.semester' => $ayAndS['semester']
                ];
            }
            $conditions['OR'] = $orConditions;
            $conditionsA['OR'] = $orAConditions;
        }

        $registeredMatched = TableRegistry::getTableLocator()->get('CourseRegistrations')
            ->find()
            ->where($conditions)
            ->contain(['PublishedCourses'])
            ->order(['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'])
            ->toArray();

        $addedMatched = TableRegistry::getTableLocator()->get('CourseAdds')
            ->find()
            ->where(array_merge($conditionsA, ['CourseAdds.registrar_confirmation' => 1]))
            ->contain(['PublishedCourses'])
            ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
            ->toArray();

        foreach ($registeredMatched as $reg) {
            $equivalenceMapped = $equivalentCoursesTable->find()
                ->where([
                    'course_be_substituted_id' => $reg->published_course->course_id,
                    'course_for_substituted_id' => $courseId
                ])
                ->count();

            if ($equivalenceMapped) {
                $matchingCourses[] = $reg->published_course->course_id;
            }
        }

        foreach ($addedMatched as $add) {
            $equivalenceMapped = $equivalentCoursesTable->find()
                ->where([
                    'course_be_substituted_id' => $add->published_course->course_id,
                    'course_for_substituted_id' => $courseId
                ])
                ->count();

            if ($equivalenceMapped) {
                $matchingCourses[] = $add->published_course->course_id;
            }
        }

        return array_unique($matchingCourses);
    }

    /**
     * Retrieves course titles matching a given prefix.
     *
     * @param string|null $title The title prefix.
     * @return array List of matching course titles.
     */
    public function getCourseTitle($title = null)
    {
        return $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'course_title'
        ])
            ->where(['course_title LIKE' => trim($title) . '%'])
            ->toArray();
    }

    /**
     * Checks if an equivalent course has a recent grade.
     *
     * @param int|null $studentId The student ID.
     * @param int|null $courseId The course ID.
     * @return bool True if a recent grade exists, false otherwise.
     */
    public function isEquivalentCourseTakenHaveRecentGrade($studentId = null, $courseId = null)
    {
        if (!$studentId || !$courseId) {
            return false;
        }

        $student = TableRegistry::getTableLocator()->get('Students')
            ->find()
            ->select(['curriculum_id'])
            ->where(['id' => $studentId])
            ->first();

        if (!$student) {
            return false;
        }

        $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')
            ->validEquivalentCourse($courseId, $student->curriculum_id);

        if (empty($matchingCourses)) {
            return false;
        }

        foreach ($matchingCourses as $courseId) {
            if ($this->isCourseTakenHaveRecentGrade($studentId, $courseId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a course has a recent passing grade.
     *
     * @param int|null $studentId The student ID.
     * @param int|null $courseId The course ID.
     * @return bool True if a recent passing grade exists, false otherwise.
     */
    public function isCourseTakenHaveRecentPassGrade($studentId = null, $courseId = null)
    {
        if (!$studentId || !$courseId) {
            return false;
        }

        $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list', ['valueField' => 'id'])
            ->where(['course_id' => $courseId])
            ->toArray();

        if (empty($publishedCourseIds)) {
            return false;
        }

        $registration = TableRegistry::getTableLocator()->get('CourseRegistrations')
            ->find()
            ->where(['student_id' => $studentId, 'published_course_id IN' => $publishedCourseIds])
            ->order(['id' => 'DESC'])
            ->first();

        $add = TableRegistry::getTableLocator()->get('CourseAdds')
            ->find()
            ->where(['student_id' => $studentId, 'published_course_id IN' => $publishedCourseIds])
            ->order(['id' => 'DESC'])
            ->first();

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        if ($registration) {
            $grade = $examGradesTable->getApprovedGrade($registration->id, true);
            if (!empty($grade) && $grade['pass_grade'] != 0) {
                return true;
            }
        }

        if ($add) {
            $grade = $examGradesTable->getApprovedGrade($add->id, false);
            if (!empty($grade) && $grade['pass_grade'] != 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a course or its equivalent has a recent grade.
     *
     * @param int|null $studentId The student ID.
     * @param int|null $courseId The course ID.
     * @return bool True if a recent grade exists, false otherwise.
     */
    public function isCourseTakenHaveRecentGrade($studentId = null, $courseId = null)
    {
        if (!$studentId || !$courseId) {
            return false;
        }

        $student = TableRegistry::getTableLocator()->get('Students')
            ->find()
            ->select(['curriculum_id'])
            ->where(['id' => $studentId])
            ->first();

        if (!$student) {
            return false;
        }

        $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')
            ->validEquivalentCourse($courseId, $student->curriculum_id);

        if (empty($matchingCourses)) {
            return false;
        }

        $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list', ['valueField' => 'id'])
            ->where(['course_id IN' => $matchingCourses])
            ->toArray();

        if (empty($publishedCourseIds)) {
            return false;
        }

        $conditions = ['student_id' => $studentId, 'published_course_id IN' => $publishedCourseIds];
        $registrations = TableRegistry::getTableLocator()->get('CourseRegistrations')
            ->find()
            ->where($conditions)
            ->contain(['PublishedCourses'])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
            ->toArray();

        $adds = TableRegistry::getTableLocator()->get('CourseAdds')
            ->find()
            ->where(array_merge($conditions, [
                'department_approval' => 1,
                'registrar_confirmation' => 1
            ]))
            ->contain(['PublishedCourses'])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
            ->toArray();

        $registerAndAddFreq = [];
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        foreach ($registrations as $reg) {
            if (!$courseRegistrationsTable->isCourseDropped($reg->id)) {
                $registerAndAddFreq[] = [
                    'id' => $reg->id,
                    'type' => 'register',
                    'created' => $reg->created
                ];
            }
        }

        foreach ($adds as $add) {
            $registerAndAddFreq[] = [
                'id' => $add->id,
                'type' => 'add',
                'created' => $add->created
            ];
        }

        usort($registerAndAddFreq, function ($a, $b) {
            return strcmp($a['created'], $b['created']);
        });

        return !empty($registerAndAddFreq);
    }

    /**
     * Retrieves repeatable grades for a published course.
     *
     * @param int|null $publishedCourseId The published course ID.
     * @return array List of repeatable grades.
     */
    public function getRepeatableGradeGivenPublishedCourse($publishedCourseId = null)
    {
        if (!$publishedCourseId) {
            return ['I', 'NG', 'W', 'DO', 'F'];
        }

        $course = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find()
            ->where(['id' => $publishedCourseId])
            ->contain([
                'Courses' => [
                    'GradeTypes' => [
                        'Grades' => ['fields' => ['grade', 'allow_repetition']]
                    ]
                ]
            ])
            ->first();

        $repeatableGrades = ['I' => 'I', 'NG' => 'NG', 'W' => 'W', 'DO' => 'DO', 'F' => 'F'];
        if ($course && !empty($course->course->grade_type->grades)) {
            foreach ($course->course->grade_type->grades as $grade) {
                if ($grade->allow_repetition) {
                    $repeatableGrades[$grade->grade] = $grade->grade;
                }
            }
        }

        return $repeatableGrades;
    }
}
