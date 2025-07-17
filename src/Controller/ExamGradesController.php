<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ComponentCollection;
use Cake\I18n\Time;
use Cake\Mailer\Email;

class ExamGradesController extends AppController
{
    public $name = 'ExamGrades';

    public $helpers = ['Html', 'Form'];

    protected $menuOptions = [
        'parent' => 'grades',
        'exclude' => [
            'index',
            'add',
            'autoNgAndDoToF',
            'studentCopy',
            'exportMstersheetXls',
            'exportRemedialMastersheetXls',
            'viewGrade',
            'cancelFxResitRequest',
            'academicStatusGradeInterface',
            'getAddCoursesDataEntry',
            'getPublishedAddCourses',
            'getRemedialSectionsCombo',
        ],
        'alias' => [
            'approveNonFreshmanGradeSubmission' => 'Approve Grade Submission',
            'approveFreshmanGradeSubmission' => 'Approve Freshman Grade',
            'manageNg' => 'Manage NG',
            'studentGradeView' => 'My Grade Report',
            'departmentGradeReport' => 'Student Grade Report',
            'freshmanGradeReport' => 'Freshman Grade Report',
            'dataEntryInterface' => 'Missing Registration & Grade Entry',
            'academicStatusGradeInterface' => 'Data Entry with Academic Status',
            'gradeUpdate' => 'Grade Cancellation and Update',
            'requestFxExamSit' => 'Request FX Resit Exam',
            'viewFxResit' => 'View FX resit requests',
            'cancelNgGrade' => 'Cancel Grade Converted from NG',
            'masterSheetRemedial' => 'Remedial Master Sheet',
            'collegeRegistrarGradeReport' => 'Student Grade Report'
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Flash');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow([
            'autoNgAndDoToF',
            'exportMstersheetXls',
            'exportMastersheetPdf',
            'viewXls',
            'manageNg',
            'viewFxResit',
            'exportRemedialMastersheetXls',
            'getRemedialSectionsCombo',
            'cheatingView'
        ]);
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        $currentAcademicYear = $defaultAcademicYear = $this->AcademicYear->currentAcademicYear();

        $currAcYearExploded = explode('/', $currentAcademicYear);
        $previousAcademicYear = $currentAcademicYear;

        if (!empty($currAcYearExploded)) {
            $previousAcademicYear = ($currAcYearExploded[0] - 1) . '/' . ($currAcYearExploded[1] - 1);
        }

        $applicationStartYear = defined('APPLICATION_START_YEAR') ? APPLICATION_START_YEAR : 2000;
        $acyearArrayData = $this->AcademicYear->academicYearInArray($applicationStartYear, explode('/', $currentAcademicYear)[0]);

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        $yearLevelTable = TableRegistry::getTableLocator()->get('YearLevels');

        $programs = $programTable->find('list')
            ->where(['Programs.active' => 1])
            ->toArray();

        $programTypes = $programTypeTable->find('list')
            ->where(['ProgramTypes.active' => 1])
            ->toArray();

        $deptsForYearLevel = $departmentTable->find('list')
            ->where(['Departments.active' => 1])
            ->toArray();

        $yearLevels = $yearLevelTable->distinctYearLevelBasedOnRole(
            null,
            null,
            array_keys($deptsForYearLevel),
            array_keys($programs)
        );

