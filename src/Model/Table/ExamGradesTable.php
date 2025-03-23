<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamGradesTable extends Table
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

        $this->setTable('exam_grades');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CourseRegistrations', [
            'className' => 'CourseRegistrations',
            'foreignKey' => 'course_registration_id',
            'propertyName' => 'CourseRegistration',
        ]);
        $this->belongsTo('CourseAdds', [
            'className' => 'CourseAdds',
            'foreignKey' => 'course_add_id',
            'propertyName' => 'CourseAdd',
        ]);
        $this->belongsTo('CourseInstructorAssignments', [
            'className' => 'CourseInstructorAssignments',
            'foreignKey' => 'course_instructor_assignment_id',
            'propertyName' => 'CourseInstructorAssignment',
        ]);
        $this->belongsTo('MakeupExams', [
            'className' => 'MakeupExams',
            'foreignKey' => 'makeup_exam_id',
            'propertyName' => 'MakeupExam',
        ]);
        $this->belongsTo('GradeScales', [
            'className' => 'GradeScales',
            'foreignKey' => 'grade_scale_id',
            'joinType' => 'INNER',
            'propertyName' => 'GradeScale',
        ]);
        $this->hasMany('ExamGradeChanges', [
            'className' => 'ExamGradeChanges',
            'foreignKey' => 'exam_grade_id',
            'propertyName' => 'ExamGradeChange',
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
            ->scalar('grade')
            ->maxLength('grade', 20)
            ->requirePresence('grade', 'create')
            ->notEmptyString('grade');

        $validator
            ->boolean('department_reply')
            ->notEmptyString('department_reply');

        $validator
            ->allowEmptyString('department_approval');

        $validator
            ->scalar('department_reason')
            ->allowEmptyString('department_reason');

        $validator
            ->dateTime('department_approval_date')
            ->allowEmptyDateTime('department_approval_date');

        $validator
            ->scalar('department_approved_by')
            ->maxLength('department_approved_by', 36)
            ->allowEmptyString('department_approved_by');

        $validator
            ->allowEmptyString('registrar_approval');

        $validator
            ->scalar('registrar_reason')
            ->allowEmptyString('registrar_reason');

        $validator
            ->dateTime('registrar_approval_date')
            ->allowEmptyDateTime('registrar_approval_date');

        $validator
            ->scalar('registrar_approved_by')
            ->maxLength('registrar_approved_by', 36)
            ->allowEmptyString('registrar_approved_by');

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

        $rules->add($rules->existsIn(['course_registration_id'], 'CourseRegistrations'));
        $rules->add($rules->existsIn(['course_add_id'], 'CourseAdds'));
        $rules->add($rules->existsIn(['course_instructor_assignment_id'], 'CourseInstructorAssignments'));
        $rules->add($rules->existsIn(['makeup_exam_id'], 'MakeupExams'));
        $rules->add($rules->existsIn(['grade_scale_id'], 'GradeScales'));

        return $rules;
    }


    // Is grade submitted for publishe course
    function is_grade_submitted($published_course_ids = null, $student_lists = array())
    {

        $published_courses_student_registred_score_grade = 0;
        // debug("is_grade_submitted = ".$published_course_ids);

        if (isset($student_lists) && !empty($student_lists)) {
            $grade_submitted_registred_courses = $this->CourseRegistration->find('list', array(
                'conditions' => array(
                    'CourseRegistration.published_course_id' => $published_course_ids,
                    'CourseRegistration.student_id' => $student_lists
                ),
                'fields' => array('CourseRegistration.id')
            ));
        } else {
            $grade_submitted_registred_courses = $this->CourseRegistration->find(
                'list',
                array(
                    'conditions' => array('CourseRegistration.published_course_id' => $published_course_ids),
                    'fields' => array('CourseRegistration.id')
                )
            );
        }


        if (!empty($grade_submitted_registred_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamGrade.course_registration_id' => $grade_submitted_registred_courses))
            );

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        //check course adds
        if (isset($student_lists) && !empty($student_lists)) {
            $grade_submitted_add_courses = $this->CourseAdd->find('list', array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $published_course_ids,
                    'CourseAdd.student_id' => $student_lists,
                    'CourseAdd.department_approval = 1',
                    'CourseAdd.registrar_confirmation = 1'
                ),
                'fields' => array('CourseAdd.id')
            ));
        } else {
            $grade_submitted_add_courses = $this->CourseAdd->find('list', array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $published_course_ids,
                    'CourseAdd.department_approval = 1',
                    'CourseAdd.registrar_confirmation = 1'
                ),
                'fields' => array('CourseAdd.id')
            ));
        }


        if (!empty($grade_submitted_add_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamGrade.course_add_id' => $grade_submitted_add_courses))
            );

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
            /* $published_courses_student_registred_score_grade = ClassRegistry::init('ExamResult')->find('count', array('conditions' => array('ExamResult.course_add_id' => $grade_submitted_add_courses)));
            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            } */
        }

        return $published_courses_student_registred_score_grade;
    }


    // grade submitted for publishe course,  return array

    function getGradeSubmmissionDate($published_course_ids = null)
    {

        $published_courses_student_registred_score_grade = array();

        $grade_submitted_registred_courses = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.published_course_id' => $published_course_ids
            ),
            'fields' => array('CourseRegistration.id')
        ));

        if (!empty($grade_submitted_registred_courses)) {
            $published_courses_student_registred_score_grade = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $grade_submitted_registred_courses
                ),
                'recursive' => -1
            ));

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        //check course adds
        $grade_submitted_add_courses = $this->CourseAdd->find('list', array(
            'conditions' => array(
                'CourseAdd.published_course_id' => $published_course_ids,
                //'CourseAdd.department_approval=1',
                //'CourseAdd.registrar_confirmation=1'
            ),
            'fields' => array('CourseAdd.id')
        ));

        if (!empty($grade_submitted_add_courses)) {
            $published_courses_student_registred_score_grade = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $grade_submitted_add_courses
                ),
                'recursive' => -1
            ));

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }

            $published_courses_student_registred_score_grade = ClassRegistry::init('ExamResult')->find('first', array(
                'conditions' => array(
                    'ExamResult.course_add_id' => $grade_submitted_add_courses
                ),
                'recursive' => -1
            ));

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        return $published_courses_student_registred_score_grade;
    }

    // to allow hard deletion
    // Check grade is submitted for current section of student  given student id and section id return true if grade is submitted and return false if grade has not been submitted and allow hard deletion.

    function isCourseGradeSubmitted($student_id = null, $section_id = null)
    {

        if (isset($section_id)) {
            // get student published courses for the given section
            $sections = $this->CourseRegistration->PublishedCourse->find('all', array(
                'conditions' => array('PublishedCourse.section_id' => $section_id),
                'recursive' => -1
            ));

            $publishedCourse_ids = array();

            if (!empty($sections)) {
                foreach ($sections as $sk => $sv) {
                    $publishedCourse_ids[] = $sv['PublishedCourse']['id'];
                }
            }

            $list_course_registration_ids = array();

            if (!empty($publishedCourse_ids)) {
                //find course regisration number
                if (!empty($student_id)) {
                    /* $courseRegistration = $this->CourseRegistration->find('all', array(
                        'conditions' => array(
                            'CourseRegistration.student_id' => $student_id,
                            'CourseRegistration.published_course_id' => $publishedCourse_ids
                        ),
                        'fields' => array('id', 'student_id'),
                        'recursive' => -1
                    ));

                    if (!empty($courseRegistration)) {
                        foreach ($courseRegistration as $crk => $crv) {
                            $list_course_registration_ids[] = $crv['CourseRegistration']['id'];
                        }
                    } */

                    $list_course_registration_ids = $this->CourseRegistration->find('list', array(
                        'conditions' => array(
                            'CourseRegistration.student_id' => $student_id,
                            'CourseRegistration.published_course_id' => $publishedCourse_ids
                        ),
                        'fields' => array('CourseRegistration.id')
                    ));
                } else {
                    $list_course_registration_ids = $this->CourseRegistration->find('list', array(
                        'conditions' => array(
                            'CourseRegistration.published_course_id' => $publishedCourse_ids
                        ),
                        'fields' => array('CourseRegistration.id')
                    ));
                }

                if (!empty($list_course_registration_ids)) {
                    // ExamResults should be checked first than ExamGrades, If assesment is started, we can assume grade is beig submitted. Neway.

                    $gradeSubmitted = $this->find(
                        'count',
                        array(
                            'conditions' => array('ExamGrade.course_registration_id' => $list_course_registration_ids),
                            'recursive' => -1
                        )
                    );

                    if ($gradeSubmitted > 0) {
                        return true;
                    }
                    /* $examResultFilled = ClassRegistry::init('ExamResult')->find('count', array('conditions' => array('ExamResult.course_registration_id' => $list_course_registration_ids), 'recursive' => -1));

                    if ($examResultFilled > 0) {
                        return true;
                    } */
                }

                $list_course_add_ids = array();

                //check course adds
                if (!empty($student_id)) {
                    $list_course_add_ids = $this->CourseAdd->find('list', array(
                        'conditions' => array(
                            'CourseAdd.student_id' => $student_id,
                            'CourseAdd.published_course_id' => $publishedCourse_ids,
                            'CourseAdd.department_approval = 1',
                            'CourseAdd.registrar_confirmation = 1'
                        ),
                        'fields' => array('CourseAdd.id')
                    ));
                } else {
                    $list_course_add_ids = $this->CourseAdd->find('list', array(
                        'conditions' => array(
                            'CourseAdd.published_course_id' => $publishedCourse_ids,
                            'CourseAdd.department_approval = 1',
                            'CourseAdd.registrar_confirmation = 1'
                        ),
                        'fields' => array('CourseAdd.id')
                    ));
                }

                if (!empty($list_course_add_ids)) {
                    // ExamResults should be checked first than ExamGrades, If assesment is started, we can assume grade is beig submitted. Neway.

                    $published_courses_student_added_score_grade = $this->find(
                        'count',
                        array(
                            'conditions' => array('ExamGrade.course_add_id' => $list_course_add_ids),
                            'recursive' => -1
                        )
                    );

                    if ($published_courses_student_added_score_grade > 0) {
                        return true;
                    }
                    /* $published_courses_student_added_score_grade = ClassRegistry::init('ExamResult')->find('count', array('conditions' => array('ExamResult.course_add_id' => $list_course_add_ids), 'recursive' => -1));

                    if ($published_courses_student_added_score_grade > 0) {
                        return true;
                    } */
                }
            }
            return false;
        }
        return false;
    }

    // To be implemented
    function isEverGradeSubmitInTheNameOfSection($section_id = null)
    {

        if (isset($section_id)) {
            // get student published courses for the given section
            $sections = $this->CourseRegistration->PublishedCourse->find(
                'all',
                array('conditions' => array('PublishedCourse.section_id' => $section_id), 'recursive' => -1)
            );

            $gradeSubmitted = 0;
            $publishedCourse_ids = array();

            $list_course_registration_ids = array();
            $grade_submitted_add_courses = array();

            if (!empty($sections)) {
                foreach ($sections as $sk => $sv) {
                    $publishedCourse_ids[] = $sv['PublishedCourse']['id'];
                }
            }

            if (!empty($publishedCourse_ids)) {
                //find course regisration id
                $list_course_registration_ids = $this->CourseRegistration->find('list', array(
                    'conditions' => array(
                        'CourseRegistration.published_course_id' => $publishedCourse_ids
                    ),
                    'fields' => array('CourseRegistration.id')
                ));

                //find course add id
                $grade_submitted_add_courses = $this->CourseAdd->find('list', array(
                    'conditions' => array(
                        'CourseAdd.published_course_id' => $publishedCourse_ids,
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation = 1'
                    ),
                    'fields' => array('CourseAdd.id')
                ));
            }

            //find grade submitted

            if (!empty($list_course_registration_ids)) {
                $gradeSubmitted = $this->find(
                    'count',
                    array(
                        'conditions' => array('ExamGrade.course_registration_id' => $list_course_registration_ids),
                        'recursive' => -1
                    )
                );

                if ($gradeSubmitted > 0) {
                    return true;
                }

                $gradeSubmitted = ClassRegistry::init('ExamResult')->find(
                    'count',
                    array(
                        'conditions' => array('ExamResult.course_registration_id' => $list_course_registration_ids),
                        'recursive' => -1
                    )
                );

                if ($gradeSubmitted > 0) {
                    return true;
                }
            }

            if (!empty($grade_submitted_add_courses)) {
                $published_courses_student_registred_score_grade = $this->find(
                    'count',
                    array(
                        'conditions' => array('ExamGrade.course_add_id' => $grade_submitted_add_courses),
                        'recursive' => -1
                    )
                );

                if ($published_courses_student_registred_score_grade > 0) {
                    return true;
                }

                $published_courses_student_registred_score_grade = ClassRegistry::init('ExamResult')->find(
                    'count',
                    array(
                        'conditions' => array('ExamResult.course_add_id' => $grade_submitted_add_courses),
                        'recursive' => -1
                    )
                );

                if ($published_courses_student_registred_score_grade > 0) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    function gradeCanBeChanged($grade_id = null)
    {

        $grade_history = $this->find('first', array(
            'conditions' => array(
                'ExamGrade.id' => $grade_id
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'PublishedCourse' => array(
                        'Course' => array('id', 'course_code_title'),
                        'fields' => array('id', 'course_id')
                    ),
                    'Student' => array('id', 'full_name_studentnumber', 'graduated'),
                    'fields' => array('id', 'student_id', 'published_course_id')
                ),
                'CourseAdd' => array(
                    'PublishedCourse' => array(
                        'Course' => array('id', 'course_code_title'),
                        'fields' => array('id', 'course_id')
                    ),
                    'Student' => array('id', 'full_name_studentnumber', 'graduated'),
                    'fields' => array('id', 'student_id', 'published_course_id')
                ),
                'MakeupExam' => array(
                    'PublishedCourse' => array(
                        'Course' => array('id', 'course_code_title'),
                        'fields' => array('id', 'course_id')
                    ),
                    'Student' => array('id', 'full_name_studentnumber', 'graduated'),
                    'fields' => array('id', 'student_id', 'published_course_id')
                ),
                'ExamGradeChange'
            )
        ));

        debug($grade_history);

        if (!empty($grade_history['ExamGrade'])) {
            $course_title_course_code = '';
            $full_name_studentnumber = '';
            $most_recent_grade = '';

            if (!empty($grade_history['CourseRegistration']['id'])) {
                if (!empty($grade_history['CourseRegistration']['Student']['id'])) {
                    $full_name_studentnumber = $grade_history['CourseRegistration']['Student']['full_name_studentnumber'];
                }
                if (!empty($grade_history['CourseRegistration']['PublishedCourse']['id']) && !empty($grade_history['CourseRegistration']['PublishedCourse']['Course']['id'])) {
                    $course_title_course_code = $grade_history['CourseRegistration']['PublishedCourse']['Course']['course_code_title'];
                }
            } elseif (!empty($grade_history['CourseAdd']['id'])) {
                if (!empty($grade_history['CourseAdd']['Student']['id'])) {
                    $full_name_studentnumber = $grade_history['CourseAdd']['Student']['full_name_studentnumber'];
                }
                if (!empty($grade_history['CourseAdd']['PublishedCourse']['id']) && !empty($grade_history['CourseAdd']['PublishedCourse']['Course']['id'])) {
                    $course_title_course_code = $grade_history['CourseAdd']['PublishedCourse']['Course']['course_code_title'];
                }
            } elseif (!empty($grade_history['MakeupExam']['id'])) {
                if (!empty($grade_history['MakeupExam']['Student']['id'])) {
                    $full_name_studentnumber = $grade_history['MakeupExam']['Student']['full_name_studentnumber'];
                }
                if (!empty($grade_history['MakeupExam']['PublishedCourse']['id']) && !empty($grade_history['MakeupExam']['PublishedCourse']['Course']['id'])) {
                    $course_title_course_code = $grade_history['MakeupExam']['PublishedCourse']['Course']['course_code_title'];
                }
            }

            if ($grade_history['ExamGrade']) {
                $most_recent_grade = $grade_history['ExamGrade']['grade'];
                if ($grade_history['ExamGrade']['department_approval'] == null) {
                    return "There is already a submitted grade " . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is waiting for department approval. Please first finalize the approval process for the already submitted grade before recording a new grade.";
                } elseif ($grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == null) {
                    return "There is already a submitted grade" . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is waiting for registrar approval. Please first let the registrar finalize the approval process for the already submitted grade before recording a new grade.";
                }
            }

            if (isset($grade_history['ExamGradeChange']) && !empty($grade_history['ExamGradeChange'])) {
                foreach ($grade_history['ExamGradeChange'] as $key => $examGradeChange) {
                    //Is it waiting department approval?
                    $most_recent_grade = $examGradeChange['grade'];
                    if ($examGradeChange['manual_ng_conversion'] != 1 && $examGradeChange['auto_ng_conversion'] != 1) {
                        if ($examGradeChange['initiated_by_department'] != 1 && $examGradeChange['department_approval'] == null) {
                            if ($examGradeChange['makeup_exam_result'] == null) {
                                return "There is already a submitted grade change request " . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is awaiting department approval. Please first finalize the approval process for the already submitted grade change request before recording a new grade.";
                            } else {
                                return "There is already a submitted makeup exam grade " . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is awaiting department approval. Please first finalize the approval process for the already submitted makeup exam grade before recording a new grade.";
                            }
                        } elseif (($examGradeChange['initiated_by_department'] == 1 && $examGradeChange['department_approval'] != -1 && $examGradeChange['registrar_approval'] != -1) || ($examGradeChange['initiated_by_department'] != 1 && $examGradeChange['department_approval'] == 1)) {
                            if ($examGradeChange['initiated_by_department'] == 1 && $examGradeChange['makeup_exam_result'] != null) {
                                return "There is already a submitted supplementary exam grade " . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is awaiting registrar approval. Please first let the registrar finalize the approval process for the already submitted supplementary exam grade before recording a new grade.";
                            } elseif ($examGradeChange['makeup_exam_result'] == null && $examGradeChange['college_approval'] == null) {
                                return "There is already a submitted grade " . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is awaiting college approval. Please first let the college finalize the approval process for the already submitted grade before recording a new grade.";
                            } elseif (($examGradeChange['makeup_exam_result'] != null || $examGradeChange['college_approval'] == 1) && $examGradeChange['registrar_approval'] == null) {
                                return "There is already a submitted grade " . (!empty($most_recent_grade) ? '(' . $most_recent_grade . ')' : '') . ' for ' . ((!empty($full_name_studentnumber) ? $full_name_studentnumber : 'the selected student') . (!empty($course_title_course_code) ? ' for ' . $course_title_course_code : ' and course')) . " which is awaiting registrar approval. Please first let the registrar finalize the approval process for the already submitted grade before recording a new grade.";
                            }
                        }
                    }
                } //Auto and manual grade change removal
            }
        }

        return true;
    }

    function getStudentCoursesAndFinalGrade($student_id, $acadamic_year, $semester, $include_exempted = 0)
    {

        $courses_and_grades = array();

        $course_registered = $this->CourseRegistration->find('all', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.academic_year' => $acadamic_year,
                'CourseRegistration.semester' => $semester,
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            ),
            'order' => array(
                'CourseRegistration.academic_year' => 'ASC',
                'CourseRegistration.semester' => 'ASC',
                'CourseRegistration.id' => 'ASC'
            )
        ));

        $course_added = $this->CourseAdd->find('all', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.academic_year' => $acadamic_year,
                'CourseAdd.semester' => $semester
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            ),
            'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
        ));

        if (isset($course_added) && !empty($course_added)) {
            foreach ($course_added as $ca_key => $ca_value) {
                if (!($ca_value['PublishedCourse']['add'] == 1 || ($ca_value['CourseAdd']['department_approval'] == 1 && $ca_value['CourseAdd']['registrar_confirmation'] == 1))) {
                    unset($course_added[$ca_key]);
                }
            }
        }

        $student_detail = $this->CourseAdd->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );

        $exempted_courses = array();

        $student_level = $this->CourseAdd->Student->StudentExamStatus->studentYearAndSemesterLevelOfStatus(
            $student_id,
            $acadamic_year,
            $semester
        );
        //debug($student_level);

        if ($student_level['year'] == 1) {
            $student_year_level = '1st';
        } elseif ($student_level['year'] == 2) {
            $student_year_level = '2nd';
        } elseif ($student_level['year'] == 3) {
            $student_year_level = '3rd';
        } else {
            if (isset($student_level['year'])) {
                $student_year_level = $student_level['year'] . 'th';
            } else {
                $student_year_level = $student_level['year'] . 'th';
            }
        }

        $year_level_id = ClassRegistry::init('YearLevel')->field('id', array(
            'YearLevel.department_id' => $student_detail['Student']['department_id'],
            'YearLevel.name' => $student_year_level,
        ));

        if (!empty($student_detail['Student']['curriculum_id'])) {
            $courses_to_be_given = $this->CourseAdd->PublishedCourse->Course->find('all', array(
                'conditions' => array(
                    'Course.curriculum_id' => $student_detail['Student']['curriculum_id'],
                    'Course.year_level_id' => $year_level_id,
                    'Course.semester' => $semester
                ),
                'recursive' => -1
            ));

            if ($include_exempted == 1) {
                $all_exempted_courses = $this->CourseAdd->Student->CourseExemption->find('all', array(
                    'conditions' => array(
                        'CourseExemption.student_id' => $student_detail['Student']['id'],
                        'CourseExemption.department_accept_reject' => 1,
                        'CourseExemption.registrar_confirm_deny' => 1,
                    ),
                    'recursive' => -1
                ));

                if (isset($all_exempted_courses) && !empty($all_exempted_courses)) {
                    foreach ($all_exempted_courses as $ex_key => $exempted_course) {
                        foreach ($courses_to_be_given as $c_key => $course_to_be_given) {
                            if ($course_to_be_given['Course']['id'] == $exempted_course['CourseExemption']['course_id']) {
                                $index = count($courses_and_grades);
                                $courses_and_grades[$index]['course_title'] = trim(
                                    $course_to_be_given['Course']['course_title']
                                );
                                $courses_and_grades[$index]['course_code'] = trim(
                                    $course_to_be_given['Course']['course_code']
                                );
                                $courses_and_grades[$index]['course_id'] = $course_to_be_given['Course']['id'];
                                $courses_and_grades[$index]['major'] = $course_to_be_given['Course']['major'];
                                $courses_and_grades[$index]['credit'] = $course_to_be_given['Course']['credit'];
                                $courses_and_grades[$index]['thesis'] = $course_to_be_given['Course']['thesis'];
                                $courses_and_grades[$index]['elective'] = $course_to_be_given['Course']['elective'];

                                if (isset($exempted_course['CourseExemption']['grade']) && !empty($exempted_course['CourseExemption']['grade'])) {
                                    $courses_and_grades[$index]['grade'] = $exempted_course['CourseExemption']['grade'] . '(EX)';
                                } else {
                                    $courses_and_grades[$index]['grade'] = 'EX';
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($course_registered)) {
            foreach ($course_registered as $key => $value) {
                if (!$this->CourseRegistration->isCourseDroped(
                        $value['CourseRegistration']['id']
                    ) && isset($value['PublishedCourse']['Course']['id'])) {
                    $index = count($courses_and_grades);
                    $courses_and_grades[$index]['course_title'] = trim(
                        $value['PublishedCourse']['Course']['course_title']
                    );
                    $courses_and_grades[$index]['course_code'] = trim(
                        $value['PublishedCourse']['Course']['course_code']
                    );
                    $courses_and_grades[$index]['course_id'] = $value['PublishedCourse']['Course']['id'];

                    if ($student_detail['Student']['curriculum_id'] != $value['PublishedCourse']['Course']['curriculum_id']) {
                        $coursemajor = ClassRegistry::init('EquivalentCourse')->isEquivalentCourseMajor(
                            $value['PublishedCourse']['course_id'],
                            $student_detail['Student']['curriculum_id']
                        );
                        $courses_and_grades[$index]['major'] = $coursemajor;
                    } else {
                        $courses_and_grades[$index]['major'] = $value['PublishedCourse']['Course']['major'];
                    }

                    //$courses_and_grades[$index]['major'] = $value['PublishedCourse']['Course']['major'];
                    $courses_and_grades[$index]['credit'] = $value['PublishedCourse']['Course']['credit'];
                    $courses_and_grades[$index]['thesis'] = $value['PublishedCourse']['Course']['thesis'];
                    $courses_and_grades[$index]['elective'] = $value['PublishedCourse']['Course']['elective'];


                    $grade_detail = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                    //debug($grade_detail);
                    //debug($courses_and_grades);
                    if (!empty($grade_detail)) {
                        $courses_and_grades[$index]['grade'] = $grade_detail['grade'];
                        if (isset($grade_detail['point_value'])) {
                            $courses_and_grades[$index]['point_value'] = $grade_detail['point_value'];
                            $courses_and_grades[$index]['pass_grade'] = $grade_detail['pass_grade'];
                            $courses_and_grades[$index]['used_in_gpa'] = $grade_detail['used_in_gpa'];
                        } else {
                            $gradeTypeDetailsPF = ClassRegistry::init('Grade')->find('first', array(
                                'conditions' => array('Grade.id' => $grade_detail['grade_id']),
                                'contain' => array('GradeType')
                            ));
                            debug($gradeTypeDetailsPF);
                            if (isset($gradeTypeDetailsPF['GradeType']) && !empty($gradeTypeDetailsPF['GradeType'])) {
                                if ($gradeTypeDetailsPF['GradeType']['used_in_gpa'] == false) {
                                    $courses_and_grades[$index]['pass_fail_grade'] = true;
                                    //  break;
                                }
                            }
                        }
                    }

                    //To determine if a student registered more than once for the same course
                    $matching_courses = array();
                    $cID = $value['PublishedCourse']['Course']['id'];

                    if (!empty($student_detail['Student']['curriculum_id'])) {
                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                            $cID,
                            $student_detail['Student']['curriculum_id']
                        );
                    }

                    $matching_courses[$cID] = $cID;
                    //debug($matching_courses);

                    $register_and_add_freq = $this->getCourseFrequenceRegAdds($matching_courses, $student_id);

                    //If the student add or register once
                    if (count($register_and_add_freq) <= 1) {
                        $courses_and_grades[$index]['repeated_old'] = false;
                        $courses_and_grades[$index]['repeated_new'] = false;
                    } else {
                        //If the student has multiple registration and/or add
                        $rept = $this->repeatationLabeling(
                            $register_and_add_freq,
                            'register',
                            $value['CourseRegistration']['id'],
                            $student_detail,
                            $courses_and_grades[$index]['course_id']
                        );
                        $courses_and_grades[$index]['repeated_old'] = $rept['repeated_old'];
                        $courses_and_grades[$index]['repeated_new'] = $rept['repeated_new'];
                        /* if ($value['CourseRegistration']['id'] == 681549) {
                            debug($rept);
                        } */
                    }
                }
            }
        }

        if (!empty($course_added)) {
            foreach ($course_added as $key => $value) {
                $index = count($courses_and_grades);
                $courses_and_grades[$index]['course_title'] = trim($value['PublishedCourse']['Course']['course_title']);
                $courses_and_grades[$index]['course_code'] = trim($value['PublishedCourse']['Course']['course_code']);
                $courses_and_grades[$index]['course_id'] = $value['PublishedCourse']['Course']['id'];

                if ($student_detail['Student']['curriculum_id'] != $value['PublishedCourse']['Course']['curriculum_id']) {
                    $coursemajor = ClassRegistry::init('EquivalentCourse')->isEquivalentCourseMajor(
                        $value['PublishedCourse']['course_id'],
                        $student_detail['Student']['curriculum_id']
                    );
                    $courses_and_grades[$index]['major'] = $coursemajor;
                } else {
                    $courses_and_grades[$index]['major'] = $value['PublishedCourse']['Course']['major'];
                }

                $courses_and_grades[$index]['credit'] = $value['PublishedCourse']['Course']['credit'];
                $courses_and_grades[$index]['thesis'] = $value['PublishedCourse']['Course']['thesis'];
                $courses_and_grades[$index]['elective'] = $value['PublishedCourse']['Course']['elective'];

                $grade_detail = $this->getApprovedGrade($value['CourseAdd']['id'], 0);

                if (!empty($grade_detail)) {
                    $courses_and_grades[$index]['grade'] = $grade_detail['grade'];
                    if (isset($grade_detail['point_value'])) {
                        $courses_and_grades[$index]['point_value'] = $grade_detail['point_value'];
                        $courses_and_grades[$index]['pass_grade'] = $grade_detail['pass_grade'];
                        $courses_and_grades[$index]['used_in_gpa'] = $grade_detail['used_in_gpa'];
                    }
                }

                //To determine if a student registered more than once for the same course
                $matching_courses = array();
                $cID = $value['PublishedCourse']['Course']['id'];

                if (!empty($student_detail['Student']['curriculum_id'])) {
                    $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                        $cID,
                        $student_detail['Student']['curriculum_id']
                    );
                }

                $matching_courses[$cID] = $cID;
                debug($matching_courses);

                $register_and_add_freq = $this->getCourseFrequenceRegAdds($matching_courses, $student_id);

                //If the student add or register once
                if (count($register_and_add_freq) <= 1) {
                    $courses_and_grades[$index]['repeated_old'] = false;
                    $courses_and_grades[$index]['repeated_new'] = false;
                } else {
                    //If the student has multiple registration and/or add
                    //debug($value['CourseAdd']['id']);
                    //debug($this->repeatationLabeling($register_and_add_freq, 'add', $value['CourseAdd']['id'], $student_detail, $courses_and_grades[$index]['course_id']));

                    $rept = $this->repeatationLabeling(
                        $register_and_add_freq,
                        'add',
                        $value['CourseAdd']['id'],
                        $student_detail,
                        $courses_and_grades[$index]['course_id']
                    );
                    $courses_and_grades[$index]['repeated_old'] = $rept['repeated_old'];
                    $courses_and_grades[$index]['repeated_new'] = $rept['repeated_new'];
                }
            }
        }

        // debug($courses_and_grades);
        return $courses_and_grades;
    }

    function getApprovedGrade($register_add_id = null, $registration = 1)
    {

        //debug($register_add_id);
        $approved_grade_detail = array();

        if ($registration == 1) {
            $grade_detail = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $register_add_id,
                    'ExamGrade.department_approval' => 1,
                    'ExamGrade.registrar_approval' => 1,
                ),
                //'order' => array('ExamGrade.created' => 'DESC'), // Back Dated grade affects this, Neway
                'order' => array('ExamGrade.id' => 'DESC'),
                'recursive' => -1
            ));
            //debug($grade_detail);
        } else {
            $grade_detail = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $register_add_id,
                    'ExamGrade.department_approval' => 1,
                    'ExamGrade.registrar_approval' => 1,
                ),
                //'order' => array('ExamGrade.created' => 'DESC'), // Back Dated grade affects this, Neway
                'order' => array('ExamGrade.id' => 'DESC'),
                'recursive' => -1
            ));
            //debug($grade_detail);
        }

        if (!empty($grade_detail)) {
            $grade_change = $this->ExamGradeChange->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.exam_grade_id' => $grade_detail['ExamGrade']['id'],
                    'OR' => array(
                        array(
                            'ExamGradeChange.department_approval' => 1,
                            'ExamGradeChange.registrar_approval' => 1,
                            array(
                                'OR' => array(
                                    array(
                                        //If it is grade change
                                        'ExamGradeChange.makeup_exam_result IS NULL',
                                        'ExamGradeChange.college_approval' => 1,
                                    ),
                                    array(
                                        //Makeup exam
                                        'ExamGradeChange.makeup_exam_result IS NOT NULL',
                                    )
                                )
                            )
                        ),
                        'ExamGradeChange.manual_ng_conversion = 1',
                        'ExamGradeChange.auto_ng_conversion = 1'
                    )
                ),
                'recursive' => -1,
                //'order' => array('ExamGradeChange.created' => 'DESC')
                'order' => array('ExamGradeChange.id' => 'DESC')
            ));

            if (!empty($grade_change)) {
                $approved_grade_detail['grade'] = $grade_change['ExamGradeChange']['grade'];
                $approved_grade_detail['gradeChangeRequested'] = $grade_change['ExamGradeChange']['created'];
                $approved_grade_detail['gradeChangeApproved'] = $grade_change['ExamGradeChange']['modified'];
                $approved_grade_detail['gradeChangeReason'] = $grade_change['ExamGradeChange']['reason'];
                $approved_grade_detail['gradeChangeResult'] = $grade_change['ExamGradeChange']['result'];
                $approved_grade_detail['makeupExamResult'] = $grade_change['ExamGradeChange']['makeup_exam_result'];
                $approved_grade_detail['manualNGConversion'] = $grade_change['ExamGradeChange']['manual_ng_conversion'];
                $approved_grade_detail['autoNGConversion'] = $grade_change['ExamGradeChange']['auto_ng_conversion'];
                $approved_grade_detail['grade_change_id'] = $grade_change['ExamGradeChange']['id'];

                if ($grade_change['ExamGradeChange']['manual_ng_conversion']) {
                    $approved_grade_detail['manualNGConvertedBy'] = ClassRegistry::init('User')->field(
                        'full_name',
                        array('User.id' => $grade_change['ExamGradeChange']['manual_ng_converted_by'])
                    );
                }
            } else {
                $approved_grade_detail['grade'] = $grade_detail['ExamGrade']['grade'];
                $approved_grade_detail['approved'] = $grade_detail['ExamGrade']['modified'];
            }

            $approved_grade_detail['grade_id'] = $grade_detail['ExamGrade']['id'];
            $approved_grade_detail['submitted'] = $grade_detail['ExamGrade']['created'];

            $approved_grade_detail['backdatedGradeEntry'] = ((strpos(
                    $grade_detail['ExamGrade']['registrar_reason'],
                    'backend'
                ) !== false) || $grade_detail['ExamGrade']['registrar_reason'] == 'Via backend data entry interface' ? 1 : 0);
            $approved_grade_detail['registrarGradeEntry'] = ((strpos(
                    $grade_detail['ExamGrade']['registrar_reason'],
                    'Data Entry'
                ) !== false) && $grade_detail['ExamGrade']['registrar_reason'] == 'Registrar Data Entry interface' ? 1 : 0);

            if ($approved_grade_detail['backdatedGradeEntry']) {
                $approved_grade_detail['approved'] = $grade_detail['ExamGrade']['modified'];
            }

            $grade_related_detail = $this->GradeScale->find('first', array(
                'conditions' => array(
                    'GradeScale.id' => $grade_detail['ExamGrade']['grade_scale_id']
                ),
                'contain' => array(
                    'GradeScaleDetail' => array(
                        'Grade' => array(
                            'conditions' => array(
                                'Grade.grade' => $approved_grade_detail['grade']
                            ),
                            'GradeType'
                        )
                    )
                )
            ));

            if (isset($grade_related_detail['GradeScaleDetail']) && !empty($grade_related_detail['GradeScaleDetail'])) {
                foreach ($grade_related_detail['GradeScaleDetail'] as $key => $value) {
                    //debug($value);
                    if (!empty($value['Grade'])) {
                        $approved_grade_detail['point_value'] = $value['Grade']['point_value'];
                        $approved_grade_detail['pass_grade'] = $value['Grade']['pass_grade'];
                        $approved_grade_detail['used_in_gpa'] = $value['Grade']['GradeType']['used_in_gpa'];
                        $approved_grade_detail['grade_scale_id'] = $value['grade_scale_id'];
                        $approved_grade_detail['grade_type'] = $value['Grade']['GradeType']['type'];
                        $approved_grade_detail['grade_type_id'] = $value['Grade']['GradeType']['id'];
                        $approved_grade_detail['repeatable'] = $value['Grade']['allow_repetition'];
                        $approved_grade_detail['grade_scale'] = $grade_related_detail['GradeScale']['name'];
                        break;
                    } else {
                        /*
                        $gradeTypeDetailsPF = ClassRegistry::init('Grade')->find('first',array('conditions'=>array('Grade.id'=>$value['grade_id']), 'contain'=>array('GradeType')));
                        debug($gradeTypeDetailsPF);
                        if (isset($gradeTypeDetailsPF['GradeType']) && !empty($gradeTypeDetailsPF['GradeType'])) {
                            if ($gradeTypeDetailsPF['GradeType']['used_in_gpa']==false) {
                                $approved_grade_detail['pass_fail_grade'] = true;
                                break;
                            }
                        }
                        */
                    }
                }
            }

            if (isset($approved_grade_detail['grade']) && (strcasecmp(
                        $approved_grade_detail['grade'],
                        'NG'
                    ) == 0 || strcasecmp($approved_grade_detail['grade'], 'DO') == 0 || strcasecmp(
                        $approved_grade_detail['grade'],
                        'I'
                    ) == 0 || strcasecmp($approved_grade_detail['grade'], 'W') == 0)) {
                $approved_grade_detail['pass_grade'] = 0;
            }

            //debug($approved_grade_detail);
            return $approved_grade_detail;
        } else {
            return array();
        }
    }

    function getApprovedNotChangedGrade($register_add_id = null, $registration = 1)
    {

        $approved_grade_detail = array();

        if ($registration == 1) {
            $grade_detail = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $register_add_id,
                    'ExamGrade.department_approval' => 1,
                    'ExamGrade.registrar_approval' => 1,
                ),
                'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
                'recursive' => -1
            ));
        } else {
            $grade_detail = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $register_add_id,
                    'ExamGrade.department_approval' => 1,
                    'ExamGrade.registrar_approval' => 1,
                ),
                'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
                'recursive' => -1
            ));
        }

        if (!empty($grade_detail)) {
            $approved_grade_detail['grade'] = $grade_detail['ExamGrade']['grade'];
            $approved_grade_detail['grade_id'] = $grade_detail['ExamGrade']['id'];
            $approved_grade_detail['exam_grade_grade_scale_id'] = $grade_detail['ExamGrade']['grade_scale_id'];

            $grade_related_detail = $this->GradeScale->find('first', array(
                'conditions' => array(
                    'GradeScale.id' => $grade_detail['ExamGrade']['grade_scale_id']
                ),
                'contain' => array(
                    'GradeScaleDetail' => array(
                        'Grade' => array(
                            'conditions' => array(
                                'Grade.grade' => $approved_grade_detail['grade']
                            ),
                            'GradeType'
                        )
                    )
                )
            ));

            if (isset($grade_related_detail['GradeScaleDetail']) && !empty($grade_related_detail['GradeScaleDetail'])) {
                foreach ($grade_related_detail['GradeScaleDetail'] as $key => $value) {
                    //debug($value);
                    if (!empty($value['Grade'])) {
                        //debug($value['Grade']);
                        $approved_grade_detail['point_value'] = (isset($value['Grade']['point_value']) ? $value['Grade']['point_value'] : 0);
                        $approved_grade_detail['pass_grade'] = (isset($value['Grade']['pass_grade']) ? $value['Grade']['pass_grade'] : false);
                        $approved_grade_detail['used_in_gpa'] = (isset($value['Grade']['GradeType']['used_in_gpa']) ? $value['Grade']['GradeType']['used_in_gpa'] : false);
                        $approved_grade_detail['allow_repetition'] = (isset($value['Grade']['allow_repetition']) ? $value['Grade']['allow_repetition'] : false);
                        break;
                    }
                }
            }
            return $approved_grade_detail;
        } else {
            return array();
        }
    }

    function getApprovedGradeForMakeUpExam($register_add_id = null, $registration = 1)
    {

        $approved_grade_detail = array();

        $possibleAllowedRepetitionGrade[PROGRAM_UNDEGRADUATE]['C-'] = 'C-';
        $possibleAllowedRepetitionGrade[PROGRAM_UNDEGRADUATE]['D'] = 'D';
        $possibleAllowedRepetitionGrade[PROGRAM_UNDEGRADUATE]['F'] = 'F';
        $possibleAllowedRepetitionGrade[PROGRAM_UNDEGRADUATE]['I'] = 'I';
        $possibleAllowedRepetitionGrade[PROGRAM_UNDEGRADUATE]['NG'] = 'NG';
        $possibleAllowedRepetitionGrade[PROGRAM_UNDEGRADUATE]['Fx'] = 'Fx';

        $possibleAllowedRepetitionGrade[PROGRAM_POST_GRADUATE]['C'] = 'C';
        $possibleAllowedRepetitionGrade[PROGRAM_POST_GRADUATE]['C+'] = 'C+';
        $possibleAllowedRepetitionGrade[PROGRAM_POST_GRADUATE]['D'] = 'D';
        $possibleAllowedRepetitionGrade[PROGRAM_POST_GRADUATE]['F'] = 'F';
        $possibleAllowedRepetitionGrade[PROGRAM_POST_GRADUATE]['I'] = 'I';
        $possibleAllowedRepetitionGrade[PROGRAM_POST_GRADUATE]['NG'] = 'NG';

        if ($registration == 1) {
            $grade_details = $this->find('all', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $register_add_id,
                    'ExamGrade.course_registration_id is not null',
                    'ExamGrade.department_approval' => 1,
                    'ExamGrade.registrar_approval' => 1,
                ),
                'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
                'recursive' => -1
            ));
        } else {
            $grade_details = $this->find('all', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $register_add_id,
                    'ExamGrade.course_registration_id is not null',
                    'ExamGrade.department_approval' => 1,
                    'ExamGrade.registrar_approval' => 1,
                ),
                'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
                'recursive' => -1
            ));
        }

        //debug($grade_details[0]);

        if (!empty($grade_details)) {
            foreach ($grade_details as $grade_detail_id => $grade_detail) {
                $approved_grade_detail['grade'] = $grade_detail['ExamGrade']['grade'];
                $approved_grade_detail['grade_id'] = $grade_detail['ExamGrade']['id'];
                $approved_grade_detail['exam_grade_grade_scale_id'] = (!empty($grade_detail['ExamGrade']['grade_scale_id']) ? $grade_detail['ExamGrade']['grade_scale_id'] : 0);

                $grade_related_detail = $this->GradeScale->find('first', array(
                    'conditions' => array(
                        'GradeScale.id' => $grade_detail['ExamGrade']['grade_scale_id']
                    ),
                    'contain' => array(
                        'GradeScaleDetail' => array(
                            'Grade' => array(
                                'conditions' => array(
                                    'Grade.grade' => $approved_grade_detail['grade']
                                ),
                                'GradeType'
                            )
                        )
                    )
                ));

                if (isset($grade_related_detail['GradeScaleDetail']) && !empty($grade_related_detail['GradeScaleDetail'])) {
                    //debug($grade_related_detail);
                    foreach ($grade_related_detail['GradeScaleDetail'] as $key => $value) {
                        //debug($value);
                        if (!empty($value['Grade']) && ((isset($value['Grade']['allow_repetition']) && $value['Grade']['allow_repetition']) || (!empty($possibleAllowedRepetitionGrade) && in_array(
                                        $value['Grade']['grade'],
                                        $possibleAllowedRepetitionGrade[$grade_related_detail['GradeScale']['program_id']]
                                    )))) {
                            $approved_grade_detail['point_value'] = (isset($value['Grade']['point_value']) ? $value['Grade']['point_value'] : 0);
                            $approved_grade_detail['pass_grade'] = (isset($value['Grade']['pass_grade']) ? $value['Grade']['pass_grade'] : false);
                            $approved_grade_detail['used_in_gpa'] = (isset($value['Grade']['GradeType']['used_in_gpa']) ? $value['Grade']['GradeType']['used_in_gpa'] : false);
                            $approved_grade_detail['allow_repetition'] = (isset($value['Grade']['allow_repetition']) ? $value['Grade']['allow_repetition'] : false);
                            return $approved_grade_detail;
                        }
                    }
                }
            }
            return $approved_grade_detail;
        } else {
            return array();
        }
    }

    function getGradeForStats($register_add_id = null, $registration = 1)
    {

        //debug($register_add_id);
        $approved_grade_detail = array();

        if ($registration == 1) {
            $grade_detail = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $register_add_id,
                ),
                'order' => array('ExamGrade.created DESC'),
                'recursive' => -1
            ));
        } else {
            $grade_detail = $this->find('first', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $register_add_id,
                ),
                'order' => array('ExamGrade.created DESC'),
                'recursive' => -1
            ));
            //debug($grade_detail);
        }

        if (!empty($grade_detail)) {
            $grade_change = $this->ExamGradeChange->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.exam_grade_id' => $grade_detail['ExamGrade']['id'],
                    'OR' => array(
                        array(
                            'OR' => array(
                                array(
                                    //If it is grade change
                                    'ExamGradeChange.makeup_exam_result IS NULL',
                                    'ExamGradeChange.college_approval' => 1,
                                ),
                                array(
                                    //Makeup exam
                                    'ExamGradeChange.makeup_exam_result IS NOT NULL',
                                )
                            )

                        ),
                        'ExamGradeChange.manual_ng_conversion = 1',
                        'ExamGradeChange.auto_ng_conversion = 1'
                    )
                ),
                'recursive' => -1,
                'order' => array('ExamGradeChange.created DESC')
            ));

            //debug($grade_change);

            if (!empty($grade_change)) {
                $approved_grade_detail['grade'] = $grade_change['ExamGradeChange']['grade'];
            } else {
                $approved_grade_detail['grade'] = $grade_detail['ExamGrade']['grade'];
            }

            $approved_grade_detail['grade_id'] = $grade_detail['ExamGrade']['id'];

            $grade_related_detail = $this->GradeScale->find('first', array(
                'conditions' => array(
                    'GradeScale.id' => $grade_detail['ExamGrade']['grade_scale_id']
                ),
                'contain' => array(
                    'GradeScaleDetail' => array(
                        'Grade' => array(
                            'conditions' => array(
                                'Grade.grade' => $approved_grade_detail['grade']
                            ),
                            'GradeType'
                        )
                    )
                )
            ));

            if (isset($grade_related_detail['GradeScaleDetail'])) {
                foreach ($grade_related_detail['GradeScaleDetail'] as $key => $value) {
                    //debug($value);
                    if (!empty($value['Grade'])) {
                        $approved_grade_detail['point_value'] = $value['Grade']['point_value'];
                        $approved_grade_detail['pass_grade'] = $value['Grade']['pass_grade'];
                        $approved_grade_detail['used_in_gpa'] = $value['Grade']['GradeType']['used_in_gpa'];
                        break;
                    }
                }
            }
            //debug($approved_grade_detail);
            return $approved_grade_detail;
        } else {
            return array();
        }
    }

    function editableExamType($published_course_id)
    {

        $gradeSubmittedCountR = 0;
        $gradeSubmittedCountA = 0;

        $registIds = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.published_course_id' => $published_course_id,
            ),
            'fields' => array('CourseRegistration.id', 'CourseRegistration.id')
        ));

        $addIds = $this->CourseAdd->find('list', array(
            'conditions' => array(
                'CourseAdd.published_course_id' => $published_course_id,
            ),
            'fields' => array('CourseAdd.id', 'CourseAdd.id')
        ));

        if (!empty($registIds)) {
            $gradeSubmittedCountR = $this->find('count', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $registIds,
                    'ExamGrade.department_approval' => 1
                )
            ));
        }

        if (!empty($addIds)) {
            $gradeSubmittedCountA = $this->find('count', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $addIds,
                    'ExamGrade.department_approval' => 1
                )
            ));
        }

        if ($gradeSubmittedCountR > 0 || $gradeSubmittedCountA > 0) {
            return true;
        }

        return false;
    }

    function getExamType($register_add_id = null, $registration = 1)
    {

        $exam_result = array();

        if ($registration == 1) {
            $exam_result = $this->CourseRegistration->ExamResult->find('all', array(
                'conditions' => array(
                    'ExamResult.course_registration_id' => $register_add_id,
                ),
                'contain' => array('ExamType' => array('order' => 'ExamType.order'))
            ));
        } else {
            $exam_result = $this->CourseRegistration->ExamResult->find('all', array(
                'conditions' => array(
                    'ExamResult.course_registration_id' => $register_add_id,
                    'course_add' => 1,
                ),
                'contain' => array('ExamType' => array('order' => 'ExamType.order'))
            ));
        }

        return $exam_result;
    }

    function getStudentsWithNG($published_course_id = null)
    {

        $students_with_ng = array();

        $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse(
            $published_course_id
        );
        //debug($student_course_register_and_adds);

        if (!empty($student_course_register_and_adds)) {
            foreach ($student_course_register_and_adds as $key => $register_add_makeup) {
                foreach ($register_add_makeup as $key => $value) {
                    if ($value['Student']['graduated'] == 0) {
                        //debug($value['Student']['graduated']);

                        $garde = array();

                        if (isset($value['CourseRegistration']) && !empty($value['CourseRegistration']) && is_numeric(
                                $value['CourseRegistration']['id']
                            ) && $value['CourseRegistration']['id'] > 0) {
                            $garde = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                        } elseif (isset($value['CourseAdd']) && !empty($value['CourseAdd']) && is_numeric(
                                $value['CourseAdd']['id']
                            ) && $value['CourseAdd']['id'] > 0) {
                            $garde = $this->getApprovedGrade($value['CourseAdd']['id'], 0);
                        }

                        if (!empty($garde) && strcasecmp($garde['grade'], 'NG') == 0) {
                            $index = count($students_with_ng);
                            $students_with_ng[$index]['full_name'] = $value['Student']['first_name'] . ' ' . $value['Student']['middle_name'] . ' ' . $value['Student']['last_name'];
                            $students_with_ng[$index]['studentnumber'] = $value['Student']['studentnumber'];
                            $students_with_ng[$index]['gender'] = $value['Student']['gender'];
                            $students_with_ng[$index]['grade_id'] = $garde['grade_id'];
                            $students_with_ng[$index]['grade'] = $garde['grade'];
                            $students_with_ng[$index]['haveAssesmentData'] = (isset($value['ExamResult']) && !empty($value['ExamResult']) ? true : false);
                        }
                    }
                }
            }
        }

        return $students_with_ng;
    }

    function getStudentsWithFX($published_course_id = null, $fxselectedbystudent = false)
    {

        $students_with_fx = array();

        if ($fxselectedbystudent) {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse(
                $published_course_id
            );
        } else {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse(
                $published_course_id
            );
        }

        if (!empty($student_course_register_and_adds)) {
            foreach ($student_course_register_and_adds as $key => $register_add_makeup) {
                foreach ($register_add_makeup as $key => $value) {
                    $garde = array();

                    if (isset($value['CourseRegistration']) && !empty($value['CourseRegistration']) && is_numeric(
                            $value['CourseRegistration']['id']
                        ) && $value['CourseRegistration']['id'] > 0) {
                        $garde = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                    } elseif (isset($value['CourseAdd']) && !empty($value['CourseAdd']) && is_numeric(
                            $value['CourseAdd']['id']
                        ) && $value['CourseAdd']['id'] > 0) {
                        $garde = $this->getApprovedGrade($value['CourseAdd']['id'], 0);
                    }

                    if (!empty($garde) && (strcasecmp(
                                $garde['grade'],
                                'Fx'
                            ) == 0 /* || (strcasecmp($garde['grade'], 'F') == 0 &&  isset($garde['used_in_gpa']) && $garde['used_in_gpa']) */)) {
                        $index = count($students_with_fx);

                        $students_with_fx[$value['Student']['id']]['full_name'] = $value['Student']['first_name'] . ' ' . $value['Student']['middle_name'] . ' ' . $value['Student']['last_name'];
                        $students_with_fx[$value['Student']['id']]['studentnumber'] = $value['Student']['studentnumber'];
                        $students_with_fx[$value['Student']['id']]['student_id'] = $value['Student']['id'];
                        $students_with_fx[$value['Student']['id']]['grade_id'] = $garde['grade_id'];

                        if (isset($value['CourseRegistration']) && !empty($value['CourseRegistration']) && is_numeric(
                                $value['CourseRegistration']['id']
                            ) && $value['CourseRegistration']['id'] > 0) {
                            $students_with_fx[$value['Student']['id']]['course_registration_id'] = $value['CourseRegistration']['id'];
                            $students_with_fx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                        } elseif (isset($value['CourseAdd']) && !empty($value['CourseAdd']) && is_numeric(
                                $value['CourseAdd']['id']
                            ) && $value['CourseAdd']['id'] > 0) {
                            $students_with_fx[$value['Student']['id']]['course_add_id'] = $value['CourseAdd']['id'];
                            $students_with_fx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                        }

                        $students_with_fx[$value['Student']['id']]['grade'] = (isset($garde['grade']) ? $garde['grade'] : '');
                    }
                }
            }
        }

        return $students_with_fx;
    }

    function getStudentsWithFXForMakeupAssignment($published_course_id = null, $fxselectedbystudent = false)
    {

        $students_with_fx = array();
        $student_course_register_and_adds = array();

        if ($fxselectedbystudent) {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsTakingFxExamPublishedCourse(
                $published_course_id
            );
        }

        if (!empty($student_course_register_and_adds)) {
            foreach ($student_course_register_and_adds as $key => $register_add_makeup) {
                foreach ($register_add_makeup as $key => $value) {
                    if (isset($value['CourseRegistration']) && !empty($value['CourseRegistration']) && $value['CourseRegistration']['id'] != "") {
                        $garde = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                    } else {
                        $garde = $this->getApprovedGrade($value['CourseAdd']['id'], 0);
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
                            $students_with_fx[$value['Student']['id']]['makeupalreadyapplied'] = ClassRegistry::init(
                                'MakeupExam'
                            )->makeUpExamApplied(
                                $value['Student']['id'],
                                $value['PublishedCourse']['id'],
                                $value['CourseRegistration']['id'],
                                1
                            );
                        } elseif (isset($value['CourseAdd']) && !empty($value['CourseAdd']) && $value['CourseAdd']['id'] != "") {
                            $students_with_fx[$value['Student']['id']]['course_add_id'] = $value['CourseAdd']['id'];
                            $students_with_fx[$value['Student']['id']]['published_course_id'] = $value['PublishedCourse']['id'];
                            $students_with_fx[$value['Student']['id']]['makeupalreadyapplied'] = ClassRegistry::init(
                                'MakeupExam'
                            )->makeUpExamApplied(
                                $value['Student']['id'],
                                $value['PublishedCourse']['id'],
                                $value['CourseAdd']['id'],
                                0
                            );
                        }

                        $students_with_fx[$value['Student']['id']]['grade'] = $garde['grade'];
                    }
                }
            }
        }

        return $students_with_fx;
    }

    function getStudentCopies($student_ids = null, $academic_year = null, $semester = null)
    {

        $student_copies = array();

        if (!empty($student_ids)) {
            foreach ($student_ids as $key => $student_id) {
                $student_copy = $this->getStudentCopy($student_id, $academic_year, $semester);
                if (!empty($student_copy['courses'])) {
                    $student_copy['University'] = ClassRegistry::init('University')->getStudentUnivrsity($student_id);
                    $student_copies[] = $student_copy;
                }
            }
        }

        return $student_copies;
    }

    function getStudentCopy($student_id = null, $academic_year = null, $semester = null)
    {

        $student_copy = array();

        if (!empty($student_id) && is_numeric($student_id) && $student_id > 0) {
            $student_detail = $this->CourseAdd->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                ),
                'contain' => array(
                    'Program' => array('id', 'name', 'shortname'),
                    'ProgramType' => array('id', 'name', 'shortname', 'equivalent_to_id'),
                    'Department' => array('id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone'),
                    'College' => array(
                        'fields' => array(
                            'id',
                            'name',
                            'shortname',
                            'type',
                            'stream',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Campus' => array('id', 'name'),
                    ),
                    'Curriculum',
                )
            ));

            //debug($student_detail);
            $program_type_id = $this->CourseAdd->Student->ProgramTypeTransfer->getStudentProgramType(
                $student_id,
                $academic_year,
                $semester
            );

            $program_type_detail = $this->CourseAdd->Student->ProgramType->find(
                'first',
                array('conditions' => array('ProgramType.id' => $program_type_id), 'recursive' => -1)
            );

            $student_copy['ProgramType'] = $program_type_detail['ProgramType'];
            $program_type_id = $this->CourseAdd->Student->ProgramType->getParentProgramType($program_type_id);

            $pattern = $this->CourseAdd->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                $student_detail['Student']['program_id'],
                $program_type_id,
                $academic_year
            );

            //Retrieving AY and Semester list based on pattern for status
            $ay_and_s_list = array();
            debug($pattern);

            if ($pattern <= 1) {
                $ay_and_s_list[0]['academic_year'] = $academic_year;
                $ay_and_s_list[0]['semester'] = $semester;
            } else {
                $status_prepared = $this->CourseAdd->Student->StudentExamStatus->find('count', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $student_id,
                        'StudentExamStatus.academic_year' => $academic_year,
                        'StudentExamStatus.semester' => $semester
                    ),
                    'recursive' => -1,
                    //'order' => array('StudentExamStatus.created' => 'DESC')
                    'order' => array(
                        'StudentExamStatus.academic_year' => 'DESC',
                        'StudentExamStatus.semester' => 'DESC',
                        'StudentExamStatus.created' => 'DESC'
                    )
                ));

                debug($status_prepared);

                if ($status_prepared == 0) {
                    $ay_and_s_list_draft = $this->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToGenerateStatus(
                        $student_detail['Student']['id'],
                        $academic_year,
                        $semester
                    );
                    debug($ay_and_s_list_draft);
                    //If there are lots of semester without status generation. It is to avoid including other semester/s in the current pattern
                    if (count($ay_and_s_list_draft) > $pattern) {
                        for ($i = 0; $i < $pattern; $i++) {
                            $ay_and_s_list[$i] = $ay_and_s_list_draft[$i];
                        }
                    } else {
                        $ay_and_s_list = $ay_and_s_list_draft;
                    }
                } else {
                    $ay_and_s_list = $this->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToUpdateStatus(
                        $student_detail['Student']['id'],
                        $academic_year,
                        $semester
                    );
                    debug($ay_and_s_list);
                }
            }
            debug($ay_and_s_list);

            //Get list of courses a student registered within the pattern AY and semester list
            $options = array();

            if (!empty($ay_and_s_list)) {
                foreach ($ay_and_s_list as $key => $ay_s) {
                    $options['conditions']['OR'][] = array(
                        'CourseRegistration.academic_year' => $ay_s['academic_year'],
                        'CourseRegistration.semester' => $ay_s['semester'],
                        'CourseRegistration.student_id' => $student_detail['Student']['id']
                    );
                }
                //}

                $options['conditions']['CourseRegistration.student_id'] = $student_detail['Student']['id'];

                $options['contain'] = array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum',
                            'GradeType' => array('Grade')
                        ),
                        'Section' => array(
                            'fields' => array('id', 'name'),
                            'Curriculum' => array(
                                'id',
                                'name',
                                'year_introduced',
                                'type_credit',
                                'specialization_english_degree_nomenclature',
                                'english_degree_nomenclature',
                                'active'
                            ),
                            'Department' => array(
                                'id',
                                'name',
                                'college_id',
                                'type',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                            'College' => array(
                                'id',
                                'name',
                                'shortname',
                                'type',
                                'stream',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                            'YearLevel' => array('id', 'name'),
                        ),
                        'YearLevel' => array('fields' => array('id', 'name')),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        )
                    ),
                    'Section' => array(
                        'Curriculum' => array(
                            'id',
                            'name',
                            'year_introduced',
                            'type_credit',
                            'specialization_english_degree_nomenclature',
                            'english_degree_nomenclature',
                            'active'
                        ),
                        'College' => array(
                            'id',
                            'name',
                            'shortname',
                            'type',
                            'stream',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Department' => array(
                            'id',
                            'name',
                            'college_id',
                            'type',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Program' => array('id', 'name', 'shortname'),
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'YearLevel' => array('id', 'name'),
                    ),
                );

                $options['order'] = array(
                    'CourseRegistration.academic_year' => 'ASC',
                    'CourseRegistration.semester' => 'ASC',
                    'CourseRegistration.id' => 'ASC'
                );

                $student_course_registrations = $this->CourseRegistration->find('all', $options);

                //Get list of courses a student added within the pattern AY and semester list
                $options = array();

                //if (!empty($ay_and_s_list)) {
                foreach ($ay_and_s_list as $key => $ay_s) {
                    $options['conditions']['OR'][] = array(
                        'CourseAdd.academic_year' => $ay_s['academic_year'],
                        'CourseAdd.semester' => $ay_s['semester'],
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation = 1',
                        'CourseAdd.student_id' => $student_detail['Student']['id']
                    );
                }
                //}

                $options['conditions']['CourseAdd.student_id'] = $student_detail['Student']['id'];

                $options['contain'] = array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum',
                            'GradeType' => array('Grade')
                        ),
                        'Section' => array(
                            'fields' => array('id', 'name'),
                            'Curriculum' => array(
                                'id',
                                'name',
                                'year_introduced',
                                'type_credit',
                                'specialization_english_degree_nomenclature',
                                'english_degree_nomenclature',
                                'active'
                            ),
                            'Department' => array(
                                'id',
                                'name',
                                'college_id',
                                'type',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                            'College' => array(
                                'id',
                                'name',
                                'shortname',
                                'type',
                                'stream',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                        ),
                        'YearLevel' => array('fields' => array('id', 'name')),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        )
                    )
                );

                $options['order'] = array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                );

                $student_course_adds = $this->CourseAdd->find('all', $options);
            }


            $student_copy['courses'] = array();

            //List courses the student registered for
            if (!empty($student_course_registrations)) {
                foreach ($student_course_registrations as $key => $student_course_registration) {
                    if ($student_course_registration['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped(
                            $student_course_registration['CourseRegistration']['id']
                        )) {
                        $r_index = count($student_copy['courses']);
                        $student_copy['courses'][$r_index]['Course'] = $student_course_registration['PublishedCourse']['Course'];
                        $student_copy['courses'][$r_index]['PublishedCourse'] = $student_course_registration['PublishedCourse'];
                        $student_copy['courses'][$r_index]['CourseRegistration'] = $student_course_registration['CourseRegistration'];
                        $student_copy['courses'][$r_index]['Grade'] = $this->getApprovedGrade(
                            $student_course_registration['CourseRegistration']['id'],
                            1
                        );
                        $student_copy['courses'][$r_index]['ExamType'] = $this->getExamType(
                            $student_course_registration['CourseRegistration']['id'],
                            1
                        );
                        $student_copy['courses'][$r_index]['hasEquivalentMap'] = ClassRegistry::init(
                            'EquivalentCourse'
                        )->checkCourseHasEquivalentCourse(
                            $student_course_registration['PublishedCourse']['course_id'],
                            $student_detail['Student']['curriculum_id']
                        );

                        // to check that or verify that later Neway
                        $student_copy['courses'][$r_index]['section'] = $student_course_registration['PublishedCourse']['Section'];
                        // to display the examgrade is from reg/add in results tab, Neway 10 = Registration,  11 = course add
                        $student_copy['courses'][$r_index]['regAdd'] = 10;

                        //$student_copy['courses'][$r_index]['firstTime'] = $this->isRegistrationAddForFirstTime($student_course_registration['CourseRegistration']['id'], 1, 1);

                        $course_id = $student_course_registration['PublishedCourse']['Course']['id'];
                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                            $course_id,
                            $student_detail['Student']['curriculum_id']
                        );
                        $matching_courses[$course_id] = $course_id;

                        $studentDetail['Student'] = $student_detail['Student'];
                        $register_add_id = $student_course_registration['CourseRegistration']['id'];

                        $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                            $matching_courses,
                            $student_detail['Student']['id']
                        );

                        if (count($register_and_add_freq) <= 1) {
                            $student_copy['courses'][$r_index]['firstTime'] = 1;
                        } else {
                            $student_copy['courses'][$r_index]['firstTime'] = 0;
                            if (isset($course_id) && !empty($course_id)) {
                                $rep = $this->repeatationLabeling(
                                    $register_and_add_freq,
                                    'register',
                                    $register_add_id,
                                    $studentDetail,
                                    $course_id
                                );
                                //debug($rep);
                                /* if ($rep['repeated_old'] == false && $rep['repeated_new'] == true) {
                                //return true;
                            } */
                                $student_copy['courses'][$r_index]['RepeatitionLabel'] = $rep;
                            }
                        }
                    }
                }
            }

            //List courses the student added for
            if (!empty($student_course_adds)) {
                foreach ($student_course_adds as $key => $student_course_add) {
                    if ($student_course_add['PublishedCourse']['drop'] == 0) {
                        $r_index = count($student_copy['courses']);
                        $student_copy['courses'][$r_index]['Course'] = $student_course_add['PublishedCourse']['Course'];
                        $student_copy['courses'][$r_index]['CourseAdd'] = $student_course_add['CourseAdd'];
                        $student_copy['courses'][$r_index]['Grade'] = $this->getApprovedGrade(
                            $student_course_add['CourseAdd']['id'],
                            0
                        );
                        $student_copy['courses'][$r_index]['ExamType'] = $this->getExamType(
                            $student_course_add['CourseAdd']['id'],
                            0
                        );
                        $student_copy['courses'][$r_index]['hasEquivalentMap'] = ClassRegistry::init(
                            'EquivalentCourse'
                        )->checkCourseHasEquivalentCourse(
                            $student_course_add['PublishedCourse']['course_id'],
                            $student_detail['Student']['curriculum_id']
                        );

                        // to check that or verify that later Neway
                        $student_copy['courses'][$r_index]['section'] = $student_course_add['PublishedCourse']['Section'];
                        // to display the examgrade is from reg/add in results tab, Neway 10 = Registration,  11 = course add
                        $student_copy['courses'][$r_index]['regAdd'] = 11;
                        //$student_copy['courses'][$r_index]['firstTime'] = $this->isRegistrationAddForFirstTime($student_course_add['CourseAdd']['id'], 0, 1);

                        $course_id = $student_course_add['PublishedCourse']['Course']['id'];
                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                            $course_id,
                            $student_detail['Student']['curriculum_id']
                        );
                        $matching_courses[$course_id] = $course_id;

                        $studentDetail['Student'] = $student_detail['Student'];
                        $register_add_id = $student_course_add['CourseAdd']['id'];

                        $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                            $matching_courses,
                            $student_detail['Student']['id']
                        );

                        if (count($register_and_add_freq) <= 1) {
                            $student_copy['courses'][$r_index]['firstTime'] = 1;
                        } else {
                            $student_copy['courses'][$r_index]['firstTime'] = 0;
                            if (isset($course_id) && !empty($course_id)) {
                                $rep = $this->repeatationLabeling(
                                    $register_and_add_freq,
                                    'add',
                                    $register_add_id,
                                    $studentDetail,
                                    $course_id
                                );
                                //debug($rep);
                                /* if ($rep['repeated_old'] == false && $rep['repeated_new'] == true) {
                                //return true;
                            } */
                                $student_copy['courses'][$r_index]['RepeatitionLabel'] = $rep;
                            }
                        }
                    }
                }
            }

            if (!empty($student_course_registrations)) {
                $section_detail = $this->CourseAdd->Student->Section->find('first', array(
                    'conditions' => array(
                        'Section.id' => $student_course_registrations[0]['PublishedCourse']['section_id']
                    ),
                    'contain' => array(
                        'Curriculum' => array(
                            'id',
                            'name',
                            'year_introduced',
                            'type_credit',
                            'specialization_english_degree_nomenclature',
                            'english_degree_nomenclature',
                            'active'
                        ),
                        'College' => array(
                            'id',
                            'name',
                            'shortname',
                            'type',
                            'stream',
                            'type_amharic',
                            'amharic_name',
                            'phone'
                        ),
                        'Department' => array(
                            'id',
                            'name',
                            'college_id',
                            'type',
                            'type_amharic',
                            'amharic_name',
                            'phone'
                        ),
                        'Program' => array('id', 'name', 'shortname'),
                        'ProgramType' => array('id', 'name', 'shortname'),
                        'YearLevel' => array('id', 'name'),
                    )
                ));

                $student_copy['Section'] = $section_detail['Section'];
                $student_copy['Section']['Program'] = $section_detail['Program'];
                $student_copy['Section']['ProgramType'] = $section_detail['ProgramType'];
                $student_copy['Section']['Curriculum'] = $section_detail['Curriculum'];
                $student_copy['Section']['College'] = $section_detail['College'];
                $student_copy['Section']['Department'] = $section_detail['Department'];
                $student_copy['Section']['YearLevel'] = $section_detail['YearLevel'];
                $student_copy['YearLevel'] = $section_detail['YearLevel'];
            } else {
                $student_copy['Section'] = array();
                $student_copy['YearLevel'] = array();
            }

            $student_copy['Student'] = $student_detail['Student'];
            $student_copy['Curriculum'] = $student_detail['Curriculum'];
            $student_copy['Program'] = $student_detail['Program'];
            $student_copy['College'] = $student_detail['College'];
            $student_copy['Department'] = $student_detail['Department'];
            $student_copy['academic_year'] = $academic_year;
            $student_copy['semester'] = $semester;

            $student_copy['Student']['Program'] = $student_detail['Program'];
            $student_copy['Student']['ProgramType'] = $student_detail['ProgramType'];
            $student_copy['Student']['College'] = $student_detail['College'];
            //$student_copy['Student']['Curriculum'] = $student_detail['Curriculum'];
            $student_copy['Student']['Department'] = $student_detail['Department'];


            $student_status = $this->CourseRegistration->Student->StudentExamStatus->find('first', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id,
                    'StudentExamStatus.academic_year' => $academic_year,
                    'StudentExamStatus.semester' => $semester
                ),
                'contain' => array('AcademicStatus'),
                'order' => array(/* 'StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', */
                    'StudentExamStatus.created' => 'DESC'
                )
            ));

            //debug($student_status);
            if (!isset($student_status['StudentExamStatus']) && empty($student_status)) {
                $student_copy['StudentExamStatus'] = array();
                $student_copy['AcademicStatus'] = array();
            } else {
                $student_copy['StudentExamStatus'] = $student_status['StudentExamStatus'];
                $student_copy['AcademicStatus'] = $student_status['AcademicStatus'];
            }

            //Retrieving previous status
            $all_student_status = $this->CourseRegistration->Student->StudentExamStatus->find('all', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id,
                ),
                'contain' => array('AcademicStatus'),
                //'order' => array('StudentExamStatus.academic_year ASC', 'StudentExamStatus.semester ASC')
                'group' => array(
                    'StudentExamStatus.student_id',
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester'
                ),
                'order' => array('StudentExamStatus.academic_year' => 'ASC', 'StudentExamStatus.semester' => 'ASC')
            ));

            $previous_credit_hour_sum = 0;
            $previous_grade_point_sum = 0;
            $previous_student_status = array();
            $previous_academic_status = array();

            if (!empty($all_student_status)) {
                foreach ($all_student_status as $st_key => $student_status2) {
                    if (strcasecmp(
                            $student_status2['StudentExamStatus']['academic_year'],
                            $academic_year
                        ) == 0 && strcasecmp(
                            $student_status2['StudentExamStatus']['semester'],
                            $semester
                        ) == 0 || empty($student_status)) {
                        break;
                    } else {
                        $previous_credit_hour_sum += $student_status2['StudentExamStatus']['credit_hour_sum'];
                        $previous_grade_point_sum += $student_status2['StudentExamStatus']['grade_point_sum'];
                        $previous_student_status = $student_status2['StudentExamStatus'];
                        $previous_academic_status = $student_status2['AcademicStatus'];
                    }
                }
            }

            //If there is previous status
            if (!empty($previous_student_status)) {
                $student_copy['PreviousStudentExamStatus'] = $previous_student_status;
                $student_copy['PreviousAcademicStatus'] = $previous_academic_status;
                $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] = $previous_credit_hour_sum;
                $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] = $previous_grade_point_sum;
            } else {
                //If there is no previous status
                $student_copy['PreviousStudentExamStatus'] = array();
                $student_copy['PreviousAcademicStatus'] = array();
            }
        }

        return $student_copy;
    }

    function getStudentACProfile($student_id = null, $academic_year = null, $semester = null)
    {

        $student_copy = array();

        if (!empty($student_id) && is_numeric($student_id) && $student_id > 0) {
            $student_detail = $this->CourseAdd->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name', 'equivalent_to_id'),
                    'Department' => array('id', 'name', 'college_id', 'type', 'amharic_name', 'type_amharic', 'phone'),
                    'College' => array(
                        'fields' => array(
                            'id',
                            'name',
                            'shortname',
                            'type',
                            'stream',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Campus' => array('id', 'name'),
                    ),
                    'Curriculum',
                )
            ));
            //debug($student_detail);

            $program_type_id = $this->CourseAdd->Student->ProgramTypeTransfer->getStudentProgramType(
                $student_id,
                $academic_year,
                $semester
            );

            $program_type_detail = $this->CourseAdd->Student->ProgramType->find(
                'first',
                array('conditions' => array('ProgramType.id' => $program_type_id), 'recursive' => -1)
            );

            $student_copy['ProgramType'] = $program_type_detail['ProgramType'];
            $program_type_id = $this->CourseAdd->Student->ProgramType->getParentProgramType($program_type_id);
            $pattern = $this->CourseAdd->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                $student_detail['Student']['program_id'],
                $program_type_id,
                $academic_year
            );

            //Retrieving AY and Semester list based on pattern for status
            $ay_and_s_list = array();
            $ay_and_s_list[0]['academic_year'] = $academic_year;
            $ay_and_s_list[0]['semester'] = $semester;

            //Get list of courses a student registered within the pattern AY and semester list
            $options = array();
            $student_course_registrations = array();

            if (!empty($ay_and_s_list)) {
                foreach ($ay_and_s_list as $key => $ay_s) {
                    $options['conditions']['OR'][] = array(
                        'CourseRegistration.academic_year' => $ay_s['academic_year'],
                        'CourseRegistration.semester' => $ay_s['semester'],
                        'CourseRegistration.student_id' => $student_detail['Student']['id']
                    );
                }

                $options['conditions']['CourseRegistration.student_id'] = $student_detail['Student']['id'];

                $options['contain'] = array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum',
                            'GradeType' => array('Grade')
                        ),
                        'Section' => array(
                            'fields' => array('id', 'name'),
                            'Department' => array(
                                'id',
                                'name',
                                'college_id',
                                'type',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                            'College' => array(
                                'id',
                                'name',
                                'shortname',
                                'type',
                                'stream',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                        ),
                        'YearLevel' => array('fields' => array('id', 'name')),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        )
                    ),
                    'Section' => array(
                        'Curriculum' => array(
                            'id',
                            'name',
                            'year_introduced',
                            'type_credit',
                            'specialization_english_degree_nomenclature',
                            'english_degree_nomenclature',
                            'active'
                        ),
                        'College' => array(
                            'id',
                            'name',
                            'shortname',
                            'type',
                            'stream',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Department' => array(
                            'id',
                            'name',
                            'college_id',
                            'type',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                    ),
                );

                $options['order'] = array(
                    'CourseRegistration.academic_year' => 'ASC',
                    'CourseRegistration.semester' => 'ASC',
                    'CourseRegistration.id' => 'ASC'
                );

                $student_course_registrations = $this->CourseRegistration->find('all', $options);
                //}

                //Get list of courses a student added within the pattern AY and semester list
                $options = array();
                $student_course_adds = array();

                //if (!empty($ay_and_s_list)) {
                foreach ($ay_and_s_list as $key => $ay_s) {
                    $options['conditions']['OR'][] = array(
                        'CourseAdd.academic_year' => $ay_s['academic_year'],
                        'CourseAdd.semester' => $ay_s['semester'],
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation = 1',
                        'CourseAdd.student_id' => $student_detail['Student']['id']
                    );
                }

                $options['conditions']['CourseAdd.student_id'] = $student_detail['Student']['id'];

                $options['contain'] = array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'Curriculum',
                            'GradeType' => array('Grade')
                        ),
                        'Section' => array(
                            'fields' => array('id', 'name'),
                            'Department' => array(
                                'id',
                                'name',
                                'college_id',
                                'type',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                            'College' => array(
                                'id',
                                'name',
                                'shortname',
                                'type',
                                'stream',
                                'amharic_name',
                                'type_amharic',
                                'phone'
                            ),
                        ),
                        'YearLevel' => array('fields' => array('id', 'name')),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        )
                    )
                );

                $options['order'] = array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                );

                $student_course_adds = $this->CourseAdd->find('all', $options);
            }

            //List courses the student registered for
            $student_copy['courses'] = array();
            // $student_copy['massAdds'] = array();

            if (!empty($student_course_registrations)) {
                foreach ($student_course_registrations as $key => $student_course_registration) {
                    if ($student_course_registration['PublishedCourse']['drop'] == 0 && !$this->CourseRegistration->isCourseDroped(
                            $student_course_registration['CourseRegistration']['id']
                        )) {
                        $r_index = count($student_copy['courses']);
                        $student_copy['courses'][$r_index]['Course'] = $student_course_registration['PublishedCourse']['Course'];
                        $student_copy['courses'][$r_index]['PublishedCourse'] = $student_course_registration['PublishedCourse'];
                        $student_copy['courses'][$r_index]['CourseRegistration'] = $student_course_registration['CourseRegistration'];
                        $student_copy['courses'][$r_index]['Grade'] = $this->getApprovedGrade(
                            $student_course_registration['CourseRegistration']['id'],
                            1
                        );
                        $student_copy['courses'][$r_index]['ExamType'] = $this->getExamType(
                            $student_course_registration['CourseRegistration']['id'],
                            1
                        );
                        $student_copy['courses'][$r_index]['hasEquivalentMap'] = ClassRegistry::init(
                            'EquivalentCourse'
                        )->checkCourseHasEquivalentCourse(
                            $student_course_registration['PublishedCourse']['course_id'],
                            $student_detail['Student']['curriculum_id']
                        );
                        // to check that or verify that later Neway
                        $student_copy['courses'][$r_index]['section'] = $student_course_registration['PublishedCourse']['Section'];
                        // to display the examgrade is from reg/add in results tab, Neway 10 = Registration,  11 = course add
                        $student_copy['courses'][$r_index]['regAdd'] = 10;

                        //$student_copy['courses'][$r_index]['firstTime'] = $this->isRegistrationAddForFirstTime($student_course_registration['CourseRegistration']['id'], 1, 1);

                        $course_id = $student_course_registration['PublishedCourse']['Course']['id'];
                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                            $course_id,
                            $student_detail['Student']['curriculum_id']
                        );
                        $matching_courses[$course_id] = $course_id;

                        $studentDetail['Student'] = $student_detail['Student'];
                        $register_add_id = $student_course_registration['CourseRegistration']['id'];

                        $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                            $matching_courses,
                            $student_detail['Student']['id']
                        );

                        if (count($register_and_add_freq) <= 1) {
                            $student_copy['courses'][$r_index]['firstTime'] = 1;
                        } else {
                            $student_copy['courses'][$r_index]['firstTime'] = 0;
                            if (isset($course_id) && !empty($course_id)) {
                                $rep = $this->repeatationLabeling(
                                    $register_and_add_freq,
                                    'register',
                                    $register_add_id,
                                    $studentDetail,
                                    $course_id
                                );
                                //debug($rep);
                                /* if ($rep['repeated_old'] == false && $rep['repeated_new'] == true) {
                                //return true;
                            } */
                                $student_copy['courses'][$r_index]['RepeatitionLabel'] = $rep;
                            }
                        }
                    }
                }
            }

            //List courses the student added for
            if (!empty($student_course_adds)) {
                foreach ($student_course_adds as $key => $student_course_add) {
                    if ($student_course_add['PublishedCourse']['drop'] == 0) {
                        $r_index = count($student_copy['courses']);
                        $student_copy['courses'][$r_index]['Course'] = $student_course_add['PublishedCourse']['Course'];
                        $student_copy['courses'][$r_index]['PublishedCourse'] = $student_course_add['PublishedCourse'];
                        $student_copy['courses'][$r_index]['CourseAdd'] = $student_course_add['CourseAdd'];
                        $student_copy['courses'][$r_index]['Grade'] = $this->getApprovedGrade(
                            $student_course_add['CourseAdd']['id'],
                            0
                        );
                        $student_copy['courses'][$r_index]['ExamType'] = $this->getExamType(
                            $student_course_add['CourseAdd']['id'],
                            0
                        );
                        $student_copy['courses'][$r_index]['hasEquivalentMap'] = ClassRegistry::init(
                            'EquivalentCourse'
                        )->checkCourseHasEquivalentCourse(
                            $student_course_add['PublishedCourse']['course_id'],
                            $student_detail['Student']['curriculum_id']
                        );
                        // to check that or verify that later Neway
                        $student_copy['courses'][$r_index]['section'] = $student_course_add['PublishedCourse']['Section'];
                        // to display the examgrade is from reg/add in results tab, Neway 10 = Registration,  11 = course add
                        $student_copy['courses'][$r_index]['regAdd'] = 11;
                        //$student_copy['courses'][$r_index]['firstTime'] = $this->isRegistrationAddForFirstTime($student_course_add['CourseAdd']['id'], 0, 1);

                        $course_id = $student_course_add['PublishedCourse']['Course']['id'];
                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                            $course_id,
                            $student_detail['Student']['curriculum_id']
                        );
                        $matching_courses[$course_id] = $course_id;

                        $studentDetail['Student'] = $student_detail['Student'];
                        $register_add_id = $student_course_add['CourseAdd']['id'];

                        $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                            $matching_courses,
                            $student_detail['Student']['id']
                        );

                        if (count($register_and_add_freq) <= 1) {
                            $student_copy['courses'][$r_index]['firstTime'] = 1;
                        } else {
                            $student_copy['courses'][$r_index]['firstTime'] = 0;
                            if (isset($course_id) && !empty($course_id)) {
                                $rep = $this->repeatationLabeling(
                                    $register_and_add_freq,
                                    'add',
                                    $register_add_id,
                                    $studentDetail,
                                    $course_id
                                );
                                //debug($rep);
                                /* if ($rep['repeated_old'] == false && $rep['repeated_new'] == true) {
                                //return true;
                            } */
                                $student_copy['courses'][$r_index]['RepeatitionLabel'] = $rep;
                            }
                        }
                    }
                }
            }

            // don't remove this, shows section and college institute, the code seems silly but it works, Neway
            if (!empty($student_course_registrations)) {
                $section_detail = $this->CourseAdd->Student->Section->find('first', array(
                    'conditions' => array(
                        'Section.id' => $student_course_registrations[0]['PublishedCourse']['section_id']
                    ),
                    'contain' => array(
                        'Curriculum' => array(
                            'id',
                            'name',
                            'year_introduced',
                            'type_credit',
                            'specialization_english_degree_nomenclature',
                            'english_degree_nomenclature',
                            'active'
                        ),
                        'College' => array(
                            'id',
                            'name',
                            'shortname',
                            'type',
                            'stream',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Department' => array(
                            'id',
                            'name',
                            'college_id',
                            'type',
                            'amharic_name',
                            'type_amharic',
                            'phone'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                    )
                ));
                $student_copy['Section'] = $section_detail['Section'];
                $student_copy['Section']['Program'] = $section_detail['Program'];
                $student_copy['Section']['ProgramType'] = $section_detail['ProgramType'];
                $student_copy['Section']['Curriculum'] = $section_detail['Curriculum'];
                $student_copy['Section']['College'] = $section_detail['College'];
                $student_copy['Section']['Department'] = $section_detail['Department'];
                $student_copy['Section']['YearLevel'] = $section_detail['YearLevel'];
                $student_copy['YearLevel'] = $section_detail['YearLevel'];
            } else {
                $student_copy['Section'] = array();
                $student_copy['YearLevel'] = array();
            }

            $student_copy['Student'] = $student_detail['Student'];
            $student_copy['Curriculum'] = $student_detail['Curriculum'];
            $student_copy['Program'] = $student_detail['Program'];
            $student_copy['College'] = $student_detail['College'];
            $student_copy['Department'] = $student_detail['Department'];
            $student_copy['academic_year'] = $academic_year;
            $student_copy['semester'] = $semester;

            $student_copy['Student']['Program'] = $student_detail['Program'];
            $student_copy['Student']['ProgramType'] = $student_detail['ProgramType'];
            $student_copy['Student']['College'] = $student_detail['College'];
            //$student_copy['Student']['Curriculum'] = $student_detail['Curriculum'];
            $student_copy['Student']['Department'] = $student_detail['Department'];

            $student_status = $this->CourseRegistration->Student->StudentExamStatus->find('first', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id,
                    'StudentExamStatus.academic_year' => $academic_year,
                    'StudentExamStatus.semester' => $semester
                ),
                'contain' => array('AcademicStatus'),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'ASC',
                    'StudentExamStatus.semester' => 'ASC',
                    'StudentExamStatus.id' => 'DESC'
                )
            ));
            //debug($student_status);

            if (!isset($student_status['StudentExamStatus']) && empty($student_status)) {
                $student_copy['StudentExamStatus'] = array();
                $student_copy['AcademicStatus'] = array();
            } else {
                $student_copy['StudentExamStatus'] = $student_status['StudentExamStatus'];
                $student_copy['AcademicStatus'] = $student_status['AcademicStatus'];
            }

            //Retrieving previous status
            $all_student_status = $this->CourseRegistration->Student->StudentExamStatus->find('all', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id,
                ),
                'contain' => array('AcademicStatus'),
                //'order' => array('StudentExamStatus.academic_year ASC', 'StudentExamStatus.semester ASC')
                'group' => array(
                    'StudentExamStatus.student_id',
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester'
                ),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'ASC',
                    'StudentExamStatus.semester' => 'ASC',
                    'StudentExamStatus.id' => 'DESC'
                )
            ));

            $previous_credit_hour_sum = 0;
            $previous_grade_point_sum = 0;
            $previous_student_status = array();
            $previous_academiv_status = array();

            if (!empty($all_student_status)) {
                foreach ($all_student_status as $st_key => $student_status2) {
                    if (strcasecmp(
                            $student_status2['StudentExamStatus']['academic_year'],
                            $academic_year
                        ) == 0 && strcasecmp(
                            $student_status2['StudentExamStatus']['semester'],
                            $semester
                        ) == 0 || empty($student_status)) {
                        break;
                    } else {
                        $previous_credit_hour_sum += $student_status2['StudentExamStatus']['credit_hour_sum'];
                        $previous_grade_point_sum += $student_status2['StudentExamStatus']['grade_point_sum'];
                        $previous_student_status = $student_status2['StudentExamStatus'];
                        $previous_academiv_status = $student_status2['AcademicStatus'];
                    }
                }
            }

            //If there is previous status
            if (!empty($previous_student_status)) {
                $student_copy['PreviousStudentExamStatus'] = $previous_student_status;
                $student_copy['PreviousAcademicStatus'] = $previous_academiv_status;
                $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] = $previous_credit_hour_sum;
                $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] = $previous_grade_point_sum;
            } else {
                //If there is no previous status
                $student_copy['PreviousStudentExamStatus'] = array();
                $student_copy['PreviousAcademicStatus'] = array();
            }
        }

        return $student_copy;
    }

    function getMasterSheet($section_id = null, $academic_year = null, $semester = null)
    {

        $students_and_grades = array();

        $students_in_section = $this->CourseRegistration->Student->Section->StudentsSection->find('all', array(
            'conditions' => array(
                'StudentsSection.section_id' => $section_id
            ),
            'group' => array(
                'StudentsSection.student_id',
                'StudentsSection.section_id'
            ),
            'recursive' => -1,
        ));

        $students_in_section_ids = $this->CourseRegistration->Student->Section->StudentsSection->find('list', array(
            'conditions' => array(
                'StudentsSection.section_id' => $section_id
            ),
            'group' => array(
                'StudentsSection.student_id',
                'StudentsSection.section_id'
            ),
            'fields' => array(
                'StudentsSection.student_id',
                'StudentsSection.student_id'
            ),
            'recursive' => -1,
        ));

        $studentRegisteredCourseForSection = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.section_id' => $section_id
            ),
            'fields' => array(
                'CourseRegistration.student_id',
                'CourseRegistration.section_id'
            ),
            'recursive' => -1,
        ));

        $count = count($students_in_section);

        if (!empty($studentRegisteredCourseForSection)) {
            foreach ($studentRegisteredCourseForSection as $stuId => $sectId) {
                if (!in_array($stuId, $students_in_section_ids) && $sectId == $section_id) {
                    $students_in_section[$count]['StudentsSection']['student_id'] = $stuId;
                    $students_in_section[$count]['StudentsSection']['section_id'] = $sectId;
                    $count++;
                }
            }
        }

        /* Get each student pattern, AY & Semester and list of courses s/he registered and add within the returned AY and semester */
        $registered_courses = array();
        $added_courses = array();

        if (!empty($students_in_section)) {
            foreach ($students_in_section as $key => $section_student) {
                $student_detail = $this->CourseAdd->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $section_student['StudentsSection']['student_id']
                    ),
                    'recursive' => -1
                ));

                $program_type_id = $this->CourseAdd->Student->ProgramTypeTransfer->getStudentProgramType(
                    $student_detail['Student']['id'],
                    $academic_year,
                    $semester
                );

                $program_type_id = $this->CourseAdd->Student->ProgramType->getParentProgramType($program_type_id);
                $pattern = $this->CourseAdd->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                    $student_detail['Student']['program_id'],
                    $program_type_id,
                    $academic_year
                );

                //Retrieving AY and Semester list based on pattern for status
                $ay_and_s_list = array();

                if ($pattern <= 1) {
                    $ay_and_s_list[0]['academic_year'] = $academic_year;
                    $ay_and_s_list[0]['semester'] = $semester;
                } else {
                    $status_prepared = $this->CourseAdd->Student->StudentExamStatus->find('count', array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $student_detail['Student']['id'],
                            'StudentExamStatus.academic_year' => $academic_year,
                            'StudentExamStatus.semester' => $semester
                        ),
                        //'order' => array('StudentExamStatus.created' => 'DESC'),
                        'order' => array(
                            'StudentExamStatus.academic_year' => 'DESC',
                            'StudentExamStatus.semester' => 'DESC',
                            'StudentExamStatus.created' => 'DESC'
                        ),
                        'recursive' => -1,
                    ));

                    if ($status_prepared == 0) {
                        $ay_and_s_list_draft = $this->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToGenerateStatus(
                            $student_detail['Student']['id'],
                            $academic_year,
                            $semester
                        );
                        //If there are lots of semester without status generation. It is to avoid including other semester/s in the current pattern
                        if (count($ay_and_s_list_draft) > $pattern) {
                            for ($i = 0; $i < $pattern; $i++) {
                                $ay_and_s_list[$i] = $ay_and_s_list_draft[$i];
                            }
                        } else {
                            $ay_and_s_list = $ay_and_s_list_draft;
                        }
                    } else {
                        $ay_and_s_list = $this->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToUpdateStatus(
                            $student_detail['Student']['id'],
                            $academic_year,
                            $semester
                        );
                    }
                } //End of getting AY and Semester list

                //Get list of courses a student registered within the pattern AY and semester list
                $options = array();

                if (!empty($ay_and_s_list)) {
                    foreach ($ay_and_s_list as $key => $ay_s) {
                        $options['conditions']['OR'][] = array(
                            'CourseRegistration.academic_year' => $ay_s['academic_year'],
                            'CourseRegistration.semester' => $ay_s['semester'],
                        );
                    }
                }

                $options['conditions']['CourseRegistration.student_id'] = $student_detail['Student']['id'];
                $options['contain'] = array('PublishedCourse' => array('Course'));

                $student_course_registrations = $this->CourseRegistration->find('all', $options);

                //Get list of courses a student added within the pattern AY and semester list
                $options = array();

                if (!empty($ay_and_s_list)) {
                    foreach ($ay_and_s_list as $key => $ay_s) {
                        $options['conditions']['OR'][] = array(
                            'CourseAdd.academic_year' => $ay_s['academic_year'],
                            'CourseAdd.semester' => $ay_s['semester'],
                            'CourseAdd.department_approval = 1',
                            'CourseAdd.registrar_confirmation = 1'
                        );
                    }
                }

                $options['conditions']['CourseAdd.student_id'] = $student_detail['Student']['id'];
                $options['contain'] = array('PublishedCourse' => array('Course'));

                $student_course_adds = $this->CourseAdd->find('all', $options);

                //List courses the section registered for
                if (!empty($student_course_registrations)) {
                    foreach ($student_course_registrations as $key => $student_course_registration) {
                        if ($student_course_registration['PublishedCourse']['drop'] == 0) {
                            //Avoiding repeated data
                            foreach ($registered_courses as $key2 => $registered_course) {
                                if ($registered_course['id'] == $student_course_registration['PublishedCourse']['Course']['id']) {
                                    continue 2;
                                }
                            }

                            $r_index = count($registered_courses);

                            $registered_courses[$r_index]['id'] = $student_course_registration['PublishedCourse']['Course']['id'];
                            $registered_courses[$r_index]['course_title'] = $student_course_registration['PublishedCourse']['Course']['course_title'];
                            $registered_courses[$r_index]['course_id'] = $student_course_registration['PublishedCourse']['Course']['id'];
                            $registered_courses[$r_index]['course_code'] = $student_course_registration['PublishedCourse']['Course']['course_code'];
                            $registered_courses[$r_index]['credit'] = $student_course_registration['PublishedCourse']['Course']['credit'];
                            $registered_courses[$r_index]['published_course_id'] = $student_course_registration['CourseRegistration']['published_course_id'];
                        }
                    }
                }

                //List courses the section added for
                if (!empty($student_course_adds)) {
                    foreach ($student_course_adds as $key => $student_course_add) {
                        if ($student_course_add['PublishedCourse']['drop'] == 0) {
                            //Avoiding repeated data
                            foreach ($added_courses as $key2 => $added_course) {
                                if ($added_course['id'] == $student_course_add['PublishedCourse']['Course']['id']) {
                                    continue 2;
                                }
                            }

                            $r_index = count($added_courses);

                            $added_courses[$r_index]['id'] = $student_course_add['PublishedCourse']['Course']['id'];
                            $added_courses[$r_index]['course_title'] = $student_course_add['PublishedCourse']['Course']['course_title'];
                            $added_courses[$r_index]['course_code'] = $student_course_add['PublishedCourse']['Course']['course_code'];
                            $added_courses[$r_index]['course_id'] = $student_course_add['PublishedCourse']['Course']['id'];
                            $added_courses[$r_index]['credit'] = $student_course_add['PublishedCourse']['Course']['credit'];
                            $added_courses[$r_index]['published_course_id'] = $student_course_add['CourseAdd']['published_course_id'];
                        }
                    }
                }
            }
        }

        //Compiling each registered course grade
        if (!empty($students_in_section)) {
            foreach ($students_in_section as $key => $value) {
                $previous_ay_and_s_list = $this->getListOfAyAndSemester(
                    $value['StudentsSection']['student_id'],
                    $academic_year,
                    $semester
                );
                $deduct_credit = 0;
                $deduct_gp = 0;
                $index = count($students_and_grades);

                $student_detail = $this->CourseRegistration->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $value['StudentsSection']['student_id']
                    ),
                    'recursive' => -1
                ));

                $students_and_grades[$index]['full_name'] = $student_detail['Student']['first_name'] . ' ' . $student_detail['Student']['middle_name'] . ' ' . $student_detail['Student']['last_name'];
                $students_and_grades[$index]['studentnumber'] = $student_detail['Student']['studentnumber'];
                $students_and_grades[$index]['gender'] = $student_detail['Student']['gender'];

                $student_status = $this->CourseRegistration->Student->StudentExamStatus->find('first', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $value['StudentsSection']['student_id'],
                        'StudentExamStatus.academic_year' => $academic_year,
                        'StudentExamStatus.semester' => $semester
                    ),
                    'contain' => array(
                        'AcademicStatus'
                    )
                ));
                //debug($student_status);

                if (isset($student_status['StudentExamStatus'])) {
                    $students_and_grades[$index]['StudentExamStatus'] = $student_status['StudentExamStatus'];
                    $students_and_grades[$index]['AcademicStatus'] = $student_status['AcademicStatus'];
                } else {
                    $students_and_grades[$index]['StudentExamStatus'] = array();
                    $students_and_grades[$index]['AcademicStatus'] = array();
                }

                //Retrieving previous status
                $all_student_status = $this->CourseRegistration->Student->StudentExamStatus->find('all', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $value['StudentsSection']['student_id'],
                    ),
                    //'order' => array('StudentExamStatus.created' => 'ASC'),
                    'group' => array(
                        'StudentExamStatus.student_id',
                        'StudentExamStatus.academic_year',
                        'StudentExamStatus.semester'
                    ),
                    'order' => array('StudentExamStatus.academic_year' => 'ASC', 'StudentExamStatus.semester' => 'ASC'),
                    'recursive' => -1,
                ));

                $previous_credit_hour_sum = 0;
                $previous_grade_point_sum = 0;
                $previous_student_status = array();

                if (!empty($all_student_status)) {
                    foreach ($all_student_status as $st_key => $student_status2) {
                        if (strcasecmp(
                                $student_status2['StudentExamStatus']['academic_year'],
                                $academic_year
                            ) == 0 && strcasecmp($student_status2['StudentExamStatus']['semester'], $semester) == 0) {
                            break;
                        } else {
                            $previous_credit_hour_sum += $student_status2['StudentExamStatus']['credit_hour_sum'];
                            $previous_grade_point_sum += $student_status2['StudentExamStatus']['grade_point_sum'];
                            $previous_student_status = $student_status2['StudentExamStatus'];
                        }
                    }
                }

                //If there is previous status
                if (!empty($previous_student_status)) {
                    $students_and_grades[$index]['PreviousStudentExamStatus'] = $previous_student_status;
                    $students_and_grades[$index]['PreviousStudentExamStatus']['previous_credit_hour_sum'] = $previous_credit_hour_sum;
                    $students_and_grades[$index]['PreviousStudentExamStatus']['previous_grade_point_sum'] = $previous_grade_point_sum;
                } else {
                    //If there is no previous status
                    $students_and_grades[$index]['PreviousStudentExamStatus'] = array();
                }

                //Exam grade for each course a student registered
                if (!empty($registered_courses)) {
                    foreach ($registered_courses as $key2 => $registered_course) {
                        //debug($registered_course);

                        $registration_id = $this->CourseRegistration->field('id', array(
                            'CourseRegistration.published_course_id' => $registered_course['published_course_id'],
                            'CourseRegistration.student_id' => $value['StudentsSection']['student_id'],
                        ));

                        if (!empty($registration_id)) {
                            //debug($this->getApprovedGrade($registration_id, 1));
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']] = $this->getApprovedGrade(
                                $registration_id,
                                1
                            );
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['credit'] = $registered_course['credit'];
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['registered'] = true;
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['droped'] = $this->CourseRegistration->isCourseDroped(
                                $registration_id
                            );
                        } else {
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']] = array();
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['registered'] = false;
                        }
                        //END: Getting repeated courses credit hour (if there is any)
                    }
                }


                //Exam grade for each course a student added
                if (!empty($added_courses)) {
                    foreach ($added_courses as $key2 => $added_course) {
                        $add_id = $this->CourseAdd->field('id', array(
                            'CourseAdd.published_course_id' => $added_course['published_course_id'],
                            'CourseAdd.student_id' => $value['StudentsSection']['student_id'],
                            'CourseAdd.department_approval = 1',
                            'CourseAdd.registrar_confirmation = 1'
                        ));

                        if (!empty($add_id)) {
                            $students_and_grades[$index]['courses']['a-' . $added_course['id']] = $this->getApprovedGrade(
                                $add_id,
                                0
                            );
                            $students_and_grades[$index]['courses']['a-' . $added_course['id']]['credit'] = $added_course['credit'];
                            $students_and_grades[$index]['courses']['a-' . $added_course['id']]['added'] = true;
                            //START: Getting repeated courses credit hour (if there is any)
                        } else {
                            $students_and_grades[$index]['courses']['a-' . $added_course['id']] = array();
                            $students_and_grades[$index]['courses']['a-' . $added_course['id']]['added'] = false;
                        }
                    }
                }

                $all_ay_s_list_for_deduction_calc = $previous_ay_and_s_list;
                $ded_index = count($all_ay_s_list_for_deduction_calc);

                $all_ay_s_list_for_deduction_calc[$ded_index]['academic_year'] = $academic_year;
                $all_ay_s_list_for_deduction_calc[$ded_index]['semester'] = $semester;

                $credit_and_point_deduction = $this->getTotalCreditAndPointDeduction(
                    $value['StudentsSection']['student_id'],
                    $all_ay_s_list_for_deduction_calc
                );

                $students_and_grades[$index]['deduct_credit'] = $credit_and_point_deduction['deduct_credit_hour_sum'];
                $students_and_grades[$index]['deduct_gp'] = $credit_and_point_deduction['deduct_grade_point_sum'];
            }
        }

        $master_sheet = array();
        $master_sheet['registered_courses'] = $registered_courses;
        $master_sheet['added_courses'] = $added_courses;
        $master_sheet['students_and_grades'] = $students_and_grades;
        //debug($students_ids);

        return $master_sheet;
    }

    function studentCopy($student_ids = array())
    {

        $student_copy = array();
        $student_copy_array = array();

        if (!empty($student_ids)) {
            App::import('Component', 'Auth');
            $Auth = new AuthComponent(new ComponentRegistry());

            foreach ($student_ids as $ky => $student_id) {
                $student_detail = $this->CourseRegistration->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $student_id
                    ),
                    'contain' => array(
                        'Curriculum',
                        'GraduateList',
                        'GraduationWork' => array('order' => array('GraduationWork.created DESC')),
                        'ExitExam' => array('order' => array('ExitExam.exam_date' => 'DESC', 'ExitExam.id' => 'DESC')),
                        'Program',
                        'ProgramType',
                        'HighSchoolEducationBackground' => array(
                            'conditions' => array(
                                'HighSchoolEducationBackground.national_exam_taken' => 1
                            ),
                            'Region' => array(
                                'fields' => array('id', 'name', 'country_id'),
                                'Country' => array('id', 'name'),
                            )
                        ),
                        'HigherEducationBackground',
                        'EslceResult',
                        'EheeceResult',
                        'Department',
                        'College',
                        'Country' => array('id', 'name'),
                        'Region' => array(
                            'fields' => array('id', 'name', 'country_id'),
                            'Country' => array('id', 'name'),
                        ),
                        'AcceptedStudent' => array(
                            'fields' => array(
                                'id',
                                'studentnumber',
                                'high_school',
                                'moeadmissionnumber',
                                'region_id',
                                'academicyear'
                            ),
                            'Region' => array(
                                'fields' => array('id', 'name', 'country_id'),
                                'Country' => array('id', 'name'),
                            )
                        ),
                        'EslceResult' => array('order' => array('EslceResult.exam_year' => 'DESC')),
                        'EheeceResult' => array('order' => array('EheeceResult.exam_year' => 'DESC')),
                    )
                ));


                $university_detail = ClassRegistry::init('University')->getStudentUnivrsity($student_id);
                $transcript_footer_detail = $this->CourseRegistration->Student->Program->TranscriptFooter->getStudentTranscriptFooter(
                    $student_id
                );

                $recentCode = ClassRegistry::init('CertificateVerificationCode')->find('first', array(
                    'conditions' => array(
                        'CertificateVerificationCode.student_id' => $student_id,
                        'CertificateVerificationCode.user' => $Auth->user('full_name'),
                        'CertificateVerificationCode.type' => "Student Copy"
                    )
                ));

                if (isset($recentCode) && !empty($recentCode)) {
                    $code = $recentCode['CertificateVerificationCode']['code'];
                } else {
                    $verification = array();
                    $code = ClassRegistry::init('CertificateVerificationCode')->generateCode('SC');
                    $verification['CertificateVerificationCode']['user'] = $Auth->user('full_name');
                    $verification['CertificateVerificationCode']['student_id'] = $student_id;
                    $verification['CertificateVerificationCode']['type'] = "Student Copy";
                    $verification['CertificateVerificationCode']['code'] = $code;
                    //ClassRegistry::init('CertificateVerificationCode')->create();
                    ClassRegistry::init('CertificateVerificationCode')->save($verification);
                }

                $ExitExam = array();

                if ($student_detail['Student']['program_id'] == 1) {
                    $approvedExitExamGrade = $this->getApprovedExitExamGrade($student_id);

                    if (isset($approvedExitExamGrade) && !empty($approvedExitExamGrade['grade'])) {
                        $ExitExam['course'] = $approvedExitExamGrade['Course']['course_code_title'];
                        //debug($approvedExitExamGrade['Course']['course_code_title']);
                        $gradeForDocument = ((strcasecmp($approvedExitExamGrade['grade'], 'P') == 0 || strcasecmp(
                                $approvedExitExamGrade['grade'],
                                'Pass'
                            ) == 0) ? 'Pass' : ((strcasecmp($approvedExitExamGrade['grade'], 'F') == 0 || strcasecmp(
                                $approvedExitExamGrade['grade'],
                                'Fail'
                            ) == 0) ? 'Fail' : '---'));

                        $exitExamresult = ClassRegistry::init('ExitExam')->find('first', array(
                            'conditions' => array(
                                'ExitExam.student_id' => $student_id,
                                //'ExitExam.course_id' =>  $student['Student']['ExitExamGrade']['course_id']
                            ),
                            'order' => array('ExitExam.exam_date' => 'DESC', 'ExitExam.id' => 'DESC'),
                            'recursive' => -1
                        ));

                        if ($exitExamresult) {
                            //debug($exitExamresult['ExitExam']);
                            $gradeForDocument .= ' (' . $exitExamresult['ExitExam']['result'] . '%)';
                            $ExitExam['exam_date'] = $exitExamresult['ExitExam']['exam_date'];
                            $ExitExam['result'] = $exitExamresult['ExitExam']['result'];
                        }

                        if (isset($exitExamresult['ExitExam']['result']) && is_numeric(
                                $exitExamresult['ExitExam']['result']
                            ) && ((int)$exitExamresult['ExitExam']['result'] < 50)) {
                            $gradeForDocument = 'Fail (' . $exitExamresult['ExitExam']['result'] . '%)';
                        }

                        if (isset($exitExamresult['ExitExam']['result']) && is_numeric(
                                $exitExamresult['ExitExam']['result']
                            ) && ((int)$exitExamresult['ExitExam']['result'] >= 50)) {
                            $gradeForDocument = 'Pass (' . $exitExamresult['ExitExam']['result'] . '%)';
                        }

                        $ExitExam['result_formated'] = $gradeForDocument;
                    } else {
                        $exitExamresult = ClassRegistry::init('ExitExam')->find('first', array(
                            'conditions' => array(
                                'ExitExam.student_id' => $student_id,
                                //'ExitExam.course_id' =>  $student['Student']['ExitExamGrade']['course_id']
                            ),
                            'order' => array('ExitExam.exam_date' => 'DESC', 'ExitExam.id' => 'DESC'),
                            'recursive' => -1
                        ));

                        if ($exitExamresult) {
                            if (!empty($exitExamresult['ExitExam']['result']) && $exitExamresult['ExitExam']['result'] >= 50) {
                                $gradeForDocument = 'Pass (' . $exitExamresult['ExitExam']['result'] . '%)';
                                $ExitExam['exam_date'] = $exitExamresult['ExitExam']['exam_date'];
                                $ExitExam['result'] = $exitExamresult['ExitExam']['result'];
                                $ExitExam['result_formated'] = $gradeForDocument;
                            } elseif (!empty($exitExamresult['ExitExam']['result']) && $exitExamresult['ExitExam']['result'] < 50) {
                                $gradeForDocument = 'Fail (' . $exitExamresult['ExitExam']['result'] . '%)';
                                $ExitExam['exam_date'] = $exitExamresult['ExitExam']['exam_date'];
                                $ExitExam['result'] = $exitExamresult['ExitExam']['result'];
                                $ExitExam['result_formated'] = $gradeForDocument;
                            }
                        }
                    }
                }


                //Student profile
                $student_copy['student_detail']['Student'] = $student_detail['Student'];
                $student_copy['student_detail']['Student']['code'] = $code;
                $student_copy['student_detail']['Student']['Country'] = $student_detail['Country'];
                $student_copy['student_detail']['Student']['Region'] = $student_detail['Region'];
                $student_copy['student_detail']['Student']['AcceptedStudent'] = $student_detail['AcceptedStudent'];
                $student_copy['student_detail']['GraduationWork'] = (isset($student_detail['GraduationWork'][0]) ? $student_detail['GraduationWork'][0] : array());
                $student_copy['student_detail']['Curriculum'] = $student_detail['Curriculum'];
                $student_copy['student_detail']['University'] = $university_detail;
                $student_copy['student_detail']['TranscriptFooter'] = $transcript_footer_detail;
                $student_copy['student_detail']['College'] = $student_detail['College'];
                $student_copy['student_detail']['Department'] = $student_detail['Department'];
                $student_copy['student_detail']['Program'] = $student_detail['Program'];
                $student_copy['student_detail']['ProgramType'] = $student_detail['ProgramType'];
                $student_copy['student_detail']['HighSchoolEducationBackground'] = $student_detail['HighSchoolEducationBackground'];
                $student_copy['student_detail']['HigherEducationBackground'] = $student_detail['HigherEducationBackground'];
                $student_copy['student_detail']['EslceResult'] = $student_detail['EslceResult'];
                $student_copy['student_detail']['EheeceResult'] = $student_detail['EheeceResult'];
                $student_copy['student_detail']['EslceResult'] = $student_detail['EslceResult'];
                $student_copy['student_detail']['EheeceResult'] = $student_detail['EheeceResult'];
                $student_copy['student_detail']['GraduateList'] = $student_detail['GraduateList'];
                $student_copy['student_detail']['ExemptionList'] = $this->CourseRegistration->Student->CourseExemption->studentExemptedCourseList(
                    $student_id
                );

                $student_copy['student_detail']['ExitExam'] = $ExitExam;
                debug($student_copy['student_detail']['ExitExam']);

                if ($student_detail['GraduateList']['id'] != "") {
                    $student_copy['student_detail']['GraduationStatuse'] = ClassRegistry::init(
                        'GraduationStatus'
                    )->getStudentGraduationStatus($student_id);
                } else {
                    $student_copy['student_detail']['GraduationStatuse'] = null;
                }

                //debug($student_detail);exit();
                //Student grades
                //The first date the student either register or add

                $first_registration = $this->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id
                    ),
                    //'order' => array('CourseRegistration.created ASC'),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'ASC',
                        'CourseRegistration.semester' => 'ASC',
                        'CourseRegistration.id' => 'ASC'
                    )
                ));

                $first_add = $this->CourseAdd->find('first', array(
                    'conditions' => array(
                        'CourseAdd.student_id' => $student_id,
                        'CourseAdd.department_approval=1',
                        'CourseAdd.registrar_confirmation=1'
                    ),
                    //'order' => array('CourseAdd.created ASC'),
                    'order' => array(
                        'CourseAdd.academic_year' => 'ASC',
                        'CourseAdd.semester' => 'ASC',
                        'CourseAdd.id' => 'ASC'
                    ),
                ));

                //debug($first_registration);
                if (!empty($first_registration) && empty($first_add)) {
                    $first_ay_se['academic_year'] = $first_registration['CourseRegistration']['academic_year'];
                    $first_ay_se['semester'] = $first_registration['CourseRegistration']['semester'];
                } elseif (empty($first_registration) && !empty($first_add)) {
                    $first_ay_se['academic_year'] = $first_add['CourseAdd']['academic_year'];
                    $first_ay_se['semester'] = $first_add['CourseAdd']['semester'];
                } elseif (empty($first_registration) && empty($first_add)) {
                    $first_ay_se['academic_year'] = null;
                    $first_ay_se['semester'] = null;
                } elseif (substr($first_registration['CourseRegistration']['academic_year'], 0, 4) > substr(
                        $first_add['CourseAdd']['academic_year'],
                        0,
                        4
                    )) {
                    $first_ay_se['academic_year'] = $first_add['CourseAdd']['academic_year'];
                    $first_ay_se['semester'] = $first_add['CourseAdd']['semester'];
                } else {
                    $first_ay_se['academic_year'] = $first_registration['CourseRegistration']['academic_year'];
                    $first_ay_se['semester'] = $first_registration['CourseRegistration']['semester'];
                }

                //The last a/y and semster the student register or add
                $last_registration = $this->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id
                    ),
                    //'order' => array('CourseRegistration.created DESC'),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    )
                ));

                $last_add = $this->CourseAdd->find('first', array(
                    'conditions' => array(
                        'CourseAdd.student_id' => $student_id,
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation = 1'
                    ),
                    //'order' => array('CourseAdd.created DESC'),
                    'order' => array(
                        'CourseAdd.academic_year' => 'DESC',
                        'CourseAdd.semester' => 'DESC',
                        'CourseAdd.id' => 'DESC'
                    )
                ));

                if (!empty($last_registration) && empty($last_add)) {
                    $last_ay_se['academic_year'] = $last_registration['CourseRegistration']['academic_year'];
                    $last_ay_se['semester'] = $last_registration['CourseRegistration']['semester'];
                } elseif (empty($last_registration) && !empty($last_add)) {
                    $last_ay_se['academic_year'] = $last_add['CourseAdd']['academic_year'];
                    $last_ay_se['semester'] = $last_add['CourseAdd']['semester'];
                } elseif (empty($last_registration) && empty($last_add)) {
                    $last_ay_se['academic_year'] = null;
                    $last_ay_se['semester'] = null;
                } elseif (substr($last_registration['CourseRegistration']['academic_year'], 0, 4) < substr(
                        $last_add['CourseAdd']['academic_year'],
                        0,
                        4
                    )) {
                    $last_ay_se['academic_year'] = $last_add['CourseAdd']['academic_year'];
                    $last_ay_se['semester'] = $last_add['CourseAdd']['semester'];
                } else {
                    $last_ay_se['academic_year'] = $last_registration['CourseRegistration']['academic_year'];
                    $last_ay_se['semester'] = $last_registration['CourseRegistration']['semester'];
                }

                if ($first_ay_se['academic_year'] != null && $first_ay_se['semester'] != null) {
                    $next_ay_se = $this->CourseRegistration->Student->StudentExamStatus->getPreviousSemester(
                        $first_ay_se['academic_year'],
                        $first_ay_se['semester']
                    );
                    $student_copy['courses_taken'] = array();

                    do {
                        $next_ay_se = $this->CourseRegistration->Student->StudentExamStatus->getNextSemster(
                            $next_ay_se['academic_year'],
                            $next_ay_se['semester']
                        );

                        $index = count($student_copy['courses_taken']);

                        $student_copy['courses_taken'][$index]['academic_year'] = $next_ay_se['academic_year'];
                        $student_copy['courses_taken'][$index]['semester'] = $next_ay_se['semester'];
                        $student_copy['courses_taken'][$index]['readmitted'] = $this->CourseRegistration->Student->Readmission->isReadmitted(
                            $student_id,
                            $next_ay_se['academic_year'],
                            $next_ay_se['semester']
                        );


                        $exam_status = $this->CourseRegistration->Student->StudentExamStatus->find('first', array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $student_id,
                                'StudentExamStatus.academic_year' => $next_ay_se['academic_year'],
                                'StudentExamStatus.semester' => $next_ay_se['semester']
                            ),
                            'contain' => array('AcademicStatus'),
                            'order' => array(/* 'StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', */
                                'StudentExamStatus.created' => 'DESC'
                            )
                        ));

                        $student_copy['courses_taken'][$index]['status'] = (empty($exam_status) ? null : $exam_status);
                        $student_copy['courses_taken'][$index]['courses_and_grades'] = $this->getStudentCoursesAndFinalGrade(
                            $student_id,
                            $next_ay_se['academic_year'],
                            $next_ay_se['semester'],
                            0
                        );

                        if (empty($student_copy['courses_taken'][$index]['courses_and_grades'])) {
                            unset($student_copy['courses_taken'][$index]);
                        }
                    } while (!(strcasecmp(
                            $last_ay_se['academic_year'],
                            $next_ay_se['academic_year']
                        ) == 0 && strcasecmp($last_ay_se['semester'], $next_ay_se['semester']) == 0));
                }
                $student_copy_array[] = $student_copy;
            }
        }
        //debug($student_copy_array);
        return $student_copy_array;
    }

    /*** Returns all academic year and semester a student register and/or add. ***/
    function getListOfAyAndSemester($student_id = null, $upto_acadamic_year = null, $upto_first_semester = null)
    {

        //It excludes up-to a/y and semester if they are given.
        //Otherwise it will return the whole academic year and semester a student attends a course
        $ay_and_s_list = array();
        $next_ay_and_s = array();
        $academic_status_empty = array();

        $first_added = $this->CourseAdd->find('first', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation = 1'
            ),
            'recursive' => -1,
            //'order' => array('CourseAdd.created' => 'ASC'),
            'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
        ));

        $first_registered = $this->CourseRegistration->find('first', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
            ),
            'recursive' => -1,
            //'order' => array('CourseRegistration.created' => 'ASC'),
            'order' => array(
                'CourseRegistration.academic_year' => 'ASC',
                'CourseRegistration.semester' => 'ASC',
                'CourseRegistration.id' => 'ASC'
            )
        ));


        $academic_status_empty = $this->CourseAdd->Student->StudentExamStatus->find('first', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id,
            ),
            'recursive' => -1,
            //'order' => array('StudentExamStatus.created' => 'DESC')
            'order' => array(
                'StudentExamStatus.academic_year' => 'DESC',
                'StudentExamStatus.semester' => 'DESC',
                'StudentExamStatus.created' => 'DESC'
            )
        ));

        // introduced for regeneration bug when there is no status history in the system

        if (empty($academic_status_empty) && !empty($upto_acadamic_year) && !empty($upto_first_semester)) {
            $ay_and_s_list[0]['academic_year'] = $upto_acadamic_year;
            $ay_and_s_list[0]['semester'] = $upto_first_semester;
            return $ay_and_s_list;
        }

        $last_added = $this->CourseAdd->find('first', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation = 1'
            ),
            'recursive' => -1,
            //'order' => array('CourseAdd.created' => 'DESC'),
            'order' => array(
                'CourseAdd.academic_year' => 'DESC',
                'CourseAdd.semester' => 'DESC',
                'CourseAdd.id' => 'DESC'
            )
        ));

        $last_registered = $this->CourseRegistration->find('first', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
            ),
            'recursive' => -1,
            //'order' => array('CourseRegistration.created' => 'DESC'),
            'order' => array(
                'CourseRegistration.academic_year' => 'DESC',
                'CourseRegistration.semester' => 'DESC',
                'CourseRegistration.id' => 'DESC'
            )
        ));

        // If there is no registration and add (fresh student for the first time)
        if (empty($first_added) && empty($first_registered)) {
            return array();
            // If the course registration comes first, then we need to start the acdamic year and semester generation from the earliest possible time
        } elseif (
            isset($first_registered['CourseRegistration']) && !empty($first_registered['CourseRegistration']) &&
            (((!isset($first_added['CourseAdd']) || empty($first_added['CourseAdd']))) ||
                ((isset($first_added['CourseAdd']) && !empty($first_added['CourseAdd']) && $first_added['CourseAdd']['created'] > $first_registered['CourseRegistration']['created'])))
        ) {
            $next_ay_and_s['academic_year'] = $first_registered['CourseRegistration']['academic_year'];
            $next_ay_and_s['semester'] = $first_registered['CourseRegistration']['semester'];
            //debug($first_registered);
        } else {
            $next_ay_and_s['academic_year'] = $first_added['CourseAdd']['academic_year'];
            $next_ay_and_s['semester'] = $first_added['CourseAdd']['semester'];
        }

        if (
            isset($last_registered['CourseRegistration']) && !empty($last_registered['CourseRegistration']) &&
            (((!isset($last_added['CourseAdd']) || empty($last_added['CourseAdd']))) ||
                (isset($last_added['CourseAdd']) && !empty($last_added['CourseAdd']) && ($last_added['CourseAdd']['created'] < $last_registered['CourseRegistration']['created'] || $last_added['CourseAdd']['academic_year'] < $last_registered['CourseRegistration']['academic_year'])))
        ) {
            $last_ay = $last_registered['CourseRegistration']['academic_year'];
            $last_s = $last_registered['CourseRegistration']['semester'];
            // debug($last_ay);
        } else {
            $last_ay = $last_added['CourseAdd']['academic_year'];
            $last_s = $last_added['CourseAdd']['semester'];
            //debug($last_ay);
        }


        /////// ADDED BY NEWAY FOR TEMPORARY FRESHMAN FIRST SEMESTER BACKDATED GRADE ENTRY FIX /////////////////

        /* if (($student_id == 259844 || $student_id == 259846) && !empty($first_registered) && (!is_numeric($first_registered['CourseRegistration']['year_level_id']) || $first_registered['CourseRegistration']['year_level_id'] == 0)) {
            $ay_and_s_list[0]['academic_year'] = $first_registered['CourseRegistration']['academic_year'];
            $ay_and_s_list[0]['semester'] = $first_registered['CourseRegistration']['semester'];
            return $ay_and_s_list;
        } */

        /////// END ADDED BY NEWAY FOR TEMPORARY FRESHMAN FIRST SEMESTER BACKDATED GRADE ENTRY FIX /////////////////


        //If the student takes only one semester
        if ((strcasecmp($last_ay, $next_ay_and_s['academic_year']) == 0 && strcasecmp(
                $last_s,
                $next_ay_and_s['semester']
            ) == 0)) {
            $ay_and_s_list[0]['academic_year'] = $next_ay_and_s['academic_year'];
            $ay_and_s_list[0]['semester'] = $next_ay_and_s['semester'];
            return $ay_and_s_list;
        }

        $count = 1;

        while (!(($upto_acadamic_year != null && $upto_first_semester != null && strcasecmp(
                $upto_acadamic_year,
                $next_ay_and_s['academic_year']
            ) == 0 && strcasecmp($upto_first_semester, $next_ay_and_s['semester']) == 0))) {
            $count++;

            if ($count > 100) {
                return $ay_and_s_list;
            }

            $course_registered = $this->CourseRegistration->find('count', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.academic_year' => $next_ay_and_s['academic_year'],
                    'CourseRegistration.semester' => $next_ay_and_s['semester'],
                ),
                'recursive' => -1
            ));

            $course_added = $this->CourseAdd->find('count', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                    'CourseAdd.academic_year' => $next_ay_and_s['academic_year'],
                    'CourseAdd.semester' => $next_ay_and_s['semester'],
                ),
                'recursive' => -1
            ));

            if ($course_registered > 0 || $course_added > 0) {
                $index = count($ay_and_s_list);
                $ay_and_s_list[$index]['academic_year'] = $next_ay_and_s['academic_year'];
                $ay_and_s_list[$index]['semester'] = $next_ay_and_s['semester'];
            }

            if (strcasecmp($last_ay, $next_ay_and_s['academic_year']) == 0 && strcasecmp(
                    $last_s,
                    $next_ay_and_s['semester']
                ) == 0) {
                break;
            }

            $next_ay_and_s = $this->CourseRegistration->Student->StudentExamStatus->getNextSemster(
                $next_ay_and_s['academic_year'],
                $next_ay_and_s['semester']
            );
        }

        // withdrawlAfterRegistration must be enabled if there is no problem in the function, Mass Registration via section might register such students students evenif they withdraw and we will endup canceling NGs when the students return. (Neway)

        /* if (!empty($ay_and_s_list)) {
            foreach ($ay_and_s_list as $k => &$v) {
                $withdrawlAfterRegistration = $this->CourseRegistration->Student->Clearance->withDrawaAfterRegistration($student_id, $v['academic_year'], $v['semester']);
                if ($withdrawlAfterRegistration) {
                    unset($ay_and_s_list[$k]);
                }
            }
        } */

        return $ay_and_s_list;
    }

    function isRegistrationAddForFirstTime($id = null, $registration = 1, $include_equivalent = 1)
    {

        //To determine if a student registered more than once for the same course

        $course_id = null;
        $student_id = null;

        if ($registration == 1) {
            $regisration_detail = $this->CourseRegistration->find('first', array(
                'conditions' => array(
                    'CourseRegistration.id' => $id
                ),
                'contain' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'Course'
                    )
                )
            ));

            $course_id = (isset($regisration_detail['PublishedCourse']['Course']['id']) && !empty($regisration_detail['PublishedCourse']['Course']['id']) ? $regisration_detail['PublishedCourse']['Course']['id'] : null);
            $student_id = (isset($regisration_detail['CourseRegistration']['student_id']) && !empty($regisration_detail['CourseRegistration']['student_id']) ? $regisration_detail['CourseRegistration']['student_id'] : null);
            $studentDetail = (!empty($student_id) ? $this->CourseRegistration->Student->find(
                "first",
                array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
            ) : array());

            $student_department['Student'] = (isset($regisration_detail['Student']) && !empty($regisration_detail['Student']['id']) ? $regisration_detail['Student'] : array());
            $course_department['Course'] = (isset($regisration_detail['PublishedCourse']['Course']) && !empty($regisration_detail['PublishedCourse']['Course']['id']) ? $regisration_detail['PublishedCourse']['Course'] : array());
        } else {
            //debug($id);

            $add_detail = $this->CourseAdd->find('first', array(
                'conditions' => array(
                    'CourseAdd.id' => $id
                ),
                'contain' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'Course'
                    )
                )
            ));

            $course_id = (isset($add_detail['PublishedCourse']['Course']['id']) && !empty($add_detail['PublishedCourse']['Course']['id']) ? $add_detail['PublishedCourse']['Course']['id'] : null);
            $student_id = (isset($add_detail['CourseAdd']['student_id']) && !empty($add_detail['CourseAdd']['student_id']) ? $add_detail['CourseAdd']['student_id'] : null);
            $studentDetail = (!empty($student_id) ? $this->CourseAdd->Student->find(
                "first",
                array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
            ) : array());
            $student_department['Student'] = (isset($add_detail['Student']) && !empty($add_detail['Student']['id']) ? $add_detail['Student'] : array());
            $course_department['Course'] = (isset($add_detail['PublishedCourse']['Course']) && !empty($add_detail['PublishedCourse']['Course']['id']) ? $add_detail['PublishedCourse']['Course'] : array());
            //debug($course_id);
        }

        if (!empty($student_id) && !empty($course_id)) {
            //debug($course_id);

            $matching_courses = array();

            if ($include_equivalent == 1 && isset($student_department['Student']) && !empty($student_department['Student']['curriculum_id'])) {
                $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                    $course_id,
                    $student_department['Student']['curriculum_id']
                );
            }

            $matching_courses[$course_id] = $course_id;

            $register_and_add_freq = $this->getCourseFrequenceRegAdds($matching_courses, $student_id); //array();

            //If the student add or register once
            if (count($register_and_add_freq) <= 1) {
                return true;
            } else {
                //debug($register_and_add_freq);
                if ($registration == 1) {
                    if (isset($regisration_detail['PublishedCourse']['course_id']) && !empty($regisration_detail['PublishedCourse']['course_id'])) {
                        $rep = $this->repeatationLabeling(
                            $register_and_add_freq,
                            'register',
                            $id,
                            $studentDetail,
                            $regisration_detail['PublishedCourse']['course_id']
                        );

                        debug($rep);

                        if ($rep['repeated_old'] == false && $rep['repeated_new'] == true) {
                            return true;
                        }
                    }
                } else {
                    //debug($id);
                    //debug($register_and_add_freq);

                    if (isset($add_detail['PublishedCourse']['course_id']) && !empty($add_detail['PublishedCourse']['course_id'])) {
                        $rep = $this->repeatationLabeling(
                            $register_and_add_freq,
                            'add',
                            $id,
                            $studentDetail,
                            $add_detail['PublishedCourse']['course_id']
                        );

                        debug($rep);

                        if ($rep['repeated_old'] == false && $rep['repeated_new'] == true) {
                            return true;
                        }
                    }
                }
                //debug($register_and_add_freq);
            }
            debug($register_and_add_freq);
        }
        return false;
    }

    public function getDepartmentNonApprovedCoursesList(
        $department_college_id,
        $department = 1,
        $role_id = '',
        $current_acadamic_year = ''
    ) {

        //check to which department is assigned.
        $yearsInPast = Configure::read('ExamGrade.Approval.yearsInPast');
        $registrationAddMakeupIDs = array();
        $results = array();
        $ac_year = '%';

        $freshman_dept_or_college = 'college_id';

        if (isset($role_id) && !empty($role_id)) {
            if ($role_id == ROLE_DEPARTMENT) {
                $freshman_dept_or_college = 'given_by_department_id';
            } else {
                $freshman_dept_or_college = 'college_id';
            }
        }

        if (isset($current_acadamic_year) && !empty($current_acadamic_year)) {
            if (is_array($current_acadamic_year)) {
                $ac_year = "'" . implode("', '", $current_acadamic_year) . "'";
            } else {
                $ac_year = "'" . $current_acadamic_year . "'";
            }
        }

        if ($department == 1) {
            /* $resultsRegistration = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_registration_id
            ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)

            WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR))
            AND (
                (exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
                (exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
                (exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
            )
            AND exam_grades.course_registration_id IN (
                SELECT id FROM course_registrations WHERE published_course_id IN (
                    SELECT id  FROM published_courses WHERE academic_year IN ($ac_year) AND given_by_department_id = " . $department_college_id . "
                )
            )"); */


            $resultsRegistration = $this->query(
                "SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_registration_id
			) AS t2 ON (exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)

			INNER JOIN course_registrations AS course_registrations ON course_registrations.id = exam_grades.course_registration_id
			INNER JOIN students AS students ON students.id = course_registrations.student_id
			INNER JOIN published_courses AS published_courses ON published_courses.id = course_registrations.published_course_id

            WHERE students.graduated = 0 AND published_courses.academic_year IN ($ac_year) AND published_courses.given_by_department_id = $department_college_id
			AND (
				(exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
				(exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
				(exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
			)"
            );


            /* $resultsAdds = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, course_add_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_add_id
            ) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)

            WHERE (
                (exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
                (exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
                (exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
            ) AND exam_grades.course_add_id IN (
                SELECT id FROM course_adds WHERE published_course_id IN (
                    SELECT id FROM published_courses WHERE academic_year IN ($ac_year) AND given_by_department_id = " . $department_college_id . "
                )
            )"); */


            $resultsAdds = $this->query(
                "SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, course_add_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_add_id
			) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)

			INNER JOIN course_adds AS course_adds ON course_adds.id = exam_grades.course_add_id
			INNER JOIN students AS students ON students.id = course_adds.student_id
			INNER JOIN published_courses AS published_courses ON published_courses.id = course_adds.published_course_id

            WHERE students.graduated = 0 AND published_courses.academic_year IN ($ac_year) AND published_courses.given_by_department_id = $department_college_id
			AND (
				(exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
				(exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
				(exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
			)"
            );


            /* $resultsMakeup = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, makeup_exam_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY makeup_exam_id
            ) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)

            WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(), INTERVAL $yearsInPast YEAR))
            AND (
                (exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
                (exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
                (exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
            )
            AND exam_grades.makeup_exam_id IN (
                SELECT id FROM makeup_exams WHERE published_course_id IN (
                    SELECT id FROM published_courses WHERE academic_year IN ($ac_year) AND given_by_department_id = " . $department_college_id . "
                )
            )"); */


            $resultsMakeup = $this->query(
                "SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
            	SELECT id, course_registration_id, makeup_exam_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY makeup_exam_id
			) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)

			INNER JOIN makeup_exams AS makeup_exams ON makeup_exams.id = exam_grades.makeup_exam_id
			INNER JOIN students AS students ON students.id = makeup_exams.student_id
			INNER JOIN published_courses AS published_courses ON published_courses.id = makeup_exams.published_course_id

			WHERE students.graduated = 0 AND published_courses.academic_year IN ($ac_year) AND published_courses.given_by_department_id = $department_college_id
			AND (
				(exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
				(exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
				(exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
			)"
            );

            //$results = $resultsMakeup + $resultsAdds + $resultsRegistration;

            debug($resultsRegistration);
            debug($resultsMakeup);
            debug($resultsAdds);

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        } else {
            /* $resultsRegistration = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, MAX(created) AS latest, department_approval,registrar_approval
                FROM exam_grades WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_registration_id
            ) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)

            WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(), INTERVAL $yearsInPast YEAR))
            AND  (
                (exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
                (exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1  and exam_grades.department_reply = 1) OR
                (exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1  and exam_grades.department_reply = 1)
            )
            AND exam_grades.course_registration_id IN (
                SELECT id FROM course_registrations WHERE published_course_id IN (
                    SELECT id FROM published_courses WHERE department_id IS NULL AND academic_year IN ($ac_year) AND college_id = " . $department_college_id . "
                )
            )"); */


            $resultsRegistration = $this->query(
                "SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, MAX(created) AS latest, department_approval,registrar_approval
                FROM exam_grades WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_registration_id
			) AS t2 ON ( exam_grades.id = t2.id AND exam_grades.course_registration_id = t2.course_registration_id AND exam_grades.created = t2.latest)

			INNER JOIN course_registrations AS course_registrations ON course_registrations.id = exam_grades.course_registration_id
			INNER JOIN students AS students ON students.id = course_registrations.student_id
			INNER JOIN published_courses AS published_courses ON published_courses.id = course_registrations.published_course_id

            WHERE students.graduated = 0 AND published_courses.department_id IS NULL AND published_courses.academic_year IN ($ac_year) AND published_courses.$freshman_dept_or_college = $department_college_id
			AND (
				(exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
				(exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1  and exam_grades.department_reply = 1) OR
				(exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1  and exam_grades.department_reply = 1)
			)"
            );


            /* $resultsAdds = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, course_add_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_add_id
            ) AS t2 ON ( exam_grades.id = t2.id AND  exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)

            WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(), INTERVAL $yearsInPast YEAR))
            AND (
                (exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
                (exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
                (exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
            )
            AND exam_grades.course_add_id IN (
                SELECT id FROM course_adds WHERE published_course_id IN (
                    SELECT id  FROM published_courses WHERE department_id IS NULL AND academic_year IN ($ac_year) AND college_id = " . $department_college_id . "
                )
            )"); */


            $resultsAdds = $this->query(
                "SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, course_add_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
				WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY course_add_id
			) AS t2 ON ( exam_grades.id = t2.id AND  exam_grades.course_add_id = t2.course_add_id AND exam_grades.created = t2.latest)

			INNER JOIN course_adds AS course_adds ON course_adds.id = exam_grades.course_add_id
			INNER JOIN students AS students ON students.id = course_adds.student_id
			INNER JOIN published_courses AS published_courses ON published_courses.id = course_adds.published_course_id

            WHERE students.graduated = 0 AND published_courses.department_id IS NULL AND published_courses.academic_year IN ($ac_year) AND published_courses.$freshman_dept_or_college = $department_college_id
			AND (
				(exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
				(exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
				(exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
			)"
            );


            /* $resultsMakeup = $this->query("SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, makeup_exam_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY makeup_exam_id
            ) AS t2 ON (exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)

            WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(), INTERVAL $yearsInPast YEAR))
            AND (
                (exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
                (exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
                (exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
            )
            AND exam_grades.makeup_exam_id IN (
                SELECT id  FROM makeup_exams WHERE published_course_id IN (
                    SELECT id FROM published_courses WHERE department_id IS NULL AND academic_year IN ($ac_year) AND college_id = " . $department_college_id . "
                )
            )"); */


            $resultsMakeup = $this->query(
                "SELECT exam_grades.id, exam_grades.course_registration_id, exam_grades.registrar_approval, exam_grades.department_approval, exam_grades.course_add_id, exam_grades.makeup_exam_id
            FROM exam_grades exam_grades
            INNER JOIN (
                SELECT id, course_registration_id, makeup_exam_id, MAX(created) AS latest, department_approval, registrar_approval FROM exam_grades
                WHERE registrar_approval = -1 OR department_approval IS NULL GROUP BY makeup_exam_id
			) AS t2 ON (exam_grades.id = t2.id AND exam_grades.makeup_exam_id = t2.makeup_exam_id AND exam_grades.created = t2.latest)

			INNER JOIN makeup_exams AS makeup_exams ON makeup_exams.id = exam_grades.makeup_exam_id
			INNER JOIN students AS students ON students.id = makeup_exams.student_id
			INNER JOIN published_courses AS published_courses ON published_courses.id = makeup_exams.published_course_id

        	WHERE students.graduated = 0 AND published_courses.department_id IS NULL AND published_courses.academic_year IN ($ac_year) AND published_courses.$freshman_dept_or_college = $department_college_id
			AND (
				(exam_grades.department_approval is null and exam_grades.registrar_approval is null) OR
				(exam_grades.department_approval = 1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1) OR
				(exam_grades.department_approval = -1 and exam_grades.registrar_approval = -1 and exam_grades.department_reply = 1)
			)"
            );

            //$results = $resultsMakeup + $resultsAdds + $resultsRegistration;

            debug($resultsRegistration);
            debug($resultsMakeup);
            debug($resultsAdds);

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $k => $value) {
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
        // debug($registrationAddMakeupIDs);

        debug($freshman_dept_or_college);

        $publication_ids = array();
        $publication_ids_rejected = array();

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publication_ids_register = $this->CourseRegistration->find('list', array(
                'fields' => array('CourseRegistration.published_course_id'),
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['register'])
            ));

            $publication_ids = $publication_ids + $publication_ids_register;
        }

        if (!empty($registrationAddMakeupIDs['registerRejected'])) {
            $publication_ids_register_rejected = $this->CourseRegistration->find('list', array(
                'fields' => array('CourseRegistration.published_course_id'),
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['registerRejected'])
            ));

            $publication_ids_rejected = $publication_ids_rejected + $publication_ids_register_rejected;
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publication_ids_add = $this->CourseAdd->find('list', array(
                'fields' => array('CourseAdd.published_course_id'),
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['add'])
            ));

            $publication_ids += $publication_ids_add;
        }

        if (!empty($registrationAddMakeupIDs['addRejected'])) {
            $publication_ids_add_rejected = $this->CourseAdd->find('list', array(
                'fields' => array('CourseAdd.published_course_id'),
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['addRejected'])
            ));

            $publication_ids_rejected += $publication_ids_add_rejected;
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'fields' => array('MakeupExam.published_course_id'),
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeup'])
            ));

            $publication_ids += $publication_ids_makeup;
        }

        if (!empty($registrationAddMakeupIDs['makeupRejected'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'fields' => array('MakeupExam.published_course_id'),
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeupRejected'])
            ));

            $publication_ids_rejected += $publication_ids_makeup;
        }

        if (isset($publication_ids) && !empty($publication_ids)) {
            $distinct_publication_ids = array_unique($publication_ids);
        }

        if (isset($publication_ids_rejected) && !empty($publication_ids_rejected)) {
            $distinct_publication_ids_rejected = array_unique($publication_ids_rejected);
        }

        if (isset($publication_ids) && !empty($distinct_publication_ids)) {
            if ($department == 1) {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                        //'PublishedCourse.given_by_department_id' => $department_college_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'GivenByDepartment' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position',
                                'Title',
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'type_credit'),
                        )
                    )
                ));

                $published_courses_rejected = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids_rejected,
                        //'PublishedCourse.given_by_department_id' => $department_college_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'GivenByDepartment' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position',
                                'Title',
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'type_credit'),
                        )
                    )
                ));
            } else {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                        'PublishedCourse.' . $freshman_dept_or_college . '' => $department_college_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'GivenByDepartment' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position',
                                'Title',
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'type_credit'),
                        )
                    )
                ));

                $published_courses_rejected = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids_rejected,
                        'PublishedCourse.' . $freshman_dept_or_college . '' => $department_college_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position',
                                'Title',
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'type_credit'),
                        )
                    )
                ));
            }
        }

        if (isset($published_courses)) {
            $courses_for_approval['from_instructor'] = $published_courses;
        } else {
            $courses_for_approval['from_instructor'] = array();
        }

        if (isset($published_courses_rejected)) {
            $courses_for_approval['from_registrars'] = $published_courses_rejected;
        } else {
            $courses_for_approval['from_registrars'] = array();
        }

        return $courses_for_approval;
    }

    function getRegistrarNonApprovedCoursesList(
        $department_ids = null,
        $college_ids = null,
        $program_id = null,
        $program_type_ids = null
    ) {

        //check to which department is assigned.
        $yearsInPast = Configure::read('ExamGrade.Approval.yearsInPast');
        $registrationAddMakeupIDs = array();
        $published_courses = array();
        $results = array();
        $resultsRegistration = array();
        $resultsMakeup = array();
        $resultsAdds = array();
        $queryPs = " id is not null ";
        $queryPss = " published_courses.id is not null ";

        if (!empty($program_id)) {
            $prg_ids = implode(', ', $program_id);
            $queryPs .= " and program_id in ($prg_ids) ";
            $queryPss .= " and published_courses.program_id in ($prg_ids)";
        }

        if (!empty($program_type_id)) {
            $prg_type_ids = implode(', ', $program_type_id);
            $queryPs .= " and program_type_id IN ($prg_type_ids)";
            $queryPss .= " and published_courses.program_type_id in ($prg_type_ids) ";
        }

        if (!empty($department_ids)) {
            $dept_ids = implode(', ', $department_ids);
            $queryPs .= " and department_id in ($dept_ids)";
            $queryPss .= " and published_courses.department_id in ($dept_ids) ";
        }

        if (!empty($college_ids) && empty($department_ids)) {
            $college_ids = implode(', ', $college_ids);
            $queryPs .= " and college_id in ($college_ids) and department_id is null ";
            $queryPss .= " and published_courses.college_id in ($college_ids) and department_id is null ";
        }

        debug($queryPs);
        debug($queryPss);

        if (!empty($queryPs) && !empty($queryPss)) {
            $resultsRegistrationNR = $this->query(
                "SELECT DISTINCT exam_grades. * FROM exam_grades
			INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
			INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
			WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR))
			AND exam_grades.registrar_approval IS NULL AND exam_grades.department_approval = 1 AND $queryPss
			AND exam_grades.course_registration_id NOT IN (
				SELECT exam_grades.course_registration_id FROM exam_grades
				WHERE exam_grades.registrar_approval = 1 AND exam_grades.department_approval = 1 AND exam_grades.course_registration_id IS NOT NULL
				GROUP BY exam_grades.course_registration_id ORDER BY exam_grades.id DESC
			)"
            );

            $resultsRegistrationR = $this->query(
                "SELECT DISTINCT exam_grades. * FROM exam_grades
			INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
			INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
			WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR))
			AND exam_grades.registrar_approval is null AND exam_grades.department_reply = 1 AND $queryPss
			AND exam_grades.course_registration_id NOT IN (
				SELECT exam_grades.course_registration_id FROM exam_grades
				WHERE exam_grades.registrar_approval = 1 AND exam_grades.department_approval = 1 AND exam_grades.course_registration_id IS NOT NULL
				GROUP BY exam_grades.course_registration_id ORDER BY exam_grades.id DESC
			)"
            );

            $resultsRegistration = array_merge($resultsRegistrationNR, $resultsRegistrationR);

            $resultsAddsNR = $this->query(
                "SELECT DISTINCT exam_grades. * FROM exam_grades
			INNER JOIN course_adds course_adds ON exam_grades.course_add_id = course_adds.id
			INNER JOIN published_courses published_courses ON course_adds.published_course_id = published_courses.id
			WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR))
			AND exam_grades.registrar_approval IS NULL AND exam_grades.department_approval = 1 AND $queryPss
			AND exam_grades.course_add_id NOT IN (
				SELECT exam_grades.course_add_id FROM exam_grades
				WHERE exam_grades.registrar_approval = 1 AND exam_grades.department_approval = 1 AND exam_grades.course_add_id IS NOT NULL
				GROUP BY exam_grades.course_add_id ORDER BY exam_grades.id DESC
			)"
            );

            $resultsAddsR = $this->query(
                "SELECT DISTINCT exam_grades. * FROM exam_grades
			INNER JOIN course_adds course_adds ON exam_grades.course_add_id = course_adds.id
			INNER JOIN published_courses published_courses ON course_adds.published_course_id = published_courses.id
			WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR))
			AND exam_grades.registrar_approval is null AND exam_grades.department_reply = 1 AND $queryPss
			AND exam_grades.course_add_id NOT IN (
				SELECT exam_grades.course_add_id FROM exam_grades
				WHERE exam_grades.registrar_approval = 1 AND exam_grades.department_approval = 1 AND exam_grades.course_add_id IS NOT NULL
				GROUP BY exam_grades.course_add_id ORDER BY exam_grades.id DESC
			)"
            );

            $resultsAdds = array_merge($resultsAddsNR, $resultsAddsR);

            $resultsMakeupNR = $this->query(
                "SELECT DISTINCT exam_grades. * FROM exam_grades
			INNER JOIN makeup_exams makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
			INNER JOIN published_courses published_courses ON makeup_exams.published_course_id = published_courses.id
			WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR)) AND exam_grades.registrar_approval IS NULL
			AND exam_grades.department_approval = 1 AND $queryPss
			AND exam_grades.makeup_exam_id NOT IN (
				SELECT exam_grades.makeup_exam_id FROM exam_grades
				WHERE exam_grades.registrar_approval = 1 AND exam_grades.department_approval = 1 AND exam_grades.makeup_exam_id IS NOT NULL
				GROUP BY exam_grades.makeup_exam_id ORDER BY exam_grades.id DESC
			)"
            );

            $resultsMakeupR = $this->query(
                "SELECT DISTINCT exam_grades. * FROM exam_grades
			INNER JOIN makeup_exams makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
			INNER JOIN published_courses published_courses ON makeup_exams.published_course_id = published_courses.id
			WHERE (DATE(exam_grades.created) > DATE_SUB(curdate(),INTERVAL $yearsInPast YEAR)) AND exam_grades.registrar_approval is null
			AND exam_grades.department_reply = 1  AND $queryPss
			AND exam_grades.makeup_exam_id NOT IN (
				SELECT exam_grades.makeup_exam_id FROM exam_grades
				WHERE exam_grades.registrar_approval = 1 AND exam_grades.department_approval = 1 AND exam_grades.makeup_exam_id IS NOT NULL
				GROUP BY exam_grades.makeup_exam_id  ORDER BY exam_grades.id DESC
			)"
            );

            $resultsMakeup = array_merge($resultsMakeupNR, $resultsMakeupR);

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }


        if (!empty($results)) {
            foreach ($results as $k => $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        // debug($registrationAddMakeupIDs);
        $publication_ids = array();
        if (!empty($registrationAddMakeupIDs['register'])) {
            $publication_ids_register = $this->CourseRegistration->find('list', array(
                'fields' => array('CourseRegistration.published_course_id'),
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['register'])
            ));

            $publication_ids = $publication_ids + $publication_ids_register;
        }


        if (!empty($registrationAddMakeupIDs['add'])) {
            $publication_ids_add = $this->CourseAdd->find('list', array(
                'fields' => array('CourseAdd.published_course_id'),
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['add'])
            ));

            $publication_ids += $publication_ids_add;
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'fields' => array('MakeupExam.published_course_id'),
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeup'])
            ));

            $publication_ids += $publication_ids_makeup;
        }

        if (isset($publication_ids) && !empty($publication_ids)) {
            $distinct_publication_ids = array_unique($publication_ids);
        }

        if (isset($publication_ids) && !empty($distinct_publication_ids)) {
            if (!empty($college_ids) && empty($department_ids)) {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                        'PublishedCourse.college_id' => $college_ids,
                        'PublishedCourse.program_type_id' => $program_type_ids,
                        'PublishedCourse.program_id' => $program_id,
                        'PublishedCourse.department_id is null'
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position',
                                'Title',
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'type_credit'),
                        )
                    ),
                    'order' => array('PublishedCourse.academic_year DESC', 'PublishedCourse.semester DESC')
                ));
            } elseif (!empty($department_ids)) {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                        'PublishedCourse.program_type_id' => $program_type_ids,
                        'PublishedCourse.program_id' => $program_id,
                        'PublishedCourse.department_id' => $department_ids
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position',
                                'Title',
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'type_credit'),
                        )
                    ),
                    'order' => array('PublishedCourse.academic_year DESC', 'PublishedCourse.semester DESC')
                ));
            }
        }

        return $published_courses;
    }

    function getRegistrarNonApprovedCoursesList2(
        $department_ids = null,
        $college_ids = null,
        $acadamic_year = '',
        $semester = '',
        $program_id = null,
        $program_type_id = null,
        $acy_ranges = array()
    ) {

        //check to which department is assigned.
        $registrationAddMakeupIDs = array();

        $queryPs = " id is not null ";
        $queryPss = " published_courses.id is not null ";

        if (isset($acy_ranges) && !empty($acy_ranges)) {
            $acy_ranges_by_coma_quoted = "'" . implode("', '", $acy_ranges) . "'";
            $queryPs .= ' and published_courses.academic_year IN (' . $acy_ranges_by_coma_quoted . ') ';
            $queryPss .= ' and published_courses.academic_year IN (' . $acy_ranges_by_coma_quoted . ') ';
        }

        if (!empty($acadamic_year)) {
            $queryPs .= " and published_courses.academic_year LIKE '" . $acadamic_year . "' ";
            $queryPss .= " and published_courses.academic_year LIKE '" . $acadamic_year . "' ";
        }

        if (!empty($semester)) {
            $queryPs .= " and published_courses.semester LIKE '" . $semester . "' ";
            $queryPss .= " and published_courses.semester LIKE '" . $semester . "' ";
        }

        if (!empty($program_id)) {
            $prg_ids = implode(', ', $program_id);
            $queryPs .= " and program_id in ($prg_ids) ";
            $queryPss .= " and published_courses.program_id in ($prg_ids)";
        }

        if (!empty($program_type_id)) {
            $prg_type_ids = implode(', ', $program_type_id);
            $queryPs .= " and program_type_id IN ($prg_type_ids)";
            $queryPss .= " and published_courses.program_type_id in ($prg_type_ids) ";
        }

        if (!empty($department_ids)) {
            $dept_ids = implode(', ', $department_ids);
            $queryPs .= " and department_id in ($dept_ids)";
            $queryPss .= " and published_courses.department_id in ($dept_ids) ";
        }

        if (!empty($college_ids) && empty($department_ids)) {
            $college_ids = implode(', ', $college_ids);
            $queryPs .= " and college_id in ($college_ids) and department_id is null ";
            $queryPss .= " and published_courses.college_id in ($college_ids) and published_courses.department_id is null ";
        }

        debug($queryPs);
        debug($queryPss);

        if (!empty($queryPs) && !empty($queryPss)) {
            $resultsRegistration = $this->query(
                "SELECT exam_grades.* FROM exam_grades
			INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
			INNER JOIN students students ON course_registrations.student_id = students.id
			INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
			WHERE students.graduated = 0 AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = -1)) AND exam_grades.registrar_approval IS NULL AND $queryPss GROUP BY exam_grades.course_registration_id ORDER BY exam_grades.id DESC"
            );

            if (!empty($resultsRegistration)) {
                foreach ($resultsRegistration as $key => $value) {
                    //debug($value);
                    if ($this->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ExamGrade.course_registration_id' => $value['exam_grades']['course_registration_id'],
                                'ExamGrade.registrar_approval' => 1
                            )
                        )
                    )) {
                        unset($resultsRegistration[$key]);
                    }
                }
            }

            //debug($resultsRegistration);


            $resultsAdds = $this->query(
                "SELECT exam_grades.* FROM exam_grades
			INNER JOIN course_adds course_adds ON exam_grades.course_add_id = course_adds.id
			INNER JOIN students students ON course_adds.student_id = students.id
			INNER JOIN published_courses published_courses ON course_adds.published_course_id = published_courses.id
			WHERE students.graduated = 0 AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1)) AND exam_grades.registrar_approval IS NULL AND $queryPss GROUP BY exam_grades.course_add_id ORDER BY exam_grades.id DESC"
            );

            if (!empty($resultsAdds)) {
                foreach ($resultsAdds as $key => $value) {
                    //debug($value);
                    if ($this->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ExamGrade.course_add_id' => $value['exam_grades']['course_add_id'],
                                'ExamGrade.registrar_approval' => 1
                            )
                        )
                    )) {
                        unset($resultsAdds[$key]);
                    }
                }
            }

            //debug($resultsAdds);

            $resultsMakeup = $this->query(
                "SELECT exam_grades.* FROM exam_grades
			INNER JOIN makeup_exams makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
			INNER JOIN students students ON makeup_exams.student_id = students.id
			INNER JOIN published_courses published_courses ON makeup_exams.published_course_id = published_courses.id
			WHERE students.graduated = 0 AND (exam_grades.department_approval = 1 OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1)) AND exam_grades.registrar_approval IS NULL AND $queryPss GROUP BY exam_grades.makeup_exam_id ORDER BY exam_grades.id DESC"
            );

            if (!empty($resultsMakeup)) {
                foreach ($resultsMakeup as $key => $value) {
                    //debug($value);
                    if ($this->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ExamGrade.makeup_exam_id' => $value['exam_grades']['makeup_exam_id'],
                                'ExamGrade.registrar_approval' => 1
                            )
                        )
                    )) {
                        unset($resultsMakeup[$key]);
                    }
                }
            }

            //debug($resultsMakeup);

            $results = array_merge($resultsRegistration, $resultsAdds, $resultsMakeup);
        }


        if (isset($results) && !empty($results)) {
            foreach ($results as $k => $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        $publication_ids = array();

        if (isset($registrationAddMakeupIDs['register']) && !empty($registrationAddMakeupIDs['register'])) {
            $publication_ids_register = $this->CourseRegistration->find('list', array(
                'fields' => array('CourseRegistration.published_course_id'),
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['register'])
            ));

            $publication_ids = $publication_ids + $publication_ids_register;
        }


        if (isset($registrationAddMakeupIDs['add']) && !empty($registrationAddMakeupIDs['add'])) {
            $publication_ids_add = $this->CourseAdd->find('list', array(
                'fields' => array('CourseAdd.published_course_id'),
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['add'])
            ));
            $publication_ids += $publication_ids_add;
        }

        if (isset($registrationAddMakeupIDs['makeup']) && !empty($registrationAddMakeupIDs['makeup'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'fields' => array('MakeupExam.published_course_id'),
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeup'])
            ));

            $publication_ids += $publication_ids_makeup;
        }

        if (isset($publication_ids) && !empty($publication_ids)) {
            $distinct_publication_ids = array_unique($publication_ids);
        }

        $published_courses = array();

        if (isset($publication_ids) && !empty($distinct_publication_ids)) {
            if (!empty($college_ids) && empty($department_ids)) {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name', 'type'),
                        'GivenByDepartment' => array('id', 'name', 'type'),
                        'College' => array('id', 'name', 'type'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                                'User' => array('id', 'username', 'email', 'active', 'email_verified'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        )
                    ),
                    'order' => array('PublishedCourse.academic_year DESC', 'PublishedCourse.semester DESC')
                ));
            } elseif (!empty($department_ids)) {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name', 'type'),
                        'GivenByDepartment' => array('id', 'name', 'type'),
                        'College' => array('id', 'name', 'type'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                                'User' => array('id', 'username', 'email', 'active', 'email_verified'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        )
                    ),
                    'order' => array('PublishedCourse.academic_year DESC', 'PublishedCourse.semester DESC')
                ));
            }
        }

        return $published_courses;
    }

    //It returns list of courses from each department and freshman program that the registrar is supposed to confirm the grade submission

    function getRegistrarNonApprovedPublishedCourseList(
        $department_ids = null,
        $college_ids = null,
        $semester,
        $program_id,
        $program_type_id,
        $academic_year,
        $year_level_id = null
    ) {

        //check to which department is assigned.
        $registrationAddMakeupIDs = array();
        $results = array();

        if (!empty($department_ids)) {
            $dept_ids = implode(', ', $department_ids);

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
					WHERE department_id IN ($dept_ids) and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $program_id
                ) . ")  and program_type_id in (" . implode(
                    ', ',
                    $program_type_id
                ) . ") and academic_year='" . $academic_year . "'
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
					WHERE department_id IN ($dept_ids) and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $program_id
                ) . ")  and program_type_id in (" . implode(
                    ', ',
                    $program_type_id
                ) . ") and academic_year='" . $academic_year . "'
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
					WHERE department_id IN ($dept_ids) and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $program_id
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $program_type_id
                ) . ") and academic_year='" . $academic_year . "'
				)
			) "
            );

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($college_ids)) {
            $college_ids = implode(', ', $college_ids);

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
					WHERE college_id IN ($college_ids) and department_id is null and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $program_id
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $program_type_id
                ) . ") and academic_year='" . $academic_year . "'
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
					WHERE college_id IN ($college_ids) and department_id is null and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $program_id
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $program_type_id
                ) . ") and academic_year='" . $academic_year . "'
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
					WHERE college_id IN ($college_ids) and department_id is null and semester='" . $semester . "' and program_id in (" . implode(
                    ', ',
                    $program_id
                ) . ") and program_type_id in (" . implode(
                    ', ',
                    $program_type_id
                ) . ") and academic_year='" . $academic_year . "'
				)
			) "
            );

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $k => $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }
        // debug($registrationAddMakeupIDs);

        $publication_ids = array();
        if (!empty($registrationAddMakeupIDs['register'])) {
            $publication_ids_register = $this->CourseRegistration->find('list', array(
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['register']),
                'fields' => array('CourseRegistration.published_course_id')
            ));
            $publication_ids = $publication_ids + $publication_ids_register;
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publication_ids_add = $this->CourseAdd->find('list', array(
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['add']),
                'fields' => array('CourseAdd.published_course_id')
            ));
            $publication_ids += $publication_ids_add;
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeup']),
                'fields' => array('MakeupExam.published_course_id')
            ));
            $publication_ids += $publication_ids_makeup;
        }

        if (isset($publication_ids) && !empty($publication_ids)) {
            $distinct_publication_ids = array_unique($publication_ids);
        }

        if (isset($publication_ids) && !empty($distinct_publication_ids)) {
            $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                'conditions' => array(
                    'PublishedCourse.id' => $distinct_publication_ids
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'Section' => array('id', 'name'),
                    'Department' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'CourseInstructorAssignment' => array(
                        'fields' => array('id', 'published_course_id', 'staff_id'),
                        'Staff' => array(
                            'fields' => array('id', 'full_name', 'user_id'),
                            'Position',
                            'Title',
                        ),
                        'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                    ),
                    'Course' => array(
                        'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                        'Curriculum' => array('id', 'type_credit'),
                    )
                )
            ));
        }

        return $published_courses;
    }

    function getRejectedOrNonApprovedPublishedCourseList(
        $department_college_id,
        $department = 1,
        $acadamic_year = '',
        $semester = '',
        $program_ids = array(),
        $program_type_ids = array()/* , $year_level_ids = array() */,
        $acy_ranges = array()
    ) {

        $registrationAddMakeupIDs = array();
        $results = array();

        //check to which department /college course is assigned.
        if (!empty($department_college_id) && $department == 1) {
            $queryPss = " published_courses.id is not null and (published_courses.year_level_id is not null or published_courses.year_level_id != 0 or published_courses.year_level_id != '') ";
            $queryPss .= " and published_courses.given_by_department_id = $department_college_id ";
        } else {
            $queryPss = " published_courses.id is not null and (published_courses.year_level_id is null or published_courses.year_level_id = 0 or published_courses.year_level_id = '') ";
            $queryPss .= " and published_courses.program_id = 1 and published_courses.program_type_id = 1 and department_id is null and published_courses.given_by_department_id = $department_college_id ";
        }

        if (isset($acy_ranges) && !empty($acy_ranges)) {
            $acy_ranges_by_coma_quoted = "'" . implode("', '", $acy_ranges) . "'";
            $queryPss .= ' and published_courses.academic_year IN (' . $acy_ranges_by_coma_quoted . ') ';
        }

        if (!empty($acadamic_year)) {
            $queryPss .= " and published_courses.academic_year LIKE '" . $acadamic_year . "' ";
        }

        if (!empty($semester)) {
            $queryPss .= " and published_courses.semester LIKE '" . $semester . "' ";
        }

        if (!empty($program_ids)) {
            $queryPss .= " and published_courses.program_id IN ('" . implode(', ', $program_ids) . "') ";
        }

        if (!empty($program_type_ids)) {
            $queryPss .= " and published_courses.program_type_id IN ('" . implode(', ', $program_type_ids) . "') ";
        }

        /* if (!empty($year_level_ids)) {
            $queryPss .= " and published_courses.year_level_id IN ('" . implode(', ', $year_level_ids). "') ";
        } */

        debug($queryPss);

        //$minimum_date_of_published_courses = $this->query("SELECT published_courses.created FROM published_courses WHERE $queryPss ORDER BY published_courses.created ASC LIMIT 1");
        //debug($minimum_date_of_published_courses[0]['published_courses']['created']);
        //$first_published_for_dept = $minimum_date_of_published_courses[0]['published_courses']['created'];

        /* $resultsRegistrationcheck = $this->query("SELECT exam_grades.* FROM exam_grades
        INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
        INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
        WHERE exam_grades.department_approval IS NOT NULL AND $queryPss ");
        debug($resultsRegistrationcheck); */

        if (!empty($queryPss)) {
            $resultsRegistrationNR = $this->query(
                "SELECT DISTINCT exam_grades.* FROM exam_grades
			INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
			INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
			WHERE exam_grades.department_approval IS NULL AND $queryPss
			AND exam_grades.course_registration_id NOT IN (
				SELECT exam_grades.course_registration_id
				FROM exam_grades
				WHERE exam_grades.registrar_approval = 1
				AND exam_grades.department_approval = 1
				AND exam_grades.course_registration_id IS NOT NULL
				GROUP BY exam_grades.course_registration_id
				ORDER BY exam_grades.id DESC
			) "
            );

            $resultsRegistrationR = $this->query(
                "SELECT DISTINCT exam_grades.* FROM exam_grades
			INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
			INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
			WHERE exam_grades.registrar_approval = -1 AND exam_grades.department_approval = 1 AND $queryPss
			AND exam_grades.course_registration_id NOT IN (
				SELECT exam_grades.course_registration_id
				FROM exam_grades
				WHERE exam_grades.registrar_approval = 1
				AND exam_grades.department_approval = 1
				AND exam_grades.course_registration_id IS NOT NULL
				GROUP BY exam_grades.course_registration_id
				ORDER BY exam_grades.id DESC
			) "
            );

            $resultsRegistration = array_merge($resultsRegistrationNR, $resultsRegistrationR);
            debug($resultsRegistration);

            $resultsAddsNR = $this->query(
                "SELECT DISTINCT exam_grades.* FROM exam_grades
			INNER JOIN course_adds course_adds ON exam_grades.course_add_id = course_adds.id
			INNER JOIN published_courses published_courses ON course_adds.published_course_id = published_courses.id
			WHERE  exam_grades.department_approval IS NULL AND $queryPss
			AND exam_grades.course_add_id NOT IN (
				SELECT exam_grades.course_add_id
				FROM exam_grades
				WHERE exam_grades.registrar_approval = 1
				AND exam_grades.department_approval = 1
				AND exam_grades.course_add_id IS NOT NULL
				GROUP BY exam_grades.course_add_id
				ORDER BY exam_grades.id DESC
			) "
            );

            $resultsAddsR = $this->query(
                "SELECT DISTINCT exam_grades.* FROM exam_grades
			INNER JOIN course_adds course_adds ON exam_grades.course_add_id = course_adds.id
			INNER JOIN published_courses published_courses ON course_adds.published_course_id = published_courses.id
			WHERE exam_grades.registrar_approval = -1 AND exam_grades.department_approval = 1 AND $queryPss
			AND exam_grades.course_add_id NOT IN (
				SELECT exam_grades.course_add_id
				FROM exam_grades
				WHERE exam_grades.registrar_approval = 1
				AND exam_grades.department_approval = 1
				AND exam_grades.course_add_id IS NOT NULL
				GROUP BY exam_grades.course_add_id
				ORDER BY exam_grades.id DESC
			) "
            );

            $resultsAdds = array_merge($resultsAddsNR, $resultsAddsR);

            debug($resultsAdds);

            $resultsMakeupNR = $this->query(
                "SELECT DISTINCT exam_grades.* FROM exam_grades
			INNER JOIN makeup_exams makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
			INNER JOIN published_courses published_courses ON makeup_exams.published_course_id = published_courses.id
			WHERE exam_grades.department_approval IS NULL AND $queryPss
			AND exam_grades.makeup_exam_id NOT IN (
				SELECT exam_grades.makeup_exam_id
				FROM exam_grades
				WHERE exam_grades.registrar_approval = 1
				AND exam_grades.department_approval = 1
				AND exam_grades.makeup_exam_id IS NOT NULL
				GROUP BY exam_grades.makeup_exam_id
				ORDER BY exam_grades.id DESC
			) "
            );

            $resultsMakeupR = $this->query(
                "SELECT DISTINCT exam_grades.* FROM exam_grades
			INNER JOIN makeup_exams makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
			INNER JOIN published_courses published_courses ON makeup_exams.published_course_id = published_courses.id
			WHERE exam_grades.registrar_approval = -1 AND exam_grades.department_approval = 1 AND $queryPss
			AND exam_grades.makeup_exam_id NOT IN (
				SELECT exam_grades.makeup_exam_id
				FROM exam_grades
				WHERE exam_grades.registrar_approval = 1
				AND exam_grades.department_approval = 1
				AND exam_grades.makeup_exam_id IS NOT NULL
				GROUP BY exam_grades.makeup_exam_id
				ORDER BY exam_grades.id DESC
			) "
            );

            $resultsMakeup = array_merge($resultsMakeupNR, $resultsMakeupR);

            debug($resultsMakeup);

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (!empty($results)) {
            foreach ($results as $k => $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
            //debug($registrationAddMakeupIDs);
        }

        $publication_ids = array();

        if (!empty($registrationAddMakeupIDs['register'])) {
            $publication_ids_register = $this->CourseRegistration->find('list', array(
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['register']),
                'fields' => array('CourseRegistration.published_course_id')
            ));
            $publication_ids = $publication_ids + $publication_ids_register;
        }

        if (!empty($registrationAddMakeupIDs['add'])) {
            $publication_ids_add = $this->CourseAdd->find('list', array(
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['add']),
                'fields' => array('CourseAdd.published_course_id'),
            ));
            $publication_ids += $publication_ids_add;
        }

        if (!empty($registrationAddMakeupIDs['makeup'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeup']),
                'fields' => array('MakeupExam.published_course_id'),
            ));
            $publication_ids += $publication_ids_makeup;
        }

        if (isset($publication_ids) && !empty($publication_ids)) {
            $distinct_publication_ids = array_unique($publication_ids);
        }

        $published_courses = array();

        if (isset($publication_ids) && !empty($distinct_publication_ids)) {
            if ($department == 1) {
                //debug($distinct_publication_ids);
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                        'PublishedCourse.given_by_department_id' => $department_college_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        )
                    )
                ));
                // debug($published_courses);
            } else {
                $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                    'conditions' => array(
                        'PublishedCourse.id' => $distinct_publication_ids,
                        'PublishedCourse.college_id' => $department_college_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'CourseInstructorAssignment' => array(
                            'fields' => array('id', 'published_course_id', 'staff_id'),
                            'Staff' => array(
                                'fields' => array('id', 'full_name', 'user_id'),
                                'Position' => array('id', 'position'),
                                'Title' => array('id', 'title'),
                            ),
                            'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                        ),
                        'Course' => array(
                            'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                        )
                    )
                ));
            }
        }

        return $published_courses;
    }


    function getRejectedOrNonApprovedPublishedCourseList2(
        $department_id,
        $acadamic_year = '',
        $semester = '',
        $year_level = array(),
        $program_ids = array(),
        $program_type_ids = array(),
        $acy_ranges = array(),
        $role_id = ROLE_DEPARTMENT,
        $freshman = 0
    ) {

        $registrationAddMakeupIDs = array();
        $results = array();

        $given_by = 'given_by_department_id';

        if (!empty($department_id) && $role_id == ROLE_DEPARTMENT) {
            if (is_array($department_id)) {
                $depts_by_coma_quoted = "'" . implode("', '", $department_id) . "'";
            } else {
                $depts_by_coma_quoted = '' . $department_id . '';
            }
            $queryPss = " published_courses.id is not null ";
            $queryPss .= " and published_courses.given_by_department_id IN ($depts_by_coma_quoted)";
        } elseif (!empty($department_id) && $role_id == ROLE_COLLEGE) {
            if (is_array($department_id)) {
                $depts_by_coma_quoted = "'" . implode("', '", $department_id) . "'";
            } else {
                $depts_by_coma_quoted = '' . $department_id . '';
            }
            $queryPss = " published_courses.id is not null ";
            $queryPss .= " and published_courses.college_id IN ($depts_by_coma_quoted)";
            $given_by = 'college_id';
        } else {
            return array();
        }

        if (isset($acy_ranges) && !empty($acy_ranges)) {
            $acy_ranges_by_coma_quoted = "'" . implode("', '", $acy_ranges) . "'";
            $queryPss .= ' and published_courses.academic_year IN (' . $acy_ranges_by_coma_quoted . ') ';
        }

        if (!empty($acadamic_year)) {
            $queryPss .= " and published_courses.academic_year LIKE '" . $acadamic_year . "' ";
        }

        if (!empty($semester)) {
            $queryPss .= " and published_courses.semester LIKE '" . $semester . "' ";
        }

        if (!$freshman) {
            if (!empty($year_level)) {
                if (!empty($department_id) && $role_id == ROLE_DEPARTMENT) {
                    $published_dept_ids = ClassRegistry::init('PublishedCourse')->find(
                        'list',
                        array(
                            'group' => array('PublishedCourse.department_id'),
                            'fields' => array('PublishedCourse.department_id', 'PublishedCourse.department_id'),
                            'conditions' => array(
                                'PublishedCourse.given_by_department_id' => $department_id,
                                'PublishedCourse.academic_year' => (isset($acy_ranges) && !empty($acy_ranges) ? $acy_ranges : $acadamic_year)
                            )
                        )
                    );
                }

                //debug($published_dept_ids);

                $year_level_ids = ClassRegistry::init('YearLevel')->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.department_id' => $published_dept_ids,
                            'YearLevel.name' => $year_level
                        )
                    )
                );

                if (!empty($year_level_ids)) {
                    $year_level_ids = array_keys($year_level_ids);
                }

                //debug($year_level_ids);

                $year_levels_coma_quoted = "'" . implode("', '", $year_level_ids) . "'";
                $queryPss .= " and (published_courses.year_level_id IN ($year_levels_coma_quoted) OR ((published_courses.year_level_id IS NULL OR published_courses.year_level_id = '' OR published_courses.year_level_id = 0) AND published_courses.college_id IS NOT NULL )) ";
                //$queryPss .= " and (published_courses.year_level_id IN ('" . implode(', ', $year_level_id) . "')) ";
            }
        } else {
            $queryPss .= " and (published_courses.year_level_id IS NULL OR published_courses.year_level_id = '' OR published_courses.year_level_id = 0) AND published_courses.college_id IS NOT NULL )) ";
        }

        if (!empty($program_ids)) {
            if (is_array($program_ids)) {
                $program_ids_coma_quoted = "'" . implode("', '", $program_ids) . "'";
            } else {
                $program_ids_coma_quoted = '' . $program_ids . '';
            }
            $queryPss .= " and published_courses.program_id IN ($program_ids_coma_quoted) ";
        }

        if (!empty($program_type_ids)) {
            if (is_array($program_type_ids)) {
                $program_type_ids_coma_quoted = "'" . implode("', '", $program_type_ids) . "'";
            } else {
                $program_type_ids_coma_quoted = '' . $program_type_ids . '';
            }
            $queryPss .= " and published_courses.program_type_id IN ($program_type_ids_coma_quoted) ";
        }

        debug($queryPss);

        if (!empty($queryPss)) {
            $resultsRegistration = $this->query(
                "SELECT exam_grades.* FROM exam_grades
			INNER JOIN course_registrations course_registrations ON exam_grades.course_registration_id = course_registrations.id
			INNER JOIN published_courses published_courses ON course_registrations.published_course_id = published_courses.id
			INNER JOIN students students ON course_registrations.student_id = students.id
			WHERE students.graduated = 0 AND ((exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL) OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1) OR (exam_grades.department_reply = 0 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)) AND $queryPss GROUP BY exam_grades.course_registration_id ORDER BY exam_grades.id DESC"
            );

            if (!empty($resultsRegistration)) {
                foreach ($resultsRegistration as $key => $value) {
                    if ($this->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ExamGrade.course_registration_id' => $value['exam_grades']['course_registration_id'],
                                'ExamGrade.registrar_approval' => 1
                            )
                        )
                    )) {
                        unset($resultsRegistration[$key]);
                    }
                }
            }

            //debug($resultsRegistration);

            $resultsAdds = $this->query(
                "SELECT exam_grades.* FROM exam_grades
			INNER JOIN course_adds course_adds ON exam_grades.course_add_id = course_adds.id
			INNER JOIN students students ON course_adds.student_id = students.id
			INNER JOIN published_courses published_courses ON course_adds.published_course_id = published_courses.id
			WHERE students.graduated = 0 AND ((exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL) OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1) OR (exam_grades.department_reply = 0 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)) AND $queryPss GROUP BY exam_grades.course_add_id ORDER BY exam_grades.id DESC"
            );

            if (!empty($resultsAdds)) {
                foreach ($resultsAdds as $key => $value) {
                    if ($this->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ExamGrade.course_add_id' => $value['exam_grades']['course_add_id'],
                                'ExamGrade.registrar_approval' => 1
                            )
                        )
                    )) {
                        unset($resultsAdds[$key]);
                    }
                }
            }

            //debug($resultsAdds);

            $resultsMakeup = $this->query(
                "SELECT exam_grades.* FROM exam_grades
			INNER JOIN makeup_exams makeup_exams ON exam_grades.makeup_exam_id = makeup_exams.id
			INNER JOIN students students ON makeup_exams.student_id = students.id
			INNER JOIN published_courses published_courses ON makeup_exams.published_course_id = published_courses.id
			WHERE students.graduated = 0 AND ((exam_grades.department_approval IS NULL AND exam_grades.registrar_approval IS NULL) OR (exam_grades.department_reply = 1 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1) OR (exam_grades.department_reply = 0 AND exam_grades.department_approval = 1 AND exam_grades.registrar_approval = -1)) AND $queryPss GROUP BY exam_grades.makeup_exam_id ORDER BY exam_grades.id DESC"
            );

            if (!empty($resultsMakeup)) {
                foreach ($resultsMakeup as $key => $value) {
                    if ($this->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ExamGrade.makeup_exam_id' => $value['exam_grades']['makeup_exam_id'],
                                'ExamGrade.registrar_approval' => 1
                            )
                        )
                    )) {
                        unset($resultsMakeup[$key]);
                    }
                }
            }

            //debug($resultsMakeup);

            $results = array_merge($resultsRegistration, $resultsMakeup, $resultsAdds);
        }

        if (isset($results) && !empty($results)) {
            foreach ($results as $k => $value) {
                if (!empty($value['exam_grades']['course_registration_id'])) {
                    $registrationAddMakeupIDs['register'][] = $value['exam_grades']['course_registration_id'];
                } elseif (!empty($value['exam_grades']['course_add_id'])) {
                    $registrationAddMakeupIDs['add'][] = $value['exam_grades']['course_add_id'];
                } elseif (!empty($value['exam_grades']['makeup_exam_id'])) {
                    $registrationAddMakeupIDs['makeup'][] = $value['exam_grades']['makeup_exam_id'];
                }
            }
        }

        $publication_ids = array();

        if (isset($registrationAddMakeupIDs['register']) && !empty($registrationAddMakeupIDs['register'])) {
            $publication_ids_register = $this->CourseRegistration->find('list', array(
                'conditions' => array('CourseRegistration.id' => $registrationAddMakeupIDs['register']),
                'fields' => array('CourseRegistration.published_course_id')
            ));

            $publication_ids = $publication_ids + $publication_ids_register;
        }

        if (isset($registrationAddMakeupIDs['add']) && !empty($registrationAddMakeupIDs['add'])) {
            $publication_ids_add = $this->CourseAdd->find('list', array(
                'conditions' => array('CourseAdd.id' => $registrationAddMakeupIDs['add']),
                'fields' => array('CourseAdd.published_course_id'),
            ));

            $publication_ids += $publication_ids_add;
        }

        if (isset($registrationAddMakeupIDs['makeup']) && !empty($registrationAddMakeupIDs['makeup'])) {
            $publication_ids_makeup = $this->MakeupExam->find('list', array(
                'conditions' => array('MakeupExam.id' => $registrationAddMakeupIDs['makeup']),
                'fields' => array('MakeupExam.published_course_id'),
            ));

            $publication_ids += $publication_ids_makeup;
        }

        if (isset($publication_ids) && !empty($publication_ids)) {
            $distinct_publication_ids = array_unique($publication_ids);
        }

        if (isset($publication_ids) && !empty($distinct_publication_ids)) {
            debug($distinct_publication_ids);
            $published_courses = $this->CourseRegistration->PublishedCourse->find('all', array(
                'conditions' => array(
                    'PublishedCourse.id' => $distinct_publication_ids,
                    'PublishedCourse.' . $given_by . '' => $department_id
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'Section' => array('id', 'name'),
                    'Department' => array('id', 'name', 'type'),
                    'GivenByDepartment' => array('id', 'name', 'type'),
                    'College' => array('id', 'name', 'type'),
                    'YearLevel' => array('id', 'name'),
                    'CourseInstructorAssignment' => array(
                        'fields' => array('id', 'published_course_id', 'staff_id'),
                        'Staff' => array(
                            'fields' => array('id', 'full_name', 'user_id'),
                            'Position' => array('id', 'position'),
                            'Title' => array('id', 'title'),
                            'User' => array('id', 'username', 'email', 'active', 'email_verified'),
                        ),
                        'conditions' => array('CourseInstructorAssignment.isprimary' => 1)
                    ),
                    'Course' => array(
                        'fields' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                    )
                )
            ));
            // debug($published_courses);
        }

        if (isset($published_courses) && !empty($published_courses)) {
            return $published_courses;
        }

        return array();
    }


    function getAddSemester($student_id = null, $current_academic_year = null)
    {

        $first_added = $this->CourseAdd->find('first', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation = 1'
            ),
            'recursive' => -1,
            //'order' => array('CourseAdd.created ASC')
            'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
        ));

        $last_added = $this->CourseAdd->find('first', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation = 1'
            ),
            'recursive' => -1,
            //'order' => array('CourseAdd.created DESC')
            'order' => array(
                'CourseAdd.academic_year' => 'DESC',
                'CourseAdd.semester' => 'DESC',
                'CourseAdd.id' => 'DESC'
            )
        ));

        //If there is no registration and add (fresh student for the first time)
        if (empty($first_added) && empty($last_added)) {
            return array();
        } else {
            //getLastestStudentSemesterAndAcademicYear
            return $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear(
                $student_id,
                $current_academic_year
            );
            //return $this->getListOfAyAndSemester($student_id);
        }
    }

    function getTotalCreditAndPointDeduction($student_id = null, $all_ay_s_list = array())
    {

        $processed_course_reg = array();
        $processed_course_add = array();
        $deduct_credit_hour_sum = 0;
        $deduct_grade_point_sum = 0;
        $m_deduct_credit_hour_sum = 0;
        $m_deduct_grade_point_sum = 0;
        $credit_and_point_deduction = array();

        if (!empty($all_ay_s_list)) {
            foreach ($all_ay_s_list as $ays_key => $ay_and_s) {
                $course_and_grades = $this->getStudentCoursesAndFinalGrade(
                    $student_id,
                    $ay_and_s['academic_year'],
                    $ay_and_s['semester']
                );

                /* if ($ay_and_s['academic_year'] == "2017/18" && $ay_and_s['semester'] == "I") {
                    debug($course_and_grades);
                } */

                if (!empty($course_and_grades)) {
                    foreach ($course_and_grades as $key => $registered_added_course) {
                        /* if(!(isset($registered_added_course['grade']) && (isset($registered_added_course['point_value']) || strcasecmp($registered_added_course['grade'], 'I') == 0))) {
                            break 2;
                        } */

                        if ((isset($registered_added_course['grade']) && (strcasecmp(
                                    $registered_added_course['grade'],
                                    'I'
                                ) == 0 || strcasecmp(
                                    $registered_added_course['grade'],
                                    'W'
                                ) == 0 /* || strcasecmp($registered_added_course['grade'], 'NG') == 0 */))) {
                            continue;
                        }

                        if (isset($registered_added_course['grade']) && (strcasecmp(
                                    $registered_added_course['grade'],
                                    'W'
                                ) != 0 || strcasecmp(
                                    $registered_added_course['grade'],
                                    'I'
                                ) != 0 /* || strcasecmp($registered_added_course['grade'], 'NG') != 0 */) && isset($registered_added_course['used_in_gpa']) && $registered_added_course['used_in_gpa']) {
                            //Begin: credit hour and grade point deduction sum

                            if ($registered_added_course['repeated_new'] == true) {
                                /*** Get list of registration and add for the current course or
                                 * substituted course excluding current academic year and semester ***/

                                /** The returned AY and semester list is till the current round of AY and Semester. It is used to consider repeated courses within the same pattern AY and Semester **/

                                $previous_ay_and_s2 = $this->getListOfAyAndSemester(
                                    $student_id,
                                    $ay_and_s['academic_year'],
                                    $ay_and_s['semester']
                                );

                                debug($previous_ay_and_s2);
                                $course_registrations = $this->CourseRegistration->Student->CourseRegistration->getCourseRegistrations(
                                    $student_id,
                                    $previous_ay_and_s2,
                                    $registered_added_course['course_id'],
                                    1,
                                    1
                                );
                                //debug($course_registrations);
                                $course_adds = $this->CourseAdd->getCourseAdds(
                                    $student_id,
                                    $previous_ay_and_s2,
                                    $registered_added_course['course_id'],
                                    1
                                );

                                debug($course_adds);
                                debug($course_registrations);
                                debug($processed_course_reg);
                                //Add repeated courses credit hour and grade point

                                if (!empty($course_registrations)) {
                                    foreach ($course_registrations as $cr_key => $cr_value) {
                                        //To avoid double sum
                                        //debug($processed_course_reg);
                                        if (!in_array($cr_value['CourseRegistration']['id'], $processed_course_reg)) {
                                            $grade_detail = $this->getApprovedGrade(
                                                $cr_value['CourseRegistration']['id'],
                                                1
                                            );

                                            if ((isset($grade_detail['grade']) && (strcasecmp(
                                                        $grade_detail['grade'],
                                                        'I'
                                                    ) == 0 || strcasecmp($grade_detail['grade'], 'W') == 0))) {
                                                continue;
                                            }

                                            debug($grade_detail);
                                            debug($cr_value['PublishedCourse']);

                                            $deduct_credit_hour_sum += $cr_value['PublishedCourse']['Course']['credit'];

                                            if (!empty($grade_detail)) {
                                                $deduct_grade_point_sum += ($grade_detail['point_value'] * $cr_value['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($cr_value['PublishedCourse']['Course']['major'] == 1) {
                                                $m_deduct_credit_hour_sum += $cr_value['PublishedCourse']['Course']['credit'];

                                                if (!empty($grade_detail)) {
                                                    $m_deduct_grade_point_sum += ($grade_detail['point_value'] * $cr_value['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processed_course_reg[] = $cr_value['CourseRegistration']['id'];
                                        }
                                    }
                                }

                                debug($deduct_credit_hour_sum);
                                debug($deduct_grade_point_sum);
                                debug($processed_course_reg);

                                if (!empty($course_adds)) {
                                    foreach ($course_adds as $cr_key => $ca_value) {
                                        if (!in_array($ca_value['CourseAdd']['id'], $processed_course_add)) {
                                            $grade_detail = $this->getApprovedGrade($ca_value['CourseAdd']['id'], 0);
                                            debug($grade_detail);

                                            if ((isset($grade_detail['grade']) && (strcasecmp(
                                                        $grade_detail['grade'],
                                                        'I'
                                                    ) == 0 || strcasecmp($grade_detail['grade'], 'W') == 0))) {
                                                continue;
                                            }

                                            $deduct_credit_hour_sum += $ca_value['PublishedCourse']['Course']['credit'];

                                            if (!empty($grade_detail)) {
                                                $deduct_grade_point_sum += ($grade_detail['point_value'] * $ca_value['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($ca_value['PublishedCourse']['Course']['major'] == 1) {
                                                $m_deduct_credit_hour_sum += $ca_value['PublishedCourse']['Course']['credit'];

                                                if (!empty($grade_detail)) {
                                                    $m_deduct_grade_point_sum += ($grade_detail['point_value'] * $ca_value['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processed_course_add[] = $ca_value['CourseAdd']['id'];
                                        }
                                    }
                                }
                            }
                            //End of credit hour and grade point deduction sum
                        }
                    }
                }
            }
        }

        $credit_and_point_deduction['deduct_credit_hour_sum'] = $deduct_credit_hour_sum;
        //Sufiya Case Fix here but I have to see the effect with repeated course before apply to remove

        /* if ($deduct_credit_hour_sum > 0 && $deduct_grade_point_sum == 0){
            $credit_and_point_deduction['deduct_credit_hour_sum'] = 0;
        } */

        $credit_and_point_deduction['deduct_grade_point_sum'] = $deduct_grade_point_sum;
        $credit_and_point_deduction['m_deduct_credit_hour_sum'] = $m_deduct_credit_hour_sum;
        $credit_and_point_deduction['m_deduct_grade_point_sum'] = $m_deduct_grade_point_sum;

        return $credit_and_point_deduction;
    }

    function getTotalCreditPointDeduction($student_id)
    {

        $processed_course_reg = array();
        $processed_course_add = array();
        $deduct_credit_hour_sum = 0;
        $deduct_grade_point_sum = 0;
        $m_deduct_credit_hour_sum = 0;
        $m_deduct_grade_point_sum = 0;
        $credit_and_point_deduction = array();

        $all_ay_s_list = $this->getListOfAyAndSemester($student_id);
        debug($all_ay_s_list);

        if (!empty($all_ay_s_list)) {
            foreach ($all_ay_s_list as $ays_key => $ay_and_s) {
                $course_and_grades = $this->getStudentCoursesAndFinalGrade(
                    $student_id,
                    $ay_and_s['academic_year'],
                    $ay_and_s['semester']
                );

                if (!empty($course_and_grades)) {
                    foreach ($course_and_grades as $key => $registered_added_course) {
                        /* if (!(isset($registered_added_course['grade']) && (isset($registered_added_course['point_value']) || strcasecmp($registered_added_course['grade'],
                            'I') == 0))) {
                            break 2;
                        } */

                        if (
                            (isset($registered_added_course['grade']) && (strcasecmp(
                                        $registered_added_course['grade'],
                                        'I'
                                    ) == 0 || strcasecmp($registered_added_course['grade'], 'W') == 0))
                        ) {
                            continue;
                        }

                        if (strcasecmp(
                                $registered_added_course['grade'],
                                'I'
                            ) != 0 && $registered_added_course['used_in_gpa']) {
                            //Begin: credit hour and grade point deduction sum

                            if ($registered_added_course['repeated_new'] == true) {
                                /*** Get list of registration and add for the current course or
                                 * substituted course excluding current academic year and semester ***/

                                /** The returned AY and semester list is till the current round of AY and Semester. It is used to consider repeated courses within the same pattern AY and Semester **/

                                $previous_ay_and_s2 = $this->getListOfAyAndSemester(
                                    $student_id,
                                    $ay_and_s['academic_year'],
                                    $ay_and_s['semester']
                                );

                                $course_registrations = $this->CourseRegistration->Student->CourseRegistration->getCourseRegistrations(
                                    $student_id,
                                    $previous_ay_and_s2,
                                    $registered_added_course['course_id'],
                                    1,
                                    1
                                );
                                debug($course_registrations);

                                $course_adds = $this->CourseAdd->getCourseAdds(
                                    $student_id,
                                    $previous_ay_and_s2,
                                    $registered_added_course['course_id'],
                                    1
                                );
                                debug($course_adds);

                                //Add repeated courses credit hour and grade point
                                if (!empty($course_registrations)) {
                                    foreach ($course_registrations as $cr_key => $cr_value) {
                                        //To avoid double sum
                                        //debug($processed_course_reg);
                                        if (!in_array($cr_value['CourseRegistration']['id'], $processed_course_reg)) {
                                            $grade_detail = $this->getApprovedGrade(
                                                $cr_value['CourseRegistration']['id'],
                                                1
                                            );

                                            $deduct_credit_hour_sum += $cr_value['PublishedCourse']['Course']['credit'];

                                            if (!empty($grade_detail)) {
                                                $deduct_grade_point_sum += ($grade_detail['point_value'] * $cr_value['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($cr_value['PublishedCourse']['Course']['major'] == 1) {
                                                $m_deduct_credit_hour_sum += $cr_value['PublishedCourse']['Course']['credit'];

                                                if (!empty($grade_detail)) {
                                                    $m_deduct_grade_point_sum += ($grade_detail['point_value'] * $cr_value['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processed_course_reg[] = $cr_value['CourseRegistration']['id'];
                                        }
                                    }
                                }

                                if (!empty($course_adds)) {
                                    foreach ($course_adds as $cr_key => $ca_value) {
                                        if (!in_array($ca_value['CourseAdd']['id'], $processed_course_add)) {
                                            $grade_detail = $this->getApprovedGrade($ca_value['CourseAdd']['id'], 0);

                                            $deduct_credit_hour_sum += $ca_value['PublishedCourse']['Course']['credit'];

                                            if (!empty($grade_detail)) {
                                                $deduct_grade_point_sum += ($grade_detail['point_value'] * $ca_value['PublishedCourse']['Course']['credit']);
                                            }

                                            if ($ca_value['PublishedCourse']['Course']['major'] == 1) {
                                                $m_deduct_credit_hour_sum += $ca_value['PublishedCourse']['Course']['credit'];
                                                if (!empty($grade_detail)) {
                                                    $m_deduct_grade_point_sum += ($grade_detail['point_value'] * $ca_value['PublishedCourse']['Course']['credit']);
                                                }
                                            }

                                            $processed_course_add[] = $ca_value['CourseAdd']['id'];
                                        }
                                    }
                                }
                            }
                            //End of credit hour and grade point deduction sum
                        }
                    }
                }
            }
        }

        $credit_and_point_deduction['deduct_credit_hour_sum'] = $deduct_credit_hour_sum;
        $credit_and_point_deduction['deduct_grade_point_sum'] = $deduct_grade_point_sum;
        $credit_and_point_deduction['m_deduct_credit_hour_sum'] = $m_deduct_credit_hour_sum;
        $credit_and_point_deduction['m_deduct_grade_point_sum'] = $m_deduct_grade_point_sum;

        return $credit_and_point_deduction;
    }

    //////////////////////////////////////////////////////////////////////////

    //used for student profile will be modified
    function getStudentAllCoursesAndFinalGrade($student_id = null, $current_academic_year = null, $include_exempted = 0)
    {

        $courses_and_grades = array();

        $course_registered = $this->CourseRegistration->find('all', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            ),
            'order' => array(
                'CourseRegistration.academic_year' => 'ASC',
                'CourseRegistration.semester' => 'ASC',
                'CourseRegistration.id' => 'ASC'
            )
        ));

        $course_added = $this->CourseAdd->find('all', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            ),
            'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
        ));


        if (!empty($course_added)) {
            foreach ($course_added as $ca_key => $ca_value) {
                if (!($ca_value['PublishedCourse']['add'] == 1 || ($ca_value['CourseAdd']['department_approval'] == 1 && $ca_value['CourseAdd']['registrar_confirmation'] == 1))) {
                    unset($course_added[$ca_key]);
                }
            }
        }

        $student_detail = $this->CourseAdd->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );

        $exempted_courses = array();

        $student_level = $this->CourseAdd->Student->StudentExamStatus->studentYearAndSemesterLevelOfStatus(
            $student_id,
            $current_academic_year,
            null
        );

        if ($student_level['year'] == 1) {
            $student_year_level = '1st';
        } elseif ($student_level['year'] == 2) {
            $student_year_level = '2nd';
        } elseif ($student_level['year'] == 3) {
            $student_year_level = '3rd';
        } else {
            $student_year_level = 'th';
        }

        $year_level_id = null;

        if (isset($student_detail['Student']['department_id']) && is_numeric(
                $student_detail['Student']['department_id']
            ) && $student_detail['Student']['department_id']) {
            $year_level_id = ClassRegistry::init('YearLevel')->field(
                'id',
                array(
                    'YearLevel.department_id' => $student_detail['Student']['department_id'],
                    'YearLevel.name' => $student_year_level
                )
            );
        }


        if (!empty($student_detail['Student']['curriculum_id'])) {
            $courses_to_be_given = $this->CourseAdd->PublishedCourse->Course->find('all', array(
                'conditions' => array(
                    'Course.curriculum_id' => $student_detail['Student']['curriculum_id'],
                    'Course.year_level_id' => $year_level_id,
                ),
                'recursive' => -1
            ));

            if ($include_exempted == 1) {
                $all_exempted_courses = $this->CourseAdd->Student->CourseExemption->find('all', array(
                    'conditions' => array(
                        'CourseExemption.student_id' => $student_detail['Student']['id'],
                        'CourseExemption.department_accept_reject' => 1,
                        'CourseExemption.registrar_confirm_deny' => 1,
                    ),
                    'recursive' => -1
                ));

                if (!empty($all_exempted_courses)) {
                    foreach ($all_exempted_courses as $ex_key => $exempted_course) {
                        foreach ($courses_to_be_given as $c_key => $course_to_be_given) {
                            if ($course_to_be_given['Course']['id'] == $exempted_course['CourseExemption']['course_id']) {
                                $index = count($courses_and_grades);
                                $courses_and_grades[$index]['course_title'] = $course_to_be_given['Course']['course_title'];
                                $courses_and_grades[$index]['course_code'] = $course_to_be_given['Course']['course_code'];
                                $courses_and_grades[$index]['course_id'] = $course_to_be_given['Course']['id'];
                                $courses_and_grades[$index]['major'] = $course_to_be_given['Course']['major'];
                                $courses_and_grades[$index]['credit'] = $course_to_be_given['Course']['credit'];
                                $courses_and_grades[$index]['thesis'] = $course_to_be_given['Course']['thesis'];
                                $courses_and_grades[$index]['grade'] = 'EX';
                                $courses_and_grades[$index]['exit_exam'] = $course_to_be_given['Course']['exit_exam'];
                                $courses_and_grades[$index]['elective'] = $course_to_be_given['Course']['elective'];
                            }
                        }
                    }
                }
            }
        }

        if (!empty($course_registered)) {
            foreach ($course_registered as $key => $value) {
                if (!$this->CourseRegistration->isCourseDroped($value['CourseRegistration']['id'])) {
                    $index = count($courses_and_grades);
                    $courses_and_grades[$index]['course_title'] = $value['PublishedCourse']['Course']['course_title'];
                    $courses_and_grades[$index]['course_code'] = $value['PublishedCourse']['Course']['course_code'];
                    $courses_and_grades[$index]['course_id'] = $value['PublishedCourse']['Course']['id'];
                    $courses_and_grades[$index]['major'] = $value['PublishedCourse']['Course']['major'];
                    $courses_and_grades[$index]['credit'] = $value['PublishedCourse']['Course']['credit'];
                    $courses_and_grades[$index]['thesis'] = $value['PublishedCourse']['Course']['thesis'];
                    $courses_and_grades[$index]['exit_exam'] = $value['PublishedCourse']['Course']['exit_exam'];
                    $courses_and_grades[$index]['elective'] = $value['PublishedCourse']['Course']['elective'];

                    $grade_detail = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                    debug($grade_detail);

                    if (!empty($grade_detail)) {
                        $courses_and_grades[$index]['grade'] = $grade_detail['grade'];
                        if (isset($grade_detail['point_value'])) {
                            $courses_and_grades[$index]['point_value'] = $grade_detail['point_value'];
                            $courses_and_grades[$index]['pass_grade'] = $grade_detail['pass_grade'];
                            $courses_and_grades[$index]['used_in_gpa'] = $grade_detail['used_in_gpa'];
                        }
                    }

                    //To determine if a student registered more than once for the same course

                    $matching_courses = array();

                    $cID = $value['PublishedCourse']['Course']['id'];

                    $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                        $cID,
                        $student_detail['Student']['curriculum_id']
                    );
                    $matching_courses[$cID] = $cID;
                    $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                        $matching_courses,
                        $student_detail['Student']['id']
                    );

                    //If the student add or register once
                    if (count($register_and_add_freq) <= 1) {
                        $courses_and_grades[$index]['repeated_old'] = false;
                        $courses_and_grades[$index]['repeated_new'] = false;
                    } else {
                        //If the student has multiple registration and/or add
                        $rep = $this->repeatationLabeling(
                            $register_and_add_freq,
                            'register',
                            $value['CourseRegistration']['id'],
                            $student_detail,
                            $courses_and_grades[$index]['course_id']
                        );
                        $courses_and_grades[$index]['repeated_old'] = $rep['repeated_old'];
                        $courses_and_grades[$index]['repeated_new'] = $rep['repeated_new'];
                    }
                }
            }
        }

        if (!empty($course_added)) {
            foreach ($course_added as $key => $value) {
                $index = count($courses_and_grades);

                $courses_and_grades[$index]['course_title'] = $value['PublishedCourse']['Course']['course_title'];
                $courses_and_grades[$index]['course_code'] = $value['PublishedCourse']['Course']['course_code'];
                $courses_and_grades[$index]['course_id'] = $value['PublishedCourse']['Course']['id'];
                $courses_and_grades[$index]['major'] = $value['PublishedCourse']['Course']['major'];
                $courses_and_grades[$index]['credit'] = $value['PublishedCourse']['Course']['credit'];
                $courses_and_grades[$index]['thesis'] = $value['PublishedCourse']['Course']['thesis'];

                $grade_detail = $this->getApprovedGrade($value['CourseAdd']['id'], 0);

                if (!empty($grade_detail)) {
                    $courses_and_grades[$index]['grade'] = $grade_detail['grade'];
                    if (isset($grade_detail['point_value'])) {
                        $courses_and_grades[$index]['point_value'] = $grade_detail['point_value'];
                        $courses_and_grades[$index]['pass_grade'] = $grade_detail['pass_grade'];
                        $courses_and_grades[$index]['used_in_gpa'] = $grade_detail['used_in_gpa'];
                    }
                }
                //To determine if a student registered more than once for the same course

                $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                    $matching_courses,
                    $student_detail['Student']['id']
                );
                $courses_and_grades[$index]['repeated_old'] = $rep['repeated_old'];
                $courses_and_grades[$index]['repeated_new'] = $rep['repeated_new'];
                $courses_and_grades[$index]['exit_exam'] = $value['PublishedCourse']['Course']['exit_exam'];
                $courses_and_grades[$index]['elective'] = $value['PublishedCourse']['Course']['elective'];

                //If the student add or register once
                if (count($register_and_add_freq) <= 1) {
                    $courses_and_grades[$index]['repeated_old'] = false;
                    $courses_and_grades[$index]['repeated_new'] = false;
                } else {
                    //If the student has multiple registration and/or add
                    $rep = $this->repeatationLabeling(
                        $register_and_add_freq,
                        'add',
                        $value['CourseAdd']['id'],
                        $student_detail,
                        $courses_and_grades[$index]['course_id']
                    );
                    $courses_and_grades[$index]['repeated_old'] = $rep['repeated_old'];
                    $courses_and_grades[$index]['repeated_new'] = $rep['repeated_new'];
                }
            }
        }

        return $courses_and_grades;
    }

    function getPublishedCourseIfExist(
        $department_id,
        $academic_year,
        $semester,
        $program_id,
        $program_type_id,
        $studentDetail,
        $admissionAcademicYear,
        $currentAcademicYear = null
    ) {

        //debug($studentDetail);

        $section = array();

        //find the section and yearlevel of the student
        $student_ay_and_s = $this->CourseRegistration->Student->StudentExamStatus->getStudentFirstAyAndSemester(
            $studentDetail['Student']['id'],
            $admissionAcademicYear
        );

        $student_ay_and_s_list = $this->getListOfAyAndSemester($studentDetail['Student']['id']);
        // debug($student_ay_and_s_list);

        if (!empty($student_ay_and_s_list)) {
            foreach ($student_ay_and_s_list as $k => &$v) {
                $withdrawlAfterRegistration = $this->CourseRegistration->Student->Clearance->withDrawaAfterRegistration(
                    $studentDetail['Student']['id'],
                    $v['academic_year'],
                    $v['semester']
                );
                if (!$withdrawlAfterRegistration) {
                    //unset($student_ay_and_s_list[$k]);
                    $student_ay_and_s_lists[] = $v;
                }
            }
        }

        if (!empty($student_ay_and_s_lists)) {
            $student_ay_and_s_list = $student_ay_and_s_lists;
        }

        if (!empty($student_ay_and_s_list)) {
            $lastKey = count($student_ay_and_s_list) - 1;
            $student_ay_and_s['academic_year'] = $student_ay_and_s_list[$lastKey]['academic_year'];
            $student_ay_and_s['semester'] = $student_ay_and_s_list[$lastKey]['semester'];
            $next_academic_year_semester = $this->CourseRegistration->Student->StudentExamStatus->getNextSemster(
                $student_ay_and_s['academic_year'],
                $student_ay_and_s['semester']
            );
        } else {
            //$student_ay_and_s_list = $student_ay_and_s;
            $next_academic_year_semester = $student_ay_and_s;
        }

        if (!empty($student_ay_and_s_list)) {
            foreach ($student_ay_and_s_list as $ay) {
                if (($ay['semester'] == $semester && $ay['academic_year'] == $academic_year)) {
                    $next_academic_year_semester = $ay;
                    break;
                }
            }
        }
        //debug($next_academic_year_semester);
        debug($student_ay_and_s);

        if (!empty($student_ay_and_s)) {
            $status_level = $this->CourseRegistration->Student->StudentExamStatus->studentYearAndSemesterLevel(
                $studentDetail['Student']['id'],
                $student_ay_and_s['academic_year'],
                $student_ay_and_s['semester']
            );
            $yearLevelId = $this->CourseRegistration->YearLevel->find(
                'first',
                array(
                    'conditions' => array(
                        'YearLevel.name' => $status_level['year'],
                        'YearLevel.department_id' => $studentDetail['Student']['department_id']
                    ),
                    'recursive' => -1
                )
            );
            //debug($student_ay_and_s['academic_year']);
            //$student_section = ClassRegistry::init('StudentsSection')->find('first',array('conditions' => array('StudentsSection.student_id' => $studentDetail['Student']['id']),'order' => array('StudentsSection.section_id DESC')));

            $sectionfil = $this->CourseRegistration->find('first', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $studentDetail['Student']['id'],
                    'CourseRegistration.semester' => $student_ay_and_s['semester'],
                    'CourseRegistration.academic_year' => $student_ay_and_s['academic_year']
                ),
                'contain' => array('Section'),
                'recursive' => -1
            ));

            //debug($sectionfil);

            if (!empty($sectionfil)) {
                $student_section = $this->CourseRegistration->Section->StudentsSection->find('first', array(
                    'conditions' => array(
                        'StudentsSection.student_id' => $studentDetail['Student']['id'],
                        'StudentsSection.section_id' => $sectionfil['Section']['id']
                    )
                ));
                //debug($student_section);
            } else {
                $student_section = $this->CourseRegistration->Section->StudentsSection->find('first', array(
                    'conditions' => array(
                        'StudentsSection.student_id' => $studentDetail['Student']['id']
                    ),
                    'order' => array('StudentsSection.section_id' => 'DESC')
                ));
                //debug($student_section);
            }

            if (!empty($student_section)) {
                $section = $this->CourseRegistration->Section->find('first', array(
                    'conditions' => array(
                        //'Section.department_id' => $department_id,
                        //'Section.program_id' => $studentDetail['Student']['program_id'],
                        //'Section.program_type_id' => $studentDetail['Student']['program_type_id'],
                        'Section.id' => $student_section['StudentsSection']['section_id']
                    ),
                    'recursive' => -1
                ));
                debug($section);

                if ($section['Section']['academicyear'] != $academic_year) {
                    $studentSectionnns = $this->CourseRegistration->Section->StudentsSection->find('first', array(
                        'conditions' => array(
                            'StudentsSection.student_id' => $studentDetail['Student']['id'],
                            'StudentsSection.section_id in (select id from sections where academicyear = "' . $academic_year . '")'
                        ),
                        'order' => array('StudentsSection.id' => 'DESC', 'StudentsSection.section_id' => 'DESC'),
                    ));

                    //debug($studentSectionnns);

                    if (!empty($studentSectionnns)) {
                        $section = $this->CourseRegistration->Section->find('first', array(
                            'conditions' => array(
                                /*'Section.department_id' => $department_id,
                                'Section.program_id' => $studentDetail['Student']['program_id'],
                                'Section.program_type_id' => $studentDetail['Student']['program_type_id'],
                                */
                                'Section.id' => $studentSectionnns['StudentsSection']['section_id']
                            ),
                            'recursive' => -1
                        ));
                        //debug($section);
                    }
                }
            } else {
                $section = $this->CourseRegistration->Section->find('first', array(
                    'conditions' => array(
                        'Section.department_id' => $department_id,
                        'Section.program_id' => $studentDetail['Student']['program_id'],
                        'Section.program_type_id' => $studentDetail['Student']['program_type_id'],
                        'Section.year_level_id' => $yearLevelId['YearLevel']['id'],
                        'Section.academicyear' => $student_ay_and_s['academic_year']
                    ),
                    'recursive' => -1
                ));
                debug($section);
            }

            if (isset($section['Section']['academicyear']) && !empty($section['Section']['academicyear'])) {
                if ($next_academic_year_semester['academic_year'] != $section['Section']['academicyear'] && $yearLevelId['YearLevel']['name'] == $status_level['year'] && $next_academic_year_semester['academic_year'] != $currentAcademicYear) {
                    $next_academic_year_semester['academic_year'] = $section['Section']['academicyear'];
                    $next_academic_year_semester['semester'] = 'I';
                }
            }

            $student_section_id = null;

            if (isset($section['Section']['id']) && isset($studentDetail['Student']['id'])) {
                $student_section = $this->CourseRegistration->Section->StudentsSection->find('first', array(
                    'conditions' => array(
                        'StudentsSection.student_id' => $studentDetail['Student']['id'],
                        'StudentsSection.section_id' => $section['Section']['id']
                    ),
                    'order' => array('StudentsSection.created' => 'DESC')
                ));

                debug($student_section);
            }

            // check if the student has section ?
            if (empty($student_section) && !empty($section)) {
                // Does the student has curriculum ?
                if (!empty($studentDetail['Student']['curriculum_id'])) {
                    if (!empty($section)) {
                        $student_section['StudentsSection']['student_id'] = $studentDetail['Student']['id'];
                        $student_section['StudentsSection']['section_id'] = $section['Section']['id'];

                        if ($currentAcademicYear == $next_academic_year_semester['academic_year']) {
                            $student_section['StudentsSection']['archive'] = 0;
                        } else {
                            $student_section['StudentsSection']['archive'] = 0;
                        }

                        ClassRegistry::init('StudentsSection')->create();
                        ClassRegistry::init('StudentsSection')->save($student_section);

                        $student_sections = ClassRegistry::init('StudentsSection')->find('first', array(
                            'conditions' => array('StudentsSection.id' => ClassRegistry::init('StudentsSection')->id)
                        ));

                        $student_section_id = $student_sections['StudentsSection']['section_id'];
                    }
                }
            } else {
                // the student has already section, check for upgrade ?
                if (isset($student_section['StudentsSection'])) {
                    $student_section_id = $student_section['StudentsSection']['section_id'];
                    debug($student_section_id);
                }
            }

            $options = array();
            //$options['conditions']['PublishedCourse.department_id'] = $department_id;

            //if the student mistakenly placed to wrong program type
            if (isset($section['Section']['program_id']) && !empty($section['Section']['program_id'])) {
                $options['conditions']['PublishedCourse.program_id'] = $section['Section']['program_id'];
            } else {
                $options['conditions']['PublishedCourse.program_id'] = $program_id;
            }

            //if the student mistakenly placed to wrong section type
            if (isset($section['Section']['program_type_id']) && !empty($section['Section']['program_type_id'])) {
                $options['conditions']['PublishedCourse.program_type_id'] = $section['Section']['program_type_id'];
            } else {
                $options['conditions']['PublishedCourse.program_type_id'] = $program_type_id;
            }

            if (!empty($academic_year) && !empty($semester)) {
                $section_id = $this->CourseRegistration->Section->StudentsSection->find('first', array(
                    'conditions' => array(
                        'StudentsSection.student_id' => $studentDetail['Student']['id'],
                        'StudentsSection.section_id in (select id from sections where academicyear = "' . $academic_year . '") '
                    )
                ));

                debug($section_id);

                $options['conditions']['PublishedCourse.semester'] = $semester;
                $options['conditions']['PublishedCourse.academic_year'] = $academic_year;
                $options['conditions']['PublishedCourse.section_id'] = (!empty($student_section_id) ? $student_section_id : (!empty($section_id['StudentsSection']['section_id']) ? $section_id['StudentsSection']['section_id'] : 0));
            } else {
                $options['conditions']['PublishedCourse.semester'] = $next_academic_year_semester['semester'];
                $options['conditions']['PublishedCourse.academic_year'] = $next_academic_year_semester['academic_year'];
                $options['conditions']['PublishedCourse.section_id'] = (!empty($student_section_id) ? $student_section_id : 0);
            }


            $options['contain'] = array(
                'Course' => array(
                    'fields' => array('id', 'course_title', 'course_code', 'credit'),
                    'Prerequisite',
                    'GradeType' => array(
                        'Grade' => array(
                            'fields' => array('id', 'grade')
                        )
                    )
                ),
                'CourseInstructorAssignment' => array(
                    'Staff' => array(
                        'fields' => array('id', 'full_name', 'first_name', 'middle_name', 'last_name'),
                        'Title' => array('id', 'title'),
                        'College' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'Position' => array('id', 'position'),
                    ),
                    'order' => array('isprimary' => 'DESC'),
                    'limit' => 1
                )
            );

            $options['fields'] = array(
                'DISTINCT PublishedCourse.course_id',
                'PublishedCourse.department_id',
                'PublishedCourse.academic_year',
                'PublishedCourse.semester',
                'PublishedCourse.program_id',
                'PublishedCourse.program_type_id',
                'PublishedCourse.id',
                'PublishedCourse.section_id',
                'PublishedCourse.grade_scale_id',
                'PublishedCourse.year_level_id'
            );

            $student_course_registrations['courses'] = $this->CourseRegistration->PublishedCourse->find(
                'all',
                $options
            );

            //debug($options);
            //debug($student_course_registrations['courses']);

            $freq = array();

            if (!empty($student_course_registrations['courses'])) {
                foreach ($student_course_registrations['courses'] as $k => &$value) {
                    $failedAnyPrerequistie['freq'] = 0;

                    $is_grade_submitted = $this->isGradeSubmittedForPublishedCourseGivenStudentId(
                        $studentDetail['Student']['id'],
                        $value['PublishedCourse']['id']
                    );

                    $registeredOrAddCourse = $this->CourseRegistration->find('first', array(
                        'conditions' => array(
                            'CourseRegistration.student_id' => $studentDetail['Student']['id'],
                            'CourseRegistration.published_course_id' => $value['PublishedCourse']['id']
                        ),
                        'recursive' => -1
                    ));
                    //debug($registeredOrAddCourse);

                    if (!empty($registeredOrAddCourse)) {
                        $value['PublishedCourse']['grade'] = $this->getApprovedGrade(
                            $registeredOrAddCourse['CourseRegistration']['id'],
                            1
                        );
                        $value['CourseRegistration'] = $registeredOrAddCourse['CourseRegistration'];
                    } else {
                        $registeredOrAddCourse = $this->CourseAdd->find('first', array(
                            'conditions' => array(
                                'CourseAdd.student_id' => $studentDetail['Student']['id'],
                                'CourseAdd.published_course_id' => $value['PublishedCourse']['id']
                            ),
                            'recursive' => -1
                        ));

                        if (!empty($registeredOrAddCourse)) {
                            $value['PublishedCourse']['grade'] = $this->getApprovedGrade(
                                $registeredOrAddCourse['CourseAdd']['id'],
                                0
                            );
                            $value['CourseAdd'] = $registeredOrAddCourse['CourseAdd'];
                        }
                    }

                    if (!empty($value['Course']['Prerequisite'])) {
                        foreach ($value['Course']['Prerequisite'] as $preValue) {
                            $failed = ClassRegistry::init('CourseDrop')->prequisite_taken(
                                $studentDetail['Student']['id'],
                                $preValue['prerequisite_course_id']
                            );
                            if ($failed == 0 && $preValue['co_requisite'] != true) {
                                $failedAnyPrerequistie['freq']++;
                            }
                        }
                    }

                    if ($failedAnyPrerequistie['freq'] > 0) {
                        $value['PublishedCourse']['prerequisiteFailed'] = true;
                    } else {
                        $value['PublishedCourse']['prerequisiteFailed'] = 0;
                    }

                    if ($is_grade_submitted) {
                        $value['PublishedCourse']['readOnly'] = true;
                        //$value['Course']['achievedgrade'] = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);
                    } else {
                        $value['PublishedCourse']['readOnly'] = false;
                    }

                    if (!empty($value['CourseInstructorAssignment'])) {
                        //debug($value['CourseInstructorAssignment']);
                        $value['PublishedCourse']['haveAssignedIntructor'] = true;
                        //$value['PublishedCourse']['AssignedIntructor'] = $value['CourseInstructorAssignment'][0]['Staff'];
                    } else {
                        $value['PublishedCourse']['haveAssignedIntructor'] = false;
                        //$value['PublishedCourse']['AssignedIntructor'] = array();
                    }

                    $value['Course']['grade_scale_id'] = ClassRegistry::init(
                        'GradeScale'
                    )->getGradeScaleIdGivenPublishedCourse($value['PublishedCourse']['id']);
                }
            }
        }

        return $student_course_registrations;
    }

    function getPublishedCourseGradeGradeScale($id)
    {

        $courseRegistration = $this->CourseRegistration->find('first', array(
            'conditions' => array(
                'CourseRegistration.published_course_id' => $id,
                'CourseRegistration.id in (select course_registration_id from exam_grades where grade_scale_id is not null and department_approval = 1 and registrar_approval = 1 and registrar_reason != "Via backend data entry interface" and registrar_reason != "Via backend data entry interface")'
            ),
            'contain' => array('ExamGrade')
        ));

        if (isset($courseRegistration['CourseRegistration']) && !empty($courseRegistration['CourseRegistration']) && isset($courseRegistration['ExamGrade'])) {
            return $courseRegistration['ExamGrade'][0]['grade_scale_id'];
        }

        $courseAdd = $this->CourseAdd->find(
            'first',
            array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $id,
                    'CourseAdd.id in (select course_add_id from exam_grades where grade_scale_id is not null and department_approval = 1 and registrar_approval = 1)'
                ),
                'contain' => array('ExamGrade')
            )
        );

        if (isset($courseAdd['CourseAdd']) && !empty($courseAdd['CourseAdd']) && isset($courseAdd['ExamGrade'])) {
            return $courseAdd['ExamGrade'][0]['grade_scale_id'];
        }

        return 0;   //debug($courseRegistration);
    }


    function isGradeSubmittedForPublishedCourseGivenStudentId($studentId, $published_course_ids)
    {

        $published_courses_student_registred_score_grade = 0;

        $grade_submitted_registred_courses = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.published_course_id' => $published_course_ids,
                'CourseRegistration.student_id' => $studentId
            ),
            'fields' => array('CourseRegistration.id')
        ));

        if (!empty($grade_submitted_registred_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamGrade.course_registration_id' => $grade_submitted_registred_courses))
            );
            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        //check course adds
        $grade_submitted_add_courses = $this->CourseAdd->find('list', array(
            'conditions' => array(
                'CourseAdd.published_course_id' => $published_course_ids,
                'CourseAdd.student_id' => $studentId,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation = 1'
            ),
            'fields' => array('CourseAdd.id')
        ));

        if (!empty($grade_submitted_add_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamGrade.course_add_id' => $grade_submitted_add_courses))
            );
            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        return $published_courses_student_registred_score_grade;
    }


    //used for student profile will be modified
    function getCourseRepetation($course_reg_add_id, $student_id, $registered = 1)
    {

        $student_detail = $this->CourseRegistration->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );

        $course_added = array();
        $course_registered = array();

        $courses_and_grades = array();

        if ($registered == 1) {
            $course_registered = $this->CourseRegistration->find('all', array(
                'conditions' => array(
                    //'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.id' => $course_reg_add_id,
                    'CourseRegistration.id not in (select course_registration_id from course_drops where registrar_confirmation = 1 and department_approval = 1)',
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course'
                    )
                )
            ));
            //debug($course_registered);
        } else {
            $course_added = $this->CourseAdd->find('all', array(
                'conditions' => array(
                    //'CourseAdd.student_id' => $student_id,
                    'CourseAdd.id' => $course_reg_add_id,
                    'CourseAdd.department_approval = 1',
                    'CourseAdd.registrar_confirmation = 1'
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course'
                    )
                )
            ));
        }

        if (!empty($course_registered)) {
            foreach ($course_registered as $key => $value) {
                if (isset($value['CourseRegistration']['id']) && !$this->CourseRegistration->isCourseDroped(
                        $value['CourseRegistration']['id']
                    )) {
                    if (isset($value['PublishedCourse']['Course']['id']) && !empty($value['PublishedCourse']['Course']['id'])) {
                        //$index = count($courses_and_grades);
                        $courses_and_grades['course_title'] = $value['PublishedCourse']['Course']['course_title'];
                        $courses_and_grades['course_code'] = $value['PublishedCourse']['Course']['course_code'];
                        $courses_and_grades['course_id'] = $value['PublishedCourse']['Course']['id'];
                        $courses_and_grades['major'] = $value['PublishedCourse']['Course']['major'];
                        $courses_and_grades['credit'] = $value['PublishedCourse']['Course']['credit'];
                        $courses_and_grades['thesis'] = $value['PublishedCourse']['Course']['thesis'];

                        $grade_detail = $this->getApprovedGrade($value['CourseRegistration']['id'], 1);

                        if (!empty($grade_detail)) {
                            $courses_and_grades['grade'] = $grade_detail['grade'];
                            if (isset($grade_detail['point_value'])) {
                                $courses_and_grades['point_value'] = $grade_detail['point_value'];
                                $courses_and_grades['pass_grade'] = $grade_detail['pass_grade'];
                                $courses_and_grades['used_in_gpa'] = $grade_detail['used_in_gpa'];
                            }
                        }

                        //To determine if a student registered more than once for the same course
                        $matching_courses = array();

                        $cID = $value['PublishedCourse']['Course']['id'];

                        if (isset($student_detail['Student']['curriculum_id']) && !empty($student_detail['Student']['curriculum_id'])) {
                            $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                                $cID,
                                $student_detail['Student']['curriculum_id'],
                                0
                            );
                        }

                        $matching_courses[$cID] = $cID;

                        $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                            $matching_courses,
                            $student_detail['Student']['id']
                        );
                        //debug($register_and_add_freq);

                        //If the student add or register once
                        if (count($register_and_add_freq) <= 1) {
                            $courses_and_grades['repeated_old'] = false;
                            $courses_and_grades['repeated_new'] = false;
                        } else {
                            //If the student has multiple registration and/or add
                            $rep = $this->repeatationLabeling(
                                $register_and_add_freq,
                                'register',
                                $value['CourseRegistration']['id'],
                                $student_detail,
                                $courses_and_grades['course_id']
                            );
                            $courses_and_grades['repeated_old'] = $rep['repeated_old'];
                            $courses_and_grades['repeated_new'] = $rep['repeated_new'];
                        }
                    }
                }
            }
        }

        if (!empty($course_added)) {
            foreach ($course_added as $key => $value) {
                if (isset($value['PublishedCourse']['Course']['id']) && !empty($value['PublishedCourse']['Course']['id'])) {
                    $index = count($courses_and_grades);

                    $courses_and_grades['course_title'] = $value['PublishedCourse']['Course']['course_title'];
                    $courses_and_grades['course_code'] = $value['PublishedCourse']['Course']['course_code'];
                    $courses_and_grades['course_id'] = $value['PublishedCourse']['Course']['id'];
                    $courses_and_grades['major'] = $value['PublishedCourse']['Course']['major'];
                    $courses_and_grades['credit'] = $value['PublishedCourse']['Course']['credit'];
                    $courses_and_grades['thesis'] = $value['PublishedCourse']['Course']['thesis'];


                    $grade_detail = $this->getApprovedGrade($value['CourseAdd']['id'], 0);

                    if (!empty($grade_detail)) {
                        $courses_and_grades[$index]['grade'] = $grade_detail['grade'];
                        if (isset($grade_detail['point_value'])) {
                            $courses_and_grades['point_value'] = $grade_detail['point_value'];
                            $courses_and_grades['pass_grade'] = $grade_detail['pass_grade'];
                            $courses_and_grades['used_in_gpa'] = $grade_detail['used_in_gpa'];
                        }
                    }

                    //To determine if a student registered more than once for the same course
                    $matching_courses = array();

                    $cID = $value['PublishedCourse']['Course']['id'];

                    if (isset($student_detail['Student']['curriculum_id']) && !empty($student_detail['Student']['curriculum_id'])) {
                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
                            $cID,
                            $student_detail['Student']['curriculum_id']
                        );
                    }

                    $matching_courses[$cID] = $cID;

                    $register_and_add_freq = $this->getCourseFrequenceRegAdds(
                        $matching_courses,
                        $student_detail['Student']['id']
                    );

                    //If the student add or register once
                    if (count($register_and_add_freq) <= 1) {
                        $courses_and_grades['repeated_old'] = false;
                        $courses_and_grades['repeated_new'] = false;
                    } else {
                        //If the student has multiple registration and/or add
                        $rep = $this->repeatationLabeling(
                            $register_and_add_freq,
                            'add',
                            $value['CourseAdd']['id'],
                            $student_detail,
                            $courses_and_grades['course_id']
                        );
                        $courses_and_grades['repeated_old'] = $rep['repeated_old'];
                        $courses_and_grades['repeated_new'] = $rep['repeated_new'];
                    }
                }
            }
        }

        return $courses_and_grades;
    }

    function gradeSubmittedForAYSem($student_id, $AY, $Sem)
    {

        $grade_submitted_registred_courses = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.semester' => $Sem,
                'CourseRegistration.academic_year' => $AY
            ),
            'fields' => array('CourseRegistration.id')
        ));

        if (!empty($grade_submitted_registred_courses)) {
            foreach ($grade_submitted_registred_courses as $k => $v) {
                $grade_detail = $this->getApprovedGrade($v, 1);
                if (isset($grade_detail['grade']) && $grade_detail['grade'] != 'W') {
                    return 1;
                }
            }
        }

        $grade_submitted_added_courses = $this->CourseAdd->find('list', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.semester' => $Sem,
                'CourseAdd.academic_year' => $AY
            ),
            'fields' => array('CourseAdd.id')
        ));


        if (!empty($grade_submitted_added_courses)) {
            foreach ($grade_submitted_added_courses as $k => $v) {
                $grade_detail = $this->getApprovedGrade($v, 0);
                if (isset($grade_detail['grade']) && $grade_detail['grade'] != 'W') {
                    return 1;
                }
            }
        }
        return 0;
    }


    function checkCourseFrequencyTaken($student_id, $acadamic_year, $semester, $course_id)
    {

        $courses_and_grades = array();

        $matching_courses = ClassRegistry::init('Course')->getTakenEquivalentCourses($student_id, $course_id);

        $student_detail = $this->CourseAdd->Student->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id,
            ),
            'recursive' => -1
        ));

        $course_registered = $this->CourseRegistration->find('all', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.academic_year' => $acadamic_year,
                'CourseRegistration.semester' => $semester
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            )
        ));

        $course_added = $this->CourseAdd->find('all', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.academic_year' => $acadamic_year,
                'CourseAdd.semester' => $semester
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            )
        ));

        if (!empty($course_added)) {
            foreach ($course_added as $ca_key => $ca_value) {
                if (!($ca_value['PublishedCourse']['add'] == 1 || ($ca_value['CourseAdd']['department_approval'] == 1 && $ca_value['CourseAdd']['registrar_confirmation'] == 1))) {
                    unset($course_added[$ca_key]);
                }
            }
        }

        $registration_and_course_add_merged = array_merge($course_added, $course_registered);
        //debug($registration_and_course_add_merged);

        if (!empty($registration_and_course_add_merged)) {
            foreach ($registration_and_course_add_merged as $k => $value) {
                $register_and_add_freq = $this->getCourseFrequenceRegAdds($matching_courses, $student_id);
                //If the student add or register once
                if (count($register_and_add_freq) <= 1) {
                    $courses_and_grades[$acadamic_year]['repeated_old'] = false;
                    $courses_and_grades[$acadamic_year]['repeated_new'] = false;
                } else {
                    //If the student has multiple registration and/or add
                    $rept = $this->repeatationLabeling(
                        $register_and_add_freq,
                        'add',
                        $value['CourseAdd']['id'],
                        $student_detail,
                        $value['PublishedCourse']['course_id']
                    );

                    $courses_and_grades[$acadamic_year]['repeated_old'] = $rept['repeated_old'];
                    $courses_and_grades[$acadamic_year]['repeated_new'] = $rept['repeated_new'];
                }
            }
        }

        debug($courses_and_grades);
        return $courses_and_grades;
    }


    function getListOfSubmittedGradeForDepartmentApproval($col_dpt_id = null, $department = 1)
    {

        $department_action_required_list = $this->find('all', array(
            'conditions' => array(
                'ExamGrade.department_approval IS null',
                'ExamGrade.registrar_approval IS null',
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'CourseInstructorAssignment.isprimary' => 1
                            ),
                            'Staff'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
                    )
                ),
                'CourseAdd' => array(
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'CourseInstructorAssignment.isprimary' => 1
                            ),
                            'Staff'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
                    )
                ),
                'MakeupExam' => array(
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'CourseInstructorAssignment.isprimary' => 1
                            ),
                            'Staff'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
                    )
                ),
            ),
            //'order' => array('ExamGrade.created' => 'DESC') // will interfere with backdated grade entry, Neway
            'order' => array('ExamGrade.id' => 'DESC')
        ));

        $publishedCourses = array();

        if (!empty($department_action_required_list)) {
            foreach ($department_action_required_list as $key => $grade_change_detail) {
                //Grade change for student course registration
                if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "" && (($department == 1 && $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'] == $col_dpt_id) || ($department == 0 && $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] == $col_dpt_id))) {
                    $publishedCourses[] = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse'];
                }
            }
        }

        return $publishedCourses;
    }


    //Registrar grade  approval
    function getListOfGradeForRegistrarApproval($department_ids = null, $college_ids = null)
    {

        $registrar_action_required_list = $this->find('all', array(
            'conditions' => array(
                'ExamGrade.department_approval = 1',
                'ExamGrade.registrar_approval IS null',
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'CourseInstructorAssignment.isprimary' => 1
                            ),
                            'Staff'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
                    )
                ),
                'CourseAdd' => array(
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'CourseInstructorAssignment.isprimary' => 1
                            ),
                            'Staff'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
                    )
                ),
                'MakeupExam' => array(
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'CourseInstructorAssignment.isprimary' => 1
                            ),
                            'Staff'
                        ),
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'Section' => array('id', 'name'),
                        'Department' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Course' => array('id', 'course_title', 'course_code', 'course_detail_hours', 'credit')
                    )
                ),
            ),
            //'order' => array('ExamGrade.created' => 'DESC') // will interfere with backdated grade entry, Neway
            'order' => array('ExamGrade.id' => 'DESC')
        ));

        $count = 0;
        $publishedCourses = array();

        if (!empty($registrar_action_required_list)) {
            foreach ($registrar_action_required_list as $key => $grade_detail) {
                if (empty($grade_detail['ExamGrade']['registrar_approval']) && $grade_detail['ExamGrade']['department_approval'] == 1) {
                    //Grade change for student course registration
                    if (isset($grade_detail['CourseRegistration']) && !empty($grade_detail['CourseRegistration']) && $grade_detail['CourseRegistration']['id'] != "") {
                        if ((!empty($department_ids) && isset($grade_detail['CourseRegistration']['PublishedCourse']['Department']['id']) && in_array(
                                    $grade_detail['CourseRegistration']['PublishedCourse']['Department']['id'],
                                    $department_ids
                                )) || (!empty($college_ids) && !empty($grade_detail['CourseRegistration']['PublishedCourse']['college_id']) && in_array(
                                    $grade_detail['CourseRegistration']['PublishedCourse']['college_id'],
                                    $college_ids
                                ))) {
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['PublishedCourse'] = $grade_detail['CourseRegistration']['PublishedCourse'];
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['Course'] = $grade_detail['CourseRegistration']['PublishedCourse']['Course'];
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['Program'] = $grade_detail['CourseRegistration']['PublishedCourse']['Program'];
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['ProgramType'] = $grade_detail['CourseRegistration']['PublishedCourse']['ProgramType'];
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['Section'] = $grade_detail['CourseRegistration']['PublishedCourse']['Section'];
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['YearLevel'] = $grade_detail['CourseRegistration']['PublishedCourse']['YearLevel'];
                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['Department'] = $grade_detail['CourseRegistration']['PublishedCourse']['Department'];

                            $publishedCourses[$grade_detail['CourseRegistration']['PublishedCourse']['id']]['CourseInstructorAssignment'] = $grade_detail['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'];
                        }
                    } else {
                        if ((!empty($department_ids) && isset($grade_detail['CourseAdd']['PublishedCourse']['Department']['id']) && in_array(
                                    $grade_detail['CourseAdd']['PublishedCourse']['Department']['id'],
                                    $department_ids
                                )) || (!empty($college_ids) && !empty($grade_detail['CourseAdd']['PublishedCourse']['college_id']) && in_array(
                                    $grade_detail['CourseAdd']['PublishedCourse']['college_id'],
                                    $college_ids
                                ))) {
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['PublishedCourse'] = $grade_detail['CourseAdd']['PublishedCourse'];
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['Course'] = $grade_detail['CourseAdd']['PublishedCourse']['Course'];
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['Program'] = $grade_detail['CourseAdd']['PublishedCourse']['Program'];
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['ProgramType'] = $grade_detail['CourseAdd']['PublishedCourse']['ProgramType'];
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['Section'] = $grade_detail['CourseAdd']['PublishedCourse']['Section'];
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['YearLevel'] = $grade_detail['CourseAdd']['PublishedCourse']['YearLevel'];


                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['CourseInstructorAssignment'] = $grade_detail['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'];
                            $publishedCourses[$grade_detail['CourseAdd']['PublishedCourse']['id']]['Department'] = $grade_detail['CourseAdd']['PublishedCourse']['Department'];
                        }
                    }
                }
                $count++;
            }
        }
        return $publishedCourses;
    }

    public function getMostApprovedGradeForSMS($phoneNumber)
    {

        $studentDetail = ClassRegistry::init('Student')->find(
            'first',
            array('conditions' => array('Student.phone_mobile' => $phoneNumber), 'contain' => array('User'))
        );
        $gradeDetails = '';

        if (!empty($studentDetail)) {
            $mostRecentRegistration = $this->getMostRecentRegistrationDetail($studentDetail['Student']['id']);
            $coursesTaken = $this->getStudentCoursesAndFinalGrade(
                $studentDetail['Student']['id'],
                $mostRecentRegistration['CourseRegistration']['academic_year'],
                $mostRecentRegistration['CourseRegistration']['semester'],
                1
            );
            return $this->formateGradeForSMS($coursesTaken, $studentDetail);
        } else {
            // parent phone number ? what if the parent has more than one child ?
            $parentPhone = ClassRegistry::init('Contact')->find(
                'all',
                array('conditions' => array('Contact.phone_mobile' => $phoneNumber), 'contain' => array('Student'))
            );

            if (!empty($parentPhone)) {
                $allofTheirKids = 'Your child ';
                foreach ($parentPhone as $k => $pv) {
                    $mostRecentRegistration = $this->getMostRecentRegistrationDetail($pv['Student']['id']);
                    $coursesTaken = $this->getStudentCoursesAndFinalGrade(
                        $pv['Contact']['student_id'],
                        $mostRecentRegistration['CourseRegistration']['academic_year'],
                        $mostRecentRegistration['CourseRegistration']['semester'],
                        1
                    );
                    $allofTheirKids .= $this->formateGradeForSMS($coursesTaken, $pv);
                }
                return $allofTheirKids;
            }
        }
        return "You dont have the privilage to view grade.";
    }

    public function getMostRecentRegistrationDetail($studentId)
    {

        $mostRecentRegistration = $this->CourseRegistration->find('first', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $studentId
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course'
                )
            ),
            'order' => array(
                //'CourseRegistration.created' => 'DESC', Interfares with Backdated Grade Entry, Neway
                'CourseRegistration.id' => 'DESC',
                'CourseRegistration.academic_year' => 'DESC',
                'CourseRegistration.semester DESC'
            )
        ));

        return $mostRecentRegistration;
    }

    public function formateGradeForSMS($gradeLists, $studentDetail)
    {

        $display = '';
        $display .= $studentDetail['Student']['first_name'] . ' ' . $studentDetail['Student']['last_name'] . '(' . $studentDetail['Student']['studentnumber'] . ') has scored :-';

        if (isset($gradeLists) && !empty($gradeLists)) {
            foreach ($gradeLists as $k => $value) {
                $display .= $value['course_title'] . ' => ' . $value['grade'] . '  ';
            }
        }

        return $display;
    }

    public function getGradeDetailsForEmailNotification($exam_grade_id)
    {

        $details = $this->find('first', array(
            'conditions' => array(
                'ExamGrade.id' => $exam_grade_id
            ),
            'contain' => array(
                'CourseAdd' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'Course',
                        'CourseInstructorAssignment' => array('Staff')
                    )
                ),
                'MakeupExam' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'Course',
                        'CourseInstructorAssignment' => array('Staff')
                    )
                ),
                'CourseRegistration' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'Course',
                        'CourseInstructorAssignment' => array('Staff')
                    )
                )
            )
        ));

        return $details;
    }

    public function getLetterGradeStatistics($publishedCourseID)
    {

        if (empty($publishedCourseID)) {
            return array();
        }

        $graph['data'] = array();
        $graph['series'] = array();
        $graph['labels'] = array();
        $gradestats['statistics'] = array();

        $publishedCourses_reg = $this->CourseRegistration->find('all', array(
            'conditions' => array(
                "CourseRegistration.published_course_id" => $publishedCourseID
            ),
            'contain' => array(
                'Student',
                'ExamGrade',
                'PublishedCourse' => array(
                    'Section',
                    'YearLevel',
                    'Program',
                    'ProgramType',
                    'Course',
                    'Department'
                )
            )
        ));

        $publishedCourses_add = $this->CourseAdd->find('all', array(
            'conditions' => array(
                "CourseAdd.published_course_id" => $publishedCourseID
            ),
            'contain' => array(
                'Student',
                'ExamGrade',
                'PublishedCourse' => array(
                    'Section',
                    'YearLevel',
                    'Program',
                    'ProgramType',
                    'Course',
                    'Department'
                )
            )
        ));

        $publishedCourses = array_merge($publishedCourses_reg, $publishedCourses_add);

        $organized_Published_courses_by_sections = array();
        $graph['labels'][0] = "Grade";
        //$graph['labels'][1] = "Frequency";

        if (!empty($publishedCourses)) {
            foreach ($publishedCourses as $key => $published_course) {
                if (isset($published_course['CourseRegistration']['id'])) {
                    $gradee = $this->CourseRegistration->ExamGrade->getGradeForStats(
                        $published_course['CourseRegistration']['id'],
                        1
                    );
                } elseif (isset($published_course['CourseAdd']['id'])) {
                    $gradee = $this->CourseRegistration->ExamGrade->getGradeForStats(
                        $published_course['CourseAdd']['id'],
                        0
                    );
                }
                if (!empty($gradee['grade'])) {
                    $graph['series'][$gradee['grade']] = $gradee['grade'];
                    if (isset($graph['data'][$gradee['grade']][0])) {
                        $graph['data'][$gradee['grade']][0] += 1;
                    } else {
                        $graph['data'][$gradee['grade']][0] = 1;
                    }
                    if (isset($gradestats['statistics'][$gradee['grade']])) {
                        $gradestats['statistics'][$gradee['grade']] += 1;
                    } else {
                        $gradestats['statistics'][$gradee['grade']] = 1;
                    }
                }
                //debug($gradestats['statistics']);
            }
        }

        $gradestats['graph'] = $graph;
        return $gradestats;
    }

    //event management system, observer pattern raising event
    public function afterSave($created, $options = array())
    {
        /* parent::afterSave($created, $options);
        if ($created === true) {
            $Event = new Event('Model.ExamGrade.createdModified', $this, array(
                'id' => $this->id,
                // 'data' => $this->data[$this->alias]
            ));
            $this->getEventManager()->dispatch($Event);
        } */
    }

    function repeatationLabeling($register_and_add_freq, $type = 'add', $add_reg_value, $student_detail, $course_id)
    {

        $rep['repeated_old'] = false;
        $rep['repeated_new'] = false;

        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse(
            $course_id,
            $student_detail['Student']['curriculum_id']
        );
        $matching_courses[$course_id] = $course_id;

        $isLastRegistration = $this->isTheLastRegOrAdd(
            $matching_courses,
            $student_detail['Student']['id'],
            $course_id,
            $add_reg_value
        );
        //strike all repetation

        if (!empty($register_and_add_freq)) {
            foreach ($register_and_add_freq as $k) {
                if ($k['id'] == $add_reg_value && strcasecmp($k['type'], $type) == 0 && !$isLastRegistration) {
                    $rep['repeated_old'] = true;
                    $rep['repeated_new'] = false;
                    //return $rep;
                }
            }
        }

        $rep['repeated_new'] = $isLastRegistration;
        return $rep;
    }

    function isTheLastRegOrAdd($matching_courses, $student_id, $course_id, $add_reg_value)
    {

        //$courses_separated_by_coma = join(',', $matching_courses);
        $add_reg_freq = $this->getCourseFrequenceRegAdds($matching_courses, $student_id);
        debug($add_reg_value);

        $lastElement = $add_reg_freq[count($add_reg_freq) - 1];
        debug($lastElement['course_id'] == $course_id && $add_reg_value == $lastElement['id']);

        if ($lastElement['course_id'] == $course_id && $add_reg_value == $lastElement['id']) {
            return true;
        }
        return false;
    }

    function getCourseFrequenceRegAdds($matching_courses, $student_id)
    {

        $courses_separated_by_coma = join(',', $matching_courses);
        //$courses_separated_by_coma = "'" . implode ( "', '", $matching_courses ) . "'";

        //debug($courses_separated_by_coma);

        $add_freq = array();
        $registration_freq = array();
        $register_and_add_freq = array();

        if (isset($matching_courses) && !empty($courses_separated_by_coma)) {
            $registration_freq = $this->CourseRegistration->find('all', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.published_course_id in (select id from published_courses where course_id in (' . $courses_separated_by_coma . '))',
                    'CourseRegistration.id not in (select course_registration_id from course_drops where registrar_confirmation = 1 and department_approval = 1)',
                ),
                'contain' => array('PublishedCourse'),
                //'order' => array('CourseRegistration.created' => 'ASC'), // Grade Entry effect, Neway
                'order' => array('CourseRegistration.id' => 'ASC')
            ));

            $add_freq = $this->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                    'CourseAdd.department_approval = 1',
                    'CourseAdd.registrar_confirmation = 1',
                    'CourseAdd.published_course_id in (select id from published_courses where course_id in (' . $courses_separated_by_coma . '))'
                ),
                'contain' => array('PublishedCourse'),
                //'order' => array('CourseAdd.created' => 'ASC'), // Grade Entry effect, Neway
                'order' => array('CourseAdd.id' => 'ASC')
            ));
        }

        $register_and_add_course_registration = array();
        //merging course registration and add

        if (!empty($registration_freq)) {
            foreach ($registration_freq as $key2 => $value2) {
                if (!empty($value2['PublishedCourse']['id']) && !$this->CourseRegistration->isCourseDroped(
                        $value2['CourseRegistration']['id']
                    )) {
                    $m_index = count($register_and_add_freq);
                    $raacr = $this->getApprovedGrade($value2['CourseRegistration']['id'], 1);

                    if (isset($raacr) && !empty($raacr)) {
                        $register_and_add_course_registration[$value2['PublishedCourse']['course_id']]['register'][$value2['CourseRegistration']['id']] = $raacr['grade'];
                    }

                    $register_and_add_freq[$m_index]['id'] = $value2['CourseRegistration']['id'];
                    $register_and_add_freq[$m_index]['type'] = 'register';
                    $register_and_add_freq[$m_index]['course_id'] = $value2['PublishedCourse']['course_id'];
                    $register_and_add_freq[$m_index]['created'] = $value2['CourseRegistration']['created'];
                }
            }
        }

        if (!empty($add_freq)) {
            foreach ($add_freq as $key2 => $value2) {
                if (!empty($value2['PublishedCourse']['id'])) {
                    $m_index = count($register_and_add_freq);
                    $raaca = $this->getApprovedGrade($value2['CourseAdd']['id'], 0);

                    if (isset($raaca) && !empty($raaca)) {
                        $register_and_add_course_registration[$value2['PublishedCourse']['course_id']]['add'][$value2['CourseAdd']['id']] = $raaca['grade'];
                    }

                    $register_and_add_freq[$m_index]['id'] = $value2['CourseAdd']['id'];
                    $register_and_add_freq[$m_index]['type'] = 'add';
                    $register_and_add_freq[$m_index]['course_id'] = $value2['PublishedCourse']['course_id'];
                    $register_and_add_freq[$m_index]['created'] = $value2['CourseAdd']['created'];
                }
            }
        }

        //Sorting by date
        if (!empty($register_and_add_freq)) {
            for ($i = 0; $i < count($register_and_add_freq); $i++) {
                for ($j = $i + 1; $j < count($register_and_add_freq); $j++) {
                    if ($register_and_add_freq[$i]['created'] > $register_and_add_freq[$j]['created']) {
                        $tmp = $register_and_add_freq[$i];
                        $register_and_add_freq[$i] = $register_and_add_freq[$j];
                        $register_and_add_freq[$j] = $tmp;
                    }
                }
            }
        }

        return $register_and_add_freq;
    }

    function getApprovedThesisGrade($student_id)
    {

        $curr = $this->CourseRegistration->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );

        $student_curriculum_attachments = ClassRegistry::init('CurriculumAttachment')->find(
            'list',
            array(
                'conditions' => array('CurriculumAttachment.student_id' => $student_id),
                'fields' => array('CurriculumAttachment.curriculum_id', 'CurriculumAttachment.curriculum_id'),
                'group' => array('CurriculumAttachment.student_id', 'CurriculumAttachment.curriculum_id')
            )
        );

        if (!empty($curr) && isset($curr['Student']['curriculum_id']) && !empty($curr['Student']['curriculum_id'])) {
            $curriculums_to_look = array();

            $curriculums_to_look[] = $curr['Student']['curriculum_id'];

            if (!empty($student_curriculum_attachments)) {
                $curriculums_to_look = $curriculums_to_look + $student_curriculum_attachments;
            }

            $thesisCourse = $this->CourseRegistration->PublishedCourse->Course->find('list', array(
                'conditions' => array(
                    'Course.thesis' => 1,
                    'Course.curriculum_id' => $curriculums_to_look
                ),
                'fields' => array('Course.id', 'Course.id'),
            ));

            $CourseIdList = implode(', ', $thesisCourse);

            if (!empty($thesisCourse)) {
                $registration = $this->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id,
                        'CourseRegistration.published_course_id in (select id from published_courses where course_id in (' . $CourseIdList . '))'
                    ),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'contain' => array('PublishedCourse' => array('Course'))
                ));
            }
        }

        if (isset($registration) && !empty($registration)) {
            $grade = $this->getApprovedGrade($registration['CourseRegistration']['id'], 1);
            return $grade;
        } else {
            //TO DO: Check from course adds
            return array();
        }
    }

    function getApprovedThesisTitleAndGrade($student_id)
    {

        $curr = $this->CourseRegistration->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );

        $student_curriculum_attachments = ClassRegistry::init('CurriculumAttachment')->find(
            'list',
            array(
                'conditions' => array('CurriculumAttachment.student_id' => $student_id),
                'fields' => array('CurriculumAttachment.curriculum_id', 'CurriculumAttachment.curriculum_id'),
                'group' => array('CurriculumAttachment.student_id', 'CurriculumAttachment.curriculum_id')
            )
        );

        $thesisCourse = array();

        if (!empty($curr) && isset($curr['Student']['curriculum_id']) && !empty($curr['Student']['curriculum_id'])) {
            $curriculums_to_look = array();

            $curriculums_to_look[] = $curr['Student']['curriculum_id'];

            if (!empty($student_curriculum_attachments)) {
                $curriculums_to_look = $curriculums_to_look + $student_curriculum_attachments;
            }

            $thesisCourse = $this->CourseRegistration->PublishedCourse->Course->find('list', array(
                'conditions' => array(
                    'Course.thesis' => 1,
                    'Course.curriculum_id' => $curriculums_to_look
                ),
                'fields' => array('Course.id', 'Course.id'),
            ));

            $CourseIdList = implode(', ', $thesisCourse);

            if (!empty($thesisCourse)) {
                $registration = $this->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id,
                        'CourseRegistration.published_course_id in (select id from published_courses where course_id in (' . $CourseIdList . '))'
                    ),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'contain' => array('PublishedCourse' => array('Course'))
                ));
            }
        }

        if (isset($registration) && !empty($registration)) {
            $grade = $this->getApprovedGrade($registration['CourseRegistration']['id'], 1);
            if (!empty($grade) && !empty($thesisCourse)) {
                $graduationWork = ClassRegistry::init('GraduationWork')->find('first', array(
                    'conditions' => array(
                        'GraduationWork.student_id' => $student_id,
                        'GraduationWork.course_id' => $thesisCourse
                    ),
                    'fields' => array('GraduationWork.id', 'GraduationWork.type', 'GraduationWork.title'),
                    'order' => array('GraduationWork.modified' => 'DESC', 'GraduationWork.id' => 'DESC'),
                    'recursive' => -1
                ));

                if (!empty($graduationWork)) {
                    return array_merge($grade, $graduationWork);
                } else {
                    return $grade;
                }
            } elseif (!empty($grade)) {
                return $grade;
            } else {
                return array();
            }
        } else {
            //TO DO: Check from course adds
            return array();
        }
    }

    function getApprovedExitExamGrade($student_id)
    {

        $registration = array();
        $courseAdd = array();

        $curr = $this->CourseRegistration->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );

        $student_curriculum_attachments = ClassRegistry::init('CurriculumAttachment')->find(
            'list',
            array(
                'conditions' => array('CurriculumAttachment.student_id' => $student_id),
                'fields' => array('CurriculumAttachment.curriculum_id', 'CurriculumAttachment.curriculum_id'),
                'group' => array('CurriculumAttachment.student_id', 'CurriculumAttachment.curriculum_id')
            )
        );

        if (!empty($curr) && isset($curr['Student']['curriculum_id']) && !empty($curr['Student']['curriculum_id'])) {
            $curriculums_to_look = array();

            $curriculums_to_look[] = $curr['Student']['curriculum_id'];

            if (!empty($student_curriculum_attachments)) {
                $curriculums_to_look = $curriculums_to_look + $student_curriculum_attachments;
            }

            $exitExamCourse = $this->CourseRegistration->PublishedCourse->Course->find('list', array(
                'conditions' => array(
                    'Course.exit_exam' => 1,
                    'Course.curriculum_id' => $curriculums_to_look
                ),
                'fields' => array('Course.id', 'Course.id'),
            ));

            $CourseIdList = implode(', ', $exitExamCourse);

            if (!empty($exitExamCourse)) {
                $registration = $this->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id,
                        'CourseRegistration.published_course_id in (select id from published_courses where course_id in (' . $CourseIdList . '))'
                    ),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'contain' => array('PublishedCourse' => array('Course'))
                ));

                isset($registration['PublishedCourse']['course_id']) ? debug(
                    'ExitRegCourseID: ' . $registration['PublishedCourse']['course_id']
                ) : '';

                if (empty($registration)) {
                    $courseAdd = $this->CourseAdd->find('first', array(
                        'conditions' => array(
                            'CourseAdd.student_id' => $student_id,
                            'CourseAdd.department_approval = 1',
                            'CourseAdd.registrar_confirmation = 1',
                            'CourseAdd.published_course_id in (select id from published_courses where course_id in (' . $CourseIdList . '))'
                        ),
                        'order' => array(
                            'CourseAdd.academic_year' => 'DESC',
                            'CourseAdd.semester' => 'DESC',
                            'CourseAdd.id' => 'DESC'
                        ),
                        'contain' => array('PublishedCourse' => array('Course'))
                    ));

                    isset($courseAdd['PublishedCourse']['course_id']) ? debug(
                        'ExitAddCourseID: ' . $courseAdd['PublishedCourse']['course_id']
                    ) : '';
                }
            }
        }

        if (!empty($registration)) {
            $grade = $this->getApprovedGrade($registration['CourseRegistration']['id'], 1);
            $grade = $grade + $registration['PublishedCourse'];
            return $grade;
        } elseif (!empty($courseAdd)) {
            $grade = $this->getApprovedGrade($courseAdd['CourseAdd']['id'], 0);
            $grade = $grade + $courseAdd['PublishedCourse'];
            return $grade;
        }

        return array();
    }

    public function getListOfFXGradeChangeForStudentChoice(
        $student_id,
        $academic_year = null,
        $semester = null,
        $department_id = null
    ) {

        $fxGrade = array();

        if (isset($student_id) && !empty($student_id)) {
            $fx_grade_list_course_reg = $this->CourseRegistration->find('all', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.id in (select course_registration_id from exam_grades where grade = "Fx" and registrar_approval = 1 and registrar_approval = 1)'
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course',
                        'CourseInstructorAssignment'
                    ),
                    'Student',
                    'ExamGrade'
                )
            ));

            //debug($fx_grade_list_course_reg);
            $fx_grade_list_course_add = $this->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                    'CourseAdd.id in (select course_add_id from exam_grades where grade = "Fx" and registrar_approval = 1 and registrar_approval = 1)'
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course'
                    ),
                    'Student',
                    'ExamGrade'
                )
            ));
            debug($fx_grade_list_course_add);
        } elseif (isset($academic_year) && isset($semester) && isset($department_id)) {
            $fx_grade_list_course_reg = $this->CourseRegistration->find('all', array(
                'conditions' => array(
                    'CourseRegistration.academic_year' => $academic_year,
                    'CourseRegistration.semester' => $semester,
                    'PublishedCourse.department_id' => $department_id,
                    'CourseRegistration.id in (select course_registration_id from exam_grades where grade = "Fx" and registrar_approval = 1 and registrar_approval = 1 )'
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course',
                        'CourseInstructorAssignment'
                    ),
                    'Student',
                    'ExamGrade'
                )
            ));

            $fx_grade_list_course_add = $this->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.semester' => $semester,
                    'PublishedCourse.department_id' => $department_id,
                    'CourseAdd.id in (select course_add_id from exam_grades where grade = "Fx" and registrar_approval = 1 and registrar_approval = 1 )'
                ),
                'contain' => array(
                    'PublishedCourse' => array('Course'),
                    'Student',
                    'ExamGrade'
                )
            ));
        }

        if (isset($fx_grade_list_course_reg) && !empty($fx_grade_list_course_reg)) {
            foreach ($fx_grade_list_course_reg as $fk => $fv) {
                $grade = $this->getApprovedGrade($fv['CourseRegistration']['id'], 1);
                $latestGrade['LatestGradeDetail'] = $this->CourseRegistration->getCourseRegistrationLatestGradeDetail(
                    $fv['CourseRegistration']['id']
                );

                if (isset($latestGrade['LatestGradeDetail']['type']) && $latestGrade['LatestGradeDetail']['type'] == "Change") {
                    debug($grade);
                    /*  $fv['Student']['applied_id'] = ClassRegistry::init('FxResitRequest')->fxresetId($fv['CourseRegistration']['id'],0);
                    $fv['Student']['fxgradesubmitted'] = true;
                    $fxGrade[] = $fv; */
                } else {
                    if (isset($grade) && !empty($grade) && ($grade['grade'] == "FX" || $grade['grade'] == "Fx")) {
                        $fv['Student']['applied_id'] = ClassRegistry::init('FxResitRequest')->fxresetId(
                            $fv['CourseRegistration']['id'],
                            1
                        );
                        $fv['Student']['fxgradesubmitted'] = false;
                        $fxGrade[] = $fv;
                    }
                }
            }
        }

        if (isset($fx_grade_list_course_add) && !empty($fx_grade_list_course_add)) {
            foreach ($fx_grade_list_course_add as $fk => $fv) {
                $grade = $this->getApprovedGrade($fv['CourseAdd']['id'], 0);
                $latestGrade['LatestGradeDetail'] = $this->CourseAdd->getCourseAddLatestGradeDetail(
                    $fv['CourseAdd']['id']
                );
                debug($latestGrade);
                if (isset($latestGrade['LatestGradeDetail']['type']) && $latestGrade['LatestGradeDetail']['type'] == "Change") {
                    /* $fv['Student']['applied_id'] = ClassRegistry::init('FxResitRequest')->fxresetId($fv['CourseAdd']['id'],0);
                    $fv['Student']['fxgradesubmitted'] = true;
                    $fxGrade[] = $fv; */
                } else {
                    if (isset($grade) && !empty($grade) && $grade['grade'] == "Fx") {
                        $fv['Student']['applied_id'] = ClassRegistry::init('FxResitRequest')->fxresetId(
                            $fv['CourseAdd']['id'],
                            0
                        );
                        $fv['Student']['fxgradesubmitted'] = false;
                        $fxGrade[] = $fv;
                    }
                }
            }
        }

        return $fxGrade;
    }

    //Automatically converted
    function getListOfNGGrade(
        $academicyear,
        $semester,
        $department_id,
        $program_id,
        $program_type_id,
        $gradeConverted,
        $type = 0
    ) {

        if ($type == 1) {
            $publishedCourseLists = ClassRegistry::init('PublishedCourse')->find('all', array(
                'conditions' => array(
                    'PublishedCourse.semester' => $semester,
                    'PublishedCourse.academic_year' => $academicyear,
                    'PublishedCourse.program_id' => $program_id,
                    'PublishedCourse.program_type_id' => $program_type_id,
                    'PublishedCourse.college_id' => $department_id
                ),
                'contain' => array(
                    'Course',
                    'Program',
                    'ProgramType',
                    'Department' => array('College'),
                    'College',
                    'CourseAdd' => array(
                        'Student',
                        'ExamResult' => array(
                            'conditions' => array(
                                'ExamResult.course_add' => 0
                            ),
                            'order' => array('ExamResult.result' => 'ASC'),
                            'limit' => 1
                        ),
                    ),
                    'CourseRegistration' => array(
                        'Student',
                        'ExamResult' => array(
                            'order' => array('ExamResult.result' => 'ASC'),
                            'limit' => 1
                        )
                    ),
                    'MakeupExam' => array(
                        'Student',
                        'ExamResult' => array(
                            'limit' => 1
                        ),
                    )
                )
            ));
        } else {
            $publishedCourseLists = ClassRegistry::init('PublishedCourse')->find('all', array(
                'conditions' => array(
                    'PublishedCourse.semester' => $semester,
                    'PublishedCourse.academic_year' => $academicyear,
                    'PublishedCourse.program_id' => $program_id,
                    'PublishedCourse.program_type_id' => $program_type_id,
                    'PublishedCourse.department_id' => $department_id
                ),
                'contain' => array(
                    'Course',
                    'Program',
                    'ProgramType',
                    'Department' => array('College'),
                    'CourseAdd' => array(
                        'Student',
                        'ExamResult' => array(
                            'conditions' => array(
                                'ExamResult.course_add' => 0
                            ),
                            'order' => array('ExamResult.result' => 'ASC'),
                            'limit' => 1
                        ),
                    ),
                    'CourseRegistration' => array(
                        'Student',
                        'ExamResult' => array(
                            'order' => array('ExamResult.result' => 'ASC'),
                            'limit' => 1
                        )
                    ),
                    'MakeupExam' => array(
                        'Student',
                        'ExamResult' => array(
                            'limit' => 1
                        ),
                    )
                )
            ));
        }

        $autoConvertedGradeLists = array();

        $applicable_grades = array(
            'I' => 'I',
            'DO' => 'DO',
            'W' => 'W',
            'F' => 'F'
        );

        if (!empty($publishedCourseLists)) {
            foreach ($publishedCourseLists as $pk => $pv) {
                if (!empty($pv['CourseRegistration'])) {
                    foreach ($pv['CourseRegistration'] as $crk => $crv) {
                        if ($crv['Student']['graduated'] == 0) {
                            $autoChange = $this->find('first', array(
                                'conditions' => array(
                                    'ExamGrade.course_registration_id' => $crv['id'],
                                    'ExamGrade.grade' => 'NG',
                                ),
                                'contain' => array(
                                    'ExamGradeChange' => array(
                                        'conditions' => array(
                                            'ExamGradeChange.grade' => (!empty($gradeConverted) ? $gradeConverted : $applicable_grades),
                                            'OR' => array(
                                                'ExamGradeChange.manual_ng_conversion' => 1,
                                                'ExamGradeChange.auto_ng_conversion' => 1,
                                            )
                                        ),
                                        'order' => array('ExamGradeChange.id' => 'DESC')
                                    )
                                )
                            ));

                            if (isset($autoChange['ExamGradeChange']) && !empty($autoChange['ExamGradeChange'])) {
                                $crv['Student']['haveAssesmentData'] = (isset($crv['ExamResult']) && !empty($crv['ExamResult']) ? true : false);
                                $crv['Student']['p_crs_id'] = $pv['PublishedCourse']['id'];

                                $autoChange['Course'] = $pv['Course'];
                                $autoChange['Student'] = $crv['Student'];

                                if (isset($pv['Department']['name']) && !empty($pv['Department']['name'])) {
                                    $autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . $pv['Department']['name'] . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
                                } else {
                                    $autoConvertedGradeLists[$pv/* ['Department'] */
                                    ['College']['name'] . '~' . ($crv['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman') . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
                                }
                            }
                        }
                    }
                }


                if (!empty($pv['CourseAdd'])) {
                    foreach ($pv['CourseAdd'] as $cadk => $cadv) {
                        if ($cadv['Student']['graduated'] == 0) {
                            $autoChange = $this->find('first', array(
                                'conditions' => array(
                                    'ExamGrade.course_add_id' => $cadv['id'],
                                    'ExamGrade.grade' => 'NG',
                                ),
                                'contain' => array(
                                    'ExamGradeChange' => array(
                                        'conditions' => array(
                                            'ExamGradeChange.grade' => (!empty($gradeConverted) ? $gradeConverted : $applicable_grades),
                                            'OR' => array(
                                                'ExamGradeChange.manual_ng_conversion' => 1,
                                                'ExamGradeChange.auto_ng_conversion' => 1,
                                            )
                                        ),
                                        'order' => array('ExamGradeChange.id' => 'DESC')
                                    )
                                )
                            ));

                            if (isset($autoChange['ExamGradeChange']) && !empty($autoChange['ExamGradeChange'])) {
                                $cadv['Student']['haveAssesmentData'] = (isset($cadv['ExamResult']) && !empty($cadv['ExamResult']) ? true : false);
                                $cadv['Student']['p_crs_id'] = $pv['PublishedCourse']['id'];

                                $autoChange['Course'] = $pv['Course'];
                                $autoChange['Student'] = $cadv['Student'];

                                if (isset($pv['Department']['name']) && !empty($pv['Department']['name'])) {
                                    $autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . $pv['Department']['name'] . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
                                } else {
                                    $autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . 'Freshman' . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
                                }
                            }
                        }
                    }
                }

                if (!empty($pv['MakeupExam'])) {
                    foreach ($pv['MakeupExam'] as $mkpk => $mkpv) {
                        if ($mkpv['Student']['graduated'] == 0) {
                            $autoChange = $this->find('first', array(
                                'conditions' => array(
                                    'ExamGrade.makeup_exam_id' => $mkpv['id'],
                                    'ExamGrade.grade' => 'NG',
                                ),
                                'contain' => array(
                                    'ExamGradeChange' => array(
                                        'conditions' => array(
                                            'ExamGradeChange.grade' => (!empty($gradeConverted) ? $gradeConverted : $applicable_grades),
                                            'OR' => array(
                                                'ExamGradeChange.manual_ng_conversion' => 1,
                                                'ExamGradeChange.auto_ng_conversion' => 1,
                                            )
                                        ),
                                        'order' => array('ExamGradeChange.id' => 'DESC')
                                    )
                                )
                            ));

                            if (isset($autoChange['ExamGradeChange']) && !empty($autoChange['ExamGradeChange'])) {
                                $mkpv['Student']['haveAssesmentData'] = (isset($mkpv['ExamResult']) && !empty($mkpv['ExamResult']) ? true : false);
                                $mkpv['Student']['p_crs_id'] = $pv['PublishedCourse']['id'];

                                $autoChange['Course'] = $pv['Course'];
                                $autoChange['Student'] = $mkpv['Student'];

                                if (isset($pv['Department']['name']) && !empty($pv['Department']['name'])) {
                                    $autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . $pv['Department']['name'] . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
                                } else {
                                    $autoConvertedGradeLists[$pv['Department']['College']['name'] . '~' . 'Freshman' . '~' . $pv['Program']['name'] . '~' . $pv['ProgramType']['name']][] = $autoChange;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $autoConvertedGradeLists;
    }

    function isGradeSubmittedForPublishedCourse($published_course_ids)
    {

        $published_courses_student_registred_score_grade = 0;

        $grade_submitted_registred_courses = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.published_course_id' => $published_course_ids,
            ),
            'fields' => array('CourseRegistration.id')
        ));

        if (!empty($grade_submitted_registred_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamGrade.course_registration_id' => $grade_submitted_registred_courses))
            );
            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        //check course adds
        $grade_submitted_add_courses = $this->CourseAdd->find('list', array(
            'conditions' => array(
                'CourseAdd.published_course_id' => $published_course_ids,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation = 1'
            ),
            'fields' => array('CourseAdd.id')
        ));

        if (!empty($grade_submitted_add_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamGrade.course_add_id' => $grade_submitted_add_courses))
            );
            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        return $published_courses_student_registred_score_grade;
    }

    function getMasterSheetRemedial($section_id = null, $academic_year = null, $semester = null)
    {

        $students_and_grades = array();

        $students_in_section = $this->CourseRegistration->Student->Section->StudentsSection->find('all', array(
            'conditions' => array(
                'StudentsSection.section_id' => $section_id
            ),
            'group' => array(
                'StudentsSection.student_id',
                'StudentsSection.section_id'
            ),
            'recursive' => -1,
        ));

        $students_in_section_ids = $this->CourseRegistration->Student->Section->StudentsSection->find('list', array(
            'conditions' => array(
                'StudentsSection.section_id' => $section_id
            ),
            'group' => array(
                'StudentsSection.student_id',
                'StudentsSection.section_id'
            ),
            'fields' => array(
                'StudentsSection.student_id',
                'StudentsSection.student_id'
            ),
            'recursive' => -1,
        ));

        $studentRegisteredCourseForSection = $this->CourseRegistration->find('list', array(
            'conditions' => array(
                'CourseRegistration.section_id' => $section_id
            ),
            'fields' => array(
                'CourseRegistration.student_id',
                'CourseRegistration.section_id'
            ),
            'recursive' => -1,
        ));

        $count = count($students_in_section);

        if (!empty($studentRegisteredCourseForSection)) {
            foreach ($studentRegisteredCourseForSection as $stuId => $sectId) {
                if (!in_array($stuId, $students_in_section_ids) && $sectId == $section_id) {
                    $students_in_section[$count]['StudentsSection']['student_id'] = $stuId;
                    $students_in_section[$count]['StudentsSection']['section_id'] = $sectId;
                    $count++;
                }
            }
        }

        /* Get each student pattern, AY & Semester and list of courses s/he registered and add within the returned AY and semester */
        $registered_courses = array();
        $added_courses = array();

        if (!empty($students_in_section)) {
            foreach ($students_in_section as $key => $section_student) {
                $student_detail = $this->CourseAdd->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $section_student['StudentsSection']['student_id']
                    ),
                    'recursive' => -1
                ));

                $program_type_id = $this->CourseAdd->Student->ProgramTypeTransfer->getStudentProgramType(
                    $student_detail['Student']['id'],
                    $academic_year,
                    $semester
                );

                $program_type_id = $this->CourseAdd->Student->ProgramType->getParentProgramType($program_type_id);
                $pattern = $this->CourseAdd->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                    $student_detail['Student']['program_id'],
                    $program_type_id,
                    $academic_year
                );

                //Retrieving AY and Semester list based on pattern for status
                $ay_and_s_list = array();

                if ($pattern <= 1) {
                    $ay_and_s_list[0]['academic_year'] = $academic_year;
                    $ay_and_s_list[0]['semester'] = $semester;
                } else {
                    $status_prepared = $this->CourseAdd->Student->StudentExamStatus->find('count', array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $student_detail['Student']['id'],
                            'StudentExamStatus.academic_year' => $academic_year,
                            'StudentExamStatus.semester' => $semester
                        ),
                        //'order' => array('StudentExamStatus.created' => 'DESC'),
                        'order' => array(
                            'StudentExamStatus.academic_year' => 'DESC',
                            'StudentExamStatus.semester' => 'DESC',
                            'StudentExamStatus.created' => 'DESC'
                        ),
                        'recursive' => -1,
                    ));

                    if ($status_prepared == 0) {
                        $ay_and_s_list_draft = $this->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToGenerateStatus(
                            $student_detail['Student']['id'],
                            $academic_year,
                            $semester
                        );
                        //If there are lots of semester without status generation. It is to avoid including other semester/s in the current pattern
                        if (count($ay_and_s_list_draft) > $pattern) {
                            for ($i = 0; $i < $pattern; $i++) {
                                $ay_and_s_list[$i] = $ay_and_s_list_draft[$i];
                            }
                        } else {
                            $ay_and_s_list = $ay_and_s_list_draft;
                        }
                    } else {
                        $ay_and_s_list = $this->CourseAdd->Student->StudentExamStatus->getAcadamicYearAndSemesterListToUpdateStatus(
                            $student_detail['Student']['id'],
                            $academic_year,
                            $semester
                        );
                    }
                }

                //Get list of courses a student registered within the pattern AY and semester list
                $options = array();

                if (!empty($ay_and_s_list)) {
                    foreach ($ay_and_s_list as $key => $ay_s) {
                        $options['conditions']['OR'][] = array(
                            'CourseRegistration.academic_year' => $ay_s['academic_year'],
                            'CourseRegistration.semester' => $ay_s['semester'],
                        );
                    }
                }

                $options['conditions']['CourseRegistration.student_id'] = $student_detail['Student']['id'];

                $options['contain'] = array(
                    'PublishedCourse' => array(
                        'Course',
                        'ExamType' => array('order' => 'ExamType.order')
                    )
                );

                $student_course_registrations = $this->CourseRegistration->find('all', $options);

                //List courses the section registered for
                if (!empty($student_course_registrations)) {
                    foreach ($student_course_registrations as $key => $student_course_registration) {
                        if ($student_course_registration['PublishedCourse']['drop'] == 0) {
                            //Avoiding repeated data
                            foreach ($registered_courses as $key2 => $registered_course) {
                                if ($registered_course['id'] == $student_course_registration['PublishedCourse']['Course']['id']) {
                                    continue 2;
                                }
                            }

                            $r_index = count($registered_courses);

                            $registered_courses[$r_index]['id'] = $student_course_registration['PublishedCourse']['Course']['id'];
                            $registered_courses[$r_index]['course_title'] = $student_course_registration['PublishedCourse']['Course']['course_title'];
                            $registered_courses[$r_index]['course_id'] = $student_course_registration['PublishedCourse']['Course']['id'];
                            $registered_courses[$r_index]['course_code'] = $student_course_registration['PublishedCourse']['Course']['course_code'];
                            $registered_courses[$r_index]['credit'] = $student_course_registration['PublishedCourse']['Course']['credit'];
                            $registered_courses[$r_index]['published_course_id'] = $student_course_registration['CourseRegistration']['published_course_id'];
                            $registered_courses[$r_index]['exam_type'] = $student_course_registration['PublishedCourse']['ExamType'];
                        }
                    }
                }
                //List courses the section added for
            }
        }

        //Compiling each registered course grade
        if (!empty($students_in_section)) {
            foreach ($students_in_section as $key => $value) {
                $index = count($students_and_grades);

                $student_detail = $this->CourseRegistration->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $value['StudentsSection']['student_id']
                    ),
                    'recursive' => -1
                ));

                $students_and_grades[$index]['student_id'] = $value['StudentsSection']['student_id'];
                $students_and_grades[$index]['full_name'] = $student_detail['Student']['first_name'] . ' ' . $student_detail['Student']['middle_name'] . ' ' . $student_detail['Student']['last_name'];
                $students_and_grades[$index]['studentnumber'] = $student_detail['Student']['studentnumber'];
                $students_and_grades[$index]['gender'] = $student_detail['Student']['gender'];

                //Exam grade for each course a student registered
                if (!empty($registered_courses)) {
                    foreach ($registered_courses as $key2 => $registered_course) {
                        $registration_id = $this->CourseRegistration->field('id', array(
                            'CourseRegistration.published_course_id' => $registered_course['published_course_id'],
                            'CourseRegistration.student_id' => $value['StudentsSection']['student_id'],
                        ));

                        if (!empty($registration_id)) {
                            //debug($this->getApprovedGrade($registration_id, 1));
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']] = $this->getApprovedGrade(
                                $registration_id,
                                1
                            );
                            //$students_and_grades[$index]['courses']['r-' . $registered_course['id']]['Assesment'] = $this->getExamTypeTranspose($registration_id, $registered_course['published_course_id'], 1);
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['Assesment'] = ClassRegistry::init(
                                'ExamType'
                            )->getAssessementDetailTypeRemedialMasterSheet($registration_id, 1);

                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['registration_id'] = $registration_id;
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['published_c_id'] = $registered_course['published_course_id'];
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['credit'] = $registered_course['credit'];
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['registered'] = true;
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['droped'] = $this->CourseRegistration->isCourseDroped(
                                $registration_id
                            );
                        } else {
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']] = array();
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['Assesment'] = array();
                            $students_and_grades[$index]['courses']['r-' . $registered_course['id']]['registered'] = false;
                        }
                    }
                }
            }
        }

        $master_sheet = array();
        $master_sheet['registered_courses'] = $registered_courses;
        //$master_sheet['added_courses'] = $added_courses;
        $master_sheet['students_and_grades'] = $students_and_grades;

        //debug($master_sheet);

        return $master_sheet;
    }
}
