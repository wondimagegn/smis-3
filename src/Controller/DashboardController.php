<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Exception;

class DashboardController extends AppController
{

    public $menuOptions = [

        'weight' => -100000000,
        'exclude' => ['index', 'getModal']
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('AcademicYear');

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow([
            'getMessageAjax', // Ensure this action is allowed
            'index',
            'getRankAjax',
            'getApprovalCourseListAjax',
            'getApprovalRejectGradeChange',
            'getApprovalRejectGrade',
            'dispatchedAssignedCourseList',
            'addDropRequestList',
            'clearanceWithdrawSubRequest',
            'getProfileNotComplete',
            'getCourseSchedule',
            'getBackupAccountRequest',
            'getAcademicCalendar',
            'getStudentAssignedDormitory'
        ]);
    }
    public function beforeRender(Event $event)
    {

        $acyearArrayData = $this->AcademicYear->acyearArray();
        $defaultAcademicYear = $this->AcademicYear->currentAcademicYear();

        if (!empty($acyearArrayData)) {
            foreach ($acyearArrayData as $k => $v) {
                if ($v == $defaultAcademicYear) {
                    $defaultAcademicYear = $k;
                    break;
                }
            }
        }

        $session = $this->request->getSession();
        $authUser = $session->read('Auth.User');
        $usersRelation = $session->read('users_relation');

        if ($authUser) {
            if (
                isset($usersRelation['User']['id']) &&
                $authUser['id'] == $usersRelation['User']['id'] &&
                $this->role_id == $authUser['role_id']
            ) {
                $roleId = $authUser['role_id'];
            } else {
                $session->destroy();
                return $this->redirect($this->Auth->logout());
            }
        } else {
            $session->destroy();
            return $this->redirect($this->Auth->logout());
        }

        $this->set('role_id', $roleId);
        $this->set(compact('acyearArrayData', 'defaultAcademicYear'));

        // Unset password safely
        $requestData = $this->request->getData();
        unset($requestData['User']['password']);
    }


    public function getModal($published_course_id = null)
    {
        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');


        if (!empty($published_course_id)) {
            //get publishedcourse details
            $publishedCourse_details = TableRegistry::getTableLocator()->get('CourseSchedule')->get_published_course_details($published_course_id);
            $formatted_published_course_detail = array();

            if (!empty($publishedCourse_details)) {
                $formatted_published_course_detail['course_code'] = $publishedCourse_details['Course']['course_code'];
                $formatted_published_course_detail['course_name'] = $publishedCourse_details['Course']['course_title'];

                //Instructor assigned for lecture
                if ($publishedCourse_details['Course']['lecture_hours'] != 0) {
                    if (!empty($publishedCourse_details['CourseInstructorAssignment'])) {
                        $is_instructor_assigned = false;
                        foreach ($publishedCourse_details['CourseInstructorAssignment'] as $assigned_instructor) {
                            if (strcasecmp($assigned_instructor['type'], 'Lecture') == 0 || strcasecmp($assigned_instructor['type'], 'Lecture+Tutorial') == 0 || strcasecmp($assigned_instructor['type'], 'Lecture+Lab') == 0) {
                                if (isset($formatted_published_course_detail['lecture'])) {
                                    $formatted_published_course_detail['lecture'] = $formatted_published_course_detail['lecture'] . ', ' . $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                                } else {
                                    $formatted_published_course_detail['lecture'] = $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                                }
                                $is_instructor_assigned = true;
                            }
                        }
                        if ($is_instructor_assigned == false) {
                            $formatted_published_course_detail['lecture'] = "TBA";
                        }
                    } else {
                        $formatted_published_course_detail['lecture'] = "TBA";
                    }
                }

                //Instructor assigned for Tutorial
                if ($publishedCourse_details['Course']['tutorial_hours'] != 0) {
                    if (!empty($publishedCourse_details['CourseInstructorAssignment'])) {
                        $is_instructor_assigned = false;
                        foreach ($publishedCourse_details['CourseInstructorAssignment'] as $assigned_instructor) {
                            if (strcasecmp($assigned_instructor['type'], 'Tutorial') == 0 || strcasecmp($assigned_instructor['type'], 'Lecture+Tutorial') == 0) {
                                if (isset($formatted_published_course_detail['tutorial'])) {
                                    $formatted_published_course_detail['tutorial'] = $formatted_published_course_detail['tutorial'] . ', ' . $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                                } else {
                                    $formatted_published_course_detail['tutorial'] = $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                                }
                                $is_instructor_assigned = true;
                            }
                        }
                        if ($is_instructor_assigned == false) {
                            $formatted_published_course_detail['tutorial'] = "TBA";
                        }
                    } else {
                        $formatted_published_course_detail['tutorial'] = "TBA";
                    }
                }

                //Instructor assigned for Laboratory
                if ($publishedCourse_details['Course']['laboratory_hours'] != 0) {
                    if (!empty($publishedCourse_details['CourseInstructorAssignment'])) {
                        $is_instructor_assigned = false;
                        foreach ($publishedCourse_details['CourseInstructorAssignment'] as $assigned_instructor) {
                            if (strcasecmp($assigned_instructor['type'], 'Lab') == 0 || strcasecmp($assigned_instructor['type'], 'Lecture+Lab') == 0) {
                                if (isset($formatted_published_course_detail['lab'])) {
                                    $formatted_published_course_detail['lab'] = $formatted_published_course_detail['lab'] . ', ' . $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                                } else {
                                    $formatted_published_course_detail['lab'] = $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                                }
                                $is_instructor_assigned = true;
                            }
                        }
                        if ($is_instructor_assigned == false) {
                            $formatted_published_course_detail['lab'] = "TBA";
                        }
                    } else {
                        $formatted_published_course_detail['lab'] = "TBA";
                    }
                }
            }

        }
        $this->set([
            'formatted_published_course_detail' => $formatted_published_course_detail,
            '_serialize' => ['formatted_published_course_detail']
        ]);
    }

