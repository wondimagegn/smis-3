<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseDropsTable extends Table
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
    public function validationDefault(Validator $validator)
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
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['course_registration_id'], 'CourseRegistrations'));

        return $rules;
    }

    // info student to drop courses which failed the prerequisite

    function prequisite_taken($student_id = null, $prerequisite_course_idd = null)
    {
        //check the student is transfered and have exemption approved
        $exemptedCourse = ClassRegistry::init('CourseExemption')->isCourseExempted($student_id, $prerequisite_course_idd);

        if (!empty($exemptedCourse)) {
            return true;
        }

        $prerequisite_course_id = $this->CourseRegistration->PublishedCourse->Course->getTakenEquivalentCourses($student_id, $prerequisite_course_idd);

        $publishedCourseIds = $this->CourseRegistration->PublishedCourse->find('list', array(
            'conditions' => array(
                'PublishedCourse.course_id' => $prerequisite_course_id
            ),
            'fields' => array('id'),
            'recursive' => -1
        ));

        //debug(implode(",", $publishedCourseIds));

        if (!empty($publishedCourseIds)) {

            $course_registration_ids = $this->CourseRegistration->find('list', array(
                'conditions' => array(
                    'CourseRegistration.published_course_id' => $publishedCourseIds,
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.id not in (select course_registration_id from course_drops where student_id = ' .$student_id . ' and registrar_confirmation = 1 and department_approval = 1) '
                ),
                'fields' => 'id',
                //'order' => array('CourseRegistration.created' => 'DESC')
                'order' => array('CourseRegistration.academic_year' => 'DESC', 'CourseRegistration.semester' => 'DESC', 'CourseRegistration.id' => 'DESC', 'CourseRegistration.created' => 'DESC')
            ));

            //debug($course_registration_ids);

            //check student is passed ?
            if (!empty($course_registration_ids)) {

                foreach ($course_registration_ids as $key => $course_registration_id) {

                    $latest_grade = $this->CourseRegistration->getCourseRegistrationLatestApprovedGradeDetail($course_registration_id);

                    //prerequisite taken but grade is not submitted
                    if (empty($latest_grade)) {
                        return 2; // on hold
                    }

                    if (strcasecmp($latest_grade['type'], 'Change') == 0) {
                        $grade_scale_id = $this->CourseRegistration->ExamGrade->field('grade_scale_id', array('ExamGrade.id' => $latest_grade['ExamGrade']['exam_grade_id']));
                        $gradeSubmitted = $this->isGradePassed($latest_grade['ExamGrade']['grade'], $grade_scale_id);
                    } else {
                        $gradeSubmitted = $this->isGradePassed($latest_grade['ExamGrade']['grade'], $latest_grade['ExamGrade']['grade_scale_id']);
                    }

                    //student is qualified to take course
                    if ($gradeSubmitted == 1) {
                        return true; //normal registration
                    }
                }
            }

            //check add
            $course_add_ids = ClassRegistry::init('CourseAdd')->find('list', array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $publishedCourseIds,
                    'CourseAdd.student_id' => $student_id
                ),
                'fields' => 'id',
                //'order' => array('CourseAdd.created' => 'DESC')
                'order' => array('CourseAdd.academic_year' => 'DESC', 'CourseAdd.semester' => 'DESC', 'CourseAdd.id' => 'DESC', 'CourseAdd.created' => 'DESC')
            ));

            if (!empty($course_add_ids)) {

                foreach ($course_add_ids as $key => $course_add_id) {

                    $latest_grade = ClassRegistry::init('CourseAdd')->getCourseAddLatestApprovedGradeDetail($course_add_id);

                    //prerequist taken but grade is not submitted
                    if (empty($latest_grade)) {
                        return 2;
                    }

                    //$gradeSubmitted = $this->isGradePassed($latest_grade['ExamGrade']['grade'], $latest_grade['ExamGrade']['grade_scale_id']);

                    if (isset($latest_grade['type']) && strcasecmp($latest_grade['type'], 'Change') == 0) {
                        $grade_scale_id = $this->CourseRegistration->ExamGrade->field('grade_scale_id', array('ExamGrade.id' => $latest_grade['ExamGrade']['exam_grade_id']));
                        $gradeSubmitted = $this->isGradePassed($latest_grade['ExamGrade']['grade'], $grade_scale_id);
                    } else {
                        $gradeSubmitted = $this->isGradePassed($latest_grade['ExamGrade']['grade'], $latest_grade['ExamGrade']['grade_scale_id']);
                    }

                    //student is qualified to take course
                    if ($gradeSubmitted == 1) {
                        return true; //normal registration
                    }
                }
            }
        }

        return false;  //failed the prerequiste
    }

    //check course is taken beforing displaying list of add courses for students.

    function course_taken($student_id = null, $course_id = null)
    {
        /**
         *1 -exclude from add
         *2 -exclude from add
         *3 -allow add
         *4 failed prequisite
         */

        //does the course has prerequiste

        if (!empty($course_id) && $this->isPrerequisteExist($course_id) === true) {

            $prerequisite_course_id = $this->getPrerequisteCourseId($course_id);

            $listOfPrerequistes = $this->CourseRegistration->PublishedCourse->Course->Prerequisite->find('list', array(
                'conditions' => array('Prerequisite.course_id' => $course_id),
                'fields' => array(
                    'Prerequisite.prerequisite_course_id',
                    'Prerequisite.prerequisite_course_id'
                )
            ));

            //debug($listOfPrerequistes);

            if (isset($listOfPrerequistes) && !empty($listOfPrerequistes)) {
                foreach ($listOfPrerequistes as $k => $v) {
                    if (!empty($v)) {
                        if ($this->prequisite_taken($student_id, $v) === false) {
                            return 4;
                        }
                    }
                }
            }

        }

        if (!empty($course_id)) {

            $equivalent_course_taken = $this->CourseRegistration->PublishedCourse->Course->getTakenEquivalentCourses($student_id, $course_id);

            $publishedCourseIds = $this->CourseRegistration->PublishedCourse->find('list', array(
                'conditions' => array(
                    'PublishedCourse.course_id' => $equivalent_course_taken
                ),
                'fields' => array('id'),
                'recursive' => -1
            ));
        }


        if (isset($publishedCourseIds) && !empty($publishedCourseIds)) {
            $course_registration_ids = $this->CourseRegistration->find('list', array(
                'conditions' => array(
                    'CourseRegistration.published_course_id' => $publishedCourseIds,
                    'CourseRegistration.id not in (select course_registration_id from course_drops where registrar_confirmation = 1 and department_approval = 1)',
                    'CourseRegistration.student_id' => $student_id
                ),
                'fields' => 'id',
                //'order' => array('CourseRegistration.created' => 'DESC')
                'order' => array('CourseRegistration.academic_year' => 'ASC', 'CourseRegistration.semester' => 'ASC', 'CourseRegistration.id' => 'ASC')
            ));
        }

        //check student is passed ?
        if (isset($course_registration_ids) && !empty($course_registration_ids)) {
            foreach ($course_registration_ids as $key => $course_registration_id) {
                // $latest_grade = $this->CourseRegistration->getCourseRegistrationLatestApprovedGradeDetail($course_registration_id);
                $latest_grade = $this->CourseRegistration->ExamGrade->getApprovedGrade($course_registration_id, 1);

                //course taken but grade is on  exclude from add
                if (empty($latest_grade)) {
                    return 2; // on exclude
                }

                /* if (strcasecmp($latest_grade['type'], 'Change') == 0) {
                    $grade_scale_id = $this->CourseRegistration->ExamGrade->field('grade_scale_id', array('ExamGrade.id' => $latest_grade['ExamGrade']['exam_grade_id']));
                    $gradeSubmitted = $this->isTheGradeAllowedForRepetition($latest_grade['ExamGrade']['grade'], $grade_scale_id);
                    //debug($gradeSubmitted);
                } else {
                    $gradeSubmitted = $this->isTheGradeAllowedForRepetition($latest_grade['ExamGrade']['grade'], $latest_grade['ExamGrade']['grade_scale_id']);
                } */
                //debug($gradeSubmitted);

                if (isset($latest_grade['grade']) && !empty($latest_grade['grade'])) {
                    $gradeSubmitted = $this->isTheGradeAllowedForRepetition($latest_grade['grade'], $latest_grade['grade_scale_id']);
                } else {
                    $gradeSubmitted = $this->isTheGradeAllowedForRepetition($latest_grade['ExamGrade']['grade'], $latest_grade['ExamGrade']['grade_scale_id']);
                }

                //student is allowed for repetation
                if ($gradeSubmitted == 1) {
                    return 3; // allow rep
                } else if ($gradeSubmitted == 0) {
                    return 2;
                }
            }
        }

        if (isset($publishedCourseIds) && !empty($publishedCourseIds)) {
            //check add
            $course_add_ids = ClassRegistry::init('CourseAdd')->find('list', array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $publishedCourseIds,
                    'CourseAdd.registrar_confirmation' => 1,
                    'CourseAdd.department_approval' => 1,
                    'CourseAdd.student_id' => $student_id
                ),
                'fields' => 'id',
                //'order' => array('CourseAdd.created' => 'DESC')
                'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC'),
            ));

            if (!empty($course_add_ids)) {
                foreach ($course_add_ids as $key => $course_add_id) {

                    //$latest_grade = ClassRegistry::init('CourseAdd')->getCourseAddLatestApprovedGradeDetail($course_add_id);

                    $latest_grade = ClassRegistry::init('ExamGrade')->getApprovedGrade($course_add_id, 0);

                    //course taken but grade is exclude from add
                    if (empty($latest_grade)) {
                        return 2; // on exclude
                    }

                    if (isset($latest_grade['grade']) && !empty($latest_grade['grade'])) {
                        $gradeSubmitted = $this->isTheGradeAllowedForRepetition($latest_grade['grade'], $latest_grade['grade_scale_id']);
                    } else {
                        $gradeSubmitted = $this->isTheGradeAllowedForRepetition($latest_grade['ExamGrade']['grade'], $latest_grade['ExamGrade']['grade_scale_id']);
                    }

                    //student is already taken courses dont allow add
                    if ($gradeSubmitted == 1) {
                        return 3; //normal registration
                    } else if ($gradeSubmitted == 0) {
                        return 2;
                    }
                }
            }
        }

        return 3;  //not taken any of the course allow
    }

    // Is the given grade pass or fail

    function isGradePassed($grade = null, $scale_id = null)
    {
        $is_grade_pass_mark = ClassRegistry::init('GradeScale')->find('first', array(
            'conditions' => array('GradeScale.id' => $scale_id),
            'contain' => array(
                'GradeScaleDetail' => array(
                    'Grade' => array('id', 'pass_grade', 'grade')
                )
            )
        ));

        //debug($grade);

        if (strcasecmp($grade, 'I') == 0) {
            return 1;
        }

        if (isset($is_grade_pass_mark['GradeScaleDetail']) && !empty($is_grade_pass_mark['GradeScaleDetail'])) {
            foreach ($is_grade_pass_mark['GradeScaleDetail'] as $index => $value) {
                if (isset($grade) && !empty($grade)) {
                    if ((strcasecmp($value['Grade']['grade'], $grade) == 0 && $value['Grade']['pass_grade'] == 1) || (strcasecmp($value['Grade']['grade'], 'I') == 0)) {
                        return 1;
                    }
                }
            }
        }

        return 0;
    }

    // Is the given grade pass or fail
    function isTheGradeAllowedForRepetition($grade = null, $scale_id = null)
    {
        $is_grade_allow_repetition = ClassRegistry::init('GradeScale')->find('first', array(
            'conditions' => array('GradeScale.id' => $scale_id),
            'contain' => array(
                'GradeScaleDetail' => array(
                    'Grade' => array(
                        'id',
                        'pass_grade',
                        'grade',
                        'allow_repetition'
                    )
                )
            )
        ));

        debug($grade);
        debug($scale_id);

        if (isset($is_grade_allow_repetition['GradeScaleDetail']) && !empty($is_grade_allow_repetition['GradeScaleDetail'])) {
            foreach ($is_grade_allow_repetition['GradeScaleDetail'] as $index => $value) {
                if (isset($grade) && !empty($grade)) {
                    if (strcasecmp($value['Grade']['grade'], $grade) == 0 && $value['Grade']['allow_repetition']) {
                        return 1;
                    }
                }
            }
        }

        return 0;
    }

    function dropRecommendedCourses($semester = null, $academic_year = null, $student_id = null)
    {
        $coursesDrop = $this->CourseRegistration->find('all', array(
            'conditions' => array(
                'CourseRegistration.academic_year LIKE ' => $academic_year . '%',
                'CourseRegistration.semester like ' => $semester . '%',
                'CourseRegistration.student_id' => $student_id,
                //'Student.id NOT IN (select student_id from graduate_lists)',
                'Student.graduated' => 0,
                'CourseRegistration.id NOT IN (select course_registration_id from course_drops)',
                'CourseRegistration.id NOT IN (select course_registration_id from exam_grades)'
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'Prerequisite' => array('id', 'course_id',  'prerequisite_course_id', 'co_requisite')
                    )
                ),
                'Student' => array('id', 'full_name', 'studentnumber', 'gender', 'graduated'),
                'ExamGrade'
            )
        ));

        $course_drop_reformat = array();
        $count = 0;

        if (!empty($coursesDrop)) {
            foreach ($coursesDrop as $index => $value) {
                if (!empty($value['PublishedCourse']['Course']['Prerequisite'])) {
                    $passed_count = 0;
                    foreach ($value['PublishedCourse']['Course']['Prerequisite'] as $preindex => $prevalue) {
                        $pre_passed = $this->prequisite_taken($this->student_id, $prevalue['prerequisite_course_id']);
                        if ($pre_passed) {
                            $passed_count++;
                        }
                    }

                    if ($passed_count == count($value['PublishedCourse']['Course']['Prerequisite'])) {
                        $course_drop_reformat[$count] = $value;
                        $course_drop_reformat[$count]['prequisite_taken_passsed'] = 1;
                    } else {
                        $course_drop_reformat[$count] = $value;
                        $course_drop_reformat[$count]['prequisite_taken_passsed'] = 0;
                    }

                } else {
                    $course_drop_reformat[$count] = $value;
                    $course_drop_reformat[$count]['prequisite_taken_passsed'] = 1;
                }

                $count++;
            }
        }

        return $course_drop_reformat;
    }

    function list_of_students_need_force_drop($department_ids = null, $college_ids = null, $program_ids = null, $program_type_ids = null, $academic_year = null, $semester = null, $freshmaninclude = 0)
    {

        //$type_of_registrations = array(31, 32, 33);
        $type_of_registrations = array(11, 12, 13);

        $list_of_registred_ids = array();
        $coursesDrop = array();
        $options = array();

        $options['conditions']['CourseRegistration.type'] = $type_of_registrations;
        $options['group'] = array('CourseRegistration.student_id'/* , 'CourseRegistration.section_id' */);

        if (!empty($academic_year)) {
            $options['conditions']['CourseRegistration.academic_year'] = $academic_year;
        }

        if (!empty($semester)) {
            $options['conditions']['CourseRegistration.semester'] = $semester;
        }

        if (!empty($freshmaninclude) && !empty($college_ids)) {
            //$options['conditions'][] = 'CourseRegistration.year_level_id IS NULL OR CourseRegistration.year_level_id = 0 OR CourseRegistration.year_level_id = ""';
            $options['conditions']['OR'] = array('CourseRegistration.year_level_id = ""', 'CourseRegistration.year_level_id IS NULL', 'CourseRegistration.year_level_id = 0');
        }

        if (!empty($department_ids)) {

            $options['contain'] = array(
                'Student'  => array(
                    'conditions' => array(
                        'Student.department_id' => $department_ids,
                        'Student.program_id' => $program_ids,
                        'Student.program_type_id' => $program_type_ids,
                        'Student.graduated' => 0,
                    ),
                    'fields' => array('id', 'department_id')
                )
            );

        } else if (!empty($college_ids)) {

            $options['contain'] = array(
                'Student'  => array(
                    'conditions' => array(
                        'Student.department_id IS NULL',
                        'Student.college_id' => $college_ids,
                        'Student.program_id' => $program_ids,
                        'Student.program_type_id' => $program_type_ids,
                        'Student.graduated' => 0,
                    ),
                    'fields' => array('id', 'college_id', 'department_id')
                )
            );
        }

        //debug($options);

        $listOfStudentCourseRegistrations = $this->CourseRegistration->find('all', $options);

        debug(count($listOfStudentCourseRegistrations));

        if (!empty($listOfStudentCourseRegistrations)) {
            foreach ($listOfStudentCourseRegistrations as $index => $registred_id) {
                //incase regisration without student id
                //$list_of_registred_ids[] = $registred_id['CourseRegistration']['id'];
                if (isset($registred_id['Student']['department_id']) && !empty($registred_id['Student']['department_id']) && !empty($registred_id['CourseRegistration']['student_id'])) {
                    if ((is_array($department_ids) && in_array($registred_id['Student']['department_id'], $department_ids)) || (!is_array($department_ids) && $registred_id['Student']['department_id'] == $department_ids)) {
                        $list_of_registred_ids[] = $registred_id['CourseRegistration']['id'];
                    }
                } else if (isset($registred_id['Student']['college_id']) && !empty($registred_id['Student']['college_id']) && !empty($registred_id['CourseRegistration']['student_id'])) {
                    if ((is_array($college_ids) && in_array($registred_id['Student']['college_id'], $college_ids)) || (!is_array($college_ids) && $registred_id['Student']['college_id'] == $college_ids)) {
                        $list_of_registred_ids[] = $registred_id['CourseRegistration']['id'];
                    }
                }
            }
        }

        $coursesDrop['list'] = array();
        $coursesDrop['count'] = 0;

        if (!empty($list_of_registred_ids)) {
            $coursesDrop['list'] = $this->CourseRegistration->find('all', array(
                'conditions' => array(
                    'CourseRegistration.id' => $list_of_registred_ids,
                    //'Student.id NOT IN (select student_id from graduate_lists)',
                    //'CourseRegistration.id NOT IN (select course_registration_id from course_drops where course_registration_id is not null )',
                    //'CourseRegistration.id NOT IN (select course_registration_id from exam_grades where course_registration_id is not null )'
                ),
                'contain' => array(
                    'Student' => array(
                        'Department' => array('id', 'name'),
                        'College' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Program' => array('id', 'name'),
                        'fields' => array('id', 'program_id', 'program_type_id', 'department_id', 'college_id', 'studentnumber', 'full_name', 'gender', 'graduated')
                    ),
                    'CourseDrop',
                    'ExamGrade'
                )
            ));

            //debug($coursesDrop['list'][0]);

            if (!empty($coursesDrop['list'])) {
                foreach ($coursesDrop['list'] as $key => &$cdrop) {
                    if (!empty($cdrop['ExamGrade']) || !empty($cdrop['CourseDrop'])) {
                        unset($coursesDrop['list'][$key]);
                        continue;
                    }
                }
            }

            $coursesDrop['count'] = count($coursesDrop['list']);

        }

        return $coursesDrop;
    }

    function drop_courses_list($student_id = null, $academic_year = null)
    {
        $student_detail_with_list_of_registred_coures = array();

        $latest_academic_year_semester = $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear($student_id, $academic_year);

        $previous_status_semester = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester(
            $latest_academic_year_semester['academic_year'],
            $latest_academic_year_semester['semester']
        );

        $latest_status_year_semester = $this->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevelOfStatusDisplay(
            $student_id,
            $latest_academic_year_semester['academic_year'],
            $previous_status_semester['semester']
        );

        //	debug( $latest_status_year_semester);
        $student_section_exam_status = $this->CourseRegistration->Student->get_student_section($student_id, $latest_academic_year_semester['academic_year'], $latest_status_year_semester['semester']);

        //StudentBasicInfo
        $student_detail_with_list_of_registred_coures['student_basic'] = $student_section_exam_status;

        //student latest registration semester
        $semester = $this->CourseRegistration->latestCourseRegistrationSemester($academic_year, $student_id);

        if (!empty($student_section_exam_status['StudentBasicInfo']['department_id'])) {
            $coursesDrop = $this->CourseRegistration->find('all', array(
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Prerequisite' => array('id', 'prerequisite_course_id', 'co_requisite'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                            'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours', 'Course.credit')
                        )
                    ),
                    'YearLevel'
                ),
                'conditions' => array(
                    'CourseRegistration.academic_year like' => $academic_year . '%',
                    'CourseRegistration.semester' => $semester,
                    'CourseRegistration.year_level_id' => $student_section_exam_status['Section']['year_level_id'],
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.id NOT IN (select course_registration_id from course_drops) ',
                    'CourseRegistration.id NOT IN (select course_registration_id from exam_grades where course_registration_id is not null )'
                )
            ));
        } else {
            $coursesDrop = $this->CourseRegistration->find('all', array(
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Prerequisite' => array('id', 'prerequisite_course_id', 'co_requisite'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                            'fields' => array('Course.id', 'Course.course_code', 'Course.course_title', 'Course.lecture_hours', 'Course.tutorial_hours', 'Course.laboratory_hours', 'Course.credit')
                        )
                    ),
                    'YearLevel'
                ),
                'conditions' => array(
                    'CourseRegistration.academic_year like' => $academic_year . '%',
                    'CourseRegistration.semester' => $semester,
                    'OR' => array(
                        'CourseRegistration.year_level_id is null',
                        'CourseRegistration.year_level_id = ""',
                        'CourseRegistration.year_level_id = 0',
                    ),
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.id NOT IN (select course_registration_id from course_drops) ',
                    'CourseRegistration.id NOT IN (select course_registration_id from exam_grades where course_registration_id is not null )'
                )
            ));
        }

        //debug($coursesDrop);

        $already_dropped = array();

        if (!empty($coursesDrop)) {
            foreach ($coursesDrop as $index => $value) {
                $check = $this->find('count', array('conditions' => array('CourseDrop.course_registration_id' => $value['CourseRegistration']['id']), 'recursive' => -1));
                if ($check > 0) {
                    $already_dropped[] = $value['CourseRegistration']['id'];
                }
            }
        }

        $student_detail_with_list_of_registred_coures['alreadyDropped'] = $already_dropped;
        $student_detail_with_list_of_registred_coures['courseDrop'] = $coursesDrop;
        $student_detail_with_list_of_registred_coures['semester'] = $latest_academic_year_semester['semester'];

        // $student_detail_with_list_of_registred_coures['academic_year']=$coursesDrop;
        return $student_detail_with_list_of_registred_coures;
    }

    // filter out the list of students who have registred but grade is not submitted

    function student_list_registred_but_not_dropped($data = null, $current_academic_year = null)
    {
        $options = array();
        $search_conditions = array();
        $organized_students = array();

        $latest_semester_academic_year = $this->CourseRegistration->latest_academic_year_semester($current_academic_year);

        //$search_conditions['conditions'][] = array('CourseRegistration.student_id NOT IN  (select student_id from graduate_lists)');
        $search_conditions['conditions'][] = array('Student.graduated' => 0);

        $search_conditions['contain'] = array(
            'Student' => array(
                'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'graduated'),
                'Department' => array('id', 'name'),
                'Program' => array('id', 'name', 'shortname'),
                'ProgramType' => array('id', 'name', 'shortname')
            ),
            'CourseDrop',
            'ExamGrade'
        );

        //debug($latest_semester_academic_year);

        $search_conditions['conditions'][] = array('CourseRegistration.academic_year like ' => $latest_semester_academic_year['academic_year'] . '%');
        $search_conditions['group'] = array('CourseRegistration.student_id');
        $search_conditions['conditions'][] = array('CourseRegistration.id NOT IN (select course_registration_id from exam_grades where course_registration_id is not null)');
        $search_conditions['conditions'][] = array('CourseRegistration.id NOT IN (select course_registration_id from course_drops where course_registration_id is not null)');

        if (!empty($data['Student']['department_id'])) {
            $search_conditions['conditions'][] = array('Student.department_id' => $data['Student']['department_id']);
        }

        if (!empty($data['Student']['studentnumber'])) {
            $search_conditions['conditions'][] = array('Student.studentnumber' => $data['Student']['studentnumber']);
        }

        if (!empty($data['Student']['college_id'])) {
            $search_conditions['conditions'][] = array('Student.college_id' => $data['Student']['college_id'], 'Student.department_id is null');
        }

        if (!empty($data['Student']['semester'])) {
            $search_conditions['conditions'][] = array('CourseRegistration.semester' => $data['Student']['semester']);
        }

        if (!empty($data['Student']['program_id'])) {
            $search_conditions['conditions'][] = array('Student.program_id' => $data['Student']['program_id']);
        }

        if (!empty($data['Student']['program_type_id'])) {
            $search_conditions['conditions'][] = array('Student.program_type_id' => $data['Student']['program_type_id']);
        }

        if (!empty($this->department_ids) && empty($data['Student']['department_id']) && empty($data['Student']['studentnumber'])) {
            $search_conditions['conditions'][] = array('Student.department_id' => $this->department_ids);
        } else if (!empty($this->college_ids) && empty($data['Student']['college_id'])) {
            $search_conditions['conditions'][] = array('Student.college_id' => $this->college_ids, 'Student.department_id is null');
        }

        $result = $this->CourseRegistration->find('all', $search_conditions);
        // debug($result);
        return $result;
    }

    function isPrerequisteExist($course_id = null)
    {
        $prequist = $this->CourseRegistration->PublishedCourse->Course->Prerequisite->find('count', array(
            'conditions' => array(
                'Prerequisite.course_id' => $course_id,
                'Prerequisite.co_requisite' => 0
            )
        ));

        if ($prequist > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getPrerequisteCourseId($course_id = null)
    {
        $prequist = $this->CourseRegistration->PublishedCourse->Course->Prerequisite->find('first', array('conditions' => array('Prerequisite.course_id' => $course_id)));

        if (isset($prequist['Prerequisite']['prerequisite_course_id'])) {
            return 	$prequist['Prerequisite']['prerequisite_course_id'];
        } else {
            return array();
        }
    }

    function list_course_drop_request($role_id = null, $department_id = null, $current_academic_year = null, $college_ids = null)
    {
        $section_organized_published_course = array();
        $courseDrops = array();

        if (!empty($department_id)) {
            $sections = $this->Student->Section->find('list', array(
                'conditions' => array(
                    'Section.department_id' => $department_id,
                    'Section.archive' => 0,
                )
            ));
        } else if (!empty($college_ids)) {
            $sections = $this->Student->Section->find('list', array(
                'conditions' => array(
                    'Section.department_id is null',
                    'Section.college_id ' => $college_ids,
                    'Section.archive' => 0
                )
            ));
        }

        // query according their roles
        $this->Student->bindModel(array('hasMany' => array('StudentsSection')));

        if ($role_id == ROLE_DEPARTMENT) {
            $courseDrops = $this->find('all', array(
                'conditions' => array(
                    'Student.department_id' => $department_id,
                    'CourseDrop.department_approval is null',
                    'CourseDrop.registrar_confirmation is null',
                    'Student.graduated = 0'
                ),
                'contain' => array(
                    'CourseRegistration',
                    'Student' => array(
                        'Department' => array('id', 'name'),
                        'College' => array('id', 'name'),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'StudentsSection' => array(
                            'conditions' => array('StudentsSection.archive = 0')
                        ),
                        'CourseRegistration' => array(
                            'PublishedCourse' => array(
                                'Course' => array(
                                    'fields' => array('id', 'course_detail_hours', 'credit', 'course_title', 'course_code')
                                ),
                                'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                            ),
                            'fields' => array('id')
                        ),
                        'fields' => array('id', 'full_name', 'department_id', 'program_id', 'program_type_id', 'college_id', 'graduated')
                    )
                )
            ));
        } else {
            if (!empty($college_ids)) {
                if ($role_id == ROLE_COLLEGE) {
                    $courseDrops = $this->find('all', array(
                        'conditions' => array(
                            'Student.department_id is null',
                            'Student.college_id ' => $college_ids,
                            'CourseDrop.department_approval is null',
                            'CourseDrop.registrar_confirmation is null',
                            'Student.graduated = 0'
                        ),
                        'contain' => array(
                            'CourseRegistration',
                            'Student' => array(
                                'Department' => array('id', 'name'),
                                'College' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'StudentsSection' => array(
                                    'conditions' => array('StudentsSection.archive = 0')
                                ),
                                'CourseRegistration' => array(
                                    'PublishedCourse' => array(
                                        'Course' => array(
                                            'fields' => array('id', 'course_detail_hours', 'credit', 'course_title', 'course_code')
                                        ),
                                        'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                                    ),
                                    'fields' => array('id')
                                ),
                                'fields' => array('id', 'full_name', 'department_id', 'program_id', 'program_type_id', 'college_id', 'graduated')
                            )
                        )
                    ));
                } else {
                    $courseDrops = $this->find('all', array(
                        'conditions' => array(
                            'Student.department_id is null',
                            'Student.college_id' => $college_ids,
                            'CourseDrop.department_approval = 1',
                            'CourseDrop.registrar_confirmation is null',
                            'Student.graduated = 0'
                        ),
                        'contain' => array(
                            'CourseRegistration',
                            'Student' => array(
                                'Department' => array('id', 'name'),
                                'College' => array('id', 'name'),
                                'Program' => array('id', 'name'),
                                'ProgramType' => array('id', 'name'),
                                'StudentsSection' => array(
                                    'conditions' => array('StudentsSection.archive = 0')
                                ),
                                'CourseRegistration' => array(
                                    'PublishedCourse' => array(
                                        'Course' => array(
                                            'fields' => array('id', 'course_detail_hours', 'credit', 'course_title', 'course_code')
                                        ),
                                        'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                                    ),
                                    'fields' => array('id')
                                ),
                                'fields' => array('id', 'full_name', 'department_id', 'program_id', 'program_type_id', 'college_id', 'graduated')
                            )
                        )
                    ));
                }
            } else {
                $courseDrops = $this->find('all', array(
                    'conditions' => array(
                        'Student.department_id' => $department_id,
                        'CourseDrop.department_approval = 1',
                        'CourseDrop.registrar_confirmation is null',
                        'Student.graduated = 0'
                    ),
                    'contain' => array(
                        'CourseRegistration',
                        'Student' => array(
                            'Department' => array('id', 'name'),
                            'College' => array('id', 'name'),
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'StudentsSection' => array(
                                'conditions' => array('StudentsSection.archive = 0')
                            ),
                            'CourseRegistration' => array(
                                'PublishedCourse' => array(
                                    'Course' => array(
                                        'fields' => array('id', 'course_detail_hours', 'credit', 'course_title', 'course_code')
                                    ),
                                    'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                                ),
                                'fields' => array('id')
                            ),
                            'fields' => array('id', 'full_name', 'department_id', 'program_id', 'program_type_id', 'college_id', 'graduated')
                        )
                    )
                ));
            }
        }

        if (!empty($courseDrops)) {
            foreach ($courseDrops as $pk => &$pv) {
                if (array_key_exists($pv['Student']['StudentsSection'][0]['section_id'], $sections)) {

                    $semesterAc = ClassRegistry::init('CourseRegistration')->getLastestStudentSemesterAndAcademicYear($pv['Student']['id'], $current_academic_year, 1);
                    $pv['Student']['max_load'] = $this->Student->calculateStudentLoad($pv['Student']['id'], $semesterAc['semester'], $semesterAc['academic_year']);

                    if (!empty($pv['Student']['Department']['name'])) {
                        $section_organized_published_course[$pv['Student']['Department']['name']][$pv['Student']['Program']['name']][$pv['Student']['ProgramType']['name']][$sections[$pv['Student']['StudentsSection'][0]['section_id']]][] = $pv;
                    } else {
                        $section_organized_published_course['Pre/Fresh'][$pv['Student']['Program']['name']][$pv['Student']['ProgramType']['name']][$sections[$pv['Student']['StudentsSection'][0]['section_id']]][] = $pv;
                    }
                }
            }
        }

        return  $section_organized_published_course;
    }

    //count_drop_request // registrar=1,department=2
    function count_drop_request($department_ids = null, $registrar = 1, $college_ids = null)
    {
        $options = array();
        if ($registrar == 1) {
            if (!empty($department_ids)) {
                $options['conditions'] = array(
                    'Student.department_id' => $department_ids,
                    'CourseDrop.department_approval = 1',
                    'CourseDrop.registrar_confirmation is null',
                    'Student.graduated = 0',
                );
            }
            if (!empty($college_ids)) {
                $options['conditions'] = array(
                    'Student.department_id is null',
                    'Student.college_id' => $college_ids,
                    'CourseDrop.department_approval = 1',
                    'CourseDrop.registrar_confirmation is null',
                    'Student.graduated = 0',
                );
            }
        } else if ($registrar == 2) {
            $options['conditions'] = array(
                'Student.department_id' => $department_ids,
                'CourseDrop.department_approval is null',
                'Student.graduated = 0',
            );
        } else if ($registrar == 3) {
            if (!empty($college_ids)) {
                $options['conditions'] = array(
                    'Student.department_id is null',
                    'Student.college_id' => $college_ids,
                    'CourseDrop.department_approval = 1',
                    'CourseDrop.registrar_confirmation is null',
                    'Student.graduated = 0',
                );
            }
        }

        $courseDropCount = 0;

        if (!empty($options['conditions'])) {

            $this->Student->bindModel(array('hasMany' => array('StudentsSection')));

            $options['contain'] = array(
                'Student' => array(
                    'StudentsSection' => array(
                        'conditions' => array('StudentsSection.archive = 0')
                    ),
                    'fields' => array('id', 'full_name', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated')
                )
            );

            $options['recursive'] = -1;

            //debug($options);

            $courseDropCount = $this->find('count', $options);
        }

        return  $courseDropCount;
    }


    function droppedCreditSum($student_id)
    {
        $courseDrops = $this->find('all', array(
            'conditions' => array(
                'CourseDrop.student_id' => $student_id,
                'CourseDrop.department_approval = 1',
                'CourseDrop.registrar_confirmation = 1'
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'PublishedCourse' => array('Course')
                )
            )
        ));

        $droppedsum = 0;

        if (!empty($courseDrops)) {
            foreach ($courseDrops as $k => $v) {
                // course taken in different year
                if (isset($v['CourseRegistration']['PublishedCourse']['course_id'])) {
                    $courseTakenHaveGrade = $this->CourseRegistration->PublishedCourse->Course->isCourseTakenHaveRecentPassGrade($student_id, $v['CourseRegistration']['PublishedCourse']['course_id']);
                    if ($courseTakenHaveGrade == false) {
                        $droppedsum += $v['CourseRegistration']['PublishedCourse']['Course']['credit'];
                    }
                }
            }
        }

        //debug($student_id);
        //debug($droppedsum);

        return $droppedsum;
    }
}
