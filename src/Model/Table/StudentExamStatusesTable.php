<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentExamStatusesTable extends Table
{

    public function initialize(array $config)
    {

        parent::initialize($config);

        $this->setTable('student_exam_statuses');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('AcademicStatuses', [
            'foreignKey' => 'academic_status_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {

        $validator
            ->integer('student_id', 'Must be a valid number')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id');

        $validator
            ->integer('academic_status_id', 'Must be a valid number')
            ->requirePresence('academic_status_id', 'create')
            ->notEmptyString('academic_status_id');

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

        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['academic_status_id'], 'AcademicStatuses'));

        return $rules;
    }

    /**
     * return students exam status based on course registration and their
     * score for the coures they registered of the latest academic year and semester
     * satuses for single student in case the student is allowed to continue his study in
     * any condition including readmission, propation, and any exception if there is.
     * return boolean
     */

    function get_student_exam_status($student_id = null, $academic_year = null, $semester = null)
    {

        $return = $this->isStudentPassed($student_id, $academic_year);
        //debug($return);

        if ($return) {
            if ($return == 1) {
                return 1; // first time
            } elseif ($return == 2) {
                return 2; // status not generated
            } elseif ($return == 3) {
                return 3; // okay
            } elseif ($return == 4) {
                return 4;  // dismissed
            } elseif ($return == 5) {
                return 5;
            }
        } else {
            //check student is treated by exception
            $readmitted = $this->Student->Readmission->isReadmitted($student_id, $academic_year, $semester);
            // TODO 1 will be replace by probation
            $probation = 0;

            if ($readmitted || $probation) {
                return 3;
            }
            return false;
        }
    }


    function getStudentLastExamStatus($student_id = null, $academic_year)
    {

        $return = $this->find(
            'first',
            array(
                'conditions' => array('StudentExamStatus.student_id' => $student_id),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                    'StudentExamStatus.id' => 'DESC'
                )
            )
        );

        if (empty($return)) {
            return 1; // first time
        } elseif ($return['StudentExamStatus']['academic_status_id'] == 4) {
            //check student is treated by exception
            $readmitted = $this->Student->Readmission->is_readmitted($student_id, $academic_year);
            // TODO 1 will be replace by probation
            $probation = 0;
            if ($readmitted || $probation) {
                return 3;
            }
            return 4;  // dismissed
        }
        return 3;
    }


    function academicRulesOfStudents($academic_year)
    {

        $x = $this->Student->find('all', array(
            'joins' => array(
                'table' => 'graduate_lists',
                'type' => 'inner',
                'foreignKey' => false,
                'conditions' => array(
                    'Student.id = GraduateList.student_id'
                )
            )
        ));
    }

    /**
     * return student elegibility for meal service, can be used for dormitory, health,
     * and other service. It returns the elegibility for serivce
     */

    function elegibleForService($student_id = null, $current_academic_year = null)
    {

        $last_registred_semester_academic_year = array();
        $last_status_semester_academic_year = array();

        $list_of_semester_academic_year = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester(
            $student_id
        );

        $last_registred_semester_academic_year = end($list_of_semester_academic_year);


        $student_last_status = $this->find('first', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id,
                'StudentExamStatus.academic_year' => $last_registred_semester_academic_year['academic_year'],
                'StudentExamStatus.semester' => $last_registred_semester_academic_year['semester']
            )
        ));

        if (empty($student_last_status)) {
            if (count($list_of_semester_academic_year) > 1) {
                $last_status_semester_academic_year = $list_of_semester_academic_year[count(
                    $list_of_semester_academic_year
                ) - 2];
                $student_last_status == $this->find('first', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $student_id,
                        'StudentExamStatus.academic_year' => $last_status_semester_academic_year['academic_year'],
                        'StudentExamStatus.semester' => $last_status_semester_academic_year['semester']
                    )
                ));
            }
        } else {
            $last_status_semester_academic_year = $list_of_semester_academic_year[count(
                $list_of_semester_academic_year
            ) - 1];
        }

        // student last status not known
        if (empty($student_last_status)) {
            if ($this->Student->Dismissal->dismissedBecauseOfDiscipelanryAfterRegistrationNotReadmitted(
                $student_id,
                $current_academic_year
            )) {
                $this->invalidate(
                    'error',
                    'The student is dismissed because of disciplinary after registration. S/he is not elegible to get meal service.'
                );
                return 0;
            } elseif ($this->Student->Clearance->clearedAfterRegistration($student_id)) {
                $this->invalidate(
                    'error',
                    'The student is cleared after registration. S/he is not elegible to get meal service.'
                );
                return 0;
            } elseif ($this->Student->DropOut->dropOutAfterLastRegistration($student_id, $current_academic_year)) {
                $this->invalidate(
                    'error',
                    'The student is drop out after registration. S/he is not elegible to get meal service.'
                );
                return 0;
            } else {
                return 1;
            }
        } elseif ($this->isStudentPassed($student_id, $last_status_semester_academic_year['academic_year'])) {
            if ($this->Student->Clearance->withDrawAfterLastStatusButNotReadmitted(
                $student_id,
                $current_academic_year
            )) {
                $this->invalidate(
                    'error',
                    'The student is withdraw after his/her last academic status, and not readmitted. S/he is not elegible to get meal service.'
                );
                return 0;
            } elseif ($this->Student->Dismissal->dismissedBecauseOfDiscipelanryNotReadmitted(
                $student_id,
                $current_academic_year
            )) {
                $this->invalidate(
                    'error',
                    'The student is dismissed because of disciplinary. S/he is not elegible to get meal service.'
                );
                return 0;
            } elseif ($this->Student->DropOut->dropOutAfterLastRegistration($student_id, $current_academic_year)) {
                $this->invalidate(
                    'error',
                    'The student is drop out after last registration. S/he is not elegible to get meal service.'
                );
                return 0;
            } else {
                return 1;
            }
        } else {
            if ($this->Student->Readmission->is_readmitted($student_id, $current_academic_year)) {
                return 1; // allow
            } else {
                $this->invalidate('error', 'The student is dismissed.S/he is not elegible to get meal service.');
                return 0; //deny
            }
        }
    }

    /*
      * 1- dismissed disciplinary after registration not elegible
      * 2- is cleared after registration. S/he is not elegible to get meal service
      * 3- The student is drop out after registration. S/he is not elegible to get meal service
      * 4 - The student is withdraw after his/her last academic status, and not readmitted. S/he is not elegible to get meal service.
      * 5 - The student is dismissed because of disciplinary. S/he is not elegible to get meal service.
      * 6 - The student is drop out after last registration. S/he is not elegible to get meal service.
      * 7 - The student is dismissed.S/he is not elegible to get meal service.
     */

    public function isElegibleForService($student_id, $current_academic_year = null)
    {

        $student_last_status = $this->find(
            'first',
            array(
                'conditions' => array('StudentExamStatus.student_id' => $student_id),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                    'StudentExamStatus.id' => 'DESC'
                )
            )
        );

        // student last status not known
        if (empty($student_last_status)) {
            if ($this->Student->Dismissal->dismissedBecauseOfDiscipelanryAfterRegistrationNotReadmitted(
                $student_id,
                $current_academic_year
            )) {
                return 1;
            } elseif ($this->Student->Clearance->clearedAfterRegistration($student_id)) {
                return 2;
            } elseif ($this->Student->DropOut->dropOutAfterLastRegistration($student_id, $current_academic_year)) {
                return 3;
            } else {
                return 1;
            }
        } elseif ($this->isStudentPassed($student_id, $student_last_status['StudentExamStatus']['academic_year'])) {
            if ($this->Student->Clearance->withDrawAfterLastStatusButNotReadmitted(
                $student_id,
                $current_academic_year
            )) {
                return 4;
            } elseif ($this->Student->Dismissal->dismissedBecauseOfDiscipelanryNotReadmitted(
                $student_id,
                $current_academic_year
            )) {
                return 5;
            } elseif ($this->Student->DropOut->dropOutAfterLastRegistration($student_id, $current_academic_year)) {
                return 6;
            } else {
                // check if there is any status on previous academic year and deny it if there is no

                $previouseAc = $this->getPreviousSemester($current_academic_year);

                $previousStatus = $this->find('first', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $student_id,
                        'StudentExamStatus.academic_status_id !=' => 4,
                        'StudentExamStatus.academic_year' => $previouseAc['academic_year']
                    ),
                    'order' => array(
                        'StudentExamStatus.academic_year' => 'DESC',
                        'StudentExamStatus.semester' => 'DESC',
                        'StudentExamStatus.id' => 'DESC'
                    )
                ));

                if (empty($previousStatus)) {
                    return 7;
                }
                return 1;
            }
        } else {
            if ($this->Student->Readmission->is_readmitted($student_id, $current_academic_year)) {
                return 1; // allow
            } elseif ($this->StudentExamStatus->checkFxPresenseInStatus($student_id) == 0) {
                return 1; //allow
            } else {
                return 7; //deny
            }
        }
    }

    function isStudentPassed($student_id = null, $acadamic_year = null)
    {

        /*
        * 1- First time allow registration
        * 2- Status not generated not allow regisration
        * 3- Allow registration student qualified
        * 4- Dismissed
        * 5-status assignment not done but generated
        */
        $list_of_semester_academic_year = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester(
            $student_id
        );

        // check if there is no list of semester and academic year of student
        if (empty($list_of_semester_academic_year)) {
            return 1;
        } else {
            if (isset($acadamic_year) && !empty($acadamic_year)) {
                // get previous
                $sem = "I";
                foreach ($list_of_semester_academic_year as $k => $v) {
                    if ($v["academic_year"] == $acadamic_year && $sem >= $v['semester']) {
                        $last_registred_semester_academic_year['academic_year'] = $v["academic_year"];
                        $last_registred_semester_academic_year['semester'] = $v["semester"];
                    }
                }
            }

            if (empty($last_registred_semester_academic_year)) {
                $lastIndex = count($list_of_semester_academic_year) - 1;
                $last_registred_semester_academic_year = $list_of_semester_academic_year[$lastIndex];
            }

            //check if any registration or add for the student for previous academic year and semester if none
            // consider it as first time
            $previousACSem = $this->getPreviousSemester(
                $last_registred_semester_academic_year['academic_year'],
                $last_registred_semester_academic_year['semester']
            );

            //check registration and add in that semester, and academic year
            if (isset($previousACSem['academic_year']) && !empty($previousACSem['academic_year'])) {
                $first_added = $this->Student->CourseAdd->find('first', array(
                    'conditions' => array(
                        'CourseAdd.student_id' => $student_id,
                        'CourseAdd.academic_year' => $previousACSem['academic_year'],
                        'CourseAdd.semester' => $previousACSem['semester'],
                        'CourseAdd.department_approval = 1',
                        'CourseAdd.registrar_confirmation = 1'
                    ),
                    'recursive' => -1,
                    'order' => array(
                        'CourseAdd.academic_year' => 'ASC',
                        'CourseAdd.semester' => 'ASC',
                        'CourseAdd.id' => 'ASC'
                    ),
                ));

                $first_registered = $this->Student->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id,
                        'CourseRegistration.academic_year' => $previousACSem['academic_year'],
                        'CourseRegistration.semester' => $previousACSem['semester'],
                    ),
                    'recursive' => -1,
                    'order' => array(
                        'CourseRegistration.academic_year' => 'ASC',
                        'CourseRegistration.semester' => 'ASC',
                        'CourseRegistration.id' => 'ASC'
                    )
                ));

                //first time
                if (empty($first_added) && empty($first_registered)) {
                    return 1;
                }
            }
        }

        //check pattern and got the last status for decision making
        $studentDetail = $this->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );
        $program_type_id = $this->Student->ProgramType->getParentProgramType(
            $studentDetail['Student']['program_type_id']
        );
        $pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
            $studentDetail['Student']['program_id'],
            $program_type_id,
            $last_registred_semester_academic_year['academic_year']
        );
        debug($pattern);

        if ($pattern <= 1) {
            // check if there is any status generated for student ?
            $student_statuses = $this->find('first', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id,
                    'StudentExamStatus.semester' => $last_registred_semester_academic_year['semester'],
                    'StudentExamStatus.academic_year' => $last_registred_semester_academic_year['academic_year'],
                ),
                'recursive' => -1,
                //'order' => array('StudentExamStatus.id' => 'DESC', 'StudentExamStatus.created' => 'DESC', 'StudentExamStatus.academic_status_id' => 'DESC')
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                    'StudentExamStatus.id' => 'DESC',
                    'StudentExamStatus.created' => 'DESC',
                    'StudentExamStatus.academic_status_id' => 'DESC'
                )
            ));
            //debug($student_statuses);
        } else {
            $student_statuses = $this->find('first', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id,
                ),
                'recursive' => -1,
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                    'StudentExamStatus.id' => 'DESC',
                    'StudentExamStatus.created' => 'DESC',
                    'StudentExamStatus.academic_status_id' => 'DESC'
                )
            ));
        }

        if (empty($student_statuses)) {
            // status not generated so you can not register for next semester  but only pass or fail grade type
            $check = $this->onlyRegisteredPassFailGradeType(
                $student_id,
                $last_registred_semester_academic_year['academic_year'],
                $last_registred_semester_academic_year['semester']
            );
            if ($check) {
                return 3;
            }
            return 2; // status not generated so you can not register for next semester
        } elseif (isset($student_statuses['StudentExamStatus']['academic_status_id']) && $student_statuses['StudentExamStatus']['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) {
            return 3;
        } elseif ($this->onlyRegisteredPassFailGradeType(
            $student_id,
            $last_registred_semester_academic_year['academic_year'],
            $last_registred_semester_academic_year['semester']
        )) {
            return 3;
        } elseif ($this->Student->maxCreditExcludingI(
                $student_id,
                $last_registred_semester_academic_year['semester'],
                $last_registred_semester_academic_year['academic_year']
            ) < ClassRegistry::init('AcademicCalendar')->minimumCreditForStatus($student_id)) {
            // taken credit is less than expected
            //ClassRegistry::init('AcademicCalendar')->minimumCreditForStatus($student_id)
            return 3;
        } elseif (!empty($student_statuses['StudentExamStatus']) && is_null(
                $student_statuses['StudentExamStatus']['academic_status_id']
            )) {
            $readmitted = $this->Student->Readmission->is_readmitted($student_id, $acadamic_year);
            if ($readmitted) {
                return 3;
            } else {
                return 5;
            }
        }

        $readmitted = $this->Student->Readmission->is_readmitted($student_id, $acadamic_year);

        if ($readmitted) {
            return 3;
        }

        return 4; // dismissed
    }

    function onlyRegisteredPassFailGradeType($student_id, $academicYear, $semester)
    {

        $registrationList = $this->Student->CourseRegistration->find('all', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.academic_year' => $academicYear,
                'CourseRegistration.semester' => $semester
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course' => array('GradeType')
                )
            )
        ));

        if (!empty($registrationList)) {
            foreach ($registrationList as $k => $v) {
                //if there is one course, needs to be consider for status generation
                if (isset($v['PublishedCourse']['Course']['GradeType']['id']) && $v['PublishedCourse']['Course']['GradeType']['used_in_gpa']) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    function getPreviousSemester($acadamic_year = null, $semester = null)
    {

        $a_y_and_semster = array();

        if ($semester == 'III') {
            $a_y_and_semster['academic_year'] = $acadamic_year;
            $a_y_and_semster['semester'] = 'II';
        } elseif ($semester == 'II') {
            $a_y_and_semster['academic_year'] = $acadamic_year;
            $a_y_and_semster['semester'] = 'I';
        } else {
            $a_y_and_semster['academic_year'] = (substr($acadamic_year, 0, 4) - 1) . '/' . (substr(
                    $acadamic_year,
                    2,
                    2
                ));
            $a_y_and_semster['semester'] = 'III';
        }
        return $a_y_and_semster;
    }

    function getNextSemster($acadamic_year = null, $semester = null)
    {

        $a_y_and_semster = array();

        if ($semester == 'I') {
            $a_y_and_semster['academic_year'] = $acadamic_year;
            $a_y_and_semster['semester'] = 'II';
        } elseif ($semester == 'II') {
            $a_y_and_semster['academic_year'] = $acadamic_year;
            $a_y_and_semster['semester'] = 'III';
        } else {
            $a_y_and_semster['academic_year'] = (substr($acadamic_year, 0, 4) + 1) . '/' . substr(
                    (substr($acadamic_year, 0, 4) + 2),
                    2,
                    2
                );
            $a_y_and_semster['semester'] = 'I';
        }
        return $a_y_and_semster;
    }

    function getAcadamicYearAndSemesterListToGenerateStatus($student_id = null, $acadamic_year = null, $semester = null)
    {

        $ay_and_s_list = array();
        $next_ay_and_s = array();

        $last_exam_status = $this->find('first', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id
            ),
            'recursive' => -1,
            //'order' => array('StudentExamStatus.created' => 'DESC')
            'order' => array(
                'StudentExamStatus.academic_year' => 'DESC',
                'StudentExamStatus.semester' => 'DESC',
                'StudentExamStatus.id' => 'DESC',
                'StudentExamStatus.created' => 'DESC'
            )
        ));

        //debug($last_exam_status);

        $prepared = $this->find('count', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id,
                'StudentExamStatus.academic_year' => $acadamic_year,
                'StudentExamStatus.semester' => $semester
            ),
            'recursive' => -1,
            'order' => array(
                'StudentExamStatus.academic_year' => 'DESC',
                'StudentExamStatus.semester' => 'DESC',
                'StudentExamStatus.id' => 'DESC',
                'StudentExamStatus.created' => 'DESC'
            )
            //'order' => array('StudentExamStatus.created' => 'DESC')
        ));

        //debug($prepared);
        //debug($last_exam_status);

        if ($prepared > 0) {
            return $ay_and_s_list;
        } elseif (empty($last_exam_status)) {
            $first_added = $this->Student->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                    //'CourseAdd.department_approval=1',
                    'CourseAdd.registrar_confirmation = 1'
                ),
                'contain' => array('PublishedCourse'),
                //'order' => array('CourseAdd.created' => 'ASC'),
                'order' => array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                ),
            ));

            if (!empty($first_added)) {
                foreach ($first_added as $key => $value) {
                    if ($value['PublishedCourse']['add'] == 1 || ($value['CourseAdd']['department_approval'] == 1 && $value['CourseAdd']['registrar_confirmation'] == 1)) {
                        $first_added = $value;
                        unset($first_added['PublishedCourse']);
                        break;
                    }
                }
            }

            $first_registered = $this->Student->CourseRegistration->find('first', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                ),
                'recursive' => -1,
                //'order' => array('CourseRegistration.created' => 'ASC')
                'order' => array(
                    'CourseRegistration.academic_year' => 'ASC',
                    'CourseRegistration.semester' => 'ASC',
                    'CourseRegistration.id' => 'ASC'
                )
            ));

            // incase of withdrawal for the first time without status
            $withdrawlForTheFirstTimeAfterRegistration = $this->Student->Clearance->withDrawaAfterFirstTimeRegistration(
                $student_id,
                $acadamic_year,
                $semester
            );
            //debug($first_registered);

            //If the status generation is for the first time
            if (empty($first_added) && empty($first_registered)) {
                return $next_ay_and_s;
                /*** If the course registration comes first, then we need to start the status generation from the earliest possible time ***/
            } elseif (isset($first_registered['CourseRegistration']) && !empty($first_registered['CourseRegistration']) && (((!isset($first_added['CourseAdd']) || empty($first_added['CourseAdd']))) || (isset($first_added['CourseAdd']) && !empty($first_added['CourseAdd']) && ($first_added['CourseAdd']['created'] > $first_registered['CourseRegistration']['created'])))) {
                $previous_ay_and_s = $this->getPreviousSemester(
                    $first_registered['CourseRegistration']['academic_year'],
                    $first_registered['CourseRegistration']['semester']
                );
                $next_ay_and_s['academic_year'] = $previous_ay_and_s['academic_year'];
                $next_ay_and_s['semester'] = $previous_ay_and_s['semester'];
                // debug($next_ay_and_s);
            } else {
                $previous_ay_and_s = $this->getPreviousSemester(
                    $first_added['CourseAdd']['academic_year'],
                    $first_added['CourseAdd']['semester']
                );
                $next_ay_and_s['academic_year'] = $previous_ay_and_s['academic_year'];
                $next_ay_and_s['semester'] = $previous_ay_and_s['semester'];
                //debug($previous_ay_and_s);
            }
        } else {
            $next_ay_and_s['academic_year'] = $last_exam_status['StudentExamStatus']['academic_year'];
            $next_ay_and_s['semester'] = $last_exam_status['StudentExamStatus']['semester'];
            //debug($last_exam_status);
        }

        // debug($next_ay_and_s);
        // debug($acadamic_year);
        // debug($semester);
        $count = 1;

        do {
            $count++;

            if ($count > 100) {
                break;
            }

            $next_ay_and_s = $this->getNextSemster($next_ay_and_s['academic_year'], $next_ay_and_s['semester']);
            //debug($next_ay_and_s);

            $course_registered = $this->Student->CourseRegistration->find('count', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.academic_year' => $next_ay_and_s['academic_year'],
                    'CourseRegistration.semester' => $next_ay_and_s['semester'],
                )
            ));

            // debug($course_registered);
            // debug($next_ay_and_s);

            $course_adds = $this->Student->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                    'CourseAdd.academic_year' => $next_ay_and_s['academic_year'],
                    'CourseAdd.semester' => $next_ay_and_s['semester'],
                ),
                'order' => array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                ),
                'contain' => array('PublishedCourse')
            ));
            //debug($course_adds);

            $course_added = 0;

            if (!empty($course_adds)) {
                foreach ($course_adds as $key => $value) {
                    if ($value['PublishedCourse']['add'] == 1 || ($value['CourseAdd']['department_approval'] == 1 && $value['CourseAdd']['registrar_confirmation'] == 1)) {
                        $course_added++;
                    }
                }
            }
            //debug($course_added);

            if ($course_registered > 0 || $course_added > 0) {
                $index = count($ay_and_s_list);
                $ay_and_s_list[$index]['academic_year'] = $next_ay_and_s['academic_year'];
                $ay_and_s_list[$index]['semester'] = $next_ay_and_s['semester'];
            }
        } while (!(strcasecmp($acadamic_year, $next_ay_and_s['academic_year']) == 0 && strcasecmp(
                $semester,
                $next_ay_and_s['semester']
            ) == 0));

        //debug($count);
        //debug($ay_and_s_list);

        //first
        if (empty($ay_and_s_list) && empty($last_exam_status)) {
            $index = count($ay_and_s_list);
            $first = $this->getStudentFirstAyAndSemester($student_id);
            $ay_and_s_list[$index]['academic_year'] = $first['academic_year'];
            $ay_and_s_list[$index]['semester'] = $first['semester'];
        }
        //debug($ay_and_s_list);

        if (!empty($ay_and_s_list)) {
            foreach ($ay_and_s_list as $k => &$v) {
                $withdrawlAfterRegistration = $this->Student->Clearance->withDrawaAfterRegistration(
                    $student_id,
                    $v['academic_year'],
                    $v['semester']
                );
                $gradeSubmittedForAnyCourse = ClassRegistry::init('ExamGrade')->gradeSubmittedForAYSem(
                    $student_id,
                    $v['academic_year'],
                    $v['semester']
                );

                $withdrawlAfterRegistration = true;

                if ($withdrawlAfterRegistration && $gradeSubmittedForAnyCourse == 0) {
                    //debug($withdrawlAfterRegistration);
                    unset($ay_and_s_list[$k]);
                }
            }
        }

        debug($ay_and_s_list);
        return $ay_and_s_list;
    }

    function getStudentFirstAyAndSemester($student_id = null, $admissionAY = null)
    {

        $ac_semester_list = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester($student_id);
        $ay_semester = array();

        if (!empty($ac_semester_list)) {
            foreach ($ac_semester_list as $key => $value) {
                $ay_semester['academic_year'] = $value['academic_year'];
                $ay_semester['semester'] = $value['semester'];
                break;
            }
        }

        if (empty($ay_semester) && !empty($admissionAY)) {
            $ay_semester['academic_year'] = $admissionAY;
            $ay_semester['semester'] = "I";
        }

        return $ay_semester;
    }

    function getAcadamicYearAndSemesterListToUpdateStatus($student_id = null, $acadamic_year = null, $semester = null)
    {

        $ay_and_s_list = array();
        $next_ay_and_s = array();

        $student_statuses = $this->find('all', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id,
            ),
            //'order' => 'StudentExamStatus.created ASC',
            'order' => array(
                'StudentExamStatus.academic_year' => 'ASC',
                'StudentExamStatus.semester' => 'ASC',
                'StudentExamStatus.id' => 'ASC',
                'StudentExamStatus.created' => 'ASC'
            ),
            'recursive' => -1
        ));

        $last_exam_status = array();

        if (!empty($student_statuses)) {
            foreach ($student_statuses as $key => $student_status) {
                if (strcasecmp(
                        $student_status['StudentExamStatus']['academic_year'],
                        $acadamic_year
                    ) == 0 && strcasecmp($student_status['StudentExamStatus']['semester'], $semester) == 0) {
                    break;
                }
                $last_exam_status = $student_status['StudentExamStatus'];
            }
        }

        if (empty($last_exam_status)) {
            $first_added = $this->Student->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                ),
                'contain' => array('PublishedCourse'),
                //'order' => array('CourseAdd.created' => 'ASC')
                'order' => array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                )
            ));

            if (!empty($first_added)) {
                foreach ($first_added as $key => $value) {
                    if ($value['PublishedCourse']['add'] == 1 || ($value['CourseAdd']['department_approval'] == 1 && $value['CourseAdd']['registrar_confirmation'] == 1)) {
                        $first_added = $value;
                        unset($first_added['PublishedCourse']);
                        break;
                    }
                }
            }

            $first_registered = $this->Student->CourseRegistration->find('first', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                ),
                'recursive' => -1,
                //'order' => array('CourseRegistration.created' => 'ASC')
                'order' => array(
                    'CourseRegistration.academic_year' => 'ASC',
                    'CourseRegistration.semester' => 'ASC',
                    'CourseRegistration.id' => 'ASC'
                )
            ));

            //If the status generation is for the first time
            if (empty($first_added) && empty($first_registered)) {
                return $next_ay_and_s;
            } elseif (isset($first_registered['CourseRegistration']) && !empty($first_registered['CourseRegistration']) && (((!isset($first_added['CourseAdd']) || empty($first_added['CourseAdd']))) || (isset($first_added['CourseAdd']) && !empty($first_added['CourseAdd']) && $first_added['CourseAdd']['created'] > $first_registered['CourseRegistration']['created']))) {
                //If the course registration comes first, then we need to start the status generation from the earliest possible time ***/
                $previous_ay_and_s = $this->getPreviousSemester(
                    $first_registered['CourseRegistration']['academic_year'],
                    $first_registered['CourseRegistration']['semester']
                );
                $next_ay_and_s['academic_year'] = $previous_ay_and_s['academic_year'];
                $next_ay_and_s['semester'] = $previous_ay_and_s['semester'];
            } else {
                $previous_ay_and_s = $this->getPreviousSemester(
                    $first_added['CourseAdd']['academic_year'],
                    $first_added['CourseAdd']['semester']
                );
                $next_ay_and_s['academic_year'] = $previous_ay_and_s['academic_year'];
                $next_ay_and_s['semester'] = $previous_ay_and_s['semester'];
            }
        } else {
            $next_ay_and_s['academic_year'] = $last_exam_status['academic_year'];
            $next_ay_and_s['semester'] = $last_exam_status['semester'];
        }

        $count = 1;

        do {
            $count++;

            if ($count > 100) {
                break;
                //exit();
            }

            $next_ay_and_s = $this->getNextSemster($next_ay_and_s['academic_year'], $next_ay_and_s['semester']);

            $course_registered = $this->Student->CourseRegistration->find('count', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.academic_year' => $next_ay_and_s['academic_year'],
                    'CourseRegistration.semester' => $next_ay_and_s['semester'],
                ),
                'recursive' => -1
            ));

            $course_adds = $this->Student->CourseAdd->find('all', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id,
                    'CourseAdd.academic_year' => $next_ay_and_s['academic_year'],
                    'CourseAdd.semester' => $next_ay_and_s['semester'],
                ),
                'order' => array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                ),
                'contain' => array('PublishedCourse')
            ));

            $course_added = 0;

            if (!empty($course_adds)) {
                foreach ($course_adds as $key => $value) {
                    if ($value['PublishedCourse']['add'] == 1 || ($value['CourseAdd']['department_approval'] == 1 && $value['CourseAdd']['registrar_confirmation'] == 1)) {
                        $course_added++;
                    }
                }
            }

            if ($course_registered > 0 || $course_added > 0) {
                $index = count($ay_and_s_list);
                $ay_and_s_list[$index]['academic_year'] = $next_ay_and_s['academic_year'];
                $ay_and_s_list[$index]['semester'] = $next_ay_and_s['semester'];
            }
        } while (!(strcasecmp($acadamic_year, $next_ay_and_s['academic_year']) == 0 && strcasecmp(
                $semester,
                $next_ay_and_s['semester']
            ) == 0));

        return $ay_and_s_list;
    }

    function studentYearAndSemesterLevelOfStatus($student_id, $acadamic_year, $semester)
    {

        $student_statuses = $this->find('all', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id,
                'StudentExamStatus.academic_status_id != 4 and StudentExamStatus.academic_status_id is not null '
            ),
            'order' => array(
                'StudentExamStatus.academic_year' => 'ASC',
                'StudentExamStatus.semester' => 'ASC'
            ),
            'recursive' => -1
        ));

        $semester_count = 0;

        if (!empty($student_statuses)) {
            foreach ($student_statuses as $key => $student_status) {
                if (strcasecmp(
                        $student_status['StudentExamStatus']['academic_year'],
                        $acadamic_year
                    ) == 0 && strcasecmp($student_status['StudentExamStatus']['semester'], $semester) == 0) {
                    break;
                } else {
                    $semester_count++;
                }
            }
        }

        $year_level = ((int)($semester_count / 2)) + 1;
        // counting semesters and dividing by 2 will not be correct always,
        // there are 3 semsters in their statuses and some have only 1 semester per year, Summer,
        // a method from Section MODEL is much better than this ClassRegistry::init('Section')->getStudentYearLevel($student_id),
        // this is also not coorrect but works for most of our cases( one can add a student in 5th year and registering for one course doesn't make him a 5 year studen.
        //  we need more robust and efficient code that uses curriculum, registrations and grades to determine year level of the student  and updates it in student table via scheduled task.), Neway


        if ($semester_count % 2 > 0) {
            $semster_level = 'II';
        } else {
            $semster_level = 'I';
        }

        $status_level['year'] = $year_level;
        $status_level['semester'] = $semster_level;

        return $status_level;
    }

    function isThereTcwRuleInDismisal(
        $student_id = null,
        $program_id = null,
        $academic_year = null,
        $semester = null,
        $year_level = null,
        $semester_level = null,
        $admissionyear = null
    ) {

        $academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
            'conditions' => array(
                'AcademicStand.academic_status_id' => 4,
                'AcademicStand.program_id' => $program_id
            ),
            'order' => array('AcademicStand.academic_year_from' => 'ASC'),
            'recursive' => -1
        ));
        $as = null;

        if (!empty($academic_stands)) {
            foreach ($academic_stands as $key => $academic_stand) {
                $stand_year_levels = unserialize($academic_stand['AcademicStand']['year_level_id']);
                $stand_semesters = unserialize($academic_stand['AcademicStand']['semester']);
                //Student acadamic stand searching by year and semster level for status
                if (in_array($year_level, $stand_year_levels) && in_array($semester_level, $stand_semesters)) {
                    //Checking if the acadamic stand is applicable to the student
                    if ((substr(
                                $admissionyear,
                                0,
                                4
                            ) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1 && substr(
                                $academic_year,
                                0,
                                4
                            ) >= $academic_stand['AcademicStand']['academic_year_from'])) {
                        $as = $academic_stand['AcademicStand'];
                    }
                }
            } //End of acadamic stands searching (loop)
        }

        if (!empty($as)) {
            //Searching for the rule by the acadamic stand
            $acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
                'conditions' => array(
                    'AcademicRule.academic_stand_id' => $as['id']
                ),
                'recursive' => -1
            ));
            debug($acadamic_rules);
            //If acadamic rule is found
            if (!empty($acadamic_rules)) {
                foreach ($acadamic_rules as $key => $acadamic_rule) {
                    if ($acadamic_rule['AcademicRule']['tcw'] == 1) {
                        //return true;
                        return 1;
                    }
                }
            }
        }
        //return false;
        return 0;
    }

    function isTherePfwRuleInDismisal(
        $student_id = null,
        $program_id = null,
        $academic_year = null,
        $semester = null,
        $year_level = null,
        $semester_level = null,
        $admissionyear = null
    ) {

        $academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
            'conditions' => array(
                'AcademicStand.academic_status_id = 4',
                'AcademicStand.program_id' => $program_id
            ),
            'order' => array('AcademicStand.academic_year_from' => 'ASC'),
            'recursive' => -1
        ));

        $as = null;
        if (!empty($academic_stands)) {
            foreach ($academic_stands as $key => $academic_stand) {
                $stand_year_levels = unserialize($academic_stand['AcademicStand']['year_level_id']);
                $stand_semesters = unserialize($academic_stand['AcademicStand']['semester']);
                //Student acadamic stand searching by year and semster level for status
                if (in_array($year_level, $stand_year_levels) && in_array($semester_level, $stand_semesters)) {
                    //Checking if the acadamic stand is applicable to the student
                    if ((substr(
                                $admissionyear,
                                0,
                                4
                            ) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1 && substr(
                                $academic_year,
                                0,
                                4
                            ) >= $academic_stand['AcademicStand']['academic_year_from'])) {
                        $as = $academic_stand['AcademicStand'];
                    }
                }
            } //End of acadamic stands searching (loop)
        }

        if (!empty($as)) {
            //Searching for the rule by the acadamic stand
            $acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
                'conditions' => array(
                    'AcademicRule.academic_stand_id' => $as['id']
                ),
                'recursive' => -1
            ));

            debug($acadamic_rules);
            //If acadamic rule is found
            if (!empty($acadamic_rules)) {
                foreach ($acadamic_rules as $key => $acadamic_rule) {
                    if ($acadamic_rule['AcademicRule']['pfw'] == 1) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function updateAcdamicStatusByPublishedCourse($published_course_id = null)
    {

        $fully_saved = true;
        $last_exam_status = array();

        //Getting all students who are registered and add for the published course so that we can generate a status for the student if and only if all registered and course grades are submitted and approved by registrar.
        $registered_students = $this->Student->CourseRegistration->PublishedCourse->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $published_course_id
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'Student.graduated = 0' => array(
                        //'fields' => array('id', 'full_name', 'program_id', 'admissionyear', 'program_type_id', 'academicyear', 'graduated'),
                        //'GraduateList'
                    )
                )
            )
        ));

        //debug($registered_students['CourseRegistration']);

        //The following add student list will retrieve all students even if their add is not approved
        //The assumption is that, the student will be filtered later on (whether s/he get grades for all add and registered courses)
        $added_students = $this->Student->CourseAdd->PublishedCourse->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $published_course_id
            ),
            'contain' => array(
                'CourseAdd' => array(
                    'Student.graduated = 0' => array(
                        //'fields' => array('id', 'full_name', 'program_id', 'admissionyear', 'program_type_id', 'academicyear', 'graduated'),
                        //'GraduateList'
                    )
                )
            )
        ));
        //Merging all students (registered and add) To make sure that the student is not included twice to avoid double status generation,
        //duplicate checking is done by student id (to protect from extreme case scenarios: a student is registered and again add a course with some kind of mistake)
        $registered_added_students = array();

        if (isset($registered_students['CourseRegistration']) && !empty($registered_students['CourseRegistration'])) {
            foreach ($registered_students['CourseRegistration'] as $key2 => $value2) {
                $found = false;
                if (!empty($registered_added_students)) {
                    foreach ($registered_added_students as $ras_key => $course_registration) {
                        if (isset($value2['Student']['id']) && !empty($value2['Student']['id']) && $course_registration['Student']['id'] == $value2['Student']['id']) {
                            $found = true;
                            break;
                        }
                    }
                }

                if ($found == false && isset($value2['Student']['graduated']) && $value2['Student']['graduated'] == 0) {
                    $registered_added_students[] = $value2;
                }
            }
        }

        //debug($added_students['CourseAdd']);

        if (isset($added_students['CourseAdd']) && !empty($added_students['CourseAdd'])) {
            foreach ($added_students['CourseAdd'] as $key2 => $value2) {
                $found = false;
                if (!empty($registered_added_students)) {
                    foreach ($registered_added_students as $ras_key => $course_add) {
                        if (isset($value2['Student']['id']) && !empty($value2['Student']['id']) && $course_add['Student']['id'] == $value2['Student']['id']) {
                            $found = true;
                            break;
                        }
                    }
                }

                if ($found == false && isset($value2['Student']['graduated']) && $value2['Student']['graduated'] == 0) {
                    $registered_added_students[] = $value2;
                }
            }
        }

        if (isset($registered_students['PublishedCourse'])) {
            $acadamic_year = $registered_students['PublishedCourse']['academic_year'];
            $semester = $registered_students['PublishedCourse']['semester'];
        }

        $student_exam_status = array();

        if (!empty($registered_added_students)) {
            foreach ($registered_added_students as $ras_key => $course_registration) {
                if ($course_registration['Student']['graduated'] == 0) {
                    $program_type_id = $this->Student->ProgramTypeTransfer->getStudentProgramType(
                        $course_registration['Student']['id'],
                        $acadamic_year,
                        $semester
                    );
                    $program_type_id = $this->Student->ProgramType->getParentProgramType($program_type_id);

                    $pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                        $course_registration['Student']['program_id'],
                        $program_type_id,
                        $acadamic_year
                    );

                    $ay_and_s_list = $this->getAcadamicYearAndSemesterListToGenerateStatus(
                        $course_registration['Student']['id'],
                        $acadamic_year,
                        $semester
                    );

                    //introduce to generate last status for extension students in case of 11 semester where the last semester escaped from status labeling
                    //////////////////////////////////
                    $lastPattern = $this->Student->ProgramType->StudentStatusPattern->isLastSemesterInCurriculum(
                        $course_registration['Student']['id']
                    );

                    $lastRegisteredSem = $this->Student->CourseRegistration->find('first', array(
                        'conditions' => array(
                            'CourseRegistration.student_id' => $course_registration['Student']['id']
                        ),
                        'order' => array(
                            'CourseRegistration.academic_year' => 'DESC',
                            'CourseRegistration.semester' => 'DESC',
                            'CourseRegistration.id' => 'DESC'
                        ),
                        'recursive' => -1
                    ));

                    if ($lastPattern && $lastRegisteredSem['CourseRegistration']['academic_year'] == $acadamic_year && $lastRegisteredSem['CourseRegistration']['semester'] == $semester) {
                        //debug($ay_and_s_list);
                        $pattern = 1;
                    }
                    //////////////////////////////////

                    if (empty($ay_and_s_list)) {
                        //Status is already generated for the given A/Y & semester and you may need to update it.
                        //TODO: This rare case scenario happens when there is multiple publication for the same semester
                    } elseif (count($ay_and_s_list) >= $pattern) {
                        //It is on the perfect way. Generate student status for the last returned a/y and semester.
                        /*
                        1. Make sure that all registered and add courses grade is submitted and approved by registrar.
                        2. For each course get grade point and credit hour and calc the SGPA
                        */
                        $credit_hour_sum = 0;
                        $grade_point_sum = 0;
                        $m_credit_hour_sum = 0;
                        $m_grade_point_sum = 0;
                        $deduct_credit_hour_sum = 0;
                        $deduct_grade_point_sum = 0;
                        $m_deduct_credit_hour_sum = 0;
                        $m_deduct_grade_point_sum = 0;
                        $complete = true;
                        $first_acadamic_year = null;
                        $first_semester = null;
                        $processed_course_reg = array();
                        $processed_course_add = array();

                        $all_ay_s_list = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester(
                            $course_registration['Student']['id'],
                            $ay_and_s_list[0]['academic_year'],
                            $ay_and_s_list[0]['semester']
                        );

                        if (!empty($ay_and_s_list)) {
                            foreach ($ay_and_s_list as $key => $ay_and_s) {
                                $ays_index = count($all_ay_s_list);
                                $all_ay_s_list[$ays_index]['academic_year'] = $ay_and_s['academic_year'];
                                $all_ay_s_list[$ays_index]['semester'] = $ay_and_s['semester'];

                                if ($first_acadamic_year == null) {
                                    $first_acadamic_year = $ay_and_s['academic_year'];
                                    $first_semester = $ay_and_s['semester'];
                                }

                                $course_and_grades = $this->Student->CourseRegistration->ExamGrade->getStudentCoursesAndFinalGrade(
                                    $course_registration['Student']['id'],
                                    $ay_and_s['academic_year'],
                                    $ay_and_s['semester']
                                );

                                if (!empty($course_and_grades)) {
                                    foreach ($course_and_grades as $key => $registered_added_course) {
                                        if (!(isset($registered_added_course['grade']) && (isset($registered_added_course['point_value']) || strcasecmp(
                                                    $registered_added_course['grade'],
                                                    'I'
                                                ) == 0 || strcasecmp($registered_added_course['grade'], 'W')))) {
                                            $complete = false;
                                            break 2;
                                        }


                                        if (isset($registered_added_course['grade']) && (strcasecmp(
                                                    $registered_added_course['grade'],
                                                    'I'
                                                ) == 0 || strcasecmp(
                                                    $registered_added_course['grade'],
                                                    'W'
                                                ) == 0 || strcasecmp($registered_added_course['grade'], 'NG') == 0)) {
                                            $complete = false;
                                            break 2;
                                        }


                                        if (strcasecmp(
                                                $registered_added_course['grade'],
                                                'I'
                                            ) != 0 && isset($registered_added_course['used_in_gpa']) && $registered_added_course['used_in_gpa']) {
                                            $credit_hour_sum += $registered_added_course['credit'];
                                            $grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);

                                            if ($registered_added_course['major'] == 1) {
                                                $m_credit_hour_sum += $registered_added_course['credit'];
                                                $m_grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);
                                            }
                                        }
                                    }
                                }
                            }
                        }


                        if ($complete === true && $credit_hour_sum > 0) {
                            //DEDUCTION: Credit hour and grade point
                            /*
                                1. Get all academic year and semester the student previously attends
                                2. For each academic year semester, get courses and grade details
                                3. Perform the deduction sum
                            */

                            $credit_and_point_deduction = $this->Student->CourseAdd->ExamGrade->getTotalCreditAndPointDeduction(
                                $course_registration['Student']['id'],
                                $all_ay_s_list
                            );
                            $deduct_credit_hour_sum = $credit_and_point_deduction['deduct_credit_hour_sum'];
                            $deduct_grade_point_sum = $credit_and_point_deduction['deduct_grade_point_sum'];
                            $m_deduct_credit_hour_sum = $credit_and_point_deduction['m_deduct_credit_hour_sum'];
                            $m_deduct_grade_point_sum = $credit_and_point_deduction['m_deduct_grade_point_sum'];

                            $stat_index = count($student_exam_status);

                            $student_exam_status[$stat_index]['student_id'] = $course_registration['Student']['id'];
                            $student_exam_status[$stat_index]['academic_year'] = $acadamic_year;
                            $student_exam_status[$stat_index]['semester'] = $semester;
                            $student_exam_status[$stat_index]['grade_point_sum'] = $grade_point_sum;
                            $student_exam_status[$stat_index]['credit_hour_sum'] = $credit_hour_sum;
                            $student_exam_status[$stat_index]['m_grade_point_sum'] = $m_grade_point_sum;
                            $student_exam_status[$stat_index]['m_credit_hour_sum'] = $m_credit_hour_sum;

                            if ($grade_point_sum > 0 && $credit_hour_sum > 0) {
                                $student_exam_status[$stat_index]['sgpa'] = $grade_point_sum / $credit_hour_sum;
                            } else {
                                $student_exam_status[$stat_index]['sgpa'] = 0;
                            }

                            $status_histories = $this->find('all', array(
                                'conditions' => array(
                                    'StudentExamStatus.student_id' => $course_registration['Student']['id'],
                                ),
                                //'order' => array('StudentExamStatus.created' => 'ASC')
                                'order' => array(
                                    'StudentExamStatus.academic_year' => 'ASC',
                                    'StudentExamStatus.semester' => 'ASC',
                                    'StudentExamStatus.created' => 'ASC'
                                )
                            ));

                            $last_exam_status = array();
                            $cumulative_grade_point = $student_exam_status[$stat_index]['grade_point_sum'];
                            $cumulative_credit_hour = $student_exam_status[$stat_index]['credit_hour_sum'];
                            $m_cumulative_grade_point = $student_exam_status[$stat_index]['m_grade_point_sum'];
                            $m_cumulative_credit_hour = $student_exam_status[$stat_index]['m_credit_hour_sum'];

                            if (!empty($status_histories)) {
                                foreach ($status_histories as $key => $status_history) {
                                    if (!(strcasecmp(
                                            $status_history['StudentExamStatus']['academic_year'],
                                            $acadamic_year
                                        ) == 0 && strcasecmp(
                                            $status_history['StudentExamStatus']['semester'],
                                            $semester
                                        ) == 0)) {
                                        $cumulative_grade_point += $status_history['StudentExamStatus']['grade_point_sum'];
                                        $cumulative_credit_hour += $status_history['StudentExamStatus']['credit_hour_sum'];
                                        $m_cumulative_grade_point += $status_history['StudentExamStatus']['m_grade_point_sum'];
                                        $m_cumulative_credit_hour += $status_history['StudentExamStatus']['m_credit_hour_sum'];
                                        $last_exam_status = $status_history['StudentExamStatus'];
                                    } else {
                                        break;
                                    }
                                }
                            }


                            if (($cumulative_grade_point - $deduct_grade_point_sum) > 0 && ($cumulative_credit_hour - $deduct_credit_hour_sum) > 0) {
                                $student_exam_status[$stat_index]['cgpa'] = (($cumulative_grade_point - $deduct_grade_point_sum) / ($cumulative_credit_hour - $deduct_credit_hour_sum));
                            } else {
                                $student_exam_status[$stat_index]['cgpa'] = 0;
                            }

                            if (($m_cumulative_grade_point - $m_deduct_grade_point_sum) > 0 && ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum) > 0) {
                                $student_exam_status[$stat_index]['mcgpa'] = (($m_cumulative_grade_point - $m_deduct_grade_point_sum) / ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum));
                            } else {
                                $student_exam_status[$stat_index]['mcgpa'] = 0;
                            }

                            //Status identification
                            $student_level = $this->studentYearAndSemesterLevelOfStatus(
                                $course_registration['Student']['id'],
                                $acadamic_year,
                                $semester
                            );

                            if ($student_level['year'] == 1) {
                                $student_level['year'] .= 'st';
                            } elseif ($student_level['year'] == 2) {
                                $student_level['year'] .= 'nd';
                            } elseif ($student_level['year'] == 3) {
                                $student_level['year'] .= 'rd';
                            } else {
                                $student_level['year'] .= 'th';
                            }

                            $academic_statuses = ClassRegistry::init('AcademicStatus')->find('all', array(
                                'conditions' => array(
                                    'AcademicStatus.computable = 1'
                                ),
                                'order' => array('AcademicStatus.order' => 'ASC'),
                                'recursive' => -1
                            ));

                            //Checking the student against each academic status
                            if (!empty($academic_statuses)) {
                                foreach ($academic_statuses as $key => $academic_statuse) {
                                    $academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
                                        'conditions' => array(
                                            'AcademicStand.academic_status_id' => $academic_statuse['AcademicStatus']['id'],
                                            'AcademicStand.program_id' => $course_registration['Student']['program_id']
                                        ),
                                        'order' => array('AcademicStand.academic_year_from' => 'ASC'),
                                        'recursive' => -1
                                    ));

                                    $as = null;
                                    if (!empty($academic_stands)) {
                                        foreach ($academic_stands as $key => $academic_stand) {
                                            $stand_year_levels = unserialize(
                                                $academic_stand['AcademicStand']['year_level_id']
                                            );
                                            $stand_semesters = unserialize(
                                                $academic_stand['AcademicStand']['semester']
                                            );
                                            //Student acadamic stand searching by year and semster level for status
                                            if (in_array($student_level['year'], $stand_year_levels) && in_array(
                                                    $student_level['semester'],
                                                    $stand_semesters
                                                )) {
                                                //Checking if the acadamic stand is applicable to the student
                                                if ((substr(
                                                            $course_registration['Student']['academicyear'],
                                                            0,
                                                            4
                                                        ) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1 && substr(
                                                            $acadamic_year,
                                                            0,
                                                            4
                                                        ) >= $academic_stand['AcademicStand']['academic_year_from'])) {
                                                    $as = $academic_stand['AcademicStand'];
                                                }
                                            }

                                            if (!empty($as)) {
                                                //Searching for the rule by the acadamic stand
                                                $acadamic_rules = ClassRegistry::init('AcademicRule')->find(
                                                    'all',
                                                    array(
                                                        'conditions' => array(
                                                            'AcademicRule.academic_stand_id' => $as['id']
                                                        ),
                                                        'recursive' => -1
                                                    )
                                                );

                                                //If acadamic rule is found
                                                if (!empty($acadamic_rules)) {
                                                    $status_found = false;
                                                    foreach ($acadamic_rules as $key => $acadamic_rule) {
                                                        $ar = $acadamic_rule['AcademicRule'];
                                                        $sgpa = round($student_exam_status[$stat_index]['sgpa'], 2);
                                                        $cgpa = round($student_exam_status[$stat_index]['cgpa'], 2);
                                                        //Rule matching
                                                        $sgpa_test = true;
                                                        $cgpa_test = true;

                                                        //Is SGPA in the rule?
                                                        if (!empty($ar['sgpa']) && !(($ar['scmo'] == '>' && $sgpa > $ar['sgpa']) || ($ar['scmo'] == '>=' && $sgpa >= $ar['sgpa']) || ($ar['scmo'] == '<' && $sgpa < $ar['sgpa']) || ($ar['scmo'] == '<=' && $sgpa <= $ar['sgpa']))) {
                                                            $sgpa_test = false;
                                                        }

                                                        //Is CGPA in the rule?
                                                        if (!empty($ar['cgpa']) && !(($ar['ccmo'] == '>' && $cgpa > $ar['cgpa']) || ($ar['ccmo'] == '>=' && $cgpa >= $ar['cgpa']) || ($ar['ccmo'] == '<' && $cgpa < $ar['cgpa']) || ($ar['ccmo'] == '<=' && $cgpa <= $ar['cgpa']))) {
                                                            $cgpa_test = false;
                                                        }

                                                        if ($sgpa_test && $cgpa_test) {
                                                            $status_found = true;
                                                            break;
                                                        }
                                                    }

                                                    //Based on the defined rule, if the student status is determined
                                                    if ($status_found) {
                                                        //If the status is warning and there is status history
                                                        //($course_registration['Student']['program_id']==1 && $course_registration['Student']['program_type_id']==1) &&
                                                        debug($credit_hour_sum);

                                                        if (($credit_hour_sum < ClassRegistry::init(
                                                                'AcademicCalendar'
                                                            )->minimumCreditForStatus(
                                                                $course_registration['Student']['id']
                                                            ))) {
                                                            if (!empty($status_histories) && empty($last_exam_status['academic_status_id'])) {
                                                                $academic_status_id = $as['academic_status_id'];
                                                            } else {
                                                                $academic_status_id = null;
                                                            }
                                                        } elseif ($academic_statuse['AcademicStatus']['id'] == 3 && !empty($last_exam_status)) {
                                                            //If previous status is warning
                                                            if ($last_exam_status['academic_status_id'] == 3) {
                                                                //Check if there is Two Consecutive Warning (TCW) in the dismisal
                                                                if ($this->isThereTcwRuleInDismisal(
                                                                    $student_exam_status[$stat_index]['student_id'],
                                                                    $course_registration['Student']['program_id'],
                                                                    $student_exam_status[$stat_index]['academic_year'],
                                                                    $student_exam_status[$stat_index]['semester'],
                                                                    $student_level['year'],
                                                                    $student_level['semester'],
                                                                    $course_registration['Student']['academicyear']
                                                                )) {
                                                                    $academic_status_id = 4; //Dismisal
                                                                } else {
                                                                    $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                                }
                                                            } elseif ($last_exam_status['academic_status_id'] == 6) {
                                                                //If previous status is probation
                                                                //Check if there is Probation Followed by Warning (PFW) in the dismisal
                                                                if ($this->isTherePfwRuleInDismisal(
                                                                    $student_exam_status[$stat_index]['student_id'],
                                                                    $course_registration['Student']['program_id'],
                                                                    $student_exam_status[$stat_index]['academic_year'],
                                                                    $student_exam_status[$stat_index]['semester'],
                                                                    $student_level['year'],
                                                                    $student_level['semester'],
                                                                    $course_registration['Student']['academicyear']
                                                                )) {
                                                                    $academic_status_id = 4; //Dismisal
                                                                } else {
                                                                    $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                                }
                                                            } else {
                                                                $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                            }
                                                        } else {
                                                            //($course_registration['Student']['program_id']==1 && $course_registration['Student']['program_type_id']==1) && after zewdu comments
                                                            if (($credit_hour_sum < ClassRegistry::init(
                                                                    'AcademicCalendar'
                                                                )->minimumCreditForStatus(
                                                                    $course_registration['Student']['id']
                                                                ))) {
                                                                if (!empty($status_histories) && empty($last_exam_status['academic_status_id'])) {
                                                                    $academic_status_id = $as['academic_status_id'];
                                                                } else {
                                                                    $academic_status_id = null;
                                                                }
                                                            } else {
                                                                $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                            }
                                                        }
                                                        //Later on integrate it with multi rule
                                                        $student_exam_status[$stat_index]['academic_status_id'] = $academic_status_id;
                                                        break 2;
                                                    }
                                                }
                                            }
                                        }
                                        //End of acadamic stands searching (loop)
                                    }
                                }
                                //End of academic status list (loop)
                            }

                            $otherAcademicRule = ClassRegistry::init('OtherAcademicRule')->whatIsTheStatus(
                                $course_and_grades,
                                $course_registration['Student'],
                                $student_level
                            );
                            if (isset($otherAcademicRule) && !empty($otherAcademicRule)) {
                                $student_exam_status[$stat_index]['academic_status_id'] = $otherAcademicRule;
                            } else {
                                debug($otherAcademicRule);
                            }
                        } else {
                            //Grade is not fully submitted and there is nothing to do here
                        }
                    } elseif (count($ay_and_s_list) > $pattern) {
                        //There is program transfer in the middle. and the missed semester is
                        //integrated with the current semester with the above if condition.
                        //There is nothing to do here unless exceptional demand is raised
                    } else {
                        //Pattern is not fulfilled and wait till the next semester to generate status
                        //and there is nothing to do here
                    }
                }
            } //End of each registered student loop
        }

        if (!empty($student_exam_status)) {
            if (!$this->saveAll($student_exam_status, array('validate' => false))) {
                $fully_saved = false;
            }
        }

        return $fully_saved;
    }

    function updateAcdamicStatusForGradeChange($id = null, $type = 'change')
    {

        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());

        if (strcasecmp($type, 'change') == 0) {
            $grade_change_detail = $this->Student->CourseRegistration->ExamGrade->ExamGradeChange->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.id' => $id
                ),
                'contain' => array(
                    'ExamGrade' => array(
                        'CourseRegistration' => array(
                            'PublishedCourse',
                            'Student'
                        ),
                        'CourseAdd' => array(
                            'PublishedCourse',
                            'Student'
                        )
                    )
                )
            ));

            if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") {
                $student = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
                $published_course = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse'];
            } else {
                $student = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
                $published_course = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse'];
            }
        } elseif (strcasecmp($type, 'add') == 0) {
            $grade_change_detail = $this->Student->CourseAdd->find('first', array(
                'conditions' => array(
                    'CourseAdd.id' => $id,
                    //Grade is already submitted and there is no need to check approval
                    //'CourseAdd.department_approval=1',
                    //'CourseAdd.registrar_confirmation=1'
                ),
                'contain' => array(
                    'PublishedCourse',
                    'Student'
                )
            ));

            $student = $grade_change_detail['Student'];
            $published_course = $grade_change_detail['PublishedCourse'];
        } else {
            $grade_change_detail = $this->Student->CourseRegistration->find('first', array(
                'conditions' => array(
                    'CourseRegistration.id' => $id
                ),
                'contain' => array(
                    'PublishedCourse',
                    'Student'
                )
            ));

            $student = $grade_change_detail['Student'];
            $published_course = $grade_change_detail['PublishedCourse'];
        }

        $acadamic_year = $published_course['academic_year'];
        $semester = $published_course['semester'];
        $student_exam_status = array();
        $sucessfully_updated = true;

        $previous_student_exam_status = $this->find('first', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student['id'],
                'StudentExamStatus.academic_year' => $acadamic_year,
                'StudentExamStatus.semester' => $semester
            ),
            'recursive' => -1
        ));

        if (!empty($previous_student_exam_status)) {
            $program_type_id = $this->Student->ProgramTypeTransfer->getStudentProgramType(
                $student['id'],
                $acadamic_year,
                $semester
            );
            $program_type_id = $this->Student->ProgramType->getParentProgramType($program_type_id);
            $pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                $student['program_id'],
                $program_type_id,
                $acadamic_year
            );
            $ay_and_s_list = $this->getAcadamicYearAndSemesterListToUpdateStatus(
                $student['id'],
                $acadamic_year,
                $semester
            );
            $credit_hour_sum = 0;
            $grade_point_sum = 0;
            $m_credit_hour_sum = 0;
            $m_grade_point_sum = 0;
            $deduct_credit_hour_sum = 0;
            $deduct_grade_point_sum = 0;
            $m_deduct_credit_hour_sum = 0;
            $m_deduct_grade_point_sum = 0;
            $complete = true;
            $first_acadamic_year = null;
            $first_semester = null;
            $processed_course_reg = array();
            $processed_course_add = array();

            if (!empty($ay_and_s_list)) {
                foreach ($ay_and_s_list as $key => $ay_and_s) {
                    if ($first_acadamic_year == null) {
                        $first_acadamic_year = $ay_and_s['academic_year'];
                        $first_semester = $ay_and_s['semester'];
                    }

                    $course_and_grades = $this->Student->CourseRegistration->ExamGrade->getStudentCoursesAndFinalGrade(
                        $student['id'],
                        $ay_and_s['academic_year'],
                        $ay_and_s['semester']
                    );

                    if (!empty($course_and_grades)) {
                        foreach ($course_and_grades as $key => $registered_added_course) {
                            if (!(isset($registered_added_course['grade']) && (isset($registered_added_course['point_value']) || strcasecmp(
                                        $registered_added_course['grade'],
                                        'I'
                                    ) == 0))) {
                                $complete = false;
                                break 2;
                            }

                            if (isset($registered_added_course['grade']) && (strcasecmp(
                                        $registered_added_course['grade'],
                                        'I'
                                    ) == 0 || strcasecmp($registered_added_course['grade'], 'W') == 0 || strcasecmp(
                                        $registered_added_course['grade'],
                                        'NG'
                                    ) == 0)) {
                                $complete = false;
                                break 2;
                            }

                            if (strcasecmp(
                                    $registered_added_course['grade'],
                                    'I'
                                ) != 0 && isset($registered_added_course['used_in_gpa']) && $registered_added_course['used_in_gpa']) {
                                $credit_hour_sum += $registered_added_course['credit'];
                                $grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);

                                if ($registered_added_course['major'] == 1) {
                                    $m_credit_hour_sum += $registered_added_course['credit'];
                                    $m_grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);
                                }

                                //debug($this->Student->CourseRegistration->find('all', array('conditions' => array('CourseRegistration.student_id' => $student['id']), 'contain' => array('PublishedCourse' => array('Course')))));
                                //Begin: credit hour and grade point deduction sum

                                if ($registered_added_course['repeated_new'] == true) {
                                    /*** Get list of registration and add for the current course or substituted course
                                     * excluding current acadamic year and semester ***/

                                    /** The returned AY and semester list is till the current round of AY and Semester. It is used to consider repeted courses within the same pattern AY and Semester **/
                                    //$previous_ay_and_s2 = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester($student['id'], $first_acadamic_year, $first_semester);
                                    $previous_ay_and_s2 = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester(
                                        $student['id'],
                                        $ay_and_s['academic_year'],
                                        $ay_and_s['semester']
                                    );
                                    $course_registrations = $this->Student->CourseRegistration->getCourseRegistrations(
                                        $student['id'],
                                        $previous_ay_and_s2,
                                        $registered_added_course['course_id'],
                                        1,
                                        1
                                    );
                                    $course_adds = $this->Student->CourseAdd->getCourseAdds(
                                        $student['id'],
                                        $previous_ay_and_s2,
                                        $registered_added_course['course_id'],
                                        1
                                    );
                                    //Add repeated courses credit hour and grade point

                                    if (!empty($course_registrations)) {
                                        foreach ($course_registrations as $cr_key => $cr_value) {
                                            //To avoid double sum
                                            if (!in_array(
                                                $cr_value['CourseRegistration']['id'],
                                                $processed_course_reg
                                            )) {
                                                $grade_detail = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade(
                                                    $cr_value['CourseRegistration']['id'],
                                                    1
                                                );
                                                $deduct_credit_hour_sum += $cr_value['PublishedCourse']['Course']['credit'];
                                                $deduct_grade_point_sum += ($grade_detail['point_value'] * $cr_value['PublishedCourse']['Course']['credit']);

                                                if ($cr_value['PublishedCourse']['Course']['major'] == 1) {
                                                    $m_deduct_credit_hour_sum += $cr_value['PublishedCourse']['Course']['credit'];
                                                    $m_deduct_grade_point_sum += ($grade_detail['point_value'] * $cr_value['PublishedCourse']['Course']['credit']);
                                                }

                                                $processed_course_reg[] = $cr_value['CourseRegistration']['id'];
                                            }
                                        }
                                    }

                                    if (!empty($course_adds)) {
                                        foreach ($course_adds as $cr_key => $ca_value) {
                                            if (!in_array($ca_value['CourseAdd']['id'], $processed_course_reg)) {
                                                $grade_detail = $this->Student->CourseAdd->ExamGrade->getApprovedGrade(
                                                    $ca_value['CourseAdd']['id'],
                                                    0
                                                );
                                                $deduct_credit_hour_sum += $ca_value['PublishedCourse']['Course']['credit'];
                                                $deduct_grade_point_sum += ($grade_detail['point_value'] * $ca_value['PublishedCourse']['Course']['credit']);

                                                if ($ca_value['PublishedCourse']['Course']['major'] == 1) {
                                                    $m_deduct_credit_hour_sum += $ca_value['PublishedCourse']['Course']['credit'];
                                                    $m_deduct_grade_point_sum += ($grade_detail['point_value'] * $ca_value['PublishedCourse']['Course']['credit']);
                                                }

                                                $processed_course_add[] = $ca_value['CourseAdd']['id'];
                                            }
                                        }
                                    }
                                    /*if(!empty($course_registrations) && (empty($course_adds) || ($course_registrations[0]['CourseRegistration']['created'] >= $course_adds[0]['CourseAdd']['created']))) {
                                        $grade_detail = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade($course_registrations[0]['CourseRegistration']['id'], 1);
                                        $deduct_credit_hour_sum += $course_registrations[0]['PublishedCourse']['Course']['credit'];
                                        $deduct_grade_point_sum += ($grade_detail['point_value']*$course_registrations[0]['PublishedCourse']['Course']['credit']);
                                        if($course_registrations[0]['PublishedCourse']['Course']['major'] == 1) {
                                            $m_deduct_credit_hour_sum += $course_registrations[0]['PublishedCourse']['Course']['credit'];
                                            $m_deduct_grade_point_sum += ($grade_detail['point_value']*$course_registrations[0]['PublishedCourse']['Course']['credit']);
                                        }
                                    }
                                    else if(!empty($course_adds) && (empty($course_registrations) || ($course_registrations[0]['CourseRegistration']['created'] <= $course_adds[0]['CourseAdd']['created']))) {
                                        $grade_detail = $this->Student->CourseAdd->ExamGrade->getApprovedGrade($course_adds[0]['CourseAdd']['id'], 0);
                                        $deduct_credit_hour_sum += $course_adds[0]['PublishedCourse']['Course']['credit'];
                                        $deduct_grade_point_sum += ($grade_detail['point_value']*$course_adds[0]['PublishedCourse']['Course']['credit']);
                                        if($course_registrations[0]['PublishedCourse']['Course']['major'] == 1) {
                                            $m_deduct_credit_hour_sum += $course_registrations[0]['PublishedCourse']['Course']['credit'];
                                            $m_deduct_grade_point_sum += ($grade_detail['point_value']*$course_registrations[0]['PublishedCourse']['Course']['credit']);
                                        }
                                    }*/
                                } //End of credit hour and grade point deduction sum
                            }
                        }
                    }
                    //debug($course_and_grades);
                }
            }

            if (count($ay_and_s_list) >= $pattern) {
                if ($complete === true && $credit_hour_sum > 0) {
                    $student_exam_status['id'] = $previous_student_exam_status['StudentExamStatus']['id'];
                    $student_exam_status['student_id'] = $student['id'];
                    $student_exam_status['academic_year'] = $acadamic_year;
                    $student_exam_status['semester'] = $semester;
                    $student_exam_status['grade_point_sum'] = $grade_point_sum;
                    $student_exam_status['credit_hour_sum'] = $credit_hour_sum;
                    $student_exam_status['m_grade_point_sum'] = $m_grade_point_sum;
                    $student_exam_status['m_credit_hour_sum'] = $m_credit_hour_sum;
                    $student_exam_status['created'] = $AcademicYear->getAcademicYearBegainingDate(
                        $acadamic_year,
                        $semester
                    );

                    if ($credit_hour_sum > 0) {
                        $student_exam_status['sgpa'] = $grade_point_sum / $credit_hour_sum;
                    } else {
                        $student_exam_status['sgpa'] = 0;
                    }

                    $status_histories = $this->find('all', array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $student['id'],
                        ),
                        'order' => array('StudentExamStatus.created' => 'ASC')
                    ));

                    $cumulative_grade_point = $student_exam_status['grade_point_sum'];
                    $cumulative_credit_hour = $student_exam_status['credit_hour_sum'];
                    $m_cumulative_grade_point = $student_exam_status['m_grade_point_sum'];
                    $m_cumulative_credit_hour = $student_exam_status['m_credit_hour_sum'];

                    if (!empty($status_histories)) {
                        foreach ($status_histories as $key => $status_history) {
                            if (!(strcasecmp(
                                    $status_history['StudentExamStatus']['academic_year'],
                                    $acadamic_year
                                ) == 0 && strcasecmp(
                                    $status_history['StudentExamStatus']['semester'],
                                    $semester
                                ) == 0)) {
                                $cumulative_grade_point += $status_history['StudentExamStatus']['grade_point_sum'];
                                $cumulative_credit_hour += $status_history['StudentExamStatus']['credit_hour_sum'];
                                $m_cumulative_grade_point += $status_history['StudentExamStatus']['m_grade_point_sum'];
                                $m_cumulative_credit_hour += $status_history['StudentExamStatus']['m_credit_hour_sum'];
                                $last_exam_status = $status_history['StudentExamStatus'];
                            } else {
                                break;
                            }
                        }
                    }

                    if (($cumulative_grade_point - $deduct_grade_point_sum) > 0 && ($cumulative_credit_hour - $deduct_credit_hour_sum) > 0) {
                        $student_exam_status['cgpa'] = (($cumulative_grade_point - $deduct_grade_point_sum) / ($cumulative_credit_hour - $deduct_credit_hour_sum));
                    } else {
                        $student_exam_status['cgpa'] = 0;
                    }

                    if (($m_cumulative_grade_point - $m_deduct_grade_point_sum) > 0 && ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum) > 0) {
                        $student_exam_status['mcgpa'] = (($m_cumulative_grade_point - $m_deduct_grade_point_sum) / ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum));
                    } else {
                        $student_exam_status['mcgpa'] = 0;
                    }

                    //Status identification
                    $student_level = $this->studentYearAndSemesterLevelOfStatus(
                        $student['id'],
                        $acadamic_year,
                        $semester
                    );

                    if ($student_level['year'] == 1) {
                        $student_level['year'] .= 'st';
                    } elseif ($student_level['year'] == 2) {
                        $student_level['year'] .= 'nd';
                    } elseif ($student_level['year'] == 3) {
                        $student_level['year'] .= 'rd';
                    } else {
                        $student_level['year'] .= 'th';
                    }

                    $academic_statuses = ClassRegistry::init('AcademicStatus')->find('all', array(
                        'conditions' => array('AcademicStatus.computable = 1'),
                        'order' => array('AcademicStatus.order' => 'ASC'),
                        'recursive' => -1
                    ));
                    //Checking the student against each academic status

                    if (empty($academic_statuses)) {
                        foreach ($academic_statuses as $key => $academic_statuse) {
                            $academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
                                'conditions' => array(
                                    'AcademicStand.academic_status_id' => $academic_statuse['AcademicStatus']['id'],
                                    'AcademicStand.program_id' => $student['program_id']
                                ),
                                'order' => array('AcademicStand.academic_year_from' => 'ASC'),
                                'recursive' => -1
                            ));

                            $as = null;

                            if (!empty($academic_stands)) {
                                foreach ($academic_stands as $key => $academic_stand) {
                                    $stand_year_levels = unserialize($academic_stand['AcademicStand']['year_level_id']);
                                    $stand_semesters = unserialize($academic_stand['AcademicStand']['semester']);
                                    //Student acadamic stand searching by year and semster level for status
                                    if (in_array($student_level['year'], $stand_year_levels) && in_array(
                                            $student_level['semester'],
                                            $stand_semesters
                                        )) {
                                        //Checking if the acadamic stand is applicable to the student
                                        if ((substr(
                                                    $student['academicyear'],
                                                    0,
                                                    4
                                                ) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1 && substr(
                                                    $acadamic_year,
                                                    0,
                                                    4
                                                ) >= $academic_stand['AcademicStand']['academic_year_from'])) {
                                            $as = $academic_stand['AcademicStand'];
                                        }
                                    }

                                    if (!empty($as)) {
                                        //Searching for the rule by the acadamic stand
                                        $acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
                                            'conditions' => array(
                                                'AcademicRule.academic_stand_id' => $as['id']
                                            ),
                                            'recursive' => -1
                                        ));

                                        //If acadamic rule is found
                                        if (!empty($acadamic_rules)) {
                                            $status_found = false;
                                            foreach ($acadamic_rules as $key => $acadamic_rule) {
                                                $ar = $acadamic_rule['AcademicRule'];
                                                $sgpa = round($student_exam_status['sgpa'], 2);
                                                $cgpa = round($student_exam_status['cgpa'], 2);
                                                //Rule matching
                                                $sgpa_test = true;
                                                $cgpa_test = true;
                                                //Is SGPA in the rule?
                                                if (!empty($ar['sgpa']) && !(($ar['scmo'] == '>' && $sgpa > $ar['sgpa']) || ($ar['scmo'] == '>=' && $sgpa >= $ar['sgpa']) || ($ar['scmo'] == '<' && $sgpa < $ar['sgpa']) || ($ar['scmo'] == '<=' && $sgpa <= $ar['sgpa']))) {
                                                    $sgpa_test = false;
                                                }
                                                //Is CGPA in the rule?
                                                if (!empty($ar['cgpa']) && !(($ar['ccmo'] == '>' && $cgpa > $ar['cgpa']) || ($ar['ccmo'] == '>=' && $cgpa >= $ar['cgpa']) || ($ar['ccmo'] == '<' && $cgpa < $ar['cgpa']) || ($ar['ccmo'] == '<=' && $cgpa <= $ar['cgpa']))) {
                                                    $cgpa_test = false;
                                                }

                                                if ($sgpa_test && $cgpa_test) {
                                                    $status_found = true;
                                                    break;
                                                }
                                            }

                                            //Based on the defined rule, if the student status is determined
                                            if ($status_found) {
                                                //If the status is warning and there is status history
                                                if (($credit_hour_sum < ClassRegistry::init(
                                                        'AcademicCalendar'
                                                    )->minimumCreditForStatus($student['id']))) {
                                                    if (isset($status_histories) && empty($last_exam_status['academic_status_id'])) {
                                                        $academic_status_id = $as['academic_status_id'];
                                                    } else {
                                                        $academic_status_id = null;
                                                    }
                                                } elseif ($academic_statuse['AcademicStatus']['id'] == 3 && !empty($last_exam_status)) {
                                                    //If previous status is warning
                                                    if ($last_exam_status['academic_status_id'] == 3) {
                                                        //Check if there is Two Consecutive Warning (TCW) in the dismisal
                                                        if ($this->isThereTcwRuleInDismisal(
                                                            $student_exam_status['student_id'],
                                                            $student['program_id'],
                                                            $student_exam_status['academic_year'],
                                                            $student_exam_status['semester'],
                                                            $student_level['year'],
                                                            $student_level['semester'],
                                                            $student['academicyear']
                                                        )) {
                                                            $academic_status_id = 4; //Dismisal
                                                        } else {
                                                            $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                        }
                                                    } elseif ($last_exam_status['academic_status_id'] == 6) {
                                                        //If previous status is probation
                                                        //Check if there is Probation Followed by Warning (PFW) in the dismisal
                                                        if ($this->isTherePfwRuleInDismisal(
                                                            $student_exam_status['student_id'],
                                                            $student['program_id'],
                                                            $student_exam_status['academic_year'],
                                                            $student_exam_status['semester'],
                                                            $student_level['year'],
                                                            $student_level['semester'],
                                                            $student['academicyear']
                                                        )) {
                                                            $academic_status_id = 4; //Dismisal
                                                        } else {
                                                            $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                        }
                                                    } else {
                                                        $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                    }
                                                } else {
                                                    $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                }
                                                //Later on integrate it with multi rule
                                                $student_exam_status['academic_status_id'] = $academic_status_id;
                                                break 2;
                                            }
                                        }
                                    }
                                } //End of acadamic stands searching (loop)
                            }
                        } //End of acadamic status list (loop)
                    }
                } else {
                    //Grade is not fully submitted and there is nothing to do here
                }
            }
        } else {
            //generate status if there is no status
            //return $this->updateAcdamicStatusByPublishedCourse($published_course['id']);
        }

        if (!empty($student_exam_status)) {
            if (!$this->save($student_exam_status, array('validate' => false))) {
                $sucessfully_updated = false;
            }
        }

        return $sucessfully_updated;
    }


    /* This function returns student acdamic status at the end but before the given acadamic year and semester
        Return values
        A. 1 = for the first time (pattern not fullfilled)
        B. 2 = status is not generated before the given acadamic year and semester (on hold)
        C. Array = Student status object
    */

    function getStudentAcadamicStatus($student_id = null, $acadamic_year = null, $semester = null)
    {

        //Check if there is any generated status
        // debug($acadamic_year);
        // debug($semester);

        $last_student_status = $this->find('first', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id
            ),
            'recursive' => -1,
            'order' => array('StudentExamStatus.created' => 'DESC')
        ));

        debug($last_student_status);

        $student_detail = $this->Student->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id
            ),
            'recursive' => -1
        ));

        $p_ay_and_s_list = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester(
            $student_id,
            $acadamic_year,
            $semester
        );
        debug($p_ay_and_s_list);

        if (empty($p_ay_and_s_list)) {
            return 1;
        }

        $previous_ay_and_s['academic_year'] = $p_ay_and_s_list[count($p_ay_and_s_list) - 1]['academic_year'];
        $previous_ay_and_s['semester'] = $p_ay_and_s_list[count($p_ay_and_s_list) - 1]['semester'];

        $previous_ay_and_s = $this->getPreviousSemester($acadamic_year, $semester);
        debug($previous_ay_and_s);
        debug($acadamic_year);
        debug($semester);
        debug($previous_ay_and_s['academic_year']);
        debug($previous_ay_and_s['semester']);

        $program_type_id = $this->Student->ProgramTypeTransfer->getStudentProgramType(
            $student_id,
            $previous_ay_and_s['academic_year'],
            $previous_ay_and_s['semester']
        );
        $program_type_id = $this->Student->ProgramType->getParentProgramType($program_type_id);

        $pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
            $student_detail['Student']['program_id'],
            $program_type_id,
            $previous_ay_and_s['academic_year']
        );
        debug($previous_ay_and_s);

        $ay_and_s_list = $this->getAcadamicYearAndSemesterListToGenerateStatus(
            $student_detail['Student']['id'],
            $previous_ay_and_s['academic_year'],
            $previous_ay_and_s['semester']
        );
        debug($previous_ay_and_s);

        debug($ay_and_s_list);

        if (empty($last_student_status)) {
            /* If there is no generated status, then check if the pattern is fullfilled. If it is fullfilled then the student can register "on hold" state otherwise it is used to mean that the student still required to attend more semester to know about his/her status (in other words, the student has > 1 pattern value). */
            if (count($ay_and_s_list) >= $pattern) {
                return 2;
            } else {
                return 1;
            }
        } else {
            /* If there is already recorded status, then the next possibility will be either the student has last status or it is onhold state */
            debug($ay_and_s_list);
            debug($pattern);
            if (count($ay_and_s_list) >= $pattern) {
                return 2;
            } elseif (!empty($last_student_status['StudentExamStatus']) && is_null(
                    $last_student_status['StudentExamStatus']['academic_status_id']
                )) {
                return 0;
            } else {
                return $last_student_status['StudentExamStatus'];
            }
        }
    }

    function studentYearAndSemesterLevelOfStatusDisplay($student_id, $acadamic_year, $semester)
    {

        return $this->studentYearAndSemesterLevelOfStatus($student_id, $acadamic_year, $semester);
    }

    function studentYearAndSemesterLevel($student_id, $acadamic_year = null, $semester = null)
    {

        $student_statuses = $this->find('all', array(
            'conditions' => array(
                'StudentExamStatus.student_id' => $student_id,
                //'StudentExamStatus.academic_status_id is not null',
                'StudentExamStatus.academic_status_id != 4'
            ),
            'recursive' => -1,
            'order' => array('StudentExamStatus.created' => 'ASC')
        ));

        $semester_count = 0;

        if (!empty($student_statuses)) {
            foreach ($student_statuses as $key => $student_status) {
                if (strcasecmp(
                        $student_status['StudentExamStatus']['academic_year'],
                        $acadamic_year
                    ) == 0 && strcasecmp($student_status['StudentExamStatus']['semester'], $semester) == 0) {
                    break;
                } else {
                    $semester_count++;
                }
            }
        }

        $year_level = ((int)($semester_count / 2)) + 1;

        if ($semester_count % 2 > 0) {
            $semster_level = 'II';
        } else {
            $semster_level = 'I';
        }

        $name = '';

        switch ($year_level) {
            case 1:
                $name = $year_level . 'st';
                break;
            case 2:
                $name = $year_level . 'nd';
                break;
            case 3:
                $name = $year_level . 'rd';
                break;
            default:
                $name = $year_level . 'th';
        }


        /* if (!empty($acadamic_year)) {
            $student_statuses = $this->Student->CourseRegistration->find('all', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                    'CourseRegistration.academic_year <=' => $acadamic_year
                ),
                'recursive' => -1,
                'order' => array('CourseRegistration.academic_year ASC,CourseRegistration.semester ASC '),
                'group' => array('CourseRegistration.academic_year')
            ));

        } else {
            $student_statuses = $this->Student->CourseRegistration->find('all', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id,
                ),
                'recursive' => -1,
                'order' => array('CourseRegistration.academic_year ASC,CourseRegistration.semester ASC '),
                'group' => array('CourseRegistration.academic_year')
            ));
        }

        $year_count = 0;

        if (!empty($student_statuses)) {
            foreach ($student_statuses as $key => $student_status) {
                $semster_level = $student_status['CourseRegistration']['semester'];
                $year_count++;
            }
        }

        $name = '';

        switch ($year_count) {
            case 1:
                $name = $year_level . 'st';
                break;
            case 2:
                $name = $year_level . 'nd';
                break;
            case 3:
                $name = $year_level . 'rd';
                break;
            default:
                $name = $year_level . 'th';
        } */

        $status_level['year'] = $name;
        $status_level['semester'] = $semster_level;
        debug($status_level);
        return $status_level;
    }


    function isServiceDeserved($student_id = null, $academic_year = null)
    {
        /*
        // 1. scenario : student serive ? -passed but not dismissed, withdraw, and  cleared but not if cleared

        $find_last_status=$this->find('first',array('conditions'=>array('StudentExamStatus.student_id'=>$student_id),'order'=>'StudentExamStatus.created DESC'));
        $find_last_registration=;

         if (Is student has on hold state (regisred or add after last status but status not
         generated ) {
            if ( student current dismisal because of any declnary or some reason)
                   // deny
            else if  student withdraw after registration due to some reason
                    //deny
            else if drop out after registration
                    //  deny

             else
                    // elegible for serivce


         } else if student passed last registred semester  {
             // display warning/informative message for those students cleared but not registred/add
             if (withdraw after last status and not readmitted) {
                                deny service

             }else if (dismissed after last status becuase of diceplend and not
             readmitted) {
                   deny
             } else if student drop out after last regisration {
                // deny

             } else {
                // elegible
             }

         } else {
                 if (student gets probation after last dismisal) {
                    // elegible for service

                 } else if (is_readmitted) {
                        //elegible
                 } else {
                    //deny
                 }
        } */
    }


    function updateAcdamicStatusByStudent($studentID, $published_course_id)
    {

        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());
        $fully_saved = true;
        $last_exam_status = array();

        //Getting all students who are registered and add for the published course so that we can generate a status for the student if and only if all registered and course grades are submitted and approved by registrar.
        $registered_students = $this->Student->CourseRegistration->PublishedCourse->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $published_course_id
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $studentID,
                    ),
                    //'order' => array('CourseRegistration.created' => 'ASC'),
                    'order' => array('CourseRegistration.id' => 'ASC'),
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'full_name',
                            'Student.program_id',
                            'Student.admissionyear',
                            'Student.program_type_id'
                        ),
                        'GraduateList'
                    )
                )
            )
        ));

        //The following add student list will retrieve all students even if their add is not approved The assumption is that, the student will be filtered later on (whether s/he get grades for all add and registered courses)
        $added_students = $this->Student->CourseAdd->PublishedCourse->find('first', array(
            'conditions' => array('PublishedCourse.id' => $published_course_id),
            'contain' => array(
                'CourseAdd' => array(
                    'conditions' => array(
                        'CourseAdd.student_id' => $studentID,
                    ),
                    //'order' => array('CourseAdd.created' => 'ASC'),
                    'order' => array('CourseAdd.id' => 'ASC'),
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'full_name',
                            'Student.program_id',
                            'Student.admissionyear',
                            'Student.program_type_id'
                        ),
                        'GraduateList'
                    )
                )
            )
        ));

        //Merging all students (registered and add) To make sure that the student is not included twice to avoid double status generation,
        //duplicate checking is done by student id (to protect from extreme case scenarios:
        //a student is registered and again add a course with some kind of mistake)

        $registered_added_students = array();

        if (!empty($registered_students['CourseRegistration'])) {
            foreach ($registered_students['CourseRegistration'] as $key2 => $value2) {
                $found = false;
                if (!empty($registered_added_students)) {
                    foreach ($registered_added_students as $ras_key => $course_registration) {
                        if ($course_registration['Student']['id'] == $value2['Student']['id']) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found == false) {
                    $registered_added_students[] = $value2;
                }
            }
        }

        if (!empty($added_students['CourseAdd'])) {
            foreach ($added_students['CourseAdd'] as $key2 => $value2) {
                $found = false;
                if (!empty($registered_added_students)) {
                    foreach ($registered_added_students as $ras_key => $course_add) {
                        if ($course_add['Student']['id'] == $value2['Student']['id']) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found == false) {
                    $registered_added_students[] = $value2;
                }
            }
        }


        $acadamic_year = $registered_students['PublishedCourse']['academic_year'];
        $semester = $registered_students['PublishedCourse']['semester'];

        $student_exam_status = array();

        if (!empty($registered_added_students)) {
            foreach ($registered_added_students as $ras_key => $course_registration) {
                $program_type_id = $this->Student->ProgramTypeTransfer->getStudentProgramType(
                    $course_registration['Student']['id'],
                    $acadamic_year,
                    $semester
                );
                $program_type_id = $this->Student->ProgramType->getParentProgramType($program_type_id);

                $pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                    $course_registration['Student']['program_id'],
                    $program_type_id,
                    $acadamic_year
                );

                //introduce to generate last status for extension students in case of 11 semester where the last semester escaped from status labeling
                //////////////////////////////////

                $lastPattern = $this->Student->ProgramType->StudentStatusPattern->isLastSemesterInCurriculum(
                    $course_registration['Student']['id']
                );

                $lastRegisteredSem = $this->Student->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $course_registration['Student']['id']
                    ),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'recursive' => -1
                ));

                if ($lastPattern && $lastRegisteredSem['CourseRegistration']['academic_year'] == $acadamic_year && $lastRegisteredSem['CourseRegistration']['semester'] == $semester) {
                    //debug($ay_and_s_list);
                    $pattern = 1;
                } /* else if ($course_registration['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
                    $pattern = 2;
                } */

                /* // check if the academic year and semester has withdrawal case ?
                $withdrawAfter = $this->Student->Clearance->withDrawaAfterRegistration($course_registration['Student']['id'], $acadamic_year, $semester);

                if ($withdrawAfter) {
                    continue;
                } else {
                    $ay_and_s_list = $this->getAcadamicYearAndSemesterListToGenerateStatus($course_registration['Student']['id'], $acadamic_year, $semester);
                    debug($ay_and_s_list);

                    if (!empty($ay_and_s_list)) {
                        foreach ($ay_and_s_list as $key => &$ay_and_s) {
                            $withdrawAfter = $this->Student->Clearance->withDrawaAfterRegistration($course_registration['Student']['id'], $ay_and_s['academic_year'], $ay_and_s['semester']);
                            //debug($withdrawAfter);
                            if ($withdrawAfter) {
                                //debug($withdrawAfter);
                                unset($ay_and_s_list[$key]);
                            }
                        }
                    }

                    $ay_and_s_list = array_values($ay_and_s_list);
                } */

                $ay_and_s_list = $this->getAcadamicYearAndSemesterListToGenerateStatus(
                    $course_registration['Student']['id'],
                    $acadamic_year,
                    $semester
                );

                if (empty($ay_and_s_list)) {
                    //Status is already generated for the given A/Y & semester and you may need to update it.
                    //TODO: This rare case scenario happens when there is multiple publication for the same semester
                } elseif (count($ay_and_s_list) >= $pattern) {
                    //It is on the perfect way. Generate student status for the last returned a/y and semester.
                    /*
                        1. Make sure that all registered and add courses grade is submitted and approved by registrar.
                        2. For each course get grade point and credit hour and calc the SGPA
                    */

                    $credit_hour_sum = 0;
                    $grade_point_sum = 0;
                    $m_credit_hour_sum = 0;
                    $m_grade_point_sum = 0;
                    $deduct_credit_hour_sum = 0;
                    $deduct_grade_point_sum = 0;
                    $m_deduct_credit_hour_sum = 0;
                    $m_deduct_grade_point_sum = 0;
                    $complete = true;
                    $first_acadamic_year = null;

                    $first_semester = null;
                    $processed_course_reg = array();
                    $processed_course_add = array();

                    $all_ay_s_list = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester(
                        $course_registration['Student']['id'],
                        $ay_and_s_list[0]['academic_year'],
                        $ay_and_s_list[0]['semester']
                    );
                    debug($all_ay_s_list);

                    if (!empty($ay_and_s_list)) {
                        foreach ($ay_and_s_list as $key => $ay_and_s) {
                            $ays_index = count($all_ay_s_list);
                            $all_ay_s_list[$ays_index]['academic_year'] = $ay_and_s['academic_year'];
                            $all_ay_s_list[$ays_index]['semester'] = $ay_and_s['semester'];

                            if ($first_acadamic_year == null) {
                                $first_acadamic_year = $ay_and_s['academic_year'];
                                $first_semester = $ay_and_s['semester'];
                            }

                            $course_and_grades = $this->Student->CourseRegistration->ExamGrade->getStudentCoursesAndFinalGrade(
                                $course_registration['Student']['id'],
                                $ay_and_s['academic_year'],
                                $ay_and_s['semester']
                            );

                            if (!empty($course_and_grades)) {
                                foreach ($course_and_grades as $key => $registered_added_course) {
                                    if (!(isset($registered_added_course['grade']) && (isset($registered_added_course['point_value']) || strcasecmp(
                                                $registered_added_course['grade'],
                                                'I'
                                            ) == 0 || strcasecmp($registered_added_course['grade'], 'W') == 0))) {
                                        $complete = false;
                                        break 2;
                                    }

                                    if (isset($registered_added_course['grade']) && (strcasecmp(
                                                $registered_added_course['grade'],
                                                'I'
                                            ) == 0 || strcasecmp(
                                                $registered_added_course['grade'],
                                                'W'
                                            ) == 0 || strcasecmp($registered_added_course['grade'], 'NG') == 0)) {
                                        $complete = false;
                                        break 2;
                                    }


                                    if (strcasecmp(
                                            $registered_added_course['grade'],
                                            'I'
                                        ) != 0 && isset($registered_added_course['used_in_gpa']) && $registered_added_course['used_in_gpa']) {
                                        $credit_hour_sum += $registered_added_course['credit'];
                                        $grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);

                                        if ($registered_added_course['major'] == 1) {
                                            $m_credit_hour_sum += $registered_added_course['credit'];
                                            $m_grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //debug($complete);

                    //check credit hour is greater than 25, and have previous status not generated

                    /* echo $acadamic_year . " && " . $semester . '==' . $credit_hour_sum . '<br/>';

                    if ($credit_hour_sum <= ClassRegistry::init('AcademicCalendar')->minimumCreditForStatus($course_registration['Student']['id'])) {
                        $complete = false;
                    } */


                    //TODO:read setting for minimum credit for status generation

                    /* if ($course_registration['Student']['program_id'] == 1 && $course_registration['Student']['program_type_id'] == 1) {
                        if ($credit_hour_sum < ClassRegistry::init('AcademicCalendar')->minimumCreditForStatus($course_registration['Student']['id'])) {
                            $complete = false;
                        }
                    } */


                    if ($complete === true && $credit_hour_sum > 0) {
                        //debug($all_ay_s_list);

                        //DEDUCTION: Credit hour and grade point
                        /*
                        1. Get all academic year and semester the student previously attends
                        2. For each academic year semester, get courses and grade details
                        3. Perform the deduction sum
                        */

                        $credit_and_point_deduction = $this->Student->CourseAdd->ExamGrade->getTotalCreditAndPointDeduction(
                            $course_registration['Student']['id'],
                            $all_ay_s_list
                        );

                        $deduct_credit_hour_sum = $credit_and_point_deduction['deduct_credit_hour_sum'];
                        $deduct_grade_point_sum = $credit_and_point_deduction['deduct_grade_point_sum'];
                        $m_deduct_credit_hour_sum = $credit_and_point_deduction['m_deduct_credit_hour_sum'];
                        $m_deduct_grade_point_sum = $credit_and_point_deduction['m_deduct_grade_point_sum'];

                        $stat_index = count($student_exam_status);
                        debug($stat_index);

                        $student_exam_status[$stat_index]['student_id'] = $course_registration['Student']['id'];
                        $student_exam_status[$stat_index]['academic_year'] = $acadamic_year;
                        $student_exam_status[$stat_index]['semester'] = $semester;
                        $student_exam_status[$stat_index]['grade_point_sum'] = $grade_point_sum;
                        $student_exam_status[$stat_index]['credit_hour_sum'] = $credit_hour_sum;
                        $student_exam_status[$stat_index]['m_grade_point_sum'] = $m_grade_point_sum;
                        $student_exam_status[$stat_index]['m_credit_hour_sum'] = $m_credit_hour_sum;

                        $student_exam_status[$stat_index]['created'] = $AcademicYear->getAcademicYearBegainingDate(
                            $acadamic_year,
                            $semester
                        );

                        if ($credit_hour_sum > 0) {
                            $student_exam_status[$stat_index]['sgpa'] = $grade_point_sum / $credit_hour_sum;
                        } else {
                            $student_exam_status[$stat_index]['sgpa'] = 0;
                        }

                        $status_histories = $this->find('all', array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $course_registration['Student']['id'],
                            ),
                            //'order' => array('StudentExamStatus.created ASC')
                            'order' => array(
                                'StudentExamStatus.academic_year' => 'ASC',
                                'StudentExamStatus.semester' => 'ASC',
                                'StudentExamStatus.created' => 'ASC'
                            )
                        ));

                        $last_exam_status = array();
                        $cumulative_grade_point = $student_exam_status[$stat_index]['grade_point_sum'];
                        $cumulative_credit_hour = $student_exam_status[$stat_index]['credit_hour_sum'];
                        $m_cumulative_grade_point = $student_exam_status[$stat_index]['m_grade_point_sum'];
                        $m_cumulative_credit_hour = $student_exam_status[$stat_index]['m_credit_hour_sum'];

                        if (!empty($status_histories)) {
                            foreach ($status_histories as $key => $status_history) {
                                if (!(strcasecmp(
                                        $status_history['StudentExamStatus']['academic_year'],
                                        $acadamic_year
                                    ) == 0 && strcasecmp(
                                        $status_history['StudentExamStatus']['semester'],
                                        $semester
                                    ) == 0)) {
                                    $cumulative_grade_point += $status_history['StudentExamStatus']['grade_point_sum'];
                                    $cumulative_credit_hour += $status_history['StudentExamStatus']['credit_hour_sum'];
                                    $m_cumulative_grade_point += $status_history['StudentExamStatus']['m_grade_point_sum'];
                                    $m_cumulative_credit_hour += $status_history['StudentExamStatus']['m_credit_hour_sum'];
                                    $last_exam_status = $status_history['StudentExamStatus'];
                                } else {
                                    break;
                                }
                            }
                        }

                        if (($cumulative_grade_point - $deduct_grade_point_sum) > 0 && ($cumulative_credit_hour - $deduct_credit_hour_sum) > 0) {
                            $student_exam_status[$stat_index]['cgpa'] = (($cumulative_grade_point - $deduct_grade_point_sum) / ($cumulative_credit_hour - $deduct_credit_hour_sum));
                        } else {
                            $student_exam_status[$stat_index]['cgpa'] = 0;
                        }

                        if (($m_cumulative_grade_point - $m_deduct_grade_point_sum) > 0 && ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum) > 0) {
                            $student_exam_status[$stat_index]['mcgpa'] = (($m_cumulative_grade_point - $m_deduct_grade_point_sum) / ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum));
                        } else {
                            $student_exam_status[$stat_index]['mcgpa'] = 0;
                        }

                        //Status identification

                        $student_level = $this->studentYearAndSemesterLevelOfStatus(
                            $course_registration['Student']['id'],
                            $acadamic_year,
                            $semester
                        );

                        /*
                        if($student_level['year'] == 1) {
                            $student_level['year'] .= 'st';
                        } else if($student_level['year'] == 2) {
                            $student_level['year'] .= 'nd';
                        } else if($student_level['year'] == 3) {
                            $student_level['year'] .= 'rd';
                        } else {
                            $student_level['year'] .= 'th';
                        }
                        */

                        $academic_statuses = ClassRegistry::init('AcademicStatus')->find('all', array(
                            'conditions' => array('AcademicStatus.computable = 1'),
                            'order' => array('AcademicStatus.order' => 'ASC'),
                            'recursive' => -1
                        ));

                        //Checking the student against each academic status

                        if (!empty($academic_statuses)) {
                            foreach ($academic_statuses as $key => $academic_statuse) {
                                $academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
                                    'conditions' => array(
                                        'AcademicStand.academic_status_id' => $academic_statuse['AcademicStatus']['id'],
                                        'AcademicStand.program_id' => $course_registration['Student']['program_id']
                                    ),
                                    'order' => array('AcademicStand.academic_year_from' => 'ASC'),
                                    'recursive' => -1
                                ));

                                $as = null;

                                if (!empty($academic_stands)) {
                                    foreach ($academic_stands as $key => $academic_stand) {
                                        $stand_year_levels = unserialize(
                                            $academic_stand['AcademicStand']['year_level_id']
                                        );
                                        $stand_semesters = unserialize($academic_stand['AcademicStand']['semester']);
                                        //Student acadamic stand searching by year and semster level for status
                                        debug($student_level);

                                        if (in_array($student_level['year'], $stand_year_levels) && in_array(
                                                $student_level['semester'],
                                                $stand_semesters
                                            )) {
                                            //Checking if the acadamic stand is applicable to the student
                                            if ((substr(
                                                        $course_registration['Student']['academicyear'],
                                                        0,
                                                        4
                                                    ) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1 && substr(
                                                        $acadamic_year,
                                                        0,
                                                        4
                                                    ) >= $academic_stand['AcademicStand']['academic_year_from'])) {
                                                $as = $academic_stand['AcademicStand'];
                                            }
                                        }

                                        /* if ($acadamic_year >= "2014/15") {
                                            debug($as);
                                        } */

                                        if (!empty($as)) {
                                            //Searching for the rule by the acadamic stand
                                            $acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
                                                'conditions' => array(
                                                    'AcademicRule.academic_stand_id' => $as['id']
                                                ),
                                                'recursive' => -1
                                            ));

                                            //If acadamic rule is found
                                            debug($acadamic_rules);

                                            if (!empty($acadamic_rules)) {
                                                $status_found = false;

                                                if (!empty($acadamic_rules)) {
                                                    foreach ($acadamic_rules as $key => $acadamic_rule) {
                                                        $ar = $acadamic_rule['AcademicRule'];
                                                        $sgpa = round($student_exam_status[$stat_index]['sgpa'], 2);
                                                        $cgpa = round($student_exam_status[$stat_index]['cgpa'], 2);

                                                        //Rule matching
                                                        $sgpa_test = true;
                                                        $cgpa_test = true;
                                                        //Is SGPA in the rule?

                                                        if (!empty($ar['sgpa']) && !(($ar['scmo'] == '>' && $sgpa > $ar['sgpa']) || ($ar['scmo'] == '>=' && $sgpa >= $ar['sgpa']) || ($ar['scmo'] == '<' && $sgpa < $ar['sgpa']) || ($ar['scmo'] == '<=' && $sgpa <= $ar['sgpa']))) {
                                                            $sgpa_test = false;
                                                        }

                                                        //Is CGPA in the rule?
                                                        if (!empty($ar['cgpa']) && !(($ar['ccmo'] == '>' && $cgpa > $ar['cgpa']) || ($ar['ccmo'] == '>=' && $cgpa >= $ar['cgpa']) || ($ar['ccmo'] == '<' && $cgpa < $ar['cgpa']) || ($ar['ccmo'] == '<=' && $cgpa <= $ar['cgpa']))) {
                                                            $cgpa_test = false;
                                                        }

                                                        if ($sgpa_test && $cgpa_test) {
                                                            $status_found = true;
                                                            break;
                                                        }
                                                    }
                                                }

                                                //Based on the defined rule, if the student status is determined
                                                if ($status_found) {
                                                    //($course_registration['Student']['program_id']==1 && $course_registration['Student']['program_type_id']==1) &&
                                                    if (($credit_hour_sum < ClassRegistry::init(
                                                            'AcademicCalendar'
                                                        )->minimumCreditForStatus(
                                                            $course_registration['Student']['id']
                                                        ))) {
                                                        if (!empty($status_histories) && empty($last_exam_status['academic_status_id'])) {
                                                            $academic_status_id = $as['academic_status_id'];
                                                        } else {
                                                            $academic_status_id = null;
                                                        }
                                                        //If the status is warning and there is status history
                                                    } elseif ($academic_statuse['AcademicStatus']['id'] == 3 && !empty($last_exam_status)) {
                                                        //If previous status is warning
                                                        if ($last_exam_status['academic_status_id'] == 3) {
                                                            //Check if there is Two Consecutive Warning (TCW) in the dismisal
                                                            if ($this->isThereTcwRuleInDismisal(
                                                                $student_exam_status[$stat_index]['student_id'],
                                                                $course_registration['Student']['program_id'],
                                                                $student_exam_status[$stat_index]['academic_year'],
                                                                $student_exam_status[$stat_index]['semester'],
                                                                $student_level['year'],
                                                                $student_level['semester'],
                                                                $course_registration['Student']['academicyear']
                                                            )) {
                                                                $academic_status_id = 4; //Dismisal
                                                                debug($last_exam_status['academic_status_id']);
                                                            } else {
                                                                $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                            }
                                                        } elseif ($last_exam_status['academic_status_id'] == 6) {
                                                            //If previous status is probation
                                                            //Check if there is Probation Followed by Warning (PFW) in the dismisal
                                                            if ($this->isTherePfwRuleInDismisal(
                                                                $student_exam_status[$stat_index]['student_id'],
                                                                $course_registration['Student']['program_id'],
                                                                $student_exam_status[$stat_index]['academic_year'],
                                                                $student_exam_status[$stat_index]['semester'],
                                                                $student_level['year'],
                                                                $student_level['semester'],
                                                                $course_registration['Student']['academicyear']
                                                            )) {
                                                                $academic_status_id = 4; //Dismisal
                                                            } else {
                                                                $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                            }
                                                        } else {
                                                            $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                        }
                                                    } else {
                                                        $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                    }
                                                    //Later on integrate it with multi rule
                                                    $student_exam_status[$stat_index]['academic_status_id'] = $academic_status_id;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                    //End of acadamic stands searching (loop)
                                }
                            }
                            //End of academic status list (loop)
                        }

                        $otherAcademicRule = ClassRegistry::init('OtherAcademicRule')->whatIsTheStatus(
                            $course_and_grades,
                            $course_registration['Student'],
                            $student_level
                        );

                        if (isset($otherAcademicRule) && !empty($otherAcademicRule)) {
                            $student_exam_status[$stat_index]['academic_status_id'] = $otherAcademicRule;
                        } else {
                            debug($otherAcademicRule);
                        }
                    } else {
                        //Grade is not fully submitted and there is nothing to do here
                    }
                } elseif (count($ay_and_s_list) > $pattern) {
                    //There is program transfer in the middle. and the missed semester is integrated with the current semester with the above if condition.
                    //There is nothing to do here unless exceptional demand is raised
                } else {
                    //Pattern is not fulfilled and wait till the next semester to generate status and there is nothing to do here
                }
            } //End of each registered student loop
        }

        debug($student_exam_status);

        if (!empty($student_exam_status)) {
            if (!$this->saveAll($student_exam_status, array('validate' => false))) {
                $fully_saved = false;
            }
        }

        return $fully_saved;
    }

    function getGradeChangeList(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $year_level_id = null,
        $freshman = 0
    ) {

        debug($freshman);

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $query = '';

        $queryAdd = '';
        $queryMakeup = '';

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and ps.program_id="' . $program_ids[1] . '"';

                $queryAdd .= ' and ps.program_id="' . $program_ids[1] . '"';
                $queryMakeup .= ' and ps.program_id="' . $program_ids[1] . '"';
            } else {
                $query .= ' and ps.program_id="' . $program_id . '"';

                $queryAdd .= ' and ps.program_id="' . $program_id . '"';
                $queryMakeup .= ' and ps.program_id="' . $program_id . '"';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';

                $queryAdd .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';
                $queryMakeup .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';
            } else {
                $query .= ' and ps.program_type_id="' . $program_type_id . '"';

                $queryAdd .= ' and ps.program_type_id="' . $program_type_id . '"';
                $queryMakeup .= ' and ps.program_type_id="' . $program_type_id . '"';
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $query .= ' and ps.academic_year="' . $acadamic_year . '"';
            $query .= ' and cr.academic_year="' . $acadamic_year . '"';

            $queryAdd .= ' and ps.academic_year="' . $acadamic_year . '"';
            $queryAdd .= ' and ca.academic_year="' . $acadamic_year . '"';

            $queryMakeup .= ' and ps.academic_year="' . $acadamic_year . '"';
            //$queryMakeup  .= ' and me.academic_year="' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and ps.semester="' . $semester . '"';
            $query .= ' and cr.semester="' . $semester . '"';

            $queryAdd .= ' and ps.semester="' . $semester . '"';
            $queryAdd .= ' and ca.semester="' . $semester . '"';

            $queryMakeup .= ' and ps.semester="' . $semester . '"';
            //$queryMakeup .= ' and me.semester="' . $semester . '"';
        }

        // list out the department
        if ($freshman == 0) {
            if (isset($department_id) && !empty($department_id)) {
                $college_id = explode('~', $department_id);
                if (count($college_id) > 1) {
                    $departments = $this->Student->Department->find('all', array(
                        'conditions' => array(
                            'Department.college_id' => $college_id[1],
                            'Department.active' => 1
                        ),
                        'contain' => array(
                            'College',
                            'YearLevel'
                        )
                    ));
                } else {
                    $departments = $this->Student->Department->find('all', array(
                        'conditions' => array(
                            'Department.id' => $department_id,
                            'Department.active' => 1
                        ),
                        'contain' => array(
                            'College',
                            'YearLevel'
                        )
                    ));
                }
            } else {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.active' => 1
                    ),
                    'contain' => array(
                        'College',
                        'YearLevel'
                    )
                ));
            }
        } else {
            if (isset($department_id) && !empty($department_id)) {
                $college_id = explode('~', $department_id);
                if (count($college_id) > 1) {
                    $colleges = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.id' => $college_id[1],
                            'College.active' => 1
                        ),
                    ));
                } else {
                    $departments = array();
                }
            } else {
                $colleges = $this->Student->College->find('list', array(
                    'conditions' => array(
                        'College.active' => 1
                    ),
                ));
            }
        }

        $gradeChangeList = array();
        if ($freshman == 0) {
            if (!empty($departments)) {
                foreach ($departments as $key => $value) {
                    $internalQuery = '';
                    foreach ($value['YearLevel'] as $ykey => $yvalue) {
                        if (!empty($year_level_id)) {
                            if ($yvalue['name'] == $year_level_id) {
                                $internalQuery .= ' and ps.year_level_id = "' . $yvalue['id'] . '"';
                                $internalQuery .= ' and ps.given_by_department_id = "' . $value['Department']['id'] . '"';

                                $queryRegistration = "SELECT cr.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
								FROM course_registrations AS cr, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
								WHERE eg.course_registration_id = cr.id AND eg.registrar_approval = 1
								AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
								AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = cr.published_course_id AND cia.published_course_id = ps.id $query $internalQuery";

                                // TO DO: Including Adds and Makeup Grade changes to make the report provide full information, Neway
                                $queryAdds = "SELECT ca.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
								FROM course_adds AS ca, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
								WHERE eg.course_add_id = ca.id AND eg.registrar_approval = 1 AND ca.department_approval = 1 AND ca.registrar_confirmation = 1 AND eg.course_add_id is not null
								AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
								AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = ca.published_course_id AND cia.published_course_id = ps.id $queryAdd $internalQuery";

                                $queryMakeups = "SELECT me.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
								FROM makeup_exams AS me, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
								WHERE eg.makeup_exam_id = me.id AND eg.registrar_approval = 1
								AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
								AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = me.published_course_id AND cia.published_course_id = ps.id $queryMakeup $internalQuery";
                            }
                        } else {
                            $internalQuery .= ' and ps.year_level_id="' . $yvalue['id'] . '"';
                            $internalQuery .= ' and ps.given_by_department_id="' . $value['Department']['id'] . '"';

                            $queryRegistration = "SELECT cr.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion,egc.auto_ng_conversion,egc.makeup_exam_result,egc.result, egc.initiated_by_department
							FROM course_registrations AS cr, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
							WHERE eg.course_registration_id = cr.id AND eg.registrar_approval = 1
							AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
							AND eg.registrar_approval =1 AND egc.exam_grade_id = eg.id AND ps.id = cr.published_course_id AND cia.published_course_id = ps.id $query $internalQuery";

                            // TO DO: Including Adds and Makeup Grade changes
                            $queryAdds = "SELECT ca.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
							FROM course_adds AS ca, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
							WHERE eg.course_add_id = ca.id AND eg.registrar_approval = 1 AND ca.department_approval = 1 AND ca.registrar_confirmation = 1  AND eg.course_add_id is not null
							AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
							AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = ca.published_course_id AND cia.published_course_id = ps.id $queryAdd $internalQuery";

                            $queryMakeups = "SELECT me.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
							FROM makeup_exams AS me, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
							WHERE eg.makeup_exam_id = me.id AND eg.registrar_approval = 1
							AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
							AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = me.published_course_id AND cia.published_course_id = ps.id $queryMakeup $internalQuery";
                        }

                        if (!empty($queryRegistration)) {
                            $distChangeList = $this->query($queryRegistration);

                            if (!empty($distChangeList)) {
                                foreach ($distChangeList as $dk => $dl) {
                                    //debug($dl);
                                    $details = $this->Student->CourseRegistration->find('first', array(
                                        'conditions' => array(
                                            'CourseRegistration.id' => $dl['cr']['id']
                                        ),
                                        'contain' => array(
                                            'PublishedCourse' => array(
                                                'CourseInstructorAssignment' => array(
                                                    'Staff' => array(
                                                        'Title',
                                                        'Position',
                                                    ),
                                                ),
                                                'GivenByDepartment',
                                                'Course'
                                            ),
                                            'Student'
                                        )
                                    ));

                                    $grade_detail["oldGrade"] = $dl['eg']['grade'];
                                    $grade_detail['course'] = $details['PublishedCourse']['Course']['course_title'] . ' (' . $details['PublishedCourse']['Course']['course_code'] . ')';

                                    $grade_detail = array_merge($grade_detail, $dl['egc']);

                                    $grade_detail['department_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['department_approved_by'])
                                    );
                                    $grade_detail['college_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['college_approved_by'])
                                    );
                                    $grade_detail['registrar_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['registrar_approved_by'])
                                    );

                                    $staff_full = $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];

                                    $grade_detail['full_name'] = $details['Student']['full_name'];
                                    $grade_detail['student_id'] = $details['Student']['id'];
                                    $grade_detail['studentnumber'] = $details['Student']['studentnumber'];
                                    $grade_detail['gender'] = $details['Student']['gender'];
                                    $grade_detail['graduated'] = $details['Student']['graduated'];

                                    $gradeChangeList[$staff_full][] = $grade_detail;
                                }
                            }
                        }

                        if (!empty($queryAdds)) {
                            $distChangeListAdd = $this->query($queryAdds);

                            if (!empty($distChangeListAdd)) {
                                foreach ($distChangeListAdd as $dk => $dl) {
                                    //debug($dl);
                                    $details = $this->Student->CourseAdd->find('first', array(
                                        'conditions' => array(
                                            'CourseAdd.id' => $dl['ca']['id']
                                        ),
                                        'contain' => array(
                                            'PublishedCourse' => array(
                                                'CourseInstructorAssignment' => array(
                                                    'Staff' => array(
                                                        'Title',
                                                        'Position',
                                                    ),
                                                ),
                                                'GivenByDepartment',
                                                'Course'
                                            ),
                                            'Student'
                                        )
                                    ));

                                    $grade_detail["oldGrade"] = $dl['eg']['grade'];
                                    $grade_detail['course'] = $details['PublishedCourse']['Course']['course_title'] . ' (' . $details['PublishedCourse']['Course']['course_code'] . ')';

                                    $grade_detail = array_merge($grade_detail, $dl['egc']);

                                    $grade_detail['department_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['department_approved_by'])
                                    );
                                    $grade_detail['college_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['college_approved_by'])
                                    );
                                    $grade_detail['registrar_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['registrar_approved_by'])
                                    );

                                    $staff_full = $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];

                                    $grade_detail['full_name'] = $details['Student']['full_name'];
                                    $grade_detail['student_id'] = $details['Student']['id'];
                                    $grade_detail['studentnumber'] = $details['Student']['studentnumber'];
                                    $grade_detail['gender'] = $details['Student']['gender'];
                                    $grade_detail['graduated'] = $details['Student']['graduated'];

                                    $gradeChangeList[$staff_full][] = $grade_detail;
                                }
                            }
                        }

                        if (!empty($queryMakeups)) {
                            $distChangeListMakeUp = $this->query($queryMakeups);

                            if (!empty($distChangeListMakeUp)) {
                                foreach ($distChangeListMakeUp as $dk => $dl) {
                                    //debug($dl);
                                    $details = $this->Student->MakeupExam->find('first', array(
                                        'conditions' => array(
                                            'MakeupExam.id' => $dl['me']['id']
                                        ),
                                        'contain' => array(
                                            'PublishedCourse' => array(
                                                'CourseInstructorAssignment' => array(
                                                    'Staff' => array(
                                                        'Title',
                                                        'Position',
                                                    ),
                                                ),
                                                'GivenByDepartment',
                                                'Course'
                                            ),
                                            'Student'
                                        )
                                    ));

                                    $grade_detail["oldGrade"] = $dl['eg']['grade'];
                                    $grade_detail['course'] = $details['PublishedCourse']['Course']['course_title'] . ' (' . $details['PublishedCourse']['Course']['course_code'] . ')';

                                    $grade_detail = array_merge($grade_detail, $dl['egc']);

                                    $grade_detail['department_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['department_approved_by'])
                                    );
                                    $grade_detail['college_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['college_approved_by'])
                                    );
                                    $grade_detail['registrar_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                                        'Staff.full_name',
                                        array('Staff.user_id' => $dl['egc']['registrar_approved_by'])
                                    );

                                    $staff_full = $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];

                                    $grade_detail['full_name'] = $details['Student']['full_name'];
                                    $grade_detail['student_id'] = $details['Student']['id'];
                                    $grade_detail['studentnumber'] = $details['Student']['studentnumber'];
                                    $grade_detail['gender'] = $details['Student']['gender'];
                                    $grade_detail['graduated'] = $details['Student']['graduated'];

                                    $gradeChangeList[$staff_full][] = $grade_detail;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $college_id = explode('~', $department_id);

            if (empty($college_id[1])) {
                $college_id = $this->Student->College->find('list', array(
                    'conditions' => array(
                        'College.active' => 1
                    ),
                    'fields' => array(
                        'College.id',
                        'College.id'
                    )
                ));
                $internalQuery = ' and ps.college_id in (' . implode(
                        ',',
                        $college_id
                    ) . ') and ps.department_id is null';
                //debug($internalQuery);
            } else {
                $internalQuery = ' and ps.college_id="' . $college_id[1] . '" and ps.department_id is null';
            }

            $queryRegistration = "SELECT cr.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
			FROM course_registrations AS cr, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
			WHERE eg.course_registration_id = cr.id AND eg.registrar_approval = 1
			AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
			AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = cr.published_course_id AND cia.published_course_id = ps.id $query $internalQuery";

            // TO DO: Including Adds and Makeup Grade changes
            $queryAdds = "SELECT ca.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
			FROM course_adds AS ca, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
			WHERE eg.course_add_id = ca.id AND eg.registrar_approval = 1 AND ca.department_approval = 1 AND ca.registrar_confirmation = 1  AND eg.course_add_id is not null
			AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
			AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = ca.published_course_id AND cia.published_course_id = ps.id $queryAdd $internalQuery";

            $queryMakeups = "SELECT me.id, ps.department_id, eg.grade, egc.grade, egc.reason, cia.staff_id, egc.department_approved_by, egc.college_approved_by, egc.registrar_approved_by, egc.college_reason, egc.registrar_reason, egc.department_reason, egc.department_approval_date, egc.registrar_approval_date, egc.college_approval_date, egc.manual_ng_conversion, egc.auto_ng_conversion, egc.makeup_exam_result, egc.result, egc.initiated_by_department
			FROM makeup_exams AS me, published_courses AS ps, exam_grades AS eg, exam_grade_changes AS egc, course_instructor_assignments AS cia
			WHERE eg.makeup_exam_id = me.id AND eg.registrar_approval = 1
			AND eg.id IN (SELECT exam_grade_id FROM exam_grade_changes WHERE registrar_approval = 1)
			AND eg.registrar_approval = 1 AND egc.exam_grade_id = eg.id AND ps.id = me.published_course_id AND cia.published_course_id = ps.id $queryMakeup $internalQuery";

            debug($queryRegistration);

            if (!empty($queryRegistration)) {
                $distChangeList = $this->query($queryRegistration);

                if (!empty($distChangeList)) {
                    foreach ($distChangeList as $dk => $dl) {
                        $details = $this->Student->CourseRegistration->find('first', array(
                            'conditions' => array(
                                'CourseRegistration.id' => $dl['cr']['id']
                            ),
                            'contain' => array(
                                'PublishedCourse' => array(
                                    'CourseInstructorAssignment' => array(
                                        'Staff' => array(
                                            'Title',
                                            'Position',
                                        ),
                                    ),
                                    'GivenByDepartment',
                                    'Course'
                                ),
                                'Student'
                            )
                        ));

                        $grade_detail["oldGrade"] = $dl['eg']['grade'];
                        $grade_detail['course'] = $details['PublishedCourse']['Course']['course_title'] . ' (' . $details['PublishedCourse']['Course']['course_code'] . ')';

                        $grade_detail = array_merge($grade_detail, $dl['egc']);

                        $staff_full = $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . ' ' . $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];

                        $grade_detail['department_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['department_approved_by'])
                        );
                        $grade_detail['college_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['college_approved_by'])
                        );
                        $grade_detail['registrar_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['registrar_approved_by'])
                        );

                        $grade_detail['full_name'] = $details['Student']['full_name'];
                        $grade_detail['student_id'] = $details['Student']['id'];
                        $grade_detail['studentnumber'] = $details['Student']['studentnumber'];
                        $grade_detail['gender'] = $details['Student']['gender'];
                        $grade_detail['graduated'] = $details['Student']['graduated'];

                        $gradeChangeList[$staff_full][] = $grade_detail;
                    }
                }
            }

            if (!empty($queryAdds)) {
                $distChangeListAdd = $this->query($queryAdds);

                if (!empty($distChangeListAdd)) {
                    foreach ($distChangeListAdd as $dk => $dl) {
                        //debug($dl);
                        $details = $this->Student->CourseAdd->find('first', array(
                            'conditions' => array(
                                'CourseAdd.id' => $dl['ca']['id']
                            ),
                            'contain' => array(
                                'PublishedCourse' => array(
                                    'CourseInstructorAssignment' => array(
                                        'Staff' => array(
                                            'Title',
                                            'Position',
                                        ),
                                    ),
                                    'GivenByDepartment',
                                    'Course'
                                ),
                                'Student'
                            )
                        ));

                        $grade_detail["oldGrade"] = $dl['eg']['grade'];
                        $grade_detail['course'] = $details['PublishedCourse']['Course']['course_title'] . ' (' . $details['PublishedCourse']['Course']['course_code'] . ')';

                        $grade_detail = array_merge($grade_detail, $dl['egc']);

                        $grade_detail['department_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['department_approved_by'])
                        );
                        $grade_detail['college_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['college_approved_by'])
                        );
                        $grade_detail['registrar_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['registrar_approved_by'])
                        );

                        $staff_full = $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];

                        $grade_detail['full_name'] = $details['Student']['full_name'];
                        $grade_detail['student_id'] = $details['Student']['id'];
                        $grade_detail['studentnumber'] = $details['Student']['studentnumber'];
                        $grade_detail['gender'] = $details['Student']['gender'];
                        $grade_detail['graduated'] = $details['Student']['graduated'];

                        $gradeChangeList[$staff_full][] = $grade_detail;
                    }
                }
            }

            if (!empty($queryMakeups)) {
                $distChangeListMakeUp = $this->query($queryMakeups);

                if (!empty($distChangeListMakeUp)) {
                    foreach ($distChangeListMakeUp as $dk => $dl) {
                        //debug($dl);
                        $details = $this->Student->MakeupExam->find('first', array(
                            'conditions' => array(
                                'MakeupExam.id' => $dl['me']['id']
                            ),
                            'contain' => array(
                                'PublishedCourse' => array(
                                    'CourseInstructorAssignment' => array(
                                        'Staff' => array(
                                            'Title',
                                            'Position',
                                        ),
                                    ),
                                    'GivenByDepartment',
                                    'Course'
                                ),
                                'Student'
                            )
                        ));

                        $grade_detail["oldGrade"] = $dl['eg']['grade'];
                        $grade_detail['course'] = $details['PublishedCourse']['Course']['course_title'] . ' (' . $details['PublishedCourse']['Course']['course_code'] . ')';

                        $grade_detail = array_merge($grade_detail, $dl['egc']);

                        $grade_detail['department_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['department_approved_by'])
                        );
                        $grade_detail['college_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['college_approved_by'])
                        );
                        $grade_detail['registrar_approved_by'] = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->Staff->field(
                            'Staff.full_name',
                            array('Staff.user_id' => $dl['egc']['registrar_approved_by'])
                        );

                        $staff_full = $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $details['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'];

                        $grade_detail['full_name'] = $details['Student']['full_name'];
                        $grade_detail['student_id'] = $details['Student']['id'];
                        $grade_detail['studentnumber'] = $details['Student']['studentnumber'];
                        $grade_detail['gender'] = $details['Student']['gender'];
                        $grade_detail['graduated'] = $details['Student']['graduated'];

                        $gradeChangeList[$staff_full][] = $grade_detail;
                    }
                }
            }
        }
        return $gradeChangeList;
    }

    public function getNotGradeSubmittedList(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $year_level_id = null,
        $freshman = 0
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }
        $query = '';
        $lateGradeSubmissionList = array();
        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and ps.program_id="' . $program_ids[1] . '"';
            } else {
                $query .= ' and ps.program_id="' . $program_id . '"';
            }
        }

        if (
            isset($program_type_id)
            && !empty($program_type_id)
        ) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';
            } else {
                $query .= ' and ps.program_type_id="' . $program_type_id . '"';
            }
        }

        if (
            isset($acadamic_year) &&
            !empty($acadamic_year)
        ) {
            $query .= ' and ps.academic_year="' . $acadamic_year . '"';
            $query .= ' and cr.academic_year="' . $acadamic_year . '"';
        }
        if (isset($semester) && !empty($semester)) {
            $query .= ' and ps.semester="' . $semester . '"';
            $query .= ' and cr.semester="' . $semester . '"';
        }

        // list out the department
        if ($freshman == 0) {
            if (isset($department_id) && !empty($department_id)) {
                $college_id = explode('~', $department_id);
                if (count($college_id) > 1) {
                    $departments = $this->Student->Department->find(
                        'all',
                        array(
                            'conditions' => array('Department.college_id' => $college_id[1]),
                            'contain' => array('College', 'YearLevel')
                        )
                    );
                    $departments[] = $this->Student->College->find(
                        'first',
                        array(
                            'conditions' => array('College.id' => $college_id[1]),
                            'recursive' => -1
                        )
                    );
                } else {
                    $departments = $this->Student->Department->find(
                        'all',
                        array(
                            'conditions' => array('Department.id' => $department_id),
                            'contain' => array('College', 'YearLevel')
                        )
                    );
                }
            } else {
                $department1 = $this->Student->Department->find('all', array('contain' => array('College', 'YearLevel'))
                );


                $department2 = $this->Student->College->find(
                    'all',
                    array(
                        'recursive' => -1
                    )
                );
                $departments = array_merge($department1, $department2);
            }
        }
        $gradeChangeList = array();
        if ($freshman == 0) {
            foreach ($departments as $key => $value) {
                $internalQuery = '';
                $yearLevel = array();
                if (!isset($value['YearLevel'])) {
                    $internalQuery .= ' and (ps.year_level_id is null or ps.year_level_id=0) and ps.department_id is null';
                    $internalQuery .= ' and ps.college_id="' . $value['College']['id'] . '"';
                    $queryRegistration = "SELECT cr.published_course_id
FROM course_registrations AS cr, published_courses AS ps WHERE cr.published_course_id = ps.id
AND ps.id
IN (SELECT published_course_id
FROM course_instructor_assignments
WHERE semester='$semester'
AND academic_year='$acadamic_year'
AND isprimary =1
)
AND cr.id NOT
IN (
SELECT course_registration_id
FROM exam_grades
WHERE course_registration_id IS NOT NULL
) $query $internalQuery  GROUP BY cr.published_course_id";

                    $internalQuery = '';
                    if (!empty($queryRegistration)) {
                        $distChangeList = $this->query($queryRegistration);
                        foreach ($distChangeList as $dk => $dl) {
                            $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                                $dl['cr']['published_course_id']
                            ); //double check since query report if one student even submit as delay
                            if (empty($gradeSubmitted)) {
                                $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                    'first',
                                    array(
                                        'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                                        'contain' => array(
                                            'Section' => array(
                                                'YearLevel',
                                                'Program',
                                                'ProgramType',
                                                'Department' => array('College')
                                            ),
                                            'PublishedCourse' => array(
                                                'GivenByDepartment' => array('College'),
                                                'Course',
                                                'YearLevel'
                                            ),
                                            'Staff' => array('Title', 'Position')
                                        )
                                    )
                                );
                                //debug($courseInstructorAssignment);

                                $gradeSubmissionDeadline = ClassRegistry::init(
                                    'AcademicCalendar'
                                )->recentAcademicYearSchedule(
                                    $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                    $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                    $courseInstructorAssignment['PublishedCourse']['program_id'],
                                    $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                    $courseInstructorAssignment['PublishedCourse']['department_id'],
                                    $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                                );
                                $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                                $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                            }
                        }
                    }
                } else {
                    if (!empty($year_level_id)) {
                        foreach ($value['YearLevel'] as $yykey => $yyvalue) {
                            if (
                                !empty($year_level_id)
                                && strcasecmp($year_level_id, $yyvalue['name']) == 0
                            ) {
                                $yearLevel[$yykey] = $yyvalue;
                            }
                        }
                    } elseif (empty($year_level_id)) {
                        $yearLevel = $value['YearLevel'];
                    }
                }

                foreach ($yearLevel as $ykey => $yvalue) {
                    if (!empty($year_level_id)) {
                        if ($yvalue['name'] == $year_level_id) {
                            $internalQuery .= ' and ps.year_level_id="' . $yvalue['id'] . '"';
                            $internalQuery .= ' and ps.department_id="' . $value['Department']['id'] . '"';
                            $queryRegistration = "SELECT distinct cr.published_course_id
FROM course_registrations AS cr, published_courses AS ps
WHERE cr.published_course_id = ps.id
AND ps.id
IN (

SELECT published_course_id
FROM course_instructor_assignments
WHERE semester='$semester'
AND academic_year='$acadamic_year'
AND isprimary =1
)
AND cr.id NOT
IN (

SELECT course_registration_id
FROM exam_grades
WHERE course_registration_id IS NOT NULL
) $query $internalQuery  GROUP BY ps.id";
                        }
                    } else {
                        $internalQuery .= ' and ps.year_level_id="' . $yvalue['id'] . '"';
                        $internalQuery .= ' and ps.department_id="' . $value['Department']['id'] . '"';

                        $queryRegistration = "SELECT cr.published_course_id
FROM course_registrations AS cr, published_courses AS ps
WHERE cr.published_course_id = ps.id
AND ps.id
IN (

SELECT published_course_id
FROM course_instructor_assignments
WHERE semester='$semester'
AND academic_year='$acadamic_year'
AND isprimary =1
)
AND cr.id NOT
IN (

SELECT course_registration_id
FROM exam_grades
WHERE course_registration_id IS NOT NULL
) $query $internalQuery  GROUP BY cr.published_course_id";
                    }
                    $internalQuery = '';
                    if (!empty($queryRegistration)) {
                        $distChangeList = $this->query($queryRegistration);
                        foreach ($distChangeList as $dk => $dl) {
                            $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                                $dl['cr']['published_course_id']
                            ); //double check since query report if one student even submit as delay
                            if (empty($gradeSubmitted)) {
                                $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                    'first',
                                    array(
                                        'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                                        'contain' => array(
                                            'Section' => array(
                                                'YearLevel',
                                                'Program',
                                                'ProgramType',
                                                'Department' => array('College')
                                            ),
                                            'PublishedCourse' => array(
                                                'GivenByDepartment' => array('College'),
                                                'Course',
                                                'YearLevel'
                                            ),
                                            'Staff' => array('Title', 'Position')
                                        )
                                    )
                                );
                                //debug($courseInstructorAssignment);

                                $gradeSubmissionDeadline = ClassRegistry::init(
                                    'AcademicCalendar'
                                )->recentAcademicYearSchedule(
                                    $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                    $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                    $courseInstructorAssignment['PublishedCourse']['program_id'],
                                    $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                    $courseInstructorAssignment['PublishedCourse']['department_id'],
                                    $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                                );
                                $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                                $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                            }
                        }
                    }
                }
            }
        } else {
            $internalQuery = '';

            $college_id = explode('~', $department_id);

            $internalQuery = ' and ps.college_id="' . $college_id[1] . '" and ps.department_id is null';

            debug($internalQuery);

            $queryRegistration = "SELECT cr.published_course_id
FROM course_registrations AS cr, published_courses AS ps
WHERE cr.published_course_id = ps.id
AND ps.id
IN (

SELECT published_course_id
FROM course_instructor_assignments
WHERE semester='$semester'
AND academic_year='$acadamic_year'
AND isprimary =1
)
AND cr.id NOT
IN (

SELECT course_registration_id
FROM exam_grades
WHERE course_registration_id IS NOT NULL
) $query $internalQuery  GROUP BY cr.published_course_id";


            if (!empty($queryRegistration)) {
                $distChangeList = $this->query($queryRegistration);

                debug($distChangeList);
                foreach ($distChangeList as $dk => $dl) {
                    $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                        $dl['cr']['published_course_id']
                    ); //double check since query report if one student even submit as delay
                    if (empty($gradeSubmitted)) {
                        $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                            'first',
                            array(
                                'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                                'contain' => array(
                                    'Section' => array(
                                        'YearLevel',
                                        'Program',
                                        'ProgramType',
                                        'Department' => array('College')
                                    ),
                                    'PublishedCourse' => array(
                                        'GivenByDepartment' => array('College'),
                                        'Course',
                                        'YearLevel'
                                    ),
                                    'Staff' => array('Title', 'Position')
                                )
                            )
                        );
                        //debug($courseInstructorAssignment);

                        $gradeSubmissionDeadline = ClassRegistry::init('AcademicCalendar')->recentAcademicYearSchedule(
                            $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                            $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                            $courseInstructorAssignment['PublishedCourse']['program_id'],
                            $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                            $courseInstructorAssignment['PublishedCourse']['department_id'],
                            $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                        );
                        $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                        $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                    }
                }
            }
        }
        return $lateGradeSubmissionList;
    }

    public function getGradeSubmittedInstructorList(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $year_level_id = null,
        $freshman = 0
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $query = '';
        $queryAdd = '';
        $lateGradeSubmissionList = array();

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and ps.program_id="' . $program_ids[1] . '"';
                $queryAdd .= ' and ps.program_id="' . $program_ids[1] . '"';
            } else {
                $query .= ' and ps.program_id="' . $program_id . '"';
                $queryAdd .= ' and ps.program_id="' . $program_id . '"';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';
                $queryAdd .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';
            } else {
                $query .= ' and ps.program_type_id="' . $program_type_id . '"';
                $queryAdd .= ' and ps.program_type_id="' . $program_type_id . '"';
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $query .= ' and ps.academic_year="' . $acadamic_year . '"';
            $query .= ' and cr.academic_year="' . $acadamic_year . '"';

            $queryAdd .= ' and ps.academic_year="' . $acadamic_year . '"';
            $queryAdd .= ' and ad.academic_year="' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and ps.semester="' . $semester . '"';
            $query .= ' and cr.semester="' . $semester . '"';

            $queryAdd .= ' and ps.semester="' . $semester . '"';
            $queryAdd .= ' and ad.semester="' . $semester . '"';
        }

        // list out the department
        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id[1], 'College.active' => 1))
                );
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
            $colleges = $this->Student->College->find(
                'all',
                array('conditions' => array('College.active' => 1), 'recursive' => -1)
            );
        }


        $gradeChangeList = array();

        if ($freshman == 1) {
            $departments = array();
        }

        if ($freshman == 0) {
            if (!empty($departments)) {
                foreach ($departments as $key => $value) {
                    $internalQuery = '';
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
                            $internalQuery = '';
                            if (!empty($year_level_id)) {
                                if ($yvalue['name'] == $year_level_id) {
                                    $internalQuery .= ' and ps.year_level_id="' . $yvalue['id'] . '"';
                                    $internalQuery .= ' and ps.department_id="' . $value['Department']['id'] . '"';

                                    $queryRegistration = "SELECT distinct cr.published_course_id
									FROM course_registrations AS cr, published_courses AS ps
									WHERE cr.published_course_id = ps.id
									AND ps.id IN (
										SELECT published_course_id
										FROM course_instructor_assignments
										WHERE semester='$semester' AND academic_year='$acadamic_year' AND isprimary =1
									)
									AND cr.id IN (
										SELECT course_registration_id
										FROM exam_grades
										WHERE course_registration_id IS NOT NULL
									) $query $internalQuery  GROUP BY ps.id";

                                    $queryCourseAdd = "SELECT distinct ad.published_course_id
									FROM course_adds AS ad, published_courses AS ps
									WHERE ad.published_course_id = ps.id
									AND ps.id IN (
										SELECT published_course_id
										FROM course_instructor_assignments
										WHERE semester='$semester' AND academic_year='$acadamic_year' AND isprimary =1
									)
									AND ad.id IN (
										SELECT course_add_id
										FROM exam_grades
										WHERE course_add_id IS NOT NULL
									) $queryAdd $internalQuery GROUP BY ps.id";
                                }
                            } else {
                                $internalQuery .= ' and ps.year_level_id="' . $yvalue['id'] . '"';
                                $internalQuery .= ' and ps.department_id="' . $value['Department']['id'] . '"';

                                $queryRegistration = "SELECT cr.published_course_id
								FROM course_registrations AS cr, published_courses AS ps
								WHERE cr.published_course_id = ps.id
								AND ps.id IN (
									SELECT published_course_id
									FROM course_instructor_assignments
									WHERE semester='$semester' AND academic_year='$acadamic_year' AND isprimary =1
								) AND cr.id  IN (
									SELECT course_registration_id
									FROM exam_grades
									WHERE course_registration_id IS NOT NULL
								) $query $internalQuery  GROUP BY cr.published_course_id";

                                $queryCourseAdd = "SELECT ad.published_course_id
								FROM course_adds AS ad, published_courses AS ps
								WHERE ad.published_course_id = ps.id
								AND ps.id IN (
									SELECT published_course_id
									FROM course_instructor_assignments
									WHERE semester='$semester' AND academic_year='$acadamic_year' AND isprimary =1
								) AND ad.id  IN (
									SELECT course_add_id
									FROM exam_grades
									WHERE course_add_id IS NOT NULL
								) $queryAdd $internalQuery  GROUP BY ad.published_course_id";
                            }

                            if (!empty($queryRegistration)) {
                                $distChangeList = $this->query($queryRegistration);

                                if (!empty($distChangeList)) {
                                    foreach ($distChangeList as $dk => $dl) {
                                        $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                                            $dl['cr']['published_course_id']
                                        ); //double check since query report if one student even submit as delay
                                        if (!empty($gradeSubmitted)) {
                                            $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                                'first',
                                                array(
                                                    'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                                                    'contain' => array(
                                                        'Section' => array(
                                                            'YearLevel',
                                                            'Program',
                                                            'ProgramType',
                                                            'Department' => array('College')
                                                        ),
                                                        'PublishedCourse' => array(
                                                            'GivenByDepartment' => array('College'),
                                                            'Course',
                                                            'YearLevel'
                                                        ),
                                                        'Staff' => array('Title', 'Position')
                                                    )
                                                )
                                            );

                                            $gradeSubmissionDeadline = ClassRegistry::init(
                                                'AcademicCalendar'
                                            )->recentAcademicYearSchedule(
                                                $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                                $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                                $courseInstructorAssignment['PublishedCourse']['program_id'],
                                                $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                                $courseInstructorAssignment['PublishedCourse']['department_id'],
                                                $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                                            );
                                            $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                                            $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                                        }
                                    }
                                }

                                /* }

                            if (!empty($queryCourseAdd)) { */

                                $distChangeListadd = $this->query($queryCourseAdd);

                                if (!empty($distChangeListadd)) {
                                    foreach ($distChangeListadd as $dk => $dl) {
                                        $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                                            $dl['ad']['published_course_id']
                                        ); //double check since query report if one student even submit as delay
                                        if (!empty($gradeSubmitted)) {
                                            $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                                'first',
                                                array(
                                                    'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['ad']['published_course_id']),
                                                    'contain' => array(
                                                        'Section' => array(
                                                            'YearLevel',
                                                            'Program',
                                                            'ProgramType',
                                                            'Department' => array('College')
                                                        ),
                                                        'PublishedCourse' => array(
                                                            'GivenByDepartment' => array('College'),
                                                            'Course',
                                                            'YearLevel'
                                                        ),
                                                        'Staff' => array('Title', 'Position')
                                                    )
                                                )
                                            );

                                            $gradeSubmissionDeadline = ClassRegistry::init(
                                                'AcademicCalendar'
                                            )->recentAcademicYearSchedule(
                                                $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                                $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                                $courseInstructorAssignment['PublishedCourse']['program_id'],
                                                $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                                $courseInstructorAssignment['PublishedCourse']['department_id'],
                                                $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                                            );
                                            $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                                            $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['ad']['published_course_id']] = $courseInstructorAssignment;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            // pre engineering
            if (!empty($colleges)) {
                foreach ($colleges as $key => $value) {
                    $internalQuery = '';
                    $internalQuery .= ' and (ps.year_level_id=0 or ps.year_level_id is null or ps.year_level_id = "" )';
                    $internalQuery .= ' and (ps.department_id=0 or ps.department_id is null)';

                    $queryRegistration = "SELECT cr.published_course_id
					FROM course_registrations AS cr, published_courses AS ps
					WHERE cr.published_course_id = ps.id
					AND ps.id IN (
						SELECT published_course_id
						FROM course_instructor_assignments
						WHERE semester='$semester' AND academic_year='$acadamic_year' AND isprimary =1
					) AND cr.id  IN (
						SELECT course_registration_id
						FROM exam_grades
						WHERE course_registration_id IS NOT NULL
					) $query $internalQuery  GROUP BY cr.published_course_id";

                    $queryCourseAdd = "SELECT ad.published_course_id
					FROM course_adds AS ad, published_courses AS ps
					WHERE ad.published_course_id = ps.id
					AND ps.id IN (
						SELECT published_course_id
						FROM course_instructor_assignments
						WHERE semester='$semester' AND academic_year='$acadamic_year' AND isprimary =1
					) AND ad.id  IN (
						SELECT course_add_id
						FROM exam_grades
						WHERE course_add_id IS NOT NULL
					) $queryAdd $internalQuery  GROUP BY ad.published_course_id";


                    if (!empty($queryRegistration)) {
                        $distChangeList = $this->query($queryRegistration);

                        if (!empty($distChangeList)) {
                            foreach ($distChangeList as $dk => $dl) {
                                $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                                    $dl['cr']['published_course_id']
                                ); //double check since query report if one student even submit as delay
                                if (!empty($gradeSubmitted)) {
                                    $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                        'first',
                                        array(
                                            'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                                            'contain' => array(
                                                'Section' => array(
                                                    'YearLevel',
                                                    'Program',
                                                    'ProgramType',
                                                    'Department' => array('College')
                                                ),
                                                'PublishedCourse' => array(
                                                    'GivenByDepartment' => array('College'),
                                                    'Course',
                                                    'YearLevel'
                                                ),
                                                'Staff' => array('Title', 'Position')
                                            )
                                        )
                                    );

                                    $collID = ClassRegistry::init('PublishedCourse')->field(
                                        'college_id',
                                        array('PublishedCourse.id' => $dl['cr']['published_course_id'])
                                    );

                                    $gradeSubmissionDeadline = ClassRegistry::init(
                                        'AcademicCalendar'
                                    )->recentAcademicYearSchedule(
                                        $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                        $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                        $courseInstructorAssignment['PublishedCourse']['program_id'],
                                        $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                        $courseInstructorAssignment['PublishedCourse']['department_id'],
                                        '1st',
                                        1,
                                        $collID
                                    );
                                    $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                                    $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                                }
                            }
                        }
                        /* }

                    if (!empty($queryCourseAdd)) { */

                        $distChangeListadd = $this->query($queryCourseAdd);

                        if (!empty($distChangeListadd)) {
                            foreach ($distChangeListadd as $dk => $dl) {
                                $gradeSubmitted = $this->Student->CourseRegistration->ExamGrade->is_grade_submitted(
                                    $dl['ad']['published_course_id']
                                ); //double check since query report if one student even submit as delay
                                if (!empty($gradeSubmitted)) {
                                    $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                        'first',
                                        array(
                                            'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['ad']['published_course_id']),
                                            'contain' => array(
                                                'Section' => array(
                                                    'YearLevel',
                                                    'Program',
                                                    'ProgramType',
                                                    'Department' => array('College')
                                                ),
                                                'PublishedCourse' => array(
                                                    'GivenByDepartment' => array('College'),
                                                    'Course',
                                                    'YearLevel'
                                                ),
                                                'Staff' => array('Title', 'Position')
                                            )
                                        )
                                    );

                                    $collID = ClassRegistry::init('PublishedCourse')->field(
                                        'college_id',
                                        array('PublishedCourse.id' => $dl['ad']['published_course_id'])
                                    );

                                    $gradeSubmissionDeadline = ClassRegistry::init(
                                        'AcademicCalendar'
                                    )->recentAcademicYearSchedule(
                                        $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                        $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                        $courseInstructorAssignment['PublishedCourse']['program_id'],
                                        $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                        $courseInstructorAssignment['PublishedCourse']['department_id'],
                                        '1st',
                                        1,
                                        $collID
                                    );
                                    $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmissionDeadline['AcademicCalendar']['grade_submission_end_date'];

                                    $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['ad']['published_course_id']] = $courseInstructorAssignment;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $lateGradeSubmissionList;
    }

    public function getDelayedGradeSubmissionList(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $year_level_id = null,
        $freshman = 0
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }
        $query = '';
        $lateGradeSubmissionList = array();

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and ps.program_id="' . $program_ids[1] . '"';
            } else {
                $query .= ' and ps.program_id="' . $program_id . '"';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and ps.program_type_id="' . $program_type_ids[1] . '"';
            } else {
                $query .= ' and ps.program_type_id="' . $program_type_id . '"';
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $query .= ' and ps.academic_year="' . $acadamic_year . '"';
            $query .= ' and cr.academic_year="' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and ps.semester="' . $semester . '"';
            $query .= ' and cr.semester="' . $semester . '"';
        }

        // list out the department
        if ($freshman == 0) {
            if (isset($department_id) && !empty($department_id)) {
                $college_id = explode('~', $department_id);
                if (count($college_id) > 1) {
                    $departments = $this->Student->Department->find('all', array(
                        'conditions' => array(
                            'Department.college_id' => $college_id[1],
                            'Department.active' => 1
                        ),
                        'contain' => array('College', 'YearLevel')
                    ));
                } else {
                    $departments = $this->Student->Department->find(
                        'all',
                        array(
                            'conditions' => array('Department.id' => $department_id),
                            'contain' => array('College', 'YearLevel')
                        )
                    );
                }
            } else {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            }
        }

        $gradeSubmissionDeadline = ClassRegistry::init('AcademicCalendar')->getGradeSubmissionDate(
            $acadamic_year,
            $semester,
            $program_id,
            $program_type_id,
            $department_id,
            $year_level_id
        );

        debug($gradeSubmissionDeadline);


        $deadLineWhereSQL = '';
        $gradeSubmissionEnd = '';
        if (isset($gradeSubmissionDeadline) && !empty($gradeSubmissionDeadline)) {
            $deadLineWhereSQL .= ' and created >="' . $gradeSubmissionDeadline . '"';
            $gradeSubmissionEnd = $gradeSubmissionDeadline;
        }
        $gradeChangeList = array();

        if ($freshman == 0) {
            foreach ($departments as $key => $value) {
                $internalQuery = '';
                $yearLevel = array();
                if (!empty($year_level_id)) {
                    foreach ($value['YearLevel'] as $yykey => $yyvalue) {
                        if (
                            !empty($year_level_id)
                            && strcasecmp($year_level_id, $yyvalue['name']) == 0
                        ) {
                            $yearLevel[$yykey] = $yyvalue;
                        }
                    }
                } elseif (empty($year_level_id)) {
                    $yearLevel = $value['YearLevel'];
                }
                foreach ($yearLevel as $ykey => $yvalue) {
                    $internalQuery .= ' and ps.year_level_id="' . $yvalue['id'] . '"';
                    $internalQuery .= ' and ps.given_by_department_id="' . $value['Department']['id'] . '"';

                    $queryRegistration = "SELECT cr.published_course_id
					FROM course_registrations AS cr, published_courses AS ps
					WHERE cr.published_course_id = ps.id
					AND ps.id
					IN (

					SELECT published_course_id
					FROM course_instructor_assignments
					WHERE semester='$semester'
					AND academic_year='$acadamic_year'
					AND isprimary =1
					)
					AND cr.id
					IN (
					SELECT course_registration_id
					FROM exam_grades
					WHERE
					course_registration_id IS NOT NULL
					$deadLineWhereSQL
					) $query $internalQuery
					GROUP BY cr.published_course_id ";

                    $internalQuery = '';
                    if (!empty($queryRegistration)) {
                        $distChangeList = $this->query($queryRegistration);
                        foreach ($distChangeList as $dk => $dl) {
                            $gradeSubmittedDate = $this->Student->CourseRegistration->ExamGrade->getGradeSubmmissionDate(
                                $dl['cr']['published_course_id']
                            );

                            $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                                'first',
                                array(
                                    'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                                    'contain' => array(
                                        'Section' => array(
                                            'YearLevel',
                                            'Program',
                                            'ProgramType',
                                            'Department' => array('College')
                                        ),
                                        'PublishedCourse' => array(
                                            'GivenByDepartment' => array('College'),
                                            'Course',
                                            'YearLevel'
                                        ),
                                        'Staff' => array('Title', 'Position')
                                    )
                                )
                            );

                            $gradeSubmissionDeadline = ClassRegistry::init(
                                'AcademicCalendar'
                            )->recentAcademicYearSchedule(
                                $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                                $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                                $courseInstructorAssignment['PublishedCourse']['program_id'],
                                $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                                $courseInstructorAssignment['PublishedCourse']['department_id'],
                                $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                            );
                            $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmittedDate['ExamGrade']['created'];

                            $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                        }
                    }
                }
            }
        } else {
            $college_id = explode('~', $department_id);

            debug($department_id);
            debug($college_id);

            $internalQuery = '';
            $internalQuery .= ' and (ps.year_level_id is null or ps.year_level_id=0 )';
            if (isset($college_id[1]) && !empty($college_id[1])) {
                $internalQuery .= ' and ps.college_id="' . $college_id[1] . '"';
            }
            debug($deadLineWhereSQL);


            $queryRegistration = "SELECT cr.published_course_id
					FROM course_registrations AS cr, published_courses AS ps
					WHERE cr.published_course_id = ps.id
					AND ps.id
					IN (

					SELECT published_course_id
					FROM course_instructor_assignments
					WHERE semester='$semester'
					AND academic_year='$acadamic_year'
					AND isprimary =1
					)
					AND cr.id
					IN (
					SELECT course_registration_id
					FROM exam_grades
					WHERE
					course_registration_id IS NOT NULL
					) $query $internalQuery
					GROUP BY cr.published_course_id ";
            debug($queryRegistration);


            if (!empty($queryRegistration)) {
                $distChangeList = $this->query($queryRegistration);
                debug($distChangeList);
                foreach ($distChangeList as $dk => $dl) {
                    $gradeSubmittedDate = $this->Student->CourseRegistration->ExamGrade->getGradeSubmmissionDate(
                        $dl['cr']['published_course_id']
                    );

                    $courseInstructorAssignment = $this->Student->CourseRegistration->PublishedCourse->CourseInstructorAssignment->find(
                        'first',
                        array(
                            'conditions' => array('CourseInstructorAssignment.published_course_id' => $dl['cr']['published_course_id']),
                            'contain' => array(
                                'Section' => array(
                                    'YearLevel',
                                    'Program',
                                    'ProgramType',
                                    'Department' => array('College')
                                ),
                                'PublishedCourse' => array(
                                    'GivenByDepartment' => array('College'),
                                    'Course',
                                    'YearLevel'
                                ),
                                'Staff' => array('Title', 'Position')
                            )
                        )
                    );

                    $gradeSubmissionDeadline = ClassRegistry::init('AcademicCalendar')->recentAcademicYearSchedule(
                        $courseInstructorAssignment['CourseInstructorAssignment']['academic_year'],
                        $courseInstructorAssignment['CourseInstructorAssignment']['semester'],
                        $courseInstructorAssignment['PublishedCourse']['program_id'],
                        $courseInstructorAssignment['PublishedCourse']['program_type_id'],
                        $courseInstructorAssignment['PublishedCourse']['department_id'],
                        $courseInstructorAssignment['PublishedCourse']['YearLevel']['name']
                    );
                    debug($gradeSubmittedDate);
                    $courseInstructorAssignment['CourseInstructorAssignment']['grade_submission_deadline'] = $gradeSubmittedDate['ExamGrade']['created'];

                    $lateGradeSubmissionList[$courseInstructorAssignment['PublishedCourse']['GivenByDepartment']['name']][$courseInstructorAssignment['PublishedCourse']['Course']['course_title'] . '(' . $courseInstructorAssignment['PublishedCourse']['Course']['course_code'] . ')'][$dl['cr']['published_course_id']] = $courseInstructorAssignment;
                }
            }
        }
        return $lateGradeSubmissionList;
    }


    public function getDismissedStudent(
        $acadamic_year = null,
        $semester = null,
        $program_id = 0,
        $program_type_id = 0,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $freshman = 0,
        $exclude_graduated = 0
    ) {

        $regions = $this->Student->Region->find('list');
        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }
        //okay
        $query = '';
        $secQueryIn = ' id is not null ';
        $college_id = array();

        $query .= ' and stexam.academic_status_id =' . DISMISSED_ACADEMIC_STATUS_ID . '';

        if (!empty($region_id) && $region_id > 0) {
            $query .= ' and s.region_id = ' . $region_id . '';
        }

        if (!empty($sex) && $sex != "all") {
            $query .= ' and s.gender = "' . $sex . '"';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $query .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
                $secQueryIn .= ' and program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $query .= ' and s.program_id = ' . $program_id . '';
                $secQueryIn .= ' and program_id =' . $program_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_id = 0';
                $secQueryIn .= ' and program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $query .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
                $secQueryIn .= ' and program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $query .= ' and s.program_type_id = ' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_type_id = 0';
                $secQueryIn .= ' and program_type_id = 0';
            }
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
        }

        if (!empty($acadamic_year)) {
            $secQueryIn .= ' and academicyear = "' . $acadamic_year . '"';
            $query .= ' and stexam.academic_year = "' . $acadamic_year . '"';
        }

        if (!empty($semester)) {
            $query .= ' and stexam.semester = "' . $semester . '"';
        }

        $dismisstedLists = array();

        $secQueryYD = '';
        $count = 0;

        if ($freshman == 1) {
            $departments = array();
            $colleges = array();
        }

        if ($freshman == 0) {
            if (!empty($departments)) {
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
                            $deptID = $value['Department']['id'];

                            $secQueryYD .= ' and year_level_id = "' . $yvalue['id'] . '"';
                            $secQueryYD .= ' and department_id = "' . $value['Department']['id'] . '"';

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'order' => array(
                                    'StudentsSection.id' => 'DESC',
                                    'StudentsSection.modified' => 'DESC',
                                    'StudentsSection.section_id' => 'DESC'
                                )
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);

                            $student_ids = implode(", ", $x);

                            $dismisstedListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
							FROM students AS s, student_exam_statuses AS stexam
							WHERE s.department_id = $deptID AND stexam.student_id = s.id $query AND stexam.student_id IN ($student_ids) GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa ";

                            $disResult = $this->query($dismisstedListSQL);

                            if (!empty($disResult)) {
                                foreach ($disResult as $dr) {
                                    $credit_type = 'Credit';

                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                        'contain' => array('Curriculum' => array('id', 'type_credit')),
                                        'recursive' => -1
                                    ));

                                    if (!empty($secName['Curriculum']['id']) && (count(
                                                explode('ECTS', $secName['Curriculum']['type_credit'])
                                            ) >= 2)) {
                                        $credit_type = 'ECTS';
                                    }

                                    $mg = array_merge($dr['s'], $dr['stexam']);

                                    $dismisstedLists[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] /* . '~' . $secstulist[$dr['s']['id']] */ . '~' . $yvalue['name'] . '~' . $credit_type][$count] = $mg;
                                    $count++;
                                }
                            }

                            $dismisstedListSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering
            $college_id = array();
            $colleges = array();

            if (isset($department_id) /* && !empty($department_id) */) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.id' => $college_ids[1], 'College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                }
            }

            if (isset($college_id) && !empty($college_id)) {
                //debug($college_id);
                $secQueryYD = '';

                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id, 'College.active' => 1), 'recursive' => -1)
                );

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $secQueryYD .= ' and college_id ="' . $cv['College']['id'] . '" and department_id is null and id is not null  and (year_level_id is null OR year_level_id = 0 OR year_level_id ="")';

                        $collID = $cv['College']['id'];

                        $sectionLists = ClassRegistry::init('Section')->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $cv['College']['id'],
                                'Section.academicyear' => $acadamic_year,
                                'Section.department_id is null'
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                        ));

                        if (isset($sectionLists) && !empty($sectionLists)) {
                            $secstulist = ClassRegistry::init('StudentsSection')->find('list', array(
                                'conditions' => array(
                                    'StudentsSection.section_id' => $sectionLists
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'order' => array(
                                    'StudentsSection.id' => 'DESC',
                                    'StudentsSection.modified' => 'DESC',
                                    'StudentsSection.section_id' => 'DESC'
                                )
                            ));
                        }

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $dismisstedListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
						FROM students AS s, student_exam_statuses AS stexam
						WHERE s.college_id = $collID AND s.department_id IS NULL AND stexam.student_id = s.id $query AND stexam.student_id IN ($student_ids) GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa";

                        $disResult = $this->query($dismisstedListSQL);

                        $credit_type = 'Credit';

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $count++;

                                $secName = ClassRegistry::init('Section')->find('first', array(
                                    'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                    'recursive' => -1
                                ));

                                $mg = array_merge($dr['s'], $dr['stexam']);

                                $dismisstedLists[$cv['College']['name'] . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman') . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] /* . '~' . $secstulist[$dr['s']['id']] */ . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . '~' . $credit_type][$count] = $mg;
                            }
                        }

                        $dismisstedListSQL = '';
                    }
                }
            }
        }

        return $dismisstedLists;
    }

    public function getActiveStudent(
        $acadamic_year = null,
        $semester = null,
        $program_id = 0,
        $program_type_id = 0,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $freshman = 0,
        $exclude_graduated = 0
    ) {

        $regions = $this->Student->Region->find('list');
        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }
        //okay
        $query = '';
        $secQueryIn = ' id is not null ';
        $college_id = array();

        //$query .= ' and stexam.academic_status_id !=' . DISMISSED_ACADEMIC_STATUS_ID . '';

        if (!empty($region_id) && $region_id > 0) {
            $query .= ' and s.region_id = ' . $region_id . '';
        }

        if (!empty($sex) && $sex != "all") {
            $query .= ' and s.gender = "' . $sex . '"';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $query .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
                $secQueryIn .= ' and program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $query .= ' and s.program_id = ' . $program_id . '';
                $secQueryIn .= ' and program_id =' . $program_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_id = 0';
                $secQueryIn .= ' and program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $query .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
                $secQueryIn .= ' and program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $query .= ' and s.program_type_id = ' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_type_id = 0';
                $secQueryIn .= ' and program_type_id = 0';
            }
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
        }

        if (!empty($acadamic_year)) {
            $secQueryIn .= ' and academicyear = "' . $acadamic_year . '"';
            $query .= ' and stexam.academic_year = "' . $acadamic_year . '"';
        }

        if (!empty($semester)) {
            $query .= ' and stexam.semester = "' . $semester . '"';
        }

        $activeLists = array();

        $secQueryYD = '';
        $count = 0;

        if ($freshman == 1) {
            $departments = array();
            $colleges = array();
        }

        if ($freshman == 0) {
            if (!empty($departments)) {
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
                            $deptID = $value['Department']['id'];

                            $secQueryYD .= ' and year_level_id = "' . $yvalue['id'] . '"';
                            $secQueryYD .= ' and department_id = "' . $value['Department']['id'] . '"';

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'order' => array(
                                    'StudentsSection.id' => 'DESC',
                                    'StudentsSection.modified' => 'DESC',
                                    'StudentsSection.section_id' => 'DESC'
                                )
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);

                            $student_ids = implode(", ", $x);

                            $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
							FROM students AS s, student_exam_statuses AS stexam
							WHERE s.department_id = $deptID AND stexam.student_id = s.id $query AND stexam.student_id IN ($student_ids) GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa ";

                            $disResult = $this->query($activeListSQL);

                            if (!empty($disResult)) {
                                foreach ($disResult as $dr) {
                                    $credit_type = 'Credit';

                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                        'contain' => array('Curriculum' => array('id', 'type_credit')),
                                        'recursive' => -1
                                    ));

                                    if (!empty($secName['Curriculum']['id']) && (count(
                                                explode('ECTS', $secName['Curriculum']['type_credit'])
                                            ) >= 2)) {
                                        $credit_type = 'ECTS';
                                    }

                                    $mg = array_merge($dr['s'], $dr['stexam']);

                                    $activeLists[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] /* . '~' . $secstulist[$dr['s']['id']] */ . '~' . $yvalue['name'] . '~' . $credit_type][$count] = $mg;
                                    $count++;
                                }
                            }

                            $activeListSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering
            $college_id = array();
            $colleges = array();

            $programs_available_for_registrar_college_level_permissions = Configure::read(
                'programs_available_for_registrar_college_level_permissions'
            );
            $program_types_available_for_registrar_college_level_permissions = Configure::read(
                'program_types_available_for_registrar_college_level_permissions'
            );

            if (empty($programs_available_for_registrar_college_level_permissions)) {
                $programs_available_for_registrar_college_level_permissions = 0;
            }

            if (empty($program_types_available_for_registrar_college_level_permissions)) {
                $program_types_available_for_registrar_college_level_permissions = 0;
            }

            if (isset($department_id) /* && !empty($department_id) */) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.id' => $college_ids[1], 'College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                }
            }

            $query = ' and stexam.academic_year = "' . $acadamic_year . '" and stexam.semester = "' . $semester . '"';

            if (isset($college_id) && !empty($college_id)) {
                //debug($college_id);
                $secQueryYD = '';

                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id, 'College.active' => 1), 'recursive' => -1)
                );

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $collID = $cv['College']['id'];

                        /* $secQueryYD .= ' and college_id ="' . $cv['College']['id'] . '" and department_id is null and id is not null  and (year_level_id is null OR year_level_id = 0 OR year_level_id ="")';

                        $sectionLists = ClassRegistry::init('Section')->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $cv['College']['id'],
                                'Section.academicyear' => $acadamic_year,
                                'Section.department_id is null'
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                        ));

                        if (isset($sectionLists) && !empty($sectionLists)) {

                            $secstulist = ClassRegistry::init('StudentsSection')->find('list', array(
                                'conditions' => array(
                                    'StudentsSection.section_id' => $sectionLists
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'order' => array('StudentsSection.id' => 'DESC','StudentsSection.modified' => 'DESC', 'StudentsSection.section_id' => 'DESC')
                            ));
                        }

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
                        FROM students AS s, student_exam_statuses AS stexam
                        WHERE s.college_id = $collID AND s.department_id IS NULL AND stexam.student_id = s.id $query AND stexam.student_id IN ($student_ids) GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa";

                        */

                        $college_sections = $this->Student->Section->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $collID,
                                'Section.academicyear' => $acadamic_year,
                                'Section.program_id' => (!empty($program_id) ? $program_id : $programs_available_for_registrar_college_level_permissions),
                                //'Section.program_type_id' => (!empty($program_type_id) ? $program_type_id : $program_types_available_for_registrar_college_level_permissions),
                                'OR' => array(
                                    'Section.department_id IS NULL',
                                    'Section.department_id = 0',
                                    'Section.department_id = ""',
                                ),
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                        ));

                        //debug($college_sections);

                        if (empty($college_sections)) {
                            continue;
                        }

                        $secstulist = ClassRegistry::init('StudentsSection')->find('list', array(
                            'conditions' => array(
                                'StudentsSection.section_id' => $college_sections
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'order' => array(
                                'StudentsSection.id' => 'DESC',
                                'StudentsSection.modified' => 'DESC',
                                'StudentsSection.section_id' => 'DESC'
                            )
                        ));

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        //exit();

                        $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
						FROM students AS s, student_exam_statuses AS stexam
						WHERE s.college_id = $collID AND stexam.student_id IN ($student_ids) AND stexam.student_id = s.id $query GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa";


                        $disResult = $this->query($activeListSQL);

                        if (empty($disResult)) {
                            continue;
                        }

                        $credit_type = 'Credit';

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $count++;

                                $secName = ClassRegistry::init('Section')->find('first', array(
                                    'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                    'recursive' => -1
                                ));

                                $mg = array_merge($dr['s'], $dr['stexam']);

                                $activeLists[$cv['College']['name'] . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman') . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] /* . '~' . $secstulist[$dr['s']['id']] */ . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . '~' . $credit_type][$count] = $mg;
                            }
                        }

                        $activeListSQL = '';
                    }
                }
            }
        }

        return $activeLists;
    }

    public function getActiveStudentNotRegistered(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $currentAcademicYear,
        $currentSemester,
        $freshman = 0,
        $exclude_graduated = ''
    ) {

        //$regions = $this->Student->Region->find('list');
        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $query = '';
        $secQueryIn = ' id is not null ';
        $college_id = array();

        //$query .= ' and stexam.academic_status_id != "' . DISMISSED_ACADEMIC_STATUS_ID . '"';

        if (!empty($region_id) && $region_id > 0) {
            $query .= ' and s.region_id =' . $region_id . '';
        }

        if (!empty($sex) && $sex != "all") {
            $query .= ' and s.gender = "' . $sex . '"';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        /* if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and s.program_id=' . $program_ids[1] . '';
                $secQueryIn .= ' and program_id=' . $program_ids[1] . '';
            } else {
                $query .= ' and s.program_id=' . $program_id . '';
                $secQueryIn .= ' and program_id=' . $program_id . '';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_ids[1] . '';
            } else {
                $query .= ' and s.program_type_id=' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_id . '';
            }
        } */

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $query .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
                $secQueryIn .= ' and program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $query .= ' and s.program_id = ' . $program_id . '';
                $secQueryIn .= ' and program_id =' . $program_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_id = 0';
                $secQueryIn .= ' and program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $query .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
                $secQueryIn .= ' and program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $query .= ' and s.program_type_id = ' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_type_id = 0';
                $secQueryIn .= ' and program_type_id = 0';
            }
        }

        // list out the department
        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
        }

        if (!empty($acadamic_year)) {
            $query .= ' and stexam.academic_year = "' . $acadamic_year . '"';
            $secQueryIn .= ' and academicyear = "' . $acadamic_year . '"';
        }

        if (!empty($semester)) {
            $query .= ' and stexam.semester = "' . $semester . '"';
        }

        if ($freshman == 1) {
            $departments = array();
        }

        $activeLists = array();
        $secQueryYD = '';
        $count = 0;

        if ($freshman == 0) {
            if (!empty($departments)) {
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
                            $secQueryYD .= ' and year_level_id="' . $yvalue['id'] . '"';
                            $secQueryYD .= ' and department_id="' . $value['Department']['id'] . '"';

                            $deptID = $value['Department']['id'];

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);
                            $student_ids = implode(", ", $x);

                            $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
							FROM students AS s, student_exam_statuses AS stexam
							WHERE s.department_id = $deptID AND s.graduated = 0 AND stexam.student_id = s.id $query AND stexam.student_id IN ($student_ids) GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa ";

                            $disResult = $this->query($activeListSQL);

                            if (!empty($disResult)) {
                                foreach ($disResult as $dr) {
                                    $registeredCheck = $this->Student->CourseRegistration->find('count', array(
                                        'conditions' => array(
                                            'CourseRegistration.student_id' => $dr['s']['id'],
                                            'CourseRegistration.semester' => $currentSemester,
                                            'CourseRegistration.academic_year' => $currentAcademicYear,
                                        ),
                                        'recursive' => -1
                                    ));

                                    $gradutionCheck = $this->Student->GraduateList->find(
                                        'count',
                                        array(
                                            'conditions' => array('GraduateList.student_id' => $dr['s']['id']),
                                            'recursive' => -1
                                        )
                                    );

                                    if ($gradutionCheck == 0 && $registeredCheck == 0 && $dr['s']['id']) {
                                        $credit_type = 'Credit';

                                        $secName = ClassRegistry::init('Section')->find('first', array(
                                            'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                            'contain' => array('Curriculum' => array('id', 'type_credit')),
                                            'recursive' => -1
                                        ));

                                        if (!empty($secName['Curriculum']['id']) && (count(
                                                    explode('ECTS', $secName['Curriculum']['type_credit'])
                                                ) >= 2)) {
                                            $credit_type = 'ECTS';
                                        }

                                        $mg = array_merge($dr['s'], $dr['stexam']);

                                        $activeLists[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester']/*  . '~' . $secstulist[$dr['s']['id']] */ . '~' . $yvalue['name'] . '~' . $credit_type][$count] = $mg;
                                        $count++;
                                    }
                                }
                            }

                            $activeListSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering

            $college_id = array();
            $colleges = array();

            if (isset($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.id' => $college_ids[1], 'College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                }
            }

            if (!empty($college_id)) {
                debug($college_id);

                $secQueryYD = '';

                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
                );

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $secQueryYD .= ' and college_id = "' . $cv['College']['id'] . '" and department_id is null and id is not null and (year_level_id is null OR year_level_id = 0 OR year_level_id = "")';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
                        ));

                        debug(count($secstulist));
                        $secQueryYD = '';

                        $collID = $cv['College']['id'];

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
						FROM students AS s, student_exam_statuses AS stexam
						WHERE s.college_id = $collID AND s.graduated = 0 AND stexam.student_id = s.id $query AND stexam.student_id IN ($student_ids) GROUP BY stexam.academic_year, stexam.semester, stexam.student_id ORDER BY s.first_name, stexam.cgpa ";

                        $disResult = $this->query($activeListSQL);

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $registeredCheck = $this->Student->CourseRegistration->find('count', array(
                                    'conditions' => array(
                                        'CourseRegistration.student_id' => $dr['s']['id'],
                                        'CourseRegistration.semester' => $currentSemester,
                                        'CourseRegistration.academic_year' => $currentAcademicYear,
                                        'OR' => array(
                                            'CourseRegistration.year_level_id is null',
                                            'CourseRegistration.year_level_id = 0',
                                            'CourseRegistration.year_level_id = ""',
                                        )
                                    ),
                                    'recursive' => -1
                                ));

                                $gradutionCheck = $this->Student->GraduateList->find(
                                    'count',
                                    array(
                                        'conditions' => array('GraduateList.student_id' => $dr['s']['id']),
                                        'recursive' => -1
                                    )
                                );

                                $credit_type = 'Credit';

                                if ($gradutionCheck == 0 && $registeredCheck == 0 && $dr['s']['id']) {
                                    $count++;

                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                        'recursive' => -1
                                    ));

                                    $mg = array_merge($dr['s'], $dr['stexam']);

                                    $activeLists[$cv['College']['name'] . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman') . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester']/*  . '~' . $secstulist[$dr['s']['id']] */ . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . '~' . $credit_type][$count] = $mg;
                                }
                            }
                        }
                        $activeListSQL = '';
                    }
                }
            }
        }

        return $activeLists;
    }

    public function getRegisteredStudentList(
        $acadamic_year = null,
        $semester = null,
        $program_id = 0,
        $program_type_id = 0,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $freshman = 0,
        $exclude_graduated = ''
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $queryR = '';
        $secQueryIn = ' id is not null ';
        $secQueryIn = ' id is not null ';

        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        if (!empty($region_id) && $region_id > 0) {
            $queryR .= ' and s.region_id=' . $region_id . '';
        }

        if (!empty($sex) && $sex != "all") {
            $queryR .= ' and s.gender LIKE "' . $sex . '%"';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $queryR .= ' and s.graduated = 0';
        }

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $queryR .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
                $secQueryIn .= ' and program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $queryR .= ' and s.program_id = ' . $program_id . '';
                $secQueryIn .= ' and program_id =' . $program_id . '';
            } else {
                // prevent any access
                $queryR .= ' and s.program_id = 0';
                $secQueryIn .= ' and program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $queryR .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
                $secQueryIn .= ' and program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $queryR .= ' and s.program_type_id = ' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $queryR .= ' and s.program_type_id = 0';
                $secQueryIn .= ' and program_type_id = 0';
            }
        }


        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $secQueryIn .= ' and academicyear="' . $acadamic_year . '"';
            $queryR .= ' and reg.academic_year="' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $queryR .= ' and reg.semester="' . $semester . '"';
        }

        if ($freshman == 1) {
            $departments = array();
        }

        $secQueryYD = '';
        $count = 0;

        $studentListRegistered = array();

        if ($freshman == 0) {
            if (!empty($departments)) {
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
                            $ylID = $yvalue['id'];
                            $deptID = $value['Department']['id'];

                            $secQueryYD .= ' and year_level_id="' . $ylID . '"';
                            $secQueryYD .= ' and department_id="' . $deptID . '"';

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD)"
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'order' => array(
                                    'StudentsSection.id' => 'DESC',
                                    'StudentsSection.modified' => 'DESC',
                                    'StudentsSection.section_id' => 'DESC'
                                )
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);
                            $student_ids = implode(", ", $x);

                            $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.program_id, s.program_type_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated, s.admissionyear, s.curriculum_id, s.department_id, s.college_id
							FROM students AS s, course_registrations AS reg
							WHERE s.department_id = $deptID AND reg.year_level_id = $ylID AND reg.student_id = s.id $queryR AND reg.student_id IN ($student_ids) GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id ORDER BY reg.academic_year DESC, reg.semester DESC, reg.section_id, s.first_name, s.middle_name, s.last_name ";

                            $disResultRegistration = $this->query($studentListRegisteredSQL);

                            if (empty($disResultRegistration)) {
                                continue;
                            }

                            if (!empty($disResultRegistration)) {
                                foreach ($disResultRegistration as $dr) {
                                    $credit_type = 'Credit';

                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array('Section.id' => $dr['reg']['section_id']),
                                        'contain' => array('Curriculum' => array('id', 'type_credit')),
                                        'fields' => array('Section.id', 'Section.name'),
                                        'recursive' => -1
                                    ));

                                    if (!empty($secName['Curriculum']['id']) && (count(
                                                explode('ECTS', $secName['Curriculum']['type_credit'])
                                            ) >= 2)) {
                                        $credit_type = 'ECTS';
                                    }

                                    $load = $this->Student->calculateStudentLoad(
                                        $dr['s']['id'],
                                        $semester,
                                        $acadamic_year,
                                        1
                                    );
                                    //debug($load);

                                    $mg = array_merge($dr['s'], $dr['reg'], $load);

                                    $studentListRegistered[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['reg']['academic_year'] . '~' . $dr['reg']['semester'] . '~' . $yvalue['name'] . '~' . $credit_type][$count] = $mg;
                                    $count++;
                                }
                            }

                            //debug($studentListRegistered);
                            $studentListRegisteredSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering
            $college_id = array();
            $colleges = array();

            $programs_available_for_registrar_college_level_permissions = Configure::read(
                'programs_available_for_registrar_college_level_permissions'
            );
            $program_types_available_for_registrar_college_level_permissions = Configure::read(
                'program_types_available_for_registrar_college_level_permissions'
            );

            if (empty($programs_available_for_registrar_college_level_permissions)) {
                $programs_available_for_registrar_college_level_permissions = 0;
            }

            if (empty($program_types_available_for_registrar_college_level_permissions)) {
                $program_types_available_for_registrar_college_level_permissions = 0;
            }

            if (isset($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.id' => $college_ids[1], 'College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                }
            }

            $queryR = ' AND reg.academic_year = "' . $acadamic_year . '" AND reg.semester = "' . $semester . '"';

            if (!empty($college_id)) {
                //debug($college_id);
                $secQueryYD = '';
                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
                );
                //debug($colleges);

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $collegeID = $cv['College']['id'];

                        /*
                        $secQueryYD .= ' and college_id ="' . $collegeID . '" and department_id is null and id is not null and (year_level_id is null OR year_level_id = 0 OR year_level_id = "")';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'order' => array('StudentsSection.id' => 'DESC','StudentsSection.modified' => 'DESC', 'StudentsSection.section_id' => 'DESC')
                        ));

                        debug(count($secstulist));
                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated
                        FROM students AS s, course_registrations AS reg
                        WHERE s.college_id = $collegeID AND (reg.year_level_id IS NULL OR reg.year_level_id = '' OR reg.year_level_id = 0) AND reg.student_id = s.id $queryR AND reg.student_id IN ($student_ids) GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id ORDER BY reg.academic_year DESC, reg.semester DESC, reg.section_id, s.first_name, s.middle_name, s.last_name ";

                        */

                        $college_sections = $this->Student->Section->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $collegeID,
                                'Section.academicyear' => $acadamic_year,
                                'Section.program_id' => (!empty($program_id) ? $program_id : $programs_available_for_registrar_college_level_permissions),
                                //'Section.program_type_id' => (!empty($program_type_id) ? $program_type_id : $program_types_available_for_registrar_college_level_permissions),
                                'OR' => array(
                                    'Section.department_id IS NULL',
                                    'Section.department_id = 0',
                                    'Section.department_id = ""',
                                ),
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                        ));

                        //debug($college_sections);

                        if (empty($college_sections)) {
                            continue;
                        }

                        $college_section_ids = implode(", ", $college_sections);

                        debug($college_section_ids);

                        //exit();

                        $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated
						FROM students AS s, course_registrations AS reg
						WHERE reg.section_id IN ($college_section_ids) AND reg.student_id = s.id $queryR GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id ORDER BY reg.academic_year DESC, reg.semester DESC, reg.section_id, s.first_name, s.middle_name, s.last_name ";

                        $disResultRegistration = $this->query($studentListRegisteredSQL);

                        if (empty($disResultRegistration)) {
                            continue;
                        }

                        $credit_type = 'Credit';

                        if (!empty($disResultRegistration)) {
                            foreach ($disResultRegistration as $dr) {
                                $secName = ClassRegistry::init('Section')->find('first', array(
                                    'conditions' => array(
                                        'Section.id' => $dr['reg']['section_id'],
                                        'Section.department_id is null',
                                    ),
                                    'fields' => array('Section.id', 'Section.name', 'Section.program_id'),
                                    'recursive' => -1
                                ));

                                $load = $this->Student->calculateStudentLoad(
                                    $dr['s']['id'],
                                    $semester,
                                    $acadamic_year,
                                    1
                                );
                                //debug($load);

                                $mg = array_merge($dr['s'], $dr['reg'], $load);

                                $studentListRegistered[$cv['College']['name'] . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : ' Pre/Fresh') . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['reg']['academic_year'] . '~' . $dr['reg']['semester'] . '~' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . '~' . $credit_type][$count] = $mg;
                                $count++;
                            }
                        }

                        //debug($studentListRegistered);
                        $studentListRegisteredSQL = '';
                    }
                }
            }
        }
        return $studentListRegistered;
    }

    function getNumberOfDismissedStudent($acadamic_year = null, $semester = null, $department_id = null)
    {

        $acSem['prevACSem'] = array();
        $acSem['dismissedTotalCount'] = 0;
        $acSem['dismissedFemaleTotalCount'] = 0;
        $acSem['dismissedMaleTotalCount'] = 0;
        $acSem['totalRegistrationInPrevSemAc'] = 0;

        if (empty($acadamic_year) && empty($semester)) {
            return $acSem;
        }
        $prevSemester = $this->getPreviousSemester($acadamic_year, $semester);

        $options = array();
        $optionsm = array();
        $optionsf = array();

        $options['conditions']['StudentExamStatus.academic_status_id'] = 4;
        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $options['conditions'][] = 'StudentExamStatus.student_id  IN (SELECT id FROM students
					   where college_id="' . $college_id[1] . '")';
            } else {
                $options['conditions'][] = 'StudentExamStatus.student_id  IN (SELECT id FROM students where department_id=' . $department_id . ')';
            }
        }
        if (isset($prevSemester['academic_year']) && !empty($prevSemester['academic_year'])) {
            $options['conditions']['StudentExamStatus.academic_year'] = $prevSemester['academic_year'];
        }
        if (isset($prevSemester['semester']) && !empty($prevSemester['semester'])) {
            $options['conditions']['StudentExamStatus.semester'] = $prevSemester['semester'];
        }
        $optionsf = $options;
        $optionsm = $options;
        $options['group'] = array('StudentExamStatus.student_id');

        $optionsm['group'] = array('StudentExamStatus.student_id');
        $optionsf['group'] = array('StudentExamStatus.student_id');
        $optionsm['conditions'][] = 'StudentExamStatus.student_id  IN (SELECT id FROM students where gender="male")';
        $optionsf['conditions'][] = 'StudentExamStatus.student_id  IN (SELECT id FROM students where gender="female")';
        // $options['group'] = array('StudentExamStatus.student_id');
        $acSem['prevACSem'] = $prevSemester;
        $acSem['dismissedTotalCount'] = $this->find('count', $options);
        $acSem['dismissedFemaleTotalCount'] = $this->find('count', $optionsf);
        $acSem['dismissedMaleTotalCount'] = $this->find('count', $optionsm);
        if (!empty($department_id)) {
            if (is_array($department_id)) {
                $acSem['totalRegistrationInPrevSemAc'] = ClassRegistry::init('CourseRegistration')->find('count', array(
                    'conditions' => array(
                        'CourseRegistration.academic_year' => $prevSemester['academic_year'],
                        'CourseRegistration.semester' => $prevSemester['semester'],
                        'CourseRegistration.student_id in (select id from students where department_id in (' . join(
                            ',',
                            $department_id
                        ) . '))'
                    ),
                    'group' => 'CourseRegistration.student_id',
                    'recursive' => -1
                ));
            } else {
                $acSem['totalRegistrationInPrevSemAc'] = ClassRegistry::init('CourseRegistration')->find('count', array(
                    'conditions' => array(
                        'CourseRegistration.academic_year' => $prevSemester['academic_year'],
                        'CourseRegistration.semester' => $prevSemester['semester'],

                        'CourseRegistration.student_id in (select id from students where department_id=' . $department_id . ')'
                    ),
                    'group' => 'CourseRegistration.student_id',
                    'recursive' => -1
                ));
            }
        } else {
            $acSem['totalRegistrationInPrevSemAc'] = ClassRegistry::init('CourseRegistration')->find('count', array(
                'conditions' => array(
                    'CourseRegistration.academic_year' => $prevSemester['academic_year'],
                    'CourseRegistration.semester' => $prevSemester['semester']
                ),
                'group' => 'CourseRegistration.student_id',
                'recursive' => -1
            ));
        }

        return $acSem;
    }

    function getRank($student_id, $type = "cgpa")
    {

        $student_ids = array();
        $options = array();
        $option1 = array();
        $optionb = array();
        $sectionIds = array();
        $recentRegistration = ClassRegistry::init('CourseRegistration')->getMostRecentRegisteration($student_id);


        $options['order'] = array('StudentExamStatus.' . $type . ' DESC');
        if (!empty($recentRegistration)) {
            if (!empty($recentRegistration['Section']['YearLevel']['id'])) {
                $sectionIds = ClassRegistry::init('Section')->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Section.year_level_id' => $recentRegistration['CourseRegistration']['year_level_id'],
                            'Section.academicyear' => $recentRegistration['Section']['academicyear'],
                            'Section.department_id' => $recentRegistration['Section']['department_id'],
                            'Section.program_type_id' => $recentRegistration['Section']['program_type_id'],
                        ),
                        'fields' => array('Section.id', 'Section.id')
                    )
                );
            } elseif (empty($recentRegistration['Section']['department_id'])) {
                $sectionIds = ClassRegistry::init('Section')->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Section.year_level_id is null and Section.department_id is null and Section.college_id=' . $recentRegistration['Section']['college_id'] . ' and
Section.academicyear=' . $recentRegistration['Section']['academicyear'] . '',
                            'Section.program_id' => $recentRegistration['Section']['program_id'],
                            'Section.program_type_id' => $recentRegistration['Section']['program_type_id']
                        ),
                        'fields' => array('Section.id')
                    )
                );
            }

            $options['conditions']['StudentExamStatus.academic_year'] = $recentRegistration['CourseRegistration']['academic_year'];
            $options['conditions']['StudentExamStatus.semester'] = $recentRegistration['CourseRegistration']['semester'];
            $option1 = $options;
            $optionb = $options;
            $option1['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id=' . $recentRegistration['CourseRegistration']['section_id'] . ')';

            if (!empty($sectionIds)) {
                $optionb['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id in (' . join(
                        ',',
                        $sectionIds
                    ) . '))';
            }


            $options['conditions']['StudentExamStatus.student_id'] = $student_id;

            $selectedStudentStatus = $this->find('first', $options);

            if (!empty($selectedStudentStatus)) {
                $option1['conditions']['StudentExamStatus.' . $type . ' >'] = $selectedStudentStatus['StudentExamStatus'][$type];
                $optionb['conditions']['StudentExamStatus.' . $type . ' >'] = $selectedStudentStatus['StudentExamStatus'][$type];

                $ownSectionStatusAbove = $this->find('count', $option1);
                $ownBatchStatusAbove = $this->find('count', $optionb);
                //debug($ownSectionStatusAbove);
                //debug($ownBatchStatusAbove);
                $urRank['Section']['rank'] = $this->rankName($ownSectionStatusAbove + 1);
                $urRank['Batch']['rank'] = $this->rankName($ownBatchStatusAbove + 1);
                $urRank['ACSem']['academic_year'] = $recentRegistration['CourseRegistration']['academic_year'];
                $urRank['ACSem']['semester'] = $recentRegistration['CourseRegistration']['semester'];
                $urRank['cgpa'] = $selectedStudentStatus['StudentExamStatus']['cgpa'];
                $urRank['sgpa'] = $selectedStudentStatus['StudentExamStatus']['sgpa'];

                return $urRank;
            } else {
                // check if s/he has previous semester status
                $options = array();
                $option1 = array();
                $optionb = array();
                $sectionIds = array();
                $options['order'] = array('StudentExamStatus.' . $type . ' DESC');
                $pevSemester = $this->getPreviousSemester(
                    $recentRegistration['CourseRegistration']['academic_year'],
                    $recentRegistration['CourseRegistration']['semester']
                );

                $recentRegistration = array();
                $recentRegistration = ClassRegistry::init('CourseRegistration')->getRegisteration(
                    $student_id,
                    $pevSemester['academic_year'],
                    $pevSemester['semester']
                );

                if (!empty($recentRegistration['CourseRegistration']['year_level_id'])) {
                    $sectionIds = ClassRegistry::init('Section')->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Section.year_level_id' => $recentRegistration['CourseRegistration']['year_level_id'],
                                'Section.academicyear' => $recentRegistration['Section']['academicyear'],
                                'Section.department_id' => $recentRegistration['Section']['department_id'],
                                'Section.program_id' => $recentRegistration['Section']['program_id'],
                                'Section.program_type_id' => $recentRegistration['Section']['program_type_id'],
                            ),
                            'fields' => array('Section.id', 'Section.id')
                        )
                    );
                } elseif (empty($recentRegistration['Section']['department_id'])) {
                    $sectionIds = ClassRegistry::init('Section')->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Section.year_level_id is null and Section.department_id is null and Section.college_id=' . $recentRegistration['Section']['college_id'] . ' and
		Section.academicyear=' . $recentRegistration['Section']['academicyear'] . '',
                                'Section.program_id' => $recentRegistration['Section']['program_id'],
                                'Section.program_type_id' => $recentRegistration['Section']['program_type_id']
                            ),
                            'fields' => array('Section.id', 'Section.id')
                        )
                    );
                }

                $options['conditions']['StudentExamStatus.academic_year'] = $pevSemester['academic_year'];
                $options['conditions']['StudentExamStatus.semester'] = $pevSemester['semester'];
                $option1 = $options;

                $optionb = $options;

                $option1['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id=' . $recentRegistration['CourseRegistration']['section_id'] . ')';
                if (!empty($sectionIds)) {
                    $optionb['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id in (' . join(
                            ',',
                            $sectionIds
                        ) . '))';
                }
                $options['conditions']['StudentExamStatus.student_id'] = $student_id;

                $selectedStudentStatus = $this->find('first', $options);

                if (!empty($selectedStudentStatus)) {
                    $option1['conditions']['StudentExamStatus.' . $type . ' >'] = $selectedStudentStatus['StudentExamStatus'][$type];
                    $optionb['conditions']['StudentExamStatus.' . $type . ' >'] = $selectedStudentStatus['StudentExamStatus'][$type];

                    $ownSectionStatusAbove = $this->find('count', $option1);
                    $ownBatchStatusAbove = $this->find('count', $optionb);
                    $urRank['Section']['rank'] = $this->rankName($ownSectionStatusAbove + 1);
                    $urRank['ACSem']['academic_year'] = $pevSemester['academic_year'];
                    $urRank['ACSem']['semester'] = $pevSemester['semester'];
                    $urRank['Batch']['rank'] = $this->rankName($ownBatchStatusAbove + 1);
                    $urRank['cgpa'] = $selectedStudentStatus['StudentExamStatus']['cgpa'];
                    $urRank['sgpa'] = $selectedStudentStatus['StudentExamStatus']['sgpa'];
                    return $urRank;
                } else {
                    //rank them based on their prep results
                    return false;
                }
            }
        } else {
            //rank them based on their prep results
            return false;
        }
    }


    function checkFxPresenseInStatus($student_id)
    {

        $recentRegistration = ClassRegistry::init('CourseRegistration')->getMostRecentRegisteration($student_id);
        if (empty($recentRegistration)) {
            //first time allow registration
            return 1;
        } else {
            $found = false;
            $gradeLists = $this->Student->CourseRegistration->ExamGrade->getStudentCoursesAndFinalGrade(
                $student_id,
                $recentRegistration['CourseRegistration']['academic_year'],
                $recentRegistration['CourseRegistration']['semester']
            );

            foreach ($gradeLists as $k => $v) {
                if (isset($v['grade']) && strcmp($v['grade'], 'Fx') == 0) {
                    return 0;
                }
            }
        }
        return 1;
    }

    public function getStudentByResult(
        $acadamic_year,
        $semester,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $from = 0,
        $to = 4,
        $academic_status_id = 4,
        $type = 'gpa',
        $freshman = 0,
        $exclude_graduated = ''
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $regions = $this->Student->Region->find('list');
        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        $query = '';
        $secQueryIn = ' id is not null ';
        $college_id = array();
        // $query .= ' and stexam.academic_status_id !=4';

        if (isset($from) && !empty($from) && !empty($to) && $type == 'sgpa') {
            $query .= ' and stexam.sgpa >= ' . $from . ' and stexam.sgpa <= ' . $to . ' ';
        }

        if (isset($from) && !empty($from) && isset($to) && !empty($to) && $type == 'cgpa') {
            $query .= ' and stexam.cgpa >= ' . $from . ' and stexam.cgpa <= ' . $to . ' ';
        }

        if (isset($academic_status_id) && !empty($academic_status_id)) {
            $query .= ' and stexam.academic_status_id = ' . $academic_status_id . '';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        if (isset($region_id) && !empty($region_id)) {
            $query .= ' and s.region_id = ' . $region_id . '';
        }

        if (isset($sex) && !empty($sex) && $sex != "all") {
            $query .= ' and s.gender = "' . $sex . '"';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and s.program_id=' . $program_ids[1] . '';
                $secQueryIn .= ' and program_id=' . $program_ids[1] . '';
            } else {
                $query .= ' and s.program_id=' . $program_id . '';
                $secQueryIn .= ' and program_id=' . $program_id . '';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_ids[1] . '';
            } else {
                $query .= ' and s.program_type_id=' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_id . '';
            }
        }

        // list out the department

        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_ids[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            }
        } else {
            $departments = $this->Student->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel')
            ));
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $secQueryIn .= ' and academicyear = "' . $acadamic_year . '"';
            $query .= ' and stexam.academic_year = "' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and stexam.semester="' . $semester . '"';
        }

        if ($type == 'sgpa') {
            $orderBy = 'stexam.sgpa DESC';
        } else {
            $orderBy = 'stexam.cgpa DESC';
        }

        $activeLists = array();

        $secQueryYD = '';
        $count = 0;

        if ($freshman == 1) {
            $departments = array();
            $colleges = array();
        }

        if ($freshman == 0) {
            if (!empty($departments)) {
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
                            $secQueryYD .= ' and year_level_id = "' . $yvalue['id'] . '"';
                            $secQueryYD .= ' and department_id = "' . $value['Department']['id'] . '"';

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                // speeds up the report by skipping redendunt students in the section
                                'group' => array(
                                    'StudentsSection.student_id',
                                    'StudentsSection.section_id'
                                )
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);

                            $student_ids = implode(", ", $x);

                            $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
							FROM students AS s, student_exam_statuses AS stexam
							WHERE stexam.student_id = s.id $query and stexam.student_id in ($student_ids) order by $orderBy ";

                            /* $topStudentsQuery = "SELECT DISTINCT stes.student_id FROM student_exam_statuses AS stes, students AS s,students_sections AS stsec
                            WHERE stes.student_id = s.id and stes.student_id=stsec.student_id and stsec.student_id = s.id  $query ORDER BY stes.cgpa DESC"; */

                            $disResult = $this->query($activeListSQL);

                            if (!empty($disResult)) {
                                foreach ($disResult as $dr) {
                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                        'contain' => array(
                                            'YearLevel' => array('fields' => array('id', 'name'))
                                        ),
                                        'recursive' => -1
                                    ));

                                    //debug($secName);
                                    //$student['College'] = $value['College']['name'];
                                    $student['Department'] = $value['Department']['name'];
                                    $student['AcademicYear'] = $dr['stexam']['academic_year'];
                                    $student['Semester'] = $dr['stexam']['semester'];
                                    $student['Section'] = $secName['Section']['name'];
                                    $student['YearLevel'] = $secName['YearLevel']['name'];

                                    $mg = array_merge($dr['s'], $dr['stexam'], $student);

                                    $activeLists[$value['College']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']]][$count] = $mg;

                                    //$activeLists[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']]. '~' . $secName['Section']['name'] . '~' . $secName['YearLevel']['name']. '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] . '~' . $secstulist[$dr['s']['id']]][$count] = $mg;
                                    $count++;
                                }
                            }

                            $activeListSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering
            $college_id = array();
            $colleges = array();

            if (isset($department_id) /* && !empty($department_id) */) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.id' => $college_ids[1],
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                }
            }

            if (isset($college_id) && !empty($college_id)) {
                //debug($college_id);
                $secQueryYD = '';

                $colleges = $this->Student->College->find('all', array(
                    'conditions' => array(
                        'College.id' => $college_id,
                        'College.active' => 1
                    ),
                    'recursive' => -1
                ));

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $secQueryYD .= ' and college_id ="' . $cv['College']['id'] . '" and department_id is null and id is not null  and (year_level_id is null OR year_level_id = 0 OR year_level_id ="")';

                        $sectionLists = ClassRegistry::init('Section')->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $cv['College']['id'],
                                'Section.academicyear' => $acadamic_year,
                                'Section.department_id is null',
                                //'Section.year_level_id is null'
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                        ));

                        if (isset($sectionLists) && !empty($sectionLists)) {
                            $secstulist = ClassRegistry::init('StudentsSection')->find('list', array(
                                'conditions' => array(
                                    'StudentsSection.section_id' => $sectionLists
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
                            ));
                        }

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $activeListSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year, s.academicyear, s.graduated
						FROM students AS s, student_exam_statuses AS stexam
						WHERE stexam.student_id = s.id $query and stexam.student_id in ($student_ids) order by $orderBy ";

                        $disResult = $this->query($activeListSQL);

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $count++;

                                $secName = ClassRegistry::init('Section')->find('first', array(
                                    'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                    'recursive' => -1
                                ));

                                //$student['College'] = $value['College']['name'];
                                $student['Department'] = 'Pre/Freshman';
                                $student['AcademicYear'] = $dr['stexam']['academic_year'];
                                $student['Semester'] = $dr['stexam']['semester'];
                                $student['Section'] = $secName['Section']['name'];
                                $student['YearLevel'] = 'Pre/1st';

                                $mg = array_merge($dr['s'], $dr['stexam'], $student);

                                $activeLists[$cv['College']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']]][$count] = $mg;
                                //$activeLists[$cv['College']['name'] . '~' . ' Pre/Freshman' . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . ' Pre/1st'. '~' . $dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] . '~' . $secstulist[$dr['s']['id']]][$count] = $mg;
                            }
                        }

                        $activeListSQL = '';
                    }
                }
            }
        }

        return $activeLists;
    }


    function getAcademicYearRange($from, $to)
    {

        $list = array();
        $next_ay_and_s['academic_year'] = $from;
        $list[$from] = $from;

        if ($from == $to) {
            return $list;
        }

        do {
            $next_ay_and_s = $this->getNextSemster($next_ay_and_s['academic_year'], null);
            $list[$next_ay_and_s['academic_year']] = $next_ay_and_s['academic_year'];
        } while (!(strcasecmp($to, $next_ay_and_s['academic_year']) == 0));
        return $list;
    }

    function has3FAndTakenRequiredCredit($student_id, $academic_year, $semester)
    {

        $courseRegistrations = $this->Student->CourseRegistration->find('all', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.academic_year' => $academic_year,
                'CourseRegistration.semester' => $semester
            ),
            'contain' => array('PublishedCourse' => array('Course'))
        ));

        $Fcount = 0;
        $totalCredit = 0;
        foreach ($courseRegistrations as $k => $v) {
            $grade_detail = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade(
                $v['CourseRegistration']['id'],
                1
            );

            if (!empty($grade_detail) && ($grade_detail['grade'] == "F" || $grade_detail['grade'] == "Fx")) {
                $Fcount++;
            }
            $totalCredit += $v['PublishedCourse']['Course']['credit'];
        }

        if ($totalCredit >= 12 && $Fcount >= 3) {
            return true;
        }
        return false;
    }


    function getACSemRank($student_id, $academic_year, $semester, $type = "cgpa")
    {

        $recentRegistration = ClassRegistry::init('CourseRegistration')->find('first', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.academic_year' => $academic_year,
                'CourseRegistration.semester' => $semester
            ),
            'contain' => array('PublishedCourse'),
        ));

        $student_detail = $this->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'contain' => array('AcceptedStudent'))
        );

        if (!empty($recentRegistration) && !empty($student_detail)) {
            return $this->getRankGivenRegistration($recentRegistration, $student_detail, $type);
        }
        return array();
    }

    function getRankGivenRegistration($theMostRecentRegistration, $student_detail, $type = "cgpa")
    {

        $urRank = array();
        $options = array();
        $option1 = array();
        $optionb = array();
        $optionOwnCollege = array();
        $collegeSectionIds = array();
        $sectionIds = array();
        $options['order'] = array('StudentExamStatus.' . $type . ' DESC');
        $recentRegistration = $theMostRecentRegistration;
        $options['conditions']['StudentExamStatus.academic_year'] = $recentRegistration['CourseRegistration']['academic_year'];
        $options['conditions']['StudentExamStatus.semester'] = $recentRegistration['CourseRegistration']['semester'];
        $options['conditions']['StudentExamStatus.student_id'] = $student_detail['Student']['id'];

        $selectedStudentStatus = $this->find('first', $options);
        /*
       if(empty($selectedStudentStatus))
           {
             $count=0;
         while(empty($selectedStudentStatus))
             {
        // get previous status
        if(!empty($recentRegistration['CourseRegistration'])) {
        $pevSemester=$this->getPreviousSemester($recentRegistration['CourseRegistration']['academic_year'],$recentRegistration['CourseRegistration']['semester']);

        $recentRegistration=ClassRegistry::init('CourseRegistration')->find('first',array('conditions'=>array('CourseRegistration.student_id'=>$student_detail['Student']['id'],'CourseRegistration.academic_year'=>$pevSemester['academic_year'],'CourseRegistration.semester'=>$pevSemester['semester']),'recursive'=>-1));
        if(!empty($recentRegistration)) {
        $options['conditions']['StudentExamStatus.academic_year']=$recentRegistration['CourseRegistration']['academic_year'];
        $options['conditions']['StudentExamStatus.semester']=$recentRegistration['CourseRegistration']['semester'];

        $selectedStudentStatus = $this->find('first', $options);
        }
        }
        $count++;
        if($count>5) {
        break;
        }
        debug($count);
             }
      }
      */
        if (!empty($selectedStudentStatus)) {
            $departmentIds = $this->Student->Department->find(
                'list',
                array(
                    'conditions' => array('Department.college_id' => $student_detail['Student']['college_id']),
                    'fields' => array('Department.id', 'Department.id')
                )
            );
            debug($recentRegistration);
            if (!empty($recentRegistration['CourseRegistration']['year_level_id'])) {
                $yearLevelName = ClassRegistry::init('YearLevel')->find('first', array(
                    'conditions' => array('YearLevel.id' => $recentRegistration['CourseRegistration']['year_level_id']),
                    'recursive' => -1
                ));
                $yearLevelIds = ClassRegistry::init('YearLevel')->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.name' => $yearLevelName['YearLevel']['name'],
                            'YearLevel.department_id' => $departmentIds
                        ),
                        'fields' => array('YearLevel.id', 'YearLevel.id')
                    )
                );
                $sectionIds = ClassRegistry::init('Section')->find('list', array(
                    'conditions' => array(
                        'Section.year_level_id' => $recentRegistration['CourseRegistration']['year_level_id'],
                        'Section.academicyear' => $recentRegistration['CourseRegistration']['academic_year'],
                        'Section.department_id' => $recentRegistration['PublishedCourse']['department_id'],
                        'Section.program_id' => $recentRegistration['PublishedCourse']['program_id'],
                        'Section.program_type_id' => $recentRegistration['PublishedCourse']['program_type_id']
                    ),
                    'fields' => array('Section.id', 'Section.id')
                ));
                debug($sectionIds);
                debug($student_detail['Student']['program_type_id']);
                $collegeSectionIds = ClassRegistry::init('Section')->find('list', array(
                    'conditions' => array(
                        'Section.year_level_id' => $yearLevelIds,
                        'Section.academicyear' => $recentRegistration['CourseRegistration']['academic_year'],
                        'Section.program_id' => $recentRegistration['PublishedCourse']['program_id'],
                        'Section.program_type_id' => $recentRegistration['PublishedCourse']['program_type_id']
                    ),
                    'fields' => array('Section.id', 'Section.id')
                ));
            } elseif (empty($recentRegistration['CourseRegistration']['year_level_id'])) {
                $sectionIds = ClassRegistry::init('Section')->find('list', array(
                    'conditions' => array(
                        'Section.year_level_id is null and Section.department_id is null',
                        'Section.college_id' => $recentRegistration['PublishedCourse']['college_id'],
                        'Section.academicyear' => $recentRegistration['CourseRegistration']['academic_year'],
                        'Section.program_id' => $recentRegistration['PublishedCourse']['program_id'],
                        'Section.program_type_id' => $recentRegistration['PublishedCourse']['program_type_id']
                    ),
                    'fields' => array('Section.id')
                ));
                debug($sectionIds);
                $collegeSectionIds = ClassRegistry::init('Section')->find('list', array(
                    'conditions' => array(
                        'Section.year_level_id is null',
                        'Section.college_id' => $recentRegistration['PublishedCourse']['college_id'],
                        'Section.academicyear' => $recentRegistration['CourseRegistration']['academic_year'],
                        'Section.program_id' => $recentRegistration['PublishedCourse']['program_id'],
                        'Section.program_type_id' => $recentRegistration['PublishedCourse']['program_type_id']
                    ),
                    'fields' => array('Section.id', 'Section.id')
                ));
            }
            $options['conditions']['StudentExamStatus.academic_year'] = $recentRegistration['CourseRegistration']['academic_year'];
            $options['conditions']['StudentExamStatus.semester'] = $recentRegistration['CourseRegistration']['semester'];
            $option1 = $options;
            $optionb = $options;
            $optionOwnCollege = $options;

            $option1['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id=' . $recentRegistration['CourseRegistration']['section_id'] . ')';                      // debug($recentRegistration['CourseRegistration']);
            //debug($sectionIds);
            //debug($collegeSectionIds);
            //debug($options);
            if (!empty($sectionIds)) {
                $optionb['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id in (' . join(
                        ',',
                        $sectionIds
                    ) . '))';
            }
            if (!empty($collegeSectionIds)) {
                $optionOwnCollege['conditions'][] = 'StudentExamStatus.student_id IN (SELECT student_id FROM students_sections where section_id in (' . join(
                        ',',
                        $collegeSectionIds
                    ) . '))';
            }

            unset($optionb['conditions']['StudentExamStatus.student_id']);
            unset($option1['conditions']['StudentExamStatus.student_id']);
            unset($optionOwnCollege['conditions']['StudentExamStatus.student_id']);

            $option1['conditions']['StudentExamStatus.' . $type . ' >'] = $selectedStudentStatus['StudentExamStatus'][$type];
            $optionb['conditions']['StudentExamStatus.' . $type . ' >'] = $selectedStudentStatus['StudentExamStatus'][$type];
            $optionOwnCollege['conditions']['StudentExamStatus.' . $type . ' >'] =
                $selectedStudentStatus['StudentExamStatus'][$type];
            if (!empty($option1) && !empty($optionb) && !empty($optionOwnCollege)) {
                debug($option1);
                debug($optionb);
                debug($optionOwnCollege);
                $ownSectionStatusAbove = $this->find('count', $option1) + 1;
                $ownBatchStatusAbove = $this->find('count', $optionb) + 1;
                $ownCollegeStatusAbove = $this->find('count', $optionOwnCollege) + 1;
                $urRank['Rank']['student_id'] = $recentRegistration['CourseRegistration']['student_id'];

                $urRank['Rank']['section_rank'] = $this->rankName($ownSectionStatusAbove);

                $urRank['Rank']['batch_rank'] = $this->rankName($ownBatchStatusAbove);
                $urRank['Rank']['college_rank'] = $this->rankName($ownCollegeStatusAbove);
                $urRank['Rank']['academicyear'] = $recentRegistration['CourseRegistration']['academic_year'];
                $urRank['Rank']['semester'] = $recentRegistration['CourseRegistration']['semester'];
                $urRank['Rank']['cgpa'] = $selectedStudentStatus['StudentExamStatus']['cgpa'];
                $urRank['Rank']['sgpa'] = $selectedStudentStatus['StudentExamStatus']['sgpa'];
                $urRank['Rank']['category'] = $type;
                return $urRank;
            }
        }
        return array();
    }



    public function displayStudentRank($studentId, $academicYear)
    {
        $StudentRanks = TableRegistry::getTableLocator()->get('StudentRanks');

        // First attempt to get for a specific academic year
        $rankQuery = $StudentRanks->find()
            ->where([
                'StudentRanks.student_id' => $studentId,
                'StudentRanks.academicyear' => $academicYear
            ])
            ->order(['StudentRanks.academicyear' => 'DESC']);

        $ranks = $rankQuery->toArray();

        // If not found for academicYear, try all years
        if (empty($ranks)) {
            $ranks = $StudentRanks->find()
                ->where(['StudentRanks.student_id' => $studentId])
                ->order(['StudentRanks.academicyear' => 'DESC'])
                ->toArray();
        }

        $rankFormatted = [];

        foreach ($ranks as $rank) {
            $key = $rank->academicyear . '-' . $rank->semester;
            $rankFormatted[$key][$rank->category] = $rank->toArray();
        }

        return $rankFormatted;
    }


    function rankName($i)
    {

        $name = '';
        switch ($i) {
            case 1:
                $name = $i . 'st';
                break;
            case 2:
                $name = $i . 'nd';
                break;
            case 3:
                $name = $i . 'rd';
                break;
            default:
                $name = $i . 'th';
        }
        return $name;
    }

    public function getAttrationRate(
        $acadamic_year,
        $semester,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $year_level_id = null,
        $region_id = null,
        $sex = null
    ) {

        $query = "";
        $student_ids = array();
        $options = array();
        $colleges = array();

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $colleges = ClassRegistry::init('College')->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id[1]), 'contain' => array('Department'))
                );
            } else {
                $colleges = ClassRegistry::init('College')->find(
                    'all',
                    array(
                        'conditions' => array('College.id in (select college_id from departments where department_id=' . $department_id . ''),
                        'contain' => array('Department')
                    )
                );
            }
        } else {
            $colleges = ClassRegistry::init('College')->find('all', array('contain' => array('Department')));
        }


        if (isset($region_id) && !empty($region_id)) {
            $query .= ' and s.region_id=' . $region_id . '';
        }
        debug($program_id);
        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $programs = ClassRegistry::init('Program')->find(
                    'list',
                    array('conditions' => array('Program.id' => $program_ids[1]))
                );
            } else {
                $programs = ClassRegistry::init('Program')->find(
                    'list',
                    array('conditions' => array('Program.id' => $program_id))
                );
            }
        } else {
            $programs = ClassRegistry::init('Program')->find('list');
        }
        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $programTypes = ClassRegistry::init('ProgramType')->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $program_type_ids[1]))
                );
            } else {
                $programTypes = ClassRegistry::init('ProgramType')->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $program_type_id))
                );
            }
        } else {
            $programTypes = ClassRegistry::init('ProgramType')->find('list');
        }
        $registration = array();
        $studentExamStatus = array();
        $attrationSummery = array();
        $region = '';
        $sex = '';
        foreach ($colleges as $k => $v) {
            if (isset($year_level_id) && !empty($year_level_id) && $year_level_id != "all") {
                $yearLevels = ClassRegistry::init('YearLevel')->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.department_id in (select id from departments where college_id="' . $v['College']['id'] . '")',
                            'YearLevel.name' => $year_level_id
                        ),
                        'fields' => array('id', 'name')
                    )
                );
            } else {
                $yearLevels = ClassRegistry::init('YearLevel')->find(
                    'list',
                    array(
                        'conditions' => array('YearLevel.department_id in (select id from departments where college_id="' . $v['College']['id'] . '")'),
                        'fields' => array('id', 'name')
                    )
                );
            }

            if (isset($region_id) && !empty($region_id)) {
                $region .= ' and region_id=' . $region_id . '';
            }
            if (isset($sex) && !empty($sex)) {
                if ($sex != "all") {
                    $sex .= ' and gender=' . $sex . '';
                }
            }

            foreach ($programTypes as $pk => $pv) {
                foreach ($programs as $ppk => $ppv) {
                    foreach ($v['Department'] as $deptk => $deptv) {
                        $yearLevels = ClassRegistry::init('YearLevel')->find(
                            'list',
                            array(
                                'conditions' => array('YearLevel.department_id' => $deptv['id']),
                                'fields' => array('id', 'name')
                            )
                        );

                        foreach ($yearLevels as $yk => $yn) {
                            if (
                                !empty($acadamic_year) && !empty($semester) && !empty($yn)
                                && !empty($deptv['name']) && !empty($pv) && !empty($ppv) && !empty($v['College']['name'])
                                && !empty($deptv['id'])
                            ) {
                                $attrationSummery['yearLevel'][$yn] = $yn;
                                $totalRegistered = ClassRegistry::init('CourseRegistration')->find('list', array(
                                    'conditions' => array(
                                        'CourseRegistration.semester' => $semester,
                                        'CourseRegistration.academic_year' => $acadamic_year,
                                        'CourseRegistration.year_level_id' => $yk,
                                        'CourseRegistration.student_id in (select id from students where department_id=' . $deptv['id'] . '
and program_id=' . $ppk . ' and program_type_id=' . $pk . ' ' . $sex . ' ' . $region . ')'
                                    ),
                                    'group' => array('CourseRegistration.student_id'),
                                    'fields' => array('student_id', 'student_id')
                                ));

                                $attrationSummery[$ppv . '~' . $pv][$v['College']['name']][$deptv['name']][$yn]['total'] = count(
                                    $totalRegistered
                                );
                                $attrationSummery[$ppv . '~' . $pv][$v['College']['name']][$deptv['name']][$yn]['female'] = ClassRegistry::init(
                                    'StudentExamStatus'
                                )->find('count', array(
                                    'conditions' => array(
                                        'StudentExamStatus.semester' => $semester,
                                        'StudentExamStatus.academic_year' => $acadamic_year,
                                        'StudentExamStatus.academic_status_id' => 4,
                                        'StudentExamStatus.student_id' => $totalRegistered,
                                        'StudentExamStatus.student_id in (select id from students where gender="female" and department_id=' . $deptv['id'] . ' and program_id=' . $ppk . ' and program_type_id=' . $pk . ' ' . $region . ' )'
                                    )
                                ));
                                $attrationSummery[$ppv . '~' . $pv][$v['College']['name']][$deptv['name']][$yn]['male'] = ClassRegistry::init(
                                    'StudentExamStatus'
                                )->find('count', array(
                                    'conditions' => array(
                                        'StudentExamStatus.semester' => $semester,
                                        'StudentExamStatus.academic_year' => $acadamic_year,
                                        'StudentExamStatus.academic_status_id' => 4,
                                        'StudentExamStatus.student_id' => $totalRegistered,
                                        'StudentExamStatus.student_id in (select id from students where gender="male" and department_id=' . $deptv['id'] . ' and program_id=' . $ppk . ' and program_type_id=' . $pk . ' ' . $region . ')'
                                    )
                                ));
                            }
                        }
                    }
                }
            }
        }
        return $attrationSummery;
    }


    public function getTopScorer(
        $acadamic_year,
        $semester,
        $program_id = 0,
        $program_type_id = 0,
        $department_id = null,
        $top = 10,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $by = "cgpa",
        $freshman = 0,
        $exclude_graduated = 0
    ) {

        $query = "";
        $student_ids = array();
        $options = array();
        $optionsIteration = array();
        $regions = $this->Student->Region->find('list');
        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');
        debug($top);
        $query = '';

        if (empty($top)) {
            $top = 10;
        }

        if (isset($exclude_graduated) && $exclude_graduated) {
            $query .= ' and s.graduated = 0';
        }

        if (!empty($region_id) && $region_id > 0) {
            $query .= ' and s.region_id =' . $region_id . '';
        }

        if (!empty($sex) && $sex != "all") {
            $query .= ' and s.gender="' . $sex . '"';
        }

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $query .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $query .= ' and s.program_id = ' . $program_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $query .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $query .= ' and s.program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_type_id = 0';
            }
        }

        if (!empty($acadamic_year)) {
            //$query .= ' and sec.academicyear="' . $acadamic_year . '"';
        }

        // list out the department
        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_id[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_ids[$college_id[1]] = $college_id[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
            $college_ids = $this->Student->College->find(
                'list',
                array('conditions' => array('College.active' => 1), 'fields' => array('College.id', 'College.id'))
            );
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $query .= ' and stexam.academic_year="' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and stexam.semester="' . $semester . '"';
        }

        $topLists = array();
        $internalQuery = '';
        $count = 0;

        $acadamic_year_quoted = "'" . $acadamic_year . "'";
        $semester_quoted = "'" . $semester . "'";

        if ($freshman == 0) {
            if (!empty($departments)) {
                foreach ($departments as $key => $value) {
                    //$internalQuery .= ' and (sec.year_level_id is null or sec.year_level_id=0 )';
                    //$internalQuery .= ' and sec.college_id="' . $value . '"';

                    $yearlevelIds = array();
                    $optionsSec = array();

                    if (isset($acadamic_year) && !empty($acadamic_year)) {
                        $optionsSec['Section.academicyear'] = $acadamic_year;
                    }

                    if (isset($program_id) && !empty($program_id)) {
                        $optionsSec['Section.program_id'] = $program_id;
                    }

                    if (isset($program_type_id) && !empty($program_type_id)) {
                        $optionsSec['Section.program_type_id'] = $program_type_id;
                    }

                    if (!empty($year_level_id)) {
                        foreach ($value['YearLevel'] as $yykey => $yyvalue) {
                            if (!empty($year_level_id) && strcasecmp($year_level_id, $yyvalue['name']) == 0) {
                                $yearLevel[$yykey] = $yyvalue;
                            }
                        }
                    } elseif (empty($year_level_id)) {
                        $yearLevel = $value['YearLevel'];
                    }

                    if (isset($yearLevel) && !empty($yearLevel)) {
                        foreach ($yearLevel as $pk => $pv) {
                            $yearlevelIds[$pv['id']] = $pv['id'];
                        }
                    }

                    $deptID = $optionsSec['Section.department_id'] = $value['Department']['id'];
                    $optionsSec['Section.year_level_id'] = $yearlevelIds;

                    $sectionIds = ClassRegistry::init('Section')->find(
                        'list',
                        array('conditions' => $optionsSec, 'fields' => array('Section.id', 'Section.id'))
                    );

                    if (!empty($sectionIds)) {
                        $studentIds = ClassRegistry::init('StudentsSection')->find(
                            'list',
                            array(
                                'conditions' => array('StudentsSection.section_id' => $sectionIds),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.student_id')
                            )
                        );

                        if (!empty($studentIds)) {
                            $topListsSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.semester, stexam.academic_year
							FROM students AS s, student_exam_statuses AS stexam
							WHERE stexam.student_id = s.id AND s.department_id = $deptID AND stexam.student_id IN (" . implode(
                                    ',',
                                    $studentIds
                                ) . ") $query GROUP BY stexam.student_id, stexam.academic_year, stexam.semester ORDER BY stexam.$by DESC  LIMIT $top";

                            $disResult = $this->query($topListsSQL);

                            if (!empty($disResult)) {
                                foreach ($disResult as &$dr) {
                                    $topLists[$dr['s']['id']] = $dr['s']['id'];
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if (!empty($college_ids)) {
                foreach ($college_ids as $key => $value) {
                    //$internalQuery .= ' and (sec.year_level_id is null or sec.year_level_id=0 )';
                    //$internalQuery .= ' and sec.college_id="' . $value . '"';
                    $optionsSec = array();

                    if (isset($acadamic_year) && !empty($acadamic_year)) {
                        $optionsSec['Section.academicyear'] = $acadamic_year;
                    }

                    if (isset($program_id) && !empty($program_id)) {
                        $optionsSec['Section.program_id'] = $program_id;
                    }

                    if (isset($program_type_id) && !empty($program_type_id)) {
                        $optionsSec['Section.program_type_id'] = $program_type_id;
                    }

                    $optionsSec['Section.college_id'] = $key;

                    $optionsSec['OR'] = array(
                        'Section.department_id IS NULL',
                        'Section.department_id ' => array('', 0)
                    );


                    $sectionIds = ClassRegistry::init('Section')->find(
                        'list',
                        array('conditions' => $optionsSec, 'fields' => array('Section.id', 'Section.id'))
                    );

                    if (!empty($sectionIds)) {
                        $studentIds = ClassRegistry::init('StudentsSection')->find(
                            'list',
                            array(
                                'conditions' => array('StudentsSection.section_id' => $sectionIds),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.student_id')
                            )
                        );
                        $disResult = array();

                        if (!empty($studentIds)) {
                            $topListsSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.semester, stexam.academic_year
							FROM students AS s, student_exam_statuses AS stexam
							WHERE stexam.student_id = s.id AND s.college_id = $key AND s.department_id IS NULL AND stexam.student_id IN (" . implode(
                                    ',',
                                    $studentIds
                                ) . ") GROUP BY stexam.student_id, stexam.academic_year, stexam.semester ORDER BY stexam.$by DESC LIMIT $top";

                            $disResult = $this->query($topListsSQL);

                            if (!empty($disResult)) {
                                foreach ($disResult as &$dr) {
                                    $topLists[$dr['s']['id']] = $dr['s']['id'];
                                }
                            }
                        }
                    }

                    $internalQuery = '';
                }
            }
        }

        if (isset($top) && !empty($top)) {
            $options['limit'] = $top;
        }

        if (isset($semester) && !empty($semester)) {
            $options['conditions']['StudentExamStatus.semester'] = $semester;
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $options['conditions']['StudentExamStatus.academic_year'] = $acadamic_year;
        }

        $options['contain'] = array(
            'Student' => array(
                'Department' => array('id', 'name'),
                'College' => array('id', 'name', 'shortname', 'stream'),
                'Program' => array('id', 'name'),
                'ProgramType' => array('id', 'name'),
                'Curriculum' => array(
                    'id',
                    'name',
                    'type_credit',
                    'english_degree_nomenclature',
                    'minimum_credit_points',
                    'specialization_english_degree_nomenclature'
                )
            ),
            'AcademicStatus' => array('id', 'name')
        );

        $options['conditions']['StudentExamStatus.student_id'] = $topLists;
        $options['order'] = array("StudentExamStatus.$by DESC");
        $options['group'] = array('StudentExamStatus.student_id');

        $students = $this->find('all', $options);
        $formattedStudentList = array();

        if (!empty($students)) {
            foreach ($students as $key => &$student) {
                if (!empty($acadamic_year) && !empty($semester)) {
                    $student['Student']['yearLevel'] = ClassRegistry::init('Section')->getStudentYearLevel(
                        $student['Student']['id']
                    )['year'];
                }
                if (!isset($student['Student']['Department']['name'])) {
                    $formattedStudentList[$student['Student']['Program']['name']][$student['Student']['ProgramType']['name']][] = $student;
                }
                if (isset($student['Student']['Department']['name'])) {
                    $formattedStudentList[$student['Student']['Program']['name']][$student['Student']['ProgramType']['name']][] = $student;
                }
            }
        }
        return $formattedStudentList;
    }

    function updateAcdamicStatusByPublishedCourseOfStudent($published_course_id = null, $student_id = null)
    {

        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());

        $fully_saved = true;
        $last_exam_status = array();

        //Getting all students who are registered and add for the published course so that we can generate a status for the student if and only if all registered and course grades are submitted and approved by registrar.
        $registered_students = $this->Student->CourseRegistration->PublishedCourse->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $published_course_id
            ),
            'contain' => array(
                'Course' => array(
                    'CourseCategory'
                ),
                'CourseRegistration' => array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $student_id,
                    ),
                    //'order' => array('CourseRegistration.created' => 'ASC'), // will be Affected by Grade Entry, Neway
                    'order' => array(
                        'CourseRegistration.academic_year' => 'ASC',
                        'CourseRegistration.semester' => 'ASC',
                        'CourseRegistration.id' => 'ASC'
                    ),
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'full_name',
                            'Student.program_id',
                            'Student.admissionyear',
                            'Student.program_type_id',
                            'Student.academicyear',
                            'Student.graduated'
                        ),
                        'GraduateList'
                    )
                )
            )
        ));

        //The following add student list will retrieve all students even if their add is not approved
        //The assumption is that, the student will be filtered later on (whether s/he get grades for all add and registered courses)
        $added_students = $this->Student->CourseAdd->PublishedCourse->find('first', array(
            'conditions' => array(
                'PublishedCourse.id' => $published_course_id
            ),
            'contain' => array(
                'Course' => array(
                    'CourseCategory'
                ),
                'CourseAdd' => array(
                    'conditions' => array(
                        'CourseAdd.student_id' => $student_id,
                    ),
                    //'order' => array('CourseAdd.created' => 'ASC'),
                    'order' => array(
                        'CourseAdd.academic_year' => 'ASC',
                        'CourseAdd.semester' => 'ASC',
                        'CourseAdd.id' => 'ASC'
                    ),
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'full_name',
                            'Student.program_id',
                            'Student.admissionyear',
                            'Student.program_type_id',
                            'Student.academicyear',
                            'Student.graduated'
                        ),
                        'GraduateList'
                    )
                )
            )
        ));
        //Merging all students (registered and add) To make sure that the student is not included twice to avoid double status generation,
        //duplicate checking is done by student id (to protect from extreme case scenarios:
        //a student is registered and again add a course with some kind of mistake)

        $registered_added_students = array();

        if (isset($registered_students['CourseRegistration'])) {
            foreach ($registered_students['CourseRegistration'] as $key2 => $value2) {
                $found = false;
                if (!empty($registered_added_students)) {
                    foreach ($registered_added_students as $ras_key => $course_registration) {
                        if ($course_registration['Student']['id'] == $value2['Student']['id']) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found == false) {
                    $registered_added_students[] = $value2;
                }
            }
        }

        if (isset($added_students['CourseAdd'])) {
            foreach ($added_students['CourseAdd'] as $key2 => $value2) {
                $found = false;
                if (!empty($registered_added_students)) {
                    foreach ($registered_added_students as $ras_key => $course_add) {
                        if ($course_add['Student']['id'] == $value2['Student']['id']) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found == false) {
                    $registered_added_students[] = $value2;
                }
            }
        }

        if (isset($registered_students['PublishedCourse'])) {
            $acadamic_year = $registered_students['PublishedCourse']['academic_year'];
            $semester = $registered_students['PublishedCourse']['semester'];
        }

        $student_exam_status = array();

        if (!empty($registered_added_students)) {
            foreach ($registered_added_students as $ras_key => $course_registration) {
                //debug($course_registration);

                $program_type_id = $this->Student->ProgramTypeTransfer->getStudentProgramType(
                    $course_registration['Student']['id'],
                    $acadamic_year,
                    $semester
                );
                $program_type_id = $this->Student->ProgramType->getParentProgramType($program_type_id);

                $pattern = $this->Student->ProgramType->StudentStatusPattern->getProgramTypePattern(
                    $course_registration['Student']['program_id'],
                    $program_type_id,
                    $acadamic_year
                );

                //introduced to generate last status for extension students in case of 11 semester where the last semester escaped from status labeling
                //////////////////////////////////

                $lastPattern = $this->Student->ProgramType->StudentStatusPattern->isLastSemesterInCurriculum(
                    $course_registration['Student']['id']
                );
                debug($lastPattern);

                $lastRegisteredSem = $this->Student->CourseRegistration->find('first', array(
                    'conditions' => array(
                        'CourseRegistration.student_id' => $course_registration['Student']['id']
                    ),
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'recursive' => -1
                ));

                debug($lastPattern);
                debug($lastRegisteredSem);

                if ($lastPattern && $lastRegisteredSem['CourseRegistration']['academic_year'] == $acadamic_year && $lastRegisteredSem['CourseRegistration']['semester'] == $semester) {
                    //debug($ay_and_s_list);
                    $pattern = 1;
                }

                //////////////////////////////////

                $ay_and_s_list = $this->getAcadamicYearAndSemesterListToGenerateStatus(
                    $course_registration['Student']['id'],
                    $acadamic_year,
                    $semester
                );

                if (empty($ay_and_s_list)) {
                    //Status is already generated for the given A/Y & semester and you may need to update it.
                    //TODO: This rare case scenario happens when there is multiple publication for the same semester
                } elseif (count($ay_and_s_list) >= $pattern) {
                    //It is on the perfect way. Generate student status for the last returned a/y and semester.
                    /*
                        1. Make sure that all registered and add courses grade is submitted and approved by registrar.
                        2. For each course get grade point and credit hour and calc the SGPA
                    */

                    $credit_hour_sum = 0;
                    $grade_point_sum = 0;
                    $m_credit_hour_sum = 0;
                    $m_grade_point_sum = 0;
                    $deduct_credit_hour_sum = 0;
                    $deduct_grade_point_sum = 0;
                    $m_deduct_credit_hour_sum = 0;
                    $m_deduct_grade_point_sum = 0;
                    $complete = true;
                    $first_acadamic_year = null;
                    $first_semester = null;
                    $processed_course_reg = array();
                    $processed_course_add = array();
                    //debug($ay_and_s_list);

                    $all_ay_s_list = $this->Student->CourseRegistration->ExamGrade->getListOfAyAndSemester(
                        $course_registration['Student']['id'],
                        $ay_and_s_list[0]['academic_year'],
                        $ay_and_s_list[0]['semester']
                    );

                    if (!empty($ay_and_s_list)) {
                        foreach ($ay_and_s_list as $key => $ay_and_s) {
                            //debug($ay_and_s);

                            $ays_index = count($all_ay_s_list);
                            $all_ay_s_list[$ays_index]['academic_year'] = $ay_and_s['academic_year'];
                            $all_ay_s_list[$ays_index]['semester'] = $ay_and_s['semester'];

                            if ($first_acadamic_year == null) {
                                $first_acadamic_year = $ay_and_s['academic_year'];
                                $first_semester = $ay_and_s['semester'];
                            }

                            $course_and_grades = $this->Student->CourseRegistration->ExamGrade->getStudentCoursesAndFinalGrade(
                                $course_registration['Student']['id'],
                                $ay_and_s['academic_year'],
                                $ay_and_s['semester']
                            );

                            if (!empty($course_and_grades)) {
                                foreach ($course_and_grades as $key => $registered_added_course) {
                                    //debug($registered_added_course);

                                    if (!(isset($registered_added_course['grade']) && ((isset($registered_added_course['used_in_gpa']) && $registered_added_course['used_in_gpa'] == false) || isset($registered_added_course['point_value']) || strcasecmp(
                                                $registered_added_course['grade'],
                                                'I'
                                            ) == 0 || strcasecmp(
                                                $registered_added_course['grade'],
                                                'W'
                                            ) == 0 || strcasecmp($registered_added_course['grade'], 'NG') == 0))) {
                                        $complete = false;
                                        break 2;
                                    } else {
                                        //debug($registered_added_course);
                                        debug(
                                            (isset($registered_added_course['grade']) && (isset($registered_added_course['point_value']) || strcasecmp(
                                                        $registered_added_course['grade'],
                                                        'I'
                                                    ) == 0 || strcasecmp(
                                                        $registered_added_course['grade'],
                                                        'W'
                                                    ) == 0 || strcasecmp($registered_added_course['grade'], 'NG') == 0))
                                        );
                                    }

                                    if (strcasecmp($registered_added_course['grade'], 'NG') == 0 || strcasecmp(
                                            $registered_added_course['grade'],
                                            'W'
                                        ) == 0) {
                                        debug($ay_and_s['academic_year']);
                                        debug($ay_and_s['semester']);
                                        $complete = false;
                                        break 2;
                                    }


                                    if (isset($registered_added_course['grade']) && (strcasecmp(
                                                $registered_added_course['grade'],
                                                'I'
                                            ) == 0 || strcasecmp(
                                                $registered_added_course['grade'],
                                                'W'
                                            ) == 0 || strcasecmp($registered_added_course['grade'], 'NG') == 0)) {
                                        $complete = false;
                                        break 2;
                                    }


                                    if ((strcasecmp($registered_added_course['grade'], 'I') != 0 && strcasecmp(
                                                $registered_added_course['grade'],
                                                'W'
                                            ) != 0 && strcasecmp(
                                                $registered_added_course['grade'],
                                                'NG'
                                            ) != 0) && isset($registered_added_course['used_in_gpa']) && $registered_added_course['used_in_gpa']) {
                                        $credit_hour_sum += $registered_added_course['credit'];
                                        $grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);

                                        if ($registered_added_course['major'] == 1) {
                                            $m_credit_hour_sum += $registered_added_course['credit'];
                                            $m_grade_point_sum += ($registered_added_course['credit'] * $registered_added_course['point_value']);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($complete === true && $credit_hour_sum > 0) {
                        // debug($course_and_grades);
                        //DEDUCTION: Credit hour and grade point
                        /*
                            1. Get all academic year and semester the student previously attends
                            2. For each academic year semester, get courses and grade details
                            3. Perform the deduction sum
                        */
                        //debug($all_ay_s_list);

                        $credit_and_point_deduction = $this->Student->CourseAdd->ExamGrade->getTotalCreditAndPointDeduction(
                            $course_registration['Student']['id'],
                            $all_ay_s_list
                        );
                        debug($credit_and_point_deduction);

                        $deduct_credit_hour_sum = $credit_and_point_deduction['deduct_credit_hour_sum'];
                        $deduct_grade_point_sum = $credit_and_point_deduction['deduct_grade_point_sum'];
                        $m_deduct_credit_hour_sum = $credit_and_point_deduction['m_deduct_credit_hour_sum'];
                        $m_deduct_grade_point_sum = $credit_and_point_deduction['m_deduct_grade_point_sum'];

                        $stat_index = count($student_exam_status);

                        $student_exam_status[$stat_index]['student_id'] = $course_registration['Student']['id'];
                        $student_exam_status[$stat_index]['created'] = $AcademicYear->getAcademicYearBegainingDate(
                            $acadamic_year,
                            $semester
                        );


                        $student_exam_status[$stat_index]['academic_year'] = $acadamic_year;
                        $student_exam_status[$stat_index]['semester'] = $semester;
                        $student_exam_status[$stat_index]['grade_point_sum'] = $grade_point_sum;
                        $student_exam_status[$stat_index]['credit_hour_sum'] = $credit_hour_sum;
                        $student_exam_status[$stat_index]['m_grade_point_sum'] = $m_grade_point_sum;
                        $student_exam_status[$stat_index]['m_credit_hour_sum'] = $m_credit_hour_sum;

                        if ($grade_point_sum > 0 && $credit_hour_sum > 0) {
                            $student_exam_status[$stat_index]['sgpa'] = $grade_point_sum / $credit_hour_sum;
                        } else {
                            $student_exam_status[$stat_index]['sgpa'] = 0;
                        }

                        $status_histories = $this->find('all', array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $course_registration['Student']['id'],
                            ),
                            //'order' => array('StudentExamStatus.created' => 'ASC')
                            'order' => array(
                                'StudentExamStatus.academic_year' => 'ASC',
                                'StudentExamStatus.semester' => 'ASC',
                                'StudentExamStatus.created' => 'ASC'
                            )
                        ));

                        $last_exam_status = array();

                        $cumulative_grade_point = $student_exam_status[$stat_index]['grade_point_sum'];
                        $cumulative_credit_hour = $student_exam_status[$stat_index]['credit_hour_sum'];
                        $m_cumulative_grade_point = $student_exam_status[$stat_index]['m_grade_point_sum'];
                        $m_cumulative_credit_hour = $student_exam_status[$stat_index]['m_credit_hour_sum'];

                        if (!empty($status_histories)) {
                            foreach ($status_histories as $key => $status_history) {
                                if (!(strcasecmp(
                                        $status_history['StudentExamStatus']['academic_year'],
                                        $acadamic_year
                                    ) == 0 && strcasecmp(
                                        $status_history['StudentExamStatus']['semester'],
                                        $semester
                                    ) == 0)) {
                                    $cumulative_grade_point += $status_history['StudentExamStatus']['grade_point_sum'];
                                    $cumulative_credit_hour += $status_history['StudentExamStatus']['credit_hour_sum'];
                                    $m_cumulative_grade_point += $status_history['StudentExamStatus']['m_grade_point_sum'];
                                    $m_cumulative_credit_hour += $status_history['StudentExamStatus']['m_credit_hour_sum'];
                                    $last_exam_status = $status_history['StudentExamStatus'];
                                } else {
                                    break;
                                }
                            }
                        }

                        if (($cumulative_grade_point - $deduct_grade_point_sum) > 0 && ($cumulative_credit_hour - $deduct_credit_hour_sum) > 0) {
                            $student_exam_status[$stat_index]['cgpa'] = (($cumulative_grade_point - $deduct_grade_point_sum) / ($cumulative_credit_hour - $deduct_credit_hour_sum));
                        } else {
                            $student_exam_status[$stat_index]['cgpa'] = 0;
                        }

                        if (($m_cumulative_grade_point - $m_deduct_grade_point_sum) > 0 && ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum) > 0) {
                            $student_exam_status[$stat_index]['mcgpa'] = (($m_cumulative_grade_point - $m_deduct_grade_point_sum) / ($m_cumulative_credit_hour - $m_deduct_credit_hour_sum));
                        } else {
                            $student_exam_status[$stat_index]['mcgpa'] = 0;
                        }

                        //Status identification
                        $student_level = $this->studentYearAndSemesterLevelOfStatus(
                            $course_registration['Student']['id'],
                            $acadamic_year,
                            $semester
                        );

                        if ($student_level['year'] == 1) {
                            $student_level['year'] .= 'st';
                        } elseif ($student_level['year'] == 2) {
                            $student_level['year'] .= 'nd';
                        } elseif ($student_level['year'] == 3) {
                            $student_level['year'] .= 'rd';
                        } else {
                            $student_level['year'] .= 'th';
                        }

                        $academic_statuses = ClassRegistry::init('AcademicStatus')->find('all', array(
                            'conditions' => array('AcademicStatus.computable = 1'),
                            'order' => array('AcademicStatus.order' => 'ASC'),
                            'recursive' => -1
                        ));

                        debug($student_level);
                        //Checking the student against each academic status

                        if (!empty($academic_statuses)) {
                            foreach ($academic_statuses as $key => $academic_statuse) {
                                $academic_stands = ClassRegistry::init('AcademicStand')->find('all', array(
                                    'conditions' => array(
                                        'AcademicStand.academic_status_id' => $academic_statuse['AcademicStatus']['id'],
                                        'AcademicStand.program_id' => $course_registration['Student']['program_id']
                                    ),
                                    'order' => array('AcademicStand.academic_year_from' => 'ASC'),
                                    'recursive' => -1
                                ));

                                $as = null;

                                if (!empty($academic_stands)) {
                                    foreach ($academic_stands as $key => $academic_stand) {
                                        $stand_year_levels = unserialize(
                                            $academic_stand['AcademicStand']['year_level_id']
                                        );
                                        $stand_semesters = unserialize($academic_stand['AcademicStand']['semester']);

                                        //Student acadamic stand searching by year and semster level for status
                                        if (in_array($student_level['year'], $stand_year_levels) && in_array(
                                                $student_level['semester'],
                                                $stand_semesters
                                            )) {
                                            //Checking if the acadamic stand is applicable to the student
                                            if ((substr(
                                                        $course_registration['Student']['academicyear'],
                                                        0,
                                                        4
                                                    ) >= $academic_stand['AcademicStand']['academic_year_from']) || ($academic_stand['AcademicStand']['applicable_for_all_current_student'] == 1 && substr(
                                                        $acadamic_year,
                                                        0,
                                                        4
                                                    ) >= $academic_stand['AcademicStand']['academic_year_from'])) {
                                                $as = $academic_stand['AcademicStand'];
                                            }
                                        }

                                        if (!empty($as)) {
                                            //Searching for the rule by the acadamic stand
                                            $acadamic_rules = ClassRegistry::init('AcademicRule')->find('all', array(
                                                'conditions' => array(
                                                    'AcademicRule.academic_stand_id' => $as['id']
                                                ),
                                                'recursive' => -1
                                            ));

                                            //debug($as);
                                            //If acadamic rule is found
                                            if (!empty($acadamic_rules)) {
                                                $status_found = false;
                                                foreach ($acadamic_rules as $key => $acadamic_rule) {
                                                    $ar = $acadamic_rule['AcademicRule'];
                                                    $sgpa = round($student_exam_status[$stat_index]['sgpa'], 2);
                                                    $cgpa = round($student_exam_status[$stat_index]['cgpa'], 2);

                                                    //Rule matching
                                                    $sgpa_test = true;
                                                    $cgpa_test = true;

                                                    //Is SGPA in the rule?
                                                    if (!empty($ar['sgpa']) && !(($ar['scmo'] == '>' && $sgpa > $ar['sgpa']) || ($ar['scmo'] == '>=' && $sgpa >= $ar['sgpa']) || ($ar['scmo'] == '<' && $sgpa < $ar['sgpa']) || ($ar['scmo'] == '<=' && $sgpa <= $ar['sgpa']))) {
                                                        $sgpa_test = false;
                                                    }

                                                    //Is CGPA in the rule?
                                                    if (!empty($ar['cgpa']) && !(($ar['ccmo'] == '>' && $cgpa > $ar['cgpa']) || ($ar['ccmo'] == '>=' && $cgpa >= $ar['cgpa']) || ($ar['ccmo'] == '<' && $cgpa < $ar['cgpa']) || ($ar['ccmo'] == '<=' && $cgpa <= $ar['cgpa']))) {
                                                        $cgpa_test = false;
                                                    }
                                                    if ($sgpa_test && $cgpa_test) {
                                                        $status_found = true;
                                                        break;
                                                    }
                                                }

                                                //Based on the defined rule, if the student status is determined
                                                if ($status_found) {
                                                    //If the status is warning and there is status history
                                                    //($course_registration['Student']['program_id']==1 && $course_registration['Student']['program_type_id']==1) &&
                                                    debug($credit_hour_sum);
                                                    debug($last_exam_status);
                                                    debug($as['academic_status_id']);

                                                    if (($credit_hour_sum < ClassRegistry::init(
                                                            'AcademicCalendar'
                                                        )->minimumCreditForStatus(
                                                            $course_registration['Student']['id']
                                                        ))) {
                                                        if (!empty($status_histories) && empty($last_exam_status['academic_status_id'])) {
                                                            $academic_status_id = $as['academic_status_id'];
                                                        } else {
                                                            $academic_status_id = null;
                                                        }
                                                        //  $academic_status_id = null;
                                                    } elseif ($academic_statuse['AcademicStatus']['id'] == 3 && !empty($last_exam_status)) {
                                                        //If previous status is warning
                                                        if ($last_exam_status['academic_status_id'] == 3) {
                                                            //Check if there is Two Consecutive Warning (TCW) in the dismisal
                                                            if ($this->isThereTcwRuleInDismisal(
                                                                $student_exam_status[$stat_index]['student_id'],
                                                                $course_registration['Student']['program_id'],
                                                                $student_exam_status[$stat_index]['academic_year'],
                                                                $student_exam_status[$stat_index]['semester'],
                                                                $student_level['year'],
                                                                $student_level['semester'],
                                                                $course_registration['Student']['academicyear']
                                                            )) {
                                                                $academic_status_id = 4; //Dismisal
                                                            } else {
                                                                $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                            }
                                                        } elseif ($last_exam_status['academic_status_id'] == 6) {
                                                            //If previous status is probation
                                                            //Check if there is Probation Followed by Warning (PFW) in the dismisal
                                                            if ($this->isTherePfwRuleInDismisal(
                                                                $student_exam_status[$stat_index]['student_id'],
                                                                $course_registration['Student']['program_id'],
                                                                $student_exam_status[$stat_index]['academic_year'],
                                                                $student_exam_status[$stat_index]['semester'],
                                                                $student_level['year'],
                                                                $student_level['semester'],
                                                                $course_registration['Student']['academicyear']
                                                            )) {
                                                                $academic_status_id = 4; //Dismisal
                                                            } else {
                                                                $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                            }
                                                        } else {
                                                            $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                        }
                                                    } else {
                                                        //($course_registration['Student']['program_id']==1 && $course_registration['Student']['program_type_id']==1) &&
                                                        if (($credit_hour_sum < ClassRegistry::init(
                                                                'AcademicCalendar'
                                                            )->minimumCreditForStatus(
                                                                $course_registration['Student']['id']
                                                            ))) {
                                                            if (!empty($status_histories) && empty($last_exam_status['academic_status_id'])) {
                                                                $academic_status_id = $as['academic_status_id'];
                                                            } else {
                                                                $academic_status_id = null;
                                                            }
                                                        } else {
                                                            $academic_status_id = $academic_statuse['AcademicStatus']['id'];
                                                        }
                                                    }
                                                    //Later on integrate it with multi rule
                                                    $student_exam_status[$stat_index]['academic_status_id'] = $academic_status_id;
                                                    break 2;
                                                }
                                            }
                                        }
                                    } //End of acadamic stands searching (loop)
                                }
                            } //End of academic status list (loop)
                        }

                        // any other academic rule that must override previous status

                        $otherAcademicRule = ClassRegistry::init('OtherAcademicRule')->whatIsTheStatus(
                            $course_and_grades,
                            $course_registration['Student'],
                            $student_level
                        );

                        if (isset($otherAcademicRule) && !empty($otherAcademicRule)) {
                            $student_exam_status[$stat_index]['academic_status_id'] = $otherAcademicRule;
                        } else {
                            debug($otherAcademicRule);
                        }
                    } else {
                        //Grade is not fully submitted and there is nothing to do here
                    }
                } elseif (count($ay_and_s_list) > $pattern) {
                    //There is program transfer in the middle. and the missed semester is integrated with the current semester with the above if condition.
                    //There is nothing to do here unless exceptional demand is raised
                } else {
                    //Pattern is not fulfilled and wait till the next semester to generate status and there is nothing to do here
                    debug($student_exam_status);
                }
            }
            //End of each registered student loop
        }

        if (!empty($student_exam_status)) {
            if (!$this->saveAll($student_exam_status, array('validate' => false))) {
                $fully_saved = false;
            }
        }
        return $fully_saved;
    }


    public function getMostRecentStatusForSMS($phoneNumber)
    {

        $studentDetail = ClassRegistry::init('Student')->find('first', array(
            'conditions' => array(
                'Student.phone_mobile' => $phoneNumber
            ),
            'contain' => array('User')
        ));

        $statusDetail = '';

        if (!empty($studentDetail)) {
            $mostRecentStatus = $this->find("first", array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $studentDetail['Student']['id']
                ),
                'contain' => array(
                    'Student',
                    'AcademicStatus'
                ),
                'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC')
            ));
            //return $mostRecentStatus;
            return $this->formateStatusForSMS($mostRecentStatus);
        } else {
            // parent phone number ? what if the parent has more than one child ?
            $parentPhone = ClassRegistry::init('Contact')->find('all', array(
                'conditions' => array(
                    'Contact.phone_mobile' => $phoneNumber
                ),
                'contain' => array(
                    'Student',
                    'AcademicStatus'
                )
            ));

            if (!empty($parentPhone)) {
                $allofTheirKids = 'Your child ';
                foreach ($parentPhone as $k => $pv) {
                    $mostRecentStatus = $this->find("first", array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $pv['Student']['id']
                        ),
                        'contain' => array(
                            'Student',
                            'AcademicStatus'
                        ),
                        'order' => array(
                            'StudentExamStatus.academic_year' => 'DESC',
                            'StudentExamStatus.semester' => 'DESC'
                        )
                    ));
                    $allofTheirKids .= $this->formateStatusForSMS($mostRecentStatus);
                }
                return $allofTheirKids;
            }
        }
        return "You dont have the privilage to view student status.";
    }

    public function formateStatusForSMS($mostRecentStatus)
    {

        $display = '';
        if (!empty($mostRecentStatus)) {
            $statusname = (!empty($mostRecentStatus['AcademicStatus']['name'])) ? $mostRecentStatus['AcademicStatus']['name'] : "undetermined";
            $display .= $mostRecentStatus['Student']['first_name'] . ' ' . $mostRecentStatus['Student']['last_name'] . '(' . $mostRecentStatus['Student']['studentnumber'] . ') has an academic status of ' . $statusname . ' with SGPA:' . $mostRecentStatus['StudentExamStatus']['sgpa'] . ' and CGPA:' . $mostRecentStatus['StudentExamStatus']['cgpa'] . ' for an academic year ' . $mostRecentStatus['StudentExamStatus']['academic_year'] . ' of semester ' . $mostRecentStatus['StudentExamStatus']['semester'];
            return $display;
        }
        return "There is no academic status to view currently.";
    }

    public function getGraduatingStudent(
        $acadamic_year,
        $program_id,
        $program_type_id,
        $department_id,
        $gender,
        $region_id
    ) {

        return array();
    }

    public function getGraduatingRateToEntryStudent(
        $acadamic_year,
        $program_id,
        $program_type_id,
        $department_id,
        $sex = "all",
        $region_id
    ) {

        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());

        $options = array();
        $optionAdmitted = array();
        $regions = $this->Student->Region->find('list');
        $departments = array();

        $options['conditions'][] = 'Student.graduated = 1';
        $optionAdmitted['conditions'][] = 'Student.graduated = 0';

        $options['contain'] = array(
            'GraduateList',
            'Department',
            'Program',
            'ProgramType',
            'AcceptedStudent'
        );

        $optionAdmitted = array(
            'Department',
            'Program',
            'ProgramType',
            'AcceptedStudent'
        );

        $options['order'] = array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC');

        if (isset($region_id) && !empty($region_id)) {
            $options['conditions']['Student.region_id'] = $region_id;
            $optionAdmitted['conditions']['Student.region_id'] = $region_id;
        }

        if (isset($sex) && !empty($sex) && $sex != "all") {
            $options['conditions']['Student.gender'] = $sex;
            $optionAdmitted['conditions']['Student.sex'] = $sex;
        }

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $options['conditions']['Student.program_id'] = $program_ids[1];
                $optionAdmitted['conditions']['Student.program_id'] = $program_ids[1];
            } else {
                $options['conditions']['Student.program_id'] = $program_id;
                $optionAdmitted['conditions']['Student.program_id'] = $program_id;
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $options['conditions']['Student.program_id'] = $program_type_ids[1];
                $optionAdmitted['conditions']['Student.program_type_id'] = $program_type_ids[1];
            } else {
                $options['conditions']['Student.program_id'] = $program_type_id;
                $optionAdmitted['conditions']['Student.program_type_id'] = $program_type_id;
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $graduateDate = $AcademicYear->getAcademicYearBegainingDate($acadamic_year);
            $options['conditions'][] = "Student.id IN (SELECT student_id FROM graduate_lists where graduate_date >='$graduateDate')";
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            } else {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array(
                        'College',
                        'YearLevel' => array(
                            'order' => array('YearLevel.name ASC')
                        )
                    )
                ));
            }
        } else {
            $departments = $this->Student->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array(
                    'College',
                    'YearLevel' => array(
                        'order' => array('YearLevel.name ASC')
                    )
                )
            ));
        }

        $distributionGraduateEntry = array();

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                $options['conditions']['Student.department_id'] = $value['Department']['id'];
                $optionAdmitted['conditions']['Student.department_id'] = $value['Department']['id'];
                $graduateStudents = $this->Student->find('all', $options);

                if (!empty($graduateStudents)) {
                    foreach ($graduateStudents as $gkey => $gvalue) {
                        debug($gvalue);
                        if (!empty($gvalue['AcceptedStudent']['academicyear']) && !empty($gvalue['GraduateList']['graduate_date'])) {
                            $distributionGraduateEntry[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $gvalue['Program']['name'] . '~' . $gvalue['ProgramType']['name'] . '~' . $gvalue['GraduateList']['graduate_date']][$gvalue['AcceptedStudent']['academicyear']][strtolower(
                                $gvalue['Student']['gender']
                            )]['graduated']++;
                            $optionAdmitted['conditions']['AcceptedStudent.academicyear'] = $gvalue['AcceptedStudent']['academicyear'];
                            $optionAdmitted['conditions']['Student.program_id'] = $gvalue['Student']['program_id'];
                            $optionAdmitted['conditions']['Student.program_type_id'] = $gvalue['Student']['program_type_id'];
                            $optionAdmitted['conditions']['Student.gender'] = $gvalue['Student']['gender'];
                            $distributionGraduateEntry[$value['College']['name'] . '~' . $value['Department']['name'] . '~' . $gvalue['Program']['name'] . '~' . $gvalue['ProgramType']['name'] . '~' . $gvalue['GraduateList']['graduate_date']][$gvalue['AcceptedStudent']['academicyear']][strtolower(
                                $gvalue['Student']['gender']
                            )]['admitted'] = $this->Student->find('count', $optionAdmitted);
                        }
                    }
                }
            }
        }

        $distribution['distributionGraduateEntry'] = $distributionGraduateEntry;
        debug($distribution);
        return $distribution;
    }

    function getMostRecentStudentStatus($college_id)
    {

        $notGraduatedstudents = $this->Student->find('all', array(
            'conditions' => array(
                'Student.college_id' => $college_id,
                //'Student.id'=>9925,
                'Student.graduated' => 0
            ),
        ));

        if (!empty($notGraduatedstudents)) {
            foreach ($notGraduatedstudents as $k => &$val) {
                //check status
                $status = $this->find('first', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $val['Student']['id']
                    ),
                    'order' => array(
                        'StudentExamStatus.academic_year DESC',
                        'StudentExamStatus.semester DESC'
                    ),
                    'recursive' => -1
                ));

                if ($status['StudentExamStatus']['academic_status_id'] == 4 && !empty($status)) {
                    unset($notGraduatedstudents[$k]);
                } else {
                    $val['Student']['status_academic_year'] = isset($status['StudentExamStatus']['academic_year']) ? $status['StudentExamStatus']['academic_year'] : '';
                    $val['Student']['status_semester'] = isset($status['StudentExamStatus']['semester']) ? $status['StudentExamStatus']['semester'] : '';
                }
            }
        }

        return $notGraduatedstudents;
    }

    function getMostRecentStudentStatusForKoha($student_ids, $acceptedId = 0)
    {

        if ($acceptedId == 1) {
            $notGraduatedstudents = $this->Student->find('all', array(
                'conditions' => array(
                    'Student.accepted_student_id' => $student_ids,
                    'Student.graduated' => 0
                ),
                'contain' => array(
                    'AcceptedStudent',
                    'Department',
                    'User'
                )
            ));
        } else {
            $notGraduatedstudents = $this->Student->find('all', array(
                'conditions' => array(
                    'Student.id' => $student_ids,
                    'Student.graduated' => 0
                ),
                'contain' => array(
                    'AcceptedStudent',
                    'Department',
                    'User'
                )
            ));
        }

        if (!empty($notGraduatedstudents)) {
            foreach ($notGraduatedstudents as $k => &$val) {
                $expired = $this->Student->isBorrowerExpired(
                    $val['Student']['studentnumber'],
                    $val['Student']['college_id']
                );
                if ($expired) {
                    //check status
                    $status = $this->find('first', array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $val['Student']['id']
                        ),
                        'order' => array(
                            'StudentExamStatus.academic_year DESC',
                            'StudentExamStatus.semester DESC'
                        ),
                        'recursive' => -1
                    ));

                    if (isset($status) && !empty($status)) {
                        if ($status['StudentExamStatus']['academic_status_id'] == 4 && !empty($status)) {
                            unset($notGraduatedstudents[$k]);
                        } else {
                            $val['Student']['status_academic_year'] = isset($status['StudentExamStatus']['academic_year']) ? $status['StudentExamStatus']['academic_year'] : '';
                            $val['Student']['status_semester'] = isset($status['StudentExamStatus']['semester']) ? $status['StudentExamStatus']['semester'] : '';
                        }
                    }
                }
            }
        }

        return $notGraduatedstudents;
    }


    function getStudentTakenCreditsForExitExam($student_id)
    {

        $student = $this->Student->find('all', array(
            'conditions' => array(
                'Student.id' => $student_id,
                'Student.graduated' => 0,
            ),
            'contain' => array(
                'CourseRegistration.id' => array(
                    'PublishedCourse' => array(
                        'fields' => array(
                            'PublishedCourse.id',
                            'PublishedCourse.add',
                            'PublishedCourse.drop'
                        ),
                        'Course.credit',
                        'Course.major',
                        'Course.thesis',
                        'Course.exit_exam',
                    )
                ),
                'CourseAdd.id' => array(
                    'PublishedCourse' => array(
                        'fields' => array(
                            'PublishedCourse.id',
                            'PublishedCourse.add',
                            'PublishedCourse.drop'
                        ),
                        'Course.credit',
                        'Course.major',
                        'Course.thesis',
                        'Course.exit_exam',
                    )
                ),
                'Attachment' => array(
                    'conditions' => array(
                        'Attachment.model' => 'Student'
                    )
                ),

            ),

            'fields' => array(
                'id',
                'graduated',
                'curriculum_id',
            ),
            'recursive' => -1,
        ));

        debug($student);

        $taken = array();
        $taken_course_count = 0;

        $curriculum_major_course_count = 0;
        $curriculum_minor_course_count = 0;

        $taken_major_course_count = 0;
        $taken_minor_course_count = 0;

        $taken_major_course_credit = 0;
        $taken_minor_course_credit = 0;

        $droped_credit_sum = 0;
        $droped_courses_count = 0;

        $course_count_registration = 0;
        $course_count_add = 0;

        $credit_sum_registration = 0;
        $credit_sum_add = 0;

        $thesis_taken = 0;
        $thesis_credit = 0;

        $credit_sum = 0;

        if (!empty($student['CourseRegistration'])) {
            foreach ($student['CourseRegistration'] as $key => $course_registration) {
                if (!$this->Student->CourseRegistration->isCourseDroped(
                        $course_registration['id']
                    ) && $course_registration['PublishedCourse']['drop'] == 0 && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime(
                        $course_registration['id'],
                        1,
                        1
                    )) {
                    $credit_sum += $course_registration['PublishedCourse']['Course']['credit'];
                    $taken_course_count++;
                    $course_count_registration++;
                    $credit_sum_registration += $course_registration['PublishedCourse']['Course']['credit'];

                    if ($course_registration['PublishedCourse']['Course']['major'] == 1) {
                        $taken_major_course_count++;
                        $taken_major_course_credit += $course_registration['PublishedCourse']['Course']['credit'];

                        if ($course_registration['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_registration['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }
                    } else {
                        $taken_minor_course_count++;
                        $taken_minor_course_credit += $course_registration['PublishedCourse']['Course']['credit'];

                        if ($course_registration['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_registration['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }
                    }
                } else {
                    $droped_credit_sum += $course_registration['PublishedCourse']['Course']['credit'];
                    $droped_courses_count++;
                }
            }
        }

        if (!empty($student['CourseAdd'])) {
            foreach ($student['CourseAdd'] as $key => $course_add) {
                if ($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime(
                    $course_add['id'],
                    0,
                    1
                )) {
                    $credit_sum += $course_add['PublishedCourse']['Course']['credit'];
                    $taken_course_count++;
                    $course_count_add++;
                    $credit_sum_add += $course_add['PublishedCourse']['Course']['credit'];

                    if ($course_add['PublishedCourse']['Course']['major'] == 1) {
                        $taken_major_course_count++;
                        $taken_major_course_credit += $course_add['PublishedCourse']['Course']['credit'];

                        if ($course_add['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_add['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }
                    } else {
                        $taken_minor_course_count++;
                        $taken_minor_course_credit += $course_add['PublishedCourse']['Course']['credit'];

                        if ($course_add['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_add['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }
                    }
                }
            }
        }

        $all_exempted_courses = $this->Student->CourseExemption->find('all', array(
            'conditions' => array(
                'CourseExemption.student_id' => $student_id,
                'CourseExemption.department_accept_reject' => 1,
                'CourseExemption.registrar_confirm_deny' => 1,
            ),
            'recursive' => -1
        ));

        $curriculum_major_course_count = $this->Student->Curriculum->Course->find('count', array(
            'conditions' =>
                array(
                    'Course.curriculum_id' => $student['Student']['curriculum_id'],
                    'Course.major' => 1,
                ),
        ));

        $curriculum_minor_course_count = $this->Student->Curriculum->Course->find('count', array(
            'conditions' => array(
                'Course.curriculum_id' => $student['Student']['curriculum_id'],
                'Course.major' => 0,
            ),
        ));

        $studentAttachedCurriculumIds = $this->Student->CurriculumAttachment->find('list', array(
            'conditions' => array(
                'CurriculumAttachment.student_id' => $student['Student']['id'],
            ),
            'fields' => array(
                'id',
                'curriculum_id',
            ),
        ));

        $student_curriculum_course_list = $this->Student->Curriculum->Course->find('list', array(
            'conditions' => array(
                'Course.curriculum_id' => $studentAttachedCurriculumIds,
            ),
            'fields' => array(
                'id',
                'credit',
                'major',
                'thesis',
            ),
            'recursive' => -1,
        ));

        $student_curriculum_course_id_list = array_keys($student_curriculum_course_list);

        $exempted_credit_sum = 0;
        $exempted_course_count = 0;

        if (!empty($all_exempted_courses)) {
            foreach ($all_exempted_courses as $ec_key => $all_exempted_course) {
                //Check if the exempted course is from their curriculum
                if (in_array(
                    $all_exempted_course['CourseExemption']['course_id'],
                    $student_curriculum_course_id_list
                )) {
                    //$credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                    $exempted_credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                    $exempted_course_count++;
                }
            }
        }

        $photo_dirname = null;
        $photo_basename = 'noimage.jpg';

        if (!empty($student['Attachment'])) {
            foreach ($student['Attachment'] as $ak => $av) {
                if (!empty($av['dirname']) && !empty($av['basename'])) {
                    $photo_dirname = $av['dirname'];
                    $photo_basename = $av['basename'];
                }
            }
        }

        $taken['credit_sum'] = $credit_sum;
        $taken['exempted_credit_sum'] = $exempted_credit_sum;
        $taken['exempted_course_count'] = $exempted_course_count;
        $taken['taken_course_count'] = $taken_course_count;
        $taken['curriculum_major_course_count'] = $curriculum_major_course_count;
        $taken['curriculum_minor_course_count'] = $curriculum_minor_course_count;
        $taken['taken_major_course_count'] = $taken_major_course_count;
        $taken['taken_minor_course_count'] = $taken_minor_course_count;
        $taken['taken_major_course_credit'] = $taken_major_course_credit;
        $taken['taken_minor_course_credit'] = $taken_minor_course_credit;
        $taken['course_count_registration'] = $course_count_registration;
        $taken['course_count_add'] = $course_count_add;
        $taken['credit_sum_registration'] = $credit_sum_registration;
        $taken['credit_sum_add'] = $credit_sum_add;
        $taken['thesis_taken'] = $thesis_taken;
        $taken['thesis_credit'] = $thesis_credit;

        $taken['droped_courses_count'] = $droped_courses_count;
        $taken['droped_credit_sum'] = $droped_credit_sum;

        $taken['photo_dirname'] = $photo_dirname;
        $taken['photo_basename'] = $photo_basename;

        return $taken;
    }


    public function getStudentResultsForHemis(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $freshman = 0,
        $exclude_graduated = '',
        $only_with_complete_data = 0
    ) {

        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $query = '';
        $secQueryIn = ' id is not null ';
        $college_id = array();

        // $query .= ' and stexam.academic_status_id !=4';

        if (isset($region_id) && !empty($region_id)) {
            $query .= ' and s.region_id=' . $region_id . '';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        if (isset($sex) && !empty($sex) && $sex != "all") {
            $query .= ' and s.gender="' . $sex . '"';
        }

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $query .= ' and s.program_id=' . $program_ids[1] . '';
                $secQueryIn .= ' and program_id=' . $program_ids[1] . '';
            } else {
                $query .= ' and s.program_id=' . $program_id . '';
                $secQueryIn .= ' and program_id=' . $program_id . '';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $query .= ' and s.program_type_id=' . $program_type_ids[1] . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_ids[1] . '';
            } else {
                $query .= ' and s.program_type_id=' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_id . '';
            }
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            //$query .= ' and sec.academicyear="'.$acadamic_year.'"';
            $secQueryIn .= ' and academicyear="' . $acadamic_year . '"';
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_ids[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            }
        } else {
            $departments = $this->Student->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel')
            ));
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            $query .= ' and stexam.academic_year="' . $acadamic_year . '"';
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and stexam.semester="' . $semester . '"';
        }

        $studentResultsHemis = array();

        $secQueryYD = '';
        $count = 0;

        if ($freshman == 1) {
            $departments = array();
        }

        if ($freshman == 0) {
            //$query .= ' and s.curriculum_id is not null ';
            if (!empty($departments)) {
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

                    foreach ($yearLevel as $ykey => $yvalue) {
                        $secQueryYD .= ' and year_level_id="' . $yvalue['id'] . '"';
                        $secQueryYD .= ' and department_id="' . $value['Department']['id'] . '"';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
                        ));

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);

                        $student_ids = implode(", ", $x);

                        $studentResultsHemisSQL = "SELECT DISTINCT s.studentnumber, s.id , s.first_name, s.middle_name, s.last_name, s.gender, s.department_id, s.college_id, s.region_id, s.program_id, s.program_type_id, s.graduated, s.academicyear, s.student_national_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year
						FROM students AS s, student_exam_statuses AS stexam
						WHERE stexam.student_id = s.id $query and stexam.student_id in ($student_ids) order by stexam.academic_year DESC, stexam.semester  DESC, s.first_name ASC, s.middle_name ASC";

                        $disResult = $this->query($studentResultsHemisSQL);

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $studentTakenCreditsSemesters = ClassRegistry::init(
                                    'StudentExamStatus'
                                )->getStudentTotalAccumulatedCreditsAndSemesterCount(
                                    $dr['s']['id'],
                                    $acadamic_year,
                                    $semester
                                );

                                $institutionCodes = ClassRegistry::init('Department')->find('first', array(
                                    'conditions' => array(
                                        'Department.id' => $dr['s']['department_id']
                                    ),
                                    'contain' => array(
                                        'College' => array(
                                            'fields' => array(
                                                'College.id',
                                                'College.name',
                                                'College.shortname',
                                                'College.institution_code',
                                            ),
                                            'Campus' => array(
                                                'fields' => array(
                                                    'Campus.id',
                                                    'Campus.name',
                                                    'Campus.campus_code',
                                                ),
                                            )
                                        )
                                    ),
                                    'fields' => array(
                                        'Department.id',
                                        'Department.name',
                                        'Department.institution_code',
                                    ),
                                    'recursive' => -1
                                ));

                                $studentRegion['Region'] = ClassRegistry::init('Region')->field(
                                    'Region.name',
                                    array('Region.id' => $dr['s']['region_id'])
                                );

                                $studentTakenCreditsSemesters['StudentTakenCreditsSemesters'] = $studentTakenCreditsSemesters;

                                if ($only_with_complete_data && (is_null($dr['s']['student_national_id']) || is_null(
                                            $institutionCodes['Department']['institution_code']
                                        ))) {
                                    continue;
                                }

                                $mg = array_merge(
                                    $dr['s'],
                                    $dr['stexam'],
                                    $institutionCodes,
                                    $studentTakenCreditsSemesters,
                                    $studentRegion
                                );
                                $studentResultsHemis[$dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] /* . '~' . $value['College']['shortname'] . '~' . $value['Department']['name']  */][$count] = $mg;
                                $count++;
                            }
                        }

                        $studentResultsHemisSQL = '';
                    }
                }
            }
        } else {
            $college_id = array();
            $colleges = array();

            if (isset($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.id' => $college_ids[1],
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                }
            }

            if (isset($college_id) && !empty($college_id)) {
                $secQueryYD = '';
                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
                );

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $secQueryYD .= ' and college_id="' . $cv['College']['id'] . '" and department_id is null and id is not null and (year_level_id is null or year_level_id = "" or year_level_id = 0)';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array(
                                'StudentsSection.student_id',
                                'StudentsSection.section_id'
                            )
                        ));

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $studentResultsHemisSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.department_id, s.college_id, s.region_id, s.program_id, s.program_type_id, s.graduated, s.academicyear,s.student_national_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year
						FROM students AS s, student_exam_statuses AS stexam
						WHERE stexam.student_id = s.id $query and stexam.student_id in ($student_ids) order by stexam.academic_year DESC, stexam.semester  DESC, s.first_name ASC, s.middle_name ASC";

                        $disResult = $this->query($studentResultsHemisSQL);

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $studentTakenCreditsSemesters = ClassRegistry::init(
                                    'StudentExamStatus'
                                )->getStudentTotalAccumulatedCreditsAndSemesterCount(
                                    $dr['s']['id'],
                                    $acadamic_year,
                                    $semester
                                );

                                $institutionCodes = ClassRegistry::init('College')->find('first', array(
                                    'conditions' => array(
                                        'College.id' => $dr['s']['college_id']
                                    ),
                                    'contain' => array(
                                        'Campus' => array(
                                            'fields' => array(
                                                'Campus.id',
                                                'Campus.name',
                                                'Campus.campus_code',
                                            ),
                                        )
                                    ),
                                    'fields' => array(
                                        'College.id',
                                        'College.name',
                                        'College.shortname',
                                        'College.institution_code',
                                    ),
                                    'recursive' => -1
                                ));

                                $studentRegion['Region'] = ClassRegistry::init('Region')->field(
                                    'Region.name',
                                    array('Region.id' => $dr['s']['region_id'])
                                );

                                $studentTakenCreditsSemesters['StudentTakenCreditsSemesters'] = $studentTakenCreditsSemesters;

                                if ($only_with_complete_data && (is_null($dr['s']['student_national_id']) || is_null(
                                            $institutionCodes['College']['institution_code']
                                        ))) {
                                    continue;
                                }
                                $mg = array_merge(
                                    $dr['s'],
                                    $dr['stexam'],
                                    $institutionCodes,
                                    $studentTakenCreditsSemesters,
                                    $studentRegion
                                );
                                $studentResultsHemis[$dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester']/*  . '~' .  $cv['College']['shortname'] */][$count] = $mg;
                                $count++;
                            }
                        }
                        $studentResultsHemisSQL = '';
                    }
                }
            }
        }

        return $studentResultsHemis;
    }

    public function getStudentTotalAccumulatedCreditsAndSemesterCount(
        $student_id = null,
        $academic_year = null,
        $semester = null
    ) {

        if (isset($student_id) && !empty($student_id) && isset($academic_year) && !empty($academic_year) && isset($semester) && !empty($semester)) {
            $totalAttendedSemesters = $this->find('list', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id
                ),
                'fields' => array(
                    'StudentExamStatus.id',
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester',
                ),
                'group' => array(
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester'
                ),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                )
            ));

            //debug($totalAttendedSemesters);

            $lastGPA = $this->find('all', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id
                ),
                /* 'fields' => array(
                    'StudentExamStatus.id',
                    'StudentExamStatus.student_id',
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester',
                    'StudentExamStatus.sgpa',
                    'StudentExamStatus.cgpa',
                ), */
                'group' => array(
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester'
                ),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                ),
                'recursive' => -1
            ));

            $latastLastStatus = array();

            if (!empty($lastGPA)) {
                if ($lastGPA[0]['StudentExamStatus']['academic_year'] != $academic_year && $lastGPA[0]['StudentExamStatus']['semester'] != $semester) {
                    /* debug($lastGPA[0]['StudentExamStatus']['student_id']);
                    debug($lastGPA[0]['StudentExamStatus']['academic_year']);
                    debug($lastGPA[0]['StudentExamStatus']['semester']);
                    debug($lastGPA[0]['StudentExamStatus']['cgpa']);
                    debug($lastGPA[0]['StudentExamStatus']['sgpa']); */
                    //debug($lastGPA[0]['StudentExamStatus']);
                    $latastLastStatus = $lastGPA[0]['StudentExamStatus'];
                }
            }

            //debug($lastGPA);

            $stexstIdsAll = array();
            $curr_sem_stexst_id = null;

            // get the selected acy and semester  student exam status id and find the sum of credits and semester count less than or equal to that student exam status id.

            if (empty($latastLastStatus)) {
                if (!empty($totalAttendedSemesters)) {
                    foreach ($totalAttendedSemesters as $sem => $exstid_acy) {
                        foreach ($exstid_acy as $exstid => $acy) {
                            if ($sem == $semester && $acy == $academic_year) {
                                $curr_sem_stexst_id = $exstid;
                            }
                            array_push($stexstIdsAll, $exstid);
                        }
                    }
                }
            } else {
                if (!empty($totalAttendedSemesters)) {
                    //debug($latastLastStatus['academic_year']);
                    //debug($latastLastStatus['semester']);
                    foreach ($totalAttendedSemesters as $sem => $exstid_acy) {
                        foreach ($exstid_acy as $exstid => $acy) {
                            if ($sem == $latastLastStatus['semester'] && $acy == $latastLastStatus['academic_year']) {
                                $curr_sem_stexst_id = $exstid;
                            }
                            array_push($stexstIdsAll, $exstid);
                        }
                    }
                }
            }

            $stexstIdsSearch = array();

            if (isset($curr_sem_stexst_id) && !empty($stexstIdsAll)) {
                foreach ($stexstIdsAll as $key => $ids) {
                    if ($ids <= $curr_sem_stexst_id) {
                        //debug($ids);
                        array_push($stexstIdsSearch, $ids);
                    }
                }
            }

            $stexst_ids = implode(", ", $stexstIdsSearch);

            $querystexst = "SELECT COUNT(*) AS totalSemesters, SUM(credit_hour_sum) AS totalAccumulatedCredits FROM student_exam_statuses WHERE id in (" . $stexst_ids . " )";
            $result = $this->query($querystexst);

            //debug($result);

            return $result;
        } else {
            return $result = array();
        }
    }

    public function getStudentTotalAccumulatedCreditsAndSemesterCountGraduated($student_id = null)
    {

        if (isset($student_id) && !empty($student_id)) {
            /*
            $totalAttendedSemesters = $this->find('list', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id
                ),
                'fields' => array(
                    'StudentExamStatus.id',
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester',
                ),
                'group' => array(
                    'StudentExamStatus.academic_year',
                    'StudentExamStatus.semester'
                ),
                'order' => array(
                    'StudentExamStatus.academic_year' => 'DESC',
                    'StudentExamStatus.semester' => 'DESC',
                )
            ));

            //debug($totalAttendedSemesters);

            $stexstIdsAll = array();

            foreach ($totalAttendedSemesters as $sem => $exstid_acy) {
                foreach ($exstid_acy as $exstid => $acy) {
                    array_push($stexstIdsAll, $exstid );
                }
            }

            $stexst_ids = implode(", ", $stexstIdsAll);

            debug($stexst_ids);

            $querystexst = "SELECT COUNT(*) AS totalSemesters, SUM(credit_hour_sum) AS totalAccumulatedCredits FROM student_exam_statuses WHERE id in (" .$stexst_ids." )" ;
            $result = $this->query($querystexst);
            return $result[0][0];

            */

            $totalAttendedSemestersCountRegistration = ClassRegistry::init('CourseRegistration')->find('list', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id
                ),
                /* 'fields' => array(
                    'CourseRegistration.student_id',
                    'CourseRegistration.academic_year',
                    'CourseRegistration.semester',
                ), */
                'group' => array(
                    'CourseRegistration.semester',
                    'CourseRegistration.academic_year',
                    'CourseRegistration.student_id',
                ),
                'order' => array(
                    'CourseRegistration.id' => 'DESC',
                    'CourseRegistration.academic_year' => 'DESC',
                    'CourseRegistration.semester' => 'DESC',
                )
            ));

            //debug(count($totalAttendedSemestersCountRegistration));
            //debug($this->Student->calculateCumulativeStudentRegistredAddedCredit($student_id, 1, null, null, 0));

            $result['TotalAcademicPeriods'] = count($totalAttendedSemestersCountRegistration);
            $result['TotalAccumulatedCredits'] = $this->Student->calculateCumulativeStudentRegistredAddedCredit(
                $student_id,
                1,
                null,
                null,
                0
            );

            return $result;
        } else {
            return array();
        }
    }

    public function getStudentGraduateForHemis(
        $department_id = null,
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $only_with_complete_data = 0
    ) {

        $options = array(
            'contain' => array(
                'Student' => array(
                    'fields' => array(
                        'Student.id',
                        'Student.full_name',
                        'Student.first_name',
                        'Student.middle_name',
                        'Student.last_name',
                        'Student.gender',
                        'Student.studentnumber',
                        'Student.program_id',
                        'Student.program_type_id',
                        'Student.student_national_id',
                        'Student.graduated',
                        'Student.region_id',
                        'GraduateList.graduate_date',
                        'GraduateList.minute_number',
                        'GraduateList.student_id',
                    ),
                    'order' => array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC'),
                    'Department' => array(
                        'fields' => array(
                            'Department.id',
                            'Department.name',
                            'Department.institution_code'
                        )
                    ),
                    'College' => array(
                        'fields' => array(
                            'College.id',
                            'College.name',
                            'College.shortname',
                            'College.institution_code'
                        )
                    ),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'Region' => array('id', 'name'),
                    'ExitExam' => array(
                        'fields' => array(
                            'ExitExam.id',
                            'ExitExam.result',
                            'ExitExam.exam_date',
                            'ExitExam.modified'
                        ),
                        'order' => array(
                            'ExitExam.id' => 'DESC',
                            'ExitExam.modified' => 'DESC',
                            'ExitExam.exam_date' => 'DESC'
                        ),
                    ),
                    'StudentExamStatus' => array(
                        'fields' => array(
                            'StudentExamStatus.student_id',
                            'StudentExamStatus.cgpa',
                            'StudentExamStatus.academic_year',
                            'StudentExamStatus.semester'
                        ),
                        'order' => array(
                            'StudentExamStatus.academic_year' => 'DESC',
                            'StudentExamStatus.semester' => 'DESC'
                        ),
                    )
                ),

            ),
            'group' => array('GraduateList.student_id'),
            'order' => 'Student.first_name ASC',
            'recursive' => -1
        );

        if (isset($semester) && !empty($semester) && isset($acadamic_year) && !empty($acadamic_year)) {
            App::import('Component', 'AcademicYear');
            $AcademicYear = new AcademicYearComponent(new ComponentRegistry());
            $acYearBeginingDate = $AcademicYear->getAcademicYearBegainingDate($acadamic_year);
            $selected_year = explode("-", $acYearBeginingDate);

            if (!empty($selected_year[0])) {
                if ($semester == 'I') {
                    $minDate = $acYearBeginingDate;
                    $maxDate = ($selected_year[0] + 1) . '-' . '02' . '-' . '20';
                } elseif ($semester == 'II') {
                    $minDate = ($selected_year[0] + 1) . '-' . '02' . '-' . '21';
                    $maxDate = ($selected_year[0] + 1) . '-' . '06' . '-' . '20';
                } elseif ($semester == 'III') {
                    $minDate = ($selected_year[0] + 1) . '-' . '06' . '-' . '21';
                    $maxDate = ($selected_year[0] + 1) . '-' . '09' . '-' . '20';
                }

                //$query .=  ' and gl.graduate_date BETWEEN "'. $acYearBeginingDate.'" AND "'. $maxDate .'"';
                /* $query .=  ' and gl.graduate_date >= "'. $minDate.'"';
                $query .=  ' and gl.graduate_date <= "'. $maxDate.'"'; */

                //debug($minDate);
                //debug($maxDate);

                $options['conditions'][] = array('GraduateList.graduate_date BETWEEN "' . $minDate . '" AND "' . $maxDate . '"');
            }
        }

        if (isset($department_id) && !empty($department_id)) {
            $c_or_d = explode('~', $department_id);
            if ($c_or_d[0] == 'c') {
                $options['conditions'][]['Student.college_id'] = $c_or_d[1];
                //$options['conditions'][] =  array('Student.department_id IS NULL');
            } else {
                $options['conditions'][]['Student.department_id'] = $department_id;
            }
        }

        if ($program_id != 0) {
            $options['conditions'][]['Student.program_id'] = $program_id;
        }

        if ($program_type_id != 0) {
            $options['conditions'][]['Student.program_type_id'] = $program_type_id;
        }

        if (isset($options['conditions']) && !empty($options['conditions'])) {
            $graduatedStudents = ClassRegistry::init('GraduateList')->find('all', $options);
        } else {
            $graduatedStudents = array();
        }

        //debug(count($graduatedStudents));
        //debug($graduatedStudents);

        $graduatedStudentsFiltered = array();
        $count = 0;

        if (!empty($graduatedStudents)) {
            foreach ($graduatedStudents as $rtst => $grSt) {
                //$departmentStudyPogramID = ClassRegistry::init('Curriculum')->field('Curriculum.department_study_program_id', array('Curriculum.id' => $dr['s']['curriculum_id']));

                if ($only_with_complete_data && (is_null($grSt['Student']['student_national_id']) || is_null(
                            $grSt['Student']['Department']['institution_code']
                        ))) {
                    //debug($grSt['Student']);
                    continue;
                }

                $grSt['GraduateList']['academic_year'] = $acadamic_year;
                $grSt['GraduateList']['semester'] = $semester;

                $grSt['GraduateList']['AccumulatedCreditsAndSemesterCount'] = array();
                $grSt['GraduateList']['AccumulatedCreditsAndSemesterCount'] = $this->getStudentTotalAccumulatedCreditsAndSemesterCountGraduated(
                    $grSt['Student']['id']
                );


                //$mrgSGL = array_merge($grSt['Student'], $grSt['GraduateList']);
                $graduatedStudentsFiltered[$acadamic_year . '~' . $semester /* . '~' . $grSt['GraduateList']['graduate_date'] . '~' . $grSt['GraduateList']['minute_number'] . '~'  . $grSt['Student']['Program']['name'] . '~' . $grSt['Student']['ProgramType']['name'] . '~' . $grSt['Student']['Department']['name'] .'~' . $grSt['Student']['College']['name'] */][$count] = $grSt;
                $count++;
            }
        }

        //debug($graduatedStudentsFiltered);
        return $graduatedStudentsFiltered;
    }

    public function getStudentTakenCreditsForHemis($student_id, $graduated = 0)
    {

        $student = $this->Student->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id,
                'Student.graduated' => $graduated,
            ),
            'contain' => array(
                'CourseRegistration.id' => array(
                    'PublishedCourse' => array(
                        'fields' => array(
                            'PublishedCourse.id',
                            'PublishedCourse.drop'
                        ),
                        'Course.credit',
                        'Course.major',
                        'Course.thesis',
                        'Course.exit_exam',
                    )
                ),
                'CourseAdd.id' => array(
                    'PublishedCourse' => array(
                        'fields' => array(
                            'PublishedCourse.id',
                            'PublishedCourse.drop'
                        ),
                        'Course.credit',
                        'Course.major',
                        'Course.thesis',
                        'Course.exit_exam',
                    )
                ),
                'Attachment' => array(
                    'conditions' => array(
                        'Attachment.model' => 'Student'
                    )
                ),
            ),
            'fields' => array(
                'Student.id',
                'Student.graduated',
                'Student.curriculum_id',
            ),
            'recursive' => -1,
        ));

        //debug($student);

        $taken = array();
        $taken_course_count = 0;

        $curriculum_major_course_count = 0;
        $curriculum_minor_course_count = 0;

        $taken_major_course_count = 0;
        $taken_minor_course_count = 0;

        $taken_major_course_credit = 0;
        $taken_minor_course_credit = 0;

        $droped_credit_sum = 0;
        $droped_courses_count = 0;

        $course_count_registration = 0;
        $course_count_add = 0;

        $credit_sum_registration = 0;
        $credit_sum_add = 0;

        $thesis_taken = 0;
        $thesis_credit = 0;

        $exit_exam_credit = 0;
        $exit_exam_taken = 0;

        $credit_sum = 0;

        if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
            foreach ($student['CourseRegistration'] as $key => $course_registration) {
                if (!$this->Student->CourseRegistration->isCourseDroped(
                        $course_registration['id']
                    ) && $course_registration['PublishedCourse']['drop'] == 0 && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime(
                        $course_registration['id'],
                        1,
                        1
                    )) {
                    $credit_sum += $course_registration['PublishedCourse']['Course']['credit'];
                    $taken_course_count++;
                    $course_count_registration++;
                    $credit_sum_registration += $course_registration['PublishedCourse']['Course']['credit'];

                    if ($course_registration['PublishedCourse']['Course']['major'] == 1) {
                        $taken_major_course_count++;
                        $taken_major_course_credit += $course_registration['PublishedCourse']['Course']['credit'];

                        if ($course_registration['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_registration['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }

                        if ($course_registration['PublishedCourse']['Course']['exit_exam'] == 1) {
                            $exit_exam_credit = $course_registration['PublishedCourse']['Course']['credit'];
                            $exit_exam_taken = 1;
                        }
                    } else {
                        $taken_minor_course_count++;
                        $taken_minor_course_credit += $course_registration['PublishedCourse']['Course']['credit'];

                        if ($course_registration['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_registration['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }

                        if ($course_registration['PublishedCourse']['Course']['exit_exam'] == 1) {
                            $exit_exam_credit = $course_registration['PublishedCourse']['Course']['credit'];
                            $exit_exam_taken = 1;
                        }
                    }
                } else {
                    $droped_credit_sum += $course_registration['PublishedCourse']['Course']['credit'];
                    $droped_courses_count++;
                }
            }
        }

        if (isset($student['CourseAdd']) && !empty($student['CourseAdd'])) {
            foreach ($student['CourseAdd'] as $key => $course_add) {
                if ($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime(
                    $course_add['id'],
                    0,
                    1
                )) {
                    $credit_sum += $course_add['PublishedCourse']['Course']['credit'];
                    $taken_course_count++;
                    $course_count_add++;
                    $credit_sum_add += $course_add['PublishedCourse']['Course']['credit'];

                    if ($course_add['PublishedCourse']['Course']['major'] == 1) {
                        $taken_major_course_count++;
                        $taken_major_course_credit += $course_add['PublishedCourse']['Course']['credit'];

                        if ($course_add['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_add['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }

                        if ($course_add['PublishedCourse']['Course']['exit_exam'] == 1) {
                            $exit_exam_credit = $course_add['PublishedCourse']['Course']['credit'];
                            $exit_exam_taken = 1;
                        }
                    } else {
                        $taken_minor_course_count++;
                        $taken_minor_course_credit += $course_add['PublishedCourse']['Course']['credit'];

                        if ($course_add['PublishedCourse']['Course']['thesis'] == 1) {
                            $thesis_credit = $course_add['PublishedCourse']['Course']['credit'];
                            $thesis_taken = 1;
                        }
                        if ($course_add['PublishedCourse']['Course']['exit_exam'] == 1) {
                            $exit_exam_credit = $course_add['PublishedCourse']['Course']['credit'];
                            $exit_exam_taken = 1;
                        }
                    }
                }
            }
        }


        $all_exempted_courses = $this->Student->CourseExemption->find('all', array(
            'conditions' => array(
                'CourseExemption.student_id' => $student['Student']['id'],
                'CourseExemption.department_accept_reject' => 1,
                'CourseExemption.registrar_confirm_deny' => 1,
            ),
            'recursive' => -1
        ));

        $curriculum_major_course_count = $this->Student->Curriculum->Course->find('count', array(
            'conditions' => array(
                'Course.curriculum_id' => $student['Student']['curriculum_id'],
                'Course.major' => 1,
            ),
        ));

        $curriculum_minor_course_count = $this->Student->Curriculum->Course->find('count', array(
            'conditions' => array(
                'Course.curriculum_id' => $student['Student']['curriculum_id'],
                'Course.major' => 0,
            ),
        ));

        $studentAttachedCurriculumIds = $this->Student->CurriculumAttachment->find('list', array(
            'conditions' => array(
                'CurriculumAttachment.student_id' => $student['Student']['id'],
            ),
            'fields' => array(
                'id',
                'curriculum_id',
            ),
        ));

        $student_curriculum_course_list = $this->Student->Curriculum->Course->find('list', array(
            'conditions' => array(
                'Course.curriculum_id' => $studentAttachedCurriculumIds,
            ),
            'fields' => array(
                'id',
                'credit',
                'major',
                'thesis',
                'exit_exam',
            ),
            'recursive' => -1,
        ));

        $student_curriculum_course_id_list = array_keys($student_curriculum_course_list);

        $exempted_credit_sum = 0;
        $exempted_course_count = 0;

        if (isset($all_exempted_courses) && !empty($all_exempted_courses)) {
            foreach ($all_exempted_courses as $ec_key => $all_exempted_course) {
                //Check if the exempted course is from their curriculum
                if (in_array(
                    $all_exempted_course['CourseExemption']['course_id'],
                    $student_curriculum_course_id_list
                )) {
                    //$credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                    $exempted_credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                    $exempted_course_count++;
                }
            }
        }

        $photo_dirname = null;
        $photo_basename = 'noimage.jpg';

        if (isset($student['Attachment']) && !empty($student['Attachment'])) {
            foreach ($student['Attachment'] as $ak => $av) {
                if (!empty($av['dirname']) && !empty($av['basename'])) {
                    $photo_dirname = $av['dirname'];
                    $photo_basename = $av['basename'];
                }
            }
        }

        $taken['credit_sum'] = $credit_sum;
        $taken['exempted_credit_sum'] = $exempted_credit_sum;
        $taken['exempted_course_count'] = $exempted_course_count;
        $taken['taken_course_count'] = $taken_course_count;
        $taken['curriculum_major_course_count'] = $curriculum_major_course_count;
        $taken['curriculum_minor_course_count'] = $curriculum_minor_course_count;
        $taken['taken_major_course_count'] = $taken_major_course_count;
        $taken['taken_minor_course_count'] = $taken_minor_course_count;
        $taken['taken_major_course_credit'] = $taken_major_course_credit;
        $taken['taken_minor_course_credit'] = $taken_minor_course_credit;
        $taken['course_count_registration'] = $course_count_registration;
        $taken['course_count_add'] = $course_count_add;
        $taken['credit_sum_registration'] = $credit_sum_registration;
        $taken['credit_sum_add'] = $credit_sum_add;
        $taken['thesis_taken'] = $thesis_taken;
        $taken['thesis_credit'] = $thesis_credit;

        $taken['exit_exam_taken'] = $exit_exam_taken;
        $taken['exit_exam_credit'] = $exit_exam_credit;

        $taken['droped_courses_count'] = $droped_courses_count;
        $taken['droped_credit_sum'] = $droped_credit_sum;

        $taken['photo_dirname'] = $photo_dirname;
        $taken['photo_basename'] = $photo_basename;

        return $taken;
    }

    public function getStudentListForOffice(
        $acadamic_year = null,
        $semester = null,
        $program_id = 0,
        $program_type_id = 0,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $freshman = 0,
        $exclude_graduated = ''
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $queryR = '';
        $secQueryIn = ' id is not null ';
        $secQueryIn = ' id is not null ';

        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        if (!empty($region_id) && $region_id > 0) {
            $queryR .= ' and s.region_id=' . $region_id . '';
        }

        if (!empty($sex) && $sex != "all") {
            $queryR .= ' and s.gender="' . $sex . '"';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $queryR .= ' and s.graduated = 0';
        }

        /* if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $queryR .= ' and s.program_id=' . $program_ids[1] . '';
                $secQueryIn .= ' and program_id=' . $program_ids[1] . '';
            } else {
                $queryR .= ' and s.program_id=' . $program_id . '';
                $secQueryIn .= ' and program_id=' . $program_id . '';
            }
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $queryR .= ' and s.program_type_id=' . $program_type_ids[1] . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_ids[1] . '';
            } else {
                $queryR .= ' and s.program_type_id=' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id=' . $program_type_id . '';
            }
        } */

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $queryR .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
                $secQueryIn .= ' and program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $queryR .= ' and s.program_id = ' . $program_id . '';
                $secQueryIn .= ' and program_id =' . $program_id . '';
            } else {
                // prevent any access
                $queryR .= ' and s.program_id = 0';
                $secQueryIn .= ' and program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $queryR .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
                $secQueryIn .= ' and program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $queryR .= ' and s.program_type_id = ' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $queryR .= ' and s.program_type_id = 0';
                $secQueryIn .= ' and program_type_id = 0';
            }
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
        }

        if (!empty($acadamic_year)) {
            $secQueryIn .= ' and academicyear="' . $acadamic_year . '"';
            $queryR .= ' and reg.academic_year="' . $acadamic_year . '"';
        }

        if (!empty($semester)) {
            $queryR .= ' and reg.semester="' . $semester . '"';
        }

        if ($freshman == 1) {
            $departments = array();
        }

        $secQueryYD = '';
        $count = 0;

        $studentListRegistered = array();

        if ($freshman == 0) {
            if (!empty($departments)) {
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
                            $secQueryYD .= ' and year_level_id="' . $yvalue['id'] . '"';
                            $secQueryYD .= ' and department_id="' . $value['Department']['id'] . '"';

                            $ylID = $yvalue['id'];
                            $deptID = $value['Department']['id'];

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD)"
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);
                            $student_ids = implode(", ", $x);

                            $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.program_id, s.program_type_id, s.curriculum_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated, s.email, s.email_alternative, s.phone_mobile
							FROM students AS s, course_registrations AS reg
							WHERE s.department_id = $deptID AND reg.year_level_id = $ylID AND reg.student_id = s.id $queryR AND reg.student_id IN ($student_ids) GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id ORDER BY reg.academic_year DESC, reg.semester DESC, reg.section_id, s.first_name, s.middle_name, s.last_name ";

                            $disResultRegistration = $this->query($studentListRegisteredSQL);

                            if (!empty($disResultRegistration)) {
                                foreach ($disResultRegistration as $dr) {
                                    $checkRegistered = ClassRegistry::init('CourseRegistration')->find('count', array(
                                        'conditions' => array(
                                            'CourseRegistration.student_id' => $dr['s']['id'],
                                            'CourseRegistration.semester' => $semester,
                                            'CourseRegistration.academic_year' => $acadamic_year
                                        )
                                    ));

                                    if ($checkRegistered) {
                                        $secName = ClassRegistry::init('Section')->find('first', array(
                                            'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                            'recursive' => -1
                                        ));

                                        //$load = $this->Student->calculateStudentLoad($dr['s']['id'], $semester, $acadamic_year, 1);
                                        $yearLevel['yearLevel'] = ClassRegistry::init('Section')->getStudentYearLevel(
                                            $dr['s']['id']
                                        )/* ['year'] */
                                        ;

                                        if (isset($dr['s']['curriculum_id']) && !empty($dr['s']['curriculum_id'])) {
                                            $curriculumDetails = $this->Student->Curriculum->find('first', array(
                                                'conditions' => array(
                                                    'Curriculum.id' => $dr['s']['curriculum_id'],
                                                ),
                                                'fields' => array(
                                                    'id',
                                                    'name',
                                                    'type_credit',
                                                    'english_degree_nomenclature',
                                                    'minimum_credit_points',
                                                    'specialization_english_degree_nomenclature',
                                                ),
                                                'recursive' => -1,
                                            ));
                                        } else {
                                            $curriculumDetails = array();
                                        }

                                        $student['Campus'] = ClassRegistry::init('Campus')->field(
                                            'Campus.name',
                                            array('Campus.id' => $value['College']['campus_id'])
                                        );
                                        $student['College'] = $value['College']['name'];
                                        $student['Department'] = $value['Department']['name'];
                                        $student['Program'] = $programs[$dr['s']['program_id']];
                                        $student['ProgramType'] = $programTypes[$dr['s']['program_type_id']];
                                        $student['AcademicYear'] = $yearLevel['yearLevel']['academicyear'];
                                        $student['YearLevel'] = $yearLevel['yearLevel']['year'];

                                        //$mg = array_merge($dr['s'], $load, $curriculumDetails, $student/* , $secName */);

                                        $mg = array_merge($dr['s'], $curriculumDetails, $student);

                                        $studentListRegistered[$value['College']['name'] /* . '~' . $value['Department']['name'] . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['reg']['academic_year'] . '~' . $dr['reg']['semester'] */][$count] = $mg;
                                        $count++;
                                    }
                                }
                            }

                            $studentListRegisteredSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering
            $college_id = array();
            $colleges = array();

            if (isset($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.id' => $college_ids[1], 'College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                } elseif ($department_id == 0) {
                    $college_id = $this->Student->College->find(
                        'list',
                        array(
                            'conditions' => array('College.active' => 1),
                            'fields' => array('College.id', 'College.id')
                        )
                    );
                }
            }

            if (!empty($college_id)) {
                //debug($college_id);
                $secQueryYD = '';
                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
                );
                //debug($colleges);

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $secQueryYD .= ' and college_id="' . $cv['College']['id'] . '" and department_id is null and id is not null and (year_level_id is null OR year_level_id = 0 OR year_level_id = "")';

                        $collegeID = $cv['College']['id'];

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')
                        ));

                        debug(count($secstulist));
                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.program_id, s.program_type_id, s.curriculum_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated, s.email, s.email_alternative, s.phone_mobile
						FROM students AS s, course_registrations AS reg
						WHERE s.college_id = $collegeID AND (reg.year_level_id IS NULL OR reg.year_level_id = '' OR reg.year_level_id = 0) AND reg.student_id = s.id $queryR AND reg.student_id IN ($student_ids) GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id ORDER BY reg.academic_year DESC, reg.semester DESC, reg.section_id, s.first_name, s.middle_name, s.last_name ";

                        $disResultRegistration = $this->query($studentListRegisteredSQL);

                        if (!empty($disResultRegistration)) {
                            foreach ($disResultRegistration as $dr) {
                                $checkRegistered = ClassRegistry::init('CourseRegistration')->find('count', array(
                                    'conditions' => array(
                                        'CourseRegistration.student_id' => $dr['s']['id'],
                                        'CourseRegistration.semester' => $semester,
                                        'CourseRegistration.academic_year' => $acadamic_year
                                    )
                                ));

                                if ($checkRegistered) {
                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                        'recursive' => -1
                                    ));

                                    //$load = $this->Student->calculateStudentLoad($dr['s']['id'], $semester, $acadamic_year, 1);
                                    //debug($load);

                                    $curriculumDetails['Curriculum'] = array();

                                    $student['Campus'] = ClassRegistry::init('Campus')->field(
                                        'Campus.name',
                                        array('Campus.id' => $cv['College']['campus_id'])
                                    );
                                    $student['College'] = $cv['College']['name'];
                                    $student['Department'] = $secName['Section']['name'] . ' - ' . ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman');
                                    $student['Program'] = $programs[$dr['s']['program_id']];
                                    $student['ProgramType'] = $programTypes[$dr['s']['program_type_id']];
                                    $student['AcademicYear'] = $secName['Section']['academicyear'];
                                    $student['YearLevel'] = ($secName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');

                                    //$mg = array_merge($dr['s'], $dr['reg'], $load);
                                    $mg = array_merge($dr['s'], $curriculumDetails, $student);

                                    $studentListRegistered[$cv['College']['name'] /* . '~' . ' Pre/Fresh' . '~' . $programs[$dr['s']['program_id']] . '~' . $programTypes[$dr['s']['program_type_id']] . '~' . $secName['Section']['name'] . '~' . $dr['reg']['academic_year']  . '~' . $dr['reg']['semester'] */][$count] = $mg;
                                    $count++;
                                }
                            }
                        }

                        //debug($studentListRegistered);
                        $studentListRegisteredSQL = '';
                    }
                }
            }
        }
        return $studentListRegistered;
    }

    public function getStudentEnrolmentForHemis(
        $acadamic_year = null,
        $semester = null,
        $program_id = 0,
        $program_type_id = 0,
        $department_id = null,
        $sex = 'all',
        $year_level_id = null,
        $region_id = null,
        $freshman = 0,
        $exclude_graduated = '',
        $only_with_complete_data = 0
    ) {

        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        $get_freshman_second_semester_grades_for_department_assiged = 0;

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $query = '';
        $secQueryIn = ' id is not null ';
        $college_id = array();

        // $query .= ' and stexam.academic_status_id !=4';

        if (!empty($region_id) && $region_id > 0) {
            $query .= ' and s.region_id=' . $region_id . '';
        }

        if (!empty($exclude_graduated) && $exclude_graduated == 1) {
            $query .= ' and s.graduated = 0';
        }

        if ($only_with_complete_data && $freshman) {
            $query .= ' and s.student_national_id is not null ';
        } elseif ($only_with_complete_data) {
            $query .= ' and s.curriculum_id is not null and s.student_national_id is not null ';
        }

        if (!empty($sex) && $sex != "all") {
            $query .= ' and s.gender="' . $sex . '"';
        }

        if (!empty($program_id)) {
            if (is_array($program_id)) {
                $programs_comma_quoted = "'" . implode("', '", $program_id) . "'";
                $query .= ' and s.program_id IN (' . $programs_comma_quoted . ')';
                $secQueryIn .= ' and program_id IN (' . $programs_comma_quoted . ')';
            } elseif ($program_id != 0) {
                $query .= ' and s.program_id = ' . $program_id . '';
                $secQueryIn .= ' and program_id =' . $program_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_id = 0';
                $secQueryIn .= ' and program_id = 0';
            }
        }

        if (!empty($program_type_id)) {
            if (is_array($program_type_id)) {
                $program_types_comma_quoted = "'" . implode("', '", $program_type_id) . "'";
                $query .= ' and s.program_type_id IN (' . $program_types_comma_quoted . ')';
                $secQueryIn .= ' and program_type_id IN (' . $program_types_comma_quoted . ')';
            } elseif ($program_type_id != 0) {
                $query .= ' and s.program_type_id = ' . $program_type_id . '';
                $secQueryIn .= ' and program_type_id = ' . $program_type_id . '';
            } else {
                // prevent any access
                $query .= ' and s.program_type_id = 0';
                $secQueryIn .= ' and program_type_id = 0';
            }
        }

        /* if (!empty($acadamic_year)) {
            //$query .= ' and sec.academicyear="'.$acadamic_year.'"';
            $secQueryIn .= ' and academicyear="' . $acadamic_year . '"';
        } */

        if (isset($department_id) && !empty($department_id)) {
            $college_ids = explode('~', $department_id);
            if (count($college_ids) > 1) {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.college_id' => $college_ids[1], 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
                $college_id[$college_ids[1]] = $college_ids[1];
            } else {
                $departments = $this->Student->Department->find(
                    'all',
                    array(
                        'conditions' => array('Department.id' => $department_id, 'Department.active' => 1),
                        'contain' => array('College', 'YearLevel')
                    )
                );
            }
        } else {
            $departments = $this->Student->Department->find(
                'all',
                array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel'))
            );
        }

        if (isset($acadamic_year) && !empty($acadamic_year)) {
            if (!empty($semester) && $semester == 'I' && !empty($year_level_id) && $year_level_id == '2nd' && ((!empty($program_id) && ((!is_array(
                                $program_id
                            ) && $program_id == PROGRAM_UNDEGRADUATE)) || (is_array($program_id) && count(
                                $program_id
                            ) == 1 && in_array(
                                PROGRAM_UNDEGRADUATE,
                                $program_id
                            ))) && (!empty($program_type_id) && ((!is_array(
                                $program_type_id
                            ) && $program_type_id == PROGRAM_TYPE_REGULAR)) || (is_array($program_type_id) && count(
                                $program_type_id
                            ) == 1 && in_array(PROGRAM_TYPE_REGULAR, $program_type_id))))) {
                debug($year_level_id);
                $current_acadamic_year = $acadamic_year;
                $ac_year_exploded = explode('/', $acadamic_year);
                $acadamic_year = ($ac_year_exploded[0] - 1) . '/' . ($ac_year_exploded[1] - 1);
                debug($acadamic_year);
                $get_freshman_second_semester_grades_for_department_assiged = 1;
                $semester = 'II';
            }

            $query .= ' and stexam.academic_year="' . $acadamic_year . '"';

            if ($get_freshman_second_semester_grades_for_department_assiged) {
                $secQueryIn .= ' and academicyear="' . $current_acadamic_year . '"';
            } else {
                $secQueryIn .= ' and academicyear="' . $acadamic_year . '"';
            }
        }

        if (isset($semester) && !empty($semester)) {
            $query .= ' and stexam.semester="' . $semester . '"';
        }

        $studentResultsHemis = array();

        $secQueryYD = '';
        $count = 0;

        if ($freshman == 1) {
            $departments = array();
        }

        if ($freshman == 0) {
            //$query .= ' and s.curriculum_id is not null ';
            if (!empty($departments)) {
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

                    foreach ($yearLevel as $ykey => $yvalue) {
                        $secQueryYD .= ' and year_level_id="' . $yvalue['id'] . '"';
                        $secQueryYD .= ' and department_id="' . $value['Department']['id'] . '"';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'order' => array(
                                'StudentsSection.id DESC',
                                'StudentsSection.modified DESC',
                                'StudentsSection.section_id DESC'
                            )
                        ));

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);

                        $student_ids = implode(", ", $x);

                        $studentResultsHemisSQL = "SELECT DISTINCT s.studentnumber, s.id , s.first_name, s.middle_name, s.last_name, s.gender, s.department_id, s.college_id, s.accepted_student_id, s.region_id, s.program_id, s.program_type_id, s.curriculum_id, s.graduated, s.academicyear, s.student_national_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year
						FROM students AS s, student_exam_statuses AS stexam
						WHERE stexam.student_id = s.id $query and stexam.student_id in ($student_ids) order by stexam.academic_year DESC, stexam.semester  DESC, s.first_name ASC, s.middle_name ASC";

                        $disResult = $this->query($studentResultsHemisSQL);

                        if ($get_freshman_second_semester_grades_for_department_assiged) {
                            $acadamic_year = $current_acadamic_year;
                            $semester = 'I';
                        }

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $departmentStudyPogramID = ClassRegistry::init('Curriculum')->field(
                                    'Curriculum.department_study_program_id',
                                    array('Curriculum.id' => $dr['s']['curriculum_id'])
                                );

                                if ($only_with_complete_data && is_null($departmentStudyPogramID)) {
                                    continue;
                                }

                                $secName = ClassRegistry::init('Section')->find('first', array(
                                    'conditions' => array('Section.id' => $secstulist[$dr['s']['id']]),
                                    'contain' => array(
                                        'YearLevel' => array('fields' => array('id', 'name'))
                                    ),
                                    'recursive' => -1
                                ));

                                //debug($secName);

                                $student = array();
                                //$student['AcademicYear'] = $dr['stexam']['academic_year'];
                                //$student['Semester'] = $dr['stexam']['semester'];
                                $student['Section'] = $secName['Section']['name'];
                                $student['YearLevel'] = mb_substr($secName['YearLevel']['name'], 0, 1);

                                $student['Region'] = ClassRegistry::init('Region')->field(
                                    'Region.name',
                                    array('Region.id' => $dr['s']['region_id'])
                                );


                                //debug(substr($secName['YearLevel']['name'], 0, 1)); // for the single-byte encoded (binary) strings
                                //debug($secName['YearLevel']['name'][0]); // for the single-byte encoded (binary) strings
                                //debug(mb_substr($secName['YearLevel']['name'], 0, 1)); // for the multi-byte encoded strings, such as UTF-8

                                $institutionCodes = ClassRegistry::init('Department')->find('first', array(
                                    'conditions' => array(
                                        'Department.id' => $dr['s']['department_id']
                                    ),
                                    'contain' => array(
                                        'College' => array(
                                            'fields' => array(
                                                'College.id',
                                                'College.name',
                                                'College.institution_code',
                                            ),
                                            'Campus' => array(
                                                'fields' => array(
                                                    'Campus.id',
                                                    'Campus.name',
                                                    'Campus.campus_code',
                                                ),
                                            )
                                        )
                                    ),
                                    'fields' => array(
                                        'Department.id',
                                        'Department.name',
                                        'Department.institution_code',
                                    ),
                                    'recursive' => -1
                                ));


                                /*
                                    Enrollment Types

                                    NW  New Student
                                    AS  Advanced Standing
                                    CN  Continuing
                                    TR  Transfer
                                    EX  Exchange
                                    ITR Internal Transfer
                                    PR  Personal Readmission
                                    AR  Academic Readmission

                                */

                                //check for course Exemption here
                                // no need to check exemption as there is no way to calculate/generate status in SMiS, the data can be sent to MoE from the previous University.


                                $last_registration = ClassRegistry::init('CourseRegistration')->find('first', array(
                                    'conditions' => array(
                                        'CourseRegistration.student_id' => $dr['s']['id']
                                    ),
                                    'order' => array(
                                        'CourseRegistration.academic_year' => 'DESC',
                                        'CourseRegistration.semester' => 'DESC',
                                        'CourseRegistration.id' => 'DESC'
                                    ),
                                    'recursive' => -1
                                ));

                                $current_registration = ClassRegistry::init('CourseRegistration')->find('first', array(
                                    'conditions' => array(
                                        'CourseRegistration.student_id' => $dr['s']['id'],
                                        'CourseRegistration.academic_year' => $acadamic_year,
                                        'CourseRegistration.semester' => $semester
                                    ),
                                    'order' => array(
                                        'CourseRegistration.academic_year' => 'DESC',
                                        'CourseRegistration.semester' => 'DESC',
                                        'CourseRegistration.id' => 'DESC'
                                    ),
                                    'recursive' => -1
                                ));

                                //debug($last_registration['CourseRegistration']['academic_year']);
                                //debug($last_registration['CourseRegistration']['semester']);

                                $student_status_by_selected_acy_sem = ClassRegistry::init('StudentExamStatus')->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'StudentExamStatus.student_id' => $dr['s']['id'],
                                            'StudentExamStatus.academic_year' => $acadamic_year,
                                            'StudentExamStatus.semester' => $semester,
                                            'StudentExamStatus.academic_status_id IS NOT NULL'
                                        ),
                                        'order' => array(
                                            'StudentExamStatus.id' => 'DESC',
                                            'StudentExamStatus.modified' => 'DESC'
                                        )
                                    )
                                );

                                $last_student_status = array();

                                if ($student_status_by_selected_acy_sem) {
                                    $last_student_status = ClassRegistry::init('StudentExamStatus')->find(
                                        'first',
                                        array(
                                            'conditions' => array(
                                                'StudentExamStatus.student_id' => $dr['s']['id'],
                                                'StudentExamStatus.academic_year' => $acadamic_year,
                                                'StudentExamStatus.semester' => $semester,
                                                'StudentExamStatus.academic_status_id IS NOT NULL'
                                            ),
                                            'order' => array(
                                                'StudentExamStatus.id' => 'DESC',
                                                'StudentExamStatus.modified' => 'DESC'
                                            ),
                                            'recursive' => -1
                                        )
                                    );
                                } else {
                                    $last_student_status = ClassRegistry::init('StudentExamStatus')->find(
                                        'first',
                                        array(
                                            'conditions' => array(
                                                'StudentExamStatus.student_id' => $dr['s']['id'],
                                                //'StudentExamStatus.academic_year' => $acadamic_year,
                                                //'StudentExamStatus.semester' => $semester,
                                                'StudentExamStatus.academic_status_id IS NOT NULL'
                                            ),
                                            'order' => array(
                                                'StudentExamStatus.id' => 'DESC',
                                                'StudentExamStatus.created' => 'DESC'
                                            ),
                                            'recursive' => -1
                                        )
                                    );
                                }

                                //debug($last_student_status);

                                $readmitted = array();

                                if (!empty($last_student_status)) {
                                    $possibleReadmissionYears = $this->getAcademicYearRange(
                                        $last_student_status['StudentExamStatus']['academic_year'],
                                        $acadamic_year
                                    );

                                    $readmitted = ClassRegistry::init('Readmission')->find('first', array(
                                        'conditions' => array(
                                            'Readmission.student_id' => $dr['s']['id'],
                                            'Readmission.registrar_approval' => 1,
                                            'Readmission.academic_commision_approval' => 1,
                                            'Readmission.academic_year' => $possibleReadmissionYears,
                                            //'Readmission.semester' => $last_student_status['StudentExamStatus']['semester'],
                                            /* 'OR' => array(
                                                'Readmission.academic_year' => $last_student_status['StudentExamStatus']['academic_year'],
                                                'Readmission.semester' => $last_student_status['StudentExamStatus']['semester'],
                                                'Readmission.registrar_approval_date' > $last_student_status['StudentExamStatus']['modified'],
                                                'Readmission.modified' > $last_student_status['StudentExamStatus']['modified'],
                                                'Readmission.academic_year' => $secName['Section']['academicyear'],
                                            ) */
                                        ),
                                        'order' => array(
                                            'Readmission.academic_year' => 'DESC',
                                            'Readmission.semester' => 'DESC',
                                            'Readmission.modified' => 'DESC'
                                        ),
                                    ));
                                }

                                /* $exempted_courses_of_student = ClassRegistry::init('CourseExemption')->find('all', array(
                                    'conditions' => array(
                                        'CourseExemption.student_id' => $dr['s']['id'],
                                        'CourseExemption.department_accept_reject' => 1,
                                        'CourseExemption.registrar_confirm_deny' => 1,
                                    ),
                                    'fields' => array(
                                        'CourseExemption.id',
                                        'CourseExemption.course_id',
                                        'CourseExemption.request_date',
                                        'CourseExemption.student_id',
                                        'CourseExemption.created',
                                    ),
                                    'recursive' => -1
                                ));

                                if (!empty($exempted_courses_of_student)) {
                                    debug($exempted_courses_of_student);

                                    $first_registration = ClassRegistry::init('CourseRegistration')->find('first', array(
                                        'conditions' => array(
                                            'CourseRegistration.student_id' => $dr['s']['id']
                                        ),
                                        'order' => array('CourseRegistration.created ASC'),
                                        'recursive' => -1
                                    ));

                                    debug($first_registration);

                                    if ($first_registration['CourseRegistration']['semester'] == $last_registration['CourseRegistration']['semester'] && $first_registration['CourseRegistration']['academic_year'] == $last_registration['CourseRegistration']['academic_year'] ) {

                                    }

                                    // check in which semester the student have exemption given published course created against exempted ccourse approval or by some kind

                                }  */

                                if (count(
                                        $readmitted
                                    ) && (isset($last_student_status['StudentExamStatus']) && $last_student_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID)) {
                                    $student['EnrollmentType'] = 'AR';
                                    $is_student_readmitted = 1;
                                } elseif (count($readmitted)) {
                                    $student['EnrollmentType'] = 'PR';
                                    $is_student_readmitted = 1;
                                } elseif ($secName['YearLevel']['name'] == '1st' && $current_registration['CourseRegistration']['semester'] == 'I') {
                                    $student['EnrollmentType'] = 'NW';
                                } elseif ($dr['s']['program_type_id'] == PROGRAM_TYPE_ADVANCE_STANDING) {
                                    $student['EnrollmentType'] = 'AS';
                                }/*  else if (!empty($exempted_courses_of_student)) {
                                    // this is temporary
                                    $student['EnrollmentType'] = 'TR';
                                } */ else {
                                    $student['EnrollmentType'] = 'CN';
                                }

                                /*
                                    Program Modality

                                    RG  Regular =>  1, 4
                                    EX  Extension/Weekend   2, 8
                                    SU  Summer => 3
                                    DIS Distance => 6
                                    WS  Winter In-service
                                    DEX Daytime Extension   Part-time   5
                                    ONL Online  => E-learning => 7

                                */

                                // Replaced by DepartmentStudyPrograms below **********************

                                /* if ($dr['s']['program_type_id'] == PROGRAM_TYPE_REGULAR || $dr['s']['program_type_id'] == PROGRAM_TYPE_ADVANCE_STANDING) {
                                    $student['ProgramModality'] = 'RG';
                                } else if ($dr['s']['program_type_id'] == PROGRAM_TYPE_EVENING || $dr['s']['program_type_id'] == PROGRAM_TYPE_WEEKEND) {
                                    $student['ProgramModality'] = 'EX';
                                } else if ($dr['s']['program_type_id'] == PROGRAM_TYPE_SUMMER) {
                                    $student['ProgramModality'] = 'SU';
                                } else if ($dr['s']['program_type_id'] == PROGRAM_TYPE_DISTANCE) {
                                    $student['ProgramModality'] = 'DIS';
                                } else if ($dr['s']['program_type_id'] == PROGRAM_TYPE_PART_TIME) {
                                    $student['ProgramModality'] = 'DEX';
                                } else if ($dr['s']['program_type_id'] == PROGRAM_TYPE_ON_LINE) {
                                    $student['ProgramModality'] = 'ONL';
                                }  */

                                // Temporary Target Qualification until curriculum is integrated with study_programs

                                // Replaced by DepartmentStudyPrograms below **********************

                                /* if ($dr['s']['program_id'] == PROGRAM_UNDEGRADUATE) {
                                    $student['TargetQualification'] = 'BCH';
                                } else if ($dr['s']['program_id'] == PROGRAM_POST_GRADUATE ) {
                                    // && $dr['s']['college_id'] != 3
                                    // pg students in other colleges exept CMHS
                                    $student['ProgramModality'] = 'MST';
                                } else if ($dr['s']['program_id'] == PROGRAM_PhD) {
                                    $student['TargetQualification'] = 'PHD';
                                } else if ($dr['s']['program_id'] == PROGRAM_PGDT) {
                                    $student['TargetQualification'] = 'PGD';
                                } */

                                //$departmentStudyPogramID = ClassRegistry::init('Curriculum')->field('Curriculum.department_study_program_id', array('Curriculum.id' => $dr['s']['curriculum_id']));

                                if (!is_null($departmentStudyPogramID) && is_numeric($departmentStudyPogramID)) {
                                    $curriculumStudyprogramDetails = ClassRegistry::init(
                                        'DepartmentStudyProgram'
                                    )->find('first', array(
                                        'conditions' => array(
                                            'DepartmentStudyProgram.id' => $departmentStudyPogramID
                                        ),
                                        'contain' => array(
                                            'StudyProgram' => array('fields' => array('id', 'code')),
                                            'ProgramModality' => array('fields' => array('id', 'code')),
                                            'Qualification' => array('fields' => array('id', 'code')),
                                        ),
                                        'fields' => array(
                                            'DepartmentStudyProgram.id',
                                            'DepartmentStudyProgram.study_program_id'
                                        )
                                    ));

                                    if (!empty($curriculumStudyprogramDetails)) {
                                        $student['StudyProgram'] = $curriculumStudyprogramDetails['StudyProgram']['code'];
                                        $student['ProgramModality'] = $curriculumStudyprogramDetails['ProgramModality']['code'];
                                        $student['TargetQualification'] = $curriculumStudyprogramDetails['Qualification']['code'];
                                    }
                                } else {
                                    $student['StudyProgram'] = '';
                                    $student['ProgramModality'] = '';
                                    $student['TargetQualification'] = '';
                                }

                                /*
                                    Foreign Programs

                                    BAG Bilateral Agreement
                                    REF Refugees
                                    SCH Scholarships

                                */

                                $region_id = ClassRegistry::init('AcceptedStudent')->field(
                                    'AcceptedStudent.region_id',
                                    array('AcceptedStudent.id' => $dr['s']['accepted_student_id'])
                                );
                                $country_id = ClassRegistry::init('Region')->field(
                                    'Region.country_id',
                                    array('Region.id' => $region_id)
                                );

                                if ($country_id && $country_id != COUNTRY_ID_OF_ETHIOPIA) {
                                    $student['ForeignProgram'] = 'SCH';
                                } else {
                                    $student['ForeignProgram'] = '';
                                }

                                if ($dr['s']['program_id'] == PROGRAM_UNDEGRADUATE && $dr['s']['program_type_id'] == PROGRAM_TYPE_REGULAR && $country_id && $country_id == COUNTRY_ID_OF_ETHIOPIA) {
                                    $student['CostSharingLoan'] = 'Y';
                                } else {
                                    $student['CostSharingLoan'] = 'N';
                                }

                                $minimum_credit_points_required = ClassRegistry::init('Curriculum')->field(
                                    'Curriculum.minimum_credit_points',
                                    array('Curriculum.id' => $dr['s']['curriculum_id'])
                                );

                                if ($minimum_credit_points_required) {
                                    $student['RequiredCredit'] = $minimum_credit_points_required;
                                } else {
                                    $student['RequiredCredit'] = 'N/A';
                                }

                                if (!is_null($dr['s']['curriculum_id'])) {
                                    $student['RequiredAcademicPeriods'] = ClassRegistry::init('Course')->find(
                                        'count',
                                        array(
                                            'conditions' => array(
                                                'Course.curriculum_id' => $dr['s']['curriculum_id']
                                            ),
                                            'order' => array(
                                                'Course.year_level_id' => 'DESC',
                                                'Course.semester' => 'DESC'
                                            ),
                                            'group' => array('Course.year_level_id', 'Course.semester'),
                                            'fields' => array('Course.id'),
                                            'recursive' => -1
                                        )
                                    );
                                } else {
                                    $student['RequiredAcademicPeriods'] = 'N/A';
                                }

                                $student['CurrentRegistredCredit'] = 0;
                                $student['CumulativeRegistredCredit'] = 0;
                                $student['CumulativeGPA'] = 0;

                                //$student['CurrentRegistredCredit'] = $this->Student->calculateStudentLoad($dr['s']['id'], $semester, $acadamic_year, 0);
                                $student['CurrentRegistredCredit'] = $this->Student->calculateCumulativeStudentRegistredAddedCredit(
                                    $dr['s']['id'],
                                    0,
                                    $semester,
                                    $acadamic_year,
                                    0
                                );
                                $student['CumulativeRegistredCredit'] = $this->Student->calculateCumulativeStudentRegistredAddedCredit(
                                    $dr['s']['id'],
                                    1,
                                    $semester,
                                    $acadamic_year,
                                    0
                                );

                                if (isset($last_student_status['StudentExamStatus'])) {
                                    $student['CumulativeGPA'] = $last_student_status['StudentExamStatus']['cgpa'];
                                } else {
                                    $student['CumulativeGPA'] = 'N/A';
                                }

                                if ($dr['s']['program_id'] == PROGRAM_UNDEGRADUATE) {
                                    if ($dr['s']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                                        if ($country_id && $country_id == COUNTRY_ID_OF_ETHIOPIA) {
                                            $student['Sponsorship'] = SPONSORED_BY_FEDERAL_GOVERNMENT;
                                            $student['DormitoryServiceType'] = 'K';
                                        } else {
                                            $student['Sponsorship'] = SPONSORED_BY_OTHER;
                                        }

                                        if ($last_registration['CourseRegistration']['cafeteria_consumer'] == 1) {
                                            $student['FoodServiceType'] = 'K';
                                        } elseif ($last_registration['CourseRegistration']['cafeteria_consumer'] == 0) {
                                            $student['FoodServiceType'] = 'C';
                                        } else {
                                            $student['FoodServiceType'] = 'K';
                                        }

                                        if ($student['CostSharingLoan'] == 'Y' && $secName['Section']['college_id'] != 3 && $dr['s']['college_id'] != 3 && isset($current_registration['CourseRegistration']['semester']) && !empty($current_registration['CourseRegistration']['semester'])) {
                                            if ($current_registration['CourseRegistration']['semester'] == 'I') {
                                                $student['CurrentCostSharing'] = round(
                                                    (NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC / 2),
                                                    2
                                                );
                                            } elseif ($current_registration['CourseRegistration']['semester'] == 'II') {
                                                $student['CurrentCostSharing'] = NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC;
                                            }

                                            if ($student['YearLevel'] == 1) {
                                                $student['AccumulatedCostSharing'] = $student['CurrentCostSharing'];
                                            } else {
                                                $student['AccumulatedCostSharing'] = ((($student['YearLevel'] - 1) * NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC) + $student['CurrentCostSharing']);
                                            }
                                        } else {
                                            //temporary
                                            $student['CurrentCostSharing'] = '';
                                            $student['AccumulatedCostSharing'] = '';
                                        }
                                    } elseif ($dr['s']['program_type_id'] == PROGRAM_TYPE_ADVANCE_STANDING || $dr['s']['program_type_id'] == PROGRAM_TYPE_PART_TIME || $dr['s']['program_type_id'] == PROGRAM_TYPE_SUMMER) {
                                        $student['Sponsorship'] = SPONSORED_BY_EMPLOYER;
                                    } else {
                                        $student['Sponsorship'] = SPONSORED_BY_SELF_PRIVATE;
                                    }
                                } elseif ($dr['s']['program_id'] == PROGRAM_POST_GRADUATE) {
                                    if ($dr['s']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                                        if ($country_id && $country_id == COUNTRY_ID_OF_ETHIOPIA) {
                                            $student['Sponsorship'] = SPONSORED_BY_REGIONAL_GOVERNMENT;
                                        } else {
                                            $student['Sponsorship'] = SPONSORED_BY_OTHER;
                                        }
                                    } elseif ($dr['s']['program_type_id'] == PROGRAM_TYPE_PART_TIME || $dr['s']['program_type_id'] == PROGRAM_TYPE_SUMMER) {
                                        $student['Sponsorship'] = SPONSORED_BY_EMPLOYER;
                                    } else {
                                        $student['Sponsorship'] = SPONSORED_BY_SELF_PRIVATE;
                                    }

                                    if ($dr['s']['program_type_id'] == PROGRAM_TYPE_REGULAR || $dr['s']['program_type_id'] == PROGRAM_TYPE_SUMMER) {
                                        $student['DormitoryServiceType'] = 'K';
                                    }
                                } elseif ($dr['s']['program_id'] == PROGRAM_PhD || $dr['s']['program_id'] == PROGRAM_PGDT) {
                                    if ($dr['s']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                                        if ($country_id && $country_id == COUNTRY_ID_OF_ETHIOPIA) {
                                            $student['Sponsorship'] = SPONSORED_BY_EMPLOYER;
                                        } else {
                                            $student['Sponsorship'] = SPONSORED_BY_OTHER;
                                        }
                                    }

                                    $student['DormitoryServiceType'] = 'K';
                                }

                                if ($get_freshman_second_semester_grades_for_department_assiged) {
                                    //replace the academic year and semester to the filter academic year and semester
                                    $dr['stexam']['academic_year'] = $acadamic_year;
                                    $dr['stexam']['semester'] = $semester;

                                    $mg = array_merge($dr['s'], $dr['stexam'], $institutionCodes, $student);
                                    $studentResultsHemis[$acadamic_year . '~' . $semester . '~' . $value['College']['shortname'] /* . '~' . $value['Department']['name']  */][$count] = $mg;
                                } else {
                                    // the normal implemetation
                                    $mg = array_merge($dr['s'], $dr['stexam'], $institutionCodes, $student);
                                    $studentResultsHemis[$dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] . '~' . $value['College']['shortname'] /* . '~' . $value['Department']['name']  */][$count] = $mg;
                                }

                                $count++;
                            }
                        }

                        $studentResultsHemisSQL = '';
                    }
                }
            }
        } /* else {

            $college_id = array();
            $colleges = array();

            if (isset($department_id)) {
                $college_ids = explode('~', $department_id);
                if (count($college_ids) > 1) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.id' => $college_ids[1],
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                } else if ($department_id == 0) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                }
            }

            if (isset($college_id) && !empty($college_id)) {

                $secQueryYD = '';
                $colleges = $this->Student->College->find('all', array('conditions' => array('College.id' => $college_id), 'recursive' => -1));

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {

                        $secQueryYD .= ' and college_id="' . $cv['College']['id'] . '" and department_id is null and id is not null and (year_level_id is null or year_level_id = "" or year_level_id = 0)';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'
                            )
                        ));

                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $studentResultsHemisSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.region_id, s.program_id, s.program_type_id, s.graduated, s.academicyear,s.student_national_id, stexam.academic_status_id, stexam.sgpa, stexam.cgpa, stexam.credit_hour_sum, stexam.semester, stexam.academic_year
                        FROM students AS s, student_exam_statuses AS stexam
                        WHERE stexam.student_id = s.id $query and stexam.student_id in ($student_ids) order by stexam.academic_year DESC, stexam.semester  DESC, s.first_name ASC, s.middle_name ASC";

                        $disResult = $this->query($studentResultsHemisSQL);

                        if (!empty($disResult)) {
                            foreach ($disResult as $dr) {
                                $mg = array_merge($dr['s'], $dr['stexam']);
                                $studentResultsHemis[$dr['stexam']['academic_year'] . '~' . $dr['stexam']['semester'] . '~' .  $cv['College']['shortname']][$count] = $mg;
                                $count++;
                            }
                        }
                        $studentResultsHemisSQL = '';
                    }
                }
            }
        } */

        return $studentResultsHemis;
    }

    public function getRegisteredStudentListForAddDrop(
        $acadamic_year = null,
        $semester = null,
        $program_id = null,
        $program_type_id = null,
        $department_id = null,
        $year_level_id = null,
        $freshman = 0,
        $studentnumber = ''
    ) {

        if (empty($acadamic_year) && empty($semester)) {
            return array();
        }

        $queryR = ' and s.graduated = 0 ';
        $secQueryIn = ' id is not null and archive = 0 ';

        if (isset($program_id) && !empty($program_id)) {
            $prog_ids = "'" . implode("', '", $program_id) . "'";
            $queryR .= ' and s.program_id IN (' . $prog_ids . ')';
            $secQueryIn .= ' and program_id IN (' . $prog_ids . ')';
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $prog_type_ids = "'" . implode("', '", $program_type_id) . "'";
            $queryR .= ' and s.program_type_id IN (' . $prog_type_ids . ')';
            $secQueryIn .= ' and program_type_id IN (' . $prog_type_ids . ')';
        }


        if (isset($studentnumber) && !empty($studentnumber)) {
            $queryR .= ' and s.studentnumber LIKE "' . (trim($studentnumber)) . '"';
        }

        if (isset($department_id) && !empty($department_id)) {
            if (!$freshman) {
                $departments = $this->Student->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            }
        } else {
            $departments = $this->Student->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel')
            ));
        }

        if (!empty($acadamic_year)) {
            $secQueryIn .= ' and academicyear LIKE "' . $acadamic_year . '"';
            $queryR .= ' and reg.academic_year LIKE "' . $acadamic_year . '"';
        }

        if (!empty($semester)) {
            $queryR .= ' and reg.semester="' . $semester . '"';
        }

        if ($freshman == 1) {
            $departments = array();
        }

        $secQueryYD = '';
        $count = 0;

        $studentListRegistered = array();

        if ($freshman == 0) {
            if (!empty($departments)) {
                foreach ($departments as $key => $value) {
                    //$college_id[$value['Department']['college_id']] = $value['Department']['college_id'];
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
                            $ylID = $yvalue['id'];

                            $secQueryYD .= ' and year_level_id="' . $ylID . '"';
                            $secQueryYD .= ' and department_id="' . $value['Department']['id'] . '"';

                            $secstulist = $this->Student->StudentsSection->find('list', array(
                                'conditions' => array(
                                    'StudentsSection.archive' => 0,
                                    "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD)",
                                ),
                                'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                                'order' => array(
                                    'StudentsSection.id' => 'DESC',
                                    'StudentsSection.modified' => 'DESC',
                                    'StudentsSection.section_id' => 'DESC'
                                )
                            ));

                            $secQueryYD = '';

                            if (empty($secstulist)) {
                                continue;
                            }

                            $x = array_keys($secstulist);
                            $student_ids = implode(", ", $x);

                            $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.program_id, s.program_type_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated, s.admissionyear, s.curriculum_id, s.department_id, s.college_id
							FROM students AS s, course_registrations AS reg
							WHERE reg.year_level_id = $ylID and reg.student_id = s.id $queryR and reg.student_id in ($student_ids) GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id order by reg.section_id, s.first_name";

                            $disResultRegistration = $this->query($studentListRegisteredSQL);

                            if (!empty($disResultRegistration)) {
                                foreach ($disResultRegistration as $dr) {
                                    $checkRegistered = ClassRegistry::init('CourseRegistration')->find('count', array(
                                        'conditions' => array(
                                            'CourseRegistration.student_id' => $dr['s']['id'],
                                            'CourseRegistration.semester' => $semester,
                                            'CourseRegistration.academic_year' => $acadamic_year,
                                            'OR' => array(
                                                'CourseRegistration.year_level_id is not null',
                                                'CourseRegistration.year_level_id != ""',
                                                'CourseRegistration.year_level_id != 0',
                                            )
                                        )
                                    ));

                                    if ($checkRegistered) {
                                        $secName = ClassRegistry::init('Section')->find('first', array(
                                            'conditions' => array(
                                                'Section.id' => $dr['reg']['section_id'],
                                                'Section.department_id is not null',
                                            ),
                                            'contain' => array(
                                                'YearLevel' => array('fields' => array('id', 'name'))
                                            ),
                                            'recursive' => -1
                                        ));


                                        $last_student_status = ClassRegistry::init('StudentExamStatus')->find(
                                            'first',
                                            array(
                                                'conditions' => array(
                                                    'StudentExamStatus.student_id' => $dr['s']['id'],
                                                ),
                                                'contain' => array(
                                                    'AcademicStatus' => array('id', 'name', 'computable')
                                                ),
                                                'order' => array(
                                                    'StudentExamStatus.academic_year' => 'DESC',
                                                    'StudentExamStatus.semester' => 'DESC',
                                                ),
                                                'recursive' => -1
                                            )
                                        );

                                        $student = array();
                                        $student['Student'] = $dr['s'];
                                        $student['Student']['full_name'] = $dr['s']['first_name'] . ' ' . $dr['s']['middle_name'] . ' ' . $dr['s']['last_name'];
                                        $student['Department']['name'] = $this->Student->Department->field(
                                            'Department.name',
                                            array('Department.id' => $dr['s']['department_id'])
                                        );

                                        if (isset($dr['s']['curriculum_id']) && is_numeric(
                                                $dr['s']['curriculum_id']
                                            ) && $dr['s']['curriculum_id']) {
                                            $studentCurriculum = $this->Student->Curriculum->find('first', array(
                                                'conditions' => array('Curriculum.id' => $dr['s']['curriculum_id']),
                                                'fields' => array(
                                                    'Curriculum.id',
                                                    'Curriculum.name',
                                                    'type_credit',
                                                    'year_introduced',
                                                    'active'
                                                ),
                                                'recursive' => -1
                                            ));

                                            if (!empty($studentCurriculum)) {
                                                $student['Curriculum'] = $studentCurriculum['Curriculum'];
                                            } else {
                                                $student['Curriculum'] = array();
                                            }
                                        }

                                        $student['Program']['name'] = $this->Student->Program->field(
                                            'Program.name',
                                            array('Program.id' => $dr['s']['program_id'])
                                        );
                                        $student['ProgramType']['name'] = $this->Student->ProgramType->field(
                                            'ProgramType.name',
                                            array('ProgramType.id' => $dr['s']['program_type_id'])
                                        );
                                        $student['Registration'] = $dr['reg'];
                                        $student['LastStatus'] = $last_student_status;
                                        $student['Section'] = $secName['Section'];
                                        $student['YearLevel'] = $secName['YearLevel'];
                                        $student['Load'] = $this->Student->calculateStudentLoad(
                                            $dr['s']['id'],
                                            $semester,
                                            $acadamic_year
                                        );
                                        $student['MaxLoadAllowed'] = ClassRegistry::init(
                                            'AcademicCalendar'
                                        )->maximumCreditPerSemester($dr['s']['id']);

                                        $studentListRegistered[$count] = $student;

                                        $count++;
                                    }
                                }
                            }

                            //debug($studentListRegistered);
                            $studentListRegisteredSQL = '';
                        }
                    }
                }
            }
        } else {
            //preengineering
            $college_id = array();
            $colleges = array();

            if (isset($department_id)) {
                if ($freshman) {
                    $college_id = $this->Student->College->find('list', array(
                        'conditions' => array(
                            'College.id' => $department_id,
                            'College.active' => 1
                        ),
                        'fields' => array('College.id', 'College.id')
                    ));
                }
            }

            if (!empty($college_id)) {
                //debug($college_id);
                $secQueryYD = '';
                $colleges = $this->Student->College->find(
                    'all',
                    array('conditions' => array('College.id' => $college_id), 'recursive' => -1)
                );
                //debug($colleges);

                if (!empty($colleges)) {
                    foreach ($colleges as $ck => $cv) {
                        $secQueryYD .= ' and archive = 0 and college_id="' . $cv['College']['id'] . '" and department_id is null and (year_level_id is null OR year_level_id = 0 OR year_level_id = "")';

                        $secstulist = $this->Student->StudentsSection->find('list', array(
                            'conditions' => array(
                                'StudentsSection.archive' => 0,
                                "StudentsSection.section_id in (select id from sections where $secQueryIn $secQueryYD )"
                            ),
                            'fields' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'group' => array('StudentsSection.student_id', 'StudentsSection.section_id'),
                            'order' => array(
                                'StudentsSection.id' => 'DESC',
                                'StudentsSection.modified' => 'DESC',
                                'StudentsSection.section_id' => 'DESC'
                            )
                        ));

                        debug(count($secstulist));
                        $secQueryYD = '';

                        if (empty($secstulist)) {
                            continue;
                        }

                        $x = array_keys($secstulist);
                        $student_ids = implode(", ", $x);

                        $studentListRegisteredSQL = "SELECT DISTINCT s.studentnumber, s.id, s.first_name, s.middle_name, s.last_name, s.gender, s.program_id, s.program_type_id, reg.semester, reg.academic_year, reg.section_id, s.academicyear, s.graduated, s.admissionyear, s.curriculum_id, s.department_id, s.college_id
						FROM students AS s, course_registrations AS reg
						WHERE (reg.year_level_id is null or reg.year_level_id = '' or reg.year_level_id = 0) and reg.student_id = s.id $queryR and reg.student_id in ($student_ids) GROUP BY reg.semester, reg.academic_year, reg.section_id, reg.student_id order by reg.section_id, s.first_name";

                        $disResultRegistration = $this->query($studentListRegisteredSQL);

                        if (!empty($disResultRegistration)) {
                            foreach ($disResultRegistration as $dr) {
                                $checkRegistered = ClassRegistry::init('CourseRegistration')->find('count', array(
                                    'conditions' => array(
                                        'CourseRegistration.student_id' => $dr['s']['id'],
                                        'CourseRegistration.semester' => $semester,
                                        'CourseRegistration.academic_year' => $acadamic_year,
                                        'OR' => array(
                                            'CourseRegistration.year_level_id is null',
                                            'CourseRegistration.year_level_id = ""',
                                            'CourseRegistration.year_level_id = 0',
                                        )
                                    )
                                ));

                                if ($checkRegistered) {
                                    $secName = ClassRegistry::init('Section')->find('first', array(
                                        'conditions' => array(
                                            'Section.id' => $dr['reg']['section_id'],
                                            'Section.department_id is null',
                                        ),
                                        'contain' => array(
                                            'YearLevel' => array('fields' => array('id', 'name'))
                                        ),
                                        'recursive' => -1
                                    ));


                                    $last_student_status = ClassRegistry::init('StudentExamStatus')->find(
                                        'first',
                                        array(
                                            'conditions' => array(
                                                'StudentExamStatus.student_id' => $dr['s']['id'],
                                            ),
                                            'contain' => array(
                                                'AcademicStatus' => array('id', 'name', 'computable')
                                            ),
                                            'order' => array(
                                                'StudentExamStatus.academic_year' => 'DESC',
                                                'StudentExamStatus.semester' => 'DESC',
                                            ),
                                            'recursive' => -1
                                        )
                                    );

                                    $student = array();
                                    $student['Student'] = $dr['s'];
                                    $student['Student']['full_name'] = $dr['s']['first_name'] . ' ' . $dr['s']['middle_name'] . ' ' . $dr['s']['last_name'];
                                    $student['College']['name'] = $this->Student->College->field(
                                        'College.name',
                                        array('College.id' => $dr['s']['college_id'])
                                    );

                                    $student['Curriculum'] = array();

                                    $student['Program']['name'] = $this->Student->Program->field(
                                        'Program.name',
                                        array('Program.id' => $dr['s']['program_id'])
                                    );
                                    $student['ProgramType']['name'] = $this->Student->ProgramType->field(
                                        'ProgramType.name',
                                        array('ProgramType.id' => $dr['s']['program_type_id'])
                                    );
                                    $student['Registration'] = $dr['reg'];
                                    $student['LastStatus'] = $last_student_status;
                                    $student['Section'] = $secName['Section'];
                                    $student['YearLevel'] = $secName['YearLevel'];
                                    $student['Load'] = $this->Student->calculateStudentLoad(
                                        $dr['s']['id'],
                                        $semester,
                                        $acadamic_year
                                    );
                                    $student['MaxLoadAllowed'] = ClassRegistry::init(
                                        'AcademicCalendar'
                                    )->maximumCreditPerSemester($dr['s']['id']);

                                    $studentListRegistered[$count] = $student;
                                    $count++;
                                }
                            }
                        }

                        //debug($studentListRegistered);
                        $studentListRegisteredSQL = '';
                    }
                }
            }
        }
        return $studentListRegistered;
    }

    function regenerate_all_status_of_student_by_student_id($student_id = null, $check_within_the_week = 1)
    {

        if (!empty($student_id) && is_numeric($student_id) && $student_id > 0) {
            $statusListForDelete = array();

            if ($check_within_the_week) {
                $last_status_regenerated_date = ClassRegistry::init('StudentExamStatus')->field(
                    'StudentExamStatus.modified',
                    array('StudentExamStatus.student_id' => $student_id)
                );

                debug($last_status_regenerated_date);
                debug(date("Y-m-d 23:59:59", strtotime("-" . 1 . " week")));

                // if there is more recent status, skip regenerating status
                if ($last_status_regenerated_date > (date("Y-m-d 23:59:59", strtotime("-" . 1 . " week")))) {
                    debug($last_status_regenerated_date);
                    return 3;
                }
            }

            $alreadyGeneratedStatus = ClassRegistry::init('StudentExamStatus')->find('list', array(
                'conditions' => array(
                    'StudentExamStatus.student_id' => $student_id
                ),
                'fields' => array('StudentExamStatus.id', 'StudentExamStatus.id')
            ));

            debug($alreadyGeneratedStatus);

            if (!empty($alreadyGeneratedStatus)) {
                $statusListForDelete = $alreadyGeneratedStatus;
            }

            $course_registered = ClassRegistry::init('CourseRegistration')->find('list', array(
                'conditions' => array(
                    'CourseRegistration.student_id' => $student_id
                ),
                'order' => array(
                    'CourseRegistration.academic_year' => 'ASC',
                    'CourseRegistration.semester' => 'ASC',
                    'CourseRegistration.id' => 'ASC'
                ),
                'fields' => array('CourseRegistration.published_course_id', 'CourseRegistration.published_course_id'),
                'recursive' => -1
            ));

            $course_added = ClassRegistry::init('CourseAdd')->find('list', array(
                'conditions' => array(
                    'CourseAdd.student_id' => $student_id
                ),
                'order' => array(
                    'CourseAdd.academic_year' => 'ASC',
                    'CourseAdd.semester' => 'ASC',
                    'CourseAdd.id' => 'ASC'
                ),
                'fields' => array('CourseAdd.published_course_id', 'CourseAdd.published_course_id'),
                'recursive' => -1
            ));

            $listofputaken = $course_registered + $course_added;

            $listPublishedCourseTakenBySection = ClassRegistry::init('PublishedCourse')->find('all', array(
                'conditions' => array(
                    'PublishedCourse.id' => $listofputaken
                ),
                'order' => array('PublishedCourse.academic_year' => 'ASC', 'PublishedCourse.semester' => 'ASC'),
                'recursive' => -1
            ));

            if (!empty($statusListForDelete)) {
                //debug($statusListForDelete);
                ClassRegistry::init('StudentExamStatus')->deleteAll(
                    array('StudentExamStatus.id' => $statusListForDelete),
                    false
                );
            }

            $statusgenerated = false;

            if (!empty($listPublishedCourseTakenBySection)) {
                foreach ($listPublishedCourseTakenBySection as $value) {
                    $checkIfStatusIsGenerated = ClassRegistry::init('Student')->StudentExamStatus->find('count', array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $student_id,
                            'StudentExamStatus.academic_year' => $value['PublishedCourse']['academic_year'],
                            'StudentExamStatus.semester' => $value['PublishedCourse']['semester']
                        )
                    ));

                    if (!$checkIfStatusIsGenerated) {
                        $statusgenerated = $this->updateAcdamicStatusByPublishedCourseOfStudent(
                            $value['PublishedCourse']['id'],
                            $student_id
                        );
                        if ($statusgenerated) {
                            //debug($value);
                        } else {
                            //debug($value);
                        }
                    }
                }
            }

            return $statusgenerated;
        }
    }
}
