<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PublishedCoursesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('published_courses');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');
        $this->belongsTo('YearLevels', [
                'propertyName' => 'YearLevel']);
        $this->belongsTo('GradeScales',
            [
                'propertyName' => 'GradeScale']);
        $this->belongsTo('Courses',
            [
                'propertyName' => 'Course']);
        $this->belongsTo('ProgramTypes',
            [
                'propertyName' => 'ProgramType']);
        $this->belongsTo('Programs',
            [
                'propertyName' => 'Program']);
        $this->belongsTo('Departments',
            [
                'propertyName' => 'Department']);
        $this->belongsTo('GivenByDepartments', [
            'className' => 'Departments',
            'foreignKey' => 'given_by_department_id',
            'propertyName' => 'GivenByDepartment',
        ]);
        $this->belongsTo('Colleges',[
            'propertyName' => 'College']);
        $this->belongsTo('Sections',[
            'propertyName' => 'Section']);

        $this->hasMany('CourseSchedules',
            [
                'propertyName' => 'CourseSchedule',]);
        $this->hasMany('UnschedulePublishedCourses',
            [
                'propertyName' => 'UnschedulePublishedCourse',]);
        $this->hasMany('ExamSchedules',
            [
                'propertyName' => 'ExamSchedule',]);
        $this->hasMany('GradeScalePublishedCourses',
            [
                'propertyName' => 'GradeScalePublishedCourse',]);
        $this->hasMany('MakeupExams',[
            'propertyName' => 'MakeupExam']);
        $this->hasMany('MergedSectionsCourses',
            [
                'propertyName' => 'MergedSectionCourse']);
        $this->hasMany('MergedSectionsExams',
            [
                'propertyName' => 'MergedSectionExam']);
        $this->hasMany('SectionSplitForPublishedCourses',
            [
                'propertyName' => 'SectionSplitForPublishedCourse']);
        $this->hasMany('CourseInstructorAssignments', [
            'dependent' => true,
            'propertyName' => 'CourseInstructorAssignment'
        ]);
        $this->hasMany('CourseRegistrations',
            [
                'propertyName' => 'CourseRegistration']);
        $this->hasMany('CourseAdds',
            [
                'propertyName' => 'CourseAdd']);
        $this->hasMany('ClassPeriodCourseConstraints',
            [
                'propertyName' => 'ClassPeriodCourseConstraint']);
        $this->hasMany('ClassRoomCourseConstraints',
            [
                'propertyName' => 'ClassRoomCourseConstraint']);
        $this->hasMany('CourseExamConstraints',
            [
                'propertyName' => 'CourseExamConstraint']);
        $this->hasMany('ExamRoomCourseConstraints',
            [
                'propertyName' => 'ExamRoomCourseConstraint']);
        $this->hasMany('Attendances',
            [
                'propertyName' => 'Attendance']);
        $this->hasMany('ExamTypes',[
            'propertyName' => 'ExamType']);
        $this->hasMany('FxResitRequests',
            [
                'propertyName' => 'FxResitRequest']);
        $this->hasMany('ResultEntryAssignments',
            [
                'propertyName' => 'ResultEntryAssignment']);

        $this->hasOne('CourseExamGapConstraints',
            [
                'propertyName' => 'CourseExamGapConstraint']);

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('year_level_id', 'create')
            ->notEmptyString('year_level_id', 'Year level is required.')
            ->numeric('year_level_id', 'Year level must be numeric.');

        $validator
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester', 'Semester is required.');

        $validator
            ->requirePresence('course_id', 'create')
            ->notEmptyString('course_id', 'Course ID is required.')
            ->numeric('course_id', 'Course ID must be numeric.');

        $validator
            ->requirePresence('program_type_id', 'create')
            ->notEmptyString('program_type_id', 'Program Type ID is required.')
            ->numeric('program_type_id', 'Program Type ID must be numeric.');

        $validator
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Program ID is required.')
            ->numeric('program_id', 'Program ID must be numeric.');

        $validator
            ->requirePresence('department_id', 'create')
            ->notEmptyString('department_id', 'Department ID is required.')
            ->numeric('department_id', 'Department ID must be numeric.');

        $validator
            ->requirePresence('section_id', 'create')
            ->notEmptyString('section_id', 'Section ID is required.')
            ->numeric('section_id', 'Section ID must be numeric.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['course_id'], 'Courses'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['section_id'], 'Sections'));
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));

        return $rules;
    }

    function canItBeDeleted($id = null)
    {
        if ($this->CourseRegistration->find('count', array('conditions' => array('CourseRegistration.published_course_id' => $id))) > 0) {
            return false;
        }

        if ($this->CourseAdd->find('count', array('conditions' => array('CourseAdd.published_course_id' => $id))) > 0) {
            return false;
        } elseif ($this->MakeupExam->find('count', array('conditions' => array('MakeupExam.published_course_id' => $id))
            ) > 0) {
            return false;
        } else {
            return true;
        }
    }

    function getSectionofPublishedCourses($data, $department_id = null, $publishedcourses = null, $college_id = null)
    {
        if ($college_id) {
            $sections = $this->Section->find('list', array(
                'conditions' => array(
                    'Section.college_id' => $college_id,
                    'Section.department_id is null',
                    'Section.program_id' => $data['PublishedCourse']['program_id'],
                    'Section.program_type_id' => $data['PublishedCourse']['program_type_id'],
                    'Section.archive' => 0
                ),
                'recursive' => -1
            ));
        } else {
            $sections = $this->Section->find('list', array(
                'conditions' => array(
                    'Section.department_id' => $department_id,
                    'Section.year_level_id' => $data['PublishedCourse']['year_level_id'],
                    'Section.program_id' => $data['PublishedCourse']['program_id'],
                    'Section.program_type_id' => $data['PublishedCourse']['program_type_id'],
                    'Section.archive' => 0
                ),
                'recursive' => -1
            ));
        }

        //format section display
        if (!empty($sections) && !empty($publishedcourses)) {
            $section_organized_published_courses = array();
            foreach ($sections as $section_id => $section_name) {
                foreach ($publishedcourses as $kkk => &$vvv) {
                    if ($vvv['PublishedCourse']['section_id'] == $section_id) {
                        if ($this->CourseRegistration->ExamGrade->is_grade_submitted($vvv['PublishedCourse']['id']) > 0) {
                            $vvv['PublishedCourse']['unpublish_readOnly'] = true;
                            $vvv['PublishedCourse']['have_course_registration_or_add'] = true;
                        } else {
                            $vvv['PublishedCourse']['unpublish_readOnly'] = false;
                            $vvv['PublishedCourse']['have_course_registration_or_add'] = false;

                            if ($this->CourseRegistration->find('count', array('conditions' => array('CourseRegistration.published_course_id' => $vvv['PublishedCourse']['id'])))) {
                                $vvv['PublishedCourse']['have_course_registration_or_add'] = true;
                            }

                            if ($this->CourseAdd->find('count', array('conditions' => array('CourseAdd.published_course_id' => $vvv['PublishedCourse']['id'])))) {
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

    function get_section_organized_published_courses($data = null, $department_id = null, $publishedcourses = null, $college_id = null)
    {
        if ($college_id) {
            $sections = $this->Section->find('list', array(
                'conditions' => array(
                    'Section.college_id' => $college_id,
                    'Section.department_id is null',
                    'Section.program_id' => PROGRAM_UNDEGRADUATE,
                    'Section.program_type_id' => PROGRAM_TYPE_REGULAR,
                    'Section.archive' => 0
                ),
                'recursive' => -1
            ));
        } else {
            $sections = $this->Section->find('list', array(
                'conditions' => array(
                    'Section.department_id' => $department_id,
                    'Section.program_id' => $data['PublishedCourse']['program_id'],
                    'Section.year_level_id' => $data['PublishedCourse']['year_level_id'],
                    'Section.archive' => 0
                ),
                'recursive' => -1
            ));
        }

        //format section display
        if (!empty($sections) && !empty($publishedcourses)) {
            $section_organized_published_courses = array();
            foreach ($sections as $section_id => $section_name) {
                foreach ($publishedcourses as $kkk => &$vvv) {
                    if ($vvv['PublishedCourse']['section_id'] == $section_id) {
                        // debug($this->CourseRegistration->ExamGrade->is_grade_submitted($vvv['PublishedCourse']['id']));
                        if ($this->CourseRegistration->ExamGrade->is_grade_submitted($vvv['PublishedCourse']['id']) > 0) {
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

    function getSectionOrganizedPublishedCoursesM($publishedcourses = null)
    {
        $section_organized_published_courses = array();
        foreach ($publishedcourses as $kkk => &$vvv) {
            if ($this->CourseRegistration->ExamGrade->is_grade_submitted($vvv['PublishedCourse']['id']) > 0) {
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

    function getInstructorDetailGivingPublishedCourse($published_course_id = null)
    {
        $instructor_detail = array();

        if (isset($published_course_id) && !empty($published_course_id)) {
            $instructor_detail = $this->CourseInstructorAssignment->find('first', array(
                'fields' => array('CourseInstructorAssignment.published_course_id'),
                'conditions' => array('CourseInstructorAssignment.published_course_id' => $published_course_id),
                'contain' => array(
                    'Staff' => array(
                        'fields' => array(
                            'first_name',
                            'middle_name',
                            'last_name'
                        ),
                        'Title' => array('id', 'title'),
                        'Position' => array('id', 'position'),
                        'User' => array('id', 'username', 'email', 'active', 'email_verified'),
                    )
                )
            ));
        }

        return $instructor_detail;
    }

    /** Find list of students registered for a given publish course return array of add/register students for the given publish course */
    function getStudentsTakingPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = array();
        $student_adds = array();
        $students = array();
        $students_makeup_exam = array();

        if ($published_course_id != null) {
            $students = $this->CourseRegistration->find('all', array(
                'fields' => array('CourseRegistration.id'),
                'conditions' => array(
                    'CourseRegistration.published_course_id' => $published_course_id
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'fields' => array('section_id', 'college_id', 'department_id', 'given_by_department_id', 'add', 'drop', 'grade_scale_id')
                    ),
                    'ExamGrade' => array(
                        //'order' => array('ExamGrade.created' => 'DESC')
                        'order' => array('ExamGrade.id' => 'DESC')
                    ),
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                    ),
                    'CourseDrop',
                    'ExamResult.course_add = 0' => array(
                        'ExamType'
                    ),
                    'ResultEntryAssignment'
                ),
                // we have to implemet this for new grade entry for a section (it will remove duplicated registrations for grade entry) but it will affect some students existing student grades for grade change and grade views tools(if this function is used except grade entry by the instructor). Neway
                // This might be useful for checking registration is unique per semester.
                /* 'group' => array(
                    'CourseRegistration.student_id',
                    'CourseRegistration.published_course_id',
                ) */
                /* 'order' => array(
                    'CourseRegistration.id' = 'ASC',
                ) */
            ));

            if (!empty($students)) {
                foreach ($students as $key => &$student) {
                    if ($this->CourseRegistration->isCourseDroped($student['CourseRegistration']['id'])) {
                        unset($students[$key]);
                    } else {
                        $students[$key]['ExamGradeHistory'] = $this->CourseRegistration->getCourseRegistrationGradeHistory($student['CourseRegistration']['id']);
                        $students[$key]['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail($student['CourseRegistration']['id']);
                        $students[$key]['AnyExamGradeIsOnProcess'] = $this->CourseRegistration->isAnyGradeOnProcess($student['CourseRegistration']['id']);
                        $students[$key]['freshman_program'] = (!is_null($student['PublishedCourse']['college_id']) ? true : false);

                        if (isset($students[$key]['ExamGrade']) && !empty($students[$key]['ExamGrade'])) {
                            foreach ($students[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                                $students[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                                $students[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                            }
                        }
                    }
                }
            }

            $this->CourseRegistration->Student->bindModel(array('hasMany' => array('StudentsSection' => array('className' => 'StudentsSection'))));

            $student_all_adds = $this->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $published_course_id,
                    'department_approval' => 1,
                    'registrar_confirmation' => 1,
                ),
                'contain' => array(
                    'PublishedCourse',
                    'ExamGrade' => array(
                        //'order' => array('ExamGrade.created' => 'DESC')
                        'order' => array('ExamGrade.id' => 'DESC')
                    ),
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                        'StudentsSection.archive = 0'
                    ),
                    'ExamResult.course_add = 1'
                )
            ));

            $section_and_course_detail = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array(
                    'Section' => array(
                        'YearLevel',
                        'College',
                        'Department',
                        'Program',
                        'ProgramType',
                    ),
                    'Course'
                )
            ));

            $section_detail = $section_and_course_detail['Section'];
            $course_detail = $section_and_course_detail['Course'];
            //debug($student_all_adds);
            //debug($section_detail);

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    //Check that the add is confirmed by the department and registrar OR it is published as mass add
                    if (($student_all_add['CourseAdd']['department_approval'] == 1 && $student_all_add['CourseAdd']['registrar_confirmation'] == 1) || $student_all_add['PublishedCourse']['add'] == 1) {
                        //Approved and confirmed by for each exam grade

                        if (!empty($student_all_adds[$key]['ExamGrade'])) {
                            foreach ($student_all_adds[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                                $student_all_adds[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $student_all_adds[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                                $student_all_adds[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $student_all_adds[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                            }
                        }

                        //$student_all_adds[$key]['ExamGradeHistory'] = $this->CourseAdd->getCourseAddGradeHistory($student_all_add['CourseAdd']['id']);
                        $student_all_add['ExamGradeHistory'] = $this->CourseAdd->getCourseAddGradeHistory($student_all_add['CourseAdd']['id']);
                        $student_all_add['LatestGradeDetail'] = $this->CourseAdd->getCourseAddLatestGradeDetail($student_all_add['CourseAdd']['id']);
                        //$student_all_adds[$key]['AnyExamGradeIsOnProcess'] = $this->CourseAdd->isAnyGradeOnProcess($student_all_add['CourseAdd']['id']);
                        $student_all_add['AnyExamGradeIsOnProcess'] = $this->CourseAdd->isAnyGradeOnProcess($student_all_add['CourseAdd']['id']);
                        $student_all_add[$key]['freshman_program'] = (!is_null($student_all_add['PublishedCourse']['college_id']) ? true : false);

                        if (isset($student_all_add['Student']['StudentsSection'][0]['section_id']) && strcasecmp($student_all_add['Student']['StudentsSection'][0]['section_id'], $section_detail['id']) == 0) {
                            $students[] = $student_all_add;
                        } else {
                            $student_adds[] = $student_all_add;
                        }
                    } else {
                        unset($student_all_adds[$key]);
                    }
                }
            }

            $students_makeup_exam = $this->MakeupExam->find('all', array(
                'conditions' => array(
                    'MakeupExam.published_course_id' => $published_course_id
                ),
                'contain' => array(
                    'CourseRegistration' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        ),
                    ),
                    'CourseAdd' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        ),
                    ),
                    'ExamGradeChange' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        ),
                        'order' => array('ExamGradeChange.id' => 'DESC')
                    ),
                    'PublishedCourse',
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                    )
                ),
                //'ExamResult.course_add = 0'
            ));

            //debug($students_makeup_exam);

            //student previously taken course detail
            if (!empty($students_makeup_exam)) {
                foreach ($students_makeup_exam as $key => $student_makeup_exam) {
                    if ($student_makeup_exam['MakeupExam']['course_registration_id'] != null) {
                        $students_makeup_exam[$key]['ExamGradeHistory'] = $this->CourseRegistration->getCourseRegistrationGradeHistory($student_makeup_exam['MakeupExam']['course_registration_id']);
                        $students_makeup_exam[$key]['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail($student_makeup_exam['MakeupExam']['course_registration_id']);
                        $students_makeup_exam[$key]['AnyExamGradeIsOnProcess'] = $this->CourseRegistration->isAnyGradeOnProcess($student_makeup_exam['MakeupExam']['course_registration_id']);
                        $students_makeup_exam[$key]['freshman_program'] = (!is_null($student_makeup_exam['PublishedCourse']['college_id']) ? true : false);

                        $students_makeup_exam[$key]['ExamResultHistory'] = $this->CourseRegistration->ExamResult->find('all', array('conditions' => array('ExamResult.course_registration_id' => $student_makeup_exam['MakeupExam']['course_registration_id']), 'contain' => array()));
                    } else {
                        $students_makeup_exam[$key]['ExamGradeHistory'] = $this->CourseAdd->getCourseAddGradeHistory($student_makeup_exam['MakeupExam']['course_add_id']);
                        $students_makeup_exam[$key]['LatestGradeDetail'] = $this->CourseAdd->getCourseAddLatestGradeDetail($student_makeup_exam['MakeupExam']['course_add_id']);
                        $students_makeup_exam[$key]['AnyExamGradeIsOnProcess'] = $this->CourseAdd->isAnyGradeOnProcess($student_makeup_exam['MakeupExam']['course_add_id']);
                        $students_makeup_exam[$key]['freshman_program'] = (!is_null($student_makeup_exam['PublishedCourse']['college_id']) ? true : false);

                        $students_makeup_exam[$key]['ExamResultHistory'] = $this->CourseAdd->ExamResult->find('all', array('conditions' => array('ExamResult.course_add_id' => $student_makeup_exam['MakeupExam']['course_add_id']), 'contain' => array()));
                    }

                    if (!empty($student_makeup_exam['CourseRegistration']) && $student_makeup_exam['CourseRegistration']['id'] != "") {
                        $students_makeup_exam[$key]['ExamGrade'] = $student_makeup_exam['CourseRegistration']['ExamGrade'];
                    } else {
                        $students_makeup_exam[$key]['ExamGrade'] = $student_makeup_exam['CourseAdd']['ExamGrade'];
                    }

                    if (isset($students_makeup_exam[$key]['ExamGrade']) && !empty($students_makeup_exam[$key]['ExamGrade'])) {
                        foreach ($students_makeup_exam[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                            $students_makeup_exam[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students_makeup_exam[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                            $students_makeup_exam[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students_makeup_exam[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                        }
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;
        $student_course_register_and_adds['makeup'] = $students_makeup_exam;

        //debug($student_course_register_and_adds);
        return $student_course_register_and_adds;
    }

    function getStudentsTakingFxExamPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = array();
        $student_adds = array();
        $students = array();
        $students_makeup_exam = array();

        if ($published_course_id != null) {
            $section_and_course_detail = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array(
                    'Section',
                    'Course'
                )
            ));

            $section_detail = $section_and_course_detail['Section'];
            $course_detail = $section_and_course_detail['Course'];

            $students_makeup_exam = $this->MakeupExam->find('all', array(
                'conditions' => array(
                    'MakeupExam.published_course_id' => $published_course_id
                ),
                'contain' => array(
                    'CourseRegistration' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        ),
                    ),
                    'CourseAdd' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        ),
                    ),
                    'ExamGradeChange' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        ),
                        'order' => array('ExamGradeChange.id' => 'DESC')
                    ),
                    'PublishedCourse',
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                    )
                )
            ));

            //student previously taken course detail
            if (!empty($students_makeup_exam)) {
                foreach ($students_makeup_exam as $key => &$student_makeup_exam) {
                    if ($student_makeup_exam['MakeupExam']['course_registration_id'] != null) {
                        $students_makeup_exam[$key]['ExamGradeHistory'] = $this->CourseRegistration->getCourseRegistrationGradeHistory($student_makeup_exam['MakeupExam']['course_registration_id']);
                        $students_makeup_exam[$key]['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail($student_makeup_exam['MakeupExam']['course_registration_id']);
                        $students_makeup_exam[$key]['AnyExamGradeIsOnProcess'] = $this->CourseRegistration->isAnyGradeOnProcess($student_makeup_exam['MakeupExam']['course_registration_id']);
                        $students_makeup_exam[$key]['freshman_program'] = (!is_null($student_makeup_exam['PublishedCourse']['college_id']) ? true : false);
                        $students_makeup_exam[$key]['ExamResultHistory'] = $this->CourseRegistration->ExamResult->find('all', array('conditions' => array('ExamResult.course_registration_id' => $student_makeup_exam['MakeupExam']['course_registration_id']), 'contain' => array()));
                    } else {
                        $students_makeup_exam[$key]['ExamGradeHistory'] = $this->CourseAdd->getCourseAddGradeHistory($student_makeup_exam['MakeupExam']['course_add_id']);
                        $students_makeup_exam[$key]['LatestGradeDetail'] = $this->CourseAdd->getCourseAddLatestGradeDetail($student_makeup_exam['MakeupExam']['course_add_id']);
                        $students_makeup_exam[$key]['AnyExamGradeIsOnProcess'] = $this->CourseAdd->isAnyGradeOnProcess($student_makeup_exam['MakeupExam']['course_add_id']);
                        $students_makeup_exam[$key]['freshman_program'] = (!is_null($student_makeup_exam['PublishedCourse']['college_id']) ? true : false);
                        $students_makeup_exam[$key]['ExamResultHistory'] = $this->CourseAdd->ExamResult->find('all', array('conditions' => array('ExamResult.course_add_id' => $student_makeup_exam['MakeupExam']['course_add_id']), 'contain' => array()));
                    }

                    if (!empty($student_makeup_exam['CourseRegistration']) && $student_makeup_exam['CourseRegistration']['id'] != "") {
                        $students_makeup_exam[$key]['ExamGrade'] = $student_makeup_exam['CourseRegistration']['ExamGrade'];
                    } else {
                        $students_makeup_exam[$key]['ExamGrade'] = $student_makeup_exam['CourseAdd']['ExamGrade'];
                    }

                    if (isset($students_makeup_exam[$key]['ExamGrade']) && !empty($students_makeup_exam[$key]['ExamGrade'])) {
                        foreach ($students_makeup_exam[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                            $students_makeup_exam[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students_makeup_exam[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                            $students_makeup_exam[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students_makeup_exam[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                        }
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;
        $student_course_register_and_adds['makeup'] = $students_makeup_exam;
        //debug($student_course_register_and_adds);
        return $student_course_register_and_adds;
    }

    function getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = array();
        $student_adds = array();
        $students = array();
        $students_makeup_exam = array();

        if ($published_course_id != null) {
            $section_and_course_detail = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array(
                    'Section',
                    'Course'
                )
            ));

            $section_detail = $section_and_course_detail['Section'];
            $course_detail = $section_and_course_detail['Course'];

            $students_makeup_exam = $this->ResultEntryAssignment->find('all', array(
                'conditions' => array(
                    'ResultEntryAssignment.published_course_id' => $published_course_id
                ),
                'contain' => array(
                    'CourseRegistration' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        )
                    ),
                    'CourseAdd' => array(
                        'ExamGrade' => array(
                            'order' => array('ExamGrade.id' => 'DESC')
                        )
                    ),
                    'PublishedCourse',
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                    )
                )
            ));

            //student previously taken course detail
            if (!empty($students_makeup_exam)) {
                foreach ($students_makeup_exam as $key => &$student_makeup_exam) {
                    if ($student_makeup_exam['ResultEntryAssignment']['course_registration_id'] != null) {
                        $students_makeup_exam[$key]['ExamGradeHistory'] = $this->CourseRegistration->getCourseRegistrationGradeHistory($student_makeup_exam['ResultEntryAssignment']['course_registration_id']);
                        $students_makeup_exam[$key]['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail($student_makeup_exam['ResultEntryAssignment']['course_registration_id']);
                        $students_makeup_exam[$key]['AnyExamGradeIsOnProcess'] = $this->CourseRegistration->isAnyGradeOnProcess($student_makeup_exam['ResultEntryAssignment']['course_registration_id']);
                        $students_makeup_exam[$key]['freshman_program'] = (!is_null($student_makeup_exam['PublishedCourse']['college_id']) ? true : false);
                    } else {
                        debug($student_makeup_exam['ResultEntryAssignment']['course_add_id']);
                        $students_makeup_exam[$key]['ExamGradeHistory'] = $this->CourseRegistration->getCourseRegistrationGradeHistory($student_makeup_exam['ResultEntryAssignment']['course_add_id'], 0);
                        $students_makeup_exam[$key]['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail($student_makeup_exam['ResultEntryAssignment']['course_add_id'], 0);
                        $students_makeup_exam[$key]['AnyExamGradeIsOnProcess'] = $this->CourseRegistration->isAnyGradeOnProcess($student_makeup_exam['ResultEntryAssignment']['course_add_id']);
                        $students_makeup_exam[$key]['freshman_program'] = (!is_null($student_makeup_exam['PublishedCourse']['college_id']) ? true : false);
                        $students_makeup_exam[$key]['ExamResultHistory'] = $this->CourseAdd->ExamResult->find('all', array('conditions' => array('ExamResult.course_add_id' => $student_makeup_exam['ResultEntryAssignment']['course_add_id']), 'contain' => array()));
                    }

                    if (!empty($student_makeup_exam['CourseRegistration']) && $student_makeup_exam['CourseRegistration']['id'] != "") {
                        $students_makeup_exam[$key]['ExamGrade'] = $student_makeup_exam['CourseRegistration']['ExamGrade'];
                    } else {
                        $students_makeup_exam[$key]['ExamGrade'] = $student_makeup_exam['CourseAdd']['ExamGrade'];
                    }

                    if (isset($students_makeup_exam[$key]['ExamGrade']) && !empty($students_makeup_exam[$key]['ExamGrade'])) {
                        foreach ($students_makeup_exam[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                            $students_makeup_exam[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students_makeup_exam[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                            $students_makeup_exam[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students_makeup_exam[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                        }
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;
        $student_course_register_and_adds['makeup'] = $students_makeup_exam;
        //debug($student_course_register_and_adds);
        return $student_course_register_and_adds;
    }

    function getStudentSelectedFxExamPublishedCourse($published_course_id = null)
    {
        $student_course_register_and_adds = array();

        if ($published_course_id != null) {
            $fx_applied_student_lists = ClassRegistry::init('FxResitRequest')->find('list', array(
                'conditions' => array(
                    'FxResitRequest.published_course_id' => $published_course_id
                ),
                'fields' => array(
                    'FxResitRequest.student_id',
                    'FxResitRequest.student_id'
                )
            ));

            $students = $this->CourseRegistration->find('all', array(
                'fields' => array(
                    'CourseRegistration.id'
                ),
                'conditions' => array(
                    'CourseRegistration.published_course_id' => $published_course_id,
                    'CourseRegistration.student_id' => $fx_applied_student_lists,
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'fields' => array(
                            'college_id'
                        )
                    ),
                    'ExamGrade' => array(
                        'order' => array('ExamGrade.id' => 'DESC')
                    ),
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                    ),
                    'CourseDrop',
                    'ExamResult.course_add = 0' => array(
                        'ExamType'
                    )
                )
            ));

            if (!empty($students)) {
                foreach ($students as $key => &$student) {
                    $students[$key]['ExamGradeHistory'] = $this->CourseRegistration->getCourseRegistrationGradeHistory($student['CourseRegistration']['id']);
                    $students[$key]['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail($student['CourseRegistration']['id']);

                    if (isset($students[$key]['LatestGradeDetail']['type']) && $students[$key]['LatestGradeDetail']['type'] == "Change" && $students[$key]['LatestGradeDetail']['ExamGrade']['department_approval'] == 1) {
                        $disabledbutton = true;
                    }

                    $students[$key]['AnyExamGradeIsOnProcess'] = $this->CourseRegistration->isAnyGradeOnProcess($student['CourseRegistration']['id']);
                    $students[$key]['freshman_program'] = (!is_null($student['PublishedCourse']['college_id']) ? true : false);

                    if (isset($students[$key]['ExamGrade']) && !empty($students[$key]['ExamGrade'])) {
                        foreach ($students[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                            $students[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                            $students[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $students[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                        }
                    }
                }
            }

            $student_all_adds = $this->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $published_course_id,
                    'CourseAdd.student_id' => $fx_applied_student_lists,
                    //'department_approval' => 1,
                    //'registrar_confirmation' => 1,
                ),
                'contain' => array(
                    'PublishedCourse',
                    'ExamGrade' => array(
                        'order' => array('ExamGrade.id' => 'DESC')
                    ),
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.gender', 'Student.graduated', 'Student.academicyear'),
                        'order' => array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC'),
                        'StudentsSection.archive = 0'
                    ),
                    'ExamResult.course_add = 1'
                )
            ));

            $section_and_course_detail = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array(
                    'Section',
                    'Course'
                )
            ));

            $section_detail = $section_and_course_detail['Section'];
            $course_detail = $section_and_course_detail['Course'];

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    if (isset($student_all_adds[$key]['ExamGrade']) && !empty($student_all_adds[$key]['ExamGrade'])) {
                        foreach ($student_all_adds[$key]['ExamGrade'] as $eg_key => $exam_grade) {
                            $student_all_adds[$key]['ExamGrade'][$eg_key]['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $student_all_adds[$key]['ExamGrade'][$eg_key]['department_approved_by']));
                            $student_all_adds[$key]['ExamGrade'][$eg_key]['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $student_all_adds[$key]['ExamGrade'][$eg_key]['registrar_approved_by']));
                        }
                    }

                    $student_all_add['ExamGradeHistory'] = $this->CourseAdd->getCourseAddGradeHistory($student_all_add['CourseAdd']['id']);
                    $student_all_add['LatestGradeDetail'] = $this->CourseAdd->getCourseAddLatestGradeDetail($student_all_add['CourseAdd']['id']);

                    if (isset($student_all_add['LatestGradeDetail']['type']) && $student_all_add['LatestGradeDetail']['type'] == "Change" && $student_all_add['LatestGradeDetail']['ExamGrade']['department_approval'] == 1) {
                        $disabledbutton = true;
                    }

                    $student_all_add['AnyExamGradeIsOnProcess'] = $this->CourseAdd->isAnyGradeOnProcess($student_all_add['CourseAdd']['id']);
                    $student_all_add[$key]['freshman_program'] = (!is_null($student_all_add['PublishedCourse']['college_id']) ? true : false);

                    if (isset($student_all_add['Student']['StudentsSection'][0]['section_id']) && strcasecmp($student_all_add['Student']['StudentsSection'][0]['section_id'], $section_detail['id']) == 0) {
                        $students[] = $student_all_add;
                    } else {
                        $student_adds[] = $student_all_add;
                    }
                }
            }
        }

        $student_course_register_and_adds['register'] = $students;
        $student_course_register_and_adds['add'] = $student_adds;

        $students_with_fx = array();

        if (!empty($student_course_register_and_adds)) {
            foreach ($student_course_register_and_adds as $key => $register_add_makeup) {
                foreach ($register_add_makeup as $key => $value) {
                    debug($value);

                    if (isset($value['CourseRegistration']) && !empty($value['CourseRegistration']) && $value['CourseRegistration']['id'] != "") {
                        $garde = $this->CourseRegistration->ExamGrade->getApprovedGrade($value['CourseRegistration']['id'], 1);
                    } else {
                        $garde = $this->CourseRegistration->ExamGrade->getApprovedGrade($value['CourseAdd']['id'], 0);
                    }

                    if (!empty($garde) && strcasecmp($garde['grade'], 'Fx') == 0) {
                        $index = count($students_with_fx);

                        $students_with_fx[$value['Student']['id']]['full_name'] = $value['Student']['first_name'] . ' ' . $value['Student']['middle_name'] . ' ' . $value['Student']['last_name'];
                        $students_with_fx[$value['Student']['id']]['studentnumber'] = $value['Student']['studentnumber'];
                        $students_with_fx[$value['Student']['id']]['student_id'] = $value['Student']['id'];
                        $students_with_fx[$value['Student']['id']]['grade_id'] = $garde['grade_id'];

                        if (isset($value['CourseRegistration']) && !empty($value['CourseRegistration']) && $value['CourseRegistration']['id'] != "") {
                            $students_with_fx[$value['Student']['id']]['course_registration_id'] = $value['CourseRegistration']['id'];
                            $students_with_fx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                            $students_with_fx[$value['Student']['id']]['makeupalreadyapplied'] = ClassRegistry::init('MakeupExam')->makeUpExamApplied($value['Student']['id'], $value['PublishedCourse']['id'], $value['CourseRegistration']['id'], 1);
                        } elseif (isset($value['CourseAdd']) && !empty($value['CourseAdd']) && $value['CourseAdd']['id'] != "") {
                            $students_with_fx[$value['Student']['id']]['course_add_id'] = $value['CourseAdd']['id'];
                            $students_with_fx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];

                            debug(ClassRegistry::init('MakeupExam')->makeUpExamApplied($value['Student']['id'], $value['PublishedCourse']['id'], $value['CourseAdd']['id'], 0));
                            $students_with_fx[$value['Student']['id']]['makeupalreadyapplied'] = ClassRegistry::init('MakeupExam')->makeUpExamApplied($value['Student']['id'], $value['PublishedCourse']['id'], $value['CourseAdd']['id'], 0);
                        }
                        $students_with_fx[$value['Student']['id']]['grade'] = $garde['grade'];
                    } else {
                        debug($garde);
                    }
                }
            }
        }
        debug($students_with_fx);
        return $students_with_fx;
    }

    function getStudentsWhoAddPublishedCourse($published_course_id = null, $college_id = null)
    {
        $student_adds = array();

        if ($published_course_id != null) {
            $options = array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $published_course_id,
                ),
                'contain' => array(
                    'PublishedCourse'
                )
            );

            if (!empty($college_id)) {
                $department_ids = $this->Department->find('list', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id
                    ),
                    'fields' => array(
                        'Department.id'
                    ),
                    'recursive' => -1
                ));
                $options['conditions']['OR']['PublishedCourse.college_id'] = $college_id;
                $options['conditions']['OR']['PublishedCourse.department_id'] = $department_ids;
            }

            $student_all_adds = $this->CourseAdd->find('all', $options);

            if (!empty($student_all_adds)) {
                foreach ($student_all_adds as $key => &$student_all_add) {
                    //Check that the add is confirmed by the department and registrar OR it is published as mass add
                    if (($student_all_add['CourseAdd']['department_approval'] == 1 && $student_all_add['CourseAdd']['registrar_confirmation'] == 1) || $student_all_add['PublishedCourse']['add'] == 1) {
                        $student_adds[] = $student_all_add['CourseAdd']['student_id'];
                    }
                }
            }
        }
        return $student_adds;
    }

    function getGradeScaleDetail($published_course_id = null)
    {
        $grade_scale = array();

        if (!empty($published_course_id)) {
            $course_detail = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array(
                    'CourseRegistration' => array('ExamGrade'),
                    'CourseAdd' => array('ExamGrade'),
                    'MakeupExam' => array('ExamGrade'),
                    'Course' => array(
                        'Curriculum' => array(
                            'Program' => array('id', 'name'),
                            'Department' => array('College')
                        )
                    )
                )
            ));

            $grade_scale_detail = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array(
                    'Course',
                    'GradeScale' => array(
                        'GradeScaleDetail' => array(
                            'order' => array('maximum_result' => 'DESC'),
                            'Grade' => array('GradeType')
                        )
                    )
                )
            ));

            //debug($course_detail);
            //Check if grade is already submitted so that the already applied scale will be used
            if ((isset($course_detail['CourseRegistration'][0]['ExamGrade']) && !empty($course_detail['CourseRegistration'][0]['ExamGrade'])) || (isset($course_detail['CourseAdd'][0]['ExamGrade']) && !empty($course_detail['CourseAdd'][0]['ExamGrade']))) {
                $grade_scale_detail = $this->GradeScale->find('first', array(
                    'conditions' => array(
                        'GradeScale.id' => ((isset($course_detail['CourseRegistration'][0]['ExamGrade']) && !empty($course_detail['CourseRegistration'][0]['ExamGrade'])) ? $course_detail['CourseRegistration']['0']['ExamGrade'][0]['grade_scale_id'] : $course_detail['CourseAdd']['0']['ExamGrade'][0]['grade_scale_id'])
                    ),
                    'contain' => array(
                        'GradeScaleDetail' => array(
                            'order' => array('maximum_result' => 'DESC'),
                            'Grade' => array('GradeType')
                        )
                    )
                ));

                //debug($grade_scale_detail);
                if ($course_detail['PublishedCourse']['grade_scale_id'] != "" && $course_detail['PublishedCourse']['grade_scale_id'] != "0") {
                    $grade_scale['scale_by'] = 'Department';
                } else {
                    $grade_scale['scale_by'] = 'College';
                }

                $grade_scale['Course'] = $course_detail['Course'];
                $grade_scale['GradeType'] = $grade_scale_detail['GradeScaleDetail']['0']['Grade']['GradeType'];
                $formated_grade_scale_details = array();
                $count = 0;

                if (!empty($grade_scale_detail)) {
                    foreach ($grade_scale_detail['GradeScaleDetail'] as $key => $grade_scale_det) {
                        $formated_grade_scale_details[$count]['minimum_result'] = $grade_scale_det['minimum_result'];
                        $formated_grade_scale_details[$count]['maximum_result'] = $grade_scale_det['maximum_result'];
                        $formated_grade_scale_details[$count]['grade'] = $grade_scale_det['Grade']['grade'];
                        $formated_grade_scale_details[$count]['point_value'] = $grade_scale_det['Grade']['point_value'];
                        $formated_grade_scale_details[$count]['repeatable'] = $grade_scale_det['Grade']['allow_repetition'];
                        $formated_grade_scale_details[$count++]['pass_grade'] = $grade_scale_det['Grade']['pass_grade'];
                    }
                }

                $grade_scale['GradeScaleDetail'] = $formated_grade_scale_details;
                $grade_scale['GradeScale'] = $grade_scale_detail['GradeScale'];
            } elseif ($grade_scale_detail['PublishedCourse']['grade_scale_id'] != "" && $grade_scale_detail['PublishedCourse']['grade_scale_id'] != "0") {
                //if it already has assigned grade scale
                $grade_scale['scale_by'] = 'Department';
                $grade_scale['Course'] = $grade_scale_detail['Course'];
                $grade_scale['GradeType'] = $grade_scale_detail['GradeScale']['GradeScaleDetail']['0']['Grade']['GradeType'];
                $formated_grade_scale_details = array();
                $count = 0;

                foreach ($grade_scale_detail['GradeScale']['GradeScaleDetail'] as $key => $grade_scale_det) {
                    $formated_grade_scale_details[$count]['minimum_result'] = $grade_scale_det['minimum_result'];
                    $formated_grade_scale_details[$count]['maximum_result'] = $grade_scale_det['maximum_result'];
                    $formated_grade_scale_details[$count]['grade'] = $grade_scale_det['Grade']['grade'];
                    $formated_grade_scale_details[$count]['point_value'] = $grade_scale_det['Grade']['point_value'];
                    $formated_grade_scale_details[$count]['repeatable'] = $grade_scale_det['Grade']['allow_repetition'];
                    $formated_grade_scale_details[$count++]['pass_grade'] = $grade_scale_det['Grade']['pass_grade'];
                }

                $grade_scale['GradeScaleDetail'] = $formated_grade_scale_details;
                unset($grade_scale_detail['GradeScale']['GradeScaleDetail']);
                $grade_scale['GradeScale'] = $grade_scale_detail['GradeScale'];
            } else {
                //If it is delegated to the department
                if (
                    ($course_detail['Course']['Curriculum']['program_id'] == 1 && $course_detail['Course']['Curriculum']['Department']['College']['deligate_scale'] == 1) ||
                    ($course_detail['Course']['Curriculum']['program_id'] == 2 && $course_detail['Course']['Curriculum']['Department']['College']['deligate_for_graduate_study'] == 1)
                ) {
                    if (!empty($course_detail['PublishedCourse']['department_id'])) {
                        $grade_scale['error'] = 'Grade scale is not defined for <u>' . $grade_scale_detail['Course']['course_title'] . ' (' . $grade_scale_detail['Course']['course_code'] . ')</u> course or scale defined is deactived. Please contact <u>' . $course_detail['Course']['Curriculum']['Department']['name'] . '</u> department to set grade scale for the course.';
                    } else {
                        $grade_scale['error'] = 'Grade scale is not defined for <u>' . $grade_scale_detail['Course']['course_title'] . ' (' . $grade_scale_detail['Course']['course_code'] . ')</u> course or scale defined is deactived. Please contact <u>Freshman Program</u> to set grade scale for the course.';
                    }
                    $grade_scale['author'] = 'Department';
                } else {
                    //If it is not delegated by the college to the department
                    $grade_scale['author'] = 'College';
                    $grade_scale_and_type = $this->Course->getGradeScaleDetails($course_detail['Course']['id'], $course_detail['Course']['Curriculum']['Department']['College']['id']);
                    //debug($grade_scale_and_type);

                    if (count($grade_scale_and_type['GradeScale']) == 0) {
                        $grade_type_detail = $this->Course->find('first', array('conditions' => array('Course.id' => $course_detail['Course']['id']), 'contain' => array('GradeType')));
                        //debug($grade_type_detail);

                        $recomended_grade_types = array();
                        $recomended_grade_types_count = 0;

                        $available_grade_types =  $this->GradeScale->find('all', array(
                            'conditions' => array(
                                'GradeScale.model' => 'College',
                                'GradeScale.foreign_key' => $course_detail['Course']['Curriculum']['Department']['College']['id'],
                                'GradeScale.program_id' => $course_detail['Course']['Curriculum']['program_id'],
                                'GradeScale.active' => 1
                            ),
                            'contain' => array(
                                'GradeType' => array(
                                    'conditions' => array(
                                        'GradeType.active' => 1,
                                        'GradeType.used_in_gpa' => (isset($grade_type_detail['GradeType']['used_in_gpa']) ? $grade_type_detail['GradeType']['used_in_gpa'] : array(1,0)),
                                        'GradeType.scale_required' => (isset($grade_type_detail['GradeType']['scale_required']) ? $grade_type_detail['GradeType']['scale_required'] : array(1,0)),
                                    )
                                )
                            )
                        ));

                        //debug($available_grade_types);

                        if (!empty($available_grade_types)) {
                            foreach ($available_grade_types as $k => $grTypes) {
                                if (!empty($grTypes['GradeType']['type']) && !in_array($grTypes['GradeType']['type'], $recomended_grade_types)) {
                                    $recomended_grade_types[] = $grTypes['GradeType']['type'];
                                }
                            }
                        }

                        if (!empty($recomended_grade_types)) {
                            $recomended_grade_types_count = count($recomended_grade_types);
                            $recomended_grade_types = '"' . (implode('" or "', $recomended_grade_types)) . '"';
                        }

                        //debug($recomended_grade_types_count);
                        //debug($recomended_grade_types);

                        if ($recomended_grade_types_count) {
                            $grade_scale['error'] = 'Grade scale is not defined/deactivated for "' . $grade_type_detail['GradeType']['type'] . '" grade type under ' . $course_detail['Course']['Curriculum']['Department']['College']['name'] . ' for <b><u>' . $course_detail['Course']['Curriculum']['Program']['name'] . '</u></b> program. Please contact ' . $course_detail['Course']['Curriculum']['Department']['name'] .  ' department to change the grade type of ' .  $course_detail['Course']['course_code_title'] . ' course under ' .  $course_detail['Course']['Curriculum']['name'] . ' curriculum from "' . $grade_type_detail['GradeType']['type'] . '" to ' . $recomended_grade_types . ' grade type which have a defined active grade scales and try to submit the grade for the course.';
                        } else {
                            $grade_scale['error'] = 'Grade scale is not defined/deactivated for "' . $grade_type_detail['GradeType']['type'] . '" grade type under ' . $course_detail['Course']['Curriculum']['Department']['College']['name'] . ' for <b><u>' . $course_detail['Course']['Curriculum']['Program']['name'] . '</u></b> program. Please contact the registrar to set grade scale and you can submit grade for the course.';
                        }
                    } elseif (count($grade_scale_and_type['GradeScale']) > 1) {
                        $grade_scale['error'] = 'Multiple grade scale for the same grade type is set by the ' . $course_detail['Course']['Curriculum']['Department']['College']['name'] . ' for ' . $grade_scale_detail['Course']['course_title'] . ' (' . $grade_scale_detail['Course']['course_code'] . ') course. Please contact your ' . $course_detail['Course']['Curriculum']['Department']['College']['name'] . ' to deactivate grade scales which are not on use.';
                    } else {
                        $grade_scale_detail = $this->GradeScale->find('first', array(
                            'conditions' => array(
                                'GradeScale.id' => $grade_scale_and_type['GradeScale']['0']['id']
                            ),
                            'contain' => array(
                                'GradeScaleDetail' => array(
                                    'order' => array('maximum_result' => 'DESC'),
                                    'Grade' => array('GradeType')
                                )
                            )
                        ));

                        //debug($grade_scale_detail);
                        $grade_scale['scale_by'] = 'College';
                        $grade_scale['Course'] = $course_detail['Course'];

                        $grade_scale['GradeType'] = $grade_scale_detail['GradeScaleDetail']['0']['Grade']['GradeType'];
                        $formated_grade_scale_details = array();
                        $count = 0;

                        if (!empty($grade_scale_detail)) {
                            foreach ($grade_scale_detail['GradeScaleDetail'] as $key => $grade_scale_det) {
                                $formated_grade_scale_details[$count]['minimum_result'] = $grade_scale_det['minimum_result'];
                                $formated_grade_scale_details[$count]['maximum_result'] = $grade_scale_det['maximum_result'];
                                $formated_grade_scale_details[$count]['grade'] = $grade_scale_det['Grade']['grade'];
                                $formated_grade_scale_details[$count]['point_value'] = $grade_scale_det['Grade']['point_value'];
                                $formated_grade_scale_details[$count]['repeatable'] = $grade_scale_det['Grade']['allow_repetition'];
                                $formated_grade_scale_details[$count++]['pass_grade'] = $grade_scale_det['Grade']['pass_grade'];
                            }
                        }

                        $grade_scale['GradeScaleDetail'] = $formated_grade_scale_details;
                        $grade_scale['GradeScale'] = $grade_scale_detail['GradeScale'];
                        //debug($grade_scale);
                    }
                }
            }
        }
        return $grade_scale;
    }

    function lastPublishedCoursesForSection($section_id = null)
    {
        $published_courses_list = array();

        if (!empty($section_id)) {
            $last_ac_and_semester = $this->find('first', array(
                'fields' => array(
                    'academic_year',
                    'semester'
                ),
                'conditions' => array(
                    'PublishedCourse.section_id' => $section_id,
                ),
                'order' => array('PublishedCourse.created' => 'DESC'),
                'contain' => array()
            ));
            //debug($last_ac_and_semester);

            $published_courses = array();

            if (!empty($last_ac_and_semester)) {
                $published_courses = $this->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.academic_year' => $last_ac_and_semester['PublishedCourse']['academic_year'],
                        'PublishedCourse.semester' => $last_ac_and_semester['PublishedCourse']['semester'],
                        'PublishedCourse.section_id' => $section_id,
                        'PublishedCourse.drop' => 0
                    ),
                    'contain' => array('Course')
                ));
            }
            //debug($published_courses);

            if (!empty($published_courses)) {
                foreach ($published_courses as $key => $published_course) {
                    $published_courses_list[$published_course['PublishedCourse']['id']] = $published_course['Course']['course_title'] . ' (' . $published_course['Course']['course_code'] . ') [' . $last_ac_and_semester['PublishedCourse']['academic_year'] . ' Acdamic year, ' . $last_ac_and_semester['PublishedCourse']['semester'] . ' Semester]';
                }
            }
        }
        //debug($published_courses_list);

        return $published_courses_list;
    }

    function sectionPublishedCourses($section_id = null)
    {
        $published_courses_list = array();

        if (!empty($section_id)) {
            $published_courses = $this->find('all', array(
                'conditions' => array(
                    'PublishedCourse.section_id' => $section_id,
                    'PublishedCourse.drop' => 0
                ),
                'contain' => array('Course')
            ));

            //debug($published_courses);
            // check the acyear and semester appear correctly or remove them Neway
            if (!empty($published_courses)) {
                foreach ($published_courses as $key => $published_course) {
                    $published_courses_list[$published_course['PublishedCourse']['id']] = $published_course['Course']['course_title'] . ' (' . $published_course['Course']['course_code'] . ') [' . $published_course['PublishedCourse']['academic_year'] . ' Acdamic year, ' . $published_course['PublishedCourse']['semester'] . ' Semester]';
                }
            }
        }

        //debug($published_courses_list);
        return $published_courses_list;
    }

    function isItValidGradeForPublishedCourse($published_course_id, $grade)
    {
        $grade_scale_details_all = $this->getGradeScaleDetail($published_course_id);
        $grade_scale_details = $grade_scale_details_all['GradeScaleDetail'];

        $valid_grades = array();

        if (!empty($grade_scale_details)) {
            foreach ($grade_scale_details as $key => $scale) {
                $valid_grades[] = $scale['grade'];
            }
        }

        if (in_array($grade, $valid_grades)) {
            return true;
        } else {
            return false;
        }
    }

    function getInstructorByExamGradeId($exam_grade_id = null)
    {
        $course_instructor = null;

        if (!empty($exam_grade_id)) {
            $exam_grade_detail = $this->CourseRegistration->ExamGrade->find('first', array(
                'conditions' => array(
                    'ExamGrade.id' => $exam_grade_id
                ),
                'contain' => array(
                    'CourseAdd' => array(
                        'PublishedCourse' => array(
                            'CourseInstructorAssignment' => array(
                                'conditions' => array(
                                    'CourseInstructorAssignment.type LIKE \'%Lecture%\''
                                ),
                                'Staff'
                            )
                        )
                    ),
                    'CourseRegistration' => array(
                        'PublishedCourse' => array(
                            'CourseInstructorAssignment' => array(
                                'conditions' => array(
                                    'CourseInstructorAssignment.type LIKE \'%Lecture%\''
                                ),
                                'Staff'
                            )
                        )
                    )
                )
            ));



            if (isset($exam_grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) && !empty($exam_grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'])) {
                $course_instructor = $exam_grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
            } elseif (isset($exam_grade_detail['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) && !empty($exam_grade_detail['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'])) {
                $course_instructor = $exam_grade_detail['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
            }
        }

        return $course_instructor;
    }

    function getPublishedCourseByExamGradeId($exam_grade_id = null)
    {
        $published_course = null;

        if (!empty($exam_grade_id)) {
            $exam_grade_detail = $this->CourseRegistration->ExamGrade->find('first', array(
                'conditions' => array(
                    'ExamGrade.id' => $exam_grade_id
                ),
                'contain' => array(
                    'CourseAdd' => array('PublishedCourse'),
                    'CourseRegistration' => array('PublishedCourse')
                )
            ));

            if (isset($exam_grade_detail['CourseRegistration']['PublishedCourse']) && !empty($exam_grade_detail['CourseRegistration']['PublishedCourse'])) {
                $published_course = $exam_grade_detail['CourseRegistration']['PublishedCourse'];
            } elseif (isset($exam_grade_detail['CourseAdd']['PublishedCourse']) && !empty($exam_grade_detail['CourseAdd']['PublishedCourse'])) {
                $published_course = $exam_grade_detail['CourseAdd']['PublishedCourse'];
            }
        }

        return $published_course;
    }

    function previous_semester_and_academic_course_published($given_semester = null, $given_academic_year = null, $department_id = null, $program_id = null, $program_type_id = null, $year_level_id = null, $section_id = null)
    {
        if ($given_semester == 'I') {
            $previous_ac_semester = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester($given_academic_year, 'I');
        } else {
            $previous_ac_semester = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester($given_academic_year, $given_semester);
        }

        //check if section has already published courses
        if (isset($section_id) && !empty($section_id)) {
            $publishedCourseInThatSection = $this->find('first', array('conditions' => array('PublishedCourse.section_id' => $section_id), 'recursive' => -1, 'order' => array('PublishedCourse.created DESC')));
            $sectionDetail = $this->Section->find('first', array('conditions' => array('Section.id' => $section_id), 'recursive' => -1, 'order' => array('Section.created DESC')));

            if (isset($publishedCourseInThatSection) && !empty($publishedCourseInThatSection) && isset($publishedCourseInThatSection['PublishedCourse']['department_id']) && !empty($publishedCourseInThatSection['PublishedCourse']['department_id'])) {
                //transfered to a new department the whole section
                if ($department_id != $publishedCourseInThatSection['PublishedCourse']['department_id'] && $sectionDetail['Section']['department_id'] == $department_id) {
                    $department_id = $publishedCourseInThatSection['PublishedCourse']['department_id'];
                    $year_level_id = $publishedCourseInThatSection['PublishedCourse']['year_level_id'];
                }
            }
        }


        $is_course_published = $this->find('count', array(
            'conditions' => array(
                'PublishedCourse.semester' => $previous_ac_semester['semester'],
                'PublishedCourse.academic_year LIKE ' => $previous_ac_semester['academic_year'] . '%',
                'PublishedCourse.department_id' => $department_id,
                'PublishedCourse.program_id' => $program_id,
                'PublishedCourse.program_type_id' => $program_type_id,
                'PublishedCourse.year_level_id' => $year_level_id,
                'PublishedCourse.section_id' => $section_id
            )
        ));

        if ($is_course_published > 0) {
            return true;
        } else {
            $first_time = $this->find('count', array('conditions' => array('PublishedCourse.section_id' => $section_id)));
            //think engineering first year students ? How the system allows second semester registration first_time == 0 and yearlevel = 1st and college_id = engineering and given_semester = II

            $freshdetail = $this->YearLevel->find('first', array(
                'conditions' => array(
                    'YearLevel.id' => $year_level_id,
                ),
                'contain' => array('Department')
            ));

            //find one student from students section
            $oneSampleStudent = ClassRegistry::init('StudentsSection')->find('first', array(
                'conditions' => array(
                    'StudentsSection.section_id' => $section_id,
                    'StudentsSection.archive' => 0
                ),
                'recursive' => -1
            ));

            //find one student from students section
            if (isset($oneSampleStudent)) {
                $getStudentPreSection = ClassRegistry::init('StudentsSection')->find('first', array(
                    'conditions' => array(
                        'StudentsSection.student_id' => $oneSampleStudent['StudentsSection']['student_id'],
                        'StudentsSection.archive' => 1
                    )
                ));
            }

            if (isset($getStudentPreSection['StudentsSection']['section_id'])) {
                $wasTheStudentHasPreProgram = $this->Section->find('count', array(
                    'conditions' => array(
                        'Section.id' => $getStudentPreSection['StudentsSection']['section_id'],
                        'Section.department_id is null'
                    )
                ));
            }


            if (($first_time == 0 && $given_semester == 'I') || ($first_time == 0 && $given_semester == 'II' && $freshdetail['YearLevel']['name'] == '1st' && $wasTheStudentHasPreProgram)) {
                return true;
            } else {
                //check if the student has published course in first semester in some section
                if (!empty($oneSampleStudent)) {
                    $findMostRecentSection = ClassRegistry::init('StudentsSection')->find('first', array(
                        'conditions' => array(
                            'StudentsSection.student_id' => $oneSampleStudent['StudentsSection']['student_id'],
                            'StudentsSection.section_id !=' => $section_id
                        ),
                        'order' => 'StudentsSection.created DESC',
                        'recursive' => -1
                    ));

                    $isCoursepublished = $this->find('count', array(
                            'conditions' => array(
                                'PublishedCourse.semester' => $previous_ac_semester['semester'],
                                'PublishedCourse.academic_year LIKE ' => $previous_ac_semester['academic_year'] . '%',
                                'PublishedCourse.department_id' => $department_id,
                                'PublishedCourse.program_id' => $program_id,
                                'PublishedCourse.program_type_id' => $program_type_id,
                                'PublishedCourse.year_level_id' => $year_level_id,
                                'PublishedCourse.section_id' => $findMostRecentSection['StudentsSection']['section_id']
                            )
                    ));

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

    function get_section_organized_published_courses_scale_attachment($data = null, $department_id = null, $publishedcourses = null, $college_id = null)
    {
        if (strcasecmp($department_id, 'pre') === 0) {
            $sections = $this->Section->find('list', array(
                'conditions' => array(
                    'Section.college_id' => $college_id,
                    'Section.department_id is null',
                    'Section.program_id' => PROGRAM_UNDEGRADUATE,
                    'Section.program_type_id' => PROGRAM_TYPE_REGULAR,
                    'Section.archive' => 0
                ),
                'recursive' => -1
            ));
        } else {
            $sections = $this->Section->find('list', array(
                'conditions' => array(
                    'Section.department_id' => $department_id,
                    'Section.program_id' => $data['PublishedCourse']['program_id'],
                    'Section.archive' => 0
                ),
                'recursive' => -1
            ));
        }

        //format section display
        if (!empty($sections) && !empty($publishedcourses)) {
            $section_organized_published_courses = array();
            foreach ($sections as $section_id => $section_name) {
                foreach ($publishedcourses as $kkk => &$vvv) {
                    if ($vvv['PublishedCourse']['section_id'] == $section_id) {
                        if ($this->CourseRegistration->ExamGrade->is_grade_submitted($vvv['PublishedCourse']['id']) > 0) {
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

    function isPublishedCourseRequiredScale($published_course_id)
    {
        $requiredScale = $this->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $published_course_id,
            ),
            'contain' => array('Course' => array('GradeType'))
        ));

        return $requiredScale;
    }

    function isCoursePublishedInSection($sectionId)
    {
        $count = $this->find('count', array(
            'conditions' => array(
                'PublishedCourse.section_id' => $sectionId,
            ),
            'recursive' => -1
        ));

        return $count;
    }

    function listSimilarPublishedCoursesForCombo($publishedCourseId = null)
    {
        $publishedList = array();

        if (!empty($publishedCourseId)) {
            $published_course = $this->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $publishedCourseId
                ),
                'contain' => array(
                    'Section',
                    'YearLevel',
                    'GivenByDepartment',
                    'CourseInstructorAssignment' => array(
                        'Staff' => array(
                            'Department',
                            'Title' => array('id', 'title'),
                            'Position' => array('id', 'position')
                        )
                    ),
                    'Course'
                )
            ));

            $pubList = $this->find('all', array(
                'conditions' => array(
                    'PublishedCourse.course_id' => $published_course['PublishedCourse']['course_id'],
                    'PublishedCourse.semester' => $published_course['PublishedCourse']['semester'],
                    'PublishedCourse.academic_year' => $published_course['PublishedCourse']['academic_year']
                ),
                'contain' => array(
                    'Section',
                    'YearLevel',
                    'GivenByDepartment',
                    'CourseInstructorAssignment' => array(
                        'Staff' => array(
                            'Department',
                            'Title' => array('id', 'title'),
                            'Position' => array('id', 'position')
                        )
                    ),
                    'Course'
                )
            ));

            if (!empty($pubList)) {
                $publishedList[''] = '[ Select Section to Assign ]';
                foreach ($pubList as $key => $value) {
                    $publishedList[$value['PublishedCourse']['id']] = $value['Course']['course_title'] . ' (' . $value['Course']['course_code'] . ') ' . $value['Section']['name'] . ' (' . (!empty($value['YearLevel']['name']) ? $value['YearLevel']['name'] : ($value['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $value['Section']['academicyear'] . ') ' . (isset($value['CourseInstructorAssignment'][0]) ? $value['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . ' ' . $value['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $value['CourseInstructorAssignment'][0]['Staff']['Department']['name'] . ')' : '');
                }
            }
        }

        return $publishedList;
    }
}
