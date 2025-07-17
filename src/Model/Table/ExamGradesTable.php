<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Expression\QueryExpression;
class ExamGradesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('exam_grades');
        $this->setPrimaryKey('id');

        // Associations
        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('GradeScales', [
            'foreignKey' => 'grade_scale_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('CourseAdds', [
            'foreignKey' => 'course_add_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('MakeupExams', [
            'foreignKey' => 'makeup_exam_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('CourseInstructorAssignments', [
            'foreignKey' => 'course_instructor_assignment_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('ExamGradeChanges', [
            'foreignKey' => 'exam_grade_id',
            'dependent' => false,
        ]);

        // Behaviors
        // Placeholder for Tools.Logable (requires CakePHP 3.x compatible version)
        // $this->addBehavior('Tools.Logable', [
        //     'change' => 'full',
        //     'description_ids' => true,
        //     'displayField' => 'username',
        //     'foreignKey' => 'foreign_key'
        // ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('grade')
            ->requirePresence('grade', 'create')
            ->notEmptyString('grade', 'Please provide grade');

        $validator
            ->numeric('course_registration_id')
            ->allowEmptyString('course_registration_id')
            ->add('course_registration_id', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Course Registration id must be numeric'
            ]);

        $validator
            ->numeric('course_add_id')
            ->allowEmptyString('course_add_id')
            ->add('course_add_id', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Course Add id must be numeric'
            ]);

        $validator
            ->numeric('makeup_exam_id')
            ->allowEmptyString('makeup_exam_id')
            ->add('makeup_exam_id', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Makeup Exam id must be numeric'
            ]);

        return $validator;
    }

    public function isGradeSubmitted($publishedCourseIds = null, array $studentLists = []): int
    {
        $publishedCoursesStudentRegisteredScoreGrade = 0;

        if (!empty($studentLists)) {
            $gradeSubmittedRegisteredCourses = $this->CourseRegistrations->find('list')
                ->where([
                    'CourseRegistrations.published_course_id IN' => $publishedCourseIds,
                    'CourseRegistrations.student_id IN' => $studentLists
                ])
                ->select(['CourseRegistrations.id'])
                ->toArray();
        } else {
            $gradeSubmittedRegisteredCourses = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.published_course_id IN' => $publishedCourseIds])
                ->select(['CourseRegistrations.id'])
                ->toArray();
        }

        if (!empty($gradeSubmittedRegisteredCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_registration_id IN' => $gradeSubmittedRegisteredCourses])
                ->count();

            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return $publishedCoursesStudentRegisteredScoreGrade;
            }
        }

        if (!empty($studentLists)) {
            $gradeSubmittedAddCourses = $this->CourseAdds->find('list')
                ->where([
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.student_id IN' => $studentLists,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ])
                ->select(['CourseAdds.id'])
                ->toArray();
        } else {
            $gradeSubmittedAddCourses = $this->CourseAdds->find('list')
                ->where([
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ])
                ->select(['CourseAdds.id'])
                ->toArray();
        }

        if (!empty($gradeSubmittedAddCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_add_id IN' => $gradeSubmittedAddCourses])
                ->count();

            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return $publishedCoursesStudentRegisteredScoreGrade;
            }
        }

        return $publishedCoursesStudentRegisteredScoreGrade;
    }

    public function getGradeSubmissionDate($publishedCourseIds = null): array
    {
        $publishedCoursesStudentRegisteredScoreGrade = [];

        $gradeSubmittedRegisteredCourses = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.published_course_id IN' => $publishedCourseIds])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        if (!empty($gradeSubmittedRegisteredCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_registration_id IN' => $gradeSubmittedRegisteredCourses])
                ->first();

            if (!empty($publishedCoursesStudentRegisteredScoreGrade)) {
                return $publishedCoursesStudentRegisteredScoreGrade->toArray();
            }
        }

        $gradeSubmittedAddCourses = $this->CourseAdds->find('list')
            ->where(['CourseAdds.published_course_id IN' => $publishedCourseIds])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($gradeSubmittedAddCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_add_id IN' => $gradeSubmittedAddCourses])
                ->first();

            if (!empty($publishedCoursesStudentRegisteredScoreGrade)) {
                return $publishedCoursesStudentRegisteredScoreGrade->toArray();
            }

            $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
            $publishedCoursesStudentRegisteredScoreGrade = $examResultsTable->find()
                ->where(['ExamResults.course_add_id IN' => $gradeSubmittedAddCourses])
                ->first();

            if (!empty($publishedCoursesStudentRegisteredScoreGrade)) {
                return $publishedCoursesStudentRegisteredScoreGrade->toArray();
            }
        }

        return $publishedCoursesStudentRegisteredScoreGrade;
    }

    public function isCourseGradeSubmitted(?int $studentId = null, ?int $sectionId = null): bool
    {
        if (!isset($sectionId)) {
            return false;
        }

        $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
            ->where(['PublishedCourses.section_id' => $sectionId])
            ->select(['PublishedCourses.id'])
            ->toArray();

        $publishedCourseIds = array_column($publishedCourses, 'id');

        if (empty($publishedCourseIds)) {
            return false;
        }

        $listCourseRegistrationIds = [];
        if (!empty($studentId)) {
            $listCourseRegistrationIds = $this->CourseRegistrations->find('list')
                ->where([
                    'CourseRegistrations.student_id' => $studentId,
                    'CourseRegistrations.published_course_id IN' => $publishedCourseIds
                ])
                ->select(['CourseRegistrations.id'])
                ->toArray();
        } else {
            $listCourseRegistrationIds = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.published_course_id IN' => $publishedCourseIds])
                ->select(['CourseRegistrations.id'])
                ->toArray();
        }

        if (!empty($listCourseRegistrationIds)) {
            $gradeSubmitted = $this->find()
                ->where(['ExamGrades.course_registration_id IN' => $listCourseRegistrationIds])
                ->count();

            if ($gradeSubmitted > 0) {
                return true;
            }
        }

        $listCourseAddIds = [];
        if (!empty($studentId)) {
            $listCourseAddIds = $this->CourseAdds->find('list')
                ->where([
                    'CourseAdds.student_id' => $studentId,
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ])
                ->select(['CourseAdds.id'])
                ->toArray();
        } else {
            $listCourseAddIds = $this->CourseAdds->find('list')
                ->where([
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ])
                ->select(['CourseAdds.id'])
                ->toArray();
        }

        if (!empty($listCourseAddIds)) {
            $publishedCoursesStudentAddedScoreGrade = $this->find()
                ->where(['ExamGrades.course_add_id IN' => $listCourseAddIds])
                ->count();

            if ($publishedCoursesStudentAddedScoreGrade > 0) {
                return true;
            }
        }

        return false;
    }

    public function isEverGradeSubmitInTheNameOfSection(?int $sectionId = null): bool
    {
        if (!isset($sectionId)) {
            return false;
        }

        $sections = $this->CourseRegistrations->PublishedCourses->find()
            ->where(['PublishedCourses.section_id' => $sectionId])
            ->select(['PublishedCourses.id'])
            ->toArray();

        $publishedCourseIds = array_column($sections, 'id');
        $listCourseRegistrationIds = [];
        $gradeSubmittedAddCourses = [];

        if (!empty($publishedCourseIds)) {
            $listCourseRegistrationIds = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.published_course_id IN' => $publishedCourseIds])
                ->select(['CourseRegistrations.id'])
                ->toArray();

            $gradeSubmittedAddCourses = $this->CourseAdds->find('list')
                ->where([
                    'CourseAdds.published_course_id IN' => $publishedCourseIds,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ])
                ->select(['CourseAdds.id'])
                ->toArray();
        }

        if (!empty($listCourseRegistrationIds)) {
            $gradeSubmitted = $this->find()
                ->where(['ExamGrades.course_registration_id IN' => $listCourseRegistrationIds])
                ->count();

            if ($gradeSubmitted > 0) {
                return true;
            }

            $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
            $gradeSubmitted = $examResultsTable->find()
                ->where(['ExamResults.course_registration_id IN' => $listCourseRegistrationIds])
                ->count();

            if ($gradeSubmitted > 0) {
                return true;
            }
        }

        if (!empty($gradeSubmittedAddCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_add_id IN' => $gradeSubmittedAddCourses])
                ->count();

            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return true;
            }

            $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
            $publishedCoursesStudentRegisteredScoreGrade = $examResultsTable->find()
                ->where(['ExamResults.course_add_id IN' => $gradeSubmittedAddCourses])
                ->count();

            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return true;
            }
        }

        return false;
    }

    public function gradeCanBeChanged(?int $gradeId = null)
    {
        $gradeHistory = $this->find()
            ->where(['ExamGrades.id' => $gradeId])
            ->contain([
                'CourseRegistrations' => [
                    'PublishedCourses' => [
                        'Courses' => ['fields' => ['id', 'course_code_title']],
                        'fields' => ['id', 'course_id']
                    ],
                    'Students' => ['fields' => ['id', 'full_name_studentnumber', 'graduated']],
                    'fields' => ['id', 'student_id', 'published_course_id']
                ],
                'CourseAdds' => [
                    'PublishedCourses' => [
                        'Courses' => ['fields' => ['id', 'course_code_title']],
                        'fields' => ['id', 'course_id']
                    ],
                    'Students' => ['fields' => ['id', 'full_name_studentnumber', 'graduated']],
                    'fields' => ['id', 'student_id', 'published_course_id']
                ],
                'MakeupExams' => [
                    'PublishedCourses' => [
                        'Courses' => ['fields' => ['id', 'course_code_title']],
                        'fields' => ['id', 'course_id']
                    ],
                    'Students' => ['fields' => ['id', 'full_name_studentnumber', 'graduated']],
                    'fields' => ['id', 'student_id', 'published_course_id']
                ],
                'ExamGradeChanges'
            ])
            ->first();

        if (!empty($gradeHistory)) {
            $courseTitleCourseCode = '';
            $fullNameStudentnumber = '';
            $mostRecentGrade = '';

            if (!empty($gradeHistory->course_registration)) {
                if (!empty($gradeHistory->course_registration->student)) {
                    $fullNameStudentnumber = $gradeHistory->course_registration->student->full_name_studentnumber;
                }
                if (!empty($gradeHistory->course_registration->published_course) && !empty($gradeHistory->course_registration->published_course->course)) {
                    $courseTitleCourseCode = $gradeHistory->course_registration->published_course->course->course_code_title;
                }
            } elseif (!empty($gradeHistory->course_add)) {
                if (!empty($gradeHistory->course_add->student)) {
                    $fullNameStudentnumber = $gradeHistory->course_add->student->full_name_studentnumber;
                }
                if (!empty($gradeHistory->course_add->published_course) && !empty($gradeHistory->course_add->published_course->course)) {
                    $courseTitleCourseCode = $gradeHistory->course_add->published_course->course->course_code_title;
                }
            } elseif (!empty($gradeHistory->makeup_exam)) {
                if (!empty($gradeHistory->makeup_exam->student)) {
                    $fullNameStudentnumber = $gradeHistory->makeup_exam->student->full_name_studentnumber;
                }
                if (!empty($gradeHistory->makeup_exam->published_course) && !empty($gradeHistory->makeup_exam->published_course->course)) {
                    $courseTitleCourseCode = $gradeHistory->makeup_exam->published_course->course->course_code_title;
                }
            }

            if (!empty($gradeHistory->grade)) {
                $mostRecentGrade = $gradeHistory->grade;
                if ($gradeHistory->department_approval === null) {
                    return sprintf(
                        "There is already a submitted grade %s for %s which is waiting for department approval. Please first finalize the approval process for the already submitted grade before recording a new grade.",
                        !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                        (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                    );
                } elseif ($gradeHistory->department_approval == 1 && $gradeHistory->registrar_approval === null) {
                    return sprintf(
                        "There is already a submitted grade %s for %s which is waiting for registrar approval. Please first let the registrar finalize the approval process for the already submitted grade before recording a new grade.",
                        !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                        (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                    );
                }
            }

            if (!empty($gradeHistory->exam_grade_changes)) {
                foreach ($gradeHistory->exam_grade_changes as $examGradeChange) {
                    $mostRecentGrade = $examGradeChange->grade;
                    if ($examGradeChange->manual_ng_conversion != 1 && $examGradeChange->auto_ng_conversion != 1) {
                        if ($examGradeChange->initiated_by_department != 1 && $examGradeChange->department_approval === null) {
                            if ($examGradeChange->makeup_exam_result === null) {
                                return sprintf(
                                    "There is already a submitted grade change request %s for %s which is awaiting department approval. Please first finalize the approval process for the already submitted grade change request before recording a new grade.",
                                    !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                                    (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                                );
                            } else {
                                return sprintf(
                                    "There is already a submitted makeup exam grade %s for %s which is awaiting department approval. Please first finalize the approval process for the already submitted makeup exam grade before recording a new grade.",
                                    !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                                    (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                                );
                            }
                        } elseif (($examGradeChange->initiated_by_department == 1 && $examGradeChange->department_approval != -1 && $examGradeChange->registrar_approval != -1) || ($examGradeChange->initiated_by_department != 1 && $examGradeChange->department_approval == 1)) {
                            if ($examGradeChange->initiated_by_department == 1 && $examGradeChange->makeup_exam_result !== null) {
                                return sprintf(
                                    "There is already a submitted supplementary exam grade %s for %s which is awaiting registrar approval. Please first let the registrar finalize the approval process for the already submitted supplementary exam grade before recording a new grade.",
                                    !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                                    (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                                );
                            } elseif ($examGradeChange->makeup_exam_result === null && $examGradeChange->college_approval === null) {
                                return sprintf(
                                    "There is already a submitted grade %s for %s which is awaiting college approval. Please first let the college finalize the approval process for the already submitted grade before recording a new grade.",
                                    !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                                    (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                                );
                            } elseif (($examGradeChange->makeup_exam_result !== null || $examGradeChange->college_approval == 1) && $examGradeChange->registrar_approval === null) {
                                return sprintf(
                                    "There is already a submitted grade %s for %s which is awaiting registrar approval. Please first let the registrar finalize the approval process for the already submitted grade before recording a new grade.",
                                    !empty($mostRecentGrade) ? "($mostRecentGrade)" : '',
                                    (!empty($fullNameStudentnumber) ? $fullNameStudentnumber : 'the selected student') . (!empty($courseTitleCourseCode) ? " for $courseTitleCourseCode" : ' and course')
                                );
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function getStudentCoursesAndFinalGrade(int $studentId, ?string $academicYear, ?string $semester, int $includeExempted = 0): array
    {
        $coursesAndGrades = [];

        $courseRegistered = $this->CourseRegistrations->find()
            ->where([
                'CourseRegistrations.student_id' => $studentId,
                'CourseRegistrations.academic_year' => $academicYear,
                'CourseRegistrations.semester' => $semester
            ])
            ->contain(['PublishedCourses.Courses'])
            ->order(['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'])
            ->toArray();

        $courseAdded = $this->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.academic_year' => $academicYear,
                'CourseAdds.semester' => $semester
            ])
            ->contain(['PublishedCourses.Courses'])
            ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
            ->toArray();

        foreach ($courseAdded as $key => $courseAdd) {
            if (!($courseAdd->published_course->add == 1 || ($courseAdd->department_approval == 1 && $courseAdd->registrar_confirmation == 1))) {
                unset($courseAdded[$key]);
            }
        }

        $studentDetail = $this->CourseAdds->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $exemptedCourses = [];
        $studentLevel = $this->CourseAdds->Students->StudentExamStatuses->studentYearAndSemesterLevelOfStatus($studentId, $academicYear, $semester);

        $yearLevels = [
            1 => '1st',
            2 => '2nd',
            3 => '3rd'
        ];
        $studentYearLevel = isset($yearLevels[$studentLevel['year']]) ? $yearLevels[$studentLevel['year']] : $studentLevel['year'] . 'th';

        $yearLevelId = TableRegistry::getTableLocator()->get('YearLevels')->find()
            ->where([
                'YearLevels.department_id' => $studentDetail->department_id,
                'YearLevels.name' => $studentYearLevel
            ])
            ->select(['YearLevels.id'])
            ->first()->id ?? null;

        if (!empty($studentDetail->curriculum_id)) {
            $coursesToBeGiven = $this->CourseAdds->PublishedCourses->Courses->find()
                ->where([
                    'Courses.curriculum_id' => $studentDetail->curriculum_id,
                    'Courses.year_level_id' => $yearLevelId,
                    'Courses.semester' => $semester
                ])
                ->toArray();

            if ($includeExempted == 1) {
                $allExemptedCourses = $this->CourseAdds->Students->CourseExemptions->find()
                    ->where([
                        'CourseExemptions.student_id' => $studentDetail->id,
                        'CourseExemptions.department_accept_reject' => 1,
                        'CourseExemptions.registrar_confirm_deny' => 1
                    ])
                    ->toArray();

                foreach ($allExemptedCourses as $exemptedCourse) {
                    foreach ($coursesToBeGiven as $courseToBeGiven) {
                        if ($courseToBeGiven->id == $exemptedCourse->course_id) {
                            $index = count($coursesAndGrades);
                            $coursesAndGrades[$index] = [
                                'course_title' => trim($courseToBeGiven->course_title),
                                'course_code' => trim($courseToBeGiven->course_code),
                                'course_id' => $courseToBeGiven->id,
                                'major' => $courseToBeGiven->major,
                                'credit' => $courseToBeGiven->credit,
                                'thesis' => $courseToBeGiven->thesis,
                                'elective' => $courseToBeGiven->elective,
                                'grade' => !empty($exemptedCourse->grade) ? $exemptedCourse->grade . '(EX)' : 'EX'
                            ];
                        }
                    }
                }
            }
        }

        foreach ($courseRegistered as $value) {
            if (!$this->CourseRegistrations->isCourseDropped($value->id) && !empty($value->published_course->course)) {
                $index = count($coursesAndGrades);
                $coursesAndGrades[$index] = [
                    'course_title' => trim($value->published_course->course->course_title),
                    'course_code' => trim($value->published_course->course->course_code),
                    'course_id' => $value->published_course->course->id,
                    'credit' => $value->published_course->course->credit,
                    'thesis' => $value->published_course->course->thesis,
                    'elective' => $value->published_course->course->elective
                ];

                if ($studentDetail->curriculum_id != $value->published_course->course->curriculum_id) {
                    $coursesAndGrades[$index]['major'] = TableRegistry::getTableLocator()->get('EquivalentCourses')->isEquivalentCourseMajor(
                        $value->published_course->course_id,
                        $studentDetail->curriculum_id
                    );
                } else {
                    $coursesAndGrades[$index]['major'] = $value->published_course->course->major;
                }

                $gradeDetail = $this->getApprovedGrade($value->course_registration->id, 1);

                if (!empty($gradeDetail)) {
                    $coursesAndGrades[$index]['grade'] = $gradeDetail['grade'];
                    if (isset($gradeDetail['point_value'])) {
                        $coursesAndGrades[$index]['point_value'] = $gradeDetail['point_value'];
                        $coursesAndGrades[$index]['pass_grade'] = $gradeDetail['pass_grade'];
                        $coursesAndGrades[$index]['used_in_gpa'] = $gradeDetail['used_in_gpa'];
                    } else {
                        $gradeTypeDetailsPF = TableRegistry::getTableLocator()->get('Grades')->find()
                            ->where(['Grades.id' => $gradeDetail['grade_id']])
                            ->contain(['GradeTypes'])
                            ->first();

                        if (!empty($gradeTypeDetailsPF->grade_type) && !$gradeTypeDetailsPF->grade_type->used_in_gpa) {
                            $coursesAndGrades[$index]['pass_fail_grade'] = true;
                        }
                    }
                }

                $matchingCourses = [$value->published_course->course->id => $value->published_course->course->id];
                if (!empty($studentDetail->curriculum_id)) {
                    $matchingCourses = array_merge(
                        $matchingCourses,
                        TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($value->published_course->course->id, $studentDetail->curriculum_id)
                    );
                }

                $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentId);

                if (count($registerAndAddFreq) <= 1) {
                    $coursesAndGrades[$index]['repeated_old'] = false;
                    $coursesAndGrades[$index]['repeated_new'] = false;
                } else {
                    $rept = $this->repeatationLabeling($registerAndAddFreq, 'register', $value->course_registration->id, $studentDetail, $coursesAndGrades[$index]['course_id']);
                    $coursesAndGrades[$index]['repeated_old'] = $rept['repeated_old'];
                    $coursesAndGrades[$index]['repeated_new'] = $rept['repeated_new'];
                }
            }
        }

        foreach ($courseAdded as $value) {
            $index = count($coursesAndGrades);
            $coursesAndGrades[$index] = [
                'course_title' => trim($value->published_course->course->course_title),
                'course_code' => trim($value->published_course->course->course_code),
                'course_id' => $value->published_course->course->id,
                'credit' => $value->published_course->course->credit,
                'thesis' => $value->published_course->course->thesis,
                'elective' => $value->published_course->course->elective
            ];

            if ($studentDetail->curriculum_id != $value->published_course->course->curriculum_id) {
                $coursesAndGrades[$index]['major'] = TableRegistry::getTableLocator()->get('EquivalentCourses')->isEquivalentCourseMajor(
                    $value->published_course->course_id,
                    $studentDetail->curriculum_id
                );
            } else {
                $coursesAndGrades[$index]['major'] = $value->published_course->course->major;
            }

            $gradeDetail = $this->getApprovedGrade($value->course_add->id, 0);

            if (!empty($gradeDetail)) {
                $coursesAndGrades[$index]['grade'] = $gradeDetail['grade'];
                if (isset($gradeDetail['point_value'])) {
                    $coursesAndGrades[$index]['point_value'] = $gradeDetail['point_value'];
                    $coursesAndGrades[$index]['pass_grade'] = $gradeDetail['pass_grade'];
                    $coursesAndGrades[$index]['used_in_gpa'] = $gradeDetail['used_in_gpa'];
                }
            }

            $matchingCourses = [$value->published_course->course->id => $value->published_course->course->id];
            if (!empty($studentDetail->curriculum_id)) {
                $matchingCourses = array_merge(
                    $matchingCourses,
                    TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($value->published_course->course->id, $studentDetail->curriculum_id)
                );
            }

            $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentId);

            if (count($registerAndAddFreq) <= 1) {
                $coursesAndGrades[$index]['repeated_old'] = false;
                $coursesAndGrades[$index]['repeated_new'] = false;
            } else {
                $rept = $this->repeatationLabeling($registerAndAddFreq, 'add', $value->course_add->id, $studentDetail, $coursesAndGrades[$index]['course_id']);
                $coursesAndGrades[$index]['repeated_old'] = $rept['repeated_old'];
                $coursesAndGrades[$index]['repeated_new'] = $rept['repeated_new'];
            }
        }

        return $coursesAndGrades;
    }

    public function getApprovedGrade(?int $registerAddId = null, int $registration = 1): array
    {
        $approvedGradeDetail = [];

        $gradeDetail = $this->find()
            ->where([
                $registration == 1 ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => $registerAddId,
                'ExamGrades.department_approval' => 1,
                'ExamGrades.registrar_approval' => 1
            ])
            ->order(['ExamGrades.id' => 'DESC'])
            ->first();

        if (!empty($gradeDetail)) {
            $gradeChange = $this->ExamGradeChanges->find()
                ->where([
                    'ExamGradeChanges.exam_grade_id' => $gradeDetail->id,
                    'OR' => [
                        [
                            'ExamGradeChanges.department_approval' => 1,
                            'ExamGradeChanges.registrar_approval' => 1,
                            'OR' => [
                                [
                                    'ExamGradeChanges.makeup_exam_result IS NULL',
                                    'ExamGradeChanges.college_approval' => 1
                                ],
                                ['ExamGradeChanges.makeup_exam_result IS NOT NULL']
                            ]
                        ],
                        ['ExamGradeChanges.manual_ng_conversion' => 1],
                        ['ExamGradeChanges.auto_ng_conversion' => 1]
                    ]
                ])
                ->order(['ExamGradeChanges.id' => 'DESC'])
                ->first();

            if (!empty($gradeChange)) {
                $approvedGradeDetail = [
                    'grade' => $gradeChange->grade,
                    'gradeChangeRequested' => $gradeChange->created,
                    'gradeChangeApproved' => $gradeChange->modified,
                    'gradeChangeReason' => $gradeChange->reason,
                    'gradeChangeResult' => $gradeChange->result,
                    'makeupExamResult' => $gradeChange->makeup_exam_result,
                    'manualNGConversion' => $gradeChange->manual_ng_conversion,
                    'autoNGConversion' => $gradeChange->auto_ng_conversion,
                    'grade_change_id' => $gradeChange->id,
                    'noGradeChangeRecorded' => false
                ];

                if ($gradeChange->manual_ng_conversion) {
                    $approvedGradeDetail['manualNGConvertedBy'] = TableRegistry::getTableLocator()->get('Users')
                        ->find()
                        ->where(['Users.id' => $gradeChange->manual_ng_converted_by])
                        ->select(['Users.first_name','Users.middle_name','Users.last_name'])
                        ->first()->full_name ?? '';
                }
            } else {
                $approvedGradeDetail = [
                    'grade' => $gradeDetail->grade,
                    'approved' => $gradeDetail->modified,
                    'noGradeChangeRecorded' => true
                ];
            }

            $approvedGradeDetail['grade_id'] = $gradeDetail->id;
            $approvedGradeDetail['submitted'] = $gradeDetail->created;
            $approvedGradeDetail['backdatedGradeEntry'] = (stripos($gradeDetail->registrar_reason, 'backend') !== false || $gradeDetail->registrar_reason == 'Via backend data entry interface') ? 1 : 0;
            $approvedGradeDetail['registrarGradeEntry'] = (stripos($gradeDetail->registrar_reason, 'Data Entry') !== false && $gradeDetail->registrar_reason == 'Registrar Data Entry interface') ? 1 : 0;

            if ($approvedGradeDetail['backdatedGradeEntry']) {
                $approvedGradeDetail['approved'] = $gradeDetail->modified;
            }

            $gradeRelatedDetail = $this->GradeScales->find()
                ->where(['GradeScales.id' => $gradeDetail->grade_scale_id])
                ->contain([
                    'GradeScaleDetails.Grades' => [
                        'conditions' => ['Grades.grade' => $approvedGradeDetail['grade']],
                        'GradeTypes'
                    ]
                ])
                ->first();

            if (!empty($gradeRelatedDetail->grade_scale_details)) {
                foreach ($gradeRelatedDetail->grade_scale_details as $value) {
                    if (!empty($value->grade)) {
                        $approvedGradeDetail['point_value'] = $value->grade->point_value;
                        $approvedGradeDetail['pass_grade'] = $value->grade->pass_grade;
                        $approvedGradeDetail['used_in_gpa'] = $value->grade->grade_type->used_in_gpa;
                        $approvedGradeDetail['grade_scale_id'] = $value->grade_scale_id;
                        $approvedGradeDetail['grade_type'] = $value->grade->grade_type->type;
                        $approvedGradeDetail['grade_type_id'] = $value->grade->grade_type->id;
                        $approvedGradeDetail['repeatable'] = $value->grade->allow_repetition;
                        $approvedGradeDetail['grade_scale'] = $gradeRelatedDetail->name;
                        break;
                    }
                }
            }

            if (!empty($approvedGradeDetail['grade']) && in_array(strtoupper($approvedGradeDetail['grade']), ['NG', 'DO', 'I', 'W'])) {
                $approvedGradeDetail['pass_grade'] = 0;
            }

            return $approvedGradeDetail;
        }

        return [];
    }

    public function getApprovedNotChangedGrade(?int $registerAddId = null, int $registration = 1): array
    {
        $approvedGradeDetail = [];

        $gradeDetail = $this->find()
            ->where([
                $registration == 1 ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => $registerAddId,
                'ExamGrades.department_approval' => 1,
                'ExamGrades.registrar_approval' => 1
            ])
            ->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC'])
            ->first();

        if (!empty($gradeDetail)) {
            $approvedGradeDetail = [
                'grade' => $gradeDetail->grade,
                'grade_id' => $gradeDetail->id,
                'exam_grade_grade_scale_id' => $gradeDetail->grade_scale_id,
                'noGradeChangeRecorded' => empty($gradeDetail->exam_grade_changes)
            ];

            $gradeRelatedDetail = $this->GradeScales->find()
                ->where(['GradeScales.id' => $gradeDetail->grade_scale_id])
                ->contain([
                    'GradeScaleDetails.Grades' => [
                        'conditions' => ['Grades.grade' => $approvedGradeDetail['grade']],
                        'GradeTypes'
                    ]
                ])
                ->first();

            if (!empty($gradeRelatedDetail->grade_scale_details)) {
                foreach ($gradeRelatedDetail->grade_scale_details as $value) {
                    if (!empty($value->grade)) {
                        $approvedGradeDetail['point_value'] = $value->grade->point_value ?? 0;
                        $approvedGradeDetail['pass_grade'] = $value->grade->pass_grade ?? false;
                        $approvedGradeDetail['used_in_gpa'] = $value->grade->grade_type->used_in_gpa ?? false;
                        $approvedGradeDetail['allow_repetition'] = $value->grade->allow_repetition ?? false;
                        break;
                    }
                }
            }

            return $approvedGradeDetail;
        }

        return [];
    }

    public function getApprovedGradeForMakeUpExam(?int $registerAddId = null, int $registration = 1): array
    {
        $approvedGradeDetail = [];
        $possibleAllowedRepetitionGrade = [
            PROGRAM_UNDERGRADUATE => [
                'C-' => 'C-',
                'D' => 'D',
                'F' => 'F',
                'I' => 'I',
                'Fx' => 'Fx'
            ],
            PROGRAM_POST_GRADUATE => [
                'C' => 'C',
                'C+' => 'C+',
                'D' => 'D',
                'F' => 'F',
                'I' => 'I'
            ]
        ];

        $gradeDetails = $this->find()
            ->where([
                $registration == 1 ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => $registerAddId,
                'ExamGrades.course_registration_id IS NOT NULL',
                'ExamGrades.department_approval' => 1,
                'ExamGrades.registrar_approval' => 1
            ])
            ->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC'])
            ->toArray();

        if (!empty($gradeDetails)) {
            foreach ($gradeDetails as $gradeDetail) {
                $approvedGradeDetail = [
                    'grade' => $gradeDetail->grade,
                    'grade_id' => $gradeDetail->id,
                    'exam_grade_grade_scale_id' => $gradeDetail->grade_scale_id ?? 0,
                    'haveGradeChange' => !empty($gradeDetail->exam_grade_changes)
                ];

                $gradeRelatedDetail = $this->GradeScales->find()
                    ->where(['GradeScales.id' => $gradeDetail->grade_scale_id])
                    ->contain([
                        'GradeScaleDetails.Grades' => [
                            'conditions' => ['Grades.grade' => $approvedGradeDetail['grade']],
                            'GradeTypes'
                        ]
                    ])
                    ->first();

                if (!empty($gradeRelatedDetail->grade_scale_details)) {
                    foreach ($gradeRelatedDetail->grade_scale_details as $value) {
                        if (!empty($value->grade) && (
                                ($value->grade->allow_repetition ?? false) ||
                                (!empty($possibleAllowedRepetitionGrade[$gradeRelatedDetail->program_id]) &&
                                    in_array($value->grade->grade, $possibleAllowedRepetitionGrade[$gradeRelatedDetail->program_id]))
                            )) {
                            $approvedGradeDetail['point_value'] = $value->grade->point_value ?? 0;
                            $approvedGradeDetail['pass_grade'] = $value->grade->pass_grade ?? false;
                            $approvedGradeDetail['used_in_gpa'] = $value->grade->grade_type->used_in_gpa ?? false;
                            $approvedGradeDetail['allow_repetition'] = $value->grade->allow_repetition ?? false;

                            if ($approvedGradeDetail['grade'] == 'NG' && $approvedGradeDetail['haveGradeChange']) {
                                $approvedGradeDetail['allow_repetition'] = false;
                            }

                            return $approvedGradeDetail;
                        }
                    }
                }
            }
            return $approvedGradeDetail;
        }

        return [];
    }

    public function getGradeForStats(?int $registerAddId = null, int $registration = 1): array
    {
        $approvedGradeDetail = [];

        $gradeDetail = $this->find()
            ->where([$registration == 1 ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => $registerAddId])
            ->order(['ExamGrades.created' => 'DESC'])
            ->first();

        if (!empty($gradeDetail)) {
            $gradeChange = $this->ExamGradeChanges->find()
                ->where([
                    'ExamGradeChanges.exam_grade_id' => $gradeDetail->id,
                    'OR' => [
                        [
                            'OR' => [
                                [
                                    'ExamGradeChanges.makeup_exam_result IS NULL',
                                    'ExamGradeChanges.college_approval' => 1
                                ],
                                ['ExamGradeChanges.makeup_exam_result IS NOT NULL']
                            ]
                        ],
                        ['ExamGradeChanges.manual_ng_conversion' => 1],
                        ['ExamGradeChanges.auto_ng_conversion' => 1]
                    ]
                ])
                ->order(['ExamGradeChanges.created' => 'DESC'])
                ->first();

            $approvedGradeDetail['grade'] = !empty($gradeChange) ? $gradeChange->grade : $gradeDetail->grade;
            $approvedGradeDetail['grade_id'] = $gradeDetail->id;

            $gradeRelatedDetail = $this->GradeScales->find()
                ->where(['GradeScales.id' => $gradeDetail->grade_scale_id])
                ->contain([
                    'GradeScaleDetails.Grades' => [
                        'conditions' => ['Grades.grade' => $approvedGradeDetail['grade']],
                        'GradeTypes'
                    ]
                ])
                ->first();

            if (!empty($gradeRelatedDetail->grade_scale_details)) {
                foreach ($gradeRelatedDetail->grade_scale_details as $value) {
                    if (!empty($value->grade)) {
                        $approvedGradeDetail['point_value'] = $value->grade->point_value;
                        $approvedGradeDetail['pass_grade'] = $value->grade->pass_grade;
                        $approvedGradeDetail['used_in_gpa'] = $value->grade->grade_type->used_in_gpa;
                        break;
                    }
                }
            }

            return $approvedGradeDetail;
        }

        return [];
    }

    public function editableExamType(int $publishedCourseId): bool
    {
        $gradeSubmittedCountR = 0;
        $gradeSubmittedCountA = 0;

        $registIds = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.published_course_id' => $publishedCourseId])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        $addIds = $this->CourseAdds->find('list')
            ->where(['CourseAdds.published_course_id' => $publishedCourseId])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($registIds)) {
            $gradeSubmittedCountR = $this->find()
                ->where([
                    'ExamGrades.course_registration_id IN' => $registIds,
                    'ExamGrades.department_approval' => 1
                ])
                ->count();
        }

        if (!empty($addIds)) {
            $gradeSubmittedCountA = $this->find()
                ->where([
                    'ExamGrades.course_add_id IN' => $addIds,
                    'ExamGrades.department_approval' => 1
                ])
                ->count();
        }

        return $gradeSubmittedCountR > 0 || $gradeSubmittedCountA > 0;
    }

    public function getExamType(?int $registerAddId = null, int $registration = 1): array
    {
        $examResult = [];

        $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
        $examResult = $examResultsTable->find()
            ->where([
                $registration == 1 ? ['ExamResults.course_registration_id' => $registerAddId] : ['ExamResults.course_registration_id' => $registerAddId, 'course_add' => 1]
            ])
            ->contain(['ExamTypes' => ['sort' => ['ExamTypes.order' => 'ASC']]])
            ->toArray();

        return $examResult;
    }

    public function getStudentsWithNG(?int $publishedCourseId = null): array
    {
        $studentsWithNg = [];

        if (!empty($publishedCourseId)) {
            $publishedCourseTable= TableRegistry::getTableLocator()->get('PublishedCourses');
            $studentCourseRegisterAndAdds = $publishedCourseTable->getStudentsTakingPublishedCourse($publishedCourseId);

            foreach ($studentCourseRegisterAndAdds as $registerAddMakeup) {
                foreach ($registerAddMakeup as $value) {

                    if ($value->student->graduated == 0) {
                        $grade = [];

                        if ( $value instanceof \App\Model\Entity\CourseRegistration && !empty($value->id) && is_numeric($value->id) && $value->id > 0) {
                            $grade = $this->getApprovedGrade($value->id, 1);
                        } elseif ($value instanceof \App\Model\Entity\CourseAdd  && !empty($value->id)
                            && is_numeric($value->id) && $value->id > 0) {
                            $grade = $this->getApprovedGrade($value->id, 0);
                        }

                        if (!empty($grade) && strcasecmp($grade['grade'], 'NG') == 0
                            && !empty($grade['noGradeChangeRecorded'])) {
                            $index = count($studentsWithNg);
                            $studentsWithNg[$index] = [
                                'full_name' => $value->student->full_name,
                                'studentnumber' => $value->student->studentnumber,
                                'gender' => $value->student->gender,
                                'grade_id' => $grade['grade_id'],
                                'grade' => $grade['grade'],
                                'haveAssesmentData' => !empty($value->exam_results)
                            ];
                        }
                    }
                }
            }
        }

        return $studentsWithNg;
    }

    public function getStudentsWithFX(?int $publishedCourseId = null, bool $fxSelectedByStudent = false): array
    {
        $studentsWithFx = [];

        if (!empty($publishedCourseId)) {
            $studentCourseRegisterAndAdds = $fxSelectedByStudent
                ? $this->CourseRegistrations->PublishedCourses->getStudentsTakingFxExamPublishedCourse($publishedCourseId)
                : $this->CourseRegistrations->PublishedCourses->getStudentsTakingPublishedCourse($publishedCourseId);

            foreach ($studentCourseRegisterAndAdds as $registerAddMakeup) {
                foreach ($registerAddMakeup as $value) {
                    if ($value['Student']['graduated'] == 0) {
                        $grade = [];

                        if (!empty($value['CourseRegistration']) && is_numeric($value['CourseRegistration']['id']) && $value['CourseRegistration']['id'] > 0) {
                            $grade = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                        } elseif (!empty($value['CourseAdd']) && is_numeric($value['CourseAdd']['id']) && $value['CourseAdd']['id'] > 0) {
                            $grade = $this->getApprovedGrade($value['CourseAdd']['id'], 0);
                        }

                        if (!empty($grade) && strcasecmp($grade['grade'], 'Fx') == 0 && !empty($grade['noGradeChangeRecorded'])) {
                            $studentsWithFx[$value['Student']['id']] = [
                                'full_name' => $value['Student']['first_name'] . ' ' . $value['Student']['middle_name'] . ' ' . $value['Student']['last_name'],
                                'studentnumber' => $value['Student']['studentnumber'],
                                'student_id' => $value['Student']['id'],
                                'grade_id' => $grade['grade_id'],
                                'gender' => $value['Student']['gender'],
                                'p_c_id' => $publishedCourseId ?? 0,
                                'grade' => $grade['grade']
                            ];

                            if (!empty($value['CourseRegistration']) && is_numeric($value['CourseRegistration']['id']) && $value['CourseRegistration']['id'] > 0) {
                                $studentsWithFx[$value['Student']['id']]['course_registration_id'] = $value['CourseRegistration']['id'];
                                $studentsWithFx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                            } elseif (!empty($value['CourseAdd']) && is_numeric($value['CourseAdd']['id']) && $value['CourseAdd']['id'] > 0) {
                                $studentsWithFx[$value['Student']['id']]['course_add_id'] = $value['CourseAdd']['id'];
                                $studentsWithFx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                            }
                        }
                    }
                }
            }
        }

        return $studentsWithFx;
    }

    public function getStudentsWithFXForMakeupAssignment(?int $publishedCourseId = null, bool $fxSelectedByStudent = false): array
    {
        $studentsWithFx = [];

        if ($fxSelectedByStudent && !empty($publishedCourseId)) {
            $studentCourseRegisterAndAdds = $this->CourseRegistrations->PublishedCourses->getStudentsTakingFxExamPublishedCourse($publishedCourseId);

            foreach ($studentCourseRegisterAndAdds as $registerAddMakeup) {
                foreach ($registerAddMakeup as $value) {
                    if ($value['Student']['graduated'] == 0) {
                        $grade = !empty($value['CourseRegistration']) && !empty($value['CourseRegistration']['id'])
                            ? $this->getApprovedGrade($value['CourseRegistration']['id'], 1)
                            : $this->getApprovedGrade($value['CourseAdd']['id'], 0);

                        if (!empty($grade) && strcasecmp($grade['grade'], 'Fx') == 0 && !empty($grade['noGradeChangeRecorded'])) {
                            $studentsWithFx[$value['Student']['id']] = [
                                'full_name' => $value['Student']['first_name'] . ' ' . $value['Student']['middle_name'] . ' ' . $value['Student']['last_name'],
                                'studentnumber' => $value['Student']['studentnumber'],
                                'student_id' => $value['Student']['id'],
                                'grade_id' => $grade['grade_id'],
                                'gender' => $value['Student']['gender'],
                                'p_c_id' => $publishedCourseId ?? 0,
                                'grade' => $grade['grade']
                            ];

                            if (!empty($value['CourseRegistration']) && !empty($value['CourseRegistration']['id'])) {
                                $studentsWithFx[$value['Student']['id']]['course_registration_id'] = $value['CourseRegistration']['id'];
                                $studentsWithFx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                                $studentsWithFx[$value['Student']['id']]['makeupalreadyapplied'] = TableRegistry::getTableLocator()->get('MakeupExams')->makeUpExamApplied(
                                    $value['Student']['id'],
                                    $value['PublishedCourse']['id'],
                                    $value['CourseRegistration']['id'],
                                    1
                                );
                            } elseif (!empty($value['CourseAdd']) && !empty($value['CourseAdd']['id'])) {
                                $studentsWithFx[$value['Student']['id']]['course_add_id'] = $value['CourseAdd']['id'];
                                $studentsWithFx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                                $studentsWithFx[$value['Student']['id']]['makeupalreadyapplied'] = TableRegistry::getTableLocator()->get('MakeupExams')->makeUpExamApplied(
                                    $value['Student']['id'],
                                    $value['PublishedCourse']['id'],
                                    $value['CourseAdd']['id'],
                                    0
                                );
                            }
                        }
                    }
                }
            }
        }

        return $studentsWithFx;
    }

    public function getStudentCopies(?array $studentIds = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $studentCopies = [];

        if (!empty($studentIds)) {
            foreach ($studentIds as $studentId) {
                $studentCopy = $this->getStudentCopy($studentId, $academicYear, $semester);
                if (!empty($studentCopy['courses'])) {
                    $studentCopy['University'] = TableRegistry::getTableLocator()->get('Universities')->getStudentUniversity($studentId);
                    $studentCopies[] = $studentCopy;
                }
            }
        }

        return $studentCopies;
    }

    public function getStudentCopy(?int $studentId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $studentCopy = [];

        if (!empty($studentId) && is_numeric($studentId) && $studentId > 0) {
            $studentDetail = $this->CourseAdds->Students->find()
                ->where(['Students.id' => $studentId])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname', 'equivalent_to_id']],
                    'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                    'Colleges' => [
                        'fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone'],
                        'Campuses' => ['fields' => ['id', 'name']]
                    ],
                    'Curriculums'
                ])
                ->first();

            $programTypeId = $this->CourseAdds->Students->ProgramTypeTransfers->getStudentProgramType($studentId, $academicYear, $semester);
            $programTypeDetail = $this->CourseAdds->Students->ProgramTypes->find()
                ->where(['ProgramTypes.id' => $programTypeId])
                ->first();

            $studentCopy['ProgramType'] = $programTypeDetail->toArray();
            $programTypeId = $this->CourseAdds->Students->ProgramTypes->getParentProgramType($programTypeId);
            $pattern = $this->CourseAdds->Students->ProgramTypes->StudentStatusPatterns->getProgramTypePattern(
                $studentDetail->program_id,
                $programTypeId,
                $academicYear
            );

            $ayAndSList = [[
                'academic_year' => $academicYear,
                'semester' => $semester
            ]];

            if ($pattern > 1) {
                $statusPrepared = $this->CourseAdds->Students->StudentExamStatuses->find()
                    ->where([
                        'StudentExamStatuses.student_id' => $studentId,
                        'StudentExamStatuses.academic_year' => $academicYear,
                        'StudentExamStatuses.semester' => $semester
                    ])
                    ->count();

                if ($statusPrepared == 0) {
                    $ayAndSListDraft = $this->CourseAdds->Students->StudentExamStatuses->getAcademicYearAndSemesterListToGenerateStatus(
                        $studentDetail->id,
                        $academicYear,
                        $semester
                    );
                    if (count($ayAndSListDraft) > $pattern) {
                        $ayAndSList = array_slice($ayAndSListDraft, 0, $pattern);
                    } else {
                        $ayAndSList = $ayAndSListDraft;
                    }
                } else {
                    $ayAndSList = $this->CourseAdds->Students->StudentExamStatuses->getAcademicYearAndSemesterListToUpdateStatus(
                        $studentDetail->id,
                        $academicYear,
                        $semester
                    );
                }
            }

            $options = [
                'conditions' => ['CourseRegistrations.student_id' => $studentDetail->id],
                'contain' => [
                    'PublishedCourses' => [
                        'Courses' => [
                            'Curriculums',
                            'GradeTypes.Grades'
                        ],
                        'Sections' => [
                            'fields' => ['id', 'name'],
                            'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'specialization_english_degree_nomenclature', 'english_degree_nomenclature', 'active']],
                            'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                            'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']],
                            'YearLevels' => ['fields' => ['id', 'name']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'CourseInstructorAssignments' => [
                            'fields' => ['id', 'published_course_id', 'staff_id'],
                            'Staffs' => [
                                'fields' => ['id', 'full_name'],
                                'Positions' => ['fields' => ['id', 'position']],
                                'Titles' => ['fields' => ['id', 'title']]
                            ],
                            'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                        ]
                    ],
                    'Sections' => [
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'specialization_english_degree_nomenclature', 'english_degree_nomenclature', 'active']],
                        'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']],
                        'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']],
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ]
                ],
                'order' => ['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC']
            ];

            foreach ($ayAndSList as $ayS) {
                $options['conditions']['OR'][] = [
                    'CourseRegistrations.academic_year' => $ayS['academic_year'],
                    'CourseRegistrations.semester' => $ayS['semester']
                ];
            }

            $studentCourseRegistrations = $this->CourseRegistrations->find('all', $options)->toArray();

            $options = [
                'conditions' => [
                    'CourseAdds.student_id' => $studentDetail->id,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ],
                'contain' => [
                    'PublishedCourses' => [
                        'Courses' => [
                            'Curriculums',
                            'GradeTypes.Grades'
                        ],
                        'Sections' => [
                            'fields' => ['id', 'name'],
                            'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                            'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'CourseInstructorAssignments' => [
                            'fields' => ['id', 'published_course_id', 'staff_id'],
                            'Staffs' => [
                                'fields' => ['id', 'full_name'],
                                'Positions' => ['fields' => ['id', 'position']],
                                'Titles' => ['fields' => ['id', 'title']]
                            ],
                            'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                        ]
                    ]
                ],
                'order' => ['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC']
            ];

            foreach ($ayAndSList as $ayS) {
                $options['conditions']['OR'][] = [
                    'CourseAdds.academic_year' => $ayS['academic_year'],
                    'CourseAdds.semester' => $ayS['semester']
                ];
            }

            $studentCourseAdds = $this->CourseAdds->find('all', $options)->toArray();

            $studentCopy['courses'] = [];

            foreach ($studentCourseRegistrations as $studentCourseRegistration) {
                if ($studentCourseRegistration->published_course->drop == 0 && !$this->CourseRegistrations->isCourseDropped($studentCourseRegistration->id)) {
                    $rIndex = count($studentCopy['courses']);
                    $studentCopy['courses'][$rIndex] = [
                        'Course' => $studentCourseRegistration->published_course->course,
                        'PublishedCourse' => $studentCourseRegistration->published_course,
                        'CourseRegistration' => $studentCourseRegistration,
                        'Grade' => $this->getApprovedGrade($studentCourseRegistration->id, 1),
                        'ExamType' => $this->getExamType($studentCourseRegistration->id, 1),
                        'hasEquivalentMap' => TableRegistry::getTableLocator()->get('EquivalentCourses')->checkCourseHasEquivalentCourse(
                            $studentCourseRegistration->published_course->course_id,
                            $studentDetail->curriculum_id
                        ),
                        'section' => $studentCourseRegistration->published_course->section,
                        'regAdd' => 10
                    ];

                    $courseId = $studentCourseRegistration->published_course->course->id;
                    $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse(
                        $courseId,
                        $studentDetail->curriculum_id
                    );
                    $matchingCourses[$courseId] = $courseId;

                    $studentDetailArray = ['Student' => $studentDetail->toArray()];
                    $registerAddId = $studentCourseRegistration->id;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 1;
                    } else {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 0;
                        if (!empty($courseId)) {
                            $rep = $this->repeatationLabeling($registerAndAddFreq, 'register', $registerAddId, $studentDetailArray, $courseId);
                            $studentCopy['courses'][$rIndex]['RepeatitionLabel'] = $rep;
                        }
                    }
                }
            }

            foreach ($studentCourseAdds as $studentCourseAdd) {
                if ($studentCourseAdd->published_course->drop == 0) {
                    $rIndex = count($studentCopy['courses']);
                    $studentCopy['courses'][$rIndex] = [
                        'Course' => $studentCourseAdd->published_course->course,
                        'PublishedCourse' => $studentCourseAdd->published_course,
                        'CourseAdd' => $studentCourseAdd,
                        'Grade' => $this->getApprovedGrade($studentCourseAdd->id, 0),
                        'ExamType' => $this->getExamType($studentCourseAdd->id, 0),
                        'hasEquivalentMap' => TableRegistry::getTableLocator()->get('EquivalentCourses')->checkCourseHasEquivalentCourse(
                            $studentCourseAdd->published_course->course_id,
                            $studentDetail->curriculum_id
                        ),
                        'section' => $studentCourseAdd->published_course->section,
                        'regAdd' => 11
                    ];

                    $courseId = $studentCourseAdd->published_course->course->id;
                    $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse(
                        $courseId,
                        $studentDetail->curriculum_id
                    );
                    $matchingCourses[$courseId] = $courseId;

                    $studentDetailArray = ['Student' => $studentDetail->toArray()];
                    $registerAddId = $studentCourseAdd->id;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 1;
                    } else {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 0;
                        if (!empty($courseId)) {
                            $rep = $this->repeatationLabeling($registerAndAddFreq, 'add', $registerAddId, $studentDetailArray, $courseId);
                            $studentCopy['courses'][$rIndex]['RepeatitionLabel'] = $rep;
                        }
                    }
                }
            }

            if (!empty($studentCourseRegistrations)) {
                $sectionDetail = $this->CourseAdds->Students->Sections->find()
                    ->where(['Sections.id' => $studentCourseRegistrations[0]->published_course->section_id])
                    ->contain([
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'specialization_english_degree_nomenclature', 'english_degree_nomenclature', 'active']],
                        'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']],
                        'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']],
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->first();

                $studentCopy['Section'] = $sectionDetail->toArray();
                $studentCopy['Section']['Program'] = $sectionDetail->program->toArray();
                $studentCopy['Section']['ProgramType'] = $sectionDetail->program_type->toArray();
                $studentCopy['Section']['Curriculum'] = $sectionDetail->curriculum->toArray();
                $studentCopy['Section']['College'] = $sectionDetail->college->toArray();
                $studentCopy['Section']['Department'] = $sectionDetail->department->toArray();
                $studentCopy['Section']['YearLevel'] = $sectionDetail->year_level->toArray();
                $studentCopy['YearLevel'] = $sectionDetail->year_level->toArray();
            } else {
                $studentCopy['Section'] = [];
                $studentCopy['YearLevel'] = [];
            }

            $studentCopy['Student'] = $studentDetail->toArray();
            $studentCopy['Curriculum'] = $studentDetail->curriculum->toArray();
            $studentCopy['Program'] = $studentDetail->program->toArray();
            $studentCopy['College'] = $studentDetail->college->toArray();
            $studentCopy['Department'] = $studentDetail->department->toArray();
            $studentCopy['academic_year'] = $academicYear;
            $studentCopy['semester'] = $semester;

            $studentCopy['Student']['Program'] = $studentDetail->program->toArray();
            $studentCopy['Student']['ProgramType'] = $studentDetail->program_type->toArray();
            $studentCopy['Student']['College'] = $studentDetail->college->toArray();
            $studentCopy['Student']['Department'] = $studentDetail->department->toArray();

            $studentStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                ->where([
                    'StudentExamStatuses.student_id' => $studentId,
                    'StudentExamStatuses.academic_year' => $academicYear,
                    'StudentExamStatuses.semester' => $semester
                ])
                ->contain(['AcademicStatuses'])
                ->order(['StudentExamStatuses.created' => 'DESC'])
                ->first();

            if (empty($studentStatus)) {
                $studentCopy['StudentExamStatus'] = [];
                $studentCopy['AcademicStatus'] = [];
            } else {
                $studentCopy['StudentExamStatus'] = $studentStatus->toArray();
                $studentCopy['AcademicStatus'] = $studentStatus->academic_status->toArray();
            }

            $allStudentStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                ->where(['StudentExamStatuses.student_id' => $studentId])
                ->contain(['AcademicStatuses'])
                ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.academic_year', 'StudentExamStatuses.semester'])
                ->order(['StudentExamStatuses.academic_year' => 'ASC', 'StudentExamStatuses.semester' => 'ASC'])
                ->toArray();

            $previousCreditHourSum = 0;
            $previousGradePointSum = 0;
            $previousStudentStatus = [];
            $previousAcademicStatus = [];

            foreach ($allStudentStatus as $studentStatus2) {
                if ($studentStatus2->academic_year == $academicYear && $studentStatus2->semester == $semester || empty($studentStatus)) {
                    break;
                }
                $previousCreditHourSum += $studentStatus2->credit_hour_sum;
                $previousGradePointSum += $studentStatus2->grade_point_sum;
                $previousStudentStatus = $studentStatus2->toArray();
                $previousAcademicStatus = $studentStatus2->academic_status->toArray();
            }

            if (!empty($previousStudentStatus)) {
                $studentCopy['PreviousStudentExamStatus'] = $previousStudentStatus;
                $studentCopy['PreviousAcademicStatus'] = $previousAcademicStatus;
                $studentCopy['PreviousStudentExamStatus']['previous_credit_hour_sum'] = $previousCreditHourSum;
                $studentCopy['PreviousStudentExamStatus']['previous_grade_point_sum'] = $previousGradePointSum;
            } else {
                $studentCopy['PreviousStudentExamStatus'] = [];
                $studentCopy['PreviousAcademicStatus'] = [];
            }
        }

        return $studentCopy;
    }


    public function getStudentACProfile(?int $studentId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $studentCopy = [];

        if (!empty($studentId) && is_numeric($studentId) && $studentId > 0) {
            $studentDetail = $this->CourseAdds->Students->find()
                ->where(['Students.id' => $studentId])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'equivalent_to_id']],
                    'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                    'Colleges' => [
                        'fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone'],
                        'Campuses' => ['fields' => ['id', 'name']]
                    ],
                    'Curriculums'
                ])
                ->first();

            $programTypeId = $this->CourseAdds->Students->ProgramTypeTransfers->getStudentProgramType($studentId, $academicYear, $semester);
            $programTypeDetail = $this->CourseAdds->Students->ProgramTypes->find()
                ->where(['ProgramTypes.id' => $programTypeId])
                ->first();

            $studentCopy['ProgramType'] = $programTypeDetail ? $programTypeDetail->toArray() : [];
            $programTypeId = $this->CourseAdds->Students->ProgramTypes->getParentProgramType($programTypeId);
            $pattern = $this->CourseAdds->Students->ProgramTypes->StudentStatusPatterns->getProgramTypePattern(
                $studentDetail->program_id,
                $programTypeId,
                $academicYear
            );

            $ayAndSList = [[
                'academic_year' => $academicYear,
                'semester' => $semester
            ]];

            $options = [
                'conditions' => ['CourseRegistrations.student_id' => $studentDetail->id],
                'contain' => [
                    'PublishedCourses' => [
                        'Courses' => [
                            'Curriculums',
                            'GradeTypes.Grades'
                        ],
                        'Sections' => [
                            'fields' => ['id', 'name'],
                            'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                            'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'CourseInstructorAssignments' => [
                            'fields' => ['id', 'published_course_id', 'staff_id'],
                            'Staffs' => [
                                'fields' => ['id', 'first_name', 'middle_name', 'last_name'],
                                'Positions' => ['fields' => ['id', 'position']],
                                'Titles' => ['fields' => ['id', 'title']]
                            ],
                            'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                        ]
                    ],
                    'Sections' => [
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'specialization_english_degree_nomenclature', 'english_degree_nomenclature', 'active']],
                        'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']],
                        'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ]
                ],
                'order' => ['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC']
            ];

            foreach ($ayAndSList as $ayS) {
                $options['conditions']['OR'][] = [
                    'CourseRegistrations.academic_year' => $ayS['academic_year'],
                    'CourseRegistrations.semester' => $ayS['semester']
                ];
            }

            $studentCourseRegistrations = $this->CourseRegistrations->find('all', $options)->toArray();

            $options = [
                'conditions' => [
                    'CourseAdds.student_id' => $studentDetail->id,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ],
                'contain' => [
                    'PublishedCourses' => [
                        'Courses' => [
                            'Curriculums',
                            'GradeTypes.Grades'
                        ],
                        'Sections' => [
                            'fields' => ['id', 'name'],
                            'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                            'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'CourseInstructorAssignments' => [
                            'fields' => ['id', 'published_course_id', 'staff_id'],
                            'Staffs' => [
                                'fields' => ['id', 'first_name', 'middle_name', 'last_name'],
                                'Positions' => ['fields' => ['id', 'position']],
                                'Titles' => ['fields' => ['id', 'title']]
                            ],
                            'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                        ]
                    ]
                ],
                'order' => ['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC']
            ];

            foreach ($ayAndSList as $ayS) {
                $options['conditions']['OR'][] = [
                    'CourseAdds.academic_year' => $ayS['academic_year'],
                    'CourseAdds.semester' => $ayS['semester']
                ];
            }

            $studentCourseAdds = $this->CourseAdds->find('all', $options)->toArray();

            $studentCopy['courses'] = [];

            foreach ($studentCourseRegistrations as $studentCourseRegistration) {
                if ($studentCourseRegistration->published_course->drop == 0 && !$this->CourseRegistrations->isCourseDropped($studentCourseRegistration->id)) {
                    $rIndex = count($studentCopy['courses']);
                    $studentCopy['courses'][$rIndex] = [
                        'Course' => $studentCourseRegistration->published_course->course,
                        'PublishedCourse' => $studentCourseRegistration->published_course,
                        'CourseRegistration' => $studentCourseRegistration,
                        'Grade' => $this->getApprovedGrade($studentCourseRegistration->id, 1),
                        'ExamType' => $this->getExamType($studentCourseRegistration->id, 1),
                        'hasEquivalentMap' => TableRegistry::getTableLocator()->get('EquivalentCourses')->checkCourseHasEquivalentCourse(
                            $studentCourseRegistration->published_course->course_id,
                            $studentDetail->curriculum_id
                        ),
                        'section' => $studentCourseRegistration->published_course->section,
                        'regAdd' => 10
                    ];

                    $courseId = $studentCourseRegistration->published_course->course->id;
                    $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse(
                        $courseId,
                        $studentDetail->curriculum_id
                    );
                    $matchingCourses[$courseId] = $courseId;

                    $studentDetailArray = ['Student' => $studentDetail->toArray()];
                    $registerAddId = $studentCourseRegistration->id;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 1;
                    } else {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 0;
                        if (!empty($courseId)) {
                            $rep = $this->repeatationLabeling($registerAndAddFreq, 'register', $registerAddId, $studentDetailArray, $courseId);
                            $studentCopy['courses'][$rIndex]['RepeatitionLabel'] = $rep;
                        }
                    }
                }
            }

            foreach ($studentCourseAdds as $studentCourseAdd) {
                if ($studentCourseAdd->published_course->drop == 0) {
                    $rIndex = count($studentCopy['courses']);
                    $studentCopy['courses'][$rIndex] = [
                        'Course' => $studentCourseAdd->published_course->course,
                        'PublishedCourse' => $studentCourseAdd->published_course,
                        'CourseAdd' => $studentCourseAdd,
                        'Grade' => $this->getApprovedGrade($studentCourseAdd->id, 0),
                        'ExamType' => $this->getExamType($studentCourseAdd->id, 0),
                        'hasEquivalentMap' => TableRegistry::getTableLocator()->get('EquivalentCourses')->checkCourseHasEquivalentCourse(
                            $studentCourseAdd->published_course->course_id,
                            $studentDetail->curriculum_id
                        ),
                        'section' => $studentCourseAdd->published_course->section,
                        'regAdd' => 11
                    ];

                    $courseId = $studentCourseAdd->published_course->course->id;
                    $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse(
                        $courseId,
                        $studentDetail->curriculum_id
                    );
                    $matchingCourses[$courseId] = $courseId;

                    $studentDetailArray = ['Student' => $studentDetail->toArray()];
                    $registerAddId = $studentCourseAdd->id;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 1;
                    } else {
                        $studentCopy['courses'][$rIndex]['firstTime'] = 0;
                        if (!empty($courseId)) {
                            $rep = $this->repeatationLabeling($registerAndAddFreq, 'add', $registerAddId, $studentDetailArray, $courseId);
                            $studentCopy['courses'][$rIndex]['RepeatitionLabel'] = $rep;
                        }
                    }
                }
            }

            if (!empty($studentCourseRegistrations)) {
                $sectionDetail = $this->CourseAdds->Students->Sections->find()
                    ->where(['Sections.id' => $studentCourseRegistrations[0]->published_course->section_id])
                    ->contain([
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'specialization_english_degree_nomenclature', 'english_degree_nomenclature', 'active']],
                        'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream', 'amharic_name', 'type_amharic', 'phone']],
                        'Departments' => ['fields' => ['id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone']],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->first();

                $studentCopy['Section'] = $sectionDetail ? $sectionDetail->toArray() : [];
                $studentCopy['Section']['Program'] = $sectionDetail->program ? $sectionDetail->program->toArray() : [];
                $studentCopy['Section']['ProgramType'] = $sectionDetail->program_type ? $sectionDetail->program_type->toArray() : [];
                $studentCopy['Section']['Curriculum'] = $sectionDetail->curriculum ? $sectionDetail->curriculum->toArray() : [];
                $studentCopy['Section']['College'] = $sectionDetail->college ? $sectionDetail->college->toArray() : [];
                $studentCopy['Section']['Department'] = $sectionDetail->department ? $sectionDetail->department->toArray() : [];
                $studentCopy['Section']['YearLevel'] = $sectionDetail->year_level ? $sectionDetail->year_level->toArray() : [];
                $studentCopy['YearLevel'] = $sectionDetail->year_level ? $sectionDetail->year_level->toArray() : [];
            } else {
                $studentCopy['Section'] = [];
                $studentCopy['YearLevel'] = [];
            }

            $studentCopy['Student'] = $studentDetail->toArray();
            $studentCopy['Curriculum'] = $studentDetail->curriculum ? $studentDetail->curriculum->toArray() : [];
            $studentCopy['Program'] = $studentDetail->program ? $studentDetail->program->toArray() : [];
            $studentCopy['College'] = $studentDetail->college ? $studentDetail->college->toArray() : [];
            $studentCopy['Department'] = $studentDetail->department ? $studentDetail->department->toArray() : [];
            $studentCopy['academic_year'] = $academicYear;
            $studentCopy['semester'] = $semester;

            $studentCopy['Student']['Program'] = $studentDetail->program ? $studentDetail->program->toArray() : [];
            $studentCopy['Student']['ProgramType'] = $studentDetail->program_type ? $studentDetail->program_type->toArray() : [];
            $studentCopy['Student']['College'] = $studentDetail->college ? $studentDetail->college->toArray() : [];
            $studentCopy['Student']['Department'] = $studentDetail->department ? $studentDetail->department->toArray() : [];

            $studentStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                ->where([
                    'StudentExamStatuses.student_id' => $studentId,
                    'StudentExamStatuses.academic_year' => $academicYear,
                    'StudentExamStatuses.semester' => $semester
                ])
                ->contain(['AcademicStatuses'])
                ->order(['StudentExamStatuses.academic_year' => 'ASC', 'StudentExamStatuses.semester' => 'ASC', 'StudentExamStatuses.id' => 'DESC'])
                ->first();

            if (empty($studentStatus)) {
                $studentCopy['StudentExamStatus'] = [];
                $studentCopy['AcademicStatus'] = [];
            } else {
                $studentCopy['StudentExamStatus'] = $studentStatus->toArray();
                $studentCopy['AcademicStatus'] = $studentStatus->academic_status ? $studentStatus->academic_status->toArray() : [];
            }

            $allStudentStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                ->where(['StudentExamStatuses.student_id' => $studentId])
                ->contain(['AcademicStatuses'])
                ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.academic_year', 'StudentExamStatuses.semester'])
                ->order(['StudentExamStatuses.academic_year' => 'ASC', 'StudentExamStatuses.semester' => 'ASC', 'StudentExamStatuses.id' => 'DESC'])
                ->toArray();

            $previousCreditHourSum = 0;
            $previousGradePointSum = 0;
            $previousStudentStatus = [];
            $previousAcademicStatus = [];

            foreach ($allStudentStatus as $studentStatus2) {
                if ($studentStatus2->academic_year == $academicYear && $studentStatus2->semester == $semester || empty($studentStatus)) {
                    break;
                }
                $previousCreditHourSum += $studentStatus2->credit_hour_sum;
                $previousGradePointSum += $studentStatus2->grade_point_sum;
                $previousStudentStatus = $studentStatus2->toArray();
                $previousAcademicStatus = $studentStatus2->academic_status ? $studentStatus2->academic_status->toArray() : [];
            }

            if (!empty($previousStudentStatus)) {
                $studentCopy['PreviousStudentExamStatus'] = $previousStudentStatus;
                $studentCopy['PreviousAcademicStatus'] = $previousAcademicStatus;
                $studentCopy['PreviousStudentExamStatus']['previous_credit_hour_sum'] = $previousCreditHourSum;
                $studentCopy['PreviousStudentExamStatus']['previous_grade_point_sum'] = $previousGradePointSum;
            } else {
                $studentCopy['PreviousStudentExamStatus'] = [];
                $studentCopy['PreviousAcademicStatus'] = [];
            }
        }

        return $studentCopy;
    }

    public function getMasterSheet(?int $sectionId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $studentsAndGrades = [];
        $registeredCourses = [];
        $addedCourses = [];

        $studentsInSection = $this->CourseRegistrations->Students->Sections->StudentsSections->find()
            ->where(['StudentsSections.section_id' => $sectionId])
            ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
            ->toArray();

        $studentsInSectionIds = $this->CourseRegistrations->Students->Sections->StudentsSections->find('list')
            ->where(['StudentsSections.section_id' => $sectionId])
            ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
            ->select(['StudentsSections.student_id'])
            ->toArray();

        $studentRegisteredCourseForSection = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.section_id' => $sectionId])
            ->select(['CourseRegistrations.student_id', 'CourseRegistrations.section_id'])
            ->toArray();

        $count = count($studentsInSection);

        if (!empty($studentRegisteredCourseForSection)) {
            foreach ($studentRegisteredCourseForSection as $stuId => $sectId) {
                if (!in_array($stuId, $studentsInSectionIds) && $sectId == $sectionId) {
                    $studentsInSection[$count]['StudentsSection'] = [
                        'student_id' => $stuId,
                        'section_id' => $sectId
                    ];
                    $count++;
                }
            }
        }

        if (!empty($studentsInSection)) {
            foreach ($studentsInSection as $sectionStudent) {
                $studentDetail = $this->CourseAdds->Students->find()
                    ->where(['Students.id' => $sectionStudent['StudentsSection']['student_id']])
                    ->first();

                $programTypeId = $this->CourseAdds->Students->ProgramTypeTransfers->getStudentProgramType(
                    $studentDetail->id,
                    $academicYear,
                    $semester
                );
                $programTypeId = $this->CourseAdds->Students->ProgramTypes->getParentProgramType($programTypeId);
                $pattern = $this->CourseAdds->Students->ProgramTypes->StudentStatusPatterns->getProgramTypePattern(
                    $studentDetail->program_id,
                    $programTypeId,
                    $academicYear
                );

                $ayAndSList = [];

                if ($pattern <= 1) {
                    $ayAndSList[0] = [
                        'academic_year' => $academicYear,
                        'semester' => $semester
                    ];
                } else {
                    $statusPrepared = $this->CourseAdds->Students->StudentExamStatuses->find()
                        ->where([
                            'StudentExamStatuses.student_id' => $studentDetail->id,
                            'StudentExamStatuses.academic_year' => $academicYear,
                            'StudentExamStatuses.semester' => $semester
                        ])
                        ->count();

                    if ($statusPrepared == 0) {
                        $ayAndSListDraft = $this->CourseAdds->Students->StudentExamStatuses->getAcademicYearAndSemesterListToGenerateStatus(
                            $studentDetail->id,
                            $academicYear,
                            $semester
                        );
                        if (count($ayAndSListDraft) > $pattern) {
                            for ($i = 0; $i < $pattern; $i++) {
                                $ayAndSList[$i] = $ayAndSListDraft[$i];
                            }
                        } else {
                            $ayAndSList = $ayAndSListDraft;
                        }
                    } else {
                        $ayAndSList = $this->CourseAdds->Students->StudentExamStatuses->getAcademicYearAndSemesterListToUpdateStatus(
                            $studentDetail->id,
                            $academicYear,
                            $semester
                        );
                    }
                }

                $options = [
                    'conditions' => ['CourseRegistrations.student_id' => $studentDetail->id],
                    'contain' => ['PublishedCourses.Courses']
                ];

                if (!empty($ayAndSList)) {
                    foreach ($ayAndSList as $ayS) {
                        $options['conditions']['OR'][] = [
                            'CourseRegistrations.academic_year' => $ayS['academic_year'],
                            'CourseRegistrations.semester' => $ayS['semester']
                        ];
                    }
                }

                $studentCourseRegistrations = $this->CourseRegistrations->find('all', $options)->toArray();

                $options = [
                    'conditions' => [
                        'CourseAdds.student_id' => $studentDetail->id,
                        'CourseAdds.department_approval' => 1,
                        'CourseAdds.registrar_confirmation' => 1
                    ],
                    'contain' => ['PublishedCourses.Courses']
                ];

                if (!empty($ayAndSList)) {
                    foreach ($ayAndSList as $ayS) {
                        $options['conditions']['OR'][] = [
                            'CourseAdds.academic_year' => $ayS['academic_year'],
                            'CourseAdds.semester' => $ayS['semester']
                        ];
                    }
                }

                $studentCourseAdds = $this->CourseAdds->find('all', $options)->toArray();

                foreach ($studentCourseRegistrations as $studentCourseRegistration) {
                    if ($studentCourseRegistration->published_course->drop == 0) {
                        foreach ($registeredCourses as $registeredCourse) {
                            if ($registeredCourse['id'] == $studentCourseRegistration->published_course->course->id) {
                                continue 2;
                            }
                        }

                        $rIndex = count($registeredCourses);
                        $registeredCourses[$rIndex] = [
                            'id' => $studentCourseRegistration->published_course->course->id,
                            'course_title' => $studentCourseRegistration->published_course->course->course_title,
                            'course_id' => $studentCourseRegistration->published_course->course->id,
                            'course_code' => $studentCourseRegistration->published_course->course->course_code,
                            'credit' => $studentCourseRegistration->published_course->course->credit,
                            'published_course_id' => $studentCourseRegistration->published_course_id
                        ];
                    }
                }

                foreach ($studentCourseAdds as $studentCourseAdd) {
                    if ($studentCourseAdd->published_course->drop == 0) {
                        foreach ($addedCourses as $addedCourse) {
                            if ($addedCourse['id'] == $studentCourseAdd->published_course->course->id) {
                                continue 2;
                            }
                        }

                        $rIndex = count($addedCourses);
                        $addedCourses[$rIndex] = [
                            'id' => $studentCourseAdd->published_course->course->id,
                            'course_title' => $studentCourseAdd->published_course->course->course_title,
                            'course_code' => $studentCourseAdd->published_course->course->course_code,
                            'course_id' => $studentCourseAdd->published_course->course->id,
                            'credit' => $studentCourseAdd->published_course->course->credit,
                            'published_course_id' => $studentCourseAdd->published_course_id
                        ];
                    }
                }
            }
        }

        if (!empty($studentsInSection)) {
            foreach ($studentsInSection as $value) {
                $previousAyAndSList = $this->getListOfAyAndSemester($value['StudentsSection']['student_id'], $academicYear, $semester);
                $index = count($studentsAndGrades);

                $studentDetail = $this->CourseRegistrations->Students->find()
                    ->where(['Students.id' => $value['StudentsSection']['student_id']])
                    ->first();

                $studentsAndGrades[$index] = [
                    'full_name' => $studentDetail->first_name . ' ' . $studentDetail->middle_name . ' ' . $studentDetail->last_name,
                    'studentnumber' => $studentDetail->studentnumber,
                    'gender' => $studentDetail->gender
                ];

                $studentStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                    ->where([
                        'StudentExamStatuses.student_id' => $value['StudentsSection']['student_id'],
                        'StudentExamStatuses.academic_year' => $academicYear,
                        'StudentExamStatuses.semester' => $semester
                    ])
                    ->contain(['AcademicStatuses'])
                    ->first();

                if (!empty($studentStatus)) {
                    $studentsAndGrades[$index]['StudentExamStatus'] = $studentStatus->toArray();
                    $studentsAndGrades[$index]['AcademicStatus'] = $studentStatus->academic_status ? $studentStatus->academic_status->toArray() : [];
                } else {
                    $studentsAndGrades[$index]['StudentExamStatus'] = [];
                    $studentsAndGrades[$index]['AcademicStatus'] = [];
                }

                $allStudentStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                    ->where(['StudentExamStatuses.student_id' => $value['StudentsSection']['student_id']])
                    ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.academic_year', 'StudentExamStatuses.semester'])
                    ->order(['StudentExamStatuses.academic_year' => 'ASC', 'StudentExamStatuses.semester' => 'ASC'])
                    ->toArray();

                $previousCreditHourSum = 0;
                $previousGradePointSum = 0;
                $previousStudentStatus = [];

                foreach ($allStudentStatus as $studentStatus2) {
                    if ($studentStatus2->academic_year == $academicYear && $studentStatus2->semester == $semester) {
                        break;
                    }
                    $previousCreditHourSum += $studentStatus2->credit_hour_sum;
                    $previousGradePointSum += $studentStatus2->grade_point_sum;
                    $previousStudentStatus = $studentStatus2->toArray();
                }

                if (!empty($previousStudentStatus)) {
                    $studentsAndGrades[$index]['PreviousStudentExamStatus'] = $previousStudentStatus;
                    $studentsAndGrades[$index]['PreviousStudentExamStatus']['previous_credit_hour_sum'] = $previousCreditHourSum;
                    $studentsAndGrades[$index]['PreviousStudentExamStatus']['previous_grade_point_sum'] = $previousGradePointSum;
                } else {
                    $studentsAndGrades[$index]['PreviousStudentExamStatus'] = [];
                }

                if (!empty($registeredCourses)) {
                    foreach ($registeredCourses as $registeredCourse) {
                        $registrationId = $this->CourseRegistrations->find()
                            ->where([
                                'CourseRegistrations.published_course_id' => $registeredCourse['published_course_id'],
                                'CourseRegistrations.student_id' => $value['StudentsSection']['student_id']
                            ])
                            ->select(['CourseRegistrations.id'])
                            ->first();

                        if (!empty($registrationId)) {
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']] = $this->getApprovedGrade($registrationId->id, 1);
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['credit'] = $registeredCourse['credit'];
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['registered'] = true;
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['droped'] = $this->CourseRegistrations->isCourseDropped($registrationId->id);
                        } else {
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']] = [];
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['registered'] = false;
                        }
                    }
                }

                if (!empty($addedCourses)) {
                    foreach ($addedCourses as $addedCourse) {
                        $addId = $this->CourseAdds->find()
                            ->where([
                                'CourseAdds.published_course_id' => $addedCourse['published_course_id'],
                                'CourseAdds.student_id' => $value['StudentsSection']['student_id'],
                                'CourseAdds.department_approval' => 1,
                                'CourseAdds.registrar_confirmation' => 1
                            ])
                            ->select(['CourseAdds.id'])
                            ->first();

                        if (!empty($addId)) {
                            $studentsAndGrades[$index]['courses']['a-' . $addedCourse['id']] = $this->getApprovedGrade($addId->id, 0);
                            $studentsAndGrades[$index]['courses']['a-' . $addedCourse['id']]['credit'] = $addedCourse['credit'];
                            $studentsAndGrades[$index]['courses']['a-' . $addedCourse['id']]['added'] = true;
                        } else {
                            $studentsAndGrades[$index]['courses']['a-' . $addedCourse['id']] = [];
                            $studentsAndGrades[$index]['courses']['a-' . $addedCourse['id']]['added'] = false;
                        }
                    }
                }

                $allAySListForDeductionCalc = $previousAyAndSList;
                $dedIndex = count($allAySListForDeductionCalc);
                $allAySListForDeductionCalc[$dedIndex] = [
                    'academic_year' => $academicYear,
                    'semester' => $semester
                ];

                $creditAndPointDeduction = $this->getTotalCreditAndPointDeduction($value['StudentsSection']['student_id'], $allAySListForDeductionCalc);

                $studentsAndGrades[$index]['deduct_credit'] = $creditAndPointDeduction['deduct_credit_hour_sum'];
                $studentsAndGrades[$index]['deduct_gp'] = $creditAndPointDeduction['deduct_grade_point_sum'];
            }
        }

        return [
            'registered_courses' => $registeredCourses,
            'added_courses' => $addedCourses,
            'students_and_grades' => $studentsAndGrades
        ];
    }

    public function studentCopy(array $studentIds = []): array
    {
        $studentCopyArray = [];

        if (!empty($studentIds)) {
            foreach ($studentIds as $studentId) {
                $studentCopy = [];

                $studentDetail = $this->CourseRegistrations->Students->find()
                    ->where(['Students.id' => $studentId])
                    ->contain([
                        'Curriculums',
                        'GraduateLists',
                        'GraduationWorks' => ['sort' => ['GraduationWorks.created' => 'DESC']],
                        'ExitExams' => ['sort' => ['ExitExams.exam_date' => 'DESC', 'ExitExams.id' => 'DESC']],
                        'Programs',
                        'ProgramTypes',
                        'HighSchoolEducationBackgrounds' => [
                            'conditions' => ['HighSchoolEducationBackgrounds.national_exam_taken' => 1],
                            'Regions' => [
                                'fields' => ['id', 'name', 'country_id'],
                                'Countries' => ['fields' => ['id', 'name']]
                            ]
                        ],
                        'HigherEducationBackgrounds',
                        'EslceResults',
                        'EheeceResults',
                        'Departments',
                        'Colleges',
                        'Countries' => ['fields' => ['id', 'name']],
                        'Regions' => [
                            'fields' => ['id', 'name', 'country_id'],
                            'Countries' => ['fields' => ['id', 'name']]
                        ],
                        'AcceptedStudents' => [
                            'fields' => ['id', 'studentnumber', 'high_school', 'moeadmissionnumber', 'region_id', 'academicyear'],
                            'Regions' => [
                                'fields' => ['id', 'name', 'country_id'],
                                'Countries' => ['fields' => ['id', 'name']]
                            ]
                        ],
                        'EslceResults' => ['sort' => ['EslceResults.exam_year' => 'DESC']],
                        'EheeceResults' => ['sort' => ['EheeceResults.exam_year' => 'DESC']]
                    ])
                    ->first();

                $universityDetail = TableRegistry::getTableLocator()->get('Universities')->getStudentUniversity($studentId);
                $transcriptFooterDetail = $this->CourseRegistrations->Students->Programs->TranscriptFooters->getStudentTranscriptFooter($studentId);

                $auth = $this->getController()->Authentication->getIdentity();
                $recentCode = TableRegistry::getTableLocator()->get('CertificateVerificationCodes')->find()
                    ->where([
                        'CertificateVerificationCodes.student_id' => $studentId,
                        'CertificateVerificationCodes.user' => $auth->full_name,
                        'CertificateVerificationCodes.type' => 'Student Copy'
                    ])
                    ->first();

                if ($recentCode) {
                    $code = $recentCode->code;
                } else {
                    $code = TableRegistry::getTableLocator()->get('CertificateVerificationCodes')->generateCode('SC');
                    $verification = TableRegistry::getTableLocator()->get('CertificateVerificationCodes')->newEntity([
                        'user' => $auth->full_name,
                        'student_id' => $studentId,
                        'type' => 'Student Copy',
                        'code' => $code
                    ]);
                    TableRegistry::getTableLocator()->get('CertificateVerificationCodes')->save($verification);
                }

                $exitExam = [];

                if ($studentDetail->program_id == 1) {
                    $approvedExitExamGrade = $this->getApprovedExitExamGrade($studentId);

                    if (!empty($approvedExitExamGrade['grade'])) {
                        $exitExam['course'] = $approvedExitExamGrade['Course']['course_code_title'];
                        $gradeMap = [
                            'p' => 'Pass',
                            'pass' => 'Pass',
                            'f' => 'Fail',
                            'fail' => 'Fail'
                        ];
                        $gradeForDocument = $gradeMap[strtolower($approvedExitExamGrade['grade'])] ?? '---';

                        $exitExamResult = TableRegistry::getTableLocator()->get('ExitExams')->find()
                            ->where(['ExitExams.student_id' => $studentId])
                            ->order(['ExitExams.exam_date' => 'DESC', 'ExitExams.id' => 'DESC'])
                            ->first();

                        if ($exitExamResult) {
                            $gradeForDocument .= ' (' . $exitExamResult->result . '%)';
                            $exitExam['exam_date'] = $exitExamResult->exam_date;
                            $exitExam['result'] = $exitExamResult->result;
                        }

                        if (!empty($exitExamResult->result) && is_numeric($exitExamResult->result)) {
                            $gradeForDocument = (int)$exitExamResult->result < 50
                                ? 'Fail (' . $exitExamResult->result . '%)'
                                : 'Pass (' . $exitExamResult->result . '%)';
                        }

                        $exitExam['result_formated'] = $gradeForDocument;
                    } else {
                        $exitExamResult = TableRegistry::getTableLocator()->get('ExitExams')->find()
                            ->where(['ExitExams.student_id' => $studentId])
                            ->order(['ExitExams.exam_date' => 'DESC', 'ExitExams.id' => 'DESC'])
                            ->first();

                        if ($exitExamResult && !empty($exitExamResult->result)) {
                            $gradeForDocument = $exitExamResult->result >= 50
                                ? 'Pass (' . $exitExamResult->result . '%)'
                                : 'Fail (' . $exitExamResult->result . '%)';
                            $exitExam['exam_date'] = $exitExamResult->exam_date;
                            $exitExam['result'] = $exitExamResult->result;
                            $exitExam['result_formated'] = $gradeForDocument;
                        }
                    }
                }

                $studentCopy['student_detail'] = [
                    'Student' => array_merge($studentDetail->toArray(), ['code' => $code]),
                    'Country' => $studentDetail->country ? $studentDetail->country->toArray() : [],
                    'Region' => $studentDetail->region ? $studentDetail->region->toArray() : [],
                    'AcceptedStudent' => $studentDetail->accepted_student ? $studentDetail->accepted_student->toArray() : [],
                    'GraduationWork' => !empty($studentDetail->graduation_works) ? $studentDetail->graduation_works[0]->toArray() : [],
                    'Curriculum' => $studentDetail->curriculum ? $studentDetail->curriculum->toArray() : [],
                    'University' => $universityDetail,
                    'TranscriptFooter' => $transcriptFooterDetail,
                    'College' => $studentDetail->college ? $studentDetail->college->toArray() : [],
                    'Department' => $studentDetail->department ? $studentDetail->department->toArray() : [],
                    'Program' => $studentDetail->program ? $studentDetail->program->toArray() : [],
                    'ProgramType' => $studentDetail->program_type ? $studentDetail->program_type->toArray() : [],
                    'HighSchoolEducationBackground' => $studentDetail->high_school_education_backgrounds,
                    'HigherEducationBackground' => $studentDetail->higher_education_backgrounds,
                    'EslceResult' => $studentDetail->eslce_results,
                    'EheeceResult' => $studentDetail->eheece_results,
                    'GraduateList' => $studentDetail->graduate_list ? $studentDetail->graduate_list->toArray() : [],
                    'ExemptionList' => $this->CourseRegistrations->Students->CourseExemptions->studentExemptedCourseList($studentId),
                    'ExitExam' => $exitExam
                ];

                $studentCopy['student_detail']['GraduationStatus'] = !empty($studentDetail->graduate_list->id)
                    ? TableRegistry::getTableLocator()->get('GraduationStatuses')->getStudentGraduationStatus($studentId)
                    : null;

                $firstRegistration = $this->CourseRegistrations->find()
                    ->where(['CourseRegistrations.student_id' => $studentId])
                    ->order(['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'])
                    ->first();

                $firstAdd = $this->CourseAdds->find()
                    ->where([
                        'CourseAdds.student_id' => $studentId,
                        'CourseAdds.department_approval' => 1,
                        'CourseAdds.registrar_confirmation' => 1
                    ])
                    ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
                    ->first();

                $firstAySe = [];

                if (!empty($firstRegistration) && empty($firstAdd)) {
                    $firstAySe = [
                        'academic_year' => $firstRegistration->academic_year,
                        'semester' => $firstRegistration->semester
                    ];
                } elseif (empty($firstRegistration) && !empty($firstAdd)) {
                    $firstAySe = [
                        'academic_year' => $firstAdd->academic_year,
                        'semester' => $firstAdd->semester
                    ];
                } elseif (empty($firstRegistration) && empty($firstAdd)) {
                    $firstAySe = [
                        'academic_year' => null,
                        'semester' => null
                    ];
                } elseif (substr($firstRegistration->academic_year, 0, 4) > substr($firstAdd->academic_year, 0, 4)) {
                    $firstAySe = [
                        'academic_year' => $firstAdd->academic_year,
                        'semester' => $firstAdd->semester
                    ];
                } else {
                    $firstAySe = [
                        'academic_year' => $firstRegistration->academic_year,
                        'semester' => $firstRegistration->semester
                    ];
                }

                $lastRegistration = $this->CourseRegistrations->find()
                    ->where(['CourseRegistrations.student_id' => $studentId])
                    ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.id' => 'DESC'])
                    ->first();

                $lastAdd = $this->CourseAdds->find()
                    ->where([
                        'CourseAdds.student_id' => $studentId,
                        'CourseAdds.department_approval' => 1,
                        'CourseAdds.registrar_confirmation' => 1
                    ])
                    ->order(['CourseAdds.academic_year' => 'DESC', 'CourseAdds.semester' => 'DESC', 'CourseAdds.id' => 'DESC'])
                    ->first();

                $lastAySe = [];

                if (!empty($lastRegistration) && empty($lastAdd)) {
                    $lastAySe = [
                        'academic_year' => $lastRegistration->academic_year,
                        'semester' => $lastRegistration->semester
                    ];
                } elseif (empty($lastRegistration) && !empty($lastAdd)) {
                    $lastAySe = [
                        'academic_year' => $lastAdd->academic_year,
                        'semester' => $lastAdd->semester
                    ];
                } elseif (empty($lastRegistration) && empty($lastAdd)) {
                    $lastAySe = [
                        'academic_year' => null,
                        'semester' => null
                    ];
                } elseif (substr($lastRegistration->academic_year, 0, 4) < substr($lastAdd->academic_year, 0, 4)) {
                    $lastAySe = [
                        'academic_year' => $lastAdd->academic_year,
                        'semester' => $lastAdd->semester
                    ];
                } else {
                    $lastAySe = [
                        'academic_year' => $lastRegistration->academic_year,
                        'semester' => $lastRegistration->semester
                    ];
                }

                if (!empty($firstAySe['academic_year']) && !empty($firstAySe['semester'])) {
                    $nextAySe = $this->CourseRegistrations->Students->StudentExamStatuses->getPreviousSemester(
                        $firstAySe['academic_year'],
                        $firstAySe['semester']
                    );
                    $studentCopy['courses_taken'] = [];

                    do {
                        $nextAySe = $this->CourseRegistrations->Students->StudentExamStatuses->getNextSemester(
                            $nextAySe['academic_year'],
                            $nextAySe['semester']
                        );

                        $index = count($studentCopy['courses_taken']);
                        $studentCopy['courses_taken'][$index] = [
                            'academic_year' => $nextAySe['academic_year'],
                            'semester' => $nextAySe['semester'],
                            'readmitted' => $this->CourseRegistrations->Students->Readmissions->isReadmitted(
                                $studentId,
                                $nextAySe['academic_year'],
                                $nextAySe['semester']
                            )
                        ];

                        $examStatus = $this->CourseRegistrations->Students->StudentExamStatuses->find()
                            ->where([
                                'StudentExamStatuses.student_id' => $studentId,
                                'StudentExamStatuses.academic_year' => $nextAySe['academic_year'],
                                'StudentExamStatuses.semester' => $nextAySe['semester']
                            ])
                            ->contain(['AcademicStatuses'])
                            ->order(['StudentExamStatuses.created' => 'DESC'])
                            ->first();

                        $studentCopy['courses_taken'][$index]['status'] = $examStatus ? $examStatus->toArray() : null;
                        $studentCopy['courses_taken'][$index]['courses_and_grades'] = $this->getStudentCoursesAndFinalGrade(
                            $studentId,
                            $nextAySe['academic_year'],
                            $nextAySe['semester'],
                            0
                        );

                        if (empty($studentCopy['courses_taken'][$index]['courses_and_grades'])) {
                            unset($studentCopy['courses_taken'][$index]);
                        }
                    } while (!($lastAySe['academic_year'] == $nextAySe['academic_year'] && $lastAySe['semester'] == $nextAySe['semester']));
                }

                $studentCopyArray[] = $studentCopy;
            }
        }

        return $studentCopyArray;
    }

    public function getListOfAyAndSemester(?int $studentId = null, ?string $uptoAcademicYear = null, ?string $uptoFirstSemester = null): array
    {
        $ayAndSList = [];

        $firstAdded = $this->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
            ->first();

        $firstRegistered = $this->CourseRegistrations->find()
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->order(['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'])
            ->first();

        $academicStatusEmpty = $this->CourseAdds->Students->StudentExamStatuses->find()
            ->where(['StudentExamStatuses.student_id' => $studentId])
            ->order(['StudentExamStatuses.academic_year' => 'DESC', 'StudentExamStatuses.semester' => 'DESC', 'StudentExamStatuses.created' => 'DESC'])
            ->first();

        if (empty($academicStatusEmpty) && !empty($uptoAcademicYear) && !empty($uptoFirstSemester)) {
            return [[
                'academic_year' => $uptoAcademicYear,
                'semester' => $uptoFirstSemester
            ]];
        }

        $lastAdded = $this->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->order(['CourseAdds.academic_year' => 'DESC', 'CourseAdds.semester' => 'DESC', 'CourseAdds.id' => 'DESC'])
            ->first();

        $lastRegistered = $this->CourseRegistrations->find()
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.id' => 'DESC'])
            ->first();

        if (empty($firstAdded) && empty($firstRegistered)) {
            return [];
        }

        $nextAyAndS = [];

        if (!empty($firstRegistered) && (empty($firstAdded) || $firstAdded->created > $firstRegistered->created)) {
            $nextAyAndS = [
                'academic_year' => $firstRegistered->academic_year,
                'semester' => $firstRegistered->semester
            ];
        } else {
            $nextAyAndS = [
                'academic_year' => $firstAdded->academic_year,
                'semester' => $firstAdded->semester
            ];
        }

        $lastAy = !empty($lastRegistered) && (empty($lastAdded) || $lastAdded->created < $lastRegistered->created)
            ? $lastRegistered->academic_year
            : ($lastAdded ? $lastAdded->academic_year : null);
        $lastS = !empty($lastRegistered) && (empty($lastAdded) || $lastAdded->created < $lastRegistered->created)
            ? $lastRegistered->semester
            : ($lastAdded ? $lastAdded->semester : null);

        if ($lastAy == $nextAyAndS['academic_year'] && $lastS == $nextAyAndS['semester']) {
            return [[
                'academic_year' => $nextAyAndS['academic_year'],
                'semester' => $nextAyAndS['semester']
            ]];
        }

        $count = 1;

        while (!(($uptoAcademicYear && $uptoFirstSemester && $uptoAcademicYear == $nextAyAndS['academic_year'] && $uptoFirstSemester == $nextAyAndS['semester']))) {
            $count++;
            if ($count > 100) {
                return $ayAndSList;
            }

            $courseRegistered = $this->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.student_id' => $studentId,
                    'CourseRegistrations.academic_year' => $nextAyAndS['academic_year'],
                    'CourseRegistrations.semester' => $nextAyAndS['semester']
                ])
                ->count();

            $courseAdded = $this->CourseAdds->find()
                ->where([
                    'CourseAdds.student_id' => $studentId,
                    'CourseAdds.academic_year' => $nextAyAndS['academic_year'],
                    'CourseAdds.semester' => $nextAyAndS['semester']
                ])
                ->count();

            if ($courseRegistered > 0 || $courseAdded > 0) {
                $index = count($ayAndSList);
                $ayAndSList[$index] = [
                    'academic_year' => $nextAyAndS['academic_year'],
                    'semester' => $nextAyAndS['semester']
                ];
            }

            if ($lastAy == $nextAyAndS['academic_year'] && $lastS == $nextAyAndS['semester']) {
                break;
            }

            $nextAyAndS = $this->CourseRegistrations->Students->StudentExamStatuses->getNextSemester(
                $nextAyAndS['academic_year'],
                $nextAyAndS['semester']
            );
        }

        return $ayAndSList;
    }

    public function isRegistrationAddForFirstTime(?int $id = null, int $registration = 1, int $includeEquivalent = 1): bool
    {
        $courseId = null;
        $studentId = null;
        $studentDetail = [];
        $studentDepartment = [];
        $courseDepartment = [];

        if ($registration == 1) {
            $registrationDetail = $this->CourseRegistrations->find()
                ->where(['CourseRegistrations.id' => $id])
                ->contain([
                    'Students',
                    'PublishedCourses.Courses'
                ])
                ->first();

            $courseId = $registrationDetail->published_course->course->id ?? null;
            $studentId = $registrationDetail->student_id ?? null;
            $studentDetail = $studentId ? $this->CourseRegistrations->Students->find()->where(['Students.id' => $studentId])->first() : [];
            $studentDepartment['Student'] = $registrationDetail->student ? $registrationDetail->student->toArray() : [];
            $courseDepartment['Course'] = $registrationDetail->published_course->course ? $registrationDetail->published_course->course->toArray() : [];
        } else {
            $addDetail = $this->CourseAdds->find()
                ->where(['CourseAdds.id' => $id])
                ->contain([
                    'Students',
                    'PublishedCourses.Courses'
                ])
                ->first();

            $courseId = $addDetail->published_course->course->id ?? null;
            $studentId = $addDetail->student_id ?? null;
            $studentDetail = $studentId ? $this->CourseAdds->Students->find()->where(['Students.id' => $studentId])->first() : [];
            $studentDepartment['Student'] = $addDetail->student ? $addDetail->student->toArray() : [];
            $courseDepartment['Course'] = $addDetail->published_course->course ? $addDetail->published_course->course->toArray() : [];
        }

        if (!empty($studentId) && !empty($courseId)) {
            $matchingCourses = [];

            if ($includeEquivalent == 1 && !empty($studentDepartment['Student']['curriculum_id'])) {
                $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse(
                    $courseId,
                    $studentDepartment['Student']['curriculum_id']
                );
            }

            $matchingCourses[$courseId] = $courseId;
            $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentId);

            if (count($registerAndAddFreq) <= 1) {
                return true;
            }

            if ($registration == 1 && !empty($registrationDetail->published_course->course_id)) {
                $rep = $this->repeatationLabeling(
                    $registerAndAddFreq,
                    'register',
                    $id,
                    $studentDetail ? ['Student' => $studentDetail->toArray()] : [],
                    $registrationDetail->published_course->course_id
                );

                if ($rep['repeated_old'] === false && $rep['repeated_new'] === true) {
                    return true;
                }
            } elseif (!empty($addDetail->published_course->course_id)) {
                $rep = $this->repeatationLabeling(
                    $registerAndAddFreq,
                    'add',
                    $id,
                    $studentDetail ? ['Student' => $studentDetail->toArray()] : [],
                    $addDetail->published_course->course_id
                );

                if ($rep['repeated_old'] === false && $rep['repeated_new'] === true) {
                    return true;
                }
            }
        }

        return false;
    }


    public function getDepartmentNonApprovedCoursesList(int $departmentCollegeId, int $department = 1, string $roleId = '', $currentAcademicYear = ''): array
    {
        $yearsInPast = Configure::read('ExamGrade.Approval.yearsInPast');
        $registrationAddMakeupIDs = [];
        $results = [];
        $acYear = '%';
        $freshmanDeptOrCollege = 'college_id';

        if (!empty($roleId)) {
            $freshmanDeptOrCollege = $roleId == 'ROLE_DEPARTMENT' ? 'given_by_department_id' : 'college_id';
        }

        if (!empty($currentAcademicYear)) {
            $acYear = is_array($currentAcademicYear) ? "'" . implode("', '", $currentAcademicYear) . "'" : "'" . $currentAcademicYear . "'";
        }

        if ($department == 1) {
            $resultsRegistration = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
                FROM exam_grades exam_grades
                INNER JOIN (
                    SELECT id, course_registration_id, MAX(created) AS latest, department_approval, registrar_approval
                    FROM exam_grades
                    WHERE registrar_approval = -1 OR department_approval IS NULL
                    GROUP BY course_registration_id
                ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)
                INNER JOIN course_registrations AS course_registrations ON course_registrations.id = exam_grades.course_registration_id
                INNER JOIN students AS students ON students.id = course_registrations.student_id
                INNER JOIN published_courses AS published_courses ON published_courses.id = course_registrations.published_course_id
                WHERE students.graduated = 0
                AND published_courses.academic_year IN ($acYear)
                AND published_courses.given_by_department_id = $departmentCollegeId
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                    OR (exam_grades.department_approval = -1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                )")->fetchAll('assoc');

            $resultsAdds = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
                FROM exam_grades exam_grades
                INNER JOIN (
                    SELECT id, course_registration_id, course_add_id, MAX(created) AS latest, department_approval, registrar_approval
                    FROM exam_grades
                    WHERE registrar_approval = -1 OR department_approval IS NULL
                    GROUP BY course_add_id
                ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)
                INNER JOIN course_adds AS course_adds ON course_adds.id = exam_grades.course_add_id
                INNER JOIN students AS students ON students.id = course_adds.student_id
                INNER JOIN published_courses AS published_courses ON published_courses.id = course_adds.published_course_id
                WHERE students.graduated = 0
                AND published_courses.academic_year IN ($acYear)
                AND published_courses.given_by_department_id = $departmentCollegeId
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                    OR (exam_grades.department_approval = -1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                )")->fetchAll('assoc');

            $resultsMakeup = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
                FROM exam_grades exam_grades
                INNER JOIN (
                    SELECT id, course_registration_id, makeup_exam_id, MAX(created) AS latest, department_approval, registrar_approval
                    FROM exam_grades
                    WHERE registrar_approval = -1 OR department_approval IS NULL
                    GROUP BY makeup_exam_id
                ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)
                INNER JOIN makeup_exams AS makeup_exams ON makeup_exams.id = exam_grades.makeup_exam_id
                INNER JOIN students AS students ON students.id = makeup_exams.student_id
                INNER JOIN published_courses AS published_courses ON published_courses.id = makeup_exams.published_course_id
                WHERE students.graduated = 0
                AND published_courses.academic_year IN ($acYear)
                AND published_courses.given_by_department_id = $departmentCollegeId
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                    OR (exam_grades.department_approval = -1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                )")->fetchAll('assoc');

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        } else {
            $resultsRegistration = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
                FROM exam_grades exam_grades
                INNER JOIN (
                    SELECT id, course_registration_id, MAX(created) AS latest, department_approval, registrar_approval
                    FROM exam_grades
                    WHERE registrar_approval = -1 OR department_approval IS NULL
                    GROUP BY course_registration_id
                ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)
                INNER JOIN course_registrations AS course_registrations ON course_registrations.id = exam_grades.course_registration_id
                INNER JOIN students AS students ON students.id = course_registrations.student_id
                INNER JOIN published_courses AS published_courses ON published_courses.id = course_registrations.published_course_id
                WHERE students.graduated = 0
                AND published_courses.department_id IS NULL
                AND published_courses.academic_year IN ($acYear)
                AND published_courses.$freshmanDeptOrCollege = $departmentCollegeId
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                    OR (exam_grades.department_approval = -1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                )")->fetchAll('assoc');

            $resultsAdds = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
                FROM exam_grades exam_grades
                INNER JOIN (
                    SELECT id, course_registration_id, course_add_id, MAX(created) AS latest, department_approval, registrar_approval
                    FROM exam_grades
                    WHERE registrar_approval = -1 OR department_approval IS NULL
                    GROUP BY course_add_id
                ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)
                INNER JOIN course_adds AS course_adds ON course_adds.id = exam_grades.course_add_id
                INNER JOIN students AS students ON students.id = course_adds.student_id
                INNER JOIN published_courses AS published_courses ON published_courses.id = course_adds.published_course_id
                WHERE students.graduated = 0
                AND published_courses.department_id IS NULL
                AND published_courses.academic_year IN ($acYear)
                AND published_courses.$freshmanDeptOrCollege = $departmentCollegeId
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                    OR (exam_grades.department_approval = -1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                )")->fetchAll('assoc');

            $resultsMakeup = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
                FROM exam_grades exam_grades
                INNER JOIN (
                    SELECT id, course_registration_id, makeup_exam_id, MAX(created) AS latest, department_approval, registrar_approval
                    FROM exam_grades
                    WHERE registrar_approval = -1 OR department_approval IS NULL
                    GROUP BY makeup_exam_id
                ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)
                INNER JOIN makeup_exams AS makeup_exams ON makeup_exams.id = exam_grades.makeup_exam_id
                INNER JOIN students AS students ON students.id = makeup_exams.student_id
                INNER JOIN published_courses AS published_courses ON published_courses.id = makeup_exams.published_course_id
                WHERE students.graduated = 0
                AND published_courses.department_id IS NULL
                AND published_courses.academic_year IN ($acYear)
                AND published_courses.$freshmanDeptOrCollege = $departmentCollegeId
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                    OR (exam_grades.department_approval = -1 AND exam_grades.registrar_approval = -1 AND exam_grades.department_reply = 1)
                )")->fetchAll('assoc');

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    if ($value['exam_grades']['registrar_approval'] == -1) {
                        $registrationAddMakeupIDs['registerRejected'][] = $value['exam_grades']['course_registration_id'];
                    } else {
                        $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                    }
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    if ($value['exam_grades']['registrar_approval'] == -1) {
                        $registrationAddMakeupIDs['addRejected'][] = $value['exam_grades']['course_add_id'];
                    } else {
                        $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                    }
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    if ($value['exam_grades']['registrar_approval'] == -1) {
                        $registrationAddMakeupIDs['makeupRejected'][] = $value['exam_grades']['makeup_exam_id'];
                    } else {
                        $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                    }
                }
            }
        }

        $publicationIds = [];
        $publicationIdsRejected = [];

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publicationIdsRegister = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.id IN' => $registrationAddMakeupIDs['register']])
                ->select(['CourseRegistrations.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsRegister);
        }

        if (!empty($registrationAddMakeupIDs['registerRejected'])) {
            $publicationIdsRegisterRejected = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.id IN' => $registrationAddMakeupIDs['registerRejected']])
                ->select(['CourseRegistrations.published_course_id'])
                ->toArray();
            $publicationIdsRejected = array_merge($publicationIdsRejected, $publicationIdsRegisterRejected);
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publicationIdsAdd = $this->CourseAdds->find('list')
                ->where(['CourseAdds.id IN' => $registrationAddMakeupIDs['add']])
                ->select(['CourseAdds.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsAdd);
        }

        if (!empty($registrationAddMakeupIDs['addRejected'])) {
            $publicationIdsAddRejected = $this->CourseAdds->find('list')
                ->where(['CourseAdds.id IN' => $registrationAddMakeupIDs['addRejected']])
                ->select(['CourseAdds.published_course_id'])
                ->toArray();
            $publicationIdsRejected = array_merge($publicationIdsRejected, $publicationIdsAddRejected);
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publicationIdsMakeup = $this->MakeupExams->find('list')
                ->where(['MakeupExams.id IN' => $registrationAddMakeupIDs['makeup']])
                ->select(['MakeupExams.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsMakeup);
        }

        if (!empty($registrationAddMakeupIDs['makeupRejected'])) {
            $publicationIdsMakeupRejected = $this->MakeupExams->find('list')
                ->where(['MakeupExams.id IN' => $registrationAddMakeupIDs['makeupRejected']])
                ->select(['MakeupExams.published_course_id'])
                ->toArray();
            $publicationIdsRejected = array_merge($publicationIdsRejected, $publicationIdsMakeupRejected);
        }

        $distinctPublicationIds = !empty($publicationIds) ? array_unique($publicationIds) : [];
        $distinctPublicationIdsRejected = !empty($publicationIdsRejected) ? array_unique($publicationIdsRejected) : [];

        $coursesForApproval = ['from_instructor' => [], 'from_registrars' => []];

        if (!empty($distinctPublicationIds)) {
            $conditions = ['PublishedCourses.id IN' => $distinctPublicationIds];
            if ($department != 1) {
                $conditions['PublishedCourses.' . $freshmanDeptOrCollege] = $departmentCollegeId;
            }

            $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'GivenByDepartments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'CourseInstructorAssignments' => [
                        'fields' => ['id', 'published_course_id', 'staff_id'],
                        'Staffs' => [
                            'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'user_id'],
                            'Positions' => ['fields' => ['id', 'position']],
                            'Titles' => ['fields' => ['id', 'title']]
                        ],
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                    ],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'type_credit']]
                    ]
                ])
                ->toArray();

            $coursesForApproval['from_instructor'] = $publishedCourses;
        }

        if (!empty($distinctPublicationIdsRejected)) {
            $conditions = ['PublishedCourses.id IN' => $distinctPublicationIdsRejected];
            if ($department != 1) {
                $conditions['PublishedCourses.' . $freshmanDeptOrCollege] = $departmentCollegeId;
            }

            $publishedCoursesRejected = $this->CourseRegistrations->PublishedCourses->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'GivenByDepartments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'CourseInstructorAssignments' => [
                        'fields' => ['id', 'published_course_id', 'staff_id'],
                        'Staffs' => [
                            'fields' => ['id','first_name', 'middle_name', 'last_name', 'user_id'],
                            'Positions' => ['fields' => ['id', 'position']],
                            'Titles' => ['fields' => ['id', 'title']]
                        ],
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                    ],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'type_credit']]
                    ]
                ])
                ->toArray();

            $coursesForApproval['from_registrars'] = $publishedCoursesRejected;
        }

        return $coursesForApproval;
    }

    public function getRegistrarNonApprovedCoursesList(
        $departmentIds = null,
        $collegeIds = null,
        $academicYear = '',
        $semester = '',
        $programId = null,
        $programTypeId = null,
        array $acyRanges = []
    ) {
        $connection = ConnectionManager::get('default');

        $buildWhere = function () use ($academicYear, $semester, $acyRanges, $departmentIds, $collegeIds, $programId, $programTypeId) {
            $conditions = ["published_courses.id IS NOT NULL"];

            if (!empty($acyRanges)) {
                $quoted = implode("','", $acyRanges);
                $conditions[] = "published_courses.academic_year IN ('$quoted')";
            }

            if (!empty($academicYear)) {
                $conditions[] = "published_courses.academic_year LIKE '$academicYear'";
            }

            if (!empty($semester)) {
                $conditions[] = "published_courses.semester LIKE '$semester'";
            }

            if (!empty($programId)) {
                $ids = implode(',', (array)$programId);
                $conditions[] = "published_courses.program_id IN ($ids)";
            }

            if (!empty($programTypeId)) {
                $ids = implode(',', (array)$programTypeId);
                $conditions[] = "published_courses.program_type_id IN ($ids)";
            }

            if (!empty($departmentIds)) {
                $ids = implode(',', (array)$departmentIds);
                $conditions[] = "published_courses.department_id IN ($ids)";
            } elseif (!empty($collegeIds)) {
                $ids = implode(',', (array)$collegeIds);
                $conditions[] = "published_courses.college_id IN ($ids)";
                $conditions[] = "published_courses.department_id IS NULL";
            }

            return implode(' AND ', $conditions);
        };

        $whereSQL = $buildWhere();

        $fetchGrades = function ($joinTable, $joinField) use ($connection, $whereSQL) {
            return $connection->execute("
            SELECT exam_grades.* FROM exam_grades
            INNER JOIN $joinTable ON exam_grades.$joinField = $joinTable.id
            INNER JOIN students ON $joinTable.student_id = students.id
            INNER JOIN published_courses ON $joinTable.published_course_id = published_courses.id
            WHERE students.graduated = 0
              AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval IN (1, -1)))
              AND exam_grades.registrar_approval IS NULL
              AND $whereSQL
            GROUP BY exam_grades.$joinField
            ORDER BY exam_grades.id DESC
        ")->fetchAll('assoc');
        };

        $resultsRegistration = $fetchGrades('course_registrations', 'course_registration_id');
        $resultsAdds = $fetchGrades('course_adds', 'course_add_id');
        $resultsMakeup = $fetchGrades('makeup_exams', 'makeup_exam_id');

        $results = array_merge($resultsRegistration, $resultsAdds, $resultsMakeup);

        // Extract published_course_ids
        $registrationAddMakeupIDs = ['register' => [], 'add' => [], 'makeup' => []];
        foreach ($results as $row) {
            if (!empty($row['course_registration_id'])) {
                $registrationAddMakeupIDs['register'][] = $row['course_registration_id'];
            } elseif (!empty($row['course_add_id'])) {
                $registrationAddMakeupIDs['add'][] = $row['course_add_id'];
            } elseif (!empty($row['makeup_exam_id'])) {
                $registrationAddMakeupIDs['makeup'][] = $row['makeup_exam_id'];
            }
        }

        $CourseRegistration = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $CourseAdd = TableRegistry::getTableLocator()->get('CourseAdds');
        $MakeupExam = TableRegistry::getTableLocator()->get('MakeupExams');
        $PublishedCourse = TableRegistry::getTableLocator()->get('PublishedCourses');

        $publicationIds = [];

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publicationIds += $CourseRegistration->find('list')
                ->select(['published_course_id'])
                ->where(['id IN' => $registrationAddMakeupIDs['register']])
                ->toArray();
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publicationIds += $CourseAdd->find('list')
                ->select(['published_course_id'])
                ->where(['id IN' => $registrationAddMakeupIDs['add']])
                ->toArray();
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publicationIds += $MakeupExam->find('list')
                ->select(['published_course_id'])
                ->where(['id IN' => $registrationAddMakeupIDs['makeup']])
                ->toArray();
        }

        $distinctIds = array_unique(array_values($publicationIds));
        if (empty($distinctIds)) return [];

        return $PublishedCourse->find()
            ->contain([
                'Programs',
                'ProgramTypes',
                'Sections',
                'Departments',
                'GivenByDepartments',
                'Colleges',
                'YearLevels',
                'Courses' => ['Curriculums'],
                'CourseInstructorAssignments' => function ($q) {
                    return $q->where(['CourseInstructorAssignments.isprimary' => 1])
                        ->contain(['Staffs' => ['Titles', 'Positions', 'Users']]);
                }
            ])
            ->where(['PublishedCourses.id IN' => $distinctIds])
            ->order(['PublishedCourses.academic_year' => 'DESC', 'PublishedCourses.semester' => 'DESC'])
            ->enableHydration(false)
            ->toArray();
    }
    public function getRegistrarNonApprovedCoursesList2(?array $departmentIds = null, ?array $collegeIds = null, string $academicYear = '', string $semester = '', ?array $programId = null, ?array $programTypeId = null, array $acyRanges = []): array
    {
        $registrationAddMakeupIDs = [];
        $queryPs = 'id IS NOT NULL';
        $queryPss = 'published_courses.id IS NOT NULL';

        if (!empty($acyRanges)) {
            $acyRangesByComaQuoted = "'" . implode("', '", $acyRanges) . "'";
            $queryPs .= " AND published_courses.academic_year IN ($acyRangesByComaQuoted)";
            $queryPss .= " AND published_courses.academic_year IN ($acyRangesByComaQuoted)";
        }

        if (!empty($academicYear)) {
            $queryPs .= " AND published_courses.academic_year LIKE '$academicYear'";
            $queryPss .= " AND published_courses.academic_year LIKE '$academicYear'";
        }

        if (!empty($semester)) {
            $queryPs .= " AND published_courses.semester LIKE '$semester'";
            $queryPss .= " AND published_courses.semester LIKE '$semester'";
        }

        if (!empty($programId)) {
            $prgIds = implode(', ', $programId);
            $queryPs .= " AND program_id IN ($prgIds)";
            $queryPss .= " AND published_courses.program_id IN ($prgIds)";
        }

        if (!empty($programTypeId)) {
            $prgTypeIds = implode(', ', $programTypeId);
            $queryPs .= " AND program_type_id IN ($prgTypeIds)";
            $queryPss .= " AND published_courses.program_type_id IN ($prgTypeIds)";
        }

        if (!empty($departmentIds)) {
            $deptIds = implode(', ', $departmentIds);
            $queryPs .= " AND department_id IN ($deptIds)";
            $queryPss .= " AND published_courses.department_id IN ($deptIds)";
        }

        if (!empty($collegeIds) && empty($departmentIds)) {
            $collegeIdsStr = implode(', ', $collegeIds);
            $queryPs .= " AND college_id IN ($collegeIdsStr) AND department_id IS NULL";
            $queryPss .= " AND published_courses.college_id IN ($collegeIdsStr) AND published_courses.department_id IS NULL";
        }

        $results = [];

        if (!empty($queryPs) && !empty($queryPss)) {
            $resultsRegistration = $this->query("SELECT exam_grades.*
                FROM exam_grades
                INNER JOIN course_registrations ON exam_grades.course_registration_id = course_registrations.id
                INNER JOIN students ON course_registrations.student_id = students.id
                INNER JOIN published_courses ON course_registrations.published_course_id = published_courses.id
                WHERE students.graduated = 0
                AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = -1))
                AND exam_grades.registrar_approval IS NULL
                AND $queryPss
                GROUP BY exam_grades.course_registration_id
                ORDER BY exam_grades.id DESC")->fetchAll('assoc');

            if (!empty($resultsRegistration)) {
                foreach ($resultsRegistration as $key => $value) {
                    if ($this->find()->where([
                        'ExamGrades.course_registration_id' => $value['exam_grades']['course_registration_id'],
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])->count()) {
                        unset($resultsRegistration[$key]);
                    }
                }
            }

            $resultsAdds = $this->query("SELECT exam_grades.*
                FROM exam_grades
                INNER JOIN course_adds ON exam_grades.course_add_id = course_adds.id
                INNER JOIN students ON course_adds.student_id = students.id
                INNER JOIN published_courses ON course_adds.published_course_id = published_courses.id
                WHERE students.graduated = 0
                AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1))
                AND exam_grades.registrar_approval IS NULL
                AND $queryPss
                GROUP BY exam_grades.course_add_id
                ORDER BY exam_grades.id DESC")->fetchAll('assoc');

            if (!empty($resultsAdds)) {
                foreach ($resultsAdds as $key => $value) {
                    if ($this->find()->where([
                        'ExamGrades.course_add_id' => $value['exam_grades']['course_add_id'],
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])->count()) {
                        unset($resultsAdds[$key]);
                    }
                }
            }

            $resultsMakeup = $this->query("SELECT exam_grades.*
                FROM exam_grades
                INNER JOIN makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
                INNER JOIN students ON makeup_exams.student_id = students.id
                INNER JOIN published_courses ON makeup_exams.published_course_id = published_courses.id
                WHERE students.graduated = 0
                AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1))
                AND exam_grades.registrar_approval IS NULL
                AND $queryPss
                GROUP BY exam_grades.makeup_exam_id
                ORDER BY exam_grades.id DESC")->fetchAll('assoc');

            if (!empty($resultsMakeup)) {
                foreach ($resultsMakeup as $key => $value) {
                    if ($this->find()->where([
                        'ExamGrades.makeup_exam_id' => $value['exam_grades']['makeup_exam_id'],
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])->count()) {
                        unset($resultsMakeup[$key]);
                    }
                }
            }

            $results = array_merge($resultsRegistration, $resultsAdds, $resultsMakeup);
        }

        if (!empty($results)) {
            foreach ($results as $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        $publicationIds = [];

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publicationIdsRegister = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.id IN' => $registrationAddMakeupIDs['register']])
                ->select(['CourseRegistrations.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsRegister);
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publicationIdsAdd = $this->CourseAdds->find('list')
                ->where(['CourseAdds.id IN' => $registrationAddMakeupIDs['add']])
                ->select(['CourseAdds.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsAdd);
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publicationIdsMakeup = $this->MakeupExams->find('list')
                ->where(['MakeupExams.id IN' => $registrationAddMakeupIDs['makeup']])
                ->select(['MakeupExams.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsMakeup);
        }

        $distinctPublicationIds = !empty($publicationIds) ? array_unique($publicationIds) : [];
        $publishedCourses = [];

        if (!empty($distinctPublicationIds)) {
            $conditions = ['PublishedCourses.id IN' => $distinctPublicationIds];

            if (!empty($collegeIds) && empty($departmentIds)) {
                $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
                    ->where($conditions)
                    ->contain([
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Sections' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'GivenByDepartments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'type']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'CourseInstructorAssignments' => [
                            'fields' => ['id', 'published_course_id', 'staff_id'],
                            'Staffs' => [
                                'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'user_id'],
                                'Positions' => ['fields' => ['id', 'position']],
                                'Titles' => ['fields' => ['id', 'title']],
                                'Users' => ['fields' => ['id', 'username', 'email', 'active', 'email_verified']]
                            ],
                            'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                        ],
                        'Courses' => [
                            'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                            'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                        ]
                    ])
                    ->order(['PublishedCourses.academic_year' => 'DESC', 'PublishedCourses.semester' => 'DESC'])
                    ->toArray();
            } elseif (!empty($departmentIds)) {
                $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
                    ->where($conditions)
                    ->contain([
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Sections' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'GivenByDepartments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'type']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'CourseInstructorAssignments' => [
                            'fields' => ['id', 'published_course_id', 'staff_id'],
                            'Staffs' => [
                                'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'user_id'],
                                'Positions' => ['fields' => ['id', 'position']],
                                'Titles' => ['fields' => ['id', 'title']],
                                'Users' => ['fields' => ['id', 'username', 'email', 'active', 'email_verified']]
                            ],
                            'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                        ],
                        'Courses' => [
                            'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                            'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                        ]
                    ])
                    ->order(['PublishedCourses.academic_year' => 'DESC', 'PublishedCourses.semester' => 'DESC'])
                    ->toArray();
            }
        }

        return $publishedCourses;
    }

    public function getRegistrarNonApprovedPublishedCourseList(?array $departmentIds = null, ?array $collegeIds = null, string $semester, array $programId, array $programTypeId, string $academicYear, ?int $yearLevelId = null): array
    {
        $registrationAddMakeupIDs = [];
        $results = [];

        if (!empty($departmentIds)) {
            $deptIds = implode(', ', $departmentIds);
            $resultsRegistration = $this->query(
                "
			SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.course_add_id, exam_grades.makeup_exam_id FROM exam_grades exam_grades
			INNER JOIN (
				SELECT id, course_registration_id, MAX( created ) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE department_approval = 1 AND registrar_approval IS NULL GROUP BY course_registration_id
			) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)

			WHERE exam_grades.department_approval = 1 AND exam_grades.registrar_approval IS NULL
			AND exam_grades.course_registration_id IN (
				SELECT id FROM course_registrations
				WHERE published_course_id IN (
					SELECT id FROM published_courses
					WHERE department_id IN ($deptIds) and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $programId
                ) . ")  and program_type_id in (" . implode(
                    ', ',
                    $programTypeId
                ) . ") and academic_year='" . $academicYear . "'
				)
			) "
            );


            $resultsAdds = $this->query(
                "
			SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.course_add_id, exam_grades.makeup_exam_id FROM exam_grades exam_grades
			INNER JOIN (
				SELECT id, course_add_id, MAX( created ) AS latest, department_approval, registrar_approval  FROM exam_grades
				WHERE department_approval = 1 AND registrar_approval IS NULL GROUP BY course_add_id
			) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)

			WHERE exam_grades.department_approval = 1 AND exam_grades.registrar_approval IS NULL
			AND exam_grades.course_add_id IN (
				SELECT id FROM course_adds
				WHERE published_course_id IN (
					SELECT id FROM published_courses
					WHERE department_id IN ($deptIds) and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $programId
                ) . ")  and program_type_id in (" . implode(
                    ', ',
                    $programTypeId
                ) . ") and academic_year='" . $academicYear . "'
				)
			) "
            );


            $resultsMakeup = $this->query(
                "
			SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.course_add_id, exam_grades.makeup_exam_id FROM exam_grades exam_grades
			INNER JOIN (
				SELECT id, makeup_exam_id, MAX( created ) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE department_approval = 1 AND registrar_approval IS NULL GROUP BY makeup_exam_id
			) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)

			WHERE exam_grades.department_approval = 1 AND exam_grades.registrar_approval IS NULL
			AND exam_grades.makeup_exam_id IN (
				SELECT id FROM makeup_exams
				WHERE published_course_id IN (
					SELECT id FROM published_courses
					WHERE department_id IN ($deptIds) and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $programId
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $programTypeId
                ) . ") and academic_year='" . $academicYear . "'
				)
			) "
            );

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);

        }

        if (!empty($collegeIds)) {
            $collegeIdsStr = implode(', ', $collegeIds);

            $resultsRegistration = $this->query(
                "
			SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.course_add_id, exam_grades.makeup_exam_id FROM exam_grades exam_grades
			INNER JOIN (
				SELECT id, course_registration_id, MAX( created ) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE department_approval = 1 AND registrar_approval IS NULL GROUP BY course_registration_id
			) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)

			WHERE exam_grades.department_approval = 1 AND exam_grades.registrar_approval IS NULL
			AND exam_grades.course_registration_id IN (
				SELECT id FROM course_registrations
				WHERE published_course_id IN (
					SELECT id FROM published_courses
					WHERE college_id IN ($collegeIdsStr) and department_id is null and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $programId
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $programTypeId
                ) . ") and academic_year='" . $academicYear . "'
				)
			) "
            );


            $resultsAdds = $this->query(
                "
			SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.course_add_id, exam_grades.makeup_exam_id FROM exam_grades exam_grades
			INNER JOIN (
				SELECT id, course_add_id, MAX( created ) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE department_approval = 1 AND registrar_approval IS NULL GROUP BY course_add_id
			) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)

			WHERE exam_grades.department_approval = 1 AND exam_grades.registrar_approval IS NULL
			AND exam_grades.course_add_id IN (
				SELECT id FROM course_adds
				WHERE published_course_id IN (
					SELECT id FROM published_courses
					WHERE college_id IN ($collegeIdsStr) and department_id is null and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $programId
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $programTypeId
                ) . ") and academic_year='" . $academicYear . "'
				)
			) "
            );


            $resultsMakeup = $this->query(
                "
			SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.course_add_id, exam_grades.makeup_exam_id FROM exam_grades exam_grades
			INNER JOIN (
				SELECT id, makeup_exam_id, MAX( created ) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE department_approval = 1 AND registrar_approval IS NULL GROUP BY makeup_exam_id
			) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)

			WHERE exam_grades.department_approval = 1 AND exam_grades.registrar_approval IS NULL
			AND exam_grades.makeup_exam_id IN (
				SELECT id FROM makeup_exams
				WHERE published_course_id IN (
					SELECT id FROM published_courses
					WHERE college_id IN ($collegeIdsStr) and department_id is null and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $programId
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $programTypeId
                ) . ") and academic_year='" . $academicYear . "'
				)
			) "
            );

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        $publicationIds = [];

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publicationIdsRegister = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.id IN' => $registrationAddMakeupIDs['register']])
                ->select(['CourseRegistrations.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsRegister);
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publicationIdsAdd = $this->CourseAdds->find('list')
                ->where(['CourseAdds.id IN' => $registrationAddMakeupIDs['add']])
                ->select(['CourseAdds.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsAdd);
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publicationIdsMakeup = $this->MakeupExams->find('list')
                ->where(['MakeupExams.id IN' => $registrationAddMakeupIDs['makeup']])
                ->select(['MakeupExams.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsMakeup);
        }

        $distinctPublicationIds = !empty($publicationIds) ? array_unique($publicationIds) : [];
        $publishedCourses = [];

        if (!empty($distinctPublicationIds)) {
            $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
                ->where(['PublishedCourses.id IN' => $distinctPublicationIds])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'CourseInstructorAssignments' => [
                        'fields' => ['id', 'published_course_id', 'staff_id'],
                        'Staffs' => [
                            'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'user_id'],
                            'Positions' => ['fields' => ['id', 'position']],
                            'Titles' => ['fields' => ['id', 'title']]
                        ],
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                    ],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'type_credit']]
                    ]
                ])
                ->toArray();
        }

        return $publishedCourses;
    }

    public function getRejectedOrNonApprovedPublishedCourseList(int $departmentCollegeId, int $department = 1, string $academicYear = '', string $semester = '', array $programIds = [], array $programTypeIds = [], array $acyRanges = []): array
    {
        $registrationAddMakeupIDs = [];
        $results = [];
        $queryPss = $department == 1
            ? "published_courses.id IS NOT NULL AND (published_courses.year_level_id IS NOT NULL OR published_courses.year_level_id != 0 OR published_courses.year_level_id != '') AND published_courses.given_by_department_id = $departmentCollegeId"
            : "published_courses.id IS NOT NULL AND (published_courses.year_level_id IS NULL OR published_courses.year_level_id = 0 OR published_courses.year_level_id = '') AND published_courses.program_id = 1 AND published_courses.program_type_id = 1 AND department_id IS NULL AND published_courses.given_by_department_id = $departmentCollegeId";

        if (!empty($acyRanges)) {
            $acyRangesByComaQuoted = "'" . implode("', '", $acyRanges) . "'";
            $queryPss .= " AND published_courses.academic_year IN ($acyRangesByComaQuoted)";
        }

        if (!empty($academicYear)) {
            $queryPss .= " AND published_courses.academic_year LIKE '$academicYear'";
        }

        if (!empty($semester)) {
            $queryPss .= " AND published_courses.semester LIKE '$semester'";
        }

        if (!empty($programIds)) {
            $programIdsStr = "'" . implode("', '", $programIds) . "'";
            $queryPss .= " AND published_courses.program_id IN ($programIdsStr)";
        }

        if (!empty($programTypeIds)) {
            $programTypeIdsStr = "'" . implode("', '", $programTypeIds) . "'";
            $queryPss .= " AND published_courses.program_type_id IN ($programTypeIdsStr)";
        }

        if (!empty($queryPss)) {
            $resultsRegistrationNR = $this->query("SELECT DISTINCT exam_grades.*
                FROM exam_grades
                INNER JOIN course_registrations ON exam_grades.course_registration_id = course_registrations.id
                INNER JOIN published_courses ON course_registrations.published_course_id = published_courses.id
                WHERE exam_grades.department_approval IS NULL
                AND $queryPss
                AND exam_grades.course_registration_id NOT IN (
                    SELECT exam_grades.course_registration_id
                    FROM exam_grades
                    WHERE exam_grades.registrar_approval = 1
                    AND exam_grades.department_approval = 1
                    AND exam_grades.course_registration_id IS NOT NULL
                    GROUP BY exam_grades.course_registration_id
                    ORDER BY exam_grades.id DESC
                )")->fetchAll('assoc');

            $resultsRegistrationR = $this->query("SELECT DISTINCT exam_grades.*
                FROM exam_grades
                INNER JOIN course_registrations ON exam_grades.course_registration_id = course_registrations.id
                INNER JOIN published_courses ON course_registrations.published_course_id = published_courses.id
                WHERE exam_grades.registrar_approval = -1
                AND exam_grades.department_approval = 1
                AND $queryPss
                AND exam_grades.course_registration_id NOT IN (
                    SELECT exam_grades.course_registration_id
                    FROM exam_grades
                    WHERE exam_grades.registrar_approval = 1
                    AND exam_grades.department_approval = 1
                    AND exam_grades.course_registration_id IS NOT NULL
                    GROUP BY exam_grades.course_registration_id
                    ORDER BY exam_grades.id DESC
                )")->fetchAll('assoc');

            $resultsRegistration = array_merge($resultsRegistrationNR, $resultsRegistrationR);

            $resultsAddsNR = $this->query("SELECT DISTINCT exam_grades.*
                FROM exam_grades
                INNER JOIN course_adds ON exam_grades.course_add_id = course_adds.id
                INNER JOIN published_courses ON course_adds.published_course_id = published_courses.id
                WHERE exam_grades.department_approval IS NULL
                AND $queryPss
                AND exam_grades.course_add_id NOT IN (
                    SELECT exam_grades.course_add_id
                    FROM exam_grades
                    WHERE exam_grades.registrar_approval = 1
                    AND exam_grades.department_approval = 1
                    AND exam_grades.course_add_id IS NOT NULL
                    GROUP BY exam_grades.course_add_id
                    ORDER BY exam_grades.id DESC
                )")->fetchAll('assoc');

            $resultsAddsR = $this->query("SELECT DISTINCT exam_grades.*
                FROM exam_grades
                INNER JOIN course_adds ON exam_grades.course_add_id = course_adds.id
                INNER JOIN published_courses ON course_adds.published_course_id = published_courses.id
                WHERE exam_grades.registrar_approval = -1
                AND exam_grades.department_approval = 1
                AND $queryPss
                AND exam_grades.course_add_id NOT IN (
                    SELECT exam_grades.course_add_id
                    FROM exam_grades
                    WHERE exam_grades.registrar_approval = 1
                    AND exam_grades.department_approval = 1
                    AND exam_grades.course_add_id IS NOT NULL
                    GROUP BY exam_grades.course_add_id
                    ORDER BY exam_grades.id DESC
                )")->fetchAll('assoc');

            $resultsAdds = array_merge($resultsAddsNR, $resultsAddsR);

            $resultsMakeupNR = $this->query("SELECT DISTINCT exam_grades.*
                FROM exam_grades
                INNER JOIN makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
                INNER JOIN published_courses ON makeup_exams.published_course_id = published_courses.id
                WHERE exam_grades.department_approval IS NULL
                AND $queryPss
                AND exam_grades.makeup_exam_id NOT IN (
                    SELECT exam_grades.makeup_exam_id
                    FROM exam_grades
                    WHERE exam_grades.registrar_approval = 1
                    AND exam_grades.department_approval = 1
                    AND exam_grades.makeup_exam_id IS NOT NULL
                    GROUP BY exam_grades.makeup_exam_id
                    ORDER BY exam_grades.id DESC
                )")->fetchAll('assoc');

            $resultsMakeupR = $this->query("SELECT DISTINCT exam_grades.*
                FROM exam_grades
                INNER JOIN makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
                INNER JOIN published_courses ON makeup_exams.published_course_id = published_courses.id
                WHERE exam_grades.registrar_approval = -1
                AND exam_grades.department_approval = 1
                AND $queryPss
                AND exam_grades.makeup_exam_id NOT IN (
                    SELECT exam_grades.makeup_exam_id
                    FROM exam_grades
                    WHERE exam_grades.registrar_approval = 1
                    AND exam_grades.department_approval = 1
                    AND exam_grades.makeup_exam_id IS NOT NULL
                    GROUP BY exam_grades.makeup_exam_id
                    ORDER BY exam_grades.id DESC
                )")->fetchAll('assoc');

            $resultsMakeup = array_merge($resultsMakeupNR, $resultsMakeupR);
            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        $publicationIds = [];

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publicationIdsRegister = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.id IN' => $registrationAddMakeupIDs['register']])
                ->select(['CourseRegistrations.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsRegister);
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publicationIdsAdd = $this->CourseAdds->find('list')
                ->where(['CourseAdds.id IN' => $registrationAddMakeupIDs['add']])
                ->select(['CourseAdds.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsAdd);
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publicationIdsMakeup = $this->MakeupExams->find('list')
                ->where(['MakeupExams.id IN' => $registrationAddMakeupIDs['makeup']])
                ->select(['MakeupExams.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsMakeup);
        }

        $distinctPublicationIds = !empty($publicationIds) ? array_unique($publicationIds) : [];
        $publishedCourses = [];

        if (!empty($distinctPublicationIds)) {
            $conditions = [
                'PublishedCourses.id IN' => $distinctPublicationIds,
                $department == 1 ? 'PublishedCourses.given_by_department_id' : 'PublishedCourses.college_id' => $departmentCollegeId
            ];

            $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'CourseInstructorAssignments' => [
                        'fields' => ['id', 'published_course_id', 'staff_id'],
                        'Staffs' => [
                            'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'user_id'],
                            'Positions' => ['fields' => ['id', 'position']],
                            'Titles' => ['fields' => ['id', 'title']]
                        ],
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                    ],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                    ]
                ])
                ->toArray();
        }

        return $publishedCourses;
    }


    public function getRejectedOrNonApprovedPublishedCourseList2($departmentId, string $academicYear = '', string $semester = '', array $yearLevel = [], array $programIds = [], array $programTypeIds = [], array $acyRanges = [], string $roleId = 'ROLE_DEPARTMENT', int $freshman = 0): array
    {
        $registrationAddMakeupIDs = [];
        $results = [];
        $givenBy = 'given_by_department_id';

        if (!empty($departmentId) && $roleId === 'ROLE_DEPARTMENT') {
            $deptsByComaQuoted = is_array($departmentId) ? "'" . implode("', '", $departmentId) . "'" : "'$departmentId'";
            $queryPss = "published_courses.id IS NOT NULL AND published_courses.given_by_department_id IN ($deptsByComaQuoted)";
        } elseif (!empty($departmentId) && $roleId === 'ROLE_COLLEGE') {
            $deptsByComaQuoted = is_array($departmentId) ? "'" . implode("', '", $departmentId) . "'" : "'$departmentId'";
            $queryPss = "published_courses.id IS NOT NULL AND published_courses.college_id IN ($deptsByComaQuoted)";
            $givenBy = 'college_id';
        } else {
            return [];
        }

        if (!empty($acyRanges)) {
            $acyRangesByComaQuoted = "'" . implode("', '", $acyRanges) . "'";
            $queryPss .= " AND published_courses.academic_year IN ($acyRangesByComaQuoted)";
        }

        if (!empty($academicYear)) {
            $queryPss .= " AND published_courses.academic_year LIKE '$academicYear'";
        }

        if (!empty($semester)) {
            $queryPss .= " AND published_courses.semester LIKE '$semester'";
        }

        if (!$freshman) {
            if (!empty($yearLevel) && !empty($departmentId) && $roleId === 'ROLE_DEPARTMENT') {
                $publishedDeptIds = $this->CourseRegistrations->PublishedCourses->find('list')
                    ->where([
                        'PublishedCourses.given_by_department_id IN' => is_array($departmentId) ? $departmentId : [$departmentId],
                        'PublishedCourses.academic_year IN' => !empty($acyRanges) ? $acyRanges : [$academicYear]
                    ])
                    ->group(['PublishedCourses.department_id'])
                    ->select(['PublishedCourses.department_id'])
                    ->toArray();

                $yearLevelIds = TableRegistry::getTableLocator()->get('YearLevels')->find('list')
                    ->where(['YearLevels.department_id IN' => array_keys($publishedDeptIds), 'YearLevels.name IN' => $yearLevel])
                    ->select(['YearLevels.id'])
                    ->toArray();

                if (!empty($yearLevelIds)) {
                    $yearLevelsComaQuoted = "'" . implode("', '", array_keys($yearLevelIds)) . "'";
                    $queryPss .= " AND (published_courses.year_level_id IN ($yearLevelsComaQuoted) OR ((published_courses.year_level_id IS NULL OR published_courses.year_level_id = '' OR published_courses.year_level_id = 0) AND published_courses.college_id IS NOT NULL))";
                }
            }
        } else {
            $queryPss .= " AND (published_courses.year_level_id IS NULL OR published_courses.year_level_id = '' OR published_courses.year_level_id = 0) AND published_courses.college_id IS NOT NULL";
        }

        if (!empty($programIds)) {
            $programIdsComaQuoted = is_array($programIds) ? "'" . implode("', '", $programIds) . "'" : "'$programIds'";
            $queryPss .= " AND published_courses.program_id IN ($programIdsComaQuoted)";
        }

        if (!empty($programTypeIds)) {
            $programTypeIdsComaQuoted = is_array($programTypeIds) ? "'" . implode("', '", $programTypeIds) . "'" : "'$programTypeIds'";
            $queryPss .= " AND published_courses.program_type_id IN ($programTypeIdsComaQuoted)";
        }

        if (!empty($queryPss)) {
            $resultsRegistration = $this->query("SELECT exam_grades.*
                FROM exam_grades
                INNER JOIN course_registrations ON exam_grades.course_registration_id = course_registrations.id
                INNER JOIN published_courses ON course_registrations.published_course_id = published_courses.id
                INNER JOIN students ON course_registrations.student_id = students.id
                WHERE students.graduated = 0
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)
                    OR (exam_grades.department_reply = 0 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)
                )
                AND $queryPss
                GROUP BY exam_grades.course_registration_id
                ORDER BY exam_grades.id DESC")->fetchAll('assoc');

            if (!empty($resultsRegistration)) {
                foreach ($resultsRegistration as $key => $value) {
                    if ($this->find()->where([
                        'ExamGrades.course_registration_id' => $value['exam_grades']['course_registration_id'],
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])->count()) {
                        unset($resultsRegistration[$key]);
                    }
                }
            }

            $resultsAdds = $this->query("SELECT exam_grades.*
                FROM exam_grades
                INNER JOIN course_adds ON exam_grades.course_add_id = course_adds.id
                INNER JOIN students ON course_adds.student_id = students.id
                INNER JOIN published_courses ON course_adds.published_course_id = published_courses.id
                WHERE students.graduated = 0
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)
                    OR (exam_grades.department_reply = 0 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)
                )
                AND $queryPss
                GROUP BY exam_grades.course_add_id
                ORDER BY exam_grades.id DESC")->fetchAll('assoc');

            if (!empty($resultsAdds)) {
                foreach ($resultsAdds as $key => $value) {
                    if ($this->find()->where([
                        'ExamGrades.course_add_id' => $value['exam_grades']['course_add_id'],
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])->count()) {
                        unset($resultsAdds[$key]);
                    }
                }
            }

            $resultsMakeup = $this->query("SELECT exam_grades.*
                FROM exam_grades
                INNER JOIN makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
                INNER JOIN students ON makeup_exams.student_id = students.id
                INNER JOIN published_courses ON makeup_exams.published_course_id = published_courses.id
                WHERE students.graduated = 0
                AND (
                    (exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL)
                    OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)
                    OR (exam_grades.department_reply = 0 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)
                )
                AND $queryPss
                GROUP BY exam_grades.makeup_exam_id
                ORDER BY exam_grades.id DESC")->fetchAll('assoc');

            if (!empty($resultsMakeup)) {
                foreach ($resultsMakeup as $key => $value) {
                    if ($this->find()->where([
                        'ExamGrades.makeup_exam_id' => $value['exam_grades']['makeup_exam_id'],
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])->count()) {
                        unset($resultsMakeup[$key]);
                    }
                }
            }

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        $publicationIds = [];

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publicationIdsRegister = $this->CourseRegistrations->find('list')
                ->where(['CourseRegistrations.id IN' => $registrationAddMakeupIDs['register']])
                ->select(['CourseRegistrations.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsRegister);
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publicationIdsAdd = $this->CourseAdds->find('list')
                ->where(['CourseAdds.id IN' => $registrationAddMakeupIDs['add']])
                ->select(['CourseAdds.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsAdd);
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publicationIdsMakeup = $this->MakeupExams->find('list')
                ->where(['MakeupExams.id IN' => $registrationAddMakeupIDs['makeup']])
                ->select(['MakeupExams.published_course_id'])
                ->toArray();
            $publicationIds = array_merge($publicationIds, $publicationIdsMakeup);
        }

        $distinctPublicationIds = !empty($publicationIds) ? array_unique($publicationIds) : [];
        $publishedCourses = [];

        if (!empty($distinctPublicationIds)) {
            $conditions = [
                'PublishedCourses.id IN' => $distinctPublicationIds,
                "PublishedCourses.$givenBy IN" => is_array($departmentId) ? $departmentId : [$departmentId]
            ];

            $publishedCourses = $this->CourseRegistrations->PublishedCourses->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'GivenByDepartments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'type']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'CourseInstructorAssignments' => [
                        'fields' => ['id', 'published_course_id', 'staff_id'],
                        'Staffs' => [
                            'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'user_id'],
                            'Positions' => ['fields' => ['id', 'position']],
                            'Titles' => ['fields' => ['id', 'title']],
                            'Users' => ['fields' => ['id', 'username', 'email', 'active', 'email_verified']]
                        ],
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                    ],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                    ]
                ])
                ->toArray();
        }

        return $publishedCourses;
    }

    public function getAddSemester(?int $studentId = null, ?string $currentAcademicYear = null): array
    {
        $firstAdded = $this->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
            ->first();

        $lastAdded = $this->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->order(['CourseAdds.academic_year' => 'DESC', 'CourseAdds.semester' => 'DESC', 'CourseAdds.id' => 'DESC'])
            ->first();

        if (empty($firstAdded) && empty($lastAdded)) {
            return [];
        }

        return $this->CourseRegistrations->getLastestStudentSemesterAndAcademicYear($studentId, $currentAcademicYear);
    }

    public function getTotalCreditAndPointDeduction(?int $studentId = null, array $allAySList = []): array
    {
        $processedCourseReg = [];
        $processedCourseAdd = [];
        $deductCreditHourSum = 0;
        $deductGradePointSum = 0;
        $mDeductCreditHourSum = 0;
        $mDeductGradePointSum = 0;
        $creditAndPointDeduction = [];

        if (!empty($allAySList)) {
            foreach ($allAySList as $ayAndS) {
                $courseAndGrades = $this->getStudentCoursesAndFinalGrade($studentId, $ayAndS['academic_year'], $ayAndS['semester']);

                if (!empty($courseAndGrades)) {
                    foreach ($courseAndGrades as $registeredAddedCourse) {
                        if (isset($registeredAddedCourse['grade']) && (strcasecmp($registeredAddedCourse['grade'], 'I') === 0 || strcasecmp($registeredAddedCourse['grade'], 'W') === 0)) {
                            continue;
                        }

                        if (isset($registeredAddedCourse['grade']) && strcasecmp($registeredAddedCourse['grade'], 'W') !== 0 && strcasecmp($registeredAddedCourse['grade'], 'I') !== 0 && isset($registeredAddedCourse['used_in_gpa']) && $registeredAddedCourse['used_in_gpa']) {
                            if ($registeredAddedCourse['repeated_new'] === true) {
                                $previousAyAndS2 = $this->getListOfAyAndSemester($studentId, $ayAndS['academic_year'], $ayAndS['semester']);
                                $courseRegistrations = $this->CourseRegistrations->Students->CourseRegistrations->getCourseRegistrations($studentId, $previousAyAndS2, $registeredAddedCourse['course_id'], 1, 1);
                                $courseAdds = $this->CourseAdds->getCourseAdds($studentId, $previousAyAndS2, $registeredAddedCourse['course_id'], 1);

                                if (!empty($courseRegistrations)) {
                                    foreach ($courseRegistrations as $crValue) {
                                        if (!in_array($crValue['CourseRegistration']['id'], $processedCourseReg)) {
                                            $gradeDetail = $this->getApprovedGrade($crValue['CourseRegistration']['id'], 1);

                                            if (isset($gradeDetail['grade']) && (strcasecmp($gradeDetail['grade'], 'I') === 0 || strcasecmp($gradeDetail['grade'], 'W') === 0)) {
                                                continue;
                                            }

                                            $deductCreditHourSum += $crValue['PublishedCourse']['Course']['credit'];
                                            if (!empty($gradeDetail)) {
                                                $deductGradePointSum += ($gradeDetail['point_value'] * $crValue['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($crValue['PublishedCourse']['Course']['major'] == 1) {
                                                $mDeductCreditHourSum += $crValue['PublishedCourse']['Course']['credit'];
                                                if (!empty($gradeDetail)) {
                                                    $mDeductGradePointSum += ($gradeDetail['point_value'] * $crValue['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processedCourseReg[] = $crValue['CourseRegistration']['id'];
                                        }
                                    }
                                }

                                if (!empty($courseAdds)) {
                                    foreach ($courseAdds as $caValue) {
                                        if (!in_array($caValue['CourseAdd']['id'], $processedCourseAdd)) {
                                            $gradeDetail = $this->getApprovedGrade($caValue['CourseAdd']['id'], 0);

                                            if (isset($gradeDetail['grade']) && (strcasecmp($gradeDetail['grade'], 'I') === 0 || strcasecmp($gradeDetail['grade'], 'W') === 0)) {
                                                continue;
                                            }

                                            $deductCreditHourSum += $caValue['PublishedCourse']['Course']['credit'];
                                            if (!empty($gradeDetail)) {
                                                $deductGradePointSum += ($gradeDetail['point_value'] * $caValue['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($caValue['PublishedCourse']['Course']['major'] == 1) {
                                                $mDeductCreditHourSum += $caValue['PublishedCourse']['Course']['credit'];
                                                if (!empty($gradeDetail)) {
                                                    $mDeductGradePointSum += ($gradeDetail['point_value'] * $caValue['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processedCourseAdd[] = $caValue['CourseAdd']['id'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $creditAndPointDeduction['deduct_credit_hour_sum'] = $deductCreditHourSum;
        $creditAndPointDeduction['deduct_grade_point_sum'] = $deductGradePointSum;
        $creditAndPointDeduction['m_deduct_credit_hour_sum'] = $mDeductCreditHourSum;
        $creditAndPointDeduction['m_deduct_grade_point_sum'] = $mDeductGradePointSum;

        return $creditAndPointDeduction;
    }

    public function getTotalCreditPointDeduction(?int $studentId = null): array
    {
        $processedCourseReg = [];
        $processedCourseAdd = [];
        $deductCreditHourSum = 0;
        $deductGradePointSum = 0;
        $mDeductCreditHourSum = 0;
        $mDeductGradePointSum = 0;
        $creditAndPointDeduction = [];

        $allAySList = $this->getListOfAyAndSemester($studentId);

        if (!empty($allAySList)) {
            foreach ($allAySList as $ayAndS) {
                $courseAndGrades = $this->getStudentCoursesAndFinalGrade($studentId, $ayAndS['academic_year'], $ayAndS['semester']);

                if (!empty($courseAndGrades)) {
                    foreach ($courseAndGrades as $registeredAddedCourse) {
                        if (isset($registeredAddedCourse['grade']) && (strcasecmp($registeredAddedCourse['grade'], 'I') === 0 || strcasecmp($registeredAddedCourse['grade'], 'W') === 0)) {
                            continue;
                        }

                        if (strcasecmp($registeredAddedCourse['grade'], 'I') !== 0 && $registeredAddedCourse['used_in_gpa']) {
                            if ($registeredAddedCourse['repeated_new'] === true) {
                                $previousAyAndS2 = $this->getListOfAyAndSemester($studentId, $ayAndS['academic_year'], $ayAndS['semester']);
                                $courseRegistrations = $this->CourseRegistrations->Students->CourseRegistrations->getCourseRegistrations($studentId, $previousAyAndS2, $registeredAddedCourse['course_id'], 1, 1);
                                $courseAdds = $this->CourseAdds->getCourseAdds($studentId, $previousAyAndS2, $registeredAddedCourse['course_id'], 1);

                                if (!empty($courseRegistrations)) {
                                    foreach ($courseRegistrations as $crValue) {
                                        if (!in_array($crValue['CourseRegistration']['id'], $processedCourseReg)) {
                                            $gradeDetail = $this->getApprovedGrade($crValue['CourseRegistration']['id'], 1);

                                            $deductCreditHourSum += $crValue['PublishedCourse']['Course']['credit'];
                                            if (!empty($gradeDetail)) {
                                                $deductGradePointSum += ($gradeDetail['point_value'] * $crValue['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($crValue['PublishedCourse']['Course']['major'] == 1) {
                                                $mDeductCreditHourSum += $crValue['PublishedCourse']['Course']['credit'];
                                                if (!empty($gradeDetail)) {
                                                    $mDeductGradePointSum += ($gradeDetail['point_value'] * $crValue['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processedCourseReg[] = $crValue['CourseRegistration']['id'];
                                        }
                                    }
                                }

                                if (!empty($courseAdds)) {
                                    foreach ($courseAdds as $caValue) {
                                        if (!in_array($caValue['CourseAdd']['id'], $processedCourseAdd)) {
                                            $gradeDetail = $this->getApprovedGrade($caValue['CourseAdd']['id'], 0);

                                            $deductCreditHourSum += $caValue['PublishedCourse']['Course']['credit'];
                                            if (!empty($gradeDetail)) {
                                                $deductGradePointSum += ($gradeDetail['point_value'] * $caValue['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($caValue['PublishedCourse']['Course']['major'] == 1) {
                                                $mDeductCreditHourSum += $caValue['PublishedCourse']['Course']['credit'];
                                                if (!empty($gradeDetail)) {
                                                    $mDeductGradePointSum += ($gradeDetail['point_value'] * $caValue['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processedCourseAdd[] = $caValue['CourseAdd']['id'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $creditAndPointDeduction['deduct_credit_hour_sum'] = $deductCreditHourSum;
        $creditAndPointDeduction['deduct_grade_point_sum'] = $deductGradePointSum;
        $creditAndPointDeduction['m_deduct_credit_hour_sum'] = $mDeductCreditHourSum;
        $creditAndPointDeduction['m_deduct_grade_point_sum'] = $mDeductGradePointSum;

        return $creditAndPointDeduction;
    }

    public function getStudentAllCoursesAndFinalGrade(?int $studentId = null, ?string $currentAcademicYear = null, int $includeExempted = 0): array
    {
        $coursesAndGrades = [];

        $courseRegistered = $this->CourseRegistrations->find()
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses'])
            ->order(['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'])
            ->toArray();

        $courseAdded = $this->CourseAdds->find()
            ->where(['CourseAdds.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses'])
            ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
            ->toArray();

        if (!empty($courseAdded)) {
            foreach ($courseAdded as $key => $caValue) {
                if (!($caValue->published_course->add == 1 || ($caValue->department_approval == 1 && $caValue->registrar_confirmation == 1))) {
                    unset($courseAdded[$key]);
                }
            }
        }

        $studentDetail = $this->CourseAdds->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $studentLevel = $this->CourseAdds->Students->StudentExamStatuses->studentYearAndSemesterLevelOfStatus($studentId, $currentAcademicYear, null);

        $yearLevels = [
            1 => '1st',
            2 => '2nd',
            3 => '3rd'
        ];
        $studentYearLevel = isset($yearLevels[$studentLevel['year']]) ? $yearLevels[$studentLevel['year']] : 'th';

        $yearLevelId = null;
        if (!empty($studentDetail->department_id) && is_numeric($studentDetail->department_id)) {
            $yearLevelId = TableRegistry::getTableLocator()->get('YearLevels')->find()
                ->where(['YearLevels.department_id' => $studentDetail->department_id, 'YearLevels.name' => $studentYearLevel])
                ->select(['YearLevels.id'])
                ->first();
        }

        if (!empty($studentDetail->curriculum_id)) {
            $coursesToBeGiven = $this->CourseAdds->PublishedCourses->Courses->find()
                ->where([
                    'Courses.curriculum_id' => $studentDetail->curriculum_id,
                    'Courses.year_level_id' => $yearLevelId ? $yearLevelId->id : null
                ])
                ->toArray();

            if ($includeExempted == 1) {
                $allExemptedCourses = $this->CourseAdds->Students->CourseExemptions->find()
                    ->where([
                        'CourseExemptions.student_id' => $studentDetail->id,
                        'CourseExemptions.department_accept_reject' => 1,
                        'CourseExemptions.registrar_confirm_deny' => 1
                    ])
                    ->toArray();

                if (!empty($allExemptedCourses)) {
                    foreach ($allExemptedCourses as $exemptedCourse) {
                        foreach ($coursesToBeGiven as $courseToBeGiven) {
                            if ($courseToBeGiven->id == $exemptedCourse->course_id) {
                                $index = count($coursesAndGrades);
                                $coursesAndGrades[$index] = [
                                    'course_title' => $courseToBeGiven->course_title,
                                    'course_code' => $courseToBeGiven->course_code,
                                    'course_id' => $courseToBeGiven->id,
                                    'major' => $courseToBeGiven->major,
                                    'credit' => $courseToBeGiven->credit,
                                    'thesis' => $courseToBeGiven->thesis,
                                    'grade' => 'EX',
                                    'exit_exam' => $courseToBeGiven->exit_exam,
                                    'elective' => $courseToBeGiven->elective
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (!empty($courseRegistered)) {
            foreach ($courseRegistered as $value) {
                if (!$this->CourseRegistrations->isCourseDropped($value->id)) {
                    $index = count($coursesAndGrades);
                    $coursesAndGrades[$index] = [
                        'course_title' => $value->published_course->course->course_title,
                        'course_code' => $value->published_course->course->course_code,
                        'course_id' => $value->published_course->course->id,
                        'major' => $value->published_course->course->major,
                        'credit' => $value->published_course->course->credit,
                        'thesis' => $value->published_course->course->thesis,
                        'exit_exam' => $value->published_course->course->exit_exam,
                        'elective' => $value->published_course->course->elective
                    ];

                    $gradeDetail = $this->getApprovedGrade($value->id, 1);

                    if (!empty($gradeDetail)) {
                        $coursesAndGrades[$index]['grade'] = $gradeDetail['grade'];
                        if (isset($gradeDetail['point_value'])) {
                            $coursesAndGrades[$index]['point_value'] = $gradeDetail['point_value'];
                            $coursesAndGrades[$index]['pass_grade'] = $gradeDetail['pass_grade'];
                            $coursesAndGrades[$index]['used_in_gpa'] = $gradeDetail['used_in_gpa'];
                        }
                    }

                    $matchingCourses = [];
                    $cId = $value->published_course->course->id;
                    if (!empty($studentDetail->curriculum_id)) {
                        $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($cId, $studentDetail->curriculum_id);
                    }
                    $matchingCourses[$cId] = $cId;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $coursesAndGrades[$index]['repeated_old'] = false;
                        $coursesAndGrades[$index]['repeated_new'] = false;
                    } else {
                        $rep = $this->repeatationLabeling($registerAndAddFreq, 'register', $value->id, ['Student' => $studentDetail->toArray()], $coursesAndGrades[$index]['course_id']);
                        $coursesAndGrades[$index]['repeated_old'] = $rep['repeated_old'];
                        $coursesAndGrades[$index]['repeated_new'] = $rep['repeated_new'];
                    }
                }
            }
        }

        if (!empty($courseAdded)) {
            foreach ($courseAdded as $value) {
                $index = count($coursesAndGrades);
                $coursesAndGrades[$index] = [
                    'course_title' => $value->published_course->course->course_title,
                    'course_code' => $value->published_course->course->course_code,
                    'course_id' => $value->published_course->course->id,
                    'major' => $value->published_course->course->major,
                    'credit' => $value->published_course->course->credit,
                    'thesis' => $value->published_course->course->thesis,
                    'exit_exam' => $value->published_course->course->exit_exam,
                    'elective' => $value->published_course->course->elective
                ];

                $gradeDetail = $this->getApprovedGrade($value->id, 0);

                if (!empty($gradeDetail)) {
                    $coursesAndGrades[$index]['grade'] = $gradeDetail['grade'];
                    if (isset($gradeDetail['point_value'])) {
                        $coursesAndGrades[$index]['point_value'] = $gradeDetail['point_value'];
                        $coursesAndGrades[$index]['pass_grade'] = $gradeDetail['pass_grade'];
                        $coursesAndGrades[$index]['used_in_gpa'] = $gradeDetail['used_in_gpa'];
                    }
                }

                $matchingCourses = [];
                $cId = $value->published_course->course->id;
                if (!empty($studentDetail->curriculum_id)) {
                    $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($cId, $studentDetail->curriculum_id);
                }
                $matchingCourses[$cId] = $cId;

                $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                if (count($registerAndAddFreq) <= 1) {
                    $coursesAndGrades[$index]['repeated_old'] = false;
                    $coursesAndGrades[$index]['repeated_new'] = false;
                } else {
                    $rep = $this->repeatationLabeling($registerAndAddFreq, 'add', $value->id, ['Student' => $studentDetail->toArray()], $coursesAndGrades[$index]['course_id']);
                    $coursesAndGrades[$index]['repeated_old'] = $rep['repeated_old'];
                    $coursesAndGrades[$index]['repeated_new'] = $rep['repeated_new'];
                }
            }
        }

        return $coursesAndGrades;
    }

    public function getPublishedCourseIfExist(int $departmentId, string $academicYear, string $semester, int $programId, int $programTypeId, array $studentDetail, string $admissionAcademicYear, ?string $currentAcademicYear = null): array
    {
        $section = [];
        $studentAyAndS = $this->CourseRegistrations->Students->StudentExamStatuses->getStudentFirstAyAndSemester($studentDetail['Student']['id'], $admissionAcademicYear);
        $studentAyAndSList = $this->getListOfAyAndSemester($studentDetail['Student']['id']);
        $studentAyAndSLists = [];

        if (!empty($studentAyAndSList)) {
            foreach ($studentAyAndSList as $v) {
                $withdrawalAfterRegistration = $this->CourseRegistrations->Students->Clearances->withDrawaAfterRegistration($studentDetail['Student']['id'], $v['academic_year'], $v['semester']);
                if (!$withdrawalAfterRegistration) {
                    $studentAyAndSLists[] = $v;
                }
            }
            $studentAyAndSList = $studentAyAndSLists;
        }

        if (!empty($studentAyAndSList)) {
            $lastKey = count($studentAyAndSList) - 1;
            $studentAyAndS['academic_year'] = $studentAyAndSList[$lastKey]['academic_year'];
            $studentAyAndS['semester'] = $studentAyAndSList[$lastKey]['semester'];
            $nextAcademicYearSemester = $this->CourseRegistrations->Students->StudentExamStatuses->getNextSemester($studentAyAndS['academic_year'], $studentAyAndS['semester']);
        } else {
            $nextAcademicYearSemester = $studentAyAndS;
        }

        if (!empty($studentAyAndSList)) {
            foreach ($studentAyAndSList as $ay) {
                if ($ay['semester'] == $semester && $ay['academic_year'] == $academicYear) {
                    $nextAcademicYearSemester = $ay;
                    break;
                }
            }
        }

        if (!empty($studentAyAndS)) {
            $statusLevel = $this->CourseRegistrations->Students->StudentExamStatuses->studentYearAndSemesterLevel($studentDetail['Student']['id'], $studentAyAndS['academic_year'], $studentAyAndS['semester']);
            $yearLevel = $this->CourseRegistrations->YearLevels->find()
                ->where(['YearLevels.name' => $statusLevel['year'], 'YearLevels.department_id' => $studentDetail['Student']['department_id']])
                ->first();

            $sectionFil = $this->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.student_id' => $studentDetail['Student']['id'],
                    'CourseRegistrations.semester' => $studentAyAndS['semester'],
                    'CourseRegistrations.academic_year' => $studentAyAndS['academic_year']
                ])
                ->contain(['Sections'])
                ->first();

            if (!empty($sectionFil)) {
                $studentSection = $this->CourseRegistrations->Sections->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $studentDetail['Student']['id'],
                        'StudentsSections.section_id' => $sectionFil->section->id
                    ])
                    ->first();
            } else {
                $studentSection = $this->CourseRegistrations->Sections->StudentsSections->find()
                    ->where(['StudentsSections.student_id' => $studentDetail['Student']['id']])
                    ->order(['StudentsSections.section_id' => 'DESC'])
                    ->first();
            }

            if (!empty($studentSection)) {
                $section = $this->CourseRegistrations->Sections->find()
                    ->where(['Sections.id' => $studentSection->section_id])
                    ->first();

                if ($section->academicyear != $academicYear) {
                    $studentSectionnns = $this->CourseRegistrations->Sections->StudentsSections->find()
                        ->where([
                            'StudentsSections.student_id' => $studentDetail['Student']['id'],
                            'StudentsSections.section_id IN' => $this->CourseRegistrations->Sections->find()->where(['Sections.academicyear' => $academicYear])->select(['Sections.id'])
                        ])
                        ->order(['StudentsSections.id' => 'DESC', 'StudentsSections.section_id' => 'DESC'])
                        ->first();

                    if (!empty($studentSectionnns)) {
                        $section = $this->CourseRegistrations->Sections->find()
                            ->where(['Sections.id' => $studentSectionnns->section_id])
                            ->first();
                    }
                }
            } else {
                $section = $this->CourseRegistrations->Sections->find()
                    ->where([
                        'Sections.department_id' => $departmentId,
                        'Sections.program_id' => $studentDetail['Student']['program_id'],
                        'Sections.program_type_id' => $studentDetail['Student']['program_type_id'],
                        'Sections.year_level_id' => $yearLevel ? $yearLevel->id : null,
                        'Sections.academicyear' => $studentAyAndS['academic_year']
                    ])
                    ->first();
            }

            if (!empty($section->academicyear)) {
                if ($nextAcademicYearSemester['academic_year'] != $section->academicyear && $yearLevel && $yearLevel->name == $statusLevel['year'] && $nextAcademicYearSemester['academic_year'] != $currentAcademicYear) {
                    $nextAcademicYearSemester['academic_year'] = $section->academicyear;
                    $nextAcademicYearSemester['semester'] = 'I';
                }
            }

            $studentSectionId = null;

            if (!empty($section->id) && !empty($studentDetail['Student']['id'])) {
                $studentSection = $this->CourseRegistrations->Sections->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $studentDetail['Student']['id'],
                        'StudentsSections.section_id' => $section->id
                    ])
                    ->order(['StudentsSections.created' => 'DESC'])
                    ->first();
            }

            if (empty($studentSection) && !empty($section)) {
                if (!empty($studentDetail['Student']['curriculum_id'])) {
                    $studentSectionEntity = $this->CourseRegistrations->Sections->StudentsSections->newEntity([
                        'student_id' => $studentDetail['Student']['id'],
                        'section_id' => $section->id,
                        'archive' => ($currentAcademicYear == $nextAcademicYearSemester['academic_year']) ? 0 : 0
                    ]);
                    $this->CourseRegistrations->Sections->StudentsSections->save($studentSectionEntity);
                    $studentSectionId = $studentSectionEntity->section_id;
                }
            } else {
                if (!empty($studentSection)) {
                    $studentSectionId = $studentSection->section_id;
                }
            }

            $options = [];
            $options['conditions'] = [
                'PublishedCourses.program_id' => !empty($section->program_id) ? $section->program_id : $programId,
                'PublishedCourses.program_type_id' => !empty($section->program_type_id) ? $section->program_type_id : $programTypeId
            ];

            if (!empty($academicYear) && !empty($semester)) {
                $sectionId = $this->CourseRegistrations->Sections->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $studentDetail['Student']['id'],
                        'StudentsSections.section_id IN' => $this->CourseRegistrations->Sections->find()->where(['Sections.academicyear' => $academicYear])->select(['Sections.id'])
                    ])
                    ->select(['StudentsSections.section_id'])
                    ->first();

                $options['conditions']['PublishedCourses.semester'] = $semester;
                $options['conditions']['PublishedCourses.academic_year'] = $academicYear;
                $options['conditions']['PublishedCourses.section_id'] = $studentSectionId ?? ($sectionId->section_id ?? 0);
            } else {
                $options['conditions']['PublishedCourses.semester'] = $nextAcademicYearSemester['semester'];
                $options['conditions']['PublishedCourses.academic_year'] = $nextAcademicYearSemester['academic_year'];
                $options['conditions']['PublishedCourses.section_id'] = $studentSectionId ?? 0;
            }

            $options['contain'] = [
                'Courses' => [
                    'fields' => ['id', 'course_title', 'course_code', 'credit'],
                    'Prerequisites',
                    'GradeTypes.Grades' => ['fields' => ['id', 'grade']]
                ],
                'CourseInstructorAssignments' => [
                    'Staffs' => [
                        'fields' => ['id',  'first_name', 'middle_name', 'last_name'],
                        'Titles' => ['fields' => ['id', 'title']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name']],
                        'Positions' => ['fields' => ['id', 'position']]
                    ],
                    'order' => ['isprimary' => 'DESC'],
                    'limit' => 1
                ]
            ];

            $options['fields'] = [
                'DISTINCT PublishedCourses.course_id',
                'PublishedCourses.department_id',
                'PublishedCourses.academic_year',
                'PublishedCourses.semester',
                'PublishedCourses.program_id',
                'PublishedCourses.program_type_id',
                'PublishedCourses.id',
                'PublishedCourses.section_id',
                'PublishedCourses.grade_scale_id',
                'PublishedCourses.year_level_id'
            ];

            $studentCourseRegistrations['courses'] = $this->CourseRegistrations->PublishedCourses->find()
                ->where($options['conditions'])
                ->contain($options['contain'])
                ->select($options['fields'])
                ->toArray();

            if (!empty($studentCourseRegistrations['courses'])) {
                foreach ($studentCourseRegistrations['courses'] as &$value) {
                    $failedAnyPrerequisite = ['freq' => 0];
                    $isGradeSubmitted = $this->isGradeSubmittedForPublishedCourseGivenStudentId($studentDetail['Student']['id'], $value->id);

                    $registeredOrAddCourse = $this->CourseRegistrations->find()
                        ->where([
                            'CourseRegistrations.student_id' => $studentDetail['Student']['id'],
                            'CourseRegistrations.published_course_id' => $value->id
                        ])
                        ->first();

                    if (!empty($registeredOrAddCourse)) {
                        $value->grade = $this->getApprovedGrade($registeredOrAddCourse->id, 1);
                        $value->course_registration = $registeredOrAddCourse;
                    } else {
                        $registeredOrAddCourse = $this->CourseAdds->find()
                            ->where([
                                'CourseAdds.student_id' => $studentDetail['Student']['id'],
                                'CourseAdds.published_course_id' => $value->id
                            ])
                            ->first();

                        if (!empty($registeredOrAddCourse)) {
                            $value->grade = $this->getApprovedGrade($registeredOrAddCourse->id, 0);
                            $value->course_add = $registeredOrAddCourse;
                        }
                    }

                    if (!empty($value->course->prerequisites)) {
                        foreach ($value->course->prerequisites as $preValue) {
                            $failed = TableRegistry::getTableLocator()->get('CourseDrops')->prerequisiteTaken($studentDetail['Student']['id'], $preValue->prerequisite_course_id);
                            if ($failed == 0 && !$preValue->co_requisite) {
                                $failedAnyPrerequisite['freq']++;
                            }
                        }
                    }

                    $value->prerequisite_failed = $failedAnyPrerequisite['freq'] > 0;
                    $value->read_only = $isGradeSubmitted;

                    $value->have_assigned_instructor = !empty($value->course_instructor_assignments);
                    $value->course->grade_scale_id = TableRegistry::getTableLocator()->get('GradeScales')->getGradeScaleIdGivenPublishedCourse($value->id);
                }
            }
        }

        return $studentCourseRegistrations;
    }

    public function getPublishedCourseGradeGradeScale(int $id): int
    {
        $courseRegistration = $this->CourseRegistrations->find()
            ->where([
                'CourseRegistrations.published_course_id' => $id,
                'CourseRegistrations.id IN' => $this->find()
                    ->where([
                        'ExamGrades.grade_scale_id IS NOT NULL',
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1,
                        'ExamGrades.registrar_reason NOT IN' => ['Via backend data entry interface']
                    ])
                    ->select(['ExamGrades.course_registration_id'])
            ])
            ->contain(['ExamGrades'])
            ->first();

        if (!empty($courseRegistration) && !empty($courseRegistration->exam_grades)) {
            return $courseRegistration->exam_grades[0]->grade_scale_id;
        }

        $courseAdd = $this->CourseAdds->find()
            ->where([
                'CourseAdds.published_course_id' => $id,
                'CourseAdds.id IN' => $this->find()
                    ->where([
                        'ExamGrades.grade_scale_id IS NOT NULL',
                        'ExamGrades.department_approval' => 1,
                        'ExamGrades.registrar_approval' => 1
                    ])
                    ->select(['ExamGrades.course_add_id'])
            ])
            ->contain(['ExamGrades'])
            ->first();

        if (!empty($courseAdd) && !empty($courseAdd->exam_grades)) {
            return $courseAdd->exam_grades[0]->grade_scale_id;
        }

        return 0;
    }

    public function isGradeSubmittedForPublishedCourseGivenStudentId(int $studentId, $publishedCourseIds): int
    {
        $publishedCoursesStudentRegisteredScoreGrade = 0;

        $gradeSubmittedRegisteredCourses = $this->CourseRegistrations->find('list')
            ->where([
                'CourseRegistrations.published_course_id' => is_array($publishedCourseIds) ? $publishedCourseIds : [$publishedCourseIds],
                'CourseRegistrations.student_id' => $studentId
            ])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        if (!empty($gradeSubmittedRegisteredCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_registration_id IN' => $gradeSubmittedRegisteredCourses])
                ->count();
            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return $publishedCoursesStudentRegisteredScoreGrade;
            }
        }

        $gradeSubmittedAddCourses = $this->CourseAdds->find('list')
            ->where([
                'CourseAdds.published_course_id' => is_array($publishedCourseIds) ? $publishedCourseIds : [$publishedCourseIds],
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($gradeSubmittedAddCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_add_id IN' => $gradeSubmittedAddCourses])
                ->count();
            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return $publishedCoursesStudentRegisteredScoreGrade;
            }
        }

        return $publishedCoursesStudentRegisteredScoreGrade;
    }

    public function getCourseRepetation(int $courseRegAddId, int $studentId, int $registered = 1): array
    {
        $studentDetail = $this->CourseRegistrations->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $coursesAndGrades = [];
        $courseRegistered = [];
        $courseAdded = [];

        if ($registered == 1) {
            $courseRegistered = $this->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.id' => $courseRegAddId,
                    'CourseRegistrations.id NOT IN' => $this->CourseRegistrations->CourseDrops->find()
                        ->where(['CourseDrops.registrar_confirmation' => 1, 'CourseDrops.department_approval' => 1])
                        ->select(['CourseDrops.course_registration_id'])
                ])
                ->contain(['PublishedCourses.Courses'])
                ->toArray();
        } else {
            $courseAdded = $this->CourseAdds->find()
                ->where([
                    'CourseAdds.id' => $courseRegAddId,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1
                ])
                ->contain(['PublishedCourses.Courses'])
                ->toArray();
        }

        if (!empty($courseRegistered)) {
            foreach ($courseRegistered as $value) {
                if (!$this->CourseRegistrations->isCourseDropped($value->id) && !empty($value->published_course->course->id)) {
                    $coursesAndGrades = [
                        'course_title' => $value->published_course->course->course_title,
                        'course_code' => $value->published_course->course->course_code,
                        'course_id' => $value->published_course->course->id,
                        'major' => $value->published_course->course->major,
                        'credit' => $value->published_course->course->credit,
                        'thesis' => $value->published_course->course->thesis
                    ];

                    $gradeDetail = $this->getApprovedGrade($value->id, 1);

                    if (!empty($gradeDetail)) {
                        $coursesAndGrades['grade'] = $gradeDetail['grade'];
                        if (isset($gradeDetail['point_value'])) {
                            $coursesAndGrades['point_value'] = $gradeDetail['point_value'];
                            $coursesAndGrades['pass_grade'] = $gradeDetail['pass_grade'];
                            $coursesAndGrades['used_in_gpa'] = $gradeDetail['used_in_gpa'];
                        }
                    }

                    $matchingCourses = [];
                    $cId = $value->published_course->course->id;
                    if (!empty($studentDetail->curriculum_id)) {
                        $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($cId, $studentDetail->curriculum_id, 0);
                    }
                    $matchingCourses[$cId] = $cId;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $coursesAndGrades['repeated_old'] = false;
                        $coursesAndGrades['repeated_new'] = false;
                    } else {
                        $rep = $this->repeatationLabeling($registerAndAddFreq, 'register', $value->id, ['Student' => $studentDetail->toArray()], $coursesAndGrades['course_id']);
                        $coursesAndGrades['repeated_old'] = $rep['repeated_old'];
                        $coursesAndGrades['repeated_new'] = $rep['repeated_new'];
                    }
                }
            }
        }

        if (!empty($courseAdded)) {
            foreach ($courseAdded as $value) {
                if (!empty($value->published_course->course->id)) {
                    $coursesAndGrades = [
                        'course_title' => $value->published_course->course->course_title,
                        'course_code' => $value->published_course->course->course_code,
                        'course_id' => $value->published_course->course->id,
                        'major' => $value->published_course->course->major,
                        'credit' => $value->published_course->course->credit,
                        'thesis' => $value->published_course->course->thesis
                    ];

                    $gradeDetail = $this->getApprovedGrade($value->id, 0);

                    if (!empty($gradeDetail)) {
                        $coursesAndGrades['grade'] = $gradeDetail['grade'];
                        if (isset($gradeDetail['point_value'])) {
                            $coursesAndGrades['point_value'] = $gradeDetail['point_value'];
                            $coursesAndGrades['pass_grade'] = $gradeDetail['pass_grade'];
                            $coursesAndGrades['used_in_gpa'] = $gradeDetail['used_in_gpa'];
                        }
                    }

                    $matchingCourses = [];
                    $cId = $value->published_course->course->id;
                    if (!empty($studentDetail->curriculum_id)) {
                        $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($cId, $studentDetail->curriculum_id);
                    }
                    $matchingCourses[$cId] = $cId;

                    $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentDetail->id);

                    if (count($registerAndAddFreq) <= 1) {
                        $coursesAndGrades['repeated_old'] = false;
                        $coursesAndGrades['repeated_new'] = false;
                    } else {
                        $rep = $this->repeatationLabeling($registerAndAddFreq, 'add', $value->id, ['Student' => $studentDetail->toArray()], $coursesAndGrades['course_id']);
                        $coursesAndGrades['repeated_old'] = $rep['repeated_old'];
                        $coursesAndGrades['repeated_new'] = $rep['repeated_new'];
                    }
                }
            }
        }

        return $coursesAndGrades;
    }

    public function gradeSubmittedForAYSem(int $studentId, string $academicYear, string $semester): int
    {
        $gradeSubmittedRegisteredCourses = $this->CourseRegistrations->find('list')
            ->where([
                'CourseRegistrations.student_id' => $studentId,
                'CourseRegistrations.semester' => $semester,
                'CourseRegistrations.academic_year' => $academicYear
            ])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        if (!empty($gradeSubmittedRegisteredCourses)) {
            foreach ($gradeSubmittedRegisteredCourses as $v) {
                $gradeDetail = $this->getApprovedGrade($v, 1);
                if (isset($gradeDetail['grade']) && $gradeDetail['grade'] != 'W') {
                    return 1;
                }
            }
        }

        $gradeSubmittedAddedCourses = $this->CourseAdds->find('list')
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.semester' => $semester,
                'CourseAdds.academic_year' => $academicYear
            ])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($gradeSubmittedAddedCourses)) {
            foreach ($gradeSubmittedAddedCourses as $v) {
                $gradeDetail = $this->getApprovedGrade($v, 0);
                if (isset($gradeDetail['grade']) && $gradeDetail['grade'] != 'W') {
                    return 1;
                }
            }
        }

        return 0;
    }

    public function checkCourseFrequencyTaken(int $studentId, string $academicYear, string $semester, int $courseId): array
    {
        $coursesAndGrades = [];
        $matchingCourses = TableRegistry::getTableLocator()->get('Courses')->getTakenEquivalentCourses($studentId, $courseId);

        $studentDetail = $this->CourseAdds->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $courseRegistered = $this->CourseRegistrations->find()
            ->where([
                'CourseRegistrations.student_id' => $studentId,
                'CourseRegistrations.academic_year' => $academicYear,
                'CourseRegistrations.semester' => $semester
            ])
            ->contain(['PublishedCourses.Courses'])
            ->toArray();

        $courseAdded = $this->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.academic_year' => $academicYear,
                'CourseAdds.semester' => $semester
            ])
            ->contain(['PublishedCourses.Courses'])
            ->toArray();

        if (!empty($courseAdded)) {
            foreach ($courseAdded as $key => $caValue) {
                if (!($caValue->published_course->add == 1 || ($caValue->department_approval == 1 && $caValue->registrar_confirmation == 1))) {
                    unset($courseAdded[$key]);
                }
            }
        }

        $registrationAndCourseAddMerged = array_merge($courseAdded, $courseRegistered);

        if (!empty($registrationAndCourseAddMerged)) {
            foreach ($registrationAndCourseAddMerged as $value) {
                $registerAndAddFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentId);

                if (count($registerAndAddFreq) <= 1) {
                    $coursesAndGrades[$academicYear]['repeated_old'] = false;
                    $coursesAndGrades[$academicYear]['repeated_new'] = false;
                } else {
                    $rept = $this->repeatationLabeling(
                        $registerAndAddFreq,
                        'add',
                        $value->id ?? $value->course_add->id,
                        ['Student' => $studentDetail->toArray()],
                        $value->published_course->course_id
                    );
                    $coursesAndGrades[$academicYear]['repeated_old'] = $rept['repeated_old'];
                    $coursesAndGrades[$academicYear]['repeated_new'] = $rept['repeated_new'];
                }
            }
        }

        return $coursesAndGrades;
    }



    public function getListOfSubmittedGradeForDepartmentApproval(?int $colDptId = null, int $department = 1): array
    {
        $departmentActionRequiredList = $this->find()
            ->where([
                'ExamGrades.department_approval IS NULL',
                'ExamGrades.registrar_approval IS NULL'
            ])
            ->contain([
                'CourseRegistrations.PublishedCourses' => [
                    'CourseInstructorAssignments' => [
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                        'Staffs'
                    ],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit']]
                ],
                'CourseAdds.PublishedCourses' => [
                    'CourseInstructorAssignments' => [
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                        'Staffs'
                    ],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit']]
                ],
                'MakeupExams.PublishedCourses' => [
                    'CourseInstructorAssignments' => [
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                        'Staffs'
                    ],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit']]
                ]
            ])
            ->order(['ExamGrades.id' => 'DESC'])
            ->toArray();

        $publishedCourses = [];

        if (!empty($departmentActionRequiredList)) {
            foreach ($departmentActionRequiredList as $gradeChangeDetail) {
                if (
                    !empty($gradeChangeDetail->course_registration) &&
                    $gradeChangeDetail->course_registration->id &&
                    (
                        ($department == 1 && $gradeChangeDetail->course_registration->published_course->given_by_department_id == $colDptId) ||
                        ($department == 0 && $gradeChangeDetail->course_registration->published_course->college_id == $colDptId)
                    )
                ) {
                    $publishedCourses[] = $gradeChangeDetail->course_registration->published_course;
                }
            }
        }

        return $publishedCourses;
    }

    public function getListOfGradeForRegistrarApproval(?array $departmentIds = null, ?array $collegeIds = null): array
    {
        $registrarActionRequiredList = $this->find()
            ->where([
                'ExamGrades.department_approval' => 1,
                'ExamGrades.registrar_approval IS NULL'
            ])
            ->contain([
                'CourseRegistrations.PublishedCourses' => [
                    'CourseInstructorAssignments' => [
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                        'Staffs'
                    ],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit']]
                ],
                'CourseAdds.PublishedCourses' => [
                    'CourseInstructorAssignments' => [
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                        'Staffs'
                    ],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit']]
                ],
                'MakeupExams.PublishedCourses' => [
                    'CourseInstructorAssignments' => [
                        'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                        'Staffs'
                    ],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'course_detail_hours', 'credit']]
                ]
            ])
            ->order(['ExamGrades.id' => 'DESC'])
            ->toArray();

        $publishedCourses = [];

        if (!empty($registrarActionRequiredList)) {
            foreach ($registrarActionRequiredList as $gradeDetail) {
                if (empty($gradeDetail->registrar_approval) && $gradeDetail->department_approval == 1) {
                    if (
                        !empty($gradeDetail->course_registration) &&
                        $gradeDetail->course_registration->id &&
                        (
                            (!empty($departmentIds) && !empty($gradeDetail->course_registration->published_course->department->id) && in_array($gradeDetail->course_registration->published_course->department->id, $departmentIds)) ||
                            (!empty($collegeIds) && !empty($gradeDetail->course_registration->published_course->college_id) && in_array($gradeDetail->course_registration->published_course->college_id, $collegeIds))
                        )
                    ) {
                        $publishedCourses[$gradeDetail->course_registration->published_course->id] = [
                            'PublishedCourse' => $gradeDetail->course_registration->published_course,
                            'Course' => $gradeDetail->course_registration->published_course->course,
                            'Program' => $gradeDetail->course_registration->published_course->program,
                            'ProgramType' => $gradeDetail->course_registration->published_course->program_type,
                            'Section' => $gradeDetail->course_registration->published_course->section,
                            'YearLevel' => $gradeDetail->course_registration->published_course->year_level,
                            'Department' => $gradeDetail->course_registration->published_course->department,
                            'CourseInstructorAssignment' => $gradeDetail->course_registration->published_course->course_instructor_assignments
                        ];
                    } elseif (
                        !empty($gradeDetail->course_add) &&
                        (
                            (!empty($departmentIds) && !empty($gradeDetail->course_add->published_course->department->id) && in_array($gradeDetail->course_add->published_course->department->id, $departmentIds)) ||
                            (!empty($collegeIds) && !empty($gradeDetail->course_add->published_course->college_id) && in_array($gradeDetail->course_add->published_course->college_id, $collegeIds))
                        )
                    ) {
                        $publishedCourses[$gradeDetail->course_add->published_course->id] = [
                            'PublishedCourse' => $gradeDetail->course_add->published_course,
                            'Course' => $gradeDetail->course_add->published_course->course,
                            'Program' => $gradeDetail->course_add->published_course->program,
                            'ProgramType' => $gradeDetail->course_add->published_course->program_type,
                            'Section' => $gradeDetail->course_add->published_course->section,
                            'YearLevel' => $gradeDetail->course_add->published_course->year_level,
                            'Department' => $gradeDetail->course_add->published_course->department,
                            'CourseInstructorAssignment' => $gradeDetail->course_add->published_course->course_instructor_assignments
                        ];
                    }
                }
            }
        }

        return $publishedCourses;
    }

    public function getMostApprovedGradeForSMS(string $phoneNumber): string
    {
        $studentDetail = TableRegistry::getTableLocator()->get('Students')->find()
            ->where(['Students.phone_mobile' => $phoneNumber])
            ->contain(['Users'])
            ->first();

        if (!empty($studentDetail)) {
            $mostRecentRegistration = $this->getMostRecentRegistrationDetail($studentDetail->id);
            $coursesTaken = $this->getStudentCoursesAndFinalGrade(
                $studentDetail->id,
                $mostRecentRegistration->academic_year,
                $mostRecentRegistration->semester,
                1
            );
            return $this->formateGradeForSMS($coursesTaken, $studentDetail->toArray());
        } else {
            $parentPhone = TableRegistry::getTableLocator()->get('Contacts')->find()
                ->where(['Contacts.phone_mobile' => $phoneNumber])
                ->contain(['Students'])
                ->toArray();

            if (!empty($parentPhone)) {
                $allOfTheirKids = 'Your child ';
                foreach ($parentPhone as $pv) {
                    $mostRecentRegistration = $this->getMostRecentRegistrationDetail($pv->student->id);
                    $coursesTaken = $this->getStudentCoursesAndFinalGrade(
                        $pv->student->id,
                        $mostRecentRegistration->academic_year,
                        $mostRecentRegistration->semester,
                        1
                    );
                    $allOfTheirKids .= $this->formateGradeForSMS($coursesTaken, $pv->toArray());
                }
                return $allOfTheirKids;
            }
        }

        return "You don't have the privilege to view grades.";
    }

    public function getMostRecentRegistrationDetail(int $studentId)
    {
        return $this->CourseRegistrations->find()
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses'])
            ->order([
                'CourseRegistrations.id' => 'DESC',
                'CourseRegistrations.academic_year' => 'DESC',
                'CourseRegistrations.semester' => 'DESC'
            ])
            ->first();
    }

    public function formateGradeForSMS(array $gradeLists, array $studentDetail): string
    {
        $display = $studentDetail['Student']['first_name'] . ' ' . $studentDetail['Student']['last_name'] . '(' . $studentDetail['Student']['studentnumber'] . ') has scored :-';

        if (!empty($gradeLists)) {
            foreach ($gradeLists as $value) {
                $display .= $value['course_title'] . ' => ' . $value['grade'] . '  ';
            }
        }

        return $display;
    }

    public function getGradeDetailsForEmailNotification(int $examGradeId): array
    {
        $details = $this->find()
            ->where(['ExamGrades.id' => $examGradeId])
            ->contain([
                'CourseAdds' => [
                    'Students',
                    'PublishedCourses' => [
                        'Courses',
                        'CourseInstructorAssignments.Staffs'
                    ]
                ],
                'MakeupExams' => [
                    'Students',
                    'PublishedCourses' => [
                        'Courses',
                        'CourseInstructorAssignments.Staffs'
                    ]
                ],
                'CourseRegistrations' => [
                    'Students',
                    'PublishedCourses' => [
                        'Courses',
                        'CourseInstructorAssignments.Staffs'
                    ]
                ]
            ])
            ->first();

        return $details ? $details->toArray() : [];
    }

    public function getLetterGradeStatistics(int $publishedCourseId): array
    {
        if (empty($publishedCourseId)) {
            return [];
        }

        $graph = ['data' => [], 'series' => [], 'labels' => ['Grade']];
        $gradeStats = ['statistics' => []];

        $publishedCoursesReg = $this->CourseRegistrations->find()
            ->where(['CourseRegistrations.published_course_id' => $publishedCourseId])
            ->contain([
                'Students',
                'ExamGrades',
                'PublishedCourses' => [
                    'Sections',
                    'YearLevels',
                    'Programs',
                    'ProgramTypes',
                    'Courses',
                    'Departments'
                ]
            ])
            ->toArray();

        $publishedCoursesAdd = $this->CourseAdds->find()
            ->where(['CourseAdds.published_course_id' => $publishedCourseId])
            ->contain([
                'Students',
                'ExamGrades',
                'PublishedCourses' => [
                    'Sections',
                    'YearLevels',
                    'Programs',
                    'ProgramTypes',
                    'Courses',
                    'Departments'
                ]
            ])
            ->toArray();

        $publishedCourses = array_merge($publishedCoursesReg, $publishedCoursesAdd);

        if (!empty($publishedCourses)) {
            foreach ($publishedCourses as $publishedCourse) {
                $gradee = !empty($publishedCourse->course_registration)
                    ? $this->CourseRegistrations->ExamGrades->getGradeForStats($publishedCourse->course_registration->id, 1)
                    : $this->CourseRegistrations->ExamGrades->getGradeForStats($publishedCourse->course_add->id, 0);

                if (!empty($gradee['grade'])) {
                    $graph['series'][$gradee['grade']] = $gradee['grade'];
                    $graph['data'][$gradee['grade']][0] = ($graph['data'][$gradee['grade']][0] ?? 0) + 1;
                    $gradeStats['statistics'][$gradee['grade']] = ($gradeStats['statistics'][$gradee['grade']] ?? 0) + 1;
                }
            }
        }

        $gradeStats['graph'] = $graph;
        return $gradeStats;
    }

    public function afterSave($entity, $options = [])
    {
        parent::afterSave($entity, $options);
        if ($entity->isNew()) {
            $event = new Event('Model.ExamGrade.createdModified', $this, ['id' => $entity->id]);
            $this->getEventManager()->dispatch($event);
        }
    }

    public function repeatationLabeling(array $registerAndAddFreq, string $type = 'add', int $addRegValue, array $studentDetail, int $courseId): array
    {
        $rep = ['repeated_old' => false, 'repeated_new' => false];

        $matchingCourses = TableRegistry::getTableLocator()->get('EquivalentCourses')->validEquivalentCourse($courseId, $studentDetail['Student']['curriculum_id']);
        $matchingCourses[$courseId] = $courseId;

        $isLastRegistration = $this->isTheLastRegOrAdd($matchingCourses, $studentDetail['Student']['id'], $courseId, $addRegValue);

        if (!empty($registerAndAddFreq)) {
            foreach ($registerAndAddFreq as $k) {
                if ($k['id'] == $addRegValue && strcasecmp($k['type'], $type) === 0 && !$isLastRegistration) {
                    $rep['repeated_old'] = true;
                    $rep['repeated_new'] = false;
                }
            }
        }

        $rep['repeated_new'] = $isLastRegistration;
        return $rep;
    }

    public function isTheLastRegOrAdd(array $matchingCourses, int $studentId, int $courseId, int $addRegValue): bool
    {
        $addRegFreq = $this->getCourseFrequenceRegAdds($matchingCourses, $studentId);
        $lastElement = end($addRegFreq);
        return $lastElement['course_id'] == $courseId && $addRegValue == $lastElement['id'];
    }

    public function getCourseFrequenceRegAdds(array $matchingCourses, int $studentId): array
    {
        $coursesSeparatedByComa = implode(',', $matchingCourses);
        $addFreq = [];
        $registrationFreq = [];
        $registerAndAddFreq = [];

        if (!empty($coursesSeparatedByComa)) {
            $registrationFreq = $this->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.student_id' => $studentId,
                    'CourseRegistrations.published_course_id IN' => $this->CourseRegistrations->PublishedCourses->find()
                        ->where(['PublishedCourses.course_id IN' => $matchingCourses])
                        ->select(['PublishedCourses.id']),
                    'CourseRegistrations.id NOT IN' => $this->CourseRegistrations->CourseDrops->find()
                        ->where(['CourseDrops.registrar_confirmation' => 1, 'CourseDrops.department_approval' => 1])
                        ->select(['CourseDrops.course_registration_id'])
                ])
                ->contain(['PublishedCourses'])
                ->order(['CourseRegistrations.id' => 'ASC'])
                ->toArray();

            $addFreq = $this->CourseAdds->find()
                ->where([
                    'CourseAdds.student_id' => $studentId,
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1,
                    'CourseAdds.published_course_id IN' => $this->CourseRegistrations->PublishedCourses->find()
                        ->where(['PublishedCourses.course_id IN' => $matchingCourses])
                        ->select(['PublishedCourses.id'])
                ])
                ->contain(['PublishedCourses'])
                ->order(['CourseAdds.id' => 'ASC'])
                ->toArray();
        }

        $registerAndAddCourseRegistration = [];

        if (!empty($registrationFreq)) {
            foreach ($registrationFreq as $value) {
                if (!empty($value->published_course->id) && !$this->CourseRegistrations->isCourseDropped($value->id)) {
                    $mIndex = count($registerAndAddFreq);
                    $raacr = $this->getApprovedGrade($value->id, 1);

                    if (!empty($raacr)) {
                        $registerAndAddCourseRegistration[$value->published_course->course_id]['register'][$value->id] = $raacr['grade'];
                    }

                    $registerAndAddFreq[$mIndex] = [
                        'id' => $value->id,
                        'type' => 'register',
                        'course_id' => $value->published_course->course_id,
                        'created' => $value->created
                    ];
                }
            }
        }

        if (!empty($addFreq)) {
            foreach ($addFreq as $value) {
                if (!empty($value->published_course->id)) {
                    $mIndex = count($registerAndAddFreq);
                    $raaca = $this->getApprovedGrade($value->id, 0);

                    if (!empty($raaca)) {
                        $registerAndAddCourseRegistration[$value->published_course->course_id]['add'][$value->id] = $raaca['grade'];
                    }

                    $registerAndAddFreq[$mIndex] = [
                        'id' => $value->id,
                        'type' => 'add',
                        'course_id' => $value->published_course->course_id,
                        'created' => $value->created
                    ];
                }
            }
        }

        usort($registerAndAddFreq, function ($a, $b) {
            return $a['created'] <=> $b['created'];
        });

        return $registerAndAddFreq;
    }

    public function getApprovedThesisGrade(int $studentId): array
    {
        $curr = $this->CourseRegistrations->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $studentCurriculumAttachments = TableRegistry::getTableLocator()->get('CurriculumAttachments')->find('list')
            ->where(['CurriculumAttachments.student_id' => $studentId])
            ->group(['CurriculumAttachments.student_id', 'CurriculumAttachments.curriculum_id'])
            ->select(['CurriculumAttachments.curriculum_id'])
            ->toArray();

        if (!empty($curr) && !empty($curr->curriculum_id)) {
            $curriculumsToLook = [$curr->curriculum_id];
            if (!empty($studentCurriculumAttachments)) {
                $curriculumsToLook = array_merge($curriculumsToLook, $studentCurriculumAttachments);
            }

            $thesisCourse = $this->CourseRegistrations->PublishedCourses->Courses->find('list')
                ->where(['Courses.thesis' => 1, 'Courses.curriculum_id IN' => $curriculumsToLook])
                ->select(['Courses.id'])
                ->toArray();

            $courseIdList = implode(', ', $thesisCourse);

            if (!empty($thesisCourse)) {
                $registration = $this->CourseRegistrations->find()
                    ->where([
                        'CourseRegistrations.student_id' => $studentId,
                        'CourseRegistrations.published_course_id IN' => $this->CourseRegistrations->PublishedCourses->find()
                            ->where(['PublishedCourses.course_id IN' => $thesisCourse])
                            ->select(['PublishedCourses.id'])
                    ])
                    ->contain(['PublishedCourses.Courses'])
                    ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.id' => 'DESC'])
                    ->first();

                if (empty($registration)) {
                    $thesisFromAdd = $this->CourseAdds->find()
                        ->where([
                            'CourseAdds.student_id' => $studentId,
                            'CourseAdds.registrar_confirmation' => 1
                        ])
                        ->contain([
                            'PublishedCourses.Courses' => [
                                'conditions' => [
                                    'OR' => [
                                        'Courses.id IN' => $thesisCourse,
                                        'Courses.thesis' => 1
                                    ]
                                ]
                            ]
                        ])
                        ->order(['CourseAdds.academic_year' => 'DESC', 'CourseAdds.semester' => 'DESC', 'CourseAdds.id' => 'DESC'])
                        ->first();
                }
            }
        }

        if (!empty($registration)) {
            return $this->getApprovedGrade($registration->id, 1);
        } elseif (!empty($thesisFromAdd)) {
            return $this->getApprovedGrade($thesisFromAdd->id, 0);
        }

        return [];
    }

    public function getApprovedThesisTitleAndGrade(int $studentId): array
    {
        $curr = $this->CourseRegistrations->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $studentCurriculumAttachments = TableRegistry::getTableLocator()->get('CurriculumAttachments')->find('list')
            ->where(['CurriculumAttachments.student_id' => $studentId])
            ->group(['CurriculumAttachments.student_id', 'CurriculumAttachments.curriculum_id'])
            ->select(['CurriculumAttachments.curriculum_id'])
            ->toArray();

        $thesisCourse = [];

        if (!empty($curr) && !empty($curr->curriculum_id)) {
            $curriculumsToLook = [$curr->curriculum_id];
            if (!empty($studentCurriculumAttachments)) {
                $curriculumsToLook = array_merge($curriculumsToLook, $studentCurriculumAttachments);
            }

            $thesisCourse = $this->CourseRegistrations->PublishedCourses->Courses->find('list')
                ->where(['Courses.thesis' => 1, 'Courses.curriculum_id IN' => $curriculumsToLook])
                ->select(['Courses.id'])
                ->toArray();

            $courseIdList = implode(', ', $thesisCourse);

            if (!empty($thesisCourse)) {
                $registration = $this->CourseRegistrations->find()
                    ->where([
                        'CourseRegistrations.student_id' => $studentId,
                        'CourseRegistrations.published_course_id IN' => $this->CourseRegistrations->PublishedCourses->find()
                            ->where(['PublishedCourses.course_id IN' => $thesisCourse])
                            ->select(['PublishedCourses.id'])
                    ])
                    ->contain(['PublishedCourses.Courses'])
                    ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.id' => 'DESC'])
                    ->first();

                if (empty($registration)) {
                    $thesisFromAdd = $this->CourseAdds->find()
                        ->where([
                            'CourseAdds.student_id' => $studentId,
                            'CourseAdds.registrar_confirmation' => 1
                        ])
                        ->contain([
                            'PublishedCourses.Courses' => [
                                'conditions' => [
                                    'OR' => [
                                        'Courses.id IN' => $thesisCourse,
                                        'Courses.thesis' => 1
                                    ]
                                ]
                            ]
                        ])
                        ->order(['CourseAdds.academic_year' => 'DESC', 'CourseAdds.semester' => 'DESC', 'CourseAdds.id' => 'DESC'])
                        ->first();
                }
            }
        }

        if (!empty($registration)) {
            $grade = $this->getApprovedGrade($registration->id, 1);
            if (!empty($grade) && !empty($thesisCourse)) {
                $graduationWork = TableRegistry::getTableLocator()->get('GraduationWorks')->find()
                    ->where([
                        'GraduationWorks.student_id' => $studentId,
                        'GraduationWorks.course_id IN' => $thesisCourse
                    ])
                    ->select(['id', 'type', 'title'])
                    ->order(['GraduationWorks.modified' => 'DESC', 'GraduationWorks.id' => 'DESC'])
                    ->first();

                if (!empty($graduationWork)) {
                    $graduationWork->title = trim(str_replace('  ', ' ', $graduationWork->title));
                    return array_merge($grade, ['GraduationWork' => $graduationWork->toArray()]);
                }
                return $grade;
            }
            return !empty($grade) ? $grade : [];
        } elseif (!empty($thesisFromAdd)) {
            $grade = $this->getApprovedGrade($thesisFromAdd->id, 0);
            if (!empty($grade) && !empty($thesisCourse)) {
                $graduationWork = TableRegistry::getTableLocator()->get('GraduationWorks')->find()
                    ->where([
                        'GraduationWorks.student_id' => $studentId,
                        'GraduationWorks.course_id IN' => $thesisCourse
                    ])
                    ->select(['id', 'type', 'title'])
                    ->order(['GraduationWorks.modified' => 'DESC', 'GraduationWorks.id' => 'DESC'])
                    ->first();

                if (!empty($graduationWork)) {
                    $graduationWork->title = trim(str_replace('  ', ' ', $graduationWork->title));
                    return array_merge($grade, ['GraduationWork' => $graduationWork->toArray()]);
                }
                return $grade;
            }
            return !empty($grade) ? $grade : [];
        }

        return [];
    }

    public function getApprovedExitExamGrade(int $studentId): array
    {
        $curr = $this->CourseRegistrations->Students->find()
            ->where(['Students.id' => $studentId])
            ->first();

        $studentCurriculumAttachments = TableRegistry::getTableLocator()->get('CurriculumAttachments')->find('list')
            ->where(['CurriculumAttachments.student_id' => $studentId])
            ->group(['CurriculumAttachments.student_id', 'CurriculumAttachments.curriculum_id'])
            ->select(['CurriculumAttachments.curriculum_id'])
            ->toArray();

        if (!empty($curr) && !empty($curr->curriculum_id)) {
            $curriculumsToLook = [$curr->curriculum_id];
            if (!empty($studentCurriculumAttachments)) {
                $curriculumsToLook = array_merge($curriculumsToLook, $studentCurriculumAttachments);
            }

            $exitExamCourse = $this->CourseRegistrations->PublishedCourses->Courses->find('list')
                ->where(['Courses.exit_exam' => 1, 'Courses.curriculum_id IN' => $curriculumsToLook])
                ->select(['Courses.id'])
                ->toArray();

            $courseIdList = implode(', ', $exitExamCourse);

            if (!empty($exitExamCourse)) {
                $registration = $this->CourseRegistrations->find()
                    ->where([
                        'CourseRegistrations.student_id' => $studentId,
                        'CourseRegistrations.published_course_id IN' => $this->CourseRegistrations->PublishedCourses->find()
                            ->where(['PublishedCourses.course_id IN' => $exitExamCourse])
                            ->select(['PublishedCourses.id'])
                    ])
                    ->contain(['PublishedCourses.Courses'])
                    ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.id' => 'DESC'])
                    ->first();

                if (empty($registration)) {
                    $courseAdd = $this->CourseAdds->find()
                        ->where([
                            'CourseAdds.student_id' => $studentId,
                            'CourseAdds.department_approval' => 1,
                            'CourseAdds.registrar_confirmation' => 1,
                            'CourseAdds.published_course_id IN' => $this->CourseRegistrations->PublishedCourses->find()
                                ->where(['PublishedCourses.course_id IN' => $exitExamCourse])
                                ->select(['PublishedCourses.id'])
                        ])
                        ->contain(['PublishedCourses.Courses'])
                        ->order(['CourseAdds.academic_year' => 'DESC', 'CourseAdds.semester' => 'DESC', 'CourseAdds.id' => 'DESC'])
                        ->first();
                }
            }
        }

        if (!empty($registration)) {
            $grade = $this->getApprovedGrade($registration->id, 1);
            return array_merge($grade, ['PublishedCourse' => $registration->published_course->toArray()]);
        } elseif (!empty($courseAdd)) {
            $grade = $this->getApprovedGrade($courseAdd->id, 0);
            return array_merge($grade, ['PublishedCourse' => $courseAdd->published_course->toArray()]);
        }

        return [];
    }

    public function getListOfFXGradeChangeForStudentChoice(?int $studentId = null, ?string $academicYear = null, ?string $semester = null, ?int $departmentId = null): array
    {
        $fxGrade = [];

        if (!empty($studentId)) {
            $fxGradeListCourseReg = $this->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.student_id' => $studentId,
                    'CourseRegistrations.id IN' => $this->find()
                        ->where(['ExamGrades.grade' => 'Fx', 'ExamGrades.registrar_approval' => 1])
                        ->select(['ExamGrades.course_registration_id'])
                ])
                ->contain(['PublishedCourses' => ['Courses', 'CourseInstructorAssignments'], 'Students', 'ExamGrades'])
                ->toArray();

            $fxGradeListCourseAdd = $this->CourseAdds->find()
                ->where([
                    'CourseAdds.student_id' => $studentId,
                    'CourseAdds.id IN' => $this->find()
                        ->where(['ExamGrades.grade' => 'Fx', 'ExamGrades.registrar_approval' => 1])
                        ->select(['ExamGrades.course_add_id'])
                ])
                ->contain(['PublishedCourses.Courses', 'Students', 'ExamGrades'])
                ->toArray();
        } elseif (!empty($academicYear) && !empty($semester) && !empty($departmentId)) {
            $fxGradeListCourseReg = $this->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.academic_year' => $academicYear,
                    'CourseRegistrations.semester' => $semester,
                    'PublishedCourses.department_id' => $departmentId,
                    'CourseRegistrations.id IN' => $this->find()
                        ->where(['ExamGrades.grade' => 'Fx', 'ExamGrades.registrar_approval' => 1])
                        ->select(['ExamGrades.course_registration_id'])
                ])
                ->contain(['PublishedCourses' => ['Courses', 'CourseInstructorAssignments'], 'Students', 'ExamGrades'])
                ->toArray();

            $fxGradeListCourseAdd = $this->CourseAdds->find()
                ->where([
                    'CourseAdds.semester' => $semester,
                    'PublishedCourses.department_id' => $departmentId,
                    'CourseAdds.id IN' => $this->find()
                        ->where(['ExamGrades.grade' => 'Fx', 'ExamGrades.registrar_approval' => 1])
                        ->select(['ExamGrades.course_add_id'])
                ])
                ->contain(['PublishedCourses.Courses', 'Students', 'ExamGrades'])
                ->toArray();
        }

        if (!empty($fxGradeListCourseReg)) {
            foreach ($fxGradeListCourseReg as $fv) {
                $grade = $this->getApprovedGrade($fv->id, 1);
                $latestGrade = $this->CourseRegistrations->getCourseRegistrationLatestGradeDetail($fv->id);

                if (!empty($latestGrade['LatestGradeDetail']['type']) && $latestGrade['LatestGradeDetail']['type'] === 'Change') {
                    continue;
                } elseif (!empty($grade) && in_array($grade['grade'], ['FX', 'Fx'])) {
                    $fv->student->applied_id = TableRegistry::getTableLocator()->get('FxResitRequests')->fxresetId($fv->id, 1);
                    $fv->student->fxgradesubmitted = false;
                    $fxGrade[] = $fv;
                }
            }
        }

        if (!empty($fxGradeListCourseAdd)) {
            foreach ($fxGradeListCourseAdd as $fv) {
                $grade = $this->getApprovedGrade($fv->id, 0);
                $latestGrade = $this->CourseAdds->getCourseAddLatestGradeDetail($fv->id);

                if (!empty($latestGrade['LatestGradeDetail']['type']) && $latestGrade['LatestGradeDetail']['type'] === 'Change') {
                    continue;
                } elseif (!empty($grade) && $grade['grade'] === 'Fx') {
                    $fv->student->applied_id = TableRegistry::getTableLocator()->get('FxResitRequests')->fxresetId($fv->id, 0);
                    $fv->student->fxgradesubmitted = false;
                    $fxGrade[] = $fv;
                }
            }
        }

        return $fxGrade;
    }

    public function getListOfNGGrade(string $academicYear, string $semester, int $departmentId, int $programId, int $programTypeId, ?array $gradeConverted = null, int $type = 0): array
    {
        $conditions = [
            'PublishedCourses.semester' => $semester,
            'PublishedCourses.academic_year' => $academicYear,
            'PublishedCourses.program_id' => $programId,
            'PublishedCourses.program_type_id' => $programTypeId
        ];

        if ($type == 1) {
            $conditions['PublishedCourses.college_id'] = $departmentId;
        } else {
            $conditions['PublishedCourses.department_id'] = $departmentId;
        }

        $publishedCourseLists = TableRegistry::getTableLocator()->get('PublishedCourses')->find()
            ->where($conditions)
            ->contain([
                'Courses',
                'Programs',
                'ProgramTypes',
                'Departments.Colleges',
                'Colleges',
                'CourseAdds' => [
                    'Students',
                    'ExamResults' => [
                        'conditions' => ['ExamResults.course_add' => 0],
                        'order' => ['ExamResults.result' => 'ASC'],
                        'limit' => 1
                    ]
                ],
                'CourseRegistrations' => [
                    'Students',
                    'ExamResults' => [
                        'order' => ['ExamResults.result' => 'ASC'],
                        'limit' => 1
                    ]
                ],
                'MakeupExams' => [
                    'Students',
                    'ExamResults' => ['limit' => 1]
                ]
            ])
            ->toArray();

        $autoConvertedGradeLists = [];
        $applicableGrades = ['I', 'DO', 'W', 'F'];

        if (!empty($publishedCourseLists)) {
            foreach ($publishedCourseLists as $pv) {
                if (!empty($pv->course_registrations)) {
                    foreach ($pv->course_registrations as $crv) {
                        if ($crv->student->graduated == 0) {
                            $autoChange = $this->find()
                                ->where([
                                    'ExamGrades.course_registration_id' => $crv->id,
                                    'ExamGrades.grade' => 'NG'
                                ])
                                ->contain([
                                    'ExamGradeChanges' => [
                                        'conditions' => [
                                            'ExamGradeChanges.grade IN' => !empty($gradeConverted) ? $gradeConverted : $applicableGrades,
                                            'OR' => [
                                                'ExamGradeChanges.manual_ng_conversion' => 1,
                                                'ExamGradeChanges.auto_ng_conversion' => 1
                                            ]
                                        ],
                                        'order' => ['ExamGradeChanges.id' => 'DESC']
                                    ]
                                ])
                                ->first();

                            if (!empty($autoChange->exam_grade_changes)) {
                                $crv->student->haveAssesmentData = !empty($crv->exam_results);
                                $crv->student->p_crs_id = $pv->id;
                                $autoChange->course = $pv->course;
                                $autoChange->student = $crv->student;

                                $key = !empty($pv->department->name)
                                    ? "{$pv->department->college->name}~{$pv->department->name}~{$pv->program->name}~{$pv->program_type->name}"
                                    : "{$pv->college->name}~" . ($crv->student->program_id == 1 ? 'Remedial Program' : 'Freshman') . "~{$pv->program->name}~{$pv->program_type->name}";

                                $autoConvertedGradeLists[$key][] = $autoChange;
                            }
                        }
                    }
                }

                if (!empty($pv->course_adds)) {
                    foreach ($pv->course_adds as $cadv) {
                        if ($cadv->student->graduated == 0) {
                            $autoChange = $this->find()
                                ->where([
                                    'ExamGrades.course_add_id' => $cadv->id,
                                    'ExamGrades.grade' => 'NG'
                                ])
                                ->contain([
                                    'ExamGradeChanges' => [
                                        'conditions' => [
                                            'ExamGradeChanges.grade IN' => !empty($gradeConverted) ? $gradeConverted : $applicableGrades,
                                            'OR' => [
                                                'ExamGradeChanges.manual_ng_conversion' => 1,
                                                'ExamGradeChanges.auto_ng_conversion' => 1
                                            ]
                                        ],
                                        'order' => ['ExamGradeChanges.id' => 'DESC']
                                    ]
                                ])
                                ->first();

                            if (!empty($autoChange->exam_grade_changes)) {
                                $cadv->student->haveAssesmentData = !empty($cadv->exam_results);
                                $cadv->student->p_crs_id = $pv->id;
                                $autoChange->course = $pv->course;
                                $autoChange->student = $cadv->student;

                                $key = !empty($pv->department->name)
                                    ? "{$pv->department->college->name}~{$pv->department->name}~{$pv->program->name}~{$pv->program_type->name}"
                                    : "{$pv->college->name}~Freshman~{$pv->program->name}~{$pv->program_type->name}";

                                $autoConvertedGradeLists[$key][] = $autoChange;
                            }
                        }
                    }
                }

                if (!empty($pv->makeup_exams)) {
                    foreach ($pv->makeup_exams as $mkpv) {
                        if ($mkpv->student->graduated == 0) {
                            $autoChange = $this->find()
                                ->where([
                                    'ExamGrades.makeup_exam_id' => $mkpv->id,
                                    'ExamGrades.grade' => 'NG'
                                ])
                                ->contain([
                                    'ExamGradeChanges' => [
                                        'conditions' => [
                                            'ExamGradeChanges.grade IN' => !empty($gradeConverted) ? $gradeConverted : $applicableGrades,
                                            'OR' => [
                                                'ExamGradeChanges.manual_ng_conversion' => 1,
                                                'ExamGradeChanges.auto_ng_conversion' => 1
                                            ]
                                        ],
                                        'order' => ['ExamGradeChanges.id' => 'DESC']
                                    ]
                                ])
                                ->first();

                            if (!empty($autoChange->exam_grade_changes)) {
                                $mkpv->student->haveAssesmentData = !empty($mkpv->exam_results);
                                $mkpv->student->p_crs_id = $pv->id;
                                $autoChange->course = $pv->course;
                                $autoChange->student = $mkpv->student;

                                $key = !empty($pv->department->name)
                                    ? "{$pv->department->college->name}~{$pv->department->name}~{$pv->program->name}~{$pv->program_type->name}"
                                    : "{$pv->college->name}~Freshman~{$pv->program->name}~{$pv->program_type->name}";

                                $autoConvertedGradeLists[$key][] = $autoChange;
                            }
                        }
                    }
                }
            }
        }

        return $autoConvertedGradeLists;
    }

    public function isGradeSubmittedForPublishedCourse($publishedCourseIds): int
    {
        $publishedCoursesStudentRegisteredScoreGrade = 0;

        $gradeSubmittedRegisteredCourses = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.published_course_id' => is_array($publishedCourseIds) ? $publishedCourseIds : [$publishedCourseIds]])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        if (!empty($gradeSubmittedRegisteredCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_registration_id IN' => $gradeSubmittedRegisteredCourses])
                ->count();
            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return $publishedCoursesStudentRegisteredScoreGrade;
            }
        }

        $gradeSubmittedAddCourses = $this->CourseAdds->find('list')
            ->where([
                'CourseAdds.published_course_id' => is_array($publishedCourseIds) ? $publishedCourseIds : [$publishedCourseIds],
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($gradeSubmittedAddCourses)) {
            $publishedCoursesStudentRegisteredScoreGrade = $this->find()
                ->where(['ExamGrades.course_add_id IN' => $gradeSubmittedAddCourses])
                ->count();
            if ($publishedCoursesStudentRegisteredScoreGrade > 0) {
                return $publishedCoursesStudentRegisteredScoreGrade;
            }
        }

        return $publishedCoursesStudentRegisteredScoreGrade;
    }

    public function getMasterSheetRemedial(?int $sectionId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $studentsAndGrades = [];
        $registeredCourses = [];

        $studentsInSection = $this->CourseRegistrations->Students->Sections->StudentsSections->find()
            ->where(['StudentsSections.section_id' => $sectionId])
            ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
            ->toArray();

        $studentsInSectionIds = $this->CourseRegistrations->Students->Sections->StudentsSections->find('list')
            ->where(['StudentsSections.section_id' => $sectionId])
            ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
            ->select(['StudentsSections.student_id'])
            ->toArray();

        $studentRegisteredCourseForSection = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.section_id' => $sectionId])
            ->select(['CourseRegistrations.student_id', 'CourseRegistrations.section_id'])
            ->toArray();

        $count = count($studentsInSection);

        if (!empty($studentRegisteredCourseForSection)) {
            foreach ($studentRegisteredCourseForSection as $stuId => $sectId) {
                if (!in_array($stuId, $studentsInSectionIds) && $sectId == $sectionId) {
                    $studentsInSection[$count] = [
                        'StudentsSection' => [
                            'student_id' => $stuId,
                            'section_id' => $sectId
                        ]
                    ];
                    $count++;
                }
            }
        }

        if (!empty($studentsInSection)) {
            foreach ($studentsInSection as $sectionStudent) {
                $studentDetail = $this->CourseAdds->Students->find()
                    ->where(['Students.id' => $sectionStudent['StudentsSection']['student_id']])
                    ->first();

                $programTypeId = $this->CourseAdds->Students->ProgramTypeTransfers->getStudentProgramType(
                    $studentDetail->id,
                    $academicYear,
                    $semester
                );
                $programTypeId = $this->CourseAdds->Students->ProgramTypes->getParentProgramType($programTypeId);
                $pattern = $this->CourseAdds->Students->ProgramTypes->StudentStatusPatterns->getProgramTypePattern(
                    $studentDetail->program_id,
                    $programTypeId,
                    $academicYear
                );

                $ayAndSList = [];

                if ($pattern <= 1) {
                    $ayAndSList[] = ['academic_year' => $academicYear, 'semester' => $semester];
                } else {
                    $statusPrepared = $this->CourseAdds->Students->StudentExamStatuses->find()
                        ->where([
                            'StudentExamStatuses.student_id' => $studentDetail->id,
                            'StudentExamStatuses.academic_year' => $academicYear,
                            'StudentExamStatuses.semester' => $semester
                        ])
                        ->count();

                    if ($statusPrepared == 0) {
                        $ayAndSListDraft = $this->CourseAdds->Students->StudentExamStatuses->getAcademicYearAndSemesterListToGenerateStatus(
                            $studentDetail->id,
                            $academicYear,
                            $semester
                        );
                        if (count($ayAndSListDraft) > $pattern) {
                            $ayAndSList = array_slice($ayAndSListDraft, 0, $pattern);
                        } else {
                            $ayAndSList = $ayAndSListDraft;
                        }
                    } else {
                        $ayAndSList = $this->CourseAdds->Students->StudentExamStatuses->getAcademicYearAndSemesterListToUpdateStatus(
                            $studentDetail->id,
                            $academicYear,
                            $semester
                        );
                    }
                }

                $options = [
                    'conditions' => ['CourseRegistrations.student_id' => $studentDetail->id],
                    'contain' => [
                        'PublishedCourses' => [
                            'Courses',
                            'ExamTypes' => ['order' => ['ExamTypes.order' => 'ASC']]
                        ]
                    ]
                ];

                if (!empty($ayAndSList)) {
                    foreach ($ayAndSList as $ayS) {
                        $options['conditions']['OR'][] = [
                            'CourseRegistrations.academic_year' => $ayS['academic_year'],
                            'CourseRegistrations.semester' => $ayS['semester']
                        ];
                    }
                }

                $studentCourseRegistrations = $this->CourseRegistrations->find('all', $options)->toArray();

                if (!empty($studentCourseRegistrations)) {
                    foreach ($studentCourseRegistrations as $studentCourseRegistration) {
                        if ($studentCourseRegistration->published_course->drop == 0) {
                            foreach ($registeredCourses as $registeredCourse) {
                                if ($registeredCourse['id'] == $studentCourseRegistration->published_course->course->id) {
                                    continue 2;
                                }
                            }

                            $rIndex = count($registeredCourses);
                            $registeredCourses[$rIndex] = [
                                'id' => $studentCourseRegistration->published_course->course->id,
                                'course_title' => $studentCourseRegistration->published_course->course->course_title,
                                'course_id' => $studentCourseRegistration->published_course->course->id,
                                'course_code' => $studentCourseRegistration->published_course->course->course_code,
                                'credit' => $studentCourseRegistration->published_course->course->credit,
                                'published_course_id' => $studentCourseRegistration->published_course_id,
                                'exam_type' => $studentCourseRegistration->published_course->exam_types
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($studentsInSection)) {
            foreach ($studentsInSection as $value) {
                $index = count($studentsAndGrades);

                $studentDetail = $this->CourseRegistrations->Students->find()
                    ->where(['Students.id' => $value['StudentsSection']['student_id']])
                    ->first();

                $studentsAndGrades[$index] = [
                    'student_id' => $value['StudentsSection']['student_id'],
                    'full_name' => $studentDetail->first_name . ' ' . $studentDetail->middle_name . ' ' . $studentDetail->last_name,
                    'studentnumber' => $studentDetail->studentnumber,
                    'gender' => $studentDetail->gender
                ];

                if (!empty($registeredCourses)) {
                    foreach ($registeredCourses as $registeredCourse) {
                        $registrationId = $this->CourseRegistrations->find()
                            ->where([
                                'CourseRegistrations.published_course_id' => $registeredCourse['published_course_id'],
                                'CourseRegistrations.student_id' => $value['StudentsSection']['student_id']
                            ])
                            ->select(['CourseRegistrations.id'])
                            ->first();

                        if (!empty($registrationId)) {
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']] = $this->getApprovedGrade($registrationId->id, 1);
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['Assesment'] = TableRegistry::getTableLocator()->get('ExamTypes')->getAssessementDetailTypeRemedialMasterSheet($registrationId->id, 1);
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['registration_id'] = $registrationId->id;
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['published_c_id'] = $registeredCourse['published_course_id'];
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['credit'] = $registeredCourse['credit'];
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['registered'] = true;
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']]['droped'] = $this->CourseRegistrations->isCourseDropped($registrationId->id);
                        } else {
                            $studentsAndGrades[$index]['courses']['r-' . $registeredCourse['id']] = [
                                'Assesment' => [],
                                'registered' => false
                            ];
                        }
                    }
                }
            }
        }

        return [
            'registered_courses' => $registeredCourses,
            'students_and_grades' => $studentsAndGrades
        ];
    }
}
