<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;

class CourseRegistrationsController extends AppController
{
    public $menuOptions = [
        'parent' => 'registrations',
        'exclude' => [
            'getCourseRegisteredGradeList',
            'getCourseRegisteredGradeResult',
            'getCourseCategoryCombo',
            'search',
            'showCourseRegistredStudents',
            'getSectionCombo',
            'getSectionComboForView',
            'getIndividualRegistration',
            'manageMissingRegistration',
            'updateMissingRegistration',
            'search',
        ],
        'alias' => [
            'index' => 'View All Registration',
            'cancelIndividualRegistration' => 'Cancel Student\'s Registration',
            'cancelRegistration' => 'Cancel Section\'s Registration',
            'registerIndividualCourse' => 'Register Courses By Section',
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Flash');

    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        if ($this->Auth->user('role_id') == ROLE_STUDENT) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $student = $studentsTable->find()
                ->where(['Students.id' => $this->Auth->user('student_id')])
                ->first();

            if (empty($student->ecardnumber) &&
                !empty($this->Auth->user('id')) &&
                $this->request->getParam('controller') !== 'Students' &&
                $this->request->getParam('action') !== 'change') {
                return $this->redirect(['controller' => 'Students', 'action' => 'change']);
            }
        }
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        $defaultAcademicYear = $this->AcademicYear->currentAcademicYear();

        if (defined('ACY_BACK_COURSE_REGISTRATION') && is_numeric(ACY_BACK_COURSE_REGISTRATION)) {
            $startYear = explode('/', $defaultAcademicYear)[0] - ACY_BACK_COURSE_REGISTRATION;
            $endYear = explode('/', $defaultAcademicYear)[0];
            $acyearArrayData = $this->AcademicYear->academicYearInArray($startYear, $endYear);
            $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated($startYear, $endYear);
            $defaultAcademicYearMinusSeparated = str_replace('/', '-', $defaultAcademicYear);
        } else {
            $acyearArrayData = [$defaultAcademicYear => $defaultAcademicYear];
            $defaultAcademicYearMinusSeparated = str_replace('/', '-', $defaultAcademicYear);
            $acYearMinuSeparated = [$defaultAcademicYearMinusSeparated => $defaultAcademicYearMinusSeparated];
        }

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = $programsTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();
        $programTypes = $programTypesTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $yearLevels = $this->year_levels;

        $this->set(compact(
            'acyearArrayData',
            'programTypes',
            'programs',
            'acYearMinuSeparated',
            'defaultAcademicYear',
            'defaultAcademicYearMinusSeparated',
            'yearLevels'
        ));
    }

