<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SenateListsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('senate_lists');
        $this->setPrimaryKey('id');
        $this->setDisplayField('minute_number');

        // Add Associations
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        // Add Timestamp behavior for tracking created/modified times
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('minute_number')
            ->maxLength('minute_number', 255, 'Minute number is too long.')
            ->notEmptyString('minute_number', 'Minute number is required.');

        $validator
            ->integer('student_id', 'Student ID must be numeric.')
            ->notEmptyString('student_id', 'Student ID is required.');

        return $validator;
    }

    function getListOfStudentsForSenateList($program_id = null, $program_type_id = null, $department_id = null, $admission_year = '', $percentCompletedCredit = 0.45, $studentnumber = '')
    {
        /***
        1. Get all students in the department who are neither in graduation nor senate list
        2. Filter students who takes a minimum of the specified credit hours on their curriculum
        //fully registered, add, exempt, substitute all courses from their curriculum
        3. Decide who is going to be included in the senate list and generate justification for those who will not be on the senate list.
        RULE:
        1. No F, NG, I, DO, W, and check if the course are not repeated.getCourseRepetation() TODO
        2. All required course should be taken (it is by category)

        3. A minimum of x CGPA
        4. Return the list
         ***/

        //$percentCompletedCredit = 0.45;

        if (!empty($admission_year)) {
            $options['conditions'][] = array('Student.academicyear LIKE ' => $admission_year . '%');
        }

        if (!empty(trim($studentnumber))) {
            $options['conditions'][] = array('Student.studentnumber LIKE ' => trim($studentnumber) . '%');
        }

        $options['conditions']['Student.program_id'] = $program_id;
        $options['conditions']['Student.graduated'] = 0;

        if ($program_type_id != 0 && !empty($program_type_id)) {
            $options['conditions']['Student.program_type_id'] = $program_type_id;
        } else {
            App::import('Component', 'Auth');
            $Auth = new AuthComponent(new ComponentRegistry());
            if ($Auth->user('role_id') == ROLE_REGISTRAR && $Auth->user('is_admin') != 1) {
                if ($program_type_id == 0) {
                    $userPermissions = ClassRegistry::init('User')->getUserDetails($Auth->user('id'));
                    if (!empty($userPermissions['StaffAssigne']['program_type_id'])) {
                        $options['conditions']['Student.program_type_id'] = unserialize($userPermissions['StaffAssigne']['program_type_id']);
                    } else {
                        $options['conditions']['Student.program_type_id'] = 0;
                    }
                }
            }
        }

        $options['conditions']['Student.department_id'] = $department_id;
        $options['conditions'][] = 'Student.curriculum_id IS NOT NULL AND Student.curriculum_id > 0';

        $minimumPointofCurriculum = $this->Student->Curriculum->find('first', array(
            'conditions' => array(
                'Curriculum.department_id' => $department_id,
                'Curriculum.program_id' => $program_id,
            ),
            'order' => array('Curriculum.minimum_credit_points' => 'ASC'),
            'recursive' => -1,
        ));

        // debug($minimumPointofCurriculum['Curriculum']['id']);
        // debug($minimumPointofCurriculum['Curriculum']['name']);
        // debug($minimumPointofCurriculum['Curriculum']['minimum_credit_points']);

        $courseNotUsedInGPA = $this->Student->Curriculum->Course->find('first', array(
            'conditions' => array(
                'Course.department_id' => $department_id,
                'GradeType.used_in_gpa' => 0,
                'Curriculum.program_id' => $program_id,
            ),
            'contain' => array(
                'GradeType',
                'Curriculum' => array('id', 'program_id')
            ),
            'order' => array('Course.credit' => 'DESC'),
            'recursive' => -1,
        ));

        //debug($courseNotUsedInGPA);

        if (!empty($courseNotUsedInGPA)) {
            $notUsedInCGPACreditSum = $courseNotUsedInGPA['Course']['credit'];
        } else {
            $notUsedInCGPACreditSum = 0;
        }

        //debug($notUsedInCGPACreditSum);

        //$studentquery = ' and student_id=20816';

        if (isset($program_type_id) && !empty($program_type_id)) {
            $exemptionMaximum = $this->query(
                "SELECT student_id, SUM(course_taken_credit) FROM  course_exemptions
				WHERE student_id in (select id from students where graduated = 0 and department_id = $department_id and program_id = $program_id and program_type_id = $program_type_id)
				and student_id  NOT IN (SELECT student_id FROM senate_lists where student_id is not null)
				and student_id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null )
				GROUP BY student_id order by SUM(course_taken_credit) DESC limit 1"
            );
        } else {
            $exemptionMaximum = $this->query(
                "SELECT student_id, SUM(course_taken_credit) FROM  course_exemptions
				WHERE student_id in (select id from students where graduated = 0 and department_id = $department_id and program_id= $program_id)
				and student_id  NOT IN (SELECT student_id FROM senate_lists where student_id is not null)
				and student_id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null)
				GROUP BY student_id order by SUM(course_taken_credit)  DESC limit 1"
            );
        }

        // debug($exemptionMaximum);
        // debug($percentCompletedCredit);

        if (isset($exemptionMaximum[0][0]['SUM( course_taken_credit )']) && !empty($exemptionMaximum[0][0]['SUM(course_taken_credit)'])) {
            $exptionPoint = $exemptionMaximum[0][0]['SUM(course_taken_credit)'];
        } else {
            $exptionPoint = 0;
        }

        debug($exptionPoint);

        if (isset($studentnumber) && !empty($studentnumber)) {
            $studentnumberQuoted = "'" . trim($studentnumber) . "%'";
            $studentLists = $this->query(
                "SELECT student_id, SUM(credit_hour_sum) FROM  student_exam_statuses
				WHERE student_id IN (select id from students where graduated = 0 and department_id = $department_id and studentnumber LIKE $studentnumberQuoted and program_id = $program_id)
				and student_id  NOT IN (SELECT student_id FROM senate_lists where student_id IN (select id from students where studentnumber LIKE $studentnumberQuoted))
				and student_id NOT IN (SELECT student_id FROM graduate_lists where student_id IN (select id from students where studentnumber LIKE $studentnumberQuoted))
				GROUP BY student_id HAVING SUM(credit_hour_sum) >= " . ($minimumPointofCurriculum['Curriculum']['minimum_credit_points'] - $exptionPoint - $notUsedInCGPACreditSum) * ($percentCompletedCredit) . ""
            );
        } elseif (isset($program_type_id) && !empty($program_type_id)) {
            $studentLists = $this->query(
                "SELECT student_id, SUM(credit_hour_sum) FROM  student_exam_statuses
				WHERE student_id in (select id from students where graduated = 0 and department_id = $department_id and program_id = $program_id and program_type_id = $program_type_id)
				and student_id  NOT IN (SELECT student_id FROM senate_lists where student_id is not null)
				and student_id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null)
				GROUP BY student_id HAVING SUM(credit_hour_sum) >= " . ($minimumPointofCurriculum['Curriculum']['minimum_credit_points'] - $exptionPoint - $notUsedInCGPACreditSum) * ($percentCompletedCredit) . ""
            );
        } else {
            $studentLists = $this->query(
                "SELECT student_id, SUM(credit_hour_sum) FROM  student_exam_statuses
				WHERE student_id in (select id from students where graduated = 0 and department_id = $department_id and program_id = $program_id)
				and student_id  NOT IN (SELECT student_id FROM senate_lists where student_id is not null)
				and student_id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null)
				GROUP BY student_id HAVING SUM(credit_hour_sum) >= " . ($minimumPointofCurriculum['Curriculum']['minimum_credit_points'] - $exptionPoint - $notUsedInCGPACreditSum) * ($percentCompletedCredit) . ""
            );
        }

        //debug($studentLists);

        // consider only those students who have registered and achieved the minimum credit hours without status since there are courses which doenst require status
        debug(count($studentLists));

        $options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM senate_lists where student_id is not null)';

        $student_ids = array();

        if (!empty($studentLists)) {
            foreach ($studentLists as $id) {
                // debug($id);
                // debug($notUsedInCGPACreditSum);
                // debug($minimumPointofCurriculum['Curriculum']['minimum_credit_points']);

                if (($id[0]['SUM(credit_hour_sum)'] + $notUsedInCGPACreditSum) >= $minimumPointofCurriculum['Curriculum']['minimum_credit_points']) {
                    $student_ids[$id['student_exam_statuses']['student_id']] = $id['student_exam_statuses']['student_id'];
                } else {
                    $student_ids[$id['student_exam_statuses']['student_id']] = $id['student_exam_statuses']['student_id'];
                }
            }
        }

        //debug($student_ids);

        if (!empty($student_ids)) {
            $options['conditions']['Student.id'] = $student_ids;
        }

        debug($options);
        //return array();
        //$options['conditions'][] = 'Student.id  IN (SELECT student_id FROM course_registrations)';

        $options['contain'] = array(
            'Curriculum' => array(
                'fields' => array('id', 'type_credit', 'minimum_credit_points', 'certificate_name', 'amharic_degree_nomenclature', 'specialization_amharic_degree_nomenclature', 'english_degree_nomenclature', 'specialization_english_degree_nomenclature', 'minimum_credit_points', 'name', 'year_introduced'),
                'Department',
                'CourseCategory' => array('id', 'curriculum_id')
            ),
            'Department.name',
            'Program.name',
            'ProgramType.name',
            'CourseRegistration.id' => array(
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.drop', 'PublishedCourse.academic_year', 'PublishedCourse.semester'),
                    'Course.course_title',
                    'Course.credit' => array('CourseCategory'),
                    'Course.curriculum_id',
                    'Course.course_code',
                ),
                'ExamGrade'
                //'order' => array('CourseRegistration.academic_year' => 'ASC', 'CourseRegistration.semester' => 'ASC', 'CourseRegistration.id' => 'ASC')
            ),
            'CourseAdd.id' => array(
                'fields' => array('registrar_confirmation'),
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.drop', 'PublishedCourse.academic_year', 'PublishedCourse.semester'),
                    'Course.credit' => array('CourseCategory'),
                    'Course.course_title',
                    'Course.curriculum_id',
                    'Course.course_code',
                ),
                'ExamGrade'
                //'order' => array('CourseAdd.academic_year' => 'ASC', 'CourseAdd.semester' => 'ASC', 'CourseAdd.id' => 'ASC')
            )
        );

        $options['fields'] = array('Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.program_id', 'Student.program_type_id', 'Student.admissionyear', 'Student.gender', 'Student.academicyear', 'Student.student_national_id');
        $options['order'] = array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC');
        $genericErrorMessage = array();

        //$options['limit']=30;

        $students = $this->Student->find('all', $options);
        //debug($students);
        debug(count($students));

        $filtered_students = array();

        if (!empty($students)) {
            foreach ($students as $key => $student) {
                if (isset($student['Curriculum']['id'])) {
                    $credit_sum = 0;

                    $donot_consider_cgpa = false;

                    $course_category_detail = $this->Student->Curriculum->CourseCategory->find('all', array(
                        'conditions' => array(
                            'CourseCategory.curriculum_id' => $student['Curriculum']['id']
                        ),
                        'recursive' => -1
                    ));

                    $course_categories = array();

                    if (!empty($course_category_detail)) {
                        foreach ($course_category_detail as $key => $value) {
                            $course_categories[$value['CourseCategory']['name']]['mandatory_credit'] = $value['CourseCategory']['mandatory_credit'];
                            $course_categories[$value['CourseCategory']['name']]['total_credit'] = $value['CourseCategory']['total_credit'];
                            $course_categories[$value['CourseCategory']['name']]['taken_credit'] = 0;
                        }
                    }

                    $sumexcludingfromreg = 0;   ////////////// check this back
                    $justsummedandtaken = 0;

                    if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
                        foreach ($student['CourseRegistration'] as $key => $course_registration) {
                            // isRegistrationAddForFirstTime return false sometimes?
                            $justsummedandtaken += $course_registration['PublishedCourse']['Course']['credit'];

                            if (!$this->Student->CourseRegistration->isCourseDroped($course_registration['id']) && $course_registration['PublishedCourse']['drop'] == 0 && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_registration['id'], 1, 1)) {
                                $credit_sum += $course_registration['PublishedCourse']['Course']['credit'];

                                if ($student['Curriculum']['id'] != $course_registration['PublishedCourse']['Course']['curriculum_id']) {
                                    $course_ids = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_registration['PublishedCourse']['course_id'], $student['Curriculum']['id']);
                                    $course_cat_mapped = ClassRegistry::init('EquivalentCourse')->courseEquivalentCategory($course_registration['PublishedCourse']['course_id'], $student['Curriculum']['id']);

                                    //debug($course_ids);
                                    //debug($course_cat_mapped);

                                    if (!empty($course_cat_mapped)) {
                                        $course_categories[$course_cat_mapped]['taken_credit'] += $course_registration['PublishedCourse']['Course']['credit'];
                                    }
                                } else {
                                    if (isset($course_registration['PublishedCourse']['Course']['CourseCategory']['name']) && !isset($course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'])) {
                                        $course_ids = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_registration['PublishedCourse']['course_id'], $student['Curriculum']['id']);
                                        $course_cat_mapped = ClassRegistry::init('EquivalentCourse')->courseEquivalentCategory($course_registration['PublishedCourse']['course_id'], $student['Curriculum']['id']);
                                        //debug($course_cat_mapped);

                                        if (!empty($course_cat_mapped)) {
                                            $course_categories[$course_cat_mapped]['taken_credit'] += $course_registration['PublishedCourse']['Course']['credit'];
                                        } else {
                                            $course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] = 0;
                                        }
                                    } elseif (isset($course_registration['PublishedCourse']['Course']['CourseCategory']['name'])) {
                                        $course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] += $course_registration['PublishedCourse']['Course']['credit'];
                                    }
                                }
                            } else {
                                if (isset($course_registration['PublishedCourse']['Course']['credit']) && !empty($course_registration['PublishedCourse']['Course']['credit'])) {
                                    $sumexcludingfromreg += $course_registration['PublishedCourse']['Course']['credit'];
                                }
                                /* debug($course_registration['PublishedCourse']['Course']['course_title']);
                                debug($course_registration['PublishedCourse']['Course']['CourseCategory']['name']);
                                debug($this->Student->CourseRegistration->isCourseDroped($course_registration['id']));
                                debug($course_registration['PublishedCourse']['drop']);
                                debug($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_registration['id'], 1, 1));
                                debug($course_registration); */
                            }
                        }
                    }

                    // debug($justsummedandtaken);
                    // debug($credit_sum);
                    // debug($sumexcludingfromreg);

                    $addSum = 0;
                    $sumexcludingfromreg = 0;
                    $sumexcludingfromadd = 0;
                    //$debugingsum=0;

                    if (isset($student['CourseAdd']) && !empty($student['CourseAdd'])) {
                        foreach ($student['CourseAdd'] as $key => $course_add) {
                            //debug($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_add['id'], 0, 1));
                            //debug($course_add);

                            if ($course_add['registrar_confirmation'] && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_add['id'], 0, 1)) {
                                $credit_sum += $course_add['PublishedCourse']['Course']['credit'];
                                $addSum += $course_add['PublishedCourse']['Course']['credit'];

                                if ($student['Curriculum']['id'] != $course_add['PublishedCourse']['Course']['curriculum_id']) {
                                    //debug($course_add['PublishedCourse']['Course']);

                                    $course_ids = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_add['PublishedCourse']['course_id'], $student['Curriculum']['id']);
                                    $course_cat_mapped = ClassRegistry::init('EquivalentCourse')->courseEquivalentCategory($course_add['PublishedCourse']['course_id'], $student['Curriculum']['id']);

                                    //debug($course_ids);
                                    //debug($course_cat_mapped);

                                    if (!empty($course_cat_mapped)) {
                                        $course_categories[$course_cat_mapped]['taken_credit'] += $course_add['PublishedCourse']['Course']['credit'];
                                    }
                                } else {
                                    if (isset($course_add['PublishedCourse']['Course']['CourseCategory']['name']) && !isset($course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'])) {
                                        //debug($course_add['PublishedCourse']['Course']['CourseCategory']['name']);
                                        $course_ids = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_add['PublishedCourse']['course_id'], $student['Curriculum']['id']);

                                        $course_cat_mapped = ClassRegistry::init('Course')->find('first', array('conditions' => array('Course.id' => $course_ids), 'contain' => array('CourseCategory')));
                                        //debug($course_ids);
                                        $course_cat_mapped = ClassRegistry::init('EquivalentCourse')->courseEquivalentCategory($course_add['PublishedCourse']['course_id'], $student['Curriculum']['id']);

                                        if (!empty($course_cat_mapped)) {
                                            $course_categories[$course_cat_mapped]['taken_credit'] += $course_add['PublishedCourse']['Course']['credit'];
                                        } else {
                                            $course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] = 0;
                                        }
                                    } elseif (isset($course_add['PublishedCourse']['Course']['CourseCategory']['name'])) {
                                        //debug($course_add['PublishedCourse']['Course']['CourseCategory']['name']);
                                        $course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] += $course_add['PublishedCourse']['Course']['credit'];
                                    }
                                }
                            } else {
                                /* debug($course_add['PublishedCourse']['course_id']);
                                debug($course_add['PublishedCourse']['Course']['course_title']);
                                debug($course_add['PublishedCourse']['Course']['CourseCategory']['name']);
                                debug($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_add['id'], 0, 1));
                                debug($course_add['registrar_confirmation']);
                                debug($course_add['id']);
                                debug($course_add); */
                                if (isset($course_add['PublishedCourse']['Course']['credit']) && !empty($course_add['PublishedCourse']['Course']['credit'])) {
                                    $sumexcludingfromadd += $course_add['PublishedCourse']['Course']['credit'];
                                }
                            }
                        }
                    }

                    // debug($addSum);
                    // debug($credit_sum);
                    // debug($sumexcludingfromadd);

                    //debug($student['Curriculum']['minimum_credit_points']);
                    //debug($student['Student']['id']);
                    //debug($course_categories);

                    //Include all exempted courses in the credit_sum
                    $all_exempted_courses = $this->Student->CourseExemption->find('all', array(
                        'conditions' => array(
                            'CourseExemption.student_id' => $student['Student']['id'],
                            'CourseExemption.department_accept_reject' => 1,
                            'CourseExemption.registrar_confirm_deny' => 1,
                        ),
                        'contain' => array('Course' => array('CourseCategory'))
                    ));

                    //debug($all_exempted_courses);

                    $studentAttachedCurriculumIds = $this->Student->CurriculumAttachment->find('list', array(
                        'conditions' => array(
                            'CurriculumAttachment.student_id' => $student['Student']['id'],
                        ),
                        'fields' => array('curriculum_id', 'curriculum_id'),
                    ));

                    //debug($studentAttachedCurriculumIds);

                    $student_curriculum_course_id_list = array();

                    if (!empty($studentAttachedCurriculumIds)) {
                        $student_curriculum_course_list = $this->Student->Curriculum->Course->find('list', array(
                            'conditions' => array(
                                'Course.curriculum_id' => $studentAttachedCurriculumIds,
                            ),
                            'fields' => array('id', 'credit'),
                            'recursive' => -1
                        ));

                        if (!empty($student_curriculum_course_list)) {
                            $student_curriculum_course_id_list = array_keys($student_curriculum_course_list);
                        }
                    }


                    $only_exempted_credit = 0;
                    //debug($all_exempted_courses);
                    //debug($student_curriculum_course_id_list);

                    if (!empty($all_exempted_courses) && !empty($student_curriculum_course_id_list)) {
                        foreach ($all_exempted_courses as $ec_key => $all_exempted_course) {
                            //debug($all_exempted_course);
                            //Check if the exempted course is from their curriculum
                            if (in_array($all_exempted_course['CourseExemption']['course_id'], $student_curriculum_course_id_list)) {
                                // why course_id ? replaced with course_taken_credit, Neway
                                $only_exempted_credit += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                                //$credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                                $credit_sum += $all_exempted_course['Course']['credit'];
                                $course_categories[$all_exempted_course['Course']['CourseCategory']['name']]['taken_credit'] += $all_exempted_course['Course']['credit'];
                            }
                        }
                    }

                    // debug($only_exempted_credit);
                    // debug($student['Student']['id']);
                    // debug($credit_sum);
                    // debug($student['Curriculum']['minimum_credit_points']);

                    //die;
                    if ($credit_sum >= $student['Curriculum']['minimum_credit_points']) {
                        //Now the student fullfill the minimum credit hour requirement
                        //debug($student['Curriculum']['name']);
                        //debug($student['Student']);
                        $incomplete_grade = false;
                        $invalid_grade = false;
                        $cid = $student['Curriculum']['id'];

                        if (!isset($filtered_students[$cid])) {
                            $filtered_students[$cid][0]['Curriculum'] = $student['Curriculum'];
                            $filtered_students[$cid][0]['Program'] = $student['Program'];
                            $filtered_students[$cid][0]['Department'] = $student['Department'];
                        }

                        $index = count($filtered_students[$cid]);
                        $filtered_students[$cid][$index]['Student'] = $student['Student'];
                        $filtered_students[$cid][$index]['ProgramType'] = $student['ProgramType'];
                        $filtered_students[$cid][$index]['credit_taken'] = $credit_sum;
                        $filtered_students[$cid][$index]['disqualification'] = null;
                        $filtered_students[$cid][$index]['ExemptedCredit'] = $this->Student->CourseExemption->getStudentCourseExemptionCredit($student['Student']['id']);

                        //Check: 1) All registered course grade is submitted and 2) A valid grade for each registration

                        $courses_with_c = array();
                        $courses_with_c_plus = array();

                        $courses_with_failed_grade = array();

                        if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
                            foreach ($student['CourseRegistration'] as $key => $course_registration) {
                                if (!$this->Student->CourseRegistration->isCourseDroped($course_registration['id']) && $course_registration['PublishedCourse']['drop'] == 0) {
                                    $grade_detail = $this->Student->CourseRegistration->getCourseRegistrationLatestApprovedGradeDetail($course_registration['id']);
                                    $courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_registration['id'], $course_registration['student_id'], 1);
                                    //debug($courseRepeated);

                                    if ($courseRepeated['repeated_old']) {
                                        //debug($course_registration);
                                        continue;
                                    }


                                    if (!empty($grade_detail) && isset($grade_detail['ExamGrade']['grade']) && !empty($grade_detail['ExamGrade']['grade'])) {
                                        $latestApprovedGrade = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade($course_registration['id'], 1);

                                        // fix invalid grades NG, I, F, Fx, Fail in exam_grades if they have a valid grade changes
                                        if (strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) {
                                            if (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                                //debug($grade_detail);
                                                //debug($latestApprovedGrade);
                                                $grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
                                            }
                                            //debug($grade_detail);
                                        } elseif (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                            // for repeated courses that have a grade change through supplementary exam grade change
                                            //debug($grade_detail);
                                            //debug($latestApprovedGrade);
                                            $grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
                                        }

                                        if (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id'] && isset($latestApprovedGrade['pass_grade']) && !$latestApprovedGrade['pass_grade'] && empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                            $courses_with_failed_grade[] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
                                        } elseif (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id'] && isset($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                            $courses_with_failed_grade[] = 'Failed Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
                                        } elseif (isset($grade_detail['ExamGrade']['grade']) && (strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'NG'
                                                ) == 0 || strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'DO'
                                                ) == 0 || strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'I'
                                                ) == 0 || strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'F'
                                                ) == 0 || strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'W'
                                                ) == 0 || strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'Fx'
                                                ) == 0 || strcasecmp(
                                                    $grade_detail['ExamGrade']['grade'],
                                                    'Fail'
                                                ) == 0)) {
                                            $courses_with_failed_grade[] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
                                        }
                                    }


                                    /// add exam grade pass and fail check, recently students with the fail grade( like D set as fail grade) appear in seenate list.

                                    if (empty($grade_detail) && !$incomplete_grade) {
                                        //debug($grade_detail);
                                        //debug($course_registration['id']);
                                        if (isset($course_registration['ExamGrade']) && !empty($course_registration['ExamGrade']) && count($course_registration['ExamGrade']) > 1) {
                                            debug($course_registration);
                                            $check_for_duplicate_grade_entry = $this->Student->CourseRegistration->ExamGrade->find('count', array('conditions' => array('ExamGrade.course_registration_id' => $course_registration['id'], 'ExamGrade.registrar_approval' => 1)));
                                            if (!$check_for_duplicate_grade_entry) {
                                                $filtered_students[$cid][$index]['disqualification'][] = 'Incomplete double grade entry: ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
                                                $incomplete_grade = true;
                                                $donot_consider_cgpa = true;
                                            }
                                        } else {
                                            $filtered_students[$cid][$index]['disqualification'][] = 'The student have incomplete grade from course registration. All student exam grade should be submitted and approved by both department and registrar.';
                                            $incomplete_grade = true;
                                            $donot_consider_cgpa = true;
                                        }
                                        //$filtered_students[$cid][$index]['disqualification'][] = 'The student have incomplete grade from course registration. All student exam grade should be submitted and approved by both department and registrar.';
                                        //debug($incomplete_grade);
                                        //$incomplete_grade = true;
                                        //$donot_consider_cgpa = true;
                                    } elseif (!$invalid_grade && isset($grade_detail['ExamGrade']['grade']) && (strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'NG'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'DO'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'I'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'F'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'W'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'Fx'
                                            ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0)) {
                                        //debug($invalid_grade);
                                        //debug($grade_detail['ExamGrade']);
                                        //$filtered_students[$cid][$index]['disqualification'][] = 'Student has invalid grade. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                        /* if (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id']) {
                                            $filtered_students[$cid][$index]['disqualification'][] = 'The student have invalid grade (' . $grade_detail['ExamGrade']['grade'] . ') from course registration in '. $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester for ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . '). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                        } else {
                                            $filtered_students[$cid][$index]['disqualification'][] = 'The student have invalid grade from course registration. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                        } */
                                        $filtered_students[$cid][$index]['disqualification'][] = 'The student have invalid/failed grade from course registration. Any of the student grade should not contain NG, I, DO, W, or a grade set as fail grade in grade scale.';
                                        //debug($courseRepeated);
                                        //debug($grade_detail);
                                        $invalid_grade = true;
                                        $donot_consider_cgpa = true;
                                    }

                                    if (($student['Student']['program_id'] == PROGRAM_POST_GRADUATE || $student['Student']['program_id'] == PROGRAM_PhD) && isset($grade_detail['ExamGrade']['grade']) && !empty($grade_detail['ExamGrade']['grade']) && isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id']) {
                                        if (strcasecmp($grade_detail['ExamGrade']['grade'], 'C+') == 0) {
                                            $courses_with_c_plus[] = 'Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
                                        } elseif (strcasecmp($grade_detail['ExamGrade']['grade'], 'C') == 0) {
                                            $courses_with_c[] = 'Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim($course_registration['PublishedCourse']['Course']['course_title'])) . ' (' . (trim($course_registration['PublishedCourse']['Course']['course_code'])) . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ', ' . ($course_registration['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_registration['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_registration['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_registration['PublishedCourse']['semester'])))  . ' semester. (Course Registration)';
                                        }
                                    }
                                }
                            }
                        }

                        //Check: 1) All added course grade is submitted and 2) A valid grade for each add

                        if (isset($student['CourseAdd']) && !empty($student['CourseAdd'])) {
                            foreach ($student['CourseAdd'] as $key => $course_add) {
                                if ($course_add['registrar_confirmation'] == false) {
                                    continue;
                                }

                                $grade_detail = $this->Student->CourseAdd->getCourseAddLatestApprovedGradeDetail($course_add['id']);
                                $courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_add['id'], $course_add['student_id'], 0);

                                //debug($courseRepeated);

                                if ($courseRepeated['repeated_old']) {
                                    //debug($course_add);
                                    continue;
                                }


                                if (!empty($grade_detail) && isset($grade_detail['ExamGrade']['grade']) && !empty($grade_detail['ExamGrade']['grade'])) {
                                    $latestApprovedGrade = $this->Student->CourseRegistration->ExamGrade->getApprovedGrade($course_add['id'], 0);

                                    // fix invalid grades NG, I, F, Fx, Fail in exam_grades if they have a valid grade changes
                                    if (strcasecmp($grade_detail['ExamGrade']['grade'], 'NG') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'I') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0) {
                                        if (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                            //debug($grade_detail);
                                            //debug($latestApprovedGrade);
                                            $grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
                                        }
                                        //debug($grade_detail);
                                    } elseif (isset($latestApprovedGrade['grade_change_id']) && !empty($latestApprovedGrade['grade_change_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                        // for repeated courses that have a grade change through supplementary exam grade change
                                        //debug($grade_detail);
                                        //debug($latestApprovedGrade);
                                        $grade_detail['ExamGrade']['grade'] = $latestApprovedGrade['grade'];
                                    }

                                    if (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id'] && isset($latestApprovedGrade['pass_grade']) && !$latestApprovedGrade['pass_grade'] && empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                        $courses_with_failed_grade[] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_title']
                                            )) . ' (' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_code']
                                            )) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester']))) . ' semester. (Course Add)';
                                    } elseif (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id'] && isset($latestApprovedGrade['point_value']) && !empty($latestApprovedGrade['point_value']) && $latestApprovedGrade['point_value'] >= 0 && !$latestApprovedGrade['pass_grade'] && !empty($latestApprovedGrade['grade_scale_id']) && $grade_detail['ExamGrade']['id'] == $latestApprovedGrade['grade_id']) {
                                        $courses_with_failed_grade[] = 'Failed Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_title']
                                            )) . ' (' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_code']
                                            )) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester']))) . ' semester. (Course Add)';
                                    } elseif (isset($grade_detail['ExamGrade']['grade']) && (strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'NG'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'DO'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'I'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'F'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'W'
                                            ) == 0 || strcasecmp(
                                                $grade_detail['ExamGrade']['grade'],
                                                'Fx'
                                            ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0)) {
                                        $courses_with_failed_grade[] = 'Invalid Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_title']
                                            )) . ' (' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_code']
                                            )) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester']))) . ' semester. (Course Add)';
                                    }
                                }

                                /// add exam grade pass and fail check, recently students with the fail grade( like D set as fail grade) appear in seenate list.

                                if (empty($grade_detail) && !$incomplete_grade) {
                                    $filtered_students[$cid][$index]['disqualification'][] = 'The student have incomplete grade from course add. All student exam grade should be submitted and approved by both department and registrar.';
                                    //debug($incomplete_grade);
                                    //debug($course_add['id']);
                                    $incomplete_grade = true;
                                    $donot_consider_cgpa = true;
                                } elseif (!$invalid_grade && isset($grade_detail['ExamGrade']['grade']) && (strcasecmp(
                                            $grade_detail['ExamGrade']['grade'],
                                            'NG'
                                        ) == 0 || strcasecmp(
                                            $grade_detail['ExamGrade']['grade'],
                                            'DO'
                                        ) == 0 || strcasecmp(
                                            $grade_detail['ExamGrade']['grade'],
                                            'I'
                                        ) == 0 || strcasecmp(
                                            $grade_detail['ExamGrade']['grade'],
                                            'F'
                                        ) == 0 || strcasecmp(
                                            $grade_detail['ExamGrade']['grade'],
                                            'W'
                                        ) == 0 || strcasecmp(
                                            $grade_detail['ExamGrade']['grade'],
                                            'Fx'
                                        ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fail') == 0)) {
                                    /* if (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id']) {
                                        $filtered_students[$cid][$index]['disqualification'][] = 'The student have invalid grade (' . $grade_detail['ExamGrade']['grade'] . ') from course add in '. $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester'])))  . ' semester for ' . (trim($course_add['PublishedCourse']['Course']['course_title'])) .' (' . (trim($course_add['PublishedCourse']['Course']['course_code'])) .'). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                    } else {
                                        $filtered_students[$cid][$index]['disqualification'][] = 'The student have invalid grade from course add. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                    } */
                                    $filtered_students[$cid][$index]['disqualification'][] = 'The student have invalid/failed grade from course add. Any of the student grade should not contain NG, I, DO, W, or a grade set as fail grade in grade scale.';
                                    $invalid_grade = true;
                                    $donot_consider_cgpa = true;
                                    //debug($grade_detail);
                                }

                                if (($student['Student']['program_id'] == PROGRAM_POST_GRADUATE || $student['Student']['program_id'] == PROGRAM_PhD) && isset($grade_detail['ExamGrade']['grade']) && !empty($grade_detail['ExamGrade']['grade']) && isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id']) {
                                    if (strcasecmp($grade_detail['ExamGrade']['grade'], 'C+') == 0) {
                                        $courses_with_c_plus[] = 'Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_title']
                                            )) . ' (' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_code']
                                            )) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester']))) . ' semester. (Course Add)';
                                    } elseif (strcasecmp($grade_detail['ExamGrade']['grade'], 'C') == 0) {
                                        $courses_with_c[] = 'Grade (' . $grade_detail['ExamGrade']['grade'] . ') : ' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_title']
                                            )) . ' (' . (trim(
                                                $course_add['PublishedCourse']['Course']['course_code']
                                            )) . ') in ' . $course_add['PublishedCourse']['academic_year'] . ', ' . ($course_add['PublishedCourse']['semester'] == 'I' ? '1st' : ($course_add['PublishedCourse']['semester'] == 'II' ? '2nd' : ($course_add['PublishedCourse']['semester'] == 'III' ? '3rd' : $course_add['PublishedCourse']['semester']))) . ' semester. (Course Add)';
                                    }
                                }
                            }
                        }

                        //Check: All mandatory courses is taken
                        //debug($course_categories);

                        if (isset($course_categories) && !empty($course_categories)) {
                            foreach ($course_categories as $category_name => $course_category) {
                                if ($course_category['taken_credit'] < $course_category['mandatory_credit']) {
                                    $filtered_students[$cid][$index]['disqualification'][] = 'According to student attached curriculum, the student is expected to take a minimum of ' . $course_category['mandatory_credit'] . ' credits from ' . $category_name . ' course category. ' . (!empty($course_category['taken_credit']) ? 'Currently, the student only took ' . $course_category['taken_credit'] . ' credits.' : 'The student doesn\'t took any course from ' . $category_name . ' course category');
                                    $donot_consider_cgpa = true;
                                }
                            }
                        }

                        if (!empty($courses_with_c) || !empty($courses_with_c_plus)) {
                            if (count($courses_with_c) == 1 && count($courses_with_c_plus) == 0) {
                                // only 1 C, allowed
                            } elseif (count($courses_with_c_plus) <= 2 && count($courses_with_c) == 0) {
                                // 1 or 2 C+ grades with out any C grade, allowed
                            } /* else if (count($courses_with_c) <= MAXIMUM_C_GRADES_ALLOWED_FOR_POST_GRADUATE && count($courses_with_c_plus) <= MAXIMUM_C_PLUS_GRADES_ALLOWED_FOR_POST_GRADUATE) {

                            } else if (count($courses_with_c) >= MAXIMUM_C_GRADES_ALLOWED_FOR_POST_GRADUATE || count($courses_with_c_plus) >= MAXIMUM_C_PLUS_GRADES_ALLOWED_FOR_POST_GRADUATE) {

                            }  */ else {
                                $filtered_students[$cid][$index]['disqualification'][] = 'Only one C/C+ or two C+ grades without any C grade is allowed for graduate studies for graduation. <span class="rejected">The student got ' . (!empty($courses_with_c_plus) ? (count(
                                        $courses_with_c_plus
                                    ) == 1 ? ' 1 C+ grade' : count(
                                            $courses_with_c_plus
                                        ) . ' C+ grades') : '') . (!empty($courses_with_c) ? ((!empty($courses_with_c_plus) ? ' and ' : '') . (count(
                                            $courses_with_c
                                        ) == 1 ? ' 1 C grade' : count(
                                                $courses_with_c
                                            ) . ' C grades')) : '') . '</span>' . (!empty($courses_with_c_plus) ? '<br>' . (implode(
                                            '<br>',
                                            $courses_with_c_plus
                                        )) : '') . (!empty($courses_with_c) ? '<br>' . (implode(
                                            '<br/>',
                                            $courses_with_c
                                        )) : '');
                                $donot_consider_cgpa = true;
                            }
                        }

                        debug($courses_with_failed_grade);

                        if (!empty($courses_with_failed_grade)) {
                            $filtered_students[$cid][$index]['disqualification'][] = (!empty($courses_with_failed_grade) ? (count(
                                    $courses_with_failed_grade
                                ) == 1 ? 'Invalid/Failed grade (1)' : 'Invalid/Failed Grades List: (' . count(
                                        $courses_with_failed_grade
                                    ) . ')') . '<br>' . (implode('<br>', $courses_with_failed_grade)) : '');
                        }

                        //Check: A minimum cgpa is achieved
                        $minimum_cgpa = $this->Student->Program->GraduationRequirement->getMinimumGraduationCGPA($program_id, $student['Student']['admissionyear']);

                        $last_status = $this->Student->StudentExamStatus->find('first', array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $student['Student']['id']
                            ),
                            //'order' => array('StudentExamStatus.created' => 'DESC'),
                            'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
                            'recursive' => -1
                        ));

                        if (!$donot_consider_cgpa && !empty($last_status) && $last_status['StudentExamStatus']['cgpa'] < $minimum_cgpa) {
                            $filtered_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
                            $filtered_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
                            $filtered_students[$cid][$index]['disqualification'][] = 'The student need to achieve a minimum CGPA of ' . $minimum_cgpa . '. Currently, the student got CGPA of ' . $last_status['StudentExamStatus']['cgpa'] . '.';
                        } elseif (!empty($last_status) && $last_status['StudentExamStatus']['cgpa'] < $minimum_cgpa) {
                            $filtered_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
                            $filtered_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
                            $filtered_students[$cid][$index]['disqualification'][] = 'The student need to achieve a minimum CGPA of ' . $minimum_cgpa . '. Currently, the student got CGPA of ' . $last_status['StudentExamStatus']['cgpa'] . '.';
                        } elseif (!empty($last_status)) {
                            $filtered_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
                            $filtered_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
                        } else {
                            $filtered_students[$cid][$index]['cgpa'] = null;
                            $filtered_students[$cid][$index]['mcgpa'] = null;
                            if ($student['Student']['program_id'] != PROGRAM_PhD) {
                                $filtered_students[$cid][$index]['disqualification'][] = 'The student doesn\'t have any status.' . (!empty($minimum_cgpa) ? ' The Student must achieve a minimum CGPA of ' . $minimum_cgpa . '.' : '');
                            }
                        }
                    } else {
                        $genericErrorMessage['CourseCategory'] = $course_categories;
                        $genericErrorMessage['ExemptionSum'] = $only_exempted_credit;
                    }
                }
            }
        }
        //debug($genericErrorMessage);
        return $filtered_students;
    }

    function getListOfStudentsForSenateListGivenId($student_ids = array())
    {
        /***
        1. Get all students in the department who are neither in graduation nor senate list
        2. Filter students who takes a minimum of the specified credit hours on their curriculum

        //fully registered, add, exempt, substitute all courses from their curriculum
        3. Decide who is going to be included in the senate list and generate justification for those who will not be on the senate list.
        RULE:

        1. No F, NG, I, DO, W

        2. All required course should be taken (it is by category)

        3. A minimum of x CGPA

        4. Return the list

         ***/
        $options['conditions']['Student.id'] = $student_ids;
        $options['conditions'][] = 'Student.curriculum_id IS NOT NULL';
        $options['conditions'][] = 'Student.curriculum_id <> 0';
        $options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null )';
        $options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM senate_lists where student_id is not null )';

        $options['contain'] = array(
            'Curriculum' => array(
                'fields' => array('id', 'minimum_credit_points', 'certificate_name', 'amharic_degree_nomenclature', 'specialization_amharic_degree_nomenclature', 'english_degree_nomenclature', 'specialization_english_degree_nomenclature', 'minimum_credit_points', 'name'),
                'Department',
                'CourseCategory' => array(
                    'id',
                    'curriculum_id'
                )
            ),
            'Department.name',
            'Program.name',
            'ProgramType.name',
            'CourseRegistration.id' => array(
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.drop'),
                    'Course.credit' => array('CourseCategory')
                )
            ),
            'CourseAdd.id' => array(
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.drop'),
                    'Course.credit' => array('CourseCategory')
                )
            )
        );

        $options['fields'] = array('Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.program_id', 'Student.program_type_id', 'Student.admissionyear', 'Student.gender');
        $options['order'] = array('Student.first_name ASC', 'Student.middle_name ASC', 'Student.last_name ASC');
        //$options['limit'] = 10;
        //debug($options);
        $students = $this->Student->find('all', $options);
        //debug($students);
        $filtered_students = array();

        foreach ($students as $key => $student) {
            $credit_sum = 0;
            $donot_consider_cgpa = false;

            $course_category_detail = $this->Student->Curriculum->CourseCategory->find('all', array(
                'conditions' => array(
                    'CourseCategory.curriculum_id' => $student['Curriculum']['id']
                ),
                'recursive' => -1
            ));

            $course_categories = array();

            foreach ($course_category_detail as $key => $value) {
                $course_categories[$value['CourseCategory']['name']]['mandatory_credit'] = $value['CourseCategory']['mandatory_credit'];
                $course_categories[$value['CourseCategory']['name']]['total_credit'] = $value['CourseCategory']['total_credit'];
                $course_categories[$value['CourseCategory']['name']]['taken_credit'] = 0;
            }

            foreach ($student['CourseRegistration'] as $key => $course_registration) {
                // figure out why isRegistrationAddForFirstTime return false sometimes?
                if (
                    !$this->Student->CourseRegistration->isCourseDroped($course_registration['id'])
                    && $course_registration['PublishedCourse']['drop'] == 0
                    && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime(
                        $course_registration['id'],
                        1,
                        1
                    )
                ) {
                    $credit_sum += $course_registration['PublishedCourse']['Course']['credit'];

                    if (!isset($course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'])) {
                        $course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] = 0;
                    }
                    $course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] += $course_registration['PublishedCourse']['Course']['credit'];
                }
            }

            foreach ($student['CourseAdd'] as $key => $course_add) {
                if ($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_add['id'], 0, 1)) {
                    $credit_sum += $course_add['PublishedCourse']['Course']['credit'];
                    if (!isset($course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'])) {
                        $course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] = 0;
                    }
                    $course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] += $course_add['PublishedCourse']['Course']['credit'];
                }
            }

            //Include all exempted courses in the credit_sum
            $all_exempted_courses = $this->Student->CourseExemption->find('all', array(
                'conditions' => array(
                    'CourseExemption.student_id' => $student['Student']['id'],
                    'CourseExemption.department_accept_reject' => 1,
                    'CourseExemption.registrar_confirm_deny' => 1,
                ),
                'recursive' => -1
            ));

            $studentAttachedCurriculumIds = $this->Student->CurriculumAttachment->find('list', array(
                'conditions' => array(
                    'CurriculumAttachment.student_id' => $student['Student']['id'],
                ),
                'fields' => array('curriculum_id', 'curriculum_id'),
            ));

            $student_curriculum_course_list = $this->Student->Curriculum->Course->find('list', array(
                'conditions' => array(
                    'Course.curriculum_id' => $studentAttachedCurriculumIds,
                ),
                'fields' => array('id', 'credit'),
                'recursive' => -1
            ));

            $student_curriculum_course_id_list = array_keys($student_curriculum_course_list);
            $exempted_credit_sum = 0;

            foreach ($all_exempted_courses as $ec_key => $all_exempted_course) {
                //Check if the exempted course is from their curriculum
                if (in_array($all_exempted_course['CourseExemption']['course_id'], $student_curriculum_course_id_list)) {
                    $credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                    $exempted_credit_sum += $student_curriculum_course_list[$all_exempted_course['CourseExemption']['course_id']];
                }
            }

            if ($credit_sum >= $student['Curriculum']['minimum_credit_points']) {
                //Now the student fullfill the minimum credit hour requirement
                $incomplete_grade = false;
                $invalid_grade = false;
                $cid = $student['Curriculum']['id'];

                if (!isset($filtered_students[$cid])) {
                    $filtered_students[$cid][0]['Curriculum'] = $student['Curriculum'];
                    $filtered_students[$cid][0]['Program'] = $student['Program'];
                    $filtered_students[$cid][0]['Department'] = $student['Department'];
                }

                $index = count($filtered_students[$cid]);
                $filtered_students[$cid][$index]['Student'] = $student['Student'];
                $filtered_students[$cid][$index]['ProgramType'] = $student['ProgramType'];
                $filtered_students[$cid][$index]['credit_taken'] = $credit_sum;
                $filtered_students[$cid][$index]['disqualification'] = null;

                //Check: 1) All registered course grade is submitted and 2) A valid grade for each registration
                foreach ($student['CourseRegistration'] as $key => $course_registration) {
                    if (!$this->Student->CourseRegistration->isCourseDroped($course_registration['id']) && $course_registration['PublishedCourse']['drop'] == 0) {
                        $grade_detail = $this->Student->CourseRegistration->getCourseRegistrationLatestApprovedGradeDetail($course_registration['id']);
                        $courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_registration['id'], $course_registration['student_id'], 0);

                        if ($courseRepeated['repeated_old']) {
                            continue;
                        }
                        if (empty($grade_detail) && !$incomplete_grade) {
                            $filtered_students[$cid][$index]['disqualification'][] = 'Student has incomplete grade. All student exam grade should be submitted and approved by both department and registrar.';
                            debug($incomplete_grade);
                            debug($course_registration['id']);
                            $incomplete_grade = true;
                            $donot_consider_cgpa = true;
                        } elseif (!$invalid_grade && isset($grade_detail['ExamGrade']['grade']) && (strcasecmp(
                                    $grade_detail['ExamGrade']['grade'],
                                    'NG'
                                ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'DO') == 0 || strcasecmp(
                                    $grade_detail['ExamGrade']['grade'],
                                    'I'
                                ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp(
                                    $grade_detail['ExamGrade']['grade'],
                                    'W'
                                ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp(
                                    $grade_detail['ExamGrade']['grade'],
                                    'Fail'
                                ) == 0)) {
                            $filtered_students[$cid][$index]['disqualification'][] = 'Student has invalid grade. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
                            $invalid_grade = true;
                            $donot_consider_cgpa = true;
                            debug($grade_detail);
                        }
                    }
                }

                //Check: 1) All added course grade is submitted and 2) A valid grade for each add
                foreach ($student['CourseAdd'] as $key => $course_add) {
                    $grade_detail = $this->Student->CourseAdd->getCourseAddLatestApprovedGradeDetail($course_add['id']);
                    $courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_add['id'], $course_add['student_id'], 0);

                    if ($courseRepeated['repeated_old']) {
                        continue;
                    }

                    if (empty($grade_detail) && !$incomplete_grade) {
                        $filtered_students[$cid][$index]['disqualification'][] = 'Student has incomplete grade. All student exam grade should be submitted and approved by both department and registrar.';
                        debug($incomplete_grade);
                        debug($course_registration['id']);
                        $incomplete_grade = true;
                        $donot_consider_cgpa = true;
                    } elseif (!$invalid_grade && isset($grade_detail['ExamGrade']['grade']) && (strcasecmp(
                                $grade_detail['ExamGrade']['grade'],
                                'NG'
                            ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'DO') == 0 || strcasecmp(
                                $grade_detail['ExamGrade']['grade'],
                                'I'
                            ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'F') == 0 || strcasecmp(
                                $grade_detail['ExamGrade']['grade'],
                                'W'
                            ) == 0 || strcasecmp($grade_detail['ExamGrade']['grade'], 'Fx') == 0 || strcasecmp(
                                $grade_detail['ExamGrade']['grade'],
                                'Fail'
                            ) == 0)) {
                        $filtered_students[$cid][$index]['disqualification'][] = 'Student has invalid grade. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
                        $invalid_grade = true;
                        $donot_consider_cgpa = true;
                        debug($grade_detail);
                    }
                    //debug($grade_detail);
                }

                //Check: All mandatory courses is taken
                //debug($course_categories);
                foreach ($course_categories as $category_name => $course_category) {
                    if ($course_category['taken_credit'] < $course_category['mandatory_credit']) {
                        $filtered_students[$cid][$index]['disqualification'][] = 'According to the curriculum, the student is expected to take a minimum of ' . $course_category['mandatory_credit'] . ' credit hours from ' . $category_name . ' course category. Currently the student takes only ' . $course_category['taken_credit'] . ' credit hours.';
                        $donot_consider_cgpa = true;
                    }
                }
                // debug($student['Student']);

                //Check: A minimum cgpa is achieved
                $minimum_cgpa = $this->Student->Program->GraduationRequirement->getMinimumGraduationCGPA($student['Student']['program_id'], $student['Student']['admissionyear']);

                $last_status = $this->Student->StudentExamStatus->find('first', array(
                    'conditions' => array(
                        'StudentExamStatus.student_id' => $student['Student']['id']
                    ),
                    'order' => array(
                        'StudentExamStatus.created DESC'
                    ),
                    'recursive' => -1
                ));

                if (!$donot_consider_cgpa && !empty($last_status) && $last_status['StudentExamStatus']['cgpa'] < $minimum_cgpa) {
                    $filtered_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
                    $filtered_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
                    $filtered_students[$cid][$index]['disqualification'][] = 'The student need to achieve a minimum of ' . $minimum_cgpa . ' CGPA point. Currently the student has ' . $last_status['StudentExamStatus']['cgpa'] . ' CGPA point.';
                } elseif (!empty($last_status)) {
                    $filtered_students[$cid][$index]['cgpa'] = $last_status['StudentExamStatus']['cgpa'];
                    $filtered_students[$cid][$index]['mcgpa'] = $last_status['StudentExamStatus']['mcgpa'];
                } else {
                    $filtered_students[$cid][$index]['cgpa'] = null;
                    $filtered_students[$cid][$index]['mcgpa'] = null;
                }
            }
        }
        return $filtered_students;
    }
}
