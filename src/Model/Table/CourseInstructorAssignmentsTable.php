<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseInstructorAssignmentsTable extends Table
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

        $this->setTable('course_instructor_assignments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');


        $this->belongsTo('Sections',
        ['propertyName' => 'Section']);
        $this->belongsTo('CourseSplitSections',
            ['propertyName' => 'CourseSplitSection']);
        $this->belongsTo('Staffs', ['propertyName' => 'Staff']);
        $this->belongsTo('PublishedCourses',
            ['propertyName' => 'PublishedCourse']);

        $this->hasMany('ExamGrades', [
            'foreignKey' => 'course_instructor_assignment_id',
            'propertyName' => 'ExamGrade'
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->boolean('isprimary')
            ->notEmptyString('isprimary');

        $validator
            ->boolean('evaluation_printed')
            ->notEmptyString('evaluation_printed');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['section_id'], 'Sections'));
        $rules->add($rules->existsIn(['staff_id'], 'Staffs'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));
        $rules->add($rules->existsIn(['course_split_section_id'], 'CourseSplitSections'));

        return $rules;
    }

    function listOfSectionInstructorAssigned($acadamic_year = null, $semester = null, $instructor_id = null)
    {
        $published_course_details = $this->find('all', array(
            'fields' => array('id'),
            'conditions' => array(
                //'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                'CourseInstructorAssignment.staff_id' => $instructor_id,
                'CourseInstructorAssignment.academic_year' => $acadamic_year,
                'CourseInstructorAssignment.semester' => $semester,
                'CourseInstructorAssignment.isprimary' => 1
            ),
            'contain' => array('PublishedCourse' => 'section_id')
        ));

        $sections_formated = array();

        if (!empty($published_course_details)) {
            foreach ($published_course_details as $key => $published_course_detail) {
                $section_detail = $this->Section->find('all', array(
                    'conditions' => array('Section.id' => $published_course_detail['PublishedCourse']['section_id']),
                    'contain' => array('Department', 'Program', 'ProgramType')
                ));

                if (!isset($section_detail[0]['Section'])) {
                    debug($section_detail);
                }

                if (isset($section_detail[0]['Section'])) {
                    $sections_formated[$section_detail[0]['Section']['id']] = $section_detail[0]['Section']['name'] . ' (' . $section_detail[0]['Program']['name'] . ', ' . $section_detail[0]['ProgramType']['name'];

                    if (!empty($section_detail[0]['Department']['name'])) {
                        $sections_formated[$section_detail[0]['Section']['id']] .= ' - ' . $section_detail[0]['Department']['name'] . ')';
                    } else {
                        $sections_formated[$section_detail[0]['Section']['id']] .=  ($section_detail[0]['Section']['program_id'] == PROGRAM_REMEDIAL ? ' - Remedial)' : ' - Pre/Freshman)');
                    }
                }
            }
        }

        return $sections_formated;
    }

    function listOfDepartmentSections($department_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_id = null, $yearLevel = null)
    {

        if (!empty($yearLevel)) {
            $published_courses_by_section = $this->PublishedCourse->find('all', array(
                'conditions' => array(
                    "OR" => array(
                        "PublishedCourse.department_id" => $department_id,
                        "PublishedCourse.given_by_department_id" => $department_id
                    ),
                    'PublishedCourse.year_level_id' => $yearLevel,
                    'PublishedCourse.program_id' => $program_id,
                    'PublishedCourse.program_type_id' => $program_type_id,
                    'PublishedCourse.academic_year' => $acadamic_year,
                    'PublishedCourse.semester' => $semester,
                    'PublishedCourse.drop' => 0,
                ),
                'contain' => array(
                    'Department',
                    'College',
                    'Course',
                    'GivenByDepartment',
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name', 'shortname'),
                    'Program' => array('id', 'name', 'shortname'),
                    'Section' => array(
                        'College',
                        'Department',
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'Program' => array('id', 'name', 'shortname'),
                        'YearLevel' => array('id', 'name'),
                    ),
                ),
                'order' => array('PublishedCourse.academic_year' => 'DESC', 'PublishedCourse.semester' => 'ASC', 'PublishedCourse.program_id' => 'ASC', 'PublishedCourse.program_type_id' => 'ASC', 'PublishedCourse.year_level_id' => 'ASC', 'PublishedCourse.section_id' => 'ASC', 'PublishedCourse.course_id' => 'ASC', 'PublishedCourse.id' => 'DESC'),
            ));
        } else {
            $published_courses_by_section = $this->PublishedCourse->find('all', array(
                'conditions' => array(
                    "OR" => array(
                        "PublishedCourse.department_id" => $department_id,
                        "PublishedCourse.given_by_department_id" => $department_id
                    ),
                    'PublishedCourse.program_id' => $program_id,
                    'PublishedCourse.program_type_id' => $program_type_id,
                    'PublishedCourse.academic_year' => $acadamic_year,
                    'PublishedCourse.semester' => $semester,
                    'PublishedCourse.drop' => 0,
                ),
                'contain' => array(
                    'Department',
                    'College',
                    'Course',
                    'GivenByDepartment',
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name', 'shortname'),
                    'Program' => array('id', 'name', 'shortname'),
                    'Section' => array(
                        'College',
                        'Department',
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'Program' => array('id', 'name', 'shortname'),
                        'YearLevel' => array('id', 'name'),
                    ),
                ),
                'order' => array('PublishedCourse.academic_year' => 'DESC', 'PublishedCourse.semester' => 'ASC', 'PublishedCourse.program_id' => 'ASC', 'PublishedCourse.program_type_id' => 'ASC', 'PublishedCourse.year_level_id' => 'ASC', 'PublishedCourse.section_id' => 'ASC', 'PublishedCourse.course_id' => 'ASC', 'PublishedCourse.id' => 'DESC'),
            ));
        }

        return $published_courses_by_section;
    }

    function listOfCoursesInstructorAssignedBySection($acadamic_year = null, $semester = null, $instructor_id = null, $return_select_box = 0)
    {
        $list_of_sections = $this->listOfSectionInstructorAssigned($acadamic_year, $semester, $instructor_id);
        $courses_formated = array();

        if (!empty($list_of_sections)) {
            foreach ($list_of_sections as $section_id => $section) {
                $course_list = $this->find('all', array(
                    'conditions' => array(
                        'CourseInstructorAssignment.staff_id' => $instructor_id,
                        'CourseInstructorAssignment.isprimary' => 1,
                        'PublishedCourse.academic_year' => $acadamic_year,
                        'PublishedCourse.semester' => $semester,
                        'PublishedCourse.section_id' => $section_id,
                        'PublishedCourse.drop' => 0,
                        //'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                    ),
                    'contain' => array('PublishedCourse' => array('Course'))
                ));

                $courses_formated[$section] = array();

                if (!empty($course_list)) {
                    foreach ($course_list as $key => $course) {
                        $course_detail = $this->PublishedCourse->Course->find('all', array(
                            'fields' => array('Course.course_title', 'Course.course_code'),
                            'conditions' => array(
                                'Course.id' => $course['CourseInstructorAssignment']['published_course_id']
                            ),
                            'recursive' => 0,
                            'contain' => array()
                        ));

                        $courses_formated[$section][$course['PublishedCourse']['id']] = ((trim($course['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course['PublishedCourse']['Course']['course_code'])) . ')');
                    }
                }
            }
        }

        if ($return_select_box == 1) {
            $published_courses_combo = "";
            if (!empty($courses_formated)) {
                $published_courses_combo .= '<option value="">[ Select Course ]</option>';
            } else {
                $published_courses_combo .= '<option value="">[ No Assigned Courses Found, Try Changing Filters ]</option>';
            }
            if (count($courses_formated) > 0) {
                foreach ($courses_formated as $id => $course) {
                    $published_courses_combo .= "<optgroup label='" . $id . "'>";
                    foreach ($course as $key => $value) {
                        $published_courses_combo .= "<option value='" . $key . "'>" . $value . "</option>";
                    }
                    $published_courses_combo .= "</optgroup>";
                }
            }
            return $published_courses_combo;
        } else {
            return $courses_formated;
        }
    }

    function listOfFxCoursesInstructorAssignedBySection($acadamic_year = null, $semester = null, $instructor_id = null, $return_select_box = 0)
    {
        $list_of_sections = $this->listOfSectionInstructorAssigned($acadamic_year, $semester, $instructor_id);
        $courses_formated = array();

        if (!empty($list_of_sections)) {
            foreach ($list_of_sections as $section_id => $section) {
                $course_list = $this->find('all', array(
                    'conditions' => array(
                        'CourseInstructorAssignment.staff_id' => $instructor_id,
                        'CourseInstructorAssignment.isprimary' => 1,
                        'PublishedCourse.academic_year' => $acadamic_year,
                        'PublishedCourse.semester' => $semester,
                        'PublishedCourse.section_id' => $section_id,
                        'PublishedCourse.drop' => 0,
                    ),
                    'contain' => array('PublishedCourse' => array('Course'))
                ));
                //$courses_formated[$section] = array();

                if (!empty($course_list)) {
                    foreach ($course_list as $key => $course) {
                        //check if course is assigned , only assigned one will be here
                        if (ClassRegistry::init('MakeupExam')->assignedMakeup($course['CourseInstructorAssignment']['published_course_id'])) {
                            $course_detail = $this->PublishedCourse->Course->find('all', array(
                                'fields' => array('Course.course_title', 'Course.course_code'),
                                'conditions' => array(
                                    'Course.id' => $course['CourseInstructorAssignment']['published_course_id']
                                ),
                                'recursive' => 0,
                                'contain' => array()
                            ));

                            $courses_formated[$section][$course['PublishedCourse']['id']] = ((trim($course['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course['PublishedCourse']['Course']['course_code'])) . ')');
                        }
                    }
                }
            }
        }

        if ($return_select_box == 1) {
            $published_courses_combo = "";
            if (!empty($courses_formated)) {
                $published_courses_combo .= '<option value="">[ Select Course ]</option>';
            } else {
                $published_courses_combo .= '<option value="">[ Select Academic Year & Semester ]</option>';
            }
            if (count($courses_formated) > 0) {
                foreach ($courses_formated as $id => $course) {
                    $published_courses_combo .= "<optgroup label='" . $id . "'>";
                    foreach ($course as $key => $value) {
                        $published_courses_combo .= "<option value='" . $key . "'>" . $value . "</option>";
                    }
                    $published_courses_combo .= "</optgroup>";
                }
            }
            return $published_courses_combo;
        } else {
            return $courses_formated;
        }
    }

    function listOfAssignedGradeEntryAssignedBySection($acadamic_year = null, $semester = null, $instructor_id = null, $return_select_box = 0)
    {
        $list_of_sections = $this->listOfSectionInstructorAssigned($acadamic_year, $semester, $instructor_id);
        $courses_formated = array();

        if (!empty($list_of_sections)) {
            foreach ($list_of_sections as $section_id => $section) {
                $course_list = $this->find('all', array(
                    'conditions' => array(
                        'CourseInstructorAssignment.staff_id' => $instructor_id,
                        'CourseInstructorAssignment.isprimary' => 1,
                        'PublishedCourse.academic_year' => $acadamic_year,
                        'PublishedCourse.semester' => $semester,
                        'PublishedCourse.section_id' => $section_id,
                        'PublishedCourse.drop' => 0,
                    ),
                    'contain' => array('PublishedCourse' => array('Course'))
                ));
                //$courses_formated[$section] = array();

                if (!empty($course_list)) {
                    foreach ($course_list as $key => $course) {
                        //check if course is assigned , only assigned one will be here
                        if (ClassRegistry::init('ResultEntryAssignment')->assignedResultEntry($course['CourseInstructorAssignment']['published_course_id'])) {
                            $courses_formated[$section][$course['PublishedCourse']['id']] = ((trim($course['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course['PublishedCourse']['Course']['course_code'])) . ')');
                        }
                    }
                }
            }
        }

        if ($return_select_box == 1) {
            $published_courses_combo = "";
            if (!empty($courses_formated)) {
                $published_courses_combo .= '<option value="">[ Select Course ]</option>';
            } else {
                $published_courses_combo .= '<option value="">[ No Assigned Courses Found, Try Changing Filters ]</option>';
            }
            if (count($courses_formated) > 0) {
                foreach ($courses_formated as $id => $course) {
                    $published_courses_combo .= "<optgroup label='" . $id . "'>";
                    foreach ($course as $key => $value) {
                        $published_courses_combo .= "<option value='" . $key . "'>" . $value . "</option>";
                    }
                    $published_courses_combo .= "</optgroup>";
                }
            }
            return $published_courses_combo;
        } else {
            return $courses_formated;
        }
    }

    function listOfCoursesSectionsTakingOrgBySection($department_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_id = null, $selectible_section = 0, $yearLevel = null)
    {
        $yearLevelId = null;

        if (!empty($yearLevel) && !empty($department_id)) {
            $yearLevelId = $this->PublishedCourse->YearLevel->field('id', array(
                'YearLevel.department_id' => $department_id,
                'YearLevel.name' => $yearLevel
            ));
        }

        $published_courses_by_section = $this->listOfDepartmentSections($department_id, $acadamic_year, $semester, $program_id, $program_type_id, $yearLevelId);

        $organized_Published_courses_by_sections = array();

        if ($selectible_section == 0) {
            if (!empty($published_courses_by_section)) {
                foreach ($published_courses_by_section as $pc_key => $published_course) {
                    $organized_Published_courses_by_sections[$published_course['Section']['name'] . ' (' . (isset($published_course['Section']['YearLevel']['name']) ? $published_course['Section']['YearLevel']['name'] : (isset($published_course['PublishedCourse']['YearLevel']['name']) ? $published_course['PublishedCourse']['YearLevel']['name'] : ($published_course['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'))) . ', ' . (isset($published_course['Section']['academicyear']) ? $published_course['Section']['academicyear'] : $published_course['PublishedCourse']['academic_year']) . ')'][$published_course['PublishedCourse']['id']] = ((trim($published_course['Course']['course_title'])) . ' (' . (trim($published_course['Course']['course_code'])) . ')');
                }
            }
        } else {
            if (!empty($published_courses_by_section)) {
                foreach ($published_courses_by_section as $pc_key => $published_course) {
                    $organized_Published_courses_by_sections['s~' . $published_course['Section']['id']] = $published_course['Section']['name'] . ' (' . (isset($published_course['Section']['YearLevel']['name']) ? $published_course['Section']['YearLevel']['name'] : (isset($published_course['PublishedCourse']['YearLevel']['name']) ? $published_course['PublishedCourse']['YearLevel']['name'] : ($published_course['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'))) . ', ' . (isset($published_course['Section']['academicyear']) ? $published_course['Section']['academicyear'] : $published_course['PublishedCourse']['academic_year']) . ')';
                    $organized_Published_courses_by_sections[$published_course['PublishedCourse']['id']] = ' --> ' . (trim($published_course['Course']['course_title'])) . ' (' . (trim($published_course['Course']['course_code'])) . ')';
                }
            }
        }

        return $organized_Published_courses_by_sections;
    }

    function listOfCollegeFreshmanSections($college_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_id = null, $given_by_department_id = null)
    {
        if (!empty($given_by_department_id)) {
            $published_courses_by_section = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.college_id' => $college_id,
                    'Section.department_id IS NULL',
                    'Section.program_id' => $program_id,
                    'Section.program_type_id' => $program_type_id,
                    'Section.id IN (SELECT DISTINCT section_id FROM published_courses AS PublishedCourse WHERE PublishedCourse.department_id IS NULL AND PublishedCourse.academic_year = \'' . $acadamic_year . '\' AND PublishedCourse.semester = \'' . $semester . '\' and PublishedCourse.given_by_department_id=' . $given_by_department_id . ')'
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course',
                        'College',
                        'GivenByDepartment',
                        'YearLevel' => array('id', 'name'),
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'Program' => array('id', 'name', 'shortname'),
                        'conditions' => array(
                            'PublishedCourse.given_by_department_id' => $given_by_department_id,
                            'PublishedCourse.academic_year' => $acadamic_year,
                            'PublishedCourse.semester' => $semester,
                            'PublishedCourse.drop' => 0,
                        ),
                        'order' => array('PublishedCourse.academic_year' => 'DESC', 'PublishedCourse.semester' => 'ASC', 'PublishedCourse.program_id' => 'ASC', 'PublishedCourse.program_type_id' => 'ASC', 'PublishedCourse.year_level_id' => 'ASC', 'PublishedCourse.section_id' => 'ASC', 'PublishedCourse.course_id' => 'ASC', 'PublishedCourse.id' => 'DESC'),
                    ),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name', 'shortname'),
                    'Program' => array('id', 'name', 'shortname'),
                    'College',
                    'Department'
                )
            ));
        } else {
            $published_courses_by_section = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.college_id' => $college_id,
                    'Section.department_id IS NULL',
                    'Section.program_id' => $program_id,
                    'Section.program_type_id' => $program_type_id,
                    'Section.id IN (SELECT DISTINCT section_id FROM published_courses AS PublishedCourse WHERE PublishedCourse.department_id IS NULL AND PublishedCourse.academic_year = \'' . $acadamic_year . '\' AND PublishedCourse.semester = \'' . $semester . '\')'
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course',
                        'College',
                        'GivenByDepartment',
                        'YearLevel' => array('id', 'name'),
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'Program' => array('id', 'name', 'shortname'),
                        'conditions' => array(
                            'PublishedCourse.academic_year' => $acadamic_year,
                            'PublishedCourse.semester' => $semester,
                            'PublishedCourse.drop' => 0,
                        ),
                        'order' => array('PublishedCourse.academic_year' => 'DESC', 'PublishedCourse.semester' => 'ASC', 'PublishedCourse.program_id' => 'ASC', 'PublishedCourse.program_type_id' => 'ASC', 'PublishedCourse.year_level_id' => 'ASC', 'PublishedCourse.section_id' => 'ASC', 'PublishedCourse.course_id' => 'ASC', 'PublishedCourse.id' => 'DESC'),
                    ),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name', 'shortname'),
                    'Program' => array('id', 'name', 'shortname'),
                    'College',
                    'Department'
                )
            ));
        }

        return $published_courses_by_section;
    }

    function listOfCoursesCollegeFreshTakingOrgBySection($college_id = null, $acadamic_year = null, $semester = null, $program_id = null, $program_type_id = null, $selectible_section = 0, $given_by_department_id = null)
    {
        $published_courses_by_section = $this->listOfCollegeFreshmanSections($college_id, $acadamic_year, $semester, $program_id, $program_type_id, $given_by_department_id);
        $organized_Published_courses_by_sections = array();

        if ($selectible_section == 0) {
            if (!empty($published_courses_by_section)) {
                foreach ($published_courses_by_section as $key => $published_course_by_section) {
                    /* $organized_Published_courses_by_sections[$published_course_by_section['Section']['name']] = array(); */
                    foreach ($published_course_by_section['PublishedCourse'] as $pc_key => $published_course) {
                        $organized_Published_courses_by_sections[$published_course_by_section['Section']['name'] . ' (Pre/1st, ' . $published_course_by_section['Section']['academicyear'] . ')'][$published_course['id']] = ((trim(
                                $published_course['Course']['course_title']
                            )) . ' (' . (trim($published_course['Course']['course_code'])) . ')');
                    }
                }
            }
        } else {
            if (!empty($published_courses_by_section)) {
                foreach ($published_courses_by_section as $key => $published_course_by_section) {
                    $organized_Published_courses_by_sections['s~' . $published_course_by_section['Section']['id']] = $published_course_by_section['Section']['name'];
                    foreach ($published_course_by_section['PublishedCourse'] as $pc_key => $published_course) {
                        $organized_Published_courses_by_sections[$published_course['id']] = ' --> ' . (trim($published_course['Course']['course_title'])) . ' (' . (trim($published_course['Course']['course_code'])) . ')';
                    }
                }
            }
        }

        return $organized_Published_courses_by_sections;
    }

    function instructorLoadOrganizedByAcademicYearAndSemester($data = null)
    {
        $organized_loads_of_instructor = array();
        if (!empty($data)) {
            foreach ($data as $key => $assigned_courses) {
                $organized_loads_of_instructor[$assigned_courses['PublishedCourse']['academic_year']][$assigned_courses['PublishedCourse']['semester']][] = $assigned_courses;
            }
        }
        return $organized_loads_of_instructor;
    }

    function organized_published_courses_by_program_sections($publishedCourses = null)
    {
        $organized_published_courses = array();

        if (!empty($publishedCourses)) {
            foreach ($publishedCourses as $in => &$value) {
                if (!empty($value['Program']['id']) && !empty($value['ProgramType']['id']) && !empty($value['ProgramType']['id']) && !empty($value['Section']['id'])) {
                    if (isset($value['PublishedCourse']['given_by_department_id']) && !empty($value['PublishedCourse']['given_by_department_id'])) {
                        $value['departments'] = $this->PublishedCourse->Department->find('list', array(
                            'conditions' => array(
                                'Department.college_id' => $value['GivenByDepartment']['college_id'],
                                'Department.active' => 1
                            )
                        ));
                    }

                    if (!empty($value['YearLevel']['id'])) {
                        $organized_published_courses[$value['Program']['name']][$value['ProgramType']['name']][$value['YearLevel']['name']][$value['Section']['name']][] = $value;
                    } else {
                        $organized_published_courses[$value['Program']['name']][$value['ProgramType']['name']]['Pre/1st'][$value['Section']['name']][] = $value;
                    }
                }
            }
        }
        return $organized_published_courses;
    }

    function organized_Published_courses_by_for_assignment($publishedcourses = null)
    {
        $sections_array = array();
        $course_type_array = array();

        if (!empty($publishedcourses)) {
            foreach ($publishedcourses as $key => $publishedcourse) {
                $department_name = null;
                $year_level_name = null;

                if (!empty($publishedcourse['PublishedCourse']['department_id'])) {
                    $department_name = $publishedcourse['Department']['name'];
                    $year_level_name = $publishedcourse['YearLevel']['name'];
                } elseif (empty($publishedcourse['PublishedCourse']['department_id']) && !empty($publishedcourse['PublishedCourse']['college_id'])) {
                    $department_name = $publishedcourse['College']['name'];
                    if ($publishedcourse['Program']['id'] == PROGRAM_REMEDIAL) {
                        $year_level_name = 'Remedial';
                    } else {
                        $year_level_name = 'Pre/1st';
                    }
                }

                if (!empty($publishedcourse['SectionSplitForPublishedCourse'])) {
                    foreach ($publishedcourse['SectionSplitForPublishedCourse'][0]['CourseSplitSection'] as $split_section_for_course) {
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['course_title'] = $publishedcourse['Course']['course_title'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['course_id'] = $publishedcourse['Course']['id'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['course_code'] = $publishedcourse['Course']['course_code'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['credit'] = $publishedcourse['Course']['credit'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' . $publishedcourse['Course']['laboratory_hours'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['published_course_id'] = $publishedcourse['PublishedCourse']['id'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['grade_submitted'] =

                            $this->PublishedCourse->CourseRegistration->ExamGrade->is_grade_submitted($publishedcourse['PublishedCourse']['id']);

                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['course_split_section_id'] = $split_section_for_course['id'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['section_id']  =  $publishedcourse['PublishedCourse']['section_id'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['published_course_id'] =  $publishedcourse['PublishedCourse']['id'];
                        $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['given_by_department_id']  =  $publishedcourse['PublishedCourse']['given_by_department_id'];

                        if (!empty($publishedcourse['CourseInstructorAssignment'])) {
                            foreach ($publishedcourse['CourseInstructorAssignment'] as $askey => $assign_instructor) {
                                if ($split_section_for_course['id'] == $assign_instructor['course_split_section_id']) {
                                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['full_name'] = ((isset($assign_instructor['Staff']['Title']['title']) ? $assign_instructor['Staff']['Title']['title'] . ' ' : '') . (!empty($assign_instructor['Staff']['full_name']) ? $assign_instructor['Staff']['full_name'] : ''));
                                    $sections_array[$publishedcourse['Department']['name']][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['position'] = (isset($assign_instructor['Staff']['Position']['position']) ? $assign_instructor['Staff']['Position']['position'] : '');
                                    $sections_array[$publishedcourse['Department']['name']][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['course_type'] = $assign_instructor['type'];
                                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]['assign_instructor'][$assign_instructor['isprimary']][$askey]['CourseInstructorAssignment_id'] = $assign_instructor['id'];
                                }
                            }
                        }

                        if ($publishedcourse['Course']['lecture_hours'] > 0) {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]["Lecture"] = "Lecture";
                            if ($publishedcourse['Course']['tutorial_hours'] > 0 && $publishedcourse['Course']['laboratory_hours'] > 0) {
                                $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']][$split_section_for_course['section_name']]["Lecture+Tutorial+Lab"] = "Lect.+Tut.+Lab";
                            }
                        }

                        if ($publishedcourse['Course']['tutorial_hours'] > 0) {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]["tutorial"] = "Tutorial";
                            if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]["Lecture+Tutorial"] = "Lect.+Tut.";
                            }
                        } elseif ($publishedcourse['Course']['laboratory_hours'] > 0) {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]["Lab"] = "Lab";
                            if ($publishedcourse['Course']['lecture_hours'] > 0) {
                                $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]["Lecture+Lab"] = "Lec.+Lab";
                            }
                        } else {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$split_section_for_course['section_name']]["Other"] = "Other";
                        }
                    }
                } else {
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['course_title'] = $publishedcourse['Course']['course_title'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['course_id'] = $publishedcourse['Course']['id'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['course_code'] = $publishedcourse['Course']['course_code'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['credit'] = $publishedcourse['Course']['credit'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' . $publishedcourse['Course']['laboratory_hours'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['section_id'] = $publishedcourse['PublishedCourse']['section_id'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['published_course_id'] = $publishedcourse['PublishedCourse']['id'];
                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['given_by_department_id'] = $publishedcourse['PublishedCourse']['given_by_department_id'];

                    $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['grade_submitted'] = $this->PublishedCourse->CourseRegistration->ExamGrade->is_grade_submitted($publishedcourse['PublishedCourse']['id']);

                    if (!empty($publishedcourse['CourseInstructorAssignment'])) {
                        foreach ($publishedcourse['CourseInstructorAssignment'] as $askey => $assign_instructor) {
                            $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['full_name'] = ((isset($assign_instructor['Staff']['Title']['title']) ? $assign_instructor['Staff']['Title']['title'] . ' ' : '') . (!empty($assign_instructor['Staff']['full_name']) ? $assign_instructor['Staff']['full_name'] : ''));
                            $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['position'] = (isset($assign_instructor['Staff']['Position']['position']) ? $assign_instructor['Staff']['Position']['position'] : '');
                            $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['course_type'] = $assign_instructor['type'];
                            $sections_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]['assign_instructor'][$assign_instructor['isprimary']][$askey]['CourseInstructorAssignment_id'] = $assign_instructor['id'];
                        }
                    }

                    //$course_type_array[$key][-1] = "---Select---";

                    if ($publishedcourse['Course']['lecture_hours'] > 0) {
                        $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Lecture"] = "Lecture";
                        if ($publishedcourse['Course']['tutorial_hours'] > 0 && $publishedcourse['Course']['laboratory_hours'] > 0) {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Lecture+Tutorial+Lab"] = "Lect.+Tut.+Lab";
                        }
                    }

                    if ($publishedcourse['Course']['tutorial_hours'] > 0) {
                        $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["tutorial"] = "Tutorial";
                        if ($publishedcourse['Course']['lecture_hours'] > 0) {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Lecture+Tutorial"] = "Lect.+Tut.";
                            if ($publishedcourse['Course']['id'] == 550) {
                                debug($course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Lecture+Tutorial"]);
                            }
                        }
                    }

                    if ($publishedcourse['Course']['laboratory_hours'] > 0) {
                        $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Lab"] = "Lab";
                        if ($publishedcourse['Course']['lecture_hours'] > 0) {
                            $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Lecture+Lab"] = "Lect.+Lab";
                        }
                    } else {
                        $course_type_array[$department_name][$publishedcourse['Program']['name']][$publishedcourse['ProgramType']['name']][$year_level_name][$publishedcourse['Section']['name'] . ' (' . $year_level_name  . ', ' . $publishedcourse['Section']['academicyear'] . ')'][$key]["Other"] = "Other";
                    }
                }
            }
        }

        $course_return['sections_array'] = $sections_array;
        $course_return['course_type_array'] = $course_type_array;

        return $course_return;
    }

    function getDisptachedCoursesForNotification($department_id = null)
    {

        $dispatchedCourseLists = array();

        $publishedOn = date('Y-m-d ', strtotime("-" . DAYS_BACK_DISPATCHED_NOTIFICATION . " day "));

        $dispatched_detail = $this->PublishedCourse->find('all', array(
            'conditions' => array(
                'PublishedCourse.given_by_department_id' => $department_id,
                //'PublishedCourse.department_id <>'=>$department_id,
                'PublishedCourse.id NOT IN (select published_course_id from course_instructor_assignments)',
                'PublishedCourse.created >= ' => $publishedOn
            ),
            'contain' => array(
                'CourseInstructorAssignment',
                'Department',
                'College',
                'Section',
                'YearLevel',
                'GivenByDepartment',
                'Course',
                'Program',
                'ProgramType'
            )
        ));

        if (!empty($dispatched_detail)) {
            foreach ($dispatched_detail as $k => $v) {
                $gradeSubmitted = ClassRegistry::init('ExamGrade')->is_grade_submitted($v['PublishedCourse']['id']);

                if ($gradeSubmitted == 0 && $v['PublishedCourse']['department_id'] != $department_id) {
                    $dispatchedCourseLists[] = $v;
                }
            }
        }

        return  $dispatchedCourseLists;
    }

    function getDisptachedCoursesNotAssigned($department_id = null)
    {
        $dispatchedCourseLists = array();

        $publishedOn = date('Y-m-d ', strtotime("-" . DAYS_BACK_DISPATCHED_NOTIFICATION . " day "));

        $dispatched_detail = $this->PublishedCourse->find('all', array(
            'conditions' => array(
                'PublishedCourse.given_by_department_id <> ' => $department_id,
                'PublishedCourse.department_id' => $department_id,
                'PublishedCourse.id NOT IN (select published_course_id from course_instructor_assignments)',
                'PublishedCourse.created >= ' => $publishedOn
            ),
            'contain' => array(
                'CourseInstructorAssignment',
                'Department',
                'College',
                'GivenByDepartment',
                'Section',
                'YearLevel',
                'Course',
                'Program',
                'ProgramType'
            )
        ));

        if (!empty($dispatched_detail)) {
            foreach ($dispatched_detail as $k => $v) {
                $gradeSubmitted = ClassRegistry::init('ExamGrade')->is_grade_submitted($v['PublishedCourse']['id']);
                if (empty($gradeSubmitted)) {
                    $dispatchedCourseLists[] = $v;
                }
            }
        }

        return  $dispatched_detail;
    }

    function getGradeSubmissionStat($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null)
    {

        $academicCalendarOptions = array();
        $instructorAssignmentOptions = array();

        $instructorAssignmentOptions['conditions']['PublishedCourse.drop'] = 0;

        if (isset($acadamic_year) && isset($semester)) {
            $instructorAssignmentOptions['limit'] = 50000;

            $academicCalendarOptions['conditions']['AcademicCalendar.academic_year'] = $acadamic_year;
            $academicCalendarOptions['conditions']['AcademicCalendar.semester'] = $semester;

            $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.academic_year'] = $acadamic_year;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.semester'] = $semester;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.isprimary'] = 1;
            //$instructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where semester="' . $semester . '" and academic_year="' . $acadamic_year . '")';
            $instructorAssignmentOptions['conditions']['PublishedCourse.academic_year'] = $acadamic_year;
            $instructorAssignmentOptions['conditions']['PublishedCourse.semester'] = $semester;
        }

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.program_type_id'] = $program_type_id;
            //$instructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where and program_type_id=' . $program_type_id . ')';
            $instructorAssignmentOptions['conditions']['PublishedCourse.program_type_id'] = $program_type_id;
        }

        if ($program_id != 0 && !empty($program_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.program_id'] = $program_id;
            //$instructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where program_id="' . $program_id . '")';
            $instructorAssignmentOptions['conditions']['PublishedCourse.program_id'] = $program_id;
        }


        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                //$instructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where drop = 0 and college_id=' . $college_id[1] . ')';
                $instructorAssignmentOptions['conditions']['PublishedCourse.college_id'] = $college_id[1];
            } else {
                debug($department_id);
                //$instructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where drop = 0 and department_id=' . $department_id . ')';
                $instructorAssignmentOptions['conditions']['PublishedCourse.department_id'] = $department_id;
            }
        }

        debug($instructorAssignmentOptions);


        $instructorAssignmentOptions['contain'] = array(
            'Staff' => array(
                'fields' => array(
                    'id',
                    'full_name',
                    'first_name',
                    'middle_name',
                    'last_name'
                ),
                'Title' => array('id', 'title'),
                'College' => array('id', 'name'),
                'Department' => array('id', 'name'),
            ),
            'PublishedCourse' => array(
                'Program' => array('id', 'name'),
                'YearLevel' => array('id', 'name'),
                'ProgramType' => array('id', 'name'),
                'Department' => array('id', 'name'),
                'College' => array('id', 'name'),
                'Course' => array('course_title', 'course_code')
            )
        );


        $academicCalendarOptions['contain'] = array(
            'Program' => array(
                'fields' => array(
                    'id',
                    'name'
                )
            ),
            'ProgramType' => array(
                'fields' => array(
                    'id',
                    'name'
                )
            ),
        );

        $academicCalendarDetails = ClassRegistry::init('AcademicCalendar')->find('all', $academicCalendarOptions);

        $gradeSubmissionDateOfYearLevel = array();

        debug($academicCalendarDetails);

        if (!empty($academicCalendarDetails)) {
            foreach ($academicCalendarDetails as $k => $value) {
                $department_ids = unserialize($value['AcademicCalendar']['department_id']);
                $year_level_ids = unserialize($value['AcademicCalendar']['year_level_id']);

                //debug($department_ids);

                $pre_c = array();

                if (!empty($year_level_ids) && !empty($department_ids)) {
                    foreach ($year_level_ids as $yk => $yv) {
                        foreach ($department_ids as $dpk => $dpv) {
                            $pre_college_ids = explode('pre_', $dpv);
                            if (count($pre_college_ids) > 1) {
                                array_push($pre_c, $pre_college_ids[1]);
                                debug($yv);
                                debug($pre_college_ids[1]);
                            }

                            $gradeSubmissionDateOfYearLevel[$yv][$dpv]['grade_submission_end_date'] = $value['AcademicCalendar']['grade_submission_end_date'];
                        }
                    }
                }
            }
        }

        $reformattedGradeSubmissionStat = array();

        if (empty($gradeSubmissionDateOfYearLevel)) {
            return $reformattedGradeSubmissionStat;
        }

        $assignmentList = $this->find('all', $instructorAssignmentOptions);

        //debug($assignmentList);

        $noDaysDelayed = 0;

        if (!empty($assignmentList)) {
            foreach ($assignmentList as $kk => $kkvalue) {
                $gradeSubmitteddate = ClassRegistry::init('ExamGrade')->getGradeSubmmissionDate($kkvalue['CourseInstructorAssignment']['published_course_id']);
                debug($gradeSubmitteddate);

                // if grade is not submitted, check if the deadline is passed against the current time, if it does calculate the number of days passed
                if (empty($gradeSubmitteddate)) {
                    if (isset($kkvalue['PublishedCourse']['YearLevel']['name']) && !empty($kkvalue['PublishedCourse']['YearLevel']['name'])) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'];

                        debug($kkvalue['PublishedCourse']['department_id']);

                        if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                            $current_date = date('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = date('Y-m-d H:i:s', strtotime($grade_submission_end_date));

                            if ($grade_submission_end_date > $current_date) {
                                $noDaysDelayed = 0;
                            } else {
                                // $reformattedGradeSubmissionStat=$this->TimeAgoFormat($grade_submission_end_date);
                                $noDaysDelayed = $this->TimeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                debug($noDaysDelayed);

                                $reformattedGradeSubmissionStat[$kkvalue['PublishedCourse']['Program']['name']][$kkvalue['PublishedCourse']['ProgramType']['name']][$kkvalue['Staff']['College']['name']][$kkvalue['Staff']['Department']['name']][$kkvalue['Staff']['full_name']][$kkvalue['PublishedCourse']['Course']['course_title'] . " (" . $kkvalue['PublishedCourse']['Course']['course_code'] . ")"]['noDaysDelayed'] = $noDaysDelayed;
                            }
                        }
                    } else {
                        // preengineering
                        if (isset($kkvalue['PublishedCourse']['College']['id']) && !empty($kkvalue['PublishedCourse']['College']['id'])) {
                            $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue['PublishedCourse']['College']['id']]['grade_submission_end_date'];

                            $current_date = date('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = date('Y-m-d H:i:s', strtotime($grade_submission_end_date));

                            if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                                if ($grade_submission_end_date > $current_date) {
                                    $noDaysDelayed = 0;
                                } else {
                                    $noDaysDelayed = $this->TimeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                    $reformattedGradeSubmissionStat[$kkvalue['PublishedCourse']['Program']['name']][$kkvalue['PublishedCourse']['ProgramType']['name']][$kkvalue['Staff']['College']['name']][$kkvalue['Staff']['Department']['name']][$kkvalue['Staff']['full_name']][$kkvalue['PublishedCourse']['Course']['course_title'] . " (" . $kkvalue['PublishedCourse']['Course']['course_code'] . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                }
                            }
                        }
                    }
                }

                // if grade is submitted, check the submitted date, with the deadline, if
                // the submitted date is less than the deadline, it is fine, nothing to do

                if (!empty($gradeSubmitteddate)) {
                    if (isset($kkvalue['PublishedCourse']['YearLevel']['name']) && !empty($kkvalue['PublishedCourse']['YearLevel']['name'])) {
                        if (isset($gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date']) && !empty($gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'])) {
                            $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'];

                            debug($grade_submission_end_date);

                            if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                                $grade_submission_end_date_formatted = date('Y-m-d H:i:s', strtotime($grade_submission_end_date));

                                if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                                    $noDaysDelayed = 0;
                                } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                                    // $reformattedGradeSubmissionStat=$this->TimeAgoFormat($grade_submission_end_date);
                                    $noDaysDelayed = $this->TimeAgoFormat($gradeSubmitteddate['ExamGrade']['created'], $grade_submission_end_date_formatted);

                                    $reformattedGradeSubmissionStat[$kkvalue['PublishedCourse']['Program']['name']][$kkvalue['PublishedCourse']['ProgramType']['name']][$kkvalue['Staff']['College']['name']][$kkvalue['Staff']['Department']['name']][$kkvalue['Staff']['full_name']][$kkvalue['PublishedCourse']['Course']['course_title'] . " (" . $kkvalue['PublishedCourse']['Course']['course_code'] . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                }
                            }
                        }
                    } else {
                        // preengineering
                        if (isset($kkvalue['PublishedCourse']['College']['id']) && !empty($kkvalue['PublishedCourse']['College']['id'])) {
                            $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue['PublishedCourse']['College']['id']]['grade_submission_end_date'];


                            if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                                $grade_submission_end_date_formatted = date('Y-m-d H:i:s', strtotime($grade_submission_end_date));
                                if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                                    $noDaysDelayed = 0;
                                } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                                    $noDaysDelayed = $this->TimeAgoFormat($gradeSubmitteddate['ExamGrade']['created'], $grade_submission_end_date_formatted);
                                    $reformattedGradeSubmissionStat[$kkvalue['PublishedCourse']['Program']['name']][$kkvalue['PublishedCourse']['ProgramType']['name']][$kkvalue['Staff']['College']['name']][$kkvalue['Staff']['Department']['name']][$kkvalue['Staff']['full_name']][$kkvalue['PublishedCourse']['Course']['course_title'] . " (" . $kkvalue['PublishedCourse']['Course']['course_code'] . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                }
                            }
                        }
                    }
                }

                // if the grade submitted is greater than grade submission deadline, calculate the number of days delyed after the deadline passed
            }
        }

        //  debug($reformattedGradeSubmissionStat);
        return $reformattedGradeSubmissionStat;
    }


    function getGradeSubmissionDelayStat($acadamic_year, $semester, $program_id = null, $program_type_id = null, $department_id = null)
    {

        $academicCalendarOptions = array();
        $instructorAssignmentOptions = array();

        $instructorAssignmentOptions['conditions']['PublishedCourse.drop'] = 0;

        if (isset($acadamic_year) && isset($semester)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.academic_year'] = $acadamic_year;
            $academicCalendarOptions['conditions']['AcademicCalendar.semester'] = $semester;

            $instructorAssignmentOptions['limit'] = 3000000000;

            $instructorAssignmentOptions['conditions']['PublishedCourse.academic_year'] = $acadamic_year;
            $instructorAssignmentOptions['conditions']['PublishedCourse.semester'] = $semester;
        }

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.program_type_id'] = $program_type_id;
            $instructorAssignmentOptions['conditions']['PublishedCourse.program_type_id'] = $program_type_id;
        }

        if ($program_id != 0 && !empty($program_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.program_id'] = $program_id;
            $instructorAssignmentOptions['conditions']['PublishedCourse.program_id'] = $program_id;
        }


        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);

            if (count($college_id) > 1) {
                $departmentLists = ClassRegistry::init('Department')->find('list', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    )
                ));

                if (!empty($departmentLists)) {
                    $instructorAssignmentOptions['conditions']['PublishedCourse.department_id'] = array_keys($departmentLists);
                } else {
                    $instructorAssignmentOptions['conditions']['PublishedCourse.college_id'] = $college_id[1];
                }
            } else {
                $instructorAssignmentOptions['conditions']['PublishedCourse.department_id'] = $department_id;
            }
        }

        debug($instructorAssignmentOptions);

        $instructorAssignmentOptions['contain'] = array(
            'CourseInstructorAssignment' => array(
                'Staff' => array(
                    'fields' => array(
                        'id',
                        'full_name',
                        'first_name',
                        'middle_name',
                        'last_name'
                    ),
                    'Title' => array('id', 'title'),
                    'College' => array('id', 'name'),
                    'Department' => array('id', 'name'),
                ),
                'conditions' => array(
                    'CourseInstructorAssignment.isprimary' => 1
                )
            ),
            'Program' => array('id', 'name'),
            'Section' => array('id', 'name'),
            'YearLevel' => array('id', 'name'),
            'ProgramType' => array('id', 'name'),
            'Department' => array('id', 'name'),
            'College' => array('id', 'name'),
            'Course' => array('id', 'course_title', 'course_code')
        );

        debug($instructorAssignmentOptions);

        $academicCalendarOptions['contain'] = array(
            'Program' => array(
                'fields' => array('id','name')
            ),
            'ProgramType' => array(
                'fields' => array('id', 'name')
            ),
        );

        $academicCalendarDetails = ClassRegistry::init('AcademicCalendar')->find('all', $academicCalendarOptions);

        $gradeSubmissionDateOfYearLevel = array();

        if (!empty($academicCalendarDetails)) {
            foreach ($academicCalendarDetails as $k => $value) {
                $department_ids = unserialize($value['AcademicCalendar']['department_id']);
                $year_level_ids = unserialize($value['AcademicCalendar']['year_level_id']);
                foreach ($year_level_ids as $yk => $yv) {
                    foreach ($department_ids as $dpk => $dpv) {
                        $gradeSubmissionDateOfYearLevel[$yv][$dpv]['grade_submission_end_date'] = $value['AcademicCalendar']['grade_submission_end_date'];
                    }
                }
            }
        }


        $reformattedGradeSubmissionStat = array();

        if (empty($gradeSubmissionDateOfYearLevel)) {
            return $reformattedGradeSubmissionStat;
        }

        $assignmentList = $this->PublishedCourse->find('all', $instructorAssignmentOptions);

        $noDaysDelayed = 0;

        if (!empty($assignmentList)) {
            foreach ($assignmentList as $kk => $kkvalue) {
                $gradeSubmitteddate = ClassRegistry::init('ExamGrade')->getGradeSubmmissionDate($kkvalue['PublishedCourse']['id']);

                // if grade is not submitted, check if the deadline is passed against the current time, if it does calculate the number of days elapsed
                if (empty($gradeSubmitteddate)) {
                    if (isset($kkvalue['YearLevel']['name']) && !empty($kkvalue['YearLevel']['name'])) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'];

                        if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                            $current_date = date('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = date('Y-m-d H:i:s', strtotime($grade_submission_end_date));
                            debug($grade_submission_end_date);

                            if ($grade_submission_end_date > $current_date) {
                                $noDaysDelayed = 0;
                            } else {
                                $noDaysDelayed = $this->TimeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                if (isset($kkvalue['CourseInstructorAssignment'][0]) && !empty($kkvalue['CourseInstructorAssignment'][0]['isprimary'])) {
                                    $reformattedGradeSubmissionStat[$kkvalue['Program']['name']][$kkvalue['ProgramType']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['College']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['Department']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['full_name']][$kkvalue['Course']['course_title'] . " (" . $kkvalue['Course']['course_code'] . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                    $reformattedGradeSubmissionStat[$kkvalue['Program']['name']][$kkvalue['ProgramType']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['College']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['Department']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['full_name']][$kkvalue['Course']['course_title'] . " (" . $kkvalue['Course']['course_code'] . ")"]['Section'] = $kkvalue['Section']['name'] . '(' . $kkvalue['YearLevel']['name'] . ')';
                                }
                            }
                        }
                    } else {
                        // preengineering
                        if (isset($kkvalue['College']['id']) && !empty($kkvalue['College']['id'])) {
                            $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue['College']['id']]['grade_submission_end_date'];
                            $current_date = date('Y-m-d H:i:s');
                            $grade_submission_end_date_formatted = date('Y-m-d H:i:s', strtotime($grade_submission_end_date));

                            if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                                if ($grade_submission_end_date > $current_date) {
                                    $noDaysDelayed = 0;
                                } else {
                                    $noDaysDelayed = $this->TimeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                    if (isset($kkvalue['CourseInstructorAssignment'][0]) && !empty($kkvalue['CourseInstructorAssignment'][0])) {
                                        $reformattedGradeSubmissionStat[$kkvalue['Program']['name']][$kkvalue['ProgramType']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['College']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['Department']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['full_name']][$kkvalue['Course']['course_title'] . " (" . $kkvalue['Course']['course_code'] . ")"]['noDaysDelayed'] = $noDaysDelayed;
                                        $reformattedGradeSubmissionStat[$kkvalue['Program']['name']][$kkvalue['ProgramType']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['College']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['Department']['name']][$kkvalue['CourseInstructorAssignment'][0]['Staff']['full_name']][$kkvalue['Course']['course_title'] . " (" . $kkvalue['Course']['course_code'] . ")"]['Section'] = $kkvalue['Section']['name'] . '(Pre)';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        debug($reformattedGradeSubmissionStat);

        return $reformattedGradeSubmissionStat;
    }

    public function TimeAgoFormat($time, $grade_submission_date)
    {
        $time_array  =
            array(
                12 * 30 * 24 * 60 * 60 => 'year',
                30 * 24 * 60 * 60     => 'month',
                24 * 60 * 60          => 'day',
                60 * 60               => 'hour',
                60                    => 'minute',
                1                     => 'second'
            );

        $timestamp = strtotime($time);
        $gradeSubmissionDate = strtotime($grade_submission_date);

        if ($timestamp > $gradeSubmissionDate) {
            $time_diff = $timestamp - $gradeSubmissionDate;
        } else {
            $time_diff = $gradeSubmissionDate - $timestamp;
        }

        if ($time_diff < 1) {
            return '0 second';
        }

        foreach ($time_array as $seconds => $str) {
            $time_ago = $time_diff / $seconds;
            if ($time_ago >= 1) {
                $ago     = round($time_ago);
                return $ago . ' ' . $str . ($ago > 1 ? 's' : '');
            }
        }
    }

    function getGradeSubmissionStatNumber(
        $acadamic_year,
        $semester,
        $program_id = null,
        $program_type_id = null,
        $department_id = null
    ) {

        $courseInstructorAssignmentOptions = array();
        $academicCalendarOptions = array();
        $instructorAssignmentOptions = array();
        $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] = 0;
        $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] = 0;
        $reformattedGradeSubmissionStat['Instructor']['totalCourseAssignment'] = 0;
        $courseInstructorAssignmentOptions['recursive'] = -1;
        //$instructorAssignmentOptions['limit']=100000;
        //$courseInstructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where drop=0)';
        $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.isprimary'] = 1;
        $instructorAssignmentOptions['conditions'][] = 'CourseInstructorAssignment.published_course_id in (select id from published_courses where id is not null)';


        if (isset($acadamic_year) && isset($semester)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.academic_year'] = $acadamic_year;
            $academicCalendarOptions['conditions']['AcademicCalendar.semester'] = $semester;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.academic_year'] = $acadamic_year;
            $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.semester'] = $semester;

            // $instructorAssignmentOptions['conditions']['CourseInstructorAssignment.type'] ='Lecture';
            $instructorAssignmentOptions['contain']['PublishedCourse'] = array(
                'conditions' => array(
                    'PublishedCourse.academic_year' => $acadamic_year,
                    'PublishedCourse.semester' => $semester,
                    'PublishedCourse.drop' => 0,
                ),
            );
            /*
              $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignment.academic_year']=$acadamic_year;
              $courseInstructorAssignmentOptions['conditions']['CourseInstructorAssignment.semester']=$semester;
*/
            $courseInstructorAssignmentOptions['conditions'] = array(
                'CourseInstructorAssignment.isprimary' => 1,
                'CourseInstructorAssignment.academic_year' => $acadamic_year,
                'CourseInstructorAssignment.semester' => $semester,
                'CourseInstructorAssignment.published_course_id in (select id from
published_courses where id is not null)'
            );
        }
        debug($courseInstructorAssignmentOptions);
        if ($program_type_id != 0 && !empty($program_type_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.program_type_id'] = $program_type_id;
            $instructorAssignmentOptions['contain']['PublishedCourse'] = array(
                'conditions' => array(
                    'PublishedCourse.program_type_id' => $program_type_id,
                )
            );

            $courseInstructorAssignmentOptions['conditions'] = array(
                'CourseInstructorAssignment.published_course_id in (select id from published_courses where program_type_id=' . $program_type_id . '))'
            );
        }


        if ($program_id != 0 && !empty($program_id)) {
            $academicCalendarOptions['conditions']['AcademicCalendar.program_id'] = $program_id;
            $instructorAssignmentOptions['contain']['PublishedCourse'] = array(
                'conditions' => array(
                    'PublishedCourse.program_id' => $program_id,

                )
            );
            $courseInstructorAssignmentOptions['conditions'] = array(
                'CourseInstructorAssignment.published_course_id in (select id from published_courses where program_id=' . $program_id . '))'
            );
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $instructorAssignmentOptions['contain']['Staff'] = array(
                    'conditions' => array(
                        'Staff.college_id' => $college_id[1],

                    ),
                );
                $courseInstructorAssignmentOptions['conditions'] = array(
                    'CourseInstructorAssignment.staff_id in (select id from staffs where college_id=' . $college_id . ')'
                );
            } else {
                $instructorAssignmentOptions['contain']['Staff'] = array(
                    'conditions' => array(
                        'Staff.department_id' => $department_id,

                    )
                );
                $courseInstructorAssignmentOptions['conditions'] = array(
                    'CourseInstructorAssignment.staff_id in (select id from staffs where department_id=' . $department_id . '))'
                );
            }
        }

        $instructorAssignmentOptions['contain'] = array(
            'Staff' => array(
                'fields' => array(
                    'id', 'full_name', 'first_name', 'middle_name',
                    'last_name'
                ),
                'Title' => array('id', 'title'),

                'College' => array('id', 'name'),
                'Department' => array('id', 'name'),
            ),
            'PublishedCourse' => array(
                'Program' => array('id', 'name'),
                'YearLevel' => array('id', 'name'),
                'ProgramType' => array('id', 'name'),
                'Department' => array('id', 'name'),
                'College' => array('id', 'name'),
                'Course' => array('course_title', 'course_code')
            )
        );
        $academicCalendarOptions['contain'] = array(
            'Program' => array(
                'fields' => array(
                    'id',
                    'name'
                )
            ),
            'ProgramType' => array(
                'fields' => array(
                    'id',
                    'name'
                )
            ),
        );
        $academicCalendarDetails = ClassRegistry::init('AcademicCalendar')->find(
            'all',
            $academicCalendarOptions
        );
        $reformattedGradeSubmissionStat['Instructor']['totalCourseAssignment'] = count(ClassRegistry::init('CourseInstructorAssignment')->find('all', $courseInstructorAssignmentOptions));

        $gradeSubmissionDateOfYearLevel = array();
        foreach ($academicCalendarDetails as $k => $value) {
            $department_ids = unserialize($value['AcademicCalendar']['department_id']);
            $year_level_ids = unserialize($value['AcademicCalendar']['year_level_id']);
            foreach ($year_level_ids as $yk => $yv) {
                foreach ($department_ids as $dpk => $dpv) {
                    $gradeSubmissionDateOfYearLevel[$yv][$dpv]['grade_submission_end_date'] =
                        $value['AcademicCalendar']['grade_submission_end_date'];
                }
            }
        }
        // $reformattedGradeSubmissionStat=array();
        // debug($instructorAssignmentOptions);
        $assignmentList = $this->find('all', $instructorAssignmentOptions);
        debug($this->find('count', $instructorAssignmentOptions));
        if (empty($gradeSubmissionDateOfYearLevel)) {
            return $reformattedGradeSubmissionStat;
        }

        $noDaysDelayed = 0;
        $count = 0;
        foreach ($assignmentList as $kk => $kkvalue) {
            $gradeSubmitteddate = ClassRegistry::init('ExamGrade')->getGradeSubmmissionDate(
                $kkvalue['CourseInstructorAssignment']['published_course_id']
            );
            // if grade is not submitted, check if the deadline is passed against the
            // current time, if it does calculate the number of days passed
            if (empty($gradeSubmitteddate)) {
                //$count++;
                if (
                    isset($kkvalue['PublishedCourse']['YearLevel']['name'])
                    && !empty($kkvalue['PublishedCourse']['YearLevel']['name'])
                ) {
                    $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'];

                    if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                        $current_date = date('Y-m-d H:i:s');
                        $grade_submission_end_date_formatted =
                            date('Y-m-d H:i:s', strtotime($grade_submission_end_date));


                        if ($grade_submission_end_date_formatted > $current_date) {
                            $noDaysDelayed = 0;
                            $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                        } else {
                            $noDaysDelayed = $this->TimeAgoFormat($current_date, $grade_submission_end_date_formatted);
                            $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                        }
                    } else {
                        debug($grade_submission_end_date);
                    }
                } else {
                    // preengineering
                    if (
                        isset($kkvalue['PublishedCourse']['College']['id'])
                        && !empty($kkvalue['PublishedCourse']['College']['id'])
                    ) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue['PublishedCourse']['College']['id']]['grade_submission_end_date'];
                        $current_date = date('Y-m-d H:i:s');
                        $grade_submission_end_date_formatted =
                            date('Y-m-d H:i:s', strtotime($grade_submission_end_date));

                        if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                            if ($grade_submission_end_date > $current_date) {
                                $noDaysDelayed = 0;
                                $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                            } else {
                                $noDaysDelayed = $this->TimeAgoFormat($current_date, $grade_submission_end_date_formatted);
                                $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                            }
                        }
                    } else {
                        debug($kkvalue['PublishedCourse']);
                    }
                }
            }

            // if grade is submitted, check the submitted date, with the deadline, if
            // the submitted date is less than the deadline, it is fine, nothing to do

            if (!empty($gradeSubmitteddate)) {
                //$count++;
                if (
                    isset($kkvalue['PublishedCourse']['YearLevel']['name'])
                    && !empty($kkvalue['PublishedCourse']['YearLevel']['name'])
                ) {
                    if (isset($gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date']) && !empty($gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'])) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel[$kkvalue['PublishedCourse']['YearLevel']['name']][$kkvalue['PublishedCourse']['department_id']]['grade_submission_end_date'];



                        if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                            $grade_submission_end_date_formatted =
                                date('Y-m-d H:i:s', strtotime($grade_submission_end_date));
                            if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                                $noDaysDelayed = 0;
                                $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                            } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                                // $reformattedGradeSubmissionStat=$this->TimeAgoFormat($grade_submission_end_date);
                                $noDaysDelayed = $this->TimeAgoFormat(
                                    $gradeSubmitteddate['ExamGrade']['created'],
                                    $grade_submission_end_date_formatted
                                );
                                $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                            }
                        }
                    }
                } else {
                    // preengineering
                    if (
                        isset($kkvalue['PublishedCourse']['College']['id']) &&
                        !empty($kkvalue['PublishedCourse']['College']['id'])
                    ) {
                        $grade_submission_end_date = $gradeSubmissionDateOfYearLevel['1st']['pre_' . $kkvalue['PublishedCourse']['College']['id']]['grade_submission_end_date'];

                        if (isset($grade_submission_end_date) && !empty($grade_submission_end_date)) {
                            $grade_submission_end_date_formatted =
                                date('Y-m-d H:i:s', strtotime($grade_submission_end_date));
                            if ($gradeSubmitteddate['ExamGrade']['created'] < $grade_submission_end_date_formatted) {
                                $noDaysDelayed = 0;
                                $reformattedGradeSubmissionStat['Instructor']['noInstNotDelayedSub'] += 1;
                            } elseif ($gradeSubmitteddate['ExamGrade']['created'] > $grade_submission_end_date_formatted) {
                                $noDaysDelayed = $this->TimeAgoFormat(
                                    $gradeSubmitteddate['ExamGrade']['created'],
                                    $grade_submission_end_date_formatted
                                );

                                $reformattedGradeSubmissionStat['Instructor']['noInstDelayedSub'] += 1;
                            }
                        }
                    }
                }
            }

            // if the grade submitted is greater than grade submission deadline,
            // calculate the number of days delyed after the deadline passed
        }
        debug($count);
        //  debug($reformattedGradeSubmissionStat);
        return $reformattedGradeSubmissionStat;
    }

    function getAllDepartmentYearLevelMatchingYear($id)
    {

        $yearLevel = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.id' => $id)));

        $yearLevelIdss = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.name' => array_values($yearLevel))));

        $yearLevelIds[] = 0;
        foreach ($yearLevelIdss as $key => $value) {
            # code...
            $yearLevelIds[] = $key;
        }

        return $yearLevelIds;
    }

    public function getCourseNotAssigned($acadamic_year, $semester, $program_id, $program_type_id, $department_id, $year_level_id, $freshman = 0)
    {
        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $options = array();
        $options['conditions']['PublishedCourse.drop'] = 0;

        //$lateGradeSubmissionList = array();

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $options['conditions']['PublishedCourse.program_id'] = $program_ids[1];
            } else {
                $options['conditions']['PublishedCourse.program_id'] = $program_id;
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $options['conditions']['PublishedCourse.program_type_id'] = $program_type_ids[1];
            } else {
                $options['conditions']['PublishedCourse.program_type_id'] = $program_type_id;
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $options['conditions']['PublishedCourse.academic_year'] = $acadamic_year;
        }

        if (isset($semester) && !empty($semester)) {
            $options['conditions']['PublishedCourse.semester'] = $semester;
        }

        // list out the department
        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->PublishedCourse->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_ids[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->PublishedCourse->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            }
        } else {
            $departments = $this->PublishedCourse->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel')
            ));
        }



        $options['contain'] = array(
            'GivenByDepartment',
            'YearLevel',
            'Course',
            'Program',
            'ProgramType',
            'Section.archive = 0'
        );

        //$options['conditions'][] = 'PublishedCourse.id NOT IN (SELECT published_course_id FROM course_instructor_assignments)';
        $options['conditions'][] = 'PublishedCourse.id NOT IN (SELECT published_course_id FROM course_instructor_assignments WHERE academic_year ="' . $acadamic_year . '" AND semester = "' . $semester . '" GROUP BY published_course_id)';

        $notAssignedCourseList = array();

        //$assignedCourseList = ClassRegistry::init('CourseInstructorAssignment')->find('list', array('conditions' => array('CourseInstructorAssignment.academic_year' => $acadamic_year, 'CourseInstructorAssignment.semester' => $semester), 'fields' => array('CourseInstructorAssignment.published_course_id', 'CourseInstructorAssignment.published_course_id'), 'group' => array('CourseInstructorAssignment.published_course_id')));

        if ($freshman == 1) {
            $departments = array();
            $colleges = array();
        }


        if ($freshman == 0) {
            foreach ($departments as $key => $value) {
                $college_id[$value['Department']['college_id']] = $value['Department']['college_id'];
                $yearLevel = array();
                if (!empty($year_level_id)) {
                    foreach ($value['YearLevel'] as $yykey => $yyvalue) {
                        if (!empty($year_level_id) && strcasecmp($year_level_id, $yyvalue['name']) == 0) {
                            $yearLevel[$yykey] = $yyvalue;
                        }
                    }
                } elseif (empty($year_level_id)) {
                    $yearLevel = $value['YearLevel'];
                }

                if (!empty($yearLevel)) {
                    foreach ($yearLevel as $ykey => $yvalue) {
                        if (!empty($year_level_id)) {
                            if ($yvalue['name'] == $year_level_id) {
                                $options['conditions']['PublishedCourse.year_level_id'] = $yvalue['id'];
                            }
                        } else {
                            $options['conditions']['PublishedCourse.year_level_id'] = $yvalue['id'];
                            $options['conditions']['PublishedCourse.department_id'] = $value['Department']['id'];
                        }

                        $options['conditions']['PublishedCourse.academic_year'] = $acadamic_year;
                        $options['conditions']['PublishedCourse.semester'] = $semester;

                        //debug($options);

                        $courseInstructorAssignment = $this->PublishedCourse->find('all', $options);

                        //debug($courseInstructorAssignment);

                        $notAssignedCourseList[$value['Department']['name']][$yvalue['name']] = $courseInstructorAssignment;
                    }
                }
            }
        } else {
            // freshman

            $college_id = array();
            $colleges = array();

            if (isset($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->PublishedCourse->College->find('list', array(
                        'conditions' => array(
                            'College.id' => $college_ids[1],
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                } elseif ($department_id == 0) {
                    $college_id = $this->PublishedCourse->College->find('list', array(
                        'conditions' => array(
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                }
            }


            if (isset($college_id) && !empty($college_id)) {
                $colleges = $this->PublishedCourse->College->find('all', array(
                    'conditions' => array(
                        'College.id' => $college_id,
                        'College.active' => 1
                    ),
                    'recursive' => -1
                ));

                if (!empty($colleges)) {
                    foreach ($colleges as $key => $value) {
                        $options['conditions'] = array(
                            'PublishedCourse.department_id is null',
                            'PublishedCourse.academic_year' => $acadamic_year,
                            'PublishedCourse.semester' => $semester,
                            'OR' => array(
                                'PublishedCourse.year_level_id is null',
                                'PublishedCourse.year_level_id = 0',
                                'PublishedCourse.year_level_id =""'
                            ),
                            'PublishedCourse.college_id' => $value['College']['id'],
                            'PublishedCourse.id NOT IN (SELECT published_course_id FROM course_instructor_assignments WHERE academic_year ="' . $acadamic_year . '" AND semester = "' . $semester . '" GROUP BY published_course_id)'
                        );


                        //debug($options['conditions']);
                        //debug($value['College']['id']);
                        //debug($value['College']['name']);


                        $courseInstructorAssignment = $this->PublishedCourse->find('all', $options);

                        $notAssignedCourseList[$value['College']['name']]['Pre/1st'] = $courseInstructorAssignment;
                    }
                }
            }
        }

        //debug($notAssignedCourseList);
        return $notAssignedCourseList;
    }
}