    public function search()
    {
        $url = ['action' => 'index'];

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData())) {
            foreach ($this->request->getData() as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (!empty($vv) && is_array($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url["{$k}.{$kk}.{$kkk}"] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url["{$k}.{$kk}"] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }

    public function initSearchIndex()
    {
        $session = $this->request->getSession();
        if (!empty($this->request->getData('Search'))) {
            $session->write('search_data_index', $this->request->getData('Search'));
        } elseif ($session->check('search_data_index')) {
            $this->request = $this->request->withData('Search', $session->read('search_data_index'));
        }
    }

    public function register()
    {
        if ($this->Auth->user('student_id')) {
            $session = $this->request->getSession();
            $user = $session->read('Auth.User');

            if ($user['role_id'] == ROLE_STUDENT && !empty($user['id'])) {
                $studentStatusPatternTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');
                $isExitExamEligible = $studentStatusPatternTable->isEligibleForExitExam($this->Auth->user('student_id'));

                $isNotProfilePage = $this->request->getParam('action') !== 'profile';
                $isNotUsersPage = $this->request->getParam('controller') !== 'Users';
                $isNotChangePwdPage = $this->request->getParam('action') !== 'changePwd';

                if (($isExitExamEligible || defined('FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE')
                        && FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE == 1) &&
                    $isNotProfilePage && $isNotUsersPage && $isNotChangePwdPage) {
                    if (!$studentStatusPatternTable->completedFillingProfileInfomation($this->Auth->user('student_id'))) {
                        $this->Flash->warning(__(
                            'Dear {0}, before proceeding, you must complete your basic profile. If you encounter an error, are unable to update your profile on your own, or require further assistance, please report to the registrar record officer assigned to your department.',
                            $user['first_name']
                        ));
                        return $this->redirect(['controller' => 'Students', 'action' => 'profile']);
                    }
                }

                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $studentDetails = $studentsTable->find()
                    ->select(['studentnumber', 'country_id', 'faida_identification_number', 'faida_alias_number'])
                    ->where(['Students.id' => $this->Auth->user('student_id')])
                    ->first();

                $isEthiopianStudent = !empty($studentDetails->country_id) && (int)$studentDetails->country_id == COUNTRY_ID_OF_ETHIOPIA;
                $isFaidaFinFilled = !empty($studentDetails->faida_identification_number);
                $isFaidaFanFilled = !empty($studentDetails->faida_alias_number);

                if ($isEthiopianStudent && (!$isFaidaFinFilled || !$isFaidaFanFilled) &&
                    ($isExitExamEligible || defined('FORCE_ALL_STUDENTS_TO_FILL_FAIDA_FIN')
                        && FORCE_ALL_STUDENTS_TO_FILL_FAIDA_FIN == 1) &&
                    $isNotProfilePage && $isNotUsersPage && $isNotChangePwdPage) {
                    if (!$isFaidaFinFilled && !$isFaidaFanFilled) {
                        $this->Flash->info(__(
                            'Dear {0}, before proceeding, you must update your Fayda Identification Number (FIN) and Fayda Alias Number (FAN). Ensure that you provide the correct 16-digit FAN, located on the front, and the 12-digit FIN, found on the back of your national Fayda ID card.',
                            $user['first_name']
                        ));
                    } elseif (!$isFaidaFinFilled) {
                        $this->Flash->info(__(
                            'Dear {0}, before proceeding, you must update your Fayda Identification Number (FIN). Please ensure that you provide the correct 12-digit FIN, located on the back of your national Fayda ID card.',
                            $user['first_name']
                        ));
                    } else {
                        $this->Flash->info(__(
                            'Dear {0}, before proceeding, you must update your Fayda Alias Number (FAN). Please ensure that you provide the correct 16-digit FAN, located on the front of your national Fayda ID card.',
                            $user['first_name']
                        ));
                    }
                    return $this->redirect(['controller' => 'Students', 'action' => 'profile']);
                }
            }

            $latestAcademicYear = $this->AcademicYear->currentAcademicYear();
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $studentSection = $studentsTable->studentAcademicDetail($this->Auth->user('student_id'));

            if (empty($studentSection['Section']) && !empty($studentSection['StudentsSection'])) {
                $this->Flash->info(__('Your previous semester is archived and you are not assigned to a section for the current semester. Make sure you have a proper section assignment for this semester before trying to register.'));
                return $this->redirect(['controller' => 'Students', 'action' => 'student_academic_profile', $this->Auth->user('student_id')]);
            } elseif (empty($studentSection['Section']) && empty($studentSection['StudentsSection'])) {
                $this->Flash->info(__(
                    'You are not assigned to any section for {0} academic year. Communicate with your department and ensure you have a proper section assignment in {0} before trying to register.',
                    $latestAcademicYear ?? $this->AcademicYear->currentAcademicYear()
                ));
                return $this->redirect(['controller' => 'Students', 'action' => 'student_academic_profile', $this->Auth->user('student_id')]);
            }

            $studentDetails = $studentsTable->find()
                ->where(['Students.id' => $this->Auth->user('student_id')])
                ->first();

            $studentsSectionTable = TableRegistry::getTableLocator()->get('StudentsSections');
            $getCourseNotRegistered = $studentsSectionTable->getMostRecentSectionPublishedCourseNotRegistered($this->Auth->user('student_id'));

            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $isThereFxInPrevAcademicStatus = $studentExamStatusTable->checkFxPresenseInStatus($this->Auth->user('student_id'));

            if (!empty($getCourseNotRegistered)) {
                $latestAcademicYear = $getCourseNotRegistered[0]['PublishedCourse']['academic_year'];
            } else {
                $this->Flash->info(__('You have already registered or there are no published courses to register for.'));
                return $this->redirect(['action' => 'index']);
            }

            $latestAcSemester = $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear($this->Auth->user('student_id'), $latestAcademicYear);
            $latestSemester = $latestAcSemester['semester'];

            $paymentTable = TableRegistry::getTableLocator()->get('Payments');
            $paymentRequired = $paymentTable->paidPayment($this->Auth->user('student_id'), $latestAcSemester);
            $passedOrFailed = $studentExamStatusTable->get_student_exam_status($this->Auth->user('student_id'), $latestAcademicYear, $latestSemester);

            if (($passedOrFailed == 1 || $passedOrFailed == 3) && $isThereFxInPrevAcademicStatus == 1 && $paymentRequired) {
                $getStudentAcademicStatus = $studentExamStatusTable->getStudentAcadamicStatus($this->Auth->user('student_id'), $latestAcademicYear, $latestSemester);
                $studentSection = $studentsTable->studentAcademicDetail($this->Auth->user('student_id'), $latestAcademicYear);

                $getRegistrationDeadline = false;
                if (!empty($this->department_id) && !empty($studentSection['Section'])) {
                    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                    $yearLevelId = $yearLevelsTable->find()
                        ->select(['name'])
                        ->where(['id' => $studentSection['Section'][0]['year_level_id']])
                        ->first()
                        ->name;

                    $academicCalendarTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
                    $getRegistrationDeadline = $academicCalendarTable->checkRegistration(
                        $latestAcademicYear,
                        $latestSemester,
                        $this->department_id,
                        $yearLevelId,
                        $studentDetails->program_id,
                        $studentDetails->program_type_id
                    );
                } elseif (!empty($this->college_id) && !empty($studentSection['Section'])) {
                    $academicCalendarTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
                    $getRegistrationDeadline = $academicCalendarTable->checkRegistration(
                        $latestAcademicYear,
                        $latestSemester,
                        'pre_' . $this->college_id,
                        0,
                        $studentDetails->program_id,
                        $studentDetails->program_type_id
                    );
                } else {
                    $this->Flash->info(__(
                        'You are not assigned to any section for {0} academic year. Communicate with your department and ensure you have a proper section assignment in {0} before trying to register.',
                        $latestAcademicYear ?? $this->AcademicYear->currentAcademicYear()
                    ));
                    return $this->redirect(['controller' => 'Students', 'action' => 'student_academic_profile', $this->Auth->user('student_id')]);
                }

                if ($getRegistrationDeadline === 1) {
                    // Registration allowed
                } else {
                    $registrationStartDate = $getRegistrationDeadline;

                    if (!empty($registrationStartDate) && $this->__isDate($registrationStartDate)) {
                        $this->Flash->info(__(
                            'Course registration will start on {0} for {1} semester of {2} academic year. Please come back and register on the date specified.',
                            date('M d, Y', strtotime($registrationStartDate)),
                            $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                            $latestAcademicYear
                        ));
                    } elseif (!$getRegistrationDeadline) {
                        $this->Flash->set(__(
                            'Course registration start and end date is not defined for {0} semester of {1} academic year. You can <a href="{2}/pages/academic_calender" target="_blank">check Academic Calendar here</a> and come back later when it is defined.',
                            $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                            $latestAcademicYear,
                            BASE_URL_HTTPS
                        ), ['element' => 'default', 'params' => ['type' => 'Info', 'class' => 'info-box info-message']]);
                    } else {
                        $this->set('deadlinepassed', true);
                        $this->Flash->warning(__(
                            'Course registration deadline has passed for {0} semester of {1} academic year. Please advise the registrar.',
                            $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                            $latestAcademicYear
                        ));
                    }

                    return $this->redirect(['action' => 'index', str_replace('/', '-', $latestAcademicYear), $latestSemester]);
                }

                $notRegistered = $this->CourseRegistration->alreadyRegistred($latestSemester, $latestAcademicYear, $this->Auth->user('student_id'));

                if ($notRegistered > 0) {
                    $this->Flash->info(__(
                        'You have already registered for {0} semester of {1} academic year. You can view the courses you have registered here.',
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                    return $this->redirect(['action' => 'index', str_replace('/', '-', $latestAcademicYear), $latestSemester]);
                }

                if ($this->request->is(['post', 'put']) && !empty($this->request->getData())) {
                    $notRegistered = $this->CourseRegistration->alreadyRegistred(
                        $this->request->getData('CourseRegistration.1.semester'),
                        $latestAcademicYear,
                        $this->request->getData('CourseRegistration.1.student_id')
                    );

                    if (!$notRegistered) {
                        if (!empty($this->request->getData('CourseRegistration')) && $this->request->getData('registerCourses')) {
                            $courseRegistrations = $this->request->getData('CourseRegistration');
                            foreach ($courseRegistrations as $key => &$registration) {
                                if ($key > 0) {
                                    if (isset($registration['elective_course']) && $registration['elective_course'] == 1 && isset($registration['gp']) && $registration['gp'] == 0) {
                                        unset($courseRegistrations[$key]);
                                        continue;
                                    } elseif (isset($registration['gp']) && empty($registration['gp'])) {
                                        unset($courseRegistrations[$key]);
                                        continue;
                                    } elseif (empty($registration['published_course_id']) || empty($registration['student_id'])) {
                                        unset($courseRegistrations[$key]);
                                        continue;
                                    }

                                    if (empty($registration['year_level_id'])) {
                                        $registration['year_level_id'] = null;
                                    }
                                    $registration['cafeteria_consumer'] = $courseRegistrations[0]['cafeteria_consumer'];
                                }
                            }
                            unset($courseRegistrations[0]);

                            if (!empty($courseRegistrations)) {
                                if ($this->CourseRegistration->saveMany($courseRegistrations, ['validate' => false])) {
                                    $registeredCoursesCount = count($courseRegistrations);
                                    $this->Flash->success(__(
                                        'You have successfully registered {0} for {1} semester of {2} academic year.',
                                        $registeredCoursesCount == 1 ? '1 course' : $registeredCoursesCount . ' courses',
                                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                                        $latestAcademicYear
                                    ));
                                    return $this->redirect(['action' => 'index', str_replace('/', '-', $latestAcademicYear), $latestSemester]);
                                }
                            } else {
                                $this->Flash->error(__(
                                    'Please select the courses you want to register for {0} semester of {1} academic year.',
                                    $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                                    $latestAcademicYear
                                ));
                            }
                        }
                    } else {
                        $this->Flash->error(__(
                            'You have already registered for {0} semester of {1} academic year.',
                            $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                            $latestAcademicYear
                        ));
                        return $this->redirect(['action' => 'index', str_replace('/', '-', $latestAcademicYear), $latestSemester]);
                    }
                }

                if (!empty($studentSection)) {
                    if (count($studentSection['Section']) > 0) {
                        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                        $conditions = [
                            'PublishedCourse.add' => 0,
                            'PublishedCourse.drop' => 0,
                            'PublishedCourse.academic_year LIKE' => $latestAcademicYear . '%',
                            'PublishedCourse.semester' => $latestSemester
                        ];

                        if (empty($studentSection['Student']['department_id'])) {
                            $conditions['PublishedCourse.department_id IS'] = null;
                            $conditions['PublishedCourse.section_id'] = $studentSection['Section'][0]['id'];
                            $conditions['OR'] = [
                                'PublishedCourse.year_level_id' => 0,
                                'PublishedCourse.year_level_id' => '',
                                'PublishedCourse.year_level_id IS' => null
                            ];
                            $conditions['PublishedCourse.college_id'] = $studentSection['Student']['college_id'];
                        } else {
                            $conditions['PublishedCourse.department_id'] = $this->department_id;
                            $conditions['PublishedCourse.section_id'] = $studentSection['Section'][0]['id'];
                            $conditions['PublishedCourse.year_level_id'] = $studentSection['Section'][0]['year_level_id'];
                        }

                        $publishedCourses = $publishedCoursesTable->find()
                            ->contain([
                                'Courses' => [
                                    'Prerequisites' => ['fields' => ['id', 'prerequisite_course_id', 'co_requisite']],
                                    'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                                    'fields' => [
                                        'Courses.id',
                                        'Courses.course_code',
                                        'Courses.course_title',
                                        'Courses.lecture_hours',
                                        'Courses.tutorial_hours',
                                        'Courses.laboratory_hours',
                                        'Courses.credit'
                                    ]
                                ]
                            ])
                            ->where($conditions)
                            ->toArray();

                        $publishedCourses = $this->CourseRegistration->getRegistrationType($publishedCourses, $this->Auth->user('student_id'), $getStudentAcademicStatus);
                        $previousStatusSemester = $studentExamStatusTable->getPreviousSemester($latestAcademicYear, $latestSemester);
                        $latestStatusYearSemester = $studentExamStatusTable->studentYearAndSemesterLevelOfStatusDisplay(
                            $this->Auth->user('student_id'),
                            $latestAcademicYear,
                            $previousStatusSemester['semester']
                        );
                        $studentSectionExamStatus = $studentsTable->getStudentSection(
                            $this->Auth->user('student_id'),
                            $latestAcademicYear,
                            $latestStatusYearSemester['semester']
                        );
                        $this->set(compact('publishedCourses', 'studentSection', 'studentSectionExamStatus'));
                    }
                }

                if (empty($publishedCourses)) {
                    $this->Flash->info(__(
                        'There is no published course for {0} semester of {1} academic year that requires your registration for now. You can check it later.',
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                    return $this->redirect(['action' => 'index', str_replace('/', '-', $latestAcademicYear), $latestSemester]);
                }
            } else {
                if ($passedOrFailed == 2) {
                    $this->Flash->info(__(
                        'Your academic status for the previous semester is not yet determined due to incomplete grade submission of registered courses. For now, you cannot register for {0} semester of {1} academic year. Please come back later and check!',
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                } elseif ($passedOrFailed == 4) {
                    $this->Flash->info(__(
                        'Your academic status for the previous semester is dismissed. You cannot register for {0} semester of {1} academic year. If you fulfill the requirements for readmission, don\'t forget to apply to be readmitted for the next academic year.',
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                } elseif ($isThereFxInPrevAcademicStatus == 0) {
                    $this->Flash->info(__(
                        'You have an invalid grade (Fx or NG) in your last registration. Please fix those grade problems first and come back for {0} semester of {1} academic year registration.',
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                } elseif ($paymentRequired == 0) {
                    $this->Flash->info(__(
                        'Payment is required for registration for {0} semester of {1} academic year. Please communicate with the registrar about the issues.',
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                }

                return $this->redirect(['action' => 'index', str_replace('/', '-', $latestAcademicYear), $latestSemester]);
            }
        } else {
            $this->Flash->info(__('You need to log in or must have a student role to register for courses.'));
            return $this->redirect('/');
        }
    }

    public function studentListNotRegistered($data = null)
    {
        $options = ['fields' => ['PublishedCourses.id']];
        $searchConditions = [
            'conditions' => [['Students.graduated' => 0]],
            'fields' => ['Students.id', 'Students.studentnumber', 'Students.full_name'],
            'limit' => 20,
            'order' => ['Students.full_name'],
            'contain' => [
                'Sections' => ['fields' => ['id', 'year_level_id']],
                'StudentsSections' => ['conditions' => ['StudentsSections.archive' => 0]],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']]
            ]
        ];

        $organizedStudents = [];
        $publishedCourseIds = [];

        $latestSemesterAcademicYear = !empty($data['Student']['academicyear'])
            ? $this->CourseRegistration->latestAcademicYearSemester($data['Student']['academicyear'])
            : $this->CourseRegistration->latestAcademicYearSemester($this->AcademicYear->currentAcademicYear());

        if (!empty($latestSemesterAcademicYear)) {
            $options['conditions'] = [
                'PublishedCourses.academic_year LIKE' => $latestSemesterAcademicYear['academic_year'] . '%',
                'PublishedCourses.add' => 0
            ];

            if (!empty($data['Student']['department_id'])) {
                $options['conditions']['PublishedCourses.department_id'] = $data['Student']['department_id'];
            }

            if (empty($data['Student']['department_id']) || empty($data['Student']['college_id'])) {
                if (!empty($this->department_ids)) {
                    $options['conditions']['PublishedCourses.department_id IN'] = $this->department_ids;
                } elseif (!empty($this->college_ids)) {
                    $options['conditions']['PublishedCourses.college_id IN'] = $this->college_ids;
                }
            }

            if (!empty($data['Student']['program_id'])) {
                $options['conditions']['PublishedCourses.program_id'] = $data['Student']['program_id'];
            }

            if (!empty($data['Student']['program_type_id'])) {
                $options['conditions']['PublishedCourses.program_type_id'] = $data['Student']['program_type_id'];
            }

            if (!empty($data['Student']['semester'])) {
                $options['conditions']['PublishedCourses.semester'] = $data['Student']['semester'];
                $this->request = $this->request->withData('Student.semester', $data['Student']['semester']);
            }

            $publishedCourseIds = TableRegistry::getTableLocator()->get('PublishedCourses')
                ->find('list', $options)
                ->toArray();

            if (empty($publishedCourseIds)) {
                return [];
            }
        }

        if (!empty($data) && !empty($publishedCourseIds)) {
            if (!empty($data['Student']['program_id'])) {
                $searchConditions['conditions'][] = ['Students.program_id' => $data['Student']['program_id']];
            }

            if (!empty($data['Student']['program_type_id'])) {
                $searchConditions['conditions'][] = ['Students.program_type_id' => $data['Student']['program_type_id']];
            }

            if (!empty($data['Student']['department_id'])) {
                $departmentIds = $this->_givenPublisheCourseReturnDept($publishedCourseIds);
                if (in_array($data['Student']['department_id'], $departmentIds['dept'])) {
                    $searchConditions['conditions'][] = ['Students.department_id' => $data['Student']['department_id']];
                }
            }

            if (!empty($data['Student']['college_id'])) {
                $searchConditions['conditions'][] = [
                    'Students.college_id' => $data['Student']['college_id'],
                    'Students.department_id IS' => null
                ];
            }

            if (!empty($data['Student']['studentnumber'])) {
                $searchConditions['conditions'][] = ['Students.studentnumber LIKE' => trim($data['Student']['studentnumber'])];
            }

            if (!empty($this->department_ids) && empty($data['Student']['department_id'])) {
                $searchConditions['conditions'][] = ['Students.department_id IN' => $this->department_ids];
            } elseif (!empty($this->college_ids) && empty($data['Student']['college_id'])) {
                $collegeIds = $this->_givenPublisheCourseReturnDept($publishedCourseIds);
                $searchConditions['conditions'][] = [
                    'Students.college_id IN' => $collegeIds['college'],
                    'Students.department_id IS' => null
                ];
            }
        } else {
            if (!empty($this->department_ids)) {
                $departmentIds = $this->_givenPublisheCourseReturnDept($publishedCourseIds);
                $searchConditions['conditions'][] = ['Students.department_id IN' => $departmentIds['dept']];
            } elseif (!empty($this->college_ids)) {
                $collegeIds = $this->_givenPublisheCourseReturnDept($publishedCourseIds);
                $searchConditions['conditions'][] = [
                    'Students.department_id IS' => null,
                    'Students.college_id IN' => $collegeIds['college']
                ];
            }
        }

        $sectionIds = TableRegistry::getTableLocator()->get('PublishedCourses')
            ->find('list')
            ->where(['PublishedCourses.id IN' => $publishedCourseIds])
            ->select('section_id')
            ->toArray();

        $sectionsStudents = TableRegistry::getTableLocator()->get('StudentsSections')
            ->find('list')
            ->where(['section_id IN' => $sectionIds, 'archive' => 0])
            ->select('student_id')
            ->toArray();

        $searchConditions['conditions'][] = ['Students.id IN' => $sectionsStudents];

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsTable->hasMany('StudentsSections', ['className' => 'StudentsSections']);

        $students = $studentsTable->find('all', $searchConditions)->toArray();

        if (!empty($students)) {
            $studentsListNotRegistered = [];
            foreach ($students as $id => &$detail) {
                $registeredAllPublishedCourse = 0;
                foreach ($publishedCourseIds as $pvv) {
                    $check = $this->CourseRegistration->find()
                        ->where([
                            'CourseRegistrations.student_id' => $detail['id'],
                            'CourseRegistrations.published_course_id' => $pvv
                        ])
                        ->count();

                    if ($check > 0) {
                        $registeredAllPublishedCourse++;
                    }
                }
                if ($registeredAllPublishedCourse > 0) {
                    unset($students[$id]);
                    $registeredAllPublishedCourse = 0;
                }
            }
        }

        if (!empty($students)) {
            foreach ($students as $studentValue) {
                if (!empty($studentValue['StudentsSections']) && count($studentValue['StudentsSections']) > 0) {
                    $yearLevelFound = null;
                    foreach ($studentValue['Sections'] as $sectValue) {
                        if ($studentValue['StudentsSections'][0]['section_id'] == $sectValue['id']) {
                            $yearLevelFound = $sectValue['year_level_id'];
                        }
                    }
                    $organizedStudents[$studentValue['Program']['name']][$studentValue['ProgramType']['name']][$yearLevelFound][$studentValue['StudentsSections'][0]['section_id']][] = $studentValue;
                }
            }
            return $organizedStudents;
        }

        return $organizedStudents;
    }

    public function maintainRegistration($studentId = null, $registerSelectedSection = null)
    {
        $this->registerStudent($studentId, $registerSelectedSection);
    }

    private function registerStudent($studentId = null, $registerSelectedSection = null)
    {
        $currentAcyAndSemester = $this->AcademicYear->currentAcyAndSemester();
        $latestAcademicYear = $academicYearSelected = $currentAcyAndSemester['academic_year'];

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('Student'))) {
            $academicYearSelected = $this->request->getData('Student.academicyear');
            $latestAcademicYear = $this->request->getData('Student.academicyear');
            if (empty($this->request->getData('Student.semester'))) {
                $this->request->setData('Student.semester', $currentAcyAndSemester['semester']);
            }
        } elseif (!empty($this->request->getData('CourseRegistration'))) {
            $academicYearSelected = $this->request->getData('CourseRegistration.1.academic_year');
            $latestAcademicYear = $this->request->getData('CourseRegistration.1.academic_year');
        }

        if (empty($studentId) && !empty($registerSelectedSection)) {
            $this->request->setData('Student.academicyear', $academicYearSelected);
            $this->request->setData('continue', true);

            $studentsList = $this->CourseRegistration->Section->getSectionActiveStudentsId($registerSelectedSection);

            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $conditions = ['Students.id IN' => $studentsList];

            if (!empty($this->department_ids)) {
                $conditions['Students.department_id IN'] = $this->department_ids;
                $conditions['Students.program_type_id IN'] = $this->program_type_ids;
                $conditions['Students.program_id IN'] = $this->program_ids;
            } elseif (!empty($this->college_ids)) {
                $conditions['Students.college_id IN'] = $this->college_ids;
                $conditions['Students.program_type_id IN'] = $this->program_type_ids;
                $conditions['Students.program_id IN'] = $this->program_ids;
                $conditions['Students.department_id IS'] = null;
            } elseif (!empty($this->department_id)) {
                $conditions['Students.department_id'] = $this->department_id;
            } elseif (!empty($this->college_id)) {
                $conditions['Students.college_id'] = $this->college_id;
            }

            $eligibleRegistrarResponsibility = $studentsTable->find()
                ->where($conditions)
                ->count();

            if ($eligibleRegistrarResponsibility == 0) {
                $this->Flash->error(__('You do not have the privilege to register the selected student. Your action is logged and reported to the system administrators.'));
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $breakerDetail = $usersTable->find()
                    ->contain(['Staffs', 'Students'])
                    ->where(['Users.id' => $this->Auth->user('id')])
                    ->first();

                $details = '';
                if (!empty($breakerDetail->staffs)) {
                    $details .= $breakerDetail->staffs[0]->first_name . ' ' . $breakerDetail->staffs[0]->middle_name . ' ' . $breakerDetail->staffs[0]->last_name . ' (' . $breakerDetail->username . ')';
                } elseif (!empty($breakerDetail->students)) {
                    $details .= $breakerDetail->students[0]->first_name . ' ' . $breakerDetail->students[0]->middle_name . ' ' . $breakerDetail->students[0]->last_name . ' (' . $breakerDetail->username . ')';
                }

                $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                $autoMessageTable->sendPermissionManagementBreakAttempt(
                    Configure::read('User.user'),
                    '<u>' . $details . '</u> is trying to register students without assigned privilege. Please give appropriate warning.'
                );
            } else {
                $isRegistered = $this->CourseRegistration->massRegisterStudent($registerSelectedSection, $academicYearSelected);

                if ($isRegistered == 1) {
                    $this->Flash->success(__('All students (who are not dismissed and fulfilled prerequisites) in the selected section are registered successfully for non-elective courses for the selected academic year and semester. You can view all course registrations using the "Course Registration View" option or maintain elective courses separately if any, on manage missing registration on each student\'s academic profile.'));
                } elseif ($isRegistered == 3) {
                    $this->Flash->info(__('Some of the students in the selected section are not eligible for registration.'));
                }
            }
        }

        if ($studentId) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $this->request->setData('Student.studentnumber', $studentsTable->get($studentId)->studentnumber);
            if (isset($latestAcademicYear)) {
                $this->request->setData('Student.academicyear', $latestAcademicYear);
            }

            $conditions = ['Students.id' => $studentId];
            if (!empty($this->department_ids)) {
                $conditions['Students.department_id IN'] = $this->department_ids;
                $conditions['Students.program_type_id IN'] = $this->program_type_ids;
                $conditions['Students.program_id IN'] = $this->program_ids;
            } elseif (!empty($this->college_ids)) {
                $conditions['Students.college_id IN'] = $this->college_ids;
                $conditions['Students.program_type_id IN'] = $this->program_type_ids;
                $conditions['Students.program_id IN'] = $this->program_ids;
                $conditions['Students.department_id IS'] = null;
            } elseif (!empty($this->department_id)) {
                $conditions['Students.department_id'] = $this->department_id;
            } elseif (!empty($this->college_id)) {
                $conditions['Students.college_id'] = $this->college_id;
            }

            $eligibleRegistrarResponsibility = $studentsTable->find()
                ->where($conditions)
                ->count();

            if ($eligibleRegistrarResponsibility == 0) {
                $this->Flash->error(__('You do not have the privilege to register the selected student. Your action is logged and reported to the system administrators.'));
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $breakerDetail = $usersTable->find()
                    ->contain(['Staffs', 'Students'])
                    ->where(['Users.id' => $this->Auth->user('id')])
                    ->first();

                $details = '';
                if (!empty($breakerDetail->staffs)) {
                    $details .= $breakerDetail->staffs[0]->first_name . ' ' . $breakerDetail->staffs[0]->middle_name . ' ' . $breakerDetail->staffs[0]->last_name . ' (' . $breakerDetail->username . ')';
                } elseif (!empty($breakerDetail->students)) {
                    $details .= $breakerDetail->students[0]->first_name . ' ' . $breakerDetail->students[0]->middle_name . ' ' . $breakerDetail->students[0]->last_name . ' (' . $breakerDetail->username . ')';
                }

                $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                $autoMessageTable->sendPermissionManagementBreakAttempt(
                    Configure::read('User.user'),
                    '<u>' . $details . '</u> is trying to register students without assigned privilege. Please give appropriate warning.'
                );
                $this->request->setData('Student.studentnumber', null);
            } else {
                $this->request->setData('continue', true);
            }
        }

        $buttonClicked = false;
        $buttonIndex = '';

        if ($this->request->getData('CourseRegistration.register_count') !== null) {
            for ($i = 0; $i <= $this->request->getData('CourseRegistration.register_count'); $i++) {
                if ($this->request->getData('registerSelected_' . $i) !== null) {
                    $buttonClicked = true;
                    $buttonIndex = $i;
                    break;
                }
            }
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('CourseRegistration')) && $buttonClicked) {
            if ($this->request->getData('registerSelected_' . $buttonIndex) !== null) {
                $this->request->setData('CourseRegistration.register_count', null);
                $studentLists = [];
                $regCount = 0;
                $studCount = 0;

                if (!empty($this->request->getData('CourseRegistration'))) {
                    foreach ($this->request->getData('CourseRegistration') as $key => $data) {
                        if (is_numeric($key)) {
                            if (isset($data['ggp']) && ($data['ggp'] == '1' || $data['ggp'] == 1)) {
                                $studCount++;
                                $notRegistered = $this->CourseRegistration->alreadyRegistred(
                                    $this->request->getData('Student.semester'),
                                    $this->request->getData('Student.academicyear'),
                                    $data['student_id']
                                );

                                if (!$notRegistered) {
                                    $publishedCourseLists = $this->CourseRegistration->registerSingleStudent(
                                        $data['student_id'],
                                        $this->request->getData('Student.academicyear'),
                                        $this->request->getData('Student.semester'),
                                        $excludeElective = 1
                                    );

                                    if ($publishedCourseLists['passed'] === false || $publishedCourseLists['passed'] == 4) {
                                        continue;
                                    }

                                    $psL = $this->CourseRegistration->getRegistrationType($publishedCourseLists['register'], $data['student_id']);

                                    if (!empty($psL)) {
                                        $totalSelectedCredit = 0;
                                        foreach ($psL as $pl) {
                                            if ((!isset($pl['prequisite_taken_passsed']) && !isset($pl['exemption'])) ||
                                                (isset($pl['prequisite_taken_passsed']) && $pl['prequisite_taken_passsed'] == 1)) {
                                                if (isset($pl['PublishedCourse']['id']) && !empty($pl['PublishedCourse']['id'])) {
                                                    $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
                                                    $alreadyTakenCourse = !empty($pl['PublishedCourse']['course_id'])
                                                        ? $courseDropsTable->course_taken($data['student_id'], $pl['PublishedCourse']['course_id'], 1)
                                                        : 0;

                                                    if ($alreadyTakenCourse != 3) {
                                                        continue;
                                                    }

                                                    $coursesTable = TableRegistry::getTableLocator()->get('Courses');
                                                    $courseCredit = $coursesTable->get($pl['PublishedCourse']['course_id'])->credit;
                                                    $totalSelectedCredit += $courseCredit;

                                                    $maxLoad = $this->CourseRegistration->Student->calculateStudentLoad(
                                                        $data['student_id'],
                                                        $pl['PublishedCourse']['semester'],
                                                        $pl['PublishedCourse']['academic_year']
                                                    );
                                                    $allowedMaximum = TableRegistry::getTableLocator()->get('AcademicCalendars')
                                                        ->maximumCreditPerSemester($data['student_id']);

                                                    if (is_numeric($maxLoad) && $maxLoad >= 0) {
                                                        if (($maxLoad + $courseCredit) <= $allowedMaximum &&
                                                            ($maxLoad + $totalSelectedCredit) <= $allowedMaximum) {
                                                            $studentLists['CourseRegistration'][$regCount] = [
                                                                'student_id' => $data['student_id'],
                                                                'semester' => $pl['PublishedCourse']['semester'],
                                                                'academic_year' => $pl['PublishedCourse']['academic_year'],
                                                                'year_level_id' => $pl['PublishedCourse']['year_level_id'],
                                                                'section_id' => $pl['PublishedCourse']['section_id'],
                                                                'published_course_id' => $pl['PublishedCourse']['id']
                                                            ];
                                                            $regCount++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $this->request->setData("CourseRegistration.{$key}.ggp", null);
                            }
                        }
                    }
                }

                if (!empty($studentLists['CourseRegistration'])) {
                    if ($this->CourseRegistration->saveMany($studentLists['CourseRegistration'], ['validate' => false])) {
                        $this->Flash->success(__(
                            'You have successfully registered the selected {0} for non-elective courses for {1} semester of {2} academic year. If there are any elective courses published for the section, maintain them separately on manage missing registration on student academic profile.',
                            $studCount == 1 ? '1 student' : $studCount . ' students',
                            $this->request->getData('Student.semester') == 'I' ? '1st' : ($this->request->getData('Student.semester') == 'II' ? '2nd' : '3rd'),
                            $this->request->getData('Student.academicyear')
                        ));
                        $this->request->setData('continue', false);
                        $this->request->setData('CourseRegistration', null);
                    }
                }
            }
        }

        if ($this->request->is(['post', 'put']) && $this->request->getData('continue') !== null) {
            $students = empty($studentId) ? $this->studentListNotRegistered($this->request->getData()) : [];

            if (empty($students) && empty($studentId)) {
                if (!empty($this->request->getData())) {
                    if (!$this->Flash->getMessages('flash')) {
                        $semester = $this->request->getData('Student.semester');
                        $academicYear = $this->request->getData('Student.academicyear');
                        if (!empty($this->request->getData('Student.section_id'))) {
                            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                            $sectionName = $sectionsTable->get($this->request->getData('Student.section_id'))->name;
                            $this->Flash->info(__(
                                'No result found for {0} section that needs course registration maintenance for {1} semester of {2} academic year.',
                                $sectionName,
                                $semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : '3rd'),
                                $academicYear
                            ));
                        } else {
                            $this->Flash->info(__(
                                'No result found in the given criteria that needs course registration maintenance for {0} semester of {1} academic year.',
                                $semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : '3rd'),
                                $academicYear
                            ));
                        }
                    }
                } else {
                    $this->Flash->info(__(
                        'No result found in the given criteria that needs course registration maintenance for {0} semester of {1} academic year.',
                        $semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : '3rd'),
                        $academicYear
                    ));
                }
            }

            if (!empty($this->request->getData('Student.studentnumber'))) {
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $studId = $studentsTable->find()
                    ->select(['id'])
                    ->where(['Students.studentnumber LIKE' => trim($this->request->getData('Student.studentnumber'))])
                    ->first()
                    ->id ?? null;

                if (empty($studId)) {
                    $this->Flash->error(__('Student ID not found. Check if you made a typo error, correct and try again!'));
                    return $this->redirect(['action' => 'maintainRegistration']);
                }

                $studentName = $studentsTable->get($studId)->full_name;

                if ($this->Auth->user('role_id') == ROLE_REGISTRAR && $this->Auth->user('is_admin') != 1) {
                    $conditions = ['Students.id' => $studId];
                    if (!empty($this->department_ids)) {
                        $conditions['Students.department_id IN'] = $this->department_ids;
                        $conditions['Students.program_type_id IN'] = $this->program_type_ids;
                        $conditions['Students.program_id IN'] = $this->program_ids;
                    } elseif (!empty($this->college_ids)) {
                        $conditions['Students.college_id IN'] = $this->college_ids;
                        $conditions['Students.program_type_id IN'] = $this->program_type_ids;
                        $conditions['Students.program_id IN'] = $this->program_ids;
                        $conditions['Students.department_id IS'] = null;
                    }

                    $eligibleRegistrarResponsibility = $studentsTable->find()
                        ->where($conditions)
                        ->count();

                    if ($eligibleRegistrarResponsibility == 0) {
                        $this->Flash->error(__(
                            'You do not have the privilege to register {0} ({1}).',
                            $studentName,
                            trim($this->request->getData('Student.studentnumber'))
                        ));
                        $this->request->setData('CourseRegistration', null);
                        return $this->redirect(['action' => 'maintainRegistration']);
                    }
                }

                $latestAcSemester = $this->CourseRegistration->getLastestStudentSemesterAndAcademicYear($studId, $latestAcademicYear);
                $latestSemester = $latestAcSemester['semester'];
                $studentSection = $studentsTable->student_academic_detail($studId, $latestAcademicYear);

                if (!empty($latestAcSemester)) {
                    $overMaximumCreditAllowed = $studentsTable->checkAllowedMaxCreditLoadPerSemester(
                        $studId,
                        $latestAcSemester['semester'],
                        $latestAcSemester['academic_year']
                    );
                } else {
                    $overMaximumCreditAllowed = $studentsTable->checkAllowedMaxCreditLoadPerSemester(
                        $studId,
                        $this->request->getData('Student.semester') ?? $this->AcademicYear->currentAcyAndSemester()['semester'],
                        $this->request->getData('Student.academicyear') ?? $this->AcademicYear->currentAcyAndSemester()['academic_year']
                    );
                }

                $publishedCourses = $this->CourseRegistration->registerSingleStudent($studId, $latestAcademicYear, $latestSemester);

                if ($publishedCourses['passed'] === false || $publishedCourses['passed'] == 4) {
                    $this->Flash->info(__(
                        '{0} ({1}) is dismissed. You cannot register the student for semester {2}/{3}.',
                        $studentName,
                        trim($this->request->getData('Student.studentnumber')),
                        $latestSemester,
                        $latestAcademicYear
                    ));
                    $this->set('dismissed', true);
                }

                $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                $previousStatusSemester = $studentExamStatusTable->getPreviousSemester($latestAcademicYear, $latestSemester);
                $latestStatusYearSemester = $studentExamStatusTable->studentYearAndSemesterLevelOfStatusDisplay(
                    $studId,
                    $latestAcademicYear,
                    $previousStatusSemester['semester']
                );
                $studentSectionExamStatus = $studentsTable->getStudentSection(
                    $studId,
                    $latestAcademicYear,
                    $latestStatusYearSemester['semester']
                );
                $publishedCourses = $publishedCourses['register'];

                if (isset($studentSectionExamStatus['Section']) && !empty($studentSectionExamStatus['Section']['id']) &&
                    !$studentSectionExamStatus['Section']['archive'] && !$studentSectionExamStatus['Section']['StudentsSection']['archive']) {
                    $this->request->setData('Student.section_id', $studentSectionExamStatus['Section']['id']);
                    if (!empty($studentSectionExamStatus['Section']['department_id'])) {
                        $this->request->setData('Student.department_id', $studentSectionExamStatus['Section']['department_id']);
                    } else {
                        $this->request->setData('Student.college_id', $studentSectionExamStatus['Section']['college_id']);
                    }
                    $this->request->setData('Student.academicyear', $studentSectionExamStatus['Section']['academicyear']);
                    $this->request->setData('Student.program_id', $studentSectionExamStatus['Section']['program_id']);
                    $this->request->setData('Student.program_type_id', $studentSectionExamStatus['Section']['program_type_id']);
                    if (isset($studentSectionExamStatus['Section']['YearLevel']) && !empty($studentSectionExamStatus['Section']['YearLevel']['name'])) {
                        $this->request->setData('Student.year_level_id', $studentSectionExamStatus['Section']['YearLevel']['name']);
                    }
                } elseif (!empty($studentSectionExamStatus['StudentBasicInfo'])) {
                    if (!empty($studentSectionExamStatus['StudentBasicInfo']['department_id'])) {
                        $this->request->setData('Student.department_id', $studentSectionExamStatus['StudentBasicInfo']['department_id']);
                    } else {
                        $this->request->setData('Student.college_id', $studentSectionExamStatus['StudentBasicInfo']['college_id']);
                    }
                    $this->request->setData('Student.program_id', $studentSectionExamStatus['StudentBasicInfo']['program_id']);
                    $this->request->setData('Student.program_type_id', $studentSectionExamStatus['StudentBasicInfo']['program_type_id']);
                }

                if (empty($publishedCourses)) {
                    $this->Flash->warning(__(
                        'No published course is found for {0} ({1}) for {2} semester of {3} academic year. Please check course registrations for the latest semester on the student academic profile or contact the student department for course publication.',
                        $studentName,
                        trim($this->request->getData('Student.studentnumber')),
                        $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                        $latestAcademicYear
                    ));
                }

                $this->set('hide_search', true);
                $this->set(compact('publishedCourses', 'studentSection', 'year_level_name', 'studentSectionExamStatus'));
            }
            $this->set(compact('students'));
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('register'))) {
            $studentID = null;
            $semester = null;

            if (!empty($this->request->getData('Student.studentnumber'))) {
                foreach ($this->request->getData('CourseRegistration') as $value) {
                    if (isset($value['student_id'])) {
                        $studentID = $value['student_id'];
                    }
                }
                $notRegistered = $this->CourseRegistration->alreadyRegistred(
                    $this->request->getData('Student.semester'),
                    $this->request->getData('Student.academicyear'),
                    $studentID
                );
                $semester = $this->request->getData('Student.semester');
            } else {
                $semester = $this->request->getData('CourseRegistration.1.semester');
                $notRegistered = $this->CourseRegistration->alreadyRegistred(
                    $this->request->getData('CourseRegistration.1.semester'),
                    $this->request->getData('CourseRegistration.1.academic_year'),
                    $this->request->getData('CourseRegistration.1.student_id')
                );
            }

            if ($notRegistered == 0) {
                if (!empty($this->request->getData('CourseRegistration'))) {
                    $courseRegistrations = $this->request->getData('CourseRegistration');
                    if (empty($this->request->getData('Student.studentnumber'))) {
                        foreach ($courseRegistrations as $key => &$value) {
                            if (isset($value['elective_course']) && $value['elective_course'] == 1 && isset($value['gp']) && $value['gp'] == 0) {
                                unset($courseRegistrations[$key]);
                                continue;
                            } elseif (isset($value['gp']) && empty($value['gp'])) {
                                unset($courseRegistrations[$key]);
                                continue;
                            } elseif (empty($value['published_course_id']) || empty($value['student_id'])) {
                                unset($courseRegistrations[$key]);
                                continue;
                            }
                            if (empty($value['year_level_id'])) {
                                $value['year_level_id'] = null;
                            }
                            $value['cafeteria_consumer'] = $courseRegistrations[0]['cafeteria_consumer'];
                        }
                    } else {
                        $cafeteriaConsumer = array_pop($courseRegistrations);
                        $courseRegistrations = array_values($courseRegistrations);

                        foreach ($courseRegistrations as $key => &$value) {
                            if (isset($value['elective_course']) && $value['elective_course'] == 1 && isset($value['gp']) && $value['gp'] == 0) {
                                unset($courseRegistrations[$key]);
                                continue;
                            } elseif (isset($value['gp']) && empty($value['gp'])) {
                                unset($courseRegistrations[$key]);
                                continue;
                            } elseif (empty($value['published_course_id']) || empty($value['student_id'])) {
                                unset($courseRegistrations[$key]);
                                continue;
                            }
                            if (empty($value['year_level_id'])) {
                                $value['year_level_id'] = null;
                            }
                            $value['cafeteria_consumer'] = $cafeteriaConsumer['cafeteria_consumer'];
                        }
                    }

                    if (!empty($courseRegistrations)) {
                        if ($this->CourseRegistration->saveMany($courseRegistrations, ['validate' => false])) {
                            $studentId = $courseRegistrations[0]['student_id'];
                            $sem = $courseRegistrations[0]['semester'];
                            $acYear = $courseRegistrations[0]['academic_year'];

                            $studentsTable = TableRegistry::getTableLocator()->get('Students');
                            $studentName = $studentsTable->get($studentId)->full_name;
                            $studentNumber = $studentsTable->get($studentId)->studentnumber;

                            $this->Flash->success(__(
                                'You have successfully registered {0} ({1}) for {2} semester of {3} academic year.',
                                $studentName,
                                $studentNumber,
                                $sem == 'I' ? '1st' : ($sem == 'II' ? '2nd' : '3rd'),
                                $acYear
                            ));
                            $this->request->setData('Student.studentnumber', null);
                            return $this->redirect(['action' => 'maintainRegistration']);
                        }
                    } else {
                        $this->Flash->error(__(
                            'Please select the courses you want to register for {0} semester of {1} academic year.',
                            $latestSemester == 'I' ? '1st' : ($latestSemester == 'II' ? '2nd' : '3rd'),
                            $latestAcademicYear
                        ));
                    }
                }
            } else {
                $this->Flash->error(__(
                    'The student has already registered for {0} semester of {1} academic year.',
                    $semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : '3rd'),
                    $this->AcademicYear->currentAcademicYear()
                ));
            }
        }

        $latestSemesterAcademicYear = $this->CourseRegistration->latestAcademicYearSemester($this->AcademicYear->currentAcademicYear());
        $yearLevels = $this->year_levels;
        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = !empty($this->program_ids)
            ? $programsTable->find('list')->where(['Programs.id IN' => $this->program_ids])->toArray()
            : $programsTable->find('list')->toArray();

        $programTypes = !empty($this->program_type_ids)
            ? $programTypesTable->find('list')->where(['ProgramTypes.id IN' => $this->program_type_ids])->toArray()
            : $programTypesTable->find('list')->toArray();

        $departments = [];
        $colleges = [];
        $sections = [];

        if ($this->Auth->user('role_id') == ROLE_REGISTRAR || $this->Auth->user('role_id') == ROLE_DEPARTMENT
            || $this->Auth->user('role_id') == ROLE_COLLEGE) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

            if ($this->Auth->user('role_id') == ROLE_REGISTRAR || $this->Auth->user('role_id') == ROLE_DEPARTMENT) {
                if (!empty($this->department_ids)) {
                    $departments = $departmentsTable->allDepartmentsByCollege2(0, $this->department_ids, [],
                        1);
                    $conditions = [
                        'Sections.department_id' => !empty($this->request->getData('Student.department_id'))
                            ? $this->request->getData('Student.department_id')
                            : array_values($this->department_ids)[0],
                        'Sections.program_type_id' => !empty($this->request->getData('Student.program_type_id'))
                            ? $this->request->getData('Student.program_type_id')
                            : array_values($this->program_type_ids)[0],
                        'Sections.program_id' => !empty($this->request->getData('Student.program_id'))
                            ? $this->request->getData('Student.program_id')
                            : array_values($this->program_ids)[0],
                        'Sections.academicyear LIKE' => !empty($this->request->getData('Student.academicyear'))
                            ? $this->request->getData('Student.academicyear')
                            : $this->AcademicYear->currentAcademicYear(),
                        'OR' => [
                            'Sections.year_level_id IS NOT NULL',
                            'Sections.year_level_id <>' => 0,
                            'Sections.year_level_id !=' => ''
                        ],
                        'Sections.archive' => 0
                    ];
                    if (!empty($this->request->getData('Student.year_level_id'))) {
                        $conditions['YearLevels.name LIKE'] = $this->request->getData('Student.year_level_id');
                    } else {
                        $conditions['YearLevels.name LIKE'] = array_values($yearLevels)[0];
                    }
                } elseif (!empty($this->department_id)) {
                    $departments = $departmentsTable->find('list')
                        ->where(['Departments.id' => $this->department_id])
                        ->toArray();
                    $conditions = [
                        'Sections.department_id' => $this->department_id,
                        'Sections.program_type_id' => !empty($this->request->getData('Student.program_type_id'))
                            ? $this->request->getData('Student.program_type_id')
                            : array_values($this->program_type_ids)[0],
                        'Sections.program_id' => !empty($this->request->getData('Student.program_id'))
                            ? $this->request->getData('Student.program_id')
                            : array_values($this->program_ids)[0],
                        'Sections.academicyear LIKE' => !empty($this->request->getData('Student.academicyear'))
                            ? $this->request->getData('Student.academicyear')
                            : $this->AcademicYear->current_academicyear(),
                        'YearLevels.name LIKE' => !empty($this->request->getData('Student.year_level_id'))
                            ? $this->request->getData('Student.year_level_id')
                            : key($yearLevels),
                        'OR' => [
                            'Sections.year_level_id IS NOT NULL',
                            'Sections.year_level_id <>' => 0,
                            'Sections.year_level_id !=' => ''
                        ],
                        'Sections.archive' => 0
                    ];
                }
            }

            if (!empty($this->college_ids) || !empty($this->college_id)) {
                $collegeId = !empty($this->college_id) ? $this->college_id : array_values($this->college_ids)[0];
                $colleges = $collegesTable->find('list')
                    ->where(['Colleges.id' => $this->college_ids, 'Colleges.active' => 1])
                    ->toArray();
                $conditions = [
                    'Sections.college_id' => !empty($this->request->getData('Student.college_id'))
                        ? $this->request->getData('Student.college_id')
                        : $collegeId,
                    'Sections.program_type_id' => !empty($this->request->getData('Student.program_type_id'))
                        ? $this->request->getData('Student.program_type_id')
                        : array_values($this->program_type_ids)[0],
                    'Sections.program_id' => !empty($this->request->getData('Student.program_id'))
                        ? $this->request->getData('Student.program_id')
                        : array_values($this->program_ids)[0],
                    'Sections.academicyear LIKE' => !empty($this->request->getData('Student.academicyear'))
                        ? $this->request->getData('Student.academicyear')
                        : $this->AcademicYear->currentAcademicYear(),
                    'Sections.department_id IS' => null,
                    'Sections.archive' => 0
                ];
            }

            $sections = $sectionsTable->find('all')
                ->contain([
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'type']],
                    'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                ])
                ->where($conditions)
                ->order([
                    'Sections.year_level_id' => 'ASC',
                    'Sections.college_id' => 'ASC',
                    'Sections.department_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ])
                ->toArray();

            $sectionOrganizedByYearLevel = ['' => '[ Select Section ]'];
            foreach ($sections as $v) {
                if (!empty($v['YearLevel']['name'])) {
                    $sectionOrganizedByYearLevel[$v['id']] = $v['name'] . " (" . $v['academicyear'] . ", " .
                        $v['YearLevel']['name'] . ")";
                } else {
                    $sectionOrganizedByYearLevel[$v['id']] = $v['name'] . " (" . $v['academicyear'] . ", " .
                        ($v['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . ")";
                }
            }
            $sections = !empty($sectionOrganizedByYearLevel) ? $sectionOrganizedByYearLevel : ['' => '[ No Active Section,
            Try Changing Filters ] '];
        }

        if (!empty($this->request->getData('Student'))) {
            $programsTable = TableRegistry::getTableLocator()->get('Programs');
            $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

            $programName = $programsTable->find()
                ->select(['name'])
                ->where(['Programs.id' => $this->request->getData('Student.program_id')])
                ->first()
                ->name ?? '';

            $programTypeName = $programTypesTable->find()
                ->select(['name'])
                ->where(['ProgramTypes.id' => $this->request->getData('Student.program_type_id')])
                ->first()
                ->name ?? '';

            $academicYear = $this->request->getData('Student.academicyear');
            $semester = $this->request->getData('Student.semester');

            if (!empty($this->request->getData('Student.department_id'))) {
                $departmentName = $departmentsTable->find()
                    ->select(['id', 'name', 'type'])
                    ->where(['Departments.id' => $this->request->getData('Student.department_id')])
                    ->first();
            } elseif (!empty($this->request->getData('Student.college_id'))) {
                $collegeName = $collegesTable->find()
                    ->select(['id', 'name', 'type'])
                    ->where(['Colleges.id' => $this->request->getData('Student.college_id')])
                    ->first();
            }

            $this->set(compact('programName', 'programTypeName', 'academicYear', 'semester', 'departmentName', 'collegeName'));
        }

        $this->set(compact('departments', 'colleges', 'programs', 'programTypes', 'latestSemesterAcademicYear', 'sections', 'yearLevels'));
        $this->render('maintainRegistration');
    }

    public function initSearch()
    {
        $session = $this->request->getSession();
        if (!empty($this->request->getData('Student'))) {
            $session->write('search_data_registration', $this->request->getData('Student'));
        } else {
            $this->request = $this->request->withData('Student', $session->read('search_data_registration'));
        }
    }


    public function initMaintainAcademicYear()
    {
        $session = $this->request->getSession();
        if (!empty($this->request->getData('Student')) && !empty($this->request->getData('Student.academicyear'))) {
            $session->write('search_data_registration', $this->request->getData('Student'));
        } else {
            $this->request = $this->request->withData('Student', $session->read('search_data_registration'));
        }
    }

    public function registerIndividualCourse()
    {
        if ($this->Auth->user('role_id') != ROLE_REGISTRAR) {
            return $this->redirect(['action' => 'index']);
        }

        $session = $this->request->getSession();
        if ($session->read('search_data_registration') && $this->request->getData('getsection') === null) {
            $this->request = $this->request->withData('getsection', true)
                ->withData('Student', $session->read('search_data_registration'));
            $this->set('hide_search', true);
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('registerIndivdualCourse'))) {
            $oneIsSelected = 0;
            $selectedPublishedCourses = [];
            $formattedSaveAllRegistration = [];
            $count = 0;
            $totalSelectedCredit = 0;

            $currentAcyAndSemester = $this->AcademicYear->current_acy_and_semester();

            if (!empty($this->request->getData('PublishedCourse'))) {
                foreach ($this->request->getData('PublishedCourse') as $sectionId => $publishedCourse) {
                    $studentList = $this->CourseRegistration->Section->getSectionActiveStudentsId($sectionId, $this->request->getData('Student.academic_year'));
                    foreach ($publishedCourse as $pId => $selected) {
                        if ($selected == 1) {
                            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                            $publishedCourseDetailed = $publishedCoursesTable->find()
                                ->contain(['Courses' => ['fields' => ['id', 'credit']]])
                                ->where(['PublishedCourses.id' => $pId])
                                ->first();
                            $oneIsSelected++;
                            $totalSelectedCredit += $publishedCourseDetailed->course->credit;

                            if (!empty($studentList)) {
                                foreach ($studentList as $studentId) {
                                    $maxLoad = $this->CourseRegistration->Student->calculateStudentLoad(
                                        $studentId,
                                        $publishedCourseDetailed->semester,
                                        $publishedCourseDetailed->academic_year
                                    );
                                    $allowedMaximum = TableRegistry::getTableLocator()->get('AcademicCalendars')
                                        ->maximumCreditPerSemester($studentId);

                                    if (is_numeric($maxLoad) && $maxLoad > 0) {
                                        if (($maxLoad + $publishedCourseDetailed->course->credit) <= $allowedMaximum &&
                                            ($maxLoad + $totalSelectedCredit) <= $allowedMaximum) {
                                            $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
                                            if (!$this->CourseRegistration->courseRegistered(
                                                    $pId,
                                                    $publishedCourseDetailed->semester,
                                                    $publishedCourseDetailed->academic_year,
                                                    $studentId
                                                ) && $courseDropsTable->course_taken($studentId, $publishedCourseDetailed->course_id, 1) == 3) {
                                                $formattedSaveAllRegistration['CourseRegistration'][$count] = [
                                                    'published_course_id' => $publishedCourseDetailed->id,
                                                    'course_id' => $publishedCourseDetailed->course_id,
                                                    'semester' => $publishedCourseDetailed->semester,
                                                    'academic_year' => $publishedCourseDetailed->academic_year,
                                                    'student_id' => $studentId,
                                                    'section_id' => $publishedCourseDetailed->section_id,
                                                    'year_level_id' => (is_numeric($publishedCourseDetailed->year_level_id) && $publishedCourseDetailed->year_level_id > 0)
                                                        ? $publishedCourseDetailed->year_level_id
                                                        : null
                                                ];

                                                if ($currentAcyAndSemester['academic_year'] == $publishedCourseDetailed->academic_year &&
                                                    $currentAcyAndSemester['semester'] == $publishedCourseDetailed->semester) {
                                                    $formattedSaveAllRegistration['CourseRegistration'][$count]['created'] =
                                                    $formattedSaveAllRegistration['CourseRegistration'][$count]['modified'] = date('Y-m-d H:i:s');
                                                } else {
                                                    $checkRegisteredDate = $this->CourseRegistration->find()
                                                        ->where([
                                                            'CourseRegistrations.academic_year' => $publishedCourseDetailed->academic_year,
                                                            'CourseRegistrations.student_id' => $studentId,
                                                            'CourseRegistrations.semester' => $publishedCourseDetailed->semester
                                                        ])
                                                        ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.id' => 'DESC'])
                                                        ->first();

                                                    $cregTimeAmended = (!empty($checkRegisteredDate->created) && $checkRegisteredDate->created != '0000-00-00 00:00:00')
                                                        ? date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($checkRegisteredDate->created)))
                                                        : $this->AcademicYear->getAcademicYearBegainingDate(
                                                            $publishedCourseDetailed->academic_year,
                                                            $publishedCourseDetailed->semester
                                                        );
                                                    $formattedSaveAllRegistration['CourseRegistration'][$count]['created'] = $cregTimeAmended;
                                                }

                                                $count++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($formattedSaveAllRegistration)) {
                $totalRegistered = (int) (count($formattedSaveAllRegistration['CourseRegistration']) / $oneIsSelected);
                if ($this->CourseRegistration->saveMany($formattedSaveAllRegistration['CourseRegistration'], ['validate' => false])) {
                    $this->Flash->success(__(
                        'Course registration is maintained for {0} {1} who took prerequisites if any, and not registered more than the allowed maximum credit set per semester ({2}) including to selected course(s) and registered for at least one course.',
                        $totalRegistered,
                        $totalRegistered > 1 ? 'eligible students' : 'eligible student',
                        $allowedMaximum
                    ));
                } else {
                    $this->Flash->error(__('The selected course(s) couldn\'t be registered for the selected section students.'));
                }
                return $this->redirect(['action' => 'registerIndividualCourse']);
            } else {
                $this->Flash->info(__(
                    'No students in the selected section(s) require registration for the chosen course(s). If you believe this is an error, please verify that the section and students in the section are not archived, ensure prerequisite course requirements are met, students are registered for at least one course excluding the selected course(s), and confirm that the maximum allowed credits ({0}) per semester have not been exceeded including to selected course(s).',
                    $allowedMaximum
                ));
            }

            if ($oneIsSelected == 0) {
                $this->Flash->error(__('Please select at least one course you want to register for eligible students.'));
            }
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('getsection'))) {
            $session->delete('search_data_registration');
            $everythingFine = false;

            switch (true) {
                case empty($this->request->getData('Student.academic_year')):
                    $this->Flash->error(__('Please select the academic year you want to cancel course registration.'));
                    break;
                case empty($this->request->getData('Student.semester')):
                    $this->Flash->error(__('Please select the semester you want to cancel course registration.'));
                    break;
                case empty($this->request->getData('Student.program_id')):
                    $this->Flash->error(__('Please select the program you want to cancel courses registration.'));
                    break;
                case empty($this->request->getData('Student.program_type_id')):
                    $this->Flash->error(__('Please select the program type you want to cancel course registration.'));
                    break;
                default:
                    $everythingFine = true;
            }

            if ($everythingFine) {
                $this->initSearch();

                $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                $conditions = [
                    'PublishedCourses.drop' => 0,
                    'PublishedCourses.add' => 0,
                    'PublishedCourses.program_id' => $this->request->getData('Student.program_id'),
                    'PublishedCourses.program_type_id' => $this->request->getData('Student.program_type_id'),
                    'PublishedCourses.semester' => $this->request->getData('Student.semester'),
                    'PublishedCourses.academic_year' => $this->request->getData('Student.academic_year')
                ];

                if (!empty($this->request->getData('Student.department_id'))) {
                    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                    $yearLevelId = $yearLevelsTable->find()
                        ->select(['id'])
                        ->where([
                            'YearLevels.department_id' => $this->request->getData('Student.department_id'),
                            'YearLevels.name' => $this->request->getData('Student.year_level_id')
                        ])
                        ->first()
                        ->id ?? null;

                    $conditions['PublishedCourses.department_id'] = $this->request->getData('Student.department_id');
                    $conditions['PublishedCourses.year_level_id'] = $yearLevelId;

                    $sections = $sectionsTable->find('list')
                        ->where([
                            'Sections.department_id' => $this->request->getData('Student.department_id'),
                            'Sections.year_level_id' => $yearLevelId,
                            'Sections.program_id' => $this->request->getData('Student.program_id'),
                            'Sections.program_type_id' => $this->request->getData('Student.program_type_id'),
                            'Sections.academicyear' => $this->request->getData('Student.academic_year')
                        ])
                        ->toArray();
                } elseif (!empty($this->request->getData('Student.college_id'))) {
                    $conditions['PublishedCourses.college_id'] = $this->request->getData('Student.college_id');
                    $conditions['PublishedCourses.department_id IS'] = null;

                    $sections = $sectionsTable->find('list')
                        ->where([
                            'Sections.college_id' => $this->request->getData('Student.college_id'),
                            'Sections.department_id IS' => null,
                            'Sections.program_id' => $this->request->getData('Student.program_id'),
                            'Sections.program_type_id' => $this->request->getData('Student.program_type_id'),
                            'Sections.academicyear' => $this->request->getData('Student.academic_year')
                        ])
                        ->toArray();
                }

                $listOfPublishedCourses = $publishedCoursesTable->find()
                    ->select(['id', 'section_id'])
                    ->contain([
                        'Courses' => [
                            'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                            'fields' => ['id', 'course_title', 'course_code', 'credit', 'lecture_hours', 'tutorial_hours', 'laboratory_hours']
                        ],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'type']],
                        'Sections' => [
                            'fields' => ['id', 'name', 'academicyear', 'archive'],
                            'YearLevels' => ['fields' => ['id', 'name']],
                            'Programs' => ['fields' => ['id', 'name']],
                            'ProgramTypes' => ['fields' => ['id', 'name']],
                            'Departments' => ['fields' => ['id', 'name', 'type']],
                            'Colleges' => ['fields' => ['id', 'name', 'type']],
                            'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->where($conditions)
                    ->toArray();

                $organizedPublishedCourseBySection = [];
                $publishCoursesListIds = [];
                $publishedCounter = 0;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lv) {
                        if (!empty($lv->section_id)) {
                            $organizedPublishedCourseBySection[$lv->section_id][$publishedCounter] = $lv;
                            $publishCoursesListIds[$publishedCounter] = $lv->id;
                            $publishedCounter++;
                        }
                    }
                }

                if (empty($listOfPublishedCourses) && empty($this->request->getData('registerIndivdualCourse'))) {
                    $this->Flash->info(__('No result is found in the given criteria. Either all students are registered or no active section is found with published courses that need mass course registration.'));
                } else {
                    $this->set('hide_search', true);
                    $this->set(compact('sections', 'listOfPublishedCourses', 'organizedPublishedCourseBySection', 'publishedCounter'));
                }

                $yearLevelId = $this->request->getData('Student.year_level_id') ?? 'Pre/1st';

                $programsTable = TableRegistry::getTableLocator()->get('Programs');
                $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

                $programName = $programsTable->find()
                    ->select(['name'])
                    ->where(['Programs.id' => $this->request->getData('Student.program_id')])
                    ->first()
                    ->name ?? '';

                $programTypeName = $programTypesTable->find()
                    ->select(['name'])
                    ->where(['ProgramTypes.id' => $this->request->getData('Student.program_type_id')])
                    ->first()
                    ->name ?? '';

                $academicYear = $this->request->getData('Student.academic_year');
                $semester = $this->request->getData('Student.semester');

                if (!empty($this->request->getData('Student.department_id'))) {
                    $departmentName = $departmentsTable->find()
                        ->select(['id', 'name', 'type'])
                        ->where(['Departments.id' => $this->request->getData('Student.department_id')])
                        ->first();
                } elseif (!empty($this->request->getData('Student.college_id'))) {
                    $collegeName = $collegesTable->find()
                        ->select(['id', 'name', 'type'])
                        ->where(['Colleges.id' => $this->request->getData('Student.college_id')])
                        ->first();
                }

                $this->set(compact('sections', 'yearLevelId', 'programName', 'programTypeName', 'academicYear', 'semester', 'departmentName', 'collegeName'));
            }
        }

        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $yearLevels = $yearLevelsTable->distinctYearLevel();
        $programs = !empty($this->program_ids)
            ? $programsTable->find('list')->where(['Programs.id IN' => $this->program_ids])->toArray()
            : $programsTable->find('list')->toArray();
        $programTypes = !empty($this->program_type_ids)
            ? $programTypesTable->find('list')->where(['ProgramTypes.id IN' => $this->program_type_ids])->toArray()
            : $programTypesTable->find('list')->toArray();

        if ($this->Auth->user('role_id') == ROLE_REGISTRAR || $this->Auth->user('role_id') == ROLE_DEPARTMENT) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
            if (!empty($this->department_ids)) {
                $departments = $departmentsTable->allDepartmentsByCollege2(0, $this->department_ids, [], 1);
                $this->set(compact('departments'));
            } elseif (!empty($this->college_ids)) {
                $colleges = $collegesTable->find('list')
                    ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                    ->toArray();
                $this->set(compact('colleges'));
            }
        } elseif ($this->Auth->user('role_id') == ROLE_COLLEGE) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $departments = $departmentsTable->find('list')
                ->where(['Departments.college_id' => $this->college_id, 'Departments.active' => 1])
                ->toArray();
            $this->set(compact('departments'));
        } elseif ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $departments = $departmentsTable->find('list')
                ->where(['Departments.id' => $this->department_id])
                ->toArray();
            $yearLevels = $yearLevelsTable->find('list')
                ->where(['YearLevels.department_id' => $this->department_id])
                ->toArray();
            $this->request = $this->request->withData('Student.department_id', $this->department_id);
            $this->set(compact('departments'));
        }

        $this->set(compact('yearLevels', 'programs', 'programTypes'));
    }

    public function cancelRegistration()
    {
        $session = $this->request->getSession();
        if ($session->read('search_data_registration') && $this->request->getData('getsection') === null) {
            $this->request = $this->request->withData('getsection', true)
                ->withData('Student', $session->read('search_data_registration'));
            $this->set('hide_search', true);
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('canceregistration'))) {
            $oneIsSelected = 0;
            $selectedPublishedCourses = [];

            if (!empty($this->request->getData('PublishedCourse'))) {
                foreach ($this->request->getData('PublishedCourse') as $sectionId => $publishedCourse) {
                    foreach ($publishedCourse as $pId => $selected) {
                        if ($selected == 1) {
                            $oneIsSelected++;
                            $selectedPublishedCourses[] = $pId;
                        }
                    }
                }
            }

            if ($oneIsSelected) {
                if (!empty($selectedPublishedCourses)) {
                    $registerForDelete = ['register' => []];
                    $addForDelete = ['add' => []];
                    $gradeSubmittedPubCount = 0;

                    foreach ($selectedPublishedCourses as $pid) {
                        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                        $isGradeSubmitted = $examGradesTable->isGradeSubmitted($pid);

                        if (!$isGradeSubmitted) {
                            $tmp = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($pid);

                            if (!empty($tmp['register']) && count($tmp['register']) > 0) {
                                foreach ($tmp['register'] as $value) {
                                    if (!empty($value['CourseRegistration']['id'])) {
                                        $registerForDelete['register'][] = $value['CourseRegistration']['id'];
                                    }
                                    if (!empty($value['CourseAdd']['id'])) {
                                        $addForDelete['add'][] = $value['CourseAdd']['id'];
                                    }
                                }
                            }
                            if (!empty($tmp['add']) && count($tmp['add']) > 0) {
                                foreach ($tmp['add'] as $value) {
                                    if (!empty($value['CourseAdd']['id'])) {
                                        $addForDelete['add'][] = $value['CourseAdd']['id'];
                                    }
                                }
                            }
                        } else {
                            $gradeSubmittedPubCount++;
                        }
                    }

                    if (count($selectedPublishedCourses) != $gradeSubmittedPubCount) {
                        if (!empty($registerForDelete['register'])) {
                            $this->CourseRegistration->deleteAll(['CourseRegistrations.id IN' => $registerForDelete['register']],
                                false);
                        }
                        if (!empty($addForDelete['add'])) {
                            $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                            $courseAddsTable->deleteAll(['CourseAdds.id IN' => $addForDelete['add']], false);
                        }
                        if (!empty($registerForDelete['register']) || !empty($addForDelete['add'])) {
                            $this->Flash->success(__('Course registration is cancelled for all section students who registered/added for selected published course(s).'));
                        }
                    } else {
                        $this->Flash->error(__('You cannot cancel the selected course(s) registration as grades are already submitted for some or all students registered/added for the selected course(s).'));
                    }
                }
            } else {
                $this->Flash->error(__('Please select course(s) you want to cancel registration for those who were registered in the given section.'));
            }
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('getsection'))) {
            $session->delete('search_data_registration');
            $everythingFine = false;

            switch (true) {
                case empty($this->request->getData('Student.academic_year')):
                    $this->Flash->error(__('Please select the academic year you want to cancel course registration.'));
                    break;
                case empty($this->request->getData('Student.semester')):
                    $this->Flash->error(__('Please select the semester you want to cancel course registration.'));
                    break;
                case empty($this->request->getData('Student.program_id')):
                    $this->Flash->error(__('Please select the program you want to cancel courses registration.'));
                    break;
                case empty($this->request->getData('Student.program_type_id')):
                    $this->Flash->error(__('Please select the program type you want to cancel course registration.'));
                    break;
                default:
                    $everythingFine = true;
            }

            if ($everythingFine) {
                $this->initSearch();

                $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                $conditions = [
                    'PublishedCourses.drop' => 0,
                    'PublishedCourses.program_id' => $this->request->getData('Student.program_id'),
                    'PublishedCourses.program_type_id' => $this->request->getData('Student.program_type_id'),
                    'PublishedCourses.semester' => $this->request->getData('Student.semester'),
                    'PublishedCourses.academic_year' => $this->request->getData('Student.academic_year')
                ];

                if (!empty($this->request->getData('Student.department_id'))) {
                    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                    $yearLevelId = $yearLevelsTable->find()
                        ->select(['id'])
                        ->where([
                            'YearLevels.department_id' => $this->request->getData('Student.department_id'),
                            'YearLevels.name' => $this->request->getData('Student.year_level_id')
                        ])
                        ->first()
                        ->id ?? null;

                    $conditions['PublishedCourses.department_id'] = $this->request->getData('Student.department_id');
                    $conditions['PublishedCourses.year_level_id'] = $yearLevelId;

                    $sections = $sectionsTable->find('list')
                        ->where([
                            'Sections.department_id' => $this->request->getData('Student.department_id'),
                            'Sections.year_level_id' => $yearLevelId,
                            'Sections.program_id' => $this->request->getData('Student.program_id'),
                            'Sections.program_type_id' => $this->request->getData('Student.program_type_id'),
                            'Sections.academicyear' => $this->request->getData('Student.academic_year')
                        ])
                        ->toArray();
                } elseif (!empty($this->request->getData('Student.college_id'))) {
                    $conditions['PublishedCourses.college_id'] = $this->request->getData('Student.college_id');
                    $conditions['PublishedCourses.department_id IS'] = null;

                    $sections = $sectionsTable->find('list')
                        ->where([
                            'Sections.college_id' => $this->request->getData('Student.college_id'),
                            'Sections.department_id IS' => null,
                            'Sections.program_id' => $this->request->getData('Student.program_id'),
                            'Sections.program_type_id' => $this->request->getData('Student.program_type_id'),
                            'Sections.academicyear' => $this->request->getData('Student.academic_year')
                        ])
                        ->toArray();
                }

                $listOfPublishedCourses = $publishedCoursesTable->find()
                    ->select(['id', 'section_id'])
                    ->contain([
                        'Courses' => [
                            'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                            'fields' => ['id', 'course_title', 'course_code', 'credit', 'lecture_hours', 'tutorial_hours', 'laboratory_hours']
                        ],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'type']],
                        'Sections' => [
                            'fields' => ['id', 'name', 'academicyear', 'archive'],
                            'YearLevels' => ['fields' => ['id', 'name']],
                            'Programs' => ['fields' => ['id', 'name']],
                            'ProgramTypes' => ['fields' => ['id', 'name']],
                            'Departments' => ['fields' => ['id', 'name', 'type']],
                            'Colleges' => ['fields' => ['id', 'name', 'type']],
                            'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->where($conditions)
                    ->toArray();

                $organizedPublishedCourseBySection = [];
                $publishCoursesListIds = [];
                $publishedCounter = 0;
                $gradeSubmittedCounter = 0;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lv) {
                        if (!empty($lv->section_id)) {
                            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                            $isGradeSubmitted = $examGradesTable->isGradeSubmitted($lv->id);
                            $anyRegistration = $this->CourseRegistration->find()
                                ->where(['CourseRegistrations.published_course_id' => $lv->id])
                                ->count();

                            if ($anyRegistration) {
                                $organizedPublishedCourseBySection[$lv->section_id][$publishedCounter] = $lv;
                                $organizedPublishedCourseBySection[$lv->section_id][$publishedCounter]['grade_submitted'] = $isGradeSubmitted ? 1 : 0;
                                if ($isGradeSubmitted) {
                                    $gradeSubmittedCounter++;
                                }
                                $publishCoursesListIds[$publishedCounter] = $lv->id;
                            }
                            $publishedCounter++;
                        }
                    }
                }

                $publishedCourseRegister = $this->CourseRegistration->find()
                    ->contain(['ExamGrades', 'PublishedCourses' => ['Courses']])
                    ->where([
                        'CourseRegistrations.published_course_id IN' => $publishCoursesListIds,
                        'CourseRegistrations.published_course_id IN (SELECT published_course_id FROM course_registrations)',
                        'CourseRegistrations.id NOT IN (SELECT course_registration_id FROM exam_grades WHERE course_registration_id IS NOT NULL)'
                    ])
                    ->order(['CourseRegistrations.id' => 'DESC'])
                    ->toArray();

                $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                $publishedCourseAdd = $courseAddsTable->find()
                    ->contain(['ExamGrades', 'PublishedCourses' => ['Courses']])
                    ->where([
                        'CourseAdds.published_course_id IN' => $publishCoursesListIds,
                        'CourseAdds.published_course_id IN (SELECT published_course_id FROM course_adds)',
                        'CourseAdds.id NOT IN (SELECT course_add_id FROM exam_grades WHERE course_add_id IS NOT NULL)'
                    ])
                    ->toArray();

                if (empty($publishedCourseRegister) && empty($publishedCourseAdd) && empty($this->request->getData('canceregistration'))) {
                    $this->Flash->info(__('No result is found. Either grades are submitted or there is no course registration in the selected criteria.'));
                } else {
                    $this->set('hide_search', true);
                    $listOfPublishedCourses = $organizedPublishedCourseBySection;
                    $this->set(compact('sections', 'listOfPublishedCourses', 'organizedPublishedCourseBySection', 'publishedCounter', 'gradeSubmittedCounter'));
                }

                $yearLevelId = $this->request->getData('Student.year_level_id') ?? 'Pre/1st';

                $programsTable = TableRegistry::getTableLocator()->get('Programs');
                $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

                $programName = $programsTable->find()
                    ->select(['name'])
                    ->where(['Programs.id' => $this->request->getData('Student.program_id')])
                    ->first()
                    ->name ?? '';

                $programTypeName = $programTypesTable->find()
                    ->select(['name'])
                    ->where(['ProgramTypes.id' => $this->request->getData('Student.program_type_id')])
                    ->first()
                    ->name ?? '';

                $academicYear = $this->request->getData('Student.academic_year');
                $semester = $this->request->getData('Student.semester');

                if (!empty($this->request->getData('Student.department_id'))) {
                    $departmentName = $departmentsTable->find()
                        ->select(['id', 'name', 'type'])
                        ->where(['Departments.id' => $this->request->getData('Student.department_id')])
                        ->first();
                } elseif (!empty($this->request->getData('Student.college_id'))) {
                    $collegeName = $collegesTable->find()
                        ->select(['id', 'name', 'type'])
                        ->where(['Colleges.id' => $this->request->getData('Student.college_id')])
                        ->first();
                }

                $this->set(compact('sections', 'yearLevelId', 'programName', 'programTypeName', 'academicYear', 'semester', 'departmentName', 'collegeName'));
            }
        }

        $currentAcy = $this->AcademicYear->currentAcademicYear();
        if (defined('ACY_BACK_COURSE_REGISTRATION') && is_numeric(ACY_BACK_COURSE_REGISTRATION)) {
            $academicYearList = $this->AcademicYear->academicYearInArray(
                explode('/', $currentAcy)[0] - ACY_BACK_COURSE_REGISTRATION,
                explode('/', $currentAcy)[0]
            );
        } else {
            $academicYearList = [$currentAcy => $currentAcy];
        }

        if ($this->Auth->user('role_id') == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 1) {
            $academicYearList = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, explode('/', $currentAcy)[0]);
        }

        $this->set(compact('academicYearList'));
    }

    public function showCourseRegisteredStudents($publishedCourseId = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $registeredStudents = $this->CourseRegistration->PublishedCourse->getStudentsTakingPublishedCourse($publishedCourseId);
        $this->set(compact('registeredStudents'));
    }

    public function getCourseRegisteredGradeList($registerOrAdd = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $gradeScale = [];

        if ($registerOrAdd && $registerOrAdd != "0") {
            $registerOrAdd = explode('~', $registerOrAdd);
            if (strcasecmp($registerOrAdd[1], 'add') == 0) {
                $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                $publishedCourseId = $courseAddsTable->find()
                    ->select(['published_course_id'])
                    ->where(['id' => $registerOrAdd[0]])
                    ->first()
                    ->published_course_id;
            } else {
                $publishedCourseId = $this->CourseRegistration->find()
                    ->select(['published_course_id'])
                    ->where(['id' => $registerOrAdd[0]])
                    ->first()
                    ->published_course_id;
            }
            $gradeScale = $this->CourseRegistration->PublishedCourse->CourseRegistration->getPublishedCourseGradeScaleList($publishedCourseId);
            $gradeScale = ['0' => '[ Select Grade ]'] + $gradeScale;
        }
        $this->set(compact('gradeScale'));
    }

    public function getCourseRegisteredGradeResult($registerOrAdd = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $gradeHistory = [];

        if ($registerOrAdd && $registerOrAdd != "0") {
            $registerOrAdd = explode('~', $registerOrAdd);
            if (count($registerOrAdd) == 2) {
                if ($registerOrAdd[1] == 'register') {
                    $gradeHistory = $this->CourseRegistration->getCourseRegistrationGradeHistory($registerOrAdd[0]);
                } else {
                    $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                    $gradeHistory = $courseAddsTable->getCourseAddGradeHistory($registerOrAdd[0]);
                }
            }
        }
        $this->set(compact('gradeHistory', 'registerOrAdd'));
    }

    public function givenPublishedCourseReturnDept($publishCourseIds = [])
    {
        $departmentCollegesIds = ['dept' => [], 'college' => []];

        if (!empty($publishCourseIds)) {
            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            foreach ($publishCourseIds as $id) {
                $collegeDepartment = $publishedCoursesTable->find()
                    ->select(['department_id', 'college_id'])
                    ->where(['PublishedCourses.id' => $id])
                    ->first();

                if (!empty($collegeDepartment->department_id)) {
                    $departmentCollegesIds['dept'][] = $collegeDepartment->department_id;
                } else {
                    $departmentCollegesIds['college'][] = $collegeDepartment->college_id;
                }
            }
        }
        return $departmentCollegesIds;
    }

    public function cancelIndividualRegistration($studentId = null)
    {
        $session = $this->request->getSession();
        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('canceregistration'))) {
            $registrationListForDelete = array_keys($this->request->getData('CourseRegistration'));

            if ($this->CourseRegistration->deleteAll(['CourseRegistrations.id IN' => $registrationListForDelete], false)) {
                $this->Flash->success(__('The selected student course registration cancellation is successful.'));
            }

            $session->delete('search_data_registration');
            $this->request = $this->request->withData('getstudentregistration', null);
        }

        if ($session->read('search_data_registration') && $this->request->getData('getstudentregistration') === null) {
            $this->request = $this->request->withData('getstudentregistration', true)
                ->withData('Student', $session->read('search_data_registration'));
            $this->set('hide_search', true);
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('getstudentregistration'))) {
            $session->delete('search_data_registration');
            $everythingFine = false;

            switch (true) {
                case empty($this->request->getData('Student.academic_year')):
                    $this->Flash->error(__('Please select the academic year you want to cancel course registration.'));
                    break;
                case empty($this->request->getData('Student.semester')):
                    $this->Flash->error(__('Please select the semester you want to cancel course registration.'));
                    break;
                case empty($this->request->getData('Student.studentnumber')):
                    $this->Flash->error(__('Please provide the student number (ID) you want to cancel course registration.'));
                    break;
                default:
                    $everythingFine = true;
            }

            if ($everythingFine) {
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $checkIdIsValid = $studentsTable->find()
                    ->where(['Students.studentnumber' => trim($this->request->getData('Student.studentnumber'))])
                    ->count();

                if ($checkIdIsValid == 0) {
                    $everythingFine = false;
                    $this->Flash->error(__('The provided student number is not valid.'));
                }
            }

            if ($everythingFine) {
                $this->initSearch();

                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $studentDbId = $studentsTable->find()
                    ->select(['id'])
                    ->where(['Students.studentnumber' => $this->request->getData('Student.studentnumber')])
                    ->first()
                    ->id;

                $studentSection = $studentsTable->studentAcademicDetail($studentDbId, $this->request->getData('Student.academic_year'));
                $studentSectionExamStatus = $studentsTable->getStudentSection(
                    $studentDbId,
                    $this->request->getData('Student.academic_year'),
                    $this->request->getData('Student.semester')
                );

                $courseRegistrationIdPublishIds = $this->CourseRegistration->find()
                    ->select(['CourseRegistrations.id', 'CourseRegistrations.published_course_id'])
                    ->where([
                        'CourseRegistrations.student_id' => $studentDbId,
                        'CourseRegistrations.semester' => $this->request->getData('Student.semester'),
                        'CourseRegistrations.academic_year' => $this->request->getData('Student.academic_year')
                    ])
                    ->toArray();

                $publishCourseIds = array_column($courseRegistrationIdPublishIds, 'published_course_id');

                $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                $listOfPublishedCourses = $publishedCoursesTable->find()
                    ->select(['id', 'section_id'])
                    ->contain([
                        'Courses' => [
                            'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                            'fields' => ['id', 'course_title', 'course_code', 'credit', 'lecture_hours', 'tutorial_hours', 'laboratory_hours']
                        ],
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'type']],
                        'Sections' => [
                            'fields' => ['id', 'name', 'academicyear', 'archive'],
                            'YearLevels' => ['fields' => ['id', 'name']],
                            'Programs' => ['fields' => ['id', 'name']],
                            'ProgramTypes' => ['fields' => ['id', 'name']],
                            'Departments' => ['fields' => ['id', 'name', 'type']],
                            'Colleges' => ['fields' => ['id', 'name', 'type']],
                            'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->where([
                        'PublishedCourses.id IN' => $publishCourseIds,
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.academic_year' => $this->request->getData('Student.academic_year'),
                        'PublishedCourses.semester' => $this->request->getData('Student.semester')
                    ])
                    ->toArray();

                $organizedPublishedCourseBySection = [];
                $publishCoursesListIds = [];
                $publishedCounter = 0;
                $gradeSubmittedCounter = 0;
                $isGradeSubmittedToAnyCourse = false;

                if (!empty($listOfPublishedCourses)) {
                    foreach ($listOfPublishedCourses as $lv) {
                        if (!empty($lv->section_id)) {
                            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                            $isGradeSubmitted = $examGradesTable->isGradeSubmitted($lv->id);
                            $organizedPublishedCourseBySection[$lv->section_id][$publishedCounter] = $lv;

                            if ($isGradeSubmitted) {
                                $organizedPublishedCourseBySection[$lv->section_id][$publishedCounter]['grade_submitted'] = 1;
                                $gradeSubmittedCounter++;
                                $isGradeSubmittedToAnyCourse = true;
                            } else {
                                $organizedPublishedCourseBySection[$lv->section_id][$publishedCounter]['grade_submitted'] = 0;
                            }
                            $publishCoursesListIds[$publishedCounter] = $lv->id;
                            $publishedCounter++;
                        }
                    }
                }

                if (empty($listOfPublishedCourses)) {
                    $this->Flash->info(__('No result is found. There is no course registration in the selected criteria.'));
                } else {
                    $this->set('hide_search', true);
                    $listOfPublishedCourses = $organizedPublishedCourseBySection;
                    $this->set(compact('listOfPublishedCourses', 'organizedPublishedCourseBySection', 'publishedCounter', 'gradeSubmittedCounter', 'studentSectionExamStatus', 'isGradeSubmittedToAnyCourse', 'courseRegistrationIdPublishIds'));
                }
            }
        }

        $currentAcy = $this->AcademicYear->currentAcademicYear();
        if (defined('ACY_BACK_COURSE_REGISTRATION') && is_numeric(ACY_BACK_COURSE_REGISTRATION)) {
            $academicYearList = $this->AcademicYear->academicYearInArray(
                explode('/', $currentAcy)[0] - ACY_BACK_COURSE_REGISTRATION,
                explode('/', $currentAcy)[0]
            );
        } else {
            $academicYearList = [$currentAcy => $currentAcy];
        }

        if ($this->Auth->user('role_id') == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 1) {
            $academicYearList = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, explode('/', $currentAcy)[0]);
        }

        $this->set(compact('academicYearList'));
    }


    public function gradeViewByCourse()
    {
        $this->paginate = [
            'contain' => [
                'Students' => [
                    'Departments',
                    'Curriculums',
                    'ProgramTypes',
                    'Programs'
                ],
                'ExamGrades' => [
                    'sort' => ['ExamGrades.created' => 'DESC']
                ]
            ]
        ];

        $session = $this->request->getSession();
        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('CourseRegistration')) && !empty($this->request->getData('viewPDF'))) {
            $this->request = $this->request->withData('CourseRegistration', $session->read('search_data_list_course'));
        }

        if (!empty($this->passedArgs)) {
            if (isset($this->passedArgs['page'])) {
                $this->initSearchCourseLists();
                $this->request = $this->request->withData('CourseRegistration.page', $this->passedArgs['page']);
                $this->initSearchCourseLists();
            }
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('CourseRegistration')) && !empty($this->request->getData('listStudentWithGrade'))) {
            $this->initSearchCourseLists();
        }

        $this->paginate['limit'] = !empty($this->request->getData('CourseRegistration.limit'))
            ? $this->request->getData('CourseRegistration.limit')
            : 50;

        if (!empty($this->request->getData('CourseRegistration.department_id'))) {
            $this->paginate['conditions']['Students.department_id'] = $this->request->getData('CourseRegistration.department_id');
        }

        if (!empty($this->request->getData('CourseRegistration.college_id'))) {
            $this->paginate['conditions']['Students.college_id'] = $this->request->getData('CourseRegistration.college_id');
            if ($this->request->getData('CourseRegistration.college_id') == 'pre') {
                $this->paginate['conditions']['Students.department_id IS'] = null;
            }
        }

        if (!empty($this->request->getData('CourseRegistration.program_id'))) {
            $this->paginate['conditions']['Students.program_id'] = $this->request->getData('CourseRegistration.program_id');
        }

        if (!empty($this->request->getData('CourseRegistration.program_type_id'))) {
            $this->paginate['conditions']['Students.program_type_id'] = $this->request->getData('CourseRegistration.program_type_id');
        }

        if (!empty($this->request->getData('CourseRegistration.course_id'))) {
            $connection = ConnectionManager::get('default');
            $listCourseRegistrationIdsSql = sprintf(
                "SELECT GROUP_CONCAT(cr.id) as ids FROM course_registrations AS cr, published_courses AS ps WHERE cr.academic_year='%s' AND cr.semester='%s' AND ps.semester='%s' AND ps.academic_year='%s' AND ps.id = cr.published_course_id AND ps.course_id=%d AND ps.program_id=%d AND ps.program_type_id=%d AND cr.published_course_id=ps.id ORDER BY GROUP_CONCAT(cr.id)",
                $this->request->getData('CourseRegistration.acadamic_year'),
                $this->request->getData('CourseRegistration.semester'),
                $this->request->getData('CourseRegistration.semester'),
                $this->request->getData('CourseRegistration.acadamic_year'),
                $this->request->getData('CourseRegistration.course_id'),
                $this->request->getData('CourseRegistration.program_id'),
                $this->request->getData('CourseRegistration.program_type_id')
            );
            $listCourseRegistrationIdsQueryResult = $connection->execute($listCourseRegistrationIdsSql)->fetchAll('assoc');

            if (!empty($listCourseRegistrationIdsQueryResult[0]['ids'])) {
                $this->paginate['conditions']['CourseRegistrations.id IN'] = explode(',', $listCourseRegistrationIdsQueryResult[0]['ids']);
            }
        }

        if (!empty($this->request->getData('CourseRegistration.sortby'))) {
            $this->paginate['sort'] = 'Students.' . $this->request->getData('CourseRegistration.sortby');
            $this->paginate['direction'] = 'ASC';
            $this->paginate['sortWhitelist'] = ['Students.middle_name', 'Students.last_name', 'Students.studentnumber'];
        }

        if (!empty($this->request->getData('CourseRegistration.page'))) {
            $this->paginate['page'] = $this->request->getData('CourseRegistration.page');
        }

        $studentExamGradeList = !empty($this->paginate['conditions'])
            ? $this->paginate($this->CourseRegistration)
            : [];

        if (empty($studentExamGradeList) && !empty($this->request->getData())) {
            $this->Flash->info(__('No student taking the course and scored grade for the selected course.'));
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('CourseRegistration')) && !empty($this->request->getData('viewPDF'))) {
            $this->viewBuilder()->setLayout(false);
            $coursesTable = TableRegistry::getTableLocator()->get('Courses');
            $courseDetail = $coursesTable->find()
                ->contain(['Curriculums', 'YearLevels'])
                ->where(['Courses.id' => $this->request->getData('CourseRegistration.course_id')])
                ->first();

            $academicYear = $this->request->getData('CourseRegistration.acadamic_year');
            $semester = $this->request->getData('CourseRegistration.semester');

            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $department = $departmentsTable->find()
                ->contain(['Colleges' => ['Campuses']])
                ->where(['Departments.id' => $this->request->getData('CourseRegistration.department_id')])
                ->first();

            $programsTable = TableRegistry::getTableLocator()->get('Programs');
            $program = $programsTable->find()
                ->where(['Programs.id' => $this->request->getData('CourseRegistration.program_id')])
                ->first();

            $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
            $programType = $programTypesTable->find()
                ->where(['ProgramTypes.id' => $this->request->getData('CourseRegistration.program_type_id')])
                ->first();

            $universitiesTable = TableRegistry::getTableLocator()->get('Universities');
            $university = $universitiesTable->getStudentUniversity($studentExamGradeList[0]->student_id);
            $filename = "Roaster-{$department->name} Academic_Year-{$academicYear} Semester-{$semester}";

            $this->set(compact(
                'courseDetail',
                'department',
                'program',
                'programType',
                'university',
                'studentExamGradeList',
                'filename',
                'academicYear',
                'semester'
            ));

            return $this->render('grade_view_xls');
        }

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

        if (!empty($this->department_ids)) {
            $departments = $departmentsTable->find('list')
                ->where(['Departments.id IN' => $this->department_ids])
                ->toArray();
        } elseif (!empty($this->college_ids)) {
            $colleges = $collegesTable->find('list')
                ->where(['Colleges.id IN' => $this->college_ids])
                ->toArray();
        } else {
            if (!empty($this->department_id) && $this->Auth->user('role_id') == ROLE_DEPARTMENT) {
                $departments = $departmentsTable->find('list')
                    ->where(['Departments.id' => $this->department_id])
                    ->toArray();
            } elseif (!empty($this->college_id) && $this->Auth->user('role_id') == ROLE_COLLEGE) {
                $colleges = $collegesTable->find('list')
                    ->where(['Colleges.id' => $this->college_id])
                    ->toArray();
                $colleges['pre'] = 'Pre Engineering';
            }
        }

        $selectedAcademicYear = $this->AcademicYear->currentAcademicYear();
        $defaultSemester = 'I';

        if (!empty($this->request->getData('CourseRegistration.acadamic_year'))) {
            $selectedAcademicYear = $this->request->getData('CourseRegistration.acadamic_year');
        }

        if (!empty($this->request->getData('CourseRegistration.semester'))) {
            $defaultSemester = $this->request->getData('CourseRegistration.semester');
        }

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = $programsTable->find('list')->toArray();
        $programTypes = $programTypesTable->find('list')->toArray();

        $defaultDepartment = !empty($this->request->getData('CourseRegistration.department_id'))
            ? $this->request->getData('CourseRegistration.department_id')
            : (!empty($departments) ? array_key_first($departments) : null);

        $defaultCollege = !empty($this->request->getData('CourseRegistration.college_id'))
            ? $this->request->getData('CourseRegistration.college_id')
            : (!empty($colleges) ? array_key_first($colleges) : null);

        $defaultProgram = !empty($this->request->getData('CourseRegistration.program_id'))
            ? $this->request->getData('CourseRegistration.program_id')
            : array_key_first($programs);

        $defaultProgramType = !empty($this->request->getData('CourseRegistration.program_type_id'))
            ? $this->request->getData('CourseRegistration.program_type_id')
            : array_key_first($programTypes);

        $courses = $defaultDepartment
            ? $this->getCourseLists(
                $selectedAcademicYear,
                $defaultSemester,
                $defaultProgram,
                $defaultProgramType,
                $defaultCollege,
                $defaultDepartment,
                false
            )
            : $this->getCourseLists(
                $selectedAcademicYear,
                $defaultSemester,
                $defaultProgram,
                $defaultProgramType,
                $defaultCollege,
                $defaultDepartment,
                true
            );

        $sortOptions = [
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'studentnumber' => 'Student ID'
        ];

        $this->set(compact(
            'programs',
            'courses',
            'programTypes',
            'departments',
            'sortOptions',
            'colleges',
            'studentExamGradeList'
        ));
    }

    public function getCourseCategoryCombo($parameters)
    {
        $this->viewBuilder()->setLayout('ajax');
        $courseLists = [];
        $criteriaLists = explode('~', $parameters);

        if (!empty($criteriaLists[0])) {
            $courseLists = $this->getCourseLists(
                str_replace('-', '/', $criteriaLists[2]),
                $criteriaLists[3],
                $criteriaLists[4],
                $criteriaLists[5],
                $criteriaLists[1],
                $criteriaLists[0],
                false
            );
        } elseif (!empty($criteriaLists[1])) {
            $courseLists = $this->getCourseLists(
                str_replace('-', '/', $criteriaLists[2]),
                $criteriaLists[3],
                $criteriaLists[4],
                $criteriaLists[5],
                $criteriaLists[1],
                $criteriaLists[0],
                $criteriaLists[1] == 'pre'
            );
        }

        $this->set(compact('courseLists'));
    }

    public function getCourseLists($academicYear, $semester, $programId, $programTypeId, $collegeId = null, $departmentId = null, $pre = false)
    {
        $courseLists = [];
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $conditions = [
            'PublishedCourses.academic_year' => $academicYear,
            'PublishedCourses.semester' => $semester,
            'PublishedCourses.program_id' => $programId,
            'PublishedCourses.program_type_id' => $programTypeId
        ];

        if ($pre) {
            $conditions['PublishedCourses.college_id'] = $collegeId;
            $conditions['PublishedCourses.department_id IS'] = null;
        } elseif (!empty($departmentId)) {
            $conditions['PublishedCourses.department_id'] = $departmentId;
        } elseif (!empty($collegeId)) {
            $conditions['PublishedCourses.college_id'] = $collegeId;
        }

        $courses = $publishedCoursesTable->find()
            ->contain(['Courses'])
            ->where($conditions)
            ->toArray();

        foreach ($courses as $course) {
            $courseLists[$course->course->id] = sprintf(
                '%s (%s-%s)',
                $course->course->course_title,
                $course->course->course_code,
                $course->course->credit
            );
        }

        return $courseLists;
    }

    public function initSearchCourseLists()
    {
        $session = $this->request->getSession();
        if (!empty($this->request->getData('CourseRegistration'))) {
            $session->write('search_data_list_course', $this->request->getData('CourseRegistration'));
        } else {
            $this->request = $this->request->withData('CourseRegistration', $session->read('search_data_list_course'));
        }
    }

    public function index($academicYear = null, $semester = null)
    {
        if (empty($academicYear) && empty($semester)) {
            $this->initSearchIndex();
        }

        $this->viewRegistration($academicYear, $semester);
    }

    public function viewRegistration($academicYear = null, $semester = null)
    {
        $options = [
            'contain' => [
                'Students' => [
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream']],
                    'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']],
                    'sort' => [
                        'Students.academicyear' => 'DESC',
                        'Students.studentnumber' => 'ASC',
                        'Students.id' => 'ASC',
                        'Students.first_name' => 'ASC',
                        'Students.middle_name' => 'ASC',
                        'Students.last_name' => 'ASC',
                        'Students.program_id' => 'ASC',
                        'Students.program_type_id' => 'ASC'
                    ]
                ],
                'YearLevels',
                'CourseDrops',
                'PublishedCourses' => [
                    'Courses' => [
                        'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                    ],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Departments' => ['fields' => ['id', 'name', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream']],
                    'Sections' => [
                        'fields' => ['id', 'name', 'academicyear', 'archive'],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Programs' => ['fields' => ['id', 'name', 'shortname']],
                        'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'shortname', 'type', 'stream']],
                        'Curriculums' => ['fields' => ['id', 'name', 'type_credit', 'year_introduced', 'active']]
                    ],
                    'YearLevels' => ['fields' => ['id', 'name']]
                ]
            ],
            'sort' => [
                'CourseRegistrations.year_level_id' => 'ASC',
                'CourseRegistrations.academic_year' => 'DESC',
                'CourseRegistrations.semester' => 'DESC',
                'CourseRegistrations.published_course_id' => 'ASC',
                'CourseRegistrations.id' => 'DESC'
            ]
        ];

        $preCollegeIdViaDeptSelectOption = 0;

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('generateRegisteredList'))) {
            $options['group'] = ['CourseRegistrations.student_id'];
        }

        $session = $this->request->getSession();
        if (!empty($academicYear) && !empty($semester)) {
            $this->request = $this->request
                ->withData('Search.academic_year', str_replace('-', '/', $academicYear))
                ->withData('Search.semester', $semester);
            if (!defined('ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY') || !ALLOW_GRADE_REPORTABLE_SEMITER_ONLY) {
                $this->request = $this->request->withData('search', true);
                $session->write('search_data_index', $this->request->getData());
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->initSearchIndex();
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('Search'))) {
            if ($this->Auth->user('role_id') == ROLE_STUDENT && !empty($this->request->getData('search'))) {
                $options['conditions']['CourseRegistrations.student_id'] = $this->student_id;

                if (!empty($this->request->getData('Search.semester'))) {
                    $options['conditions']['CourseRegistrations.semester'] = $this->request->getData('Search.semester');
                }

                if (!empty($this->request->getData('Search.academic_year'))) {
                    $options['conditions']['CourseRegistrations.academic_year'] = $this->request->getData('Search.academic_year');
                }
            } else {
                if (empty($this->request->getData('Search.section_id'))) {
                    $this->Flash->error(__('Please select a section from the list.'));
                    return $this->redirect(['action' => 'index']);
                }

                if (!empty($this->request->getData('Search.department_id'))) {
                    $cOrD = explode('~', $this->request->getData('Search.department_id'));
                    if ($cOrD[0] == 'c') {
                        $preCollegeIdViaDebtSelectOption = $cOrD[1];
                        $options['conditions']['Students.college_id'] = $cOrD[1];
                    } else {
                        $options['conditions']['Students.department_id'] = $this->request->getData('Search.department_id');
                    }
                } elseif (!empty($this->request->getData('Search.college_id'))) {
                    $options['conditions']['Students.college_id'] = $this->request->getData('Search.college_id');
                    if ($this->onlyPre) {
                        $options['conditions']['Students.department_id IS'] = null;
                    }
                }

                if (!empty($this->request->getData('Search.program_id'))) {
                    $options['conditions']['Students.program_id'] = $this->request->getData('Search.program_id');
                }

                if (!empty($this->request->getData('Search.program_type_id'))) {
                    $options['conditions']['Students.program_type_id'] = $this->request->getData('Search.program_type_id');
                }

                if (!empty($this->request->getData('Search.semester'))) {
                    $options['conditions']['CourseRegistrations.semester'] = $this->request->getData('Search.semester');
                }

                if (!empty($this->request->getData('Search.academic_year'))) {
                    $options['conditions']['CourseRegistrations.academic_year'] = $this->request->getData('Search.academic_year');
                }

                if (!empty($this->request->getData('Search.section_id'))) {
                    if ($preCollegeIdViaDebtSelectOption) {
                        $options['conditions']['CourseRegistrations.section_id'] = $this->request->getData('Search.section_id');
                        $options['conditions']['OR'] = [
                            'CourseRegistrations.year_level_id IS' => null,
                            'CourseRegistrations.year_level_id' => 0,
                            'CourseRegistrations.year_level_id' => ''
                        ];
                    } else {
                        $options['conditions']['CourseRegistrations.section_id'] = $this->request->getData('Search.section_id');
                    }
                }

                if (!empty(trim($this->request->getData('Search.studentnumber')))) {
                    $studentsTable = TableRegistry::getTableLocator()->get('Students');
                    $studentId = $studentsTable->find()
                        ->select(['id'])
                        ->where(['Students.studentnumber' => trim($this->request->getData('Search.studentnumber'))])
                        ->first()
                        ->id ?? null;

                    $deptIds = array_filter(array_merge((array)($this->department_ids ?? []), (array)($this->department_id ?? [])));
                    $collIds = array_filter(array_merge((array)($this->college_ids ?? []), (array)($this->college_id ?? [])));

                    $conditions = ['Students.id' => $studentId];
                    if (!empty($deptIds) && !empty($collIds) && !$this->onlyPre) {
                        $conditions['OR'] = [
                            'Students.department_id IN' => $deptIds,
                            'Students.college_id IN' => $collIds
                        ];
                    } elseif (!empty($deptIds)) {
                        $conditions['Students.department_id IN'] = $deptIds;
                    } elseif (!empty($collIds)) {
                        $conditions['Students.college_id IN'] = $collIds;
                    }

                    $eligibleUser = $studentsTable->find()
                        ->where($conditions)
                        ->count();

                    if ($eligibleUser == 0) {
                        $this->Flash->error(__('You do not have the privilege to view the selected student details.'));
                        $this->request = $this->request->withData(null);
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $options['conditions'] = [];
                        if (!empty($this->department_ids)) {
                            $options['conditions']['Students.department_id IN'] = $this->department_ids;
                        } elseif (!empty($this->college_ids)) {
                            $options['conditions']['Students.college_id IN'] = $this->college_ids;
                        } elseif ($preCollegeIdViaDebtSelectOption) {
                            $options['conditions']['Students.college_id'] = $preCollegeIdViaDebtSelectOption;
                        } else {
                            $options['conditions']['Students.department_id'] = $this->request->getData('Search.department_id');
                        }
                        $options['conditions']['Students.studentnumber'] = trim($this->request->getData('Search.studentnumber'));
                    }
                }
            }
        } else {
            $options = [];
        }

        $courseRegistrations = (!empty($options['conditions']))
            ? $this->CourseRegistration->find('all', $options)->toArray()
            : [];

        if ($this->Auth->user('role_id') != ROLE_STUDENT) {
            if (empty($courseRegistrations) && (!empty($options['conditions']) || !empty($this->request->getData('search')))) {
                $this->Flash->info(__('No Course Registration is found with the given search criteria.'));
            }
        } else {
            if (empty($courseRegistrations) && (!empty($options['conditions']) || !empty($this->request->getData('search')))) {
                if (defined('ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY') && ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY) {
                    $this->Flash->info(__('You can check your latest registration here in Registration or Results tab.'));
                    return $this->redirect(['controller' => 'Students', 'action' => 'student_academic_profile']);
                } else {
                    $this->Flash->info(__('No Course Registration is found with the given search criteria.'));
                }
            }
        }

        if ($this->request->is(['post', 'put']) && (!empty($this->request->getData('generateSlip')) || !empty($this->request->getData('getGradeReport')))) {
            if (!empty($courseRegistrations)) {
                $studentCopies = [];
                $studentNumber = '';

                $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $universitiesTable = TableRegistry::getTableLocator()->get('Universities');
                $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

                if (!empty($this->request->getData('Search.studentnumber')) && $this->Auth->user('role_id') == ROLE_REGISTRAR) {
                    $studentNumber = $this->request->getData('Search.studentnumber');
                    $studentsTable = TableRegistry::getTableLocator()->get('Students');
                    $studentId = $studentsTable->find()
                        ->select(['id'])
                        ->where(['Students.studentnumber' => $studentNumber])
                        ->first()
                        ->id ?? null;

                    if ($studentId) {
                        $acSemylist = $this->CourseRegistration->find()
                            ->select(['academic_year', 'semester'])
                            ->where(['CourseRegistrations.student_id' => $studentId])
                            ->group(['CourseRegistrations.student_id', 'CourseRegistrations.academic_year', 'CourseRegistrations.semester'])
                            ->order(['CourseRegistrations.created' => 'ASC'])
                            ->toArray();

                        foreach ($acSemylist as $count => $acsemval) {
                            $studentCopy = $examGradesTable->getStudentCopy(
                                $studentId,
                                $acsemval->academic_year,
                                $acsemval->semester
                            );
                            if (!empty($studentCopy['courses'])) {
                                $studentCopy['University'] = $universitiesTable->getStudentUniversity($studentId);
                                $studentCopies[$count] = $studentCopy;
                            }
                        }
                    }
                } else {
                    $firstStudentId = $courseRegistrations[0]->student_id;
                    foreach ($courseRegistrations as $v) {
                        if (!empty($this->request->getData('getGradeReport'))) {
                            $checkStatusGenerated = $studentExamStatusesTable->find()
                                ->where([
                                    'StudentExamStatuses.student_id' => $v->student_id,
                                    'StudentExamStatuses.academic_year' => $v->academic_year,
                                    'StudentExamStatuses.semester' => $v->semester
                                ])
                                ->count();

                            if ($checkStatusGenerated) {
                                if ($this->Auth->user('role_id') == ROLE_STUDENT) {
                                    $studentExamStatusesTable->regenerateAllStatusOfStudentByStudentId($this->student_id);
                                }
                                $studentCopy = $examGradesTable->getStudentCopy(
                                    $v->student_id,
                                    $v->academic_year,
                                    $v->semester
                                );
                            } else {
                                continue;
                            }
                        } else {
                            $studentCopy = $examGradesTable->getStudentCopy(
                                $v->student_id,
                                $v->academic_year,
                                $v->semester
                            );
                        }

                        if (!empty($studentCopy['courses'])) {
                            $studentCopy['University'] = $universitiesTable->getStudentUniversity($v->student_id);
                            $studentCopy['RegistrationDate'] = $v->created;
                            $studentCopies[$v->student_id] = $studentCopy;
                        }
                    }
                }

                if ($this->Auth->user('role_id') == ROLE_STUDENT) {
                    $studentNumber = $this->request->getData('Search.studentnumber');
                }

                if (!empty($studentCopies)) {
                    $this->viewBuilder()->setLayout('pdf/default');
                    $this->response = $this->response->withType('application/pdf');
                    $this->set(compact('studentCopies', 'studentNumber', 'firstStudentId'));

                    return $this->render(!empty($this->request->getData('generateSlip')) ? 'register_slip_pdf' : 'grade_report_pdf');
                } else {
                    $this->Flash->error(__('ERROR: No Data to Export to PDF!.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->info(__('No Course Registration is found with the given search criteria.'));
            }
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('generateRegisteredList'))) {
            if (!empty($courseRegistrations)) {
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $programsTable = TableRegistry::getTableLocator()->get('Programs');
                $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
                $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

                $departmentName = $departmentsTable->find()
                    ->select(['name'])
                    ->where(['Departments.id' => $this->request->getData('Search.department_id')])
                    ->first()
                    ->name ?? 'Pre/Freshman';

                $programName = $programsTable->find()
                    ->select(['name'])
                    ->where(['Programs.id' => $this->request->getData('Search.program_id')])
                    ->first()
                    ->name;

                $programTypeName = $programTypesTable->find()
                    ->select(['name'])
                    ->where(['ProgramTypes.id' => $this->request->getData('Search.program_type_id')])
                    ->first()
                    ->name;

                $sectionDetail = $sectionsTable->find()
                    ->contain(['YearLevels'])
                    ->where(['Sections.id' => $this->request->getData('Search.section_id')])
                    ->first();

                if ($sectionDetail->program_id == PROGRAM_REMEDIAL) {
                    $departmentName = 'Remedial Program';
                }

                $sectionName = sprintf(
                    '%s (%s)',
                    $sectionDetail->name,
                    !empty($sectionDetail->year_level->name)
                        ? $sectionDetail->year_level->name
                        : ($sectionDetail->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')
                );

                $registrationFormatted = [
                    sprintf(
                        '%s~%s~%s~%s~%s~%s',
                        $this->request->getData('Search.academic_year'),
                        $this->request->getData('Search.semester'),
                        $departmentName,
                        $programName,
                        $programTypeName,
                        $sectionName
                    ) => $courseRegistrations
                ];

                $studentsInRegistrationListPdf = $registrationFormatted;

                if (!empty($studentsInRegistrationListPdf)) {
                    $this->viewBuilder()->setLayout('pdf/default');
                    $this->response = $this->response->withType('application/pdf');
                    $this->set(compact('studentsInRegistrationListPdf'));
                    return $this->render('registration_list_pdf');
                } else {
                    $this->Flash->error(__('ERROR: No Data to Export to PDF!.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                return [];
            }
        }

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

        $programs = $programsTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();
        $programTypes = $programTypesTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        if ($this->Auth->user('role_id') == ROLE_REGISTRAR || $session->read('Auth.User.Role.parent_id') == ROLE_REGISTRAR) {
            if (!empty($this->department_ids)) {
                $departments = $departmentsTable->allDepartmentsByCollege2(0, $this->department_ids, [], 1);
            } elseif (!empty($this->college_ids)) {
                $colleges = $collegesTable->find('list')
                    ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                    ->toArray();
            }

            if ($session->read('Auth.User.is_admin') == 1) {
                $programs = $programsTable->find('list')->toArray();
                $programTypes = $programTypesTable->find('list')->toArray();
            } else {
                $programs = $programsTable->find('list')
                    ->where(['Programs.id' => $this->program_id])
                    ->toArray();
                $programTypes = $programTypesTable->find('list')
                    ->where(['ProgramTypes.id' => $this->program_type_id])
                    ->toArray();
            }
        } elseif ($this->Auth->user('role_id') == ROLE_COLLEGE) {
            $departments = $departmentsTable->allDepartmentInCollegeIncludingPre(null, $this->college_id, 1, 1);
        } elseif ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
            $departments = $departmentsTable->find('list')
                ->where(['Departments.id' => $this->department_id])
                ->toArray();
        }

        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        if (!empty($this->request->getData())) {
            if (!empty($this->request->getData('Search.college_id'))) {
                $conditions = [
                    'Sections.program_id' => $this->request->getData('Search.program_id'),
                    'Sections.program_type_id' => $this->request->getData('Search.program_type_id'),
                    'Sections.academicyear' => $this->request->getData('Search.academic_year'),
                    'Sections.college_id' => $this->request->getData('Search.college_id'),
                    'Sections.department_id IS' => null,
                    'Sections.archive' => 0
                ];
                if ($this->onlyPre || (count(explode('c~', $this->request->getData('Search.department_id'))) == 2)) {
                    $conditions['Sections.department_id IS'] = null;
                }
                $sections = $sectionsTable->find()
                    ->contain([
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']]
                    ])
                    ->where($conditions)
                    ->order([
                        'Sections.college_id' => 'ASC',
                        'Sections.department_id' => 'ASC',
                        'Sections.year_level_id' => 'ASC',
                        'Sections.id' => 'ASC',
                        'Sections.name' => 'ASC'
                    ])
                    ->toArray();
            } elseif (!empty($this->request->getData('Search.department_id'))) {
                $isCollege = count(explode('c~', $this->request->getData('Search.department_id'))) == 2;
                if ($isCollege) {
                    $collegeId = explode('c~', $this->request->getData('Search.department_id'))[1];
                    $conditions = [
                        'Sections.program_id' => $this->request->getData('Search.program_id'),
                        'Sections.program_type_id' => $this->request->getData('Search.program_type_id'),
                        'Sections.academicyear' => $this->request->getData('Search.academic_year'),
                        'Sections.college_id' => $collegeId,
                        'Sections.archive' => 0
                    ];
                    if ($this->onlyPre || $isCollege) {
                        $conditions['Sections.department_id IS'] = null;
                    }
                } else {
                    $conditions = [
                        'Sections.program_id' => $this->request->getData('Search.program_id'),
                        'Sections.program_type_id' => $this->request->getData('Search.program_type_id'),
                        'Sections.academicyear' => $this->request->getData('Search.academic_year'),
                        'Sections.department_id' => $this->request->getData('Search.department_id'),
                        'Sections.archive' => 0
                    ];
                }
                $sections = $sectionsTable->find()
                    ->contain([
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Colleges' => ['fields' => ['id', 'name']]
                    ])
                    ->where($conditions)
                    ->order([
                        'Sections.year_level_id' => 'ASC',
                        'Sections.college_id' => 'ASC',
                        'Sections.department_id' => 'ASC',
                        'Sections.id' => 'ASC',
                        'Sections.name' => 'ASC'
                    ])
                    ->toArray();
            }
        } elseif (empty($this->request->getData()) && $this->Auth->user('role_id') != ROLE_STUDENT) {
            $conditions = [
                'Sections.program_id' => array_values($this->program_ids)[0],
                'Sections.program_type_id' => array_values($this->program_type_ids)[0],
                'Sections.academicyear' => $this->AcademicYear->currentAcademicYear(),
                'Sections.archive' => 0
            ];
            if (!empty($this->college_ids) && $this->Auth->user('role_id') != ROLE_DEPARTMENT || (!empty($this->college_id) && $this->Auth->user('role_id') == ROLE_COLLEGE)) {
                $conditions['Sections.college_id'] = $this->Auth->user('role_id') == ROLE_COLLEGE ? $this->college_id : array_values($this->college_ids)[0];
                if ($this->onlyPre || $this->Auth->user('role_id') == ROLE_COLLEGE) {
                    $conditions['Sections.department_id IS'] = null;
                }
            } elseif (!empty($this->department_ids) && $this->Auth->user('role_id') != ROLE_COLLEGE || (!empty($this->department_id) && $this->Auth->user('role_id') == ROLE_DEPARTMENT)) {
                $conditions['Sections.department_id'] = $this->Auth->user('role_id') == ROLE_DEPARTMENT ? $this->department_id : array_values($this->department_ids)[0];
            }
            $sections = $sectionsTable->find()
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Departments' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Colleges' => ['fields' => ['id', 'name']]
                ])
                ->where($conditions)
                ->order([
                    'Sections.year_level_id' => 'ASC',
                    'Sections.college_id' => 'ASC',
                    'Sections.department_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ])
                ->toArray();
        }

        if ($this->Auth->user('role_id') != ROLE_STUDENT && !empty($sections)) {
            $sectionOrganizedByYearLevel = [];
            if (!empty($sections)) {
                foreach ($sections as $v) {
                    $sectionOrganizedByYearLevel[$v->id] = sprintf(
                        '%s (%s, %s)',
                        $v->name,
                        $v->academicyear,
                        !empty($v->year_level->name)
                            ? $v->year_level->name
                            : ($v->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')
                    );
                }
            } else {
                $sectionOrganizedByYearLevel['-1'] = '[ No Results, Try Changing Search Filters ]';
            }
            $sections = $sectionOrganizedByYearLevel;
        } else {
            $sections = ['-1' => '[ No Results, Try Changing Search Filters ]'];
        }

        if ($this->Auth->user('role_id') == ROLE_STUDENT) {
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $studentAySList = $examGradesTable->getListOfAyAndSemester($this->student_id);
            $academicYears = [];

            if (!empty($studentAySList)) {
                $statusGeneratedAcySemester = [];
                $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

                foreach ($studentAySList as $ayS) {
                    $academicYears[$ayS['academic_year']] = $ayS['academic_year'];
                    $checkStatusGenerated = $studentExamStatusesTable->find()
                        ->where([
                            'StudentExamStatuses.student_id' => $this->student_id,
                            'StudentExamStatuses.academic_year' => $ayS['academic_year'],
                            'StudentExamStatuses.semester' => $ayS['semester']
                        ])
                        ->count();

                    if ($checkStatusGenerated) {
                        if (defined('ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY') && ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY) {
                            $statusGeneratedAcySemester = [
                                'academic_year' => $ayS['academic_year'],
                                'semester' => $ayS['semester']
                            ];
                        } elseif (
                            !empty($this->request->getData('Search.academic_year')) &&
                            $this->request->getData('Search.academic_year') == $ayS['academic_year'] &&
                            $this->request->getData('Search.semester') == $ayS['semester']
                        ) {
                            $statusGeneratedAcySemester = [
                                'academic_year' => $ayS['academic_year'],
                                'semester' => $ayS['semester']
                            ];
                        }
                    }
                }

                $this->set(compact('statusGeneratedAcySemester'));
            } elseif (!empty($this->request->getData('Search.academic_year'))) {
                $academicYears[$this->request->getData('Search.academic_year')] = $this->request->getData('Search.academic_year');
            }

            $this->set(compact('academicYears'));
        }

        $this->set(compact('courseRegistrations', 'departments', 'colleges', 'acyear_array_data', 'programs', 'programTypes', 'sections'));
        $this->render('view_registration');
    }

    public function getSectionCombo($parameters)
    {
        $this->viewBuilder()->setLayout('ajax');
        $criteriaLists = explode('~', $parameters);
        $sectionOrganizedByYearLevel = ['-1' => '[ No Active Sections, Try Changing Filters ]'];

        if (!empty($criteriaLists) && count($criteriaLists) > 4) {
            $departmentCollegeId = $criteriaLists[0];
            $academicYear = str_replace('-', '/', $criteriaLists[1]);
            $programId = $criteriaLists[2];
            $programTypeId = $criteriaLists[3];
            $type = $criteriaLists[4];
            $ylName = $criteriaLists[5];

            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
            $options = [
                'conditions' => [
                    'Sections.academicyear' => $academicYear,
                    'Sections.program_id' => $programId,
                    'Sections.program_type_id' => $programTypeId,
                    'Sections.archive' => 0
                ],
                'contain' => [
                    'Programs',
                    'ProgramTypes',
                    'Departments',
                    'YearLevels',
                    'Colleges'
                ],
                'order' => [
                    'Sections.year_level_id' => 'ASC',
                    'Sections.college_id' => 'ASC',
                    'Sections.department_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ]
            ];

            if ($type == 'c') {
                $options['conditions']['Sections.college_id'] = $departmentCollegeId;
                $options['conditions']['Sections.department_id IS'] = null;
            } else {
                $options['conditions']['Sections.department_id'] = $departmentCollegeId;
                $options['conditions']['YearLevels.name LIKE'] = $ylName;
            }

            $sections = $sectionsTable->find('all', $options)->toArray();

            if (!empty($sections)) {
                $sectionOrganizedByYearLevel = ['' => '[ Select Section ]'];
                foreach ($sections as $v) {
                    $sectionOrganizedByYearLevel[$v->id] = sprintf(
                        '%s (%s, %s)',
                        $v->name,
                        $v->academicyear,
                        !empty($v->year_level->name)
                            ? $v->year_level->name
                            : ($v->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')
                    );
                }
            }
        }

        $this->set(compact('sectionOrganizedByYearLevel'));
    }

    public function getSectionComboForView($parameters)
    {
        $this->viewBuilder()->setLayout('ajax');
        $criteriaLists = explode('~', $parameters);
        $sectionOrganizedByYearLevel = ['-1' => '[ No Results, Try Changing Search Filters ]'];

        if (!empty($criteriaLists) && count($criteriaLists) > 4) {
            $cOrD = explode('~', $criteriaLists[0]);
            if ($cOrD[0] == 'c') {
                $departmentCollegeId = $criteriaLists[1];
                $academicYear = str_replace('-', '/', $criteriaLists[2]);
                $programId = $criteriaLists[3];
                $programTypeId = $criteriaLists[4];
                $type = $criteriaLists[5];
            } else {
                $departmentCollegeId = $criteriaLists[0];
                $academicYear = str_replace('-', '/', $criteriaLists[1]);
                $programId = $criteriaLists[2];
                $programTypeId = $criteriaLists[3];
                $type = $criteriaLists[4];
            }

            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
            $options = [
                'conditions' => [
                    'Sections.academicyear' => $academicYear,
                    'Sections.program_id' => $programId,
                    'Sections.program_type_id' => $programTypeId,
                    'Sections.created >=' => FrozenTime::now()->subYears(ACY_BACK_COURSE_REGISTRATION)->endOfDay()
                ],
                'contain' => [
                    'Programs',
                    'ProgramTypes',
                    'Departments',
                    'YearLevels',
                    'Colleges'
                ],
                'order' => [
                    'Sections.year_level_id' => 'ASC',
                    'Sections.college_id' => 'ASC',
                    'Sections.department_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ]
            ];

            if ($type == 'c' || $cOrD[0] == 'c') {
                $options['conditions']['Sections.college_id'] = $departmentCollegeId;
                $options['conditions']['Sections.department_id IS'] = null;
            } else {
                $options['conditions']['Sections.department_id'] = $departmentCollegeId;
            }

            $sections = $sectionsTable->find('all', $options)->toArray();

            if (!empty($sections)) {
                foreach ($sections as $v) {
                    $sectionOrganizedByYearLevel[$v->id] = sprintf(
                        '%s (%s, %s)',
                        $v->name,
                        $v->academicyear,
                        !empty($v->year_level->name)
                            ? $v->year_level->name
                            : ($v->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')
                    );
                }
            }
        }

        $this->set(compact('sectionOrganizedByYearLevel'));
    }

    public function manageMissingRegistration($studentId)
    {
        $this->viewBuilder()->setLayout('ajax');

        $currentAcy = $this->AcademicYear->currentAcademicYear();
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentAdmissionYear = $studentsTable->find()
            ->select(['academicyear'])
            ->where(['Students.id' => $studentId])
            ->first()
            ->academicyear ?? $currentAcy;

        $startYr = $endYr = explode('/', $currentAcy)[0];
        $academicYearList = [];

        if (!empty($studentAdmissionYear)) {

            $startYr = explode('/', $studentAdmissionYear)[0] ?: $startYr;
            $academicYearList = $this->AcademicYear->academicYearInArray($startYr, $endYr);

            $session = $this->request->getSession();
            if (
                $session->read('Auth.User.role_id') == ROLE_REGISTRAR &&
                !$session->read('Auth.User.is_admin') &&
                defined('RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION') &&
                is_numeric(RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION) &&
                count($academicYearList) > RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION
            ) {
                $startYear = $startYr > ($endYr - RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION)
                    ? $startYr
                    : ($endYr - RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION);
                $academicYearList = $this->AcademicYear->academicYearInArray($startYear, $endYr);
            }

            if (empty($academicYearList)) {
                $academicYearList[$currentAcy] = $currentAcy;
            }
        } else {
            $academicYearList[$currentAcy] = $currentAcy;
        }

        $studentID = $studentId;
        $this->set(compact('academicYearList', 'studentID'));
    }

    public function updateMissingRegistration()
    {
        $selectedStudentId = null;
        $session = $this->request->getSession();

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData())
            && $session->read('Auth.User.role_id') == ROLE_REGISTRAR) {

            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $selectedStudentDetail = $studentsTable->find()
                ->where(['Students.id' => $this->request->getData('Student.selected_student_id')])
                ->first();

            $selectedStudentId = $selectedStudentDetail->id;
            $ngGradeDeletionList = [];
            $courseAddAndRegistrationExamGradeIds = [];
            $haveAtLeastOneNGWithAssessment = false;
            $generatedStudentExamStatus = false;

            if (!empty($this->request->getData('cancelNG'))) {
                $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                $makeupExamsTable = TableRegistry::getTableLocator()->get('MakeupExams');
                $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

                foreach ($this->request->getData('CourseRegistration') as $student) {
                    if (
                        !empty($student['gp']) && $student['gp'] == 1 &&
                        $student['grade'] == 'NG' &&
                        empty($student['ng_grade_with_assessment'])
                    ) {
                        $isThereAnyGradeChange = $examGradesTable->find()
                            ->contain(['ExamGradeChanges'])
                            ->where([
                                'ExamGrades.course_registration_id' => $student['course_registration_id'] ?? $student['id']
                            ])
                            ->first();
                        debug($isThereAnyGradeChange);

                        if (
                            !empty($student['gp']) && $student['gp'] == 1 &&
                            !empty($student['id']) &&
                            !empty($selectedStudentDetail) &&
                            empty($isThereAnyGradeChange->exam_grade_changes)
                        ) {
                            $ngGradeDeletionList['ExamGrades'][] = $student['id'];
                        }
                        debug($ngGradeDeletionList);
                        debug($student);

                        $tmp = $examGradesTable->find()
                            ->where(['ExamGrades.id' => $student['grade_id']])
                            ->contain([
                                'CourseAdds' => [
                                    'PublishedCourses' => [
                                        'fields' => ['id', 'course_id'],
                                        'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'credit']],
                                    ],
                                    'ExamResults' => [
                                        'queryBuilder' => function ($query) {
                                            return $query
                                                ->where(['ExamResults.course_add' => 0])
                                                ->limit(1);
                                        },
                                    ],
                                ],
                                'CourseRegistrations' => [
                                    'PublishedCourses' => [
                                        'fields' => ['id', 'course_id'],
                                        'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'credit']],
                                    ],
                                    'ExamResults' => [
                                        'queryBuilder' => function ($query) {
                                            return $query->limit(1);
                                        },
                                    ],
                                ],
                                'MakeupExams' => [
                                    'PublishedCourses' => [
                                        'fields' => ['id', 'course_id'],
                                        'Courses' => ['fields' => ['id', 'course_title', 'course_code', 'credit']],
                                    ],
                                    'ExamResults' => [
                                        'queryBuilder' => function ($query) {
                                            return $query->limit(1);
                                        },
                                    ],
                                ],
                                'ExamGradeChanges',
                            ])
                            ->first();

                        debug($tmp);
                        die;

                        if (empty($tmp->exam_grade_changes)) {
                            if (
                                !empty($tmp->course_registration) &&
                                $tmp->grade === null &&
                                empty($tmp->course_registration->exams)
                            ) {
                                $courseAddAndRegistrationExamGradeIds['CourseRegistration'][] = $tmp->course_registration->id;
                                $courseAddAndRegistrationExamGradeIds['ExamGrade'][] = $tmp->id;
                                $selectedStudentId = $tmp->course_registration->student_id;
                            } elseif (
                                !empty($tmp->course_add->id) &&
                                $tmp->grade === null &&
                                empty($tmp->course_add->exams)
                            ) {
                                $courseAddAndRegistrationExamGradeIds['CourseAdd'][] = $tmp->course_add->id;
                                $courseAddAndRegistrationExamGradeIds['ExamGrade'][] = $tmp->id;
                                $selectedStudentId = $tmp->course_add->student_id;
                            } elseif (
                                !empty($tmp->makeup_exam->id) &&
                                $tmp->grade === null &&
                                empty($tmp->makeup_exam->exams)
                            ) {
                                $courseAddAndRegistrationExamGradeIds['MakeupExam'][] = $tmp->makeup_exam->id;
                                $courseAddAndRegistrationExamGradeIds['ExamGrade'][] = $tmp->id;
                                $selectedStudentId = $tmp->makeup_exam->student_id;
                            }
                        }
                    } elseif (!empty($student['ng_grade_with_assessment'])) {
                        $haveAtLeastOneNGWithAssessment = true;
                    }
                }

                if (!empty($courseAddAndRegistrationExamGradeIds['ExamGrade']) && !$haveAtLeastOneNGWithAssessment) {
                    $success = false;

                    if (!empty($courseAddAndRegistrationExamGradeIds['CourseRegistration'])) {
                        if (
                            $this->CourseRegistration->deleteAll(['CourseRegistrations.id IN' => $courseAddAndRegistrationExamGradeIds['CourseRegistration']], false) &&
                            $examGradesTable->deleteAll(['ExamGrades.id IN' => $courseAddAndRegistrationExamGradeIds['ExamGrade']], false)
                        ) {
                            if ($selectedStudentId > 0) {
                                $generatedStudentExamStatus = $studentExamStatusesTable->regenerateAllStatusOfStudentByStudentId($selectedStudentId, null);
                            }
                            $success = true;
                            $message = sprintf(
                                'You have cancelled %d NG grades and course registration.%s',
                                count($courseAddAndRegistrationExamGradeIds['ExamGrade']),
                                $generatedStudentExamStatus ? ' Student academic status is also regenerated.' : ''
                            );
                        }
                    } elseif (!empty($courseAddAndRegistrationExamGradeIds['CourseAdd'])) {
                        if (
                            $courseAddsTable->deleteAll(['CourseAdds.id IN' => $courseAddAndRegistrationExamGradeIds['CourseAdd']], false) &&
                            $examGradesTable->deleteAll(['ExamGrades.id IN' => $courseAddAndRegistrationExamGradeIds['ExamGrade']], false)
                        ) {
                            if ($selectedStudentId > 0) {
                                $generatedStudentExamStatus = $studentExamStatusesTable->regenerateAllStatusOfStudentByStudentId($selectedStudentId, null);
                            }
                            $success = true;
                            $message = sprintf(
                                'You have cancelled %d NG grades and course adds.%s',
                                count($courseAddAndRegistrationExamGradeIds['ExamGrade']),
                                $generatedStudentExamStatus ? ' Student academic status is also regenerated.' : ''
                            );
                        }
                    } elseif (!empty($courseAddAndRegistrationExamGradeIds['MakeupExam'])) {
                        if (
                            $makeupExamsTable->deleteAll(['MakeupExams.id IN' => $courseAddAndRegistrationExamGradeIds['MakeupExam']], false) &&
                            $examGradesTable->deleteAll(['ExamGrades.id IN' => $courseAddAndRegistrationExamGradeIds['ExamGrade']], false)
                        ) {
                            if ($selectedStudentId > 0) {
                                $generatedStudentExamStatus = $studentExamStatusesTable->regenerateAllStatusOfStudentByStudentId($selectedStudentId, null);
                            }
                            $success = true;
                            $message = sprintf(
                                'You have cancelled %d NG grades and makeup results.%s',
                                count($courseAddAndRegistrationExamGradeIds['ExamGrade']),
                                $generatedStudentExamStatus ? ' Student academic status is also regenerated.' : ''
                            );
                        }
                    }

                    if ($success) {
                        $this->Flash->success(__($message));
                        return $this->redirect([
                            'controller' => 'Students',
                            'action' => 'student_academic_profile',
                            $selectedStudentId ?: $this->request->getData('Student.selected_student_id')
                        ]);
                    }
                } else {
                    $errorMessage = $haveAtLeastOneNGWithAssessment
                        ? 'The selected NG Grade(s) could not be cancelled for %s. Check if there is an NG grade which has assessment data for the selected NG Grades.'
                        : 'The selected NG Grade(s) could not be cancelled for %s. Check if there is a grade change from the selected NG Grades.';
                    $this->Flash->error(__(
                        sprintf(
                            $errorMessage,
                            $selectedStudentDetail->full_name . ' (' . $selectedStudentDetail->studentnumber . ')'
                        )
                    ));
                    return $this->redirect([
                        'controller' => 'Students',
                        'action' => 'student_academic_profile',
                        $selectedStudentId ?: $this->request->getData('Student.selected_student_id')
                    ]);
                }
            }

            if (!empty($this->request->getData('registerMissingCourse'))) {
                $registrationLists = [];
                $count = 0;
                $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');

                if (!empty($this->request->getData('CourseRegistration'))) {
                    foreach ($this->request->getData('CourseRegistration') as $student) {
                        if (
                            !empty($student['gp']) && $student['gp'] == 1 &&
                            !empty($student['published_course_id']) &&
                            empty($student['id']) &&
                            !empty($selectedStudentDetail)
                        ) {
                            $publishedCourseDetail = $publishedCoursesTable->find()
                                ->where(['PublishedCourses.id' => $student['published_course_id']])
                                ->first();

                            if ($publishedCourseDetail) {
                                $registrationLists['CourseRegistration'][$count] = [
                                    'year_level_id' => (is_numeric($publishedCourseDetail->year_level_id) && $publishedCourseDetail->year_level_id > 0)
                                        ? $publishedCourseDetail->year_level_id
                                        : null,
                                    'section_id' => $publishedCourseDetail->section_id,
                                    'semester' => $publishedCourseDetail->semester,
                                    'academic_year' => $publishedCourseDetail->academic_year,
                                    'student_id' => $selectedStudentDetail->id,
                                    'published_course_id' => $student['published_course_id']
                                ];

                                $checkRegistered = $this->CourseRegistration->find()
                                    ->where([
                                        'CourseRegistrations.academic_year' => $publishedCourseDetail->academic_year,
                                        'CourseRegistrations.student_id' => $selectedStudentDetail->id,
                                        'CourseRegistrations.semester' => $publishedCourseDetail->semester
                                    ])
                                    ->order([
                                        'CourseRegistrations.academic_year' => 'DESC',
                                        'CourseRegistrations.semester' => 'DESC',
                                        'CourseRegistrations.id' => 'DESC'
                                    ])
                                    ->first();

                                if ($checkRegistered) {
                                    $registrationLists['CourseRegistration'][$count]['created'] = $checkRegistered->created;
                                    $registrationLists['CourseRegistration'][$count]['modified'] = $checkRegistered->created;
                                } else {
                                    $currentAcyAndSemester = $this->AcademicYear->currentAcyAndSemester();
                                    $date = ($currentAcyAndSemester['academic_year'] == $publishedCourseDetail->academic_year &&
                                        $currentAcyAndSemester['semester'] == $publishedCourseDetail->semester)
                                        ? FrozenTime::now()
                                        : $this->AcademicYear->getAcademicYearBegainingDate(
                                            $publishedCourseDetail->academic_year,
                                            $publishedCourseDetail->semester
                                        );
                                    $registrationLists['CourseRegistration'][$count]['created'] = $date;
                                    $registrationLists['CourseRegistration'][$count]['modified'] = $date;
                                }

                                $count++;
                            }
                        }
                    }
                }

                if (!empty($registrationLists)) {
                    if ($this->CourseRegistration->saveMany($registrationLists['CourseRegistration'], ['validate' => false])) {
                        $this->Flash->success(__(
                            'Missing course registration for %s was successful for %d %s for %s academic year semester %s, Dated: %s.',
                            $selectedStudentDetail->full_name . ' (' . $selectedStudentDetail->studentnumber . ')',
                            count($registrationLists['CourseRegistration']),
                            count($registrationLists['CourseRegistration']) > 1 ? 'courses' : 'course',
                            $registrationLists['CourseRegistration'][0]['academic_year'],
                            $registrationLists['CourseRegistration'][0]['semester'],
                            $registrationLists['CourseRegistration'][0]['created']->format('F j, Y h:i:s A')
                        ));
                        return $this->redirect([
                            'controller' => 'Students',
                            'action' => 'student_academic_profile',
                            $selectedStudentDetail->id ?: $this->request->getData('Student.selected_student_id')
                        ]);
                    }
                }

                $this->Flash->error(__(
                    'The Missing registration cannot be added for %s. Please try again.',
                    $selectedStudentDetail->full_name . ' (' . $selectedStudentDetail->studentnumber . ')'
                ));
                return $this->redirect([
                    'controller' => 'Students',
                    'action' => 'student_academic_profile',
                    $selectedStudentDetail->id ?: $this->request->getData('Student.selected_student_id')
                ]);
            }
        }

        return $this->redirect([
            'controller' => 'Students',
            'action' => 'student_academic_profile',
            $selectedStudentDetail->id ?? $selectedStudentId ?? ''
        ]);
    }

    public function getIndividualRegistration($parameters)
    {
        $this->viewBuilder()->setLayout('ajax');
        $criteriaLists = explode('~', $parameters);
        $publishedCourses = [];
        $showManageNgLink = false;
        $lastSemesterStatusIsNotGenerated = false;
        $session = $this->request->getSession();

        if (!empty($criteriaLists) && count($criteriaLists) > 2) {
            $academicYear = str_replace('-', '/', $criteriaLists[0]);
            $semester = $criteriaLists[1];
            $studentId = $criteriaLists[2];

            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
            $getStudentSection = $sectionsTable->getStudentSectionInGivenAcademicYear($academicYear, $studentId, $semester);

            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $studentDetail = $studentsTable->find()
                ->contain(['Departments', 'Colleges', 'Programs', 'ProgramTypes', 'Curriculums'])
                ->where(['Students.id' => $studentId])
                ->first();

            $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');
            $exemptedCoursesCourseIds = $courseExemptionsTable->find()
                ->select(['course_id'])
                ->where(['CourseExemptions.student_id' => $studentId,
                    'CourseExemptions.registrar_confirm_deny' => 1])
                ->extract('course_id')
                ->toArray();

            $latestAcademicYear = ['academic_year' => $academicYear, 'semester' => $semester];
            $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $passedOrFailed = $studentExamStatusesTable->getStudentExamStatus($studentId,
                $latestAcademicYear['academic_year'], $semester);

            if (in_array($passedOrFailed, [1, 3, 2]) && $passedOrFailed != DISMISSED_ACADEMIC_STATUS_ID) {
                if ($passedOrFailed == 2 && $passedOrFailed != 1) {
                    $lastSemesterStatusIsNotGenerated = true;
                }

                if (!empty($getStudentSection)) {
                    $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                    $publishedCourses = $publishedCoursesTable->find()
                        ->contain([
                            'Courses' => [
                                'Prerequisites',
                                'fields' => ['id', 'course_title', 'course_code', 'credit'],
                                'GradeTypes' => [
                                    'Grades' => ['fields' => ['id', 'grade','grade_type_id']]
                                ]
                            ]
                        ])
                        ->where([
                            'PublishedCourses.semester' => $semester,
                            'PublishedCourses.academic_year' => $academicYear,
                            'PublishedCourses.section_id' => $getStudentSection->id
                        ])
                        ->toArray();
                }

                $failedAnyPrerequisite = ['freq' => 0];
                $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
                $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
                // Ensure CourseRegistration is a valid table object
                $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');



                foreach ($publishedCourses as &$vv) {

                    $vv->isExemptedCourse = in_array($vv->course_id, $exemptedCoursesCourseIds);

                    $courseRegistration = $courseRegistrationsTable->find()
                        ->contain([
                            'ExamResults' => [
                                'queryBuilder' => function ($query) {
                                    return $query
                                        ->contain([
                                            'ExamTypes' => [
                                                'queryBuilder' => function ($subQuery) {
                                                    return $subQuery->limit(1);
                                                }
                                            ]
                                        ])
                                        ->order(['ExamResults.result' => 'ASC'])
                                        ->limit(1);
                                }
                            ]
                        ])
                        ->where([
                            'CourseRegistrations.student_id' => $studentId,
                            'CourseRegistrations.published_course_id' => $vv->id
                        ])
                        ->first();

                    if (!empty($vv->course->prerequisites)) {
                        foreach ($vv->course->prerequisites as $preValue) {

                            $failed = $courseDropsTable->prerequisiteTaken($studentId, $preValue->prerequisite_course_id);
                            if ($failed == 0 && !$preValue->co_requisite) {
                                $failedAnyPrerequisite['freq']++;
                            }
                        }
                    }


                    $vv->prerequisiteFailed = $failedAnyPrerequisite['freq'] > 0;
                    $failedAnyPrerequisite['freq'] = 0;

                    $vv->mass_dropped = $vv->drop;
                    $vv->mass_added = $vv->add;

                    if (!empty($courseRegistration)) {
                        $approvedGrade = $examGradesTable->getApprovedGrade($courseRegistration->id, 1);

                        if ($approvedGrade && $approvedGrade['grade'] == 'NG') {
                            $vv->readOnly = false;
                            $vv->grade = $approvedGrade['grade'];
                            $vv->grade_id = $approvedGrade['grade_id'];
                            $vv->haveGradeChange = !empty($approvedGrade['grade_change_id']);
                            $vv->grade_change_id = $approvedGrade['grade_change_id'] ?? null;
                            $vv->haveAssessmentData = false;

                            if (!empty($courseRegistration->exam_results)) {
                                $vv->readOnly = true;
                                $vv->haveAssessmentData = true;

                                if ($this->onlyPre && !empty($this->college_ids) && !empty($vv->college_id)
                                    && in_array($vv->college_id, $this->college_ids)) {
                                    $showManageNgLink = true;
                                } elseif (!$this->onlyPre && !empty($this->department_ids) &&
                                    !empty($vv->department_id) &&
                                    in_array($vv->department_id, $this->department_ids)) {
                                    $showManageNgLink = true;
                                }

                                if ($session->read('Auth.User.is_admin') == 1) {
                                    $showManageNgLink = true;
                                }
                            }
                        } elseif ($approvedGrade && $approvedGrade['grade'] != "NG") {
                            $vv->readOnly = true;
                            $vv->grade = $approvedGrade['grade'];
                            $vv->grade_id = $approvedGrade['grade_id'];
                            $vv->haveGradeChange = !empty($approvedGrade['grade_change_id']);
                            $vv->grade_change_id = $approvedGrade['grade_change_id'] ?? null;
                        } else {
                            $vv->readOnly = false;
                        }

                        $vv->course_registration_id = $courseRegistration->id;
                    } else {
                        $vv->readOnly = false;
                        $vv->grade = '';
                    }

                    if ($vv->isExemptedCourse) {
                        $vv->readOnly = true;
                        $vv->grade = 'TR';
                        $vv->grade_id = null;
                    }
                }
            } elseif ($passedOrFailed == 2) {
                $lastSemesterStatusIsNotGenerated = true;
            } else {
                if ($passedOrFailed == DISMISSED_ACADEMIC_STATUS_ID) {
                    $status = sprintf(
                        '%s (%s) is dismissed. You cannot register the student for %s semester of %s.
                        Please advise the student to apply for readmission if applicable.',
                        $studentDetail->full_name,
                        $studentDetail->studentnumber,
                        $latestAcademicYear['semester'] == 'I' ? '1st' :
                            ($latestAcademicYear['semester'] == 'II' ? '2nd' :
                                ($latestAcademicYear['semester'] == 'III' ? '3rd' : $latestAcademicYear['semester'])),
                        $latestAcademicYear['academic_year']
                    );
                    $this->set(compact('status'));
                }
            }
        }

        $this->set(compact('publishedCourses', 'studentDetail', 'showManageNgLink', 'lastSemesterStatusIsNotGenerated'));
    }

    public function isDate($variable)
    {
        try {
            new \DateTime($variable);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