        if (($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE) && $this->request->getSession()->read('Auth.User.is_admin') == 0) {
            $programs = $programTable->find('list')
                ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
                ->toArray();

            $programTypes = $programTypeTable->find('list')
                ->where(['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1])
                ->toArray();

            $acyearArrayData = $this->AcademicYear->academicYearInArray(
                (explode('/', $defaultAcademicYear)[0] - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL),
                explode('/', $defaultAcademicYear)[0]
            );
        }

        $academicYearRange = $this->AcademicYear->academicYearInArray(
            (explode('/', $defaultAcademicYear)[0] - ACY_BACK_GRADE_APPROVAL_DASHBOARD),
            explode('/', $defaultAcademicYear)[0]
        );

        if (count($academicYearRange) >= 2) {
            $startYr = array_pop($academicYearRange);
            $endYr = reset($academicYearRange);
            $yearsToLookListForDisplay = 'from ' . $startYr . ' up to ' . $endYr;
        } elseif (count($academicYearRange) == 1) {
            $yearsToLookListForDisplay = ' on ' . $defaultAcademicYear;
        } else {
            $yearsToLookListForDisplay = '';
        }


        $this->set(compact(
            'acyearArrayData',
            'defaultAcademicYear',
            'previousAcademicYear',
            'programTypes',
            'programs',
            'yearLevels',
            'yearsToLookListForDisplay'
        ));

        if ($this->request->getData('User.password')) {
            $this->request = $this->request->withData('User.password', null);
        }
    }

    public function index()
    {
        if ($this->Acl->check($this->Auth->user(), 'controllers/ExamGrades/collegeGradeView')) {
            return $this->redirect(['controller' => 'ExamGrades', 'action' => 'collegeGradeView']);
        } elseif ($this->Acl->check($this->Auth->user(), 'controllers/ExamResults/submitGradeForInstructor')) {
            return $this->redirect(['controller' => 'ExamResults', 'action' => 'submitGradeForInstructor']);
        } elseif ($this->Acl->check($this->Auth->user(), 'controllers/ExamResults/submitFreshmanGradeForInstructor')) {
            return $this->redirect(['controller' => 'ExamResults', 'action' => 'submitFreshmanGradeForInstructor']);
        } elseif ($this->Acl->check($this->Auth->user(), 'controllers/ExamGrades/registrarGradeView')) {
            return $this->redirect(['controller' => 'ExamGrades', 'action' => 'registrarGradeView']);
        } elseif ($this->Acl->check($this->Auth->user(), 'controllers/ExamResults/add')) {
            return $this->redirect(['controller' => 'ExamResults', 'action' => 'add']);
        } elseif ($this->Acl->check($this->Auth->user(), 'controllers/ExamGrades/studentGradeView')) {
            return $this->redirect(['controller' => 'ExamGrades', 'action' => 'studentGradeView']);
        } else {
            $this->Flash->warning('You are not Authorized to access the page you just selected!');
            return $this->redirect('/');
        }
    }

    public function studentCopy($student_id = null)
    {
        $student_copy = null;
        $costShares = [];
        $costSharingPayments = [];
        $clearances = [];

        if (!empty($this->request->getData('displayStudentCopyPrint')) && !empty($this->request->getData('ExamGrade.id'))) {
            $student_id = $this->request->getData('ExamGrade.id');
        }

        if (isset($student_id) || !empty($this->request->getData('continueStudentCopyPrint'))) {
            if (!empty($this->request->getData('ExamGrade.studentnumber')) && !empty($this->request->getData('continueStudentCopyPrint'))) {
                $studentnumber = trim($this->request->getData('ExamGrade.studentnumber'));

                if (empty($studentnumber)) {
                    $this->Flash->error('Please provide Student ID.');
                    return $this->redirect(['action' => 'studentCopy']);
                } else {
                    $studentTable = TableRegistry::getTableLocator()->get('Students');
                    $student_detail = $studentTable->find()
                        ->where(['Students.studentnumber' => $studentnumber])
                        ->first();

                    if (isset($student_detail->id)) {
                        $costShareTable = TableRegistry::getTableLocator()->get('CostShares');
                        $costSharingPaymentTable = TableRegistry::getTableLocator()->get('CostSharingPayments');
                        $clearanceTable = TableRegistry::getTableLocator()->get('Clearances');

                        $costShares = $costShareTable->find()
                            ->where(['CostShares.student_id' => $student_detail->id])
                            ->order(['CostShares.cost_sharing_sign_date' => 'ASC'])
                            ->toArray();

                        $costSharingPayments = $costSharingPaymentTable->find()
                            ->where(['CostSharingPayments.student_id' => $student_detail->id])
                            ->order(['CostSharingPayments.created' => 'ASC'])
                            ->toArray();

                        $clearances = $clearanceTable->find()
                            ->where([
                                'Clearances.student_id' => $student_detail->id,
                                'Clearances.type' => 'clearance',
                                'Clearances.confirmed' => 1
                            ])
                            ->order(['Clearances.request_date' => 'ASC'])
                            ->toArray();
                    }
                }
            } else {
                $studentTable = TableRegistry::getTableLocator()->get('Students');
                $student_detail = $studentTable->find()
                    ->where(['Students.id' => $student_id])
                    ->first();
            }

            if (empty($student_detail)) {
                $this->Flash->error('Please provide a valid Student ID.');
                return $this->redirect(['action' => 'studentCopy']);
            } elseif ($this->request->getSession()->read('Auth.User.is_admin') == 0 && (
                    (!empty($student_detail->department_id) && !in_array($student_detail->department_id, $this->department_ids)) ||
                    (empty($student_detail->department_id) && !in_array($student_detail->college_id, $this->college_ids))
                )) {
                $departmentTable = TableRegistry::getTableLocator()->get('Departments');
                $collegeTable = TableRegistry::getTableLocator()->get('Colleges');

                if (!empty($student_detail->department_id)) {
                    $department_name = $departmentTable->get($student_detail->department_id)->name . ' Department';
                } else {
                    $department_name = $collegeTable->get($student_detail->college_id)->name . ' Freshman Program';
                }

                $this->Flash->error("You do not have the privilege to manage {$department_name} students. Please contact the registrar system administrator to get privilege on {$department_name}.");
                return $this->redirect(['action' => 'studentCopy']);
            } else {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $student_copy = $examGradeTable->studentCopy([$student_detail->id])[0];

                if (empty($student_copy['courses_taken'])) {
                    $this->Flash->error('There is no course a student registered for to display student copy.');
                } elseif (!empty($this->request->getData('displayStudentCopyPrint')) && !empty($this->request->getData('ExamGrade.id'))) {
                    $no_of_semester = $this->request->getData('ExamGrade.no_of_semester');
                    $course_justification = $this->request->getData('ExamGrade.course_justification');
                    $font_size = $this->request->getData('ExamGrade.font_size');

                    if ($course_justification == 2) {
                        $course_justification = 0;
                    } elseif ($course_justification == 0) {
                        $course_justification = -2;
                    } else {
                        $course_justification = -1;
                    }

                    $student_copies = [$student_copy];

                    $this->set(compact('student_copies', 'no_of_semester', 'course_justification', 'font_size'));

                    $this->response = $this->response->withType('application/pdf');
                    $this->viewBuilder()->setLayout('/pdf/default');
                    $this->render('/Elements/student_copy_pdf');
                }
            }
        }

        $font_size_options = [
            27 => 'Small 1', 28 => 'Small 2', 29 => 'Small 3',
            30 => 'Medium 1', 31 => 'Medium 2', 32 => 'Medium 3',
            33 => 'Large 1', 34 => 'Large 2'
        ];
        $this->set(compact('student_copy', 'font_size_options', 'costShares', 'costSharingPayments', 'clearances'));
    }

    public function massStudentCopy()
    {
        $this->massStudentCopyInternal(null, null, null);
    }

    protected function massStudentCopyInternal($program_id = null, $program_type_id = null, $department = null)
    {
        $studentTable = TableRegistry::getTableLocator()->get('Students');
        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');

        $programs = $programTable->find('list')->toArray();
        $program_types = $programTypeTable->find('list')->toArray();
        $departments = $departmentTable->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids);
        $department_combo_id = null;
        $program_types = [0 => 'All Program Types'] + $program_types;
        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;

        if (!empty($this->request->getData('listStudentsForStudentCopy'))) {
            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $students_for_mass_student_copy = $examGradeTable->CourseRegistrations->Students->getStudentListName(
                $this->request->getData('ExamGrade.academic_year'),
                $this->request->getData('ExamGrade.program_id'),
                $this->request->getData('ExamGrade.program_type_id'),
                $this->request->getData('ExamGrade.department_id'),
                null,
                $this->request->getData('ExamGrade.studentnumber'),
                $this->request->getData('ExamGrade.name')
            );

            $default_department_id = $this->request->getData('ExamGrade.department_id');
            $default_program_id = $this->request->getData('ExamGrade.program_id');
            $default_program_type_id = $this->request->getData('ExamGrade.program_type_id');
            $academic_year_selected = $this->request->getData('ExamGrade.academic_year');

            $program_id = $this->request->getData('ExamGrade.program_id');
            $program_type_id = $this->request->getData('ExamGrade.program_type_id');
        }

        if (!empty($this->request->getData('getStudentCopy'))) {
            $student_ids = [];

            foreach ($this->request->getData('Student') as $student) {
                if ($student['gp'] == 1) {
                    $student_ids[] = $student['student_id'];
                }
            }

            if (empty($student_ids)) {
                $this->Flash->error('You are required to select at least one student.');
            } else {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $student_copies = $examGradeTable->studentCopy($student_ids);

                if (empty($student_copies)) {
                    $this->Flash->info('There is no course registration for the selected students to display student copy.');
                } else {
                    $no_of_semester = $this->request->getData('Setting.no_of_semester');
                    $course_justification = $this->request->getData('Setting.course_justification');
                    $font_size = $this->request->getData('Setting.font_size');

                    if ($course_justification == 2) {
                        $course_justification = 0;
                    } elseif ($course_justification == 0) {
                        $course_justification = -2;
                    } else {
                        $course_justification = -1;
                    }

                    $this->set(compact('student_copies', 'no_of_semester', 'course_justification', 'font_size'));
                    $this->response = $this->response->withType('application/pdf');
                    $this->viewBuilder()->setLayout('/pdf/default');
                    $this->render('/Elements/student_copy_pdf');
                }
            }
        }

        $font_size_options = [
            27 => 'Small 1', 28 => 'Small 2', 29 => 'Small 3',
            30 => 'Medium 1', 31 => 'Medium 2', 32 => 'Medium 3',
            33 => 'Large 1', 34 => 'Large 2'
        ];

        $this->set(compact(
            'departments',
            'program_types',
            'programs',
            'default_program_type_id',
            'font_size_options',
            'students_for_mass_student_copy',
            'default_program_id',
            'default_department_id'
        ));
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid exam grade'));
            return $this->redirect(['action' => 'index']);
        }

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $examGrade = $examGradeTable->get($id);
        $this->set('examGrade', $examGrade);
    }

    public function autoNgAndDoToF()
    {
        $privileged_registrar = [];

        $userTable = TableRegistry::getTableLocator()->get('Users');
        $all_users = $userTable->find()
            ->where([
                'Users.role_id IN' => [ROLE_REGISTRAR, ROLE_COLLEGE, ROLE_DEPARTMENT, ROLE_INSTRUCTOR],
                'Users.active' => 1
            ])
            ->contain(['StaffAssignes'])
            ->toArray();

        if (!empty($all_users)) {
            foreach ($all_users as $user) {
                if ($this->Acl->check($user, 'controllers/ExamGrades/registrar_grade_view')) {
                    $privileged_registrar[] = $user;
                }
            }

            if (!empty($privileged_registrar)) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $examGradeTable->ExamGradeChanges->autoNgAndDoConversion($privileged_registrar);
            } else {
                return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
            }
        }
    }

    public function manageNg($published_course_id = null)
    {
        if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR) {
            $this->Flash->error('You are not authorized to manage NG grades.');
            return $this->redirect('/');
        }

        $published_course_combo_id = null;
        $department_combo_id = null;
        $publishedCourses = [];
        $students_with_ng = [];
        $have_message = false;
        $privileged_registrar = [];

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');

        $programs = $programTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();

        $program_types = $programTypeTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $departments = [];
        $colleges = [];
        $only_pre_assigned = 0;

        if (!empty($this->department_ids)) {
            $departments = $departmentTable->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
        } elseif (!empty($this->college_ids)) {
            if ($this->onlyPre) {
                $only_pre_assigned = 1;
                $departments = $departmentTable->onlyFreshmanInAllColleges($this->college_ids, 1);
            } else {
                $departments = $departmentTable->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
            }
        }

        if ($this->request->getSession()->read('Auth.User.is_admin') == 1) {
            $departments = $departmentTable->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids,
                true, true);
        }

        if (!empty($published_course_id)) {
            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $published_course_details = $publishedCourseTable->find()
                ->where(['PublishedCourses.id' => $published_course_id])
                ->contain([
                    'Departments' => ['fields' => ['id', 'college_id']],
                    'GivenByDepartments' => ['fields' => ['id', 'college_id']],
                    'CourseInstructorAssignments' => ['Staffs'],
                    'Courses' => ['fields' => ['id', 'course_title', 'course_code','year_level_id']],
                    'Sections' => ['fields' => ['id', 'name', 'academicyear','year_level_id']],
                    'CourseRegistrations' => [
                        'ExamGrades' => ['fields' => ['id', 'grade','course_registration_id']],
                        'queryBuilder' => function ($q) {
                            return $q->limit(1);
                        }
                    ],
                    'CourseAdds' => [
                        'ExamGrades' => ['fields' => ['id', 'grade','course_add_id']],
                        'queryBuilder' => function ($q) {
                            return $q->limit(1);
                        }
                    ]
                ])
                ->first();

            $deptID = [];
            $collID = [];
            $user_ids_to_look = [];
            $all_users = [];

            if (!empty($published_course_details->given_by_department_id)) {
                $deptID[] = $published_course_details->given_by_department_id;
                if (!empty($published_course_details->given_by_department->college_id)
                    && is_numeric($published_course_details->given_by_department->college_id)) {
                    $collID[] = $published_course_details->given_by_department->college_id;
                }
            }

            if (!empty($published_course_details->department_id)) {
                $deptID[] = $published_course_details->department_id;
                if (!empty($published_course_details->department->college_id) &&
                    is_numeric($published_course_details->department->college_id)) {
                    $collID[] = $published_course_details->department->college_id;
                }
            }

            if (!empty($published_course_details->college_id) && is_numeric($published_course_details->college_id)) {
                $collID[] = $published_course_details->college_id;
            }

            if (!empty($deptID)) {
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $staffsTable = TableRegistry::getTableLocator()->get('Staffs');

                $department_heads = $usersTable->find('list')
                    ->select(['Users.id'])
                    ->where([
                        'Users.id IN' => $staffsTable->find()->select(['user_id'])->where(['Staffs.department_id IN' => $deptID,
                            'Staffs.active' => 1]),
                        'Users.active' => 1,
                        'Users.is_admin' => 1,
                        'Users.role_id' => ROLE_DEPARTMENT
                    ])
                    ->toArray();

                if (!empty($department_heads)) {
                    $user_ids_to_look = $department_heads;
                }
            }

            if (!empty($collID)) {
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $staffsTable = TableRegistry::getTableLocator()->get('Staffs');

                $college_deans = $usersTable->find('list')
                    ->select(['Users.id'])
                    ->where([
                        'Users.id IN' => $staffsTable->find()->select(['user_id'])->where(['Staffs.college_id IN' => $collID,
                            'Staffs.active' => 1]),
                        'Users.active' => 1,
                        'Users.is_admin' => 1,
                        'Users.role_id' => ROLE_COLLEGE,
                    ])
                    ->toArray();

                if (!empty($college_deans)) {
                    debug($college_deans);
                    $user_ids_to_look = array_merge($user_ids_to_look, $college_deans);
                }
            }

            $user_ids_to_look[$this->request->getSession()->read('Auth.User.id')] =
                $this->request->getSession()->read('Auth.User.id');

            $user_ids_to_look[$this->request->getSession()->read('Auth.User.id')] =
                $this->request->getSession()->read('Auth.User.id');

            if (!empty($user_ids_to_look)) {
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $all_users = $usersTable->find()
                    ->where(['Users.id IN' => $user_ids_to_look])
                    ->contain(['StaffAssignes'])
                    ->toArray();
            }

            if (!empty($all_users)) {
                foreach ($all_users as $user) {
                    $privileged_registrar[] = $user;
                }
            }

        }

        if (!empty($this->request->getData('listPublishedCourses'))) {
            // No action needed here
        } elseif (!empty($this->request->getData('changeNgGrade'))) {
            if (trim($this->request->getData('ExamGrade.minute_number')) === '') {
                $this->Flash->error('Please enter minute number.');
            } else {
                $check1 = 1;
                $check2 = 1;

                if ($this->request->getSession()->read('Auth.User.is_admin') != 1) {
                    $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                    if (!empty($this->department_ids)) {
                        $check1 = $publishedCourseTable->find()
                            ->where([
                                'PublishedCourses.id' => $published_course_id,
                                'OR' => [
                                    'PublishedCourses.given_by_department_id IN' => $this->department_ids,
                                    'PublishedCourses.department_id IN' => $this->department_ids
                                ]
                            ])
                            ->count();
                    }

                    if (!empty($this->college_ids)) {
                        $check2 = $publishedCourseTable->find()
                            ->where([
                                'PublishedCourses.id' => $published_course_id,
                                'PublishedCourses.college_id IN' => $this->college_ids
                            ])
                            ->count();
                    }
                }

                if ($check1 == 0 || $check2 == 0) {
                    $this->Flash->error('You are not authorized to manage the selected NG grades.');
                    return $this->redirect('/');
                }

                debug($privileged_registrar);

                $exam_grade_changes = [];

                if (!empty($this->request->getData('ExamGrade'))) {
                    foreach ($this->request->getData('ExamGrade') as $grade_change) {
                        if (is_array($grade_change) && !empty($grade_change['grade'])) {
                            $exam_grade_changes[] = $grade_change;
                        }
                    }
                }

                if (empty($exam_grade_changes)) {
                    $this->Flash->error('You are required to select at least one student NG grade change.');
                } else {
                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    if ($examGradeTable->ExamGradeChanges->applyManualNgConversion(
                        $exam_grade_changes,
                        trim($this->request->getData('ExamGrade.minute_number')),
                        $this->request->getSession()->read('Auth.User.id'),
                        $privileged_registrar,
                        $this->request->getSession()->read('Auth.User.full_name')
                    )) {
                        $have_message = true;
                        $this->Flash->success('NG exam grade change for ' . count($exam_grade_changes) . ' student grades was successful.');
                        return $this->redirect(!empty($published_course_id) ? ['action' => 'manageNg', $published_course_id] : ['action' => 'manageNg']);
                    } else {
                        $this->Flash->error('NG exam grade change is not successful for the selected students. Please try again.');
                    }
                }
            }
        }

        if (!empty($this->request->getData()) && !empty($this->request->getData('listPublishedCourses'))) {
            $department_id = $this->request->getData('ExamGrade.department_id');
            $this->request = $this->request->withData('ExamGrade.published_course_id', null);
            $published_course_id = null;
            $department_combo_id = $department_id;

            $college_id = explode('~', $department_id);

            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            if (is_array($college_id) && count($college_id) > 1) {
                $college_id = $college_id[1];
                $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesCollegeFreshTakingOrgBySection(
                    $college_id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester'),
                    $this->request->getData('ExamGrade.program_id'),
                    $this->request->getData('ExamGrade.program_type_id')
                );
            } else {
                $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesSectionsTakingOrgBySection(
                    $department_id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester'),
                    $this->request->getData('ExamGrade.program_id'),
                    $this->request->getData('ExamGrade.program_type_id')
                );
            }

            debug($publishedCourses);

            if (empty($publishedCourses)) {
                $collegeTable = TableRegistry::getTableLocator()->get('Colleges');
                $departmentTable = TableRegistry::getTableLocator()->get('Departments');
                $colleges = $collegeTable->find('list')->toArray();
                $departments = $departmentTable->find('list')->toArray();

                $this->Flash->info('No published course is found under ' . ($this->onlyPre || count(explode('~', $department_id)) > 1 ? $colleges[$college_id] : $departments[$department_id]) . ' with the selected search criteria.');
                return $this->redirect(['action' => 'manageNg']);
            } else {
                $publishedCourses = [0 => '[ Select Published Course ]'] + $publishedCourses;
            }
        }

        if (!empty($published_course_id) || (!empty($this->request->getData('ExamGrade.published_course_id')) && $this->request->getData('ExamGrade.published_course_id') != 0)) {
            if (!empty($this->request->getData('ExamGrade.published_course_id'))) {
                $published_course_id = $this->request->getData('ExamGrade.published_course_id');
            }

            $publishedCourses = [];
            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $published_course = $publishedCourseTable->find()
                ->where(['PublishedCourses.id' => $published_course_id])
                ->contain(['Courses', 'Sections'])
                ->first();


            if ($this->request->getSession()->read('Auth.User.is_admin') != 1 && (
                    empty($published_course) ||
                    (!empty($published_course->department_id) && !in_array($published_course->department_id, $this->department_ids)) ||
                    (!empty($published_course->college_id) && !in_array($published_course->college_id, $this->college_ids))
                )) {
                $this->Flash->error(empty($published_course) ? 'Please select a valid published course.' : 'You are not authorized to manage the selected published course.');
                return $this->redirect(['action' => 'manageNg']);
            } elseif (empty($published_course)) {
                $this->Flash->error('Please select a valid published course.');
                return $this->redirect(['action' => 'manageNg']);
            } else {

                if (empty($published_course->department_id)) {

                    $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesCollegeFreshTakingOrgBySection(
                        $published_course->college_id,
                        $published_course->academic_year,
                        $published_course->semester,
                        $published_course->program_id,
                        $published_course->program_type_id
                    );
                    $department_combo_id = 'c~' . $published_course->college_id;
                } else {


                    $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesSectionsTakingOrgBySection(
                        $published_course->department_id,
                        $published_course->academic_year,
                        $published_course->semester,
                        $published_course->program_id,
                        $published_course->program_type_id
                    );
                    $department_combo_id = $published_course->department_id;
                }
            }


            $published_course_combo_id = $published_course_id;
            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $students_with_ng = $examGradeTable->getStudentsWithNG($published_course_id);

            if (empty($students_with_ng) && !$have_message) {
                $this->Flash->info('There is no student with NG grade for ' . ($published_course->course->course_code_title ?? '') . ' course from ' . ($published_course->section->name ?? '') . ' section.');
            }

            $program_id = $published_course->program_id;
            $program_type_id = $published_course->program_type_id;
            $department_id = !empty($published_course->college_id) ? 'c~' . $published_course->college_id : $published_course->department_id;
            $college_id = $published_course->college_id ?? null;
            $academic_year_selected = $published_course->academic_year;
            $semester_selected = $published_course->semester;

            $this->request = $this->request->withData('ExamGrade.department_id', $department_id);
        }

        $applicable_grades = [
            '' => '[ Select Grade ]',
            'I' => 'I (Incomplete)',
            'DO' => 'DO (Dropout)',
            'W' => 'W (Withdraw)',
            'F' => 'F'
        ];

        $this->set(compact(
            'publishedCourses',
            'programs',
            'program_types',
            'departments',
            'published_course_combo_id',
            'department_combo_id',
            'students_with_ng',
            'applicable_grades',
            'program_id',
            'program_type_id',
            'department_id',
            'college_id',
            'academic_year_selected',
            'semester_selected',
            'only_pre_assigned'
        ));
    }

    public function manageFx($published_course_id = null)
    {
        $published_course_combo_id = null;
        $department_combo_id = null;
        $publishedCourses = [];
        $students_with_ng = [];
        $have_message = false;

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = $programTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();

        $program_types = $programTypeTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $departments = [];
        $colleges = [];
        $only_pre_assigned = 0;

        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        if (!empty($this->department_ids)) {
            $departments = $departmentTable->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
        } elseif (!empty($this->college_ids)) {
            if ($this->onlyPre) {
                $only_pre_assigned = 1;
                $departments = $departmentTable->onlyFreshmanInAllColleges($this->college_ids, 1);
            } else {
                $departments = $departmentTable->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
            }
        }

        if ($this->request->getSession()->read('Auth.User.is_admin') == 1) {
            $departments = $departmentTable->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids, true, true);
        }

        if (!empty($this->request->getData('listPublishedCourses'))) {
            // No action needed here
        } elseif (!empty($this->request->getData('ExamGrade')) && !empty($this->request->getData('changeNgGrade'))) {
            debug($this->request->getData());

            if (trim($this->request->getData('ExamGrade.minute_number')) === '') {
                $this->Flash->error(__('Please enter minute number.'));
            } else {
                $exam_grade_changes = [];
                $student_ids_to_regenerate_status = [];

                foreach ($this->request->getData('ExamGrade') as $grade_change) {
                    if (is_array($grade_change) && !empty($grade_change['grade']) && $grade_change['grade'] != 'Fx' && !empty($grade_change['student_id'])) {
                        $exam_grade_changes[] = $grade_change;
                        if (!in_array($grade_change['student_id'], $student_ids_to_regenerate_status)) {
                            $student_ids_to_regenerate_status[] = $grade_change['student_id'];
                        }
                    }
                }

                debug($exam_grade_changes);
                debug($student_ids_to_regenerate_status);

                if (empty($exam_grade_changes)) {
                    $this->Flash->error(__('You are required to apply at least one grade change.'));
                } else {
                    $privileged_registrar = [];
                    $userTable = TableRegistry::getTableLocator()->get('Users');
                    $all_users = $userTable->find()
                        ->where([
                            'Users.role_id IN' => [ROLE_REGISTRAR, ROLE_COLLEGE, ROLE_DEPARTMENT, ROLE_INSTRUCTOR],
                            'Users.active' => 1
                        ])
                        ->contain(['StaffAssignes'])
                        ->toArray();

                    foreach ($all_users as $user) {
                        if ($this->Acl->check($user, 'controllers/ExamGrades/registrar_grade_view')) {
                            $privileged_registrar[] = $user;
                        }
                    }

                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    if (!$examGradeTable->ExamGradeChanges->applyManualFxConversion(
                        $exam_grade_changes,
                        trim($this->request->getData('ExamGrade.minute_number')),
                        $this->Auth->user('id'),
                        $privileged_registrar
                    )) {
                        $this->Flash->error(__('Fx exam grade change is not done for the selected students. Please try again.'));
                    } else {
                        $have_message = true;
                        $exam_grade_changes_count = count($exam_grade_changes);
                        $this->Flash->success(__('Fx exam grade change for ' . $exam_grade_changes_count . ' ' . ($exam_grade_changes_count > 1 ? 'courses' : 'course') . ' was successful.'));

                        if ($this->request->getSession()->check('exam_grade_search_filters_pid')) {
                            $this->request->getSession()->delete('exam_grade_search_filters_pid');
                        }

                        $this->initExamGradeSearchFiltersPid();

                        if (!empty($student_ids_to_regenerate_status)) {
                            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                            foreach ($student_ids_to_regenerate_status as $stdnt_id) {
                                $status_status = $studentExamStatusTable->regenerate_all_status_of_student_by_student_id($stdnt_id, 0);
                                if ($status_status == 3) {
                                    // Status regenerated in last week, check for changes
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($this->request->getData('listPublishedCourses'))) {
            if (!empty($published_course_id)) {
                if ($this->request->getSession()->check('exam_grade_search_filters_pid')) {
                    $this->request->getSession()->delete('exam_grade_search_filters_pid');
                }
                $this->initExamGradeSearchFiltersPid();
                return $this->redirect(['action' => 'manageFx']);
            }

            $department_id = $this->request->getData('ExamGrade.department_id');
            $this->request = $this->request->withData('ExamGrade.published_course_id', null);
            $published_course_id = null;
            $department_combo_id = $department_id;
            $college_id = explode('~', $department_id);

            $registrar = ($this->role_id == ROLE_REGISTRAR) ? 1 : 0;

            $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            if (is_array($college_id) && count($college_id) > 1) {
                $college_id = $college_id[1];
                $publishedCourses = $courseRegistrationTable->listOfCoursesWithFx(
                    $college_id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester'),
                    $this->request->getData('ExamGrade.program_id'),
                    $this->request->getData('ExamGrade.program_type_id'),
                    1,
                    $registrar
                );
            } else {
                $publishedCourses = $courseRegistrationTable->listOfCoursesWithFx(
                    $department_id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester'),
                    $this->request->getData('ExamGrade.program_id'),
                    $this->request->getData('ExamGrade.program_type_id'),
                    0,
                    $registrar
                );
            }

            if (empty($publishedCourses)) {
                $this->Flash->info(__('No published course with Fx grade is found with the selected filter criteria'));
                return $this->redirect(['action' => 'manageFx']);
            } else {
                $publishedCourses = [0 => '[ Select Published Course ]'] + $publishedCourses;
            }
        }

        if (!empty($published_course_id) || (!empty($this->request->getData('ExamGrade.published_course_id')) && $this->request->getData('ExamGrade.published_course_id') != 0)) {
            if (!empty($this->request->getData('ExamGrade.published_course_id'))) {
                $published_course_id = $this->request->getData('ExamGrade.published_course_id');
            }

            $publishedCourses = [];
            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $published_course = $publishedCourseTable->find()
                ->where(['PublishedCourses.id' => $published_course_id])
                ->contain(['Courses', 'Sections'])
                ->first();

            if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') != 1 && (
                    empty($published_course) ||
                    (!empty($published_course->department_id) && !in_array($published_course->department_id, $this->department_ids)) ||
                    (!empty($published_course->college_id) && !in_array($published_course->college_id, $this->college_ids))
                )) {
                $this->Flash->error(empty($published_course) ? 'Please select a valid published course.' : 'You are not authorized to manage the selected published course.');
                return $this->redirect(['action' => 'manageFx']);
            } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT && !empty($published_course->given_by_department_id) && $this->department_id != $published_course->given_by_department_id) {
                $this->Flash->error('Please select a valid published course.');
                return $this->redirect(['action' => 'manageFx']);
            } elseif (empty($published_course)) {
                $this->Flash->error('Please select a valid published course.');
                return $this->redirect(['action' => 'manageFx']);
            } else {
                $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                if (empty($published_course->department_id)) {
                    if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && (
                            $this->request->getSession()->read('Auth.User.is_admin') == 1 ||
                            (!empty($this->college_ids) && !empty($published_course->college_id) && in_array($published_course->college_id, $this->college_ids))
                        )) {
                        $publishedCourses = $courseRegistrationTable->listOfCoursesWithFx(
                            $published_course->college_id,
                            $published_course->academic_year,
                            $published_course->semester,
                            $published_course->program_id,
                            $published_course->program_type_id,
                            1
                        );
                        $department_combo_id = 'c~' . $published_course->college_id;
                    } elseif (
                        (!empty($published_course->given_by_department_id) || !empty($published_course->department_id)) &&
                        (
                            ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && !empty($this->department_ids) && !empty($published_course->given_by_department_id) && in_array($published_course->given_by_department_id, $this->department_ids)) ||
                            ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT && !empty($published_course->given_by_department_id) && $published_course->given_by_department_id == $this->department_id)
                        )
                    ) {
                        $deptID = $published_course->given_by_department_id ?? $published_course->department_id;
                        $publishedCourses = $courseRegistrationTable->listOfCoursesWithFx(
                            $deptID,
                            $published_course->academic_year,
                            $published_course->semester,
                            $published_course->program_id,
                            $published_course->program_type_id,
                            0
                        );
                        $department_combo_id = $deptID;
                    }
                } else {
                    $publishedCourses = $courseRegistrationTable->listOfCoursesWithFx(
                        $published_course->given_by_department_id,
                        $published_course->academic_year,
                        $published_course->semester,
                        $published_course->program_id,
                        $published_course->program_type_id,
                        0
                    );
                    $department_combo_id = $published_course->department_id;
                }
            }

            $published_course_combo_id = $published_course_id;
            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $students_with_ng = $examGradeTable->getStudentsWithFX($published_course_id);

            if (empty($students_with_ng) && !$have_message) {
                $this->Flash->info(__('There is no student with Fx for ' . ($published_course->course->course_code_title ?? 'the selected course.') . ' course from ' . ($published_course->section->name ?? '') . ' section.'));
            }

            $program_id = $published_course->program_id;
            $program_type_id = $published_course->program_type_id;
            $academic_year_selected = $published_course->academic_year;
            $semester_selected = $published_course->semester;
            $department_id = !empty($published_course->college_id) ? 'c~' . $published_course->college_id : $published_course->department_id;
            $college_id = $published_course->college_id ?? null;

            $this->request = $this->request->withData('ExamGrade.department_id', $department_id);
        }

        $applicable_grades = [
            '' => '[ Select Grade ]',
            'I' => 'I (Incomplete)',
            'DO' => 'DO (Dropout)',
            'W' => 'W (Withdraw)'
        ];

        $this->set(compact(
            'publishedCourses',
            'programs',
            'program_types',
            'departments',
            'published_course_combo_id',
            'department_combo_id',
            'students_with_ng',
            'applicable_grades',
            'program_id',
            'program_type_id',
            'department_id',
            'academic_year_selected',
            'semester_selected'
        ));
    }

    protected function initExamGradeSearchFiltersPid()
    {
        if (!empty($this->request->getData('ExamGrade'))) {
            $search_filters = [
                'ExamGrade' => [
                    'academic_year' => $this->request->getData('ExamGrade.academic_year'),
                    'semester' => $this->request->getData('ExamGrade.semester'),
                    'program_id' => $this->request->getData('ExamGrade.program_id'),
                    'program_type_id' => $this->request->getData('ExamGrade.program_type_id')
                ]
            ];

            if (!empty($this->request->getData('ExamGrade.published_course_id'))) {
                $search_filters['ExamGrade']['published_course_id'] = $this->request->getData('ExamGrade.published_course_id');
            }

            if (!empty($this->request->getData('ExamGrade.department_id'))) {
                $search_filters['ExamGrade']['department_id'] = $this->request->getData('ExamGrade.department_id');
            }

            if (!empty($this->request->getData('ExamGrade.college_id'))) {
                $search_filters['ExamGrade']['college_id'] = $this->request->getData('ExamGrade.college_id');
            }

            $this->request = $this->request->withData('ExamGrade', $search_filters['ExamGrade']);
            $this->request->getSession()->write('exam_grade_search_filters_pid', $this->request->getData('ExamGrade'));
        } elseif ($this->request->getSession()->check('exam_grade_search_filters_pid')) {
            $this->request = $this->request->withData('ExamGrade', $this->request->getSession()->read('exam_grade_search_filters_pid'));
        }
    }

    public function add()
    {
        if (!empty($this->request->getData())) {
            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $examGrade = $examGradeTable->newEntity($this->request->getData());

            if ($examGradeTable->save($examGrade)) {
                $this->Flash->success('The exam grade has been saved');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The exam grade could not be saved. Please, try again.'));
            }
        }

        $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $makeupExamTable = TableRegistry::getTableLocator()->get('MakeupExams');
        $courseAddTable = TableRegistry::getTableLocator()->get('CourseAdds');

        $courseRegistrations = $courseRegistrationTable->find('list')->toArray();
        $makeupExams = $makeupExamTable->find('list')->toArray();
        $courseAdds = $courseAddTable->find('list')->toArray();

        $this->set(compact('courseRegistrations', 'makeupExams', 'courseAdds'));
    }

    public function edit($id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->Flash->error(__('Invalid exam grade'));
            return $this->redirect(['action' => 'index']);
        }

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');

        if (!empty($this->request->getData())) {
            $examGrade = $examGradeTable->get($id);
            $examGrade = $examGradeTable->patchEntity($examGrade, $this->request->getData());

            if ($examGradeTable->save($examGrade)) {
                $this->Flash->success(__('The exam grade has been saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The exam grade could not be saved. Please, try again.'));
            }
        }

        if (empty($this->request->getData())) {
            $this->request = $this->request->withData($examGradeTable->get($id)->toArray());
        }

        $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddTable = TableRegistry::getTableLocator()->get('CourseAdds');

        $courseRegistrations = $courseRegistrationTable->find('list')->toArray();
        $courseAdds = $courseAddTable->find('list')->toArray();

        $this->set(compact('courseRegistrations', 'courseAdds'));
    }

    public function delete($id = null, $action_controller_id = null)
    {
        $exam_grade = !empty($action_controller_id) ? explode('~', $action_controller_id) : [];

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $examGrade = $examGradeTable->find()->where(['ExamGrades.id' => $id])->first();

        if (!$examGrade) {
            $this->Flash->error('Invalid id for exam grade');
            if (!empty($exam_grade[0]) && !empty($exam_grade[1]) && !empty($exam_grade[2])) {
                return $this->redirect(['controller' => $exam_grade[1], 'action' => $exam_grade[0], $exam_grade[2]]);
            } elseif (!empty($exam_grade[0]) && !empty($exam_grade[1])) {
                return $this->redirect(['controller' => $exam_grade[1], 'action' => $exam_grade[0]]);
            }
            return $this->redirect(['action' => 'index']);
        }

        $check_not_involved_approved_by_department = $examGradeTable->find()
            ->where([
                'ExamGrades.id' => $id,
                'ExamGrades.registrar_approval IS' => null,
                'ExamGrades.department_approval IS' => null
            ])
            ->count();

        if ($check_not_involved_approved_by_department == 0) {
            if ($examGradeTable->delete($examGrade)) {
                $this->Flash->success('Exam grade deleted.');
                if (!empty($exam_grade[0]) && !empty($exam_grade[1]) && !empty($exam_grade[2])) {
                    return $this->redirect(['controller' => $exam_grade[1], 'action' => $exam_grade[0], $exam_grade[2]]);
                } elseif (!empty($exam_grade[0]) && !empty($exam_grade[1])) {
                    return $this->redirect(['controller' => $exam_grade[1], 'action' => $exam_grade[0]]);
                }
                return $this->redirect(['action' => 'index']);
            }
        }

        $this->Flash->error('Exam grade is not deleted.');
        return $this->redirect(['action' => 'index']);
    }

    public function approveFreshmanGradeSubmission($published_course_id = null)
    {
        $this->approveGradeSubmission($published_course_id, 0);
        $this->render('approve_grade_submission');
    }

    public function approveNonFreshmanGradeSubmission($published_course_id = null)
    {
        $this->approveGradeSubmission($published_course_id, 1);
        $this->render('approve_grade_submission');
    }

    protected function approveGradeSubmission($published_course_id = null, $department = 1)
    {
        if (!empty($published_course_id)) {
            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $check = $department == 1
                ? $publishedCourseTable->find()
                    ->where([
                        'PublishedCourses.id' => $published_course_id,
                        'PublishedCourses.given_by_department_id IN' => $this->department_ids
                    ])
                    ->count()
                : $publishedCourseTable->find()
                    ->where([
                        'PublishedCourses.id' => $published_course_id,
                        'PublishedCourses.college_id IN' => $this->college_ids
                    ])
                    ->count();

            if ($check == 0) {
                $this->Flash->error('You are not eligible to approve the selected course grades.');
                return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
            }

            $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $get_list_of_students_with_grade = $courseRegistrationTable->PublishedCourses->getStudentsTakingPublishedCourse($published_course_id);

            $publishedCourseDetail = $publishedCourseTable->find()
                ->where(['PublishedCourses.id' => $published_course_id])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'GivenByDepartments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'type']],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                    ]
                ])
                ->select(['id', 'academic_year', 'semester'])
                ->first();

            $this->request = $this->request->withData('Search.academic_year', $publishedCourseDetail->academic_year);

            $hide_approve_list = true;
            $turn_off_search = true;

            $gradeScaleDetail = $courseRegistrationTable->PublishedCourses->getGradeScaleDetail($published_course_id);
            $instructorDetail = $courseRegistrationTable->PublishedCourses->getInstructorDetailGivingPublishedCourse($published_course_id);

            $examTypeTable = TableRegistry::getTableLocator()->get('ExamTypes');
            $exam_types = $examTypeTable->find()
                ->where(['ExamTypes.published_course_id' => $published_course_id])
                ->order(['order' => 'ASC'])
                ->select(['id', 'exam_name', 'percent', 'order'])
                ->toArray();

            $this->set(compact(
                'get_list_of_students_with_grade',
                'hide_approve_list',
                'gradeScaleDetail',
                'instructorDetail',
                'publishedCourseDetail',
                'exam_types',
                'published_course_id',
                'turn_off_search'
            ));
        }

        if (!empty($this->request->getData()) && !empty($this->request->getData('approvegradesubmission'))) {
            $approval = $this->request->getData('ExamGrade.department_approval');
            $reason = $this->request->getData('ExamGrade.department_reason');

            $this->request = $this->request->withData('ExamGrade.department_approval', null);
            $this->request = $this->request->withData('ExamGrade.department_reason', null);

            $reformat_approve_grade = [];
            $count = 0;
            $any_exam_grade_id = '';
            $registrar_rejection = 0;

            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');

            if (!empty($this->request->getData('ExamGrade'))) {
                foreach ($this->request->getData('ExamGrade') as $exam_grade_value) {
                    $exam_grade_detail = $examGradeTable->find()
                        ->where(['ExamGrades.id' => $exam_grade_value['id']])
                        ->first();

                    if ($exam_grade_detail->registrar_approval == -1) {
                        $registrar_rejection = 1;
                        $any_exam_grade_id = $exam_grade_detail->id;

                        unset(
                            $exam_grade_detail->id,
                            $exam_grade_detail->registrar_approval,
                            $exam_grade_detail->registrar_reason,
                            $exam_grade_detail->registrar_approval_date,
                            $exam_grade_detail->registrar_approved_by,
                            $exam_grade_detail->created,
                            $exam_grade_detail->modified
                        );

                        $exam_grade_detail->department_reply = 1;
                        $exam_grade_detail->department_approval = $approval;
                        $exam_grade_detail->department_reason = $reason;
                        $exam_grade_detail->department_approval_date = Time::now();
                        $exam_grade_detail->department_approved_by = $this->Auth->user('id');
                        $reformat_approve_grade['ExamGrade'][$count] = $exam_grade_detail->toArray();
                    } else {
                        $any_exam_grade_id = $exam_grade_value['id'];
                        $reformat_approve_grade['ExamGrade'][$count] = [
                            'id' => $exam_grade_value['id'],
                            'department_approval' => $approval,
                            'department_reason' => $reason,
                            'department_approved_by' => $this->Auth->user('id'),
                            'department_approval_date' => Time::now()
                        ];
                    }
                    $count++;
                }
            }

            if (!empty($reformat_approve_grade['ExamGrade'])) {
                $entities = $examGradeTable->newEntities($reformat_approve_grade['ExamGrade'], ['validate' => false]);
                if ($examGradeTable->saveMany($entities)) {
                    $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                    $course_instructor = $courseRegistrationTable->PublishedCourses->getInstructorByExamGradeId($any_exam_grade_id);
                    $course = $courseRegistrationTable->PublishedCourses->Courses->getCourseByExamGradeId($any_exam_grade_id);
                    $section = $courseRegistrationTable->PublishedCourses->Sections->getSectionByExamGradeId($any_exam_grade_id);
                    $published_course = $courseRegistrationTable->PublishedCourses->getPublishedCourseByExamGradeId($any_exam_grade_id);

                    if (!empty($course_instructor) && !empty($course_instructor['user_id'])) {
                        $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                        $message = sprintf(
                            'Your <u>%s (%s)</u> grade submission is %s by the %s for <u>%s</u> section. <a href="/exam_results/add/%s">View Grade</a>',
                            $course['course_title'],
                            $course['course_code'],
                            $approval == 1 ? 'approved' : 'rejected',
                            $department == 1 ? 'department' : 'freshman program',
                            $section['name'],
                            $published_course['id']
                        );

                        $message = sprintf(
                            '<p style="text-align:justify; padding:0px; margin:0px" class="%s">%s</p>',
                            $approval == -1 ? 'rejected' : 'accepted',
                            $message
                        );

                        $auto_message = $autoMessageTable->newEntity([
                            'message' => $message,
                            'read' => 0,
                            'user_id' => $course_instructor['user_id']
                        ]);

                        $autoMessageTable->save($auto_message);
                    }

                    if ($approval && $registrar_rejection) {
                        $this->Flash->success('The exam grade has been rejected and sent back to the registrar stating the grades are correct. The system will notify registrar to confirm the result.');
                    } elseif ($approval == -1) {
                        $this->Flash->warning('The exam grade has been rejected and sent back to the instructor for re-consideration. The system will notify the assigned instructor to check the result and re-submit again.');
                    } else {
                        $this->Flash->success('The exam grade has been approved. The system will notify registrar to confirm the result.');
                    }

                    return $this->redirect(['action' => $department == 1 ? 'approveNonFreshmanGradeSubmission' : 'approveFreshmanGradeSubmission']);
                } else {
                    $this->Flash->error('The exam grade approval could not be completed. Please, try again.');
                }
            } else {
                $this->Flash->error('No Exam Grade selected to approve. Please select at least one.');
            }
        }

        $this->initSearch();

        $defaultAcademicYear = !empty($this->request->getData('Search.academic_year'))
            ? $this->request->getData('Search.academic_year')
            : $this->AcademicYear->currentAcademicYear();

        if (!empty($this->request->getData()) && is_null($published_course_id)) {
            $everythingFine = empty($this->request->getData('Search.academic_year')) ? false : true;

            if (!$everythingFine) {
                $this->request = $this->request->withData('Search.academic_year', $defaultAcademicYear);
                $this->Flash->error('Please select the academic year you want to approve grade submission.');
            } else {
                $selected_academic_year = $this->request->getData('Search.academic_year') ?? $defaultAcademicYear;
                $selected_programs = $this->request->getData('Search.program_id') ?? $this->program_type_ids;
                $selected_program_types = $this->request->getData('Search.program_type_id') ?? $this->program_type_ids;
                $selected_semester = $this->request->getData('Search.semester') ?? '';
                $selected_year_levels = $this->request->getData('Search.year_level_id') ?? $this->getDefaultYearLevels();

                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $published_course_list_student_registered = $department == 1
                    ? $examGradeTable->getRejectedOrNonApprovedPublishedCourseList2(
                        $this->department_id,
                        $selected_academic_year,
                        $selected_semester,
                        $selected_year_levels,
                        $selected_programs,
                        $selected_program_types,
                        null,
                        $this->role_id,
                        0
                    )
                    : $examGradeTable->getRejectedOrNonApprovedPublishedCourseList2(
                        $this->college_id,
                        $selected_academic_year,
                        $selected_semester,
                        $selected_year_levels,
                        $selected_programs,
                        $selected_program_types,
                        null,
                        $this->role_id,
                        1
                    );

                if (!empty($published_course_list_student_registered)) {
                    $grade_submitted_courses_organized_by_published_course = $this->organizePublishedCourses($published_course_list_student_registered);
                    $this->set('turn_off_search', true);
                    $this->set(compact('grade_submitted_courses_organized_by_published_course'));
                } else {
                    $this->set('turn_off_search', false);
                    $this->Flash->info("There is no grade submission for {$defaultAcademicYear} academic year that needs your approval for now. You can change the filters and check other academic year grade submissions which are prior to {$defaultAcademicYear}.");
                }

                $this->set(compact(
                    'grade_submitted_courses_organized_by_published_course',
                    'department'
                ));
            }
        }

        $this->setDepartmentAndCollegeLists();

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $programs = $programTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();
        $programTypes = $programTypeTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $this->set(compact(
            'grade_submitted_courses_organized_by_published_course',
            'programs',
            'programTypes',
            'defaultAcademicYear',
        ));
    }

    public function confirmGradeSubmission($published_course_id = null)
    {
        $section_prog_id = '';
        $section_prog_type_id = '';

        if (!empty($published_course_id)) {
            $check1 = 1;
            $check2 = 1;
            $any_exam_grade_id = '';

            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            if (!empty($this->department_ids)) {
                $check1 = $publishedCourseTable->find()
                    ->where([
                        'PublishedCourses.id' => $published_course_id,
                        'PublishedCourses.department_id IN' => $this->department_ids
                    ])
                    ->count();
            }

            if (!empty($this->college_ids)) {
                $check2 = $publishedCourseTable->find()
                    ->where([
                        'PublishedCourses.id' => $published_course_id,
                        'PublishedCourses.college_id IN' => $this->college_ids
                    ])
                    ->count();
            }

            if ($check1 == 0 || $check2 == 0) {
                $this->Flash->error('You are not eligible to approve the selected course grades.');
                return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
            }

            $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $get_list_of_students_with_grade = $courseRegistrationTable->PublishedCourses->getStudentsTakingPublishedCourse($published_course_id);

            $publishedCourseDetail = $publishedCourseTable->find()
                ->select(['id', 'academic_year', 'semester', 'program_id', 'program_type_id'])
                ->where(['PublishedCourses.id' => $published_course_id])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'GivenByDepartments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'type']],
                    'Courses' => [
                        'fields' => ['id', 'course_title', 'course_code', 'credit'],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                    ]
                ])
                ->first();

            $this->request = $this->request->withData('Search.academic_year', $publishedCourseDetail->academic_year);

            $section_prog_id = $publishedCourseDetail->program_id;
            $section_prog_type_id = $publishedCourseDetail->program_type_id;

            $hide_approve_list = true;
            $search_published_course = true;
            $turn_off_search = true;

            $gradeScaleDetail = $courseRegistrationTable->PublishedCourses->getGradeScaleDetail($published_course_id);
            $instructorDetail = $courseRegistrationTable->PublishedCourses->getInstructorDetailGivingPublishedCourse($published_course_id);

            $this->set(compact(
                'get_list_of_students_with_grade',
                'hide_approve_list',
                'search_published_course',
                'gradeScaleDetail',
                'instructorDetail',
                'publishedCourseDetail',
                'turn_off_search'
            ));
        }

        if (!empty($this->request->getData()) && !empty($this->request->getData('confirmgradesubmission'))) {
            $confirmed = $this->request->getData('ExamGrade.registrar_approval') == 1 ? 1 : 0;
            $reason = $this->request->getData('ExamGrade.registrar_reason');
            $approval = $this->request->getData('ExamGrade.registrar_approval');

            $this->request = $this->request->withData('ExamGrade.registrar_approval', null);
            $this->request = $this->request->withData('ExamGrade.registrar_reason', null);

            $reformat_approve_grade = [];
            $approved_exam_grades = [];
            $count = 0;

            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');

            if (!empty($this->request->getData('ExamGrade'))) {
                foreach ($this->request->getData('ExamGrade') as $exam_grade_value) {
                    $any_exam_grade_id = $exam_grade_value['id'];
                    $reformat_approve_grade['ExamGrade'][$count] = [
                        'id' => $exam_grade_value['id'],
                        'registrar_approval' => $approval,
                        'registrar_approved_by' => $this->Auth->user('id'),
                        'registrar_reason' => $reason,
                        'registrar_approval_date' => Time::now()
                    ];
                    $approved_exam_grades[] = $exam_grade_value['id'];
                    $count++;
                }
            }

            if (!empty($reformat_approve_grade['ExamGrade'])) {
                $entities = $examGradeTable->newEntities($reformat_approve_grade['ExamGrade'], ['validate' => false]);
                if ($examGradeTable->saveMany($entities)) {
                    $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                    $autoMessageTable->sendNotificationOnRegistrarGradeConfirmation($reformat_approve_grade['ExamGrade']);

                    $published_course_search = $examGradeTable->find()
                        ->where(['ExamGrades.id' => $reformat_approve_grade['ExamGrade'][0]['id']])
                        ->contain([
                            'CourseRegistrations' => ['PublishedCourses'],
                            'CourseAdds' => ['PublishedCourses']
                        ])
                        ->first();

                    debug($published_course_search);

                    $published_course_id2 = !empty($published_course_search->course_registration)
                        ? $published_course_search->course_registration->published_course->id
                        : $published_course_search->course_add->published_course->id;

                    $result = $examGradeTable->CourseRegistrations->Students->StudentExamStatuses->updateAcdamicStatusByPublishedCourse($published_course_id2);

                    if (defined('GRADE_NOTIFICATION_FOR_STUDENTS_SYSTEM_WIDE_ENABLED') && GRADE_NOTIFICATION_FOR_STUDENTS_SYSTEM_WIDE_ENABLED && $confirmed && !empty($approved_exam_grades)) {
                        debug($approved_exam_grades);
                        if (!empty($section_prog_id) && !empty($section_prog_type_id)) {
                            $generalSettingTable = TableRegistry::getTableLocator()->get('GeneralSettings');
                            $generalSettings = $generalSettingTable->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(null, $section_prog_id, $section_prog_type_id, null);
                            debug($generalSettings);
                            debug($generalSettings->notifyStudentsGradeByEmail);

                            if (!empty($generalSettings) && $generalSettings->notifyStudentsGradeByEmail) {
                                debug($this->attachGradeToEmail($approved_exam_grades));
                            }
                        }
                    }

                    if ($result) {
                        $this->Flash->success($confirmed ? 'Exam grade submission confirmed successfully.' : 'Exam grade submission is rejected and sent back to department for re-consideration.');
                    } else {
                        $this->Flash->warning('Exam grade submission confirmed successfully but student academic status is not generated. Please regenerate student academic status manually if this exam grade submission is the last submitted grade of the section for the semester.');
                    }
                    return $this->redirect(['action' => 'confirmGradeSubmission']);
                } else {
                    $this->Flash->error('The exam grade submission could not be approved. Please, try again.');
                }
            } else {
                $this->Flash->error('No Exam Grade submission is selected to approve. Please select one.');
            }
        }

        $this->initSearch();

        $defaultAcademicYear = !empty($this->request->getData('Search.academic_year'))
            ? $this->request->getData('Search.academic_year')
            : $this->AcademicYear->currentAcademicYear();

        if (!empty($this->request->getData()) && is_null($published_course_id)) {
            $everythingFine = empty($this->request->getData('Search.academic_year')) ? false : true;

            if (!$everythingFine) {
                $this->request = $this->request->withData('Search.academic_year', $defaultAcademicYear);
                $this->Flash->error('Please select the academic year you want to confirm the grade submission.');
            } else {
                $selected_academic_year = $this->request->getData('Search.academic_year') ?? $defaultAcademicYear;
                $selected_programs = $this->request->getData('Search.program_id') ?? $this->program_ids;
                $selected_program_types = $this->request->getData('Search.program_type_id') ?? $this->program_type_ids;
                $selected_semester = $this->request->getData('Search.semester') ?? '';

                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $published_course_list_student_registered = !empty($this->department_ids)
                    ? $examGradeTable->getRegistrarNonApprovedCoursesList2(
                        $this->department_ids,
                        null,
                        $selected_academic_year,
                        $selected_semester,
                        $selected_programs,
                        $selected_program_types,
                        null
                    )
                    : $examGradeTable->getRegistrarNonApprovedCoursesList2(
                        null,
                        $this->college_ids,
                        $selected_academic_year,
                        $selected_semester,
                        $selected_programs,
                        $selected_program_types,
                        null
                    );

                if (!empty($published_course_list_student_registered)) {
                    $grade_submitted_courses_organized_by_published_course = $this->organizePublishedCourses($published_course_list_student_registered);
                    $this->set('turn_off_search', true);
                    $this->set(compact('grade_submitted_courses_organized_by_published_course'));
                } else {
                    $this->set('turn_off_search', false);
                    $this->Flash->info("There is no grade submission for {$selected_academic_year} academic year" . (!empty($selected_semester) ? " Semester {$selected_semester}" : ' in the given criteria') . " that needs your confirmation for now. You can change the filters and check other academic year grade submissions which are prior to {$selected_academic_year}.");
                }
            }
        }

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $programs = $programTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();
        $programTypes = $programTypeTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $this->setDepartmentAndCollegeLists();

        $this->set(compact(
            'grade_submitted_courses_organized_by_published_course',
            'programs',
            'programTypes',
            'defaultAcademicYear',
        ));
    }

    protected function initSearch()
    {
        if (!empty($this->request->getData('Search'))) {
            $this->request->getSession()->write('search_data', $this->request->getData('Search'));
        } elseif ($this->request->getSession()->check('search_data')) {
            $this->request = $this->request->withData('Search', $this->request->getSession()->read('search_data'));
        }
    }

    protected function attachGradeToEmail($exam_grade_ids = null)
    {
        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $detail = $examGradeTable->find()
            ->where(['ExamGrades.id IN' => $exam_grade_ids])
            ->select(['ExamGrades.id', 'ExamGrades.grade', 'ExamGrades.course_registration_id', 'ExamGrades.course_add_id'])
            ->contain([
                'CourseRegistrations' => [
                    'fields' => ['id', 'published_course_id'],
                    'Students' => [
                        'fields' => ['id', 'full_name', 'first_name', 'email'],
                        'Users' => ['fields' => ['id', 'email', 'email_verified']]
                    ],
                    'PublishedCourses' => [
                        'fields' => ['id'],
                        'Courses' => ['fields' => ['course_code', 'course_title', 'credit']]
                    ]
                ],
                'CourseAdds' => [
                    'fields' => ['id', 'published_course_id'],
                    'Students' => [
                        'fields' => ['id', 'full_name', 'first_name', 'email'],
                        'Users' => ['fields' => ['id', 'email', 'email_verified']]
                    ],
                    'PublishedCourses' => [
                        'fields' => ['id'],
                        'Courses' => ['fields' => ['course_code', 'course_title', 'credit']]
                    ]
                ]
            ])
            ->toArray();

        if (!empty($detail)) {
            $subject = "Examination Result";
            foreach ($detail as $value) {
                if (
                    (!empty($value->course_registration->student->user->email) && !empty($value->course_registration->student->user->email_verified) && (int)$value->course_registration->student->user->email_verified) ||
                    (!empty($value->course_add->student->user->email) && !empty($value->course_add->student->user->email_verified) && (int)$value->course_add->student->user->email_verified)
                ) {
                    $email = !empty($value->course_registration->student->user->email)
                        ? $value->course_registration->student->user->email
                        : $value->course_add->student->user->email;
                    $body = sprintf(
                        'Dear %s, the grade you have got for %s is %s',
                        !empty($value->course_registration->student->first_name) ? $value->course_registration->student->first_name : $value->course_add->student->first_name,
                        !empty($value->course_registration->published_course->course->course_title)
                            ? $value->course_registration->published_course->course->course_title . ' (' . $value->course_registration->published_course->course->course_code . ')'
                            : $value->course_add->published_course->course->course_title . ' (' . $value->course_add->published_course->course->course_code . ')',
                        $value->grade
                    );
                    $this->sendGradeNotification($email, $subject, $body, $value->course_registration->student->id ?? $value->course_add->student->id);
                }
            }
        }
    }

    protected function sendGradeNotification($email = null, $subject = null, $body = null, $student_id = null)
    {
        $sent = false;
        $auth = $this->request->getSession()->read('Auth.User');
        $from = $auth['id'];
        $contentOfEmail = null;

        if (!empty($email)) {
            if ($this->sendEmail('grade_notification', $subject, $email, $body, $student_id)) {
                $contentOfEmail = "To: {$email}\nSubject: {$subject}\n{$this->getEmailReturnAddress()}\n--content--\n{$body}\n";
                $mailerTable = TableRegistry::getTableLocator()->get('Mailers');
                $message = [
                    'from' => $from,
                    'subject' => $subject,
                    'content' => $contentOfEmail,
                    'model' => 'ExamGrade'
                ];
                $mailerTable->logMessage($message);
                $sent = true;
            }
        }
        return $sent;
    }

    protected function getEmailReturnAddress()
    {
        $from = defined('EMAIL_DEFAULT_FROM') ? EMAIL_DEFAULT_FROM : 'no-reply@example.com';
        $replyTo = defined('EMAIL_DEFAULT_REPLY_TO') ? EMAIL_DEFAULT_REPLY_TO : 'no-reply@example.com';
        $returnPath = defined('EMAIL_DEFAULT_RETURN_PATH') ? EMAIL_DEFAULT_RETURN_PATH : 'no-reply@example.com';
        return "From: {$from}\nReply-To: {$replyTo}\nReturn-Path: {$returnPath}";
    }

    protected function attachNameToEmail($student_id = null)
    {
        if ($student_id) {
            $studentTable = TableRegistry::getTableLocator()->get('Students');
            $students = $studentTable->find()
                ->where(['Students.id' => $student_id])
                ->contain(['Users' => ['fields' => ['email_verified']]])
                ->first();

            if (!empty($students) && !empty($students->user->email_verified)) {
                $this->set('firstname', $students->first_name);
                $this->set('lastname', $students->middle_name);
                return true;
            }
        }
        return false;
    }

    protected function sendEmail($templateName, $emailSubject, $to, $body, $student_id, $from = EMAIL_DEFAULT_FROM, $replyToEmail = EMAIL_DEFAULT_REPLY_TO, $return = EMAIL_DEFAULT_RETURN_PATH, $sendAs = 'both')
    {
        if (!$this->attachNameToEmail($student_id)) {
            return false;
        }

        $this->set('message', $body);

        $email = new Email();
        $email
            ->setTemplate($templateName)
            ->setEmailFormat($sendAs)
            ->setFrom($from ?? 'no-reply@example.com')
            ->setTo($to)
            ->setSubject($emailSubject)
            ->setReplyTo($replyToEmail ?? 'no-reply@example.com')
            ->setReturnPath($return ?? 'no-reply@example.com')
            ->setViewVars(['message' => $body]);

        try {
            return $email->send();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function studentGradeView($ay1 = null, $ay2 = null, $semester = null)
    {
        $studentEvaluationRateTable = TableRegistry::getTableLocator()->get('StudentEvaluationRates');
        $notEvaluatedList = $studentEvaluationRateTable->getNotEvaluatedRegisteredCourse($this->student_id);

        $generalSettingTable = TableRegistry::getTableLocator()->get('GeneralSettings');
        if (!$generalSettingTable->allowStudentsGradeViewWithoutInstructorsEvaluation($this->student_id) && !empty($notEvaluatedList)) {
            return $this->redirect(['controller' => 'StudentEvaluationRates', 'action' => 'add']);
        }

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $student_ay_s_list = $examGradeTable->getListOfAyAndSemester($this->student_id);
        $academic_years = [];

        if (!empty($student_ay_s_list)) {
            foreach ($student_ay_s_list as $ay_s) {
                $academic_years[$ay_s['academic_year']] = $ay_s['academic_year'];
            }
        }

        if (!empty($ay1) && !empty($ay2) && !empty($semester)) {
            $this->request = $this->request->withData('ExamGrade.academic_year', str_replace('-', '/', $ay1));
            $this->request = $this->request->withData('ExamGrade.semester', $semester);
            $this->request = $this->request->withData('myGradeReport', true);
        }

        if (!empty($this->request->getData('myGradeReport'))) {
            $student_copy = $examGradeTable->getStudentCopy(
                $this->student_id,
                $this->request->getData('ExamGrade.academic_year'),
                $this->request->getData('ExamGrade.semester')
            );
        }

        $this->set(compact('academic_years', 'student_copy'));

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT && !empty($this->request->getSession()->read('Auth.User.id'))) {
            $studentStatusPatternTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');
            $isExitExamEligible = $studentStatusPatternTable->isEligibleForExitExam($this->student_id);

            $isNotProfilePage = strcasecmp($this->request->getParam('action'), 'profile') != 0;
            $isNotUsersPage = strcasecmp($this->request->getParam('controller'), 'users') != 0;
            $isNotChangePwdPage = strcasecmp($this->request->getParam('action'), 'changePwd') != 0;

            if (($isExitExamEligible || (defined('FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE') && FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE == 1)) && $isNotProfilePage && $isNotUsersPage && $isNotChangePwdPage) {
                if (!$studentStatusPatternTable->completedFillingProfileInformation($this->student_id)) {
                    $this->Flash->warning('Dear ' . $this->request->getSession()->read('Auth.User.first_name') . ', before proceeding, you must complete your basic profile. If you encounter an error, are unable to update your profile on your own, or require further assistance, please report to the registrar record officer assigned to your department.');
                    return $this->redirect(['controller' => 'Students', 'action' => 'profile']);
                }
            }

            $studentTable = TableRegistry::getTableLocator()->get('Students');
            $studentDetails = $studentTable->find()
                ->where(['Students.id' => $this->student_id])
                ->select(['studentnumber', 'country_id', 'faida_identification_number', 'faida_alias_number'])
                ->first();

            $isEthiopianStudent = !empty($studentDetails->country_id) && (int)$studentDetails->country_id == COUNTRY_ID_OF_ETHIOPIA;
            $isFaidaFinFilled = !empty($studentDetails->faida_identification_number);
            $isFaidaFanFilled = !empty($studentDetails->faida_alias_number);

            if ($isEthiopianStudent && (!$isFaidaFinFilled || !$isFaidaFanFilled) && ($isExitExamEligible || (defined('FORCE_ALL_STUDENTS_TO_FILL_FAIDA_FIN') && FORCE_ALL_STUDENTS_TO_FILL_FAIDA_FIN == 1)) && $isNotProfilePage && $isNotUsersPage && $isNotChangePwdPage) {
                $message = 'Dear ' . $this->request->getSession()->read('Auth.User.first_name') . ', before proceeding, you must update your ';
                if (!$isFaidaFinFilled && !$isFaidaFanFilled) {
                    $message .= 'Fayda Identification Number (FIN) and Fayda Alias Number (FAN). Ensure that you provide the correct 16-digit FAN, located on the front, and the 12-digit FIN, found on the back of your national Fayda ID card.';
                } elseif (!$isFaidaFinFilled) {
                    $message .= 'Fayda Identification Number (FIN). Please ensure that you provide the correct 12-digit FIN, located on the back of your national Fayda ID card.';
                } else {
                    $message .= 'Fayda Alias Number (FAN). Please ensure that you provide the correct 16-digit FAN, located on the front of your national Fayda ID card.';
                }
                $this->Flash->info($message);
                return $this->redirect(['controller' => 'Students', 'action' => 'profile']);
            }
        }
    }

    public function departmentGradeView($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
    {
        if (!empty($this->request->getData())) {
            $this->viewGrade(null, $type, null, null, null, 'department');
        } else {
            $this->viewGrade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'department');
        }
    }

    protected function getDefaultYearLevels()
    {
        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        $yearLevelTable = TableRegistry::getTableLocator()->get('YearLevels');

        $programs = $programTable->find('list')
            ->where(['Programs.active' => 1])
            ->toArray();

        $depts_for_year_level = $departmentTable->find('list')
            ->where(['Departments.active' => 1])
            ->toArray();

        return $yearLevelTable->distinctYearLevelBasedOnRole(
            null,
            null,
            array_keys($depts_for_year_level),
            array_keys($programs)
        );
    }

    protected function organizePublishedCourses($published_course_list_student_registered)
    {
        $grade_submitted_courses_organized_by_published_course = [];

        foreach ($published_course_list_student_registered as $value) {
            $year_level_name = $value['YearLevel']['name'] ?? 'Pre/1st';
            $department_id = $value['Department']['id'] ?? 0;
            $college_id = $value['College']['id'] ?? null;

            if (is_numeric($department_id) && $department_id > 0) {
                $grade_submitted_courses_organized_by_published_course[$department_id][$value['Program']['name']][$value['ProgramType']['name']][$year_level_name][$value['Section']['name']][$value['PublishedCourse']['id']] = $value;
            } elseif (is_numeric($college_id) && $college_id > 0) {
                $grade_submitted_courses_organized_by_published_course['c~' . $college_id][$value['Program']['name']][$value['ProgramType']['name']][$year_level_name][$value['Section']['name']][$value['PublishedCourse']['id']] = $value;
            }
        }

        return $grade_submitted_courses_organized_by_published_course;
    }

    protected function setDepartmentAndCollegeLists()
    {
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        $collegeTable = TableRegistry::getTableLocator()->get('Colleges');

        if (!empty($this->department_ids) && $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
            $departments = $departmentTable->find('list')
                ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                ->toArray();
            $this->set(compact('departments'));
        } elseif (!empty($this->college_ids) && $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
            $colleges = $collegeTable->find('list')
                ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                ->toArray();
            $this->set(compact('colleges'));
        } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE) {
            $departments = $departmentTable->find('list')
                ->where(['Departments.college_id IN' => $this->college_ids, 'Departments.active' => 1])
                ->toArray();
            $this->set(compact('departments'));
        } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT) {
            $departments = $departmentTable->find('list')
                ->where(['Departments.active' => 1])
                ->toArray();
            $yearLevels = $this->getDefaultYearLevels();
            $this->set(compact('departments', 'yearLevels'));
        }

        $departmentsss = $departmentTable->find('list')
            ->where(['Departments.active' => 1])
            ->toArray();
        $collegesss = $collegeTable->find('list')
            ->where(['Colleges.active' => 1])
            ->toArray();

        $this->set(compact('departmentsss', 'collegesss'));
    }


    public function freshmanGradeView($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
    {
        if (!empty($this->request->getData())) {
            $this->viewGrade(null, $type, null, null, null, 'freshman');
        } else {
            $this->viewGrade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'freshman');
        }
    }

    public function collegeGradeView($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
    {
        if (!empty($this->request->getData())) {
            $this->viewGrade(null, $type, null, null, null, 'college');
        } else {
            $this->viewGrade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'college');
        }
    }

    public function cheatingView()
    {
        if (!empty($this->request->getData()) && !empty($this->request->getData('viewCheatingStudentList'))) {
            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $studentsWithCheatingCases = $examGradeTable->CourseRegistrations->listOfStudentsWithNGToFWithCheating(
                $this->request->getData('ExamGrade.department_id'),
                $this->request->getData('ExamGrade.academic_year'),
                $this->request->getData('ExamGrade.semester'),
                $this->request->getData('ExamGrade.program_id'),
                $this->request->getData('ExamGrade.program_type_id'),
                0
            );

            if (empty($studentsWithCheatingCases)) {
                $this->Flash->error('There is no cheating result grade change recorded.');
            }
            $this->set(compact('studentsWithCheatingCases'));
        }

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');

        $programs = !empty($this->program_id)
            ? $programTable->find('list')
                ->where(['Programs.id' => $this->program_id])
                ->toArray()
            : $programTable->find('list')->toArray();

        $programTypes = !empty($this->program_type_id)
            ? $programTypeTable->find('list')
                ->where(['ProgramTypes.id' => $this->program_type_id])
                ->toArray()
            : $programTypeTable->find('list')->toArray();

        if (!empty($this->department_ids)) {
            $departments = $departmentTable->find('list')
                ->where(['Departments.id IN' => $this->department_ids])
                ->toArray();
        } elseif (!empty($this->department_id)) {
            $departments = $departmentTable->find('list')
                ->where(['Departments.id' => $this->department_id])
                ->toArray();
        }

        $this->set(compact('programs', 'departments'));
    }

    public function registrarGradeView($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null)
    {
        debug($section_or_published_course_id);
        if (!empty($this->request->getData())) {
            $this->viewGrade(null, $type, null, null, null, 'registrar');
        } else {
            $this->viewGrade($section_or_published_course_id, $type, $ay1, $ay2, $semester, 'registrar');
        }
    }

    protected function viewGrade($section_or_published_course_id = null, $type = 'pc', $ay1 = null, $ay2 = null, $semester = null, $who = 'registrar')
    {
        $published_course_combo_id = null;
        $department_combo_id = null;
        $publishedCourses = [];
        $students_with_ng = [];
        $have_message = false;

        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programs = $programTable->find('list')->toArray();

        debug($section_or_published_course_id);

        $grade_view_action = 'index';

        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        if (strcasecmp($who, 'registrar') == 0) {
            if ($this->request->getSession()->read('Auth.User.is_admin') == 1) {
                $departments = $departmentTable->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids, true, true);
            } else {
                $departments = $this->onlyPre
                    ? $departmentTable->onlyFreshmanInAllColleges($this->college_ids, 1)
                    : $departmentTable->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids, 1);
            }
            $grade_view_action = 'registrarGradeView';
        } elseif (strcasecmp($who, 'college') == 0) {
            $departments = $departmentTable->allCollegeDepartments($this->college_id, 1);
            $grade_view_action = 'collegeGradeView';
        } elseif (strcasecmp($who, 'department') == 0) {
            $departments = [0 => 0];
            $grade_view_action = 'departmentGradeView';
        } elseif (strcasecmp($who, 'freshman') == 0) {
            $departments = [0 => 0];
            $grade_view_action = 'freshmanGradeView';
        } else {
            $departments = [];
        }

        if (!empty($this->request->getData())) {
            if (strcasecmp($who, 'department') == 0) {
                $department_id = $this->department_id;
            } elseif (strcasecmp($who, 'freshman') == 0 || $this->onlyPre) {
                $department_id = empty($this->request->getData('ExamGrade.department_id')) && !empty($this->college_ids)
                    ? 'c~' . array_values($this->college_ids)[0]
                    : ($this->request->getData('ExamGrade.department_id') ?? 'c~' . $this->college_id);
            } else {
                $department_id = $this->request->getData('ExamGrade.department_id');
            }

            $department_combo_id = $department_id;
            $college_id = explode('~', $department_id);

            debug($college_id);

            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            if (is_array($college_id) && count($college_id) > 1) {
                $college_id = $college_id[1];
                $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesCollegeFreshTakingOrgBySection(
                    $college_id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester'),
                    $this->request->getData('ExamGrade.program_id'),
                    $this->request->getData('ExamGrade.program_type_id'),
                    1
                );
            } else {
                $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesSectionsTakingOrgBySection(
                    $department_id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester'),
                    $this->request->getData('ExamGrade.program_id'),
                    $this->request->getData('ExamGrade.program_type_id'),
                    1
                );
            }

            if (empty($publishedCourses)) {
                $this->Flash->info('No published courses is found with the given criteria.');
                return $this->redirect(['action' => $grade_view_action]);
            } else {
                $publishedCourses = [0 => '[ Select Published Course/Section ]'] + $publishedCourses;
            }

            debug($this->request->getData());
        }

        if (!empty($section_or_published_course_id)) {
            $published_course_id = $section_or_published_course_id;
            $section_detail = [];
            $published_course = [];

            if (strcasecmp($type, 'section') == 0) {
                $section_id = $section_or_published_course_id;
                debug($section_id);

                $sectionTable = TableRegistry::getTableLocator()->get('Sections');
                $section_detail = $sectionTable->find()
                    ->where(['Sections.id' => $section_id])
                    ->contain([
                        'Departments',
                        'Colleges',
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->first();

                $department_id = $section_detail->department->id ?? null;

                if (empty($department_id)) {
                    $college_id = $section_detail->college_id;
                    $section_college_id = $section_detail->college_id;
                } else {
                    $college_id = null;
                    $section_college_id = $section_detail->department->college_id;
                    if (!empty($department_id)) {
                        $privileged_department_ids[] = $department_id;
                    }
                }

                debug($department_id);

                $academic_year = "$ay1/$ay2";
                $program_id = $section_detail->program_id;
                $program_type_id = $section_detail->program_type_id;
                $published_course_combo_id = 's~' . $section_or_published_course_id;
            } else {
                $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                $published_course = $publishedCourseTable->find()
                    ->where(['PublishedCourses.id' => $section_or_published_course_id])
                    ->contain([
                        'Departments',
                        'GivenByDepartments',
                        'Colleges',
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']],
                        'Sections' => [
                            'Colleges',
                            'Departments',
                            'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                            'Programs' => ['fields' => ['id', 'name', 'shortname']],
                            'YearLevels' => ['fields' => ['id', 'name']]
                        ]
                    ])
                    ->first();

                if (!empty($published_course->department_id)) {
                    $privileged_department_ids[] = $published_course->department_id;
                }

                if (!empty($published_course->given_by_department_id)) {
                    $privileged_department_ids[] = $published_course->given_by_department_id;
                }

                $department_id = $published_course->department_id;
                $given_by_department_id = $published_course->given_by_department_id;
                $college_id = $published_course->college_id;
                $academic_year = $published_course->academic_year;
                $program_id = $published_course->program_id;
                $program_type_id = $published_course->program_type_id;
                $semester = $published_course->semester;
                $published_course_combo_id = $section_or_published_course_id;

                $section_college_id = $college_id ?? ($published_course->department->college_id ?? $published_course->given_by_department->college_id);

                if (!empty($section_college_id)) {
                    $privileged_department_ids[] = $section_college_id;
                }
            }

            $publishedCourses = [];
            debug($published_course);
            debug($section_detail);

            if ($this->request->getSession()->read('Auth.User.is_admin') == 1 && $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
                // Registrar admin, allow full access
            } elseif (
                (empty($published_course) && empty($section_detail)) ||
                (strcasecmp($who, 'registrar') == 0 && !empty($department_id) && !in_array($department_id, $this->department_ids)) ||
                (strcasecmp($who, 'registrar') == 0 && !empty($college_id) && !in_array($college_id, $this->college_ids)) ||
                (strcasecmp($who, 'college') == 0 && $section_college_id != $this->college_id) ||
                (strcasecmp($who, 'department') == 0 && !in_array($this->department_id, $privileged_department_ids)) ||
                (strcasecmp($who, 'freshman') == 0 && (!in_array($this->college_id, $privileged_department_ids)))
            ) {
                $this->Flash->error('Please select a valid published course or section.');
                return $this->redirect(['action' => $grade_view_action]);
            } else {
                $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                if (empty($department_id)) {
                    $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesCollegeFreshTakingOrgBySection(
                        $college_id,
                        $academic_year,
                        $semester,
                        $program_id,
                        $program_type_id,
                        1,
                        $this->department_id == $given_by_department_id ? $given_by_department_id : null
                    );
                    $department_combo_id = 'c~' . $college_id;
                } else {
                    $publishedCourses = $publishedCourseTable->CourseInstructorAssignments->listOfCoursesSectionsTakingOrgBySection(
                        $department_id,
                        $academic_year,
                        $semester,
                        $program_id,
                        $program_type_id,
                        1
                    );
                    $department_combo_id = $department_id;
                }
            }

            if (strcasecmp($type, 'section') != 0) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $student_course_register_and_adds = $examGradeTable->CourseRegistrations->PublishedCourses->getStudentsTakingPublishedCourse($section_or_published_course_id);
                $students = $student_course_register_and_adds['register'];
                $student_adds = $student_course_register_and_adds['add'];
                $student_makeup = $student_course_register_and_adds['makeup'];
                $grade_submission_status = $examGradeTable->CourseAdds->ExamResults->getExamGradeSubmissionStatus($section_or_published_course_id, $student_course_register_and_adds);

                $section_and_course_detail = $publishedCourseTable->find()
                    ->where(['PublishedCourses.id' => $section_or_published_course_id])
                    ->contain([
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']],
                        'GivenByDepartments',
                        'Departments',
                        'Colleges',
                        'Sections' => [
                            'Colleges',
                            'Departments',
                            'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                            'Programs' => ['fields' => ['id', 'name', 'shortname']],
                            'YearLevels' => ['fields' => ['id', 'name']]
                        ],
                        'Courses'
                    ])
                    ->first();

                $section_detail = $section_and_course_detail->section;
                $course_detail = $section_and_course_detail->course;
                $view_only = true;
                $display_grade = true;
                $grade_view_only = true;
                $exam_types = [];

                $program_id = $section_and_course_detail->section->program_id;
                $program_type_id = $section_and_course_detail->section->program_type_id;
                $department_id = $section_and_course_detail->section->department_id;
                $academic_year_selected = $academic_year;
                $semester_selected = $semester;

                $this->set(compact(
                    'published_course_id',
                    'publishedCourses',
                    'programs',
                    'program_types',
                    'departments',
                    'published_course_combo_id',
                    'department_combo_id',
                    'students',
                    'student_adds',
                    'student_makeup',
                    'grade_submission_status',
                    'course_detail',
                    'section_detail',
                    'view_only',
                    'display_grade',
                    'exam_types',
                    'grade_view_only',
                    'program_id',
                    'program_type_id',
                    'department_id',
                    'academic_year_selected',
                    'semester_selected'
                ));

                $this->render('view_grade');
                return;
            } else {
                $sectionTable = TableRegistry::getTableLocator()->get('Sections');
                $section_details = $sectionTable->find()
                    ->where(['Sections.id' => $section_or_published_course_id])
                    ->contain([
                        'Departments',
                        'Colleges',
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']]
                    ])
                    ->first();

                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $master_sheet = $examGradeTable->getMasterSheet($section_or_published_course_id, $academic_year, $semester);
                $section_detail = $section_details->toArray();
                $department_detail = $section_details->department;
                $college_detail = $section_details->college;
                $program_detail = $section_details->program;
                $program_type_detail = $section_details->program_type;

                $program_id = $section_details->program->id;
                $program_type_id = $section_details->program_type->id;
                $department_id = $section_details->department->id;
                $academic_year_selected = $academic_year;
                $semester_selected = $semester;

                $this->request->getSession()->write('master_sheet', $master_sheet);
                $this->request->getSession()->write('section_detail', $section_detail);
                $this->request->getSession()->write('department_detail', $department_detail);
                $this->request->getSession()->write('college_detail', $college_detail);
                $this->request->getSession()->write('program_detail', $program_detail);
                $this->request->getSession()->write('program_type_detail', $program_type_detail);
                $this->request->getSession()->write('program_id', $program_id);
                $this->request->getSession()->write('program_type_id', $program_type_id);
                $this->request->getSession()->write('department_id', $department_id);
                $this->request->getSession()->write('academic_year_selected', $academic_year_selected);
                $this->request->getSession()->write('semester_selected', $semester_selected);

                $this->set(compact(
                    'published_course_id',
                    'publishedCourses',
                    'programs',
                    'program_types',
                    'departments',
                    'published_course_combo_id',
                    'department_combo_id',
                    'master_sheet',
                    'section_detail',
                    'college_detail',
                    'department_detail',
                    'program_detail',
                    'program_type_detail',
                    'academic_year',
                    'semester',
                    'program_id',
                    'program_type_id',
                    'department_id',
                    'academic_year_selected',
                    'semester_selected'
                ));

                $this->render('master_sheet');
                return;
            }
        }

        $this->set(compact(
            'publishedCourses',
            'programs',
            'program_types',
            'departments',
            'published_course_combo_id',
            'department_combo_id',
            'student_course_register_and_adds'
        ));

        $this->render('view_grade');
    }

    public function exportMastersheetXls()
    {
        $this->viewBuilder()->disableAutoLayout();
        $master_sheet = $this->request->getSession()->read('master_sheet');
        $section_detail = $this->request->getSession()->read('section_detail');
        $department_detail = $this->request->getSession()->read('department_detail');
        $college_detail = $this->request->getSession()->read('college_detail');
        $program_detail = $this->request->getSession()->read('program_detail');
        $program_type_detail = $this->request->getSession()->read('program_type_detail');
        $program_id = $this->request->getSession()->read('program_id');
        $program_type_id = $this->request->getSession()->read('program_type_id');
        $department_id = $this->request->getSession()->read('department_id');
        $academic_year = $this->request->getSession()->read('academic_year_selected');
        $semester = $this->request->getSession()->read('semester_selected');
        $filename = "Master_Sheet_" . str_replace([' ', '/', '-'], '_', trim(preg_replace('/\s\s+/', ' ', $section_detail['name']))) . '_' . str_replace(['/', '-'], '_', $academic_year) . '_' . $semester . '_' . Time::now()->format('Y-m-d');

        $this->set(compact(
            'master_sheet',
            'section_detail',
            'college_detail',
            'department_detail',
            'program_detail',
            'program_type_detail',
            'program_id',
            'program_type_id',
            'filename',
            'department_id',
            'academic_year',
            'semester'
        ));

        $this->render('/Elements/master_sheet_xls');
    }

    public function exportMastersheetPdf()
    {
        $this->viewBuilder()->disableAutoLayout();
        $master_sheet = $this->request->getSession()->read('master_sheet');
        $section_detail = $this->request->getSession()->read('section_detail');
        $department_detail = $this->request->getSession()->read('department_detail');
        $college_detail = $this->request->getSession()->read('college_detail');
        $program_detail = $this->request->getSession()->read('program_detail');
        $program_type_detail = $this->request->getSession()->read('program_type_detail');
        $program_id = $this->request->getSession()->read('program_id');
        $program_type_id = $this->request->getSession()->read('program_type_id');
        $department_id = $this->request->getSession()->read('department_id');
        $academic_year = $this->request->getSession()->read('academic_year_selected');
        $semester = $this->request->getSession()->read('semester_selected');
        $filename = "Master_Sheet_" . str_replace(' ', '_', trim(preg_replace('/\s\s+/', ' ', $section_detail['name']))) . '_' . str_replace('/', '-', $academic_year) . '_' . $semester . '_' . Time::now()->format('Y-m-d');

        $this->set(compact(
            'master_sheet',
            'section_detail',
            'college_detail',
            'department_detail',
            'program_detail',
            'program_type_detail',
            'program_id',
            'program_type_id',
            'filename',
            'department_id',
            'academic_year',
            'semester'
        ));

        $this->response = $this->response->withType('application/pdf');
        $this->render('/Elements/master_sheet_pdf');
    }

    public function departmentGradeReport($section_id = null, $semester = null)
    {
        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT || !$this->onlyPre) {
            $this->gradeReport($section_id, $semester, 0);
        } else {
            $this->gradeReport($section_id, $semester, 1);
        }
    }

    public function collegeRegistrarGradeReport($section_id = null, $semester = null)
    {
        $this->registrarGradeReport($section_id, $semester, 0);
    }

    public function freshmanGradeReport($section_id = null, $semester = null)
    {
        $this->gradeReport($section_id, $semester, 1);
    }

    protected function gradeReport($section_id = null, $semester = null, $freshman_program = 0)
    {
        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = $programTable->find('list')->toArray();
        $program_types = $programTypeTable->find('list')->toArray();

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || (!empty($this->program_ids) && !empty($this->program_type_ids))) {
            $programs = $programTable->find('list')
                ->where(['Programs.id IN' => $this->program_ids])
                ->toArray();
            $program_types = $programTypeTable->find('list')
                ->where(['ProgramTypes.id IN' => $this->program_type_ids])
                ->toArray();
        }

        $departments = [0 => 0];

        if (!empty($this->request->getData('listSections')) && !empty($section_id)) {
            $action = $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT || !$this->onlyPre || $freshman_program == 0
                ? 'departmentGradeReport'
                : 'freshmanGradeReport';
            return $this->redirect(['action' => $action]);
        } elseif (!empty($this->request->getData('listSections'))) {
            $sectionTable = TableRegistry::getTableLocator()->get('Sections');
            $options = [
                'conditions' => [
                    'Sections.academic_year' => $this->request->getData('ExamGrade.academic_year'),
                    'Sections.program_id' => $this->request->getData('ExamGrade.program_id'),
                    'Sections.program_type_id' => $this->request->getData('ExamGrade.program_type_id')
                ],
                'contain' => [
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']]
                ],
                'order' => ['Sections.academic_year' => 'DESC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC']
            ];

            if ($freshman_program == 1) {
                $options['conditions'][] = ['Sections.college_id' => $this->college_id, 'Sections.department_id IS' => null];
            } else {
                $options['conditions'][] = ['Sections.department_id' => $this->department_id];
            }

            $sections_detail = $sectionTable->find('all', $options)->toArray();

            $sections = [];
            if (empty($sections_detail)) {
                $this->Flash->info('There is no section by the selected search criteria.');
            } else {
                foreach ($sections_detail as $secvalue) {
                    $sections[$secvalue->program->name . ', ' . $secvalue->program_type->name][$secvalue->id] = $secvalue->name . ' (' . (!empty($secvalue->year_level_id) ? $secvalue->year_level->name : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue->academic_year . ')';
                }
                $sections = [0 => '[ Select Section ]'] + $sections;
            }

            $academic_year_selected = $this->request->getData('ExamGrade.academic_year');
            $semester_selected = $this->request->getData('ExamGrade.semester');
            $program_id = $this->request->getData('ExamGrade.program_id');
            $program_type_id = $this->request->getData('ExamGrade.program_type_id');
            $department_id = $this->request->getData('ExamGrade.department_id') ?? ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT ? $this->department_id : (!empty($this->department_ids) ? array_values($this->department_ids)[0] : null));
            $college_id = $this->request->getData('ExamGrade.college_id') ?? ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE ? $this->college_id : (!empty($this->college_ids) ? array_values($this->college_ids)[0] : null));
        }

        if (!empty($this->request->getData('getGradeReport')) || (!empty($section_id) && !empty($semester) && $section_id != 0)) {
            if (!empty($this->request->getData('getGradeReport'))) {
                $section_id = $this->request->getData('ExamGrade.section_id');
                $semester = $this->request->getData('ExamGrade.semester_selected');
            }

            $sectionTable = TableRegistry::getTableLocator()->get('Sections');
            $section_detail = $sectionTable->find()
                ->where(['Sections.id' => $section_id])
                ->first();

            $students_in_section = $sectionTable->getSectionStudents($section_id, null, 1);

            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $section_published_course_detail = $publishedCourseTable->find()
                ->where([
                    'PublishedCourses.section_id' => $section_detail->id,
                    'PublishedCourses.semester' => $semester
                ])
                ->first();

            $academic_year_selected = $section_published_course_detail ? $section_published_course_detail->academic_year : $section_detail->academic_year;
            $semester_selected = $semester;
            $program_id = $section_detail->program_id;
            $program_type_id = $section_detail->program_type_id;
            $department_id = $section_detail->department_id;
            $college_id = $section_detail->college_id;

            $options = [
                'conditions' => [
                    'Sections.academic_year' => $academic_year_selected,
                    'Sections.program_id' => $program_id,
                    'Sections.program_type_id' => $program_type_id
                ],
                'contain' => [
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']]
                ],
                'order' => ['Sections.academic_year' => 'DESC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC']
            ];

            if ($freshman_program == 1) {
                $options['conditions'][] = ['Sections.college_id' => !empty($college_id) ? $college_id : $this->college_id, 'Sections.department_id IS' => null];
            } else {
                $options['conditions'][] = ['Sections.department_id' => !empty($department_id) ? $department_id : $this->department_id];
            }

            $sections_detail = $sectionTable->find('all', $options)->toArray();

            $sections = [];
            if (empty($sections_detail)) {
                $this->Flash->info('There is no section by the selected search criteria.');
            } else {
                foreach ($sections_detail as $secvalue) {
                    $sections[$secvalue->program->name . ', ' . $secvalue->program_type->name][$secvalue->id] = $secvalue->name . ' (' . (!empty($secvalue->year_level_id) ? $secvalue->year_level->name : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue->academic_year . ')';
                }
                $sections = [0 => '[ Select Section ]'] + $sections;
            }
        }

        if (!empty($this->request->getData('getGradeReport'))) {
            $student_ids = [];

            if (!empty($this->request->getData('Student'))) {
                foreach ($this->request->getData('Student') as $student) {
                    if (!empty($student['gp']) && $student['gp'] == 1) {
                        $student_ids[] = $student['student_id'];
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Flash->error('You are required to select at least one student.');
            } else {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $student_copies = $examGradeTable->getStudentCopies($student_ids, $academic_year_selected, $semester);

                if (empty($student_copies)) {
                    $this->Flash->info('There is no course registration for the selected students to display grade report.');
                } else {
                    $this->set(compact('student_copies'));
                    $this->response = $this->response->withType('application/pdf');
                    $this->viewBuilder()->setLayout('/pdf/default');
                    $this->render('grade_report_pdf');
                    return;
                }
            }
        }

        $acyear_registrar = $this->AcademicYear->academicYearInArray(Time::now()->year - ACY_BACK_FOR_ALL, Time::now()->year);

        $this->set(compact(
            'programs',
            'program_types',
            'departments',
            'academic_year_selected',
            'semester_selected',
            'program_id',
            'program_type_id',
            'section_id',
            'sections',
            'students_in_section',
            'student_copies',
            'department_id',
            'college_id',
            'acyear_registrar'
        ));

        $this->render('grade_report');
    }

    protected function registrarGradeReport($section_id = null, $semester = null, $freshman_program = 0)
    {
        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = $programTable->find('list')->toArray();
        $program_types = $programTypeTable->find('list')->toArray();

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || (!empty($this->program_ids) && !empty($this->program_type_ids))) {
            $programs = $programTable->find('list')
                ->where(['Programs.id IN' => $this->program_ids])
                ->toArray();
            $program_types = $programTypeTable->find('list')
                ->where(['ProgramTypes.id IN' => $this->program_type_ids])
                ->toArray();
        }

        $departments = [];

        if (!empty($this->request->getData('listSections')) && !empty($section_id)) {
            return $this->redirect(['action' => 'collegeRegistrarGradeReport']);
        } elseif (!empty($this->request->getData('listSections'))) {
            $sectionTable = TableRegistry::getTableLocator()->get('Sections');
            $options = [
                'conditions' => [
                    'Sections.academic_year' => $this->request->getData('ExamGrade.academic_year'),
                    'Sections.program_id' => $this->request->getData('ExamGrade.program_id'),
                    'Sections.program_type_id' => $this->request->getData('ExamGrade.program_type_id')
                ],
                'contain' => [
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']]
                ],
                'order' => ['Sections.academic_year' => 'DESC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC']
            ];

            if ($freshman_program == 1) {
                $options['conditions'][] = [
                    'Sections.college_id' => $this->request->getData('ExamGrade.college_id') ?? $this->college_id,
                    'Sections.department_id IS' => null
                ];
            } else {
                $options['conditions'][] = ['Sections.department_id' => $this->request->getData('ExamGrade.department_id')];
            }

            $sections_detail = $sectionTable->find('all', $options)->toArray();

            $sections = [];
            if (empty($sections_detail)) {
                $this->Flash->info('There is no section by the selected search criteria.');
            } else {
                foreach ($sections_detail as $secvalue) {
                    $sections[$secvalue->program->name . ', ' . $secvalue->program_type->name][$secvalue->id] = $secvalue->name . ' (' . (!empty($secvalue->year_level_id) ? $secvalue->year_level->name : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue->academic_year . ')';
                }
                $sections = [0 => '[ Select Section ]'] + $sections;
            }

            $academic_year_selected = $this->request->getData('ExamGrade.academic_year');
            $semester_selected = $this->request->getData('ExamGrade.semester');
            $program_id = $this->request->getData('ExamGrade.program_id');
            $program_type_id = $this->request->getData('ExamGrade.program_type_id');
            $department_id = $this->request->getData('ExamGrade.department_id') ?? null;
            $college_id = $this->request->getData('ExamGrade.college_id') ?? null;
        }

        if (!empty($this->request->getData('getGradeReport')) || (!empty($section_id) && !empty($semester) && $section_id != 0)) {
            if (!empty($this->request->getData('getGradeReport'))) {
                $section_id = $this->request->getData('ExamGrade.section_id');
                $semester = $this->request->getData('ExamGrade.semester_selected');
            }

            $sectionTable = TableRegistry::getTableLocator()->get('Sections');
            $section_detail = $sectionTable->find()
                ->where(['Sections.id' => $section_id])
                ->first();

            $students_in_section = $sectionTable->getSectionStudents($section_id, null, 1);

            $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $section_published_course_detail = $publishedCourseTable->find()
                ->where([
                    'PublishedCourses.section_id' => $section_detail->id,
                    'PublishedCourses.academic_year' => $section_detail->academic_year,
                    'PublishedCourses.semester' => $semester
                ])
                ->first();

            $academic_year_selected = $section_published_course_detail ? $section_published_course_detail->academic_year : $section_detail->academic_year;
            $semester_selected = $semester;
            $program_id = $section_detail->program_id;
            $program_type_id = $section_detail->program_type_id;
            $department_id = $section_detail->department_id;
            $college_id = $section_detail->college_id;

            $options = [
                'conditions' => [
                    'Sections.academic_year' => $academic_year_selected,
                    'Sections.program_id' => $program_id,
                    'Sections.program_type_id' => $program_type_id
                ],
                'contain' => [
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']]
                ],
                'order' => ['Sections.academic_year' => 'DESC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC']
            ];

            if ($freshman_program == 1) {
                $options['conditions'][] = ['Sections.college_id' => !empty($college_id) ? $college_id : $this->college_ids, 'Sections.department_id IS' => null];
            } else {
                $options['conditions'][] = ['Sections.department_id' => !empty($department_id) ? $department_id : $this->department_ids];
            }

            $sections_detail = $sectionTable->find('all', $options)->toArray();

            $sections = [];
            if (empty($sections_detail)) {
                $this->Flash->info('There is no section by the selected search criteria.');
            } else {
                foreach ($sections_detail as $secvalue) {
                    $sections[$secvalue->program->name . ', ' . $secvalue->program_type->name][$secvalue->id] = $secvalue->name . ' (' . (!empty($secvalue->year_level_id) ? $secvalue->year_level->name : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue->academic_year . ')';
                }
                $sections = [0 => '[ Select Section ]'] + $sections;
            }
        }

        if (!empty($this->request->getData('getGradeReport'))) {
            $student_ids = [];

            if (!empty($this->request->getData('Student'))) {
                foreach ($this->request->getData('Student') as $student) {
                    if (!empty($student['gp']) && $student['gp'] == 1) {
                        $student_ids[] = $student['student_id'];
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Flash->error('You are required to select at least one student.');
            } else {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $student_copies = $examGradeTable->getStudentCopies($student_ids, $academic_year_selected, $semester);

                if (empty($student_copies)) {
                    $this->Flash->info('There is no course registration for the selected students to display grade report.');
                } else {
                    $this->set(compact('student_copies'));
                    $this->response = $this->response->withType('application/pdf');
                    $this->viewBuilder()->setLayout('/pdf/default');
                    $this->render('grade_report_pdf');
                    $this->request = $this->request->withData('Student', null);
                    return;
                }
            }
        }

        if (!empty($this->department_ids)) {
            $departmentTable = TableRegistry::getTableLocator()->get('Departments');
            $departments = $departmentTable->find('list')
                ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                ->toArray();
        } elseif (!empty($this->college_ids)) {
            $collegeTable = TableRegistry::getTableLocator()->get('Colleges');
            $colleges = $collegeTable->find('list')
                ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                ->toArray();
        }

        $acyear_registrar = $this->AcademicYear->academicYearInArray(Time::now()->year - ACY_BACK_FOR_ALL, Time::now()->year);

        $this->set(compact(
            'programs',
            'program_types',
            'departments',
            'academic_year_selected',
            'acyear_registrar',
            'semester_selected',
            'program_id',
            'program_type_id',
            'section_id',
            'sections',
            'students_in_section',
            'student_copies',
            'colleges',
            'department_id',
            'college_id'
        ));

        $this->render('grade_report_registrar');
    }

    public function dataEntryInterface()
    {
        if ($this->role_id == ROLE_REGISTRAR) {
            $this->dataEntryInterfaceInternal();
        }
    }
    public function gradeUpdate()
    {
        if ($this->role_id == ROLE_REGISTRAR) {
            $this->dataEntryInterfaceEdit();
        }
    }

    public function academicStatusGradeInterface()
    {
        $this->academicStatusGradeInterfaceInternal();
    }
    protected function dataEntryInterfaceInternal($selected = null)
    {
        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        $collegeTable = TableRegistry::getTableLocator()->get('Colleges');

        $programs = $programTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->toArray();
        $program_types = $programTypeTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1])
            ->toArray();

        $departments = [];

        if (!empty($this->request->getData('saveGrade'))) {
            $publishedCoursesId = [];
            $student_ids = [];
            $studentId = null;
            $courseRegistrationAndGrade = [];
            $count = 0;
            $scaleNotFound = ['freq' => 0];

            if (!empty($this->request->getData('CourseRegistration'))) {
                foreach ($this->request->getData('CourseRegistration') as $student) {
                    if ($student['grade_scale_id'] == 0) {
                        $scaleNotFound['freq']++;
                    }

                    if (!empty($student['gp']) && $student['gp'] == 1 && $student['grade_scale_id'] != 0 && !empty($student['grade'])) {
                        $student_ids[] = $student['student_id'];
                        $studentId = $student['student_id'];
                        $courseRegistrationAndGrade[$count]['CourseRegistration'] = $student;
                        debug($student);

                        $date_created_and_modified_for_save = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

                        $publishedCoursesId = $student['published_course_id'];
                        $courseRegistrationAndGrade[$count]['ExamGrade'][$count] = [
                            'grade' => $student['grade'],
                            'department_approval' => 1,
                            'grade_scale_id' => $student['grade_scale_id'],
                            'department_reason' => 'Via backend data entry interface',
                            'registrar_approval' => 1,
                            'registrar_reason' => 'Via backend data entry interface',
                            'registrar_approval_date' => $date_created_and_modified_for_save,
                            'department_approval_date' => $date_created_and_modified_for_save,
                            'department_approved_by' => $this->Auth->user('id'),
                            'registrar_approved_by' => $this->Auth->user('id'),
                            'created' => $date_created_and_modified_for_save,
                            'modified' => $date_created_and_modified_for_save
                        ];
                        $courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $date_created_and_modified_for_save;
                        $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $date_created_and_modified_for_save;
                    }

                    $count++;
                }
            }

            if (!empty($courseRegistrationAndGrade)) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                foreach ($courseRegistrationAndGrade as $data) {
                    $examGradeTable->CourseRegistrations->saveMany($examGradeTable->CourseRegistrations->newEntities($data, ['validate' => false]));
                }

                if ($scaleNotFound['freq'] > 0) {
                    $this->Flash->success(__('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'));
                } else {
                    $this->Flash->success(__('You have entered the data successfully.'));
                }
            } else {
                if ($scaleNotFound['freq'] > 0) {
                    $this->Flash->error(__('It is required to have defined grade scale in order to perform data entry. ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'));
                    $this->request = $this->request->withData('listPublishedCourse', true);
                } else {
                    if (empty($student_ids)) {
                        $this->request = $this->request->withData('listPublishedCourse', true);
                        $this->Flash->error(__('You are required to select at least one student.'));
                    }
                }
            }
        }

        if (!empty($this->request->getData('addCoursesGrade'))) {
            $publishedCoursesId = [];
            $student_ids = [];
            $studentId = null;
            $courseAddAndGrade = [];
            $count = 0;
            $scaleNotFound = ['freq' => 0];

            if (!empty($this->request->getData('CourseAdd'))) {
                foreach ($this->request->getData('CourseAdd') as $student) {
                    if ($student['grade_scale_id'] == 0) {
                        $scaleNotFound['freq']++;
                    }

                    if (!empty($student['gp']) && $student['gp'] == 1 && $student['grade_scale_id'] != 0 && !empty($student['grade'])) {
                        $student_ids[] = $student['student_id'];
                        $studentId = $student['student_id'];
                        $courseAddAndGrade[$count]['CourseAdd'] = $student;

                        $date_created_and_modified_for_save = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'], $student['semester']);

                        $publishedCoursesId = $student['published_course_id'];
                        $courseAddAndGrade[$count]['ExamGrade'][$count] = [
                            'grade' => $student['grade'],
                            'department_approval' => 1,
                            'grade_scale_id' => $student['grade_scale_id'],
                            'department_reason' => 'Via backend data entry interface',
                            'registrar_approval' => 1,
                            'registrar_reason' => 'Via backend data entry interface',
                            'registrar_approval_date' => $date_created_and_modified_for_save,
                            'department_approval_date' => $date_created_and_modified_for_save,
                            'department_approved_by' => $this->Auth->user('id'),
                            'registrar_approved_by' => $this->Auth->user('id'),
                            'created' => $date_created_and_modified_for_save,
                            'modified' => $date_created_and_modified_for_save
                        ];
                        $courseAddAndGrade[$count]['CourseAdd']['created'] = $date_created_and_modified_for_save;
                        $courseAddAndGrade[$count]['CourseAdd']['modified'] = $date_created_and_modified_for_save;
                    }

                    $count++;
                }
            }

            if (!empty($courseAddAndGrade)) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                foreach ($courseAddAndGrade as $data) {
                    $examGradeTable->CourseAdds->saveMany($examGradeTable->CourseAdds->newEntities($data, ['validate' => false]));
                }

                if ($scaleNotFound['freq'] > 0) {
                    $this->Flash->success(__('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'));
                } else {
                    $this->Flash->success(__('You have entered the add course(s) data successfully.'));
                }
            } else {
                if ($scaleNotFound['freq'] > 0) {
                    $this->Flash->error(__('It is required to have defined grade scale in order to perform data entry. ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'));
                    $this->request = $this->request->withData('listPublishedCourse', true);
                } else {
                    if (empty($student_ids)) {
                        $this->request = $this->request->withData('listPublishedCourse', true);
                        $this->Flash->error(__('You are required to select at least one student.'));
                    }
                }
            }

            $this->request = $this->request->withData('ExamGrade.studentnumber', $this->request->getData('Student.studentnumber'));
            $this->request = $this->request->withData('ExamGrade.semester', $this->request->getData('Student.semester'));
            $this->request = $this->request->withData('ExamGrade.academic_year', str_replace('-', '/', $this->request->getData('Student.academic_year')));
            $this->request = $this->request->withData('listPublishedCourse', true);
        }

        debug($this->request->getData());

        if (!empty($this->request->getData('listPublishedCourse'))) {
            $department_ids = [];
            $everyThingOk = false;
            $selectedStudent = [];

            if (!empty($this->department_ids)) {
                $studentTable = TableRegistry::getTableLocator()->get('Students');
                $selectedStudent = $studentTable->find()
                    ->where(['Students.studentnumber' => trim($this->request->getData('ExamGrade.studentnumber'))])
                    ->contain(['StudentsSections'])
                    ->first();

                if (!empty($selectedStudent)) {
                    $selectedStudentDetail = $studentTable->getStudentRegisteredAddDropCurriculumResult($selectedStudent->id);
                    if (!in_array($selectedStudent->department_id, $this->department_ids)) {
                        $this->Flash->warning(__('You don\'t have the privilege to enter data for ' . $this->request->getData('ExamGrade.studentnumber') . '.'));
                    } else {
                        $everyThingOk = true;
                    }
                } else {
                    $this->Flash->error(__(' ' . $this->request->getData('ExamGrade.studentnumber') . ' is not a valid student number.'));
                }
            } elseif (!empty($this->college_ids)) {
                $studentTable = TableRegistry::getTableLocator()->get('Students');
                $selectedStudent = $studentTable->find()
                    ->where(['Students.studentnumber' => trim($this->request->getData('ExamGrade.studentnumber'))])
                    ->contain(['StudentsSections'])
                    ->first();

                if (!empty($selectedStudent)) {
                    $selectedStudentDetail = $studentTable->getStudentRegisteredAddDropCurriculumResult($selectedStudent->id);
                    if (!in_array($selectedStudent->college_id, $this->college_ids)) {
                        $this->Flash->warning(__('You don\'t have the privilege to enter data for ' . $this->request->getData('ExamGrade.studentnumber') . '.'));
                    } else {
                        $everyThingOk = true;
                    }
                } else {
                    $this->Flash->error(__(' ' . $this->request->getData('ExamGrade.studentnumber') . ' is not a valid student number.'));
                }
            } else {
                $this->Flash->error(__('You don\'t have the privilege to enter data for the selected student.'));
            }

            if ($everyThingOk && !empty($selectedStudent)) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $yearLevelAndSemesterOfStudent = $examGradeTable->CourseRegistrations->Students->StudentExamStatuses->studentYearAndSemesterLevel(
                    $selectedStudent->id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester')
                );
                $graduated = $examGradeTable->CourseRegistrations->Students->SenateLists->find()
                    ->where(['SenateLists.student_id' => $selectedStudent->id])
                    ->count();

                $student_academic_profile = $examGradeTable->CourseRegistrations->Students->getStudentRegisteredAddDropCurriculumResult(
                    $selectedStudent->id,
                    $this->AcademicYear->currentAcademicYear()
                );

                $this->set(compact('student_academic_profile'));

                $selectedStudentDetails = $examGradeTable->getStudentCopy(
                    $selectedStudent->id,
                    $this->request->getData('ExamGrade.academic_year'),
                    $this->request->getData('ExamGrade.semester')
                );
                $admission_explode = explode('-', $selectedStudentDetails['Student']['admissionyear']);
                $studentAdmissionYear = $this->AcademicYear->getAcademicYear($admission_explode[1], $admission_explode[0]);

                if (empty($selectedStudentDetails['courses'])) {
                    $publishedCourses = $examGradeTable->getPublishedCourseIfExist(
                        $selectedStudentDetails['Student']['department_id'],
                        $this->request->getData('ExamGrade.academic_year'),
                        $this->request->getData('ExamGrade.semester'),
                        $selectedStudentDetails['Student']['program_id'],
                        $selectedStudentDetails['Student']['program_type_id'],
                        $selectedStudentDetails,
                        $studentAdmissionYear,
                        $this->AcademicYear->currentAcademicYear()
                    );

                    $studentbasic = $selectedStudentDetails;
                    $this->set(compact('publishedCourses', 'studentbasic'));
                } elseif (!empty($selectedStudentDetails['courses'])) {
                    $publishedCourses = $examGradeTable->getPublishedCourseIfExist(
                        $selectedStudentDetails['Student']['department_id'],
                        $this->request->getData('ExamGrade.academic_year'),
                        $this->request->getData('ExamGrade.semester'),
                        $selectedStudentDetails['Student']['program_id'],
                        $selectedStudentDetails['Student']['program_type_id'],
                        $selectedStudentDetails,
                        $studentAdmissionYear,
                        $this->AcademicYear->currentAcademicYear()
                    );

                    if (!empty($publishedCourses['courses'])) {
                        foreach ($publishedCourses['courses'] as $key => &$value) {
                            if ($value['PublishedCourse']['readOnly']) {
                                unset($publishedCourses['courses'][$key]);
                            }
                        }
                    }

                    $publishedCourses['courses'] = $this->mergePublishedCourse($publishedCourses, $selectedStudentDetails);
                    $studentbasic = $selectedStudentDetails;
                    $this->set(compact('publishedCourses', 'studentbasic', 'graduated'));
                }

                $this->set(compact('graduated'));
            }
        }

        if (!empty($this->department_ids)) {
            $departments = $departmentTable->find('list')
                ->where(['Departments.id IN' => $this->department_ids])
                ->toArray();
        } elseif (!empty($this->college_ids)) {
            $colleges = $collegeTable->find('list')
                ->where(['Colleges.id IN' => $this->college_ids])
                ->toArray();
        }

        $current_acy = $this->AcademicYear->currentAcademicYear();
        if (is_numeric(ACY_BACK_FOR_BACK_DATED_DATA_ENTRY) && ACY_BACK_FOR_BACK_DATED_DATA_ENTRY) {
            $acyear_list = $this->AcademicYear->academicYearInArray(
                (explode('/', $current_acy)[0] - ACY_BACK_FOR_BACK_DATED_DATA_ENTRY),
                explode('/', $current_acy)[0]
            );
        } elseif (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
            $acyear_list = $this->AcademicYear->academicYearInArray(
                (explode('/', $current_acy)[0] - ACY_BACK_FOR_ALL),
                explode('/', $current_acy)[0]
            );
        } else {
            $acyear_list = [$current_acy => $current_acy];
        }

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) {
            $acyear_list = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, explode('/', $current_acy)[0]);
        }

        $this->set(compact(
            'programs',
            'program_types',
            'departments',
        ));

        $this->render('data_entry_interface');
    }


    protected function mergePublishedCourse($publish1, $publish2)
    {
        $publishedCourses = ['courses' => []];
        $academicYear = $publish1['courses'][0]['PublishedCourse']['academic_year'] ?? null;
        $semester = $publish1['courses'][0]['PublishedCourse']['semester'] ?? null;
        $publish3 = ['courses' => []];
        $publish5 = ['courses' => []];
        $studentId = null;

        if (!empty($publish2['courses'])) {
            foreach ($publish2['courses'] as $pv2) {
                if (
                    isset($pv2['PublishedCourse']['academic_year']) &&
                    $pv2['PublishedCourse']['academic_year'] == $academicYear &&
                    isset($pv2['PublishedCourse']['semester']) &&
                    $pv2['PublishedCourse']['semester'] == $semester
                ) {
                    $publish3['courses'][] = $pv2;
                } else {
                    if (!empty($pv2['CourseRegistration']['student_id'])) {
                        $studentId = $pv2['CourseRegistration']['student_id'];
                    }
                }
            }
        }

        if (empty($publish3['courses'])) {
            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $plist = $examGradeTable->CourseRegistrations->find()
                ->where([
                    'CourseRegistrations.academic_year' => $academicYear,
                    'CourseRegistrations.semester' => $semester,
                    'CourseRegistrations.student_id' => $studentId
                ])
                ->contain([
                    'PublishedCourses' => [
                        'Courses' => ['GradeTypes' => ['Grades']],
                        'CourseInstructorAssignments' => [
                            'Staffs' => [
                                'fields' => ['id', 'full_name', 'first_name', 'middle_name', 'last_name'],
                                'Titles' => ['fields' => ['id', 'title']],
                                'Colleges' => ['fields' => ['id', 'name']],
                                'Departments' => ['fields' => ['id', 'name']],
                                'Positions' => ['fields' => ['id', 'position']]
                            ],
                            'order' => ['isprimary' => 'DESC'],
                            'limit' => 1
                        ]
                    ],
                    'ExamGrades'
                ])
                ->toArray();

            $count = 0;
            if (!empty($plist)) {
                foreach ($plist as &$plv) {
                    $plv['PublishedCourse']['grade'] = $examGradeTable->getApprovedGrade($plv['CourseRegistration']['id'], 1);
                    $publish3['courses'][$count]['PublishedCourse'] = $plv['PublishedCourse'];
                    $publish3['courses'][$count]['Course'] = $plv['PublishedCourse']['Course'];
                    $publish3['courses'][$count]['CourseRegistration'] = $plv['CourseRegistration'];
                    $count++;
                }
            }

            $pAddlist = $examGradeTable->CourseAdds->find()
                ->where([
                    'CourseAdds.department_approval' => 1,
                    'CourseAdds.registrar_confirmation' => 1,
                    'CourseAdds.academic_year' => $academicYear,
                    'CourseAdds.semester' => $semester,
                    'CourseAdds.student_id' => $studentId
                ])
                ->contain([
                    'PublishedCourses' => [
                        'Courses' => ['GradeTypes' => ['Grades']],
                        'CourseInstructorAssignments' => [
                            'Staffs' => [
                                'fields' => ['id', 'full_name', 'first_name', 'middle_name', 'last_name'],
                                'Titles' => ['fields' => ['id', 'title']],
                                'Colleges' => ['fields' => ['id', 'name']],
                                'Departments' => ['fields' => ['id', 'name']],
                                'Positions' => ['fields' => ['id', 'position']]
                            ],
                            'order' => ['isprimary' => 'DESC'],
                            'limit' => 1
                        ]
                    ],
                    'ExamGrades'
                ])
                ->toArray();

            if (!empty($pAddlist)) {
                foreach ($pAddlist as &$plv) {
                    $plv['PublishedCourse']['grade'] = $examGradeTable->getApprovedGrade($plv['CourseAdd']['id'], 0);
                    $publish3['courses'][$count]['PublishedCourse'] = $plv['PublishedCourse'];
                    $publish3['courses'][$count]['Course'] = $plv['PublishedCourse']['Course'];
                    $publish3['courses'][$count]['CourseAdd'] = $plv['CourseAdd'];
                    $count++;
                }
            }
        }

        if (!empty($publish1['courses'])) {
            foreach ($publish1['courses'] as $pv1) {
                $found = false;
                foreach ($publish3['courses'] as $pv3) {
                    if ($pv1['PublishedCourse']['id'] == $pv3['PublishedCourse']['id']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $publish5['courses'][] = $pv1;
                }
            }
        }

        $publishedCourses['courses'] = array_merge($publish5['courses'], $publish3['courses']);

        $freq = [];
        if (!empty($publishedCourses['courses'])) {
            foreach ($publishedCourses['courses'] as $v) {
                if (isset($v['PublishedCourse']['course_id']) && !isset($freq[$v['PublishedCourse']['course_id']])) {
                    $freq[$v['PublishedCourse']['course_id']] = 0;
                }
                if (!empty($v['PublishedCourse']['course_id'])) {
                    $freq[$v['PublishedCourse']['course_id']]++;
                }
            }
        }

        debug($freq);

        if (!empty($publishedCourses['courses'])) {
            foreach ($publishedCourses['courses'] as $k => &$vv) {
                $failedAnyPrerequistie = ['freq' => 0];

                if ($freq[$vv['PublishedCourse']['course_id']] > 1 && !isset($vv['CourseRegistration'])) {
                    unset($publishedCourses['courses'][$k]);
                }

                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $is_grade_submitted = $examGradeTable->isGradeSubmittedForPublishedCourseGivenStudentId($publish2['Student']['id'], $vv['PublishedCourse']['id']);

                if (!empty($vv['Course']['Prerequisite'])) {
                    debug($vv['Course']);
                    $courseDropTable = TableRegistry::getTableLocator()->get('CourseDrops');
                    foreach ($vv['Course']['Prerequisite'] as $preValue) {
                        $failed = $courseDropTable->prerequisite_taken($publish2['Student']['id'], $preValue['prerequisite_course_id']);
                        debug($failed);
                        if ($failed == 0 && $preValue['co_requisite'] != true) {
                            $failedAnyPrerequistie['freq']++;
                        }
                    }
                }

                if ($failedAnyPrerequistie['freq'] > 0) {
                    $vv['PublishedCourse']['prerequisiteFailed'] = true;
                } else {
                    $vv['PublishedCourse']['prerequisiteFailed'] = 0;
                }

                $vv['PublishedCourse']['readOnly'] = $is_grade_submitted;

                if (!empty($vv['PublishedCourse']['grade_scale_id']) && $vv['PublishedCourse']['grade_scale_id'] != 0) {
                    $vv['Course']['grade_scale_id'] = $vv['PublishedCourse']['grade_scale_id'];
                } else {
                    $gradeScaleTable = TableRegistry::getTableLocator()->get('GradeScales');
                    $vv['Course']['grade_scale_id'] = $gradeScaleTable->getGradeScaleId($vv['Course']['grade_type_id'], $publish2);
                }
            }
        }

        return $publishedCourses['courses'];
    }


    protected function academicStatusGradeInterfaceInternal($selected = null)
    {
        /*
         * 1. Retrieve list of sections based on the given search criteria
         * 2. Display list of sections
         * 3. Upon the selection of section, display list of students with check-box
         * 4. Prepare student grade report in PDF for the selected students
         */
        $programTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypeTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $gradeTypeTable = TableRegistry::getTableLocator()->get('GradeTypes');
        $gradeTable = TableRegistry::getTableLocator()->get('Grades');
        $academicStatusTable = TableRegistry::getTableLocator()->get('AcademicStatuses');

        $programs = $programTable->find('list')->toArray();
        $program_types = $programTypeTable->find('list')->toArray();
        $departments = [];

        if (!empty($this->request->getData('saveGrade'))) {
            $publishedCoursesId = [];
            $student_ids = [];
            $studentId = null;
            $courseRegistrationAndGrade = [];
            $count = 0;
            $scaleNotFound = ['freq' => 0];

            foreach ($this->request->getData('CourseRegistration') as $student) {
                if ($student['grade_scale_id'] == 0) {
                    $scaleNotFound['freq']++;
                    debug($scaleNotFound);
                }

                if (!empty($student['gp']) && $student['gp'] == 1 && $student['grade_scale_id'] != 0) {
                    $student_ids[] = $student['student_id'];
                    $studentId = $student['student_id'];
                    $courseRegistrationAndGrade[$count]['CourseRegistration'] = $student;
                    $publishedCoursesId = $student['published_course_id'];
                    $courseRegistrationAndGrade[$count]['ExamGrade'][$count] = [
                        'grade' => $student['grade'],
                        'department_approval' => 1,
                        'grade_scale_id' => $student['grade_scale_id'],
                        'department_reason' => 'Via backend data entry interface',
                        'registrar_approval' => 1,
                        'registrar_reason' => 'Via backend data entry interface',
                        'registrar_approval_date' => $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year']),
                        'department_approval_date' => $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year']),
                        'created' => $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year']),
                        'modified' => $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year'])
                    ];
                    $courseRegistrationAndGrade[$count]['CourseRegistration']['created'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year']);
                    $courseRegistrationAndGrade[$count]['CourseRegistration']['modified'] = $this->AcademicYear->getAcademicYearBegainingDate($student['academic_year']);
                }
                $count++;
            }

            if (!empty($courseRegistrationAndGrade)) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                foreach ($courseRegistrationAndGrade as $data) {
                    $examGradeTable->CourseRegistrations->saveMany($examGradeTable->CourseRegistrations->newEntities($data, ['validate' => false]));
                }

                if ($scaleNotFound['freq'] > 0) {
                    $this->Flash->success(__('You have entered some data successfully but ' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'));
                } else {
                    $this->Flash->success(__('You have entered the data successfully.'));
                }

                $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                $studentExamStatusTable->deleteAll(['StudentExamStatuses.student_id' => $studentId], false);
                $studentExamStatusTable->updateAcademicStatusByStudent($studentId, $publishedCoursesId);
            } else {
                if ($scaleNotFound['freq'] > 0) {
                    $this->Flash->info(__('' . $scaleNotFound['freq'] . ' course(s) don\'t have scale, please ask either the registrar or department to define scale.'));
                } else {
                    $this->Flash->error(__('You are required to select at least one course.'));
                }
            }

            if (empty($student_ids)) {
                $this->request = $this->request->withData('listPublishedCourse', true);
                $this->Flash->error(__('You are required to select at least one course.'));
            }
        }

        if (!empty($this->request->getData('listPublishedCourse'))) {
            $department_ids = [];
            $everyThingOk = false;
            $selectedStudent = [];

            if (!empty($this->department_ids)) {
                $studentTable = TableRegistry::getTableLocator()->get('Students');
                $selectedStudent = $studentTable->find()
                    ->where(['Students.studentnumber' => trim($this->request->getData('Search.studentnumber'))])
                    ->contain(['StudentsSections'])
                    ->first();

                if (!empty($selectedStudent)) {
                    $selectedStudentDetail = $studentTable->getStudentRegisteredAddDropCurriculumResult($selectedStudent->id);
                    if (!in_array($selectedStudent->department_id, $this->department_ids)) {
                        $this->Flash->info(__('You don\'t have the privilege to enter data for ' . $this->request->getData('Search.studentnumber') . '.'));
                    } else {
                        $everyThingOk = true;
                    }
                } else {
                    $this->Flash->info(__(' ' . $this->request->getData('Search.studentnumber') . ' is not a valid student number.'));
                }
            } else {
                $this->Flash->info(__('You don\'t have the privilege to enter data for the selected student.'));
            }

            if ($everyThingOk && !empty($selectedStudent)) {
                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $yearLevelAndSemesterOfStudent = $examGradeTable->CourseRegistrations->Students->StudentExamStatuses->studentYearAndSemesterLevel(
                    $selectedStudent->id,
                    $this->request->getData('Search.academic_year'),
                    $this->request->getData('Search.semester')
                );

                $student_academic_profile = $examGradeTable->CourseRegistrations->Students->getStudentRegisteredAddDropCurriculumResult(
                    $selectedStudent->id,
                    $this->AcademicYear->currentAcademicYear()
                );
                $this->set(compact('student_academic_profile'));

                $selectedStudentDetails = $examGradeTable->getStudentCopy(
                    $selectedStudent->id,
                    $this->request->getData('Search.academic_year'),
                    $this->request->getData('Search.semester')
                );

                $admission_explode = explode('-', $selectedStudentDetails['Student']['admissionyear']);
                $studentAdmissionYear = $this->AcademicYear->getAcademicYear($admission_explode[1], $admission_explode[0]);

                if (empty($selectedStudentDetails['courses'])) {
                    $publishedCourses = $examGradeTable->getPublishedCourseIfExist(
                        $selectedStudentDetails['Student']['department_id'],
                        $this->request->getData('Search.academic_year'),
                        $this->request->getData('Search.semester'),
                        $selectedStudentDetails['Student']['program_id'],
                        $selectedStudentDetails['Student']['program_type_id'],
                        $selectedStudentDetails,
                        $studentAdmissionYear,
                        $this->AcademicYear->currentAcademicYear()
                    );
                    if (empty($publishedCourses['courses'])) {
                        $manuallStatusEntry = true;
                    }
                    $studentbasic = $selectedStudentDetails;
                    $this->set(compact('publishedCourses', 'manuallStatusEntry', 'studentbasic'));
                } elseif (!empty($selectedStudentDetails['courses'])) {
                    $publishedCourses = $examGradeTable->getPublishedCourseIfExist(
                        $selectedStudentDetails['Student']['department_id'],
                        $this->request->getData('Search.academic_year'),
                        $this->request->getData('Search.semester'),
                        $selectedStudentDetails['Student']['program_id'],
                        $selectedStudentDetails['Student']['program_type_id'],
                        $selectedStudentDetails,
                        $studentAdmissionYear,
                        $this->AcademicYear->currentAcademicYear()
                    );
                    foreach ($publishedCourses['courses'] as $key => &$value) {
                        if ($value['PublishedCourse']['readOnly']) {
                            unset($publishedCourses['courses'][$key]);
                        }
                    }
                    $publishedCourses['courses'] = $this->mergePublishedCourse($publishedCourses, $selectedStudentDetails);
                    $studentbasic = $selectedStudentDetails;
                    $this->set(compact('publishedCourses', 'studentbasic'));
                }
            }
        }

        if (!empty($this->department_ids)) {
            $departmentTable = TableRegistry::getTableLocator()->get('Departments');
            $departments = $departmentTable->find('list')
                ->where(['Departments.id IN' => $this->department_ids])
                ->toArray();
        } elseif (!empty($this->college_ids)) {
            $collegeTable = TableRegistry::getTableLocator()->get('Colleges');
            $colleges = $collegeTable->find('list')
                ->where(['Colleges.id IN' => $this->college_ids])
                ->toArray();
        }

        $gradeTypes = $gradeTypeTable->find('list', ['fields' => ['id', 'type']])->toArray();
        if (empty($this->request->getData())) {
            $temp = array_keys($gradeTypes);
            $gradeTypeId = $temp[0];
        } else {
            $gradeTypeId = !empty($this->request->getData('GradeScale.grade_type_id'))
                ? $this->request->getData('GradeScale.grade_type_id')
                : array_keys($gradeTypes)[0];
        }

        $grades = $gradeTable->find('list')
            ->where(['Grades.grade_type_id' => $gradeTypeId])
            ->select(['id', 'grade'])
            ->toArray();
        $academicStatuses = $academicStatusTable->find('list')
            ->select(['id', 'name'])
            ->toArray();

        $this->set(compact(
            'programs',
            'academicStatuses',
            'program_types',
            'grades',
            'departments',
            'gradeTypes',
        ));

        $this->render('academic_status_grade_interface');
    }

    public function importArchivedData()
    {
        if (!empty($this->request->getData()) && !empty($this->request->getData('ExamGrade.File.tmp_name'))) {
            $fileType = $this->request->getData('ExamGrade.File.type');
            if (!in_array($fileType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                $this->Flash->error(__('Importing Error. Please save your Excel file as "Excel 97-2003 Workbook" or "Excel Workbook" type and try again. Current file format is: ' . $fileType));
                return;
            }

            // Placeholder for PhpSpreadsheet integration
            // $data = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            // $spreadsheet = $data->load($this->request->getData('ExamGrade.File.tmp_name'));
            // $xls_data = $spreadsheet->getActiveSheet()->toArray();

            $required_fields = [
                'studentnumber',
                'course_code',
                'course_title',
                'credit',
                'grade',
                'academic_year',
                'semester',
                'academic_status',
                'cgpa',
                'mgpa'
            ];

            // Placeholder for checking sheet data
            $xls_data = []; // Replace with actual data parsing using PhpSpreadsheet
            if (empty($xls_data)) {
                $this->Flash->error(__('Importing Error. The Excel file you uploaded is empty.'));
                return;
            }

            if (empty($xls_data[0])) {
                $this->Flash->error(__('Importing Error. Please insert your field names (studentnumber, course_code, course_title, credit, grade, academic_year, semester, academic_status, cgpa, mgpa) at the first row of your Excel file.'));
                return;
            }

            $non_existing_field = [];
            foreach ($required_fields as $field) {
                if (!in_array($field, $xls_data[0])) {
                    $non_existing_field[] = $field;
                }
            }

            if (!empty($non_existing_field)) {
                $this->Flash->error(__('Importing Error. ' . implode(', ', $non_existing_field) . ' is/are required in the Excel file you imported at the first row.'));
                return;
            }
        }
    }

    public function getAddCoursesDataEntry($student_id, $academic_year, $semester)
    {
        $this->viewBuilder()->setLayout('ajax');

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $studentTable = TableRegistry::getTableLocator()->get('Students');
        $departmentTable = TableRegistry::getTableLocator()->get('Departments');
        $collegeTable = TableRegistry::getTableLocator()->get('Colleges');

        $student = $studentTable->find()
            ->where(['Students.id' => $student_id])
            ->contain(['Colleges'])
            ->first();

        $departments = $departmentTable->find('list')
            ->where([
                'Departments.active' => 1,
                'Departments.id IN' => $examGradeTable->CourseAdds->PublishedCourses->find()
                    ->select(['department_id'])
                    ->where([
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => str_replace('-', '/', $academic_year),
                        'PublishedCourses.program_id' => $student->program_id,
                        'PublishedCourses.program_type_id' => $student->program_type_id
                    ])
            ])
            ->toArray();

        $colleges = $collegeTable->find('list')
            ->where(['Colleges.active' => 1])
            ->toArray();

        $addParamaters = [
            'student_id' => $student_id,
            'academic_year' => $academic_year,
            'semester' => $semester,
            'studentnumber' => str_replace('/', '-', $student->studentnumber)
        ];

        $this->set(compact('colleges', 'departments', 'addParamaters'));

        $already_added_courses_count = $examGradeTable->CourseAdds->find()
            ->where([
                'CourseAdds.student_id' => $student_id,
                'CourseAdds.academic_year' => str_replace('-', '/', $academic_year),
                'CourseAdds.semester' => $semester,
                'OR' => [
                    ['CourseAdds.department_approval' => 1, 'CourseAdds.registrar_confirmation IS' => null],
                    ['CourseAdds.department_approval' => 1, 'CourseAdds.registrar_confirmation' => ''],
                    ['CourseAdds.registrar_confirmation' => 1]
                ]
            ])
            ->count();

        $collegesList = $collegeTable->find('list')
            ->where(['Colleges.active' => 1])
            ->toArray();
        $departmentsList = [];

        if (!empty($student->college->stream)) {
            if ($student->program_id == PROGRAM_UNDEGRADUATE) {
                $collegesList = $collegeTable->find('list')
                    ->where([
                        'Colleges.active' => 1,
                        'Colleges.stream' => $student->college->stream,
                        'Colleges.campus_id' => $student->college->campus_id
                    ])
                    ->toArray();
            } else {
                $collegesList = $collegeTable->find('list')
                    ->where(['Colleges.id' => $student->college_id])
                    ->toArray();
            }
        }

        if (!empty($student->department_id)) {
            $departmentsList = $departmentTable->find('list')
                ->where([
                    'Departments.college_id' => $student->college_id,
                    'Departments.active' => 1,
                    'Departments.id IN' => $examGradeTable->CourseAdds->PublishedCourses->find()
                        ->select(['department_id'])
                        ->where([
                            'PublishedCourses.semester' => $semester,
                            'PublishedCourses.academic_year' => str_replace('-', '/', $academic_year),
                            'PublishedCourses.program_id' => $student->program_id,
                            'PublishedCourses.program_type_id' => $student->program_type_id
                        ])
                ])
                ->toArray();
        }

        $student_section_exam_status = $studentTable->get_student_section($student_id, $academic_year, $semester);

        $this->set(compact('collegesList', 'departmentsList', 'student_section_exam_status', 'already_added_courses_count'));
    }

    public function getPublishedAddCourses($section_id = null, $addParamaters = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $academicYearSemesterArray = explode(",", $addParamaters);
        debug($section_id);
        debug($academicYearSemesterArray);

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        if (!empty($academicYearSemesterArray)) {
            $academicYear = str_replace("-", "/", $academicYearSemesterArray[1]);
            $current_academic_year = $academicYear;
            $section_semester = $academicYearSemesterArray[2];
        } else {
            $current_academic_year = $this->AcademicYear->currentAcademicYear();
            $latestAcSemester = $courseRegistrationTable->getLastestStudentSemesterAndAcademicYear($academicYearSemesterArray[0], $current_academic_year);
            $section_semester = $courseRegistrationTable->latest_semester_of_section($section_id, $current_academic_year);

            if ($section_semester == 2) {
                $section_semester = $latestAcSemester['semester'];
            }
        }

        $student_section_id = $examGradeTable->CourseAdds->Students->StudentsSections->field('section_id', [
            'student_id' => $academicYearSemesterArray[0],
            'archive' => 0
        ]);

        if ($student_section_id == $section_id) {
            $otherpublished = $examGradeTable->CourseAdds->PublishedCourses->find()
                ->where([
                    'PublishedCourses.academic_year' => $current_academic_year,
                    'PublishedCourses.semester' => $section_semester,
                    'PublishedCourses.add' => 0,
                    'PublishedCourses.section_id' => $section_id
                ])
                ->contain([
                    'Courses' => [
                        'fields' => ['course_code', 'credit', 'id', 'course_title'],
                        'GradeTypes' => ['Grades']
                    ]
                ])
                ->toArray();
        } else {
            $sectionAcademicYear = $examGradeTable->CourseAdds->PublishedCourses->Sections->find()
                ->where(['Sections.id' => $section_id])
                ->first();

            $otherpublished = $examGradeTable->CourseAdds->PublishedCourses->find()
                ->where([
                    'PublishedCourses.academic_year' => $sectionAcademicYear->academic_year,
                    'PublishedCourses.semester' => $section_semester,
                    'PublishedCourses.drop' => 0,
                    'PublishedCourses.section_id' => $section_id
                ])
                ->contain([
                    'Courses' => [
                        'fields' => ['course_code', 'credit', 'id', 'course_title'],
                        'GradeTypes' => ['Grades']
                    ]
                ])
                ->toArray();
        }

        if (!empty($academicYearSemesterArray[0])) {
            $otherAdds = $this->excludeAlreadyAdded($otherpublished, $academicYearSemesterArray[0]);
        }

        $addParamaterss = [
            'student_id' => $academicYearSemesterArray[0],
            'academic_year' => $academicYearSemesterArray[1],
            'semester' => $academicYearSemesterArray[2]
        ];

        $this->set(compact('otherAdds', 'addParamaterss'));
    }

    protected function excludeAlreadyAdded($otherAdds, $student_id = null)
    {
        $pub_own_as_add_courses = [];
        $count = 0;

        foreach ($otherAdds as $ownValue) {
            if (!empty($ownValue['Course']['id'])) {
                $courseDropTable = TableRegistry::getTableLocator()->get('CourseDrops');
                $already_taken_course = $courseDropTable->course_taken($student_id, $ownValue['Course']['id']);
            }

            debug($already_taken_course);

            if ($already_taken_course == 1 || $already_taken_course == 4 || $already_taken_course == 2) {
                $pub_own_as_add_courses[$count] = $ownValue;
                $pub_own_as_add_courses[$count]['already_added'] = 1;

                if ($already_taken_course == 4) {
                    $pub_own_as_add_courses[$count]['prerequiste_failed'] = 1;
                }

                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $pub_own_as_add_courses[$count]['PublishedCourse']['grade_scale_id'] = $examGradeTable->getPublishedCourseGradeGradeScale($ownValue['PublishedCourse']['id']);
            } else {
                $pub_own_as_add_courses[$count] = $ownValue;

                $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $gradeScaleTable = TableRegistry::getTableLocator()->get('GradeScales');
                $pub_own_as_add_courses[$count]['PublishedCourse']['grade_scale_id'] = $examGradeTable->getPublishedCourseGradeGradeScale($ownValue['PublishedCourse']['id'])
                    ?: $gradeScaleTable->getGradeScaleIdGivenPublishedCourse($ownValue['PublishedCourse']['id']);
                $pub_own_as_add_courses[$count]['already_added'] = 0;
            }

            $count++;
        }

        return $pub_own_as_add_courses;
    }


    public function viewPdf($id = null)
    {
        if (!$id) {
            $this->Flash->error('Sorry, not able to generate Pdf.');
            return $this->redirect(['action' => 'index']);
        }

        $view_only = true;
        $examTypeTable = TableRegistry::getTableLocator()->get('ExamTypes');
        $exam_types = $examTypeTable->find()
            ->select(['id', 'exam_name', 'percent', 'order', 'mandatory'])
            ->where(['ExamTypes.published_course_id' => $id])
            ->order(['order' => 'ASC'])
            ->toArray();

        $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $published_course_detail = $publish_course_detail_info = $publishedCourseTable->find()
            ->where(['PublishedCourses.id' => $id])
            ->contain([
                'Courses' => ['CourseCategories'],
                'Sections' => ['YearLevels'],
                'Programs',
                'ProgramTypes',
                'Departments' => ['Colleges'],
                'CourseInstructorAssignments' => [
                    'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                    'Staffs'
                ]
            ])
            ->first();

        $student_course_register_and_adds = $publishedCourseTable->getStudentsTakingPublishedCourse($id);
        $students = $student_course_register_and_adds['register'];
        $student_adds = $student_course_register_and_adds['add'];
        $student_makeup = $student_course_register_and_adds['makeup'];

        $total_student_count = count($students) + count($student_adds) + count($student_makeup);

        $universityTable = TableRegistry::getTableLocator()->get('Universities');
        $university = $universityTable->getSectionUniversity($publish_course_detail_info->section_id);

        $filename = "Grade_Sheet_" . str_replace(' ', '_', trim(str_replace('  ', ' ', $publish_course_detail_info->section->name))) . '_' .
            str_replace('/', '-', $publish_course_detail_info->academic_year) . '_' . $publish_course_detail_info->semester;

        $this->set(compact(
            'published_course_detail',
            'students',
            'exam_types',
            'student_adds',
            'student_makeup',
            'filename',
            'university',
            'publish_course_detail_info',
            'view_only',
            'total_student_count'
        ));

        $this->response = $this->response->withType('application/pdf');
        $this->viewBuilder()->setLayout('pdf/default');
        $this->render('Elements/marksheet_grade_pdf');
    }

    public function viewXls($id = null)
    {
        if (!$id) {
            $this->Flash->error('Sorry, unable to generate Excel File.');
            return $this->redirect(['action' => 'index']);
        }

        $view_only = true;

        $examTypeTable = TableRegistry::getTableLocator()->get('ExamTypes');
        $exam_types = $examTypeTable->find()
            ->select(['id', 'exam_name', 'percent', 'order', 'mandatory'])
            ->where(['ExamTypes.published_course_id' => $id])
            ->order(['order' => 'ASC'])
            ->toArray();

        $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $published_course_detail = $publish_course_detail_info = $publishedCourseTable->find()
            ->where(['PublishedCourses.id' => $id])
            ->contain([
                'Courses' => ['CourseCategories'],
                'Sections' => ['YearLevels'],
                'Programs',
                'ProgramTypes',
                'Departments' => ['Colleges'],
                'Colleges',
                'CourseInstructorAssignments' => [
                    'conditions' => ['CourseInstructorAssignments.isprimary' => 1],
                    'Staffs' => [
                        'Titles' => ['fields' => ['id', 'title']]
                    ]
                ]
            ])
            ->first();

        $student_course_register_and_adds = $publishedCourseTable->getStudentsTakingPublishedCourse($id);
        $students = $student_course_register_and_adds['register'];
        $student_adds = $student_course_register_and_adds['add'];
        $student_makeup = $student_course_register_and_adds['makeup'];

        $total_student_count = count($students) + count($student_adds) + count($student_makeup);

        $universityTable = TableRegistry::getTableLocator()->get('Universities');
        $university = $universityTable->getSectionUniversity($publish_course_detail_info->section_id);

        $semester_map = [
            'I' => '1st',
            'II' => '2nd',
            'III' => '3rd'
        ];
        $semester_display = isset($semester_map[$publish_course_detail_info->semester])
            ? $semester_map[$publish_course_detail_info->semester]
            : $publish_course_detail_info->semester;

        $filename = "Mark_Sheet_" . $publish_course_detail_info->course->course_code . '_' .
            str_replace(' ', '_', trim(str_replace('  ', ' ', $publish_course_detail_info->section->name))) . '_' .
            str_replace('/', '-', $publish_course_detail_info->academic_year) . '_' .
            $semester_display . '_semester_' . Time::now()->format('Y-m-d');

        $this->set(compact(
            'published_course_detail',
            'students',
            'exam_types',
            'student_adds',
            'student_makeup',
            'filename',
            'university',
            'publish_course_detail_info',
            'view_only',
            'total_student_count'
        ));

        $this->viewBuilder()->setLayout(false);
        $this->render('Elements/marksheet_grade_xls');
    }

    public function requestFxExamSit()
    {
        if (!empty($this->student_id)) {
            $this->getFxGrade($this->student_id);
        } else {
            $this->getFxGrade(0);
        }
        $this->render('request_fx_exam_sit');
    }

    protected function getFxGrade($student_id = 0)
    {
        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $fx_grade_change = $examGradeTable->getListOfFXGradeChangeForStudentChoice($student_id);
        debug($fx_grade_change);

        $fxResitRequestTable = TableRegistry::getTableLocator()->get('FxResitRequests');
        $applied_request = $fxResitRequestTable->doesFxAppliedandQuotaUsed($this->student_id, $this->AcademicYear->currentAcademicYear());
        debug($applied_request);

        if ($applied_request == 2) {
            $this->Flash->error('You have already applied one Fx exam retake and it is only allowed one course per semester to retake FX exam based on the new legislation.');
            // return $this->redirect(['action' => 'viewFxResit']);
        } elseif ($applied_request == 3) {
            $this->Flash->error('You have finished 3 Fx examination retake and based on the new legislation you are allowed to 4 Fx throughout your stay at the university.');
            return $this->redirect(['action' => 'viewFxResit']);
        }

        if (!empty($this->request->getData())) {
            $selectedCourseCount = 0;
            $selectedCourseDetail = null;
            foreach ($this->request->getData('FxResitRequest') as $fv) {
                if ($fv['selected_id'] == 1) {
                    $selectedCourseCount++;
                    $selectedCourseDetail['FxResitRequest'] = $fv;
                }
            }

            if ($selectedCourseCount > 1) {
                $this->Flash->error('You are allowed only to apply for one fx exam sit, please select only one course.');
            } else {
                if (!empty($selectedCourseDetail['FxResitRequest']['course_registration_id'])) {
                    $doesStudentAppliedFxSit = $fxResitRequestTable->doesStudentAppliedFxSit($selectedCourseDetail['FxResitRequest']['course_registration_id'], 1);
                } elseif (!empty($selectedCourseDetail['FxResitRequest']['course_add_id'])) {
                    $doesStudentAppliedFxSit = $fxResitRequestTable->doesStudentAppliedFxSit($selectedCourseDetail['FxResitRequest']['course_add_id'], 0);
                }

                if ($doesStudentAppliedFxSit) {
                    $this->Flash->error('You have already applied for Fx exam for the course, you can not apply now.');
                } elseif (!empty($selectedCourseDetail)) {
                    $fxResitRequest = $fxResitRequestTable->newEntity($selectedCourseDetail);
                    if ($fxResitRequestTable->save($fxResitRequest)) {
                        $this->Flash->success('Thank you, you have applied to Fx exam resit and your application will be dispatched to the instructor.');
                    }
                }
            }
        }

        $this->set(compact('applied_request', 'fx_grade_change'));
    }

    public function viewFxResit()
    {
        $publishedCourseTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $options = [
            'contain' => [
                'Courses',
                'FxResitRequests' => ['Students']
            ]
        ];

        if (!empty($this->student_id)) {
            $options['conditions'][] = ['PublishedCourses.id IN' => $publishedCourseTable->FxResitRequests->find()->select(['published_course_id'])->where(['student_id' => $this->student_id])];
        } else {
            $options['conditions'][] = ['PublishedCourses.id IN' => $publishedCourseTable->FxResitRequests->find()->select(['published_course_id'])->where(['published_course_id IS NOT NULL'])];
            if (!empty($this->department_id)) {
                $options['conditions']['PublishedCourses.given_by_department_id'] = $this->department_id;
            } elseif (!empty($this->department_ids)) {
                $options['conditions']['PublishedCourses.given_by_department_id IN'] = $this->department_ids;
            }
        }

        if (!empty($this->request->getData('viewFxApplication'))) {
            if (!empty($this->student_id)) {
                $options['conditions']['PublishedCourses.academic_year'] = $this->request->getData('ExamGrade.academic_year');
                $options['conditions']['PublishedCourses.semester'] = $this->request->getData('ExamGrade.semester');
                $options['conditions'][] = ['PublishedCourses.id IN' => $publishedCourseTable->FxResitRequests->find()->select(['published_course_id'])->where(['student_id' => $this->student_id])];
                debug($options);
            } else {
                $options['conditions']['PublishedCourses.academic_year'] = $this->request->getData('ExamGrade.academic_year');
                $options['conditions']['PublishedCourses.semester'] = $this->request->getData('ExamGrade.semester');
                if (!empty($this->department_id)) {
                    $options['conditions']['PublishedCourses.department_id'] = $this->department_id;
                } elseif (!empty($this->department_ids)) {
                    $options['conditions']['PublishedCourses.department_id IN'] = $this->department_ids;
                }
            }
            $fxRequests = $publishedCourseTable->find('all', $options)->toArray();
        } else {
            $fxRequests = $publishedCourseTable->find('all', $options)->toArray();
            debug($options);
        }

        if ($this->role_id == ROLE_STUDENT && !empty($this->student_id)) {
            foreach ($fxRequests as &$fxx) {
                foreach ($fxx->fx_resit_requests as $kxx => $kr) {
                    if ($kr->student_id != $this->student_id) {
                        unset($fxx->fx_resit_requests[$kxx]);
                    }
                }
            }
        }

        $this->set(compact('fxRequests'));
    }

    public function cancelFxResitRequest($id = null)
    {
        if (!$id) {
            $this->Flash->error('Invalid request.');
            return $this->redirect(['action' => 'requestFxExamSit']);
        }

        $fxResitRequestTable = TableRegistry::getTableLocator()->get('FxResitRequests');
        $isUserEligibleToDelete = $fxResitRequestTable->find()
            ->where([
                'FxResitRequests.student_id' => $this->student_id,
                'FxResitRequests.id' => $id
            ])
            ->first();

        if (!empty($isUserEligibleToDelete)) {
            $reg = !empty($isUserEligibleToDelete->course_registration_id) ? 1 : 0;
            $reg_add_id = !empty($isUserEligibleToDelete->course_registration_id) ? $isUserEligibleToDelete->course_registration_id : $isUserEligibleToDelete->course_add_id;
            $makeupExamTable = TableRegistry::getTableLocator()->get('MakeupExams');
            $departmentAssignedFxToInstructor = $makeupExamTable->makeUpExamApplied($this->student_id, $isUserEligibleToDelete->published_course_id, $reg_add_id, $reg);

            if ($departmentAssignedFxToInstructor) {
                $this->Flash->error('Your request has already been assigned to instructor for exam retake.');
                return $this->redirect(['action' => 'requestFxExamSit']);
            } elseif ($fxResitRequestTable->delete($fxResitRequestTable->get($id))) {
                $this->Flash->success('You have successfully cancelled your request.');
                return $this->redirect(['action' => 'requestFxExamSit']);
            }
        }

        return $this->redirect(['action' => 'requestFxExamSit']);
    }


    public function cancelNgGrade()
    {
        if (!empty($this->request->getData('cancelNGGrade'))) {
            $gradeToBeCancelled = [];
            $courseAddandRegistrationExamGradeIds = [];
            $exam_grade_change_ids_to_delete = [];
            $exam_grade_ids_to_delete = [];
            $student_ids_to_regenerate_status = [];

            $ng_grades_without_any_assessment = [];
            $ng_grades_registration_ids_without_any_assessment = [];
            $ng_grades_add_ids_without_any_assessment = [];
            $ng_grades_makeup_ids_without_any_assessment = [];

            if (!empty($this->request->getData('ExamGrade'))) {
                foreach ($this->request->getData('ExamGrade') as $key => $student) {
                    if (is_int($key) && $student['gp'] == 1) {
                        $courseAddandRegistrationExamGradeIds['ExamGrade'][] = $student['id'];
                        $exam_grade_ids_to_delete[] = $student['id'];

                        if (!in_array($student['student_id'], $student_ids_to_regenerate_status)) {
                            $student_ids_to_regenerate_status[] = $student['student_id'];
                        }

                        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                        $tmp = $examGradeTable->find()
                            ->where(['ExamGrades.id' => $student['id']])
                            ->contain([
                                'CourseAdds' => [
                                    'ExamResults' => [
                                        'conditions' => ['ExamResults.course_add' => 0],
                                        'limit' => 1
                                    ]
                                ],
                                'CourseRegistrations' => [
                                    'ExamResults' => ['limit' => 1]
                                ],
                                'MakeupExams' => [
                                    'ExamResults' => ['limit' => 1]
                                ],
                                'ExamGradeChanges'
                            ])
                            ->first();

                        debug($tmp);

                        if (!empty($tmp->exam_grade_changes)) {
                            foreach ($tmp->exam_grade_changes as $exGrChange) {
                                debug($exGrChange->id);
                                debug($exGrChange->exam_grade_id);
                                $exam_grade_change_ids_to_delete[] = $exGrChange->id;
                            }
                        }

                        if (!empty($tmp->course_registration) && !empty($tmp->course_registration->id)) {
                            $courseAddandRegistrationExamGradeIds['CourseRegistration'][] = $tmp->course_registration->id;
                            if (empty($tmp->course_registration->exam_results)) {
                                debug($tmp->course_registration->exam_results);
                                $ng_grades_without_any_assessment['ExamGrade'][] = $student['id'];
                                $ng_grades_registration_ids_without_any_assessment['CourseRegistration'][] = $tmp->course_registration->id;
                            }
                        } elseif (!empty($tmp->course_add) && !empty($tmp->course_add->id)) {
                            $courseAddandRegistrationExamGradeIds['CourseAdd'][] = $tmp->course_add->id;
                            if (empty($tmp->course_add->exam_results)) {
                                debug($tmp->course_add->exam_results);
                                $ng_grades_without_any_assessment['ExamGrade'][] = $student['id'];
                                $ng_grades_add_ids_without_any_assessment['CourseAdd'][] = $tmp->course_add->id;
                            }
                        } elseif (!empty($tmp->makeup_exam) && !empty($tmp->makeup_exam->id)) {
                            $courseAddandRegistrationExamGradeIds['MakeupExam'][] = $tmp->makeup_exam->id;
                            if (empty($tmp->makeup_exam->exam_results)) {
                                debug($tmp->makeup_exam->exam_results);
                                $ng_grades_without_any_assessment['ExamGrade'][] = $student['id'];
                                $ng_grades_makeup_ids_without_any_assessment['MakeupExam'][] = $tmp->makeup_exam->id;
                            }
                        }
                    }
                }
            }

            debug($courseAddandRegistrationExamGradeIds);
            debug($exam_grade_change_ids_to_delete);
            debug($exam_grade_ids_to_delete);
            debug($student_ids_to_regenerate_status);

            $students_count = count($student_ids_to_regenerate_status);
            $regenerated_students_count = 0;

            if (!empty($courseAddandRegistrationExamGradeIds['ExamGrade'])) {
                $deleted_grades_without_assessment = 0;

                if (!empty($ng_grades_without_any_assessment)) {
                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    if ($examGradeTable->deleteAll(['ExamGrades.id IN' => $ng_grades_without_any_assessment['ExamGrade']], false)) {
                        if (!empty($ng_grades_registration_ids_without_any_assessment)) {
                            $examGradeTable->CourseRegistrations->deleteAll(['CourseRegistrations.id IN' => $ng_grades_registration_ids_without_any_assessment['CourseRegistration']], false);
                            $deleted_grades_without_assessment += count($ng_grades_registration_ids_without_any_assessment['CourseRegistration']);
                        }

                        if (!empty($ng_grades_add_ids_without_any_assessment)) {
                            $examGradeTable->CourseAdds->deleteAll(['CourseAdds.id IN' => $ng_grades_add_ids_without_any_assessment['CourseAdd']], false);
                            $deleted_grades_without_assessment += count($ng_grades_add_ids_without_any_assessment['CourseAdd']);
                        }

                        if (!empty($ng_grades_makeup_ids_without_any_assessment)) {
                            $examGradeTable->MakeupExams->deleteAll(['MakeupExams.id IN' => $ng_grades_makeup_ids_without_any_assessment['MakeupExam']], false);
                            $deleted_grades_without_assessment += count($ng_grades_makeup_ids_without_any_assessment['MakeupExam']);
                        }
                    }

                    debug('Empty Grades without any assessment: ' . count($ng_grades_without_any_assessment));
                    debug('Deleted grades without any assessment: ' . $deleted_grades_without_assessment);
                }

                if (!empty($courseAddandRegistrationExamGradeIds['CourseRegistration'])) {
                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    if (Configure::read('DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION') && false) {
                        if ($examGradeTable->CourseRegistrations->deleteAll(['CourseRegistrations.id IN' => $courseAddandRegistrationExamGradeIds['CourseRegistration']], false)) {
                            $examGradeTable->deleteAll(['ExamGrades.id IN' => $courseAddandRegistrationExamGradeIds['ExamGrade']], false);
                            $this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG grades and registration.');
                        }
                    } else {
                        if ($examGradeTable->deleteAll(['ExamGrades.id IN' => $courseAddandRegistrationExamGradeIds['ExamGrade']], false)) {
                            $this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG ' . (count($courseAddandRegistrationExamGradeIds['ExamGrade']) > 1 ? 'grades' : 'grade') . '. Course Registration data and Assessment data is not affected.');
                        }
                    }
                }

                if (!empty($courseAddandRegistrationExamGradeIds['CourseAdd'])) {
                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    if (Configure::read('DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION') && false) {
                        if ($examGradeTable->CourseAdds->deleteAll(['CourseAdds.id IN' => $courseAddandRegistrationExamGradeIds['CourseAdd']], false)) {
                            $examGradeTable->deleteAll(['ExamGrades.id IN' => $courseAddandRegistrationExamGradeIds['ExamGrade']], false);
                            $this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG grades and course adds.');
                        }
                    } else {
                        if ($examGradeTable->deleteAll(['ExamGrades.id IN' => $courseAddandRegistrationExamGradeIds['ExamGrade']], false)) {
                            $this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG ' . (count($courseAddandRegistrationExamGradeIds['ExamGrade']) > 1 ? 'grades' : 'grade') . '. Course Add data and Assessment data is not affected.');
                        }
                    }
                }

                if (!empty($courseAddandRegistrationExamGradeIds['MakeupExam'])) {
                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    if (Configure::read('DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION') && false) {
                        if ($examGradeTable->MakeupExams->deleteAll(['MakeupExams.id IN' => $courseAddandRegistrationExamGradeIds['MakeupExam']], false)) {
                            $examGradeTable->deleteAll(['ExamGrades.id IN' => $courseAddandRegistrationExamGradeIds['ExamGrade']], false);
                            $this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG grades and course adds.');
                        }
                    } else {
                        if ($examGradeTable->deleteAll(['ExamGrades.id IN' => $courseAddandRegistrationExamGradeIds['ExamGrade']], false)) {
                            $this->Flash->success('You have cancelled ' . count($courseAddandRegistrationExamGradeIds['ExamGrade']) . ' NG ' . (count($courseAddandRegistrationExamGradeIds['ExamGrade']) > 1 ? 'grades' : 'grade') . '. Makeup data and Assessment data is not affected.');
                        }
                    }
                }

                if (!empty($exam_grade_change_ids_to_delete)) {
                    $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                    debug($examGradeTable->ExamGradeChanges->deleteAll(['ExamGradeChanges.id IN' => $exam_grade_change_ids_to_delete], false));
                }

                if (!empty($student_ids_to_regenerate_status)) {
                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                    foreach ($student_ids_to_regenerate_status as $stdnt_id) {
                        $status_status = $studentExamStatusTable->regenerate_all_status_of_student_by_student_id($stdnt_id, 0);

                        if ($status_status == 3) {
                            // Status regenerated in last 1 week, check for possible changes
                        } else {
                            $regenerated_students_count++;
                        }
                    }
                }

                if (!empty($this->request->getData('ExamGrade.select_all'))) {
                    unset($this->request->data['ExamGrade']['select_all']);
                }
            }
        }

        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $applicable_grades = [
            'F' => 'F',
            'I' => 'I (Incomplete)',
            'DO' => 'DO (Dropout)',
            'W' => 'W (Withdraw)'
        ];

        if (!empty($this->request->getData('listPublishedCourses'))) {
            $type = (!empty($this->college_ids) || count(explode('~', $this->request->getData('ExamGrade.department_id'))) > 1) ? 1 : 0;

            $selected_academicyear = !empty($this->request->getData('ExamGrade.academic_year'))
                ? $this->request->getData('ExamGrade.academic_year')
                : $defaultacademicyear;

            $selected_programs = !empty($this->request->getData('ExamGrade.program_id'))
                ? $this->request->getData('ExamGrade.program_id')
                : $this->program_ids;

            $selected_program_types = !empty($this->request->getData('ExamGrade.program_type_id'))
                ? $this->request->getData('ExamGrade.program_type_id')
                : $this->program_type_ids;

            $selected_semester = !empty($this->request->getData('ExamGrade.semester'))
                ? $this->request->getData('ExamGrade.semester')
                : null;

            if (!empty($this->request->getData('ExamGrade.department_id')) || !empty($this->request->getData('ExamGrade.college_id'))) {
                $coll_id = !empty($this->request->getData('ExamGrade.department_id'))
                    ? explode('~', $this->request->getData('ExamGrade.department_id'))
                    : [];

                $selected_dept_coll_id = count($coll_id) > 1
                    ? $coll_id[1]
                    : (!empty($this->request->getData('ExamGrade.college_id'))
                        ? $this->request->getData('ExamGrade.college_id')
                        : (!empty($this->request->getData('ExamGrade.department_id'))
                            ? $this->request->getData('ExamGrade.department_id')
                            : (!empty($this->college_ids)
                                ? array_values($this->college_ids)[0]
                                : array_values($this->department_ids)[0])));
            } else {
                $selected_dept_coll_id = !empty($this->college_ids)
                    ? array_values($this->college_ids)[0]
                    : (!empty($this->department_ids)
                        ? array_values($this->department_ids)[0]
                        : null);
            }

            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $examGradeChanges = $examGradeTable->getListOfNGGrade(
                $selected_academicyear,
                $selected_semester,
                $selected_dept_coll_id,
                $selected_programs,
                $selected_program_types,
                !empty($this->request->getData('ExamGrade.grade')) ? $this->request->getData('ExamGrade.grade') : 0,
                $type
            );

            $turn_off_search = true;

            if (empty($examGradeChanges)) {
                $this->Flash->info('No auto or manual NG to ' . (!empty($this->request->getData('ExamGrade.grade')) ? $this->request->getData('ExamGrade.grade') : implode(', ', array_keys($applicable_grades))) . ' converted grade is found using the given search criteria.');
            } else {
                $turn_off_search = true;
            }

            $this->set(compact('examGradeChanges', 'turn_off_search'));
        }

        if (!empty($this->college_ids)) {
            $colleges = TableRegistry::getTableLocator()->get('Colleges')->find('list')
                ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                ->toArray();
            $departments = [];
        } elseif (!empty($this->department_ids)) {
            $departments = TableRegistry::getTableLocator()->get('Departments')->find('list')
                ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                ->toArray();
            $colleges = [];
        }

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) {
            $departments = TableRegistry::getTableLocator()->get('Departments')->allDepartmentInCollegeIncludingPre($this->department_ids, $this->college_ids, 1, 1);
        }

        if (!empty($this->request->getData('ExamGrade.select_all'))) {
            unset($this->request->data['ExamGrade']['select_all']);
        }

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $programs = $examGradeTable->CourseRegistrations->PublishedCourses->Programs->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();
        $programTypes = $examGradeTable->CourseRegistrations->PublishedCourses->ProgramTypes->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $current_acy = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION) && YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION) {
            $acyear_list = $this->AcademicYear->academicYearInArray(
                (explode('/', $current_acy)[0] - YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION),
                explode('/', $current_acy)[0]
            );
        } elseif (is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
            $acyear_list = $this->AcademicYear->academicYearInArray(
                (explode('/', $current_acy)[0] - ACY_BACK_FOR_ALL),
                explode('/', $current_acy)[0]
            );
        } else {
            $acyear_list = [$current_acy => $current_acy];
        }

        if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1) {
            $acyear_list = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, explode('/', $current_acy)[0]);
        }

        $this->set(compact('departments', 'colleges', 'acyear_list', 'applicable_grades'));
    }

    public function masterSheetRemedial($section_id = null, $ay1 = '2023', $ay2 = '24', $semester = 'I', $selected_program_id = '', $selected_program_type_id = '', $compact_version = '')
    {
        $current_acy = $this->AcademicYear->currentAcademicYear();

        $program_id = !empty($selected_program_id) ? $selected_program_id : PROGRAM_REMEDIAL;
        $program_type_id = !empty($selected_program_type_id) ? $selected_program_type_id : PROGRAM_TYPE_REGULAR;

        $acyear_list = $this->AcademicYear->academicYearInArray(
            (explode('/', $current_acy)[0] - 2),
            explode('/', $current_acy)[0]
        );

        $compact_version_checked = !empty($compact_version) ? 1 : 0;

        $programsss = [PROGRAM_REMEDIAL => 'Remedial'];
        $programTypesss = [
            PROGRAM_TYPE_REGULAR => 'Regular',
            PROGRAM_TYPE_EVENING => 'Evening',
            PROGRAM_TYPE_WEEKEND => 'Weekend'
        ];

        $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $remedial_sections = $examGradeTable->CourseRegistrations->Students->Sections->find('list')
            ->where([
                'Sections.program_id' => $program_id,
                'Sections.program_type_id' => $program_type_id,
                'Sections.academic_year' => $current_acy
            ])
            ->order([
                'Sections.year_level_id' => 'ASC',
                'Sections.college_id' => 'ASC',
                'Sections.department_id' => 'ASC',
                'Sections.id' => 'ASC',
                'Sections.name' => 'ASC'
            ])
            ->toArray();

        if (!empty($remedial_sections)) {
            $remedial_sections = [0 => '[ Select Section ]'] + $remedial_sections;
        }

        debug($remedial_sections);

        $this->set(compact(
            'acyear_list',
            'programsss',
            'programTypesss',
            'remedial_sections',
            'program_id',
            'program_type_id',
            'compact_version_checked'
        ));

        if (!empty($section_id) && $section_id > 0) {
            $section_combo_id = $section_or_published_course_id = $section_id;
            $academic_year = $ay1 . '/' . $ay2;

            $section_details = $examGradeTable->CourseRegistrations->Students->Sections->find()
                ->where(['Sections.id' => $section_or_published_course_id])
                ->contain([
                    'Departments',
                    'Colleges',
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']]
                ])
                ->first();

            $course_ids = $examGradeTable->CourseRegistrations->PublishedCourses->find('list')
                ->where(['PublishedCourses.section_id' => $section_id])
                ->select(['PublishedCourses.course_id'])
                ->toArray();

            $master_sheet = $examGradeTable->getMasterSheetRemedial($section_or_published_course_id, $academic_year, $semester);

            $section_detail = $section_details->toArray()['Section'];
            $department_detail = $section_details->toArray()['Department'];
            $college_detail = $section_details->toArray()['College'];
            $program_detail = $section_details->toArray()['Program'];
            $program_type_detail = $section_details->toArray()['ProgramType'];

            $program_id = $section_details->program->id;
            $program_type_id = $section_details->program_type->id;
            $department_id = $section_details->department->id;
            $academic_year_selected = $academic_year;
            $semester_selected = $semester;

            // Store to session for excel
            $this->request->getSession()->write('master_sheet', $master_sheet);
            $this->request->getSession()->write('section_detail', $section_detail);
            $this->request->getSession()->write('department_detail', $department_detail);
            $this->request->getSession()->write('college_detail', $college_detail);
            $this->request->getSession()->write('program_detail', $program_detail);
            $this->request->getSession()->write('program_type_detail', $program_type_detail);
            $this->request->getSession()->write('program_id', $program_id);
            $this->request->getSession()->write('program_type_id', $program_type_id);
            $this->request->getSession()->write('department_id', $department_id);
            $this->request->getSession()->write('academic_year_selected', $academic_year_selected);
            $this->request->getSession()->write('semester_selected', $semester_selected);
            $this->request->getSession()->write('compact_version', $compact_version);

            $this->set(compact(
                'master_sheet',
                'section_detail',
                'college_detail',
                'department_detail',
                'program_detail',
                'program_type_detail',
                'academic_year',
                'semester',
                'program_id',
                'program_type_id',
                'department_id',
                'academic_year_selected',
                'semester_selected',
                'acyear_list',
                'programsss',
                'programTypesss',
                'section_combo_id'
            ));
        }

        $this->render('master_sheet_remedial');
    }


    public function exportRemedialMastersheetXls()
    {
        $this->viewBuilder()->setLayout(false);

        $master_sheet = $this->request->getSession()->read('master_sheet');
        $section_detail = $this->request->getSession()->read('section_detail');
        $department_detail = $this->request->getSession()->read('department_detail');
        $college_detail = $this->request->getSession()->read('college_detail');
        $program_detail = $this->request->getSession()->read('program_detail');
        $program_type_detail = $this->request->getSession()->read('program_type_detail');
        $program_id = $this->request->getSession()->read('program_id');
        $program_type_id = $this->request->getSession()->read('program_type_id');
        $department_id = $this->request->getSession()->read('department_id');
        $academic_year = $this->request->getSession()->read('academic_year_selected');
        $semester = $this->request->getSession()->read('semester_selected');
        $compact_version = $this->request->getSession()->read('compact_version');

        $filename = "Remedial_Master_Sheet_" . str_replace(' ', '_', trim(str_replace('  ', ' ', $section_detail['name']))) . '_' .
            str_replace('/', '-', $academic_year) . '_' . $semester . '_' . Time::now()->format('Y-m-d');

        $this->set(compact(
            'master_sheet',
            'section_detail',
            'college_detail',
            'department_detail',
            'program_detail',
            'program_type_detail',
            'program_id',
            'program_type_id',
            'filename',
            'department_id',
            'academic_year',
            'semester'
        ));

        if ($compact_version) {
            $this->render('Element/remedial_master_sheet_compact_xls');
        } else {
            $this->render('Element/remedial_master_sheet_xls');
        }
    }

    public function getRemedialSectionsCombo($parameters)
    {
        $this->viewBuilder()->setLayout('ajax');

        $criteriaLists = explode('~', $parameters);
        debug($criteriaLists);

        if (!empty($criteriaLists) && count($criteriaLists) > 3) {
            $academicYear = str_replace('-', '/', $criteriaLists[0]);
            $semester = $criteriaLists[1];
            $program_id = $criteriaLists[2];
            $program_type_id = $criteriaLists[3];

            $sectionTable = TableRegistry::getTableLocator()->get('Sections');
            $options = [
                'conditions' => [
                    'Sections.academic_year' => $academicYear,
                    'Sections.program_id' => $program_id,
                    'Sections.program_type_id' => $program_type_id
                ],
                'contain' => [
                    'Programs',
                    'ProgramTypes',
                    'Departments',
                    'YearLevels',
                    'Colleges',
                    'PublishedCourses'
                ],
                'order' => [
                    'Sections.year_level_id' => 'ASC',
                    'Sections.college_id' => 'ASC',
                    'Sections.department_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ]
            ];

            debug($options);

            $sections = $sectionTable->find('all', $options)->toArray();

            $remedialSectionOrganized = [];

            if (!empty($sections)) {
                $remedialSectionOrganized[''] = '[ Select Section ]';
                foreach ($sections as $v) {
                    if (!empty($v->year_level->name)) {
                        $remedialSectionOrganized[$v->id] = $v->name . " (" . $v->academic_year . ", " . $v->year_level->name . ")";
                    } else {
                        $remedialSectionOrganized[$v->id] = $v->name . " (" . $v->academic_year . ", " . ($v->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . ")";
                    }
                }
            } else {
                $remedialSectionOrganized[''] = '[ No Active Sections, Try Changing Filters ]';
            }
        }

        $this->set(compact('remedialSectionOrganized'));
    }
}
