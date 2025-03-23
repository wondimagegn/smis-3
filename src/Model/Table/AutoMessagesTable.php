<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AutoMessagesTable extends Table
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

        $this->setTable('auto_messages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'propertyName'=>'User'
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
            ->scalar('id')
            ->maxLength('id', 36)
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->notEmptyString('message');

        $validator
            ->boolean('is_read')
            ->notEmptyString('is_read');

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

        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    function getMessages($user_id = null)
    {
        $auto_messages = $this->find()
            ->where([
                'AutoMessages.is_read' => 0,
                'AutoMessages.user_id' => $user_id
            ])
            ->order(['AutoMessages.created' => 'DESC'])
            ->limit(AUTO_MESSAGE_LIMIT)
            ->enableHydration(false) // Disable object hydration to return an array like CakePHP 2.x
            ->toArray();
        return $auto_messages;
    }

    function sendMessage($user_id = null, $message = null, $type = null)
    {

        $auto_message = array();
        $auto_message['message'] = $message;

        if ($type === 1) {
            $auto_message['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message['message'] . '</p>';
        } elseif ($type === -1) {
            $auto_message['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message['message'] . '</p>';
        } elseif ($type === 0) {
            $auto_message['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="on-process">' . $auto_message['message'] . '</p>';
        }

        $auto_message['is_read'] = 0;
        $auto_message['user_id'] = $user_id;

        $this->save($auto_message);
    }

    function alumniRegistrationMessage($message, $type = null)
    {

        $auto_message = array();

        $usersLists = ClassRegistry::init('User')->find(
            'list',
            array('conditions' => array('User.role_id' => 13), 'fields' => array('User.id', 'User.id'))
        );

        if (!empty($usersLists)) {
            foreach ($usersLists as $usr) {
                $auto_message['message'] = $message;

                if ($type === 1) {
                    $auto_message['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message['message'] . '</p>';
                } elseif ($type === -1) {
                    $auto_message['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message['message'] . '</p>';
                } elseif ($type === 0) {
                    $auto_message['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="on-process">' . $auto_message['message'] . '</p>';
                }

                $auto_message['is_read'] = 0;
                $auto_message['user_id'] = $usr;

                $this->save($auto_message);
            }
        }
    }

    function postMessageToGroup($role_id = null, $subject = null, $message = null, $data = null)
    {

        debug($message);

        $auto_message['message'] = '<h7>' . $subject . '</h7><p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . nl2br(
                htmlentities($message)
            ) . '</p>';
        $usersMessage['AutoMessage'] = $this->User->getListOfUsersRole($role_id, $auto_message['message'], $data);

        debug($usersMessage);

        if (!empty($usersMessage)) {
            return $this->saveAll($usersMessage['AutoMessage'], array('validate' => false));
        }

        return false;
    }

    function sendNotificationOnRegistrarGradeConfirmation($confirmed_grades = null)
    {

        //Notification to student
        //debug($confirmed_grades);
        $grade_ids = array();
        $auto_message = array();

        if (!empty($confirmed_grades) && $confirmed_grades[0]['registrar_approval'] == 1) {
            foreach ($confirmed_grades as $key => $grade) {
                $grade_ids[] = $grade['id'];
            }

            $grade_details = ClassRegistry::init('ExamGrade')->find('all', array(
                'conditions' => array(
                    'ExamGrade.id' => $grade_ids
                ),
                'contain' => array(
                    'CourseAdd' => array('PublishedCourse' => array('Course'), 'Student'),
                    'CourseRegistration' => array('PublishedCourse' => array('Course'), 'Student')
                )
            ));

            //Student notification
            if (!empty($grade_details)) {
                foreach ($grade_details as $key => $grade_detail) {
                    if (!empty($grade_detail['ExamGrade']['course_registration_id']) && $grade_detail['ExamGrade']['course_registration_id'] > 0 && isset($grade_detail['CourseRegistration']['Student']) && !empty($grade_detail['CourseRegistration']['Student']['user_id'])) {
                        $index = count($auto_message);
                        $auto_message[$index]['message'] = 'You got <strong>' . $grade_detail['ExamGrade']['grade'] . '</strong> for the course <u>' . $grade_detail['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $grade_detail['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ')</u>.';
                        $auto_message[$index]['is_read'] = 0;
                        $auto_message[$index]['user_id'] = $grade_detail['CourseRegistration']['Student']['user_id'];
                    } elseif (!empty($grade_detail['ExamGrade']['course_add_id']) && $grade_detail['ExamGrade']['course_add_id'] > 0 && isset($grade_detail['CourseAdd']['Student']) && !empty($grade_detail['CourseAdd']['Student']['user_id'])) {
                        $index = count($auto_message);
                        $auto_message[$index]['message'] = 'You got <strong>' . $grade_detail['ExamGrade']['grade'] . '</strong> for the course <u>' . $grade_detail['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $grade_detail['CourseAdd']['PublishedCourse']['Course']['course_code'] . ')</u>.';
                        $auto_message[$index]['is_read'] = 0;
                        $auto_message[$index]['user_id'] = $grade_detail['CourseAdd']['Student']['user_id'];
                    }
                }
            }
        }

        //Instructor notification
        $index = count($auto_message);

        $course_instructor = ClassRegistry::init('PublishedCourse')->getInstructorByExamGradeId(
            $confirmed_grades[0]['id']
        );
        $course = ClassRegistry::init('Course')->getCourseByExamGradeId($confirmed_grades[0]['id']);
        $section = ClassRegistry::init('Section')->getSectionByExamGradeId($confirmed_grades[0]['id']);
        $published_course = ClassRegistry::init('PublishedCourse')->getPublishedCourseByExamGradeId(
            $confirmed_grades[0]['id']
        );

        if (!empty($course_instructor) && $course_instructor['user_id'] != "") {
            $auto_message[$index]['message'] = 'Your <u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> grade submission is ' . ($confirmed_grades[0]['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' by the registrar for <u>' . ($section['name']) . '</u> section. <a href="/exam_results/add/' . $published_course['id'] . '">View Grade</a>';

            if ($confirmed_grades[0]['registrar_approval'] == -1) {
                $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[$index]['message'] . '</p>';
            } elseif ($confirmed_grades[0]['registrar_approval'] == 1) {
                $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[$index]['message'] . '</p>';
            }

            $auto_message[$index]['is_read'] = 0;
            $auto_message[$index]['user_id'] = $course_instructor['user_id'];
        }

        //Department notification
        $grade_ids = array();

        foreach ($confirmed_grades as $key => $grade) {
            $grade_ids[] = $grade['id'];
        }

        $department_approved_bys = ClassRegistry::init('ExamGrade')->find('list', array(
            'conditions' => array(
                'ExamGrade.id' => $grade_ids
            ),
            'fields' => array('ExamGrade.department_approved_by'),
            'recursive' => -1
        ));

        $department_approved_bys = array_unique($department_approved_bys);

        if (!empty($department_approved_bys)) {
            foreach ($department_approved_bys as $key => $department_approved_by) {
                if (!empty($department_approved_by)) {
                    $index = count($auto_message);

                    if (isset($published_course['department_id']) && !empty($published_course['department_id'])) {
                        $approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                    } elseif (isset($published_course['college_id']) && !empty($published_course['college_id'])) {
                        //$approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                        $approvalpage = '/examGrades/approve_freshman_grade_submission';
                    } else {
                        $approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                    }

                    $approvalpage = '/examGrades/approve_non_freshman_grade_submission';

                    $auto_message[$index]['message'] = '<u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> course grade is ' . ($confirmed_grades[0]['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' by the registrar for <u>' . ($section['name']) . '</u> section. <a href="' . $approvalpage . '/' . $published_course['id'] . '">View Grade</a>';

                    if ($confirmed_grades[0]['registrar_approval'] == -1) {
                        $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[$index]['message'] . '</p>';
                    } elseif ($confirmed_grades[0]['registrar_approval'] == 1) {
                        $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[$index]['message'] . '</p>';
                    }

                    $auto_message[$index]['is_read'] = 0;
                    $auto_message[$index]['user_id'] = $department_approved_by;
                }
            }
        }

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
        //debug($auto_message);
        //debug($grade_details);
    }

    function sendNotificationOnRegistrarGradeRollback(
        $rolledback_grades = null,
        $rolled_back_by = '',
        $rolled_back_by_id = '',
        $department_approved_bys
    ) {

        //Notification to student
        //debug($confirmed_grades);
        $grade_ids = array();
        $auto_message = array();

        debug($rolledback_grades);

        if (!empty($rolledback_grades)) {
            $grade_details = ClassRegistry::init('ExamGrade')->find('all', array(
                'conditions' => array(
                    'ExamGrade.id' => $rolledback_grades
                ),
                'contain' => array(
                    'CourseAdd' => array(
                        'PublishedCourse' => array('Course'),
                        'Student'
                    ),
                    'CourseRegistration' => array(
                        'PublishedCourse' => array('Course'),
                        'Student'
                    )
                )
            ));

            //Student notification
            if (!empty($grade_details)) {
                foreach ($grade_details as $key => $grade_detail) {
                    if (!empty($grade_detail['ExamGrade']['course_registration_id']) && $grade_detail['ExamGrade']['course_registration_id'] > 0 && isset($grade_detail['CourseRegistration']['Student']) && !empty($grade_detail['CourseRegistration']['Student']['user_id'])) {
                        $index = count($auto_message);
                        $auto_message[$index]['message'] = 'Exam grade you got <strong>' . $grade_detail['ExamGrade']['grade'] . '</strong> for the course <u>' . $grade_detail['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $grade_detail['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ')</u>. is rolled back for resubmission by the registrar.';
                        $auto_message[$index]['is_read'] = 0;
                        $auto_message[$index]['user_id'] = $grade_detail['CourseRegistration']['Student']['user_id'];
                    } elseif (!empty($grade_detail['ExamGrade']['course_add_id']) && $grade_detail['ExamGrade']['course_add_id'] > 0 && isset($grade_detail['CourseAdd']['Student']) && !empty($grade_detail['CourseAdd']['Student']['user_id'])) {
                        $index = count($auto_message);
                        $auto_message[$index]['message'] = 'Exam grade you got <strong>' . $grade_detail['ExamGrade']['grade'] . '</strong> for the course <u>' . $grade_detail['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $grade_detail['CourseAdd']['PublishedCourse']['Course']['course_code'] . ')</u>. is rolled back for resubmission by the registrar.';
                        $auto_message[$index]['is_read'] = 0;
                        $auto_message[$index]['user_id'] = $grade_detail['CourseAdd']['Student']['user_id'];
                    }
                }
            }
        } else {
            return;
        }

        //Instructor notification
        $index = count($auto_message);

        $course_instructor = ClassRegistry::init('PublishedCourse')->getInstructorByExamGradeId(
            array_values($rolledback_grades)[0]
        );

        $course = ClassRegistry::init('Course')->getCourseByExamGradeId(array_values($rolledback_grades)[0]);
        $section = ClassRegistry::init('Section')->getSectionByExamGradeId(array_values($rolledback_grades)[0]);
        $published_course = ClassRegistry::init('PublishedCourse')->getPublishedCourseByExamGradeId(
            array_values($rolledback_grades)[0]
        );

        //debug($published_course);

        $rolledbackGrades_count = count($grade_details);

        if (!empty($course_instructor) && $course_instructor['user_id'] != "") {
            $auto_message[$index]['message'] = 'Grade you submitted for <u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> for <u>' . ($section['name']) . '</u> section is rolled back for ' . ($rolledbackGrades_count . ' ' . ($rolledbackGrades_count == 1 ? 'student' : 'students')) . ' by ' . (!empty($rolled_back_by) ? $rolled_back_by : 'the registrar') . ' for your resubmission. Please note that you need to cancel the submitted grades in order to adjust results and submit again. <a href="/exam_results/add/' . $published_course['id'] . '">Resubmit Grade</a>';
            $auto_message[$index]['is_read'] = 0;
            $auto_message[$index]['user_id'] = $course_instructor['user_id'];
        }

        $index = count($auto_message);

        //Department notification
        if (!empty($department_approved_bys)) {
            foreach ($department_approved_bys as $key => $department_approved_by) {
                if (!empty($department_approved_by)) {
                    $index = count($auto_message);

                    /* if (isset($published_course['department_id']) && !empty($published_course['department_id'])) {
                        $approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                    } else if (isset($published_course['college_id']) && !empty($published_course['college_id'])) {
                        //$approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                        $approvalpage = '/examGrades/approve_freshman_grade_submission';
                    } else {
                        $approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                    } */

                    $approvalpage = '/examGrades/approve_non_freshman_grade_submission';

                    $auto_message[$index]['message'] = '<u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> course grade submitted ' . (isset($course_instructor['full_name']) && !empty($course_instructor['full_name']) ? 'by ' . $course_instructor['full_name'] : '') . ' for <u>' . ($section['name']) . '</u> section is rolled back for ' . ($rolledbackGrades_count . ' ' . ($rolledbackGrades_count == 1 ? 'student' : 'students')) . ' by ' . (!empty($rolled_back_by) ? $rolled_back_by : 'the registrar') . '. Please check with the instructor before approving the grade again. <a href="' . $approvalpage . '/' . $published_course['id'] . '">View Grade</a>';

                    $auto_message[$index]['is_read'] = 0;
                    $auto_message[$index]['user_id'] = $department_approved_by;
                }
            }
        }

        $index = count($auto_message);

        // Registrar notification
        if (!empty($rolled_back_by_id)) {
            $approvalpage = 'confirm_grade_submission';
            $auto_message[$index]['message'] = 'You rolled back ' . ($rolledbackGrades_count . ' ' . ($rolledbackGrades_count == 1 ? 'student' : 'students')) . ' submitted grade ' . (isset($course_instructor['full_name']) && !empty($course_instructor['full_name']) ? 'by ' . $course_instructor['full_name'] : '') . ' for the course <u>' . $course['course_title'] . ' (' . $course['course_code'] . ')</u> form <u>' . ($section['name']) . '</u> section for grade resubmission. <a href="/exam_grades/' . $approvalpage . '/' . $published_course['id'] . '">View Grade</a>';
            $auto_message[$index]['is_read'] = 0;
            $auto_message[$index]['user_id'] = $rolled_back_by_id;
        }

        debug($auto_message);

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function sendNotificationOnInstructorAssignment($publishedCourseId = null)
    {

        $auto_message = array();

        if (!empty($publishedCourseId)) {
            $assignment_details = ClassRegistry::init('CourseInstructorAssignment')->find('all', array(
                'conditions' => array(
                    'CourseInstructorAssignment.published_course_id' => $publishedCourseId,
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Department' => array(
                            'fields' => array('id', 'name')
                        ),
                        'College' => array(
                            'fields' => array('id', 'name')
                        ),
                        'GivenByDepartment' => array(
                            'fields' => array('id', 'name')
                        ),
                        'Program',
                        'ProgramType',
                        'Course'
                    ),
                    'Staff' => array('User', 'Title'),
                    'Section' => array('YearLevel')
                )
            ));

            //Instructor and Course Owner Department Notification
            if (!empty($assignment_details)) {
                foreach ($assignment_details as $key => $assignment_detail) {
                    if (!empty($assignment_detail['Staff']['user_id'])) {
                        $index = count($auto_message);

                        if (empty($assignment_detail['Section']['YearLevel']['name'])) {
                            $auto_message[$index]['message'] = 'You are assigned to <strong><u>' . $assignment_detail['PublishedCourse']['Course']['course_title'] . ' (' . $assignment_detail['PublishedCourse']['Course']['course_code'] . ')' . '</u>  <br />
							Section: ' . $assignment_detail['Section']['name'] . ' <br/> Year Level: Pre <br/>
							Department: ' . $assignment_detail['PublishedCourse']['College']['name'] . ' <br/>
							Year Level: Pre/1st<br/>
							Program: ' . $assignment_detail['PublishedCourse']['Program']['name'] . ' <br/>
							Program Type: ' . $assignment_detail['PublishedCourse']['ProgramType']['name'] . '<br/>
							Academic Year: ' . $assignment_detail['PublishedCourse']['academic_year'] . ' <br/>
							Semester: ' . $assignment_detail['PublishedCourse']['semester'] . '';
                        } else {
                            $auto_message[$index]['message'] = 'You are assigned  to <strong><u>' . $assignment_detail['PublishedCourse']['Course']['course_title'] . ' (' . $assignment_detail['PublishedCourse']['Course']['course_code'] . ')' . '</u> <br />
							Section: ' . $assignment_detail['Section']['name'] . ' <br/>
							Year Level: ' . $assignment_detail['Section']['YearLevel']['name'] . '<br/>
							Department: ' . $assignment_detail['PublishedCourse']['Department']['name'] . ' <br/>
							Program: ' . $assignment_detail['PublishedCourse']['Program']['name'] . ' <br/>
							Program Type: ' . $assignment_detail['PublishedCourse']['ProgramType']['name'] . '<br/>
							Academic Year: ' . $assignment_detail['PublishedCourse']['academic_year'] . ' <br/>
							Semester: ' . $assignment_detail['PublishedCourse']['semester'] . '';
                        }

                        $auto_message[$index]['read'] = 0;
                        $auto_message[$index]['user_id'] = $assignment_detail['Staff']['user_id'];
                    }

                    if (empty($assignment_detail['PublishedCourse']['department_id'])) {
                        $ownerDepartmentUser = ClassRegistry::init('User')->find('first', array(
                            'conditions' => array(
                                'User.is_admin' => 1,
                                'User.role_id' => ROLE_COLLEGE,
                                'User.id IN (select user_id from staffs where college_id=' . $assignment_detail['PublishedCourse']['college_id'] . ')'
                            ),

                            'recursive' => -1
                        ));

                        $index = count($auto_message);

                        $auto_message[$index]['message'] = 'The course you disptached <strong><u>' . $assignment_detail['PublishedCourse']['Course']['course_title'] . ' (' . $assignment_detail['PublishedCourse']['Course']['course_code'] . ')' . '</u>
						to department of ' . $assignment_detail['PublishedCourse']['GivenByDepartment']['name'] . ' has  assigned ' . $assignment_detail['Staff']['Title']['title'] . ' ' . $assignment_detail['Staff']['full_name'] . ' <br />
						Section: ' . $assignment_detail['Section']['name'] . ' <br/>
						Year Level: Pre/1st<br/>
						Department: ' . $assignment_detail['PublishedCourse']['College']['name'] . ' <br/>
						Program: ' . $assignment_detail['PublishedCourse']['Program']['name'] . ' <br/>
						Program Type: ' . $assignment_detail['PublishedCourse']['ProgramType']['name'] . '<br/>
						Academic Year: ' . $assignment_detail['PublishedCourse']['academic_year'] . ' <br/>
						Semester: ' . $assignment_detail['PublishedCourse']['semester'] . '';

                        $auto_message[$index]['is_read'] = 0;
                        $auto_message[$index]['user_id'] = $ownerDepartmentUser['User']['id'];
                    } elseif ($assignment_detail['PublishedCourse']['department_id'] != $assignment_detail['PublishedCourse']['given_by_department_id']) {
                        $ownerDepartmentUser = ClassRegistry::init('User')->find('first', array(
                            'conditions' => array(
                                'User.is_admin' => 1,
                                'User.role_id' => ROLE_DEPARTMENT,
                                'User.id IN (select user_id from staffs where department_id=' . $assignment_detail['PublishedCourse']['department_id'] . ')'
                            ),
                            'recursive' => -1
                        ));

                        $index = count($auto_message);

                        $auto_message[$index]['message'] = 'The course you disptached <strong><u>' . $assignment_detail['PublishedCourse']['Course']['course_title'] . ' (' . $assignment_detail['PublishedCourse']['Course']['course_code'] . ')' . '</u>
						to department of ' . $assignment_detail['PublishedCourse']['GivenByDepartment']['name'] . ' has assigned ' . $assignment_detail['Staff']['Title']['title'] . ' ' . $assignment_detail['Staff']['full_name'] . ' <br />
						Section: ' . $assignment_detail['Section']['name'] . ' <br/>
						Year Level: ' . $assignment_detail['Section']['YearLevel']['name'] . '<br/>
						Department: ' . $assignment_detail['PublishedCourse']['Department']['name'] . ' <br/>
						Program: ' . $assignment_detail['PublishedCourse']['Program']['name'] . ' <br/>
						Program Type: ' . $assignment_detail['PublishedCourse']['ProgramType']['name'] . '<br/>
						Academic Year: ' . $assignment_detail['PublishedCourse']['academic_year'] . ' <br/>
						Semester: ' . $assignment_detail['PublishedCourse']['semester'] . '';

                        $auto_message[$index]['read'] = 0;
                        $auto_message[$index]['user_id'] = $ownerDepartmentUser['User']['id'];
                    }
                }
            }
        }

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function sendNotificationOnDepartmentGradeChangeApproval($grade_change = null)
    {

        $auto_message = array();

        /* $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
            'conditions' => array(
                'ExamGradeChange.id' => $grade_change['id']
            ),
            'recursive' => -1
        )); */

        if (isset($grade_change['id']) && !empty($grade_change['id'])) {
            $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.id' => $grade_change['id']
                ),
                'recursive' => -1
            ));
        } elseif (isset($grade_change['ExamGradeChange']['id']) && !empty($grade_change['ExamGradeChange']['id'])) {
            $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.id' => $grade_change['ExamGradeChange']['id']
                ),
                'recursive' => -1
            ));
        } elseif (isset($grade_change['exam_grade_id']) && !empty($grade_change['exam_grade_id'])) {
            $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.exam_grade_id' => $grade_change['exam_grade_id']
                ),
                'order' => array('ExamGradeChange.id' => 'DESC'),
                'recursive' => -1
            ));
        } elseif (isset($grade_change['ExamGradeChange']['exam_grade_id']) && !empty($grade_change['ExamGradeChange']['exam_grade_id'])) {
            $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
                'conditions' => array(
                    'ExamGradeChange.exam_grade_id' => $grade_change['ExamGradeChange']['exam_grade_id']
                ),
                'order' => array('ExamGradeChange.id' => 'DESC'),
                'recursive' => -1
            ));
        } else {
            return;
        }

        $exam_garde_details = $this->gradeRelatedDetails($exam_garde_change['ExamGradeChange']['exam_grade_id']);

        //Instructor notification

        if (isset($exam_garde_details['Instructor']['user_id']) && !empty($exam_garde_details['Instructor']['user_id']) && $exam_garde_change['ExamGradeChange']['initiated_by_department'] == 0) {
            if (!empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_id'])) {
                $auto_message[0]['message'] = 'Your makeup exam grade submission to <u>' . $exam_garde_details['Student']['full_name'] . '</u> for the course <u>' . $exam_garde_details['Course']['course_title'] . ' (' . $exam_garde_details['Course']['course_code'] . ')</u> is ' . ($grade_change['department_approval'] == 1 ? (isset($grade_change['department_reply']) ? 're-accepted' : 'accepted') : (isset($grade_change['department_reply']) ? 'rejected (after register rejection)' : 'rejected')) . ' by <u>' . (!empty($exam_garde_details['PublishedCourse']['department_id']) ? $exam_garde_details['Department']['name'] . ' Department' : $exam_garde_details['College']['name'] . ' Freshman Program') . '</u>. <a href="/exam_results/add/' . $exam_garde_details['PublishedCourse']['id'] . '">View Grade</a>';
            } else {
                $auto_message[0]['message'] = 'Your exam grade change request to <u>' . $exam_garde_details['Student']['full_name'] . '</u> for the course <u>' . $exam_garde_details['Course']['course_title'] . ' (' . $exam_garde_details['Course']['course_code'] . ')</u> is ' . ($grade_change['department_approval'] == 1 ? 'accepted' : 'rejected') . ' by <u>' . (!empty($exam_garde_details['PublishedCourse']['department_id']) ? $exam_garde_details['Department']['name'] . ' Department' : $exam_garde_details['College']['name'] . ' Freshman Program') . '</u>. <a href="/exam_results/add/' . $exam_garde_details['PublishedCourse']['id'] . '">View Grade</a>';
            }

            if ($grade_change['department_approval'] == -1) {
                $auto_message[0]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[0]['message'] . '</p>';
            } elseif ($grade_change['department_approval'] == 1) {
                $auto_message[0]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[0]['message'] . '</p>';
            }

            $auto_message[0]['is_read'] = 0;
            $auto_message[0]['user_id'] = $exam_garde_details['Instructor']['user_id'];
        }

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function sendNotificationOnCollegeGradeChangeApproval($grade_change = null)
    {

        $auto_message = array();

        $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
            'conditions' => array(
                'ExamGradeChange.id' => $grade_change['id']
            ),
            'recursive' => -1
        ));

        //debug($exam_garde_change);
        $exam_garde_details = $this->gradeRelatedDetails($exam_garde_change['ExamGradeChange']['exam_grade_id']);

        //Instructor notification
        if (isset($exam_garde_details['Instructor']['user_id']) && !empty($exam_garde_details['Instructor']['user_id']) && $exam_garde_change['ExamGradeChange']['initiated_by_department'] == 0) {
            $auto_message[0]['message'] = 'Your exam grade change request to <u>' . $exam_garde_details['Student']['full_name'] . '</u> for the course <u>' . $exam_garde_details['Course']['course_title'] . ' (' . $exam_garde_details['Course']['course_code'] . ')</u> is ' . ($grade_change['college_approval'] == 1 ? 'accepted' : 'rejected') . ' by <u>' . (!empty($exam_garde_details['PublishedCourse']['department_id']) ? $exam_garde_details['Department']['College']['name'] : $exam_garde_details['College']['name']) . '</u>. <a href="/exam_results/add/' . $exam_garde_details['PublishedCourse']['id'] . '">View Grade</a>';

            if ($grade_change['college_approval'] == -1) {
                $auto_message[0]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[0]['message'] . '</p>';
            } elseif ($grade_change['college_approval'] == 1) {
                $auto_message[0]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[0]['message'] . '</p>';
            }

            $auto_message[0]['is_read'] = 0;
            $auto_message[0]['user_id'] = $exam_garde_details['Instructor']['user_id'];
        }

        /* $dept_approved_by_staff_detail = ClassRegistry::init('Staff')->find('first', array(
            'conditions' => array(
                'Staff.id' => $exam_garde_change['ExamGradeChange']['department_approved_by']
            ),
            'recursive' => -1
        )); */

        //debug($dept_approved_by_staff_detail);exit();
        if (!empty($exam_garde_change['ExamGradeChange']['department_approved_by'])) {
            if ($exam_garde_change['ExamGradeChange']['initiated_by_department'] == 1) {
                $approvalpage = '/examResults/submit_grade_for_instructor';
            } elseif (isset($exam_garde_details['Department']) && !empty($exam_garde_details['Department'])) {
                $approvalpage = '/examGrades/approve_non_freshman_grade_submission';
            } elseif (isset($exam_garde_details['College']) && !empty($exam_garde_details['College'])) {
                //$approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                $approvalpage = '/examGrades/approve_freshman_grade_submission';
            }

            $auto_message[1]['message'] = 'Exam grade change request to <u>' . $exam_garde_details['Student']['full_name'] . '</u> for the course <u>' . $exam_garde_details['Course']['course_title'] . ' (' . $exam_garde_details['Course']['course_code'] . ')</u> is ' . ($grade_change['college_approval'] == 1 ? 'accepted' : 'rejected') . ' by <u>' . (!empty($exam_garde_details['PublishedCourse']['department_id']) ? $exam_garde_details['Department']['College']['name'] : $exam_garde_details['College']['name']) . '</u>. <a href="' . $approvalpage . '/' . $exam_garde_details['PublishedCourse']['id'] . '">View Grade</a>';

            if ($grade_change['college_approval'] == -1) {
                $auto_message[1]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[1]['message'] . '</p>';
            } elseif ($grade_change['college_approval'] == 1) {
                $auto_message[1]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[1]['message'] . '</p>';
            }

            $auto_message[1]['is_read'] = 0;
            $auto_message[1]['user_id'] = $exam_garde_change['ExamGradeChange']['department_approved_by'];
        }


        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function sendNotificationOnRegistrarGradeChangeApproval($grade_change = null)
    {

        //To college, department, instructor, student
        $auto_message = array();

        $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
            'conditions' => array(
                'ExamGradeChange.id' => $grade_change['id']
            ),
            'recursive' => -1
        ));

        //debug($grade_change);
        debug($exam_garde_change);
        $exam_garde_details = $this->gradeRelatedDetails(
            $exam_garde_change['ExamGradeChange']['exam_grade_id'],
            $grade_change['id']
        );
        //debug($exam_garde_details);

        $initiated_fullname = '';

        //Instructor notification
        if (isset($exam_garde_details['Instructor']['user_id']) && !empty($exam_garde_details['Instructor']['user_id'])) {
            if ($exam_garde_change['ExamGradeChange']['initiated_by_department'] == 0) {
                $auto_message[0]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> grade change' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? '(through Supplementary Exam)' : ' ') . ' you initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                        $exam_garde_details['Course']['course_title']
                    )) . ' (' . (trim(
                        $exam_garde_details['Course']['course_code']
                    )) . ')</u> you tought in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '. <a href="/exam_results/add/' . ($exam_garde_details['PublishedCourse']['id']) . '">View Grade</a>';
            } elseif ($exam_garde_change['ExamGradeChange']['initiated_by_department'] == 1) {
                $initiated_fullname = $this->User->field(
                    'full_name',
                    array('User.id' => (!empty($exam_garde_change['ExamGradeChange']['department_approved_by']) ? $exam_garde_change['ExamGradeChange']['department_approved_by'] : (!empty($exam_garde_change['ExamGradeChange']['college_approved_by']) ? $exam_garde_change['ExamGradeChange']['college_approved_by'] : '0')))
                );
                if (!empty($exam_garde_change['ExamGradeChange']['college_approved_by']) && $exam_garde_change['ExamGradeChange']['college_approved_by'] === $exam_garde_change['ExamGradeChange']['department_approved_by']) {
                    $auto_message[0]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> grade change' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? ' through Supplementary Exam' : ' ') . ', initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> by ' . (!empty($initiated_fullname) ? $initiated_fullname : 'your college') . ' for the course <u>' . (trim(
                            $exam_garde_details['Course']['course_title']
                        )) . ' (' . (trim(
                            $exam_garde_details['Course']['course_code']
                        )) . ')</u> you tought in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '. <a href="/exam_results/add/' . ($exam_garde_details['PublishedCourse']['id']) . '">View Grade</a>';
                } else {
                    $auto_message[0]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> grade change' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? ' through Supplementary Exam' : ' ') . ', initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> by ' . (!empty($initiated_fullname) ? $initiated_fullname : 'your department') . ' for the course <u>' . (trim(
                            $exam_garde_details['Course']['course_title']
                        )) . ' (' . (trim(
                            $exam_garde_details['Course']['course_code']
                        )) . ')</u> you tought in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '. <a href="/exam_results/add/' . ($exam_garde_details['PublishedCourse']['id']) . '">View Grade</a>';
                }
            }

            if (isset($auto_message[0]['message']) && !empty($auto_message[0]['message'])) {
                if ($grade_change['registrar_approval'] == -1) {
                    $auto_message[0]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[0]['message'] . '</p>';
                } elseif ($grade_change['registrar_approval'] == 1) {
                    $auto_message[0]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[0]['message'] . '</p>';
                }

                $auto_message[0]['is_read'] = 0;
                $auto_message[0]['user_id'] = $exam_garde_details['Instructor']['user_id'];
            }
        }

        $view_grade_url = '';

        //Department notification
        if (!empty($exam_garde_change['ExamGradeChange']['department_approved_by'])) {
            $view_grade_url = 'department_grade_view';
            //$auto_message[1]['message'] = 'Exam grade change request from <strong>' . ($exam_garde_details['ExamGrade']['grade']) . ' to <strong>' . ($grade_change['grade']) . '</strong>. for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for <u>' . (trim($exam_garde_details['Course']['course_title'])) . ' (' . (trim($exam_garde_details['Course']['course_code'])) . ')</u> course is ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' by the registrar.'; //<a href="' . $approvalpage . '/' . $exam_garde_details['PublishedCourse']['id'] . '">View Grade</a>
            if ($exam_garde_change['ExamGradeChange']['initiated_by_department'] == 1) {
                $auto_message[1]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> ' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? 'supplementary exam' : 'exam') . ' grade change you initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                        $exam_garde_details['Course']['course_title']
                    )) . ' (' . (trim(
                        $exam_garde_details['Course']['course_code']
                    )) . ')</u> the student attended in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';
            } else {
                $auto_message[1]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> exam grade change initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                        $exam_garde_details['Course']['course_title']
                    )) . ' (' . (trim(
                        $exam_garde_details['Course']['course_code']
                    )) . ')</u> the student attended in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';
            }

            if ($grade_change['registrar_approval'] == -1) {
                $auto_message[1]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[1]['message'] . '</p>';
            } elseif ($grade_change['registrar_approval'] == 1) {
                $auto_message[1]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[1]['message'] . '</p>';
            }

            if (isset($auto_message[1]['message']) && !empty($auto_message[1]['message'])) {
                $auto_message[1]['is_read'] = 0;
                if (!empty($view_grade_url)) {
                    $auto_message[1]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                }
                $auto_message[1]['user_id'] = $exam_garde_change['ExamGradeChange']['department_approved_by'];
            }
        }

        //Student Notification
        if (isset($exam_garde_details['Student']['user_id']) && !empty($exam_garde_details['Student']['user_id']) && $grade_change['registrar_approval'] == 1) {
            $auto_message[2]['message'] = 'Your Exam grade for the course <u>' . (trim(
                    $exam_garde_details['Course']['course_title']
                )) . ' (' . (trim(
                    $exam_garde_details['Course']['course_code']
                )) . ')</u> you attented in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . ' is changed from <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong>' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? ' through supplementary exam grade change.' : ' through exam grade change.') . '';
            $auto_message[2]['is_read'] = 0;
            $auto_message[2]['user_id'] = $exam_garde_details['Student']['user_id'];
        }

        //College Notification
        if (!empty($exam_garde_change['ExamGradeChange']['college_approved_by'])) {
            if (!empty($exam_garde_change['ExamGradeChange']['college_approved_by'])) {
                if ((isset($exam_garde_details['PublishedCourse']['year_level_id']) && empty($exam_garde_details['PublishedCourse']['year_level_id'])) || (isset($exam_garde_details['Student']['department_id']) && empty($exam_garde_details['Student']['department_id']))) {
                    $view_grade_url = 'freshman_grade_view';
                } else {
                    $view_grade_url = 'college_grade_view';
                }
            }

            //$auto_message[3]['message'] = 'Exam grade change request from <strong>' . ($exam_garde_details['ExamGrade']['grade']) . ' to <strong>' . ($grade_change['grade']) . '</strong> for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for <u>' . (trim($exam_garde_details['Course']['course_title'])) . ' (' . (trim($exam_garde_details['Course']['course_code'])) . ')</u> course is ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' by the registrar.';

            if ($exam_garde_change['ExamGradeChange']['initiated_by_department'] == 1 && !empty($exam_garde_change['ExamGradeChange']['department_approved_by']) && $exam_garde_change['ExamGradeChange']['college_approved_by'] === $exam_garde_change['ExamGradeChange']['department_approved_by']) {
                $auto_message[3]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> ' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? 'supplementary exam' : 'exam') . ' grade change initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                        $exam_garde_details['Course']['course_title']
                    )) . ' (' . (trim(
                        $exam_garde_details['Course']['course_code']
                    )) . ')</u> the student attended in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';
            } else {
                $auto_message[3]['message'] = 'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> exam grade change initiated for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                        $exam_garde_details['Course']['course_title']
                    )) . ' (' . (trim(
                        $exam_garde_details['Course']['course_code']
                    )) . ')</u> the student attended in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';
            }


            if (isset($auto_message[3]['message']) && !empty($auto_message[3]['message'])) {
                if ($grade_change['registrar_approval'] == -1) {
                    $auto_message[3]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[3]['message'] . '</p>';
                } elseif ($grade_change['registrar_approval'] == 1) {
                    $auto_message[3]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $auto_message[3]['message'] . '</p>';
                }

                if (!empty($view_grade_url)) {
                    $auto_message[3]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                }

                $auto_message[3]['is_read'] = 0;
                $auto_message[3]['user_id'] = $exam_garde_change['ExamGradeChange']['college_approved_by'];
            }
        }

        //Registrar Notification
        if (!empty($exam_garde_change['ExamGradeChange']['registrar_approved_by'])) {
            $view_grade_url = 'registrar_grade_view';
            $auto_message[4]['message'] = 'You ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong> ' . (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) ? 'supplementary exam' : 'exam') . ' grade change ' . (isset($exam_garde_change['ExamGradeChange']['initiated_by_department']) && $exam_garde_change['ExamGradeChange']['initiated_by_department'] == 1 ? 'initiated by ' . (!empty($initiated_fullname) ? $initiated_fullname : ' the department/college') : '') . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                    $exam_garde_details['Course']['course_title']
                )) . ' (' . (trim(
                    $exam_garde_details['Course']['course_code']
                )) . ')</u> the student attended in the ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';

            if (isset($auto_message[4]['message']) && !empty($auto_message[4]['message'])) {
                if ($grade_change['registrar_approval'] == -1) {
                    $auto_message[4]['message'] = '<p style="text-align:justify; padding:0px; margin:0px">' . $auto_message[4]['message'] . '</p>';
                } elseif ($grade_change['registrar_approval'] == 1) {
                    $auto_message[4]['message'] = '<p style="text-align:justify; padding:0px; margin:0px">' . $auto_message[4]['message'] . '</p>';
                }

                if (!empty($view_grade_url)) {
                    $auto_message[4]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                }

                $auto_message[4]['is_read'] = 0;
                $auto_message[4]['user_id'] = $exam_garde_change['ExamGradeChange']['registrar_approved_by'];
            }
        }

        debug($auto_message);

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function sendNotificationOnAutoAndManualGradeChange(
        $grade_changes = null,
        $privilaged_registrars = array(),
        $use_the_new_format = 0,
        $converted_by_full_name = ''
    ) {

        //To student and registrar
        $auto_message = array();
        /* $grade_changes[0]['grade'] = 'F';
        $grade_changes[0]['exam_grade_id'] = 1;
        $grade_changes[0]['manual_ng_conversion'] = 1; */

        if (!empty($grade_changes)) {
            foreach ($grade_changes as $key => $grade_change) {
                $index = count($auto_message);

                //debug($grade_change);

                $exam_garde_change = ClassRegistry::init('ExamGradeChange')->find('first', array(
                    'conditions' => array(
                        //'ExamGradeChange.grade' => $grade_change['grade'],
                        'ExamGradeChange.exam_grade_id' => $grade_change['exam_grade_id']
                    ),
                    'order' => array('ExamGradeChange.id' => 'DESC'),
                    'recursive' => -1
                ));

                debug($exam_garde_change);

                $exam_garde_details = $this->gradeRelatedDetails($grade_change['exam_grade_id']);

                $change_type_and_grade = '';

                if (isset($exam_garde_change['ExamGradeChange']['auto_ng_conversion']) && !empty($exam_garde_change['ExamGradeChange']['auto_ng_conversion']) && $exam_garde_change['ExamGradeChange']['auto_ng_conversion'] == 1) {
                    $change_type_and_grade = 'through Auto NG to F Conversion to <strong>' . (isset($exam_garde_change['ExamGradeChange']['grade']) && !empty($exam_garde_change['ExamGradeChange']['grade']) ? $exam_garde_change['ExamGradeChange']['grade'] : (isset($grade_change['grade']) && !empty($grade_change['grade']) ? $grade_change['grade'] : '')) . '</strong> grade';
                } elseif (isset($exam_garde_change['ExamGradeChange']['manual_ng_conversion']) && !empty($exam_garde_change['ExamGradeChange']['manual_ng_conversion']) && $exam_garde_change['ExamGradeChange']['manual_ng_conversion'] == 1) {
                    $change_type_and_grade = 'throgh Manual NG Conversion to <strong>' . (isset($exam_garde_change['ExamGradeChange']['grade']) && !empty($exam_garde_change['ExamGradeChange']['grade']) ? $exam_garde_change['ExamGradeChange']['grade'] : (isset($grade_change['grade']) && !empty($grade_change['grade']) ? $grade_change['grade'] : '')) . '</strong> grade';
                } elseif (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && isset($exam_garde_change['ExamGradeChange']['initiated_by_department']) && $exam_garde_change['ExamGradeChange']['initiated_by_department'] == 1) {
                    $change_type_and_grade = 'throgh Supplemetary Exam' . (isset($exam_garde_details['ExamGrade']['grade']) && !empty($exam_garde_details['ExamGrade']['grade']) ? ' from <strong>' . $exam_garde_details['ExamGrade']['grade'] . '</strong>' : '') . ' to <strong>' . (isset($exam_garde_change['ExamGradeChange']['grade']) && !empty($exam_garde_change['ExamGradeChange']['grade']) ? $exam_garde_change['ExamGradeChange']['grade'] : (isset($grade_change['grade']) && !empty($grade_change['grade']) ? $grade_change['grade'] : '')) . '</strong> grade';
                } elseif (isset($exam_garde_change['ExamGradeChange']['makeup_exam_result']) && !empty($exam_garde_change['ExamGradeChange']['makeup_exam_result'])) {
                    $change_type_and_grade = 'throgh Supplemetary Exam' . (isset($exam_garde_details['ExamGrade']['grade']) && !empty($exam_garde_details['ExamGrade']['grade']) ? ' from <strong>' . $exam_garde_details['ExamGrade']['grade'] . '</strong>' : '') . ' to <strong>' . (isset($exam_garde_change['ExamGradeChange']['grade']) && !empty($exam_garde_change['ExamGradeChange']['grade']) ? $exam_garde_change['ExamGradeChange']['grade'] : (isset($grade_change['grade']) && !empty($grade_change['grade']) ? $grade_change['grade'] : '')) . '</strong> grade';
                } elseif (isset($exam_garde_change['ExamGradeChange']['registrar_approved_by']) && !empty($exam_garde_change['ExamGradeChange']['registrar_approved_by'])) {
                    $change_type_and_grade = 'manually' . (isset($exam_garde_details['ExamGrade']['grade']) && !empty($exam_garde_details['ExamGrade']['grade']) ? ' from <strong>' . $exam_garde_details['ExamGrade']['grade'] . '</strong>' : '') . ' to <strong>' . (isset($exam_garde_change['ExamGradeChange']['grade']) && !empty($exam_garde_change['ExamGradeChange']['grade']) ? $exam_garde_change['ExamGradeChange']['grade'] : (isset($grade_change['grade']) && !empty($grade_change['grade']) ? $grade_change['grade'] : '')) . '</strong> grade';
                }

                debug($change_type_and_grade);

                //Student Notification
                if (isset($exam_garde_details['Student']['user_id']) && !empty($exam_garde_details['Student']['user_id'])) {
                    $auto_message[$index]['message'] = 'Your Exam grade is changed ' . $change_type_and_grade . ' for the course <u>' . (trim(
                            $exam_garde_details['Course']['course_title']
                        )) . ' (' . (trim(
                            $exam_garde_details['Course']['course_code']
                        )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';
                    $auto_message[$index]['read'] = 0;
                    $auto_message[$index]['user_id'] = $exam_garde_details['Student']['user_id'];
                }

                //Instructor Notification
                if (isset($exam_garde_details['Instructor']['user_id']) && !empty($exam_garde_details['Instructor']['user_id'])) {
                    $index = count($auto_message);
                    $auto_message[$index]['message'] = 'Exam grade you submitted is changed ' . $change_type_and_grade . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                            $exam_garde_details['Course']['course_title']
                        )) . ' (' . (trim(
                            $exam_garde_details['Course']['course_code']
                        )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.' . (isset($exam_garde_details['PublishedCourse']['id']) && !empty($exam_garde_details['PublishedCourse']['id']) ? ' <a href="/exam_results/add/' . $exam_garde_details['PublishedCourse']['id'] . '">View Grade</a>' : '');
                    $auto_message[$index]['read'] = 0;
                    $auto_message[$index]['user_id'] = $exam_garde_details['Instructor']['user_id'];
                }

                if (!$use_the_new_format) {
                    //Department Notification
                    if (isset($exam_garde_change['ExamGrade']['department_approved_by']) && !empty($exam_garde_change['ExamGrade']['department_approved_by'])) {
                        $index = count($auto_message);

                        $auto_message[$index]['message'] = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                                $exam_garde_details['Course']['course_title']
                            )) . ' (' . (trim(
                                $exam_garde_details['Course']['course_code']
                            )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . '.';

                        if ((isset($exam_garde_details['PublishedCourse']['year_level_id']) && empty($exam_garde_details['PublishedCourse']['year_level_id'])) || (isset($exam_garde_details['Student']['department_id']) && empty($exam_garde_details['Student']['department_id']))) {
                            $view_grade_url = 'freshman_grade_view';
                        } else {
                            $view_grade_url = 'department_grade_view';
                        }

                        $view_grade_url = 'department_grade_view';

                        if (!empty($view_grade_url) && !empty($exam_garde_details['PublishedCourse']['id'])) {
                            $auto_message[$index]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade Details</a>';
                        }

                        $auto_message[$index]['is_read'] = 0;
                        $auto_message[$index]['user_id'] = $exam_garde_change['ExamGrade']['department_approved_by'];
                    }
                }

                //debug($privilaged_registrars);
                //debug($exam_garde_details);
                if (!empty($privilaged_registrars)) {
                    foreach ($privilaged_registrars as $key => $privilaged_registrar) {
                        $index = count($auto_message);
                        //Registrar Notification

                        if (!$use_the_new_format) {
                            if (!empty($privilaged_registrar['StaffAssigne']['id'])) {
                                if (isset($privilaged_registrar['StaffAssigne']['department_id']) && !empty($privilaged_registrar['StaffAssigne']['department_id'])) {
                                    $department_ids = unserialize(
                                        $privilaged_registrar['StaffAssigne']['department_id']
                                    );
                                } else {
                                    $department_ids = array();
                                }

                                if (isset($privilaged_registrar['StaffAssigne']['college_id']) && !empty($privilaged_registrar['StaffAssigne']['college_id'])) {
                                    $college_ids = unserialize($privilaged_registrar['StaffAssigne']['college_id']);
                                } else {
                                    $college_ids = array();
                                }
                            }
                        }
                        //debug($department_ids);
                        //debug($college_ids);
                        $view_grade_url = '';

                        if (isset($privilaged_registrar['User']['role_id']) && $privilaged_registrar['User']['role_id'] != ROLE_STUDENT) {
                            if ($privilaged_registrar['User']['role_id'] == ROLE_REGISTRAR) {
                                $view_grade_url = 'registrar_grade_view';
                            } elseif ($privilaged_registrar['User']['role_id'] == ROLE_COLLEGE) {
                                if (isset($exam_garde_details['Student']['department_id']) && empty($exam_garde_details['Student']['department_id'])) {
                                    $view_grade_url = 'freshman_grade_view';
                                } else {
                                    $view_grade_url = 'college_grade_view';
                                }
                            } elseif ($privilaged_registrar['User']['role_id'] == ROLE_DEPARTMENT) {
                                $view_grade_url = 'department_grade_view';
                            }
                        }

                        if (!$use_the_new_format) {
                            if ((!empty($exam_garde_details['Student']['department_id']) && isset($department_ids) && !empty($department_ids) && in_array(
                                        $exam_garde_details['Student']['department_id'],
                                        $department_ids
                                    )) || (empty($exam_garde_details['Student']['department_id']) && !empty($exam_garde_details['Student']['college_id']) && !empty($exam_garde_details['Student']['college_id']) && isset($college_ids) && !empty($college_ids) && in_array(
                                        $exam_garde_details['Student']['college_id'],
                                        $college_ids
                                    ))) {
                                $auto_message[$index]['message'] = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                                        $exam_garde_details['Course']['course_title']
                                    )) . ' (' . (trim(
                                        $exam_garde_details['Course']['course_code']
                                    )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year']; //' is changed from <strong>' . ($exam_garde_details['ExamGrade']['grade']) . '</strong> to <strong>' . ($exam_garde_change['ExamGradeChange']['grade']) . '</strong>';

                                if (isset($exam_garde_details['ExamGradeChange']['auto_ng_conversion']) && $exam_garde_details['ExamGradeChange']['auto_ng_conversion'] == 1) {
                                    $auto_message[$index]['message'] .= ' automatically.';
                                } else {
                                    $auto_message[$index]['message'] .= ' manually.';
                                }

                                if (!empty($view_grade_url)) {
                                    $auto_message[$index]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                                }

                                $auto_message[$index]['read'] = 0;
                                $auto_message[$index]['user_id'] = $privilaged_registrar['User']['id'];
                            }
                        } else {
                            if ($privilaged_registrar['User']['role_id'] == ROLE_REGISTRAR) {
                                if (isset($exam_garde_details['ExamGradeChange']['auto_ng_conversion']) && $exam_garde_details['ExamGradeChange']['auto_ng_conversion'] == 1) {
                                    $auto_message[$index]['message'] = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                                            $exam_garde_details['Course']['course_title']
                                        )) . ' (' . (trim(
                                            $exam_garde_details['Course']['course_code']
                                        )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'] . ' automatically by the System.';
                                } else {
                                    $auto_message[$index]['message'] = 'You applied an Exam Grade change ' . $change_type_and_grade . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                                            $exam_garde_details['Course']['course_title']
                                        )) . ' (' . (trim(
                                            $exam_garde_details['Course']['course_code']
                                        )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'];
                                }
                            } else {
                                $auto_message[$index]['message'] = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_garde_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . (trim(
                                        $exam_garde_details['Course']['course_title']
                                    )) . ' (' . (trim(
                                        $exam_garde_details['Course']['course_code']
                                    )) . ')</u> from ' . ($exam_garde_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_garde_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_garde_details['PublishedCourse']['academic_year'];
                                if (isset($exam_garde_details['ExamGradeChange']['auto_ng_conversion']) && $exam_garde_details['ExamGradeChange']['auto_ng_conversion'] == 1) {
                                    $auto_message[$index]['message'] .= ', automatically by the System.';
                                } else {
                                    $auto_message[$index]['message'] .= ', manually by ' . $converted_by_full_name . '.';
                                }
                            }

                            if (!empty($view_grade_url)) {
                                /* if ($privilaged_registrar['User']['role_id'] == ROLE_DEPARTMENT && ((isset($exam_garde_change['CourseRegistration']['year_levele_id']) && !empty($exam_garde_change['CourseRegistration']['year_levele_id'])) || (isset($exam_garde_change['CourseAdd']['year_levele_id']) && !empty($exam_garde_change['CourseAdd']['year_levele_id'])))) {
                                    $auto_message[$index]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                                } else if ($privilaged_registrar['User']['role_id'] != ROLE_DEPARTMENT) {
                                    $auto_message[$index]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                                } */

                                $auto_message[$index]['message'] .= ' <a href="/exam_grades/' . $view_grade_url . '/' . $exam_garde_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                            }

                            $auto_message[$index]['is_read'] = 0;
                            $auto_message[$index]['user_id'] = $privilaged_registrar['User']['id'];
                        }
                    }
                }
            }
        }

        debug($auto_message);

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function gradeRelatedDetails($exam_grade_id = null)
    {

        $exam_garde_details = array();

        $exam_garde_details_r = ClassRegistry::init('ExamGrade')->find('first', array(
            'conditions' => array(
                'ExamGrade.id' => $exam_grade_id
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'OR' => array(
                                    'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                                    'CourseInstructorAssignment.isprimary' => 1
                                )
                            ),
                            'Staff' => array('Title', 'Position'),
                            'limit' => 1
                        ),
                        'Course',
                        'Section',
                        'Department',
                        'College'
                    )
                ),
                'CourseAdd' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'OR' => array(
                                    'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                                    'CourseInstructorAssignment.isprimary' => 1
                                )
                            ),
                            'Staff' => array('Title', 'Position'),
                            'limit' => 1
                        ),
                        'Course',
                        'Section',
                        'Department' => array('College'),
                        'College'
                    )
                ),
                'MakeupExam' => array(
                    'Student',
                    'PublishedCourse' => array(
                        'CourseInstructorAssignment' => array(
                            'conditions' => array(
                                'OR' => array(
                                    'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                                    'CourseInstructorAssignment.isprimary' => 1
                                )
                            ),
                            'Staff' => array('Title', 'Position'),
                            'limit' => 1
                        ),
                        'Course',
                        'Section',
                        'Department' => array('College'),
                        'College'
                    )
                )
            )
        ));

        if (empty($exam_garde_details_r['ExamGrade']['id'])) {
            return array();
        }

        $exam_garde_details['ExamGrade'] = $exam_garde_details_r['ExamGrade'];

        //debug($exam_garde_details_r);
        if (!empty($exam_garde_details_r['CourseRegistration']['id'])) {
            $exam_garde_details['Student'] = $exam_garde_details_r['CourseRegistration']['Student'];
            $exam_garde_details['Course'] = $exam_garde_details_r['CourseRegistration']['PublishedCourse']['Course'];
            $exam_garde_details['Section'] = $exam_garde_details_r['CourseRegistration']['PublishedCourse']['Section'];
            $exam_garde_details['Department'] = $exam_garde_details_r['CourseRegistration']['PublishedCourse']['Department'];
            $exam_garde_details['College'] = $exam_garde_details_r['CourseRegistration']['PublishedCourse']['College'];

            $exam_garde_details['Instructor'] = array();

            if (isset($exam_garde_details_r['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'])) {
                $exam_garde_details['Instructor'] = $exam_garde_details_r['CourseRegistration']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
            }

            $exam_garde_details['PublishedCourse'] = (isset($exam_garde_details_r['CourseRegistration']['PublishedCourse']) && !empty($exam_garde_details_r['CourseRegistration']['PublishedCourse']) ? $exam_garde_details_r['CourseRegistration']['PublishedCourse'] : array());
        } elseif (!empty($exam_garde_details_r['CourseAdd']['id'])) {
            $exam_garde_details['Student'] = $exam_garde_details_r['CourseAdd']['Student'];
            $exam_garde_details['Course'] = $exam_garde_details_r['CourseAdd']['PublishedCourse']['Course'];
            $exam_garde_details['Section'] = $exam_garde_details_r['CourseAdd']['PublishedCourse']['Section'];
            $exam_garde_details['Department'] = $exam_garde_details_r['CourseAdd']['PublishedCourse']['Department'];
            $exam_garde_details['College'] = $exam_garde_details_r['CourseAdd']['PublishedCourse']['College'];

            $exam_garde_details['Instructor'] = array();

            if (isset($exam_garde_details_r['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'])) {
                $exam_garde_details['Instructor'] = $exam_garde_details_r['CourseAdd']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
            }

            $exam_garde_details['PublishedCourse'] = (isset($exam_garde_details_r['CourseAdd']['PublishedCourse']) && !empty($exam_garde_details_r['CourseAdd']['PublishedCourse']) ? $exam_garde_details_r['CourseAdd']['PublishedCourse'] : array());
        } elseif (!empty($exam_garde_details_r['MakeupExam']['id'])) {
            $exam_garde_details['Student'] = $exam_garde_details_r['MakeupExam']['Student'];
            $exam_garde_details['Course'] = $exam_garde_details_r['MakeupExam']['PublishedCourse']['Course'];
            $exam_garde_details['Section'] = $exam_garde_details_r['MakeupExam']['PublishedCourse']['Section'];
            $exam_garde_details['Department'] = $exam_garde_details_r['MakeupExam']['PublishedCourse']['Department'];
            $exam_garde_details['College'] = $exam_garde_details_r['MakeupExam']['PublishedCourse']['College'];

            $exam_garde_details['Instructor'] = array();

            if (isset($exam_garde_details_r['MakeupExam']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'])) {
                $exam_garde_details['Instructor'] = $exam_garde_details_r['MakeupExam']['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'];
            }

            $exam_garde_details['PublishedCourse'] = (isset($exam_garde_details_r['MakeupExam']['PublishedCourse']) && !empty($exam_garde_details_r['MakeupExam']['PublishedCourse']) ? $exam_garde_details_r['MakeupExam']['PublishedCourse'] : array());
        } else {
            $exam_garde_details['Student'] = array();
            $exam_garde_details['Course'] = array();
            $exam_garde_details['Section'] = array();
            $exam_garde_details['Department'] = array();
            $exam_garde_details['College'] = array();
            $exam_garde_details['Instructor'] = array();
            $exam_garde_details['PublishedCourse'] = array();
        }

        return $exam_garde_details;
    }

    function getInstructorLatestCourseAssignment($user_id = null)
    {

        if (isset($user_id) && !empty($user_id)) {
            $staff = ClassRegistry::init('Staff')->find('first', array(
                'conditions' => array('Staff.user_id' => $user_id),
                'recursive' => -1
            ));

            $latest_course_assignment = ClassRegistry::init('CourseInstructorAssignment')->find('first', array(
                'conditions' => array(
                    'CourseInstructorAssignment.staff_id' => $staff['Staff']['id']
                ),
                'order' => array('CourseInstructorAssignment.created DESC'),
                'recursive' => -1
            ));

            $course_assignments = ClassRegistry::init('CourseInstructorAssignment')->find('all', array(
                'conditions' => array(
                    'CourseInstructorAssignment.staff_id' => $staff['Staff']['id'],
                    'CourseInstructorAssignment.academic_year' => $latest_course_assignment['CourseInstructorAssignment']['academic_year'],
                    'CourseInstructorAssignment.semester' => $latest_course_assignment['CourseInstructorAssignment']['semester'],
                    'OR' => array(
                        'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                        'CourseInstructorAssignment.isprimary' => 1
                    )
                ),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Department',
                        'College',
                        'Section',
                        'Course',
                        'CourseRegistration' => array(
                            'ExamGrade' => array(
                                'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC')
                            )
                        ),
                        'CourseAdd' => array(
                            'ExamGrade' => array(
                                'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC')
                            )
                        )
                    )
                )
            ));
            //debug($course_assignments);
            $ongoing_courses = array();

            if (!empty($course_assignments)) {
                foreach ($course_assignments as $key => $course_assignment) {
                    $grade_submitted = true;

                    if ($course_assignment['PublishedCourse']['drop'] == 0) {
                        if (!isset($course_assignment['PublishedCourse']['CourseRegistration'])) {
                            debug($course_assignment);
                        }

                        if (!empty($course_assignment['PublishedCourse']['CourseRegistration'])) {
                            foreach ($course_assignment['PublishedCourse']['CourseRegistration'] as $key2 => $course_registration) {
                                //Excluding students who dropped the course
                                $course_droped = ClassRegistry::init('CourseRegistration')->isCourseDroped(
                                    $course_registration['id']
                                );

                                if (!$course_droped && (empty($course_registration['ExamGrade']) || $course_registration['ExamGrade'][0]['department_approval'] == -1)) {
                                    $grade_submitted = false;
                                    if ($course_assignment['PublishedCourse']['id'] == 6) {
                                        debug($course_registration);
                                    }
                                    break;
                                }
                            }
                        }

                        if ($grade_submitted == true) {
                            if (!empty($course_assignment['PublishedCourse']['CourseAdd'])) {
                                foreach ($course_assignment['PublishedCourse']['CourseAdd'] as $key2 => $course_add) {
                                    //Course drop consideration left
                                    if (empty($course_add['ExamGrade']) || $course_add['ExamGrade'][0]['department_approval'] == -1) {
                                        $grade_submitted = false;
                                        if ($course_assignment['PublishedCourse']['id'] == 6) {
                                            debug($course_add);
                                        }
                                        break;
                                    }
                                }
                            }
                        }

                        if ($grade_submitted == false) {
                            $index = count($ongoing_courses);

                            $ongoing_courses[$index]['Course'] = $course_assignment['PublishedCourse']['Course'];
                            $ongoing_courses[$index]['Section'] = $course_assignment['PublishedCourse']['Section'];
                            $ongoing_courses[$index]['Department'] = $course_assignment['PublishedCourse']['Department'];
                            $ongoing_courses[$index]['College'] = $course_assignment['PublishedCourse']['College'];
                            $ongoing_courses[$index]['PublishedCourse'] = $course_assignment['PublishedCourse'];

                            unset($ongoing_courses[$index]['PublishedCourse']['Course']);
                            unset($ongoing_courses[$index]['PublishedCourse']['CourseRegistration']);
                            unset($ongoing_courses[$index]['PublishedCourse']['CourseAdd']);
                        }
                    }
                }
            }

            //debug($ongoing_courses);
            //debug($course_assignments);
            //debug($latest_course_assignment);
            //debug($staff);
            return $ongoing_courses;
        } else {
            return array();
        }
    }

    function sendPermissionManagementBreakAttempt($user_id = null, $message = null)
    {

        $user = ClassRegistry::init('User')->find(
            'first',
            array('conditions' => array('User.id' => $user_id), 'recursive' => -1)
        );
        $sys_admins = ClassRegistry::init('User')->find(
            'all',
            array('conditions' => array('User.role_id' => 1), 'recursive' => -1)
        );
        $auto_message = array();

        if (!empty($sys_admins)) {
            foreach ($sys_admins as $sys_admin) {
                $index = count($auto_message);
                if ($message) {
                    $auto_message[$index]['message'] = $message;
                } else {
                    $auto_message[$index]['message'] = '<u>' . $user['User']['first_name'] . ' ' . $user['User']['middle_name'] . ' ' . $user['User']['last_name'] . ' (' . $user['User']['username'] . ')</u> is trying to break permission management system. Please give appropriate warning.';
                }

                $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[$index]['message'] . '</p>';
                $auto_message[$index]['is_read'] = 0;
                $auto_message[$index]['user_id'] = $sys_admin['User']['id'];
            }
        }

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    function sendInappropriateAccessAttempt($user_id = null, $message = null)
    {

        $user = ClassRegistry::init('User')->find(
            'first',
            array('conditions' => array('User.id' => $user_id), 'recursive' => -1)
        );
        $sys_admins = ClassRegistry::init('User')->find(
            'all',
            array('conditions' => array('User.role_id' => 1), 'recursive' => -1)
        );
        $auto_message = array();

        if (!empty($sys_admins)) {
            foreach ($sys_admins as $sys_admin) {
                $index = count($auto_message);
                if ($message) {
                    $auto_message[$index]['message'] = $message;
                } else {
                    $auto_message[$index]['message'] = '<u>' . $user['User']['first_name'] . ' ' . $user['User']['middle_name'] . ' ' . $user['User']['last_name'] . ' (' . $user['User']['username'] . ')</u> is trying to access a page or a permission not allowed to access. Please give appropriate warning.';
                }

                $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $auto_message[$index]['message'] . '</p>';
                $auto_message[$index]['is_read'] = 0;
                $auto_message[$index]['user_id'] = $sys_admin['User']['id'];
            }
        }

        if (!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        }
    }

    public function sendRegistrarAssignedNotificationToDepartmentAndCollege(
        $current_academic_year = null,
        $department_id = null,
        $college_id = null,
        $message = null
    ) {
        /* $accepted_student_department_notified = ClassRegistry::init('AcceptedStudent')->find('all', array(
            'conditions' => array(
                'AcceptedStudent.Placement_Approved_By_Department is null',
                'AcceptedStudent.academicyear' => $current_academic_year,
                'AcceptedStudent.placementtype' => REGISTRAR_ASSIGNED
            )
        ));

        $colleges_departments = ClassRegistry::init('User')->find('all', array(
            'conditions' => array(
                'User.role_id' => array(5, 6),
                'User.admin' => 1,
                'User.id IN (select user_id from staffs where department_id = ' . $department_id . ' OR college_id=' . $college_id . ' ) '

            ),
            'recursive' => -1
        ));

        $auto_message = array();

        if (!empty($colleges_departments)) {
            foreach($colleges_departments as $sys_admin) {
                $index = count($auto_message);
                if($message) {
                    $auto_message[$index]['message'] = $message;
                } else {
                    $auto_message[$index]['message'] = ' you have '. count($accepted_student_department_notified).' assigned to your '. (isset($department_id) && !empty($department_id) ? 'department' : 'college' ).' for '. $current_academic_year .' academic year.' . (isset($department_id) && !empty($department_id) ? ' You can  now attach the students to a curriculum in Placement > Accepted Students > Attach Curriculum ' : '');
                }

                $auto_message[$index]['message'] = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">'.$auto_message[$index]['message'].'</p>';
                $auto_message[$index]['read'] = 0;
                $auto_message[$index]['user_id'] = $sys_admin['User']['id'];
            }
        }

        if(!empty($auto_message)) {
            $this->saveAll($auto_message, array('validate' => false));
        } */
    }
}
