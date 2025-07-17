<?php
namespace App\Controller;


use App\Controller\AppController;

use Cake\Event\EventInterface;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use App\Controller\Component\AcademicYearComponent;

class ExamGradeChangesController extends AppController
{
    /**
     * @var AcademicYearComponent
     */
    protected $AcademicYear;

    protected $menuOptions = [
        'parent' => 'examGrades',
        'controllerButton' => false,
        'exclude' => ['*'],
        'alias' => [
            'manageDepartmentGradeChange' => 'Approve Grade Change',
            'manageCollegeGradeChange' => 'Manage Grade Change',
            'manageRegistrarGradeChange' => 'Manage Grade Change',
            'departmentMakeupExamResult' => 'Supplementary Exam',
            'freshmanMakeupExamResult' => 'Freshman Supplementary Exam',
            'manageFreshmanGradeChange' => 'Manage Freshman Grade Change',
            'cancelAutoGradeChange' => 'Cancel Auto Grade Change'
        ]
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
        $this->AcademicYear = $this->loadComponent('AcademicYear');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // No Auth->allow needed as per original commented-out code
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $defaultAcademicYear = $this->AcademicYear->currentAcademicYear();
        $currAcYearExploded = explode('/', $defaultAcademicYear);
        $previousAcademicYear = $defaultAcademicYear;

        if (!empty($currAcYearExploded)) {
            $previousAcademicYear = ($currAcYearExploded[0] - 1) . '/' . ($currAcYearExploded[1] - 1);
        }

        // debug($previousAcademicYear);

        $acYearArrayData = $this->AcademicYear->academicYearInArray(
            APPLICATION_START_YEAR,
            (int)explode('/', $defaultAcademicYear)[0]
        );

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');

        $programs = $programsTable->find('list', [
            'conditions' => ['Programs.active' => 1],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $programTypes = $programTypesTable->find('list', [
            'conditions' => ['ProgramTypes.active' => 1],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $deptsForYearLevel = $departmentsTable->find('list', [
            'conditions' => ['Departments.active' => 1],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $yearLevels = $yearLevelsTable->distinctYearLevelBasedOnRole(
            null,
            null,
            array_keys($deptsForYearLevel),
            array_keys($programs)
        );

        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        $departmentId = $this->request->getSession()->read('Auth.User.department_id');
        $programIds = $this->request->getSession()->read('Auth.User.program_ids');
        $programTypeIds = $this->request->getSession()->read('Auth.User.program_type_ids');
        $isAdmin = $this->request->getSession()->read('Auth.User.is_admin');

        if ($roleId == ROLE_DEPARTMENT) {
            // Year levels already filtered by distinctYearLevelBasedOnRole
        }

        if (($roleId == ROLE_REGISTRAR || $roleId == ROLE_COLLEGE) && $isAdmin == 0) {
            $programs = $programsTable->find('list', [
                'conditions' => ['Programs.id IN' => $programIds, 'Programs.active' => 1],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();

            $programTypes = $programTypesTable->find('list', [
                'conditions' => ['ProgramTypes.id IN' => $programTypeIds, 'ProgramTypes.active' => 1],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        }

        $yearsToLookListForDisplay = $this->AcademicYear->academicYearInArray(
            ((int)explode('/', $defaultAcademicYear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL,
            (int)explode('/', $defaultAcademicYear)[0]
        );

        if (count($yearsToLookListForDisplay) >= 2) {
            $startYr = array_pop($yearsToLookListForDisplay);
            $endYr = reset($yearsToLookListForDisplay);
            $yearsToLookListForDisplay = 'from ' . $startYr . ' up to ' . $endYr;
        } elseif (count($yearsToLookListForDisplay) == 1) {
            $yearsToLookListForDisplay = ' on ' . $defaultAcademicYear;
        } else {
            $yearsToLookListForDisplay = '';
        }

        // debug($yearsToLookListForDisplay);

        $this->set(compact(
            'acYearArrayData',
            'programTypes',
            'defaultAcademicYear',
            'previousAcademicYear',
            'programs',
            'yearLevels',
            'yearsToLookListForDisplay'
        ));

        if (isset($this->request->getData()['User']['password'])) {
            $requestData = $this->request->getData();
            unset($requestData['User']['password']);
            $this->request = $this->request->withData($requestData);
        }
    }

    public function index()
    {
        if ($this->Acl->check($this->Auth->user(), 'controllers/ExamGrades/freshman_grade_view')) {
            return $this->redirect(['controller' => 'ExamGrades', 'action' => 'freshman_grade_view']);
        }
        return $this->redirect(['controller' => 'ExamGrades', 'action' => 'department_grade_view']);
    }

    public function manageFreshmanGradeChange()
    {
        $this->manageGradeChange(0);
        $this->render('manage_department_grade_change');
    }

    public function manageDepartmentGradeChange()
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE) {
            $this->manageGradeChange(1);
            $this->render('manage_department_grade_change');
        } else {
            $this->Flash->error(__('You need to have department role to access exam grade change management area. Please contact your system administrator to get department role.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    protected function manageGradeChange($department = 1)
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE) {
            $departmentId = $this->department_id;
            $collegeId = $this->college_id;
            $departmentIds = $this->department_ids ?: [$departmentId];

            $colOrDptId = $department ? $departmentId : $collegeId;

            $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
            $requestData = $this->request->getData();
            if (!empty($requestData) && !isset($requestData['ApproveAllGradeChangeByDepartment'])) {
                if (isset($requestData['ExamGradeChange']['grade_change_count']) && $requestData['ExamGradeChange']['grade_change_count'] != 0) {
                    $approvalsCount = 0;
                    $rejectionsCount = 0;
                    $rejectedRejections = 0;
                    $acceptedRejections = 0;

                    for ($i = 1; $i <= $requestData['ExamGradeChange']['grade_change_count']; $i++) {
                        if (isset($requestData['approveGradeChangeByDepartment_' . $i])) {
                            $examGradeChangeDetail = $examGradeChangesTable->find()
                                ->where(['ExamGradeChanges.id' => $requestData['ExamGradeChange'][$i]['id']])
                                ->contain([
                                    'ExamGrades' => [
                                        'CourseRegistrations' => ['PublishedCourses'],
                                        'CourseAdds' => ['PublishedCourses']
                                    ]
                                ])
                                ->first();

                            if (empty($examGradeChangeDetail)) {
                                $this->Flash->error(__('The system unable to find the exam grade change request. It happens when the grade change request is canceled in the middle of approval process. Please try again.'));
                            } elseif (
                                (
                                    !empty($examGradeChangeDetail->exam_grade->course_registration) &&
                                    $examGradeChangeDetail->exam_grade->course_registration->id != "" &&
                                    (
                                        ($department == 1 && $colOrDptId != $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department_id) ||
                                        ($department == 0 && !in_array($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department_id, $departmentIds))
                                    )
                                ) ||
                                (
                                    !empty($examGradeChangeDetail->exam_grade->course_add) &&
                                    $examGradeChangeDetail->exam_grade->course_add->id != "" &&
                                    (
                                        ($department == 1 && $colOrDptId != $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department_id) ||
                                        ($department == 0 && !in_array($examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department_id, $departmentIds))
                                    )
                                )
                            ) {
                                // debug($examGradeChangeDetail);
                                $this->Flash->error(__('You are not authorized to manage the selected exam grade change request.'));
                            } elseif ($examGradeChangeDetail->department_approval == 1 && ($examGradeChangeDetail->registrar_approval == 1 || $examGradeChangeDetail->registrar_approval === null)) {
                                $this->Flash->error(__('The selected grade change request is already processed. Please use the following report tool to get details on the status of the grade change request.'));
                                return $this->redirect(['action' => 'index']);
                            } else {
                                $departmentGradeChangeApproval = [];

                                if ($examGradeChangeDetail->department_approval === null) {
                                    $departmentGradeChangeApproval = [
                                        'id' => $requestData['ExamGradeChange'][$i]['id'],
                                        'department_approval' => isset($requestData['ExamGradeChange'][$i]['department_approval']) ? ($requestData['ExamGradeChange'][$i]['department_approval'] == 1 ? 1 : -1) : -1,
                                        'department_reason' => trim($requestData['ExamGradeChange'][$i]['department_reason'] ?? ''),
                                        'department_approval_date' => date('Y-m-d H:i:s'),
                                        'department_approved_by' => $this->Auth->user('id')
                                    ];

                                    if ($departmentGradeChangeApproval['department_approval'] == 1) {
                                        $approvalsCount++;
                                    } elseif ($departmentGradeChangeApproval['department_approval'] == -1) {
                                        $rejectionsCount++;
                                    }
                                } else {
                                    if (!empty($examGradeChangeDetail->grade)) {
                                        $departmentGradeChangeApproval = [
                                            'exam_grade_id' => $examGradeChangeDetail->exam_grade_id,
                                            'grade' => $examGradeChangeDetail->grade,
                                            'minute_number' => !empty($examGradeChangeDetail->minute_number) ? trim($examGradeChangeDetail->minute_number) : '',
                                            'makeup_exam_id' => !empty($examGradeChangeDetail->makeup_exam_id) ? $examGradeChangeDetail->makeup_exam_id : null,
                                            'makeup_exam_result' => isset($examGradeChangeDetail->makeup_exam_result) ? $examGradeChangeDetail->makeup_exam_result : null,
                                            'initiated_by_department' => !empty($examGradeChangeDetail->initiated_by_department) ? $examGradeChangeDetail->initiated_by_department : 0,
                                            'result' => !empty($examGradeChangeDetail->result) ? $examGradeChangeDetail->result : null,
                                            'department_reply' => 1,
                                            'department_approval' => isset($requestData['ExamGradeChange'][$i]['department_approval']) ? ($requestData['ExamGradeChange'][$i]['department_approval'] == 1 ? 1 : -1) : -1,
                                            'department_reason' => !empty($requestData['ExamGradeChange'][$i]['department_reason']) ? trim($requestData['ExamGradeChange'][$i]['department_reason']) : '',
                                            'department_approval_date' => date('Y-m-d H:i:s'),
                                            'department_approved_by' => $this->Auth->user('id')
                                        ];

                                        $departmentGradeChangeApproval['reason'] = !empty($departmentGradeChangeApproval['department_reason']) ? $departmentGradeChangeApproval['department_reason'] :
                                            (!empty($examGradeChangeDetail->reason) ? $examGradeChangeDetail->reason : '');

                                        if ($departmentGradeChangeApproval['department_approval'] == 1) {
                                            $approvalsCount++;
                                            $acceptedRejections++;
                                        } elseif ($departmentGradeChangeApproval['department_approval'] == -1) {
                                            $rejectionsCount++;
                                            $rejectedRejections++;
                                        }

                                        if ($roleId == ROLE_COLLEGE) {
                                            $departmentGradeChangeApproval['college_reason'] = !empty($departmentGradeChangeApproval['department_reason']) ? $departmentGradeChangeApproval['department_reason'] :
                                                (!empty($examGradeChangeDetail->reason) ? $examGradeChangeDetail->reason : '');
                                            $departmentGradeChangeApproval['college_approval'] = $departmentGradeChangeApproval['department_approval'];
                                            $departmentGradeChangeApproval['college_approval_date'] = $departmentGradeChangeApproval['department_approval_date'];
                                            $departmentGradeChangeApproval['college_approved_by'] = $departmentGradeChangeApproval['department_approved_by'];
                                        } else {
                                            $departmentGradeChangeApproval['college_reason'] = '';
                                            $departmentGradeChangeApproval['college_approval'] = null;
                                            $departmentGradeChangeApproval['college_approval_date'] = null;
                                            $departmentGradeChangeApproval['college_approved_by'] = '';
                                        }

                                        $departmentGradeChangeApproval['registrar_reason'] = '';
                                        $departmentGradeChangeApproval['registrar_approval'] = null;
                                        $departmentGradeChangeApproval['registrar_approval_date'] = null;
                                        $departmentGradeChangeApproval['registrar_approved_by'] = '';
                                    }
                                }

                                if (!empty($departmentGradeChangeApproval)) {
                                    $entity = $examGradeChangesTable->newEntity($departmentGradeChangeApproval);
                                    if ($examGradeChangesTable->save($entity, ['validate' => false])) {
                                        $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
                                        $autoMessagesTable->sendNotificationOnDepartmentGradeChangeApproval($departmentGradeChangeApproval);
                                        if ($rejectionsCount == 0 && $rejectedRejections == 0) {
                                            $this->Flash->success(__('Your exam grade change request approval was successful.'));
                                        } elseif ($rejectionsCount != 0) {
                                            $this->Flash->success(__('Your exam grade change request approval/rejection was successful.'));
                                        } elseif ($rejectedRejections != 0) {
                                            $this->Flash->success(__('Your exam grade change request approval/rejecting rejections was successful.'));
                                        }
                                        return $this->redirect(['action' => $department == 1 ? 'manageDepartmentGradeChange' : 'manageFreshmanGradeChange']);
                                    } else {
                                        $this->Flash->error(__('The system is unable to complete your exam grade change request approval. Please try again.'));
                                        return $this->redirect(['action' => $department == 1 ? 'manageDepartmentGradeChange' : 'manageFreshmanGradeChange']);
                                    }
                                } else {
                                    $this->Flash->error(__('The system is unable to complete your exam grade change request approval. Please try again.'));
                                    return $this->redirect(['action' => $department == 1 ? 'manageDepartmentGradeChange' : 'manageFreshmanGradeChange']);
                                }
                            }
                        }
                    }
                } elseif (isset($requestData['ApproveAllGradeChangeByDepartment'])) {
                    if (isset($requestData['Mass']['ExamGradeChange']['select_all'])) {
                        unset($requestData['Mass']['ExamGradeChange']['select_all']);
                    }

                    $successfulApproval = 0;
                    if (!empty($requestData['Mass']['ExamGradeChange'])) {
                        foreach ($requestData['Mass']['ExamGradeChange'] as $grk => $grv) {
                            if ($grv['gp'] == 1) {
                                $examGradeChangeDetail = $examGradeChangesTable->find()
                                    ->where(['ExamGradeChanges.id' => $grv['id']])
                                    ->contain([
                                        'ExamGrades' => [
                                            'CourseRegistrations' => ['PublishedCourses'],
                                            'CourseAdds' => ['PublishedCourses']
                                        ]
                                    ])
                                    ->first();

                                if (empty($examGradeChangeDetail)) {
                                    continue;
                                } elseif (
                                    (
                                        !empty($examGradeChangeDetail->exam_grade->course_registration) &&
                                        $examGradeChangeDetail->exam_grade->course_registration->id != "" &&
                                        (
                                            ($department == 1 && $colOrDptId != $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department_id) ||
                                            ($department == 0 && !in_array($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department_id, $departmentIds))
                                        )
                                    ) ||
                                    (
                                        !empty($examGradeChangeDetail->exam_grade->course_add) &&
                                        $examGradeChangeDetail->exam_grade->course_add->id != "" &&
                                        (
                                            ($department == 1 && $colOrDptId != $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department_id) ||
                                            ($department == 0 && !in_array($examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department_id, $departmentIds))
                                        )
                                    )
                                ) {
                                    continue;
                                } elseif ($examGradeChangeDetail->department_approval == 1 && ($examGradeChangeDetail->registrar_approval == 1 || $examGradeChangeDetail->registrar_approval === null)) {
                                    continue;
                                } else {
                                    $departmentGradeChangeApproval = [
                                        'id' => $grv['id'],
                                        'department_approval' => $grv['department_approval'] ?? 1,
                                        'department_reason' => 'Teacher reason accepted!',
                                        'department_approval_date' => date('Y-m-d H:i:s'),
                                        'department_approved_by' => $this->Auth->user('id')
                                    ];

                                    $entity = $examGradeChangesTable->newEntity($departmentGradeChangeApproval);
                                    if ($examGradeChangesTable->save($entity, ['validate' => false])) {
                                        $successfulApproval++;
                                        $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
                                        $autoMessagesTable->sendNotificationOnDepartmentGradeChangeApproval($departmentGradeChangeApproval);
                                    }
                                }
                            }
                        }
                    }

                    if ($successfulApproval) {
                        $this->Flash->success(__('You have approved the selected exam grade change requests successfully.'));
                        return $this->redirect(['action' => $department == 1 ? 'manageDepartmentGradeChange' : 'manageFreshmanGradeChange']);
                    }
                }
            }

            $examGradeChanges = $examGradeChangesTable->getListOfGradeChangeForDepartmentApproval($colOrDptId, $department, $departmentIds);
            $makeupExamGradeChanges = $examGradeChangesTable->getListOfMakeupGradeChangeForDepartmentApproval($colOrDptId, 0, $department, $departmentIds);
            $rejectedMakeupExamGradeChanges = $examGradeChangesTable->getListOfMakeupGradeChangeForDepartmentApproval($colOrDptId, 1, $department, $departmentIds);
            $rejectedDepartmentMakeupExamGradeChanges = $examGradeChangesTable->getMakeupGradesAskedByDepartmentRejectedByRegistrar($colOrDptId, $department, $departmentIds);


            $this->set(compact('examGradeChanges', 'makeupExamGradeChanges', 'rejectedMakeupExamGradeChanges', 'rejectedDepartmentMakeupExamGradeChanges'));
        } else {
            $this->Flash->error(__('NOT AUTHORIZED! You need to have either college or department role to access exam grade change management area. Please contact your system administrator if you feel this message is not right.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function manageCollegeGradeChange()
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_COLLEGE) {
            $collegeId = $this->request->getSession()->read('Auth.User.college_id');
            $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
            $departmentsTable= TableRegistry::getTableLocator()->get('Departments');
            $examGradeChanges = $examGradeChangesTable->getListOfGradeChangeForCollegeApproval($collegeId);

            $requestData = $this->request->getData();
            if (!empty($requestData) && !isset($requestData['ApproveAllGradeChangeByCollege'])) {
                if (isset($requestData['ExamGradeChange']['grade_change_count']) && $requestData['ExamGradeChange']['grade_change_count'] != 0) {
                    for ($i = 1; $i <= $requestData['ExamGradeChange']['grade_change_count']; $i++) {
                        if (isset($requestData['approveGradeChangeByCollege_' . $i])) {
                            $examGradeChangeDetail = $examGradeChangesTable->find()
                                ->where(['ExamGradeChanges.id' => $requestData['ExamGradeChange'][$i]['id']])
                                ->contain([
                                    'ExamGrades' => [
                                        'CourseRegistrations' => ['PublishedCourses' => ['Departments', 'GivenByDepartments']],
                                        'CourseAdds' => ['PublishedCourses' => ['Departments', 'GivenByDepartments']]
                                    ]
                                ])
                                ->first();

                            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                            $givenByDeptIds = $departmentsTable->find('list', [
                                'conditions' => [
                                    'Departments.college_id' => $examGradeChangeDetail->exam_grade->course_registration
                                        ? $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id
                                        : $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id
                                ],
                                'keyField' => 'id',
                                'valueField' => 'id'
                            ])->toArray();

                            if (empty($examGradeChangeDetail)) {
                                $this->Flash->error(__('The system unable to find the exam grade change request. It happens when the grade change request is cancelled in the middle of the approval process. Please try again.'));
                            } elseif (
                                (
                                    !empty($examGradeChangeDetail->exam_grade->course_registration) &&
                                    $examGradeChangeDetail->exam_grade->course_registration->id != "" &&
                                    (
                                        (!empty($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department) &&
                                            $collegeId != $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id) ||
                                        (!empty($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id) &&
                                            $collegeId != $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id ||
                                            !in_array($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department_id, $givenByDeptIds))
                                    )
                                ) ||
                                (
                                    !empty($examGradeChangeDetail->exam_grade->course_add) &&
                                    $examGradeChangeDetail->exam_grade->course_add->id != "" &&
                                    (
                                        (!empty($examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department) &&
                                            $collegeId != $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id) ||
                                        (!empty($examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id) &&
                                            $collegeId != $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id)
                                    )
                                )
                            ) {
                                // debug($examGradeChangeDetail);
                                $this->Flash->error(__('You are not authorized to manage the selected exam grade change request.'));
                            } elseif ($examGradeChangeDetail->department_approval != 1) {
                                $this->Flash->error(__('The selected grade change request is being processed by the department. Please try again later.'));
                                return $this->redirect(['action' => 'index']);
                            } elseif ($examGradeChangeDetail->college_approval !== null) {
                                $this->Flash->error(__('The selected grade change request is already processed. Please use the following report tool to get details on the status of the grade change request.'));
                                return $this->redirect(['action' => 'index']);
                            } else {
                                $collegeGradeChangeApproval = [
                                    'id' => $requestData['ExamGradeChange'][$i]['id'],
                                    'college_approval' => isset($requestData['ExamGradeChange'][$i]['college_approval']) ? ($requestData['ExamGradeChange'][$i]['college_approval'] == 1 ? 1 : -1) : -1,
                                    'college_reason' => $requestData['ExamGradeChange'][$i]['college_reason'] ?? '',
                                    'college_approval_date' => date('Y-m-d H:i:s'),
                                    'college_approved_by' => $this->Auth->user('id')
                                ];

                                $entity = $examGradeChangesTable->newEntity($collegeGradeChangeApproval);
                                if ($examGradeChangesTable->save($entity, ['validate' => false])) {
                                    $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
                                    $autoMessagesTable->sendNotificationOnCollegeGradeChangeApproval($collegeGradeChangeApproval);
                                    $this->Flash->success(__('Your exam grade change request approval is successfully done.'));
                                    return $this->redirect(['action' => 'manageCollegeGradeChange']);
                                } else {
                                    $this->Flash->error(__('The system is unable to complete your exam grade change request approval. Please try again.'));
                                    return $this->redirect(['action' => 'manageCollegeGradeChange']);
                                }
                            }
                        }
                    }
                } elseif (isset($requestData['ApproveAllGradeChangeByCollege'])) {
                    if (isset($requestData['Mass']['ExamGradeChange']['select_all'])) {
                        unset($requestData['Mass']['ExamGradeChange']['select_all']);
                    }

                    $successfulApproval = 0;
                    if (!empty($requestData['Mass']['ExamGradeChange'])) {
                        foreach ($requestData['Mass']['ExamGradeChange'] as $grk => $grv) {
                            if ($grv['gp'] == 1) {
                                $examGradeChangeDetail = $examGradeChangesTable->find()
                                    ->where(['ExamGradeChanges.id' => $grv['id']])
                                    ->contain([
                                        'ExamGrades' => [
                                            'CourseRegistrations' => ['PublishedCourses' => ['Departments', 'GivenByDepartments']],
                                            'CourseAdds' => ['PublishedCourses' => ['Departments', 'GivenByDepartments']]
                                        ]
                                    ])
                                    ->first();

                                $givenByDeptIds = $departmentsTable->find('list', [
                                    'conditions' => [
                                        'Departments.college_id' => $examGradeChangeDetail->exam_grade->course_registration
                                            ? $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id
                                            : $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id
                                    ],
                                    'keyField' => 'id',
                                    'valueField' => 'id'
                                ])->toArray();

                                if (empty($examGradeChangeDetail)) {
                                    continue;
                                } elseif (
                                    (
                                        !empty($examGradeChangeDetail->exam_grade->course_registration) &&
                                        $examGradeChangeDetail->exam_grade->course_registration->id != "" &&
                                        (
                                            (!empty($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department) &&
                                                $collegeId != $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id) ||
                                            (!empty($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id) &&
                                                $collegeId != $examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department->college_id ||
                                                !in_array($examGradeChangeDetail->exam_grade->course_registration->published_course->given_by_department_id, $givenByDeptIds))
                                        )
                                    ) ||
                                    (
                                        !empty($examGradeChangeDetail->exam_grade->course_add) &&
                                        $examGradeChangeDetail->exam_grade->course_add->id != "" &&
                                        (
                                            (!empty($examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department) &&
                                                $collegeId != $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id) ||
                                            (!empty($examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id) &&
                                                $collegeId != $examGradeChangeDetail->exam_grade->course_add->published_course->given_by_department->college_id)
                                        )
                                    )
                                ) {
                                    continue;
                                } elseif ($examGradeChangeDetail->department_approval != 1) {
                                    continue;
                                } elseif ($examGradeChangeDetail->college_approval !== null) {
                                    continue;
                                } else {
                                    $collegeGradeChangeApproval = [
                                        'id' => $grv['id'],
                                        'college_approval' => 1,
                                        'college_reason' => 'Teacher reason accepted!',
                                        'college_approval_date' => date('Y-m-d H:i:s'),
                                        'college_approved_by' => $this->Auth->user('id')
                                    ];

                                    $entity = $examGradeChangesTable->newEntity($collegeGradeChangeApproval);
                                    if ($examGradeChangesTable->save($entity, ['validate' => false])) {
                                        $successfulApproval++;
                                        $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
                                        $autoMessagesTable->sendNotificationOnCollegeGradeChangeApproval($collegeGradeChangeApproval);
                                    }
                                }
                            }
                        }
                    }

                    if ($successfulApproval) {
                        $this->Flash->success(__('You have approved the selected exam grade change requests successfully.'));
                        return $this->redirect(['action' => 'manageCollegeGradeChange']);
                    }
                }
            }

            $this->set(compact('examGradeChanges'));
        } else {
            $this->Flash->error(__('NOT AUTHORIZED! You need to have college role to access exam grade change management area. Please contact your system administrator if you feel this message is not right.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function cancelAutoGradeChange()
    {
        $requestData = $this->request->getData();
        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');

        if (!empty($requestData) && !empty($requestData['cancelAutoGrade'])) {
            $gradeToBeCancelled = [];
            foreach ($requestData['ExamGradeChange'] as $key => $student) {
                if (is_numeric($key) && $student['gp'] == 1) {
                    $gradeToBeCancelled[] = $student['id'];
                }
            }

            if (!empty($gradeToBeCancelled)) {
                if ($examGradeChangesTable->deleteAll(['ExamGradeChanges.id IN' => $gradeToBeCancelled], false)) {
                    $this->Flash->success(__('You have cancelled {0} auto converted grades.', count($gradeToBeCancelled)));
                }
            }
        }

        if (!empty($requestData) && !empty($requestData['listPublishedCourses'])) {
            $type = !empty($this->request->getSession()->read('Auth.User.college_ids')) ? 1 : 0;

            // debug($requestData);

            $examGradeChanges = $examGradeChangesTable->getListOfGradeAutomaticallyConverted(
                $requestData['ExamGradeChange']['acadamic_year'] ?? '',
                $requestData['ExamGradeChange']['semester'] ?? '',
                $requestData['ExamGradeChange']['department_id'] ?? '',
                $requestData['ExamGradeChange']['program_id'] ?? '',
                $requestData['ExamGradeChange']['program_type_id'] ?? '',
                $requestData['ExamGradeChange']['grade'] ?? '',
                $type
            );

            // debug($examGradeChanges);
            $this->set(compact('examGradeChanges'));
        }

        $departments = [];
        if (!empty($this->request->getSession()->read('Auth.User.college_ids'))) {
            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
            $departments = $collegesTable->find('list', [
                'conditions' => ['Colleges.id IN' => $this->request->getSession()->read('Auth.User.college_ids'), 'Colleges.active' => 1],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        } elseif (!empty($this->request->getSession()->read('Auth.User.department_ids'))) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $departments = $departmentsTable->find('list', [
                'conditions' => ['Departments.id IN' => $this->request->getSession()->read('Auth.User.department_ids'), 'Departments.active' => 1],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        }

        $this->set(compact('departments'));
    }

    public function manageRegistrarGradeChange()
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_REGISTRAR) {
            $departmentIds = $this->department_ids ?: [];
            $collegeIds = $this->college_ids ?: [];
            $programIds = $this->program_ids ?: [];
            $programTypeIds = $this->program_type_ids ?: [];
            $requestData = $this->request->getData();
            $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');

            if (!empty($requestData) && !isset($requestData['ApproveAllGradeChangeByRegistrar'])) {

                if (isset($requestData['grade_change_count']) && $requestData['grade_change_count'] != 0) {

                    for ($i = 1; $i <= $requestData['grade_change_count']; $i++) {
                        if (!empty($requestData['approveGradeChangeByRegistrar_' . $i])) {

                            $examGradeChangeDetail = $examGradeChangesTable->find()
                                ->where(['ExamGradeChanges.id' => $requestData['ExamGradeChange'][$i]['id']])
                                ->contain([
                                    'ExamGrades' => [
                                        'CourseRegistrations' => ['PublishedCourses' => ['Departments']],
                                        'CourseAdds' => ['PublishedCourses' => ['Departments']]
                                    ]
                                ])
                                ->first();

                            if (empty($examGradeChangeDetail)) {
                                $this->Flash->error(__('The system unable to find the exam grade change request. It happens when the grade change request is canceled in the middle of the approval process. Please try again.'));
                            } elseif ($examGradeChangeDetail->department_approval != 1 || ($examGradeChangeDetail->makeup_exam_result === null && $examGradeChangeDetail->college_approval != 1)) {
                                $this->Flash->error(__('The selected grade change request is being processed by the department and/or college. Please try again later.'));
                                return $this->redirect(['action' => 'index']);
                            } elseif ($examGradeChangeDetail->registrar_approval !== null) {
                                $this->Flash->error(__('The selected grade change request is already processed. Please use the following report tool to get details on the status of the grade change request.'));
                                return $this->redirect(['action' => 'index']);
                            } else {


                                $registrarGradeChangeApproval = [
                                    'id' => $requestData['ExamGradeChange'][$i]['id'],
                                    'registrar_approval' => isset($requestData['ExamGradeChange'][$i]['registrar_approval']) ?
                                        ($requestData['ExamGradeChange'][$i]['registrar_approval'] == 1 ? 1 : -1) : -1,
                                    'registrar_reason' => $requestData['ExamGradeChange'][$i]['registrar_reason'] ?? '',
                                    'registrar_approval_date' => (new Time())->setTimezone('UTC')->format('Y-m-d H:i:s'),
                                    'registrar_approved_by' => $this->Auth->user('id')
                                ];


                                // Fetch the existing record
                                $existingEntity = $examGradeChangesTable->get($requestData['ExamGradeChange'][$i]['id'], ['contain' => []]);

                                // Update only the specified fields
                                $entity = $examGradeChangesTable->patchEntity(
                                    $existingEntity,
                                    $registrarGradeChangeApproval,
                                );


                               // $entity = $examGradeChangesTable->newEntity($registrarGradeChangeApproval);
                                if ($examGradeChangesTable->save($entity)) {

                                    $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
                                    $autoMessagesTable->sendNotificationOnRegistrarGradeChangeApproval($registrarGradeChangeApproval);

                                    if ($registrarGradeChangeApproval['registrar_approval'] == 1) {
                                        $gradeChangeDetail = $examGradeChangesTable->find()
                                            ->where(['ExamGradeChanges.id' => $registrarGradeChangeApproval['id']])
                                            ->contain([
                                                'ExamGrades' => [
                                                    'CourseRegistrations' => ['PublishedCourses', 'Students'],
                                                    'CourseAdds' => ['PublishedCourses', 'Students']
                                                ]
                                            ])
                                            ->first();

                                        $student = $gradeChangeDetail->exam_grade->course_registration
                                            ? $gradeChangeDetail->exam_grade->course_registration->student
                                            : $gradeChangeDetail->exam_grade->course_add->student;
                                        $publishedCourse = $gradeChangeDetail->exam_grade->course_registration
                                            ? $gradeChangeDetail->exam_grade->course_registration->published_course
                                            : $gradeChangeDetail->exam_grade->course_add->published_course;

                                        $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                                        $statusStatus = $studentExamStatusTable->regenerateAllStatusOfStudentByStudentId($student->id);

                                        if ($statusStatus == 3) {
                                            $previousStudentExamStatus = $studentExamStatusTable->find()
                                                ->where([
                                                    'StudentExamStatuses.student_id' => $student->id,
                                                    'StudentExamStatuses.academic_year' => $publishedCourse->academic_year,
                                                    'StudentExamStatuses.semester' => $publishedCourse->semester
                                                ])
                                                ->first();

                                            if (!empty($previousStudentExamStatus)) {
                                                $statusStatus = $studentExamStatusTable->updateAcademicStatusForGradeChange($registrarGradeChangeApproval['id']);
                                            } else {
                                                $statusStatus = $studentExamStatusTable->updateAcademicStatusByPublishedCourse($publishedCourse->id);
                                            }
                                        }

                                        if ($statusStatus) {
                                            $this->Flash->success(__('Your exam grade change request approval is successfully done and academic status of the student is also updated.'));
                                        } else {
                                            $this->Flash->warning(__('Your exam grade change request approval is successfully done but student academic status is not completed. Please regenerate student academic status.'));
                                        }
                                    } else {
                                        $this->Flash->success(__('Your exam grade change request approval is successfully done.'));
                                    }

                                    if (isset($requestData['Mass']['ExamGradeChange']['select_all'])) {
                                        unset($requestData['Mass']['ExamGradeChange']['select_all']);
                                    }

                                    return $this->redirect(['action' => 'manageRegistrarGradeChange']);
                                } else {


                                    $this->Flash->error(__('The system is unable to complete your exam grade change request approval. Please try again.'));
                                    if (isset($requestData['Mass']['ExamGradeChange']['select_all'])) {
                                        unset($requestData['Mass']['ExamGradeChange']['select_all']);
                                    }
                                    return $this->redirect(['action' => 'manageRegistrarGradeChange']);
                                }
                            }
                        }
                    }
                } elseif (isset($requestData['ApproveAllGradeChangeByRegistrar'])) {
                    if (isset($requestData['Mass']['ExamGradeChange']['select_all'])) {
                        unset($requestData['Mass']['ExamGradeChange']['select_all']);
                    }

                    $successfulApproval = 0;
                    if (!empty($requestData['Mass']['ExamGradeChange'])) {
                        foreach ($requestData['Mass']['ExamGradeChange'] as $grk => $grv) {
                            if ($grv['gp'] == 1) {
                                $examGradeChangeDetail = $examGradeChangesTable->find()
                                    ->where(['ExamGradeChanges.id' => $grv['id']])
                                    ->contain([
                                        'ExamGrades' => [
                                            'CourseRegistrations' => ['PublishedCourses' => ['Departments']],
                                            'CourseAdds' => ['PublishedCourses' => ['Departments']]
                                        ]
                                    ])
                                    ->first();

                                if (empty($examGradeChangeDetail)) {
                                    continue;
                                } elseif ($examGradeChangeDetail->department_approval != 1 || ($examGradeChangeDetail->makeup_exam_result === null && $examGradeChangeDetail->college_approval != 1)) {
                                    continue;
                                } elseif ($examGradeChangeDetail->registrar_approval !== null) {
                                    continue;
                                } else {
                                    $registrarGradeChangeApproval = [
                                        'id' => $grv['id'],
                                        'registrar_approval' => 1,
                                        'registrar_reason' => 'Teacher reason accepted!',
                                        'registrar_approval_date' => date('Y-m-d H:i:s'),
                                        'registrar_approved_by' => $this->Auth->user('id')
                                    ];

                                    $entity = $examGradeChangesTable->newEntity($registrarGradeChangeApproval);
                                    if ($examGradeChangesTable->save($entity, ['validate' => false])) {
                                        $successfulApproval++;
                                        $autoMessagesTable = TableRegistry::getTableLocator()->get('AutoMessages');
                                        $autoMessagesTable->sendNotificationOnRegistrarGradeChangeApproval($registrarGradeChangeApproval);

                                        $gradeChangeDetail = $examGradeChangesTable->find()
                                            ->where(['ExamGradeChanges.id' => $registrarGradeChangeApproval['id']])
                                            ->contain([
                                                'ExamGrades' => [
                                                    'CourseRegistrations' => ['PublishedCourses', 'Students'],
                                                    'CourseAdds' => ['PublishedCourses', 'Students']
                                                ]
                                            ])
                                            ->first();

                                        $student = $gradeChangeDetail->exam_grade->course_registration
                                            ? $gradeChangeDetail->exam_grade->course_registration->student
                                            : $gradeChangeDetail->exam_grade->course_add->student;
                                        $publishedCourse = $gradeChangeDetail->exam_grade->course_registration
                                            ? $gradeChangeDetail->exam_grade->course_registration->published_course
                                            : $gradeChangeDetail->exam_grade->course_add->published_course;

                                        $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                                        $statusStatus = $studentExamStatusTable->regenerateAllStatusOfStudentByStudentId($student->id);

                                        if ($statusStatus == 3) {
                                            $previousStudentExamStatus = $studentExamStatusTable->find()
                                                ->where([
                                                    'StudentExamStatuses.student_id' => $student->id,
                                                    'StudentExamStatuses.academic_year' => $publishedCourse->academic_year,
                                                    'StudentExamStatuses.semester' => $publishedCourse->semester
                                                ])
                                                ->first();

                                            if (!empty($previousStudentExamStatus)) {
                                                $statusStatus = $studentExamStatusTable->updateAcademicStatusForGradeChange($registrarGradeChangeApproval['id']);
                                            } else {
                                                $statusStatus = $studentExamStatusTable->updateAcademicStatusByPublishedCourse($publishedCourse->id);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($successfulApproval) {
                        $this->Flash->success(__('Your exam grade change request approval is successfully done.'));
                        return $this->redirect(['action' => 'manageRegistrarGradeChange']);
                    }
                }
            }

            $examGradeChanges = $examGradeChangesTable->getListOfGradeChangeForRegistrarApproval($departmentIds, $collegeIds, $programIds, $programTypeIds)['summary'];
            $makeupExamGradeChanges = $examGradeChangesTable->getListOfMakeupGradeChangeForRegistrarApproval($departmentIds, $collegeIds, $programIds, $programTypeIds)['summary'];
            $departmentMakeupExamGradeChanges = $examGradeChangesTable->getListOfMakeupGradeChangeByDepartmentForRegistrarApproval($departmentIds, $collegeIds, $programIds, $programTypeIds)['summary'];


            $this->set(compact('examGradeChanges', 'makeupExamGradeChanges', 'departmentMakeupExamGradeChanges'));
        } else {
            $this->Flash->error(__('NOT AUTHORIZED! You need to have registrar role to access exam grade change management area. Please contact your system administrator if you feel this message is not right.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function freshmanMakeupExamResult()
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_COLLEGE) {
            $this->makeupExamResult(0);
            $this->render('makeup_exam_result');
        } else {
            $this->Flash->error(__('You need to have a college role to access Freshman Supplementary/Makeup exam administration. Please contact your system administrator.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function departmentMakeupExamResult()
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_DEPARTMENT) {
            $this->makeupExamResult(1);
            $this->render('makeup_exam_result');
        } else {
            $this->Flash->error(__('You need to have department role to access Supplementary/Makeup exam administration. Please contact your system administrator.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    protected function makeupExamResult($department = 1)
    {
        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE) {
            $departmentId = $this->request->getSession()->read('Auth.User.department_id');
            $collegeId = $this->request->getSession()->read('Auth.User.college_id');
            $programIds = $this->request->getSession()->read('Auth.User.program_ids') ?: [];

            $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
            $coursesTable = TableRegistry::getTableLocator()->get('Courses');
            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
            $programsTable = TableRegistry::getTableLocator()->get('Programs');

            $requestData = $this->request->getData();
            if (!empty($requestData)) {
                $saveIsOk = true;
                $gradeHistory = [];
                $registerOrAdd = [];
                $gradeId = 0;
                $student = [];
                $publishedCourseId = 0;
                $courseTitleCode = '';
                $studentNameStudentNumber = '';

                if ($requestData['ExamGradeChange']['course_registration_id'] != "0") {
                    $registerOrAdd = explode('~', $requestData['ExamGradeChange']['course_registration_id']);
                    $type = $registerOrAdd[1] == 'add' ? 'course_add' : 'course_registration';

                    $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    $gradeId = $examGradesTable->find()
                        ->select(['exam_grade_id'])
                        ->where([$type == 'course_add' ? 'ExamGrades.course_add_id' : 'ExamGrades.course_registration_id' => $registerOrAdd[0]])
                        ->order(['ExamGrades.exam_grade_id' => 'DESC', 'ExamGrades.created' => 'DESC'])
                        ->first();

                    $gradeId = $gradeId ? $gradeId->exam_grade_id : 0;

                    $publishedCourseId = $type == 'course_add'
                        ? $courseAddsTable->find()->select(['published_course_id'])->where(['id' => $registerOrAdd[0]])->first()->published_course_id
                        : $courseRegistrationsTable->find()->select(['published_course_id'])->where(['id' => $registerOrAdd[0]])->first()->published_course_id;

                    $gradeHistory = $type == 'course_add'
                        ? $courseAddsTable->getCourseAddGradeHistory($registerOrAdd[0])
                        : $courseRegistrationsTable->getCourseRegistrationGradeHistory($registerOrAdd[0]);

                    $studentData = $type == 'course_add'
                        ? $courseAddsTable->find()->where(['CourseAdds.id' => $registerOrAdd[0]])->contain(['Students'])->first()
                        : $courseRegistrationsTable->find()->where(['CourseRegistrations.id' => $registerOrAdd[0]])->contain(['Students'])->first();

                    $student = $studentData ? (array)$studentData->student : [];

                    $onProgress = $type == 'course_add'
                        ? $courseAddsTable->isAnyGradeOnProcess($registerOrAdd[0])
                        : $courseRegistrationsTable->isAnyGradeOnProcess($registerOrAdd[0]);

                    if (!empty($publishedCourseId)) {
                        $courseId = $courseRegistrationsTable->PublishedCourses->find()
                            ->select(['course_id'])
                            ->where(['PublishedCourses.id' => $publishedCourseId])
                            ->first();
                        if ($courseId) {
                            $courseDetails = $coursesTable->find()
                                ->where(['Courses.id' => $courseId->course_id])
                                ->first();
                            $courseTitleCode = $courseDetails ? $courseDetails->course_code_title : '';
                        }
                    }

                    if (!empty($student)) {
                        $studentNameStudentNumber = $student['full_name_studentnumber'];
                    }

                    if (!empty($student) && (
                            ($department == 1 && $student['department_id'] != $departmentId) ||
                            ($department == 0 && $student['college_id'] != $collegeId)
                        )) {
                        if ($department == 1) {
                            $this->Flash->warning(__('You are not authorized to do that!'));
                            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                        }
                    }

                    if (empty($student)) {
                        $this->Flash->error(__('Invalid Student.'));
                        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                    }

                    if (empty($publishedCourseId)) {
                        $this->Flash->error(__('Invalid course is selected.'));
                        return $this->redirect(['action' => 'add']);
                    }

                    if (empty($gradeId)) {
                        $this->Flash->error(__('Exam grade is not yet submitted for {0} for {1}. Supplementary or Makeup Exam result can only be submitted after the instructor submitted student grade. Please communicate the assigned instructor to submit grade for the student or you can submit on behalf of him if you have the appropriate permission.', $studentNameStudentNumber ?: 'the selected student', $courseTitleCode ?: 'the selected course'));
                        $saveIsOk = false;
                    } else {
                        $status = $examGradesTable->gradeCanBeChanged($gradeId);
                        if ($status === true) {
                            $examGradeChange = ['exam_grade_id' => $gradeId];
                            $previousGrade = $examGradesTable->find()->select(['grade'])->where(['exam_grade_id' => $gradeId])->first()->grade;
                        } else {
                            $this->Flash->error(__($status));
                            $saveIsOk = false;
                        }
                    }
                } else {
                    $this->Flash->error(__('You are required to select the course for which {0} takes Supplementary or Makeup Exam.', $studentNameStudentNumber ?: 'the selected student'));
                    $saveIsOk = false;
                }

                if (empty($requestData['ExamGradeChange']['student_id']) || $requestData['ExamGradeChange']['student_id'] == "0") {
                    $this->Flash->error(__('You are required to select the student who takes the Supplementary or Makeup Exam.'));
                    $saveIsOk = false;
                } elseif ($saveIsOk && (empty($requestData['ExamGradeChange']['grade']) || $requestData['ExamGradeChange']['grade'] == "0")) {
                    $this->Flash->error(__('You are required to select exam grade for {0} for {1}.', $studentNameStudentNumber ?: 'the selected student', $courseTitleCode ?: 'the selected course'));
                    $saveIsOk = false;
                } elseif ($saveIsOk && !$courseRegistrationsTable->PublishedCourses->isItValidGradeForPublishedCourse($publishedCourseId, $requestData['ExamGradeChange']['grade'])) {
                    $this->Flash->warning(__('Invalid Grade for {0}!', $courseTitleCode ?: 'the selected course'));
                    $saveIsOk = false;
                } else {
                    $examGradeChange['grade'] = $requestData['ExamGradeChange']['grade'];
                }

                if ($saveIsOk && (!isset($requestData['ExamGradeChange']['makeup_exam_result']) || !is_numeric($requestData['ExamGradeChange']['makeup_exam_result']) || $requestData['ExamGradeChange']['makeup_exam_result'] < 0 || $requestData['ExamGradeChange']['makeup_exam_result'] > 100)) {
                    $this->Flash->error(__('Please enter a valid exam result.'));
                    $saveIsOk = false;
                } elseif (!isset($requestData['ExamGradeChange']['grade']) || empty($requestData['ExamGradeChange']['grade'])) {
                    $this->Flash->error(__('Please enter a valid grade.'));
                    $saveIsOk = false;
                } elseif (!isset($requestData['ExamGradeChange']['makeup_exam_result']) || empty($requestData['ExamGradeChange']['makeup_exam_result'])) {
                    $this->Flash->error(__('Please enter a valid exam result.'));
                    $saveIsOk = false;
                } else {
                    $examGradeChange['makeup_exam_result'] = $requestData['ExamGradeChange']['makeup_exam_result'];
                }

                $examGradeChange['reason'] = $requestData['ExamGradeChange']['reason'] ?? '';

                if ($roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE) {
                    $examGradeChange['department_reason'] = $requestData['ExamGradeChange']['reason'] ?? '';
                    $examGradeChange['initiated_by_department'] = 1;
                    $examGradeChange['department_approval'] = 1;
                    $examGradeChange['department_approved_by'] = $this->Auth->user('id');
                    $examGradeChange['department_approval_date'] = date('Y-m-d H:i:s');

                    if ($roleId == ROLE_COLLEGE) {
                        $examGradeChange['college_approval'] = 1;
                        $examGradeChange['college_reason'] = $requestData['ExamGradeChange']['reason'] ?? '';
                        $examGradeChange['college_approved_by'] = $this->Auth->user('id');
                        $examGradeChange['college_approval_date'] = date('Y-m-d H:i:s');
                    } else {
                        $examGradeChange['college_approval'] = null;
                        $examGradeChange['college_reason'] = '';
                        $examGradeChange['college_approved_by'] = '';
                        $examGradeChange['college_approval_date'] = null;
                    }
                } else {
                    $examGradeChange['department_reason'] = '';
                    $examGradeChange['initiated_by_department'] = 0;
                    $examGradeChange['department_approval'] = null;
                    $examGradeChange['department_approved_by'] = '';
                    $examGradeChange['department_approval_date'] = null;
                    $examGradeChange['college_approval'] = null;
                    $examGradeChange['college_reason'] = '';
                    $examGradeChange['college_approved_by'] = '';
                    $examGradeChange['college_approval_date'] = null;
                    $saveIsOk = false;
                }

                $examGradeChange['minute_number'] = $requestData['ExamGradeChange']['minute_number'] ?? '';
                $examGradeChange['registrar_reason'] = '';
                $examGradeChange['registrar_approved_by'] = '';
                $examGradeChange['registrar_approval'] = null;

                if ($saveIsOk) {
                    $entity = $examGradeChangesTable->newEntity($examGradeChange);
                    if ($examGradeChangesTable->save($entity)) {
                        if ($roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE) {
                            $this->Flash->success(__('The Makeup/Supplementary exam result for {0} for {1} has been saved and sent to the registrar for confirmation.', $studentNameStudentNumber ?: 'the selected student', $courseTitleCode ?: 'the selected course'));
                        } else {
                            $this->Flash->success(__('The Makeup/Supplementary exam result for {0} for {1} has been saved and sent to department for approval.', $studentNameStudentNumber ?: 'the selected student', $courseTitleCode ?: 'the selected course'));
                        }

                        return $this->redirect(['action' => $roleId == ROLE_DEPARTMENT ? 'departmentMakeupExamResult' : ($roleId == ROLE_COLLEGE ? 'departmentMakeupExamResult' : 'index')]);
                    } else {
                        $this->Flash->error(__('The Makeup/Supplementary exam result could not be saved for {0} for {1}. Please, try again.', $studentNameStudentNumber ?: 'the selected student', $courseTitleCode ?: 'the selected course'));
                    }
                }
            }

            $programId = !empty($requestData['ExamGradeChange']['program_id']) ? $requestData['ExamGradeChange']['program_id'] :
                (!empty($programIds) ? reset($programIds) : 1);
            if (empty($programId)) {
                $programId = 1;
            }

            $programs = $roleId == ROLE_COLLEGE
                ? $programsTable->find('list', ['conditions' => ['Programs.id' => PROGRAM_UNDERGRADUATE]])->toArray()
                : $programsTable->find('list', ['conditions' => ['Programs.id IN' => $programIds, 'Programs.active' => 1]])->toArray();

            $studentSections = ($department == 1 || $roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE)
                ? $sectionsTable->allDepartmentSectionsOrganizedByProgramTypeSuppExam($department == 1 ? $departmentId : $collegeId, $department, $programId, 3)
                : $sectionsTable->allDepartmentSectionsOrganizedByProgramType($department == 1 ? $departmentId : $collegeId, $department, $programId, 3);

            $studentSectionId = $requestData['ExamGradeChange']['student_section_id'] ?? 0;
            $students = $studentSectionId && ($department == 1 || $roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE)
                ? $examGradeChangesTable->possibleStudentsForSup($studentSectionId)
                : $sectionsTable->allStudents($studentSectionId);

            $studentId = $requestData['ExamGradeChange']['student_id'] ?? 0;
            $studentRegisteredCourses = $studentId && ($department == 1 || $roleId == ROLE_DEPARTMENT || $roleId == ROLE_COLLEGE)
                ? $courseRegistrationsTable->Students->getPossibleStudentRegisteredAndAddCoursesForSup($studentId)
                : $courseRegistrationsTable->Students->getStudentRegisteredAndAddCourses($studentId);

            $registeredCourseId = $requestData['ExamGradeChange']['course_registration_id'] ?? 0;
            $examGrades = $publishedCourseId ? $courseRegistrationsTable->getPublishedCourseGradeScaleList($publishedCourseId) : [];
            $examGrades = !empty($examGrades) ? ['0' => '[ Select Grade ]'] + $examGrades : ['0' => '[ No Grade Scale Found ]'];
            $grade = !empty($requestData['ExamGradeChange']['grade']) ? $requestData['ExamGradeChange']['grade'] : '';

            $studentSections = !empty($studentSections) ? ['0' => '[ Select Section ]'] + $studentSections : ['0' => '[ No Section Found ]'];
            $students = !empty($students) ? ['0' => '[ Select Student ]'] + $students : ['0' => '[ No Student Found ]'];
            $studentRegisteredCourses = !empty($studentRegisteredCourses) ? ['0' => '[ Select Course ]'] + $studentRegisteredCourses : ['0' => '[ No Course Found ]'];

            $this->set(compact(
                'studentSections',
                'studentSectionId',
                'programs',
                'programId',
                'students',
                'studentId',
                'studentRegisteredCourses',
                'registeredCourseId',
                'examGrades',
                'grade',
                'gradeHistory',
                'registerOrAdd',
                'saveIsOk'
            ));
        } else {
            $this->Flash->error(__('You need to have either college or department role to access Supplementary/Makeup exam administration. Please contact your system administrator.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
        if (!$id || !$examGradeChangesTable->exists(['id' => $id])) {
            $this->Flash->error(__('Invalid id for exam grade change deletion.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($examGradeChangesTable->canItBeDeleted($id)) {
            $entity = $examGradeChangesTable->get($id);
            if ($examGradeChangesTable->delete($entity)) {
                $this->Flash->success(__('Exam grade change is deleted'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Exam grade change was not deleted'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('Exam grade change is either submitted by the instructor or already on approval process to delete.'));
            return $this->redirect(['action' => 'index']);
        }
    }
}
?>
