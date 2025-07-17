<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PublishedCoursesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('published_courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id'
        ]);
        $this->belongsTo('GradeScales', [
            'foreignKey' => 'grade_scale_id'
        ]);
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id'
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id'
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id'
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id'
        ]);
        $this->belongsTo('GivenByDepartments', [
            'className' => 'Departments',
            'foreignKey' => 'given_by_department_id'
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id'
        ]);
        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id'
        ]);

        $this->hasMany('CourseSchedules', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('UnschedulePublishedCourses', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('ExamSchedules', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('GradeScalePublishedCourses', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('MakeupExams', [
            'foreignKey' => 'published_course_id'
        ]);
        $this->hasMany('MergedSectionsCourses', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('MergedSectionsExams', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('SectionSplitForPublishedCourses', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('CourseInstructorAssignments', [
            'foreignKey' => 'published_course_id',
            'dependent' => true
        ]);
        $this->hasMany('CourseRegistrations', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('CourseAdds', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('ClassPeriodCourseConstraints', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('ClassRoomCourseConstraints', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('CourseExamConstraints', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('ExamRoomCourseConstraints', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('Attendances', [
            'foreignKey' => 'published_course_id'
        ]);
        $this->hasMany('ExamTypes', [
            'foreignKey' => 'published_course_id'
        ]);
        $this->hasMany('FxResitRequests', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
        $this->hasMany('ResultEntryAssignments', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);

        $this->hasOne('CourseExamGapConstraints', [
            'foreignKey' => 'published_course_id',
            'dependent' => false
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('year_level_id')
            ->multiple('semester')
            ->numeric('course_id')
            ->numeric('program_type_id')
            ->numeric('program_id')
            ->numeric('department_id')
            ->numeric('section_id');

        return $validator;
    }

    public function canItBeDeleted($id = null)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');

        if ($courseRegistrationsTable->find()->where(['published_course_id' => $id])->count() > 0) {
            return false;
        }

        if ($courseAddsTable->find()->where(['published_course_id' => $id])->count() > 0) {
            return false;
        } elseif ($makeupExamsTable->find()->where(['published_course_id' => $id])->count() > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getSectionofPublishedCourses($data, $department_id = null, $publishedcourses = null, $college_id = null)
    {
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');

        if ($college_id) {
            $sections = $sectionsTable->find('list')
                ->where([
                    'college_id' => $college_id,
                    'department_id IS NULL',
                    'program_id' => $data['PublishedCourse']['program_id'],
                    'program_type_id' => $data['PublishedCourse']['program_type_id'],
                    'archive' => 0
                ])
                ->toArray();
        } else {
            $sections = $sectionsTable->find('list')
                ->where([
                    'department_id' => $department_id,
                    'year_level_id' => $data['PublishedCourse']['year_level_id'],
                    'program_id' => $data['PublishedCourse']['program_id'],
                    'program_type_id' => $data['PublishedCourse']['program_type_id'],
                    'archive' => 0
                ])
                ->toArray();
        }

        if (!empty($sections) && !empty($publishedcourses)) {
            $section_organized_published_courses = [];
            foreach ($sections as $section_id => $section_name) {
                foreach ($publishedcourses as $kkk => &$vvv) {
                    if ($vvv['PublishedCourse']['section_id'] == $section_id) {
                        if ($examGradesTable->isGradeSubmitted($vvv['PublishedCourse']['id']) > 0) {
                            $vvv['PublishedCourse']['unpublish_readOnly'] = true;
                            $vvv['PublishedCourse']['have_course_registration_or_add'] = true;
                        } else {
                            $vvv['PublishedCourse']['unpublish_readOnly'] = false;
                            $vvv['PublishedCourse']['have_course_registration_or_add'] = false;

                            if ($courseRegistrationsTable->find()->where(['published_course_id' => $vvv['PublishedCourse']['id']])->count() > 0) {
                                $vvv['PublishedCourse']['have_course_registration_or_add'] = true;
                            }

                            if ($courseAddsTable->find()->where(['published_course_id' => $vvv['PublishedCourse']['id']])->count() > 0) {
                                $vvv['PublishedCourse']['have_course_registration_or_add'] = true;
                            }
                        }

                        $section_organized_published_courses[$section_name][] = $publishedcourses[$kkk];
                    }
                }
            }
            return $section_organized_published_courses;
        }
        return null;
    }

    public function getSectionOrganizedPublishedCourses($data = null, $department_id = null, $publishedcourses = null, $college_id = null)
    {
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        if ($college_id) {
            $sections = $sectionsTable->find('list')
                ->where([
                    'college_id' => $college_id,
                    'department_id IS NULL',
                    'program_id' => PROGRAM_UNDEGRADUATE,
                    'program_type_id' => PROGRAM_TYPE_REGULAR,
                    'archive' => 0
                ])
                ->toArray();
        } else {
            $sections = $sectionsTable->find('list')
                ->where([
                    'department_id' => $department_id,
                    'program_id' => $data['PublishedCourse']['program_id'],
                    'archive' => 0
                ])
                ->toArray();
        }

        if (!empty($sections) && !empty($publishedcourses)) {
            $section_organized_published_courses = [];
            foreach ($sections as $section_id => $section_name) {
                foreach ($publishedcourses as $kkk => &$vvv) {
                    if ($vvv['PublishedCourse']['section_id'] == $section_id) {
                        if ($examGradesTable->isGradeSubmitted($vvv['PublishedCourse']['id']) > 0) {
                            $vvv['PublishedCourse']['scale_readOnly'] = true;
                            $vvv['PublishedCourse']['unpublish_readOnly'] = true;
                        } else {
                            $vvv['PublishedCourse']['scale_readOnly'] = false;
                            $vvv['PublishedCourse']['unpublish_readOnly'] = false;
                        }
                        $section_organized_published_courses[$section_name . "(" . $vvv['Section']['ProgramType']['name'] . ")"][] = $publishedcourses[$kkk];
                    }
                }
            }
            return $section_organized_published_courses;
        }
        return null;
    }

    public function getSectionOrganizedPublishedCoursesM($publishedcourses = null)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $section_organized_published_courses = [];
        foreach ($publishedcourses as $kkk => &$vvv) {
            if ($examGradesTable->isGradeSubmitted($vvv['PublishedCourse']['id']) > 0) {
                $vvv['PublishedCourse']['scale_readOnly'] = true;
                $vvv['PublishedCourse']['unpublish_readOnly'] = true;
            } else {
                $vvv['PublishedCourse']['scale_readOnly'] = false;
                $vvv['PublishedCourse']['unpublish_readOnly'] = false;
            }

            if ($vvv['PublishedCourse']['year_level_id'] == 0 || empty($vvv['PublishedCourse']['year_level_id'])) {
                $section_organized_published_courses[$vvv['Section']['College']['name'] . ' ' . $vvv['Section']['ProgramType']['name'] . ' ' . 'Pre  Section ' . $vvv['Section']['name']][] = $publishedcourses[$kkk];
            } else {
                $section_organized_published_courses[$vvv['Section']['Department']['name'] . ' ' . $vvv['Section']['ProgramType']['name'] . ' ' . $vvv['Section']['YearLevel']['name'] . '  Year  Section ' . $vvv['Section']['name']][] = $publishedcourses[$kkk];
            }
        }
        return $section_organized_published_courses;
    }

    public function getInstructorDetailGivingPublishedCourse($published_course_id = null)
    {
        $instructor_detail = [];

        if (isset($published_course_id) && !empty($published_course_id)) {
            $courseInstructorAssignmentsTable = TableRegistry::getTableLocator()->get('CourseInstructorAssignments');

            $instructor_detail = $courseInstructorAssignmentsTable->find()
                ->select(['published_course_id'])
                ->where(['published_course_id' => $published_course_id])
                ->contain([
                    'Staffs' => [
                        'fields' => ['first_name', 'middle_name', 'last_name'],
                        'Titles' => ['fields' => ['id', 'title']],
                        'Positions' => ['fields' => ['id', 'position']],
                        'Users' => ['fields' => ['id', 'username', 'email', 'active', 'email_verified']]
                    ]
                ])
                ->first();
        }

        return $instructor_detail;
    }

    public function getStudentsTakingPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = [];
        $student_adds = [];
        $students = [];
        $students_makeup_exam = [];

        if ($published_course_id != null) {
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
            $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
            $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
            $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');
            $usersTable = TableRegistry::getTableLocator()->get('Users');
            $resultEntryAssignmentsTable = TableRegistry::getTableLocator()->get('ResultEntryAssignments');

            $students = $courseRegistrationsTable->find()
                ->select(['id'])
                ->where(['published_course_id' => $published_course_id])
                ->contain([
                    'PublishedCourses' => [
                        'fields' => ['section_id', 'college_id', 'department_id', 'given_by_department_id', 'add', 'drop', 'grade_scale_id']
                    ],
                    'ExamGrades' => [
                        'queryBuilder' => function ($query) {
                            return $query->order(['id' => 'DESC']);
                        }
                    ],
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'queryBuilder' => function ($query) {
                            return $query->order(['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC']);
                        }
                    ],
                    'CourseDrops',
                    'ExamResults' => [
                        'queryBuilder' => function ($query) {
                            return $query
                                ->where(['course_add' => 0])
                                ->contain(['ExamTypes']);
                        }
                    ],
                    'ResultEntryAssignments'
                ])
                ->toArray();

            if (!empty($students)) {
                foreach ($students as $key => &$student) {
                    if ($courseRegistrationsTable->isCourseDropped($student->id)) {
                        unset($students[$key]);
                    } else {
                        $student->exam_grade_history = $courseRegistrationsTable->getCourseRegistrationGradeHistory($student->id);
                        $student->latest_grade_detail = $courseRegistrationsTable->getCourseRegistrationLatestGradeDetail($student->id);
                        $student->any_exam_grade_is_on_process = $courseRegistrationsTable->isAnyGradeOnProcess($student->id);
                        $student->freshman_program = (!is_null($student->published_course->college_id) ? true : false);

                        if (!empty($student->exam_grades)) {
                            foreach ($student->exam_grades as $eg_key => $exam_grade) {
                                $student->exam_grades[$eg_key]->department_approved_by_name = $usersTable->find()
                                    ->select(['first_name','middle_name', 'last_name'])
                                    ->where(['id' => $exam_grade->department_approved_by])
                                    ->enableHydration(true)
                                    ->first()->full_name ?? 'N/A';
                                $student->exam_grades[$eg_key]->registrar_approved_by_name = $usersTable->find()
                                    ->select(['first_name','middle_name', 'last_name'])
                                    ->where(['id' => $exam_grade->registrar_approved_by])
                                    ->enableHydration(true)
                                    ->first()->full_name ?? 'N/A';
                            }
                        }
                    }
                }
            }


            $studentsSectionsTable->belongsTo('Students', [
                'foreignKey' => 'student_id'
            ]);

            $student_all_adds = $courseAddsTable->find()
                ->where([
                    'published_course_id' => $published_course_id,
                    'department_approval' => 1,
                    'registrar_confirmation' => 1
                ])
                ->contain([
                    'PublishedCourses',
                    'ExamGrades' => [
                        'queryBuilder' => function ($query) {
                            return $query->order(['id' => 'DESC']);
                        }
                    ],
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'queryBuilder' => function ($query) {
                            return $query->order(['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC']);
                        },
                        'StudentsSections' => [
                            'queryBuilder' => function ($query) {
                                return $query->where(['archive' => 0]);
                            }
                        ]
                    ],
                    'ExamResults' => [
                        'queryBuilder' => function ($query) {
                            return $query->where(['course_add' => 1]);
                        }
                    ]
                ])
                ->toArray();

            $section_and_course_detail = $this->find()
                ->where(['PublishedCourses.id' => $published_course_id])
                ->contain(['Sections', 'Courses'])
                ->first();

            $section_detail = $section_and_course_detail->section;
            $course_detail = $section_and_course_detail->course;

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    if (($student_all_add->department_approval == 1 && $student_all_add->registrar_confirmation == 1) || $student_all_add->published_course->add == 1) {
                        if (!empty($student_all_add->exam_grades)) {
                            foreach ($student_all_add->exam_grades as $eg_key => $exam_grade) {
                                $exam_grade->department_approved_by_name = $usersTable->find()
                                    ->select(['first_name', 'last_name'])
                                    ->where(['id' => $exam_grade->department_approved_by])
                                    ->enableHydration(true)
                                    ->first()->full_name ?? 'N/A';
                                $exam_grade->registrar_approved_by_name = $usersTable->find()
                                    ->select(['first_name', 'last_name'])
                                    ->where(['id' => $exam_grade->registrar_approved_by])
                                    ->enableHydration(true)
                                    ->first()->full_name ?? 'N/A';
                            }
                        }

                        $student_all_add->exam_grade_history = $courseAddsTable->getCourseAddGradeHistory($student_all_add->id);
                        $student_all_add->latest_grade_detail = $courseAddsTable->getCourseAddLatestGradeDetail($student_all_add->id);
                        $student_all_add->any_exam_grade_is_on_process = $courseAddsTable->isAnyGradeOnProcess($student_all_add->id);
                        $student_all_add->freshman_program = (!is_null($student_all_add->published_course->college_id) ? true : false);

                        if (isset($student_all_add->student->students_sections[0]->section_id) && strcasecmp($student_all_add->student->students_sections[0]->section_id, $section_detail->id) == 0) {
                            $students[] = $student_all_add;
                        } else {
                            $student_adds[] = $student_all_add;
                        }
                    } else {
                        unset($student_all_adds[$key]);
                    }
                }
            }

            $students_makeup_exam = $makeupExamsTable->find()
                ->where(['MakeupExams.published_course_id' => $published_course_id])
                ->contain([
                    'CourseRegistrations' => [
                        'ExamGrades' => ['sort' => ['id' => 'DESC']]
                    ],
                    'CourseAdds' => [
                        'ExamGrades' => ['sort' => ['id' => 'DESC']]
                    ],
                    'ExamGradeChanges' => [
                        'ExamGrades' => ['sort' => ['id' => 'DESC']],
                        'sort' => ['id' => 'DESC']
                    ],
                    'PublishedCourses',
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'sort' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC']
                    ]
                ])
                ->toArray();

            if (!empty($students_makeup_exam)) {
                foreach ($students_makeup_exam as $key => &$student_makeup_exam) {
                    if ($student_makeup_exam->course_registration_id != null) {
                        $student_makeup_exam->exam_grade_history = $courseRegistrationsTable->getCourseRegistrationGradeHistory($student_makeup_exam->course_registration_id);
                        $student_makeup_exam->latest_grade_detail = $courseRegistrationsTable->getCourseRegistrationLatestGradeDetail($student_makeup_exam->course_registration_id);
                        $student_makeup_exam->any_exam_grade_is_on_process = $courseRegistrationsTable->isAnyGradeOnProcess($student_makeup_exam->course_registration_id);
                        $student_makeup_exam->freshman_program = (!is_null($student_makeup_exam->published_course->college_id) ? true : false);

                        $student_makeup_exam->exam_result_history = $examResultsTable->find()
                            ->where(['course_registration_id' => $student_makeup_exam->course_registration_id])
                            ->toArray();
                    } else {
                        $student_makeup_exam->exam_grade_history = $courseAddsTable->getCourseAddGradeHistory($student_makeup_exam->course_add_id);
                        $student_makeup_exam->latest_grade_detail = $courseAddsTable->getCourseAddLatestGradeDetail($student_makeup_exam->course_add_id);
                        $student_makeup_exam->any_exam_grade_is_on_process = $courseAddsTable->isAnyGradeOnProcess($student_makeup_exam->course_add_id);
                        $student_makeup_exam->freshman_program = (!is_null($student_makeup_exam->published_course->college_id) ? true : false);

                        $student_makeup_exam->exam_result_history = $examResultsTable->find()
                            ->where(['course_add_id' => $student_makeup_exam->course_add_id])
                            ->toArray();
                    }

                    if (!empty($student_makeup_exam->course_registration) && $student_makeup_exam->course_registration->id != "") {
                        $student_makeup_exam->exam_grades = $student_makeup_exam->course_registration->exam_grades;
                    } else {
                        $student_makeup_exam->exam_grades = $student_makeup_exam->course_add->exam_grades ?? [];
                    }

                    if (!empty($student_makeup_exam->exam_grades)) {
                        foreach ($student_makeup_exam->exam_grades as $eg_key => $exam_grade) {
                            $exam_grade->department_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->department_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                            $exam_grade->registrar_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->registrar_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                        }
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;
        $student_course_register_and_adds['makeup'] = $students_makeup_exam;

        return $student_course_register_and_adds;
    }

    public function getStudentsTakingFxExamPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = [];
        $student_adds = [];
        $students = [];
        $students_makeup_exam = [];

        if ($published_course_id != null) {
            $fxResitRequestsTable = TableRegistry::getTableLocator()->get('FxResitRequests');
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
            $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');
            $usersTable = TableRegistry::getTableLocator()->get('Users');

            $fx_applied_student_lists = $fxResitRequestsTable->find('list')
                ->where(['published_course_id' => $published_course_id])
                ->select(['student_id', 'student_id'])
                ->toArray();

            $students = $courseRegistrationsTable->find()
                ->select(['id'])
                ->where([
                    'published_course_id' => $published_course_id,
                    'student_id IN' => $fx_applied_student_lists
                ])
                ->contain([
                    'PublishedCourses' => ['fields' => ['college_id']],
                    'ExamGrades' => ['sort' => ['id' => 'DESC']],
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'sort' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC']
                    ],
                    'CourseDrops',
                    'ExamResults' => [
                        'where' => ['course_add' => 0],
                        'ExamTypes'
                    ]
                ])
                ->toArray();

            if (!empty($students)) {
                foreach ($students as $key => &$student) {
                    $student->exam_grade_history = $courseRegistrationsTable->getCourseRegistrationGradeHistory($student->id);
                    $student->latest_grade_detail = $courseRegistrationsTable->getCourseRegistrationLatestGradeDetail($student->id);
                    $student->any_exam_grade_is_on_process = $courseRegistrationsTable->isAnyGradeOnProcess($student->id);
                    $student->freshman_program = (!is_null($student->published_course->college_id) ? true : false);

                    if (!empty($student->exam_grades)) {
                        foreach ($student->exam_grades as $eg_key => $exam_grade) {
                            $exam_grade->department_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->department_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                            $exam_grade->registrar_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->registrar_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                        }
                    }
                }
            }

            $student_all_adds = $courseAddsTable->find()
                ->where([
                    'published_course_id' => $published_course_id,
                    'student_id IN' => $fx_applied_student_lists
                ])
                ->contain([
                    'PublishedCourses',
                    'ExamGrades' => ['sort' => ['id' => 'DESC']],
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'sort' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC'],
                        'StudentsSections' => ['where' => ['archive' => 0]]
                    ],
                    'ExamResults' => ['where' => ['course_add' => 1]]
                ])
                ->toArray();

            $section_and_course_detail = $this->find()
                ->where(['id' => $published_course_id])
                ->contain(['Sections', 'Courses'])
                ->first();

            $section_detail = $section_and_course_detail->section;
            $course_detail = $section_and_course_detail->course;

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    if (!empty($student_all_add->exam_grades)) {
                        foreach ($student_all_add->exam_grades as $eg_key => $exam_grade) {
                            $exam_grade->department_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->department_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                            $exam_grade->registrar_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->registrar_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                        }
                    }

                    $student_all_add->exam_grade_history = $courseAddsTable->getCourseAddGradeHistory($student_all_add->id);
                    $student_all_add->latest_grade_detail = $courseAddsTable->getCourseAddLatestGradeDetail($student_all_add->id);
                    $student_all_add->any_exam_grade_is_on_process = $courseAddsTable->isAnyGradeOnProcess($student_all_add->id);
                    $student_all_add->freshman_program = (!is_null($student_all_add->published_course->college_id) ? true : false);

                    if (isset($student_all_add->student->students_sections[0]->section_id) && strcasecmp($student_all_add->student->students_sections[0]->section_id, $section_detail->id) == 0) {
                        $students[] = $student_all_add;
                    } else {
                        $student_adds[] = $student_all_add;
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;

        return $student_course_register_and_adds;
    }

    public function getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = [];
        $student_adds = [];
        $students = [];
        $students_makeup_exam = [];

        if ($published_course_id != null) {
            $resultEntryAssignmentsTable = TableRegistry::getTableLocator()->get('ResultEntryAssignments');
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $examResultsTable = TableRegistry::getTableLocator()->get('ExamResults');
            $usersTable = TableRegistry::getTableLocator()->get('Users');

            $students_makeup_exam = $resultEntryAssignmentsTable->find()
                ->where(['published_course_id' => $published_course_id])
                ->contain([
                    'CourseRegistrations' => [
                        'ExamGrades' => ['sort' => ['id' => 'DESC']]
                    ],
                    'CourseAdds' => [
                        'ExamGrades' => ['sort' => ['id' => 'DESC']]
                    ],
                    'PublishedCourses',
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'sort' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC']
                    ]
                ])
                ->toArray();

            if (!empty($students_makeup_exam)) {
                foreach ($students_makeup_exam as $key => &$student_makeup_exam) {
                    if ($student_makeup_exam->course_registration_id != null) {
                        $student_makeup_exam->exam_grade_history = $courseRegistrationsTable->getCourseRegistrationGradeHistory($student_makeup_exam->course_registration_id);
                        $student_makeup_exam->latest_grade_detail = $courseRegistrationsTable->getCourseRegistrationLatestGradeDetail($student_makeup_exam->course_registration_id);
                        $student_makeup_exam->any_exam_grade_is_on_process = $courseRegistrationsTable->isAnyGradeOnProcess($student_makeup_exam->course_registration_id);
                        $student_makeup_exam->freshman_program = (!is_null($student_makeup_exam->published_course->college_id) ? true : false);
                    } else {
                        $student_makeup_exam->exam_grade_history = $courseAddsTable->getCourseAddGradeHistory($student_makeup_exam->course_add_id);
                        $student_makeup_exam->latest_grade_detail = $courseAddsTable->getCourseAddLatestGradeDetail($student_makeup_exam->course_add_id);
                        $student_makeup_exam->any_exam_grade_is_on_process = $courseAddsTable->isAnyGradeOnProcess($student_makeup_exam->course_add_id);
                        $student_makeup_exam->freshman_program = (!is_null($student_makeup_exam->published_course->college_id) ? true : false);
                        $student_makeup_exam->exam_result_history = $examResultsTable->find()
                            ->where(['course_add_id' => $student_makeup_exam->course_add_id])
                            ->toArray();
                    }

                    if (!empty($student_makeup_exam->course_registration) && $student_makeup_exam->course_registration->id != "") {
                        $student_makeup_exam->exam_grades = $student_makeup_exam->course_registration->exam_grades;
                    } else {
                        $student_makeup_exam->exam_grades = $student_makeup_exam->course_add->exam_grades ?? [];
                    }

                    if (!empty($student_makeup_exam->exam_grades)) {
                        foreach ($student_makeup_exam->exam_grades as $eg_key => $exam_grade) {
                            $exam_grade->department_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->department_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                            $exam_grade->registrar_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->registrar_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                        }
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;
        $student_course_register_and_adds['makeup'] = $students_makeup_exam;

        return $student_course_register_and_adds;
    }

    public function getStudentSelectedFxExamPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = [];

        if ($published_course_id != null) {
            $fxResitRequestsTable = TableRegistry::getTableLocator()->get('FxResitRequests');
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');
            $usersTable = TableRegistry::getTableLocator()->get('Users');

            $fx_applied_student_lists = $fxResitRequestsTable->find('list')
                ->where(['published_course_id' => $published_course_id])
                ->select(['student_id', 'student_id'])
                ->toArray();

            $students = $courseRegistrationsTable->find()
                ->select(['id'])
                ->where([
                    'published_course_id' => $published_course_id,
                    'student_id IN' => $fx_applied_student_lists
                ])
                ->contain([
                    'PublishedCourses' => ['fields' => ['college_id']],
                    'ExamGrades' => ['sort' => ['id' => 'DESC']],
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'sort' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC']
                    ],
                    'CourseDrops',
                    'ExamResults' => [
                        'where' => ['course_add' => 0],
                        'ExamTypes'
                    ]
                ])
                ->toArray();

            if (!empty($students)) {
                foreach ($students as $key => &$student) {
                    $student->exam_grade_history = $courseRegistrationsTable->getCourseRegistrationGradeHistory($student->id);
                    $student->latest_grade_detail = $courseRegistrationsTable->getCourseRegistrationLatestGradeDetail($student->id);
                    $student->any_exam_grade_is_on_process = $courseRegistrationsTable->isAnyGradeOnProcess($student->id);
                    $student->freshman_program = (!is_null($student->published_course->college_id) ? true : false);

                    if (!empty($student->exam_grades)) {
                        foreach ($student->exam_grades as $eg_key => $exam_grade) {
                            $exam_grade->department_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->department_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                            $exam_grade->registrar_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->registrar_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                        }
                    }
                }
            }

            $student_all_adds = $courseAddsTable->find()
                ->where([
                    'published_course_id' => $published_course_id,
                    'student_id IN' => $fx_applied_student_lists
                ])
                ->contain([
                    'PublishedCourses',
                    'ExamGrades' => ['sort' => ['id' => 'DESC']],
                    'Students' => [
                        'fields' => ['id', 'first_name', 'middle_name', 'last_name', 'studentnumber', 'gender', 'graduated', 'academicyear'],
                        'sort' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC'],
                        'StudentsSections' => ['where' => ['archive' => 0]]
                    ],
                    'ExamResults' => ['where' => ['course_add' => 1]]
                ])
                ->toArray();

            $section_and_course_detail = $this->find()
                ->where(['id' => $published_course_id])
                ->contain(['Sections', 'Courses'])
                ->first();

            $section_detail = $section_and_course_detail->section;
            $course_detail = $section_and_course_detail->course;

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    if (!empty($student_all_add->exam_grades)) {
                        foreach ($student_all_add->exam_grades as $eg_key => $exam_grade) {
                            $exam_grade->department_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->department_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                            $exam_grade->registrar_approved_by_name = $usersTable->find()
                                ->select(['first_name', 'last_name'])
                                ->where(['id' => $exam_grade->registrar_approved_by])
                                ->enableHydration(true)
                                ->first()->full_name ?? 'N/A';
                        }
                    }

                    $student_all_add->exam_grade_history = $courseAddsTable->getCourseAddGradeHistory($student_all_add->id);
                    $student_all_add->latest_grade_detail = $courseAddsTable->getCourseAddLatestGradeDetail($student_all_add->id);
                    $student_all_add->any_exam_grade_is_on_process = $courseAddsTable->isAnyGradeOnProcess($student_all_add->id);
                    $student_all_add->freshman_program = (!is_null($student_all_add->published_course->college_id) ? true : false);

                    if (isset($student_all_add->student->students_sections[0]->section_id) && strcasecmp($student_all_add->student->students_sections[0]->section_id, $section_detail->id) == 0) {
                        $students[] = $student_all_add;
                    } else {
                        $student_adds[] = $student_all_add;
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;

        return $student_course_register_and_adds;
    }

    public function getStudentsWhoAddPublishedCourse($published_course_id = null, $college_id = null)
    {
        $student_adds = [];

        if ($published_course_id != null) {
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');

            $options = [
                'where' => ['published_course_id' => $published_course_id],
                'contain' => ['PublishedCourses']
            ];

            if (!empty($college_id)) {
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $department_ids = $departmentsTable->find('list')
                    ->where(['college_id' => $college_id])
                    ->select(['id'])
                    ->toArray();
                $options['where']['OR'] = [
                    'PublishedCourses.college_id' => $college_id,
                    'PublishedCourses.department_id IN' => $department_ids
                ];
            }

            $student_all_adds = $courseAddsTable->find()
                ->where($options['where'])
                ->contain($options['contain'])
                ->toArray();

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    if (($student_all_add->department_approval == 1 && $student_all_add->registrar_confirmation == 1) || $student_all_add->published_course->add == 1) {
                        $student_adds[] = $student_all_add->student_id;
                    }
                }
            }
        }
        return $student_adds;
    }

    public function getGradeScaleDetail($published_course_id = null)
    {
        $grade_scale = [];

        if (!empty($published_course_id)) {
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
            $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');
            $gradeScalesTable = TableRegistry::getTableLocator()->get('GradeScales');

            $course_detail = $this->find()
                ->where(['id' => $published_course_id])
                ->contain([
                    'CourseRegistrations' => ['ExamGrades'],
                    'CourseAdds' => ['ExamGrades'],
                    'MakeupExams' => ['ExamGrades'],
                    'Courses' => [
                        'Curriculums' => [
                            'Programs' => ['fields' => ['id', 'name']],
                            'Departments' => ['Colleges']
                        ]
                    ]
                ])
                ->first();

            $grade_scale_detail = $this->find()
                ->where(['id' => $published_course_id])
                ->contain([
                    'Courses',
                    'GradeScales' => [
                        'GradeScaleDetails' => [
                            'sort' => ['maximum_result' => 'DESC'],
                            'Grades' => ['GradeTypes']
                        ]
                    ]
                ])
                ->first();

            if ((!empty($course_detail->course_registrations[0]->exam_grades)) || (!empty($course_detail->course_adds[0]->exam_grades))) {
                $grade_scale_detail = $gradeScalesTable->find()
                    ->where([
                        'id' => (!empty($course_detail->course_registrations[0]->exam_grades) ? $course_detail->course_registrations[0]->exam_grades[0]->grade_scale_id : $course_detail->course_adds[0]->exam_grades[0]->grade_scale_id)
                    ])
                    ->contain([
                        'GradeScaleDetails' => [
                            'sort' => ['maximum_result' => 'DESC'],
                            'Grades' => ['GradeTypes']
                        ]
                    ])
                    ->first();

                if ($course_detail->grade_scale_id != "" && $course_detail->grade_scale_id != "0") {
                    $grade_scale['scale_by'] = 'Department';
                } else {
                    $grade_scale['scale_by'] = 'College';
                }

                $grade_scale['Course'] = $course_detail->course;
                $grade_scale['GradeType'] = $grade_scale_detail->grade_scale_details[0]->grade->grade_type;
                $formated_grade_scale_details = [];
                $count = 0;

                if (!empty($grade_scale_detail)) {
                    foreach ($grade_scale_detail->grade_scale_details as $key => $grade_scale_det) {
                        $formated_grade_scale_details[$count]['minimum_result'] = $grade_scale_det->minimum_result;
                        $formated_grade_scale_details[$count]['maximum_result'] = $grade_scale_det->maximum_result;
                        $formated_grade_scale_details[$count]['grade'] = $grade_scale_det->grade->grade;
                        $formated_grade_scale_details[$count]['point_value'] = $grade_scale_det->grade->point_value;
                        $formated_grade_scale_details[$count]['repeatable'] = $grade_scale_det->grade->allow_repetition;
                        $formated_grade_scale_details[$count++]['pass_grade'] = $grade_scale_det->grade->pass_grade;
                    }
                }

                $grade_scale['GradeScaleDetail'] = $formated_grade_scale_details;
                $grade_scale['GradeScale'] = $grade_scale_detail;
            } elseif ($grade_scale_detail->grade_scale_id != "" && $grade_scale_detail->grade_scale_id != "0") {
                $grade_scale['scale_by'] = 'Department';
                $grade_scale['Course'] = $grade_scale_detail->course;
                $grade_scale['GradeType'] = $grade_scale_detail->grade_scale->grade_scale_details[0]->grade->grade_type;
                $formated_grade_scale_details = [];
                $count = 0;

                foreach ($grade_scale_detail->grade_scale->grade_scale_details as $key => $grade_scale_det) {
                    $formated_grade_scale_details[$count]['minimum_result'] = $grade_scale_det->minimum_result;
                    $formated_grade_scale_details[$count]['maximum_result'] = $grade_scale_det->maximum_result;
                    $formated_grade_scale_details[$count]['grade'] = $grade_scale_det->grade->grade;
                    $formated_grade_scale_details[$count]['point_value'] = $grade_scale_det->grade->point_value;
                    $formated_grade_scale_details[$count]['repeatable'] = $grade_scale_det->grade->allow_repetition;
                    $formated_grade_scale_details[$count++]['pass_grade'] = $grade_scale_det->grade->pass_grade;
                }

                $grade_scale['GradeScaleDetail'] = $formated_grade_scale_details;
                $grade_scale['GradeScale'] = $grade_scale_detail->grade_scale;
            } else {
                if (($course_detail->course->curriculum->program_id == 1 && $course_detail->course->curriculum->department->college->deligate_scale == 1) ||
                    ($course_detail->course->curriculum->program_id == 2 && $course_detail->course->curriculum->department->college->deligate_for_graduate_study == 1)
                ) {
                    if (!empty($course_detail->department_id)) {
                        $grade_scale['error'] = 'Grade scale is not defined for <u>' . $grade_scale_detail->course->course_title . ' (' . $grade_scale_detail->course->course_code . ')</u> course or scale defined is deactived. Please contact <u>' . $course_detail->course->curriculum->department->name . '</u> department to set grade scale for the course.';
                    } else {
                        $grade_scale['error'] = 'Grade scale is not defined for <u>' . $grade_scale_detail->course->course_title . ' (' . $grade_scale_detail->course->course_code . ')</u> course or scale defined is deactived. Please contact <u>Freshman Program</u> to set grade scale for the course.';
                    }
                    $grade_scale['author'] = 'Department';
                } else {
                    $grade_scale['author'] = 'College';
                    $grade_scale_and_type = $course_detail->course->getGradeScaleDetails($course_detail->course->id, $course_detail->course->curriculum->department->college->id);

                    if (count($grade_scale_and_type['GradeScale']) == 0) {
                        $grade_type_detail = $course_detail->course->find()
                            ->where(['id' => $course_detail->course->id])
                            ->contain(['GradeTypes'])
                            ->first();

                        $recomended_grade_types = [];
                        $recomended_grade_types_count = 0;

                        $available_grade_types = $gradeScalesTable->find()
                            ->where([
                                'model' => 'College',
                                'foreign_key' => $course_detail->course->curriculum->department->college->id,
                                'program_id' => $course_detail->course->curriculum->program_id,
                                'active' => 1
                            ])
                            ->contain([
                                'GradeTypes' => [
                                    'where' => [
                                        'active' => 1,
                                        'used_in_gpa IN' => $grade_type_detail->grade_type->used_in_gpa ?? [1, 0],
                                        'scale_required IN' => $grade_type_detail->grade_type->scale_required ?? [1, 0]
                                    ]
                                ]
                            ])
                            ->toArray();

                        if (!empty($available_grade_types)) {
                            foreach ($available_grade_types as $grTypes) {
                                if (!empty($grTypes->grade_type->type) && !in_array($grTypes->grade_type->type, $recomended_grade_types)) {
                                    $recomended_grade_types[] = $grTypes->grade_type->type;
                                }
                            }
                        }

                        if (!empty($recomended_grade_types)) {
                            $recomended_grade_types_count = count($recomended_grade_types);
                            $recomended_grade_types = '"' . (implode('" or "', $recomended_grade_types)) . '"';
                        }

                        if ($recomended_grade_types_count) {
                            $grade_scale['error'] = 'Grade scale is not defined/deactivated for "' . $grade_type_detail->grade_type->type . '" grade type under ' . $course_detail->course->curriculum->department->college->name . ' for <b><u>' . $course_detail->course->curriculum->program->name . '</u></b> program. Please contact ' . $course_detail->course->curriculum->department->name .  ' department to change the grade type of ' .  $course_detail->course->course_code_title . ' course under ' .  $course_detail->course->curriculum->name . ' curriculum from "' . $grade_type_detail->grade_type->type . '" to ' . $recomended_grade_types . ' grade type which have a defined active grade scales and try to submit the grade for the course.';
                        } else {
                            $grade_scale['error'] = 'Grade scale is not defined/deactivated for "' . $grade_type_detail->grade_type->type . '" grade type under ' . $course_detail->course->curriculum->department->college->name . ' for <b><u>' . $course_detail->course->curriculum->program->name . '</u></b> program. Please contact the registrar to set grade scale and you can submit grade for the course.';
                        }
                    } elseif (count($grade_scale_and_type['GradeScale']) > 1) {
                        $grade_scale['error'] = 'Multiple grade scale for the same grade type is set by the ' . $course_detail->course->curriculum->department->college->name . ' for ' . $grade_scale_detail->course->course_title . ' (' . $grade_scale_detail->course->course_code . ') course. Please contact your ' . $course_detail->course->curriculum->department->college->name . ' to deactivate grade scales which are not on use.';
                    } else {
                        $grade_scale_detail = $gradeScalesTable->find()
                            ->where(['id' => $grade_scale_and_type['GradeScale'][0]['id']])
                            ->contain([
                                'GradeScaleDetails' => [
                                    'sort' => ['maximum_result' => 'DESC'],
                                    'Grades' => ['GradeTypes']
                                ]
                            ])
                            ->first();

                        $grade_scale['scale_by'] = 'College';
                        $grade_scale['Course'] = $course_detail->course;

                        $grade_scale['GradeType'] = $grade_scale_detail->grade_scale_details[0]->grade->grade_type;
                        $formated_grade_scale_details = [];
                        $count = 0;

                        if (!empty($grade_scale_detail)) {
                            foreach ($grade_scale_detail->grade_scale_details as $key => $grade_scale_det) {
                                $formated_grade_scale_details[$count]['minimum_result'] = $grade_scale_det->minimum_result;
                                $formated_grade_scale_details[$count]['maximum_result'] = $grade_scale_det->maximum_result;
                                $formated_grade_scale_details[$count]['grade'] = $grade_scale_det->grade->grade;
                                $formated_grade_scale_details[$count]['point_value'] = $grade_scale_det->grade->point_value;
                                $formated_grade_scale_details[$count]['repeatable'] = $grade_scale_det->grade->allow_repetition;
                                $formated_grade_scale_details[$count++]['pass_grade'] = $grade_scale_det->grade->pass_grade;
                            }
                        }

                        $grade_scale['GradeScaleDetail'] = $formated_grade_scale_details;
                        $grade_scale['GradeScale'] = $grade_scale_detail;
                    }
                }
            }
        }
        return $grade_scale;
    }

    public function lastPublishedCoursesForSection($section_id = null)
    {
        $published_courses_list = [];

        if (!empty($section_id)) {
            $last_ac_and_semester = $this->find()
                ->select(['academic_year', 'semester'])
                ->where(['section_id' => $section_id])
                ->order(['created' => 'DESC'])
                ->first();

            $published_courses = [];

            if (!empty($last_ac_and_semester)) {
                $published_courses = $this->find()
                    ->where([
                        'academic_year' => $last_ac_and_semester->academic_year,
                        'semester' => $last_ac_and_semester->semester,
                        'section_id' => $section_id,
                        'drop' => 0,
                        'add' => 0
                    ])
                    ->contain(['Courses'])
                    ->toArray();
            }

            if (!empty($published_courses)) {
                foreach ($published_courses as $published_course) {
                    if (isset($published_course->course->id)) {
                        $published_courses_list[$published_course->id] = $published_course->course->course_code_title . ', ' . ($last_ac_and_semester->semester == 'I' ? '1st' : ($last_ac_and_semester->semester == 'II' ? '2nd' : '3rd')) . ' semester,  ' . $last_ac_and_semester->academic_year;
                    }
                }
            }
        }

        return $published_courses_list;
    }

    public function sectionPublishedCourses($section_id = null)
    {
        $published_courses_list = [];

        if (!empty($section_id)) {
            $published_courses = $this->find()
                ->where([
                    'section_id' => $section_id,
                    'drop' => 0,
                    'add' => 0
                ])
                ->contain(['Courses'])
                ->toArray();

            if (!empty($published_courses)) {
                foreach ($published_courses as $published_course) {
                    if (isset($published_course->course->id)) {
                        $published_courses_list[$published_course->id] = $published_course->course->course_code_title . ', ' . ($published_course->semester == 'I' ? '1st' : ($published_course->semester == 'II' ? '2nd' : '3rd')) . ' semester,  ' . $published_course->academic_year;
                    }
                }
            }
        }

        return $published_courses_list;
    }

    public function isItValidGradeForPublishedCourse($published_course_id, $grade)
    {
        $grade_scale_details_all = $this->getGradeScaleDetail($published_course_id);
        $grade_scale_details = $grade_scale_details_all['GradeScaleDetail'];

        $valid_grades = [];

        if (!empty($grade_scale_details)) {
            foreach ($grade_scale_details as $scale) {
                $valid_grades[] = $scale['grade'];
            }
        }

        if (in_array($grade, $valid_grades)) {
            return true;
        } else {
            return false;
        }
    }

    public function getInstructorByExamGradeId($exam_grade_id = null)
    {
        $course_instructor = null;

        if (!empty($exam_grade_id)) {
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

            $exam_grade_detail = $examGradesTable->find()
                ->where(['id' => $exam_grade_id])
                ->contain([
                    'CourseAdds' => [
                        'PublishedCourses' => [
                            'CourseInstructorAssignments' => [
                                'where' => ['type LIKE' => '%Lecture%'],
                                'Staffs'
                            ]
                        ]
                    ],
                    'CourseRegistrations' => [
                        'PublishedCourses' => [
                            'CourseInstructorAssignments' => [
                                'where' => ['type LIKE' => '%Lecture%'],
                                'Staffs'
                            ]
                        ]
                    ]
                ])
                ->first();

            if (!empty($exam_grade_detail->course_registration->published_course->course_instructor_assignments[0]->staff)) {
                $course_instructor = $exam_grade_detail->course_registration->published_course->course_instructor_assignments[0]->staff;
            } elseif (!empty($exam_grade_detail->course_add->published_course->course_instructor_assignments[0]->staff)) {
                $course_instructor = $exam_grade_detail->course_add->published_course->course_instructor_assignments[0]->staff;
            }
        }

        return $course_instructor;
    }

    public function getPublishedCourseByExamGradeId($exam_grade_id = null)
    {
        $published_course = null;

        if (!empty($exam_grade_id)) {
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

            $exam_grade_detail = $examGradesTable->find()
                ->where(['id' => $exam_grade_id])
                ->contain([
                    'CourseAdds' => ['PublishedCourses'],
                    'CourseRegistrations' => ['PublishedCourses']
                ])
                ->first();

            if (!empty($exam_grade_detail->course_registration->published_course)) {
                $published_course = $exam_grade_detail->course_registration->published_course;
            } elseif (!empty($exam_grade_detail->course_add->published_course)) {
                $published_course = $exam_grade_detail->course_add->published_course;
            }
        }

        return $published_course;
    }

    public function previousSemesterAndAcademicCoursePublished($given_semester = null, $given_academic_year = null, $department_id = null, $program_id = null, $program_type_id = null, $year_level_id = null, $section_id = null)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        if ($given_semester == 'I') {
            $previous_ac_semester = $studentExamStatusesTable->getPreviousSemester($given_academic_year, 'I');
        } else {
            $previous_ac_semester = $studentExamStatusesTable->getPreviousSemester($given_academic_year, $given_semester);
        }

        if (isset($section_id) && !empty($section_id)) {
            $publishedCourseInThatSection = $this->find()
                ->where(['section_id' => $section_id])
                ->order(['created' => 'DESC'])
                ->first();

            $sectionDetail = $sectionsTable->find()
                ->where(['id' => $section_id])
                ->order(['created' => 'DESC'])
                ->first();

            if (!empty($publishedCourseInThatSection) && !empty($publishedCourseInThatSection->department_id)) {
                if ($department_id != $publishedCourseInThatSection->department_id && $sectionDetail->department_id == $department_id) {
                    $department_id = $publishedCourseInThatSection->department_id;
                    $year_level_id = $publishedCourseInThatSection->year_level_id;
                }
            }
        }

        $is_course_published = $this->find()
            ->where([
                'semester' => $previous_ac_semester['semester'],
                'academic_year LIKE' => $previous_ac_semester['academic_year'] . '%',
                'department_id' => $department_id,
                'program_id' => $program_id,
                'program_type_id' => $program_type_id,
                'year_level_id' => $year_level_id,
                'section_id' => $section_id
            ])
            ->count();

        if ($is_course_published > 0) {
            return true;
        } else {
            $first_time = $this->find()
                ->where(['section_id' => $section_id])
                ->count();

            $freshdetail = $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels')->find()
                ->where(['id' => $year_level_id])
                ->contain(['Departments'])
                ->first();

            $oneSampleStudent = $studentsSectionsTable->find()
                ->where([
                    'section_id' => $section_id,
                    'archive' => 0
                ])
                ->first();

            if (isset($oneSampleStudent)) {
                $getStudentPreSection = $studentsSectionsTable->find()
                    ->where([
                        'student_id' => $oneSampleStudent->student_id,
                        'archive' => 1
                    ])
                    ->first();
            }

            if (isset($getStudentPreSection->section_id)) {
                $wasTheStudentHasPreProgram = $sectionsTable->find()
                    ->where([
                        'id' => $getStudentPreSection->section_id,
                        'department_id IS NULL'
                    ])
                    ->count();
            }

            if (($first_time == 0 && $given_semester == 'I') || ($first_time == 0 && $given_semester == 'II' && $freshdetail->name == '1st' && $wasTheStudentHasPreProgram)) {
                return true;
            } else {
                if (!empty($oneSampleStudent)) {
                    $findMostRecentSection = $studentsSectionsTable->find()
                        ->where([
                            'student_id' => $oneSampleStudent->student_id,
                            'section_id !=' => $section_id
                        ])
                        ->order(['created' => 'DESC'])
                        ->first();

                    $isCoursepublished = $this->find()
                        ->where([
                            'semester' => $previous_ac_semester['semester'],
                            'academic_year LIKE' => $previous_ac_semester['academic_year'] . '%',
                            'department_id' => $department_id,
                            'program_id' => $program_id,
                            'program_type_id' => $program_type_id,
                            'year_level_id' => $year_level_id,
                            'section_id' => $findMostRecentSection->section_id
                        ])
                        ->count();

                    if ($isCoursepublished) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    public function getSectionOrganizedPublishedCoursesScaleAttachment($data = null, $department_id = null, $publishedcourses = null, $college_id = null)
    {
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        if (strcasecmp($department_id, 'pre') === 0) {
            $sections = $sectionsTable->find('list')
                ->where([
                    'college_id' => $college_id,
                    'department_id IS NULL',
                    'program_id' => PROGRAM_UNDEGRADUATE,
                    'program_type_id' => PROGRAM_TYPE_REGULAR,
                    'archive' => 0
                ])
                ->toArray();
        } else {
            $sections = $sectionsTable->find('list')
                ->where([
                    'department_id' => $department_id,
                    'program_id' => $data['PublishedCourse']['program_id'],
                    'archive' => 0
                ])
                ->toArray();
        }

        if (!empty($sections) && !empty($publishedcourses)) {
            $section_organized_published_courses = [];
            foreach ($sections as $section_id => $section_name) {
                foreach ($publishedcourses as $kkk => &$vvv) {
                    if ($vvv['PublishedCourse']['section_id'] == $section_id) {
                        if ($examGradesTable->isGradeSubmitted($vvv['PublishedCourse']['id']) > 0) {
                            $vvv['PublishedCourse']['scale_readOnly'] = true;
                            $vvv['PublishedCourse']['unpublish_readOnly'] = true;
                        } else {
                            $vvv['PublishedCourse']['scale_readOnly'] = false;
                            $vvv['PublishedCourse']['unpublish_readOnly'] = false;
                        }
                        $section_organized_published_courses[$section_name . "(" . $vvv['Section']['ProgramType']['name'] . ")"][] = $publishedcourses[$kkk];
                    }
                }
            }
            return $section_organized_published_courses;
        }

        return null;
    }

    public function isPublishedCourseRequiredScale($published_course_id)
    {
        $requiredScale = $this->find()
            ->where(['id' => $published_course_id])
            ->contain(['Courses' => ['GradeTypes']])
            ->first();

        return $requiredScale;
    }

    public function isCoursePublishedInSection($sectionId)
    {
        $count = $this->find()
            ->where(['section_id' => $sectionId])
            ->count();

        return $count;
    }

    public function listSimilarPublishedCoursesForCombo($publishedCourseId = null)
    {
        $publishedList = [];

        if (!empty($publishedCourseId)) {
            $published_course = $this->find()
                ->where(['id' => $publishedCourseId])
                ->contain([
                    'Sections',
                    'YearLevels',
                    'GivenByDepartments',
                    'CourseInstructorAssignments' => [
                        'Staffs' => [
                            'Departments',
                            'Titles' => ['fields' => ['id', 'title']],
                            'Positions' => ['fields' => ['id', 'position']]
                        ]
                    ],
                    'Courses'
                ])
                ->first();

            $pubList = $this->find()
                ->where([
                    'course_id' => $published_course->course_id,
                    'semester' => $published_course->semester,
                    'academic_year' => $published_course->academic_year
                ])
                ->contain([
                    'Sections',
                    'YearLevels',
                    'GivenByDepartments',
                    'CourseInstructorAssignments' => [
                        'Staffs' => [
                            'Departments',
                            'Titles' => ['fields' => ['id', 'title']],
                            'Positions' => ['fields' => ['id', 'position']]
                        ]
                    ],
                    'Courses'
                ])
                ->toArray();

            if (!empty($pubList)) {
                $publishedList[''] = '[ Select Section to Assign ]';
                foreach ($pubList as $value) {
                    $publishedList[$value->id] = $value->course->course_code_title . $value->section->name . ' (' . (!empty($value->year_level->name) ? $value->year_level->name : ($value->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $value->section->academicyear . ') ' . (isset($value->course_instructor_assignments[0]) ? $value->course_instructor_assignments[0]->staff->title->title . ' ' . $value->course_instructor_assignments[0]->staff->full_name . ' (' . $value->course_instructor_assignments[0]->staff->department->name . ')' : '');
                }
            }
        }

        return $publishedList;
    }
}