    public function index()
    {
        $this->viewBuilder()->setLayout('dashboard');

        // Load AcademicYear Model
        $currentAcy = $this->AcademicYear->currentAcademicyear();
        if (
            is_numeric(Configure::read('BackEntry.ACY_BACK_GRADE_APPROVAL_DASHBOARD'))
            && Configure::read('BackEntry.ACY_BACK_GRADE_APPROVAL_DASHBOARD')
        ) {
            $acYearsArray = $this->AcademicYear->academicYearInArray(
                ((explode('/', $currentAcy)[0]) - Configure::read('BackEntry.ACY_BACK_GRADE_APPROVAL_DASHBOARD')),
                (explode('/', $currentAcy)[0])
            );
        } else {
            $acYearsArray[$currentAcy] = $currentAcy;
        }

        $acyRangesByComaQuotedForDisplay = implode(", ", $acYearsArray);
        $this->set(compact('acyRangesByComaQuotedForDisplay'));

        $comingAcademicCalendarsDeadlines = [];
        $this->set(compact('comingAcademicCalendarsDeadlines'));

        // Session handling using CakePHP 3.7
        $session = $this->request->getSession();
        $authUser = $session->read('Auth.User');

        // Student Profile Completion Check
        if (
            isset($authUser['role_id']) && $authUser['role_id'] == Configure::read('Roles.STUDENT') &&
            !empty($authUser['id']) &&
            strcasecmp($this->request->getParam('action'), 'profile') !== 0 &&
            (
                TableRegistry::getTableLocator()->get('StudentStatusPatterns')->isEligibleForExitExam(
                    $this->student_id
                ) ||
                Configure::read('FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE') == 1
            ) &&
            strcasecmp($this->request->getParam('controller'), 'users') !== 0 &&
            strcasecmp($this->request->getParam('action'), 'changePwd') !== 0
        ) {
            if (
                !TableRegistry::getTableLocator()->get('StudentStatusPatterns')->
                completedFillingProfileInformation($this->student_id)
            ) {
                $this->Flash->warning(
                    __(
                        'Dear {0}, you are required to complete your basic profile before proceeding. If you encounter an error or need assistance, please report to the registrar record officer assigned to your department.',
                        $authUser['first_name']
                    )
                );
                return $this->redirect(['controller' => 'Students', 'action' => 'profile']);
            }
        }
    }


    public function getProfileNotComplete()
    {
        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');
        $profile_not_buildc = 0;

        if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/Students/profile_not_build_list') && ($this->role_id != ROLE_STUDENT)) {
            if (!empty($this->department_ids)) {
                $profile_not_buildc = TableRegistry::getTableLocator()->get('Student')->getProfileNotBuildListCount(
                    DAYS_BACK_PROFILE,
                    $this->department_ids,
                    null,
                    $this->program_ids,
                    $this->program_type_ids
                );
            } elseif (!empty($this->college_ids)) {
                $profile_not_buildc = TableRegistry::getTableLocator()->get('Student')->getProfileNotBuildListCount(
                    DAYS_BACK_PROFILE,
                    null,
                    $this->college_ids,
                    $this->program_ids,
                    $this->program_type_ids
                );
            }
        }


