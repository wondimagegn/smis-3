<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class MakeupExamsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('makeup_exams');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // Associations
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('CourseAdds', [
            'foreignKey' => 'course_add_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ExamResults', [
            'foreignKey' => 'makeup_exam_id',
            'dependent' => false,
        ]);
        $this->hasMany('ExamGrades', [
            'foreignKey' => 'makeup_exam_id',
            'dependent' => false,
        ]);
        $this->hasMany('ExamGradeChanges', [
            'foreignKey' => 'makeup_exam_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('minute_number', 'Please provide minute number.')
            ->requirePresence('minute_number', 'create')
            ->notEmptyString('published_course_id', 'Please select course.')
            ->requirePresence('published_course_id', 'create')
            ->numeric('published_course_id')
            ->notEmptyString('student_id', 'Please select the student who is taking the makeup exam.')
            ->requirePresence('student_id', 'create')
            ->numeric('student_id');

        return $validator;
    }

    public function getmakeupExams($department_id = "", $acadamic_year = "", $program_id = "", $program_type_id = "", $semester = "")
    {
        $makeup_exams_formated = [];

        if (!empty($department_id) && !empty($acadamic_year) && !empty($program_id)) {
            $conditions = [
                'PublishedCourses.department_id' => $department_id,
                'PublishedCourses.academic_year' => $acadamic_year,
                'PublishedCourses.program_id' => $program_id,
            ];

            if (!empty($program_type_id)) {
                $conditions['PublishedCourses.program_type_id'] = $program_type_id;
            }

            if (!empty($semester)) {
                $conditions['PublishedCourses.semester'] = $semester;
            }

            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');

            // Makeup exams assigned to the instructor
            $all_makeup_exams = $publishedCoursesTable->find()
                ->where($conditions)
                ->contain([
                    'Sections' => [
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit']],
                    ],
                    'Courses',
                    'MakeupExams' => [
                        'ExamGradeChanges',
                        'ExamResults',
                        'ExamGrades',
                        'CourseRegistrations' => [
                            'PublishedCourses' => ['Courses'],
                            'Students' => [
                                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit']],
                            ],
                        ],
                        'CourseAdds' => [
                            'PublishedCourses' => ['Courses'],
                            'Students' => [
                                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit']],
                            ],
                        ],
                    ],
                ])
                ->toArray();

            $count = 0;

            if (!empty($all_makeup_exams)) {
                foreach ($all_makeup_exams as $makeup_exams) {
                    if (!empty($makeup_exams->makeup_exams)) {
                        foreach ($makeup_exams->makeup_exams as $makeup_exam) {
                            if (!empty($makeup_exam->course_registration) && !empty($makeup_exam->course_registration->student->id)) {
                                $makeup_exams_formated[$count] = [
                                    'student_name' => $makeup_exam->course_registration->student->full_name,
                                    'student_id' => $makeup_exam->course_registration->student->studentnumber,
                                    'exam_for' => $makeup_exam->course_registration->published_course->course->course_code_title . ' (Course Registration)',
                                    'gender' => $makeup_exam->course_registration->student->gender,
                                    'graduated' => $makeup_exam->course_registration->student->graduated,
                                    'student_attached_curriculum' => !empty($makeup_exam->course_registration->student->curriculum)
                                        ? $makeup_exam->course_registration->student->curriculum->name . ' - ' .
                                        $makeup_exam->course_registration->student->curriculum->year_introduced . ' (' .
                                        (stripos($makeup_exam->course_registration->student->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit') . ')'
                                        : '',
                                ];
                            } elseif (!empty($makeup_exam->course_add) && !empty($makeup_exam->course_add->student->id)) {
                                $makeup_exams_formated[$count] = [
                                    'student_name' => $makeup_exam->course_add->student->full_name,
                                    'student_id' => $makeup_exam->course_add->student->studentnumber,
                                    'exam_for' => $makeup_exam->course_add->published_course->course->course_code_title . ' (Course Add)',
                                    'gender' => $makeup_exam->course_add->student->gender,
                                    'graduated' => $makeup_exam->course_add->student->graduated,
                                    'student_attached_curriculum' => !empty($makeup_exam->course_add->student->curriculum)
                                        ? $makeup_exam->course_add->student->curriculum->name . ' - ' .
                                        $makeup_exam->course_add->student->curriculum->year_introduced . ' (' .
                                        (stripos($makeup_exam->course_add->student->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit') . ')'
                                        : '',
                                ];
                            } else {
                                continue;
                            }

                            $makeup_exams_formated[$count]['minute_number'] = $makeup_exam->minute_number;
                            $makeup_exams_formated[$count]['taken_exam'] = $makeup_exams->course->course_code_title;
                            $makeup_exams_formated[$count]['section_exam_taken'] = $makeup_exams->section->name . ' (' .
                                (!empty($makeup_exams->section->year_level->name)
                                    ? $makeup_exams->section->year_level->name
                                    : ($makeup_exams->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' .
                                $makeup_exams->section->academicyear . ') ' .
                                ($makeup_exams->section->archive ? '<span class="rejected"> (Archived) </span>' : '<span class="accepted"> (Active) </span>');
                            $makeup_exams_formated[$count]['section_curriculum'] = !empty($makeup_exams->section->curriculum)
                                ? $makeup_exams->section->curriculum->name . ' - ' .
                                $makeup_exams->section->curriculum->year_introduced . ' (' .
                                (stripos($makeup_exams->section->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit') . ')'
                                : '';
                            $makeup_exams_formated[$count]['created'] = $makeup_exam->created;
                            $makeup_exams_formated[$count]['modified'] = $makeup_exam->modified;
                            $makeup_exams_formated[$count]['ExamGrade'] = $makeup_exam->exam_grades;
                            $makeup_exams_formated[$count]['ExamResult'] = $makeup_exam->exam_results;

                            if (!empty($makeup_exam->exam_grade_changes)) {
                                $makeup_exams_formated[$count]['ExamGradeChange'] = $makeup_exam->exam_grade_changes[0];
                                $status = $examGradeChangesTable->examGradeChangeStateDescription($makeup_exam->exam_grade_changes[0]);
                                $makeup_exams_formated[$count]['ExamGradeChange']['state'] = $status['state'];
                                $makeup_exams_formated[$count]['ExamGradeChange']['description'] = $status['description'];
                            }

                            $makeup_exams_formated[$count]['id'] = $makeup_exam->id;
                            $count++;
                        }
                    }
                }
            }

            // Makeup exams directly submitted by the department
            $all_makeup_exams = $publishedCoursesTable->find()
                ->where($conditions)
                ->contain([
                    'Courses',
                    'CourseRegistrations' => [
                        'Students',
                        'ExamGrades' => [
                            'ExamGradeChanges' => [
                                'conditions' => ['ExamGradeChanges.initiated_by_department' => 1],
                            ],
                        ],
                    ],
                    'CourseAdds' => [
                        'Students',
                        'ExamGrades' => [
                            'ExamGradeChanges' => [
                                'conditions' => ['ExamGradeChanges.initiated_by_department' => 1],
                            ],
                        ],
                    ],
                ])
                ->toArray();

            if (!empty($all_makeup_exams)) {
                foreach ($all_makeup_exams as $published_course) {
                    if (!empty($published_course->course_registrations)) {
                        foreach ($published_course->course_registrations as $course_registration) {
                            if (!empty($course_registration->student->id) && !empty($course_registration->exam_grades) && !empty($course_registration->exam_grades[0]->exam_grade_changes)) {
                                foreach ($course_registration->exam_grades[0]->exam_grade_changes as $exam_grade_change) {
                                    $makeup_exams_formated[$count] = [
                                        'student_name' => $course_registration->student->full_name,
                                        'student_id' => $course_registration->student->studentnumber,
                                        'exam_for' => $published_course->course->course_code_title . ' (Course Registration)',
                                        'gender' => $course_registration->student->gender,
                                        'graduated' => $course_registration->student->graduated,
                                        'taken_exam' => null,
                                        'section_exam_taken' => null,
                                        'ExamGradeChange' => $exam_grade_change->toArray(),
                                    ];
                                    $status = $examGradeChangesTable->examGradeChangeStateDescription($exam_grade_change);
                                    $makeup_exams_formated[$count]['ExamGradeChange']['state'] = $status['state'];
                                    $makeup_exams_formated[$count]['ExamGradeChange']['description'] = $status['description'];
                                    $count++;
                                }
                            }
                        }
                    } elseif (!empty($published_course->course_adds)) {
                        foreach ($published_course->course_adds as $course_add) {
                            if (!empty($course_add->student->id) && !empty($course_add->exam_grades) && !empty($course_add->exam_grades[0]->exam_grade_changes)) {
                                foreach ($course_add->exam_grades[0]->exam_grade_changes as $exam_grade_change) {
                                    $makeup_exams_formated[$count] = [
                                        'student_name' => $course_add->student->full_name,
                                        'student_id' => $course_add->student->studentnumber,
                                        'exam_for' => $published_course->course->course_code_title . ' (Course Add)',
                                        'gender' => $course_add->student->gender,
                                        'graduated' => $course_add->student->graduated,
                                        'taken_exam' => null,
                                        'section_exam_taken' => null,
                                        'ExamGradeChange' => $exam_grade_change->toArray(),
                                    ];
                                    $status = $examGradeChangesTable->examGradeChangeStateDescription($exam_grade_change);
                                    $makeup_exams_formated[$count]['ExamGradeChange']['state'] = $status['state'];
                                    $makeup_exams_formated[$count]['ExamGradeChange']['description'] = $status['description'];
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $makeup_exams_formated;
    }

    public function BACKUP_getmakeupExams($department_id = "", $acadamic_year = "", $program_id = "", $program_type_id = "0", $semester = "0")
    {
        $makeup_exams_formated = [];

        if (!empty($department_id) && !empty($acadamic_year) && !empty($program_id)) {
            $conditions = [
                'PublishedCourses.department_id' => $department_id,
                'PublishedCourses.academic_year' => $acadamic_year,
                'PublishedCourses.program_id' => $program_id,
            ];

            if (!empty($program_type_id)) {
                $conditions['PublishedCourses.program_type_id'] = $program_type_id;
            }

            if (!empty($semester)) {
                $conditions['PublishedCourses.semester'] = $semester;
            }

            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

            $all_makeup_exams = $publishedCoursesTable->find()
                ->where($conditions)
                ->contain([
                    'Sections',
                    'Courses',
                    'MakeupExams' => [
                        'ExamResults',
                        'ExamGrades',
                        'CourseRegistrations' => [
                            'PublishedCourses' => ['Courses'],
                            'Students',
                        ],
                        'CourseAdds' => [
                            'PublishedCourses' => ['Courses'],
                            'Students',
                        ],
                    ],
                ])
                ->toArray();

            $count = 0;

            if (!empty($all_makeup_exams)) {
                foreach ($all_makeup_exams as $makeup_exams) {
                    if (!empty($makeup_exams->makeup_exams)) {
                        foreach ($makeup_exams->makeup_exams as $makeup_exam) {
                            if (!empty($makeup_exam->course_registration)) {
                                $makeup_exams_formated[$count] = [
                                    'student_name' => $makeup_exam->course_registration->student->full_name,
                                    'student_id' => $makeup_exam->course_registration->student->studentnumber,
                                    'exam_for' => $makeup_exam->course_registration->published_course->course->course_code_title . ' (Course Registration)',
                                ];
                            } else {
                                $makeup_exams_formated[$count] = [
                                    'student_name' => $makeup_exam->course_add->student->full_name,
                                    'student_id' => $makeup_exam->course_add->student->studentnumber,
                                    'exam_for' => $makeup_exam->course_add->published_course->course->course_code_title . ' (Course Add)',
                                ];
                            }

                            $makeup_exams_formated[$count]['minute_number'] = $makeup_exam->minute_number;
                            $makeup_exams_formated[$count]['taken_exam'] = $makeup_exams->course->course_code_title;
                            $makeup_exams_formated[$count]['section_exam_taken'] = $makeup_exams->section->name;
                            $makeup_exams_formated[$count]['created'] = $makeup_exam->created;
                            $makeup_exams_formated[$count]['modified'] = $makeup_exam->modified;
                            $makeup_exams_formated[$count]['ExamGrade'] = $makeup_exam->exam_grades;
                            $makeup_exams_formated[$count]['ExamResult'] = $makeup_exam->exam_results;
                            $makeup_exams_formated[$count]['id'] = $makeup_exam->id;
                            $count++;
                        }
                    }
                }
            }
        }

        return $makeup_exams_formated;
    }

    public function canItBeDeleted($id = "")
    {
        if (!empty($id)) {
            $result_and_grade = $this->find()
                ->where(['MakeupExams.id' => $id])
                ->contain(['ExamResults', 'ExamGrades', 'ExamGradeChanges'])
                ->first();

            if (
                !empty($result_and_grade->exam_results) ||
                !empty($result_and_grade->exam_grades) ||
                !empty($result_and_grade->exam_grade_changes)
            ) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function makeUpExamApplied($student_id, $published_course_id, $reg_add_id, $reg = 0)
    {
        $conditions = ['MakeupExams.student_id' => $student_id];

        if ($reg == 1) {
            $conditions['MakeupExams.course_registration_id'] = $reg_add_id;
        } else {
            $conditions['MakeupExams.course_add_id'] = $reg_add_id;
        }

        $result = $this->find()
            ->where($conditions)
            ->select(['id'])
            ->first();

        return !empty($result->id) ? $result->id : 0;
    }

    public function assignedMakeup($published_course_id)
    {
        if (!empty($published_course_id)) {
            return $this->find()
                ->where(['MakeupExams.published_course_id' => $published_course_id])
                ->count();
        }
        return 0;
    }
}
