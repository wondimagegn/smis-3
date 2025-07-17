<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\AcademicYearComponent;

use Cake\Database\Expression\QueryExpression;

class ExamGradeChangesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('exam_grade_changes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->belongsTo('ExamGrades', [
            'foreignKey' => 'exam_grade_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MakeupExams', [
            'foreignKey' => 'makeup_exam_id',
            'joinType' => 'LEFT',
        ]);
        $this->addBehavior('Timestamp');
        // Initialize component
        $this->academicYearComponent = new AcademicYearComponent(new ComponentRegistry());

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('grade', 'Please enter makeup exam scored grade.', 'create')
            ->requirePresence('grade', 'create')
            ->notEmptyString('minute_number', 'Please enter makeup exam minute number.', 'create')
            ->requirePresence('minute_number', 'create')
            ->integer('registrar_approval')
            ->allowEmptyString('registrar_reason')
            ->dateTime('registrar_approval_date')
            ->uuid('registrar_approved_by')
            ->allowEmptyString('registrar_approved_by');

        return $validator;
    }

    public function canItBeDeleted($id = "")
    {
        if ($id != "") {
            $examGradeChange = $this->find()
                ->where(['id' => $id])
                ->first();

            if ($examGradeChange->initiated_by_department == 1 && $examGradeChange->registrar_approval === null) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function examGradeChangeStateDescription($exam_grade_change = null)
    {
        $status = [];

        if (is_array($exam_grade_change)) {
            if (empty($exam_grade_change)) {
                $status['state'] = 'on-process';
                $status['description'] = 'Grade is not yet submitted.';
            } else {
                if ($exam_grade_change['initiated_by_department'] == 1 || $exam_grade_change['department_approval'] == 1) {
                    if ($exam_grade_change['college_approval'] == 1 || $exam_grade_change['makeup_exam_result'] != null) {
                        if ($exam_grade_change['registrar_approval'] == 1) {
                            $status['state'] = 'accepted';
                            $status['description'] = 'Accepted';
                        } else if ($exam_grade_change['registrar_approval'] == -1) {
                            $status['state'] = 'rejected';
                            if ($exam_grade_change['college_approval'] == 1) {
                                $status['description'] = 'Accepted by both department and college but rejected by registrar.';
                            } else {
                                $status['description'] = 'Accepted by department but rejected by registrar.';
                            }
                        } else if ($exam_grade_change['registrar_approval'] === null) {
                            $status['state'] = 'on-process';
                            if ($exam_grade_change['college_approval'] == 1) {
                                $status['description'] = 'Accepted by both department and college and waiting for registrar approval.';
                            } else {
                                $status['description'] = 'Accepted by department and waiting for registrar approval.';
                            }
                        }
                    } else if ($exam_grade_change['college_approval'] == -1) {
                        $status['state'] = 'rejected';
                        $status['description'] = 'Accepted by department but rejected by college.';
                    } else if ($exam_grade_change['college_approval'] === null) {
                        $status['state'] = 'on-process';
                        $status['description'] = 'Accepted by department and waiting for college approval.';
                    }
                } else if ($exam_grade_change['department_approval'] == -1) {
                    $status['state'] = 'rejected';
                    $status['description'] = 'Rejected by the department.';
                } else if ($exam_grade_change['department_approval'] === null) {
                    $status['state'] = 'on-process';
                    $status['description'] = 'Waiting for department approval.';
                }
            }
        }
        return $status;
    }

    public function getListOfGradeChangeForDepartmentApproval($col_dpt_id = null, $department = 1, $departmentIDs = [])
    {
        // Validate inputs
        if (empty($col_dpt_id) && empty($departmentIDs)) {
            return ['summary' => [], 'count' => 0];
        }

        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
        ];

        if (!empty($departmentIDs)) {
            $conditions['given_by_department_id'] = $departmentIDs;
        } elseif ($department == 0) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $department_ids = $departmentsTable->find('list', [
                'conditions' => ['Departments.college_id' => $col_dpt_id, 'Departments.active' => 1],
                'keyField' => 'id',
                'valueField' => 'id'
            ])->toArray();

            if (empty($department_ids)) {
                return ['summary' => [], 'count' => 0];
            }
            $conditions['given_by_department_id'] = array_values($department_ids);
        } else {
            $conditions['given_by_department_id'] = [$col_dpt_id];
        }

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
                'MakeupExams.id',
                'MakeupExams.published_course_id',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NULL',
                'ExamGradeChanges.department_approval IS NULL',
                'ExamGradeChanges.manual_ng_conversion' => 0,
                'ExamGradeChanges.auto_ng_conversion' => 0,
            ])
            ->contain([
                'MakeupExams' => [
                    'queryBuilder' => function ($q) use ($conditions) {
                        return $q->select([
                            'MakeupExams.id',
                            'MakeupExams.published_course_id',
                        ])->contain([
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                        ]);
                    },
                ],
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $department_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;

        foreach ($department_action_required_list as $grade_change_detail) {
            $type = null;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            if (!$publishedCourse || empty($exam_grade->$type->student) || $exam_grade->$type->student->graduated) {
                continue;
            }

            if ($department == 1) {
                if (!$this->isValidPublishedCourse($publishedCourse, ['given_by_department_id' => [$col_dpt_id], 'academic_year' => $conditions['academic_year'], 'year_level_id' => $conditions['year_level_id']]) &&
                    (empty($departmentIDs) || !$this->isValidPublishedCourse($publishedCourse, ['given_by_department_id' => $departmentIDs, 'academic_year' => $conditions['academic_year'], 'year_level_id' => $conditions['year_level_id']]))) {
                    continue;
                }
            } else {
                if (!$this->isValidPublishedCourse($publishedCourse, ['college_id' => [$col_dpt_id], 'academic_year' => $conditions['academic_year'], 'year_level_id' => $conditions['year_level_id']]) &&
                    (empty($departmentIDs) || !$this->isValidPublishedCourse($publishedCourse, ['given_by_department_id' => $departmentIDs, 'academic_year' => $conditions['academic_year'], 'year_level_id' => $conditions['year_level_id']]))) {
                    continue;
                }
            }

            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? (array)$exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? (array)$publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => (array)$grade_change_detail,
                'Staff' => !empty($publishedCourse->course_instructor_assignments) && !empty($publishedCourse->course_instructor_assignments[0]->staff)
                    ? (array)$publishedCourse->course_instructor_assignments[0]->staff
                    : [],
                'Section' => $section ? (array)$section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            $exam_grade_changes_summary[$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    public function getListOfMakeupGradeChangeForDepartmentApproval($col_dep_id = null, $registrar_rejected = 0,
        $department = 1, $departmentIDs = [])
    {
        // Validate inputs
        if (empty($col_dep_id) && empty($departmentIDs)) {
            return ['summary' => [], 'count' => 0];
        }

        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Normalize departmentIDs
        $department_ids = is_array($departmentIDs) ? $departmentIDs : (empty($departmentIDs) ? [] : [$departmentIDs]);

        // Get department IDs if department = 0
        if (empty($department_ids) && !$department) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $department_ids = $departmentsTable->find('list', [
                'conditions' => ['Departments.college_id' => $col_dep_id, 'Departments.active' => 1],
                'keyField' => 'id',
                'valueField' => 'id'
            ])->toArray();
            if (empty($department_ids)) {
                return ['summary' => [], 'count' => 0];
            }
        } elseif (empty($department_ids) && $department) {
            $department_ids = [$col_dep_id];
        }

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
            'given_by_department_id' => $department_ids,
        ];

        // Main query
        $queryConditions = [
            'ExamGradeChanges.makeup_exam_result IS NOT NULL',
            'ExamGradeChanges.department_approval IS NULL',
        ];
        if ($registrar_rejected) {
            $queryConditions = [
                'ExamGradeChanges.makeup_exam_result IS NOT NULL',
                'ExamGradeChanges.initiated_by_department' => 0,
                'ExamGradeChanges.department_approval' => 1,
                'ExamGradeChanges.registrar_approval' => -1,
            ];
        }

        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGradeChanges.makeup_exam_result',
                'ExamGradeChanges.registrar_approval',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
                'MakeupExams.id',
                'MakeupExams.published_course_id',
            ])
            ->where($queryConditions)
            ->contain([
                'MakeupExams' => [
                    'queryBuilder' => function ($q) use ($conditions) {
                        return $q->select([
                            'MakeupExams.id',
                            'MakeupExams.published_course_id',
                        ])->contain([
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                        ]);
                    },
                ],
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $department_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;
        $processed_makeup_grade_changes = [];

        foreach ($department_action_required_list as $grade_change_detail) {
            $type = null;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            $makeupExam = !empty($grade_change_detail->makeup_exam) ? $grade_change_detail->makeup_exam : null;
            if (!$publishedCourse || empty($exam_grade->$type->student) || $exam_grade->$type->student->graduated) {
                continue;
            }

            // Handle registrar_rejected deduplication
            if ($registrar_rejected) {
                $latest_grade_change = $this->find()
                    ->select(['ExamGradeChanges.exam_grade_id', 'ExamGradeChanges.registrar_approval'])
                    ->where(['ExamGradeChanges.exam_grade_id' => $grade_change_detail->exam_grade_id])
                    ->order(['ExamGradeChanges.created' => 'DESC'])
                    ->first();
                if (!$latest_grade_change || $latest_grade_change->registrar_approval != -1 || in_array($latest_grade_change->exam_grade_id, $processed_makeup_grade_changes)) {
                    continue;
                }
                $processed_makeup_grade_changes[] = $latest_grade_change->exam_grade_id;
            }

            // Validate PublishedCourse
            $published_by_college_asked_by_department = false;
            if ($type && ($department || !empty($department_ids) || $col_dep_id)) {
                if (!$this->isValidPublishedCourse($publishedCourse, $conditions, $published_by_college_asked_by_department)) {
                    if (!$published_by_college_asked_by_department || empty($department_ids)) {
                        continue;
                    }
                }
                if ($department) {
                    if ($publishedCourse->given_by_department_id != $col_dep_id && (empty($department_ids) || !in_array($publishedCourse->given_by_department_id, $department_ids))) {
                        continue;
                    }
                } else {
                    if ($publishedCourse->college_id != $col_dep_id && (empty($department_ids) || !in_array($publishedCourse->given_by_department_id, $department_ids))) {
                        continue;
                    }
                }
            }

            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? (array)$exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? (array)$publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => (array)$grade_change_detail,
                'Staff' => !empty($makeupExam->published_course->course_instructor_assignments) && !empty($makeupExam->published_course->course_instructor_assignments[0]->staff)
                    ? (array)$makeupExam->published_course->course_instructor_assignments[0]->staff
                    : [],
                'ExamCourse' => !empty($makeupExam->published_course->course) ? (array)$makeupExam->published_course->course : [],
                'ExamSection' => !empty($makeupExam->published_course->section) ? (array)$makeupExam->published_course->section : [],
                'MakeupExam' => !empty($makeupExam) ? (array)$makeupExam : [],
                'Section' => $section ? (array)$section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            // Remove PublishedCourse from MakeupExam to avoid duplication
            if (!empty($summary_entry['MakeupExam']['published_course'])) {
                unset($summary_entry['MakeupExam']['published_course']);
            }

            $exam_grade_changes_summary[$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    public function getMakeupGradesAskedByDepartmentRejectedByRegistrar($col_dep_id = null, $department = 1, $departmentIDs = [])
    {
        // Validate inputs
        if (empty($col_dep_id) && empty($departmentIDs)) {
            return ['summary' => [], 'count' => 0];
        }

        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Normalize departmentIDs
        $department_ids = is_array($departmentIDs) ? $departmentIDs : (empty($departmentIDs) ? [] : [$departmentIDs]);

        // Get department IDs if department = 0
        if (empty($department_ids) && !$department) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $department_ids = $departmentsTable->find('list', [
                'conditions' => ['Departments.college_id' => $col_dep_id, 'Departments.active' => 1],
                'keyField' => 'id',
                'valueField' => 'id'
            ])->toArray();
            if (empty($department_ids)) {
                return ['summary' => [], 'count' => 0];
            }
        } elseif (empty($department_ids) && $department) {
            $department_ids = [$col_dep_id];
        }

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
        ];
        if (!empty($department_ids)) {
            $conditions['given_by_department_id'] = $department_ids;
        }

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGradeChanges.makeup_exam_result',
                'ExamGradeChanges.registrar_approval',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NOT NULL',
                'ExamGradeChanges.initiated_by_department' => 1,
                'ExamGradeChanges.department_approval' => 1,
                'ExamGradeChanges.registrar_approval' => -1,
            ])
            ->contain([
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    $where = [
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ];
                                    if (!empty($conditions['given_by_department_id'])) {
                                        $where['PublishedCourses.given_by_department_id IN'] = $conditions['given_by_department_id'];
                                    }
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'Departments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                            },
                                            'Colleges' => [
                                                'queryBuilder' => function ($q) {
                                                    return $q->select(['Colleges.id', 'Colleges.name']);
                                                },
                                            ],
                                        ],
                                    ])->where($where);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    $where = [
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ];
                                    if (!empty($conditions['given_by_department_id'])) {
                                        $where['PublishedCourses.given_by_department_id IN'] = $conditions['given_by_department_id'];
                                    }
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'Departments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                            },
                                            'Colleges' => [
                                                'queryBuilder' => function ($q) {
                                                    return $q->select(['Colleges.id', 'Colleges.name']);
                                                },
                                            ],
                                        ],
                                    ])->where($where);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $department_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;
        $processed_makeup_grade_changes = [];

        foreach ($department_action_required_list as $grade_change_detail) {
            $type = null;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            if (!$publishedCourse || empty($exam_grade->$type->student) || $exam_grade->$type->student->graduated) {
                continue;
            }

            // Handle deduplication
            $latest_grade_change = $this->find()
                ->select(['ExamGradeChanges.exam_grade_id', 'ExamGradeChanges.registrar_approval'])
                ->where(['ExamGradeChanges.exam_grade_id' => $grade_change_detail->exam_grade_id])
                ->order(['ExamGradeChanges.created' => 'DESC'])
                ->first();
            if (!$latest_grade_change || $latest_grade_change->registrar_approval != -1 || in_array($latest_grade_change->exam_grade_id, $processed_makeup_grade_changes)) {
                continue;
            }
            $processed_makeup_grade_changes[] = $latest_grade_change->exam_grade_id;

            // Validate PublishedCourse
            $published_by_college_asked_by_department = false;
            if ($type && ($department || !empty($department_ids) || $col_dep_id)) {
                if (!$this->isValidPublishedCourse($publishedCourse, $conditions, $published_by_college_asked_by_department)) {
                    if (!$published_by_college_asked_by_department || empty($department_ids)) {
                        continue;
                    }
                }
                if ($department) {
                    if ($publishedCourse->given_by_department_id != $col_dep_id && (empty($department_ids) || !in_array($publishedCourse->given_by_department_id, $department_ids))) {
                        continue;
                    }
                } else {
                    if ($publishedCourse->college_id != $col_dep_id && (empty($department_ids) || !in_array($publishedCourse->given_by_department_id, $department_ids))) {
                        continue;
                    }
                }
            }

            $department = !empty($publishedCourse->department) ? $publishedCourse->department : null;
            $college = !empty($department->college) ? $department->college : (!empty($publishedCourse->college) ? $publishedCourse->college : null);
            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;

            $collegeName = !empty($college->name) ? $college->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $departmentName = !empty($department->name) ? $department->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? (array)$exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? (array)$publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => (array)$grade_change_detail,
                'Staff' => !empty($publishedCourse->course_instructor_assignments) && !empty($publishedCourse->course_instructor_assignments[0]->staff)
                    ? (array)$publishedCourse->course_instructor_assignments[0]->staff
                    : [],
                'Section' => $section ? (array)$section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            $exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    /**
     * Check if PublishedCourse meets conditions for registrar approval
     * @param object $publishedCourse
     * @param array $conditions
     * @param bool $published_by_college_asked_by_department
     * @return bool
     */
    private function isValidPublishedCourse($publishedCourse, array $conditions, &$published_by_college_asked_by_department = null)
    {
        if (empty($publishedCourse)) {
            return false;
        }
        if (!in_array($publishedCourse->academic_year, $conditions['academic_year'], true)) {
            return false;
        }
        if (!$conditions['year_level_id']($publishedCourse->year_level_id)) {
            return false;
        }
        if (!empty($conditions['department_id'])) {
            if ($publishedCourse->department_id === null) {
                if ($published_by_college_asked_by_department !== null) {
                    $published_by_college_asked_by_department = true;
                }
                return false;
            }
            if (!in_array($publishedCourse->department_id, $conditions['department_id'])) {
                return false;
            }
        } elseif (!empty($conditions['college_id'])) {
            if ($publishedCourse->department_id !== null || !in_array($publishedCourse->college_id, $conditions['college_id'])) {
                return false;
            }
        }
        if (!empty($conditions['program_id']) && !empty($publishedCourse->program_id) && !in_array($publishedCourse->program_id, $conditions['program_id'])) {
            return false;
        }
        if (!empty($conditions['program_type_id']) && !empty($publishedCourse->program_type_id) && !in_array($publishedCourse->program_type_id, $conditions['program_type_id'])) {
            return false;
        }
        if (!empty($conditions['given_by_department_id']) && ($publishedCourse->given_by_department_id === null || !in_array($publishedCourse->given_by_department_id, $conditions['given_by_department_id']))) {
            return false;
        }
        return true;
    }

    /**
     * Check if PublishedCourse meets conditions for college approval
     * @param object $publishedCourse
     * @param array $conditions
     * @param string $college_id
     * @param string|null $given_by_college_id
     * @return bool
     */
    private function isValidPublishedCourseForCollege($publishedCourse, array $conditions, $college_id, $given_by_college_id)
    {
        if (empty($publishedCourse)) {
            return false;
        }
        if (!in_array($publishedCourse->academic_year, $conditions['academic_year'], true)) {
            return false;
        }
        if (!$conditions['year_level_id']($publishedCourse->year_level_id)) {
            return false;
        }
        if (!empty($conditions['given_by_department_id']) && ($publishedCourse->given_by_department_id === null || !in_array($publishedCourse->given_by_department_id, $conditions['given_by_department_id']))) {
            return false;
        }
        // Case-insensitive comparison for college_id
        return (isset($publishedCourse->given_by_department->college_id) && strcasecmp($publishedCourse->given_by_department->college_id, $college_id) === 0) ||
            (isset($publishedCourse->college_id) && strcasecmp($publishedCourse->college_id, $college_id) === 0) ||
            ($given_by_college_id !== null && strcasecmp($given_by_college_id, $college_id) === 0);
    }

    public function getListOfGradeChangeForCollegeApproval($college_id = null)
    {
        // Validate college_id
        if (empty($college_id)) {
            return ['summary' => [], 'count' => 0];
        }

        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Get department IDs for the college
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $department_ids = $departmentsTable->find('list', [
            'conditions' => ['Departments.college_id' => $college_id, 'Departments.active' => 1],
            'keyField' => 'id',
            'valueField' => 'id'
        ])->toArray();

        if (empty($department_ids)) {
            return ['summary' => [], 'count' => 0];
        }

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
            'given_by_department_id' => array_values($department_ids),
        ];

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
                'MakeupExams.id',
                'MakeupExams.published_course_id',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NULL',
                'ExamGradeChanges.college_approval IS NULL',
                'ExamGradeChanges.registrar_approval IS NULL',
                'ExamGradeChanges.department_approval' => 1,
            ])
            ->contain([
                'MakeupExams' => [
                    'queryBuilder' => function ($q) use ($conditions) {
                        return $q->select([
                            'MakeupExams.id',
                            'MakeupExams.published_course_id',
                        ])->contain([
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                        ]);
                    },
                ],
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                        'PublishedCourses.given_by_department_id IN' => $conditions['given_by_department_id'],
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $registrar_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;

        foreach ($registrar_action_required_list as $grade_change_detail) {
            $type = null;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            if (!$publishedCourse || empty($exam_grade->$type->student) || $exam_grade->$type->student->graduated) {
                continue;
            }

            // Get given_by_college_id
            $given_by_college_id = null;
            if (!empty($publishedCourse->given_by_department_id)) {
                $given_by_college_id = $departmentsTable->find()
                    ->select(['college_id'])
                    ->where(['Departments.id' => $publishedCourse->given_by_department_id])
                    ->first();
                $given_by_college_id = $given_by_college_id ? $given_by_college_id->college_id : null;
            }

            if (!$this->isValidPublishedCourseForCollege($publishedCourse, $conditions, $college_id, $given_by_college_id)) {
                continue;
            }

            $department = !empty($publishedCourse->department) ? $publishedCourse->department : null;
            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;

            $departmentName = !empty($department->name) ? $department->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$departmentName][$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? (array)$exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? (array)$publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => (array)$grade_change_detail,
                'Staff' => !empty($publishedCourse->course_instructor_assignments) && !empty($publishedCourse->course_instructor_assignments[0]->staff)
                    ? (array)$publishedCourse->course_instructor_assignments[0]->staff
                    : [],
                'Section' => $section ? (array)$section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            $exam_grade_changes_summary[$departmentName][$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$departmentName][$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    public function getListOfGradeChangeOnWaitingCollegeApproval($exam_grade_id = null, $college_id = null)
    {
        // Validate inputs
        if (empty($exam_grade_id) || empty($college_id)) {
            return ['summary' => [], 'count' => 0];
        }

        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
        ];

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
                'MakeupExams.id',
                'MakeupExams.published_course_id',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NULL',
                'ExamGradeChanges.college_approval IS NULL',
                'ExamGradeChanges.registrar_approval IS NULL',
                'ExamGradeChanges.department_approval' => 1,
                'ExamGradeChanges.exam_grade_id' => $exam_grade_id,
            ])
            ->contain([
                'MakeupExams' => [
                    'queryBuilder' => function ($q) use ($conditions) {
                        return $q->select([
                            'MakeupExams.id',
                            'MakeupExams.published_course_id',
                        ])->contain([
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                    },
                ],
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'Departments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                            },
                                            'Colleges' => [
                                                'queryBuilder' => function ($q) {
                                                    return $q->select(['Colleges.id', 'Colleges.name']);
                                                },
                                            ],
                                        ],
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.given_by_department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->contain([
                                        'Departments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                            },
                                            'Colleges' => [
                                                'queryBuilder' => function ($q) {
                                                    return $q->select(['Colleges.id', 'Colleges.name']);
                                                },
                                            ],
                                        ],
                                        'GivenByDepartments' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['GivenByDepartments.id', 'GivenByDepartments.college_id']);
                                            },
                                        ],
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $college_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        foreach ($college_action_required_list as $grade_change_detail) {
            $type = null;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            if (!$publishedCourse || empty($exam_grade->$type->student) || $exam_grade->$type->student->graduated) {
                continue;
            }

            // Get given_by_college_id
            $given_by_college_id = null;
            if (!empty($publishedCourse->given_by_department_id)) {
                $given_by_college_id = $departmentsTable->find()
                    ->select(['college_id'])
                    ->where(['Departments.id' => $publishedCourse->given_by_department_id])
                    ->first();
                $given_by_college_id = $given_by_college_id ? $given_by_college_id->college_id : null;
            }

            if (!$this->isValidPublishedCourseForCollege($publishedCourse, $conditions, $college_id, $given_by_college_id)) {
                continue;
            }

            $department = !empty($publishedCourse->department) ? $publishedCourse->department : null;
            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;

            $departmentName = !empty($department->name) ? $department->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$departmentName][$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? (array)$exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? (array)$publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => (array)$grade_change_detail,
                'Staff' => !empty($publishedCourse->course_instructor_assignments) && !empty($publishedCourse->course_instructor_assignments[0]->staff)
                    ? (array)$publishedCourse->course_instructor_assignments[0]->staff
                    : [],
                'Section' => $section ? (array)$section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            $exam_grade_changes_summary[$departmentName][$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$departmentName][$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    public function getListOfGradeChangeForRegistrarApproval($department_ids = null, $college_ids = null, $program_id = null, $program_type_id = null)
    {
        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Normalize input parameters to arrays
        $department_ids = is_array($department_ids) ? $department_ids : (empty($department_ids) ? [] : [$department_ids]);
        $college_ids = is_array($college_ids) ? $college_ids : (empty($college_ids) ? [] : [$college_ids]);
        $program_id = is_array($program_id) ? $program_id : (empty($program_id) ? [] : [$program_id]);
        $program_type_id = is_array($program_type_id) ? $program_type_id : (empty($program_type_id) ? [] : [$program_type_id]);

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
        ];
        if (!empty($department_ids)) {
            $conditions['department_id'] = $department_ids;
        } elseif (!empty($college_ids)) {
            $conditions['department_id'] = null;
            $conditions['college_id'] = $college_ids;
        } else {
            return ['summary' => [], 'count' => 0];
        }
        if (!empty($program_id)) {
            $conditions['program_id'] = $program_id;
        }
        if (!empty($program_type_id)) {
            $conditions['program_type_id'] = $program_type_id;
        }

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.initiated_by_department',
                'ExamGradeChanges.created',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
                'MakeupExams.id',
                'MakeupExams.published_course_id',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NULL',
                'ExamGradeChanges.registrar_approval IS NULL',
                'ExamGradeChanges.college_approval' => 1,
                'ExamGradeChanges.department_approval' => 1,
                'ExamGradeChanges.manual_ng_conversion' => 0,
                'ExamGradeChanges.auto_ng_conversion' => 0,
            ])
            ->contain([
                'MakeupExams' => [
                    'queryBuilder' => function ($q) use ($conditions) {
                        return $q->select([
                            'MakeupExams.id',
                            'MakeupExams.published_course_id',
                        ])->contain([
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                            ],
                        ]);
                    },
                ],
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q;
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id','Sections.name','Sections.year_level_id','Sections.academicyear',
                                            'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id','Staffs.first_name','Staffs.middle_name','Staffs.last_name',
                                                'Staffs.position_id','Staffs.title_id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id','Titles.title']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id','Positions.position']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q;
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id','Sections.name','Sections.year_level_id','Sections.academicyear',
                                            'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $registrar_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;

        foreach ($registrar_action_required_list as $grade_change_detail) {
            $type = null;
            $published_by_college_asked_by_department = false;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            if ($type && (!empty($program_id) || !empty($program_type_id) || !empty($department_ids) || !empty($college_ids))) {
                if (!$this->isValidPublishedCourse($publishedCourse, $conditions,$published_by_college_asked_by_department)) {
                    continue;
                }
                if (!empty($exam_grade->$type->student->graduated)) {
                    continue;
                }
            } elseif (!$publishedCourse) {
                continue;
            }

            $department = !empty($publishedCourse->department) ? $publishedCourse->department : null;
            $college = !empty($department->college) ? $department->college : (!empty($publishedCourse->college) ? $publishedCourse->college : null);
            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;

            $collegeName = !empty($college->name) ? $college->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $departmentName = !empty($department->name) ? $department->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName] ?? []);


            $exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index] = [
                'Student' => !empty($exam_grade->$type->student) ? $exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? $publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => $grade_change_detail,
                'Staff' => !empty($publishedCourse->course_instructor_assignments) && !empty($publishedCourse->course_instructor_assignments[0]->staff) ? $publishedCourse->course_instructor_assignments[0]->staff : [],
                'Section' => !empty($section) ? $section : null,
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    public function getListOfMakeupGradeChangeForRegistrarApproval($department_ids = null, $college_ids = null, $program_id = null, $program_type_id = null)
    {
        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Normalize input parameters to arrays
        $department_ids = is_array($department_ids) ? $department_ids : (empty($department_ids) ? [] : [$department_ids]);
        $college_ids = is_array($college_ids) ? $college_ids : (empty($college_ids) ? [] : [$college_ids]);
        $program_id = is_array($program_id) ? $program_id : (empty($program_id) ? [] : [$program_id]);
        $program_type_id = is_array($program_type_id) ? $program_type_id : (empty($program_type_id) ? [] : [$program_type_id]);

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
        ];
        if (!empty($department_ids)) {
            $conditions['department_id'] = $department_ids;
        } elseif (!empty($college_ids)) {
            $conditions['department_id'] = null;
            $conditions['college_id'] = $college_ids;
        } else {
            return ['summary' => [], 'count' => 0];
        }
        if (!empty($program_id)) {
            $conditions['program_id'] = $program_id;
        }
        if (!empty($program_type_id)) {
            $conditions['program_type_id'] = $program_type_id;
        }

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGradeChanges.initiated_by_department',
                'ExamGradeChanges.makeup_exam_result',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
                'MakeupExams.id',
                'MakeupExams.published_course_id',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NOT NULL',
                'ExamGradeChanges.initiated_by_department' => 0,
                'ExamGradeChanges.department_approval' => 1,
                'ExamGradeChanges.registrar_approval IS NULL',
            ])
            ->contain([
                'MakeupExams' => [
                    'queryBuilder' => function ($q) use ($conditions) {
                        return $q->select([
                            'MakeupExams.id',
                            'MakeupExams.published_course_id',
                        ])->contain([
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.graduated']);
                                },
                            ],
                        ]);
                    },
                ],
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id','Students.studentnumber','Students.first_name',
                                        'Students.middle_name','Students.last_name', 'Students.graduated']);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id','Students.studentnumber','Students.first_name',
                                        'Students.middle_name','Students.last_name', 'Students.graduated']);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where([
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ]);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);


        $registrar_action_required_list = $query->all()->toArray();
        $exam_grade_changes_summary = [];
        $total_count = 0;

        foreach ($registrar_action_required_list as $grade_change_detail) {
            $type = null;
            $published_by_college_asked_by_department=false;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            $makeupExam = !empty($grade_change_detail->makeup_exam) ? $grade_change_detail->makeup_exam : null;
            if ($type && (!empty($program_id) || !empty($program_type_id) || !empty($department_ids) || !empty($college_ids))) {
                if (!$this->isValidPublishedCourse($publishedCourse, $conditions,$published_by_college_asked_by_department)) {
                    continue;
                }
                if (!empty($exam_grade->$type->student->graduated)) {
                    continue;
                }
                if (!empty($program_id) && !empty($publishedCourse->program_id) && !in_array($publishedCourse->program_id, $program_id)) {
                    continue;
                }
                if (!empty($program_type_id) && !empty($publishedCourse->program_type_id) && !in_array($publishedCourse->program_type_id, $program_type_id)) {
                    continue;
                }
            } elseif (!$publishedCourse) {
                continue;
            }

            $department = !empty($publishedCourse->department) ? $publishedCourse->department : null;
            $college = !empty($department->college) ? $department->college : (!empty($publishedCourse->college) ? $publishedCourse->college : null);
            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;

            $collegeName = !empty($college->name) ? $college->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $departmentName = !empty($department->name) ? $department->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? $exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? $publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => $grade_change_detail,
                'Staff' => !empty($makeupExam->published_course->course_instructor_assignments) && !empty($makeupExam->published_course->course_instructor_assignments[0]->staff)
                    ? $makeupExam->published_course->course_instructor_assignments[0]->staff
                    : [],
                'ExamCourse' => !empty($makeupExam->published_course->course) ? $makeupExam->published_course->course : [],
                'ExamSection' => !empty($makeupExam->published_course->section) ? $makeupExam->published_course->section : [],
                'MakeupExam' => !empty($makeupExam) ? $makeupExam : [],
                'Section' => $section ? $section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            // Remove PublishedCourse from MakeupExam to avoid duplication
            if (!empty($summary_entry['MakeupExam']['published_course'])) {
                unset($summary_entry['MakeupExam']['published_course']);
            }

            $exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }


    public function getListOfMakeupGradeChangeByDepartmentForRegistrarApproval($department_ids = null, $college_ids = null, $program_id = null, $program_type_id = null)
    {
        // Validate ACY_BACK_FOR_GRADE_CHANGE_APPROVAL
        if (!defined('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL') || !is_int(ACY_BACK_FOR_GRADE_CHANGE_APPROVAL) || ACY_BACK_FOR_GRADE_CHANGE_APPROVAL < 0) {
            throw new \InvalidArgumentException('Invalid ACY_BACK_FOR_GRADE_CHANGE_APPROVAL constant');
        }

        // Get academic years using the component
        try {
            $currentAcademicYear = $this->academicYearComponent->currentAcademicYear();
            $currentYear = (int) explode('/', $currentAcademicYear)[0];
            $yearsToLook = $this->academicYearComponent->academicYearInArray(
                $currentYear - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
                $currentYear
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load academic years: ' . $e->getMessage());
        }

        // Normalize input parameters to arrays
        $department_ids = is_array($department_ids) ? $department_ids : (empty($department_ids) ? [] : [$department_ids]);
        $college_ids = is_array($college_ids) ? $college_ids : (empty($college_ids) ? [] : [$college_ids]);
        $program_id = is_array($program_id) ? $program_id : (empty($program_id) ? [] : [$program_id]);
        $program_type_id = is_array($program_type_id) ? $program_type_id : (empty($program_type_id) ? [] : [$program_type_id]);

        // Build conditions for PublishedCourses
        $conditions = [
            'academic_year' => $yearsToLook,
            'year_level_id' => function ($value) {
                return !is_null($value) && $value !== '' && $value != 0;
            },
        ];
        if (!empty($program_id) && !empty($program_type_id)) {
            $conditions['program_id'] = $program_id;
            $conditions['program_type_id'] = $program_type_id;
            if (!empty($department_ids)) {
                $conditions['department_id'] = $department_ids;
            } elseif (!empty($college_ids)) {
                $conditions['department_id'] = null;
                $conditions['college_id'] = $college_ids;
            } else {
                return ['summary' => [], 'count' => 0];
            }
        } else {
            if (!empty($department_ids)) {
                $conditions['department_id'] = $department_ids;
            } elseif (!empty($college_ids)) {
                $conditions['department_id'] = null;
                $conditions['college_id'] = $college_ids;
            } else {
                return ['summary' => [], 'count' => 0];
            }
        }

        // Main query
        $query = $this->find()
            ->select([
                'ExamGradeChanges.id',
                'ExamGradeChanges.exam_grade_id',
                'ExamGradeChanges.grade',
                'ExamGradeChanges.reason',
                'ExamGradeChanges.created',
                'ExamGradeChanges.makeup_exam_result',
                'ExamGradeChanges.initiated_by_department',
                'ExamGrades.id',
                'ExamGrades.course_registration_id',
                'ExamGrades.course_add_id',
                'ExamGrades.grade',
                'ExamGrades.created',
                'ExamGrades.department_approved_by',
                'ExamGrades.registrar_approved_by',
            ])
            ->where([
                'ExamGradeChanges.makeup_exam_result IS NOT NULL',
                'ExamGradeChanges.initiated_by_department' => 1,
                'ExamGradeChanges.department_approval' => 1,
                'ExamGradeChanges.registrar_approval IS NULL',
            ])
            ->contain([
                'ExamGrades' => [
                    'queryBuilder' => function ($q) {
                        return $q->select([
                            'ExamGrades.id',
                            'ExamGrades.course_registration_id',
                            'ExamGrades.course_add_id',
                            'ExamGrades.grade',
                            'ExamGrades.created',
                            'ExamGrades.department_approved_by',
                            'ExamGrades.registrar_approved_by',
                        ])->order(['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']);
                    },
                    'CourseRegistrations' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseRegistrations.id',
                            'CourseRegistrations.student_id',
                            'CourseRegistrations.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.first_name','Students.middle_name','Students.last_name',
                                        'Students.studentnumber'])->where(['Students.graduated' => 0]);

                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    $where = [
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ];
                                    if (!empty($conditions['department_id'])) {
                                        $where['PublishedCourses.department_id IN'] = $conditions['department_id'];
                                    } elseif (!empty($conditions['college_id'])) {
                                        $where['PublishedCourses.department_id IS NULL'] = null;
                                        $where['PublishedCourses.college_id IN'] = $conditions['college_id'];
                                    }
                                    if (!empty($conditions['program_id'])) {
                                        $where['PublishedCourses.program_id IN'] = $conditions['program_id'];
                                    }
                                    if (!empty($conditions['program_type_id'])) {
                                        $where['PublishedCourses.program_type_id IN'] = $conditions['program_type_id'];
                                    }
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where($where);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                    'CourseAdds' => function ($q) use ($conditions) {
                        return $q->select([
                            'CourseAdds.id',
                            'CourseAdds.student_id',
                            'CourseAdds.published_course_id',
                        ])->contain([
                            'Students' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['Students.id', 'Students.first_name','Students.middle_name','Students.last_name',
                                        'Students.studentnumber'])->where(['Students.graduated' => 0]);
                                },
                            ],
                            'PublishedCourses' => [
                                'queryBuilder' => function ($q) use ($conditions) {
                                    $where = [
                                        'PublishedCourses.academic_year IN' => $conditions['academic_year'],
                                        'PublishedCourses.year_level_id IS NOT NULL',
                                        'PublishedCourses.year_level_id !=' => '',
                                        'PublishedCourses.year_level_id !=' => 0,
                                    ];
                                    if (!empty($conditions['department_id'])) {
                                        $where['PublishedCourses.department_id IN'] = $conditions['department_id'];
                                    } elseif (!empty($conditions['college_id'])) {
                                        $where['PublishedCourses.department_id IS NULL'] = null;
                                        $where['PublishedCourses.college_id IN'] = $conditions['college_id'];
                                    }
                                    if (!empty($conditions['program_id'])) {
                                        $where['PublishedCourses.program_id IN'] = $conditions['program_id'];
                                    }
                                    if (!empty($conditions['program_type_id'])) {
                                        $where['PublishedCourses.program_type_id IN'] = $conditions['program_type_id'];
                                    }
                                    return $q->select([
                                        'PublishedCourses.id',
                                        'PublishedCourses.academic_year',
                                        'PublishedCourses.program_id',
                                        'PublishedCourses.program_type_id',
                                        'PublishedCourses.department_id',
                                        'PublishedCourses.college_id',
                                        'PublishedCourses.year_level_id',
                                        'PublishedCourses.course_id',
                                        'PublishedCourses.section_id',
                                    ])->where($where);
                                },
                                'Departments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id']);
                                    },
                                    'Colleges' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Colleges.id', 'Colleges.name']);
                                        },
                                    ],
                                ],
                                'Courses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                                    },
                                ],
                                'Sections' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['Sections.id', 'Sections.program_id', 'Sections.program_type_id']);
                                    },
                                    'Programs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Programs.id', 'Programs.name']);
                                        },
                                    ],
                                    'ProgramTypes' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                                        },
                                    ],
                                    'YearLevels' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['YearLevels.id']);
                                        },
                                    ],
                                ],
                                'CourseInstructorAssignments' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select([
                                            'CourseInstructorAssignments.id',
                                            'CourseInstructorAssignments.published_course_id',
                                            'CourseInstructorAssignments.staff_id',
                                            'CourseInstructorAssignments.isprimary'
                                        ])->where(['CourseInstructorAssignments.isprimary' => 1]);
                                    },
                                    'Staffs' => [
                                        'queryBuilder' => function ($q) {
                                            return $q->select(['Staffs.id']);
                                        },
                                        'Titles' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Titles.id']);
                                            },
                                        ],
                                        'Positions' => [
                                            'queryBuilder' => function ($q) {
                                                return $q->select(['Positions.id']);
                                            },
                                        ],
                                    ],
                                ],
                            ],
                        ])->leftJoinWith('PublishedCourses');
                    },
                ],
            ])
            ->order(['ExamGradeChanges.id' => 'DESC', 'ExamGradeChanges.created' => 'DESC']);

        $registrar_action_required_list = $query->all()->toArray();

        $exam_grade_changes_summary = [];
        $total_count = 0;

        foreach ($registrar_action_required_list as $grade_change_detail) {
            $type = null;
            $published_by_college_asked_by_department = false;
            $exam_grade = isset($grade_change_detail->exam_grade) ? $grade_change_detail->exam_grade : null;
            if (!$exam_grade) {
                continue;
            }
            if (!empty($exam_grade->course_registration) || !empty($exam_grade->course_registration_id)) {
                $type = 'course_registration';
            } elseif (!empty($exam_grade->course_add) || !empty($exam_grade->course_add_id)) {
                $type = 'course_add';
            }
            if (empty($type)) {
                continue;
            }

            $publishedCourse = !empty($exam_grade->$type->published_course) ? $exam_grade->$type->published_course : null;
            if ($type && (!empty($program_id) || !empty($program_type_id) || !empty($department_ids) || !empty($college_ids))) {
                if (!$this->isValidPublishedCourse($publishedCourse, $conditions, $published_by_college_asked_by_department)) {
                    if (!$published_by_college_asked_by_department || empty($department_ids)) {
                        continue;
                    }
                }
                if (!empty($exam_grade->$type->student->graduated)) {
                    continue;
                }
            } elseif (!$publishedCourse) {
                continue;
            }

            $department = !empty($publishedCourse->department) ? $publishedCourse->department : null;
            $college = !empty($department->college) ? $department->college : (!empty($publishedCourse->college) ? $publishedCourse->college : null);
            $section = !empty($publishedCourse->section) ? $publishedCourse->section : null;

            $collegeName = !empty($college->name) ? $college->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $departmentName = !empty($department->name) ? $department->name : (!empty($publishedCourse->program_id) && $publishedCourse->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');
            $programName = !empty($section->program->name) ? $section->program->name : '';
            $programTypeName = !empty($section->program_type->name) ? $section->program_type->name : '';

            $index = count($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName] ?? []);
            $summary_entry = [
                'Student' => !empty($exam_grade->$type->student) ? $exam_grade->$type->student : [],
                'Course' => !empty($publishedCourse->course) ? $publishedCourse->course : [],
                'latest_grade' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationLatestGrade(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddLatestGrade(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGradeChange' => $grade_change_detail,
                'Staff' => !empty($publishedCourse->course_instructor_assignments) && !empty($publishedCourse->course_instructor_assignments[0]->staff)
                    ? $publishedCourse->course_instructor_assignments[0]->staff
                    : [],
                'Section' => $section ? $section : [],
                'ExamGradeHistory' => $type === 'course_registration'
                    ? $this->ExamGrades->CourseRegistrations->getCourseRegistrationGradeHistory(!empty($exam_grade->course_registration_id) ? $exam_grade->course_registration_id : 0)
                    : $this->ExamGrades->CourseAdds->getCourseAddGradeHistory(!empty($exam_grade->course_add_id) ? $exam_grade->course_add_id : 0),
                'ExamGrade' => $this->ExamGrades->find()
                    ->select([
                        'ExamGrades.id',
                        'ExamGrades.course_registration_id',
                        'ExamGrades.course_add_id',
                        'ExamGrades.grade',
                        'ExamGrades.created',
                        'ExamGrades.department_approved_by',
                        'ExamGrades.registrar_approved_by',
                    ])
                    ->where([
                        $type === 'course_registration' ? 'ExamGrades.course_registration_id' : 'ExamGrades.course_add_id' => !empty($exam_grade->$type->id) ? $exam_grade->$type->id : 0,
                    ])
                    ->order(['ExamGrades.created' => 'DESC'])
                    ->all()
                    ->toArray(),
            ];

            $exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index] = $summary_entry;
            $total_count++;

            $usersTable = TableRegistry::getTableLocator()->get('Users');
            foreach ($exam_grade_changes_summary[$collegeName][$departmentName][$programName][$programTypeName][$index]['ExamGrade'] as &$exam_grade_detail) {
                $exam_grade_detail['department_approved_by_name'] = '';
                if (!empty($exam_grade_detail['department_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['department_approved_by']])
                        ->first();
                    $exam_grade_detail['department_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
                $exam_grade_detail['registrar_approved_by_name'] = '';
                if (!empty($exam_grade_detail['registrar_approved_by'])) {
                    $user = $usersTable->find()
                        ->select([
                            'first_name',
                            'middle_name',
                            'last_name',
                        ])
                        ->where(['Users.id' => $exam_grade_detail['registrar_approved_by']])
                        ->first();
                    $exam_grade_detail['registrar_approved_by_name'] = $user
                        ? trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name)
                        : '';
                }
            }
        }

        return ['summary' => $exam_grade_changes_summary, 'count' => $total_count];
    }

    public function applyManualNgConversion($exam_grade_changes = null, $minute_number = null, $login_user = null, $privilaged_registrar = [], $converted_by_full_name = '')
    {
        if (empty($exam_grade_changes)) {
            return false;
        }

        $new_exam_grade = [];
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        foreach ($exam_grade_changes as $exam_grade_change) {
            $grade_id = isset($exam_grade_change['grade_id']) ? $exam_grade_change['grade_id'] : (isset($exam_grade_change['id']) ? $exam_grade_change['id'] : null);
            if (empty($grade_id)) {
                continue;
            }

            $exam_grade_change_detail = $examGradesTable->find()
                ->select([
                    'ExamGrades.id',
                    'ExamGrades.course_registration_id',
                    'ExamGrades.course_add_id',
                ])
                ->contain([
                    'CourseRegistrations' => [
                        'queryBuilder' => function ($q) {
                            return $q->select([
                                'CourseRegistrations.id',
                                'CourseRegistrations.student_id',
                                'CourseRegistrations.published_course_id',
                            ])->contain([
                                'PublishedCourses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['PublishedCourses.id']);
                                    },
                                ],
                            ]);
                        },
                    ],
                    'CourseAdds' => [
                        'queryBuilder' => function ($q) {
                            return $q->select([
                                'CourseAdds.id',
                                'CourseAdds.student_id',
                                'CourseAdds.published_course_id',
                            ])->contain([
                                'PublishedCourses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['PublishedCourses.id']);
                                    },
                                ],
                            ]);
                        },
                    ],
                ])
                ->where(['ExamGrades.id' => $grade_id])
                ->order(['ExamGrades.id' => 'ASC'])
                ->first();

            if (!$exam_grade_change_detail) {
                continue;
            }

            $grade = [];
            $type = null;
            if (!empty($exam_grade_change_detail->course_registration) && is_numeric($exam_grade_change_detail->course_registration->id) && $exam_grade_change_detail->course_registration->id > 0) {
                $type = 'course_registration';
                $grade = $examGradesTable->getApprovedGrade($exam_grade_change_detail->course_registration->id, 1);
            } elseif (!empty($exam_grade_change_detail->course_add) && is_numeric($exam_grade_change_detail->course_add->id) && $exam_grade_change_detail->course_add->id > 0) {
                $type = 'course_add';
                $grade = $examGradesTable->getApprovedGrade($exam_grade_change_detail->course_add->id, 0);
            }

            if (!empty($grade['grade']) && strcasecmp($grade['grade'], 'NG') === 0 && isset($exam_grade_change['grade']) && !empty($exam_grade_change['grade']) && isset($exam_grade_change['grade_id']) && $exam_grade_change['grade_id'] == $exam_grade_change_detail->exam_grade_id) {
                $index = count($new_exam_grade);
                $new_exam_grade[$index] = [
                    'exam_grade_id' => $exam_grade_change['id'],
                    'minute_number' => $minute_number,
                    'grade' => $exam_grade_change['grade'],
                    'cheating' => isset($exam_grade_change['cheating']) ? $exam_grade_change['cheating'] : null,
                    'reason' => 'Manual NG Conversion',
                    'department_reason' => '',
                    'college_reason' => '',
                    'registrar_reason' => '',
                    'manual_ng_conversion' => 1,
                    'manual_ng_converted_by' => $login_user,
                    'department_approved_by' => $login_user,
                    'registrar_approved_by' => $login_user,
                    'college_approved_by' => $login_user,
                    'p_c_id' => ($type === 'course_registration' ? $exam_grade_change_detail->course_registration->published_course_id : $exam_grade_change_detail->course_add->published_course_id),
                    'stdnt_id' => ($type === 'course_registration' ? $exam_grade_change_detail->course_registration->student_id : $exam_grade_change_detail->course_add->student_id),
                ];
            }
        }

        if (!empty($new_exam_grade)) {
            $entities = $this->newEntities($new_exam_grade);
            if ($this->saveMany($entities, ['validate' => false])) {
                $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                foreach ($new_exam_grade as $value) {

                    if (!empty($value['stdnt_id'])) {
                        if ($studentExamStatusTable->regenerateAllStatusOfStudentByStudentId($value['stdnt_id']) == 3) {
                            $studentExamStatusTable->updateAcademicStatusByPublishedCourse($value['p_c_id']);
                        }
                    } else {
                        $studentExamStatusTable->updateAcademicStatusByPublishedCourse($value['p_c_id']);
                    }
                }
                $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                $autoMessageTable->sendNotificationOnAutoAndManualGradeChange($new_exam_grade, $privilaged_registrar, 1, $converted_by_full_name);
                return true;
            }
        }

        return false;
    }
    public function getGradeChangeStat($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null)
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

        $registrationOptions = [
            'conditions' => [
                'PublishedCourses.id IN' => $this->find()
                    ->select(['published_course_id'])
                    ->from('course_registrations as cr')
                    ->where([
                        'cr.academic_year' => $acadamic_year,
                        'cr.semester' => $semester
                    ])
            ],
            'contain' => [
                'Departments' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'CourseRegistrations' => ['ExamGrades' => ['ExamGradeChanges']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
            ]
        ];

        if (isset($acadamic_year) && isset($semester)) {
            $registrationOptions['conditions']['PublishedCourses.academic_year'] = $acadamic_year;
            $registrationOptions['conditions']['PublishedCourses.semester'] = $semester;
        }

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $registrationOptions['conditions']['PublishedCourses.program_type_id'] = $program_type_id;
        }

        if ($program_id != 0 && !empty($program_id)) {
            $registrationOptions['conditions']['PublishedCourses.program_id'] = $program_id;
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departmentList = TableRegistry::getTableLocator()->get('Departments')->find('list')
                    ->where(['college_id' => $college_id[1]])
                    ->select(['id'])
                    ->toArray();
                $registrationOptions['conditions']['PublishedCourses.given_by_department_id IN'] = $departmentList;
            } else {
                $registrationOptions['conditions']['PublishedCourses.given_by_department_id'] = $department_id;
            }
        }

        $registration = $publishedCoursesTable->find('all', $registrationOptions)->toArray();

        return $registration;
    }

    public function getInstGradeChangeStat($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null)
    {
        $courseInstructorAssignmentsTable = TableRegistry::getTableLocator()->get('CourseInstructorAssignments');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');

        $query_conditions = [];

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $department_ids = $departmentsTable->find('list')
                    ->where(['college_id' => $college_id[1], 'active' => 1])
                    ->select(['id'])
                    ->toArray();
                $query_conditions[] = 'ps.department_id IN (' . implode(',', $department_ids) . ')';
            } else {
                $query_conditions[] = 'ps.department_id = ' . $department_id;
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $query_conditions[] = 'cr.academic_year = "' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query_conditions[] = 'cr.semester = "' . $semester . '"';
        }

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            $query_conditions[] = count($program_ids) > 1 ? 'ps.program_id = ' . $program_ids[1] : 'ps.program_id = ' . $program_id;
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            $query_conditions[] = count($program_type_ids) > 1 ? 'ps.program_type_id = ' . $program_type_ids[1] : 'ps.program_type_id = ' . $program_type_id;
        }

        $query = "SELECT eg.id, ps.id, ps.course_id FROM `exam_grade_changes` AS ch
            INNER JOIN exam_grades AS eg ON ch.exam_grade_id = eg.id
            INNER JOIN course_registrations AS cr ON cr.id = eg.course_registration_id
            INNER JOIN published_courses AS ps ON cr.published_course_id = ps.id
            WHERE ch.registrar_approval = 1 AND ps.id IN (
                SELECT published_course_id FROM course_instructor_assignments
            )" . (!empty($query_conditions) ? ' AND ' . implode(' AND ', $query_conditions) : '');

        $gradeChangeStatResult = $this->getDataSource()->fetchAll($query);

        $published_ids = [];
        if (!empty($gradeChangeStatResult)) {
            foreach ($gradeChangeStatResult as $value) {
                $published_ids[] = $value['ps']['id'];
            }

            $options = [
                'conditions' => [
                    'CourseInstructorAssignments.published_course_id IN' => $published_ids,
                    'CourseInstructorAssignments.academic_year' => $acadamic_year,
                    'CourseInstructorAssignments.semester' => $semester
                ],
                'contain' => [
                    'PublishedCourses' => [
                        'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'credit']],
                        'Sections' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                    ],
                    'Staffs' => ['Positions', 'Titles', 'Departments', 'Colleges']
                ],
                'order' => ['CourseInstructorAssignments.academic_year' => 'DESC']
            ];

            $instructors = $courseInstructorAssignmentsTable->find('all', $options)->toArray();

            $formattedInstructorList = [];
            if (!empty($instructors)) {
                foreach ($instructors as $inst) {
                    $inst['PublishedCourse']['numberofgradechange'] = $this->getNumberofGradeChange($inst['PublishedCourse']['id']);
                    $formattedInstructorList[$inst['Staff']['Department']['name'] . '~' . $inst['PublishedCourse']['Program']['name'] . '~' . $inst['PublishedCourse']['ProgramType']['name']][$inst['Staff']['id']][] = $inst;
                }
            }

            return $formattedInstructorList;
        }

        return [];
    }

    public function getNumberofGradeChange($publishedCourseId)
    {
        if (empty($publishedCourseId)) {
            return 0;
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        // Get CourseRegistration IDs
        $registeredLists = $courseRegistrationsTable->find('list', [
            'conditions' => ['CourseRegistrations.published_course_id' => $publishedCourseId],
            'keyField' => 'id',
            'valueField' => 'id'
        ])->toArray();

        // Get CourseAdd IDs
        $addedList = $courseAddsTable->find('list', [
            'conditions' => ['CourseAdds.published_course_id' => $publishedCourseId],
            'keyField' => 'id',
            'valueField' => 'id'
        ])->toArray();

        if (empty($registeredLists) && empty($addedList)) {
            return 0;
        }

        $query = $this->find()
            ->select(['ExamGradeChanges.id'])
            ->where(['ExamGradeChanges.registrar_approval' => 1]);

        if (!empty($registeredLists) && !empty($addedList)) {
            $query->innerJoinWith('ExamGrades', function ($q) use ($registeredLists, $addedList) {
                return $q->where([
                    'OR' => [
                        'ExamGrades.course_registration_id IN' => array_values($registeredLists),
                        'ExamGrades.course_add_id IN' => array_values($addedList),
                    ]
                ]);
            });
        } elseif (!empty($registeredLists)) {
            $query->innerJoinWith('ExamGrades', function ($q) use ($registeredLists) {
                return $q->where(['ExamGrades.course_registration_id IN' => array_values($registeredLists)]);
            });
        } else {
            $query->innerJoinWith('ExamGrades', function ($q) use ($addedList) {
                return $q->where(['ExamGrades.course_add_id IN' => array_values($addedList)]);
            });
        }

        return $query->count();
    }

    public function applyManualFxConversion($exam_grade_changes = null, $minute_number = null, $login_user = null, $privilaged_registrar = [])
    {
        if (empty($exam_grade_changes)) {
            return false;
        }

        $new_exam_grade = [];
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        foreach ($exam_grade_changes as $exam_grade_change) {
            if (empty($exam_grade_change['id'])) {
                continue;
            }

            $exam_grade_change_detail = $examGradesTable->find()
                ->select([
                    'ExamGrades.id',
                    'ExamGrades.course_registration_id',
                    'ExamGrades.course_add_id',
                ])
                ->contain([
                    'CourseRegistrations' => [
                        'queryBuilder' => function ($q) {
                            return $q->select([
                                'CourseRegistrations.id',
                                'CourseRegistrations.published_course_id',
                            ])->contain([
                                'PublishedCourses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['PublishedCourses.id']);
                                    },
                                ],
                            ]);
                        },
                    ],
                    'CourseAdds' => [
                        'queryBuilder' => function ($q) {
                            return $q->select([
                                'CourseAdds.id',
                                'CourseAdds.published_course_id',
                            ])->contain([
                                'PublishedCourses' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['PublishedCourses.id']);
                                    },
                                ],
                            ]);
                        },
                    ],
                ])
                ->where(['ExamGrades.id' => $exam_grade_change['id']])
                ->first();

            if (!$exam_grade_change_detail) {
                continue;
            }

            $grade = [];
            $type = null;
            if (!empty($exam_grade_change_detail->course_registration) && is_numeric($exam_grade_change_detail->course_registration->id) && $exam_grade_change_detail->course_registration->id > 0) {
                $type = 'course_registration';
                $grade = $examGradesTable->getApprovedGrade($exam_grade_change_detail->course_registration->id, 1);
            } elseif (!empty($exam_grade_change_detail->course_add) && is_numeric($exam_grade_change_detail->course_add->id) && $exam_grade_change_detail->course_add->id > 0) {
                $type = 'course_add';
                $grade = $examGradesTable->getApprovedGrade($exam_grade_change_detail->course_add->id, 0);
            }

            if (!empty($grade['grade']) && strcasecmp($grade['grade'], 'Fx') === 0 && isset($exam_grade_change['grade']) && !empty($exam_grade_change['grade'])) {
                $index = count($new_exam_grade);
                $new_exam_grade[$index] = [
                    'exam_grade_id' => $exam_grade_change['id'],
                    'minute_number' => $minute_number,
                    'grade' => $exam_grade_change['grade'],
                    'manual_ng_conversion' => 1,
                    'registrar_approval' => 1,
                    'college_approval' => 1,
                    'department_approval' => 1,
                    'manual_ng_converted_by' => $login_user,
                    'reason' => 'Manual Fx Conversion',
                    'department_reason' => '',
                    'college_reason' => '',
                    'registrar_reason' => '',
                    'department_approved_by' => $login_user,
                    'registrar_approved_by' => $login_user,
                    'college_approved_by' => $login_user,
                    'p_c_id' => isset($exam_grade_change['p_c_id']) && !empty($exam_grade_change['p_c_id']) && $exam_grade_change['p_c_id'] > 0
                        ? $exam_grade_change['p_c_id']
                        : ($type === 'course_registration'
                            ? $exam_grade_change_detail->course_registration->published_course_id
                            : $exam_grade_change_detail->course_add->published_course_id),
                ];
            }
        }

        if (!empty($new_exam_grade)) {
            $entities = $this->newEntities($new_exam_grade);
            if ($this->saveMany($entities, ['validate' => false])) {
                $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                /* foreach ($new_exam_grade as $value) {
                    if (strcasecmp($value['grade'], 'I') == 0) {
                        $studentExamStatusTable->updateAcademicStatusByPublishedCourse($value['p_c_id']);
                    }
                } */
                $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                $autoMessageTable->sendNotificationOnAutoAndManualGradeChange($new_exam_grade, $privilaged_registrar);
                return true;
            }
        }

        return false;
    }

    public function getListOfGradeAutomaticallyConverted($academicyear, $semester, $department_id, $program_id, $program_type_id, $gradeConverted, $type = 0)
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $conditions = [
            'PublishedCourses.semester' => $semester,
            'PublishedCourses.academic_year' => $academicyear,
            'PublishedCourses.program_id' => $program_id,
            'PublishedCourses.program_type_id' => $program_type_id,
        ];
        if ($type == 1) {
            $conditions['PublishedCourses.college_id'] = $department_id;
        } else {
            $conditions['PublishedCourses.department_id'] = $department_id;
        }

        $publishedCourseLists = $publishedCoursesTable->find()
            ->select([
                'PublishedCourses.id',
                'PublishedCourses.semester',
                'PublishedCourses.academic_year',
                'PublishedCourses.program_id',
                'PublishedCourses.program_type_id',
                'PublishedCourses.department_id',
                'PublishedCourses.college_id',
            ])
            ->contain([
                'Courses' => function ($q) {
                    return $q->select(['Courses.id', 'Courses.course_title', 'Courses.course_code']);
                },
                'Programs' => function ($q) {
                    return $q->select(['Programs.id', 'Programs.name']);
                },
                'ProgramTypes' => function ($q) {
                    return $q->select(['ProgramTypes.id', 'ProgramTypes.name']);
                },
                'Departments' => function ($q) {
                    return $q->select(['Departments.id', 'Departments.name', 'Departments.college_id'])
                        ->contain([
                            'Colleges' => function ($q) {
                                return $q->select(['Colleges.id', 'Colleges.name']);
                            },
                        ]);
                },
                'CourseRegistrations' => function ($q) {
                    return $q->select(['CourseRegistrations.id', 'CourseRegistrations.student_id'])
                        ->contain([
                            'Students' => function ($q) {
                                return $q->select(['Students.id']);
                            },
                        ]);
                },
                'CourseAdds' => function ($q) {
                    return $q->select(['CourseAdds.id', 'CourseAdds.student_id'])
                        ->contain([
                            'Students' => function ($q) {
                                return $q->select(['Students.id']);
                            },
                        ]);
                },
            ])
            ->where($conditions)
            ->all()
            ->toArray();

        $autoConvertedGradeLists = [];

        if (!empty($publishedCourseLists)) {
            foreach ($publishedCourseLists as $pv) {
                // Check for CourseRegistration auto conversion
                foreach ($pv->course_registrations as $crv) {
                    $autoChange = $this->find()
                        ->select([
                            'ExamGradeChanges.id',
                            'ExamGradeChanges.exam_grade_id',
                            'ExamGradeChanges.grade',
                            'ExamGradeChanges.reason',
                            'ExamGradeChanges.created',
                            'ExamGradeChanges.auto_ng_conversion',
                        ])
                        ->contain([
                            'ExamGrades' => function ($q) use ($gradeConverted, $crv) {
                                return $q->select([
                                    'ExamGrades.id',
                                    'ExamGrades.course_registration_id',
                                    'ExamGrades.grade',
                                ])->where([
                                    'ExamGrades.course_registration_id' => $crv->id,
                                    'ExamGrades.grade' => $gradeConverted,
                                ]);
                            },
                        ])
                        ->where(['ExamGradeChanges.auto_ng_conversion' => 1])
                        ->first();

                    if (!empty($autoChange) && !empty($autoChange->exam_grade)) {
                        $autoChange->course = $pv->course;
                        $autoChange->student = $crv->student;
                        $key = $pv->department->college->name . '~' . $pv->department->name . '~' . $pv->program->name . '~' . $pv->program_type->name;
                        $autoConvertedGradeLists[$key][] = (array)$autoChange;
                    }
                }

                // Check for CourseAdd auto conversion
                foreach ($pv->course_adds as $cadv) {
                    $autoChange = $this->find()
                        ->select([
                            'ExamGradeChanges.id',
                            'ExamGradeChanges.exam_grade_id',
                            'ExamGradeChanges.grade',
                            'ExamGradeChanges.reason',
                            'ExamGradeChanges.created',
                            'ExamGradeChanges.auto_ng_conversion',
                        ])
                        ->contain([
                            'ExamGrades' => function ($q) use ($gradeConverted, $cadv) {
                                return $q->select([
                                    'ExamGrades.id',
                                    'ExamGrades.course_add_id',
                                    'ExamGrades.grade',
                                ])->where([
                                    'ExamGrades.course_add_id' => $cadv->id,
                                    'ExamGrades.grade' => $gradeConverted,
                                ]);
                            },
                        ])
                        ->where(['ExamGradeChanges.auto_ng_conversion' => 1])
                        ->first();

                    if (!empty($autoChange) && !empty($autoChange->exam_grade)) {
                        $autoChange->course = $pv->course;
                        $autoChange->student = $cadv->student;
                        $key = $pv->department->college->name . '~' . $pv->department->name . '~' . $pv->program->name . '~' . $pv->program_type->name;
                        $autoConvertedGradeLists[$key][] = (array)$autoChange;
                    }
                }
            }
        }

        return $autoConvertedGradeLists;
    }

    public function possibleStudentsForSup($section_id = "")
    {
        if (empty($section_id)) {
            return [];
        }

        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $students = $sectionsTable->find()
            ->select(['Sections.id', 'Sections.department_id', 'Sections.college_id', 'Sections.program_id', 'Sections.academicyear'])
            ->contain([
                'Students' => function ($q) {
                    return $q->select([
                        'Students.id',
                        'Students.first_name',
                        'Students.middle_name',
                        'Students.last_name',
                        'Students.studentnumber',
                        'Students.graduated',
                    ])
                        ->where(['Students.graduated' => 0])
                        ->order(['Students.first_name' => 'ASC', 'Students.middle_name' => 'ASC', 'Students.last_name' => 'ASC']);
                },
            ])
            ->where(['Sections.id' => $section_id])
            ->first();

        $section_ids = ['0', '0'];
        if (!empty($students) && !empty($students->department_id) && $students->department_id > 0) {
            $section_ids = $sectionsTable->find('list', [
                'conditions' => [
                    'Sections.department_id' => $students->department_id,
                    'Sections.program_id' => $students->program_id,
                ],
                'keyField' => 'id',
                'valueField' => 'id'
            ])->toArray();
        } elseif (!empty($students) && !empty($students->college_id) && empty($students->department_id)) {
            $section_ids = $sectionsTable->find('list', [
                'conditions' => [
                    'Sections.college_id' => $students->college_id,
                    'Sections.program_id' => $students->program_id,
                    'Sections.academicyear' => $students->academicyear,
                ],
                'keyField' => 'id',
                'valueField' => 'id'
            ])->toArray();
        }

        $possibleAllowedRepetitionGrade = [];
        if (!empty($students) && $students->program_id == PROGRAM_POST_GRADUATE) {
            $possibleAllowedRepetitionGrade = [
                'C' => 'C',
                'C+' => 'C+',
                'D' => 'D',
                'F' => 'F',
                // 'NG' => 'NG',
                'FAIL' => 'FAIL',
                'I' => 'I',
            ];
        } else {
            $possibleAllowedRepetitionGrade = [
                'C-' => 'C-',
                'D' => 'D',
                'F' => 'F',
                // 'NG' => 'NG',
                'FAIL' => 'FAIL',
                'I' => 'I',
            ];
        }

        $student_list = [];
        if (!empty($students) && !empty($students->students)) {
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');

            foreach ($students->students as $student) {
                if (!$student->graduated) {
                    $courseRegistered = $courseRegistrationsTable->find('list', [
                        'conditions' => [
                            'CourseRegistrations.student_id' => $student->id,
                            'CourseRegistrations.section_id IN' => array_values($section_ids),
                        ],
                        'keyField' => 'id',
                        'valueField' => 'id'
                    ])->toArray();

                    $graded = $examGradesTable->getApprovedGradeForMakeUpExam($courseRegistered, 1);

                    if (!$graduateListsTable->isGraduated($student->id) && !empty($graded)) {
                        if (!empty($graded) && ((isset($graded['allow_repetition']) && $graded['allow_repetition']) || (!empty($possibleAllowedRepetitionGrade) && isset($graded['grade']) && array_key_exists($graded['grade'], $possibleAllowedRepetitionGrade)))) {
                            $student_list[$student->id] = $student->full_name_studentnumber;
                        }
                    }
                }
            }
        }

        return $student_list;
    }

}
