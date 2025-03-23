<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseAddsTable extends Table
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

        $this->setTable('course_adds');
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
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ExamGrades', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('ExamResults', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('FxResitRequest', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('HistoricalStudentCourseGradeExcludes', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('MakeupExams', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('RejectedExamGrades', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('ResultEntryAssignments', [
            'foreignKey' => 'course_add_id',
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
            ->allowEmptyString('reason');

        $validator
            ->uuid('department_approved_by')
            ->allowEmptyString('department_approved_by');

        $validator
            ->boolean('registrar_confirmation')
            ->allowEmptyString('registrar_confirmation');

        $validator
            ->uuid('registrar_confirmed_by')
            ->allowEmptyString('registrar_confirmed_by');

        $validator
            ->boolean('auto_rejected')
            ->notEmptyString('auto_rejected');

        $validator
            ->boolean('cron_job')
            ->notEmptyString('cron_job');

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
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));

        return $rules;
    }

    function getCourseAddGradeHistory($course_add_id = null)
    {
        $grade_history = array();

        if ($course_add_id != null) {
            $grade_history_row = $this->ExamGrade->find('all', array(
                'conditions' => array(
                    'ExamGrade.course_add_id' => $course_add_id
                ),
                //'order' => array('ExamGrade.created' => 'DESC'), // back dated grade entry affects this, Neway
                'order' => array('ExamGrade.id' => 'DESC'),
                'contain' => array(
                    'ExamGradeChange' => array(
                        //'order' => array('ExamGradeChange.created' => 'ASC'),
                        'order' => array('ExamGradeChange.id' => 'ASC'),
                    ),
                )
            ));

            $count = 0;
            $grade_history[$count]['type'] = 'Add';

            if (count($grade_history_row) > 1) {
                $skip_first = false;
                foreach ($grade_history_row as $key => $rejected_grade) {
                    if (!$skip_first) {
                        $skip_first = true;
                        continue;
                    }

                    $rejected_grade['ExamGrade']['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $rejected_grade['ExamGrade']['department_approved_by']));
                    $rejected_grade['ExamGrade']['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $rejected_grade['ExamGrade']['registrar_approved_by']));
                    $grade_history[$count]['rejected'][] = $rejected_grade['ExamGrade'];
                }
            } else {
                $grade_history[$count]['rejected'] = array();
            }

            if (isset($grade_history_row[0]['ExamGrade'])) {
                $grade_history[$count]['ExamGrade'] = $grade_history_row[0]['ExamGrade'];
            } else {
                $grade_history[$count]['ExamGrade'] = array();
            }

            if (isset($grade_history_row[0]['ExamGradeChange'])) {
                foreach ($grade_history_row[0]['ExamGradeChange'] as $key => $examGradeChange) {
                    $count++;

                    $grade_history[$count]['type'] = 'Change';
                    $examGradeChange['department_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['department_approved_by']));
                    $examGradeChange['college_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['college_approved_by']));
                    $examGradeChange['registrar_approved_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['registrar_approved_by']));
                    //grade scale lately introduce after prerequist bug
                    $examGradeChange['grade_scale_id'] = ClassRegistry::init('ExamGrade')->field('grade_scale_id', array('ExamGrade.id' => $examGradeChange['exam_grade_id']));

                    if ($examGradeChange['manual_ng_converted_by'] != "") {
                        $examGradeChange['manual_ng_converted_by_name'] = ClassRegistry::init('User')->field('full_name', array('User.id' => $examGradeChange['manual_ng_converted_by']));
                    }
                    $grade_history[$count]['ExamGrade'] = $examGradeChange;
                }
            }
        }
        return $grade_history;
    }

    function getExamGradeChangeStatus($exam_grade_change = null, $type = 'simple')
    {
        if (is_array($exam_grade_change)) {
            if (empty($exam_grade_change)) {
                return 'on-process';
            } else {
                if ($exam_grade_change['manual_ng_conversion'] == 1 || $exam_grade_change['auto_ng_conversion'] == 1) {
                    return 'accepted';
                }

                if ($exam_grade_change['initiated_by_department'] == 1 || $exam_grade_change['department_approval'] == 1) {
                    if ($exam_grade_change['college_approval'] == 1 || $exam_grade_change['makeup_exam_result'] != null) {
                        if ($exam_grade_change['registrar_approval'] == 1) {
                            return 'accepted';
                        } elseif ($exam_grade_change['registrar_approval'] == -1) {
                            return 'rejected';
                        } elseif ($exam_grade_change['registrar_approval'] == null) {
                            return 'on-process';
                        }
                    } elseif ($exam_grade_change['college_approval'] == -1) {
                        return 'rejected';
                    } elseif ($exam_grade_change['college_approval'] == null) {
                        return 'on-process';
                    }
                } elseif ($exam_grade_change['department_approval'] == -1) {
                    return 'rejected';
                } elseif ($exam_grade_change['department_approval'] == null) {
                    return 'on-process';
                }
            }
        }
        return 'on-process';
    }

    function getExamGradeStatus($exam_grade = null, $type = 'simple')
    {
        if (is_array($exam_grade)) {
            if (empty($exam_grade)) {
                return 'on-process';
            } else {
                if ($exam_grade['department_approval'] == 1) {
                    if ($exam_grade['registrar_approval'] == 1) {
                        return 'accepted';
                    } elseif ($exam_grade['registrar_approval'] == -1) {
                        return 'rejected';
                    } elseif ($exam_grade['registrar_approval'] == null) {
                        return 'on-process';
                    }
                } elseif ($exam_grade['department_approval'] == -1) {
                    return 'rejected';
                } elseif ($exam_grade['department_approval'] == null) {
                    return 'on-process';
                }
            }
        }
        return 'on-process';
    }

    function isAnyGradeOnProcess($course_add_id = null)
    {
        $grade_histories = $this->getCourseAddGradeHistory($course_add_id);

        if (!empty($grade_histories)) {
            foreach ($grade_histories as $key => $grade_history) {
                if ((strcasecmp($grade_history['type'], 'Add') == 0 && strcasecmp($this->getExamGradeStatus($grade_history['ExamGrade']), 'on-process') == 0) || (strcasecmp($grade_history['type'], 'Change') == 0 && strcasecmp($this->getExamGradeChangeStatus($grade_history['ExamGrade']), 'on-process') == 0)) {
                    return true;
                }
            }
        }

        return false;
    }

    //When it return the grade, it doesn't consider the approval of the course add grade But it evalutes the approval for grade change to return grade
    function getCourseRegistrationLatestGrade($course_add_id = null)
    {
        $grade_histories = $this->getCourseAddGradeHistory($course_add_id);
        $latest_grade = "";

        if (!empty($grade_histories)) {
            foreach ($grade_histories as $key => $grade_history) {
                if (
                    isset($grade_history['ExamGrade']['grade']) && $grade_history['ExamGrade']['grade'] != $latest_grade &&
                    ($grade_history['type'] != 'Change' || (($grade_history['ExamGrade']['department_approval'] == 1 || $grade_history['ExamGrade']['initiated_by_department'] == 1) && $grade_history['ExamGrade']['registrar_approval'] == 1 && $grade_history['ExamGrade']['college_approval'] == 1) || ($grade_history['ExamGrade']['makeup_exam_result'] != null && ($grade_history['ExamGrade']['department_approval'] == 1 || $grade_history['ExamGrade']['initiated_by_department'] == 1) && $grade_history['ExamGrade']['registrar_approval'] == 1))
                ) {
                    $latest_grade = $grade_history['ExamGrade']['grade'];
                }
            }
        }

        return $latest_grade;
    }

    //Return grade detail for course add regardless of its approval state and it may return grade change detail unless it is not fully rejected.
    function getCourseAddLatestGradeDetail($course_add_id = null)
    {
        $grade_histories = $this->getCourseAddGradeHistory($course_add_id);
        $latest_grade_detail = array();

        if (!empty($grade_histories)) {
            foreach ($grade_histories as $key => $grade_history) {
                if (
                    strcasecmp($grade_history['type'], 'Add') == 0 ||
                    (strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] == null && $grade_history['ExamGrade']['department_approval'] != -1 && $grade_history['ExamGrade']['college_approval'] != -1 && $grade_history['ExamGrade']['registrar_approval'] != -1) ||
                    (strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null) ||
                    (strcasecmp($grade_history['type'], 'Change') == 0 && ($grade_history['ExamGrade']['auto_ng_conversion'] == 1 || $grade_history['ExamGrade']['manual_ng_conversion'] == 1))
                ) {
                    $latest_grade_detail = $grade_history;
                }
                if (isset($latest_grade_detail['rejected'])) {
                    unset($latest_grade_detail['rejected']);
                }
            }
        }
        return $latest_grade_detail;
    }

    //Return grade detail for course add only if it is approved by the department and college It also considers fully approved grade changes.
    public function getCourseAddLatestApprovedGradeDetail($course_add_id = null)
    {
        $grade_histories = $this->getCourseAddGradeHistory($course_add_id);
        $latest_grade_detail = array();
        //debug($grade_histories);

        if (!empty($grade_histories)) {
            foreach ($grade_histories as $key => $grade_history) {
                if (
                    ((strcasecmp(
                                $grade_history['type'],
                                'Add'
                            ) == 0 && !empty($grade_history['ExamGrade']) && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) ||
                        (strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] == null && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['college_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) ||
                        (strcasecmp($grade_history['type'], 'Change') == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null && $grade_history['ExamGrade']['initiated_by_department'] == 0 && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1) ||
                        (strcasecmp(
                                $grade_history['type'],
                                'Change'
                            ) == 0 && $grade_history['ExamGrade']['makeup_exam_result'] != null && $grade_history['ExamGrade']['initiated_by_department'] == 1 && $grade_history['ExamGrade']['department_approval'] == 1 && $grade_history['ExamGrade']['registrar_approval'] == 1)) || (isset($grade_history['ExamGrade']['auto_ng_conversion']) && $grade_history['ExamGrade']['auto_ng_conversion'])
                ) {
                    //$latest_grade_detail = $grade_history;
                    //debug($grade_history);
                    if (isset($latest_grade_detail) && !empty($latest_grade_detail)) {
                        if ($grade_history['ExamGrade']['created'] > $latest_grade_detail['ExamGrade']['created']) {
                            $latest_grade_detail = $grade_history;
                        }
                    } else {
                        $latest_grade_detail = $grade_history;
                    }
                }
                if (isset($latest_grade_detail['rejected'])) {
                    unset($latest_grade_detail['rejected']);
                }
            }
        }

        //debug($latest_grade_detail);
        return $latest_grade_detail;
    }

    function getCourseAdds($student_id = null, $ay_and_s_list = array(), $course_id = null, $include_equivalent = 1)
    {
        $course_adds = array();

        if (!empty($student_id)) {
            $options = array();

            if (!empty($ay_and_s_list)) {
                foreach ($ay_and_s_list as $key => $ay_and_s) {
                    $options['conditions']['OR'][] = array(
                        'CourseAdd.academic_year' => $ay_and_s['academic_year'],
                        'CourseAdd.semester' => $ay_and_s['semester']
                    );
                }
            }

            $options['conditions'][] = array(
                'CourseAdd.student_id' => $student_id,
                //'CourseAdd.department_approval = 1',
                //'CourseAdd.registrar_confirmation = 1'
            );

            //$matching_courses = array();
            //$matching_courses[] = $course_id;
            //debug($matching_courses);

            if ($include_equivalent == 1) {
                $student_department = $this->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $student_id
                    ),
                    'recursive' => -1
                ));

                $course_department = $this->PublishedCourse->Course->find('first', array(
                    'conditions' => array(
                        'Course.id' => $course_id
                    ),
                    'contain' => 'Curriculum'
                ));

                debug($course_id);

                if (!empty($student_department['Student']['department_id'])) {
                    // If the course is main course for the department. If it is, then we are going to concentrate on its equivalent.

                    if ($student_department['Student']['department_id'] == $course_department['Curriculum']['department_id'] && $student_department['Student']['curriculum_id'] == $course_department['Course']['curriculum_id']) {
                        //no need to get equivalent course if the course curriculum and attached curriculum is the same

                        /* $course_be_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array(
                            'conditions' => array(  // this was the bug previously which misses "'conditions' =>" and loads all courses from equivalent_courses table and the system takes decades to generete a single student status, foud it while formatting, proper formatting of the code is easy to read detect error and fix., Neway
                                'EquivalentCourse.course_for_substitued_id' => $course_id
                            ),
                            'recursive' => -1
                        ));

                        debug($course_id);
                        debug($course_be_substitueds);

                        if (!empty($course_be_substitueds)) {
                            foreach ($course_be_substitueds as $key => $value) {
                                $matching_courses[] = $value['EquivalentCourse']['course_be_substitued_id'];
                            }
                        } */

                        $matching_courses[] = $course_id;
                    } else {
                        // If the course is from other department then we are going to look for its equivalent department course

                        $matching_courses = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_id, $student_department['Student']['curriculum_id']);

                        // the above code is much efficient and correct than the following block of code to find course_for_substitueds, Neway

                        /* $course_for_substitueds = array();

                        $course_for_substitueds = ClassRegistry::init('EquivalentCourse')->find('all', array(
                            'conditions' => array(
                                'EquivalentCourse.course_be_substitued_id' => $course_id
                            ),
                            'recursive' => -1
                        ));


                        debug($course_for_substitueds);

                        if (!empty($course_for_substitueds)) {
                            foreach ($course_for_substitueds as $key => $value) {
                                $course_detail = $this->PublishedCourse->Course->find('first', array(
                                    'conditions' => array(
                                        'Course.id' => $value['EquivalentCourse']['course_for_substitued_id']
                                    ),
                                    'contain' => array('Curriculum')
                                ));

                                if ($course_detail['Curriculum']['department_id'] == $student_department['Student']['department_id']) {
                                    $matching_courses[] = $value['EquivalentCourse']['course_for_substitued_id'];
                                }
                            }
                        } */
                    }
                }
            } else {
                $matching_courses[] = $course_id;
            }

            $options['order'] = array('CourseAdd.created DESC');
            $options['contain'] = array('PublishedCourse' => array('Course'));

            $course_adds_raw = $this->find('all', $options);

            debug($course_adds_raw);
            debug($matching_courses);

            if (!empty($course_adds_raw)) {
                foreach ($course_adds_raw as $key => $value) {
                    if (($value['PublishedCourse']['add'] == 1 || ($value['CourseAdd']['department_approval'] == 1 && $value['CourseAdd']['registrar_confirmation'] == 1)) && in_array(
                            $value['PublishedCourse']['Course']['id'],
                            $matching_courses
                        )) {
                        $course_adds[] = $value;
                    }
                }
            }
        }
        return $course_adds;
    }

    // registrar 1, department 2

    function courseAddRequestWaitingApproval($department_ids = null, $registrar_department = 1, $college_ids = null, $registrar_college_privilage = 1, $program_id = null, $program_type_id = null, $acy_ranges = null)
    {
        $this->Student->bindModel(array('hasMany' => array('StudentsSection')));

        if ($registrar_department == 1) {
            $options['contain'] = array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'Prerequisite',
                        'fields' => array('id', 'credit', 'course_detail_hours', 'course_title', 'course_code')
                    ),
                    'Section' => array(
                        'Department' => array('id', 'name'),
                        'College' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                        'fields' => array('id', 'name'),
                    ),
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                ),
                'Student' => array(
                    'Department' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'StudentsSection' => array(
                        'conditions' => array(
                            'StudentsSection.archive = 0'
                        )
                    ),
                    'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                    'fields' => array('id', 'full_name',  'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated',' academicyear'),
                    'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
                )
            );

            if (isset($program_id) && !empty($program_id)) {
                $options['conditions'] = array('Student.program_id' => $program_id);
            }

            if (isset($program_id) && !empty($program_id)) {
                $options['conditions'] = array('Student.program_type_id' => $program_type_id);
            }

            $options['conditions']['Student.department_id'] = $department_ids;
            $options['conditions'][] = 'CourseAdd.department_approval = 1';
            $options['conditions'][] = 'CourseAdd.registrar_confirmation is null';
            $options['conditions'][] = 'Student.graduated = 0';
            $options['conditions'][] = 'PublishedCourse.section_id is not null';
            $options['conditions'][] = 'PublishedCourse.course_id is not null';
        } elseif ($registrar_department == 2) {
            $options['contain'] = array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'Prerequisite',
                        'fields' => array('id', 'credit', 'course_detail_hours', 'course_title', 'course_code')
                    ),
                    'Section' => array(
                        'Department' => array('id', 'name'),
                        'College' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                        'fields' => array('id', 'name'),
                    ),
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                ),
                'Student' => array(
                    'Department' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'StudentsSection' => array(
                        'conditions' => array(
                            'StudentsSection.archive = 0'
                        )
                    ),
                    'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                    'fields' => array('id', 'full_name',  'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated',' academicyear'),
                    'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
                )
            );

            $options['conditions'] = array(
                'Student.department_id' => $department_ids,
                'CourseAdd.department_approval is null',
                'CourseAdd.department_approved_by is null',
                'Student.graduated = 0',
                'PublishedCourse.section_id is not null',
                'PublishedCourse.course_id is not null'
            );
        }

        // college
        if (!empty($college_ids) && $registrar_college_privilage == 1) {
            $options['contain'] = array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'Prerequisite',
                        'fields' => array('id', 'credit', 'course_detail_hours', 'course_title', 'course_code')
                    ),
                    'Section' => array(
                        'Department' => array('id', 'name'),
                        'College' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                        'fields' => array('id', 'name'),
                    ),
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                ),
                'Student' => array(
                    'Department' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'StudentsSection' => array(
                        'conditions' => array(
                            'StudentsSection.archive=0'
                        )
                    ),
                    'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                    'fields' => array('id', 'full_name',  'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated',' academicyear'),
                    'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
                )
            );

            $options['conditions'] = array(
                'Student.department_id is null',
                'Student.college_id' => $college_ids,
                'CourseAdd.department_approval is null',
                'Student.graduated = 0',
                'PublishedCourse.section_id is not null',
                'PublishedCourse.course_id is not null'
            );
        }

        // registrar college privilage
        if (!empty($college_ids) && $registrar_college_privilage == 2) {
            $options['contain'] = array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'Prerequisite',
                        'fields' => array('id', 'credit', 'course_detail_hours', 'course_title', 'course_code')
                    ),
                    'Section' => array(
                        'Department' => array('id', 'name'),
                        'College' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                        'fields' => array('id', 'name'),
                    ),
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.semester', 'PublishedCourse.academic_year')
                ),
                'Student' => array(
                    'Department' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'StudentsSection' => array(
                        'conditions' => array(
                            'StudentsSection.archive = 0'
                        )
                    ),
                    'Curriculum' => array('id', 'name', 'year_introduced' ,'type_credit', 'active'),
                    'fields' => array('id', 'full_name',  'gender', 'studentnumber', 'program_id', 'program_type_id', 'department_id', 'college_id', 'graduated',' academicyear'),
                    'order' => array('Student.academicyear' => 'DESC', 'Student.studentnumber' => 'ASC',  'Student.id' => 'ASC', 'Student.full_name' => 'ASC'),
                )
            );

            $options['conditions'] = array(
                'Student.department_id is null',
                'Student.college_id' => $college_ids,
                'CourseAdd.department_approval = 1',
                'CourseAdd.registrar_confirmation is null',
                'CourseAdd.department_approved_by is not null',
                "OR" => array(
                    "CourseAdd.year_level_id is null",
                    "CourseAdd.year_level_id = ''",
                    "CourseAdd.year_level_id = 0",
                ),
                'Student.graduated = 0',
                'PublishedCourse.section_id is not null',
                'PublishedCourse.course_id is not null'
            );
        }

        $courseAdds = array();

        if (!empty($options['conditions'])) {
            if (isset($acy_ranges) && !empty($acy_ranges)) {
                $acy_ranges_by_comma_quoted = "'" . implode("', '", $acy_ranges) . "'";
                $options['conditions'][] = 'CourseAdd.academic_year IN (' . $acy_ranges_by_comma_quoted . ')';
            }

            debug($options['conditions']);

            $courseAdds = $this->find('all', $options);
        }

        return  $courseAdds;
    }


    //count_add_request // registrar=1, department = 2
    function count_add_request($department_ids = null, $registrar = 1, $college_ids = null, $program_id = null, $program_type_id = null, $acy_ranges = null)
    {
        $options = array();

        if (!empty($department_ids) || !empty($college_ids)) {
            if ($registrar == 1) {
                /* if (!empty($department_ids) && is_array($department_ids)) {
                    $activeDepartmentIDs = $this->PublishedCourse->Department->find('list', array('conditions' => array('Department.id' => $department_ids, 'Department.active' => 1)));
                    if (!empty($activeDepartmentIDs)) {
                        $department_ids  = array_keys($activeDepartmentIDs);
                    }
                }

                if (!empty($college_ids) && is_array($college_ids)) {
                    $activeCollegeIDs = $this->PublishedCourse->College->find('list', array('conditions' => array('College.id' => $college_ids, 'College.active' => 1 )));
                    if (!empty($activeCollegeIDs)) {
                        $college_ids = array_keys($activeCollegeIDs);
                    }
                } */

                if (isset($program_id) && !empty($program_id)) {
                    $options['conditions']['Student.program_id'] = $program_id;
                }

                if (isset($program_type_id) && !empty($program_type_id)) {
                    $options['conditions']['Student.program_type_id'] = $program_type_id;
                }

                if (!empty($department_ids)) {
                    $options['conditions']['Student.department_id'] = $department_ids;
                    $options['conditions'][] = 'CourseAdd.department_approval = 1';
                    $options['conditions'][] = 'CourseAdd.registrar_confirmation is null';
                } elseif (!empty($college_ids)) {
                    $options['conditions'] = array(
                        'Student.department_id is null ',
                        'Student.college_id ' => $college_ids,
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation is null',
                        'Student.graduated = 0',
                    );
                }
                //debug($options);
            } elseif ($registrar == 2) {
                $options['conditions'] = array(
                    'Student.department_id' => $department_ids,
                    'CourseAdd.department_approval is null',
                    'CourseAdd.department_approval is null',
                    'Student.graduated = 0',
                );
            } elseif ($registrar == 3) {
                if (!empty($college_ids)) {
                    $options['conditions'] = array(
                        'Student.department_id is null',
                        'Student.college_id' => $college_ids,
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation is null',
                        'Student.graduated = 0',
                    );
                }
            }
        }

        $courseAddCount = 0;

        if (!empty($options['conditions'])) {
            if (isset($acy_ranges) && !empty($acy_ranges)) {
                $acy_ranges_by_comma_quoted = "'" . implode("', '", $acy_ranges) . "'";
                $options['conditions'][] = 'CourseAdd.academic_year IN (' . $acy_ranges_by_comma_quoted . ')';
                $options['conditions'][] = 'CourseAdd.auto_rejected <> 1';
            }

            $this->Student->bindModel(array('hasMany' => array('StudentsSection')));
            $options['group'] = array('CourseAdd.student_id');

            $options['contain'] = array(
                'Student' => array(
                    'StudentsSection' => array(
                        'conditions' => array('StudentsSection.archive = 0'),
                    ),
                    'fields' => array(
                        'id',
                        'full_name',
                        'gender',
                        'studentnumber',
                        'program_id',
                        'program_type_id',
                        'department_id',
                        'college_id',
                        'graduated'
                    )
                )
            );

            $options['recursive'] = -1;

            debug($options);

            $courseAddCount = $this->find('count', $options);
        }

        return  $courseAddCount;
    }

    function reformatApprovalRequest($courseAdds = null, $department_id = null, $current_academic_year = null, $college_id = null)
    {
        $section_organized_published_course = array();

        if (!empty($courseAdds)) {
            //debug($courseAdds[0]);
            foreach ($courseAdds as $pk => &$pv) {
                $pv['Student']['max_load'] = $this->Student->calculateStudentLoad($pv['Student']['id'], $pv['CourseAdd']['semester'], $pv['CourseAdd']['academic_year']);
                $pv['Student']['maximumCreditPerSemester'] =  ClassRegistry::init('AcademicCalendar')->maximumCreditPerSemester($pv['Student']['id']);
                //$pv['Student']['GeneralSetting'] =  ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($pv['Student']['id'])['GeneralSetting'];

                if (isset($pv['PublishedCourse']['Course']['credit']) && ($pv['PublishedCourse']['Course']['credit'] + $pv['Student']['max_load']) > $pv['Student']['maximumCreditPerSemester']) {
                    $pv['Student']['willBeOverMaxLoadWithThisAdd'] = 1;
                    $pv['Student']['overCredit'] = (($pv['PublishedCourse']['Course']['credit'] + $pv['Student']['max_load']) - $pv['Student']['maximumCreditPerSemester']);
                } else {
                    $pv['Student']['willBeOverMaxLoadWithThisAdd'] = 0;
                    $pv['Student']['overCredit'] = 0;
                }

                if (empty($pv['Student']['Department']['name'])) {
                    $section_organized_published_course['Pre/Fresh'][$pv['Student']['Program']['name']][$pv['Student']['ProgramType']['name']][$pv['PublishedCourse']['Section']['name']][] = $pv;
                } else {
                    $section_organized_published_course[$pv['Student']['Department']['name']][$pv['Student']['Program']['name']][$pv['Student']['ProgramType']['name']][$pv['PublishedCourse']['Section']['name']][] = $pv;
                }
            }
        }

        //debug($section_organized_published_course);
        return $section_organized_published_course;
    }

    function courseHasPrerequistAndFullFilled($course, $student_id)
    {
        if (!empty($course['Course']['Prerequisite'])) {
            $ready_registred_course_ids = array();

            $published_courses = $this->PublishedCourse->find('all', array(
                'conditions' => array(
                    'PublishedCourse.department_id' => $course['PublishedCourse']['department_id'],
                    'PublishedCourse.section_id' => $course['PublishedCourse']['section_id'],
                    'PublishedCourse.year_level_id' => $course['PublishedCourse']['year_level_id'],
                    'PublishedCourse.add' => 0,
                    'PublishedCourse.academic_year LIKE' => $course['PublishedCourse']['academic_year'] . '%',
                    'PublishedCourse.semester' => $course['PublishedCourse']['semester']
                ),
                'recursive' => -1
            ));

            if (!empty($published_courses)) {
                foreach ($published_courses as $p => $v) {
                    $ready_registred_course_ids[] = $v['PublishedCourse']['course_id'];
                }
            }

            //if the student is requested  exemption, and approved by department dont register her/him for that particuluar course
            if (ClassRegistry::init('CourseExemption')->isCourseExempted($student_id, $course['PublishedCourse']['course_id']) > 0) {
                return true;
            }


            $passed_count = array();
            $passed_count['passed'] = 0;
            $passed_count['onhold'] = 0;

            if (!empty($course['Course']['Prerequisite'])) {
                foreach ($course['Course']['Prerequisite'] as $preindex => $prevalue) {
                    // check for co-requisite, if passed or taken in same semester allow add
                    if ($prevalue['co_requisite'] == 1) {
                        if (in_array($prevalue['prerequisite_course_id'], $ready_registred_course_ids)) {
                            $passed_count['passed'] = $passed_count['passed'] + 1;
                        } else {
                            $pre_passed = ClassRegistry::init('CourseDrop')->prequisite_taken($student_id, $prevalue['prerequisite_course_id']);
                            if ($pre_passed === true) {
                                $passed_count['passed'] = $passed_count['passed'] + 1;
                            } elseif ($pre_passed == 2) {
                                $passed_count['onhold'] = $passed_count['onhold'] + 1;
                            }
                        }
                    } else {
                        // check for normal prequiste and is the student passed
                        $pre_passed = ClassRegistry::init('CourseDrop')->prequisite_taken($student_id, $prevalue['prerequisite_course_id']);

                        //debug($pre_passed);
                        //debug($prevalue['prerequisite_course_id']);

                        if ($pre_passed === true || $pre_passed == 2) {
                            $passed_count['passed'] = $passed_count['passed'] + 1;
                        } else {
                            //debug($prevalue['prerequisite_course_id']);
                        }
                    }
                }
            }

            //debug($passed_count['passed']);
            // does he/she pass all the prerequist set ?
            if ($passed_count['passed'] == count($course['Course']['Prerequisite'])) {
                return true;
            } else {
                return false;
            }
        }
        // means no prerequiste needed.
        return true;
    }

    function deleteCourseAddIfRegistrationNotPresent($academic_year, $semester)
    {
        $courseAdds = $this->find('list', array(
            'conditions' => array(
                'CourseAdd.academic_year' => $academic_year,
                'CourseAdd.semester' => $semester,
                'CourseAdd.id not in (select course_add_id from exam_grades where course_add_id is not null)',
                'CourseAdd.student_id not in (select student_id from course_registrations where semester="' . $semester . '" and academic_year="' . $academic_year . '")'
            ),
            'fields' => array('CourseAdd.id', 'CourseAdd.id')
        ));

        if (!empty($courseAdds)) {
            return $this->deleteAll(array('CourseAdd.id' => $courseAdds), false);
        }
    }
}
