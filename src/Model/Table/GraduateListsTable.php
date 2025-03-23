<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GraduateListsTable extends Table
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

        $this->setTable('graduate_lists');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
    }


    function getListOfStudentsForGraduateList($program_id = null, $program_type_id = null, $department_id = null)
    {

        // 1. Get all students in the department who are in the senate list but not in the graduate list


        if (is_array($program_type_id)) {
            $options['conditions']['Student.program_type_id'] = $program_type_id;
        } elseif ($program_type_id != 0) {
            $options['conditions']['Student.program_type_id'] = $program_type_id;
        } else {
            $options['conditions']['Student.program_type_id'] = 0;
        }

        if (is_array($program_id)) {
            $options['conditions']['Student.program_id'] = $program_id;
        } elseif ($program_type_id != 0) {
            $options['conditions']['Student.program_id'] = $program_id;
        } else {
            $options['conditions']['Student.program_id'] = 0;
        }

        $options['conditions']['Student.department_id'] = $department_id;
        $options['conditions'][] = 'Student.curriculum_id IS NOT NULL';
        $options['conditions'][] = 'Student.graduated = 0';
        $options['conditions'][] = 'Student.curriculum_id <> 0';
        $options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM graduate_lists)';
        $options['conditions'][] = 'Student.id IN (SELECT student_id FROM senate_lists)';

        $options['contain'] = array(
            //'Curriculum.minimum_credit_points' => array('CourseCategory'),
            'Curriculum' => array(
                'fields' => array(
                    'id',
                    'minimum_credit_points',
                    'certificate_name',
                    'amharic_degree_nomenclature',
                    'specialization_amharic_degree_nomenclature',
                    'english_degree_nomenclature',
                    'specialization_english_degree_nomenclature',
                    'minimum_credit_points',
                    'name',
                    'type_credit'
                ),
                'Department',
                'CourseCategory' => array('id', 'curriculum_id')
            ),
            'Department.name',
            'Program.name',
            'ProgramType.name',
            'CourseRegistration.id' => array(
                'PublishedCourse.id' => array(
                    'Course.credit' => array('CourseCategory', 'GradeType')
                )
            ),
            'CourseAdd.id' => array(
                'PublishedCourse.id' => array(
                    'Course.credit' => array('CourseCategory', 'GradeType')
                )
            ),
            'StudentExamStatus'
        );

        $options['fields'] = array(
            'Student.full_name',
            'Student.first_name',
            'Student.middle_name',
            'Student.last_name',
            'Student.curriculum_id',
            'Student.program_id',
            'Student.program_type_id',
            'Student.studentnumber',
            'Student.admissionyear',
            'Student.gender'
        );

        $options['order'] = array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC');
        debug($options);

        $students = $this->Student->find('all', $options);
        //debug($students);

        $organized_students = array();

        if (!empty($students)) {
            foreach ($students as $key => $student) {
                $credit_sum = 0;
                $not_used_gpa_sum = 0;
                $donot_consider_cgpa = false;
                $credit_hour_sum = 0;

                /* foreach ($student['StudentExamStatus'] as $ses_key => $ses_value) {
                    $credit_sum += $ses_value['credit_hour_sum'];
                } */


                if (isset($student['CourseAdd']) && !empty($student['CourseAdd'])) {
                    foreach ($student['CourseAdd'] as $ses_key => $ses_value) {
                        if ($ses_value['PublishedCourse']['Course']['GradeType']['used_in_gpa'] == false) {
                            $not_used_gpa_sum += $ses_value['PublishedCourse']['Course']['credit'];
                        }
                        $credit_sum += $ses_value['PublishedCourse']['Course']['credit'];
                    }
                }

                if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
                    foreach ($student['CourseRegistration'] as $ses_key => $ses_value) {
                        if ($ses_value['PublishedCourse']['Course']['GradeType']['used_in_gpa'] == false) {
                            $not_used_gpa_sum += $ses_value['PublishedCourse']['Course']['credit'];
                        }
                        $credit_sum += $ses_value['PublishedCourse']['Course']['credit'];
                    }
                }

                $all_exempted_courses = $this->Student->CourseExemption->find('all', array(
                    'conditions' => array(
                        'CourseExemption.student_id' => $student['Student']['id'],
                        'CourseExemption.department_accept_reject' => 1,
                        'CourseExemption.registrar_confirm_deny' => 1,
                    ),
                    'contain' => array(
                        'Course' => array('CourseCategory')
                    )
                ));

                $studentAttachedCurriculumIds = $this->Student->CurriculumAttachment->find('list', array(
                    'conditions' => array(
                        'CurriculumAttachment.student_id' => $student['Student']['id'],
                    ),
                    'fields' => array('curriculum_id', 'curriculum_id'),
                ));

                $student_curriculum_course_list = array();

                if (!empty($studentAttachedCurriculumIds)) {
                    $student_curriculum_course_list = $this->Student->Curriculum->Course->find('list', array(
                        'conditions' => array(
                            'Course.curriculum_id' => $studentAttachedCurriculumIds,
                        ),
                        'fields' => array('id', 'credit'),
                        'recursive' => -1
                    ));
                }

                $student_curriculum_course_id_list = array();

                if (!empty($student_curriculum_course_list)) {
                    $student_curriculum_course_id_list = array_keys($student_curriculum_course_list);
                }

                $only_exempted_credit = 0;
                $course_categories = array();

                if (!empty($all_exempted_courses) && !empty($student_curriculum_course_id_list)) {
                    foreach ($all_exempted_courses as $ec_key => $all_exempted_course) {
                        //Check if the exempted course is from their curriculum
                        if (in_array(
                            $all_exempted_course['CourseExemption']['course_id'],
                            $student_curriculum_course_id_list
                        )) {
                            // why course_id ? we need to replace with course_taken_credit
                            $only_exempted_credit += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                            $credit_sum += $all_exempted_course['Course']['credit'];
                            $course_categories[$all_exempted_course['Course']['CourseCategory']['name']]['taken_credit'] += $all_exempted_course['Course']['credit'];
                        }
                    }
                }

                if ($credit_sum >= $student['Curriculum']['minimum_credit_points']) {
                    $cid = $student['Curriculum']['id'];

                    if (!isset($organized_students[$cid])) {
                        $organized_students[$cid][0]['Curriculum'] = $student['Curriculum'];
                        $organized_students[$cid][0]['Program'] = $student['Program'];
                        $organized_students[$cid][0]['Department'] = $student['Department'];
                    }

                    $index = count($organized_students[$cid]);

                    $organized_students[$cid][$index]['Student'] = $student['Student'];
                    $organized_students[$cid][$index]['ProgramType'] = $student['ProgramType'];
                    $organized_students[$cid][$index]['credit_taken'] = $credit_sum;
                    $organized_students[$cid][$index]['disqualification'] = null;

                    $last_status = $this->Student->StudentExamStatus->find('first', array(
                        'conditions' => array(
                            'StudentExamStatus.student_id' => $student['Student']['id']
                        ),
                        'order' => array(
                            'StudentExamStatus.created DESC'
                        ),
                        'recursive' => -1
                    ));

                    if (!empty($last_status)) {
                        $organized_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
                        $organized_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
                    }
                }
            }
        }

        return $organized_students;
    }

    public function temporaryDegree($student_id = null, $ctype = null)
    {

        $temporary_degree = array();

        if (isset($ctype) && !empty($ctype)) {
            $certificate_type = $ctype;
        } else {
            $certificate_type = 'TD';
        }

        if (!empty($student_id)) {
            //App::import('Component', 'Auth');
            //$Auth = new AuthComponent();

            $student_detail = $this->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                ),
                'contain' => array(
                    'Curriculum',
                    'GraduateList',
                    'Program',
                    'ProgramType',
                    'Department',
                    'College',
                    'StudentExamStatus' => array('order' => array('StudentExamStatus.created DESC'))
                )
            ));

            if ($student_detail['Student']['graduated'] == 1 || !empty($student_detail['GraduateList'])) {
                //debug($student_detail['Student']['program_id']);

                $university_detail = ClassRegistry::init('University')->getStudentUnivrsity($student_id);

                $recentCode = ClassRegistry::init('CertificateVerificationCode')->find('first', array(
                    'conditions' => array(
                        'CertificateVerificationCode.student_id' => $student_id,
                        'OR' => array(
                            'CertificateVerificationCode.user' => '"' . AuthComponent::user('id') . '"',
                            'CertificateVerificationCode.user' => '"' . AuthComponent::user('full_name') . '"',
                        ),
                        'CertificateVerificationCode.type' => $certificate_type,
                    ),
                    'order' => array('CertificateVerificationCode.modified DESC')
                ));

                if (isset($recentCode) && !empty($recentCode)) {
                    $code = $recentCode['CertificateVerificationCode']['code'];
                } else {
                    $verification = array();
                    $code = ClassRegistry::init('CertificateVerificationCode')->generateCode($certificate_type);
                    $verification['CertificateVerificationCode']['user'] = AuthComponent::user('id');
                    $verification['CertificateVerificationCode']['student_id'] = $student_detail['Student']['id'];
                    $verification['CertificateVerificationCode']['type'] = $certificate_type;
                    $verification['CertificateVerificationCode']['code'] = $code;
                    ClassRegistry::init('CertificateVerificationCode')->create();
                    ClassRegistry::init('CertificateVerificationCode')->save($verification);
                }

                $ExitExam = array();
                $approvedThesisGrade = '';

                if ($student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE) {
                    $approvedExitExamGrade = ClassRegistry::init('ExamGrade')->getApprovedExitExamGrade($student_id);

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
                } elseif ($student_detail['Student']['program_id'] == PROGRAM_POST_GRADUATE || $student_detail['Student']['program_id'] == PROGRAM_PhD) {
                    //$approvedThesisGrade = ClassRegistry::init('ExamGrade')->getApprovedThesisGrade($student_id);
                    $approvedThesisGrade = ClassRegistry::init('ExamGrade')->getApprovedThesisTitleAndGrade(
                        $student_id
                    );
                }

                //Student profile
                $temporary_degree['student_detail']['Student'] = $student_detail['Student'];
                $temporary_degree['student_detail']['Student']['code'] = $code;
                $temporary_degree['student_detail']['Curriculum'] = $student_detail['Curriculum'];
                $temporary_degree['student_detail']['University'] = $university_detail;
                $temporary_degree['student_detail']['College'] = $student_detail['College'];
                $temporary_degree['student_detail']['Department'] = $student_detail['Department'];
                $temporary_degree['student_detail']['Program'] = $student_detail['Program'];
                $temporary_degree['student_detail']['ProgramType'] = $student_detail['ProgramType'];
                $temporary_degree['student_detail']['GraduateList'] = $student_detail['GraduateList'];
                $temporary_degree['student_detail']['StudentExamStatus'] = $student_detail['StudentExamStatus'][0];
                $temporary_degree['student_detail']['GraduationStatuse'] = ClassRegistry::init(
                    'GraduationStatus'
                )->getStudentGraduationStatus($student_id);

                $temporary_degree['student_detail']['ExitExam'] = $ExitExam;
                debug($temporary_degree['student_detail']['ExitExam']);

                if (!empty($approvedThesisGrade)) {
                    $temporary_degree['student_detail']['ThesisGrade'] = $approvedThesisGrade;
                    debug($temporary_degree['student_detail']['ThesisGrade']);
                }
            }
        }
        return $temporary_degree;
    }

    function getTemporaryDegreeCertificateForMassPrint($student_ids = array(), $ctype = null)
    {

        $temporary_degree = array();
        $temporary_degree_mass = array();

        //App::import('Component', 'Auth');
        //$Auth = new AuthComponent();

        if (isset($ctype) && !empty($ctype)) {
            $certificate_type = $ctype;
        } else {
            $certificate_type = 'TD';
        }

        debug($certificate_type);

        App::import('Component', 'EthiopicDateTime');
        $ethiopicDateTime = new EthiopicDateTimeComponent();

        if (!empty($student_ids)) {
            foreach ($student_ids as $k => $student_id) {
                $student_detail = $this->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => $student_id,
                        'Student.graduated' => 1
                    ),
                    'contain' => array(
                        'Curriculum',
                        'GraduateList',
                        'Program',
                        'ProgramType',
                        'Department',
                        'College',
                        'StudentExamStatus' => array('order' => array('StudentExamStatus.created DESC'))
                    )
                ));


                if ($student_detail['Student']['graduated'] == 1 || !empty($student_detail['GraduateList'])) {
                    $university_detail = ClassRegistry::init('University')->getStudentUnivrsity($student_id);

                    $recentCode = ClassRegistry::init('CertificateVerificationCode')->find('first', array(
                        'conditions' => array(
                            'CertificateVerificationCode.student_id' => $student_id,
                            'OR' => array(
                                'CertificateVerificationCode.user LIKE ' => '"' . AuthComponent::user('id') . '"',
                                'CertificateVerificationCode.user LIKE ' => '"' . AuthComponent::user(
                                        'full_name'
                                    ) . '"',
                            ),
                            'CertificateVerificationCode.type' => $certificate_type
                        ),
                        //'order' => array('CertificateVerificationCode.modified DESC')
                    ));

                    debug($student_id);

                    debug(AuthComponent::user('id'));

                    debug($certificate_type);

                    debug($recentCode);

                    if (isset($recentCode) && !empty($recentCode)) {
                        $code = $recentCode['CertificateVerificationCode']['code'];
                    } else {
                        $verification = array();
                        $code = ClassRegistry::init('CertificateVerificationCode')->generateCode($certificate_type);
                        $verification['CertificateVerificationCode']['user'] = AuthComponent::user('id');
                        $verification['CertificateVerificationCode']['student_id'] = $student_detail['Student']['id'];
                        $verification['CertificateVerificationCode']['type'] = $certificate_type;
                        $verification['CertificateVerificationCode']['code'] = $code;
                        ClassRegistry::init('CertificateVerificationCode')->create();
                        ClassRegistry::init('CertificateVerificationCode')->save($verification);
                    }

                    $ExitExam = array();
                    $approvedThesisGrade = '';

                    if ($student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE) {
                        $approvedExitExamGrade = ClassRegistry::init('ExamGrade')->getApprovedExitExamGrade(
                            $student_id
                        );

                        if (isset($approvedExitExamGrade)) {
                            $ExitExam['course'] = $approvedExitExamGrade['Course']['course_code_title'];
                            //debug($approvedExitExamGrade['Course']['course_code_title']);
                            $gradeForDocument = ((strcasecmp($approvedExitExamGrade['grade'], 'P') == 0 || strcasecmp(
                                    $approvedExitExamGrade['grade'],
                                    'Pass'
                                ) == 0) ? 'Pass' : ((strcasecmp(
                                    $approvedExitExamGrade['grade'],
                                    'F'
                                ) == 0 || strcasecmp($approvedExitExamGrade['grade'], 'Fail') == 0) ? 'Fail' : '---'));

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
                    } elseif ($student_detail['Student']['program_id'] == PROGRAM_POST_GRADUATE || $student_detail['Student']['program_id'] == PROGRAM_PhD) {
                        //$approvedThesisGrade = ClassRegistry::init('ExamGrade')->getApprovedThesisGrade($student_id);
                        $approvedThesisGrade = ClassRegistry::init('ExamGrade')->getApprovedThesisTitleAndGrade(
                            $student_id
                        );
                    }

                    //Student profile
                    $temporary_degree['student_detail']['Student'] = $student_detail['Student'];
                    $temporary_degree['student_detail']['Student']['code'] = $code;
                    $temporary_degree['student_detail']['Curriculum'] = $student_detail['Curriculum'];
                    $temporary_degree['student_detail']['University'] = $university_detail;
                    $temporary_degree['student_detail']['College'] = $student_detail['College'];
                    $temporary_degree['student_detail']['Department'] = $student_detail['Department'];
                    $temporary_degree['student_detail']['Program'] = $student_detail['Program'];
                    $temporary_degree['student_detail']['ProgramType'] = $student_detail['ProgramType'];
                    $temporary_degree['student_detail']['GraduateList'] = $student_detail['GraduateList'];
                    $temporary_degree['student_detail']['StudentExamStatus'] = $student_detail['StudentExamStatus'][0];
                    $temporary_degree['student_detail']['GraduationStatuse'] = ClassRegistry::init(
                        'GraduationStatus'
                    )->getStudentGraduationStatus($student_id);

                    $temporary_degree['student_detail']['ExitExam'] = $ExitExam;
                    debug($temporary_degree['student_detail']['ExitExam']);

                    if (!empty($approvedThesisGrade)) {
                        $temporary_degree['student_detail']['ThesisGrade'] = $approvedThesisGrade;
                        debug($temporary_degree['student_detail']['ThesisGrade']);
                    }

                    $e_day = $ethiopicDateTime->GetEthiopicDay(date('j'), date('n'), date('Y'));
                    $e_month = $ethiopicDateTime->GetEthiopicMonth(date('j'), date('n'), date('Y'));
                    $e_year = $ethiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));
                    $e_month_name = $ethiopicDateTime->GetEthiopicMonthName(date('j'), date('n'), date('Y'));
                    $g_d = $temporary_degree['student_detail']['GraduateList']['graduate_date'];
                    $e_g_day = $ethiopicDateTime->GetEthiopicDay(
                        substr($g_d, 8, 2),
                        substr($g_d, 5, 2),
                        substr($g_d, 0, 4)
                    );
                    $e_g_month = $ethiopicDateTime->GetEthiopicMonth(
                        substr($g_d, 8, 2),
                        substr($g_d, 5, 2),
                        substr($g_d, 0, 4)
                    );
                    $e_g_year = $ethiopicDateTime->GetEthiopicYear(
                        substr($g_d, 8, 2),
                        substr($g_d, 5, 2),
                        substr($g_d, 0, 4)
                    );
                    $e_g_month_name = $ethiopicDateTime->GetEthiopicMonthName(
                        substr($g_d, 8, 2),
                        substr($g_d, 5, 2),
                        substr($g_d, 0, 4)
                    );

                    $graduate_date = date(
                        'd F, Y',
                        mktime(
                            0,
                            0,
                            0,
                            substr($temporary_degree['student_detail']['GraduateList']['graduate_date'], 5, 2),
                            substr($temporary_degree['student_detail']['GraduateList']['graduate_date'], 8, 2),
                            substr($temporary_degree['student_detail']['GraduateList']['graduate_date'], 0, 4)
                        )
                    );


                    if ($temporary_degree['student_detail']['GraduationStatuse']) {
                        $temporary_degree['student_detail']['e_day'] = $e_day;
                        $temporary_degree['student_detail']['e_month'] = $e_month;
                        $temporary_degree['student_detail']['e_year'] = $e_year;
                        $temporary_degree['student_detail']['e_month_name'] = $e_month_name;
                        $temporary_degree['student_detail']['e_g_day'] = $e_g_day;
                        $temporary_degree['student_detail']['e_g_month'] = $e_g_month;
                        $temporary_degree['student_detail']['e_g_year'] = $e_g_year;
                        $temporary_degree['student_detail']['e_g_month_name'] = $e_g_month_name;

                        $temporary_degree['student_detail']['graduated_date'] = $graduate_date;
                        //$temporary_degree['student_detail']['graduated_ethiopic_date'] = $this->getEthiopicGraduationDate($graduate_date);
                    } else {
                        $temporary_degree['student_detail']['e_day'] = $e_day;
                        $temporary_degree['student_detail']['e_month'] = $e_month;
                        $temporary_degree['student_detail']['e_year'] = $e_year;
                        $temporary_degree['student_detail']['e_month_name'] = $e_month_name;
                        $temporary_degree['student_detail']['e_g_day'] = $e_g_day;
                        $temporary_degree['student_detail']['e_g_month'] = $e_g_month;
                        $temporary_degree['student_detail']['e_g_year'] = $e_g_year;
                        $temporary_degree['student_detail']['e_g_month_name'] = $e_g_month_name;

                        $temporary_degree['student_detail']['graduated_date'] = $graduate_date;
                        //$temporary_degree['student_detail']['graduated_ethiopic_date'] = $this->getEthiopicGraduationDate($graduate_date);
                    }
                    $temporary_degree_mass[] = $temporary_degree;
                }
            }
        }
        return $temporary_degree_mass;
    }

    function getStudentListGraduated(
        $admission_year = null,
        $program_id,
        $program_type_id,
        $department_id,
        $year_level_id = null,
        $studentNumber = null,
        $studentName = null,
        $graduated = 1,
        $graduate_date_from = null,
        $graduate_date_to = null
    ) {

        // 1. Get all students in the department who are in  in the graduate list

        $options['conditions']['Student.program_id'] = $program_id;

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $options['conditions']['Student.program_type_id'] = $program_type_id;
        } else {
            if (AuthComponent::user('role_id') == ROLE_REGISTRAR && AuthComponent::user('is_admin') != 1) {
                if ($program_type_id == 0) {
                    $userPermissions = ClassRegistry::init('User')->getUserDetails(AuthComponent::user('id'));
                    if (!empty($userPermissions['StaffAssigne']['program_type_id'])) {
                        $options['conditions']['Student.program_type_id'] = unserialize(
                            $userPermissions['StaffAssigne']['program_type_id']
                        );
                    } else {
                        $options['conditions']['Student.program_type_id'] = 0;
                    }
                }
            }
        }

        $options['conditions']['Student.department_id'] = $department_id;
        $options['conditions'][] = 'Student.curriculum_id IS NOT NULL';
        $options['conditions'][] = 'Student.curriculum_id <> 0';


        if ($graduated == 0) {
            $options['conditions'][] = 'Student.graduated = 0';
        } else {
            $options['conditions'][] = 'Student.graduated = 1';
        }

        $options['contain'] = array('GraduateList', 'Department' => array('id', 'name'));

        if ($graduated == 1 && !empty($graduate_date_from) && !empty($graduate_date_to)) {
            $minDate = $graduate_date_from['year'] . '-' . $graduate_date_from['month'] . '-' . $graduate_date_from['day'];
            $maxDate = $graduate_date_to['year'] . '-' . $graduate_date_to['month'] . '-' . $graduate_date_to['day'];
            //debug($minDate);
            //debug($maxDate);

            $options['conditions'][] = 'GraduateList.graduate_date BETWEEN "' . $minDate . '" AND "' . $maxDate . '"';
        }

        if ($graduated == 0 && !empty($admission_year)) {
            //$admissionYear = explode('/', $admission_year);
            //$options['conditions']['YEAR(Student.admissionyear)'] = $admissionYear[0];
            $options['conditions']['Student.academicyear'] = $admission_year;
        }

        if (!empty($studentName)) {
            //$options['conditions']['Student.name LIKE '] = '%'.$studentName.'%';
            $options['conditions'][] = 'Student.name LIKE %' . $studentName . '%';
        }

        if (!empty($studentNumber)) {
            unset($options);
            $options['conditions']['Student.studentnumber'] = trim($studentNumber);
        }

        //debug($options);

        $options['fields'] = array(
            'Student.id',
            'Student.curriculum_id',
            'Student.full_name',
            'Student.first_name',
            'Student.middle_name',
            'Student.last_name',
            'Student.studentnumber',
            'Student.admissionyear',
            'Student.gender',
            'Student.department_id',
            'Student.college_id',
            'Student.program_id',
            'Student.program_type_id',
            'Student.graduated',
            'Student.academicyear',
            'Student.admissionyear'
        );
        $options['order'] = array(
            'Student.first_name ASC',
            'Student.middle_name ASC',
            'Student.last_name ASC',
            'Student.academicyear ASC'
        );

        $options['contain'] = array(
            'GraduateList',
            'Curriculum' => array('id', 'name'),
            'College' => array('id', 'name'),
            'Department' => array('id', 'name'),
            'Program' => array('id', 'name', 'shortname'),
            'ProgramType' => array('id', 'name', 'shortname')
        );

        return $this->Student->find('all', $options);
    }

    function getEthiopicGraduationDate($graduationDate)
    {

        App::import('Component', 'EthiopicDateTime');

        $EthiopicDateTime = new EthiopicDateTimeComponent();

        $ethiopicDate['e_day'] = $EthiopicDateTime->GetEthiopicDay(date('j'), date('n'), date('Y'));
        $ethiopicDate['e_month'] = $EthiopicDateTime->GetEthiopicMonth(date('j'), date('n'), date('Y'));
        $ethiopicDate['e_year'] = $EthiopicDateTime->GetEthiopicYear(date('j'), date('n'), date('Y'));
        $ethiopicDate['e_month_name'] = $EthiopicDateTime->GetEthiopicMonthName(date('j'), date('n'), date('Y'));

        $g_d = $graduationDate;

        $g_d_obj = new DateTime($g_d);
        //debug($date->format('j'));

        $ethiopicDate['e_g_day'] = $EthiopicDateTime->GetEthiopicDay(
            $g_d_obj->format('j'),
            $g_d_obj->format('n'),
            $g_d_obj->format('Y')
        );
        $ethiopicDate['e_g_month'] = $EthiopicDateTime->GetEthiopicMonth(
            $g_d_obj->format('j'),
            $g_d_obj->format('n'),
            $g_d_obj->format('Y')
        );
        $ethiopicDate['e_g_year'] = $EthiopicDateTime->GetEthiopicYear(
            $g_d_obj->format('j'),
            $g_d_obj->format('n'),
            $g_d_obj->format('Y')
        );
        $ethiopicDate['e_g_month_name'] = $EthiopicDateTime->GetEthiopicMonthName(
            $g_d_obj->format('j'),
            $g_d_obj->format('n'),
            $g_d_obj->format('Y')
        );

        return $ethiopicDate;
    }

    public function isGraduated($studentId)
    {

        $result = $this->find('count', array(
            'conditions' => array(
                'GraduateList.student_id' => $studentId
            ),
        ));

        if ($result) {
            return true;
        }
        return false;
    }
}
