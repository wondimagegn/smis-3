<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DepartmentTransfersTable extends Table
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

        $this->setTable('department_transfers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);
        $this->belongsTo('FromDepartments', [
            'foreignKey' => 'from_department_id',
        ]);
        $this->belongsTo('ToColleges', [
            'foreignKey' => 'to_college_id',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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
            ->scalar('minute_number')
            ->maxLength('minute_number', 20)
            ->allowEmptyString('minute_number');

        $validator
            ->integer('sender_department_approval')
            ->allowEmptyString('sender_department_approval');

        $validator
            ->scalar('sender_department_remark')
            ->allowEmptyString('sender_department_remark');

        $validator
            ->dateTime('sender_department_approval_date')
            ->allowEmptyDateTime('sender_department_approval_date');

        $validator
            ->scalar('sender_department_approval_by')
            ->maxLength('sender_department_approval_by', 36)
            ->allowEmptyString('sender_department_approval_by');

        $validator
            ->date('transfer_request_date')
            ->requirePresence('transfer_request_date', 'create')
            ->notEmptyDate('transfer_request_date');

        $validator
            ->integer('receiver_department_approval')
            ->allowEmptyString('receiver_department_approval');

        $validator
            ->dateTime('receiver_department_approval_date')
            ->allowEmptyDateTime('receiver_department_approval_date');

        $validator
            ->scalar('receiver_department_remark')
            ->allowEmptyString('receiver_department_remark');

        $validator
            ->scalar('receiver_department_approval_by')
            ->maxLength('receiver_department_approval_by', 36)
            ->allowEmptyString('receiver_department_approval_by');

        $validator
            ->integer('sender_college_approval')
            ->allowEmptyString('sender_college_approval');

        $validator
            ->dateTime('sender_college_approval_date')
            ->allowEmptyDateTime('sender_college_approval_date');

        $validator
            ->scalar('sender_college_remark')
            ->allowEmptyString('sender_college_remark');

        $validator
            ->scalar('sender_college_approval_by')
            ->maxLength('sender_college_approval_by', 36)
            ->allowEmptyString('sender_college_approval_by');

        $validator
            ->integer('receiver_college_approval')
            ->allowEmptyString('receiver_college_approval');

        $validator
            ->dateTime('receiver_college_approval_date')
            ->allowEmptyDateTime('receiver_college_approval_date');

        $validator
            ->scalar('receiver_college_remark')
            ->allowEmptyString('receiver_college_remark');

        $validator
            ->scalar('receiver_college_approval_by')
            ->maxLength('receiver_college_approval_by', 36)
            ->allowEmptyString('receiver_college_approval_by');

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
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['from_department_id'], 'FromDepartments'));
        $rules->add($rules->existsIn(['to_college_id'], 'ToColleges'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }

    function attachSemesterAttended($data = null)
    {
        if (!empty($data)) {
            foreach ($data as $i => &$v) {
                $v['DepartmentTransfer']['semester_attended'] = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester($v['DepartmentTransfer']['student_id']);
            }
        }
        return $data;
    }

    function getTransferedCourseCredit($student_id)
    {
        $transferedDepartment = $this->find('all', array(
            'conditions' => array(
                'DepartmentTransfer.student_id' => $student_id,
                'DepartmentTransfer.sender_department_approval' => 1,
                'DepartmentTransfer.sender_college_approval' => 1,
                'DepartmentTransfer.receiver_department_approval' => 1,
                'DepartmentTransfer.receiver_college_approval' => 1,
            )
        ));

        $totalTransfered = 0;

        if (!empty($transferedDepartment)) {
            foreach ($transferedDepartment as $k => $v) {
                // find all courses registered in the previous department
                $transferSql = "SELECT cr.id, cr.student_id, cr.published_course_id, pc.course_id, pc.department_id FROM  `course_registrations` AS cr, published_courses AS pc WHERE pc.department_id=" . $v['DepartmentTransfer']['from_department_id'] . " AND pc.id = cr.published_course_id AND cr.student_id=" . $v['DepartmentTransfer']['student_id'] . "";
                $transferResult = $this->query($transferSql);

                if (!empty($transferResult)) {
                    foreach ($transferResult as $kk => $vv) {
                        if (isset($vv['cr']['id']) && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($vv['cr']['id'], 1, 1)) {
                            $totalTransfered += $totalTransfered;
                        }
                    }
                }

                $transferAddSql = "SELECT cr.id, cr.student_id, cr.published_course_id, pc.course_id, pc.department_id FROM  `course_adds` AS cr, published_courses AS pc WHERE pc.department_id=" . $v['DepartmentTransfer']['from_department_id'] . " AND pc.id = cr.published_course_id AND cr.student_id=" . $v['DepartmentTransfer']['student_id'] . "";
                $transferAddResult = $this->query($transferAddSql);

                if (!empty($transferAddResult)) {
                    foreach ($transferAddResult as $kk => $vv) {
                        if (isset($vv['cr']['id']) && $this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($vv['cr']['id'], 0, 1)) {
                            $totalTransfered += $totalTransfered;
                        }
                    }
                }
            }
        }

        return $totalTransfered;
    }


    function chceckStudentForDepartmentTransfer($student_id = null, $from_student = '')
    {
        /***
        2. Filter students who takes a minimum of the specified credit hours on their curriculum
        //fully registered, add, exempt, substitute all courses from their curriculum
        3. Decide who is going to be included in the senate list and generate justification for those who will not be on the senate list.
        RULE:
        1. No F, NG, I, DO, W, and check if the course are not repeated.getCourseRepetation() TODO
        2. All required course should be taken (it is by category)

        3. A minimum of x CGPA
        4. Return the list
         ***/


        $validStudentID = $this->Student->find('count', array('conditions' => array('Student.id' => $student_id)));

        debug($validStudentID);

        $percentCompletedCredit = 50;

        if ($validStudentID) {
            $options['conditions'][] = array('Student.id' => $student_id);
        } else {
            return 'Student ID Not Found';
        }

        $studentCurriculumID = $this->Student->field('curriculum_id', array('Student.id' => $student_id));

        debug($studentCurriculumID);

        if (is_null($studentCurriculumID) || empty($studentCurriculumID)) {
            return 'Curriculum not attached';
        }

        $options['conditions']['Student.graduated'] = 0;

        //$options['conditions']['Student.department_id'] = $department_id;
        $options['conditions'][] = 'Student.curriculum_id IS NOT NULL';



        debug($studentCurriculumID);

        $minimumPointofCurriculum = $this->Student->Curriculum->find('first', array(
            'conditions' => array(
                'Curriculum.id' => $studentCurriculumID,
            ),
            'order' => array('Curriculum.minimum_credit_points' => 'ASC'),
            'recursive' => -1,
        ));

        debug($minimumPointofCurriculum['Curriculum']['id']);
        debug($minimumPointofCurriculum['Curriculum']['name']);
        debug($minimumPointofCurriculum['Curriculum']['minimum_credit_points']);

        $courseNotUsedInGPA = $this->Student->Curriculum->Course->find('all', array(
            'conditions' => array(
                'Course.curriculum_id' => $studentCurriculumID,
                'GradeType.used_in_gpa' => 0,
            ),
            'contain' => array(
                'GradeType',
                'Curriculum' => array('id', 'program_id')
            ),
            'order' => array('Course.credit' => 'DESC'),
            'fields' => array('Course.id', 'Course.credit'),
            'recursive' => -1,
        ));

        debug($courseNotUsedInGPA);

        $notUsedInCGPACreditSum = 0;

        if (!empty($courseNotUsedInGPA)) {
            foreach ($courseNotUsedInGPA as $ckey => $cval) {
                $notUsedInCGPACreditSum += $cval['Course']['credit'];
            }
        }

        debug($notUsedInCGPACreditSum);

        //$studentquery = ' and student_id=20816';

        //$notUsedInCGPACreditSum  = $this->query("SELECT SUM(credit) FROM courses WHERE curriculum_id = $studentCurriculumID");

        debug($notUsedInCGPACreditSum);

        $exptionPoint = 0;

        debug($exptionPoint);

        /* if (isset($studentnumber) && !empty($studentnumber)) {
            $studentnumberQuoted = "'" . trim($studentnumber). "%'";
            $studentLists = $this->query(
                "SELECT student_id, SUM(credit_hour_sum) FROM  student_exam_statuses
                WHERE student_id IN (select id from students where graduated = 0 and department_id = $department_id and studentnumber LIKE $studentnumberQuoted and program_id = $program_id)
                and student_id  NOT IN (SELECT student_id FROM senate_lists where student_id IN (select id from students where studentnumber LIKE $studentnumberQuoted))
                and student_id NOT IN (SELECT student_id FROM graduate_lists where student_id IN (select id from students where studentnumber LIKE $studentnumberQuoted))
                GROUP BY student_id HAVING SUM(credit_hour_sum) >= " . ($minimumPointofCurriculum['Curriculum']['minimum_credit_points'] - $exptionPoint - $notUsedInCGPACreditSum) * ($percentCompletedCredit) . ""
            );
        } */

        $studentLists = $this->query("SELECT student_id, SUM(credit_hour_sum) FROM  student_exam_statuses WHERE student_id = $student_id GROUP BY student_id HAVING SUM(credit_hour_sum) <= " . ($minimumPointofCurriculum['Curriculum']['minimum_credit_points'] - $exptionPoint - $notUsedInCGPACreditSum) * ($percentCompletedCredit) . "");

        debug($studentLists);

        // consider only those students who have registered and achieved the minimum credit hours without status since there are courses which doenst require status
        debug(count($studentLists));

        $student_ids = array();

        if (!empty($studentLists)) {
            foreach ($studentLists as $id) {
                debug($id);
                debug($notUsedInCGPACreditSum);
                debug($minimumPointofCurriculum['Curriculum']['minimum_credit_points']);

                if (($id[0]['SUM(credit_hour_sum)'] + $notUsedInCGPACreditSum) >= $minimumPointofCurriculum['Curriculum']['minimum_credit_points']) {
                    $student_ids[$id['student_exam_statuses']['student_id']] = $id['student_exam_statuses']['student_id'];
                } else {
                    $student_ids[$id['student_exam_statuses']['student_id']] = $id['student_exam_statuses']['student_id'];
                }
            }
        }

        debug($student_ids);

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
            )
        );

        $options['fields'] = array('Student.curriculum_id', 'Student.full_name', 'Student.first_name', 'Student.middle_name', 'Student.last_name', 'Student.studentnumber', 'Student.admissionyear', 'Student.gender', 'Student.academicyear', 'Student.student_national_id');
        $options['order'] = array('Student.first_name' => 'ASC', 'Student.middle_name' => 'ASC', 'Student.last_name' => 'ASC');
        $genericErrorMessage = array();

        //$options['limit']=30;

        $students = $this->Student->find('all', $options);
        //debug($students);
        //debug(count($students));

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
                                    if (!isset($course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'])) {
                                        $course_ids = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_registration['PublishedCourse']['course_id'], $student['Curriculum']['id']);
                                        $course_cat_mapped = ClassRegistry::init('EquivalentCourse')->courseEquivalentCategory($course_registration['PublishedCourse']['course_id'], $student['Curriculum']['id']);
                                        //debug($course_cat_mapped);

                                        if (!empty($course_cat_mapped)) {
                                            $course_categories[$course_cat_mapped]['taken_credit'] += $course_registration['PublishedCourse']['Course']['credit'];
                                        } else {
                                            $course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] = 0;
                                        }
                                    } else {
                                        $course_categories[$course_registration['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] += $course_registration['PublishedCourse']['Course']['credit'];
                                    }
                                }
                            } else {
                                $sumexcludingfromreg += $course_registration['PublishedCourse']['Course']['credit'];

                                /* debug($course_registration['PublishedCourse']['Course']['course_title']);
                                debug($course_registration['PublishedCourse']['Course']['CourseCategory']['name']);
                                debug($this->Student->CourseRegistration->isCourseDroped($course_registration['id']));
                                debug($course_registration['PublishedCourse']['drop']);
                                debug($this->Student->CourseRegistration->ExamGrade->isRegistrationAddForFirstTime($course_registration['id'], 1, 1));
                                debug($course_registration); */
                            }
                        }
                    }

                    debug($justsummedandtaken);
                    debug($credit_sum);
                    debug($sumexcludingfromreg);

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

                                    if (isset($course_cat_mapped)) {
                                        $course_categories[$course_cat_mapped]['taken_credit'] += $course_add['PublishedCourse']['Course']['credit'];
                                    }
                                } else {
                                    if (!isset($course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'])) {
                                        //debug($course_add['PublishedCourse']['Course']['CourseCategory']['name']);
                                        $course_ids = ClassRegistry::init('EquivalentCourse')->validEquivalentCourse($course_add['PublishedCourse']['course_id'], $student['Curriculum']['id']);

                                        $course_cat_mapped = ClassRegistry::init('Course')->find('first', array('conditions' => array('Course.id' => $course_ids), 'contain' => array('CourseCategory')));
                                        //debug($course_ids);
                                        $course_cat_mapped = ClassRegistry::init('EquivalentCourse')->courseEquivalentCategory($course_add['PublishedCourse']['course_id'], $student['Curriculum']['id']);

                                        if (isset($course_cat_mapped)) {
                                            $course_categories[$course_cat_mapped]['taken_credit'] += $course_add['PublishedCourse']['Course']['credit'];
                                        } else {
                                            $course_categories[$course_add['PublishedCourse']['Course']['CourseCategory']['name']]['taken_credit'] = 0;
                                        }
                                    } else {
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
                                $sumexcludingfromadd += $course_add['PublishedCourse']['Course']['credit'];
                            }
                        }
                    }

                    debug($addSum);
                    debug($credit_sum);
                    debug($sumexcludingfromadd);

                    debug($student['Curriculum']['minimum_credit_points']);
                    debug($student['Student']['id']);
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

                    debug($studentAttachedCurriculumIds);

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
                    debug($all_exempted_courses);
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

                    debug($only_exempted_credit);
                    //  debug($student['Student']['id']);
                    debug($credit_sum);
                    debug($student['Curriculum']['minimum_credit_points']);

                    //die;
                    if ($credit_sum <= $student['Curriculum']['minimum_credit_points']) {
                        //Now the student fullfill the minimum credit hour requirement
                        debug($student['Curriculum']['name']);
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
                        $filtered_students[$cid][$index]['Curriculum'] = $student['Curriculum']['name'];
                        $filtered_students[$cid][$index]['minimum_credit_points'] = $student['Curriculum']['minimum_credit_points'];
                        $filtered_students[$cid][$index]['credits_added'] = $addSum;
                        $filtered_students[$cid][$index]['credits_excluding_from_add'] = $sumexcludingfromadd;
                        $filtered_students[$cid][$index]['credits_excluding_from_registration'] = $sumexcludingfromreg;
                        $filtered_students[$cid][$index]['credits_registrered'] = $addSum;
                        $filtered_students[$cid][$index]['credit_taken'] = $credit_sum;
                        $filtered_students[$cid][$index]['disqualification'] = null;
                        $filtered_students[$cid][$index]['ExemptedCredit'] = $this->Student->CourseExemption->getStudentCourseExemptionCredit($student['Student']['id']);

                        $filtered_students[$cid][$index]['Courses'] = array();

                        $courses_count = 1;

                        if ($credit_sum > ($student['Curriculum']['minimum_credit_points'] * ($percentCompletedCredit / 100))) {
                            $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'Student has ' : ' You have ') . ' taken more than half of the courses from the attached curriculum.';
                            continue;
                        }

                        //Check: 1) All registered course grade is submitted and 2) A valid grade for each registration

                        if (isset($student['CourseRegistration']) && !empty($student['CourseRegistration'])) {
                            foreach ($student['CourseRegistration'] as $key => $course_registration) {
                                if (!$this->Student->CourseRegistration->isCourseDroped($course_registration['id']) && $course_registration['PublishedCourse']['drop'] == 0) {
                                    $grade_detail = $this->Student->CourseRegistration->getCourseRegistrationLatestApprovedGradeDetail($course_registration['id']);
                                    $courseRepeated = $this->Student->CourseRegistration->ExamGrade->getCourseRepetation($course_registration['id'], $course_registration['student_id'], 1);
                                    //debug($courseRepeated);

                                    if ($courseRepeated['repeated_old']) {
                                        debug($course_registration);
                                        continue;
                                    }

                                    //$filtered_students[$cid][$index]['Courses'][$courses_count]['Course'] = $course_registration;
                                    //$filtered_students[$cid][$index]['Courses'][$courses_count]['Grade'] = $grade_detail;
                                    //$filtered_students[$cid][$index]['Courses'][$courses_count]['Repeatition'][] = $courseRepeated;

                                    /// add exam grade pass and fail check, recently students with the fail grade( like D set as fail grade) appear in seenate list.

                                    if (empty($grade_detail) && !$incomplete_grade) {
                                        debug($grade_detail);
                                        debug($course_registration['id']);
                                        $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student has ' : ' You have ') . ' incomplete grade. All of ' . (empty($from_student) ? 'the student' : 'your') . ' grade should be submitted and approved by both department and registrar.';
                                        debug($incomplete_grade);
                                        debug($course_registration['id']);
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
                                        debug($invalid_grade);
                                        debug($grade_detail['ExamGrade']);
                                        //$filtered_students[$cid][$index]['disqualification'][] = 'Student has invalid grade. Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                        if (isset($grade_detail['ExamGrade']['course_registration_id']) && $course_registration['id'] == $grade_detail['ExamGrade']['course_registration_id']) {
                                            $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student has ' : ' You have ') . ' invalid grade (' . $grade_detail['ExamGrade']['grade'] . ') in ' . $course_registration['PublishedCourse']['academic_year'] . ' semester ' . $course_registration['PublishedCourse']['semester'] . ' for ' . $course_registration['PublishedCourse']['Course']['course_title'] . ' (' . $course_registration['PublishedCourse']['Course']['course_code'] . '). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                        } else {
                                            $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student has ' : ' You have ') . ' invalid grade. Any  of ' . (empty($from_student) ? 'the student' : 'your') . ' grade should not contain NG, I, DO, W, FAIL, Fx/F.';
                                        }
                                        debug($courseRepeated);
                                        debug($grade_detail);
                                        $invalid_grade = true;
                                        $donot_consider_cgpa = true;
                                    }
                                    $courses_count++;
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
                                    debug($course_add);
                                    continue;
                                }

                                //$filtered_students[$cid][$index]['Courses'][$courses_count]['Course'] = $course_add;
                                //$filtered_students[$cid][$index]['Courses'][$courses_count]['Grade'] = $grade_detail;
                                //$filtered_students[$cid][$index]['Courses'][$courses_count]['Repeatition'][] = $courseRepeated;

                                /// add exam grade pass and fail check, recently students with the fail grade( like D set as fail grade) appear in seenate list.

                                if (empty($grade_detail) && !$incomplete_grade) {
                                    $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student has ' : ' You have ') . ' incomplete grade. All of ' . (empty($from_student) ? 'the student' : 'your') . ' grade should be submitted and approved by both department and registrar.';
                                    debug($incomplete_grade);
                                    debug($course_add['id']);
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
                                    //$filtered_students[$cid][$index]['disqualification'][] = 'Student has invalid grade(' . $grade_detail['ExamGrade']['grade'] . ') in '. $course_add['PublishedCourse']['academic_year'] .' semester ' . $course_add['PublishedCourse']['semester'] . ' for ' . $course_add['PublishedCourse']['Course']['course_title'] .' (' . $course_add['PublishedCourse']['Course']['course_code'] .'). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
                                    if (isset($grade_detail['ExamGrade']['course_add_id']) && $course_add['id'] == $grade_detail['ExamGrade']['course_add_id']) {
                                        $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student has ' : ' You have ') . ' invalid grade(' . $grade_detail['ExamGrade']['grade'] . ') in ' . $course_add['PublishedCourse']['academic_year'] . ' semester ' . $course_add['PublishedCourse']['semester'] . ' for ' . $course_add['PublishedCourse']['Course']['course_title'] . ' (' . $course_add['PublishedCourse']['Course']['course_code'] . '). Any of the student grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
                                    } else {
                                        $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student has ' : ' You have ') . ' invalid grade. Any of ' . (empty($from_student) ? 'the student' : 'your') . ' grade should not contain NG, I, DO, W, FAIL, Fx and/or F.';
                                    }
                                    $invalid_grade = true;
                                    $donot_consider_cgpa = true;
                                    debug($grade_detail);
                                }
                                $courses_count++;
                            }
                        }


                        //Check: All mandatory courses is taken
                        //debug($course_categories);

                        if (isset($course_categories) && !empty($course_categories)) {
                            foreach ($course_categories as $category_name => $course_category) {
                                if ($course_category['taken_credit'] < $course_category['mandatory_credit']) {
                                    //$filtered_students[$cid][$index]['disqualification'][] = 'According to the curriculum, the student is expected to take a minimum of ' . $course_category['mandatory_credit'] . ' credit hours from ' . $category_name . ' course category. Currently the student only took ' . $course_category['taken_credit'] . ' credit hours.';
                                    $donot_consider_cgpa = true;
                                }
                            }
                        }

                        //Check: A minimum cgpa is achieved
                        $minimum_cgpa = 1.75; //$this->Student->Program->GraduationRequirement->getMinimumGraduationCGPA($program_id, $student['Student']['admissionyear']);

                        $last_status = $this->Student->StudentExamStatus->find('first', array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $student['Student']['id']
                            ),
                            //'order' => array('StudentExamStatus.created' => 'DESC'),
                            'order' => array('StudentExamStatus.academic_year' => 'DESC', 'StudentExamStatus.semester' => 'DESC', 'StudentExamStatus.created' => 'DESC'),
                            'recursive' => -1
                        ));


                        if (!empty($last_status) && $last_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                            $filtered_students[$cid][$index]['disqualification'][] = (empty($from_student) ? 'The Student ' : ' You are ') . ' dismissed in .' . $last_status['StudentExamStatus']['academic_year'] . ' semester ' . $last_status['StudentExamStatus']['semester'] . '. The student is advised to apply for readmission.';
                            continue;
                        }

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
                    } else {
                        $genericErrorMessage['CourseCategory'] = $course_categories;
                        $genericErrorMessage['ExemptionSum'] = $only_exempted_credit;
                    }
                }
            }
        }

        $filtered_students = array_values($filtered_students);

        if (isset($filtered_students[0][0]['Curriculum'])) {
            unset($filtered_students[0][0]);
        }

        return $filtered_students;
    }
}
