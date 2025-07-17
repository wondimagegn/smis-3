<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class CourseInstructorAssignmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('course_instructor_assignments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id'
        ]);

        $this->belongsTo('CourseSplitSections', [
            'foreignKey' => 'course_split_section_id'
        ]);

        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id'
        ]);

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id'
        ]);

        $this->hasMany('ExamGrades', [
            'foreignKey' => 'course_instructor_assignment_id',
            'dependent' => false
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('academic_year', 'Academic year is required')
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('semester', 'Semester is required')
            ->requirePresence('semester', 'create')
            ->integer('section_id', 'Section ID must be numeric')
            ->requirePresence('section_id', 'create')
            ->integer('staff_id', 'Staff ID must be numeric')
            ->requirePresence('staff_id', 'create')
            ->integer('published_course_id', 'Published course ID must be numeric')
            ->requirePresence('published_course_id', 'create')
            ->notEmptyString('type', 'Type is required')
            ->requirePresence('type', 'create');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('section_id', 'Sections'), [
            'errorField' => 'section_id',
            'message' => 'The specified section does not exist.'
        ]);

        $rules->add($rules->existsIn('course_split_section_id', 'CourseSplitSections'), [
            'errorField' => 'course_split_section_id',
            'message' => 'The specified course split section does not exist.'
        ]);

        $rules->add($rules->existsIn('staff_id', 'Staffs'), [
            'errorField' => 'staff_id',
            'message' => 'The specified staff does not exist.'
        ]);

        $rules->add($rules->existsIn('published_course_id', 'PublishedCourses'), [
            'errorField' => 'published_course_id',
            'message' => 'The specified published course does not exist.'
        ]);

        return $rules;
    }



    public function listOfSectionInstructorAssigned($academic_year = null, $semester = null, $instructor_id = null): array
    {
        $published_course_details = $this->find()
            ->select(['CourseInstructorAssignments.id'])
            ->where([
                'CourseInstructorAssignments.staff_id' => $instructor_id,
                'CourseInstructorAssignments.academic_year' => $academic_year,
                'CourseInstructorAssignments.semester' => $semester,
                'CourseInstructorAssignments.isprimary' => 1
            ])
            ->contain(['PublishedCourses' => ['fields' => ['section_id']]])
            ->toArray();

        $sections_formated = [];

        if (!empty($published_course_details)) {
            foreach ($published_course_details as $published_course_detail) {
                $section_detail = $this->Sections->find()
                    ->where(['Sections.id' => $published_course_detail->published_course->section_id])
                    ->contain(['Departments', 'Programs', 'ProgramTypes'])
                    ->first();

                if (!$section_detail) {
                    debug($section_detail);
                    continue;
                }

                $section_name = trim(preg_replace('/\s+/', ' ', $section_detail->name));
                $section_label = $section_name . ' (' . $section_detail->program->name . ', ' . $section_detail->program_type->name;

                if (!empty($section_detail->department->name)) {
                    $section_label .= ' - ' . $section_detail->department->name . ')';
                } else {
                    $section_label .= ($section_detail->program_id == PROGRAM_REMEDIAL ? ' - Remedial)' : ' - Pre/Freshman)');
                }

                $sections_formated[$section_detail->id] = $section_label;
            }
        }

        return $sections_formated;
    }

    public function listOfDepartmentSections($department_id = null, $academic_year = null, $semester = null, $program_id = null, $program_type_id = null, $yearLevel = null): array
    {
        $conditions = [
            'OR' => [
                'PublishedCourses.department_id' => $department_id,
                'PublishedCourses.given_by_department_id' => $department_id
            ],
            'PublishedCourses.program_id' => $program_id,
            'PublishedCourses.program_type_id' => $program_type_id,
            'PublishedCourses.academic_year' => $academic_year,
            'PublishedCourses.semester' => $semester,
            'PublishedCourses.drop' => 0
        ];

        if (!empty($yearLevel)) {
            $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
            $yearLevelRecord = $yearLevelsTable->find()
                ->select(['id'])
                ->where([
                    'YearLevels.department_id' => $department_id,
                    'YearLevels.name' => $yearLevel
                ])
                ->first();

            if ($yearLevelRecord) {
                $conditions['PublishedCourses.year_level_id'] = $yearLevelRecord->id;
            }
        }

        $published_courses_by_section = $this->PublishedCourses->find()
            ->where($conditions)
            ->contain([
                'Departments',
                'Colleges',
                'Courses',
                'GivenByDepartments',
                'YearLevels' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                'Programs' => ['fields' => ['id', 'name', 'shortname']],
                'Sections' => [
                    'Colleges',
                    'Departments',
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'YearLevels' => ['fields' => ['id', 'name']]
                ]
            ])
            ->order([
                'PublishedCourses.academic_year' => 'DESC',
                'PublishedCourses.semester' => 'ASC',
                'PublishedCourses.program_id' => 'ASC',
                'PublishedCourses.program_type_id' => 'ASC',
                'PublishedCourses.year_level_id' => 'ASC',
                'PublishedCourses.section_id' => 'ASC',
                'PublishedCourses.course_id' => 'ASC',
                'PublishedCourses.id' => 'DESC'
            ])
            ->toArray();

        return $published_courses_by_section;
    }

    public function listOfCoursesInstructorAssignedBySection($academic_year = null, $semester = null, $instructor_id = null, $return_select_box = 0)
    {
        $list_of_sections = $this->listOfSectionInstructorAssigned($academic_year, $semester, $instructor_id);
        $courses_formated = [];

        if (!empty($list_of_sections)) {
            foreach ($list_of_sections as $section_id => $section) {
                $course_list = $this->find()
                    ->where([
                        'CourseInstructorAssignments.staff_id' => $instructor_id,
                        'CourseInstructorAssignments.isprimary' => 1,
                        'PublishedCourses.academic_year' => $academic_year,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.section_id' => $section_id,
                        'PublishedCourses.drop' => 0
                    ])
                    ->contain(['PublishedCourses' => ['Courses']])
                    ->toArray();

                $courses_formated[$section] = [];

                if (!empty($course_list)) {
                    foreach ($course_list as $course) {
                        $courses_formated[$section][$course->published_course->id] = trim($course->published_course->course->course_title) . ' (' . trim($course->published_course->course->course_code) . ')';
                    }
                }
            }
        }

        if ($return_select_box == 1) {
            $published_courses_combo = '';
            if (!empty($courses_formated)) {
                $published_courses_combo .= '<option value="">[ Select Course ]</option>';
            } else {
                $published_courses_combo .= '<option value="">[ No Assigned Courses Found, Try Changing Filters ]</option>';
            }
            if (count($courses_formated) > 0) {
                foreach ($courses_formated as $id => $course) {
                    $published_courses_combo .= "<optgroup label='" . htmlspecialchars($id) . "'>";
                    foreach ($course as $key => $value) {
                        $published_courses_combo .= "<option value='" . $key . "'>" . htmlspecialchars($value) . "</option>";
                    }
                    $published_courses_combo .= "</optgroup>";
                }
            }
            return $published_courses_combo;
        }

        return $courses_formated;
    }

    public function listOfFxCoursesInstructorAssignedBySection($academic_year = null, $semester = null, $instructor_id = null, $return_select_box = 0)
    {
        $list_of_sections = $this->listOfSectionInstructorAssigned($academic_year, $semester, $instructor_id);
        $courses_formated = [];

        if (!empty($list_of_sections)) {
            foreach ($list_of_sections as $section_id => $section) {
                $course_list = $this->find()
                    ->where([
                        'CourseInstructorAssignments.staff_id' => $instructor_id,
                        'CourseInstructorAssignments.isprimary' => 1,
                        'PublishedCourses.academic_year' => $academic_year,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.section_id' => $section_id,
                        'PublishedCourses.drop' => 0
                    ])
                    ->contain(['PublishedCourses' => ['Courses']])
                    ->toArray();

                if (!empty($course_list)) {
                    $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');
                    foreach ($course_list as $course) {
                        if ($makeupExamsTable->assignedMakeup($course->published_course->id)) {
                            $courses_formated[$section][$course->published_course->id] = trim($course->published_course->course->course_title) . ' (' . trim($course->published_course->course->course_code) . ')';
                        }
                    }
                }
            }
        }

        if ($return_select_box == 1) {
            $published_courses_combo = '';
            if (!empty($courses_formated)) {
                $published_courses_combo .= '<option value="">[ Select Course ]</option>';
            } else {
                $published_courses_combo .= '<option value="">[ Select Academic Year & Semester ]</option>';
            }
            if (count($courses_formated) > 0) {
                foreach ($courses_formated as $id => $course) {
                    $published_courses_combo .= "<optgroup label='" . htmlspecialchars($id) . "'>";
                    foreach ($course as $key => $value) {
                        $published_courses_combo .= "<option value='" . $key . "'>" . htmlspecialchars($value) . "</option>";
                    }
                    $published_courses_combo .= "</optgroup>";
                }
            }
            return $published_courses_combo;
        }

        return $courses_formated;
    }

    public function listOfAssignedGradeEntryAssignedBySection($academic_year = null, $semester = null, $instructor_id = null, $return_select_box = 0)
    {
        $list_of_sections = $this->listOfSectionInstructorAssigned($academic_year, $semester, $instructor_id);
        $courses_formated = [];

        if (!empty($list_of_sections)) {
            foreach ($list_of_sections as $section_id => $section) {
                $course_list = $this->find()
                    ->where([
                        'CourseInstructorAssignments.staff_id' => $instructor_id,
                        'CourseInstructorAssignments.isprimary' => 1,
                        'PublishedCourses.academic_year' => $academic_year,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.section_id' => $section_id,
                        'PublishedCourses.drop' => 0
                    ])
                    ->contain(['PublishedCourses' => ['Courses']])
                    ->toArray();

                if (!empty($course_list)) {
                    $resultEntryAssignmentsTable = TableRegistry::getTableLocator()->get('ResultEntryAssignments');
                    foreach ($course_list as $course) {
                        if ($resultEntryAssignmentsTable->assignedResultEntry($course->published_course->id)) {
                            $courses_formated[$section][$course->published_course->id] = trim($course->published_course->course->course_title) . ' (' . trim($course->published_course->course->course_code) . ')';
                        }
                    }
                }
            }
        }

        if ($return_select_box == 1) {
            $published_courses_combo = '';
            if (!empty($courses_formated)) {
                $published_courses_combo .= '<option value="">[ Select Course ]</option>';
            } else {
                $published_courses_combo .= '<option value="">[ No Assigned Courses Found, Try Changing Filters ]</option>';
            }
            if (count($courses_formated) > 0) {
                foreach ($courses_formated as $id => $course) {
                    $published_courses_combo .= "<optgroup label='" . htmlspecialchars($id) . "'>";
                    foreach ($course as $key => $value) {
                        $published_courses_combo .= "<option value='" . $key . "'>" . htmlspecialchars($value) . "</option>";
                    }
                    $published_courses_combo .= "</optgroup>";
                }
            }
            return $published_courses_combo;
        }

        return $courses_formated;
    }

    public function listOfCoursesSectionsTakingOrgBySection($department_id = null, $academic_year = null, $semester = null, $program_id = null, $program_type_id = null, $selectible_section = 0, $yearLevel = null): array
    {
        $yearLevelId = null;

        if (!empty($yearLevel) && !empty($department_id)) {
            $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
            $yearLevelRecord = $yearLevelsTable->find()
                ->select(['id'])
                ->where([
                    'YearLevels.department_id' => $department_id,
                    'YearLevels.name' => $yearLevel
                ])
                ->first();

            if ($yearLevelRecord) {
                $yearLevelId = $yearLevelRecord->id;
            }
        }

        $published_courses_by_section = $this->listOfDepartmentSections($department_id, $academic_year, $semester, $program_id, $program_type_id, $yearLevelId);

        $organized_Published_courses_by_sections = [];

        if ($selectible_section == 0) {
            foreach ($published_courses_by_section as $published_course) {

                $section_name = trim(preg_replace('/\s+/', ' ', $published_course->section->name));
                $year_level_name = $published_course->section->year_level->name ?? ($published_course->year_level->name ?? ($published_course->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'));
                $academic_year_label = $published_course->section->academicyear ?? $published_course->academicyear;
                $label = $section_name . ' (' . $year_level_name . ', ' . $academic_year_label . ')';
                $organized_Published_courses_by_sections[$label][$published_course->id] = trim($published_course->course->course_title) . ' (' . trim($published_course->course->course_code) . ')';
            }
        } else {
            foreach ($published_courses_by_section as $published_course) {
                $section_name = trim(preg_replace('/\s+/', ' ', $published_course->section->name));
                $year_level_name = $published_course->section->year_level->name ?? ($published_course->year_level->name ?? ($published_course->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'));
                $academic_year_label = $published_course->section->academicyear ?? $published_course->academicyear;
                $label = $section_name . ' (' . $year_level_name . ', ' . $academic_year_label . ')';
                $organized_Published_courses_by_sections[$label]['s~' . $published_course->section->id] = 'Master Sheet for ' . $section_name;
                $organized_Published_courses_by_sections[$label][$published_course->id] = trim($published_course->course->course_title) . ' (' . trim($published_course->course->course_code) . ')';
            }
        }

        return $organized_Published_courses_by_sections;
    }

    public function listOfCollegeFreshmanSections($college_id = null, $academic_year = null, $semester = null, $program_id = null, $program_type_id = null, $given_by_department_id = null): array
    {
        $conditions = [
            'Sections.college_id' => $college_id,
            'Sections.department_id IS' => null,
            'Sections.program_id' => $program_id,
            'Sections.program_type_id' => $program_type_id
        ];

        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $subquery = $publishedCoursesTable->find()
            ->select(['section_id'])
            ->distinct(['section_id'])
            ->where([
                'PublishedCourses.department_id IS' => null,
                'PublishedCourses.academic_year' => $academic_year,
                'PublishedCourses.semester' => $semester
            ]);

        if (!empty($given_by_department_id)) {
            $subquery->where(['PublishedCourses.given_by_department_id' => $given_by_department_id]);
            $conditions['PublishedCourses.given_by_department_id'] = $given_by_department_id;
        }

        $conditions['Sections.id IN'] = $subquery;

        $published_courses_by_section = $this->Sections->find()
            ->where($conditions)
            ->contain([
                'PublishedCourses' => [
                    'Courses',
                    'Colleges',
                    'GivenByDepartments',
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'conditions' => [
                        'PublishedCourses.academic_year' => $academic_year,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.drop' => 0
                    ],
                    'sort' => [
                        'PublishedCourses.academic_year' => 'DESC',
                        'PublishedCourses.semester' => 'ASC',
                        'PublishedCourses.program_id' => 'ASC',
                        'PublishedCourses.program_type_id' => 'ASC',
                        'PublishedCourses.year_level_id' => 'ASC',
                        'PublishedCourses.section_id' => 'ASC',
                        'PublishedCourses.course_id' => 'ASC',
                        'PublishedCourses.id' => 'DESC'
                    ]
                ],
                'YearLevels' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                'Programs' => ['fields' => ['id', 'name', 'shortname']],
                'Colleges',
                'Departments'
            ])
            ->toArray();

        return $published_courses_by_section;
    }

    public function listOfCoursesCollegeFreshTakingOrgBySection($college_id = null, $academic_year = null, $semester = null, $program_id = null, $program_type_id = null, $selectible_section = 0, $given_by_department_id = null): array
    {
        $published_courses_by_section = $this->listOfCollegeFreshmanSections($college_id, $academic_year, $semester, $program_id, $program_type_id, $given_by_department_id);
        $organized_Published_courses_by_sections = [];

        if ($selectible_section == 0) {
            foreach ($published_courses_by_section as $published_course_by_section) {
                $section_name = trim(preg_replace('/\s+/', ' ', $published_course_by_section->section->name));
                $label = $section_name . ($published_course_by_section->section->program_id == PROGRAM_REMEDIAL ? ' (Remedial, ' : ' (Pre/1st, ') . $published_course_by_section->section->academic_year . ')';
                foreach ($published_course_by_section->published_courses as $published_course) {
                    $organized_Published_courses_by_sections[$label][$published_course->id] = trim($published_course->course->course_title) . ' (' . trim($published_course->course->course_code) . ')';
                }
            }
        } else {
            foreach ($published_courses_by_section as $published_course_by_section) {
                $section_name = trim(preg_replace('/\s+/', ' ', $published_course_by_section->section->name));
                $label = $section_name . ($published_course_by_section->section->program_id == PROGRAM_REMEDIAL ? ' (Remedial, ' : ' (Pre/1st, ') . $published_course_by_section->section->academic_year . ')';
                $organized_Published_courses_by_sections[$label]['s~' . $published_course_by_section->section->id] = 'Mark Sheet for ' . $section_name;
                foreach ($published_course_by_section->published_courses as $published_course) {
                    $organized_Published_courses_by_sections[$label][$published_course->id] = trim($published_course->course->course_title) . ' (' . trim($published_course->course->course_code) . ')';
                }
            }
        }

        return $organized_Published_courses_by_sections;
    }

    public function instructorLoadOrganizedByAcademicYearAndSemester($data = null): array
    {
        $organized_loads_of_instructor = [];
        if (!empty($data)) {
            foreach ($data as $assigned_courses) {
                $organized_loads_of_instructor[$assigned_courses->published_course->academic_year][$assigned_courses->published_course->semester][] = $assigned_courses;
            }
        }
        return $organized_loads_of_instructor;
    }

    public function organizedPublishedCoursesByProgramSections($publishedCourses = null): array
    {
        $organized_published_courses = [];

        if (!empty($publishedCourses)) {
            foreach ($publishedCourses as &$value) {
                if (!empty($value->program->id) && !empty($value->program_type->id) && !empty($value->section->id)) {
                    if (!empty($value->published_course->given_by_department_id)) {
                        $value->departments = $this->PublishedCourses->Departments->find('list')
                            ->where([
                                'Departments.college_id' => $value->given_by_department->college_id,
                                'Departments.active' => 1
                            ])
                            ->toArray();
                    }

                    $year_level_name = $value->year_level->id ? $value->year_level->name : 'Pre/1st';
                    $organized_published_courses[$value->program->name][$value->program_type->name][$year_level_name][$value->section->name][] = $value;
                }
            }
        }

        return $organized_published_courses;
    }

    public function organizedPublishedCoursesByForAssignment($publishedcourses = null): array
    {
        $sections_array = [];
        $course_type_array = [];

        if (!empty($publishedcourses)) {
            foreach ($publishedcourses as $key => $publishedcourse) {
                $department_name = null;
                $year_level_name = null;

                if (!empty($publishedcourse->published_course->department_id)) {
                    $department_name = $publishedcourse->department->name;
                    $year_level_name = $publishedcourse->year_level->name;
                } elseif (!empty($publishedcourse->published_course->college_id)) {
                    $department_name = $publishedcourse->college->name;
                    $year_level_name = $publishedcourse->program->id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st';
                }

                $section_label = $publishedcourse->section->name . ' (' . $year_level_name . ', ' . $publishedcourse->section->academic_year . ')';

                if (!empty($publishedcourse->section_split_for_published_courses)) {
                    foreach ($publishedcourse->section_split_for_published_courses[0]->course_split_section as $split_section_for_course) {
                        $sections_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name] = [
                            'course_title' => $publishedcourse->course->course_title,
                            'course_id' => $publishedcourse->course->id,
                            'course_code' => $publishedcourse->course->course_code,
                            'credit' => $publishedcourse->course->credit,
                            'credit_detail' => $publishedcourse->course->lecture_hours . ' ' . $publishedcourse->course->tutorial_hours . ' ' . $publishedcourse->course->laboratory_hours,
                            'published_course_id' => $publishedcourse->published_course->id,
                            'grade_submitted' => $this->PublishedCourses->CourseRegistrations->ExamGrades->is_grade_submitted($publishedcourse->published_course->id),
                            'course_split_section_id' => $split_section_for_course->id,
                            'section_id' => $publishedcourse->published_course->section_id,
                            'given_by_department_id' => $publishedcourse->published_course->given_by_department_id
                        ];

                        if (!empty($publishedcourse->course_instructor_assignments)) {
                            foreach ($publishedcourse->course_instructor_assignments as $askey => $assign_instructor) {
                                if ($split_section_for_course->id == $assign_instructor->course_split_section_id) {
                                    $sections_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]['assign_instructor'][$assign_instructor->isprimary][$askey] = [
                                        'full_name' => ($assign_instructor->staff->title->title ?? '') . ' ' . ($assign_instructor->staff->full_name ?? ''),
                                        'position' => $assign_instructor->staff->position->position ?? '',
                                        'course_type' => $assign_instructor->type,
                                        'CourseInstructorAssignment_id' => $assign_instructor->id
                                    ];
                                }
                            }
                        }

                        if ($publishedcourse->course->lecture_hours > 0) {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["Lecture"] = "Lecture";
                            if ($publishedcourse->course->tutorial_hours > 0 && $publishedcourse->course->laboratory_hours > 0) {
                                $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["Lecture+Tutorial+Lab"] = "Lect.+Tut.+Lab";
                            }
                        }

                        if ($publishedcourse->course->tutorial_hours > 0) {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["tutorial"] = "Tutorial";
                            if ($publishedcourse->course->lecture_hours > 0) {
                                $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["Lecture+Tutorial"] = "Lect.+Tut.";
                            }
                        } elseif ($publishedcourse->course->laboratory_hours > 0) {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["Lab"] = "Lab";
                            if ($publishedcourse->course->lecture_hours > 0) {
                                $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["Lecture+Lab"] = "Lect.+Lab";
                            }
                        } else {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$split_section_for_course->section_name]["Other"] = "Other";
                        }
                    }
                } else {
                    $sections_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key] = [
                        'course_title' => $publishedcourse->course->course_title,
                        'course_id' => $publishedcourse->course->id,
                        'course_code' => $publishedcourse->course->course_code,
                        'credit' => $publishedcourse->course->credit,
                        'credit_detail' => $publishedcourse->course->lecture_hours . ' ' . $publishedcourse->course->tutorial_hours . ' ' . $publishedcourse->course->laboratory_hours,
                        'section_id' => $publishedcourse->published_course->section_id,
                        'published_course_id' => $publishedcourse->published_course->id,
                        'given_by_department_id' => $publishedcourse->published_course->given_by_department_id,
                        'grade_submitted' => $this->PublishedCourses->CourseRegistrations->ExamGrades->is_grade_submitted($publishedcourse->published_course->id)
                    ];

                    if (!empty($publishedcourse->course_instructor_assignments)) {
                        foreach ($publishedcourse->course_instructor_assignments as $askey => $assign_instructor) {
                            $sections_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]['assign_instructor'][$assign_instructor->isprimary][$askey] = [
                                'full_name' => ($assign_instructor->staff->title->title ?? '') . ' ' . ($assign_instructor->staff->full_name ?? ''),
                                'position' => $assign_instructor->staff->position->position ?? '',
                                'course_type' => $assign_instructor->type,
                                'CourseInstructorAssignment_id' => $assign_instructor->id
                            ];
                        }
                    }

                    if ($publishedcourse->course->lecture_hours > 0) {
                        $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Lecture"] = "Lecture";
                        if ($publishedcourse->course->tutorial_hours > 0 && $publishedcourse->course->laboratory_hours > 0) {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Lecture+Tutorial+Lab"] = "Lect.+Tut.+Lab";
                        }
                    }

                    if ($publishedcourse->course->tutorial_hours > 0) {
                        $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["tutorial"] = "Tutorial";
                        if ($publishedcourse->course->lecture_hours > 0) {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Lecture+Tutorial"] = "Lect.+Tut.";
                            if ($publishedcourse->course->id == 550) {
                                debug($course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Lecture+Tutorial"]);
                            }
                        }
                    } elseif ($publishedcourse->course->laboratory_hours > 0) {
                        $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Lab"] = "Lab";
                        if ($publishedcourse->course->lecture_hours > 0) {
                            $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Lecture+Lab"] = "Lect.+Lab";
                        }
                    } else {
                        $course_type_array[$department_name][$publishedcourse->program->name][$publishedcourse->program_type->name][$year_level_name][$section_label][$key]["Other"] = "Other";
                    }
                }
            }
        }

        return [
            'sections_array' => $sections_array,
            'course_type_array' => $course_type_array
        ];
    }

    public function getDisptachedCoursesForNotification($department_id = null): array
    {
        $dispatchedCourseLists = [];

        $publishedOn = Time::now()->modify('-' . DAYS_BACK_DISPATCHED_NOTIFICATION . ' days')->format('Y-m-d');

        $dispatched_detail = $this->PublishedCourses->find()
            ->where([
                'PublishedCourses.given_by_department_id' => $department_id,
                'PublishedCourses.id NOT IN' => $this->find()->select(['published_course_id']),
                'PublishedCourses.created >=' => $publishedOn
            ])
            ->contain([
                'CourseInstructorAssignments',
                'Departments',
                'Colleges',
                'Sections',
                'YearLevels',
                'GivenByDepartments',
                'Courses',
                'Programs',
                'ProgramTypes'
            ])
            ->toArray();

        if (!empty($dispatched_detail)) {
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            foreach ($dispatched_detail as $v) {
                $gradeSubmitted = $examGradesTable->is_grade_submitted($v->published_course->id);
                if ($gradeSubmitted == 0 && $v->published_course->department_id != $department_id) {
                    $dispatchedCourseLists[] = $v;
                }
            }
        }

        return $dispatchedCourseLists;
    }

    public function getDisptachedCoursesNotAssigned($department_id = null): array
    {
        $dispatchedCourseLists = [];

        $publishedOn = Time::now()->modify('-' . DAYS_BACK_DISPATCHED_NOTIFICATION . ' days')->format('Y-m-d');

        $dispatched_detail = $this->PublishedCourses->find()
            ->where([
                'PublishedCourses.given_by_department_id <>' => $department_id,
                'PublishedCourses.department_id' => $department_id,
                'PublishedCourses.id NOT IN' => $this->find()->select(['published_course_id']),
                'PublishedCourses.created >=' => $publishedOn
            ])
            ->contain([
                'CourseInstructorAssignments',
                'Departments',
                'Colleges',
                'GivenByDepartments',
                'Sections',
                'YearLevels',
                'Courses',
                'Programs',
                'ProgramTypes'
            ])
            ->toArray();

        if (!empty($dispatched_detail)) {
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            foreach ($dispatched_detail as $v) {
                $gradeSubmitted = $examGradesTable->is_grade_submitted($v->published_course->id);
                if (empty($gradeSubmitted)) {
                    $dispatchedCourseLists[] = $v;
                }
            }
        }

        return $dispatchedCourseLists;
    }

    public function getGradeSubmissionStat($academic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null): array
    {
        $academicCalendarOptions = [];
        $instructorAssignmentOptions = [
            'conditions' => [
                'PublishedCourses.drop' => 0,
                'CourseInstructorAssignments.isprimary' => 1
            ]
        ];

        if (!empty($academic_year) && !empty($semester)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.academic_year'] = $academic_year;
            $academicCalendarOptions['conditions']['AcademicCalendars.semester'] = $semester;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignments.academic_year'] = $academic_year;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignments.semester'] = $semester;
            $instructorAssignmentOptions['conditions']['PublishedCourses.academic_year'] = $academic_year;
            $instructorAssignmentOptions['conditions']['PublishedCourses.semester'] = $semester;
        }

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.program_type_id'] = $program_type_id;
            $instructorAssignmentOptions['conditions']['PublishedCourses.program_type_id'] = $program_type_id;
        }

        if ($program_id != 0 && !empty($program_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.program_id'] = $program_id;
            $instructorAssignmentOptions['conditions']['PublishedCourses.program_id'] = $program_id;
        }

        if (!empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $instructorAssignmentOptions['conditions']['PublishedCourses.college_id'] = $college_id[1];
            } else {
                debug($department_id);
                $instructorAssignmentOptions['conditions']['PublishedCourses.department_id'] = $department_id;
            }
        }

        debug($instructorAssignmentOptions);

        $instructorAssignmentOptions['contain'] = [
            'Staffs' => [
                'fields' => ['id', 'full_name', 'first_name', 'middle_name', 'last_name'],
                'Titles' => ['fields' => ['id', 'title']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']]
            ],
            'PublishedCourses' => [
                'Programs' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Courses' => ['fields' => ['course_title', 'course_code']]
            ]
        ];

        $academicCalendarOptions['contain'] = [
            'Programs' => ['fields' => ['id', 'name']],
            'ProgramTypes' => ['fields' => ['id', 'name']]
        ];

        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
        $academicCalendarDetails = $academicCalendarsTable->find('all', $academicCalendarOptions)->toArray();

        $gradeSubmissionDateOfYearLevel = [];

        if (!empty($academicCalendarDetails)) {
            foreach ($academicCalendarDetails as $value) {
                $department_ids = unserialize($value->department_id);
                $year_level_ids = unserialize($value->year_level_id);

                if (!empty($year_level_ids) && !empty($department_ids)) {
                    foreach ($year_level_ids as $yv) {
                        foreach ($department_ids as $dpv) {
                            $pre_college_ids = explode('pre_', $dpv);
                            if (count($pre_college_ids) > 1) {
                                debug($yv);
                                debug($pre_college_ids[1]);
                            }
                            $gradeSubmissionDateOfYearLevel[$yv][$dpv]['grade_submission_end_date'] = $value->grade_submission_end_date;
                        }
                    }
                }
            }
        }

        $reformattedGradeSubmissionStat = [];

        if (empty($gradeSubmissionDateOfYearLevel)) {
            return $reformattedGradeSubmissionStat;
        }

        $assignmentList = $this->find('all', $instructorAssignmentOptions)->toArray();

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        foreach ($assignmentList as $kkvalue) {
            $gradeSubmitteddate = $examGradesTable->getGradeSubmmissionDate($kkvalue->published_course->id);
            debug($gradeSubmitteddate);

            $noDaysDelayed = 0;
            if (empty($gradeSubmitteddate)) {
                if (!empty($kkvalue->published_course->year_level->name)) {
                    $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue->published_course->year_level->name][$kkvalue->published_course->department_id]['grade_submission_end_date'] ?? null;

                    if (!empty($grade_submission_end_date)) {
                        $current_date = Time::now()->format('Y-m-d H:i:s');
                        $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                        if ($grade_submission_end_date > $current_date) {
                            $noDaysDelayed = 0;
                        } else {
                            $noDaysDelayed = $this->timeAgoFormat($current_date, $grade_submission_end_date_formatted);
                            $reformattedGradeSubmissionStat[$kkvalue->published_course->program->name][$kkvalue->published_course->program_type->name][$kkvalue->staff->college->name][$kkvalue->staff->department->name][$kkvalue->staff->full_name][$kkvalue->published_course->course->course_title . " (" . $kkvalue->published_course->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                        }
                    }
                } else {
                    if (!empty($kkvalue->published_course->college->id)) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue->published_course->college->id]['grade_submission_end_date'] ?? null;

                        if (!empty($grade_submission_end_date)) {
                            $current_date = Time::now()->format('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                            if ($grade_submission_end_date > $current_date) {
                                $noDaysDelayed = 0;
                            } else {
                                $noDaysDelayed = $this->timeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                $reformattedGradeSubmissionStat[$kkvalue->published_course->program->name][$kkvalue->published_course->program_type->name][$kkvalue->staff->college->name][$kkvalue->staff->department->name][$kkvalue->staff->full_name][$kkvalue->published_course->course->course_title . " (" . $kkvalue->published_course->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                            }
                        }
                    }
                }
            }

            if (!empty($gradeSubmitteddate)) {
                if (!empty($kkvalue->published_course->year_level->name)) {
                    $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue->published_course->year_level->name][$kkvalue->published_course->department_id]['grade_submission_end_date'] ?? null;

                    if (!empty($grade_submission_end_date)) {
                        $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');
                        debug($grade_submission_end_date);

                        if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                            $noDaysDelayed = 0;
                        } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                            $noDaysDelayed = $this->timeAgoFormat($gradeSubmitteddate['ExamGrade']['created'], $grade_submission_end_date_formatted);
                            $reformattedGradeSubmissionStat[$kkvalue->published_course->program->name][$kkvalue->published_course->program_type->name][$kkvalue->staff->college->name][$kkvalue->staff->department->name][$kkvalue->staff->full_name][$kkvalue->published_course->course->course_title . " (" . $kkvalue->published_course->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                        }
                    }
                } else {
                    if (!empty($kkvalue->published_course->college->id)) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue->published_course->college->id]['grade_submission_end_date'] ?? null;

                        if (!empty($grade_submission_end_date)) {
                            $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');
                            if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                                $noDaysDelayed = 0;
                            } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                                $noDaysDelayed = $this->timeAgoFormat($gradeSubmitteddate['ExamGrade']['created'], $grade_submission_end_date_formatted);
                                $reformattedGradeSubmissionStat[$kkvalue->published_course->program->name][$kkvalue->published_course->program_type->name][$kkvalue->staff->college->name][$kkvalue->staff->department->name][$kkvalue->staff->full_name][$kkvalue->published_course->course->course_title . " (" . $kkvalue->published_course->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                            }
                        }
                    }
                }
            }
        }

        return $reformattedGradeSubmissionStat;
    }

    public function getGradeSubmissionDelayStat($academic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null): array
    {
        $academicCalendarOptions = [];
        $instructorAssignmentOptions = [
            'conditions' => [
                'PublishedCourses.drop' => 0
            ]
        ];

        if (!empty($academic_year) && !empty($semester)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.academic_year'] = $academic_year;
            $academicCalendarOptions['conditions']['AcademicCalendars.semester'] = $semester;
            $instructorAssignmentOptions['conditions']['PublishedCourses.academic_year'] = $academic_year;
            $instructorAssignmentOptions['conditions']['PublishedCourses.semester'] = $semester;
        }

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.program_type_id'] = $program_type_id;
            $instructorAssignmentOptions['conditions']['PublishedCourses.program_type_id'] = $program_type_id;
        }

        if ($program_id != 0 && !empty($program_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.program_id'] = $program_id;
            $instructorAssignmentOptions['conditions']['PublishedCourses.program_id'] = $program_id;
        }

        if (!empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $departmentLists = $departmentsTable->find('list')
                    ->where([
                        'Departments.college_id' => $college_id[1],
                        'Departments.active' => 1
                    ])
                    ->toArray();

                if (!empty($departmentLists)) {
                    $instructorAssignmentOptions['conditions']['PublishedCourses.department_id IN'] = array_keys($departmentLists);
                } else {
                    $instructorAssignmentOptions['conditions']['PublishedCourses.college_id'] = $college_id[1];
                }
            } else {
                $instructorAssignmentOptions['conditions']['PublishedCourses.department_id'] = $department_id;
            }
        }

        debug($instructorAssignmentOptions);

        $instructorAssignmentOptions['contain'] = [
            'CourseInstructorAssignments' => [
                'Staffs' => [
                    'fields' => ['id', 'full_name', 'first_name', 'middle_name', 'last_name'],
                    'Titles' => ['fields' => ['id', 'title']],
                    'Colleges' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'conditions' => ['CourseInstructorAssignments.isprimary' => 1]
                ]
            ],
            'Programs' => ['fields' => ['id', 'name']],
            'Sections' => ['fields' => ['id', 'name']],
            'YearLevels' => ['fields' => ['id', 'name']],
            'ProgramTypes' => ['fields' => ['id', 'name']],
            'Departments' => ['fields' => ['id', 'name']],
            'Colleges' => ['fields' => ['id', 'name']],
            'Courses' => ['fields' => ['id', 'course_title', 'course_code']]
        ];

        $academicCalendarOptions['contain'] = [
            'Programs' => ['fields' => ['id', 'name']],
            'ProgramTypes' => ['fields' => ['id', 'name']]
        ];

        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
        $academicCalendarDetails = $academicCalendarsTable->find('all', $academicCalendarOptions)->toArray();

        $gradeSubmissionDateOfYearLevel = [];

        foreach ($academicCalendarDetails as $value) {
            $department_ids = unserialize($value->department_id);
            $year_level_ids = unserialize($value->year_level_id);
            foreach ($year_level_ids as $yv) {
                foreach ($department_ids as $dpv) {
                    $gradeSubmissionDateOfYearLevel[$yv][$dpv]['grade_submission_end_date'] = $value->grade_submission_end_date;
                }
            }
        }

        $reformattedGradeSubmissionStat = [];

        if (empty($gradeSubmissionDateOfYearLevel)) {
            return $reformattedGradeSubmissionStat;
        }

        $assignmentList = $this->PublishedCourses->find('all', $instructorAssignmentOptions)->toArray();

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        foreach ($assignmentList as $kkvalue) {
            $gradeSubmitteddate = $examGradesTable->getGradeSubmmissionDate($kkvalue->published_course->id);

            $noDaysDelayed = 0;
            if (empty($gradeSubmitteddate)) {
                if (!empty($kkvalue->year_level->name)) {
                    $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue->year_level->name][$kkvalue->published_course->department_id]['grade_submission_end_date'] ?? null;

                    if (!empty($grade_submission_end_date)) {
                        $current_date = Time::now()->format('Y-m-d H:i:s');
                        $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                        if ($grade_submission_end_date_formatted > $current_date) {
                            $noDaysDelayed = 0;
                            if (!empty($kkvalue->course_instructor_assignments[0]) && $kkvalue->course_instructor_assignments[0]->isprimary) {
                                $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['Section'] = $kkvalue->section->name . '(' . $kkvalue->year_level->name . ')';
                            }
                        } else {
                            $noDaysDelayed = $this->timeAgoFormat($current_date, $grade_submission_end_date_formatted);
                            if (!empty($kkvalue->course_instructor_assignments[0]) && $kkvalue->course_instructor_assignments[0]->isprimary) {
                                $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['Section'] = $kkvalue->section->name . '(' . $kkvalue->year_level->name . ')';
                            }
                        }
                    }
                } else {
                    if (!empty($kkvalue->college->id)) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue->college->id]['grade_submission_end_date'] ?? null;

                        if (!empty($grade_submission_end_date)) {
                            $current_date = Time::now()->format('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                            if ($grade_submission_end_date > $current_date) {
                                $noDaysDelayed = 0;
                                if (!empty($kkvalue->course_instructor_assignments[0])) {
                                    $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                    $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['Section'] = $kkvalue->section->name . '(Pre)';
                                }
                            } else {
                                $noDaysDelayed = $this->timeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                if (!empty($kkvalue->course_instructor_assignments[0])) {
                                    $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                    $reformattedGradeSubmissionStat[$kkvalue->program->name][$kkvalue->program_type->name][$kkvalue->course_instructor_assignments[0]->staff->college->name][$kkvalue->course_instructor_assignments[0]->staff->department->name][$kkvalue->course_instructor_assignments[0]->staff->full_name][$kkvalue->course->course_title . " (" . $kkvalue->course->course_code . ")"]['Section'] = $kkvalue->section->name . '(Pre)';
                                }
                            }
                        }
                    }
                }
            }
        }

        return $reformattedGradeSubmissionStat;
    }



    public function timeAgoFormat($time, $grade_submission_date): string
    {
        $time_array = [
            12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];

        $timestamp = Time::parse($time)->getTimestamp();
        $gradeSubmissionDate = Time::parse($grade_submission_date)->getTimestamp();

        $time_diff = abs($timestamp - $gradeSubmissionDate);

        if ($time_diff < 1) {
            return '0 second';
        }

        foreach ($time_array as $seconds => $str) {
            $time_ago = $time_diff / $seconds;
            if ($time_ago >= 1) {
                $ago = round($time_ago);
                return $ago . ' ' . $str . ($ago > 1 ? 's' : '');
            }
        }

        return '0 second';
    }


    public function getGradeSubmissionStatNumber($academic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null): array
    {
        $courseInstructorAssignmentOptions = [
            'conditions' => [
                'CourseInstructorAssignments.isprimary' => 1,
                'CourseInstructorAssignments.published_course_id IN' => $this->PublishedCourses->find()->select(['id'])
            ]
        ];
        $academicCalendarOptions = [];
        $instructorAssignmentOptions = [
            'conditions' => [
                'CourseInstructorAssignments.isprimary' => 1
            ]
        ];
        $reformattedGradeSubmissionStat = [
            'Instructor' => [
                'noInstDelayedSub' => 0,
                'noInstNotDelayedSub' => 0,
                'totalCourseAssignment' => 0
            ]
        ];

        if (!empty($academic_year) && !empty($semester)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.academic_year'] = $academic_year;
            $academicCalendarOptions['conditions']['AcademicCalendars.semester'] = $semester;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignments.academic_year'] = $academic_year;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignments.semester'] = $semester;
            $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.academic_year'] = $academic_year;
            $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.semester'] = $semester;
            $instructorAssignmentOptions['contain']['PublishedCourses'] = [
                'conditions' => [
                    'PublishedCourses.academic_year' => $academic_year,
                    'PublishedCourses.semester' => $semester,
                    'PublishedCourses.drop' => 0
                ]
            ];
            $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.published_course_id IN'] = $this->PublishedCourses->find()
                ->select(['id'])
                ->where(['drop' => 0]);
        }

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.program_type_id'] = $program_type_id;
            $instructorAssignmentOptions['contain']['PublishedCourses'] = [
                'conditions' => ['PublishedCourses.program_type_id' => $program_type_id]
            ];
            $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.published_course_id IN'] = $this->PublishedCourses->find()
                ->select(['id'])
                ->where(['program_type_id' => $program_type_id]);
        }

        if ($program_id != 0 && !empty($program_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendars.program_id'] = $program_id;
            $instructorAssignmentOptions['contain']['PublishedCourses'] = [
                'conditions' => ['PublishedCourses.program_id' => $program_id]
            ];
            $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.published_course_id IN'] = $this->PublishedCourses->find()
                ->select(['id'])
                ->where(['program_id' => $program_id]);
        }

        if (!empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $instructorAssignmentOptions['contain']['Staffs'] = [
                    'conditions' => ['Staffs.college_id' => $college_id[1]]
                ];
                $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.staff_id IN'] = $this->Staffs->find()
                    ->select(['id'])
                    ->where(['college_id' => $college_id[1]]);
            } else {
                debug($department_id);
                $instructorAssignmentOptions['contain']['Staffs'] = [
                    'conditions' => ['Staffs.department_id' => $department_id]
                ];
                $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignments.staff_id IN'] = $this->Staffs->find()
                    ->select(['id'])
                    ->where(['department_id' => $department_id]);
            }
        }

        debug($courseInstructorAssignmentOptions);

        $instructorAssignmentOptions['contain'] = array_merge($instructorAssignmentOptions['contain'] ?? [], [
            'Staffs' => [
                'fields' => ['id', 'full_name', 'first_name', 'middle_name', 'last_name'],
                'Titles' => ['fields' => ['id', 'title']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']]
            ],
            'PublishedCourses' => [
                'Programs' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Courses' => ['fields' => ['course_title', 'course_code']]
            ]
        ]);

        $academicCalendarOptions['contain'] = [
            'Programs' => ['fields' => ['id', 'name']],
            'ProgramTypes' => ['fields' => ['id', 'name']]
        ];

        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
        $academicCalendarDetails = $academicCalendarsTable->find('all', $academicCalendarOptions)->toArray();

        $reformattedGradeSubmissionStat['Instructor']['totalCourseAssignment'] = $this->find('all', $courseInstructorAssignmentOptions)->count();

        $gradeSubmissionDateOfYearLevel = [];
        foreach ($academicCalendarDetails as $value) {
            $department_ids = unserialize($value->department_id);
            $year_level_ids = unserialize($value->year_level_id);
            foreach ($year_level_ids as $yv) {
                foreach ($department_ids as $dpv) {
                    $gradeSubmissionDateOfYearLevel[$yv][$dpv]['grade_submission_end_date'] = $value->grade_submission_end_date;
                }
            }
        }

        if (empty($gradeSubmissionDateOfYearLevel)) {
            return $reformattedGradeSubmissionStat;
        }

        $assignmentList = $this->find('all', $instructorAssignmentOptions)->toArray();
        debug($this->find('count', $instructorAssignmentOptions));

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $count = 0;
        foreach ($assignmentList as $kkvalue) {
            $gradeSubmitteddate = $examGradesTable->getGradeSubmissionDate($kkvalue->published_course->id);

            $noDaysDelayed = 0;
            if (empty($gradeSubmitteddate)) {
                if (!empty($kkvalue->published_course->year_level->name)) {
                    $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue->published_course->year_level->name][$kkvalue->published_course->department_id]['grade_submission_end_date'] ?? null;

                    if (!empty($grade_submission_end_date)) {
                        $current_date = Time::now()->format('Y-m-d H:i:s');
                        $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                        if ($grade_submission_end_date_formatted > $current_date) {
                            $noDaysDelayed = 0;
                            $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                        } else {
                            $noDaysDelayed = $this->timeAgoFormat($current_date, $grade_submission_end_date_formatted);
                            $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                        }
                    } else {
                        debug($grade_submission_end_date);
                    }
                } else {
                    if (!empty($kkvalue->published_course->college->id)) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue->published_course->college->id]['grade_submission_end_date'] ?? null;

                        if (!empty($grade_submission_end_date)) {
                            $current_date = Time::now()->format('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                            if ($grade_submission_end_date > $current_date) {
                                $noDaysDelayed = 0;
                                $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                            } else {
                                $noDaysDelayed = $this->timeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                            }
                        }
                    } else {
                        debug($kkvalue->published_course);
                    }
                }
            }

            if (!empty($gradeSubmitteddate)) {
                if (!empty($kkvalue->published_course->year_level->name)) {
                    $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue->published_course->year_level->name][$kkvalue->published_course->department_id]['grade_submission_end_date'] ?? null;

                    if (!empty($grade_submission_end_date)) {
                        $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                        if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                            $noDaysDelayed = 0;
                            $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                        } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                            $noDaysDelayed = $this->timeAgoFormat($gradeSubmitteddate['ExamGrade']['created'], $grade_submission_end_date_formatted);
                            $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                        }
                    }
                } else {
                    if (!empty($kkvalue->published_course->college->id)) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue->published_course->college->id]['grade_submission_end_date'] ?? null;

                        if (!empty($grade_submission_end_date)) {
                            $grade_submission_end_date_formatted = Time::parse($grade_submission_end_date)->format('Y-m-d H:i:s');

                            if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                                $noDaysDelayed = 0;
                                $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                            } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                                $noDaysDelayed = $this->timeAgoFormat($gradeSubmitteddate['ExamGrade']['created'], $grade_submission_end_date_formatted);
                                $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                            }
                        }
                    }
                }
            }
        }

        debug($count);
        return $reformattedGradeSubmissionStat;
    }

    public function getAllDepartmentYearLevelMatchingYear($id): array
    {
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $yearLevel = $yearLevelsTable->find('list')
            ->where(['YearLevels.id' => $id])
            ->toArray();

        $yearLevelIds = $yearLevelsTable->find('list')
            ->where(['YearLevels.name IN' => array_values($yearLevel)])
            ->toArray();

        $result = [0];
        foreach ($yearLevelIds as $key => $value) {
            $result[] = $key;
        }

        return $result;
    }

    public function getCourseNotAssigned($academic_year, $semester, $program_id, $program_type_id, $department_id, $year_level_id, $freshman = 0): array
    {
        if (empty($academic_year) && empty($semester)) {
            return [];
        }

        $options = [
            'conditions' => [
                'PublishedCourses.drop' => 0,
                'PublishedCourses.academic_year' => $academic_year,
                'PublishedCourses.semester' => $semester,
                'PublishedCourses.id NOT IN' => $this->find()
                    ->select(['published_course_id'])
                    ->where([
                        'CourseInstructorAssignments.academic_year' => $academic_year,
                        'CourseInstructorAssignments.semester' => $semester
                    ])
                    ->group(['published_course_id'])
            ],
            'contain' => [
                'GivenByDepartments',
                'YearLevels',
                'Courses',
                'Programs',
                'ProgramTypes',
                'Sections' => ['conditions' => ['Sections.archive' => 0]]
            ]
        ];

        if (!empty($program_id)) {
            $program_ids = explode('~', $program_id);
            $options['conditions']['PublishedCourses.program_id'] = count($program_ids) > 1 ? $program_ids[1] : $program_id;
        }

        if (!empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            $options['conditions']['PublishedCourses.program_type_id'] = count($program_type_ids) > 1 ? $program_type_ids[1] : $program_type_id;
        }

        $departments = [];
        if (!empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->PublishedCourses->Departments->find('all')
                    ->where([
                        'Departments.college_id' => $college_ids[1],
                        'Departments.active' => 1
                    ])
                    ->contain(['Colleges', 'YearLevels'])
                    ->toArray();
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->PublishedCourses->Departments->find('all')
                    ->where([
                        'Departments.id' => $department_id,
                        'Departments.active' => 1
                    ])
                    ->contain(['Colleges', 'YearLevels'])
                    ->toArray();
            }
        } else {
            $departments = $this->PublishedCourses->Departments->find('all')
                ->where(['Departments.active' => 1])
                ->contain(['Colleges', 'YearLevels'])
                ->toArray();
        }

        $notAssignedCourseList = [];

        if ($freshman == 0) {
            foreach ($departments as $value) {
                $college_id[$value->college_id] = $value->college_id;
                $yearLevel = [];
                if (!empty($year_level_id)) {
                    foreach ($value->year_levels as $yyvalue) {
                        if (strcasecmp($year_level_id, $yyvalue->name) == 0) {
                            $yearLevel[] = $yyvalue;
                        }
                    }
                } else {
                    $yearLevel = $value->year_levels;
                }

                if (!empty($yearLevel)) {
                    foreach ($yearLevel as $yvalue) {
                        $options['conditions']['PublishedCourses.year_level_id'] = $yvalue->id;
                        $options['conditions']['PublishedCourses.department_id'] = $value->id;

                        $courseInstructorAssignment = $this->PublishedCourses->find('all', $options)->toArray();
                        $notAssignedCourseList[$value->name][$yvalue->name] = $courseInstructorAssignment;
                    }
                }
            }
        } else {
            $college_id = [];
            if (!empty($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->PublishedCourses->Colleges->find('list')
                        ->where([
                            'Colleges.id' => $college_ids[1],
                            'Colleges.active' => 1
                        ])
                        ->select(['id'])
                        ->toArray();
                } elseif ($department_id == 0) {
                    $college_id = $this->PublishedCourses->Colleges->find('list')
                        ->where(['Colleges.active' => 1])
                        ->select(['id'])
                        ->toArray();
                }
            }

            if (!empty($college_id)) {
                $colleges = $this->PublishedCourses->Colleges->find('all')
                    ->where([
                        'Colleges.id IN' => $college_id,
                        'Colleges.active' => 1
                    ])
                    ->toArray();

                foreach ($colleges as $value) {
                    $options['conditions'] = [
                        'PublishedCourses.department_id IS' => null,
                        'PublishedCourses.academic_year' => $academic_year,
                        'PublishedCourses.semester' => $semester,
                        'OR' => [
                            'PublishedCourses.year_level_id IS' => null,
                            'PublishedCourses.year_level_id' => 0,
                            'PublishedCourses.year_level_id' => ''
                        ],
                        'PublishedCourses.college_id' => $value->id,
                        'PublishedCourses.id NOT IN' => $this->find()
                            ->select(['published_course_id'])
                            ->where([
                                'CourseInstructorAssignments.academic_year' => $academic_year,
                                'CourseInstructorAssignments.semester' => $semester
                            ])
                            ->group(['published_course_id'])
                    ];

                    $courseInstructorAssignment = $this->PublishedCourses->find('all', $options)->toArray();
                    $notAssignedCourseList[$value->name]['Pre/1st'] = $courseInstructorAssignment;
                }
            }
        }

        return $notAssignedCourseList;
    }
}