        $this->set([
            'profile_not_buildc' => $profile_not_buildc,
            '_serialize' => ['profile_not_buildc']
        ]);

    }

    public function getAcademicCalender()
    {
        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        $calendar = array();

        if ($this->role_id == ROLE_STUDENT) {
            $calendarr = TableRegistry::getTableLocator()->get('AcademicCalendar')->getAcademicCalender(
                $this->AcademicYear->currentAcademicyear()
            );
            if (!empty($this->department_id)) {
                $calendar[$this->department_id] = $calendarr[$this->department_id];
            } elseif (!empty($this->college_id) && empty($this->department_id)) {
                $calendar['pre_' . $this->college_id] = $calendarr['pre_' . $this->college_id];
            }
            $this->set(compact('calendar'));
        }


        $this->set([
            'calendar' => $calendar,
            '_serialize' => ['calendar']
        ]);
    }

    public function clearanceWithdrawSubRequest()
    {
        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        $clearance_request = 0;
        $exemption_request = 0;
        $substitution_request = 0;

        //clearances/approve_clearance
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR /* || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT */) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/Clearances/approve_clearance')) {
                $current_academic_year_start_date = $this->AcademicYear->getAcademicyearBegainingDate(
                    $this->AcademicYear->currentAcademicyear()
                );
                if (!empty($this->college_ids)) {
                    $clearance_request = TableRegistry::getTableLocator()->get('Clearance')->count_clearnce_request(null, $this->college_ids, DAYS_BACK_CLEARANCE, $current_academic_year_start_date);
                } elseif (!empty($this->department_ids)) {
                    $clearance_request = TableRegistry::getTableLocator()->get('Clearance')->count_clearnce_request($this->department_ids, null, DAYS_BACK_CLEARANCE, $current_academic_year_start_date);
                }
                //$this->set(compact('clearance_request'));
                //$this->set('_serialize', array('clearance_request'));
            }
        }

        //courseExemptions/list_exemption_request
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseExemptions/list_exemption_request')) {
                if (!empty($this->college_ids)) {
                    $exemption_request = TableRegistry::getTableLocator()->get('CourseExemption')->count_exemption_request($this->role_id, null, $this->college_ids);
                } elseif (!empty($this->department_ids)) {
                    $exemption_request = TableRegistry::getTableLocator()->get('CourseExemption')->count_exemption_request($this->role_id, $this->department_ids, null);
                }
            }
        }

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseExemptions/list_exemption_request')) {
                if (!empty($this->department_id)) {
                    $exemption_request = TableRegistry::getTableLocator()->get('CourseExemption')->count_exemption_request($this->role_id, $this->department_id, null);
                }
                //$this->set(compact('exemption_request'));
                // $this->set('_serialize', array('exemption_request'));
            }
        }
        //substitution
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseSubstitutionRequests/approve_substitution')) {
                $substitution_request = TableRegistry::getTableLocator()->get('CourseSubstitutionRequest')->count_substitution_request($this->department_id);
            }
        }

        $this->set([
            'clearance_request'=>$clearance_request,
            'substitution_request'=>$substitution_request,
            'exemption_request'=>$exemption_request,
            '_serialize' => ['clearance_request','substitution_request','exemption_request']
        ]);

    }

    public function addDropRequestList()
    {
        //course_drops/approve_drops
        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        $drop_request = 0;
        $drop_request_dpt = 0;
        $forced_drops = 0;
        $add_request_dpt = 0;
        $add_request = 0;

        $current_acy = $this->AcademicYear->currentAcademicyear();

        if (is_numeric(ACY_BACK_COURSE_ADD_DROP_APPROVAL) && ACY_BACK_COURSE_ADD_DROP_APPROVAL) {
            $ac_yearsAddDrop = $this->AcademicYear->academicYearInArray(((explode('/', $current_acy)[0]) - ACY_BACK_COURSE_ADD_DROP_APPROVAL), (explode('/', $current_acy)[0]));
        } else {
            $ac_yearsAddDrop[$current_acy] = $current_acy;
        }

        $ac_yearsAddDrop = array_keys($ac_yearsAddDrop);

        if (
            $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT ||
            $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE ||
            $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR
        ) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseDrops/approve_drops')) {
                if ($this->role_id == ROLE_REGISTRAR) {
                    if (!empty($this->department_ids)) {
                        $drop_request = TableRegistry::getTableLocator()->get('CourseDrop')->count_drop_request($this->department_ids);
                    } elseif (!empty($this->college_ids)) {
                        $drop_request = TableRegistry::getTableLocator()->get('CourseDrop')->count_drop_request(null, 1, $this->college_ids);
                    }
                } else {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $drop_request_dpt = TableRegistry::getTableLocator()->get('CourseDrop')->count_drop_request($this->department_id, 2);
                    } elseif ($this->role_id == ROLE_COLLEGE) {
                        $drop_request = TableRegistry::getTableLocator()->get('CourseDrop')->count_drop_request(null, 3, $this->college_id);
                    }
                }
            }

            if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
                if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseDrops/forced_drop')) {
                    if (!empty($this->department_ids)) {
                        $forced_drops = TableRegistry::getTableLocator()->get('CourseDrop')->list_of_students_need_force_drop($this->department_ids, null, $this->program_ids, $this->program_type_ids, $current_acy);
                    } elseif (!empty($this->college_ids)) {
                        $forced_drops = TableRegistry::getTableLocator()->get('CourseDrop')->list_of_students_need_force_drop(null, $this->college_ids, $this->program_ids, $this->program_type_ids, $current_acy, null, 1);
                    }

                    if (count($forced_drops) && $forced_drops['count'] != 0) {
                        //$forced_drops = count($forced_drops) - 1;
                        $forced_drops = $forced_drops['count'];
                    } else {
                        $forced_drops = 0;
                    }
                    //$this->set(compact('forced_drops'));
                }
            }

            //course_adds/approve_adds
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseAdds/approve_adds')) {
                if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
                    if (!empty($this->department_ids)) {
                        $add_request = TableRegistry::getTableLocator()->get('CourseAdd')->count_add_request(
                            $this->department_ids,
                            1,
                            null,
                            $this->program_ids,
                            $this->program_type_ids,
                            $ac_yearsAddDrop
                        );
                    } elseif (!empty($this->college_ids)) {
                        $add_request = TableRegistry::getTableLocator()->get('CourseAdd')->count_add_request(null, 1, $this->college_ids, $this->program_ids, $this->program_type_ids, $ac_yearsAddDrop);
                    }
                } else {
                    if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
                        $add_request_dpt = TableRegistry::getTableLocator()->get('CourseAdd')->count_add_request($this->department_id, 2, null, null, null, $ac_yearsAddDrop);
                    } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE) {
                        $add_request = TableRegistry::getTableLocator()->get('CourseAdd')->count_add_request(null, 3, $this->college_id, null, null, $ac_yearsAddDrop);
                    }
                }
            }
        }




        $this->set([
            'drop_request'=>$drop_request,
            'drop_request_dpt'=>$drop_request_dpt,
            'add_request'=>$add_request,
            'add_request_dpt'=>$add_request_dpt,
            'forced_drops'=>$forced_drops,
            '_serialize' => [ 'drop_request',
                'drop_request_dpt',
                'add_request',
                'add_request_dpt',
                'forced_drops']
        ]);
    }

    // Introduced for the purpose of optimization, the login process is becoming slow becuse of too many queries
    public function getMessageAjax()
    {

        try {

            $this->request->allowMethod(['post','ajax','get']); // Ensure the request is AJAX
            $this->viewBuilder()->setClassName('Json'); // Set response format to JSON

            $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
            $autoMessages = $autoMessagesTable->getMessages($this->Auth->user('id'));

            $response = [
                'status' => 'success',
                'auto_messages' => $autoMessages
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }


        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($response));

    }

    public function getRankAjax()
    {
        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) {
            $rank = TableRegistry::getTableLocator()->get('StudentExamStatus')->displayStudentRank(
                $this->student_id,
                $this->AcademicYear->currentAcademicyear()
            );

        }
        $this->set([
            'rank'=>$rank,
            '_serialize' => [ 'rank']
        ]);

    }

    public function getStudentAssignedDormitory()
    {

        $this->request->allowMethod(['post', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) {
            $dormAssignedStudent = TableRegistry::getTableLocator()->get('DormitoryAssignment')->getStudentAssignedDormitory($this->student_id);
          //  $this->set('dormAssignedStudent', $dormAssignedStudent);
          //  $this->set('_serialize', array('dormAssignedStudent'));
        }

        $this->set([
            'dormAssignedStudent'=>$dormAssignedStudent,
            '_serialize' => [ 'dormAssignedStudent']
        ]);
    }

    public function getCourseSchedule()
    {
        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) {
            $student_course_schedules = array();
            // $student_course_schedules = ClassRegistry::init('CourseSchedule')->getCourseSchedulesForStudent($this->student_id, $this->AcademicYear->currentAcademicyear());

            $this->set([
                'student_course_schedules'=>student_course_schedules,
                '_serialize' => [ 'student_course_schedules']
            ]);

        } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_INSTRUCTOR) {
            $instructor_course_schedules = TableRegistry::getTableLocator()->get('CourseSchedule')->getCourseSchedulesForInstructor($this->Auth->user('id'), $this->role_id);

            $this->set([
                'instructor_course_schedules'=>$instructor_course_schedules,
                '_serialize' => [ 'instructor_course_schedules']
            ]);
        }
    }

    public function getApprovalRejectGrade()
    {

        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        $current_acy = $this->AcademicYear->currentAcademicyear();

        if (is_numeric(ACY_BACK_GRADE_APPROVAL_DASHBOARD) && ACY_BACK_GRADE_APPROVAL_DASHBOARD) {
            $ac_years = $this->AcademicYear->academicYearInArray(((explode('/', $current_acy)[0])  - ACY_BACK_GRADE_APPROVAL_DASHBOARD), (explode('/', $current_acy)[0]));
        } else {
            $ac_years[$current_acy] = $current_acy;
        }

        $ac_years = array_keys($ac_years);

        //If the user has department grade approval privilage
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamGrades/approve_non_freshman_grade_submission')) {
                $courses_for_dpt_approvals = TableRegistry::getTableLocator()->get('ExamGrade')->getRejectedOrNonApprovedPublishedCourseList2($this->department_id, '', '', array(), array(), array(), $ac_years, $this->role_id);
                //debug($courses_for_dpt_approvals);

                $this->set([
                    'courses_for_dpt_approvals'=>$courses_for_dpt_approvals,
                    '_serialize' => [ 'courses_for_dpt_approvals']
                ]);
            }
        }

        //If the user has regustrar grade confirmation privilage
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamGrades/confirm_grade_submission')) {
                $courses_for_registrar_approval = TableRegistry::getTableLocator()->get('ExamGrade')->getRegistrarNonApprovedCoursesList2($this->department_ids, $this->college_ids, '', '', $this->program_ids, $this->program_type_ids, $ac_years);

                $this->set([
                    'courses_for_registrar_approval'=>$courses_for_registrar_approval,
                    '_serialize' => [ 'courses_for_registrar_approval']
                ]);
            }
        }
    }

    public function getApprovalRejectGradeChange()
    {

        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');


        $exam_grade_change_requests = 0;
        $makeup_exam_grades = 0;
        $rejected_makeup_exams = 0;
        $rejected_supplementary_exams = 0;
        $exam_grade_changes_for_college_approval = 0;
        $reg_exam_grade_change_requests = 0;
        $reg_makeup_exam_grades = 0;
        $reg_supplementary_exam_grades = 0;
        $fm_exam_grade_change_requests = 0;
        $fm_makeup_exam_grades = 0;
        $fm_rejected_makeup_exams = 0;
        $fm_rejected_supplementary_exams = 0;

        $departmentIDs =  array();

        if (!empty($this->department_ids)) {
            $departmentIDs = $this->department_ids;
        }

        //If the user has college grade change approval privilage
        if ($this->request->getSession()->read('Auth.User.role_id')
            == ROLE_COLLEGE) {
            if ($this->MenuOptimized->check($this->Auth->user(),
                'controllers/ExamGradeChanges/manage_college_grade_change')) {


                $exam_grade_changes_for_college_approval = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfGradeChangeForCollegeApproval($this->college_id);
                $exam_grade_changes_for_college_approval_sum = 0;

                if (!empty($exam_grade_changes_for_college_approval)) {
                    foreach ($exam_grade_changes_for_college_approval as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            foreach ($value2 as $key3 => $value3) {
                                $exam_grade_changes_for_college_approval_sum += count($value3);
                            }
                        }
                    }
                }

                $exam_grade_changes_for_college_approval = $exam_grade_changes_for_college_approval_sum;

            }
        }

        //If the user has department grade change approval privilage
        if (
            $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT
            || $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE
        ) {
            $departmentIDs = array();

            if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
                $departmentIDs[] = $this->department_id;
            } else {
                $departmentIDs = $this->department_ids;
            }


            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamGradeChanges/manage_department_grade_change')) {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                    $exam_grade_change_requests = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfGradeChangeForDepartmentApproval($this->department_id, 1, $departmentIDs);
                    $makeup_exam_grades = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->department_id, 0, 1, $departmentIDs);
                    $rejected_makeup_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->department_id, 1, 1, $departmentIDs);
                    $rejected_supplementary_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getMakeupGradesAskedByDepartmentRejectedByRegistrar($this->department_id, 1, $departmentIDs);
                } else {
                    $exam_grade_change_requests = TableRegistry::getTableLocator()->get('ExamGradeChange')->getListOfGradeChangeForDepartmentApproval($this->college_id, 0, $departmentIDs);
                    $makeup_exam_grades = TableRegistry::getTableLocator()->get('ExamGradeChange')->getListOfMakeupGradeChangeForDepartmentApproval($this->college_id, 0, 0, $departmentIDs);
                    $rejected_makeup_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->college_id, 1, 0, $departmentIDs);
                    $rejected_supplementary_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getMakeupGradesAskedByDepartmentRejectedByRegistrar($this->college_id, 0, $departmentIDs);
                }

                $exam_grade_change_requests_sum = 0;

                if (!empty($exam_grade_change_requests)) {
                    foreach ($exam_grade_change_requests as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $exam_grade_change_requests_sum += count($value2);
                        }
                    }
                }

                $exam_grade_change_requests = $exam_grade_change_requests_sum;

                //debug($exam_grade_change_requests);

                //$makeup_exam_grades = ClassRegistry::init('ExamGradeChange')->getListOfMakeupGradeChangeForDepartmentApproval($this->department_id, 0, 1, $departmentIDs);
                $makeup_exam_grades_sum = 0;

                if (!empty($makeup_exam_grades)) {
                    foreach ($makeup_exam_grades as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $makeup_exam_grades_sum += count($value2);
                        }
                    }
                }

                $makeup_exam_grades = $makeup_exam_grades_sum;

                //$rejected_makeup_exams = ClassRegistry::init('ExamGradeChange')->getListOfMakeupGradeChangeForDepartmentApproval($this->department_id, 1, 1, $departmentIDs);
                $rejected_makeup_exams_sum = 0;

                if (!empty($rejected_makeup_exams)) {
                    foreach ($rejected_makeup_exams as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $rejected_makeup_exams_sum += count($value2);
                        }
                    }
                }

                $rejected_makeup_exams = $rejected_makeup_exams_sum;

                //$rejected_supplementary_exams = ClassRegistry::init('ExamGradeChange')->getMakeupGradesAskedByDepartmentRejectedByRegistrar($this->department_id, 1, $departmentIDs);
                $rejected_supplementary_exams_sum = 0;

                if (!empty($rejected_supplementary_exams)) {
                    foreach ($rejected_supplementary_exams as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $rejected_supplementary_exams_sum += count($value2);
                        }
                    }
                }

                $rejected_supplementary_exams = $rejected_supplementary_exams_sum;
            }
        }

        //If the user has freshman grade change approval privilage
        if (
            $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT
            || $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE
        ) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/ExamGradeChanges/manage_freshman_grade_change')) {
                if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
                    $fm_exam_grade_change_requests = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfGradeChangeForDepartmentApproval($this->department_id, 1, $departmentIDs);
                    $fm_makeup_exam_grades = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->department_id, 0, 1, $departmentIDs);
                    $fm_rejected_makeup_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->department_id, 1, 1, $departmentIDs);
                    $fm_rejected_supplementary_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getMakeupGradesAskedByDepartmentRejectedByRegistrar($this->department_id, 1, $departmentIDs);
                } else {
                    $fm_exam_grade_change_requests = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfGradeChangeForDepartmentApproval($this->college_id, 0, $departmentIDs);
                    $fm_makeup_exam_grades = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->college_id, 0, 0, $departmentIDs);
                    $fm_rejected_makeup_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getListOfMakeupGradeChangeForDepartmentApproval($this->college_id, 1, 0, $departmentIDs);
                    $fm_rejected_supplementary_exams = TableRegistry::getTableLocator()->get('ExamGradeChanges')->getMakeupGradesAskedByDepartmentRejectedByRegistrar($this->college_id, 0, $departmentIDs);
                }

                //$fm_exam_grade_change_requests = ClassRegistry::init('ExamGradeChange')->getListOfGradeChangeForDepartmentApproval($this->college_id, 0, $departmentIDs);
                $fm_exam_grade_change_requests_sum = 0;

                if (!empty($fm_exam_grade_change_requests)) {
                    foreach ($fm_exam_grade_change_requests as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $fm_exam_grade_change_requests_sum += count($value2);
                        }
                    }
                }

                $fm_exam_grade_change_requests = $fm_exam_grade_change_requests_sum;

                //$fm_makeup_exam_grades = ClassRegistry::init('ExamGradeChange')->getListOfMakeupGradeChangeForDepartmentApproval($this->college_id, 0, 0, $departmentIDs);
                $fm_makeup_exam_grades_sum = 0;

                if (!empty($fm_makeup_exam_grades)) {
                    foreach ($fm_makeup_exam_grades as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $fm_makeup_exam_grades_sum += count($value2);
                        }
                    }
                }

                $fm_makeup_exam_grades = $fm_makeup_exam_grades_sum;

                //$fm_rejected_makeup_exams = ClassRegistry::init('ExamGradeChange')->getListOfMakeupGradeChangeForDepartmentApproval($this->college_id, 1, 0, $departmentIDs);
                $fm_rejected_makeup_exams_sum = 0;

                if (!empty($fm_rejected_makeup_exams)) {
                    foreach ($fm_rejected_makeup_exams as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $fm_rejected_makeup_exams_sum += count($value2);
                        }
                    }
                }

                $fm_rejected_makeup_exams = $fm_rejected_makeup_exams_sum;

                //$fm_rejected_supplementary_exams = ClassRegistry::init('ExamGradeChange')->getMakeupGradesAskedByDepartmentRejectedByRegistrar($this->college_id, 0, $departmentIDs);
                $fm_rejected_supplementary_exams_sum = 0;

                if (!empty($fm_rejected_supplementary_exams)) {
                    foreach ($fm_rejected_supplementary_exams as $key => $value) {
                        foreach ($value as $key2 => $value2) {
                            $fm_rejected_supplementary_exams_sum += count($value2);
                        }
                    }
                }

                $fm_rejected_supplementary_exams = $fm_rejected_supplementary_exams_sum;
            }
        }

        //If the user has registrar grade change approval privilage

        if ($this->request->getSession()->read('Auth.User.role_id')
            == ROLE_REGISTRAR) {
            if ($this->MenuOptimized->check($this->Auth->user(),
                'controllers/ExamGradeChanges/manage_registrar_grade_change')) {
                $serviceType='Stat';
                $reg_exam_grade_change_requests =
                    TableRegistry::getTableLocator()->get('ExamGradeChanges')->
                    getListOfGradeChangeForRegistrarApproval($this->department_ids,
                        $this->college_ids, $this->program_ids,
                        $this->program_type_ids,$serviceType);
                $reg_makeup_exam_grades = TableRegistry::getTableLocator()->
                get('ExamGradeChanges')->
                getListOfMakeupGradeChangeForRegistrarApproval($this->department_ids,
                    $this->college_ids, $this->program_ids, $this->program_type_ids,
                    $serviceType);

                $reg_supplementary_exam_grades = TableRegistry::getTableLocator()->get(
                    'ExamGradeChanges'
                )->getListOfMakeupGradeChangeByDepartmentForRegistrarApproval(
                    $this->department_ids,
                    $this->college_ids,
                    $this->program_ids,
                    $this->program_type_ids,
                    $serviceType
                );

            }
        }

        $this->set([

            'makeup_exam_grades'=>$makeup_exam_grades,
            'rejected_makeup_exams'=>$rejected_makeup_exams,
            'rejected_supplementary_exams'=>$rejected_supplementary_exams,
            'exam_grade_changes_for_college_approval'=>$exam_grade_changes_for_college_approval,
            'reg_exam_grade_change_requests'=>$reg_exam_grade_change_requests,
            'reg_makeup_exam_grades'=>$reg_makeup_exam_grades,
            'reg_supplementary_exam_grades'=>$reg_supplementary_exam_grades,
            'fm_exam_grade_change_requests'=>$fm_exam_grade_change_requests,
            'fm_makeup_exam_grades'=>$fm_makeup_exam_grades,
            'fm_rejected_makeup_exams'=>$fm_rejected_makeup_exams,
            'fm_rejected_supplementary_exams'=>$fm_rejected_supplementary_exams,
            '_serialize' => [
                'makeup_exam_grades',
                'rejected_makeup_exams',
                'rejected_supplementary_exams',
                'exam_grade_changes_for_college_approval',
                'reg_exam_grade_change_requests',
                'reg_makeup_exam_grades',
                'reg_supplementary_exam_grades',
                'fm_exam_grade_change_requests',
                'fm_makeup_exam_grades',
                'fm_rejected_makeup_exams',
                'fm_rejected_supplementary_exams']
        ]);

    }

    public function getBackupAccountRequest()
    {
        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');


        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN) {
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/Backups/index')) {
                $latest_backups = TableRegistry::getTableLocator()->get('Backups')->getLatestBackups(3);
                $this->set(compact('latest_backups'));
            }

            $tasks_for_confirmation = TableRegistry::getTableLocator()->get('Votes')->getListOfTaskForConfirmation($this->Auth->user('id'));
            $confirmed_tasks = TableRegistry::getTableLocator()->get('Votes')->getListOfOtherAdminTasks($this->Auth->user('id'));

            $confirmed_taskss = count($confirmed_tasks);
            $password_reset_confirmation_request = 0;
            $admin_cancelation_confirmation_request = 0;
            $admin_assignment_confirmation_request = 0;
            $role_change_confirmation_request = 0;
            $deactivation_confirmation_request = 0;
            $activation_confirmation_request = 0;

            if (!empty($tasks_for_confirmation)) {
                foreach ($tasks_for_confirmation as $value) {
                    if (strcasecmp($value['Vote']['task'], 'Password Reset') == 0) {
                        $password_reset_confirmation_request++;
                    } elseif (strcasecmp($value['Vote']['task'], 'Administrator Cancellation') == 0) {
                        $admin_cancelation_confirmation_request++;
                    } elseif (strcasecmp($value['Vote']['task'], 'Administrator Assignment') == 0) {
                        $admin_assignment_confirmation_request++;
                    } elseif (strcasecmp($value['Vote']['task'], 'Role Change') == 0) {
                        $role_change_confirmation_request++;
                    } elseif (strcasecmp($value['Vote']['task'], 'Account Deactivation') == 0) {
                        $deactivation_confirmation_request++;
                    } elseif (strcasecmp($value['Vote']['task'], 'Account Activation') == 0) {
                        $activation_confirmation_request++;
                    }
                }
            }


            $this->set([
                'password_reset_confirmation_request'=>$password_reset_confirmation_request,
                'admin_cancelation_confirmation_request'=>$admin_cancelation_confirmation_request,
                'admin_assignment_confirmation_request'=>$admin_assignment_confirmation_request,
                'confirmed_taskss'=>$confirmed_taskss,
                'role_change_confirmation_request'=>$role_change_confirmation_request,
                'deactivation_confirmation_request'=>$deactivation_confirmation_request,
                'activation_confirmation_request'=>$activation_confirmation_request,
                'latest_backups'=>$latest_backups,
                '_serialize' => ['password_reset_confirmation_request',
                    'admin_cancelation_confirmation_request',
                    'admin_assignment_confirmation_request',
                    'confirmed_taskss',
                    'role_change_confirmation_request',
                    'deactivation_confirmation_request',
                    'activation_confirmation_request',
                    'latest_backups']
            ]);

        }
    }

    public function courseSchedule()
    {
        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) {
            /* $student_course_schedules = ClassRegistry::init('CourseSchedule')->getCourseSchedulesForStudent($this->student_id);
            $this->set(compact('section_course_schedule', 'starting_and_ending_hour'));
            $this->set('_serialize', array('section_course_schedule', 'starting_and_ending_hour')); */
        } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_INSTRUCTOR) {
            $instructor_course_schedules = TableRegistry::getTableLocator()->get('CourseSchedules')->getCourseSchedulesForInstructor($this->Auth->user('id'), $this->role_id);

            $this->set([
                'instructor_course_schedules'=>$instructor_course_schedules,
                '_serialize' => ['instructor_course_schedules']
            ]);
        }
    }

    public function disptachedAssignedCourseList()
    {

        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setClassName('Json');


        if (
            $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT ||
            $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE
        ) {
            //If the user has instructor assignment
            if ($this->MenuOptimized->check($this->Auth->user(), 'controllers/CourseInstructorAssignments/assign_course_instructor')) {
                $dispatched_course_not_assigned = TableRegistry::getTableLocator()->get('CourseInstructorAssignments')->getDisptachedCoursesNotAssigned($this->department_id);
                $dispatched_course_list = TableRegistry::getTableLocator()->get('CourseInstructorAssignments')->getDisptachedCoursesForNotification($this->department_id);

                $this->set(compact(
                    'dispatched_course_not_assigned',
                    'dispatched_course_list'
                ));


                $this->set([
                    'dispatched_course_list'=>$dispatched_course_list,
                    '_serialize' => ['dispatched_course_list']
                ]);
            }
        }
    }

    public function view_logs()
    {
        $logs  = array();

        if (!empty($this->request->getData())) {
            // $this->Model->findUserActions(301, array('fields' => array('id','model'),'model' => 'BookTest');
            $params = array();

            $params['fields'] = array(
                'id',
                'model',
                'user_id',
                'ip',
                'foreign_key',
                'description',
                'action',
                'change',
                'created'
            );

            if ($this->request->getData('Dashboard.username') !== null) {
                $username = $this->request->getData('Dashboard.username');
                $params['conditions'][] = "user_id IN (SELECT id FROM users WHERE username like '%$username%' )";
            }

            if (!empty($this->request->getData('Dashboard.action'))) {
                $params['conditions']['action'] = $this->request->getData('Dashboard.action');
            }

            if (!empty($this->request->getData('Dashboard.action'))) {
                $params['conditions']['model'] = $this->request->getData('Dashboard.action');
            }

            if (!empty($this->request->getData('Dashboard.change_date_from'))) {
                $change_date_from = $this->request->getData('Dashboard.change_date_from');
                $params['conditions']['created >='] = $change_date_from['year'] . '-' . $change_date_from['month'] . '-' . $change_date_from['day'];
            }

            if (!empty($this->request->getData('Dashboard.change_date_to'))) {
                $change_date_to = $this->request->getData('Dashboard.change_date_to');

                $params['conditions']['created <='] = $change_date_to['year'] . '-' . $change_date_to['month'] . '-' . $change_date_to['day'] . ' ';
            }

            if (!empty($this->request->getData('Dashboard.limit'))) {
                $params['limit'] = $this->request->getData('Dashboard.limit');
            } else {
                $params['limit'] = 5;
            }

            $logs = TableRegistry::getTableLocator()->get('Users')->getUserLogDetail($this->Auth->user('id'), $params);
        }

        $this->set(compact('logs'));
    }
}
