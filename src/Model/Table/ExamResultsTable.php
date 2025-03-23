<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamResultsTable extends Table
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

        $this->setTable('exam_results');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ExamTypes', [
            'foreignKey' => 'exam_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
        ]);
        $this->belongsTo('CourseAdds', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->belongsTo('MakeupExams', [
            'foreignKey' => 'makeup_exam_id',
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
            ->numeric('result')
            ->requirePresence('result', 'create')
            ->notEmptyString('result');

        $validator
            ->boolean('course_add')
            ->notEmptyString('course_add');

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

        $rules->add($rules->existsIn(['exam_type_id'], 'ExamTypes'));
        $rules->add($rules->existsIn(['course_registration_id'], 'CourseRegistrations'));
        $rules->add($rules->existsIn(['course_add_id'], 'CourseAdds'));
        $rules->add($rules->existsIn(['makeup_exam_id'], 'MakeupExams'));

        return $rules;
    }

    /**
     * Is grade submitted for publishe course
     * return array
     */
    public function isExamResultSubmitted($published_course_ids = null)
    {

        $published_courses_student_registred_score_grade = 0;

        $grade_submitted_registred_courses = $this->CourseRegistration->find(
            'list',
            array(
                'conditions' => array('CourseRegistration.published_course_id' => $published_course_ids),
                'fields' => array('CourseRegistration.id')
            )
        );

        if (!empty($grade_submitted_registred_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamResult.course_registration_id' => $grade_submitted_registred_courses))
            );

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        //check course adds
        $grade_submitted_add_courses = $this->CourseAdd->find(
            'list',
            array(
                'conditions' => array(
                    'CourseAdd.published_course_id' => $published_course_ids,
                    'CourseAdd.department_approval=1',
                    'CourseAdd.registrar_confirmation=1'
                ),
                'fields' => array('CourseAdd.id')
            )
        );

        if (!empty($grade_submitted_add_courses)) {
            $published_courses_student_registred_score_grade = $this->find(
                'count',
                array('conditions' => array('ExamResult.course_add_id' => $grade_submitted_add_courses))
            );

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        return $published_courses_student_registred_score_grade;
    }


    function isStudenSectionChangePossible($student_id = null, $section_id = null)
    {

        $check = $this->CourseRegistration->find(
            'count',
            array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.section_id' => $section_id
                )
            )
        );
        if ($check > 0) {
            return false;
        }
        return true;
    }

    function isRegistredInNameOfSectionAndSubmittedGrade($student_id = null, $section_id = null)
    {

        $course_regitration_ids = $this->CourseRegistration->find(
            'list',
            array(
                'fields' => 'id',
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.section_id' => $section_id
                )
            )
        );

        if (!empty($course_regitration_ids)) {
            $check = $this->CourseRegistration->ExamGrade->find('count', array(
                'conditions' => array(
                    'ExamGrade.course_registration_id' => $course_regitration_ids
                )
            ));
            //if ($check == 0) {
            return true;
            //}
        }
        return false;
    }

    /**
     *Function that calculate grade results and return pass/fail
     */
    public function calculateGradeAndReturnPassOrFail($exam_result = array())
    {

        $test = $this->GradeScale->find('all');
        debug($test);
        return "passed";
    }

    public function getTotalResultGrade($result = null, $published_course_id = null)
    {

        if (!$result) {
            return 'NG';
        } else {
            $grade_scales = $this->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
            foreach ($grade_scales['GradeScaleDetail'] as $gs_key => $grade_scale) {
                if ($result >= $grade_scale['minimum_result'] && $result <= $grade_scale['maximum_result']) {
                    return $grade_scale['grade'];
                }
            }
        }
        return 'NG';
    }

    public function generateCourseGrade($students = array(), $grade_scales = array(), $exam_types = array())
    {

        //debug($students);
        //debug($exam_types);
        $mandatory_exams = array();
        $exam_types_of_published_course = array(); //exam_types of published course
        foreach ($exam_types as $key => $exam_type) {
            if ($exam_type['ExamType']['mandatory'] == 1) {
                $mandatory_exams[] = $exam_type['ExamType']['id'];
            }
            $exam_types_of_published_course[] = $exam_type['ExamType']['id'];
        }
        //  debug($mandatory_exams);
        foreach ($students as $stu_key => $student) {
            //Grade calc for course registration and add
            if (!isset($student['MakeupExam'])) {
                if (!isset($student['ExamGrade']) || empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1) {
                    $exam_sum = 0;
                    $NG = false;
                    $taken_exams = array();
                    $grade = array();
                    foreach ($student['ExamResult'] as $er_key => $examResult) {
                        //if(!in_array($examResult['exam_type_id'], $taken_exams) ) {
                        if (
                            !in_array($examResult['exam_type_id'], $taken_exams) &&
                            in_array($examResult['exam_type_id'], $exam_types_of_published_course)
                        ) {
                            $exam_sum += $examResult['result'];
                            $taken_exams[] = $examResult['exam_type_id'];
                        } else {
                            //If there is duplication, the duplicated result will be deleted
                            $this->delete($examResult['id']);
                        }
                    }
                    foreach ($mandatory_exams as $me_key => $mandatory_exam) {
                        if (!in_array($mandatory_exam, $taken_exams)) {
                            $grade['grade'] = 'NG';
                            break;
                        }
                    }

                    if (empty($grade)) {
                        foreach ($grade_scales['GradeScaleDetail'] as $gs_key => $grade_scale) {
                            if ($exam_sum >= $grade_scale['minimum_result'] && $exam_sum <= $grade_scale['maximum_result']) {
                                $grade['grade'] = $grade_scale['grade'];
                                break;
                            }
                        }
                    }
                    //debug($grade_scales);
                    //debug($grade);
                    if (count($taken_exams) >= count($exam_types)) {
                        $grade['fully_taken'] = true;
                    } else {
                        $grade['fully_taken'] = false;
                    }
                    if (isset($student['CourseRegistration'])) {
                        $grade['course_registration_id'] = $student['CourseRegistration']['id'];
                    } else { //if(isset($student['CourseAdd']))
                        $grade['course_add_id'] = $student['CourseAdd']['id'];
                    }
                    //else
                    //$grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }//End of grade calc for course registration and add
            } else {//Grade calc for makeup exam
                //debug($student);
                //I have to figure out why i put the following condition
                if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1) {
                    $NG = false;
                    $grade = array();
                    if ($student['MakeupExam']['course_registration_id'] != null) {
                        $grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamGrade.course_registration_id' => $student['MakeupExam']['course_registration_id']
                                    ),
                                'order' => array('ExamGrade.created DESC'),
                                'recursive' => -1
                            )
                        );
                        $grade['exam_grade_id'] = $grade['exam_grade_id']['ExamGrade']['id'];
                        //$grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->field('id', array('ExamGrade.course_add_id' => $student['MakeupExam']['course_add_id']));
                    } else {
                        $grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamGrade.course_add_id' => $student['MakeupExam']['course_add_id']
                                    ),
                                'order' => array('ExamGrade.created DESC'),
                                'recursive' => -1
                            )
                        );
                        $grade['exam_grade_id'] = $grade['exam_grade_id']['ExamGrade']['id'];
                        //$grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->field('id', array('ExamGrade.course_registration_id' => $student['MakeupExam']['course_registration_id']));;
                    }
                    $grade['minute_number'] = $student['MakeupExam']['minute_number'];
                    $grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $grade['makeup_exam_result'] = $student['MakeupExam']['result'];
                    $grade['initiated_by_department'] = 0;
                    if ($student['MakeupExam']['result'] == null) {
                        $grade['grade'] = 'NG';
                    }

                    if (!isset($grade['grade'])) {
                        foreach ($grade_scales['GradeScaleDetail'] as $gs_key => $grade_scale) {
                            if ($student['MakeupExam']['result'] >= $grade_scale['minimum_result'] && $student['MakeupExam']['result'] <= $grade_scale['maximum_result']) {
                                $grade['grade'] = $grade_scale['grade'];
                                break;
                            }
                        }
                    }
                    //debug($grade_scales);
                    //debug($grade);
                    if ($student['MakeupExam']['result'] == null) {
                        $grade['fully_taken'] = false;
                    } else {
                        $grade['fully_taken'] = true;
                    }
                    //if($student['MakeupExam']['course_registration_id'] != null)
                    //$grade['course_registration_id'] = $student['MakeupExam']['course_registration_id'];
                    //else if(isset($student['CourseAdd']))
                    //$grade['course_add_id'] = $student['CourseAdd']['id'];
                    //else
                    //$grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }//End of grade calc for makeup exam
            }
        }// End of each student processing (foreach loop)
        /*
        why ? it is nothing checked
        $cccc++;
        if (isset($cccc) && $cccc==3) {
            return $students;
        }
        */
        return $students;
    }

    function generateGradeEntryCourseGrade(
        $students = array(),
        $grade_scales = array()
    ) {

        $exam_types_of_published_course = array();
        foreach ($students as $stu_key => $student) {
            //Grade calc for course registration and add
            if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1) {
                $NG = false;
                $grade = array();
                if (isset($student['ResultEntryAssignment']['course_registration_id'])) {
                    $grade['course_registration_id'] = $student['ResultEntryAssignment']['course_registration_id'];
                } else { //if(isset($student['CourseAdd']))
                    $grade['course_add_id'] = $student['ResultEntryAssignment']['course_add_id'];
                }

                if (
                    $student['ResultEntryAssignment']['result']
                    == null
                ) {
                    $grade['grade'] = 'NG';
                }

                if (!isset($grade['grade'])) {
                    foreach ($grade_scales['GradeScaleDetail'] as $gs_key => $grade_scale) {
                        if ($student['ResultEntryAssignment']['result'] >= $grade_scale['minimum_result'] && $student['ResultEntryAssignment']['result'] <= $grade_scale['maximum_result']) {
                            $grade['grade'] = $grade_scale['grade'];
                            break;
                        }
                    }
                }

                if ($student['ResultEntryAssignment']['result'] == null) {
                    $grade['fully_taken'] = false;
                } else {
                    $grade['fully_taken'] = true;
                }

                $students[$stu_key]['GeneratedExamGrade'] = $grade;
            } //End of grade calc for makeup exam
        }// End of each student processing (foreach loop)
        debug($students);
        return $students;
    }


    public function generateCourseGradeWithOutScale($students = array(), $exam_types = array())
    {

        $mandatory_exams = array();
        $exam_types_of_published_course = array(); //exam_types of published course
        foreach ($exam_types as $key => $exam_type) {
            if ($exam_type['ExamType']['mandatory'] == 1) {
                $mandatory_exams[] = $exam_type['ExamType']['id'];
            }
            $exam_types_of_published_course[] = $exam_type['ExamType']['id'];
        }
        //  debug($mandatory_exams);
        foreach ($students as $stu_key => $student) {
            //Grade calc for course registration and add
            if (!isset($student['MakeupExam'])) {
                if (!isset($student['ExamGrade']) || empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1) {
                    $exam_sum = 0;
                    $NG = false;
                    $taken_exams = array();
                    $grade = array();
                    foreach ($student['ExamResult'] as $er_key => $examResult) {
                        //if(!in_array($examResult['exam_type_id'], $taken_exams) ) {
                        if (
                            !in_array($examResult['exam_type_id'], $taken_exams) &&
                            in_array($examResult['exam_type_id'], $exam_types_of_published_course)
                        ) {
                            $exam_sum += $examResult['result'];
                            $taken_exams[] = $examResult['exam_type_id'];
                        } else {
                            //If there is duplication, the duplicated result will be deleted
                            $this->delete($examResult['id']);
                        }
                    }
                    foreach ($mandatory_exams as $me_key => $mandatory_exam) {
                        if (!in_array($mandatory_exam, $taken_exams)) {
                            $grade['grade'] = 'NG';
                            break;
                        }
                    }

                    if (empty($grade)) {
                        //foreach($grade_scales['GradeScaleDetail'] as $gs_key => $grade_scale){
                        //  if($exam_sum >= $grade_scale['minimum_result'] && $exam_sum <= $grade_scale['maximum_result']) {
                        $grade['grade'] = $exam_sum;
                        //  break;
                        //  }
                        //  }
                    }
                    //debug($grade_scales);
                    //debug($grade);
                    if (count($taken_exams) >= count($exam_types)) {
                        $grade['fully_taken'] = true;
                    } else {
                        $grade['fully_taken'] = false;
                    }
                    if (isset($student['CourseRegistration'])) {
                        $grade['course_registration_id'] = $student['CourseRegistration']['id'];
                    } else { //if(isset($student['CourseAdd']))
                        $grade['course_add_id'] = $student['CourseAdd']['id'];
                    }
                    //else
                    //$grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }//End of grade calc for course registration and add
            } else {//Grade calc for makeup exam
                //debug($student);
                //I have to figure out why i put the following condition
                if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1) {
                    $NG = false;
                    $grade = array();
                    if ($student['MakeupExam']['course_registration_id'] != null) {
                        $grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamGrade.course_registration_id' => $student['MakeupExam']['course_registration_id']
                                    ),
                                'order' => array('ExamGrade.created DESC'),
                                'recursive' => -1
                            )
                        );
                        $grade['exam_grade_id'] = $grade['exam_grade_id']['ExamGrade']['id'];
                        //$grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->field('id', array('ExamGrade.course_add_id' => $student['MakeupExam']['course_add_id']));
                    } else {
                        $grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamGrade.course_add_id' => $student['MakeupExam']['course_add_id']
                                    ),
                                'order' => array('ExamGrade.created DESC'),
                                'recursive' => -1
                            )
                        );
                        $grade['exam_grade_id'] = $grade['exam_grade_id']['ExamGrade']['id'];
                        //$grade['exam_grade_id'] = $this->CourseRegistration->ExamGrade->field('id', array('ExamGrade.course_registration_id' => $student['MakeupExam']['course_registration_id']));;
                    }
                    $grade['minute_number'] = $student['MakeupExam']['minute_number'];
                    $grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $grade['makeup_exam_result'] = $student['MakeupExam']['result'];
                    $grade['initiated_by_department'] = 0;
                    if ($student['MakeupExam']['result'] == null) {
                        $grade['grade'] = 'NG';
                    }

                    if (!isset($grade['grade'])) {
                        // foreach($grade_scales['GradeScaleDetail'] as $gs_key => $grade_scale){
                        //  if($student['MakeupExam']['result'] >= $grade_scale['minimum_result'] && $student['MakeupExam']['result'] <= $grade_scale['maximum_result']) {
                        $grade['grade'] = $student['MakeupExam']['result'];
                        //  break;
                        // }
                        //}
                    }
                    //debug($grade_scales);
                    //debug($grade);
                    if ($student['MakeupExam']['result'] == null) {
                        $grade['fully_taken'] = false;
                    } else {
                        $grade['fully_taken'] = true;
                    }
                    //if($student['MakeupExam']['course_registration_id'] != null)
                    //$grade['course_registration_id'] = $student['MakeupExam']['course_registration_id'];
                    //else if(isset($student['CourseAdd']))
                    //$grade['course_add_id'] = $student['CourseAdd']['id'];
                    //else
                    //$grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }//End of grade calc for makeup exam
            }
        }// End of each student processing (foreach loop)

        return $students;
    }

    public function submitGrade(
        $student_course_register_and_adds,
        $students_course_in_progress,
        $grade_scales,
        $exam_types
    ) {

        //debug($student_course_register_and_adds);
        //debug($grade_scales);
        $exam_grades = array();
        $exam_grade_changes = array();
        $students_register = $this->generateCourseGrade(
            $student_course_register_and_adds['register'],
            $grade_scales,
            $exam_types
        );
        $students_add = $this->generateCourseGrade(
            $student_course_register_and_adds['add'],
            $grade_scales,
            $exam_types
        );
        $students_makeup = $this->generateCourseGrade(
            $student_course_register_and_adds['makeup'],
            $grade_scales,
            $exam_types
        );
        //debug($students_register);
        foreach ($students_register as $key => $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array(
                    $student['Student']['id'],
                    $students_course_in_progress
                )) {
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];
                $exam_grades[] = $student['GeneratedExamGrade'];
            }
        }
        foreach ($students_add as $key => $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array(
                    $student['Student']['id'],
                    $students_course_in_progress
                )) {
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];
                $exam_grades[] = $student['GeneratedExamGrade'];
            }
        }//debug($exam_grades);
        foreach ($students_makeup as $key => $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array(
                    $student['Student']['id'],
                    $students_course_in_progress
                )) {
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];
                $exam_grade_changes[] = $student['GeneratedExamGrade'];
            }
        }
        $grade_submit_status = array();
        if (!empty($exam_grades)) {
            if ($this->CourseRegistration->ExamGrade->saveAll($exam_grades, array('validate' => false))) {
                if (!empty($exam_grade_changes)) {
                    if (!$this->CourseRegistration->ExamGrade->ExamGradeChange->saveAll(
                        $exam_grade_changes,
                        array('validate' => false)
                    )) {
                        $grade_submit_status['error'] = "Exam grade for " . count(
                                $exam_grades
                            ) . " students is submited but faild to record makeup exam grade result. Please make your makeup exam grade submision again.";
                    }
                }
            } else {
                $grade_submit_status['error'] = "Exam grade submision is faild. Please try again.";
            }
        } elseif (!empty($exam_grade_changes)) {
            if (!$this->CourseRegistration->ExamGrade->ExamGradeChange->saveAll(
                $exam_grade_changes,
                array('validate' => false)
            )) {
                $grade_submit_status['error'] = "Makeup exam grade submission is faild. Please try again.";
            }
        }
        $grade_submit_status['course_registration_add'] = $exam_grades;
        $grade_submit_status['makeup_exam'] = $exam_grade_changes;
        return $grade_submit_status;
    }

    public function submitGradeEntryAssignment(
        $student_course_register_and_adds,
        $students_course_in_progress,
        $grade_scales
    ) {

        $exam_grades = array();
        $exam_grade_changes = array();
        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());

        $students_makeup = $this->generateGradeEntryCourseGrade(
            $student_course_register_and_adds['makeup'],
            $grade_scales
        );

        foreach ($students_makeup as $key => $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array(
                    $student['Student']['id'],
                    $students_course_in_progress
                )) {
                //find academic year and semester
                $academicYear = "";
                $semester = "";
                if (isset($student['GeneratedExamGrade']['course_add_id']) && !empty($student['GeneratedExamGrade']['course_add_id'])) {
                    $regAddDetail = $this->CourseAdd->find(
                        'first',
                        array(
                            'conditions' => array('CourseAdd.id' => $student['GeneratedExamGrade']['course_add_id']),
                            'recursive' => -1
                        )
                    );
                    $academicYear = $regAddDetail['CourseAdd']['academic_year'];
                    $semester = $regAddDetail['CourseAdd']['semester'];
                } else {
                    $regAddDetail = $this->CourseRegistration->find(
                        'first',
                        array(
                            'conditions' => array('CourseRegistration.id' => $student['GeneratedExamGrade']['course_registration_id']),
                            'recursive' => -1
                        )
                    );
                    $academicYear = $regAddDetail['CourseRegistration']['academic_year'];
                    $semester = $regAddDetail['CourseRegistration']['semester'];
                }
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];

                $student['GeneratedExamGrade']['created'] = $AcademicYear->getAcademicYearBegainingDate(
                    $academicYear,
                    $semester
                );
                $student['GeneratedExamGrade']['modified'] = $AcademicYear->getAcademicYearBegainingDate(
                    $academicYear,
                    $semester
                );

                $exam_grades[] = $student['GeneratedExamGrade'];
            }
        }

        $grade_submit_status = array();
        if (!empty($exam_grades)) {
            if ($this->CourseRegistration->ExamGrade->saveAll($exam_grades, array('validate' => false))) {
            } else {
                $grade_submit_status['error'] = "Exam grade submision is faild. Please try again.";
            }
        }

        $grade_submit_status['course_registration_add'] = $exam_grades;
        $grade_submit_status['makeup_exam'] = $exam_grade_changes;
        return $grade_submit_status;
    }

    public function getExamGradeSubmissionStatus($published_course_id = null, $student_course_register_and_adds = null)
    {

        $grade_submission_status = array();

        $grade_submission_status['scale_defined'] = false;

        $grade_submission_status['grade_submited'] = false;
        $grade_submission_status['grade_submited_partially'] = false;
        $grade_submission_status['grade_submited_fully'] = false;

        $grade_submission_status['grade_dpt_approved'] = false;
        $grade_submission_status['grade_dpt_approved_partially'] = false;
        $grade_submission_status['grade_dpt_approved_fully'] = false;

        $grade_submission_status['grade_reg_approved'] = false;
        $grade_submission_status['grade_reg_approved_partially'] = false;
        $grade_submission_status['grade_reg_approved_fully'] = false;

        $grade_submission_status['grade_dpt_rejected'] = false;
        $grade_submission_status['grade_dpt_rejected_partially'] = false;
        $grade_submission_status['grade_dpt_rejected_fully'] = false;

        $grade_submission_status['grade_dpt_accepted'] = false;
        $grade_submission_status['grade_dpt_accepted_partially'] = false;
        $grade_submission_status['grade_dpt_accepted_fully'] = false;

        $grade_submission_status['grade_reg_rejected'] = false;
        $grade_submission_status['grade_reg_rejected_partially'] = false;
        $grade_submission_status['grade_reg_rejected_fully'] = false;

        $grade_submission_status['grade_reg_accepted'] = false;
        $grade_submission_status['grade_reg_accepted_partially'] = false;
        $grade_submission_status['grade_reg_accepted_fully'] = false;

        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse(
                $published_course_id
            );
        }

        $count_submited_grade = 0;
        $count_dpt_approved_grade = 0;
        $count_reg_approved_grade = 0;
        $count_dpt_accepted_grade = 0;
        $count_dpt_rejected_grade = 0;
        $count_reg_accepted_grade = 0;
        $count_reg_rejected_grade = 0;

        $students_register = $student_course_register_and_adds['register'];
        $students_add = $student_course_register_and_adds['add'];
        $students_makeup = $student_course_register_and_adds['makeup'];

        foreach ($students_register as $key => $student) {
            if (!empty($student['ExamGrade'])) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    if ($student['ExamGrade'][0]['department_approval'] == 1) {
                        $count_dpt_accepted_grade++;
                    } else {
                        $count_dpt_rejected_grade++;
                    }
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    if ($student['ExamGrade'][0]['registrar_approval'] == 1) {
                        $count_reg_accepted_grade++;
                    } else {
                        $count_reg_rejected_grade++;
                    }
                }
            }
        }

        foreach ($students_add as $key => $student) {
            if (!empty($student['ExamGrade'])) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    if ($student['ExamGrade'][0]['department_approval'] == 1) {
                        $count_dpt_accepted_grade++;
                    } else {
                        $count_dpt_rejected_grade++;
                    }
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    if ($student['ExamGrade'][0]['registrar_approval'] == 1) {
                        $count_reg_accepted_grade++;
                    } else {
                        $count_reg_rejected_grade++;
                    }
                }
            }
        }
        //debug($students_makeup);
        foreach ($students_makeup as $key => $student) {
            if (!empty($student['ExamGradeChange'])) {
                $count_submited_grade++;
                if ($student['ExamGradeChange'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    if ($student['ExamGradeChange'][0]['department_approval'] == 1) {
                        $count_dpt_accepted_grade++;
                    } else {
                        $count_dpt_rejected_grade++;
                    }
                }
                if ($student['ExamGradeChange'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    if ($student['ExamGradeChange'][0]['registrar_approval'] == 1) {
                        $count_reg_accepted_grade++;
                    } else {
                        $count_reg_rejected_grade++;
                    }
                }
            }
        }

        $grade_scale = $this->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
        if (!isset($grade_scale['error'])) {
            $grade_submission_status['scale_defined'] = true;
        }

        if ($count_submited_grade > 0) {
            $grade_submission_status['grade_submited'] = true;
        }
        if ($count_submited_grade == (count($students_add) + count($students_register) + count($students_makeup))) {
            $grade_submission_status['grade_submited_fully'] = true;
        } elseif ($count_submited_grade < (count($students_add) + count($students_register) + count(
                    $students_makeup
                ))) {
            $grade_submission_status['grade_submited_partially'] = true;
        }
        //debug($count_dpt_approved_grade);
        //debug((count($students_add) + count($students_register)));
        //debug($students_add);
        //debug($students_register);
        if ($count_dpt_approved_grade > 0) {
            $grade_submission_status['grade_dpt_approved'] = true;
            if ($count_dpt_approved_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_fully'] = true;
            } elseif ($count_dpt_approved_grade < $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_partially'] = true;
            }
        }

        if ($count_reg_approved_grade > 0) {
            $grade_submission_status['grade_reg_approved'] = true;
            if ($count_reg_approved_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_fully'] = true;
            } elseif ($count_reg_approved_grade < $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_partially'] = true;
            }
        }

        if ($count_dpt_rejected_grade > 0) {
            $grade_submission_status['grade_dpt_rejected'] = true;
            if ($count_dpt_rejected_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_dpt_rejected_partially'] = true;
            }
        }

        if ($count_dpt_accepted_grade > 0) {
            $grade_submission_status['grade_dpt_accepted'] = true;
            if ($count_dpt_accepted_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_accepted_fully'] = false;
            } else {
                $grade_submission_status['grade_dpt_accepted_partially'] = true;
            }
        }

        if ($count_reg_accepted_grade > 0) {
            $grade_submission_status['grade_reg_rejected'] = true;
            if ($count_reg_accepted_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_reg_rejected_partially'] = true;
            }
        }

        if ($count_reg_rejected_grade > 0) {
            $grade_submission_status['grade_reg_accepted'] = false;
            if ($count_reg_rejected_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_accepted_fully'] = false;
            } else {
                $grade_submission_status['grade_reg_accepted_partially'] = false;
            }
        }

        //debug($grade_submission_status);
        return $grade_submission_status;
    }

    public function getExamGradeEntrySubmissionStatus(
        $published_course_id = null,
        $student_course_register_and_adds = null
    ) {

        $grade_submission_status = array();

        $grade_submission_status['scale_defined'] = false;

        $grade_submission_status['grade_submited'] = false;
        $grade_submission_status['grade_submited_partially'] = false;
        $grade_submission_status['grade_submited_fully'] = false;

        $grade_submission_status['grade_dpt_approved'] = false;
        $grade_submission_status['grade_dpt_approved_partially'] = false;
        $grade_submission_status['grade_dpt_approved_fully'] = false;

        $grade_submission_status['grade_reg_approved'] = false;
        $grade_submission_status['grade_reg_approved_partially'] = false;
        $grade_submission_status['grade_reg_approved_fully'] = false;

        $grade_submission_status['grade_dpt_rejected'] = false;
        $grade_submission_status['grade_dpt_rejected_partially'] = false;
        $grade_submission_status['grade_dpt_rejected_fully'] = false;

        $grade_submission_status['grade_dpt_accepted'] = false;
        $grade_submission_status['grade_dpt_accepted_partially'] = false;
        $grade_submission_status['grade_dpt_accepted_fully'] = false;

        $grade_submission_status['grade_reg_rejected'] = false;
        $grade_submission_status['grade_reg_rejected_partially'] = false;
        $grade_submission_status['grade_reg_rejected_fully'] = false;

        $grade_submission_status['grade_reg_accepted'] = false;
        $grade_submission_status['grade_reg_accepted_partially'] = false;
        $grade_submission_status['grade_reg_accepted_fully'] = false;

        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse(
                $published_course_id
            );
        }

        $count_submited_grade = 0;
        $count_dpt_approved_grade = 0;
        $count_reg_approved_grade = 0;
        $count_dpt_accepted_grade = 0;
        $count_dpt_rejected_grade = 0;
        $count_reg_accepted_grade = 0;
        $count_reg_rejected_grade = 0;


        $students_makeup = $student_course_register_and_adds['makeup'];

        foreach ($students_makeup as $key => $student) {
            if (
                !empty($student['ExamGrade']) &&
                !empty($student['CourseRegistration']['id'])
            ) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    if ($student['ExamGrade'][0]['department_approval'] == 1) {
                        $count_dpt_accepted_grade++;
                    } else {
                        $count_dpt_rejected_grade++;
                    }
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    if ($student['ExamGrade'][0]['registrar_approval'] == 1) {
                        $count_reg_accepted_grade++;
                    } else {
                        $count_reg_rejected_grade++;
                    }
                }
            }

            if (
                !empty($student['ExamGrade']) &&
                !empty($student['CourseAdd']['id'])
            ) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    if ($student['ExamGrade'][0]['department_approval'] == 1) {
                        $count_dpt_accepted_grade++;
                    } else {
                        $count_dpt_rejected_grade++;
                    }
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    if ($student['ExamGrade'][0]['registrar_approval'] == 1) {
                        $count_reg_accepted_grade++;
                    } else {
                        $count_reg_rejected_grade++;
                    }
                }
            }
        }


        $grade_scale = $this->CourseRegistration->PublishedCourse->getGradeScaleDetail($published_course_id);
        if (!isset($grade_scale['error'])) {
            $grade_submission_status['scale_defined'] = true;
        }

        if ($count_submited_grade > 0) {
            $grade_submission_status['grade_submited'] = true;
        }
        if ($count_submited_grade == (count($students_makeup))) {
            $grade_submission_status['grade_submited_fully'] = true;
        } elseif ($count_submited_grade < (count($students_makeup))) {
            $grade_submission_status['grade_submited_partially'] = true;
        }

        if ($count_dpt_approved_grade > 0) {
            $grade_submission_status['grade_dpt_approved'] = true;
            if ($count_dpt_approved_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_fully'] = true;
            } elseif ($count_dpt_approved_grade < $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_partially'] = true;
            }
        }

        if ($count_reg_approved_grade > 0) {
            $grade_submission_status['grade_reg_approved'] = true;
            if ($count_reg_approved_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_fully'] = true;
            } elseif ($count_reg_approved_grade < $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_partially'] = true;
            }
        }

        if ($count_dpt_rejected_grade > 0) {
            $grade_submission_status['grade_dpt_rejected'] = true;
            if ($count_dpt_rejected_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_dpt_rejected_partially'] = true;
            }
        }

        if ($count_dpt_accepted_grade > 0) {
            $grade_submission_status['grade_dpt_accepted'] = true;
            if ($count_dpt_accepted_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_accepted_fully'] = false;
            } else {
                $grade_submission_status['grade_dpt_accepted_partially'] = true;
            }
        }

        if ($count_reg_accepted_grade > 0) {
            $grade_submission_status['grade_reg_rejected'] = true;
            if ($count_reg_accepted_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_reg_rejected_partially'] = true;
            }
        }

        if ($count_reg_rejected_grade > 0) {
            $grade_submission_status['grade_reg_accepted'] = false;
            if ($count_reg_rejected_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_accepted_fully'] = false;
            } else {
                $grade_submission_status['grade_reg_accepted_partially'] = false;
            }
        }

        //debug($grade_submission_status);
        return $grade_submission_status;
    }

    function cancelSubmitedGrade($published_course_id = null, $student_course_register_and_adds = null)
    {

        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse(
                $published_course_id
            );
        }

        $exam_grades_for_deletion = array();
        $exam_grade_changes_for_deletion = array();
        $students_register = $student_course_register_and_adds['register'];
        $students_add = $student_course_register_and_adds['add'];
        $students_makeup = $student_course_register_and_adds['makeup'];

        if (!empty($students_register) && is_array($students_register)) {
            foreach ($students_register as $key => $student) {
                if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                }
            }
        }

        if (!empty($students_add) && is_array($students_add)) {
            foreach ($students_add as $key => $student) {
                if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                }
            }
        }

        if (!empty($students_makeup) && is_array($students_makeup)) {
            foreach ($students_makeup as $key => $student) {
                if (isset($student['ExamGradeChange']) && !empty($student['ExamGradeChange']) && $student['ExamGradeChange'][0]['department_approval'] == null) {
                    $exam_grade_changes_for_deletion[] = $student['ExamGradeChange'][0]['id'];
                }
            }
        }

        $grade_cancelation_status = array();

        if (!empty($exam_grades_for_deletion)) {
            if ($this->CourseRegistration->ExamGrade->deleteAll(array('ExamGrade.id' => $exam_grades_for_deletion),
                false)) {
                if (!empty($exam_grade_changes_for_deletion)) {
                    if ($this->CourseRegistration->ExamGrade->ExamGradeChange->deleteAll(
                        array('ExamGradeChange.id' => $exam_grade_changes_for_deletion),
                        false
                    )) {
                        $grade_cancelation_status['error'] = "Exam grade cancelation for " . count(
                                $exam_grades_for_deletion
                            ) . " students is done but faild to cancel makeup exam grade. Please make your makeup exam grade cancelation again.";
                    }
                }
            } else {
                $grade_cancelation_status['error'] = "Exam grade cancelation is faild. Please try again.";
            }
        } elseif (!empty($exam_grade_changes_for_deletion)) {
            if ($this->CourseRegistration->ExamGrade->ExamGradeChange->deleteAll(
                array('ExamGradeChange.id' => $exam_grade_changes_for_deletion),
                false
            )) {
                $grade_cancelation_status['error'] = "Makeup exam grade cancelation is faild. Please try again.";
            }
        }

        $grade_cancelation_status['course_registration_add'] = $exam_grades_for_deletion;
        $grade_cancelation_status['makeup_exam'] = $exam_grade_changes_for_deletion;

        return $grade_cancelation_status;
    }

    function cancelSubmitedGradeEntry($published_course_id = null, $student_course_register_and_adds = null)
    {

        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistration->PublishedCourse->getStudentsRequiresGradeEntryExamPublishedCourse(
                $published_course_id
            );
        }

        $exam_grades_for_deletion = array();
        $exam_grade_changes_for_deletion = array();

        $students_makeup = $student_course_register_and_adds['makeup'];

        if (!empty($students_makeup) && is_array($students_makeup)) {
            foreach ($students_makeup as $key => $student) {
                if (isset($student['CourseRegistration']['ExamGrade']) && !empty($student['CourseRegistration']['ExamGrade']) && $student['CourseRegistration']['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                } elseif (isset($student['CourseAdd']['ExamGrade']) && !empty($student['CourseAdd']['ExamGrade']) && $student['CourseAdd']['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                }
            }
        }

        $grade_cancelation_status = array();

        if (!empty($exam_grades_for_deletion)) {
            if ($this->CourseRegistration->ExamGrade->deleteAll(array('ExamGrade.id' => $exam_grades_for_deletion),
                false)) {
                if (!empty($exam_grade_changes_for_deletion)) {
                    if ($this->CourseRegistration->ExamGrade->ExamGradeChange->deleteAll(
                        array('ExamGradeChange.id' => $exam_grade_changes_for_deletion),
                        false
                    )) {
                        $grade_cancelation_status['error'] = "Exam grade cancelation for " . count(
                                $exam_grades_for_deletion
                            ) . " students is done but faild to cancel exam grade entry. Please make your exam grade entry cancelation again.";
                    }
                }
            } else {
                $grade_cancelation_status['error'] = "Exam grade cancelation is faild. Please try again.";
            }
        }

        $grade_cancelation_status['course_registration_add'] = $exam_grades_for_deletion;
        $grade_cancelation_status['makeup_exam'] = $exam_grade_changes_for_deletion;

        return $grade_cancelation_status;
    }
}
