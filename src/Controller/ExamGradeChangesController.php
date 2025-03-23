<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ExamGradeChangesController extends AppController
{

    public $name = 'ExamGradeChanges';


    public $menuOptions = array(
        'parent' => 'examGrades',
        'controllerButton' => false,
        'exclude' => array('*'), //array('index'),
        'alias' => array(
            'manage_department_grade_change' => 'Approve Grade Change',
            'manage_college_grade_change' => 'Manage Grade Change',
            'manage_registrar_grade_change' => 'Manage Grade Change',
            'department_makeup_exam_result' => 'Supplementary Exam',
            'freshman_makeup_exam_result' => 'Freshman Supplementary Exam',
            'manage_freshman_grade_change' => 'Manage Freshman Grade Change',
            'cancel_auto_grade_change' => 'Cancel Auto Grade Change'
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);

        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $curr_ac_year_expoded = explode('/', $defaultacademicyear);

        $previous_academicyear = $defaultacademicyear;

        if (!empty($curr_ac_year_expoded)) {
            $previous_academicyear = ($curr_ac_year_expoded[0] - 1) . '/' . ($curr_ac_year_expoded[1] - 1);
        }

        //debug($previous_academicyear);

        $acyear_array_data = $this->AcademicYear->academicYearInArray(
            APPLICATION_START_YEAR,
            (explode('/', $defaultacademicyear)[0])
        );
        //debug($acyear_array_data);

        //$this->set('defaultacademicyear', $defaultacademicyear);


        $programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.active' => 1)));
        $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.active' => 1))
        );

        $depts_for_year_level = ClassRegistry::init('Department')->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );

        //$yearLevels = $this->year_levels;
        $yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(
            null,
            null,
            array_keys($depts_for_year_level),
            array_keys($programs)
        );
        //debug($yearLevels);

        if ($this->role_id == ROLE_DEPARTMENT) {
            //$yearLevels = ClassRegistry::init('YearLevel')->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id, 'YearLevel.name' => $yearLevels)));
        }

        if (($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_COLLEGE) && $this->Session->read(
                'Auth.User'
            )['is_admin'] == 0) {
            $programs = ClassRegistry::init('Program')->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
            );
            $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
            );
        }

        //$academicYearRange = new AcademicYearComponent(new ComponentCollection);
        $academicYearRange = new $this->AcademicYear(new ComponentCollection());
        $years_to_look_list_for_display = $academicYearRange->academicYearInArray(
            ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL),
            (explode('/', $defaultacademicyear)[0])
        );

        if (count($years_to_look_list_for_display) >= 2) {
            // $years_to_look_list_for_display = array_values($years_to_look_list_for_display);
            // $endYear = $years_to_look_list_for_display[0];
            // $startYear = end($years_to_look_list_for_display);
            $startYr = array_pop($years_to_look_list_for_display);
            $endYr = reset($years_to_look_list_for_display);
            $years_to_look_list_for_display = 'from ' . $startYr . ' up to ' . $endYr;
        } else {
            if (count($years_to_look_list_for_display) == 1) {
                $years_to_look_list_for_display = ' on ' . $defaultacademicyear;
            } else {
                $years_to_look_list_for_display = '';
            }
        }

        debug($years_to_look_list_for_display);

        //debug($yearLevels);

        $this->set(
            compact(
                'acyear_array_data',
                'program_types',
                'defaultacademicyear',
                'previous_academicyear',
                'programTypes',
                'programs',
                'yearLevels',
                'years_to_look_list_for_display'
            )
        );

        unset($this->request->data['User']['password']);
    }

    public function index()
    {

        if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/freshman_grade_view')) {
            return $this->redirect(array('controller' => 'exam_grades', 'action' => 'freshman_grade_view'));
        } else {
            return $this->redirect(array('controller' => 'exam_grades', 'action' => 'department_grade_view'));
        }
    }

    //College Freshman
    public function manage_freshman_grade_change()
    {

        $this->__manage_grade_change(0);
        $this->render('manage_department_grade_change');
    }

    //DEPARTMENT
    public function manage_department_grade_change()
    {

        //Role based checking is removed
        //if($this->role_id == 6) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_COLLEGE) {
            $this->__manage_grade_change(1);
            $this->render('manage_department_grade_change');
        } else {
            $this->Flash->error(
                'You need to have department role to access exam grade change management area. Please contact your system administrator to get department role.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    //DEPARTMENT and COLLEGE freshman grade change (common)
    private function __manage_grade_change($department = 1)
    {

        $departmentIDs = array();

        //Role based checking is removed
        //if($this->role_id == 6 || $this->role_id == 5) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_COLLEGE) {
            if ($department) {
                $col_or_dpt_id = $this->department_id;
                $departmentIDs[] = $this->department_id;
            } else {
                $col_or_dpt_id = $this->college_id;
                $departmentIDs = $this->department_ids;
            }


            debug($departmentIDs);
            debug($col_or_dpt_id);

            $exam_grade_changes = $this->ExamGradeChange->getListOfGradeChangeForDepartmentApproval(
                $col_or_dpt_id,
                $department,
                $departmentIDs
            );

            //getListOfGradeChangeForCollegeApproval
            //debug($exam_grade_changes);

            $makeup_exam_grade_changes = $this->ExamGradeChange->getListOfMakeupGradeChangeForDepartmentApproval(
                $col_or_dpt_id,
                0,
                $department,
                $departmentIDs
            );
            $rejected_makeup_exam_grade_changes = $this->ExamGradeChange->getListOfMakeupGradeChangeForDepartmentApproval(
                $col_or_dpt_id,
                1,
                $department,
                $departmentIDs
            );
            $rejected_department_makeup_exam_grade_changes = $this->ExamGradeChange->getMakeupGradesAskedByDepartmentRejectedByRegistrar(
                $col_or_dpt_id,
                $department,
                $departmentIDs
            );
            //debug($this->request->data);

            if (isset($this->request->data) && !isset($this->request->data['ApproveAllGradeChangeByDepartment'])) {
                if (isset($this->request->data['ExamGradeChange']['grade_change_count']) && $this->request->data['ExamGradeChange']['grade_change_count'] != 0) {
                    $approvals_count = 0;
                    $rejections_count = 0;
                    $rejected_rejections = 0;
                    $accepted_rejections = 0;

                    for ($i = 1; $i <= $this->request->data['ExamGradeChange']['grade_change_count']; $i++) {
                        if (isset($this->request->data['approveGradeChangeByDepartment_' . $i])) {
                            $exam_grade_change_detail = $this->ExamGradeChange->find('first', array(
                                'conditions' => array('ExamGradeChange.id' => $this->request->data['ExamGradeChange'][$i]['id']),
                                'contain' => array(
                                    'ExamGrade' => array(
                                        'CourseRegistration' => array('PublishedCourse'),
                                        'CourseAdd' => array('PublishedCourse')
                                    )
                                )
                            ));

                            if (empty($exam_grade_change_detail)) {
                                $this->Flash->error(
                                    'The system unable to find the exam grade change request. It happens when the grade change request is canceled in the middle of approval process. Please try again.'
                                );
                            } else {
                                if ((isset($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "" && (($department == 1 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'])
                                            //|| ($department == 0 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'])))
                                            || ($department == 0 && !in_array(
                                                    $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'],
                                                    $departmentIDs
                                                ))))
                                    || (isset($exam_grade_change_detail['ExamGrade']['CourseAdd']) && !empty($exam_grade_change_detail['ExamGrade']['CourseAdd']) && $exam_grade_change_detail['ExamGrade']['CourseAdd']['id'] != "" && (($department == 1 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'])
                                            //|| ($department == 0 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'])))
                                            || ($department == 0 && !in_array(
                                                    $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'],
                                                    $departmentIDs
                                                ))))
                                ) {
                                    debug($exam_grade_change_detail);
                                    debug($department);
                                    $this->Flash->error(
                                        'You are not authorized to manage the selected exam grade change request.'
                                    );
                                } else {
                                    if ($exam_grade_change_detail['ExamGradeChange']['department_approval'] == 1 && ($exam_grade_change_detail['ExamGradeChange']['registrar_approval'] == 1 || $exam_grade_change_detail['ExamGradeChange']['registrar_approval'] == null)) {
                                        $this->Flash->error(
                                            'The selected grade change request is already processed. Please use the following report tool to get details on the status of the grade change request.'
                                        );
                                        return $this->redirect(array('action' => 'index'));
                                    } else {
                                        $department_grade_change_approval = array();

                                        if (is_null(
                                            $exam_grade_change_detail['ExamGradeChange']['department_approval']
                                        )) {
                                            $department_grade_change_approval['id'] = $this->request->data['ExamGradeChange'][$i]['id'];
                                            $department_grade_change_approval['department_approval'] = (isset($this->request->data['ExamGradeChange'][$i]['department_approval']) ? ($this->request->data['ExamGradeChange'][$i]['department_approval'] == 1 ? 1 : -1) : -1);
                                            $department_grade_change_approval['department_reason'] = trim(
                                                $this->request->data['ExamGradeChange'][$i]['department_reason']
                                            );
                                            $department_grade_change_approval['department_approval_date'] = date(
                                                'Y-m-d H:i:s'
                                            );
                                            $department_grade_change_approval['department_approved_by'] = $this->Auth->user(
                                                'id'
                                            );

                                            if ($department_grade_change_approval['department_approval'] == 1) {
                                                $approvals_count++;
                                            } else {
                                                if ($department_grade_change_approval['department_approval'] == -1) {
                                                    $rejections_count++;
                                                }
                                            }
                                        } else {
                                            if (!empty($exam_grade_change_detail['ExamGradeChange']['grade'])) {
                                                $department_grade_change_approval['exam_grade_id'] = $exam_grade_change_detail['ExamGradeChange']['exam_grade_id'];
                                                $department_grade_change_approval['grade'] = $exam_grade_change_detail['ExamGradeChange']['grade'];
                                                $department_grade_change_approval['minute_number'] = (isset($exam_grade_change_detail['ExamGradeChange']['minute_number']) && !empty($exam_grade_change_detail['ExamGradeChange']['minute_number']) ? trim(
                                                    $exam_grade_change_detail['ExamGradeChange']['minute_number']
                                                ) : '');
                                                $department_grade_change_approval['makeup_exam_id'] = (isset($exam_grade_change_detail['ExamGradeChange']['makeup_exam_id']) && !empty($exam_grade_change_detail['ExamGradeChange']['makeup_exam_id']) ? $exam_grade_change_detail['ExamGradeChange']['makeup_exam_id'] : null);
                                                $department_grade_change_approval['makeup_exam_result'] = (isset($exam_grade_change_detail['ExamGradeChange']['makeup_exam_result']) && (!empty($exam_grade_change_detail['ExamGradeChange']['makeup_exam_result']) || $exam_grade_change_detail['ExamGradeChange']['makeup_exam_result'] == 0) ? $exam_grade_change_detail['ExamGradeChange']['makeup_exam_result'] : null);
                                                $department_grade_change_approval['initiated_by_department'] = (isset($exam_grade_change_detail['ExamGradeChange']['initiated_by_department']) && !empty($exam_grade_change_detail['ExamGradeChange']['initiated_by_department']) ? $exam_grade_change_detail['ExamGradeChange']['initiated_by_department'] : 0);
                                                $department_grade_change_approval['result'] = (isset($exam_grade_change_detail['ExamGradeChange']['result']) && !empty($exam_grade_change_detail['ExamGradeChange']['result']) ? $exam_grade_change_detail['ExamGradeChange']['result'] : null);
                                                $department_grade_change_approval['department_reply'] = 1;
                                                $department_grade_change_approval['department_approval'] = (isset($this->request->data['ExamGradeChange'][$i]['department_approval']) ? ($this->request->data['ExamGradeChange'][$i]['department_approval'] == 1 ? 1 : -1) : -1);
                                                $department_grade_change_approval['department_reason'] = (isset($this->request->data['ExamGradeChange'][$i]['department_reason']) && !empty($this->request->data['ExamGradeChange'][$i]['department_reason']) ? trim(
                                                    $this->request->data['ExamGradeChange'][$i]['department_reason']
                                                ) : '');
                                                $department_grade_change_approval['department_approval_date'] = date(
                                                    'Y-m-d H:i:s'
                                                );
                                                $department_grade_change_approval['department_approved_by'] = $this->Auth->user(
                                                    'id'
                                                );

                                                if (isset($department_grade_change_approval['department_reason']) && !empty($department_grade_change_approval['department_reason'])) {
                                                    $department_grade_change_approval['reason'] = $department_grade_change_approval['department_reason'];
                                                } else {
                                                    if (isset($exam_grade_change_detail['ExamGradeChange']['reason']) && empty($exam_grade_change_detail['ExamGradeChange']['reason'])) {
                                                        $department_grade_change_approval['reason'] = $exam_grade_change_detail['ExamGradeChange']['reason'];
                                                    } else {
                                                        $department_grade_change_approval['reason'] = '';
                                                    }
                                                }

                                                if ($department_grade_change_approval['department_approval'] == 1) {
                                                    $approvals_count++;
                                                    $accepted_rejections++;
                                                } else {
                                                    if ($department_grade_change_approval['department_approval'] == -1) {
                                                        $rejections_count++;
                                                        $rejected_rejections++;
                                                    }
                                                }

                                                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                                                    if (isset($department_grade_change_approval['department_reason']) && !empty($department_grade_change_approval['department_reason'])) {
                                                        $department_grade_change_approval['college_reason'] = $department_grade_change_approval['department_reason'];
                                                    } else {
                                                        if (isset($exam_grade_change_detail['ExamGradeChange']['reason']) && empty($exam_grade_change_detail['ExamGradeChange']['reason'])) {
                                                            $department_grade_change_approval['reason'] = $exam_grade_change_detail['ExamGradeChange']['reason'];
                                                            $department_grade_change_approval['college_reason'] = '';
                                                        } else {
                                                            $department_grade_change_approval['college_reason'] = '';
                                                        }
                                                    }

                                                    $department_grade_change_approval['college_approval'] = $department_grade_change_approval['department_approval'];
                                                    $department_grade_change_approval['college_approval_date'] = $department_grade_change_approval['department_approval_date'];
                                                    $department_grade_change_approval['college_approved_by'] = $department_grade_change_approval['department_approved_by'];
                                                } else {
                                                    $department_grade_change_approval['college_reason'] = '';
                                                    $department_grade_change_approval['college_approval'] = null;
                                                    $department_grade_change_approval['college_approval_date'] = null;
                                                    $department_grade_change_approval['college_approved_by'] = '';
                                                }

                                                $department_grade_change_approval['registrar_reason'] = '';
                                                $department_grade_change_approval['registrar_approval'] = null;
                                                $department_grade_change_approval['registrar_approval_date'] = null;
                                                $department_grade_change_approval['registrar_approved_by'] = '';
                                            }
                                        }

                                        if (!empty($department_grade_change_approval)) {
                                            if ($this->ExamGradeChange->save(
                                                $department_grade_change_approval,
                                                array('validate' => false)
                                            )) {
                                                //Notifications
                                                ClassRegistry::init(
                                                    'AutoMessage'
                                                )->sendNotificationOnDepartmentGradeChangeApproval(
                                                    $department_grade_change_approval
                                                );
                                                //$this->Flash->success('Your exam grade change request approval was successful.');
                                                if ($rejections_count == 0 && $rejected_rejections == 0) {
                                                    $this->Flash->success(
                                                        'Your exam grade change request approval was successful.'
                                                    );
                                                } else {
                                                    if ($rejections_count != 0) {
                                                        $this->Flash->success(
                                                            'Your exam grade change request approval/rejection was successful.'
                                                        );
                                                    } else {
                                                        if ($rejected_rejections != 0) {
                                                            $this->Flash->success(
                                                                'Your exam grade change request approval/rejecting rejections was successful.'
                                                            );
                                                        }
                                                    }
                                                }
                                                return $this->redirect(
                                                    array('action' => ($department == 1 ? 'manage_department_grade_change' : 'manage_freshman_grade_change'))
                                                );
                                            } else {
                                                $this->Flash->error(
                                                    'The system is unable to complete your exam grade change request approval. Please try again.'
                                                );
                                                return $this->redirect(
                                                    array('action' => ($department == 1 ? 'manage_department_grade_change' : 'manage_freshman_grade_change'))
                                                );
                                            }
                                        } else {
                                            $this->Flash->error(
                                                'The system is unable to complete your exam grade change request approval. Please try again.'
                                            );
                                            return $this->redirect(
                                                array('action' => ($department == 1 ? 'manage_department_grade_change' : 'manage_freshman_grade_change'))
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (isset($this->request->data) && isset($this->request->data['ApproveAllGradeChangeByDepartment'])) {
                    if (isset($this->request->data['Mass']['ExamGradeChange']['select_all'])) {
                        unset($this->request->data['Mass']['ExamGradeChange']['select_all']);
                    }

                    $sucessfulapproval = 0;

                    if (!empty($this->request->data['Mass']['ExamGradeChange'])) {
                        foreach ($this->request->data['Mass']['ExamGradeChange'] as $grk => $grv) {
                            if ($grv['gp'] == 1) {
                                $exam_grade_change_detail = $this->ExamGradeChange->find('first', array(
                                    'conditions' => array('ExamGradeChange.id' => $grv['id']),
                                    'contain' => array(
                                        'ExamGrade' => array(
                                            'CourseRegistration' => array('PublishedCourse'),
                                            'CourseAdd' => array('PublishedCourse')
                                        )
                                    )
                                ));

                                if (empty($exam_grade_change_detail)) {
                                    //request grade change cancelled
                                } else {
                                    if ((isset($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "" && (($department == 1 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'])
                                                //|| ($department == 0 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'])))
                                                || ($department == 0 && !in_array(
                                                        $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'],
                                                        $departmentIDs
                                                    ))))
                                        || (isset($exam_grade_change_detail['ExamGrade']['CourseAdd']) && !empty($exam_grade_change_detail['ExamGrade']['CourseAdd']) && $exam_grade_change_detail['ExamGrade']['CourseAdd']['id'] != "" && (($department == 1 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'])
                                                //|| ($department == 0 && $col_or_dpt_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['college_id'])))
                                                || ($department == 0 && !in_array(
                                                        $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['given_by_department_id'],
                                                        $departmentIDs
                                                    ))))
                                    ) {
                                        //not authorized
                                    } else {
                                        if ($exam_grade_change_detail['ExamGradeChange']['department_approval'] == 1 && ($exam_grade_change_detail['ExamGradeChange']['registrar_approval'] == 1 || $exam_grade_change_detail['ExamGradeChange']['registrar_approval'] == null)) {
                                            //already approved
                                        } else {
                                            $department_grade_change_approval = array();

                                            if ($exam_grade_change_detail['ExamGradeChange']['department_approval'] == null) {
                                                $department_grade_change_approval['id'] = $grv['id'];
                                                $department_grade_change_approval['department_approval'] = $grv['department_approval'];
                                                $department_grade_change_approval['department_reason'] = "Teacher reason accepted!";
                                                $department_grade_change_approval['department_approval_date'] = date(
                                                    'Y-m-d H:i:s'
                                                );
                                                $department_grade_change_approval['department_approved_by'] = $this->Auth->user(
                                                    'id'
                                                );
                                            }

                                            if (!empty($department_grade_change_approval)) {
                                                if ($this->ExamGradeChange->save(
                                                    $department_grade_change_approval,
                                                    array('validate' => false)
                                                )) {
                                                    $sucessfulapproval++;
                                                    //Notifications
                                                    ClassRegistry::init(
                                                        'AutoMessage'
                                                    )->sendNotificationOnDepartmentGradeChangeApproval(
                                                        $department_grade_change_approval
                                                    );
                                                }
                                            } else {
                                                $this->Flash->error(
                                                    'The system is unable to complete your exam grade change request approval. Please try again.'
                                                );
                                                return $this->redirect(
                                                    array('action' => ($department == 1 ? 'manage_department_grade_change' : 'manage_freshman_grade_change'))
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($sucessfulapproval) {
                        $this->Flash->success(
                            'You have approved the selected exam grade change requests successfully.'
                        );
                        return $this->redirect(
                            array('action' => ($department == 1 ? 'manage_department_grade_change' : 'manage_freshman_grade_change'))
                        );
                    }
                }
            }

            $this->set(
                compact(
                    'exam_grade_changes',
                    'makeup_exam_grade_changes',
                    'rejected_makeup_exam_grade_changes',
                    'rejected_department_makeup_exam_grade_changes'
                )
            );
        } else {
            $this->Flash->error(
                'NOT AUTHORIZED!! You need to have either college or department role to access exam grade change management area. Please contact your system administrator if you feel this message is not right.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    //COLLEGE
    public function manage_college_grade_change()
    {

        //Role based checking is removed
        //if($this->role_id == 5 || $this->role_id == 6) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
            $exam_grade_changes = $this->ExamGradeChange->getListOfGradeChangeForCollegeApproval($this->college_id);

            if (isset($this->request->data) && !isset($this->request->data['ApproveAllGradeChangeByCollege'])) {
                if (isset($this->request->data['ExamGradeChange']['grade_change_count']) && $this->request->data['ExamGradeChange']['grade_change_count'] != 0) {
                    for ($i = 1; $i <= $this->request->data['ExamGradeChange']['grade_change_count']; $i++) {
                        if (isset($this->request->data['approveGradeChangeByCollege_' . $i])) {
                            $exam_grade_change_detail = $this->ExamGradeChange->find('first', array(
                                'conditions' => array('ExamGradeChange.id' => $this->request->data['ExamGradeChange'][$i]['id']),
                                'contain' => array(
                                    'ExamGrade' => array(
                                        'CourseRegistration' => array(
                                            'PublishedCourse' => array('Department', 'GivenByDepartment')
                                        ),
                                        'CourseAdd' => array(
                                            'PublishedCourse' => array('Department', 'GivenByDepartment')
                                        )
                                    )
                                )
                            ));

                            $given_by_dept_ids = ClassRegistry::init('Department')->find('list', array(
                                'conditions' => array('Department.college_id' => $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id']),
                                'fields' => array('Department.id', 'Department.id')
                            ));

                            debug($exam_grade_change_detail);

                            if (empty($exam_grade_change_detail)) {
                                $this->Flash->error(
                                    'The system unable to find the exam grade change request. It happens when the grade change request is cancelled in the middle of the approval process. Please try again.'
                                );
                            } else {
                                if ((isset($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "" && ((!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'])
                                            || (!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'] != $this->college_id
                                                || !in_array(
                                                    $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'],
                                                    $given_by_dept_ids
                                                ))))
                                    || (isset($exam_grade_change_detail['ExamGrade']['CourseAdd']) && !empty($exam_grade_change_detail['ExamGrade']['CourseAdd']) && $exam_grade_change_detail['ExamGrade']['CourseAdd']['id'] != "" && ((!empty($exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['Department']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id'])
                                            || (!empty($exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id'])))
                                ) {
                                    debug(
                                        (!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id']) || (!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] != $this->college_id || !in_array(
                                                $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'],
                                                $given_by_dept_ids
                                            ))
                                    );
                                    debug(
                                        (!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'])
                                    );
                                    debug(
                                        !empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] != $this->college_id
                                    );
                                    debug(
                                        !in_array(
                                            $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'],
                                            $given_by_dept_ids
                                        )
                                    );
                                    debug(
                                        $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'] != $this->college_id
                                    );
                                    debug(
                                        strcasecmp(
                                            $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id'],
                                            $this->college_id
                                        ) != 0
                                    );
                                    debug(
                                        $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']
                                    );
                                    debug($this->college_id);
                                    debug($exam_grade_change_detail);
                                    debug($given_by_dept_ids);

                                    $this->Flash->error(
                                        'You are not authorized to manage the selected exam grade change request.'
                                    );
                                } else {
                                    if ($exam_grade_change_detail['ExamGradeChange']['department_approval'] != 1) {
                                        $this->Flash->error(
                                            'The selected grade change request is being processed by the departement. Please try again later.'
                                        );
                                        return $this->redirect(array('action' => 'index'));
                                    } else {
                                        if ($exam_grade_change_detail['ExamGradeChange']['college_approval'] != null) {
                                            $this->Flash->error(
                                                'The selected grade change request is already processed. Please use the following report tool to get details on the status of the grade change request.'
                                            );
                                            return $this->redirect(array('action' => 'index'));
                                        } else {
                                            $college_grade_change_approval = array();
                                            $college_grade_change_approval['id'] = $this->request->data['ExamGradeChange'][$i]['id'];
                                            $college_grade_change_approval['college_approval'] = (isset($this->request->data['ExamGradeChange'][$i]['college_approval']) ? ($this->request->data['ExamGradeChange'][$i]['college_approval'] == 1 ? 1 : -1) : -1);
                                            $college_grade_change_approval['college_reason'] = $this->request->data['ExamGradeChange'][$i]['college_reason'];
                                            $college_grade_change_approval['college_approval_date'] = date(
                                                'Y-m-d H:i:s'
                                            );
                                            $college_grade_change_approval['college_approved_by'] = $this->Auth->user(
                                                'id'
                                            );

                                            if ($this->ExamGradeChange->save(
                                                $college_grade_change_approval,
                                                array('validate' => false)
                                            )) {
                                                //Notifications
                                                ClassRegistry::init(
                                                    'AutoMessage'
                                                )->sendNotificationOnCollegeGradeChangeApproval(
                                                    $college_grade_change_approval
                                                );
                                                $this->Flash->success(
                                                    'Your exam grade change request approval is successfully done.'
                                                );
                                                return $this->redirect(array('action' => 'manage_college_grade_change')
                                                );
                                            } else {
                                                $this->Flash->error(
                                                    'The system is unable to complete your exam grade change request approval. Please try again.'
                                                );
                                                return $this->redirect(array('action' => 'manage_college_grade_change')
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (isset($this->request->data['ApproveAllGradeChangeByCollege'])) {
                    if (isset($this->request->data['Mass']['ExamGradeChange']['select_all'])) {
                        unset($this->request->data['Mass']['ExamGradeChange']['select_all']);
                    }
                    $sucessfulapproval = 0;

                    if (!empty($this->request->data['Mass']['ExamGradeChange'])) {
                        foreach ($this->request->data['Mass']['ExamGradeChange'] as $grk => $grv) {
                            if ($grv['gp'] == 1) {
                                $exam_grade_change_detail = $this->ExamGradeChange->find('first', array(
                                    'conditions' => array('ExamGradeChange.id' => $grv['id']),
                                    'contain' => array(
                                        'ExamGrade' => array(
                                            'CourseRegistration' => array(
                                                'PublishedCourse' => array('Department', 'GivenByDepartment')
                                            ),
                                            'CourseAdd' => array(
                                                'PublishedCourse' => array('Department', 'GivenByDepartment')
                                            )
                                        )
                                    )
                                ));

                                $given_by_dept_ids = ClassRegistry::init('Department')->find('list', array(
                                    'conditions' => array('Department.college_id' => $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id']),
                                    'fields' => array('Department.id', 'Department.id')
                                ));


                                if (empty($exam_grade_change_detail)) {
                                    //request grade change cancelled
                                } else {
                                    if ((isset($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "" && ((!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'])
                                                || (!empty($exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['college_id']) && $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['GivenByDepartment']['college_id'] != $this->college_id
                                                    || !in_array(
                                                        $exam_grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse']['given_by_department_id'],
                                                        $given_by_dept_ids
                                                    ))))
                                        || (isset($exam_grade_change_detail['ExamGrade']['CourseAdd']) && !empty($exam_grade_change_detail['ExamGrade']['CourseAdd']) && $exam_grade_change_detail['ExamGrade']['CourseAdd']['id'] != "" && ((!empty($exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id'])
                                                || (!empty($exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id']) && $this->college_id != $exam_grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse']['GivenByDepartment']['college_id'])))
                                    ) {
                                    } else {
                                        if ($exam_grade_change_detail['ExamGradeChange']['department_approval'] != 1) {
                                        } else {
                                            if ($exam_grade_change_detail['ExamGradeChange']['college_approval'] != null) {
                                            } else {
                                                $college_grade_change_approval = array();
                                                $college_grade_change_approval['id'] = $grv['id'];
                                                $college_grade_change_approval['college_approval'] = 1;
                                                $college_grade_change_approval['college_reason'] = "Teacher reason accepted!";
                                                $college_grade_change_approval['college_approval_date'] = date(
                                                    'Y-m-d H:i:s'
                                                );
                                                $college_grade_change_approval['college_approved_by'] = $this->Auth->user(
                                                    'id'
                                                );

                                                if ($this->ExamGradeChange->save(
                                                    $college_grade_change_approval,
                                                    array('validate' => false)
                                                )) {
                                                    $sucessfulapproval++;
                                                    //Notifications
                                                    ClassRegistry::init(
                                                        'AutoMessage'
                                                    )->sendNotificationOnCollegeGradeChangeApproval(
                                                        $college_grade_change_approval
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($sucessfulapproval) {
                        $this->Flash->success(
                            'You have approved the selected exam grade change requests successfully.'
                        );
                        return $this->redirect(array('action' => 'manage_college_grade_change'));
                    }
                }
            }

            $this->set(compact('exam_grade_changes'));
        } else {
            $this->Flash->error(
                'NOT AUTHORIZED!! You need to have either college or department role to access exam grade change management area. Please contact your system administrator if you feel this message is not right.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    public function cancel_auto_grade_change()
    {

        if (isset($this->request->data) && !empty($this->request->data['cancelAutoGrade'])) {
            $gradeToBeCancelled = array();

            foreach ($this->request->data['ExamGradeChange'] as $key => $student) {
                if (is_int($key) && $student['gp'] == 1) {
                    $gradeToBeCancelled[] = $student['id'];
                }
            }

            if (isset($gradeToBeCancelled) && !empty($gradeToBeCancelled)) {
                if ($this->ExamGradeChange->deleteAll(array('ExamGradeChange.id' => $gradeToBeCancelled), false)) {
                    $this->Flash->success(
                        'You have cancelled ' . count($gradeToBeCancelled) . ' auto converted grades.'
                    );
                }
            }
        }

        if (isset($this->request->data) && !empty($this->request->data['listPublishedCourses'])) {
            if (isset($this->college_ids) && !empty($this->college_ids)) {
                $type = 1;
            } else {
                if (isset($this->department_ids) && !empty($this->department_ids)) {
                    $type = 0;
                }
            }

            debug($this->request->data);

            $examGradeChanges = $this->ExamGradeChange->getListOfGradeAutomaticallyConverted(
                $this->request->data['ExamGradeChange']['acadamic_year'],
                $this->request->data['ExamGradeChange']['semester'],
                $this->request->data['ExamGradeChange']['department_id'],
                $this->request->data['ExamGradeChange']['program_id'],
                $this->request->data['ExamGradeChange']['program_type_id'],
                $this->request->data['ExamGradeChange']['grade'],
                $type
            );

            debug($examGradeChanges);
            $this->set(compact('examGradeChanges'));
        }

        if (isset($this->college_ids) && !empty($this->college_ids)) {
            $departments = ClassRegistry::init('College')->find(
                'list',
                array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
            );
        } elseif (isset($this->department_ids) && !empty($this->department_ids)) {
            $departments = ClassRegistry::init('Department')->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids, 'Department.active' => 1))
            );
        }

        $this->set(compact('departments'));
    }

    //REGISTRAR
    public function manage_registrar_grade_change()
    {

        //Role based checking is removed
        //if($this->role_id == 4) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
            //debug($this->department_ids);
            //debug($this->college_ids);
            $exam_grade_changes = $this->ExamGradeChange->getListOfGradeChangeForRegistrarApproval(
                $this->department_ids,
                $this->college_ids,
                $this->program_ids,
                $this->program_type_ids
            );
            $makeup_exam_grade_changes = $this->ExamGradeChange->getListOfMakeupGradeChangeForRegistrarApproval(
                $this->department_ids,
                $this->college_ids,
                $this->program_ids,
                $this->program_type_ids
            );
            $department_makeup_exam_grade_changes = $this->ExamGradeChange->getListOfMakeupGradeChangeByDepartmentForRegistrarApproval(
                $this->department_ids,
                $this->college_ids,
                $this->program_ids,
                $this->program_type_ids
            );

            if (isset($this->request->data) && !isset($this->request->data['ApproveAllGradeChangeByRegistrar'])) {
                if (isset($this->request->data['ExamGradeChange']['grade_change_count']) && $this->request->data['ExamGradeChange']['grade_change_count'] != 0) {
                    for ($i = 1; $i <= $this->request->data['ExamGradeChange']['grade_change_count']; $i++) {
                        if (isset($this->request->data['approveGradeChangeByRegistrar_' . $i])) {
                            $exam_grade_change_detail = $this->ExamGradeChange->find('first', array(
                                'conditions' => array('ExamGradeChange.id' => $this->request->data['ExamGradeChange'][$i]['id']),
                                'contain' => array(
                                    'ExamGrade' => array(
                                        'CourseRegistration' => array('PublishedCourse' => array('Department')),
                                        'CourseAdd' => array('PublishedCourse' => array('Department'))
                                    )
                                )
                            ));

                            if (empty($exam_grade_change_detail)) {
                                $this->Flash->error(
                                    'The system unable to find the exam grade change request. It happens when the grade change request is canceled in the middle of the approval process. Please try again.'
                                );
                            } elseif ($exam_grade_change_detail['ExamGradeChange']['department_approval'] != 1 || ($exam_grade_change_detail['ExamGradeChange']['makeup_exam_result'] == null && $exam_grade_change_detail['ExamGradeChange']['college_approval'] != 1)) {
                                $this->Flash->error(
                                    'The selected grade change request is being processed by the department and/or college. Please try again later.'
                                );
                                return $this->redirect(array('action' => 'index'));
                            } elseif ($exam_grade_change_detail['ExamGradeChange']['registrar_approval'] != null) {
                                $this->Flash->error(
                                    'The selected grade change request is already processed. Please use the following report tool to get details on the status of the grade change request.'
                                );
                                return $this->redirect(array('action' => 'index'));
                            } else {
                                $registrar_grade_change_approval = array();
                                $registrar_grade_change_approval['id'] = $this->request->data['ExamGradeChange'][$i]['id'];
                                $registrar_grade_change_approval['registrar_approval'] = (isset($this->request->data['ExamGradeChange'][$i]['registrar_approval']) ? ($this->request->data['ExamGradeChange'][$i]['registrar_approval'] == 1 ? 1 : -1) : -1);
                                $registrar_grade_change_approval['registrar_reason'] = $this->request->data['ExamGradeChange'][$i]['registrar_reason'];
                                $registrar_grade_change_approval['registrar_approval_date'] = date('Y-m-d H:i:s');
                                $registrar_grade_change_approval['registrar_approved_by'] = $this->Auth->user('id');

                                if ($this->ExamGradeChange->save(
                                    $registrar_grade_change_approval,
                                    array('validate' => false)
                                )) {
                                    //Notifications
                                    ClassRegistry::init('AutoMessage')->sendNotificationOnRegistrarGradeChangeApproval(
                                        $registrar_grade_change_approval
                                    );
                                    if ($registrar_grade_change_approval['registrar_approval'] == 1) {
                                        //Check if status is generated or not
                                        $grade_change_detail = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->CourseRegistration->ExamGrade->ExamGradeChange->find(
                                            'first',
                                            array(
                                                'conditions' => array(
                                                    'ExamGradeChange.id' => $registrar_grade_change_approval['id']
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
                                            )
                                        );

                                        if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") {
                                            $student = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
                                            $published_course = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse'];
                                        } else {
                                            $student = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
                                            $published_course = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse'];
                                        }

                                        // regenarate all status regardless if it not regenerated within 1 week
                                        $status_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                            $student['id']
                                        );

                                        if ($status_status == 3) {
                                            // status is regenerated in last 1 week, so check if there is any changes are possible after that

                                            $previous_student_exam_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->find(
                                                'first',
                                                array(
                                                    'conditions' => array(
                                                        'StudentExamStatus.student_id' => $student['id'],
                                                        'StudentExamStatus.academic_year' => $published_course['academic_year'],
                                                        'StudentExamStatus.semester' => $published_course['semester']
                                                    ),
                                                    'recursive' => -1
                                                )
                                            );

                                            if (!empty($previous_student_exam_status)) {
                                                //Status is already generated
                                                $status_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusForGradeChange(
                                                    $registrar_grade_change_approval['id']
                                                );
                                            } else {
                                                $status_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse(
                                                    $published_course['id']
                                                );
                                            }
                                        } else {
                                            // all student status will be generated from begining by deleting all previous statuses generated if any.
                                        }

                                        if ($status_status) {
                                            $this->Flash->success(
                                                'Your exam grade change request approval is successfully done and academic status of the student is also updated.'
                                            );
                                        } else {
                                            $this->Flash->warning(
                                                'Your exam grade change request approval is successfully done but student academic status is not completed. Please regenarate student academic status.'
                                            );
                                        }
                                    } else {
                                        $this->Flash->success(
                                            'Your exam grade change request approval is successfully done.'
                                        );
                                    }

                                    if (isset($this->request->data['Mass']['ExamGradeChange']['select_all'])) {
                                        unset($this->request->data['Mass']['ExamGradeChange']['select_all']); // to make sure, even if it is redirected
                                    }

                                    return $this->redirect(array('action' => 'manage_registrar_grade_change'));
                                } else {
                                    $this->Flash->error(
                                        'The system is unable to complete your exam grade change request approval. Please try again.'
                                    );
                                    if (isset($this->request->data['Mass']['ExamGradeChange']['select_all'])) {
                                        unset($this->request->data['Mass']['ExamGradeChange']['select_all']); // to make sure, even if it is redirected
                                    }
                                    return $this->redirect(array('action' => 'manage_registrar_grade_change'));
                                }
                            }
                        }
                    }
                }
            } else {
                if (isset($this->request->data) && isset($this->request->data['ApproveAllGradeChangeByRegistrar'])) {
                    if (isset($this->request->data['Mass']['ExamGradeChange']['select_all'])) {
                        unset($this->request->data['Mass']['ExamGradeChange']['select_all']);
                    }

                    $sucessfulapproval = 0;

                    if (!empty($this->request->data['Mass']['ExamGradeChange'])) {
                        foreach ($this->request->data['Mass']['ExamGradeChange'] as $grk => $grv) {
                            if ($grv['gp'] == 1) {
                                $exam_grade_change_detail = $this->ExamGradeChange->find(
                                    'first',
                                    array(
                                        'conditions' => array('ExamGradeChange.id' => $grv['id']),
                                        'contain' => array(
                                            'ExamGrade' => array(
                                                'CourseRegistration' => array('PublishedCourse' => array('Department')),
                                                'CourseAdd' => array('PublishedCourse' => array('Department'))
                                            )
                                        )
                                    )
                                );
                                if (empty($exam_grade_change_detail)) {
                                } else {
                                    if ($exam_grade_change_detail['ExamGradeChange']['department_approval'] != 1 || ($exam_grade_change_detail['ExamGradeChange']['makeup_exam_result'] == null && $exam_grade_change_detail['ExamGradeChange']['college_approval'] != 1)) {
                                    } else {
                                        if ($exam_grade_change_detail['ExamGradeChange']['registrar_approval'] != null) {
                                        } else {
                                            $registrar_grade_change_approval = array();
                                            $registrar_grade_change_approval['id'] = $grv['id'];
                                            $registrar_grade_change_approval['registrar_approval'] = 1;
                                            $registrar_grade_change_approval['registrar_reason'] = "Teacher reason accepted!";
                                            $registrar_grade_change_approval['registrar_approval_date'] = date(
                                                'Y-m-d H:i:s'
                                            );
                                            $registrar_grade_change_approval['registrar_approved_by'] = $this->Auth->user(
                                                'id'
                                            );

                                            if ($this->ExamGradeChange->save(
                                                $registrar_grade_change_approval,
                                                array('validate' => false)
                                            )) {
                                                $sucessfulapproval++;
                                                //Notifications
                                                ClassRegistry::init(
                                                    'AutoMessage'
                                                )->sendNotificationOnRegistrarGradeChangeApproval(
                                                    $registrar_grade_change_approval
                                                );
                                                if ($registrar_grade_change_approval['registrar_approval'] == 1) {
                                                    //Check if status is generated or not
                                                    $grade_change_detail = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->CourseRegistration->ExamGrade->ExamGradeChange->find(
                                                        'first',
                                                        array(
                                                            'conditions' => array(
                                                                'ExamGradeChange.id' => $registrar_grade_change_approval['id']
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
                                                        )
                                                    );

                                                    if (isset($grade_change_detail['ExamGrade']['CourseRegistration']) && !empty($grade_change_detail['ExamGrade']['CourseRegistration']) && $grade_change_detail['ExamGrade']['CourseRegistration']['id'] != "") {
                                                        $student = $grade_change_detail['ExamGrade']['CourseRegistration']['Student'];
                                                        $published_course = $grade_change_detail['ExamGrade']['CourseRegistration']['PublishedCourse'];
                                                    } else {
                                                        $student = $grade_change_detail['ExamGrade']['CourseAdd']['Student'];
                                                        $published_course = $grade_change_detail['ExamGrade']['CourseAdd']['PublishedCourse'];
                                                    }


                                                    // regenarate all status regardless if it not regenerated within 1 week
                                                    $status_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->regenerate_all_status_of_student_by_student_id(
                                                        $student['id']
                                                    );

                                                    if ($status_status == 3) {
                                                        // status is regenerated in last 1 week, so check if there is any changes are possible after that

                                                        $previous_student_exam_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->find(
                                                            'first',
                                                            array(
                                                                'conditions' => array(
                                                                    'StudentExamStatus.student_id' => $student['id'],
                                                                    'StudentExamStatus.academic_year' => $published_course['academic_year'],
                                                                    'StudentExamStatus.semester' => $published_course['semester']
                                                                ),
                                                                'recursive' => -1
                                                            )
                                                        );

                                                        if (!empty($previous_student_exam_status)) {
                                                            //Status is already generated
                                                            $status_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusForGradeChange(
                                                                $registrar_grade_change_approval['id']
                                                            );
                                                            //debug($status_status);
                                                        } else {
                                                            $status_status = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->StudentExamStatus->updateAcdamicStatusByPublishedCourse(
                                                                $published_course['id']
                                                            );
                                                        }
                                                    } else {
                                                        // all student status will be generated from begining by deleting all previous statuses generated if any.
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($sucessfulapproval) {
                        $this->Flash->success('Your exam grade change request approval is successfully done.');
                        return $this->redirect(array('action' => 'manage_registrar_grade_change'));
                    }
                }
            }

            $this->set(
                compact('exam_grade_changes', 'makeup_exam_grade_changes', 'department_makeup_exam_grade_changes')
            );
        } else {
            $this->Flash->error(
                'NOT AUTHORIZED!! You need to have registrar role to access exam grade change management area. Please contact your system administrator if you feel this message is not right.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    public function freshman_makeup_exam_result()
    {

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
            $this->__makeup_exam_result(0);
            $this->render('makeup_exam_result');
        } else {
            $this->Flash->error(
                'You need to have a college role to access Freshman Supplemetary/Makeup exam administration. Please contact your system administrator.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    public function department_makeup_exam_result()
    {

        //Role based checking is skipped
        //if($this->role_id == 6) {
        if ($this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_DEPARTMENT /* || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE */) {
            $this->__makeup_exam_result(1);
            $this->render('makeup_exam_result');
        } else {
            $this->Flash->error(
                'You need to have department role to access Supplemetary/Makeup exam administration. Please contact your system administrator.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    //TODO: Reject grade change when there is other submitted grade which is on process (DONE)
    private function __makeup_exam_result($departement = 1)
    {

        $grade_history = array();
        $register_or_add = array();
        //Role based checking is skipped
        //if($this->role_id == 6 || $this->role_id == 5) {
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                'Auth.User'
            )['role_id'] == ROLE_COLLEGE) {
            if (!empty($this->request->data)) {
                //debug($this->request->data);
                $save_is_ok = true;

                if ($this->request->data['ExamGradeChange']['course_registration_id'] != "0") {
                    $register_or_add = explode('~', $this->request->data['ExamGradeChange']['course_registration_id']);

                    $grade_id = 0;
                    $student = array();
                    $published_course_id = 0;

                    if (strcasecmp($register_or_add[1], 'add') == 0) {
                        $grade_id = $this->ExamGradeChange->ExamGrade->find('first', array(
                            'fields' => array('ExamGrade.id'),
                            'conditions' => array(
                                'ExamGrade.course_add_id' => $register_or_add[0]
                            ),
                            'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
                            'recursive' => -1
                        ));

                        if (!empty($grade_id)) {
                            $grade_id = $grade_id['ExamGrade']['id'];
                        }

                        $published_course_id = $this->ExamGradeChange->ExamGrade->CourseAdd->field(
                            'published_course_id',
                            array('id' => $register_or_add[0])
                        );
                        $grade_history = $this->ExamGradeChange->ExamGrade->CourseAdd->getCourseAddGradeHistory(
                            $register_or_add[0]
                        );

                        $student = $this->ExamGradeChange->ExamGrade->CourseAdd->find('first', array(
                            'conditions' => array('CourseAdd.id' => $register_or_add[0]),
                            'contain' => array('Student')
                        ));

                        if (!empty($student)) {
                            $student = $student['Student'];
                        }

                        $on_progress = $this->ExamGradeChange->ExamGrade->CourseAdd->isAnyGradeOnProcess(
                            $register_or_add[0]
                        );
                    } else {
                        $grade_id = $this->ExamGradeChange->ExamGrade->find('first', array(
                            'fields' => array('ExamGrade.id'),
                            'conditions' => array(
                                'ExamGrade.course_registration_id' => $register_or_add[0]
                            ),
                            'order' => array('ExamGrade.id' => 'DESC', 'ExamGrade.created' => 'DESC'),
                            'recursive' => -1
                        ));

                        if (!empty($grade_id)) {
                            $grade_id = $grade_id['ExamGrade']['id'];
                        }

                        $published_course_id = $this->ExamGradeChange->ExamGrade->CourseRegistration->field(
                            'published_course_id',
                            array('id' => $register_or_add[0])
                        );
                        $grade_history = $this->ExamGradeChange->ExamGrade->CourseRegistration->getCourseRegistrationGradeHistory(
                            $register_or_add[0]
                        );

                        $student = $this->ExamGradeChange->ExamGrade->CourseRegistration->find('first', array(
                            'conditions' => array('CourseRegistration.id' => $register_or_add[0]),
                            'contain' => array('Student')
                        ));

                        if (!empty($student)) {
                            $student = $student['Student'];
                        }

                        $on_progress = $this->ExamGradeChange->ExamGrade->CourseRegistration->isAnyGradeOnProcess(
                            $register_or_add[0]
                        );
                    }

                    $course_title_code = '';
                    $student_name_student_number = '';

                    if (!empty($published_course_id)) {
                        $pc_crs_id = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->field(
                            'PublishedCourse.course_id',
                            array('PublishedCourse.id' => $published_course_id)
                        );
                        if (!empty($pc_crs_id)) {
                            $courseDetails = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Course->find(
                                'first',
                                array('conditions' => array('Course.id' => $pc_crs_id), 'recursive' => -1)
                            );
                            if (!empty($courseDetails)) {
                                $course_title_code = $courseDetails['Course']['course_code_title'];
                            }
                        }
                    }

                    if (!empty($student) && is_array($student)) {
                        $student_name_student_number = $student['full_name_studentnumber'];
                    }

                    if (!empty($student) && ($departement == 1 && $student['department_id'] != $this->department_id) || ($departement == 0 && $student['college_id'] != $this->college_id)) {
                        if ($departement == 1) {
                            $this->Flash->warning('You are not authorized to do that!');
                            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
                        }
                    }

                    if (empty($student)) {
                        $this->Flash->error('Invalid Student.');
                        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
                    }

                    if (empty($published_course_id)) {
                        $this->Flash->error('Invalid course is selected.');
                        return $this->redirect(array('action' => 'add'));
                    }

                    if (empty($grade_id)) {
                        $this->Flash->error(
                            'Exam grade is not yet submitted for ' . (!empty($student_name_student_number) ? $student_name_student_number : 'the selected student') . ' for ' . (!empty($course_title_code) ? $course_title_code : 'the selected course') . '. Supplemetary or Makeup Exam result can only be submitted after the instructor submitted student grade. Please communicate the assigned instructor to submit grade for the student or you can submit on behalf of him if you have the appropraite permission.'
                        );
                        $save_is_ok = false;
                    } else {
                        $status = $this->ExamGradeChange->ExamGrade->gradeCanBeChanged($grade_id);
                        if ($status === true) {
                            $exam_grade_change['ExamGradeChange']['exam_grade_id'] = $grade_id;
                            $previous_grade = $this->ExamGradeChange->ExamGrade->field('grade', array('id' => $grade_id)
                            );
                        } else {
                            $this->Flash->error(__($status));
                            $save_is_ok = false;
                        }
                    }
                } else {
                    $this->Flash->error(
                        'You are required to select the course for which ' . (!empty($student_name_student_number) ? $student_name_student_number : 'the selected student') . ' takes Supplemetary or Makeup Exam.'
                    );
                    $save_is_ok = false;
                }

                if (!isset($this->request->data['ExamGradeChange']['student_id']) || $this->request->data['ExamGradeChange']['student_id'] == "0") {
                    $this->Flash->error(
                        'You are required to select the student who takes the Supplementary or Makeup Exam.'
                    );
                    $save_is_ok = false;
                } else {
                    if ($save_is_ok && (!isset($this->request->data['ExamGradeChange']['grade']) || $this->request->data['ExamGradeChange']['grade'] == "" || $this->request->data['ExamGradeChange']['grade'] == "0")) {
                        $this->Flash->error(
                            'You are required to select exam grade for ' . (!empty($student_name_student_number) ? $student_name_student_number : 'the selected student') . ' for ' . (!empty($course_title_code) ? $course_title_code : 'the selected course') . '.'
                        );
                        $save_is_ok = false;
                    } else {
                        if ($save_is_ok && !$this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->isItValidGradeForPublishedCourse(
                                $published_course_id,
                                $this->request->data['ExamGradeChange']['grade']
                            )) {
                            $this->Flash->warning(
                                'Invalid Grade for ' . (!empty($course_title_code) ? $course_title_code : 'the selected course') . '!'
                            );
                            $save_is_ok = false;
                        } else {
                            $exam_grade_change['ExamGradeChange']['grade'] = $this->request->data['ExamGradeChange']['grade'];
                        }
                    }
                }

                if ($save_is_ok && !(isset($this->request->data['ExamGradeChange']['makeup_exam_result']) && !empty($this->request->data['ExamGradeChange']['makeup_exam_result']) && is_numeric(
                            $this->request->data['ExamGradeChange']['makeup_exam_result']
                        ) && $this->request->data['ExamGradeChange']['makeup_exam_result'] >= 0 && $this->request->data['ExamGradeChange']['makeup_exam_result'] <= 100)) {
                    $this->Flash->error('Please enter a valid exam result.');
                    $save_is_ok = false;
                } else {
                    if (!isset($this->request->data['ExamGradeChange']['grade']) || (isset($this->request->data['ExamGradeChange']['grade']) && empty($this->request->data['ExamGradeChange']['grade']))) {
                        $this->Flash->error('Please enter a valid grade.');
                        $save_is_ok = false;
                    } else {
                        if (!isset($this->request->data['ExamGradeChange']['makeup_exam_result']) || (isset($this->request->data['ExamGradeChange']['makeup_exam_result']) && empty($this->request->data['ExamGradeChange']['makeup_exam_result']))) {
                            $this->Flash->error('Please enter a valid exam result.');
                            $save_is_ok = false;
                        } else {
                            $exam_grade_change['ExamGradeChange']['makeup_exam_result'] = $this->request->data['ExamGradeChange']['makeup_exam_result'];
                        }
                    }
                }

                $exam_grade_change['ExamGradeChange']['reason'] = $this->request->data['ExamGradeChange']['reason'];

                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_COLLEGE) {
                    $exam_grade_change['ExamGradeChange']['department_reason'] = $this->request->data['ExamGradeChange']['reason'];
                    $exam_grade_change['ExamGradeChange']['initiated_by_department'] = 1;
                    $exam_grade_change['ExamGradeChange']['department_approval'] = 1;
                    $exam_grade_change['ExamGradeChange']['department_approved_by'] = $this->Auth->user('id');
                    $exam_grade_change['ExamGradeChange']['department_approval_date'] = date('Y-m-d H:i:s');

                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                        $exam_grade_change['ExamGradeChange']['college_approval'] = 1;
                        $exam_grade_change['ExamGradeChange']['college_reason'] = $this->request->data['ExamGradeChange']['reason'];
                        $exam_grade_change['ExamGradeChange']['college_approved_by'] = $this->Auth->user('id');
                        $exam_grade_change['ExamGradeChange']['college_approval_date'] = date('Y-m-d H:i:s');
                        $exam_grade_change['ExamGradeChange']['college_approval_date'] = null;
                    } else {
                        $exam_grade_change['ExamGradeChange']['college_approval'] = null;
                        $exam_grade_change['ExamGradeChange']['college_reason'] = '';
                        $exam_grade_change['ExamGradeChange']['college_approved_by'] = '';
                        $exam_grade_change['ExamGradeChange']['college_approval_date'] = null;
                    }
                } else {
                    $exam_grade_change['ExamGradeChange']['department_reason'] = '';
                    $exam_grade_change['ExamGradeChange']['initiated_by_department'] = 0;
                    $exam_grade_change['ExamGradeChange']['department_approval'] = null;
                    $exam_grade_change['ExamGradeChange']['department_approved_by'] = '';
                    $exam_grade_change['ExamGradeChange']['department_approval_date'] = null;
                    $exam_grade_change['ExamGradeChange']['college_approval'] = null;
                    $exam_grade_change['ExamGradeChange']['college_reason'] = '';
                    $exam_grade_change['ExamGradeChange']['college_approved_by'] = '';
                    $exam_grade_change['ExamGradeChange']['college_approval_date'] = null;

                    $save_is_ok = false;
                }

                $exam_grade_change['ExamGradeChange']['minute_number'] = $this->request->data['ExamGradeChange']['minute_number'];

                $exam_grade_change['ExamGradeChange']['registrar_reason'] = '';
                $exam_grade_change['ExamGradeChange']['registrar_approved_by'] = '';
                $exam_grade_change['ExamGradeChange']['registrar_approval'] = null;

                if ($save_is_ok) {
                    $this->ExamGradeChange->create();

                    if ($this->ExamGradeChange->save($exam_grade_change)) {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                                'Auth.User'
                            )['role_id'] == ROLE_COLLEGE) {
                            $this->Flash->success(
                                'The Makeup/Supplementary exam result for ' . (!empty($student_name_student_number) ? $student_name_student_number : 'the selected student') . ' for ' . (!empty($course_title_code) ? $course_title_code : 'the selected course') . ' has been saved and sent to the registrar for confimation.'
                            );
                        } else {
                            $this->Flash->success(
                                'The Makeup/Supplementary exam result for ' . (!empty($student_name_student_number) ? $student_name_student_number : 'the selected student') . ' for ' . (!empty($course_title_code) ? $course_title_code : 'the selected course') . ' has been saved and sent to deparment for approval.'
                            );
                        }

                        if (!empty($departement) || $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                            return $this->redirect(array('action' => 'department_makeup_exam_result'));
                        } else {
                            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                                return $this->redirect(array('action' => 'department_makeup_exam_result'));
                            } else {
                                return $this->redirect(array('action' => 'index'));
                            }
                        }
                    } else {
                        $this->Flash->error(
                            'The Makeup/Supplementary exam result could not be saved for ' . (!empty($student_name_student_number) ? $student_name_student_number : 'the selected student') . ' for ' . (!empty($course_title_code) ? $course_title_code : 'the selected course') . '. Please, try again.'
                        );
                    }
                }

                //redisplay
                if (!empty($departement) || $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $programsss = $programs = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find(
                        'list',
                        array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
                    );
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                        $programsss = $programs = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find(
                            'list',
                            array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                        );
                    }
                } else {
                    $programsss = $programs = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find(
                        'list',
                        array('conditions' => array('Program.active' => 1))
                    );
                }

                $program_id = $this->request->data['ExamGradeChange']['program_id'];

                if (empty($program_id) && !empty($this->program_ids)) {
                    $program_id = array_values($this->program_ids)[0];
                    if (empty($program_id)) {
                        $program_id = 1;
                    }
                }

                if (empty($program_id)) {
                    $program_id = 1;
                }

                if (!empty($departement) || $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $student_sections = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                        ($departement == 1 ? $this->department_id : $this->college_id),
                        $departement,
                        $program_id,
                        3
                    );
                } else {
                    $student_sections = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramType(
                        ($departement == 1 ? $this->department_id : $this->college_id),
                        $departement,
                        $program_id,
                        3
                    );
                }

                $student_section_id = $this->request->data['ExamGradeChange']['student_section_id'];

                if (!empty($student_section_id)) {
                    if (!empty($departement) || $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_COLLEGE) {
                        $students = $this->ExamGradeChange->possibleStudentsForSup($student_section_id);
                    } else {
                        $students = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->allStudents(
                            $student_section_id
                        );
                    }
                } else {
                    $students = array();
                }

                $student_id = $this->request->data['ExamGradeChange']['student_id'];

                //debug($students);

                if (!empty($student_id)) {
                    if (!empty($departement) || $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_DEPARTMENT || $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_COLLEGE) {
                        $student_registered_courses = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->getPossibleStudentRegisteredAndAddCoursesForSup(
                            $student_id
                        );
                    } else {
                        $student_registered_courses = $this->ExamGradeChange->ExamGrade->CourseRegistration->Student->getStudentRegisteredAndAddCourses(
                            $student_id
                        );
                    }
                } else {
                    $student_registered_courses = array();
                }

                $registered_course_id = $this->request->data['ExamGradeChange']['course_registration_id'];

                if (isset($published_course_id) && !empty($published_course_id)) {
                    $exam_grades = $this->ExamGradeChange->ExamGrade->CourseRegistration->getPublishedCourseGradeScaleList(
                        $published_course_id
                    );
                } else {
                    $exam_grades = array();
                }

                if (!empty($exam_grades)) {
                    $exam_grades = array('0' => '[ Select Grade ]') + $exam_grades;
                } else {
                    $exam_grades = array('0' => '[ No Grade Scale Found ]');
                }

                //$exam_grades = $exam_grades + array('NG' => 'NG');
                //debug($exam_grades);

                if (isset($this->request->data['ExamGradeChange']['grade']) && !empty($this->request->data['ExamGradeChange']['grade'])) {
                    $grade = $this->request->data['ExamGradeChange']['grade'];
                } else {
                    $grade = '';
                }
            } else {
                if (!empty($departement) || $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $programsss = $programs = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find(
                        'list',
                        array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
                    );
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                        $programsss = $programs = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find(
                            'list',
                            array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                        );
                    }
                } else {
                    $programsss = $programs = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->Program->find(
                        'list',
                        array('conditions' => array('Program.active' => 1))
                    );
                }

                $program_id = 1;

                if (isset($this->request->data['ExamGradeChange']['program_id']) && !empty($this->request->data['ExamGradeChange']['program_id'])) {
                    $program_id = $this->request->data['ExamGradeChange']['program_id'];
                } else {
                    if (!empty($this->program_ids)) {
                        $program_id = array_values($this->program_ids)[0];
                        if (empty($program_id)) {
                            $program_id = 1;
                        }
                    }
                }

                if (!empty($departement) || $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $student_sections = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                        ($departement == 1 ? $this->department_id : $this->college_id),
                        $departement,
                        $program_id,
                        3
                    );
                } else {
                    $student_sections = $this->ExamGradeChange->ExamGrade->CourseRegistration->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramType(
                        ($departement == 1 ? $this->department_id : $this->college_id),
                        $departement,
                        $program_id,
                        3
                    );
                }

                $student_section_id = 0;

                $students = array();
                $student_id = 0;

                $student_registered_courses = array();
                $registered_course_id = 0;

                $exam_grades = array('0' => '[ No Grade Scale Found ]');
                $grade = '';

                $grade_history = '';
            }

            if (!empty($student_sections)) {
                $student_sections = array('0' => '[ Select Section ]') + $student_sections;
            } else {
                $student_sections = array('0' => '[ No Section Found ]');
            }

            if (isset($students) && !empty($students)) {
                $students = array('0' => '[ Select Student ]') + $students;
            } else {
                $students = array('0' => '[ No Student Found ]');
            }

            if (isset($student_registered_courses) && !empty($student_registered_courses)) {
                $student_registered_courses = array('0' => '[ Select Course ]') + $student_registered_courses;
            } else {
                $student_registered_courses = array('0' => '[ No Course Found ]');
            }

            $this->set(
                compact(
                    'student_sections',
                    'student_section_id',
                    'programs',
                    'programsss',
                    'program_id',
                    'students',
                    'student_id',
                    'student_registered_courses',
                    'registered_course_id',
                    'exam_grades',
                    'grade',
                    'grade_history',
                    'register_or_add',
                    'save_is_ok'
                )
            );
        } else {
            $this->Flash->error(
                'You need to have either college or department role to access Supplmetary/Makeup exam administration. Please contact your system administrator.'
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    public function delete($id = null)
    {

        if (!$id || !$this->ExamGradeChange->exists($id)) {
            $this->Flash->error(__('Invalid id for exam grade change deletion.'));
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->ExamGradeChange->canItBeDeleted($id)) {
            if ($this->ExamGradeChange->delete($id)) {
                $this->Flash->success('Exam grade change is deleted');
                return $this->redirect(array('action' => 'index'));
            }

            $this->Flash->error('Exam grade change was not deleted');
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Flash->error(
                'Exam grade change is either submitted by the instructor or already on approval process to delete.'
            );
            return $this->redirect(array('action' => 'index'));
        }
    }
}
